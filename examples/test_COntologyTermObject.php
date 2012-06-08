<?php

/**
 * {@link COntologyTermObject.php Base} term test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * base term {@link COntologyTermObject class}.
 *
 *	@package	Test
 *	@subpackage	Entities
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 12/04/2012
 */

/*=======================================================================================
 *																						*
 *								test_COntologyBaseTerm.php								*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."COntologyTermObject.php" );


/*=======================================================================================
 *	TEST CLASS																			*
 *======================================================================================*/
 
//
// Test class.
//
class MyClass extends COntologyTermObject{}


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
	$collection = new CMongoContainer( $db->selectCollection( 'COntologyTermObject' ) );
	 
	//
	// Load terms.
	//
	echo( '<h3>Load terms</h3>' );
	
	echo( '<i><b>PREDICATE</b></i><br>' );
	echo( '<i>$predicate = new MyClass();</i><br>' );
	$predicate = new MyClass();
	echo( '<i>$predicate->Code( \'IS_A\' );</i><br>' );
	$predicate->Code( 'IS_A' );
	echo( '<i>$idp = $predicate->Commit( $collection );</i><br>' );
	$idp = $predicate->Commit( $collection );
	echo( "$predicate<pre>" ); print_r( $predicate ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i><b>TERM 1</b></i><br>' );
	echo( '<i>$term1 = new MyClass();</i><br>' );
	$term1 = new MyClass();
	echo( '<i>$term1->Code( \'NS\' );</i><br>' );
	$term1->Code( 'NS' );
	echo( '<i>$term1->Kind( kTYPE_NAMESPACE, TRUE );</i><br>' );
	$term1->Kind( kTYPE_NAMESPACE, TRUE );
	echo( '<i>$term1->Modified( new CDataTypeStamp() );</i><br>' );
	$term1->Modified( new CDataTypeStamp() );
	echo( '<i>$id1 = $term1->Commit( $collection );</i><br>' );
	$id1 = $term1->Commit( $collection );
	echo( "$term1<pre>" ); print_r( $term1 ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i><b>TERM 2</b></i><br>' );
	echo( '<i>$term2 = new MyClass();</i><br>' );
	$term2 = new MyClass();
	echo( '<i>$term2->NS( \'NS\' );</i><br>' );
	$term2->NS( 'NS' );
	echo( '<i>$term2->Code( \'TERM2\' );</i><br>' );
	$term2->Code( 'TERM2' );
	echo( '<i>$term2->Kind( kTYPE_TRAIT, TRUE );</i><br>' );
	$term2->Kind( kTYPE_TRAIT, TRUE );
	echo( '<i>$term2->Relate( $term1, $predicate, TRUE );</i><br>' );
	$term2->Relate( $term1, $predicate, TRUE );
	echo( '<i>$term2->Valid( $term1 );</i><br>' );
	$term2->Valid( $term1 );
	echo( '<i>$term2->Modified( new CDataTypeStamp() );</i><br>' );
	$term2->Modified( new CDataTypeStamp() );
	echo( '<i>$id2 = $term2->Commit( $collection );</i><br>' );
	$id2 = $term2->Commit( $collection );
	echo( "$term2<pre>" ); print_r( $term2 ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i><b>TERM 3</b></i><br>' );
	echo( '<i>$term3 = new MyClass();</i><br>' );
	$term3 = new MyClass();
	echo( '<i>$term3->NS( \'NS\' );</i><br>' );
	$term3->NS( 'NS' );
	echo( '<i>$term3->Code( \'TERM3\' );</i><br>' );
	$term3->Code( 'TERM3' );
	echo( '<i>$term3->Relate( $term1, $predicate, TRUE );</i><br>' );
	$term3->Relate( $term1, $predicate, TRUE );
	echo( '<i>$object3->Relate( $term2, $predicate, TRUE );</i><br>' );
	$term3->Relate( $term2, $predicate, TRUE );
	echo( '<i>$term3->Valid( (string) $term2 );</i><br>' );
	$term3->Valid( (string) $term2 );
	echo( '<i>$id3 = $term3->Commit( $collection );</i><br>' );
	$id3 = $term3->Commit( $collection );
	echo( "$term3<pre>" ); print_r( $term3 ); echo( '</pre>' );
	echo( '<hr>' );
	echo( '<hr>' );
	 
	//
	// Try duplicate reference.
	//
	echo( '<h3>Try duplicate reference</h3>' );

	echo( '<i>$term3->Relate( $id1, $idp, TRUE );</i><br>' );
	$term3->Relate( $id1, $idp, TRUE );
	echo( '<pre>' ); print_r( $term3 ); echo( '</pre>' );
	echo( '<i>$term3->Commit( $collection );</i><br>' );
	$term3->Commit( $collection );
	echo( '<pre>' ); print_r( $term3 ); echo( '</pre>' );
	echo( '<hr>' );
	echo( '<hr>' );
	 
	//
	// Try delete reference.
	//
	echo( '<h3>Try delete reference</h3>' );

	echo( '<i>$term3->Relate( $id1, $idp, FALSE );</i><br>' );
	$term3->Relate( $id1, $idp, FALSE );
	echo( '<i>$term3->Commit( $collection );</i><br>' );
	$term3->Commit( $collection );
	echo( '<pre>' ); print_r( $term3 ); echo( '</pre>' );
	echo( '<hr>' );
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
