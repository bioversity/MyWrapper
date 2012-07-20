<?php

/**
 * <i>CWarehouseSession</i> class definition.
 *
 * This file contains the class definition of <b>CWarehouseSession</b> which overloads its
 * {@link CSessionObject ancestor} to implement a session that uses MongoDB as the database
 * and Neo4j as the graph.
 *
 *	@package	MyWrapper
 *	@subpackage	Session
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 21/07/2012
*/

/*=======================================================================================
 *																						*
 *								CWarehouseSession.php									*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CSessionMongoNeo4j.php" );

/**
 * Ontology trait.
 *
 * This include file contains the ontology trait definitions.
 */
require_once( kPATH_LIBRARY_TRAITS."TSessionMongoNeo4jOntology.php" );

/**
 *	Warehouse session object.
 *
 * This concrete class implements a data warehouse session object, it features a Mongo
 * data {@link DataStore() store} and a Neo4j Graph {@link GraphStore() store}, it also
 * features the ontology {@link TSessionMongoNeo4jOntology trait}.
 *
 *	@package	MyWrapper
 *	@subpackage	Session
 */
class CWarehouseSession extends CSessionMongoNeo4j
{
	use
	
	/**
	 * Ontology trait.
	 *
	 * This trait implements the necessary session elements to handle ontologies.
	 */
	 TSessionMongoNeo4jOntology;

	 

} // class CWarehouseSession.


?>
