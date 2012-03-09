<?php

/**
 * {@link CContainer.php Base} object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * base object {@link CContainer class}.
 *
 *	@package	Test
 *	@subpackage	Framework
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 07/03/2012
 */

/*=======================================================================================
 *																						*
 *									test_CContainer.php									*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Offset includes.
//
require_once( kPATH_LIBRARY_DEFINES."Offsets.inc.php" );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."CContainer.php" );


/*=======================================================================================
 *	DEFINE TEST CLASS																	*
 *======================================================================================*/
 
//
// Declare test class.
//
class MyTest extends CContainer
{
	protected function _Commit( &$theObject, $theIdentifier, $theModifiers )
	{
		$container = $this->Container();
		
		if( array_key_exists( kTAG_ID_NATIVE, (array) $theObject ) )
			$container[ $id = $theObject[ kTAG_ID_NATIVE ] ] = $theObject;
		
		else
			$container[ $id = count( $container ) ] = $theObject;
		
		$this->Container( $container );
		
		return $id;																	// ==>
	}

	protected function _Load( $theIdentifier, $theModifiers )
	{
		$container = $this->Container();
		
		if( is_array( $theIdentifier ) )
		{
			if( array_key_exists( kTAG_ID_NATIVE, (array) $theidentifier ) )
			{
				$id = $theIdentifier[ kTAG_ID_NATIVE ];
				
				if( array_key_exists( $id, (array) $container ) )
					return $container[ $id];										// ==>
			}
		}
		
		if( array_key_exists( $theIdentifier, (array) $container ) )
			return $container[ $theIdentifier];										// ==>
		
		return NULL;																// ==>
	}

	protected function _Delete( $theIdentifier, $theModifiers )
	{
		$container = $this->Container();
		
		if( is_array( $theIdentifier ) )
		{
			if( array_key_exists( kTAG_ID_NATIVE, (array) $theidentifier ) )
			{
				$id = $theIdentifier[ kTAG_ID_NATIVE ];
				
				if( array_key_exists( $id, (array) $container ) )
				{
					$save = $container[ $id];
					unset( $container[ $id] );
					$this->Container( $container );

					return $save;													// ==>
				}
			}
		}
		
		if( array_key_exists( $theIdentifier, (array) $container ) )
		{
			$save = $container[ $theIdentifier];
			unset( $container[ $theIdentifier] );
			$this->Container( $container );

			return $save;															// ==>
		}
		
		return NULL;																// ==>
	}
}


/*=======================================================================================
 *	TEST DEFAULT EXCEPTIONS																*
 *======================================================================================*/

//
// Test class.
//
try
{
	//
	// Create.
	//
	echo( '<h3>Commit</h3>' );
	
	echo( '<i>$test = new MyTest( Array() );</i><br>' );
	$test = new MyTest( Array() );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$object = array( 1 => \'Uno\', 2 => \'Due\' );</i><br>' );
	$object = array( 1 => 'Uno', 2 => 'Due' );
	echo( '<i>$test = new MyTest( $object );</i><br>' );
	$test = new MyTest( $object );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Load.
	//
	echo( '<h3>Load</h3>' );
	
	$found = $test->Load( 1 );
	echo( '<i>$found = $test->Load( 1 );</i><br>' );
	echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );

	//
	// Commit.
	//
	echo( '<h3>Commit</h3>' );
	
	echo( '<i>$test = new MyTest( Array() );</i><br>' );
	$test = new MyTest( Array() );
	echo( '<i>$object = array( '.kTAG_ID_NATIVE.' => \'pippo\', Name => \'Uno\' );</i><br>' );
	$object = array( kTAG_ID_NATIVE => 'pippo', 'Name' => 'Pippo' );
	echo( '<i>$found = $test->Commit( $object );</i><br>' );
	$found = $test->Commit( $object );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$object = array( 1 => \'Uno\', 2 => \'Due\' );</i><br>' );
	$object = array( 1 => 'Uno', 2 => 'Due' );
	echo( '<i>$found = $test->Commit( $object );</i><br>' );
	$found = $test->Commit( $object );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$object = array( 1, 2, 3 );</i><br>' );
	$object = array( 1, 2, 3 );
	echo( '<i>$found = $test->Commit( $object );</i><br>' );
	$found = $test->Commit( $object );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );

	//
	// Delete.
	//
	echo( '<h3>Delete</h3>' );
	
	echo( '<i>$object = $test->Delete( \'pippo\' );</i><br>' );
	$object = $test->Delete( 'pippo' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Object:<pre>' ); print_r( $object ); echo( '</pre>' );
	echo( '<hr>' );
}

//
// Catch exceptions.
//
catch( Exception $error )
{
	echo( CException::AsHTML( $error ) );
	echo( '<pre>'.(string) $error.'</pre>' );
	echo( '<hr>' );
}

echo( "Done!<br />" );

?>
