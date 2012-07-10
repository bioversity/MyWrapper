<?php

/**
 * <i>TNameDescription</i> trait definition.
 *
 * This file contains the trait definition of <b>TNameDescription</b> which implements the
 * {@link Name() Name} and {@link Description() Description} trait.
 *
 *	@package	MyWrapper
 *	@subpackage	Traits
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 06/07/2012
*/

/*=======================================================================================
 *																						*
 *									TNameDescription.php								*
 *																						*
 *======================================================================================*/

/**
 * Attributes.
 *
 * This include file contains the attribute class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CAttribute.php" );

/**
 *	Name and description traits.
 *
 * This trait implements two methods that manage respectively the {@link Name() name} and
 * the {@link Description() description} attributes.
 *
 *	@package	MyWrapper
 *	@subpackage	Traits
 */
trait TNameDescription
{
		

/*=======================================================================================
 *																						*
 *								PUBLIC MEMBER INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Name																			*
	 *==================================================================================*/

	/**
	 * Manage name.
	 *
	 * This method can be used to manage an object's {@link kTAG_NAME name}, it manages an
	 * array of structures with the following offsets:
	 *
	 * <ul>
	 *	<li><i>{@link kTAG_LANGUAGE kTAG_LANGUAGE}</i>: The name's language, this element
	 *		represents the code of the language in which the next element is expressed in.
	 *	<li><i>{@link kTAG_DATA kTAG_DATA}</i>: The object's name or label.
	 * </ul>
	 *
	 * No two elements may share the same language and only one element may omit the
	 * language, the parameters to this method are:
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
	 * @see kTAG_NAME kTAG_DATA kTAG_LANGUAGE
	 */
	public function Name( $theValue = NULL, $theLanguage = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageTypedOffset( $this,
											  kTAG_NAME, kTAG_DATA, kTAG_LANGUAGE,
											  $theLanguage, $theValue, $getOld );	// ==>

	} // Name.

	 
	/*===================================================================================
	 *	Description																		*
	 *==================================================================================*/

	/**
	 * Manage description.
	 *
	 * This method can be used to manage the {@link kTAG_DESCRIPTION description}, it
	 * manages an array of structures with the following offsets:
	 *
	 * <ul>
	 *	<li><i>{@link kTAG_LANGUAGE kTAG_LANGUAGE}</i>: The description's language, this
	 *		element represents the code of the language in which the next element is
	 *		expressed in.
	 *	<li><i>{@link kTAG_DATA kTAG_DATA}</i>: The dataset description or comment.
	 * </ul>
	 *
	 * No two elements may share the same language and only one element may omit the
	 * language, the parameters to this method are:
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

	 

} // trait TNameDescription.


?>
