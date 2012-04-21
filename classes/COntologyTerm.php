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
 *	@version	1.00 16/04/2012
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
require_once( kPATH_LIBRARY_SOURCE."COntologyTermObject.php" );

/**
 * Generic term.
 *
 * This {@link kTYPE_TERM kind} of {@link COntologyTermObject term} represents a generic
 * term that does not have any specific qualification or function, expect to serve as an
 * aggregator or category for other terms.
 *
 * In this class we enforce the {@link kTYPE_TERM kTYPE_TERM} {@link Kind() kind}, and
 * we ensure that the term has both the {@link Code() code} and {@link Name() name} in order
 * to have an {@link _IsInited() inited} {@link kFLAG_STATE_INITED status}.
 *
 *	@package	MyWrapper
 *	@subpackage	Ontology
 */
class COntologyTerm extends COntologyTermObject
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
 *								PUBLIC MEMBER INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Node																			*
	 *==================================================================================*/

	/**
	 * Manage node references.
	 *
	 * This method can be used to handle the object's {@link kTAG_NODE node} references, it
	 * uses the standard accessor {@link _ManageArrayOffset() method} to manage the list of
	 * nodes that point to this term.
	 *
	 * Each element of this list represents the ID of a node in the ontology.
	 *
	 * For a more thorough reference of how this method works, please consult the
	 * {@link _ManageArrayOffset() _ManageArrayOffset} method, in which the first parameter
	 * will be the constant {@link kTAG_KIND kTAG_KIND}.
	 *
	 * Note that you should only use this method for retrieving information, since
	 * {@link COntologyNode nodes} store automatically this information when
	 * {@link Commit() saved}.
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
	 * @see kTAG_NODE
	 */
	public function Node( $theValue = NULL, $theOperation = NULL, $getOld = FALSE )
	{
		return $this->_ManageArrayOffset
					( kTAG_NODE, $theValue, $theOperation, $getOld );				// ==>

	} // Node.

		

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
	 * We overload this method to enforce the {@link kTYPE_TERM kTYPE_TERM}
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
	 * @see kTYPE_TERM
	 */
	protected function _PrepareCommit( &$theContainer, &$theIdentifier, &$theModifiers )
	{
		//
		// Set namespace kind.
		//
		$this->Kind( kTYPE_TERM, TRUE );
		
		//
		// Call parent method.
		//
		parent::_PrepareCommit( $theContainer, $theIdentifier, $theModifiers );
	
	} // _PrepareCommit.

	 

} // class COntologyTerm.


?>
