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
 * Object reference type.
 *
 * This data type should be used as object references, it is generally expressed as a
 * structure composed of the following elements:
 *
 * <ul>
 *	<li><i>{@link kTAG_REFERENCE_ID kTAG_REFERENCE_ID}</i>: Object
 *		{@link kTAG_LID local} unique identifier. This value may be used as a scalar in
 *		cases in which the location of the object is univoque.
 *	<li><i>{@link kTAG_REFERENCE_CONTAINER kTAG_REFERENCE_CONTAINER}</i>: Object container,
 *		reference to the container in which the object is stored. This component will be
 *		used in cases in which the container of the object is not univoque.
 *	<li><i>{@link kTAG_REFERENCE_DATABASE kTAG_REFERENCE_DATABASE}</i>: Database container,
 *		reference to the database or container superclass in which the object is stored.
 *		This component will be used in cases in which the database of the object is not
 *		univoque.
 * </ul>
 */
define( "kTYPE_REF",						':REF' );				// Object reference.

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

/**
 * List type.
 *
 * This value represents the list data type, it represents an array of elements that may be
 * scalars or other lists, this specific data type is a generalised tag for list structures.
 */
define( "kTYPE_LIST",						':LIST' );				// List.

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
 *	DATA FORMAT ENUMERATIONS															*
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
 * SVG type.
 *
 * This value represents the SVG image data type, it is generally expressed in XML.
 */
define( "kTYPE_SVG",						':SVG' );				// SVG.

/**
 * PNG type.
 *
 * This value represents the PNG image data type, it is generally expressed in hexadecimal.
 */
define( "kTYPE_PNG",						':PNG' );				// PNG.

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
 *	TERM OR ONTOLOGY GRAPH NODE TYPES													*
 *======================================================================================*/

/**
 * Root.
 *
 * This is the tag that represents a root term or node.
 */
define( "kTYPE_ROOT",						':ROOT' );

/**
 * Namespace.
 *
 * This is the tag that represents a namespace.
 */
define( "kTYPE_NAMESPACE",					':NAMESPACE' );

/**
 * Attribute.
 *
 * This is the tag that represents an attribute.
 */
define( "kTYPE_ATTRIBUTE",					':ATTRIBUTE' );

/**
 * Structure.
 *
 * This is the tag that represents a term that rtepresents a structure.
 */
define( "kTYPE_STRUCTURE",					':STRUCTURE' );

/**
 * Predicate.
 *
 * This is the tag that represents a predicate.
 */
define( "kTYPE_PREDICATE",					':PREDICATE' );

/**
 * Trait.
 *
 * This is the tag that represents a generic trait.
 */
define( "kTYPE_TRAIT",						':TRAIT' );

/**
 * Method.
 *
 * This is the tag that represents a generic method.
 */
define( "kTYPE_METHOD",						':METHOD' );

/**
 * Measure.
 *
 * This is the tag that represents a measure term.
 */
define( "kTYPE_MEASURE",					':MEASURE' );

/**
 * Annotation.
 *
 * This is the tag that represents an annotation term.
 */
define( "kTYPE_ANNOTATION",					':ANNOTATION' );

/**
 * Enumeration.
 *
 * This is the tag that represents an enumeration term.
 */
define( "kTYPE_ENUMERATION",				':ENUMERATION' );

/**
 * Dictionary.
 *
 * This is the tag that represents a dictionary term.
 */
define( "kTYPE_DICTIONARY",					':DICTIONARY' );

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
define( "kTYPE_MongoId",					':MongoId' );			// MongoId.

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
define( "kTYPE_MongoCode",					':MongoCode' );		// MongoCode.


?>
