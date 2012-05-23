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
 * This include file contains all exception class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CException.php" );

/**
 *	Attribute class.
 *
 * This class is a static methods holder that manage {@link ArrayObject ArrayObject} derived
 * class attributes.
 *
 * The main function of this class is to concentrate all attribute management methods in a
 * static class which can be used for managing the attributes of any class derived from an
 * ArrayObject.
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
 *							STATIC LOW-LEVEL ATTRIBUTES INTERFACE						*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	ManageOffset																	*
	 *==================================================================================*/

	/**
	 * Manage a scalar offset.
	 *
	 * This method can be used to manage a scalar offset, this options involves setting,
	 * retrieving and deleting an offset of the provided array or ArrayObject.
	 *
	 * The method accepts the following parameters:
	 *
	 * <ul>
	 *	<li><b>&$theReference</b>: Reference to an array or ArrayObject derived instance.
	 *	<li><b>$theOffset</b>: The offset to manage.
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
	 * @param reference			   &$theReference		Object reference.
	 * @param string				$theOffset			Offset.
	 * @param mixed					$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @static
	 * @return mixed
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
			// Save current value.
			//
			if( is_array( $theReference ) )
				$save = ( array_key_exists( $theOffset, $theReference ) )
					  ? $theReference[ $theOffset ]
					  : NULL;
			else
				$save = ( $theReference->offsetExists( $theOffset ) )
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
	 * This method can be used to manage an array offset, this options involves setting,
	 * retrieving and deleting elements of an offset which contains an array of values, this
	 * method concentrates in managing the offset's elements, rather than
	 * {@link ManageOffset() managing} the offset itself.
	 *
	 * The offset's array should be composed by elements that <i>must</i> be convertable to
	 * strings: the string value represents the index of the element, which means that no
	 * two elements can have the same string value.
	 *
	 * When deleting elements, if the list becomes empty, the whole offset will be deleted.
	 *
	 * The method accepts the following parameters:
	 *
	 * <ul>
	 *	<li><b>&$theReference</b>: Reference to an array or ArrayObject derived instance.
	 *	<li><b>$theOffset</b>: The offset to manage.
	 *	<li><b>$theValue</b>: This parameter represents the data element to be set, or the
	 *		index to the data element to be deleted or retrieved:
	 *	 <ul>
	 *		<li><i>NULL</i>: This value indicates that we want to operate on all elements,
	 *			which means, depending on the next parameter, that we are either retrieving
	 *			or deleting the full list. If the operation parameter is <i>TRUE</i>, no
	 *			element will be added.
	 *		<li><i>array</i>: This value indicates that we want to operate on a list of
	 *			values: each of these values will be handled according to the operation
	 *			parameter. Note that an ArrayObject is not considered in this scenario, so
	 *			in that case you would have to convert it to an array.
	 *		<li><i>other</i>: Any other type represents either the new value to be added or
	 *			the index to the value to be returned or deleted. <i>It must be possible to
	 *			cast this value to a string, this is what will be used to compare
	 *			elements</i>.
	 *	 </ul>
	 *	<li><b>$theOperation</b>: This parameter represents the operation to be performed,
	 *		it will be evaluated as a boolean and its scope depends on the value of the
	 *		previous parameter:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the element or list.
	 *		<li><i>FALSE</i>: Delete the element or list.
	 *		<li><i>TRUE</i>: Add the element or list. If you provided <i>NULL</i> as value,
	 *			the operation will do nothing.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value <i>before</i> it was eventually modified.
	 *		<li><i>FALSE</i>: Return the value <i>after</i> it was eventually modified.
	 *	 </ul>
	 * </ul>
	 *
	 * @param reference			   &$theReference		Object reference.
	 * @param string				$theOffset			Offset.
	 * @param mixed					$theValue			Value to manage.
	 * @param mixed					$theOperation		Operation to perform.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @static
	 * @return mixed
	 */
	static function ManageArrayOffset( &$theReference, $theOffset, $theValue = NULL,
																   $theOperation = NULL,
																   $getOld = FALSE )
	{
		//
		// Check reference.
		//
		if( is_array( $theReference )
		 || ($theReference instanceof ArrayObject) )
		{
			//
			// Handle multiple parameters:
			//
			if( is_array( $theValue ) )
			{
				//
				// Init local storage.
				//
				$result = Array();
				
				//
				// Iterate values.
				//
				foreach( $theValue as $value )
					$result[]
						= self::ManageArrayOffset
							( $theReference, $theOffset, $value, $theOperation, $getOld );
				
				return $result;														// ==>
			
			} // Multiple parameters.
			
			//
			// Save current list.
			//
			if( is_array( $theReference ) )
				$list = ( array_key_exists( $theOffset, $theReference ) )
					  ? $theReference[ $theOffset ]
					  : NULL;
			else
				$list = ( $theReference->offsetExists( $theOffset ) )
					  ? $theReference[ $theOffset ]
					  : NULL;
			
			//
			// Index list.
			//
			if( ($list !== NULL)			// Has data
			 && ($theValue !== NULL) )		// and not deleting list.
			{
				//
				// Index list.
				//
				$match = Array();
				foreach( $list as $element )
					$match[ md5( $element, TRUE ) ]
						= $element;
				
				//
				// Save index.
				//
				$idx = md5( (string) $theValue, TRUE );
				
				//
				// Save match.
				//
				$save = ( array_key_exists( $idx, $match ) )
					  ? $match[ $idx ]
					  : NULL;
			
			} // Has data and operates on list.
			
			//
			// Return current value.
			//
			if( $theOperation === NULL )
			{
				//
				// Handle list.
				//
				if( $theValue === NULL )
					return $list;													// ==>
				
				//
				// Handle element.
				//
				if( $list !== NULL )
					return $save;													// ==>
				
				return NULL;														// ==>
			
			} // Return value.
			
			//
			// Delete data.
			//
			if( $theOperation === FALSE )
			{
				//
				// Delete list.
				//
				if( $theValue === NULL )
				{
					//
					// Handle list.
					//
					if( $list !== NULL )
					{
						//
						// Delete offset.
						//
						if( is_array( $theReference ) )
							unset( $theReference[ $theOffset ] );
						else
							$theReference->offsetUnset( $theOffset );
					
					} // Has data.
					
					if( $getOld )
						return $list;												// ==>
					
					return NULL;													// ==>
				
				} // Delete list.
				
				//
				// Delete element.
				//
				if( $save !== NULL )
				{
					//
					// Remove element.
					//
					unset( $match[ $idx ] );
					
					//
					// Update list.
					//
					if( count( $match ) )
						$theReference[ $theOffset ] = array_values( $match );
					
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
			// Skip operation.
			//
			if( $theValue === NULL )
			{
				if( $getOld )
					return $list;													// ==>
				
				return NULL;														// ==>
			
			} // Replace list.
			
			//
			// Add or replace element.
			//
			if( $list !== NULL )
			{
				//
				// Set element.
				//
				$match[ $idx ] = $theValue;
				
				//
				// Update offset.
				//
				$theReference[ $theOffset ] = array_values( $match );
			
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
	 * A typed offset is structured as follows:
	 *
	 * <ul>
	 *	<li><i>Type</i>: This offset contains a scalar which determined the type or category
	 *		of the element. This offset may be omitted.
	 *	<li><i>Data</i>: This offset contains the element data, in this method we treat it
	 *		as a scalar. This offset may not be omitted.
	 * </ul>
	 *
	 * No two elements may share the same type or category.
	 *
	 * The method accepts the following parameters:
	 *
	 * <ul>
	 *	<li><b>&$theReference</b>: Reference to an array or ArrayObject derived instance.
	 *	<li><b>$theMainOffset</b>: The offset to manage.
	 *	<li><b>$theTypeOffset</b>: The element's offset of the type or category.
	 *	<li><b>$theDataOffset</b>: The element's offset of the data.
	 *	<li><b>$theType</b>: This parameter represents the value of the index element of the
	 *		item, depending on the next parameter this value will be used for matching
	 *		items in the list:
	 *	 <ul>
	 *		<li><i>NULL</i>: An empty type means that we are looking for the item lacking
	 *			the index element.
	 *		<li><i>array</i>: If you provide an array, it means that you are operating on a
	 *			list of items: depending on the next parameter this will mean either
	 *			retrieving the data elements of the items matching the array, deleting these
	 *			items, or adding/replacing the items; in this lastcase, this means that the
	 *			next parameter must also be an array and that each of its elements will be
	 *			associated to the corresponding index element.
	 *		<li><i>other</i>: Any other value will be considered as the index to retrieve,
	 *			remove or add/replace. You <i>MUST</i> be able to cast this value to a
	 *			string.
	 *	 </ul>
	 *	<li><b>$theData</b>: This parameter represents the item's data element, or the
	 *		operation to be performed:
	 *	 <ul>
	 *		<li><i>NULL</i>: This indicates that we want to retrieve the data of the item
	 *			with index matching the previous parameter.
	 *		<li><i>FALSE</i>: This indicates that we want to remove the item matching the
	 *			index provided in the previous parameter.
	 *		<li><i>other</i>: Any other value indicates that we want to add or replace the
	 *			data element of the item matching the previous parameter. Note that if the
	 *			previous parameter is an array, this one must also be an array matching the
	 *			latter's count.
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
	 * The method will return the matched element's value of the data offset.
	 *
	 * @param reference			   &$theReference		Object reference.
	 * @param string				$theMainOffset		Main offset.
	 * @param string				$theTypeOffset		Type offset.
	 * @param string				$theDataOffset		Data offset.
	 * @param mixed					$theType			Type.
	 * @param mixed					$theData			Data or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @static
	 * @return mixed
	 */
	static function ManageTypedOffset( &$theReference,
										$theMainOffset, $theTypeOffset, $theDataOffset,
										$theType = NULL, $theData = NULL,
										$getOld = FALSE )
	{
		//
		// Check reference.
		//
		if( is_array( $theReference )
		 || ($theReference instanceof ArrayObject) )
		{
			//
			// Recursing workflow.
			//
			if( is_array( $theType ) )
			{
				//
				// Init operation flag.
				//
				$add = FALSE;
				
				//
				// Check add/replace operation.
				//
				if( ($theData !== NULL)
				 && ($theData !== FALSE) )
				{
					//
					// Check if data count matches types count.
					//
					if( (! is_array( $theData ))
					 || (count( $theData ) != count( $theType )) )
						throw new CException
								( "Type and data parameters do not match for set operation",
								  kERROR_INVALID_PARAMETER,
								  kMESSAGE_TYPE_ERROR,
								  array( 'Types offset' => $theTypeOffset,
										 'Type' => $theType,
										 'Data offset' => $theDataOffset,
										 'Data' => $theData ) );				// !@! ==>
					
					//
					// Set operation flag.
					//
					$add = TRUE;
				
				} // Add/replace.
				
				//
				// Init result.
				//
				$result = Array();
				
				//
				// Iterate types.
				//
				$count = count( $theType );
				$type = reset( $theType );
				$data = ( $add ) ? reset( $theData ) : $theData;
				while( $count-- )
				{
					//
					// Recurse.
					//
					$result[]
						= CAttribute::ManageTypedOffset
							( $theReference,
							  $theMainOffset, $theTypeOffset, $theDataOffset,
							  $type, $data, $getOld );
					
					//
					// Advance.
					//
					$type = next( $theType );
					if( $add )
						$data = next( $theData );
				}
				
				return $result;														// ==>
			
			} // Multiple items.
			
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
					// Match category.
					//
					if( ( ($theType !== NULL)
					   && ( ( is_array( $element )
						   && array_key_exists( $theTypeOffset, $element )
						   && ($element[ $theTypeOffset ] == (string) $theType) )
						 || ( ($element instanceof ArrayObject)
						   && $element->offsetExists( $theTypeOffset )
						   && ($element->offsetGet( $theTypeOffset ) == (string) $theType) ) ) )
					 || ( ($theType === NULL)
					   && ( ( is_array( $element )
						   && (! array_key_exists( $theTypeOffset, $element )) )
						 || ( ($element instanceof ArrayObject)
						   && (! $element->offsetExists( $theTypeOffset )) ) ) ) )
					{
						$idx = $key;
						$save = $element[ $theDataOffset ];
						break;												// =>
					
					} // Matched.
				
				} // Iterating offset elements.
			
			} // Has data.
			
			//
			// Return current value.
			//
			if( $theData === NULL )
				return $save;														// ==>
			
			//
			// Delete current value.
			//
			if( $theData === FALSE )
			{
				//
				// Handle matched.
				//
				if( $idx !== NULL )
				{
					//
					// Remove category.
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
						if( is_array( $theReference ) )
							unset( $theReference[ $theMainOffset ] );
						else
							$theReference->offsetUnset( $theMainOffset );
					
					} // No elements left.
				
				} // Matched category.
				
				if( $getOld )
					return $save;													// ==>
				
				return NULL;														// ==>
			
			} // Delete current value.
			
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
			// Replace category.
			//
			elseif( $idx !== NULL )
				$theReference[ $theMainOffset ][ $idx ] = $element;
				
			//
			// Add category.
			//
			else
				$theReference[ $theMainOffset ][] = $element;
			
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
	 * A typed array offset is structured as follows:
	 *
	 * <ul>
	 *	<li><i>Type</i>: This offset contains a scalar which determined the type or category
	 *		of the element. This offset may be omitted.
	 *	<li><i>Data</i>: This offset contains the element data as an array of data elements,
	 *		these elements are handled by the {@link ManageArrayOffset() ManageArrayOffset}
	 *		method.
	 * </ul>
	 *
	 * No two elements may share the same type or category and all data elements within a
	 * category must be unique.
	 *
	 * The method accepts the following parameters:
	 *
	 * <ul>
	 *	<li><b>&$theReference</b>: Reference to an array or ArrayObject derived instance.
	 *	<li><b>$theMainOffset</b>: The offset to manage.
	 *	<li><b>$theTypeOffset</b>: The element's offset of the type or category.
	 *	<li><b>$theDataOffset</b>: The element's offset of the data.
	 *	<li><b>$theType</b>: This parameter represents the index to the offset's elements.
	 *	<li><b>$theData</b>: This parameter represents the element's data element, if
	 *		<i>NULL</i> or omitted, it implieas that the operation applies to the whole list
	 *		of data elements.
	 *	<li><b>$theOperation</b>: This parameter represents the operation to be performed,
	 *		it will be evaluated as a boolean and its scope depends on the value of the
	 *		previous parameter:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the element or list.
	 *		<li><i>FALSE</i>: Delete the element or list.
	 *		<li><i>TRUE</i>: Add the element or list.
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
	 *
	 * @static
	 * @return mixed
	 */
	static function ManageTypedArrayOffset( &$theReference,
											 $theMainOffset, $theTypeOffset, $theDataOffset,
											 $theType, $theData = NULL,
											 $theOperation = NULL, $getOld = FALSE )
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
					// Match category.
					//
					if( ( ($theType !== NULL)
					   && ( ( is_array( $element )
						   && array_key_exists( $theTypeOffset, $element )
						   && ($element[ $theTypeOffset ] == (string) $theType) )
						 || ( ($element instanceof ArrayObject)
						   && $element->offsetExists( $theTypeOffset )
						   && ($element->offsetGet( $theTypeOffset ) == (string) $theType) ) ) )
					 || ( ($theType === NULL)
					   && ( ( is_array( $element )
						   && (! array_key_exists( $theTypeOffset, $element )) )
						 || ( ($element instanceof ArrayObject)
						   && (! $element->offsetExists( $theTypeOffset )) ) ) ) )
					{
						$idx = $key;
						$save = $element;
						break;												// =>
					
					} // Matched.
				
				} // Iterating offset elements.
			
			} // Has data.
			
			//
			// Return current value.
			//
			if( $theOperation === NULL )
			{
				//
				// Handle data.
				//
				if( $save !== NULL )
				{
					//
					// Return list.
					//
					if( $theData === NULL )
						return $save[ $theDataOffset ];								// ==>

					return CAttribute::ManageArrayOffset
						( $save, $theDataOffset, $theData,
						  $theOperation, $getOld );									// ==>
				
				} // Has data.
				
				return NULL;														// ==>
			
			} // Return current element.
			
			//
			// Delete matched value.
			//
			if( $theOperation === FALSE )
			{
				//
				// Handle data.
				//
				if( $save !== NULL )
				{
					//
					// Remove element.
					//
					if( $theData !== NULL )
					{
						//
						// Remove data element.
						//
						$result= CAttribute::ManageArrayOffset
							( $save, $theDataOffset, $theData, $theOperation, $getOld );
						
						//
						// Update category.
						//
						if( ( is_array( $save )
						   && array_key_exists( $theDataOffset, $save ) )
						 || ( ($save instanceof ArrayObject)
						   && $save->offsetExists( $theDataOffset ) ) )
							$theReference[ $theMainOffset ][ $idx ] = $save;
						
						//
						// Delete category.
						//
						else
						{
							//
							// Remove from list.
							//
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
						
						} // No more data elements.
						
						return $result;												// ==>
					
					} // Provided data element.
					
					//
					// Remove from list.
					//
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
					
					if( $getOld )
						return $save;												// ==>
				
				} // Matched.
				
				return NULL;														// ==>
			
			} // Delete matched element.
			
			//
			// Create first category.
			//
			if( $list === NULL )
			{
				//
				// Create element.
				//
				$save = Array();
				if( $theType !== NULL )
					$save[ $theTypeOffset ] = $theType;
				$result= CAttribute::ManageArrayOffset
							( $save, $theDataOffset, $theData, $theOperation, $getOld );
				
				//
				// Set offset.
				//
				$theReference[ $theMainOffset ] = array( $save );
				
				return $result;														// ==>
			
			} // Missing main offset.
			
			//
			// Create new category.
			//
			elseif( $save === NULL )
			{
				//
				// Create element.
				//
				$save = Array();
				if( $theType !== NULL )
					$save[ $theTypeOffset ] = $theType;
				$result= CAttribute::ManageArrayOffset
							( $save, $theDataOffset, $theData, $theOperation, $getOld );
				
				//
				// Add to list.
				//
				$list[] = $save;
				
				//
				// Set offset.
				//
				$theReference[ $theMainOffset ] = $list;
				
				return $result;														// ==>
			
			} // New category.
			
			//
			// Update category.
			//
			else
			{
				//
				// Update element.
				//
				$result= CAttribute::ManageArrayOffset
							( $save, $theDataOffset, $theData, $theOperation, $getOld );
				
				//
				// Set offset.
				//
				$theReference[ $theMainOffset ][ $idx ] = $save;
				
				return $result;														// ==>
			
			} // Matched category.
		
		} // Supported reference.

		throw new CException
				( "Unsupported object reference",
				  kERROR_UNSUPPORTED,
				  kMESSAGE_TYPE_ERROR,
				  array( 'Reference' => $theReference ) );						// !@! ==>
	
	} // ManageTypedArrayOffset.

	 

} // class CAttribute.


?>
