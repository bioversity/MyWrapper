<?php

/**
 * <i>CPredicateTerm</i> class definition.
 *
 * This file contains the class definition of <b>CPredicateTerm</b> which represents a
 * predicate {@link COntologyTerm term}.
 *
 *	@package	MyWrapper
 *	@subpackage	Ontology
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 16/04/2012
 */

/*=======================================================================================
 *																						*
 *									CPredicateTerm.php									*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."COntologyTerm.php" );

/**
 * Predicate term.
 *
 * This {@link kTAG_TERM_PREDICATE kind} of {@link COntologyTerm term} represents a
 * predicate {@link COntologyTerm term}. In general, it can represent a term that is mainly
 * used to qualify a relationship.
 *
 * In this class we enforce the {@link kTAG_TERM_PREDICATE kTAG_TERM_PREDICATE}
 * {@link Kind() kind}.
 *
 *	@package	MyWrapper
 *	@subpackage	Ontology
 */
class CPredicateTerm extends COntologyTerm
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
	 * {@link kTAG_TERM_PREDICATE kTAG_TERM_PREDICATE} {@link Kind() kind}, note that
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
	 * @see kTAG_TERM_PREDICATE
	 */
	protected function _PrepareCommit( &$theContainer, &$theIdentifier, &$theModifiers )
	{
		//
		// Set namespace kind.
		//
		$this->Kind( kTAG_TERM_PREDICATE, TRUE );
		
		//
		// Call parent method.
		//
		COntologyTermObject::_PrepareCommit( $theContainer, $theIdentifier, $theModifiers );
	
	} // _PrepareCommit.

	 

} // class CPredicateTerm.


?>
