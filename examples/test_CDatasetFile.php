<?php
	
/**
 * {@link CDatasetFile.php Query} object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * {@link CDatasetFile class}.
 *
 *	@package	Test
 *	@subpackage	Persistence
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 03/04/2012
 */

/*=======================================================================================
 *																						*
 *									test_CDatasetFile.php								*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."CDatasetFile.php" );


/*=======================================================================================
 *	TEST MAIL ADDRESS OBJECT															*
 *======================================================================================*/
 
//
// TRY BLOCK.
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
	$collection = new CMongoContainer( $db->selectCollection( 'CDatasetFile' ) );
	 
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
	echo( '<pre>' ); print_r( $user ); echo( '</pre>' );
	echo( '<hr>' );
	 
	//
	// Create dataset.
	//
	echo( '<h3>Create dataset</h3>' );
	
	echo( '<i>$dataset = new CDataset();</i><br>' );
	$dataset = new CDataset();
	echo( '<i>$dataset->Title( \'DATASET 1\' );</i><br>' );
	$dataset->Title( 'DATASET 1' );
	echo( '<i>$dataset->User( $user );</i><br>' );
	$dataset->User( $user );
	echo( '<i>$dataset->Name( \'Swiss national inventory\', \'ISO:639:3:Part1:en\' );</i><br>' );
	$dataset->Name( 'Swiss national inventory', 'ISO:639:3:Part1:en' );
	echo( '<i>$dataset->Name( \'Inventario nazionale svizzero\', \'ISO:639:3:Part1:it\' );</i><br>' );
	$dataset->Name( 'Inventario nazionale svizzero', 'ISO:639:3:Part1:it' );
	echo( '<i>$dataset->Description( \'Catalogue of accessions held in Switzerland\', \'ISO:639:3:Part1:en\' );</i><br>' );
	$dataset->Description( 'Catalogue of accessions held in Switzerland', 'ISO:639:3:Part1:en' );
	echo( '<i>$dataset->Description( \'Inventario di campioni di germoplasma conservati in Svizzera\', \'ISO:639:3:Part1:it\' );</i><br>' );
	$dataset->Description( 'Inventario di campioni di germoplasma conservati in Svizzera', 'ISO:639:3:Part1:it' );
	echo( '<i>$dataset->Domain( \':DOMAIN:110\', TRUE );</i><br>' );
	$dataset->Domain( ':DOMAIN:110', TRUE );
	echo( '<i>$dataset->Domain( \':DOMAIN:120\', TRUE );</i><br>' );
	$dataset->Domain( ':DOMAIN:120', TRUE );
	echo( '<i>$dataset->Domain( \':DOMAIN:200\', TRUE );</i><br>' );
	$dataset->Domain( ':DOMAIN:200', TRUE );
	echo( '<i>$dataset->Category( \':CATEGORY:1\', TRUE );</i><br>' );
	$dataset->Category( ':CATEGORY:1', TRUE );
	echo( '<i>$dataset->Category( \':CATEGORY:5\', TRUE );</i><br>' );
	$dataset->Category( ':CATEGORY:5', TRUE );
	echo( '<i>$dataset->Created( new CDataTypeStamp( \'2012/01/12\') );</i><br>' );
	$dataset->Created( new CDataTypeStamp( '2012/01/12' ) );
	echo( '<i>$dataset->Modified( new CDataTypeStamp() );</i><br>' );
	$dataset->Modified( new CDataTypeStamp() );
	echo( '<i>$dataset_id = $dataset->Commit( $collection );</i><br>' );
	$dataset_id = $dataset->Commit( $collection );
	echo( "$dataset_id<pre>" ); print_r( $dataset ); echo( '</pre>' );
	echo( '<hr>' );

	//
	// Instantiate empty object.
	//
	echo( '<i>$test = new CDatasetFile();</i><br>' );
	$test = new CDatasetFile();
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Add dataset.
	//
	echo( '<i>$test->Dataset( $dataset_id );</i><br>' );
	$test->Dataset( $dataset_id );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Add references.
	//
	echo( '<i>$test->Referenced( \'Ref 1\', TRUE );</i><br>' );
	$test->Referenced( 'Ref 1', TRUE );
	echo( '<i>$test->Referenced( \'Ref 2\', TRUE );</i><br>' );
	$test->Referenced( 'Ref 2', TRUE );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Add status.
	//
	echo( '<i>$test->Status( \'Original\', TRUE );</i><br>' );
	$test->Status( 'Original', TRUE );
	echo( '<i>$test->Status( \'Processed\', TRUE );</i><br>' );
	$test->Status( 'Processed', TRUE );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Add kind.
	//
	echo( '<i>$test->Kind( \'Kind 1\', TRUE );</i><br>' );
	$test->Kind( 'Kind 1', TRUE );
	echo( '<i>$test->Kind( \'Kind 2\', TRUE );</i><br>' );
	$test->Kind( 'Kind 2', TRUE );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Add columns.
	//
	echo( '<i>$test->Column( 0, \'Tag 1\', \'Title 1\' );</i><br>' );
	$test->Column( 0, 'Tag1', 'Title 1' );
	echo( '<i>$test->Column( 1, \'Tag 2\', \'Title 2\' );</i><br>' );
	$test->Column( 1, 'Tag2', 'Title 2' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Modify columns.
	//
	echo( '<i>$test->Column( 0, \'Tag 0\' );</i><br>' );
	$test->Column( 0, 'Tag 0' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Retrieve column.
	//
	echo( '<i>$found = $test->Column( 1 );</i><br>' );
	$found = $test->Column( 1 );
	echo( '<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Retrieve columns.
	//
	echo( '<i>$found = $test->Column();</i><br>' );
	$found = $test->Column();
	echo( '<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Delete column.
	//
	echo( '<i>$test->Column( 1, FALSE );</i><br>' );
	$test->Column( 1, FALSE );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Instantiate from array.
	//
	echo( '<i>$test = new CDatasetFile( $test );</i><br>' );
	$test = new CDatasetFile( $test );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<h3>DONE</h3>' );
}
catch( Exception $error )
{
	echo( '<h3>Unexpected exception</h3>' );
	echo( CException::AsHTML( $error ) );
	echo( '<pre>'.(string) $error.'</pre>' );
	echo( '<hr>' );
}

?>
