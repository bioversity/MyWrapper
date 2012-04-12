<?php

/**
 * <i>CTermNamespace</i> class definition.
 *
 * This file contains the class definition of <b>CTermNamespace</b> which represents the
 * ancestor of namespace term objects.
 *
 *	@package	MyWrapper
 *	@subpackage	Ontology
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 12/04/2012
 */

/*=======================================================================================
 *																						*
 *									CTermNamespace.php									*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."COntologyBaseTerm.php" );

/**
 * Namespace term.
 *
 * This {@link kOFFSET_TERM_NAMESPACE kind} of {@link COntologyBaseTerm term} represents a
 * namespace, or group that qualifies the {@link Code() codes} and {@link Name() names} of
 * the {@link COntologyBaseTerm terms} that belong to this namespace.
 *
 * In this class we enforce the {@link kOFFSET_TERM_NAMESPACE kOFFSET_TERM_NAMESPACE}
 * {@link Kind() kind}.
 *
 *	@package	MyWrapper
 *	@subpackage	Ontology
 */
class CTermNamespace extends COntologyBaseTerm
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
	 * {@link kOFFSET_TERM_NAMESPACE kOFFSET_TERM_NAMESPACE} {@link Kind() kind}.
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
	 * @see kOFFSET_TERM_NAMESPACE
	 */
	protected function _PrepareCommit( &$theContainer, &$theIdentifier, &$theModifiers )
	{
		//
		// Set namespace kind.
		//
		$this->Kind( kOFFSET_TERM_NAMESPACE, TRUE );
		
		//
		// Call parent method.
		//
		parent::_PrepareCommit( $theContainer, $theIdentifier, $theModifiers );
	
	} // _PrepareCommit.

	 

} // class CTermNamespace.


?>
