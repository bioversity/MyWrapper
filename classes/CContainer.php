<?php

/**
 * <i>CContainer</i> class definition.
 *
 * This file contains the class definition of <b>CContainer</b> which represents the
 * ancestor of all object stores in this library.
 *
 *	@package	MyWrapper
 *	@subpackage	Persistence
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 07/03/2012
 */

/*=======================================================================================
 *																						*
 *									CContainer.php										*
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
 * This <i>abstract</i> class is the ancestor of all container classes in this library, it
 * implements the interface and workflow which all derived classes wrapping databases and
 * other data stores will implement.
 *
 * The public interface declares three main operations:
 *
 * <ul>
 *	<li><i>{@link Commit() Commit}</i>: This method will insert, replace or modify objects
 *		in the current container.
 *	<li><i>{@link Load() Load}</i>: This method will retrieve objects from the current
 *		container.
 *	<li><i>{@link Delete() Delete}</i>: This method will remove objects from the current
 *		container.
 * </ul>
 *
 * The class features a {@link Container() member} that holds the native data store.
 *
 *	@package	MyWrapper
 *	@subpackage	Persistence
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
	 * Derived classes should overload this method if a default value is possible; to check
	 * for specific container types they should rather overload the member accessor
	 * {@link Container() method}.
	 *
	 * @param mixed					$theContainer		Native object store.
	 *
	 * @access public
	 *
	 * @uses Container()
	 */
	public function __construct( $theContainer = NULL )
	{
		$this->Container( $theContainer );
		
	} // Constructor.

	 
	/*===================================================================================
	 *	__toString																		*
	 *==================================================================================*/

	/**
	 * Return container name.
	 *
	 * This method should return the current container's name.
	 *
	 * All derived concrete classes should implement this method, all containers must be
	 * able to return a name.
	 *
	 * @access public
	 * @return string
	 */
	abstract public function __toString();

		

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
	 *	<li><i>FALSE</i>: Delete the current value.
	 *	<li><i>other</i>: Set the value with the provided parameter.
	 * </ul>
	 *
	 * The second parameter is a boolean which if <i>TRUE</i> will return the <i>old</i>
	 * value when replacing containers; if <i>FALSE</i>, it will return the currently set
	 * value.
	 *
	 * In derived classes you should overload this method to check if the provided container
	 * is of the correct type, in this class we accept anything.
	 *
	 * @param mixed					$theValue			Persistent container or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses ManageMember()
	 */
	public function Container( $theValue = NULL, $getOld = FALSE )
	{
		return $this->ManageMember( $this->mContainer, $theValue, $getOld );		// ==>

	} // Container.

		

