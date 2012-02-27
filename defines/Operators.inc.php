<?php

/*=======================================================================================
 *																						*
 *									Operators.inc.php									*
 *																						*
 *======================================================================================*/
 
/**
 *	Tokens.
 *
 *	This file contains the common enumeration identifying operators used by all classes in
 *	this library.
 *
 *	@package	Framework
 *	@subpackage	Definitions
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 01/06/2011
 */

/*=======================================================================================
 *	OPERATORS																			*
 *======================================================================================*/

/**
 * Disabled.
 *
 * This enumeration represents a disabled operator.
 */
define( "kOPERATOR_DISABLED",			'$NO' );			// Disabled.

/**
 * Equal.
 *
 * This enumeration represents equality.
 */
define( "kOPERATOR_EQUAL",				'$EQ' );			// Equals.

/**
 * Not equal.
 *
 * This enumeration represents inequality.
 */
define( "kOPERATOR_EQUAL_NOT",			'$NE' );			// Not equal.

/**
 * Like.
 *
 * This enumeration represents case and accent matching (for strings).
 */
define( "kOPERATOR_LIKE",				'$AS' );			// Like.

/**
 * Not like.
 *
 * This enumeration represents case and accent non matching (for strings).
 */
define( "kOPERATOR_LIKE_NOT",			'$NS' );			// Not like.

/**
 * Prefix.
 *
 * This enumeration represents prefix comparaison: <i>starts with</i> (for strings).
 */
define( "kOPERATOR_PREFIX",				'$PX' );			// Starts with.

/**
 * Contains.
 *
 * This enumeration represents content comparaison: <i>contains</i> (for strings).
 */
define( "kOPERATOR_CONTAINS",			'$CX' );			// Contains.

/**
 * Suffix.
 *
 * This enumeration represents suffix comparaison: <i>ends with</i> (for strings).
 */
define( "kOPERATOR_SUFFIX",				'$SX' );			// Ends with.

/**
 * Regular expression.
 *
 * This enumeration represents a regular expression (for strings).
 */
define( "kOPERATOR_REGEX",				'$RE' );			// Regular expression.

/**
 * Smaller.
 *
 * This enumeration represents less than.
 */
define( "kOPERATOR_LESS",				'$LT' );			// Less than.

/**
 * Smaller or equal.
 *
 * This enumeration represents less than or equal.
 */
define( "kOPERATOR_LESS_EQUAL",			'$LE' );			// Less than or equal.

/**
 * Greater.
 *
 * This enumeration represents greater than.
 */
define( "kOPERATOR_GREAT",				'$GT' );			// Greater than.

/**
 * Greater or equal.
 *
 * This enumeration represents greater than or equal.
 */
define( "kOPERATOR_GREAT_EQUAL",		'$GE' );			// Greater than or equal.

/**
 * Range inclusive.
 *
 * This enumeration represents a range including limits.
 */
define( "kOPERATOR_IRANGE",				'$IRG' );			// Range inclusive.

/**
 * Range exclusive.
 *
 * This enumeration represents a range excluding limits.
 */
define( "kOPERATOR_ERANGE",				'$ERG' );			// Range exclusive.

/**
 * Empty or null.
 *
 * This enumeration represents empty or null.
 */
define( "kOPERATOR_NULL",				'$NU' );			// Empty or null.

/**
 * Not empty or null.
 *
 * This enumeration represents not empty or null.
 */
define( "kOPERATOR_NOT_NULL",			'$NN' );			// Not empty or null.

/**
 * Belongs to.
 *
 * This enumeration matches the value to a list of options.
 */
define( "kOPERATOR_IN",					'$IN' );			// Belongs to.

/**
 * Does not belong to.
 *
 * This enumeration excludes values that match a list of options.
 */
define( "kOPERATOR_NI",					'$NI' );			// Does not belong to.

/**
 * All.
 *
 * This enumeration matches a list of values to all elements of a list of options.
 */
define( "kOPERATOR_ALL",				'$AL' );			// All.

/**
 * Not all.
 *
 * This enumeration is the negation of the match {@link kOPERATOR_ALL all} operator.
 */
define( "kOPERATOR_NALL",				'$NAL' );			// Not all.

/**
 * Expression.
 *
 * This enumeration qualifies expression terms.
 */
define( "kOPERATOR_EX",					'$EX' );			// Expression.

/*=======================================================================================
 *	BOOLEAN OPERATORS																	*
 *======================================================================================*/

/**
 * AND.
 *
 * This value represents the AND (A && B) operator.
 */
define( "kOPERATOR_AND",				'$AND' );

/**
 * Not AND.
 *
 * This value represents the not AND (NOT(A && B)) operator.
 */
define( "kOPERATOR_NAND",				'$NAND' );

/**
 * OR.
 *
 * This value represents the OR (A || B) operator.
 */
define( "kOPERATOR_OR",					'$OR' );

/**
 * Not OR.
 *
 * This value represents the not OR (NOT(A || B)) operator.
 */
define( "kOPERATOR_NOR",				'$NOR' );


?>
