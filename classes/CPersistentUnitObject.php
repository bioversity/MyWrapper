<?php

/**
 * <i>CPersistentUnitObject</i> class definition.
 *
 * This file contains the class definition of <b>CPersistentUnitObject</b> which extends its
 * {@link CPersistentObject ancestor} to implement an object that has a unique key, a field
 * storing its {@link kTAG_CLASS class} and a {@link kTAG_VERSION version}.
 *
 *	@package	MyWrapper
 *	@subpackage	Persistence
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 12/03/2012
 */

/*=======================================================================================
 *																						*
 *								CPersistentUnitObject.php								*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CPersistentObject.php" );

/**
 * Unit objects ancestor.
 *
 * A unit object is one that has a unique identifier, that is, it can be uniquely identified
 * among a collection of other objects.
 *
 * Instances derived from this class have a series of additional properties and methods that
 * govern how these can be stored and retrieved from containers.
 *
 * The class features two read-only public methods that return:
 *
 * <ul>
 *	<li><i>{@link _id() _id()}</i>: The object unique identifier as used by the native
 *		persistent container to uniquely identify the object.
 *	<li><i>{@link _index() _index()}</i>: The full index that uniquely identifies the
 *		object, expressed as a string.
 * </ul>
 *
 * Although both methods seem to return the same information, they are different, in the
 * sense that the {@link _index() _index} method should return the property or properties
 * that constitute the object's unique identifier concatenated in a single string. This may
 * be too long to use as an index, so {@link _id() _id()} should return the actual value
 * used as the key, which may be the hashed {@link _index() _index} value. In other words,
 * {@link _index() _index} is the human readable version of {@link _id() _id}.
 *
 * When the object is {@link Commit committed} for the first time, the value of the
 * {@link _id() _id} method will be set in the {@link kTAG_LID kTAG_LID} offset
 * which represents the object ID. This offset should never be changed and represents the
 * persistent identifier of the object.
 *
 * The class implements the {@link __toString() __toString} method to return the value of
 * the object's {@link _index() index} by default.
 *
 * This object also introduces the concept of object reference, that is, a structure that
 * can be used to refer to other objects. The class implements a series of protected methods
 * that derived classes can use to implement properties that point to other objects:
 *
 * <ul>
 *	<li><i>{@link _ParseReferences() _ParseReferences}</i>: When adding object references to
 *		properties one is also allowed to add the actual instance, at
 *		{@link Commit() commit} time, these objects must also be committed before committing
 *		the object that references them: this method will handle this.
 *	<li><i>{@link _ManageObjectList() _ManageObjectList}</i>: This method can be used as a
 *		base for handling properties that consist of object references lists. It handles a
 *		list of scalar object reference elements or a list of predicate/object pairs.
 *	<li><i>{@link _CheckRelationObject() _CheckRelationObject}</i>: This method can be used
 *		to normalise parameters that expect object references, these can be overloaded by
 *		derived classes to implement a custom framework.
 *	<li><i>{@link _CheckRelationPredicate() _CheckRelationPredicate}</i>: This method can be
 *		used to normalise parameters that expect predicate object references, these can be
 *		overloaded by derived classes to implement a custom framework.
 * </ul>
 *
 * The above protected interface is not used in this class, but it is made available to
 * derived classes in order to handle objects derived from this class.
 *
 * Objects derived from this class also hold, by default, their class name in an
 * {@link kTAG_CLASS offset}, this is used to {@link NewObject() instantiate} objects of
 * thecorrect class when retrieving data from a container.
 *
 * This class also features a {@link kTAG_VERSION version} which is an integer,
 * incremented each time the object is {@link Commit() committed}: this is  useful to
 * implement a concurrency control mechanism.
 *
 * Starting from this class we only handle {@link CContainer CContainer} derived instances
 * as containers, other container types will not be supported. This is because
 * {@link CContainer CContainer} derived instances have an
 * {@link CDataType::SerialiseObject() serialise} and
 * {@link CMongoDataWrapper::UnserialiseObject() unserialise} interface for handling special
 * data types.
 *
 * The specifics of this are managed by the {@link CContainer CContainer} derived classes,
 * so when planning your objects think in advance in what containers you plan to store them.
 *
 * Finally, this class features a series of static methods;
 *
 * <ul>
 *	<li><i>{@link NewObject() NewObject}</i>: This method can be used to instantiate a class
 *		from a mixed class data store, it accepts the same parameters as the
 *		{@link __construct() constructor}, but will return an instance of the correct
 *		{@link kTAG_CLASS class}.
 *	<li><i>{@link Reference() Reference}</i>: This method will convert an instance derived
 *		from this class into a standard object reference structure in which it is possible
 *		to indicate both the {@link kTAG_REFERENCE_CONTAINER container} and the
 *		{@link kTAG_REFERENCE_DATABASE database}.
 * </ul>
 *
 * We declare the class abstract because the object must be {@link _IsInited() inited} to be
 * {@link Commit() committed} and the {@link _index() index} must be explicitly implemented.
 *
 *	@package	MyWrapper
 *	@subpackage	Persistence
 */
abstract class CPersistentUnitObject extends CPersistentObject
{
		

/*=======================================================================================
 *																						*
 *											MAGIC										*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	__toString																		*
	 *==================================================================================*/

	/**
	 * Return object identifier.
	 *
	 * In this class we return the object string {@link _index() identifier}.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses _index()
	 */
	public function __toString()					{	return (string) $this->_index();	}

		

/*=======================================================================================
 *																						*
 *								PUBLIC STATUS INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Persistent																		*
	 *==================================================================================*/

	/**
	 * Check whether object is persistent.
	 *
	 * This method will return <i>TRUE</i> if the object has the {@link kTAG_LID local}
	 * identifier, this would mean that the object has either been {@link Commit() saved}
	 * or that the object was {@link _Load() loaded} from a {@link CContainer container}.
	 *
	 * @access public
	 * @return boolean
	 */
	public function Persistent()				{	return $this->offsetExists( kTAG_LID );	}

		

/*=======================================================================================
 *																						*
 *								STATIC REFERENCE INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	NewObject																		*
	 *==================================================================================*/