/*=======================================================================================
 *																						*
 *								PUBLIC ELEMENT INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Database																		*
	 *==================================================================================*/

	/**
	 * Return database.
	 *
	 * This method should return the current container's database, if this is not relevant,
	 * it should return <i>NULL</i>.
	 *
	 * @access public
	 * @return mixed
	 */
	abstract public function Database();

		

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
	 *	<li><b>$theObject</b>: The object or data to be committed.
	 *	<li><b>$theIdentifier</b>: This parameter is expected to be the object's unique
	 *		identifier within the container, it will be the {@link Load() access} key to the
	 *		object once committed. If the value is <i>NULL</i>, it means that it is the duty
	 *		of the current container to set it, this will generally be the case when
	 *		inserting objects; in all other cases the parameter is required.
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
	 *		<li><i>{@link kFLAG_STATE_ENCODED kFLAG_STATE_ENCODED}</i>: This option will
	 *			generally be taken from the first parameter if it is a
	 *			{@link CPersistentObject persistent} object: if set, it means that the
	 *			object may have elements derived from concrete instances of
	 *			{@link CDataType CDataType}, which means that these will have to be
	 *			{@link UnserialiseObject() encoded} into the native database types before
	 *			committing the data. This option will be set {@link _PrepareCommit() before}
	 *			{@link _Commit() committing}.
	 *	 </ul>
	 * </ul>
	 *
	 * The method will return the object's key within the container or raise an exception if
	 * the operation was not successful.
	 *
	 * The operation is performed by a protected interface whose workflow is as follows:
	 *
	 * <ul>
	 *	<li><i>{@link _PrepareCommit() _PrepareCommit}()</i>: This method can be used to
	 *		check the parameters and initialise the resources. It is here where we
	 *		{@link kFLAG_STATE_ENCODED eventually} {@link UnserialiseObject() unserialise}
	 *		the object.
	 *	<li><i>{@link _Commit() _Commit}()</i>: This method will perform the actual commit.
	 *	<li><i>{@link _FinishCommit() _FinishCommit}()</i>: This method can be used to perform
	 *		eventual post-flight adjustments. It is here where we
	 *		{@link kFLAG_STATE_ENCODED eventually}
	 *		{@link CDataType::SerialiseObject() serialise} the object.
	 *		the object.
	 * </ul>
	 *
	 * @param reference			   &$theObject			Object to commit.
	 * @param mixed					$theIdentifier		Object identifier.
	 * @param bitfield				$theModifiers		Commit modifiers.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses _PrepareCommit()
	 * @uses _Commit()
	 * @uses _FinishCommit()
	 */
	public function Commit( &$theObject,
							 $theIdentifier = NULL,
							 $theModifiers = kFLAG_PERSIST_REPLACE )
	{
		//
		// Prepare.
		//
		$this->_PrepareCommit( $theObject, $theIdentifier, $theModifiers );
		
		//
		// Store object.
		//
		$theIdentifier
			= $this->_Commit
				( $theObject, $theIdentifier, $theModifiers );
		
		//
		// Finish.
		//
		$this->_FinishCommit( $theObject, $theIdentifier, $theModifiers );
		
		return $theIdentifier;														// ==>

	} // Commit.

	 
	/*===================================================================================
	 *	Load																			*
	 *==================================================================================*/

	/**
	 * Load object.
	 *
	 * This method can be used to load an object from the current data store, it expects two
	 * parameters:
	 *
	 * <ul>
	 *	<li><b>$theIdentifier</b>: This parameter is expected to be the object's unique
	 *		identifier within the container, it will be the access key to the object.
	 *	<li><b>$theModifiers</b>: This parameter represents the load operation options, in
	 *		this class we only consider the {@link kFLAG_STATE_ENCODED kFLAG_STATE_ENCODED}
	 *		option: if set, the identifier will be {@link UnserialiseData() unserialised}
	 *		{@link _PrepareLoad() before} {@link _Load() searching} for the object.
	 * </ul>
	 *
	 * The method should return the found object, or <i>NULL</i> if not found.
	 *
	 * The actual operation is performed by a protected interface:
	 *
	 * <ul>
	 *	<li><i>{@link _PrepareLoad() _PrepareLoad}</i>: Normalise parameters and initialise
	 *		resources.
	 *	<li><i>{@link _Load() _Load}</i>: Find and load object.
	 *	<li><i>{@link _FinishLoad() _FinishLoad}</i>: Cleanup after the operation.
	 * </ul>
	 *
	 * @param mixed					$theIdentifier		Object identifier.
	 * @param bitfield				$theModifiers		Load modifiers.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses _PrepareLoad()
	 * @uses _Load()
	 * @uses _FinishLoad()
	 */
	public function Load( $theIdentifier, $theModifiers = kFLAG_DEFAULT )
	{
		//
		// Prepare.
		//
		$this->_PrepareLoad( $theIdentifier, $theModifiers  );
		
		//
		// Perform load.
		//
		$object = $this->_Load( $theIdentifier, $theModifiers  );
		
		//
		// Finish.
		//
		$this->_FinishLoad( $object, $theIdentifier, $theModifiers  );
		
		return $object;																// ==>

	} // Load.

	 
	/*===================================================================================
	 *	Delete																			*
	 *==================================================================================*/

	/**
	 * Delete object.
	 *
	 * This method can be used to remove an object from the current data store, it expects
	 * two parameters:
	 *
	 * <ul>
	 *	<li><b>$theIdentifier</b>: This parameter is expected to be the object's unique
	 *		identifier within the container, it will be the access key to the object.
	 *	<li><b>$theModifiers</b>: This parameter represents the operation options, in this
	 *		class we only consider the {@link kFLAG_STATE_ENCODED kFLAG_STATE_ENCODED}
	 *		option: if set, the identifier will be {@link UnserialiseData() unserialised}
	 *		{@link _PrepareDelete() before} {@link _Delete() removing} the object.
	 * </ul>
	 *
	 * The method should return the deleted object, or <i>NULL</i> if not found.
	 *
	 * The actual operation is performed by a protected interface:
	 *
	 * <ul>
	 *	<li><i>{@link _PrepareDelete() _PrepareDelete}</i>: Normalise parameters and
	 *		initialise resources.
	 *	<li><i>{@link _Delete() _Delete}</i>: Remove object from container.
	 *	<li><i>{@link _FinishDelete() _FinishDelete}</i>: Cleanup after the operation.
	 * </ul>
	 *
	 * @param mixed					$theIdentifier		Object identifier.
	 * @param bitfield				$theModifiers		Delete modifiers.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses _PrepareDelete()
	 * @uses _Delete()
	 * @uses _FinishDelete()
	 */
	public function Delete( $theIdentifier, $theModifiers = kFLAG_DEFAULT )
	{
		//
		// Prepare.
		//
		$this->_PrepareDelete( $theIdentifier, $theModifiers );
		
		//
		// Perform load.
		//
		$object = $this->_Delete( $theIdentifier, $theModifiers );
		
		//
		// Finish.
		//
		$this->_FinishDelete( $object, $theIdentifier, $theModifiers );
		
		return $object;																// ==>

	} // Delete.

		

