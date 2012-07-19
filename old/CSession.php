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
require_once( kPATH_LIBRARY_SOURCE."CSession.inc.php" );

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
 *	@package	MyWrapper
 *	@subpackage	Site
 */
class CSession extends CArrayObject
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
	 protected $mUserContainer = NULL;

		

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
 *								PUBLIC DATA MEMBER INTERFACE							*
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
	 *	ContainerUsers																	*
	 *==================================================================================*/

	/**
	 * Manage the session entities container.
	 *
	 * This method can be used to manage the session's user container
	 * {@link kSESSION_CONTAINER_USER reference}, the provided parameter represents either
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
	 * @see kSESSION_CONTAINER_USER
	 */
	public function ContainerUsers( $theValue = NULL, $getOld = FALSE )
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
					( "Invalid users container reference",
					  kERROR_INVALID_PARAMETER,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Container' => $theValue ) );						// !@! ==>
		}
		
		return CAttribute::ManageOffset
				( $this, kSESSION_CONTAINER_USER, $theValue, $getOld );			// ==>

	} // ContainerUsers.

	 
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
	 *	UserKind																		*
	 *==================================================================================*/

	/**
	 * Manage the session user kinds.
	 *
	 * This method can be used to manage the session's {@link CUser user}
	 * {@link kTAG_KIND kinds}, the provided parameter represents either the new value
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
	 * @see kSESSION_USER_KIND
	 */
	public function UserKind( $theValue = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageOffset
				( $this, kSESSION_USER_KIND, $theValue, $getOld );					// ==>

	} // UserKind.

	 
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
		$container = $this->ContainerUsers();
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
		// Load store.
		//
		$this->DataStore( New Mongo() );
		
		//
		// Load graph.
		//
		$this->GraphStore(
			new Everyman\Neo4j\Client(
				DEFAULT_kNEO4J_HOST, DEFAULT_kNEO4J_PORT ) );
		
		//
		// Load database.
		//
		$this->Database(
			$this->Store()->selectDB(
				kDEFAULT_DATABASE ) );
		
		//
		// Load entities container.
		//
		$this->ContainerUsers(
			$this->Database()->selectCollection(
				kDEFAULT_CNT_USERS ));

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
		// Set user login stamp.
		//
		$this->offsetSet( kSESSION_USER_STAMP, new CDatatypeStamp() );
		
		//
		// Normalise user.
		//
		if( ! ($theUser instanceof CUser) )
			$theUser = new CUser( $theUser );
		
		//
		// Set user ID.
		//
		$this->UserId( $theUser[ kTAG_LID ] );
		
		//
		// Set user name.
		//
		$this->UserName( $theUser->Name() );
		
		//
		// Set user email.
		//
		$this->UserEmail( $theUser->Email() );
		
		//
		// Set user kinds.
		//
		$this->UserKind( $theUser->Kind() );
		
		//
		// Set user roles.
		//
		$this->UserRole( $theUser->Role() );

	} // _LoadUser.

	 

} // class CSession.


?>
