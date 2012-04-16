<?php

/**
 * <i>CWarehouseWrapper</i> class definition.
 *
 * This file contains the class definition of <b>CWarehouseWrapper</b> which overloads its
 * {@link CMongoDataWrapper ancestor} to implement a Mongo data store wrapper for the
 * germplasm data warehouse.
 *
 *	@package	MyWrapper
 *	@subpackage	Wrappers
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 10/04/2012
 */

/*=======================================================================================
 *																						*
 *								CWarehouseWrapper.php									*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CMongoDataWrapper.php" );

/**
 * User definitions.
 *
 * This include file contains the user class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CUser.php" );

/**
 * Local definitions.
 *
 * This include file contains all local definitions to this class.
 */
require_once( kPATH_LIBRARY_SOURCE."CWarehouseWrapper.inc.php" );

/**
 *	Warehouse Mongo data wrapper.
 *
 * This class overloads its {@link CMongoDataWrapper ancestor} to implement a web-service
 * customised to the germplasm data warehouse.
 *
 * This class adds the following operations:
 *
 * <ul>
 *	<li><i>{@link kAPI_OP_LOGIN kAPI_OP_LOGIN}</i>: Login, this operation expects the user
 *		{@link kAPI_OPT_USER_CODE code} and {@link kAPI_OPT_USER_PASS password} and will
 *		return the matching user.
 * </ul>
 *
 * The class adds the following options:
 *
 * <ul>
 *	<li><i>{@link kAPI_OPT_USER_CODE kAPI_OPT_USER_CODE}</i>: User code, this option expects
 *		a string corresponding to the user {@link CEntity::Code() code}.
 *	<li><i>{@link kAPI_OPT_USER_PASS kAPI_OPT_USER_PASS}</i>: User code, this option expects
 *		a string corresponding to the user {@link CUser::Password() password}.
 * </ul>
 *
 *	@package	MyWrapper
 *	@subpackage	Wrappers
 */
class CWarehouseWrapper extends CMongoDataWrapper
{
		

/*=======================================================================================
 *																						*
 *								PROTECTED PARSING INTERFACE								*
 *																						*
 *======================================================================================*/


	
	/*===================================================================================
	 *	_ParseRequest																	*
	 *==================================================================================*/

	/**
	 * Parse request.
	 *
	 * We overload this method to parse the user {@link kAPI_OPT_USER_CODE code} and
	 * {@link kAPI_OPT_USER_PASS password} tags.
	 *
	 * @access private
	 *
	 * @uses _ParseUserCode()
	 * @uses _ParseUserPass()
	 */
	protected function _ParseRequest()
	{
		//
		// Call parent method.
		//
		parent::_ParseRequest();
		
		//
		// Handle parameters.
		//
		$this->_ParseUserCode();
		$this->_ParseUserPass();
	
	} // _ParseRequest.

		

/*=======================================================================================
 *																						*
 *								PROTECTED PARSING UTILITIES								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_ParseUserCode																	*
	 *==================================================================================*/

	/**
	 * Parse user code.
	 *
	 * This method will parse the user {@link kAPI_OPT_USER_CODE code} parameter.
	 *
	 * @access private
	 *
	 * @see kAPI_DATA_REQUEST kAPI_OPT_USER_CODE
	 */
	protected function _ParseUserCode()
	{
		//
		// Handle no response flag.
		//
		if( array_key_exists( kAPI_OPT_USER_CODE, $_REQUEST ) )
		{
			if( $this->offsetExists( kAPI_DATA_REQUEST ) )
				$this->_OffsetManage
					( kAPI_DATA_REQUEST, kAPI_OPT_USER_CODE,
					  $_REQUEST[ kAPI_OPT_USER_CODE ] );
		}
	
	} // _ParseUserCode.

	 
	/*===================================================================================
	 *	_ParseUserPass																	*
	 *==================================================================================*/

	/**
	 * Parse user password.
	 *
	 * This method will parse the user {@link kAPI_OPT_USER_PASS password} parameter.
	 *
	 * @access private
	 *
	 * @see kAPI_DATA_REQUEST kAPI_OPT_USER_PASS
	 */
	protected function _ParseUserPass()
	{
		//
		// Handle no response flag.
		//
		if( array_key_exists( kAPI_OPT_USER_PASS, $_REQUEST ) )
		{
			if( $this->offsetExists( kAPI_DATA_REQUEST ) )
				$this->_OffsetManage
					( kAPI_DATA_REQUEST, kAPI_OPT_USER_PASS,
					  $_REQUEST[ kAPI_OPT_USER_PASS ] );
		}
	
	} // _ParseUserPass.

		

/*=======================================================================================
 *																						*
 *							PROTECTED VALIDATION UTILITIES								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_ValidateOperation																*
	 *==================================================================================*/

