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
define( "kAPI_OP_LOGIN",				'@LOGIN' );

/**
 * Get users web-service.
 *
 * This command can be used to search for {@link CUser users} according to the parameters
 * provided to the service:
 *
 * <ul>
 *	<li><i>{@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS}</i>: This parameter represents
 *		a list of user identifiers that can be expressed as:
 *	 <ul>
 *		<li><i>string</i>: In this case the value is interpreted as the user code.
 *		<li><i>array</i>: In this case the value is interpreted as the user
 *			{@link kTAG_LID identifier}.
 *	 </ul>
 *	<li><i>{@link kAPI_DATA_QUERY kAPI_DATA_QUERY}</i>: This parameter represents a
 *		{@link CMongoQuery query}.
 * </ul>
 *
 * The two options are not exclusive and will be applied together with an
 * {@link kOPERATOR_AND AND} operator.
 *
 * If you omit the {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter the service
 * will enforce the use of {@link kAPI_DATA_PAGING paging} options.
 *
 * The service will check the {@link kAPI_DATA_SORT kAPI_DATA_SORT} parameter to order the
 * returned records by the list of fields provided in that parameter.
 *
 * The service will also check the {@link kAPI_DATA_FIELD kAPI_DATA_FIELD} parameter to
 * restrict the returned fields to the provided list.
 *
 * In this service the {@link kAPI_CONTAINER container} is not required, if omitted it will
 * be initialised to the {@link kENTITY_CONTAINER kENTITY_CONTAINER} constant.
 *
 * If the service matches users, it will return an array structured as follows:
 *
 * <ul>
 *	<li><i>Key</i>: The {@link CUser user} {@link kTAG_CODE code}.
 *	<li><i>Value</i>: The contents of the {@link CUser user} record.
 * </ul>
 */
define( "kAPI_OP_GET_USERS",			'@GET_USERS' );

/**
 * Get managed users web-service.
 *
 * This command is equivalent to the {@link kAPI_OP_GET_USERS kAPI_OP_GET_USERS} service,
 * except that the identifiers provided in the
 * {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter refer to the
 * {@link CUser user} {@link CUser::Manager() managers}; in other words, you can use this
 * service to retrieve the users managed by a set of other users.
 *
 * If you omit the {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter, it is
 * assumed that the eventual {@link kAPI_DATA_QUERY kAPI_DATA_QUERY} operates on all users
 * that have a {@link kTAG_MANAGER kTAG_MANAGER} tag.
 *
 * All other parameters are handled as in the {@link kAPI_OP_GET_USERS kAPI_OP_GET_USERS}
 * service.
 */
define( "kAPI_OP_GET_MANAGED_USERS",	'@GET_MANAGED_USERS' );

/**
 * Get terms web-service.
 *
 * This command can be used to search for {@link COntologyTerm terms} according to the
 * parameters provided to the service:
 *
 * <ul>
 *	<li><i>{@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS}</i>: This parameter represents
 *		a list of {@link COntologyTerm term} identifiers that can be expressed as:
 *	 <ul>
 *		<li><i>string</i>: In this case the value is interpreted as the term global
 *			{@link kTAG_GID identifier}.
 *		<li><i>array</i>: In this case the value is interpreted as the term local
 *			{@link kTAG_LID identifier}.
 *	 </ul>
 *	<li><i>{@link kAPI_DATA_QUERY kAPI_DATA_QUERY}</i>: This parameter represents a
 *		{@link CMongoQuery query}.
 * </ul>
 *
 * The two options are not exclusive and will be applied together with an
 * {@link kOPERATOR_AND AND} operator.
 *
 * If you omit the {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter the service
 * will enforce the use of {@link kAPI_DATA_PAGING paging} options.
 *
 * The service will check the {@link kAPI_DATA_SORT kAPI_DATA_SORT} parameter to order the
 * returned records by the list of fields provided in that parameter.
 *
 * The service will also check the {@link kAPI_DATA_FIELD kAPI_DATA_FIELD} parameter to
 * restrict the returned fields to the provided list.
 *
 * In this service the {@link kAPI_CONTAINER container} is not required, if omitted it will
 * be initialised to the {@link kDEFAULT_CNT_TERMS kDEFAULT_CNT_TERMS} constant.
 *
 * If the service matches terms, it will return an array structured as follows:
 *
 * <ul>
 *	<li><i>Key</i>: The {@link COntologyTerm term} global {@link kTAG_GID identifier}.
 *	<li><i>Value</i>: The contents of the {@link COntologyTerm term} record.
 * </ul>
 */
