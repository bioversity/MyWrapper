<?php

/**
 * <i>CSession</i> class definition.
 *
 * This file contains the class definition of <b>CSession</b> which wraps this class
 * {@link CArrayObject ancestor} around a session.
 *
 *	@package	MyWrapper
 *	@subpackage	Site
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 12/07/2012
*/

/*=======================================================================================
 *																						*
 *									CSession.php										*
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
 * Graph definitions.
 *
 * This include file contains the graph class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CGraphEdge.php" );

/**
 * Wrapper definitions.
 *
 * This include file contains the wrapper class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CWarehouseWrapper.php" );

/**
 * Local definitions.
 *
 * This include file contains all local definitions to this class.
 */
require_once( kPATH_LIBRARY_SOURCE."CSession.inc.php" );

/**
 *	Session object.
 *
 * This class implements a session object, it wraps the default PHP session array into a
 * class which is stored in a {@link kTAG_SESSION default} session offset.
 * 
 * The idea is to derive from this class and include the custom libraries.
 *
 *	@package	MyWrapper
 *	@subpackage	Site
 */
class CSession extends CArrayObject
{
		

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
	 * The constructor will instantiate an object either from an array, by loading all
	 * corresponding properties, or as an empty object.
	 *
	 * @param mixed					$theData			File structure.
	 *
	 * @access public
	 */
	public function __construct( $theData = NULL )
	{
		//
		// Empty statement.
		//
		if( $theData === NULL )
		{
			//
			// Call parent constructor.
			//
			parent::__construct();
			
			//
			// Initialise default resources.
			//
			$this->_InitResources();
		}
		
		//
		// Handle provided statement.
		//
		elseif( is_array( $theData )
			 || ($theData instanceof ArrayObject) )
			parent::__construct( (array) $theData );

	} // Constructor.

		

/*=======================================================================================
 *																						*
 *								PUBLIC MEMBER INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Graph																			*
	 *==================================================================================*/

	/**
	 * Manage the session graph reference.
	 *
	 * This method can be used to manage the session's graph
	 * {@link kSESSION_GRAPH reference}, the provided parameter represents either the new
	 * value or the operation to be performed:
	 *
	 * <ul>
	 *	<li><i>NULL</i>: Return the current value.
	 *	<li><i>FALSE</i>: Delete the current value.
	 *	<li><i>other</i>: Set the value with the provided parameter.
	 * </ul>
	 *
	 * The second parameter is a boolean which if <i>TRUE</i> will return the <i>old</i>
	 * value when replacing values; if <i>FALSE</i>, it will return the currently set value.
	 *
	 * Prior to setting the value the method will check whether the value is of the correct
	 * type.
	 *
	 * @param mixed					$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses CAttribute::ManageOffset()
	 *
	 * @see kSESSION_GRAPH
	 */
	public function Graph( $theValue = NULL, $getOld = FALSE )
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
					( "Invalid graph reference",
					  kERROR_INVALID_PARAMETER,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Graph' => $theValue ) );							// !@! ==>
		}
		
