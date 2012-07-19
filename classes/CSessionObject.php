<?php

/**
 * <i>CSessionObject</i> class definition.
 *
 * This file contains the class definition of <b>CSessionObject</b> which wraps this class
 * {@link CArrayObject ancestor} around a session.
 *
 *	@package	MyWrapper
 *	@subpackage	Session
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 12/07/2012
*/

/*=======================================================================================
 *																						*
 *									CSessionObject.php									*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CArrayObject.php" );

/**
 * Attributes.
 *
 * This include file contains the attributes class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CAttribute.php" );

/**
 * User definitions.
 *
 * This include file contains the user class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CUser.php" );

/**
 * Wrapper definitions.
 *
 * This include file contains the wrapper class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CWrapper.inc.php" );
require_once( kPATH_LIBRARY_SOURCE."CDataWrapper.inc.php" );
require_once( kPATH_LIBRARY_SOURCE."CMongoDataWrapper.inc.php" );
require_once( kPATH_LIBRARY_SOURCE."CWarehouseWrapper.inc.php" );

/**
 * Local definitions.
 *
 * This include file contains all local definitions to this class.
 */
require_once( kPATH_LIBRARY_SOURCE."CSessionObject.inc.php" );

/**
 *	Session object.
 *
 * This class implements a session object, it should be stored in the session with the
 * {@link kDEFAULT_SESSION kDEFAULT_SESSION} offset.
 * 
 * This class and its derived instances store session objects in data members and the view
 * model object in its array, this means that the server holds private data and the public
 * data is published to the web pages by converting the array to JSON.
 *
 * This class is declared abstract, because it lets concrete derived instances take care of
 * choosing the specific database engines.
 *
 *	@package	MyWrapper
 *	@subpackage	Session
 */
