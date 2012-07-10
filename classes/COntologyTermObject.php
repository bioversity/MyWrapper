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
require_once( kPATH_LIBRARY_SOURCE."CCodedUnitObject.php" );

/**
 * Kind.
 *
 * This include file contains the kind trait definitions.
 */
require_once( kPATH_LIBRARY_TRAITS."TKind.php" );

/**
 * Creation and modification.
 *
 * This include file contains the creation and last modification trait definitions.
 */
require_once( kPATH_LIBRARY_TRAITS."TDateStamp.php" );

/**
 * Tokens.
 *
 * This include file contains all default token definitions.
 */
require_once( kPATH_LIBRARY_DEFINES."Tokens.inc.php" );

/**
 * Ontology term ancestor.
 *
 * An ontology term represents a concept identified by a pair of elements: the
 * {@link NS() namespace} and the {@link Code() code}. The {@link NS() namespace} groups all
 * {@link Code() codes} belonging to a specific category, the {@link Code() code} identifies
 * the term within the {@link NS() namespace}.
 *
 * The unique {@link _index() identifier} of instances from this class are formed by the
 * combination of the {@link NS() namespace} and @link Code() code}, separated by a
 * {@link kTOKEN_NAMESPACE_SEPARATOR separator} token; if the term has no namespace, the
 * token is omitted.
 *
 * This class represents the ancestor of ontology terms, it adds to its
 * {@link CCodedUnitObject parent} the following requirements and features:
 *
 * <ul>
 *	<li><i>{@link _CheckReference() _CheckReference}</i>: When instantiating the class, the
 *		parameter provided as the identifier is required to be an object reference.
 *	<li><i>{@link kFLAG_STATE_ENCODED kFLAG_STATE_ENCODED} flag</i>: This flag is enforced,
 *		which means that all derived classes must use the standard {@link CDataType types}.
 * </ul>
 *
 * We declare this class abstract to force the creation of specific ontology term types.
 *
 *	@package	MyWrapper
 *	@subpackage	Ontology
 */
abstract class COntologyTermObject extends CCodedUnitObject
{
		

/*=======================================================================================
 *																						*
 *										TRAITS											*
 *																						*
 *======================================================================================*/

	use
	 
	/*===================================================================================
	 *	Kind																			*
	 *==================================================================================*/

	/**
	 * Manage file kind.
	 *
	 * This attribute records the various terms relating to the current kind or type of the
	 * file.
	 */
	TKind,
	 
	/*===================================================================================
	 *	Creation and modification dates													*
	 *==================================================================================*/

	/**
	 * Manage creation and modification dates.
	 *
	 * These two attributes represent respectively the dataset's {@link Created() creation}
	 * and the the dataset's last {@link Modified() modification} time-stamps.
	 */
	TDateStamp;

		

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
	 * We {@link CCodedUnitObject::__construct() overload} the constructor to
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
	 * This method can be used to handle the term {@link kTAG_NAMESPACE namespace}, it uses
	 * the standard accessor {@link CAttribute::ManageOffset() method} to manage the
	 * {@link kTAG_NAMESPACE offset}.
	 *
	 * The namespace collects a series of terms under a common group in which each term
	 * {@link Code() code} is unique, this {@link kTAG_NAMESPACE offset} represents a term
	 * reference.
	 *
	 * For a more in-depth reference of this method, please consult the
	 * {@link CAttribute::ManageOffset() CAttribute::ManageOffset} method, in which the
	 * second parameter will be the constant {@link kTAG_NAMESPACE kTAG_NAMESPACE}.
	 *
	 * @param mixed					$theValue			Value.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses CAttribute::ManageOffset()
	 *
	 * @see kTAG_NAMESPACE
	 */
	public function NS( $theValue = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageOffset
				( $this, kTAG_NAMESPACE, $theValue, $getOld );						// ==>

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
		}
		
