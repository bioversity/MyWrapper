<?php

/*=======================================================================================
 *																						*
 *									Types.inc.php										*
 *																						*
 *======================================================================================*/
 
/**
 *	Enumerations.
 *
 *	This file contains common data types used by all classes.
 *
 *	@package	Framework
 *	@subpackage	Definitions
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 23/03/2011
 *				2.00 20/02/2012
 */

/*=======================================================================================
 *	PRIMITIVE DATA TYPE ENUMERATIONS													*
 *======================================================================================*/

/**
 * String type.
 *
 * This value represents the primitive string data type.
 */
define( "kDATA_TYPE_STRING",				':STR' );				// String.

/**
 * 32 bit signed integer type.
 *
 * This value represents the primitive 32 bit signed integer data type.
 */
define( "kDATA_TYPE_INT32",					':INT32' );				// 32 bit integer.

/**
 * 64 bit signed integer type.
 *
 * This value represents the primitive 64 bit signed integer data type.
 */
define( "kDATA_TYPE_INT64",					':INT64' );				// 64 bit integer.

/**
 * Float type.
 *
 * This value represents the primitive floating point data type.
 */
define( "kDATA_TYPE_FLOAT",					':FLOAT' );				// Float.

/**
 * Date type.
 *
 * This value represents a date represented as a <i>YYYYMMDD</i> string in which missing
 * elements should be omitted. This means that if we don't know the day we can express that
 * date as <i>YYYYMM</i>.
 */
define( "kDATA_TYPE_DATE",					':DATE' );				// Date.

/**
 * time type.
 *
 * This value represents a time represented as a <i>YYYY-MM-DD HH:MM:SS</i> string in which
 * you may not have missing elements.
 */
define( "kDATA_TYPE_TIME",					':TIME' );				// Time.

/**
 * Timestamp type.
 *
 * This data type should be used for native time-stamps, in general it can be shared as a
 * structure formatted as follows:
 *
 * <ul>
 *	<li><i>{@link kOBJ_TYPE_STAMP_SEC kOBJ_TYPE_STAMP_SEC}</i>: Number of seconds since
 *		January 1st, 1970.
 *	<li><i>{@link kOBJ_TYPE_STAMP_USEC kOBJ_TYPE_STAMP_USEC}</i>:Microseconds.
 * </ul>
 */
define( "kDATA_TYPE_STAMP",					':STAMP' );				// Timestamp.

/**
 * Boolean type.
 *
 * This value represents the primitive boolean data type, it is assumed that it is provided
 * as (y/n; Yes/No; 1/0; TRUE/FALSE) and will be converted to 1/0.
 */
define( "kDATA_TYPE_BOOLEAN",				':BOOL' );				// Boolean.

/**
 * Binary type.
 *
 * This value represents the primitive binary data type, it is assumed that it is provided
 * as a hexadecimal string.
 */
define( "kDATA_TYPE_BINARY",				':BIN' );				// Binary.

/*=======================================================================================
 *	STRUCTURED STRING DATA TYPE ENUMERATIONS											*
 *======================================================================================*/

/**
 * Enumeration type.
 *
 * This value represents the enumeration data type, it represents an enumeration element or
 * container.
 *
 * Enumerations represent a vocabulary from which one value must be chosen.
 */
define( "kDATA_TYPE_ENUM",					':ENUM' );				// Enumeration.

/**
 * Set type.
 *
 * This value represents the enumerated set data type, it represents an enumerated set
 * element or container.
 *
 * Sets represent a vocabulary from which one or more value must be chosen.
 */
define( "kDATA_TYPE_SET",					':SET' );				// Set.

/*=======================================================================================
 *	SUB-OBJECT TYPES																	*
 *======================================================================================*/

/**
 * Seconds.
 *
 * This tag defines the number of seconds since January 1st, 1970.
 */
define( "kOBJ_TYPE_STAMP_SEC",				'sec' );

/**
 * Microseconds.
 *
 * This tag defines microseconds.
 */
define( "kOBJ_TYPE_STAMP_USEC",				'usec' );

/*=======================================================================================
 *	STRING FORMAT ENUMERATIONS															*
 *======================================================================================*/

/**
 * PHP type.
 *
 * This value represents the primitive PHP data type, it is an PHP serialised object string.
 */
define( "kDATA_TYPE_PHP",					':PHP' );				// PHP.

