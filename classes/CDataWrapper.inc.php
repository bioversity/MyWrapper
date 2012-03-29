<?php

/*=======================================================================================
 *																						*
 *								CDataWrapper.inc.php									*
 *																						*
 *======================================================================================*/
 
/**
 *	{@link CDataWrapper CDataWrapper} definitions.
 *
 *	This file contains common definitions used by the {@link CDataWrapper CDataWrapper}
 *	class.
 *
 *	@package	Framework
 *	@subpackage	Wrappers
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 05/06/2011
 *				2.00 22/02/2012
 */

/*=======================================================================================
 *	DEFAULT SERVICE TAGS																*
 *======================================================================================*/

/**
 * Web-service database.
 *
 * This is the tag that represents the database on which we want to operate.
 *
 * Cardinality: one.
 */
define( "kAPI_DATABASE",			':$database' );

/**
 * Web-service database container.
 *
 * This is the tag that represents the database container on which we want to operate.
 *
 * Cardinality: one.
 */
define( "kAPI_CONTAINER",			':$container' );

/*=======================================================================================
 *	DEFAULT PAGING TAGS																	*
 *======================================================================================*/

/**
 * Page start tag.
 *
 * This tag is used to define the starting page or record number.
 */
define( "kAPI_PAGE_START",			':$page-start' );

/**
 * Page limit tag.
 *
 * This tag is used to define the maximum number of elements to be returned by a request,
 * this should not be confused with the {@link kAPI_PAGE_COUNT count} tag which defines
 * the total number of elements affected by a request.
 */
define( "kAPI_PAGE_LIMIT",			':$page-limit' );

/**
 * Page count tag.
 *
 * This tag is used to define the <i>actual</i> number of elements returned by a request,
 * this value will be smaller or equal {@link kAPI_PAGE_LIMIT limit} tag which defines
 * the maximum number of elements to be returned by a request.
 */
define( "kAPI_PAGE_COUNT",			'_page-count' );

/*=======================================================================================
 *	DEFAULT DATA MANAGEMENT TAGS														*
 *======================================================================================*/

/**
 * Data store filter.
 *
 * This is the tag that represents the data store filter or query.
 *
 * Cardinality: one or zero.
 */
define( "kAPI_DATA_QUERY",			':$query' );

/**
 * Data store object fields.
 *
 * This is the tag that represents the data store object elements that should be returned:
 * if omitted it is assumed that the whole object is to be returned.
 *
 * Cardinality: one or zero.
 */
define( "kAPI_DATA_FIELD",			':$field' );

/**
 * Data store sort order.
 *
 * This is the tag that represents the data store sort elements that should be used for
 * sorting the results.
 *
 * Cardinality: one or zero.
 */
define( "kAPI_DATA_SORT",			':$sort' );

/**
 * Data store object.
 *
 * This is the tag that represents the data store object, this value is used when committing
 * data back to the data store.
 *
 * Cardinality: one or zero.
 */
define( "kAPI_DATA_OBJECT",			':$object' );

/**
 * Data store options.
 *
 * This is the tag that represents the data store options, this value is used to provide
 * additional options to the operation. It is structured as a key/value pair having the
 * following default key elements:
 *
 * <ul>
 *	<li><i>{@link kAPI_OPT_SAFE kAPI_OPT_SAFE}</i>: Safe commit option.
 *	<li><i>{@link kAPI_OPT_FSYNC kAPI_OPT_FSYNC}</i>: Safe and sync commit option.
 *	<li><i>{@link kAPI_OPT_TIMEOUT kAPI_OPT_TIMEOUT}</i>: Operation timeout.
 *	<li><i>{@link kAPI_OPT_SINGLE kAPI_OPT_SINGLE}</i>: Single object selection.
 * </ul>
 *
 * Cardinality: one or zero.
 */
define( "kAPI_DATA_OPTIONS",		':$options' );

/*=======================================================================================
 *	DEFAULT RESPONSE TAGS																*
 *======================================================================================*/

/**
 * Paging.
 *
 * This offset represents the collector tag for request paging parameters.
 *
 * <i>Note: this is a <b>reserved offset tag</b>.</i>
 */
define( "kAPI_DATA_PAGING",			'_paging' );

/**
 * Response.
 *
 * This tag holds the response block.
 */
define( "kAPI_DATA_RESPONSE",		'_response' );

/*=======================================================================================
 *	DEFAULT OPERATION ENUMERATIONS														*
 *======================================================================================*/

/**
 * COUNT web-service.
 *
 * This is the tag that represents the COUNT web-service operation, used to return the
 * total number of elements satisfying a query.
 */
define( "kAPI_OP_COUNT",			'count' );

/**
 * GET web-service.
 *
 * This is the tag that represents the GET web-service operation, used to retrieve objects
 * from the data store.
 */
define( "kAPI_OP_GET",				'get' );

/**
 * SET web-service.
 *
 * This is the tag that represents the SET web-service operation, used to insert or update
 * objects in the data store.
 */
define( "kAPI_OP_SET",				'set' );

/**
 * UPDATE web-service.
 *
 * This is the tag that represents the UPDATE web-service operation, used to update existing
 * objects in the data store.
 *
 * This option implies that the object already exists, or the operation should fail.
 */
define( "kAPI_OP_UPDATE",			'update' );

/**
 * INSERT web-service.
 *
 * This is the tag that represents the INSERT web-service operation, used to insert new
 * objects in the data store.
 *
 * This option implies that the object does not exists, or the operation should fail.
 */
define( "kAPI_OP_INSERT",			'insert' );

/**
 * BATCH-INSERT web-service.
 *
 * This service is equivalent to the {@link kAPI_OP_INSERT kAPI_OP_INSERT} command, except
 * that in this case you provide a list ov objects to insert.
 *
 * This option implies that the objects do not exists, or the operation should fail.
 */
define( "kAPI_OP_BATCH_INSERT",		'batch-insert' );

/**
 * MODIFY web-service.
 *
 * This is the tag that represents the MODIFY web-service operation, used to modify partial
 * contents of objects in the data store
 *
 * This option implies that the object already exists, or the operation should fail.
 */
define( "kAPI_OP_MODIFY",			'modify' );

/**
 * DELETE web-service.
 *
 * This is the tag that represents the DELETE web-service operation, used to delete objects
 * from the data store.
 */
define( "kAPI_OP_DEL",				'del' );

/*=======================================================================================
 *	DEFAULT OPTION ENUMERATIONS															*
 *======================================================================================*/

/**
 * SAFE option.
 *
 * Can be a boolean or integer, defaults to FALSE. If FALSE, the program continues executing
 * without waiting for a database response. If TRUE, the program will wait for the database
 * response and throw an exception if the operation did not succeed.
 */
define( "kAPI_OPT_SAFE",			'safe' );

/**
 * FSYNC option.
 *
 * Boolean, defaults to FALSE. Forces the update to be synced to disk before returning
 * success. If TRUE, a safe update is implied and will override setting safe to FALSE.
 */
define( "kAPI_OPT_FSYNC",			'fsync' );

/**
 * TIMEOUT option.
 *
 * Integer, if "safe" is set, this sets how long (in milliseconds) for the client to wait
 * for a database response. If the database does not respond within the timeout period, an
 * exception will be thrown.
 */
define( "kAPI_OPT_TIMEOUT",			'timeout' );

/**
 * SINGLE option.
 *
 * Boolean, used in the {@link kAPI_OP_DEL delete} operation: if TRUE, only one object will
 * be deleted; if not, all matching objects will be deleted.
 */
define( "kAPI_OPT_SINGLE",			'justOne' );

?>
