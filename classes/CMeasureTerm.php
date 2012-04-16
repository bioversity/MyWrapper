<?php

/**
 * <i>CMeasureTerm</i> class definition.
 *
 * This file contains the class definition of <b>CMeasureTerm</b> which represents a measure
 * {@link COntologyTerm term}.
 *
 *	@package	MyWrapper
 *	@subpackage	Ontology
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 16/04/2012
 */

/*=======================================================================================
 *																						*
 *									CMeasureTerm.php									*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."COntologyTerm.php" );

/**
 * Scale term.
 *
 * This {@link kTAG_TERM_MEASURE kind} of {@link COntologyTerm term} represents a measure
 * term, a term that defines a measurement scale which refers to a {@link kTAG_UNIT unit}
 * {@link COntologyTerm term} and features a data {@link kTAG_TYPE type}; such terms can be
 * used to annotate data elements.
 *
 * These two properties are managed respectively by the {@link Unit() Unit} and
 * {@link Type() Type} methods, the {@link Type() type} {@link kTAG_TYPE property} must be
 * present for object's {@link kFLAG_STATE_INITED status} to be
 * {@link _IsInited() initialised}.
 *
 * The class also enforces the {@link kTAG_TERM_MEASURE kTAG_TERM_MEASURE}
 * {@link Kind() kind}.
 *
 *	@package	MyWrapper
 *	@subpackage	Ontology
 */
class CMeasureTerm extends COntologyTerm
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
	 * We {@link COntologyTermObject::__construct() overload} the constructor to initialise
	 * the {@link _IsInited() inited} {@link kFLAG_STATE_INITED flag} if the
	 * {@link Type() type} attribute is set.
	 *
	 * @param mixed					$theContainer		Persistent container.
	 * @param mixed					$theIdentifier		Object identifier.
	 * @param bitfield				$theModifiers		Create modifiers.
	 *
	 * @access public
	 *
	 * @uses _IsInited
	 *
	 * @see kTAG_TERM_ENUM
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
		$this->_IsInited( $this->_IsInited() &&
						  $this->offsetExists( kTAG_TYPE ) );
		
	} // Constructor.

		

/*=======================================================================================
 *																						*
 *								PUBLIC MEMBER INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Type																			*
	 *==================================================================================*/

	/**
	 * Manage data type.
	 *
	 * This method can be used to manage the measure data {@link kTAG_TYPE type}, it uses
	 * the standard accessor {@link _ManageOffset() method} to manage the
	 * {@link kTAG_TYPE offset}:
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
	 * @param NULL|FALSE|string		$theValue			Data type tag.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 */
	public function Type( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kTAG_TYPE, $theValue, $getOld );				// ==>

	} // Type.

	 
	/*===================================================================================
	 *	Unit																			*
	 *==================================================================================*/

	/**
	 * Manage valid reference.
	 *
	 * This method can be used to handle the measure {@link kTAG_UNIT unit}, it uses the
	 * standard accessor {@link _ManageOffset() method} to manage the
	 * {@link kTAG_UNIT offset}.
	 *
	 * The value provided to this property should be the {@link _id() identifier} of a term
	 * hat defines a unit.
	 *
	 * For a more in-depth reference of this method, please consult the
	 * {@link _ManageOffset() _ManageOffset} method, in which the first parameter will be
	 * the constant {@link kOFFSET_VALID kOFFSET_VALID}.
	 *
	 * In this class we feed the value to the{@link _CheckReference() _CheckReference}
	 * method that will take care of handling object references.
	 *
	 * @param mixed					$theValue			Value.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses _ManageOffset
	 *
	 * @see kTAG_UNIT
	 */
	public function Unit( $theValue = NULL, $getOld = FALSE )
	{
		//
		// Check identifier.
		//
		if( ($theValue !== NULL)
		 && ($theValue !== FALSE) )
			$theValue = $this->_CheckReference( $theValue );
		
		return $this->_ManageOffset( kTAG_UNIT, $theValue, $getOld );				// ==>

	} // Unit.

		

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
	 * {@link kFLAG_STATE_INITED status}: this is set if {@link kTAG_TYPE type} property is
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
			$this->_IsInited( $this->_IsInited() &&
							  $this->offsetExists( kTAG_TYPE ) );
	
	} // offsetSet.

	 
	/*===================================================================================
	 *	offsetUnset																		*
	 *==================================================================================*/

	/**
	 * Reset a value for a given offset.
	 *
	 * We overload this method to manage the {@link _IsInited() inited}
	 * {@link kFLAG_STATE_INITED status}: this is set if {@link kTAG_TYPE type} property is
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
		$this->_IsInited( $this->_IsInited() &&
						  $this->offsetExists( kTAG_TYPE ) );
	
	} // offsetUnset.

		

/*=======================================================================================
 *																						*
 *								PROTECTED PERSISTENCE UTILITIES							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_PrepareCommit																	*
	 *==================================================================================*/

	/**
	 * Normalise before a store.
	 *
	 * We overload this method to enforce the
	 * {@link kTAG_TERM_MEASURE kTAG_TERM_MEASURE} {@link Kind() kind}, note that we call
	 * the {@link COntologyTermObject COntologyTermObject} version of this method instead of
	 * the {@link COntologyTerm parent} one.
	 *
	 * @param reference			   &$theContainer		Object container.
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Commit modifiers.
	 *
	 * @access protected
	 *
	 * @throws {@link CException CException}
	 *
	 * @uses Kind()
	 *
	 * @see kTAG_TERM_ENUM
	 */
	protected function _PrepareCommit( &$theContainer, &$theIdentifier, &$theModifiers )
	{
		//
		// Set namespace kind.
		//
		$this->Kind( kTAG_TERM_MEASURE, TRUE );
		
		//
		// Call parent method.
		//
		COntologyTermObject::_PrepareCommit( $theContainer, $theIdentifier, $theModifiers );
	
	} // _PrepareCommit.

	 

} // class CMeasureTerm.


?>