define( "kAPI_OP_GET_TERMS",			'@GET_TERMS' );

/**
 * Match terms web-service.
 *
 * This is the tag that represents the match terms web-service operation, it is equivalent
 * to the inherited {@link kAPI_OP_MATCH kAPI_OP_MATCH} operation, except that it applies
 * to {@link COntologyTerm terms} and will return the matching combination of terms and
 * nodes.
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
define( "kAPI_OP_MATCH_TERMS",			'@MATCH_TERMS' );

/**
 * Set tags web-service.
 *
 * This is the tag that represents the set tags web service, it expects the
 * {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter to hold a list of elements
 * that can take the following forms:
 *
 * <ul>
 *	<li><i>Scalar</i>: A scalar is expected if the annotation element is a single node
 *		being both a {@link kTYPE_TRAIT trait} and a {@link kTYPE_MEASURE measure}, in this
 *		case the scalar should be the term {@link COntologyTerm::GID() identifier}.
 *	<li><i>Array</i>: An array is expected if the tag is comprised of a chain of terms.
 *	 <ul>
 *		<li><i>{@link COntologyTerm Terms}</i>: The list of terms must be represented by an
 *			array of term {@link COntologyTerm::GID() identifiers} in which the number of
 *			elements must be odd, the odd elements represent {@link COntologyNode node}
 *			{@link COntologyTerm term} references and the even elements represent
 *			{@link COntologyEdge edge} predicate {@link COntologyTerm terms}.
 *		<li><i>{@link COntologyEdge Edges}</i>: The list of edges must be represented as an
 *			array of integers representing {@link COntologyEdge edge} identifiers. This edge
 *			sequence will ultimately be transformed in a chain of terms that will be matched
 *			in the database.
 *	 </ul>
 * </ul>
 *
 * The service will check if the provided chain of {@link COntologyTerm terms} exists, in
 * that case it will return the found {@link COntologyTag record}; if not, it will create a
 * new {@link COntologyTag record}.
 *
 * Note that this service will instantiate term {@link COntologyTag paths}, so call
 * this service only if you are sure you need to do it; if any error occurs, the operation
 * will be aborted (but the eventual created annotations will not).
 *
 * The service will return the result in the same format as the
 * {@link kAPI_OP_GET_TAGS kAPI_OP_GET_TAGS} service.
 */
define( "kAPI_OP_SET_TAGS",			'@SET_TAGS' );

/**
 * Get tags web-service.
 *
 * This command can be used to search for {@link COntologyTag tags} according to the
 * parameters provided to the service:
 *
 * <ul>
 *	<li><i>{@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS}</i>: This parameter represents
 *		a list of {@link COntologyTag tag} identifiers that can be expressed as:
 *	 <ul>
 *		<li><i>string</i>: In this case the value is interpreted as the tag global
 *			{@link kTAG_GID identifier}.
 *		<li><i>integer</i>: In this case the value is interpreted as the tag local
 *			{@link kTAG_LID identifier}.
 *	 </ul>
 *	<li><i>{@link kAPI_DATA_QUERY kAPI_DATA_QUERY}</i>: This parameter represents a
 *		{@link CMongoQuery query}.
 * </ul>
 *
 * The two options are not exclusive and will be applied together with an
 * {@link kOPERATOR_AND AND} operator.
 *
 * If you omit the {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter the service
 * will enforce the use of {@link kAPI_DATA_PAGING paging} options.
 *
 * The service will check the {@link kAPI_DATA_SORT kAPI_DATA_SORT} parameter to order the
 * returned records by the list of fields provided in that parameter.
 *
 * The service will also check the {@link kAPI_DATA_FIELD kAPI_DATA_FIELD} parameter to
 * restrict the returned fields to the provided list.
 *
 * In this service the {@link kAPI_CONTAINER container} is not required, if omitted it will
 * be initialised to the {@link kDEFAULT_CNT_TAGS kDEFAULT_CNT_TAGS} constant.
 *
 * If the service matches tags, it will return an array structured as follows:
 *
 * <ul>
 *	<li><i>Key</i>: The {@link COntologyTag tag} global {@link kTAG_GID identifier}.
 *	<li><i>Value</i>: The contents of the {@link COntologyTag tag} record.
 * </ul>
 */
