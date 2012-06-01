<?php

/**
 * {@link COntologyEdgeIndex.php Base} object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * base object {@link COntologyEdgeIndex class}.
 *
 *	@package	Test
 *	@subpackage	Persistence
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 31/05/2012
 */

/*=======================================================================================
 *																						*
 *								test_COntologyEdgeIndex.php								*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."COntologyEdgeIndex.php" );

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
	$client = $container[ kTAG_NODE ] = new Everyman\Neo4j\Client( 'localhost', 7474 );

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
	$container[ kTAG_TERM ]
		= new CMongoContainer
			( $db->selectCollection( 'COntologyEdge' ) );
	
	//
	// Instantiate MongoCollection.
	//
	$collection = $db->selectCollection( kDEFAULT_CNT_EDGES );
	
	//
	// Instantiate CMongoCollection.
	//
	$my_container = new CMongoContainer( $db->selectCollection( kDEFAULT_CNT_EDGES ) );
	
	//
	// Create composite container.
	//
	$composite = array( kTAG_NODE => $client, kTAG_TERM => $db );

	//
	// Create terms.
	//
	echo( '<h3>Create terms</h3>' );

	echo( '<i>$subject_term = new COntologyTerm();</i><br>' );
	$subject_term = new COntologyTerm();
	echo( '<i>$subject_term->Code( \'SUBJECT\' );</i><br>' );
	$subject_term->Code( 'SUBJECT' );
	echo( '<i>$subject_term->Name( \'Subject term\', \'en\' );</i><br>' );
	$subject_term->Name( 'Subject term', 'en' );
	echo( '<i>$subject_term->Commit( $container[ kTAG_TERM ] );</i><br>' );
	$subject_term->Commit( $container[ kTAG_TERM ] );
	
	echo( '<i>$predicate_term = new COntologyTerm();</i><br>' );
	$predicate_term = new COntologyTerm();
	echo( '<i>$predicate_term->Code( \'PREDICATE\' );</i><br>' );
	$predicate_term->Code( 'PREDICATE' );
	echo( '<i>$predicate_term->Name( \'Predicate term\', \'en\' );</i><br>' );
	$predicate_term->Name( 'Predicate term', 'en' );
	echo( '<i>$predicate_term->Commit( $container[ kTAG_TERM ] );</i><br>' );
	$predicate_term->Commit( $container[ kTAG_TERM ] );
	
	echo( '<i>$object_term = new COntologyTerm();</i><br>' );
	$object_term = new COntologyTerm();
	echo( '<i>$object_term->Code( \'OBJECT\' );</i><br>' );
	$object_term->Code( 'OBJECT' );
	echo( '<i>$object_term->Name( \'Object term\', \'en\' );</i><br>' );
	$object_term->Name( 'Object term', 'en' );
	echo( '<i>$object_term->Commit( $container[ kTAG_TERM ] );</i><br>' );
	$object_term->Commit( $container[ kTAG_TERM ] );
	echo( '<hr>' );
	echo( '<hr>' );

	//
	// Create nodes.
	//
	echo( '<h3>Create nodes</h3>' );

	echo( '<i>$subject = new COntologyNode( $container ) );</i><br>' );
	$subject = new COntologyNode( $container );
	echo( '<i>$subject->Term( $subject_term );</i><br>' );
	$subject->Term( $subject_term );
	echo( '<i>$subject[ \'COMMENT\' ] = \'This is the subject node\';</i><br>' );
	$subject[ 'COMMENT' ] = 'This is the subject node';
	echo( '<i>$subject->Commit( $container );</i><br>' );
	$subject->Commit( $container );

	echo( '<i>$object = new COntologyNode( $container ) );</i><br>' );
	$object = new COntologyNode( $container );
	echo( '<i>$object->Term( $object_term );</i><br>' );
	$object->Term( $object_term );
	echo( '<i>$object[ \'COMMENT\' ] = \'This is the object node\';</i><br>' );
	$object[ 'COMMENT' ] = 'This is the object node';
	echo( '<i>$object->Commit( $container );</i><br>' );
	$object->Commit( $container );

	//
	// Create edge.
	//
	echo( '<h3>Create edge</h3>' );

	echo( '<i>$edge = $test = $subject->relateTo( $container, $predicate_term, $object );</i><br>' );
	$edge = $test = $subject->relateTo( $container, $predicate_term, $object );
	echo( '<i>$test[ \'COMMENT\' ] = \'This is the edge node\';</i><br>' );
	$test[ 'COMMENT' ] = 'This is the edge node';
	echo( '<i>$id = $test->Commit( $container );</i><br>' );
	$id = $test->Commit( $container );
	echo( "$id<pre>" ); print_r( $test ); echo( '</pre>' );
	 
	//
	// Test content.
	//
	echo( '<h3>Content</h3>' );

	try
	{
		echo( '<i>Not existing</i><br>' );
		echo( '<i>$test = new COntologyEdgeIndex( 1, $container[ kTAG_NODE ] ) );</i><br>' );
		$test = new COntologyEdgeIndex( 9999999999, $container[ kTAG_NODE ] );
		echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );
	
	} echo( '<hr>' );

	try
	{
		echo( '<i>Invalid content</i><br>' );
		echo( '<i>$test = new COntologyEdgeIndex( \'pippo\', $container[ kTAG_NODE ] ) );</i><br>' );
		$test = new COntologyEdgeIndex( 'pippo', $container[ kTAG_NODE ] );
		echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );
	
	} echo( '<hr>' );
	 
	echo( '<i>From a node</i><br>' );
	echo( '<i>$test = new COntologyEdgeIndex( $edge->Node() ) );</i><br>' );
	$test = new COntologyEdgeIndex( $edge->Node() );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>From an ID</i><br>' );
	echo( '<i>$test = new COntologyEdgeIndex( $edge->Node()->getId(), $container[ kTAG_NODE ] ) );</i><br>' );
	$test = new COntologyEdgeIndex( $edge->Node()->getId(), $container[ kTAG_NODE ] );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>From a string</i><br>' );
	echo( '<i>$test = new COntologyEdgeIndex( (string) $edge->Node()->getId(), $container[ kTAG_NODE ] ) );</i><br>' );
	$test = new COntologyEdgeIndex( (string) $edge->Node()->getId(), $container[ kTAG_NODE ] );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );

	//
	// Test store.
	//
	echo( '<h3>Commit</h3>' );

	echo( '<i>To a MongoDB</i><br>' );
	echo( '<i>$status = $test->Commit( $db );</i><br>' );
	$status = $test->Commit( $db );
	echo( '<pre>' ); print_r( $status ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>To a MongoCollection</i><br>' );
	echo( '<i>$status = $test->Commit( $collection );</i><br>' );
	$status = $test->Commit( $collection );
	echo( '<pre>' ); print_r( $status ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>To a CMongoContainer</i><br>' );
	echo( '<i>$status = $test->Commit( $my_container );</i><br>' );
	$status = $test->Commit( $my_container );
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
	// Test retrieve.
	//
	echo( '<h3>Retrieve</h3>' );

	echo( '<i>From a node</i><br>' );
	echo( '<i>$test = new COntologyEdgeIndex( $edge->Node() ) );</i><br>' );
	$test = new COntologyEdgeIndex( $edge->Node() );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>From an identifier</i><br>' );
	echo( '<i>$test = new COntologyEdgeIndex( $edge->Node()->getId(), $client ) );</i><br>' );
	$test = new COntologyEdgeIndex( $edge->Node()->getId(), $client );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>From a string</i><br>' );
	echo( '<i>$test = new COntologyEdgeIndex( (string) $edge->Node()->getId(), $client ) );</i><br>' );
	$test = new COntologyEdgeIndex( (string) $edge->Node()->getId(), $client );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>From a query</i><br>' );
	echo( '<i>$query = new CMongoQuery();</i><br>' );
	$query = new CMongoQuery();
	echo( '<i>$query->AppendStatement( CQueryStatement::Equals( kTAG_SUBJECT.\'.\'.kTAG_NODE, $subject->Node()->getId() ), kOPERATOR_AND );</i><br>' );
	$query->AppendStatement( CQueryStatement::Equals( kTAG_SUBJECT.'.'.kTAG_NODE, $subject->Node()->getId() ), kOPERATOR_AND );
	echo( '<i>$query->AppendStatement( CQueryStatement::Equals( kTAG_PREDICATE.\'.\'.kTAG_TERM, $predicate_term->GID() ), kOPERATOR_AND );</i><br>' );
	$query->AppendStatement( CQueryStatement::Equals( kTAG_PREDICATE.'.'.kTAG_TERM, $predicate_term->GID() ), kOPERATOR_AND );
	echo( '<i>$query->AppendStatement( CQueryStatement::Equals( kTAG_OBJECT.\'.\'.kTAG_NODE, $object->Node()->getId() ), kOPERATOR_AND );</i><br>' );
	$query->AppendStatement( CQueryStatement::Equals( kTAG_OBJECT.'.'.kTAG_NODE, $object->Node()->getId() ), kOPERATOR_AND );
	echo( '<pre>' ); print_r( $query ); echo( '</pre>' );
	echo( '<i>$test = new COntologyEdgeIndex( $query, $composite ) );</i><br>' );
	$test = new COntologyEdgeIndex( $query, $composite );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>From a node path</i><br>' );
	echo( '<i>$path = COntologyEdgeIndex::EdgeNodePath( $subject, $predicate_term, $object );</i><br>' );
	$path = COntologyEdgeIndex::EdgeNodePath( $subject, $predicate_term, $object );
	echo( '<pre>' ); print_r( $path ); echo( '</pre>' );
	echo( '<i>$query = new CMongoQuery();</i><br>' );
	$query = new CMongoQuery();
	echo( '<i>$query->AppendStatement( CQueryStatement::Equals( kTAG_PATH, $path ), kOPERATOR_AND );</i><br>' );
	$query->AppendStatement( CQueryStatement::Equals( kTAG_PATH, $path ), kOPERATOR_AND );
	echo( '<pre>' ); print_r( $query ); echo( '</pre>' );
	echo( '<i>$test = new COntologyEdgeIndex( $query, $composite ) );</i><br>' );
	$test = new COntologyEdgeIndex( $query, $composite );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>From a term path</i><br>' );
	echo( '<i>$path = COntologyEdgeIndex::EdgeTermPath( $subject, $predicate_term, $object );</i><br>' );
	$path = COntologyEdgeIndex::EdgeTermPath( $subject, $predicate_term, $object );
	echo( '<pre>' ); print_r( $path ); echo( '</pre>' );
	echo( '<i>$query = new CMongoQuery();</i><br>' );
	$query = new CMongoQuery();
	echo( '<i>$query->AppendStatement( CQueryStatement::Equals( kTAG_DATA.\'.\'.kTAG_EDGE_TERM, $path ), kOPERATOR_AND );</i><br>' );
	$query->AppendStatement( CQueryStatement::Equals( kTAG_DATA.'.'.kTAG_EDGE_TERM, $path ), kOPERATOR_AND );
	echo( '<pre>' ); print_r( $query ); echo( '</pre>' );
	echo( '<i>$test = new COntologyEdgeIndex( $query, $composite ) );</i><br>' );
	$test = new COntologyEdgeIndex( $query, $composite );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
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
	echo( '<i>$status = $test->Commit( $collection, kFLAG_PERSIST_DELETE );</i><br>' );
	$status = $test->Commit( $collection, kFLAG_PERSIST_DELETE );
	echo( '<pre>' ); print_r( $status ); echo( '</pre>' );
	echo( '<hr>' );
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
