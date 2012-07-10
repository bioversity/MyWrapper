<?php

/**
 * <i>TState</i> trait definition.
 *
 * This file contains the trait definition of <b>TState</b> which implements the
 * {@link State() State} trait.
 *
 *	@package	MyWrapper
 *	@subpackage	Traits
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 10/07/2012
*/

/*=======================================================================================
 *																						*
 *										TState.php										*
 *																						*
 *======================================================================================*/

/**
 * Attributes.
 *
 * This include file contains the attribute class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CAttribute.php" );

/**
 *	State trait.
 *
 * This trait implements a method that manages the current object's state attribute, it is
 * a list of elements each of which should represent a specific state or status.
 *
 *	@package	MyWrapper
 *	@subpackage	Traits
 */
trait TState
{
		

/*=======================================================================================
 *																						*
 *								PUBLIC MEMBER INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	State																			*
	 *==================================================================================*/

	/**
	 * Manage the state.
	 *
	 * This method can be used to handle the object's list of {@link kTAG_STATE states}, it
	 * uses the standard accessor {@link CAttribute::ManageArrayOffset() method} to manage
	 * the list of tags associated with the various states of the current object.
	 *
	 * Each element of this list should indicate an object state, status or quality. This
	 * information indicates in which state the object is in.
	 *
	 * For a more thorough reference of how this method works, please consult the
	 * {@link CAttribute::ManageArrayOffset() CAttribute::ManageArrayOffset} method, in
	 * which the second parameter will be the constant {@link kTAG_STATE kTAG_STATE}.
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
	 * @see kTAG_STATE
	 */
	public function State( $theValue = NULL, $theOperation = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageArrayOffset
					( $this, kTAG_STATE, $theValue, $theOperation, $getOld );		// ==>

	} // State.

	 

} // trait TState.


?>
