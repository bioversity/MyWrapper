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
 *	DEFAULT STATUS TAGS																	*
 *======================================================================================*/

/**
 * Native status.
 *
 * This tag will hold the native status of the operation.
 */
define( "kAPI_STATUS_NATIVE",		'native' );

/*=======================================================================================
 *	DEFAULT COUNTER TAGS																*
 *======================================================================================*/

/**
 * Count tag.
 *
 * This tag will hold the total number of elements affected by the operation.
 */
define( "kAPI_AFFECTED_COUNT",		'affected' );

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
 * This tag defines a web service that returns an object by reference. It is equivalent to
 * the {@link kAPI_OP_GET_ONE kAPI_OP_GET_ONE} operation, except that instead of using the
 * query provided in the {@link kAPI_DATA_QUERY kAPI_DATA_QUERY} parameter, it will try to
 * extract an identifier from the object provided in the
 * {@link kAPI_DATA_OBJECT kAPI_DATA_OBJECT} parameter.
 *
 * Note that as with other values in the {@link kAPI_DATA_OBJECT object} parameter, you must
 * {@link CDataType::SerialiseObject() serialise} the value.
 */
define( "kAPI_OP_GET_OBJECT_REF",	'GetObjectByReference' );

/*=======================================================================================
 *	DEFAULT OPTION ENUMERATIONS															*
 *======================================================================================*/

/**
 * No response option.
 *
 * Can be a boolean or integer, defaults to FALSE. If TRUE, the
 * {@link kAPI_DATA_RESPONSE response} section will not be included in the result. This can
 * be useful when you are only interested in the status of the operation and not in the
 * response.
 */
define( "kAPI_OPT_NO_RESP",			':$no-response' );

?>
