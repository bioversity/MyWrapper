<?php

/**
 * {@link CGraphNodeObject.php Base} object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * base object {@link CGraphNodeObject class}.
 *
 *	@package	Test
 *	@subpackage	Entities
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 16/04/2012
 */

/*=======================================================================================
 *																						*
 *								test_CGraphNodeObject.php								*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."CGraphNodeObject.php" );


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
	$collection = new CMongoContainer( $db->selectCollection( 'CGraphNodeObject' ) );
	 
	//
	// Load entities.
	//
	echo( '<h3>Load entities</h3>' );
	
	echo( '<i><b>OBJECT 1</b></i><br>' );
	echo( '<i>$object1 = new CGraphNodeObject();</i><br>' );
	$object1 = new CGraphNodeObject();
	echo( '<i>$object1[ kTAG_ID ] = \'ENTITY1\';</i><br>' );
	$object1[ kTAG_ID ] = 'ENTITY1';
	echo( '<pre>' ); print_r( $object1 ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i><b>OBJECT 2</b></i><br>' );
	echo( '<i>$object2 = new CGraphNodeObject();</i><br>' );
	$object2 = new CGraphNodeObject();
	echo( '<i>$object1[ kTAG_ID ] = \'ENTITY2\';</i><br>' );
	$object1[ kTAG_ID ] = 'ENTITY2';
	echo( '<i>$object2->Valid( $object1 );</i><br>' );
	$object2->Valid( $object1 );
	echo( '<pre>' ); print_r( $object2 ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i><b>OBJECT 3</b></i><br>' );
	echo( '<i>$object3 = new CGraphNodeObject();</i><br>' );
	$object3 = new CGraphNodeObject();
	echo( '<i>$object1[ kTAG_ID ] = \'ENTITY3\';</i><br>' );
	$object1[ kTAG_ID ] = 'ENTITY3';
	echo( '<i>$object3->Relate( $object1, $object2, TRUE );</i><br>' );
	$object3->Relate( $object1, $object2, TRUE );
	echo( '<i>$object3->Relate( $object2, NULL, TRUE );</i><br>' );
	$object3->Relate( $object2, NULL, TRUE );
	echo( '<i>$object3->Relate( $object3[ kTAG_ID ], FALSE, TRUE );</i><br>' );
	$object3->Relate( $object3[ kTAG_ID ], FALSE, TRUE );
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
	echo( "$id1<pre>" ); print_r( $object1 ); echo( '</pre>' );
	echo( "$id2<pre>" ); print_r( $object2 ); echo( '</pre>' );
	echo( '<hr>' );
exit;
	 
	//
	// Test valid chain.
	//
	echo( '<h3>Test valid chain</h3>' );

	echo( "<i>$object1</i><br>" );
	echo( '<i>$valid = CGraphNodeObject::ValidObject( $collection, $id1, kFLAG_STATE_ENCODED );</i><br>' );
	$valid = CGraphNodeObject::ValidObject( $collection, $id1, kFLAG_STATE_ENCODED );
	echo( '<pre>' ); print_r( $valid ); echo( '</pre>' );
	echo( '<hr>' );

	echo( "<i>$object2</i><br>" );
	echo( '<i>$valid = CGraphNodeObject::ValidObject( $collection, $id2, kFLAG_STATE_ENCODED );</i><br>' );
	$valid = CGraphNodeObject::ValidObject( $collection, $id2, kFLAG_STATE_ENCODED );
	echo( '<pre>' ); print_r( $valid ); echo( '</pre>' );
	echo( '<hr>' );

	echo( "<i>$object3</i><br>" );
	echo( '<i>$valid = CGraphNodeObject::ValidObject( $collection, $id3, kFLAG_STATE_ENCODED );</i><br>' );
	$valid = CGraphNodeObject::ValidObject( $collection, $id3, kFLAG_STATE_ENCODED );
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
		echo( '<i>$valid = CGraphNodeObject::ValidObject( $collection, $id2, kFLAG_STATE_ENCODED );</i><br>' );
		$valid = CGraphNodeObject::ValidObject( $collection, $id2, kFLAG_STATE_ENCODED );
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
		echo( '<i>$valid = CGraphNodeObject::ValidObject( $collection, $id3, kFLAG_STATE_ENCODED );</i><br>' );
		$valid = CGraphNodeObject::ValidObject( $collection, $id3, kFLAG_STATE_ENCODED );
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
