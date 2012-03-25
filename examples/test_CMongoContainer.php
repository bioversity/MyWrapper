<?php

/**
 * {@link CMongoContainer.php Base} object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * base object {@link CMongoContainer class}.
 *
 *	@package	Test
 *	@subpackage	Framework
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 09/03/2012
 */

/*=======================================================================================
 *																						*
 *								test_CMongoContainer.php								*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."CMongoContainer.php" );


/*=======================================================================================
 *	TEST																				*
 *======================================================================================*/

//
// Test class.
//
try
{
	//
	// Instantiate Mongo database.
	//
	$mongo = New Mongo();
	
	//
	// Select MCPD database.
	//
	$db = $mongo->selectDB( "TEST" );
	
	//
	// Drop database.
	//
	$db->drop();
	
	//
	// Select test collection.
	//
	$collection = $db->selectCollection( 'CMongoContainer' );
	 
	//
	// Create.
	//
	echo( '<h3>Create</h3>' );
	
	echo( '<i>$test = new CMongoContainer();</i><br>' );
	$test = new CMongoContainer();
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );

	//
	// Data members.
	//
	echo( '<h3>Data members</h3>' );
	
	echo( '<i>$test->Container( $collection );</i><br>' );
	$test->Container( $collection );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$x = (string) $test;</i><br>' );
	$x = (string) $test;
	echo( '<pre>' ); print_r( $x ); echo( '</pre>' );
	echo( '<i>$x = $test->Database();</i><br>' );
	$x = $test->Database();
	echo( '<pre>' ); print_r( $x ); echo( '</pre>' );
	echo( '<pre>' ); print_r( (string) $x ); echo( '</pre>' );
	echo( '<i>$x = $test->Container();</i><br>' );
	$x = $test->Container();
	echo( '<pre>' ); print_r( $x ); echo( '</pre>' );
	echo( '<hr>' );

	//
	// Replace.
	//
	echo( '<h3>Replace</h3>' );
	
	echo( '<i>$object = array( \'Name\' => \'Milko\', \'Surname\' => \'Skofic\' );</i><br>' );
	$object = array( 'Name' => 'Milko', 'Surname' => 'Skofic' );
	echo( '<pre>' ); print_r( $object ); echo( '</pre>' );
	echo( '<i>$found = $test->Commit( $object );</i><br>' );
	$found = $test->Commit( $object );
	echo( '<pre>' ); print_r( $object ); echo( '</pre>' );
	echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$object = array( \'Name\' => \'Luca\', \'Surname\' => \'Sampieri\' );</i><br>' );
	$object = array( 'Name' => 'Luca', 'Surname' => 'Sampieri' );
	echo( '<pre>' ); print_r( $object ); echo( '</pre>' );
	echo( '<i>$found = $test->Commit( $object, 10 );</i><br>' );
	$found = $test->Commit( $object, 10 );
	echo( '<pre>' ); print_r( $object ); echo( '</pre>' );
	echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$object = array( \'Name\' => \'Pippo\', \'_id\' => 20 );</i><br>' );
	$object = array( 'Name' => 'Pippo', '_id' => 20 );
	echo( '<pre>' ); print_r( $object ); echo( '</pre>' );
	echo( '<i>$found = $test->Commit( $object );</i><br>' );
	$found = $test->Commit( $object );
	echo( '<pre>' ); print_r( $object ); echo( '</pre>' );
	echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
