<?php

/**
 * <i>CAttribute</i> class definition.
 *
 * This file contains the class definition of <b>CAttribute</b> which represents a static
 * attribute class.
 *
 *	@package	MyWrapper
 *	@subpackage	Framework
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 22/05/2012
 */

/*=======================================================================================
 *																						*
 *									CAttribute.php										*
 *																						*
 *======================================================================================*/

/**
 * Exceptions.
 *
 * This include file contains the exception class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CException.php" );

/**
 *	Attribute class.
 *
 * This class is a static methods repository which is used to manage
 * {@link CArrayObject CArrayObject} derived classe's attributes.
 *
 * The class is a collection of static methods which represent member accessor methods for
 * objects that store their properties as array elements. These methods will handle
 * insertion, extraction and deletion of attributes in either arrays or ArrayObject
 * instances, these methods will then be used by {@link CArrayObject CArrayObject} derived
 * classes as the base for their member accessor methods.
 *
 * The class features the following methods:
 *
 * <ul>
 *	<li><i>{@link ManageOffset() ManageOffset}</i>: Manage a scalar offset.
 *	<li><i>{@link ManageArrayOffset() ManageArrayOffset}</i>: Manage elements of an array
 *		offset.
 *	<li><i>{@link ManageTypedOffset() ManageTypedOffset}</i>: Manage elements of an array
 *		offset by category or type.
 *	<li><i>{@link ManageTypedArrayOffset() ManageTypedArrayOffset}</i>: Manage elements of
 *		an array offset by category or type and data element value.
 * </ul>
 *
 *	@package	MyWrapper
 *	@subpackage	Framework
 */
class CAttribute
{
		

/*=======================================================================================
 *																						*
 *							STATIC ATTRIBUTE MANAGEMENT INTERFACE						*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	ManageOffset																	*
	 *==================================================================================*/

	/**
	 * Manage a scalar offset.
	 *
	 * This method can be used to manage a scalar offset, its options involve setting,
	 * retrieving and deleting an offset of the provided array or ArrayObject.
	 *
	 * The method accepts the following parameters:
	 *
	 * <ul>
	 *	<li><b>&$theReference</b>: Reference to the attributes container, it may either
	 *		refer to an array or an ArrayObject, any other type will trigger an exception.
	 *	<li><b>$theOffset</b>: The offset to the attribute contained in the previous
	 *		parameter that is to be managed.
	 *	<li><b>$theValue</b>: The value or operation:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the offset's current value.
	 *		<li><i>FALSE</i>: Delete the offset.
	 *		<li><i>other</i>: Any other type represents the offset's new value.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value of the offset <i>before</i> it was eventually
	 *			modified.
	 *		<li><i>FALSE</i>: Return the value of the offset <i>after</i> it was eventually
	 *			modified.
	 *	 </ul>
	 * </ul>
	 *
	 * @param reference			   &$theReference		Array or ArrayObject reference.
	 * @param string				$theOffset			Offset to be managed.
	 * @param mixed					$theValue			New value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @static
	 * @return mixed
	 *
	 * @throws {@link CException CException}
	 */
	static function ManageOffset( &$theReference, $theOffset, $theValue = NULL,
															  $getOld = FALSE )
	{
		//
		// Check reference.
		//
		if( is_array( $theReference )
		 || ($theReference instanceof ArrayObject) )
		{
			//
			// Normalise offset.
			//
			$theOffset = (string) $theOffset;
			
			//
			// Save current list.
			//
			$save = ( isset( $theReference[ $theOffset ] ) )
				  ? $theReference[ $theOffset ]
				  : NULL;
			
			//
			// Return current value.
			//
			if( $theValue === NULL )
				return $save;														// ==>
			
			//
			// Delete offset.
			//
			if( $theValue === FALSE )
			{
				if( $save !== NULL )
				{
					if( is_array( $theReference ) )
						unset( $theReference[ $theOffset ] );
					else
						$theReference->offsetUnset( $theOffset );
				}
			}
			
			//
			// Set offset.
			//
			else
				$theReference[ $theOffset ] = $theValue;
			
			if( $getOld )
				return $save;														// ==>
			
			return ( $theValue === FALSE )
				 ? NULL																// ==>
				 : $theValue;														// ==>
		
		} // Supported reference.

		throw new CException
				( "Unsupported object reference",
				  kERROR_UNSUPPORTED,
				  kMESSAGE_TYPE_ERROR,
				  array( 'Reference' => $theReference ) );						// !@! ==>
	
	} // ManageOffset.

	 
	/*===================================================================================
	 *	ManageArrayOffset																*
	 *==================================================================================*/

