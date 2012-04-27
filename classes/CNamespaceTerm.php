<?php

/**
 * <i>CNamespaceTerm</i> class definition.
 *
 * This file contains the class definition of <b>CNamespaceTerm</b> which represents the
 * ancestor of namespace term objects.
 *
 *	@package	MyWrapper
 *	@subpackage	Ontology
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 16/04/2012
 */

/*=======================================================================================
 *																						*
 *									CNamespaceTerm.php									*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."COntologyTermObject.php" );

/**
 * Namespace term.
 *
 * This {@link kTYPE_NAMESPACE kind} of {@link COntologyTermObject term} represents a
 * namespace, or group that qualifies the {@link Code() codes} and {@link Name() names} of
 * the {@link COntologyTermObject terms} that belong to this namespace.
 *
 * In this class we enforce the {@link kTYPE_NAMESPACE kTYPE_NAMESPACE}
 * {@link Kind() kind}.
 *
 * Note that we inherit from {@link COntologyTermObject COntologyTermObject} because this
 * class does not require the object to have the {@link Name() name} in order to have an
 * {@link _IsInited() inited} {@link kFLAG_STATE_INITED status}.
 *
 *	@package	MyWrapper
 *	@subpackage	Ontology
 */
class CNamespaceTerm extends COntologyTermObject
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
	 * {@link kTYPE_NAMESPACE kTYPE_NAMESPACE} {@link Kind() kind}.
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
	 * @see kTYPE_NAMESPACE
	 */
	protected function _PrepareCommit( &$theContainer, &$theIdentifier, &$theModifiers )
	{
		//
		// Set namespace kind.
		//
		$this->Kind( kTYPE_NAMESPACE, TRUE );
		
		//
		// Call parent method.
		//
		parent::_PrepareCommit( $theContainer, $theIdentifier, $theModifiers );
	
	} // _PrepareCommit.

	 

} // class CNamespaceTerm.


?>
