<?php

/**
 * <i>CArrayObject</i> class definition.
 *
 * This file contains the class definition of <b>CArrayObject</b> which represents the
 * ancestor of all classes in this library.
 *
 *	@package	MyWrapper
 *	@subpackage	Framework
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 07/04/2009
 *				2.00 23/11/2010
 *				3.00 13/02/2012
 */

/*=======================================================================================
 *																						*
 *									CArrayObject.php									*
 *																						*
 *======================================================================================*/

/**
 * Library.
 *
 * This include file contains the definitions of the {@link CObject CObject} class which
 * contains a static library of methods.
 */
require_once( kPATH_LIBRARY_SOURCE."CObject.php" );

/**
 * Offsets.
 *
 * This include file contains all default offset definitions.
 */
require_once( kPATH_LIBRARY_DEFINES."Offsets.inc.php" );

/**
 *	Common ancestor.
 *
 * This class represents the ancestor of most entity mapped classes, it maps objects to an
 * array by deriving from {@link ArrayObject ArrayObject} and it defines a couple of general
 * purpose utility static methods.
 *
 * This class implements the following interfaces:
 *
 * <ul>
 *	<li><i>Offsets</i>: In this class there cannot be an offset with a <i>NULL</i> value,
 *		the offset itself should be {@link offsetUnset() deleted} in that case. Because of
 *		this we also override the inherited behaviour by suppressing notices and warnings
 *		when {@link offsetGet() getting} non-existant offsets.
 *	<li><i>String formatting</i>: We provide a generalised static method to
 *		{@link StringNormalise() format} strings which accepts a bitfield parameter that
 *		indicates which operation to perform, such as {@link kFLAG_MODIFIER_UTF8 UTF8}
 *		encode, {@link kFLAG_MODIFIER_LTRIM left} and {@link kFLAG_MODIFIER_RTRIM right}
 *		trim, {@link kFLAG_MODIFIER_NULL NULL} handling, {@link kFLAG_MODIFIER_NOCASE case}
 *		insensitive conversion, {@link kFLAG_MODIFIER_URL URL},
 *		{@link kFLAG_MODIFIER_HTML HTML} and {@link kFLAG_MODIFIER_HEX HEX} encoding and
 *		{@link kFLAG_MODIFIER_HASH hashing}.
 *	<li><i>Time formatting</i>: We provide a generalised static
 *		{@link DurationString() method} to display duration strings.
 * </ul>
 *
 *	@package	MyWrapper
 *	@subpackage	Framework
 */
class CArrayObject extends ArrayObject
{
		

/*=======================================================================================
 *																						*
 *								PUBLIC ARRAY ACCESS INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	offsetGet																		*
	 *==================================================================================*/

	/**
	 * Return a value at a given offset.
	 *
	 * This method should return the value corresponding to the provided offset.
	 *
	 * This method is overloaded to prevent notices from being triggered when seeking
	 * non-existing offsets.
	 *
	 * In this class no offset may have a <i>NULL</i> value, if this method returns a
	 * <i>NULL</i> value, it means that the offset doesn't exist.
	 *
	 * @param string				$theOffset			Offset.
	 *
	 * @access public
	 * @return mixed
	 */
	public function offsetGet( $theOffset )	{	return @parent::offsetGet( $theOffset );	}

	 
	/*===================================================================================
	 *	offsetSet																		*
	 *==================================================================================*/

	/**
	 * Set a value for a given offset.
	 *
	 * This method should set the provided value corresponding to the provided offset.
	 *
	 * This method is overloaded to prevent setting <i>NULL</i> values: if this is the case,
	 * the method will {@link offsetUnset() delete} the offset.
	 *
	 * @param string				$theOffset			Offset.
	 * @param string|NULL			$theValue			Value to set at offset.
	 *
	 * @access public
	 *
	 * @uses offsetUnset()
	 */
	public function offsetSet( $theOffset, $theValue )
	{
		//
		// Set value.
		//
		if( $theValue !== NULL )
			parent::offsetSet( $theOffset, $theValue );
		
		//
		// Delete offset.
		//
		else
			$this->offsetUnset( $theOffset );
	
	} // offsetSet.

	 
	/*===================================================================================
	 *	offsetUnset																		*
	 *==================================================================================*/

