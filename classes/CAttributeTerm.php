<?php

/**
 * <i>CAttributeTerm</i> class definition.
 *
 * This file contains the class definition of <b>CAttributeTerm</b> which represents an
 * attribute {@link COntologyTerm term}.
 *
 *	@package	MyWrapper
 *	@subpackage	Ontology
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 16/04/2012
 */

/*=======================================================================================
 *																						*
 *									CAttributeTerm.php									*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."COntologyTerm.php" );

/**
 * Attribute term.
 *
 * This {@link kTAG_TERM_ATTRIBUTE kind} of {@link COntologyTerm term} represents an
 * attribute {@link COntologyTerm term}. In general, it represents a term that is mainly
 * used to qas an attribute of other terms.
 *
 * In this class we enforce the {@link kTAG_TERM_ATTRIBUTE kTAG_TERM_ATTRIBUTE}
 * {@link Kind() kind}.
 *
 *	@package	MyWrapper
 *	@subpackage	Ontology
 */
class CAttributeTerm extends COntologyTerm
{
		

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
	 * {@link kTAG_TERM_ATTRIBUTE kTAG_TERM_ATTRIBUTE} {@link Kind() kind}, note that
	 * we call the {@link COntologyTermObject COntologyTermObject} version of this method
	 * instead of the {@link COntologyTerm parent} one.
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
	 * @see kTAG_TERM_ATTRIBUTE
	 */
	protected function _PrepareCommit( &$theContainer, &$theIdentifier, &$theModifiers )
	{
		//
		// Set namespace kind.
		//
		$this->Kind( kTAG_TERM_ATTRIBUTE, TRUE );
		
		//
		// Call parent method.
		//
		COntologyTermObject::_PrepareCommit( $theContainer, $theIdentifier, $theModifiers );
	
	} // _PrepareCommit.

	 

} // class CAttributeTerm.


?>
