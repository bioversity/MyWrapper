<?php

/**
 * {@link CMongoUnitObject.php Base} object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * base object {@link CMongoUnitObject class}.
 *
 *	@package	Test
 *	@subpackage	Persistence
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 13/12/2012
 */

/*=======================================================================================
 *																						*
 *									test_CUnitObject.php								*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."CMongoUnitObject.php" );


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
	$collection = $db->selectCollection( 'CMongoUnitObject' );
	 
	//
	// Test instantiation.
	//
	echo( '<h3>Instantiation</h3>' );
	
	echo( '<i>new CMongoUnitObject();</i><br>' );
	$test = new CMongoUnitObject();
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	
	$container = array( 'NAME' => 'Milko', 'SURNAME' => 'Skofic' );
	echo( 'Container:<pre>' ); print_r( $container ); echo( '</pre>' );
	echo( '<i>new CMongoUnitObject( $container );</i><br>' );
	$test = new CMongoUnitObject( $container );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	 
	//
	// Test persistence.
	//
	echo( '<h3>Persistence</h3>' );
	
	echo( '<i>$identifier = $test->Commit( $collection );</i><br>' );
	$identifier = $test->Commit( $collection );
	echo( "$identifier<pre>" ); print_r( $test ); echo( '</pre>' );
	
	echo( '<i>$test = new CMongoUnitObject( $collection, $identifier );</i><br>' );
	$test = new CMongoUnitObject( $collection, $identifier );
	echo( "<pre>" ); print_r( $test ); echo( '</pre>' );
	
	echo( '<i>new CMongoUnitObject( array( \'NAME\' => \'TEST\' ) );</i><br>' );
	$test = new CMongoUnitObject( array( 'NAME' => 'TEST' ) );
	echo( '<i>$identifier = $test->Commit( $collection, \'NEW\' );</i><br>' );
	$identifier = $test->Commit( $collection, 'NEW' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	
	echo( '<i>$test = new CMongoUnitObject( $collection, \'NEW\' );</i><br>' );
	$test = new CMongoUnitObject( $collection, 'NEW' );
	echo( "<pre>" ); print_r( $test ); echo( '</pre>' );
	
	echo( '<i>$test[ \'NEW\' ] = \'NEW\';</i><br>' );
	echo( '<i>$test->Commit( $collection );</i><br>' );
	$test[ 'NEW' ] = 'NEW';
	$test->Commit( $collection );
	echo( "<pre>" ); print_r( $test ); echo( '</pre>' );
	
	echo( '<i>$test = new CMongoObject();</i><br>' );
	echo( '<i>$test[ \'OTHER\' ] = \'Other\';</i><br>' );
	echo( '<i>$test->Commit( $collection, \'OTHER\' );;</i><br>' );
	echo( '<i>$test = NewObject( $collection, \'OTHER\' );</i><br>' );
	$test = new CMongoObject();
	$test[ 'OTHER' ] = 'Other';
	$test->Commit( $collection, 'OTHER' );
	$test = CMongoUnitObject::NewObject( $collection, 'OTHER' );
	echo( "<pre>" ); print_r( $test ); echo( '</pre>' );
	
	echo( '<i>$test = NewObject( $collection, \'NEW\' );</i><br>' );
	$test = CMongoUnitObject::NewObject( $collection, 'NEW' );
	echo( "<pre>" ); print_r( $test ); echo( '</pre>' );
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
