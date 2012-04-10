<?php

/*=======================================================================================
 *																						*
 *								CWarehouseWrapper.inc.php								*
 *																						*
 *======================================================================================*/
 
/**
 *	{@link CWarehouseWrapper CWarehouseWrapper} definitions.
 *
 * This file contains common definitions used by the
 * {@link CWarehouseWrapper CWarehouseWrapper} class.
 *
 *	@package	MyWrapper
 *	@subpackage	Wrappers
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 10/04/2012
 */

/*=======================================================================================
 *	DEFAULT OPERATION ENUMERATIONS														*
 *======================================================================================*/

/**
 * GET-ONE web-service.
 *
 * This is the tag that represents the findOne Mongo operation, it will return the first
 * matched object.
 */
define( "kAPI_OP_LOGIN",			'@LOGIN' );

/*=======================================================================================
 *	DEFAULT OPTION ENUMERATIONS															*
 *======================================================================================*/

/**
 * User code option.
 *
 * This option refers to the user {@link CEntity::Code() code} for the
 * {@link kAPI_OP_LOGIN login} operation.
 */
define( "kAPI_OPT_USER_CODE",		':@user-code' );

/**
 * User password option.
 *
 * This option refers to the user {@link CUser::Password() password} for the
 * {@link kAPI_OP_LOGIN login} operation.
 */
define( "kAPI_OPT_USER_PASS",		':@user-pass' );

?>
