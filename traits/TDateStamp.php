<?php

/**
 * <i>TDateStamp</i> trait definition.
 *
 * This file contains the trait definition of <b>TDateStamp</b> which implements the
 * {@link Created() Created} and {@link Modified() Modified} traits.
 *
 *	@package	MyWrapper
 *	@subpackage	Traits
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 06/07/2012
*/

/*=======================================================================================
 *																						*
 *									TDateStamp.php										*
 *																						*
 *======================================================================================*/

/**
 * Attributes.
 *
 * This include file contains the attribute class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CAttribute.php" );

/**
 *	Time-stamp trait.
 *
 * This trait implements two methods that can be used to record the dates of an object.
 * The {@link Created() creation} date records when the object was generated or created, in
 * general this value does not change; the {@link Modified() modification} date records the
 * last time the object has been modified.
 *
 *	@package	MyWrapper
 *	@subpackage	Traits
 */
trait TDateStamp
{
		

/*=======================================================================================
 *																						*
 *								PUBLIC MEMBER INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Created																			*
	 *==================================================================================*/

	/**
	 * Manage object creation time stamp.
	 *
	 * This method can be used to manage the object {@link kTAG_CREATED creation}
	 * time-stamp, it uses the standard accessor {@link CAttribute::ManageOffset() method}
	 * to manage the {@link kTAG_MODIFIED offset}:
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
	 * @param NULL|FALSE|string		$theValue			Object creation date.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses CAttribute::ManageOffset()
	 *
	 * @see kTAG_CREATED
	 */
	public function Created( $theValue = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageOffset( $this, kTAG_CREATED, $theValue, $getOld );	// ==>

	} // Created.

	 
	/*===================================================================================
	 *	Modified																		*
	 *==================================================================================*/

	/**
	 * Manage object last modification time stamp.
	 *
	 * This method can be used to manage the object last {@link kTAG_MODIFIED modification}
	 * time-stamp, or the date in which the last modification was made on the object, it
	 * uses the standard accessor {@link CAttribute::ManageOffset() method} to manage the
	 * {@link kTAG_MODIFIED offset}:
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
	 * @param NULL|FALSE|string		$theValue			Object last modification date.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses CAttribute::ManageOffset()
	 *
	 * @see kTAG_MODIFIED
	 */
	public function Modified( $theValue = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageOffset
					( $this, kTAG_MODIFIED, $theValue, $getOld );					// ==>

	} // Modified.

	 

} // trait TDateStamp.


?>
