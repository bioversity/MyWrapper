<?php

/**
 * {@link COntologyTag.php Base} term test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * base term {@link COntologyTag class}.
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
require_once( kPATH_LIBRARY_SOURCE."COntologyTag.php" );


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
	$collection = new CMongoContainer( $db->selectCollection( 'COntologyTag' ) );
	
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
	echo( '<i>$path = new COntologyTag();</i><br>' );
	$path = new COntologyTag();
	echo( '<i>$path->Term( $terms );</i><br>' );
	$path->Term( $terms );
	$id = $path->Commit( $collection );
	echo( "$path<pre>" ); print_r( $path ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i><b>Create with elements</b></i><br>' );
	echo( '<i>$path = new COntologyTag();</i><br>' );
	$path = new COntologyTag();
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
	echo( '<i>$path = new COntologyTag();</i><br>' );
	$path = new COntologyTag();
	echo( '<i>$path->Term( $terms );</i><br>' );
	$path->Term( $terms );
	$id = $path->Commit( $collection );
	echo( "$path<pre>" ); print_r( $path ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Persistence.
	//
	echo( '<h3>Persistence</h3>' );
	
	echo( '<i><b>Load by ID</b></i><br>' );
	echo( '<i>$path = new COntologyTag( $collection, COntologyTag::HashIndex( 2 ) );</i><br>' );
	$path = new COntologyTag( $collection, COntologyTag::HashIndex( 2 ) );
	echo( "$path<pre>" ); print_r( $path ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i><b>Load by ID (string)</b></i><br>' );
	echo( '<i>$path = new COntologyTag( $collection, 2 );</i><br>' );
	$path = new COntologyTag( $collection, 2 );
	echo( "$path<pre>" ); print_r( $path ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i><b>Load by query</b></i><br>' );
	echo( '<i>$query = new CMongoQuery();</i><br>' );
	$query = new CMongoQuery();
	echo( '<i>$query->AppendStatement( CQueryStatement::Equals( kTAG_UID, new CDataTypeBinary( md5( $path->Path(), TRUE ) ), kTYPE_BINARY ), kOPERATOR_AND );</i><br>' );
	$query->AppendStatement( CQueryStatement::Equals( kTAG_UID, new CDataTypeBinary( md5( $path->Path(), TRUE ) ), kTYPE_BINARY ), kOPERATOR_AND );
	echo( '<i>$path = new COntologyTag( $collection, $query );</i><br>' );
	$path = new COntologyTag( $collection, $query );
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
