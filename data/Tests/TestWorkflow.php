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
 *									TestWorkflow.php									*
 *																						*
 *======================================================================================*/

/**
 * Includes.
 *
 * This include file contains the default definitions and symbols.
 */
require_once( "/Library/WebServer/Library/wrapper/includes.inc.php" );

/**
 * Categories environment.
 *
 * This include file contains the default domain and category definitions.
 */
require_once( "/Library/WebServer/Library/wrapper/local/categories.inc.php" );

/**
 * Server environment.
 *
 * This include file contains the server run-time definitions.
 */
require_once( "/Library/WebServer/Library/wrapper/local/server.inc.php" );

/**
 * Site environment.
 *
 * This include file contains the site run-time definitions.
 */
require_once( "site.inc.php" );

/**
 * ADODB library.
 */
require_once( kPATH_LIB_ADODB."adodb.inc.php" );

/**
 * ADODB iterators.
 */
require_once( kPATH_LIB_ADODB."adodb-iterator.inc.php" );

/**
 * ADODB exceptions.
 */
require_once( kPATH_LIB_ADODB."adodb-exceptions.inc.php" );

/**
 * Operators.
 *
 * This include file contains all operator definitions.
 */
require_once( kPATH_LIBRARY_DEFINES."Operators.inc.php" );

/**
 * Session.
 *
 * This include file contains the session tag definitions.
 */
require_once( kPATH_LIBRARY_DEFINES."Session.inc.php" );

/**
 * Terms.
 *
 * This include file contains the ontology terms class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."COntologyTerm.php" );

/**
 * FAO Institutes.
 *
 * This include file contains the ontology terms class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CFAOInstitute.php" );

/**
 * Users.
 *
 * This include file contains the user terms class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CUser.inc.php" );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."COntologyEdge.php" );

use Everyman\Neo4j\Transport,
	Everyman\Neo4j\Client,
	Everyman\Neo4j\Index\NodeIndex,
	Everyman\Neo4j\Index\RelationshipIndex,
	Everyman\Neo4j\Index\NodeFulltextIndex,
	Everyman\Neo4j\Node,
	Everyman\Neo4j\Batch;

		

/*=======================================================================================
 *																						*
 *											MAIN										*
 *																						*
 *======================================================================================*/



/**
 * Open session.
 */
session_start();

/**
 * Init session variables.
 */
$_SESSION[ 'TERMS' ] = $_SESSION[ 'NODES' ] = Array();
	 
/**
 * Create test ontology.
 *
 *	@package	MyWrapper
 *	@subpackage	Data
 */
try
{
	//
	// Connect.
	//
	Connect( kDEFAULT_DATABASE, kDEFAULT_CNT_TERMS, FALSE );
	
	//
	// Load test ontology.
	//
	LoadTerms( $_SESSION[ kSESSION_CONTAINER ], TRUE );
	LoadDescriptors( $_SESSION[ kSESSION_CONTAINER ], TRUE );

} // TRY BLOCK.

//
// CATCH BLOCK.
//
catch( Exception $error )
{
//	echo( CException::AsHTML( $error ) );
	echo( (string) $error );
echo( "\n" );
print_r( $error->Reference( 'Object' ) );
echo( "\n" );
}

exit( "Done!\n" );

		

