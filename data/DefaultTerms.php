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
 * Run-time.
 *
 * This include file contains the run-time definitions.
 */
require_once( "/Library/WebServer/Library/wrapper/environment.inc.php" );

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
	LoadISO3166( $_SESSION[ kSESSION_CONTAINER ], TRUE );

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
	 * @access private
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
	 * @access private
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
	 * @access private
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
	 * @access private
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
	 * @access private
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
	 * @access private
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
	 * @access private
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
	 * @access private
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
	 * @access private
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
	 * @access private
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
		// Term.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ENUMERATION );
		$term->Code( substr( kTYPE_TERM, 1 ) );
		$term->Name( 'Term', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents a generic term.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kTYPE_TERM', kTYPE_EXACT, TRUE );
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
		// Ontology term.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Kind( kTYPE_ENUMERATION );
		$term->Code( substr( kTYPE_ONTOLOGY, 1 ) );
		$term->Name( 'Ontology', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents an ontology.',
		  kDEFAULT_LANGUAGE );
		$term->Synonym( 'kTYPE_ONTOLOGY', kTYPE_EXACT, TRUE );
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
	 * @access private
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
	 * @access private
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
	 * @access private
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
	 * @access private
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
		$term->Type( kTYPE_ENUM );
		$term->Cardinality( kCARD_0_1 );
		$term->Synonym( 'kTAG_TYPE', kTYPE_EXACT, TRUE );
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
		$term->Type( kTYPE_ENUM );
		$term->Cardinality( kCARD_ANY );
		$term->Synonym( 'kTAG_KIND', kTYPE_EXACT, TRUE );
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
		$term->Type( kTYPE_ENUM );
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
	 * @access private
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
	 * @access private
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
	 *	LoadISO3166																		*
	 *==================================================================================*/

	/**
	 * Load ISO 3166.
	 *
	 * This function will load the ISO 3166 enumeration terms.
	 *
	 * If the last parameter is <i>TRUE</i>, the function will display the name of the
	 * created terms.
	 *
	 * @param CContainer			$theContainer		Collection.
	 * @param boolean				$doDisplay			Display created terms.
	 *
	 * @access private
	 */
	function LoadISO3166( CContainer $theContainer, $doDisplay = TRUE )
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
		// ENUM-OF.
		//
		$pred
			= CPersistentUnitObject::NewObject
				( $theContainer, COntologyTermObject::HashIndex( kPRED_ENUM_OF ),
				  kFLAG_STATE_ENCODED );
		if( ! $pred )
			throw new Exception
				( 'Unable to find enumeration predicate.' );					// !@! ==>
		
		//
		// ISO 3166-1.
		//
		$ns = new COntologyTerm();
		$ns->Code( 'ISO-3166-1' );
        $ns->Name( 'Country codes', kDEFAULT_LANGUAGE );
		$ns->Definition
		( 'Codes for the representation of names of countries and their '
		 .'subdivisions – Part 1: Country codes.', kDEFAULT_LANGUAGE );
		$ns->Kind( kTYPE_NAMESPACE, TRUE );
		$ns->Commit( $theContainer );
		$root = $term_index->findOne( kTAG_TERM, (string) $ns );
		if( $root === NULL )
		{
			$root = new COntology( $container );
			$root->Term( $ns );
			$root->Commit( $container );
		}
		else
			$root = new COntology( $container, $root );
		if( $doDisplay )
			echo( $ns->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$ns] [".$root->Node()->getId()."]\n" );
		
		//
		// ISO 3166-1 - Alpha 3 codes.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'Alpha-3' );
		$term->Name( '3-character codes', kDEFAULT_LANGUAGE );
		$term->Definition
		( '3 character ISO 3166-1 codes.', kDEFAULT_LANGUAGE );
		$ns->Kind( kTYPE_ONTOLOGY, TRUE );
		$term->Type( kTYPE_ENUM );
		$term->Pattern( '[A-Z]{3}', TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );

		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		$root = $node;
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$root->Node()->getId()."]\n" );
		
		//
		// Aruba
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'ABW' );
		$term->Name( "Aruba" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '533', kTYPE_EXACT, TRUE );
		$term->Synonym( 'AW', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Afghanistan
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'AFG' );
		$term->Name( "Afghanistan" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '004', kTYPE_EXACT, TRUE );
		$term->Synonym( 'AF', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// French Afars and Issas
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'AFI' );
		$term->Name( "French Afars and Issas" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( 'AI', kTYPE_EXACT, TRUE );
		$term->Valid( $ns[ kTAG_GID ].kTOKEN_NAMESPACE_SEPARATOR.'DJI' );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Angola
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'AGO' );
		$term->Name( "Angola" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '024', kTYPE_EXACT, TRUE );
		$term->Synonym( 'AO', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Anguilla
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'AIA' );
		$term->Name( "Anguilla" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '660', kTYPE_EXACT, TRUE );
		$term->Synonym( 'AI', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Åland Islands
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'ALA' );
		$term->Name( "Åland Islands" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '248', kTYPE_EXACT, TRUE );
		$term->Synonym( 'AX', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Albania
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'ALB' );
		$term->Name( "Albania" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '008', kTYPE_EXACT, TRUE );
		$term->Synonym( 'AL', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Andorra
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'AND' );
		$term->Name( "Andorra" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '020', kTYPE_EXACT, TRUE );
		$term->Synonym( 'AD', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Netherlands Antilles
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'ANT' );
		$term->Name( "Netherlands Antilles" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '530', kTYPE_EXACT, TRUE );
		$term->Synonym( 'AN', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// United Arab Emirates
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'ARE' );
		$term->Name( "United Arab Emirates" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '784', kTYPE_EXACT, TRUE );
		$term->Synonym( 'AE', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Argentina
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'ARG' );
		$term->Name( "Argentina" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '032', kTYPE_EXACT, TRUE );
		$term->Synonym( 'AR', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Armenia
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'ARM' );
		$term->Name( "Armenia" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '051', kTYPE_EXACT, TRUE );
		$term->Synonym( 'AM', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// American Samoa
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'ASM' );
		$term->Name( "American Samoa" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '016', kTYPE_EXACT, TRUE );
		$term->Synonym( 'AS', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Antarctica
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'ATA' );
		$term->Name( "Antarctica" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '010', kTYPE_EXACT, TRUE );
		$term->Synonym( 'AQ', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// British Antarctic Territory
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'ATB' );
		$term->Name( "British Antarctic Territory" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '080', kTYPE_EXACT, TRUE );
		$term->Synonym( 'BQ', kTYPE_EXACT, TRUE );
		$term->Valid( $ns[ kTAG_GID ].kTOKEN_NAMESPACE_SEPARATOR.'ATA' );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// French Southern Territories
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'ATF' );
		$term->Name( "French Southern Territories" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '260', kTYPE_EXACT, TRUE );
		$term->Synonym( 'TF', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Antigua and Barbuda
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'ATG' );
		$term->Name( "Antigua and Barbuda" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '028', kTYPE_EXACT, TRUE );
		$term->Synonym( 'AG', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Dronning Maud Land
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'ATN' );
		$term->Name( "Dronning Maud Land" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '216', kTYPE_EXACT, TRUE );
		$term->Valid( $ns[ kTAG_GID ].kTOKEN_NAMESPACE_SEPARATOR.'ATA' );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Australia
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'AUS' );
		$term->Name( "Australia" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '036', kTYPE_EXACT, TRUE );
		$term->Synonym( 'AU', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Austria
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'AUT' );
		$term->Name( "Austria" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '040', kTYPE_EXACT, TRUE );
		$term->Synonym( 'AT', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Azerbaijan
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'AZE' );
		$term->Name( "Azerbaijan" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '031', kTYPE_EXACT, TRUE );
		$term->Synonym( 'AZ', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Burundi
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'BDI' );
		$term->Name( "Burundi" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '108', kTYPE_EXACT, TRUE );
		$term->Synonym( 'BI', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Belgium
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'BEL' );
		$term->Name( "Belgium" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '056', kTYPE_EXACT, TRUE );
		$term->Synonym( 'BE', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Benin
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'BEN' );
		$term->Name( "Benin" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '204', kTYPE_EXACT, TRUE );
		$term->Synonym( 'BJ', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Burkina Faso
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'BFA' );
		$term->Name( "Burkina Faso" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '854', kTYPE_EXACT, TRUE );
		$term->Synonym( 'BF', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Bangladesh
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'BGD' );
		$term->Name( "Bangladesh" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '050', kTYPE_EXACT, TRUE );
		$term->Synonym( 'BD', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Bulgaria
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'BGR' );
		$term->Name( "Bulgaria" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '100', kTYPE_EXACT, TRUE );
		$term->Synonym( 'BG', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Bahrain
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'BHR' );
		$term->Name( "Bahrain" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '048', kTYPE_EXACT, TRUE );
		$term->Synonym( 'BH', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Bahamas
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'BHS' );
		$term->Name( "Bahamas" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '044', kTYPE_EXACT, TRUE );
		$term->Synonym( 'BS', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Bosnia and Herzegovina
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'BIH' );
		$term->Name( "Bosnia and Herzegovina" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '070', kTYPE_EXACT, TRUE );
		$term->Synonym( 'BA', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Belarus
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'BLR' );
		$term->Name( "Belarus" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '112', kTYPE_EXACT, TRUE );
		$term->Synonym( 'BY', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Belize
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'BLZ' );
		$term->Name( "Belize" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '084', kTYPE_EXACT, TRUE );
		$term->Synonym( 'BZ', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Bermuda
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'BMU' );
		$term->Name( "Bermuda" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '060', kTYPE_EXACT, TRUE );
		$term->Synonym( 'BM', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Bolivia
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'BOL' );
		$term->Name( "Bolivia" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '068', kTYPE_EXACT, TRUE );
		$term->Synonym( 'BO', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Brazil
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'BRA' );
		$term->Name( "Brazil" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '076', kTYPE_EXACT, TRUE );
		$term->Synonym( 'BR', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Barbados
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'BRB' );
		$term->Name( "Barbados" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '052', kTYPE_EXACT, TRUE );
		$term->Synonym( 'BB', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Brunei Darussalam
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'BRN' );
		$term->Name( "Brunei Darussalam" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '096', kTYPE_EXACT, TRUE );
		$term->Synonym( 'BN', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Bhutan
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'BTN' );
		$term->Name( "Bhutan" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '064', kTYPE_EXACT, TRUE );
		$term->Synonym( 'BT', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Burma
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'BUR' );
		$term->Name( "Burma" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '104', kTYPE_EXACT, TRUE );
		$term->Synonym( 'BU', kTYPE_EXACT, TRUE );
		$term->Valid( $ns[ kTAG_GID ].kTOKEN_NAMESPACE_SEPARATOR.'MMR' );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Bouvet Island
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'BVT' );
		$term->Name( "Bouvet Island" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '074', kTYPE_EXACT, TRUE );
		$term->Synonym( 'BV', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Botswana
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'BWA' );
		$term->Name( "Botswana" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '072', kTYPE_EXACT, TRUE );
		$term->Synonym( 'BW', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Byelorussian SSR
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'BYS' );
		$term->Name( "Byelorussian SSR" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( 'BY', kTYPE_EXACT, TRUE );
		$term->Valid( $ns[ kTAG_GID ].kTOKEN_NAMESPACE_SEPARATOR.'BLR' );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Central African Republic
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'CAF' );
		$term->Name( "Central African Republic" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '140', kTYPE_EXACT, TRUE );
		$term->Synonym( 'CF', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Canada
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'CAN' );
		$term->Name( "Canada" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '124', kTYPE_EXACT, TRUE );
		$term->Synonym( 'CA', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Cocos (Keeling) Islands
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'CCK' );
		$term->Name( "Cocos (Keeling) Islands" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '166', kTYPE_EXACT, TRUE );
		$term->Synonym( 'CC', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Switzerland
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'CHE' );
		$term->Name( "Switzerland" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '756', kTYPE_EXACT, TRUE );
		$term->Synonym( 'CH', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Chile
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'CHL' );
		$term->Name( "Chile" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '152', kTYPE_EXACT, TRUE );
		$term->Synonym( 'CL', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// China
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'CHN' );
		$term->Name( "China" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '156', kTYPE_EXACT, TRUE );
		$term->Synonym( 'CN', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Côte d'Ivoire
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'CIV' );
		$term->Name( "Côte d'Ivoire" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '384', kTYPE_EXACT, TRUE );
		$term->Synonym( 'CI', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Cameroon
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'CMR' );
		$term->Name( "Cameroon" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '120', kTYPE_EXACT, TRUE );
		$term->Synonym( 'CM', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// The Democratic Republic of the Congo
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'COD' );
		$term->Name( "The Democratic Republic of the Congo" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '180', kTYPE_EXACT, TRUE );
		$term->Synonym( 'CD', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Congo
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'COG' );
		$term->Name( "Congo" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '178', kTYPE_EXACT, TRUE );
		$term->Synonym( 'CG', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Cook Islands
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'COK' );
		$term->Name( "Cook Islands" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '184', kTYPE_EXACT, TRUE );
		$term->Synonym( 'CK', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Colombia
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'COL' );
		$term->Name( "Colombia" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '170', kTYPE_EXACT, TRUE );
		$term->Synonym( 'CO', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Comoros
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'COM' );
		$term->Name( "Comoros" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '174', kTYPE_EXACT, TRUE );
		$term->Synonym( 'KM', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Cape Verde
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'CPV' );
		$term->Name( "Cape Verde" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '132', kTYPE_EXACT, TRUE );
		$term->Synonym( 'CV', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Costa Rica
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'CRI' );
		$term->Name( "Costa Rica" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '188', kTYPE_EXACT, TRUE );
		$term->Synonym( 'CR', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Czechoslovakia
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'CSK' );
		$term->Name( "Czechoslovakia" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '200', kTYPE_EXACT, TRUE );
		$term->Synonym( 'CS', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Canton and Enderbury Islands
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'CTE' );
		$term->Name( "Canton and Enderbury Islands" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '128', kTYPE_EXACT, TRUE );
		$term->Synonym( 'CT', kTYPE_EXACT, TRUE );
		$term->Valid( $ns[ kTAG_GID ].kTOKEN_NAMESPACE_SEPARATOR.'KIR' );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Cuba
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'CUB' );
		$term->Name( "Cuba" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '192', kTYPE_EXACT, TRUE );
		$term->Synonym( 'CU', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Christmas Island
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'CXR' );
		$term->Name( "Christmas Island" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '162', kTYPE_EXACT, TRUE );
		$term->Synonym( 'CX', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Cayman Islands
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'CYM' );
		$term->Name( "Cayman Islands" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '136', kTYPE_EXACT, TRUE );
		$term->Synonym( 'KY', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Cyprus
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'CYP' );
		$term->Name( "Cyprus" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '196', kTYPE_EXACT, TRUE );
		$term->Synonym( 'CY', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Czech Republic
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'CZE' );
		$term->Name( "Czech Republic" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '203', kTYPE_EXACT, TRUE );
		$term->Synonym( 'CZ', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// German Democratic Republic (East Germany)
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'DDR' );
		$term->Name( "German Democratic Republic (East Germany)" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '278', kTYPE_EXACT, TRUE );
		$term->Synonym( 'DD', kTYPE_EXACT, TRUE );
		$term->Valid( $ns[ kTAG_GID ].kTOKEN_NAMESPACE_SEPARATOR.'DEU' );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Germany
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'DEU' );
		$term->Name( "Germany" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '276', kTYPE_EXACT, TRUE );
		$term->Synonym( 'DE', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Dahomey
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'DHY' );
		$term->Name( "Dahomey" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( 'DY', kTYPE_EXACT, TRUE );
		$term->Valid( $ns[ kTAG_GID ].kTOKEN_NAMESPACE_SEPARATOR.'BEN' );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Djibouti
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'DJI' );
		$term->Name( "Djibouti" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '262', kTYPE_EXACT, TRUE );
		$term->Synonym( 'DJ', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Dominica
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'DMA' );
		$term->Name( "Dominica" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '212', kTYPE_EXACT, TRUE );
		$term->Synonym( 'DM', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Denmark
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'DNK' );
		$term->Name( "Denmark" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '208', kTYPE_EXACT, TRUE );
		$term->Synonym( 'DK', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Dominican Republic
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'DOM' );
		$term->Name( "Dominican Republic" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '214', kTYPE_EXACT, TRUE );
		$term->Synonym( 'DO', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Algeria
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'DZA' );
		$term->Name( "Algeria" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '012', kTYPE_EXACT, TRUE );
		$term->Synonym( 'DZ', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Ecuador
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'ECU' );
		$term->Name( "Ecuador" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '218', kTYPE_EXACT, TRUE );
		$term->Synonym( 'EC', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Egypt
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'EGY' );
		$term->Name( "Egypt" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '818', kTYPE_EXACT, TRUE );
		$term->Synonym( 'EG', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Eritrea
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'ERI' );
		$term->Name( "Eritrea" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '232', kTYPE_EXACT, TRUE );
		$term->Synonym( 'ER', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Western Sahara
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'ESH' );
		$term->Name( "Western Sahara" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '732', kTYPE_EXACT, TRUE );
		$term->Synonym( 'EH', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Spain
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'ESP' );
		$term->Name( "Spain" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '724', kTYPE_EXACT, TRUE );
		$term->Synonym( 'ES', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Estonia
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'EST' );
		$term->Name( "Estonia" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '233', kTYPE_EXACT, TRUE );
		$term->Synonym( 'EE', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Ethiopia
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'ETH' );
		$term->Name( "Ethiopia" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '231', kTYPE_EXACT, TRUE );
		$term->Synonym( 'ET', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Finland
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'FIN' );
		$term->Name( "Finland" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '246', kTYPE_EXACT, TRUE );
		$term->Synonym( 'FI', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Fiji
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'FJI' );
		$term->Name( "Fiji" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '242', kTYPE_EXACT, TRUE );
		$term->Synonym( 'FJ', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Falkland Islands (Malvinas)
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'FLK' );
		$term->Name( "Falkland Islands (Malvinas)" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '238', kTYPE_EXACT, TRUE );
		$term->Synonym( 'FK', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// France
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'FRA' );
		$term->Name( "France" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '250', kTYPE_EXACT, TRUE );
		$term->Synonym( 'FR', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Faroe Islands
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'FRO' );
		$term->Name( "Faroe Islands" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '234', kTYPE_EXACT, TRUE );
		$term->Synonym( 'FO', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Federated States of Micronesia
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'FSM' );
		$term->Name( "Federated States of Micronesia" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '583', kTYPE_EXACT, TRUE );
		$term->Synonym( 'FM', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Gabon
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'GAB' );
		$term->Name( "Gabon" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '266', kTYPE_EXACT, TRUE );
		$term->Synonym( 'GA', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// United Kingdom
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'GBR' );
		$term->Name( "United Kingdom" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '826', kTYPE_EXACT, TRUE );
		$term->Synonym( 'GB', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Gilbert and Ellice Islands
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'GEL' );
		$term->Name( "Gilbert and Ellice Islands" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( 'GE', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Georgia
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'GEO' );
		$term->Name( "Georgia" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '268', kTYPE_EXACT, TRUE );
		$term->Synonym( 'GE', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Guernsey
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'GGY' );
		$term->Name( "Guernsey" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '831', kTYPE_EXACT, TRUE );
		$term->Synonym( 'GG', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Ghana
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'GHA' );
		$term->Name( "Ghana" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '288', kTYPE_EXACT, TRUE );
		$term->Synonym( 'GH', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Gibraltar
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'GIB' );
		$term->Name( "Gibraltar" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '292', kTYPE_EXACT, TRUE );
		$term->Synonym( 'GI', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Guinea
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'GIN' );
		$term->Name( "Guinea" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '324', kTYPE_EXACT, TRUE );
		$term->Synonym( 'GN', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Guadeloupe
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'GLP' );
		$term->Name( "Guadeloupe" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '312', kTYPE_EXACT, TRUE );
		$term->Synonym( 'GP', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Gambia
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'GMB' );
		$term->Name( "Gambia" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '270', kTYPE_EXACT, TRUE );
		$term->Synonym( 'GM', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Guinea-Bissau
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'GNB' );
		$term->Name( "Guinea-Bissau" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '624', kTYPE_EXACT, TRUE );
		$term->Synonym( 'GW', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Equatorial Guinea
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'GNQ' );
		$term->Name( "Equatorial Guinea" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '226', kTYPE_EXACT, TRUE );
		$term->Synonym( 'GQ', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Greece
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'GRC' );
		$term->Name( "Greece" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '300', kTYPE_EXACT, TRUE );
		$term->Synonym( 'GR', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Grenada
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'GRD' );
		$term->Name( "Grenada" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '308', kTYPE_EXACT, TRUE );
		$term->Synonym( 'GD', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Greenland
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'GRL' );
		$term->Name( "Greenland" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '304', kTYPE_EXACT, TRUE );
		$term->Synonym( 'GL', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Guatemala
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'GTM' );
		$term->Name( "Guatemala" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '320', kTYPE_EXACT, TRUE );
		$term->Synonym( 'GT', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// French Guiana
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'GUF' );
		$term->Name( "French Guiana" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '254', kTYPE_EXACT, TRUE );
		$term->Synonym( 'GF', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Guam
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'GUM' );
		$term->Name( "Guam" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '316', kTYPE_EXACT, TRUE );
		$term->Synonym( 'GU', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Guyana
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'GUY' );
		$term->Name( "Guyana" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '328', kTYPE_EXACT, TRUE );
		$term->Synonym( 'GY', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Hong Kong
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'HKG' );
		$term->Name( "Hong Kong" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '344', kTYPE_EXACT, TRUE );
		$term->Synonym( 'HK', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Heard Island and McDonald Islands
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'HMD' );
		$term->Name( "Heard Island and McDonald Islands" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '334', kTYPE_EXACT, TRUE );
		$term->Synonym( 'HM', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Honduras
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'HND' );
		$term->Name( "Honduras" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '340', kTYPE_EXACT, TRUE );
		$term->Synonym( 'HN', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Croatia
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'HRV' );
		$term->Name( "Croatia" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '191', kTYPE_EXACT, TRUE );
		$term->Synonym( 'HR', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Haiti
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'HTI' );
		$term->Name( "Haiti" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '332', kTYPE_EXACT, TRUE );
		$term->Synonym( 'HT', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Hungary
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'HUN' );
		$term->Name( "Hungary" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '348', kTYPE_EXACT, TRUE );
		$term->Synonym( 'HU', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Upper Volta
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'HVO' );
		$term->Name( "Upper Volta" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( 'HV', kTYPE_EXACT, TRUE );
		$term->Valid( $ns[ kTAG_GID ].kTOKEN_NAMESPACE_SEPARATOR.'BFA' );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Indonesia
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'IDN' );
		$term->Name( "Indonesia" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '360', kTYPE_EXACT, TRUE );
		$term->Synonym( 'ID', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Isle of Man
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'IMN' );
		$term->Name( "Isle of Man" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '833', kTYPE_EXACT, TRUE );
		$term->Synonym( 'IM', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// India
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'IND' );
		$term->Name( "India" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '356', kTYPE_EXACT, TRUE );
		$term->Synonym( 'IN', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// British Indian Ocean Territory
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'IOT' );
		$term->Name( "British Indian Ocean Territory" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '086', kTYPE_EXACT, TRUE );
		$term->Synonym( 'IO', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Ireland
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'IRL' );
		$term->Name( "Ireland" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '372', kTYPE_EXACT, TRUE );
		$term->Synonym( 'IE', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Islamic Republic of Iran
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'IRN' );
		$term->Name( "Islamic Republic of Iran" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '364', kTYPE_EXACT, TRUE );
		$term->Synonym( 'IR', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Iraq
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'IRQ' );
		$term->Name( "Iraq" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '368', kTYPE_EXACT, TRUE );
		$term->Synonym( 'IQ', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Iceland
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'ISL' );
		$term->Name( "Iceland" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '352', kTYPE_EXACT, TRUE );
		$term->Synonym( 'IS', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Israel
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'ISR' );
		$term->Name( "Israel" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '376', kTYPE_EXACT, TRUE );
		$term->Synonym( 'IL', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Italy
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'ITA' );
		$term->Name( "Italy" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '380', kTYPE_EXACT, TRUE );
		$term->Synonym( 'IT', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Jamaica
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'JAM' );
		$term->Name( "Jamaica" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '388', kTYPE_EXACT, TRUE );
		$term->Synonym( 'JM', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Jersey
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'JEY' );
		$term->Name( "Jersey" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '832', kTYPE_EXACT, TRUE );
		$term->Synonym( 'JE', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Jordan
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'JOR' );
		$term->Name( "Jordan" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '400', kTYPE_EXACT, TRUE );
		$term->Synonym( 'JO', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Japan
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'JPN' );
		$term->Name( "Japan" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '392', kTYPE_EXACT, TRUE );
		$term->Synonym( 'JP', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Johnston Island
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'JTN' );
		$term->Name( "Johnston Island" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '396', kTYPE_EXACT, TRUE );
		$term->Synonym( 'JT', kTYPE_EXACT, TRUE );
		$term->Valid( $ns[ kTAG_GID ].kTOKEN_NAMESPACE_SEPARATOR.'UMI' );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Kazakhstan
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'KAZ' );
		$term->Name( "Kazakhstan" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '398', kTYPE_EXACT, TRUE );
		$term->Synonym( 'KZ', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Kenya
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'KEN' );
		$term->Name( "Kenya" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '404', kTYPE_EXACT, TRUE );
		$term->Synonym( 'KE', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Kyrgyzstan
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'KGZ' );
		$term->Name( "Kyrgyzstan" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '417', kTYPE_EXACT, TRUE );
		$term->Synonym( 'KG', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Cambodia
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'KHM' );
		$term->Name( "Cambodia" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '116', kTYPE_EXACT, TRUE );
		$term->Synonym( 'KH', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Kiribati
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'KIR' );
		$term->Name( "Kiribati" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '296', kTYPE_EXACT, TRUE );
		$term->Synonym( 'KI', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Saint Kitts and Nevis
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'KNA' );
		$term->Name( "Saint Kitts and Nevis" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '659', kTYPE_EXACT, TRUE );
		$term->Synonym( 'KN', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Republic of Korea
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'KOR' );
		$term->Name( "Republic of Korea" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '410', kTYPE_EXACT, TRUE );
		$term->Synonym( 'KR', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Kuwait
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'KWT' );
		$term->Name( "Kuwait" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '414', kTYPE_EXACT, TRUE );
		$term->Synonym( 'KW', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Lao People's Democratic Republic
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'LAO' );
		$term->Name( "Lao People's Democratic Republic" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '418', kTYPE_EXACT, TRUE );
		$term->Synonym( 'LA', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Lebanon
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'LBN' );
		$term->Name( "Lebanon" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '422', kTYPE_EXACT, TRUE );
		$term->Synonym( 'LB', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Liberia
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'LBR' );
		$term->Name( "Liberia" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '430', kTYPE_EXACT, TRUE );
		$term->Synonym( 'LR', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Libyan Arab Jamahiriya
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'LBY' );
		$term->Name( "Libyan Arab Jamahiriya" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '434', kTYPE_EXACT, TRUE );
		$term->Synonym( 'LY', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Saint Lucia
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'LCA' );
		$term->Name( "Saint Lucia" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '662', kTYPE_EXACT, TRUE );
		$term->Synonym( 'LC', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Liechtenstein
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'LIE' );
		$term->Name( "Liechtenstein" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '438', kTYPE_EXACT, TRUE );
		$term->Synonym( 'LI', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Sri Lanka
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'LKA' );
		$term->Name( "Sri Lanka" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '144', kTYPE_EXACT, TRUE );
		$term->Synonym( 'LK', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Lesotho
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'LSO' );
		$term->Name( "Lesotho" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '426', kTYPE_EXACT, TRUE );
		$term->Synonym( 'LS', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Lithuania
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'LTU' );
		$term->Name( "Lithuania" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '440', kTYPE_EXACT, TRUE );
		$term->Synonym( 'LT', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Luxembourg
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'LUX' );
		$term->Name( "Luxembourg" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '442', kTYPE_EXACT, TRUE );
		$term->Synonym( 'LU', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Latvia
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'LVA' );
		$term->Name( "Latvia" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '428', kTYPE_EXACT, TRUE );
		$term->Synonym( 'LV', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Macao
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'MAC' );
		$term->Name( "Macao" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '446', kTYPE_EXACT, TRUE );
		$term->Synonym( 'MO', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Morocco
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'MAR' );
		$term->Name( "Morocco" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '504', kTYPE_EXACT, TRUE );
		$term->Synonym( 'MA', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Monaco
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'MCO' );
		$term->Name( "Monaco" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '492', kTYPE_EXACT, TRUE );
		$term->Synonym( 'MC', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Republic of Moldova
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'MDA' );
		$term->Name( "Republic of Moldova" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '498', kTYPE_EXACT, TRUE );
		$term->Synonym( 'MD', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Madagascar
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'MDG' );
		$term->Name( "Madagascar" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '450', kTYPE_EXACT, TRUE );
		$term->Synonym( 'MG', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Maldives
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'MDV' );
		$term->Name( "Maldives" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '462', kTYPE_EXACT, TRUE );
		$term->Synonym( 'MV', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Mexico
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'MEX' );
		$term->Name( "Mexico" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '484', kTYPE_EXACT, TRUE );
		$term->Synonym( 'MX', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Marshall Islands
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'MHL' );
		$term->Name( "Marshall Islands" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '584', kTYPE_EXACT, TRUE );
		$term->Synonym( 'MH', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Midway Islands
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'MID' );
		$term->Name( "Midway Islands" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '488', kTYPE_EXACT, TRUE );
		$term->Synonym( 'MI', kTYPE_EXACT, TRUE );
		$term->Valid( $ns[ kTAG_GID ].kTOKEN_NAMESPACE_SEPARATOR.'UMI' );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// The Former Yugoslav Republic of Macedonia
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'MKD' );
		$term->Name( "The Former Yugoslav Republic of Macedonia" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '807', kTYPE_EXACT, TRUE );
		$term->Synonym( 'MK', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Mali
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'MLI' );
		$term->Name( "Mali" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '466', kTYPE_EXACT, TRUE );
		$term->Synonym( 'ML', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Malta
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'MLT' );
		$term->Name( "Malta" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '470', kTYPE_EXACT, TRUE );
		$term->Synonym( 'MT', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Myanmar
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'MMR' );
		$term->Name( "Myanmar" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '104', kTYPE_EXACT, TRUE );
		$term->Synonym( 'MM', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Mongolia
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'MNG' );
		$term->Name( "Mongolia" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '496', kTYPE_EXACT, TRUE );
		$term->Synonym( 'MN', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Northern Mariana Islands
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'MNP' );
		$term->Name( "Northern Mariana Islands" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '580', kTYPE_EXACT, TRUE );
		$term->Synonym( 'MP', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Mozambique
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'MOZ' );
		$term->Name( "Mozambique" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '508', kTYPE_EXACT, TRUE );
		$term->Synonym( 'MZ', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Mauritania
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'MRT' );
		$term->Name( "Mauritania" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '478', kTYPE_EXACT, TRUE );
		$term->Synonym( 'MR', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Montserrat
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'MSR' );
		$term->Name( "Montserrat" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '500', kTYPE_EXACT, TRUE );
		$term->Synonym( 'MS', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Martinique
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'MTQ' );
		$term->Name( "Martinique" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '474', kTYPE_EXACT, TRUE );
		$term->Synonym( 'MQ', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Mauritius
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'MUS' );
		$term->Name( "Mauritius" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '480', kTYPE_EXACT, TRUE );
		$term->Synonym( 'MU', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Malawi
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'MWI' );
		$term->Name( "Malawi" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '454', kTYPE_EXACT, TRUE );
		$term->Synonym( 'MW', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Malaysia
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'MYS' );
		$term->Name( "Malaysia" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '458', kTYPE_EXACT, TRUE );
		$term->Synonym( 'MY', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Mayotte
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'MYT' );
		$term->Name( "Mayotte" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '175', kTYPE_EXACT, TRUE );
		$term->Synonym( 'YT', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Namibia
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'NAM' );
		$term->Name( "Namibia" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '516', kTYPE_EXACT, TRUE );
		$term->Synonym( 'NA', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// New Caledonia
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'NCL' );
		$term->Name( "New Caledonia" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '540', kTYPE_EXACT, TRUE );
		$term->Synonym( 'NC', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Niger
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'NER' );
		$term->Name( "Niger" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '562', kTYPE_EXACT, TRUE );
		$term->Synonym( 'NE', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Norfolk Island
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'NFK' );
		$term->Name( "Norfolk Island" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '574', kTYPE_EXACT, TRUE );
		$term->Synonym( 'NF', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Nigeria
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'NGA' );
		$term->Name( "Nigeria" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '566', kTYPE_EXACT, TRUE );
		$term->Synonym( 'NG', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// New Hebrides
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'NHB' );
		$term->Name( "New Hebrides" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( 'NH', kTYPE_EXACT, TRUE );
		$term->Valid( $ns[ kTAG_GID ].kTOKEN_NAMESPACE_SEPARATOR.'VUT' );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Nicaragua
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'NIC' );
		$term->Name( "Nicaragua" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '558', kTYPE_EXACT, TRUE );
		$term->Synonym( 'NI', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Niue
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'NIU' );
		$term->Name( "Niue" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '570', kTYPE_EXACT, TRUE );
		$term->Synonym( 'NU', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Netherlands
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'NLD' );
		$term->Name( "Netherlands" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '528', kTYPE_EXACT, TRUE );
		$term->Synonym( 'NL', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Norway
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'NOR' );
		$term->Name( "Norway" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '578', kTYPE_EXACT, TRUE );
		$term->Synonym( 'NO', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Nepal
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'NPL' );
		$term->Name( "Nepal" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '524', kTYPE_EXACT, TRUE );
		$term->Synonym( 'NP', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Nauru
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'NRU' );
		$term->Name( "Nauru" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '520', kTYPE_EXACT, TRUE );
		$term->Synonym( 'NR', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Neutral Zone
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'NTZ' );
		$term->Name( "Neutral Zone" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '536', kTYPE_EXACT, TRUE );
		$term->Synonym( 'NT', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// New Zealand
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'NZL' );
		$term->Name( "New Zealand" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '554', kTYPE_EXACT, TRUE );
		$term->Synonym( 'NZ', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Oman
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'OMN' );
		$term->Name( "Oman" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '512', kTYPE_EXACT, TRUE );
		$term->Synonym( 'OM', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Pakistan
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'PAK' );
		$term->Name( "Pakistan" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '586', kTYPE_EXACT, TRUE );
		$term->Synonym( 'PK', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Panama
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'PAN' );
		$term->Name( "Panama" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '591', kTYPE_EXACT, TRUE );
		$term->Synonym( 'PA', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Trust Territory of the Pacific Islands
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'PCI' );
		$term->Name( "Trust Territory of the Pacific Islands" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '582', kTYPE_EXACT, TRUE );
		$term->Synonym( 'PC', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Pitcairn
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'PCN' );
		$term->Name( "Pitcairn" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '612', kTYPE_EXACT, TRUE );
		$term->Synonym( 'PN', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Panama Canal Zone
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'PCZ' );
		$term->Name( "Panama Canal Zone" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '594', kTYPE_EXACT, TRUE );
		$term->Synonym( 'PZ', kTYPE_EXACT, TRUE );
		$term->Valid( $ns[ kTAG_GID ].kTOKEN_NAMESPACE_SEPARATOR.'PAN' );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Peru
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'PER' );
		$term->Name( "Peru" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '604', kTYPE_EXACT, TRUE );
		$term->Synonym( 'PE', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Philippines
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'PHL' );
		$term->Name( "Philippines" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '608', kTYPE_EXACT, TRUE );
		$term->Synonym( 'PH', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Palau
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'PLW' );
		$term->Name( "Palau" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '585', kTYPE_EXACT, TRUE );
		$term->Synonym( 'PW', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Papua New Guinea
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'PNG' );
		$term->Name( "Papua New Guinea" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '598', kTYPE_EXACT, TRUE );
		$term->Synonym( 'PG', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Poland
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'POL' );
		$term->Name( "Poland" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '616', kTYPE_EXACT, TRUE );
		$term->Synonym( 'PL', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Puerto Rico
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'PRI' );
		$term->Name( "Puerto Rico" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '630', kTYPE_EXACT, TRUE );
		$term->Synonym( 'PR', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Democratic People's Republic of Korea
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'PRK' );
		$term->Name( "Democratic People's Republic of Korea" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '408', kTYPE_EXACT, TRUE );
		$term->Synonym( 'KP', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Portugal
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'PRT' );
		$term->Name( "Portugal" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '620', kTYPE_EXACT, TRUE );
		$term->Synonym( 'PT', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Paraguay
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'PRY' );
		$term->Name( "Paraguay" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '600', kTYPE_EXACT, TRUE );
		$term->Synonym( 'PY', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Palestinian Territory, Occupied
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'PSE' );
		$term->Name( "Palestinian Territory, Occupied" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '275', kTYPE_EXACT, TRUE );
		$term->Synonym( 'PS', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// U.S. Miscellaneous Pacific Islands
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'PUS' );
		$term->Name( "U.S. Miscellaneous Pacific Islands" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '849', kTYPE_EXACT, TRUE );
		$term->Synonym( 'PU', kTYPE_EXACT, TRUE );
		$term->Valid( $ns[ kTAG_GID ].kTOKEN_NAMESPACE_SEPARATOR.'UMI' );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// French Polynesia
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'PYF' );
		$term->Name( "French Polynesia" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '258', kTYPE_EXACT, TRUE );
		$term->Synonym( 'PF', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Qatar
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'QAT' );
		$term->Name( "Qatar" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '634', kTYPE_EXACT, TRUE );
		$term->Synonym( 'QA', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Réunion
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'REU' );
		$term->Name( "Réunion" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '638', kTYPE_EXACT, TRUE );
		$term->Synonym( 'RE', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Southern Rhodesia
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'RHO' );
		$term->Name( "Southern Rhodesia" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( 'RH', kTYPE_EXACT, TRUE );
		$term->Valid( $ns[ kTAG_GID ].kTOKEN_NAMESPACE_SEPARATOR.'ZWE' );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Romania
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'ROM' );
		$term->Name( "Romania" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '642', kTYPE_EXACT, TRUE );
		$term->Synonym( 'RO', kTYPE_EXACT, TRUE );
		$term->Valid( $ns[ kTAG_GID ].kTOKEN_NAMESPACE_SEPARATOR.'ROU' );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Romania
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'ROU' );
		$term->Name( "Romania" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '642', kTYPE_EXACT, TRUE );
		$term->Synonym( 'RO', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Russian Federation
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'RUS' );
		$term->Name( "Russian Federation" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '643', kTYPE_EXACT, TRUE );
		$term->Synonym( 'RU', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Rwanda
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'RWA' );
		$term->Name( "Rwanda" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '646', kTYPE_EXACT, TRUE );
		$term->Synonym( 'RW', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Saudi Arabia
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'SAU' );
		$term->Name( "Saudi Arabia" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '682', kTYPE_EXACT, TRUE );
		$term->Synonym( 'SA', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Serbia and Montenegro (Federal Republic of Yugoslavia)
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'SCG' );
		$term->Name( "Serbia and Montenegro (Federal Republic of Yugoslavia)" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '891', kTYPE_EXACT, TRUE );
		$term->Synonym( 'CS', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Sudan
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'SDN' );
		$term->Name( "Sudan" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '736', kTYPE_EXACT, TRUE );
		$term->Synonym( 'SD', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Senegal
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'SEN' );
		$term->Name( "Senegal" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '686', kTYPE_EXACT, TRUE );
		$term->Synonym( 'SN', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Singapore
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'SGP' );
		$term->Name( "Singapore" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '702', kTYPE_EXACT, TRUE );
		$term->Synonym( 'SG', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// South Georgia and the South Sandwich Islands
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'SGS' );
		$term->Name( "South Georgia and the South Sandwich Islands" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '239', kTYPE_EXACT, TRUE );
		$term->Synonym( 'GS', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Saint Helena
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'SHN' );
		$term->Name( "Saint Helena" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '654', kTYPE_EXACT, TRUE );
		$term->Synonym( 'SH', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Svalbard and Jan Mayen
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'SJM' );
		$term->Name( "Svalbard and Jan Mayen" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '744', kTYPE_EXACT, TRUE );
		$term->Synonym( 'SJ', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Sikkim
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'SKM' );
		$term->Name( "Sikkim" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '698', kTYPE_EXACT, TRUE );
		$term->Synonym( 'SK', kTYPE_EXACT, TRUE );
		$term->Valid( $ns[ kTAG_GID ].kTOKEN_NAMESPACE_SEPARATOR.'IND' );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Solomon Islands
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'SLB' );
		$term->Name( "Solomon Islands" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '090', kTYPE_EXACT, TRUE );
		$term->Synonym( 'SB', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Sierra Leone
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'SLE' );
		$term->Name( "Sierra Leone" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '694', kTYPE_EXACT, TRUE );
		$term->Synonym( 'SL', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// El Salvador
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'SLV' );
		$term->Name( "El Salvador" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '222', kTYPE_EXACT, TRUE );
		$term->Synonym( 'SV', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// San Marino
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'SMR' );
		$term->Name( "San Marino" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '674', kTYPE_EXACT, TRUE );
		$term->Synonym( 'SM', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Somalia
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'SOM' );
		$term->Name( "Somalia" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '706', kTYPE_EXACT, TRUE );
		$term->Synonym( 'SO', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Saint Pierre and Miquelon
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'SPM' );
		$term->Name( "Saint Pierre and Miquelon" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '666', kTYPE_EXACT, TRUE );
		$term->Synonym( 'PM', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Sao Tome and Principe
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'STP' );
		$term->Name( "Sao Tome and Principe" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '678', kTYPE_EXACT, TRUE );
		$term->Synonym( 'ST', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Union of Soviet Socialist Republics
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'SUN' );
		$term->Name( "Union of Soviet Socialist Republics" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '810', kTYPE_EXACT, TRUE );
		$term->Synonym( 'SU', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Suriname
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'SUR' );
		$term->Name( "Suriname" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '740', kTYPE_EXACT, TRUE );
		$term->Synonym( 'SR', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Slovakia
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'SVK' );
		$term->Name( "Slovakia" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '703', kTYPE_EXACT, TRUE );
		$term->Synonym( 'SK', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Slovenia
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'SVN' );
		$term->Name( "Slovenia" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '705', kTYPE_EXACT, TRUE );
		$term->Synonym( 'SI', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Sweden
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'SWE' );
		$term->Name( "Sweden" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '752', kTYPE_EXACT, TRUE );
		$term->Synonym( 'SE', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Swaziland
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'SWZ' );
		$term->Name( "Swaziland" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '748', kTYPE_EXACT, TRUE );
		$term->Synonym( 'SZ', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Seychelles
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'SYC' );
		$term->Name( "Seychelles" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '690', kTYPE_EXACT, TRUE );
		$term->Synonym( 'SC', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Syrian Arab Republic
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'SYR' );
		$term->Name( "Syrian Arab Republic" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '760', kTYPE_EXACT, TRUE );
		$term->Synonym( 'SY', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Turks and Caicos Islands
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'TCA' );
		$term->Name( "Turks and Caicos Islands" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '796', kTYPE_EXACT, TRUE );
		$term->Synonym( 'TC', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Chad
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'TCD' );
		$term->Name( "Chad" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '148', kTYPE_EXACT, TRUE );
		$term->Synonym( 'TD', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Togo
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'TGO' );
		$term->Name( "Togo" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '768', kTYPE_EXACT, TRUE );
		$term->Synonym( 'TG', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Thailand
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'THA' );
		$term->Name( "Thailand" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '764', kTYPE_EXACT, TRUE );
		$term->Synonym( 'TH', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Tajikistan
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'TJK' );
		$term->Name( "Tajikistan" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '762', kTYPE_EXACT, TRUE );
		$term->Synonym( 'TJ', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Tokelau
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'TKL' );
		$term->Name( "Tokelau" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '772', kTYPE_EXACT, TRUE );
		$term->Synonym( 'TK', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Turkmenistan
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'TKM' );
		$term->Name( "Turkmenistan" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '795', kTYPE_EXACT, TRUE );
		$term->Synonym( 'TM', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Timor-Leste
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'TLS' );
		$term->Name( "Timor-Leste" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '626', kTYPE_EXACT, TRUE );
		$term->Synonym( 'TL', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// East Timor
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'TMP' );
		$term->Name( "East Timor" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '626', kTYPE_EXACT, TRUE );
		$term->Valid( $ns[ kTAG_GID ].kTOKEN_NAMESPACE_SEPARATOR.'TLS' );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Tonga
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'TON' );
		$term->Name( "Tonga" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '776', kTYPE_EXACT, TRUE );
		$term->Synonym( 'TO', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Trinidad and Tobago
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'TTO' );
		$term->Name( "Trinidad and Tobago" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '780', kTYPE_EXACT, TRUE );
		$term->Synonym( 'TT', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Tunisia
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'TUN' );
		$term->Name( "Tunisia" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '788', kTYPE_EXACT, TRUE );
		$term->Synonym( 'TN', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Turkey
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'TUR' );
		$term->Name( "Turkey" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '792', kTYPE_EXACT, TRUE );
		$term->Synonym( 'TR', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Tuvalu
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'TUV' );
		$term->Name( "Tuvalu" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '798', kTYPE_EXACT, TRUE );
		$term->Synonym( 'TV', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Taiwan, Province of China
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'TWN' );
		$term->Name( "Taiwan, Province of China" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '158', kTYPE_EXACT, TRUE );
		$term->Synonym( 'TW', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// United Republic of Tanzania
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'TZA' );
		$term->Name( "United Republic of Tanzania" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '834', kTYPE_EXACT, TRUE );
		$term->Synonym( 'TZ', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Uganda
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'UGA' );
		$term->Name( "Uganda" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '800', kTYPE_EXACT, TRUE );
		$term->Synonym( 'UG', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Ukraine
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'UKR' );
		$term->Name( "Ukraine" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '804', kTYPE_EXACT, TRUE );
		$term->Synonym( 'UA', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// United States Minor Outlying Islands
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'UMI' );
		$term->Name( "United States Minor Outlying Islands" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '581', kTYPE_EXACT, TRUE );
		$term->Synonym( 'UM', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Uruguay
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'URY' );
		$term->Name( "Uruguay" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '858', kTYPE_EXACT, TRUE );
		$term->Synonym( 'UY', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// United States
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'USA' );
		$term->Name( "United States" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '840', kTYPE_EXACT, TRUE );
		$term->Synonym( 'US', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Uzbekistan
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'UZB' );
		$term->Name( "Uzbekistan" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '860', kTYPE_EXACT, TRUE );
		$term->Synonym( 'UZ', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Holy See (Vatican City State)
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'VAT' );
		$term->Name( "Holy See (Vatican City State)" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '336', kTYPE_EXACT, TRUE );
		$term->Synonym( 'VA', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Saint Vincent and the Grenadines
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'VCT' );
		$term->Name( "Saint Vincent and the Grenadines" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '670', kTYPE_EXACT, TRUE );
		$term->Synonym( 'VC', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Democratic Republic of Viet-Nam (North Viet-Nam)
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'VDR' );
		$term->Name( "Democratic Republic of Viet-Nam (North Viet-Nam)" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( 'VD', kTYPE_EXACT, TRUE );
		$term->Valid( $ns[ kTAG_GID ].kTOKEN_NAMESPACE_SEPARATOR.'VNM' );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Venezuela
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'VEN' );
		$term->Name( "Venezuela" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '862', kTYPE_EXACT, TRUE );
		$term->Synonym( 'VE', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Virgin Islands, British
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'VGB' );
		$term->Name( "Virgin Islands, British" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '092', kTYPE_EXACT, TRUE );
		$term->Synonym( 'VG', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Virgin Islands, U.S.
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'VIR' );
		$term->Name( "Virgin Islands, U.S." );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '850', kTYPE_EXACT, TRUE );
		$term->Synonym( 'VI', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Viet Nam
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'VNM' );
		$term->Name( "Viet Nam" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '704', kTYPE_EXACT, TRUE );
		$term->Synonym( 'VN', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Vanuatu
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'VUT' );
		$term->Name( "Vanuatu" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '548', kTYPE_EXACT, TRUE );
		$term->Synonym( 'VU', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Wake Island
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'WAK' );
		$term->Name( "Wake Island" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '872', kTYPE_EXACT, TRUE );
		$term->Synonym( 'WK', kTYPE_EXACT, TRUE );
		$term->Valid( $ns[ kTAG_GID ].kTOKEN_NAMESPACE_SEPARATOR.'UMI' );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Wallis and Futuna
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'WLF' );
		$term->Name( "Wallis and Futuna" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '876', kTYPE_EXACT, TRUE );
		$term->Synonym( 'WF', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Samoa
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'WSM' );
		$term->Name( "Samoa" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '882', kTYPE_EXACT, TRUE );
		$term->Synonym( 'WS', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Yemen
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'YEM' );
		$term->Name( "Yemen" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '887', kTYPE_EXACT, TRUE );
		$term->Synonym( 'YE', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Democratic Yemen (South Yemen)
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'YMD' );
		$term->Name( "Democratic Yemen (South Yemen)" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '720', kTYPE_EXACT, TRUE );
		$term->Synonym( 'YD', kTYPE_EXACT, TRUE );
		$term->Valid( $ns[ kTAG_GID ].kTOKEN_NAMESPACE_SEPARATOR.'YEM' );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Socialist Federal Republic of Yugoslavia
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'YUG' );
		$term->Name( "Socialist Federal Republic of Yugoslavia" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '890', kTYPE_EXACT, TRUE );
		$term->Synonym( 'YU', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// South Africa
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'ZAF' );
		$term->Name( "South Africa" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '710', kTYPE_EXACT, TRUE );
		$term->Synonym( 'ZA', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Zaire
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'ZAR' );
		$term->Name( "Zaire" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( 'ZR', kTYPE_EXACT, TRUE );
		$term->Valid( $ns[ kTAG_GID ].kTOKEN_NAMESPACE_SEPARATOR.'COD' );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Zambia
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'ZMB' );
		$term->Name( "Zambia" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '894', kTYPE_EXACT, TRUE );
		$term->Synonym( 'ZM', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Zimbabwe
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'ZWE' );
		$term->Name( "Zimbabwe" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '716', kTYPE_EXACT, TRUE );
		$term->Synonym( 'ZW', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Montenegro
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'MNE' );
		$term->Name( "Montenegro" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '499', kTYPE_EXACT, TRUE );
		$term->Synonym( 'ME', kTYPE_EXACT, TRUE );
		$term->Valid( $ns[ kTAG_GID ].kTOKEN_NAMESPACE_SEPARATOR.'SCG' );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Serbia
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'SRB' );
		$term->Name( "Serbia" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '688', kTYPE_EXACT, TRUE );
		$term->Synonym( 'RS', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Daghestan
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'DGH' );
		$term->Name( "Daghestan" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Saint-Barthélemy
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'BLM' );
		$term->Name( "Saint-Barthélemy" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '652', kTYPE_EXACT, TRUE );
		$term->Synonym( 'BL', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Saint Martin
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'MAF' );
		$term->Name( "Saint Martin" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '663', kTYPE_EXACT, TRUE );
		$term->Synonym( 'MF', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Metropolitan France
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'FXX' );
		$term->Name( "Metropolitan France" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '249', kTYPE_EXACT, TRUE );
		$term->Synonym( 'FX', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Ethiopia (before Eritrea broke away)
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'ETH' );
		$term->Name( "Ethiopia (before Eritrea broke away)" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '230', kTYPE_EXACT, TRUE );
		$term->Synonym( 'ET', kTYPE_EXACT, TRUE );
		$term->Valid( $ns[ kTAG_GID ].kTOKEN_NAMESPACE_SEPARATOR.'ETH' );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Federal Republic of Germany (West Germany)
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'DEU' );
		$term->Name( "Federal Republic of Germany (West Germany)" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '280', kTYPE_EXACT, TRUE );
		$term->Synonym( 'DE', kTYPE_EXACT, TRUE );
		$term->Valid( $ns[ kTAG_GID ].kTOKEN_NAMESPACE_SEPARATOR.'DEU' );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Netherlands Antilles (before Aruba separation)
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'ANT' );
		$term->Name( "Netherlands Antilles (before Aruba separation)" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '552', kTYPE_EXACT, TRUE );
		$term->Synonym( 'AN', kTYPE_EXACT, TRUE );
		$term->Valid( $ns[ kTAG_GID ].kTOKEN_NAMESPACE_SEPARATOR.'ANT' );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Panama (before Canal Zone)
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'PAN' );
		$term->Name( "Panama (before Canal Zone)" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '590', kTYPE_EXACT, TRUE );
		$term->Synonym( 'PA', kTYPE_EXACT, TRUE );
		$term->Valid( $ns[ kTAG_GID ].kTOKEN_NAMESPACE_SEPARATOR.'PAN' );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Saint Kitts-Nevis-Anguilla
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'KNA' );
		$term->Name( "Saint Kitts-Nevis-Anguilla" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '658', kTYPE_EXACT, TRUE );
		$term->Synonym( 'KN', kTYPE_EXACT, TRUE );
		$term->Valid( $ns[ kTAG_GID ].kTOKEN_NAMESPACE_SEPARATOR.'KNA' );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Republic of Viet-Nam (South Vietnam)
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'VNM' );
		$term->Name( "Republic of Viet-Nam (South Vietnam)" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '714', kTYPE_EXACT, TRUE );
		$term->Synonym( 'VN', kTYPE_EXACT, TRUE );
		$term->Valid( $ns[ kTAG_GID ].kTOKEN_NAMESPACE_SEPARATOR.'VNM' );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Yemen Arab Republic (North Yemen)
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'YEM' );
		$term->Name( "Yemen Arab Republic (North Yemen)" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( '886', kTYPE_EXACT, TRUE );
		$term->Synonym( 'YE', kTYPE_EXACT, TRUE );
		$term->Valid( $ns[ kTAG_GID ].kTOKEN_NAMESPACE_SEPARATOR.'YEM' );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Curaçao
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'CUW' );
		$term->Name( "Curaçao" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Synonym( 'CW', kTYPE_EXACT, TRUE );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Kossovo
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'KSV' );
		$term->Name( "Kossovo" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Valid( $ns[ kTAG_GID ].kTOKEN_NAMESPACE_SEPARATOR.'SCG' );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );

		//
		// Sint Maarten (Dutch part)
		//
		$term = new COntologyTerm();
		$term->NS( $ns );
		$term->Code( 'SXM' );
		$term->Name( "Sint Maarten (Dutch part)" );
		$term->Type( kTYPE_ENUM );
		$term->Synonym( $term->Code(), kTYPE_EXACT, TRUE );
		$term->Valid( $ns[ kTAG_GID ].kTOKEN_NAMESPACE_SEPARATOR.'MAF' );
		$term->Relate( $ns, $pred, TRUE );
		$term->Commit( $theContainer );
		$node = $term_index->findOne( kTAG_TERM, (string) $term );
		if( $node === NULL )
		{
			$node = new COntologyNode( $container );
			$node->Term( $term );
			$node->Commit( $container );
		}
		else
			$node = new COntologyNode( $container, $node );
		$id = Array();
		$id[] = $root->Node()->getId();
		$id[] = (string) $pred;
		$id[] = $node->Node()->getId();
		$id = implode( '/', $id );
		$edge = $node_index->findOne( kTAG_EDGE_NODE, $id );
		if( $edge === NULL )
		{
			$edge = $node->RelateTo( $container, $pred, $root );
			$edge->Commit( $container );
		}
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )
				 ." [$term] [".$node->Node()->getId()."]\n" );
		
	} // LoadISO3166.


?>
