<?php

/**
 * <i>CWarehouseWrapperClient</i> class definition.
 *
 * This file contains the class definition of <b>CWarehouseWrapperClient</b> which overloads
 * its {@link CMongoDataWrapperClient ancestor} to implement a warehouse data store wrapper
 * client.
 *
 *	@package	MyWrapper
 *	@subpackage	Wrappers
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 10/04/2012
 */

/*=======================================================================================
 *																						*
 *								CWarehouseWrapperClient.php								*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CMongoDataWrapperClient.php" );

/**
 * Server definitions.
 *
 * This include file contains all definitions of the server object.
 */
require_once( kPATH_LIBRARY_SOURCE."CWarehouseWrapper.php" );

/**
 *	Warehouse data wrapper client.
 *
 * This class represents a germplasm warehouse web-services data wrapper client, it
 * facilitates the job of requesting information from servers derived from the
 * {@link CWarehouseWrapper CWarehouseWrapper} class.
 *
 * This class adds the following properties to its {@link CWrapperClient ancestor}:
 *
 * <ul>
 *	<li><i>User {@link UserCode() code}</i>: This {@link kAPI_OPT_USER_CODE property}
 *		represents the user code provided to the {@link kAPI_OP_LOGIN login} operation.
 *	<li><i>User {@link UserPass() password}</i>: This {@link kAPI_OPT_USER_PASS property}
 *		represents the user password provided to the {@link kAPI_OP_LOGIN login} operation.
 * </ul>
 *
 * The class also adds the following new operations:
 *
 * <ul>
 *	<li><i>{@link kAPI_OP_LOGIN kAPI_OP_LOGIN}</i>: This is the tag that represents the
 *		login operation, it will return the matching user {@link CUser object} if the
 *		provided user {@link kAPI_OPT_USER_CODE code} and
 *		{@link kAPI_OPT_USER_PASS password} match. 
 * </ul>
 *
 *	@package	MyWrapper
 *	@subpackage	Wrappers
 */
class CWarehouseWrapperClient extends CDataWrapperClient
{
		

/*=======================================================================================
 *																						*
 *								PUBLIC MEMBER INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Operation																		*
	 *==================================================================================*/

	/**
	 * Manage operation.
	 *
	 * We {@link CDataWrapperClient::Operation() overload} this method to add the following
	 * allowed operations:
	 *
	 * <ul>
	 *	<li><i>{@link kAPI_OP_LOGIN kAPI_OP_LOGIN}</i>: This is the tag that represents
	 *		the user login operation, it will return the {@link CUser user} matching the
	 *		provided user {@link UserCode() code} and {@link UserPass() password}.
	 * </ul>
	 *
	 * @param string				$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @throws {@link CException CException}
	 *
	 * @uses CAttribute::ManageOffset()
	 *
	 * @see kAPI_OPERATION
	 * @see kAPI_OP_GET_ONE kAPI_OP_GET_OBJECT_REF
	 */
	public function Operation( $theValue = NULL, $getOld = FALSE )
	{
		//
		// Check operation.
		//
		if( ($theValue !== NULL)
		 && ($theValue !== FALSE) )
		{
			switch( $theValue )
			{
				case kAPI_OP_LOGIN:
				case kAPI_OP_GET_TERMS:
				case kAPI_OP_MATCH_TERMS:
				case kAPI_OP_GET_NODES:
				case kAPI_OP_GET_EDGES:
				case kAPI_OP_GET_RELS:
				case kAPI_OP_GET_ROOTS:
				case kAPI_OP_SET_TAGS:
					break;
				
				default:
					return parent::Operation( $theValue, $getOld );					// ==>
			}
		}
		
		return CAttribute::ManageOffset
				( $this, kAPI_OPERATION, $theValue, $getOld );						// ==>

	} // Operation.

	 
	/*===================================================================================
	 *	UserCode																		*
	 *==================================================================================*/

	/**
	 * Manage user code.
	 *
	 * This method can be used to manage the user {@link kAPI_OPT_USER_CODE code}, it
	 * accepts a string which represents either the user code, or the requested operation:
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
	 * @param integer				$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses CAttribute::ManageOffset()
	 *
	 * @see kAPI_OPT_USER_CODE
	 */
	public function UserCode( $theValue = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageOffset
				( $this, kAPI_OPT_USER_CODE, $theValue, $getOld );					// ==>

	} // UserCode.

	 
	/*===================================================================================
	 *	UserPass																		*
	 *==================================================================================*/

	/**
	 * Manage user password.
	 *
	 * This method can be used to manage the user {@link kAPI_OPT_USER_PASS password}, it
	 * accepts a string which represents either the user password, or the requested
	 * operation:
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
	 * @param integer				$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses CAttribute::ManageOffset()
	 *
	 * @see kAPI_OPT_USER_PASS
	 */
	public function UserPass( $theValue = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageOffset
				( $this, kAPI_OPT_USER_PASS, $theValue, $getOld );					// ==>

	} // UserPass.

	 
	/*===================================================================================
	 *	Identifiers																		*
	 *==================================================================================*/

