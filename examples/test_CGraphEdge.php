<?php

/**
 * {@link CGraphEdge.php Base} object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * base object {@link CGraphEdge class}.
 *
 *	@package	Test
 *	@subpackage	Persistence
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 19/04/2012
 */

/*=======================================================================================
 *																						*
 *									test_CGraphEdge.php									*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."CGraphEdge.php" );


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
		echo( '<i>$test = new CGraphEdge();</i><br>' );
		$test = new CGraphEdge();
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
		echo( '<i>$test = new CGraphEdge( $content ) );</i><br>' );
		$test = new CGraphEdge( $content );
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
		echo( '<i>$test = new CGraphEdge( $content ) );</i><br>' );
		$test = new CGraphEdge( $content );
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
		echo( '<i>$test = new CGraphEdge( $content ) );</i><br>' );
		$test = new CGraphEdge( $content );
		echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );
		echo( '<br>' );
	}
	echo( '<hr>' );

	echo( '<i>From a node</i><br>' );
	echo( '<i>$subject = $container->makeNode();</i><br>' );
	$subject = $container->makeNode();
	echo( '<i>$subject->setProperty( \'Name\', \'Subject\' )->save();</i><br>' );
	$subject->setProperty( 'Name', 'Subject' )->save();
	echo( '<i>$object = $container->makeNode();</i><br>' );
	$object = $container->makeNode();
	echo( '<i>$object->setProperty( \'Name\', \'Object\' )->save();</i><br>' );
	$object->setProperty( 'Name', 'Object' )->save();
	echo( '<i>$edge = $subject->relateTo( $object, \'PREDICATE\' );</i><br>' );
	$edge = $subject->relateTo( $object, 'PREDICATE' );
	echo( '<i>$test = new CGraphEdge( $container, $edge ) );</i><br>' );
	$test = new CGraphNode( $container, $edge );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	echo( '<hr>' );
	
	//
	// Cleanup.
	//
	$container->deleteNode( $subject );
	$container->deleteNode( $object );
	
	//
	// Test properties.
	//
	echo( '<h3>Properties</h3>' );
	
	echo( '<i>Empty node</i><br>' );
	echo( '<i>$test = new CGraphEdge( $container );</i><br>' );
	$test = new CGraphEdge( $container );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Load property</i><br>' );
	echo( '<i>$test[ kTAG_NAME ] = \'Is-a\' );</i><br>' );
	$test[ kTAG_NAME ] = 'Is-a';
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
	// Test type.
	//
	echo( '<h3>Type</h3>' );
	
	echo( '<i>Set type</i><br>' );
	echo( '<i>$test->Type( \'IS_A\' );</i><br>' );
	$test->Type( 'IS_A' );
	echo( '<i>$type = $test->Type();</i><br>' );
	echo( $test->Type().'<br>' );
	echo( '<hr>' );
	
	//
	// Set subject and object.
	//
	echo( '<h3>Set subject and object</h3>' );
	
	echo( '<i>$subject = new CGraphNode( $container );</i><br>' );
	$subject = new CGraphNode( $container );
	echo( '<i>$subject[ kTAG_NAME ] = \'Subject\' );</i><br>' );
	$subject[ kTAG_NAME ] = 'Subject';
	echo( '<i>$id = $subject->Commit( $container );</i><br>' );
	$id = $subject->Commit( $container );
	echo( '<i>$test->Subject( $subject );</i><br>' );
	$test->Subject( $subject );
	echo( '<i>$object = new CGraphNode( $container );</i><br>' );
	$object = new CGraphNode( $container );
	echo( '<i>$object[ kTAG_NAME ] = \'Object\' );</i><br>' );
	$object[ kTAG_NAME ] = 'Object';
	echo( '<i>$id = $object->Commit( $container );</i><br>' );
	$id = $object->Commit( $container );
	echo( '<i>$test->Object( $object );</i><br>' );
	$test->Object( $object );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
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
	
	echo( '<i>Retrieve node</i><br>' );
	echo( '<i>$test = new CGraphEdge( $container, $id );</i><br>' );
	$test = new CGraphEdge( $container, $id );
	echo( "$id:<pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Delete node</i><br>' );
	echo( '<i>$ok = $test->Commit( $container, NULL, kFLAG_PERSIST_DELETE );</i><br>' );
	$ok = $test->Commit( $container, NULL, kFLAG_PERSIST_DELETE );
	echo( "$ok:<pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	try
	{
		echo( '<i>Delete node</i><br>' );
		echo( '<i>$ok = $test->Commit( $container, NULL, kFLAG_PERSIST_DELETE );</i><br>' );
		$ok = $test->Commit( $container, NULL, kFLAG_PERSIST_DELETE );
		echo( "$ok:<pre>" ); print_r( $test ); echo( '</pre>' );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );
		echo( '<br>' );
	}
	echo( '<hr>' );
	
	//
	// Cleanup.
	//
	echo( '<i>$subject->Commit( $container, NULL, kFLAG_PERSIST_DELETE );</i><br>' );
	$subject->Commit( $container, NULL, kFLAG_PERSIST_DELETE );
	echo( '<i>$object->Commit( $container, NULL, kFLAG_PERSIST_DELETE );</i><br>' );
	$object->Commit( $container, NULL, kFLAG_PERSIST_DELETE );
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
