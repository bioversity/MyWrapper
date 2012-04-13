<?php

/**
 * <i>CCodedUnitObject</i> class definition.
 *
 * This file contains the class definition of <b>CCodedUnitObject</b> which represents the
 * ancestor of coded unit objects.
 *
 *	@package	MyWrapper
 *	@subpackage	Framework
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 12/04/2012
 */

/*=======================================================================================
 *																						*
 *									CCodedUnitObject.php								*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CPersistentUnitObject.php" );

/**
 * Coded unit object.
 *
 * Objects derived from this class have the following predefined properties:
 *
 * <ul>
 *	<li><i>{@link kTAG_CODE kTAG_CODE}</i>: This attribute represents the current object's
 *		{@link Code() code}, identifier or acronym.
 *	<li><i>{@link kTAG_KIND kTAG_KIND}</i>: This attribute represents the current object's
 *		{@link Kind() kind} or type.
 *	<li><i>{@link kTAG_REFS kTAG_REFS}</i>: This offset holds the object list of references,
 *		these are references to other objects that form a subject/predicate/object triplet,
 *		where the subject is the current object, and the list of predicate/object pairs is
 *		held in this offset.
 *	<li><i>{@link kTAG_VALID kTAG_VALID}</i>: This offset holds the
 *		{@link kOFFSET_ID native} identifier of the valid object. This should be used
 *		when the current object becomes obsolete: instead of deleting it we create a new one
 *		and store in this {@link kTAG_VALID offset} the identifier of the new object that
 *		will replace the current one.
 *	<li><i>{@link kTAG_MOD_STAMP kTAG_MOD_STAMP}</i>: This offset holds the object's last
 *		modification time stamp, this property should be used to mark all objects.
 * </ul>
 *
 * By default, the unique {@link _index() identifier} of the object is its
 * {@link Code() code}, which is also its {@link _id() id}.
 *
 * Objects of this class require at least the {@link Code() code} {@link kTAG_CODE offset}
 * to have an {@link _IsInited() initialised} {@link kFLAG_STATE_INITED status}.
 *
 * This class implements the string {@link __toString() conversion}, by default it returns
 * the value of the object {@link _index() identifier}.
 *
 *	@package	MyWrapper
 *	@subpackage	Framework
 */
class CCodedUnitObject extends CPersistentUnitObject
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
	 * We {@link CPersistentObject::__construct() overload} the constructor to initialise
	 * the {@link _IsInited() inited} {@link kFLAG_STATE_INITED flag} if the
	 * {@link Code() code} attribute is set.
	 *
	 * @param mixed					$theContainer		Persistent container.
	 * @param mixed					$theIdentifier		Object identifier.
	 * @param bitfield				$theModifiers		Create modifiers.
	 *
	 * @access public
	 *
	 * @uses _IsInited
	 *
	 * @see kTAG_CODE
	 */
	public function __construct( $theContainer = NULL,
								 $theIdentifier = NULL,
								 $theModifiers = kFLAG_DEFAULT )
	{
		//
		// Call parent method.
		//
		parent::__construct( $theContainer, $theIdentifier, $theModifiers );
		
		//
		// Set inited status.
		//
		$this->_IsInited( $this->offsetExists( kTAG_CODE ) );
		
	} // Constructor.

	 
	/*===================================================================================
	 *	__toString																		*
	 *==================================================================================*/

	/**
	 * Return object identifier.
	 *
	 * In this class we return the object string {@link _index() identifier}.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses _index()
	 */
	public function __toString()								{	return $this->_index();	}

		

