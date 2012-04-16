<?php

/*=======================================================================================
 *																						*
 *									Offsets.inc.php										*
 *																						*
 *======================================================================================*/
 
/**
 *	Default offsets.
 *
 * This file contains the definitions of the default offsets or tags used by objects in this
 * library, whenever choosing offsets for {@link CPersistentObject persistent} objects, you
 * should first make sure that they are not among those defined in this file.
 *
 *	@package	MyWrapper
 *	@subpackage	Definitions
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 29/05/2009
 *				2.00 23/11/2010
 *				3.00 18/02/2012
 */

/*=======================================================================================
 *	DEFAULT IDENTIFICATION TAGS															*
 *======================================================================================*/

/**
 * Local unique identifier offset.
 *
 * This is the tag that represents the object's unique identifier, this offset should hold
 * a scalar value which uniquely identifies the object within the collection that holds it.
 *
 * This should not be confused with the unique global identifier, which represents the value
 * or values used by the public to refer to that object.
 *
 * This value should be tightly integrated with the database.
 */
define( "kTAG_ID",								'_id' );

/*=======================================================================================
 *	DEFAULT REFERENCE TAGS																*
 *======================================================================================*/

/**
 * Identifier reference tag.
 *
 * This is the tag is the offset used to indicate an object unique identifier within an
 * object reference.
 */
define( "kTAG_REFERENCE_ID",					'$id' );

/**
 * Collection name reference tag.
 *
 * This tag is the offset used to indicate a container within an object reference.
 */
define( "kTAG_REFERENCE_CONTAINER",				'$ref' );

/**
 * Database name reference tag.
 *
 * This tag is the offset used to indicate a database within an object reference.
 */
define( "kTAG_REFERENCE_DATABASE",				'$db' );

/*=======================================================================================
 *	DEFAULT OBJECT TAGS																	*
 *======================================================================================*/

/**
 * Class tag.
 *
 * This is the offset that should be used to store the object's class name, it will be used
 * to {@link CMongoUnitObject::NewObject() instantiate} objects when loading them from their
 * containers.
 */
define( "kTAG_CLASS",							':CLASS' );

/**
 * Version tag.
 *
 * This tag is an offset that should be used to represent the object's version, the version
 * is a value that should change each time the object is saved: it can be used to check
 * whether an object was modified since the last time it was read.
 *
 * By default it is an integer incremented each time the object is saved.
 */
define( "kTAG_VERSION",							':VERSION' );

/*=======================================================================================
 *	DEFAULT ATTRIBUTE TAGS																*
 *======================================================================================*/

/**
 * Type.
 *
 * This tag is used as the default offset for indicating an attribute's data type, in
 * general it is used in a structure in conjunction with the {@link kTAG_DATA data} offset
 + to indicate the data type of the item.
 */
define( "kTAG_TYPE",							':TYPE' );

/**
 * Kind.
 *
 * This tag is used as the default offset for indicating a kind attribute. A kind is
 * similar to the {@link kTAG_TYPE kTAG_TYPE} attribute, except that in the latter case it
 * qualifies specifically the {@link kTAG_DATA kTAG_DATA} elements, in this case it
 * discriminates the elements of a list.
 */
define( "kTAG_KIND",							':KIND' );

/**
 * Unit.
 *
 * This tag is used as the default offset for indicating a unit attribute. A unit is a
 * measurement unit such as centimeters, in general this offset will hold a reference to
 * an object that defines the unit.
 */
define( "kTAG_UNIT",							':UNIT' );

/**
 * Data.
 *
 * This tag is used as the default offset for indicating an attribute's data or content, in
 * general this tag is used with the {@link kTAG_TYPE type} offset when storing 
 */
define( "kTAG_DATA",							':DATA' );

/**
 * Code offset.
 *
 * This tag is used as the default offset for indicating an attribute's code or acronym.
 */
define( "kTAG_CODE",							':CODE' );

/**
 * Enumeration offset.
 *
 * This tag is used as the default offset for indicating an attribute containing an
 * enumeration code or acronym.
 */
define( "kTAG_ENUM",							':ENUM' );

/**
 * Namespace offset.
 *
 * This tag is used as the default offset for indicating a namespace code or acronym.
 */
define( "kTAG_NAMESPACE",						':NS' );

/**
 * Name offset.
 *
 * This tag is used as the default offset for indicating an attribute's name.
 */
define( "kTAG_NAME",							':NAME' );

/**
 * Description.
 *
 * This tag is used as the default offset for indicating an attribute's description.
 */
define( "kTAG_DESCRIPTION",						':DESCR' );

/**
 * Definition.
 *
 * This tag is used as the default offset for indicating an attribute's definition.
 */
define( "kTAG_DEFINITION",						':DEF' );

/**
 * Language.
 *
 * This tag is used as the default offset for indicating the language of an attribute, it
 * should be the 2 character ISO 639 language code.
 */
define( "kTAG_LANGUAGE",						':LANGUAGE' );

/**
 * Status.
 *
 * This tag is used as the default offset for indicating an attribute's status or state, it
 * will generally be an array of tags defining the various states associated with the
 * object.
 */
define( "kTAG_STATUS",							':STATUS' );

/**
 * Annotation.
 *
 * This tag is used as the default offset for indicating a list of annotations, in general
 * it will contain a list of key/value pairs.
 */
define( "kTAG_ANNOTATION",						':ANNOTATION' );

/*=======================================================================================
 *	DEFAULT TIME-STAMP TAGS																*
 *======================================================================================*/

/**
 * Creation time-stammp.
 *
 * This tag is used as the default offset for indicating a creation time-stamp.
 */
