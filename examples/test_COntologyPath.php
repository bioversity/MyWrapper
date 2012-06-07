<?php

/**
 * {@link COntologyPath.php Base} term test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * base term {@link COntologyPath class}.
 *
 *	@package	Test
 *	@subpackage	Entities
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 07/06/2012
 */

/*=======================================================================================
 *																						*
 *								test_COntologyPath.php									*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."COntologyPath.php" );


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
	// Instantiate path container.
	//
	$collection = new CMongoContainer( $db->selectCollection( 'COntologyPath' ) );
	
	//
	// Instantiate path container.
	//
	$db2 = $mongo->selectDB( "WAREHOUSE" );
	$term_cont = new CMongoContainer( $db2->selectCollection( "TERMS" ) );
	 
	//
	// Load terms.
	//
	echo( '<h3>Load terms</h3>' );
	
	echo( '<i>$term_trait = new COntologyTerm( $term_cont, COntologyTerm::HashIndex( \'MCPD:ORIGCTY\' ) );</i><br>' );
	$term_trait = new COntologyTerm( $term_cont, COntologyTerm::HashIndex( 'MCPD:ORIGCTY' ) );
	echo( '<i>$method_trait_1 = \'ISO:3166\';</i><br>' );
	$method_trait_1 = 'ISO:3166';
	echo( '<i>$method_trait_2 = \'ISO:3166:1\';</i><br>' );
	$method_trait_2 = 'ISO:3166:1';
	echo( '<i>$term_scale = new COntologyTerm( $term_cont, COntologyTerm::HashIndex( \'ISO:3166:1:ALPHA-3\' ) );</i><br>' );
	$term_scale = new COntologyTerm( $term_cont, COntologyTerm::HashIndex( 'ISO:3166:1:ALPHA-3' ) );
	echo( '<i>$terms = array( $term_trait, kPRED_METHOD_OF, $method_trait_1, kPRED_METHOD_OF, $method_trait_2, kPRED_SCALE_OF, $term_scale );</i><br>' );
	$terms = array( $term_trait, kPRED_METHOD_OF, $method_trait_1, kPRED_METHOD_OF, $method_trait_2, kPRED_SCALE_OF, $term_scale );
	echo( '<hr>' );
	
	//
	// Create  paths.
	//
	echo( '<h3>Create terms</h3>' );
	
	echo( '<i><b>Create with list</b></i><br>' );
	echo( '<i>$path = new COntologyPath();</i><br>' );
	$path = new COntologyPath();
	echo( '<i>$path->Term( $terms );</i><br>' );
	$path->Term( $terms );
	$id = $path->Commit( $collection );
	echo( "$path<pre>" ); print_r( $path ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i><b>Create with elements</b></i><br>' );
	echo( '<i>$path = new COntologyPath();</i><br>' );
	$path = new COntologyPath();
	echo( '<i>$path->Term( $term_trait );</i><br>' );
	$path->Term( $term_trait );
	echo( '<i>$path->Term( kPRED_SCALE_OF );</i><br>' );
	$path->Term( kPRED_SCALE_OF );
	echo( '<i>$path->Term( $term_scale );</i><br>' );
	$path->Term( $term_scale );
	$id = $path->Commit( $collection );
	echo( "$path<pre>" ); print_r( $path ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i><b>Try creating same</b></i><br>' );
	echo( '<i>$path = new COntologyPath();</i><br>' );
	$path = new COntologyPath();
	echo( '<i>$path->Term( $terms );</i><br>' );
	$path->Term( $terms );
	$id = $path->Commit( $collection );
	echo( "$path<pre>" ); print_r( $path ); echo( '</pre>' );
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
