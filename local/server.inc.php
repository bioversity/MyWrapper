<?php

/*=======================================================================================
 *																						*
 *									server.inc.php										*
 *																						*
 *======================================================================================*/
 
/**
 *	Server environment definitions.
 *
 *	This file should be included at the top level of the application or web site, it
 *	represents the server environment definitions, those specific to the current server.
 *
 *	@package	MyWrapper
 *	@subpackage	Run-time
 *
 *	@author		Milko A. Skofic <m.skofic@cgiar.org>
 *	@version	1.00 03/08/2012
 */

/*=======================================================================================
 *	NEO4J CLIENT PARAMETERS																*
 *======================================================================================*/

/**
 * Neo4j host.
 *
 * This tag defines the default NEO4J host.
 *
 * Cardinality: one.
 */
define( "kDEFAULT_kNEO4J_HOST",		'localhost' );

/**
 * Neo4j port.
 *
 * This tag defines the default NEO4J port.
 *
 * Cardinality: one.
 */
define( "kDEFAULT_kNEO4J_PORT",		'7474' );

/**
 * Neo4j user.
 *
 * This tag defines the default NEO4J user.
 *
 * Cardinality: one.
 */
define( "kDEFAULT_kNEO4J_USER",		NULL );

/**
 * Neo4j password.
 *
 * This tag defines the default NEO4J user password.
 *
 * Cardinality: one.
 */
define( "kDEFAULT_kNEO4J_PASS",		NULL );

/*=======================================================================================
 *	USER DEFINITIONS																	*
 *======================================================================================*/

/**
 * Default guest name.
 *
 * This value defines the default guest user name.
 */
define( "kDEFAULT_GUEST_NAME",		"Guest" );

/**
 * Default guest code.
 *
 * This value defines the default guest user code.
 */
define( "kDEFAULT_GUEST_CODE",		"guest" );

/**
 * Default guest password.
 *
 * This value defines the default guest user password.
 */
define( "kDEFAULT_GUEST_PASS",		"guest" );

/**
 * Default guest e-mail.
 *
 * This value defines the default guest user e-mail.
 */
define( "kDEFAULT_GUEST_EMAIL",		"helpdesk@grinfo.net" );

/*=======================================================================================
 *	MYSQL CLIENT PARAMETERS																*
 *======================================================================================*/

/**
 * MySQL ANCILLARY DSN.
 *
 * This tag holds the MySQL ancillary database data source name (read only).
 *
 * Cardinality: one.
 */
define( "DEFAULT_ANCILLARY_HOST",
		'MySQLi://WEB-SERVICES:webservicereader@192.168.181.1/ANCILLARY' );

?>
