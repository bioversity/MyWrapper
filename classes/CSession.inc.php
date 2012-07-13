<?php

/*=======================================================================================
 *																						*
 *									CSession.inc.php									*
 *																						*
 *======================================================================================*/
 
/**
 * {@link CSession CSession} definitions.
 *
 * This file contains common definitions used by the {@link CSession CSession} class.
 *
 *	@package	MyWrapper
 *	@subpackage	Site
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 12/07/2012
 */

/*=======================================================================================
 *	SESSION TAGS																		*
 *======================================================================================*/

/**
 * User ID instance.
 *
 * This tag defines the current user ID instance.
 *
 * Type: CDataTypeBinary.
 */
define( "kSESSION_USER_ID",					'_SESSION_USER_ID' );

/**
 * User name instance.
 *
 * This tag defines the current user name instance.
 *
 * Type: string.
 */
define( "kSESSION_USER_NAME",				'_SESSION_USER_NAME' );

/**
 * User email instance.
 *
 * This tag defines the current user email instance.
 *
 * Type: string.
 */
define( "kSESSION_USER_EMAIL",				'_SESSION_USER_EMAIL' );

/**
 * User roles instance.
 *
 * This tag defines the current user roles instance.
 *
 * Type: array.
 */
define( "kSESSION_USER_ROLE",				'_SESSION_USER_ROLE' );

/**
 * User kinds instance.
 *
 * This tag defines the current user kinds instance.
 *
 * Type: array.
 */
define( "kSESSION_USER_KIND",				'_SESSION_USER_KIND' );

/**
 * Mongo instance.
 *
 * This tag defines the default Mongo instance.
 *
 * Cardinality: one.
 */
define( "kSESSION_STORE",					'_SESSION_STORE' );

/**
 * Neo4j instance.
 *
 * This tag defines the default Neo4j instance.
 *
 * Cardinality: one.
 */
define( "kSESSION_GRAPH",					'_SESSION_GRAPH' );

/**
 * Database instance.
 *
 * This tag defines the current database instance.
 *
 * Cardinality: one.
 */
define( "kSESSION_DATABASE",				'_SESSION_DATABASE' );

/**
 * Entity container instance.
 *
 * This tag defines the entity container instance.
 *
 * Cardinality: one.
 */
define( "kSESSION_CONTAINER_ENTITY",		'_SESSION_CONTAINER_ENTITY' );

?>
