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
 * is the provided identifier and as value the matched object or <i>NULL</i>. If you omit
 * the {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter it mis assumed that you
 * want all terms, in that case the service will enforce the use of
 * {@link kAPI_DATA_PAGING paging} options.
 */
define( "kAPI_OP_GET_TERMS",		'@GET_TERMS' );

/**
 * Get nodes web-service.
 *
 * This is the tag that represents the get nodes web service, it will locate all
 * {@link COntologyNode nodes} matching the provided identifiers in the
 * {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter and return the following
 * structure:
 *
 * <ul>
 *	<li><i>{@link kAPI_RESPONSE_TERMS kAPI_RESPONSE_TERMS}</i>: The list of terms related to
 *		the list of nodes as follows:
 *	 <ul>
 *		<li><i>Index</i>: The term {@link kTAG_GID identifier}.
 *		<li><i>Value</i>: The term properties.
 *	 </ul>
 *	<li><i>{@link kAPI_RESPONSE_NODES kAPI_RESPONSE_NODES}</i>: The list of nodes as
 *		follows:
 *	 <ul>
 *		<li><i>Index</i>: The node ID.
 *		<li><i>Value</i>: The node properties.
 *	 </ul>
 * </ul>
 *
 * If you omit the {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter, no elements
 * will be returned. The service does not use {@link kAPI_DATA_PAGING paging} options.
 */
define( "kAPI_OP_GET_NODES",		'@GET_NODES' );

/**
 * Get edges web-service.
 *
 * This is the tag that represents the get edges web service, it will locate all
 * {@link COntologyEdge edges} matching the provided identifiers in the
 * {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter and return the following
 * structure:
 *
 * <ul>
 *	<li><i>{@link kAPI_RESPONSE_TERMS kAPI_RESPONSE_TERMS}</i>: The list of terms related to
 *		the list of subject and object nodes and the list of predicate terms as follows:
 *	 <ul>
 *		<li><i>Index</i>: The term {@link kTAG_GID identifier}.
 *		<li><i>Value</i>: The term properties.
 *	 </ul>
 *	<li><i>{@link kAPI_RESPONSE_NODES kAPI_RESPONSE_NODES}</i>: The list of subject and
 *		object nodes as follows:
 *	 <ul>
 *		<li><i>Index</i>: The node ID.
 *		<li><i>Value</i>: The node properties.
 *	 </ul>
 *	<li><i>{@link kAPI_RESPONSE_EDGES kAPI_RESPONSE_EDGES}</i>: The list of edges as an
 *		array of elements structured as follows:
 *	 <ul>
 *		<li><i>{@link kAPI_RESPONSE_SUBJECT kAPI_RESPONSE_SUBJECT}</i>: The subject
 *			{@link COntologyNode node} ID.
 *		<li><i>{@link kAPI_RESPONSE_PREDICATE kAPI_RESPONSE_PREDICATE}</i>: The predicate
 *			{@link COntologyTerm term} {@link kTAG_GID identifier}.
 *		<li><i>{@link kAPI_RESPONSE_OBJECT kAPI_RESPONSE_OBJECT}</i>: The object
 *			{@link COntologyNode node} ID.
 *	 </ul>
 * </ul>
 *
 * If you omit the {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter, no elements
 * will be returned. The service does not use {@link kAPI_DATA_PAGING paging} options.
 */
define( "kAPI_OP_GET_EDGES",		'@GET_EDGES' );

/**
 * Query ontologies web-service.
 *
 * This is the tag that represents the query ontologies web service, it will locate all
 * {@link kTYPE_ONTOLOGY ontology} {@link COntologyNode nodes} matching the provided
 * attributes in the {@link kAPI_OPT_NODE_SELECTORS kAPI_OPT_NODE_SELECTORS} parameter and
 * return the following structure:
 *
 * <ul>
 *	<li><i>{@link kAPI_RESPONSE_TERMS kAPI_RESPONSE_TERMS}</i>: The list of terms related to
 *		the list of ontologies as follows:
 *	 <ul>
 *		<li><i>Index</i>: The term {@link kTAG_GID identifier}.
 *		<li><i>Value</i>: The term properties.
 *	 </ul>
 *	<li><i>{@link kAPI_RESPONSE_NODES kAPI_RESPONSE_NODES}</i>: The list of ontologies as
 *		follows:
 *	 <ul>
 *		<li><i>Index</i>: The node ID.
 *		<li><i>Value</i>: The node properties.
 *	 </ul>
 * </ul>
 *
 * If you omit the {@link kAPI_OPT_NODE_SELECTORS kAPI_OPT_NODE_SELECTORS} parameter, all
 * {@link COntologyNode nodes} with {@link kTYPE_ONTOLOGY ontology}
 * {@link COntologyNode::Kind() kind} will be returned.
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

/*=======================================================================================
 *	DEFAULT RESPONSE TAGS																*
 *======================================================================================*/

/**
 * Terms.
 *
 * This tag will hold the list of terms.
 */
define( "kAPI_RESPONSE_TERMS",		'terms' );

/**
 * Nodes.
 *
 * This tag will hold the list of nodes.
 */
define( "kAPI_RESPONSE_NODES",		'nodes' );

/**
 * Edges.
 *
 * This tag will hold the list of edges.
 */
define( "kAPI_RESPONSE_EDGES",		'edges' );

/*=======================================================================================
 *	DEFAULT RELATIONSHIP TAGS															*
 *======================================================================================*/

/**
 * Subject.
 *
 * This tag {@link kAPI_RESPONSE_NODES refers} to the subject {@link COntologyNode node} of
 * a relationship.
 */
define( "kAPI_RESPONSE_SUBJECT",	's' );

/**
 * Predicate.
 *
 * This tag {@link kAPI_RESPONSE_TERMS refers} to the predicate {@link COntologyTerm term}
 * of a relationship.
 */
define( "kAPI_RESPONSE_PREDICATE",	'p' );

/**
 * Object.
 *
 * This tag {@link kAPI_RESPONSE_NODES refers} to the object {@link COntologyNode node} of
 * a relationship.
 */
define( "kAPI_RESPONSE_OBJECT",		'o' );

?>