define( "kAPI_OP_GET_TAGS",				'@GET_TAGS' );

/**
 * Get nodes web-service.
 *
 * This command can be used to search for {@link COntologyNode nodes} according to the
 * parameters provided to the service:
 *
 * <ul>
 *	<li><i>{@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS}</i>: This parameter represents
 *		a list of {@link COntologyNode node} identifiers that are be expressed as an
 *		integer.
 *	<li><i>{@link kAPI_DATA_QUERY kAPI_DATA_QUERY}</i>: This parameter represents a
 *		{@link CMongoQuery query}.
 * </ul>
 *
 * The two options are not exclusive and will be applied together with an
 * {@link kOPERATOR_AND AND} operator.
 *
 * If you omit the {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter the service
 * will enforce the use of {@link kAPI_DATA_PAGING paging} options.
 *
 * The service will check the {@link kAPI_DATA_SORT kAPI_DATA_SORT} parameter to order the
 * returned records by the list of fields provided in that parameter, this option applies
 * only to the list of returned {@link COntologyNode nodes}.
 *
 * The service will also check the {@link kAPI_DATA_FIELD kAPI_DATA_FIELD} parameter to
 * restrict the returned fields to the provided list, note that this option will only apply
 * to the fields of the related {@link COntologyTerm terms}, the {@link COntologyNode node}
 * records will be returned complete.
 *
 * In this service the {@link kAPI_CONTAINER container} is not required, if omitted it will
 * be initialised to the {@link kDEFAULT_CNT_NODES kDEFAULT_CNT_NODES} constant.
 *
 * If the service matches nodes, it will return an array structured as follows:
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
 */
define( "kAPI_OP_GET_NODES",		'@GET_NODES' );

/**
 * Get roots web-service.
 *
 * This command can be used to search {@link kTYPE_ROOT root} {@link COntologyNode nodes}
 * matching the same parameters as the {@link kAPI_OP_GET_NODES kAPI_OP_GET_NODES} service.
 *
 * This service is functionally equivalent to {@link kAPI_OP_GET_NODES kAPI_OP_GET_NODES}
 * except that a condition is added by default in which the selected nodes must have the
 * {@link kTYPE_ROOT root} {@link kTAG_KIND kid}.
 */
define( "kAPI_OP_GET_ROOTS",		'@GET_ROOTS' );

/**
 * Get edges web-service.
 *
 * This command can be used to search for {@link COntologyEdge edges} according to the
 * parameters provided to the service:
 *
 * <ul>
 *	<li><i>{@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS}</i>: This parameter represents
 *		a list of {@link COntologyEdge edge} identifiers that are be expressed as an
 *		integer.
 *	<li><i>{@link kAPI_DATA_QUERY kAPI_DATA_QUERY}</i>: This parameter represents a
 *		{@link CMongoQuery query}.
 * </ul>
 *
 * The two options are not exclusive and will be applied together with an
 * {@link kOPERATOR_AND AND} operator.
 *
 * If you omit the {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter the service
 * will enforce the use of {@link kAPI_DATA_PAGING paging} options.
 *
 * The service accepts a parameter, {@link kAPI_OPT_PREDICATES kAPI_OPT_PREDICATES}, which
 * represents a list of predicates, provided as a list of {@link COntologyTerm term}
 * identifiers, to take into consideration: if the provided
 * {@link kAPI_OPT_PREDICATES_INC kAPI_OPT_PREDICATES_INC} parameter resolves to <i>TRUE</i>
 * boolean the list of predicates will be considered as an inclusion, that is, only edges
 * containing one of the predicates in the list will be selected; if the
 * {@link kAPI_OPT_PREDICATES_INC kAPI_OPT_PREDICATES_INC} parameter resolves to
 * <i>FALSE</i>, only edges whose type is not among the list will be selected. If the
 * {@link kAPI_OPT_PREDICATES_INC kAPI_OPT_PREDICATES_INC} parameter is omitted, it will
 * default to <i>TRUE</i>.
 *
 * The service will check the {@link kAPI_DATA_SORT kAPI_DATA_SORT} parameter to order the
 * returned records by the list of fields provided in that parameter, this option applies
 * only to the list of returned {@link COntologyEdge edges}.
 *
 * The service will also check the {@link kAPI_DATA_FIELD kAPI_DATA_FIELD} parameter to
 * restrict the returned fields to the provided list, note that this option will only apply
 * to the fields of the related {@link COntologyTerm terms}, the {@link COntologyEdge edge}
 * and {@link COntologyNode node} records will be returned complete.
 *
 * In this service the {@link kAPI_CONTAINER container} is not required, if omitted it will
 * be initialised to the {@link kDEFAULT_CNT_EDGES kDEFAULT_CNT_EDGES} constant.
 *
 * If the service matches edges, it will return an array structured as follows:
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
 * The service also expects a {@link kAPI_OPT_LEVELS kAPI_OPT_LEVELS} parameter, a signed
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
 * The record {@link kAPI_AFFECTED_COUNT count} refers to the edges count.
 *
 * Note that the {@link kAPI_CONTAINER container} is not required, if omitted it will be
 * initialised to the {@link kDEFAULT_CNT_TERMS kDEFAULT_CNT_TERMS} constant.
 */
