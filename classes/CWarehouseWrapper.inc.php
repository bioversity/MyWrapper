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
 * {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter and the direction provided in
 * the {@link kAPI_OPT_DIRECTION kAPI_OPT_DIRECTION} parameter.
 *
 * Depending on the presence or not of the {@link kAPI_OPT_DIRECTION kAPI_OPT_DIRECTION}
 * parameter:
 *
 * <ul>
 *	<li><i>{@link kAPI_OPT_DIRECTION kAPI_OPT_DIRECTION} not provided</i>: In this case we
 *		assume that the {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter contains
 *		the list of {@link COntologyEdge edge} identifiers to match.
 *	<li><i>{@link kAPI_OPT_DIRECTION kAPI_OPT_DIRECTION} provided</i>: In this case we
 *		assume that the {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter contains
 *		the list of {@link COntologyNode node} identifiers for which we want to retrieve
 *		connected {@link COntologyEdge edges} in the direction provided in the
 *		{@link kAPI_OPT_DIRECTION kAPI_OPT_DIRECTION} parameter:
 *	 <ul>
 *		<li><i>{@link kAPI_DIRECTION_IN kAPI_DIRECTION_IN}</i>: The service will return all
 *			{@link COntologyEdge edges} that point to the {@link COntologyNode nodes}
 *			provided in the {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter.
 *		<li><i>{@link kAPI_DIRECTION_OUT kAPI_DIRECTION_OUT}</i>: The service will return
 *			all {@link COntologyEdge edges} pointing from the {@link COntologyNode nodes}
 *			provided in the {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter.
 *		<li><i>{@link kAPI_DIRECTION_ALL kAPI_DIRECTION_ALL}</i>: The service will return
 *			all {@link COntologyEdge edges} connected in any way to the
 *			{@link COntologyNode nodes} provided in the
 *			{@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter.
 *	 </ul>
 * </ul>
 *
 * The service will return the following structure:
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
 * If you provide the {@link kAPI_OPT_PREDICATES kAPI_OPT_PREDICATES} parameter, only those
 * {@link COntologyEdge edges} whose type matches any of the predicate
 * {@link COntologyTerm term} identifiers provided in that parameter will be selected.
 *
 * If you omit the {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter, no elements
 * will be returned. The service does not use {@link kAPI_DATA_PAGING paging} options.
 */
define( "kAPI_OP_GET_EDGES",		'@GET_EDGES' );

/**
 * Get relations web-service.
 *
 * This is the tag that represents the get relations web service, it will locate all
 * {@link COntologyEdge edges} related to the provided identifiers in the
 * {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter, in the direction provided in
 * the {@link kAPI_OPT_DIRECTION kAPI_OPT_DIRECTION} parameter and for the
 * {@link kAPI_OPT_LEVELS kAPI_OPT_LEVELS} levels.
 *
 * The service expects the same parameters as the
 * {@link kAPI_OP_GET_EDGES kAPI_OP_GET_EDGES} service and returns a similar structure in
 * which the only difference is that the {@link kAPI_RESPONSE_EDGES kAPI_RESPONSE_EDGES}
 * element will be structured as follows:
 *
 * <ul>
 *	<li><i>{@link kAPI_RESPONSE_EDGES kAPI_RESPONSE_EDGES}</i>: The list of edges will be an
 *		array structured as follows:
 *	 <ul>
 *		<li><i>Index</i>: The node identifier provided in the
 *			{@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter.
 *		<li><i>Value</i>: An array of elements structured as follows:
 *		 <ul>
 *			<li><i>{@link kAPI_RESPONSE_SUBJECT kAPI_RESPONSE_SUBJECT}</i>: The subject
 *				{@link COntologyNode node} ID.
 *			<li><i>{@link kAPI_RESPONSE_PREDICATE kAPI_RESPONSE_PREDICATE}</i>: The
 *				predicate {@link COntologyTerm term} {@link kTAG_GID identifier}.
 *			<li><i>{@link kAPI_RESPONSE_OBJECT kAPI_RESPONSE_OBJECT}</i>: The object
 *				{@link COntologyNode node} ID.
 *		 </ul>
 *	 </ul>
 * </ul>
 *
 * For more information consult the {@link kAPI_OP_GET_EDGES kAPI_OP_GET_EDGES} command.
 *
 * If the {@link kAPI_OPT_DIRECTION kAPI_OPT_DIRECTION} parameteris not provided, the
 * service will set it by default to {@link kAPI_DIRECTION_OUT kAPI_DIRECTION_OUT}, which
 * translates to traversing the graph following the subclass predicate towards the root.
 * 
 * If the {@link kAPI_OPT_LEVELS kAPI_OPT_LEVELS} parameter is not provided, it is assumed
 * that no limit is set, which means care should be taken. A negative value means no limit.
 */
define( "kAPI_OP_GET_RELS",			'@GET_RELS' );

/**
 * Query roots web-service.
 *
 * This is the tag that represents the query roots web service, it will locate all
 * {@link kTYPE_ROOT root} {@link COntologyNode nodes} matching the provided
 * attributes in the {@link kAPI_OPT_ATTRIBUTES kAPI_OPT_ATTRIBUTES} parameter and
 * return the following structure:
 *
 * <ul>
 *	<li><i>{@link kAPI_RESPONSE_TERMS kAPI_RESPONSE_TERMS}</i>: The list of terms related to
 *		the list of root nodes as follows:
 *	 <ul>
 *		<li><i>Index</i>: The term {@link kTAG_GID identifier}.
 *		<li><i>Value</i>: The term properties.
 *	 </ul>
 *	<li><i>{@link kAPI_RESPONSE_NODES kAPI_RESPONSE_NODES}</i>: The list of root nodes as
 *		follows:
 *	 <ul>
 *		<li><i>Index</i>: The node ID.
 *		<li><i>Value</i>: The node properties.
 *	 </ul>
 * </ul>
 *
 * If you omit the {@link kAPI_OPT_ATTRIBUTES kAPI_OPT_ATTRIBUTES} parameter, all
 * {@link COntologyNode nodes} with {@link kTYPE_ROOT root}
 * {@link COntologyNode::Kind() kind} will be returned.
 */
define( "kAPI_OP_QUERY_ROOTS",		'@QUERY_ROOTS' );

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
 * Attribute selectors option.
 *
 * This option is used by operations that query {@link COntologyNode nodes}, its content is
 * an array structured as follows:
 *
 * <ul>
 *	<li><i>Index</i>: The array element index is the attribute key.
 *	<li><i>Value</i>: The value is an array of attribute values to be matched.
 * </ul>
 *
 * The resulting query will be composed in <i>AND</i> mode.
 */
define( "kAPI_OPT_ATTRIBUTES",		':@attributes' );

/**
 * Relationship direction.
 *
 * This option is used when retrieving {@link kAPI_OP_GET_EDGES edges}: it indicates
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