	/**
	 * Manage an array offset.
	 *
	 * An array offset is an array property, this method can be used to set, retrieve and
	 * delete the elements of this property, as opposed to
	 * {@link ManageOffset() ManageOffset}, which is used to manage the property as a whole.
	 *
	 * The elements of this list are uniquely identified by a closure function which is
	 * either passed to this method or defaults to the {@link HashClosure() HashClosure}
	 * function.
	 *
	 * When deleting elements, if the list becomes empty, the whole offset will be deleted.
	 *
	 * The method accepts the following parameters:
	 *
	 * <ul>
	 *	<li><b>&$theReference</b>: Reference to the attributes container, it may either
	 *		refer to an array or an ArrayObject, any other type will trigger an exception.
	 *	<li><b>$theOffset</b>: The offset to the attribute contained in the previous
	 *		parameter that is to be managed. This referenced element is expected to be an
	 *		array, if this is not the case, the method will raise an exception.
	 *	<li><b>$theValue</b>: Depending on the next parameter, this may either refer to the
	 *		value to be set or to the index of the element to be retrieved or deleted:
	 *	 <ul>
	 *		<li><i>NULL</i>: This value indicates that we want to operate on all elements,
	 *			which means, in practical terms, that we either want to retrieve or delete
	 *			the full list. If the operation parameter resolves to <i>TRUE</i>, the
	 *			method will default to retrieving the current list and no new element will
	 *			be added.
	 *		<li><i>array</i>: An array indicates that we want to operate on a list of
	 *			values and that other parameters may also be provided as lists. Note that
	 *			ArrayObject instances are not considered here as arrays.
	 *		<li><i>other</i>: Any other type represents either the new value to be added or
	 *			the index to the value to be returned or deleted. Note that this value will
	 *			be hashed by the provided or {@link HashClosure() default} closure to
	 *			determine if the element is new or not. If you provide an ArrayObject
	 *			derived instance, it will be converted to an array.
	 *	 </ul>
	 *	<li><b>$theOperation</b>: This parameter represents the operation to be performed
	 *		whose scope depends on the value of the previous parameter:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the list element or full list.
	 *		<li><i>FALSE</i>: Delete the list element or full list.
	 *		<li><i>array</i>: This type is only considered if the <i>$theValue</i> parameter
	 *			is provided as an array: the method will be called for each element of the
	 *			<i>$theValue</i> parameter matched with the corresponding element of this
	 *			parameter, which also means that both both parameters must share the same
	 *			count.
	 *		<li><i>other</i>: Add the value to the list. If you provided <i>NULL</i> in the
	 *			previous parameter, the operation will be reset to <i>NULL</i>.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value <i>before</i> it was eventually modified.
	 *		<li><i>FALSE</i>: Return the value <i>after</i> it was eventually modified.
	 *	 </ul>
	 *	<li><b>$theClosure</b>: The hashing closure, this function accepts two parameters:
	 *	 <ul>
	 *		<li><i>$theElement</i>: The element to be hashed, if it is an array, the next
	 *			parameter is expected to be the offset to the element's item to be used for
	 *			hashing.
	 *		<li><i>$theOffset</i>: If provided, it implies that the previous parameter is an
	 *			array and the current parameter will be used as the offset to the value to
	 *			be hashed.
	 *	 </ul>
	 *		The function should return a value representing the element's key. If omitted or
	 *		<i>NULL</i>, the {@link HashClosure() default} closure will be used. If the
	 *		<i>$theValue</i> parameter was provided as an array, you can provide an array of
	 *		closures each applying to the corresponding element of <i>$theValue</i> list.
	 * </ul>
	 *
	 * @param reference			   &$theReference		Object reference.
	 * @param string				$theOffset			Offset.
	 * @param mixed					$theValue			Value to manage.
	 * @param mixed					$theOperation		Operation to perform.
	 * @param boolean				$getOld				TRUE get old value.
	 * @param closure				$theClosure			Hashing anonymous function.
	 *
	 * @static
	 * @return mixed
	 *
	 * @throws {@link CException CException}
	 *
	 * @uses HashClosure()
	 */
	static function ManageArrayOffset( &$theReference, $theOffset, $theValue = NULL,
																   $theOperation = NULL,
																   $getOld = FALSE,
																   $theClosure = NULL )
	{
		//
		// Check reference.
		//
		if( is_array( $theReference )
		 || ($theReference instanceof ArrayObject) )
		{
			//
			// Normalise offset.
			//
			$theOffset = (string) $theOffset;
			
			//
			// Resolve hashing closure.
			//
			if( $theClosure === NULL )
				$theClosure = self::HashClosure();
			
			//
			// Handle multiple parameters:
			//
			if( is_array( $theValue ) )
			{
				//
				// Init local storage.
				//
				$result = Array();
				$count = count( $theValue );
				
				//
				// Check operation.
				//
				if( is_array( $theOperation )
				 && (count( $theOperation ) != $count) )
					throw new CException
							( "Values and operations counts do not match",
							  kERROR_UNSUPPORTED,
							  kMESSAGE_TYPE_ERROR,
							  array( 'Values' => $theValue,
									 'Operations' => $theOperation ) );			// !@! ==>
				
				//
				// Check closures.
				//
				if( is_array( $theClosure )
				 && (count( $theClosure ) != $count) )
					throw new CException
							( "Values and closures counts do not match",
							  kERROR_UNSUPPORTED,
							  kMESSAGE_TYPE_ERROR,
							  array( 'Values' => $theValue,
									 'Closures' => $theClosure ) );				// !@! ==>
				
				//
				// Iterate values.
				//
				foreach( $theValue as $index => $value )
				{
					//
					// Set operation.
					//
					$operation = ( is_array( $theOperation ) )
							   ? $theOperation[ $index ]
							   : $theOperation;
				
					//
					// Set closure.
					//
					$closure = ( is_array( $theClosure ) )
							 ? $theClosure[ $index ]
							 : $theClosure;
					
					//
					// Get result.
					//
					$result[]
						= self::ManageArrayOffset
							( $theReference, $theOffset,
							  $value, $operation,
							  $getOld, $closure );
				
				} // Iterating list of values.
				
				return $result;														// ==>
			
			} // Multiple parameters.
			
			//
			// Manage full list.
			//
			if( $theValue === NULL )
			{
				//
				// Prevent adding.
				// This is because we would be adding the operation...
				//
				if( $theOperation )
					$theOperation = NULL;
				
				//
				// Retrieve or delete.
				//
				return self::ManageOffset(
					$theReference, $theOffset, $theOperation, $getOld );			// ==>
			
			} // Manage full list.
			
			//
			// Save current list.
			//
			$list = ( isset( $theReference[ $theOffset ] ) )
				  ? $theReference[ $theOffset ]
				  : NULL;
			
			//
			// Match element.
			//
			$idx = $save = NULL;
			if( is_array( $list )
			 || ($list instanceof ArrayObject) )
			{
				//
				// Set match hash.
				//
				$match = $theClosure( $theValue );
				
				//
				// Match element.
				//
				foreach( $list as $key => $value )
				{
					//
					// Match.
					//
					if( $match == $theClosure( $value ) )
					{
						//
						// Save index.
						//
						$idx = $key;
						
						//
						// Check value.
						//
						if( is_array( $value )
						 || ($value instanceof ArrayObject) )
						{
							//
							// Save value.
							//
							$save = $value;
							
							break;											// =>
						
						} // Data item is a list.
						
						else
							throw new CException
									( "Unsupported list attribute element type",
									  kERROR_UNSUPPORTED,
									  kMESSAGE_TYPE_ERROR,
									  array( 'Attribute' => $list,
											 'Element' => $value ) );			// !@! ==>
					
					} // Matched.
				
				} // Matching element.
			
			} // Attribute is a list.
			
			//
			// Invalid attribute type.
			//
			elseif( $list !== NULL )
				throw new CException
						( "Unsupported list attribute type",
						  kERROR_UNSUPPORTED,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Attribute' => $list,
						  		 'Offset' => $theOffset ) );					// !@! ==>
			
			//
			// Return current value.
			//
			if( $theOperation === NULL )
				return $save;														// ==>
			
			//
			// Delete data.
			//
			if( $theOperation === FALSE )
			{
				//
				// Delete element.
				//
				if( $idx !== NULL )
				{
					//
					// Remove element.
					//
					unset( $list[ $idx ] );
					
					//
					// Update list.
					//
					if( count( $list ) )
						$theReference[ $theOffset ] = array_values( $list );
					
					//
					// Delete offset.
					//
					else
					{
						//
						// Delete offset.
						//
						if( is_array( $theReference ) )
							unset( $theReference[ $theOffset ] );
						else
							$theReference->offsetUnset( $theOffset );
					
					} // Deleted all elements.
				
				} // Element exists.
				
				if( $getOld )
					return $save;													// ==>
				
				return NULL;														// ==>
			
			} // Delete data.
			
			//
			// Add or replace element.
			//
			if( $list !== NULL )
			{
				//
				// Set element.
				//
				$list[ $idx ] = $theValue;
				
				//
				// Update offset.
				//
				$theReference[ $theOffset ] = array_values( $list );
			
			} // Had values.
			
			//
			// Create list.
			//
			else
				$theReference[ $theOffset ] = array( $theValue );
			
			if( $getOld )
				return $save;														// ==>
			
			return $theValue;														// ==>
		
		} // Supported reference.