/*=======================================================================================
 *																						*
 *								PUBLIC REFERENCE INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Reference																		*
	 *==================================================================================*/

	/**
	 * Convert an object to a reference.
	 *
	 * This method accepts an object derived from
	 * {@link CPersistentUnitObject CPersistentUnitObject} and returns a structure that can
	 * be used as a reference to that object and stored as a property.
	 *
	 * The method will return an array composed by the following offsets:
	 *
	 * <ul>
	 *	<li><i>{@link kTAG_ID_REFERENCE kTAG_ID_REFERENCE}</i>: The object identifier, if
	 *		the provided object does not have an {@link kTAG_ID_NATIVE identifier}, this
	 *		method will raise an exception.
	 *	<li><i>{@link kTAG_CONTAINER_REFERENCE kTAG_CONTAINER_REFERENCE}</i>: The container
	 *		name.
	 *	<li><i>{@link kTAG_DATABASE_REFERENCE kTAG_DATABASE_REFERENCE}</i>: The database
	 *		name.
	 *	<li><i>{@link kTAG_CLASS kTAG_CLASS}</i>: The object's class name.
	 * </ul>
	 *
	 * The method accepts two parameters:
	 *
	 * <ul>
	 *	<li><b>$theObject</b>: The object to be referenced, it must be derived from
	 *		{@link CPersistentUnitObject CPersistentUnitObject} or the method will raise an
	 *		exception.
	 *	<li><b>$theModifiers</b>: This bitfield determines what elements should be included
	 *		in the reference:
	 *	 <ul>
	 *		<li><i>{@link kFLAG_REFERENCE_IDENTIFIER kFLAG_REFERENCE_IDENTIFIER}</i>: The
	 *			object {@link kTAG_ID_NATIVE identifier} will be stored under the
	 *			{@link kTAG_ID_REFERENCE kTAG_ID_REFERENCE} offset. If the object does not
	 *			have this identifier, the method will raise an exception. This is the
	 *			default option.
	 *		<li><i>{@link kFLAG_REFERENCE_CONTAINER kFLAG_REFERENCE_CONTAINER}</i>: The
	 *			current container name will be stored under the
	 *			{@link kTAG_CONTAINER_REFERENCE kTAG_CONTAINER_REFERENCE} offset. If the
	 *			provided value is empty, the offset will not be set.
	 *		<li><i>{@link kFLAG_REFERENCE_DATABASE kFLAG_REFERENCE_DATABASE}</i>: The
	 *			current container's database name will be stored under the
	 *			{@link kTAG_DATABASE_REFERENCE kTAG_DATABASE_REFERENCE} offset. If the
	 *			current object's {@link Database() database} name is <i>NULL</i>, the
	 *			offset will not be set.
	 *		<li><i>{@link kFLAG_REFERENCE_CLASS kFLAG_REFERENCE_CLASS}</i>: The provided
	 *			object's class name will be stored under the {@link kTAG_CLASS kTAG_CLASS}
	 *			offset.
	 *	 </ul>
	 * </ul>
	 *
	 * @param CPersistentUnitObject	$theObject			Object to reference.
	 * @param bitfield				$theModifiers		Referencing options.
	 *
	 * @access public
	 * @return array
	 *
	 * @throws {@link CException CException}
	 *
	 * @uses __toString()
	 * @uses Database()
	 *
	 * @see kFLAG_REFERENCE_IDENTIFIER kFLAG_REFERENCE_CONTAINER
	 * @see kFLAG_REFERENCE_DATABASE kFLAG_REFERENCE_CLASS
	 */
	public function Reference( $theObject, $theModifiers = kFLAG_REFERENCE_IDENTIFIER )
	{
		//
		// Check provided object.
		//
		if( ! $theObject instanceof CPersistentUnitObject )
			throw new CException
				( "Invalid object",
				  kERROR_INVALID_PARAMETER,
				  kMESSAGE_TYPE_ERROR,
				  array( 'Object' => $theObject ) );							// !@! ==>
		
		//
		// Init reference.
		//
		$reference = Array();

		//
		// Load identifier.
		//
		if( $theModifiers & kFLAG_REFERENCE_IDENTIFIER )
		{
			if( $theObject->offsetExists( kTAG_ID_NATIVE ) )
				$reference[ kTAG_ID_REFERENCE ] = $theObject[ kTAG_ID_NATIVE ];
			else
				throw new CException
					( "Object does not have an identifier",
					  kERROR_OPTION_MISSING,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Object' => $theObject ) );						// !@! ==>
		}
		
		//
		// Load container information.
		//
		if( ($theModifiers & kFLAG_REFERENCE_CONTAINER)
		 && strlen( $tmp = $this->__toString() ) )
			$reference[ kTAG_CONTAINER_REFERENCE ] = $tmp;
		
		//
		// Load database information.
		//
		if( ($theModifiers & kFLAG_REFERENCE_DATABASE)
		 && (($tmp = $this->Database()) !== NULL) )
			$reference[ kTAG_DATABASE_REFERENCE ] = (string) $tmp;
		
		//
		// Load object class information.
		//
		if( $theModifiers & kFLAG_REFERENCE_CLASS )
			$reference[ kTAG_CLASS ] = get_class( $theObject );
		
		return $reference;															// ==>

	} // Reference.

		

/*=======================================================================================
 *																						*
 *								PUBLIC CONVERSION INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	UnserialiseObject																*
	 *==================================================================================*/

	/**
	 * Unserialise provided object.
	 *
	 * This method will convert concrete derived instances of {@link CDataType CDataType}
	 * into native data types suitable to be stored in containers.
	 *
	 * This method will scan the provided object or structure and pass all instances derived
	 * from {@link CDataType CDataType} to another public {@link UnserialiseData() method}
	 * that will convert these objects into native data types that are compatible with the
	 * specific container type.
	 *
	 * The method will scan the provided structure and select all elements which are arrays,
	 * ArrayObjects or objects derived from {@link CDataType CDataType}, these elements will
	 * be sent to the {@link UnserialiseData() UnserialiseData} method that will take care
	 * of converting these structures into native data types that are compatible with the
	 * specific container type.
	 *
	 * The method will perform the conversion directly into the provided reference and will
	 * use recursion to traverse the provided structures.
	 *
	 * Elements sent to the {@link UnserialiseData() conversion} method are selected as
	 * follows:
	 *
	 * <ul>
	 *	<li><i>{@link CDataType CDataType}</i>: All instances derived from this class are
	 *		sent to the {@link UnserialiseData() UnserialiseData} method.
	 *	<li><i>Array</i> or <i>ArrayObject</i>: If the structure is composed of exactly 2
	 *		offsets and these elements are {@link kTAG_TYPE kTAG_TYPE} and
	 *		{@link kTAG_DATA kTAG_DATA}, it will be sent to the
	 *		{@link UnserialiseData() UnserialiseData} method. If the above condition is not
	 *		satisfied, the structure will be sent recursively to this method.
	 * </ul>
	 *
	 * @param reference			   &$theObject			Object to encode.
	 *
	 * @access public
	 *
	 * @uses UnserialiseData()
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
				// Intercept standard data types.
				//
				if( $value instanceof CDataType )
				//
				// Note this ugly workflow:
				// I need to do this or else I get this
				// Notice: Indirect modification of overloaded element of MyClass
				// has no effect in /MySource.php
				// Which means that I cannot pass $theObject[ $key ] to UnserialiseData()
				// or I get the notice and the thing doesn't work.
				//
				{
					//
					// Copy data.
					//
					$save = $theObject[ $key ];
					
					//
					// Convert data.
					//
					$this->UnserialiseData( $save );
					
					//
					// Restore data.
					//
					$theObject[ $key ] = $save;
				}
					
				//
				// Intercept structs.
				//
				elseif( is_array( $value )
					 || ($value instanceof ArrayObject) )
				{
					//
					// Check required elements.
					//
					if( array_key_exists( kTAG_TYPE, (array) $value )
					 && array_key_exists( kTAG_DATA, (array) $value )
					 && (count( $value ) == 2) )
					//
					// Note this ugly workflow:
					// I need to do this or else I get this
					// Notice: Indirect modification of overloaded element of MyClass
					// has no effect in /MySource.php
					// Which means that I cannot pass $theObject[ $key ] to UnserialiseData()
					// or I get the notice and the thing doesn't work.
					//
					{
						//
						// Copy data.
						//
						$save = $theObject[ $key ];
						
						//
						// Convert data.
						//
						$this->UnserialiseData( $save );
						
						//
						// Restore data.
						//
						$theObject[ $key ] = $save;
					}
					
					//
					// Recurse.
					//
					else
					//
					// Note this ugly workflow:
					// I need to do this or else I get this
					// Notice: Indirect modification of overloaded element of MyClass
					// has no effect in /MySource.php
					// Which means that I cannot pass $theObject[ $key ] to UnserialiseData()
					// or I get the notice and the thing doesn't work.
					//
					{
						//
						// Copy data.
						//
						$save = $theObject[ $key ];
						
						//
						// Convert data.
						//
						$this->UnserialiseObject( $save );
						
						//
						// Restore data.
						//
						$theObject[ $key ] = $save;
					}
				
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
	 * traverses an object and provides this method with all elements that satisfy the
	 * following conditions:
	 *
	 * <ul>
	 *	<li><i>{@link CDataType CDataType}</i>: All instances derived from this class are
	 *		sent to this method.
	 *	<li><i>Array</i> or <i>ArrayObject</i>: If the structure is composed of exactly 2
	 *		offsets and these elements are {@link kTAG_TYPE kTAG_TYPE} and
	 *		{@link kTAG_DATA kTAG_DATA}, it will be sent to this method.
	 * </ul>
	 *
	 * Derived concrete classes will implement this method to intercept all structures that
	 * can be converted to a native data type compatible with the current container.
	 *
	 * The elements to be converted are provided by reference, which means that they have to
	 * be converted in place.
	 *
	 * This class is abstract, so we force derived classes to implement this method.
	 *
	 * @param reference			   &$theElement			Element to encode.
	 *
	 * @access public
	 */
	abstract public function UnserialiseData( &$theElement );

		

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
	 * the container, the method expects the same parameters as the public
	 * {@link Commit() interface}, except that in this method these are passed by reference.
	 *
	 * The method should return the object's key within the container or raise an exception
	 * if the operation was not successful.
	 *
	 * @param reference			   &$theObject			Object to commit.
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Commit modifiers.
	 *
	 * @access protected
	 * @return mixed
	 */
	abstract protected function _Commit( &$theObject, &$theIdentifier, &$theModifiers );

	 
	/*===================================================================================
	 *	_Load																			*
	 *==================================================================================*/

	/**
	 * Load object.
	 *
	 * Derived classes must implement this method to actually retrieve the provided object
	 * from the container, the method expects the same parameters as the public
	 * {@link Load() interface}, except that in this method these are passed by reference.
	 *
	 * The method should return the found object or <i>NULL</i> if not found.
	 *
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Load modifiers.
	 *
	 * @access protected
	 * @return mixed
	 */
	abstract protected function _Load( &$theIdentifier, &$theModifiers );

	 
	/*===================================================================================
	 *	_Delete																			*
	 *==================================================================================*/

	/**
	 * Load object.
	 *
	 * Derived classes must implement this method to actually remove the object from the
	 * container, the method expects the same parameters as the public
	 * {@link Delete() interface}, except that in this method these are passed by reference.
	 *
	 * The method should return the removed object or <i>NULL</i> if not found.
	 *
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Delete modifiers.
	 *
	 * @access protected
	 * @return mixed
	 */
	abstract protected function _Delete( &$theIdentifier, &$theModifiers );

		

/*=======================================================================================
 *																						*
 *								PROTECTED PERSISTENCE UTILITIES							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_PrepareLoad																	*
	 *==================================================================================*/

	/**
	 * Prepare before a {@link _Load() load}.
	 *
	 * The duty of this method is to ensure that the parameters provided to the
	 * {@link _Load() find} operation are valid.
	 *
	 * In this class we ensure that the identifier has been provided, that the current
	 * object has a native {@link Container() container} and we
	 * {@link UnserialiseData() unserialise} the provided identifier if
	 * {@link kFLAG_STATE_ENCODED needed}.
	 *
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Load modifiers.
	 *
	 * @access protected
	 *
	 * @throws {@link CException CException}
	 *
	 * @uses Container()
	 * @uses UnserialiseData()
	 *
	 * @see kFLAG_STATE_ENCODED
	 * @see kERROR_OPTION_MISSING kERROR_INVALID_STATE
	 */
	protected function _PrepareLoad( &$theIdentifier, &$theModifiers )
	{
		//
		// Check if identifier is there.
		//
		if( $theIdentifier === NULL )
			throw new CException
					( "Missing object identifier",
					  kERROR_OPTION_MISSING,
					  kMESSAGE_TYPE_ERROR );									// !@! ==>
		
		//
		// Check container.
		//
		if( $this->Container() === NULL )
			throw new CException
				( "Missing native container",
				  kERROR_INVALID_STATE,
				  kMESSAGE_TYPE_ERROR );										// !@! ==>
		
		//
		// Unserialise identifier.
		//
		if( $theModifiers & kFLAG_STATE_ENCODED )
			$this->UnserialiseData( $theIdentifier );
	
	} // _PrepareLoad.

	 
	/*===================================================================================
	 *	_PrepareCommit																	*
	 *==================================================================================*/

	/**
	 * Prepare before a {@link _Commit() commit}.
	 *
	 * This method will be called before the {@link _Commit() store} operation, its duty is
	 * to prepare the object and check the parameters, please refer to {@link Commit() this}
	 * documentation for a reference of the method's parameters. Note that in this method
	 * all three parameters are passed by reference.
	 *
	 * By default we perform the following checks:
	 *
	 * <ul>
	 *	<li>Ensure the identifier is provided if the operation is not an
	 *		{@link kFLAG_PERSIST_INSERT insert}.
	 *	<li>Ensure the method has the correct options.
	 *	<li>Ensure the current object has a container.
	 *	<li>Get the {@link CPersistentObject::_IsEncoded() encoded} status
	 *		{@link kFLAG_STATE_ENCODED flag} from the object.
	 *	<li>{@link UnserialiseObject() Unserialise} object and
	 *		{@link UnserialiseData() identifier} if necessary.
	 * </ul>
	 *
	 * In derived classes you should always call the parent method, remember to check this
	 * method to determine whether to implement your custom changes before or after calling
	 * this method.
	 *
	 * @param reference			   &$theObject			Object or data.
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Commit modifiers.
	 *
	 * @access protected
	 *
	 * @throws {@link CException CException}
	 *
	 * @uses Container()
	 * @uses UnserialiseObject()
	 * @uses UnserialiseData()
	 *
	 * @see kFLAG_STATE_ENCODED
	 * @see kERROR_OPTION_MISSING kERROR_INVALID_PARAMETER kERROR_INVALID_STATE
	 */
	protected function _PrepareCommit( &$theObject, &$theIdentifier, &$theModifiers )
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
				  array( 'Modifiers' => $theModifiers ) );						// !@! ==>
		
		//
		// Check options.
		//
		if( ! ($theModifiers & kFLAG_PERSIST_MASK) )
			throw new CException
				( "Invalid operation options",
				  kERROR_INVALID_PARAMETER,
				  kMESSAGE_TYPE_ERROR,
				  array( 'Modifiers' => $theModifiers ) );						// !@! ==>
		
		//
		// Check container.
		//
		if( $this->Container() === NULL )
			throw new CException
				( "Missing native container",
				  kERROR_INVALID_STATE,
				  kMESSAGE_TYPE_ERROR );										// !@! ==>
		
		//
		// Set encode option.
		//
		if( $theObject instanceof CStatusObject )
		{
			if( ($opt = $theObject->Status()) & kFLAG_STATE_ENCODED )
				$theModifiers |= kFLAG_STATE_ENCODED;
		}
		
		//
		// Unserialise object and identifier.
		//
		if( $theModifiers & kFLAG_STATE_ENCODED )
		{
			//
			// Process object.
			//
			$this->UnserialiseObject( $theObject );
			
			//
			// Process identifier.
			//
			$this->UnserialiseData( $theIdentifier );
		}
	
	} // _PrepareCommit.

	 
	/*===================================================================================
	 *	_PrepareDelete																	*
	 *==================================================================================*/

	/**
	 * Prepare before a {@link _Delete() delete}.
	 *
	 * The duty of this method is to ensure that the parameters provided to the
	 * {@link _Delete() delete} operation are valid.
	 *
	 * In this class we ensure that the identifier has been provided, that the current
	 * object has a native {@link Container() container} and we
	 * {@link UnserialiseData() unserialise} the provided identifier if
	 * {@link kFLAG_STATE_ENCODED needed}.
	 *
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Commit modifiers.
	 *
	 * @access protected
	 *
	 * @throws {@link CException CException}
	 *
	 * @uses Container()
	 *
	 * @see kERROR_INVALID_STATE
	 */
	protected function _PrepareDelete( &$theIdentifier, &$theModifiers )
	{
		//
		// Check if identifier is there.
		//
		if( $theIdentifier === NULL )
			throw new CException
					( "Missing object identifier",
					  kERROR_OPTION_MISSING,
					  kMESSAGE_TYPE_ERROR );									// !@! ==>
		
		//
		// Check container.
		//
		if( $this->Container() === NULL )
			throw new CException
				( "Missing native container",
				  kERROR_INVALID_STATE,
				  kMESSAGE_TYPE_ERROR );										// !@! ==>
		
		//
		// Unserialise identifier.
		//
		if( $theModifiers & kFLAG_STATE_ENCODED )
			$this->UnserialiseData( $theIdentifier );
	
	} // _PrepareDelete.

	 
	/*===================================================================================
	 *	_FinishLoad																		*
	 *==================================================================================*/

	/**
	 * Normalise after a {@link _Load() load}.
	 *
	 * This method will be called after the {@link _Load() load} operation, its duty is to
	 * clean up or normalise after the operation. The parameters are passed by reference.
	 *
	 * The method expects the found object or data in the first parameter and the original
	 * identifier in the second.
	 *
	 * In this class we {@link kFLAG_STATE_ENCODED eventually}
	 * {@link CDataType::SerialiseObject() serialise} the object and the
	 * {@link CDataType::SerialiseData() identifier}.
	 *
	 * @param reference			   &$theObject			Object reference.
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Commit modifiers.
	 *
	 * @access protected
	 *
	 * @uses CDataType::SerialiseObject()
	 * @uses CDataType::SerialiseData()
	 *
	 * @see kFLAG_STATE_ENCODED
	 */
	protected function _FinishLoad( &$theObject, &$theIdentifier, &$theModifiers )
	{
		//
		// Serialise object.
		//
		if( $theModifiers & kFLAG_STATE_ENCODED )
		{
			//
			// Serialise object.
			//
			CDataType::SerialiseObject( $theObject );
			
			//
			// Serialise identifier.
			//
			CDataType::SerialiseData( $theIdentifier );
			
			//
			// Copy status.
			//
			if( $theObject instanceof CPersistentObject )
				$theObject->Status( $theObject->Status() | kFLAG_STATE_ENCODED );
		
		} // Serialise option.
	
	} // _FinishCommit.

	 
	/*===================================================================================
	 *	_FinishCommit																	*
	 *==================================================================================*/

	/**
	 * Normalise after a {@link _Commit() store}.
	 *
	 * This method will be called after the {@link _Commit() store} operation, its duty is
	 * to clean up or restore the object after the operation please refer to
	 * {@link Commit() this} documentation for a reference of these parameters. Note that in
	 * this method all three parameters are passed by reference.
	 *
	 * In this class we {@link kFLAG_STATE_ENCODED eventually}
	 * {@link CDataType::SerialiseObject() serialise} the object.
	 *
	 * @param reference			   &$theObject			Object or data.
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Commit modifiers.
	 *
	 * @access protected
	 *
	 * @uses CDataType::SerialiseObject()
	 * @uses CDataType::SerialiseData()
	 *
	 * @see kFLAG_STATE_ENCODED
	 */
	protected function _FinishCommit( &$theObject, &$theIdentifier, &$theModifiers )
	{
		//
		// Serialise object.
		//
		if( $theModifiers & kFLAG_STATE_ENCODED )
		{
			//
			// Serialise object.
			//
			CDataType::SerialiseObject( $theObject );
	
			//
			// Serialise identifier.
			//
			CDataType::SerialiseData( $theIdentifier );
		}
	
	} // _FinishCommit.

	 
	/*===================================================================================
	 *	_FinishDelete																	*
	 *==================================================================================*/

	/**
	 * Normalise after a store.
	 *
	 * This method will be called after the {@link _Delete() delete} operation, its duty is
	 * to clean up or restore the object after the operation please refer to
	 * {@link Delete() this} documentation for a reference of these parameters. Note that in
	 * this method all two parameters are passed by reference.
	 *
	 * In this class we {@link kFLAG_STATE_ENCODED eventually}
	 * {@link CDataType::SerialiseObject() serialise} the object and the
	 * {@link CDataType::SerialiseData() identifier}.
	 *
	 * @param reference			   &$theObject			Object or data.
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Commit modifiers.
	 *
	 * @access protected
	 */
	protected function _FinishDelete( &$theObject, &$theIdentifier, &$theModifiers )
	{
		//
		// Serialise identifier.
		//
		if( $theModifiers & kFLAG_STATE_ENCODED )
		{
			//
			// Serialise object.
			//
			CDataType::SerialiseObject( $theObject );
	
			//
			// Serialise identifier.
			//
			CDataType::SerialiseData( $theIdentifier );
		}
	
	} // _FinishDelete.

	 

} // class CContainer.


?>
