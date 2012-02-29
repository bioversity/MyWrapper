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
class Class2 extends CMongoUnitObject{}


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
	
	$ref = array( kTAG_COLLECTION_REFERENCE => 'Collection', kTAG_ID_REFERENCE => 'ID' );
	echo( 'Reference<pre>' ); print_r( $ref ); echo( '</pre>' );
	echo( '<i>$test = new CMongoDBRef( $ref );</i><br>' );
	$test = new CMongoDBRef( $ref );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	
	try
	{
		$object = new Class1( array( 'Name' => 'Milko', 'Surname' => 'Skofic' ) );
		echo( '<i>$object = new Class1( array( \'Name\' => \'Milko\', \'Surname\' => \'Skofic\' ) );</i><br>' );
		echo( 'Object<pre>' ); print_r( $object ); echo( '</pre>' );
		echo( '<i>$test = new CMongoDBRef( $object, $collection );</i><br>' );
		$test = new CMongoDBRef( $object, $collection );
		echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );
		echo( '<br>' );
	}

	$object = new Class1( array( 'Name' => 'Milko', 'Surname' => 'Skofic' ) );
	echo( '<i>$object = new Class1( array( \'Name\' => \'Milko\', \'Surname\' => \'Skofic\' ) );</i><br>' );
	echo( '<i>$id = $object->Commit( $collection );</i><br>' );
	$id = $object->Commit( $collection );
	echo( '<pre>' ); print_r( $object ); echo( '</pre>' );
	echo( '<i>$test = new CMongoDBRef( $object, $collection );</i><br>' );
	$test = new CMongoDBRef( $object, $collection );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
exit;
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