	/**
	 * Reset a value for a given offset.
	 *
	 * This method should reset the value corresponding to the provided offset.
	 *
	 * We overload this method to prevent notices on non-existing offsets.
	 *
	 * @param string				$theOffset			Offset.
	 *
	 * @access public
	 */
	public function offsetUnset( $theOffset )	{	@parent::offsetUnset( $theOffset );		}

		

/*=======================================================================================
 *																						*
 *								PUBLIC ARRAY UTILITY INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	keys																			*
	 *==================================================================================*/

	/**
	 * Return object's keys.
	 *
	 * This method has the same function as array's <i>array_keys()</i>, it will return an
	 * array comprised of all object's offsets.
	 *
	 * @access public
	 * @return array
	 */
	public function keys()							{	return array_keys( (array) $this );	}

	 
	/*===================================================================================
	 *	values																			*
	 *==================================================================================*/

	/**
	 * Return object's values.
	 *
	 * This method has the same function as array's <i>array_values()</i>, it will return an
	 * array comprised of all object's values.
	 *
	 * @access public
	 * @return array
	 */
	public function values()					{	return array_values( (array) $this );	}

		

/*=======================================================================================
 *																						*
 *							PROTECTED MEMBER ACCESSOR INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_ManageOffset																	*
	 *==================================================================================*/

	/**
	 * Manage an offset.
	 *
	 * This library implements a standard interface for managing object properties using
	 * methods, this method extends the approach to offset members:
	 *
	 * <ul>
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
	 * @param string				$theOffset			Offset.
	 * @param mixed					$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access protected
	 * @return mixed
	 *
	 * @uses offsetGet()
	 * @uses offsetSet()
	 * @uses offsetUnset()
	 */
	protected function _ManageOffset( $theOffset, $theValue = NULL, $getOld = FALSE )
	{
		//
		// Save current value.
		//
		$save = $this->offsetGet( (string) $theOffset );
		
		//
		// Return current value.
		//
		if( $theValue === NULL )
			return $save;															// ==>
		
		//
		// Delete offset.
		//
		if( $theValue === FALSE )
			$this->offsetUnset( $theOffset );
		
		//
		// Set offset.
		//
		else
			$this->offsetSet( $theOffset, $theValue );
		
		return ( $getOld ) ? $save													// ==>
						   : $this->offsetGet( (string) $theOffset );				// ==>
	
	} // _ManageOffset.

	 
	/*===================================================================================
	 *	_ManageArrayOffset																*
	 *==================================================================================*/

