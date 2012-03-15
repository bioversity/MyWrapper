<?php

/*=======================================================================================
 *																						*
 *								CMongoDataWrapper.inc.php								*
 *																						*
 *======================================================================================*/
 
/**
 *	{@link CMongoDataWrapper CMongoDataWrapper} definitions.
 *
 * This file contains common definitions used by the
 * {@link CMongoDataWrapper CMongoDataWrapper} class.
 *
 *	@package	Framework
 *	@subpackage	Wrappers
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 07/06/2011
 *				2.00 23/02/2012
 */

/*=======================================================================================
 *	DEFAULT OPERATION ENUMERATIONS														*
 *======================================================================================*/

/**
 * GET-ONE web-service.
 *
 * This is the tag that represents the findOne Mongo operation, it will return the first
 * matched object.
 */
define( "kAPI_OP_GET_ONE",			'get-one' );

/**
 * GET-OBJECT-REF web-service.
 *
 * This tag defines a web service that returns an object when provided an object reference:
 * with this option you do not provide a {@link kAPI_DATA_QUERY query} but you provide a
 * {@link MongoDBRef reference} object in the {@link kAPI_DATA_OBJECT object} parameter.
 *
 * Note that as with other values in the {@link kAPI_DATA_OBJECT object} parameter, you must
 * {@link CMongoContainer::SerialiseObject() serialise} the value.
 */
define( "kAPI_OP_GET_OBJECT_REF",	'GetObjectByReference' );

/*=======================================================================================
 *	DEFAULT COUNTER TAGS																*
 *======================================================================================*/

/**
 * Count tag.
 *
 * This tag will hold the total number of elements affected by the operation.
 */
define( "kAPI_AFFECTED_COUNT",		'_count' );

?>
