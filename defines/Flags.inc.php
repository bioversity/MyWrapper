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
 *	@package	Framework
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

/*=======================================================================================
 *	OBJECT STATUS FLAGS - MASKS															*
 *======================================================================================*/

/**
 * State mask.
 *
 * This value masks all the state flags.
 */
define( "kFLAG_STATE_MASK",				0x00000003 );		// State mask.

/*=======================================================================================
 *	STRING MODIFIER FLAGS																*
 *======================================================================================*/

/**
 * Modifier: UTF8 convert.
 *
 * If the flag is set, the string will be converted to the UTF8 character set.
 */
define( "kFLAG_MODIFIER_UTF8",		0x00080000 );			// Convert to UTF8.

/**
 * Modifier: Left trim.
 *
 * If the flag is set the string will be left trimmed.
 *
 * This modification will be applied after the eventual {@link kFLAG_MODIFIER_UTF8 UTF-8}
 * conversion.
 */
define( "kFLAG_MODIFIER_LTRIM",		0x00100000 );			// Left trim.

/**
 * Modifier: Right trim.
 *
 * If the flag is set the string will be right trimmed.
 *
 * This modification will be applied after the eventual {@link kFLAG_MODIFIER_UTF8 UTF-8}
 * conversion.
 */
define( "kFLAG_MODIFIER_RTRIM",		0x00200000 );			// Left trim.

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
define( "kFLAG_MODIFIER_TRIM",		0x00300000 );			// Trim.

/**
 * Modifier: NULL.
 *
 * If the flag is set and the string is empty, it will be converted to a <i>NULL</i> value.
 *
 * This modification will be applied after the eventual {@link kFLAG_MODIFIER_UTF8 UTF-8}
 * and {@link kFLAG_MODIFIER_TRIM trim} conversions.
 */
define( "kFLAG_MODIFIER_NULL",		0x00400000 );			// NULL.

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
define( "kFLAG_MODIFIER_NULLSTR",	0x00C00000 );			// NULL string.

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
define( "kFLAG_MODIFIER_NOCASE",	0x01000000 );			// Case insensitive.

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
define( "kFLAG_MODIFIER_URL",		0x02000000 );			// URL-encode.

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
define( "kFLAG_MODIFIER_HTML",		0x04000000 );			// HTML-encode.

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
define( "kFLAG_MODIFIER_HEX",		0x08000000 );			// Convert to hexadecimal.

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
define( "kFLAG_MODIFIER_HEXEXP",	0x18000000 );			// Convert to hexadecimal expr.

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
define( "kFLAG_MODIFIER_HASH",		0x20000000 );			// Hash to 32 character hex.

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
define( "kFLAG_MODIFIER_HASH_BIN",	0x60000000 );			// Hash to 16 character binary.

/*=======================================================================================
 *	STRING MODIFIER FLAGS - MASKS														*
 *======================================================================================*/

/**
 * TRIM mask.
 *
 * This value masks both trim flags.
 */
define( "kFLAG_MODIFIER_MASK_TRIM",	0x00300000 );			// Trim mask.

/**
 * NULL mask.
 *
 * This value masks both the NULL flags.
 */
define( "kFLAG_MODIFIER_MASK_NULL",	0x00C00000 );			// NULL mask.

/**
 * HEX mask.
 *
 * This value masks both the HEX flags.
 */
define( "kFLAG_MODIFIER_MASK_HEX",	0x18000000 );			// HEX mask.

/**
 * Hash mask.
 *
 * This value masks the both hash flags.
 */
define( "kFLAG_MODIFIER_MASK_HASH",	0x60000000 );			// Hash mask.

/**
 * Modifiers mask.
 *
 * This value masks the string modifier flags.
 */
define( "kFLAG_MODIFIER_MASK",		0x7FF80000 );			// Mask.

/*=======================================================================================
 *	DEFAULT FLAGS																		*
 *======================================================================================*/

/**
 * Default state.
 *
 * This bitfield value represents the default flags state.
 */
define( "kFLAG_DEFAULT",			0x00000000 );			// Default mask.

/**
 * Status mask.
 *
 * This bitfield value represents the default flags mask.
 */
define( "kFLAG_DEFAULT_MASK",		0x7FFFFFFF );			// Default flags mask.


?>
