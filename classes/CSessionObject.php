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
 * This class implements a session object, it expects to be stored in the session with the
 * {@link kDEFAULT_SESSION kDEFAULT_SESSION} offset.
 *
 * Concrete instances derived from this class use the data members to store server data and
 * the object's offsets to store the view model data, this means that the server holds
 * private data and the public data is published to the web pages by
 * {@link __toString() converting} the offsets to JSON.
 *
 * This class is declared abstract, because it does not assume which storage engines and
 * custom resources will be used.
 *
 * The class implements the <i>Serializable</i> interface which can be overloaded by working
 * on its protected {@link _Serialise() serialize} and {@link _Unserialise() unserialize}
 * interface; remember that this also means that the sleep and wake events will also be
 * governed by that interface.
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
	 *
	 * @uses _Init()
	 * @uses _Register()
	 *
	 * @throws {@link CException CException}
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
		$this->_Init();
		
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
	 * The duty of this method is to serialise the current object, it operates by calling a
	 * protected {@link _Serialise() method} which prepares all resources to be serialised.
	 *
	 * The object is serialised in an array of two elements:
	 *
	 * <ul>
	 *	<li><i>0</i>: The first element is an array containing the array part of the object.
	 *	<li><i>1</i>: The second element is an array containing the object's data members,
	 *		each element is indexed by the data member variable name.
	 * </ul>
	 *
	 * The method will return the serialised array.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses _Serialise()
	 */
	public function serialize()
	{
		//
		// Initialise data container.
		//
		$data = array( 0 => Array(), 1 => Array() );
		
		//
		// Serialise object.
		//
		$this->_Serialise( $data );
		
		return serialize( $data );													// ==>
		
	} // serialize().

	 
	/*===================================================================================
	 *	unserialize																		*
	 *==================================================================================*/

	/**
	 * Default unserialization interface.
	 *
	 * The duty of this method is to load the current object with the provided serialised
	 * data, it operates by calling a protected {@link _Unserialise() method} which extracts
	 * repsective elements from the provided serialised data.
	 *
	 * The parameter is a serialised array of two elements:
	 *
	 * <ul>
	 *	<li><i>0</i>: The first element is an array containing the array part of the object.
	 *	<li><i>1</i>: The second element is an array containing the object's data members,
	 *		each element is indexed by the data member variable name.
	 * </ul>
	 *
	 * Before exiting, the method will {@link _Register() restore} the view model from
	 * member data where applicable in order to reflect changes made by server-side
	 * procedures.
	 *
	 * @param string				$theData			Serialized data.
	 *
	 * @access public
	 *
	 * @uses _Unserialise()
	 * @uses _Register()
	 */
	public function unserialize( $theData )
	{
		//
		// Unserialise container.
		//
		$data = unserialize( $theData );
		
		//
		// Reconstitute object.
		//
		$this->_Unserialise( $data );
		
		//
		// Initialise view model.
		//
		$this->_Register();
		
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
	 * @throws {@link CException CException}
	 *
	 * @uses _RegisterUser()
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
	 * @param string				$theCode			User code.
	 * @param string				$thePass			User password.
	 *
	 * @access public
	 * @return CObject
	 *
	 * @uses Query()
	 * @uses UsersContainer()
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
	 *	_Init																			*
	 *==================================================================================*/

	/**
	 * Initialise resources.
	 *
	 * This method is called when {@link __construct() instantiating} the object, its duty
	 * is to initialise the required resources.
	 *
	 * @access protected
	 *
	 * @uses _InitOffsets()
	 * @uses _InitDataStore()
	 * @uses _InitGraphStore()
	 * @uses _InitDatabase()
	 * @uses _InitUserContainer()
	 */
	protected function _Init()
	{
		//
		// Init offsets.
		//
		$this->_InitOffsets();
		
		//
		// Init data store.
		//
		$this->_InitDataStore();
		
		//
		// Init graph store.
		//
		$this->_InitGraphStore();
		
		//
		// Init database.
		//
		$this->_InitDatabase();
		
		//
		// Init users container.
		//
		$this->_InitUserContainer();
		
	} // _Init.

	 
	/*===================================================================================
	 *	_InitOffsets																	*
	 *==================================================================================*/

	/**
	 * Initialise offsets.
	 *
	 * The duty of this method is to initialise the object's offsets.
	 *
	 * In this class we do nothing, derived classes may overload this method to prepare the
	 * object's offsets.
	 *
	 * @access protected
	 */
	protected function _InitOffsets()													   {}

	 
	/*===================================================================================
	 *	_InitDataStore																	*
	 *==================================================================================*/

	/**
	 * Initialise data store.
	 *
	 * The duty of this method is to initialise the data store.
	 *
	 * In this class we declare this method as abstract, derived classes must decide what
	 * data store engine to use.
	 *
	 * @access protected
	 */
	abstract protected function _InitDataStore();

	 
	/*===================================================================================
	 *	_InitGraphStore																	*
	 *==================================================================================*/

	/**
	 * Initialise graph store.
	 *
	 * The duty of this method is to initialise the graph store.
	 *
	 * In this class we declare this method as abstract, derived classes must decide what
	 * graph engine to use.
	 *
	 * @access protected
	 */
	abstract protected function _InitGraphStore();

	 
	/*===================================================================================
	 *	_InitDatabase																	*
	 *==================================================================================*/

	/**
	 * Initialise database.
	 *
	 * The duty of this method is to initialise the database.
	 *
	 * In this class we declare this method as abstract, derived classes must decide what
	 * database engine to use.
	 *
	 * @access protected
	 */
	abstract protected function _InitDatabase();

	 
	/*===================================================================================
	 *	_InitUserContainer																*
	 *==================================================================================*/

	/**
	 * Initialise users container.
	 *
	 * The duty of this method is to initialise the user container.
	 *
	 * In this class we declare this method as abstract, derived classes must decide what
	 * user container to use.
	 *
	 * @access protected
	 */
	abstract protected function _InitUserContainer();

	 
	/*===================================================================================
	 *	_InitUser																		*
	 *==================================================================================*/

	/**
	 * Initialise user.
	 *
	 * The duty of this method is to initialise the current user.
	 *
	 * In this class we do nothing, derived classes may overload this method to prepare the
	 * session's user.
	 *
	 * @access protected
	 */
	protected function _InitUser()														   {}

		

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
	 * This method should prepare elements to be serialised and store them in the provided
	 * array which has the following structure:
	 *
	 * <ul>
	 *	<li><i>0</i>: The first element is an array containing the array part of the object.
	 *	<li><i>1</i>: The second element is an array containing the object's data members,
	 *		each element is indexed by the data member variable name.
	 * </ul>
	 *
	 * The method expects the parameter to have been initialised.
	 *
	 * @param reference			   &$theData			Serialised data container.
	 *
	 * @access protected
	 *
	 * @uses _SerialiseOffsets()
	 * @uses _SerialiseDataStore()
	 * @uses _SerialiseGraphStore()
	 * @uses _SerialiseDatabase()
	 * @uses _SerialiseUserContainer()
	 */
	protected function _Serialise( &$theData )
	{
		//
		// Serialise offsets.
		//
		$this->_SerialiseOffsets( $theData[ 0 ] );
		
		//
		// Serialise data store.
		//
		$this->_SerialiseDataStore( $theData[ 1 ] );
		
		//
		// Serialise graph store.
		//
		$this->_SerialiseGraphStore( $theData[ 1 ] );
		
		//
		// Serialise database.
		//
		$this->_SerialiseDatabase( $theData[ 1 ] );
		
		//
		// Serialise users container.
		//
		$this->_SerialiseUserContainer( $theData[ 1 ] );
		
	} // _Serialise.

	 
	/*===================================================================================
	 *	_SerialiseOffsets																*
	 *==================================================================================*/

	/**
	 * Serialise offsets.
	 *
	 * The duty of this method is to serialise the object's offsets, the provided parameter
	 * represents the serialised offsets container.
	 *
	 * The method will simply copy the object's offsets in the provided array.
	 *
	 * Note that the expected parameter is the actual serialise container element reserved
	 * to offsets (<i>$theData[ 0 ]</i>), not the whole container.
	 *
	 * @param reference			   &$theData			Offsets serialise container.
	 *
	 * @access protected
	 */
	protected function _SerialiseOffsets( &$theData )
	{
		$theData = $this->getArrayCopy();
	
	} // _SerialiseOffsets.

	 
	/*===================================================================================
	 *	_SerialiseDataStore																*
	 *==================================================================================*/

	/**
	 * Serialise data store.
	 *
	 * The duty of this method is to serialise the data store, the provided parameter
	 * represents the serialised members container.
	 *
	 * In this class we declare this method as abstract, derived classes must decide what
	 * data store engine to use.
	 *
	 * @param reference			   &$theData			Members serialise container.
	 *
	 * @access protected
	 */
	abstract protected function _SerialiseDataStore( &$theData );

	 
	/*===================================================================================
	 *	_SerialiseGraphStore															*
	 *==================================================================================*/

	/**
	 * Serialise graph store.
	 *
	 * The duty of this method is to serialise the graph store, the provided parameter
	 * represents the serialised members container.
	 *
	 * In this class we declare this method as abstract, derived classes must decide what
	 * data store engine to use.
	 *
	 * @param reference			   &$theData			Members serialise container.
	 *
	 * @access protected
	 */
	abstract protected function _SerialiseGraphStore( &$theData );

	 
	/*===================================================================================
	 *	_SerialiseDatabase																*
	 *==================================================================================*/

	/**
	 * Serialise database.
	 *
	 * The duty of this method is to serialise the database, the provided parameter
	 * represents the serialised members container.
	 *
	 * In this class we declare this method as abstract, derived classes must decide what
	 * database engine to use.
	 *
	 * @param reference			   &$theData			Members serialise container.
	 *
	 * @access protected
	 */
	abstract protected function _SerialiseDatabase( &$theData );

	 
	/*===================================================================================
	 *	_SerialiseUserContainer															*
	 *==================================================================================*/

	/**
	 * Serialise users container.
	 *
	 * The duty of this method is to serialise the user container, the provided parameter
	 * represents the serialised members container.
	 *
	 * In this class we declare this method as abstract, derived classes must decide what
	 * user container to use.
	 *
	 * @param reference			   &$theData			Members serialise container.
	 *
	 * @access protected
	 */
	abstract protected function _SerialiseUserContainer( &$theData );

	 
	/*===================================================================================
	 *	_SerialiseUser																	*
	 *==================================================================================*/

	/**
	 * Serialise user.
	 *
	 * The duty of this method is to serialise the current user, the provided parameter
	 * represents the serialised members container.
	 *
	 * By default we first commit the current user and then we store the user
	 * {@link kTAG_LID identifier} in the data member; when unserialising the
	 * {@link _UnserialiseUser() method} will load the user with the stored identifier.
	 *
	 * @param reference			   &$theData			Members serialise container.
	 *
	 * @access protected
	 *
	 * @uses UsersContainer()
	 */
	protected function _SerialiseUser( &$theData )
	{
		//
		// Handle user.
		//
		if( $save = $this->User() )
		{
			//
			// Commit user.
			//
			$save->Commit( $this->UsersContainer() );
			
			//
			// Serialise identifier.
			// Note that I use the member and not the method:
			// it would complain that the parameter is not a user object.
			//
			$theData[ 'mUser' ] = $save[ kTAG_LID ];
		
		} // Has user.
	
	} // _SerialiseUser.

		

/*=======================================================================================
 *																						*
 *							PROTECTED UNSERIALISATION INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_Unserialise																		*
	 *==================================================================================*/

	/**
	 * Unserialise object.
	 *
	 * This method should prepare elements to be serialised and store them in the provided
	 * array which has the following structure:
	 *
	 * <ul>
	 *	<li><i>0</i>: The first element is an array containing the array part of the object.
	 *	<li><i>1</i>: The second element is an array containing the object's data members,
	 *		each element is indexed by the data member variable name.
	 * </ul>
	 *
	 * The method expects the parameter to have been initialised.
	 *
	 * @param reference			   &$theData			Serialised data container.
	 *
	 * @access protected
	 *
	 * @uses _UnserialiseOffsets()
	 * @uses _UnserialiseDataStore()
	 * @uses _UnserialiseGraphStore()
	 * @uses _UnserialiseDatabase()
	 * @uses _UnserialiseUserContainer()
	 */
	protected function _Unserialise( &$theData )
	{
		//
		// Unserialise offsets.
		//
		$this->_UnserialiseOffsets( $theData[ 0 ] );
		
		//
		// Unserialise data store.
		//
		$this->_UnserialiseDataStore( $theData[ 1 ] );
		
		//
		// Unserialise graph store.
		//
		$this->_UnserialiseGraphStore( $theData[ 1 ] );
		
		//
		// Unserialise database.
		//
		$this->_UnserialiseDatabase( $theData[ 1 ] );
		
		//
		// Unserialise users container.
		//
		$this->_UnserialiseUserContainer( $theData[ 1 ] );
		
	} // _Unserialise.

	 
	/*===================================================================================
	 *	_UnserialiseOffsets																*
	 *==================================================================================*/

	/**
	 * Unserialise offsets.
	 *
	 * The duty of this method is to restore the object's offsets using the provided
	 * parameter.
	 *
	 * The method will simply copy the serialised offsets in the current object.
	 *
	 * Note that the expected parameter is the actual serialised container element reserved
	 * to offsets (<i>$theData[ 0 ]</i>), not the whole container.
	 *
	 * @param reference			   &$theData			Offsets serialise container.
	 *
	 * @access protected
	 */
	protected function _UnserialiseOffsets( &$theData )
	{
		$this->exchangeArray( $theData );
	
	} // _UnserialiseOffsets.

	 
	/*===================================================================================
	 *	_UnserialiseDataStore															*
	 *==================================================================================*/

	/**
	 * Unserialise data store.
	 *
	 * The duty of this method is to restore the data store using the provided parameter.
	 *
	 * In this class we declare this method as abstract, derived classes must decide what
	 * data store engine to use.
	 *
	 * @param reference			   &$theData			Members serialise container.
	 *
	 * @access protected
	 */
	abstract protected function _UnserialiseDataStore( &$theData );

	 
	/*===================================================================================
	 *	_UnserialiseGraphStore															*
	 *==================================================================================*/

	/**
	 * Unserialise graph store.
	 *
	 * The duty of this method is to restore the graph store using the provided parameter.
	 *
	 * In this class we declare this method as abstract, derived classes must decide what
	 * data store engine to use.
	 *
	 * @param reference			   &$theData			Members serialise container.
	 *
	 * @access protected
	 */
	abstract protected function _UnserialiseGraphStore( &$theData );

	 
	/*===================================================================================
	 *	_UnserialiseDatabase															*
	 *==================================================================================*/

	/**
	 * Unserialise database.
	 *
	 * The duty of this method is to restore the database using the provided parameter.
	 *
	 * In this class we declare this method as abstract, derived classes must decide what
	 * database engine to use.
	 *
	 * @param reference			   &$theData			Members serialise container.
	 *
	 * @access protected
	 */
	abstract protected function _UnserialiseDatabase( &$theData );

	 
	/*===================================================================================
	 *	_UnserialiseUserContainer														*
	 *==================================================================================*/

	/**
	 * Unserialise users container.
	 *
	 * The duty of this method is to restore the user container using the provided
	 * parameter.
	 *
	 * In this class we declare this method as abstract, derived classes must decide what
	 * user container to use.
	 *
	 * @param reference			   &$theData			Members serialise container.
	 *
	 * @access protected
	 */
	abstract protected function _UnserialiseUserContainer( &$theData );

	 
	/*===================================================================================
	 *	_UnserialiseUser																*
	 *==================================================================================*/

	/**
	 * Unserialise user.
	 *
	 * The duty of this method is to restore the current user using the provided parameter.
	 *
	 * If the stored user is not found, we do not raise an exception, we simply reset the
	 * user.
	 *
	 * @param reference			   &$theData			Members serialise container.
	 *
	 * @access protected
	 *
	 * @uses User()
	 * @uses _LoadUser()
	 */
	protected function _UnserialiseUser( &$theData )
	{
		//
		// Unserialise user.
		//
		if( array_key_exists( 'mUser', $theData ) )
			$this->_LoadUser( $theData[ 'mUser' ] );
		
		//
		// Reset user.
		//
		else
			$this->User( FALSE );
	
	} // _UnserialiseUser.

		

/*=======================================================================================
 *																						*
 *							PROTECTED VIEW MODEL INTERFACE								*
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
	 * The view model is stored in this object's offsets, the current web page will convert
	 * the data into JSON and use it in the Javascript procedures.
	 *
	 * In this class we register the eventual user data.
	 *
	 * @access protected
	 *
	 * @uses _RegisterUser()
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
	 *
	 * @throws {@link CException CException}
	 *
	 * @uses User()
	 * @uses _RegisterUserName()
	 * @uses _RegisterUserEmail()
	 * @uses _RegisterUserKind()
	 * @uses _RegisterUserRole()
	 * @uses _RegisterUserLogged()
	 */
	protected function _RegisterUser()
	{
		//
		// Get current user.
		//
		$user = $this->User();
		if( ($user === NULL)
		 || ($user instanceof CUser) )
		{
			//
			// Handle name.
			//
			$this->_RegisterUserName( $user );
			
			//
			// Handle e-mail.
			//
			$this->_RegisterUserEmail( $user );
			
			//
			// Handle kinds.
			//
			$this->_RegisterUserKind( $user );
			
			//
			// Handle roles.
			//
			$this->_RegisterUserRole( $user );
			
			//
			// Set logged flag.
			//
			$this->_RegisterUserLogged( $user );
		
		} // User object or no user.
		
		else
			throw new CException
				( "Unsupported user type",
				  kERROR_UNSUPPORTED,
				  kMESSAGE_TYPE_ERROR,
				  array( 'User' => $user ) );									// !@! ==>
		
	} // _RegisterUser.

	 
	/*===================================================================================
	 *	_RegisterUserName																*
	 *==================================================================================*/

	/**
	 * Load user name in view model.
	 *
	 * This method will load user {@load CUser::Name() name}  in the view model.
	 *
	 * The provided parameter represents the current {@link User() user}.
	 *
	 * This method can be overloaded if derived classes need to do do custom stuff with the
	 * name.
	 *
	 * Note that if the user is missing the name is set to <i>Login</i>, this assumes the
	 * name goes onto the login button which becomes a popup whe  the user has logged.
	 *
	 * @param mixed					$theData			Current user.
	 *
	 * @access protected
	 */
	protected function _RegisterUserName( $theData )
	{
		//
		// Handle name.
		//
		$this->offsetSet( kSESSION_USER_NAME,
						  ( ( $theData !== NULL ) ? $theData->Name() : 'Login' ) );
		
		
	} // _RegisterUserName.

	 
	/*===================================================================================
	 *	_RegisterUserEmail																*
	 *==================================================================================*/

	/**
	 * Load user e-mail in view model.
	 *
	 * This method will load user {@link CUser::Email() e-mail} in the view model.
	 *
	 * The provided parameter represents the current {@link User() user}.
	 *
	 * This method can be overloaded if derived classes need to do do custom stuff with the
	 * e-mail.
	 *
	 * @param mixed					$theData			Current user.
	 *
	 * @access protected
	 */
	protected function _RegisterUserEmail( $theData )
	{
		//
		// Handle e-mail.
		//
		$this->offsetSet( kSESSION_USER_EMAIL,
						  ( ( $theData !== NULL ) ? $theData->Email() : NULL ) );
		
	} // _RegisterUserEmail.

	 
	/*===================================================================================
	 *	_RegisterUserKind																*
	 *==================================================================================*/

	/**
	 * Load user kinds in view model.
	 *
	 * This method will load user {@load CUser::Kind() kinds} in the view model.
	 *
	 * The provided parameter represents the current {@link User() user}.
	 *
	 * This method can be overridden if derived classes need to do custom stuff using the
	 * user kinds; in this class we simply copy the property contents.
	 *
	 * @param mixed					$theData			Current user.
	 *
	 * @access protected
	 */
	protected function _RegisterUserKind( $theData )
	{
		//
		// Handle kinds.
		//
		$this->offsetSet( kSESSION_USER_KIND,
						  ( ( $theData !== NULL ) ? $theData->Kind() : Array() ) );
		
	} // _RegisterUserKind.

	 
	/*===================================================================================
	 *	_RegisterUserRole																*
	 *==================================================================================*/

	/**
	 * Load user roles in view model.
	 *
	 * This method will load user {@load CUser::Role() roles} in the view model.
	 *
	 * The provided parameter represents the current {@link User() user}.
	 *
	 * This method can be overridden if derived classes need to do custom stuff using the
	 * user roles; in this class we simply copy the property contents.
	 *
	 * @param mixed					$theData			Current user.
	 *
	 * @access protected
	 */
	protected function _RegisterUserRole( $theData )
	{
		//
		// Handle kinds.
		//
		$this->offsetSet( kSESSION_USER_ROLE,
						  ( ( $theData !== NULL ) ? $theData->Role() : Array() ) );
		
	} // _RegisterUserRole.

	 
	/*===================================================================================
	 *	_RegisterUserLogged																*
	 *==================================================================================*/

	/**
	 * Set user logged flag in view model.
	 *
	 * This method will set the logged {@link kSESSION_USER_LOGGED flag} in the view model.
	 *
	 * The provided parameter represents the current {@link User() user}.
	 *
	 * @param mixed					$theData			Current user.
	 *
	 * @access protected
	 */
	protected function _RegisterUserLogged( $theData )
	{
		//
		// Handle kinds.
		//
		$this->offsetSet( kSESSION_USER_LOGGED, (boolean) ( $theData !== NULL ) );
		
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
	 * {@link UsersContainer() container}, it expects the user identifier as the parameter.
	 *
	 * If the user is not found, it is not considered an error, so no exception will be
	 * raised and the current user will be reset; for this reason remember to
	 * {@link _Register() register} the view model whenever you call this method.
	 *
	 * The method will return the eventual old user if the operation replaced it.
	 *
	 * @param mixed					$theIdentifier		User identifier.
	 *
	 * @access protected
	 *
	 * @throws {@link CException CException}
	 *
	 * @uses UsersContainer()
	 * @uses User()
	 */
	protected function _LoadUser( $theIdentifier )
	{
		//
		// Instantiate user.
		//
		$user = new CUser( $this->UsersContainer(), $theIdentifier );
		
		return ( $user->Persistent() )
			 ? $this->User( $user, TRUE )											// ==>
			 : $this->User( FALSE, TRUE );											// ==>
		
	} // _LoadUser.

	 

} // class CSessionObject.


?>