	/**
	 * Validate request operation.
	 *
	 * This method can be used to check whether the provided
	 * {@link kAPI_OPERATION operation} parameter is valid.
	 *
	 * In this class we check that both the user {@link kAPI_OPT_USER_CODE code} and
	 * {@link kAPI_OPT_USER_PASS password} have been sent when requesting a
	 * {@link kAPI_OP_LOGIN login} operation, in addition to the
	 * {@link kAPI_DATABASE database} and {@link kAPI_CONTAINER container} references.
	 *
	 * @access protected
	 */
	protected function _ValidateOperation()
	{
		//
		// Parse operation.
		//
		switch( $parameter = $_REQUEST[ kAPI_OPERATION ] )
		{
			case kAPI_OP_LOGIN:
				
				//
				// Check for database.
				//
				if( (! array_key_exists( kAPI_DATABASE, $_REQUEST ))
				 || (! strlen( $_REQUEST[ kAPI_DATABASE ] )) )
					throw new CException
						( "Missing database reference",
						  kERROR_OPTION_MISSING,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Operation' => $parameter ) );					// !@! ==>
				
				//
				// Check for container.
				//
				if( (! array_key_exists( kAPI_CONTAINER, $_REQUEST ))
				 || (! strlen( $_REQUEST[ kAPI_CONTAINER ] )) )
					throw new CException
						( "Missing container reference",
						  kERROR_OPTION_MISSING,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Operation' => $parameter ) );					// !@! ==>

				//
				// Check for user code.
				//
				if( (! array_key_exists( kAPI_OPT_USER_CODE, $_REQUEST ))
				 || (! strlen( $_REQUEST[ kAPI_OPT_USER_CODE ] )) )
					throw new CException
						( "Missing user code",
						  kERROR_OPTION_MISSING,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Operation' => $parameter ) );					// !@! ==>
				
				//
				// Check for user password.
				//
				if( (! array_key_exists( kAPI_OPT_USER_PASS, $_REQUEST ))
				 || (! strlen( $_REQUEST[ kAPI_OPT_USER_PASS ] )) )
					throw new CException
						( "Missing user password",
						  kERROR_OPTION_MISSING,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Operation' => $parameter ) );					// !@! ==>
				
				break;
			
			//
			// Handle unknown operation.
			//
			default:
				parent::_ValidateOperation();
				break;
			
		} // Parsing parameter.
	
	} // _ValidateOperation.

		

/*=======================================================================================
 *																						*
 *								PROTECTED HANDLER INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_HandleRequest																	*
	 *==================================================================================*/

	/**
	 * Handle request.
	 *
	 * This method will handle the request.
	 *
	 * @access protected
	 */
	protected function _HandleRequest()
	{
		//
		// Parse by operation.
		//
		switch( $op = $_REQUEST[ kAPI_OPERATION ] )
		{
			case kAPI_OP_LOGIN:
				$this->_Handle_Login();
				break;

			default:
				parent::_HandleRequest();
				break;
		}
	
	} // _HandleRequest.

	 
	/*===================================================================================
	 *	_Handle_Login																	*
	 *==================================================================================*/

	/**
	 * Handle {@link kAPI_OP_LOGIN login} request.
	 *
	 * This method will first {@link CUser::__construct() load} the user by using its
	 * {@link CEntity::Code() code}, then match the {@link CUser::Password() password}; if
	 * they match it will return the user record.
	 *
	 * @access protected
	 */
	protected function _Handle_Login()
	{
		//
		// Instantiate container.
		//
		$container = new CMongoContainer( $_REQUEST[ kAPI_CONTAINER ] );
		
		//
		// Create user ID.
		//
		$id = kENTITY_USER.kTOKEN_CLASS_SEPARATOR.$_REQUEST[ kAPI_OPT_USER_CODE ];
		
		//
		// Format user ID.
		//
		$id = new CDataTypeBinary( md5( $id, TRUE ) );
		
		//
		// Instantiate user.
		//
		$user = new CUser( $container, $id );
		if( $user !== NULL )
		{
			//
			// Check password.
			//
			if( $user->Password() == $_REQUEST[ kAPI_OPT_USER_PASS ] )
			{
				//
				// Set count.
				//
				$this->_OffsetManage( kAPI_DATA_STATUS, kAPI_AFFECTED_COUNT, 1 );
	
				//
				// Serialise response.
				//
				CDataType::SerialiseObject( $user );
	
				//
				// Copy response.
				//
				$this->offsetSet( kAPI_DATA_RESPONSE, $user );
				
				return;																// ==>
			
			} // Passwords match.
		
		} // Found user.

		//
		// Set count.
		//
		$this->_OffsetManage( kAPI_DATA_STATUS, kAPI_AFFECTED_COUNT, 0 );

		//
		// Set severity.
		//
		$this->_OffsetManage( kAPI_DATA_STATUS, kTAG_STATUS,
							  kMESSAGE_TYPE_WARNING );
		
		//
		// Set code.
		//
		$this->_OffsetManage( kAPI_DATA_STATUS, kTAG_CODE,
							  kERROR_NOT_FOUND );
		
		//
		// Set message.
		//
		$this->_OffsetManage( kAPI_DATA_STATUS, kTAG_DESCRIPTION,
							  array( kTAG_TYPE => kDATA_TYPE_STRING,
									 kTAG_LANGUAGE => 'en',
									 kTAG_DATA => 'Object not found.' ) );
	
	} // _Handle_Login.

	 

} // class CWarehouseWrapper.


?>
