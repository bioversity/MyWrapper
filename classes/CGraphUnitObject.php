<?php

/**
 * <i>CGraphUnitObject</i> class definition.
 *
 * This file contains the class definition of <b>CGraphUnitObject</b> which represents the
 * ancestor of graph unit objects.
 *
 *	@package	MyWrapper
 *	@subpackage	Framework
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 12/04/2012
 */

/*=======================================================================================
 *																						*
 *								CGraphUnitObject.php									*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CCodedUnitObject.php" );

/**
 * Graph unit object.
 *
 * This class represents the ancestor of {@link CCodedUnitObject coded} unit objects that
 * exist within a graph, they add two default properties:
 *
 * <ul>
 *	<li><i>{@link kTAG_LINK_IN kTAG_LINK_IN}</i>: This property contains the list of
 *		{@link RelatedFrom() incoming} references or links, it is a list of object
 *		{@link kOFFSET_ID identifiers} or object references.
 *	<li><i>{@link kTAG_LINK_OUT kTAG_LINK_OUT}</i>: This property contains the list of
 *		{@link RelateTo() outgoing} references or links, it is a list of object
 *		{@link kOFFSET_ID identifiers} or object references.
 * </ul>
 *
 *	@package	MyWrapper
 *	@subpackage	Framework
 */
class CGraphUnitObject extends CCodedUnitObject
{
		

/*=======================================================================================
 *																						*
 *								PUBLIC MEMBER INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	RelatedFrom																		*
	 *==================================================================================*/

	/**
	 * Manage incoming references.
	 *
	 * This method can be used to handle {@link kTAG_LINK_IN incoming} references, it
	 * accepts the following parameters:
	 *
	 * <ul>
	 *	<li><b>$theValue</b>: Reference or object. This parameter represents the reference,
	 *		it may be a scalar, representing either the referenced object
	 *		{@link kOFFSET_ID identifier}, a structure representing an object reference,
	 *		or the referenced object itself.
	 *	<li><b>$theOperation</b>: The operation to perform:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the element matched by the previous parameters.
	 *		<li><i>FALSE</i>: Delete the element matched by the previous parameters and
	 *			return it.
	 *		<li><i>other</i>: Any other value means that we want to add to the list the
	 *			element provided in the previous parameters, either appending it if there
	 *			was no matching element, or by replacing a matching element. The method will
	 *			return either the replaced element or the new one.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return when deleting or
	 *		replacing:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the deleted or replaced element.
	 *		<li><i>FALSE</i>: Return the replacing element or <i>NULL</i> when deleting.
	 *	 </ul>
	 * </ul>
	 *
	 * @param mixed					$theValue			Reference value.
	 * @param mixed					$theOperation		Operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses _ManageObjectList()
	 *
	 * @see kTAG_LINK_IN
	 */
	public function RelatedFrom( $theValue, $theOperation = NULL, $getOld = FALSE )
	{
		return $this->_ManageObjectList
				( kTAG_LINK_IN, $theValue, $theOperation, $getOld );				// ==>

	} // RelatedFrom.

	 
	/*===================================================================================
	 *	RelateTo																		*
	 *==================================================================================*/

	/**
	 * Manage outgoing references.
	 *
	 * This method can be used to handle {@link kTAG_LINK_OUT outgoing} references, it
	 * accepts the following parameters:
	 *
	 * <ul>
	 *	<li><b>$theValue</b>: Reference or object. This parameter represents the reference,
	 *		it may be a scalar, representing either the referenced object
	 *		{@link kOFFSET_ID identifier}, a structure representing an object reference,
	 *		or the referenced object itself.
	 *	<li><b>$theOperation</b>: The operation to perform:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the element matched by the previous parameters.
	 *		<li><i>FALSE</i>: Delete the element matched by the previous parameters and
	 *			return it.
	 *		<li><i>other</i>: Any other value means that we want to add to the list the
	 *			element provided in the previous parameters, either appending it if there
	 *			was no matching element, or by replacing a matching element. The method will
	 *			return either the replaced element or the new one.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return when deleting or
	 *		replacing:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the deleted or replaced element.
	 *		<li><i>FALSE</i>: Return the replacing element or <i>NULL</i> when deleting.
	 *	 </ul>
	 * </ul>
	 *
	 * @param mixed					$theValue			Reference value.
	 * @param mixed					$theOperation		Operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses _ManageObjectList()
	 *
	 * @see kTAG_LINK_OUT
	 */
	public function RelateTo( $theValue, $theOperation = NULL, $getOld = FALSE )
	{
		return $this->_ManageObjectList
				( kTAG_LINK_OUT, $theValue, $theOperation, $getOld );				// ==>

	} // RelateTo.

		

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
	 * We overload this method to check if the object in {@link _IsInited() initialised}, if
	 * this is not the case we raise an exception.
	 *
	 * We also scan the {@link kTAG_LINK_IN incoming} and {@link kTAG_LINK_OUT outgoing}
	 * references to commit any elements that are actual instances and convert them to
	 * references: we discriminate such elements by selecting only objects derived from
	 * the {@link CPersistentUnitObject CPersistentUnitObject} class.
	 *
	 * @param reference			   &$theContainer		Object container.
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Commit modifiers.
	 *
	 * @access protected
	 *
	 * @throws {@link CException CException}
	 *
	 * @see kERROR_OPTION_MISSING
	 */
	protected function _PrepareCommit( &$theContainer, &$theIdentifier, &$theModifiers )
	{
		//
		// Call parent method.
		//
		parent::_PrepareCommit( $theContainer, $theIdentifier, $theModifiers );
		
		//
		// Handle incoming references.
		//
		$this->_PrepareReferenceList( $theContainer, kTAG_LINK_IN, $theModifiers );
		
		//
		// Handle outgoing references.
		//
		$this->_PrepareReferenceList( $theContainer, kTAG_LINK_OUT, $theModifiers );
		
	} // _PrepareCommit.

	 

} // class CGraphUnitObject.


?>
