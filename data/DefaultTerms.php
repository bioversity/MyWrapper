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
 *									DefaultTerms.php									*
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
 * Create default attributes ontology.
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
	Index();
	
	//
	// Load types.
	//
	LoadNamespaces( $_SESSION[ kSESSION_CONTAINER ], TRUE );
	LoadPredicates( $_SESSION[ kSESSION_CONTAINER ], TRUE );
	LoadPrimitiveTypes( $_SESSION[ kSESSION_CONTAINER ], TRUE );
	LoadCompositeTypes( $_SESSION[ kSESSION_CONTAINER ], TRUE );
	LoadStructuredTypes( $_SESSION[ kSESSION_CONTAINER ], TRUE );
	LoadEncodedTypes( $_SESSION[ kSESSION_CONTAINER ], TRUE );
	LoadReferenceTypes( $_SESSION[ kSESSION_CONTAINER ], TRUE );
	LoadTermTypes( $_SESSION[ kSESSION_CONTAINER ], TRUE );
	LoadCardinalityTypes( $_SESSION[ kSESSION_CONTAINER ], TRUE );
	LoadOperatorTypes( $_SESSION[ kSESSION_CONTAINER ], TRUE );
	LoadEntityTypes( $_SESSION[ kSESSION_CONTAINER ], TRUE );
	LoadCustomTypes( $_SESSION[ kSESSION_CONTAINER ], TRUE );
	
	//
	// Load terms.
	//
	LoadIdentifierTerms( $_SESSION[ kSESSION_CONTAINER ], TRUE );
	LoadReferenceTerms( $_SESSION[ kSESSION_CONTAINER ], TRUE );
	LoadAttributeTerms( $_SESSION[ kSESSION_CONTAINER ], TRUE );
	LoadPropertyTerms( $_SESSION[ kSESSION_CONTAINER ], TRUE );
	
	//
	// Load properties.
	//
	LoadMailProperties( $_SESSION[ kSESSION_CONTAINER ], TRUE );
	
	//
	// Load Standards.
	//
	LoadDefaultDomains( $_SESSION[ kSESSION_CONTAINER ], TRUE );
	LoadDefaultCategories( $_SESSION[ kSESSION_CONTAINER ], TRUE );
	LoadCropGroupDescriptors( $_SESSION[ kSESSION_CONTAINER ], TRUE );
	LoadUnStatsRegions( $_SESSION[ kSESSION_CONTAINER ], TRUE );
	LoadISO( $_SESSION[ kSESSION_CONTAINER ], TRUE );
	LoadMCPD( $_SESSION[ kSESSION_CONTAINER ], TRUE );
	
	//
	// Load data dictionaries.
	//
	LoadDatadictStructs( $_SESSION[ kSESSION_CONTAINER ], TRUE );
	LoadEntityDatadict( $_SESSION[ kSESSION_CONTAINER ], TRUE );
	LoadDatasetDatadict( $_SESSION[ kSESSION_CONTAINER ], TRUE );
	LoadFAOInstituteDDict( $_SESSION[ kSESSION_CONTAINER ], TRUE );

	//
	// Connect.
	//
	Connect( kDEFAULT_DATABASE, kENTITY_CONTAINER, FALSE );
	
	//
	// Load FAO institutes.
	//
	LoadFAOInstitutes( $_SESSION[ kSESSION_CONTAINER ], TRUE );

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
	 *	Index																			*
	 *==================================================================================*/

	/**
	 * Index.
	 *
	 * This function will index the default collections.
	 *
	 * @access protected
	 */
	function Index()
	{
		//
		// Index terms collection.
		//
		$collection
			= $_SESSION[ kSESSION_DATABASE ]
				->selectCollection( kDEFAULT_CNT_TERMS );
		$collection->ensureIndex( array( kTAG_GID => 1 ), array( 'unique' => TRUE ) );
		$collection->ensureIndex( kTAG_CODE );
		$collection->ensureIndex( kTAG_NAME );
		$collection->ensureIndex( kTAG_KIND );
		$collection->ensureIndex( kTAG_NODE );
	
		//
		// Index nodes collection.
		//
		$collection
			= $_SESSION[ kSESSION_DATABASE ]
				->selectCollection( kDEFAULT_CNT_NODES );
		$collection->ensureIndex( kTAG_DATA );
	
		//
		// Index edges collection.
		//
		$collection
			= $_SESSION[ kSESSION_DATABASE ]
				->selectCollection( kDEFAULT_CNT_EDGES );
		$collection->ensureIndex( kTAG_SUBJECT );
		$collection->ensureIndex( kTAG_PREDICATE );
		$collection->ensureIndex( kTAG_OBJECT );
	
	} // Index.

	 
	/*===================================================================================
	 *	LoadNamespaces																	*
	 *==================================================================================*/

	/**
	 * Load namespaces.
	 *
	 * This function will load all namespace terms.
	 *
	 * If the last parameter is <i>TRUE</i>, the function will display the name of the
	 * created terms.
	 *
	 * @param CContainer			$theContainer		Collection.
	 * @param boolean				$doDisplay			Display created terms.
	 *
	 * @access protected
	 */
	function LoadNamespaces( CContainer $theContainer, $doDisplay = TRUE )
	{
		//
		// Inform.
		//
		if( $doDisplay )
		{
			echo( "\n".__FUNCTION__."\n" );
			echo( "------------------\n" );
		}
		
		//
		// Set default namespaces.
		//
		$components = array
		(
			array( 'id'	=> '',
				   'nam' => 'Default namespace',
				   'def' => 'The default namespace is used to qualify all attributes and '
						   .'other terms that constitute the default vocabulary for the '
						   .'ontology. Elements of this namespace will be used to build '
						   .'ontologies.' ),
			array( 'id'	=> kTAG_ENTITY,
				   'syn' => 'kTAG_ENTITY',
				   'nam' => 'Entity',
				   'def' => 'This term is used to indicate an entity.' ),
			array( 'id'	=> kTAG_SESSION,
				   'syn' => 'kTAG_SESSION',
				   'nam' => 'Session',
				   'def' => 'This term is used to indicate a session.' )
		);
		
		//
		// Load terms.
		//
		$default = NULL;
		foreach( $components as $component )
		{
			//
			// Instantiate term.
			//
			$term = new COntologyTerm( $theContainer, 
									   COntologyTerm::HashIndex( $component[ 'id' ] ) );
			if( ! $term->Persistent() )
			{
				if( $default !== NULL )
				{
					$term->NS( $default );
					$term->Code( substr( $component[ 'id' ], strlen( $default ) + 1 ) );
				}
				else
					$term->Code( $component[ 'id' ] );
				$term->Kind( kTYPE_NAMESPACE, TRUE );
				$term->Name( $component[ 'nam' ], kDEFAULT_LANGUAGE );
				$term->Definition( $component[ 'def' ], kDEFAULT_LANGUAGE );
				if( array_key_exists( 'syn', $component ) )
					$term->Synonym( $component[ 'syn' ], kTYPE_EXACT, TRUE );
				$term->Commit( $theContainer );
				if( $default === NULL )
					$default = $term;
				
				//
				// Display.
				//
				if( $doDisplay )
					echo( "[$term] ".$term->Name( NULL, kDEFAULT_LANGUAGE )."\n" );
			}
		}
		
		//
		// Set other namespaces.
		//
		$components = array
		(
			array( 'id'	=> 'ECPGR',
				   'nam' => 'European Cooperative Programme for Plant Genetic Resources',
				   'def' => 'The European Cooperative Programme for Plant Genetic '
				   		   .'Resources (ECPGR) is a collaborative programme among most '
				   		   .'European countries aimed at ensuring the long-term '
				   		   .'conservation and facilitating the increased utilization '
				   		   .'of plant genetic resources in Europe.',
				   'url' => 'http://www.ecpgr.cgiar.org/' ),
			array( 'id'	=> 'FAO',
				   'nam' => 'Food and Agriculture Organization of the United Nations',
				   'def' => 'Food and Agriculture Organization of the United Nations '
				   		   .'(FAO).',
				   'url' => 'http://www.fao.org/' ),
			array( 'id'	=> 'FAO:INST',
				   'ns' => 'FAO',
				   'nam' => 'World Information and Early Warning System Institute',
				   'def' => 'World Information and Early Warning System on PGRFA '
				   		   .'institute database entry.',
				   'url' => 'http://apps3.fao.org/wiews/institute_query.htm?i_l=EN' )
		);
		
		//
		// Load terms.
		//
		foreach( $components as $component )
		{
			//
			// Instantiate term.
			//
			$term = new COntologyTerm( $theContainer, 
									   COntologyTerm::HashIndex( $component[ 'id' ] ) );
			if( ! $term->Persistent() )
			{
				if( array_key_exists( 'ns', $component ) )
				{
					$ns = new COntologyTerm(
							$theContainer, COntologyTerm::HashIndex(
								$component[ 'ns' ] ) );
					$term->NS( $ns );
					$term->Code( substr( $component[ 'id' ], strlen( $ns ) + 1 ) );
				}
				else
					$term->Code( $component[ 'id' ] );
				$term->Kind( kTYPE_NAMESPACE, TRUE );
				$term->Name( $component[ 'nam' ], kDEFAULT_LANGUAGE );
				$term->Definition( $component[ 'def' ], kDEFAULT_LANGUAGE );
				if( array_key_exists( 'syn', $component ) )
					$term->Synonym( $component[ 'syn' ], kTYPE_EXACT, TRUE );
				if( array_key_exists( 'url', $component ) )
					$term[ kOFFSET_URL ] = $component[ 'url' ];
				$term->Commit( $theContainer );
				
				//
				// Display.
				//
				if( $doDisplay )
					echo( "[$term] ".$term->Name( NULL, kDEFAULT_LANGUAGE )."\n" );
			}
		}
		
	} // LoadNamespaces.

	 
	/*===================================================================================
	 *	LoadPredicates																	*
	 *==================================================================================*/

	/**
	 * Load predicate terms.
	 *
	 * This function will load all default predicate terms.
	 *
	 * If the last parameter is <i>TRUE</i>, the function will display the name of the
	 * created terms.
	 *
	 * @param CContainer			$theContainer		Collection.
	 * @param boolean				$doDisplay			Display created terms.
	 *
	 * @access protected
	 */
	function LoadPredicates( CContainer $theContainer, $doDisplay = TRUE )
	{
		//
		// Inform.
		//
		if( $doDisplay )
		{
			echo( "\n".__FUNCTION__."\n" );
			echo( "------------------\n" );
		}
		
		//
		// Init local storage.
		//
		$components = array
		(
			array( 'id'	=> kPRED_IS_A,
				   'syn' => 'kPRED_IS_A',
				   'nam' => 'Is-a',
				   'def' => 'This predicate is equivalent to a subclass, it can be used to relate a term to the default '
				   		   .'category to which it belongs within the current ontology.' ),
			array( 'id'	=> kPRED_PART_OF,
				   'syn' => 'kPRED_PART_OF',
				   'nam' => 'Part-of',
				   'def' => 'This predicate indicates that the subject or origin of the relation is part of the object or '
				   		   .'target of the relation.' ),
			array( 'id'	=> kPRED_COMPONENT_OF,
				   'syn' => 'kPRED_COMPONENT_OF',
				   'nam' => 'Component-of',
				   'def' => 'This predicate indicates that the subject or origin of the relation is a component of the object or '
				   		   .'target of the relation.' ),
			array( 'id'	=> kPRED_SCALE_OF,
				   'syn' => 'kPRED_SCALE_OF',
				   'nam' => 'Scale-of',
				   'def' => 'This predicate is used to relate a term that can be used to annotate data with its method term '
				   		   .'or trait term.' ),
			array( 'id'	=> kPRED_METHOD_OF,
				   'syn' => 'kPRED_METHOD_OF',
				   'nam' => 'Method-of',
				   'def' => 'This predicate is used to relate a term which represent a measurement method with '
				   		   .'its trait term.' ),
			array( 'id'	=> kPRED_ENUM_OF,
				   'syn' => 'kPRED_ENUM_OF',
				   'nam' => 'Enumeration-of',
				   'def' => 'This predicate is used to relate enumerated terms, it will relate the enumerated value with '
				   		   .'its measure term or with a superclass of the enumerated value, if in a hierarchy.' )
		);
		
		//
		// Save common namespace.
		//
		$namespace = new COntologyTerm( $theContainer, COntologyTerm::HashIndex( '' ) );
		
		//
		// Get namespace length.
		//
		$len = strlen( $namespace ) + 1;
		
		//
		// Load terms.
		//
		foreach( $components as $component )
		{
			//
			// Create term.
			//
			$term = new COntologyTerm( $theContainer, 
									   COntologyTerm::HashIndex( $component[ 'id' ] ) );
			if( ! $term->Persistent() )
			{
				$term->NS( $namespace );
				$term->Code( substr( $component[ 'id' ], $len ) );
				$term->Kind( kTYPE_PREDICATE, TRUE );
				$term->Name( $component[ 'nam' ], kDEFAULT_LANGUAGE );
				$term->Definition( $component[ 'def' ], kDEFAULT_LANGUAGE );
				$term->Synonym( $component[ 'syn' ], kTYPE_EXACT, TRUE );
				$term->Commit( $theContainer );
				
				//
				// Display.
				//
				if( $doDisplay )
					echo( "[$term] (".$component[ 'syn' ].") "
						 .$term->Name( NULL, kDEFAULT_LANGUAGE )
						 ."\n" );
			}
		}
	
	} // LoadPredicates.

	 
	/*===================================================================================
	 *	LoadPrimitiveTypes																*
	 *==================================================================================*/

	/**
	 * Load primitive data types.
	 *
	 * This function will load all primitive data types, primitive types are data types
	 * that cannot be derived from other data types.
	 *
	 * If the last parameter is <i>TRUE</i>, the function will display the name of the
	 * created terms.
	 *
	 * @param CContainer			$theContainer		Collection.
	 * @param boolean				$doDisplay			Display created terms.
	 *
	 * @access protected
	 */
	function LoadPrimitiveTypes( CContainer $theContainer, $doDisplay = TRUE )
	{
		//
		// Inform.
		//
		if( $doDisplay )
		{
			echo( "\n".__FUNCTION__."\n" );
			echo( "------------------\n" );
		}
		
		//
		// Init local storage.
		//
		$components = array
		(
			array( 'id'	=> kTYPE_STRING,
				   'syn' => 'kTYPE_STRING',
				   'nam' => 'String',
				   'def' => 'This represents the primitive string data type.' ),
			array( 'id'	=> kTYPE_INT32,
				   'syn' => 'kTYPE_INT32',
				   'nam' => '32 bit integer',
				   'def' => 'This represents the primitive 32 bit integer data type.' ),
			array( 'id'	=> kTYPE_INT64,
				   'syn' => 'kTYPE_INT64',
				   'nam' => '64 bit integer',
				   'def' => 'This represents the primitive 64 bit integer data type.' ),
			array( 'id'	=> kTYPE_FLOAT,
				   'syn' => 'kTYPE_FLOAT',
				   'nam' => 'Float',
				   'def' => 'This represents the primitive floating point number data type.' ),
			array( 'id'	=> kTYPE_BOOLEAN,
				   'syn' => 'kTYPE_BOOLEAN',
				   'nam' => 'Boolean',
				   'def' => 'This represents the primitive boolean data type, it is assumed that it is provided as 1/0.' )
		);
		
		//
		// Save common namespace.
		//
		$namespace = new COntologyTerm( $theContainer, COntologyTerm::HashIndex( '' ) );
		
		//
		// Get namespace length.
		//
		$len = strlen( $namespace ) + 1;
		
		//
		// Load terms.
		//
		foreach( $components as $component )
		{
			//
			// Create term.
			//
			$term = new COntologyTerm( $theContainer, 
									   COntologyTerm::HashIndex( $component[ 'id' ] ) );
			if( ! $term->Persistent() )
			{
				$term->NS( $namespace );
				$term->Code( substr( $component[ 'id' ], $len ) );
				$term->Kind( kTYPE_ENUMERATION, TRUE );
				$term->Name( $component[ 'nam' ], kDEFAULT_LANGUAGE );
				$term->Definition( $component[ 'def' ], kDEFAULT_LANGUAGE );
				$term->Synonym( $component[ 'syn' ], kTYPE_EXACT, TRUE );
				$term->Commit( $theContainer );
				
				//
				// Display.
				//
				if( $doDisplay )
					echo( "[$term] (".$component[ 'syn' ].") "
						 .$term->Name( NULL, kDEFAULT_LANGUAGE )
						 ."\n" );
			}
		}
	
	} // LoadPrimitiveTypes.

	 
	/*===================================================================================
	 *	LoadCompositeTypes																*
	 *==================================================================================*/

	/**
	 * Load composite data types.
	 *
	 * This function will load all composite data types, composite types are data types
	 * that are constituted by primitive data types, but that define a specialised type:
	 * for instance a {@link kTYPE_DATE date} expressed as a <i>YYYY-MM-DD</i> string
	 * is a string, but it expresses a date.
	 *
	 * If the last parameter is <i>TRUE</i>, the function will display the name of the
	 * created terms.
	 *
	 * @param CContainer			$theContainer		Collection.
	 * @param boolean				$doDisplay			Display created terms.
	 *
	 * @access protected
	 */
	function LoadCompositeTypes( CContainer $theContainer, $doDisplay = TRUE )
	{
		//
		// Inform.
		//
		if( $doDisplay )
		{
			echo( "\n".__FUNCTION__."\n" );
			echo( "------------------\n" );
		}
		
		//
		// Init local storage.
		//
		$components = array
		(
			array( 'id'	=> kTYPE_DATE,
				   'ns'	 => TRUE,
				   'syn' => 'kTYPE_DATE',
				   'nam' => 'Date',
				   'def' => 'This term represents a date represented as a YYYYMMDD string in which missing '
						   .'elements should be omitted. This means that if we don\'t know the day we can '
						   .'express that date as YYYYMM string. The year is required and the month is '
						   .'required if you provide the day.' ),
			array( 'id'	=> kTYPE_TIME,
				   'ns'	 => TRUE,
				   'syn' => 'kTYPE_TIME',
				   'nam' => 'Time',
				   'def' => 'This term represents a date represented as a YYYY-MM-DD HH:MM:SS string '
						   .'in which you may not have missing elements.' ),
			array( 'id'	=> kTYPE_REGEX,
				   'ns'	 => TRUE,
				   'syn' => 'kTYPE_REGEX',
				   'nam' => 'Regular expression',
				   'def' => 'This term represents a regular expression string type.' ),
				   
			array( 'id'	=> kTYPE_BINARY_STRING,
				   'ns'	 => FALSE,
				   'syn' => 'kTYPE_BINARY_STRING',
				   'nam' => 'Binary string',
				   'def' => 'This term represents a binary string.' ),
			array( 'id'	=> kTYPE_STAMP_SEC,
				   'ns'	 => FALSE,
				   'syn' => 'kTYPE_STAMP_SEC',
				   'nam' => 'Seconds',
				   'def' => 'This term represents the number of seconds since January 1st, 1970.' ),
			array( 'id'	=> kTYPE_STAMP_USEC,
				   'ns'	 => FALSE,
				   'syn' => 'kTYPE_STAMP_USEC',
				   'nam' => 'Microseconds',
				   'def' => 'This term represents the number of microseconds since the last second.' ),
			array( 'id'	=> kTYPE_BINARY_TYPE,
				   'ns'	 => FALSE,
				   'syn' => 'kTYPE_BINARY_TYPE',
				   'nam' => 'Binary string type',
				   'def' => 'This term represents a binary string type.' )
		);
		
		//
		// Save common namespace.
		//
		$namespace = new COntologyTerm( $theContainer, COntologyTerm::HashIndex( '' ) );
		
		//
		// Get namespace length.
		//
		$len = strlen( $namespace ) + 1;
		
		//
		// Load terms.
		//
		foreach( $components as $component )
		{
			//
			// Create term.
			//
			$term = new COntologyTerm( $theContainer, 
									   COntologyTerm::HashIndex( $component[ 'id' ] ) );
			if( ! $term->Persistent() )
			{
				if( $component[ 'ns' ] )
				{
					$term->NS( $namespace );
					$term->Code( substr( $component[ 'id' ], $len ) );
				}
				else
					$term->Code( $component[ 'id' ] );
				$term->Kind( kTYPE_ENUMERATION, TRUE );
				$term->Name( $component[ 'nam' ], kDEFAULT_LANGUAGE );
				$term->Definition( $component[ 'def' ], kDEFAULT_LANGUAGE );
				$term->Synonym( $component[ 'syn' ], kTYPE_EXACT, TRUE );
				$term->Commit( $theContainer );
				
				//
				// Display.
				//
				if( $doDisplay )
					echo( "[$term] (".$component[ 'syn' ].") "
						 .$term->Name( NULL, kDEFAULT_LANGUAGE )
						 ."\n" );
			}
		}
	
	} // LoadCompositeTypes.

	 
	/*===================================================================================
	 *	LoadStructuredTypes																*
	 *==================================================================================*/

	/**
	 * Load structured data types.
	 *
	 * This function will load all structured data types, structured types are data types
	 * that may be composed by a combination of primitive data types.
	 *
	 * If the last parameter is <i>TRUE</i>, the function will display the name of the
	 * created terms.
	 *
	 * @param CContainer			$theContainer		Collection.
	 * @param boolean				$doDisplay			Display created terms.
	 *
	 * @access protected
	 */
	function LoadStructuredTypes( CContainer $theContainer, $doDisplay = TRUE )
	{
		//
		// Inform.
		//
		if( $doDisplay )
		{
			echo( "\n".__FUNCTION__."\n" );
			echo( "------------------\n" );
		}
		
		//
		// Init local storage.
		//
		$components = array
		(
			array( 'id'	=> kTYPE_REF,
				   'syn' => 'kTYPE_REF',
				   'nam' => 'Object reference',
				   'def' => 'This term represents an object reference, in general it '
				   		   .'will be the local unique identifier of the object used '
				   		   .'as a scalar value, or a structure adding the container and '
				   		   .'database in which the referenced object is stored. This data '
				   		   .'type quelifies all properties and attributes that refer to '
				   		   .'other objects.' ),
			array( 'id'	=> kTYPE_BINARY,
				   'syn' => 'kTYPE_BINARY',
				   'nam' => 'Binary',
				   'def' => 'This term represents a binary string data type, in general it will be '
						   .'as a structure containing a binary string in hexadecimal format.' ),
			array( 'id'	=> kTYPE_STAMP,
				   'syn' => 'kTYPE_STAMP',
				   'nam' => 'Time-stamp',
				   'def' => 'This term represents a date, time and milliseconds stamp, in general '
						   .'it will be a structure holding the number of secods since January 1st 1970 '
						   .'and optionally the number of milliseconds.' ),
			array( 'id'	=> kTYPE_ENUM,
				   'syn' => 'kTYPE_ENUM',
				   'nam' => 'Enumeration',
				   'def' => 'This term represents an enumeration container, enumerations are '
						   .'a controlled vocabulary in which one may only choose one element. '
						   .'This data type implies that the term forms a tree whose siblings '
						   .'are the enumeration elements.' ),
			array( 'id'	=> kTYPE_ENUM_SET,
				   'syn' => 'kTYPE_ENUM_SET',
				   'nam' => 'Enumerated set',
				   'def' => 'This term represents an enumerated set container, sets are '
						   .'a controlled vocabulary from which one may choose one or more elements. '
						   .'This data type implies that the term forms a tree whose siblings '
						   .'are the enumeration elements.' ),
			array( 'id'	=> kTYPE_LIST,
				   'syn' => 'kTYPE_LIST',
				   'nam' => 'List',
				   'def' => 'This term represents a list data type, lists are arrays of '
						   .'scalars or other structures, including lists.' )
		);
		
		//
		// Save common namespace.
		//
		$namespace = new COntologyTerm( $theContainer, COntologyTerm::HashIndex( '' ) );
		
		//
		// Get namespace length.
		//
		$len = strlen( $namespace ) + 1;
		
		//
		// Load terms.
		//
		foreach( $components as $component )
		{
			//
			// Create term.
			//
			$term = new COntologyTerm( $theContainer, 
									   COntologyTerm::HashIndex( $component[ 'id' ] ) );
			if( ! $term->Persistent() )
			{
				$term->NS( $namespace );
				$term->Code( substr( $component[ 'id' ], $len ) );
				$term->Kind( kTYPE_ENUMERATION, TRUE );
				$term->Name( $component[ 'nam' ], kDEFAULT_LANGUAGE );
				$term->Definition( $component[ 'def' ], kDEFAULT_LANGUAGE );
				$term->Synonym( $component[ 'syn' ], kTYPE_EXACT, TRUE );
				$term->Commit( $theContainer );
				
				//
				// Display.
				//
				if( $doDisplay )
					echo( "[$term] (".$component[ 'syn' ].") "
						 .$term->Name( NULL, kDEFAULT_LANGUAGE )
						 ."\n" );
			}
		}
	
	} // LoadStructuredTypes.

	 
	/*===================================================================================
	 *	LoadEncodedTypes																*
	 *==================================================================================*/

	/**
	 * Load encoded data types.
	 *
	 * This function will load all file data types, file types are data types that represent
	 * data in files.
	 *
	 * If the last parameter is <i>TRUE</i>, the function will display the name of the
	 * created terms.
	 *
	 * @param CContainer			$theContainer		Collection.
	 * @param boolean				$doDisplay			Display created terms.
	 *
	 * @access protected
	 */
	function LoadEncodedTypes( CContainer $theContainer, $doDisplay = TRUE )
	{
		//
		// Inform.
		//
		if( $doDisplay )
		{
			echo( "\n".__FUNCTION__."\n" );
			echo( "------------------\n" );
		}
		
		//
		// Init local storage.
		//
		$components = array
		(
			array( 'id'	=> kTYPE_PHP,
				   'syn' => 'kTYPE_PHP',
				   'nam' => 'PHP',
				   'def' => 'This term represents PHP-encoded data.' ),
			array( 'id'	=> kTYPE_JSON,
				   'syn' => 'kTYPE_JSON',
				   'nam' => 'JSON',
				   'def' => 'This term represents JSON-encoded data.' ),
			array( 'id'	=> kTYPE_XML,
				   'syn' => 'kTYPE_XML',
				   'nam' => 'XML',
				   'def' => 'This term represents XML-encoded data.' ),
			array( 'id'	=> kTYPE_HTML,
				   'syn' => 'kTYPE_HTML',
				   'nam' => 'HTML',
				   'def' => 'This term represents HTML-encoded data.' ),
			array( 'id'	=> kTYPE_CSV,
				   'syn' => 'kTYPE_CSV',
				   'nam' => 'CSV',
				   'def' => 'This term represents CSV-encoded data.' ),
			array( 'id'	=> kTYPE_SVG,
				   'syn' => 'kTYPE_SVG',
				   'nam' => 'Scalable Vector Graphics',
				   'def' => 'This term represents the Scalable Vector Graphics data type.' ),
			array( 'id'	=> kTYPE_PNG,
				   'syn' => 'kTYPE_PNG',
				   'nam' => 'Portable Network Graphics',
				   'def' => 'This term represents the Portable Network Graphics data type.' ),
			array( 'id'	=> kTYPE_META,
				   'syn' => 'kTYPE_META',
				   'nam' => 'Metadata',
				   'def' => 'This term represents meta-data.' )
		);
		
		//
		// Save common namespace.
		//
		$namespace = new COntologyTerm( $theContainer, COntologyTerm::HashIndex( '' ) );
		
		//
		// Get namespace length.
		//
		$len = strlen( $namespace ) + 1;
		
		//
		// Load terms.
		//
		foreach( $components as $component )
		{
			//
			// Create term.
			//
			$term = new COntologyTerm( $theContainer, 
									   COntologyTerm::HashIndex( $component[ 'id' ] ) );
			if( ! $term->Persistent() )
			{
				$term->NS( $namespace );
				$term->Code( substr( $component[ 'id' ], $len ) );
				$term->Kind( kTYPE_ENUMERATION, TRUE );
				$term->Name( $component[ 'nam' ], kDEFAULT_LANGUAGE );
				$term->Definition( $component[ 'def' ], kDEFAULT_LANGUAGE );
				$term->Synonym( $component[ 'syn' ], kTYPE_EXACT, TRUE );
				$term->Commit( $theContainer );
				
				//
				// Display.
				//
				if( $doDisplay )
					echo( "[$term] (".$component[ 'syn' ].") "
						 .$term->Name( NULL, kDEFAULT_LANGUAGE )
						 ."\n" );
			}
		}
	
	} // LoadEncodedTypes.

	 
	/*===================================================================================
	 *	LoadCustomTypes																	*
	 *==================================================================================*/

	/**
	 * Load custom data types.
	 *
	 * This function will load all custom data types, in general it will apply to custom
	 * native data types
	 *
	 * If the last parameter is <i>TRUE</i>, the function will display the name of the
	 * created terms.
	 *
	 * @param CContainer			$theContainer		Collection.
	 * @param boolean				$doDisplay			Display created terms.
	 *
	 * @access protected
	 */
	function LoadCustomTypes( CContainer $theContainer, $doDisplay = TRUE )
	{
		//
		// Inform.
		//
		if( $doDisplay )
		{
			echo( "\n".__FUNCTION__."\n" );
			echo( "------------------\n" );
		}
		
		//
		// Init local storage.
		//
		$components = array
		(
			array( 'id'	=> kTYPE_MongoId,
				   'syn' => 'kTYPE_MongoId',
				   'nam' => 'MongoId',
				   'def' => 'This term represents a MongoId data type.' ),
			array( 'id'	=> kTYPE_MongoCode,
				   'syn' => 'kTYPE_MongoCode',
				   'nam' => 'MongoCode',
				   'def' => 'This term represents a MongoCode data type.' )
		);
		
		//
		// Save common namespace.
		//
		$namespace = new COntologyTerm( $theContainer, COntologyTerm::HashIndex( '' ) );
		
		//
		// Get namespace length.
		//
		$len = strlen( $namespace ) + 1;
		
		//
		// Load terms.
		//
		foreach( $components as $component )
		{
			//
			// Create term.
			//
			$term = new COntologyTerm( $theContainer, 
									   COntologyTerm::HashIndex( $component[ 'id' ] ) );
			if( ! $term->Persistent() )
			{
				$term->NS( $namespace );
				$term->Code( substr( $component[ 'id' ], $len ) );
				$term->Kind( kTYPE_ENUMERATION, TRUE );
				$term->Name( $component[ 'nam' ], kDEFAULT_LANGUAGE );
				$term->Definition( $component[ 'def' ], kDEFAULT_LANGUAGE );
				$term->Synonym( $component[ 'syn' ], kTYPE_EXACT, TRUE );
				$term->Commit( $theContainer );
				
				//
				// Display.
				//
				if( $doDisplay )
					echo( "[$term] (".$component[ 'syn' ].") "
						 .$term->Name( NULL, kDEFAULT_LANGUAGE )
						 ."\n" );
			}
		}
	
	} // LoadCustomTypes.

	 
	/*===================================================================================
	 *	LoadReferenceTypes																*
	 *==================================================================================*/

	/**
	 * Load reference types.
	 *
	 * This function will load all default reference types.
	 *
	 * If the last parameter is <i>TRUE</i>, the function will display the name of the
	 * created terms.
	 *
	 * @param CContainer			$theContainer		Collection.
	 * @param boolean				$doDisplay			Display created terms.
	 *
	 * @access protected
	 */
	function LoadReferenceTypes( CContainer $theContainer, $doDisplay = TRUE )
	{
		//
		// Inform.
		//
		if( $doDisplay )
		{
			echo( "\n".__FUNCTION__."\n" );
			echo( "------------------\n" );
		}
		
		//
		// Init local storage.
		//
		$components = array
		(
			array( 'id'	=> kTYPE_EXACT,
				   'syn' => 'kTYPE_EXACT',
				   'nam' => 'Exact reference',
				   'def' => 'This term represents an exact reference or synonym.' ),
			array( 'id'	=> kTYPE_BROAD,
				   'syn' => 'kTYPE_BROAD',
				   'nam' => 'Broad reference',
				   'def' => 'This term represents a broad reference or synonym.' ),
			array( 'id'	=> kTYPE_NARROW,
				   'syn' => 'kTYPE_NARROW',
				   'nam' => 'Narrow reference',
				   'def' => 'This term represents a narrow reference or synonym.' ),
			array( 'id'	=> kTYPE_RELATED,
				   'syn' => 'kTYPE_RELATED',
				   'nam' => 'Related reference',
				   'def' => 'This term represents a related reference or synonym.' )
		);
		
		//
		// Save common namespace.
		//
		$namespace = new COntologyTerm( $theContainer, COntologyTerm::HashIndex( '' ) );
		
		//
		// Get namespace length.
		//
		$len = strlen( $namespace ) + 1;
		
		//
		// Load terms.
		//
		foreach( $components as $component )
		{
			//
			// Create term.
			//
			$term = new COntologyTerm( $theContainer, 
									   COntologyTerm::HashIndex( $component[ 'id' ] ) );
			if( ! $term->Persistent() )
			{
				$term->NS( $namespace );
				$term->Code( substr( $component[ 'id' ], $len ) );
				$term->Kind( kTYPE_ENUMERATION, TRUE );
				$term->Name( $component[ 'nam' ], kDEFAULT_LANGUAGE );
				$term->Definition( $component[ 'def' ], kDEFAULT_LANGUAGE );
				$term->Synonym( $component[ 'syn' ], kTYPE_EXACT, TRUE );
				$term->Commit( $theContainer );
				
				//
				// Display.
				//
				if( $doDisplay )
					echo( "[$term] (".$component[ 'syn' ].") "
						 .$term->Name( NULL, kDEFAULT_LANGUAGE )
						 ."\n" );
			}
		}
	
	} // LoadReferenceTypes.

	 
	/*===================================================================================
	 *	LoadTermTypes																	*
	 *==================================================================================*/

	/**
	 * Load reference types.
	 *
	 * This function will load all default term types.
	 *
	 * If the last parameter is <i>TRUE</i>, the function will display the name of the
	 * created terms.
	 *
	 * @param CContainer			$theContainer		Collection.
	 * @param boolean				$doDisplay			Display created terms.
	 *
	 * @access protected
	 */
	function LoadTermTypes( CContainer $theContainer, $doDisplay = TRUE )
	{
		//
		// Inform.
		//
		if( $doDisplay )
		{
			echo( "\n".__FUNCTION__."\n" );
			echo( "------------------\n" );
		}
		
		//
		// Init local storage.
		//
		$components = array
		(
			array( 'id'	=> kTYPE_ROOT,
				   'syn' => 'kTYPE_ROOT',
				   'nam' => 'Root',
				   'def' => 'A root node is an entry point into an ontology, '
				   		   .'it represents the term that defines the whole ontology.' ),
			array( 'id'	=> kTYPE_NAMESPACE,
				   'syn' => 'kTYPE_NAMESPACE',
				   'nam' => 'Namespace',
				   'def' => 'A namespace term is used as a container for a set of other '
				   		   .'term identifiers to allow the disambiguation of homonym '
				   		   .'identifiers residing in different namespaces. In general, '
				   		   .'namespaces are grouped by functionality.' ),
			array( 'id'	=> kTYPE_ATTRIBUTE,
				   'syn' => 'kTYPE_ATTRIBUTE',
				   'nam' => 'Attribute',
				   'def' => 'An attribute term is used to tag attributes or properties of '
				   		   .'an object, it describes and identifies a specific property '
				   		   .'or quality of an object.' ),
			array( 'id'	=> kTYPE_TYPEDEF,
				   'syn' => 'kTYPE_TYPEDEF',
				   'nam' => 'Type definition',
				   'def' => 'A type definition node is used to record the data structure '
				   		   .'of an attribute or property. It forms a graph of nodes which '
				   		   .'are in turn type definitions or primitive types, this '
				   		   .'structure can be used to illustrate a data structure.' ),
			array( 'id'	=> kTYPE_PREDICATE,
				   'syn' => 'kTYPE_PREDICATE',
				   'nam' => 'Predicate',
				   'def' => 'A predicate term is used to qualify the relation between a '
						   .'subject and an object, it represents the type or kind of the '
				   		   .'relationship.' ),
			array( 'id'	=> kTYPE_TRAIT,
				   'syn' => 'kTYPE_TRAIT',
				   'nam' => 'Trait',
				   'def' => 'A trait node is generally a leaf node of an ontology, it '
				   		   .'defines a leaf concept that may be used to annotate data. '
				   		   .'Plant height, for instance, is a trait. The only child nodes '
				   		   .'of a trait can be methods and scales.' ),
			array( 'id'	=> kTYPE_METHOD,
				   'syn' => 'kTYPE_METHOD',
				   'nam' => 'Method',
				   'def' => 'A method is a node that defines the specific method or '
				   		   .'workflow with which trait data is collected. Methods are used '
				   		   .'to record variations in the way a specific trait data is '
				   		   .'measured or collected.' ),
			array( 'id'	=> kTYPE_MEASURE,
				   'syn' => 'kTYPE_MEASURE',
				   'nam' => 'Measure',
				   'def' => 'A measure or scale node defines the specific data type of '
				   		   .'a trait or method. One method or trait may be expressed in '
				   		   .'many different types of measures or scales.' ),
			array( 'id'	=> kTYPE_ANNOTATION,
				   'syn' => 'kTYPE_ANNOTATION',
				   'nam' => 'Annotation',
				   'def' => 'Data elements are tagged by annotations, these annotations '
				   		   .'describe the trait, method and scale of the data and are '
				   		   .'expressed as a sequence of terms.' ),
			array( 'id'	=> kTYPE_ENUMERATION,
				   'syn' => 'kTYPE_ENUMERATION',
				   'nam' => 'Enumeration',
				   'def' => 'An enumeration is a term that represents an element of a '
				   		   .'controlled vocabulary.' ),
			array( 'id'	=> kTYPE_DICTIONARY,
				   'syn' => 'kTYPE_DICTIONARY',
				   'nam' => 'Dictionary',
				   'def' => 'A dictionary node is a root node of a graph that represents a '
				   		   .'data structure or dictionary.' )
		);
		
		//
		// Save common namespace.
		//
		$namespace = new COntologyTerm( $theContainer, COntologyTerm::HashIndex( '' ) );
		
		//
		// Get namespace length.
		//
		$len = strlen( $namespace ) + 1;
		
		//
		// Load terms.
		//
		foreach( $components as $component )
		{
			//
			// Create term.
			//
			$term = new COntologyTerm( $theContainer, 
									   COntologyTerm::HashIndex( $component[ 'id' ] ) );
			if( ! $term->Persistent() )
			{
				$term->NS( $namespace );
				$term->Code( substr( $component[ 'id' ], $len ) );
				$term->Kind( kTYPE_ENUMERATION, TRUE );
				$term->Name( $component[ 'nam' ], kDEFAULT_LANGUAGE );
				$term->Definition( $component[ 'def' ], kDEFAULT_LANGUAGE );
				$term->Synonym( $component[ 'syn' ], kTYPE_EXACT, TRUE );
				$term->Commit( $theContainer );
				
				//
				// Display.
				//
				if( $doDisplay )
					echo( "[$term] (".$component[ 'syn' ].") "
						 .$term->Name( NULL, kDEFAULT_LANGUAGE )
						 ."\n" );
			}
		}
	
	} // LoadTermTypes.

	 
	/*===================================================================================
	 *	LoadCardinalityTypes															*
	 *==================================================================================*/

	/**
	 * Load cardinality types.
	 *
	 * This function will load all default cardinality types.
	 *
	 * If the last parameter is <i>TRUE</i>, the function will display the name of the
	 * created terms.
	 *
	 * @param CContainer			$theContainer		Collection.
	 * @param boolean				$doDisplay			Display created terms.
	 *
	 * @access protected
	 */
	function LoadCardinalityTypes( CContainer $theContainer, $doDisplay = TRUE )
	{
		//
		// Inform.
		//
		if( $doDisplay )
		{
			echo( "\n".__FUNCTION__."\n" );
			echo( "------------------\n" );
		}
		
		//
		// Init local storage.
		//
		$components = array
		(
			array( 'id'	=> kCARD_0_1,
				   'syn' => 'kCARD_0_1',
				   'nam' => 'Zero or one',
				   'def' => 'This term defines a cardinality of zero or one.' ),
			array( 'id'	=> kCARD_1,
				   'syn' => 'kCARD_1',
				   'nam' => 'One',
				   'def' => 'This term defines a cardinality of exactly one.' ),
			array( 'id'	=> kCARD_ANY,
				   'syn' => 'kCARD_ANY',
				   'nam' => 'Any',
				   'def' => 'This term defines a cardinality of any kind.' )
		);
		
		//
		// Save common namespace.
		//
		$namespace = new COntologyTerm( $theContainer, COntologyTerm::HashIndex( '' ) );
		
		//
		// Get namespace length.
		//
		$len = strlen( $namespace ) + 1;
		
		//
		// Load terms.
		//
		foreach( $components as $component )
		{
			//
			// Create term.
			//
			$term = new COntologyTerm( $theContainer, 
									   COntologyTerm::HashIndex( $component[ 'id' ] ) );
			if( ! $term->Persistent() )
			{
				$term->NS( $namespace );
				$term->Code( substr( $component[ 'id' ], $len ) );
				$term->Kind( kTYPE_ENUMERATION, TRUE );
				$term->Name( $component[ 'nam' ], kDEFAULT_LANGUAGE );
				$term->Definition( $component[ 'def' ], kDEFAULT_LANGUAGE );
				$term->Synonym( $component[ 'syn' ], kTYPE_EXACT, TRUE );
				$term->Commit( $theContainer );
				
				//
				// Display.
				//
				if( $doDisplay )
					echo( "[$term] (".$component[ 'syn' ].") "
						 .$term->Name( NULL, kDEFAULT_LANGUAGE )
						 ."\n" );
			}
		}
	
	} // LoadCardinalityTypes.

	 
	/*===================================================================================
	 *	LoadOperatorTypes																*
	 *==================================================================================*/

	/**
	 * Load operator types.
	 *
	 * This function will load all default operator types.
	 *
	 * If the last parameter is <i>TRUE</i>, the function will display the name of the
	 * created terms.
	 *
	 * @param CContainer			$theContainer		Collection.
	 * @param boolean				$doDisplay			Display created terms.
	 *
	 * @access protected
	 */
	function LoadOperatorTypes( CContainer $theContainer, $doDisplay = TRUE )
	{
		//
		// Inform.
		//
		if( $doDisplay )
		{
			echo( "\n".__FUNCTION__."\n" );
			echo( "------------------\n" );
		}
		
		//
		// Init local storage.
		//
		$components = array
		(
			array( 'id'	=> kOPERATOR_DISABLED,
				   'syn' => 'kOPERATOR_DISABLED',
				   'nam' => 'Disabled',
				   'def' => 'This operator represents a disabled operator.' ),
			array( 'id'	=> kOPERATOR_EQUAL,
				   'syn' => 'kOPERATOR_EQUAL',
				   'nam' => 'Equals',
				   'def' => 'This operator represents equality.' ),
			array( 'id'	=> kOPERATOR_EQUAL_NOT,
				   'syn' => 'kOPERATOR_EQUAL_NOT',
				   'nam' => 'Not equal',
				   'def' => 'This operator represents inequality.' ),
			array( 'id'	=> kOPERATOR_LIKE,
				   'syn' => 'kOPERATOR_LIKE',
				   'nam' => 'Like',
				   'def' => 'This operator represents case and accent matching '
				   		   .'(for strings).' ),
			array( 'id'	=> kOPERATOR_LIKE_NOT,
				   'syn' => 'kOPERATOR_LIKE_NOT',
				   'nam' => 'Not like',
				   'def' => 'This operator represents case and accent non matching '
				   		   .'(for strings).' ),
			array( 'id'	=> kOPERATOR_PREFIX,
				   'syn' => 'kOPERATOR_PREFIX',
				   'nam' => 'Starts with',
				   'def' => 'This operator represents prefix comparaison: starts with '
				   		   .'(for strings).' ),
			array( 'id'	=> kOPERATOR_PREFIX_NOCASE,
				   'syn' => 'kOPERATOR_PREFIX_NOCASE',
				   'nam' => 'Starts with no-case',
				   'def' => 'This operator represents prefix comparaison: starts with '
				   		   .'(for strings) with case and accent insensitive matching.' ),
			array( 'id'	=> kOPERATOR_CONTAINS,
				   'syn' => 'kOPERATOR_CONTAINS',
				   'nam' => 'Contains',
				   'def' => 'represents content comparaison: contains '
				   		   .'(for strings).' ),
			array( 'id'	=> kOPERATOR_CONTAINS_NOCASE,
				   'syn' => 'kOPERATOR_CONTAINS_NOCASE',
				   'nam' => 'Contains with no-case',
				   'def' => 'represents content comparaison: contains '
				   		   .'(for strings) with case and accent insensitive matching.' ),
			array( 'id'	=> kOPERATOR_SUFFIX,
				   'syn' => 'kOPERATOR_SUFFIX',
				   'nam' => 'Ends with',
				   'def' => 'This operator represents prefix comparaison: ends with '
				   		   .'(for strings).' ),
			array( 'id'	=> kOPERATOR_SUFFIX_NOCASE,
				   'syn' => 'kOPERATOR_SUFFIX_NOCASE',
				   'nam' => 'Ends with no-case',
				   'def' => 'This operator represents prefix comparaison: ends with '
				   		   .'(for strings) with case and accent insensitive matching.' ),
			array( 'id'	=> kOPERATOR_REGEX,
				   'syn' => 'kOPERATOR_REGEX',
				   'nam' => 'Regular expression',
				   'def' => 'This operator represents a regular expression '
				   		   .'(for strings).' ),
			array( 'id'	=> kOPERATOR_LESS,
				   'syn' => 'kOPERATOR_LESS',
				   'nam' => 'Less than',
				   'def' => 'This operator represents less than.' ),
			array( 'id'	=> kOPERATOR_LESS_EQUAL,
				   'syn' => 'kOPERATOR_LESS_EQUAL',
				   'nam' => 'Less than or equal',
				   'def' => 'This operator represents less than or equal.' ),
			array( 'id'	=> kOPERATOR_GREAT,
				   'syn' => 'kOPERATOR_LESS',
				   'nam' => 'Greater than',
				   'def' => 'This operator represents greater than.' ),
			array( 'id'	=> kOPERATOR_GREAT_EQUAL,
				   'syn' => 'kOPERATOR_GREAT_EQUAL',
				   'nam' => 'Greater than or equal',
				   'def' => 'This operator represents greater than or equal.' ),
			array( 'id'	=> kOPERATOR_IRANGE,
				   'syn' => 'kOPERATOR_IRANGE',
				   'nam' => 'Range inclusive',
				   'def' => 'This operator represents a range including limits.' ),
			array( 'id'	=> kOPERATOR_ERANGE,
				   'syn' => 'kOPERATOR_ERANGE',
				   'nam' => 'Range exclusive',
				   'def' => 'This operator represents a range excluding limits.' ),
			array( 'id'	=> kOPERATOR_NULL,
				   'syn' => 'kOPERATOR_NULL',
				   'nam' => 'Empty, null or missing',
				   'def' => 'This operator represents not empty, null or missing.' ),
			array( 'id'	=> kOPERATOR_IN,
				   'syn' => 'kOPERATOR_IN',
				   'nam' => 'Belongs to',
				   'def' => 'This operator matches values in a list of options.' ),
			array( 'id'	=> kOPERATOR_NI,
				   'syn' => 'kOPERATOR_NI',
				   'nam' => 'Does not belong to',
				   'def' => 'This operator matches values excluded from list of options.' ),
			array( 'id'	=> kOPERATOR_ALL,
				   'syn' => 'kOPERATOR_ALL',
				   'nam' => 'All',
				   'def' => 'This operator matches a list of values to all the '
				   		   .'elements of a list of options.' ),
			array( 'id'	=> kOPERATOR_NALL,
				   'syn' => 'kOPERATOR_NALL',
				   'nam' => 'Not all',
				   'def' => 'This operator negates matching a list of values to all the '
				   		   .'elements of a list of options.' ),
			array( 'id'	=> kOPERATOR_EX,
				   'syn' => 'kOPERATOR_EX',
				   'nam' => 'Expression',
				   'def' => 'This operator qualifies expression terms.' ),
			array( 'id'	=> kOPERATOR_AND,
				   'syn' => 'kOPERATOR_AND',
				   'nam' => 'AND',
				   'def' => 'This operator represents the AND (A && B) operator.' ),
			array( 'id'	=> kOPERATOR_NAND,
				   'syn' => 'kOPERATOR_NAND',
				   'nam' => 'Not AND',
				   'def' => 'This operator represents the not AND (NOT(A && B)) '
				   		   .'operator.' ),
			array( 'id'	=> kOPERATOR_OR,
				   'syn' => 'kOPERATOR_OR',
				   'nam' => 'OR',
				   'def' => 'This operator represents the OR (A || B) operator.' ),
			array( 'id'	=> kOPERATOR_NOR,
				   'syn' => 'kOPERATOR_NOR',
				   'nam' => 'Not OR',
				   'def' => 'This operator represents the not OR (NOT(A || B)) operator' )
		);
		
		//
		// Load terms.
		//
		foreach( $components as $component )
		{
			//
			// Create term.
			//
			$term = new COntologyTerm( $theContainer, 
									   COntologyTerm::HashIndex( $component[ 'id' ] ) );
			if( ! $term->Persistent() )
			{
				$term->Code( $component[ 'id' ] );
				$term->Kind( kTYPE_ENUMERATION, TRUE );
				$term->Name( $component[ 'nam' ], kDEFAULT_LANGUAGE );
				$term->Definition( $component[ 'def' ], kDEFAULT_LANGUAGE );
				$term->Synonym( $component[ 'syn' ], kTYPE_EXACT, TRUE );
				$term->Commit( $theContainer );
				
				//
				// Display.
				//
				if( $doDisplay )
					echo( "[$term] (".$component[ 'syn' ].") "
						 .$term->Name( NULL, kDEFAULT_LANGUAGE )
						 ."\n" );
			}
		}
	
	} // LoadOperatorTypes.

	 
	/*===================================================================================
	 *	LoadEntityTypes																	*
	 *==================================================================================*/

	/**
	 * Load entity types.
	 *
	 * This function will load all default entity types.
	 *
	 * If the last parameter is <i>TRUE</i>, the function will display the name of the
	 * created terms.
	 *
	 * @param CContainer			$theContainer		Collection.
	 * @param boolean				$doDisplay			Display created terms.
	 *
	 * @access protected
	 */
	function LoadEntityTypes( CContainer $theContainer, $doDisplay = TRUE )
	{
		//
		// Inform.
		//
		if( $doDisplay )
		{
			echo( "\n".__FUNCTION__."\n" );
			echo( "------------------\n" );
		}
		
		//
		// Init local storage.
		//
		$components = array
		(
			array( 'id'	=> kENTITY_INST,
				   'syn' => 'kENTITY_INST',
				   'nam' => 'Institute',
				   'def' => 'Institute, organisation or institution.' ),
			array( 'id'	=> kENTITY_INST_FAO,
				   'syn' => 'kENTITY_INST_FAO',
				   'nam' => 'FAO/WIEWS institute',
				   'def' => 'An institute listed in the FAO/WIEWS institutes database.' ),
			array( 'id'	=> kENTITY_USER,
				   'syn' => 'kENTITY_USER',
				   'nam' => 'User',
				   'def' => 'User or person.' )
		);
		
		//
		// Save common namespace.
		//
		$namespace = new COntologyTerm( $theContainer,
										COntologyTerm::HashIndex( kTAG_ENTITY ) );
		
		//
		// Get namespace length.
		//
		$len = strlen( $namespace ) + 1;
		
		//
		// Load terms.
		//
		foreach( $components as $component )
		{
			//
			// Create term.
			//
			$term = new COntologyTerm( $theContainer, 
									   COntologyTerm::HashIndex( $component[ 'id' ] ) );
			if( ! $term->Persistent() )
			{
				$term->NS( $namespace );
				$term->Code( substr( $component[ 'id' ], $len ) );
				$term->Kind( kTYPE_ENUMERATION, TRUE );
				$term->Name( $component[ 'nam' ], kDEFAULT_LANGUAGE );
				$term->Definition( $component[ 'def' ], kDEFAULT_LANGUAGE );
				$term->Synonym( $component[ 'syn' ], kTYPE_EXACT, TRUE );
				$term->Commit( $theContainer );
				
				//
				// Display.
				//
				if( $doDisplay )
					echo( "[$term] (".$component[ 'syn' ].") "
						 .$term->Name( NULL, kDEFAULT_LANGUAGE )
						 ."\n" );
			}
		}
	
	} // LoadEntityTypes.

	 
	/*===================================================================================
	 *	LoadIdentifierTerms																*
	 *==================================================================================*/

	/**
	 * Load reference types.
	 *
	 * This function will load all default identifier terms.
	 *
	 * If the last parameter is <i>TRUE</i>, the function will display the name of the
	 * created terms.
	 *
	 * @param CContainer			$theContainer		Collection.
	 * @param boolean				$doDisplay			Display created terms.
	 *
	 * @access protected
	 */
	function LoadIdentifierTerms( CContainer $theContainer, $doDisplay = TRUE )
	{
		//
		// Inform.
		//
		if( $doDisplay )
		{
			echo( "\n".__FUNCTION__."\n" );
			echo( "------------------\n" );
		}
		
		//
		// Init local storage.
		//
		$components = array
		(
			array( 'id'	=> kTAG_LID,
				   'syn' => 'kTAG_LID',
				   'car' => 'kCARD_1',
				   'nam' => 'Local unique identifier',
				   'def' => 'This term represents the object\'s local unique identifier, '
				   		   .'his offset should hold a scalar value which uniquely '
				   		   .'identifies the object within the collection that holds it. '
				   		   .'This should not be confused with the global identifier, '
				   		   .'which represents the value or values used by the public to '
						   .'refer to that object. This value should be tightly integrated '
						   .'with the database.' ),
			array( 'id'	=> kTAG_GID,
				   'ns' => '',
				   'syn' => 'kTAG_GID',
				   'car' => 'kCARD_1',
				   'nam' => 'Global unique identifier',
				   'def' => 'This term represents the object\'s global unique identifier, '
				   		   .'this offset should uniquely identify the object among all '
				   		   .'containers, it represents a string that may only reference '
				   		   .'that specific object. This should not be confused with the '
						   .'local identifier, which represents the key to the object '
						   .'within the local database.' ),
			array( 'id'	=> kTAG_UID,
				   'ns' => '',
				   'syn' => 'kTAG_UID',
				   'car' => 'kCARD_0_1',
				   'nam' => 'Unique identifier',
				   'def' => 'This term represents the object\'s unique identifier when '
				   		   .'the object\'s local identifier cannot be used to determine '
				   		   .'the unique key, it is a unique key that will be used to '
				   		   ,'query the database by content, rather than by key field.' )
		);
		
		//
		// Load terms.
		//
		foreach( $components as $component )
		{
			//
			// Create term.
			//
			$term = new COntologyTerm( $theContainer, 
									   COntologyTerm::HashIndex( $component[ 'id' ] ) );
			if( ! $term->Persistent() )
			{
				//
				// Load namespace.
				//
				if( array_key_exists( 'ns', $component ) )
				{
					$ns = new COntologyTerm(
							$theContainer, COntologyTerm::HashIndex(
								$component[ 'ns' ] ) );
					$term->NS( $ns );
					$term->Code( substr( $component[ 'id' ], strlen( $ns ) + 1 ) );
				}
				else
					$term->Code( $component[ 'id' ] );
				$term->Kind( kTYPE_ATTRIBUTE, TRUE );
				$term->Cardinality( $component[ 'car' ] );
				$term->Name( $component[ 'nam' ], kDEFAULT_LANGUAGE );
				$term->Definition( $component[ 'def' ], kDEFAULT_LANGUAGE );
				$term->Synonym( $component[ 'syn' ], kTYPE_EXACT, TRUE );
				$term->Commit( $theContainer );
				
				//
				// Display.
				//
				if( $doDisplay )
					echo( "[$term] (".$component[ 'syn' ].") "
						 .$term->Name( NULL, kDEFAULT_LANGUAGE )
						 ."\n" );
			}
		}
	
	} // LoadIdentifierTerms.

	 
	/*===================================================================================
	 *	LoadReferenceTerms																*
	 *==================================================================================*/

	/**
	 * Load reference terms.
	 *
	 * This function will load all default reference terms.
	 *
	 * If the last parameter is <i>TRUE</i>, the function will display the name of the
	 * created terms.
	 *
	 * @param CContainer			$theContainer		Collection.
	 * @param boolean				$doDisplay			Display created terms.
	 *
	 * @access protected
	 */
	function LoadReferenceTerms( CContainer $theContainer, $doDisplay = TRUE )
	{
		//
		// Inform.
		//
		if( $doDisplay )
		{
			echo( "\n".__FUNCTION__."\n" );
			echo( "------------------\n" );
		}
		
		//
		// Init local storage.
		//
		$components = array
		(
			array( 'id'	=> kTAG_REFERENCE_SYNONYM,
				   'syn' => 'kTAG_REFERENCE_SYNONYM',
				   'car' => 'kCARD_ANY',
				   'ns'	 => TRUE,
				   'nam' => 'Synonym',
				   'def' => 'This term represents a synonym. A synonym is a string that '
				   		   .'can be used as a substitute to the term, it may be of several '
				   		   .'kinds: exact, broad, narrow and related.' ),
			array( 'id'	=> kTAG_REFERENCE_XREF,
				   'syn' => 'kTAG_REFERENCE_XREF',
				   'car' => 'kCARD_ANY',
				   'ns'	 => TRUE,
				   'nam' => 'Cross-reference',
				   'def' => 'This term represents a cross-reference. A cross-reference '
				   		   .'is a reference to another term in the same container, a sort '
				   		   .'of synonym, except that it is not a string, but a reference '
				   		   .'to another term object. Cross-references can be of several '
				   		   .'kinds: exact, broad, narrow and related.' ),
			array( 'id'	=> kTAG_REFERENCE_ID,
				   'syn' => 'kTAG_REFERENCE_ID',
				   'car' => 'kCARD_1',
				   'ns'	 => FALSE,
				   'nam' => 'Identifier reference',
				   'def' => 'This term represents an object unique identifier within '
				   		   .'an object reference.' ),
			array( 'id'	=> kTAG_REFERENCE_CONTAINER,
				   'syn' => 'kTAG_REFERENCE_CONTAINER',
				   'car' => 'kCARD_0_1',
				   'typ' => 'kTYPE_STRING',
				   'ns'	 => FALSE,
				   'nam' => 'Container reference',
				   'def' => 'This term represents a container within an object '
				   		   .'reference.' ),
			array( 'id'	=> kTAG_REFERENCE_DATABASE,
				   'syn' => 'kTAG_REFERENCE_DATABASE',
				   'car' => 'kCARD_0_1',
				   'typ' => 'kTYPE_STRING',
				   'ns'	 => FALSE,
				   'nam' => 'Database reference',
				   'def' => 'This term represents a database within an object reference.' )
		);
		
		//
		// Save common namespace.
		//
		$namespace = new COntologyTerm( $theContainer, COntologyTerm::HashIndex( '' ) );
		
		//
		// Get namespace length.
		//
		$len = strlen( $namespace ) + 1;
		
		//
		// Load terms.
		//
		foreach( $components as $component )
		{
			//
			// Create term.
			//
			$term = new COntologyTerm( $theContainer, 
									   COntologyTerm::HashIndex( $component[ 'id' ] ) );
			if( ! $term->Persistent() )
			{
				if( $component[ 'ns' ] )
				{
					$term->NS( $namespace );
					$term->Code( substr( $component[ 'id' ], $len ) );
				}
				else
					$term->Code( $component[ 'id' ] );
				$term->Kind( kTYPE_ATTRIBUTE, TRUE );
				$term->Cardinality( $component[ 'car' ] );
				if( array_key_exists( 'typ', $component ) )
					$term->Type( $component[ 'typ' ] );
				$term->Name( $component[ 'nam' ], kDEFAULT_LANGUAGE );
				$term->Definition( $component[ 'def' ], kDEFAULT_LANGUAGE );
				$term->Synonym( $component[ 'syn' ], kTYPE_EXACT, TRUE );
				$term->Commit( $theContainer );
				
				//
				// Display.
				//
				if( $doDisplay )
					echo( "[$term] (".$component[ 'syn' ].") "
						 .$term->Name( NULL, kDEFAULT_LANGUAGE )
						 ."\n" );
			}
		}
	
	} // LoadReferenceTerms.

	 
	/*===================================================================================
	 *	LoadAttributeTerms																*
	 *==================================================================================*/

	/**
	 * Load reference terms.
	 *
	 * This function will load all default reference terms.
	 *
	 * If the last parameter is <i>TRUE</i>, the function will display the name of the
	 * created terms.
	 *
	 * @param CContainer			$theContainer		Collection.
	 * @param boolean				$doDisplay			Display created terms.
	 *
	 * @access protected
	 */
	function LoadAttributeTerms( CContainer $theContainer, $doDisplay = TRUE )
	{
		//
		// Inform.
		//
		if( $doDisplay )
		{
			echo( "\n".__FUNCTION__."\n" );
			echo( "------------------\n" );
		}
		
		//
		// Init local storage.
		//
		$components = array
		(
			array( 'id'	=> kTAG_CLASS,
				   'syn' => 'kTAG_CLASS',
				   'car' => 'kCARD_1',
				   'typ' => 'kTYPE_STRING',
				   'nam' => 'Class',
				   'def' => 'This term represents a class name.' ),
			array( 'id'	=> kTAG_CREATED,
				   'syn' => 'kTAG_CREATED',
				   'car' => 'kCARD_0_1',
				   'typ' => 'kTYPE_STAMP',
				   'nam' => 'Created',
				   'def' => 'This term represents a creation time-stamp.' ),
			array( 'id'	=> kTAG_MODIFIED,
				   'syn' => 'kTAG_MODIFIED',
				   'car' => 'kCARD_0_1',
				   'typ' => 'kTYPE_STAMP',
				   'nam' => 'Modified',
				   'def' => 'This term represents a last modification time-stamp.' ),
			array( 'id'	=> kTAG_VERSION,
				   'syn' => 'kTAG_VERSION',
				   'car' => 'kCARD_1',
				   'typ' => 'kTYPE_INT32',
				   'nam' => 'Version counter',
				   'def' => 'This term represents a version counter which is '
				   		   .'automatically incremented each time the object '
				   		   .'is committed.' ),
			array( 'id'	=> kOFFSET_VERSION,
				   'syn' => 'kOFFSET_VERSION',
				   'car' => 'kCARD_0_1',
				   'typ' => 'kTYPE_STRING',
				   'nam' => 'Version',
				   'def' => 'This term represents a version.' ),
			array( 'id'	=> kTAG_TYPE,
				   'syn' => 'kTAG_TYPE',
				   'car' => 'kCARD_0_1',
				   'typ' => 'kTYPE_ENUM',
				   'nam' => 'Type',
				   'def' => 'This term represents a type, in general this is used to '
				   		   .'indicate the data type of an object.' ),
			array( 'id'	=> kTAG_PATTERN,
				   'syn' => 'kTAG_PATTERN',
				   'car' => 'kCARD_ANY',
				   'typ' => 'kTYPE_STRING',
				   'nam' => 'Pattern',
				   'def' => 'This term represents a pattern attribute, in general this '
				   		   .'is used to represent the structure, composition and '
				   		   .'formatting rules of a string data type.' ),
			array( 'id'	=> kTAG_KIND,
				   'syn' => 'kTAG_KIND',
				   'car' => 'kCARD_ANY',
				   'typ' => 'kTYPE_ENUM',
				   'nam' => 'Kind',
				   'def' => 'This term represents a kind, in general this is used '
				   		   .'to qualify an object. This should not be confused with '
				   		   .'the data type.' ),
			array( 'id'	=> kTAG_DOMAIN,
				   'syn' => 'kTAG_DOMAIN',
				   'car' => 'kCARD_ANY',
				   'typ' => 'kTYPE_ENUM',
				   'nam' => 'Domain',
				   'def' => 'This term represents a domain attribute, in general this '
				   		   .'is used to represent the nature of the current object '
				   		   .'instance.' ),
			array( 'id'	=> kTAG_CATEGORY,
				   'syn' => 'kTAG_CATEGORY',
				   'car' => 'kCARD_ANY',
				   'typ' => 'kTYPE_ENUM',
				   'nam' => 'Category',
				   'def' => 'This term represents a category attribute, in general '
				   		   .'this is used to represent the area to which the current '
				   		   .'object instance belongs to.' ),
			array( 'id'	=> kTAG_CARDINALITY,
				   'syn' => 'kTAG_CARDINALITY',
				   'car' => 'kCARD_0_1',
				   'typ' => 'kTYPE_REF',
				   'nam' => 'Cardinality',
				   'def' => 'This term indicating the cardinality of a data attribute.' ),
			array( 'id'	=> kTAG_UNIT,
				   'syn' => 'kTAG_UNIT',
				   'car' => 'kCARD_1',
				   'typ' => 'kTYPE_REF',
				   'nam' => 'Unit',
				   'def' => 'This term is used to indicate the unit of a measure.' ),
			array( 'id'	=> kTAG_SOURCE,
				   'syn' => 'kTAG_SOURCE',
				   'car' => 'kCARD_0_1',
				   'typ' => 'kTYPE_STRING',
				   'nam' => 'Source',
				   'def' => 'This term is used to indicate the source of an object.' ),
			array( 'id'	=> kTAG_DATA,
				   'syn' => 'kTAG_DATA',
				   'car' => 'kCARD_1',
				   'nam' => 'Data',
				   'def' => 'This term is used to indicate the data part of a '
				   		   .'structured object.' ),
			array( 'id'	=> kTAG_CODE,
				   'syn' => 'kTAG_CODE',
				   'car' => 'kCARD_1',
				   'typ' => 'kTYPE_STRING',
				   'nam' => 'Code',
				   'def' => 'This term is used to indicate a code or acronym.' ),
			array( 'id'	=> kTAG_ENUM,
				   'syn' => 'kTAG_ENUM',
				   'car' => 'kCARD_ANY',
				   'typ' => 'kTYPE_STRING',
				   'nam' => 'Enumeration',
				   'def' => 'This term is used to indicate an enumerated code or key.' ),
			array( 'id'	=> kTAG_NAMESPACE,
				   'syn' => 'kTAG_NAMESPACE',
				   'car' => 'kCARD_1',
				   'typ' => 'kTYPE_STRING',
				   'nam' => 'Namespace term',
				   'def' => 'This term is used to indicate a namespace term reference.' ),
			array( 'id'	=> kOFFSET_NAMESPACE,
				   'syn' => 'kOFFSET_NAMESPACE',
				   'car' => 'kCARD_0_1',
				   'typ' => 'kTYPE_STRING',
				   'nam' => 'Namespace name',
				   'def' => 'This term is used to indicate a namespace name or acronym.' ),
			array( 'id'	=> kOFFSET_IMAGE,
				   'syn' => 'kOFFSET_IMAGE',
				   'car' => 'kCARD_ANY',
				   'typ' => 'kTYPE_STRING',
				   'nam' => 'Image',
				   'def' => 'This term is used to indicate an image list.' ),
			array( 'id'	=> kOFFSET_FILE,
				   'syn' => 'kOFFSET_FILE',
				   'car' => 'kCARD_0_1',
				   'nam' => 'File',
				   'def' => 'This term is used to indicate a file reference.' ),
			array( 'id'	=> kOFFSET_FILES,
				   'syn' => 'kOFFSET_FILES',
				   'car' => 'kCARD_ANY',
				   'nam' => 'Files list',
				   'def' => 'This term is used to indicate a list of files.' ),
			array( 'id'	=> kOFFSET_COLS,
				   'syn' => 'kOFFSET_COLS',
				   'car' => 'kCARD_ANY',
				   'nam' => 'Columns list',
				   'def' => 'This term is used to indicate a list of columns.' ),
			array( 'id'	=> kTAG_TERM,
				   'syn' => 'kTAG_TERM',
				   'car' => 'kCARD_1',
				   'typ' => 'kTYPE_STRING',
				   'nam' => 'Term',
				   'def' => 'This term is used to indicate a graph node term.' ),
			array( 'id'	=> kTAG_TAG,
				   'syn' => 'kTAG_TAG',
				   'car' => 'kCARD_1',
				   'typ' => 'kTYPE_STRING',
				   'nam' => 'Tag',
				   'def' => 'This term is used to indicate a tag or an annotation term.' ),
			array( 'id'	=> kTAG_NODE,
				   'syn' => 'kTAG_NODE',
				   'car' => 'kCARD_ANY',
				   'typ' => 'kTYPE_INT64',
				   'nam' => 'Node',
				   'def' => 'This term is used to indicate a graph nodes list.' ),
			array( 'id'	=> kTAG_EDGE,
				   'syn' => 'kTAG_EDGE',
				   'car' => 'kCARD_ANY',
				   'typ' => 'kTYPE_INT64',
				   'nam' => 'Edge',
				   'def' => 'This term is used to indicate an edge node.' ),
			array( 'id'	=> kTAG_SUBJECT,
				   'syn' => 'kTAG_SUBJECT',
				   'car' => 'kCARD_0_1',
				   'nam' => 'Subject',
				   'def' => 'This tag is used as the default offset for indicating '
				   		   .'a subject term or node.' ),
			array( 'id'	=> kTAG_PREDICATE,
				   'syn' => 'kTAG_PREDICATE',
				   'car' => 'kCARD_0_1',
				   'nam' => 'Predicate',
				   'def' => 'This tag is used as the default offset for indicating '
				   		   .'a predicate term or node.' ),
			array( 'id'	=> kTAG_OBJECT,
				   'syn' => 'kTAG_OBJECT',
				   'car' => 'kCARD_0_1',
				   'nam' => 'Object',
				   'def' => 'This tag is used as the default offset for indicating '
				   		   .'an object term or node.' ),
			array( 'id'	=> kTAG_PATH,
				   'syn' => 'kTAG_PATH',
				   'car' => 'kCARD_0_1',
				   'typ' => 'kTYPE_STRING',
				   'nam' => 'Path',
				   'def' => 'This tag is used as the default offset for indicating '
				   		   .'a path.' ),
			array( 'id'	=> kTAG_TITLE,
				   'syn' => 'kTAG_TITLE',
				   'car' => 'kCARD_0_1',
				   'typ' => 'kTYPE_STRING',
				   'nam' => 'Title',
				   'def' => 'This tag is used as the default offset for indicating '
				   		   .'a title or name.' ),
			array( 'id'	=> kTAG_NAME,
				   'syn' => 'kTAG_NAME',
				   'car' => 'kCARD_ANY',
				   'nam' => 'Name',
				   'def' => 'This term is used to indicate a name or label.' ),
			array( 'id'	=> kTAG_DESCRIPTION,
				   'syn' => 'kTAG_DESCRIPTION',
				   'car' => 'kCARD_ANY',
				   'nam' => 'Description',
				   'def' => 'This term is used to indicate a description or long label.' ),
			array( 'id'	=> kTAG_DEFINITION,
				   'syn' => 'kTAG_DEFINITION',
				   'car' => 'kCARD_ANY',
				   'nam' => 'Definition',
				   'def' => 'This term is used to indicate a definition.' ),
			array( 'id'	=> kTAG_EXAMPLES,
				   'syn' => 'kTAG_EXAMPLES',
				   'car' => 'kCARD_ANY',
				   'nam' => 'Examples',
				   'def' => 'This term is used as the default offset for indicating '
				   		   .'an attribute containing a list of examples.' ),
			array( 'id'	=> kTAG_LANGUAGE,
				   'syn' => 'kTAG_LANGUAGE',
				   'car' => 'kCARD_1',
				   'typ' => 'kTYPE_ENUM',
				   'nam' => 'Language',
				   'def' => 'This term is used to indicate a language.' ),
			array( 'id'	=> kTAG_STATUS,
				   'syn' => 'kTAG_STATUS',
				   'car' => 'kCARD_1',
				   'typ' => 'kTYPE_ENUM',
				   'nam' => 'Status',
				   'def' => 'This term is used to indicate a status or state.' ),
			array( 'id'	=> kTAG_STATE,
				   'syn' => 'kTAG_STATE',
				   'car' => 'kCARD_1',
				   'typ' => 'kTYPE_ENUM',
				   'nam' => 'State',
				   'def' => 'This term is used to indicate a state.' ),
			array( 'id'	=> kTAG_ROLE,
				   'syn' => 'kTAG_ROLE',
				   'car' => 'kCARD_ANY',
				   'typ' => 'kTYPE_ENUM',
				   'nam' => 'Role',
				   'def' => 'This term is used to indicate a role, function or capability '
				   		   .'it is generally a list of enumerations declaring which '
				   		   .'functions a user is allowed to perform.' ),
			array( 'id'	=> kTAG_ANNOTATION,
				   'syn' => 'kTAG_ANNOTATION',
				   'car' => 'kCARD_ANY',
				   'typ' => 'kTYPE_STRING',
				   'nam' => 'Annotation',
				   'def' => 'This term is used to indicate an annotation, attachment '
				   		   .'or comment.' ),
			array( 'id'	=> kTAG_REFS,
				   'syn' => 'kTAG_REFS',
				   'car' => 'kCARD_ANY',
				   'nam' => 'References',
				   'def' => 'This term represents the list of references of an object, '
				   		   .'it describes a list of predicate/object pairs.' ),
			array( 'id'	=> kTAG_COUNT,
				   'syn' => 'kTAG_COUNT',
				   'car' => 'kCARD_0_1',
				   'typ' => 'kTYPE_INT64',
				   'nam' => 'Count',
				   'def' => 'This term represents a generic count.' ),
			array( 'id'	=> kTAG_REF_COUNT,
				   'syn' => 'kTAG_REF_COUNT',
				   'car' => 'kCARD_1',
				   'typ' => 'kTYPE_INT64',
				   'nam' => 'References count',
				   'def' => 'This term represents the count of references of an object, '
				   		   .'it indicates.' ),
			array( 'id'	=> kTAG_TAGS,
				   'syn' => 'kTAG_TAGS',
				   'car' => 'kCARD_ANY',
				   'typ' => 'kTYPE_STRING',
				   'nam' => 'Tags',
				   'def' => 'This term represents the list of attribute terms used '
				   		   .'in the object.' ),
			array( 'id'	=> kTAG_DTAGS,
				   'syn' => 'kTAG_DTAGS',
				   'car' => 'kCARD_ANY',
				   'typ' => 'kTYPE_STRING',
				   'nam' => 'Data tags',
				   'def' => 'This term represents the list of data tags that reference '
				   		   .'the current object.' ),
			array( 'id'	=> kTAG_EDGE_TERM,
				   'syn' => 'kTAG_EDGE_TERM',
				   'car' => 'kCARD_1',
				   'typ' => 'kTYPE_STRING',
				   'nam' => 'Edge terms path',
				   'def' => 'This term represents a graph edge node by using its '
				 		   .'related terms as a path in the form of a string containing '
				 		   .'the SUBJECT/PREDICATE/OBJECT path constituted by the term '
				 		   .'identifier elements.' ),
			array( 'id'	=> kTAG_EDGE_NODE,
				   'syn' => 'kTAG_EDGE_NODE',
				   'car' => 'kCARD_1',
				   'typ' => 'kTYPE_STRING',
				   'nam' => 'Edge nodes path',
				   'def' => 'This term represents a graph edge node by using its related '
				   		   .'nodes and predicate term as a path in the form of a string '
				   		   .'containing the SUBJECT/PREDICATE/OBJECT</i> path in which '
				   		   .'the subject and object elements are represented by the '
				   		   .'respective node identifiers, and the predicate element '
		 				   .'is represented by the edge term identifier.' ),
			array( 'id'	=> kTAG_DEFAULT,
				   'syn' => 'kTAG_DEFAULT',
				   'car' => 'kCARD_0_1',
				   'typ' => 'kTYPE_REF',
				   'nam' => 'Default',
				   'def' => 'This term represents a reference to the default object, '
				   		   .'there are cases in which an object is interchangeable '
				   		   .'with many others, such as in equivalent enumerations: '
				   		   .'in this case we can use this tag to point to the default '
				   		   .'or in-use instance.' ),
			array( 'id'	=> kTAG_PREFERRED,
				   'syn' => 'kTAG_PREFERRED',
				   'car' => 'kCARD_0_1',
				   'typ' => 'kTYPE_REF',
				   'nam' => 'Preferred',
				   'def' => 'This term represents a reference to the preferred object, '
				   		   .'there are cases in which an object is obsolete, but still '
				   		   .'in use, in this case this attribute should point to the '
				   		   .'preferred object.' ),
			array( 'id'	=> kTAG_VALID,
				   'syn' => 'kTAG_VALID',
				   'car' => 'kCARD_0_1',
				   'typ' => 'kTYPE_REF',
				   'nam' => 'Valid',
				   'def' => 'This term represents a reference to the valid object, there '
				   		   .'are cases in which deleting an object is not an option, in '
				   		   .'such cases the invalid or obsolete object points to the valid '
				   		   .'object through this term.' ),
			array( 'id'	=> kTAG_PROVIDED,
				   'syn' => 'kTAG_PROVIDED',
				   'car' => 'kCARD_0_1',
				   'nam' => 'Provided',
				   'def' => 'This term references a provided object, as opposed to a '
				   		   .'generated object.' ),
			array( 'id'	=> kTAG_GENERATED,
				   'syn' => 'kTAG_GENERATED',
				   'car' => 'kCARD_0_1',
				   'nam' => 'Generated',
				   'def' => 'This term references a generated object, as opposed to a '
				   		   .'provided object.' ),
			array( 'id'	=> kTAG_OWNER,
				   'syn' => 'kTAG_OWNER',
				   'car' => 'kCARD_0_1',
				   'nam' => 'Owner',
				   'def' => 'This term references the object that owns, '
						   .'controls or generated  the current one.' ),
			array( 'id'	=> kTAG_IN,
				   'syn' => 'kTAG_IN',
				   'car' => 'kCARD_0_1',
				   'typ' => 'kTYPE_REF',
				   'nam' => 'Incoming',
				   'def' => 'This term represents the incoming direction, it can be used '
				   		   .'for tagging items that point to the current object.' ),
			array( 'id'	=> kTAG_OUT,
				   'syn' => 'kTAG_OUT',
				   'car' => 'kCARD_0_1',
				   'typ' => 'kTYPE_REF',
				   'nam' => 'Outgoing',
				   'def' => 'This term represents the outgoing direction, it can be used '
				   		   .'for tagging items to which the current object points to.' ),
			array( 'id'	=> kTAG_MANAGER,
				   'syn' => 'kTAG_MANAGER',
				   'car' => 'kCARD_0_1',
				   'typ' => 'kTYPE_REF',
				   'nam' => 'Manager',
				   'def' => 'This term is used to indicate the manager or creator of '
				   		   .'current object.' ),
			array( 'id'	=> kTAXON_RANK,
				   'syn' => 'kTAXON_RANK',
				   'car' => 'kCARD_1',
				   'typ' => 'kTYPE_ENUM',
				   'nam' => 'Rank',
				   'def' => 'This term represents a taxonomic rank.' ),
			array( 'id'	=> kTAXON_EPITHET,
				   'syn' => 'kTAXON_EPITHET',
				   'car' => 'kCARD_1',
				   'typ' => 'kTYPE_STRING',
				   'nam' => 'Epithet',
				   'def' => 'This term represents a taxon epithet.' ),
			array( 'id'	=> kTAXON_AUTHORITY,
				   'syn' => 'kTAXON_AUTHORITY',
				   'car' => 'kCARD_1',
				   'typ' => 'kTYPE_STRING',
				   'nam' => 'Authority',
				   'def' => 'This term represents a taxon name authority.' ),
			array( 'id'	=> kTAXON_NAME,
				   'syn' => 'kTAXON_NAME',
				   'car' => 'kCARD_0_1',
				   'typ' => 'kTYPE_STRING',
				   'nam' => 'Taxon',
				   'def' => 'This term represents a full taxon epithet.' ),
			array( 'id'	=> kIMAGE_THUMB_FLAG,
				   'syn' => 'kIMAGE_THUMB_FLAG',
				   'car' => 'kCARD_0_1',
				   'typ' => 'kTYPE_PNG',
				   'nam' => 'Flag thumbnail',
				   'def' => 'A flag is the image of a flag or an icon symbol representing '
				   		   .'an object, the thumbnail flag is a small sized version of '
				   		   .'this image.' ),
			array( 'id'	=> kIMAGE_MED_FLAG,
				   'syn' => 'kIMAGE_MED_FLAG',
				   'car' => 'kCARD_0_1',
				   'typ' => 'kTYPE_PNG',
				   'nam' => 'Flag image',
				   'def' => 'A flag is the image of a flag or an icon symbol representing '
				   		   .'an object, the medium flag is a medium sized version of '
				   		   .'this image.' ),
			array( 'id'	=> kIMAGE_VECT_FLAG,
				   'syn' => 'kIMAGE_VECT_FLAG',
				   'car' => 'kCARD_0_1',
				   'typ' => 'kTYPE_SVG',
				   'nam' => 'Flag vector image',
				   'def' => 'A flag is the image of a flag or an icon symbol representing '
				   		   .'an object, the vector flag is a vector version of this image '
				   		   .'which can be resized at will.' ),
			array( 'id'	=> kENTITY_INST_FAO_EPACRONYM,
				   'syn' => 'kENTITY_INST_FAO_EPACRONYM',
				   'ns' => 'ECPGR',
				   'car' => 'kCARD_0_1',
				   'typ' => 'kTYPE_STRING',
				   'nam' => 'ECPGR institute acronym',
				   'def' => 'ECPGR institute acronym.' ),
			array( 'id'	=> kENTITY_INST_FAO_TYPE,
				   'syn' => 'kENTITY_INST_FAO_TYPE',
				   'ns' => 'FAO:INST',
				   'car' => 'kCARD_0_1',
				   'typ' => 'kTYPE_STRING',
				   'nam' => 'FAO/WIEWS institute types set',
				   'def' => 'FAO/WIEWS institute types enumeration set.' ),
			array( 'id'	=> kENTITY_INST_FAO_LAT,
				   'syn' => 'kENTITY_INST_FAO_LAT',
				   'ns' => 'FAO:INST',
				   'car' => 'kCARD_0_1',
				   'typ' => 'kTYPE_INT32',
				   'nam' => 'FAO/WIEWS institute latitude',
				   'def' => 'FAO/WIEWS institute latitude.' ),
			array( 'id'	=> kENTITY_INST_FAO_LON,
				   'syn' => 'kENTITY_INST_FAO_LON',
				   'ns' => 'FAO:INST',
				   'car' => 'kCARD_0_1',
				   'typ' => 'kTYPE_INT32',
				   'nam' => 'FAO/WIEWS institute longitude',
				   'def' => 'FAO/WIEWS institute longitude.' ),
			array( 'id'	=> kENTITY_INST_FAO_ALT,
				   'syn' => 'kENTITY_INST_FAO_ALT',
				   'ns' => 'FAO:INST',
				   'car' => 'kCARD_0_1',
				   'typ' => 'kTYPE_INT32',
				   'nam' => 'FAO/WIEWS institute elevation',
				   'def' => 'FAO/WIEWS institute elevation.' ),
			array( 'id'	=> kENTITY_INST_FAO_ACT_PGR,
				   'syn' => 'kENTITY_INST_FAO_ACT_PGR',
				   'ns' => 'FAO:INST',
				   'car' => 'kCARD_0_1',
				   'typ' => 'kTYPE_STRING',
				   'nam' => 'FAO/WIEWS institute PGR activity enumeration',
				   'def' => 'This enumeration indicates that the institute manages '
						   .'plant genetic resources.' ),
			array( 'id'	=> kENTITY_INST_FAO_ACT_COLL,
				   'syn' => 'kENTITY_INST_FAO_ACT_COLL',
				   'ns' => 'FAO:INST',
				   'car' => 'kCARD_0_1',
				   'typ' => 'kTYPE_STRING',
				   'nam' => 'FAO/WIEWS institute collection management enumeration',
				   'def' => 'This enumeration indicates that the institute manages a '
				   		   .'germplasm collection.' ),
		);
		
		//
		// Save common namespace.
		//
		$namespace = new COntologyTerm( $theContainer, COntologyTerm::HashIndex( '' ) );
		
		//
		// Get namespace length.
		//
		$len = strlen( $namespace ) + 1;
		
		//
		// Load terms.
		//
		foreach( $components as $component )
		{
			//
			// Create term.
			//
			$term = new COntologyTerm( $theContainer, 
									   COntologyTerm::HashIndex( $component[ 'id' ] ) );
			if( ! $term->Persistent() )
			{
				if( array_key_exists( 'ns', $component ) )
				{
					$ns = new COntologyTerm(
							$theContainer, COntologyTerm::HashIndex(
								$component[ 'ns' ] ) );
					$term->NS( $ns );
					$term->Code( substr( $component[ 'id' ], strlen( $ns ) + 1 ) );
				}
				else
				{
					$term->NS( $namespace );
					$term->Code( substr( $component[ 'id' ], $len ) );
				}
				$term->Kind( kTYPE_ATTRIBUTE, TRUE );
				if( array_key_exists( 'car', $component ) )
					$term->Cardinality( $component[ 'car' ] );
				if( array_key_exists( 'typ', $component ) )
					$term->Type( $component[ 'typ' ] );
				$term->Name( $component[ 'nam' ], kDEFAULT_LANGUAGE );
				$term->Definition( $component[ 'def' ], kDEFAULT_LANGUAGE );
				$term->Synonym( $component[ 'syn' ], kTYPE_EXACT, TRUE );
				$term->Commit( $theContainer );
				
				//
				// Display.
				//
				if( $doDisplay )
					echo( "[$term] (".$component[ 'syn' ].") "
						 .$term->Name( NULL, kDEFAULT_LANGUAGE )
						 ."\n" );
			}
		}
	
	} // LoadAttributeTerms.

	 
	/*===================================================================================
	 *	LoadPropertyTerms																*
	 *==================================================================================*/

	/**
	 * Load property terms.
	 *
	 * This function will load all default property terms.
	 *
	 * If the last parameter is <i>TRUE</i>, the function will display the name of the
	 * created terms.
	 *
	 * @param CContainer			$theContainer		Collection.
	 * @param boolean				$doDisplay			Display created terms.
	 *
	 * @access protected
	 */
	function LoadPropertyTerms( CContainer $theContainer, $doDisplay = TRUE )
	{
		//
		// Inform.
		//
		if( $doDisplay )
		{
			echo( "\n".__FUNCTION__."\n" );
			echo( "------------------\n" );
		}
		
		//
		// Init local storage.
		//
		$components = array
		(
			array( 'id'	=> kOFFSET_PASSWORD,
				   'syn' => 'kOFFSET_PASSWORD',
				   'car' => 'kCARD_1',
				   'typ' => 'kTYPE_STRING',
				   'nam' => 'Password',
				   'def' => 'This term represents a password.' ),
			array( 'id'	=> kOFFSET_MAIL,
				   'syn' => 'kOFFSET_MAIL',
				   'car' => 'kCARD_ANY',
				   'nam' => 'Mail',
				   'def' => 'This term represents a mailing address.' ),
			array( 'id'	=> kOFFSET_EMAIL,
				   'syn' => 'kOFFSET_EMAIL',
				   'car' => 'kCARD_1',
				   'typ' => 'kTYPE_STRING',
				   'nam' => 'E-mail',
				   'def' => 'This term represents an e-mail address.' ),
			array( 'id'	=> kOFFSET_PHONE,
				   'syn' => 'kOFFSET_PHONE',
				   'car' => 'kCARD_ANY',
				   'typ' => 'kTYPE_STRING',
				   'nam' => 'Phone',
				   'def' => 'This term represents a telephone number.' ),
			array( 'id'	=> kOFFSET_FAX,
				   'syn' => 'kOFFSET_FAX',
				   'car' => 'kCARD_ANY',
				   'typ' => 'kTYPE_STRING',
				   'nam' => 'Fax',
				   'def' => 'This term represents a telefax number.' ),
			array( 'id'	=> kOFFSET_URL,
				   'syn' => 'kOFFSET_URL',
				   'car' => 'kCARD_ANY',
				   'typ' => 'kTYPE_STRING',
				   'nam' => 'URL',
				   'def' => 'This term represents an URL or internet web address.' ),
			array( 'id'	=> kOFFSET_ACRONYM,
				   'syn' => 'kOFFSET_ACRONYM',
				   'car' => 'kCARD_ANY',
				   'typ' => 'kTYPE_STRING',
				   'nam' => 'Acronym',
				   'def' => 'This term represents an acronym or abbreviation.' )
		);
		
		//
		// Save common namespace.
		//
		$namespace = new COntologyTerm( $theContainer, COntologyTerm::HashIndex( '' ) );
		
		//
		// Get namespace length.
		//
		$len = strlen( $namespace ) + 1;
		
		//
		// Load terms.
		//
		foreach( $components as $component )
		{
			//
			// Create term.
			//
			$term = new COntologyTerm( $theContainer, 
									   COntologyTerm::HashIndex( $component[ 'id' ] ) );
			if( ! $term->Persistent() )
			{
				$term->NS( $namespace );
				$term->Code( substr( $component[ 'id' ], $len ) );
				$term->Kind( kTYPE_ATTRIBUTE, TRUE );
				$term->Cardinality( $component[ 'car' ] );
				if( array_key_exists( 'typ', $component ) )
					$term->Type( $component[ 'typ' ] );
				$term->Name( $component[ 'nam' ], kDEFAULT_LANGUAGE );
				$term->Definition( $component[ 'def' ], kDEFAULT_LANGUAGE );
				$term->Synonym( $component[ 'syn' ], kTYPE_EXACT, TRUE );
				$term->Commit( $theContainer );
				
				//
				// Display.
				//
				if( $doDisplay )
					echo( "[$term] (".$component[ 'syn' ].") "
						 .$term->Name( NULL, kDEFAULT_LANGUAGE )
						 ."\n" );
			}
		}
	
	} // LoadPropertyTerms.

	 
	/*===================================================================================
	 *	LoadMailProperties																*
	 *==================================================================================*/

	/**
	 * Load mail property terms.
	 *
	 * This function will load all default mailing address property terms.
	 *
	 * If the last parameter is <i>TRUE</i>, the function will display the name of the
	 * created terms.
	 *
	 * @param CContainer			$theContainer		Collection.
	 * @param boolean				$doDisplay			Display created terms.
	 *
	 * @access protected
	 */
	function LoadMailProperties( CContainer $theContainer, $doDisplay = TRUE )
	{
		//
		// Inform.
		//
		if( $doDisplay )
		{
			echo( "\n".__FUNCTION__."\n" );
			echo( "------------------\n" );
		}
		
		//
		// Init local storage.
		//
		$components = array
		(
			array( 'id'	=> kOFFSET_PLACE,
				   'syn' => 'kOFFSET_PLACE',
				   'car' => 'kCARD_0_1',
				   'typ' => 'kTYPE_STRING',
				   'nam' => 'Place',
				   'def' => 'This term represents a place or named location part '
				   		   .'of a mailing address.' ),
			array( 'id'	=> kOFFSET_CARE,
				   'syn' => 'kOFFSET_CARE',
				   'car' => 'kCARD_0_1',
				   'typ' => 'kTYPE_STRING',
				   'nam' => 'Care of',
				   'def' => 'This term represents the care of part of a mailing address.' ),
			array( 'id'	=> kOFFSET_STREET,
				   'syn' => 'kOFFSET_STREET',
				   'car' => 'kCARD_1',
				   'typ' => 'kTYPE_STRING',
				   'nam' => 'Street/P.O. Box',
				   'def' => 'This term represents the street or P.O. Box part of a '
				   		   .'mailing address.' ),
			array( 'id'	=> kOFFSET_ZIP_CODE,
				   'syn' => 'kOFFSET_ZIP_CODE',
				   'car' => 'kCARD_1',
				   'typ' => 'kTYPE_STRING',
				   'nam' => 'Zip',
				   'def' => 'This term represents the zip code part of a mailing '
				   		   .'address.' ),
			array( 'id'	=> kOFFSET_CITY,
				   'syn' => 'kOFFSET_CITY',
				   'car' => 'kCARD_1',
				   'typ' => 'kTYPE_STRING',
				   'nam' => 'City',
				   'def' => 'This term represents the city part of a mailing address.' ),
			array( 'id'	=> kOFFSET_PROVINCE,
				   'syn' => 'kOFFSET_PROVINCE',
				   'car' => 'kCARD_0_1',
				   'typ' => 'kTYPE_STRING',
				   'nam' => 'Province',
				   'def' => 'This term represents the province part of a mailing '
				   		   .'address.' ),
			array( 'id'	=> kOFFSET_COUNTRY,
				   'syn' => 'kOFFSET_COUNTRY',
				   'car' => 'kCARD_1',
				   'typ' => 'kTYPE_ENUM',
				   'nam' => 'Country',
				   'def' => 'This term represents the country of the mailing address, '
				   		   .'in general it should be expressed as an enumerated value.' ),
			array( 'id'	=> kOFFSET_FULL,
				   'syn' => 'kOFFSET_FULL',
				   'car' => 'kCARD_0_1',
				   'typ' => 'kTYPE_STRING',
				   'nam' => 'Full mailing address',
				   'def' => 'This term represents a full mailing address in the '
				   		   .'form of a string.' )
		);
		
		//
		// Save common namespace.
		//
		$namespace = new COntologyTerm( $theContainer, COntologyTerm::HashIndex( '' ) );
		
		//
		// Get namespace length.
		//
		$len = strlen( $namespace ) + 1;
		
		//
		// Load terms.
		//
		foreach( $components as $component )
		{
			//
			// Create term.
			//
			$term = new COntologyTerm( $theContainer, 
									   COntologyTerm::HashIndex( $component[ 'id' ] ) );
			if( ! $term->Persistent() )
			{
				$term->NS( $namespace );
				$term->Code( substr( $component[ 'id' ], $len ) );
				$term->Kind( kTYPE_ATTRIBUTE, TRUE );
				$term->Cardinality( $component[ 'car' ] );
				if( array_key_exists( 'typ', $component ) )
					$term->Type( $component[ 'typ' ] );
				$term->Name( $component[ 'nam' ], kDEFAULT_LANGUAGE );
				$term->Definition( $component[ 'def' ], kDEFAULT_LANGUAGE );
				$term->Synonym( $component[ 'syn' ], kTYPE_EXACT, TRUE );
				$term->Commit( $theContainer );
				
				//
				// Display.
				//
				if( $doDisplay )
					echo( "[$term] (".$component[ 'syn' ].") "
						 .$term->Name( NULL, kDEFAULT_LANGUAGE )
						 ."\n" );
			}
		}
	
	} // LoadMailProperties.

	 
	/*===================================================================================
	 *	LoadDefaultDomains																*
	 *==================================================================================*/

	/**
	 * Load default domains.
	 *
	 * This function will load the default domains ontology.
	 *
	 * If the last parameter is <i>TRUE</i>, the function will display the name of the
	 * created terms.
	 *
	 * @param CContainer			$theContainer		Collection.
	 * @param boolean				$doDisplay			Display created terms.
	 *
	 * @access protected
	 */
	function LoadDefaultDomains( CContainer $theContainer, $doDisplay = TRUE )
	{
		//
		// Init local storage.
		//
		$nodes = Array();
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
		// ENUM-OF.
		//
		$enum_of = new COntologyTerm
						( $theContainer, COntologyTerm::HashIndex( kPRED_ENUM_OF ) );
		
		//
		// Get default namespace.
		//
		$ns = new COntologyTerm( $theContainer, COntologyTerm::HashIndex( '' ) );
		$len = strlen( (string) $ns ) + 1;
		
		//
		// Handle kDEF_DOMAIN term.
		//
		$term = new COntologyTerm( $theContainer, 
								   COntologyTerm::HashIndex( kDEF_DOMAIN ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $ns );
			$term->Code( substr( kDEF_DOMAIN, $len ) );
			$term->Kind( kTYPE_NAMESPACE, TRUE );
			$term->Name( 'Domain', kDEFAULT_LANGUAGE );
			$term->Definition( 'Default domain.', kDEFAULT_LANGUAGE );
			$term->Synonym( kDEF_DOMAIN, kTYPE_EXACT, TRUE );
			$term->Commit( $theContainer );
		}
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_ROOT, TRUE );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Commit( $container );
		//
		// Save node.
		//
		$nodes[ kDEF_DOMAIN ] = $node;
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] (kDEF_DOMAIN) "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
		
		//
		// Change namespace.
		//
		$ns = $term;
		$len = strlen( (string) $ns ) + 1;
		
		//
		// Init local storage.
		//
		$components = array
		(
			array( 'id'	=> kDOMAIN_GERMPLASM,
				   'syn' => 'kDOMAIN_GERMPLASM',
				   'nam' => 'Germplasm',
				   'def' => 'Germplasm, it is a generalised domain comprising all '
				   		   .'kinds of germplasms.' ),
			array( 'id'	=> kDOMAIN_GEOGRAPHY,
				   'syn' => 'kDOMAIN_GEOGRAPHY',
				   'nam' => 'Geography',
				   'def' => 'Geography, it is a generalised domain comprising all '
				   		   .'descriptors related to geographic data.' ),
			array( 'id'	=> kDOMAIN_LANGUAGE,
				   'syn' => 'kDOMAIN_LANGUAGE',
				   'nam' => 'Language',
				   'def' => 'Language, it is a generalised domain comprising all '
				   		   .'descriptors related to languages.' ),
			array( 'id'	=> kDOMAIN_TAXONOMY,
				   'syn' => 'kDOMAIN_TAXONOMY',
				   'nam' => 'Taxonomy',
				   'def' => 'Taxonomy, it is a generalised domain comprising all '
				   		   .'descriptors related to taxonomy.' )
		);
		
		//
		// Load data.
		//
		foreach( $components as $component )
		{
			//
			// Handle term.
			//
			$term = new COntologyTerm( $theContainer, 
									   COntologyTerm::HashIndex( $component[ 'id' ] ) );
			if( ! $term->Persistent() )
			{
				$term->NS( $ns );
				$term->Code( substr( $component[ 'id' ], $len ) );
				$term->Kind( kTYPE_ENUMERATION, TRUE );
				$term->Type( kTYPE_ENUM );
				$term->Name( $component[ 'nam' ], kDEFAULT_LANGUAGE );
				$term->Definition( $component[ 'def' ], kDEFAULT_LANGUAGE );
				$term->Synonym( $component[ 'syn' ], kTYPE_EXACT, TRUE );
				$term->Commit( $theContainer );
			}
			//
			// Handle node.
			//
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_ENUMERATION, TRUE );
			$node->Type( kTYPE_ENUM );
			$node->Domain( $ns, TRUE );
			$node->Commit( $container );
			//
			// Save node.
			//
			$nodes[ $component[ 'id' ] ] = $node;
			//
			// Handle edge.
			//
			$edge = $node->RelateTo( $container, $enum_of, $nodes[ kDEF_DOMAIN ] );
			$edge->Commit( $container );
			//
			// Display.
			//
			if( $doDisplay )
				echo( "[$term] (".$component[ 'syn' ].") "
					 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
					 .$node->Node()->getId()."}"
					 ."\n" );
		}
		
		//
		// Init local storage.
		//
		$components = array
		(
			array( 'id'	=> kDOMAIN_SAMPLE,
				   'syn' => 'kDOMAIN_SAMPLE',
				   'nam' => 'Sample',
				   'def' => 'Germplasm sample, it is a generalised domain comprising '
				   		   .'all descriptors related to germplasm samples.' ),
			array( 'id'	=> kDOMAIN_ACCESSION,
				   'syn' => 'kDOMAIN_ACCESSION',
				   'nam' => 'Accession',
				   'def' => 'Accession, it is a generalised domain comprising all '
				   		   .'descriptors related to germplasm accessions.' ),
			array( 'id'	=> kDOMAIN_SPECIMEN,
				   'syn' => 'kDOMAIN_SPECIMEN',
				   'nam' => 'Specimen',
				   'def' => 'Specimen, it is a generalised domain comprising all '
				   		   .'descriptors related to germplasm specimens; in general '
				   		   .'these will not be living material.' ),
			array( 'id'	=> kDOMAIN_LANDRACE,
				   'syn' => 'kDOMAIN_LANDRACE',
				   'nam' => 'Land-race',
				   'def' => 'Landrace, it is a generalised domain comprising all '
				   		   .'descriptors related to farmer varieties.' ),
			array( 'id'	=> kDOMAIN_POPULATION,
				   'syn' => 'kDOMAIN_POPULATION',
				   'nam' => 'Population',
				   'def' => 'Population, it is a generalised domain comprising all '
				   		   .'descriptors related to in-situ germplasm populations.' )
		);
		
		//
		// Load data.
		//
		foreach( $components as $component )
		{
			//
			// Handle term.
			//
			$term = new COntologyTerm( $theContainer, 
									   COntologyTerm::HashIndex( $component[ 'id' ] ) );
			if( ! $term->Persistent() )
			{
				$term->NS( $ns );
				$term->Code( substr( $component[ 'id' ], $len ) );
				$term->Kind( kTYPE_ENUMERATION, TRUE );
				$term->Type( kTYPE_ENUM );
				$term->Name( $component[ 'nam' ], kDEFAULT_LANGUAGE );
				$term->Definition( $component[ 'def' ], kDEFAULT_LANGUAGE );
				$term->Synonym( $component[ 'syn' ], kTYPE_EXACT, TRUE );
				$term->Commit( $theContainer );
			}
			//
			// Handle node.
			//
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_ENUMERATION, TRUE );
			$node->Type( kTYPE_ENUM );
			$node->Domain( $ns, TRUE );
			$node->Commit( $container );
			//
			// Handle edge.
			//
			$edge = $node->RelateTo( $container, $enum_of, $nodes[ kDOMAIN_GERMPLASM ] );
			$edge->Commit( $container );
			//
			// Display.
			//
			if( $doDisplay )
				echo( "[$term] (".$component[ 'syn' ].") "
					 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
					 .$node->Node()->getId()."}"
					 ."\n" );
		}
		
	} // LoadDefaultDomains.

	 
	/*===================================================================================
	 *	LoadDefaultCategories															*
	 *==================================================================================*/

	/**
	 * Load default categories.
	 *
	 * This function will load the default categories ontology.
	 *
	 * If the last parameter is <i>TRUE</i>, the function will display the name of the
	 * created terms.
	 *
	 * @param CContainer			$theContainer		Collection.
	 * @param boolean				$doDisplay			Display created terms.
	 *
	 * @access protected
	 */
	function LoadDefaultCategories( CContainer $theContainer, $doDisplay = TRUE )
	{
		//
		// Init local storage.
		//
		$nodes = Array();
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
		// ENUM-OF.
		//
		$enum_of = new COntologyTerm
						( $theContainer, COntologyTerm::HashIndex( kPRED_ENUM_OF ) );
		
		//
		// Get default namespace.
		//
		$ns = new COntologyTerm( $theContainer, COntologyTerm::HashIndex( '' ) );
		$len = strlen( (string) $ns ) + 1;
		
		//
		// Handle kDEF_CATEGORY term.
		//
		$term = new COntologyTerm( $theContainer, 
								   COntologyTerm::HashIndex( kDEF_CATEGORY ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $ns );
			$term->Code( substr( kDEF_CATEGORY, $len ) );
			$term->Kind( kTYPE_NAMESPACE, TRUE );
			$term->Name( 'Categories', kDEFAULT_LANGUAGE );
			$term->Definition( 'Default categories.', kDEFAULT_LANGUAGE );
			$term->Synonym( kDEF_CATEGORY, kTYPE_EXACT, TRUE );
			$term->Commit( $theContainer );
		}
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_ROOT, TRUE );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Commit( $container );
		//
		// Save node.
		//
		$nodes[ kDEF_CATEGORY ] = $node;
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] (kDEF_CATEGORY) "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
		
		//
		// Change namespace.
		//
		$ns = $term;
		$len = strlen( (string) $ns ) + 1;
		
		//
		// Init local storage.
		//
		$components = array
		(
			array( 'id'	=> kCATEGORY_PASSPORT,
				   'syn' => 'kCATEGORY_PASSPORT',
				   'nam' => 'Passport',
				   'def' => 'Passport category, it is a generalised category comprising '
				   		   .'all descriptors related to germplasm passport datasets.' ),
			array( 'id'	=> kCATEGORY_CHAR,
				   'syn' => 'kCATEGORY_CHAR',
				   'nam' => 'Characterisation',
				   'def' => 'Characterisation category, it is a generalised category '
				   		   .'comprising all descriptors related to germplasm '
				   		   .'characterisation datasets.' ),
			array( 'id'	=> kCATEGORY_EVAL,
				   'syn' => 'kCATEGORY_EVAL',
				   'nam' => 'Evaluation',
				   'def' => 'Evaluation category, it is a generalised category '
				   		   .'comprising all descriptors related to germplasm evaluation '
				   		   .'trial datasets.' ),
			array( 'id'	=> kCATEGORY_ADMIN,
				   'syn' => 'kCATEGORY_ADMIN',
				   'nam' => 'Administrative units',
				   'def' => 'Administrative units category, it is a generalised '
				   		   .'category comprising all descriptors related to '
				   		   .'administrative units.' ),
			array( 'id'	=> kCATEGORY_GEO,
				   'syn' => 'kCATEGORY_GEO',
				   'nam' => 'Geographic units',
				   'def' => 'Geographic units category, it is a generalised category '
				   		   .'comprising all descriptors related to geographic units.' ),
			array( 'id'	=> kCATEGORY_EPITHET,
				   'syn' => 'kCATEGORY_EPITHET',
				   'nam' => 'Epithet',
				   'def' => 'Epithet category, it is a generalised category comprising all '
						   .'descriptors related to epithets.' ),
			array( 'id'	=> kCATEGORY_AUTH,
				   'syn' => 'kCATEGORY_AUTH',
				   'nam' => 'Authority',
				   'def' => 'Authority category, it is a generalised category comprising '
				   		   .'all descriptors related to authorities.' )
		);
		
		//
		// Load data.
		//
		foreach( $components as $component )
		{
			//
			// Handle term.
			//
			$term = new COntologyTerm( $theContainer, 
									   COntologyTerm::HashIndex( $component[ 'id' ] ) );
			if( ! $term->Persistent() )
			{
				$term->NS( $ns );
				$term->Code( substr( $component[ 'id' ], $len ) );
				$term->Kind( kTYPE_ENUMERATION, TRUE );
				$term->Type( kTYPE_ENUM );
				$term->Name( $component[ 'nam' ], kDEFAULT_LANGUAGE );
				$term->Definition( $component[ 'def' ], kDEFAULT_LANGUAGE );
				$term->Synonym( $component[ 'syn' ], kTYPE_EXACT, TRUE );
				$term->Commit( $theContainer );
			}
			//
			// Handle node.
			//
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_ENUMERATION, TRUE );
			$node->Type( kTYPE_ENUM );
			$node->Domain( $ns, TRUE );
			$node->Commit( $container );
			//
			// Save nodes.
			//
			$nodes[ $component[ 'id' ] ] = $node;
			//
			// Handle edge.
			//
			$edge = $node->RelateTo( $container, $enum_of, $nodes[ kDEF_CATEGORY ] );
			$edge->Commit( $container );
			//
			// Display.
			//
			if( $doDisplay )
				echo( "[$term] (".$component[ 'syn' ].") "
					 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
					 .$node->Node()->getId()."}"
					 ."\n" );
		}
		
	} // LoadDefaultCategories.

	 
	/*===================================================================================
	 *	LoadCropGroupDescriptors															*
	 *==================================================================================*/

	/**
	 * Load crop groups and codes.
	 *
	 * This function will load the default Annex 1 crop groups and codes.
	 *
	 * If the last parameter is <i>TRUE</i>, the function will display the name of the
	 * created terms.
	 *
	 * @param CContainer			$theContainer		Collection.
	 * @param boolean				$doDisplay			Display created terms.
	 *
	 * @access protected
	 */
	function LoadCropGroupDescriptors( CContainer $theContainer, $doDisplay = TRUE )
	{
		//
		// Init local storage.
		//
		$nodes = Array();
		$container = array( kTAG_TERM => $theContainer,
							kTAG_NODE => $_SESSION[ kSESSION_NEO4J ] );
		
		//
		// Open MySQL connection.
		//
		$mysql = NewADOConnection( DEFAULT_ANCILLARY_HOST );
		if( ! $mysql )
			throw new Exception( 'Unable to connect to MySQL.' );				// !@! ==>
		$mysql->Execute( "SET CHARACTER SET 'utf8'" );
		$mysql->setFetchMode( ADODB_FETCH_ASSOC );
		
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
		// PART-OF.
		//
		$part_of
			= new COntologyTerm
						( $theContainer, COntologyTerm::HashIndex( kPRED_PART_OF ) );
		
		//
		// International Treaty on Plant Genetic Resources for Food and Agriculture.
		//
		$term = new COntologyTerm( $theContainer, 
								   COntologyTerm::HashIndex( 'ITPGRFA' ) );
		if( ! $term->Persistent() )
		{
			$term->Code( 'ITPGRFA' );
			$term->Kind( kTYPE_NAMESPACE, TRUE );
			$term->Name
			( 'International Treaty on Plant Genetic Resources descriptor',
			  kDEFAULT_LANGUAGE );
			$term->Definition
			( 'International Treaty on Plant Genetic Resources for Food and Agriculture '
			 .'descriptor.',
			  kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		}
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_ROOT, TRUE );
		$node->Commit( $container );
		//
		// Save node.
		//
		$nodes[ 'ITPGRFA' ] = $node;
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
		
		//
		// GROUP - Crop group codes.
		//
		$term
			= new COntologyTerm(
					$theContainer, 
					COntologyTerm::HashIndex(
						'ITPGRFA'.kTOKEN_NAMESPACE_SEPARATOR.'ANNEX1-CROP-GROUP' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $nodes[ 'ITPGRFA' ]->Term() );
			$term->Code( 'ANNEX1-CROP-GROUP' );
			$term->Type( kTYPE_ENUM );
			$term->Pattern( '[0-9]{3}', TRUE );
			$term->Name
			( 'Crop group code',
			  kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Annex 1 crop group code.',
			  kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		}
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Type( kTYPE_ENUM, TRUE );
		$node->Commit( $container );
		//
		// Save node.
		//
		$nodes[ 'ANNEX1-CROP-GROUP' ] = $node;
		//
		// Handle edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'ITPGRFA' ] );
		$edge->Commit( $container );
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
		
		//
		// Init local storage.
		//
		$components = array
		(
			array( 'id'	=> 'IncludedGenus',
				   'nam' => 'Included genera',
				   'def' => 'Genera included in crop definition.' ),
			array( 'id'	=> 'IncludedSpecies',
				   'nam' => 'Included species',
				   'def' => 'Species included in crop definition.' ),
			array( 'id'	=> 'ExcludedSpecies',
				   'nam' => 'Excluded species',
				   'def' => 'Species excluded in crop definition.' )
		);
		
		//
		// Load data.
		//
		foreach( $components as $component )
		{
			//
			// Handle term.
			//
			$term = new COntologyTerm( $theContainer, 
									   COntologyTerm::HashIndex( $component[ 'id' ] ) );
			if( ! $term->Persistent() )
			{
				$term->NS( $nodes[ 'ITPGRFA' ]->Term() );
				$term->Code( $component[ 'id' ] );
				$term->Kind( kTYPE_ATTRIBUTE, TRUE );
				$term->Type( kTYPE_STRING );
				$term->Cardinality( kCARD_ANY );
				$term->Name( $component[ 'nam' ], kDEFAULT_LANGUAGE );
				$term->Definition( $component[ 'def' ], kDEFAULT_LANGUAGE );
				$term->Commit( $theContainer );
			}
			
			//
			// Display.
			//
			if( $doDisplay )
				echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
					 ." [$term]\n" );
		}

		//
		// Load crop group codes.
		//
		$query = <<<EOT
SELECT
	`Code_Annex1_Groups`.`Code`,
	`Code_Annex1_Groups`.`Label`
FROM
	`Code_Annex1_Groups`
ORDER BY
	`Code_Annex1_Groups`.`Code`
EOT;
		$rs = $mysql->Execute( $query );
		foreach( $rs as $record )
		{
			//
			// Create crop groups.
			//
			$term
				= new COntologyTerm(
						$theContainer, 
						COntologyTerm::HashIndex(
							$nodes[ 'ANNEX1-CROP-GROUP' ]->Term()->GID()
						   .kTOKEN_NAMESPACE_SEPARATOR
						   .$record[ 'Code' ] ) );
			if( ! $term->Persistent() )
			{
				$term->NS( $nodes[ 'ANNEX1-CROP-GROUP' ]->Term() );
				$term->Code( $record[ 'Code' ] );
				$term->Kind( kTYPE_ENUMERATION, TRUE );
				$term->Type( kTYPE_ENUM );
				$term->Name( $record[ 'Label' ], kDEFAULT_LANGUAGE );
				$term->Commit( $theContainer );
			}
			//
			// Handle node.
			//
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_ENUMERATION, TRUE );
			$node->Type( kTYPE_ENUM );
			$node->Commit( $container );
			//
			// Save node.
			//
			$nodes[ $record[ 'Code' ] ] = $node;
			//
			// Display.
			//
			if( $doDisplay )
				echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
					 ." [$term] [".$node->Node()->getId()."]\n" );
		
		} $rs->Close();
		
		//
		// Handle crop group hierarchy.
		//
		$query = <<<EOT
