<?php

/**
 * <i>CSession</i> class definition.
 *
 * This file contains the class definition of <b>CSession</b> which wraps this class
 * {@link CArrayObject ancestor} around a session.
 *
 *	@package	MyWrapper
 *	@subpackage	Site
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 12/07/2012
*/

/*=======================================================================================
 *																						*
 *									CSession.php										*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CArrayObject.php" );

/**
 * Attributes.
 *
 * This include file contains the attributes class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CAttribute.php" );

/**
 * Wrapper definitions.
 *
 * This include file contains the wrapper class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CWarehouseWrapper.inc.php" );

/**
 * Local definitions.
 *
 * This include file contains all local definitions to this class.
 */
require_once( kPATH_LIBRARY_SOURCE."CSession.inc.php" );

/**
 *	Session object.
 *
 * This class implements a session object, it wraps the default PHP session array into a
 * class which is stored in a {@link kTAG_SESSION default} session offset.
 * 
 * The idea is to derive from this class and include the custom libraries.
 *
 *	@package	MyWrapper
 *	@subpackage	Site
 */
class CSession
{
		

/*=======================================================================================
 *																						*
 *										MAGIC											*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	__construct																		*
	 *==================================================================================*/

	/**
	 * Instantiate class.
	 *
	 * The constructor will instantiate an object either from an array, by loading all
	 * corresponding properties, or as an empty object.
	 *
	 * @param mixed					$theData			File structure.
	 *
	 * @access public
	 */
	public function __construct( $theData = NULL )
	{
		//
		// Empty statement.
		//
		if( $theData === NULL )
			parent::__construct();
		
		//
		// Handle provided statement.
		//
		elseif( is_array( $theData )
			 || ($theData instanceof ArrayObject) )
			parent::__construct( (array) $theData );

	} // Constructor.

		

/*=======================================================================================
 *																						*
 *								PUBLIC MEMBER INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	User																			*
	 *==================================================================================*/

	/**
	 * Manage the session user.
	 *
	 * This method can be used to manage the session's {@link CUser user}, the provided
	 * parameter represents either the new user or the operation to be performed:
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
	 * You should provide an object reference or the {@link kTAG_LID identifier} of the
	 * owner object.
	 *
	 * @param string				$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses CAttribute::ManageOffset()
	 *
	 * @see kSESSION_USER
	 */
	public function User( $theValue = NULL, $getOld = FALSE )
	{
		//
		// Check identifier.
		//
		if( ($theValue !== NULL)
		 && ($theValue !== FALSE) )
			$theValue = CPersistentUnitObject::NormaliseRelatedObject( $theValue );
		
		return CAttribute::ManageOffset
				( $this, kSESSION_USER, $theValue, $getOld );						// ==>

	} // User.

		

/*=======================================================================================
 *																						*
 *								PUBLIC OPERATIONS INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Login																			*
	 *==================================================================================*/

	/**
	 * Login user.
	 *
	 * This method will check whether there is a login request, in that case it will check
	 * if the provided credentials are correct and {@link User() set} the
	 * {@link kSESSION_USER user} in the current session.
	 *
	 * The method will return:
	 *
	 * <ul>
	 *	<li><i>NULL</i>: No user login request intercepted.
	 *	<li><i>FALSE</i>: User credentials not correct.
	 *	<li><i>other</i>: The user identifier.
	 * </ul>
	 *
	 * The method does not accept parameters: these will come from the request.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses CAttribute::ManageOffset()
	 *
	 * @see kSESSION_USER
	 */
	public function Login()
	{
		//
		// Check login command.
		//
		if( isset( $_REQUEST )
		 && array_key_exists( kAPI_OPERATION, $_REQUEST )
		 && isset( $_REQUEST[ kAPI_OPT_USER_CODE ] )
		 && isset( $_REQUEST[ kAPI_OPT_USER_PASS ] )
		 && ($_REQUEST[ kAPI_OPERATION ] == kAPI_OP_LOGIN) )
		{
		
		} // All required elements are there.
		
		return NULL;																// ==>

	} // Login.

	 

} // class CSession.


?>