		throw new CException
				( "Unsupported object reference",
				  kERROR_UNSUPPORTED,
				  kMESSAGE_TYPE_ERROR,
				  array( 'Reference' => $theReference ) );						// !@! ==>
	
	} // ManageArrayOffset.

	 
	/*===================================================================================
	 *	ManageTypedOffset																*
	 *==================================================================================*/

	/**
	 * Manage a typed offset.
	 *
	 * A typed offset is an element of an array property which contains two items:
	 *
	 * <ul>
	 *	<li><i>Type</i>: This item represents the type or category of the element and may be
	 *		omitted.
	 *	<li><i>Data</i>: This item represents the element data and may not be omitted.
	 * </ul>
	 *
	 * Each element of the offset is uniquely identified by the type item, there may be only
	 * one element without the type item.
	 *
	 * The method accepts the following parameters:
	 *
	 * <ul>
	 *	<li><b>&$theReference</b>: Reference to the attributes container, it may either
	 *		refer to an array or an ArrayObject, any other type will trigger an exception.
	 *	<li><b>$theMainOffset</b>: The offset to the attribute contained in the previous
	 *		parameter that is to be managed. This referenced element is expected to be an
	 *		array, if this is not the case, the method will raise an exception.
	 *	<li><b>$theTypeOffset</b>: The offset to the element's item representing the type or
	 *		category of the element.
	 *	<li><b>$theDataOffset</b>: The offset to the element's item representing the data or
	 *		value of the element.
	 *	<li><b>$theType</b>: This parameter represents the value of the type or category of
	 *		the element, it will be used for matching elements of the list:
	 *	 <ul>
	 *		<li><i>NULL</i>: An empty type means that we are looking for the element lacking
	 *			the item referenced by the <i>$theTypeOffset</i>.
	 *		<li><i>array</i>: An array indicates that we want to operate on a list of
	 *			values and that we may be receiving the next parameters also as lists. Note
	 *			that ArrayObject instances are not considered here as arrays.
	 *		<li><i>other</i>: Any other value will be considered as the index to retrieve,
	 *			remove or add/replace. Note that this value will be hashed by the provided
	 *			or {@link HashClosure() default} closure to determine if the element is new
	 *			or not.
	 *	 </ul>
	 *	<li><b>$theData</b>: This parameter represents the item's data element, or the
	 *		operation to be performed:
	 *	 <ul>
	 *		<li><i>NULL</i>: This indicates that we want to retrieve the data of the item
	 *			with index matching the previous parameter.
	 *		<li><i>FALSE</i>: This indicates that we want to remove the item matching the
	 *			index provided in the previous parameter.
	 *		<li><i>array</i>: This type is only considered if the <i>$theType</i> parameter
	 *			is provided as an array: the method will be called for each element of the
	 *			<i>$theType</i> parameter matched with the corresponding element of this
	 *			parameter. This also implies that both parameters must share the same count.
	 *		<li><i>other</i>: Any other value indicates that we want to add or replace the
	 *			data element of the item matching the previous parameter.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value of the offset <i>before</i> it was eventually
	 *			modified.
	 *		<li><i>FALSE</i>: Return the value of the offset <i>after</i> it was eventually
	 *			modified.
	 *	 </ul>
	 *	<li><b>$theClosure</b>: The hashing closure, this function accepts two parameters:
	 *	 <ul>
	 *		<li><i>$theElement</i>: The element to be considered.
	 *		<li><i>$theOffset</i>: The offset within the element to the item representing
	 *			the type or category to be hashed.
	 *	 </ul>
	 *		The function should return a value representing the element's type hash. If
	 *		omitted or <i>NULL</i>, the {@link HashClosure() default} closure will be used.
	 *		If the <i>$theType</i> parameter was provided as an array, you can provide an
	 *		array of closures each applying to the corresponding element of <i>$theType</i>
	 *		list.
	 * </ul>
	 *
	 * The method will return the matched element's value of the data offset.
	 *
	 * @param reference			   &$theReference		Object reference.
	 * @param string				$theMainOffset		Main offset.
	 * @param string				$theTypeOffset		Type offset.
	 * @param string				$theDataOffset		Data offset.
	 * @param mixed					$theType			Type.
	 * @param mixed					$theData			Data or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 * @param closure				$theClosure			Hashing anonymous function.
	 *
	 * @static
	 * @return mixed
	 *
	 * @throws {@link CException CException}
	 *
	 * @uses HashClosure()
	 */
	static function ManageTypedOffset( &$theReference,
										$theMainOffset, $theTypeOffset, $theDataOffset,
										$theType = NULL, $theData = NULL,
										$getOld = FALSE, $theClosure = NULL )
	{
		//
		// Check reference.
		//
		if( is_array( $theReference )
		 || ($theReference instanceof ArrayObject) )
		{
			//
			// Normalise offsets.
			//
			$theMainOffset = (string) $theMainOffset;
			$theTypeOffset = (string) $theTypeOffset;
			$theDataOffset = (string) $theDataOffset;
			
			//
			// Resolve hashing closure.
			//
			if( $theClosure === NULL )
				$theClosure = self::HashClosure();
			
			//
			// Handle multiple parameters:
			//
			if( is_array( $theType ) )
			{
				//
				// Init local storage.
				//
				$result = Array();
				$count = count( $theType );
				
				//
				// Check data.
				//
				if( is_array( $theData )
				 && (count( $theData ) != $count) )
					throw new CException
							( "Type and data counts do not match",
							  kERROR_UNSUPPORTED,
							  kMESSAGE_TYPE_ERROR,
							  array( 'Type' => $theType,
									 'Data' => $theData ) );					// !@! ==>
				
				//
				// Check closures.
				//
				if( is_array( $theClosure )
				 && (count( $theClosure ) != $count) )
					throw new CException
							( "Type and closure counts do not match",
							  kERROR_UNSUPPORTED,
							  kMESSAGE_TYPE_ERROR,
							  array( 'Type' => $theType,
									 'Closure' => $theClosure ) );				// !@! ==>
				
				//
				// Iterate types.
				//
				foreach( $theType as $index => $value )
				{
					//
					// Set data.
					//
					$data = ( is_array( $theData ) )
							? $theData[ $index ]
							: $theData;
				
					//
					// Set closure.
					//
					$closure = ( is_array( $theClosure ) )
							? $theClosure[ $index ]
							: $theClosure;
					
					//
					// Recurse.
					//
					$result[]
						= self::ManageTypedOffset
							( $theReference,
							  $theMainOffset, $theTypeOffset, $theDataOffset,
							  $value, $data,
							  $getOld, $closure );
				
				} // Iterating types.
				
				return $result;														// ==>
			
			} // Provided multiple parameters.
			
			//
			// Save current list.
			//
			$list = ( isset( $theReference[ $theMainOffset ] ) )
				  ? $theReference[ $theMainOffset ]
				  : NULL;
			
			//
			// Match element.
			//
			$idx = $save = NULL;
			if( is_array( $list )
			 || ($list instanceof ArrayObject) )
			{
				//
				// Set match hash.
				//
				$match = $theClosure( $theType );
				
				//
				// Match element.
				//
				foreach( $list as $key => $value )
				{
					//
					// Match.
					//
					if( $match == $theClosure( $value, $theTypeOffset ) )
					{
						//
						// Save index.
						//
						$idx = $key;
						
						//
						// Check value.
						//
						if( is_array( $value )
						 || ($value instanceof ArrayObject) )
						{
							//
							// Save value.
							//
							$save = $value;
							
							break;											// =>
						
						} // Data item is a list.
						
						else
							throw new CException
									( "Unsupported list attribute element type",
									  kERROR_UNSUPPORTED,
									  kMESSAGE_TYPE_ERROR,
									  array( 'Attribute' => $list,
											 'Element' => $value ) );			// !@! ==>
					
					} // Matched.
				
				} // Matching element.
			
			} // Attribute is a list.
			
			//
			// Invalid attribute type.
			//
			elseif( $list !== NULL )
				throw new CException
						( "Unsupported list attribute type",
						  kERROR_UNSUPPORTED,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Attribute' => $list,
						  		 'Offset' => $theMainOffset ) );				// !@! ==>
			
			//
			// Return current value.
			//
			if( $theData === NULL )
				return $save;														// ==>
			
			//
			// Delete data.
			//
			if( $theData === FALSE )
			{
				//
				// Delete element.
				//
				if( $idx !== NULL )
				{
					//
					// Remove element.
					//
					unset( $list[ $idx ] );
					
					//
					// Replace list.
					//
					if( count( $list ) )
						$theReference[ $theMainOffset ] = array_values( $list );
					
					//
					// Delete offset.
					//
					else
					{
						//
						// Delete offset.
						//
						if( is_array( $theReference ) )
							unset( $theReference[ $theMainOffset ] );
						else
							$theReference->offsetUnset( $theMainOffset );
					
					} // Deleted all elements.
				
				} // Element exists.
				
				if( $getOld )
					return $save;													// ==>
				
				return NULL;														// ==>
			
			} // Delete data.
			
			//
			// Create element.
			//
			$element = Array();
			if( $theType !== NULL )
				$element[ $theTypeOffset ] = $theType;
			$element[ $theDataOffset ] = $theData;
			
			//
			// Add first element.
			//
			if( $list === NULL )
				$theReference[ $theMainOffset ] = array( $element );
			
			//
			// Has data.
			//
			else
			{
				//
				// Replace element.
				//
				if( $idx !== NULL )
					$list[ $idx ] = $element;
					
				//
				// Add element.
				//
				else
					$list[] = $element;
				
				//
				// Update offset.
				//
				$theReference[ $theMainOffset ] = $list;
			
			} // Has data.
			
			if( $getOld )
				return $save;														// ==>
			
			return $theData;														// ==>
		
		} // Supported reference.

		throw new CException
				( "Unsupported object reference",
				  kERROR_UNSUPPORTED,
				  kMESSAGE_TYPE_ERROR,
				  array( 'Reference' => $theReference ) );						// !@! ==>
	
	} // ManageTypedOffset.

	 
	/*===================================================================================
	 *	ManageTypedArrayOffset															*
	 *==================================================================================*/

	/**
	 * Manage a typed array offset.
	 *
	 * A typed array offset is an element of an array property which contains two items:
	 *
	 * <ul>
	 *	<li><i>Type</i>: This item represents the type or category of the element and may be
	 *		omitted.
	 *	<li><i>Data</i>: This item represents the element data, it may not be omitted and it
	 *		is structured as an array of data elements.
	 * </ul>
	 *
	 * Each element of the offset is uniquely identified by the type item, there may be only
	 * one element without the type item. Also, no two element of the data item may have the
	 * same value.
	 *
	 * The method accepts the following parameters:
	 *
	 * <ul>
	 *	<li><b>&$theReference</b>: Reference to the attributes container, it may either
	 *		refer to an array or an ArrayObject, any other type will trigger an exception.
	 *	<li><b>$theMainOffset</b>: The offset to the attribute contained in the previous
	 *		parameter that is to be managed. This referenced element is expected to be an
	 *		array, if this is not the case, the method will raise an exception.
	 *	<li><b>$theTypeOffset</b>: The offset to the element's item representing the type or
	 *		category of the element.
	 *	<li><b>$theDataOffset</b>: The offset to the element's item representing the data or
	 *		value of the element.
	 *	<li><b>$theType</b>: This parameter represents the value of the type or category of
	 *		the element, it will be used for matching elements of the list:
	 *	 <ul>
	 *		<li><i>NULL</i>: An empty type means that we are looking for the element lacking
	 *			the item referenced by the <i>$theTypeOffset</i>.
	 *		<li><i>array</i>: An array indicates that we want to operate on a list of
	 *			values and that we may be receiving the next parameters also as lists. Note
	 *			that ArrayObject instances are not considered here as arrays.
	 *		<li><i>other</i>: Any other value will be considered as the element's index, the
	 *			key to the element whose data item is to be managed.
	 *	 </ul>
	 *	<li><b>$theData</b>: This parameter represents the item's data element, or the data
	 *		item scope:
	 *	 <ul>
	 *		<li><i>NULL</i>: This indicates that the operation, provided in the next
	 *			parameter, applies to the whole list od data items; this also means that if
	 *			the operation is <i>TRUE</i> we raise here an exception, because one cannot
	 *			add a <i>NULL</i> data item.
	 *		<li><i>other</i>: Any other value represents either the index of the data item
	 *			to be retrieved or deleted, or the data item to be added. Note that if the
	 *			value is an array, the array elements will be added individually, rather
	 *			than setting the data to the actual array.
	 *	 </ul>
	 *	<li><b>$theOperation</b>: This parameter represents the operation to be performed,
	 *		it will be evaluated as a boolean and its scope depends on the value of the
	 *		previous parameter:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the element's data item or list.
	 *		<li><i>FALSE</i>: Delete the element's data item or list.
	 *		<li><i>TRUE</i>: Add or replace an element's data item or list.
	 *		<li><i>array</i>: This type is only considered if the <i>$theType</i> parameter
	 *			is provided as an array: the method will be called for each element of the
	 *			<i>$theType</i> parameter matched with the corresponding element of this
	 *			parameter. This also implies that both parameters must share the same count.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value of the offset <i>before</i> it was eventually
	 *			modified.
	 *		<li><i>FALSE</i>: Return the value of the offset <i>after</i> it was eventually
	 *			modified.
	 *	 </ul>
	 *	<li><b>$theClosure</b>: The hashing closure, this function accepts two parameters:
	 *	 <ul>
	 *		<li><i>$theElement</i>: The element to be considered.
	 *		<li><i>$theOffset</i>: The offset within the element to the item representing
	 *			the type or category to be hashed.
	 *	 </ul>
	 *		The function should return a value representing the element's type hash. If
	 *		omitted or <i>NULL</i>, the {@link HashClosure() default} closure will be used.
	 *		If the <i>$theType</i> parameter was provided as an array, you can provide an
	 *		array of closures each applying to the corresponding element of <i>$theType</i>
	 *		list. Note that this function is used both to compare elements and element data
	 *		items.
	 * </ul>
	 *
	 * The method will return the matched element's value of the data offset.
	 *
	 * @param reference			   &$theReference		Object reference.
	 * @param string				$theMainOffset		Main offset.
	 * @param string				$theTypeOffset		Type offset.
	 * @param string				$theDataOffset		Data offset.
	 * @param mixed					$theType			Type.
	 * @param mixed					$theData			Data or operation.
	 * @param mixed					$theOperation		Operation to perform.
	 * @param boolean				$getOld				TRUE get old value.
	 * @param closure				$theClosure			Hashing anonymous function.
	 *
	 * @static
	 * @return mixed
	 *
	 * @throws {@link CException CException}
	 *
	 * @uses HashClosure()
	 * @uses ManageArrayOffset()
	 */
	static function ManageTypedArrayOffset( &$theReference,
											 $theMainOffset, $theTypeOffset, $theDataOffset,
											 $theType = NULL, $theData = NULL,
											 $theOperation = NULL,
											 $getOld = FALSE, $theClosure = NULL )
	{
		//
		// Check reference.
		//
		if( is_array( $theReference )
		 || ($theReference instanceof ArrayObject) )
		{
			//
			// Normalise offsets.
			//
			$theMainOffset = (string) $theMainOffset;
			$theTypeOffset = (string) $theTypeOffset;
			$theDataOffset = (string) $theDataOffset;
			
			//
			// Resolve hashing closure.
			//
			if( $theClosure === NULL )
				$theClosure = self::HashClosure();
			
			//
			// Handle multiple elements:
			//
			if( is_array( $theType ) )
			{
				//
				// Init local storage.
				//
				$result = Array();
				$count = count( $theType );
				
				//
				// Check data.
				//
				if( is_array( $theData )
				 && (count( $theData ) != $count) )
					throw new CException
							( "Type and data counts do not match",
							  kERROR_INVALID_PARAMETER,
							  kMESSAGE_TYPE_ERROR,
							  array( 'Type' => $theType,
									 'Data' => $theData ) );					// !@! ==>
				
				//
				// Check operations.
				//
				if( is_array( $theOperation )
				 && (count( $theOperation ) != $count) )
					throw new CException
							( "Type and operation counts do not match",
							  kERROR_INVALID_PARAMETER,
							  kMESSAGE_TYPE_ERROR,
							  array( 'Type' => $theType,
									 'Operation' => $theOperation ) );			// !@! ==>
				
				//
				// Check closures.
				//
				if( is_array( $theClosure )
				 && (count( $theClosure ) != $count) )
					throw new CException
							( "Type and closure counts do not match",
							  kERROR_INVALID_PARAMETER,
							  kMESSAGE_TYPE_ERROR,
							  array( 'Type' => $theType,
									 'Closure' => $theClosure ) );				// !@! ==>
				
				//
				// Iterate types.
				//
				foreach( $theType as $index => $value )
				{
					//
					// Set data.
					//
					$data = ( is_array( $theData ) )
							? $theData[ $index ]
							: $theData;
				
					//
					// Set operation.
					//
					$operation = ( is_array( $theOperation ) )
							? $theOperation[ $index ]
							: $theOperation;
				
					//
					// Set closure.
					//
					$closure = ( is_array( $theClosure ) )
							? $theClosure[ $index ]
							: $theClosure;
					
					//
					// Recurse.
					//
					$result[]
						= self::ManageTypedArrayOffset
							( $theReference,
							  $theMainOffset, $theTypeOffset, $theDataOffset,
							  $value, $data, $operation,
							  $getOld, $closure );
				
				} // Iterating types.
				
				return $result;														// ==>
			
			} // Provided multiple elements.
			
			//
			// Handle multiple data items.
			//
			elseif( is_array( $theData ) )
			{
				//
				// Init local storage.
				//
				$result = Array();
				
				//
				// Recurse each data item.
				//
				foreach( $theData as $data )
					$result[]
						= self::ManageTypedArrayOffset
							( $theReference,
							  $theMainOffset, $theTypeOffset, $theDataOffset,
							  $theType, $data, $theOperation,
							  $getOld, $theClosure );
				
				return $result;														// ==>
			
			} // Provided multiple data items.

			//
			// Save offset.
			//
			$list = ( isset( $theReference[ $theMainOffset ] ) )
				  ? $theReference[ $theMainOffset ]
				  : NULL;
			
			//
			// Match element.
			//
			$idx = $save = NULL;
			if( is_array( $list )
			 || ($list instanceof ArrayObject) )
			{
				//
				// Set match hash.
				//
				$match = $theClosure( $theType );
				
				//
				// Match element.
				//
				foreach( $list as $key => $value )
				{
					//
					// Match.
					//
					if( $match == $theClosure( $value, $theTypeOffset ) )
					{
						//
						// Save index.
						//
						$idx = $key;
						
						//
						// Check value.
						//
						if( is_array( $value )
						 || ($value instanceof ArrayObject) )
						{
							//
							// Save value.
							//
							$save = $value;
							
							break;											// =>
						
						} // Data item is a list.
						
						else
							throw new CException
									( "Unsupported list attribute element type",
									  kERROR_UNSUPPORTED,
									  kMESSAGE_TYPE_ERROR,
									  array( 'Attribute' => $list,
											 'Element' => $value ) );			// !@! ==>
					
					} // Matched.
				
				} // Matching element.
			
			} // Attribute is a list.
			
			//
			// Invalid attribute type.
			//
			elseif( $list !== NULL )
				throw new CException
						( "Unsupported list attribute type",
						  kERROR_UNSUPPORTED,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Attribute' => $list,
						  		 'Offset' => $theMainOffset ) );				// !@! ==>
			
			//
			// Retrieve.
			//
			if( $theOperation === NULL )
			{
				//
				// Handle data.
				//
				if( $idx !== NULL )
				{
					//
					// Return list.
					//
					if( $theData === NULL )
						return $save[ $theDataOffset ];								// ==>
					
					//
					// Return data item.
					//
					return self::ManageArrayOffset
								( $save,
								  $theDataOffset, $theData, $theOperation,
								  $getOld, $theClosure );							// ==>
				
				} // Has data.
				
				return NULL;														// ==>
			
			} // Retrieve.
			
			//
			// Delete.
			//
			if( $theOperation === FALSE )
			{
				//
				// Handle data.
				//
				if( $idx !== NULL )
				{
					//
					// Remove data item.
					//
					if( $theData !== NULL )
					{
						//
						// Remove data item.
						//
						$result = self::ManageArrayOffset
								( $save,
								  $theDataOffset, $theData, $theOperation,
								  $getOld, $theClosure );
						
						//
						// Update element.
						//
						if( isset( $save[ $theDataOffset ] ) )	// Data item not deleted.
						{
							//
							// Update list.
							//
							$list[ $idx ] = $save;
							
							//
							// Update attribute.
							//
							$theReference[ $theMainOffset ] = $list;
						
						} // Data item not empty.
						
						//
						// Delete element.
						//
						else
							unset( $list[ $idx ] );
					
					} // Provided data item.
					
					//
					// Remove element.
					//
					else
						unset( $list[ $idx ] );
							
					//
					// Update offset.
					//
					if( count( $list ) )
						$theReference[ $theMainOffset ] = array_values( $list );
					
					//
					// Delete offset.
					//
					else
					{
						if( is_array( $theReference ) )
							unset( $theReference[ $theMainOffset ] );
						else
							$theReference->offsetUnset( $theMainOffset );
					
					} // No elements left.
					
					//
					// Handled data item.
					//
					if( $theData !== NULL )
						return $result;												// ==>
					
					if( $getOld )
						return $save;												// ==>
				
				} // Matched element.
				
				return NULL;														// ==>
			
			} // Delete.
			
			//
			// Handle impossible operation.
			//
			if( $theData === NULL )
				throw new CException
						( "Invalid data and operation parameters combination",
						  kERROR_INVALID_PARAMETER,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Data' => $theData,
								 'Operation' => $theOperation ) );				// !@! ==>
			
			//
			// Create new element.
			//
			if( ($list === NULL)		// No elements yet,
			 || ($save === NULL) )		// or element not matched.
			{
				//
				// Init element.
				//
				$element = Array();
				
				//
				// Set type item.
				//
				if( $theType !== NULL )
					$element[ $theTypeOffset ] = $theType;
				
				//
				// Set data item.
				//
				$element[ $theDataOffset ] = array( $theData );
				
				//
				// Create first element.
				//
				if( $list === NULL )
					$theReference[ $theMainOffset ] = array( $element );
				
				//
				// Add new element.
				//
				else
					$theReference[ $theMainOffset ][] = $element;
				
				if( $getOld )
					return NULL;													// ==>
				
				return $theData;													// ==>
			
			} // New element.
			
			//
			// Update element.
			//
			$result = self::ManageArrayOffset
					( $save,
					  $theDataOffset, $theData, $theOperation,
					  $getOld, $theClosure );
			
			//
			// Update list.
			//
			$list[ $idx ] = $save;
			
			//
			// Replace attribute.
			//
			$theReference[ $theMainOffset ] = $list;
			
			return $result;															// ==>
		
		} // Supported reference.

		throw new CException
				( "Unsupported object reference",
				  kERROR_UNSUPPORTED,
				  kMESSAGE_TYPE_ERROR,
				  array( 'Reference' => $theReference ) );						// !@! ==>
	
	} // ManageTypedArrayOffset.

	 
	/*===================================================================================
	 *	ManageTypedKindOffset															*
	 *==================================================================================*/

	/**
	 * Manage a kind and type offset.
	 *
	 * These array property elements contain three items:
	 *
	 * <ul>
	 *	<li><i>Kind</i>: This item represents the kind or category of the element, this
	 *		item may be omitted.
	 *	<li><i>Type</i>: This item represents the type of element's data, this item is
	 *		required.
	 *	<li><i>Data</i>: This item represents the element data and may not be omitted.
	 * </ul>
	 *
	 * No two elements of the list can share the same kind and type, these represent the
	 * element identifier.
	 *
	 * The method accepts the following parameters:
	 *
	 * <ul>
	 *	<li><b>&$theReference</b>: Reference to the attributes container, it may either
	 *		refer to an array or an ArrayObject, any other type will trigger an exception.
	 *	<li><b>$theMainOffset</b>: The offset to the attribute contained in the previous
	 *		parameter that is to be managed. This referenced element is expected to be an
	 *		array, if this is not the case, the method will raise an exception.
	 *	<li><b>$theKindOffset</b>: The offset to the element's item representing the kind of
	 *		the element.
	 *	<li><b>$theTypeOffset</b>: The offset to the element's item representing the type of
	 *		the element's data.
	 *	<li><b>$theDataOffset</b>: The offset to the element's item representing the data or
	 *		value of the element.
	 *	<li><b>$theKind</b>: This parameter represents the value of the element's kind, it
	 *		will be used for matching elements of the list:
	 *	 <ul>
	 *		<li><i>NULL</i>: An empty kind means that we are looking for the element lacking
	 *			the item referenced by the <i>$theKindOffset</i>.
	 *		<li><i>array</i>: An array indicates that we want to operate on a list of
	 *			values and that we may be receiving the next parameters also as lists. Note
	 *			that ArrayObject instances are not considered here as arrays.
	 *		<li><i>other</i>: Any other value will be considered as the element's kind. Note
	 *			that this value will be used by the provided or
	 *			{@link DoubleHashClosure() default} closure to determine if the element is
	 *			new or not.
	 *	 </ul>
	 *	<li><b>$theType</b>: This parameter represents the value of the element's data item
	 *		type, it will be used for matching elements of the list:
	 *	 <ul>
	 *		<li><i>array</i>: If the <i>$theKind</i> parameter was provided as an array, you
	 *			can provide an array of types each applying to the corresponding element of
	 *			<i>$theKind</i> list. Note that ArrayObject instances are not considered
	 *			here as arrays.
	 *		<li><i>other</i>: Any other value will be considered as the element's data item
	 *			type. Note that this value will be used by the provided or
	 *			{@link DoubleHashClosure() default} closure to determine if the element is
	 *			new or not.
	 *	 </ul>
	 *	<li><b>$theData</b>: This parameter represents the element's data item, or the
	 *		operation to be performed:
	 *	 <ul>
	 *		<li><i>NULL</i>: This indicates that we want to retrieve the data item of the
	 *			element matching the provided kind and type parameters.
	 *		<li><i>FALSE</i>: This indicates that we want to remove the data item of the
	 *			element matching the provided kind and type parameters.
	 *		<li><i>array</i>: If the <i>$theKind</i> parameter was provided as an array, you
	 *			can provide an array of data items each applying to the corresponding
	 *			element of <i>$theKind</i> list. Note that ArrayObject instances are not
	 *			considered here as arrays.
	 *		<li><i>other</i>: Any other value indicates that we want to add or replace the
	 *			data item of the element matching the provided kind and type parameters.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the element or list <i>before</i> it was eventually
	 *			modified.
	 *		<li><i>FALSE</i>: Return the element or list <i>after</i> it was eventually
	 *			modified.
	 *	 </ul>
	 *	<li><b>$theClosure</b>: The hashing closure, this function accepts three parameters:
	 *	 <ul>
	 *		<li><i>$theElement</i>: The element to be considered.
	 *		<li><i>$theKindOffset</i>: The offset within the previous parameter to the item
	 *			representing the element's kind.
	 *		<li><i>$theTypeOffset</i>: The offset within the previous parameter to the item
	 *			representing the element's type.
	 *	 </ul>
	 *		The function should return a value representing the element's hash. If omitted
	 *		or <i>NULL</i>, the {@link DoubleHashClosure() default} closure will be used.
	 *		If the <i>$theKind</i> parameter was provided as an array, you can provide an
	 *		array of closures each applying to the corresponding element of <i>$theKind</i>
	 *		list.
	 * </ul>
	 *
	 * @param reference			   &$theReference		Object reference.
	 * @param string				$theMainOffset		Main offset.
	 * @param string				$theKindOffset		Type offset.
	 * @param string				$theTypeOffset		Type offset.
	 * @param string				$theDataOffset		Data offset.
	 * @param mixed					$theKind			Item kind.
	 * @param mixed					$theType			Item type.
	 * @param mixed					$theData			Item value.
	 * @param boolean				$getOld				TRUE get old value.
	 * @param closure				$theClosure			Hashing anonymous function.
	 *
	 * @static
	 * @return mixed
	 *
	 * @throws {@link CException CException}
	 *
	 * @uses DoubleHashClosure()
	 */
	static function ManageTypedKindOffset( &$theReference,
											$theMainOffset,
											$theKindOffset, $theTypeOffset, $theDataOffset,
											$theKind, $theType, $theData = NULL,
											$getOld = FALSE )
	{
		//
		// Check reference.
		//
		if( is_array( $theReference )
		 || ($theReference instanceof ArrayObject) )
		{
			//
			// Save offset.
			//
			if( is_array( $theReference ) )
				$list = ( array_key_exists( $theMainOffset, $theReference ) )
						? $theReference[ $theMainOffset ]
						: NULL;
			else
				$list = ( $theReference->offsetExists( $theMainOffset ) )
						? $theReference[ $theMainOffset ]
						: NULL;
			
			//
			// Handle categories.
			//
			$save = $idx = NULL;
			if( $list !== NULL )
			{
				//
				// Locate category.
				//
				foreach( $list as $key => $element )
				{
					//
					// Match kind and type.
					//
					if( ( is_array( $element )
					   && ( ( array_key_exists( $theKindOffset, $element )
						   && ($element[ $theKindOffset ] == (string) $theKind)
						   && array_key_exists( $theTypeOffset, $element )
						   && ($element[ $theTypeOffset ] == (string) $theType) )
						 || ( (! array_key_exists( $theKindOffset, $element ))
						   && ($theKind === NULL)
						   && array_key_exists( $theTypeOffset, $element )
						   && ($element[ $theTypeOffset ]
						   		== (string) $theType) ) ) )
					 || ( ($element instanceof ArrayObject)
					   && ( ( $element->offsetExists( $theKindOffset )
						   && ($element->offsetGet( $theKindOffset ) == (string) $theKind)
						   && $element->offsetExists( $theTypeOffset )
						   && ($element->offsetGet( $theTypeOffset ) == (string) $theType) )
						 || ( (! $element->offsetExists( $theKindOffset ))
						   && ($theKind === NULL)
						   && $element->offsetExists( $theTypeOffset )
						   && ($element->offsetGet( $theTypeOffset )
						   		== (string) $theType) ) ) ) )
					{
						$idx = $key;
						$save = $element[ $theDataOffset ];
						break;												// =>
					
					} // Matched.
				
				} // Iterating offset elements.
			
			} // Has data.
	
			//
			// Retrieve.
			//
			if( $theData === NULL )
				return $save;														// ==>
			
			//
			// Delete.
			//
			if( $theData === FALSE )
			{
				//
				// Handle existing list.
				//
				if( $idx !== NULL )
				{
					//
					// Delete item.
					//
					unset( $list[ $idx ] );
					
					//
					// Replace offset.
					//
					if( count( $list ) )
						$theReference[ $theMainOffset ] = array_values( $list );
					
					//
					// Delete offset.
					//
					else
					{
						if( is_array( $theReference ) )
							unset( $theReference[ $theMainOffset ] );
						else
							$theReference->offsetUnset( $theMainOffset );
					
					} // No elements left.
					
					if( $getOld )
						return $save;												// ==>
				}
				
				return NULL;														// ==>
			
			} // Delete.
			
			//
			// Create element.
			//
			$element = Array();
			if( $theKind !== NULL )
				$element[ $theKindOffset ] = $theKind;
			$element[ $theTypeOffset ] = $theType;
			$element[ $theDataOffset ] = $theData;
			
			//
			// Create first category.
			//
			if( $list === NULL )
			{
				//
				// Set offset.
				//
				$theReference[ $theMainOffset ] = array( $element );
				
				if( $getOld )
					return $save;													// ==>
				
				return $theData;													// ==>
			
			} // Missing main offset.
			
			//
			// Add new element.
			//
			if( $save === NULL )
				$list[] = $element;
			
			//
			// Update element.
			//
			else
				$list[ $idx ][ $theDataOffset ] = $theData;
			
			//
			// Set offset.
			//
			$theReference[ $theMainOffset ] = $list;
			
			if( $getOld )
				return $save;														// ==>
			
			return $theData;														// ==>
		
		} // Supported reference.

		throw new CException
				( "Unsupported object reference",
				  kERROR_UNSUPPORTED,
				  kMESSAGE_TYPE_ERROR,
				  array( 'Reference' => $theReference ) );						// !@! ==>
	
	} // ManageTypedKindOffset.

	 
	/*===================================================================================
	 *	ManageObjectList																*
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
	 *			representing the object's {@link kTAG_LID identifier}.
	 *	 </ul>
	 *		or:
	 *	<li><i>Array</i>: A structure composed of two items:
	 *	 <ul>
	 *		<li><i>Kind</i>: This offset represents the type or predicate of the reference.
	 *		<li><i>Data</i>: This offset represents the actual object or object reference.
	 *	 </ul>
	 * </ul>
	 *
	 * The reference list is numerically indexed array and this method will ensure it
	 * remains so.
	 *
	 * The method accepts the following parameters:
	 *
	 * <ul>
	 *	<li><b>&$theReference</b>: Reference to an array or ArrayObject derived instance.
	 *	<li><b>$theMainOffset</b>: The offset to manage.
	 *	<li><b>$theTypeOffset</b>: The element's offset of the type or predicate.
	 *	<li><b>$theDataOffset</b>: The element's offset of the data.
	 *	<li><b>$theValue</b>: This parameter represents either the search key in the list
	 *		when retrieving or deleting, or the reference when replacing or adding. If you
	 *		provide an array, it means that the elements may have a kind offset and that the
	 *		reference or object must be found in the data offset. When matching, if the kind
	 *		offset is not provided, it means that only those elements that do not have a
	 *		kind offset will be selected for matching. If the types match, the method will
	 *		use the {@link CPersistentUnitObject::ObjectIdentifier() ObjectIdentifier}
	 *		method to match the references, please refer to its documentation for more
	 *		information. If the provided value is not an array, it means that the reference
	 *		list does not feature types, so matches will only be performed on the reference.
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
	 * The {@link CPersistentUnitObject::ObjectIdentifier() method} used to match the list
	 * elements expects {@link kTAG_LID identifiers} in the references or objects, if these
	 * are not there, there is no way to discern duplicates.
	 *
	 * @param reference			   &$theReference		Object reference.
	 * @param string				$theMainOffset		Main offset.
	 * @param string				$theTypeOffset		Type offset.
	 * @param string				$theDataOffset		Data offset.
	 * @param mixed					$theValue			Reference or instance.
	 * @param mixed					$theOperation		Operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access protected
	 * @return mixed
	 */
	static function ManageObjectList( &$theReference,
									   $theMainOffset, $theTypeOffset, $theDataOffset,
									   $theValue, $theOperation = NULL,
									   $getOld = FALSE )
	{
		//
		// Check offset.
		//
		if( $theMainOffset === NULL )
			throw new CException
					( "Invalid offset",
					  kERROR_INVALID_PARAMETER,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Offset' => $theMainOffset ) );					// !@! ==>
		
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
		 && (! array_key_exists( $theDataOffset, $theValue )) )
		{
			//
			// Iterate arguments.
			//
			$result = Array();
			foreach( $theValue as $value )
				$result[]
					= self::ManageObjectList
						( $theReference,
						  $theMainOffset, $theTypeOffset, $theDataOffset,
						  $value, $theOperation, $getOld );
			
			return $result;															// ==>
		
		} // Execute list.
		
		//
		// Get typed reference matchers.
		//
		if( ( is_array( $theValue )
		   || ($theValue instanceof ArrayObject) )
		 && isset( $theValue[ $theDataOffset ] ) )
		{
			//
			// Set match type.
			//
			$type = ( array_key_exists( $theTypeOffset, (array) $theValue ) )
				  ? CPersistentUnitObject::ObjectIdentifier( $theValue[ $theTypeOffset ] )
				  : NULL;
			
			//
			// Set identifier.
			//
			$ident = CPersistentUnitObject::ObjectIdentifier( $theValue[ $theDataOffset ] );
		
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
			$ident = CPersistentUnitObject::ObjectIdentifier( $theValue );
		
		} // Reference matcher.
		
		//
		// Save current offset.
		//
		$save = ( array_key_exists( $theMainOffset, (array ) $theReference ) )
			  ? $theReference[ $theMainOffset ]
			  : NULL;
		
		//
		// RETRIEVE.
		//
		if( $theOperation === NULL )
		{
			//
			// Check list.
			//
			if( $save !== NULL )
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
						if( $ident
							== (string)
								CPersistentUnitObject::ObjectIdentifier( $value ) )
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
						 && array_key_exists( $theDataOffset, (array) $value ) )
						{
							//
							// Match type.
							//
							if( ($type !== NULL)
							 && array_key_exists( $theTypeOffset, (array) $value )
							 && ($type
							 	== (string)
							 		CPersistentUnitObject::ObjectIdentifier
							 			( $value[ $theTypeOffset ] )) )
							{
								//
								// Match identifier.
								//
								if( $ident
									== (string)
										CPersistentUnitObject::ObjectIdentifier
											( $value[ $theDataOffset ] ) )
									return $value;									// ==>
							
							} // Matched type.
							
							//
							// Match missing type.
							//
							elseif( ($type === NULL)
								 && (! array_key_exists( $theTypeOffset, (array) $value )) )
							{
								//
								// Match identifier.
								//
								if( $ident
									== (string)
										CPersistentUnitObject::ObjectIdentifier
											( $value[ $theDataOffset ] ) )
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
			if( $save !== NULL )
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
						if( $ident
							== (string)
								CPersistentUnitObject::ObjectIdentifier
									( $value ) )
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
						 && array_key_exists( $theDataOffset, (array) $value ) )
						{
							//
							// Match type.
							//
							if( ($type !== NULL)
							 && array_key_exists( $theTypeOffset, (array) $value )
							 && ($type
							 	== (string)
							 		CPersistentUnitObject::ObjectIdentifier
							 			( $value[ $theTypeOffset ] )) )
							{
								//
								// Match identifier.
								//
								if( $ident
								 	== (string)
								 		CPersistentUnitObject::ObjectIdentifier
											( $value[ $theDataOffset ] ) )
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
								 && (! array_key_exists( $theTypeOffset, (array) $value )) )
							{
								//
								// Match identifier.
								//
								if( $ident
								 	== (string)
								 		CPersistentUnitObject::ObjectIdentifier
											( $value[ $theDataOffset ] ) )
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
						$theReference->offsetUnset( $theMainOffset );
					
					//
					// Replace offset.
					//
					else
						$theReference->offsetSet( $theMainOffset, $new );
				
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
		if( $save !== NULL )
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
					if( $ident
						== (string)
							CPersistentUnitObject::ObjectIdentifier
								( $value ) )
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
					 && array_key_exists( $theDataOffset, (array) $value ) )
					{
						//
						// Match type.
						//
						if( ($type !== NULL)
						 && array_key_exists( $theTypeOffset, (array) $value )
						 && ($type
						 	== (string)
						 		CPersistentUnitObject::ObjectIdentifier
						 			( $value[ $theTypeOffset ] )) )
						{
							//
							// Match identifier.
							//
							if( $ident
								== (string)
									CPersistentUnitObject::ObjectIdentifier
										( $value[ $theDataOffset ] ) )
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
							 && (! array_key_exists( $theTypeOffset, (array) $value )) )
						{
							//
							// Match identifier.
							//
							if( $ident
								== (string)
									CPersistentUnitObject::ObjectIdentifier
										( $value[ $theDataOffset ] ) )
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
		$theReference->offsetSet( $theMainOffset, $save );
		
		if( $getOld )
			return $found;															// ==>
		
		return $theValue;															// ==>
	
	} // ManageObjectList.

		

/*=======================================================================================
 *																						*
 *									STATIC CLOSURE INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	HashClosure																		*
	 *==================================================================================*/

	/**
	 * Hash closure.
	 *
	 * This static method returns an anonymous function that is used as the default hashing
	 * closure function for list elements, the method returns a closure that can be used to
	 * hash a scalar or an array element depending on the provided parameters:
	 *
	 * <ul>
	 *	<li><b>$theElement</b>: This parameter contains the element to be hashed.
	 *	<li><b>$theOffset</b>: If provided, it implies that the previous parameter is an
	 *		array: the string represents the offset to the element that will be used for
	 *		hashing; if this parameter is provided and the previous parameter is neither an
	 *		array or an ArrayObject, the function will raise an exception.
	 * </ul>
	 *
	 * The function will hash the referenced value using the MD5 algorithm and return the
	 * hashed binary string.
	 *
	 * @static
	 * @return closure
	 */
	static function HashClosure()
	{
		return function( $theElement, $theOffset = NULL )
		{
			//
			// Handle offset.
			//
			if( $theOffset !== NULL )
			{
				//
				// Check element.
				//
				if( is_array( $theElement )
				 || ($theElement instanceof ArrayObject) )
				{
					//
					// Set element.
					//
					$theElement = ( isset( $theElement[ $theOffset ] ) )
								? $theElement[ $theOffset ]
								: NULL;
				
				} // Element is a list.
				
				else
					throw new CException
							( "Cannot use offset on element",
							  kERROR_INVALID_PARAMETER,
							  kMESSAGE_TYPE_ERROR,
							  array( 'Offset' => $theOffset,
									 'Element' => $theElement ) );				// !@! ==>
			
			} // Provided offset.
			
			return md5( $theElement, TRUE );										// ==>

		};																			// ==>

	} // HashClosure.

	 
	/*===================================================================================
	 *	DoubleHashClosure																*
	 *==================================================================================*/

	/**
	 * Double hash closure.
	 *
	 * This static method returns an anonymous function that is used as the default hashing
	 * closure function for list elements that feature a kind and type item, the method
	 * returns a closure that can be used to hash a scalar or an array element depending on
	 * the provided parameters:
	 *
	 * <ul>
	 *	<li><b>$theElement</b>: This parameter contains the element to be hashed.
	 *	<li><b>$theKindOffset</b>: If provided, it implies that the previous parameter is an
	 *		array: the string represents the offset to the element that represents the kind
	 *		value; if this parameter is provided and the previous parameter is neither an
	 *		array or an ArrayObject, the function will raise an exception.
	 *	<li><b>$theTypeOffset</b>: If provided, it implies that the previous parameter is an
	 *		array: the string represents the offset to the element that represents the
	 *		element's data item's type; if this parameter is provided and the first
	 *		parameter is neither an array or an ArrayObject, the function will raise an
	 *		exception.
	 * </ul>
	 *
	 * The function will concatenate the element's values referenced by the two provided
	 * offsets separating them by the {@link kTOKEN_INDEX_SEPARATOR kTOKEN_INDEX_SEPARATOR}
	 * token and will return the MD5 binary string hash of that sequence.
	 *
	 * @static
	 * @return closure
	 */
	static function DoubleHashClosure()
	{
		return function( $theElement, $theKindOffset = NULL, $theTypeOffset = NULL )
		{
			//
			// Handle offsets.
			//
			if( ($theKindOffset !== NULL)
			 || ($theTypeOffset !== NULL) )
			{
				//
				// Check element.
				//
				if( is_array( $theElement )
				 || ($theElement instanceof ArrayObject) )
				{
					//
					// Handle kind.
					//
					$sequence = ( isset( $theElement[ $theKindOffset ] ) )
							  ? md5( $theElement[ $theKindOffset ], TRUE )
							  : md5( NULL, TRUE );
					
					//
					// Separate.
					//
					$sequence .= kTOKEN_INDEX_SEPARATOR;
					
					//
					// Handle kind.
					//
					$sequence .= ( ( isset( $theElement[ $theTypeOffset ] ) )
								   ? md5( $theElement[ $theTypeOffset ], TRUE )
								   : md5( NULL, TRUE ) );
					
					return $sequence;												// ==>
					
				} // Element is a list.
				
				else
					throw new CException
							( "Cannot use offset on element",
							  kERROR_INVALID_PARAMETER,
							  kMESSAGE_TYPE_ERROR,
							  array( 'Offset' => $theOffset,
									 'Element' => $theElement ) );				// !@! ==>
			
			} // Provided offsets.
			
			return md5( $theElement, TRUE );										// ==>

		};																			// ==>

	} // DoubleHashClosure.

	 

} // class CAttribute.


?>