SELECT
	`Code_Annex1_Groups`.`Code`,
	`Code_Annex1_Groups`.`Parent`,
	`Code_Annex1_Groups`.`Label`
FROM
	`Code_Annex1_Groups`
ORDER BY
	`Code_Annex1_Groups`.`Parent`
EOT;
		$rs = $mysql->Execute( $query );
		foreach( $rs as $record )
		{
			//
			// Get parent and child terms.
			//
			$child_term
				= new COntologyTerm(
						$theContainer, 
						COntologyTerm::HashIndex(
							$nodes[ 'ANNEX1-CROP-GROUP' ]->Term()->GID()
						   .kTOKEN_NAMESPACE_SEPARATOR
						   .$record[ 'Code' ] ) );
			if( $record[ 'Parent' ] !== NULL )
				$parent_term
					= new COntologyTerm(
							$theContainer, 
							COntologyTerm::HashIndex(
								$nodes[ 'ANNEX1-CROP-GROUP' ]->Term()->GID()
							   .kTOKEN_NAMESPACE_SEPARATOR
							   .$record[ 'Parent' ] ) );
			else
				$parent_term
					= $nodes[ 'ANNEX1-CROP-GROUP' ]->Term();
			
			//
			// Get parent node.
			//
			if( $record[ 'Parent' ] !== NULL )
				$parent_node = $nodes[ $record[ 'Parent' ] ];
			else
				$parent_node = $nodes[ 'ANNEX1-CROP-GROUP' ];
			
			//
			// Get child node.
			//
			$child_node = $nodes[ $record[ 'Code' ] ];
			
			//
			// Handle edge.
			//
			$edge = $child_node->RelateTo( $container, $enum_of, $parent_node );
			$edge->Commit( $container );
			
			//
			// Display.
			//
			if( $doDisplay )
				echo( "[$child_term] ==> [$parent_term]\n" );
		
		} $rs->Close();
		
		//
		// CROP - Crop codes.
		//
		$term
			= new COntologyTerm(
					$theContainer, 
					COntologyTerm::HashIndex(
						'ITPGRFA'.kTOKEN_NAMESPACE_SEPARATOR.'ANNEX1-CROP' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $nodes[ 'ITPGRFA' ]->Term() );
			$term->Code( 'ANNEX1-CROP' );
			$term->Type( kTYPE_ENUM );
			$term->Pattern( '[0-9]{1,2}', TRUE );
			$term->Name
			( 'Crop code',
			  kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Annex 1 crop code.',
			  kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		}
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Type( kTYPE_ENUM, TRUE );
		$node->Commit( $container );
		//
		// Save node.
		//
		$nodes[ 'ANNEX1-CROP' ] = $node;
		//
		// Handle edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'ITPGRFA' ] );
		$edge->Commit( $container );
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
		
		//
		// Load crop codes.
		//
		$query = <<<EOT
SELECT
	`Code_Annex1_Crops`.`Code`,
	`Code_Annex1_Crops`.`Label`,
	`Code_Annex1_Crops`.`Group`,
	`Code_Annex1_Species`.`Genus`,
	`Code_Annex1_Species`.`Species`,
	`Code_Annex1_Species`.`ExcludedSpecies`,
	`Code_Annex1_Species`.`Observations`,
	`Code_Annex1_Species`.`Subgroup`
FROM
	`Code_Annex1_Crops`
		LEFT JOIN `Code_Annex1_Species`
			ON( `Code_Annex1_Species`.`Code` = `Code_Annex1_Crops`.`Code` )
ORDER BY
	`Code_Annex1_Crops`.`Code`
EOT;
		$rs = $mysql->Execute( $query );
		foreach( $rs as $record )
		{
			//
			// Create crop term.
			//
			$term
				= new COntologyTerm(
						$theContainer, 
						COntologyTerm::HashIndex(
							$nodes[ 'ANNEX1-CROP' ]->Term()->GID()
						   .kTOKEN_NAMESPACE_SEPARATOR
						   .$record[ 'Code' ] ) );
			if( ! $term->Persistent() )
			{
				$term->NS( $nodes[ 'ANNEX1-CROP' ]->Term() );
				$term->Code( $record[ 'Code' ] );
				$term->Kind( kTYPE_ENUMERATION, TRUE );
				$term->Type( kTYPE_ENUM );
				$term->Name( $record[ 'Label' ], kDEFAULT_LANGUAGE );
				if( $record[ 'Observations' ] !== NULL )
					$term->Description( $record[ 'Observations' ], kDEFAULT_LANGUAGE );
				if( $record[ 'Genus' ] !== NULL )
				{
					$list = Array();
					$tmp = explode( ',', $record[ 'Genus' ] );
					foreach( $tmp as $element )
					{
						if( strlen( $string = trim( $element ) ) )
							$list[] = $string;
					}
					$term[ 'ITPGRFA'
						  .kTOKEN_NAMESPACE_SEPARATOR
						  .'IncludedGenus' ] = $list;
				}
				if( $record[ 'Species' ] !== NULL )
				{
					$list = Array();
					$tmp = explode( ',', $record[ 'Species' ] );
					foreach( $tmp as $element )
					{
						if( strlen( $string = trim( $element ) ) )
							$list[] = $string;
					}
					$term[ 'ITPGRFA'
						  .kTOKEN_NAMESPACE_SEPARATOR
						  .'IncludedSpecies' ] = $list;
				}
				if( $record[ 'ExcludedSpecies' ] !== NULL )
				{
					$list = Array();
					$tmp = explode( ',', $record[ 'ExcludedSpecies' ] );
					foreach( $tmp as $element )
					{
						if( strlen( $string = trim( $element ) ) )
							$list[] = $string;
					}
					$term[ 'ITPGRFA'
						  .kTOKEN_NAMESPACE_SEPARATOR
						  .'ExcludedSpecies' ] = $list;
				}
				$term->Commit( $theContainer );
			}
			//
			// Handle node.
			//
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_ENUMERATION, TRUE );
			$node->Type( kTYPE_ENUM );
			$node->Commit( $container );
			//
			// Handle edge.
			//
			$edge = $node->RelateTo( $container, $enum_of, $nodes[ 'ANNEX1-CROP' ] );
			$edge->Commit( $container );
			
			//
			// Relate group.
			//
			if( $record[ 'Group' ] !== NULL )
			{
				$edge
					= $node->RelateTo(
						$container, $part_of, $nodes[ $record[ 'Group' ] ] );
				$edge->Commit( $container );
			}
			
			//
			// Display.
			//
			if( $doDisplay )
				echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
					 ." [$term] [".$node->Node()->getId()."]\n" );
		
		} $rs->Close();
		
	} // LoadCropGroupDescriptors.

	 
	/*===================================================================================
	 *	LoadUnStatsRegions																*
	 *==================================================================================*/

	/**
	 * Load United Nations Statistics Division regions.
	 *
	 * This function will load the United Nations Statistics Division regions enumeration
	 * terms.
	 *
	 * If the last parameter is <i>TRUE</i>, the function will display the name of the
	 * created terms.
	 *
	 * @param CContainer			$theContainer		Collection.
	 * @param boolean				$doDisplay			Display created terms.
	 *
	 * @access protected
	 */
	function LoadUnStatsRegions( CContainer $theContainer, $doDisplay = TRUE )
	{
		//
		// Init local storage.
		//
		$nodes = $_SESSION[ 'REGIONS' ] = Array();
		$container = array( kTAG_TERM => $theContainer,
							kTAG_NODE => $_SESSION[ kSESSION_NEO4J ] );
		
		//
		// Open MySQL connection.
		//
		$mysql = NewADOConnection( DEFAULT_ANCILLARY_HOST );
		if( ! $mysql )
			throw new Exception( 'Unable to connect to MySQL.' );				// !@! ==>
		$mysql->Execute( "SET CHARACTER SET 'utf8'" );
		$mysql->setFetchMode( ADODB_FETCH_ASSOC );
		
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
		// United Nations Statistics Division.
		//
		$term = new COntologyTerm( $theContainer, 
								   COntologyTerm::HashIndex( 'UNSTATS' ) );
		if( ! $term->Persistent() )
		{
			$term->Code( 'UNSTATS' );
			$term->Kind( kTYPE_NAMESPACE, TRUE );
			$term->Name
			( 'UN Statistics Division',
			  kDEFAULT_LANGUAGE );
			$term->Definition
			( 'United Nations Statistics Division.',
			  kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		}
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_ROOT, TRUE );
		$node->Commit( $container );
		//
		// Save node.
		//
		$nodes[ 'UNSTATS' ] = $node;
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
		
		//
		// UNSTATS - Region codes.
		//
		$term
			= new COntologyTerm(
					$theContainer, 
					COntologyTerm::HashIndex(
						'UNSTATS'.kTOKEN_NAMESPACE_SEPARATOR.'REGIONS' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $nodes[ 'UNSTATS' ]->Term() );
			$term->Code( 'REGIONS' );
			$term->Type( kTYPE_ENUM );
			$term->Pattern( '[0-9]{3}', TRUE );
			$term->Domain( kDOMAIN_GEOGRAPHY, TRUE );
			$term->Category( kCATEGORY_ADMIN, TRUE );
			$term->Name
			( 'Region codes',
			  kDEFAULT_LANGUAGE );
			$term->Definition
			( 'United Nations Statistics Division composition of macro geographical '
			 .'(continental) regions, geographical sub-regions, and selected economic '
			 .'and other groupings.',
			  kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		}
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Type( kTYPE_ENUM, TRUE );
		$node->Domain( kDOMAIN_GEOGRAPHY, TRUE );
		$node->Category( kCATEGORY_ADMIN, TRUE );
		$node->Commit( $container );
		//
		// Save node.
		//
		$nodes[ 'REGIONS' ] = $node;
		//
		// Handle edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'UNSTATS' ] );
		$edge->Commit( $container );
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
		
		//
		// Load region terms.
		//
		$query = <<<EOT
SELECT
	`Code_ISO_3166_Regions`.`Code`,
	`Code_ISO_3166_Regions`.`Name`
FROM
	`Code_ISO_3166_Regions`
ORDER BY
	`Code_ISO_3166_Regions`.`Code`
EOT;
		$rs = $mysql->Execute( $query );
		foreach( $rs as $record )
		{
			//
			// Create region term.
			//
			$term
				= new COntologyTerm(
						$theContainer, 
						COntologyTerm::HashIndex(
							$nodes[ 'REGIONS' ]->Term()->GID()
						   .kTOKEN_NAMESPACE_SEPARATOR
						   .$record[ 'Code' ] ) );
			if( ! $term->Persistent() )
			{
				$term->NS( $nodes[ 'REGIONS' ]->Term() );
				$term->Code( $record[ 'Code' ] );
				$term->Kind( kTYPE_ENUMERATION, TRUE );
				$term->Type( kTYPE_ENUM );
				$term->Name( $record[ 'Name' ], kDEFAULT_LANGUAGE );
				$term->Commit( $theContainer );
			}
			//
			// Handle node.
			//
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_ENUMERATION, TRUE );
			$node->Type( kTYPE_ENUM );
			$node->Commit( $container );
			//
			// Save node.
			//
			$nodes[ $record[ 'Code' ] ] = $node;
			$_SESSION[ 'REGIONS' ][ $term->Code() ] = $node;
			//
			// Display.
			//
			if( $doDisplay )
				echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
					 ." [$term] [".$node->Node()->getId()."]\n" );
		
		} $rs->Close();
		
		//
		// Handle hierarchy.
		//
		$query = <<<EOT