	/**
	 * Instantiate object.
	 *
	 * This method can be used to instantiate an object from a mixed class data store, it
	 * expects the container to be a {@link CContainer CContainer} derived instance and the
	 * identifier to be the {@link _id() unique} {@link kTAG_LID identifier} of the
	 * object.
	 *
	 * This method takes advantage of the {@link Commit() stored}
	 * {@link kTAG_CLASS class} name.
	 *
	 * The method will return an object, if the identifier matches and the object has its
	 * {@link kTAG_CLASS class} name; an array if the identifier matches, but the data
	 * doesn't contain a @link kTAG_CLASS class} name reference; <i>NULL</i> if the
	 * identifier didn't match.
	 *
	 * @param mixed					$theContainer		Persistent container.
	 * @param mixed					$theIdentifier		Object identifier.
	 * @param bitfield				$theModifiers		Create modifiers.
	 *
	 * @static
	 * @return mixed
	 */
	static function NewObject( $theContainer,
							   $theIdentifier,
							   $theModifiers = kFLAG_DEFAULT )
	{
		//
		// Check container.
		//
		if( ! $theContainer instanceof CContainer )
			throw new CException
					( "Unsupported container type",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Container' => $theContainer ) );					// !@! ==>
		
		//
		// Check identifier.
		//
		if( $theIdentifier === NULL )
			throw new CException
					( "Missing object identifier",
					  kERROR_OPTION_MISSING,
					  kMESSAGE_TYPE_ERROR );									// !@! ==>
		
		//
		// Load object.
		//
		$data = $theContainer->Load( $theIdentifier, $theModifiers );
		if( $data !== NULL )
		{
			//
			// Check class.
			//
			if( array_key_exists( kTAG_CLASS, $data ) )
			{
				//
				// Get class name.
				//
				$class = $data[ kTAG_CLASS ];
				
				//
				// Instantiate object.
				//
				$object = new $class( $data, NULL, $theModifiers );
				
				//
				// Mark as committed.
				//
				$object->_IsCommitted( TRUE );
				
				return $object;														// ==>
			
			} // Has class name.
		
		} // Found object.
		
		return $data;																// =>
		
	} // NewObject.

	 
	/*===================================================================================
	 *	Reference																		*
	 *==================================================================================*/

	/**
	 * Convert an object to a reference.
	 *
	 * This method accepts an object derived from this class and returns a structure that
	 * can be used as a reference to that object.
	 *
	 * The method will return an array composed by the following offsets:
	 *
	 * <ul>
	 *	<li><i>{@link kTAG_REFERENCE_ID kTAG_REFERENCE_ID}</i>: The object identifier,
	 *		if the provided object does not have an {@link kTAG_LID identifier}, this
	 *		method will search for a {@link kTAG_REFERENCE_ID reference}.
	 *	<li><i>{@link kTAG_REFERENCE_CONTAINER kTAG_REFERENCE_CONTAINER}</i>: The
	 *		container name, if the provided object is a reference.
	 *	<li><i>{@link kTAG_REFERENCE_DATABASE kTAG_REFERENCE_DATABASE}</i>: The
	 *		database name, if the provided object is a reference.
	 *	<li><i>{@link kTAG_CLASS kTAG_CLASS}</i>: If the provided object is derived
	 *		from this class, the object's class.
	 * </ul>
	 *
	 * The method accepts two parameters:
	 *
	 * <ul>
	 *	<li><b>$theObject</b>: The object to be referenced, or a structure containing a
	 *		reference.
	 *	<li><b>$theModifiers</b>: This bitfield determines what elements should be included
	 *		in the reference:
	 *	 <ul>
	 *		<li><i>{@link kFLAG_REFERENCE_IDENTIFIER kFLAG_REFERENCE_IDENTIFIER}</i>: The
	 *			object {@link kTAG_LID identifier} will be stored under the
	 *			{@link kTAG_REFERENCE_ID kTAG_REFERENCE_ID} offset. If the object does
	 *			not have this identifier, the method will raise an exception. This is the
	 *			default option.
	 *		<li><i>{@link kFLAG_REFERENCE_CONTAINER kFLAG_REFERENCE_CONTAINER}</i>: The
	 *			container name if available.
	 *		<li><i>{@link kFLAG_REFERENCE_DATABASE kFLAG_REFERENCE_DATABASE}</i>: The
	 *			container's database name if available.
	 *		<li><i>{@link kFLAG_REFERENCE_CLASS kFLAG_REFERENCE_CLASS}</i>: The provided
	 *			object's class name if derived from this class.
	 *	 </ul>
	 * </ul>
	 *
	 * If the provided object cannot be resolved, the method will return <i>NULL</i>.
	 *
	 * @param mixed					$theObject			Object to reference.
	 * @param bitfield				$theModifiers		Referencing options.
	 *
	 * @static
	 * @return mixed
	 *
	 * @see kFLAG_REFERENCE_IDENTIFIER kFLAG_REFERENCE_CONTAINER
	 * @see kFLAG_REFERENCE_DATABASE kFLAG_REFERENCE_CLASS
	 * @see kTAG_REFERENCE_ID kTAG_REFERENCE_CONTAINER kTAG_REFERENCE_DATABASE
	 * @see kTAG_CLASS kTAG_LID
	 */
	static function Reference( $theObject, $theModifiers = kFLAG_REFERENCE_IDENTIFIER )
	{
		//
		// Check object.
		//
		if( is_array( $theObject )
		 || ($theObject instanceof ArrayObject) )
		{
			//
			// Init local storage.
			//
			$reference = Array();

			//
			// Handle identifier.
			//
			if( $theModifiers & kFLAG_REFERENCE_IDENTIFIER )
			{
				//
				// Try identifier.
				//
				if( ( is_array( $theObject )
				   && array_key_exists( kTAG_LID, $theObject ) )
				 || ( ($theObject instanceof ArrayObject)
				   && $theObject->offsetExists( kTAG_LID ) ) )
					$reference[ kTAG_REFERENCE_ID ]
						= $theObject[ kTAG_LID ];
	
				//
				// Try reference.
				//
				elseif( ( is_array( $theObject )
					   && array_key_exists( kTAG_REFERENCE_ID, $theObject ) )
					 || ( ($theObject instanceof ArrayObject)
					   && $theObject->offsetExists( kTAG_REFERENCE_ID ) ) )
					$reference[ kTAG_REFERENCE_ID ]
						= $theObject[ kTAG_REFERENCE_ID ];
			
			} // Handle identifier.

			//
			// Handle container.
			//
			if( $theModifiers & kFLAG_REFERENCE_CONTAINER )
			{
				if( ( is_array( $theObject )
				   && array_key_exists( kTAG_REFERENCE_CONTAINER, $theObject ) )
				 || ( ($theObject instanceof ArrayObject)
				   && $theObject->offsetExists( kTAG_REFERENCE_CONTAINER ) ) )
					$reference[ kTAG_REFERENCE_CONTAINER ]
						= $theObject[ kTAG_REFERENCE_CONTAINER ];
			
			} // Handle container.

			//
			// Handle database.
			//
			if( $theModifiers & kFLAG_REFERENCE_DATABASE )
			{
				if( ( is_array( $theObject )
				   && array_key_exists( kTAG_REFERENCE_DATABASE, $theObject ) )
				 || ( ($theObject instanceof ArrayObject)
				   && $theObject->offsetExists( kTAG_REFERENCE_DATABASE ) ) )
					$reference[ kTAG_REFERENCE_DATABASE ]
						= $theObject[ kTAG_REFERENCE_DATABASE ];
			
			} // Handle database.

			//
			// Handle class.
			//
			if( $theModifiers & kFLAG_REFERENCE_CLASS )
			{
				//
				// Try object.
				//
				if( ( is_array( $theObject )
				   && array_key_exists( kTAG_CLASS, $theObject ) )
				 || ( ($theObject instanceof ArrayObject)
				   && $theObject->offsetExists( kTAG_CLASS ) ) )
					$reference[ kTAG_CLASS ]
						= $theObject[ kTAG_CLASS ];
	
				//
				// Try reference.
				//
				elseif( $theObject instanceof self )
					$reference[ kTAG_CLASS ]
						= get_class( $theObject );
			
			} // Handle class.
			
			return ( count( $reference ) )
				 ? $reference														// ==>
				 : NULL;															// ==>

		} // Is a structure.
		
		return NULL;																// ==>
		
	} // Reference.

	 
	/*===================================================================================
	 *	HashIndex																		*
	 *==================================================================================*/

	/**
	 * Hash index.
	 *
	 * This method can be used to format an identifier provided as a string, it will be
	 * used by the {@link _id() _id} method to format the result of the
	 * {@link _index() _index} method. One can consider this as the index hashing method for
	 * all derived classes.
	 *
	 * @param string				$theValue			Value to hash.
	 *
	 * @static
	 * @return string
	 */
	static function HashIndex( $theValue )
	{
		return new CDataTypeBinary( md5( $theValue, TRUE ) );						// ==>
//		return $theValue;															// ==>
	
	} // HashIndex.

		

/*=======================================================================================
 *																						*
 *							PROTECTED IDENTIFICATION INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_id																				*
	 *==================================================================================*/

	/**
	 * Return the object's unique identifier.
	 *
	 * This method can be used to return a value that represents the object's unique
	 * native identifier. The method should use the value returned by the
	 * {@link _index() _index} method and hash it if necessary.
	 *
	 * This method will be called {@link _PrepareCommit() before} {@link Commit() committing}
	 * the object to fill its unique identifier {@link kTAG_LID offset}. 
	 *
	 * If this method returns <i>NULL</i>, it is assumed that it will be the
	 * {@link CContainer container} that will provide a default unique value.
	 *
	 * In this class we return the {@link HashIndex() hashed} value of
	 * {@link _index() _index}.
	 *
	 * @access protected
	 * @return mixed
	 */
	protected function _id()				{	return $this->HashIndex( $this->_index() );	}

	 
	/*===================================================================================
	 *	_index																			*
	 *==================================================================================*/

	/**
	 * Return the object's unique index.
	 *
	 * This method can be used to return a string value that represents the object's unique
	 * identifier. This value should generally be extracted from the object's properties.
	 *
	 * In general this value will be used by the {@link _id() _id} method to form the
	 * object's unique {@link kTAG_LID identifier}, maybe hashed to make the index
	 * smaller.
	 *
	 * In this class we require derived classes to implement the method.
	 *
	 * @access protected
	 * @return string
	 */
	abstract protected function _index();

		

/*=======================================================================================
 *																						*
 *								PROTECTED PERSISTENCE INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_Commit																			*
	 *==================================================================================*/

	/**
	 * Store object in container.
	 *
	 * We overload this method to {@link _GetTags() collect} and {@link _SetTags() set} the
	 * object tags.
	 *
	 * @param reference			   &$theContainer		Object container.
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Commit modifiers.
	 *
	 * @access protected
	 * @return mixed
	 */
	protected function _Commit( &$theContainer, &$theIdentifier, &$theModifiers )
	{
		//
		// Set tags.
		//
		$this->_SetTags();
		
		return parent::_Commit( $theContainer, $theIdentifier, $theModifiers );		// ==>
	
	} // _Commit.

		

/*=======================================================================================
 *																						*
 *								PROTECTED PERSISTENCE UTILITIES							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_PrepareLoad																	*
	 *==================================================================================*/

	/**
	 * Normalise parameters of a find.
	 *
	 * We {@link CPersistentObject::_PrepareLoad() overload} this method to handle
	 * identifiers provided as structures containing either the {@link kTAG_LID native}
	 * identifier or an object {@link kTAG_REFERENCE_ID reference}.
	 *
	 * @param reference			   &$theContainer		Object container.
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Create modifiers.
	 *
	 * @access protected
	 *
	 * @throws {@link CException CException}
	 *
	 * @see kERROR_OPTION_MISSING kERROR_UNSUPPORTED
	 */
	protected function _PrepareLoad( &$theContainer, &$theIdentifier, &$theModifiers )
	{
		//
		// Handle identifier structures.
		//
		if( is_array( $theIdentifier )
		 || ($theIdentifier instanceof ArrayObject) )
		{
			//
			// Try object identifier.
			//
			if( array_key_exists( kTAG_LID, (array) $theIdentifier ) )
				$theIdentifier = $theIdentifier[ kTAG_LID ];
			
			//
			// Try object reference.
			//
			elseif( array_key_exists( kTAG_REFERENCE_ID, (array) $theIdentifier ) )
				$theIdentifier = $theIdentifier[ kTAG_REFERENCE_ID ];
		}

		//
		// Call parent method.
		//
		parent::_PrepareLoad( $theContainer, $theIdentifier, $theModifiers );
		
		//
		// Check if container is supported.
		//
		if( ! $theContainer instanceof CContainer )
			throw new CException
					( "Unsupported container type",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Container' => $theContainer ) );					// !@! ==>
	
	} // _PrepareLoad.

	 
	/*===================================================================================
	 *	_PrepareCommit																	*
	 *==================================================================================*/

	/**
	 * Normalise before a store.
	 *
	 * We {@link CPersistentObject::_PrepareCommit() overload} this method to perform the
	 * following steps:
	 *
	 * <ul>
	 *	<li><i>Identifier as structure</i>: We handle identifiers provided as object
	 *		structures or references by checking the {@link kTAG_LID native} identifier
	 *		or the object {@link kTAG_REFERENCE_ID reference}.
	 *	<li><i>Set identifier</i>: If the current object has already an
	 *		{@link kTAG_LID identifier} and an identifier was not provided we set it, if
	 *		this is not the case we set it via the {@link _id() _id} method.
	 *	<li><i>Call parent method</i>: We then call the parent method, this is to ensure all
	 *		required data is provided.
	 *	<li><i>{@link kTAG_CLASS kTAG_CLASS}</i>: We set this offset with the current
	 *		object's class name. Note that we overwrite old values.
	 *	<li><i>{@link kTAG_VERSION kTAG_VERSION}</i>: If not set, we initialise this
	 *		value to zero, if already set, we increment it.
	 * </ul>
	 *
	 * identifiers
	 * provided as structures containing either the {@link kTAG_LID native} identifier
	 * or an object {@link kTAG_REFERENCE_ID reference}.
	 *
	 * The duty of this method is to ensure that the parameters provided to the
	 * {@link _Commit() store} operation are correct.
	 *
	 * In this class we ensure that the container is a ArrayObject or a
	 * {@link CContainer CContainer} derived instance and we ensure the identifier is filled
	 * in the case it was not provided:
	 *
	 * <ul>
	 *	<li><i>Get {@link kTAG_LID kTAG_LID}</i>: If the object features the
	 *		{@link kTAG_LID kTAG_LID} offset, it will be preferred. This is
	 *		necessary, because we don't want the object identifier to change in time.
	 *	<li><i>Use the {@link _id() :id} method</i>: We use the value returned by the
	 *		{@link _id() _id} protected method, this will be the case when
	 *		{@link Commit() saving} new objects.
	 * </ul>
	 *
	 * We also handle here the other two default offsets:
	 *
	 * <ul>
	 *	<li><i>{@link kTAG_CLASS kTAG_CLASS}</i>: We set this offset with the current
	 *		object's name. Note that we overwrite old values.
	 *	<li><i>{@link kTAG_VERSION kTAG_VERSION}</i>: If not set, we initialise this
	 *		value to zero, if already set, we increment it.
	 * </ul>
	 *
	 * @param reference			   &$theContainer		Object container.
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Commit modifiers.
	 *
	 * @access protected
	 *
	 * @throws {@link CException CException}
	 *
	 * @see kERROR_OPTION_MISSING kERROR_UNSUPPORTED
	 */
	protected function _PrepareCommit( &$theContainer, &$theIdentifier, &$theModifiers )
	{
		//
		// Handle identifier structures.
		//
		if( is_array( $theIdentifier )
		 || ($theIdentifier instanceof ArrayObject) )
		{
			//
			// Try object identifier.
			//
			if( array_key_exists( kTAG_LID, (array) $theIdentifier ) )
				$theIdentifier = $theIdentifier[ kTAG_LID ];
			
			//
			// Try object reference.
			//
			elseif( array_key_exists( kTAG_REFERENCE_ID, (array) $theIdentifier ) )
				$theIdentifier = $theIdentifier[ kTAG_REFERENCE_ID ];
		}
		
		//
		// Ensure identifier.
		//
		if( $theIdentifier === NULL )
		{
			//
			// Check native identifier.
			//
			if( $this->offsetExists( kTAG_LID ) )
				$theIdentifier = $this->offsetGet( kTAG_LID );
			
			//
			// Check identifier value.
			//
			else
				$theIdentifier = $this->_id();
		}

		//
		// Call parent method.
		//
		parent::_PrepareCommit( $theContainer, $theIdentifier, $theModifiers );
		
		//
		// Check if container is supported.
		//
		if( ! $theContainer instanceof CContainer )
			throw new CException
					( "Unsupported container type",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Container' => $theContainer ) );					// !@! ==>
		
		//
		// Set class.
		//
		$this->offsetSet( kTAG_CLASS, get_class( $this ) );
		
		//
		// Set version.
		//
		$this->offsetSet( kTAG_VERSION, ( $this->offsetExists( kTAG_VERSION ) )
										 ? ($this->offsetGet( kTAG_VERSION ) + 1)
										 : 0 );
	
	} // _PrepareCommit.

		

/*=======================================================================================
 *																						*
 *								PROTECTED REFERENCE UTILITIES							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_ParseReferences																*
	 *==================================================================================*/

	/**
	 * Handle references.
	 *
	 * This method will parse the provided offset and convert all instances derived from
	 * this class to object references according to a series of rules.
	 *
	 * Object references may have two forms:
	 *
	 * <ul>
	 *	<li><i>Scalar</i>: A scalar value represents the object
	 *		{@link kTAG_LID identifier}.
	 *	<li><i>Object reference structure</i>: This form is a structure holding the
	 *		following elements:
	 *	 <ul>
	 *		<li><i>{@link kTAG_REFERENCE_ID kTAG_REFERENCE_ID}</i>: This offset holds
	 *			the object's {@link kTAG_LID identifier}.
	 *		<li><i>{@link kTAG_REFERENCE_CONTAINER kTAG_REFERENCE_CONTAINER}</i>: This
	 *			offset holds the container name in which the object resides.
	 *		<li><i>{@link kTAG_REFERENCE_DATABASE kTAG_REFERENCE_DATABASE}</i>: This
	 *			offset holds the database name in which the object resides.
	 *		<li><i>{@link kTAG_CLASS kTAG_CLASS}</i>: This offset holds the object's
	 *			class name.
	 *	 </ul>
	 *		Such structures should not have any other allowed offset.
	 * </ul>
	 *
	 * Object references are stored in offsets with the following forms:
	 *
	 * <ul>
	 *	<li><i>Scalar</i>: The offset holds the object reference as a scalar element.
	 *	<li><i>Typed</i>: A typed object reference consists of a structure in which the
	 *		{@link kTAG_DATA kTAG_DATA} offset holds the object reference and an optional
	 *		{@link kTAG_KIND kTAG_KIND} offset holds the relation predicate, which may also
	 *		be in the form of an object reference.
	 *	<li><i>List</i>: A list of references whose elements may be a combination of the
	 *		previous two formats.
	 * </ul>
	 *
	 * This method will pass the provided offset value to a
	 * {@link _CommitReferences() method} that will take care of parsing the contents and
	 * {@link _CommitReference() committing} all instances derived from this class into
	 * object references according to the provided modifier flags.
	 *
	 * The parameters to this method are:
	 *
	 * <ul>
	 *	<li><b>$theOffset</b>: The current object's offset that holds the reference or
	 *		references.
	 *	<li><b>$theContainer</b>: The container that is about to receive the current object,
	 *		it must also be the container in which to find the references and must be
	 *		derived from {@link CContainer CContainer}.
	 *	<li><b>$theModifiers</b>: A bitfield indicating which elements should be included in
	 *		the {@link CContainer::Reference() reference}:
	 *	 <ul>
	 *		<li><i>{@link kFLAG_REFERENCE_IDENTIFIER kFLAG_REFERENCE_IDENTIFIER}</i>: The
	 *			object {@link kTAG_LID identifier} will be stored under the
	 *			{@link kTAG_REFERENCE_ID kTAG_REFERENCE_ID} offset. This option is
	 *			enforced.
	 *		<li><i>{@link kFLAG_REFERENCE_CONTAINER kFLAG_REFERENCE_CONTAINER}</i>: The
	 *			provided container name will be stored under the
	 *			{@link kTAG_REFERENCE_CONTAINER kTAG_REFERENCE_CONTAINER} offset. If
	 *			the provided value is empty, the offset will not be set.
	 *		<li><i>{@link kFLAG_REFERENCE_DATABASE kFLAG_REFERENCE_DATABASE}</i>: The
	 *			provided container's database name will be stored under the
	 *			{@link kTAG_REFERENCE_DATABASE kTAG_REFERENCE_DATABASE} offset. If the
	 *			current object's {@link Database() database} name is <i>NULL</i>, the
	 *			offset will not be set.
	 *		<li><i>{@link kFLAG_REFERENCE_CLASS kFLAG_REFERENCE_CLASS}</i>: The element
	 *			object's class name will be stored under the
	 *			{@link kTAG_CLASS kTAG_CLASS} offset.
	 *	 </ul>
	 *		If none of the above flags are set, it means that object references are
	 *		expressed directly as the value of the {@link kTAG_LID identifier}, and that
	 *		{@link kTAG_REFERENCE_CONTAINER container} and
	 *		{@link kTAG_REFERENCE_DATABASE database} are implicit.
	 * </ul>
	 *
	 * @param string				$theOffset			Reference list offset.
	 * @param CContainer			$theContainer		Object container.
	 * @param bitfield				$theModifiers		Referencing options.
	 *
	 * @access protected
	 *
	 * @uses _CommitReference()
	 */
	protected function _ParseReferences( $theOffset,
										  $theContainer,
										  $theModifiers = kFLAG_DEFAULT )
	{
		//
		// Check container.
		//
		if( ! $theContainer instanceof CContainer )
			throw new CException
					( "Unsupported container type",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Container' => $theContainer ) );					// !@! ==>
		
		//
		// Load offset value.
		//
		$reference = $this->offsetGet( $theOffset );
		
		//
		// Parse value.
		//
		if( $this->_CommitReferences( $reference, $theContainer, $theModifiers ) )
			$this->offsetSet( $theOffset, $reference );
		
	} // _ParseReferences.

	 
	/*===================================================================================
	 *	_CommitReferences																*
	 *==================================================================================*/

	/**
	 * Commit references.
	 *
	 * This method will parse the provided value looking for object references, if such
	 * references are expressed as instances derived from this class, it will will
	 * {@link Commit() commit} these instances and convert them to object references.
	 *
	 * The method will first check if the provided reference is a scalar, then it will
	 * check if it is a predicate/object par and finally it will check if it is a list of
	 * references.
	 *
	 * The parameters to this method are:
	 *
	 * <ul>
	 *	<li><b>$theReference</b>: The reference to be parsed, the conversion will replace
	 *		the provided parameter.
	 *	<li><b>$theContainer</b>: The container in which the referenced object(s) resides,
	 *		please refer to the documentation of
	 *		{@link _ParseReferences() _ParseReferences} for more information.
	 *	<li><b>$theModifiers</b>: A bitfield indicating which elements of the
	 *		{@link CContainer::Reference() reference} should be included, please refer to
	 *		the documentation of {@link _ParseReferences() _ParseReferences} for more
	 *		information.
	 *	<li><b>$doRecurse</b>: This is a private parameter that you should leave untouched, it
	 *		it determines whether or not to recurse this method: it starts with a value of
	 *		2, and at each recursion the value decreases, when it reaches zero, structures
	 *		will no more be considered.
	 * </ul>
	 *
	 * The method follows this set of rules:
	 *
	 * <ul>
	 *	<li><i>Handle scalars</i>: A scalar element may be an instance derived from this
	 *		class, an instance derived from CDataType, or anything that is not an array or
	 *		an ArrayObject. If the scalar is an instance of this class, we
	 *		{@link Commit() commit} the instance and convert it to an object reference.
	 *	<li><i>Handle structures</i>: Once we have determined it is not a scalar, we check
	 *		if it is either a predicate/object pair, or if it is a list of references; in
	 *		the both cases the elements will be passed recursively to this method.
	 * </ul>
	 *
	 * Note that when we {@link Commit() commit} referenced objects we use
	 * {@link kFLAG_PERSIST_REPLACE kFLAG_PERSIST_REPLACE} as the commit type.
	 *
	 * The method will return <i>TRUE</i> is a conversion occurred and <i>FALSE</i> if not.
	 *
	 * @param reference			   &$theReference		Reference.
	 * @param CContainer			$theContainer		Object container.
	 * @param bitfield				$theModifiers		Reference options.
	 * @param integer				$doRecurse			Recurse level.
	 *
	 * @access protected
	 *
	 * @uses _CommitReference()
	 */
	protected function _CommitReferences( &$theReference,
										  $theContainer,
										  $theModifiers,
										  $doRecurse = 2 )
	{
		//
		// Init local storage.
		//
		$done = FALSE;
		
		//
		// Handle instances of this class.
		//
		if( $theReference instanceof self )
		{
			//
			// Check for recursion.
			//
			if( $this->_index() == $theReference->_index() )
				throw new CException( "Recursive reference",
									  kERROR_INVALID_STATE,
									  kMESSAGE_TYPE_ERROR,
									  array( 'Reference' => $theReference ) );	// !@! ==>

			//
			// Set commit modifiers.
			//
			$modifiers = kFLAG_PERSIST_REPLACE | ($theModifiers & kFLAG_STATE_ENCODED);
			
			//
			// Commit object.
			//
			$theReference->Commit( $theContainer, NULL, $modifiers );
			
			//
			// Convert object.
			//
			$theReference = ( $theModifiers & kFLAG_REFERENCE_MASK )
						  ? $theContainer->Reference( $theReference, $theModifiers )
						  : $theReference[ kTAG_LID ];
			
			//
			// Set result.
			//
			$done = TRUE;
		
		} // Found an instance to convert.
		
		//
		// Skip data type scalars.
		//
		elseif( ! $theReference instanceof CDataType )
		{
			//
			// Check structures.
			//
			if( $doRecurse
			 && ( is_array( $theReference )
			   || ($theReference instanceof ArrayObject) ) )
			{
				//
				// Check data element.
				//
				if( array_key_exists( kTAG_DATA, (array) $theReference ) )
				{
					//
					// Recurse on data element.
					//
					$tmp = $theReference[ kTAG_DATA ];
					if( $this->_CommitReferences
						( $tmp, $theContainer, $theModifiers, 0 ) )
					{
						$done = TRUE;
						$theReference[ kTAG_DATA ] = $tmp;
					
					} // Converted.
					
					//
					// Recurse on predicate element.
					//
					if( array_key_exists( kTAG_KIND, (array) $theReference ) )
					{
						//
						// Recurse on data element.
						//
						$tmp = $theReference[ kTAG_KIND ];
						if( $this->_CommitReferences
							( $tmp, $theContainer, $theModifiers, 0 ) )
						{
							$done = TRUE;
							$theReference[ kTAG_KIND ] = $tmp;
						
						} // Converted.
					
					} // Has predicate.
				
				} // Found predicate relation.
				
				//
				// Handle list.
				//
				elseif( $doRecurse > 1 )
				{
					//
					// Adjust recursion level.
					//
					$doRecurse--;
					
					//
					// Scan list.
					//
					foreach( $theReference as $key => $value )
					{
						//
						// Recurse element.
						//
						if( $this->_CommitReferences
							( $value, $theContainer, $theModifiers, $doRecurse ) )
						{
							$done = TRUE;
							$theReference[ $key ] = $value;
						
						} // Converted.
					
					} // Iterating list.
				
				} // Found list.
			
			} // Structure or list.
		
		} // Not a scalar data type.
		
		return $done;																// ==>
		
	} // _CommitReferences.

		

/*=======================================================================================
 *																						*
 *								PROTECTED TAGGING UTILITIES								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_GetTags																		*
	 *==================================================================================*/

	/**
	 * Get attribute tags.
	 *
	 * In this class we exclude the {@link kTAG_LID kTAG_LID}, {@link kTAG_CLASS kTAG_CLASS}
	 * and {@link kTAG_VERSION kTAG_VERSION} tags, since they are set by default.
	 *
	 * @access protected
	 * @return array
	 */
	protected function _GetTags()
	{
		//
		// Get tags.
		//
		$tags = parent::_GetTags();
		
		//
		// Remove ID, class and version.
		//
		if( ($key = array_search( kTAG_LID, $tags )) !== FALSE )
			unset( $tags[ $key ] );
		if( ($key = array_search( kTAG_CLASS, $tags )) !== FALSE )
			unset( $tags[ $key ] );
		if( ($key = array_search( kTAG_VERSION, $tags )) !== FALSE )
			unset( $tags[ $key ] );
		
		return array_values( $tags );												// ==>
	
	} // _SetTags.

		

/*=======================================================================================
 *																						*
 *							PROTECTED MEMBER ACCESSOR INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_ManageObjectList																*
	 *==================================================================================*/

	/**
	 * Manage a list of object references.
	 *
	 * This method can be used to manage a list of object references, in which each element
	 * is either:
	 *
	 * <ul>
	 *	<li><i>Scalar</i>: A scalar or object representing:
	 *	 <ul>
	 *		<li><i>The object</i>: The actual referenced object.
	 *		<li><i>The object reference</i>: An object reference structure or a scalar
	 *			representing the object {@link kTAG_LID identifier}.
	 *	 </ul>
	 *		or:
	 *	<li><i>Array</i>: A structure composed of two items:
	 *	 <ul>
	 *		<li><i>{@link kTAG_KIND kTAG_KIND}</i>: This offset represents the type or
	 *			predicate of the reference.
	 *		<li><i>{@link kTAG_DATA kTAG_DATA}</i>: This offset represents the actual object
	 *			or object reference.
	 *	 </ul>
	 * </ul>
	 *
	 * The reference list is numerically indexed array and this method will ensure it
	 * remains so.
	 *
	 * The method accepts the following parameters:
	 *
	 * <ul>
	 *	<li><b>$theOffset</b>: This parameter represents the offset in the current object
	 *		that holds the list of references. This value may not be empty.
	 *	<li><b>$theValue</b>: This parameter represents either the search key in the list
	 *		when retrieving or deleting, or the reference when replacing or adding. If you
	 *		provide an array, it means that the elements may have a
	 *		{@link kTAG_KIND kTAG_KIND} offset and that the reference or object must be
	 *		found in the {@link kTAG_DATA kTAG_DATA} offset. When matching, if the
	 *		{@link kTAG_KIND kTAG_KIND} offset is not provided, it means that only those
	 *		elements that do not have a {@link kTAG_KIND kTAG_KIND} offset will be selected
	 *		for matching. If the types match, the method will use the
	 *		{@link _ObjectIndex() _ObjectIndex} method to match the references, please refer
	 *		to its documentation for more information. If the provided value is not an
	 *		array, it means that the reference list does not feature types, so matches will
	 *		only be performed on the reference.
	 *	<li><b>$theOperation</b>: The operation to perform:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the element matched by the previous parameter.
	 *		<li><i>FALSE</i>: Delete the element matched by the previous parameter and
	 *			return it.
	 *		<li><i>other</i>: Any other value means that we want to add to the list the
	 *			element provided in the previous parameter, either appending it if there
	 *			was no matching element, or by replacing a matching element. The method will
	 *			return either the replaced element or the new one.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return when deleting or
	 *		replacing:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the deleted or replaced element.
	 *		<li><i>FALSE</i>: Return the replacing element or <i>NULL</i> when deleting.
	 *	 </ul>
	 * </ul>
	 *
	 * The {@link _ObjectIndex() method} used to match the list elements expects
	 * {@link kTAG_LID identifiers} in the references or objects, if these are not
	 * there, there is no way to discern duplicates.
	 *
	 * @param string				$theOffset			Offset.
	 * @param mixed					$theValue			Reference or instance.
	 * @param mixed					$theOperation		Operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access protected
	 * @return mixed
	 */
	protected function _ManageObjectList( $theOffset, $theValue, $theOperation = NULL,
																 $getOld = FALSE )
	{
		//
		// Check offset.
		//
		if( $theOffset === NULL )
			throw new CException
					( "Invalid offset",
					  kERROR_INVALID_PARAMETER,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Offset' => $theOffset ) );						// !@! ==>
		
		//
		// Check reference or instance.
		//
		if( $theValue === NULL )
			throw new CException
					( "Invalid reference or instance",
					  kERROR_INVALID_PARAMETER,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Reference' => $theValue ) );						// !@! ==>
		
		//
		// Generate recursive calls.
		//
		if( is_array( $theValue )
		 && (! array_key_exists( kTAG_DATA, $theValue )) )
		{
			//
			// Iterate arguments.
			//
			$result = Array();
			foreach( $theValue as $value )
				$result[]
					= $this->_ManageObjectList
						( $theOffset, $theValue, $theOperation, $getOld );
			
			return $result;															// ==>
		
		} // Execute list.
		
		//
		// Get typed reference matchers.
		//
		if( ( is_array( $theValue )
		   || ($theValue instanceof ArrayObject) )
		 && array_key_exists( kTAG_DATA, (array) $theValue ) )
		{
			//
			// Set match type.
			//
			$type = ( array_key_exists( kTAG_KIND, (array) $theValue ) )
				  ? $this->_ObjectIndex( $theValue[ kTAG_KIND ] )
				  : NULL;
			
			//
			// Set identifier.
			//
			$ident = $this->_ObjectIndex( $theValue[ kTAG_DATA ] );
		
		} // Typed reference.
		
		//
		// Get untyped reference matchers.
		//
		else
		{
			//
			// Reset type.
			//
			$type = FALSE;
			
			//
			// Set reference identifier.
			//
			$ident = $this->_ObjectIndex( $theValue );
		
		} // Reference matcher.
		
		//
		// RETRIEVE.
		//
		if( $theOperation === NULL )
		{
			//
			// Check list.
			//
			if( ($save = $this->offsetGet( $theOffset )) !== NULL )
			{
				//
				// Iterate list.
				//
				foreach( $save as $value )
				{
					//
					// Untyped match.
					//
					if( $type === FALSE )
					{
						//
						// Match identifier.
						//
						if( $ident == $this->_ObjectIndex( $value ) )
							return $value;											// ==>
					
					} // Untyped match.
					
					//
					// Typed match.
					//
					else
					{
						//
						// Select matching structures.
						//
						if( ( is_array( $value )
						   || ($value instanceof ArrayObject) )
						 && array_key_exists( kTAG_DATA, (array) $value ) )
						{
							//
							// Match type.
							//
							if( ($type !== NULL)
							 && array_key_exists( kTAG_KIND, (array) $value )
							 && ($type == $this->_ObjectIndex( $value[ kTAG_KIND ] )) )
							{
								//
								// Match identifier.
								//
								if( $ident == $this->_ObjectIndex( $value[ kTAG_DATA ] ) )
									return $value;									// ==>
							
							} // Matched type.
							
							//
							// Match missing type.
							//
							elseif( ($type === NULL)
								 && (! array_key_exists( kTAG_KIND, (array) $value )) )
							{
								//
								// Match identifier.
								//
								if( $ident == $this->_ObjectIndex( $value[ kTAG_DATA ] ) )
									return $value;									// ==>
							
							} // Matched missing type.
						
						} // Matched structure.
					
					} // Typed match.
				
				} // Iterating list.
			
			} // Have list.
			
			return NULL;															// ==>
		
		} // Retrieve.
		
		//
		// Handle delete.
		//
		if( $theOperation === FALSE )
		{
			//
			// Check list.
			//
			if( ($save = $this->offsetGet( $theOffset )) !== NULL )
			{
				//
				// Iterate list.
				//
				$found = NULL;
				$new = Array();
				foreach( $save as $value )
				{
					//
					// Untyped match.
					//
					if( $type === FALSE )
					{
						//
						// Match identifier.
						//
						if( $ident == $this->_ObjectIndex( $value ) )
						{
							//
							// Save match.
							//
							$found = $value;
							
							//
							// Iterate.
							//
							continue;										// =>
						
						} // matched identifier.
					
					} // Untyped match.
					
					//
					// Typed match.
					//
					else
					{
						//
						// Select matching structures.
						//
						if( ( is_array( $value )
						   || ($value instanceof ArrayObject) )
						 && array_key_exists( kTAG_DATA, (array) $value ) )
						{
							//
							// Match type.
							//
							if( ($type !== NULL)
							 && array_key_exists( kTAG_KIND, (array) $value )
							 && ($type == $this->_ObjectIndex( $value[ kTAG_KIND ] )) )
							{
								//
								// Match identifier.
								//
								if( $ident == $this->_ObjectIndex( $value[ kTAG_DATA ] ) )
								{
									//
									// Save match.
									//
									$found = $value;
									
									//
									// Iterate.
									//
									continue;								// =>
								
								} // matched identifier.
							
							} // Matched type.
							
							//
							// Match missing type.
							//
							elseif( ($type === NULL)
								 && (! array_key_exists( kTAG_KIND, (array) $value )) )
							{
								//
								// Match identifier.
								//
								if( $ident == $this->_ObjectIndex( $value[ kTAG_DATA ] ) )
								{
									//
									// Save match.
									//
									$found = $value;
									
									//
									// Iterate.
									//
									continue;								// =>
								
								} // matched identifier.
							
							} // Matched missing type.
						
						} // Matched structure.
					
					} // Typed match.
					
					//
					// Save noon-matching elements.
					//
					$new[] = $value;
				
				} // Iterating list.
				
				//
				// Replace list.
				//
				if( $found !== NULL )
				{
					//
					// Remove offset.
					//
					if( ! count( $new ) )
						$this->offsetUnset( $theOffset );
					
					//
					// Replace offset.
					//
					else
						$this->offsetSet( $theOffset, $new );
				
				} // Matched.
				
				if( $getOld )
					return $found;													// ==>
			
			} // Have list.
			
			return NULL;															// ==>
		
		} // Delete.
		
		//
		// Replace value.
		//
		$found = NULL;
		if( ($save = $this->offsetGet( $theOffset )) !== NULL )
		{
			//
			// Iterate list.
			//
			foreach( $save as $key => $value )
			{
				//
				// Untyped match.
				//
				if( $type === FALSE )
				{
					//
					// Match identifier.
					//
					if( $ident == $this->_ObjectIndex( $value ) )
					{
						//
						// Save replaced.
						//
						$found = $value;
						
						//
						// Replace.
						//
						$save[ $key ] = $theValue;
						
						break;												// =>
						
					} // Matched.
				
				} // Untyped match.
				
				//
				// Typed match.
				//
				else
				{
					//
					// Select matching structures.
					//
					if( ( is_array( $value )
					   || ($value instanceof ArrayObject) )
					 && array_key_exists( kTAG_DATA, (array) $value ) )
					{
						//
						// Match type.
						//
						if( ($type !== NULL)
						 && array_key_exists( kTAG_KIND, (array) $value )
						 && ($type == $this->_ObjectIndex( $value[ kTAG_KIND ] )) )
						{
							//
							// Match identifier.
							//
							if( $ident == $this->_ObjectIndex( $value[ kTAG_DATA ] ) )
							{
								//
								// Save replaced.
								//
								$found = $value;
								
								//
								// Replace.
								//
								$save[ $key ] = $theValue;
								
								break;										// =>
								
							} // Matched.
						
						} // Matched type.
						
						//
						// Match missing type.
						//
						elseif( ($type === NULL)
							 && (! array_key_exists( kTAG_KIND, (array) $value )) )
						{
							//
							// Match identifier.
							//
							if( $ident == $this->_ObjectIndex( $value[ kTAG_DATA ] ) )
							{
								//
								// Save replaced.
								//
								$found = $value;
								
								//
								// Replace.
								//
								$save[ $key ] = $theValue;
								
								break;										// =>
								
							} // Matched.
						
						} // Matched missing type.
					
					} // Matched structure.
				
				} // Typed match.
			
			} // Iterating list.
			
			//
			// Append new element.
			//
			if( $found === NULL )
				$save[] = $theValue;
		
		} // List exists.
		
		//
		// Build list.
		//
		else
			$save = array( $theValue );
		
		//
		// Create list.
		//
		$this->offsetSet( $theOffset, $save );
		
		if( $getOld )
			return $found;															// ==>
		
		return $theValue;															// ==>
	
	} // _ManageObjectList.

		

/*=======================================================================================
 *																						*
 *							PROTECTED REFERENCING UTILITIES								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_CheckRelationObject															*
	 *==================================================================================*/

	/**
	 * Normalise object reference parameter.
	 *
	 * This method can be used to normalise a parameter that is supposed to be a reference
	 * to another object, the method will perform the following conversions:
	 *
	 * <ul>
	 *	<li><i>CGraphNodeObject</i>: Objects derived from this class will be handled as
	 *		follows:
	 *	 <ul>
	 *		<li><i>{@link _IsCommitted() Committed}</i>: If the provided object has a
	 *			{@link _IsCommitted() committed} {@link kFLAG_STATE_COMMITTED status}, the
	 *			method will return the object's {@link kTAG_LID identifier}.
	 *		<li><i>Not {@link _IsCommitted() committed}</i>: The parameter will not be
	 *			converted.
	 *	 </ul>
	 *	<li><i>{@link CDataType CDataType}</i>: When providing a complex data type, we
	 *		assume the value corresponds to the {@link kTAG_LID identifier}, in which case
	 *		we leave it untouched.
	 *	<li><i>Array</i> or <i>ArrayObject</i>: In this case the method will assume the
	 *		provided structure is an object reference and it will check if the
	 *		{@link kTAG_REFERENCE_ID kTAG_REFERENCE_ID} offset is there, if this is
	 *		not the case the method will raise an exception.
	 *	<li><i>other</i>: Any other type will be converted to a string.
	 * </ul>
	 *
	 * The method will return the converted value, derived classes should first handle
	 * custom types and pass other types to the parent method.
	 *
	 * @param mixed					$theValue			Object or reference.
	 *
	 * @access protected
	 * @return mixed
	 *
	 * @uses _IsCommitted()
	 *
	 * @see kTAG_LID kTAG_REFERENCE_ID
	 */
	protected function _CheckRelationObject( $theValue )
	{
		//
		// Handle object's derived from this class.
		//
		if( $theValue instanceof self )
		{
			//
			// Reference committed objects.
			//
			if( $theValue->_IsCommitted() )
				return $theValue[ kTAG_LID ];										// ==>
			
			return $theValue;														// ==>
		
		} // Object derived from this class.
		
		//
		// Handle complex data types.
		//
		if( $theValue instanceof CDataType )
			return $theValue;														// ==>
		
		//
		// Check object reference.
		//
		if( is_array( $theValue )
		 || ($theValue instanceof ArrayObject) )
		{
			//
			// Check identifier.
			//
			if( array_key_exists( kTAG_REFERENCE_ID, (array) $theValue ) )
				return $theValue;													// ==>

			throw new CException( "Invalid object reference: missing identifier",
								  kERROR_INVALID_PARAMETER,
								  kMESSAGE_TYPE_ERROR,
								  array( 'Reference' => $theValue ) );			// !@! ==>
		
		} // Object reference?
		
		return (string) $theValue;													// ==>
	
	} // _CheckRelationObject.

	 
	/*===================================================================================
	 *	_CheckRelationPredicate															*
	 *==================================================================================*/

	/**
	 * Normalise predicate reference parameter.
	 *
	 * This method can be used to normalise a parameter that is supposed to be a relation
	 * predicate, the method will perform the following conversions:
	 *
	 * <ul>
	 *	<li><i>NULL</i>: No conversion.
	 *	<li><i>FALSE</i>: No conversion.
	 *	<li><i>CGraphNodeObject</i>: The method will pass the parameter to the
	 *		{@link _CheckRelationObject() _CheckRelationObject} method.
	 *	<li><i>{@link CDataType CDataType}</i>: The method will pass the parameter to the
	 *		{@link _CheckRelationObject() _CheckRelationObject} method.
	 *	<li><i>Array</i> or <i>ArrayObject</i>: The method will pass the parameter to the
	 *		{@link _CheckRelationObject() _CheckRelationObject} method.
	 *	<li><i>other</i>: Any other type will be converted to a string.
	 * </ul>
	 *
	 * The method will return the converted value, derived classes should first handle
	 * custom types and pass other types to the parent method.
	 *
	 * @param mixed					$theValue			Relation predicate.
	 *
	 * @access protected
	 * @return mixed
	 *
	 * @uses _IsCommitted()
	 *
	 * @see kTAG_LID kTAG_REFERENCE_ID
	 */
	protected function _CheckRelationPredicate( $theValue )
	{
		//
		// Handle missing or empty predicate.
		//
		if( ($theValue === NULL)
		 || ($theValue === FALSE) )
			return $theValue;														// ==>
		
		//
		// Handle object.
		//
		if( is_array( $theValue )
		 || ($theValue instanceof self)
		 || ($theValue instanceof CDataType)
		 || ($theValue instanceof ArrayObject) )
			return $this->_CheckRelationObject( $theValue );						// ==>
		
		return (string) $theValue;													// ==>
	
	} // _CheckRelationPredicate.

	 
	/*===================================================================================
	 *	_ObjectIndex																	*
	 *==================================================================================*/

	/**
	 * Return object index.
	 *
	 * This method is a utility that can be used to extract an object identifier from a
	 * value, it is used when adding objects or object references to a list that is not
	 * organised by object {@link kTAG_LID ID}.
	 *
	 * This method will attempt to infer the object identifier by performing the following
	 * steps:
	 *
	 * <ul>
	 *	<li><i>Array</i> or <i>ArrayObject</i>: In this case we interpret the parameter to
	 *		be either an instance of the object itself, or a reference to the object, we
	 *		check in order if any of the following can be found:
	 *	 <ul>
	 *		<li><i>{@link kTAG_LID kTAG_LID}</i>: We first check whether the
	 *			object has that offset and use it is so.
	 *		<li><i>{@link kTAG_REFERENCE_ID kTAG_REFERENCE_ID}</i>: We then check
	 *			whether the structure contains a reference identifier.
	 *		<li><i>{@link _id() _id}</i>: If the parameter is an object derived from this
	 *			class, we try to call this method and use its result.
	 *	 </ul>
	 *	<li><i>other</i>: If all of the above fails we simply return the provided value.
	 * </ul>
	 *
	 * Note that the method assumes that the returned value must be convertable to a string,
	 * if that is not the case you may get into trouble.
	 *
	 * @param mixed					$theValue			Object or identifier.
	 *
	 * @access protected
	 * @return string|NULL
	 */
	protected function _ObjectIndex( $theValue )
	{
		//
		// Return empty.
		//
		if( $theValue === NULL )
			return NULL;															// ==>
		
		//
		// Try identifier.
		//
		if( ( is_array( $theValue )
		   && array_key_exists( kTAG_LID, $theValue ) )
		 || ( ($theValue instanceof ArrayObject)
		   && $theValue->offsetExists( kTAG_LID ) ) )
			return $theValue[ kTAG_LID ];											// ==>

		//
		// Try reference identifier.
		//
		if( ( is_array( $theValue )
		   && array_key_exists( kTAG_REFERENCE_ID, $theValue ) )
		 || ( ($theValue instanceof ArrayObject)
		   && $theValue->offsetExists( kTAG_REFERENCE_ID ) ) )
			return $theValue[ kTAG_REFERENCE_ID ];									// ==>
		
		//
		// Try identifier value.
		//
		if( $theValue instanceof self )
			return (string) $theValue;
		
		return $theValue;															// ==>
	
	} // _ObjectIndex.

	 

} // class CPersistentUnitObject.


?>
