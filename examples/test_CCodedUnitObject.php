<?php

/**
 * {@link CCodedUnitObject.php Base} object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * base object {@link CCodedUnitObject class}.
 *
 *	@package	Test
 *	@subpackage	Entities
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 12/04/2012
 */

/*=======================================================================================
 *																						*
 *								test_CCodedUnitObject.php								*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."CCodedUnitObject.php" );


/*=======================================================================================
 *	TEST USER PERSISTENT OBJECTS														*
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
	// Select database.
	//
	$db = $mongo->selectDB( "TEST" );
	
	//
	// Drop database.
	//
	$db->drop();
	
	//
	// Instantiate CMongoContainer.
	//
	$collection = new CMongoContainer( $db->selectCollection( 'CCodedUnitObject' ) );
	 
	//
	// Load entities.
	//
	echo( '<h3>Load entities</h3>' );
	
	echo( '<i><b>OBJECT 1</b></i><br>' );
	echo( '<i>$object1 = new CCodedUnitObject();</i><br>' );
	$object1 = new CCodedUnitObject();
	echo( '<i>$object1->Code( \'ENTITY1\' );</i><br>' );
	$object1->Code( 'ENTITY1' );
	echo( '<i>$object1->Kind( \'OBJECT\', TRUE );</i><br>' );
	$object1->Kind( 'OBJECT', TRUE );
	echo( '<i>$object1->Kind( \'PERSON\', TRUE );</i><br>' );
	$object1->Kind( 'PERSON', TRUE );
	echo( '<i>$object1->Stamp( new CDataTypeStamp() );</i><br>' );
	$object1->Stamp( new CDataTypeStamp() );
	echo( '<pre>' ); print_r( $object1 ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i><b>OBJECT 2</b></i><br>' );
	echo( '<i>$object2 = new CCodedUnitObject();</i><br>' );
	$object2 = new CCodedUnitObject();
	echo( '<i>$object2->Code( \'ENTITY2\' );</i><br>' );
	$object2->Code( 'ENTITY2' );
	echo( '<i>$object2->Kind( \'OBJECT\', TRUE );</i><br>' );
	$object2->Kind( 'OBJECT', TRUE );
	echo( '<i>$object2->Kind( \'USER\', TRUE );</i><br>' );
	$object2->Kind( 'USER', TRUE );
	echo( '<i>$object2->Stamp( new CDataTypeStamp() );</i><br>' );
	$object2->Stamp( new CDataTypeStamp() );
	echo( '<i>$object2->Valid( $object1 );</i><br>' );
	$object2->Valid( $object1 );
	echo( '<pre>' ); print_r( $object2 ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i><b>OBJECT 3</b></i><br>' );
	echo( '<i>$object3 = new CCodedUnitObject();</i><br>' );
	$object3 = new CCodedUnitObject();
	echo( '<i>$object3->Code( \'ENTITY3\' );</i><br>' );
	$object3->Code( 'ENTITY3' );
	echo( '<i>$object3->Relate( $object1, $object2, TRUE );</i><br>' );
	$object3->Relate( $object1, $object2, TRUE );
	echo( '<i>$object3->Relate( $object2, NULL, TRUE );</i><br>' );
	$object3->Relate( $object2, NULL, TRUE );
	echo( '<i>$object3->Relate( $object3->Code(), FALSE, TRUE );</i><br>' );
	$object3->Relate( $object3->Code(), FALSE, TRUE );
	echo( '<i>$object3->Relate( $object3->Code(), NULL, TRUE );</i><br>' );
	$object3->Relate( $object3->Code(), NULL, TRUE );
	echo( '<i>$object3->Valid( $object2 );</i><br>' );
	$object3->Valid( $object2 );
	echo( '<pre>' ); print_r( $object3 ); echo( '</pre>' );
	echo( '<hr>' );
	echo( '<hr>' );
	 
	//
	// Test persistence.
	//
	echo( '<h3>Persistence</h3>' );

	echo( '<i>Committing Object 3 should commit all others, since they are intertangled.<br></i>' );
	echo( '<i>$id3 = $object3->Commit( $collection, NULL, kFLAG_PERSIST_INSERT + kFLAG_STATE_ENCODED );</i><br>' );
	$id3 = $object3->Commit( $collection, NULL, kFLAG_PERSIST_INSERT + kFLAG_STATE_ENCODED );
	echo( "$id3<pre>" ); print_r( $object3 ); echo( '</pre>' );
exit;
	echo( '<i>$id1 = $object1->Commit( $collection, NULL, kFLAG_PERSIST_INSERT + kFLAG_STATE_ENCODED );</i><br>' );
	$id1 = $object1->Commit( $collection, NULL, kFLAG_PERSIST_INSERT + kFLAG_STATE_ENCODED );
	echo( "$id1<pre>" ); print_r( $object1 ); echo( '</pre>' );
	echo( '<i>$id2 = $object2->Commit( $collection, NULL, kFLAG_PERSIST_INSERT + kFLAG_STATE_ENCODED );</i><br>' );
	$id2 = $object2->Commit( $collection, NULL, kFLAG_PERSIST_INSERT + kFLAG_STATE_ENCODED );
	echo( "$id2<pre>" ); print_r( $object2 ); echo( '</pre>' );
	echo( '<hr>' );
	 
	//
	// Test valid chain.
	//
	echo( '<h3>Test valid chain</h3>' );

	echo( "<i>$object1</i><br>" );
	echo( '<i>$valid = CCodedUnitObject::ValidObject( $collection, $id1, kFLAG_STATE_ENCODED );</i><br>' );
	$valid = CCodedUnitObject::ValidObject( $collection, $id1, kFLAG_STATE_ENCODED );
	echo( '<pre>' ); print_r( $valid ); echo( '</pre>' );
	echo( '<hr>' );

	echo( "<i>$object2</i><br>" );
	echo( '<i>$valid = CCodedUnitObject::ValidObject( $collection, $id2, kFLAG_STATE_ENCODED );</i><br>' );
	$valid = CCodedUnitObject::ValidObject( $collection, $id2, kFLAG_STATE_ENCODED );
	echo( '<pre>' ); print_r( $valid ); echo( '</pre>' );
	echo( '<hr>' );

	echo( "<i>$object3</i><br>" );
	echo( '<i>$valid = CCodedUnitObject::ValidObject( $collection, $id3, kFLAG_STATE_ENCODED );</i><br>' );
	$valid = CCodedUnitObject::ValidObject( $collection, $id3, kFLAG_STATE_ENCODED );
	echo( '<pre>' ); print_r( $valid ); echo( '</pre>' );
	echo( '<hr>' );
	 
	//
	// Test valid validation.
	//
	echo( '<h3>Test valid validation</h3>' );

	try
	{
		echo( '<i>$object1->Valid( $id3 );</i><br>' );
		$object1->Valid( $id3 );
		$object1->Commit( $collection, NULL, kFLAG_PERSIST_UPDATE + kFLAG_STATE_ENCODED );
		echo( '<pre>' ); print_r( $object1 ); echo( '</pre>' );
		echo( '<i>$valid = CCodedUnitObject::ValidObject( $collection, $id2, kFLAG_STATE_ENCODED );</i><br>' );
		$valid = CCodedUnitObject::ValidObject( $collection, $id2, kFLAG_STATE_ENCODED );
		echo( '<pre>' ); print_r( $valid ); echo( '</pre>' );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );
		echo( '<br>' );
	}
	echo( '<hr>' );
	
	try
	{
		echo( '<i>$collection->Delete( $id2, kFLAG_STATE_ENCODED );</i><br>' );
		$collection->Delete( $id2, kFLAG_STATE_ENCODED );
		echo( '<i>$valid = CCodedUnitObject::ValidObject( $collection, $id3, kFLAG_STATE_ENCODED );</i><br>' );
		$valid = CCodedUnitObject::ValidObject( $collection, $id3, kFLAG_STATE_ENCODED );
		echo( '<pre>' ); print_r( $valid ); echo( '</pre>' );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );
		echo( '<br>' );
	}
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
