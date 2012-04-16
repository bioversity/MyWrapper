<?php

/**
 * <i>CPersistentUnitObjectCache</i> class definition.
 *
 * This file contains the class definition of <b>CPersistentUnitObjectCache</b> which
 * represents a data cache of {@link CPersistentUnitObject CPersistentUnitObject} objects.
 *
 *	@package	MyWrapper
 *	@subpackage	Persistence
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 14/03/2012
 */

/*=======================================================================================
 *																						*
 *								CPersistentUnitObjectCache.php							*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CArrayObject.php" );

/**
 * Unit objects cache.
 *
 * This class can be used as a cache for {@link CPersistentUnitObject CPersistentUnitObject}
 * objects.
 *
 * Objects of this class can be 
 Since instances of that class have a unique identifier, instances can be
 * retrieved by {@link kTAG_ID identifier}.
 *
 * The cache uses the object's internal array and the element keys are the object's
 * {@link kTAG_ID identifiers} cast to string.
 *
 * The main use of such objects is to 
 *
 * @package		Framework
 * @subpackage	Persistence
 */
class CPersistentUnitObjectCache extends CArrayObject
{
		

/*=======================================================================================
 *																						*
 *								PUBLIC MEMBER INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Item																			*
	 *==================================================================================*/

	/**
	 * Manage cache items.
	 *
	 * This is the only public method offered by this class, it can be used to add, retrieve
	 * and delete cache elements.
	 *
	 * The method accepts the following parameters:
	 *
	 * <ul>
	 *	<li><b>$theObject</b>: Cache element index or element data:
	 *	 <ul>
	 *		<li><i>{@link CPersistentUnitObject CPersistentUnitObject}</i>: If this
	 *			parameter is an instance of that class, it means that you want to add it to
	 *			the cache, or replace the matching object in the cache.
	 *		<li><i>other</i>: Any other value will be cast to string and used as the index
	 *			to the cache element for the operation indicated in the next parameter.
	 *	 </ul>
	 *	<li><b>$theOperation</b>: Operation to be performed:
	 *	 <ul>
	 *		<li><i>NULL</i>: Retrieve the element indexed by the previous parameter, or
	 *			return <i>NULL</i> if not found.
	 *		<li><i>FALSE</i>: Delete the element indexed by the previous parameter.
	 *		<li><i>other</i>: Add the object provided in the first parameter.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the element <i>before</i> it was eventually modified.
	 *		<li><i>FALSE</i>: Return the element <i>after</i> it was eventually modified.
	 *	 </ul>
	 * </ul>
	 *
	 * @param mixed					$theObject			Object or object identifier.
	 * @param mixed					$theOperation		Operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 */
	public function Item( $theObject, $theOperation = NULL, $getOld = FALSE )
	{
		//
		// Retrieve item.
		//
		if( $theOperation === NULL )
			return $this->offsetGet( $theObject );									// ==>

		//
		// Save item.
		//
		$save = $this->offsetGet( $theObject );
		
		//
		// Delete item.
		//
		if( $theOperation === FALSE )
		{
			//
			// Delete item.
			//
			if( $save !== NULL )
				$this->offsetUnset( $theObject );
			
			if( $getOld )
				return $save;														// ==>
			
			return NULL;															// ==>
		
		} // Delete.
		
		//
		// Add or replace.
		//
		$this->offsetSet( $theObject, $theObject );
		
		if( $getOld )
			return $save;															// ==>
		
		return $theObject;															// ==>
		
	} // Item.

		

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
	 * We overload this method to {@link _NormaliseOffset() normalise} the offset.
	 *
	 * @param string				$theOffset			Offset.
	 *
	 * @access public
	 * @return mixed
	 */
	public function offsetGet( $theOffset )
	{
		return parent::offsetGet( $this->_NormaliseOffset( $theOffset ) );			// ==>
	
	} // offsetGet.

	 
	/*===================================================================================
	 *	offsetSet																		*
	 *==================================================================================*/

	/**
	 * Set a value for a given offset.
	 *
	 * We overload this method to ensure that the provided value is an instance derived from
	 * {@link CPersistentUnitObject CPersistentUnitObject} and we
	 * {@link _NormaliseOffset() normalise} the offset.
	 *
	 * @param string				$theOffset			Offset.
	 * @param string|NULL			$theValue			Value to set at offset.
	 *
	 * @access public
	 */
	public function offsetSet( $theOffset, $theValue )
	{
		//
		// Check value.
		//
		if( $theValue !== NULL )
		{
			//
			// Check data type.
			//
			if( ! $theValue instanceof CPersistentUnitObject )
				throw new CException
						( "Invalid data type",
						  kERROR_INVALID_PARAMETER,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Data' => $theValue ) );						// !@! ==>
		
		} // Adding/replacing.
		
		//
		// Call parent method.
		//
		parent::offsetSet( $this->_NormaliseOffset( $theOffset ), $theValue );
	
	} // offsetSet.

	 
	/*===================================================================================
	 *	offsetUnset																		*
	 *==================================================================================*/

	/**
	 * Reset a value for a given offset.
	 *
	 * We overload this method to {@link _NormaliseOffset() normalise} the offset.
	 *
	 * @param string				$theOffset			Offset.
	 *
	 * @access public
	 */
	public function offsetUnset( $theOffset )
	{
		parent::offsetUnset( $this->_NormaliseOffset( $theOffset ) );
	
	} // offsetUnset.

		

/*=======================================================================================
 *																						*
 *									PROTECTED INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_NormaliseOffset																*
	 *==================================================================================*/

	/**
	 * Normalise offset.
	 *
	 * In this class we handle {@link CPersistentUnitObject CPersistentUnitObject}
	 * instances, so we accept providing offsets as the instance itself, but in that case
	 * we need to use the object's {@link kTAG_ID identifier}.
	 *
	 * @param string				$theOffset			Offset.
	 *
	 * @access protected
	 * @return string
	 */
	protected function _NormaliseOffset( $theOffset )
	{
		//
		// Normalise offset.
		//
		if( $theOffset instanceof CPersistentUnitObject )
		{
			//
			// Get identifier.
			//
			$offset = $theOffset->offsetGet( kTAG_ID );
			
			//
			// Check object identifier.
			//
			if( $offset === NULL )
				throw new CException
						( "Missing object identifier",
						  kERROR_INVALID_PARAMETER,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Offset' => $theOffset ) );					// !@! ==>
			
			return (string) $offset;												// ==>
		
		} // Checking offset.
		
		return (string) $theOffset;													// ==>
	
	} // _NormaliseOffset.

	 

} // class CPersistentUnitObjectCache.


?>
