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
	// Create nodes.
	//
	echo( '<h3>Create nodes</h3>' );

	echo( '<i>$subject = new COntologyNode( $container );</i><br>' );
	$subject = new COntologyNode( $container );
	echo( '<i>$subject->Term( $subject_term );</i><br>' );
	$subject->Term( $subject_term );
	echo( '<i>$id = $subject->Commit( $container );</i><br>' );
	$id = $subject->Commit( $container );
	echo( '<pre>' ); print_r( $id ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>$object = new COntologyNode( $container );</i><br>' );
	$object = new COntologyNode( $container );
	echo( '<i>$object->Term( $object_term );</i><br>' );
	$object->Term( $object_term );
	echo( '<i>$id = $object->Commit( $container );</i><br>' );
	$id = $object->Commit( $container );
	echo( '<pre>' ); print_r( $id ); echo( '</pre>' );
	echo( '<hr>' );
	echo( '<hr>' );

	//
	// Create relationship.
	//
	echo( '<h3>Create relationship</h3>' );

	echo( '<i><b>Let node create a relationship</b></i><br>' );
	echo( '<i><b>Note that you must create the edge from this class, or you will not get the indexes...</b></i><br>' );
	echo( '<i>$test = $subject->RelateTo( $container, $predicate_term, $object );</i><br>' );
	$test = $subject->RelateTo( $container, $predicate_term, $object );
	echo( '<i>$test[ \'Property\' ] = \'This is a property\';</i><br>' );
	$test[ 'Property' ] = 'This is a property';
	echo( '<i>$ok = $test->Commit( $container );</i><br>' );
	$ok = $test->Commit( $container );
	echo( "$ok<pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i><b>Create a relationship from an edge node</b></i><br>' );
	echo( '<i>$test = new COntologyEdge( $container, $test->Node() );</i><br>' );
	$test = new COntologyEdge( $container, $test->Node() );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i><b>Create a relationship from an edge node identifier</b></i><br>' );
	echo( '<i>$test = new COntologyEdge( $container, $test->Node()->getId() );</i><br>' );
	$test = new COntologyEdge( $container, $test->Node()->getId() );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	echo( '<hr>' );
	
	//
	// Test properties.
	//
	echo( '<h3>Properties</h3>' );
	
	echo( '<i>List properties</i><br>' );
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
	echo( '<i>$test->offsetUnset( \'Property\' );</i><br>' );
	$test->offsetUnset( 'Property' );
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
	
	echo( '<i><b>Delete edge</b></i><br>' );
	echo( '<i>The data elements should be empty.</i><br>' );
	echo( '<i>$ok = $test->Commit( $container, NULL, kFLAG_PERSIST_DELETE );</i><br>' );
	$ok = $test->Commit( $container, NULL, kFLAG_PERSIST_DELETE );
	echo( "$ok:<pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i><b>Create relationship with object ontology node</b></i><br>' );
	echo( '<i>$test = $subject_onto->RelateTo( $container, $predicate_term, $object_onto );</i><br>' );
	$test = $subject_onto->RelateTo( $container, $predicate_term, $object_onto );
	echo( '<i>Object type</i>: '.get_class( $object_onto ).'<br>' );
	echo( '<i>Relationship ID</i>: ['.$test->Node()->getId().']<br>' );
	echo( "<pre>" ); print_r( $test ); echo( '</pre>' );

	echo( '<i><b>Create relationship with object node</b></i><br>' );
	echo( '<i>$test = $subject_onto->RelateTo( $container, $predicate_gid, $object_node );</i><br>' );
	$test = $subject_onto->RelateTo( $container, $predicate_gid, $object_node );
	echo( '<i>Object type</i>: '.get_class( $object_node ).'<br>' );
	echo( '<i>Relationship ID</i>: ['.$test->Node()->getId().']<br>' );
	echo( "<pre>" ); print_r( $test ); echo( '</pre>' );

	echo( '<i><b>Create relationship with object node ID</b></i><br>' );
	echo( '<i>$test = $subject_onto->RelateTo( $container, $predicate_term, $object_id );</i><br>' );
	$test = $subject_onto->RelateTo( $container, $predicate_gid, $object_id );
	echo( '<i>Object type</i>: '.gettype( $object_id ).'<br>' );
	echo( '<i>Relationship ID</i>: ['.$test->Node()->getId().']<br>' );
	echo( "<pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>Commit relation</i><br>' );
	echo( '<i>$id = $test->Commit( $container );</i><br>' );
	$id = $test->Commit( $container );
	echo( '<i>Relationship ID</i>: ['.$test->Node()->getId().']<br>' );
	echo( "<pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>Try relating the same nodes</i><br>' );
	echo( '<i>$test = $subject_onto->RelateTo( $container, $predicate_term, $object_id );</i><br>' );
	$test = $subject_onto->RelateTo( $container, $predicate_gid, $object_id );
	echo( '<i>Object type</i>: '.gettype( $object_id ).'<br>' );
	echo( '<i>Relationship ID</i>: ['.$test->Node()->getId().']<br>' );
	echo( "<pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>Commit the same relationship</i><br>' );
	echo( '<i>$id = $test->Commit( $container );</i><br>' );
	$id = $test->Commit( $container );
	echo( '<i>Relationship ID</i>: ['.$test->Node()->getId().']<br>' );
	echo( "<pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
/*	
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
*/	
	//
	// Cleanup.
	//
	echo( '<h3>Cleanup</h3>' );
	echo( '<i><b>Note that we first need to delete edges before being able to delete nodes.</b></i><br>' );
	
	echo( '<i>Save subject</i><br>' );
	echo( '<i>$subject = new COntologyNode( $container, $test->Subject() );</i><br>' );
	$subject = new COntologyNode( $container, $test->Subject() );
	
	echo( '<i>Save object</i><br>' );
	echo( '<i>$object = new COntologyNode( $container, $test->Object() );</i><br>' );
	$object = new COntologyNode( $container, $test->Object() );

	echo( '<hr>' );
	
	echo( '<i>Delete edge</i><br>' );
	echo( '<i>$ok = $test->Commit( $container, NULL, kFLAG_PERSIST_DELETE );</i><br>' );
	$ok = $test->Commit( $container, NULL, kFLAG_PERSIST_DELETE );
	echo( " ==> [$ok]<br>" );
	echo( '<hr>' );
	
	echo( '<i>Delete subject</i><br>' );
	echo( '<i>$ok = $subject->Commit( $container, NULL, kFLAG_PERSIST_DELETE );</i><br>' );
	$ok = $subject->Commit( $container, NULL, kFLAG_PERSIST_DELETE );
	echo( " ==> [$ok]<br>" );
	echo( '<hr>' );
	
	echo( '<i>Delete object</i><br>' );
	echo( '<i>$ok = $object->Commit( $container, NULL, kFLAG_PERSIST_DELETE );</i><br>' );
	$ok = $object->Commit( $container, NULL, kFLAG_PERSIST_DELETE );
	echo( " ==> [$ok]<br>" );
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
