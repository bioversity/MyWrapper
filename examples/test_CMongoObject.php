<?php

/**
 * {@link CMongoObject.php Base} object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * base object {@link CMongoObject class}.
 *
 *	@package	Test
 *	@subpackage	Persistence
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 13/12/2012
 */

/*=======================================================================================
 *																						*
 *									test_CMongoObject.php								*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."CMongoObject.php" );


/*=======================================================================================
 *	TEST MONGO PERSISTENT OBJECTS														*
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
	$collection = $db->selectCollection( 'CMongoObject' );
	 
	//
	// Test instantiation.
	//
	echo( '<h3>Instantiation</h3>' );
	
	echo( '<i>new CMongoObject();</i><br>' );
	$test = new CMongoObject();
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	
	$container = array( 'NAME' => 'Milko', 'SURNAME' => 'Skofic' );
	echo( 'Container:<pre>' ); print_r( $container ); echo( '</pre>' );
	echo( '<i>new CMongoObject( $container );</i><br>' );
	$test = new CMongoObject( $container );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	 
	//
	// Test persistence.
	//
	echo( '<h3>Persistence</h3>' );
	
	echo( '<i>$identifier = $test->Commit( $collection );</i><br>' );
	$identifier = $test->Commit( $collection );
	echo( "$identifier<pre>" ); print_r( $test ); echo( '</pre>' );
	
	echo( '<i>$test = new CMongoObject( $collection, $identifier );</i><br>' );
	echo( "Identifier<pre>" ); print_r( $identifier ); echo( '</pre>' );
	$test = new CMongoObject( $collection, $identifier );
	echo( "<pre>" ); print_r( $test ); echo( '</pre>' );
	
	echo( '<i>new CMongoObject( array( \'NAME\' => \'TEST\' ) );</i><br>' );
	$test = new CMongoObject( array( 'NAME' => 'TEST' ) );
	echo( '<i>$id = $test->Commit( $collection, \'NEW\' );</i><br>' );
	$id = $test->Commit( $collection, 'NEW' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	
	echo( '<i>$test = new CMongoObject( $collection, \'NEW\' );</i><br>' );
	$test = new CMongoObject( $collection, 'NEW' );
	echo( "<pre>" ); print_r( $test ); echo( '</pre>' );
	 
	//
	// Test serialisation.
	//
	echo( '<h3>Serialisation</h3>' );
	
	echo( '<i>$test = new CMongoObject( $collection, \'Milko\' );</i><br>' );
	$test = new CMongoObject( $collection, $identifier );
	$test[ 'INT32' ] = new MongoInt32( 32 );
	$test[ 'INT64' ] = new MongoInt32( 64 );
	echo( "<pre>" ); print_r( $test ); echo( '</pre>' );
	
	echo( '<i>$serial = CMongoObject::SerialiseObject( $test, TRUE );</i><br>' );
	$serial = CMongoObject::SerialiseObject( $test, TRUE );
	echo( "<pre>" ); print_r( $serial ); echo( '</pre>' );
	
	echo( '<i>$object = CMongoObject::SerialiseObject( $serial, FALSE );</i><br>' );
	$object = CMongoObject::SerialiseObject( $serial, FALSE );
	echo( "<pre>" ); print_r( $object ); echo( '</pre>' );
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
