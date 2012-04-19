<?php

/*=======================================================================================
 *																						*
 *									Session.inc.php										*
 *																						*
 *======================================================================================*/
 
/**
 *	Default session tags.
 *
 *	This file contains the list default session tags used by the classes in this library.
 *
 *	@package	MyWrapper
 *	@subpackage	Definitions
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 03/03/2011
 *				2.00 23/02/2012
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
define( "kSESSION_USER",					':USER' );

/**
 * Mongo instance.
 *
 * This tag defines the default Mongo instance.
 *
 * Cardinality: one.
 */
define( "kSESSION_MONGO",					':MONGO' );

/**
 * Database instance.
 *
 * This tag defines the current database instance.
 *
 * Cardinality: one.
 */
define( "kSESSION_DATABASE",				':DATABASE' );

/**
 * Container instance.
 *
 * This tag defines the current container instance.
 *
 * Cardinality: one.
 */
define( "kSESSION_CONTAINER",				':CONTAINER' );


?>
