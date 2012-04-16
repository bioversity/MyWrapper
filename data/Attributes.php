<?php

/**
 * <i>Attributes</i> data definitions.
 *
 * This file contains the default attribute definitions.
 *
 *	@package	MyWrapper
 *	@subpackage	Data
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 16/04/2012
 */

/*=======================================================================================
 *																						*
 *									Attributes.php										*
 *																						*
 *======================================================================================*/

/**
 * Includes.
 *
 * This include file contains the default definitions and symbols.
 */
require_once( "/Library/WebServer/Library/wrapper/includes.inc.php" );

/**
 * Run-time.
 *
 * This include file contains the run-time definitions.
 */
require_once( "environment.inc.php" );

/**
 * Namespaces.
 *
 * This include file contains the namespace term class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CNamespaceTerm.php" );

/**
 * Ontologies.
 *
 * This include file contains the ontology term class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."COntology.php" );

/**
 * Attributes.
 *
 * This include file contains the attribute term class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CAttributeTerm.php" );

/**
 * Predicates.
 *
 * This include file contains the predicate term class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CPredicateTerm.php" );

/**
 * Measures.
 *
 * This include file contains the measure term class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CMeasureTerm.php" );

/**
 * Enumerations.
 *
 * This include file contains the enumeration term class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CEnumerationTerm.php" );

/**
 * Create default attributes ontology.
 *
 *	@package	MyWrapper
 *	@subpackage	Data
 */
try
{
	 
	/*===================================================================================
	 *	INITIALISE																		*
	 *==================================================================================*/

	//
	// Instantiate Mongo database.
	//
	$mongo = New Mongo();
	
	//
	// Select MCPD database.
	//
	$db = $mongo->selectDB( kDEFAULT_DATABASE );
	
	//
	// Instantiate collection.
	//
	$container = new CMongoContainer( $db->selectCollection( kDEFAULT_ONTOLOGY ) );
	
	//
	// Drop collection.
	//
	$container->Container()->drop();
	
	//
	// Instantiate collection.
	//
	$collection = new CMongoContainer( $db->selectCollection( kDEFAULT_ONTOLOGY ) );
	 
	/*===================================================================================
	 *	CREATE NAMESPACE																*
	 *==================================================================================*/
	
	$ns = new CNamespaceTerm();
	$ns->Code( '' );
	$ns->Name( 'Default namespace', kDEFAULT_LANGUAGE );
	$ns->Definition( 'The default namespace is used to qualify all attributes and other '
					.'terms that constitute the default vocabulary for the ontology. '
					.'Elements of this ontology are used to create all other ontologies.',
					  kDEFAULT_LANGUAGE );
	$ns->Commit( $collection );
	 
	/*===================================================================================
	 *	CREATE PREDICATES																*
	 *==================================================================================*/
	
	$is_a = new CPredicateTerm();
	$is_a->NS( $ns );
	$is_a->Code( 'IS-A' );
	$is_a->Name( 'Is a', kDEFAULT_LANGUAGE );
	$is_a->Definition( 'This predicate is equivalent to a subclass, it can be used to '
					  .'relate a term to the default category to which it belongs  '
					  .'within the current ontology.',
					   kDEFAULT_LANGUAGE );
	$is_a->Commit( $collection );
	 
	/*===================================================================================
	 *	CREATE ONTOLOGY																	*
	 *==================================================================================*/
	
	$onto = new COntology();
	$onto->NS( $ns );
	$onto->Code( 'DEFAULT-ATTRIBUTES' );
	$onto->Name( 'Default attributes', kDEFAULT_LANGUAGE );
	$onto->Definition( 'This ontology collects all attributes that are defined by '
					  .'default, in other words, those attributes that are used as  '
					  .'building blocks for all other attributes.',
					  kDEFAULT_LANGUAGE );
	$onto->Commit( $collection );
	 
	/*===================================================================================
	 *	DEFAULT IDENTIFICATION ATTRIBUTES												*
	 *==================================================================================*/
	
	$cat = new COntologyTerm();
	$cat->NS( $ns );
	$cat->Code( 'IDENTIFICATION-TERMS' );
	$cat->Name( 'Identification terms', kDEFAULT_LANGUAGE );
	$cat->Definition( 'This category collects all terms that have to do with the '
					.'identification or reference to objects.',
					 kDEFAULT_LANGUAGE );
	$cat->Relate( $onto, $is_a, TRUE );
	$cat->Commit( $collection );
	
	$term = new CAttributeTerm();
	$term->Code( kTAG_ID );
	$term->Name( 'Unique identifier', kDEFAULT_LANGUAGE );
	$term->Definition( 'This offset corresponds to the object\'s unique local identifier.',
					  kDEFAULT_LANGUAGE );
	$term->Relate( $cat, $is_a, TRUE );
	$term->Commit( $collection );
	 
	/*===================================================================================
	 *	DEFAULT REFERENCE TAGS ATTRIBUTES												*
	 *==================================================================================*/
	
	$cat = new COntologyTerm();
	$cat->NS( $ns );
	$cat->Code( 'REFERENCE-TAGS' );
	$cat->Name( 'Object reference offsets', kDEFAULT_LANGUAGE );
	$cat->Definition( 'These tags represent the attribute offsets that constitute '
					 .'an object reference.',
					 kDEFAULT_LANGUAGE );
	$cat->Relate( $onto, $is_a, TRUE );
	$cat->Commit( $collection );
	
	$term = new CAttributeTerm();
	$term->Code( kTAG_REFERENCE_ID );
	$term->Name( 'Identifier reference tag', kDEFAULT_LANGUAGE );
	$term->Definition( 'This is the tag is the offset used to indicate an object '
					  .'unique identifier within an object reference.',
					  kDEFAULT_LANGUAGE );
	$term->Relate( $cat, $is_a, TRUE );
	$term->Commit( $collection );
	
	$term = new CAttributeTerm();
	$term->Code( kTAG_REFERENCE_CONTAINER );
	$term->Name( 'Collection name reference tag', kDEFAULT_LANGUAGE );
	$term->Definition( 'This is the tag is the offset used to indicate a container '
					  .'within an object reference.',
					  kDEFAULT_LANGUAGE );
	$term->Relate( $cat, $is_a, TRUE );
	$term->Commit( $collection );
	
	$term = new CAttributeTerm();
	$term->Code( kTAG_REFERENCE_DATABASE );
	$term->Name( 'Database name reference tag', kDEFAULT_LANGUAGE );
	$term->Definition( 'This is the tag is the offset used to indicate a database '
					  .'within an object reference.',
					  kDEFAULT_LANGUAGE );
	$term->Relate( $cat, $is_a, TRUE );
	$term->Commit( $collection );

} // TRY BLOCK.

//
// CATCH BLOCK.
//
catch( Exception $error )
{
//	echo( CException::AsHTML( $error ) );
	echo( (string) $error );
}

echo( "Done!\n" );


?>
