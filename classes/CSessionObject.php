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
	 * Serialization interface.
	 *
	 * We overload the inherited method to allow performing custom actions before the object
	 * gets serialized.
	 *
	 * The method will call a protected {@link _Serialise() method} which takes care of
	 * preparing the object's properties before the object gets serialized.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses _Serialise()
	 */
	public function serialize()
	{
		//
		// Prepare object.
		//
		$this->_Serialise();
		
		return parent::serialize();													// ==>
		
	} // serialize().

	 
	/*===================================================================================
	 *	unserialize																		*
	 *==================================================================================*/

	/**
	 * Unserialization interface.
	 *
	 * We overload the inherited method to allow performing custom actions after the object
	 * got unserialized.
	 *
	 * The method will first call the inherited method, then it will execute a protected
	 * {@link _Unserialise() method} which can restore resources normalised by the
	 * protected {@link _Serialise() interface}.
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
		// Unserialise object.
		//
		parent::unserialize( $theData );
		
		//
		// Restore object.
		//
		$this->_Unserialise();
		
		//
		// Update view model.
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
	 *	ShowDebug																		*
	 *==================================================================================*/

	/**
	 * Manage the debug switch.
	 *
	 * This method can be used to show or hide the debug elements by managing the
	 * {@link kSESSION_DEBUG kSESSION_DEBUG} offset, the method accepts two parameters:
	 *
	 * <ul>
	 *	<li><b>$theValue</b>: The value to set or the operation to perform:
	 *	 <ul>
	 *		<li><i>NULL</i>: Retrieve the current value.
	 *		<li><i>FALSE</i>: Hide the element.
	 *		<li><i>TRUE</i>: Show the element.
	 *	 </ul>
	 *	<li><b>$getOld</b>: A boolean flag determining which value the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value <i>before</i> it has eventually been modified,
	 *			this option is only relevant when deleting or replacing a value.
	 *		<li><i>FALSE</i>: Return the value <i>after</i> it has eventually been modified.
	 *	 </ul>
	 * </ul>
	 *
	 * If the element exists the method will return its value, if not, it will return
	 * <i>NULL</i>.
	 *
	 * @param mixed					$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses _ManageFlag()
	 *
	 * @see kSESSION_DEBUG
	 */
	public function ShowDebug( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageFlag( kSESSION_DEBUG, $theValue, $getOld );			// ==>

	} // ShowDebug.

	 
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
		if( $theValue !== NULL )
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
	 * In this class it does nothing.
	 *
	 * @access protected
	 */
	protected function _Init()															   {}

		

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
	 * is to normalise the object's properties before these get serialized. This may be
	 * necessary if elements of the object cannot be serialized, such as closures, or if
	 * automatic commits should be issued before.
	 *
	 * In this class we {@link _SerialiseUser() commit} the current user if there.
	 *
	 * @access protected
	 *
	 * @uses _SerialiseUser()
	 */
	protected function _Serialise()
	{
		//
		// Serialise user.
		//
		$this->_SerialiseUser();
		
	} // _Serialise.

		
	/*===================================================================================
	 *	_SerialiseUser																	*
	 *==================================================================================*/

	/**
	 * Serialise user.
	 *
	 * This method is called before the object gets {@link serialize() serialised}, its duty
	 * is to normalise the current user before the object goes to sleep.
	 *
	 * In this class we {@link CUser::Commit() commit} the current user and replace the user
	 * object with the user {@link kTAG_LID identifier}, so that when
	 * {@link unserialize() restoring} the object, the user record will be read from the
	 * data store again.
	 *
	 * @access protected
	 *
	 * @uses UsersContainer()
	 */
	protected function _SerialiseUser()
	{
		//
		// Handle user.
		//
		if( ($save = $this->User()) !== NULL )
		{
			//
			// Commit user.
			//
			$save->Commit( $this->UsersContainer() );
			
			//
			// Replace with identifier.
			//
			$this->mUser = $save->offsetGet( kTAG_LID );
		
		} // Has user.
	
	} // _SerialiseUser.

		

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
	 * In this class we {@link _UnserialiseUser() refresh} the current user if there.
	 *
	 * @access protected
	 *
	 * @uses _UnserialiseUser()
	 */
	protected function _Unserialise()
	{
		//
		// Unserialise user.
		//
		$this->_UnserialiseUser();
		
	} // _Unserialise.

	 
	/*===================================================================================
	 *	_UnserialiseUser																*
	 *==================================================================================*/

	/**
	 * Unserialise user.
	 *
	 * This method is called after the object gets {@link unserialize() unserialised}, its
	 * duty is to restore the current user after it was {@link _SerialiseUser() normalised}
	 * before the object was {@link serialize() serialized}.
	 *
	 * In this class we {@link _LoadUser() restore} the current user using its
	 * {@link kTAG_LID identifier} stored by the serialization
	 * {@link _SerialiseUser() method}.
	 *
	 * @access protected
	 *
	 * @uses User()
	 * @uses _LoadUser()
	 */
	protected function _UnserialiseUser()
	{
		//
		// Get user identifier.
		//
		$user = $this->User();
		
		//
		// Unserialise user.
		// Note that we cannot use the accessor method,
		// because it only accepts CUser objects.
		//
		if( $user !== NULL )
			$this->_LoadUser( $user );
	
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
	 * In this class we register the debug switch and the eventual user data.
	 *
	 * @access protected
	 *
	 * @uses _RegisterDebug()
	 * @uses _RegisterUser()
	 */
	protected function _Register()
	{
		//
		// Register properties.
		//
		$this->_RegisterDebug();
		$this->_RegisterUser();
		
	} // _Register.

	 
	/*===================================================================================
	 *	_RegisterDebug																	*
	 *==================================================================================*/

	/**
	 * Set debug switch in view model.
	 *
	 * This method will set the debug switch in the view model. The method will check if
	 * the {@link kDEFAULT_DEBUG kDEFAULT_DEBUG} symbol is defined, if this is the case it
	 * will use its value, if not the switch will be turned off.
	 *
	 * @access protected
	 *
	 * @uses ShowDebug()
	 */
	protected function _RegisterDebug()
	{
		//
		// Check symbol.
		//
		if( defined( 'kDEFAULT_DEBUG' ) )
			$this->ShowDebug( (boolean) kDEFAULT_DEBUG );
		else
			$this->ShowDebug( FALSE );
		
	} // _RegisterDebug.

	 
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
						  ( ( $theData !== NULL ) ? $theData->Email() : '' ) );
		
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
		// Get kinds.
		//
		$data = ( ($theData !== NULL)
			   && (($tmp = $theData->Kind()) !== NULL) )
			  ? $tmp
			  : Array();

		//
		// Handle roles.
		//
		$this->offsetSet( kSESSION_USER_KIND, $data );
		
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
		// Get roles.
		//
		$data = ( ($theData !== NULL)
			   && (($tmp = $theData->Role()) !== NULL) )
			  ? $tmp
			  : Array();

		//
		// Handle roles.
		//
		$this->offsetSet( kSESSION_USER_ROLE, $data );
		
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

		

