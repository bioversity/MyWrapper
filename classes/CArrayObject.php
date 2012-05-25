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
	 * This method can be used as the framework for managing scalar attributes, it can be
	 * used to add, retrieve and delete object attributes considered as scalars.
	 *
	 * The method expects the following parameters:
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
		// Normalise offset.
		//
		$theOffset = (string) $theOffset;
		
		//
		// Save current value.
		//
		$save = $this->offsetGet( $theOffset );
		
		//
		// Return offset value.
		//
		if( $theValue === NULL )
			return $save;															// ==>
		
		//
		// Delete offset value.
		//
		if( $theValue === FALSE )
		{
			//
			// Delete offset.
			//
			$this->offsetUnset( $theOffset );
			
			if( $getOld )
				return $save;														// ==>
			
			return NULL;															// ==>
		
		} // Delete.
		
		//
		// Set offset.
		//
		$this->offsetSet( $theOffset, $theValue );
		
		if( $getOld )
			return $save;															// ==>
		
		return $theValue;															// ==>
	
	} // _ManageOffset.

	 
	/*===================================================================================
	 *	_ManageArrayOffset																*
	 *==================================================================================*/

	/**
	 * Manage an array offset.
	 *
	 * This method can be used to manage an array offset, this options involves setting,
	 * retrieving and deleting elements of an offset which contains an array of values, this
	 * method concentrates in managing the offset's elements, rather than
	 * {@link _ManageOffset() managing} the offset itself.
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
	 *	<li><b>$theOffset</b>: The offset to manage.
	 *	<li><b>$theValue</b>: This parameter represents the data element to be set, or the
	 *		index to the data element to be deleted or retrieved:
	 *	 <ul>
	 *		<li><i>NULL</i>: This value is relevant only when retrieving and deleting: it
	 *			means that we want to retrieve or delete the whole list.
	 *		<li><i>array</i>: This value indicates that we want to operate on a list of
	 *			values: the method will be recursed with these values using the other
	 *			provided parameters. An ArrayObject is not considered an array.
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
	 *		<li><i>TRUE</i>: Return the element or list <i>before</i> it was eventually
	 *			modified.
	 *		<li><i>FALSE</i>: Return the element or list <i>after</i> it was eventually
	 *			modified.
	 *	 </ul>
	 *	<li><b>$useArray</b>: If <i>TRUE</i> and the
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
					= $this->_ManageArrayOffset
						( $theOffset, $value, $theOperation, $getOld );
			
			return $result;															// ==>
		
		} // Multiple parameters.
		
		//
		// Save current offset.
		//
		$list = $this->offsetGet( $theOffset );
		if( $list !== NULL )
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
		
		} // Has data.
		
		//
		// Return data.
		//
		if( $theOperation === NULL )
		{
			//
			// Handle list.
			//
			if( $theValue === NULL )
				return $list;														// ==>
			
			//
			// Handle element.
			//
			if( $list !== NULL )
				return $save;														// ==>
			
			return NULL;															// ==>
		
		} // Return data.
		
		//
		// Delete data.
		//
		if( $theOperation === FALSE )
		{
			//
			// Handle data.
			//
			if( $list !== NULL )
			{
				//
				// Handle list.
				//
				if( $theValue === NULL )
				{
					//
					// Delete offset.
					//
					$this->offsetUnset( $theOffset );
					
					if( $getOld )
						return $list;												// ==>
				
				} // Handle list.
				
				//
				// Handle element.
				//
				elseif( $save !== NULL )
				{
					//
					// Delete element.
					//
					unset( $match[ $idx ] );
					
					//
					// Update list.
					//
					if( count( $match ) )
						$this->offsetSet( $theOffset, array_values( $match ) );
					
					//
					// Delete list.
					//
					else
						$this->offsetUnset( $theOffset );
					
					if( $getOld )
						return $save;												// ==>
				
				} // Handle element.
			
			} // Has data.
			
			return NULL;															// ==>
		
		} // Delete data.
		
		//
		// Skip no data.
		//
		if( $theValue === NULL )
			return $list;															// ==>
		
		//
		// Add/replace element.
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
			$this->offsetSet( $theOffset, array_values( $match ) );
		
		} // Had data.
		
		//
		// Add first element.
		//
		else
			$this->offsetSet( $theOffset, array( $theValue ) );
		
		if( $getOld )
			return $save;															// ==>
		
		return $theValue;															// ==>
	
	} // _ManageArrayOffset.

	 
	/*===================================================================================
	 *	_ManageTypedOffset																*
	 *==================================================================================*/

	/**
	 * Manage a typed offset.
	 *
	 * A typed offset is structured as follows:
	 *
	 * <ul>
	 *	<li><i>Type</i>: This offset contains a scalar which determines the type or category
	 *		of the element. This offset may be omitted.
	 *	<li><i>Data</i>: This offset contains the element data, in this method we treat it
	 *		as a scalar. This offset may not be omitted.
	 * </ul>
	 *
	 * No two elements of the list can share the same type or category. You may have only
	 * one element without type or category, this one may be considered the default element.
	 *
	 * The method accepts the following parameters:
	 *
	 * <ul>
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
	 *			items, or adding/replacing the items; in this last case, this means that the
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
	 *		<li><i>TRUE</i>: Return the element or list <i>before</i> it was eventually
	 *			modified.
	 *		<li><i>FALSE</i>: Return the element or list <i>after</i> it was eventually
	 *			modified.
	 *	 </ul>
	 * </ul>
	 *
	 * @param string				$theMainOffset		Main offset.
	 * @param string				$theTypeOffset		Type offset.
	 * @param string				$theDataOffset		Data offset.
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
	protected function _ManageTypedOffset( $theMainOffset, $theTypeOffset, $theDataOffset,
										   $theType = NULL, $theData = NULL,
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
							  array( 'Types offset' => $theTypeOffset,
									 'Type' => $theType,
									 'Data offset' => $theDataOffset,
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
			$data = ( $add ) ? reset( $theData ) : $theData;
			while( $count-- )
			{
				//
				// Recurse.
				//
				$result[]
					= $this->_ManageTypedOffset
						( $theMainOffset, $theTypeOffset, $theDataOffset,
						  $type, $data, $getOld );
				
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
		// Save offset.
		//
		$list = $this->offsetGet( $theMainOffset );
		
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
				   && array_key_exists( $theTypeOffset, $element )
				   && ($element[ $theTypeOffset ] == (string) $theType) )
				 || ( ($theType === NULL)
				   && (! array_key_exists( $theTypeOffset, $element )) ) )
				{
					$idx = $key;
					$save = $element[ $theDataOffset ];
					break;													// =>
				
				} // Matched.
			
			} // Iterating offset elements.
		
		} // Has data.
		
		//
		// Return data.
		//
		if( $theData === NULL )
			return $save;															// ==>
		
		//
		// Delete data.
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
					$this->offsetSet( $theMainOffset, array_values( $list ) );
				
				//
				// Delete offset.
				//
				else
					$this->offsetUnset( $theMainOffset );
			
			} // Matched category.
			
			if( $getOld )
				return $save;														// ==>
			
			return NULL;															// ==>
		
		} // Delete.
		
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
			$this->offsetSet( $theMainOffset, array( $element ) );
		
		//
		// Handle existing data.
		//
		else
		{
			//
			// Replace category.
			//
			if( $idx !== NULL )
				$list[ $idx ][ $theDataOffset ] = $theData;
			
			//
			// Add new category.
			//
			else
				$list[] = $element;
			
			//
			// Update offset.
			//
			$this->offsetSet( $theMainOffset, $list );
		
		} // Has data.
		
		if( $getOld )
			return $save;															// ==>
		
		return $theData;															// ==>
	
	} // _ManageTypedOffset.

	 
	/*===================================================================================
	 *	_ManageTypedArrayOffset															*
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
	 * @param string				$theMainOffset		Offset.
	 * @param mixed					$theTypeOffset		Index offset.
	 * @param string				$theDataOffset		Data offset.
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
	protected function _ManageTypedArrayOffset( $theMainOffset,
												$theTypeOffset, $theDataOffset,
												$theType, $theData = NULL,
												$theOperation = NULL,
												$getOld = FALSE )
	{
		//
		// Save offset.
		//
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
				   && $element->offsetExists( $theTypeOffset )
				   && ($element->offsetGet( $theTypeOffset ) == (string) $theType) )
				 || ( ($theType === NULL)
				   && (! $element->offsetExists( $theTypeOffset )) ) )
				{
					$idx = $key;
					$save = $element;
					break;													// =>
				
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
					return $save[ $theDataOffset ];									// ==>

				return $this->_ManageArrayOffset
					( $save, $theDataOffset, $theData, $theOperation, $getOld );	// ==>
			
			} // Has data.
			
			return NULL;															// ==>
		
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
					$result= $this->_ManageArrayOffset
						( $save, $theDataOffset, $theData, $theOperation, $getOld );
					
					//
					// Update category.
					//
					if( $save->offsetExists( $theDataOffset ) )
					{
						//
						// Update offset.
						//
						$list[ $idx ] = $save;
						$this->offsetSet( $theMainOffset, $list );
					
					} // Has data elements left.
					
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
							$this->offsetSet( $theMainOffset, array_values( $list ) );
						
						//
						// Delete offset.
						//
						else
							$this->offsetUnset( $theMainOffset );
					
					} // Has no data elements left.
					
					return $result;													// ==>
				
				} // Provided data element.
				
				//
				// Remove from list.
				//
				unset( $list[ $idx ] );
				
				//
				// Update offset.
				//
				if( count( $list ) )
					$this->offsetSet( $theMainOffset, array_values( $list ) );
				
				//
				// Delete offset.
				//
				else
					$this->offsetUnset( $theMainOffset );
				
				if( $getOld )
					return $save;													// ==>
			
			} // Matched.
			
			return NULL;															// ==>
		
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
			$result = $this->_ManageArrayOffset
						( $save, $theDataOffset, $theData, $theOperation, $getOld );
			
			//
			// Set offset.
			//
			$this->offsetSet( $theMainOffset, array( $save ) );
		
		} // No categories.
		
		//
		// Add new category.
		//
		elseif( $save === NULL )
		{
			//
			// Create element.
			//
			$save = Array();
			if( $theType !== NULL )
				$save[ $theTypeOffset ] = $theType;
			$result = $this->_ManageArrayOffset
						( $save, $theDataOffset, $theData, $theOperation, $getOld );
			
			//
			// Add to list.
			//
			$list[] = $save;
			
			//
			// Set offset.
			//
			$this->offsetSet( $theMainOffset, $list );
		
		} // New category.
			
		//
		// Update category.
		//
		else
		{
			//
			// Update element.
			//
			$result = $this->_ManageArrayOffset
						( $save, $theDataOffset, $theData, $theOperation, $getOld );
			
			//
			// Set offset.
			//
			$list[ $idx ] = $save;
			$this->offsetSet( $theMainOffset, $list );
		
		} // Matched category.
		
		return $result;																// ==>
	
	} // _ManageTypedArrayOffset.

	 
	/*===================================================================================
	 *	_ManageTypedKindArrayOffset															*
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
	protected function _ManageTypedKindArrayOffset( $theOffset,
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
	
	} // _ManageTypedKindArrayOffset.

	 

} // class CArrayObject.


?>
