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
 *										Sorghum.php										*
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
 * Run-time environment.
 *
 * This include file contains the run-time definitions.
 */
require_once( "/Library/WebServer/Library/wrapper/local/environment.inc.php" );

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
		// Save default namespace.
		//
		$namespace = new COntologyTerm( $theContainer, COntologyTerm::HashIndex( '' ) );
		
		//
		// Load root term and node.
		//
		$term = new COntologyTerm(
			$theContainer, COntologyTerm::HashIndex(
				'CO_324' ) );
		if( ! $term->Persistent() )
		{
			//
			// Create term.
			//
			$term->Code( 'CO_324' );
			$term->Kind( kTYPE_NAMESPACE, TRUE );
			$term->Version( '03-05-2012' );
			$term->Name( 'Sorghum', kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		}

		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_ROOT, TRUE );
		$node->Domain( kDOMAIN_ACCESSION, TRUE );
		$node->Category( kCATEGORY_CHAR, TRUE );
		$node->Commit( $container );

		//
		// Save data.
		//
		$root_term = $_SESSION[ 'TERMS' ][ 'CO_324' ] = $term;
		$root_node = $_SESSION[ 'NODES' ][ 'CO_324' ] = $node;
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	Agronomic																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					':Agronomic' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $namespace );
			$term->Code( 'Agronomic' );
			$term->Name( 'Agronomic trait', kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $root_node );
		$edge->Commit( $container );

		//
		// Save data.
		//
		$parent_term = $_SESSION[ 'TERMS' ][ 'Agronomic' ] = $term;
		$parent_node = $_SESSION[ 'NODES' ][ 'Agronomic' ] = $node;
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	Grain anthracnose															 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					'CO_324:0000001' ) );
		if( ! $term->Persistent() )
		{
			//
			// Create term.
			//
			$term->NS( $root_term );
			$term->Code( '0000001' );
			$term->Name( 'Grain anthracnose', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Anthracnose disease severity on grain caused by Colletotrichum graminicola.',
			  kDEFAULT_LANGUAGE );
			$term->Description
			( 'Trials and nurseries.',
			  kDEFAULT_LANGUAGE );
			$term->Synonym( 'GANTH', kTYPE_EXACT, TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $parent_node );
		$edge->Commit( $container );

		//
		// Save data.
		//
		$parent_term = $_SESSION[ 'TERMS' ][ 'HoldingInstitute' ] = $term;
		$parent_node = $_SESSION[ 'NODES' ][ 'HoldingInstitute' ] = $node;
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	record																		 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					'CO_324:0000001:record' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $parent_term );
			$term->Code( 'record' );
			$term->Name( 'Grain Anthracnose Score', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Record disease severity (based on 1 to 5 or 1-9 scale) at maturity on a '
			 .'sample of 5 panicles or more.',
			  kDEFAULT_LANGUAGE );
			$term->Description
			( 'Maturity.',
			  kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
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
		$parent_term = $term;
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
		 *	score-1-5																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					'CO_324:0000001:record:score-1-5' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $root_term );
			$term->Code( 'score-1-5' );
			$term->Name( 'Score (1-5)', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Percentage of grains damaged on a panicle, 5 point score.',
			  kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
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
		$parent_term = $term;
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
		 *	1																			 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					'CO_324:0000001:record:score-1-5:1' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $root_term );
			$term->Code( '1' );
			$term->Name( 'Highly Resistant', kDEFAULT_LANGUAGE );
			$term->Definition
			( '0 % of grains damaged on a panicle, highly Resistant (HR).',
			  kDEFAULT_LANGUAGE );
			$term->Synonym( 'HR', kTYPE_BROAD, TRUE );
			$term->Kind( kTYPE_ENUMERATION, TRUE );
			$term->Type( kTYPE_ENUM );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_ENUMERATION, TRUE );
		$node->Type( kTYPE_ENUM );
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
		 *	2																			 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					'CO_324:0000001:record:score-1-5:2' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $root_term );
			$term->Code( '2' );
			$term->Name( 'Resistant', kDEFAULT_LANGUAGE );
			$term->Definition
			( '1-10% of grains damaged on a panicle, Resistant (R).',
			  kDEFAULT_LANGUAGE );
			$term->Synonym( 'R', kTYPE_BROAD, TRUE );
			$term->Kind( kTYPE_ENUMERATION, TRUE );
			$term->Type( kTYPE_ENUM );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_ENUMERATION, TRUE );
		$node->Type( kTYPE_ENUM );
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
		 *	3																			 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					'CO_324:0000001:record:score-1-5:3' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $root_term );
			$term->Code( '3' );
			$term->Name( 'Moderately Resistant', kDEFAULT_LANGUAGE );
			$term->Definition
			( '11-25% of grains damaged on a panicle, Moderately Resistant (MR).',
			  kDEFAULT_LANGUAGE );
			$term->Synonym( 'MR', kTYPE_BROAD, TRUE );
			$term->Kind( kTYPE_ENUMERATION, TRUE );
			$term->Type( kTYPE_ENUM );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_ENUMERATION, TRUE );
		$node->Type( kTYPE_ENUM );
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
		 *	4																			 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					'CO_324:0000001:record:score-1-5:4' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $root_term );
			$term->Code( '4' );
			$term->Name( 'Susceptible', kDEFAULT_LANGUAGE );
			$term->Definition
			( '26-50% of grains damaged on a panicle, Susceptible (S).',
			  kDEFAULT_LANGUAGE );
			$term->Synonym( 'S', kTYPE_BROAD, TRUE );
			$term->Kind( kTYPE_ENUMERATION, TRUE );
			$term->Type( kTYPE_ENUM );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_ENUMERATION, TRUE );
		$node->Type( kTYPE_ENUM );
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
		 *	5																			 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					'CO_324:0000001:record:score-1-5:5' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $root_term );
			$term->Code( '5' );
			$term->Name( 'Highly Susceptible', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'More than 50% of grains damaged on a panicle, Highly Susceptible (HS).',
			  kDEFAULT_LANGUAGE );
			$term->Synonym( 'HS', kTYPE_BROAD, TRUE );
			$term->Kind( kTYPE_ENUMERATION, TRUE );
			$term->Type( kTYPE_ENUM );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_ENUMERATION, TRUE );
		$node->Type( kTYPE_ENUM );
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
		 *	Foliar anthracnose															 *
		 *===============================================================================*/

		//
		// Get parent term and node.
		//
		$parent_node = $_SESSION[ 'NODES' ][ 'Agronomic' ];
		
		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					'CO_324:0000002' ) );
		if( ! $term->Persistent() )
		{
			//
			// Create term.
			//
			$term->NS( $root_term );
			$term->Code( '0000002' );
			$term->Name( 'Foliar anthracnose', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Anthracnose disease severity on leaves caused by Colletotrichum graminicola.',
			  kDEFAULT_LANGUAGE );
			$term->Description
			( 'Trials and nurseries.',
			  kDEFAULT_LANGUAGE );
			$term->Synonym( 'FANTH', kTYPE_EXACT, TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $parent_node );
		$edge->Commit( $container );

		//
		// Save data.
		//
		$parent_term = $_SESSION[ 'TERMS' ][ 'HoldingInstitute' ] = $term;
		$parent_node = $_SESSION[ 'NODES' ][ 'HoldingInstitute' ] = $node;
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	record-disease																 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					'CO_324:0000002:record-disease' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $parent_term );
			$term->Code( 'record-disease' );
			$term->Name( 'Foliar Anthracnose Score', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Record disease reaction type (HR, R, MR, S or HS) and disease severity '
			 .'(based on 1 to 5 scale) at the soft-dough stage on the top four leaves '
			 .'that contribute most to grain development or on all plant.',
			  kDEFAULT_LANGUAGE );
			$term->Description
			( 'Maturity.',
			  kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
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
		$parent_term = $_SESSION[ 'TERMS' ][ 'HoldingInstitute' ] = $term;
		$parent_node = $_SESSION[ 'NODES' ][ 'HoldingInstitute' ] = $node;
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	score-1-9																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					'CO_324:0000002:record-disease:score-1-9' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $root_term );
			$term->Code( 'score-1-9' );
			$term->Name( 'Score (1-9)', kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
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
		$parent_term = $_SESSION[ 'TERMS' ][ 'HoldingInstitute' ] = $term;
		$parent_node = $_SESSION[ 'NODES' ][ 'HoldingInstitute' ] = $node;
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	1																			 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					'CO_324:0000002:record-disease:score-1-9:1' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $root_term );
			$term->Code( '1' );
			$term->Name( 'Highly Resistant', kDEFAULT_LANGUAGE );
			$term->Definition
			( '< 1% leaf area with mild yellow flecks (HR).',
			  kDEFAULT_LANGUAGE );
			$term->Synonym( 'HR', kTYPE_BROAD, TRUE );
			$term->Kind( kTYPE_ENUMERATION, TRUE );
			$term->Type( kTYPE_ENUM );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_ENUMERATION, TRUE );
		$node->Type( kTYPE_ENUM );
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
		 *	2																			 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					'CO_324:0000002:record-disease:score-1-9:2' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $root_term );
			$term->Code( '2' );
			$term->Name( 'Highly Resistant', kDEFAULT_LANGUAGE );
			$term->Definition
			( '1-5% leaf area covered with hypersensitive small lesions.',
			  kDEFAULT_LANGUAGE );
			$term->Kind( kTYPE_ENUMERATION, TRUE );
			$term->Type( kTYPE_ENUM );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_ENUMERATION, TRUE );
		$node->Type( kTYPE_ENUM );
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
		 *	3																			 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					'CO_324:0000002:record-disease:score-1-9:3' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $root_term );
			$term->Code( '3' );
			$term->Name( 'Small lesions', kDEFAULT_LANGUAGE );
			$term->Definition
			( '6-10% leaf area covered with hypersensitive small lesions.',
			  kDEFAULT_LANGUAGE );
			$term->Kind( kTYPE_ENUMERATION, TRUE );
			$term->Type( kTYPE_ENUM );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_ENUMERATION, TRUE );
		$node->Type( kTYPE_ENUM );
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
		 *	4																			 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					'CO_324:0000002:record-disease:score-1-9:4' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $root_term );
			$term->Code( '4' );
			$term->Name( 'Small necrotic lesions', kDEFAULT_LANGUAGE );
			$term->Definition
			( '11-20% leaf area covered with small necrotic lesions (MR).',
			  kDEFAULT_LANGUAGE );
			$term->Synonym( 'MR', kTYPE_BROAD, TRUE );
			$term->Kind( kTYPE_ENUMERATION, TRUE );
			$term->Type( kTYPE_ENUM );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_ENUMERATION, TRUE );
		$node->Type( kTYPE_ENUM );
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
		 *	5																			 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					'CO_324:0000002:record-disease:score-1-9:5' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $root_term );
			$term->Code( '5' );
			$term->Name( 'Small necrotic coalescing lesions', kDEFAULT_LANGUAGE );
			$term->Definition
			( '21-30% leaf area covered with small necrotic coalescing lesions (MR).',
			  kDEFAULT_LANGUAGE );
			$term->Synonym( 'MR', kTYPE_BROAD, TRUE );
			$term->Kind( kTYPE_ENUMERATION, TRUE );
			$term->Type( kTYPE_ENUM );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_ENUMERATION, TRUE );
		$node->Type( kTYPE_ENUM );
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
		 *	6																			 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					'CO_324:0000002:record-disease:score-1-9:6' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $root_term );
			$term->Code( '6' );
			$term->Name( 'Large coalescing necrotic lesions', kDEFAULT_LANGUAGE );
			$term->Definition
			( '31-40% leaf area covered with large coalescing necrotic lesions (S).',
			  kDEFAULT_LANGUAGE );
			$term->Synonym( 'S', kTYPE_BROAD, TRUE );
			$term->Kind( kTYPE_ENUMERATION, TRUE );
			$term->Type( kTYPE_ENUM );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_ENUMERATION, TRUE );
		$node->Type( kTYPE_ENUM );
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
		 *	8																			 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					'CO_324:0000002:record-disease:score-1-9:8' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $root_term );
			$term->Code( '8' );
			$term->Name( 'Large coalescing necrotic lesions', kDEFAULT_LANGUAGE );
			$term->Definition
			( '51-75% leaf area covered with large coalescing necrotic lesions (HS).',
			  kDEFAULT_LANGUAGE );
			$term->Synonym( 'HS', kTYPE_BROAD, TRUE );
			$term->Kind( kTYPE_ENUMERATION, TRUE );
			$term->Type( kTYPE_ENUM );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_ENUMERATION, TRUE );
		$node->Type( kTYPE_ENUM );
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
		
	} // LoadDescriptors.


?>
