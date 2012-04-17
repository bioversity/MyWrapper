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
	 *	DEFAULT NAMESPACE																*
	 *==================================================================================*/
	
	$ns = new CNamespaceTerm();
	$ns->Code( '' );
	$ns->Name( 'Default namespace', kDEFAULT_LANGUAGE );
	$ns->Definition( 'The default namespace is used to qualify all attributes and other '
					.'terms that constitute the default vocabulary for the ontology. '
					.'Elements of this ontology are used to create all other ontologies.',
					  kDEFAULT_LANGUAGE );
	$ns->Commit( $collection );
	echo( $ns->Name( NULL, kDEFAULT_LANGUAGE )." [$ns]\n" );
	 
	/*===================================================================================
	 *	DEFAULT PREDICATES																*
	 *==================================================================================*/
	
	$term = new CPredicateTerm();
	$term->NS( $ns );
	$term->Code( 'IS-A' );
	$term->Name( 'Is-a', kDEFAULT_LANGUAGE );
	$term->Definition( 'This predicate is equivalent to a subclass, it can be used to '
					  .'relate a term to the default category to which it belongs '
					  .'within the current ontology.',
					   kDEFAULT_LANGUAGE );
	$term->Synonym( 'kPRED_IS_A', kTAG_REFERENCE_EXACT );
	$term->Commit( $collection );
	echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
	$term = new CPredicateTerm();
	$term->NS( $ns );
	$term->Code( 'PART-OF' );
	$term->Name( 'Part-of', kDEFAULT_LANGUAGE );
	$term->Definition( 'This predicate indicates that the subject is part of the object.',
					   kDEFAULT_LANGUAGE );
	$term->Synonym( 'kPRED_PART_OF', kTAG_REFERENCE_EXACT );
	$term->Commit( $collection );
	echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
	$term = new CPredicateTerm();
	$term->NS( $ns );
	$term->Code( 'SCALE-OF' );
	$term->Name( 'Scale-of', kDEFAULT_LANGUAGE );
	$term->Definition( 'This predicate is used to relate a term that can be used to '
					  .'annotate data with its method term or trait term.',
					   kDEFAULT_LANGUAGE );
	$term->Synonym( 'kPRED_SCALE_OF', kTAG_REFERENCE_EXACT );
	$term->Commit( $collection );
	echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
	$term = new CPredicateTerm();
	$term->NS( $ns );
	$term->Code( 'METHOD-OF' );
	$term->Name( 'Method-of', kDEFAULT_LANGUAGE );
	$term->Definition( 'This predicate is used to relate a term that defines '
					  .'a measurement method to the trait term.',
					   kDEFAULT_LANGUAGE );
	$term->Synonym( 'kPRED_METHOD_OF', kTAG_REFERENCE_EXACT );
	$term->Commit( $collection );
	echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	 
	/*===================================================================================
	 *	DEFAULT IDENTIFICATION OFFSETS													*
	 *==================================================================================*/
	
	$term = new CAttributeTerm();
	$term->Code( kTAG_ID );
	$term->Name( 'Unique identifier', kDEFAULT_LANGUAGE );
	$term->Definition( 'This offset corresponds to the object\'s unique local identifier.',
					  kDEFAULT_LANGUAGE );
	$term->Synonym( 'kTAG_ID', kTAG_REFERENCE_EXACT );
	$term->Commit( $collection );
	echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	 
	/*===================================================================================
	 *	DEFAULT REFERENCE TAGS ATTRIBUTES												*
	 *==================================================================================*/
	
	$term = new CAttributeTerm();
	$term->Code( kTAG_REFERENCE_ID );
	$term->Name( 'Identifier reference tag', kDEFAULT_LANGUAGE );
	$term->Definition( 'This is the offset used to indicate an object '
					  .'unique identifier within an object reference.',
					  kDEFAULT_LANGUAGE );
	$term->Synonym( 'kTAG_REFERENCE_ID', kTAG_REFERENCE_EXACT );
	$term->Commit( $collection );
	echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
	$term = new CAttributeTerm();
	$term->Code( kTAG_REFERENCE_CONTAINER );
	$term->Name( 'Collection name reference tag', kDEFAULT_LANGUAGE );
	$term->Definition( 'This is the offset used to indicate a container '
					  .'within an object reference.',
					  kDEFAULT_LANGUAGE );
	$term->Synonym( 'kTAG_REFERENCE_CONTAINER', kTAG_REFERENCE_EXACT );
	$term->Commit( $collection );
	echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
	$term = new CAttributeTerm();
	$term->Code( kTAG_REFERENCE_DATABASE );
	$term->Name( 'Database name reference tag', kDEFAULT_LANGUAGE );
	$term->Definition( 'This is the offset used to indicate a database '
					  .'within an object reference.',
					  kDEFAULT_LANGUAGE );
	$term->Synonym( 'kTAG_REFERENCE_DATABASE', kTAG_REFERENCE_EXACT );
	$term->Commit( $collection );
	echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	 
	/*===================================================================================
	 *	DEFAULT REFERENCE KINDS															*
	 *==================================================================================*/
	
	$term = new CAttributeTerm();
	$term->NS( $ns );
	$term->Code( substr( kTAG_REFERENCE_EXACT, 1 ) );
	$term->Name( 'Exact reference', kDEFAULT_LANGUAGE );
	$term->Synonym( 'kTAG_REFERENCE_EXACT', kTAG_REFERENCE_EXACT );
	$term->Commit( $collection );
	echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
	$term = new CAttributeTerm();
	$term->NS( $ns );
	$term->Code( substr( kTAG_REFERENCE_BROAD, 1 ) );
	$term->Name( 'Broad reference', kDEFAULT_LANGUAGE );
	$term->Synonym( 'kTAG_REFERENCE_BROAD', kTAG_REFERENCE_EXACT );
	$term->Commit( $collection );
	echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
	$term = new CAttributeTerm();
	$term->NS( $ns );
	$term->Code( substr( kTAG_REFERENCE_NARROW, 1 ) );
	$term->Name( 'Broad reference', kDEFAULT_LANGUAGE );
	$term->Synonym( 'kTAG_REFERENCE_NARROW', kTAG_REFERENCE_EXACT );
	$term->Commit( $collection );
	echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
	$term = new CAttributeTerm();
	$term->NS( $ns );
	$term->Code( substr( kTAG_REFERENCE_RELATED, 1 ) );
	$term->Name( 'Related reference', kDEFAULT_LANGUAGE );
	$term->Synonym( 'kTAG_REFERENCE_RELATED', kTAG_REFERENCE_EXACT );
	$term->Commit( $collection );
	echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	 
	/*===================================================================================
	 *	DEFAULT REFERENCE OFFSETS														*
	 *==================================================================================*/
	
	$term = new CAttributeTerm();
	$term->NS( $ns );
	$term->Code( substr( kOFFSET_SYNONYM, 1 ) );
	$term->Name( 'Synonym', kDEFAULT_LANGUAGE );
	$term->Synonym( 'kTAG_REFERENCE_EXACT', kTAG_REFERENCE_EXACT );
	$term->Definition( 'This is the offset used to indicate a synonym, '
					  .'a synonym is a string that can be used as a substitute to the '
					  .'term, it may be of several kinds: exact, broad, narrow and '
					  .'related.',
					  kDEFAULT_LANGUAGE );
	$term->Commit( $collection );
	echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
	$term = new CAttributeTerm();
	$term->NS( $ns );
	$term->Code( substr( kOFFSET_XREF, 1 ) );
	$term->Name( 'Cross-reference', kDEFAULT_LANGUAGE );
	$term->Synonym( 'kOFFSET_XREF', kTAG_REFERENCE_EXACT );
	$term->Definition( 'This is the offset used to indicate a cross-reference, a '
					  .'cross-reference is a reference to another term in the same '
					  .'container, a sort of synonym, except that it is not a string, '
					  .'but a reference to another term object. Cross-references can be '
					  .'of several kinds: exact, broad, narrow and related.',
					  kDEFAULT_LANGUAGE );
	$term->Commit( $collection );
	echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	 
	/*===================================================================================
	 *	DEFAULT OBJECT TAGS																*
	 *==================================================================================*/
	
	$term = new CAttributeTerm();
	$term->NS( $ns );
	$term->Code( substr( kTAG_CLASS, 1 ) );
	$term->Name( 'Class', kDEFAULT_LANGUAGE );
	$term->Synonym( 'kTAG_CLASS', kTAG_REFERENCE_EXACT );
	$term->Definition( 'This is the offset that should be used to store the object\'s '
					  .'class name, it will be used to instantiate objects when loading '
					  .'them from their containers.',
					  kDEFAULT_LANGUAGE );
	$term->Commit( $collection );
	echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
	$term = new CAttributeTerm();
	$term->NS( $ns );
	$term->Code( substr( kTAG_VERSION, 1 ) );
	$term->Name( 'Version', kDEFAULT_LANGUAGE );
	$term->Synonym( 'kTAG_VERSION', kTAG_REFERENCE_EXACT );
	$term->Definition( 'This tag is an offset that should be used to represent the '
					  .'object\'s version, the version is a value that should change '
					  .'each time the object is saved: it can be used to check whether '
					  .'an object was modified since the last time it was read. '
					  .'By default it is an integer incremented each time the object '
					  .'is saved.',
					  kDEFAULT_LANGUAGE );
	$term->Commit( $collection );
	echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	 
	/*===================================================================================
	 *	DEFAULT ATTRIBUTE TAGS															*
	 *==================================================================================*/
	
	$term = new CAttributeTerm();
	$term->NS( $ns );
	$term->Code( substr( kTAG_TYPE, 1 ) );
	$term->Name( 'Type', kDEFAULT_LANGUAGE );
	$term->Synonym( 'kTAG_TYPE', kTAG_REFERENCE_EXACT );
	$term->Definition( 'This tag is used as the default offset for indicating the data '
					  .'type of an item, in general it is used in a structure in '
					  .'conjunction with another offset to indicate the data type of '
					  .'the item.',
					  kDEFAULT_LANGUAGE );
	$term->Commit( $collection );
	echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
	$term = new CAttributeTerm();
	$term->NS( $ns );
	$term->Code( substr( kTAG_KIND, 1 ) );
	$term->Name( 'Kind', kDEFAULT_LANGUAGE );
	$term->Synonym( 'kTAG_KIND', kTAG_REFERENCE_EXACT );
	$term->Definition( 'This tag is used as the default offset for indicating a kind '
					  .'attribute. A kind is similar to the type attribute, except that '
					  .'in the latter case it qualifies specifically the data elements, '
					  .'in this case it discriminates the elements of a list.',
					  kDEFAULT_LANGUAGE );
	$term->Commit( $collection );
	echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
	$term = new CAttributeTerm();
	$term->NS( $ns );
	$term->Code( substr( kTAG_UNIT, 1 ) );
	$term->Name( 'Unit', kDEFAULT_LANGUAGE );
	$term->Synonym( 'kTAG_UNIT', kTAG_REFERENCE_EXACT );
	$term->Definition( 'This tag is used as the default offset for indicating a unit '
					  .'attribute. A unit is a measurement unit such as centimeters, '
					  .'in general this offset will hold a reference to an object that '
					  .'defines the unit.',
					  kDEFAULT_LANGUAGE );
	$term->Commit( $collection );
	echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
	$term = new CAttributeTerm();
	$term->NS( $ns );
	$term->Code( substr( kTAG_SOURCE, 1 ) );
	$term->Name( 'Source', kDEFAULT_LANGUAGE );
	$term->Synonym( 'kTAG_SOURCE', kTAG_REFERENCE_EXACT );
	$term->Definition( 'This tag is used as the default offset for indicating a unit '
					  .'attribute. A unit is a measurement unit such as centimeters, '
					  .'in general this offset will hold a reference to an object that '
					  .'defines the unit.',
					  kDEFAULT_LANGUAGE );
	$term->Commit( $collection );
	echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
	$term = new CAttributeTerm();
	$term->NS( $ns );
	$term->Code( substr( kTAG_DATA, 1 ) );
	$term->Name( 'Data', kDEFAULT_LANGUAGE );
	$term->Synonym( 'kTAG_DATA', kTAG_REFERENCE_EXACT );
	$term->Definition( 'This tag is used as the default offset for indicating an '
					  .'attribute\'s data or content, in general this tag is used in '
					  .'conjunction with the type or kind offsets when storing lists of '
					  .'items.',
					  kDEFAULT_LANGUAGE );
	$term->Commit( $collection );
	echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
	$term = new CAttributeTerm();
	$term->NS( $ns );
	$term->Code( substr( kTAG_CODE, 1 ) );
	$term->Name( 'Code', kDEFAULT_LANGUAGE );
	$term->Synonym( 'kTAG_CODE', kTAG_REFERENCE_EXACT );
	$term->Definition( 'This tag is used as the default offset for indicating an '
					  .'attribute\'s code or acronym.',
					  kDEFAULT_LANGUAGE );
	$term->Commit( $collection );
	echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );

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
