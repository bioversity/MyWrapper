<?php

/*=======================================================================================
 *																						*
 *									MessageTypes.inc.php								*
 *																						*
 *======================================================================================*/
 
/**
 *	Enumerations.
 *
 *	This file contains common enumerations used by all classes.
 *
 *	@package	Framework
 *	@subpackage	Definitions
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 23/06/2009
 */

/*=======================================================================================
 *	MESSAGE TYPES																		*
 *======================================================================================*/

/**
 * OK.
 *
 * This code indicates no errors.
 *
 * This state can be equated to an idle state.
 */
define( "kMESSAGE_TYPE_IDLE",							0 );		// Idle.

/**
 * Notice.
 *
 * This code indicates a notice.
 *
 * A notice is an informative message that does not imply an error, nor a situation that
 * should be handled; it can be considered as statistical data.
 */
define( "kMESSAGE_TYPE_NOTICE",							10 );		// Notice.

/**
 * Message.
 *
 * This code indicates a message.
 *
 * A message is an informative message that is addressed to somebody, although it does not
 * imply an error or warning, it was issued to a receiving party.
 */
define( "kMESSAGE_TYPE_MESSAGE",						20 );		// Message.

/**
 * Warning.
 *
 * This code indicates a warning.
 *
 * Warnings are informative data that indicate a potential problem, although they do not
 * imply an error, they indicate a potential problem or an issue that should be addressed
 * at least at a later stage.
 */
define( "kMESSAGE_TYPE_WARNING",						30 );		// Warning.

/**
 * Error.
 *
 * This code indicates an error.
 *
 * Errors indicate that something prevented an operation from being performed, this does
 * not necessarily mean that the whole process is halted, but that the results of an
 * operation will not be as expected.
 */
define( "kMESSAGE_TYPE_ERROR",							40 );		// Error.

/**
 * Fatal.
 *
 * This code indicates a fatal error.
 *
 * Fatal errors are {@link kMESSAGE_TYPE_ERROR errors} that result in stopping the whole
 * process: in this case the error will prevent other operations from being performed and
 * the whole process should be halted.
 */
define( "kMESSAGE_TYPE_FATAL",							50 );		// Fatal.

/**
 * Bug.
 *
 * This code indicates a bug.
 *
 * Bugs, as opposed to {@link kMESSAGE_TYPE_ERROR errors}, result from internal causes
 * independant from external factors. A bug indicates that an operation will never execute
 * as stated, it does not necessarily mean that it is {@link kMESSAGE_TYPE_FATAL fatal}, but
 * rather that the behaviour of an operation does not correspond to its declaration.
 */
define( "kMESSAGE_TYPE_BUG",							60 );		// Bug.


?>
