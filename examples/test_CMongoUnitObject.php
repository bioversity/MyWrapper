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
 *	TEST CLASS																			*
 *======================================================================================*/

//
// Test class.
//
class MyTest extends CMongoUnitObject
{
	protected function _id()
	{
		return $this->offsetGet( 'SURNAME' );										// ==>
	}
}


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
	
	echo( '<h4>Let Mongo give the ID</h4>' );
	$test = new CMongoUnitObject( $container );
	echo( '<i>$test = new CMongoUnitObject( $container );</i><br>' );
	$first = $test->Commit( $collection );
	echo( '<i>$first = $test->Commit( $collection );</i><br>' );
	echo( "<pre>" ); print_r( $first ); echo( '</pre>' );
	echo( "<pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<h4>Let class give the ID</h4>' );
	$test = new MyTest( $container );
	echo( '<i>$test = new MyTest( $container );</i><br>' );
	$second = $test->Commit( $collection );
	echo( '<i>$second = $test->Commit( $collection );</i><br>' );
	echo( "<pre>" ); print_r( $second ); echo( '</pre>' );
	echo( "<pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<h4>Provide the ID</h4>' );
	$test = new CMongoUnitObject( $container );
	echo( '<i>$test = new CMongoUnitObject( $container );</i><br>' );
	$third = $test->Commit( $collection, 'ID' );
	echo( '<i>$third = $test->Commit( $collection, \'ID\' );</i><br>' );
	echo( "<pre>" ); print_r( $third ); echo( '</pre>' );
	echo( "<pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<h4>Retrieve first with NewObject()</h4>' );
	echo( '<i>$test = CMongoUnitObject::NewObject( $collection, $first );</i><br>' );
	$test = CMongoUnitObject::NewObject( $collection, $first );
	echo( "<pre>" ); print_r( $first ); echo( '</pre>' );
	echo( "<pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<h4>Retrieve second with NewObject()</h4>' );
	echo( '<i>$test = CMongoUnitObject::NewObject( $collection, $second );</i><br>' );
	$test = CMongoUnitObject::NewObject( $collection, $second );
	echo( "<pre>" ); print_r( $second ); echo( '</pre>' );
	echo( "<pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<h4>Retrieve third with NewObject()</h4>' );
	echo( '<i>$test = CMongoUnitObject::NewObject( $collection, $third );</i><br>' );
	$test = CMongoUnitObject::NewObject( $collection, $third );
	echo( "<pre>" ); print_r( $third ); echo( '</pre>' );
	echo( "<pre>" ); print_r( $test ); echo( '</pre>' );
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
