<?php

/**
 * <i>COntologyTerm</i> class definition.
 *
 * This file contains the class definition of <b>COntologyTerm</b> which represents the
 * ancestor of generic ontology terms.
 *
 *	@package	MyWrapper
 *	@subpackage	Ontology
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 12/04/2012
 */

/*=======================================================================================
 *																						*
 *									COntologyTerm.php									*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."COntologyBaseTerm.php" );

/**
 * Generic term.
 *
 * This {@link kTAG_TERM kind} of {@link COntologyBaseTerm term} represents a generic
 * term that does not have any specific qualification or function, expect to serve as an
 * aggregator or category for other terms.
 *
 * In this class we enforce the {@link kTAG_TERM kTAG_TERM} {@link Kind() kind}, and
 * we ensure that the term has both the {@link Code() code} and {@link Name() name} in order
 * to have an {@link _IsInited() inited} {@link kFLAG_STATE_INITED status}.
 *
 *	@package	MyWrapper
 *	@subpackage	Ontology
 */
class COntologyTerm extends COntologyBaseTerm
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
	 * We {@link CCodedUnitObject::__construct() overload} the constructor to initialise
	 * the {@link _IsInited() inited} {@link kFLAG_STATE_INITED flag} if the
	 * {@link Code() code} and {@link Name() name} attributes are set.
	 *
	 * @param mixed					$theContainer		Persistent container.
	 * @param mixed					$theIdentifier		Object identifier.
	 * @param bitfield				$theModifiers		Create modifiers.
	 *
	 * @access public
	 *
	 * @uses _IsInited
	 *
	 * @see kTAG_CODE kTAG_NAME
	 */
	public function __construct( $theContainer = NULL,
								 $theIdentifier = NULL,
								 $theModifiers = kFLAG_DEFAULT )
	{
		//
		// Call ancestor method.
		//
		parent::__construct( $theContainer, $theIdentifier, $theModifiers );
		
		//
		// Set inited status.
		//
		$this->_IsInited( $this->_IsInited() &&
						  $this->offsetExists( kTAG_NAME ) );
		
	} // Constructor.

		

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
	 * {@link kFLAG_STATE_INITED status}: this is set if the {@link Code() code} and
	 * {@link Name() name} attributes are set.
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
							  $this->offsetExists( kTAG_NAME ) );
	
	} // offsetSet.

	 
	/*===================================================================================
	 *	offsetUnset																		*
	 *==================================================================================*/

	/**
	 * Reset a value for a given offset.
	 *
	 * We overload this method to manage the {@link _IsInited() inited}
	 * {@link kFLAG_STATE_INITED status}: this is set if the {@link Code() code} and
	 * {@link Name() name} attributes are set.
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
		CStatusObject::offsetUnset( $theOffset );
		
		//
		// Set inited flag.
		//
		$this->_IsInited( $this->_IsInited() &&
						  $this->offsetExists( kTAG_NAME ) );
	
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
	 * We overload this method to enforce the {@link kTAG_TERM kTAG_TERM}
	 * {@link Kind() kind}.
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
	 * @see kTAG_TERM
	 */
	protected function _PrepareCommit( &$theContainer, &$theIdentifier, &$theModifiers )
	{
		//
		// Set namespace kind.
		//
		$this->Kind( kTAG_TERM, TRUE );
		
		//
		// Call parent method.
		//
		parent::_PrepareCommit( $theContainer, $theIdentifier, $theModifiers );
	
	} // _PrepareCommit.

	 

} // class COntologyTerm.


?>
