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
 *	@version	1.00 16/04/2012
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
require_once( kPATH_LIBRARY_SOURCE."CRelatedUnitObject.php" );

/**
 * Coded unit object.
 *
 * Objects derived from this class have the {@link Code() code} predefined
 * {@link kTAG_CODE properties}, by default, the unique {@link _index() identifier} of the
 * object is its {@link Code() code}, which is also its {@link _id() id}.
 *
 * Objects of this class require at least the {@link Code() code} {@link kTAG_CODE offset}
 * to have an {@link _IsInited() initialised} {@link kFLAG_STATE_INITED status}.
 *
 *	@package	MyWrapper
 *	@subpackage	Framework
 */
class CCodedUnitObject extends CRelatedUnitObject
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
	 * standard accessor {@link CAttribute::ManageOffset() method} to manage the
	 * {@link kTAG_CODE offset}.
	 *
	 * The code represents the object's {@link _index() identifier}.
	 *
	 * For a more in-depth reference of this method, please consult the
	 * {@link CAttribute::ManageOffset() ManageOffset} method, in which the first parameter
	 * will be the constant {@link kTAG_CODE kTAG_CODE}.
	 *
	 * @param mixed					$theValue			Value.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses CAttribute::ManageOffset()
	 *
	 * @see kTAG_CODE
	 */
	public function Code( $theValue = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageOffset( $this, kTAG_CODE, $theValue, $getOld );	// ==>

	} // Code.

		

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
	 * {@link kTAG_LID identifier}.
	 *
	 * @access protected
	 * @return string
	 */
	protected function _index()									{	return $this->Code();	}

	 

} // class CCodedUnitObject.


?>