/**
 * JSON type.
 *
 * This value represents the primitive JSON data type, it is an JSON encoded string.
 */
define( "kDATA_TYPE_JSON",					':JSON' );				// JSON.

/**
 * XML type.
 *
 * This value represents the primitive XML data type, it is an XML encoded string.
 */
define( "kDATA_TYPE_XML",					':XML' );				// XML.

/**
 * HTML type.
 *
 * This value represents the primitive HTML data type, it is an HTML encoded string.
 */
define( "kDATA_TYPE_HTML",					':HTML' );				// HTML.

/**
 * CSV type.
 *
 * This value represents the primitive comma separated data type, it is an CSV encoded
 * string.
 */
define( "kDATA_TYPE_CSV",					':CSV' );				// CSV.

/*=======================================================================================
 *	MONGODB DATA TYPES																	*
 *======================================================================================*/

/**
 * MongoId.
 *
 * This value represents the MongoId object data type, when serialised it will have the
 * following structure:
 *
 * <ul>
 *	<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>: Will contain this constant.
 *	<li><i>{@link kTAG_DATA kTAG_DATA}</i>: Will contain the HEX string ID.
 * </ul>
 */
define( "kDATA_TYPE_MongoId",				'MONGO:MongoId' );			// MongoId.

/**
 * MongoCode.
 *
 * This value represents the MongoCode object data type, when serialised it will have the
 * following structure:
 *
 * <ul>
 *	<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>: Will contain this constant.
 *	<li><i>{@link kTAG_DATA kTAG_DATA}</i>: Will contain the following structure:
 *	 <ul>
 *		<li><i>code</i>: The javascript code string.
 *		<li><i>scope</i>: The list of key/value pairs.
 *	 </ul>
 * </ul>
 */
define( "kDATA_TYPE_MongoCode",				'MONGO:MongoCode' );		// MongoCode.

/**
 * MongoDate.
 *
 * This value represents the MongoDate object data type, when serialised it will have the
 * following structure:
 *
 * <ul>
 *	<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>: Will contain this constant.
 *	<li><i>{@link kTAG_DATA kTAG_DATA}</i>: Will contain the following structure:
 *	 <ul>
 *		<li><i>sec</i>: Number of seconds since January 1st, 1970 (integer).
 *		<li><i>usec</i>: Milliseconds (integer).
 *	 </ul>
 * </ul>
 */
define( "kDATA_TYPE_MongoDate",				'MONGO:MongoDate' );		// MongoDate.

/**
 * MongoInt32.
 *
 * This value represents the MongoInt32 object data type, when serialised it will have the
 * following structure:
 *
 * <ul>
 *	<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>: Will contain this constant.
 *	<li><i>{@link kTAG_DATA kTAG_DATA}</i>: Will contain the string representation of the
 *		integer.
 * </ul>
 */
define( "kDATA_TYPE_MongoInt32",			'MONGO:MongoInt32' );		// MongoInt32.

/**
 * MongoInt64.
 *
 * This value represents the MongoInt64 object data type, when serialised it will have the
 * following structure:
 *
 * <ul>
 *	<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>: Will contain this constant.
 *	<li><i>{@link kTAG_DATA kTAG_DATA}</i>: Will contain the string representation of the
 *		integer.
 * </ul>
 */
define( "kDATA_TYPE_MongoInt64",			'MONGO:MongoInt64' );		// MongoInt64.

/**
 * MongoRegex.
 *
 * This value represents the MongoRegex object data type, when serialised it will have the
 * following structure:
 *
 * <ul>
 *	<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>: Will contain this constant.
 *	<li><i>{@link kTAG_DATA kTAG_DATA}</i>: The regular expression string.
 * </ul>
 */
define( "kDATA_TYPE_MongoRegex",			'MONGO:MongoRegex' );		// MongoRegex.

/**
 * MongoBinData.
 *
 * This value represents the MongoBinData object data type, when serialised it will have the
 * following structure:
 *
 * <ul>
 *	<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>: Will contain this constant.
 *	<li><i>{@link kTAG_DATA kTAG_DATA}</i>: Will contain the following structure:
 *	 <ul>
 *		<li><i>bin</i>: The binary string.
 *		<li><i>type</i>: String type (integer).
 *	 </ul>
 * </ul>
 */
define( "kDATA_TYPE_MongoBinData",			'MONGO:MongoBinData' );		// MongoBinData.


?>
