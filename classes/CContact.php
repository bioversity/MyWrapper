<?php

/**
 * <i>CContact</i> class definition.
 *
 * This file contains the class definition of <b>CContact</b> which represents an
 * {@link CEntity entity} mapping a general purpose contact.
 *
 *	@package	MyWrapper
 *	@subpackage	Entities
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 20/03/2012
 */

/*=======================================================================================
 *																						*
 *										CContact.php									*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CEntity.php" );

/**
 * Address.
 *
 * This include file contains the address class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CMailAddress.php" );

/**
 * Contact.
 *
 * This class overloads its {@link CEntity ancestor} to implement a contact entity.
 *
 * Contacts are entities that need to be tracked by {@link Mail() mail},
 * {@link Phone() telephone} or {@link Email() e-mail}.
 *
 * The class features the following properties:
 *
 * <ul>
 *	<li><i>{@link kOFFSET_MAIL kOFFSET_MAIL}</i>: The contact {@link Mail() mailing} address
 *		list.
 *	<li><i>{@link kOFFSET_PHONE kOFFSET_PHONE}</i>: The contact {@link Phone() telephone}
 *		list.
 * </ul>
 *
 * These three properties are managed as an array of elements discriminated by the
 * {@link kTAG_KIND kTAG_KIND} offset, no two elements may share the same
 * {@link kTAG_KIND kind}.
 *
 * The object inherits the {@link _IsInited() inited} {@link kFLAG_STATE_INITED status}, it
 * is the responsibility of concrete classes to implement this feature.
 *
 *	@package	MyWrapper
 *	@subpackage	Entities
 */
class CContact extends CEntity
{
		

/*=======================================================================================
 *																						*
 *								PUBLIC MEMBER INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Mail																			*
	 *==================================================================================*/

	/**
	 * Manage mailing address.
	 *
	 * This method can be used to manage the contact {@link kOFFSET_MAIL mailing} address,
	 * it manages an array of structures with the following offsets:
	 *
	 * <ul>
	 *	<li><i>{@link kTAG_KIND kTAG_KIND}</i>: The mailing address kind, this could be
	 *		<i>home</i>, <i>work</i> or <i>other</i>. This element represents the array
	 *		key, although technically it is implemented as an element to allow searching on
	 *		all values.
	 *	<li><i>{@link kTAG_DATA kTAG_DATA}</i>: The mailing address structure or string,
	 *		this element should hold the actual address.
	 * </ul>
	 *
	 * The parameters to this method are:
	 *
	 * <ul>
	 *	<li><b>$theValue</b>: The value or operation:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the current value selected by the second parameter.
	 *		<li><i>FALSE</i>: Delete the value selected by the second parameter.
	 *		<li><i>other</i>: Set value selected by the second parameter.
	 *	 </ul>
	 *	<li><b>$theType</b>: The element type, kind or index:
	 *	 <ul>
	 *		<li><i>NULL</i>: This value indicates that the adress has no type or kind, in
	 *			general, when adding elements, this case applies to default elements.
	 *		<li><i>other</i>: All other types will be interpreted as the kind or type of
	 *			the address.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value <i>before</i> it was eventually modified.
	 *		<li><i>FALSE</i>: Return the value <i>after</i> it was eventually modified.
	 *	 </ul>
	 * </ul>
	 *
	 * @param mixed					$theValue			Mailing address or operation.
	 * @param mixed					$theType			Mailing address kind or index.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 */
	public function Mail( $theValue = NULL, $theType = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageTypedOffset( $this,
											  kOFFSET_MAIL, kTAG_DATA,
											  kTAG_KIND, $theType, $theValue,
											  $getOld );							// ==>

	} // Mail.

	 
	/*===================================================================================
	 *	Phone																			*
	 *==================================================================================*/

	/**
	 * Manage telephone numbers.
	 *
	 * This method can be used to manage the contact {@link kOFFSET_PHONE telephone}
	 * numbers, it manages an array of strings with the following offsets:
	 *
	 * <ul>
	 *	<li><i>{@link kTAG_KIND kTAG_KIND}</i>: The telephone number kind, this could be
	 *		<i>home</i>, <i>work</i> or <i>other</i>. This element represents the array
	 *		key, although technically it is implemented as an element to allow searching on
	 *		all values.
	 *	<li><i>{@link kTAG_DATA kTAG_DATA}</i>: The telephone number string, this element
	 *		should hold the actual phone number.
	 * </ul>
	 *
	 * The parameters to this method are:
	 *
	 * <ul>
	 *	<li><b>$theValue</b>: The value or operation:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the current value selected by the second parameter.
	 *		<li><i>FALSE</i>: Delete the value selected by the second parameter.
	 *		<li><i>other</i>: Set value selected by the second parameter.
	 *	 </ul>
	 *	<li><b>$theType</b>: The element type, kind or index:
	 *	 <ul>
	 *		<li><i>NULL</i>: This value indicates that the phone has no type or kind, in
	 *			general, when adding elements, this case applies to default elements.
	 *		<li><i>other</i>: All other types will be interpreted as the kind or type of
	 *			the phone number.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value <i>before</i> it was eventually modified.
	 *		<li><i>FALSE</i>: Return the value <i>after</i> it was eventually modified.
	 *	 </ul>
	 * </ul>
	 *
	 * @param string				$theValue			Telephone number or operation.
	 * @param mixed					$theType			Mailing address kind or index.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 */
	public function Phone( $theValue = NULL, $theType = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageTypedOffset( $this,
											  kOFFSET_PHONE, kTAG_DATA,
											  kTAG_KIND, $theType, $theValue,
											  $getOld );							// ==>

	} // Phone.

	 
	/*===================================================================================
	 *	Fax																				*
	 *==================================================================================*/

	/**
	 * Manage telefax numbers.
	 *
	 * This method can be used to manage the contact {@link kOFFSET_FAX telephone}
	 * numbers, it manages an array of strings with the following offsets:
	 *
	 * <ul>
	 *	<li><i>{@link kTAG_KIND kTAG_KIND}</i>: The telephone number kind, this could be
	 *		<i>home</i>, <i>work</i> or <i>other</i>. This element represents the array
	 *		key, although technically it is implemented as an element to allow searching on
	 *		all values.
	 *	<li><i>{@link kTAG_DATA kTAG_DATA}</i>: The telephone number string, this element
	 *		should hold the actual phone number.
	 * </ul>
	 *
	 * The parameters to this method are:
	 *
	 * <ul>
	 *	<li><b>$theValue</b>: The value or operation:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the current value selected by the second parameter.
	 *		<li><i>FALSE</i>: Delete the value selected by the second parameter.
	 *		<li><i>other</i>: Set value selected by the second parameter.
	 *	 </ul>
	 *	<li><b>$theType</b>: The element type, kind or index:
	 *	 <ul>
	 *		<li><i>NULL</i>: This value indicates that the phone has no type or kind, in
	 *			general, when adding elements, this case applies to default elements.
	 *		<li><i>other</i>: All other types will be interpreted as the kind or type of
	 *			the phone number.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value <i>before</i> it was eventually modified.
	 *		<li><i>FALSE</i>: Return the value <i>after</i> it was eventually modified.
	 *	 </ul>
	 * </ul>
	 *
	 * @param string				$theValue			Telephone number or operation.
	 * @param mixed					$theType			Mailing address kind or index.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 */
	public function Fax( $theValue = NULL, $theType = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageTypedOffset( $this,
											  kOFFSET_FAX, kTAG_DATA,
											  kTAG_KIND, $theType, $theValue,
											  $getOld );							// ==>

	} // Fax.

	 

} // class CContact.


?>