SELECT
	`Code_ISO_3166_Regions`.`Code`,
	`Code_ISO_3166_Regions`.`Parent`,
	`Code_ISO_3166_Regions`.`Name`
FROM
	`Code_ISO_3166_Regions`
ORDER BY
	`Code_ISO_3166_Regions`.`Parent`
EOT;
		$rs = $mysql->Execute( $query );
		foreach( $rs as $record )
		{
			//
			// Get parent and child terms.
			//
			$child_term
				= new COntologyTerm(
						$theContainer, 
						COntologyTerm::HashIndex(
							$nodes[ 'REGIONS' ]->Term()->GID()
						   .kTOKEN_NAMESPACE_SEPARATOR
						   .$record[ 'Code' ] ) );
			if( $record[ 'Parent' ] !== NULL )
				$parent_term
					= new COntologyTerm(
							$theContainer, 
							COntologyTerm::HashIndex(
								$nodes[ 'REGIONS' ]->Term()->GID()
							   .kTOKEN_NAMESPACE_SEPARATOR
							   .$record[ 'Parent' ] ) );
			else
				$parent_term
					= $nodes[ 'REGIONS' ]->Term();
			
			//
			// Get parent node.
			//
			if( $record[ 'Parent' ] !== NULL )
				$parent_node = $nodes[ $record[ 'Parent' ] ];
			else
				$parent_node = $nodes[ 'REGIONS' ];
			
			//
			// Get child node.
			//
			$child_node = $nodes[ $record[ 'Code' ] ];
			
			//
			// Handle edge.
			//
			$edge = $child_node->RelateTo( $container, $enum_of, $parent_node );
			$edge->Commit( $container );
			
			//
			// Display.
			//
			if( $doDisplay )
				echo( "[$child_term] ==> [$parent_term]\n" );
		
		} $rs->Close();
		
	} // LoadUnStatsRegions.

	 
	/*===================================================================================
	 *	LoadISO																			*
	 *==================================================================================*/

	/**
	 * Load ISO.
	 *
	 * This function will load the ISO enumeration terms.
	 *
	 * If the last parameter is <i>TRUE</i>, the function will display the name of the
	 * created terms.
	 *
	 * @param CContainer			$theContainer		Collection.
	 * @param boolean				$doDisplay			Display created terms.
	 *
	 * @access protected
	 */
	function LoadISO( CContainer $theContainer, $doDisplay = TRUE )
	{
		//
		// Init local storage.
		//
		$nodes = Array();
		$container = array( kTAG_TERM => $theContainer,
							kTAG_NODE => $_SESSION[ kSESSION_NEO4J ] );
		
		//
		// Open MySQL connection.
		//
		$mysql = NewADOConnection( DEFAULT_ANCILLARY_HOST );
		if( ! $mysql )
			throw new Exception( 'Unable to connect to MySQL.' );				// !@! ==>
		$mysql->Execute( "SET CHARACTER SET 'utf8'" );
		$mysql->setFetchMode( ADODB_FETCH_ASSOC );
		
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
		// PART-OF.
		//
		$part_of
			= new COntologyTerm
						( $theContainer, COntologyTerm::HashIndex( kPRED_PART_OF ) );
		
		//
		// ISO.
		//
		$term = new COntologyTerm( $theContainer, 
								   COntologyTerm::HashIndex( 'ISO' ) );
		if( ! $term->Persistent() )
		{
			$term->Code( 'ISO' );
			$term->Kind( kTYPE_NAMESPACE, TRUE );
			$term->Domain( kDOMAIN_LANGUAGE, TRUE );
			$term->Domain( kDOMAIN_GEOGRAPHY, TRUE );
			$term->Name
			( 'International Organization for Standardization',
			  kDEFAULT_LANGUAGE );
			$term->Name
			( 'Organisation internationale de normalisation',
			  'fr' );
			$term->Name
			( 'Международная организация по стандартизации',
			  'ru' );
			$term->Definition
			( 'Collection of industrial and commercial standards and codes.',
			  kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		}
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_ROOT, TRUE );
		$node->Commit( $container );
		//
		// Save node.
		//
		$nodes[ 'ISO' ] = $node;
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
		
		//
		// ISO 639.
		//
		$term
			= new COntologyTerm(
					$theContainer, 
					COntologyTerm::HashIndex(
						'ISO'.kTOKEN_NAMESPACE_SEPARATOR.'639' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $nodes[ 'ISO' ]->Term() );
			$term->Code( '639' );
			$term->Kind( kTYPE_NAMESPACE, TRUE );
			$term->Domain( kDOMAIN_LANGUAGE, TRUE );
			$term->Name
			( 'ISO 639',
			  kDEFAULT_LANGUAGE );
			$term->Definition
			( 'ISO 639 is a set of standards by the International Organization for '
			 .'Standardization that is concerned with representation of names for '
			 .'language and language groups.',
			  kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		}
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Domain( kDOMAIN_LANGUAGE, TRUE );
		$node->Commit( $container );
		//
		// Save node.
		//
		$nodes[ $nodes[ 'ISO' ]->Term()->GID()
			   .kTOKEN_NAMESPACE_SEPARATOR
			   .'639' ] = $node;
		//
		// Handle edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'ISO' ] );
		$edge->Commit( $container );
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
		
		//
		// Init local storage.
		//
		$components = array
		(
			array( 'id'	=> 'PrintName',
				   'nam' => 'Name associated with the language',
				   'def' => 'One of the names associated with the language.' ),
			array( 'id'	=> 'InvertedName',
				   'nam' => 'Name associated with the language',
				   'def' => 'One of the names associated with the language.' )
		);
		
		//
		// Load data.
		//
		foreach( $components as $component )
		{
			//
			// Handle term.
			//
			$term
				= new COntologyTerm(
						$theContainer, 
						COntologyTerm::HashIndex(
							$nodes[ 'ISO:639' ]->Term()->GID()
						   .kTOKEN_NAMESPACE_SEPARATOR
						   .$component[ 'id' ] ) );
			if( ! $term->Persistent() )
			{
				$term->NS( $nodes[ 'ISO:639' ]->Term() );
				$term->Code( $component[ 'id' ] );
				$term->Kind( kTYPE_ATTRIBUTE, TRUE );
				$term->Type( kTYPE_STRING );
				$term->Cardinality( kCARD_0_1 );
				$term->Name( $component[ 'nam' ], kDEFAULT_LANGUAGE );
				$term->Definition( $component[ 'def' ], kDEFAULT_LANGUAGE );
				$term->Commit( $theContainer );
			}
			
			//
			// Display.
			//
			if( $doDisplay )
				echo( "[$term] ".$term->Name( NULL, kDEFAULT_LANGUAGE )."\n" );
		}
		
		//
		// ISO 639-3.
		//
		$term
			= new COntologyTerm(
					$theContainer, 
					COntologyTerm::HashIndex(
						$nodes[ 'ISO:639' ]->Term()->GID()
					   .kTOKEN_NAMESPACE_SEPARATOR
					   .'3' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $nodes[ 'ISO:639' ]->Term() );
			$term->Code( '3' );
			$term->Kind( kTYPE_NAMESPACE, TRUE );
			$term->Domain( kDOMAIN_LANGUAGE, TRUE );
			$term->Name
			( 'Part 3 alpha-3 codes',
			  kDEFAULT_LANGUAGE );
			$term->Definition
			( 'The standard describes three‐letter codes for identifying languages. '
			 .'It extends the ISO 639-2 alpha-3 codes with an aim to cover all known '
			 .'natural languages. The standard was published by ISO on 2007-02-05.',
			  kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		}
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Domain( kDOMAIN_LANGUAGE, TRUE );
		$node->Commit( $container );
		//
		// Save node.
		//
		$nodes[ $nodes[ 'ISO:639' ]->Term()->GID()
			   .kTOKEN_NAMESPACE_SEPARATOR
			   .'3' ] = $node;
		//
		// Handle edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'ISO:639' ] );
		$edge->Commit( $container );
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
		
		//
		// Init local storage.
		//
		$components = array
		(
			array( 'id'	=> 'Part3',
				   'pat' => '[A-Z]{3}',
				   'nam' => '3-letter ISO 639-3 identifier',
				   'def' => 'ISO 639-3 3 character code identifying a language.' ),
			array( 'id'	=> 'Part2B',
				   'pat' => '[0-9]{3}',
				   'nam' => 'Bibliographic applications 639-2 identifier',
				   'def' => 'ISO 639-2 identifier of the bibliographic applications '
				   		   .'code set.' ),
			array( 'id'	=> 'Part2T',
				   'pat' => '[0-9]{3}',
				   'nam' => 'Terminology applications 639-2 identifier',
				   'def' => 'ISO 639-2 identifier of the terminology applications '
				   		   .'code set.' ),
			array( 'id'	=> 'Part1',
				   'pat' => '[0-9]{2}',
				   'nam' => '639-1 identifier',
				   'def' => 'ISO 639-1 identifier.' )
		);
		
		//
		// Load data.
		//
		foreach( $components as $component )
		{
			//
			// Handle term.
			//
			$term
				= new COntologyTerm(
						$theContainer, 
						COntologyTerm::HashIndex(
							$nodes[ 'ISO:639:3' ]->Term()->GID()
						   .kTOKEN_NAMESPACE_SEPARATOR
						   .$component[ 'id' ] ) );
			if( ! $term->Persistent() )
			{
				$term->NS( $nodes[ 'ISO:639:3' ]->Term() );
				$term->Code( $component[ 'id' ] );
				$term->Type( kTYPE_ENUM );
				$term->Pattern( $component[ 'pat' ], TRUE );
				$term->Domain( kDOMAIN_LANGUAGE, TRUE );
				$term->Name( $component[ 'nam' ], kDEFAULT_LANGUAGE );
				$term->Definition( $component[ 'def' ], kDEFAULT_LANGUAGE );
				$term->Commit( $theContainer );
			}
			
			//
			// Handle node.
			//
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_MEASURE, TRUE );
			$node->Type( kTYPE_ENUM, TRUE );
			$node->Domain( kDOMAIN_LANGUAGE, TRUE );
			$node->Commit( $container );
			
			//
			// Save node.
			//
			$nodes[ $nodes[ 'ISO:639:3' ]->Term()->GID()
				   .kTOKEN_NAMESPACE_SEPARATOR
				   .$component[ 'id' ] ] = $node;
			
			//
			// Handle edge.
			//
			$edge = $node->RelateTo( $container, $is_a, $nodes[ 'ISO:639:3' ] );
			$edge->Commit( $container );
			
			//
			// Display.
			//
			if( $doDisplay )
				echo( "[$term] "
					 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
					 .$node->Node()->getId()."}"
					 ."\n" );
		}

		//
		// ISO 639-3 - Scope.
		//
		$term
			= new COntologyTerm(
					$theContainer, 
					COntologyTerm::HashIndex(
						$nodes[ 'ISO:639:3' ]->Term()->GID()
					   .kTOKEN_NAMESPACE_SEPARATOR
					   .'Scope' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $nodes[ 'ISO:639:3' ]->Term() );
			$term->Code( 'Scope' );
			$term->Kind( kTYPE_NAMESPACE, TRUE );
			$term->Domain( kDOMAIN_LANGUAGE, TRUE );
			$term->Name
			( 'Language scope',
			  kDEFAULT_LANGUAGE );
			$term->Definition
			( 'ISO 639-3 language scope.',
			  kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		}
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Type( kTYPE_ENUM, TRUE );
		$node->Domain( kDOMAIN_LANGUAGE, TRUE );
		$node->Commit( $container );
		//
		// Save node.
		//
		$nodes[ $nodes[ 'ISO:639:3' ]->Term()->GID()
			   .kTOKEN_NAMESPACE_SEPARATOR
			   .'Scope' ] = $node;
		//
		// Handle edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'ISO:639:3' ] );
		$edge->Commit( $container );
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
		
		//
		// Init local storage.
		//
		$components = array
		(
			array( 'id'	=> 'I',
				   'nam' => 'Individual',
				   'def' => 'Individual language scope.' ),
			array( 'id'	=> 'M',
				   'nam' => 'Macrolanguage',
				   'def' => 'Macro-language scope.' ),
			array( 'id'	=> 'S',
				   'nam' => 'Special',
				   'def' => 'Special language scope.' )
		);
		
		//
		// Load data.
		//
		foreach( $components as $component )
		{
			//
			// Handle term.
			//
			$term
				= new COntologyTerm(
						$theContainer, 
						COntologyTerm::HashIndex(
							$nodes[ 'ISO:639:3:Scope' ]->Term()->GID()
						   .kTOKEN_NAMESPACE_SEPARATOR
						   .$component[ 'id' ] ) );
			if( ! $term->Persistent() )
			{
				$term->NS( $nodes[ 'ISO:639:3:Scope' ]->Term() );
				$term->Code( $component[ 'id' ] );
				$term->Kind( kTYPE_ENUMERATION, TRUE );
				$term->Type( kTYPE_ENUM );
				$term->Enumeration( $term->Code(), TRUE );
				$term->Domain( kDOMAIN_LANGUAGE, TRUE );
				$term->Name( $component[ 'nam' ], kDEFAULT_LANGUAGE );
				$term->Definition( $component[ 'def' ], kDEFAULT_LANGUAGE );
				$term->Commit( $theContainer );
			}
			
			//
			// Handle node.
			//
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_ENUMERATION, TRUE );
			$node->Type( kTYPE_ENUM, TRUE );
			$node->Domain( kDOMAIN_LANGUAGE, TRUE );
			$node->Commit( $container );
			
			//
			// Save node.
			//
			$nodes[ 'ISO:639:3:Scope'
				   .kTOKEN_NAMESPACE_SEPARATOR
				   .$component[ 'id' ] ] = $node;
			
			//
			// Handle edge.
			//
			$edge = $node->RelateTo( $container, $enum_of, $nodes[ 'ISO:639:3:Scope' ] );
			$edge->Commit( $container );
			
			//
			// Display.
			//
			if( $doDisplay )
				echo( "[$term] "
					 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
					 .$node->Node()->getId()."}"
					 ."\n" );
		}

		//
		// ISO 639-3 - Type.
		//
		$term
			= new COntologyTerm(
					$theContainer, 
					COntologyTerm::HashIndex(
						$nodes[ 'ISO:639:3' ]->Term()->GID()
					   .kTOKEN_NAMESPACE_SEPARATOR
					   .'Type' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $nodes[ 'ISO:639:3' ]->Term() );
			$term->Code( 'Type' );
			$term->Kind( kTYPE_NAMESPACE, TRUE );
			$term->Domain( kDOMAIN_LANGUAGE, TRUE );
			$term->Name
			( 'Language type',
			  kDEFAULT_LANGUAGE );
			$term->Definition
			( 'ISO 639-3 language type.',
			  kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		}
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Type( kTYPE_ENUM, TRUE );
		$node->Domain( kDOMAIN_LANGUAGE, TRUE );
		$node->Commit( $container );
		//
		// Save node.
		//
		$nodes[ $nodes[ 'ISO:639:3' ]->Term()->GID()
			   .kTOKEN_NAMESPACE_SEPARATOR
			   .'Type' ] = $node;
		//
		// Handle edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'ISO:639:3' ] );
		$edge->Commit( $container );
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );

		//
		// Init local storage.
		//
		$components = array
		(
			array( 'id'	=> 'A',
				   'nam' => 'Ancient',
				   'def' => 'Ancient language type.' ),
			array( 'id'	=> 'C',
				   'nam' => 'Constructed',
				   'def' => 'Constructed language type.' ),
			array( 'id'	=> 'E',
				   'nam' => 'Extinct',
				   'def' => 'Extinct language type.' ),
			array( 'id'	=> 'H',
				   'nam' => 'Historical',
				   'def' => 'Historical language type.' ),
			array( 'id'	=> 'L',
				   'nam' => 'Living',
				   'def' => 'Living language type.' ),
			array( 'id'	=> 'S',
				   'nam' => 'Special',
				   'def' => 'Special language type.' )
		);
		
		//
		// Load data.
		//
		foreach( $components as $component )
		{
			//
			// Handle term.
			//
			$term
				= new COntologyTerm(
						$theContainer, 
						COntologyTerm::HashIndex(
							$nodes[ 'ISO:639:3:Type' ]->Term()->GID()
						   .kTOKEN_NAMESPACE_SEPARATOR
						   .$component[ 'id' ] ) );
			if( ! $term->Persistent() )
			{
				$term->NS( $nodes[ 'ISO:639:3:Type' ]->Term() );
				$term->Code( $component[ 'id' ] );
				$term->Kind( kTYPE_ENUMERATION, TRUE );
				$term->Type( kTYPE_ENUM );
				$term->Enumeration( $term->Code(), TRUE );
				$term->Domain( kDOMAIN_LANGUAGE, TRUE );
				$term->Name( $component[ 'nam' ], kDEFAULT_LANGUAGE );
				$term->Definition( $component[ 'def' ], kDEFAULT_LANGUAGE );
				$term->Commit( $theContainer );
			}
			
			//
			// Handle node.
			//
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_ENUMERATION, TRUE );
			$node->Type( kTYPE_ENUM, TRUE );
			$node->Domain( kDOMAIN_LANGUAGE, TRUE );
			$node->Commit( $container );
			
			//
			// Save node.
			//
			$nodes[ 'ISO:639:3:Type'
				   .kTOKEN_NAMESPACE_SEPARATOR
				   .$component[ 'id' ] ] = $node;
			
			//
			// Handle edge.
			//
			$edge = $node->RelateTo( $container, $enum_of, $nodes[ 'ISO:639:3:Type' ] );
			$edge->Commit( $container );
			
			//
			// Display.
			//
			if( $doDisplay )
				echo( "[$term] "
					 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
					 .$node->Node()->getId()."}"
					 ."\n" );
		}

		//
		// Load language codes.
		//
		$language_nodes = Array();
		$query = <<<EOT
SELECT
	`Code_ISO_639_3`.`Code3`,
	`Code_ISO_639_3`.`Part2B`,
	`Code_ISO_639_3`.`Part2T`,
	`Code_ISO_639_3`.`Part1`,
	`Code_ISO_639_3`.`Scope`,
	`Code_ISO_639_3`.`Type`,
	`Code_ISO_639_3`.`ReferenceName`,
	`Code_ISO_639_3_Names`.`PrintName`,
	`Code_ISO_639_3_Names`.`InvertedName`,
	`Code_ISO_639_3`.`Comment`
FROM
	`Code_ISO_639_3`
		LEFT JOIN `Code_ISO_639_3_Names`
			ON( `Code_ISO_639_3_Names`.`Language` = `Code_ISO_639_3`.`Code3` )
ORDER BY
	`Code_ISO_639_3`.`Code3`
EOT;
		$rs = $mysql->Execute( $query );
		foreach( $rs as $record )
		{
			//
			// Create Code3 term.
			//
			$term
				= new COntologyTerm(
						$theContainer, 
						COntologyTerm::HashIndex(
							$nodes[ 'ISO:639:3:Part3' ]->Term()->GID()
						   .kTOKEN_NAMESPACE_SEPARATOR
						   .$record[ 'Code3' ] ) );
			if( ! $term->Persistent() )
			{
				$term->NS( $nodes[ 'ISO:639:3:Part3' ]->Term() );
				$term->Code( $record[ 'Code3' ] );
				$term->Kind( kTYPE_ENUMERATION, TRUE );
				$term->Type( kTYPE_ENUM );
				$term->Enumeration( $term->Code(), TRUE );
				if( $record[ 'Part2B' ] !== NULL )
					$term->Synonym( $record[ 'Part2B' ], kTYPE_EXACT, TRUE );
				if( $record[ 'Part2T' ] !== NULL )
					$term->Synonym( $record[ 'Part2T' ], kTYPE_EXACT, TRUE );
				if( $record[ 'Part1' ] !== NULL )
					$term->Synonym( $record[ 'Part1' ], kTYPE_EXACT, TRUE );
				if( $record[ 'Scope' ] !== NULL )
					$term->Domain( 'ISO:639:3:Scope:'.$record[ 'Scope' ], TRUE );
				if( $record[ 'Type' ] !== NULL )
					$term->Domain( 'ISO:639:3:Type:'.$record[ 'Type' ], TRUE );
				$term->Name( $record[ 'ReferenceName' ], kDEFAULT_LANGUAGE );
				if( $record[ 'PrintName' ] !== NULL )
					$term[ 'ISO:639:PrintName' ] = $record[ 'PrintName' ];
				if( $record[ 'InvertedName' ] !== NULL )
					$term[ 'ISO:639:InvertedName' ] = $record[ 'InvertedName' ];
				if( $record[ 'Comment' ] !== NULL )
					$term->Description( $record[ 'Comment' ], kDEFAULT_LANGUAGE );
				$term->Commit( $theContainer );
			}

			//
			// Save main term.
			//
			$term_main = $term;
			$idx_main = $term->Code();

			//
			// Handle node.
			//
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_ENUMERATION, TRUE );
			$node->Type( kTYPE_ENUM, TRUE );
			$node->Domain( kDOMAIN_LANGUAGE, TRUE );
			$node->Commit( $container );
			
			//
			// Save main node.
			//
			$node_main = $node;
			$language_nodes[ $idx_main ] = array( 'Part3' => $node );
			
			//
			// Handle edge.
			//
			$edge = $node->RelateTo( $container, $enum_of, $nodes[ 'ISO:639:3:Part3' ] );
			$edge->Commit( $container );
			
			//
			// Display.
			//
			if( $doDisplay )
				echo( "[$term] "
					 .$term->Name( NULL, kDEFAULT_LANGUAGE )
					 ." ["
					 .$node->Node()->getId()
					 ."]\n" );
		
			//
			// Handle Part2B code.
			//
			if( $record[ 'Part2B' ] !== NULL )
			{
				//
				// Create Part2B term.
				//
				$term
					= new COntologyTerm(
							$theContainer, 
							COntologyTerm::HashIndex(
								$nodes[ 'ISO:639:3:Part2B' ]->Term()->GID()
							   .kTOKEN_NAMESPACE_SEPARATOR
							   .$record[ 'Code3' ] ) );
				if( ! $term->Persistent() )
				{
					$term->NS( $nodes[ 'ISO:639:3:Part2B' ]->Term() );
					$term->Code( $record[ 'Part2B' ] );
					$term->Kind( kTYPE_ENUMERATION, TRUE );
					$term->Type( kTYPE_ENUM );
					$term->Enumeration( $term->Code(), TRUE );
					$term->Enumeration( $record[ 'Code3' ], TRUE );
					if( $record[ 'Scope' ] !== NULL )
						$term->Domain( 'ISO:639:3:Scope:'.$record[ 'Scope' ], TRUE );
					if( $record[ 'Type' ] !== NULL )
						$term->Domain( 'ISO:639:3:Type:'.$record[ 'Type' ], TRUE );
					$term->Name( $record[ 'ReferenceName' ], kDEFAULT_LANGUAGE );
					if( $record[ 'Part2T' ] !== NULL )
						$term->Synonym( $record[ 'Part2T' ], kTYPE_EXACT, TRUE );
					if( $record[ 'Part1' ] !== NULL )
						$term->Synonym( $record[ 'Part1' ], kTYPE_EXACT, TRUE );
					if( $record[ 'PrintName' ] !== NULL )
						$term[ 'ISO:639:PrintName' ] = $record[ 'PrintName' ];
					if( $record[ 'InvertedName' ] !== NULL )
						$term[ 'ISO:639:InvertedName' ] = $record[ 'InvertedName' ];
					if( $record[ 'Comment' ] !== NULL )
						$term->Description( $record[ 'Comment' ], kDEFAULT_LANGUAGE );
					$term->Commit( $theContainer );
				}
	
				//
				// Save term.
				//
				$term_alt1 = $term;
	
				//
				// Handle node.
				//
				$node = new COntologyNode( $container );
				$node->Term( $term );
				$node->Kind( kTYPE_ENUMERATION, TRUE );
				$node->Type( kTYPE_ENUM, TRUE );
				$node->Domain( kDOMAIN_LANGUAGE, TRUE );
				$node->Commit( $container );
				
				//
				// Save node.
				//
				$node_alt1 = $node;
				$language_nodes[ $idx_main ][ 'Part2B' ] = $node;
				
				//
				// Handle edge.
				//
				$edge = $node->RelateTo( $container, $enum_of, $nodes[ 'ISO:639:3:Part2B' ] );
				$edge->Commit( $container );
				
				//
				// Display.
				//
				if( $doDisplay )
					echo( "[$term] "
						 .$term->Name( NULL, kDEFAULT_LANGUAGE )
						 ." ["
						 .$node->Node()->getId()
						 ."]\n" );
			}
			else
				$term_alt1 = NULL;
		
			//
			// Handle Part2T code.
			//
			if( $record[ 'Part2T' ] !== NULL )
			{
				//
				// Create Code3 term.
				//
				$term
					= new COntologyTerm(
							$theContainer, 
							COntologyTerm::HashIndex(
								$nodes[ 'ISO:639:3:Part2T' ]->Term()->GID()
							   .kTOKEN_NAMESPACE_SEPARATOR
							   .$record[ 'Part2T' ] ) );
				if( ! $term->Persistent() )
				{
					$term->NS( $nodes[ 'ISO:639:3:Part2T' ]->Term() );
					$term->Code( $record[ 'Part2T' ] );
					$term->Kind( kTYPE_ENUMERATION, TRUE );
					$term->Type( kTYPE_ENUM );
					$term->Enumeration( $term->Code(), TRUE );
					$term->Enumeration( $record[ 'Code3' ], TRUE );
					if( $record[ 'Scope' ] !== NULL )
						$term->Domain( 'ISO:639:3:Scope:'.$record[ 'Scope' ], TRUE );
					if( $record[ 'Type' ] !== NULL )
						$term->Domain( 'ISO:639:3:Type:'.$record[ 'Type' ], TRUE );
					$term->Name( $record[ 'ReferenceName' ], kDEFAULT_LANGUAGE );
					if( $record[ 'Part2B' ] !== NULL )
						$term->Synonym( $record[ 'Part2B' ], kTYPE_EXACT, TRUE );
					if( $record[ 'Part1' ] !== NULL )
						$term->Synonym( $record[ 'Part1' ], kTYPE_EXACT, TRUE );
					if( $record[ 'PrintName' ] !== NULL )
						$term[ 'ISO:639:PrintName' ] = $record[ 'PrintName' ];
					if( $record[ 'InvertedName' ] !== NULL )
						$term[ 'ISO:639:InvertedName' ] = $record[ 'InvertedName' ];
					if( $record[ 'Comment' ] !== NULL )
						$term->Description( $record[ 'Comment' ], kDEFAULT_LANGUAGE );
					$term->Commit( $theContainer );
				}
	
				//
				// Save term.
				//
				$term_alt2 = $term;
	
				//
				// Handle node.
				//
				$node = new COntologyNode( $container );
				$node->Term( $term );
				$node->Kind( kTYPE_ENUMERATION, TRUE );
				$node->Type( kTYPE_ENUM, TRUE );
				$node->Domain( kDOMAIN_LANGUAGE, TRUE );
				$node->Commit( $container );
				
				//
				// Save main node.
				//
				$node_alt2 = $node;
				$language_nodes[ $idx_main ][ 'Part2T' ] = $node;
				
				//
				// Handle edge.
				//
				$edge = $node->RelateTo( $container, $enum_of, $nodes[ 'ISO:639:3:Part2T' ] );
				$edge->Commit( $container );
				
				//
				// Display.
				//
				if( $doDisplay )
					echo( "[$term] "
						 .$term->Name( NULL, kDEFAULT_LANGUAGE )
						 ." ["
						 .$node->Node()->getId()
						 ."]\n" );
			}
			else
				$term_alt2 = NULL;
		
			//
			// Handle Part1 code.
			//
			if( $record[ 'Part1' ] !== NULL )
			{
				//
				// Create Part2B term.
				//
				$term
					= new COntologyTerm(
							$theContainer, 
							COntologyTerm::HashIndex(
								$nodes[ 'ISO:639:3:Part1' ]->Term()->GID()
							   .kTOKEN_NAMESPACE_SEPARATOR
							   .$record[ 'Part1' ] ) );
				if( ! $term->Persistent() )
				{
					$term->NS( $nodes[ 'ISO:639:3:Part1' ]->Term() );
					$term->Code( $record[ 'Part1' ] );
					$term->Kind( kTYPE_ENUMERATION, TRUE );
					$term->Type( kTYPE_ENUM );
					$term->Enumeration( $term->Code(), TRUE );
					$term->Enumeration( $record[ 'Code3' ], TRUE );
					if( $record[ 'Scope' ] !== NULL )
						$term->Domain( 'ISO:639:3:Scope:'.$record[ 'Scope' ], TRUE );
					if( $record[ 'Type' ] !== NULL )
						$term->Domain( 'ISO:639:3:Type:'.$record[ 'Type' ], TRUE );
					$term->Name( $record[ 'ReferenceName' ], kDEFAULT_LANGUAGE );
					if( $record[ 'Part2T' ] !== NULL )
						$term->Synonym( $record[ 'Part2T' ], kTYPE_EXACT, TRUE );
					if( $record[ 'Part2B' ] !== NULL )
						$term->Synonym( $record[ 'Part2B' ], kTYPE_EXACT, TRUE );
					if( $record[ 'PrintName' ] !== NULL )
						$term[ 'ISO:639:PrintName' ] = $record[ 'PrintName' ];
					if( $record[ 'InvertedName' ] !== NULL )
						$term[ 'ISO:639:InvertedName' ] = $record[ 'InvertedName' ];
					if( $record[ 'Comment' ] !== NULL )
						$term->Description( $record[ 'Comment' ], kDEFAULT_LANGUAGE );
					$term->Commit( $theContainer );
				}
	
				//
				// Save main term.
				//
				$term_alt3 = $term;
	
				//
				// Handle node.
				//
				$node = new COntologyNode( $container );
				$node->Term( $term );
				$node->Kind( kTYPE_ENUMERATION, TRUE );
				$node->Type( kTYPE_ENUM, TRUE );
				$node->Domain( kDOMAIN_LANGUAGE, TRUE );
				$node->Commit( $container );
				
				//
				// Save node.
				//
				$node_alt3 = $node;
				$language_nodes[ $idx_main ][ 'Part1' ] = $node;
				
				//
				// Handle edge.
				//
				$edge = $node->RelateTo( $container, $enum_of, $nodes[ 'ISO:639:3:Part1' ] );
				$edge->Commit( $container );
				
				//
				// Display.
				//
				if( $doDisplay )
					echo( "[$term] "
						 .$term->Name( NULL, kDEFAULT_LANGUAGE )
						 ." ["
						 .$node->Node()->getId()
						 ."]\n" );
			}
			else
				$term_alt3 = NULL;
			
			//
			// Handle exact cross-references.
			//
			$trms = array( $term_main, ( ( $term_alt1 !== NULL ) ? $term_alt1 : NULL ),
									   ( ( $term_alt2 !== NULL ) ? $term_alt2 : NULL ),
									   ( ( $term_alt3 !== NULL ) ? $term_alt3 : NULL ) );
			$nods = array( $node_main, ( ( $term_alt1 !== NULL ) ? $node_alt1 : NULL ),
									   ( ( $term_alt2 !== NULL ) ? $node_alt2 : NULL ),
									   ( ( $term_alt3 !== NULL ) ? $node_alt3 : NULL ) );
			for( $i = 0; $i < count( $trms ); $i++ )
			{
				for( $j = 0; $j < count( $trms ); $j++ )
				{
					//
					// Skip same.
					//
					if( $i != $j )
					{
						//
						// Skip missing.
						//
						if( ($trms[ $i ] !== NULL)
						 && ($trms[ $j ] !== NULL) )
						{
							//
							// Relate terms.
							//
							$trms[ $i ]->Xref( $trms[ $j ], kTYPE_EXACT, TRUE );
							//
							// Set used.
							//
							if( $i == 0 )
							{
								//
								// Set in term.
								//
								$trms[ $j ]->Used( $trms[ $i ] );
								//
								// Set in node.
								//
								$edge
									= $nods[ $j ]->RelateTo(
										$container, kTAG_DEFAULT, $nods[ $i ] );
								$edge->Commit( $container );
							}
							//
							// Relate nodes.
							//
							$edge
								= $nods[ $i ]->RelateTo(
									$container, kTAG_REFERENCE_XREF, $nods[ $j ] );
							$edge->Node()->setProperty( kTAG_KIND, kTYPE_EXACT );
							$edge->Commit( $container );
							//
							// Commit term.
							//
							$trms[ $i ]->Commit( $theContainer );
						}
					}
				}
			}
		
		} $rs->Close();

		//
		// Load macrolanguage codes.
		//
		$query = <<<EOT
