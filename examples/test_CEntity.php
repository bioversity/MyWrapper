<?php

/**
 * {@link CEntity.php Base} object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * base object {@link CEntity class}.
 *
 *	@package	Test
 *	@subpackage	Entities
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 21/12/2012
 */

/*=======================================================================================
 *																						*
 *									test_CEntity.php									*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."CEntity.php" );


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
	$collection = new CMongoContainer( $db->selectCollection( 'CEntity' ) );
	 
	//
	// Load entities.
	//
	echo( '<h3>Load entities</h3>' );
	
	echo( '<i><b>ENTITY1</b></i><br>' );
	echo( '<i>$entity1 = new CEntity();</i><br>' );
	$entity1 = new CEntity();
	echo( '<i>$entity1->Code( \'ENTITY1\' );</i><br>' );
	$entity1->Code( 'ENTITY1' );
	echo( '<i>$entity1->Name( \'Milko A. Škofič\' );</i><br>' );
	$entity1->Name( 'Milko A. Škofič' );
	echo( '<i>$entity1->Kind( \'PERSON\', TRUE );</i><br>' );
	$entity1->Kind( 'PERSON', TRUE );
	echo( '<i>$entity1[ kTAG_ID_NATIVE ] = new CDataTypeBinary( md5( \'MILKO\', TRUE ) );</i><br>' );
	$entity1[ kTAG_ID_NATIVE ] = new CDataTypeBinary( md5( 'MILKO', TRUE ) );
	echo( '<pre>' ); print_r( $entity1 ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i><b>ENTITY2</b></i><br>' );
	echo( '<i>$entity2 = new CEntity();</i><br>' );
	$entity2 = new CEntity();
	echo( '<i>$entity2->Code( \'ENTITY2\' );</i><br>' );
	$entity2->Code( 'ENTITY2' );
	echo( '<i>$entity2->Name( \'John Smith\' );</i><br>' );
	$entity2->Name( 'John Smith' );
	echo( '<i>$entity2->Kind( \'PERSON\', TRUE );</i><br>' );
	$entity2->Kind( 'PERSON', TRUE );
	echo( '<i>$entity2->Kind( \'USER\', TRUE );</i><br>' );
	$entity2->Kind( 'USER', TRUE );
	echo( '<i>$entity2[ kTAG_ID_NATIVE ] = new CDataTypeMongoId();</i><br>' );
	$entity2[ kTAG_ID_NATIVE ] = new CDataTypeMongoId();
	echo( '<pre>' ); print_r( $entity2 ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i><b>ENTITY3</b></i><br>' );
	echo( '<i>$entity3 = new CEntity();</i><br>' );
	$entity3 = new CEntity();
	echo( '<i>$entity3->Code( \'ENTITY3\' );</i><br>' );
	$entity3->Code( 'ENTITY3' );
	echo( '<i>$entity3->Name( \'Luca Matteis\' );</i><br>' );
	$entity3->Name( 'Luca Matteis' );
	echo( '<pre>' ); print_r( $entity3 ); echo( '</pre>' );
	echo( '<hr>' );
	echo( '<hr>' );
	 
	//
	// Test relations.
	//
	echo( '<h3>Relations</h3>' );
	
	echo( '<i>$entity2->Affiliate( \'COLL\', $entity1, TRUE );</i><br>' );
	$entity2->Affiliate( 'COLL', $entity1, TRUE );
	echo( '<pre>' ); print_r( $entity2 ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$entity3->Affiliate( \'COLL\', $entity2, FALSE );</i><br>' );
	$entity3->Affiliate( 'COLL', $entity2, TRUE );
	echo( '<pre>' ); print_r( $entity3 ); echo( '</pre>' );
	echo( '<hr>' );
	 
	//
	// Test persistence.
	//
	echo( '<h3>Persistence</h3>' );

	echo( '<i>$identifier = $entity3->Commit( $collection, kFLAG_PERSIST_INSERT + kFLAG_STATE_ENCODED );</i><br>' );
	$identifier = $entity3->Commit( $collection, NULL, kFLAG_PERSIST_INSERT + kFLAG_STATE_ENCODED );
	echo( "entity1<pre>" ); print_r( $entity1 ); echo( '</pre>' );
	echo( "entity2<pre>" ); print_r( $entity2 ); echo( '</pre>' );
	echo( "entity3<pre>" ); print_r( $entity3 ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>$test = new CEntity( $collection, $identifier, kFLAG_STATE_ENCODED );</i><br>' );
	$test = new CEntity( $collection, $identifier, kFLAG_STATE_ENCODED );
	echo( "Identifier:<pre>" ); print_r( $identifier ); echo( '</pre>' );
	echo( "Object:<pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>$test = new CEntity( $collection, $entity1[ kTAG_ID_NATIVE ], kFLAG_STATE_ENCODED );</i><br>' );
	$test = new CEntity( $collection, $entity1[ kTAG_ID_NATIVE ], kFLAG_STATE_ENCODED );
	echo( "Identifier:<pre>" ); print_r( $entity1[ kTAG_ID_NATIVE ] ); echo( '</pre>' );
	echo( "Object:<pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	echo( '<hr>' );
	 
	//
	// Try duplicate affiliation.
	//
	echo( '<h3>Try duplicate affiliation</h3>' );

	echo( '<i>$entity2->Affiliate( \'COLL\', $entity1, TRUE );</i><br>' );
	$entity2->Affiliate( 'COLL', $entity1, TRUE );
	echo( '<pre>' ); print_r( $entity2 ); echo( '</pre>' );
	echo( '<i>$entity2->Commit( $collection, NULL, kFLAG_PERSIST_UPDATE + kFLAG_STATE_ENCODED );</i><br>' );
	$entity2->Commit( $collection, NULL, kFLAG_PERSIST_UPDATE + kFLAG_STATE_ENCODED );
	echo( '<pre>' ); print_r( $entity2 ); echo( '</pre>' );
	echo( '<hr>' );
	echo( '<hr>' );
	 
	//
	// Test valid reference.
	//
	echo( '<h3>Test valid reference</h3>' );

	echo( '<i>$entity1->Valid( $entity2 );</i><br>' );
	$entity1->Valid( $entity2 );
	$entity1->Commit( $collection, NULL, kFLAG_PERSIST_UPDATE + kFLAG_STATE_ENCODED );
	echo( '<pre>' ); print_r( $entity1 ); echo( '</pre>' );
	echo( '<i>$entity2->Valid( $entity3 );</i><br>' );
	$entity2->Valid( $entity3 );
	$entity2->Commit( $collection, NULL, kFLAG_PERSIST_UPDATE + kFLAG_STATE_ENCODED );
	echo( '<pre>' ); print_r( $entity2 ); echo( '</pre>' );
	echo( '<hr>' );
	echo( '<hr>' );
	 
	//
	// Test valid entity.
	//
	echo( '<h3>Test valid entity</h3>' );

	echo( '<i>$entity1[ kTAG_ID_NATIVE ];</i><br>' );
	echo( '<pre>' ); print_r( $entity1[ kTAG_ID_NATIVE ] ); echo( '</pre>' );
	echo( '<i>$valid = CEntity::ValidEntity( $collection, $entity1[ kTAG_ID_NATIVE ], kFLAG_STATE_ENCODED );</i><br>' );
	$valid = CEntity::ValidEntity( $collection, $entity1[ kTAG_ID_NATIVE ], kFLAG_STATE_ENCODED );
	echo( '<pre>' ); print_r( $valid ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>$entity2[ kTAG_ID_NATIVE ];</i><br>' );
	echo( '<pre>' ); print_r( $entity2[ kTAG_ID_NATIVE ] ); echo( '</pre>' );
	echo( '<i>$valid = CEntity::ValidEntity( $collection, $entity2[ kTAG_ID_NATIVE ], kFLAG_STATE_ENCODED );</i><br>' );
	$valid = CEntity::ValidEntity( $collection, $entity2[ kTAG_ID_NATIVE ], kFLAG_STATE_ENCODED );
	echo( '<pre>' ); print_r( $valid ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>$entity3[ kTAG_ID_NATIVE ];</i><br>' );
	echo( '<pre>' ); print_r( $entity3[ kTAG_ID_NATIVE ] ); echo( '</pre>' );
	echo( '<i>$valid = CEntity::ValidEntity( $collection, $entity3[ kTAG_ID_NATIVE ], kFLAG_STATE_ENCODED );</i><br>' );
	$valid = CEntity::ValidEntity( $collection, $entity3[ kTAG_ID_NATIVE ], kFLAG_STATE_ENCODED );
	echo( '<pre>' ); print_r( $valid ); echo( '</pre>' );
	echo( '<hr>' );
	echo( '<hr>' );
	 
	//
	// Test valid validation.
	//
	echo( '<h3>Test valid validation</h3>' );

	try
	{
		echo( '<i>$entity3->Valid( $entity1[ kTAG_ID_NATIVE ] );</i><br>' );
		$entity3->Valid( $entity1[ kTAG_ID_NATIVE ] );
		$entity3->Commit( $collection, NULL, kFLAG_PERSIST_UPDATE + kFLAG_STATE_ENCODED );
		echo( '<pre>' ); print_r( $entity3 ); echo( '</pre>' );
		echo( '<i>$entity1[ kTAG_ID_NATIVE ];</i><br>' );
		echo( '<pre>' ); print_r( $entity1[ kTAG_ID_NATIVE ] ); echo( '</pre>' );
		echo( '<i>$valid = CEntity::ValidEntity( $collection, $entity1[ kTAG_ID_NATIVE ], kFLAG_STATE_ENCODED );</i><br>' );
		$valid = CEntity::ValidEntity( $collection, $entity1[ kTAG_ID_NATIVE ], kFLAG_STATE_ENCODED );
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
		echo( '<i>$collection->Delete( $entity2[ kTAG_ID_NATIVE ], kFLAG_STATE_ENCODED );</i><br>' );
		$collection->Delete( $entity2[ kTAG_ID_NATIVE ], kFLAG_STATE_ENCODED );
		echo( '<i>$valid = CEntity::ValidEntity( $collection, $entity1[ kTAG_ID_NATIVE ], kFLAG_STATE_ENCODED );</i><br>' );
		$valid = CEntity::ValidEntity( $collection, $entity1[ kTAG_ID_NATIVE ], kFLAG_STATE_ENCODED );
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
