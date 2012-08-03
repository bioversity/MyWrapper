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
 *									TestTerms.php										*
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
		// Load root term and node.
		//
		$term = new COntologyTerm(
			$theContainer, COntologyTerm::HashIndex(
				'GERMPLASM' ) );
		if( ! $term->Persistent() )
		{
			//
			// Create term.
			//
			$term->Code( 'GERMPLASM' );
			$term->Kind( kTYPE_NAMESPACE, TRUE );
			$term->Domain( kDOMAIN_ACCESSION, TRUE );
			$term->Category( kCATEGORY_PASSPORT, TRUE );
			$term->Version( '10-06-2012' );
			$term->Name( 'Germplasm', kDEFAULT_LANGUAGE );
			$term->Definition( 'Germplsam metadata', kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		}

		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_ROOT, TRUE );
		$node->Domain( kDOMAIN_ACCESSION, TRUE );
		$node->Category( kCATEGORY_PASSPORT, TRUE );
		$node->Commit( $container );

		//
		// Save data.
		//
		$root_term = $_SESSION[ 'TERMS' ][ 'GERMPLASM' ] = $term;
		$root_node = $_SESSION[ 'NODES' ][ 'GERMPLASM' ] = $node;
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	GlobalUniqueIdentifier														 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					'GERMPLASM:GlobalUniqueIdentifier' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $root_term );
			$term->Code( 'GlobalUniqueIdentifier' );
			$term->Name( 'Global unique identifier', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'This element represents the accession identifier known to the world, this '
			 .'identifier must be persistent. Provide the identifier if you have it, if '
			 .'you do not, the system will create a persistent identifier for you. '
			 .'Among the many standards we will use the LSID (Life Sciences Identifier) '
			 .'that will be composed as follows:<ul><li>Authority: The FAO/WIEWS holding '
			 .'institute code (HoldingInstituteFAOCode).<li>Namespace: We will use the '
			 .'constant germplasm to indicate that the identifier applies to living '
			 .'material.<li>ObjectID: The catalogue number or accession number '
			 .'(AccessionNumber).<li>Version: The eventual sample identifier '
			 .'(SampleNumber).</ul>Note that if any of the above information changes, '
			 .'the identifier will also change, this means that the above data elements '
			 .'should be persistent:<ul><li>Authority: the FAO/WIEWS institutes database '
			 .'does not delete entries, when an institute changes or merges with another '
			 .'one, a new identifier will be assigned to the new entity and the old one '
			 .'will point to the new one.<li>ObjectID: If this information changes, it '
			 .'usually means that we are talking about another accession.<li>Version: '
			 .'In general, sample identifiers should not be re-­‐used within an '
			 .'accession.</ul>',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Examples( 'urn:lsid:CIV033:germplasm:WAB0020770', TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
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
		 *	Identification																 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					'GERMPLASM:Identification' ) );
		if( ! $term->Persistent() )
		{
			//
			// Create term.
			//
			$term->NS( $root_term );
			$term->Code( 'Identification' );
			$term->Name( 'Identification', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Germplasm identification metadata.',
			  kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Commit( $container );

		//
		// Save data.
		//
		$parent_term = $_SESSION[ 'TERMS' ][ 'HoldingInstitute' ] = $term;
		$parent_node = $_SESSION[ 'NODES' ][ 'HoldingInstitute' ] = $node;
		
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
		 *	LocalUniqueIdentifier														 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					'GERMPLASM:LocalUniqueIdentifier' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $root_term );
			$term->Code( 'LocalUniqueIdentifier' );
			$term->Name( 'Local unique identifier', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'This element should hold the unique identifier of the accession as known '
			 .'and provided by the holding institute or collection. This means that the '
			 .'value should uniquely identify the accession in the source database. '
			 .'This information will be used to identify accessions when receiving updates '
			 .'and will be returned in reports to allow the provider to match the original '
			 .'database records. This information is required. This data element can be '
			 .'either a string or a number, or a series of sub-­‐elements: in this last '
			 .'case these should be separated by a TAB character (“\t” or ASCII 9).',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Examples( '12487', TRUE );
			$term->Examples( 'WAB0020770', TRUE );
			$term->Examples( '123 \\t 5175', TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $parent_node );
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
		 *	AccessionNumber																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					'GERMPLASM:AccessionNumber' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $root_term );
			$term->Code( 'AccessionNumber' );
			$term->Name( 'Accession number', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'This element represents the catalogue number of the accession within its '
			 .'holding institute. This code should uniquely identify the accessionwithin '
			 .'its holding institute, in cases in which such codes are re-­‐used by '
			 .'different collections you should provide prefix or suffix it with an '
			 .'identifier that will make this code unique. This code will be used when '
			 .'requesting material, so given this information, the collection curator '
			 .'should be able to uniquely identify the germplasm.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Examples( '12487', TRUE );
			$term->Examples( 'TVSu-1027', TRUE );
			$term->Examples( 'WAB0020770', TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $parent_node );
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
		 *	SampleNumber																 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					'GERMPLASM:SampleNumber' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $root_term );
			$term->Code( 'SampleNumber' );
			$term->Name( 'Sample number', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'This value can be used whenever the germplasm refers to an accession '
			 .'sample; it is optional and should only be used if relevant.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $parent_node );
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
		 *	HoldingInstitute															 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					'GERMPLASM:HoldingInstitute' ) );
		if( ! $term->Persistent() )
		{
			//
			// Create term.
			//
			$term->NS( $root_term );
			$term->Code( 'HoldingInstitute' );
			$term->Name( 'Holding institute', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Institute in which the germplasm is held.',
			  kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Commit( $container );

		//
		// Save data.
		//
		$parent_term = $_SESSION[ 'TERMS' ][ 'HoldingInstitute' ] = $term;
		$parent_node = $_SESSION[ 'NODES' ][ 'HoldingInstitute' ] = $node;
		
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
		 *	FAOCode																		 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					'GERMPLASM:FAOCode' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $root_term );
			$term->Code( 'FAOCode' );
			$term->Name( 'FAO code', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'The FAO/WIEWS code of the holding institute, all CGIAR institutes and '
			 .'most gene banks are covered by this resource.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $parent_node );
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
		 *	HoldingCollection															 *
		 *===============================================================================*/

		//
		// Get parent node.
		//
		$parent_node = $_SESSION[ 'NODES' ][ 'HoldingInstitute' ];
		
		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					'GERMPLASM:HoldingCollection' ) );
		if( ! $term->Persistent() )
		{
			//
			// Create term.
			//
			$term->NS( $root_term );
			$term->Code( 'HoldingCollection' );
			$term->Name( 'Holding collection', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Collection in which the germplasm is held.',
			  kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Commit( $container );

		//
		// Save data.
		//
		$parent_term = $_SESSION[ 'TERMS' ][ 'HoldingCollection' ] = $term;
		$parent_node = $_SESSION[ 'NODES' ][ 'HoldingCollection' ] = $node;
		
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
		 *	CollectionCode																 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					'GERMPLASM:CollectionCode' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $root_term );
			$term->Code( 'CollectionCode' );
			$term->Name( 'Collection code', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'This code identifying the germplasm collection to which the accession '
			 .'belongs, the code refers to a record containing extra information.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Examples( 'CIATCASS', TRUE );
			$term->Examples( 'ICARDAICPW', TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $parent_node );
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
		 *	CollectionName																 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					'GERMPLASM:CollectionName' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $root_term );
			$term->Code( 'CollectionName' );
			$term->Name( 'Collection name', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'The name of the collection, in case no record of the collection is stored.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Examples( 'CIAT cassava collection', TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $parent_node );
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
		 *	Biology																		 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					'GERMPLASM:Biology' ) );
		if( ! $term->Persistent() )
		{
			//
			// Create term.
			//
			$term->NS( $root_term );
			$term->Code( 'Biology' );
			$term->Name( 'Biology', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Biological data of accession.',
			  kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Commit( $container );

		//
		// Save data.
		//
		$parent_term = $_SESSION[ 'TERMS' ][ 'Biology' ] = $term;
		$parent_node = $_SESSION[ 'NODES' ][ 'Biology' ] = $node;
		
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
		 *	SampleStatus																 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					'GERMPLASM:SampleStatus' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $root_term );
			$term->Code( 'SampleStatus' );
			$term->Name( 'Sample biological status', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Sample biological status',
			  kDEFAULT_LANGUAGE );
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
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	SAMPSTAT																	 *
		 *===============================================================================*/

		//
		// Get node index.
		//
		$query = new CMongoQuery();
		$query->AppendStatement(
			CQueryStatement::Equals(
				kTAG_DATA.'.'.kTAG_TERM, 'MCPD:SAMPSTAT' ),
			kOPERATOR_AND );
		$index = new COntologyNodeIndex( $query, $node_idx_cont );
		$tmp = new COntologyNode( $container, $index->Node() );
		
		//
		// Edge.
		//
		$edge = $tmp->RelateTo( $container, $scale_of, $node );
		$edge->Commit( $container );
	 
		/*================================================================================
		 *	SampleStatusNotes																 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					'GERMPLASM:SampleStatusNotes' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $root_term );
			$term->Code( 'SampleStatusNotes' );
			$term->Name( 'Sample biological status notes', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Sample biological status',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $parent_node );
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
		 *	Origin																		 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					'GERMPLASM:Origin' ) );
		if( ! $term->Persistent() )
		{
			//
			// Create term.
			//
			$term->NS( $root_term );
			$term->Code( 'Origin' );
			$term->Name( 'Origin', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Germplasm origin.',
			  kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Commit( $container );

		//
		// Save data.
		//
		$parent_term = $_SESSION[ 'TERMS' ][ 'Biology' ] = $term;
		$parent_node = $_SESSION[ 'NODES' ][ 'Biology' ] = $node;
		
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
		 *	SampleSource																 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					'GERMPLASM:SampleSource' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $root_term );
			$term->Code( 'SampleSource' );
			$term->Name( 'Sample source', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Source of sample',
			  kDEFAULT_LANGUAGE );
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
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	COLLSRC																		 *
		 *===============================================================================*/

		//
		// Get node index.
		//
		$query = new CMongoQuery();
		$query->AppendStatement(
			CQueryStatement::Equals(
				kTAG_DATA.'.'.kTAG_TERM, 'MCPD:COLLSRC' ),
			kOPERATOR_AND );
		$index = new COntologyNodeIndex( $query, $node_idx_cont );
		$tmp = new COntologyNode( $container, $index->Node() );
		
		//
		// Edge.
		//
		$edge = $tmp->RelateTo( $container, $scale_of, $node );
		$edge->Commit( $container );
	 
		/*================================================================================
		 *	AcquisitionSourceNotes														 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					'GERMPLASM:AcquisitionSourceNotes' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $root_term );
			$term->Code( 'AcquisitionSourceNotes' );
			$term->Name( 'Collecting or acquisition source description',
						  kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Sample source and acquisition notes',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $parent_node );
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
		 *	Management																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					'GERMPLASM:Management' ) );
		if( ! $term->Persistent() )
		{
			//
			// Create term.
			//
			$term->NS( $root_term );
			$term->Code( 'Management' );
			$term->Name( 'Management', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Germplsam management.',
			  kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Commit( $container );

		//
		// Save data.
		//
		$parent_term = $_SESSION[ 'TERMS' ][ 'Management' ] = $term;
		$parent_node = $_SESSION[ 'NODES' ][ 'Management' ] = $node;
		
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
		 *	AcquisitionDate																 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					'GERMPLASM:AcquisitionDate' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $root_term );
			$term->Code( 'AcquisitionDate' );
			$term->Name( 'Acquisition date', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Date of germplasm acquisition',
			  kDEFAULT_LANGUAGE );
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
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	ACQDATE																	 *
		 *===============================================================================*/

		//
		// Get node index.
		//
		$query = new CMongoQuery();
		$query->AppendStatement(
			CQueryStatement::Equals(
				kTAG_DATA.'.'.kTAG_TERM, 'MCPD:ACQDATE' ),
			kOPERATOR_AND );
		$index = new COntologyNodeIndex( $query, $node_idx_cont );
		$tmp = new COntologyNode( $container, $index->Node() );
		
		//
		// Edge.
		//
		$edge = $tmp->RelateTo( $container, $scale_of, $node );
		$edge->Commit( $container );
	 
		/*================================================================================
		 *	Storage																		 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					'GERMPLASM:Storage' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $root_term );
			$term->Code( 'Storage' );
			$term->Name( 'Storage', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Sample storage type',
			  kDEFAULT_LANGUAGE );
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
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	STORAGE																		 *
		 *===============================================================================*/

		//
		// Get node index.
		//
		$query = new CMongoQuery();
		$query->AppendStatement(
			CQueryStatement::Equals(
				kTAG_DATA.'.'.kTAG_TERM, 'MCPD:STORAGE' ),
			kOPERATOR_AND );
		$index = new COntologyNodeIndex( $query, $node_idx_cont );
		$tmp = new COntologyNode( $container, $index->Node() );
		
		//
		// Edge.
		//
		$edge = $tmp->RelateTo( $container, $scale_of, $node );
		$edge->Commit( $container );
	 
		/*================================================================================
		 *	StorageTypeNotes															 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					'GERMPLASM:StorageTypeNotes' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $root_term );
			$term->Code( 'StorageTypeNotes' );
			$term->Name( 'Storage notes',
						  kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Sample storage notes',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $parent_node );
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
		 *	SamplesCount																 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					'GERMPLASM:SamplesCount' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $root_term );
			$term->Code( 'SamplesCount' );
			$term->Name( 'Samples count',
						  kDEFAULT_LANGUAGE );
			$term->Definition
			( 'This value should indicate the number of seeds, seedlings, budsticks, in '
			 .'vitro plants, etc. of an accession stored in the gene bank',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_INT32 );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $parent_node );
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
		 *	LastRegenerationDate														 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm(
				$theContainer, COntologyTerm::HashIndex(
					'GERMPLASM:LastRegenerationDate' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $root_term );
			$term->Code( 'LastRegenerationDate' );
			$term->Name( 'Last regeneration date',
						  kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Date in which the accession was last regenerated, this date should '
			 .'possibly be derived from the planting date.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Commit( $container );
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $parent_node );
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
