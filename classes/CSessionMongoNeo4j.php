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
	public function Query()									{	return new CMongoQuery();	}

		

/*=======================================================================================
 *																						*
 *							PROTECTED INITIALISATION INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_Init																			*
	 *==================================================================================*/

	/**
	 * Initialise resources.
	 *
	 * This method is called when {@link __construct() instantiating} the object, its duty
	 * is to initialise the required resources.
	 *
	 * In this class we initialise the {@link _InitDataStore() data} and
	 * {@link _InitGraphStore() graph} stores, the {@link _InitDatabase() database} and the
	 * users {@link _InitUserContainer() container}.
	 *
	 * @access protected
	 *
	 * @uses _InitDataStore()
	 * @uses _InitGraphStore()
	 * @uses _InitDatabase()
	 * @uses _InitUserContainer()
	 */
	protected function _Init()
	{
		//
		// Call parent method.
		//
		parent::_Init();
		
		//
		// Initialise local resources.
		//
		$this->_InitGraphStore();
		$this->_InitDataStore();
		$this->_InitDatabase();
		$this->_InitUserContainer();
	
	} // _Init.

	 
	/*===================================================================================
	 *	_InitGraphStore																	*
	 *==================================================================================*/

	/**
	 * Initialise graph store.
	 *
	 * The duty of this method is to initialise the graph store.
	 *
	 * In this class we initialise a Neo4j client using the default
	 * {@link kDEFAULT_kNEO4J_HOST host} and {@link kDEFAULT_kNEO4J_PORT port}.
	 *
	 * @param string				$theHost			Eventual custom host or transport.
	 * @param string				$thePort			Eventual custom port.
	 *
	 * @access protected
	 *
	 * @throws {@link CException CException}
	 *
	 * @uses GraphStore()
	 *
	 * @see kDEFAULT_kNEO4J_HOST kDEFAULT_kNEO4J_PORT
	 */
	protected function _InitGraphStore( $theHost = NULL, $thePort = NULL )
	{
		//
		// Set default host.
		//
		if( $theHost === NULL )
		{
			//
			// Check host.
			//
			if( ! defined( 'kDEFAULT_kNEO4J_HOST' ) )
				throw new CException
					( "Default graph host name is undefined",
					  kERROR_OPTION_MISSING,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Symbol' => 'kDEFAULT_kNEO4J_HOST' ) );			// !@! ==>
			
			//
			// Set host.
			//
			$theHost = kDEFAULT_kNEO4J_HOST;
		
		} // Missing host.
		
		//
		// Set default port.
		//
		if( $theHost === NULL )
		{
			//
			// Check port.
			//
			if( ! defined( 'kDEFAULT_kNEO4J_PORT' ) )
				throw new CException
					( "Default graph port number is undefined",
					  kERROR_OPTION_MISSING,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Symbol' => 'kDEFAULT_kNEO4J_PORT' ) );				// !@! ==>
			
			//
			// Set port.
			//
			$thePort = kDEFAULT_kNEO4J_PORT;
		
		} // Missing host.
		
		//
		// Init graph store.
		//
		$this->GraphStore(
			new Everyman\Neo4j\Client(
				$theHost, $thePort ) );
	
	} // _InitGraphStore.

	 
	/*===================================================================================
	 *	_InitDataStore																	*
	 *==================================================================================*/

	/**
	 * Initialise data store.
	 *
	 * The duty of this method is to initialise the data store.
	 *
	 * In this class we simply instantiate a new Mongo.
	 *
	 * @param mixed					$theData			Eventual custom data store.
	 *
	 * @access protected
	 *
	 * @uses DataStore()
	 */
	protected function _InitDataStore( $theData = NULL )
	{
		//
		// Use default value.
		//
		if( $theData === NULL )
			$theData = new Mongo();
		
		//
		// Set data store.
		//
		$this->DataStore( $theData );
	
	} // _InitDataStore.

	 
	/*===================================================================================
	 *	_InitDatabase																	*
	 *==================================================================================*/

	/**
	 * Initialise database.
	 *
	 * The duty of this method is to initialise the database.
	 *
	 * In this class we initialise a MongoDB database by the default database
	 * {@link kDEFAULT_DATABASE name}.
	 *
	 * @param mixed					$theData			Eventual custom database name.
	 *
	 * @access protected
	 *
	 * @throws {@link CException CException}
	 *
	 * @uses Database()
	 *
	 * @see kDEFAULT_DATABASE
	 */
	protected function _InitDatabase( $theData = NULL )
	{
		//
		// Set default name.
		//
		if( $theData === NULL )
		{
			//
			// Check symbol.
			//
			if( ! defined( 'kDEFAULT_DATABASE' ) )
				throw new CException
					( "Default database name is undefined",
					  kERROR_OPTION_MISSING,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Symbol' => 'kDEFAULT_DATABASE' ) );					// !@! ==>
			
			//
			// Set port.
			//
			$theData = kDEFAULT_DATABASE;
		
		} // Missing host.
		
		//
		// Init database.
		//
		$this->Database(
			$this->DataStore()->
				selectDB(
					$theData ) );
	
	} // _InitDatabase.

	 
	/*===================================================================================
	 *	_InitUserContainer																	*
	 *==================================================================================*/

	/**
	 * Initialise users container.
	 *
	 * The duty of this method is to initialise the user container.
	 *
	 * In this class we initialise a MongoCollection container by the default user container
	 * {@link kDEFAULT_CNT_USERS name}.
	 *
	 * @param mixed					$theData			Eventual custom container name.
	 *
	 * @access protected
	 *
	 * @throws {@link CException CException}
	 *
	 * @uses UsersContainer()
	 *
	 * @see kDEFAULT_CNT_USERS
	 */
	protected function _InitUserContainer( $theData = NULL )
	{
		//
		// Set default name.
		//
		if( $theData === NULL )
		{
			//
			// Check symbol.
			//
			if( ! defined( 'kDEFAULT_CNT_USERS' ) )
				throw new CException
					( "Default users container name is undefined",
					  kERROR_OPTION_MISSING,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Symbol' => 'kDEFAULT_CNT_USERS' ) );				// !@! ==>
			
			//
			// Set port.
			//
			$theData = kDEFAULT_CNT_USERS;
		
		} // Missing host.
		
		//
		// Open connection.
		//
		$this->UsersContainer(
			$this->Database()->
				selectCollection(
					$theData ) );
	
	} // _InitUserContainer.

		

/*=======================================================================================
 *																						*
 *							PROTECTED SERIALISATION INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_Serialise																		*
	 *==================================================================================*/

	/**
	 * Serialise object.
	 *
	 * This method is called before the object gets {@link serialize() serialized}, its duty
	 * is to normalise the object's properties before these get serialized.
	 *
	 * In this class we {@link _SerialiseUsersContainer() replace} the users
	 * {@link UsersContainer() container} with its name,
	 * the {@link Database() database} {@link _SerialiseDatabase() with} its name and the
	 * graph {@link GraphStore() store} {@link _SerialiseGraphStore() with} its transport.
	 *
	 * We need to do the above because those objects do not serialize correctly.
	 *
	 * @access protected
	 *
	 * @uses _SerialiseUsersContainer()
	 * @uses _SerialiseDatabase()
	 * @uses _SerialiseDataStore()
	 * @uses _SerialiseGraphStore()
	 */
	protected function _Serialise()
	{
		//
		// Call parent method.
		//
		parent::_Serialise();
		
		//
		// Serialise properties.
		//
		$this->_SerialiseUsersContainer();
		$this->_SerialiseDatabase();
		$this->_SerialiseDataStore();
		$this->_SerialiseGraphStore();
		
	} // _Serialise.

		
	/*===================================================================================
	 *	_SerialiseUsersContainer														*
	 *==================================================================================*/

	/**
	 * Serialise users container.
	 *
	 * This method will replace the current object's users container
	 * {@link UsersContainer() property} with its name, because the container object cannot
	 * be serialized.
	 *
	 * Note that we do not use the accessor method, because we are setting the data member
	 * to an unsupported data type.
	 *
	 * @access protected
	 *
	 * @uses UsersContainer()
	 */
	protected function _SerialiseUsersContainer()
	{
		//
		// Handle users container.
		//
		$data = $this->UsersContainer();
		if( $data !== NULL )
			$this->mUsersContainer = $data->Container()->getName();
	
	} // _SerialiseUsersContainer.

		
	/*===================================================================================
	 *	_SerialiseDatabase																*
	 *==================================================================================*/

	/**
	 * Serialise database.
	 *
	 * This method will replace the current object's database {@link Database() property}
	 * with its name, because the database object cannot be serialized.
	 *
	 * Note that we do not use the accessor method, because we are setting the data member
	 * to an unsupported data type.
	 *
	 * @access protected
	 *
	 * @uses Database()
	 */
	protected function _SerialiseDatabase()
	{
		//
		// Handle database.
		//
		$data = $this->Database();
		if( $data !== NULL )
			$this->mDatabase = (string) $data;
	
	} // _SerialiseDatabase.

		
	/*===================================================================================
	 *	_SerialiseDataStore																*
	 *==================================================================================*/

	/**
	 * Serialise data store.
	 *
	 * This method will reset the current object's data store {@link DataStore() property},
	 * because the object cannot be serialized.
	 *
	 * @access protected
	 *
	 * @uses DataStore()
	 */
	protected function _SerialiseDataStore()				{	$this->DataStore( FALSE );	}

		
	/*===================================================================================
	 *	_SerialiseGraphStore															*
	 *==================================================================================*/

	/**
	 * Serialise graph store.
	 *
	 * This method will replace the current object's graph store
	 * {@link GraphStore() property} with its transport, because the graph store contains a
	 * closure which cannot be serialized.
	 *
	 * Note that we do not use the accessor method, because we are setting the data member
	 * to an unsupported data type.
	 *
	 * @access protected
	 *
	 * @uses GraphStore()
	 */
	protected function _SerialiseGraphStore()
	{
		//
		// Handle graph store.
		//
		$data = $this->GraphStore();
		if( $data !== NULL )
			$this->mGraphStore = $data->getTransport();
	
	} // _SerialiseGraphStore.

		

/*=======================================================================================
 *																						*
 *							PROTECTED UNSERIALISATION INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_Unserialise																	*
	 *==================================================================================*/

	/**
	 * Unserialise object.
	 *
	 * This method is called after the object gets {@link unserialize() unserialized}, its
	 * duty is to restore the object's properties that were {@link _Serialise() normalised}
	 * before the object was {@link serialize() serialized}.
	 *
	 * In this class we need to {@link _UnserialiseGraphStore() restore} the graph
	 * {@link GraphStore() store}, the {@link _UnserialiseDataStore() data}
	 * {@link DataStore() store}, {@link _UnserialiseDatabase() the}
	 * {@link Database() database} and the {@link _UnserialiseUsersContainer() users}
	 * {@link UsersContainer() container}.
	 *
	 * @access protected
	 *
	 * @uses _UnserialiseGraphStore()
	 * @uses _UnserialiseDataStore()
	 * @uses _UnserialiseDatabase()
	 * @uses _UnserialiseUsersContainer()
	 */
	protected function _Unserialise()
	{
		//
		// Unserialise properties.
		//
		$this->_UnserialiseGraphStore();
		$this->_UnserialiseDataStore();
		$this->_UnserialiseDatabase();
		$this->_UnserialiseUsersContainer();
		
		//
		// Call parent method.
		//
		parent::_Unserialise();
		
	} // _Unserialise.

	 
	/*===================================================================================
	 *	_UnserialiseGraphStore															*
	 *==================================================================================*/

	/**
	 * Unserialise graph store.
	 *
	 * This method is called after the object gets {@link unserialize() unserialised}, its
	 * duty is to restore the current graph {@link GraphStore() store} from its
	 * {@link _SerialiseGraphStore() transport}.
	 *
	 * @access protected
	 *
	 * @uses GraphStore()
	 *
	 * @see _SerialiseGraphStore()
	 */
	protected function _UnserialiseGraphStore()
	{
		//
		// Handle graph store.
		//
		$data = $this->GraphStore();
		if( $data !== NULL )
			$this->GraphStore(
				new Everyman\Neo4j\Client(
					$data ) );
	
	} // _UnserialiseGraphStore.

	 
	/*===================================================================================
	 *	_UnserialiseDataStore															*
	 *==================================================================================*/

	/**
	 * Unserialise data store.
	 *
	 * This method is called after the object gets {@link unserialize() unserialised}, its
	 * duty is to restore the current data {@link DataStore() store}.
	 *
	 * @access protected
	 *
	 * @uses _InitDataStore()
	 *
	 * @see _SerialiseDataStore()
	 */
	protected function _UnserialiseDataStore()
	{
		//
		// Call the initialisation function.
		//
		$this->_InitDataStore();
	
	} // _UnserialiseDataStore.

	 
	/*===================================================================================
	 *	_UnserialiseDatabase															*
	 *==================================================================================*/

	/**
	 * Unserialise database.
	 *
	 * This method is called after the object gets {@link unserialize() unserialised}, its
	 * duty is to restore the current {@link GraphStore() database} from its
	 * {@link _SerialiseDatabase() name}.
	 *
	 * @access protected
	 *
	 * @uses Database()
	 * @uses DataStore()
	 *
	 * @see _SerialiseDatabase()
	 */
	protected function _UnserialiseDatabase()
	{
		//
		// Handle database.
		//
		$data = $this->Database();
		if( $data !== NULL )
			$this->Database(
				$this->DataStore()->
					selectDB(
						$data ) );
	
	} // _UnserialiseDatabase.

	 
	/*===================================================================================
	 *	_UnserialiseUsersContainer														*
	 *==================================================================================*/

	/**
	 * Unserialise users container.
	 *
	 * This method is called after the object gets {@link unserialize() unserialised}, its
	 * duty is to restore the current users {@link UsersContainer() container} from its
	 * {@link _SerialiseUsersContainer() name}.
	 *
	 * @access protected
	 *
	 * @uses UsersContainer()
	 * @uses Database()
	 *
	 * @see _SerialiseUsersContainer()
	 */
	protected function _UnserialiseUsersContainer()
	{
		//
		// Handle users container.
		//
		$data = $this->UsersContainer();
		if( $data !== NULL )
			$this->UsersContainer(
				new CMongoContainer(
					$this->Database()-> 
						selectCollection(
							$data ) ) );
	
	} // _UnserialiseUsersContainer.

	 

} // class CSessionMongoNeo4j.


?>
