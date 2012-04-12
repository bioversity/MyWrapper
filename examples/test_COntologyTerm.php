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
require_once( kPATH_LIBRARY_SOURCE."CTermNamespace.php" );
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
	
	echo( '<i><b>TERM 1</b></i><br>' );
	echo( '<i>$term1 = new CTermNamespace();</i><br>' );
	$term1 = new CTermNamespace();
	echo( '<i>$term1->Code( \'NS\' );</i><br>' );
	$term1->Code( 'NS' );
	echo( '<i>$term1->Name( \'Namespace term\', \'en\' );</i><br>' );
	$term1->Name( 'Namespace term', 'en' );
	echo( '<i>$term1->Name( \'Termine spazio nome\', \'it\' );</i><br>' );
	$term1->Name( 'Termine spazio nome', 'it' );
	echo( '<i>$term1->Definition( \'This term is the namespace of other terms\', \'en\' );</i><br>' );
	$term1->Definition( 'This term is the namespace of other terms', 'en' );
	echo( '<i>$term1->Definition( \'Questo termine è lo spazio nomi di altri termini\', \'it\' );</i><br>' );
	$term1->Definition( 'Questo termine è lo spazio nomi di altri termini', 'it' );
	echo( '<i>$term1->Stamp( new CDataTypeStamp() );</i><br>' );
	$term1->Stamp( new CDataTypeStamp() );
	echo( '<i>$id1 = $term1->Commit( $collection );</i><br>' );
	$id1 = $term1->Commit( $collection );
	echo( $term1->Identifier()."<pre>" ); print_r( $term1 ); echo( '</pre>' );
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
	echo( '<i>$term2->Definition( \'This is the first term\', \'en\' );</i><br>' );
	$term2->Definition( 'This is the first term', 'en' );
	echo( '<i>$term2->Definition( \'Questo è il primo termine\', \'it\' );</i><br>' );
	$term2->Definition( 'Questo è il primo termine', 'it' );
	echo( '<i>$term2->RelatedFrom( $term1, TRUE );</i><br>' );
	$term2->RelatedFrom( $term1, TRUE );
	echo( '<i>$term2->Valid( $term1 );</i><br>' );
	$term2->Valid( $term1 );
	echo( '<i>$term2->Stamp( new CDataTypeStamp() );</i><br>' );
	$term2->Stamp( new CDataTypeStamp() );
	echo( '<i>$id2 = $term2->Commit( $collection );</i><br>' );
	$id2 = $term2->Commit( $collection );
	echo( $term2->Identifier()."<pre>" ); print_r( $term2 ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i><b>TERM 3</b></i><br>' );
	echo( '<i>$term3 = new COntologyTerm();</i><br>' );
	$term3 = new COntologyTerm();
	echo( '<i>$term3->NS( \'NS\' );</i><br>' );
	$term3->NS( 'NS' );
	echo( '<i>$term3->Code( \'TERM3\' );</i><br>' );
	$term3->Code( 'TERM3' );
	echo( '<i>$term3->Name( \'Term 3\' );</i><br>' );
	$term3->Name( 'Term 3' );
	echo( '<i>$term3->RelatedFrom( $term1, TRUE );</i><br>' );
	$term3->RelatedFrom( $term1, TRUE );
	echo( '<i>$object3->RelateTo( $term2, TRUE );</i><br>' );
	$term3->RelateTo( $term2, TRUE );
	echo( '<i>$term3->Valid( $term2->Identifier() );</i><br>' );
	$term3->Valid( $term2->Identifier() );
	echo( '<i>$id3 = $term3->Commit( $collection );</i><br>' );
	$id3 = $term3->Commit( $collection );
	echo( $term3->Identifier()."<pre>" ); print_r( $term3 ); echo( '</pre>' );
	echo( '<hr>' );
	echo( '<hr>' );
	 
	//
	// Test valid chain.
	//
	echo( '<h3>Test valid chain</h3>' );

	echo( "<i>".$term1->Identifier()."</i><br>" );
	echo( '<i>$valid = COntologyTerm::ValidObject( $collection, $id1 );</i><br>' );
	$valid = COntologyTerm::ValidObject( $collection, $id1 );
	echo( '<pre>' ); print_r( $valid ); echo( '</pre>' );
	echo( '<hr>' );

	echo( "<i>".$term2->Identifier()."</i><br>" );
	echo( '<i>$valid = COntologyTerm::ValidObject( $collection, $id2 );</i><br>' );
	$valid = COntologyTerm::ValidObject( $collection, $id2 );
	echo( '<pre>' ); print_r( $valid ); echo( '</pre>' );
	echo( '<hr>' );

	echo( "<i>".$term3->Identifier()."</i><br>" );
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
