<?php

/**
 * <i>CContainer</i> class definition.
 *
 * This file contains the class definition of <b>CContainer</b> which represents the
 * ancestor of all object stores in this library.
 *
 *	@package	Framework
 *	@subpackage	Persistence
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 07/03/2012
 */

/*=======================================================================================
 *																						*
 *										CContainer.php									*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CObject.php" );

/**
 * Persistent objects data store ancestor.
 *
 * This <i>abstract</i> class is the ancestor of all persistent object data stores, it
 * implements the common interfaces shared by all concrete instances of object data stores.
 *
 * The main duty of this object is to store, retrieve and delete objects from a persistent
 * container.
 *
 * <ul>
 *	<li><i>{@link Commit() Commit}</i>: This method will commit the provided object into the
 *		current object store.
 *	<li><i>{@link Load() Load}</i>: This method will search for the object identified by
 *		the provided value in the object store and return it.
 *	<li><i>{@link Delete() Delete}</i>: This method will search for the object identified by
 *		the provided value in the object store and delete it.
 * </ul>
 *
 * The class is designed to work with one object at the time, which means that it expects
 * and returns one object. The identifier of the object must be stored under the
 * {@link kTAG_ID_NATIVE kTAG_ID_NATIVE} key.
 *
 * @package		Framework
 * @subpackage	Persistence
 */
abstract class CContainer extends CObject
{
	/**
	 * Persistent data store.
	 *
	 * This data member holds the native persistent store.
	 *
	 * @var mixed
	 */
	 protected $mContainer = NULL;

		

/*=======================================================================================
 *																						*
 *											MAGIC										*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	__construct																		*
	 *==================================================================================*/

	/**
	 * Instantiate class.
	 *
	 * The constructor is used to instantiate the class with a native data store, the method
	 * expects a single parameter that represents the native database, table or container.
	 *
	 * Although the container provides a default value for the parameter, this will not be
	 * accepted, since concrete instances must provide their custom default container.
	 *
	 * @param mixed					$theContainer		Native persistent container.
	 *
	 * @access public
	 */
	public function __construct( $theContainer = NULL )
	{
		$this->Container( $theContainer );
		
	} // Constructor.

		

/*=======================================================================================
 *																						*
 *								PUBLIC MEMBER INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Container																		*
	 *==================================================================================*/

	/**
	 * Manage persistent container.
	 *
	 * This method can be used to manage the persistent container, it accepts a single
	 * parameter which represents either the container or the requested operation,
	 * depending on its value:
	 *
	 * <ul>
	 *	<li><i>NULL</i>: Return the current value.
	 *	<li><i>other</i>: Set the value with the provided parameter.
	 * </ul>
	 *
	 * The second parameter is a boolean which if <i>TRUE</i> will return the <i>old</i>
	 * value when setting a new value; if <i>FALSE</i>, it will return the newly set value.
	 *
	 * It is not allowed to delete the container.
	 *
	 * @param mixed					$theValue			Persistent container or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 */
	public function Container( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageMember( $this->mContainer, $theValue, $getOld );		// ==>

	} // Container.

		

/*=======================================================================================
 *																						*
 *								PUBLIC MANAGEMENT INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Commit																			*
	 *==================================================================================*/

	/**
	 * Commit provided object.
	 *
	 * This method can be used to commit the provided object to the current data store.
	 *
	 * The provided object must be either an array or an ArrayObject.
	 *
	 * The method will return the object's unique {@link kTAG_ID_NATIVE identifier} and
	 * raise an exception if not successful.
	 *
	 * The actual operation will be performed by a protected {@link _Commit() method}.
	 *
	 * @param mixed					$theObject			Object to commit.
	 * @param mixed					$theOptions			Commit options.
	 *
	 * @access public
	 * @return mixed
	 */
	public function Commit( $theObject, $theOptions = NULL )
	{
		//
		// Check object type.
		//
		if( is_array( $theObject )
		 || ($theObject instanceof ArrayObject) )
			return $this->_Commit( $theObject, $theOptions );						// ==>

		throw new CException
			( "Invalid object",
			  kERROR_INVALID_PARAMETER,
			  kMESSAGE_TYPE_ERROR,
			  array( 'Object' => $theObject ) );								// !@! ==>

	} // Commit.

	 
	/*===================================================================================
	 *	Load																			*
	 *==================================================================================*/