/*=======================================================================================
 *																						*
 *								PUBLIC MEMBER INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Code																			*
	 *==================================================================================*/

	/**
	 * Manage code.
	 *
	 * This method can be used to handle the object's {@link kTAG_CODE code}, it uses the
	 * standard accessor {@link _ManageOffset() method} to manage the
	 * {@link kTAG_CODE offset}.
	 *
	 * The code represents the object's {@link _index() identifier}.
	 *
	 * For a more in-depth reference of this method, please consult the
	 * {@link _ManageOffset() _ManageOffset} method, in which the first parameter will be
	 * the constant {@link kTAG_CODE kTAG_CODE}.
	 *
	 * @param mixed					$theValue			Value.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses _ManageOffset
	 *
	 * @see kTAG_CODE
	 */
	public function Code( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kTAG_CODE, $theValue, $getOld );				// ==>

	} // Code.

	 
	/*===================================================================================
	 *	Kind																			*
	 *==================================================================================*/

	/**
	 * Manage kind.
	 *
	 * This method can be used to handle the object's {@link kTAG_KIND kinds}, it uses the
	 * standard accessor {@link _ManageArrayOffset() method} to manage the list of kinds.
	 *
	 * Each element of this list should indicate a function or quality of the current
	 * object
	 *
	 * For a more thorough reference of how this method works, please consult the
	 * {@link _ManageArrayOffset() _ManageArrayOffset} method, in which the first parameter
	 * will be the constant {@link kTAG_KIND kTAG_KIND}.
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
	 * @see kTAG_KIND
	 */
	public function Kind( $theValue = NULL, $theOperation = NULL, $getOld = FALSE )
	{
		return $this->_ManageArrayOffset
					( kTAG_KIND, $theValue, $theOperation, $getOld );				// ==>

	} // Kind.

	 
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
	 *		from the {@link CPersistentUnitObject CPersistentUnitObject} class will also be
	 *		{@link _CommitReferences() committed} and converted to object references.
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
	 *		 <ul>
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
	 * @use _CheckRelationObject()
	 * @use _CheckRelationPredicate()
	 * @use _ManageObjectList()
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

	 
	/*===================================================================================
	 *	Stamp																			*
	 *==================================================================================*/

	/**
	 * Manage object time stamp.
	 *
	 * This method can be used to manage the object {@link kTAG_MOD_STAMP time-stamp}, or
	 * the date in which the last modification was made on the object, it uses the standard
	 * accessor {@link _ManageOffset() method} to manage the {@link kTAG_MOD_STAMP offset}:
	 *
	 * <ul>
	 *	<li><b>$theValue</b>: The value or operation:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the current value.
	 *		<li><i>FALSE</i>: Delete the value.
	 *		<li><i>other</i>: Set value.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value <i>before</i> it was eventually modified.
	 *		<li><i>FALSE</i>: Return the value <i>after</i> it was eventually modified.
	 *	 </ul>
	 * </ul>
	 *
	 * @param NULL|FALSE|string		$theValue			Entity last modification date.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses _ManageOffset
	 *
	 * @see kTAG_MOD_STAMP
	 */
	public function Stamp( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kTAG_MOD_STAMP, $theValue, $getOld );			// ==>

	} // Stamp.

		

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
	 * {@link kFLAG_STATE_INITED status}: this is set if {@link kTAG_CODE code} property is
	 * set.
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
			$this->_IsInited( $this->offsetExists( kTAG_CODE ) );
	
	} // offsetSet.

	 
	/*===================================================================================
	 *	offsetUnset																		*
	 *==================================================================================*/

	/**
	 * Reset a value for a given offset.
	 *
	 * We overload this method to manage the {@link _IsInited() inited}
	 * {@link kFLAG_STATE_INITED status}: this is set if {@link kTAG_CODE code} property is
	 * set.
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
		parent::offsetUnset( $theOffset );
		
		//
		// Set inited flag.
		//
		$this->_IsInited( $this->offsetExists( kTAG_CODE ) );
	
	} // offsetUnset.

		

/*=======================================================================================
 *																						*
 *									STATIC INTERFACE									*
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
	 * @return CCodedUnitObject
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
 *							PROTECTED IDENTIFICATION INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_index																			*
	 *==================================================================================*/

	/**
	 * Return the object's unique index.
	 *
	 * In this class we consider the {@link kTAG_CODE code} to be the object's unique
	 * {@link kOFFSET_ID identifier}.
	 *
	 * @access protected
	 * @return string
	 */
	protected function _index()									{	return $this->Code();	}

		

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
	 * We overload this method to {@link Commit() commit} eventual object references stored
	 * as instances. We scan the {@link Relate() relations} and the {@link Valid() valid}
	 * references and process all elements that derive from this class.
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
		$this->_CommitReferences( $theContainer, kTAG_REFS, $theModifiers );
		
		//
		// Handle valid reference.
		//
		$this->_CommitReferences( $theContainer, kTAG_VALID, $theModifiers );
		
	} // _PrepareCommit.

		