exit;

	//
	// Insert.
	//
	echo( '<h3>Insert</h3>' );
	
	echo( '<i>$object = array( 111 );</i><br>' );
	$object = array( 111 );
	echo( '<pre>' ); print_r( $object ); echo( '</pre>' );
	echo( '<i>$found = $test->Commit( $object, NULL, kFLAG_PERSIST_INSERT );</i><br>' );
	$found = $test->Commit( $object, NULL, kFLAG_PERSIST_INSERT );
	echo( '<pre>' ); print_r( $object ); echo( '</pre>' );
	echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$found = $test->Commit( $object, 9, kFLAG_PERSIST_INSERT );</i><br>' );
	$found = $test->Commit( $object, 9, kFLAG_PERSIST_INSERT );
	echo( '<pre>' ); print_r( $object ); echo( '</pre>' );
	echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	
	try
	{
		echo( '<i>$found = $test->Commit( $object, 9, kFLAG_PERSIST_INSERT );</i><br>' );
		$found = $test->Commit( $object, 9, kFLAG_PERSIST_INSERT );
		echo( '<pre>' ); print_r( $object ); echo( '</pre>' );
		echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );

	} echo( '<hr>' );

	//
	// Update.
	//
	echo( '<h3>Update</h3>' );
	
	echo( '<i>$object[ 0 ] = 456;</i><br>' );
	$object[ 0 ] = 456;
	echo( '<pre>' ); print_r( $object ); echo( '</pre>' );
	echo( '<i>$found = $test->Commit( $object, 9, kFLAG_PERSIST_UPDATE );</i><br>' );
	$found = $test->Commit( $object, 9, kFLAG_PERSIST_UPDATE );
	echo( '<pre>' ); print_r( $object ); echo( '</pre>' );
	echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	
	try
	{
		echo( '<i>$found = $test->Commit( $object, NULL, kFLAG_PERSIST_UPDATE );</i><br>' );
		$found = $test->Commit( $object, NULL, kFLAG_PERSIST_UPDATE );
		echo( '<pre>' ); print_r( $object ); echo( '</pre>' );
		echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
		echo( '<hr>' );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );

	} echo( '<hr>' );
	
	try
	{
		echo( '<i>$found = $test->Commit( $object, 22, kFLAG_PERSIST_UPDATE );</i><br>' );
		$found = $test->Commit( $object, 22, kFLAG_PERSIST_UPDATE );
		echo( '<pre>' ); print_r( $object ); echo( '</pre>' );
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
	
	echo( '<i>$mod = array( 1 => 10, 2 => 20, 3 => 30 );</i><br>' );
	$mod = array( 1 => 10, 2 => 20, 3 => 30 );
	echo( '<i>$found = $test->Commit( $mod, 9, kFLAG_PERSIST_MODIFY );</i><br>' );
	$found = $test->Commit( $mod, 9, kFLAG_PERSIST_MODIFY );
	echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$mod = array( 1 => NULL, 2 => 20, 3 => NULL );</i><br>' );
	$mod = array( 1 => NULL, 2 => 20, 3 => NULL );
	echo( '<i>$found = $test->Commit( $mod, 9, kFLAG_PERSIST_MODIFY );</i><br>' );
	$found = $test->Commit( $mod, 9, kFLAG_PERSIST_MODIFY );
	echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	
	try
	{
		echo( '<i>$found = $test->Commit( $mod, 22, kFLAG_PERSIST_MODIFY );</i><br>' );
		$found = $test->Commit( $mod, 22, kFLAG_PERSIST_MODIFY );
		echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );

	} echo( '<hr>' );
	
	//
	// Load.
	//
	echo( '<h3>Load</h3>' );
	
	echo( '<i>$found = $test->Load( 1 );</i><br>' );
	$found = $test->Load( 1 );
	echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$found = $test->Load( 9 );</i><br>' );
	$found = $test->Load( 9 );
	echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$found = $test->Load( $found );</i><br>' );
	$found = $test->Load( $found );
	echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );

	//
	// Delete.
	//
	echo( '<h3>Delete</h3>' );
	
	echo( '<i>$object = $test->Delete( 9 );</i><br>' );
	$found = $test->Delete( 9 );
	echo( '<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$object = $test->Delete( 9 );</i><br>' );
	$found = $test->Delete( 9 );
	echo( '<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );

	//
	// Encode.
	//
	echo( '<h3>Encode</h3>' );
	
	$array = array
	(
		kTAG_ID_NATIVE => array
		(
			kTAG_TYPE => kDATA_TYPE_MongoId,
			kTAG_DATA => '4f5e28d2961be56010000003'
		),
		'Stamp' => array
		(
			kTAG_TYPE => kDATA_TYPE_STAMP,
			kTAG_DATA => array
			(
				kOBJ_TYPE_STAMP_SEC => 22,
				kOBJ_TYPE_STAMP_USEC => 1246
			)
		),
		'RegExpr' => array
		(
			kTAG_TYPE => kDATA_TYPE_MongoRegex,
			kTAG_DATA => '/^pippo/i'
		),
		'Int32' => array
		(
			kTAG_TYPE => kDATA_TYPE_INT32,
			kTAG_DATA => 32
		),
		'Int64' => array
		(
			kTAG_TYPE => kDATA_TYPE_INT64,
			kTAG_DATA => '12345678901234'
		),
		'Binary' => array
		(
			kTAG_TYPE => kDATA_TYPE_BINARY,
			kTAG_DATA => md5( 'PIPPO' )
		)
	);
//	$object = new ArrayObject( $array );
	$object = $array;
	echo( 'Serialised<pre>' ); print_r( $object ); echo( '</pre>' );
	echo( '<i>CMongoDataWrapper::UnserialiseObject( $object );</i><br>' );
	CMongoDataWrapper::UnserialiseObject( $object );
	echo( 'Unserialised<pre>' ); print_r( $object ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>CDataType::SerialiseObject( $object );</i><br>' );
	CDataType::SerialiseObject( $object );
	echo( 'Decoded<pre>' ); print_r( $object ); echo( '</pre>' );
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
