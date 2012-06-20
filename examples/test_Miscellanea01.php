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
 *	@version	1.00 30/05/2012
 */

/*=======================================================================================
 *																						*
 *								test_Miscellanea01.php									*
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
	$db = $mongo->selectDB( "WAREHOUSE" );
	
	//
	// Instantiate CMongoContainer.
	//
	$container[ kTAG_TERM ]
		= new CMongoContainer
			( $db->selectCollection( 'TERMS' ) );
	 
	//
	// Get nodes.
	//
	echo( '<h3>Get nodes</h3>' );
	
	echo( '<i>Start</i><br>' );
	echo( '<i>$start = new COntologyNode( $container, 90 );</i><br>' );
	$start = new COntologyNode( $container, 90 );
	echo( "<pre>" ); print_r( $start ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>End</i><br>' );
	echo( '<i>$end = new COntologyNode( $container, 9687 );</i><br>' );
	$end = new COntologyNode( $container, 9687 );
	echo( "<pre>" ); print_r( $end ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>All paths</i><br>' );
	echo( '<i>$path = $start->Node()->findPathsTo( $end->Node(), kPRED_IS_A, \'out\' );</i><br>' );
	$path = $start->Node()->findPathsTo( $end->Node(), kPRED_IS_A, 'out' );
	echo( '<i>$path->setMaxDepth( 10 );</i><br>' );
	$path->setMaxDepth( 10 );
	echo( '<i>$found = $path->getPaths();</i><br>' );
	$found = $path->getPaths();
	echo( "<pre>" ); print_r( $found ); echo( '</pre>' );
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
