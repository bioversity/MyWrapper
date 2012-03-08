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
	
	echo( '<i>$test = new CArrayContainer( Array() );</i><br>' );
	$test = new CArrayContainer( Array() );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$object = Array();</i><br>' );
	echo( '<i>$object[] = array( 1 => 10 );</i><br>' );
	echo( '<i>$object[] = array( 2 => 20 );</i><br>' );
	$object = Array();
	$object[] = array( 1 => 10 );
	$object[] = array( 2 => 20 );
	echo( '<i>$test = new CArrayContainer( $object );</i><br>' );
	$test = new CArrayContainer( $object );
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
	echo( '<h3>Insert</h3>' );
	
	echo( '<i>$object = array( 123 );</i><br>' );
	$object = array( 123 );
	echo( '<i>$found = $test->Commit( $object, 10 );</i><br>' );
	$found = $test->Commit( $object, 10 );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$object = array( 234 );</i><br>' );
	$object = array( 234 );
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
	
	echo( '<i>$object = array( 3 => 30 );</i><br>' );
	$object = array( 3 => 30 );
	echo( '<i>$found = $test->Commit( $object, 11, kFLAG_PERSIST_MODIFY );</i><br>' );
	$found = $test->Commit( $object, 11, kFLAG_PERSIST_MODIFY );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$object = array( 3 => NULL );</i><br>' );
	$object = array( 3 => NULL );
	echo( '<i>$found = $test->Commit( $object, 11, kFLAG_PERSIST_MODIFY );</i><br>' );
	$found = $test->Commit( $object, 11, kFLAG_PERSIST_MODIFY );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	
	try
	{
		echo( '<i>$found = $test->Commit( $object, 22, kFLAG_PERSIST_MODIFY );</i><br>' );
		$found = $test->Commit( $object, 22, kFLAG_PERSIST_MODIFY );
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
	
	echo( '<i>$object = $test->Delete( 11 );</i><br>' );
	$object = $test->Delete( 11 );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Object:<pre>' ); print_r( $object ); echo( '</pre>' );
	echo( '<hr>' );
	
	try
	{
		echo( '<i>$object = $test->Delete( 11 );</i><br>' );
		$object = $test->Delete( 11 );
		echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
		echo( 'Object:<pre>' ); print_r( $object ); echo( '</pre>' );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );

	} echo( '<hr>' );
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
