<?php

/**
 * {@link CArrayContainer.php Base} object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * base object {@link CArrayContainer class}.
 *
 *	@package	Test
 *	@subpackage	Framework
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 07/03/2012
 */

/*=======================================================================================
 *																						*
 *								test_CArrayContainer.php								*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."CArrayContainer.php" );


/*=======================================================================================
 *	TEST																				*
 *======================================================================================*/

//
// Test class.
//
try
{
	//
	// Create.
	//
	echo( '<h3>Create</h3>' );
	
	echo( '<i>$test = new CArrayContainer();</i><br>' );
	$test = new CArrayContainer();
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$test = new CArrayContainer( new ArrayObject() );</i><br>' );
	$test1 = new CArrayContainer( new ArrayObject() );
	echo( '<pre>' ); print_r( $test1 ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$test = new CArrayContainer( Array() );</i><br>' );
	$test2 = new CArrayContainer( Array() );
	echo( '<pre>' ); print_r( $test2 ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$container = array( 10, 20 );</i><br>' );
	$container = array( 10, 20 );
	echo( '<i>$test = new CArrayContainer( $container );</i><br>' );
	$test = new CArrayContainer( $container );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );

	//
	// Data members.
	//
	echo( '<h3>Data members</h3>' );
	
	echo( '<i>$x = (string) $test1;</i><br>' );
	$x = (string) $test1;
	echo( '<pre>' ); print_r( $x ); echo( '</pre>' );
	echo( '<i>$x = (string) $test2;</i><br>' );
	$x = (string) $test2;
	echo( '<pre>' ); print_r( $x ); echo( '</pre>' );
	echo( '<i>$x = $test->Database();</i><br>' );
	$x = $test->Database();
	echo( '<pre>' ); print_r( $x ); echo( '</pre>' );
	echo( '<i>$x = $test->Container();</i><br>' );
	$x = $test->Container();
	echo( '<pre>' ); print_r( $x ); echo( '</pre>' );
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
	echo( '<h3>Insert</h3>' );
	
	echo( '<i>$object = 123;</i><br>' );
	$object = 123;
	echo( '<i>$found = $test->Commit( $object );</i><br>' );
	$found = $test->Commit( $object );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$object = 234;</i><br>' );
	$object = 234;
	echo( '<i>$found = $test->Commit( $object, \'new\' );</i><br>' );
	$found = $test->Commit( $object, 'new' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$object = array( 0 => \'Zero\', 1 => 10, \'due\' => \'Two\' );</i><br>' );
	$object = array( 0 => 'Zero', 1 => 10, 'due' => 'Two' );
	echo( '<i>$found = $test->Commit( $object );</i><br>' );
	$found = $test->Commit( $object );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	
	try
	{
		echo( '<i>$found = $test->Commit( $object, 0, kFLAG_PERSIST_INSERT );</i><br>' );
		$found = $test->Commit( $object, 0, kFLAG_PERSIST_INSERT );
		echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
		echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );

	} echo( '<hr>' );
	
	try
	{
		echo( '<i>$found = $test->Commit( $object, 22, kFLAG_PERSIST_UPDATE );</i><br>' );
		$found = $test->Commit( $object, 22, kFLAG_PERSIST_UPDATE );
		echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
		echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );

	} echo( '<hr>' );

	//
	// Modify.
	//
	echo( '<h3>Modify</h3>' );
	
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>$object = array( 3 => 30, 1 => NULL, \'due\' => \'Zwei\' );</i><br>' );
	$object = array( 3 => 30, 1 => NULL, 'due' => 'Zwei' );
	echo( '<pre>' ); print_r( $object ); echo( '</pre>' );
	echo( '<i>$found = $test->Commit( $object, 3, kFLAG_PERSIST_MODIFY );</i><br>' );
	$found = $test->Commit( $object, 3, kFLAG_PERSIST_MODIFY );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>$found = $test->Commit( $object, 0, kFLAG_PERSIST_MODIFY );</i><br>' );
	$found = $test->Commit( $object, 0, kFLAG_PERSIST_MODIFY );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>$object = 99;</i><br>' );
	$object = 99;
	echo( '<i>$found = $test->Commit( $object, 0, kFLAG_PERSIST_MODIFY );</i><br>' );
	$found = $test->Commit( $object, 0, kFLAG_PERSIST_MODIFY );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>$found = $test->Commit( $object, 1, kFLAG_PERSIST_MODIFY );</i><br>' );
	$found = $test->Commit( $object, 1, kFLAG_PERSIST_MODIFY );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );

	try
	{
		echo( '<i>$found = $test->Commit( $object, 99, kFLAG_PERSIST_MODIFY );</i><br>' );
		$found = $test->Commit( $object, 99, kFLAG_PERSIST_MODIFY );
		echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
		echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );

	} echo( '<hr>' );

	//
	// Delete.
	//
	echo( '<h3>Delete</h3>' );
	
	echo( '<i>$object = $test->Delete( 0 );</i><br>' );
	$object = $test->Delete( 0 );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Object:<pre>' ); print_r( $object ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$object = $test->Delete( 0 );</i><br>' );
	$object = $test->Delete( 0 );
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
