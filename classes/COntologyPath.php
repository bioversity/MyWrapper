<?php

/**
 * <i>COntologyPath</i> class definition.
 *
 * This file contains the class definition of <b>COntologyPath</b> which represents an
 * ontology term used to tag data.
 *
 *	@package	MyWrapper
 *	@subpackage	Ontology
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 06/06/2012
 */

/*=======================================================================================
 *																						*
 *									COntologyPath.php									*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."COntologyTermObject.php" );

/**
 * Ontology path.
 *
 * Datasets are stored by this library in documents managed by a document database, there
 * is no predefined structure, except that each document attribute, or data element, is
 * identified by a tag which is the {@link kTAG_LID identifier} of instances from this
 * class.
 *
 * Instances of this class contain a list of ontology {@link COntologyTerm terms} whose
 * elements are related between each other by predicates, which are also
 * {@link COntologyTerm terms}.
 *
 * This path or chain of {@link COntologyTerm terms} represents the unique identifier of
 * this class instances and the tags with which data elements can be described.
 *
 * The path root is a {@link COntologyTerm term} whose {@link COntologyNode::Kind() kind}
 * must be {@link kTYPE_TRAIT kTYPE_TRAIT} which represents <i>what</b> the data element is
 * and the path leaf is a {@link COntologyTerm term} whose
 * {@link COntologyNode::Kind() kind} must be {@link kTYPE_MEASURE kTYPE_MEASURE} which
 * represents the <b>type</b> or <b>scale</b> of the data; all the
 * {@link COntologyTerm terms} found between these two describe <b>how</b> the data was
 * measured or obtained. This path or chain represents the <i>descriptors</i> of data in
 * this library and the container in which these objects are stored represents the data
 * dictionary.
 *
 * The class features the following properties:
 *
 * <ul>
 *	<li><i>{@link kTAG_CODE kTAG_CODE}</i>: This {@link CCodedUnitObject::Code() inherited}
 *		{@link Code() attribute} represents the tag that will be used to mark the data
 *		elements.
 *	<li><i>{@link kTAG_PATH kTAG_PATH}</i>: This {@link GID() attribute} holds the list of
 *		{@link COntologyTerm term} {@link COntologyTerm::GID() identifiers} representing the
 *		tag path, the attribute is a string formed by the concatenation of all the
 *		{@link COntologyTerm term} {@link COntologyTerm::GID() identifiers} structured as
 *		follows: <i>SUBJECT@PREDICATE/OBJECT@PREDICATE:OBJECT...</i> where all items are
 *		{@link COntologyTerm term} {@link COntologyTerm::GID() identifiers}.
 *	<li><i>{@link kTAG_TERM kTAG_TERM}</i>: This {@link Term() attribute} holds the list of
 *		{@link COntologyTerm terms} featured in the {@link GID() path} as an array of
 *		{@link COntologyTerm term} {@link kTAG_LID identifiers} structured as follows:
 *	 <ul>
 *		<li><i>{@link kTAG_TERM kTAG_TERM}</i>: This element holds the
 *			{@link COntologyTerm term} {@link kTAG_LID identifier} of the subject or object.
 *		<li><i>{@link kTAG_PREDICATE kTAG_PREDICATE}</i>: This element holds the
 *			{@link COntologyTerm term} {@link kTAG_LID identifier} of the predicate. This
 *			element will not be there in the last elements of the list.
 *	 </ul>
 *	<li><i>{@link kTAG_REF_COUNT kTAG_REF_COUNT}</i>: This {@link RefCount() attribute}
 *		holds the count of data instances that refer to the current tag, or the number of
 *		data instances tagged by the current path. This attribute is required and is
 *		initialised to 0.
 * </ul>
 *
 *	@package	MyWrapper
 *	@subpackage	Ontology
 */
class COntologyPath extends COntologyTermObject
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
		$this->_IsInited( $this->_Inited() );
		
	} // Constructor.

		