abstract class CSessionObject extends CArrayObject
							  implements Serializable
{
	/**
	 * Data store.
	 *
	 * This data member holds the data store instance.
	 *
	 * @var mixed
	 */
	 protected $mDataStore = NULL;

	/**
	 * Graph store store.
	 *
	 * This data member holds the graph store instance.
	 *
	 * @var mixed
	 */
	 protected $mGraphStore = NULL;

	/**
	 * Session database.
	 *
	 * This data member holds the default database object.
	 *
	 * @var mixed
	 */
	 protected $mDatabase = NULL;

	/**
	 * Session users container.
	 *
	 * This data member holds the default {@link CUser user} {@link CContainer container}.
	 *
	 * @var mixed
	 */
	 protected $mUsersContainer = NULL;

	/**
	 * Session user.
	 *
	 * This data member holds the session {@link CUser user} object.
	 *
	 * @var CUser
	 */
	 protected $mUser = NULL;

		

/*=======================================================================================
 *																						*
 *										MAGIC											*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	__construct																		*
	 *==================================================================================*/

	/**
	 * Instantiate class.
	 *
	 * The constructor will either instantiate an empty object, or it will initialise its
	 * array data with the provided parameter, which represents the view model data.
	 *
	 * In all cases the database connections will be created.
	 *
	 * @param mixed					$theData			View model data.
	 *
	 * @access public
	 */
	public function __construct( $theData = NULL )
	{
		//
		// Check provided data.
		//
		if( $theData !== NULL )
		{
			//
			// Check view model.
			//
			if( is_array( $theData )
			 || ($theData instanceof ArrayObject) )
				parent::__construct( (array) $theData );
			
			else
				throw new CException
					( "Unsupported view model data",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Data' => $theData ) );							// !@! ==>
		
		} // Provided view model.
		
		else
			parent::__construct();
		
		//
		// Initialise default resources.
		//
		$this->_InitResources( TRUE );

	} // Constructor.

	 
	/*===================================================================================
	 *	__toString																		*
	 *==================================================================================*/

	/**
	 * Return string representation.
	 *
	 * In this class we return the view model converted to JSON.
	 *
	 * @access public
	 * @return string
	 */
	public function __toString()
	{
		//
		// Handle data.
		//
		if( $data = $this->getArrayCopy() )
			return CObject::JsonEncode( $data );									// ==>
		
		return '{}';																// ==>
	
	} // __toString.

		

/*=======================================================================================
 *																						*
 *								SERIALIZABLE INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	serialize																		*
	 *==================================================================================*/

	/**
	 * Default serialization interface.
	 *
	 * This method will serialize the object, the method will first close any open
	 * connections and commit the current user if there, then serialize the object.
	 *
	 * @access public
	 * @return string
	 */
	public function serialize()
	{
		//
		// Save current user.
		//
		$this->_InitUser( FALSE );
		
		//
		// Close data store.
		//
		$this->_InitDataStore( FALSE );
		
		//
		// Close graph store.
		//
		$this->_InitGraphStore( FALSE );
		
		//
		// Close database.
		//
		$this->_InitDatabase( FALSE );
		
		//
		// Close users container.
		//
		$this->_InitUserContainer( FALSE );
		
		//
		// Serialise object.
		//
		$data = Array();
		if( ($tmp = $this->User()) !== NULL )
			$data[ 'User' ] = $tmp;
		
		return serialize( $data );													// ==>
		
	} // serialize().

	 
	/*===================================================================================
	 *	unserialize																		*
	 *==================================================================================*/

	/**
	 * Default unserialization interface.
	 *
	 * This method will reconstitute the object after serialization.
	 *
	 * @param string				$theSerialised		Serialized data.
	 *
	 * @access public
	 */
	public function unserialize( $theSerialised )
	{
		//
		// Open data store.
		//
		$this->_InitDataStore( TRUE );
		
		//
		// Open graph store.
		//
		$this->_InitGraphStore( TRUE );
		
		//
		// Open database.
		//
		$this->_InitDatabase( TRUE );
		
		//
		// Open users container.
		//
		$this->_InitUserContainer( TRUE );
		
		//
		// Unserialise old object.
		//
		$theSerialised = unserialize( $theSerialised );
		if( array_key_exists( 'User', $theSerialised ) )
			$this->mUser = $theSerialised[ 'User' ];
		
		//
		// Reload current user.
		//
		$this->_InitUser( TRUE );
		
		//
		// Register default resources.
		//
		$this->_RegisterResources();
		
	} // unserialize().

		

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
	 * The method accepts two parameters:
	 *
	 * <ul>
	 *	<li><b>$theValue</b>: The value to set or the operation to perform:
	 *	 <ul>
	 *		<li><i>NULL</i>: Retrieve the current value.
	 *		<li><i>FALSE</i>: Delete the current value, it will be set to <i>NULL</i>.
	 *		<li><i>other</i>: Any other value will be interpreted as the new value to be
	 *			set.
	 *	 </ul>
	 *	<li><b>$getOld</b>: A boolean flag determining which value the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value <i>before</i> it has eventually been modified,
	 *			this option is only relevant when deleting or relacing a value.
	 *		<li><i>FALSE</i>: Return the value <i>after</i> it has eventually been modified.
	 *	 </ul>
	 * </ul>
	 *
	 * In this class, by default, we support MongoDB data stores, which means that the
	 * method expects Mongo objects, if other types are passed, an exception will be raised.
	 *
	 * @param mixed					$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses CObject::ManageMember()
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
		
		return CObject::ManageMember( $this->mDataStore, $theValue, $getOld );		// ==>

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
	 * The method accepts two parameters:
	 *
	 * <ul>
	 *	<li><b>$theValue</b>: The value to set or the operation to perform:
	 *	 <ul>
	 *		<li><i>NULL</i>: Retrieve the current value.
	 *		<li><i>FALSE</i>: Delete the current value, it will be set to <i>NULL</i>.
	 *		<li><i>other</i>: Any other value will be interpreted as the new value to be
	 *			set.
	 *	 </ul>
	 *	<li><b>$getOld</b>: A boolean flag determining which value the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value <i>before</i> it has eventually been modified,
	 *			this option is only relevant when deleting or relacing a value.
	 *		<li><i>FALSE</i>: Return the value <i>after</i> it has eventually been modified.
	 *	 </ul>
	 * </ul>
	 *
	 * In this class, by default, we support Neo4j graph stores, which means that the
	 * method expects a Neo4j client object, if other types are passed, an exception will be
	 * raised.
	 *
	 * @param mixed					$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses CObject::ManageMember()
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
		
		return CObject::ManageMember( $this->mGraphStore, $theValue, $getOld );		// ==>

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
	 * The method accepts two parameters:
	 *
	 * <ul>
	 *	<li><b>$theValue</b>: The value to set or the operation to perform:
	 *	 <ul>
	 *		<li><i>NULL</i>: Retrieve the current value.
	 *		<li><i>FALSE</i>: Delete the current value, it will be set to <i>NULL</i>.
	 *		<li><i>other</i>: Any other value will be interpreted as the new value to be
	 *			set.
	 *	 </ul>
	 *	<li><b>$getOld</b>: A boolean flag determining which value the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value <i>before</i> it has eventually been modified,
	 *			this option is only relevant when deleting or relacing a value.
	 *		<li><i>FALSE</i>: Return the value <i>after</i> it has eventually been modified.
	 *	 </ul>
	 * </ul>
	 *
	 * In this class, by default, we support MongoDB instances, if other types are passed,
	 * an exception will be thrown.
	 *
	 * @param mixed					$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses CObject::ManageMember()
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
		
		return CObject::ManageMember( $this->mDatabase, $theValue, $getOld );		// ==>

	} // Database.

	 
	/*===================================================================================
	 *	UsersContainer																	*
	 *==================================================================================*/

	/**
	 * Manage the defrault users container.
	 *
	 * This method can be used to manage the session's default users container. This object
	 * represents the the container in which all users are stored.
	 *
	 * The method accepts two parameters:
	 *
	 * <ul>
	 *	<li><b>$theValue</b>: The value to set or the operation to perform:
	 *	 <ul>
	 *		<li><i>NULL</i>: Retrieve the current value.
	 *		<li><i>FALSE</i>: Delete the current value, it will be set to <i>NULL</i>.
	 *		<li><i>other</i>: Any other value will be interpreted as the new value to be
	 *			set.
	 *	 </ul>
	 *	<li><b>$getOld</b>: A boolean flag determining which value the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value <i>before</i> it has eventually been modified,
	 *			this option is only relevant when deleting or relacing a value.
	 *		<li><i>FALSE</i>: Return the value <i>after</i> it has eventually been modified.
	 *	 </ul>
	 * </ul>
	 *
	 * In this class, by default, we support {@link CContainer CContainer} derived
	 * instances, if other types are passed, the method will raise an exception.
	 *
	 * @param mixed					$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses CObject::ManageMember()
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
			// Check value.
			//
			if( ! ($theValue instanceof CContainer) )
				throw new CException
					( "Unsupported users container reference",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Reference' => $theValue ) );						// !@! ==>
		
		} // New value.
		
		return CObject::ManageMember( $this->mUsersContainer, $theValue, $getOld );	// ==>

	} // UsersContainer.

		

/*=======================================================================================
 *																						*
 *								PUBLIC PROPERTIES INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	User																			*
	 *==================================================================================*/

	/**
	 * Manage the session user.
	 *
	 * This method can be used to manage the session's user {@link CUser object}. This
	 * object is available to the server PHP, while some of its properties will be provided
	 * to the view model.
	 *
	 * The method accepts two parameters:
	 *
	 * <ul>
	 *	<li><b>$theValue</b>: The value to set or the operation to perform:
	 *	 <ul>
	 *		<li><i>NULL</i>: Retrieve the current value.
	 *		<li><i>FALSE</i>: Delete the current value, it will be set to <i>NULL</i>.
	 *		<li><i>other</i>: Any other value will be interpreted as the new value to be
	 *			set.
	 *	 </ul>
	 *	<li><b>$getOld</b>: A boolean flag determining which value the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value <i>before</i> it has eventually been modified,
	 *			this option is only relevant when deleting or relacing a value.
	 *		<li><i>FALSE</i>: Return the value <i>after</i> it has eventually been modified.
	 *	 </ul>
	 * </ul>
	 *
	 * In this class, by default, we support {@link CUser CUser} objects, if other types are
	 * passed, an exception will be raised.
	 *
	 * @param mixed					$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses CObject::ManageMember()
	 */
	public function User( $theValue = NULL, $getOld = FALSE )
	{
		//
		// Change value.
		//
		if( ($theValue !== NULL)
		 && ($theValue !== FALSE) )
		{
			//
			// Check value.
			//
			if( ! ($theValue instanceof CUser) )
				throw new CException
					( "Unsupported user object",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'User' => $theValue ) );							// !@! ==>
		
		} // New value.
		
		//
		// Perform operation.
		//
		$save = CObject::ManageMember( $this->mUser, $theValue, $getOld );
		
		//
		// Changed value.
		//
		if( ($theValue !== NULL)
		 && ($theValue !== FALSE) )
			$this->_RegisterUser();
		
		return $save;																// ==>

	} // User.

		

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
	 * Derived classes must implement this method.
	 *
	 * @access public
	 * @return CQuery
	 */
	abstract public function Query();

	 
	/*===================================================================================
	 *	Login																			*
	 *==================================================================================*/

	/**
	 * Perform a login.
	 *
	 * This method can be used to query a user by {@link CUser::Code() code} and
	 * {@link CUser::Password() password}.
	 *
	 * The method will look for the user in the default users
	 * {@link UsersContainer() container}, if found, it will return the
	 * {@link CUser() object}, if not, it will return <i>NULL</i>.
	 *
	 * The method expects two parameters:
	 *
	 * <ul>
	 *	<li><b>$theCode</b>: The user code.
	 *	<li><b>$thePass</b>: The user password.
	 * </ul>
	 *
	 * In this class we declare the method abstract, because the specifics of seeking for
	 * the user depends on the specific database engine.
	 *
	 * @param string				$theCode			User code.
	 * @param string				$thePass			User password.
	 *
	 * @access public
	 * @return CObject
	 */
	public function Login( $theCode, $thePass )
	{
		//
		// Init query.
		//
		$query = $this->Query();
		
		//
		// Add statements.
		//
		$query->AppendStatement(
			CQueryStatement::Equals( kTAG_CODE, $theCode ),
			kOPERATOR_AND );
		$query->AppendStatement(
			CQueryStatement::Equals( kOFFSET_PASSWORD, $thePass ),
			kOPERATOR_AND );
		
		//
		// Perform query.
		//
		$query = $query->Export( $this->UsersContainer() );
		$cursor = $this->UsersContainer()->Container()->find( $query );
		
		//
		// Handle found.
		//
		if( $cursor->count() )
			return new CUser(
				$this->UsersContainer(),
				CUser::HashIndex( $theCode ) );										// ==>
		
		return NULL;																// ==>
	
	} // Login.

		