/*=======================================================================================
 *																						*
 *										FUNCTIONS										*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Connect																			*
	 *==================================================================================*/

	/**
	 * Connect.
	 *
	 * This function will connect to the database, if you provide <i>TRUE</i> to the last
	 * parameter, the function will first erase the database.
	 *
	 * @param string				$theDatabase		Database name.
	 * @param string				$theContainer		Container name.
	 * @param boolean				$doErase			Erase database flag.
	 *
	 * @access protected
	 */
	function Connect( $theDatabase = kDEFAULT_DATABASE,
					  $theContainer = kDEFAULT_CNT_TERMS,
					  $doErase = FALSE )
	{
		//
		// Instantiate Mongo database.
		//
		$_SESSION[ kSESSION_MONGO ] = New Mongo();
		
		//
		// Select database.
		//
		$_SESSION[ kSESSION_DATABASE ]
			= $_SESSION[ kSESSION_MONGO ]->selectDB( $theDatabase );
		
		//
		// Erase database.
		//
		if( $doErase )
		{
			//
			// Erase.
			//
			$_SESSION[ kSESSION_DATABASE ]->drop();
			
			//
			// Connect.
			//
			$_SESSION[ kSESSION_DATABASE ]
				= $_SESSION[ kSESSION_MONGO ]->selectDB( $theDatabase );
		
		} // Erase database.
		
		//
		// Select terms collection.
		//
		$collection = $_SESSION[ kSESSION_DATABASE ]->selectCollection( $theContainer );
		
		//
		// Select container.
		//
		$_SESSION[ kSESSION_CONTAINER ] = new CMongoContainer( $collection );
		
		//
		// Select Neo4j.
		//
		$_SESSION[ kSESSION_NEO4J ] = new Everyman\Neo4j\Client( 'localhost', 7474 );
	
	} // Connect.

	 
	/*===================================================================================
	 *	LoadTerms																		*
	 *==================================================================================*/

	/**
	 * Load descriptors.
	 *
	 * This function will load the terms.
	 *
	 * If the last parameter is <i>TRUE</i>, the function will display the name of the
	 * created terms.
	 *
	 * @param CContainer			$theContainer		Collection.
	 * @param boolean				$doDisplay			Display created terms.
	 *
	 * @access protected
	 */
	function LoadTerms( CContainer $theContainer, $doDisplay = TRUE )
	{
		//
		// Inform.
		//
		if( $doDisplay )
		{
			echo( "\n".__FUNCTION__."\n" );
			echo( "------------------\n" );
		}
	 
		/*================================================================================
		 *	TEST																		 *
		 *===============================================================================*/

		//
		// Load term.
		//
		$code = 'TEST';
		$term = new COntologyTerm(
			$theContainer, COntologyTerm::HashIndex(
				$code ) );
		if( ! $term->Persistent() )
		{
			//
			// Create term.
			//
			$term->Code( $code );
			$term->Kind( kTYPE_NAMESPACE, TRUE );
			$term->Name( 'Test', kDEFAULT_LANGUAGE );
			$term->Definition( 'Test workflow metadata', kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		
		} $ns = $_SESSION[ 'TERMS' ][ $code ] = $term;
	 
		/*================================================================================
		 *	Predicates																	 *
		 *===============================================================================*/

		//
		// Load term.
		//
		$code = 'TREATMENT-OF';
		$term = new COntologyTerm(
			$theContainer, COntologyTerm::HashIndex(
				$code ) );
		if( ! $term->Persistent() )
		{
			//
			// Create term.
			//
			$term->NS( $ns );
			$term->Code( $code );
			$term->Kind( kTYPE_PREDICATE, TRUE );
			$term->Name( 'Treatment', kDEFAULT_LANGUAGE );
			$term->Definition( 'Treatment workflow', kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		
		} $_SESSION[ 'TERMS' ][ $code ] = $term;

		//
		// Load term.
		//
		$code = 'WATER-1000';
		$term = new COntologyTerm(
			$theContainer, COntologyTerm::HashIndex(
				$code ) );
		if( ! $term->Persistent() )
		{
			//
			// Create term.
			//
			$term->NS( $ns );
			$term->Code( $code );
			$term->Kind( kTYPE_PREDICATE, TRUE );
			$term->Name( 'Irrigation (1000) liters', kDEFAULT_LANGUAGE );
			$term->Definition( 'Irrigate 1000 liters', kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		
		} $_SESSION[ 'TERMS' ][ $code ] = $term;

		//
		// Load term.
		//
		$code = 'WATER-1500';
		$term = new COntologyTerm(
			$theContainer, COntologyTerm::HashIndex(
				$code ) );
		if( ! $term->Persistent() )
		{
			//
			// Create term.
			//
			$term->NS( $ns );
			$term->Code( $code );
			$term->Kind( kTYPE_PREDICATE, TRUE );
			$term->Name( 'Irrigation (1500) liters', kDEFAULT_LANGUAGE );
			$term->Definition( 'Irrigate 1500 liters', kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		
		} $_SESSION[ 'TERMS' ][ $code ] = $term;

		//
		// Load term.
		//
		$code = 'WATER-2000';
		$term = new COntologyTerm(
			$theContainer, COntologyTerm::HashIndex(
				$code ) );
		if( ! $term->Persistent() )
		{
			//
			// Create term.
			//
			$term->NS( $ns );
			$term->Code( $code );
			$term->Kind( kTYPE_PREDICATE, TRUE );
			$term->Name( 'Irrigation (2000) liters', kDEFAULT_LANGUAGE );
			$term->Definition( 'Irrigate 2000 liters', kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		
		} $_SESSION[ 'TERMS' ][ $code ] = $term;

		//
		// Load term.
		//
		$code = 'WATER-2000';
		$term = new COntologyTerm(
			$theContainer, COntologyTerm::HashIndex(
				$code ) );
		if( ! $term->Persistent() )
		{
			//
			// Create term.
			//
			$term->NS( $ns );
			$term->Code( $code );
			$term->Kind( kTYPE_PREDICATE, TRUE );
			$term->Name( 'Irrigation (2000) liters', kDEFAULT_LANGUAGE );
			$term->Definition( 'Irrigate 2000 liters', kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		
		} $_SESSION[ 'TERMS' ][ $code ] = $term;

		//
		// Load term.
		//
		$code = 'FERTILIZER-A';
		$term = new COntologyTerm(
			$theContainer, COntologyTerm::HashIndex(
				$code ) );
		if( ! $term->Persistent() )
		{
			//
			// Create term.
			//
			$term->NS( $ns );
			$term->Code( $code );
			$term->Kind( kTYPE_PREDICATE, TRUE );
			$term->Name( 'Fertilizer A', kDEFAULT_LANGUAGE );
			$term->Definition( 'Use brand A fertilizer', kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		
		} $_SESSION[ 'TERMS' ][ $code ] = $term;

		//
		// Load term.
		//
		$code = 'FERTILIZER-B';
		$term = new COntologyTerm(
			$theContainer, COntologyTerm::HashIndex(
				$code ) );
		if( ! $term->Persistent() )
		{
			//
			// Create term.
			//
			$term->NS( $ns );
			$term->Code( $code );
			$term->Kind( kTYPE_PREDICATE, TRUE );
			$term->Name( 'Fertilizer B', kDEFAULT_LANGUAGE );
			$term->Definition( 'Use brand B fertilizer', kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		
		} $_SESSION[ 'TERMS' ][ $code ] = $term;

		//
		// Load term.
		//
		$code = 'FERTILIZER-C';
		$term = new COntologyTerm(
			$theContainer, COntologyTerm::HashIndex(
				$code ) );
		if( ! $term->Persistent() )
		{
			//
			// Create term.
			//
			$term->NS( $ns );
			$term->Code( $code );
			$term->Kind( kTYPE_PREDICATE, TRUE );
			$term->Name( 'Fertilizer C', kDEFAULT_LANGUAGE );
			$term->Definition( 'Use brand C fertilizer', kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		
		} $_SESSION[ 'TERMS' ][ $code ] = $term;

		//
		// Load term.
		//
		$code = 'FERTILIZER-100';
		$term = new COntologyTerm(
			$theContainer, COntologyTerm::HashIndex(
				$code ) );
		if( ! $term->Persistent() )
		{
			//
			// Create term.
			//
			$term->NS( $ns );
			$term->Code( $code );
			$term->Kind( kTYPE_PREDICATE, TRUE );
			$term->Name( 'Fertilizer (100) kilos', kDEFAULT_LANGUAGE );
			$term->Definition( 'Fertilize 100 Kg.', kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		
		} $_SESSION[ 'TERMS' ][ $code ] = $term;

		//
		// Load term.
		//
		$code = 'FERTILIZER-150';
		$term = new COntologyTerm(
			$theContainer, COntologyTerm::HashIndex(
				$code ) );
		if( ! $term->Persistent() )
		{
			//
			// Create term.
			//
			$term->NS( $ns );
			$term->Code( $code );
			$term->Kind( kTYPE_PREDICATE, TRUE );
			$term->Name( 'Fertilizer (150) kilos', kDEFAULT_LANGUAGE );
			$term->Definition( 'Fertilize 150 Kg.', kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		
		} $_SESSION[ 'TERMS' ][ $code ] = $term;

		//
		// Load term.
		//
		$code = 'FERTILIZER-200';
		$term = new COntologyTerm(
			$theContainer, COntologyTerm::HashIndex(
				$code ) );
		if( ! $term->Persistent() )
		{
			//
			// Create term.
			//
			$term->NS( $ns );
			$term->Code( $code );
			$term->Kind( kTYPE_PREDICATE, TRUE );
			$term->Name( 'Fertilizer (200) kilos', kDEFAULT_LANGUAGE );
			$term->Definition( 'Fertilize 200 Kg.', kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		
		} $_SESSION[ 'TERMS' ][ $code ] = $term;
	 
		/*================================================================================
		 *	Traits																		 *
		 *===============================================================================*/

		//
		// Load term.
		//
		$code = 'Protein-Content';
		$term = new COntologyTerm(
			$theContainer, COntologyTerm::HashIndex(
				$code ) );
		if( ! $term->Persistent() )
		{
			//
			// Create term.
			//
			$term->NS( $ns );
			$term->Code( $code );
			$term->Kind( kTYPE_TRAIT, TRUE );
			$term->Name( 'Protein content', kDEFAULT_LANGUAGE );
			$term->Definition( 'Protein content', kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		
		} $_SESSION[ 'TERMS' ][ $code ] = $term;

		//
		// Load term.
		//
		$code = 'Plant-Height';
		$term = new COntologyTerm(
			$theContainer, COntologyTerm::HashIndex(
				$code ) );
		if( ! $term->Persistent() )
		{
			//
			// Create term.
			//
			$term->NS( $ns );
			$term->Code( $code );
			$term->Kind( kTYPE_TRAIT, TRUE );
			$term->Name( 'Plant height', kDEFAULT_LANGUAGE );
			$term->Definition( 'Plant height', kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		
		} $_SESSION[ 'TERMS' ][ $code ] = $term;

		//
		// Load term.
		//
		$code = 'Grain-weight';
		$term = new COntologyTerm(
			$theContainer, COntologyTerm::HashIndex(
				$code ) );
		if( ! $term->Persistent() )
		{
			//
			// Create term.
			//
			$term->NS( $ns );
			$term->Code( $code );
			$term->Kind( kTYPE_TRAIT, TRUE );
			$term->Name( 'Grain weight', kDEFAULT_LANGUAGE );
			$term->Definition( 'Grain weight', kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		
		} $_SESSION[ 'TERMS' ][ $code ] = $term;
	 
		/*================================================================================
		 *	Methods																		 *
		 *===============================================================================*/

		//
		// Load term.
		//
		$code = 'Method-1';
		$term = new COntologyTerm(
			$theContainer, COntologyTerm::HashIndex(
				$code ) );
		if( ! $term->Persistent() )
		{
			//
			// Create term.
			//
			$term->NS( $ns );
			$term->Code( $code );
			$term->Kind( kTYPE_TRAIT, TRUE );
			$term->Name( 'Method 1', kDEFAULT_LANGUAGE );
			$term->Definition( 'Method 1', kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		
		} $_SESSION[ 'TERMS' ][ $code ] = $term;

		//
		// Load term.
		//
		$code = 'Method-2';
		$term = new COntologyTerm(
			$theContainer, COntologyTerm::HashIndex(
				$code ) );
		if( ! $term->Persistent() )
		{
			//
			// Create term.
			//
			$term->NS( $ns );
			$term->Code( $code );
			$term->Kind( kTYPE_TRAIT, TRUE );
			$term->Name( 'Method 2', kDEFAULT_LANGUAGE );
			$term->Definition( 'Method 2', kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		
		} $_SESSION[ 'TERMS' ][ $code ] = $term;

		//
		// Load term.
		//
		$code = 'Method-3';
		$term = new COntologyTerm(
			$theContainer, COntologyTerm::HashIndex(
				$code ) );
		if( ! $term->Persistent() )
		{
			//
			// Create term.
			//
			$term->NS( $ns );
			$term->Code( $code );
			$term->Kind( kTYPE_TRAIT, TRUE );
			$term->Name( 'Method 3', kDEFAULT_LANGUAGE );
			$term->Definition( 'Method 3', kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		
		} $_SESSION[ 'TERMS' ][ $code ] = $term;
	 
		/*================================================================================
		 *	Scales																		 *
		 *===============================================================================*/

		//
		// Load term.
		//
		$code = 'g';
		$term = new COntologyTerm(
			$theContainer, COntologyTerm::HashIndex(
				$code ) );
		if( ! $term->Persistent() )
		{
			//
			// Create term.
			//
			$term->NS( $ns );
			$term->Code( $code );
			$term->Kind( kTYPE_MEASURE, TRUE );
			$term->Type( kTYPE_FLOAT, TRUE );
			$term->Name( 'Grams', kDEFAULT_LANGUAGE );
			$term->Definition( 'Grams', kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		
		} $_SESSION[ 'TERMS' ][ $code ] = $term;

		//
		// Load term.
		//
		$code = 'cm';
		$term = new COntologyTerm(
			$theContainer, COntologyTerm::HashIndex(
				$code ) );
		if( ! $term->Persistent() )
		{
			//
			// Create term.
			//
			$term->NS( $ns );
			$term->Code( $code );
			$term->Kind( kTYPE_MEASURE, TRUE );
			$term->Type( kTYPE_FLOAT, TRUE );
			$term->Name( 'Centimeters', kDEFAULT_LANGUAGE );
			$term->Definition( 'Centimeters', kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		
		} $_SESSION[ 'TERMS' ][ $code ] = $term;

		//
		// Load term.
		//
		$code = 'Heights';
		$term = new COntologyTerm(
			$theContainer, COntologyTerm::HashIndex(
				$code ) );
		if( ! $term->Persistent() )
		{
			//
			// Create term.
			//
			$term->NS( $ns );
			$term->Code( $code );
			$term->Kind( kTYPE_MEASURE, TRUE );
			$term->Type( kTYPE_ENUM, TRUE );
			$term->Name( 'Heights', kDEFAULT_LANGUAGE );
			$term->Definition( 'Height enumeration', kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		
		} $_SESSION[ 'TERMS' ][ $code ] = $term;

		//
		// Load term.
		//
		$code = 'A';
		$term = new COntologyTerm(
			$theContainer, COntologyTerm::HashIndex(
				$code ) );
		if( ! $term->Persistent() )
		{
			//
			// Create term.
			//
			$term->NS( $_SESSION[ 'TERMS' ][ 'Heights' ] );
			$term->Code( $code );
			$term->Kind( kTYPE_ENUMERATION, TRUE );
			$term->Type( kTYPE_ENUM, TRUE );
			$term->Name( '< 10', kDEFAULT_LANGUAGE );
			$term->Definition( 'Less than 10 centimeters', kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		
		} $_SESSION[ 'TERMS' ][ $code ] = $term;

		//
		// Load term.
		//
		$code = 'B';
		$term = new COntologyTerm(
			$theContainer, COntologyTerm::HashIndex(
				$code ) );
		if( ! $term->Persistent() )
		{
			//
			// Create term.
			//
			$term->NS( $_SESSION[ 'TERMS' ][ 'Heights' ] );
			$term->Code( $code );
			$term->Kind( kTYPE_ENUMERATION, TRUE );
			$term->Type( kTYPE_ENUM, TRUE );
			$term->Name( '10-20', kDEFAULT_LANGUAGE );
			$term->Definition( 'From 10 to 20 centimeters', kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		
		} $_SESSION[ 'TERMS' ][ $code ] = $term;

		//
		// Load term.
		//
		$code = 'C';
		$term = new COntologyTerm(
			$theContainer, COntologyTerm::HashIndex(
				$code ) );
		if( ! $term->Persistent() )
		{
			//
			// Create term.
			//
			$term->NS( $_SESSION[ 'TERMS' ][ 'Heights' ] );
			$term->Code( $code );
			$term->Kind( kTYPE_ENUMERATION, TRUE );
			$term->Type( kTYPE_ENUM, TRUE );
			$term->Name( '> 20', kDEFAULT_LANGUAGE );
			$term->Definition( 'More than 20 centimeters', kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		
		} $_SESSION[ 'TERMS' ][ $code ] = $term;

		//
		// Load term.
		//
		$code = 'Weights';
		$term = new COntologyTerm(
			$theContainer, COntologyTerm::HashIndex(
				$code ) );
		if( ! $term->Persistent() )
		{
			//
			// Create term.
			//
			$term->NS( $ns );
			$term->Code( $code );
			$term->Kind( kTYPE_MEASURE, TRUE );
			$term->Type( kTYPE_ENUM, TRUE );
			$term->Name( 'Weights', kDEFAULT_LANGUAGE );
			$term->Definition( 'Weight enumeration', kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		
		} $_SESSION[ 'TERMS' ][ $code ] = $term;

		//
		// Load term.
		//
		$code = '1';
		$term = new COntologyTerm(
			$theContainer, COntologyTerm::HashIndex(
				$code ) );
		if( ! $term->Persistent() )
		{
			//
			// Create term.
			//
			$term->NS( $_SESSION[ 'TERMS' ][ 'Weights' ] );
			$term->Code( $code );
			$term->Kind( kTYPE_ENUMERATION, TRUE );
			$term->Type( kTYPE_ENUM, TRUE );
			$term->Name( '< 10', kDEFAULT_LANGUAGE );
			$term->Definition( 'Less than 10 grams', kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		
		} $_SESSION[ 'TERMS' ][ $code ] = $term;

		//
		// Load term.
		//
		$code = '2';
		$term = new COntologyTerm(
			$theContainer, COntologyTerm::HashIndex(
				$code ) );
		if( ! $term->Persistent() )
		{
			//
			// Create term.
			//
			$term->NS( $_SESSION[ 'TERMS' ][ 'Weights' ] );
			$term->Code( $code );
			$term->Kind( kTYPE_ENUMERATION, TRUE );
			$term->Type( kTYPE_ENUM, TRUE );
			$term->Name( '10-20', kDEFAULT_LANGUAGE );
			$term->Definition( 'From 10 to 20 grams', kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		
		} $_SESSION[ 'TERMS' ][ $code ] = $term;

		//
		// Load term.
		//
		$code = '3';
		$term = new COntologyTerm(
			$theContainer, COntologyTerm::HashIndex(
				$code ) );
		if( ! $term->Persistent() )
		{
			//
			// Create term.
			//
			$term->NS( $_SESSION[ 'TERMS' ][ 'Weights' ] );
			$term->Code( $code );
			$term->Kind( kTYPE_ENUMERATION, TRUE );
			$term->Type( kTYPE_ENUM, TRUE );
			$term->Name( '> 20', kDEFAULT_LANGUAGE );
			$term->Definition( 'More than 20 grams', kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		
		} $_SESSION[ 'TERMS' ][ $code ] = $term;

		//
		// Load term.
		//
		$code = 'Content';
		$term = new COntologyTerm(
			$theContainer, COntologyTerm::HashIndex(
				$code ) );
		if( ! $term->Persistent() )
		{
			//
			// Create term.
			//
			$term->NS( $ns );
			$term->Code( $code );
			$term->Kind( kTYPE_MEASURE, TRUE );
			$term->Type( kTYPE_ENUM, TRUE );
			$term->Name( 'Content', kDEFAULT_LANGUAGE );
			$term->Definition( 'Content quantity enumeration', kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		
		} $_SESSION[ 'TERMS' ][ $code ] = $term;

		//
		// Load term.
		//
		$code = 'L';
		$term = new COntologyTerm(
			$theContainer, COntologyTerm::HashIndex(
				$code ) );
		if( ! $term->Persistent() )
		{
			//
			// Create term.
			//
			$term->NS( $_SESSION[ 'TERMS' ][ 'Content' ] );
			$term->Code( $code );
			$term->Kind( kTYPE_ENUMERATION, TRUE );
			$term->Type( kTYPE_ENUM, TRUE );
			$term->Name( 'Low', kDEFAULT_LANGUAGE );
			$term->Definition( 'Low content', kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		
		} $_SESSION[ 'TERMS' ][ $code ] = $term;

		//
		// Load term.
		//
		$code = 'M';
		$term = new COntologyTerm(
			$theContainer, COntologyTerm::HashIndex(
				$code ) );
		if( ! $term->Persistent() )
		{
			//
			// Create term.
			//
			$term->NS( $_SESSION[ 'TERMS' ][ 'Content' ] );
			$term->Code( $code );
			$term->Kind( kTYPE_ENUMERATION, TRUE );
			$term->Type( kTYPE_ENUM, TRUE );
			$term->Name( 'Medium', kDEFAULT_LANGUAGE );
			$term->Definition( 'Medium content', kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		
		} $_SESSION[ 'TERMS' ][ $code ] = $term;

		//
		// Load term.
		//
		$code = 'H';
		$term = new COntologyTerm(
			$theContainer, COntologyTerm::HashIndex(
				$code ) );
		if( ! $term->Persistent() )
		{
			//
			// Create term.
			//
			$term->NS( $_SESSION[ 'TERMS' ][ 'Content' ] );
			$term->Code( $code );
			$term->Kind( kTYPE_ENUMERATION, TRUE );
			$term->Type( kTYPE_ENUM, TRUE );
			$term->Name( 'High', kDEFAULT_LANGUAGE );
			$term->Definition( 'High content', kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		
		} $_SESSION[ 'TERMS' ][ $code ] = $term;
		
	} // LoadTerms.

	 
	/*===================================================================================
	 *	LoadDescriptors																	*
	 *==================================================================================*/

	/**
	 * Load descriptors.
	 *
	 * This function will load the MCPD ontology.
	 *
	 * If the last parameter is <i>TRUE</i>, the function will display the name of the
	 * created terms.
	 *
	 * @param CContainer			$theContainer		Collection.
	 * @param boolean				$doDisplay			Display created terms.
	 *
	 * @access protected
	 */
	function LoadDescriptors( CContainer $theContainer, $doDisplay = TRUE )
	{
		//
		// Init local storage.
		//
		$container = array( kTAG_TERM => $theContainer,
							kTAG_NODE => $_SESSION[ kSESSION_NEO4J ] );
		$node_idx_cont = array( kTAG_TERM => $theContainer->Database()->selectCollection(
												kDEFAULT_CNT_NODES ),
								kTAG_NODE => $_SESSION[ kSESSION_NEO4J ] );
		
		//
		// Inform.
		//
		if( $doDisplay )
		{
			echo( "\n".__FUNCTION__."\n" );
			echo( "------------------\n" );
		}
		
		//
		// IS-A.
		//
		$is_a
			= new COntologyTerm
						( $theContainer, COntologyTerm::HashIndex( kPRED_IS_A ) );
		
		//
		// ENUM-OF.
		//
		$enum_of
			= new COntologyTerm
						( $theContainer, COntologyTerm::HashIndex( kPRED_ENUM_OF ) );
		
		//
		// METHOD-OF.
		//
		$method_of
			= new COntologyTerm
						( $theContainer, COntologyTerm::HashIndex( kPRED_METHOD_OF ) );
		
		//
		// SCALE-OF.
		//
		$scale_of
			= new COntologyTerm
						( $theContainer, COntologyTerm::HashIndex( kPRED_SCALE_OF ) );
		
		//
		// TREATMENT-OF.
		//
		$treatment_of
			= new COntologyTerm
						( $theContainer, COntologyTerm::HashIndex( 'TEST:TREATMENT-OF' ) );
	 
		/*================================================================================
		 *	TEST																		 *
		 *===============================================================================*/

		//
		// Set term code.
		//
		$code = 'TEST';

		//
		// Load term.
		//
		$ns = $term = $_SESSION[ 'TERMS' ][ $code ];

		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_ROOT, TRUE );
		$node->Commit( $container );

		//
		// Save data.
		//
		$root_node = $_SESSION[ 'NODES' ][ $code ] = $node;
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	Protein-Content																 *
		 *===============================================================================*/

		//
		// Set term code.
		//
		$code = 'Protein-Content';

		//
		// Load term.
		//
		$term = $_SESSION[ 'TERMS' ][ $code ];

		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $root_node );
		$edge->Commit( $container );

		//
		// Save data.
		//
		$parent_node = $_SESSION[ 'NODES' ][ $code ] = $node;
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	Method 1																	 *
		 *===============================================================================*/

		//
		// Set term code.
		//
		$code = 'Method-1';

		//
		// Load term.
		//
		$term = $_SESSION[ 'TERMS' ][ $code ];

		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_METHOD, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $method_of, $parent_node );
		$edge->Commit( $container );

		//
		// Save data.
		//
		$nodes = Array();
		array_push( $nodes, $node );
		$parent_node = $node;
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	WATER-1000																	 *
		 *===============================================================================*/

		//
		// Set term code.
		//
		$code = 'WATER-1000';

		//
		// Load term.
		//
		$term = $_SESSION[ 'TERMS' ][ $code ];

		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $treatment_of, $parent_node );
		$edge->Commit( $container );

		//
		// Save data.
		//
		array_push( $nodes, $node );
		$parent_node = $node;
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	FERTILIZER-A																 *
		 *===============================================================================*/

		//
		// Set term code.
		//
		$code = 'FERTILIZER-A';

		//
		// Load term.
		//
		$term = $_SESSION[ 'TERMS' ][ $code ];

		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $treatment_of, $parent_node );
		$edge->Commit( $container );

		//
		// Save data.
		//
		array_push( $nodes, $node );
		$parent_node = $node;
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	FERTILIZER-100																 *
		 *===============================================================================*/

		//
		// Set term code.
		//
		$code = 'FERTILIZER-100';

		//
		// Load term.
		//
		$term = $_SESSION[ 'TERMS' ][ $code ];

		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $treatment_of, $parent_node );
		$edge->Commit( $container );

		//
		// Save data.
		//
		array_push( $nodes, $node );
		$parent_node = $node;
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	Content																		 *
		 *===============================================================================*/

		//
		// Set term code.
		//
		$code = 'Content';

		//
		// Load term.
		//
		$term = $_SESSION[ 'TERMS' ][ $code ];

		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Type( kTYPE_ENUM );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $scale_of, $parent_node );
		$edge->Commit( $container );

		//
		// Save data.
		//
		$parent_node = $_SESSION[ 'NODES' ][ $code ] = $node;
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	Low																			 *
		 *===============================================================================*/

		//
		// Set term code.
		//
		$code = 'L';

		//
		// Load term.
		//
		$term = $_SESSION[ 'TERMS' ][ $code ];

		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Type( kTYPE_ENUM );
		$node->Kind( kTYPE_ENUMERATION, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $enum_of, $parent_node );
		$edge->Commit( $container );
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	Medium																		 *
		 *===============================================================================*/

		//
		// Set term code.
		//
		$code = 'M';

		//
		// Load term.
		//
		$term = $_SESSION[ 'TERMS' ][ $code ];

		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Type( kTYPE_ENUM );
		$node->Kind( kTYPE_ENUMERATION, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $enum_of, $parent_node );
		$edge->Commit( $container );
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	High																		 *
		 *===============================================================================*/

		//
		// Set term code.
		//
		$code = 'H';

		//
		// Load term.
		//
		$term = $_SESSION[ 'TERMS' ][ $code ];

		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Type( kTYPE_ENUM );
		$node->Kind( kTYPE_ENUMERATION, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $enum_of, $parent_node );
		$edge->Commit( $container );
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	Method 2																	 *
		 *===============================================================================*/

		//
		// Get parent node.
		//
		$parent_node = $_SESSION[ 'NODES' ][ 'Protein-Content' ];

		//
		// Set term code.
		//
		$code = 'Method-2';

		//
		// Load term.
		//
		$term = $_SESSION[ 'TERMS' ][ $code ];

		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_METHOD, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $method_of, $parent_node );
		$edge->Commit( $container );

		//
		// Save data.
		//
		$nodes = Array();
		array_push( $nodes, $node );
		$parent_node = $node;
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	WATER-1500																	 *
		 *===============================================================================*/

		//
		// Set term code.
		//
		$code = 'WATER-1500';

		//
		// Load term.
		//
		$term = $_SESSION[ 'TERMS' ][ $code ];

		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $treatment_of, $parent_node );
		$edge->Commit( $container );

		//
		// Save data.
		//
		array_push( $nodes, $node );
		$parent_node = $node;
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	FERTILIZER-B																 *
		 *===============================================================================*/

		//
		// Set term code.
		//
		$code = 'FERTILIZER-B';

		//
		// Load term.
		//
		$term = $_SESSION[ 'TERMS' ][ $code ];

		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $treatment_of, $parent_node );
		$edge->Commit( $container );

		//
		// Save data.
		//
		array_push( $nodes, $node );
		$parent_node = $node;
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	FERTILIZER-150																 *
		 *===============================================================================*/

		//
		// Set term code.
		//
		$code = 'FERTILIZER-150';

		//
		// Load term.
		//
		$term = $_SESSION[ 'TERMS' ][ $code ];

		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $treatment_of, $parent_node );
		$edge->Commit( $container );

		//
		// Save data.
		//
		array_push( $nodes, $node );
		$parent_node = $node;
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	Content																		 *
		 *===============================================================================*/

		//
		// Set term code.
		//
		$code = 'Content';

		//
		// Get node.
		//
		$node = $_SESSION[ 'NODES' ][ $code ];
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $scale_of, $parent_node );
		$edge->Commit( $container );
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	Plant-Height																 *
		 *===============================================================================*/

		//
		// Set term code.
		//
		$code = 'Plant-Height';

		//
		// Load term.
		//
		$term = $_SESSION[ 'TERMS' ][ $code ];

		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $root_node );
		$edge->Commit( $container );

		//
		// Save data.
		//
		$parent_node = $_SESSION[ 'NODES' ][ $code ] = $node;
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	Method 1																	 *
		 *===============================================================================*/

		//
		// Set term code.
		//
		$code = 'Method-1';

		//
		// Load term.
		//
		$term = $_SESSION[ 'TERMS' ][ $code ];

		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_METHOD, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $method_of, $parent_node );
		$edge->Commit( $container );

		//
		// Save data.
		//
		$nodes = Array();
		array_push( $nodes, $node );
		$parent_node = $node;
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	WATER-1000																	 *
		 *===============================================================================*/

		//
		// Set term code.
		//
		$code = 'WATER-1000';

		//
		// Load term.
		//
		$term = $_SESSION[ 'TERMS' ][ $code ];

		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $treatment_of, $parent_node );
		$edge->Commit( $container );

		//
		// Save data.
		//
		array_push( $nodes, $node );
		$parent_node = $node;
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	FERTILIZER-A																 *
		 *===============================================================================*/

		//
		// Set term code.
		//
		$code = 'FERTILIZER-A';

		//
		// Load term.
		//
		$term = $_SESSION[ 'TERMS' ][ $code ];

		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $treatment_of, $parent_node );
		$edge->Commit( $container );

		//
		// Save data.
		//
		array_push( $nodes, $node );
		$parent_node = $node;
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	FERTILIZER-100																 *
		 *===============================================================================*/

		//
		// Set term code.
		//
		$code = 'FERTILIZER-100';

		//
		// Load term.
		//
		$term = $_SESSION[ 'TERMS' ][ $code ];

		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $treatment_of, $parent_node );
		$edge->Commit( $container );

		//
		// Save data.
		//
		array_push( $nodes, $node );
		$parent_node = $node;
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	Heights																		 *
		 *===============================================================================*/

		//
		// Set term code.
		//
		$code = 'Heights';

		//
		// Load term.
		//
		$term = $_SESSION[ 'TERMS' ][ $code ];

		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Type( kTYPE_ENUM );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $scale_of, $parent_node );
		$edge->Commit( $container );

		//
		// Save data.
		//
		$parent_node = $_SESSION[ 'NODES' ][ $code ] = $node;
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	A																			 *
		 *===============================================================================*/

		//
		// Set term code.
		//
		$code = 'A';

		//
		// Load term.
		//
		$term = $_SESSION[ 'TERMS' ][ $code ];

		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Type( kTYPE_ENUM );
		$node->Kind( kTYPE_ENUMERATION, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $enum_of, $parent_node );
		$edge->Commit( $container );
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	B																			 *
		 *===============================================================================*/

		//
		// Set term code.
		//
		$code = 'B';

		//
		// Load term.
		//
		$term = $_SESSION[ 'TERMS' ][ $code ];

		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Type( kTYPE_ENUM );
		$node->Kind( kTYPE_ENUMERATION, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $enum_of, $parent_node );
		$edge->Commit( $container );
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	C																			 *
		 *===============================================================================*/

		//
		// Set term code.
		//
		$code = 'C';

		//
		// Load term.
		//
		$term = $_SESSION[ 'TERMS' ][ $code ];

		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Type( kTYPE_ENUM );
		$node->Kind( kTYPE_ENUMERATION, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $enum_of, $parent_node );
		$edge->Commit( $container );
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	cm																			 *
		 *===============================================================================*/

		//
		// Load parent node.
		//
		$parent_node = array_pop( $nodes );
		
		//
		// Set term code.
		//
		$code = 'cm';

		//
		// Load term.
		//
		$term = $_SESSION[ 'TERMS' ][ $code ];

		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Type( kTYPE_FLOAT );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $scale_of, $parent_node );
		$edge->Commit( $container );

		//
		// Save data.
		//
		$parent_node = $_SESSION[ 'NODES' ][ $code ] = $node;
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
		
	} // LoadDescriptors.


?>
