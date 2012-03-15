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
 * Types.
 *
 * This include file contains all type definitions.
 */
require_once( kPATH_LIBRARY_DEFINES."Types.inc.php" );

/**
 * Persistent objects data store ancestor.
 *
 * This <i>abstract</i> class is the ancestor of all persistent object data stores, it
 * implements the common interfaces shared by all concrete instances of object data stores.
 *
 * The class is designed to work with one object at the time, which means that it expects
 * and returns one object, it does not handle object collections.
 *
 * Persistence operations are performed through three methods:
 *
 * <ul>
 *	<li><i>{@link Commit() Commit}</i>: This method will insert, replace or modify objects
 *		in the current object store.
 *	<li><i>{@link Load() Load}</i>: This method will retrieve objects from the current
 *		object store.
 *	<li><i>{@link Delete() Delete}</i>: This method will remove objects from the current
 *		object store.
 * </ul>
 *
 * The class features a {@link Container() member} that holds the native data store.
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
	 * You instantiate the class with a native data store, the method expects a single
	 * parameter that will be handled specifically by specialised derived classes.
	 *
	 * This method should only be overloaded if you need to set a default value, or if you
	 * need to make specific initialisation before this method sets the
	 * {@link Container() container}.
	 *
	 * You should check the type of the provided parameter in the
	 * {@link Container() Container()} method rather than here.
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
	 * One is not allowed to delete the container.
	 *
	 * In derived classes you should overload this method to check if the provided container
	 * is of the correct type.
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
	 * This method can be used to commit the provided object to the current data store, it
	 * expects three parameters:
	 *
	 * <ul>
	 *	<li><b>$theObject</b>: The object to be committed.
	 *	<li><b>$theIdentifier</b>: This parameter is expected to be the object's unique
	 *		identifier within the container, it will be the {@link Load() access} key to the
	 *		object once committed. The default value is <i>NULL</i>, this will generally be
	 *		the case when inserting objects that expect their identifier to be computed by
	 *		the container itself; in all other cases the parameter is required.
	 *	<li><b>$theModifiers</b>: This parameter represents the commit operation options, by
	 *		default we assume it is a bitfield where the following values apply:
	 *	 <ul>
	 *		<li><i>{@link kFLAG_PERSIST_INSERT kFLAG_PERSIST_INSERT}</i>: The provided
	 *			object will be inserted in the container, it is assumed that no other
	 *			element in the container shares the same identifier, in that case the method
	 *			must raise an {@link kERROR_DUPLICATE exception}.
	 *		<li><i>{@link kFLAG_PERSIST_UPDATE kFLAG_PERSIST_UPDATE}</i>: The provided
	 *			object will replace the existing object. In this case the method expects
	 *			the container to have an entry with the same key as the provided identifier,
	 *			if this is not the case the method must raise an
	 *			{@link kERROR_NOT_FOUND exception}. With this option it is assumed that the
	 *			provided object's attributes will replace all the existing object's ones.
	 *		<li><i>{@link kFLAG_PERSIST_MODIFY kFLAG_PERSIST_MODIFY}</i>: The provided
	 *			object is assumed to contain a subset of an existing object's attributes,
	 *			these provided attributes will be appended or replace the existing ones.
	 *			In this case the method expects the container to have an entry with the
	 *			same key as the provided identifier, if this is not the case the method must
	 *			raise an {@link kERROR_NOT_FOUND exception}.
	 *		<li><i>{@link kFLAG_PERSIST_REPLACE kFLAG_PERSIST_REPLACE}</i>: The provided
	 *			object will be {@link kFLAG_PERSIST_INSERT inserted}, if the identifier
	 *			doesn't match any container elements, or it will
	 *			{@link kFLAG_PERSIST_UPDATE replace} the existing object. As with
	 *			{@link kFLAG_PERSIST_UPDATE update}, it is assumed that the provided
	 *			object's attributes will replace all the existing object's ones.
	 *		<li><i>{@link kFLAG_PERSIST_DELETE kFLAG_PERSIST_DELETE}</i>: This option
	 *			assumes you want to remove the object from the container, although this
	 *			operation has its own public {@link Delete() method}, derived classes can
	 *			use this option to implement a <i>deleted state</i>, rather than actually
	 *			removing the object from the container.
	 *	 </ul>
	 * </ul>
	 *
	 * The method will return the object's key within the container or raise an exception if
	 * the operation was not successful.
	 *
	 * This method relies on a virtual {@link _Commit() method} that will perform the actual
	 * operation, here we only perform the following controls:
	 *
	 * <ul>
	 *	<li><i>Check identifier</i>: by default we assume that all operations except
	 *		{@link kFLAG_PERSIST_INSERT insert} require the identifier to be provided. If
	 *		you handle default values for identifiers, you should overload this method.
	 *	<li><i>{@link _Commit() Commit}</i>: we call the protected interface to perform the
	 *		requested operation, this interface must be implemented by derived classes.
	 * </ul>
	 *
	 * By default we assume the provided object to be either an array or an ArrayObject.
	 *
	 * The actual operation will be performed by a protected {@link _Commit() method}.
	 *
	 * @param reference			   &$theObject			Object to commit.
	 * @param mixed					$theIdentifier		Object identifier.
	 * @param bitfield				$theModifiers		Commit modifiers.
	 *
	 * @access public
	 * @return mixed
	 */
	public function Commit( &$theObject,
							 $theIdentifier = NULL,
							 $theModifiers = kFLAG_PERSIST_REPLACE )
	{
		//
		// Identifier is required
		//
		if( (! ($theModifiers & kFLAG_PERSIST_INSERT))	// Not an insert
		 && ($theIdentifier === NULL) )					// and missing identifier.
			throw new CException
				( "Requested operation expects identifier",
				  kERROR_OPTION_MISSING,
				  kMESSAGE_TYPE_ERROR,
				  array( 'Identifier' => $theIdentifier,
						 'Modifiers' => $theModifiers ) );						// !@! ==>
		
		//
		// Perform operation.
		//
		if( $theModifiers & kFLAG_PERSIST_MASK )
			return $this->_Commit( $theObject, $theIdentifier, $theModifiers );		// ==>

		throw new CException
			( "Invalid operation options",
			  kERROR_INVALID_PARAMETER,
			  kMESSAGE_TYPE_ERROR,
			  array( 'Modifiers' => $theModifiers ) );							// !@! ==>

	} // Commit.

	 
	/*===================================================================================
	 *	Load																			*
	 *==================================================================================*/

	/**
	 * Load object.
	 *
	 * This method can be used to load an object fron the container:
	 *
	 * <ul>
	 *	<li><b>$theIdentifier</b>: The key to the object in the container.
	 *	<li><b>$theModifiers</b>: Optional bitfield that can be used to provide options for
	 *		the operation. In this class we do not use this parameter.
	 * </ul>
	 *
	 * The method should return the found object, or <i>NULL</i> if not found.
	 *
	 * In this class we simply call the protected {@link _Load() method} which must be
	 * implemented by derived classes. You should only overload this method if you need to
	 * initialise or process the identifier before passing it to the protected interface.
	 *
	 * @param mixed					$theIdentifier		Object identifier.
	 * @param bitfield				$theModifiers		Load modifiers.
	 *
	 * @access public
	 * @return mixed
	 */
	public function Load( $theIdentifier, $theModifiers = kFLAG_DEFAULT )
	{
		//
		// Perform load.
		//
		return $this->_Load( $theIdentifier, $theModifiers );						// ==>

	} // Load.

	 
	/*===================================================================================
	 *	Delete																			*
	 *==================================================================================*/

	/**
	 * Delete object.
	 *
	 * This method can be used to remove an object fron the container:
	 *
	 * <ul>
	 *	<li><b>$theIdentifier</b>: The key to the object in the container.
	 *	<li><b>$theModifiers</b>: Optional bitfield that can be used to provide options for
	 *		the operation. In this class we do not use this parameter.
	 * </ul>
	 *
	 * The method should return the deleted object, or <i>NULL</i> if not found.
	 *
	 * In this class we simply call the protected {@link _Delete() method} which must be
	 * implemented by derived classes. You should only overload this method if you need to
	 * initialise or process the identifier before passing it to the protected interface.
	 *
	 * @param mixed					$theIdentifier		Object identifier.
	 * @param bitfield				$theModifiers		Delete modifiers.
	 *
	 * @access public
	 * @return mixed
	 */
	public function Delete( $theIdentifier, $theModifiers = kFLAG_DEFAULT )
	{
		//
		// Perform delete.
		//
		return $this->_Delete( $theIdentifier, $theModifiers );						// ==>

	} // Delete.

		