define( "kTAG_STAMP_CRE",						':STAMP:CRE' );

/**
 * Last modification time-stammp.
 *
 * This tag is used as the default offset for indicating a last modification time-stamp.
 */
define( "kTAG_STAMP_MOD",						':STAMP:MOD' );

/*=======================================================================================
 *	DEFAULT TERM KIND TAGS																*
 *======================================================================================*/

/**
 * Term.
 *
 * This is the tag that represents a generic term.
 */
define( "kTAG_TERM",							':TERM' );

/**
 * Namespace.
 *
 * This is the tag that represents a namespace term.
 */
define( "kTAG_TERM_NAMESPACE",					':TERM:NS' );

/**
 * Ontology.
 *
 * This is the tag that represents an ontology root term.
 */
define( "kTAG_TERM_ONTOLOGY",					':TERM:ONTO' );

/**
 * Predicate.
 *
 * This is the tag that represents a predicate term.
 */
define( "kTAG_TERM_PREDICATE",					':TERM:PRED' );

/**
 * Scale.
 *
 * This is the tag that represents a measure term.
 */
define( "kTAG_TERM_MEASURE",					':TERM:MEASURE' );

/**
 * Enumeration.
 *
 * This is the tag that represents an enumeration term.
 */
define( "kTAG_TERM_ENUM",						':TERM:ENUM' );

/*=======================================================================================
 *	DEFAULT PROPERTY OFFSETS															*
 *======================================================================================*/

/**
 * Password offset.
 *
 * This is the tag that represents a password; the value is a string.
 */
define( "kOFFSET_PASSWORD",						':PASS' );

/**
 * Mail offset.
 *
 * This is the tag that represents a mailing address, the value may either be a string or
 * an array.
 */
define( "kOFFSET_MAIL",							':MAIL' );

/**
 * E-mail offset.
 *
 * This is the tag that represents an e-mail, the value may either be a string or an array.
 */
define( "kOFFSET_EMAIL",						':EMAIL' );

/**
 * Telephone offset.
 *
 * This is the tag that represents a telephone number, the value may either be a string or
 * an array.
 */
define( "kOFFSET_PHONE",						':PHONE' );

/**
 * Telefax offset.
 *
 * This is the tag that represents a telefax number, the value may either be a string or
 * an array.
 */
define( "kOFFSET_FAX",							':FAX' );

/**
 * References tag.
 *
 * This is the tag that represents the list of references of an object, it is an array of
 * object references in which each element may either be the reference itself or the
 * following structure:
 *
 * <ul>
 *	<li><i>{@link kTAG_KIND kTAG_KIND}</i>: Relation predicate, it can either be an object
 *		reference or a string.
 *	<li><i>{@link kTAG_DATA kTAG_DATA}</i>: Relation object, it will be a reference to an
 *		object in which the following elements may appear:
 *	 <ul>
 *		<li><i>{@link kTAG_REFERENCE_ID kTAG_REFERENCE_ID}</i>: The unique identifier of the
 *			referenced object.
 *		<li><i>{@link kTAG_REFERENCE_CONTAINER kTAG_REFERENCE_CONTAINER}</i>: The
 *			{@link CContainer container} name.
 *		<li><i>{@link kTAG_REFERENCE_DATABASE kTAG_REFERENCE_DATABASE}</i>: The database
 *			name.
 *		<li><i>{@link kTAG_CLASS kTAG_CLASS}</i>: The object class name.
 *	 </ul>
 * </ul>
 */
define( "kOFFSET_REFS",							':REFS' );

/**
 * Valid tag.
 *
 * This is the tag that represents the valid entry related to the current one. There may be
 * cases in which it is not an option to delete objects, so we create a new one and the old
 * one will point to the new one. This tag represents that property and it expects the value
 * of the {@link kTAG_ID native} identifier of the new object here.
 */
define( "kOFFSET_VALID",						':VALID' );

/**
 * URL.
 *
 * This is the tag that represents an URL, link or web address.
 */
define( "kOFFSET_URL",							':URL' );

/**
 * Acronym.
 *
 * This is the tag that represents a list of acronyms.
 */
define( "kOFFSET_ACRONYM",						':ACRONYM' );

/*=======================================================================================
 *	DEFAULT MAIL PROPERTY OFFSETS														*
 *======================================================================================*/

/**
 * Place offset.
 *
 * This is the tag that represents a place or named location.
 */
define( "kOFFSET_MAIL_PLACE",					':MAIL:PLACE' );

/**
 * Care of offset.
 *
 * This is the tag that represents a care of address reference.
 */
define( "kOFFSET_MAIL_CARE",					':MAIL:CARE' );

/**
 * Street offset.
 *
 * This is the tag that represents a place or named location.
 */
define( "kOFFSET_MAIL_STREET",					':MAIL:STREET' );

/**
 * ZIP offset.
 *
 * This is the tag that represents a ZIP code.
 */
define( "kOFFSET_MAIL_ZIP",						':MAIL:ZIP' );

/**
 * City offset.
 *
 * This is the tag that represents a city name.
 */
define( "kOFFSET_MAIL_CITY",					':MAIL:CITY' );

/**
 * Province offset.
 *
 * This is the tag that represents a province name or code.
 */
define( "kOFFSET_MAIL_PROVINCE",				':MAIL:PROV' );

/**
 * Country offset.
 *
 * This is the tag that represents an ISO3166 3 character country code.
 */
define( "kOFFSET_MAIL_COUNTRY",					':MAIL:COUNTRY' );

/**
 * Full address offset.
 *
 * This is the tag that represents the full address as a string.
 */
define( "kOFFSET_MAIL_FULL",					'MAIL:FULL' );


?>
