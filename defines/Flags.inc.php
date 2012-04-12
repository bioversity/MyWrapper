<?php

/*=======================================================================================
 *																						*
 *										Flags.inc.php									*
 *																						*
 *======================================================================================*/
 
/**
 *	Status flags.
 *
 *	This file contains the common status flags used by all classes in this library.
 *
 *	@package	MyWrapper
 *	@subpackage	Definitions
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 29/05/2009
 *				2.00 23/11/2010
 *				3.00 13/02/2012
 */

/*=======================================================================================
 *	OBJECT STATUS FLAGS																	*
 *======================================================================================*/

/**
 * State: Initialised.
 *
 * This bitfield value indicates that the object has been initialised, this means that all
 * required data members are present. In general, this means that elements comprising the
 * object's {@link CObject::_Index() index} are not missing.
 *
 * If this flag is not set, it means that the object lacks required elements, thus it will
 * not work correctly, or it cannot be stored persistently.
 */
define( "kFLAG_STATE_INITED",			0x00000001 );		// Initialised.

/**
 * State: Dirty.
 *
 * This bitfield value indicates that the object has been modified. In general this state is
 * only triggered by modifications to persistent data members; run time members should not
 * be included. Methods that serialise or modify the contents of the object should
 * respectively reset and set this flag.
 *
 * If the flag is not set, this means that the object has not been modified;
 */
define( "kFLAG_STATE_DIRTY",			0x00000002 );		// Dirty.

/**
 * State: Committed.
 *
 * This bitfield value indicates that the object has been either loaded from a persistent
 * container, or that it has been saved to a persistent container. If used in combination
 * with the {@link kFLAG_STATE_DIRTY dirty} flag, it allows committing the object only if it
 * was modified.
 *
 * If the flag is off, this means that the object was not instantiated from a persistent
 * container, or that it was not committed to it.
 */
define( "kFLAG_STATE_COMMITTED",		0x00000004 );		// Committed.

/**
 * State: Encoded.
 *
 * This bitfield value indicates an encoded state. This status is usually associated to
 * {@link CPersistentObject persistent} objects: if the state is ON it means that all
 * elements having custom data types, or data formats unsuitable for being transmitted over
 * the network, will be converted to standard data {@link CDataType types}. This flag will
 * be used by {@link CContainer containers} to determine whether to perform conversions
 * before and after persisting the objects.
 */
define( "kFLAG_STATE_ENCODED",			0x00000008 );		// Encoded.

/*=======================================================================================
 *	OBJECT STATUS FLAGS - MASKS															*
 *======================================================================================*/

/**
 * State mask.
 *
 * This value masks all the state flags.
 */
define( "kFLAG_STATE_MASK",				0x0000000F );		// State mask.

/*=======================================================================================
 *	PERSISTENCE ACTION FLAGS															*
 *======================================================================================*/

/**
 * Persist: Insert.
 *
 * This bitfield value indicates that we intend to insert an object in a container, this
 * means that if a duplicate object already exists, the operation should fail.
 */
define( "kFLAG_PERSIST_INSERT",			0x00000010 );		// Insert.

/**
 * Persist: Update.
 *
 * This bitfield value indicates that we intend to update an object in a container, this
 * means that the object must exist, or the operation must fail. This operation assumes that
 * the provided object will replace the entire contents of the existing one.
 */
define( "kFLAG_PERSIST_UPDATE",			0x00000020 );		// Update.

/**
 * Persist: Modify.
 *
 * This bitfield value indicates that we intend to modify an existing object, the difference
 * with the {@link kFLAG_PERSIST_UPDATE update} operation is that while the latter will
 * replace the whole object, in this case only the matching object attributes will be
 * updated and the unmatched ones will be left untouched. This option applies when adding
 * or modifying a subset of an object.
 */
define( "kFLAG_PERSIST_MODIFY",			0x00000060 );		// Modify.

/**
 * Persist: Replace.
 *
 * This bitfield value indicates that we intend to either
 * {@link kFLAG_PERSIST_INSERT insert} an object, if it doesn't already exist, or
 * {@link kFLAG_PERSIST_UPDATE update} an existing object. In the latter case, this
 * operation assumes that the provided object will replace the entire contents of the
 * existing one.
 */
define( "kFLAG_PERSIST_REPLACE",		0x00000030 );		// Replace.