/*=======================================================================================
 *																						*
 *								PROTECTED REFERENCE UTILITIES							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_CommitReferences																*
	 *==================================================================================*/

	/**
	 * Commit instance references.
	 *
	 * This method will take care of committing object references expressed as the actual
	 * instances of objects. It is called {@link _PrepareCommit() before}
	 * {@link Commit() storing} objects that contain references to other objects, its duty
	 * is to traverse these lists and {@link Commit() commit} any element that is actually
	 * an instance derived from this class, and convert it back to a reference.
	 *
	 * The parameters to this method are:
	 *
	 * <ul>
	 *	<li><b>$theContainer</b>: The container that is about to receive the current object,
	 *		it must also be the container in which to find the references and must be
	 *		derived from {@link CContainer CContainer}.
	 *	<li><b>$theOffset</b>: The current object's offset in which the reference list or
	 *		element is stored.
	 *	<li><b>$theModifiers</b>: A bitfield indicating which elements of the
	 *		{@link CContainer::Reference() reference} should be included, this parameter
	 *		will be passed to the {@link CContainer::Reference() method} that will convert
	 *		the object into a reference:
	 *	 <ul>
	 *		<li><i>{@link kFLAG_REFERENCE_IDENTIFIER kFLAG_REFERENCE_IDENTIFIER}</i>: The
	 *			object {@link kOFFSET_ID identifier} will be stored under the
	 *			{@link kOFFSET_REFERENCE_ID kOFFSET_REFERENCE_ID} offset. This option is
	 *			enforced.
	 *		<li><i>{@link kFLAG_REFERENCE_CONTAINER kFLAG_REFERENCE_CONTAINER}</i>: The
	 *			provided container name will be stored under the
	 *			{@link kOFFSET_REFERENCE_CONTAINER kOFFSET_REFERENCE_CONTAINER} offset. If
	 *			the provided value is empty, the offset will not be set.
	 *		<li><i>{@link kFLAG_REFERENCE_DATABASE kFLAG_REFERENCE_DATABASE}</i>: The
	 *			provided container's database name will be stored under the
	 *			{@link kOFFSET_REFERENCE_DATABASE kOFFSET_REFERENCE_DATABASE} offset. If the
	 *			current object's {@link Database() database} name is <i>NULL</i>, the
	 *			offset will not be set.
	 *		<li><i>{@link kFLAG_REFERENCE_CLASS kFLAG_REFERENCE_CLASS}</i>: The element
	 *			object's class name will be stored under the {@link kTAG_CLASS kTAG_CLASS}
	 *			offset.
	 *	 </ul>
	 * </ul>
	 *
	 * Note that when we {@link Commit() commit} referenced objects we use
	 * {@link kFLAG_PERSIST_REPLACE kFLAG_PERSIST_REPLACE} as the commit type.
	 *
	 * @param CContainer			$theContainer		Object container.
	 * @param string				$theOffset			Reference list offset.
	 * @param bitfield				$theModifiers		Referencing options.
	 *
	 * @access protected
	 *
	 * @uses _CommitReference()
	 */
	protected function _CommitReferences( $theContainer, $theOffset, $theModifiers )
	{
		//
		// Check container.
		//
		if( ! $theContainer instanceof CContainer )
			throw new CException
					( "Unsupported container type",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Container' => $theContainer ) );					// !@! ==>
		
		//
		// Init local storage.
		//
		$done = FALSE;
		$reference = $this->offsetGet( $theOffset );
		
		//
		// Handle scalar.
		//
		if( $reference instanceof self )
			$done = $this->_CommitReference
						( $reference, $theContainer, $theModifiers, TRUE );
		
		//
		// Handle list.
		//
		elseif( is_array( $reference )
			 || ($reference instanceof ArrayObject) )
		{
			//
			// Iterate list.
			//
			foreach( $reference as $key => $value )
			{
				//
				// Resolve.
				//
				$done = $this->_CommitReference
						( $value, $theContainer, $theModifiers, TRUE );
				if( $done !== NULL )
					$reference[ $key ] = $value;
			
			} // Iterating list.
		
		} // Is a list.
		
		//
		// Update reference.
		//
		if( $done )
			$this->offsetSet( $theOffset, $reference );
		
	} // _CommitReferences.


	/*===================================================================================
	 *	_CommitReference																*
	 *==================================================================================*/

	/**
	 * Commit instance reference.
	 *
	 * This method will check if the provided reference is an object derived from this
	 * class, in that case it will {@link Commit() commit} it and convert it to an object
	 * reference.
	 *
	 * The parameters to this method are:
	 *
	 * <ul>
	 *	<li><b>&$theReference</b>: The reference, depending on its type:
	 *	 <ul>
	 *		<li><i>CCodedUnitObject</i>: Objects derived from this class will be
	 *			{@link Commit() committed} and the parameter will be replaced with the
	 *			object {@link CContainer::Reference() reference}.
	 *		<li><i>Array</i> or <i>ArrayObject</i>: In this case we shall check whether the
	 *			structure has the {@link kTAG_KIND kTAG_KIND} and/or the
	 *			{@link kTAG_DATA kTAG_DATA} elements: in that case if these elements are
	 *			derived from this class, we {@link Commit() commit} them and replace them
	 *			with object {@link CContainer::Reference() references}.
	 *	 </ul>
	 *	<li><b>$theContainer</b>: The container that is about to receive the current object,
	 *		it must also be the container in which to find the references and must be
	 *		derived from {@link CContainer CContainer}.
	 *	<li><b>$theModifiers</b>: A bitfield indicating which elements of the
	 *		{@link CContainer::Reference() reference} should be included, this parameter
	 *		will be passed to the {@link CContainer::Reference() method} that will convert
	 *		the object into a reference:
	 *	 <ul>
	 *		<li><i>{@link kFLAG_REFERENCE_IDENTIFIER kFLAG_REFERENCE_IDENTIFIER}</i>: The
	 *			object {@link kOFFSET_ID identifier} will be stored under the
	 *			{@link kOFFSET_REFERENCE_ID kOFFSET_REFERENCE_ID} offset. This option is
	 *			enforced.
	 *		<li><i>{@link kFLAG_REFERENCE_CONTAINER kFLAG_REFERENCE_CONTAINER}</i>: The
	 *			provided container name will be stored under the
	 *			{@link kOFFSET_REFERENCE_CONTAINER kOFFSET_REFERENCE_CONTAINER} offset. If
	 *			the provided value is empty, the offset will not be set.
	 *		<li><i>{@link kFLAG_REFERENCE_DATABASE kFLAG_REFERENCE_DATABASE}</i>: The
	 *			provided container's database name will be stored under the
	 *			{@link kOFFSET_REFERENCE_DATABASE kOFFSET_REFERENCE_DATABASE} offset. If the
	 *			current object's {@link Database() database} name is <i>NULL</i>, the
	 *			offset will not be set.
	 *		<li><i>{@link kFLAG_REFERENCE_CLASS kFLAG_REFERENCE_CLASS}</i>: The element
	 *			object's class name will be stored under the {@link kTAG_CLASS kTAG_CLASS}
	 *			offset.
	 *	 </ul>
	 * </ul>
	 *
	 * The method will return <i>TRUE</i> if a {@link Commit() commit} was performed, or
	 * <i>NULL</i> if not.
	 *
	 * Note that when we {@link Commit() commit} referenced objects we use
	 * {@link kFLAG_PERSIST_REPLACE kFLAG_PERSIST_REPLACE} as the commit type.
	 *
	 * This method is called by
	 * {@link _CommitReferences() _CommitReferences}, so we assume that the
	 * provided container is an instance of {@link CContainer CContainer}.
	 *
	 * @param reference			   &$theReference		Reference.
	 * @param CContainer			$theContainer		Object container.
	 * @param bitfield				$theModifiers		Reference options.
	 * @param boolean				$doRecurse			Recurse flag.
	 *
	 * @access protected
	 * @return mixed
	 *
	 * @see kTAG_KIND kTAG_DATA
	 * @see kFLAG_PERSIST_REPLACE kFLAG_STATE_ENCODED kFLAG_REFERENCE_IDENTIFIER
	 */
	protected function _CommitReference( &$theReference,
										  $theContainer,
										  $theModifiers,
										  $doRecurse = FALSE )
	{
		//
		// Handle simple reference.
		//
		if( $theReference instanceof self )
		{
			//
			// Init local storage.
			//
			$modifiers = kFLAG_PERSIST_REPLACE | ($theModifiers & kFLAG_STATE_ENCODED);
			$theModifiers |= kFLAG_REFERENCE_IDENTIFIER;
			
			//
			// Check for recursion
			//
			if( $this->_index() == $theReference->_index() )
				throw new CException( "Recursive reference",
									  kERROR_INVALID_STATE,
									  kMESSAGE_TYPE_ERROR,
									  array( 'Reference' => $theReference ) );	// !@! ==>
			
			//
			// Commit object.
			//
			$theReference->Commit( $theContainer, NULL, $modifiers );
			
			//
			// Convert to reference.
			//
			$theReference = $theContainer->Reference( $theReference, $theModifiers );
			
			return TRUE;															// ==>
		
		} // Simple reference.
		
		//
		// Handle typed reference.
		//
		if( ( is_array( $theReference )
		   || ($theReference instanceof ArrayObject) )
		 && $doRecurse )
		{
			//
			// Init local storage.
			//
			$done = NULL;

			//
			// Handle predicate.
			// Need to do this dirty trick,
			// because references are not pointers...
			//
			if( array_key_exists( kTAG_KIND, (array) $theReference ) )
			{
				$tmp = $theReference[ kTAG_KIND ];
				$done |= $this->_CommitReference
							( $tmp, $theContainer, $theModifiers, FALSE );
				$theReference[ kTAG_KIND ] = $tmp;
			}
		
			//
			// Handle object.
			// Need to do this dirty trick,
			// because references are not pointers...
			//
			if( array_key_exists( kTAG_DATA, (array) $theReference ) )
			{
				$tmp = $theReference[ kTAG_DATA ];
				$done |= $this->_CommitReference
							( $tmp, $theContainer, $theModifiers, FALSE );
				$theReference[ kTAG_DATA ] = $tmp;
			}
			
			return $done;															// ==>
		
		} // Typed reference.
		
		return NULL;																// ==>
		
	} // _CommitReference.

	 
	/*===================================================================================
	 *	_CheckRelationObject															*
	 *==================================================================================*/

	/**
	 * Normalise object reference parameter.
	 *
	 * This method can be used to normalise a parameter that is supposed to be a reference
	 * to another object, the method will perform the following conversions:
	 *
	 * <ul>
	 *	<li><i>CCodedUnitObject</i>: Objects derived from this class will be handled as
	 *		follows:
	 *	 <ul>
	 *		<li><i>{@link _IsCommitted() Committed}</i>: If the provided object has a
	 *			{@link _IsCommitted() committed} {@link kFLAG_STATE_COMMITTED status}, the
	 *			method will return the object's {@link kOFFSET_ID identifier}.
	 *		<li><i>Not {@link _IsCommitted() committed}</i>: The parameter will not be
	 *			converted.
	 *	 </ul>
	 *	<li><i>{@link CDataType CDataType}</i>: When providing a complex data type, we
	 *		assume the value corresponds to the {@link kOFFSET_ID identifier}, in which case
	 *		we leave it untouched.
	 *	<li><i>Array</i> or <i>ArrayObject</i>: In this case the method will assume the
	 *		provided structure is an object reference and it will check if the
	 *		{@link kOFFSET_REFERENCE_ID kOFFSET_REFERENCE_ID} offset is there, if this is
	 *		not the case the method will raise an exception.
	 *	<li><i>other</i>: Any other type will be converted to a string.
	 * </ul>
	 *
	 * The method will return the converted value, derived classes should first handle
	 * custom types and pass other types to the parent method.
	 *
	 * @param mixed					$theValue			Object or reference.
	 *
	 * @access protected
	 * @return mixed
	 *
	 * @uses _IsCommitted()
	 *
	 * @see kOFFSET_ID kOFFSET_REFERENCE_ID
	 */
	protected function _CheckRelationObject( $theValue )
	{
		//
		// Handle object's derived from this class.
		//
		if( $theValue instanceof self )
		{
			//
			// Reference committed objects.
			//
			if( $theValue->_IsCommitted() )
				return $theObject[ kOFFSET_ID ];									// ==>
			
			return $theValue;														// ==>
		
		} // Object derived from this class.
		
		//
		// Handle complex data types.
		//
		if( $theValue instanceof CDataType )
			return $theValue;														// ==>
		
		//
		// Check object reference.
		//
		if( is_array( $theValue )
		 || ($theValue instanceof ArrayObject) )
		{
			//
			// Check identifier.
			//
			if( array_key_exists( kOFFSET_REFERENCE_ID, (array) $theValue ) )
				return $theValue;													// ==>

			throw new CException( "Invalid object reference: missing identifier",
								  kERROR_INVALID_PARAMETER,
								  kMESSAGE_TYPE_ERROR,
								  array( 'Reference' => $theValue ) );			// !@! ==>
		
		} // Object reference?
		
		return (string) $theValue;													// ==>
	
	} // _CheckRelationObject.

	 
	/*===================================================================================
	 *	_CheckRelationPredicate															*
	 *==================================================================================*/

	/**
	 * Normalise predicate reference parameter.
	 *
	 * This method can be used to normalise a parameter that is supposed to be a relation
	 * predicate, the method will perform the following conversions:
	 *
	 * <ul>
	 *	<li><i>NULL</i>: No conversion.
	 *	<li><i>FALSE</i>: No conversion.
	 *	<li><i>CCodedUnitObject</i>: The method will pass the parameter to the
	 *		{@link _CheckRelationObject() _CheckRelationObject} method.
	 *	<li><i>{@link CDataType CDataType}</i>: The method will pass the parameter to the
	 *		{@link _CheckRelationObject() _CheckRelationObject} method.
	 *	<li><i>Array</i> or <i>ArrayObject</i>: The method will pass the parameter to the
	 *		{@link _CheckRelationObject() _CheckRelationObject} method.
	 *	<li><i>other</i>: Any other type will be converted to a string.
	 * </ul>
	 *
	 * The method will return the converted value, derived classes should first handle
	 * custom types and pass other types to the parent method.
	 *
	 * @param mixed					$theValue			Relation predicate.
	 *
	 * @access protected
	 * @return mixed
	 *
	 * @uses _IsCommitted()
	 *
	 * @see kOFFSET_ID kOFFSET_REFERENCE_ID
	 */
	protected function _CheckRelationPredicate( $theValue )
	{
		//
		// Handle missing or empty predicate.
		//
		if( ($theValue === NULL)
		 || ($theValue === FALSE) )
			return $theValue;														// ==>
		
		//
		// Handle object.
		//
		if( is_array( $theValue )
		 || ($theValue instanceof self)
		 || ($theValue instanceof CDataType)
		 || ($theValue instanceof ArrayObject) )
			return $this->_CheckRelationObject( $theValue );						// ==>
		
		return (string) $theValue;													// ==>
	
	} // _CheckRelationPredicate.

	 

} // class CCodedUnitObject.


?>
