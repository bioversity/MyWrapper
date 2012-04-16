<?php

/**
 * {@link COntologyTerm.php Base} term test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * base term {@link COntologyTerm class}.
 *
 *	@package	Test
 *	@subpackage	Entities
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 12/04/2012
 */

/*=======================================================================================
 *																						*
 *								test_COntologyTerm.php									*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."CNamespace.php" );
require_once( kPATH_LIBRARY_SOURCE."COntology.php" );
require_once( kPATH_LIBRARY_SOURCE."CPredicate.php" );
require_once( kPATH_LIBRARY_SOURCE."COntologyTerm.php" );


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
	// Instantiate CMongoContainer.
	//
	$collection = new CMongoContainer( $db->selectCollection( 'COntologyTerm' ) );
	 
	//
	// Load terms.
	//
	echo( '<h3>Load terms</h3>' );
	
	echo( '<i><b>NAMESPACE</b></i><br>' );
	echo( '<i>$namespace = new CNamespace();</i><br>' );
	$namespace = new CNamespace();
	echo( '<i>$namespace->Code( \'NS\' );</i><br>' );
	$namespace->Code( 'NS' );
	echo( '<i>$idn = $namespace->Commit( $collection );</i><br>' );
	$idn = $namespace->Commit( $collection );
	echo( "$namespace<pre>" ); print_r( $namespace ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i><b>PREDICATE</b></i><br>' );
	echo( '<i>$predicate = new CPredicate();</i><br>' );
	$predicate = new CPredicate();
	echo( '<i>$predicate->NS( $namespace );</i><br>' );
	$predicate->NS( $namespace );
	echo( '<i>$predicate->Code( \'IS_A\' );</i><br>' );
	$predicate->Code( 'IS_A' );
	echo( '<i>$predicate->Name( \'Is a\' );</i><br>' );
	$predicate->Name( 'Is a' );
	echo( '<i>$idp = $predicate->Commit( $collection );</i><br>' );
	$idp = $predicate->Commit( $collection );
	echo( "$predicate<pre>" ); print_r( $predicate ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i><b>TERM 1</b></i><br>' );
	echo( '<i>$term1 = new COntologyTerm();</i><br>' );
	$term1 = new COntologyTerm();
	echo( '<i>$term1->NS( $namespace );</i><br>' );
	$term1->NS( $namespace );
	echo( '<i>$term1->Code( \'TERM1\' );</i><br>' );
	$term1->Code( 'TERM1' );
	echo( '<i>$term1->Name( \'Term 1\', \'en\' );</i><br>' );
	$term1->Name( 'Term 1', 'en' );
	echo( '<i>$term1->Name( \'Termine 1\', \'it\' );</i><br>' );
	$term1->Name( 'Termine 1', 'it' );
	echo( '<i>$term1->Definition( \'This term is the first term\', \'en\' );</i><br>' );
	$term1->Definition( 'This term is the first term', 'en' );
	echo( '<i>$term1->Definition( \'Questo è il primo termine\', \'it\' );</i><br>' );
	$term1->Definition( 'Questo è il primo termine', 'it' );
	echo( '<i>$term1->Stamp( new CDataTypeStamp() );</i><br>' );
	$term1->Stamp( new CDataTypeStamp() );
	echo( '<i>$id1 = $term1->Commit( $collection );</i><br>' );
	$id1 = $term1->Commit( $collection );
	echo( "$term1<pre>" ); print_r( $term1 ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i><b>TERM 2</b></i><br>' );
	echo( '<i>$term2 = new COntologyTerm();</i><br>' );
	$term2 = new COntologyTerm();
	echo( '<i>$term2->NS( \'NS\' );</i><br>' );
	$term2->NS( 'NS' );
	echo( '<i>$term2->Code( \'TERM2\' );</i><br>' );
	$term2->Code( 'TERM2' );
	echo( '<i>$term2->Name( \'Term 2\', \'en\' );</i><br>' );
	$term2->Name( 'Term 2', 'en' );
	echo( '<i>$term2->Name( \'Termine 1\', \'it\' );</i><br>' );
	$term2->Name( 'Termine 1', 'it' );
	echo( '<i>$term2->Definition( \'This is the second term\', \'en\' );</i><br>' );
	$term2->Definition( 'This is the second term', 'en' );
	echo( '<i>$term2->Definition( \'Questo è il secondo termine\', \'it\' );</i><br>' );
	$term2->Definition( 'Questo è il secondo termine', 'it' );
	echo( '<i>$term2->Relate( $term1, $predicate, TRUE );</i><br>' );
	$term2->Relate( $term1, $predicate, TRUE );
	echo( '<i>$term2->Valid( $term1 );</i><br>' );
	$term2->Valid( $term1 );
	echo( '<i>$term2->Stamp( new CDataTypeStamp() );</i><br>' );
	$term2->Stamp( new CDataTypeStamp() );
	echo( '<i>$id2 = $term2->Commit( $collection );</i><br>' );
	$id2 = $term2->Commit( $collection );
	echo( "$term2<pre>" ); print_r( $term2 ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i><b>TERM 3</b></i><br>' );
	echo( '<i>$term3 = new COntologyTerm();</i><br>' );
	$term3 = new COntologyTerm();
	echo( '<i>$term3->NS( $namespace );</i><br>' );
	$term3->NS( $namespace );
	echo( '<i>$term3->Code( \'TERM3\' );</i><br>' );
	$term3->Code( 'TERM3' );
	echo( '<i>$term3->Name( \'Term 3\' );</i><br>' );
	$term3->Name( 'Term 3' );
	echo( '<i>$term3->Relate( $term1, $predicate, TRUE );</i><br>' );
	$term3->Relate( $term1, $predicate, TRUE );
	echo( '<i>$object3->Relate( $term2, $predicate, TRUE );</i><br>' );
	$term3->Relate( $term2, $predicate, TRUE );
	echo( '<i>$term3->Valid( (string) $term2 );</i><br>' );
	$term3->Valid(  (string) $term2 );
	echo( '<i>$id3 = $term3->Commit( $collection );</i><br>' );
	$id3 = $term3->Commit( $collection );
	echo( "$term3<pre>" ); print_r( $term3 ); echo( '</pre>' );
	echo( '<hr>' );
	echo( '<hr>' );
	 
	//
	// Test valid chain.
	//
	echo( '<h3>Test valid chain</h3>' );

	echo( "<i>$term1</i><br>" );
	echo( '<i>$valid = COntologyTerm::ValidObject( $collection, $id1 );</i><br>' );
	$valid = COntologyTerm::ValidObject( $collection, $id1 );
	echo( '<pre>' ); print_r( $valid ); echo( '</pre>' );
	echo( '<hr>' );

	echo( "<i>$term2</i><br>" );
	echo( '<i>$valid = COntologyTerm::ValidObject( $collection, $id2 );</i><br>' );
	$valid = COntologyTerm::ValidObject( $collection, $id2 );
	echo( '<pre>' ); print_r( $valid ); echo( '</pre>' );
	echo( '<hr>' );

	echo( "<i>$term3</i><br>" );
	echo( '<i>$valid = COntologyTerm::ValidObject( $collection, $id3 );</i><br>' );
	$valid = COntologyTerm::ValidObject( $collection, $id3 );
	echo( '<pre>' ); print_r( $valid ); echo( '</pre>' );
	echo( '<hr>' );
	 
	//
	// Test validation.
	//
	echo( '<h3>Test validation</h3>' );

	try
	{
		echo( '<i>$term2->Name( FALSE, \'en\' );</i><br>' );
		$term2->Name( FALSE, 'en' );
		echo( '<i>$term2->Name( FALSE, \'it\' );</i><br>' );
		$term2->Name( FALSE, 'it' );
		$term2->Commit( $collection );
		echo( '<pre>' ); print_r( $term2 ); echo( '</pre>' );
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
