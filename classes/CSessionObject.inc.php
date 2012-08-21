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
 *	SESSION SERIALISE TAGS																*
 *======================================================================================*/

/**
 * Object offsets container.
 *
 * This tag defines the object offsets container.
 */
define( "kSESSION_SERIALIZE_OFFSET",		'_offsets' );

/**
 * Object members container.
 *
 * This tag defines the object members container.
 */
define( "kSESSION_SERIALIZE_MEMBER",		'_members' );

/**
 * Object static members container.
 *
 * This tag defines the object static members container.
 */
define( "kSESSION_SERIALIZE_STATIC",		'_static' );

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
 * User roles instance.
 *
 * This tag defines the current user roles instance.
 *
 * Type: array.
 */
define( "kSESSION_USER_ROLE",				'_sessionUserRole' );

/**
 * User kinds instance.
 *
 * This tag defines the current user kinds instance.
 *
 * Type: array.
 */
define( "kSESSION_USER_KIND",				'_sessionUserKind' );

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
define( "kSESSION_PARAM_USER_LOGOUT",		'LOGOUT' );

?>
