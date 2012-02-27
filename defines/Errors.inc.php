<?php

/*=======================================================================================
 *																						*
 *										Errors.inc.php									*
 *																						*
 *======================================================================================*/
 
/**
 *	Errors.
 *
 *	This file contains the common error codes used by all classes in this library.
 *
 *	@package	Framework
 *	@subpackage	Definitions
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 22/12/2010
 *	@version	2.00 07/02/2012
 */

/*=======================================================================================
 *	GENERAL ERRORS																		*
 *======================================================================================*/

/**
 * Invalid state.
 *
 * This error indicates that the current state is invalid or not fit in order to perform
 * the requested information.
 */
define( "kERROR_INVALID_STATE",				-1 );			// Invalid state.

/**
 * Invalid parameter.
 *
 * This error indicates that an invalid parameter was passed to a method or function.
 */
define( "kERROR_INVALID_PARAMETER",			-2 );			// Invalid parameter.

/**
 * Missing option.
 *
 * This error indicates that a parameter to a method is missing a required option.
 */
define( "kERROR_OPTION_MISSING",			-3 );			// Missing option.

/**
 * Bad method call.
 *
 * This error indicates a call to a non existing method. In general this will happen when
 * mapping functions to the class and the called function does not exist.
 */
define( "kERROR_BAD_METHOD_CALL",			-4 );			// Bad method call.

/**
 * Object not ready.
 *
 * This error indicates the attempt to operate with an object that has not yet been
 * properly initialised.
 */
define( "kERROR_NOT_INITED",				-5 );			// Object not ready.

/**
 * Unsupported.
 *
 * This error indicates the attempt to either perform an unsupported operation, or that an
 * unsupported data type was provided to a method.
 */
define( "kERROR_UNSUPPORTED",				-6 );			// Unsupported.

/**
 * Protected.
 *
 * This error indicates the attempt to modify a protected attribute, this usually occurs
 * when modifying a significative offset of an object that uses it as an identifier.
 */
define( "kERROR_PROTECTED",					-7 );			// Protected.

/**
 * Not found.
 *
 * This error is raised when an expected object is missing from a container.
 */
define( "kERROR_NOT_FOUND",					-8 );			// Not found.


?>
