<?php

/**
 * {@link COntologyNode.php Base} object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * base object {@link COntologyNode class}.
 *
 *	@package	Test
 *	@subpackage	Persistence
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 19/04/2012
 */

/*=======================================================================================
 *																						*
 *								test_COntologyNode.php									*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."COntologyNode.php" );

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
			( $db->selectCollection( 'COntologyNode' ) );
	 
	//
	// Test content.
	//
	echo( '<h3>Content</h3>' );

	try
	{
		echo( '<i>Empty object</i><br>' );
		echo( '<i>$test = new COntologyNode();</i><br>' );
		$test = new COntologyNode();
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
		echo( '<i>$test = new COntologyNode( $content ) );</i><br>' );
		$test = new COntologyNode( $content );
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
		echo( '<i>$test = new COntologyNode( $content ) );</i><br>' );
		$test = new COntologyNode( $content );
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
		echo( '<i>$test = new COntologyNode( $content ) );</i><br>' );
		$test = new COntologyNode( $content );
		echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );
		echo( '<br>' );
	}
	echo( '<hr>' );
	echo( '<hr>' );

	echo( '<i>From a node</i><br>' );
	echo( '<i>$term = new COntologyTerm();</i><br>' );
	$term = new COntologyTerm();
	echo( '<i>$term->Code( \'TERM\' );</i><br>' );
	$term->Code( 'TERM' );
	echo( '<i>$term->Name( \'A term\', \'en\' );</i><br>' );
	$term->Name( 'A term', 'en' );
	echo( '<i>$term->Commit( $container[ kTAG_TERM ] );</i><br>' );
	$term->Commit( $container[ kTAG_TERM ] );
	echo( '<i>$node = $container[ kTAG_NODE ]->makeNode();</i><br>' );
	$node = $container[ kTAG_NODE ]->makeNode();
	echo( '<i>$node->setProperty( kTAG_TERM, $term[ kTAG_GID ] )->save();</i><br>' );
	$node->setProperty( kTAG_TERM, $term[ kTAG_GID ] )->save();
	echo( '<i>$test = new COntologyNode( $container, $node ) );</i><br>' );
	$test = new COntologyNode( $container, $node );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>Cleanup</i><br>' );
	echo( '<i>$test->Commit( $container, NULL, kFLAG_PERSIST_DELETE );</i><br>' );
	$test->Commit( $container, NULL, kFLAG_PERSIST_DELETE );
	echo( '<hr>' );
	echo( '<hr>' );
	
	//
	// Test properties.
	//
	echo( '<h3>Properties</h3>' );
	
	echo( '<i>Empty node</i><br>' );
	echo( '<i>$test = new COntologyNode( $container );</i><br>' );
	$test = new COntologyNode( $container );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Load term properties</i><br>' );
	echo( '<i>$test->Term()->Code( \'A\' );</i><br>' );
	$test->Term()->Code( 'A' );
	echo( '<i>$test->Term()->Name( \'Term 1\', kDEFAULT_LANGUAGE );</i><br>' );
	$test->Term()->Name( 'Term 1', kDEFAULT_LANGUAGE );
	echo( '<i>$test->Term()->Definition( \'Term 1 definition\', kDEFAULT_LANGUAGE );</i><br>' );
	$test->Term()->Definition( 'Term 1 definition', kDEFAULT_LANGUAGE );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Load node properties</i><br>' );
	echo( '<i>$test[ \'A1\' ] = \'Attribute 1\' );</i><br>' );
	$test[ 'A1' ] = 'Attribute 1';
	echo( '<i>$test[ \'A2\' ] = \'Attribute 2\' );</i><br>' );
	$test[ 'A2' ] = 'Attribute 2';
	echo( '<i>$test[ kTAG_NAME ] = \'This will shadow the term name\' );</i><br>' );
	$test[ kTAG_NAME ] = 'This will shadow the term name';
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
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
	echo( '<i>$test->offsetUnset( kTAG_DEFINITION );</i><br>' );
	$test->offsetUnset( kTAG_DEFINITION );
	$props = $test->getArrayCopy();
	echo( 'Properties:<pre>' ); print_r( $props ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Delete property (will work: it\'s a node property)</i><br>' );
	echo( '<i>$test->offsetUnset( \'A2\' );</i><br>' );
	$test->offsetUnset( 'A2' );
	$props = $test->getArrayCopy();
	echo( 'Properties:<pre>' ); print_r( $props ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Delete property (will unshadow the term name)</i><br>' );
	echo( '<i>$test->offsetUnset( kTAG_NAME );</i><br>' );
	$test->offsetUnset( kTAG_NAME );
	$props = $test->getArrayCopy();
	echo( 'Properties:<pre>' ); print_r( $props ); echo( '</pre>' );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
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
	
	echo( '<i>Retrieve non-existing</i><br>' );
	echo( '<i>$test = new COntologyNode( $container, -1 );</i><br>' );
	$test = new COntologyNode( $container, -1 );
	echo( "<pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Retrieve</i><br>' );
	echo( '<i>$test = new COntologyNode( $container, $id );</i><br>' );
	$test = new COntologyNode( $container, $id );
	echo( "$id:<pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Delete node</i><br>' );
	echo( '<i>$term = $test->Term();</i><br>' );
	$term = $test->Term();
	echo( '<i>$ok = $test->Commit( $container, NULL, kFLAG_PERSIST_DELETE );</i><br>' );
	$ok = $test->Commit( $container, NULL, kFLAG_PERSIST_DELETE );
	echo( "$ok:<pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Test indexes</i><br>' );
	echo( '<i>$test->Term( $term );</i><br>' );
	$test->Term( $term );
	echo( '<i>$ok = $test->Commit( $container );</i><br>' );
	$ok = $test->Commit( $container );
	echo( '<i>$index = new NodeIndex( $container[ kTAG_NODE ], kINDEX_NODE_TERM );</i><br>' );
	$index = new NodeIndex( $container[ kTAG_NODE ], kINDEX_NODE_TERM );
	echo( '<i>$node = $index->findOne( kTAG_TERM, \'A\' );</i><br>' );
	$node = $index->findOne( kTAG_TERM, 'A' );
	echo( "<pre>" ); print_r( $node ); echo( '</pre>' );
	echo( '<i>$node = $index->findOne( kTAG_NAME, \'Term 1\' );</i><br>' );
	$node = $index->findOne( kTAG_NAME, 'Term 1' );
	echo( "<pre>" ); print_r( $node ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Delete node</i><br>' );
	echo( '<i>$ok = $test->Commit( $container, NULL, kFLAG_PERSIST_DELETE );</i><br>' );
	$ok = $test->Commit( $container, NULL, kFLAG_PERSIST_DELETE );
	echo( '<hr>' );
	
	//
	// Relations.
	//
	echo( '<h3>Relations</h3>' );
	
	echo( '<i>Create nodes</i><br>' );
	echo( '<i>$test1 = new COntologyNode( $container );</i><br>' );
	$test1 = new COntologyNode( $container );
	echo( '<i>$test1->Term()->Code( \'A\' );</i><br>' );
	$test1->Term()->Code( 'A' );
	echo( '<i>$test1->Term()->Name( \'Term 1\', kDEFAULT_LANGUAGE );</i><br>' );
	$test1->Term()->Name( 'Term 1', kDEFAULT_LANGUAGE );
	echo( '<i>$id1 = $test->Commit( $container );</i><br>' );
	$id1 = $test1->Commit( $container );
	echo( '<i>$test2 = new COntologyNode( $container );</i><br>' );
	$test2 = new COntologyNode( $container );
	echo( '<i>$test2->Term()->Code( \'B\' );</i><br>' );
	$test2->Term()->Code( 'B' );
	echo( '<i>$test2->Term()->Name( \'Term 2\', kDEFAULT_LANGUAGE );</i><br>' );
	$test2->Term()->Name( 'Term 2', kDEFAULT_LANGUAGE );
	echo( '<i>$id2 = $test2->Commit( $container );</i><br>' );
	$id2 = $test2->Commit( $container );
	echo( '<i>$test3 = new COntologyNode( $container );</i><br>' );
	$test3 = new COntologyNode( $container );
	echo( '<i>$test3->Term()->Code( \'C\' );</i><br>' );
	$test3->Term()->Code( 'C' );
	echo( '<i>$test3->Term()->Name( \'Term 3\', kDEFAULT_LANGUAGE );</i><br>' );
	$test3->Term()->Name( 'Term 3', kDEFAULT_LANGUAGE );
	echo( '<i>$id3 = $test3->Commit( $container );</i><br>' );
	$id3 = $test3->Commit( $container );
	echo( '<i>$test4 = new COntologyNode( $container );</i><br>' );
	$test4 = new COntologyNode( $container );
	echo( '<i>$test4->Term()->Code( \'D\' );</i><br>' );
	$test4->Term()->Code( 'D' );
	echo( '<i>$test4->Term()->Name( \'Term 4\', kDEFAULT_LANGUAGE );</i><br>' );
	$test4->Term()->Name( 'Term 4', kDEFAULT_LANGUAGE );
	echo( '<i>$id4 = $test4->Commit( $container );</i><br>' );
	$id4 = $test4->Commit( $container );
	echo( '<hr>' );

	echo( '<i>Relate nodes</i><br>' );
	echo( '<i>$edge1 = $test1->RelateTo( $container, $test1, $test2 );</i><br>' );
	$edge1 = $test1->RelateTo( $container, $test1, $test2 );
	echo( '<i>$edge1->Commit( $container );</i><br>' );
	$edge1->Commit( $container );
	echo( '<i>$edge2 = $test1->RelateTo( $container, $test1, $test3 );</i><br>' );
	$edge2 = $test1->RelateTo( $container, $test1, $test3 );
	echo( '<i>$edge2->Commit( $container );</i><br>' );
	$edge2->Commit( $container );
	echo( '<i>$edge3 = $test4->RelateTo( $container, $test1, $test1 );</i><br>' );
	$edge3 = $test4->RelateTo( $container, $test1, $test1 );
	echo( '<i>$edge3->Commit( $container );</i><br>' );
	$edge3->Commit( $container );
	echo( '<hr>' );

	echo( '<i>Get related to nodes</i><br>' );
	echo( '<i>$list = $test1->GetRelated( $container, NULL, \'out\' );</i><br>' );
	$list = $test1->GetRelated( $container, NULL, 'out' );
	echo( "<pre>" ); print_r( $list ); echo( '</pre>' );
	echo( '<i>$list = $test2->GetRelated( $container, NULL, \'out\' );</i><br>' );
	$list = $test2->GetRelated( $container, NULL, 'out' );
	echo( "<pre>" ); print_r( $list ); echo( '</pre>' );
	echo( '<i>$list = $test3->GetRelated( $container, NULL, \'out\' );</i><br>' );
	$list = $test3->GetRelated( $container, NULL, 'out' );
	echo( "<pre>" ); print_r( $list ); echo( '</pre>' );
	echo( '<i>$list = $test4->GetRelated( $container, NULL, \'out\' );</i><br>' );
	$list = $test4->GetRelated( $container, NULL, 'out' );
	echo( "<pre>" ); print_r( $list ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>Get related from nodes</i><br>' );
	echo( '<i>$list = $test1->GetRelated( $container, NULL, \'in\' );</i><br>' );
	$list = $test1->GetRelated( $container, NULL, 'in' );
	echo( "<pre>" ); print_r( $list ); echo( '</pre>' );
	echo( '<i>$list = $test2->GetRelated( $container, NULL, \'in\' );</i><br>' );
	$list = $test2->GetRelated( $container, NULL, 'in' );
	echo( "<pre>" ); print_r( $list ); echo( '</pre>' );
	echo( '<i>$list = $test3->GetRelated( $container, NULL, \'in\' );</i><br>' );
	$list = $test3->GetRelated( $container, NULL, 'in' );
	echo( "<pre>" ); print_r( $list ); echo( '</pre>' );
	echo( '<i>$list = $test4->GetRelated( $container, NULL, \'in\' );</i><br>' );
	$list = $test4->GetRelated( $container, NULL, 'in' );
	echo( "<pre>" ); print_r( $list ); echo( '</pre>' );

	echo( '<i>Get related to and from nodes</i><br>' );
	echo( '<i>$list = $test1->GetRelated( $container );</i><br>' );
	$list = $test1->GetRelated( $container );
	echo( "<pre>" ); print_r( $list ); echo( '</pre>' );
	echo( '<i>$list = $test2->GetRelated( $container );</i><br>' );
	$list = $test2->GetRelated( $container );
	echo( "<pre>" ); print_r( $list ); echo( '</pre>' );
	echo( '<i>$list = $test3->GetRelated( $container );</i><br>' );
	$list = $test3->GetRelated( $container );
	echo( "<pre>" ); print_r( $list ); echo( '</pre>' );
	echo( '<i>$list = $test4->GetRelated( $container );</i><br>' );
	$list = $test4->GetRelated( $container );
	echo( "<pre>" ); print_r( $list ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Cleanup.
	//
	$ok = $edge1->Commit( $container, NULL, kFLAG_PERSIST_DELETE );
	$ok = $edge2->Commit( $container, NULL, kFLAG_PERSIST_DELETE );
	$ok = $edge3->Commit( $container, NULL, kFLAG_PERSIST_DELETE );

	$ok = $test1->Commit( $container, NULL, kFLAG_PERSIST_DELETE );
	$ok = $test2->Commit( $container, NULL, kFLAG_PERSIST_DELETE );
	$ok = $test3->Commit( $container, NULL, kFLAG_PERSIST_DELETE );
	$ok = $test4->Commit( $container, NULL, kFLAG_PERSIST_DELETE );
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