SELECT
	`Code_ISO_639_3_Macrolanguages`.`Language`,
	`Code_ISO_639_3_Macrolanguages`.`MacroLanguage`
FROM
	`Code_ISO_639_3_Macrolanguages`
ORDER BY
	`Code_ISO_639_3_Macrolanguages`.`MacroLanguage`
EOT;
		$macro_predicate
			= $nodes[ 'ISO:639:3:Scope'
					 .kTOKEN_NAMESPACE_SEPARATOR
					 .'M' ]->Term();
		$rs = $mysql->Execute( $query );
		foreach( $rs as $record )
		{
			//
			// Locate macrolanguage node.
			//
			$id = $record[ 'MacroLanguage' ];
			if( ! array_key_exists( $id, $language_nodes ) )
				throw new Exception( "Unable to find [$id] node." );			// !@! ==>
			$macro = $language_nodes[ $id ];
			
			//
			// Locate language node.
			//
			$id = $record[ 'Language' ];
			if( ! array_key_exists( $id, $language_nodes ) )
				throw new Exception( "Unable to find [$id] node." );			// !@! ==>
			$language = $language_nodes[ $id ];
			
			//
			// Iterate macro-language code groups.
			//
			foreach( $macro as $group => $macro_node )
			{
				if( array_key_exists( $group, $language ) )
				{
					$edge
						= $macro_node->RelateTo(
							$container, $macro_predicate, $language[ $group ] );
					$edge->Commit( $container );
					
					//
					// Display.
					//
					if( $doDisplay )
						echo( '['.$macro_node->Term().'] => ['
							 .$language[ $group ]->Term().'] ['
							 .$edge->Node()->getId()
							 ."]\n" );
				}
			}
		
		} $rs->Close();
		unset( $language_nodes );
		
		//
		// ISO 3166.
		//
		$term
			= new COntologyTerm(
					$theContainer, 
					COntologyTerm::HashIndex(
						'ISO'.kTOKEN_NAMESPACE_SEPARATOR.'3166' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $nodes[ 'ISO' ]->Term() );
			$term->Code( '3166' );
			$term->Kind( kTYPE_NAMESPACE, TRUE );
			$term->Domain( kDOMAIN_GEOGRAPHY, TRUE );
			$term->Category( kCATEGORY_ADMIN, TRUE );
			$term->Name
			( 'Geographical codes and names',
			  kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Codes for the representation of names of countries and their subdivisions.',
			  kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		}
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Domain( kDOMAIN_GEOGRAPHY, TRUE );
		$node->Commit( $container );
		//
		// Save node.
		//
		$nodes[ $nodes[ 'ISO' ]->Term()->GID()
			   .kTOKEN_NAMESPACE_SEPARATOR
			   .'3166' ] = $node;
		//
		// Handle edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'ISO' ] );
		$edge->Commit( $container );
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
		
		//
		// ISO 3166-1.
		//
		$term
			= new COntologyTerm(
					$theContainer, 
					COntologyTerm::HashIndex(
						$nodes[ 'ISO:3166' ]->Term()->GID()
					   .kTOKEN_NAMESPACE_SEPARATOR
					   .'1' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $nodes[ 'ISO:3166' ]->Term() );
			$term->Code( '1' );
			$term->Kind( kTYPE_NAMESPACE, TRUE );
			$term->Domain( kDOMAIN_GEOGRAPHY, TRUE );
			$term->Category( kCATEGORY_ADMIN, TRUE );
			$term->Name
			( 'Country code',
			  kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Codes for the representation of names of countries and their '
			 .'subdivisions – Part 1: Country codes.',
			  kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		}
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Domain( kDOMAIN_GEOGRAPHY, TRUE );
		$node->Category( kCATEGORY_ADMIN, TRUE );
		$node->Commit( $container );
		//
		// Save node.
		//
		$nodes[ $nodes[ 'ISO:3166' ]->Term()->GID()
			   .kTOKEN_NAMESPACE_SEPARATOR
			   .'1' ] = $node;
		//
		// Handle edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'ISO:3166' ] );
		$edge->Commit( $container );
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
		
		//
		// Init local storage.
		//
		$components = array
		(
			array( 'id'	=> 'NUMERIC-3',
				   'pat' => '[0-9]{3}',
				   'nam' => '3-digit country code',
				   'def' => 'ISO 3166-1 numeric (or numeric-3) codes are three-digit '
				   		   .'country codes defined in ISO 3166-1, part of the ISO 3166 '
				   		   .'standard published by the International Organization for '
				   		   .'Standardization (ISO), to represent countries, dependent '
				   		   .'territories, and special areas of geographical interest.' ),
			array( 'id'	=> 'ALPHA-2',
				   'pat' => '[A-Z]{2}',
				   'nam' => '2-character country code',
				   'def' => 'ISO 3166-1 alpha-2 codes are two-letter country codes '
				   		   .'defined in ISO 3166-1, part of the ISO 3166 standard '
				   		   .'published by the International Organization for '
				   		   .'Standardization (ISO), to represent countries, dependent '
				   		   .'territories, and special areas of geographical interest.' ),
			array( 'id'	=> 'ALPHA-3',
				   'pat' => '[A-Z]{3}',
				   'nam' => '3-character country code',
				   'def' => 'ISO 3166-1 alpha-3 codes are three-letter country codes '
				   		   .'defined in ISO 3166-1, part of the ISO 3166 standard '
				   		   .'published by the International Organization for '
				   		   .'Standardization (ISO), to represent countries, dependent '
				   		   .'territories, and special areas of geographical interest.' )
		);
		
		//
		// Load data.
		//
		foreach( $components as $component )
		{
			//
			// Handle term.
			//
			$term
				= new COntologyTerm(
						$theContainer, 
						COntologyTerm::HashIndex(
							$nodes[ 'ISO:3166:1' ]->Term()->GID()
						   .kTOKEN_NAMESPACE_SEPARATOR
						   .$component[ 'id' ] ) );
			if( ! $term->Persistent() )
			{
				$term->NS( $nodes[ 'ISO:3166:1' ]->Term() );
				$term->Code( $component[ 'id' ] );
				$term->Type( kTYPE_ENUM );
				$term->Pattern( $component[ 'pat' ], TRUE );
				$term->Domain( kDOMAIN_GEOGRAPHY, TRUE );
				$term->Category( kCATEGORY_ADMIN, TRUE );
				$term->Name( $component[ 'nam' ], kDEFAULT_LANGUAGE );
				$term->Definition( $component[ 'def' ], kDEFAULT_LANGUAGE );
				$term->Commit( $theContainer );
			}
			
			//
			// Handle node.
			//
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_MEASURE, TRUE );
			$node->Type( kTYPE_ENUM, TRUE );
			$node->Domain( kDOMAIN_GEOGRAPHY, TRUE );
			$node->Category( kCATEGORY_ADMIN, TRUE );
			$node->Commit( $container );
			
			//
			// Save node.
			//
			$nodes[ $nodes[ 'ISO:3166:1' ]->Term()->GID()
				   .kTOKEN_NAMESPACE_SEPARATOR
				   .$component[ 'id' ] ] = $node;
			
			//
			// Handle edge.
			//
			$edge = $node->RelateTo( $container, $is_a, $nodes[ 'ISO:3166:1' ] );
			$edge->Commit( $container );
			
			//
			// Display.
			//
			if( $doDisplay )
				echo( "[$term] "
					 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
					 .$node->Node()->getId()."}"
					 ."\n" );
		}
				
		//
		// Iterate country codes.
		//
		$countries = Array();
		$query = <<<EOT
SELECT
	`Code_ISO_3166`.`ISO3`,
	`Code_ISO_3166`.`CodeNum`,
	`Code_ISO_3166`.`Code2`,
	`VALID_COUNTRY`.`ISO3` AS `ValidCode`,
	`Code_ISO_3166`.`Region`,
	`Code_ISO_3166`.`Name`,
	`Code_ISO_3166`.`Current`,
	`Code_ISO_3166`.`FlagThumb`,
	`Code_ISO_3166`.`FlagImage`,
	`Code_ISO_3166`.`FlagVector`
FROM
	`Code_ISO_3166`
		LEFT JOIN `Code_ISO_3166` `VALID_COUNTRY`
			ON( `VALID_COUNTRY`.`Code` = `Code_ISO_3166`.`Valid` )
WHERE
	`Code_ISO_3166`.`ISO3` IS NOT NULL
ORDER BY
	`Code_ISO_3166`.`ISO3`
EOT;
		$rs = $mysql->Execute( $query );
		foreach( $rs as $record )
		{
			//
			// Create alpha 3 term.
			// Note that the country code namespace is ISO-3166-1,
			// not its parent: this is because all codes are univoque.
			//
			$term
				= new COntologyTerm(
						$theContainer, 
						COntologyTerm::HashIndex(
							$nodes[ 'ISO:3166:1' ]->Term()->GID()
						   .kTOKEN_NAMESPACE_SEPARATOR
						   .$record[ 'ISO3' ] ) );
			if( ! $term->Persistent() )
			{
				$term->NS( $nodes[ 'ISO:3166:1' ]->Term() );
				$term->Code( $record[ 'ISO3' ] );
				$term->Kind( kTYPE_ENUMERATION, TRUE );
				$term->Type( kTYPE_ENUM );
				$term->Enumeration( $term->Code(), TRUE );
				if( $record[ 'CodeNum' ] !== NULL )
					$term->Synonym( $record[ 'CodeNum' ], kTYPE_EXACT, TRUE );
				if( $record[ 'Code2' ] !== NULL )
					$term->Synonym( $record[ 'Code2' ], kTYPE_EXACT, TRUE );
				$term->Domain( kDOMAIN_GEOGRAPHY, TRUE );
				$term->Category( kCATEGORY_ADMIN, TRUE );
				$term->Name( $record[ 'Name' ], kDEFAULT_LANGUAGE );
				if( $record[ 'FlagThumb' ] !== NULL )
					$term->Image( kIMAGE_THUMB_FLAG,
								  kTYPE_PNG,
								  bin2hex( $record[ 'FlagThumb' ] ) );
				if( $record[ 'FlagImage' ] !== NULL )
					$term->Image( kIMAGE_MED_FLAG,
								  kTYPE_PNG,
								  bin2hex( $record[ 'FlagImage' ] ) );
				if( $record[ 'FlagVector' ] !== NULL )
					$term->Image( kIMAGE_VECT_FLAG,
								  kTYPE_SVG,
								  $record[ 'FlagVector' ] );
				$term->Commit( $theContainer );
			}

			//
			// Save main term.
			//
			$term_main = $term;

			//
			// Handle node.
			//
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_ENUMERATION, TRUE );
			$node->Type( kTYPE_ENUM, TRUE );
			$node->Domain( kDOMAIN_GEOGRAPHY, TRUE );
			$node->Category( kCATEGORY_ADMIN, TRUE );
			$node->Commit( $container );
			
			//
			// Save main node.
			//
			$node_main = $node;
			$countries[ $record[ 'ISO3' ] ] = $node;
			
			//
			// Handle edge.
			//
			$parent = $nodes[ 'ISO:3166:1:ALPHA-3' ];
			$edge = $node->RelateTo( $container, $enum_of, $parent );
			$edge->Commit( $container );
			
			//
			// Create region edge.
			//
			if( $record[ 'Region' ] !== NULL )
			{
				$region = $_SESSION[ 'REGIONS' ][ $record[ 'Region' ] ];
				$edge = $node->RelateTo( $container, $part_of, $region );
				$edge->Commit( $container );
			}
			
			//
			// Display.
			//
			if( $doDisplay )
				echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
					 ." [$term] [".$node->Node()->getId()."]" );
			
			//
			// Only valid codes.
			//
			if( $record[ 'Current' ] )
			{
				//
				// Handle alpha 2 code.
				//
				if( $record[ 'Code2' ] !== NULL )
				{
					//
					// Create alpha 2 term.
					//
					$term
						= new COntologyTerm(
								$theContainer, 
								COntologyTerm::HashIndex(
									$nodes[ 'ISO:3166:1' ]->Term()->GID()
								   .kTOKEN_NAMESPACE_SEPARATOR
								   .$record[ 'Code2' ] ) );
					if( ! $term->Persistent() )
					{
						$term->NS( $nodes[ 'ISO:3166:1' ]->Term() );
						$term->Code( $record[ 'Code2' ] );
						$term->Kind( kTYPE_ENUMERATION, TRUE );
						$term->Type( kTYPE_ENUM );
						$term->Enumeration( $term->Code(), TRUE );
						$term->Synonym( $record[ 'ISO3' ], kTYPE_EXACT, TRUE );
						if( $record[ 'CodeNum' ] !== NULL )
							$term->Synonym( $record[ 'CodeNum' ], kTYPE_EXACT, TRUE );
						$term->Domain( kDOMAIN_GEOGRAPHY, TRUE );
						$term->Category( kCATEGORY_ADMIN, TRUE );
						$term->Name( $record[ 'Name' ], kDEFAULT_LANGUAGE );
						$term->Commit( $theContainer );
					}
		
					//
					// Save term.
					//
					$term_alt1 = $term;
		
					//
					// Handle node.
					//
					$node = new COntologyNode( $container );
					$node->Term( $term );
					$node->Kind( kTYPE_ENUMERATION, TRUE );
					$node->Type( kTYPE_ENUM, TRUE );
					$node->Domain( kDOMAIN_GEOGRAPHY, TRUE );
					$node->Category( kCATEGORY_ADMIN, TRUE );
					$node->Commit( $container );
					
					//
					// Save node.
					//
					$node_alt1 = $node;
					
					//
					// Handle edge.
					//
					$parent = $nodes[ 'ISO:3166:1:ALPHA-2' ];
					$edge = $node->RelateTo( $container, $enum_of, $parent );
					$edge->Commit( $container );
					
					//
					// Create region edge.
					//
					if( $record[ 'Region' ] !== NULL )
					{
						$region = $_SESSION[ 'REGIONS' ][ $record[ 'Region' ] ];
						$edge = $node->RelateTo( $container, $part_of, $region );
						$edge->Commit( $container );
					}
					
					//
					// Display.
					//
					if( $doDisplay )
						echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
							 ." [$term] [".$node->Node()->getId()."]" );
				}
				else
					$term_alt1 = NULL;
			
				//
				// Handle numeric 3 code.
				//
				if( $record[ 'CodeNum' ] !== NULL )
				{
					//
					// Create numeric 3 term.
					//
					$term
						= new COntologyTerm(
								$theContainer, 
								COntologyTerm::HashIndex(
									$nodes[ 'ISO:3166:1' ]->Term()->GID()
								   .kTOKEN_NAMESPACE_SEPARATOR
								   .$record[ 'CodeNum' ] ) );
					if( ! $term->Persistent() )
					{
						$term->NS( $nodes[ 'ISO:3166:1' ]->Term() );
						$term->Code( $record[ 'CodeNum' ] );
						$term->Kind( kTYPE_ENUMERATION, TRUE );
						$term->Type( kTYPE_ENUM );
						$term->Enumeration( $term->Code(), TRUE );
						$term->Synonym( $record[ 'ISO3' ], kTYPE_EXACT, TRUE );
						if( $record[ 'Code2' ] !== NULL )
							$term->Synonym( $record[ 'Code2' ], kTYPE_EXACT, TRUE );
						$term->Domain( kDOMAIN_GEOGRAPHY, TRUE );
						$term->Category( kCATEGORY_ADMIN, TRUE );
						$term->Name( $record[ 'Name' ], kDEFAULT_LANGUAGE );
						$term->Commit( $theContainer );
					}
		
					//
					// Save term.
					//
					$term_alt2 = $term;
		
					//
					// Handle node.
					//
					$node = new COntologyNode( $container );
					$node->Term( $term );
					$node->Kind( kTYPE_ENUMERATION, TRUE );
					$node->Type( kTYPE_ENUM, TRUE );
					$node->Domain( kDOMAIN_GEOGRAPHY, TRUE );
					$node->Category( kCATEGORY_ADMIN, TRUE );
					$node->Commit( $container );
					
					//
					// Save node.
					//
					$node_alt2 = $node;
					
					//
					// Handle edge.
					//
					$parent = $nodes[ 'ISO:3166:1:NUMERIC-3' ];
					$edge = $node->RelateTo( $container, $enum_of, $parent );
					$edge->Commit( $container );
					
					//
					// Create region edge.
					//
					if( $record[ 'Region' ] !== NULL )
					{
						$region = $_SESSION[ 'REGIONS' ][ $record[ 'Region' ] ];
						$edge = $node->RelateTo( $container, $part_of, $region );
						$edge->Commit( $container );
					}
					
					//
					// Display.
					//
					if( $doDisplay )
						echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
							 ." [$term] [".$node->Node()->getId()."]" );
				}
				else
					$term_alt2 = NULL;
				
				//
				// Handle exact cross-references.
				//
				$trms = array( $term_main, ( ( $term_alt1 !== NULL ) ? $term_alt1 : NULL ),
										   ( ( $term_alt2 !== NULL ) ? $term_alt2 : NULL ) );
				$nods = array( $node_main, ( ( $term_alt1 !== NULL ) ? $node_alt1 : NULL ),
										   ( ( $term_alt2 !== NULL ) ? $node_alt2 : NULL ) );
				for( $i = 0; $i < count( $trms ); $i++ )
				{
					for( $j = 0; $j < count( $trms ); $j++ )
					{
						//
						// Skip same.
						//
						if( $i != $j )
						{
							//
							// Skip missing.
							//
							if( ($trms[ $i ] !== NULL)
							 && ($trms[ $j ] !== NULL) )
							{
								//
								// Relate terms.
								//
								$trms[ $i ]->Xref( $trms[ $j ], kTYPE_EXACT, TRUE );
								//
								// Set used.
								//
								if( $i == 0 )
								{
									//
									// Set in term.
									//
									$trms[ $j ]->Used( $trms[ $i ] );
									//
									// Set in node.
									//
									$edge
										= $nods[ $j ]->RelateTo(
											$container, kTAG_DEFAULT, $nods[ $i ] );
									$edge->Commit( $container );
								}
								//
								// Relate nodes.
								//
								$edge
									= $nods[ $i ]->RelateTo(
										$container, kTAG_REFERENCE_XREF, $nods[ $j ] );
								$edge->Node()->setProperty( kTAG_KIND, kTYPE_EXACT );
								$edge->Commit( $container );
								//
								// Commit term.
								//
								$trms[ $i ]->Commit( $theContainer );
							}
						}
					}
				}
			}
			
			//
			// Display.
			//
			if( $doDisplay )
				echo( "\n" );
		
		} $rs->Close();
		
		//
		// Iterate obsolete codes.
		//
		$query = <<<EOT
