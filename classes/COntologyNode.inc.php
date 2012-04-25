<?php

/*=======================================================================================
 *																						*
 *									COntologyNode.inc.php								*
 *																						*
 *======================================================================================*/
 
/**
 * {@link COntologyNode COntologyNode} definitions.
 *
 * This file contains common definitions used by the {@link COntologyNode COntologyNode}
 * class.
 *
 *	@package	MyWrapper
 *	@subpackage	Entities
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
define( "kINDEX_NODE_TERM",						'NODES' );

?>
