<?php

/**
 * <i>CCodedUnitObject</i> class definition.
 *
 * This file contains the class definition of <b>CCodedUnitObject</b> which represents the
 * ancestor of coded unit objects.
 *
 *	@package	MyWrapper
 *	@subpackage	Framework
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 12/04/2012
 */

/*=======================================================================================
 *																						*
 *									CCodedUnitObject.php								*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CPersistentUnitObject.php" );

/**
 * Coded unit object.
 *
 * Objects derived from this class have the following predefined properties:
 *
 * <ul>
 *	<li><i>{@link kTAG_CODE kTAG_CODE}</i>: This attribute represents the current object's
 *		{@link Code() code}, identifier or acronym.
 *	<li><i>{@link kTAG_KIND kTAG_KIND}</i>: This attribute represents the current object's
 *		{@link Kind() kind} or type.
 *	<li><i>{@link kTAG_VALID kTAG_VALID}</i>: This offset holds the
 *		{@link kTAG_ID_NATIVE native} identifier of the valid object. This should be used
 *		when the current object becomes obsolete: instead of deleting it we create a new one
 *		and store in this {@link kTAG_VALID offset} the identifier of the new object that
 *		will replace the current one.
 *	<li><i>{@link kTAG_MOD_STAMP kTAG_MOD_STAMP}</i>: This offset holds the object's last
 *		modification time stamp, this property should be used to mark all objects.
 * </ul>
 *
 * By default, the unique {@link _index() identifier} of the object is its
 * {@link Code() code}, which is also its {@link _id() id}.
 *
 * Objects of this class require at least the {@link Code() code} {@link kTAG_CODE offset}
 * to have an {@link _IsInited() initialised} {@link kFLAG_STATE_INITED status}.
 *
 *	@package	MyWrapper
 *	@subpackage	Framework
 */
class CCodedUnitObject extends CPersistentUnitObject
{
		

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
	 * We {@link CPersistentObject::__construct() overload} the constructor to initialise
	 * the {@link _IsInited() inited} {@link kFLAG_STATE_INITED flag} if the
	 * {@link Code() code} attribute is set.
	 *
	 * @param mixed					$theContainer		Persistent container.
	 * @param mixed					$theIdentifier		Object identifier.
	 * @param bitfield				$theModifiers		Create modifiers.
	 *
	 * @access public
	 *
	 * @uses _IsInited
	 *
	 * @see kTAG_CODE
	 */
	public function __construct( $theContainer = NULL,
								 $theIdentifier = NULL,
								 $theModifiers = kFLAG_DEFAULT )
	{
		//
		// Call parent method.
		//
		parent::__construct( $theContainer, $theIdentifier, $theModifiers );
		
		//
		// Set inited status.
		//
		$this->_IsInited( $this->offsetExists( kTAG_CODE ) );
		
	} // Constructor.

		

/*=======================================================================================
 *																						*
 *								PUBLIC MEMBER INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Code																			*
	 *==================================================================================*/

