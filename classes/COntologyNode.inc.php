<?php

/*=======================================================================================
 *																						*
 *								COntologyNode.inc.php									*
 *																						*
 *======================================================================================*/
 
/**
 * {@link COntologyNode COntologyNode} definitions.
 *
 * This file contains common definitions used by the {@link COntologyNode COntologyNode}
 * class.
 *
 *	@package	MyWrapper
 *	@subpackage	Ontology
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 21/04/2012
 */

/*=======================================================================================
 *	DEFAULT INDEX NAMES																	*
 *======================================================================================*/

/**
 * Terms index.
 *
 * This tag defines the index (NodeIndex) collecting ontology node term references. This
 * index will link the node to the term via the following keys:
 *
 * <ul>
 *	<li><i>{@link kTAG_TERM kTAG_TERM}</i>: This key links the current node to the node's
 *		{@link COntologyTerm term} {@link kTAG_GID identifiers}.
 *	<li><i>{@link kTAG_NAME kTAG_NAME}</i>: This key links the current node to the node's
 *		{@link COntologyTerm term} {@link kTAG_NAME names}.
 * </ul>
 */
define( "kINDEX_NODE_TERM",						'TERMS' );

/**
 * Nodes index.
 *
 * This tag defines the index (NodeIndex) collecting ontology node properties. This
 * index will link the node to its properties via the following keys:
 *
 * <ul>
 *	<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>: This key links the current node to its
 *		{@link COntologyNode::Type() type}, which may either be inherited from its
 *		{@link COntologyNode::Term() term} or have been
 *		{@link COntologyNode::Type() explicitly} set.
 *	<li><i>{@link kTAG_KIND kTAG_KIND}</i>: This key links the current node to its
 *		{@link COntologyNode::Kind() kinds}, which may either be inherited from its
 *		{@link COntologyNode::Term() term} or have been
 *		{@link COntologyNode::Kind() explicitly} set.
 *	<li><i>{@link kTAG_DOMAIN kTAG_DOMAIN}</i>: This key links the current node to its
 *		{@link COntologyNode::Domain() domains}, which may either be inherited from its
 *		{@link COntologyNode::Term() term} or have been
 *		{@link COntologyNode::Domain() explicitly} set.
 *	<li><i>{@link kTAG_CATEGORY kTAG_CATEGORY}</i>: This key links the current node to its
 *		{@link COntologyNode::Category() categories}, which may either be inherited from its
 *		{@link COntologyNode::Category() term} or have been
 *		{@link COntologyNode::Category() explicitly} set.
 * </ul>
 */
define( "kINDEX_NODE_NODE",						'NODES' );

?>
