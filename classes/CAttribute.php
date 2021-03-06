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
 * Tokens.
 *
 * This include file contains all token definitions.
 */
require_once( kPATH_LIBRARY_DEFINES."Tokens.inc.php" );

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
	 *			determine if the element is new or not.
	 *	 </ul>
	 *	<li><b>$theOperation</b>: This parameter represents the operation to be performed
	 *		whose scope depends on the value of the previous parameter:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the element or full list.
	 *		<li><i>FALSE</i>: Delete the element or full list.
	 *		<li><i>array</i>: This type is only considered if the <i>$theValue</i> parameter
	 *			is provided as an array: the method will be called for each element of the
	 *			<i>$theValue</i> parameter matched with the corresponding element of this
	 *			parameter, which also means that both both parameters must share the same
	 *			count.
	 *		<li><i>other</i>: Add the <i>$theValue</i> value to the list. If you provided
	 *			<i>NULL</i> in the previous parameter, the operation will be reset to
	 *			<i>NULL</i>.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value <i>before</i> it was eventually modified.
	 *		<li><i>FALSE</i>: Return the value <i>after</i> it was eventually modified.
	 *	 </ul>
	 *	<li><b>$theClosure</b>: The hashing closure, the function should return a value
	 *		representing the element's key. If omitted or <i>NULL</i>, the
	 *		{@link HashClosure() default} closure will be used. If the <i>$theValue</i>
	 *		parameter was provided as an array, you can provide an array of closures each
	 *		applying to the corresponding element of <i>$theValue</i> list.
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
	 * @uses ManageOffset()
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
				return self::ManageOffset( $theReference,
										   $theOffset, $theOperation,
										   $getOld );								// ==>
			
			} // Manage full list.
			
			//
			// Save current list.
			//
			$list = ( isset( $theReference[ $theOffset ] ) )
				  ? $theReference[ $theOffset ]
				  : NULL;
			
			//
			// Init match.
			//
			$idx = $save = NULL;
			
			//
			// Match element.
			//
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
						// Save value.
						//
						$save = $value;
						
						break;												// =>
					
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
				// Replace element.
				//
				if( $idx !== NULL )
					$list[ $idx ] = $theValue;
				
				//
				// Append new element.
				//
				else
					$list[] = $theValue;
			
			} // Had values.
			
			//
			// Create list.
			//
			else
				$list = array( $theValue );
			
			//
			// Update offset.
			//
			$theReference[ $theOffset ] = $list;
			
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
	 * A typed offset is a list element that has at least an item containing the element
	 * data and a variable number of other items that determine the type or kind of the
	 * element: these latter items also determine the element's key and are used to
	 * retrieve, delete and create elements, no two elements may share the same combination
	 * of these items. Elements of this type must be arrays, ArrayObject instances will be
	 * converted to arrays.
	 *
	 * The method accepts the following parameters:
	 *
	 * <ul>
	 *	<li><b>&$theReference</b>: The reference to the attributes container, the object or
	 *		array that holds the list of elements. The reference must point to an array or
	 *		to an ArrayObject instance, any other type will trigger an exception.
	 *	<li><b>$theMainOffset</b>: The offset, in the previous parameter, to the list of
	 *		elements to be managed, this referenced element is expected to be an array, if
	 *		this is not the case, the method will raise an exception.
	 *	<li><b>$theDataOffset</b>: The offset to the element's data item as a string. If you
	 *		provide an array, it means that you want to perform a series of operations, in
	 *		other words, this method will be called once for each element of this array and
	 *		the combination of parameters will depend on how the other arguments will have
	 *		been provided. Note that an ArrayObject is not considered as a list in this
	 *		case.
	 *	<li><b>$theTypeOffsets</b>: This parameter represents the offsets of the element
	 *		items that represent the element's kind, type or key. In general you should
	 *		provide an array of strings, if you provide a scalar, this will become an array
	 *		of one element. If the previous parameter was provided as an array, it means
	 *		that this parameter may take three forms:
	 *	 <ul>
	 *		<li><i>scalar</i>: The single offset will be used for each element provided in
	 *			the previous parameter. Note that an ArrayObject instance is also considered
	 *			a scalar.
	 *		<li><i>list</i>: If you provide a list of strings, this list will be used for
	 *			each element provided in the previous parameter.
	 *		<li><i>matrix</i>: If you provide a list of arrays, each element of the previous
	 *			parameter will be matched with the corresponding array in this list, which
	 *			also means that in this case the count of both parameters must be the same.
	 *	 </ul>
	 *	<li><b>$theTypeValues</b>: This parameter represents the the element kind, type or
	 *		key values, this parameter is matched against the previous parameter to
	 *		constitute the significant items of the element, meaning that each element of
	 *		the previous parameter represents the offset referencing the corresponding
	 *		element in this parameter.
	 *	<li><b>$theData</b>: This parameter represents the element's data value or the
	 *		operation to be performed:
	 *	 <ul>
	 *		<li><i>array</i>: An array is considered in two different ways, depending on the
	 *			type of the <i>$theDataOffset</i> parameter:
	 *		 <ul>
	 *			<li><i>scalar</i>: In this case the array is considered the data value.
	 *			<li><i>array</i>: In this case the array is considered as the list of values
	 *				to be managed: each element of this array will be identified by the
	 *				corresponding element of the <i>$theDataOffset</i> array parameter.
	 *		 </ul>
	 *		<li><i>NULL</i>: Retrieve the element's data item matching the provided types.
	 *		<li><i>FALSE</i>: Delete the element's data item matching the provided types.
	 *		<li><i>other</i>: Add or replace the element's data item matching the provided
	 *			types with the current value.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the data item <i>before</i> it was eventually modified.
	 *		<li><i>FALSE</i>: Return the data item <i>after</i> it was eventually modified.
	 *	 </ul>
	 *	<li><b>$theClosure</b>: The hashing closure, if omitted, the
	 *		{@link HashClosure() default} closure function will be used, for more details on
	 *		how to create a custom closure please see the {@link HashClosure() HashClosure}
	 *		reference in this class. If the <i>$theDataOffset</i> was provided as an array,
	 *		this parameter may also be provided as a list of closures matching the
	 *		<i>$theDataOffset</i> parameter count.
	 * </ul>
	 *
	 * @param reference			   &$theReference		Container reference.
	 * @param string				$theMainOffset		Attribute offset.
	 * @param string				$theDataOffset		Element data item's offset.
	 * @param array					$theTypeOffsets		List of type offsets.
	 * @param array					$theTypeValues		List of type values.
	 * @param mixed					$theData			Data item value or operation.
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
									    $theMainOffset, $theDataOffset,
									    $theTypeOffsets, $theTypeValues, $theData = NULL,
										$getOld = FALSE, $theClosure = NULL )
	{
		//
		// Check reference.
		//
		if( is_array( $theReference )
		 || ($theReference instanceof ArrayObject) )
		{
			//
			// Normalise type offsets.
			//
			if( ! is_array( $theTypeOffsets ) )
				$theTypeOffsets = ( is_array( $theDataOffset ) )
								? array_fill( 0,
											  count( $theDataOffset ),
											  array( $theTypeOffsets ) )
								: array( $theTypeOffsets );
			
			//
			// At least one offset.
			//
			if( ! count( $theTypeOffsets ) )
				throw new CException
						( "You must provide at least one type offset",
						  kERROR_INVALID_PARAMETER,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Offsets' => $theTypeOffsets ) );				// !@! ==>
				
			//
			// Normalise type values.
			//
			if( ! is_array( $theTypeValues ) )
				$theTypeValues = ( is_array( $theDataOffset ) )
								? array_fill( 0, count( $theDataOffset ), $theTypeValues )
								: array_fill( 0, count( $theTypeOffsets ), $theTypeValues );
			
			//
			// Check type values count.
			//
			if( count( $theTypeOffsets ) != count( $theTypeValues ) )
				throw new CException
						( "Invalid type values parameter count",
						  kERROR_INVALID_PARAMETER,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Offsets' => $theTypeOffsets,
								 'Values' => $theTypeValues ) );				// !@! ==>
			
			//
			// Handle multiple operations.
			//
			if( is_array( $theDataOffset ) )
			{
				//
				// Check nested type offsets.
				//
				$nested_offsets = FALSE;
				if( is_array( reset( $theTypeOffsets ) ) )
				{
					//
					// Check offset counts.
					//
					if( count( $theDataOffset ) != count( $theTypeOffsets ) )
						throw new CException
								( "Invalid type offsets parameter count",
								  kERROR_INVALID_PARAMETER,
								  kMESSAGE_TYPE_ERROR,
								  array( 'Data' => $theDataOffset,
										 'Offsets' => $theTypeOffsets ) );		// !@! ==>
					
					//
					// Remember.
					//
					$nested_offsets = TRUE;
				
				} // Nested type offsets.
			
				//
				// Check values counts.
				//
				if( count( $theTypeOffsets ) != count( $theTypeValues ) )
					throw new CException
							( "Invalid type values parameter count",
							  kERROR_INVALID_PARAMETER,
							  kMESSAGE_TYPE_ERROR,
							  array( 'Offsets' => $theTypeOffsets,
									 'Values' => $theTypeValues ) );			// !@! ==>
			
				//
				// Check data counts.
				//
				if( is_array( $theData )
				 && (count( $theData ) != count( $theDataOffset )) )
					throw new CException
							( "Invalid data values parameter count",
							  kERROR_INVALID_PARAMETER,
							  kMESSAGE_TYPE_ERROR,
							  array( 'Offsets' => $theDataOffset,
									 'Data' => $theData ) );					// !@! ==>
			
				//
				// Check closure counts.
				//
				if( is_array( $theClosure )
				 && (count( $theClosure ) != count( $theDataOffset )) )
					throw new CException
							( "Invalid closures parameter count",
							  kERROR_INVALID_PARAMETER,
							  kMESSAGE_TYPE_ERROR,
							  array( 'Offsets' => $theDataOffset,
									 'Closures' => $theClosure ) );				// !@! ==>
				
				//
				// Init loop.
				//
				$result = Array();
				$flags = array_fill( 0, 4, NULL );
				
				//
				// Iterate data offsets.
				//
				foreach( $theDataOffset as $data_offset )
				{
					//
					// Get type offset.
					//
					if( $nested_offsets )
					{
						$type_offsets = ( $flags[ 0 ] )
									  ? next( $theTypeOffsets )
									  : reset( $theTypeOffsets );
						$flags[ 0 ] = TRUE;
					}
					else
						$type_offsets = $theTypeOffsets;

					//
					// Get type values.
					//
					if( $nested_offsets )
					{
						$type_values = ( $flags[ 1 ] )
									  ? next( $theTypeValues )
									  : reset( $theTypeValues );
						$flags[ 1 ] = TRUE;
					}
					else
						$type_values = $theTypeValues;
				
					//
					// Get data value or operation.
					//
					if( is_array( $theData ) )
					{
						$data = ( $flags[ 2 ] )
							  ? next( $theData )
							  : reset( $theData );
						$flags[ 2 ] = TRUE;
					}
					else
						$data = $theData;
				
					//
					// Get closure.
					//
					if( is_array( $theClosure ) )
					{
						$closure = ( $flags[ 3 ] )
								 ? next( $theClosure )
								 : reset( $theClosure );
						$flags[ 3 ] = TRUE;
					}
					else
						$closure = $theClosure;
					
					//
					// Recurse.
					//
					$result[]
						= self::ManageTypedOffset
							( $theReference,
							  $theMainOffset,
							  $data_offset, $type_offsets, $type_values, $data,
							  $getOld, $closure );
				
				} // Iterated data offsets.
				
				return $result;														// ==>
			
			} // Multiple operations.
			
			//
			// Resolve hashing closure.
			//
			if( $theClosure === NULL )
				$theClosure = self::HashClosure();

			//
			// Save offset.
			//
			$list = ( isset( $theReference[ $theMainOffset ] ) )
				  ? $theReference[ $theMainOffset ]
				  : NULL;
			
			//
			// Init matches.
			//
			$idx = $save = NULL;
			
			//
			// Match element.
			//
			if( is_array( $list )
			 || ($list instanceof ArrayObject) )
			{
				//
				// Set match reference.
				//
				$match = $theClosure( array_combine( $theTypeOffsets, $theTypeValues ),
									  $theTypeOffsets );
				
				//
				// Iterate existing elements.
				//
				foreach( $list as $key => $value )
				{
					//
					// Match.
					//
					if( $match == $theClosure( $value, $theTypeOffsets ) )
					{
						//
						// Check data offset.
						//
						if( ! isset( $value[ $theDataOffset ] ) )
							throw new CException
									( "Element is missing data offset",
									  kERROR_INVALID_STATE,
									  kMESSAGE_TYPE_ERROR,
									  array( 'Element' => $value,
											 'Offset' => $theDataOffset ) );	// !@! ==>
						
						//
						// Save index.
						//
						$idx = $key;
					
						//
						// Save value.
						//
						$save = $value;
						
						break;												// =>
					
					} // Matched.
				
				} // Iterating existing elements.
			
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
			if( $theData === NULL )
			{
				//
				// Return match.
				//
				if( $idx !== NULL )
					return $save[ $theDataOffset ];									// ==>
				
				return NULL;														// ==>
			
			} // Retrieve.
			
			//
			// Delete.
			//
			if( $theData === FALSE )
			{
				//
				// Handle data.
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
					
					if( $getOld )
						return $save[ $theDataOffset ];								// ==>
				
				} // Matched element.
				
				return NULL;														// ==>
			
			} // Delete.
			
			//
			// Replace element.
			//
			if( $idx !== NULL )
			{
				//
				// Save data.
				//
				if( $getOld )
					$save = $list[ $idx ][ $theDataOffset ];
				
				//
				// Update data.
				//
				$list[ $idx ][ $theDataOffset ] = $theData;
				
				//
				// Set offset.
				//
				$theReference[ $theMainOffset ] = $list;
				
				if( $getOld )
					return $save;													// ==>
				
				return $theData;													// ==>
			
			} // Matched element.
			
			//
			// Create element.
			//
			$element = array_combine( $theTypeOffsets, $theTypeValues );
			foreach( $theTypeOffsets as $offset )
			{
				if( $element[ $offset ] === NULL )
					unset( $element[ $offset ] );
			
			} $element[ $theDataOffset ] = $theData;
			
			//
			// Set first element.
			//
			if( $list === NULL )
				$list = array( $element );
			
			//
			// Add new element.
			//
			else
				$list[] = $element;
			
			//
			// Update offset.
			//
			$theReference[ $theMainOffset ] = $list;
			
			if( $getOld )
				return NULL;														// ==>
			
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
	 * The structures managed by this method are equivalent to the structures managed by the
	 * {@link ManageTypedOffset() ManageTypedOffset} method, except that the data item, in
	 * this case, is an array and the goal of this method is to manage the data item array
	 * elements rather than the data item itself.
	 *
	 * This method features many of the same parameters than the
	 * {@link ManageTypedOffset() ManageTypedOffset} method except for a couple of changes.
	 *
	 * Here is the list of arguments:
	 *
	 * <ul>
	 *	<li><b>&$theReference</b>: The reference to the attributes container, the object or
	 *		array that holds the list of elements. The reference must point to an array or
	 *		to an ArrayObject instance, any other type will trigger an exception.
	 *	<li><b>$theMainOffset</b>: The offset, in the previous parameter, to the list of
	 *		elements to be managed, this referenced element is expected to be an array, if
	 *		this is not the case, the method will raise an exception.
	 *	<li><b>$theDataOffset</b>: The offset to the element's data item as a string. If you
	 *		provide an array, it means that you want to perform a series of operations, in
	 *		other words, this method will be called once for each element of this array and
	 *		the combination of parameters will depend on how the other arguments will have
	 *		been provided. Note that an ArrayObject is not considered as a list in this
	 *		case.
	 *	<li><b>$theTypeOffsets</b>: This parameter represents the offsets of the element
	 *		items that represent the element's kind, type or key. In general you should
	 *		provide an array of strings, if you provide a scalar, this will become an array
	 *		of one element. If the previous parameter was provided as an array, it means
	 *		that this parameter may take three forms:
	 *	 <ul>
	 *		<li><i>scalar</i>: The single offset will be used for each element provided in
	 *			the previous parameter. Note that an ArrayObject instance is also considered
	 *			a scalar.
	 *		<li><i>list</i>: If you provide a list of strings, this list will be used for
	 *			each element provided in the previous parameter.
	 *		<li><i>matrix</i>: If you provide a list of arrays, each element of the previous
	 *			parameter will be matched with the corresponding array in this list, which
	 *			also means that in this case the count of both parameters must be the same.
	 *	 </ul>
	 *	<li><b>$theTypeValues</b>: This parameter represents the the element kind, type or
	 *		key values, this parameter is matched against the previous parameter to
	 *		constitute the significant items of the element, meaning that each element of
	 *		the previous parameter represents the offset referencing the corresponding
	 *		element in this parameter.
	 *	<li><b>$theData</b>: This parameter represents the element's data value or the the
	 *		data scope; this parameter is one of those that have a different usage than in
	 *		the {@link ManageTypedOffset() ManageTypedOffset} method:
	 *	 <ul>
	 *		<li><i>array</i>: An array is considered in two different ways, depending on the
	 *			type of the <i>$theDataOffset</i> parameter:
	 *		 <ul>
	 *			<li><i>scalar</i>: In this case the array is considered the data value.
	 *			<li><i>array</i>: In this case the array is considered as the list of values
	 *				to be managed: each element of this array will be identified by the
	 *				corresponding element of the <i>$theDataOffset</i> array parameter.
	 *		 </ul>
	 *		<li><i>NULL</i>: This indicates that the operation, provided in the next
	 *			parameter, applies to the whole data item list; this also means that if the
	 *			operation is <i>TRUE</i> we raise here an exception, because one cannot add
	 *			a <i>NULL</i> data item.
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
	 *		<li><i>array</i>: This type is only considered if the <i>$theDataOffset</i>
	 *			parameter is provided as an array: the method will be called for each
	 *			element of the <i>$theDataOffset</i> parameter matched with the
	 *			corresponding element of this parameter. This also implies that both
	 *			parameters must share the same count.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the data item <i>before</i> it was eventually modified.
	 *		<li><i>FALSE</i>: Return the data item <i>after</i> it was eventually modified.
	 *	 </ul>
	 *	<li><b>$theClosure</b>: The hashing closure, if omitted, the
	 *		{@link HashClosure() default} closure function will be used, for more details on
	 *		how to create a custom closure please see the {@link HashClosure() HashClosure}
	 *		reference in this class. If the <i>$theDataOffset</i> was provided as an array,
	 *		this parameter may also be provided as a list of closures matching the
	 *		<i>$theDataOffset</i> parameter count.
	 * </ul>
	 *
	 * @param reference			   &$theReference		Container reference.
	 * @param string				$theMainOffset		Attribute offset.
	 * @param string				$theDataOffset		Element data item's offset.
	 * @param array					$theTypeOffsets		List of type offsets.
	 * @param array					$theTypeValues		List of type values.
	 * @param mixed					$theData			Data item value or scope.
	 * @param mixed					$theOperation		Operation.
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
	 * @uses ManageTypedOffset()
	 */
	static function ManageTypedArrayOffset( &$theReference,
											 $theMainOffset, $theDataOffset,
											 $theTypeOffsets, $theTypeValues,
											 $theData = NULL, $theOperation = NULL,
											 $getOld = FALSE, $theClosure = NULL )
	{
		//
		// Check reference.
		//
		if( is_array( $theReference )
		 || ($theReference instanceof ArrayObject) )
		{
			//
			// Normalise type offsets.
			//
			if( $theTypeOffsets instanceof ArrayObject )
				$theTypeOffsets = $theTypeOffsets->getArrayCopy();
			elseif( ! is_array( $theTypeOffsets ) )
				$theTypeOffsets = ( is_array( $theDataOffset ) )
								? array_fill( 0,
											  count( $theDataOffset ),
											  array( $theTypeOffsets ) )
								: array( $theTypeOffsets );
			
			//
			// At least one offset.
			//
			if( ! count( $theTypeOffsets ) )
				throw new CException
						( "You must provide at least one type offset",
						  kERROR_INVALID_PARAMETER,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Offsets' => $theTypeOffsets ) );				// !@! ==>
				
			//
			// Normalise type values.
			//
			if( $theTypeValues instanceof ArrayObject )
				$theTypeValues = $theTypeValues->getArrayCopy();
			elseif( ! is_array( $theTypeValues ) )
				$theTypeValues = ( is_array( $theDataOffset ) )
								? array_fill( 0, count( $theDataOffset ), $theTypeValues )
								: array_fill( 0, count( $theTypeOffsets ), $theTypeValues );
			
			//
			// Check type values count.
			//
			if( count( $theTypeOffsets ) != count( $theTypeValues ) )
				throw new CException
						( "Invalid type values parameter count",
						  kERROR_INVALID_PARAMETER,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Offsets' => $theTypeOffsets,
								 'Values' => $theTypeValues ) );				// !@! ==>
			
			//
			// Handle multiple operations.
			//
			if( is_array( $theDataOffset ) )
			{
				//
				// Check nested type offsets.
				//
				$nested_offsets = FALSE;
				if( is_array( reset( $theTypeOffsets ) ) )
				{
					//
					// Check offset counts.
					//
					if( count( $theDataOffset ) != count( $theTypeOffsets ) )
						throw new CException
								( "Invalid type offsets parameter count",
								  kERROR_INVALID_PARAMETER,
								  kMESSAGE_TYPE_ERROR,
								  array( 'Data' => $theDataOffset,
										 'Offsets' => $theTypeOffsets ) );		// !@! ==>
					
					//
					// Remember.
					//
					$nested_offsets = TRUE;
				
				} // Nested type offsets.
			
				//
				// Check values counts.
				//
				if( count( $theTypeOffsets ) != count( $theTypeValues ) )
					throw new CException
							( "Invalid type values parameter count",
							  kERROR_INVALID_PARAMETER,
							  kMESSAGE_TYPE_ERROR,
							  array( 'Offsets' => $theTypeOffsets,
									 'Values' => $theTypeValues ) );			// !@! ==>
			
				//
				// Check data counts.
				//
				if( is_array( $theData )
				 && (count( $theData ) != count( $theDataOffset )) )
					throw new CException
							( "Invalid data values parameter count",
							  kERROR_INVALID_PARAMETER,
							  kMESSAGE_TYPE_ERROR,
							  array( 'Offsets' => $theDataOffset,
									 'Data' => $theData ) );					// !@! ==>
			
				//
				// Check operation counts.
				//
				if( is_array( $theOperation )
				 && (count( $theOperation ) != count( $theDataOffset )) )
					throw new CException
							( "Invalid operation values parameter count",
							  kERROR_INVALID_PARAMETER,
							  kMESSAGE_TYPE_ERROR,
							  array( 'Offsets' => $theDataOffset,
									 'Operations' => $theOperation ) );			// !@! ==>
			
				//
				// Check closure counts.
				//
				if( is_array( $theClosure )
				 && (count( $theClosure ) != count( $theDataOffset )) )
					throw new CException
							( "Invalid closures parameter count",
							  kERROR_INVALID_PARAMETER,
							  kMESSAGE_TYPE_ERROR,
							  array( 'Offsets' => $theDataOffset,
									 'Closures' => $theClosure ) );				// !@! ==>
				
				//
				// Init loop.
				//
				$result = Array();
				$flags = array_fill( 0, 5, NULL );
				
				//
				// Iterate data offsets.
				//
				foreach( $theDataOffset as $data_offset )
				{
					//
					// Get type offset.
					//
					if( $nested_offsets )
					{
						$type_offsets = ( $flags[ 0 ] )
									  ? next( $theTypeOffsets )
									  : reset( $theTypeOffsets );
						$flags[ 0 ] = TRUE;
					}
					else
						$type_offsets = $theTypeOffsets;

					//
					// Get type values.
					//
					if( $nested_offsets )
					{
						$type_values = ( $flags[ 1 ] )
									  ? next( $theTypeValues )
									  : reset( $theTypeValues );
						$flags[ 1 ] = TRUE;
					}
					else
						$type_values = $theTypeValues;
				
					//
					// Get data value or scope.
					//
					if( is_array( $theData ) )
					{
						$data = ( $flags[ 2 ] )
							  ? next( $theData )
							  : reset( $theData );
						$flags[ 2 ] = TRUE;
					}
					else
						$data = $theData;
				
					//
					// Get operation.
					//
					if( is_array( $theOperation ) )
					{
						$operation = ( $flags[ 3 ] )
								   ? next( $theOperation )
								   : reset( $theOperation );
						$flags[ 3 ] = TRUE;
					}
					else
						$operation = $theOperation;
				
					//
					// Get closure.
					//
					if( is_array( $theClosure ) )
					{
						$closure = ( $flags[ 4 ] )
								 ? next( $theClosure )
								 : reset( $theClosure );
						$flags[ 4 ] = TRUE;
					}
					else
						$closure = $theClosure;
					
					//
					// Recurse.
					//
					$result[]
						= self::ManageTypedArrayOffset
							( $theReference,
							  $theMainOffset,
							  $data_offset, $type_offsets, $type_values, $data,
							  $operation, $getOld, $closure );
				
				} // Iterated data offsets.
				
				return $result;														// ==>
			
			} // Multiple operations.
			
			//
			// Resolve hashing closure.
			//
			if( $theClosure === NULL )
				$theClosure = self::HashClosure();
			
			//
			// Handle all data item elements.
			//
			if( $theData === NULL )
			{
				//
				// Handle impossible operation.
				//
				if( $theOperation )
					throw new CException
							( "Invalid data and operation parameters combination",
							  kERROR_INVALID_PARAMETER,
							  kMESSAGE_TYPE_ERROR,
							  array( 'Data' => $theData,
									 'Operation' => $theOperation ) );			// !@! ==>
				
				return self::ManageTypedOffset(
							$theReference,
							$theMainOffset, $theDataOffset,
							$theTypeOffsets, $theTypeValues,
							$theOperation, $getOld, $theClosure );					// ==>
			
			} // Data item element not provided.

			//
			// Save offset.
			//
			$list = ( isset( $theReference[ $theMainOffset ] ) )
				  ? $theReference[ $theMainOffset ]
				  : NULL;
			
			//
			// Init matches.
			//
			$idx = $save = NULL;
			
			//
			// Match element.
			//
			if( is_array( $list )
			 || ($list instanceof ArrayObject) )
			{
				//
				// Set match reference.
				//
				$match = $theClosure( array_combine( $theTypeOffsets, $theTypeValues ),
									  $theTypeOffsets );
				
				//
				// Match types block.
				//
				foreach( $list as $key => $value )
				{
					//
					// Match.
					//
					if( $match == $theClosure( $value, $theTypeOffsets ) )
					{
						//
						// Check data offset.
						//
						if( ! isset( $value[ $theDataOffset ] ) )
							throw new CException
									( "Element is missing data offset",
									  kERROR_INVALID_STATE,
									  kMESSAGE_TYPE_ERROR,
									  array( 'Element' => $value,
											 'Offset' => $theDataOffset ) );	// !@! ==>
						
						//
						// Save index.
						//
						$idx = $key;
					
						//
						// Save value.
						//
						$save = $value;
						
						break;												// =>
					
					} // Matched.
				
				} // Iterating existing elements.
			
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
					return self::ManageArrayOffset(
									$save,
									$theDataOffset, $theData, $theOperation,
									$getOld, $theClosure );							// ==>
				
				return NULL;														// ==>
			
			} // Retrieve.
			
			//
			// Delete.
			//
			if( $theOperation === FALSE )
			{
				//
				// Matched element.
				//
				if( $idx !== NULL )
				{
					//
					// Delete data item element.
					//
					$result = self::ManageArrayOffset(
									$save,
									$theDataOffset, $theData, $theOperation,
									TRUE, $theClosure );
					
					//
					// Update data item.
					//
					if( isset( $save[ $theDataOffset ] ) )
						$list[ $idx ] = $save;
					
					//
					// Delete element.
					//
					else
						unset( $list[ $idx ] );
					
					//
					// Update list.
					//
					if( count( $list ) )
						$theReference[ $theMainOffset ] = array_values( $list );
					
					//
					// Delete element.
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
					
					if( $getOld )
						return $result;												// ==>
				
				} // Matched element.
				
				return NULL;														// ==>
			
			} // Delete.
			
			//
			// Matched element.
			//
			if( $idx !== NULL )
			{
				//
				// Add or replace data item element.
				//
				$result = self::ManageArrayOffset(
								$save,
								$theDataOffset, $theData, $theOperation,
								$getOld, $theClosure );
				
				//
				// Update list.
				//
				$list[ $idx ] = $save;
			
			} // Matched element.
			
			//
			// New element.
			//
			else
			{
				//
				// Init element.
				//
				$save = array_combine( $theTypeOffsets, $theTypeValues );
				foreach( $theTypeOffsets as $offset )
				{
					if( $save[ $offset ] === NULL )
						unset( $save[ $offset ] );
				}
				
				//
				// Add data.
				//
				$save[ $theDataOffset ] = ( is_array( $theData ) )
										? $theData
										: array( $theData );
				
				//
				// First element.
				//
				if( $list === NULL )
					$list = array( $save );
				
				//
				// New element.
				//
				else
					$list[] = $save;
				
				//
				// Set result.
				//
				$result = ( $getOld )
						? NULL
						: $theData;
			
			} // New element.
			
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
	 *
	 * @throws {@link CException CException}
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
	 * This static method returns the default hashing closure that will be used to compare
	 * list elements. This method will return a closure function that expects two arguments:
	 *
	 * <ul>
	 *	<li><b>$theElement</b>: This parameter represents the element to be hashed, it can
	 *		take two forms depending on the presence or not of the next parameter:
	 *	 <ul>
	 *		<li><i>Next parameter is not there</i>: If you omit the next parameter, it means
	 *			that the element belongs to a list of scalars and that it is the element
	 *			itself to represent the key. In this case, the function will return the MD5
	 *			hash of the element itself.
	 *		<li><i>Next parameter is there</i>: If you provide the next parameter, it means
	 *			that the provided offset or offsets point to the key data items contained in
	 *			this parameter: in this case this parameter must be either an array or an
	 *			ArrayObject, if you provide anything else, an exception will be raised. The
	 *			hash will be generated by an MD5 hash of the following sequence:
	 *			[<i>Offset</i>]
	 *			{@link kTOKEN_NAMESPACE_SEPARATOR kTOKEN_NAMESPACE_SEPARATOR}
	 *			[<i>MD5( Value )</i>], in which the value is the value referenced by the
	 *			offset or <i>NULL</i>; if there are more than one offset, these blocks will
	 *			be separated by the {@link kTOKEN_INDEX_SEPARATOR kTOKEN_INDEX_SEPARATOR}
	 *			token, the function will return the MD5 of the full sequence.
	 *	 </ul>
	 *	<li><b>$theOffsets</b>: If provided, it must be an array of strings that represent
	 *		offsets to items in the previous parameter which constitute the element's key.
	 *		Note that these offsets will be sorted prior to being used, to allow consistent
	 *		results.
	 * </ul>
	 *
	 * When creating your custom closures, you should use this function as a model and be
	 * aware of the following rule: the static methods in this class invoke a single hash
	 * closure, but they might use this closure on both lists and matrices, so you should
	 * design the function as two separate functions depending on the presence or not of
	 * the offsets list.
	 *
	 * @static
	 * @return closure
	 */
	static function HashClosure()
	{
		return function( $theElement, $theOffsets = NULL )
		{
			//
			// Handle scalar.
			//
			if( $theOffsets === NULL )
				return md5( $theElement, TRUE );									// ==>
			
			//
			// Check element.
			//
			if( is_array( $theElement )
			 || ($theElement instanceof ArrayObject) )
			{
				//
				// Init local storage.
				//
				$sequence = Array();
				
				//
				// Iterate offsets.
				//
				foreach( $theOffsets as $offset )
				{
					//
					// Init subsequence.
					//
					$subsequence = $offset.kTOKEN_NAMESPACE_SEPARATOR;
					
					//
					// Hash item.
					//
					if( isset( $theElement[ $offset ] ) )
						$subsequence .= md5( $theElement[ $offset ], TRUE );
					
					//
					// Hash missing item.
					//
					else
						$subsequence .= md5( NULL, TRUE );
					
					//
					// Add to sequence.
					//
					$sequence[] = $subsequence;
				
				} // Iterating offsets.
				
				return md5( implode( kTOKEN_INDEX_SEPARATOR, $sequence ), TRUE );	// ==>
			
			} // Valid element type.
			
			throw new CException
					( "Invalid element type",
					  kERROR_INVALID_PARAMETER,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Element' => $theElement,
					  		 'Offsets' => $theOffsets ) );						// !@! ==>

		};																			// ==>

	} // HashClosure.

	 
	/*===================================================================================
	 *	HashObjectClosure																*
	 *==================================================================================*/

	/**
	 * Hash closure.
	 *
	 * This method is equivalent to the {@link HashClosure() HashClosure} method, except
	 * that it is tailored to operate on objects or object references.
	 *
	 * It uses the {@link CPersistentUnitObject::ObjectIdentifier() ObjectIdentifier} method
	 * in place of the MD5 hash, which also means that the provided parameters must be
	 * resolvable.
	 *
	 * @static
	 * @return closure
	 */
	static function HashObjectClosure()
	{
		return function( $theElement, $theOffsets = NULL )
		{
			//
			// Handle scalar.
			//
			if( $theOffsets === NULL )
				return CPersistentUnitObject::ObjectIdentifier( $theElement );		// ==>
			
			//
			// Check element.
			//
			if( is_array( $theElement )
			 || ($theElement instanceof ArrayObject) )
			{
				//
				// Init local storage.
				//
				$sequence = Array();
				
				//
				// Iterate offsets.
				//
				foreach( $theOffsets as $offset )
				{
					//
					// Init subsequence.
					//
					$subsequence = $offset.kTOKEN_NAMESPACE_SEPARATOR;
					
					//
					// Hash item.
					//
					if( isset( $theElement[ $offset ] ) )
						$subsequence
							.= CPersistentUnitObject::ObjectIdentifier
								( $theElement[ $offset ] );
					
					//
					// Hash missing item.
					//
					else
						$subsequence .= md5( NULL );
					
					//
					// Add to sequence.
					//
					$sequence[] = $subsequence;
				
				} // Iterating offsets.
				
				return CPersistentUnitObject::ObjectIdentifier
					( implode( kTOKEN_INDEX_SEPARATOR, $sequence ) );				// ==>
			
			} // Valid element type.
			
			throw new CException
					( "Invalid element type",
					  kERROR_INVALID_PARAMETER,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Element' => $theElement,
					  		 'Offsets' => $theOffsets ) );						// !@! ==>

		};																			// ==>

	} // HashObjectClosure.

	 

} // class CAttribute.


?>
