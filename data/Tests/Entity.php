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
 *										Entity.php										*
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
		// PART-OF.
		//
		$component_of
			= new COntologyTerm
						( $theContainer, COntologyTerm::HashIndex( kPRED_COMPONENT_OF ) );
		
		/*================================================================================
		 *	ENTITY																		 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term = new COntologyTerm(
			$theContainer, COntologyTerm::HashIndex(
				kTAG_ENTITY ) );

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
		$root_term = $_SESSION[ 'TERMS' ][ kTAG_ENTITY ] = $term;
		$root_node = $_SESSION[ 'NODES' ][ kTAG_ENTITY ] = $node;
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	CODE																		 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					kTAG_CODE ) );
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Type( kTYPE_STRING );
		$node->Cardinality( kCARD_1 );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $root_node );
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
		 *	NAME																		 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					kTAG_NAME ) );
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Type( kTYPE_STRING );
		$node->Cardinality( kCARD_1 );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $root_node );
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
		 *	FAO:INST:TYPE																 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					'FAO:INST:TYPE' ) );
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Type( kTYPE_STRING );
		$node->Cardinality( kCARD_ANY );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $root_node );
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
		 *	KIND																		 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					kTAG_KIND ) );
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Type( kTYPE_ENUM );
		$node->Cardinality( kCARD_ANY );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $root_node );
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
		 *	PHONE																		 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					kOFFSET_PHONE ) );
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Type( kTYPE_TYPEDEF );
		$node->Cardinality( kCARD_ANY );
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
		$parent_term = $_SESSION[ 'TERMS' ][ kOFFSET_PHONE ] = $term;
		$parent_node = $_SESSION[ 'NODES' ][ kOFFSET_PHONE ] = $node;
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	PHONE-KIND																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					kTAG_KIND ) );
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Type( kTYPE_STRING );
		$node->Cardinality( kCARD_0_1 );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $component_of, $parent_node );
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
		 *	PHONE-DATA																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					kTAG_DATA ) );
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Type( kTYPE_STRING );
		$node->Cardinality( kCARD_1 );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $component_of, $parent_node );
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
		 *	FAX																			 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					kOFFSET_FAX ) );
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Type( kTYPE_TYPEDEF );
		$node->Cardinality( kCARD_ANY );
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
		$parent_term = $_SESSION[ 'TERMS' ][ kOFFSET_FAX ] = $term;
		$parent_node = $_SESSION[ 'NODES' ][ kOFFSET_FAX ] = $node;
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	FAX-KIND																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					kTAG_KIND ) );
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Type( kTYPE_STRING );
		$node->Cardinality( kCARD_0_1 );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $component_of, $parent_node );
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
		 *	FAX-DATA																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					kTAG_DATA ) );
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Type( kTYPE_STRING );
		$node->Cardinality( kCARD_1 );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $component_of, $parent_node );
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
		 *	EMAIL																		 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					kOFFSET_EMAIL ) );
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Type( kTYPE_STRING );
		$node->Cardinality( kCARD_1 );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $root_node );
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
		 *	MODIFIED																		 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					kTAG_MODIFIED ) );
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Type( kTYPE_STAMP );
		$node->Cardinality( kCARD_1 );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $root_node );
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
		 *	MAIL																		 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					kOFFSET_MAIL ) );
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Type( kTYPE_TYPEDEF );
		$node->Cardinality( kCARD_ANY );
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
		$parent_term = $_SESSION[ 'TERMS' ][ kOFFSET_MAIL ] = $term;
		$parent_node = $_SESSION[ 'NODES' ][ kOFFSET_MAIL ] = $node;
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	MAIL-KIND																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					kTAG_KIND ) );
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Type( kTYPE_STRING );
		$node->Cardinality( kCARD_0_1 );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $component_of, $parent_node );
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
		 *	MAIL-DATA																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					kTAG_DATA ) );
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Type( kTYPE_STRING );
		$node->Cardinality( kCARD_1 );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $component_of, $parent_node );
		$edge->Commit( $container );

		//
		// Save data.
		//
		$parent_term = $_SESSION[ 'TERMS' ][ kTAG_DATA ] = $term;
		$parent_node = $_SESSION[ 'NODES' ][ kTAG_DATA ] = $node;
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	MAIL-DATA-FULL (method)														 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					kOFFSET_FULL ) );
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Cardinality( kCARD_1 );
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
		$parent_term = $_SESSION[ 'TERMS' ][ kTAG_DATA.'.'.kOFFSET_FULL ] = $term;
		$parent_node = $_SESSION[ 'NODES' ][ kTAG_DATA.'.'.kOFFSET_FULL ] = $node;
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	MAIL-DATA-FULL																 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					kOFFSET_FULL ) );
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Type( kTYPE_STRING );
		$node->Cardinality( kCARD_1 );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Commit( $container );
		
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
		 *	MAIL-DATA-MAIL (method)														 *
		 *===============================================================================*/

		//
		// Get parent node.
		//
		$parent_node = $_SESSION[ 'NODES' ][ kTAG_DATA ];
		
		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					kOFFSET_MAIL ) );
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Cardinality( kCARD_1 );
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
		$parent_term = $_SESSION[ 'TERMS' ][ kTAG_DATA.'.'.kOFFSET_MAIL ] = $term;
		$parent_node = $_SESSION[ 'NODES' ][ kTAG_DATA.'.'.kOFFSET_MAIL ] = $node;
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	MAIL-PLACE																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					kOFFSET_PLACE ) );
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Type( kTYPE_STRING );
		$node->Cardinality( kCARD_0_1 );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $component_of, $parent_node );
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
		 *	MAIL-CARE																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					kOFFSET_CARE ) );
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Type( kTYPE_STRING );
		$node->Cardinality( kCARD_0_1 );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $component_of, $parent_node );
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
		 *	MAIL-STREET																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					kOFFSET_STREET ) );
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Type( kTYPE_STRING );
		$node->Cardinality( kCARD_0_1 );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $component_of, $parent_node );
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
		 *	MAIL-ZIP																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					kOFFSET_ZIP_CODE ) );
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Type( kTYPE_STRING );
		$node->Cardinality( kCARD_0_1 );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $component_of, $parent_node );
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
		 *	MAIL-CITY																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					kOFFSET_CITY ) );
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Type( kTYPE_STRING );
		$node->Cardinality( kCARD_0_1 );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $component_of, $parent_node );
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
		 *	MAIL-PROVINCE																 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					kOFFSET_PROVINCE ) );
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Type( kTYPE_STRING );
		$node->Cardinality( kCARD_0_1 );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $component_of, $parent_node );
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
		 *	MAIL-COUNTRY																 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					kOFFSET_COUNTRY ) );
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Type( kTYPE_STRING );
		$node->Cardinality( kCARD_1 );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $component_of, $parent_node );
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
