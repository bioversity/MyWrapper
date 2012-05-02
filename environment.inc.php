<?php

/*=======================================================================================
 *																						*
 *									environment.inc.php									*
 *																						*
 *======================================================================================*/
 
/**
 *	Default environment definitions.
 *
 *	This file should be included at the top level of the application or web site, just after
 *	the <i>includes.in.php</i>, it contains the runtime definitions that apply to the
 *	current application instance.
 *
 *	@package	MyWrapper
 *	@subpackage	Run-time
 *
 *	@author		Milko A. Skofic <m.skofic@cgiar.org>
 *	@version	1.00 16/04/2012
 */

/*=======================================================================================
 *	DATABASE DEFINITIONS																*
 *======================================================================================*/

/**
 * Default database name.
 *
 * This value defines the default database name for the current application.
 */
define( "kDEFAULT_DATABASE",		"WAREHOUSE" );

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
define( "DEFAULT_kNEO4J_HOST",		'localhost' );

/**
 * Neo4j port.
 *
 * This tag defines the default NEO4J port.
 *
 * Cardinality: one.
 */
define( "DEFAULT_kNEO4J_PORT",		'7474' );

/**
 * Neo4j user.
 *
 * This tag defines the default NEO4J user.
 *
 * Cardinality: one.
 */
define( "DEFAULT_kNEO4J_USER",		NULL );

/**
 * Neo4j password.
 *
 * This tag defines the default NEO4J user password.
 *
 * Cardinality: one.
 */
define( "DEFAULT_kNEO4J_PASS",		NULL );

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