/*=======================================================================================
 *																						*
 *							PROTECTED UTILITIES INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_ManageFlag																		*
	 *==================================================================================*/

	/**
	 * This method can be used to set or reset a flag offset, the method accepts three
	 * parameters:
	 *
	 * <ul>
	 *	<li><b>$theOffset</b>: The offset to be managed.
	 *	<li><b>$theValue</b>: The value to set or the operation to perform:
	 *	 <ul>
	 *		<li><i>NULL</i>: Retrieve the current value.
	 *		<li><i>FALSE</i>: Hide the element.
	 *		<li><i>TRUE</i>: Show the element.
	 *	 </ul>
	 *	<li><b>$getOld</b>: A boolean flag determining which value the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value <i>before</i> it has eventually been modified,
	 *			this option is only relevant when deleting or replacing a value.
	 *		<li><i>FALSE</i>: Return the value <i>after</i> it has eventually been modified.
	 *	 </ul>
	 * </ul>
	 *
	 * If the element exists the method will return its value, if not, it will return
	 * <i>NULL</i>.
	 *
	 * @param string				$theOffset			Offset.
	 * @param mixed					$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 */
	public function _ManageFlag( $theOffset, $theValue = NULL, $getOld = FALSE )
	{
		//
		// Save current value.
		//
		$save = ( $this->offsetExists( $theOffset ) )
			  ? $this->offsetGet( $theOffset )
			  : NULL;
		
		//
		// Return current value.
		//
		if( $theValue === NULL )
			return $save;															// ==>
		
		//
		// Set new value.
		//
		$this->offsetSet( $theOffset, (boolean) $theValue );
		
		if( $getOld	)
			return $save;															// ==>
		
		return (boolean) $theValue;													// ==>
		
	} // _ManageFlag.

	 

} // class CSessionObject.


?>