/*=======================================================================================
 *																						*
 *								PUBLIC MEMBER INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	GID																				*
	 *==================================================================================*/

	/**
	 * Manage term global identifier.
	 *
	 * The term global {@link kTAG_GID identifier} represents the un-hashed version of the
	 * term local {@link kTAG_LID identifier}.
	 *
	 * This value is set automatically by methods, so this method is read-only.
	 *
	 * @access public
	 * @return string
	 *
	 * @see kTAG_GID
	 */
	public function GID()									{	return $this[ kTAG_GID ];	}

	 
	/*===================================================================================
	 *	RefCount																		*
	 *==================================================================================*/

	/**
	 * Manage references count.
	 *
	 * This method can be used to retrieve the object's {@link kTAG_REF_COUNT references}
	 * count, or number of data instances tagged by this object.
	 *
	 * This method is read-only, because this value is set programmatically.
	 *
	 * @access public
	 * @return integer
	 *
	 * @see kTAG_REF_COUNT
	 */
	public function RefCount()				{	return $this->offsetGet( kTAG_REF_COUNT );	}

	 
	/*===================================================================================
	 *	Term																			*
	 *==================================================================================*/

	/**
	 * Manage terms.
	 *
	 * This method can be used to add {@link COntologyTerm term} elements of the path.
	 *
	 * The method accepts two parameters:
	 *
	 * <ul>
	 *	<li><b>$theTerm</b>: This parameter represents the term identifier, it can take the
	 *		following values:
	 *	 <ul>
	 *		<li><i>{@link COntologyTerm COntologyTerm}</i>: The method will use its
	 *			{@link kTAG_LID identifier}. 
	 *		<li><i>{@link CDataTypeBinary CDataTypeBinary}</i>: The method will assume that
	 *			the value represents the term {@link kTAG_LID identifier}.
	 *		<li><i>NULL</i>: This value indicates that we want to retrieve the whole list.
	 *		<li><i>FALSE</i>: This value indicates that we want to erase the whole list, in
	 *			this case the next parameter is ignored.
	 *		<li><i>array</i>: If you provide an array, this will replace the current one;
	 *			no check will be made on its elements: the method expects it to have been
	 *			already validated.
	 *		<li><i>other</i>: Any other value will be cast to a string and will be
	 *			interpreted as the term's {@link COntologyTerm::GID() string} identifier.
	 *	 </ul>
	 *	<li><b>$thePredicate</b>: This parameter represents the predicate term, it connects
	 *		the term provided in the previous parameter to the term that will be provided
	 *		in the next call of this method. If this is the last element of the list, this
	 *		parameter must be omitted. The values this parameter can take are the same as
	 *		those for the previous parameter.
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value <i>before</i> it was eventually modified.
	 *		<li><i>FALSE</i>: Return the value <i>after</i> it was eventually modified.
	 *	 </ul>
	 * </ul>
	 *
	 * This method operates on following structure:
	 *
	 * <ul>
	 *	<li><i>{@link kTAG_TERM kTAG_TERM}</i>: This element will hold the first parameter.
	 *	<li><i>{@link kTAG_PREDICATE kTAG_PREDICATE}</i>: This element will hold the
	 *		second parameter.
	 * </ul>
	 *
	 * in which the main offset is {@link kTAG_TERM kTAG_TERM}.
	 *
	 * @param mixed					$theTerm			Term or operation.
	 * @param mixed					$thePredicate		Predicate term.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses _CheckReference()
	 * @uses CAttribute::ManageArrayOffset()
	 *
	 * @see kTAG_TERM
	 */
	public function Term( $theTerm = NULL, $thePredicate = NULL, $getOld = FALSE )
	{
		//
		// Retrieve list.
		//
		if( $theTerm === NULL )
			return $this->offsetGet( kTAG_TERM );									// ==>
		
		//
		// Save current list.
		//
		$save = $this->offsetGet( kTAG_TERM );
		
		//
		// Delete list.
		//
		if( $theTerm === FALSE )
		{
			$this->offsetUnset( kTAG_TERM );
			if( $getOld )
				return $save;														// ==>
			return NULL;															// ==>
		}
		
		//
		// Replace list.
		//
		if( is_array( $theTerm ) )
		{
			$this->offsetSet( kTAG_TERM, $theTerm );
			if( $getOld )
				return $save;														// ==>
			return $theTerm;														// ==>
		}
		
		//
		// Init local storage.
		//
		$term = $predicate = NULL;
		
		//
		// Resolve term.
		//
		if( $theTerm instanceof COntologyTerm )
			$term = $theTerm->offsetGet( kTAG_LID );
		
		//
		// Resolve binary.
		//
		elseif( $theTerm instanceof CDataTypeBinary )
			$term = $theTerm;
		
		//
		// Resolve identifier.
		//
		else
			$term = COntologyTerm::HashIndex( (string) $theTerm );
		
		//
		// Resolve predicate.
		//
		if( $thePredicate !== NULL )
		{
			//
			// Resolve term.
			//
			if( $thePredicate instanceof COntologyTerm )
				$predicate = $thePredicate->offsetGet( kTAG_LID );
			
			//
			// Resolve binary.
			//
			elseif( $thePredicate instanceof CDataTypeBinary )
				$predicate = $thePredicate;
			
			//
			// Resolve identifier.
			//
			else
				$predicate = COntologyTerm::HashIndex( (string) $thePredicate );
		}
		
		//
		// Build element.
		//
		$element = Array();
		$element[ kTAG_TERM ] = $term;
		if( $predicate !== NULL )
			$element[ kTAG_PREDICATE ] = $predicate;
		
		//
		// Append element.
		//
		$save[] = $element;
		
		//
		// Update object.
		//
		$this->offsetSet( kTAG_TERM, $save );
		
		return $element;															// ==>

	} // Term.

		

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
	 * {@link kFLAG_STATE_INITED status}: this is set by the protected
	 * {@link _Inited() _Inited} method.
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
			$this->_IsInited( $this->_Inited() );
	
	} // offsetSet.

	 
	/*===================================================================================
	 *	offsetUnset																		*
	 *==================================================================================*/

	/**
	 * Reset a value for a given offset.
	 *
	 * We overload this method to manage the {@link _IsInited() inited}
	 * {@link kFLAG_STATE_INITED status}: this is set by the protected
	 * {@link _Inited() _Inited} method.
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
		$this->_IsInited( $this->_Inited() );
	
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
	 * We overload this method to set the {@link kTAG_PATH path}.
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
	 * @see kTYPE_ENUMERATION
	 */
	protected function _PrepareCommit( &$theContainer, &$theIdentifier, &$theModifiers )
	{
		//
		// Get container.
		//
		$container = $theContainer
		
		//
		// Call parent method.
		//
		parent::_PrepareCommit( $theContainer, $theIdentifier, $theModifiers );
	
	} // _PrepareCommit.

		