	/**
	 * Manage code.
	 *
	 * This method can be used to handle the object's {@link kTAG_CODE code}, it uses the
	 * standard accessor {@link _ManageOffset() method} to manage the
	 * {@link kTAG_CODE offset}.
	 *
	 * The code represents the object's {@link _index() identifier}.
	 *
	 * For a more in-depth reference of this method, please consult the
	 * {@link _ManageOffset() _ManageOffset} method, in which the first parameter will be
	 * the constant {@link kTAG_CODE kTAG_CODE}.
	 *
	 * @param mixed					$theValue			Value.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses _ManageOffset
	 *
	 * @see kTAG_CODE
	 */
	public function Code( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kTAG_CODE, $theValue, $getOld );				// ==>

	} // Code.

	 
	/*===================================================================================
	 *	Kind																			*
	 *==================================================================================*/

	/**
	 * Manage kind.
	 *
	 * This method can be used to handle the object's {@link kTAG_KIND kinds}, it uses the
	 * standard accessor {@link _ManageArrayOffset() method} to manage the list of kinds.
	 *
	 * Each element of this list should indicate a function or quality of the current
	 * object
	 *
	 * For a more thorough reference of how this method works, please consult the
	 * {@link _ManageArrayOffset() _ManageArrayOffset} method, in which the first parameter
	 * will be the constant {@link kTAG_KIND kTAG_KIND}.
	 *
	 * @param mixed					$theValue			Value or index.
	 * @param mixed					$theOperation		Operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses _ManageArrayOffset
	 *
	 * @see kTAG_KIND
	 */
	public function Kind( $theValue = NULL, $theOperation = NULL, $getOld = FALSE )
	{
		return $this->_ManageArrayOffset
					( kTAG_KIND, $theValue, $theOperation, $getOld );				// ==>

	} // Kind.

	 
	/*===================================================================================
	 *	Valid																			*
	 *==================================================================================*/

	/**
	 * Manage valid reference.
	 *
	 * This method can be used to handle the valid object's
	 * {@link kTAG_ID_NATIVE identifier}, it uses the standard accessor
	 * {@link _ManageOffset() method} to manage the {@link kTAG_VALID offset}.
	 *
	 * Objects derived from this class should be persistent, in other words, it is not an
	 * option to delete such objects: by creating a new object and referencing it from the
	 * old one, we maintain the original reference and point to the valid object.
	 *
	 * For a more in-depth reference of this method, please consult the
	 * {@link _ManageOffset() _ManageOffset} method, in which the first parameter will be
	 * the constant {@link kTAG_VALID kTAG_VALID}.
	 *
	 * @param mixed					$theValue			Value.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses _ManageOffset
	 *
	 * @see kTAG_VALID
	 */
	public function Valid( $theValue = NULL, $getOld = FALSE )
	{
		//
		// Check identifier.
		//
		if( ($theValue !== NULL)
		 && ($theValue !== FALSE) )
		{
			//
			// Handle arrays or array objects, excluding data types
			//
			if( (! $theValue instanceof CDataType)
			 && ( is_array( $theValue )
			   || ($theValue instanceof ArrayObject) ) )
			{
				//
				// Check native identifier.
				//
				if( array_key_exists( kTAG_ID_NATIVE, (array) $theValue ) )
					$theValue = $theValue[ kTAG_ID_NATIVE ];
			
				//
				// Check reference identifier.
				//
				if( array_key_exists( kTAG_ID_REFERENCE, (array) $theValue ) )
					$theValue = $theValue[ kTAG_ID_REFERENCE ];
			
			} // Not an identifier.
		
		} // Provided new value.
		
		return $this->_ManageOffset( kTAG_VALID, $theValue, $getOld );				// ==>

	} // Valid.

	 
	/*===================================================================================
	 *	Stamp																			*
	 *==================================================================================*/

	/**
	 * Manage object time stamp.
	 *
	 * This method can be used to manage the object {@link kTAG_MOD_STAMP time-stamp}, or
	 * the date in which the last modification was made on the object, it uses the standard
	 * accessor {@link _ManageOffset() method} to manage the {@link kTAG_MOD_STAMP offset}:
	 *
	 * <ul>
	 *	<li><b>$theValue</b>: The value or operation:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the current value.
	 *		<li><i>FALSE</i>: Delete the value.
	 *		<li><i>other</i>: Set value.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value <i>before</i> it was eventually modified.
	 *		<li><i>FALSE</i>: Return the value <i>after</i> it was eventually modified.
	 *	 </ul>
	 * </ul>
	 *
	 * @param NULL|FALSE|string		$theValue			Entity last modification date.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses _ManageOffset
	 *
	 * @see kTAG_MOD_STAMP
	 */
	public function Stamp( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kTAG_MOD_STAMP, $theValue, $getOld );			// ==>

	} // Stamp.

		

