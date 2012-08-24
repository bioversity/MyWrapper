<?php

/*=======================================================================================
 *																						*
 *										local.inc.php									*
 *																						*
 *======================================================================================*/
 
/**
 * Local definitions.
 *
 * This file contains common definitions used by the current directory
 *
 *	@package	MyWrapper
 *	@subpackage	Session
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 24/08/2012
 */

/*=======================================================================================
 *	DEFAULT SESSION OFFSETS																*
 *======================================================================================*/

/**
 * Default session offset.
 *
 * This value is used as the default offset in which the current session
 * {@link CSession object} will be stored.
 *
 * By default we set this value to the hash of the current file.
 */
define( "kDEFAULT_SESSION",			md5( __FILE__ ) );

?>
