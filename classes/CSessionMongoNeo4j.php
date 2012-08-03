<?php

/**
 * <i>CSessionMongoNeo4j</i> class definition.
 *
 * This file contains the class definition of <b>CSessionMongoNeo4j</b> which overloads its
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
 *								CSessionMongoNeo4j.php									*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CSessionObject.php" );

/**
 * Container.
 *
 * This include file contains the container class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CMongoContainer.php" );

/**
 * Query.
 *
 * This include file contains the query class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CMongoQuery.php" );

/**
 * Graph definitions.
 *
 * This include file contains the graph class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CGraphEdge.php" );

/**
 *	Mongo and Neo4j session object.
 *
 * This concrete class implements a session that uses MongoDB as its data
 * {@link DataStore() store} and Neo4j as its {@link GraphStore() store}.
 *
 * The class does not add any extra functionality to its {@link CSessionObject parent},
 * except that this class can be instantiated.
 *
 *	@package	MyWrapper
 *	@subpackage	Session
 */
class CSessionMongoNeo4j extends CSessionObject
{
		

/*=======================================================================================
 *																						*
 *								PUBLIC RESOURCES INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	DataStore																		*
	 *==================================================================================*/

	/**
	 * Manage the data store reference.
	 *
	 * This method can be used to manage the session's data store reference. The data store
	 * represents the main connection to the database, from which
	 * {@link CContainer container} references can be derived.
	 *
	 * We {@link CSessionObject::DataStore() overload} this method to ensure the provided
	 * parameter is a Mongo object.
	 *
	 * @param mixed					$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @throws {@link CException CException}
	 */
	public function DataStore( $theValue = NULL, $getOld = FALSE )
	{
		//
		// New value.
		//
		if( ($theValue !== NULL)
		 && ($theValue !== FALSE) )
		{
			//
			// Check value.
			//
			if( ! ($theValue instanceof Mongo) )
				throw new CException
					( "Unsupported data store reference",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Reference' => $theValue ) );						// !@! ==>
		
		} // New value.
		
		return parent::DataStore( $theValue, $getOld );								// ==>

	} // DataStore.

	 
	/*===================================================================================
	 *	GraphStore																		*
	 *==================================================================================*/

	/**
	 * Manage the graph store reference.
	 *
	 * This method can be used to manage the session's graph store reference. The graph
	 * store represents the main connection to the graph, the graph database vcient.
	 *
	 * We {@link CSessionObject::GraphStore() overload} this method to ensure the provided
	 * parameter is a Neo4j client object.
	 *
	 * @param mixed					$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @throws {@link CException CException}
	 */
	public function GraphStore( $theValue = NULL, $getOld = FALSE )
	{
		//
		// New value.
		//
		if( ($theValue !== NULL)
		 && ($theValue !== FALSE) )
		{
			//
			// Check value.
			//
			if( ! ($theValue instanceof Everyman\Neo4j\Client) )
				throw new CException
					( "Unsupported graph store reference",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Reference' => $theValue ) );						// !@! ==>
		
		} // New value.
		
		return parent::GraphStore( $theValue, $getOld );							// ==>

	} // GraphStore.

	 
	/*===================================================================================
	 *	Database																		*
	 *==================================================================================*/

	/**
	 * Manage the database reference.
	 *
	 * This method can be used to manage the session's default database reference. This
	 * database represents the connection to the default collections container.
	 *
	 * We {@link CSessionObject::Database() overload} this method to ensure the provided
	 * parameter is a MongoDB object.
	 *
	 * @param mixed					$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @throws {@link CException CException}
	 */
	public function Database( $theValue = NULL, $getOld = FALSE )
	{
		//
		// New value.
		//
		if( ($theValue !== NULL)
		 && ($theValue !== FALSE) )
		{
			//
			// Check value.
			//
			if( ! ($theValue instanceof MongoDB) )
				throw new CException
					( "Unsupported database reference",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Reference' => $theValue ) );						// !@! ==>
		
		} // New value.
		
		return parent::Database( $theValue, $getOld );								// ==>

	} // Database.

	 
	/*===================================================================================
	 *	UsersContainer																	*
	 *==================================================================================*/

	/**
	 * Manage the default users container.
	 *
	 * This method can be used to manage the session's default users container. This object
	 * represents the the container in which all users are stored.
	 *
	 * We {@link CSessionObject::UsersContainer() overload} this method to ensure the
	 * provided parameter is either a MongoCollection or a
	 * {@link CMongoContainer CMongoContainer} object, the latter being the expected type.
	 *
	 * @param mixed					$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @throws {@link CException CException}
	 */
	public function UsersContainer( $theValue = NULL, $getOld = FALSE )
	{
		//
		// New value.
		//
		if( ($theValue !== NULL)
		 && ($theValue !== FALSE) )
		{
			//
			// Convert.
			//
			if( $theValue instanceof MongoCollection )
			{
				//
				// Instantiate container.
				//
				$tmp = new CMongoContainer();
				
				//
				// Set native container.
				//
				$tmp->Container( $theValue );
				
				//
				// Update parameter.
				//
				$theValue = $tmp;
			
			} // MongoCollection.
			
			//
			// Check value.
			//
			if( ! ($theValue instanceof CMongoContainer) )
				throw new CException
					( "Unsupported users container reference",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Reference' => $theValue ) );						// !@! ==>
		
		} // New value.
		
		return parent::UsersContainer( $theValue, $getOld );						// ==>

	} // UsersContainer.

		

/*=======================================================================================
 *																						*
 *								PUBLIC OPERATIONS INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Query																			*
	 *==================================================================================*/

	/**
	 * Return an empty query.
	 *
	 * This method can be used to return a concrete query instance based on the specific
	 * database engines supported by the session.
	 *
	 * In this class we return an empty {@link CMongoQuery CMongoQuery}.
	 *
	 * @access public
	 * @return CQuery
	 */
	public function Query()						{	return new CMongoQuery();	}

		

/*=======================================================================================
 *																						*
 *							PROTECTED INITIALISATION INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_InitDataStore																	*
	 *==================================================================================*/

	/**
	 * Initialise data store.
	 *
	 * This method will initialise the default data store, the method expects one boolean
	 * parameter: <i>TRUE</i> means that the data store is to be set, <i>FALSE</i> means
	 * that the data store is to be reset.
	 *
	 * In this class we initialise a Mongo object.
	 *
	 * @param boolean				$theOperation		TRUE set, FALSE reset.
	 *
	 * @access protected
	 *
	 * @uses DataStore()
	 */
	protected function _InitDataStore( $theOperation )
	{
		//
		// Set.
		//
		if( $theOperation )
			$this->DataStore( new Mongo() );
		
		//
		// Reset.
		//
		else
			$this->DataStore( FALSE );
	
	} // _InitDataStore.

	 
	/*===================================================================================
	 *	_InitGraphStore																	*
	 *==================================================================================*/

	/**
	 * Initialise graph store.
	 *
	 * This method will initialise the default graph store, the method expects one boolean
	 * parameter: <i>TRUE</i> means that the graph store is to be set, <i>FALSE</i> means
	 * that the graph store is to be reset.
	 *
	 * In this class we initialise a Neo4j client using the default
	 * {@link DEFAULT_kNEO4J_HOST host} and {@link DEFAULT_kNEO4J_PORT port}.
	 *
	 * @param boolean				$theOperation		TRUE set, FALSE reset.
	 *
	 * @access protected
	 *
	 * @uses GraphStore()
	 *
	 * @see DEFAULT_kNEO4J_HOST DEFAULT_kNEO4J_PORT
	 */
	protected function _InitGraphStore( $theOperation )
	{
		//
		// Set.
		//
		if( $theOperation )
			$this->GraphStore(
				new Everyman\Neo4j\Client(
					DEFAULT_kNEO4J_HOST, DEFAULT_kNEO4J_PORT ) );
		
		//
		// Reset.
		//
		else
			$this->GraphStore( FALSE );
	
	} // _InitGraphStore.

	 
	/*===================================================================================
	 *	_InitDatabase																	*
	 *==================================================================================*/

	/**
	 * Initialise database.
	 *
	 * This method will initialise the default database connection, the method expects one
	 * boolean parameter: <i>TRUE</i> means that the database connection is to be opened,
	 * <i>FALSE</i> means that the database connection is to be closed.
	 *
	 * In this class we initialise a MongoDB database by the default database
	 * {@link kDEFAULT_DATABASE name}.
	 *
	 * @param boolean				$theOperation		TRUE set, FALSE reset.
	 *
	 * @access protected
	 *
	 * @uses Database()
	 *
	 * @see kDEFAULT_DATABASE
	 */
	protected function _InitDatabase( $theOperation )
	{
		//
		// Check default database.
		//
		if( ! defined( 'kDEFAULT_DATABASE' ) )
			throw new CException
				( "Default database name is undefined",
				  kERROR_OPTION_MISSING,
				  kMESSAGE_TYPE_ERROR,
				  array( 'Symbol' => 'kDEFAULT_DATABASE' ) );					// !@! ==>
		
		//
		// Set.
		//
		if( $theOperation )
			$this->Database(
				$this->DataStore()->
					selectDB(
						kDEFAULT_DATABASE ) );
		
		//
		// Reset.
		//
		else
			$this->Database( FALSE );
	
	} // _InitDatabase.

	 
	/*===================================================================================
	 *	_InitUserContainer																	*
	 *==================================================================================*/

	/**
	 * Initialise users container.
	 *
	 * This method will initialise the default users container, the method expects one
	 * boolean parameter: <i>TRUE</i> means that the users container is to be set,
	 * <i>FALSE</i> means that the users container is to be reset.
	 *
	 * In this class we initialise a MongoCollection container by the default user container
	 * {@link kDEFAULT_CNT_USERS name}.
	 *
	 * @param boolean				$theOperation		TRUE set, FALSE reset.
	 *
	 * @access protected
	 *
	 * @uses UsersContainer()
	 *
	 * @see kDEFAULT_CNT_USERS
	 */
	protected function _InitUserContainer( $theOperation )
	{
		//
		// Set.
		//
		if( $theOperation )
			$this->UsersContainer(
				$this->Database()->
					selectCollection(
						kDEFAULT_CNT_USERS ) );
		
		//
		// Reset.
		//
		else
			$this->UsersContainer( FALSE );
	
	} // _InitUserContainer.

	 

} // class CSessionMongoNeo4j.


?>