/**
 * Persist: Delete.
 *
 * This bitfield value indicates that we intend to delete an object from a container, if
 * the object doesn't exist, the operation should still succeed.
 */
define( "kFLAG_PERSIST_DELETE",			0x00000080 );		// Delete.

/*=======================================================================================
 *	PERSISTENCE ACTION FLAGS - MASKS													*
 *======================================================================================*/

/**
 * Write mask.
 *
 * This value masks the access flags that imply writing to the collection, with the
 * exception of {@link kFLAG_PERSIST_DELETE deleting}.
 */
define( "kFLAG_PERSIST_WRITE_MASK",		0x00000070 );		// Write mask.

/**
 * Persist mask.
 *
 * This value masks all the persistence action flags.
 */
define( "kFLAG_PERSIST_MASK",			0x000000F0 );		// Persist mask.

/*=======================================================================================
 *	REFERENCE OPTION FLAGS																*
 *======================================================================================*/

/**
 * Reference: Identifier.
 *
 * This bitfield value indicates that we intend to include
 * {@link kOFFSET_REFERENCE_ID identifier} information to the reference, this is used when
 * {@link CConttainer::Reference() converting} a {@link CPersistentObject persistent} object
 * to a reference.
 */
define( "kFLAG_REFERENCE_IDENTIFIER",	0x00000100 );		// Identifier.

/**
 * Reference: Container.
 *
 * This bitfield value indicates that we intend to include
 * {@link kOFFSET_REFERENCE_CONTAINER container} information to the reference, this is used
 * when {@link CConttainer::Reference() converting} a {@link CPersistentObject persistent}
 * object to a reference.
 */
define( "kFLAG_REFERENCE_CONTAINER",	0x00000200 );		// Container.

/**
 * Reference: Database.
 *
 * This bitfield value indicates that we intend to include
 * {@link kOFFSET_REFERENCE_DATABASE database} information to the reference, this is used
 * when {@link CConttainer::Reference() converting} a {@link CPersistentObject persistent}
 * object to a reference.
 */
define( "kFLAG_REFERENCE_DATABASE",		0x00000400 );		// Database.

/**
 * Reference: Class.
 *
 * This bitfield value indicates that we intend to include {@link kTAG_CLASS class}
 * information to the reference, this is used when
 * {@link CConttainer::Reference() converting} a {@link CPersistentObject persistent}
 * object to a reference.
 */
define( "kFLAG_REFERENCE_CLASS",		0x00000800 );		// Class.

/*=======================================================================================
 *	REFERENCE OPTION FLAGS - MASKS														*
 *======================================================================================*/

/**
 * Reference mask.
 *
 * This value masks all the reference option flags.
 */
define( "kFLAG_REFERENCE_MASK",			0x00000F00 );		// Reference options mask.

/*=======================================================================================
 *	STRING MODIFIER FLAGS																*
 *======================================================================================*/

/**
 * Modifier: UTF8 convert.
 *
 * If the flag is set, the string will be converted to the UTF8 character set.
 */
define( "kFLAG_MODIFIER_UTF8",			0x00080000 );		// Convert to UTF8.

/**
 * Modifier: Left trim.
 *
 * If the flag is set the string will be left trimmed.
 *
 * This modification will be applied after the eventual {@link kFLAG_MODIFIER_UTF8 UTF-8}
 * conversion.
 */
define( "kFLAG_MODIFIER_LTRIM",			0x00100000 );		// Left trim.

/**
 * Modifier: Right trim.
 *
 * If the flag is set the string will be right trimmed.
 *
 * This modification will be applied after the eventual {@link kFLAG_MODIFIER_UTF8 UTF-8}
 * conversion.
 */
define( "kFLAG_MODIFIER_RTRIM",			0x00200000 );		// Left trim.

/**
 * Modifier: Trim.
 *
 * If the flag is set the string will be trimmed, both left and right; this option implies
 * that {@link kFLAG_MODIFIER_LTRIM kFLAG_MODIFIER_LTRIM} and
 * {@link kFLAG_MODIFIER_RTRIM kFLAG_MODIFIER_RTRIM} is also set.
 *
 * This modification will be applied after the eventual {@link kFLAG_MODIFIER_UTF8 UTF-8}
 * conversion.
 */
define( "kFLAG_MODIFIER_TRIM",			0x00300000 );		// Trim.

