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
 * User instance.
 *
 * This tag defines the current user instance.
 *
 * Cardinality: one.
 */
define( "kSESSION_USER",					':SESSION:USER' );

/**
 * Mongo instance.
 *
 * This tag defines the default Mongo instance.
 *
 * Cardinality: one.
 */
define( "kSESSION_MONGO",					':SESSION:MONGO' );

/**
 * Neo4j instance.
 *
 * This tag defines the default Neo4j instance.
 *
 * Cardinality: one.
 */
define( "kSESSION_NEO4J",					':SESSION:NEO4J' );

/**
 * Database instance.
 *
 * This tag defines the current database instance.
 *
 * Cardinality: one.
 */
define( "kSESSION_DATABASE",				':SESSION:DATABASE' );

/**
 * Container instance.
 *
 * This tag defines the current container instance.
 *
 * Cardinality: one.
 */
define( "kSESSION_CONTAINER",				':SESSION:CONTAINER' );

?>