		return parent::Code( $theValue, $getOld );									// ==>

	} // Code.


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
 *							PUBLIC MANING ATTRIBUTES INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Name																			*
	 *==================================================================================*/

	/**
	 * Manage term name.
	 *
	 * This method can be used to manage the term {@link kTAG_NAME name}, it manages an
	 * array of structures with the following offsets:
	 *
	 * <ul>
	 *	<li><i>{@link kTAG_LANGUAGE kTAG_LANGUAGE}</i>: The name's language, this element
	 *		represents the code of the language in which the next element is expressed in.
	 *	<li><i>{@link kTAG_DATA kTAG_DATA}</i>: The term name or label.
	 * </ul>
	 *
	 * The parameters to this method are:
	 *
	 * <ul>
	 *	<li><b>$theValue</b>: The name or operation:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the current value selected by the second parameter.
	 *		<li><i>FALSE</i>: Delete the value selected by the second parameter.
	 *		<li><i>other</i>: Set value selected by the second parameter.
	 *	 </ul>
	 *	<li><b>$theLanguage</b>: The name's language code:
	 *	 <ul>
	 *		<li><i>NULL</i>: This value indicates that the name has no language, in general,
	 *			when adding elements, this case applies to default elements.
	 *		<li><i>other</i>: All other types will be interpreted as the language code.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value <i>before</i> it was eventually modified.
	 *		<li><i>FALSE</i>: Return the value <i>after</i> it was eventually modified.
	 *	 </ul>
	 * </ul>
	 *
	 * @param mixed					$theValue			Term name or operation.
	 * @param mixed					$theLanguage		Term name language code.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses CAttribute::ManageTypedOffset()
	 *
	 * @see kTAG_NAME kTAG_LANGUAGE
	 */
	public function Name( $theValue = NULL, $theLanguage = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageTypedOffset( $this,
											  kTAG_NAME, kTAG_DATA,
											  kTAG_LANGUAGE, $theLanguage,
											  $theValue, $getOld );					// ==>

	} // Name.


	/*===================================================================================
	 *	Definition																		*
	 *==================================================================================*/

	/**
	 * Manage term definition.
	 *
	 * This method can be used to manage the term {@link kTAG_DEFINITION definition}, it
	 * manages an array of structures with the following offsets:
	 *
	 * <ul>
	 *	<li><i>{@link kTAG_LANGUAGE kTAG_LANGUAGE}</i>: The definition's language, this
	 *		element represents the code of the language in which the next element is
	 *		expressed in.
	 *	<li><i>{@link kTAG_DATA kTAG_DATA}</i>: The term definition or description.
	 * </ul>
	 *
	 * The parameters to this method are:
	 *
	 * <ul>
	 *	<li><b>$theValue</b>: The definition or operation:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the current value selected by the second parameter.
	 *		<li><i>FALSE</i>: Delete the value selected by the second parameter.
	 *		<li><i>other</i>: Set value selected by the second parameter.
	 *	 </ul>
	 *	<li><b>$theLanguage</b>: The definition's language code:
	 *	 <ul>
	 *		<li><i>NULL</i>: This value indicates that the definition has no language, in
	 *			general, when adding elements, this case applies to default elements.
	 *		<li><i>other</i>: All other types will be interpreted as the language code.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value <i>before</i> it was eventually modified.
	 *		<li><i>FALSE</i>: Return the value <i>after</i> it was eventually modified.
	 *	 </ul>
	 * </ul>
	 *
	 * @param mixed					$theValue			Term definition or operation.
	 * @param mixed					$theLanguage		Term definition language code.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses CAttribute::ManageTypedOffset()
	 *
	 * @see kTAG_DEFINITION kTAG_LANGUAGE
	 */
	public function Definition( $theValue = NULL, $theLanguage = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageTypedOffset( $this,
											  kTAG_DEFINITION, kTAG_DATA,
											  kTAG_LANGUAGE, $theLanguage,
											  $theValue, $getOld );					// ==>

	} // Definition.


	/*===================================================================================
	 *	Description																		*
	 *==================================================================================*/

	/**
	 * Manage term description.
	 *
	 * This method can be used to manage the term {@link kTAG_DESCRIPTION description}, it
	 * manages an array of structures with the following offsets:
	 *
	 * <ul>
	 *	<li><i>{@link kTAG_LANGUAGE kTAG_LANGUAGE}</i>: The description's language, this
	 *		element represents the code of the language in which the next element is
	 *		expressed in.
	 *	<li><i>{@link kTAG_DATA kTAG_DATA}</i>: The term description or comment.
	 * </ul>
	 *
	 * The parameters to this method are:
	 *
	 * <ul>
	 *	<li><b>$theValue</b>: The description or operation:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the current value selected by the second parameter.
	 *		<li><i>FALSE</i>: Delete the value selected by the second parameter.
	 *		<li><i>other</i>: Set value selected by the second parameter.
	 *	 </ul>
	 *	<li><b>$theLanguage</b>: The description's language code:
	 *	 <ul>
	 *		<li><i>NULL</i>: This value indicates that the description has no language, in
	 *			general, when adding elements, this case applies to default elements.
	 *		<li><i>other</i>: All other types will be interpreted as the language code.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value <i>before</i> it was eventually modified.
	 *		<li><i>FALSE</i>: Return the value <i>after</i> it was eventually modified.
	 *	 </ul>
	 * </ul>
	 *
	 * @param mixed					$theValue			Term description or operation.
	 * @param mixed					$theLanguage		Term description language code.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses CAttribute::ManageTypedOffset()
	 *
	 * @see kTAG_DESCRIPTION kTAG_LANGUAGE
	 */
	public function Description( $theValue = NULL, $theLanguage = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageTypedOffset( $this,
											  kTAG_DESCRIPTION, kTAG_DATA,
											  kTAG_LANGUAGE, $theLanguage,
											  $theValue, $getOld );					// ==>

	} // Description.

	 
	 
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
	 * This method can be used to manage the term {@link kTAG_REFERENCE_SYNONYM synonyms}
	 * list, these elements are strings that can be considered synonyms of the current term.
	 *
	 * This property is organised as an array of items structured as follows:
	 *
	 * <ul>
	 *	<li><i>{@link kTAG_KIND kTAG_KIND}</i>: The synonym kind, its value is provided in
	 *		the <i>$theType</i> parameter.
	 *	<li><i>{@link kTAG_DATA kTAG_DATA}</i>: The synonym values organised as an array of
	 *		strings.
	 * </ul>
	 *
	 * The method expects the following parameters:
	 *
	 * <ul>
	 *	<li><b>$theValue</b>: The synonym value.
	 *	<li><b>$theType</b>: The synonym type.
	 *	<li><b>$theOperation</b>: The operation:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the current value selected by the previous parameters.
	 *		<li><i>FALSE</i>: Delete the value selected by the previous parameters.
	 *		<li><i>other</i>: Set value selected by the previous parameters.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value <i>before</i> it was eventually modified.
	 *		<li><i>FALSE</i>: Return the value <i>after</i> it was eventually modified.
	 *	 </ul>
	 * </ul>
	 *
	 * The method makes use of a static
	 * {@link CAttribute::ManageTypedArrayOffset() method}, please consult its reference for
	 * more information.
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
		return CAttribute::ManageTypedArrayOffset( $this,
												   kTAG_REFERENCE_SYNONYM, kTAG_DATA,
												   kTAG_KIND, $theType,
												   $theValue, $theOperation,
												   $getOld );						// ==>

	} // Synonym.

	 
	/*===================================================================================
	 *	Xref																			*
	 *==================================================================================*/

	/**
	 * Manage cross-references.
	 *
	 * This method can be used to manage the term
	 * {@link kTAG_REFERENCE_XREF cross-references} list, these elements are references to
	 * other terms that can be considered synonyms of the current term, the reference should
	 * be the term's {@link _id() identifier}.
	 *
	 * This property is organised as an array of items structured as follows:
	 *
	 * <ul>
	 *	<li><i>{@link kTAG_KIND kTAG_KIND}</i>: The cross-reference kind, its value is
	 *		provided in the <i>$theType</i> parameter.
	 *	<li><i>{@link kTAG_DATA kTAG_DATA}</i>: The cross-reference values organised as an
	 *		array of term identifiers.
	 * </ul>
	 *
	 * The method expects the following parameters:
	 *
	 * <ul>
	 *	<li><b>$theValue</b>: The cross-reference.
	 *	<li><b>$theType</b>: The cross-reference type.
	 *	<li><b>$theOperation</b>: The operation:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the current value selected by the previous parameters.
	 *		<li><i>FALSE</i>: Delete the value selected by the previous parameters.
	 *		<li><i>other</i>: Set value selected by the previous parameters.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value <i>before</i> it was eventually modified.
	 *		<li><i>FALSE</i>: Return the value <i>after</i> it was eventually modified.
	 *	 </ul>
	 * </ul>
	 *
	 * The method makes use of a static
	 * {@link CAttribute::ManageTypedArrayOffset() method}, please consult its reference for
	 * more information.
	 *
	 * @param string				$theValue			Object or operation.
	 * @param mixed					$theType			Cross-reference type.
	 * @param mixed					$theOperation		Operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 */
	public function Xref( $theValue, $theType, $theOperation = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageTypedArrayOffset( $this,
												   kTAG_REFERENCE_XREF, kTAG_DATA,
												   kTAG_KIND, $theType,
												   $theValue, $theOperation,
												   $getOld );						// ==>

	} // Xref.


	 
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
	 * In this class we use the term's {@link kTAG_NAMESPACE namespace} and
	 * {@link kTAG_CODE code} to build the object's unique
	 * {@link kTAG_LID identifier}; if the {@link kTAG_NAMESPACE namespace} is
	 * missing, we use the {@link kTAG_CODE code}; if the {@link kTAG_NAMESPACE namespace}
	 * is present, we use it as a prefix with the {@link kTAG_CODE code}, separated by the
	 * {@link kTOKEN_NAMESPACE_SEPARATOR separator} token.
	 *
	 * @access protected
	 * @return string
	 */
	protected function _index()
	{
		//
		// Handle namespace.
		//
		if( ($ns = $this->NS()) !== NULL )
			return $ns.kTOKEN_NAMESPACE_SEPARATOR.$this->Code();					// ==>
			
		return $this->Code();
	
	} // _index.

	 

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
