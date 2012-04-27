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
require_once( "/Library/WebServer/Library/wrapper/environment.inc.php" );

/**
 * Session.
 *
 * This include file contains the session tag definitions.
 */
require_once( kPATH_LIBRARY_DEFINES."Session.inc.php" );

/**
 * Measures.
 *
 * This include file contains the measure term class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."COntologyTerm.php" );

/**
 * Enumerations.
 *
 * This include file contains the ontology term class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."COntologyTerm.php" );

		

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
	Connect( kDEFAULT_DATABASE, kDEFAULT_DICTIONARY, TRUE );
	
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
		$term->Synonym( 'kPRED_IS_A', kTYPE_EXACT );
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
		$term->Synonym( 'kPRED_PART_OF', kTYPE_EXACT );
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
		$term->Synonym( 'kPRED_SCALE_OF', kTYPE_EXACT );
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
		$term->Synonym( 'kPRED_METHOD_OF', kTYPE_EXACT );
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
		$term->Synonym( 'kPRED_ENUM_OF', kTYPE_EXACT );
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
		$term->Synonym( 'kTYPE_STRING', kTYPE_EXACT );
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
		$term->Synonym( 'kTYPE_INT32', kTYPE_EXACT );
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
		$term->Synonym( 'kTYPE_INT64', kTYPE_EXACT );
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
		$term->Synonym( 'kTYPE_FLOAT', kTYPE_EXACT );
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
		$term->Synonym( 'kTYPE_BOOLEAN', kTYPE_EXACT );
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
		$term->Synonym( 'kTYPE_DATE', kTYPE_EXACT );
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
		$term->Synonym( 'kTYPE_TIME', kTYPE_EXACT );
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
		$term->Synonym( 'kTYPE_REGEX', kTYPE_EXACT );
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
		$term->Synonym( 'kTYPE_STAMP_SEC', kTYPE_EXACT );
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
		$term->Synonym( 'kTYPE_STAMP_USEC', kTYPE_EXACT );
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
		$term->Synonym( 'kTYPE_BINARY_STRING', kTYPE_EXACT );
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
		$term->Synonym( 'kTYPE_BINARY_TYPE', kTYPE_EXACT );
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
		$term->Synonym( 'kTYPE_BINARY', kTYPE_EXACT );
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
		$term->Synonym( 'kTYPE_STAMP', kTYPE_EXACT );
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
		$term->Synonym( 'kTYPE_ENUM', kTYPE_EXACT );
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
		$term->Synonym( 'kTYPE_ENUM_SET', kTYPE_EXACT );
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
		$term->Synonym( 'kTYPE_PHP', kTYPE_EXACT );
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
		$term->Synonym( 'kTYPE_JSON', kTYPE_EXACT );
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
		$term->Synonym( 'kTYPE_XML', kTYPE_EXACT );
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
		$term->Synonym( 'kTYPE_HTML', kTYPE_EXACT );
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
		$term->Synonym( 'kTYPE_CSV', kTYPE_EXACT );
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
		$term->Synonym( 'kTYPE_META', kTYPE_EXACT );
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
		$term->Synonym( 'kTYPE_MongoId', kTYPE_EXACT );
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
		$term->Synonym( 'kTYPE_MongoCode', kTYPE_EXACT );
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
		$term->Synonym( 'kTYPE_EXACT', kTYPE_EXACT );
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
		$term->Synonym( 'kTYPE_BROAD', kTYPE_EXACT );
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
		$term->Synonym( 'kTYPE_NARROW', kTYPE_EXACT );
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
		$term->Synonym( 'kTYPE_RELATED', kTYPE_EXACT );
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
		$term->Synonym( 'kTYPE_TERM', kTYPE_EXACT );
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
		$term->Synonym( 'kTYPE_NAMESPACE', kTYPE_EXACT );
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
		$term->Synonym( 'kTYPE_ONTOLOGY', kTYPE_EXACT );
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
		$term->Synonym( 'kTYPE_PREDICATE', kTYPE_EXACT );
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
		$term->Synonym( 'kTYPE_ATTRIBUTE', kTYPE_EXACT );
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
		$term->Synonym( 'kTYPE_MEASURE', kTYPE_EXACT );
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
		$term->Synonym( 'kTYPE_ENUMERATION', kTYPE_EXACT );
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
		$term->Synonym( 'kCARD_0_1', kTYPE_EXACT );
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
		$term->Synonym( 'kCARD_1', kTYPE_EXACT );
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
		$term->Synonym( 'kCARD_ANY', kTYPE_EXACT );
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
		$term->Synonym( 'kTAG_LID', kTYPE_EXACT );
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
		$term->Synonym( 'kTAG_GID', kTYPE_EXACT );
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
		$term->Synonym( 'kTAG_REFERENCE_SYNONYM', kTYPE_EXACT );
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
		$term->Synonym( 'kTAG_REFERENCE_XREF', kTYPE_EXACT );
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
		$term->Synonym( 'kTAG_REFERENCE_ID', kTYPE_EXACT );
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
		$term->Synonym( 'kTAG_REFERENCE_CONTAINER', kTYPE_EXACT );
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
		$term->Synonym( 'kTAG_REFERENCE_DATABASE', kTYPE_EXACT );
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
		$term->Synonym( 'kTAG_CLASS', kTYPE_EXACT );
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
		$term->Synonym( 'kTAG_CREATED', kTYPE_EXACT );
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
		$term->Synonym( 'kTAG_MODIFIED', kTYPE_EXACT );
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
		$term->Name( 'Version', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term represents a version value.',
		  kDEFAULT_LANGUAGE );
		$term->Type( kTYPE_INT32 );
		$term->Cardinality( kCARD_0_1 );
		$term->Synonym( 'kTAG_VERSION', kTYPE_EXACT );
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
		$term->Synonym( 'kTAG_TYPE', kTYPE_EXACT );
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
		$term->Synonym( 'kTAG_KIND', kTYPE_EXACT );
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
		$term->Synonym( 'kTAG_CARDINALITY', kTYPE_EXACT );
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
		$term->Synonym( 'kTAG_UNIT', kTYPE_EXACT );
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
		$term->Synonym( 'kTAG_SOURCE', kTYPE_EXACT );
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
		$term->Synonym( 'kTAG_DATA', kTYPE_EXACT );
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
		$term->Synonym( 'kTAG_CODE', kTYPE_EXACT );
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
		$term->Synonym( 'kTAG_ENUM', kTYPE_EXACT );
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
		$term->Name( 'Namespace', kDEFAULT_LANGUAGE );
		$term->Definition
		( 'This term is used to indicate a namespace.',
		  kDEFAULT_LANGUAGE );
		$term->Type( kTYPE_STRING );
		$term->Cardinality( kCARD_1 );
		$term->Synonym( 'kTAG_NAMESPACE', kTYPE_EXACT );
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
		$term->Synonym( 'kTAG_NODE', kTYPE_EXACT );
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
		$term->Synonym( 'kTAG_EDGE', kTYPE_EXACT );
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
		$term->Synonym( 'kTAG_TERM', kTYPE_EXACT );
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
		$term->Synonym( 'kTAG_NAME', kTYPE_EXACT );
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
		$term->Synonym( 'kTAG_DESCRIPTION', kTYPE_EXACT );
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
		$term->Synonym( 'kTAG_DEFINITION', kTYPE_EXACT );
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
		$term->Synonym( 'kTAG_EXAMPLES', kTYPE_EXACT );
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
		$term->Synonym( 'kTAG_LANGUAGE', kTYPE_EXACT );
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
		$term->Synonym( 'kTAG_STATUS', kTYPE_EXACT );
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
		$term->Synonym( 'kTAG_ANNOTATION', kTYPE_EXACT );
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
		$term->Synonym( 'kTAG_REFS', kTYPE_EXACT );
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
		$term->Synonym( 'kTAG_EDGE_TERM', kTYPE_EXACT );
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
		$term->Synonym( 'kTAG_EDGE_NODE', kTYPE_EXACT );
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
		$term->Synonym( 'kTAG_VALID', kTYPE_EXACT );
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
		$term->Synonym( 'kOFFSET_PASSWORD', kTYPE_EXACT );
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
		$term->Synonym( 'kOFFSET_MAIL', kTYPE_EXACT );
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
		$term->Synonym( 'kOFFSET_EMAIL', kTYPE_EXACT );
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
		$term->Synonym( 'kOFFSET_PHONE', kTYPE_EXACT );
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
		$term->Synonym( 'kOFFSET_FAX', kTYPE_EXACT );
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
		$term->Synonym( 'kOFFSET_URL', kTYPE_EXACT );
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
		$term->Synonym( 'kOFFSET_ACRONYM', kTYPE_EXACT );
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
		$term->Synonym( 'kOFFSET_PLACE', kTYPE_EXACT );
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
		$term->Synonym( 'kOFFSET_CARE', kTYPE_EXACT );
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
		$term->Synonym( 'kOFFSET_STREET', kTYPE_EXACT );
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
		$term->Synonym( 'kOFFSET_ZIP_CODE', kTYPE_EXACT );
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
		$term->Synonym( 'kOFFSET_CITY', kTYPE_EXACT );
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
		$term->Synonym( 'kOFFSET_PROVINCE', kTYPE_EXACT );
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
		$term->Synonym( 'kOFFSET_COUNTRY', kTYPE_EXACT );
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
		$term->Synonym( 'kOFFSET_FULL', kTYPE_EXACT );
		$term->Commit( $theContainer );
		if( $doDisplay )
			echo( $term->Name( NULL, kDEFAULT_LANGUAGE )." [$term]\n" );
	
	} // LoadMailProperties.


?>
