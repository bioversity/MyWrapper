<?php

/**
 * <i>CMongoDataWrapperClient</i> class definition.
 *
 * This file contains the class definition of <b>CMongoDataWrapperClient</b> which overloads
 * its {@link CDataWrapperClient ancestor} to implement a MongoDB data store wrapper client.
 *
 *	@package	MyWrapper
 *	@subpackage	Wrappers
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 01/04/2012
 */

/*=======================================================================================
 *																						*
 *								CMongoDataWrapperClient.php								*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CDataWrapperClient.php" );

/**
 * Server definitions.
 *
 * This include file contains all definitions of the server object.
 */
require_once( kPATH_LIBRARY_SOURCE."CMongoDataWrapper.php" );

/**
 *	MongoDB data wrapper client.
 *
 * This class represents a MongoDB web-services data wrapper client, it facilitates the job
 * of requesting information from servers derived from the
 * {@link CMongoDataWrapper CMongoDataWrapper} class.
 *
 * This class adds the following properties to its {@link CWrapperClient ancestor}:
 *
 * <ul>
 *	<li><i>{@link NoResponse() No} response</i>: This {@link kAPI_OPT_NO_RESP property}
 *		represents a switch that, if on, prevents the response from being sent. This can be
 *		useful when you are only interested in the status of the operation and not in the
 *		response.
 * </ul>
 *
 * The class also adds the following new operations:
 *
 * <ul>
 *	<li><i>{@link kAPI_OP_GET_ONE kAPI_OP_GET_ONE}</i>: This is the tag that represents the
 *		findOne Mongo operation, it will return the first matched object.
 *	<li><i>{@link kAPI_OP_GET_OBJECT_REF kAPI_OP_GET_OBJECT_REF}</i>: This tag defines a web
 *		service that returns an object by reference. It is equivalent to the
 *		{@link kAPI_OP_GET_ONE kAPI_OP_GET_ONE} operation, except that instead of using the
 *		query provided in the {@link kAPI_DATA_QUERY kAPI_DATA_QUERY} parameter, it will try
 *		to extract an identifier from the object provided in the
 *		{@link kAPI_DATA_OBJECT kAPI_DATA_OBJECT} parameter.
 * </ul>
 *
 *	@package	MyWrapper
 *	@subpackage	Wrappers
 */
class CMongoDataWrapperClient extends CDataWrapperClient
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
	 *	<li><i>{@link kAPI_OP_GET_ONE kAPI_OP_GET_ONE}</i>: This is the tag that represents
	 *		the findOne Mongo operation, it will return the first matched object.
	 *	<li><i>{@link kAPI_OP_GET_OBJECT_REF kAPI_OP_GET_OBJECT_REF}</i>: This tag defines a
	 *		web-service that returns an object by reference. It is equivalent to the
	 *		{@link kAPI_OP_GET_ONE kAPI_OP_GET_ONE} operation, except that instead of using
	 *		the query provided in the {@link kAPI_DATA_QUERY kAPI_DATA_QUERY} parameter, it
	 *		will try to extract an identifier from the object provided in the
	 *		{@link kAPI_DATA_OBJECT kAPI_DATA_OBJECT} parameter.
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
				case kAPI_OP_GET_ONE:
				case kAPI_OP_GET_OBJECT_REF:
					break;
				
				default:
					return parent::Operation( $theValue, $getOld );					// ==>
			}
		}
		
		return $this->_ManageOffset( kAPI_OPERATION, $theValue, $getOld );			// ==>

	} // Operation.

	 
	/*===================================================================================
	 *	NoResponse																		*
	 *==================================================================================*/

	/**
	 * Manage no response switch.
	 *
	 * This method can be used to manage the no {@link kAPI_OPT_NO_RESP response} switch, it
	 * accepts a boolean which represents either the on/off value, or the requested
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
	 * @see kAPI_OPT_NO_RESP
	 */
	public function NoResponse( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kAPI_OPT_NO_RESP, $theValue, $getOld );		// ==>

	} // NoResponse.

	 

} // class CMongoDataWrapperClient.


?>
