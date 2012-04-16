<?php

/**
 * <i>CRelatedUnitObject</i> class definition.
 *
 * This file contains the class definition of <b>CRelatedUnitObject</b> which extends its
 * {@link CPersistentUnitObject ancestor} to implement an object that features a list of
 * related objects and a {@link Valid() valid} object chain.
 *
 *	@package	MyWrapper
 *	@subpackage	Persistence
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 16/03/2012
 */

/*=======================================================================================
 *																						*
 *								CRelatedUnitObject.php									*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CPersistentUnitObject.php" );

/**
 * Related unit objects ancestor.
 *
 * This class extends its {@link CPersistentUnitObject parent} to add two properties: a
 * list of related objects and a pointer to a valid object.
 *
 * This object introduces the concept of object reference, that is, a structure that can be
 * used to refer to other objects. The {@link Relate() Relate} method can be used to manage
 * a list of object references in the {@link kTAG_REFS kTAG_REFS} offset, this list can take
 * two forms: it can be an array of object references, or an array of predicate/object pairs
 * that can constitute a graph.
 *
 * This class also features a {@link kTAG_VALID property} that can be {@link Valid() used}
 * to refer to a valid object: in other words, objects do not get deleted, they simply point
 * to the {@link Valid() valid} object, that way one can implement a system that maintains
 * referential integrity.
 *
 * To supplement the last property, this class implements a static
 * {@link ValidObject() method} that can be used to return the valid object, objects that
 * are obsolete or deleted may {@link Valid() point} to the valid object, this method will
 * follow the links until it reaches an object that is valid.
 *
 * We declare the class abstract because none of the {@link CPersistentUnitObject parent}
 * abstract methods are here explicitly implemented.
 *
 *	@package	MyWrapper
 *	@subpackage	Persistence
 */
abstract class CRelatedUnitObject extends CPersistentUnitObject
{
		

/*=======================================================================================
 *																						*
 *								PUBLIC MEMBER INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Relate																			*
	 *==================================================================================*/

	/**
	 * Manage object references.
	 *
	 * This method can be used to manage relationships between the current object and other
	 * objects, it is represented as a list of subject/predicate/object relationships in
	 * which the subject is the current object, and the list of predicate/object pairs will
	 * be stored in the {@link kTAG_REFS kTAG_REFS} offset.
	 *
	 * The method accepts the following parameters:
	 *
	 * <ul>
	 *	<li><b>$theObject</b>: Relation object, it should either be the relation object
	 *		itself, or an object reference to the relation's object. Before the current
	 *		object is {@link Commit() committed}, all elements provided as instances derived
	 *		from the {@link CRelatedUnitObject CRelatedUnitObject} class will also be
	 *		{@link _ParseReferences() committed} and converted to object references.
	 *		This parameter is passed through a protected
	 *		{@link _CheckRelationObject() method} that derived classes can use to validate
	 *		and normalise relation objects.
	 *	<li><b>$thePredicate</b>: Relation predicate, this parameter represents the kind or
	 *		predicate of the relation, depending on whether it is provided or not, each
	 *		element of the relations list will take the following form:
	 *	 <ul>
	 *		<li><i>NULL</i>: If the predicate is omitted, the element of the relation will
	 *			only have the object parameter.
	 *		<li><i>FALSE</i> The element will be an array composed of one element,
	 *			{@link kTAG_DATA kTAG_DATA}, which will hold the firat parameter.
	 *		<li><i>other</i> The element will be an array composed of two elements:
	 *		 <ul>
	 *			<li><i>{@link kTAG_KIND kTAG_KIND}</i>: This offset represents the reference
	 *				predicate, it will hold the value of this parameter.
	 *			<li><i>{@link kTAG_DATA kTAG_DATA}</i>: This offset represents the reference
	 *				object, it will hold the value of the first parameter.
	 *		 </ul>
	 *			This parameter is passed through a protected
	 *		{@link _CheckRelationPredicate() method} that derived classes can use to
	 *		validate and normalise relation predicates.
	 *	 </ul>
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
	 * @param mixed					$theObject			Reference object.
	 * @param mixed					$thePredicate		Reference predicate.
	 * @param mixed					$theOperation		Operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses _CheckRelationObject()
	 * @uses _CheckRelationPredicate()
	 * @uses _ManageObjectList()
	 *
	 * @see kTAG_REFS kTAG_KIND kTAG_DATA
	 */
	public function Relate( $theObject, $thePredicate = NULL,
										$theOperation = NULL,
										$getOld = FALSE )
	{
		//
		// Normalise relation object.
		//
		$theObject = $this->_CheckRelationObject( $theObject );
		
		//
		// Normalise relation predicate.
		//
		$thePredicate = $this->_CheckRelationPredicate( $thePredicate );
		
		//
		// Create predicate relation.
		//
		if( $thePredicate !== NULL )
		{
			//
			// Init relation.
			//
			$relation = Array();
			
			//
			// Set predicate.
			//
			if( $thePredicate !== FALSE )
				$relation[ kTAG_KIND ] = $thePredicate;
			
			//
			// Set relation object.
			//
			$relation[ kTAG_DATA ] = $theObject;
		
		} // Predicate relation.
		
		//
		// Create scalar relation.
		//
		else
			$relation = $theObject;
		
		return $this->_ManageObjectList( kTAG_REFS, $relation,
													$theOperation,
													$getOld );						// ==>

	} // Relate.

	 
	/*===================================================================================
	 *	Valid																			*
	 *==================================================================================*/

