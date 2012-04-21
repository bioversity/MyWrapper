<?php

/**
 * {@link CInstitute.php Base} object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * base object {@link CInstitute class}.
 *
 *	@package	Test
 *	@subpackage	Entities
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 21/12/2012
 */

/*=======================================================================================
 *																						*
 *									test_CInstitute.php									*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."CInstitute.php" );


/*=======================================================================================
 *	TEST INSTITUTE PERSISTENT OBJECTS													*
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
	// Instantiate CMongoContainer.
	//
	$collection = new CMongoContainer( $db->selectCollection( 'CInstitute' ) );
	 
	//
	// Load institutes.
	//
	echo( '<h3>Load institutes</h3>' );
	
	echo( '<i><b>INSTITUTE1</b></i><br>' );
	echo( '<i>$institute1 = new CInstitute();</i><br>' );
	$institute1 = new CInstitute();
	echo( '<i>$institute1->Code( \'FAO\' );</i><br>' );
	$institute1->Code( 'FAO' );
	echo( '<i>$institute1->Name( \'Food and Agriculture Organization of the United Nations\' );</i><br>' );
	$institute1->Name( 'Food and Agriculture Organization of the United Nations' );
	echo( '<i>$institute1->Email( \'mail@fao.org\' );</i><br>' );
	$institute1->Email( 'mail@fao.org' );
	echo( '<i>$institute1->Acronym( \'FAO\', TRUE );</i><br>' );
	$institute1->Acronym( 'FAO', TRUE );
	echo( '<i>$institute1->URL( \'http://fao.org\', \'Main\' );</i><br>' );
	$institute1->URL( 'http://fao.org', 'Main' );
	echo( '<i>$id1 = $institute1->Commit( $collection, NULL, kFLAG_PERSIST_INSERT + kFLAG_STATE_ENCODED );</i><br>' );
	$id1 = $institute1->Commit( $collection, NULL, kFLAG_PERSIST_INSERT + kFLAG_STATE_ENCODED );
	echo( '$id1<pre>' ); print_r( $institute1 ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i><b>INSTITUTE2</b></i><br>' );
	echo( '<i>$institute2 = new CInstitute();</i><br>' );
	$institute2 = new CInstitute();
	echo( '<i>$institute2->Code( \'ITA412\' );</i><br>' );
	$institute2->Code( 'ITA412' );
	echo( '<i>$institute2->Name( \'Bioversity International\' );</i><br>' );
	$institute2->Name( 'Bioversity International' );
	echo( '<i>$institute2->Kind( \'FAO/WIEWS\', TRUE );</i><br>' );
	$institute2->Kind( 'FAO/WIEWS', TRUE );
	echo( '<i>$institute2->URL( \'http://bioversityinternational.cgiar.org\', \'Main\' );</i><br>' );
	$institute2->URL( 'http://bioversityinternational.cgiar.org', 'Main' );
	echo( '<i>$institute2->Acronym( \'Bioversity\', TRUE );</i><br>' );
	$institute2->Acronym( 'Bioversity' );
	echo( '<i>$id2 = $institute2->Commit( $collection, NULL, kFLAG_PERSIST_INSERT + kFLAG_STATE_ENCODED );</i><br>' );
	$id2 = $institute2->Commit( $collection, NULL, kFLAG_PERSIST_INSERT + kFLAG_STATE_ENCODED );
	echo( '$id1<pre>' ); print_r( $institute2 ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i><b>INSTITUTE3</b></i><br>' );
	echo( '<i>$institute3 = new CInstitute();</i><br>' );
	$institute3 = new CInstitute();
	echo( '<i>$institute3->Code( \'ITA303\' );</i><br>' );
	$institute3->Code( 'ITA303' );
	echo( '<i>$institute3->Name( \'International Plant Genetic Resources Information\' );</i><br>' );
	$institute3->Name( 'International Plant Genetic Resources Information' );
	echo( '<i>$institute3->Kind( \'FAO/WIEWS\', TRUE );</i><br>' );
	$institute3->Kind( 'FAO/WIEWS', TRUE );
	echo( '<i>$institute3->URL( \'http://ipgri.cgiar.org\', \'Main\' );</i><br>' );
	$institute3->URL( 'http://ipgri.cgiar.org', 'Main' );
	echo( '<i>$institute3->Acronym( \'IPGRI\', TRUE );</i><br>' );
	$institute3->Acronym( 'IPGRI' );
	echo( '<i>$institute3->Valid( $id2 );</i><br>' );
	$institute3->Valid( $id2 );
	echo( '<i>$id3 = $institute3->Commit( $collection, NULL, kFLAG_PERSIST_INSERT + kFLAG_STATE_ENCODED );</i><br>' );
	$id3 = $institute3->Commit( $collection, NULL, kFLAG_PERSIST_INSERT + kFLAG_STATE_ENCODED );
	echo( '$id1<pre>' ); print_r( $institute3 ); echo( '</pre>' );
	echo( '<hr>' );
	echo( '<hr>' );
	 
	//
	// Test relations.
	//
	echo( '<h3>Relations</h3>' );
	
	echo( '<i>$institute3->Relate( $institute1, \'Division\', TRUE );</i><br>' );
	$institute3->Relate( $institute1, 'Division', TRUE );
	echo( '<pre>' ); print_r( $institute3 ); echo( '</pre>' );
	echo( '<i>$institute3->Commit( $collection, NULL, kFLAG_PERSIST_UPDATE + kFLAG_STATE_ENCODED );</i><br>' );
	$institute3->Commit( $collection, NULL, kFLAG_PERSIST_UPDATE + kFLAG_STATE_ENCODED );
	echo( '<pre>' ); print_r( $institute3 ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$institute2->Relate( $id1, \'Partner\', TRUE );</i><br>' );
	$institute2->Relate( $id1, 'Partner', TRUE );
	echo( '<pre>' ); print_r( $institute2 ); echo( '</pre>' );
	echo( '<i>$institute2->Commit( $collection, NULL, kFLAG_PERSIST_UPDATE + kFLAG_STATE_ENCODED );</i><br>' );
	$institute2->Commit( $collection, NULL, kFLAG_PERSIST_UPDATE + kFLAG_STATE_ENCODED );
	echo( '<pre>' ); print_r( $institute3 ); echo( '</pre>' );
	echo( '<hr>' );
	echo( '<hr>' );
	 
	//
	// Test valid entity.
	//
	echo( '<h3>Test valid entity</h3>' );

	echo( '<i>$id1;</i><br>' );
	echo( '<pre>' ); print_r( $id1 ); echo( '</pre>' );
	echo( '<i>$valid = CInstitute::ValidObject( $collection, $id1, kFLAG_STATE_ENCODED );</i><br>' );
	$valid = CInstitute::ValidObject( $collection, $id1, kFLAG_STATE_ENCODED );
	echo( '<pre>' ); print_r( $valid ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>$id2;</i><br>' );
	echo( '<pre>' ); print_r( $id2 ); echo( '</pre>' );
	echo( '<i>$valid = CInstitute::ValidObject( $collection, $id2, kFLAG_STATE_ENCODED );</i><br>' );
	$valid = CInstitute::ValidObject( $collection, $id2, kFLAG_STATE_ENCODED );
	echo( '<pre>' ); print_r( $valid ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>$id2;</i><br>' );
	echo( '<pre>' ); print_r( $id3 ); echo( '</pre>' );
	echo( '<i>$valid = CInstitute::ValidObject( $collection, $id3, kFLAG_STATE_ENCODED );</i><br>' );
	$valid = CInstitute::ValidObject( $collection, $id3, kFLAG_STATE_ENCODED );
	echo( '<pre>' ); print_r( $valid ); echo( '</pre>' );
	echo( '<hr>' );
	echo( '<hr>' );
	 
	//
	// Test valid validation.
	//
	echo( '<h3>Test valid validation</h3>' );

	try
	{
		echo( '<i>$institute2->Valid( $id3 );</i><br>' );
		$institute2->Valid( $id3 );
		echo( '<i>$institute2->Commit( $collection, NULL, kFLAG_PERSIST_UPDATE + kFLAG_STATE_ENCODED );</i><br>' );
		$institute2->Commit( $collection, NULL, kFLAG_PERSIST_UPDATE + kFLAG_STATE_ENCODED );
		echo( '<i>$valid = CInstitute::ValidObject( $collection, $id2, kFLAG_STATE_ENCODED );</i><br>' );
		$valid = CInstitute::ValidObject( $collection, $id2, kFLAG_STATE_ENCODED );
		echo( '<pre>' ); print_r( $valid ); echo( '</pre>' );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );
		echo( '<br>' );
	}
	echo( '<hr>' );
	
	try
	{
		echo( '<i>$collection->Delete( $id2, kFLAG_STATE_ENCODED );</i><br>' );
		$collection->Delete( $id2, kFLAG_STATE_ENCODED );
		echo( '<i>$valid = CInstitute::ValidObject( $collection, $id3, kFLAG_STATE_ENCODED );</i><br>' );
		$valid = CInstitute::ValidObject( $collection, $id3, kFLAG_STATE_ENCODED );
		echo( '<pre>' ); print_r( $valid ); echo( '</pre>' );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );
		echo( '<br>' );
	}
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
