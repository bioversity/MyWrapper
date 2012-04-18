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
 * This {@link kTYPE_PREDICATE_TERM kind} of {@link COntologyTerm term} represents a
 * predicate {@link COntologyTerm term}. In general, it can represent a term that is mainly
 * used to qualify a relationship.
 *
 * In this class we enforce the {@link kTYPE_PREDICATE_TERM kTYPE_PREDICATE_TERM}
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
	 * {@link kTYPE_PREDICATE_TERM kTYPE_PREDICATE_TERM} {@link Kind() kind}, note that
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
	 * @see kTYPE_PREDICATE_TERM
	 */
	protected function _PrepareCommit( &$theContainer, &$theIdentifier, &$theModifiers )
	{
		//
		// Set namespace kind.
		//
		$this->Kind( kTYPE_PREDICATE_TERM, TRUE );
		
		//
		// Call parent method.
		//
		COntologyTermObject::_PrepareCommit( $theContainer, $theIdentifier, $theModifiers );
	
	} // _PrepareCommit.

	 

} // class CPredicateTerm.


?>