/*=======================================================================================
 *																						*
 *								PUBLIC ARRAY ACCESS INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	offsetSet																		*
	 *==================================================================================*/

	/**
	 * Set a value for a given offset.
	 *
	 * We overload this method to manage the {@link _IsInited() inited}
	 * {@link kFLAG_STATE_INITED status}: this is set if {@link kTAG_CODE code} property is
	 * set.
	 *
	 * @param string				$theOffset			Offset.
	 * @param string|NULL			$theValue			Value to set at offset.
	 *
	 * @access public
	 */
	public function offsetSet( $theOffset, $theValue )
	{
		//
		// Call parent method.
		//
		parent::offsetSet( $theOffset, $theValue );
		
		//
		// Set inited flag.
		//
		if( $theValue !== NULL )
			$this->_IsInited( $this->offsetExists( kTAG_CODE ) );
	
	} // offsetSet.

	 
	/*===================================================================================
	 *	offsetUnset																		*
	 *==================================================================================*/

	/**
	 * Reset a value for a given offset.
	 *
	 * We overload this method to manage the {@link _IsInited() inited}
	 * {@link kFLAG_STATE_INITED status}: this is set if {@link kTAG_CODE code} property is
	 * set.
	 *
	 * @param string				$theOffset			Offset.
	 *
	 * @access public
	 */
	public function offsetUnset( $theOffset )
	{
		//
		// Call parent method.
		//
		parent::offsetUnset( $theOffset );
		
		//
		// Set inited flag.
		//
		$this->_IsInited( $this->offsetExists( kTAG_CODE ) );
	
	} // offsetUnset.

		

/*=======================================================================================
 *																						*
 *									STATIC INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	ValidObject																		*
	 *==================================================================================*/

	/**
	 * Return a valid object.
	 *
	 * This method can be used to instantiate a valid object, this means that if we provide
	 * the identifier of an expired or obsolete object that has a reference to a
	 * {@link Valid() valid} object, this method will return the valid one.
	 *
	 * The method expects the same parameters as the {@link NewObject() NewObject} static
	 * method. If the {@link Valid() valid} chain is recursive, the method will raise an
	 * exception.
	 *
	 * The method will return the first object that does not have a {@link Valid() valid}
	 * {@link kTAG_VALID reference}, or <i>NULL</i> if the object was not found.
	 *
	 * @param mixed					$theContainer		Persistent container.
	 * @param mixed					$theIdentifier		Object identifier.
	 * @param bitfield				$theModifiers		Load modifiers.
	 *
	 * @static
	 * @return CCodedUnitObject
	 */
	static function ValidObject( $theContainer,
								 $theIdentifier,
								 $theModifiers = kFLAG_DEFAULT )
	{
		//
		// Init local storage.
		//
		$chain = Array();
		
		//
		// Iterate.
		//
		do
		{
			//
			// Instantiate object.
			//
			$object = self::NewObject( $theContainer, $theIdentifier, $theModifiers );
			
			//
			// Handle object.
			//
			if( $object !== NULL )
			{
				//
				// Get identifier.
				//
				$id = (string) $theIdentifier;
				
				//
				// Check recursion.
				//
				if( in_array( $id, $chain ) )
					throw new CException
						( "Recursive valid objects chain",
						  kERROR_INVALID_STATE,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Chain' => $chain ) );							// !@! ==>
				
				//
				// Add to chain.
				//
				$chain[] = $id;
				
				//
				// Copy valid.
				//
				$theIdentifier = $object[ kTAG_VALID ];
				
			} // Found entity.
			
			//
			// Catch missing chain link.
			//
			elseif( count( $chain ) )
				throw new CException
					( "Object not found in valid chain",
					  kERROR_INVALID_STATE,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Identifier' => $theIdentifier ) );				// !@! ==>
		
		} while( ($object !== NULL) && ($theIdentifier !== NULL) );
		
		return $object;																// ==>
		
	} // ValidObject.

		

/*=======================================================================================
 *																						*
 *							PROTECTED IDENTIFICATION INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_index																			*
	 *==================================================================================*/

	/**
	 * Return the object's unique index.
	 *
	 * In this class we consider the {@link kTAG_CODE code} to be the object's unique
	 * {@link kTAG_ID_NATIVE identifier}.
	 *
	 * @access protected
	 * @return string
	 */
	protected function _index()									{	return $this->Code();	}

	 

} // class CCodedUnitObject.


?>
