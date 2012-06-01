<?php

/**
 * {@link COntologyNodeIndex.php Base} object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * base object {@link COntologyNodeIndex class}.
 *
 *	@package	Test
 *	@subpackage	Persistence
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 19/04/2012
 */

/*=======================================================================================
 *																						*
 *								test_COntologyNodeIndex.php								*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."COntologyNodeIndex.php" );

/*=======================================================================================
 *	TEST ONTOLOGY NODE INDEXES															*
 *======================================================================================*/
 
//
// Test class.
//
try
{
	//
	// Init local storage.
	//
	$container = Array();
	
	//
	// Instantiate Neo4j client.
	//
	$container[ kTAG_NODE ] = $client = new Everyman\Neo4j\Client( 'localhost', 7474 );

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
	// Instantiate index database.
	//
	$container[ kTAG_TERM ] = $db;
	
	//
	// Save collection.
	//
	$collection = $db->selectCollection( kDEFAULT_CNT_NODES );
	 
	//
	// Create node.
	//
	echo( '<i>$node = $client->makeNode();</i><br>' );
	$node = $client->makeNode();
	echo( '<i>$node->setProperty( kTAG_TERM, \'SUBJECT\' )->save();</i><br>' );
	$node->setProperty( kTAG_TERM, 'SUBJECT' )->save();
	$id = $node->getId();
	 
	//
	// Test content.
	//
	echo( '<h3>Content</h3>' );

	try
	{
		echo( '<i>Not existing</i><br>' );
		echo( '<i>$test = new COntologyNodeIndex( 1, $client ) );</i><br>' );
		$test = new COntologyNodeIndex( 9999999999, $client );
		echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );
	
	} echo( '<hr>' );

	try
	{
		echo( '<i>Invalid content</i><br>' );
		echo( '<i>$test = new COntologyNodeIndex( \'pippo\', $client ) );</i><br>' );
		$test = new COntologyNodeIndex( 'pippo', $client );
		echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );
	
	} echo( '<hr>' );
	 
	echo( '<i>From a node</i><br>' );
	echo( '<i>$test = new COntologyNodeIndex( $node ) );</i><br>' );
	$test = new COntologyNodeIndex( $node );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>From an ID</i><br>' );
	echo( '<i>$test = new COntologyNodeIndex( $id, $client ) );</i><br>' );
	$test = new COntologyNodeIndex( $id, $client );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>From a string</i><br>' );
	echo( '<i>$test = new COntologyNodeIndex( (string) $id, $client ) );</i><br>' );
	$test = new COntologyNodeIndex( (string) $id, $client );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	echo( '<hr>' );

	//
	// Test store.
	//
	echo( '<h3>Commit</h3>' );

	echo( '<i>Commit to search</i><br>' );
	echo( '<i>$ok = $test->Commit( $collection );</i><br>' );
	$ok = $test->Commit( $collection );
	echo( "<pre>" ); print_r( $ok ); echo( '</pre>' );
	echo( "<pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	echo( '<hr>' );

	//
	// Test query.
	//
	echo( '<h3>Query</h3>' );

	echo( '<i>From a query</i><br>' );
	echo( '<i>$query = new CMongoQuery();</i><br>' );
	$query = new CMongoQuery();
	echo( '<i>$query->AppendStatement( CQueryStatement::Equals( kTAG_DATA.\'.\'.kTAG_TERM, \'SUBJECT\' ), kOPERATOR_AND );</i><br>' );
	$query->AppendStatement( CQueryStatement::Equals( kTAG_DATA.'.'.kTAG_TERM, 'SUBJECT' ), kOPERATOR_AND );
	echo( '<pre>' ); print_r( $query ); echo( '</pre>' );
	echo( '<i>$test = new COntologyNodeIndex( $query, $container ) );</i><br>' );
	$test = new COntologyNodeIndex( $query, $container );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	echo( '<hr>' );

	//
	// Test store.
	//
	echo( '<h3>Commit</h3>' );

	echo( '<i>From a MongoDB</i><br>' );
	echo( '<i>$status = $test->Commit( $db );</i><br>' );
	$status = $test->Commit( $db );
	echo( '<pre>' ); print_r( $status ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>From a MongoCollection</i><br>' );
	echo( '<i>$status = $test->Commit( $collection );</i><br>' );
	$status = $test->Commit( $collection );
	echo( '<pre>' ); print_r( $status ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>From a CMongoContainer</i><br>' );
	echo( '<i>$status = $test->Commit( new CMongoContainer( $collection ) );</i><br>' );
	$status = $test->Commit( new CMongoContainer( $collection ) );
	echo( '<pre>' ); print_r( $status ); echo( '</pre>' );
	echo( '<hr>' );

	try
	{
		echo( '<i>From a anything else</i><br>' );
		echo( '<i>$status = $test->Commit( $mongo );</i><br>' );
		$status = $test->Commit( $mongo );
		echo( '<pre>' ); print_r( $status ); echo( '</pre>' );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );
	
	} echo( '<hr>' );
	echo( '<hr>' );

	//
	// Test remove.
	//
	echo( '<h3>Delete</h3>' );

	echo( '<i>From a MongoCollection</i><br>' );
	echo( '<i>$status = $test->Commit( $collection, kFLAG_PERSIST_DELETE );</i><br>' );
	$status = $test->Commit( $collection, kFLAG_PERSIST_DELETE );
	echo( '<pre>' ); print_r( $status ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>From a CMongoContainer</i><br>' );
	echo( '<i>$status = $test->Commit( $db, kFLAG_PERSIST_DELETE );</i><br>' );
	$status = $test->Commit( $db, kFLAG_PERSIST_DELETE );
	echo( '<pre>' ); print_r( $status ); echo( '</pre>' );
	echo( '<hr>' );
	echo( '<hr>' );
	
	//
	// Cleanup.
	//
	$client->deleteNode( $node );
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