SELECT
	`Code_ISO_3166`.`ISO3`,
	`VALID_COUNTRY`.`ISO3` AS `ValidCode`
FROM
	`Code_ISO_3166`
		LEFT JOIN `Code_ISO_3166` `VALID_COUNTRY`
			ON( `VALID_COUNTRY`.`Code` = `Code_ISO_3166`.`Valid` )
WHERE
(
	(`VALID_COUNTRY`.`ISO3` IS NOT NULL) AND
	(`Code_ISO_3166`.`ISO3` IS NOT NULL)
)
EOT;
		$rs = $mysql->Execute( $query );
		foreach( $rs as $record )
		{
			//
			// Get obsolete and valid nodes.
			//
			$obsolete_node = $countries[ $record[ 'ISO3' ] ];
			$valid_node = $countries[ $record[ 'ValidCode' ] ];
			
			//
			// Get obsolete and valid terms.
			//
			$obsolete_term = $obsolete_node->Term();
			$valid_term = $valid_node->Term();
			
			//
			// Set valid reference.
			//
			$obsolete_term->Valid( $valid_term );
			$obsolete_term->Commit( $theContainer );
			
			//
			// Handle edges.
			//
			$edge = $obsolete_node->RelateTo( $container, kTAG_VALID, $valid_node );
			$edge->Commit( $container );
			
			//
			// Display.
			//
			if( $doDisplay )
				echo( "[$obsolete_term] ==> [$valid_term]\n" );
		
		} $rs->Close();
		
	} // LoadISO.

	 
	/*===================================================================================
	 *	LoadMCPD																		*
	 *==================================================================================*/

	/**
	 * Load MCPD.
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
	function LoadMCPD( CContainer $theContainer, $doDisplay = TRUE )
	{
		//
		// Init local storage.
		//
		$nodes = Array();
		$container = array( kTAG_TERM => $theContainer,
							kTAG_NODE => $_SESSION[ kSESSION_NEO4J ] );
		
		//
		// Open MySQL connection.
		//
		$mysql = NewADOConnection( DEFAULT_ANCILLARY_HOST );
		if( ! $mysql )
			throw new Exception( 'Unable to connect to MySQL.' );				// !@! ==>
		$mysql->Execute( "SET CHARACTER SET 'utf8'" );
		$mysql->setFetchMode( ADODB_FETCH_ASSOC );
		
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
		// SCALE-OF.
		//
		$scale_of
			= new COntologyTerm
						( $theContainer, COntologyTerm::HashIndex( kPRED_SCALE_OF ) );
		
		//
		// Get MCPD term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'MCPD' ) );
		if( ! $term->Persistent() )
		{
			$term->Code( 'MCPD' );
			$term->Name
			( 'FAO/IPGRI Multi-Crop Passport Descriptor',
			  kDEFAULT_LANGUAGE );
			$term->Definition
			( 'The list of multi-crop passport descriptors (MCPD) is developed jointly '
			 .'by IPGRI and FAO to provide international standards to facilitate germplasm '
			 .'passport information exchange. These descriptors aim to be compatible with '
			 .'IPGRI crop descriptor lists and with the descriptors used for the FAO '
			 .'World Information and Early Warning System (WIEWS) on plant genetic '
			 .'resources (PGR).', kDEFAULT_LANGUAGE );
			$term->Kind( kTYPE_NAMESPACE, TRUE );
			$term->Domain( kDOMAIN_ACCESSION, TRUE );
			$term->Category( kCATEGORY_PASSPORT, TRUE );
			$term->Version( 'December 2001' );
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
		$nodes[ 'MCPD' ] = $node;

		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
		
		//
		// Get EURISCO MCPD term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'MCPD:EURISCO' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $nodes[ 'MCPD' ] );
			$term->Code( 'EURISCO' );
			$term->Name
			( 'EURISCO extension to the FAO/IPGRI Multi-Crop Passport Descriptor',
			  kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Extensions to the FAO/IPGRI list of multi-crop passport descriptors (MCPD).', kDEFAULT_LANGUAGE );
			$term->Kind( kTYPE_NAMESPACE, TRUE );
			$term->Domain( kDOMAIN_ACCESSION, TRUE );
			$term->Category( kCATEGORY_PASSPORT, TRUE );
			$term->Version( 'March 2011' );
			$term->Commit( $theContainer );
		}

		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )
				 ."\n" );
	 
		/*================================================================================
		 *	INSTCODE																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'MCPD:INSTCODE' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( 'MCPD' );
			$term->Code( 'INSTCODE' );
			$term->Name( 'Holding institute code', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Code of the institute where the accession is maintained. The codes '
			 .'consist of the 3-letter ISO 3166 country code of the country where the '
			 .'institute is located plus a number. The current set of Institute Codes is '
			 .'available from the FAO website (http://apps3.fao.org/wiews/). Note that '
			 .'although you usually see three digits following the country code, there '
			 .'are actually seven characters in the code, allowing four digits.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Pattern( '[A-Z]{3}[0-9]{3,4}' );
			$term->Examples( 'COL002', TRUE );
			$term->Examples( 'USA1001', TRUE );
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
		$_SESSION[ 'NODES' ][ 'INSTCODE' ] = $nodes[ 'INSTCODE' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'MCPD' ] );
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
		 *	ACCENUMB																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'MCPD:ACCENUMB' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( 'MCPD' );
			$term->Code( 'ACCENUMB' );
			$term->Name( 'Accession number', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'This number serves as a unique identifier for accessions within a genebank '
			 .'collection, and is assigned when a sample is entered into the genebank '
			 .'collection.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Examples( 'WAB0026873', TRUE );
			$term->Examples( 'CGN16587', TRUE );
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
		$nodes[ 'ACCENUMB' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'MCPD' ] );
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
		 *	COLLNUMB																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'MCPD:COLLNUMB' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( 'MCPD' );
			$term->Code( 'COLLNUMB' );
			$term->Name( 'Collecting number', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Original number assigned by the collector(s) of the sample, normally '
			 .'composed of the name or initials of the collector(s) followed by a number. '
			 .'This number is essential for identifying duplicates held in different '
			 .'collections.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Examples( 'FA90-110', TRUE );
			$term->Examples( 'BI 1117 016', TRUE );
			$term->Examples( 'ZS030', TRUE );
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
		$nodes[ 'COLLNUMB' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'MCPD' ] );
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
		 *	COLLCODE																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'MCPD:COLLCODE' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( 'MCPD' );
			$term->Code( 'COLLCODE' );
			$term->Name( 'Collecting institute code', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Code of the institute collecting the sample. If the holding institute has '
			 .'collected the material, the collecting institute code (COLLCODE) should be '
			 .'the same as the holding institute code (INSTCODE). '
			 .'Follows INSTCODE standard.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Pattern( '[A-Z]{3}[0-9]{3,4}' );
			$term->Examples( 'COL002', TRUE );
			$term->Examples( 'USA1001', TRUE );
			$term->Xref( $_SESSION[ 'NODES' ][ 'INSTCODE' ], kTYPE_RELATED, TRUE );
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
		$nodes[ 'COLLCODE' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'MCPD' ] );
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
		 *	GENUS																		 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'MCPD:GENUS' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( 'MCPD' );
			$term->Code( 'GENUS' );
			$term->Name( 'Genus', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Genus name for taxon. Initial uppercase letter required.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Domain( kDOMAIN_TAXONOMY, TRUE );
			$term->Category( kCATEGORY_EPITHET, TRUE );
			$term->Examples( 'Allium', TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Domain( kDOMAIN_TAXONOMY, TRUE );
		$node->Category( kCATEGORY_EPITHET, TRUE );
		$node->Commit( $container );
		$nodes[ 'GENUS' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'MCPD' ] );
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
		 *	SPECIES																		 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'MCPD:SPECIES' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( 'MCPD' );
			$term->Code( 'SPECIES' );
			$term->Name( 'Species', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Specific epithet portion of the scientific name in lowercase letters. '
			 .'Following abbreviation is allowed: "sp.".',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Examples( 'paniculatum', TRUE );
			$term->Domain( kDOMAIN_TAXONOMY, TRUE );
			$term->Category( kCATEGORY_EPITHET, TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Domain( kDOMAIN_TAXONOMY, TRUE );
		$node->Category( kCATEGORY_EPITHET, TRUE );
		$node->Commit( $container );
		$nodes[ 'SPECIES' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'MCPD' ] );
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
		 *	SPAUTHOR																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'MCPD:SPAUTHOR' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( 'MCPD' );
			$term->Code( 'SPAUTHOR' );
			$term->Name( 'Species authority', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'The authority for the species name.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Examples( 'L.', TRUE );
			$term->Examples( '(Desf.) B. Fedtsch.', TRUE );
			$term->Domain( kDOMAIN_TAXONOMY, TRUE );
			$term->Category( kCATEGORY_AUTH, TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Domain( kDOMAIN_TAXONOMY, TRUE );
		$node->Category( kCATEGORY_AUTH, TRUE );
		$node->Commit( $container );
		$nodes[ 'SPAUTHOR' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'MCPD' ] );
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
		 *	SUBTAXA																		 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'MCPD:SUBTAXA' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( 'MCPD' );
			$term->Code( 'SUBTAXA' );
			$term->Name( 'Subtaxa', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Subtaxa can be used to store any additional taxonomic identifier, '
			 .'in latin. Following abbreviations are allowed: "subsp." (for subspecies); '
			 .'"convar." (for convariety); "var." (for variety); "f." (for form).',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Examples( 'subsp. fuscum', TRUE );
			$term->Domain( kDOMAIN_TAXONOMY, TRUE );
			$term->Category( kCATEGORY_EPITHET, TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Domain( kDOMAIN_TAXONOMY, TRUE );
		$node->Category( kCATEGORY_EPITHET, TRUE );
		$node->Commit( $container );
		$nodes[ 'SUBTAXA' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'MCPD' ] );
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
		 *	SUBTAUTHOR																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'MCPD:SUBTAUTHOR' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( 'MCPD' );
			$term->Code( 'SUBTAUTHOR' );
			$term->Name( 'Subtaxa authority', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'The subtaxa authority at the most detailed taxonomic level.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Examples( '(Waldst. et Kit.) Arc.', TRUE );
			$term->Domain( kDOMAIN_TAXONOMY, TRUE );
			$term->Category( kCATEGORY_AUTH, TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Domain( kDOMAIN_TAXONOMY, TRUE );
		$node->Category( kCATEGORY_AUTH, TRUE );
		$node->Commit( $container );
		$nodes[ 'SUBTAUTHOR' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'MCPD' ] );
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
		 *	CROPNAME																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'MCPD:CROPNAME' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( 'MCPD' );
			$term->Code( 'CROPNAME' );
			$term->Name( 'Common crop name', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Name of the crop in colloquial language, preferably English.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Examples( 'cauliflower', TRUE );
			$term->Examples( 'white cabbage', TRUE );
			$term->Examples( 'malting barley', TRUE );
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
		$nodes[ 'CROPNAME' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'MCPD' ] );
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
		 *	ACCENAME																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'MCPD:ACCENAME' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( 'MCPD' );
			$term->Code( 'ACCENAME' );
			$term->Name( 'Accession name', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Either a registered or other formal designation given to the accession. '
			 .'First letter uppercase. Multiple names separated with semicolon without '
			 .'space.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Examples( 'Rheinische Vorgebirgstrauben;Emma;Avlon', TRUE );
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
		$nodes[ 'ACCENAME' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'MCPD' ] );
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
		 *	ACQDATE																		 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'MCPD:ACQDATE' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( 'MCPD' );
			$term->Code( 'ACQDATE' );
			$term->Name( 'Acquisition date', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Date on which the accession entered the collection as YYYYMMDD. '
			 .'Missing data (MM or DD) should be indicated with hyphens. Leading zeros '
			 .'are required.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Examples( '1968----', TRUE );
			$term->Examples( '197011--', TRUE );
			$term->Examples( '20020620', TRUE );
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
		$nodes[ 'ACQDATE' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'MCPD' ] );
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
		 *	ORIGCTY																		 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'MCPD:ORIGCTY' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( 'MCPD' );
			$term->Code( 'ORIGCTY' );
			$term->Name( 'Country of origin', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Code of the country in which the sample was originally collected.',
			  kDEFAULT_LANGUAGE );
			$term->Domain( kDOMAIN_GEOGRAPHY, TRUE );
			$term->Category( kCATEGORY_ADMIN, TRUE );
			$term->Examples( 'ITA', TRUE );
			$term->Examples( 'FRA', TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Domain( kDOMAIN_GEOGRAPHY, TRUE );
		$node->Category( kCATEGORY_ADMIN, TRUE );
		$node->Commit( $container );
		$nodes[ 'ORIGCTY' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'MCPD' ] );
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
		 *	ISO 3166-1 ALPHA-3 COUNTRY CODES											 *
		 *===============================================================================*/
		
		//
		// Prepare container.
		//
		$temp_container = array( kTAG_NODE => $_SESSION[ kSESSION_NEO4J ],
								 kTAG_TERM => $theContainer->Database() );
		$edge_container = $theContainer->Database()->selectCollection( kDEFAULT_CNT_EDGES );

		//
		// Prepare query.
		//
		$query = array( kTAG_OBJECT.'.'.kTAG_TERM => 'ISO:3166:1:ALPHA-3',
						kTAG_SUBJECT.'.'.kTAG_TERM => new MongoRegex( '/^ISO:3166:1:/' ),
						kTAG_PREDICATE.'.'.kTAG_TERM => kPRED_ENUM_OF );

		//
		// Prepare fields.
		//
		$fields = array( kTAG_SUBJECT => TRUE );
		
		//
		// Get all ALPHA 3 children.
		//
		$found = $edge_container->find( $query, $fields );
		foreach( $found as $element )
		{
			//
			// Get country node.
			//
			$country
				= new COntologyNode
					( $container, $element[ kTAG_SUBJECT ][ kTAG_NODE ] );
			
			//
			// Relate to MCPD.
			//
			$edge = $country->RelateTo( $container, $enum_of, $node );
			$edge->Commit( $container );
		}
	 
		/*================================================================================
		 *	COLLSITE																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'MCPD:COLLSITE' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( 'MCPD' );
			$term->Code( 'COLLSITE' );
			$term->Name( 'Location of collecting site', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Location information below the country level that describes where the '
			 .'accession was collected. This might include the distance in kilometres '
			 .'and direction from the nearest town, village or map grid reference point.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Examples( '7 km south of Curitiba in the state of Parana', TRUE );
			$term->Domain( kDOMAIN_GEOGRAPHY, TRUE );
			$term->Category( kCATEGORY_ADMIN, TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Domain( kDOMAIN_GEOGRAPHY, TRUE );
		$node->Category( kCATEGORY_ADMIN, TRUE );
		$node->Commit( $container );
		$nodes[ 'COLLSITE' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'MCPD' ] );
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
		 *	LATITUDE																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'MCPD:LATITUDE' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( 'MCPD' );
			$term->Code( 'LATITUDE' );
			$term->Name( 'Latitude of collecting site', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Degree (2 digits) minutes (2 digits), and seconds (2 digits) followed '
			 .'by N (North) or S (South). Every missing digit (minutes or seconds) should '
			 .'be indicated with a hyphen. Leading zeros are required.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Examples( '10----S', TRUE );
			$term->Examples( '011530N', TRUE );
			$term->Examples( '4531--S', TRUE );
			$term->Domain( kDOMAIN_GEOGRAPHY, TRUE );
			$term->Category( kCATEGORY_GEO, TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Domain( kDOMAIN_GEOGRAPHY, TRUE );
		$node->Category( kCATEGORY_GEO, TRUE );
		$node->Commit( $container );
		$nodes[ 'LATITUDE' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'MCPD' ] );
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
		 *	LONGITUDE																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'MCPD:LONGITUDE' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( 'MCPD' );
			$term->Code( 'LONGITUDE' );
			$term->Name( 'Longitude of collecting site', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Degree (3 digits), minutes (2 digits), and seconds (2 digits) followed '
			 .'by E (East) or W (West). Every missing digit (minutes or seconds) should '
			 .'be indicated with a hyphen. Leading zeros are required.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Examples( '0762510W', TRUE );
			$term->Examples( '076----W', TRUE );
			$term->Domain( kDOMAIN_GEOGRAPHY, TRUE );
			$term->Category( kCATEGORY_GEO, TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Domain( kDOMAIN_GEOGRAPHY, TRUE );
		$node->Category( kCATEGORY_GEO, TRUE );
		$node->Commit( $container );
		$nodes[ 'LONGITUDE' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'MCPD' ] );
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
		 *	ELEVATION																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'MCPD:ELEVATION' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( 'MCPD' );
			$term->Code( 'ELEVATION' );
			$term->Name( 'Elevation of collecting site', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Elevation of collecting site expressed in meters above sea level. '
			 .'Negative values are allowed.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_INT32 );
			$term->Examples( '763', TRUE );
			$term->Examples( '-15', TRUE );
			$term->Domain( kDOMAIN_GEOGRAPHY, TRUE );
			$term->Category( kCATEGORY_GEO, TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Domain( kDOMAIN_GEOGRAPHY, TRUE );
		$node->Category( kCATEGORY_GEO, TRUE );
		$node->Commit( $container );
		$nodes[ 'ELEVATION' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'MCPD' ] );
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
		 *	COLLDATE																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'MCPD:COLLDATE' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( 'MCPD' );
			$term->Code( 'COLLDATE' );
			$term->Name( 'Collecting date of sample', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Collecting date of the sample as YYYYMMDD. Missing data (MM or DD) should '
			 .'be indicated with hyphens. Leading zeros are required.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Examples( '1968----', TRUE );
			$term->Examples( '197011--', TRUE );
			$term->Examples( '20020620', TRUE );
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
		$nodes[ 'COLLDATE' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'MCPD' ] );
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
		 *	BREDCODE																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'MCPD:BREDCODE' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( 'MCPD' );
			$term->Code( 'BREDCODE' );
			$term->Name( 'Breeding institute code', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Code of the institute that has bred the material. '
			 .'Follows INSTCODE standard.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Pattern( '[A-Z]{3}[0-9]{3,4}' );
			$term->Examples( 'COL002', TRUE );
			$term->Examples( 'USA1001', TRUE );
			$term->Xref( $_SESSION[ 'NODES' ][ 'INSTCODE' ], kTYPE_RELATED, TRUE );
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
		$nodes[ 'BREDCODE' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'MCPD' ] );
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
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'MCPD:SAMPSTAT' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( 'MCPD' );
			$term->Code( 'SAMPSTAT' );
			$term->Name( 'Biological status of accession', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'The coding scheme proposed can be used at 3 different levels of detail: '
			 .'either by using the general codes such as 100, 200, 300, 400 or by using '
			 .'the more specific codes such as 110, 120 etc.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_ENUM );
			$term->Pattern( '[0-9]{3}' );
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
		$nodes[ 'SAMPSTAT' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'MCPD' ] );
		$edge->Commit( $container );
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
		
		//
		// Load biological status codes.
		//
		$query = <<<EOT
SELECT
	`Code_BiologicalStatus`.`Code`,
	`Code_BiologicalStatus`.`Parent`,
	`Code_BiologicalStatus`.`Label`,
	`Code_BiologicalStatus`.`Description`
FROM
	`Code_BiologicalStatus`
ORDER BY
	`Code_BiologicalStatus`.`Code`
EOT;
		$enums = Array();
		$rs = $mysql->Execute( $query );
		foreach( $rs as $record )
		{
			//
			// Create status term.
			//
			$enum_term
				= new COntologyTerm
					( $theContainer,
					  COntologyTerm::HashIndex
						( $term.kTOKEN_INDEX_SEPARATOR.$record[ 'Code' ] ) );
			if( ! $enum_term->Persistent() )
			{
				$enum_term->NS( $term );
				$enum_term->Code( $record[ 'Code' ] );
				$enum_term->Name( $record[ 'Label' ], kDEFAULT_LANGUAGE );
				$enum_term->Name( $record[ 'Description' ], kDEFAULT_LANGUAGE );
				$enum_term->Kind( kTYPE_ENUMERATION, TRUE );
				$enum_term->Type( kTYPE_ENUM );
				$enum_term->Enumeration( $enum_term->Code(), TRUE );
				$enum_term->Commit( $theContainer );
			}
			
			//
			// Create status node.
			//
			$enum_node = new COntologyNode( $container );
			$enum_node->Term( $enum_term );
			$enum_node->Kind( kTYPE_ENUMERATION, TRUE );
			$enum_node->Commit( $container );
			$enums[ $record[ 'Code' ] ] = $enum_node;
			
			//
			// Handle first level status.
			//
			if( $record[ 'Parent' ] === NULL )
			{
				$edge = $enum_node->RelateTo( $container, $enum_of, $node );
				$edge->Commit( $container );
			}
			
			//
			// Display.
			//
			if( $doDisplay )
				echo( "[$enum_term] "
					 .$enum_term->Name( NULL, kDEFAULT_LANGUAGE )
					 ."\n" );
		
		} $rs->Close();
		
		//
		// Relate biological status codes.
		//
		$query = <<<EOT
SELECT
	`Code_BiologicalStatus`.`Code`,
	`Code_BiologicalStatus`.`Parent`
FROM
	`Code_BiologicalStatus`
WHERE
	`Code_BiologicalStatus`.`Parent` IS NOT NULL
ORDER BY
	`Code_BiologicalStatus`.`Code`
EOT;
		$rs = $mysql->Execute( $query );
		foreach( $rs as $record )
		{
			//
			// Get child term.
			//
			$child_term = $enums[ $record[ 'Code' ] ]->Term();

			//
			// Get child node.
			//
			$child_node = $enums[ $record[ 'Code' ] ];
			
			//
			// Get parent term.
			//
			$parent_term = $enums[ $record[ 'Parent' ] ]->Term();

			//
			// Get parent node.
			//
			$parent_node = $enums[ $record[ 'Parent' ] ];
			
			//
			// Get status edge.
			//
			$edge = $child_node->RelateTo( $container, $enum_of, $parent_node );
			$edge->Commit( $container );
			
			//
			// Display.
			//
			if( $doDisplay )
				echo( "[$child_term] ==> [$parent_term]\n" );
		
		} $rs->Close();
	 
		/*================================================================================
		 *	ANCEST																		 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'MCPD:ANCEST' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( 'MCPD' );
			$term->Code( 'ANCEST' );
			$term->Name( 'Ancestral data', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Information about either pedigree or other description of ancestral '
			 .'information (i.e. parent variety in case of mutant or selection).',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Examples( 'Hanna/7*Atlas//Turk/8*Atlas', TRUE );
			$term->Examples( 'mutation found in Hanna', TRUE );
			$term->Examples( 'selection from Irene', TRUE );
			$term->Examples( 'cross involving amongst others Hanna and Irene', TRUE );
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
		$nodes[ 'ANCEST' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'MCPD' ] );
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
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'MCPD:COLLSRC' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( 'MCPD' );
			$term->Code( 'COLLSRC' );
			$term->Name( 'Collecting/acquisition source', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'The coding scheme proposed can be used at 2 different levels of detail: '
			 .'either by using the general codes (in boldface) such as 10, 20, 30, 40 '
			 .'or by using the more specific codes such as 11, 12 etc.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_ENUM );
			$term->Pattern( '[0-9]{2}' );
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
		$nodes[ 'COLLSRC' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'MCPD' ] );
		$edge->Commit( $container );
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
		
		//
		// Load biological status codes.
		//
		$query = <<<EOT
SELECT
	`Code_AcquisitionSource`.`Code`,
	`Code_AcquisitionSource`.`Parent`,
	`Code_AcquisitionSource`.`Label`,
	`Code_AcquisitionSource`.`Description`
FROM
	`Code_AcquisitionSource`
ORDER BY
	`Code_AcquisitionSource`.`Code`
EOT;
		$enums = Array();
		$rs = $mysql->Execute( $query );
		foreach( $rs as $record )
		{
			//
			// Create status term.
			//
			$enum_term
				= new COntologyTerm
					( $theContainer,
					  COntologyTerm::HashIndex
						( $term.kTOKEN_INDEX_SEPARATOR.$record[ 'Code' ] ) );
			if( ! $enum_term->Persistent() )
			{
				$enum_term->NS( $term );
				$enum_term->Code( $record[ 'Code' ] );
				$enum_term->Name( $record[ 'Label' ], kDEFAULT_LANGUAGE );
				$enum_term->Name( $record[ 'Description' ], kDEFAULT_LANGUAGE );
				$enum_term->Kind( kTYPE_ENUMERATION, TRUE );
				$enum_term->Type( kTYPE_ENUM );
				$enum_term->Enumeration( $enum_term->Code(), TRUE );
				$enum_term->Commit( $theContainer );
			}
			
			//
			// Create status node.
			//
			$enum_node = new COntologyNode( $container );
			$enum_node->Term( $enum_term );
			$enum_node->Kind( kTYPE_ENUMERATION, TRUE );
			$enum_node->Commit( $container );
			$enums[ $record[ 'Code' ] ] = $enum_node;
			
			//
			// Handle first level status.
			//
			if( $record[ 'Parent' ] === NULL )
			{
				$edge = $enum_node->RelateTo( $container, $enum_of, $node );
				$edge->Commit( $container );
			}
			
			//
			// Display.
			//
			if( $doDisplay )
				echo( "[$enum_term] "
					 .$enum_term->Name( NULL, kDEFAULT_LANGUAGE )
					 ."\n" );
		
		} $rs->Close();
		
		//
		// Relate biological status codes.
		//
		$query = <<<EOT
SELECT
	`Code_AcquisitionSource`.`Code`,
	`Code_AcquisitionSource`.`Parent`
FROM
	`Code_AcquisitionSource`
WHERE
	`Code_AcquisitionSource`.`Parent` IS NOT NULL
ORDER BY
	`Code_AcquisitionSource`.`Code`
EOT;
		$rs = $mysql->Execute( $query );
		foreach( $rs as $record )
		{
			//
			// Get child term.
			//
			$child_term = $enums[ $record[ 'Code' ] ]->Term();

			//
			// Get child node.
			//
			$child_node = $enums[ $record[ 'Code' ] ];
			
			//
			// Get parent term.
			//
			$parent_term = $enums[ $record[ 'Parent' ] ]->Term();

			//
			// Get parent node.
			//
			$parent_node = $enums[ $record[ 'Parent' ] ];
			
			//
			// Get status edge.
			//
			$edge = $child_node->RelateTo( $container, $enum_of, $parent_node );
			$edge->Commit( $container );
			
			//
			// Display.
			//
			if( $doDisplay )
				echo( "[$child_term] ==> [$parent_term]\n" );
		
		} $rs->Close();
	 
		/*================================================================================
		 *	DONORCODE																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'MCPD:DONORCODE' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( 'MCPD' );
			$term->Code( 'DONORCODE' );
			$term->Name( 'Donor institute code', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Code of the institute that donated the sample. '
			 .'Follows INSTCODE standard.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Pattern( '[A-Z]{3}[0-9]{3,4}' );
			$term->Examples( 'COL002', TRUE );
			$term->Examples( 'USA1001', TRUE );
			$term->Xref( $_SESSION[ 'NODES' ][ 'INSTCODE' ], kTYPE_RELATED, TRUE );
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
		$nodes[ 'DONORCODE' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'MCPD' ] );
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
		 *	DONORNUMB																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'MCPD:DONORNUMB' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( 'MCPD' );
			$term->Code( 'DONORNUMB' );
			$term->Name( 'Donor accession number', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Accession number assigned to the accession by the donor.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Examples( 'NGB1912', TRUE );
			$term->Xref( $nodes[ 'ACCENUMB' ], kTYPE_RELATED, TRUE );
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
		$nodes[ 'DONORNUMB' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'MCPD' ] );
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
		 *	OTHERNUMB																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'MCPD:OTHERNUMB' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( 'MCPD' );
			$term->Code( 'OTHERNUMB' );
			$term->Name
			( 'Other identification (numbers) associated with the accession',
			  kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Any other identification (numbers) known to exist in other collections for '
			 .'this accession. Use the following system: '
			 .'INSTCODE:ACCENUMB;INSTCODE:ACCENUMB;... INSTCODE and ACCENUMB follow the '
			 .'standard described above and are separated by a colon. Pairs of INSTCODE '
			 .'and ACCENUMB are separated by a semicolon without space. When the institute '
			 .'is not known, the number should be preceded by a colon.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Examples( 'NLD037:CGN00254', TRUE );
			$term->Examples( ':WAB001', TRUE );
			$term->Examples( 'SWE002:NGB1912;NLD037:CGN00254', TRUE );
			$term->Examples( 'SWE002:NGB1912;:Bra2343', TRUE );
			$term->Xref( $_SESSION[ 'NODES' ][ 'INSTCODE' ], kTYPE_RELATED, TRUE );
			$term->Xref( $nodes[ 'ACCENUMB' ], kTYPE_RELATED, TRUE );
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
		$nodes[ 'OTHERNUMB' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'MCPD' ] );
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
		 *	DUPLSITE																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'MCPD:DUPLSITE' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( 'MCPD' );
			$term->Code( 'DUPLSITE' );
			$term->Name( 'Location of safety duplicates', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'FAO Institute Code of the institute where a safety duplicate of the '
			 .'accession is maintained. The codes consist of the 3-letter ISO 3166 '
			 .'country code of the country where the institute is located plus a number.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Pattern( '[A-Z]{3}[0-9]{3,4}' );
			$term->Examples( 'COL002', TRUE );
			$term->Examples( 'USA1001', TRUE );
			$term->Xref( $_SESSION[ 'NODES' ][ 'INSTCODE' ], kTYPE_RELATED, TRUE );
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
		$nodes[ 'DUPLSITE' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'MCPD' ] );
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
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'MCPD:STORAGE' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( 'MCPD' );
			$term->Code( 'STORAGE' );
			$term->Name( 'Type of germplasm storage', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'If germplasm is maintained under different types of storage, multiple '
			 .'choices are allowed (separated by a semicolon). (Refer to FAO/IPGRI '
			 .'Genebank Standards 1994 for details on storage type.)',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_ENUM_SET );
			$term->Pattern( '[0-9]{2}' );
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
		$nodes[ 'STORAGE' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'MCPD' ] );
		$edge->Commit( $container );
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
		
		//
		// Load storage codes.
		//
		$query = <<<EOT
SELECT
	`Code_GermplasmStorage`.`Code`,
	`Code_GermplasmStorage`.`Parent`,
	`Code_GermplasmStorage`.`Label`,
	`Code_GermplasmStorage`.`Description`
FROM
	`Code_GermplasmStorage`
ORDER BY
	`Code_GermplasmStorage`.`Code`
EOT;
		$enums = Array();
		$rs = $mysql->Execute( $query );
		foreach( $rs as $record )
		{
			//
			// Create storage term.
			//
			$enum_term
				= new COntologyTerm
					( $theContainer,
					  COntologyTerm::HashIndex
						( $term.kTOKEN_INDEX_SEPARATOR.$record[ 'Code' ] ) );
			if( ! $enum_term->Persistent() )
			{
				$enum_term->NS( $term );
				$enum_term->Code( $record[ 'Code' ] );
				$enum_term->Name( $record[ 'Label' ], kDEFAULT_LANGUAGE );
				$enum_term->Name( $record[ 'Description' ], kDEFAULT_LANGUAGE );
				$enum_term->Kind( kTYPE_ENUMERATION, TRUE );
				$enum_term->Type( kTYPE_ENUM );
				$enum_term->Enumeration( $enum_term->Code(), TRUE );
				$enum_term->Commit( $theContainer );
			}
			
			//
			// Create storage node.
			//
			$enum_node = new COntologyNode( $container );
			$enum_node->Term( $enum_term );
			$enum_node->Kind( kTYPE_ENUMERATION, TRUE );
			$enum_node->Commit( $container );
			$enums[ $record[ 'Code' ] ] = $enum_node;
			
			//
			// Handle first level storage.
			//
			if( $record[ 'Parent' ] === NULL )
			{
				$edge = $enum_node->RelateTo( $container, $enum_of, $node );
				$edge->Commit( $container );
			}
			
			//
			// Display.
			//
			if( $doDisplay )
				echo( "[$enum_term] "
					 .$enum_term->Name( NULL, kDEFAULT_LANGUAGE )
					 ."\n" );
		
		} $rs->Close();
		
		//
		// Relate storage codes.
		//
		$query = <<<EOT
SELECT
	`Code_GermplasmStorage`.`Code`,
	`Code_GermplasmStorage`.`Parent`
FROM
	`Code_GermplasmStorage`
WHERE
	`Code_GermplasmStorage`.`Parent` IS NOT NULL
ORDER BY
	`Code_GermplasmStorage`.`Code`
EOT;
		$rs = $mysql->Execute( $query );
		foreach( $rs as $record )
		{
			//
			// Get child term.
			//
			$child_term = $enums[ $record[ 'Code' ] ]->Term();

			//
			// Get child node.
			//
			$child_node = $enums[ $record[ 'Code' ] ];
			
			//
			// Get parent term.
			//
			$parent_term = $enums[ $record[ 'Parent' ] ]->Term();

			//
			// Get parent node.
			//
			$parent_node = $enums[ $record[ 'Parent' ] ];
			
			//
			// Get status edge.
			//
			$edge = $child_node->RelateTo( $container, $enum_of, $parent_node );
			$edge->Commit( $container );
			
			//
			// Display.
			//
			if( $doDisplay )
				echo( "[$child_term] ==> [$parent_term]\n" );
		
		} $rs->Close();
	 
		/*================================================================================
		 *	REMARKS																		 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'MCPD:REMARKS' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( 'MCPD' );
			$term->Code( 'REMARKS' );
			$term->Name( 'Remarks', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'The remarks field is used to add notes or to elaborate on descriptors with '
			 .'value 99 or 999 (=Other). Prefix remarks with the field name they refer to '
			 .'and a colon. Separate remarks referring to different fields are separated '
			 .'by semicolons without space.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Examples( 'COLLSRC:roadside', TRUE );
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
		$nodes[ 'NICODE' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'MCPD' ] );
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
		 *	NICODE																		 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'MCPD:EURISCO:NICODE' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( 'MCPD:EURISCO' );
			$term->Code( 'NICODE' );
			$term->Name( 'National Inventory code', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Code identifying the National Inventory; the code of the country '
			 .'preparing the National Inventory. Exceptions are possible, if agreed '
			 .'with EURISCO such as NGB.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Pattern( '[A-Z]{3}' );
			$term->Examples( 'NGB', TRUE );
			$term->Examples( 'NLD', TRUE );
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
		$nodes[ 'NICODE' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'MCPD' ] );
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
		 *	COLLDESCR																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'MCPD:EURISCO:COLLDESCR' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( 'MCPD:EURISCO' );
			$term->Code( 'COLLDESCR' );
			$term->Name( 'Decoded collecting institute', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Brief name and location of the collecting institute. Only to be used '
			 .'if COLLCODE can not be used since the FAO Institution Code for this '
			 .'institute is not (yet) available.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Examples( 'Tuinartikelen Jan van Zomeren, Arnhem, The Netherlands',
							 TRUE );
			$term->Xref( $nodes[ 'COLLCODE' ], kTYPE_RELATED, TRUE );
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
		$nodes[ 'COLLDESCR' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'MCPD' ] );
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
		 *	BREDDESCR																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'MCPD:EURISCO:BREDDESCR' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( 'MCPD:EURISCO' );
			$term->Code( 'BREDDESCR' );
			$term->Name( 'Decoded breeding institute', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Brief name and location of the breeding institute. Only to be used '
			 .'if BREDCODE can not be used since the FAO Institution Code for this '
			 .'institute is not (yet) available.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Examples( 'CFFR from Chile', TRUE );
			$term->Xref( $nodes[ 'BREDCODE' ], kTYPE_RELATED, TRUE );
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
		$nodes[ 'BREDDESCR' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'MCPD' ] );
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
		 *	DONORDESCR																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'MCPD:EURISCO:DONORDESCR' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( 'MCPD:EURISCO' );
			$term->Code( 'DONORDESCR' );
			$term->Name( 'Decoded donor institute', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Brief name and location of the donor institute. Only to be used '
			 .'if DONORCODE can not be used since the FAO Institution Code for this '
			 .'institute is not (yet) available.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Examples( 'Nelly Goudwaard, Groningen, The Netherlands', TRUE );
			$term->Xref( $nodes[ 'DONORCODE' ], kTYPE_RELATED, TRUE );
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
		$nodes[ 'DONORDESCR' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'MCPD' ] );
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
		 *	DUPLDESCR																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'MCPD:EURISCO:DUPLDESCR' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( 'MCPD:EURISCO' );
			$term->Code( 'DUPLDESCR' );
			$term->Name( 'Decoded safety duplication location', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Brief name and location of the institute maintaining the safety duplicate. '
			 .'Only to be used if DUPLSITE can not be used since the FAO Institution Code '
			 .'for this institute is not (yet) available.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Examples( 'Pakhoed Freezers inc., Paramaribo, Surinam', TRUE );
			$term->Xref( $nodes[ 'DUPLSITE' ], kTYPE_RELATED, TRUE );
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
		$nodes[ 'DUPLDESCR' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'MCPD' ] );
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
		 *	ACCEURL																		 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'MCPD:EURISCO:ACCEURL' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( 'MCPD:EURISCO' );
			$term->Code( 'ACCEURL' );
			$term->Name( 'Accession URL', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'URL linking to additional data about the accession either in the holding '
			 .'genebank or from another source.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Examples( 'http://www.cgn.wageningen-ur.nl/pgr/collections/passdeta.asp?accenumb=CGN04848',
							 TRUE );
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
		$nodes[ 'ACCEURL' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'MCPD' ] );
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
		 *	MLSSTAT																		 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'MCPD:EURISCO:MLSSTAT' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( 'MCPD:EURISCO' );
			$term->Code( 'MLSSTAT' );
			$term->Name( 'MLS Status', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'The coded status of an accession with regards to the Multilateral System '
			 .'(MLS) of the International Treaty on Plant Genetic Resources for Food and '
			 .'Agriculture. Provides the information, whether the accession is included '
			 .'in the MLS. The value should be a single character: 0 means that it '
			 .'is not part of the MLS and 1 means it is part of the MLS; If the MLS status '
			 .'is unknown, the field stays empty',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Examples( '1', TRUE );
			$term->Examples( '0', TRUE );
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
		$nodes[ 'MLSSTAT' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'MCPD' ] );
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
		 *	AEGISSTAT																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'MCPD:EURISCO:AEGISSTAT' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( 'MCPD:EURISCO' );
			$term->Code( 'AEGISSTAT' );
			$term->Name( 'AEGIS Status', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'The coded status of an accession with regards to the European Genebank '
			 .'Integrated System (AEGIS). Provides the information, whether the accession '
			 .'is conserved for AEGIS. The value should be a single character: 0 means '
			 .'that it is not part of AEGIS and 1 means it is part of AEGIS; If the AEGIS '
			 .'status is unknown, the field stays empty',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Examples( '1', TRUE );
			$term->Examples( '0', TRUE );
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
		$nodes[ 'AEGISSTAT' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'MCPD' ] );
		$edge->Commit( $container );
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
		
	} // LoadMCPD.

	 
	/*===================================================================================
	 *	LoadDatadictStructs																*
	 *==================================================================================*/

	/**
	 * Load base structures.
	 *
	 * This function will load the default structures used by data dictionaries.
	 *
	 * If the last parameter is <i>TRUE</i>, the function will display the name of the
	 * created terms.
	 *
	 * @param CContainer			$theContainer		Collection.
	 * @param boolean				$doDisplay			Display created terms.
	 *
	 * @access protected
	 */
	function LoadDatadictStructs( CContainer $theContainer, $doDisplay = TRUE )
	{
		//
		// Init local storage.
		//
		$nodes = Array();
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
		// ENUM-OF.
		//
		$enum_of = new COntologyTerm
						( $theContainer, COntologyTerm::HashIndex( kPRED_ENUM_OF ) );
		
		//
		// COMPONENT-OF.
		//
		$component_of = new COntologyTerm
						( $theContainer, COntologyTerm::HashIndex( kPRED_COMPONENT_OF ) );
		
		//
		// Get default namespace.
		//
		$ns = new COntologyTerm( $theContainer, COntologyTerm::HashIndex( '' ) );
		$len = strlen( (string) $ns ) + 1;
		
		//
		// Handle language (kTAG_LANGUAGE).
		//
		$term = new COntologyTerm( $theContainer, 
								   COntologyTerm::HashIndex( kTAG_LANGUAGE ) );
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Type( kTYPE_STRING );
		$node->Cardinality( kCARD_0_1 );
		$node->Commit( $container );
		//
		// Save node.
		//
		$parent = $nodes[ kTAG_LANGUAGE ] = $node;
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] (kTAG_LANGUAGE) "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
		
		//
		// Handle English.
		//
		$term = new COntologyTerm( $theContainer, 
								   COntologyTerm::HashIndex( 'ISO:639:3:Part1:en' ) );
		//
		// Get node.
		//
		$list = $term->Node();
		//
		// Handle node.
		//
		$node = new COntologyNode( $container, $list[ 0 ] );
		//
		// Handle edge.
		//
		$edge = $node->RelateTo( $container, $enum_of, $parent );
		$edge->Commit( $container );
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
		
		//
		// Handle French.
		//
		$term = new COntologyTerm( $theContainer, 
								   COntologyTerm::HashIndex( 'ISO:639:3:Part1:fr' ) );
		//
		// Get node.
		//
		$list = $term->Node();
		//
		// Handle node.
		//
		$node = new COntologyNode( $container, $list[ 0 ] );
		//
		// Handle edge.
		//
		$edge = $node->RelateTo( $container, $enum_of, $parent );
		$edge->Commit( $container );
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
		
		//
		// Handle Spanish.
		//
		$term = new COntologyTerm( $theContainer, 
								   COntologyTerm::HashIndex( 'ISO:639:3:Part1:es' ) );
		//
		// Get node.
		//
		$list = $term->Node();
		//
		// Handle node.
		//
		$node = new COntologyNode( $container, $list[ 0 ] );
		//
		// Handle edge.
		//
		$edge = $node->RelateTo( $container, $enum_of, $parent );
		$edge->Commit( $container );
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
		
		//
		// Handle Russian.
		//
		$term = new COntologyTerm( $theContainer, 
								   COntologyTerm::HashIndex( 'ISO:639:3:Part1:ru' ) );
		//
		// Get node.
		//
		$list = $term->Node();
		//
		// Handle node.
		//
		$node = new COntologyNode( $container, $list[ 0 ] );
		//
		// Handle edge.
		//
		$edge = $node->RelateTo( $container, $enum_of, $parent );
		$edge->Commit( $container );
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
		
		//
		// Handle data (kTAG_DATA).
		//
		$term = new COntologyTerm( $theContainer, 
								   COntologyTerm::HashIndex( kTAG_DATA ) );
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Type( kTYPE_STRING );
		$node->Cardinality( kCARD_1 );
		$node->Commit( $container );
		//
		// Save node.
		//
		$nodes[ kTAG_DATA ] = $node;
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] (kTAG_DATA) "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	kTAG_NAME																	 *
		 *===============================================================================*/

		//
		// Handle name (kTAG_NAME).
		//
		$term = new COntologyTerm( $theContainer, 
								   COntologyTerm::HashIndex( kTAG_NAME ) );
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Type( kTYPE_LIST );
		$node->Cardinality( kCARD_ANY );
		$node->Commit( $container );
		//
		// Save node.
		//
		$parent = $_SESSION[ 'NODES' ][ kTAG_NAME ] = $node;
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] (kTAG_NAME) "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );

		//
		// Connect language.
		//
		$edge = $nodes[ kTAG_LANGUAGE ]->RelateTo( $container, $component_of, $parent );
		$edge->Commit( $container );

		//
		// Connect data.
		//
		$edge = $nodes[ kTAG_DATA ]->RelateTo( $container, $component_of, $parent );
		$edge->Commit( $container );
	 
		/*================================================================================
		 *	kTAG_DESCRIPTION															 *
		 *===============================================================================*/

		//
		// Handle description (kTAG_DESCRIPTION).
		//
		$term = new COntologyTerm( $theContainer, 
								   COntologyTerm::HashIndex( kTAG_DESCRIPTION ) );
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Type( kTYPE_LIST );
		$node->Cardinality( kCARD_ANY );
		$node->Commit( $container );
		//
		// Save node.
		//
		$parent = $_SESSION[ 'NODES' ][ kTAG_DESCRIPTION ] = $node;
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] (kTAG_DESCRIPTION) "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );

		//
		// Connect language.
		//
		$edge = $nodes[ kTAG_LANGUAGE ]->RelateTo( $container, $component_of, $parent );
		$edge->Commit( $container );

		//
		// Connect data.
		//
		$edge = $nodes[ kTAG_DATA ]->RelateTo( $container, $component_of, $parent );
		$edge->Commit( $container );
	 
		/*================================================================================
		 *	kTAG_DEFINITION																 *
		 *===============================================================================*/

		//
		// Handle definition (kTAG_DEFINITION).
		//
		$term = new COntologyTerm( $theContainer, 
								   COntologyTerm::HashIndex( kTAG_DEFINITION ) );
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Type( kTYPE_LIST );
		$node->Cardinality( kCARD_ANY );
		$node->Commit( $container );
		//
		// Save node.
		//
		$parent = $_SESSION[ 'NODES' ][ kTAG_DEFINITION ] = $node;
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] (kTAG_DEFINITION) "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );

		//
		// Connect language.
		//
		$edge = $nodes[ kTAG_LANGUAGE ]->RelateTo( $container, $component_of, $parent );
		$edge->Commit( $container );

		//
		// Connect data.
		//
		$edge = $nodes[ kTAG_DATA ]->RelateTo( $container, $component_of, $parent );
		$edge->Commit( $container );
		
	} // LoadDatadictStructs.

	 
	/*===================================================================================
	 *	LoadEntityDatadict																*
	 *==================================================================================*/

	/**
	 * Load entity data dictionary.
	 *
	 * This function will load the entity data dictionary.
	 *
	 * If the last parameter is <i>TRUE</i>, the function will display the name of the
	 * created terms.
	 *
	 * @param CContainer			$theContainer		Collection.
	 * @param boolean				$doDisplay			Display created terms.
	 *
	 * @access protected
	 */
	function LoadEntityDatadict( CContainer $theContainer, $doDisplay = TRUE )
	{
		//
		//	[:ENTITY:USER]
		//		Code()			[:CODE] (string)		*
		//		Password()		[:PASS] (string)
		//		Name()			[:NAME] (string)		*
		//		Email()			[:EMAIL] (string)		*
		//		Kind()			[:KIND] (array)			*
		//		Relate()		[:REFS] (array)			*
		//							[:KIND] (scalar)
		//							[:DATA] (scalar)
		//		Preferred()		[:PREFERRED] (scalar)	*
		//		Used()			[:DEFAULT] (scalar)		*
		//		Valid()			[:VALID] (scalar)		*
		//		Manager()		[:MANAGER] (scalar)		*
		//		Role()			[:ROLE] (array)
		//		Created()		[:CREATED] (stamp)		*
		//		Modified()		[:MODIFIED] (stamp)		*
		//
		
		//
		// Init local storage.
		//
		$nodes = Array();
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
		$is_a = new COntologyTerm
						( $theContainer, COntologyTerm::HashIndex( kPRED_IS_A ) );
		
		//
		// ENUM-OF.
		//
		$enum_of = new COntologyTerm
						( $theContainer, COntologyTerm::HashIndex( kPRED_ENUM_OF ) );
		
		//
		// COMPONENT-OF.
		//
		$component_of = new COntologyTerm
						( $theContainer, COntologyTerm::HashIndex( kPRED_COMPONENT_OF ) );
		
		//
		// METHOD-OF.
		//
		$method_of = new COntologyTerm
						( $theContainer, COntologyTerm::HashIndex( kPRED_METHOD_OF ) );
		
		//
		// Get default namespace.
		//
		$ns = new COntologyTerm( $theContainer, COntologyTerm::HashIndex( '' ) );
		$len = strlen( (string) $ns ) + 1;
	 
		/*================================================================================
		 *	kENTITY_USER																 *
		 *===============================================================================*/

		//
		// Handle kENTITY_USER.
		//
		$term = new COntologyTerm( $theContainer, 
								   COntologyTerm::HashIndex( kENTITY_USER ) );
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Commit( $container );
		//
		// Save node.
		//
		$nodes[ kENTITY_USER ] = $node;
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] (kENTITY_USER) "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	kTAG_CODE																	 *
		 *===============================================================================*/

		//
		// Handle user code (kTAG_CODE).
		//
		$term = new COntologyTerm( $theContainer, 
								   COntologyTerm::HashIndex( kTAG_CODE ) );
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Type( kTYPE_STRING );
		$node->Cardinality( kCARD_1 );
		$node->Commit( $container );
		//
		// Save node.
		//
		$_SESSION[ 'NODES' ][ kTAG_CODE ] = $nodes[ kTAG_CODE ] = $node;
		//
		// Handle edge.
		//
		$edge = $node->RelateTo( $container, $component_of, $nodes[ kENTITY_USER ] );
		$edge->Commit( $container );
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] (kTAG_CODE) "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	kOFFSET_PASSWORD															 *
		 *===============================================================================*/

		//
		// Handle user password (kOFFSET_PASSWORD).
		//
		$term = new COntologyTerm( $theContainer, 
								   COntologyTerm::HashIndex( kOFFSET_PASSWORD ) );
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Type( kTYPE_STRING );
		$node->Cardinality( kCARD_1 );
		$node->Commit( $container );
		//
		// Save node.
		//
		$nodes[ kOFFSET_PASSWORD ] = $node;
		//
		// Handle edge.
		//
		$edge = $node->RelateTo( $container, $component_of, $nodes[ kENTITY_USER ] );
		$edge->Commit( $container );
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] (kOFFSET_PASSWORD) "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	kTAG_NAME (user)															 *
		 *===============================================================================*/

		//
		// Handle user name (kTAG_NAME).
		//
		$term = new COntologyTerm( $theContainer, 
								   COntologyTerm::HashIndex( kTAG_NAME ) );
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Type( kTYPE_STRING );
		$node->Cardinality( kCARD_1 );
		$node->Commit( $container );
		//
		// Save node.
		//
		$nodes[ kTAG_NAME ] = $node;
		//
		// Handle edge.
		//
		$edge = $node->RelateTo( $container, $component_of, $nodes[ kENTITY_USER ] );
		$edge->Commit( $container );
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] (kTAG_NAME) "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	kOFFSET_EMAIL (user)														 *
		 *===============================================================================*/

		//
		// Handle user e-mail (kOFFSET_EMAIL).
		//
		$term = new COntologyTerm( $theContainer, 
								   COntologyTerm::HashIndex( kOFFSET_EMAIL ) );
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Type( kTYPE_STRING );
		$node->Cardinality( kCARD_1 );
		$node->Commit( $container );
		//
		// Save node.
		//
		$nodes[ kOFFSET_EMAIL ] = $node;
		//
		// Handle edge.
		//
		$edge = $node->RelateTo( $container, $component_of, $nodes[ kENTITY_USER ] );
		$edge->Commit( $container );
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] (kOFFSET_EMAIL) "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	kTAG_KIND																	 *
		 *===============================================================================*/

		//
		// Handle user kind (kTAG_KIND).
		//
		$term = new COntologyTerm( $theContainer, 
								   COntologyTerm::HashIndex( kTAG_KIND ) );
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Type( kTYPE_STRING );
		$node->Cardinality( kCARD_ANY );
		$node->Commit( $container );
		//
		// Save node.
		//
		$nodes[ kTAG_KIND ] = $node;
		//
		// Handle edge.
		//
		$edge = $node->RelateTo( $container, $component_of, $nodes[ kENTITY_USER ] );
		$edge->Commit( $container );
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] (kTAG_KIND) "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	kTAG_REFS																	 *
		 *===============================================================================*/

		//
		// Handle user references (kTAG_REFS).
		//
		$term = new COntologyTerm( $theContainer, 
								   COntologyTerm::HashIndex( kTAG_REFS ) );
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Type( kTYPE_LIST );
		$node->Cardinality( kCARD_ANY );
		$node->Commit( $container );
		//
		// Save node.
		//
		$parent = $nodes[ kTAG_REFS ] = $node;
		//
		// Handle edge.
		//
		$edge = $node->RelateTo( $container, $component_of, $nodes[ kENTITY_USER ] );
		$edge->Commit( $container );
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] (kTAG_REFS) "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
		//
		// Handle user reference kind (kTAG_KIND).
		//
		$term = new COntologyTerm( $theContainer, 
								   COntologyTerm::HashIndex( kTAG_KIND ) );
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Cardinality( kCARD_0_1 );
		$node->Commit( $container );
		//
		// Handle edge.
		//
		$edge = $node->RelateTo( $container, $component_of, $parent );
		$edge->Commit( $container );
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] (kTAG_KIND) "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
		//
		// Handle user reference data (kTAG_DATA).
		//
		$term = new COntologyTerm( $theContainer, 
								   COntologyTerm::HashIndex( kTAG_DATA ) );
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Cardinality( kCARD_1 );
		$node->Commit( $container );
		//
		// Handle edge.
		//
		$edge = $node->RelateTo( $container, $component_of, $parent );
		$edge->Commit( $container );
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] (kTAG_DATA) "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	kTAG_PREFERRED																 *
		 *===============================================================================*/

		//
		// Handle preferred user reference (kTAG_PREFERRED).
		//
		$term = new COntologyTerm( $theContainer, 
								   COntologyTerm::HashIndex( kTAG_PREFERRED ) );
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Cardinality( kCARD_0_1 );
		$node->Commit( $container );
		//
		// Save node.
		//
		$nodes[ kTAG_PREFERRED ] = $node;
		//
		// Handle edge.
		//
		$edge = $node->RelateTo( $container, $component_of, $nodes[ kENTITY_USER ] );
		$edge->Commit( $container );
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] (kTAG_PREFERRED) "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	kTAG_DEFAULT																 *
		 *===============================================================================*/

		//
		// Handle default user reference (kTAG_DEFAULT).
		//
		$term = new COntologyTerm( $theContainer, 
								   COntologyTerm::HashIndex( kTAG_DEFAULT ) );
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Cardinality( kCARD_0_1 );
		$node->Commit( $container );
		//
		// Save node.
		//
		$nodes[ kTAG_DEFAULT ] = $node;
		//
		// Handle edge.
		//
		$edge = $node->RelateTo( $container, $component_of, $nodes[ kENTITY_USER ] );
		$edge->Commit( $container );
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] (kTAG_DEFAULT) "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	kTAG_VALID																	 *
		 *===============================================================================*/

		//
		// Handle valid user reference (kTAG_VALID).
		//
		$term = new COntologyTerm( $theContainer, 
								   COntologyTerm::HashIndex( kTAG_VALID ) );
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Cardinality( kCARD_0_1 );
		$node->Commit( $container );
		//
		// Save node.
		//
		$nodes[ kTAG_VALID ] = $node;
		//
		// Handle edge.
		//
		$edge = $node->RelateTo( $container, $component_of, $nodes[ kENTITY_USER ] );
		$edge->Commit( $container );
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] (kTAG_VALID) "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	kTAG_ROLE																	 *
		 *===============================================================================*/

		//
		// Handle user roles (kTAG_ROLE).
		//
		$term = new COntologyTerm( $theContainer, 
								   COntologyTerm::HashIndex( kTAG_ROLE ) );
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$term->Type( kTYPE_ENUM );
		$node->Cardinality( kCARD_ANY );
		$node->Commit( $container );
		//
		// Save node.
		//
		$parent = $_SESSION[ 'NODES' ][ kTAG_ROLE ] = $node;
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] (kTAG_ROLE) "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
		
		//
		// Set namespace.
		//
		$ns = $term;
		$len = strlen( (string) $ns ) + 1;
		
		//
		// Handle file import (kROLE_FILE_IMPORT).
		//
		$term = new COntologyTerm( $theContainer, 
								   COntologyTerm::HashIndex( kROLE_FILE_IMPORT ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $ns );
			$term->Code( substr( kROLE_FILE_IMPORT, $len ) );
			$term->Kind( kTYPE_ENUMERATION, TRUE );
			$term->Type( kTYPE_ENUM );
			$term->Enumeration( $term->Code(), TRUE );
			$term->Name(
				"File import",
				kDEFAULT_LANGUAGE );
			$term->Definition(
				"Dataset file importer.",
				kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		}
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_ENUMERATION, TRUE );
		$node->Type( kTYPE_ENUM, TRUE );
		$node->Commit( $container );
		//
		// Connect edge.
		//
		$edge = $node->RelateTo( $container, $enum_of, $parent );
		$edge->Commit( $container );
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] (kROLE_FILE_IMPORT) "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
		
		//
		// Handle file import (kROLE_USER_MANAGE).
		//
		$term = new COntologyTerm( $theContainer, 
								   COntologyTerm::HashIndex( kROLE_USER_MANAGE ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $ns );
			$term->Code( substr( kROLE_USER_MANAGE, $len ) );
			$term->Kind( kTYPE_ENUMERATION, TRUE );
			$term->Type( kTYPE_ENUM );
			$term->Enumeration( $term->Code(), TRUE );
			$term->Name(
				"User management",
				kDEFAULT_LANGUAGE );
			$term->Definition(
				"Create, modify and delete users.",
				kDEFAULT_LANGUAGE );
			$term->Commit( $theContainer );
		}
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_ENUMERATION, TRUE );
		$node->Type( kTYPE_ENUM, TRUE );
		$node->Commit( $container );
		//
		// Connect edge.
		//
		$edge = $node->RelateTo( $container, $enum_of, $parent );
		$edge->Commit( $container );
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] (kROLE_USER_MANAGE) "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	kTAG_CREATED																 *
		 *===============================================================================*/

		//
		// Handle user creation time-stamp (kTAG_CREATED).
		//
		$term = new COntologyTerm( $theContainer, 
								   COntologyTerm::HashIndex( kTAG_CREATED ) );
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Type( kTYPE_STAMP );
		$node->Cardinality( kCARD_0_1 );
		$node->Commit( $container );
		//
		// Save node.
		//
		$nodes[ kTAG_CREATED ] = $node;
		//
		// Handle edge.
		//
		$edge = $node->RelateTo( $container, $component_of, $nodes[ kENTITY_USER ] );
		$edge->Commit( $container );
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] (kTAG_CREATED) "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	kTAG_MODIFIED																 *
		 *===============================================================================*/

		//
		// Handle user last modification time-stamp (kTAG_MODIFIED).
		//
		$term = new COntologyTerm( $theContainer, 
								   COntologyTerm::HashIndex( kTAG_MODIFIED ) );
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Type( kTYPE_STAMP );
		$node->Cardinality( kCARD_0_1 );
		$node->Commit( $container );
		//
		// Save node.
		//
		$nodes[ kTAG_MODIFIED ] = $node;
		//
		// Handle edge.
		//
		$edge = $node->RelateTo( $container, $component_of, $nodes[ kENTITY_USER ] );
		$edge->Commit( $container );
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] (kTAG_MODIFIED) "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
		
	} // LoadEntityDatadict.

	 
	/*===================================================================================
	 *	LoadDatasetDatadict																*
	 *==================================================================================*/

	/**
	 * Load dataset data dictionary.
	 *
	 * This function will load the dataset data dictionary.
	 *
	 * If the last parameter is <i>TRUE</i>, the function will display the name of the
	 * created terms.
	 *
	 * @param CContainer			$theContainer		Collection.
	 * @param boolean				$doDisplay			Display created terms.
	 *
	 * @access protected
	 */
	function LoadDatasetDatadict( CContainer $theContainer, $doDisplay = TRUE )
	{
		//
		//	[:DATASET]
		//		[:TITLE] (string) Dataset title
		//		[:ENTITY:USER] (binary) User ID
		//		[:NAME] (array) Dataset name
		//			[:LANGUAGE] (string) Dataset name language code
		//			[:DATA] (string) Dataset name string
		//		[:DESCR] (struct) Dataset description
		//			[:LANGUAGE] (string) Dataset description language code
		//			[:DATA] (string) Dataset description string
		//		[:DOMAIN] (array) Dataset domain
		//		[:CATEGORY] (array) Dataset domain category
		//		[:FILES] (array)
		//			[:FILE] (ObjectId) File reference
		//			[:REFS] (array) Referenced files list
		//			[:STATUS] (array) File status
		//			[:KIND] (array) File kind
		//			[:COLS] (array) Data dictionary
		//				[:TAG] (string) Tag GID
		//				[:TITLE] (string) Original column header value
		//
		
		//
		// Init local storage.
		//
		$nodes = Array();
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
		$is_a = new COntologyTerm
						( $theContainer, COntologyTerm::HashIndex( kPRED_IS_A ) );
		
		//
		// ENUM-OF.
		//
		$enum_of = new COntologyTerm
						( $theContainer, COntologyTerm::HashIndex( kPRED_ENUM_OF ) );
		
		//
		// COMPONENT-OF.
		//
		$component_of = new COntologyTerm
						( $theContainer, COntologyTerm::HashIndex( kPRED_COMPONENT_OF ) );
		
		//
		// Get default namespace.
		//
		$ns = new COntologyTerm( $theContainer, COntologyTerm::HashIndex( '' ) );
		$len = strlen( (string) $ns ) + 1;
	 
		/*================================================================================
		 *	kTAG_DATASET																 *
		 *===============================================================================*/

		//
		// Handle kTAG_DATASET.
		//
		$term = new COntologyTerm( $theContainer, 
								   COntologyTerm::HashIndex( kTAG_DATASET ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $ns );
			$term->Code( substr( kTAG_DATASET, $len ) );
			$term->Name( 'Dataset', kDEFAULT_LANGUAGE );
			$term->Definition( 'Data collection.', kDEFAULT_LANGUAGE );
			$term->Synonym( 'kTAG_DATASET', kTYPE_EXACT, TRUE );
			$term->Commit( $theContainer );
		}
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_ROOT, TRUE );
		$node->Kind( kTYPE_DICTIONARY, TRUE );
		$node->Commit( $container );
		//
		// Save node.
		//
		$nodes[ kTAG_DATASET ] = $node;
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] (kTAG_DATASET) "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	kTAG_TITLE																	 *
		 *===============================================================================*/

		//
		// Handle dataset title (kTAG_TITLE).
		//
		$term = new COntologyTerm( $theContainer, 
								   COntologyTerm::HashIndex( kTAG_TITLE ) );
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Type( kTYPE_STRING );
		$node->Cardinality( kCARD_1 );
		$node->Commit( $container );
		//
		// Save node.
		//
		$nodes[ kTAG_TITLE ] = $node;
		//
		// Handle edge.
		//
		$edge = $node->RelateTo( $container, $component_of, $nodes[ kTAG_DATASET ] );
		$edge->Commit( $container );
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] (kTAG_TITLE) "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	kENTITY_USER																 *
		 *===============================================================================*/

		//
		// Handle dataset user (kENTITY_USER).
		//
		$term = new COntologyTerm( $theContainer, 
								   COntologyTerm::HashIndex( kENTITY_USER ) );
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Type( kTYPE_BINARY );
		$node->Cardinality( kCARD_1 );
		$node->Commit( $container );
		//
		// Save node.
		//
		$nodes[ kENTITY_USER ] = $node;
		//
		// Handle edge.
		//
		$edge = $node->RelateTo( $container, $component_of, $nodes[ kTAG_DATASET ] );
		$edge->Commit( $container );
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] (kENTITY_USER) "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	kTAG_NAME																	 *
		 *===============================================================================*/

		//
		// Handle dataset name (kTAG_NAME).
		//
		$node = $_SESSION[ 'NODES' ][ kTAG_NAME ];
		$term = $node->Term();
		//
		// Save node.
		//
		$nodes[ kTAG_NAME ] = $node;
		//
		// Handle edge.
		//
		$edge = $node->RelateTo( $container, $component_of, $nodes[ kTAG_DATASET ] );
		$edge->Commit( $container );
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] (kTAG_NAME) "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	kTAG_DESCRIPTION															 *
		 *===============================================================================*/

		//
		// Handle dataset description (kTAG_DESCRIPTION).
		//
		$node = $_SESSION[ 'NODES' ][ kTAG_DESCRIPTION ];
		$term = $node->Term();
		//
		// Save node.
		//
		$nodes[ kTAG_DESCRIPTION ] = $node;
		//
		// Handle edge.
		//
		$edge = $node->RelateTo( $container, $component_of, $nodes[ kTAG_DATASET ] );
		$edge->Commit( $container );
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] (kTAG_DESCRIPTION) "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	kTAG_DOMAIN																	 *
		 *===============================================================================*/

		//
		// Handle dataset domain (kTAG_DOMAIN).
		//
		$term = new COntologyTerm( $theContainer, 
								   COntologyTerm::HashIndex( kTAG_DOMAIN ) );
		//
		// Get term node.
		//
		$list = $term->Node();
		//
		// Handle node.
		//
		$node = new COntologyNode( $container, $list[ 0 ] );
		//
		// Handle edge.
		//
		$edge = $node->RelateTo( $container, $component_of, $nodes[ kTAG_DATASET ] );
		$edge->Commit( $container );
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] (kTAG_DOMAIN) "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	kTAG_CATEGORY																 *
		 *===============================================================================*/

		//
		// Handle dataset category (kTAG_CATEGORY).
		//
		$term = new COntologyTerm( $theContainer, 
								   COntologyTerm::HashIndex( kTAG_CATEGORY ) );
		//
		// Get term node.
		//
		$list = $term->Node();
		//
		// Handle node.
		//
		$node = new COntologyNode( $container, $list[ 0 ] );
		//
		// Handle edge.
		//
		$edge = $node->RelateTo( $container, $component_of, $nodes[ kTAG_DATASET ] );
		$edge->Commit( $container );
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] (kTAG_CATEGORY) "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	kOFFSET_FILES																 *
		 *===============================================================================*/

		//
		// Handle dataset files (kOFFSET_FILES).
		//
		$term = new COntologyTerm( $theContainer, 
								   COntologyTerm::HashIndex( kOFFSET_FILES ) );
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Type( kTYPE_LIST );
		$node->Cardinality( kCARD_ANY );
		$node->Commit( $container );
		//
		// Save node.
		//
		$nodes[ kOFFSET_FILES ] = $node;
		//
		// Handle edge.
		//
		$edge = $node->RelateTo( $container, $component_of, $nodes[ kTAG_DATASET ] );
		$edge->Commit( $container );
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] (kOFFSET_FILES) "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	kOFFSET_FILE																 *
		 *===============================================================================*/

		//
		// Handle dataset provided file reference (kOFFSET_FILE).
		//
		$term = new COntologyTerm( $theContainer, 
								   COntologyTerm::HashIndex( kOFFSET_FILE ) );
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Type( kTYPE_MongoId );
		$node->Cardinality( kCARD_1 );
		$node->Commit( $container );
		//
		// Handle edge.
		//
		$edge = $node->RelateTo( $container, $component_of, $nodes[ kOFFSET_FILES ] );
		$edge->Commit( $container );
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] (kOFFSET_FILE) "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	kTAG_REFS																	 *
		 *===============================================================================*/

		//
		// Handle dataset file references (kTAG_REFS).
		//
		$term = new COntologyTerm( $theContainer, 
								   COntologyTerm::HashIndex( kTAG_REFS ) );
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Type( kTYPE_MongoId );
		$node->Cardinality( kCARD_ANY );
		$node->Commit( $container );
		//
		// Handle edge.
		//
		$edge = $node->RelateTo( $container, $component_of, $nodes[ kOFFSET_FILES ] );
		$edge->Commit( $container );
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] (kTAG_REFS) "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	kTAG_STATUS																	 *
		 *===============================================================================*/

		//
		// Handle dataset provided file status (kTAG_STATUS).
		//
		$term = new COntologyTerm( $theContainer, 
								   COntologyTerm::HashIndex( kTAG_STATUS ) );
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Type( kTYPE_STRING );
		$node->Cardinality( kCARD_ANY );
		$node->Commit( $container );
		//
		// Handle edge.
		//
		$edge = $node->RelateTo( $container, $component_of, $nodes[ kOFFSET_FILES ] );
		$edge->Commit( $container );
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] (kTAG_STATUS) "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	kTAG_KIND																	 *
		 *===============================================================================*/

		//
		// Handle dataset file kind (kTAG_KIND).
		//
		$term = new COntologyTerm( $theContainer, 
								   COntologyTerm::HashIndex( kTAG_KIND ) );
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Type( kTYPE_STRING );
		$node->Cardinality( kCARD_ANY );
		$node->Commit( $container );
		//
		// Handle edge.
		//
		$edge = $node->RelateTo( $container, $component_of, $nodes[ kOFFSET_FILES ] );
		$edge->Commit( $container );
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] (kTAG_KIND) "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	kOFFSET_COLS																 *
		 *===============================================================================*/

		//
		// Handle dataset file column metadata (kOFFSET_COLS).
		//
		$term = new COntologyTerm( $theContainer, 
								   COntologyTerm::HashIndex( kOFFSET_COLS ) );
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Type( kTYPE_LIST );
		$node->Cardinality( kCARD_ANY );
		$node->Commit( $container );
		//
		// Save node.
		//
		$nodes[ kOFFSET_COLS ] = $node;
		//
		// Handle edge.
		//
		$edge = $node->RelateTo( $container, $component_of, $nodes[ kOFFSET_FILES ] );
		$edge->Commit( $container );
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] (kOFFSET_COLS) "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	kTAG_TAG																	 *
		 *===============================================================================*/

		//
		// Handle dataset generated file column metadata tag (kTAG_TAG).
		//
		$term = new COntologyTerm( $theContainer, 
								   COntologyTerm::HashIndex( kTAG_TAG ) );
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Type( kTYPE_STRING );
		$node->Cardinality( kCARD_1 );
		$node->Commit( $container );
		//
		// Handle edge.
		//
		$edge = $node->RelateTo( $container, $component_of, $nodes[ kOFFSET_COLS ] );
		$edge->Commit( $container );
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] (kTAG_TAG) "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	kTAG_TITLE																	 *
		 *===============================================================================*/

		//
		// Handle dataset generated file column metadata title (kTAG_TITLE).
		//
		$term = new COntologyTerm( $theContainer, 
								   COntologyTerm::HashIndex( kTAG_TITLE ) );
		//
		// Handle node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Type( kTYPE_STRING );
		$node->Cardinality( kCARD_1 );
		$node->Commit( $container );
		//
		// Handle edge.
		//
		$edge = $node->RelateTo( $container, $component_of, $nodes[ kOFFSET_COLS ] );
		$edge->Commit( $container );
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] (kTAG_TITLE) "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
		
	} // LoadDatasetDatadict.


	/*===================================================================================
	 *	LoadFAOInstituteDDict															*
	 *==================================================================================*/

	/**
	 * Load MCPD.
	 *
	 * Load FAO/WIEWS institutes data sictionary.
	 *
	 * This function will load the FAO/WIEWS institutes ontology.
	 *
	 * If the last parameter is <i>TRUE</i>, the function will display the name of the
	 * created terms.
	 *
	 * @param CContainer			$theContainer		Collection.
	 * @param boolean				$doDisplay			Display created terms.
	 *
	 * @access protected
	 */
	function LoadFAOInstituteDDict( CContainer $theContainer, $doDisplay = TRUE )
	{
		//
		// Init local storage.
		//
		$nodes = Array();
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
		$is_a = new COntologyTerm
						( $theContainer, COntologyTerm::HashIndex( kPRED_IS_A ) );
		
		//
		// Get FAO institutes namespace.
		//
		$ns = new COntologyTerm( $theContainer, COntologyTerm::HashIndex( 'FAO:INST' ) );
		$len = strlen( (string) $ns ) + 1;
		
		//
		// Create node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $ns );
		$node->Kind( kTYPE_ROOT, TRUE );
		$node->Kind( kTYPE_DICTIONARY, TRUE );
		$node->Commit( $container );
		//
		// Save node.
		//
		$nodes[ 'FAO:INST' ] = $node;
	 
		/*================================================================================
		 *	INSTCODE																	 *
		 *===============================================================================*/

		//
		// Node.
		//
		$node = $_SESSION[ 'NODES' ][ 'INSTCODE' ];
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'FAO:INST' ] );
		$edge->Commit( $container );
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[".$node->Term()->GID()."] "
				 .$node->Term()->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
	 
		/*================================================================================
		 *	ACRONYM																		 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'FAO:INST:ACRONYM' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $ns );
			$term->Code( 'ACRONYM' );
			$term->Name( 'Collecting number', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Original number assigned by the collector(s) of the sample, normally '
			 .'composed of the name or initials of the collector(s) followed by a number. '
			 .'This number is essential for identifying duplicates held in different '
			 .'collections.',
			  kDEFAULT_LANGUAGE );
			$term->Kind( kTYPE_ATTRIBUTE, TRUE );
			$term->Cardinality( kCARD_0_1 );
			$term->Type( kTYPE_STRING );
			$term->Examples( 'USAID', TRUE );
			$term->Examples( 'BMZ', TRUE );
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
		$nodes[ 'ACRONYM' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'FAO:INST' ] );
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
		 *	ECPACRONYM																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( kENTITY_INST_FAO_EPACRONYM ) );
		if( ! $term->Persistent() )
			throw new CException( 'Missing ECPACRONYM of FAO institutes.' );	// !@! ==>
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Commit( $container );
		$nodes[ 'ACRONYM' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'FAO:INST' ] );
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
		 *	FULL_NAME																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'FAO:INST:FULL_NAME' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $ns );
			$term->Code( 'FULL_NAME' );
			$term->Name( 'Full name', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Full institution name.',
			  kDEFAULT_LANGUAGE );
			$term->Kind( kTYPE_ATTRIBUTE, TRUE );
			$term->Cardinality( kCARD_0_1 );
			$term->Type( kTYPE_STRING );
			$term->Examples( 'Pan American Development Foundation', TRUE );
			$term->Examples( 'Department of agricultural Extension/Education', TRUE );
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
		$nodes[ 'FULL_NAME' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'FAO:INST' ] );
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
		 *	TYPE																		 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( kENTITY_INST_FAO_TYPE ) );
		if( ! $term->Persistent() )
			throw new CException( 'Missing TYPE of FAO institutes.' );	// !@! ==>
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Commit( $container );
		$nodes[ 'TYPE' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'FAO:INST' ] );
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
		 *	PGR_ACTIVITY																 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( kENTITY_INST_FAO_ACT_PGR ) );
		if( ! $term->Persistent() )
			throw new CException( 'Missing PGR_ACTIVITY of FAO institutes.' );	// !@! ==>
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Commit( $container );
		$nodes[ 'PGR_ACTIVITY' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'FAO:INST' ] );
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
		 *	MAINTCOLL																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( kENTITY_INST_FAO_ACT_COLL ) );
		if( ! $term->Persistent() )
			throw new CException( 'Missing MAINTCOLL of FAO institutes.' );	// !@! ==>
		
		//
		// Node.
		//
		$node = new COntologyNode( $container );
		$node->Term( $term );
		$node->Kind( kTYPE_TRAIT, TRUE );
		$node->Kind( kTYPE_MEASURE, TRUE );
		$node->Commit( $container );
		$nodes[ 'MAINTCOLL' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'FAO:INST' ] );
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
		 *	STREET_POB																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'FAO:INST:STREET_POB' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $ns );
			$term->Code( 'STREET_POB' );
			$term->Name( 'Street or post office box number', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Street or post office box number.',
			  kDEFAULT_LANGUAGE );
			$term->Kind( kTYPE_ATTRIBUTE, TRUE );
			$term->Cardinality( kCARD_0_1 );
			$term->Type( kTYPE_STRING );
			$term->Examples( '1301 West Gregory Drive', TRUE );
			$term->Examples( 'Apartado 4661', TRUE );
			$term->Examples( 'PO Box 3214', TRUE );
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
		$nodes[ 'STREET_POB' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'FAO:INST' ] );
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
		 *	CITY_STATE																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'FAO:INST:CITY_STATE' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $ns );
			$term->Code( 'CITY_STATE' );
			$term->Name( 'City and state', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'City and state.',
			  kDEFAULT_LANGUAGE );
			$term->Kind( kTYPE_ATTRIBUTE, TRUE );
			$term->Cardinality( kCARD_0_1 );
			$term->Type( kTYPE_STRING );
			$term->Examples( 'Roma (RM)', TRUE );
			$term->Examples( 'Wuhan, Hubei Province', TRUE );
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
		$nodes[ 'CITY_STATE' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'FAO:INST' ] );
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
		 *	ZIP_CODE																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'FAO:INST:ZIP_CODE' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $ns );
			$term->Code( 'ZIP_CODE' );
			$term->Name( 'Zip code', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Zip code.',
			  kDEFAULT_LANGUAGE );
			$term->Kind( kTYPE_ATTRIBUTE, TRUE );
			$term->Cardinality( kCARD_0_1 );
			$term->Type( kTYPE_STRING );
			$term->Examples( 'Roma (RM)', TRUE );
			$term->Examples( 'Wuhan, Hubei Province', TRUE );
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
		$nodes[ 'CITY_STATE' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'FAO:INST' ] );
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
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'FAO:INST:PHONE' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $ns );
			$term->Code( 'PHONE' );
			$term->Name( 'Phone', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Telephone number.',
			  kDEFAULT_LANGUAGE );
			$term->Kind( kTYPE_ATTRIBUTE, TRUE );
			$term->Cardinality( kCARD_0_1 );
			$term->Type( kTYPE_STRING );
			$term->Examples( '870126', TRUE );
			$term->Examples( '(+58-243) 2831932', TRUE );
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
		$nodes[ 'PHONE' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'FAO:INST' ] );
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
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'FAO:INST:FAX' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $ns );
			$term->Code( 'FAX' );
			$term->Name( 'Phone', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Telefax number.',
			  kDEFAULT_LANGUAGE );
			$term->Kind( kTYPE_ATTRIBUTE, TRUE );
			$term->Cardinality( kCARD_0_1 );
			$term->Type( kTYPE_STRING );
			$term->Examples( '870126', TRUE );
			$term->Examples( '(+58-243) 2831932', TRUE );
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
		$nodes[ 'FAX' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'FAO:INST' ] );
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
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'FAO:INST:EMAIL' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $ns );
			$term->Code( 'EMAIL' );
			$term->Name( 'E-mail', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'E-mail address.',
			  kDEFAULT_LANGUAGE );
			$term->Kind( kTYPE_ATTRIBUTE, TRUE );
			$term->Cardinality( kCARD_0_1 );
			$term->Type( kTYPE_STRING );
			$term->Examples( 'recfitog@reacciun.ve', TRUE );
			$term->Examples( 'ntaylor@ca.uky.edu', TRUE );
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
		$nodes[ 'EMAIL' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'FAO:INST' ] );
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
		 *	URL																			 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'FAO:INST:URL' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $ns );
			$term->Code( 'URL' );
			$term->Name( 'URL', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Institution web page.',
			  kDEFAULT_LANGUAGE );
			$term->Kind( kTYPE_ATTRIBUTE, TRUE );
			$term->Cardinality( kCARD_0_1 );
			$term->Type( kTYPE_STRING );
			$term->Examples( 'http://www.uky.edu/Ag/Agronomy/Department/CloverGC', TRUE );
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
		$nodes[ 'URL' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'FAO:INST' ] );
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
		 *	LATITUDE																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'FAO:INST:LATITUDE' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $ns );
			$term->Code( 'LATITUDE' );
			$term->Name( 'Latitude', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Institution location latitude.',
			  kDEFAULT_LANGUAGE );
			$term->Kind( kTYPE_ATTRIBUTE, TRUE );
			$term->Cardinality( kCARD_0_1 );
			$term->Type( kTYPE_INT32 );
			$term->Examples( '5007', TRUE );
			$term->Examples( '1019', TRUE );
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
		$nodes[ 'LATITUDE' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'FAO:INST' ] );
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
		 *	LONGITUDE																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'FAO:INST:LONGITUDE' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $ns );
			$term->Code( 'LONGITUDE' );
			$term->Name( 'Longitude', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Institution location longitude.',
			  kDEFAULT_LANGUAGE );
			$term->Kind( kTYPE_ATTRIBUTE, TRUE );
			$term->Cardinality( kCARD_0_1 );
			$term->Type( kTYPE_INT32 );
			$term->Examples( '-6739', TRUE );
			$term->Examples( '2430', TRUE );
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
		$nodes[ 'LONGITUDE' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'FAO:INST' ] );
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
		 *	ALTITUDE																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'FAO:INST:ALTITUDE' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $ns );
			$term->Code( 'ALTITUDE' );
			$term->Name( 'Altitude', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Institution elevation.',
			  kDEFAULT_LANGUAGE );
			$term->Kind( kTYPE_ATTRIBUTE, TRUE );
			$term->Cardinality( kCARD_0_1 );
			$term->Type( kTYPE_INT32 );
			$term->Examples( '480', TRUE );
			$term->Examples( '0', TRUE );
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
		$nodes[ 'ALTITUDE' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'FAO:INST' ] );
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
		 *	UPDATED_ON																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'FAO:INST:UPDATED_ON' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $ns );
			$term->Code( 'UPDATED_ON' );
			$term->Name( 'Last updated', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Last record update date.',
			  kDEFAULT_LANGUAGE );
			$term->Kind( kTYPE_ATTRIBUTE, TRUE );
			$term->Cardinality( kCARD_1 );
			$term->Type( kTYPE_STRING );
			$term->Pattern( 'YYYY-MM-DD', TRUE );
			$term->Examples( '2008-10-12', TRUE );
			$term->Examples( '2002-06-14', TRUE );
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
		$nodes[ 'UPDATED_ON' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'FAO:INST' ] );
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
		 *	V_INSTCODE																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'FAO:INST:V_INSTCODE' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $ns );
			$term->Code( 'V_INSTCODE' );
			$term->Name( 'Valid institute', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Institute records cannot be deleted, they can only be set as obsolete '
			 .'by indicating in this field which is the new institution that takes the '
			 .'place of the obsolete one.',
			  kDEFAULT_LANGUAGE );
			$term->Kind( kTYPE_ATTRIBUTE, TRUE );
			$term->Cardinality( kCARD_0_1 );
			$term->Type( kTYPE_STRING );
			$term->Pattern( '[A-Z]{3}[0-9]{3,4}' );
			$term->Examples( 'COL002', TRUE );
			$term->Examples( 'USA1001', TRUE );
			$term->Xref( $_SESSION[ 'NODES' ][ 'INSTCODE' ], kTYPE_EXACT, TRUE );
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
		$nodes[ 'V_INSTCODE' ] = $node;
		
		//
		// Edge.
		//
		$edge = $node->RelateTo( $container, $is_a, $nodes[ 'FAO:INST' ] );
		$edge->Commit( $container );
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "[$term] "
				 .$term->Name( NULL, kDEFAULT_LANGUAGE )." {"
				 .$node->Node()->getId()."}"
				 ."\n" );
		
	} // LoadFAOInstituteDDict.

	 
	/*===================================================================================
	 *	LoadFAOInstitutes																*
	 *==================================================================================*/

	/**
	 * Load FAO institutes.
	 *
	 * This function will load the current FAO institutes.
	 *
	 * If the last parameter is <i>TRUE</i>, the function will display the name of the
	 * created terms.
	 *
	 * @param CContainer			$theContainer		Collection.
	 * @param boolean				$doDisplay			Display created terms.
	 *
	 * @access protected
	 */
	function LoadFAOInstitutes( CContainer $theContainer, $doDisplay = TRUE )
	{
		//
		// Inform.
		//
		if( $doDisplay )
		{
			echo( "\n".__FUNCTION__."\n" );
			echo( "------------------\n" );
		}
		
		//
		// Import FAO institutes.
		//
		$count = CFAOInstitute::Import( $theContainer );
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( "Imported [$count] FAO institutes.\n" );
		
	} // LoadFAOInstitutes.


?>
