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
 *	@package	Framework
 *	@subpackage	Definitions
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 29/05/2009
 *				2.00 23/11/2010
 *				3.00 18/02/2012
 */

/*=======================================================================================
 *	DEFAULT IDENTIFICATION OFFSETS														*
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
define( "kTAG_ID_NATIVE",						'_id' );

/*=======================================================================================
 *	DEFAULT TREE OFFSETS																*
 *======================================================================================*/

/**
 * Node level.
 *
 * This offset represents the node level.
 */
define( "kTAG_NODE_LEVEL",						'_level' );

/**
 * Left branch reference.
 *
 * This offset represents the node left branch reference.
 */
define( "kTAG_NODE_LEFT",						'_left' );

/**
 * Right branch reference.
 *
 * This offset represents the node right branch reference.
 */
define( "kTAG_NODE_RIGHT",						'_right' );

/*=======================================================================================
 *	REFERENCE DEFAULT OFFSETS															*
 *======================================================================================*/

/**
 * Identifier reference tag.
 *
 * This is the tag is the offset used to indicate an object unique identifier within an
 * object reference.
 */
define( "kTAG_ID_REFERENCE",					'$id' );

/**
 * Collection name reference tag.
 *
 * This tag is the offset used to indicate a collection within an object reference.
 */
define( "kTAG_COLLECTION_REFERENCE",			'$ref' );

/**
 * Database name reference tag.
 *
 * This tag is the offset used to indicate a database within an object reference.
 */
define( "kTAG_DATABASE_REFERENCE",				'$db' );

/*=======================================================================================
 *	DEFAULT OBJECT TAGS																	*
 *======================================================================================*/

/**
 * Reference tag.
 *
 * This offset indicates a list of references to other objects. If the context of these
 * references is univoque, the list may be represented as a simple array; if each reference
 * may have a different contect, this could be implemented as an array of
 * {@link kTAG_TYPE type} and {@link kTAG_DATA data} elements in which the first one defines
 * the context.
 */
define( "kTAG_REF",								':REF' );

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
 * This tag is used as the default offset for indicating an attribute's type, in general it
 * is used in an array in conjunction with the {@link kTAG_DATA data} offset to indicate the
 * data type of the item.
 */
define( "kTAG_TYPE",							':TYPE' );

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
 * Name offset.
 *
 * This tag is used as the default offset for indicating an attribute's name.
 */
define( "kTAG_NAME",							':NAME' );

/**
 * Description.
 *
 * This tag is used as the default offset for indicating an attribute's description or
 * definition.
 */
define( "kTAG_DESCRIPTION",						':DESCRIPTION' );

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


?>
