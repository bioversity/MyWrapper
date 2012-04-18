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
 *	@package	MyWrapper
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
 * This tag represents the primitive string data type.
 */
define( "kTYPE_STRING",						':STR' );				// String.

/**
 * 32 bit signed integer type.
 *
 * This value represents the primitive 32 bit signed integer data type.
 *
 * This data type is serialised as foillows:
 *
 * <ul>
 *	<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>: Will contain this constant.
 *	<li><i>{@link kTAG_DATA kTAG_DATA}</i>: Will contain the string representation of the
 *		integer.
 * </ul>
 */
define( "kTYPE_INT32",						':INT32' );				// 32 bit integer.

/**
 * 64 bit signed integer type.
 *
 * This value represents the primitive 64 bit signed integer data type.
 *
 * This data type is serialised as foillows:
 *
 * <ul>
 *	<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>: Will contain this constant.
 *	<li><i>{@link kTAG_DATA kTAG_DATA}</i>: Will contain the string representation of the
 *		integer.
 * </ul>
 */
define( "kTYPE_INT64",						':INT64' );				// 64 bit integer.

/**
 * Float type.
 *
 * This value represents the primitive floating point data type.
 */
define( "kTYPE_FLOAT",						':FLOAT' );				// Float.

/**
 * Boolean type.
 *
 * This value represents the primitive boolean data type, it is assumed that it is provided
 * as (y/n; Yes/No; 1/0; TRUE/FALSE) and will be converted to 1/0.
 */
define( "kTYPE_BOOLEAN",					':BOOL' );				// Boolean.

/*=======================================================================================
 *	COMPOSITE DATA TYPE ENUMERATIONS													*
 *======================================================================================*/

/**
 * Binary type.
 *
 * This value represents a binary string data type, it is generally expressed as an instance
 * of the {@link CDataTypeBinary CDataTypeBinary} class.
 */
define( "kTYPE_BINARY",						':BIN' );				// Binary.

/**
 * Date type.
 *
 * This value represents a date represented as a <i>YYYYMMDD</i> string in which missing
 * elements should be omitted. This means that if we don't know the day we can express that
 * date as <i>YYYYMM</i>.
 */
define( "kTYPE_DATE",						':DATE' );				// Date.

/**
 * Time type.
 *
 * This value represents a time represented as a <i>YYYY-MM-DD HH:MM:SS</i> string in which
 * you may not have missing elements.
 */
define( "kTYPE_TIME",						':TIME' );				// Time.

/**
 * Regular expression type.
 *
 * This tag defines a regular expression string type, it is generally expressed as an
 * instance of the {@link CDataTypeRegex CDataTypeRegex} class.
 */
define( "kTYPE_REGEX",						':RGEX' );				// Regular expression.

/*=======================================================================================
 *	STRUCTURED DATA TYPE ENUMERATIONS													*
 *======================================================================================*/

/**
 * Timestamp type.
 *
 * This data type should be used for native time-stamps, it is generally expressed as an
 * instance of the {@link CDataTypeStamp CDataTypeStamp} class.
 */
define( "kTYPE_STAMP",						':STAMP' );				// Timestamp.

/**
 * Enumeration type.
 *
 * This value represents the enumeration data type, it represents an enumeration element or
 * container.
 *
 * Enumerations represent a vocabulary from which one value must be chosen.
 */
define( "kTYPE_ENUM",						':ENUM' );				// Enumeration.

/**
 * Set type.
 *
 * This value represents the enumerated set data type, it represents an enumerated set
 * element or container.
 *
 * Sets represent a vocabulary from which one or more value must be chosen.
 */
define( "kTYPE_ENUM_SET",					':SET' );				// Set.

/*=======================================================================================
 *	SUB-OBJECT TYPES																	*
 *======================================================================================*/

/**
 * Seconds.
 *
 * This tag defines the number of seconds since January 1st, 1970.
 */
define( "kTYPE_STAMP_SEC",					'sec' );

