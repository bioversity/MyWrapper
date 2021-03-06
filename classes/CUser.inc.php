<?php

/*=======================================================================================
 *																						*
 *									CUser.inc.php										*
 *																						*
 *======================================================================================*/
 
/**
 * {@link CUser CUser} definitions.
 *
 * This file contains common definitions used by the {@link CUser CUser} class.
 *
 *	@package	MyWrapper
 *	@subpackage	Entities
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 30/03/2012
 */

/*=======================================================================================
 *	DEFAULT OBJECT TAGS																	*
 *======================================================================================*/

/**
 * Entity type.
 *
 * This value defines the user entity type.
 */
define( "kENTITY_USER",							':ENTITY:USER' );

/*=======================================================================================
 *	DEFAULT ROLES																		*
 *======================================================================================*/

/**
 * Manage users.
 *
 * This value defines the manage users role, this allows creating and modifying users.
 */
define( "kROLE_USER_MANAGE",					':ROLE:USER-MANAGE' );

/**
 * File import.
 *
 * This value defines the file import role, this allows importing files in datasets.
 */
define( "kROLE_FILE_IMPORT",					':ROLE:FILE-IMPORT' );

?>