	/**
	 * Manage an array offset.
	 *
	 * This library implements a standard interface for managing object properties using
	 * methods, this method extends the approach to offset members that are in the form of
	 * arrays, in which it should be possible to cast the values to strings and these
	 * strings should be unique within the list:
	 *
	 * <ul>
	 *	<li><b>$theOffset</b>: The offset to manage.
	 *	<li><b>$theValue</b>: This parameter represents either the value to add, or the
	 *		index of the element to operate on:
	 *	 <ul>
	 *		<li><i>NULL</i>: This value indicates that we want to operate on all elements,
	 *			which means that we are either retrieving the full list or deleting it.
	 *		<li><i>array</i>: This value indicates that we want to replace the whole list,
	 *			this will only be tested if the next parameter evaluates to <i>TRUE</i>.
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
	 *		<li><i>TRUE</i>: Add the element or list. Note that with this value, if you
	 *			provide <i>NULL</i> in the previous parameter, it will be equivalent to
	 *			deleting the whole list.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the element or list <i>before</i> it was eventually
	 *			modified.
	 *		<li><i>FALSE</i>: Return the element or list <i>after</i> it was eventually
	 *			modified.
	 *	 </ul>
	 * </ul>
	 *
	 * @param string				$theOffset			Offset.
	 * @param mixed					$theValue			Value or index.
	 * @param mixed					$theOperation		Operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access protected
	 * @return mixed
	 *
	 * @uses offsetGet()
	 * @uses offsetSet()
	 * @uses offsetUnset()
	 */
	protected function _ManageArrayOffset( $theOffset, $theValue = NULL,
													   $theOperation = NULL,
													   $getOld = FALSE )
	{
		//
		// Save current list.
		//
		$list = Array();
		$save = $this->offsetGet( $theOffset );
		if( $save !== NULL )
		{
			foreach( $save as $element )
				$list[ md5( $element, TRUE ) ] = $element;
		}
		
		//
		// Return element or list.
		//
		if( $theOperation === NULL )
		{
			//
			// Return full list or no list.
			//
			if( ($save === NULL)		// Empty list,
			 || ($theValue === NULL) )	// return full list.
				return $save;														// ==>
			
			//
			// Scan list.
			//
			if( array_key_exists( ($key = md5( (string) $theValue, TRUE )), $list ) )
				return $list[ $key ];												// ==>
			
			return NULL;															// ==>
		
		} // Return element or list.

		//
		// Delete element or list.
		//
		if( $theOperation === FALSE )
		{
			//
			// Missing list.
			//
			if( $save === NULL )
				return NULL;														// ==>
			
			//
			// Delete full list.
			//
			if( $theValue === NULL )
			{
				//
				// Delete list.
				//
				$this->offsetUnset( $theOffset );
				
				if( $getOld )
					return $save;													// ==>
				
				return NULL;														// ==>
			}
			
			//
			// Scan list.
			//
			if( $save !== NULL )
			{
				//
				// Find element.
				//
				if( array_key_exists( ($key = md5( (string) $theValue, TRUE )), $list ) )
				{
					//
					// Save old.
					//
					$old = $list[ $key ];
					
					//
					// Remove element.
					//
					unset( $list[ $key ] );
					
					//
					// Update object.
					//
					if( count( $list ) )
						$this->offsetSet( $theOffset, array_values( $list ) );
					else
						$this->offsetUnset( $theOffset );
					
					if( $getOld )
						return $old;												// ==>
				
				} // Found element.
			
			} // Has list.
			
			return NULL;															// ==>
		
		} // Delete element or list.
		
		//
		// Delete full list.
		// At this pont the operation involves
		// adding and the value is NULL.
		//
		if( $theValue === NULL )
		{
			//
			// Delete list.
			//
			$this->offsetUnset( $theOffset );
			
			if( $getOld )
				return $save;														// ==>
			
			return NULL;															// ==>
		}
		
		//
		// Replace full list.
		//
		if( is_array( $theValue ) )
		{
			//
			// Replace offset.
			//
			$this->offsetSet( $theOffset, $theValue );
			
			if( $getOld )
				return $save;														// ==>
			
			return $theValue;														// ==>
		
		} // Replace full list.
		
		//
		// Add first element.
		//
		if( $save === NULL )
			$this->offsetSet( $theOffset, array( $theValue ) );
		
		//
		// Set element.
		//
		else
		{
			//
			// Set in list.
			//
			$list[ md5( (string) $theValue, TRUE ) ] = (string) $theValue;
			
			//
			// Replace offset.
			//
			$this->offsetSet( $theOffset, array_values( $list ) );
		}
		
		if( $getOld )
			return NULL;															// ==>
		
		return $theValue;															// ==>
	
	} // _ManageArrayOffset.

	 
	/*===================================================================================
	 *	_ManageKindArrayOffset															*
	 *==================================================================================*/