/*=======================================================================================
 *																						*
 *								PUBLIC CONVERSION INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	SerialiseObject																	*
	 *==================================================================================*/

	/**
	 * Serialise provided object.
	 *
	 * This method should be used on objects containing custom data types that cannot be
	 * converted to {@link CObject::JsonEncode() JSON}, its duty is to convert special types
	 * into a format that can then be restored using the counter-method
	 * {@link UnserialiseObject() UnserialiseObject}.
	 *
	 * The method will scan the provided data elements, array or ArrayObject elements will
	 * be recursed, scalar elements will be sent to another public
	 * {@link SerialiseData() method} that will take care of converting the data if
	 * necessary, the converted data will be an array structured as follows:
	 *
	 * <ul>
	 *	<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>: This offset represents the data type in
	 *		which the data contained in the next offset is encoded.
	 *	<li><i>{@link kTAG_DATA kTAG_DATA}</i>: This offset represents the actual data, it
	 *		may be a scalar or an array containing the subcomponents of the data.
	 * </ul>
	 *
	 * The method accepts a single parameter which must be an array or ArrayObject instance,
	 * it will perform the conversion on the object itself. If you need to restore later the
	 * object to the state in which it was provided to this method, you should use the
	 * counter method {@link UnserialiseObject() UnserialiseObject}.
	 *
	 * The {@link SerialiseData() SerialiseData} method is not implemented in this class,
	 * if in derived classes you want to take advantage of this feature you must implement
	 * it.
	 *
	 * @param reference			   &$theObject			Object to decode.
	 *
	 * @access public
	 */
	public function SerialiseObject( &$theObject )
	{
		//
		// Intercept structures.
		//
		if( is_array( $theObject )
		 || ($theObject instanceof ArrayObject) )
		{
			//
			// Recurse.
			//
			foreach( $theObject as $key => $value )
				$this->SerialiseObject( $theObject[ $key ] );
		
		} // Is a struct.
		
		//
		// Convert scalars.
		//
		else
			$this->SerialiseData( $theObject );
	
	} // SerialiseObject.

	 
	/*===================================================================================
	 *	SerialiseData																	*
	 *==================================================================================*/

	/**
	 * Serialise provided data element.
	 *
	 * This method should convert the provided scalar into a structure containing the
	 * {@link kTAG_TYPE type} and the normalised {@link kTAG_DATA data}, and replace the
	 * provided reference with this structure.
	 *
	 * This method is called by a public {@link SerialiseObject() interface} which traverses
	 * an object and provides this method with all scalar elements.
	 *
	 * This method should process all data types that cannot be fully represented in JSON
	 * or those that need to be converted from a scalar to a structure, the method will
	 * replace the referenced element with an array in which the data type is set in the
	 * {@link kTAG_TYPE type} offset, and the normalised data in the {@link kTAG_DATA data}
	 * offset.
	 *
	 * For instance, a binary data object would be converted into an array where the
	 * {@link kTAG_TYPE type} offset would be set as {@link kDATA_TYPE_BINARY binary} and
	 * the {@link kTAG_DATA data} offset would be set with the hexadecimal representstion
	 * of that data.
	 *
	 * To convert the data back to the format it was provided, you should use the
	 * {@link UnserialiseData() UnserialiseData} method.
	 *
	 * The conversion is performed on the provided element itself.
	 *
	 * In this class we do not perform any conversions.
	 *
	 * @param reference			   &$theElement			Element to encode.
	 *
	 * @access public
	 */
	public function SerialiseData( &$theElement )										   {}

	 
	/*===================================================================================
	 *	UnserialiseObject																*
	 *==================================================================================*/

	/**
	 * Unserialise provided object.
	 *
	 * This method should be used on objects converted using the
	 * {@link SerialiseObject() SerialiseObject} method, its duty is to convert the elements
	 * of the object that were converted to arrays back to the data type indicated in the
	 * {@link kTAG_TYPE kTAG_TYPE} offset.
	 *
	 * The method will scan the provided structure and select all elements which are either
	 * arrays or ArrayObjects having both the {@link kTAG_TYPE kTAG_TYPE} and
	 * {@link kTAG_DATA kTAG_DATA} offsets, these elements will be sent to the
	 * {@link UnserialiseData() UnserialiseData} method that will take care of converting
	 * the array back into the data type indicated in the {@link kTAG_TYPE kTAG_TYPE}
	 * offset using the data in the {@link kTAG_DATA kTAG_DATA} offset.
	 *
	 * The method accepts a single parameter which must be an array or ArrayObject instance,
	 * it will perform the encoding on the object itself. If you need to restore later the
	 * object to the state in which it was provided to this method, you should use the
	 * counter method {@link SerialiseObject() SerialiseObject}.
	 *
	 * The {@link UnserialiseData() UnserialiseData} method is not implemented in this
	 * class, if in derived classes you want to take advantage of this feature you must
	 * implement it.
	 *
	 * @param reference			   &$theObject			Object to encode.
	 *
	 * @access public
	 */
	public function UnserialiseObject( &$theObject )
	{
		//
		// Intercept structures.
		//
		if( is_array( $theObject )
		 || ($theObject instanceof ArrayObject) )
		{
			//
			// Traverse object.
			//
			foreach( $theObject as $key => $value )
			{
				//
				// Intercept structs.
				//
				if( is_array( $value )
				 || ($value instanceof ArrayObject) )
				{
					//
					// Try conversion.
					//
					if( array_key_exists( kTAG_TYPE, (array) $value )
					 && array_key_exists( kTAG_DATA, (array) $value ) )
						$this->UnserialiseData( $theObject[ $key ] );
					
					//
					// Recurse.
					//
					else
						$this->UnserialiseObject( $theObject[ $key ] );
				
				} // Is a struct.
			
			} // Traversing object.
		
		} // Is a struct.
	
	} // UnserialiseObject.

	 
	/*===================================================================================
	 *	UnserialiseData																	*
	 *==================================================================================*/

	/**
	 * Unserialise provided data element.
	 *
	 * This method should convert the provided structure into a custom data type compatible
	 * with the current container.
	 *
	 * This method is called by a public {@link UnserialiseObject() interface} which
	 * traverses an object and provides this method with all array or ArrayObject elements.
	 *
	 * The method expects to receive structures containing both the
	 * {@link kTAG_TYPE type} and {@link kTAG_DATA data} offsets, its duty is to parse
	 * custom data types in the {@link kTAG_TYPE kTAG_TYPE} offset and convert the data
	 * provided in the {@link kTAG_DATA kTAG_DATA} back into the original data type and
	 * replace the provided element with that value.
	 *
	 * The conversion is performed on the provided element itself.
	 *
	 * In this class we do not perform any conversions.
	 *
	 * @param reference			   &$theElement			Element to encode.
	 *
	 * @access public
	 */
	public function UnserialiseData( &$theElement )										   {}

		

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
	 * This method can be used to retrieve a reference to the native container member, this
	 * can be useful when the native {@link Container() container} is not an object passed
	 * by reference.
	 *
	 * @access protected
	 * @return mixed
	 */
	protected function &_Container()						{	return $this->mContainer;	}

		

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
	 * Derived classes must implement this method to actually store the provided object in
	 * the container, it expects three parameters:
	 *
	 * <ul>
	 *	<li><b>$theObject</b>: The object to be committed.
	 *	<li><b>$theIdentifier</b>: This parameter is expected to be the object's unique
	 *		identifier within the container.
	 *	<li><b>$theModifiers</b>: This parameter represents the commit operation options, by
	 *		default we assume it is a bitfield where the following values apply:
	 *	 <ul>
	 *		<li><i>{@link kFLAG_PERSIST_INSERT kFLAG_PERSIST_INSERT}</i>: The provided
	 *			object will be inserted in the container, it is assumed that no other
	 *			element in the container shares the same identifier, in that case the method
	 *			must raise an {@link kERROR_DUPLICATE exception}.
	 *		<li><i>{@link kFLAG_PERSIST_UPDATE kFLAG_PERSIST_UPDATE}</i>: The provided
	 *			object will replace the existing object. In this case the method expects
	 *			the container to have an entry with the same key as the provided identifier,
	 *			if this is not the case the method must raise an
	 *			{@link kERROR_NOT_FOUND exception}. With this option it is assumed that the
	 *			provided object's attributes will replace all the existing object's ones.
	 *		<li><i>{@link kFLAG_PERSIST_MODIFY kFLAG_PERSIST_MODIFY}</i>: The provided
	 *			object is assumed to contain a subset of an existing object's attributes,
	 *			these provided attributes will be appended or replace the existing ones.
	 *			In this case the method expects the container to have an entry with the
	 *			same key as the provided identifier, if this is not the case the method must
	 *			raise an {@link kERROR_NOT_FOUND exception}.
	 *		<li><i>{@link kFLAG_PERSIST_REPLACE kFLAG_PERSIST_REPLACE}</i>: The provided
	 *			object will be {@link kFLAG_PERSIST_INSERT inserted}, if the identifier
	 *			doesn't match any container elements, or it will
	 *			{@link kFLAG_PERSIST_UPDATE replace} the existing object. As with
	 *			{@link kFLAG_PERSIST_UPDATE update}, it is assumed that the provided
	 *			object's attributes will replace all the existing object's ones.
	 *	 </ul>
	 * </ul>
	 *
	 * The method should return the object's key within the container or raise an exception
	 * if the operation was not successful.
	 *
	 * @param reference			   &$theObject			Object to commit.
	 * @param mixed					$theIdentifier		Object identifier.
	 * @param bitfield				$theModifiers		Commit modifiers.
	 *
	 * @access protected
	 * @return mixed
	 */
	abstract protected function _Commit( &$theObject, $theIdentifier, $theModifiers );

	 
	/*===================================================================================
	 *	_Load																			*
	 *==================================================================================*/

	/**
	 * Load object.
	 *
	 * Derived classes must implement this method to perform the actual retrieval of objects
	 * from the container.
	 *
	 * <ul>
	 *	<li><b>$theIdentifier</b>: The key to the object in the container.
	 *	<li><b>$theModifiers</b>: Bitfield containing the operation options.
	 * </ul>
	 *
	 * The method should return the found object or <i>NULL</i> if not found.
	 *
	 * @param mixed					$theIdentifier		Object identifier.
	 * @param bitfield				$theModifiers		Load modifiers.
	 *
	 * @access protected
	 * @return mixed
	 */
	abstract protected function _Load( $theIdentifier, $theModifiers );

	 
	/*===================================================================================
	 *	_Delete																			*
	 *==================================================================================*/

	/**
	 * Load object.
	 *
	 * Derived classes must implement this method to perform the actual removal of objects
	 * from the container.
	 *
	 * <ul>
	 *	<li><b>$theIdentifier</b>: The key to the object in the container.
	 *	<li><b>$theModifiers</b>: Bitfield containing the operation options.
	 * </ul>
	 *
	 * The method should return the removed object or <i>NULL</i> if not found.
	 *
	 * @param mixed					$theIdentifier		Object identifier.
	 * @param bitfield				$theModifiers		Delete modifiers.
	 *
	 * @access protected
	 * @return mixed
	 */
	abstract protected function _Delete( $theIdentifier, $theModifiers );

	 

} // class CContainer.


?>
