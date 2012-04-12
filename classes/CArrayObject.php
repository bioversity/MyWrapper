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
		// Get current list.
		//
		$save = $this->offsetGet( $theOffset );
		
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
			if( ($key = array_search( (string) $theValue, $save )) !== FALSE )
				return $save[ $key ];												// ==>
			
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
				if( ($key = array_search( (string) $theValue, $save )) !== FALSE )
				{
					//
					// Save old.
					//
					$old = $save[ $key ];
					
					//
					// Remove element.
					//
					unset( $save[ $key ] );
					
					//
					// Update object.
					//
					if( count( $save ) )
						$this->offsetSet( $theOffset, array_values( (array) $save ) );
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
		{
			//
			// Build list.
			//
			$save = array( $theValue );
			
			//
			// Set list.
			//
			$this->offsetSet( $theOffset, $save );
			
			if( $getOld )
				return NULL;														// ==>
			
			return $theValue;														// ==>
		
		} // New list.
		
		//
		// Replace element.
		//
		if( ($key = array_search( (string) $theValue, $save )) !== FALSE )
		{
			//
			// Replace.
			//
			$old = $save[ $key ];
			$save[ $key ] = $theValue;
			
			if( $getOld )
				return $old;														// ==>
			
			return $theValue;														// ==>
		
		} // List and element exist.
		
		//
		// Append the element.
		//
		$save[] = $theValue;
		
		//
		// Update offset.
		//
		$this->offsetSet( $theOffset, $save );
		
		if( $getOld )
			return NULL;															// ==>
		
		return $theValue;															// ==>
	
	} // _ManageArrayOffset.

	 
	/*===================================================================================
	 *	_ManageTypedArrayOffset															*
	 *==================================================================================*/

	/**
	 * Manage a typed array offset.
	 *
	 * This library implements a standard interface for managing object properties using
	 * methods, this method extends the approach to offset members that are in the form of
	 * arrays, in which each element is itself an array of two items:
	 *
	 * <ul>
	 *	<li><i>Index</i>: This element represents the qualifier of the item, it provides a
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
	 *	<li><b>$theOffset</b>: The offset to manage.
	 *	<li><b>$theIndex</b>: The list index offset, this value will be the offset holding
	 *		the list elements index.
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
	protected function _ManageTypedArrayOffset( $theOffset, $theIndex, $theType = NULL,
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
					= $this->_ManageTypedArrayOffset
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
	
	} // _ManageTypedArrayOffset.

	 

} // class CArrayObject.


?>