	/**
	 * Manage a kind array offset.
	 *
	 * This library implements a standard interface for managing object properties using
	 * methods, this method extends the approach to offset members that are in the form of
	 * arrays, in which each element is itself an array of two items:
	 *
	 * <ul>
	 *	<li><i>Kind</i>: This element represents the qualifier of the item, it provides a
	 *		type or qualification for the next element. It must be possible to cast this
	 *		value to a string. This element is defined by the second method parameter.
	 *	<li><i>{@link kTAG_DATA kTAG_DATA}</i>: This element represents the element data.
	 * </ul>
	 *
	 * No two elements of the list can share the same index. You may have one element
	 * without index, this one may be considered the default element.
	 *
	 * This method is intended for managing list elements rather than the list itself, for
	 * the latter purpose use the offset management methods.
	 *
	 * The parameters to this method are:
	 *
	 * <ul>
	 *	<li><b>$theOffset</b>: The main offset to manage.
	 *	<li><b>$theIndex</b>: The offset representing the type of the element.
	 *	<li><b>$theType</b>: This parameter represents the value of the index element of the
	 *		item, depending on the next parameter this value will be used for matching
	 *		items in the list:
	 *	 <ul>
	 *		<li><i>NULL</i>: An empty type means that we are looking for the item lacking
	 *			the index element.
	 *		<li><i>array</i>: If you provide an array, it means that you are operating on a
	 *			list of items: depending on the next parameter this will mean either
	 *			retrieving the {@link kTAG_DATA data} elements of the items matching the
	 *			array, deleting these items, or adding/replacing the items; in this last
	 *			case, this means that the next parameter must also be an array and that each
	 *			of its elements will be associated to the corresponding index element.
	 *		<li><i>other</i>: Any other value will be considered as the index to retrieve,
	 *			remove or add/replace. You <i>MUST</i> be able to cast this value to a
	 *			string.
	 *	 </ul>
	 *	<li><b>$theData</b>: This parameter represents the item's {@link kTAG_DATA data}
	 *		element, or the operation to be performed:
	 *	 <ul>
	 *		<li><i>NULL</i>: This indicates that we want to retrieve the data of the item
	 *			with index matching the previous parameter.
	 *		<li><i>FALSE</i>: This indicates that we want to remove the item matching the
	 *			index provided in the previous parameter.
	 *		<li><i>other</i>: Any other value indicates that we want to add or replace the
	 *			{@link kTAG_DATA data} element of the item matching the previous parameter.
	 *			Note that if the previous parameter is an array, this one must also be an
	 *			array matching the latter's count.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the element or list <i>before</i> it was eventually
	 *			modified.
	 *		<li><i>FALSE</i>: Return the element or list <i>after</i> it was eventually
	 *			modified.
	 *	 </ul>
	 * </ul>
	 *
	 * @param string				$theOffset			Offset.
	 * @param mixed					$theIndex			Index offset.
	 * @param mixed					$theType			Element type.
	 * @param mixed					$theData			Element value.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access protected
	 * @return mixed
	 *
	 * @uses offsetGet()
	 * @uses offsetSet()
	 * @uses offsetUnset()
	 */
	protected function _ManageKindArrayOffset( $theOffset, $theIndex, $theType = NULL,
																	  $theData = NULL,
																	  $getOld = FALSE )
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
							  array( 'Types' => $theType,
							  		 'Data' => $theData ) );					// !@! ==>
				
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
			$data = ( $add )
				  ? reset( $theData )
				  : $theData;
			while( $count-- )
			{
				//
				// Recurse.
				//
				$result[]
					= $this->_ManageKindArrayOffset
						( $theOffset, $theIndex, $type, $data, $getOld );
				
				//
				// Advance.
				//
				$type = next( $theType );
				if( $add )
					$data = next( $theData );
			}
			
			return $result;															// ==>
		
		} // Multiple items.
		
		//
		// Get current list.
		//
		$save = $this->offsetGet( $theOffset );
		
		//
		// Retrieve element.
		//
		if( $theData === NULL )
		{
			//
			// Handle existing list.
			//
			if( $save !== NULL )
			{
				//
				// Look for type.
				//
				foreach( $save as $item )
				{
					//
					// Match type.
					//
					if( ( ($theType === NULL)
					   && (! array_key_exists( $theIndex, $item )) )
					 || ( array_key_exists( $theIndex, $item )
					   && (((string) $theType) == $item[ $theIndex ]) ) )
						return $item[ kTAG_DATA ];									// ==>
				
				} // Iterating list items.
			
			} // Existing list.
			
			return NULL;															// ==>
			
		} // Retrieve.
		
		//
		// Delete element.
		//
		if( $theData === FALSE )
		{
			//
			// Handle existing list.
			//
			$match = NULL;
			if( $save !== NULL )
			{
				//
				// Look for type.
				//
				$list = Array();
				foreach( $save as $key => $item )
				{
					//
					// Match type.
					//
					if( ( ($theType === NULL)
					   && (! array_key_exists( $theIndex, $item )) )
					 || ( array_key_exists( $theIndex, $item )
					   && (((string) $theType) == $item[ $theIndex ]) ) )
						$match = $item[ kTAG_DATA ];
					
					//
					// Other types.
					//
					else
						$list[] = $item;
				
				} // Iterating list items.
				
				//
				// Replace offset with new list.
				//
				if( $match !== NULL )
				{
					//
					// Replace offset.
					//
					if( count( $list ) )
						$this->offsetSet( $theOffset, $list );
					
					//
					// Delete offset.
					//
					else
						$this->offsetUnset( $theOffset );
					
					if( $getOld )
						return $match;												// ==>
				
				} // Matched item.
			
			} // Existing list.
			
			return NULL;															// ==>
		
		} // Delete.
		
		//
		// Create new item.
		//
		$new = Array();
		if( $theType !== NULL )
			$new[ $theIndex ] = $theType;
		$new[ kTAG_DATA ] = $theData;
		
		//
		// Create new list.
		//
		if( $save === NULL )
		{
			//
			// Save list.
			//
			$this->offsetSet( $theOffset, array( $new ) );
			
			if( $getOld )
				return NULL;														// ==>
			
			return $theData;														// ==>
		
		} // No list.
		
		//
		// Replace item.
		//
		$match = NULL;
		$list = Array();
		foreach( $save as $key => $item )
		{
			//
			// Match type.
			//
			if( ( ($theType === NULL)
			   && (! array_key_exists( $theIndex, $item )) )
			 || ( array_key_exists( $theIndex, $item )
			   && (((string) $theType) == $item[ $theIndex ]) ) )
			{
				//
				// Save old element.
				//
				$match = $item[ kTAG_DATA ];
				
				//
				// Replace with new item.
				//
				$list[ $key ] = $new;
			
			} // Matched.
			
			//
			// Other types.
			//
			else
				$list[] = $item;
		
		} // Iterating list items.
		
		//
		// Append item.
		//
		if( $match === NULL )
			$list[] = $new;
		
		//
		// Save list.
		//
		$this->offsetSet( $theOffset, $list );
		
		if( $getOld )
			return $match;															// ==>
		
		return $theData;															// ==>
	
	} // _ManageKindArrayOffset.

	 
	/*===================================================================================
	 *	_ManageTypedArrayOffset															*
	 *==================================================================================*/

	/**
	 * Manage a type array offset.
	 *
	 * This library implements a standard interface for managing object properties using
	 * methods, this method extends the approach to offset members that are in the form of
	 * arrays, in which each item is itself an array of three elements:
	 *
	 * <ul>
	 *	<li><i>{@link kTAG_KIND kTAG_KIND}</i>: This element represents the kind or
	 *		qualifier of the item, the element is required.
	 *	<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>: This element represents the data type of the
	 *		item, this element is required.
	 *	<li><i>{@link kTAG_DATA kTAG_DATA}</i>: This element represents the item data which
	 *		should be expressed in the data type declared in the {@link kTAG_TYPE kTAG_TYPE}
	 *		element.
	 * </ul>
	 *
	 * No two elements of the list can share the same {@link kTAG_KIND kind} and
	 * {@link kTAG_TYPE kTAG_TYPE}, these represent the index of the array.
	 *
	 * This method is intended for managing list elements rather than the list itself, for
	 * the latter purpose use the offset management methods.
	 *
	 * The parameters to this method are:
	 *
	 * <ul>
	 *	<li><b>$theOffset</b>: The main offset to manage.
	 *	<li><b>$theKind</b>: The item {@link kTAG_KIND kind}; it should be able to cast this
	 *		value to a string which represents an index.
	 *	<li><b>$theType</b>: The item {@link kTAG_TYPE type}; it should be able to cast this
	 *		value to a string which represents an index.
	 *	<li><b>$theData</b>: This parameter represents the item's {@link kTAG_DATA data}
	 *		element, or the operation to be performed:
	 *	 <ul>
	 *		<li><i>NULL</i>: This indicates that we want to retrieve the data of the item
	 *			with index matching the previous parameters.
	 *		<li><i>FALSE</i>: This indicates that we want to remove the item matching the
	 *			index provided in the previous parameters.
	 *		<li><i>other</i>: Any other value indicates that we want to add or replace the
	 *			{@link kTAG_DATA data} element of the item matching the previous parameters.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the element or list <i>before</i> it was eventually
	 *			modified.
	 *		<li><i>FALSE</i>: Return the element or list <i>after</i> it was eventually
	 *			modified.
	 *	 </ul>
	 * </ul>
	 *
	 * @param string				$theOffset			Offset.
	 * @param mixed					$theKind			Item kind.
	 * @param mixed					$theType			Item type.
	 * @param mixed					$theData			Item value.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access protected
	 * @return mixed
	 *
	 * @uses offsetGet()
	 * @uses offsetSet()
	 * @uses offsetUnset()
	 */
	protected function _ManageTypedArrayOffset( $theOffset,
												$theKind, $theType,
												$theData = NULL, $getOld = FALSE )
	{
		//
		// Get current list.
		//
		$index = $save = NULL;
		$offset = $this->offsetGet( $theOffset );
		if( $offset !== NULL )
		{
			//
			// Locate item.
			//
			foreach( $offset as $key => $value )
			{
				if( ($value[ kTAG_KIND ] == (string) $theKind)
				 && ($value[ kTAG_TYPE ] == (string) $theType) )
				{
					$save = $value[ kTAG_DATA ];
					$index = $key;
					
					break;													// =>
				}
			}
		}
		
		//
		// Retrieve element.
		//
		if( $theData === NULL )
			return $save;															// ==>
		
		//
		// Delete element.
		//
		if( $theData === FALSE )
		{
			//
			// Handle existing list.
			//
			if( $index != NULL )
			{
				//
				// Delete item.
				//
				unset( $offset[ $index ] );
				
				//
				// Replace offset.
				//
				if( count( $offset ) )
					$this->offsetSet( $theOffset, array_values( $offset ) );
				
				//
				// Delete offset.
				//
				else
					$this->offsetUnset( $theOffset );
				
				if( $getOld )
					return $save;													// ==>
				
				return NULL;														// ==>
			}
			
			return NULL;															// ==>
		
		} // Delete.
		
		//
		// Replace item.
		//
		if( $index !== NULL )
		{
			//
			// Replace data.
			//
			$offset[ $index ][ kTAG_DATA ] = $theData;
			
			//
			// Replace offset.
			//
			$this->offsetSet( $theOffset, array_values( $offset ) );
			
			if( $getOld )
				return $save;														// ==>
			
			return $theData;														// ==>
		}
		
		//
		// Create item.
		//
		else
		{
			//
			// Create item.
			//
			$item = array( kTAG_KIND => $theKind,
						   kTAG_TYPE => $theType,
						   kTAG_DATA => $theData );
			
			//
			// Append item.
			//
			if( $offset !== NULL )
			{
				//
				// Add item.
				//
				$offset[] = $item;
				
				//
				// Replace offset.
				//
				$this->offsetSet( $theOffset, $offset );
			}
			
			//
			// Create offset.
			//
			else
				$this->offsetSet( $theOffset, array( $item ) );
		}
		
		if( $getOld )
			return $save;															// ==>
		
		return $theData;															// ==>
	
	} // _ManageTypedArrayOffset.

	 
	/*===================================================================================
	 *	_ManageTypedArrayListOffset														*
	 *==================================================================================*/

	/**
	 * Manage a typed array list offset.
	 *
	 * This method handles a property structured as a list of items structured as a pair of
	 * elements:
	 *
	 * <ul>
	 *	<li><i>Type</i>: The element that holds the item's type.
	 *	<li><i>{@link kTAG_DATA kTAG_DATA}</i>: The element that holds the list of values
	 *		for the type, this element has the {@link kTAG_DATA kTAG_DATA} offset by
	 *		default.
	 * </ul>
	 *
	 * The parameters to this method are:
	 *
	 * <ul>
	 *	<li><b>$theOffset</b>: The property offset.
	 *	<li><b>$theType</b>: The item type offset, it must be a string.
	 *	<li><b>$theIndex</b>: The item type value, it must be a string.
	 *	<li><b>$theData</b>: The data element upon which we want to operate.
	 *	<li><b>$theOperation</b>: The operation to be performed:
	 *	 <ul>
	 *		<li><i>NULL</i>: Retrieve the data element matching the previous parameters, or
	 *			<i>NULL</i> if not found.
	 *		<li><i>FALSE</i>: Delete the data element matching the previous parameters.
	 *		<li><i>other</i>: Add or replace the data element matching the previous
	 *			parameters.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the element <i>before</i> it was eventually modified.
	 *		<li><i>FALSE</i>: Return the element <i>after</i> it was eventually modified.
	 *	 </ul>
	 * </ul>
	 *
	 * @param string				$theOffset			Offset.
	 * @param mixed					$theIndex			Index offset.
	 * @param mixed					$theType			Element type.
	 * @param mixed					$theData			Element value.
	 * @param mixed					$theOperation		Operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access protected
	 * @return mixed
	 *
	 * @uses offsetGet()
	 * @uses offsetSet()
	 * @uses offsetUnset()
	 */
	protected function _ManageTypedArrayListOffset( $theOffset, $theIndex,
													$theType, $theData,
													$theOperation = NULL,
													$getOld = FALSE )
	{
		//
		// Init local storage.
		//
		$offset = $this->offsetGet( $theOffset );
		$element = $list = NULL;
		if( $offset !== NULL )
		{
			//
			// Locate data list.
			//
			foreach( $offset as $key => $item )
			{
				if( array_key_exists( $theIndex, $item ) )
				{
					if( $item[ $theIndex ] == $theType )
					{
						$element = $key;
						if( array_key_exists( kTAG_DATA, $offset[ $key ] ) )
						{
							$list = $offset[ $key ][ kTAG_DATA ];
							break;												// =>
						}
						
						else
							throw new CException
									( "Missing data item offset",
									  kERROR_INVALID_PARAMETER,
									  kMESSAGE_TYPE_ERROR,
									  array( 'Item' => $offset[ $key ],
											 'offset' => kTAG_DATA ) );			// !@! ==>
					}
				}
			}
		}
		
		//
		// Return value.
		//
		if( $theOperation === NULL )
		{
			//
			// Locate element.
			//
			if( $list !== NULL )
			{
				//
				// Get list.
				//
				if( in_array( $theData, $list ) )
					return $theData;												// ==>
			}
			
			return NULL;															// ==>
		}
		
		//
		// Delete value.
		//
		if( $theOperation === FALSE )
		{
			//
			// Locate element.
			//
			if( $list !== NULL )
			{
				//
				// Get element.
				//
				if( ($key = array_search( $theData, $list )) !== FALSE )
				{
					//
					// Remove element.
					//
					unset( $list[ $key ] );
					if( count( $list ) )
					{
						//
						// Update list.
						//
						$offset[ $element ][ kTAG_DATA ] = array_values( $list );
						
						//
						// Update offset.
						//
						$this->offsetSet( $theOffset, $offset );
					}
					
					//
					// No elements left.
					//
					else
					{
						//
						// Remove item.
						//
						unset( $offset[ $element ] );
						if( count( $offset ) )
							$this->offsetSet( $theOffset, array_values( $offset ) );
						
						//
						// No items left.
						//
						else
							$this->offsetUnset( $theOffset );
					}
					
					if( $getOld )
						return $theData;											// ==>
				}
			}
			
			return NULL;															// ==>
		}

		//
		// Locate element.
		//
		if( $list !== NULL )
		{
			//
			// Matched element.
			//
			if( ($key = array_search( $theData, $list )) !== FALSE )
				return $theData;													// ==>
			
			//
			// Add element.
			//
			$offset[ $element ][ kTAG_DATA ][] = $theData;
			
			//
			// Upate offset.
			//
			$this->offsetSet( $theOffset, $offset );

			if( $getOld )
				return NULL;														// ==>
			
			return $theData;														// ==>
		}
		
		//
		// Create element.
		//
		$element = array( $theIndex => $theType, kTAG_DATA => array( $theData ) );
		
		//
		// Create offset.
		//
		if( $offset !== NULL )
		{
			$offset[] = $element;
			$this->offsetSet( $theOffset, $offset );
		}
		
		//
		// Add item.
		//
		else
			$this->offsetSet( $theOffset, array( $element ) );

		if( $getOld )
			return NULL;															// ==>
		
		return $theData;															// ==>
	
	} // _ManageTypedArrayListOffset.

	 

} // class CArrayObject.


?>