/**
 * Modifier: NULL.
 *
 * If the flag is set and the string is empty, it will be converted to a <i>NULL</i> value.
 *
 * This modification will be applied after the eventual {@link kFLAG_MODIFIER_UTF8 UTF-8}
 * and {@link kFLAG_MODIFIER_TRIM trim} conversions.
 */
define( "kFLAG_MODIFIER_NULL",			0x00400000 );		// NULL.

/**
 * Modifier: NULL string.
 *
 * If the flag is set and the string is empty, it will be converted to a '<i>NULL</i>'
 * string; this option implies that {@link kFLAG_MODIFIER_NULL kFLAG_MODIFIER_NULL} option
 * is also set.
 *
 * This modification will be applied after the eventual {@link kFLAG_MODIFIER_UTF8 UTF-8}
 * and {@link kFLAG_MODIFIER_TRIM trim} conversions.
 */
define( "kFLAG_MODIFIER_NULLSTR",		0x00C00000 );		// NULL string.

/**
 * Modifier: Case insensitive.
 *
 * If the flag is set, the string will be set to lowercase, the default case insensitive
 * modifier. Note that depending on the {@link kFLAG_MODIFIER_UTF8 UTF-8} flag, either the
 * {@link strtolower() standard} or {@link mb_convert_case() multibyte} function will be
 * used.
 *
 * This modification will be applied after the eventual {@link kFLAG_MODIFIER_UTF8 UTF-8},
 * {@link kFLAG_MODIFIER_TRIM trim} and {@link kFLAG_MODIFIER_NULL NULL} conversions.
 */
define( "kFLAG_MODIFIER_NOCASE",		0x01000000 );		// Case insensitive.

/**
 * Modifier: Encode for URL.
 *
 * If the flag is set, the string will be URL-encoded; this option and
 * {@link kFLAG_MODIFIER_HTML kFLAG_MODIFIER_HTML} are mutually exclusive.
 *
 * This modification will be applied after the eventual {@link kFLAG_MODIFIER_UTF8 UTF-8},
 * {@link kFLAG_MODIFIER_TRIM trim}, {@link kFLAG_MODIFIER_NULL NULL} and
 * {@link kFLAG_MODIFIER_NOCASE lowercase} conversions.
 */
define( "kFLAG_MODIFIER_URL",			0x02000000 );		// URL-encode.

/**
 * Modifier: Encode for HTML.
 *
 * If the flag is set, the string will be URL-encoded; this option and
 * {@link kFLAG_MODIFIER_URL kFLAG_MODIFIER_URL} are mutually exclusive.
 *
 * This modification will be applied after the eventual {@link kFLAG_MODIFIER_UTF8 UTF-8},
 * {@link kFLAG_MODIFIER_TRIM trim}, {@link kFLAG_MODIFIER_NULL NULL} and
 * {@link kFLAG_MODIFIER_NOCASE lowercase} conversions.
 */
define( "kFLAG_MODIFIER_HTML",			0x04000000 );		// HTML-encode.

/**
 * Modifier: HEX.
 *
 * If the flag is set the string will be converted to a hexadecimal string; this option is
 * mutually exclusive with the {@link kFLAG_MODIFIER_HASH kFLAG_MODIFIER_HASH} and
 * {@link kFLAG_MODIFIER_HASH_BIN kFLAG_MODIFIER_HASH_BIN} flags.
 *
 * This modification will be applied after the eventual {@link kFLAG_MODIFIER_UTF8 UTF-8},
 * {@link kFLAG_MODIFIER_TRIM trim}, {@link kFLAG_MODIFIER_NULL NULL},
 * {@link kFLAG_MODIFIER_NOCASE lowercase} and {@link kFLAG_MODIFIER_URL url} or
 * {@link kFLAG_MODIFIER_HTML HTML} conversions.
 */
define( "kFLAG_MODIFIER_HEX",			0x08000000 );		// Convert to hexadecimal.