		return CAttribute::ManageOffset
				( $this, kSESSION_GRAPH, $theValue, $getOld );						// ==>

	} // Graph.

	 
	/*===================================================================================
	 *	Store																			*
	 *==================================================================================*/

	/**
	 * Manage the session store reference.
	 *
	 * This method can be used to manage the session's store
	 * {@link kSESSION_STORE reference}, the provided parameter represents either the new
	 * value or the operation to be performed:
	 *
	 * <ul>
	 *	<li><i>NULL</i>: Return the current value.
	 *	<li><i>FALSE</i>: Delete the current value.
	 *	<li><i>other</i>: Set the value with the provided parameter.
	 * </ul>
	 *
	 * The second parameter is a boolean which if <i>TRUE</i> will return the <i>old</i>
	 * value when replacing values; if <i>FALSE</i>, it will return the currently set value.
	 *
	 * Prior to setting the value the method will check whether the value is of the correct
	 * type.
	 *
	 * @param mixed					$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses CAttribute::ManageOffset()
	 *
	 * @see kSESSION_STORE
	 */
	public function Store( $theValue = NULL, $getOld = FALSE )
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
					( "Invalid store reference",
					  kERROR_INVALID_PARAMETER,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Store' => $theValue ) );							// !@! ==>
		}
		
		return CAttribute::ManageOffset
				( $this, kSESSION_STORE, $theValue, $getOld );						// ==>

	} // Store.

	 
	/*===================================================================================
	 *	Database																		*
	 *==================================================================================*/

	/**
	 * Manage the session database.
	 *
	 * This method can be used to manage the session's default database
	 * {@link kSESSION_DATABASE reference}, the provided parameter represents either the new
	 * value or the operation to be performed:
	 *
	 * <ul>
	 *	<li><i>NULL</i>: Return the current value.
	 *	<li><i>FALSE</i>: Delete the current value.
	 *	<li><i>other</i>: Set the value with the provided parameter.
	 * </ul>
	 *
	 * The second parameter is a boolean which if <i>TRUE</i> will return the <i>old</i>
	 * value when replacing values; if <i>FALSE</i>, it will return the currently set value.
	 *
	 * Prior to setting the value the method will check whether the value is of the correct
	 * type.
	 *
	 * @param mixed					$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses CAttribute::ManageOffset()
	 *
	 * @see kSESSION_DATABASE
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
			if( ! ($theValue instanceof MongoDb) )
				throw new CException
					( "Invalid database reference",
					  kERROR_INVALID_PARAMETER,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Database' => $theValue ) );						// !@! ==>
		}
		
		return CAttribute::ManageOffset
				( $this, kSESSION_DATABASE, $theValue, $getOld );					// ==>

	} // Database.

	 
	/*===================================================================================
	 *	ContainerEntities																*
	 *==================================================================================*/

	/**
	 * Manage the session entities container.
	 *
	 * This method can be used to manage the session's entities container
	 * {@link kSESSION_CONTAINER_ENTITY reference}, the provided parameter represents either
	 * the new value or the operation to be performed:
	 *
	 * <ul>
	 *	<li><i>NULL</i>: Return the current value.
	 *	<li><i>FALSE</i>: Delete the current value.
	 *	<li><i>other</i>: Set the value with the provided parameter.
	 * </ul>
	 *
	 * The second parameter is a boolean which if <i>TRUE</i> will return the <i>old</i>
	 * value when replacing values; if <i>FALSE</i>, it will return the currently set value.
	 *
	 * Prior to setting the value the method will check whether the value is of the correct
	 * type.
	 *
	 * @param mixed					$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses CAttribute::ManageOffset()
	 *
	 * @see kSESSION_CONTAINER_ENTITY
	 */
	public function ContainerEntities( $theValue = NULL, $getOld = FALSE )
	{
		//
		// New value.
		//
		if( ($theValue !== NULL)
		 && ($theValue !== FALSE) )
		{
			//
			// Normalise value.
			//
			if( $theValue instanceof MongoCollection )
				$theValue = new CMongoContainer( $theValue );
			
			//
			// Check value.
			//
			if( ! ($theValue instanceof CMongoContainer) )
				throw new CException
					( "Invalid entities container reference",
					  kERROR_INVALID_PARAMETER,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Container' => $theValue ) );						// !@! ==>
		}
		
		return CAttribute::ManageOffset
				( $this, kSESSION_CONTAINER_ENTITY, $theValue, $getOld );			// ==>

	} // ContainerEntities.

	 
	/*===================================================================================
	 *	UserId																			*
	 *==================================================================================*/

	/**
	 * Manage the session user ID.
	 *
	 * This method can be used to manage the session's {@link CUser user}
	 * {@link kTAG_LID identifier}, the provided parameter represents either the new value
	 * or the operation to be performed:
	 *
	 * <ul>
	 *	<li><i>NULL</i>: Return the current value.
	 *	<li><i>FALSE</i>: Delete the current value.
	 *	<li><i>other</i>: Set the value with the provided parameter.
	 * </ul>
	 *
	 * The second parameter is a boolean which if <i>TRUE</i> will return the <i>old</i>
	 * value when replacing values; if <i>FALSE</i>, it will return the currently set value.
	 *
	 * @param mixed					$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses CAttribute::ManageOffset()
	 *
	 * @see kSESSION_USER_ID
	 */
	public function UserId( $theValue = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageOffset
				( $this, kSESSION_USER_ID, $theValue, $getOld );					// ==>

	} // UserId.

	 
	/*===================================================================================
	 *	UserName																		*
	 *==================================================================================*/

	/**
	 * Manage the session user name.
	 *
	 * This method can be used to manage the session's {@link CUser user}
	 * {@link kTAG_NAME name}, the provided parameter represents either the new value
	 * or the operation to be performed:
	 *
	 * <ul>
	 *	<li><i>NULL</i>: Return the current value.
	 *	<li><i>FALSE</i>: Delete the current value.
	 *	<li><i>other</i>: Set the value with the provided parameter.
	 * </ul>
	 *
	 * The second parameter is a boolean which if <i>TRUE</i> will return the <i>old</i>
	 * value when replacing values; if <i>FALSE</i>, it will return the currently set value.
	 *
	 * @param mixed					$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses CAttribute::ManageOffset()
	 *
	 * @see kSESSION_USER_NAME
	 */
	public function UserName( $theValue = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageOffset
				( $this, kSESSION_USER_NAME, $theValue, $getOld );					// ==>

	} // UserName.

	 
	/*===================================================================================
	 *	UserEmail																		*
	 *==================================================================================*/

	/**
	 * Manage the session user e-mail.
	 *
	 * This method can be used to manage the session's {@link CUser user}
	 * {@link kOFFSET_EMAIL name}, the provided parameter represents either the new value
	 * or the operation to be performed:
	 *
	 * <ul>
	 *	<li><i>NULL</i>: Return the current value.
	 *	<li><i>FALSE</i>: Delete the current value.
	 *	<li><i>other</i>: Set the value with the provided parameter.
	 * </ul>
	 *
	 * The second parameter is a boolean which if <i>TRUE</i> will return the <i>old</i>
	 * value when replacing values; if <i>FALSE</i>, it will return the currently set value.
	 *
	 * @param mixed					$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses CAttribute::ManageOffset()
	 *
	 * @see kSESSION_USER_EMAIL
	 */
	public function UserEmail( $theValue = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageOffset
				( $this, kSESSION_USER_EMAIL, $theValue, $getOld );					// ==>

	} // UserEmail.

	 
	/*===================================================================================
	 *	UserRole																		*
	 *==================================================================================*/

	/**
	 * Manage the session user roles.
	 *
	 * This method can be used to manage the session's {@link CUser user}
	 * {@link kTAG_ROLE roles}, the provided parameter represents either the new value
	 * or the operation to be performed:
	 *
	 * <ul>
	 *	<li><i>NULL</i>: Return the current value.
	 *	<li><i>FALSE</i>: Delete the current value.
	 *	<li><i>other</i>: Set the value with the provided parameter.
	 * </ul>
	 *
	 * The second parameter is a boolean which if <i>TRUE</i> will return the <i>old</i>
	 * value when replacing values; if <i>FALSE</i>, it will return the currently set value.
	 *
	 * @param mixed					$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses CAttribute::ManageOffset()
	 *
	 * @see kSESSION_USER_ROLE
	 */
	public function UserRole( $theValue = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageOffset
				( $this, kSESSION_USER_ROLE, $theValue, $getOld );					// ==>

	} // UserRole.

		

/*=======================================================================================
 *																						*
 *								PUBLIC OPERATIONS INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Login																			*
	 *==================================================================================*/

	/**
	 * Login user.
	 *
	 * This method will check whether there is a login request, in that case it will check
	 * if the provided credentials are correct and {@link User() set} the
	 * {@link kSESSION_USER user} in the current session.
	 *
	 * The method will return:
	 *
	 * <ul>
	 *	<li><i>NULL</i>: No user login request intercepted.
	 *	<li><i>TRUE</i>: User credentials are correct.
	 *	<li><i>FALSE</i>: User credentials not correct.
	 * </ul>
	 *
	 * The method does not accept parameters: these will come from the request.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses CAttribute::ManageOffset()
	 *
	 * @see kSESSION_USER
	 */
	public function Login()
	{
		//
		// Get container.
		//
		$container = $this->ContainerEntities();
		if( $container !== NULL )
		{
			//
			// Check login command.
			//
			if( isset( $_REQUEST )
			 && array_key_exists( kAPI_OPERATION, $_REQUEST )
			 && isset( $_REQUEST[ kAPI_OPT_USER_CODE ] )
			 && isset( $_REQUEST[ kAPI_OPT_USER_PASS ] )
			 && ($_REQUEST[ kAPI_OPERATION ] == kAPI_OP_LOGIN) )
			{
				//
				// Get identifier.
				//
				$identifier = CUser::HashIndex( $_REQUEST[ kAPI_OPT_USER_CODE ] );
				
				//
				// Instantiate object.
				//
				$user = new CUser( $container, $identifier, kFLAG_STATE_ENCODED );
				
				//
				// Check user.
				//
				if( $user->Persistent() )
				{
					//
					// Check password.
					//
					if( $user->Password() === $_REQUEST[ kAPI_OPT_USER_PASS ] )
					{
						//
						// Set user data.
						//
						$this->_LoadUser( $user );
						
						return TRUE;												// ==>
									
					} // Password matches.
				
				} // User exists.
				
				return FALSE;														// ==>
			
			} // All required elements are there.
			
			return NULL;															// ==>
		
		} // Has entities container.
		
		throw new CException
			( "Missing entities container",
			  kERROR_OPTION_MISSING,
			  kMESSAGE_TYPE_ERROR );											// !@! ==>

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
	 * This method will initialise the default resources, in other words, those resources
	 * that can be loaded without parameters.
	 *
	 * The method will load the following properties:
	 *
	 * <ul>
	 *	<li><i>{@link Store() Store}</i>: The Mongo reference.
	 * </ul>
	 *
	 * @param mixed					$theUser			User object or record.
	 *
	 * @access protected
	 */
	protected function _InitResources()
	{
		//
		// Load graph.
		//
		$this->Graph(
			new Everyman\Neo4j\Client(
				DEFAULT_kNEO4J_HOST, DEFAULT_kNEO4J_PORT ) );
		
		//
		// Load store.
		//
		$this->Store( New Mongo() );
		
		//
		// Load database.
		//
		$this->Database(
			$this->Store()->selectDB(
				kDEFAULT_DATABASE ) );
		
		//
		// Load entities container.
		//
		$this->ContainerEntities(
			$this->Database()->selectCollection(
				kDEFAULT_CNT_ENTITIES ));

	} // _InitResources.

		

/*=======================================================================================
 *																						*
 *								PROTECTED LOADING INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_LoadUser																		*
	 *==================================================================================*/

	/**
	 * Load user.
	 *
	 * This method will load the provided user data into the current session, it expects the
	 * provided parameter to either be a user {@link CUser instance} or an array with its
	 * contents.
	 *
	 * The method will load the following properties:
	 *
	 * <ul>
	 *	<li><i>{@link UserId() ID}</i>: The {@link CUser user} {@link kTAG_LID identifier}.
	 *	<li><i>{@link UserName() Name}</i>: The {@link CUser user} {@link kTAG_NAME name}.
	 *	<li><i>{@link UserEmail() E-mail}</i>: The {@link CUser user}
	 *		{@link kOFFSET_EMAIL e-mail}.
	 *	<li><i>{@link UserRole() Roles}</i>: The {@link CUser user} {@link kTAG_ROLE roles}.
	 * </ul>
	 *
	 * @param mixed					$theUser			User object or record.
	 *
	 * @access protected
	 */
	protected function _LoadUser( $theUser )
	{
		//
		// Normalise user.
		//
		if( ! ($theUser instanceof CUser) )
			$theUser = new CUser( $theUser );
		
		//
		// Set user ID.
		//
		$this->IserId( $theUser[ kTAG_LID ] );
		
		//
		// Set user name.
		//
		$this->UserName( $theUser->Name() );
		
		//
		// Set user email.
		//
		$this->UserEmail( $theUser->Email() );
		
		//
		// Set user roles.
		//
		$this->UserRole( $theUser->Role() );

	} // _LoadUser.

	 

} // class CSession.


?>