	/**
	 * Manage identifiers list.
	 *
	 * This method can be used to manage the {@link kAPI_OPT_IDENTIFIERS identifiers}, it
	 * uses the standard accessor {@link CAttribute::ManageArrayOffset() method} to manage
	 * the list of identifiers.
	 *
	 * For a more thorough reference of how this method works, please consult the
	 * {@link CAttribute::ManageArrayOffset() CAttribute::ManageArrayOffset} method, in
	 * which the second parameter will be the constant
	 * {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS}.
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
	 * @see kAPI_OPT_IDENTIFIERS
	 */
	public function Identifiers( $theValue = NULL, $theOperation = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageArrayOffset
					( $this, kAPI_OPT_IDENTIFIERS,
					  $theValue, $theOperation, $getOld );							// ==>

	} // Identifiers.

	 
	/*===================================================================================
	 *	Predicates																		*
	 *==================================================================================*/

	/**
	 * Manage predicates list.
	 *
	 * This method can be used to manage the {@link kAPI_OPT_PREDICATES predicates}, it
	 * uses the standard accessor {@link CAttribute::ManageArrayOffset() method} to manage
	 * the list of predicates.
	 *
	 * For a more thorough reference of how this method works, please consult the
	 * {@link CAttribute::ManageArrayOffset() CAttribute::ManageArrayOffset} method, in
	 * which the second parameter will be the constant
	 * {@link kAPI_OPT_PREDICATES kAPI_OPT_PREDICATES}.
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
	 * @see kAPI_OPT_PREDICATES
	 */
	public function Predicates( $theValue = NULL, $theOperation = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageArrayOffset
					( $this, kAPI_OPT_PREDICATES,
					  $theValue, $theOperation, $getOld );							// ==>

	} // Predicates.

	 
	/*===================================================================================
	 *	Direction																		*
	 *==================================================================================*/

	/**
	 * Manage edges direction.
	 *
	 * This method can be used to manage the {@link kAPI_OP_GET_RELS edges} direction, it
	 * accepts a string which represents either the relationship
	 * {@link kAPI_OPT_DIRECTION direction}, or the requested operation:
	 *
	 * <ul>
	 *	<li><i>NULL</i>: Return the current value.
	 *	<li><i>FALSE</i>: Delete the current value.
	 *	<li><i>other</i>: Set the value with the provided parameter:
	 *	 <ul>
	 *		<li><i>{@link kAPI_DIRECTION_IN kAPI_DIRECTION_IN}</i>: Incoming relationships.
	 *		<li><i>{@link kAPI_DIRECTION_OUT kAPI_DIRECTION_OUT}</i>: Outgoing
	 *			relationships.
	 *		<li><i>{@link kAPI_DIRECTION_ALL kAPI_DIRECTION_ALL}</i>: Both incoming and
	 *			outgoing relationships.
	 *	 </ul>
	 * </ul>
	 *
	 * The second parameter is a boolean which if <i>TRUE</i> will return the <i>old</i>
	 * value when replacing values; if <i>FALSE</i>, it will return the currently set value.
	 *
	 * @param integer				$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses CAttribute::ManageOffset()
	 *
	 * @see kAPI_OPT_DIRECTION
	 */
	public function Direction( $theValue = NULL, $getOld = FALSE )
	{
		//
		// Check direction parameter.
		//
		if( ($theValue !== NULL)
		 && ($theValue !== FALSE) )
		{
			switch( $theValue )
			{
				case kAPI_DIRECTION_IN:
				case kAPI_DIRECTION_OUT:
				case kAPI_DIRECTION_ALL:
					break;
				
				default:
					throw new CException
						( "Unsupported direction option",
						  kERROR_UNSUPPORTED,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Direction' => $theValue ) );					// !@! ==>
			}
		}
		
		return CAttribute::ManageOffset
				( $this, kAPI_OPT_DIRECTION, $theValue, $getOld );					// ==>

	} // Direction.

	 
	/*===================================================================================
	 *	Levels																			*
	 *==================================================================================*/

	/**
	 * Manage traversal depts level.
	 *
	 * This method can be used to manage the {@link kAPI_OPT_LEVELS levels} of depth in
	 * graph {@link kAPI_OP_GET_RELS traversals}, it accepts an integer which represents
	 * the number of levels to traverse; if the value is negative, all levels will be
	 * traversed.
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
	 * @param integer				$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses CAttribute::ManageOffset()
	 *
	 * @see kAPI_OPT_LEVELS
	 */
	public function Levels( $theValue = NULL, $getOld = FALSE )
	{
		//
		// Check level parameter.
		//
		if( ($theValue !== NULL)
		 && ($theValue !== FALSE) )
			$theValue = (integer) $theValue;
		
		return CAttribute::ManageOffset
				( $this, kAPI_OPT_LEVELS, $theValue, $getOld );						// ==>

	} // Levels.

	 

} // class CWarehouseWrapperClient.


?>
