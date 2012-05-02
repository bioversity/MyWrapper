<?php

/**
 * <i>CTerm</i> class definition.
 *
 * This file contains the class definition of <b>CTerm</b> which represents the ancestor
 * of term objects.
 *
 *	@package	MyWrapper
 *	@subpackage	Ontology
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 16/04/2012
 */

/*=======================================================================================
 *																						*
 *										CTerm.php										*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CCodedUnitObject.php" );

/**
 * Tokens.
 *
 * This include file contains all default token definitions.
 */
require_once( kPATH_LIBRARY_DEFINES."Tokens.inc.php" );

/**
 * Term.
 *
 * A term represents a concept identified by a pair of elements: the {@link NS() namespace}
 * and the {@link Code() code}, the {@link NS() namespace} groups all {@link Code() codes}
 * belonging to a specific category, the {@link Code() code} identifies the term within the
 * {@link NS() namespace}.
 *
 * Terms are used in ontologies and to tag data elements, We add to the
 * {@link CCodedUnitObject parent} the following properties:
 *
 * <ul>
 *	<li><i>{@link kTAG_NAMESPACE kTAG_NAMESPACE}</i>: This attribute contains the
 *		{@link NS() namespace} of the current term; note that this string is the
 *		{@link _index() identifier} of the namespace term.
 *	<li><i>{@link kTAG_NAME kTAG_NAME}</i>: This attribute represents the current term's
 *		{@link Name() name}.
 *	<li><i>{@link kTAG_DESCRIPTION kTAG_DESCRIPTION}</i>: This attribute represents the
 *		current term's {@link Definition() description} or definition.
 * </ul>
 *
 * The unique {@link _index() identifier} of instances from this class are formed by the
 * combination of the {@link NS() namespace} and @link Code() code}, separated by a
 * {@link kTOKEN_NAMESPACE_SEPARATOR separator} token; if the term has no namespace, the
 * token is omitted.
 *
 *	@package	MyWrapper
 *	@subpackage	Ontology
 */
class CTerm extends CCodedUnitObject
{
		

/*=======================================================================================
 *																						*
 *								PUBLIC MEMBER INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	NS																				*
	 *==================================================================================*/

	/**
	 * Manage term namespace.
	 *
	 * This method can be used to handle the term {@link kTAG_NAMESPACE namespace}, it uses
	 * the standard accessor {@link _ManageOffset() method} to manage the
	 * {@link kTAG_NAMESPACE offset}.
	 *
	 * The namespace collects a series of terms under a common group in which each term
	 * {@link Code() code} is unique, this {@link kTAG_NAMESPACE offset} represents a term
	 * reference.
	 *
	 * For a more in-depth reference of this method, please consult the
	 * {@link _ManageOffset() _ManageOffset} method, in which the first parameter will be
	 * the constant {@link kTAG_NAMESPACE kTAG_NAMESPACE}.
	 *
	 * @param mixed					$theValue			Value.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses _ManageOffset
	 *
	 * @see kTAG_NAMESPACE
	 */
	public function NS( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kTAG_NAMESPACE, $theValue, $getOld );			// ==>

	} // NS.

	 
	/*===================================================================================
	 *	NamespaceName																	*
	 *==================================================================================*/

	/**
	 * Manage term namespace name.
	 *
	 * This method can be used to handle the term {@link kOFFSET_NAMESPACE namespace}, it
	 * uses the standard accessor {@link _ManageOffset() method} to manage the
	 * {@link kOFFSET_NAMESPACE offset}.
	 *
	 * This property represents the term's namespace name or acronym; not to be confused
	 * with the {@link kTAG_NAMESPACE kTAG_NAMESPACE} offset which represents the namespace
	 * term reference.
	 *
	 * For a more in-depth reference of this method, please consult the
	 * {@link _ManageOffset() _ManageOffset} method, in which the first parameter will be
	 * the constant {@link kOFFSET_NAMESPACE kOFFSET_NAMESPACE}.
	 *
	 * @param mixed					$theValue			Value.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses _ManageOffset
	 *
	 * @see kOFFSET_NAMESPACE
	 */
	public function NamespaceName( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kOFFSET_NAMESPACE, $theValue, $getOld );		// ==>

	} // NamespaceName.

	 
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
	 * @uses _ManageKindArrayOffset
	 *
	 * @see kTAG_NAME kTAG_LANGUAGE
	 */
	public function Name( $theValue = NULL, $theLanguage = NULL, $getOld = FALSE )
	{
		return $this->_ManageKindArrayOffset
			( kTAG_NAME, kTAG_LANGUAGE, $theLanguage, $theValue, $getOld );			// ==>

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
	 * @uses _ManageKindArrayOffset
	 *
	 * @see kTAG_DEFINITION kTAG_LANGUAGE
	 */
	public function Definition( $theValue = NULL, $theLanguage = NULL, $getOld = FALSE )
	{
		return $this->_ManageKindArrayOffset
			( kTAG_DEFINITION, kTAG_LANGUAGE, $theLanguage, $theValue, $getOld );	// ==>

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
	 * @uses _ManageKindArrayOffset
	 *
	 * @see kTAG_DESCRIPTION kTAG_LANGUAGE
	 */
	public function Description( $theValue = NULL, $theLanguage = NULL, $getOld = FALSE )
	{
		return $this->_ManageKindArrayOffset
			( kTAG_DESCRIPTION, kTAG_LANGUAGE, $theLanguage, $theValue, $getOld );	// ==>

	} // Description.

	 
	/*===================================================================================
	 *	Version																			*
	 *==================================================================================*/

	/**
	 * Manage version.
	 *
	 * This method can be used to manage the term public {@link kOFFSET_VERSION version}, it
	 * uses the standard accessor {@link _ManageOffset() method} to manage the
	 * {@link kOFFSET_VERSION offset}:
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
	 * Note that the object features another version {@link kTAG_VERSION offset} which is
	 * automatically managed by the {@link CPersistentUnitObject ancestor}, the latter is an
	 * internal value, this one can be considere the <i>public</i> version.
	 *
	 * @param NULL|FALSE|string		$theValue			Version or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @see kOFFSET_VERSION
	 */
	public function Version( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kOFFSET_VERSION, $theValue, $getOld );			// ==>

	} // Version.

		

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

	 

} // class CTerm.


?>
