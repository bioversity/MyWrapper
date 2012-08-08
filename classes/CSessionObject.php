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
 * User definitions.
 *
 * This include file contains the user class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CUser.php" );

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
		$tmp = NULL;
		$this->_InitResources( $tmp );
		
		//
		// Initialise view model.
		//
		$this->_Register();

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
		// Init local storage.
		//
		$string = '';
		
		//
		// Iterate object.
		//
		foreach( $this as $tag => $value )
			$string .= ( 'this.'.$tag.' = '.CObject::JsonEncode( $value ).'; ' );
		
		return '{'.$string.'}';														// ==>
	
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
		// Initialise data.
		//
		$data = Array();
		
		//
		// Initialise resources.
		//
		$this->_InitResources( $data );
		
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
		// Initialise data.
		//
		$data = unserialize( $theSerialised );
		
		//
		// Initialise resources.
		//
		$this->_InitResources( $data );
		
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
		return CObject::ManageMember( $this->mDatabase, $theValue, $getOld );		// ==>

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
		return CObject::ManageMember( $this->mUsersContainer, $theValue, $getOld );	// ==>

	} // UsersContainer.

		

/*=======================================================================================
 *																						*
 *							PUBLIC VIEW MODEL PROPERTIES INTERFACE						*
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
	 * This method is called in three occasions
	 *
	 * <ul>
	 *	<li><i>{@link __construct() Instantiating}</i>: The provided parameter will be
	 *		<i>NULL</i>, the duty of this method is to initialise all necessary resources.
	 *	<li><i>{@link serialize() Serialising}</i>: The provided parameter will be
	 *		a reference to the serialised object, the duty of this method is to serialise
	 *		the data members .
	 *	<li><i>{@link unserialize() Unserialising}</i>: The provided parameter will be an
	 *		array containing the serialised contents of this object, the duty of this
	 *		method is to restore all elements to their original value.
	 * </ul>
	 *
	 * @param reference			   &$theOperation		Operation or serialised data.
	 *
	 * @access protected
	 *
	 * @uses _InitOffsets()
	 * @uses _InitDataStore()
	 * @uses _InitGraphStore()
	 * @uses _InitDatabase()
	 * @uses _InitUserContainer()
	 */
	protected function _InitResources( &$theOperation )
	{
		//
		// Init offsets.
		//
		$this->_InitOffsets( $theOperation );
		
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
	 *	_InitOffsets																	*
	 *==================================================================================*/

	/**
	 * Initialise offsets.
	 *
	 * The duty of this method is to initialise the object's offsets.
	 *
	 * This method is called in three occasions
	 *
	 * <ul>
	 *	<li><i>{@link __construct() Instantiating}</i>: The provided parameter will be
	 *		<i>NULL</i>, the duty of this method is to initialise all necessary resources.
	 *	<li><i>{@link serialize() Serialising}</i>: The provided parameter will be
	 *		<i>FALSE</i>, the duty of this method is to prepare all assets that are to be
	 *		serialised.
	 *	<li><i>{@link unserialize() Unserialising}</i>: The provided parameter will be an
	 *		array containing the serialised contents of this object, the duty of this
	 *		method is to restore all elements to their original value.
	 * </ul>
	 *
	 * In this class we store the current offsets in the 0 element of the serialised data
	 * array.
	 *
	 * @param reference			   &$theOperation		Operation or serialised data.
	 *
	 * @access protected
	 */
	protected function _InitOffsets( &$theOperation )
	{
		//
		// Not initialising.
		//
		if( is_array( $theOperation ) )
		{
			//
			// Serialising.
			//
			if( ! count( $theOperation ) )
				$theOperation[] = $this->getArrayCopy();
			
			//
			// Unserialising.
			//
			else
				$this->exchangeArray( $theOperation );
		
		} // Not initialising.
	
	} // _InitOffsets.

	 
	/*===================================================================================
	 *	_InitDataStore																	*
	 *==================================================================================*/

	/**
	 * Initialise data store.
	 *
	 * The duty of this method is to initialise the data store.
	 *
	 * This method is called in three occasions
	 *
	 * <ul>
	 *	<li><i>{@link __construct() Instantiating}</i>: The provided parameter will be
	 *		<i>NULL</i>, the duty of this method is to initialise all necessary resources.
	 *	<li><i>{@link serialize() Serialising}</i>: The provided parameter will be
	 *		<i>FALSE</i>, the duty of this method is to prepare all assets that are to be
	 *		serialised.
	 *	<li><i>{@link unserialize() Unserialising}</i>: The provided parameter will be an
	 *		array containing the serialised contents of this object, the duty of this
	 *		method is to restore all elements to their original value.
	 * </ul>
	 *
	 * In this class we declare this method as abstract, derived classes must decide what
	 * data store engine to use.
	 *
	 * @param reference			   &$theOperation		Operation or serialised data.
	 *
	 * @access protected
	 */
	abstract protected function _InitDataStore( &$theOperation );

	 
	/*===================================================================================
	 *	_InitGraphStore																	*
	 *==================================================================================*/

	/**
	 * Initialise graph store.
	 *
	 * The duty of this method is to initialise the graph store.
	 *
	 * This method is called in three occasions
	 *
	 * <ul>
	 *	<li><i>{@link __construct() Instantiating}</i>: The provided parameter will be
	 *		<i>NULL</i>, the duty of this method is to initialise all necessary resources.
	 *	<li><i>{@link serialize() Serialising}</i>: The provided parameter will be
	 *		<i>FALSE</i>, the duty of this method is to prepare all assets that are to be
	 *		serialised.
	 *	<li><i>{@link unserialize() Unserialising}</i>: The provided parameter will be an
	 *		array containing the serialised contents of this object, the duty of this
	 *		method is to restore all elements to their original value.
	 * </ul>
	 *
	 * In this class we declare this method as abstract, derived classes must decide what
	 * graph engine to use.
	 *
	 * @param reference			   &$theOperation		Operation or serialised data.
	 *
	 * @access protected
	 */
	abstract protected function _InitGraphStore( &$theOperation );

	 
	/*===================================================================================
	 *	_InitDatabase																	*
	 *==================================================================================*/

	/**
	 * Initialise database.
	 *
	 * The duty of this method is to initialise the database.
	 *
	 * This method is called in three occasions
	 *
	 * <ul>
	 *	<li><i>{@link __construct() Instantiating}</i>: The provided parameter will be
	 *		<i>NULL</i>, the duty of this method is to initialise all necessary resources.
	 *	<li><i>{@link serialize() Serialising}</i>: The provided parameter will be
	 *		<i>FALSE</i>, the duty of this method is to prepare all assets that are to be
	 *		serialised.
	 *	<li><i>{@link unserialize() Unserialising}</i>: The provided parameter will be an
	 *		array containing the serialised contents of this object, the duty of this
	 *		method is to restore all elements to their original value.
	 * </ul>
	 *
	 * In this class we declare this method as abstract, derived classes must decide what
	 * database engine to use.
	 *
	 * @param reference			   &$theOperation		Operation or serialised data.
	 *
	 * @access protected
	 */
	abstract protected function _InitDatabase( &$theOperation );

	 
	/*===================================================================================
	 *	_InitUserContainer																*
	 *==================================================================================*/

	/**
	 * Initialise users container.
	 *
	 * The duty of this method is to initialise the user container.
	 *
	 * This method is called in three occasions
	 *
	 * <ul>
	 *	<li><i>{@link __construct() Instantiating}</i>: The provided parameter will be
	 *		<i>NULL</i>, the duty of this method is to initialise all necessary resources.
	 *	<li><i>{@link serialize() Serialising}</i>: The provided parameter will be
	 *		<i>FALSE</i>, the duty of this method is to prepare all assets that are to be
	 *		serialised.
	 *	<li><i>{@link unserialize() Unserialising}</i>: The provided parameter will be an
	 *		array containing the serialised contents of this object, the duty of this
	 *		method is to restore all elements to their original value.
	 * </ul>
	 *
	 * In this class we declare this method as abstract, derived classes must decide what
	 * user container to use.
	 *
	 * @param reference			   &$theOperation		Operation or serialised data.
	 *
	 * @access protected
	 */
	abstract protected function _InitUserContainer( &$theOperation );

	 
	/*===================================================================================
	 *	_InitUser																		*
	 *==================================================================================*/

	/**
	 * Initialise user.
	 *
	 * The duty of this method is to initialise the current user.
	 *
	 * This method is called in three occasions
	 *
	 * <ul>
	 *	<li><i>{@link __construct() Instantiating}</i>: The provided parameter will be
	 *		<i>NULL</i>, the method will do nothing.
	 *	<li><i>{@link serialize() Serialising}</i>: The provided parameter will be
	 *		<i>FALSE</i>, the method will convert the user {@link CUser object} into its
	 *		{@link kTAG_LID identifier}.
	 *	<li><i>{@link unserialize() Unserialising}</i>: The provided parameter will be an
	 *		array containing the serialised contents of this object, the method will restore
	 *		the user if necessary.
	 * </ul>
	 *
	 * @param reference			   &$theOperation		Operation or serialised data.
	 *
	 * @access protected
	 */
	protected function _InitUser( &$theOperation )
	{
		//
		// Not initialising.
		//
		if( is_array( $theOperation ) )
		{
			//
			// Unserialise user.
			//
			if( array_key_exists( 'mUser', $theOperation ) )
				$this->_LoadUser( $theOperation[ 'mUser' ] );
			
			//
			// Serialise user.
			//
			elseif( $save = $this->User() )
			{
				//
				// Save user.
				//
				$save->Commit( $this->UsersContainer() );
				
				//
				// Serialise identifier.
				// Note that I use the member and not the method:
				// it would complain that the parameter is not a user object.
				//
				$theOperation[ 'mUser' ] = $save[ kTAG_LID ];
			
			} // Has user.
		
		} // Not initialising.
	
	} // _InitUser.

		

/*=======================================================================================
 *																						*
 *								PROTECTED VIEW MODEL INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_Register																		*
	 *==================================================================================*/

	/**
	 * Initialise view model.
	 *
	 * This method will initialise the view model.
	 *
	 * @access protected
	 */
	protected function _Register()
	{
		//
		// Register user.
		//
		$this->_RegisterUser();
		
	} // _Register.

	 
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
		// Get current user.
		//
		$user = $this->User();
		
		//
		// Handle name.
		//
		$this->_RegisterUserName();
		
		//
		// Handle e-mail.
		//
		$this->_RegisterUserEmail();
		
		//
		// Handle kinds.
		//
		$this->_RegisterUserKind();
		
		//
		// Handle roles.
		//
		$this->_RegisterUserRole();
		
		//
		// Set logged flag.
		//
		$this->_RegisterUserLogged();
		
	} // _RegisterUser.

	 
	/*===================================================================================
	 *	_RegisterUserName																*
	 *==================================================================================*/

	/**
	 * Load user name in view model.
	 *
	 * This method will load user {@load CUser::Name() name}  in the view model.
	 *
	 * This method can be overloaded if derived classes need to do do custom stuff with the
	 * name.
	 *
	 * Note that if the user is missing the name is set to <i>Login</i>, this assumes the
	 * name goes onto the login button which becomes a popup whe  the user has logged.
	 *
	 * @access protected
	 */
	protected function _RegisterUserName()
	{
		//
		// Get current user.
		//
		$user = $this->User();
		
		//
		// Handle name.
		//
		$this->offsetSet( kSESSION_USER_NAME,
						  ( ( $user !== NULL ) ? $user->Name() : 'Login' ) );
		
		
	} // _RegisterUserName.

	 
	/*===================================================================================
	 *	_RegisterUserEmail																*
	 *==================================================================================*/

	/**
	 * Load user e-mail in view model.
	 *
	 * This method will load user {@link CUser::Email() e-mail} in the view model.
	 *
	 * This method can be overloaded if derived classes need to do do custom stuff with the
	 * e-mail.
	 *
	 * @access protected
	 */
	protected function _RegisterUserEmail()
	{
		//
		// Get current user.
		//
		$user = $this->User();
		
		//
		// Handle e-mail.
		//
		$this->offsetSet( kSESSION_USER_EMAIL,
						  ( ( $user !== NULL ) ? $user->Email() : NULL ) );
		
	} // _RegisterUserEmail.

	 
	/*===================================================================================
	 *	_RegisterUserKind																*
	 *==================================================================================*/

	/**
	 * Load user kinds in view model.
	 *
	 * This method will load user {@load CUser::Kind() kinds} in the view model.
	 *
	 * This method can be overridden if derived classes need to do custom stuff using the
	 * user kinds; in this class we simply copy the property contents.
	 *
	 * @access protected
	 */
	protected function _RegisterUserKind()
	{
		//
		// Get current user.
		//
		$user = $this->User();
		
		//
		// Handle kinds.
		//
		$this->offsetSet( kSESSION_USER_KIND,
						  ( ( $user !== NULL ) ? $user->Kind() : Array() ) );
		
	} // _RegisterUserKind.

	 
	/*===================================================================================
	 *	_RegisterUserRole																*
	 *==================================================================================*/

	/**
	 * Load user roles in view model.
	 *
	 * This method will load user {@load CUser::Role() roles} in the view model.
	 *
	 * This method can be overridden if derived classes need to do custom stuff using the
	 * user roles; in this class we simply copy the property contents.
	 *
	 * @access protected
	 */
	protected function _RegisterUserRole()
	{
		//
		// Get current user.
		//
		$user = $this->User();
		
		//
		// Handle kinds.
		//
		$this->offsetSet( kSESSION_USER_ROLE,
						  ( ( $user !== NULL ) ? $user->Role() : Array() ) );
		
	} // _RegisterUserRole.

	 
	/*===================================================================================
	 *	_RegisterUserLogged																*
	 *==================================================================================*/

	/**
	 * Set user logged flag in view model.
	 *
	 * This method will set the logged {@link kSESSION_USER_LOGGED flag} in the view model.
	 *
	 * @access protected
	 */
	protected function _RegisterUserLogged()
	{
		//
		// Get current user.
		//
		$user = $this->User();
		
		//
		// Handle kinds.
		//
		$this->offsetSet( kSESSION_USER_LOGGED,
						  ( ( $user !== NULL ) ? TRUE : FALSE ) );
		
	} // _RegisterUserLogged.

		

/*=======================================================================================
 *																						*
 *							PROTECTED OPERATIONS INTERFACE								*
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
