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
 * LOGIN web-service.
 *
 * This is the tag that represents the LOGIN operation, it will check for the
 * {@link kAPI_OPT_USER_CODE user} and {@link kAPI_OPT_USER_PASS password} and match both
 * with a user record.
 */
define( "kAPI_OP_LOGIN",			'@LOGIN' );

/**
 * Get terms web-service.
 *
 * This is the tag that represents the get terms web service, it will locate all
 * {@link COntologyTerm terms} matching the provided identifiers in the
 * {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter and return an array whose key
 * is the provided identifier (converted to a string) and as value the matched object or
 * <i>NULL</i>.
 */
define( "kAPI_OP_GET_TERMS",		'@GET_TERMS' );

/**
 * Get nodes web-service.
 *
 * This is the tag that represents the get nodes web service, it will locate all
 * {@link COntologyNode nodes} matching the provided identifiers in the
 * {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter and return an array whose key
 * is the node ID and as value is the {@link COntology::getArrayCopy() merged} attributes of
 * the node's {@link COntology::Term() term} and the node's properties, or <i>NULL</i>.
 */
define( "kAPI_OP_GET_NODES",		'@GET_NODES' );

/**
 * Query ontologies web-service.
 *
 * This is the tag that represents the query ontologies web service, it will locate all
 * {@link kTYPE_ONTOLOGY ontology} {@link COntologyNode nodes} matching the provided
 * attributes in the {@link kAPI_OPT_NODE_SELECTORS kAPI_OPT_NODE_SELECTORS} parameter and
 * return an array whose key will be the node ID and as value is the
 * {@link COntology::getArrayCopy() merged} attributes of the node's
 * {@link COntology::Term() term} and the node's properties, or <i>NULL</i>.
 */
define( "kAPI_OP_QUERY_ONTOLOGIES",	'@QUERY_ONTOLOGIES' );

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

/**
 * Identifiers option.
 *
 * This option refers to a list of object identifiers, this option is used by assorted
 * operations to receive the list of objects to be retrieved; the type of the list's
 * elements is determined by the operation.
 */
define( "kAPI_OPT_IDENTIFIERS",		':@identifiers' );

/**
 * Attribute selectors option.
 *
 * This option refers to a list of attributes and matching values, this option is used by
 * the nodes {@link kAPI_OP_QUERY_ONTOLOGIES query} operation. The contents are an array
 * indexed by the node attributes whose value is an array of matching values. The resulting
 * query will be composed in <i>AND</i> mode.
 */
define( "kAPI_OPT_NODE_SELECTORS",	':@node-selectors' );

?>
