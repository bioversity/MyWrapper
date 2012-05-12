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
	Connect( kDEFAULT_DATABASE, kDEFAULT_DICTIONARY, FALSE );
	
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
	LoadUnStatsRegions( $_SESSION[ kSESSION_CONTAINER ], TRUE );
	LoadISO( $_SESSION[ kSESSION_CONTAINER ], TRUE );
	LoadMCPD( $_SESSION[ kSESSION_CONTAINER ], TRUE );

} // TRY BLOCK.

//
// CATCH BLOCK.
//
catch( Exception $error )
{
//	echo( CException::AsHTML( $error ) );
	echo( (string) $error );
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
					  $theContainer = kDEFAULT_DICTIONARY,
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
		// Select collection.
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
		// Default namespace.
		//
		$ns = new COntologyTerm();
		$ns->Code( '' );
        $ns->Name( 'Default namespace', kDEFAULT_LANGUAGE );
		$ns->Definition
		( 'The default namespace is used to qualify all attributes and other terms that '
		 .'constitute the default vocabulary for the ontology. Elements of this ontology '
		 .'are used to create all other ontologies.',
		  kDEFAULT_LANGUAGE );
		$ns->Kind( kTYPE_NAMESPACE, TRUE );
		$ns->Commit( $theContainer );
		if( $doDisplay )
			echo( $ns->Name( NULL, kDEFAULT_LANGUAGE )." [$ns]\n" );
		
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
		// Get default namespace.
		//
		$ns
			= CPersistentUnitObject::NewObject
				( $theContainer, COntologyTermObject::HashIndex( '' ),
				  kFLAG_STATE_ENCODED );
		if( ! $ns )
			throw new Exception
				( 'Unable to find default namsepace [].' );						// !@! ==>
	
		//
		// IS-A.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_PREDICATE );
		$term->Code( substr( kPRED_IS_A, 1 ) );
		$term->Name( 'Is-a', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This predicate is equivalent to a subclass, it can be used to '
		 .'relate a term to the default category to which it belongs '
		 .'within the current ontology.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kPRED_IS_A', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
		
		//
		// PART-OF.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_PREDICATE );
		$term->Code( substr( kPRED_PART_OF, 1 ) );
		$term->Name( 'Part-of', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This predicate indicates that the subject is part of the object.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kPRED_PART_OF', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
		
		//
		// SCALE-OF.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_PREDICATE );
		$term->Code( substr( kPRED_SCALE_OF, 1 ) );
		$term->Name( 'Scale-of', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This predicate is used to relate a term that can be used to '
		 .'annotate data with its method term or trait term.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kPRED_SCALE_OF', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
		
		//
		// METHOD-OF.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_PREDICATE );
		$term->Code( substr( kPRED_METHOD_OF, 1 ) );
		$term->Name( 'Method-of', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This predicate is used to relate a term that defines a measurement '
		 .'method to the trait term.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kPRED_METHOD_OF', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
		
		//
		// ENUM-OF.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_PREDICATE );
		$term->Code( substr( kPRED_ENUM_OF, 1 ) );
		$term->Name( 'Enumeration-of', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This predicate is used to relate enumeration terms, '
		 .'this edge type relates these terms in a hierarchy, '
		 .'in which the subject is a subclass of the object.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kPRED_ENUM_OF', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
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
		// Get default namespace.
		//
		$ns
			= CPersistentUnitObject::NewObject
				( $theContainer, COntologyTermObject::HashIndex( '' ),
				  kFLAG_STATE_ENCODED );
		if( ! $ns )
			throw new Exception
				( 'Unable to find default namsepace [].' );						// !@! ==>
	
		//
		// String.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ENUMERATION );
		$term->Code( substr( kTYPE_STRING, 1 ) );
		$term->Name( 'String', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents the primitive string data type.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kTYPE_STRING', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// 32 bit integer.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ENUMERATION );
		$term->Code( substr( kTYPE_INT32, 1 ) );
		$term->Name( '32 bit integer', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents the primitive 32 bit integer data type.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kTYPE_INT32', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// 64 bit integer.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ENUMERATION );
		$term->Code( substr( kTYPE_INT64, 1 ) );
		$term->Name( '32 bit integer', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents the primitive 64 bit integer data type.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kTYPE_INT64', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Floating point number.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ENUMERATION );
		$term->Code( substr( kTYPE_FLOAT, 1 ) );
		$term->Name( 'Float', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents the primitive floating point number data type.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kTYPE_FLOAT', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Boolean.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ENUMERATION );
		$term->Code( substr( kTYPE_BOOLEAN, 1 ) );
		$term->Name( 'Boolean', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This value represents the primitive boolean data type, it is assumed that it is '
		 .'provided as 1/0.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kTYPE_BOOLEAN', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
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
		// Get default namespace.
		//
		$ns
			= CPersistentUnitObject::NewObject
				( $theContainer, COntologyTermObject::HashIndex( '' ),
				  kFLAG_STATE_ENCODED );
		if( ! $ns )
			throw new Exception
				( 'Unable to find default namsepace [].' );						// !@! ==>
	
		//
		// Date.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ENUMERATION );
		$term->Code( substr( kTYPE_DATE, 1 ) );
		$term->Name( 'Date', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents a date represented as a YYYYMMDD string in which missing '
		 .'elements should be omitted. This means that if we don\'t know the day we can '
		 .'express that date as YYYYMM string. The year is required and the month is '
		 .'required if you provide the day.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kTYPE_DATE', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Time.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ENUMERATION );
		$term->Code( substr( kTYPE_TIME, 1 ) );
		$term->Name( 'Time', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents a date represented as a YYYY-MM-DD HH:MM:SS string '
		 .'in which you may not have missing elements.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kTYPE_TIME', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Regular expression.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ENUMERATION );
		$term->Code( substr( kTYPE_REGEX, 1 ) );
		$term->Name( 'Regular expression', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents a regular expression string type.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kTYPE_REGEX', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Seconds.
		//
		$term = new COntologyTerm();
		$term->Kind( kTYPE_ENUMERATION );
		$term->Code( kTYPE_STAMP_SEC );
		$term->Name( 'Seconds', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents the number of seconds since January 1st, 1970.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kTYPE_STAMP_SEC', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Microseconds.
		//
		$term = new COntologyTerm();
		$term->Kind( kTYPE_ENUMERATION );
		$term->Code( kTYPE_STAMP_USEC );
		$term->Name( 'Microseconds', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents the number of microseconds.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kTYPE_STAMP_USEC', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Binary string.
		//
		$term = new COntologyTerm();
		$term->Kind( kTYPE_ENUMERATION );
		$term->Code( kTYPE_BINARY_STRING );
		$term->Name( 'Binary string', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents a binary string.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kTYPE_BINARY_STRING', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Binary string type.
		//
		$term = new COntologyTerm();
		$term->Kind( kTYPE_ENUMERATION );
		$term->Code( kTYPE_BINARY_TYPE );
		$term->Name( 'Binary string type', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents a binary string type.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kTYPE_BINARY_TYPE', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
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
		// Get default namespace.
		//
		$ns
			= CPersistentUnitObject::NewObject
				( $theContainer, COntologyTermObject::HashIndex( '' ),
				  kFLAG_STATE_ENCODED );
		if( ! $ns )
			throw new Exception
				( 'Unable to find default namsepace [].' );						// !@! ==>
	
		//
		// Binary.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ENUMERATION );
		$term->Code( substr( kTYPE_BINARY, 1 ) );
		$term->Name( 'Binary', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents a binary string data type, in general it will be '
		 .'as a structure containing a binary string in hexadecimal format.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kTYPE_BINARY', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Timestamp.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ENUMERATION );
		$term->Code( substr( kTYPE_STAMP, 1 ) );
		$term->Name( 'Time-stamp', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents a date, time and milliseconds stamp, in general '
		 .'it will be a structure holding the number of secods since January 1st 1970 '
		 .'and optionally the number of milliseconds.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kTYPE_STAMP', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Enumeration.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ENUMERATION );
		$term->Code( substr( kTYPE_ENUM, 1 ) );
		$term->Name( 'Enumeration', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents an enumeration container, enumerations are '
		 .'a controlled vocabulary in which one may only choose one element. '
		 .'This data type implies that the term forms a tree whose siblings '
		 .'are the enumeration elements.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kTYPE_ENUM', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Set.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ENUMERATION );
		$term->Code( substr( kTYPE_ENUM_SET, 1 ) );
		$term->Name( 'Enumerated set', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents an enumerated set container, sets are '
		 .'a controlled vocabulary from which one may choose one or more elements. '
		 .'This data type implies that the term forms a tree whose siblings '
		 .'are the enumeration elements.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kTYPE_ENUM_SET', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
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
		// Get default namespace.
		//
		$ns
			= CPersistentUnitObject::NewObject
				( $theContainer, COntologyTermObject::HashIndex( '' ),
				  kFLAG_STATE_ENCODED );
		if( ! $ns )
			throw new Exception
				( 'Unable to find default namsepace [].' );						// !@! ==>
	
		//
		// PHP.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ENUMERATION );
		$term->Code( substr( kTYPE_PHP, 1 ) );
		$term->Name( 'PHP', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents PHP-encoded data.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kTYPE_PHP', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// JSON.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ENUMERATION );
		$term->Code( substr( kTYPE_JSON, 1 ) );
		$term->Name( 'JSON', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents JSON-encoded data.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kTYPE_JSON', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// XML.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ENUMERATION );
		$term->Code( substr( kTYPE_XML, 1 ) );
		$term->Name( 'XML', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents XML-encoded data.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kTYPE_XML', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// HTML.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ENUMERATION );
		$term->Code( substr( kTYPE_HTML, 1 ) );
		$term->Name( 'HTML', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents HTML-encoded data.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kTYPE_HTML', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// CSV.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ENUMERATION );
		$term->Code( substr( kTYPE_CSV, 1 ) );
		$term->Name( 'Comma separated values', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents CSV-encoded data.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kTYPE_CSV', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// SVG.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ENUMERATION );
		$term->Code( substr( kTYPE_SVG, 1 ) );
		$term->Name( 'Scalable Vector Graphics', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents the Scalable Vector Graphics data type.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kTYPE_SVG', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// PNG.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ENUMERATION );
		$term->Code( substr( kTYPE_PNG, 1 ) );
		$term->Name( 'Portable Network Graphics', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents the Portable Network Graphics data type.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kTYPE_PNG', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Metadata.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ENUMERATION );
		$term->Code( substr( kTYPE_META, 1 ) );
		$term->Name( 'Metadata', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents meta-data.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kTYPE_META', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
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
		// Get default namespace.
		//
		$ns
			= CPersistentUnitObject::NewObject
				( $theContainer, COntologyTermObject::HashIndex( '' ),
				  kFLAG_STATE_ENCODED );
		if( ! $ns )
			throw new Exception
				( 'Unable to find default namsepace [].' );						// !@! ==>
		
		//
		// MongoId.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ENUMERATION );
		$term->Code( substr( kTYPE_MongoId, 1 ) );
		$term->Name( 'MongoId', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents a MongoId type.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kTYPE_MongoId', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
		
		//
		// MongoCode.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ENUMERATION );
		$term->Code( substr( kTYPE_MongoCode, 1 ) );
		$term->Name( 'MongoCode', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents a MongoCode type.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kTYPE_MongoCode', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
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
		// Get default namespace.
		//
		$ns
			= CPersistentUnitObject::NewObject
				( $theContainer, COntologyTermObject::HashIndex( '' ),
				  kFLAG_STATE_ENCODED );
		if( ! $ns )
			throw new Exception
				( 'Unable to find default namsepace [].' );						// !@! ==>
		
		//
		// Exact reference.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ENUMERATION );
		$term->Code( substr( kTYPE_EXACT, 1 ) );
		$term->Name( 'Exact reference', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents an exact reference or synonym.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kTYPE_EXACT', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Broad reference.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ENUMERATION );
		$term->Code( substr( kTYPE_BROAD, 1 ) );
		$term->Name( 'Broad reference', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents a broad reference or synonym.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kTYPE_BROAD', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Narrow reference.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ENUMERATION );
		$term->Code( substr( kTYPE_NARROW, 1 ) );
		$term->Name( 'Narrow reference', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents a narrow reference or synonym.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kTYPE_NARROW', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Related reference.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ENUMERATION );
		$term->Code( substr( kTYPE_RELATED, 1 ) );
		$term->Name( 'Related reference', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents a related reference or synonym.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kTYPE_RELATED', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
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
		// Get default namespace.
		//
		$ns
			= CPersistentUnitObject::NewObject
				( $theContainer, COntologyTermObject::HashIndex( '' ),
				  kFLAG_STATE_ENCODED );
		if( ! $ns )
			throw new Exception
				( 'Unable to find default namsepace [].' );						// !@! ==>
		
		//
		// Trait.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ENUMERATION );
		$term->Code( substr( kTYPE_TRAIT, 1 ) );
		$term->Name( 'Trait', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents a generic trait.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kTYPE_TRAIT', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$ns]\n" );
		
		//
		// Method.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ENUMERATION );
		$term->Code( substr( kTYPE_METHOD, 1 ) );
		$term->Name( 'Method', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents a generic method.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kTYPE_METHOD', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$ns]\n" );
	
		//
		// Namespace term.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ENUMERATION );
		$term->Code( substr( kTYPE_NAMESPACE, 1 ) );
		$term->Name( 'Namespace', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents a namespace.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kTYPE_NAMESPACE', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Root term.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ENUMERATION );
		$term->Code( substr( kTYPE_ROOT, 1 ) );
		$term->Name( 'Root', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents a root term or node.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kTYPE_ROOT', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Predicate term.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ENUMERATION );
		$term->Code( substr( kTYPE_PREDICATE, 1 ) );
		$term->Name( 'Predicate', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents a predicate.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kTYPE_PREDICATE', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Attribute term.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ENUMERATION );
		$term->Code( substr( kTYPE_ATTRIBUTE, 1 ) );
		$term->Name( 'Attribute', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents an attribute.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kTYPE_ATTRIBUTE', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Measure term.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ENUMERATION );
		$term->Code( substr( kTYPE_MEASURE, 1 ) );
		$term->Name( 'Measure', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents a scale or measurable term.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kTYPE_MEASURE', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Enumeration term.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ENUMERATION );
		$term->Code( substr( kTYPE_ENUMERATION, 1 ) );
		$term->Name( 'Enumeration', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents an enumeration term.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kTYPE_ENUMERATION', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
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
		// Get default namespace.
		//
		$ns
			= CPersistentUnitObject::NewObject
				( $theContainer, COntologyTermObject::HashIndex( '' ),
				  kFLAG_STATE_ENCODED );
		if( ! $ns )
			throw new Exception
				( 'Unable to find default namsepace [].' );						// !@! ==>
		
		//
		// Zero or one.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ENUMERATION );
		$term->Code( substr( kCARD_0_1, 1 ) );
		$term->Name( 'Zero or one', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term defines a cardinality of zero or one.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kCARD_0_1', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$ns]\n" );
		
		//
		// One.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ENUMERATION );
		$term->Code( substr( kCARD_1, 1 ) );
		$term->Name( 'One', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term defines a cardinality of exactly one.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kCARD_1', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$ns]\n" );
		
		//
		// Any.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ENUMERATION );
		$term->Code( substr( kCARD_ANY, 1 ) );
		$term->Name( 'Any', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term defines a cardinality of any kind.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kCARD_ANY', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$ns]\n" );
	
	} // LoadCardinalityTypes.

	 
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
		// Get default namespace.
		//
		$ns
			= CPersistentUnitObject::NewObject
				( $theContainer, COntologyTermObject::HashIndex( '' ),
				  kFLAG_STATE_ENCODED );
		if( ! $ns )
			throw new Exception
				( 'Unable to find default namsepace [].' );						// !@! ==>
		
		//
		// Local unique identifier.
		//
		$term = new COntologyTerm();
		$term->Code( kTAG_LID );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Name( 'Local unique identifier', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents the object\'s local unique identifier, this offset should '
		 .'hold a scalar value which uniquely identifies the object within the '
		 .'collection that holds it. This should not be confused with the global '
		 .'identifier, which represents the value or values used by the public to '
		 .'refer to that object. This value should be tightly integrated '
		 .'with the database.',
		  kDEFAULT_LANGUAGE );
		$term->Cardinality( kCARD_1 );
		$term->Synonym( 'kTAG_LID', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Global unique identifier.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kTAG_GID, 1 ) );
		$term->Name( 'Global unique identifier', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents the object\'s global unique identifier, this offset should '
		 .'uniquely identify the object among all containers, it represents a string that '
		 .'may only reference that specific object. This should not be confused with the '
		 .'local identifier, which represents the key to the object within the local '
		 .'database.',
		  kDEFAULT_LANGUAGE );
		$term->Type( kTYPE_STRING );
		$term->Cardinality( kCARD_1 );
		$term->Synonym( 'kTAG_GID', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
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
		// Get default namespace.
		//
		$ns
			= CPersistentUnitObject::NewObject
				( $theContainer, COntologyTermObject::HashIndex( '' ),
				  kFLAG_STATE_ENCODED );
		if( ! $ns )
			throw new Exception
				( 'Unable to find default namsepace [].' );						// !@! ==>
		
		//
		// Synonym.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kTAG_REFERENCE_SYNONYM, 1 ) );
		$term->Name( 'Synonym', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents a synonym. A synonym is a string that can be used as '
		 .'a substitute to the term, it may be of several kinds: exact, broad, '
		 .'narrow and related.',
		  kDEFAULT_LANGUAGE );
		$term->Cardinality( kCARD_ANY );
		$term->Synonym( 'kTAG_REFERENCE_SYNONYM', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Cross-reference.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kTAG_REFERENCE_XREF, 1 ) );
		$term->Name( 'Cross-reference', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents a cross-reference. A cross-reference is a reference to '
		 .'another term in the same container, a sort of synonym, except that it is not '
		 .'a string, but a reference to another term object. Cross-references can be of '
		 .'several kinds: exact, broad, narrow and related.',
		  kDEFAULT_LANGUAGE );
		$term->Cardinality( kCARD_ANY );
		$term->Synonym( 'kTAG_REFERENCE_XREF', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Identifier reference.
		//
		$term = new COntologyTerm();
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( kTAG_REFERENCE_ID );
		$term->Name( 'Identifier reference', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents an object unique identifier within an object reference.',
		  kDEFAULT_LANGUAGE );
		$term->Cardinality( kCARD_1 );
		$term->Synonym( 'kTAG_REFERENCE_ID', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Container reference.
		//
		$term = new COntologyTerm();
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( kTAG_REFERENCE_CONTAINER );
		$term->Name( 'Container reference', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents a container within an object reference.',
		  kDEFAULT_LANGUAGE );
		$term->Cardinality( kCARD_ANY );
		$term->Type( kTYPE_STRING );
		$term->Cardinality( kCARD_0_1 );
		$term->Synonym( 'kTAG_REFERENCE_CONTAINER', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Database reference.
		//
		$term = new COntologyTerm();
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( kTAG_REFERENCE_DATABASE );
		$term->Name( 'Database reference', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents a database within an object reference.',
		  kDEFAULT_LANGUAGE );
		$term->Cardinality( kCARD_0_1 );
		$term->Type( kTYPE_STRING );
		$term->Cardinality( kCARD_0_1 );
		$term->Synonym( 'kTAG_REFERENCE_DATABASE', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
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
		// Get default namespace.
		//
		$ns
			= CPersistentUnitObject::NewObject
				( $theContainer, COntologyTermObject::HashIndex( '' ),
				  kFLAG_STATE_ENCODED );
		if( ! $ns )
			throw new Exception
				( 'Unable to find default namsepace [].' );						// !@! ==>
		
		//
		// Class.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kTAG_CLASS, 1 ) );
		$term->Name( 'Class', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents a class name.',
		  kDEFAULT_LANGUAGE );
		$term->Type( kTYPE_STRING );
		$term->Cardinality( kCARD_1 );
		$term->Synonym( 'kTAG_CLASS', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Created.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kTAG_CREATED, 1 ) );
		$term->Name( 'Created', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents a creation time-stamp.',
		  kDEFAULT_LANGUAGE );
		$term->Type( kTYPE_STAMP );
		$term->Cardinality( kCARD_0_1 );
		$term->Synonym( 'kTAG_CREATED', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Modified.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kTAG_MODIFIED, 1 ) );
		$term->Name( 'Modified', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents a last modification time-stamp.',
		  kDEFAULT_LANGUAGE );
		$term->Type( kTYPE_STAMP );
		$term->Cardinality( kCARD_0_1 );
		$term->Synonym( 'kTAG_MODIFIED', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Version.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kTAG_VERSION, 1 ) );
		$term->Name( 'Version counter', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents a version counter which is automatically incremented '
		 .'each time the object is committed.',
		  kDEFAULT_LANGUAGE );
		$term->Type( kTYPE_INT32 );
		$term->Cardinality( kCARD_1 );
		$term->Synonym( 'kTAG_VERSION', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Version.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kOFFSET_VERSION, 1 ) );
		$term->Name( 'Version', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents a version.',
		  kDEFAULT_LANGUAGE );
		$term->Type( kTYPE_STRING );
		$term->Cardinality( kCARD_0_1 );
		$term->Synonym( 'kOFFSET_VERSION', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Type.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kTAG_TYPE, 1 ) );
		$term->Name( 'Type', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents a type, in general this is used to indicate the data type '
		 .'of an object.',
		  kDEFAULT_LANGUAGE );
		$term->Cardinality( kCARD_0_1 );
		$term->Synonym( 'kTAG_TYPE', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Pattern.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kTAG_PATTERN, 1 ) );
		$term->Name( 'Pattern', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents a pattern attribute, in general this is used to '
		 .'represent the structure, composition and formatting rules of a string '
		 .'data type.',
		  kDEFAULT_LANGUAGE );
		$term->Cardinality( kCARD_ANY );
		$term->Synonym( 'kTAG_PATTERN', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Kind.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kTAG_KIND, 1 ) );
		$term->Name( 'Kind', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents a kind, in general this is used to qualify an object. '
		 .'This should not be confused with the data type.',
		  kDEFAULT_LANGUAGE );
		$term->Cardinality( kCARD_ANY );
		$term->Synonym( 'kTAG_KIND', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Domain.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kTAG_DOMAIN, 1 ) );
		$term->Name( 'Domain', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents a domain attribute, in general this is used to '
		 .'represent the nature of the current object instance.',
		  kDEFAULT_LANGUAGE );
		$term->Cardinality( kCARD_ANY );
		$term->Synonym( 'kTAG_DOMAIN', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Category.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kTAG_CATEGORY, 1 ) );
		$term->Name( 'Category', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents a category attribute, in general this is used to '
		 .'represent the area to which the current object instance belongs to.',
		  kDEFAULT_LANGUAGE );
		$term->Cardinality( kCARD_ANY );
		$term->Synonym( 'kTAG_CATEGORY', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Cardinality.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ENUMERATION, TRUE );
		$term->Code( substr( kTAG_CARDINALITY, 1 ) );
		$term->Name( 'Cardinality', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term indicating the cardinality of a data attribute.',
		  kDEFAULT_LANGUAGE );
		$term->Cardinality( kCARD_0_1 );
		$term->Synonym( 'kTAG_CARDINALITY', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Unit.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kTAG_UNIT, 1 ) );
		$term->Name( 'Unit', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term is used to indicate the unit of a measure.',
		  kDEFAULT_LANGUAGE );
		$term->Cardinality( kCARD_0_1 );
		$term->Synonym( 'kTAG_UNIT', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Source.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kTAG_SOURCE, 1 ) );
		$term->Name( 'Source', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term is used to indicate the source of an object.',
		  kDEFAULT_LANGUAGE );
		$term->Cardinality( kCARD_ANY );
		$term->Synonym( 'kTAG_SOURCE', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Data.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kTAG_DATA, 1 ) );
		$term->Name( 'Data', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term is used to indicate the data part of a structured object.',
		  kDEFAULT_LANGUAGE );
		$term->Cardinality( kCARD_1 );
		$term->Synonym( 'kTAG_DATA', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Code.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kTAG_CODE, 1 ) );
		$term->Name( 'Code', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term is used to indicate a code or acronym.',
		  kDEFAULT_LANGUAGE );
		$term->Cardinality( kCARD_1 );
		$term->Type( kTYPE_STRING );
		$term->Synonym( 'kTAG_CODE', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Enum.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kTAG_ENUM, 1 ) );
		$term->Name( 'Enumeration', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term is used to indicate an enumerated code or key.',
		  kDEFAULT_LANGUAGE );
		$term->Type( kTYPE_STRING );
		$term->Cardinality( kCARD_ANY );
		$term->Synonym( 'kTAG_ENUM', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Namespace.
		//
		$term = new COntologyTerm();
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->NS( $ns );
		$term->Code( substr( kTAG_NAMESPACE, 1 ) );
		$term->Name( 'Namespace term', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term is used to indicate a namespace term reference.',
		  kDEFAULT_LANGUAGE );
		$term->Type( kTYPE_STRING );
		$term->Cardinality( kCARD_1 );
		$term->Synonym( 'kTAG_NAMESPACE', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Namespace.
		//
		$term = new COntologyTerm();
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->NS( $ns );
		$term->Code( substr( kOFFSET_NAMESPACE, 1 ) );
		$term->Name( 'Namespace name', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term is used to indicate a namespace name or acronym.',
		  kDEFAULT_LANGUAGE );
		$term->Type( kTYPE_STRING );
		$term->Cardinality( kCARD_1 );
		$term->Synonym( 'kOFFSET_NAMESPACE', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Image.
		//
		$term = new COntologyTerm();
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->NS( $ns );
		$term->Code( substr( kOFFSET_IMAGE, 1 ) );
		$term->Name( 'Image', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term is used to indicate an image list.',
		  kDEFAULT_LANGUAGE );
		$term->Type( kTYPE_STRING );
		$term->Cardinality( kCARD_1 );
		$term->Synonym( 'kOFFSET_IMAGE', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Node.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kTAG_NODE, 1 ) );
		$term->Name( 'Node', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term is used to indicate a graph node.',
		  kDEFAULT_LANGUAGE );
		$term->Type( kTYPE_INT32 );
		$term->Cardinality( kCARD_ANY );
		$term->Synonym( 'kTAG_NODE', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Predicate.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kTAG_EDGE, 1 ) );
		$term->Name( 'Predicate', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term is used to indicate a predicate node.',
		  kDEFAULT_LANGUAGE );
		$term->Type( kTYPE_INT32 );
		$term->Cardinality( kCARD_ANY );
		$term->Synonym( 'kTAG_EDGE', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Term.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kTAG_TERM, 1 ) );
		$term->Name( 'Term', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term is used to indicate a graph node term.',
		  kDEFAULT_LANGUAGE );
		$term->Type( kTYPE_STRING );
		$term->Cardinality( kCARD_1 );
		$term->Synonym( 'kTAG_TERM', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Name.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kTAG_NAME, 1 ) );
		$term->Name( 'Name', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term is used to indicate a name or label.',
		  kDEFAULT_LANGUAGE );
		$term->Cardinality( kCARD_ANY );
		$term->Synonym( 'kTAG_NAME', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Description.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kTAG_DESCRIPTION, 1 ) );
		$term->Name( 'Description', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term is used to indicate a description or long label.',
		  kDEFAULT_LANGUAGE );
		$term->Cardinality( kCARD_ANY );
		$term->Synonym( 'kTAG_DESCRIPTION', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Definition.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kTAG_DEFINITION, 1 ) );
		$term->Name( 'Definition', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term is used to indicate a definition.',
		  kDEFAULT_LANGUAGE );
		$term->Cardinality( kCARD_ANY );
		$term->Synonym( 'kTAG_DEFINITION', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Examples.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kTAG_EXAMPLES, 1 ) );
		$term->Name( 'Examples', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term is used as the default offset for indicating an attribute '
		 .'containing a list of examples.',
		  kDEFAULT_LANGUAGE );
		$term->Cardinality( kCARD_ANY );
		$term->Synonym( 'kTAG_EXAMPLES', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Language.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kTAG_LANGUAGE, 1 ) );
		$term->Name( 'Language', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term is used to indicate a language.',
		  kDEFAULT_LANGUAGE );
		$term->Cardinality( kCARD_1 );
		$term->Synonym( 'kTAG_LANGUAGE', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Status.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kTAG_STATUS, 1 ) );
		$term->Name( 'Status', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term is used to indicate a state or status.',
		  kDEFAULT_LANGUAGE );
		$term->Cardinality( kCARD_1 );
		$term->Synonym( 'kTAG_STATUS', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Annotation.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kTAG_ANNOTATION, 1 ) );
		$term->Name( 'Annotation', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term is used to indicate an annotation, attachment or comment.',
		  kDEFAULT_LANGUAGE );
		$term->Cardinality( kCARD_ANY );
		$term->Synonym( 'kTAG_ANNOTATION', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// References.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kTAG_REFS, 1 ) );
		$term->Name( 'References', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents the list of references of an object, it describes '
		 .'a list of predicate/object pairs.',
		  kDEFAULT_LANGUAGE );
		$term->Cardinality( kCARD_ANY );
		$term->Synonym( 'kTAG_REFS', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Tags.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kTAG_TAGS, 1 ) );
		$term->Name( 'Tags', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents the list of attribute terms used in the object.',
		  kDEFAULT_LANGUAGE );
		$term->Cardinality( kCARD_ANY );
		$term->Synonym( 'kTAG_TAGS', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Edge terms path.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kTAG_EDGE_TERM, 1 ) );
		$term->Name( 'Edge terms path', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents a graph edge node by using its related terms as a path '
		 .'in the form of a string containing the SUBJECT/PREDICATE/OBJECT path '
		 .'constituted by the term identifier elements.',
		  kDEFAULT_LANGUAGE );
		$term->Type( kTYPE_STRING );
		$term->Cardinality( kCARD_1 );
		$term->Synonym( 'kTAG_EDGE_TERM', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Edge nodes path.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kTAG_EDGE_NODE, 1 ) );
		$term->Name( 'Edge nodes path', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents a graph edge node by using its related nodes and '
		 .'predicate term as a path in the form of a string containing the '
		 .'SUBJECT/PREDICATE/OBJECT</i> path in which the subject and object elements '
		 .'are represented by the respective node identifiers, and the predicate element '
		 .'is represented by the edge term identifier.',
		  kDEFAULT_LANGUAGE );
		$term->Type( kTYPE_STRING );
		$term->Cardinality( kCARD_1 );
		$term->Synonym( 'kTAG_EDGE_NODE', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Preferred.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kTAG_PREFERRED, 1 ) );
		$term->Name( 'Preferred', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents a reference to the preferred object, there are cases '
		 .'in which an object is obsolete, but still in use, in this case this attribute '
		 .'should point to the preferred object.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kTAG_PREFERRED', kTYPE_EXACT, TRUE );
		$term->Cardinality( kCARD_0_1 );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Valid.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kTAG_VALID, 1 ) );
		$term->Name( 'Valid', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents a reference to the valid object, there are cases '
		 .'in which deleting an object is not an option, in such cases the invalid '
		 .'or obsolete object points to the valid object through this term.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kTAG_VALID', kTYPE_EXACT, TRUE );
		$term->Cardinality( kCARD_0_1 );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
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
		// Get default namespace.
		//
		$ns
			= CPersistentUnitObject::NewObject
				( $theContainer, COntologyTermObject::HashIndex( '' ),
				  kFLAG_STATE_ENCODED );
		if( ! $ns )
			throw new Exception
				( 'Unable to find default namsepace [].' );						// !@! ==>
		
		//
		// Password.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kOFFSET_PASSWORD, 1 ) );
		$term->Name( 'Password', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents a password.',
		  kDEFAULT_LANGUAGE );
		$term->Type( kTYPE_STRING );
		$term->Cardinality( kCARD_1 );
		$term->Synonym( 'kOFFSET_PASSWORD', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Address.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kOFFSET_MAIL, 1 ) );
		$term->Name( 'Mail', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents a mailing address.',
		  kDEFAULT_LANGUAGE );
		$term->Cardinality( kCARD_ANY );
		$term->Synonym( 'kOFFSET_MAIL', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Email.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kOFFSET_EMAIL, 1 ) );
		$term->Name( 'E-mail', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents an e-mail address.',
		  kDEFAULT_LANGUAGE );
		$term->Cardinality( kCARD_1 );
		$term->Synonym( 'kOFFSET_EMAIL', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Phone.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Type( kTYPE_STRING );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kOFFSET_PHONE, 1 ) );
		$term->Name( 'Phone', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents a telephone number.',
		  kDEFAULT_LANGUAGE );
		$term->Cardinality( kCARD_ANY );
		$term->Synonym( 'kOFFSET_PHONE', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Fax.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Type( kTYPE_STRING );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kOFFSET_FAX, 1 ) );
		$term->Name( 'Fax', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents a telefax number.',
		  kDEFAULT_LANGUAGE );
		$term->Cardinality( kCARD_ANY );
		$term->Synonym( 'kOFFSET_FAX', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// URL.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Type( kTYPE_STRING );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kOFFSET_URL, 1 ) );
		$term->Name( 'Fax', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents an URL or internet web address.',
		  kDEFAULT_LANGUAGE );
		$term->Cardinality( kCARD_ANY );
		$term->Synonym( 'kOFFSET_URL', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Acronym.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Type( kTYPE_STRING );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kOFFSET_ACRONYM, 1 ) );
		$term->Name( 'Fax', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents an acronym.',
		  kDEFAULT_LANGUAGE );
		$term->Cardinality( kCARD_ANY );
		$term->Synonym( 'kOFFSET_ACRONYM', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
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
		// Get default namespace.
		//
		$ns
			= CPersistentUnitObject::NewObject
				( $theContainer, COntologyTermObject::HashIndex( '' ),
				  kFLAG_STATE_ENCODED );
		if( ! $ns )
			throw new Exception
				( 'Unable to find default namsepace [].' );						// !@! ==>
		
		//
		// Init local storage.
		//
		$len = strlen( (string) $ns ) + 1;
	
		//
		// Place.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Type( kTYPE_STRING );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kOFFSET_PLACE, $len ) );
		$term->Name( 'Place', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents a place or named location part of a mailing address.',
		  kDEFAULT_LANGUAGE );
		$term->Type( kTYPE_STRING );
		$term->Cardinality( kCARD_0_1 );
		$term->Synonym( 'kOFFSET_PLACE', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Care of.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Type( kTYPE_STRING );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kOFFSET_CARE, $len ) );
		$term->Name( 'Care of', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents the care of part of a mailing address.',
		  kDEFAULT_LANGUAGE );
		$term->Type( kTYPE_STRING );
		$term->Cardinality( kCARD_0_1 );
		$term->Synonym( 'kOFFSET_CARE', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Street.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( substr( kOFFSET_STREET, $len ) );
		$term->Name( 'Street/P.O. Box', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents the street or P.O. Box part of a mailing address.',
		  kDEFAULT_LANGUAGE );
		$term->Type( kTYPE_STRING );
		$term->Cardinality( kCARD_1 );
		$term->Synonym( 'kOFFSET_STREET', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Zip.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Type( kTYPE_STRING );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kOFFSET_ZIP_CODE, $len ) );
		$term->Name( 'Zip', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents the zip code part of a mailing address.',
		  kDEFAULT_LANGUAGE );
		$term->Type( kTYPE_STRING );
		$term->Cardinality( kCARD_1 );
		$term->Synonym( 'kOFFSET_ZIP_CODE', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// City.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Type( kTYPE_STRING );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kOFFSET_CITY, $len ) );
		$term->Name( 'City', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents the city part of a mailing address.',
		  kDEFAULT_LANGUAGE );
		$term->Type( kTYPE_STRING );
		$term->Cardinality( kCARD_1 );
		$term->Synonym( 'kOFFSET_CITY', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Province.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Type( kTYPE_STRING );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kOFFSET_PROVINCE, $len ) );
		$term->Name( 'Province', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents the province part of a mailing address.',
		  kDEFAULT_LANGUAGE );
		$term->Type( kTYPE_STRING );
		$term->Cardinality( kCARD_0_1 );
		$term->Synonym( 'kOFFSET_PROVINCE', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Country.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Type( kTYPE_STRING );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kOFFSET_COUNTRY, $len ) );
		$term->Name( 'Country', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents the province part of a mailing address.',
		  kDEFAULT_LANGUAGE );
		$term->Type( kTYPE_STRING );
		$term->Cardinality( kCARD_1 );
		$term->Synonym( 'kOFFSET_COUNTRY', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
		//
		// Full address.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Type( kTYPE_STRING );
		$term->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term->Code( substr( kOFFSET_FULL, $len ) );
		$term->Name( 'Full mailing address', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents a full mailing address in the form of a string.',
		  kDEFAULT_LANGUAGE );
		$term->Type( kTYPE_STRING );
		$term->Cardinality( kCARD_0_1 );
		$term->Synonym( 'kOFFSET_FULL', kTYPE_EXACT, TRUE );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
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
		// Get node term index.
		//
		$term_index = new NodeIndex( $container[ kTAG_NODE ], kINDEX_NODE_TERM );
		$term_index->save();
		$node_index = new RelationshipIndex( $container[ kTAG_NODE ], kINDEX_NODE_TERM );
		$node_index->save();
		
		//
		// Get default namespace.
		//
		$ns
			= CPersistentUnitObject::NewObject
				( $theContainer, COntologyTermObject::HashIndex( '' ),
				  kFLAG_STATE_ENCODED );
		if( ! $ns )
			throw new Exception
				( 'Unable to find default namsepace [].' );						// !@! ==>
		
		//
		// ENUM-OF.
		//
		$enum_of
			= CPersistentUnitObject::NewObject
				( $theContainer, COntologyTermObject::HashIndex( kPRED_ENUM_OF ),
				  kFLAG_STATE_ENCODED );
		if( ! $enum_of )
			throw new Exception
				( 'Unable to find enumeration predicate.' );					// !@! ==>
		
		//
		// Init local storage.
		//
		$len = strlen( (string) $ns ) + 1;
		
		//
		// Domains.
		//
		$root_term = new COntologyTerm();
		$root_term->NS( $ns );
		$root_term->Code( substr( kDEF_DOMAIN, $len ) );
		$root_term->Kind( kTYPE_NAMESPACE, TRUE );
		$root_term->Kind( kTYPE_ROOT, TRUE );
		$root_term->Name( 'Domain', kDEFAULT_LANGUAGE );
		$root_term->Definition
		( 'Default domain.', kDEFAULT_LANGUAGE );
		$root_term->Commit( $theContainer );
		$root_node = $term_index->findOne( kTAG_TERM, (string) $root_term );
		if( $root_node === NULL )
		{
			$root_node = new COntologyNode( $container );
			$root_node->Term( $root_term );
			$root_node->Kind( kTYPE_ROOT, TRUE );
			$root_node->Kind( kTYPE_MEASURE, TRUE );
			$root_node->Commit( $container );
		}
		else
			$root_node = new COntologyNode( $container, $root_node );
		if( $doDisplay )
			echo( $root_term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$root_term] [".$root_node->Node()->getId()."]\n" );
		
		//
		// Init local storage.
		//
		$len = strlen( (string) $root_term ) + 1;
		
		//
		// Germplasm.
		//
		$ger_term = new COntologyTerm();
		$ger_term->NS( $root_term );
		$ger_term->Code( substr( kDOMAIN_GERMPLASM, $len ) );
		$ger_term->Type( kTYPE_ENUM );
		$ger_term->Name( 'Germplasm', kDEFAULT_LANGUAGE );
		$ger_term->Definition
		( 'Germplasm domain, it is a generalised domain comprising all kinds of '
		 .'germplasms.',
		  kDEFAULT_LANGUAGE );
		$ger_term->Commit( $theContainer );
		$ger_node = $term_index->findOne( kTAG_TERM, (string) $ger_term );
		if( $ger_node === NULL )
		{
			$ger_node = new COntologyNode( $container );
			$ger_node->Term( $ger_term );
			$ger_node->Kind( kTYPE_ENUMERATION, TRUE );
			$ger_node->Domain( $root_term, TRUE );
			$ger_node->Commit( $container );
		}
		else
			$ger_node = new COntologyNode( $container, $ger_node );
		$id = Array();
		$id[] = $ger_node->Node()->getId();
		$id[] = (string) $enum_of;
		$id[] = $root_node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $ger_node->RelateTo( $container, $enum_of, $root_node );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $ger_term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$ger_term] [".$root_node->Node()->getId()."]\n" );
		
		//
		// Init local storage.
		//
		$len = strlen( (string) $ger_term ) + 1;
		
		//
		// Sample.
		//
		$term = new COntologyTerm();
		$term->NS( $ger_term );
		$term->Code( substr( kDOMAIN_SAMPLE, $len ) );
		$term->Type( kTYPE_ENUM );
		$term->Name( 'Sample', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'Sample domain, it is a generalised domain comprising all descriptors '
		 .'related to germplasm samples.',
		  kDEFAULT_LANGUAGE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_ENUMERATION, TRUE );
			$node->Domain( $root_term, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $enum_of;
		$id[] = $ger_node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $enum_of, $ger_node );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$root_node->Node()->getId()."]\n" );
		
		//
		// Accession.
		//
		$term = new COntologyTerm();
		$term->NS( $ger_term );
		$term->Code( substr( kDOMAIN_ACCESSION, $len ) );
		$term->Type( kTYPE_ENUM );
		$term->Name( 'Accession', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'Accession domain, it is a generalised domain comprising all descriptors '
		 .'related to germplasm accessions.',
		  kDEFAULT_LANGUAGE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_ENUMERATION, TRUE );
			$node->Domain( $root_term, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $enum_of;
		$id[] = $ger_node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $enum_of, $ger_node );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$root_node->Node()->getId()."]\n" );
		
		//
		// Specimen.
		//
		$term = new COntologyTerm();
		$term->NS( $ger_term );
		$term->Code( substr( kDOMAIN_SPECIMEN, $len ) );
		$term->Type( kTYPE_ENUM );
		$term->Name( 'Specimen', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'Specimen domain, it is a generalised domain comprising all descriptors '
		 .'related to germplasm specimens; in general these will not be living material.',
		  kDEFAULT_LANGUAGE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_ENUMERATION, TRUE );
			$node->Domain( $root_term, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $enum_of;
		$id[] = $ger_node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $enum_of, $ger_node );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$root_node->Node()->getId()."]\n" );
		
		//
		// Land-race.
		//
		$term = new COntologyTerm();
		$term->NS( $ger_term );
		$term->Code( substr( kDOMAIN_LANDRACE, $len ) );
		$term->Type( kTYPE_ENUM );
		$term->Name( 'Land-race', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'Landrace domain, it is a generalised domain comprising all descriptors '
		 .'related to farmer varieties.',
		  kDEFAULT_LANGUAGE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_ENUMERATION, TRUE );
			$node->Domain( $root_term, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $enum_of;
		$id[] = $ger_node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $enum_of, $ger_node );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$root_node->Node()->getId()."]\n" );
		
		//
		// Population.
		//
		$term = new COntologyTerm();
		$term->NS( $ger_term );
		$term->Code( substr( kDOMAIN_POPULATION, $len ) );
		$term->Type( kTYPE_ENUM );
		$term->Name( 'Population', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'Population domain, it is a generalised domain comprising all descriptors '
		 .'related to in-situ germplasm populations.',
		  kDEFAULT_LANGUAGE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_ENUMERATION, TRUE );
			$node->Domain( $root_term, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $enum_of;
		$id[] = $ger_node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $enum_of, $ger_node );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$root_node->Node()->getId()."]\n" );
		
		//
		// Init local storage.
		//
		$len = strlen( (string) $root_term ) + 1;
		
		//
		// Geography.
		//
		$term = new COntologyTerm();
		$term->NS( $root_term );
		$term->Code( substr( kDOMAIN_GEOGRAPHY, $len ) );
		$term->Type( kTYPE_ENUM );
		$term->Name( 'Geography', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'Geography domain, it is a generalised domain comprising all descriptors '
		 .'related to geographic data.',
		  kDEFAULT_LANGUAGE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_ENUMERATION, TRUE );
			$node->Domain( $root_term, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $enum_of;
		$id[] = $root_node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $enum_of, $root_node );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$root_node->Node()->getId()."]\n" );
		
		//
		// Language.
		//
		$term = new COntologyTerm();
		$term->NS( $root_term );
		$term->Code( substr( kDOMAIN_LANGUAGE, $len ) );
		$term->Type( kTYPE_ENUM );
		$term->Name( 'Language', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'Language domain, it is a generalised domain comprising all descriptors '
		 .'related to languages.',
		  kDEFAULT_LANGUAGE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_ENUMERATION, TRUE );
			$node->Domain( $root_term, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $enum_of;
		$id[] = $root_node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $enum_of, $root_node );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$root_node->Node()->getId()."]\n" );
		
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
		// Get node term index.
		//
		$term_index = new NodeIndex( $container[ kTAG_NODE ], kINDEX_NODE_TERM );
		$term_index->save();
		$node_index = new RelationshipIndex( $container[ kTAG_NODE ], kINDEX_NODE_TERM );
		$node_index->save();
		
		//
		// Get default namespace.
		//
		$ns
			= CPersistentUnitObject::NewObject
				( $theContainer, COntologyTermObject::HashIndex( '' ),
				  kFLAG_STATE_ENCODED );
		if( ! $ns )
			throw new Exception
				( 'Unable to find default namsepace [].' );						// !@! ==>
		
		//
		// ENUM-OF.
		//
		$enum_of
			= CPersistentUnitObject::NewObject
				( $theContainer, COntologyTermObject::HashIndex( kPRED_ENUM_OF ),
				  kFLAG_STATE_ENCODED );
		if( ! $enum_of )
			throw new Exception
				( 'Unable to find enumeration predicate.' );					// !@! ==>
		
		//
		// Init local storage.
		//
		$len = strlen( (string) $ns ) + 1;
		
		//
		// Categories.
		//
		$root_term = new COntologyTerm();
		$root_term->NS( $ns );
		$root_term->Code( substr( kDEF_CATEGORY, $len ) );
		$root_term->Kind( kTYPE_NAMESPACE, TRUE );
		$root_term->Kind( kTYPE_ROOT, TRUE );
		$root_term->Name( 'Categories', kDEFAULT_LANGUAGE );
		$root_term->Definition
		( 'Default categories.', kDEFAULT_LANGUAGE );
		$root_term->Commit( $theContainer );
		$root_node = $term_index->findOne( kTAG_TERM, (string) $root_term );
		if( $root_node === NULL )
		{
			$root_node = new COntologyNode( $container );
			$root_node->Term( $root_term );
			$root_node->Kind( kTYPE_ROOT, TRUE );
			$root_node->Kind( kTYPE_MEASURE, TRUE );
			$root_node->Commit( $container );
		}
		else
			$root_node = new COntologyNode( $container, $root_node );
		if( $doDisplay )
			echo( $root_term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$root_term] [".$root_node->Node()->getId()."]\n" );
		
		//
		// Init local storage.
		//
		$len = strlen( (string) $root_term ) + 1;
		
		//
		// Passport.
		//
		$term = new COntologyTerm();
		$term->NS( $root_term );
		$term->Code( substr( kCATEGORY_PASSPORT, $len ) );
		$term->Type( kTYPE_ENUM );
		$term->Name( 'Passport', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'Passport category, it is a generalised category comprising all descriptors '
		 .'related to germplasm passport datasets.',
		  kDEFAULT_LANGUAGE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_ENUMERATION, TRUE );
			$node->Domain( $root_term, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $enum_of;
		$id[] = $root_node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $enum_of, $root_node );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$root_node->Node()->getId()."]\n" );
		
		//
		// Characterisation.
		//
		$term = new COntologyTerm();
		$term->NS( $root_term );
		$term->Code( substr( kCATEGORY_CHAR, $len ) );
		$term->Type( kTYPE_ENUM );
		$term->Name( 'Characterisation', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'Characterisation category, it is a generalised category comprising all '
		 .'descriptors related to germplasm characterisation datasets.',
		  kDEFAULT_LANGUAGE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_ENUMERATION, TRUE );
			$node->Domain( $root_term, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $enum_of;
		$id[] = $root_node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $enum_of, $root_node );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$root_node->Node()->getId()."]\n" );
		
		//
		// Evaluation.
		//
		$term = new COntologyTerm();
		$term->NS( $root_term );
		$term->Code( substr( kCATEGORY_EVAL, $len ) );
		$term->Type( kTYPE_ENUM );
		$term->Name( 'Evaluation', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'Evaluation category, it is a generalised category comprising all '
		 .'descriptors related to germplasm evaluation trial datasets.',
		  kDEFAULT_LANGUAGE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_ENUMERATION, TRUE );
			$node->Domain( $root_term, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $enum_of;
		$id[] = $root_node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $enum_of, $root_node );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$root_node->Node()->getId()."]\n" );
		
		//
		// Administrative units.
		//
		$term = new COntologyTerm();
		$term->NS( $root_term );
		$term->Code( substr( kCATEGORY_ADMIN, $len ) );
		$term->Type( kTYPE_ENUM );
		$term->Name( 'Administrative units', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'Administrative units category, it is a generalised category comprising all '
		 .'descriptors related to administrative units.',
		  kDEFAULT_LANGUAGE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_ENUMERATION, TRUE );
			$node->Domain( $root_term, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $enum_of;
		$id[] = $root_node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $enum_of, $root_node );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$root_node->Node()->getId()."]\n" );
		
		//
		// Geographic units.
		//
		$term = new COntologyTerm();
		$term->NS( $root_term );
		$term->Code( substr( kCATEGORY_GEO, $len ) );
		$term->Type( kTYPE_ENUM );
		$term->Name( 'Geographic units', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'Geographic units category, it is a generalised category comprising all '
		 .'descriptors related to geographic units.',
		  kDEFAULT_LANGUAGE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_ENUMERATION, TRUE );
			$node->Domain( $root_term, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $enum_of;
		$id[] = $root_node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $enum_of, $root_node );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$root_node->Node()->getId()."]\n" );
		
	} // LoadDefaultCategories.

	 
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
		$_SESSION[ 'REGIONS' ] = Array();
		
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
		// Get node term index.
		//
		$term_index = new NodeIndex( $container[ kTAG_NODE ], kINDEX_NODE_TERM );
		$term_index->save();
		$node_index = new RelationshipIndex( $container[ kTAG_NODE ], kINDEX_NODE_TERM );
		$node_index->save();
		
		//
		// IS-A.
		//
		$is_a
			= CPersistentUnitObject::NewObject
				( $theContainer, COntologyTermObject::HashIndex( kPRED_IS_A ),
				  kFLAG_STATE_ENCODED );
		if( ! $is_a )
			throw new Exception
				( 'Unable to find subclass of predicate.' );					// !@! ==>
		
		//
		// ENUM-OF.
		//
		$enum_of
			= CPersistentUnitObject::NewObject
				( $theContainer, COntologyTermObject::HashIndex( kPRED_ENUM_OF ),
				  kFLAG_STATE_ENCODED );
		if( ! $enum_of )
			throw new Exception
				( 'Unable to find enumeration predicate.' );					// !@! ==>
		
		//
		// United Nations Statistics Division.
		//
		$ns = new COntologyTerm();
		$ns->Code( 'UNSTATS' );
        $ns->Name( 'UN Statistics Division', kDEFAULT_LANGUAGE );
		$ns->Definition
		( 'United Nations Statistics Division.', kDEFAULT_LANGUAGE );
		$ns->Kind( kTYPE_NAMESPACE, TRUE );
		$ns->Kind( kTYPE_ROOT, TRUE );
		$ns->Commit( $theContainer );
		$root = $term_index->findOne( kTAG_TERM, (string) $ns );
		if( $root === NULL )
		{
			$root = new COntologyNode( $container );
			$root->Term( $ns );
			$root->Kind( kTYPE_ROOT, TRUE );
			$root->Commit( $container );
		}
		else
			$root = new COntologyNode( $container, $root );
		$namespace = $ns[ kTAG_GID ].kTOKEN_NAMESPACE_SEPARATOR;
		if( $doDisplay )
			echo( $ns->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$ns] [".$root->Node()->getId()."]\n" );
		
		//
		// UNSTATS - Region codes.
		//
		$region_term = new COntologyTerm();
		$region_term->NS( $ns );
		$region_term->Code( 'REGIONS' );
		$region_term->Name( 'Region codes', kDEFAULT_LANGUAGE );
		$region_term->Definition
		( 'United Nations Statistics Division composition of macro geographical '
		 .'(continental) regions, geographical sub-regions, and selected economic '
		 .'and other groupings.', kDEFAULT_LANGUAGE );
		$region_term->Type( kTYPE_ENUM );
		$region_term->Pattern( '[0-9]{3}', TRUE );
		$region_term->Domain( kDOMAIN_GEOGRAPHY, TRUE );
		$region_term->Category( kCATEGORY_ADMIN, TRUE );
		$region_term->Relate( $ns, $is_a, TRUE );
		$region_term->Commit( $theContainer );
		$region_node = $term_index->findOne( kTAG_TERM, (string) $region_term );
		if( $region_node === NULL )
		{
			$region_node = new COntologyNode( $container );
			$region_node->Term( $region_term );
			$region_node->Type( kTYPE_ENUM, TRUE );
			$region_node->Kind( kTYPE_MEASURE, TRUE );
			$region_node->Domain( kDOMAIN_GEOGRAPHY, TRUE );
			$region_node->Category( kCATEGORY_ADMIN, TRUE );
			$region_node->Commit( $container );
		}
		else
			$region_node = new COntologyNode( $container, $region_node );
		$id = Array();
		$id[] = $region_node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $root->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $region_node->RelateTo( $container, $is_a, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $region_term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$region_term] [".$root->Node()->getId()."]\n" );
		
		//
		// Load region terms.
		//
		$query = <<<EOT