	/**
	 * Load object.
	 *
	 * This method can be used to load an object identified by the provided identifier from
	 * the data store.
	 *
	 * The method will return the found object, or it will return <i>NULL</i>.
	 *
	 * @param mixed					$theIdentifier		Object identifier.
	 * @param mixed					$theOptions			Load options.
	 *
	 * @access public
	 * @return mixed
	 */
	public function Load( $theIdentifier, $theOptions = NULL )
	{
		//
		// Perform load.
		//
		return $this->_Load( $theIdentifier, $theOptions );							// ==>

	} // Load.

	 
	/*===================================================================================
	 *	Delete																			*
	 *==================================================================================*/

	/**
	 * Delete object.
	 *
	 * This method can be used to remove an object identified by the provided identifier
	 * from the data store.
	 *
	 * The method will return the object, if found, or it will return <i>NULL</i>.
	 *
	 * @param mixed					$theIdentifier		Object identifier.
	 * @param mixed					$theOptions			Delete options.
	 *
	 * @access public
	 * @return mixed
	 */
	public function Delete( $theIdentifier, $theOptions = NULL )
	{
		//
		// Perform delete.
		//
		return $this->_Delete( $theIdentifier, $theOptions );						// ==>

	} // Delete.

		

/*=======================================================================================
 *																						*
 *								PROTECTED MEMBER INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	&_Container																		*
	 *==================================================================================*/

	/**
	 * Get container reference.
	 *
	 * This method can be used to retrieve a reference to the native container member.
	 *
	 * @access protected
	 * @return mixed
	 */
	protected function &_Container()						{	return &$this->mContainer;	}

		

/*=======================================================================================
 *																						*
 *								PROTECTED MANAGEMENT INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_Commit																			*
	 *==================================================================================*/

	/**
	 * Commit provided object.
	 *
	 * Derived classes must implement this method to perform the actual storage of the
	 * provided.
	 *
	 * The provided object will be either an array or an ArrayObject.
	 *
	 * The method will return the object's unique {@link kTAG_ID_NATIVE identifier} and
	 * raise an exception if not successful.
	 *
	 * @param mixed					$theObject			Object to commit.
	 * @param mixed					$theOptions			Delete options.
	 *
	 * @access protected
	 * @return mixed
	 */
	abstract protected function _Commit( $theObject, $theOptions = NULL );

	 
	/*===================================================================================
	 *	_Load																			*
	 *==================================================================================*/

	/**
	 * Load object.
	 *
	 * Derived classes must implement this method to perform the actual search for the
	 * object identified by the provided identifier.
	 *
	 * The method will return the found object or <i>NULL</i> if not found.
	 *
	 * @param mixed					$theIdentifier		Object identifier.
	 * @param mixed					$theOptions			Delete options.
	 *
	 * @access protected
	 * @return mixed
	 */
	abstract protected function _Load( $theIdentifier, $theOptions = NULL );

	 
	/*===================================================================================
	 *	_Delete																			*
	 *==================================================================================*/

	/**
	 * Load object.
	 *
	 * Derived classes must implement this method to perform the actual search for the
	 * object identified by the provided identifier.
	 *
	 * The method will return the found (deleted) object or <i>NULL</i> if not found.
	 *
	 * @param mixed					$theIdentifier		Object identifier.
	 * @param mixed					$theOptions			Delete options.
	 *
	 * @access protected
	 * @return mixed
	 */
	abstract protected function _Delete( $theIdentifier, $theOptions = NULL );

	 

} // class CContainer.


?>
