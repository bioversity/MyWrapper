<?php

/**
 * <i>CPersistentUnitObject</i> class definition.
 *
 * This file contains the class definition of <b>CPersistentUnitObject</b> which extends its
 * {@link CPersistentObject ancestor} to implement an object that has a unique key, a field
 * storing its {@link kTAG_CLASS class} and a {@link kTAG_VERSION version}.
 *
 *	@package	Framework
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
 *
 * The class features two read-only public methods that return:
 *
 * <ul>
 *	<li><i>{@link _id() _id()}</i>: The object unique identifier as used by the native
 *		persistent container to uniquely identify the object.
 *	<li><i>{@link _index() _index()}</i>: The full index that uniquely identifies the
 *		object.
 * </ul>
 *
 * Although both methods seem to return the same information, they are different, in the
 * sense that the {@link _index() _index} method should return the property or properties
 * that constitute the object's unique identifier concatenated in a single string, this may
 * be too long to use as an index, whereas {@link _id() _id()} returns the actual value used
 * as the key, which may be the hashed {@link _index() _index} value. In other words
 * {@link _index() _index} is the human readable version of {@link _id() _id}.
 *
 * When the object is {@link Commit committed} for the first time, the value of the
 * {@link _id() _id} method will be set in the {@link kTAG_ID_NATIVE kTAG_ID_NATIVE} offset
 * which represents the object ID. This offset should never be changed and represents the
 * persistent identifier of the object.
 *
 * Objects derived from this class also hold, by default, their class name in an
 * {@link kTAG_CLASS offset}, this is used to {@link NewObject() instantiate} objects of the
 * correct class when retrieving data from a container.
 *
 * This class also features a {@link kTAG_VERSION version} which is an integer, incremented
 * each time the object is {@link Commit() committed}: this is  useful to implement a
 * concurrency control mechanism.
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
 * @package		Framework
 * @subpackage	Persistence
 */
class CPersistentUnitObject extends CPersistentObject
{
		

/*=======================================================================================
 *																						*
 *								PUBLIC MANAGEMENT INTERFACE								*
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
	 * identifier to be the {@link _id() unique} {@link kTAG_ID_NATIVE identifier} of the
	 * object.
	 *
	 * This method takes advantage of the {@link Commit() stored} {@link kTAG_CLASS class}
	 * name.
	 *
	 * The method will return an object, if the identifier matches and the object has its
	 * {@link kTAG_CLASS class} name; an array if the identifier matches, but the data
	 * doesn't contain a @link kTAG_CLASS class} name reference; <i>NULL</i> if the
	 * identifier didn't match.
	 *
	 * @param mixed					$theContainer		Persistent container.
	 * @param mixed					$theIdentifier		Object identifier.
	 *
	 * @static
	 * @return mixed
	 */
	static function NewObject( $theContainer, $theIdentifier )
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
		$data = $theContainer->Load( $theIdentifier );
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
				$object = new $class( $data );
				
				//
				// Mark as committed.
				//
				$object->_IsCommitted( TRUE );
				
				return $object;														// ==>
			
			} // Has class name.
		
		} // Found object.
		
		return $data;																// =>
		
	} // NewObject.

		

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
	 * This method will be called {@link _PrepareStore() before} {@link Commit() committing}
	 * the object to fill its unique identifier {@link kTAG_ID_NATIVE offset}. 
	 *
	 * If this method returns <i>NULL</i>, it is assumed that it will be the
	 * {@link CContainer container} that will provide a default unique value.
	 *
	 * In this class we return the value of {@link _index() _index} by default.
	 *
	 * @access protected
	 * @return mixed
	 */
	protected function _id()									{	return $this->_index();	}

	 
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
	 * object's unique {@link kTAG_ID_NATIVE identifier}, maybe hashed to make the index
	 * smaller.
	 *
	 * In this class we return by default <i>NULL</i>.
	 *
	 * @access protected
	 * @return string
	 */
	protected function _index()											{	return NULL;	}

		