/*=======================================================================================
 *																						*
 *							PROTECTED INITIALISATION INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_InitResources																	*
	 *==================================================================================*/

	/**
	 * Initialise resources.
	 *
	 * This method will initialise the default resources, the method expects one parameter
	 * that determines whether these resources are to be initialised, <i>TRUE</i>, or reset,
	 * <i>FALSE</i>.
	 *
	 * In this class we handle the {@link DataStore() data} and the
	 * {@link GraphStore() graph} stores, the {@link Database() database} and the
	 * {@link UsersContainer() users} container.
	 *
	 * @param boolean				$theOperation		TRUE set, FALSE reset.
	 *
	 * @access protected
	 *
	 * @uses _InitDataStore()
	 * @uses _InitGraphStore()
	 * @uses _InitDatabase()
	 * @uses _InitUserContainer()
	 */
	protected function _InitResources( $theOperation )
	{
		//
		// Init data store.
		//
		$this->_InitDataStore( $theOperation );
		
		//
		// Init graph store.
		//
		$this->_InitGraphStore( $theOperation );
		
		//
		// Init database.
		//
		$this->_InitDatabase( $theOperation );
		
		//
		// Init users container.
		//
		$this->_InitUserContainer( $theOperation );
		
	} // _InitResources.

	 
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
	 * The method returns no result, any error should trigger an exception.
	 *
	 * In this class we declare this method as abstract, derived classes must decide what
	 * database engine to use.
	 *
	 * @param boolean				$theOperation		TRUE set, FALSE reset.
	 *
	 * @access protected
	 */
	abstract protected function _InitDataStore( $theOperation );

	 
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
	 * The method returns no result, any error should trigger an exception.
	 *
	 * In this class we declare this method as abstract, derived classes must decide what
	 * graph engine to use.
	 *
	 * @param boolean				$theOperation		TRUE set, FALSE reset.
	 *
	 * @access protected
	 */
	abstract protected function _InitGraphStore( $theOperation );

	 
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
	 * The method returns no result, any error should trigger an exception.
	 *
	 * In this class we declare this method as abstract, derived classes must decide what
	 * database engine to use.
	 *
	 * @param boolean				$theOperation		TRUE set, FALSE reset.
	 *
	 * @access protected
	 */
	abstract protected function _InitDatabase( $theOperation );

	 
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
	 * The method returns no result, any error should trigger an exception.
	 *
	 * In this class we declare this method as abstract, derived classes must decide what
	 * database engine to use.
	 *
	 * @param boolean				$theOperation		TRUE set, FALSE reset.
	 *
	 * @access protected
	 */
	abstract protected function _InitUserContainer( $theOperation );

	 
	/*===================================================================================
	 *	_InitUser																		*
	 *==================================================================================*/

	/**
	 * Initialise user.
	 *
	 * This method will initialise the current user object, the method expects one boolean
	 * parameter: <i>TRUE</i> means that the user is to be set, <i>FALSE</i> means that the
	 * user is to be reset.
	 *
	 * Mote that this method is only called when {@link __sleep() sleeping} or
	 * {@link __wakeup() waking}: it will save eventual changes made to the user when
	 * {@link __sleep() sleeping} and reload the user when {@link __wakeup() waking}.
	 *
	 * The method returns no result, any error should trigger an exception.
	 *
	 * In this class we {@link CPersistentObject::Commit() commit} user data and store the
	 * user {@link kTAG_LID identifier} when going to {@link __sleep() sleep} and
	 * {@link _LoadUser() load} the user back when {@link __wakeup() waking}.
	 *
	 * @param boolean				$theOperation		TRUE set, FALSE reset.
	 *
	 * @access protected
	 */
	protected function _InitUser( $theOperation )
	{
		//
		// Check if there is a user.
		//
		$user = $this->User();
		if( $user !== NULL )
		{
			//
			// Wake.
			//
			if( $theOperation )
				$this->_LoadUser( $user );
			
			//
			// Sleep.
			//
			else
			{
				//
				// Save user.
				//
				$user->Commit( $this->UsersContainer() );
				
				//
				// Set identifier.
				// Note that I use the member and not the method:
				// it would complain that the parameter is not a user object.
				//
				$this->mUser = $user[ kTAG_LID ];
			
			} // Sleep.
		
		} // Has user.
	
	} // _InitUser.

		

