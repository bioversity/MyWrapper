<?php

/*=======================================================================================
 *																						*
 *									site.inc.php										*
 *																						*
 *======================================================================================*/
 
/**
 *	Local environment definitions.
 *
 *	This file should be included at the top level of the application or web site, it
 *	represents the local environment definitions, those specific to the current site.
 *
 *	@package	MyWrapper
 *	@subpackage	Run-time
 *
 *	@author		Milko A. Skofic <m.skofic@cgiar.org>
 *	@version	1.00 03/08/2012
 */

/*=======================================================================================
 *	DEFAULT DEFINITIONS																	*
 *======================================================================================*/

/**
 * Default database name.
 *
 * This value defines the default database name for the current application.
 */
define( "kDEFAULT_DATABASE",		"WAREHOUSE" );

/**
 * Default session offset.
 *
 * This value is used as the default offset in which the current session
 * {@link CSession object} will be stored.
 *
 * By default we set this value to the hash of the current file.
 */
define( "kDEFAULT_SESSION",			md5( __FILE__ ) );

?>
