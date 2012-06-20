<?php

/*=======================================================================================
 *																						*
 *									CGraphNode.inc.php									*
 *																						*
 *======================================================================================*/
 
/**
 *	{@link CGraphNode CGraphNode} definitions.
 *
 *	This file contains common definitions used by the {@link CGraphNode CGraphNode} class.
 *
 *	@package	MyWrapper
 *	@subpackage	Persistence
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 19/04/2012
 */

/*=======================================================================================
 *	CLASS AUTOLOADER																	*
 *======================================================================================*/

/**
 * This section allows automatic inclusion of the library classes.
 */
function Neo4jAutoload( $theClassName )
{
	$_path = kPATH_LIBRARY_NEO4J.'lib/'
			.str_replace( '\\', DIRECTORY_SEPARATOR, $theClassName )
			.'.php';
	if( file_exists( $_path ) )
		require_once( $_path );

} spl_autoload_register( 'Neo4jAutoload' );

/*=======================================================================================
 *	REFERENCE NEO4J CLASSES																*
 *======================================================================================*/

use Everyman\Neo4j\Transport,
	Everyman\Neo4j\Client,
	Everyman\Neo4j\Index\NodeIndex,
	Everyman\Neo4j\Index\RelationshipIndex,
	Everyman\Neo4j\Index\NodeFulltextIndex,
	Everyman\Neo4j\Node,
	Everyman\Neo4j\Path,
	Everyman\Neo4j\PathFinder,
	Everyman\Neo4j\Relationship,
	Everyman\Neo4j\Batch;

?>
