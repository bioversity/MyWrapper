<?php

/**
 * {@link CGraphNode.php Base} object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * base object {@link CGraphNode class}.
 *
 *	@package	Test
 *	@subpackage	Persistence
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 19/04/2012
 */

/*=======================================================================================
 *																						*
 *									test_CGraphNode.php									*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."CGraphNode.php" );


/*=======================================================================================
 *	TEST GRAPH NODES																	*
 *======================================================================================*/
 
//
// Test class.
//
try
{
	//
	// Instantiate Neo4j client.
	//
	$container = new Everyman\Neo4j\Client( 'localhost', 7474 );
	 
	//
	// Test content.
	//
	echo( '<h3>Content</h3>' );

	try
	{
		echo( '<i>Empty object</i><br>' );
		echo( '<i>$test = new CGraphNode();</i><br>' );
		$test = new CGraphNode();
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
		echo( '<i>$test = new CGraphNode( $content ) );</i><br>' );
		$test = new CGraphNode( $content );
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
		echo( '<i>$test = new CGraphNode( $content ) );</i><br>' );
		$test = new CGraphNode( $content );
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
		echo( '<i>$test = new CGraphNode( $content ) );</i><br>' );
		$test = new CGraphNode( $content );
		echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );
		echo( '<br>' );
	}
	echo( '<hr>' );

	echo( '<i>From a node</i><br>' );
	echo( '<i>$node = $container->makeNode();</i><br>' );
	$node = $container->makeNode();
	echo( '<i>$node->setProperty( \'Name\', \'Milko\' )->setProperty( \'Surname\', \'Skofic\' )->save();</i><br>' );
	$node->setProperty( 'Name', 'Milko' )->setProperty( 'Surname', 'Skofic' )->save();
	echo( '<i>$test = new CGraphNode( $container, $node ) );</i><br>' );
	$test = new CGraphNode( $container, $node );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	echo( '<hr>' );
	
	//
	// Test properties.
	//
	echo( '<h3>Properties</h3>' );
	
	echo( '<i>Load property</i><br>' );
	echo( '<i>$test[ kTAG_NAME ] = \'The name\' );</i><br>' );
	$test[ kTAG_NAME ] = 'The name';
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Add properties</i><br>' );
	echo( '<i>$test[ kTAG_DESCRIPTION ] = \'Description\' );</i><br>' );
	$test[ kTAG_DESCRIPTION ] = 'Description';
	echo( '<i>$test[ kTAG_DEFINITION ] = \'Definition\' );</i><br>' );
	$test[ kTAG_DEFINITION ] = 'Definition';
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$props = $test->getArrayCopy();</i><br>' );
	$props = $test->getArrayCopy();
	echo( 'Properties:<pre>' ); print_r( $props ); echo( '</pre>' );
	foreach( $test as $key => $value )
		echo( "[$key] $value<br>" );
	echo( '<hr>' );
	
	echo( '<i>Delete property</i><br>' );
	echo( '<i>$test->offsetUnset( kTAG_DEFINITION );</i><br>' );
	$test->offsetUnset( kTAG_DEFINITION );
	$props = $test->getArrayCopy();
	echo( 'Properties:<pre>' ); print_r( $props ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Delete property</i><br>' );
	echo( '<i>$test[ kTAG_DESCRIPTION ] = NULL;</i><br>' );
	$test[ kTAG_DESCRIPTION ] = NULL;
	$props = $test->getArrayCopy();
	echo( 'Properties:<pre>' ); print_r( $props ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Retrieve property</i><br>' );
	echo( '<i>$prop = $test[ kTAG_NAME ];</i><br>' );
	$prop = $test[ kTAG_NAME ];
	echo( 'Property:<pre>' ); print_r( $prop ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>List properties</i><br>' );
	echo( '<i>$prop = $test->getArrayCopy();</i><br>' );
	$prop = $test->getArrayCopy();
	echo( 'Property:<pre>' ); print_r( $prop ); echo( '</pre>' );
	echo( '<i><b>Casting doesn\'t work: any suggestions?</b></i><br>' );
	echo( '<i>$prop = (array) $test;</i><br>' );
	$prop = (array) $test;
	echo( 'Property:<pre>' ); print_r( $prop ); echo( '</pre>' );
	echo( '<hr>' );
	echo( '<hr>' );
	
	//
	// Persistence.
	//
	echo( '<h3>Persistence</h3>' );
	
	echo( '<i>Save node</i><br>' );
	echo( '<i>$id = $test->Commit( $container );</i><br>' );
	$id = $test->Commit( $container );
	echo( "$id:<pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Retrieve non-existing</i><br>' );
	echo( '<i>$test = new CGraphNode( $container, -1 );</i><br>' );
	$test = new CGraphNode( $container, -1 );
	echo( "<pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Retrieve node</i><br>' );
	echo( '<i>$test = new CGraphNode( $container, $id );</i><br>' );
	$test = new CGraphNode( $container, $id );
	echo( "<pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Relations.
	//
	echo( '<h3>Relations</h3>' );
	
	echo( '<i>Create relationship</i><br>' );
	echo( '<i>$edge = $test->RelateTo( $container, \'PREDICATE\', $node );</i><br>' );
	$edge = $test->RelateTo( $container, 'PREDICATE', $node );
	echo( "$id:<pre>" ); print_r( $edge ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Cleanup.
	//
	echo( '<h3>Cleanup</h3>' );
	
	echo( '<i>Delete node</i><br>' );
	echo( '<i>$ok = $test->Commit( $container, NULL, kFLAG_PERSIST_DELETE );</i><br>' );
	$ok = $test->Commit( $container, NULL, kFLAG_PERSIST_DELETE );
	echo( "$ok:<pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Delete node (should not raise an exception, but return NULL)</i><br>' );
	echo( '<i>$ok = $test->Commit( $container, NULL, kFLAG_PERSIST_DELETE );</i><br>' );
	$ok = $test->Commit( $container, NULL, kFLAG_PERSIST_DELETE );
	echo( "$ok:<pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Cleanup.
	//
	$container->deleteNode( $node );
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
