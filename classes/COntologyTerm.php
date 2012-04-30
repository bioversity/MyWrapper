<?php

/**
 * <i>COntologyTerm</i> class definition.
 *
 * This file contains the class definition of <b>COntologyTerm</b> which represents the
 * ancestor of generic ontology terms.
 *
 *	@package	MyWrapper
 *	@subpackage	Ontology
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 16/04/2012
 */

/*=======================================================================================
 *																						*
 *									COntologyTerm.php									*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."COntologyTermObject.php" );

/**
 * Generic term.
 *
 * This kind of {@link COntologyTermObject term} represents a generic ontology term.
 *
 * This class handles the {@link _IsInited() inited} status by checking the following values
 * in its {@link Kind() kinds} list:
 *
 * <ul>
 *	<li><i>Default</i>: By default the object must have its {@link kTAG_CODE code} set.
 *	<li><i>{@link kTYPE_NAMESPACE kTYPE_NAMESPACE}</i>: It uses the default requirements.
 *	<li><i>{@link kTYPE_ONTOLOGY kTYPE_ONTOLOGY}</i>: It requires the {@link kTAG_NAME name}
 *		to be set.
 *	<li><i>{@link kTYPE_PREDICATE kTYPE_PREDICATE}</i>: It requires the
 *		{@link kTAG_NAME name}.
 *	<li><i>{@link kTYPE_ATTRIBUTE kTYPE_ATTRIBUTE}</i>: It requires the
 *		{@link kTAG_NAME name} and {@link kTAG_CARDINALITY cardinality}.
 *	<li><i>{@link kTYPE_MEASURE kTYPE_MEASURE}</i>: It requires the {@link kTAG_NAME name}
 *		and {@link kTAG_TYPE type}.
 *	<li><i>{@link kTYPE_ENUMERATION kTYPE_ENUMERATION}</i>: It requires the
 *		{@link kTAG_NAME name} and {@link kTAG_ENUM enumeration}.
 * </ul>
 *
 * In this class we ensure that the term has both the {@link Code() code} and
 * {@link Name() name} in order to have an {@link _IsInited() inited}
 * {@link kFLAG_STATE_INITED status}.
 *
 *	@package	MyWrapper
 *	@subpackage	Ontology
 */
