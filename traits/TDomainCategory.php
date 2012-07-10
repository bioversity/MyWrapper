<?php

/**
 * <i>TDomainCategory</i> trait definition.
 *
 * This file contains the trait definition of <b>TDomainCategory</b> which implements the
 * {@link Domain() Domain} and {@link Category() Category} traits.
 *
 *	@package	MyWrapper
 *	@subpackage	Traits
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 06/07/2012
*/

/*=======================================================================================
 *																						*
 *									TDomainCategory.php									*
 *																						*
 *======================================================================================*/

/**
 * Attributes.
 *
 * This include file contains the attribute class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CAttribute.php" );

/**
 *	Category trait.
 *
 * This trait implements two methods that can be used to record the category of an object.
 * The {@link Domain() domain} lists the domains to which the object belongs, the
 * {@link Category() category} lists the categories to which the object falls into.
 *
 *	@package	MyWrapper
 *	@subpackage	Traits
 */
trait TDomainCategory
{
		

/*=======================================================================================
 *																						*
 *								PUBLIC MEMBER INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Domain																			*
	 *==================================================================================*/

	/**
	 * Manage domains.
	 *
	 * This method can be used to handle the object's {@link kTAG_DOMAIN domains}, it uses
	 * the standard accessor {@link CAttribute::ManageArrayOffset() method} to manage the
	 * list of domains.
	 *
	 * Each element of this list should indicate a domain to which the current object
	 * belongs to.
	 *
	 * For a more thorough reference of how this method works, please consult the
	 * {@link CAttribute::ManageArrayOffset() CAttribute::ManageArrayOffset} method, in
	 * which the second parameter will be the constant {@link kTAG_CATEGORY kTAG_CATEGORY}.
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
	 * @see kTAG_DOMAIN
	 */
	public function Domain( $theValue = NULL, $theOperation = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageArrayOffset
					( $this, kTAG_DOMAIN, $theValue, $theOperation, $getOld );		// ==>

	} // Domain.

	 
	/*===================================================================================
	 *	Category																		*
	 *==================================================================================*/

	/**
	 * Manage categories.
	 *
	 * This method can be used to handle the object's {@link kTAG_CATEGORY categories}, it
	 * uses the standard accessor {@link CAttribute::ManageArrayOffset() method} to manage
	 * the list of categories.
	 *
	 * Each element of this list should indicate a category to which the current object
	 * belongs to.
	 *
	 * For a more thorough reference of how this method works, please consult the
	 * {@link CAttribute::ManageArrayOffset() CAttribute::ManageArrayOffset} method, in
	 * which the second parameter will be the constant {@link kTAG_CATEGORY kTAG_CATEGORY}.
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
	 * @see kTAG_CATEGORY
	 */
	public function Category( $theValue = NULL, $theOperation = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageArrayOffset
					( $this, kTAG_CATEGORY, $theValue, $theOperation, $getOld );	// ==>

	} // Category.

	 

} // trait TDomainCategory.


?>
