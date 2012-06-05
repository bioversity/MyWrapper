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
 * List terms web-service.
 *
 * This is the tag that represents the list terms web service, it will locate all
 * {@link COntologyTerm terms} matching the provided identifiers in the
 * {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter and return an array
 * structured as follows:
 *
 * <ul>
 *	<li><i>Key</i>: The {@link COntologyTerm term} global {@link kTAG_GID identifier}.
 *	<li><i>Value</i>: The contents of the {@link COntologyTerm term}.
 * </ul>
 *
 * If you omit the {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter it is assumed
 * that you want all terms, in that case the service will enforce the use of
 * {@link kAPI_DATA_PAGING paging} options.
 *
 * Note that the {@link kAPI_CONTAINER container} is not required, if omitted it will be
 * initialised to the {@link kDEFAULT_CNT_TERMS kDEFAULT_CNT_TERMS} constant.
 */
define( "kAPI_OP_GET_TERMS",		'@GET_TERMS' );

/**
 * Match terms web-service.
 *
 * This is the tag that represents the match terms web-service operation, it is equivalent
 * to the inherited {@link kAPI_OP_MATCH kAPI_OP_MATCH} operation, except that it applies
 * to terms and will return the matching combination of terms and nodes.
 *
 * <ul>
 *	<li><i>{@link kAPI_RESPONSE_TERMS kAPI_RESPONSE_TERMS}</i>: The list of terms matched
 *		by the {@link kAPI_OP_MATCH match} service as follows:
 *	 <ul>
 *		<li><i>Key</i>: The {@link COntologyTerm term} global {@link kTAG_GID identifier}.
 *		<li><i>Value</i>: The contents of the {@link COntologyTerm term}.
 *	 </ul>
 *	<li><i>{@link kAPI_RESPONSE_NODES kAPI_RESPONSE_NODES}</i>: The list of found nodes
 *		related to the matched terms as follows:
 *	 <ul>
 *		<li><i>Key</i>: The node ID.
 *		<li><i>Value</i>: The node properties.
 *	 </ul>
 * </ul>
 */
define( "kAPI_OP_MATCH_TERMS",		'@MATCH_TERMS' );

/**
 * List nodes web-service.
 *
 * This is the tag that represents the list nodes web service, it will locate all
 * {@link COntologyNode nodes} matching the provided identifiers in the
 * {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter and return the following
 * structure:
 *
 * <ul>
 *	<li><i>{@link kAPI_RESPONSE_NODES kAPI_RESPONSE_NODES}</i>: The list of found nodes as
 *		follows:
 *	 <ul>
 *		<li><i>Key</i>: The node ID.
 *		<li><i>Value</i>: The node properties.
 *	 </ul>
 *	<li><i>{@link kAPI_RESPONSE_TERMS kAPI_RESPONSE_TERMS}</i>: The list of terms related to
 *		the list of found nodes as follows:
 *	 <ul>
 *		<li><i>Key</i>: The {@link COntologyTerm term} global {@link kTAG_GID identifier}.
 *		<li><i>Value</i>: The contents of the {@link COntologyTerm term}.
 *	 </ul>
 * </ul>
 *
 * If you omit the {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter it is assumed
 * that you want all nodes, in that case the service will enforce the use of
 * {@link kAPI_DATA_PAGING paging} options.
 *
 * Note that the {@link kAPI_CONTAINER container} is not required, if omitted it will be
 * initialised to the {@link kDEFAULT_CNT_NODES kDEFAULT_CNT_NODES} constant.
 */
define( "kAPI_OP_GET_NODES",		'@GET_NODES' );

/**
 * List edges web-service.
 *
 * This is the tag that represents the list edge nodes web service, it will locate all
 * {@link COntologyEdge edges} matching the provided identifiers in the
 * {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter and return the following
 * structure:
 *
 * <ul>
 *	<li><i>{@link kAPI_RESPONSE_EDGES kAPI_RESPONSE_EDGES}</i>: The list of edges as an
 *		array structured as follows:
 *	 <ul>
 *		<li><i>key</i>: The edge identifier.
 *		<li><i>Value</i>: An array structured as follows:
 *		 <ul>
 *			<li><i>{@link kAPI_RESPONSE_SUBJECT kAPI_RESPONSE_SUBJECT}</i>: The subject
 *				{@link COntologyNode node} ID.
 *			<li><i>{@link kAPI_RESPONSE_PREDICATE kAPI_RESPONSE_PREDICATE}</i>: The
 *				predicate {@link COntologyTerm term} {@link kTAG_GID identifier}.
 *			<li><i>{@link kAPI_RESPONSE_OBJECT kAPI_RESPONSE_OBJECT}</i>: The object
 *				{@link COntologyNode node} ID.
 *		 </ul>
 *	 </ul>
 *	<li><i>{@link kAPI_RESPONSE_NODES kAPI_RESPONSE_NODES}</i>: The list of
 *		{@link kAPI_RESPONSE_SUBJECT subject} and {@link kAPI_RESPONSE_OBJECT object}
 *		found nodes as follows:
 *	 <ul>
 *		<li><i>Key</i>: The node ID.
 *		<li><i>Value</i>: The node properties.
 *	 </ul>
 *	<li><i>{@link kAPI_RESPONSE_TERMS kAPI_RESPONSE_TERMS}</i>: The list of terms
 *		related to the list of found nodes and to the edge predicate as follows:
 *	 <ul>
 *		<li><i>Key</i>: The {@link COntologyTerm term} global
 *			{@link kTAG_GID identifier}.
 *		<li><i>Value</i>: The contents of the {@link COntologyTerm term}.
 *	 </ul>
 * </ul>
 *
 * If you omit the {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter it is assumed
 * that you want all edges, in that case the service will enforce the use of
 * {@link kAPI_DATA_PAGING paging} options.
 *
 * Note that the {@link kAPI_CONTAINER container} is not required, if omitted it will be
 * initialised to the {@link kDEFAULT_CNT_EDGES kDEFAULT_CNT_EDGES} constant.
 */
define( "kAPI_OP_GET_EDGES",		'@GET_EDGES' );

/**
 * Get relationships web-service.
 *
 * This is the tag that represents the get relationships web service, it will locate all
 * {@link COntologyEdge edge} nodes related to the {@link COntologyNode node} identifiers
 * provided in the {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter, following
 * the direction provided in the {@link kAPI_OPT_DIRECTION kAPI_OPT_DIRECTION} parameter.
 *
 * Depending on the value of the {@link kAPI_OPT_DIRECTION kAPI_OPT_DIRECTION} parameter:
 *
 * <ul>
 *	<li><i>{@link kAPI_DIRECTION_IN kAPI_DIRECTION_IN}</i>: The service will return all
 *		{@link COntologyEdge edges} that point to the {@link COntologyNode nodes}
 *		provided in the {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter.
 *	<li><i>{@link kAPI_DIRECTION_OUT kAPI_DIRECTION_OUT}</i>: The service will return
 *		all {@link COntologyEdge edges} pointing from the {@link COntologyNode nodes}
 *		provided in the {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter.
 *	<li><i>{@link kAPI_DIRECTION_ALL kAPI_DIRECTION_ALL}</i>: The service will return
 *		all {@link COntologyEdge edges} connected in any way to the
 *		{@link COntologyNode nodes} provided in the
 *		{@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter.
 *	<li><i>{@link kAPI_OPT_DIRECTION kAPI_OPT_DIRECTION} parameter not provided</i>: In this
 *		case the service will assume that the identifiers provided in the
 *		{@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter are edge node IDs, and
 *		it will simply return the matching edges.
 * </ul>
 *
 * The service also expects a {@link kAPI_OPT_LEVELS kAPI_OPT_LEVELS}parameter, a signed
 * integer, that indicates how many levels to recurse the graph traversal, if this parameter
 * is not provided, it will default to 1 level; to traverse all levels this parameter should
 * be set to a negative number; a level of 0 will only return the list of involved nodes and
 * terms.
 *
 * The service will return the same structure as the
 * {@link kAPI_OP_GET_EDGES kAPI_OP_GET_EDGES} service:
 *
 * <ul>
 *	<li><i>{@link kAPI_RESPONSE_EDGES kAPI_RESPONSE_EDGES}</i>: The list of edges as an
 *		array structured as follows:
 *	 <ul>
 *		<li><i>key</i>: The edge identifier.
 *		<li><i>Value</i>: An array structured as follows:
 *		 <ul>
 *			<li><i>{@link kAPI_RESPONSE_SUBJECT kAPI_RESPONSE_SUBJECT}</i>: The subject
 *				{@link COntologyNode node} ID.
 *			<li><i>{@link kAPI_RESPONSE_PREDICATE kAPI_RESPONSE_PREDICATE}</i>: The
 *				predicate {@link COntologyTerm term} {@link kTAG_GID identifier}.
 *			<li><i>{@link kAPI_RESPONSE_OBJECT kAPI_RESPONSE_OBJECT}</i>: The object
 *				{@link COntologyNode node} ID.
 *		 </ul>
 *	 </ul>
 *	<li><i>{@link kAPI_RESPONSE_NODES kAPI_RESPONSE_NODES}</i>: The list of
 *		{@link kAPI_RESPONSE_SUBJECT subject} and {@link kAPI_RESPONSE_OBJECT object}
 *		found nodes as follows:
 *	 <ul>
 *		<li><i>Key</i>: The node ID.
 *		<li><i>Value</i>: The node properties.
 *	 </ul>
 *	<li><i>{@link kAPI_RESPONSE_TERMS kAPI_RESPONSE_TERMS}</i>: The list of terms
 *		related to the list of found nodes and to the edge predicate as follows:
 *	 <ul>
 *		<li><i>Key</i>: The {@link COntologyTerm term} global
 *			{@link kTAG_GID identifier}.
 *		<li><i>Value</i>: The contents of the {@link COntologyTerm term}.
 *	 </ul>
 * </ul>
 *
 * If you provide the {@link kAPI_OPT_PREDICATES kAPI_OPT_PREDICATES} parameter, only those
 * {@link COntologyEdge edges} whose type matches any of the predicate
 * {@link COntologyTerm term} identifiers provided in that parameter will be selected.
 *
 * If you omit the {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter, no elements
 * will be returned. The service does not use {@link kAPI_DATA_PAGING paging} options.
 *
 * Note that the {@link kAPI_CONTAINER container} is not required, if omitted it will be
 * initialised to the {@link kDEFAULT_CNT_TERMS kDEFAULT_CNT_TERMS} constant.
 */
define( "kAPI_OP_GET_RELS",			'@GET_RELS' );

/**
 * Get roots web-service.
 *
 * This is the tag that represents the get roots web service, it will locate all
 * {@link kTYPE_ROOT root} {@link COntologyNode nodes} matching the provided
 * {@link kAPI_DATA_QUERY query}.
 *
 * A node is a root if it has among its {@link COntologyNode::Kind() kinds} the
 * {@link kTYPE_ROOT kTYPE_ROOT} tag.
 *
 * The method will return the following structure:
 *
 * <ul>
 *	<li><i>{@link kAPI_RESPONSE_NODES kAPI_RESPONSE_NODES}</i>: The list of found nodes as
 *		follows:
 *	 <ul>
 *		<li><i>Key</i>: The node ID.
 *		<li><i>Value</i>: The node properties.
 *	 </ul>
 *	<li><i>{@link kAPI_RESPONSE_TERMS kAPI_RESPONSE_TERMS}</i>: The list of terms related to
 *		the list of found nodes as follows:
 *	 <ul>
 *		<li><i>Key</i>: The {@link COntologyTerm term} global {@link kTAG_GID identifier}.
 *		<li><i>Value</i>: The contents of the {@link COntologyTerm term}.
 *	 </ul>
 * </ul>
 *
 * If you omit the {@link kAPI_DATA_QUERY kAPI_DATA_QUERY} parameter, all
 * {@link kTYPE_ROOT root} {@link COntologyNode nodes} will be selected.
 */
define( "kAPI_OP_GET_ROOTS",		'@GET_ROOTS' );

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
 * Predicates option.
 *
 * This option refers to a list of predicate {@link COntologyTerm term} identifiers, this
 * option is used by operations involving the selection of {@link COntologyEdge edge} nodes:
 * only those {@link COntologyEdge edge} nodes referring to any of the provided predicate
 * {@link COntologyTerm term} identifiers will be selected.
 */
define( "kAPI_OPT_PREDICATES",		':@predicates' );

/**
 * Relationship direction.
 *
 * This option is used when retrieving {@link kAPI_OP_GET_RELS edges}: it indicates
 * the direction of the relationships in regard to the node identifiers provided in the
 * {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter:
 *
 * <ul>
 *	<li><i>{@link kAPI_DIRECTION_IN kAPI_DIRECTION_IN}</i>: Incoming relationships, this
 *		will select all elements that point to the objects provided in the
 *		{@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter.
 *	<li><i>{@link kAPI_DIRECTION_OUT kAPI_DIRECTION_OUT}</i>: Outgoing relationships, this
 *		will select all elements pointed to by the objects provided in the
 *		{@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter.
 *	<li><i>{@link kAPI_DIRECTION_ALL kAPI_DIRECTION_ALL}</i>: All relationships, this will
 *		select all elements both pointing to and pointed to by the objects provided in the
 *		{@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter.
 * </ul>
 */
define( "kAPI_OPT_DIRECTION",		':@direction' );

/**
 * Relationship levels.
 *
 * This option is used when retrieving for {@link kAPI_OP_GET_RELS relationships}: it
 * indicates the amount of levels to follow.
 *
 * If the integer parameter is omitted, the service will force a one level step, if the
 * parameter is negative, it means that the service will continue until all levels have been
 * reached.
 */
define( "kAPI_OPT_LEVELS",			':@levels' );

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

/*=======================================================================================
 *	DEFAULT RELATIONSHIP DIRECTIONS														*
 *======================================================================================*/

/**
 * Incoming.
 *
 * This tag indicates an incoming relationship, in other words, all elements that point to
 * the current object.
 */
define( "kAPI_DIRECTION_IN",		'i' );

/**
 * Outgoing.
 *
 * This tag indicates an outgoing relationship, in other words, all elements to which the
 * current object points to.
 */
define( "kAPI_DIRECTION_OUT",		'o' );

/**
 * Incoming and outgoing.
 *
 * This tag indicates incoming and outgoing relationships, in other words, all elements that
 * are related to the current object, both pointing to it and pointed by it.
 */
define( "kAPI_DIRECTION_ALL",		'a' );

?>
