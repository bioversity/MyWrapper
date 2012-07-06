<?php

/**
 * <i>TDataset</i> trait definition.
 *
 * This file contains the trait definition of <b>TDataset</b> which implements the
 * {@link Dataset() Dataset} trait.
 *
 *	@package	MyWrapper
 *	@subpackage	Traits
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 06/07/2012
*/

/*=======================================================================================
 *																						*
 *									TDataset.php										*
 *																						*
 *======================================================================================*/

/**
 * Attributes.
 *
 * This include file contains the attribute class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CAttribute.php" );

/**
 *	Dataset trait.
 *
 * This list of traits groups dataset related traits.
 *
 *	@package	MyWrapper
 *	@subpackage	Traits
 */
trait TDataset
{
		

/*=======================================================================================
 *																						*
 *								PUBLIC MEMBER INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Dataset																			*
	 *==================================================================================*/

	/**
	 * Manage dataset.
	 *
	 * This method can be used to manage the file's {@link CDataset dataset}
	 * {@link kTAG_LID reference} or the operation to be performed:
	 *
	 * <ul>
	 *	<li><i>NULL</i>: Return the current value.
	 *	<li><i>FALSE</i>: Delete the current value.
	 *	<li><i>other</i>: Set the value with the provided parameter.
	 * </ul>
	 *
	 * The second parameter is a boolean which if <i>TRUE</i> will return the <i>old</i>
	 * value when replacing values; if <i>FALSE</i>, it will return the currently set value.
	 *
	 * @param string				$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses CAttribute::ManageOffset()
	 *
	 * @see kTAG_DATASET
	 */
	public function Dataset( $theValue = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageOffset
				( $this, kTAG_DATASET, $theValue, $getOld );						// ==>

	} // Dataset.

	 

} // trait TDataset.


?>
