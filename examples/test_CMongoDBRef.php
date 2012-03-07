<?php

/**
 * {@link CMongoDBRef.php Base} object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * base object {@link CMongoDBRef class}.
 *
 *	@package	Test
 *	@subpackage	Persistence
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 13/12/2012
 */

/*=======================================================================================
 *																						*
 *									test_CMongoDBRef.php								*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."CMongoDBRef.php" );

//
// Related includes.
//
require_once( kPATH_LIBRARY_SOURCE."CMongoUnitObject.php" );


/*=======================================================================================
 *	TEST CLASSES																		*
 *======================================================================================*/
 
//
// Test class 1.
//
class Class1 extends CMongoUnitObject{}
 
//
// Test class 2.
//
class Class2 extends CMongoObject{}


/*=======================================================================================
 *	TEST DEFAULT EXCEPTIONS																*
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
	$collection = $db->selectCollection( 'CMongoDBRef' );
	 
	//
	// Test instantiation.
	//
	echo( '<h3>Instantiation</h3>' );
	
	echo( '<i>$test = new CMongoDBRef();</i><br>' );
	$test = new CMongoDBRef();
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	$ref = array( kTAG_COLLECTION_REFERENCE => 'Collection', kTAG_ID_REFERENCE => 'ID' );
	echo( 'Reference<pre>' ); print_r( $ref ); echo( '</pre>' );
	echo( '<i>$test = new CMongoDBRef( $ref );</i><br>' );
	$test = new CMongoDBRef( $ref );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	try
	{
		echo( '<i>$object = new Class1( array( \'Name\' => \'Milko\', \'Surname\' => \'Skofic\' ) );</i><br>' );
		$object = new Class1( array( 'Name' => 'Milko', 'Surname' => 'Skofic' ) );
		echo( '<pre>' ); print_r( $object ); echo( '</pre>' );
		echo( '<i>$test = new CMongoDBRef( $object, $collection );</i><br>' );
		$test = new CMongoDBRef( $object, $collection );
		echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );
		echo( '<br>' );
	}
	echo( '<hr>' );

	$object = new Class1( array( 'Name' => 'Milko', 'Surname' => 'Skofic' ) );
	echo( '<i>$object = new Class1( array( \'Name\' => \'Milko\', \'Surname\' => \'Skofic\' ) );</i><br>' );
	echo( '<i>$id = $object->Commit( $collection );</i><br>' );
	$id = $object->Commit( $collection );
	echo( '<pre>' ); print_r( $object ); echo( '</pre>' );
	echo( '<i>$test = new CMongoDBRef( $object, $collection );</i><br>' );
	$test = new CMongoDBRef( $object, $collection );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	$ref = array( kTAG_COLLECTION_REFERENCE => 'Collection',
				  kTAG_ID_REFERENCE => 'ID',
				  kTAG_DATABASE_REFERENCE => 'OtherDatabase' );
	echo( 'Reference<pre>' ); print_r( $ref ); echo( '</pre>' );
	echo( '<i>$test = new CMongoDBRef( $ref, $collection );</i><br>' );
	$test = new CMongoDBRef( $ref, $collection );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	 
	//
	// Test resolve.
	//
	echo( '<h3>Resolve</h3>' );
	
	$ref = array( kTAG_ID_REFERENCE => $id, kTAG_COLLECTION_REFERENCE => $collection->getname() );
	echo( '<i>$ref = array( kTAG_ID_REFERENCE => $id, kTAG_COLLECTION_REFERENCE => $collection->getname() );</i><br>' );
	echo( '<i>$test = new CMongoDBRef( $ref );</i><br>' );
	$test = new CMongoDBRef( $ref );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	$object = $test->Resolve( $collection );
	echo( '<i>$object = $test->Resolve( $collection );</i><br>' );
	echo( '<pre>' ); print_r( $object ); echo( '</pre>' );
	echo( '<hr>' );

	$object = new Class2( array( 'Name' => 'Luca', 'Surname' => 'Sampieri' ) );
	echo( '<i>$object = new Class2( array( \'Name\' => \'Luca\', \'Surname\' => \'Sampieri\' ) );</i><br>' );
	echo( '<i>$id = $object->Commit( $collection );</i><br>' );
	$id = $object->Commit( $collection );
	echo( '<pre>' ); print_r( $object ); echo( '</pre>' );
	echo( '<i>$test = new CMongoDBRef( $object, $collection );</i><br>' );
	$test = new CMongoDBRef( $object, $collection );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	$object = $test->Resolve( $collection );
	echo( '<i>$object = $test->Resolve( $collection );</i><br>' );
	echo( '<pre>' ); print_r( $object ); echo( '</pre>' );
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