/*=======================================================================================
 *																						*
 *								PROTECTED PERSISTENCE UTILITIES							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_PrepareFind																	*
	 *==================================================================================*/

	/**
	 * Normalise parameters of a find.
	 *
	 * The duty of this method is to ensure that the parameters provided to a
	 * {@link _Load() find} operation are valid.
	 *
	 * In this class we ensure that the container is derived from
	 * {@link CContainer CContainer}.
	 *
	 * @param reference			   &$theContainer		Object container.
	 * @param reference			   &$theIdentifier		Object identifier.
	 *
	 * @access protected
	 *
	 * @throws {@link CException CException}
	 *
	 * @see kERROR_OPTION_MISSING kERROR_UNSUPPORTED
	 */
	protected function _PrepareFind( &$theContainer, &$theIdentifier )
	{
		//
		// Call parent method.
		//
		parent::_PrepareFind( $theContainer, $theIdentifier );
		
		//
		// Check container.
		//
		if( ! $theContainer instanceof CContainer )
			throw new CException
					( "Unsupported container type",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Container' => $theContainer ) );					// !@! ==>
	
	} // _PrepareFind.

	 
	/*===================================================================================
	 *	_PrepareStore																	*
	 *==================================================================================*/

	/**
	 * Normalise before a store.
	 *
	 * The duty of this method is to ensure that the parameters provided to the
	 * {@link _Commit() store} operation are correct.
	 *
	 * In this class we ensure that the container is a ArrayObject or a
	 * {@link CContainer CContainer} derived instance and we ensure the identifier is filled
	 * in the case it was not provided:
	 *
	 * <ul>
	 *	<li><i>Get {@link kTAG_ID_NATIVE kTAG_ID_NATIVE}</i>: If the object features the
	 *		{@link kTAG_ID_NATIVE kTAG_ID_NATIVE} offset, it will be preferred. This is
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
	 *	<li><i>{@link kTAG_VERSION kTAG_VERSION}</i>: If not set, we initialise this value
	 *		to zero, if already set, we increment it.
	 * </ul>
	 *
	 * @param reference			   &$theContainer		Object container.
	 * @param reference			   &$theIdentifier		Object identifier.
	 *
	 * @access protected
	 *
	 * @throws {@link CException CException}
	 *
	 * @see kERROR_OPTION_MISSING kERROR_UNSUPPORTED
	 */
	protected function _PrepareStore( &$theContainer, &$theIdentifier )
	{
		//
		// Call parent method.
		//
		parent::_PrepareStore( $theContainer, $theIdentifier );
		
		//
		// Handle identifier.
		//
		if( $theIdentifier === NULL )
		{
			//
			// Use existing.
			//
			if( $this->offsetExists( kTAG_ID_NATIVE ) )
				$theIdentifier = $this->offsetGet( kTAG_ID_NATIVE );
			
			//
			// Set with default value.
			//
			else
				$theIdentifier = $this->_id();
		
		} // Omitted identifier.
		
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
	
	} // _PrepareStore.

	 
	/*===================================================================================
	 *	_PrepareReferenceList															*
	 *==================================================================================*/

	/**
	 * Prepare references lists.
	 *
	 * This method will usually be called {@link _PrepareStore() before}
	 * {@link Commit() storing} the object: its duty is to {@link Commit() commit} and
	 * {@link CContainer::Reference() convert} to reference all referenced objects that are
	 * in the form of instances.
	 *
	 * The method will iterate all elements of the provided reference list, intercept all
	 * instances derived from this class and convert these to object references.
	 *
	 * The parameters to this method are:
	 *
	 * <ul>
	 *	<li><b>$theContainer</b>: The container that is about to receive the current object,
	 *		it must also be the container in which to find the references and must be
	 *		derived from {@link CContainer CContainer}.
	 *	<li><b>$theOffset</b>: The current object's offset in which the reference list is
	 *		stored.
	 *	<li><b>$theModifiers</b>: A bitfield indicating which elements of the
	 *		{@link CContainer::Reference() reference} should be included.
	 * </ul>
	 *
	 * @param CContainer			$theContainer		Object container.
	 * @param string				$theOffset			Reference list offset.
	 * @param bitfield				$theModifiers		Referencing options.
	 *
	 * @access protected
	 */
	protected function _PrepareReferenceList( $theContainer,
											  $theOffset,
											  $theModifiers = kFLAG_REFERENCE_IDENTIFIER )
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
		// Init local storage.
		//
		$done = FALSE;
		$references = $this->offsetGet( $theOffset );
		
		//
		// Handle list.
		//
		if( is_array( $references )
		 || ($references instanceof ArrayObject) )
		{
			//
			// Iterate list.
			//
			foreach( $references as $key => $value )
			{
				//
				// Handle simple reference.
				//
				if( $value instanceof self )
				{
					//
					// Commit object.
					//
					$value->Commit( $theContainer );
					
					//
					// Convert to reference.
					//
					$done = TRUE;
					$references[ $key ] = $theContainer->Reference( $value, $theModifiers );
				
				} // Simple reference.
				
				//
				// Handle typed reference.
				//
				elseif( ( is_array( $value )
					   || ($value instanceof ArrayObject) )
					 && array_key_exists( kTAG_DATA, (array) $value ) )
				{
					//
					// Check data element.
					//
					if( ($object = $value[ kTAG_DATA ]) instanceof self )
					{
						//
						// Commit.
						//
						$value[ kTAG_DATA ]->Commit( $theContainer );
						
						//
						// Convert to reference.
						//
						$done = TRUE;
						$value[ kTAG_DATA ]
							= $theContainer->Reference
								( $value[ kTAG_DATA ], $theModifiers );
						
						//
						// Update list element.
						//
						$references[ $key ] = $value;
					
					} // Is an instance.
				
				} // Possible typed list.
			
			} // Iterating list.
			
			//
			// Update list.
			//
			if( $done )
				$this->offsetSet( $theOffset, $references );
		
		} // Has a list.
		
	} // _PrepareReferenceList.

		

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
	 *			representing the object {@link kTAG_ID_NATIVE identifier}.
	 *	 </ul>
	 *		or:
	 *	<li><i>Array</i>: A structure composed of two items:
	 *	 <ul>
	 *		<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>: This offset represents the type or
	 *			class of the reference.
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
	 *		{@link kTAG_TYPE kTAG_TYPE} offset and that the reference or object must be
	 *		found in the {@link kTAG_DATA kTAG_DATA} offset. When matching, if the
	 *		{@link kTAG_TYPE kTAG_TYPE} offset is not provided, it means that only those
	 *		elements that do not have a {@link kTAG_TYPE kTAG_TYPE} offset will be selected
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
	 * {@link kTAG_ID_NATIVE identifiers} in the references or objects, if these are not
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
			$type = ( array_key_exists( kTAG_TYPE, (array) $theValue ) )
				  ? (string) $theValue[ kTAG_TYPE ]
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
							 && array_key_exists( kTAG_TYPE, (array) $value )
							 && ($type == $value[ kTAG_TYPE ]) )
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
								 && (! array_key_exists( kTAG_TYPE, (array) $value )) )
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
							 && array_key_exists( kTAG_TYPE, (array) $value )
							 && ($type == $value[ kTAG_TYPE ]) )
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
								 && (! array_key_exists( kTAG_TYPE, (array) $value )) )
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
						 && array_key_exists( kTAG_TYPE, (array) $value )
						 && ($type == $value[ kTAG_TYPE ]) )
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
							 && (! array_key_exists( kTAG_TYPE, (array) $value )) )
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

	 
	/*===================================================================================
	 *	_ObjectIndex																	*
	 *==================================================================================*/

	/**
	 * Return object index.
	 *
	 * This method is a utility that can be used to extract an identifier from a value, it
	 * is used when adding objects or object references to a list that is not organised by
	 * object {@link kTAG_ID_NATIVE ID}.
	 *
	 * This method will attempt ti infer the object identifier by performing the following
	 * steps:
	 *
	 * <ul>
	 *	<li><i>Array</i> or <i>ArrayObject</i>: In this case we interpret the parameter to
	 *		be either an instance of the object itself, or a reference to the object, we
	 *		check in order if any of the following can be found:
	 *	 <ul>
	 *		<li><i>{@link kTAG_ID_NATIVE kTAG_ID_NATIVE}</i>: We first check whether the
	 *			object has that offset and use it is so.
	 *		<li><i>{@link kTAG_ID_REFERENCE kTAG_ID_REFERENCE}</i>: We then check whether
	 *			the structure contains a reference identifier.
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
		   && array_key_exists( kTAG_ID_NATIVE, $theValue ) )
		 || ( ($theValue instanceof ArrayObject)
		   && $theValue->offsetExists( kTAG_ID_NATIVE ) ) )
			return (string) $theValue[ kTAG_ID_NATIVE ];							// ==>

		//
		// Try reference identifier.
		//
		if( ( is_array( $theValue )
		   && array_key_exists( kTAG_ID_REFERENCE, $theValue ) )
		 || ( ($theValue instanceof ArrayObject)
		   && $theValue->offsetExists( kTAG_ID_REFERENCE ) ) )
			return (string) $theValue[ kTAG_ID_REFERENCE ];							// ==>
		
		//
		// Try identifier value.
		//
		if( $theValue instanceof self )
		{
			//
			// Get identifier value.
			//
			$id = $theValue->_id();
			if( $id !== NULL )
				return (string) $id;												// ==>
			
			return NULL;															// ==>
		
		} // Try identifier value.
		
		return (string) $theValue;													// ==>
	
	} // _ObjectIndex.

	 

} // class CPersistentUnitObject.


?>