/**
 * Microseconds.
 *
 * This tag defines microseconds.
 */
define( "kTYPE_STAMP_USEC",					'usec' );

/**
 * Binary string.
 *
 * This tag defines a binary string.
 */
define( "kTYPE_BINARY_STRING",				'bin' );

/**
 * Binary string type.
 *
 * This tag defines a binary string type (integer):
 *
 * <ul>
 *	<li><i>1</i>: Function.
 *	<li><i>2</i>: Byte array (use as default).
 *	<li><i>3</i>: UUID.
 *	<li><i>5</i>: MD5.
 *	<li><i>128</i>: Custom.
 * </ul>
 */
define( "kTYPE_BINARY_TYPE",				'type' );

/*=======================================================================================
 *	STRING FORMAT ENUMERATIONS															*
 *======================================================================================*/

/**
 * PHP type.
 *
 * This value represents the primitive PHP data type, it is an PHP serialised object string.
 */
define( "kTYPE_PHP",						':PHP' );				// PHP.

/**
 * JSON type.
 *
 * This value represents the primitive JSON data type, it is an JSON encoded string.
 */
define( "kTYPE_JSON",						':JSON' );				// JSON.

/**
 * XML type.
 *
 * This value represents the primitive XML data type, it is an XML encoded string.
 */
define( "kTYPE_XML",						':XML' );				// XML.

/**
 * HTML type.
 *
 * This value represents the primitive HTML data type, it is an HTML encoded string.
 */
define( "kTYPE_HTML",						':HTML' );				// HTML.

/**
 * CSV type.
 *
 * This value represents the primitive comma separated data type, it is an CSV encoded
 * string.
 */
define( "kTYPE_CSV",						':CSV' );				// CSV.

/**
 * META type.
 *
 * This value represents the primitive meta data type, it is a generalised metadata type.
 */
define( "kTYPE_META",						':META' );				// META.

/*=======================================================================================
 *	REFERENCE TYPES																		*
 *======================================================================================*/

/**
 * Exact reference.
 *
 * This is the tag that represents an exact reference.
 */
define( "kTYPE_EXACT",						':EXACT' );

/**
 * Broad reference.
 *
 * This is the tag that represents a broad reference.
 */
define( "kTYPE_BROAD",						':BROAD' );

/**
 * Narrow reference.
 *
 * This is the tag that represents a narrow reference.
 */
define( "kTYPE_NARROW",						':NARROW' );

/**
 * Related reference.
 *
 * This is the tag that represents a related reference.
 */
define( "kTYPE_RELATED",					':RELATED' );

/*=======================================================================================
 *	TERM TYPES																			*
 *======================================================================================*/

/**
 * Term.
 *
 * This is the tag that represents a generic term.
 */
define( "kTYPE_TERM",							':TERM' );

/**
 * Namespace.
 *
 * This is the tag that represents a namespace term.
 */
define( "kTYPE_NAMESPACE_TERM",					':TERM:NS' );

/**
 * Ontology.
 *
 * This is the tag that represents an ontology root term.
 */
define( "kTYPE_ONTOLOGY_TERM",					':TERM:ONTO' );

/**
 * Predicate.
 *
 * This is the tag that represents a predicate term.
 */
define( "kTYPE_PREDICATE_TERM",					':TERM:PRED' );

/**
 * Attribute.
 *
 * This is the tag that represents an attribute term.
 */
define( "kTYPE_ATTRIBUTE_TERM",					':TERM:ATTR' );

/**
 * Measure.
 *
 * This is the tag that represents a measure term.
 */
define( "kTYPE_MEASURE_TERM",					':TERM:MEASURE' );

/**
 * Enumeration.
 *
 * This is the tag that represents an enumeration term.
 */
define( "kTYPE_ENUM_TERM",						':TERM:ENUM' );

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
define( "kTYPE_MongoId",					'MONGO:MongoId' );			// MongoId.

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
define( "kTYPE_MongoCode",				'MONGO:MongoCode' );		// MongoCode.


?>
