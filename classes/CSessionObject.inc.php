<?php

/*=======================================================================================
 *																						*
 *								CSessionObject.inc.php									*
 *																						*
 *======================================================================================*/
 
/**
 * {@link CSessionObject CSessionObject} definitions.
 *
 * This file contains common definitions used by the {@link CSessionObject CSessionObject}
 * class.
 *
 *	@package	MyWrapper
 *	@subpackage	Session
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 12/07/2012
 */

/*=======================================================================================
 *	SESSION USER TAGS																	*
 *======================================================================================*/

/**
 * User name instance.
 *
 * This tag defines the current user name instance.
 *
 * Type: string.
 */
define( "kSESSION_USER_NAME",				'_SESSION_USER_NAME' );

/**
 * User email instance.
 *
 * This tag defines the current user email instance.
 *
 * Type: string.
 */
define( "kSESSION_USER_EMAIL",				'_SESSION_USER_EMAIL' );

/**
 * User roles instance.
 *
 * This tag defines the current user roles instance.
 *
 * Type: array.
 */
define( "kSESSION_USER_ROLE",				'_SESSION_USER_ROLE' );

/**
 * User kinds instance.
 *
 * This tag defines the current user kinds instance.
 *
 * Type: array.
 */
define( "kSESSION_USER_KIND",				'_SESSION_USER_KIND' );

?>
