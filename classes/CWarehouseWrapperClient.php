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
	 * @uses _ManageOffset()
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
				case kAPI_OP_GET_NODES:
				case kAPI_OP_GET_EDGES:
				case kAPI_OP_QUERY_ROOTS:
					break;
				
				default:
					return parent::Operation( $theValue, $getOld );					// ==>
			}
		}
		
		return $this->_ManageOffset( kAPI_OPERATION, $theValue, $getOld );			// ==>

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
	 * @uses _ManageOffset()
	 *
	 * @see kAPI_OPT_USER_CODE
	 */
	public function UserCode( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kAPI_OPT_USER_CODE, $theValue, $getOld );		// ==>

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
	 * @uses _ManageOffset()
	 *
	 * @see kAPI_OPT_USER_PASS
	 */
	public function UserPass( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kAPI_OPT_USER_PASS, $theValue, $getOld );		// ==>

	} // UserPass.

	 
	/*===================================================================================
	 *	Identifiers																		*
	 *==================================================================================*/

	/**
	 * Manage identifiers list.
	 *
	 * This method can be used to manage the {@link kAPI_OPT_IDENTIFIERS identifiers}, it
	 * uses the standard accessor {@link _ManageArrayOffset() method} to manage the list of
	 * identifiers.
	 *
	 * For a more thorough reference of how this method works, please consult the
	 * {@link _ManageArrayOffset() _ManageArrayOffset} method, in which the first parameter
	 * will be the constant {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS}.
	 *
	 * @param mixed					$theValue			Value or index.
	 * @param mixed					$theOperation		Operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses _ManageOffset()
	 *
	 * @see kAPI_OPT_IDENTIFIERS
	 */
	public function Identifiers( $theValue = NULL, $theOperation = NULL, $getOld = FALSE )
	{
		return $this->_ManageArrayOffset
					( kAPI_OPT_IDENTIFIERS, $theValue, $theOperation, $getOld );	// ==>

	} // Identifiers.

	 
	/*===================================================================================
	 *	Predicates																		*
	 *==================================================================================*/

	/**
	 * Manage predicates list.
	 *
	 * This method can be used to manage the {@link kAPI_OPT_PREDICATES predicates}, it
	 * uses the standard accessor {@link _ManageArrayOffset() method} to manage the list of
	 * predicates.
	 *
	 * For a more thorough reference of how this method works, please consult the
	 * {@link _ManageArrayOffset() _ManageArrayOffset} method, in which the first parameter
	 * will be the constant {@link kAPI_OPT_PREDICATES kAPI_OPT_PREDICATES}.
	 *
	 * @param mixed					$theValue			Value or index.
	 * @param mixed					$theOperation		Operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses _ManageOffset()
	 *
	 * @see kAPI_OPT_PREDICATES
	 */
	public function Predicates( $theValue = NULL, $theOperation = NULL, $getOld = FALSE )
	{
		return $this->_ManageArrayOffset
					( kAPI_OPT_PREDICATES, $theValue, $theOperation, $getOld );		// ==>

	} // Predicates.

	 
	/*===================================================================================
	 *	Attributes																		*
	 *==================================================================================*/

	/**
	 * Manage selectors list.
	 *
	 * This method can be used to manage the {@link kAPI_OPT_ATTRIBUTES attribute}
	 * selectors, it manages the following array structure:
	 *
	 * <ul>
	 *	<li><i>Key</i>: The array key should correspond to the node attribute you want to
	 *		match:
	 *	 <ul>
	 *		<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>: The ontology node type.
	 *		<li><i>{@link kTAG_KIND kTAG_KIND}</i>: The ontology node kind.
	 *		<li><i>{@link kTAG_DOMAIN kTAG_DOMAIN}</i>: The domain of the ontology.
	 *		<li><i>{@link kTAG_CATEGORY kTAG_CATEGORY}</i>: The category of the ontology.
	 *	 </ul>
	 *	<li><i>Value</i>: An array of values to be matched.
	 * </ul>
	 *
	 * The elements of these key/value pairs will be compared in <i>AND</i>.
	 *
	 * The parameters to this method are:
	 *
	 * <ul>
	 *	<li><b>$theKey</b>: The attribute key.
	 *	<li><b>$theValue</b>: The attribute value:
	 *	 <ul>
	 *		<li><i>NULL</i>: Apply operation to all values.
	 *		<li><i>other</i>: The attribute value.
	 *	 </ul>
	 *	<li><b>$theOperation</b>: The operation:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the attribute value matched by the first and second
	 *			parameters, or return all values matching the first parameter if the second
	 *			is <i>NULL</i>.
	 *		<li><i>FALSE</i>: Delete the attribute value matched by the first and second
	 *			parameters, or delete all values matching the first parameter if the second
	 *			is <i>NULL</i>.
	 *		<li><i>TRUE</i>: Add the value in the second parameter to the attributes
	 *			matching the first parameter, or replace all values matching the first
	 *			parameter with the value in the second parameter.
	 *	 </ul>
	 * </ul>
	 *
	 * @param mixed					$theKey				Attribute key.
	 * @param mixed					$theValue			Value or index.
	 * @param mixed					$theOperation		Operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @see kAPI_OPT_ATTRIBUTES
	 */
	public function Attributes( $theKey, $theValue = NULL,
										$theOperation = NULL,
										$getOld = FALSE )
	{
		//
		// Normalise parameters.
		//
		$theKey = (string) $theKey;
		
		//
		// Save current values.
		//
		$save = NULL;
		$list = Array();
		$attribute = $this->offsetGet( kAPI_OPT_ATTRIBUTES );
		if( $attribute !== NULL )
		{
			if( array_key_exists( $theKey, $attribute ) )
			{
				$list = $attribute[ $theKey ];
				if( (! is_array( $theValue ))
				 && ($theValue !== NULL) )
					$save = ( in_array( $theValue, $list ) )
						  ? $theValue
						  : NULL;
			}
		}
		
		//
		// Return information.
		//
		if( $theOperation === NULL )
			return $save;															// ==>
		
		//
		// Delete information.
		//
		if( $theOperation === FALSE )
		{
			//
			// Delete attribute.
			//
			if( $theValue === NULL )
			{
				if( count( $list ) )
				{
					unset( $attribute[ $theKey ] );
					if( count( $attribute ) )
						$this->offsetSet( kAPI_OPT_ATTRIBUTES, $attribute );
					else
						$this->offsetUnset( kAPI_OPT_ATTRIBUTES );
					
					if( $getOld )
						return $list;												// ==>
				}
				
				return NULL;														// ==>
			}
			
			//
			// Delete attribute element.
			//
			if( $save !== NULL )
			{
				unset( $list[ $theValue ] );
				if( count( $list ) )
					$attribute[ $theKey ] = $list;
				else
				{
					unset( $attribute[ $theKey ] );
					if( ! count( $attribute ) )
						$this->offsetUnset( kAPI_OPT_ATTRIBUTES );
				}
				
				if( $getOld )
					return $save;													// ==>
			}
			
			return NULL;															// ==>
		}
		
		//
		// Replace attribute.
		//
		if( is_array( $theValue ) )
		{
			if( $attribute !== NULL )
				$attribute[ $theKey ] = $theValue;
			else
				$attribute = array( $theKey => $theValue );
			
			$this->offsetSet( kAPI_OPT_ATTRIBUTES, $attribute );
			
			if( $getOld )
				return $list;														// ==>
			
			return $theValue;														// ==>
		}
		
		//
		// Add attribute element.
		//
		if( $save === NULL )
		{
			if( count( $list ) )
				$list[] = $theValue;
			else
				$list = array( $theValue );
			
			if( $attribute === NULL )
				$attribute = Array();

			$attribute[ $theKey ] = $list;
			
			$this->offsetSet( kAPI_OPT_ATTRIBUTES, $attribute );
		}
		
		if( $getOld )
			return $save;															// ==>
		
		return $theValue;															// ==>

	} // Attributes.

	 
	/*===================================================================================
	 *	Direction																		*
	 *==================================================================================*/

	/**
	 * Manage edges direction.
	 *
	 * This method can be used to manage the {@link kAPI_OP_GET_EDGES edges} direction, it
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
	 * @uses _ManageOffset()
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
		
		return $this->_ManageOffset( kAPI_OPT_DIRECTION, $theValue, $getOld );		// ==>

	} // Direction.

	 

} // class CWarehouseWrapperClient.


?>
