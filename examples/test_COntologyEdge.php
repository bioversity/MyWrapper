<?php

/**
 * {@link COntologyEdge.php Base} object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * base object {@link COntologyEdge class}.
 *
 *	@package	Test
 *	@subpackage	Persistence
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 19/04/2012
 */

/*=======================================================================================
 *																						*
 *								test_COntologyEdge.php									*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."COntologyEdge.php" );

use Everyman\Neo4j\Transport,
	Everyman\Neo4j\Client,
	Everyman\Neo4j\Index\NodeIndex,
	Everyman\Neo4j\Index\RelationshipIndex,
	Everyman\Neo4j\Index\NodeFulltextIndex,
	Everyman\Neo4j\Node,
	Everyman\Neo4j\Batch;


/*=======================================================================================
 *	TEST ONTOLOGY NODES																	*
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
	$container[ kTAG_NODE ] = new Everyman\Neo4j\Client( 'localhost', 7474 );

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
	// Test content.
	//
	echo( '<h3>Content</h3>' );

	try
	{
		echo( '<i>Empty object</i><br>' );
		echo( '<i>$test = new COntologyEdge();</i><br>' );
		$test = new COntologyEdge();
		echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );
		echo( '<br>' );
	}
	echo( '<hr>' );
	
	try
	{
		echo( '<i>From array</i><br>' );
		echo( '<i>$content = array( \'Name\' => \'Milko\' );</i><br>' );
		$content = array( 'Name' => 'Milko' );
		echo( '<i>$test = new COntologyEdge( $content ) );</i><br>' );
		$test = new COntologyEdge( $content );
		echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );
		echo( '<br>' );
	}
	echo( '<hr>' );
	
	try
	{
		echo( '<i>From ArrayObject</i><br>' );
		echo( '<i>$content = new ArrayObject( array( \'Name\' => \'Milko\' ) );</i><br>' );
		$content = new ArrayObject( array( 'Name' => 'Milko' ) );
		echo( '<i>$test = new COntologyEdge( $content ) );</i><br>' );
		$test = new COntologyEdge( $content );
		echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );
		echo( '<br>' );
	}
	echo( '<hr>' );

	try
	{
		echo( '<i>From any other type</i><br>' );
		echo( '<i>$content = 10;</i><br>' );
		$content = 10;
		echo( '<i>$test = new COntologyEdge( $content ) );</i><br>' );
		$test = new COntologyEdge( $content );
		echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );
		echo( '<br>' );
	}
	echo( '<hr>' );
	echo( '<hr>' );
	
	//
	// Test properties.
	//
	echo( '<h3>Properties</h3>' );
	
	echo( '<i>Empty node</i><br>' );
	echo( '<i>$test = new COntologyEdge( $container );</i><br>' );
	$test = new COntologyEdge( $container );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Load predicate term properties</i><br>' );
	echo( '<i>$test->Term()->Code( \'IS-A\' );</i><br>' );
	$test->Term()->Code( 'IS-A' );
	echo( '<i>$test->Term()->Name( \'Is-a\', kDEFAULT_LANGUAGE );</i><br>' );
	$test->Term()->Name( 'Is-a', kDEFAULT_LANGUAGE );
	echo( '<i>$test->Term()->Definition( \'Subclass predicate\', kDEFAULT_LANGUAGE );</i><br>' );
	$test->Term()->Definition( 'Subclass predicate', kDEFAULT_LANGUAGE );
	echo( '$test->Term():<pre>' ); print_r( $test->Term() ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Load subject term properties</i><br>' );
	echo( '<i>$test->SubjectTerm()->Code( \'SUBJECT\' );</i><br>' );
	$test->SubjectTerm()->Code( 'SUBJECT' );
	echo( '<i>$test->SubjectTerm()->Name( \'Subject\', kDEFAULT_LANGUAGE );</i><br>' );
	$test->SubjectTerm()->Name( 'Subject', kDEFAULT_LANGUAGE );
	echo( '<i>$test->SubjectTerm()->Definition( \'Subject term\', kDEFAULT_LANGUAGE );</i><br>' );
	$test->SubjectTerm()->Definition( 'Subject term', kDEFAULT_LANGUAGE );
	echo( '$test->SubjectTerm():<pre>' ); print_r( $test->SubjectTerm() ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Load object term properties</i><br>' );
	echo( '<i>$test->ObjectTerm()->Code( \'OBJECT\' );</i><br>' );
	$test->ObjectTerm()->Code( 'OBJECT' );
	echo( '<i>$test->ObjectTerm()->Name( \'Object\', kDEFAULT_LANGUAGE );</i><br>' );
	$test->ObjectTerm()->Name( 'Object', kDEFAULT_LANGUAGE );
	echo( '<i>$test->ObjectTerm()->Definition( \'Object term\', kDEFAULT_LANGUAGE );</i><br>' );
	$test->ObjectTerm()->Definition( 'Object term', kDEFAULT_LANGUAGE );
	echo( '$test->ObjectTerm():<pre>' ); print_r( $test->ObjectTerm() ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Load predicate node properties</i><br>' );
	echo( '<i>$test[ \'Name\' ] = \'Predicate node\' );</i><br>' );
	$test[ 'Name' ] = 'Predicate node';
	echo( '<i>$test[ \'Description\' ] = \'This is a predicate node\' );</i><br>' );
	$test[ 'Description' ] = 'This is a predicate node';
	echo( '$test->Node()->getProperties():<pre>' ); print_r( $test->Node()->getProperties() ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Load subject node properties</i><br>' );
	echo( '<i>$test->Subject()->setProperty( \'Name\', \'Subject node\' );</i><br>' );
	$test->Subject()->setProperty( 'Name', 'Subject node' );
	echo( '$test->Subject()->getProperties():<pre>' ); print_r( $test->Subject()->getProperties() ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Load object node properties</i><br>' );
	echo( '<i>$test->Object()->setProperty( \'Name\', \'Object node\' );</i><br>' );
	$test->Object()->setProperty( 'Name', 'Object node' );
	echo( '$test->Object()->getProperties():<pre>' ); print_r( $test->Object()->getProperties() ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>List properties</i><br>' );
	foreach( $test as $key => $value )
		echo( "[$key] $value<br>" );
	echo( '<i>$prop = $test->getArrayCopy();</i><br>' );
	$prop = $test->getArrayCopy();
	echo( 'Property:<pre>' ); print_r( $prop ); echo( '</pre>' );
	echo( '<i><b>Casting doesn\'t work: any suggestions?</b></i><br>' );
	echo( '<i>$prop = (array) $test;</i><br>' );
	$prop = (array) $test;
	echo( 'Property:<pre>' ); print_r( $prop ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Delete property (will not work: it\'s a term)</i><br>' );
	echo( '<i>$test->offsetUnset( kTAG_NAME );</i><br>' );
	$test->offsetUnset( kTAG_NAME );
	$props = $test->getArrayCopy();
	echo( 'Properties:<pre>' ); print_r( $props ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Delete property (will work: it\'s a node property)</i><br>' );
	echo( '<i>$test->offsetUnset( \'Description\' );</i><br>' );
	$test->offsetUnset( 'Description' );
	$props = $test->getArrayCopy();
	echo( 'Properties:<pre>' ); print_r( $props ); echo( '</pre>' );
	echo( '<hr>' );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Persistence.
	//
	echo( '<h3>Persistence</h3>' );
	
	echo( '<i>Save</i><br>' );
	echo( '<i>$id = $test->Commit( $container );</i><br>' );
	$id = $test->Commit( $container );
	echo( "$id:<pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Retrieve</i><br>' );
	echo( '<i>$test = new COntologyEdge( $container, $id );</i><br>' );
	$test = new COntologyEdge( $container, $id );
	echo( "$id:<pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Save references</i><br>' );
	echo( '<i>$predicate_term = $test->Term();</i><br>' );
	$predicate_term = $test->Term();
	echo( '<i>$subject_term = $test->SubjectTerm();</i><br>' );
	$subject_term = $test->SubjectTerm();
	echo( '<i>$subject_node = $test->Subject();</i><br>' );
	$subject_node = $test->Subject();
	echo( '<i>$object_term = $test->ObjectTerm();</i><br>' );
	$object_term = $test->ObjectTerm();
	echo( '<i>$object_node = $test->Object();</i><br>' );
	$object_node = $test->Object();
	echo( '<hr>' );
	
	//
	// Members.
	//
	echo( '<h3>Members</h3>' );
	
	echo( '<i>Subject node</i><br>' );
	echo( '<i>$subject_onto_node = $test->SubjectNode( $container );</i><br>' );
	$subject_onto_node = $test->SubjectNode( $container );
	echo( "<pre>" ); print_r( $subject_onto_node ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Object node</i><br>' );
	echo( '<i>$object_onto_node = $test->ObjectNode( $container );</i><br>' );
	$object_onto_node = $test->ObjectNode( $container );
	echo( "<pre>" ); print_r( $object_onto_node ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Relations.
	//
	echo( '<h3>Relations</h3>' );
	
	echo( '<i>Create relationship elements</i><br>' );
	echo( '<i>$predicate_term = $test->Term();</i><br>' );
	$predicate_term = $test->Term();
	echo( '<i>$predicate_gid = $predicate_term[ kTAG_GID ];</i><br>' );
	$predicate_gid = $predicate_term[ kTAG_GID ];

	echo( '<i>$subject_onto = $test->SubjectNode( $container );</i><br>' );
	$subject_onto = $test->SubjectNode( $container );

	echo( '<i>$object_onto = $test->ObjectNode( $container );</i><br>' );
	$object_onto = $test->ObjectNode( $container );
	echo( '<i>$object_node = $test->Object();</i><br>' );
	$object_node = $test->Object();
	echo( '<i>$object_id = $object_node->getId();</i><br>' );
	$object_id = $object_node->getId();
	echo( '<hr>' );
	
	echo( '<i>Delete edge</i><br>' );
	echo( '<i>$ok = $test->Commit( $container, NULL, kFLAG_PERSIST_DELETE );</i><br>' );
	$ok = $test->Commit( $container, NULL, kFLAG_PERSIST_DELETE );
	echo( "$ok:<pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>$test = $subject_onto->RelateTo( $container, $predicate_term, $object_onto );</i><br>' );
	$test = $subject_onto->RelateTo( $container, $predicate_term, $object_onto );
	echo( "<pre>" ); print_r( $test ); echo( '</pre>' );

	echo( '<i>$test = $subject_onto->RelateTo( $container, $predicate_gid, $object_node );</i><br>' );
	$test = $subject_onto->RelateTo( $container, $predicate_gid, $object_node );
	echo( "<pre>" ); print_r( $test ); echo( '</pre>' );

	echo( '<i>$test = $subject_onto->RelateTo( $container, $predicate_term, $object_id );</i><br>' );
	$test = $subject_onto->RelateTo( $container, $predicate_gid, $object_id );
	echo( "<pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>Commit relation</i><br>' );
	echo( '<i>$id = $test->Commit( $container );</i><br>' );
	$id = $test->Commit( $container );
	echo( "$id<pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>Try relating the same nodes</i><br>' );
	echo( '<i>$rel = $subject_onto->RelateTo( $container, $predicate_term, $object_node );</i><br>' );
	$rel = $subject_onto->RelateTo( $container, $predicate_term, $object_node );
	echo( '<i>$id = $rel->Commit( $container );</i><br>' );
	$id = $rel->Commit( $container );
	echo( "$id<pre>" ); print_r( $rel ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Indexes.
	//
	echo( '<h3>Indexes</h3>' );
	
	echo( '<i>Test indexes</i><br>' );
	echo( '<i>$index = new RelationshipIndex( $container[ kTAG_NODE ], kINDEX_NODE_TERM );</i><br>' );
	$index = new RelationshipIndex( $container[ kTAG_NODE ], kINDEX_NODE_TERM );
	echo( '<hr>' );
	echo( '<i>$found = $index->findOne( kTAG_TERM, $test[ kTAG_GID ] );</i><br>' );
	$found = $index->findOne( kTAG_TERM, $test[ kTAG_GID ] );
	$id = $test[ kTAG_GID ];
	echo( "$id<pre>" ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	echo( '<i>$found = $index->findOne( kTAG_NAME, \'Is-a\' );</i><br>' );
	$found = $index->findOne( kTAG_NAME, 'Is-a' );
	echo( "<pre>" ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	echo( '<i>$found = $index->findOne( kTAG_EDGE_TERM, \'SUBJECT/IS-A/OBJECT\' );</i><br>' );
	$found = $index->findOne( kTAG_EDGE_TERM, 'SUBJECT/IS-A/OBJECT' );
	echo( "<pre>" ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	echo( '<i>$id = $test->Subject()->getId().\'/\'.$test[ kTAG_GID ].\'/\'.$test->Object()->getId();</i><br>' );
	$id = $test->Subject()->getId().'/'.$test[ kTAG_GID ].'/'.$test->Object()->getId();
	echo( '<i>$found = $index->findOne( kTAG_EDGE_NODE, $id );</i><br>' );
	$found = $index->findOne( kTAG_EDGE_NODE, $id );
	echo( "$id<pre>" ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Cleanup.
	//
	echo( '<h3>Cleanup</h3>' );
	
	echo( '<i>Cleanup</i><br>' );
	echo( '<i>$subject = $test->Subject();</i>' );
	$subject = $test->Subject();
	echo( '<i>$object = $test->Object();</i>' );
	$object = $test->Object();
	echo( '<i>$ok = $test->Commit( $container, NULL, kFLAG_PERSIST_DELETE );</i>' );
	$ok = $test->Commit( $container, NULL, kFLAG_PERSIST_DELETE );
	echo( " ==> $ok<br>" );
	echo( '<i>$container[ kTAG_NODE ]->deleteNode( $subject );</i><br>' );
	$container[ kTAG_NODE ]->deleteNode( $subject );
	echo( '<i>$container[ kTAG_NODE ]->deleteNode( $object );</i><br>' );
	$container[ kTAG_NODE ]->deleteNode( $object );
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