/*=======================================================================================
 *																						*
 *								PROTECTED STATUS UTILITIES								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_Inited																			*
	 *==================================================================================*/

	/**
	 * Return {@link _IsInited() initialised} status.
	 *
	 * This method will check if all the required attributes are set and return <i>TRUE</i>
	 * if this is the case, or <i>FALSE</i> if not.
	 *
	 * In this class we require the {@link kTAG_TERM kTAG_TERM} to have
	 * {@link Term() elements}.
	 *
	 * @access protected
	 * @return boolean
	 */
	protected function _Inited()
	{
		//
		// Check attributes.
		//
		return ( ($this->Code() !== NULL) &&
				 ($this->Term() !== NULL) );										// ==>
	
	} // _Inited.

		

/*=======================================================================================
 *																						*
 *									PROTECTED UTILITIES									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_Path																			*
	 *==================================================================================*/

	/**
	 * Set the path.
	 *
	 * This method will use all the elements in the {@link kTAG_TERM kTAG_TERM} offset to
	 * build the path.
	 *
	 * The method expects a single parameter, the container, which should represent the
	 * container 
	 *
	 * The following attributes are required:
	 *
	 * <ul>
	 *	<li><i>{@link kTAG_CODE kTAG_CODE}</i>: The term {@link Code() tag}.
	 *	<li><i>{@link kTAG_TERM kTAG_TERM}</i>: The path of {@link Term() term} references.
	 *	<li><i>{@link kTAG_PATH kTAG_PATH}</i>: The {@link Path() path} of term identifiers.
	 *	<li><i>{@link kTAG_REF_COUNT kTAG_REF_COUNT}</i>: the {@link RefCount() count} of
	 *		tagged data instances.
	 * </ul>
	 *
	 * @access protected
	 * @return boolean
	 */
	protected function _Inited()
	{
		//
		// Check attributes.
		//
		return ( ($this->Code() !== NULL) &&
				 ($this->Term() !== NULL) &&
				 ($this->Path() !== NULL) &&
				 ($this->TeRefCountrm() !== NULL) );								// ==>
	
	} // _Inited.

	 

} // class COntologyPath.


?>