define( "kAPI_OP_GET_RELS",			'@GET_RELS' );

/**
 * List datasets web-service.
 *
 * This command can be used to search for {@link CDataset datasets} according to the
 * parameters provided to the service:
 *
 * <ul>
 *	<li><i>{@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS}</i>: This parameter represents
 *		a list of {@link CDataset dataset} identifiers that can be expressed as:
 *	 <ul>
 *		<li><i>string</i>: In this case the value is interpreted as the dataset global
 *			{@link kTAG_GID identifier}.
 *		<li><i>array</i>: In this case the value is interpreted as the dataset local
 *			{@link kTAG_LID identifier}.
 *	 </ul>
 *	<li><i>{@link kAPI_DATA_QUERY kAPI_DATA_QUERY}</i>: This parameter represents a
 *		{@link CMongoQuery query}.
 * </ul>
 *
 * The two options are not exclusive and will be applied together with an
 * {@link kOPERATOR_AND AND} operator.
 *
 * If you omit the {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter the service
 * will enforce the use of {@link kAPI_DATA_PAGING paging} options.
 *
 * The service will check the {@link kAPI_DATA_SORT kAPI_DATA_SORT} parameter to order the
 * returned records by the list of fields provided in that parameter.
 *
 * The service will also check the {@link kAPI_DATA_FIELD kAPI_DATA_FIELD} parameter to
 * restrict the returned fields to the provided list.
 *
 * In this service the {@link kAPI_CONTAINER container} is not required, if omitted it will
 * be initialised to the {@link kDEFAULT_CNT_DATASET kDEFAULT_CNT_DATASET} constant.
 *
 * If the service matches datasets, it will return an array structured as follows:
 *
 * <ul>
 *	<li><i>Key</i>: The {@link CDataset dataset} global {@link kTAG_GID identifier}.
 *	<li><i>Value</i>: The contents of the {@link CDataset dataset}.
 * </ul>
 */
define( "kAPI_OP_GET_DATASETS",		'@GET_DATASETS' );

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
 *
 * Note that this parameter depends on the
 * {@link kAPI_OPT_PREDICATES_INC kAPI_OPT_PREDICATES_INC} parameter: if the latter is 1, it
 * means that only edges whose predicate is among this list will be selected; if the value
 * is 0, only edges whose predicate is not among this list will be selected.
 */
define( "kAPI_OPT_PREDICATES",		':@predicates' );

/**
 * Predicates inclusion option.
 *
 * This option refers to the list of {@link kAPI_OPT_PREDICATES predicates}: it is a boolean
 * (1/0) flag that determines whether the {@link kAPI_OPT_PREDICATES predicates} list is to
 * be considered as inclusive or exclusive. If omitted it defaults to 1 (inclusion).
 */
define( "kAPI_OPT_PREDICATES_INC",	':@predicates-inc' );

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
