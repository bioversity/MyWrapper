<?php

/**
 * {@link CGraphUnitObject.php Base} object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * base object {@link CGraphUnitObject class}.
 *
 *	@package	Test
 *	@subpackage	Entities
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 12/04/2012
 */

/*=======================================================================================
 *																						*
 *								test_CGraphUnitObject.php								*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."CGraphUnitObject.php" );


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
	$collection = new CMongoContainer( $db->selectCollection( 'CGraphUnitObject' ) );
	 
	//
	// Load entities.
	//
	echo( '<h3>Load entities</h3>' );
	
	echo( '<i><b>OBJECT 1</b></i><br>' );
	echo( '<i>$object1 = new CGraphUnitObject();</i><br>' );
	$object1 = new CGraphUnitObject();
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
	echo( '<i>$object2 = new CGraphUnitObject();</i><br>' );
	$object2 = new CGraphUnitObject();
	echo( '<i>$object2->Code( \'ENTITY2\' );</i><br>' );
	$object2->Code( 'ENTITY2' );
	echo( '<i>$object2->Kind( \'OBJECT\', TRUE );</i><br>' );
	$object2->Kind( 'OBJECT', TRUE );
	echo( '<i>$object2->Kind( \'USER\', TRUE );</i><br>' );
	$object2->Kind( 'USER', TRUE );
	echo( '<i>$object2->Stamp( new CDataTypeStamp() );</i><br>' );
	$object2->Stamp( new CDataTypeStamp() );
	echo( '<i>$object2->RelatedFrom( $object1, TRUE );</i><br>' );
	$object2->RelatedFrom( $object1, TRUE );
	echo( '<i>$object2->Valid( $object1->Code() );</i><br>' );
	$object2->Valid( $object1->Code() );
	echo( '<pre>' ); print_r( $object2 ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i><b>OBJECT 3</b></i><br>' );
	echo( '<i>$object3 = new CGraphUnitObject();</i><br>' );
	$object3 = new CGraphUnitObject();
	echo( '<i>$object3->Code( \'ENTITY3\' );</i><br>' );
	$object3->Code( 'ENTITY3' );
	echo( '<i>$object3->RelatedFrom( $object1, TRUE );</i><br>' );
	$object3->RelatedFrom( $object1, TRUE );
	echo( '<i>$object3->RelateTo( $object2, TRUE );</i><br>' );
	$object3->RelateTo( $object2, TRUE );
	echo( '<i>$object3->Valid( $object2->Code() );</i><br>' );
	$object3->Valid( $object2->Code() );
	echo( '<pre>' ); print_r( $object3 ); echo( '</pre>' );
	echo( '<hr>' );
	echo( '<hr>' );
	 
	//
	// Test persistence.
	//
	echo( '<h3>Persistence</h3>' );

	echo( '<i>$id3 = $object3->Commit( $collection, NULL, kFLAG_PERSIST_INSERT + kFLAG_STATE_ENCODED );</i><br>' );
	$id3 = $object3->Commit( $collection, NULL, kFLAG_PERSIST_INSERT + kFLAG_STATE_ENCODED );
	echo( "<pre>" ); print_r( $object1 ); echo( '</pre>' );
	echo( "<pre>" ); print_r( $object2 ); echo( '</pre>' );
	echo( "<pre>" ); print_r( $object3 ); echo( '</pre>' );
	echo( '<hr>' );
	
	$id1 = $object1[ kTAG_ID ];
	$id2 = $object2[ kTAG_ID ];
	 
	//
	// Test valid chain.
	//
	echo( '<h3>Test valid chain</h3>' );

	echo( "<i>$id1</i><br>" );
	echo( '<i>$valid = CGraphUnitObject::ValidObject( $collection, $id1, kFLAG_STATE_ENCODED );</i><br>' );
	$valid = CGraphUnitObject::ValidObject( $collection, $id1, kFLAG_STATE_ENCODED );
	echo( '<pre>' ); print_r( $valid ); echo( '</pre>' );
	echo( '<hr>' );

	echo( "<i>$id2</i><br>" );
	echo( '<i>$valid = CGraphUnitObject::ValidObject( $collection, $id2, kFLAG_STATE_ENCODED );</i><br>' );
	$valid = CGraphUnitObject::ValidObject( $collection, $id2, kFLAG_STATE_ENCODED );
	echo( '<pre>' ); print_r( $valid ); echo( '</pre>' );
	echo( '<hr>' );

	echo( "<i>$id3</i><br>" );
	echo( '<i>$valid = CGraphUnitObject::ValidObject( $collection, $id3, kFLAG_STATE_ENCODED );</i><br>' );
	$valid = CGraphUnitObject::ValidObject( $collection, $id3, kFLAG_STATE_ENCODED );
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
		echo( '<i>$valid = CGraphUnitObject::ValidObject( $collection, $id2, kFLAG_STATE_ENCODED );</i><br>' );
		$valid = CGraphUnitObject::ValidObject( $collection, $id2, kFLAG_STATE_ENCODED );
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
		echo( '<i>$valid = CGraphUnitObject::ValidObject( $collection, $id3, kFLAG_STATE_ENCODED );</i><br>' );
		$valid = CGraphUnitObject::ValidObject( $collection, $id3, kFLAG_STATE_ENCODED );
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
