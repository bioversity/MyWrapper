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
 *	<li><i>{@link kTYPE_ROOT kTYPE_ROOT}</i>: It requires the {@link kTAG_NAME name} to be
 *		set.
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
	 *	NS																				*
	 *==================================================================================*/

	/**
	 * Manage term namespace.
	 *
	 * We {@link CTerm::NS() overload} this method in order to normalise the namespace: it
	 * must be provided as a string, so if you provide an object, it must be derived from
	 * this class and this method will use the provided object's {@link _index() index}.
	 *
	 * In this class we do not support instances as namespaces.
	 *
	 * @param mixed					$theValue			Value.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @see kTAG_NAMESPACE
	 */
	public function NS( $theValue = NULL, $getOld = FALSE )
	{
		//
		// Normalise value.
		//
		if( ($theValue !== NULL)
		 && ($theValue !== FALSE) )
		{
			//
			// Handle object.
			//
			if( $theValue instanceof self )
			{
				//
				// Check if it has a code.
				//
				if( $theValue->Code() !== NULL )
					$theValue = $theValue->_index();
				
				else
					throw new CException
						( "Invalid namespace reference",
						  kERROR_UNSUPPORTED,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Reference' => $theValue ) );					// !@! ==>
			
			} // Provided object.
			
			//
			// Convert to string
			//
			else
				$theValue = (string) $theValue;
		
		} // Provided new value.
		
		return parent::NS( $theValue, $getOld );									// ==>

	} // NS.

	 
	/*===================================================================================
	 *	Code																			*
	 *==================================================================================*/

	/**
	 * Manage code.
	 *
	 * We {@link CCodedUnitObject::Code() overload} this method to check the format of the
	 * code, we perform the following checks:
	 *
	 * <ul>
	 *	<li><i>Characters</i>: There are a number of characters that are forbidden in
	 *		codes, providing a code with such characters will trigger an exception:
	 *	 <ul>
	 *		<li><i>period (.)</i>: No periods can be embedded in codes, these conflict with
	 *			the sub-fields character in Mongo.
	 *		<li><i>at (@)</i>: No code may start with this character, this is reserved for
	 *			identifying annotation terms.
	 *	 </ul>
	 * </ul>
	 *
	 * @param mixed					$theValue			Value.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 */
	public function Code( $theValue = NULL, $getOld = FALSE )
	{
		//
		// Intercept assignements.
		//
		if( ($theValue !== NULL)
		 && ($theValue !== FALSE) )
		{
			//
			// Check periods.
			//
			if( strpos( (string) $theValue, '.' ) !== FALSE )
				throw new CException
					( "Term codes may not have periods in their codes",
					  kERROR_INVALID_PARAMETER,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Code' => $theValue ) );							// !@! ==>

			//
			// Check ats.
			//
			if( substr( (string) $theValue, 0, 1 ) == '@' )
				throw new CException
					( "Term codes may not start with the (@) sign",
					  kERROR_INVALID_PARAMETER,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Code' => $theValue ) );							// !@! ==>
		}
		
		return parent::Code( $theValue, $getOld );									// ==>

	} // Code.

	 
	/*===================================================================================
	 *	Node																			*
	 *==================================================================================*/

	/**
	 * Manage node references.
	 *
	 * This method can be used to handle the object's {@link kTAG_NODE node} references, it
	 * uses the standard accessor {@link CAttribute::ManageArrayOffset() method} to manage
	 * the list of nodes that point to this term.
	 *
	 * Each element of this list represents the ID of a node in the ontology: each time a
	 * node references this term, its identifier will be aded to this offset.
	 *
	 * For a more thorough reference of how this method works, please consult the
	 * {@link CAttribute::ManageArrayOffset() CAttribute::ManageArrayOffset} method, in
	 * which the second parameter will be the constant {@link kTAG_KIND kTAG_KIND}.
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
	 * @uses CAttribute::ManageArrayOffset()
	 *
	 * @see kTAG_NODE
	 */
	public function Node( $theValue = NULL, $theOperation = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageArrayOffset
					( $this, kTAG_NODE, $theValue, $theOperation, $getOld );		// ==>

	} // Node.

	 
	/*===================================================================================
	 *	Predicate																		*
	 *==================================================================================*/

	/**
	 * Manage predicate node references.
	 *
	 * This method can be used to handle the object's predicate {@link kTAG_EDGE node}
	 * references, it uses the standard accessor
	 * {@link CAttribute::ManageArrayOffset() method} to
	 * manage the list of predicate nodes that point to this term.
	 *
	 * Each element of this list represents the ID of an edge node in the ontology: each
	 * time an edge node references this term, its identifier will be aded to this offset.
	 *
	 * For a more thorough reference of how this method works, please consult the
	 * {@link CAttribute::ManageArrayOffset() CAttribute::ManageArrayOffset} method, in
	 * which the second parameter will be the constant {@link kTAG_KIND kTAG_KIND}.
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
	 * @uses CAttribute::ManageArrayOffset()
	 *
	 * @see kTAG_EDGE
	 */
	public function Predicate( $theValue = NULL, $theOperation = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageArrayOffset
					( $this, kTAG_EDGE, $theValue, $theOperation, $getOld );		// ==>

	} // Predicate.

	 
	/*===================================================================================
	 *	Enumeration																		*
	 *==================================================================================*/

	/**
	 * Manage enumerations.
	 *
	 * This method can be used to handle the object's {@link kTAG_ENUM enumerations}, it
	 * uses the standard accessor {@link CAttribute::ManageArrayOffset() method} to manage
	 * the list of enumerations.
	 *
	 * Each element of this list should indicate a code or acronym defining the current
	 * object
	 *
	 * For a more thorough reference of how this method works, please consult the
	 * {@link CAttribute::ManageArrayOffset() CAttribute::ManageArrayOffset} method, in
	 * which the second parameter will be the constant {@link kTAG_ENUM kTAG_ENUM}.
	 *
	 * @param mixed					$theValue			Value or index.
	 * @param mixed					$theOperation		Operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses CAttribute::ManageArrayOffset()
	 *
	 * @see kTAG_ENUM
	 */
	public function Enumeration( $theValue = NULL, $theOperation = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageArrayOffset
					( $this, kTAG_ENUM, $theValue, $theOperation, $getOld );		// ==>

	} // Enumeration.

	 
	/*===================================================================================
	 *	Type																			*
	 *==================================================================================*/

	/**
	 * Manage data type.
	 *
	 * This method can be used to manage the measure data {@link kTAG_TYPE type}, it uses
	 * the standard accessor {@link CAttribute::ManageOffset() method} to manage the
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
		return CAttribute::ManageOffset( $this, kTAG_TYPE, $theValue, $getOld );	// ==>

	} // Type.

	 
	/*===================================================================================
	 *	Pattern																			*
	 *==================================================================================*/

	/**
	 * Manage patterns.
	 *
	 * This method can be used to handle the object's {@link kTAG_PATTERN patterns}, it
	 * uses the standard accessor {@link CAttribute::ManageArrayOffset() method} to manage
	 * the list of patterns.
	 *
	 * This term usually describes a {@link kTYPE_STRING string} data element that is
	 * restricted by a series of string patterns, use the standard XML format.
	 *
	 * For a more thorough reference of how this method works, please consult the
	 * {@link CAttribute::ManageArrayOffset() CAttribute::ManageArrayOffset} method, in
	 * which the second parameter will be the constant {@link kTAG_PATTERN kTAG_PATTERN}.
	 *
	 * @param mixed					$theValue			Value or index.
	 * @param mixed					$theOperation		Operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses CAttribute::ManageArrayOffset()
	 *
	 * @see kTAG_PATTERN
	 */
	public function Pattern( $theValue = NULL, $theOperation = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageArrayOffset
					( $this, kTAG_PATTERN, $theValue, $theOperation, $getOld );		// ==>

	} // Pattern.

	 
	/*===================================================================================
	 *	Cardinality																		*
	 *==================================================================================*/

	/**
	 * Manage data cardinality.
	 *
	 * This method can be used to manage the measure data {@link kTAG_CARDINALITY type}, it
	 * uses the standard accessor {@link CAttribute::ManageOffset() method} to manage the
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
		return CAttribute::ManageOffset
				( $this, kTAG_CARDINALITY, $theValue, $getOld );					// ==>

	} // Cardinality.

	 
	/*===================================================================================
	 *	Unit																			*
	 *==================================================================================*/

	/**
	 * Manage data unit.
	 *
	 * This method can be used to handle the measure {@link kTAG_UNIT unit}, it uses the
	 * standard accessor {@link CAttribute::ManageOffset() method} to manage the
	 * {@link kTAG_UNIT offset}.
	 *
	 * The value provided to this property should be the {@link _id() identifier} of a term
	 * hat defines a unit.
	 *
	 * For a more in-depth reference of this method, please consult the
	 * {@link CAttribute::ManageOffset() CAttribute::ManageOffset} method, in which the
	 * second parameter will be the constant {@link kTAG_VALID kTAG_VALID}.
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
	 * @uses CAttribute::ManageOffset()
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
		
		return CAttribute::ManageOffset( $this, kTAG_UNIT, $theValue, $getOld );	// ==>

	} // Unit.

	 
	/*===================================================================================
	 *	Examples																		*
	 *==================================================================================*/

	/**
	 * Manage data examples.
	 *
	 * This method can be used to handle data {@link kTAG_EXAMPLES examples}, it is a list
	 * of strings handles by the standard accessor
	 * {@link CAttribute::ManageArrayOffset() method} in the examples
	 * {@link kTAG_EXAMPLES offset}.
	 *
	 * The provided value should be an example of how the current term could be represented,
	 * an extensive set of examples should be included in order to provide enough
	 * information to handle correctly any kind of output referenced data elements could
	 * represent.
	 *
	 * For a more in-depth reference of this method, please consult the
	 * {@link CAttribute::ManageArrayOffset() CAttribute::ManageArrayOffset} method, in
	 * which the second parameter will be the constant {@link kTAG_VALID kTAG_VALID}.
	 *
	 * @param mixed					$theValue			Value or index.
	 * @param mixed					$theOperation		Operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses CAttribute::ManageArrayOffset()
	 *
	 * @see kTAG_EXAMPLES
	 */
	public function Examples( $theValue = NULL, $theOperation = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageArrayOffset
					( $this, kTAG_EXAMPLES, $theValue, $theOperation, $getOld );	// ==>

	} // Examples.

		

/*=======================================================================================
 *																						*
 *							PUBLIC RELATION ATTRIBUTES INTERFACE						*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Synonym																			*
	 *==================================================================================*/

	/**
	 * Manage synonyms.
	 *
	 * We {@link CTerm::Synonym() overload} this method to restrict the synonym
	 * {@link kTAG_KIND kind}: the <i>$theType</i> parameter must take one of the following
	 * values:
	 *
	 * <ul>
	 *	<li><i>{@link kTYPE_EXACT kTYPE_EXACT}</i>: Exact synonym.
	 *	<li><i>{@link kTYPE_BROAD kTYPE_BROAD}</i>: Broad synonym.
	 *	<li><i>{@link kTYPE_NARROW kTYPE_NARROW}</i>: Narrow synonym.
	 *	<li><i>{@link kTYPE_RELATED kTYPE_RELATED}</i>: Related synonym.
	 * </ul>
	 *
	 * @param string				$theValue			Synonym.
	 * @param mixed					$theType			Synonym type.
	 * @param mixed					$theOperation		Operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 */
	public function Synonym( $theValue, $theType, $theOperation = NULL, $getOld = FALSE )
	{
		//
		// Check synonym kind.
		//
		if( $theOperation !== NULL )
		{
			//
			// Parse type.
			//
			switch( $theType )
			{
				case kTYPE_EXACT:
				case kTYPE_BROAD:
				case kTYPE_NARROW:
				case kTYPE_RELATED:
					break;
				
				default:
					throw new CException
						( "Invalid synonym type",
						  kERROR_UNSUPPORTED,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Type' => $theType ) );						// !@! ==>
			}
		
		} // Provided synonym kind.
		
		return parent::Synonym( $theValue, $theType, $theOperation, $getOld );		// ==>

	} // Synonym.

	 
	/*===================================================================================
	 *	Xref																			*
	 *==================================================================================*/

	/**
	 * Manage cross-references.
	 *
	 * We {@link CTerm::Xref() overload} this method to restrict the cross-reference
	 * {@link kTAG_KIND kind}: the <i>$theType</i> parameter must take one of the following
	 * values:
	 *
	 * <ul>
	 *	<li><i>{@link kTYPE_EXACT kTYPE_EXACT}</i>: Exact cross-reference.
	 *	<li><i>{@link kTYPE_BROAD kTYPE_BROAD}</i>: Broad cross-reference.
	 *	<li><i>{@link kTYPE_NARROW kTYPE_NARROW}</i>: Narrow cross-reference.
	 *	<li><i>{@link kTYPE_RELATED kTYPE_RELATED}</i>: Related cross-reference.
	 * </ul>
	 *
	 * We also {@link _CheckReference() filter} the provided value to extract the object
	 * identifier.
	 *
	 * @param string				$theValue			Reference or operation.
	 * @param mixed					$theType			Reference type.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 */
	public function Xref( $theValue, $theType, $theOperation = NULL, $getOld = FALSE )
	{
		//
		// Normalise reference.
		//
		if( ($theValue !== NULL)
		 && ($theValue !== FALSE) )
			$theValue = $this->_CheckReference( $theValue );
		
		//
		// Check cross-reference kind.
		//
		if( $theOperation !== NULL )
		{
			//
			// Parse type.
			//
			switch( $theType )
			{
				case kTYPE_EXACT:
				case kTYPE_BROAD:
				case kTYPE_NARROW:
				case kTYPE_RELATED:
					break;
				
				default:
					throw new CException
						( "Invalid cross-reference type",
						  kERROR_UNSUPPORTED,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Type' => $theType ) );						// !@! ==>
			}
		
		} // Provided cross-reference kind.
		
		return parent::Xref( $theValue, $theType, $theOperation, $getOld );			// ==>

	} // Xref.

	 
	/*===================================================================================
	 *	Relate																			*
	 *==================================================================================*/

	/**
	 * Manage object references.
	 *
	 * We {@link CRelatedUnitObject::Relate() override} this method to handle references
	 * structured as follows:
	 *
	 * <ul>
	 *	<li><i>{@link kTAG_KIND kTAG_KIND}</i>: This offset represents the reference
	 *		predicate, it may be omitted if the reference has no type or when we want to
	 *		define a default reference. By default we expect here a term
	 *		{@link _CheckReference() reference}.
	 *	<li><i>{@link kTAG_DATA kTAG_DATA}</i>: This offset represents the referenced term,
	 *		it should be in the form of an object {@link _CheckReference() reference}.
	 * </ul>
	 *
	 * The main reason to override the method is to ensure references are standard
	 * {@link CDataTypeBinary binary} strings.
	 *
	 * @param mixed					$theObject			Reference object.
	 * @param mixed					$thePredicate		Reference predicate.
	 * @param mixed					$theOperation		Operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @see kTAG_LINK_OUT kTAG_KIND kTAG_DATA
	 */
	public function Relate( $theObject, $thePredicate = NULL,
										$theOperation = NULL,
										$getOld = FALSE )
	{
		//
		// Convert reference object.
		//
		$theObject = $this->_CheckReference( $theObject );
		
		//
		// Convert reference predicate.
		//
		$thePredicate = $this->_CheckReference( $thePredicate );
		
		//
		// Create reference.
		//
		$reference = ( $thePredicate !== NULL )
				   ? array( kTAG_KIND => $thePredicate, kTAG_DATA => $theObject )
				   : $theObject;
		
		return CAttribute::ManageObjectList( $this,
											 kTAG_REFS, kTAG_KIND, kTAG_DATA,
											 $reference, $theOperation,
											 $getOld );								// ==>

	} // Relate.

		
	/*===================================================================================
	 *	Valid																			*
	 *==================================================================================*/

	/**
	 * Manage valid reference.
	 *
	 * We {@link CRelatedUnitObject::Valid() overload} this method to
	 * {@link _CheckReference() ensure} that references are provided in the correct manner,
	 * that is, as a {@link CDataTypeBinary binary} standard type.
	 *
	 * @param mixed					$theValue			Value.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses _CheckReference()
	 * @uses Valid()
	 *
	 * @see kTAG_VALID
	 */
	public function Valid( $theValue = NULL, $getOld = FALSE )
	{
		//
		// Normalise identifier.
		//
		if( ($theValue !== NULL)
		 && ($theValue !== FALSE) )
			$theValue = $this->_CheckReference( $theValue );
		
		//
		// Call parent method.
		//
		return parent::Valid( $theValue, $getOld );									// ==>

	} // Valid.

		

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
 *									STATIC INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	ValidObject																		*
	 *==================================================================================*/

	/**
	 * Return a valid object.
	 *
	 * We {@link CRelatedUnitObject::ValidObject() overload} this method to enforce the
	 * {@link _IsEncoded() encoded} {@link kFLAG_STATE_ENCODED flag}.
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
		// Set encoded flag.
		//
		$theModifiers |= kFLAG_STATE_ENCODED;
		
		//
		// Call parent method.
		//
		return parent::ValidObject( $theContainer, $theIdentifier, $theModifiers );	// ==>
		
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
//		if( $this->Cardinality() !== NULL )
//			$this->Kind( kTYPE_ATTRIBUTE, TRUE );
//		if( $this->Type() !== NULL )
//			$this->Kind( kTYPE_MEASURE, TRUE );
//		if( $this->Enumeration() !== NULL )
//			$this->Kind( kTYPE_ENUMERATION, TRUE );
		
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
	 *	<li><i>{@link kTYPE_ROOT kTYPE_ROOT}</i>: It requires the {@link kTAG_NAME name}.
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
		if( ($this->Kind( kTYPE_ROOT ) !== NULL)
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
