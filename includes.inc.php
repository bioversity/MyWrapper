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
 *	LIBRARY PATHS																		*
 *======================================================================================*/

/**
 * Class library root.
 *
 * This value defines the <b><i>absolute</i></b> path to the class library root
 * directory.
 */
define( "kPATH_LIBRARY_ROOT",		"/Library/WebServer/Library/wrapper/" );

/**
 * Class library sources.
 *
 * This value defines the <b><i>absolute</i></b> path to the class library sources
 * directory.
 */
define( "kPATH_LIBRARY_SOURCE",		"/Library/WebServer/Library/wrapper/classes/" );

/**
 * Class library definitions.
 *
 * This value defines the <b><i>absolute</i></b> path to the class library definitions
 * directory.
 */
define( "kPATH_LIBRARY_DEFINES",	"/Library/WebServer/Library/wrapper/defines/" );

/*=======================================================================================
 *	DEFAULT LANGUAGE CODE																*
 *======================================================================================*/

/**
 * Default language code.
 *
 * This value defines the default ISO 639 2 character language code.
 */
define( "kDEFAULT_LANGUAGE",			'en' );				// English.

/*=======================================================================================
 *	DEFAULT TEMPORARY MEMBER NAME														*
 *======================================================================================*/

/**
 * Default temporary member name.
 *
 * This value is used whenever there is the necessity to create a temporary data member in
 * an object, the value represents the member name.
 */
define( "kDEFAULT_MEMBER",				'___TMP___' );		// Temporary data member name.

/*=======================================================================================
 *	CLASS AUTOLOADER																	*
 *======================================================================================*/

/**
 * This section allows automatic inclusion of the library classes.
 */
function MyAutoload( $theClassName )
{
	require_once( kPATH_LIBRARY_SOURCE.$theClassName.'.php' );
}
spl_autoload_register( 'MyAutoload' );

?>