/*=======================================================================================
 *																						*
 *								PROTECTED VIEW MODEL INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_RegisterUser																	*
	 *==================================================================================*/

	/**
	 * Load user in view model.
	 *
	 * This method will load user data into the view model, it will either use the current
	 * {@link User() user} or reset all user data if not present.
	 *
	 * @access protected
	 */
	protected function _RegisterUser()
	{
		//
		// Init local storage.
		//
		$object = $this->User();
		$fields = array( kTAG_NAME => kSESSION_USER_NAME,
						 kOFFSET_EMAIL => kSESSION_USER_EMAIL,
						 kTAG_KIND => kSESSION_USER_KIND,
						 kTAG_ROLE => kSESSION_USER_ROLE );
		
		//
		// Load data.
		//
		foreach( $fields as $property => $tag )
		{
			//
			// Set attribute.
			//
			if( $object !== NULL )
				$this->offsetSet( $tag, $object[ $property ] );
			
			//
			// Reset attribute.
			//
			else
				$this->offsetUnset( $tag );
		
		} // Iterating object properties
		
	} // _RegisterUser.

		

/*=======================================================================================
 *																						*
 *							PROTECTED PROPERTIES INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_LoadUser																		*
	 *==================================================================================*/

	/**
	 * Load user.
	 *
	 * This method can be used to load a {@link User() user} from the users
	 * {@link UsersContainer() container}, it expects the user identifier as the parameter
	 * and the procedure is not expected to represent a query, which means that if the user
	 * is not found an exception should be raised.
	 *
	 * The method will return the eventual old user if the operation replaced it.
	 *
	 * @param mixed					$theIdentifier		User identifier.
	 *
	 * @access protected
	 */
	protected function _LoadUser( $theIdentifier )
	{
		//
		// Instantiate user.
		//
		$user = new CUser( $this->UsersContainer(), $theIdentifier );
		
		//
		// Handle not found.
		//
		if( ! $user->Persistent() )
			throw new CException
				( "User not found",
				  kERROR_NOT_FOUND,
				  kMESSAGE_TYPE_ERROR,
				  array( 'Identifier' => $theIdentifier ) );					// !@! ==>
		
		return $this->User( $user, TRUE );											// ==>
		
	} // _LoadUser.

	 

} // class CSessionObject.


?>
