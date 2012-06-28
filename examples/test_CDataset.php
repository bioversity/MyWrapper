<?php

/**
 * {@link CDataset.php Base} object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * base object {@link CDataset class}.
 *
 *	@package	Test
 *	@subpackage	Entities
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 21/12/2012
 */

/*=======================================================================================
 *																						*
 *									test_CDataset.php									*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."CDataset.php" );


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
	$collection = new CMongoContainer( $db->selectCollection( 'CDataset' ) );
	$user_collection = new CMongoContainer( $db->selectCollection( CEntity::DefaultContainer() ) );
	
	//
	// Instantiate CMongoGridContainer.
	//
	$grid = new CMongoGridContainer( $db->getGridFS() );
	 
	//
	// Create user.
	//
	echo( '<h3>Create user</h3>' );
	
	echo( '<i>$user = new CUser();</i><br>' );
	$user = new CUser();
	echo( '<i>$user->Code( \'MIKO\' );</i><br>' );
	$user->Code( 'MIKO' );
	echo( '<i>$user->Password( \'unknown\' );</i><br>' );
	$user->Password( 'unknown' );
	echo( '<i>$user->Name( \'Milko Škofič\' );</i><br>' );
	$user->Name( 'Milko Škofič' );
	echo( '<i>$user->Email( \'m.skofic@cgiar.org\' );</i><br>' );
	$user->Email( 'm.skofic@cgiar.org' );
	echo( '<i>$user->Role( kROLE_FILE_IMPORT, TRUE );</i><br>' );
	$user->Role( kROLE_FILE_IMPORT, TRUE );
	echo( '<i>$user->Role( kROLE_USER_MANAGE, TRUE );</i><br>' );
	$user->Role( kROLE_USER_MANAGE, TRUE );
//	echo( '<i>$id = $user->Commit( $user_collection );</i><br>' );
//	$id = $user->Commit( $user_collection );
	echo( '<pre>' ); print_r( $user ); echo( '</pre>' );
	echo( '<hr>' );
	 
	//
	// Load dataset.
	//
	echo( '<h3>Load dataset</h3>' );
	
	echo( '<i>$test = new CDataset();</i><br>' );
	$test = new CDataset();
	echo( '<i>$test->Title( \'DATASET 1\' );</i><br>' );
	$test->Title( 'DATASET 1' );
	echo( '<i>$test->User( $user );</i><br>' );
	$test->User( $user );
	echo( '<i>$test->Name( \'Swiss national inventory\', \'ISO:639:3:Part1:en\' );</i><br>' );
	$test->Name( 'Swiss national inventory', 'ISO:639:3:Part1:en' );
	echo( '<i>$test->Name( \'Inventario nazionale svizzero\', \'ISO:639:3:Part1:it\' );</i><br>' );
	$test->Name( 'Inventario nazionale svizzero', 'ISO:639:3:Part1:it' );
	echo( '<i>$test->Description( \'Catalogue of accessions held in Switzerland\', \'ISO:639:3:Part1:en\' );</i><br>' );
	$test->Description( 'Catalogue of accessions held in Switzerland', 'ISO:639:3:Part1:en' );
	echo( '<i>$test->Description( \'Inventario di campioni di germoplasma conservati in Svizzera\', \'ISO:639:3:Part1:it\' );</i><br>' );
	$test->Description( 'Inventario di campioni di germoplasma conservati in Svizzera', 'ISO:639:3:Part1:it' );
	echo( '<i>$test->Domain( \':DOMAIN:110\', TRUE );</i><br>' );
	$test->Domain( ':DOMAIN:110', TRUE );
	echo( '<i>$test->Domain( \':DOMAIN:120\', TRUE );</i><br>' );
	$test->Domain( ':DOMAIN:120', TRUE );
	echo( '<i>$test->Domain( \':DOMAIN:200\', TRUE );</i><br>' );
	$test->Domain( ':DOMAIN:200', TRUE );
	echo( '<i>$test->Category( \':CATEGORY:1\', TRUE );</i><br>' );
	$test->Category( ':CATEGORY:1', TRUE );
	echo( '<i>$test->Category( \':CATEGORY:5\', TRUE );</i><br>' );
	$test->Category( ':CATEGORY:5', TRUE );
	echo( '<i>$test->Created( new CDataTypeStamp( \'2012/01/12\') );</i><br>' );
	$test->Created( new CDataTypeStamp( '2012/01/12' ) );
	echo( '<i>$test->Modified( new CDataTypeStamp() );</i><br>' );
	$test->Modified( new CDataTypeStamp() );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );

	//
	// Commit dataset.
	//
	echo( '<h3>Commit dataset</h3>' );
	
	echo( '<i>$id = $test->Commit( $collection );</i><br>' );
	$id = $test->Commit( $collection );
	echo( "$id<pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );

	//
	// Create file metadata.
	//
	echo( '<h3>Create file metadata</h3>' );
	
	echo( '<i>$file = new CDatasetFile();</i><br>' );
	$file = new CDatasetFile();
	echo( '<i>$file->Referenced( \'Ref 1\', TRUE );</i><br>' );
	$file->Referenced( 'Ref 1', TRUE );
	echo( '<i>$file->Referenced( \'Ref 2\', TRUE );</i><br>' );
	$file->Referenced( 'Ref 2', TRUE );
	echo( '<i>$file->Status( \'Original\', TRUE );</i><br>' );
	$file->Status( 'Original', TRUE );
	echo( '<i>$file->Status( \'Processed\', TRUE );</i><br>' );
	$file->Status( 'Processed', TRUE );
	echo( '<i>$file->Kind( \'Kind 1\', TRUE );</i><br>' );
	$file->Kind( 'Kind 1', TRUE );
	echo( '<i>$file->Kind( \'Kind 2\', TRUE );</i><br>' );
	$file->Kind( 'Kind 2', TRUE );
	echo( '<i>$file->Column( 0, \'Tag 1\', \'Title 1\' );</i><br>' );
	$file->Column( 0, 'Tag1', 'Title 1' );
	echo( '<i>$file->Column( 1, \'Tag 2\', \'Title 2\' );</i><br>' );
	$file->Column( 1, 'Tag2', 'Title 2' );
	echo( '<i>$test->File( 1, \'Tag 2\', \'Title 2\' );</i><br>' );
	$file->Column( 1, 'Tag2', 'Title 2' );
	echo( '<pre>' ); print_r( $file ); echo( '</pre>' );
	echo( '<hr>' );

	//
	// Store file.
	//
	echo( '<h3>Store file</h3>' );
	
	echo( '<i>$file_id = $test->StoreFile( __FILE__, $grid, $file );</i><br>' );
	$file_id = $test->StoreFile( __FILE__, $grid, $file );
	echo( 'ID: <pre>' ); print_r( $file_id ); echo( '</pre>' );
	echo( '<hr>' );

	//
	// Retrieve file.
	//
	echo( '<h3>Retrieve file</h3>' );
	
	echo( '<i$file = $grid->Load( $file_id );</i><br>' );
	$file = $grid->Load( $file_id );
	echo( 'FILE: <pre>' ); print_r( $file ); echo( '</pre>' );
	echo( '<hr>' );

	//
	// Retrieve object.
	//
	echo( '<i>$index = (string) $test->User().kTOKEN_INDEX_SEPARATOR.$test->Title();</i><br>' );
	$index = (string) $test->User().kTOKEN_INDEX_SEPARATOR.$test->Title();
	echo( '<pre>' ); print_r( $index ); echo( '</pre>' );
	echo( '<i>$test = new CDataset( $collection, CDataset::HashIndex( $index ) );</i><br>' );
	$test = new CDataset( $collection, CDataset::HashIndex( $index ) );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$test = new CDataset( $collection, $id );</i><br>' );
	$test = new CDataset( $collection, $id );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
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
