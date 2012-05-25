<?php

/**
 * <i>COntologyTermObject</i> class definition.
 *
 * This file contains the class definition of <b>COntologyTermObject</b> which represents the
 * ancestor of ontology term objects.
 *
 *	@package	MyWrapper
 *	@subpackage	Ontology
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 16/04/2012
 */

/*=======================================================================================
 *																						*
 *								COntologyTermObject.php									*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CTerm.php" );

/**
 * Ontology term ancestor.
 *
 * This class defines a {@link CTerm term} that resides in an ontology, it overloads ita
 * {@link CTerm parent} to hash the {@link _id() identifier} into a
 * {@link CDataTypeBinary binary} string and enforce the {@link _IsEncoded() encoded}
 * {@link Status() status} {@link kFLAG_STATE_ENCODED flag}.
 *
 * The class also adds the following properties:
 *
 * <ul>
 *	<li><i>{@link kTAG_REFERENCE_SYNONYM kTAG_REFERENCE_SYNONYM}</i>: This
 *		{@link Synonym() property} can be used to store synonyms as strings.
 *	<li><i>{@link kTAG_REFERENCE_XREF kTAG_REFERENCE_XREF}</i>: This {@link Xref() property}
 *		can be used to store cross references to other terms in the ontology.
 *	<li><i>{@link kTAG_SOURCE kTAG_SOURCE}</i>: This {@link Source() property} can be used
 *		to provide the source of the term.
 * </ul>
 *
 * We declare this class abstract to force the creation of specific ontology term types.
 *
 *	@package	MyWrapper
 *	@subpackage	Ontology
 */
abstract class COntologyTermObject extends CTerm
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
	 * We {@link CTerm::__construct() overload} the constructor to
	 * {@link _CheckReference() normalise} the provided identifier.
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
		// Normalise identifier.
		//
		$theIdentifier = $this->_CheckReference( $theIdentifier );

		//
		// Call ancestor method.
		//
		parent::__construct( $theContainer, $theIdentifier, $theModifiers );
		
	} // Constructor.

		

/*=======================================================================================
 *																						*
 *								PUBLIC STRUCTURE INTERFACE								*
 *																						*
 *======================================================================================*/


	 
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
		
		return $this->_ManageObjectList( kTAG_REFS, $reference,
													$theOperation,
													$getOld );						// ==>

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
	 *	_PrepareCreate																	*
	 *==================================================================================*/

	/**
	 * Normalise parameters of a create.
	 *
	 * We overload this method to enforce the {@link kFLAG_STATE_ENCODED encoded} modifier.
	 *
	 * @param reference			   &$theContainer		Object container.
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Create modifiers.
	 *
	 * @access protected
	 *
	 * @uses _IsEncoded()
	 *
	 * @see kFLAG_STATE_ENCODED
	 */
	protected function _PrepareCreate( &$theContainer, &$theIdentifier, &$theModifiers )
	{
		//
		// Set encoded flag.
		//
		$theModifiers |= kFLAG_STATE_ENCODED;
		
		//
		// Call parent method.
		//
		parent::_PrepareCreate( $theContainer, $theIdentifier, $theModifiers );
	
	} // _PrepareCreate.

	 
	/*===================================================================================
	 *	_PrepareCommit																	*
	 *==================================================================================*/

	/**
	 * Normalise before a store.
	 *
	 * We overload this method to enforce the {@link kFLAG_STATE_ENCODED encoded} modifier.
	 *
	 * @param reference			   &$theContainer		Object container.
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Commit modifiers.
	 *
	 * @access protected
	 *
	 * @throws {@link CException CException}
	 *
	 * @uses _IsInited()
	 *
	 * @see kFLAG_STATE_ENCODED
	 */
	protected function _PrepareCommit( &$theContainer, &$theIdentifier, &$theModifiers )
	{
		//
		// Set encoded flag.
		//
		$theModifiers |= kFLAG_STATE_ENCODED;
		
		//
		// Call parent method.
		//
		parent::_PrepareCommit( $theContainer, $theIdentifier, $theModifiers );
		
		//
		// Set global identifier.
		//
		$this[ kTAG_GID ] = $this->_index();
	
	} // _PrepareCommit.

		

/*=======================================================================================
 *																						*
 *								PROTECTED REFERENCE UTILITIES							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_CheckReference																	*
	 *==================================================================================*/

	/**
	 * Normalise reference parameter.
	 *
	 * This method can be used to normalise a parameter that is supposed to be a reference
	 * to another term, that is, a binary string hash of the term's
	 * {@link _index() identifier} converted to a {@link CDataTypeBinary binary}
	 * standard type.
	 *
	 * The method will perform the following conversions:
	 *
	 * <ul>
	 *	<li><i>{@link CDataTypeBinary CDataTypeBinary}</i>: This is the default data type
	 *		for the identifier.
	 *	<li><i>{@link MongoBinData MongoBinData}</i>: This type will be converted to the
	 *		standard {@link CDataTypeBinary CDataTypeBinary} type.
	 *	<li><i>COntologyTermObject</i>: Objects of the same class will have their
	 *		{@link _id() identifier} extracted.
	 *	<li><i>NULL</i>: NULL data will simply be passed.
	 *	<li><i>other</i>: Any other data type is assumed to be the term's
	 *		{@link _index() identifier}, so it will be hashed into a binary string and
	 *		converted to the standard {@link CDataTypeBinary CDataTypeBinary} type.
	 *	<li><i>array</i>: Arrays cannot be converted to string, so the method will raise an
	 *		exception.
	 * </ul>
	 *
	 * @param mixed					$theReference		Object reference.
	 *
	 * @access protected
	 * @return CDataTypeBinary
	 *
	 * @uses _IsEncoded()
	 *
	 * @see kFLAG_STATE_ENCODED
	 */
	protected function _CheckReference( $theReference )
	{
		//
		// Handle identifier.
		//
		if( $theReference !== NULL )
		{
			//
			// Skip default type.
			//
			if( ! ($theReference instanceof CDataTypeBinary) )
			{
				//
				// Handle ontology term.
				//
				if( $theReference instanceof self )
					return $theReference->_id();									// ==>
				
				//
				// Handle MongoBinData.
				//
				if( $theReference instanceof MongoBinData )
					return new CDataTypeBinary( $theReference->bin );				// ==>
			
				//
				// Trap arrays.
				//
				if( is_array( $theReference ) )
					throw new CException
						( "Invalid object reference",
						  kERROR_UNSUPPORTED,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Reference' => $theReference ) );				// !@! ==>
				
				//
				// Convert to identifier.
				//
				return $this->HashIndex( $theReference );							// ==>
			
			} // Not default type.
		
		} // Provided reference.
		
		return $theReference;														// ==>
	
	} // _CheckReference.

	 

} // class COntologyTermObject.


?>
