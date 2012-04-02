<?php

/*=======================================================================================
 *																						*
 *									CQuery.inc.php										*
 *																						*
 *======================================================================================*/
 
/**
 *	{@link CQuery CQuery} definitions.
 *
 *	This file contains common definitions used by the {@link CQuery CQuery} class.
 *
 *	@package	MyWrapper
 *	@subpackage	Persistence
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 13/06/2011
 *				2.00 22/02/2012
 */

/*=======================================================================================
 *	DEFAULT QUERY STATEMENT TAGS														*
 *======================================================================================*/

/**
 * Query subject.
 *
 * This is the tag that represents the query subject.
 *
 * Cardinality: one or zero.
 */
define( "kAPI_QUERY_SUBJECT",		':$query-subject' );

/**
 * Query operator.
 *
 * This is the tag that represents the query operator.
 *
 * Cardinality: one or zero.
 */
define( "kAPI_QUERY_OPERATOR",		':$query-operator' );

/**
 * Query data type.
 *
 * This is the tag that represents the query data type.
 *
 * Cardinality: one or zero.
 */
define( "kAPI_QUERY_TYPE",			':$query-data-type' );

/**
 * Query data.
 *
 * This is the tag that represents the query data.
 *
 * Cardinality: one or zero.
 */
define( "kAPI_QUERY_DATA",			':$query-data' );

?>