	/**
	 * Manage valid reference.
	 *
	 * This method can be used to handle the valid object's
	 * {@link kOFFSET_ID identifier}, it uses the standard accessor
	 * {@link _ManageOffset() method} to manage the {@link kTAG_VALID offset}.
	 *
	 * Objects derived from this class should be persistent, in other words, it is not an
	 * option to delete such objects: by creating a new object and referencing it from the
	 * old one, we maintain the original reference and point to the valid object.
	 *
	 * For a more in-depth reference of this method, please consult the
	 * {@link _ManageOffset() _ManageOffset} method, in which the first parameter will be
	 * the constant {@link kTAG_VALID kTAG_VALID}.
	 *
	 * In this class we feed the value to the
	 * {@link _CheckRelationObject() _CheckRelationObject} method that will take care of
	 * handling object references.
	 *
	 * @param mixed					$theValue			Value.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses _ManageOffset
	 *
	 * @see kTAG_VALID
	 */
	public function Valid( $theValue = NULL, $getOld = FALSE )
	{
		//
		// Check identifier.
		//
		if( ($theValue !== NULL)
		 && ($theValue !== FALSE) )
			$theValue = $this->_CheckRelationObject( $theValue );
		
		return $this->_ManageOffset( kTAG_VALID, $theValue, $getOld );				// ==>

	} // Valid.

		

/*=======================================================================================
 *																						*
 *								STATIC REFERENCE INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	ValidObject																		*
	 *==================================================================================*/

	/**
	 * Return a valid object.
	 *
	 * This method can be used to instantiate a valid object, this means that if we provide
	 * the identifier of an expired or obsolete object that has a reference to a
	 * {@link Valid() valid} object, this method will return the valid one.
	 *
	 * The method expects the same parameters as the {@link NewObject() NewObject} static
	 * method. If the {@link Valid() valid} chain is recursive, the method will raise an
	 * exception.
	 *
	 * The method will return the first object that does not have a {@link Valid() valid}
	 * {@link kTAG_VALID reference}, or <i>NULL</i> if the object was not found.
	 *
	 * @param mixed					$theContainer		Persistent container.
	 * @param mixed					$theIdentifier		Object identifier.
	 * @param bitfield				$theModifiers		Load modifiers.
	 *
	 * @static
	 * @return CGraphNodeObject
	 */
	static function ValidObject( $theContainer,
								 $theIdentifier,
								 $theModifiers = kFLAG_DEFAULT )
	{
		//
		// Init local storage.
		//
		$chain = Array();
		
		//
		// Iterate.
		//
		do
		{
			//
			// Instantiate object.
			//
			$object = self::NewObject( $theContainer, $theIdentifier, $theModifiers );
			
			//
			// Handle object.
			//
			if( $object !== NULL )
			{
				//
				// Get identifier.
				//
				$id = (string) $theIdentifier;
				
				//
				// Check recursion.
				//
				if( in_array( $id, $chain ) )
					throw new CException
						( "Recursive valid objects chain",
						  kERROR_INVALID_STATE,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Chain' => $chain ) );							// !@! ==>
				
				//
				// Add to chain.
				//
				$chain[] = $id;
				
				//
				// Copy valid.
				//
				$theIdentifier = $object[ kTAG_VALID ];
				
			} // Found entity.
			
			//
			// Catch missing chain link.
			//
			elseif( count( $chain ) )
				throw new CException
					( "Object not found in valid chain",
					  kERROR_INVALID_STATE,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Identifier' => $theIdentifier ) );				// !@! ==>
		
		} while( ($object !== NULL) && ($theIdentifier !== NULL) );
		
		return $object;																// ==>
		
	} // ValidObject.

		

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
	 * We {@link CPersistentUnitObject::_PrepareCommit() overload} this method to
	 * {@link Commit() commit} eventual object references stored as instances. We scan the
	 * {@link Relate() relations} and the {@link Valid() valid} references and process all
	 * elements that derive from this class.
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
		// Handle relations.
		//
		$this->_ParseReferences( kTAG_REFS, $theContainer, $theModifiers );
		
		//
		// Handle valid reference.
		//
		$this->_ParseReferences( kTAG_VALID, $theContainer, $theModifiers );
		
	} // _PrepareCommit.

	 

} // class CRelatedUnitObject.


?>
