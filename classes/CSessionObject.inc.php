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
 *	SESSION GLOBAL TAGS																	*
 *======================================================================================*/

/**
 * Debug flag.
 *
 * This tag defines the debug flag.
 *
 * Type: boolean.
 */
define( "kSESSION_DEBUG",					'_sessionDebug' );

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
define( "kSESSION_USER_NAME",				'_sessionUserName' );

/**
 * User email instance.
 *
 * This tag defines the current user email instance.
 *
 * Type: string.
 */
define( "kSESSION_USER_EMAIL",				'_sessionUserEmail' );

/**
 * User kinds instance.
 *
 * This tag defines the current user kinds instance.
 *
 * Type: array.
 */
define( "kSESSION_USER_KIND",				'_sessionUserKind' );

/**
 * User roles instance.
 *
 * This tag defines the current user roles instance.
 *
 * Type: array.
 */
define( "kSESSION_USER_ROLE",				'_sessionUserRole' );

/**
 * User logged instance.
 *
 * This tag indicates whether the user is logged or not.
 *
 * Type: boolean.
 */
define( "kSESSION_USER_LOGGED",				'_sessionUserLogged' );

/*=======================================================================================
 *	SESSION PARAMETER TAGS																*
 *======================================================================================*/

/**
 * User code.
 *
 * This tag defines the user code.
 */
define( "kSESSION_PARAM_USER_CODE",			'@user-code@' );

/**
 * User name.
 *
 * This tag defines the user name.
 */
define( "kSESSION_PARAM_USER_NAME",			'@user-name@' );

/**
 * User e-mail.
 *
 * This tag defines the user e-mail.
 */
define( "kSESSION_PARAM_USER_EMAIL",		'@user-email@' );

/**
 * User roles.
 *
 * This tag defines the user roles.
 */
define( "kSESSION_PARAM_USER_ROLE",			'@user-role@' );

/**
 * User kinds.
 *
 * This tag defines the user kinds.
 */
define( "kSESSION_PARAM_USER_KIND",			'@user-kinds@' );

/**
 * User password.
 *
 * This tag defines the user password.
 */
define( "kSESSION_PARAM_USER_PASS",			'@user-pass@' );

/**
 * User logout.
 *
 * This tag defines the user logout command.
 */
define( "kSESSION_PARAM_USER_LOGOUT",		'@LOGOUT@' );

?>
