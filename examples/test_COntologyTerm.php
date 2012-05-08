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
	// Create  terms.
	//
	echo( '<h3>Create terms</h3>' );
	
	echo( '<i><b>NAMESPACE</b></i><br>' );
	echo( '<i>$namespace = new COntologyTerm();</i><br>' );
	$namespace = new COntologyTerm();
	echo( '<i>$namespace->Code( \'NS\' );</i><br>' );
	$namespace->Code( 'NS' );
	echo( '<i>$namespace->Kind( kTYPE_NAMESPACE, TRUE );</i><br>' );
	$namespace->Kind( kTYPE_NAMESPACE, TRUE );
	echo( '<i>$idn = $namespace->Commit( $collection );</i><br>' );
	$idn = $namespace->Commit( $collection );
	echo( "$namespace<pre>" ); print_r( $namespace ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i><b>PREDICATE</b></i><br>' );
	echo( '<i>$predicate = new CPredicateTerm();</i><br>' );
	$predicate = new COntologyTerm();
	echo( '<i>$predicate->NS( $namespace );</i><br>' );
	$predicate->NS( $namespace );
	echo( '<i>$predicate->Code( \'IS_A\' );</i><br>' );
	$predicate->Code( 'IS_A' );
	echo( '<i>$predicate->Name( \'Is a\' );</i><br>' );
	$predicate->Name( 'Is a' );
	echo( '<i>$predicate->Kind( kTYPE_PREDICATE, TRUE );</i><br>' );
	$predicate->Kind( kTYPE_PREDICATE, TRUE );
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
	
	echo( '<i><b>TERM 2 (full attributes)</b></i><br>' );
	echo( '<i>$term2 = new COntologyTerm();</i><br>' );
	$term2 = new COntologyTerm();
	echo( '<i>$term2->NS( \'NS\' );</i><br>' );
	$term2->NS( 'NS' );
	echo( '<i>$term2->Code( \'TERM2\' );</i><br>' );
	$term2->Code( 'TERM2' );
	echo( '<i>$term2->Kind( \'Term\' );</i><br>' );
	$term2->Kind( 'Term' );
	echo( '<i>$term2->Name( \'Term 2\', \'en\' );</i><br>' );
	$term2->Name( 'Term 2', 'en' );
	echo( '<i>$term2->Name( \'Termine 1\', \'it\' );</i><br>' );
	$term2->Name( 'Termine 1', 'it' );
	echo( '<i>$term2->Definition( \'This is the second term\', \'en\' );</i><br>' );
	$term2->Definition( 'This is the second term', 'en' );
	echo( '<i>$term2->Definition( \'Questo è il secondo termine\', \'it\' );</i><br>' );
	$term2->Definition( 'Questo è il secondo termine', 'it' );
	echo( '<i>$term2->Description( \'This is an english description\', \'en\' );</i><br>' );
	$term2->Description( 'This is an english description', 'en' );
	echo( '<i>$term2->Domain( \'Accession\', TRUE );</i><br>' );
	$term2->Domain( 'Accession', TRUE );
	echo( '<i>$term2->Domain( \'Biology\', TRUE );</i><br>' );
	$term2->Domain( 'Biology', TRUE );
	echo( '<i>$term2->Category( \'Passport\', TRUE );</i><br>' );
	$term2->Category( 'Passport', TRUE );
	echo( '<i>$term2->Category( \'Ontology\', TRUE );</i><br>' );
	$term2->Category( 'Ontology', TRUE );
	echo( '<i>$term2->Synonym( \'Term 2\', kTYPE_BROAD, TRUE );</i><br>' );
	$term2->Synonym( 'Term 2', kTYPE_BROAD, TRUE );
	echo( '<i>$term2->Synonym( \'Termine 2\', kTYPE_BROAD, TRUE );</i><br>' );
	$term2->Synonym( 'Termine 2', kTYPE_BROAD, TRUE );
	echo( '<i>$term2->Synonym( \'ze term 2\', kTYPE_RELATED, TRUE );</i><br>' );
	$term2->Synonym( 'ze term 2', kTYPE_RELATED, TRUE );
	echo( '<i>$term2->Xref( $term1, kTYPE_RELATED, TRUE );</i><br>' );
	$term2->Xref( $term1, kTYPE_RELATED, TRUE );
	echo( '<i>$term2->Xref( $predicate, kTYPE_RELATED, TRUE );</i><br>' );
	$term2->Xref( $predicate, kTYPE_RELATED, TRUE );
	echo( '<i>$term2->Xref( $idn, kTYPE_RELATED, TRUE );</i><br>' );
	$term2->Xref( $idn, kTYPE_RELATED, TRUE );
	echo( '<i>$term2->Image( \'Thumb\', \'Link\', \'http://this.net/thumb.png\' );</i><br>' );
	$term2->Image( 'Thumb', 'Link', 'http://this.net/thumb.png' );
	echo( '<i>$term2->Image( \'Big\', \'Link\', \'http://this.net/big.png\' );</i><br>' );
	$term2->Image( 'Big', 'Link', 'http://this.net/big.png' );
	echo( '<i>$term2->NamespaceName( \'urn:lsid:me.org:domain:id:version\' );</i><br>' );
	$term2->NamespaceName( 'urn:lsid:me.org:domain:id:version' );
	echo( '<i>$term2->Source( \'wikipedia.org\', \'General\' );</i><br>' );
	$term2->Source( 'wikipedia.org', 'General' );
	echo( '<i>$term2->Source( \'http://wikipedia.org/something\', \'Specific\' );</i><br>' );
	$term2->Source( 'http://wikipedia.org/something', 'Specific' );
	echo( '<i>$term2->Version( \'1.0\' );</i><br>' );
	$term2->Version( '1.0' );
	echo( '<i>$term2->Node( 125, TRUE );</i><br>' );
	$term2->Node( 125, TRUE );
	echo( '<i>$term2->Node( 47, TRUE );</i><br>' );
	$term2->Node( 47, TRUE );
	echo( '<i>$term2->Predicate( 80, TRUE );</i><br>' );
	$term2->Predicate( 80, TRUE );
	echo( '<i>$term2->Predicate( 12, TRUE );</i><br>' );
	$term2->Predicate( 12, TRUE );
	echo( '<i>$term2->Enumeration( \'ITA\', TRUE );</i><br>' );
	$term2->Enumeration( 'ITA', TRUE );
	echo( '<i>$term2->Enumeration( \'it\', TRUE );</i><br>' );
	$term2->Enumeration( 'it', TRUE );
	echo( '<i>$term2->Type( kTYPE_STRING );</i><br>' );
	$term2->Type( kTYPE_STRING );
	echo( '<i>$term2->Pattern( \'[A-Z]{3}\', TRUE );</i><br>' );
	$term2->Pattern( '[A-Z]{3}', TRUE );
	echo( '<i>$term2->Pattern( \'[a-z]{2}\', TRUE );</i><br>' );
	$term2->Pattern( '[a-z]{2}', TRUE );
	echo( '<i>$term2->Cardinality( \'Single\' );</i><br>' );
	$term2->Cardinality( 'Single' );
	echo( '<i>$term2->Unit( $term1 );</i><br>' );
	$term2->Unit( $term1 );
	echo( '<i>$term2->Examples( \'1\', TRUE );</i><br>' );
	$term2->Examples( '1', TRUE );
	echo( '<i>$term2->Examples( \'3.5 (although integer it also accepts floats)\', TRUE );</i><br>' );
	$term2->Examples( '3.5 (although integer it also accepts floats)', TRUE );
	echo( '<i>$term2->Relate( $term1, $predicate, TRUE );</i><br>' );
	$term2->Relate( $term1, $predicate, TRUE );
	echo( '<i>$term2->Preferred( $idp );</i><br>' );
	$term2->Preferred( $idp );
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
	echo( '<i>$term3->Enumeration( \'ENUM\', TRUE );</i><br>' );
	$term3->Enumeration( 'ENUM', TRUE );
	echo( '<i>$term3->Image( \'First\', \'LINK\', \'http://image\' );</i><br>' );
	$term3->Image( 'First', 'LINK', 'http://image' );
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
	// Test persistence.
	//
	echo( '<h3>Test persistence</h3>' );

	echo( "<i>Load term 1</i><br>" );
	echo( '<i>$id = COntologyTerm::HashIndex( $term1[ kTAG_GID ] );</i><br>' );
	$id = COntologyTerm::HashIndex( $term1[ kTAG_GID ] );
	echo( '<i>$found = new COntologyTerm( $collection, $id );</i><br>' );
	$found = new COntologyTerm( $collection, $id );
	echo( '<i>$persistent = $found->Persistent();</i><br>' );
	$persistent = $found->Persistent();
	echo( "$persistent<pre>" ); print_r( $found ); echo( '</pre>' );
	echo( '<i>$id = $term2[ kTAG_LID ];</i><br>' );
	$id = $term2[ kTAG_LID ];
	echo( '<i>$found = new COntologyTerm( $collection, $id );</i><br>' );
	$found = new COntologyTerm( $collection, $id );
	echo( '<i>$persistent = $found->Persistent();</i><br>' );
	$persistent = $found->Persistent();
	echo( "$persistent<pre>" ); print_r( $found ); echo( '</pre>' );
	echo( '<i>$id = \'INVALID\';</i><br>' );
	$id = 'INVALID';
	echo( '<i>$found = new COntologyTerm( $collection, $id );</i><br>' );
	$found = new COntologyTerm( $collection, $id );
	echo( '<i>$persistent = $found->Persistent();</i><br>' );
	$persistent = $found->Persistent();
	echo( "$persistent<pre>" ); print_r( $found ); echo( '</pre>' );
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
