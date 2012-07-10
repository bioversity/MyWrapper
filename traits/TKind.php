<?php

/**
 * <i>TKind</i> trait definition.
 *
 * This file contains the trait definition of <b>TKind</b> which implements the
 * {@link Kind() Kind} trait.
 *
 *	@package	MyWrapper
 *	@subpackage	Traits
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 10/07/2012
*/

/*=======================================================================================
 *																						*
 *										TKind.php										*
 *																						*
 *======================================================================================*/

/**
 * Attributes.
 *
 * This include file contains the attribute class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CAttribute.php" );

/**
 *	Kind trait.
 *
 * This trait implements a method that manages the current object's kind attribute, it is
 * a list of elements each of which should represent a specific kind, type or function
 * associated with the current object.
 *
 *	@package	MyWrapper
 *	@subpackage	Traits
 */
trait TKind
{
		

/*=======================================================================================
 *																						*
 *								PUBLIC MEMBER INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Kind																			*
	 *==================================================================================*/

	/**
	 * Manage the kind.
	 *
	 * This method can be used to handle the object's list of {@link kTAG_KIND kinds}, it
	 * uses the standard accessor {@link CAttribute::ManageArrayOffset() method} to manage
	 * the list of kind, type or function tags associated with the file.
	 *
	 * Each element of this list should indicate a specific kind or type of the object or a
	 * specific function that the object has.
	 *
	 * For a more thorough reference of how this method works, please consult the
	 * {@link CAttribute::ManageArrayOffset() CAttribute::ManageArrayOffset} method, in
	 * which the second parameter will be the constant {@link kTAG_KIND kTAG_KIND}.
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
	 * @see kTAG_KIND
	 */
	public function Kind( $theValue = NULL, $theOperation = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageArrayOffset
					( $this, kTAG_KIND, $theValue, $theOperation, $getOld );		// ==>

	} // Kind.

	 

} // trait TKind.


?>
