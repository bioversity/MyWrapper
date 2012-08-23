<?php

/*=======================================================================================
 *																						*
 *									includes.inc.php									*
 *																						*
 *======================================================================================*/
 
/**
 *	User include file.
 *
 *	This file should be included at the top level of the application or web site as the
 *	first entry, it includes the file paths to the relevant directories and the autoload
 *	function for this library classes.
 *
 *	@package	MyWrapper
 *	@subpackage	Definitions
 *
 *	@author		Milko A. Skofic <m.skofic@cgiar.org>
 *	@version	1.00 02/02/2012
 */

/*=======================================================================================
 *	MYWRAPPER LIBRARY PATHS																*
 *======================================================================================*/

/**
 * Class library root.
 *
 * This value defines the <b><i>absolute</i></b> path to the class library root
 * directory.
 */
define( "kPATH_LIBRARY_ROOT",		"/Library/WebServer/Library/wrapper/" );

/**
 * Class library definitions.
 *
 * This value defines the <b><i>absolute</i></b> path to the class library definitions
 * directory.
 */
define( "kPATH_LIBRARY_DEFINES",	"/Library/WebServer/Library/wrapper/defines/" );

/**
 * Class library traits.
 *
 * This value defines the <b><i>absolute</i></b> path to the class library traits
 * directory.
 */
define( "kPATH_LIBRARY_TRAITS",		"/Library/WebServer/Library/wrapper/traits/" );

/**
 * Class library sources.
 *
 * This value defines the <b><i>absolute</i></b> path to the class library sources
 * directory.
 */
define( "kPATH_LIBRARY_SOURCE",		"/Library/WebServer/Library/wrapper/classes/" );

/**
 * Batch library definitions.
 *
 * This value defines the <b><i>absolute</i></b> path to the batch library definitions
 * directory.
 */
define( "kPATH_LIBRARY_BATCH",		"/Library/WebServer/Library/wrapper/batch/" );

/*=======================================================================================
 *	ADODB LIBRARY PATHS																	*
 *======================================================================================*/

/**
 * ADODB library root.
 *
 * This value defines the <b><i>absolute</i></b> path to the ADODB library directory.
 */
define( "kPATH_LIB_ADODB",			"/Library/WebServer/Library/adodb/" );

/*=======================================================================================
 *	NEO4J LIBRARY PATHS																	*
 *======================================================================================*/

/**
 * Neo4j library root.
 *
 * This value defines the <b><i>absolute</i></b> path to the Neo4j library directory.
 */
define( "kPATH_LIBRARY_NEO4J",		"/Library/WebServer/Library/Neo4jphp/" );

/*=======================================================================================
 *	DEFAULT DEFINITIONS																	*
 *======================================================================================*/

/**
 * Default language code.
 *
 * This value defines the default ISO 639 2 character language code.
 */
define( "kDEFAULT_LANGUAGE",			'en' );				// English.

/**
 * Default page limits.
 *
 * This value defines the default page limits for queries in which this was not provided.
 */
define( "kDEFAULT_LIMITS",				'50' );				// 50 by default.

/**
 * Default temporary member name.
 *
 * This value is used whenever there is the necessity to create a temporary data member in
 * an object, the value represents the member name.
 */
define( "kDEFAULT_MEMBER",				'___TMP___' );		// Temporary data member name.

/**
 * Default session offset.
 *
 * This value is used as the default offset in which the current session
 * {@link CSession object} will be stored.
 */
define( "kDEFAULT_SESSION",				'SESSION' );		// Session object offset.

/**
 * Default tags container name.
 *
 * This value defines the default tags container name.
 */
define( "kDEFAULT_CNT_TAGS",			"TAGS" );

/**
 * Default terms container name.
 *
 * This value defines the default terms container name.
 */
define( "kDEFAULT_CNT_TERMS",			"TERMS" );

/**
 * Default nodes container name.
 *
 * This value defines the default nodes container name.
 */
define( "kDEFAULT_CNT_NODES",			"NODES" );

/**
 * Default edges container name.
 *
 * This value defines the default edges container name.
 */
define( "kDEFAULT_CNT_EDGES",			"EDGES" );

/**
 * Default datasets container name.
 *
 * This value defines the default datasets container name.
 */
define( "kDEFAULT_CNT_DATASET",			"DATASETS" );

/**
 * Default users container name.
 *
 * This value defines the default users container name.
 */
define( "kDEFAULT_CNT_USERS",			"USERS" );

/*=======================================================================================
 *	CLASS AUTOLOADER																	*
 *======================================================================================*/

/**
 * This section allows automatic inclusion of the library classes.
 */
function MyAutoload( $theClassName )
{
	$_path = kPATH_LIBRARY_SOURCE
			.str_replace( '\\', DIRECTORY_SEPARATOR, $theClassName )
			.'.php';
	if( file_exists( $_path ) )
		require_once( $_path );
}
spl_autoload_register( 'MyAutoload' );

?>