SELECT
	`Code_ISO_3166_Regions`.`Code`,
	`Code_ISO_3166_Regions`.`Parent`,
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
			$term = new COntologyTerm();
			$term->NS( $ns );
			$term->Code( $record[ 'Code' ] );
			$term->Name( $record[ 'Name' ], kDEFAULT_LANGUAGE );
			$term->Kind( kTYPE_ENUMERATION, TRUE );
			$term->Type( kTYPE_ENUM );
			$term->Commit( $theContainer );
			
			//
			// Display.
			//
			if( $doDisplay )
				echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
					 ." [$term]\n" );
		
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
				= new COntologyTerm
					( $theContainer,
					  COntologyTerm::HashIndex( $namespace.$record[ 'Code' ] ) );
			if( $record[ 'Parent' ] !== NULL )
				$parent_term
					= new COntologyTerm
						( $theContainer,
						  COntologyTerm::HashIndex( $namespace.$record[ 'Parent' ] ) );
			else
				$parent_term = $region_term;
			
			//
			// Relate child term to parent term.
			//
			$child_term->Relate( $parent_term, $enum_of, TRUE );
			$child_term->Commit( $theContainer );
			
			//
			// Get/create parent node.
			//
			if( $record[ 'Parent' ] !== NULL )
			{
				$parent_node = $term_index->findOne( kTAG_TERM, (string) $parent_term );
				if( $parent_node === NULL )
				{
					$parent_node = new COntologyNode( $container );
					$parent_node->Term( $parent_term );
					$parent_node->Kind( kTYPE_ENUMERATION, TRUE );
					$parent_node->Commit( $container );
				}
				else
					$parent_node = new COntologyNode( $container, $parent_node );
				
				$_SESSION[ 'REGIONS' ][ $parent_term->Code() ]
					= $parent_node->Node()->getId();
			}
			else
				$parent_node = $region_node;
			
			//
			// Get/create child node.
			//
			$child_node = $term_index->findOne( kTAG_TERM, (string) $child_term );
			if( $child_node === NULL )
			{
				$child_node = new COntologyNode( $container );
				$child_node->Term( $child_term );
				$child_node->Kind( kTYPE_ENUMERATION, TRUE );
				$child_node->Commit( $container );
			}
			else
				$child_node = new COntologyNode( $container, $child_node );
			
			$_SESSION[ 'REGIONS' ][ $child_term->Code() ]
				= $child_node->Node()->getId();
			
			//
			// Create child/parent edge.
			//
			$id = Array();
			$id[] = $child_node->Node()->getId();
			$id[] = (string) $enum_of;
			$id[] = $parent_node->Node()->getId();
			$id = implode( '/', $id );
			$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
			if( $edge === NULL )
			{
				$edge = $child_node->RelateTo( $container, $enum_of, $parent_node );
				$edge->Commit( $container );
			}
			
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
		// Get node term index.
		//
		$term_index = new NodeIndex( $container[ kTAG_NODE ], kINDEX_NODE_TERM );
		$term_index->save();
		$node_index = new RelationshipIndex( $container[ kTAG_NODE ], kINDEX_NODE_TERM );
		$node_index->save();
		
		//
		// IS-A.
		//
		$is_a
			= CPersistentUnitObject::NewObject
				( $theContainer, COntologyTermObject::HashIndex( kPRED_IS_A ),
				  kFLAG_STATE_ENCODED );
		if( ! $is_a )
			throw new Exception
				( 'Unable to find subclass of predicate.' );					// !@! ==>
		
		//
		// ENUM-OF.
		//
		$enum_of
			= CPersistentUnitObject::NewObject
				( $theContainer, COntologyTermObject::HashIndex( kPRED_ENUM_OF ),
				  kFLAG_STATE_ENCODED );
		if( ! $enum_of )
			throw new Exception
				( 'Unable to find enumeration predicate.' );					// !@! ==>
		
		//
		// PART-OF.
		//
		$part_of
			= CPersistentUnitObject::NewObject
				( $theContainer, COntologyTermObject::HashIndex( kPRED_PART_OF ),
				  kFLAG_STATE_ENCODED );
		if( ! $enum_of )
			throw new Exception
				( 'Unable to find part-of predicate.' );						// !@! ==>
		
		//
		// ISO.
		//
		$iso_term = new COntologyTerm();
		$iso_term->Code( 'ISO' );
        $iso_term->Name( 'International Organization for Standardization', kDEFAULT_LANGUAGE );
        $iso_term->Name( 'Organisation internationale de normalisation', 'fr' );
        $iso_term->Name( 'Международная организация по стандартизации', 'ru' );
		$iso_term->Definition
		( 'Collection of industrial and commercial standards and codes.',
		  kDEFAULT_LANGUAGE );
		$iso_term->Kind( kTYPE_NAMESPACE, TRUE );
		$iso_term->Kind( kTYPE_ROOT, TRUE );
		$iso_term->Domain( kDOMAIN_LANGUAGE, TRUE );
		$iso_term->Domain( kDOMAIN_GEOGRAPHY, TRUE );
		$iso_term->Commit( $theContainer );
		$iso_node = $term_index->findOne( kTAG_TERM, (string) $iso_term );
		if( $iso_node === NULL )
		{
			$iso_node = new COntologyNode( $container );
			$iso_node->Term( $iso_term );
			$iso_node->Kind( kTYPE_ROOT, TRUE );
			$iso_node->Commit( $container );
		}
		else
			$iso_node = new COntologyNode( $container, $iso_node );
		if( $doDisplay )
			echo( $iso_term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$iso_term] [".$iso_node->Node()->getId()."]\n" );
		
		//
		// ISO 639.
		//
		$ns_639 = new COntologyTerm();
		$ns_639->NS( $iso_term );
		$ns_639->Code( '639' );
        $ns_639->Name( 'ISO 639',
        			   kDEFAULT_LANGUAGE );
		$ns_639->Definition
		( 'ISO 639 is a set of standards by the International Organization for '
		 .'Standardization that is concerned with representation of names for '
		 .'language and language groups.',
		  kDEFAULT_LANGUAGE );
		$ns_639->Kind( kTYPE_NAMESPACE, TRUE );
		$ns_639->Domain( kDOMAIN_LANGUAGE, TRUE );
		$ns_639->Commit( $theContainer );
		$root_639 = $term_index->findOne( kTAG_TERM, (string) $ns_639 );
		if( $root_639 === NULL )
		{
			$root_639 = new COntologyNode( $container );
			$root_639->Term( $ns_639 );
			$root_639->Domain( kDOMAIN_LANGUAGE, TRUE );
			$root_639->Commit( $container );
		}
		else
			$root_639 = new COntologyNode( $container, $root_639 );
		$id = Array();
		$id[] = $root_639->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $iso_node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $root_639->RelateTo( $container, $is_a, $iso_node );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $ns_639->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$ns_639] [".$root_639->Node()->getId()."]\n" );
		
		//
		// Print name.
		//
		$term_print_name = new COntologyTerm();
		$term_print_name->NS( $ns_639 );
		$term_print_name->Code( 'PrintName' );
        $term_print_name->Name
        ( 'Name associated with the language',
          kDEFAULT_LANGUAGE );
        $term_print_name->Definition
        ( 'One of the names associated with the language',
		  kDEFAULT_LANGUAGE );
		$term_print_name->Type( kTYPE_STRING );
		$term_print_name->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term_print_name->Cardinality( kCARD_0_1 );
		$term_print_name->Domain( kDOMAIN_LANGUAGE, TRUE );
		$term_print_name->Commit( $theContainer );
		if( $doDisplay )
			echo( $term_print_name->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term_print_name]\n" );
		
		//
		// Inverted name.
		//
		$term_inverted_name = new COntologyTerm();
		$term_inverted_name->NS( $ns_639 );
		$term_inverted_name->Code( 'InvertedName' );
        $term_inverted_name->Name
        ( 'Name associated with the language',
          kDEFAULT_LANGUAGE );
        $term_inverted_name->Definition
        ( 'One of the names associated with the language',
		  kDEFAULT_LANGUAGE );
		$term_inverted_name->Kind( kTYPE_ATTRIBUTE, TRUE );
		$term_inverted_name->Cardinality( kCARD_0_1 );
		$term_inverted_name->Domain( kDOMAIN_LANGUAGE, TRUE );
		$term_inverted_name->Commit( $theContainer );
		$root_inverted_name = $term_index->findOne( kTAG_TERM, (string) $term_inverted_name );
		if( $doDisplay )
			echo( $term_inverted_name->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term_inverted_name]\n" );
		
		//
		// ISO 639-3.
		//
		$ns_639_3 = new COntologyTerm();
		$ns_639_3->NS( $ns_639 );
		$ns_639_3->Code( '3' );
        $ns_639_3->Name( 'Part 3 alpha-3 codes',
        			   kDEFAULT_LANGUAGE );
		$ns_639_3->Definition
		( 'The standard describes three‐letter codes for identifying languages. '
		 .'It extends the ISO 639-2 alpha-3 codes with an aim to cover all known '
		 .'natural languages. The standard was published by ISO on 2007-02-05.',
		  kDEFAULT_LANGUAGE );
		$ns_639_3->Kind( kTYPE_NAMESPACE, TRUE );
		$ns_639_3->Domain( kDOMAIN_LANGUAGE, TRUE );
		$ns_639_3->Commit( $theContainer );
		$root_639_3 = $term_index->findOne( kTAG_TERM, (string) $ns_639_3 );
		if( $root_639_3 === NULL )
		{
			$root_639_3 = new COntologyNode( $container );
			$root_639_3->Term( $ns_639_3 );
			$root_639_3->Domain( kDOMAIN_LANGUAGE, TRUE );
			$root_639_3->Commit( $container );
		}
		else
			$root_639_3 = new COntologyNode( $container, $root_639_3 );
		$id = Array();
		$id[] = $root_639_3->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $root_639->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $root_639_3->RelateTo( $container, $is_a, $root_639 );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $ns_639_3->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$ns_639_3] [".$root_639_3->Node()->getId()."]\n" );
		
		//
		// ISO 639-3 - Code3 codes.
		//
		$code3_term = new COntologyTerm();
		$code3_term->NS( $ns_639_3 );
		$code3_term->Code( 'Part3' );
		$code3_term->Name( '3-letter ISO 639-3 identifier', kDEFAULT_LANGUAGE );
		$code3_term->Definition
		( 'ISO 639-3 3 character code identifying a language.', kDEFAULT_LANGUAGE );
		$code3_term->Type( kTYPE_ENUM );
		$code3_term->Pattern( '[A-Z]{3}', TRUE );
		$code3_term->Domain( kDOMAIN_LANGUAGE, TRUE );
		$code3_term->Relate( $ns_639_3, $is_a, TRUE );
		$code3_term->Commit( $theContainer );
		$code3_node = $term_index->findOne( kTAG_TERM, (string) $code3_term );
		if( $code3_node === NULL )
		{
			$code3_node = new COntologyNode( $container );
			$code3_node->Term( $code3_term );
			$code3_node->Type( kTYPE_ENUM, TRUE );
			$code3_node->Kind( kTYPE_MEASURE, TRUE );
			$code3_node->Domain( kDOMAIN_LANGUAGE, TRUE );
			$code3_node->Commit( $container );
		}
		else
			$code3_node = new COntologyNode( $container, $code3_node );
		$id = Array();
		$id[] = $code3_node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $root_639_3->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $code3_node->RelateTo( $container, $is_a, $root_639_3 );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $code3_term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$code3_term] [".$code3_node->Node()->getId()."]\n" );
		
		//
		// ISO 639-3 - Part2B.
		//
		$part2b_term = new COntologyTerm();
		$part2b_term->NS( $ns_639_3 );
		$part2b_term->Code( 'Part2B' );
		$part2b_term->Name( 'Bibliographic applications 639-2 identifier',
							kDEFAULT_LANGUAGE );
		$part2b_term->Definition
		( 'ISO 639-2 identifier of the bibliographic applications code set.',
		  kDEFAULT_LANGUAGE );
		$part2b_term->Type( kTYPE_ENUM );
		$part2b_term->Pattern( '[0-9]{3}', TRUE );
		$part2b_term->Domain( kDOMAIN_LANGUAGE, TRUE );
		$part2b_term->Relate( $ns_639_3, $is_a, TRUE );
		$part2b_term->Commit( $theContainer );
		$part2b_node = $term_index->findOne( kTAG_TERM, (string) $part2b_term );
		if( $part2b_node === NULL )
		{
			$part2b_node = new COntologyNode( $container );
			$part2b_node->Term( $part2b_term );
			$part2b_node->Type( kTYPE_ENUM, TRUE );
			$part2b_node->Kind( kTYPE_MEASURE, TRUE );
			$part2b_node->Domain( kDOMAIN_LANGUAGE, TRUE );
			$part2b_node->Commit( $container );
		}
		else
			$part2b_node = new COntologyNode( $container, $part2b_node );
		$id = Array();
		$id[] = $part2b_node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $root_639_3->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $part2b_node->RelateTo( $container, $is_a, $root_639_3 );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $part2b_term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$part2b_term] [".$part2b_node->Node()->getId()."]\n" );
		
		//
		// ISO 639-3 - Part2T.
		//
		$part2t_term = new COntologyTerm();
		$part2t_term->NS( $ns_639_3 );
		$part2t_term->Code( 'Part2T' );
		$part2t_term->Name( 'Terminology applications 639-2 identifier',
							kDEFAULT_LANGUAGE );
		$part2t_term->Definition
		( 'ISO 639-2 identifier of the terminology applications code set.',
		  kDEFAULT_LANGUAGE );
		$part2t_term->Type( kTYPE_ENUM );
		$part2t_term->Pattern( '[0-9]{3}', TRUE );
		$part2t_term->Domain( kDOMAIN_LANGUAGE, TRUE );
		$part2t_term->Relate( $ns_639_3, $is_a, TRUE );
		$part2t_term->Commit( $theContainer );
		$part2t_node = $term_index->findOne( kTAG_TERM, (string) $part2t_term );
		if( $part2t_node === NULL )
		{
			$part2t_node = new COntologyNode( $container );
			$part2t_node->Term( $part2t_term );
			$part2t_node->Type( kTYPE_ENUM, TRUE );
			$part2t_node->Kind( kTYPE_MEASURE, TRUE );
			$part2t_node->Domain( kDOMAIN_LANGUAGE, TRUE );
			$part2t_node->Commit( $container );
		}
		else
			$part2t_node = new COntologyNode( $container, $part2t_node );
		$id = Array();
		$id[] = $part2t_node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $root_639_3->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $part2t_node->RelateTo( $container, $is_a, $root_639_3 );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $part2t_term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$part2t_term] [".$part2t_node->Node()->getId()."]\n" );
		
		//
		// ISO 639-3 - Part1.
		//
		$part1_term = new COntologyTerm();
		$part1_term->NS( $ns_639_3 );
		$part1_term->Code( 'Part1' );
		$part1_term->Name( '639-1 identifier',
							kDEFAULT_LANGUAGE );
		$part1_term->Definition
		( 'ISO 639-1 identifier.',
		  kDEFAULT_LANGUAGE );
		$part1_term->Type( kTYPE_ENUM );
		$part1_term->Pattern( '[0-9]{2}', TRUE );
		$part1_term->Domain( kDOMAIN_LANGUAGE, TRUE );
		$part1_term->Relate( $ns_639_3, $is_a, TRUE );
		$part1_term->Commit( $theContainer );
		$part1_node = $term_index->findOne( kTAG_TERM, (string) $part1_term );
		if( $part1_node === NULL )
		{
			$part1_node = new COntologyNode( $container );
			$part1_node->Term( $part1_term );
			$part1_node->Type( kTYPE_ENUM, TRUE );
			$part1_node->Kind( kTYPE_MEASURE, TRUE );
			$part1_node->Domain( kDOMAIN_LANGUAGE, TRUE );
			$part1_node->Commit( $container );
		}
		else
			$part1_node = new COntologyNode( $container, $part1_node );
		$id = Array();
		$id[] = $part1_node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $root_639_3->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $part1_node->RelateTo( $container, $is_a, $root_639_3 );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $part1_term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$part1_term] [".$part1_node->Node()->getId()."]\n" );

		//
		// ISO 639-3 - Scope.
		//
		$scope_term = new COntologyTerm();
		$scope_term->NS( $ns_639_3 );
		$scope_term->Code( 'Scope' );
		$scope_term->Name( 'Language scope', kDEFAULT_LANGUAGE );
		$scope_term->Definition( 'ISO 639-3 language scope.', kDEFAULT_LANGUAGE );
		$scope_term->Type( kTYPE_ENUM );
		$scope_term->Domain( kDOMAIN_LANGUAGE, TRUE );
		$scope_term->Relate( $ns_639_3, $is_a, TRUE );
		$scope_term->Commit( $theContainer );
		$scope_node = $term_index->findOne( kTAG_TERM, (string) $scope_term );
		if( $scope_node === NULL )
		{
			$scope_node = new COntologyNode( $container );
			$scope_node->Term( $scope_term );
			$scope_node->Type( kTYPE_ENUM, TRUE );
			$scope_node->Kind( kTYPE_MEASURE, TRUE );
			$scope_node->Domain( kDOMAIN_LANGUAGE, TRUE );
			$scope_node->Commit( $container );
		}
		else
			$scope_node = new COntologyNode( $container, $scope_node );
		$id = Array();
		$id[] = $scope_node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $root_639_3->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $scope_node->RelateTo( $container, $enum_of, $root_639_3 );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $scope_term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$scope_term] [".$scope_node->Node()->getId()."]\n" );
		
		//
		// ISO 639-3 - Scope - Individual
		//
		$individual_scope_term = new COntologyTerm();
		$individual_scope_term->NS( $scope_term );
		$individual_scope_term->Code( 'I' );
		$individual_scope_term->Name( 'Individual', kDEFAULT_LANGUAGE );
		$individual_scope_term->Definition( 'Individual language scope.', kDEFAULT_LANGUAGE );
		$individual_scope_term->Kind( kTYPE_ENUMERATION, TRUE );
		$individual_scope_term->Type( kTYPE_ENUM );
		$individual_scope_term->Enumeration( $individual_scope_term->Code(), TRUE );
		$individual_scope_term->Relate( $scope_term, $is_a, TRUE );
		$individual_scope_term->Commit( $theContainer );
		$individual_scope_node = $term_index->findOne( kTAG_TERM,
													   (string) $individual_scope_term );
		if( $individual_scope_node === NULL )
		{
			$individual_scope_node = new COntologyNode( $container );
			$individual_scope_node->Term( $individual_scope_term );
			$individual_scope_node->Type( kTYPE_ENUM, TRUE );
			$individual_scope_node->Kind( kTYPE_ENUMERATION, TRUE );
			$individual_scope_node->Domain( kDOMAIN_LANGUAGE, TRUE );
			$individual_scope_node->Commit( $container );
		}
		else
			$individual_scope_node = new COntologyNode( $container, $individual_scope_node );
		$id = Array();
		$id[] = $individual_scope_node->Node()->getId();
		$id[] = (string) $enum_of;
		$id[] = $scope_node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $individual_scope_node->RelateTo( $container, $enum_of, $scope_node );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $individual_scope_term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$individual_scope_term] ["
				 .$individual_scope_node->Node()->getId()."]\n" );
		
		//
		// ISO 639-3 - Scope - Macrolanguage
		//
		$macrolanguage_scope_term = new COntologyTerm();
		$macrolanguage_scope_term->NS( $scope_term );
		$macrolanguage_scope_term->Code( 'M' );
		$macrolanguage_scope_term->Name( 'Macrolanguage', kDEFAULT_LANGUAGE );
		$macrolanguage_scope_term->Definition( 'Macro-language scope.', kDEFAULT_LANGUAGE );
		$macrolanguage_scope_term->Kind( kTYPE_PREDICATE, TRUE );
		$macrolanguage_scope_term->Kind( kTYPE_ENUMERATION, TRUE );
		$macrolanguage_scope_term->Type( kTYPE_ENUM );
		$macrolanguage_scope_term->Enumeration( $macrolanguage_scope_term->Code(), TRUE );
		$macrolanguage_scope_term->Relate( $scope_term, $is_a, TRUE );
		$macrolanguage_scope_term->Commit( $theContainer );
		$macrolanguage_scope_node
			= $term_index->findOne( kTAG_TERM, (string) $macrolanguage_scope_term );
		if( $macrolanguage_scope_node === NULL )
		{
			$macrolanguage_scope_node = new COntologyNode( $container );
			$macrolanguage_scope_node->Term( $macrolanguage_scope_term );
			$macrolanguage_scope_node->Type( kTYPE_ENUM, TRUE );
			$macrolanguage_scope_node->Kind( kTYPE_PREDICATE, TRUE );
			$macrolanguage_scope_node->Kind( kTYPE_ENUMERATION, TRUE );
			$macrolanguage_scope_node->Domain( kDOMAIN_LANGUAGE, TRUE );
			$macrolanguage_scope_node->Commit( $container );
		}
		else
			$macrolanguage_scope_node
				= new COntologyNode( $container, $macrolanguage_scope_node );
		$id = Array();
		$id[] = $macrolanguage_scope_node->Node()->getId();
		$id[] = (string) $enum_of;
		$id[] = $scope_node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $macrolanguage_scope_node->RelateTo( $container, $enum_of, $scope_node );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $macrolanguage_scope_term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$macrolanguage_scope_term] ["
				 .$macrolanguage_scope_node->Node()->getId()."]\n" );
		
		//
		// ISO 639-3 - Scope - Special
		//
		$special_scope_term = new COntologyTerm();
		$special_scope_term->NS( $scope_term );
		$special_scope_term->Code( 'S' );
		$special_scope_term->Name( 'Special', kDEFAULT_LANGUAGE );
		$special_scope_term->Definition( 'Special language scope.', kDEFAULT_LANGUAGE );
		$special_scope_term->Kind( kTYPE_ENUMERATION, TRUE );
		$special_scope_term->Type( kTYPE_ENUM );
		$special_scope_term->Enumeration( $special_scope_term->Code(), TRUE );
		$special_scope_term->Relate( $scope_term, $is_a, TRUE );
		$special_scope_term->Commit( $theContainer );
		$special_scope_node
			= $term_index->findOne( kTAG_TERM, (string) $special_scope_term );
		if( $special_scope_node === NULL )
		{
			$special_scope_node = new COntologyNode( $container );
			$special_scope_node->Term( $special_scope_term );
			$special_scope_node->Type( kTYPE_ENUM, TRUE );
			$special_scope_node->Kind( kTYPE_ENUMERATION, TRUE );
			$special_scope_node->Domain( kDOMAIN_LANGUAGE, TRUE );
			$special_scope_node->Commit( $container );
		}
		else
			$special_scope_node = new COntologyNode( $container, $special_scope_node );
		$id = Array();
		$id[] = $special_scope_node->Node()->getId();
		$id[] = (string) $enum_of;
		$id[] = $scope_node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $special_scope_node->RelateTo( $container, $enum_of, $scope_node );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $special_scope_term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$special_scope_term] ["
				 .$special_scope_node->Node()->getId()."]\n" );

		//
		// ISO 639-3 - Type.
		//
		$type_term = new COntologyTerm();
		$type_term->NS( $ns_639_3 );
		$type_term->Code( 'Type' );
		$type_term->Name( 'Language type',
							kDEFAULT_LANGUAGE );
		$type_term->Definition
		( 'ISO 639-3 language type.',
		  kDEFAULT_LANGUAGE );
		$type_term->Type( kTYPE_ENUM );
		$type_term->Domain( kDOMAIN_LANGUAGE, TRUE );
		$type_term->Relate( $ns_639_3, $is_a, TRUE );
		$type_term->Commit( $theContainer );
		$type_node = $term_index->findOne( kTAG_TERM, (string) $type_term );
		if( $type_node === NULL )
		{
			$type_node = new COntologyNode( $container );
			$type_node->Term( $type_term );
			$type_node->Type( kTYPE_ENUM, TRUE );
			$type_node->Kind( kTYPE_MEASURE, TRUE );
			$type_node->Domain( kDOMAIN_LANGUAGE, TRUE );
			$type_node->Commit( $container );
		}
		else
			$type_node = new COntologyNode( $container, $type_node );
		$id = Array();
		$id[] = $type_node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $root_639_3->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $type_node->RelateTo( $container, $is_a, $root_639_3 );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $type_term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$type_term] [".$type_node->Node()->getId()."]\n" );
		
		//
		// ISO 639-3 - Type - Ancient
		//
		$ancient_type_term = new COntologyTerm();
		$ancient_type_term->NS( $type_term );
		$ancient_type_term->Code( 'A' );
		$ancient_type_term->Name( 'Ancient', kDEFAULT_LANGUAGE );
		$ancient_type_term->Definition( 'Ancient language type.', kDEFAULT_LANGUAGE );
		$ancient_type_term->Kind( kTYPE_ENUMERATION, TRUE );
		$ancient_type_term->Type( kTYPE_ENUM );
		$ancient_type_term->Enumeration( $ancient_type_term->Code(), TRUE );
		$ancient_type_term->Relate( $type_term, $is_a, TRUE );
		$ancient_type_term->Commit( $theContainer );
		$ancient_type_node
			= $term_index->findOne( kTAG_TERM, (string) $ancient_type_term );
		if( $ancient_type_node === NULL )
		{
			$ancient_type_node = new COntologyNode( $container );
			$ancient_type_node->Term( $ancient_type_term );
			$ancient_type_node->Type( kTYPE_ENUM, TRUE );
			$ancient_type_node->Kind( kTYPE_ENUMERATION, TRUE );
			$ancient_type_node->Domain( kDOMAIN_LANGUAGE, TRUE );
			$ancient_type_node->Commit( $container );
		}
		else
			$ancient_type_node = new COntologyNode( $container, $ancient_type_node );
		$id = Array();
		$id[] = $ancient_type_node->Node()->getId();
		$id[] = (string) $enum_of;
		$id[] = $type_node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $ancient_type_node->RelateTo( $container, $enum_of, $type_node );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $ancient_type_term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$ancient_type_term] ["
				 .$ancient_type_node->Node()->getId()."]\n" );
		
		//
		// ISO 639-3 - Type - Constructed
		//
		$constructed_type_term = new COntologyTerm();
		$constructed_type_term->NS( $type_term );
		$constructed_type_term->Code( 'C' );
		$constructed_type_term->Name( 'Constructed', kDEFAULT_LANGUAGE );
		$constructed_type_term->Definition( 'Constructed language type.', kDEFAULT_LANGUAGE );
		$constructed_type_term->Kind( kTYPE_ENUMERATION, TRUE );
		$constructed_type_term->Type( kTYPE_ENUM );
		$constructed_type_term->Enumeration( $constructed_type_term->Code(), TRUE );
		$constructed_type_term->Relate( $type_term, $is_a, TRUE );
		$constructed_type_term->Commit( $theContainer );
		$constructed_type_node
			= $term_index->findOne( kTAG_TERM, (string) $constructed_type_term );
		if( $constructed_type_node === NULL )
		{
			$constructed_type_node = new COntologyNode( $container );
			$constructed_type_node->Term( $constructed_type_term );
			$constructed_type_node->Type( kTYPE_ENUM, TRUE );
			$constructed_type_node->Kind( kTYPE_ENUMERATION, TRUE );
			$constructed_type_node->Domain( kDOMAIN_LANGUAGE, TRUE );
			$constructed_type_node->Commit( $container );
		}
		else
			$constructed_type_node
				= new COntologyNode( $container, $constructed_type_node );
		$id = Array();
		$id[] = $constructed_type_node->Node()->getId();
		$id[] = (string) $enum_of;
		$id[] = $type_node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $constructed_type_node->RelateTo( $container, $enum_of, $type_node );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $constructed_type_term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$constructed_type_term] ["
				 .$constructed_type_node->Node()->getId()."]\n" );
		
		//
		// ISO 639-3 - Type - Extinct
		//
		$extinct_type_term = new COntologyTerm();
		$extinct_type_term->NS( $type_term );
		$extinct_type_term->Code( 'E' );
		$extinct_type_term->Name( 'Extinct', kDEFAULT_LANGUAGE );
		$extinct_type_term->Definition( 'Extinct language type.', kDEFAULT_LANGUAGE );
		$extinct_type_term->Kind( kTYPE_ENUMERATION, TRUE );
		$extinct_type_term->Type( kTYPE_ENUM );
		$extinct_type_term->Enumeration( $extinct_type_term->Code(), TRUE );
		$extinct_type_term->Relate( $type_term, $is_a, TRUE );
		$extinct_type_term->Commit( $theContainer );
		$extinct_type_node
			= $term_index->findOne( kTAG_TERM, (string) $extinct_type_term );
		if( $extinct_type_node === NULL )
		{
			$extinct_type_node = new COntologyNode( $container );
			$extinct_type_node->Term( $extinct_type_term );
			$extinct_type_node->Type( kTYPE_ENUM, TRUE );
			$extinct_type_node->Kind( kTYPE_ENUMERATION, TRUE );
			$extinct_type_node->Domain( kDOMAIN_LANGUAGE, TRUE );
			$extinct_type_node->Commit( $container );
		}
		else
			$extinct_type_node = new COntologyNode( $container, $extinct_type_node );
		$id = Array();
		$id[] = $extinct_type_node->Node()->getId();
		$id[] = (string) $enum_of;
		$id[] = $type_node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $extinct_type_node->RelateTo( $container, $enum_of, $type_node );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $extinct_type_term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$extinct_type_term] ["
				 .$extinct_type_node->Node()->getId()."]\n" );
		
		//
		// ISO 639-3 - Type - Historical
		//
		$historical_type_term = new COntologyTerm();
		$historical_type_term->NS( $type_term );
		$historical_type_term->Code( 'H' );
		$historical_type_term->Name( 'Historical', kDEFAULT_LANGUAGE );
		$historical_type_term->Definition( 'Historical language type.', kDEFAULT_LANGUAGE );
		$historical_type_term->Kind( kTYPE_ENUMERATION, TRUE );
		$historical_type_term->Type( kTYPE_ENUM );
		$historical_type_term->Enumeration( $historical_type_term->Code(), TRUE );
		$historical_type_term->Relate( $type_term, $is_a, TRUE );
		$historical_type_term->Commit( $theContainer );
		$historical_type_node
			= $term_index->findOne( kTAG_TERM, (string) $historical_type_term );
		if( $historical_type_node === NULL )
		{
			$historical_type_node = new COntologyNode( $container );
			$historical_type_node->Term( $historical_type_term );
			$historical_type_node->Type( kTYPE_ENUM, TRUE );
			$historical_type_node->Kind( kTYPE_ENUMERATION, TRUE );
			$historical_type_node->Domain( kDOMAIN_LANGUAGE, TRUE );
			$historical_type_node->Commit( $container );
		}
		else
			$historical_type_node = new COntologyNode( $container, $historical_type_node );
		$id = Array();
		$id[] = $historical_type_node->Node()->getId();
		$id[] = (string) $enum_of;
		$id[] = $type_node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $historical_type_node->RelateTo( $container, $enum_of, $type_node );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $historical_type_term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$historical_type_term] ["
				 .$historical_type_node->Node()->getId()."]\n" );
		
		//
		// ISO 639-3 - Type - Living
		//
		$living_type_term = new COntologyTerm();
		$living_type_term->NS( $type_term );
		$living_type_term->Code( 'L' );
		$living_type_term->Name( 'Living', kDEFAULT_LANGUAGE );
		$living_type_term->Definition( 'Living language type.', kDEFAULT_LANGUAGE );
		$living_type_term->Kind( kTYPE_ENUMERATION, TRUE );
		$living_type_term->Type( kTYPE_ENUM );
		$living_type_term->Enumeration( $living_type_term->Code(), TRUE );
		$living_type_term->Relate( $type_term, $is_a, TRUE );
		$living_type_term->Commit( $theContainer );
		$living_type_node
			= $term_index->findOne( kTAG_TERM, (string) $living_type_term );
		if( $living_type_node === NULL )
		{
			$living_type_node = new COntologyNode( $container );
			$living_type_node->Term( $living_type_term );
			$living_type_node->Type( kTYPE_ENUM, TRUE );
			$living_type_node->Kind( kTYPE_ENUMERATION, TRUE );
			$living_type_node->Domain( kDOMAIN_LANGUAGE, TRUE );
			$living_type_node->Commit( $container );
		}
		else
			$living_type_node = new COntologyNode( $container, $living_type_node );
		$id = Array();
		$id[] = $living_type_node->Node()->getId();
		$id[] = (string) $enum_of;
		$id[] = $type_node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $living_type_node->RelateTo( $container, $enum_of, $type_node );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $living_type_term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$living_type_term] ["
				 .$living_type_node->Node()->getId()."]\n" );
		
		//
		// ISO 639-3 - Type - Special
		//
		$special_type_term = new COntologyTerm();
		$special_type_term->NS( $type_term );
		$special_type_term->Code( 'S' );
		$special_type_term->Name( 'Special', kDEFAULT_LANGUAGE );
		$special_type_term->Definition( 'Special language type.', kDEFAULT_LANGUAGE );
		$special_type_term->Kind( kTYPE_ENUMERATION, TRUE );
		$special_type_term->Type( kTYPE_ENUM );
		$special_type_term->Enumeration( $special_type_term->Code(), TRUE );
		$special_type_term->Relate( $type_term, $is_a, TRUE );
		$special_type_term->Commit( $theContainer );
		$special_type_node
			= $term_index->findOne( kTAG_TERM, (string) $special_type_term );
		if( $special_type_node === NULL )
		{
			$special_type_node = new COntologyNode( $container );
			$special_type_node->Term( $special_type_term );
			$special_type_node->Type( kTYPE_ENUM, TRUE );
			$special_type_node->Kind( kTYPE_ENUMERATION, TRUE );
			$special_type_node->Domain( kDOMAIN_LANGUAGE, TRUE );
			$special_type_node->Commit( $container );
		}
		else
			$special_type_node = new COntologyNode( $container, $special_type_node );
		$id = Array();
		$id[] = $special_type_node->Node()->getId();
		$id[] = (string) $enum_of;
		$id[] = $type_node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $special_type_node->RelateTo( $container, $enum_of, $type_node );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $special_type_term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$special_type_term] ["
				 .$special_type_node->Node()->getId()."]\n" );
		
		//
		// ISO 3166-1.
		//
		$ns_3166 = new COntologyTerm();
		$ns_3166->NS( $iso_term );
		$ns_3166->Code( '3166-1' );
        $ns_3166->Name( 'Country code', kDEFAULT_LANGUAGE );
		$ns_3166->Definition
		( 'Codes for the representation of names of countries and their '
		 .'subdivisions – Part 1: Country codes.', kDEFAULT_LANGUAGE );
		$ns_3166->Kind( kTYPE_NAMESPACE, TRUE );
		$ns_3166->Domain( kDOMAIN_GEOGRAPHY, TRUE );
		$ns_3166->Category( kCATEGORY_ADMIN, TRUE );
		$ns_3166->Commit( $theContainer );
		$root_3166 = $term_index->findOne( kTAG_TERM, (string) $ns_3166 );
		if( $root_3166 === NULL )
		{
			$root_3166 = new COntologyNode( $container );
			$root_3166->Term( $ns_3166 );
			$root_3166->Domain( kDOMAIN_GEOGRAPHY, TRUE );
			$root_3166->Category( kCATEGORY_ADMIN, TRUE );
			$root_3166->Commit( $container );
		}
		else
			$root_3166 = new COntologyNode( $container, $root_3166 );
		$id = Array();
		$id[] = $root_3166->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $iso_node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $root_3166->RelateTo( $container, $is_a, $iso_node );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $ns_3166->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$ns_3166] [".$root_3166->Node()->getId()."]\n" );
		
		//
		// ISO 3166-1 - Numeric 3 codes.
		//
		$numeric3_term = new COntologyTerm();
		$numeric3_term->NS( $ns_3166 );
		$numeric3_term->Code( 'NUMERIC-3' );
		$numeric3_term->Name( '3-digit country code', kDEFAULT_LANGUAGE );
		$numeric3_term->Definition
		( 'ISO 3166-1 numeric (or numeric-3) codes are three-digit country codes '
		 .'defined in ISO 3166-1, part of the ISO 3166 standard published by the '
		 .'International Organization for Standardization (ISO), to represent countries, '
		 .'dependent territories, and special areas of '
		 .'geographical interest.', kDEFAULT_LANGUAGE );
		$numeric3_term->Type( kTYPE_ENUM );
		$numeric3_term->Pattern( '[0-9]{3}', TRUE );
		$numeric3_term->Domain( kDOMAIN_GEOGRAPHY, TRUE );
		$numeric3_term->Category( kCATEGORY_ADMIN, TRUE );
		$numeric3_term->Relate( $ns_3166, $is_a, TRUE );
		$numeric3_term->Commit( $theContainer );
		$numeric3_node = $term_index->findOne( kTAG_TERM, (string) $numeric3_term );
		if( $numeric3_node === NULL )
		{
			$numeric3_node = new COntologyNode( $container );
			$numeric3_node->Term( $numeric3_term );
			$numeric3_node->Type( kTYPE_ENUM, TRUE );
			$numeric3_node->Kind( kTYPE_MEASURE, TRUE );
			$numeric3_node->Domain( kDOMAIN_GEOGRAPHY, TRUE );
			$numeric3_node->Category( kCATEGORY_ADMIN, TRUE );
			$numeric3_node->Commit( $container );
		}
		else
			$numeric3_node = new COntologyNode( $container, $numeric3_node );
		$id = Array();
		$id[] = $numeric3_node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $root_3166->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $numeric3_node->RelateTo( $container, $is_a, $root_3166 );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $numeric3_term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$numeric3_term] [".$numeric3_node->Node()->getId()."]\n" );
		
		//
		// ISO 3166-1 - Alpha 2 codes.
		//
		$alpha2_term = new COntologyTerm();
		$alpha2_term->NS( $ns_3166 );
		$alpha2_term->Code( 'ALPHA-2' );
		$alpha2_term->Name( '2-character country code', kDEFAULT_LANGUAGE );
		$alpha2_term->Definition
		( 'ISO 3166-1 alpha-2 codes are two-letter country codes defined in '
		 .'ISO 3166-1, part of the ISO 3166 standard published by the International '
		 .'Organization for Standardization (ISO), to represent countries, '
		 .'dependent territories, and special areas of '
		 .'geographical interest.', kDEFAULT_LANGUAGE );
		$alpha2_term->Type( kTYPE_ENUM );
		$alpha2_term->Pattern( '[A-Z]{2}', TRUE );
		$alpha2_term->Domain( kDOMAIN_GEOGRAPHY, TRUE );
		$alpha2_term->Category( kCATEGORY_ADMIN, TRUE );
		$alpha2_term->Relate( $ns_3166, $is_a, TRUE );
		$alpha2_term->Commit( $theContainer );
		$alpha2_node = $term_index->findOne( kTAG_TERM, (string) $alpha2_term );
		if( $alpha2_node === NULL )
		{
			$alpha2_node = new COntologyNode( $container );
			$alpha2_node->Term( $alpha2_term );
			$alpha2_node->Type( kTYPE_ENUM, TRUE );
			$alpha2_node->Kind( kTYPE_MEASURE, TRUE );
			$alpha2_node->Domain( kDOMAIN_GEOGRAPHY, TRUE );
			$alpha2_node->Category( kCATEGORY_ADMIN, TRUE );
			$alpha2_node->Commit( $container );
		}
		else
			$alpha2_node = new COntologyNode( $container, $alpha2_node );
		$id = Array();
		$id[] = $alpha2_node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $root_3166->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $alpha2_node->RelateTo( $container, $is_a, $root_3166 );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $alpha2_term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$alpha2_term] [".$alpha2_node->Node()->getId()."]\n" );
		
		//
		// ISO 3166-1 - Alpha 3 codes.
		//
		$alpha3_term = new COntologyTerm();
		$alpha3_term->NS( $ns_3166 );
		$alpha3_term->Code( 'ALPHA-3' );
		$alpha3_term->Name( '3-character country code', kDEFAULT_LANGUAGE );
		$alpha3_term->Definition
		( 'ISO 3166-1 alpha-3 codes are three-letter country codes defined in '
		 .'ISO 3166-1, part of the ISO 3166 standard published by the International '
		 .'Organization for Standardization (ISO), to represent countries, '
		 .'dependent territories, and special areas of '
		 .'geographical interest.', kDEFAULT_LANGUAGE );
		$alpha3_term->Type( kTYPE_ENUM );
		$alpha3_term->Pattern( '[A-Z]{3}', TRUE );
		$alpha3_term->Domain( kDOMAIN_GEOGRAPHY, TRUE );
		$alpha3_term->Category( kCATEGORY_ADMIN, TRUE );
		$alpha3_term->Relate( $ns_3166, $is_a, TRUE );
		$alpha3_term->Commit( $theContainer );
		$alpha3_node = $term_index->findOne( kTAG_TERM, (string) $alpha3_term );
		if( $alpha3_node === NULL )
		{
			$alpha3_node = new COntologyNode( $container );
			$alpha3_node->Term( $alpha3_term );
			$alpha3_node->Type( kTYPE_ENUM, TRUE );
			$alpha3_node->Kind( kTYPE_MEASURE, TRUE );
			$alpha3_node->Domain( kDOMAIN_GEOGRAPHY, TRUE );
			$alpha3_node->Category( kCATEGORY_ADMIN, TRUE );
			$alpha3_node->Commit( $container );
		}
		else
			$alpha3_node = new COntologyNode( $container, $alpha3_node );
		$id = Array();
		$id[] = $alpha3_node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $root_3166->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $alpha3_node->RelateTo( $container, $is_a, $root_3166 );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $alpha3_term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$alpha3_term] [".$alpha3_node->Node()->getId()."]\n" );

		//
		// Load language codes.
		//
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
			$term = new COntologyTerm();
			$term->NS( $code3_term );
			$term->Code( $record[ 'Code3' ] );
			$term->Name( $record[ 'ReferenceName' ] );
			if( $record[ 'PrintName' ] !== NULL )
				$term[ $term_print_name->GID() ] = $record[ 'PrintName' ];
			if( $record[ 'InvertedName' ] !== NULL )
				$term[ $term_inverted_name->GID() ] = $record[ 'InvertedName' ];
			if( $record[ 'Comment' ] !== NULL )
				$term->Description( $record[ 'Comment' ], kDEFAULT_LANGUAGE );
			$term->Kind( kTYPE_ENUMERATION, TRUE );
			$term->Type( kTYPE_ENUM );
			$term->Enumeration( $term->Code(), TRUE );
			if( $record[ 'Part2B' ] !== NULL )
			{
				$term->Enumeration( $record[ 'Part2B' ], TRUE );
				$term->Synonym( $record[ 'Part2B' ], kTYPE_EXACT, TRUE );
			}
			if( $record[ 'Part2T' ] !== NULL )
			{
				$term->Enumeration( $record[ 'Part2T' ], TRUE );
				$term->Synonym( $record[ 'Part2T' ], kTYPE_EXACT, TRUE );
			}
			if( $record[ 'Part1' ] !== NULL )
			{
				$term->Enumeration( $record[ 'Part1' ], TRUE );
				$term->Synonym( $record[ 'Part1' ], kTYPE_EXACT, TRUE );
			}
			switch( $record[ 'Scope' ] )
			{
				case 'I':
					$term->Domain( $individual_scope_term->GID(), TRUE );
					break;
				case 'M':
					$term->Domain( $macrolanguage_scope_term->GID(), TRUE );
					break;
				case 'S':
					$term->Domain( $special_scope_term->GID(), TRUE );
					break;
			}
			switch( $record[ 'Type' ] )
			{
				case 'A':
					$term->Category( $ancient_type_term->GID(), TRUE );
					break;
				case 'C':
					$term->Category( $constructed_type_term->GID(), TRUE );
					break;
				case 'E':
					$term->Category( $extinct_type_term->GID(), TRUE );
					break;
				case 'H':
					$term->Category( $historical_type_term->GID(), TRUE );
					break;
				case 'L':
					$term->Category( $living_type_term->GID(), TRUE );
					break;
				case 'S':
					$term->Category( $special_type_term->GID(), TRUE );
					break;
			}
			$term->Relate( $code3_term, $is_a, TRUE );
			$term->Commit( $theContainer );
			
			//
			// Create Code3 node.
			//
			$node = $term_index->findOne( kTAG_TERM, (string) $term );
			if( $node === NULL )
			{
				$node = new COntologyNode( $container );
				$node->Term( $term );
				$node->Kind( kTYPE_ENUMERATION, TRUE );
				$node->Commit( $container );
			}
			else
				$node = new COntologyNode( $container, $node );
			
			//
			// Create Code3 edge.
			//
			$id = Array();
			$id[] = $node->Node()->getId();
			$id[] = (string) $enum_of;
			$id[] = $code3_node->Node()->getId();
			$id = implode( '/', $id );
			$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
			if( $edge === NULL )
			{
				$edge = $node->RelateTo( $container, $enum_of, $code3_node );
				$edge->Commit( $container );
			}
			
			//
			// Display.
			//
			if( $doDisplay )
				echo( $term->Name()
					 ." [$term] [".$node->Node()->getId()."]\n" );
		
			//
			// Handle Part2B code.
			//
			if( $record[ 'Part2B' ] !== NULL )
			{
				//
				// Create Code3 term.
				//
				$term = new COntologyTerm();
				$term->NS( $part2b_term );
				$term->Code( $record[ 'Part2B' ] );
				$term->Name( $record[ 'ReferenceName' ] );
				if( $record[ 'PrintName' ] !== NULL )
					$term[ $term_print_name->GID() ] = $record[ 'PrintName' ];
				if( $record[ 'InvertedName' ] !== NULL )
					$term[ $term_inverted_name->GID() ] = $record[ 'InvertedName' ];
				if( $record[ 'Comment' ] !== NULL )
					$term->Description( $record[ 'Comment' ], kDEFAULT_LANGUAGE );
				$term->Kind( kTYPE_ENUMERATION, TRUE );
				$term->Type( kTYPE_ENUM );
				$term->Enumeration( $term->Code(), TRUE );
				$term->Enumeration( $record[ 'Code3' ], TRUE );
				if( $record[ 'Part2T' ] !== NULL )
				{
					$term->Enumeration( $record[ 'Part2T' ], TRUE );
					$term->Synonym( $record[ 'Part2T' ], kTYPE_EXACT, TRUE );
				}
				if( $record[ 'Part1' ] !== NULL )
				{
					$term->Enumeration( $record[ 'Part1' ], TRUE );
					$term->Synonym( $record[ 'Part1' ], kTYPE_EXACT, TRUE );
				}
				switch( $record[ 'Scope' ] )
				{
					case 'I':
						$term->Domain( $individual_scope_term->GID(), TRUE );
						break;
					case 'M':
						$term->Domain( $macrolanguage_scope_term->GID(), TRUE );
						break;
					case 'S':
						$term->Domain( $special_scope_term->GID(), TRUE );
						break;
				}
				switch( $record[ 'Type' ] )
				{
					case 'A':
						$term->Category( $ancient_type_term->GID(), TRUE );
						break;
					case 'C':
						$term->Category( $constructed_type_term->GID(), TRUE );
						break;
					case 'E':
						$term->Category( $extinct_type_term->GID(), TRUE );
						break;
					case 'H':
						$term->Category( $historical_type_term->GID(), TRUE );
						break;
					case 'L':
						$term->Category( $living_type_term->GID(), TRUE );
						break;
					case 'S':
						$term->Category( $special_type_term->GID(), TRUE );
						break;
				}
				$term->Relate( $part2b_term, $is_a, TRUE );
				$term->Commit( $theContainer );
				
				//
				// Create Part2B node.
				//
				$node = $term_index->findOne( kTAG_TERM, (string) $term );
				if( $node === NULL )
				{
					$node = new COntologyNode( $container );
					$node->Term( $term );
					$node->Kind( kTYPE_ENUMERATION, TRUE );
					$node->Commit( $container );
				}
				else
					$node = new COntologyNode( $container, $node );
				
				//
				// Create Part2B edge.
				//
				$id = Array();
				$id[] = $node->Node()->getId();
				$id[] = (string) $enum_of;
				$id[] = $part2b_node->Node()->getId();
				$id = implode( '/', $id );
				$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
				if( $edge === NULL )
				{
					$edge = $node->RelateTo( $container, $enum_of, $part2b_node );
					$edge->Commit( $container );
				}
				
				//
				// Display.
				//
				if( $doDisplay )
					echo( $term->Name()
						 ." [$term] [".$node->Node()->getId()."]\n" );
			}
		
			//
			// Handle Part2T code.
			//
			if( $record[ 'Part2T' ] !== NULL )
			{
				//
				// Create Code3 term.
				//
				$term = new COntologyTerm();
				$term->NS( $part2t_term );
				$term->Code( $record[ 'Part2T' ] );
				$term->Name( $record[ 'ReferenceName' ] );
				if( $record[ 'PrintName' ] !== NULL )
					$term[ $term_print_name->GID() ] = $record[ 'PrintName' ];
				if( $record[ 'InvertedName' ] !== NULL )
					$term[ $term_inverted_name->GID() ] = $record[ 'InvertedName' ];
				if( $record[ 'Comment' ] !== NULL )
					$term->Description( $record[ 'Comment' ], kDEFAULT_LANGUAGE );
				$term->Kind( kTYPE_ENUMERATION, TRUE );
				$term->Type( kTYPE_ENUM );
				$term->Enumeration( $term->Code(), TRUE );
				$term->Enumeration( $record[ 'Code3' ], TRUE );
				if( $record[ 'Part2B' ] !== NULL )
				{
					$term->Enumeration( $record[ 'Part2B' ], TRUE );
					$term->Synonym( $record[ 'Part2B' ], kTYPE_EXACT, TRUE );
				}
				if( $record[ 'Part1' ] !== NULL )
				{
					$term->Enumeration( $record[ 'Part1' ], TRUE );
					$term->Synonym( $record[ 'Part1' ], kTYPE_EXACT, TRUE );
				}
				switch( $record[ 'Scope' ] )
				{
					case 'I':
						$term->Domain( $individual_scope_term->GID(), TRUE );
						break;
					case 'M':
						$term->Domain( $macrolanguage_scope_term->GID(), TRUE );
						break;
					case 'S':
						$term->Domain( $special_scope_term->GID(), TRUE );
						break;
				}
				switch( $record[ 'Type' ] )
				{
					case 'A':
						$term->Category( $ancient_type_term->GID(), TRUE );
						break;
					case 'C':
						$term->Category( $constructed_type_term->GID(), TRUE );
						break;
					case 'E':
						$term->Category( $extinct_type_term->GID(), TRUE );
						break;
					case 'H':
						$term->Category( $historical_type_term->GID(), TRUE );
						break;
					case 'L':
						$term->Category( $living_type_term->GID(), TRUE );
						break;
					case 'S':
						$term->Category( $special_type_term->GID(), TRUE );
						break;
				}
				$term->Relate( $part2t_term, $is_a, TRUE );
				$term->Commit( $theContainer );
				
				//
				// Create Part2T node.
				//
				$node = $term_index->findOne( kTAG_TERM, (string) $term );
				if( $node === NULL )
				{
					$node = new COntologyNode( $container );
					$node->Term( $term );
					$node->Kind( kTYPE_ENUMERATION, TRUE );
					$node->Commit( $container );
				}
				else
					$node = new COntologyNode( $container, $node );
				
				//
				// Create Part2T edge.
				//
				$id = Array();
				$id[] = $node->Node()->getId();
				$id[] = (string) $enum_of;
				$id[] = $part2t_node->Node()->getId();
				$id = implode( '/', $id );
				$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
				if( $edge === NULL )
				{
					$edge = $node->RelateTo( $container, $enum_of, $part2t_node );
					$edge->Commit( $container );
				}
				
				//
				// Display.
				//
				if( $doDisplay )
					echo( $term->Name()
						 ." [$term] [".$node->Node()->getId()."]\n" );
			}
		
			//
			// Handle Part1 code.
			//
			if( $record[ 'Part1' ] !== NULL )
			{
				//
				// Create Code3 term.
				//
				$term = new COntologyTerm();
				$term->NS( $part1_term );
				$term->Code( $record[ 'Part1' ] );
				$term->Name( $record[ 'ReferenceName' ] );
				if( $record[ 'PrintName' ] !== NULL )
					$term[ $term_print_name->GID() ] = $record[ 'PrintName' ];
				if( $record[ 'InvertedName' ] !== NULL )
					$term[ $term_inverted_name->GID() ] = $record[ 'InvertedName' ];
				if( $record[ 'Comment' ] !== NULL )
					$term->Description( $record[ 'Comment' ], kDEFAULT_LANGUAGE );
				$term->Kind( kTYPE_ENUMERATION, TRUE );
				$term->Type( kTYPE_ENUM );
				$term->Enumeration( $term->Code(), TRUE );
				$term->Enumeration( $record[ 'Code3' ], TRUE );
				if( $record[ 'Part2B' ] !== NULL )
				{
					$term->Enumeration( $record[ 'Part2B' ], TRUE );
					$term->Synonym( $record[ 'Part2B' ], kTYPE_EXACT, TRUE );
				}
				if( $record[ 'Part2T' ] !== NULL )
				{
					$term->Enumeration( $record[ 'Part2T' ], TRUE );
					$term->Synonym( $record[ 'Part2T' ], kTYPE_EXACT, TRUE );
				}
				switch( $record[ 'Scope' ] )
				{
					case 'I':
						$term->Domain( $individual_scope_term->GID(), TRUE );
						break;
					case 'M':
						$term->Domain( $macrolanguage_scope_term->GID(), TRUE );
						break;
					case 'S':
						$term->Domain( $special_scope_term->GID(), TRUE );
						break;
				}
				switch( $record[ 'Type' ] )
				{
					case 'A':
						$term->Category( $ancient_type_term->GID(), TRUE );
						break;
					case 'C':
						$term->Category( $constructed_type_term->GID(), TRUE );
						break;
					case 'E':
						$term->Category( $extinct_type_term->GID(), TRUE );
						break;
					case 'H':
						$term->Category( $historical_type_term->GID(), TRUE );
						break;
					case 'L':
						$term->Category( $living_type_term->GID(), TRUE );
						break;
					case 'S':
						$term->Category( $special_type_term->GID(), TRUE );
						break;
				}
				$term->Relate( $part1_term, $is_a, TRUE );
				$term->Commit( $theContainer );
				
				//
				// Create Part1 node.
				//
				$node = $term_index->findOne( kTAG_TERM, (string) $term );
				if( $node === NULL )
				{
					$node = new COntologyNode( $container );
					$node->Term( $term );
					$node->Kind( kTYPE_ENUMERATION, TRUE );
					$node->Commit( $container );
				}
				else
					$node = new COntologyNode( $container, $node );
				
				//
				// Create Part1 edge.
				//
				$id = Array();
				$id[] = $node->Node()->getId();
				$id[] = (string) $enum_of;
				$id[] = $part1_node->Node()->getId();
				$id = implode( '/', $id );
				$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
				if( $edge === NULL )
				{
					$edge = $node->RelateTo( $container, $enum_of, $part1_node );
					$edge->Commit( $container );
				}
				
				//
				// Display.
				//
				if( $doDisplay )
					echo( $term->Name()
						 ." [$term] [".$node->Node()->getId()."]\n" );
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
	`Code_ISO_639_3_Macrolanguages`.`Language`
EOT;
		$rs = $mysql->Execute( $query );
		foreach( $rs as $record )
		{
			//
			// Locate language node.
			//
			$id = $code3_term->GID().kTOKEN_NAMESPACE_SEPARATOR.$record[ 'Language' ];
			$language_node = $term_index->findOne( kTAG_TERM, $id );
			if( $language_node === NULL )
				throw new Exception( "Unable to find [$id] node." );			// !@! ==>
			$language_node = new COntologyNode( $container, $language_node );
			
			//
			// Locate macrolanguage node.
			//
			$id = $code3_term->GID().kTOKEN_NAMESPACE_SEPARATOR.$record[ 'MacroLanguage' ];
			$macro_node = $term_index->findOne( kTAG_TERM, $id );
			if( $macro_node === NULL )
				throw new Exception( "Unable to find [$id] node." );			// !@! ==>
			$macro_node = new COntologyNode( $container, $macro_node );
			
			//
			// Create Macrolanguage edge.
			//
			$id = Array();
			$id[] = $macro_node->Node()->getId();
			$id[] = $macrolanguage_scope_term;
			$id[] = $language_node->Node()->getId();
			$id = implode( '/', $id );
			$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
			if( $edge === NULL )
			{
				$edge = $macro_node->RelateTo( $container,
											   $macrolanguage_scope_term,
											   $language_node );
				$edge->Commit( $container );
			}
			
			//
			// Display.
			//
			if( $doDisplay )
				echo( $macro_node->Term()->Name()
					 .' => '
					 .$language_node->Term()->Name()
					 .' ['
					 .$node->Node()->getId()
					 ."]\n" );
		
		} $rs->Close();
				
		//
		// Iterate country codes.
		//
		$query = <<<EOT
SELECT
	`Code_ISO_3166`.`ISO3`,
	`Code_ISO_3166`.`CodeNum`,
	`Code_ISO_3166`.`Code2`,
	`VALID_COUNTRY`.`ISO3` AS `ValidCode`,
	`Code_ISO_3166`.`Region`,
	`Code_ISO_3166`.`Name`,
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
			//
			$term = new COntologyTerm();
			$term->NS( $ns_3166 );
			$term->Code( $record[ 'ISO3' ] );
			$term->Name( $record[ 'Name' ], kDEFAULT_LANGUAGE );
			$term->Kind( kTYPE_ENUMERATION, TRUE );
			$term->Type( kTYPE_ENUM );
			$term->Enumeration( $term->Code(), TRUE );
			if( $record[ 'CodeNum' ] !== NULL )
			{
				$term->Enumeration( $record[ 'CodeNum' ], TRUE );
				$term->Synonym( $record[ 'CodeNum' ], kTYPE_EXACT, TRUE );
			}
			if( $record[ 'Code2' ] !== NULL )
			{
				$term->Enumeration( $record[ 'Code2' ], TRUE );
				$term->Synonym( $record[ 'Code2' ], kTYPE_EXACT, TRUE );
			}
			if( $record[ 'FlagThumb' ] !== NULL )
				$term->Image( 'Thumbnail flag',
							  kTYPE_PNG,
							  bin2hex( $record[ 'FlagThumb' ] ) );
			if( $record[ 'FlagImage' ] !== NULL )
				$term->Image( 'Image flag',
							  kTYPE_PNG,
							  bin2hex( $record[ 'FlagImage' ] ) );
			if( $record[ 'FlagVector' ] !== NULL )
				$term->Image( 'Vector flag', kTYPE_SVG, $record[ 'FlagVector' ] );
			$term->Relate( $alpha3_term, $is_a, TRUE );
			$term->Commit( $theContainer );
			$preferred = $term[ kTAG_LID ];
			
			//
			// Create alpha 3 node.
			//
			$node = $term_index->findOne( kTAG_TERM, (string) $term );
			if( $node === NULL )
			{
				$node = new COntologyNode( $container );
				$node->Term( $term );
				$node->Kind( kTYPE_ENUMERATION, TRUE );
				$node->Commit( $container );
			}
			else
				$node = new COntologyNode( $container, $node );
			
			//
			// Create alpha3 edge.
			//
			$id = Array();
			$id[] = $node->Node()->getId();
			$id[] = (string) $enum_of;
			$id[] = $alpha3_node->Node()->getId();
			$id = implode( '/', $id );
			$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
			if( $edge === NULL )
			{
				$edge = $node->RelateTo( $container, $enum_of, $alpha3_node );
				$edge->Commit( $container );
			}
			
			//
			// Create region edge.
			//
			if( $record[ 'Region' ] !== NULL )
			{
				$region
					= new COntologyNode( $container,
										 $_SESSION[ 'REGIONS' ][ $record[ 'Region' ] ] );
				$id = Array();
				$id[] = $node->Node()->getId();
				$id[] = (string) $part_of;
				$id[] =  $_SESSION[ 'REGIONS' ][ $record[ 'Region' ] ];
				$id = implode( '/', $id );
				$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
				if( $edge === NULL )
				{
					$edge = $node->RelateTo( $container, $part_of, $region );
					$edge->Commit( $container );
				}
				
				$term->Relate( $region->Term(), $part_of, TRUE );
				$term->Commit( $theContainer );
			}
			
			//
			// Display.
			//
			if( $doDisplay )
				echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
					 ." [$term] [".$node->Node()->getId()."]" );
		
			//
			// Handle alpha 2 code.
			//
			if( $record[ 'Code2' ] !== NULL )
			{
				//
				// Create alpha 2 term.
				//
				$term = new COntologyTerm();
				$term->NS( $ns_3166 );
				$term->Code( $record[ 'Code2' ] );
				$term->Name( $record[ 'Name' ], kDEFAULT_LANGUAGE );
				$term->Kind( kTYPE_ENUMERATION, TRUE );
				$term->Type( kTYPE_ENUM );
				$term->Enumeration( $term->Code(), TRUE );
				$term->Enumeration( $record[ 'ISO3' ], TRUE );
				$term->Synonym( $record[ 'ISO3' ], kTYPE_EXACT, TRUE );
				if( $record[ 'CodeNum' ] !== NULL )
				{
					$term->Enumeration( $record[ 'CodeNum' ], TRUE );
					$term->Synonym( $record[ 'CodeNum' ], kTYPE_EXACT, TRUE );
				}
				$term->Relate( $alpha2_term, $is_a, TRUE );
				$term->Preferred( $preferred );
				$term->Commit( $theContainer );
				
				//
				// Create alpha 2 node.
				//
				$node = $term_index->findOne( kTAG_TERM, (string) $term );
				if( $node === NULL )
				{
					$node = new COntologyNode( $container );
					$node->Term( $term );
					$node->Kind( kTYPE_ENUMERATION, TRUE );
					$node->Commit( $container );
				}
				else
					$node = new COntologyNode( $container, $node );
				
				//
				// Create alpha2 edge.
				//
				$id = Array();
				$id[] = $node->Node()->getId();
				$id[] = (string) $enum_of;
				$id[] = $alpha2_node->Node()->getId();
				$id = implode( '/', $id );
				$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
				if( $edge === NULL )
				{
					$edge = $node->RelateTo( $container, $enum_of, $alpha2_node );
					$edge->Commit( $container );
				}
				
				//
				// Display.
				//
				if( $doDisplay )
					echo( " [$term] [".$node->Node()->getId()."]" );
			}
		
			//
			// Handle numeric 3 code.
			//
			if( $record[ 'CodeNum' ] !== NULL )
			{
				//
				// Create numeric 3 term.
				//
				$term = new COntologyTerm();
				$term->NS( $ns_3166 );
				$term->Code( $record[ 'CodeNum' ] );
				$term->Name( $record[ 'Name' ], kDEFAULT_LANGUAGE );
				$term->Kind( kTYPE_ENUMERATION, TRUE );
				$term->Type( kTYPE_ENUM );
				$term->Enumeration( $term->Code(), TRUE );
				$term->Enumeration( $record[ 'ISO3' ], TRUE );
				$term->Synonym( $record[ 'ISO3' ], kTYPE_EXACT, TRUE );
				if( $record[ 'Code2' ] !== NULL )
				{
					$term->Enumeration( $record[ 'Code2' ], TRUE );
					$term->Synonym( $record[ 'Code2' ], kTYPE_EXACT, TRUE );
				}
				$term->Relate( $numeric3_term, $is_a, TRUE );
				$term->Preferred( $preferred );
				$term->Commit( $theContainer );
				
				//
				// Create numeric 3 node.
				//
				$node = $term_index->findOne( kTAG_TERM, (string) $term );
				if( $node === NULL )
				{
					$node = new COntologyNode( $container );
					$node->Term( $term );
					$node->Kind( kTYPE_ENUMERATION, TRUE );
					$node->Commit( $container );
				}
				else
					$node = new COntologyNode( $container, $node );
				
				//
				// Create numeric3 edge.
				//
				$id = Array();
				$id[] = $node->Node()->getId();
				$id[] = (string) $enum_of;
				$id[] = $numeric3_node->Node()->getId();
				$id = implode( '/', $id );
				$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
				if( $edge === NULL )
				{
					$edge = $node->RelateTo( $container, $enum_of, $numeric3_node );
					$edge->Commit( $container );
				}
				
				//
				// Display.
				//
				if( $doDisplay )
					echo( " [$term] [".$node->Node()->getId()."]" );
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
			// Get obsolete and valid terms.
			//
			$obsolete
				= new COntologyTerm
					( $theContainer,
					  COntologyTerm::HashIndex( (string) $ns_3166
					  						   .kTOKEN_NAMESPACE_SEPARATOR
					  						   .$record[ 'ISO3' ] ) );
			$valid
				= new COntologyTerm
					( $theContainer,
					  COntologyTerm::HashIndex( (string) $ns_3166
					  						   .kTOKEN_NAMESPACE_SEPARATOR
					  						   .$record[ 'ValidCode' ] ) );
			
			//
			// Set valid reference.
			//
			$obsolete->Valid( $valid );
			$obsolete->Commit( $theContainer );
			
			//
			// Display.
			//
			if( $doDisplay )
				echo( "[$obsolete] ==> [$valid]\n" );
		
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
		// Get node term index.
		//
		$term_index = new NodeIndex( $container[ kTAG_NODE ], kINDEX_NODE_TERM );
		$term_index->save();
		$node_index = new RelationshipIndex( $container[ kTAG_NODE ], kINDEX_NODE_TERM );
		$node_index->save();
		
		//
		// IS-A.
		//
		$is_a
			= CPersistentUnitObject::NewObject
				( $theContainer, COntologyTermObject::HashIndex( kPRED_IS_A ),
				  kFLAG_STATE_ENCODED );
		if( ! $is_a )
			throw new Exception
				( 'Unable to find subclass predicate ['.kPRED_IS_A.'].' );		// !@! ==>
		
		//
		// ENUM-OF.
		//
		$enum_of
			= CPersistentUnitObject::NewObject
				( $theContainer, COntologyTermObject::HashIndex( kPRED_ENUM_OF ),
				  kFLAG_STATE_ENCODED );
		if( ! $enum_of )
			throw new Exception
				( 'Unable to find enumeration predicate ['.kPRED_ENUM_OF.'].' );// !@! ==>
		
		//
		// SCALE-OF.
		//
		$scale_of
			= CPersistentUnitObject::NewObject
				( $theContainer, COntologyTermObject::HashIndex( kPRED_SCALE_OF ),
				  kFLAG_STATE_ENCODED );
		if( ! $scale_of )
			throw new Exception
				( 'Unable to find scale predicate ['.kPRED_SCALE_OF.'].' );		// !@! ==>
		
		//
		// Get MCPD term.
		//
		$mcpd_term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'MCPD' ) );
		if( ! $mcpd_term->Persistent() )
		{
			$mcpd_term->Code( 'MCPD' );
			$mcpd_term->Name( 'FAO/IPGRI Multi-Crop Passport Descriptor',
							  kDEFAULT_LANGUAGE );
			$mcpd_term->Definition
			( 'The list of multi-crop passport descriptors (MCPD) is developed jointly '
			 .'by IPGRI and FAO to provide international standards to facilitate germplasm '
			 .'passport information exchange. These descriptors aim to be compatible with '
			 .'IPGRI crop descriptor lists and with the descriptors used for the FAO '
			 .'World Information and Early Warning System (WIEWS) on plant genetic '
			 .'resources (PGR).', kDEFAULT_LANGUAGE );
			$mcpd_term->Kind( kTYPE_NAMESPACE, TRUE );
			$mcpd_term->Kind( kTYPE_ROOT, TRUE );
			$mcpd_term->Domain( kDOMAIN_ACCESSION, TRUE );
			$mcpd_term->Category( kCATEGORY_PASSPORT, TRUE );
			$mcpd_term->Version( 'December 2001' );
			$mcpd_term->Commit( $theContainer );

		} $ns_mcpd = $mcpd_term[ kTAG_GID ].kTOKEN_NAMESPACE_SEPARATOR;
		
		//
		// Get MCPD node.
		//
		$mcpd_node = $term_index->findOne( kTAG_TERM, (string) $mcpd_term );
		if( $mcpd_node === NULL )
		{
			$mcpd_node = new COntologyNode( $container );
			$mcpd_node->Term( $mcpd_term );
			$mcpd_node->Kind( kTYPE_ROOT, TRUE );
			$mcpd_node->Domain( kDOMAIN_ACCESSION, TRUE );
			$mcpd_node->Category( kCATEGORY_PASSPORT, TRUE );
			$mcpd_node->Commit( $container );
		}
		else
			$mcpd_node = new COntologyNode( $container, $mcpd_node );

		//
		// Display.
		//
		if( $doDisplay )
			echo( $mcpd_term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$mcpd_term] [".$mcpd_node->Node()->getId()."]\n" );
		
		//
		// Get EURISCO MCPD term.
		//
		$mcpd_eurisco_term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'MCPD:EURISCO' ) );
		if( ! $mcpd_eurisco_term->Persistent() )
		{
			$mcpd_eurisco_term->NS( $mcpd_term );
			$mcpd_eurisco_term->Code( 'EURISCO' );
			$mcpd_eurisco_term->Name
			( 'EURISCO extension to the FAO/IPGRI Multi-Crop Passport Descriptor',
			  kDEFAULT_LANGUAGE );
			$mcpd_eurisco_term->Definition
			( 'Extensions to the FAO/IPGRI list of multi-crop passport descriptors (MCPD).',
			  kDEFAULT_LANGUAGE );
			$mcpd_eurisco_term->Kind( kTYPE_NAMESPACE, TRUE );
			$mcpd_eurisco_term->Domain( kDOMAIN_GEOGRAPHY, TRUE );
			$mcpd_eurisco_term->Category( kCATEGORY_ADMIN, TRUE );
			$mcpd_eurisco_term->Version( 'March 2011' );
			$mcpd_eurisco_term->Commit( $theContainer );

		} $ns_eurisco_mcpd = $mcpd_eurisco_term[ kTAG_GID ].kTOKEN_NAMESPACE_SEPARATOR;

		//
		// Display.
		//
		if( $doDisplay )
			echo( $mcpd_eurisco_term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$mcpd_eurisco_term]\n" );
	 
		/*================================================================================
		 *	INSTCODE																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( $ns_mcpd.'INSTCODE' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $mcpd_term );
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
			$term->Domain( kDOMAIN_ACCESSION, TRUE );
			$term->Category( kCATEGORY_PASSPORT, TRUE );
			$term->Commit( $theContainer );
		
		} $instcode = $term;
		
		//
		// Node.
		//
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_MEASURE, TRUE );
			$node->Domain( kDOMAIN_ACCESSION, TRUE );
			$node->Category( kCATEGORY_PASSPORT, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		
		//
		// Edge.
		//
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $mcpd_node->Node()->getId();
		$id = implode( kTOKEN_INDEX_SEPARATOR, $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $is_a, $mcpd_node );
			$edge->Commit( $container );
		}
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );
	 
		/*================================================================================
		 *	ACCENUMB																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( $ns_mcpd.'ACCENUMB' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $mcpd_term );
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
			$term->Domain( kDOMAIN_ACCESSION, TRUE );
			$term->Category( kCATEGORY_PASSPORT, TRUE );
			$term->Commit( $theContainer );
		
		} $accenumb = $term;
		
		//
		// Node.
		//
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_MEASURE, TRUE );
			$node->Domain( kDOMAIN_ACCESSION, TRUE );
			$node->Category( kCATEGORY_PASSPORT, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		
		//
		// Edge.
		//
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $mcpd_node->Node()->getId();
		$id = implode( kTOKEN_INDEX_SEPARATOR, $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $is_a, $mcpd_node );
			$edge->Commit( $container );
		}
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );
	 
		/*================================================================================
		 *	COLLNUMB																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( $ns_mcpd.'COLLNUMB' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $mcpd_term );
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
			$term->Domain( kDOMAIN_ACCESSION, TRUE );
			$term->Category( kCATEGORY_PASSPORT, TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_MEASURE, TRUE );
			$node->Domain( kDOMAIN_ACCESSION, TRUE );
			$node->Category( kCATEGORY_PASSPORT, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		
		//
		// Edge.
		//
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $mcpd_node->Node()->getId();
		$id = implode( kTOKEN_INDEX_SEPARATOR, $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $is_a, $mcpd_node );
			$edge->Commit( $container );
		}
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );
	 
		/*================================================================================
		 *	COLLCODE																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( $ns_mcpd.'COLLCODE' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $mcpd_term );
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
			$term->Domain( kDOMAIN_ACCESSION, TRUE );
			$term->Category( kCATEGORY_PASSPORT, TRUE );
			$term->Xref( $instcode, kTYPE_RELATED, TRUE );
			$term->Commit( $theContainer );
		
		} $collcode = $term;
		
		//
		// Node.
		//
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_MEASURE, TRUE );
			$node->Domain( kDOMAIN_ACCESSION, TRUE );
			$node->Category( kCATEGORY_PASSPORT, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		
		//
		// Edge.
		//
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $mcpd_node->Node()->getId();
		$id = implode( kTOKEN_INDEX_SEPARATOR, $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $is_a, $mcpd_node );
			$edge->Commit( $container );
		}
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );
	 
		/*================================================================================
		 *	GENUS																		 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( $ns_mcpd.'GENUS' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $mcpd_term );
			$term->Code( 'GENUS' );
			$term->Name( 'Genus', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Genus name for taxon. Initial uppercase letter required.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Domain( kDOMAIN_ACCESSION, TRUE );
			$term->Category( kCATEGORY_PASSPORT, TRUE );
			$term->Examples( 'Allium', TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_MEASURE, TRUE );
			$node->Domain( kDOMAIN_ACCESSION, TRUE );
			$node->Category( kCATEGORY_PASSPORT, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		
		//
		// Edge.
		//
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $mcpd_node->Node()->getId();
		$id = implode( kTOKEN_INDEX_SEPARATOR, $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $is_a, $mcpd_node );
			$edge->Commit( $container );
		}
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );
	 
		/*================================================================================
		 *	SPECIES																		 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( $ns_mcpd.'SPECIES' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $mcpd_term );
			$term->Code( 'SPECIES' );
			$term->Name( 'Species', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Specific epithet portion of the scientific name in lowercase letters. '
			 .'Following abbreviation is allowed: "sp.".',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Examples( 'paniculatum', TRUE );
			$term->Domain( kDOMAIN_ACCESSION, TRUE );
			$term->Category( kCATEGORY_PASSPORT, TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_MEASURE, TRUE );
			$node->Domain( kDOMAIN_ACCESSION, TRUE );
			$node->Category( kCATEGORY_PASSPORT, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		
		//
		// Edge.
		//
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $mcpd_node->Node()->getId();
		$id = implode( kTOKEN_INDEX_SEPARATOR, $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $is_a, $mcpd_node );
			$edge->Commit( $container );
		}
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );
	 
		/*================================================================================
		 *	SPAUTHOR																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( $ns_mcpd.'SPAUTHOR' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $mcpd_term );
			$term->Code( 'SPAUTHOR' );
			$term->Name( 'Species authority', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'The authority for the species name.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Examples( 'L.', TRUE );
			$term->Examples( '(Desf.) B. Fedtsch.', TRUE );
			$term->Domain( kDOMAIN_ACCESSION, TRUE );
			$term->Category( kCATEGORY_PASSPORT, TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_MEASURE, TRUE );
			$node->Domain( kDOMAIN_ACCESSION, TRUE );
			$node->Category( kCATEGORY_PASSPORT, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		
		//
		// Edge.
		//
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $mcpd_node->Node()->getId();
		$id = implode( kTOKEN_INDEX_SEPARATOR, $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $is_a, $mcpd_node );
			$edge->Commit( $container );
		}
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );
	 
		/*================================================================================
		 *	SUBTAXA																		 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( $ns_mcpd.'SUBTAXA' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $mcpd_term );
			$term->Code( 'SUBTAXA' );
			$term->Name( 'Subtaxa', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Subtaxa can be used to store any additional taxonomic identifier, '
			 .'in latin. Following abbreviations are allowed: "subsp." (for subspecies); '
			 .'"convar." (for convariety); "var." (for variety); "f." (for form).',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Examples( 'subsp. fuscum', TRUE );
			$term->Domain( kDOMAIN_ACCESSION, TRUE );
			$term->Category( kCATEGORY_PASSPORT, TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_MEASURE, TRUE );
			$node->Domain( kDOMAIN_ACCESSION, TRUE );
			$node->Category( kCATEGORY_PASSPORT, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		
		//
		// Edge.
		//
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $mcpd_node->Node()->getId();
		$id = implode( kTOKEN_INDEX_SEPARATOR, $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $is_a, $mcpd_node );
			$edge->Commit( $container );
		}
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );
	 
		/*================================================================================
		 *	SUBTAUTHOR																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( $ns_mcpd.'SUBTAUTHOR' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $mcpd_term );
			$term->Code( 'SUBTAUTHOR' );
			$term->Name( 'Subtaxa authority', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'The subtaxa authority at the most detailed taxonomic level.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Examples( '(Waldst. et Kit.) Arc.', TRUE );
			$term->Domain( kDOMAIN_ACCESSION, TRUE );
			$term->Category( kCATEGORY_PASSPORT, TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_MEASURE, TRUE );
			$node->Domain( kDOMAIN_ACCESSION, TRUE );
			$node->Category( kCATEGORY_PASSPORT, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		
		//
		// Edge.
		//
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $mcpd_node->Node()->getId();
		$id = implode( kTOKEN_INDEX_SEPARATOR, $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $is_a, $mcpd_node );
			$edge->Commit( $container );
		}
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );
	 
		/*================================================================================
		 *	CROPNAME																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( $ns_mcpd.'CROPNAME' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $mcpd_term );
			$term->Code( 'CROPNAME' );
			$term->Name( 'Common crop name', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Name of the crop in colloquial language, preferably English.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Examples( 'cauliflower', TRUE );
			$term->Examples( 'white cabbage', TRUE );
			$term->Examples( 'malting barley', TRUE );
			$term->Domain( kDOMAIN_ACCESSION, TRUE );
			$term->Category( kCATEGORY_PASSPORT, TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_MEASURE, TRUE );
			$node->Domain( kDOMAIN_ACCESSION, TRUE );
			$node->Category( kCATEGORY_PASSPORT, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		
		//
		// Edge.
		//
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $mcpd_node->Node()->getId();
		$id = implode( kTOKEN_INDEX_SEPARATOR, $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $is_a, $mcpd_node );
			$edge->Commit( $container );
		}
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );
	 
		/*================================================================================
		 *	ACCENAME																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( $ns_mcpd.'ACCENAME' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $mcpd_term );
			$term->Code( 'ACCENAME' );
			$term->Name( 'Accession name', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Either a registered or other formal designation given to the accession. '
			 .'First letter uppercase. Multiple names separated with semicolon without '
			 .'space.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Examples( 'Rheinische Vorgebirgstrauben;Emma;Avlon', TRUE );
			$term->Domain( kDOMAIN_ACCESSION, TRUE );
			$term->Category( kCATEGORY_PASSPORT, TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_MEASURE, TRUE );
			$node->Domain( kDOMAIN_ACCESSION, TRUE );
			$node->Category( kCATEGORY_PASSPORT, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		
		//
		// Edge.
		//
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $mcpd_node->Node()->getId();
		$id = implode( kTOKEN_INDEX_SEPARATOR, $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $is_a, $mcpd_node );
			$edge->Commit( $container );
		}
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );
	 
		/*================================================================================
		 *	ACQDATE																		 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( $ns_mcpd.'ACQDATE' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $mcpd_term );
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
			$term->Domain( kDOMAIN_ACCESSION, TRUE );
			$term->Category( kCATEGORY_PASSPORT, TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_MEASURE, TRUE );
			$node->Domain( kDOMAIN_ACCESSION, TRUE );
			$node->Category( kCATEGORY_PASSPORT, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		
		//
		// Edge.
		//
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $mcpd_node->Node()->getId();
		$id = implode( kTOKEN_INDEX_SEPARATOR, $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $is_a, $mcpd_node );
			$edge->Commit( $container );
		}
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );
	 
		/*================================================================================
		 *	ORIGCTY																		 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( $ns_mcpd.'ORIGCTY' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $mcpd_term );
			$term->Code( 'ORIGCTY' );
			$term->Name( 'Country of origin', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'ode of the country in which the sample was originally collected.',
			  kDEFAULT_LANGUAGE );
			$term->Domain( kDOMAIN_ACCESSION, TRUE );
			$term->Domain( kDOMAIN_GEOGRAPHY, TRUE );
			$term->Category( kCATEGORY_PASSPORT, TRUE );
			$term->Category( kCATEGORY_ADMIN, TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Domain( kDOMAIN_ACCESSION, TRUE );
			$node->Domain( kDOMAIN_GEOGRAPHY, TRUE );
			$node->Category( kCATEGORY_PASSPORT, TRUE );
			$node->Category( kCATEGORY_ADMIN, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		
		//
		// Edge.
		//
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $mcpd_node->Node()->getId();
		$id = implode( kTOKEN_INDEX_SEPARATOR, $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $is_a, $mcpd_node );
			$edge->Commit( $container );
		}
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );
	 
		/*================================================================================
		 *	ISO 3166-1 ALPHA-3															 *
		 *===============================================================================*/

		//
		// Term.
		//
		$scale_term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( 'ISO:3166-1:ALPHA-3' ) );
		
		//
		// Node.
		//
		$scale_node = $term_index->findOne( kTAG_TERM, (string) $scale_term );
		if( $scale_node === NULL )
			throw new Exception( "Term [$scale_term] node not found." );
		$scale_node = new COntologyNode( $container, $scale_node );
		
		//
		// Edge.
		//
		$id = Array();
		$id[] = $scale_node->Node()->getId();
		$id[] = (string) $scale_of;
		$id[] = $node->Node()->getId();
		$id = implode( kTOKEN_INDEX_SEPARATOR, $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $scale_node->RelateTo( $container, $scale_of, $node );
			$edge->Commit( $container );
		}
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );
	 
		/*================================================================================
		 *	COLLSITE																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( $ns_mcpd.'COLLSITE' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $mcpd_term );
			$term->Code( 'COLLSITE' );
			$term->Name( 'Location of collecting site', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Location information below the country level that describes where the '
			 .'accession was collected. This might include the distance in kilometres '
			 .'and direction from the nearest town, village or map grid reference point.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Examples( '7 km south of Curitiba in the state of Parana', TRUE );
			$term->Domain( kDOMAIN_ACCESSION, TRUE );
			$term->Domain( kDOMAIN_GEOGRAPHY, TRUE );
			$term->Category( kCATEGORY_PASSPORT, TRUE );
			$term->Category( kCATEGORY_ADMIN, TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_MEASURE, TRUE );
			$node->Domain( kDOMAIN_ACCESSION, TRUE );
			$node->Domain( kDOMAIN_GEOGRAPHY, TRUE );
			$node->Category( kCATEGORY_PASSPORT, TRUE );
			$node->Category( kCATEGORY_ADMIN, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		
		//
		// Edge.
		//
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $mcpd_node->Node()->getId();
		$id = implode( kTOKEN_INDEX_SEPARATOR, $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $is_a, $mcpd_node );
			$edge->Commit( $container );
		}
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );
	 
		/*================================================================================
		 *	LATITUDE																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( $ns_mcpd.'LATITUDE' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $mcpd_term );
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
			$term->Domain( kDOMAIN_ACCESSION, TRUE );
			$term->Domain( kDOMAIN_GEOGRAPHY, TRUE );
			$term->Category( kCATEGORY_PASSPORT, TRUE );
			$term->Category( kCATEGORY_GEO, TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_MEASURE, TRUE );
			$node->Domain( kDOMAIN_ACCESSION, TRUE );
			$node->Domain( kDOMAIN_GEOGRAPHY, TRUE );
			$node->Category( kCATEGORY_PASSPORT, TRUE );
			$node->Category( kCATEGORY_GEO, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		
		//
		// Edge.
		//
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $mcpd_node->Node()->getId();
		$id = implode( kTOKEN_INDEX_SEPARATOR, $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $is_a, $mcpd_node );
			$edge->Commit( $container );
		}
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );
	 
		/*================================================================================
		 *	LONGITUDE																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( $ns_mcpd.'LONGITUDE' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $mcpd_term );
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
			$term->Domain( kDOMAIN_ACCESSION, TRUE );
			$term->Domain( kDOMAIN_GEOGRAPHY, TRUE );
			$term->Category( kCATEGORY_PASSPORT, TRUE );
			$term->Category( kCATEGORY_GEO, TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_MEASURE, TRUE );
			$node->Domain( kDOMAIN_ACCESSION, TRUE );
			$node->Domain( kDOMAIN_GEOGRAPHY, TRUE );
			$node->Category( kCATEGORY_PASSPORT, TRUE );
			$node->Category( kCATEGORY_GEO, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		
		//
		// Edge.
		//
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $mcpd_node->Node()->getId();
		$id = implode( kTOKEN_INDEX_SEPARATOR, $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $is_a, $mcpd_node );
			$edge->Commit( $container );
		}
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );
	 
		/*================================================================================
		 *	ELEVATION																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( $ns_mcpd.'ELEVATION' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $mcpd_term );
			$term->Code( 'ELEVATION' );
			$term->Name( 'Elevation of collecting site', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Elevation of collecting site expressed in meters above sea level. '
			 .'Negative values are allowed.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_INT32 );
			$term->Examples( '763', TRUE );
			$term->Examples( '-15', TRUE );
			$term->Domain( kDOMAIN_ACCESSION, TRUE );
			$term->Domain( kDOMAIN_GEOGRAPHY, TRUE );
			$term->Category( kCATEGORY_PASSPORT, TRUE );
			$term->Category( kCATEGORY_GEO, TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_MEASURE, TRUE );
			$node->Domain( kDOMAIN_ACCESSION, TRUE );
			$node->Domain( kDOMAIN_GEOGRAPHY, TRUE );
			$node->Category( kCATEGORY_PASSPORT, TRUE );
			$node->Category( kCATEGORY_GEO, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		
		//
		// Edge.
		//
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $mcpd_node->Node()->getId();
		$id = implode( kTOKEN_INDEX_SEPARATOR, $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $is_a, $mcpd_node );
			$edge->Commit( $container );
		}
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );
	 
		/*================================================================================
		 *	COLLDATE																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( $ns_mcpd.'COLLDATE' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $mcpd_term );
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
			$term->Domain( kDOMAIN_ACCESSION, TRUE );
			$term->Category( kCATEGORY_PASSPORT, TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_MEASURE, TRUE );
			$node->Domain( kDOMAIN_ACCESSION, TRUE );
			$node->Category( kCATEGORY_PASSPORT, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		
		//
		// Edge.
		//
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $mcpd_node->Node()->getId();
		$id = implode( kTOKEN_INDEX_SEPARATOR, $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $is_a, $mcpd_node );
			$edge->Commit( $container );
		}
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );
	 
		/*================================================================================
		 *	BREDCODE																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( $ns_mcpd.'BREDCODE' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $mcpd_term );
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
			$term->Xref( $instcode, kTYPE_RELATED, TRUE );
			$term->Domain( kDOMAIN_ACCESSION, TRUE );
			$term->Category( kCATEGORY_PASSPORT, TRUE );
			$term->Commit( $theContainer );
		
		} $bredcode = $term;
		
		//
		// Node.
		//
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_MEASURE, TRUE );
			$node->Domain( kDOMAIN_ACCESSION, TRUE );
			$node->Category( kCATEGORY_PASSPORT, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		
		//
		// Edge.
		//
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $mcpd_node->Node()->getId();
		$id = implode( kTOKEN_INDEX_SEPARATOR, $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $is_a, $mcpd_node );
			$edge->Commit( $container );
		}
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );
	 
		/*================================================================================
		 *	SAMPSTAT																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( $ns_mcpd.'SAMPSTAT' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $mcpd_term );
			$term->Code( 'SAMPSTAT' );
			$term->Name( 'Biological status of accession', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'The coding scheme proposed can be used at 3 different levels of detail: '
			 .'either by using the general codes such as 100, 200, 300, 400 or by using '
			 .'the more specific codes such as 110, 120 etc.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_ENUM );
			$term->Pattern( '[0-9]{3}' );
			$term->Domain( kDOMAIN_ACCESSION, TRUE );
			$term->Category( kCATEGORY_PASSPORT, TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_MEASURE, TRUE );
			$node->Domain( kDOMAIN_ACCESSION, TRUE );
			$node->Category( kCATEGORY_PASSPORT, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		
		//
		// Edge.
		//
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $mcpd_node->Node()->getId();
		$id = implode( kTOKEN_INDEX_SEPARATOR, $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $is_a, $mcpd_node );
			$edge->Commit( $container );
		}
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );
		
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
				switch( $record[ 'Code' ] )
				{
					case '130':
						$enum_term->NS( $mcpd_eurisco_term
									   .kTOKEN_NAMESPACE_SEPARATOR
									   .$term->Code() );
						break;
					default:
						$enum_term->NS( $term );
						break;
				}
				$enum_term->Code( $record[ 'Code' ] );
				$enum_term->Name( $record[ 'Label' ], kDEFAULT_LANGUAGE );
				$enum_term->Name( $record[ 'Description' ], kDEFAULT_LANGUAGE );
				$enum_term->Kind( kTYPE_ENUMERATION, TRUE );
				$enum_term->Type( kTYPE_ENUM );
				$enum_term->Enumeration( $enum_term->Code(), TRUE );
				if( $record[ 'Parent' ] === NULL )
				{
					$enum_term->Relate( $term, $enum_of, TRUE );
					$enum_term->Commit( $theContainer );
				}
				$enum_term->Commit( $theContainer );
			}
			
			//
			// Create status node.
			//
			$enum_node = $term_index->findOne( kTAG_TERM, (string) $enum_term );
			if( $enum_node === NULL )
			{
				$enum_node = new COntologyNode( $container );
				$enum_node->Term( $enum_term );
				$enum_node->Kind( kTYPE_ENUMERATION, TRUE );
				$enum_node->Commit( $container );
			}
			else
				$enum_node = new COntologyNode( $container, $enum_node );
			
			//
			// Handle first level status.
			//
			if( $record[ 'Parent' ] === NULL )
			{
				//
				// Create status edge.
				//
				$id = Array();
				$id[] = $enum_node->Node()->getId();
				$id[] = (string) $enum_of;
				$id[] = $node->Node()->getId();
				$id = implode( kTOKEN_INDEX_SEPARATOR, $id );
				$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
				if( $edge === NULL )
				{
					$edge = $enum_node->RelateTo( $container, $enum_of, $node );
					$edge->Commit( $container );
				}
			}
			
			//
			// Display.
			//
			if( $doDisplay )
				echo( $enum_term->Name( NULL, kDEFAULT_LANGUAGE )
					 ." [$enum_term]\n" );
		
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
			switch( $record[ 'Code' ] )
			{
				case '130':
					$child_term
						= new COntologyTerm
							( $theContainer,
							  COntologyTerm::HashIndex
								( $mcpd_eurisco_term
								 .kTOKEN_NAMESPACE_SEPARATOR
								 .$term->Code()
								 .kTOKEN_NAMESPACE_SEPARATOR
								 .$record[ 'Code' ] ) );
					break;
				default:
					$child_term
						= new COntologyTerm
							( $theContainer,
							  COntologyTerm::HashIndex
								( $term
								 .kTOKEN_NAMESPACE_SEPARATOR
								 .$record[ 'Code' ] ) );
					break;
			}

			//
			// Get child node.
			//
			$child_node = $term_index->findOne( kTAG_TERM, (string) $child_term );
			if( $child_node === NULL )
				throw new Exception( "Term [$child_term] node not found." );
			$child_node = new COntologyNode( $container, $child_node );
			
			//
			// Get parent term.
			//
			switch( $record[ 'Parent' ] )
			{
				case '130':
					$parent_term
						= new COntologyTerm
							( $theContainer,
							  COntologyTerm::HashIndex
								( $mcpd_eurisco_term
								 .kTOKEN_NAMESPACE_SEPARATOR
								 .$term->Code()
								 .kTOKEN_NAMESPACE_SEPARATOR
								 .$record[ 'Parent' ] ) );
					break;
				default:
					$parent_term
						= new COntologyTerm
							( $theContainer,
							  COntologyTerm::HashIndex
								( $term
								 .kTOKEN_NAMESPACE_SEPARATOR
								 .$record[ 'Parent' ] ) );
					break;
			}

			//
			// Get parent node.
			//
			$parent_node = $term_index->findOne( kTAG_TERM, (string) $parent_term );
			if( $parent_node === NULL )
				throw new Exception( "Term [$parent_term] node not found." );
			$parent_node = new COntologyNode( $container, $parent_node );
			
			//
			// Relate terms.
			//
			$child_term->Relate( $parent_term, $enum_of, TRUE );
			$child_term->Commit( $theContainer );
			
			//
			// Get status edge.
			//
			$id = Array();
			$id[] = $child_node->Node()->getId();
			$id[] = (string) $enum_of;
			$id[] = $parent_node->Node()->getId();
			$id = implode( kTOKEN_INDEX_SEPARATOR, $id );
			$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
			if( $edge === NULL )
			{
				$edge = $child_node->RelateTo( $container, $enum_of, $parent_node );
				$edge->Commit( $container );
			}
			
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
				( $theContainer, COntologyTerm::HashIndex( $ns_mcpd.'ANCEST' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $mcpd_term );
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
			$term->Domain( kDOMAIN_ACCESSION, TRUE );
			$term->Category( kCATEGORY_PASSPORT, TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_MEASURE, TRUE );
			$node->Domain( kDOMAIN_ACCESSION, TRUE );
			$node->Category( kCATEGORY_PASSPORT, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		
		//
		// Edge.
		//
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $mcpd_node->Node()->getId();
		$id = implode( kTOKEN_INDEX_SEPARATOR, $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $is_a, $mcpd_node );
			$edge->Commit( $container );
		}
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );
	 
		/*================================================================================
		 *	COLLSRC																		 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( $ns_mcpd.'COLLSRC' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $mcpd_term );
			$term->Code( 'COLLSRC' );
			$term->Name( 'Collecting/acquisition source', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'The coding scheme proposed can be used at 2 different levels of detail: '
			 .'either by using the general codes (in boldface) such as 10, 20, 30, 40 '
			 .'or by using the more specific codes such as 11, 12 etc.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_ENUM );
			$term->Pattern( '[0-9]{2}' );
			$term->Domain( kDOMAIN_ACCESSION, TRUE );
			$term->Category( kCATEGORY_PASSPORT, TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_MEASURE, TRUE );
			$node->Domain( kDOMAIN_ACCESSION, TRUE );
			$node->Category( kCATEGORY_PASSPORT, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		
		//
		// Edge.
		//
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $mcpd_node->Node()->getId();
		$id = implode( kTOKEN_INDEX_SEPARATOR, $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $is_a, $mcpd_node );
			$edge->Commit( $container );
		}
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );
		
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
				if( $record[ 'Parent' ] === NULL )
				{
					$enum_term->Relate( $term, $enum_of, TRUE );
					$enum_term->Commit( $theContainer );
				}
				$enum_term->Commit( $theContainer );
			}
			
			//
			// Create status node.
			//
			$enum_node = $term_index->findOne( kTAG_TERM, (string) $enum_term );
			if( $enum_node === NULL )
			{
				$enum_node = new COntologyNode( $container );
				$enum_node->Term( $enum_term );
				$enum_node->Kind( kTYPE_ENUMERATION, TRUE );
				$enum_node->Commit( $container );
			}
			else
				$enum_node = new COntologyNode( $container, $enum_node );
			
			//
			// Handle first level status.
			//
			if( $record[ 'Parent' ] === NULL )
			{
				//
				// Create status edge.
				//
				$id = Array();
				$id[] = $enum_node->Node()->getId();
				$id[] = (string) $enum_of;
				$id[] = $node->Node()->getId();
				$id = implode( kTOKEN_INDEX_SEPARATOR, $id );
				$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
				if( $edge === NULL )
				{
					$edge = $enum_node->RelateTo( $container, $enum_of, $node );
					$edge->Commit( $container );
				}
			}
			
			//
			// Display.
			//
			if( $doDisplay )
				echo( $enum_term->Name( NULL, kDEFAULT_LANGUAGE )
					 ." [$enum_term]\n" );
		
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
			$child_term
				= new COntologyTerm
					( $theContainer,
					  COntologyTerm::HashIndex
						( $term
						 .kTOKEN_NAMESPACE_SEPARATOR
						 .$record[ 'Code' ] ) );

			//
			// Get child node.
			//
			$child_node = $term_index->findOne( kTAG_TERM, (string) $child_term );
			if( $child_node === NULL )
				throw new Exception( "Term [$child_term] node not found." );
			$child_node = new COntologyNode( $container, $child_node );
			
			//
			// Get parent term.
			//
			$parent_term
				= new COntologyTerm
					( $theContainer,
					  COntologyTerm::HashIndex
						( $term
						 .kTOKEN_NAMESPACE_SEPARATOR
						 .$record[ 'Parent' ] ) );

			//
			// Get parent node.
			//
			$parent_node = $term_index->findOne( kTAG_TERM, (string) $parent_term );
			if( $parent_node === NULL )
				throw new Exception( "Term [$parent_term] node not found." );
			$parent_node = new COntologyNode( $container, $parent_node );
			
			//
			// Relate terms.
			//
			$child_term->Relate( $parent_term, $enum_of, TRUE );
			$child_term->Commit( $theContainer );
			
			//
			// Get status edge.
			//
			$id = Array();
			$id[] = $child_node->Node()->getId();
			$id[] = (string) $enum_of;
			$id[] = $parent_node->Node()->getId();
			$id = implode( kTOKEN_INDEX_SEPARATOR, $id );
			$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
			if( $edge === NULL )
			{
				$edge = $child_node->RelateTo( $container, $enum_of, $parent_node );
				$edge->Commit( $container );
			}
			
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
				( $theContainer, COntologyTerm::HashIndex( $ns_mcpd.'DONORCODE' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $mcpd_term );
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
			$term->Xref( $instcode, kTYPE_RELATED, TRUE );
			$term->Domain( kDOMAIN_ACCESSION, TRUE );
			$term->Category( kCATEGORY_PASSPORT, TRUE );
			$term->Commit( $theContainer );
		
		} $donorcode = $term;
		
		//
		// Node.
		//
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_MEASURE, TRUE );
			$node->Domain( kDOMAIN_ACCESSION, TRUE );
			$node->Category( kCATEGORY_PASSPORT, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		
		//
		// Edge.
		//
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $mcpd_node->Node()->getId();
		$id = implode( kTOKEN_INDEX_SEPARATOR, $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $is_a, $mcpd_node );
			$edge->Commit( $container );
		}
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );
	 
		/*================================================================================
		 *	DONORNUMB																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( $ns_mcpd.'DONORNUMB' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $mcpd_term );
			$term->Code( 'DONORNUMB' );
			$term->Name( 'Donor accession number', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Accession number assigned to the accession by the donor.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Examples( 'NGB1912', TRUE );
			$term->Xref( $accenumb, kTYPE_RELATED, TRUE );
			$term->Domain( kDOMAIN_ACCESSION, TRUE );
			$term->Category( kCATEGORY_PASSPORT, TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_MEASURE, TRUE );
			$node->Domain( kDOMAIN_ACCESSION, TRUE );
			$node->Category( kCATEGORY_PASSPORT, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		
		//
		// Edge.
		//
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $mcpd_node->Node()->getId();
		$id = implode( kTOKEN_INDEX_SEPARATOR, $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $is_a, $mcpd_node );
			$edge->Commit( $container );
		}
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );
	 
		/*================================================================================
		 *	OTHERNUMB																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( $ns_mcpd.'OTHERNUMB' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $mcpd_term );
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
			$term->Xref( $instcode, kTYPE_RELATED, TRUE );
			$term->Xref( $accenumb, kTYPE_RELATED, TRUE );
			$term->Domain( kDOMAIN_ACCESSION, TRUE );
			$term->Category( kCATEGORY_PASSPORT, TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_MEASURE, TRUE );
			$node->Domain( kDOMAIN_ACCESSION, TRUE );
			$node->Category( kCATEGORY_PASSPORT, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		
		//
		// Edge.
		//
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $mcpd_node->Node()->getId();
		$id = implode( kTOKEN_INDEX_SEPARATOR, $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $is_a, $mcpd_node );
			$edge->Commit( $container );
		}
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );
	 
		/*================================================================================
		 *	DUPLSITE																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( $ns_mcpd.'DUPLSITE' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $mcpd_term );
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
			$term->Xref( $instcode, kTYPE_RELATED, TRUE );
			$term->Domain( kDOMAIN_ACCESSION, TRUE );
			$term->Category( kCATEGORY_PASSPORT, TRUE );
			$term->Commit( $theContainer );
		
		} $duplsite = $term;
		
		//
		// Node.
		//
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_MEASURE, TRUE );
			$node->Domain( kDOMAIN_ACCESSION, TRUE );
			$node->Category( kCATEGORY_PASSPORT, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		
		//
		// Edge.
		//
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $mcpd_node->Node()->getId();
		$id = implode( kTOKEN_INDEX_SEPARATOR, $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $is_a, $mcpd_node );
			$edge->Commit( $container );
		}
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );
	 
		/*================================================================================
		 *	STORAGE																		 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( $ns_mcpd.'STORAGE' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $mcpd_term );
			$term->Code( 'STORAGE' );
			$term->Name( 'Type of germplasm storage', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'If germplasm is maintained under different types of storage, multiple '
			 .'choices are allowed (separated by a semicolon). (Refer to FAO/IPGRI '
			 .'Genebank Standards 1994 for details on storage type.)',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_ENUM_SET );
			$term->Pattern( '[0-9]{2}' );
			$term->Domain( kDOMAIN_ACCESSION, TRUE );
			$term->Category( kCATEGORY_PASSPORT, TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_MEASURE, TRUE );
			$node->Domain( kDOMAIN_ACCESSION, TRUE );
			$node->Category( kCATEGORY_PASSPORT, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		
		//
		// Edge.
		//
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $mcpd_node->Node()->getId();
		$id = implode( kTOKEN_INDEX_SEPARATOR, $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $is_a, $mcpd_node );
			$edge->Commit( $container );
		}
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );
		
		//
		// Load biological status codes.
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
				if( $record[ 'Parent' ] === NULL )
				{
					$enum_term->Relate( $term, $enum_of, TRUE );
					$enum_term->Commit( $theContainer );
				}
				$enum_term->Commit( $theContainer );
			}
			
			//
			// Create storage node.
			//
			$enum_node = $term_index->findOne( kTAG_TERM, (string) $enum_term );
			if( $enum_node === NULL )
			{
				$enum_node = new COntologyNode( $container );
				$enum_node->Term( $enum_term );
				$enum_node->Kind( kTYPE_ENUMERATION, TRUE );
				$enum_node->Commit( $container );
			}
			else
				$enum_node = new COntologyNode( $container, $enum_node );
			
			//
			// Handle first level storage.
			//
			if( $record[ 'Parent' ] === NULL )
			{
				//
				// Create status edge.
				//
				$id = Array();
				$id[] = $enum_node->Node()->getId();
				$id[] = (string) $enum_of;
				$id[] = $node->Node()->getId();
				$id = implode( kTOKEN_INDEX_SEPARATOR, $id );
				$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
				if( $edge === NULL )
				{
					$edge = $enum_node->RelateTo( $container, $enum_of, $node );
					$edge->Commit( $container );
				}
			}
			
			//
			// Display.
			//
			if( $doDisplay )
				echo( $enum_term->Name( NULL, kDEFAULT_LANGUAGE )
					 ." [$enum_term]\n" );
		
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
			$child_term
				= new COntologyTerm
					( $theContainer,
					  COntologyTerm::HashIndex
						( $term
						 .kTOKEN_NAMESPACE_SEPARATOR
						 .$record[ 'Code' ] ) );

			//
			// Get child node.
			//
			$child_node = $term_index->findOne( kTAG_TERM, (string) $child_term );
			if( $child_node === NULL )
				throw new Exception( "Term [$child_term] node not found." );
			$child_node = new COntologyNode( $container, $child_node );
			
			//
			// Get parent term.
			//
			$parent_term
				= new COntologyTerm
					( $theContainer,
					  COntologyTerm::HashIndex
						( $term
						 .kTOKEN_NAMESPACE_SEPARATOR
						 .$record[ 'Parent' ] ) );

			//
			// Get parent node.
			//
			$parent_node = $term_index->findOne( kTAG_TERM, (string) $parent_term );
			if( $parent_node === NULL )
				throw new Exception( "Term [$parent_term] node not found." );
			$parent_node = new COntologyNode( $container, $parent_node );
			
			//
			// Relate terms.
			//
			$child_term->Relate( $parent_term, $enum_of, TRUE );
			$child_term->Commit( $theContainer );
			
			//
			// Get storage edge.
			//
			$id = Array();
			$id[] = $child_node->Node()->getId();
			$id[] = (string) $enum_of;
			$id[] = $parent_node->Node()->getId();
			$id = implode( kTOKEN_INDEX_SEPARATOR, $id );
			$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
			if( $edge === NULL )
			{
				$edge = $child_node->RelateTo( $container, $enum_of, $parent_node );
				$edge->Commit( $container );
			}
			
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
				( $theContainer, COntologyTerm::HashIndex( $ns_mcpd.'REMARKS' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $mcpd_term );
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
			$term->Domain( kDOMAIN_ACCESSION, TRUE );
			$term->Category( kCATEGORY_PASSPORT, TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_MEASURE, TRUE );
			$node->Domain( kDOMAIN_ACCESSION, TRUE );
			$node->Category( kCATEGORY_PASSPORT, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		
		//
		// Edge.
		//
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $mcpd_node->Node()->getId();
		$id = implode( kTOKEN_INDEX_SEPARATOR, $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $is_a, $mcpd_node );
			$edge->Commit( $container );
		}
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );
	 
		/*================================================================================
		 *	COLLDESCR																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( $ns_mcpd.'COLLDESCR' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $mcpd_eurisco_term );
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
			$term->Xref( $collcode, kTYPE_RELATED, TRUE );
			$term->Domain( kDOMAIN_ACCESSION, TRUE );
			$term->Category( kCATEGORY_PASSPORT, TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_MEASURE, TRUE );
			$node->Domain( kDOMAIN_ACCESSION, TRUE );
			$node->Category( kCATEGORY_PASSPORT, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		
		//
		// Edge.
		//
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $mcpd_node->Node()->getId();
		$id = implode( kTOKEN_INDEX_SEPARATOR, $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $is_a, $mcpd_node );
			$edge->Commit( $container );
		}
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );
	 
		/*================================================================================
		 *	BREDDESCR																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( $ns_mcpd.'BREDDESCR' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $mcpd_eurisco_term );
			$term->Code( 'BREDDESCR' );
			$term->Name( 'Decoded breeding institute', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Brief name and location of the breeding institute. Only to be used '
			 .'if BREDCODE can not be used since the FAO Institution Code for this '
			 .'institute is not (yet) available.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Examples( 'CFFR from Chile', TRUE );
			$term->Xref( $bredcode, kTYPE_RELATED, TRUE );
			$term->Domain( kDOMAIN_ACCESSION, TRUE );
			$term->Category( kCATEGORY_PASSPORT, TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_MEASURE, TRUE );
			$node->Domain( kDOMAIN_ACCESSION, TRUE );
			$node->Category( kCATEGORY_PASSPORT, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		
		//
		// Edge.
		//
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $mcpd_node->Node()->getId();
		$id = implode( kTOKEN_INDEX_SEPARATOR, $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $is_a, $mcpd_node );
			$edge->Commit( $container );
		}
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );
	 
		/*================================================================================
		 *	DONORDESCR																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( $ns_mcpd.'DONORDESCR' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $mcpd_eurisco_term );
			$term->Code( 'DONORDESCR' );
			$term->Name( 'Decoded donor institute', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Brief name and location of the donor institute. Only to be used '
			 .'if DONORCODE can not be used since the FAO Institution Code for this '
			 .'institute is not (yet) available.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Examples( 'Nelly Goudwaard, Groningen, The Netherlands', TRUE );
			$term->Xref( $donorcode, kTYPE_RELATED, TRUE );
			$term->Domain( kDOMAIN_ACCESSION, TRUE );
			$term->Category( kCATEGORY_PASSPORT, TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_MEASURE, TRUE );
			$node->Domain( kDOMAIN_ACCESSION, TRUE );
			$node->Category( kCATEGORY_PASSPORT, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		
		//
		// Edge.
		//
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $mcpd_node->Node()->getId();
		$id = implode( kTOKEN_INDEX_SEPARATOR, $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $is_a, $mcpd_node );
			$edge->Commit( $container );
		}
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );
	 
		/*================================================================================
		 *	DUPLDESCR																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( $ns_mcpd.'DUPLDESCR' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $mcpd_eurisco_term );
			$term->Code( 'DUPLDESCR' );
			$term->Name( 'Decoded safety duplication location', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'Brief name and location of the institute maintaining the safety duplicate. '
			 .'Only to be used if DUPLSITE can not be used since the FAO Institution Code '
			 .'for this institute is not (yet) available.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Examples( 'Pakhoed Freezers inc., Paramaribo, Surinam', TRUE );
			$term->Xref( $duplsite, kTYPE_RELATED, TRUE );
			$term->Domain( kDOMAIN_ACCESSION, TRUE );
			$term->Category( kCATEGORY_PASSPORT, TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_MEASURE, TRUE );
			$node->Domain( kDOMAIN_ACCESSION, TRUE );
			$node->Category( kCATEGORY_PASSPORT, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		
		//
		// Edge.
		//
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $mcpd_node->Node()->getId();
		$id = implode( kTOKEN_INDEX_SEPARATOR, $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $is_a, $mcpd_node );
			$edge->Commit( $container );
		}
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );
	 
		/*================================================================================
		 *	ACCEURL																		 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( $ns_mcpd.'ACCEURL' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $mcpd_eurisco_term );
			$term->Code( 'ACCEURL' );
			$term->Name( 'Accession URL', kDEFAULT_LANGUAGE );
			$term->Definition
			( 'URL linking to additional data about the accession either in the holding '
			 .'genebank or from another source.',
			  kDEFAULT_LANGUAGE );
			$term->Type( kTYPE_STRING );
			$term->Examples( 'http://www.cgn.wageningen-ur.nl/pgr/collections/passdeta.asp?accenumb=CGN04848',
							 TRUE );
			$term->Domain( kDOMAIN_ACCESSION, TRUE );
			$term->Category( kCATEGORY_PASSPORT, TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_MEASURE, TRUE );
			$node->Domain( kDOMAIN_ACCESSION, TRUE );
			$node->Category( kCATEGORY_PASSPORT, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		
		//
		// Edge.
		//
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $mcpd_node->Node()->getId();
		$id = implode( kTOKEN_INDEX_SEPARATOR, $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $is_a, $mcpd_node );
			$edge->Commit( $container );
		}
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );
	 
		/*================================================================================
		 *	MLSSTAT																		 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( $ns_mcpd.'MLSSTAT' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $mcpd_eurisco_term );
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
			$term->Domain( kDOMAIN_ACCESSION, TRUE );
			$term->Category( kCATEGORY_PASSPORT, TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_MEASURE, TRUE );
			$node->Domain( kDOMAIN_ACCESSION, TRUE );
			$node->Category( kCATEGORY_PASSPORT, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		
		//
		// Edge.
		//
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $mcpd_node->Node()->getId();
		$id = implode( kTOKEN_INDEX_SEPARATOR, $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $is_a, $mcpd_node );
			$edge->Commit( $container );
		}
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );
	 
		/*================================================================================
		 *	AEGISSTAT																	 *
		 *===============================================================================*/

		//
		// Term.
		//
		$term
			= new COntologyTerm
				( $theContainer, COntologyTerm::HashIndex( $ns_mcpd.'MLSSTAT' ) );
		if( ! $term->Persistent() )
		{
			$term->NS( $mcpd_eurisco_term );
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
			$term->Domain( kDOMAIN_ACCESSION, TRUE );
			$term->Category( kCATEGORY_PASSPORT, TRUE );
			$term->Commit( $theContainer );
		}
		
		//
		// Node.
		//
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Kind( kTYPE_MEASURE, TRUE );
			$node->Domain( kDOMAIN_ACCESSION, TRUE );
			$node->Category( kCATEGORY_PASSPORT, TRUE );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		
		//
		// Edge.
		//
		$id = Array();
		$id[] = $node->Node()->getId();
		$id[] = (string) $is_a;
		$id[] = $mcpd_node->Node()->getId();
		$id = implode( kTOKEN_INDEX_SEPARATOR, $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $is_a, $mcpd_node );
			$edge->Commit( $container );
		}
		
		//
		// Display.
		//
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );
		
	} // LoadMCPD.


?>