/**
 * Modifier: HEX expression.
 *
 * If the flag is set the string will be converted to a hexadecimal string suitable to be
 * used in an expression (<i>0x00...</i>). Note that if this flag is set it is assumed that
 * {@link kFLAG_MODIFIER_HEX kFLAG_MODIFIER_HEX} is also set and this flag is mutually
 * exclusive with the {@link kFLAG_MODIFIER_HASH kFLAG_MODIFIER_HASH} and
 * {@link kFLAG_MODIFIER_HASH_BIN kFLAG_MODIFIER_HASH_BIN} flags.
 *
 * This modification will be applied after the eventual {@link kFLAG_MODIFIER_UTF8 UTF-8},
 * {@link kFLAG_MODIFIER_TRIM trim}, {@link kFLAG_MODIFIER_NULL NULL},
 * {@link kFLAG_MODIFIER_NOCASE lowercase} and {@link kFLAG_MODIFIER_URL url} or
 * {@link kFLAG_MODIFIER_HTML HTML} conversions.
 */
define( "kFLAG_MODIFIER_HEXEXP",		0x18000000 );		// Convert to hexadecimal expr.

/**
 * Modifier: hash.
 *
 * If the flag is set, the string will be hashed into a 32 character hex string; this option
 * is mutually exclusive with the {@link kFLAG_MODIFIER_HEX kFLAG_MODIFIER_HEX} and
 * {@link kFLAG_MODIFIER_HEXEXP kFLAG_MODIFIER_HEXEXP} flags.
 *
 * This modification will be applied after the eventual {@link kFLAG_MODIFIER_UTF8 UTF-8},
 * {@link kFLAG_MODIFIER_TRIM trim}, {@link kFLAG_MODIFIER_NULL NULL},
 * {@link kFLAG_MODIFIER_NOCASE lowercase} and {@link kFLAG_MODIFIER_URL url} or
 * {@link kFLAG_MODIFIER_HTML HTML} conversions.
 */
define( "kFLAG_MODIFIER_HASH",			0x20000000 );		// Hash to 32 character hex.

/**
 * Modifier: binary hash.
 *
 * If the flag is set, the string will be hashed and the resulting string will be
 * a 16 character binary string. Note that if this flag is set it is assumed that
 * {@link kFLAG_MODIFIER_HASH kFLAG_MODIFIER_HASH} is also set and this option is mutually
 * exclusive with the {@link kFLAG_MODIFIER_HEX kFLAG_MODIFIER_HEX} and
 * {@link kFLAG_MODIFIER_HEXEXP kFLAG_MODIFIER_HEXEXP} flags.
 *
 * This modification will be applied after the eventual {@link kFLAG_MODIFIER_UTF8 UTF-8},
 * {@link kFLAG_MODIFIER_TRIM trim}, {@link kFLAG_MODIFIER_NULL NULL},
 * {@link kFLAG_MODIFIER_NOCASE lowercase} and {@link kFLAG_MODIFIER_URL url} or
 * {@link kFLAG_MODIFIER_HTML HTML} conversions.
 */
define( "kFLAG_MODIFIER_HASH_BIN",		0x60000000 );		// Hash to 16 character binary.

/*=======================================================================================
 *	STRING MODIFIER FLAGS - MASKS														*
 *======================================================================================*/

/**
 * TRIM mask.
 *
 * This value masks both trim flags.
 */
define( "kFLAG_MODIFIER_MASK_TRIM",		0x00300000 );		// Trim mask.

/**
 * NULL mask.
 *
 * This value masks both the NULL flags.
 */
define( "kFLAG_MODIFIER_MASK_NULL",		0x00C00000 );		// NULL mask.

/**
 * HEX mask.
 *
 * This value masks both the HEX flags.
 */
define( "kFLAG_MODIFIER_MASK_HEX",		0x18000000 );		// HEX mask.

/**
 * Hash mask.
 *
 * This value masks the both hash flags.
 */
define( "kFLAG_MODIFIER_MASK_HASH",		0x60000000 );		// Hash mask.

/**
 * Modifiers mask.
 *
 * This value masks the string modifier flags.
 */
define( "kFLAG_MODIFIER_MASK",			0x7FF80000 );		// Mask.

/*=======================================================================================
 *	DEFAULT FLAGS																		*
 *======================================================================================*/

/**
 * Default state.
 *
 * This bitfield value represents the default flags state.
 */
define( "kFLAG_DEFAULT",				0x00000000 );		// Default mask.

/**
 * Status mask.
 *
 * This bitfield value represents the default flags mask.
 */
define( "kFLAG_DEFAULT_MASK",			0x7FFFFFFF );		// Default flags mask.


?>