class COntologyTerm extends COntologyTermObject
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
	 *
	 * @see kTAG_CODE kTAG_NAME
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
	 *	Node																			*
	 *==================================================================================*/

	/**
	 * Manage node references.
	 *
	 * This method can be used to handle the object's {@link kTAG_NODE node} references, it
	 * uses the standard accessor {@link _ManageArrayOffset() method} to manage the list of
	 * nodes that point to this term.
	 *
	 * Each element of this list represents the ID of a node in the ontology: each time a
	 * node references this term, its identifier will be aded to this offset.
	 *
	 * For a more thorough reference of how this method works, please consult the
	 * {@link _ManageArrayOffset() _ManageArrayOffset} method, in which the first parameter
	 * will be the constant {@link kTAG_KIND kTAG_KIND}.
	 *
	 * Note that you should only use this method for retrieving information, since
	 * {@link COntologyNode nodes} store automatically this information when
	 * {@link Commit() saved}.
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
	 * @see kTAG_NODE
	 */
	public function Node( $theValue = NULL, $theOperation = NULL, $getOld = FALSE )
	{
		return $this->_ManageArrayOffset
					( kTAG_NODE, $theValue, $theOperation, $getOld );				// ==>

	} // Node.

	 
	/*===================================================================================
	 *	Predicate																		*
	 *==================================================================================*/

	/**
	 * Manage predicate node references.
	 *
	 * This method can be used to handle the object's predicate {@link kTAG_EDGE node}
	 * references, it uses the standard accessor {@link _ManageArrayOffset() method} to
	 * manage the list of predicate nodes that point to this term.
	 *
	 * Each element of this list represents the ID of an edge node in the ontology: each
	 * time an edge node references this term, its identifier will be aded to this offset.
	 *
	 * For a more thorough reference of how this method works, please consult the
	 * {@link _ManageArrayOffset() _ManageArrayOffset} method, in which the first parameter
	 * will be the constant {@link kTAG_KIND kTAG_KIND}.
	 *
	 * Note that you should only use this method for retrieving information, since
	 * {@link COntologyNode nodes} store automatically this information when
	 * {@link Commit() saved}.
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
	 * @see kTAG_EDGE
	 */
	public function Predicate( $theValue = NULL, $theOperation = NULL, $getOld = FALSE )
	{
		return $this->_ManageArrayOffset
					( kTAG_EDGE, $theValue, $theOperation, $getOld );				// ==>

	} // Predicate.

	 
	/*===================================================================================
	 *	Enumeration																		*
	 *==================================================================================*/

	/**
	 * Manage enumerations.
	 *
	 * This method can be used to handle the object's {@link kTAG_ENUM enumerations}, it
	 * uses the standard accessor {@link _ManageArrayOffset() method} to manage the list of
	 * enumerations.
	 *
	 * Each element of this list should indicate a code or acronym defining the current
	 * object
	 *
	 * For a more thorough reference of how this method works, please consult the
	 * {@link _ManageArrayOffset() _ManageArrayOffset} method, in which the first parameter
	 * will be the constant {@link kTAG_ENUM kTAG_ENUM}.
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
	 * @see kTAG_ENUM
	 */
	public function Enumeration( $theValue = NULL, $theOperation = NULL, $getOld = FALSE )
	{
		return $this->_ManageArrayOffset
					( kTAG_ENUM, $theValue, $theOperation, $getOld );				// ==>

	} // Enumeration.

	 
	/*===================================================================================
	 *	Type																			*
	 *==================================================================================*/

	/**
	 * Manage data type.
	 *
	 * This method can be used to manage the measure data {@link kTAG_TYPE type}, it uses
	 * the standard accessor {@link _ManageOffset() method} to manage the
	 * {@link kTAG_TYPE offset}:
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
	 * @param NULL|FALSE|string		$theValue			Data type tag.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 */
	public function Type( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kTAG_TYPE, $theValue, $getOld );				// ==>

	} // Type.

	 
	/*===================================================================================
	 *	Pattern																			*
	 *==================================================================================*/

	/**
	 * Manage patterns.
	 *
	 * This method can be used to handle the object's {@link kTAG_PATTERN patterns}, it
	 * uses the standard accessor {@link _ManageArrayOffset() method} to manage the list of
	 * patterns.
	 *
	 * This term usually describes a {@link kTYPE_STRING string} data element that is
	 * restricted by a series of string patterns, use the standard XML format.
	 *
	 * For a more thorough reference of how this method works, please consult the
	 * {@link _ManageArrayOffset() _ManageArrayOffset} method, in which the first parameter
	 * will be the constant {@link kTAG_PATTERN kTAG_PATTERN}.
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
	 * @see kTAG_PATTERN
	 */
	public function Pattern( $theValue = NULL, $theOperation = NULL, $getOld = FALSE )
	{
		return $this->_ManageArrayOffset
					( kTAG_PATTERN, $theValue, $theOperation, $getOld );			// ==>

	} // Pattern.

	 
	/*===================================================================================
	 *	Cardinality																		*
	 *==================================================================================*/

	/**
	 * Manage data cardinality.
	 *
	 * This method can be used to manage the measure data {@link kTAG_CARDINALITY type}, it
	 * uses the standard accessor {@link _ManageOffset() method} to manage the
	 * {@link kTAG_TYPE offset}:
	 *
	 * <ul>
	 *	<li><b>$theValue</b>: The value or operation:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the current value.
	 *		<li><i>FALSE</i>: Delete the value.
	 *		<li><i>string</i>: String values represent the cardinality enumeration:
	 *		 <ul>
	 *			<li><i>{@link kCARD_0_1 kCARD_0_1}</i>: Zero or one, a scalar or none.
	 *			<li><i>{@link kCARD_1 kCARD_1}</i>: Exactly one, a required scalar.
	 *			<li><i>{@link kCARD_ANY kCARD_ANY}</i>: Any, this implies that we either
	 *				have an array or no data.
	 *		 </ul>
	 *		<li><i>integer</i>: An integer represents the exact cardinality, in that case
	 *			we assume the data is an array of at most that number of elements.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value <i>before</i> it was eventually modified.
	 *		<li><i>FALSE</i>: Return the value <i>after</i> it was eventually modified.
	 *	 </ul>
	 * </ul>
	 *
	 * @param NULL|FALSE|string		$theValue			Data type tag.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 */
	public function Cardinality( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kTAG_CARDINALITY, $theValue, $getOld );		// ==>

	} // Cardinality.

	 
	/*===================================================================================
	 *	Unit																			*
	 *==================================================================================*/

	/**
	 * Manage data unit.
	 *
	 * This method can be used to handle the measure {@link kTAG_UNIT unit}, it uses the
	 * standard accessor {@link _ManageOffset() method} to manage the
	 * {@link kTAG_UNIT offset}.
	 *
	 * The value provided to this property should be the {@link _id() identifier} of a term
	 * hat defines a unit.
	 *
	 * For a more in-depth reference of this method, please consult the
	 * {@link _ManageOffset() _ManageOffset} method, in which the first parameter will be
	 * the constant {@link kTAG_VALID kTAG_VALID}.
	 *
	 * In this class we feed the value to the{@link _CheckReference() _CheckReference}
	 * method that will take care of handling object references.
	 *
	 * @param mixed					$theValue			Value.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses _ManageOffset
	 *
	 * @see kTAG_UNIT
	 */
	public function Unit( $theValue = NULL, $getOld = FALSE )
	{
		//
		// Check identifier.
		//
		if( ($theValue !== NULL)
		 && ($theValue !== FALSE) )
			$theValue = $this->_CheckReference( $theValue );
		
		return $this->_ManageOffset( kTAG_UNIT, $theValue, $getOld );				// ==>

	} // Unit.

	 
	/*===================================================================================
	 *	Examples																		*
	 *==================================================================================*/

	/**
	 * Manage data examples.
	 *
	 * This method can be used to handle data {@link kTAG_EXAMPLES examples}, it is a list
	 * of strings handles by the standard accessor {@link _ManageArrayOffset() method} in
	 * the examples {@link kTAG_EXAMPLES offset}.
	 *
	 * The provided value should be an example of how the current term could be represented,
	 * an extensive set of examples should be included in order to provide enough
	 * information to handle correctly any kind of output referenced data elements could
	 * represent.
	 *
	 * For a more in-depth reference of this method, please consult the
	 * {@link _ManageOffset() _ManageOffset} method, in which the first parameter will be
	 * the constant {@link kTAG_VALID kTAG_VALID}.
	 *
	 * @param mixed					$theValue			Value or index.
	 * @param mixed					$theOperation		Operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses _ManageOffset
	 *
	 * @see kTAG_EXAMPLES
	 */
	public function Examples( $theValue = NULL, $theOperation = NULL, $getOld = FALSE )
	{
		return $this->_ManageArrayOffset
					( kTAG_EXAMPLES, $theValue, $theOperation, $getOld );			// ==>

	} // Examples.

		

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
	 * {@link kFLAG_STATE_INITED status}: this is set if the {@link Code() code} and
	 * {@link Name() name} attributes are set.
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
	 * {@link kFLAG_STATE_INITED status}: this is set if the {@link Code() code} and
	 * {@link Name() name} attributes are set.
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
	 * We overload this method to set default {@link Kind() kinds} according to which data
	 * properties the object has:
	 *
	 * <ul>
	 *	<li><i>{@link Cardinality() Cardinality}</i>: We set the {@link Kind() kind} to
	 *		{@link kTYPE_ATTRIBUTE attribute}.
	 *	<li><i>Data {@link Type() Type}</i>: We set the {@link Kind() kind} to
	 *		{@link kTYPE_MEASURE measure}.
	 *	<li><i>{@link Enumeration() Enumeration}</i>: We set the {@link Kind() kind} to
	 *		{@link kTYPE_ENUMERATION enumeration}.
	 * </ul>
	 *
	 * We also set by default the {@link Enumerstion() enumeration} to the value of the
	 * {@link Code() code}, if the term has the {@link kTYPE_ENUMERATION enumeration}
	 * {@link Kind() kind} and the term {@link Enumeration() enumerations} are empty.
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
		// Set default kinds.
		//
		if( $this->Cardinality() !== NULL )
			$this->Kind( kTYPE_ATTRIBUTE, TRUE );
		if( $this->Type() !== NULL )
			$this->Kind( kTYPE_MEASURE, TRUE );
		if( $this->Enumeration() !== NULL )
			$this->Kind( kTYPE_ENUMERATION, TRUE );
		
		//
		// Set default enumeration.
		//
		if( ($this->Kind( kTYPE_ENUMERATION ) !== NULL)
		 && (! $this->Enumeration()) )
			$this->Enumeration( $this->Code(), TRUE );
		
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
	 * This method will check the contents of the object and return the correct
	 * {@link _IsInited() initialised} status according to the contents of the
	 * {@link Kind() kind} {@link kTAG_KIND property}:
	 *
	 * <ul>
	 *	<li><i>Default</i>: By default the object must have its {@link kTAG_CODE code} set,
	 *		this is {@link COntologyTermObject inherited}.
	 *	<li><i>{@link kTYPE_NAMESPACE kTYPE_NAMESPACE}</i>: It uses the default
	 *		requirements.
	 *	<li><i>{@link kTYPE_ONTOLOGY kTYPE_ONTOLOGY}</i>: It requires the
	 *		{@link kTAG_NAME name}.
	 *	<li><i>{@link kTYPE_PREDICATE kTYPE_PREDICATE}</i>: It requires the
	 *		{@link kTAG_NAME name}.
	 *	<li><i>{@link kTYPE_ATTRIBUTE kTYPE_ATTRIBUTE}</i>: It requires the
	 *		{@link kTAG_NAME name} and {@link kTAG_CARDINALITY cardinality}.
	 *	<li><i>{@link kTYPE_MEASURE kTYPE_MEASURE}</i>: It requires the
	 *		{@link kTAG_NAME name} and {@link kTAG_TYPE type}.
	 *	<li><i>{@link kTYPE_ENUMERATION kTYPE_ENUMERATION}</i>: It requires the
	 *		{@link kTAG_NAME name} and {@link kTAG_ENUM enumeration}.
	 * </ul>
	 *
	 * @access protected
	 * @return boolean
	 */
	protected function _Inited()
	{
		//
		// Check inherited status.
		//
		if( ! $this->_IsInited() )
			return FALSE;															// ==>
		
		//
		// Init local storage.
		//
		$props = Array();
		
		//
		// Collect required properties.
		//
		if( ($this->Kind( kTYPE_ONTOLOGY ) !== NULL)
		 || ($this->Kind( kTYPE_PREDICATE ) !== NULL) )
			$props[ kTAG_NAME ] = kTAG_NAME;
		if( $this->Kind( kTYPE_ENUMERATION ) !== NULL )
		{
			$props[ kTAG_NAME ] = kTAG_NAME;
			$props[ kTAG_TYPE ] = kTAG_ENUM;
		}
		if( $this->Kind( kTYPE_MEASURE ) !== NULL )
		{
			$props[ kTAG_NAME ] = kTAG_NAME;
			$props[ kTAG_TYPE ] = kTAG_TYPE;
		}
		if( $this->Kind( kTYPE_ATTRIBUTE ) !== NULL )
		{
			$props[ kTAG_NAME ] = kTAG_NAME;
			$props[ kTAG_CARDINALITY ] = kTAG_CARDINALITY;
		}
		
		//
		// Check required properties.
		//
		foreach( $props as $prop )
		{
			if( ! $this->offsetExists( $prop ) )
				return FALSE;														// ==>
		}
		
		return TRUE;																// ==>
	
	} // _Inited.

	 

} // class COntologyTerm.


?>
