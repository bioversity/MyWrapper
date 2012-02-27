<?php

/**
 * {@link CPersistentObject.php Base} object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * base object {@link CPersistentObject class}.
 *
 *	@package	Test
 *	@subpackage	Persistence
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 13/12/2012
 */

/*=======================================================================================
 *																						*
 *								test_CPersistentObject.php								*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."CPersistentObject.php" );


/*=======================================================================================
 *	TEST DEFAULT EXCEPTIONS																*
 *======================================================================================*/
 
//
// Instantiate test class.
//
$test = new CPersistentObject();

//
// Test class.
//
try
{
	//
	// Test instantiation.
	//
	echo( '<h3>Instantiation</h3>' );
	
	echo( '<i>new CPersistentObject();</i><br>' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	
	$container = array( array( 'NAME' => 'Milko', 'SURNAME' => 'Skofic' ) );
	echo( 'Container:<pre>' ); print_r( $container ); echo( '</pre>' );
	echo( '<i>new CPersistentObject( $container, 0 );</i><br>' );
	$test = new CPersistentObject( $container, 0 );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	
	echo( '<i>new CPersistentObject( $container, 1 );</i><br>' );
	$test = new CPersistentObject( $container, 1 );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	
	$container = array( 'NAME' => 'Milko', 'SURNAME' => 'Skofic' );
	echo( 'Container:<pre>' ); print_r( $container ); echo( '</pre>' );
	echo( '<i>new CPersistentObject( $container );</i><br>' );
	$test = new CPersistentObject( $container );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	
	try
	{
		echo( '<i>new CPersistentObject( \'pippo\' );</i><br>' );
		$test = new CPersistentObject( 'pippo' );
		echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );
		echo( '<br>' );
	}
	
	try
	{
		echo( '<i>new CPersistentObject( \'pippo\', \'pippo\' );</i><br>' );
		$test = new CPersistentObject( 'pippo', 'pippo' );
		echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );
		echo( '<br>' );
	}

	//
	// Test commit.
	//
	echo( '<h3>Commit</h3>' );
	
	$container = new ArrayObject();
	$test = new CPersistentObject();
	$test[ 'NAME' ] = 'New';
	
	echo( 'Container:<pre>' ); print_r( $container ); echo( '</pre>' );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );

	echo( '<i>$id = $test->Commit( $container );</i><br>' );
	$id = $test->Commit( $container );
	echo( "ID: $id<pre>" ); print_r( $container ); echo( '</pre>' );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre><hr>' );

	$id = $test->Commit( $container );
	echo( '<i>$id = $test->Commit( $container, \'other\' );</i><br>' );
	echo( "ID: $id<pre>" ); print_r( $container ); echo( '</pre>' );
	echo( ($id === NULL)?'OK: object not dirty<hr>':'Not ok<hr>' );

	$test[ 'other' ] = 'Modified';
	$id = $test->Commit( $container );
	echo( '<i>$test[\'other\' ] = \'Modified\';</i><br>' );
	echo( '<i>$id = $test->Commit( $container, \'other\' );</i><br>' );
	echo( "ID: $id<pre>" ); print_r( $container ); echo( '</pre>' );
	echo( ($id !== NULL)?'OK: object dirty<hr>':'Not ok<hr>' );
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
