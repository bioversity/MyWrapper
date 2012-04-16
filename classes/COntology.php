<?php

/**
 * <i>COntology</i> class definition.
 *
 * This file contains the class definition of <b>COntology</b> which represents the root
 * {@link COntologyTerm term} of an ontology.
 *
 *	@package	MyWrapper
 *	@subpackage	Ontology
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 16/04/2012
 */

/*=======================================================================================
 *																						*
 *									COntology.php										*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."COntologyTerm.php" );

/**
 * Ontology term.
 *
 * This {@link kTAG_TERM_ONTOLOGY kind} of {@link COntologyTerm term} represents the
 * root term of an ontology. In general, it can represent the ontology as a whole.
 *
 * In this class we enforce the {@link kTAG_TERM_ONTOLOGY kTAG_TERM_ONTOLOGY}
 * {@link Kind() kind}.
 *
 *	@package	MyWrapper
 *	@subpackage	Ontology
 */
class COntology extends COntologyTerm
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
	 * {@link kTAG_TERM_ONTOLOGY kTAG_TERM_ONTOLOGY} {@link Kind() kind}, note that we
	 * call the {@link COntologyTermObject COntologyTermObject} version of this method instead
	 * of the {@link COntologyTerm parent} one.
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
	 * @see kTAG_TERM_ONTOLOGY
	 */
	protected function _PrepareCommit( &$theContainer, &$theIdentifier, &$theModifiers )
	{
		//
		// Set namespace kind.
		//
		$this->Kind( kTAG_TERM_ONTOLOGY, TRUE );
		
		//
		// Call parent method.
		//
		COntologyTermObject::_PrepareCommit( $theContainer, $theIdentifier, $theModifiers );
	
	} // _PrepareCommit.

	 

} // class COntology.


?>
