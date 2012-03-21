<?php

/**
 * <i>CMongoDataWrapper</i> class definition.
 *
 * This file contains the class definition of <b>CMongoDataWrapper</b> which overloads its
 * {@link CDataWrapper ancestor} to implement a Mongo data store wrapper.
 *
 *	@package	Framework
 *	@subpackage	Wrappers
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 07/06/2011
 *				2.00 23/02/2012
 */

/*=======================================================================================
 *																						*
 *								CMongoDataWrapper.php									*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CDataWrapper.php" );

/**
 * Mongo query.
 *
 * This include file contains the Mongo {@link CMongoQuery object} class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CMongoQuery.php" );

/**
 * Session.
 *
 * This include file contains common session tag definitions.
 */
require_once( kPATH_LIBRARY_DEFINES."Session.inc.php" );

/**
 * Local definitions.
 *
 * This include file contains all local definitions to this class.
 */
require_once( kPATH_LIBRARY_SOURCE."CMongoDataWrapper.inc.php" );

/**
 *	Mongo data wrapper.
 *
 * This class overloads its {@link CDataWrapper ancestor} to implement a web-service that
 * uses a MongoDB data store to manage objects.
 *
 * This class implements the various elements declared in its {@link CDataWrapper ancestor}
 * and adds the following options:
 *
 * <ul>
 *	<li><i>{@link kAPI_OP_GET_ONE kAPI_OP_GET_ONE}</i>: This
 *		{@link kAPI_OPERATION operation} is equivalent to the
 *		{@link kAPI_OP_GET kAPI_OP_GET} operation, except that it will only return the first
 *		found element. It is equivalent to the Mongo findOne() method.
 *	<li><i>{@link kAPI_OP_GET_OBJECT_REF kAPI_OP_GET_OBJECT_REF}</i>: This
 *		{@link kAPI_OPERATION operation} will return an object referenced by an object
 *		reference (<i>MongoDBRef</i>). With this command you will not provide the
 *		{@link kAPI_CONTAINER container} and the {@link kAPI_DATA_QUERY query}, but you
 *		will provide an object reference in the {@link kAPI_DATA_OBJECT kAPI_DATA_OBJECT}
 *		parameter. Remember to {@link CDataType::SerialiseObject() serialise} the reference
 *		before providing it to the wrapper.
 * </ul>
 *
 * This class also implements a static interface that can be used to
 * {@link UnserialiseObject() unserialise} data
 * flowing to and from the service parameters and the Mongo container.
 *
 *	@package	Framework
 *	@subpackage	Wrappers
 */
class CMongoDataWrapper extends CDataWrapper
{
		

/*=======================================================================================
 *																						*
 *								PUBLIC CONVERSION INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	UnserialiseData																	*
	 *==================================================================================*/

	/**
	 * Unserialise provided data element.
	 *
	 * We {@link CContainer::UnserialiseData() implement} this method to convert all
	 * standard {@link CDataType types} into custom Mongo data types.
	 *
	 * In this class we parse the following types and {@link kTAG_TYPE offsets}:
	 *
	 * <ul>
	 *	<li><i>{@link CDataTypeMongoId CDataTypeMongoId} object or
	 *		{@link kDATA_TYPE_MongoId kDATA_TYPE_MongoId} offset</i>: We return a MongoId
	 *		object.
	 *	<li><i>{@link CDataTypeMongoCode CDataTypeMongoCode} object or
	 *		{@link kDATA_TYPE_MongoCode kDATA_TYPE_MongoCode} offset</i>: We return a
	 *		MongoCode object.
	 *	<li><i>{@link CDataTypeStamp CDataTypeStamp} object or
	 *		{@link kDATA_TYPE_STAMP kDATA_TYPE_STAMP} offset</i>: We return a MongoDate
	 *		object.
	 *	<li><i>{@link CDataTypeMongoRegex CDataTypeMongoRegex} object or
	 *		{@link kDATA_TYPE_MongoRegex kDATA_TYPE_MongoRegex} offset</i>: We return a
	 *		MongoRegex object.
	 *	<li><i>{@link CDataTypeInt32 CDataTypeInt32} object or
	 *		{@link kDATA_TYPE_INT32 kDATA_TYPE_INT32} offset</i>: We return a MongoInt32
	 *		object.
	 *	<li><i>{@link CDataTypeInt64 CDataTypeInt64} object or
	 *		{@link kDATA_TYPE_INT64 kDATA_TYPE_INT64} offset</i>: We return a MongoInt64
	 *		object.
	 *	<li><i>{@link CDataTypeBinary CDataTypeBinary} object or
	 *		{@link kDATA_TYPE_BINARY kDATA_TYPE_BINARY} offset</i>: We return a MongoBinData
	 *		object.
	 * </ul>
	 *
	 * @param reference			   &$theElement			Element to encode.
	 *
	 * @static
	 */
	static function UnserialiseData( &$theElement )
	{
		//
		// Handle type.
		//
		$data = $theElement[ kTAG_DATA ];
		switch( $theElement[ kTAG_TYPE ] )
		{
			//
			// MongoId.
			//
			case kDATA_TYPE_MongoId:
				$theElement = new MongoId( (string) $data );
				break;
			
			//
			// MongoCode.
			//
			case kDATA_TYPE_MongoCode:
				if( is_array( $data )
				 || ($data instanceof ArrayObject) )
				{
					$tmp1 = $data[ kOBJ_TYPE_CODE_SRC ];
					$tmp2 = ( array_key_exists( kOBJ_TYPE_CODE_SCOPE, (array) $data ) )
						  ? $data[ kOBJ_TYPE_CODE_SCOPE ]
						  : Array();
					$theElement = new MongoCode( $tmp1, $tmp2 );
				}
				break;
			
			//
			// MongoDate.
			//
			case kDATA_TYPE_STAMP:
				if( is_array( $data )
				 || ($data instanceof ArrayObject) )
				{
					$tmp1 = $data[ kOBJ_TYPE_STAMP_SEC ];
					$tmp2 = ( array_key_exists( kOBJ_TYPE_STAMP_USEC, (array) $data ) )
						  ? $data[ kOBJ_TYPE_STAMP_USEC ]
						  : 0;
					$theElement = new MongoDate( $tmp1, $tmp2 );
				}
				break;
			
			//
			// MongoInt32.
			//
			case kDATA_TYPE_INT32:
				$theElement = new MongoInt32( $data );
				break;
			
			//
			// MongoInt64.
			//
			case kDATA_TYPE_INT64:
				$theElement = new MongoInt64( $data );
				break;

			//
			// MongoRegex.
			//
			case kDATA_TYPE_MongoRegex:
				$theElement = new MongoRegex( $data );
				break;

			//
			// MongoBinData.
			//
			case kDATA_TYPE_BINARY:
				$data = ( function_exists( 'hex2bin' ) )
					  ? hex2bin( $data )
					  : pack( 'H*', $data );
				$theElement = new MongoBinData( $data );
				break;
		
		} // Parsing by type.
	
	} // UnserialiseData.

		

/*=======================================================================================
 *																						*
 *							PROTECTED INITIALISATION INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_InitResources																	*
	 *==================================================================================*/

	/**
	 * Initialise resources.
	 *
	 * In this class we initialise the Mongo object into the
	 * {@link kSESSION_MONGO kSESSION_MONGO} offset of the $_SESSION variable.
	 *
	 * @access private
	 */
	protected function _InitResources()		{	$_SESSION[ kSESSION_MONGO ] = new Mongo();	}

		

/*=======================================================================================
 *																						*
 *								PROTECTED PARSING INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_FormatRequest																	*
	 *==================================================================================*/

	/**
	 * Format request.
	 *
	 * This method should perform any needed formatting before the request will be handled.
	 *
	 * In this class we handle the parameters to be decoded
	 *
	 * @access private
	 */
	protected function _FormatRequest()	
	{
		//
		// Call parent method.
		//
		parent::_FormatRequest();
		
		//
		// Handle parameters.
		//
		$this->_FormatDatabase();
		$this->_FormatContainer();
	
	} // _FormatRequest.

	 
	/*===================================================================================
	 *	_ValidateRequest																*
	 *==================================================================================*/

	/**
	 * Validate request.
	 *
	 * This method should check that the request is valid and that all required parameters
	 * have been sent.
	 *
	 * In this class we check if the provided {@link kAPI_DATA_OBJECT object} contains the
	 * {@link kTAG_ID_REFERENCE identifier} when executing tree functions.
	 *
	 * @access private
	 */
	protected function _ValidateRequest()
	{
		//
		// Call parent method.
		//
		parent::_ValidateRequest();
		
		//
		// Validate parameters.
		//
		$this->_ValidateQuery();
		$this->_ValidateObject();
	
	} // _ValidateRequest.

		

/*=======================================================================================
 *																						*
 *								PROTECTED FORMAT INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_FormatDatabase																	*
	 *==================================================================================*/

	/**
	 * Format database.
	 *
	 * This method will format the request database.
	 *
	 * In this class we set the database to a MongoDB object.
	 *
	 * @access private
	 */
	protected function _FormatDatabase()
	{
		//
		// Get database connection.
		//
		if( array_key_exists( kAPI_DATABASE, $_REQUEST ) )
			$_REQUEST[ kAPI_DATABASE ]
				= $_SESSION[ kSESSION_MONGO ]->selectDB( $_REQUEST[ kAPI_DATABASE ] );
		
		//
		// Get database from reference.
		//
		elseif( ($_REQUEST[ kAPI_OPERATION ] == kAPI_OP_GET_OBJECT_REF)
			 && array_key_exists( kAPI_DATA_OBJECT, $_REQUEST )
			 && array_key_exists( kTAG_DATABASE_REFERENCE, $_REQUEST[ kAPI_DATA_OBJECT ] ) )
			$_REQUEST[ kAPI_DATABASE ]
				= $_SESSION[ kSESSION_MONGO ]
					->selectDB( $_REQUEST[ kAPI_DATA_OBJECT ][ kTAG_DATABASE_REFERENCE ] );
	
	} // _FormatDatabase.

	 
	/*===================================================================================
	 *	_FormatContainer																*
	 *==================================================================================*/

	/**
	 * Format container.
	 *
	 * This method will format the request container.
	 *
	 * In this class we set the container to a MongoCollection object.
	 *
	 * @access private
	 */
	protected function _FormatContainer()
	{
		//
		// Get collection connection.
		//
		if( array_key_exists( kAPI_CONTAINER, $_REQUEST ) )
		{
			//
			// Check if database was provided.
			//
			if( array_key_exists( kAPI_DATABASE, $_REQUEST ) )
				$_REQUEST[ kAPI_CONTAINER ]
					= $_REQUEST[ kAPI_DATABASE ]
						->selectCollection( $_REQUEST[ kAPI_CONTAINER ] );
		
		} // Provided container.
		
		//
		// Get container from reference.
		//
		elseif( ($_REQUEST[ kAPI_OPERATION ] == kAPI_OP_GET_OBJECT_REF)
			 && array_key_exists( kAPI_DATA_OBJECT, $_REQUEST )
			 && array_key_exists( kTAG_CONTAINER_REFERENCE, $_REQUEST[ kAPI_DATA_OBJECT ] )
			 && array_key_exists( kAPI_DATABASE, $_REQUEST ) )
			$_REQUEST[ kAPI_CONTAINER ]
				= $_REQUEST[ kAPI_DATABASE ]
					->selectCollection
						( $_REQUEST[ kAPI_DATA_OBJECT ][ kTAG_CONTAINER_REFERENCE ] );
	
	} // _FormatContainer.

	 
	/*===================================================================================
	 *	_FormatQuery																	*
	 *==================================================================================*/

	/**
	 * Format query.
	 *
	 * This method will format the request query.
	 *
	 * In this class we set the query to a CMongoQuery object.
	 *
	 * @access private
	 */
	protected function _FormatQuery()
	{
		//
		// Call parent method.
		//
		parent::_FormatQuery();
		
		//
		// Get query object.
		//
		if( array_key_exists( kAPI_DATA_QUERY, $_REQUEST ) )
			$_REQUEST[ kAPI_DATA_QUERY ]
				= new CMongoQuery( $_REQUEST[ kAPI_DATA_QUERY ] );
	
	} // _FormatQuery.

	 
	/*===================================================================================
	 *	_FormatObject																	*
	 *==================================================================================*/

	/**
	 * Format object.
	 *
	 * This method will format the request object.
	 *
	 * In this class we resolve the Mongo native types.
	 *
	 * @access private
	 */
	protected function _FormatObject()
	{
		//
		// Call parent method.
		//
		parent::_FormatObject();
		
		//
		// Convert to native Mongo types.
		//
		if( array_key_exists( kAPI_DATA_OBJECT, $_REQUEST ) )
			CDataType::SerialiseObject( $_REQUEST[ kAPI_DATA_OBJECT ] );
	
	} // _FormatObject.

		

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
	 * In this class, if the query was omitted and an object reference was required, we
	 * check if the object {@link kTAG_ID_NATIVE native} identifier is there: in that case
	 * we compile the query with that value.
	 *
	 * @access private
	 */
	protected function _ValidateOperation()
	{
		//
		// Parse operation.
		//
		switch( $parameter = $_REQUEST[ kAPI_OPERATION ] )
		{
			case kAPI_OP_GET_OBJECT_REF:
				
				//
				// Check for object.
				//
				if( ! array_key_exists( kAPI_DATA_OBJECT, $_REQUEST ) )
					throw new CException
						( "Missing object reference",
						  kERROR_OPTION_MISSING,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Operation' => $parameter ) );					// !@! ==>
				
			case kAPI_OP_GET_ONE:
				
				//
				// Check for database.
				//
				if( ! array_key_exists( kAPI_DATABASE, $_REQUEST ) )
					throw new CException
						( "Missing database reference",
						  kERROR_OPTION_MISSING,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Operation' => $parameter ) );					// !@! ==>
				
				//
				// Check for container.
				//
				if( ! array_key_exists( kAPI_CONTAINER, $_REQUEST ) )
					throw new CException
						( "Missing container reference",
						  kERROR_OPTION_MISSING,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Operation' => $parameter ) );					// !@! ==>
				
				//
				// Enforce query.
				// MILKO - !!! Don't know why!!!
				//
				if( ! array_key_exists( kAPI_DATA_QUERY, $_REQUEST ) )
					$_REQUEST[ kAPI_DATA_QUERY ] = Array();

				break;
			
			//
			// Compile query for commits.
			//
			case kAPI_OP_DEL:
			case kAPI_OP_UPDATE:
			case kAPI_OP_MODIFY:
				if( array_key_exists( kAPI_DATA_OBJECT, $_REQUEST )
				 && array_key_exists( kTAG_ID_NATIVE, $_REQUEST[ kAPI_DATA_OBJECT ] )
				 && (! array_key_exists( kAPI_DATA_QUERY, $_REQUEST )) )
					$_REQUEST[ kAPI_DATA_QUERY ]
						= array(
						kOPERATOR_AND => array(
							kAPI_QUERY_SUBJECT => kTAG_ID_NATIVE,
							kAPI_QUERY_OPERATOR => kOPERATOR_EQUAL,
							kAPI_QUERY_TYPE => $_REQUEST[ kAPI_DATA_OBJECT ]
															 [ kTAG_ID_NATIVE ]
															 [ kTAG_TYPE ],
							kAPI_QUERY_DATA => array(
								kTAG_TYPE => $_REQUEST[ kAPI_DATA_OBJECT ]
													   [ kTAG_ID_NATIVE ]
													   [ kTAG_TYPE ],
								kAPI_QUERY_DATA => $_REQUEST[ kAPI_DATA_OBJECT ]
															[ kTAG_ID_NATIVE ]
															[ kAPI_QUERY_DATA ] ) ) );
			
			//
			// Handle unknown operation.
			//
			default:
				parent::_ValidateOperation();
				break;
			
		} // Parsing parameter.
	
	} // _ValidateOperation.

	 
	/*===================================================================================
	 *	_ValidateObject																	*
	 *==================================================================================*/

	/**
	 * Validate request object.
	 *
	 * This method can be used to check whether the provided
	 * {@link kAPI_DATA_OBJECT object} contains the {@link kTAG_ID_REFERENCE identifier}
	 * when executing tree functions.
	 *
	 * @access private
	 */
	protected function _ValidateObject()
	{
		//
		// Parse operation.
		//
		switch( $parameter = $_REQUEST[ kAPI_OPERATION ] )
		{
			case kAPI_OP_GET_OBJECT_REF:
				if( ! array_key_exists( kTAG_ID_REFERENCE,
										$_REQUEST[ kAPI_DATA_OBJECT ] ) )
					throw new CException
						( "Missing object reference identifier",
						  kERROR_OPTION_MISSING,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Operation' => $parameter,
						  		 'Parameter' => kTAG_ID_REFERENCE ) );		// !@! ==>
				break;
			
		} // Parsing parameter.
	
	} // _ValidateObject.

	 
	/*===================================================================================
	 *	_ValidateQuery																	*
	 *==================================================================================*/

	/**
	 * Validate query reference.
	 *
	 * This method can be used to check whether the provided
	 * {@link kAPI_DATA_QUERY query} parameter is valid.
	 *
	 * In this class we convert the query to the native Mongo format.
	 *
	 * @access private
	 */
	protected function _ValidateQuery()
	{
		//
		// Handle query.
		//
		if( array_key_exists( kAPI_DATA_QUERY, $_REQUEST )
		 && count( $_REQUEST[ kAPI_DATA_QUERY ] ) )
		{
			//
			// Validate query.
			//
			$_REQUEST[ kAPI_DATA_QUERY ]->Validate();

			//
			// Format query.
			//
			if( array_key_exists( kAPI_DATA_QUERY, $_REQUEST ) )
				$_REQUEST[ kAPI_DATA_QUERY ] = $_REQUEST[ kAPI_DATA_QUERY ]->Export();
		
		} // Provided query.
	
	} // _ValidateQuery.

		

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
	 * @access private
	 */
	protected function _HandleRequest()
	{
		//
		// Parse by operation.
		//
		switch( $op = $_REQUEST[ kAPI_OPERATION ] )
		{
			case kAPI_OP_GET_OBJECT_REF:
				$this->_Handle_GetObjectByReference();
				break;

			case kAPI_OP_COUNT:
				$this->_Handle_Count();
				break;

			case kAPI_OP_GET_ONE:
				$this->_Handle_GetOne();
				break;

			case kAPI_OP_GET:
				$this->_Handle_Get();
				break;

			case kAPI_OP_SET:
				$this->_Handle_Set();
				break;

			case kAPI_OP_INSERT:
				$this->_Handle_Insert();
				break;

			case kAPI_OP_DEL:
				$this->_Handle_Delete();
				break;

			case kAPI_OP_UPDATE:
			case kAPI_OP_MODIFY:
			default:
				parent::_HandleRequest();
				break;
		}
	
	} // _HandleRequest.

	 
	/*===================================================================================
	 *	_Handle_GetObjectByReference													*
	 *==================================================================================*/

	/**
	 * Handle {@link kAPI_OP_GET_OBJECT_REF GetObjectByReference} request.
	 *
	 * This method will handle the
	 * {@link kAPI_OP_GET_OBJECT_REF kAPI_OP_GET_OBJECT_REF} request, which returns an
	 * object corresponding to the object {@link CMongoObjectReference reference} provided
	 * in the {@link kAPI_DATA_OBJECT object} parameter.
	 *
	 * @access private
	 */
	protected function _Handle_GetObjectByReference()
	{
		//
		// Resolve reference.
		//
		$response = MongoDBRef::get( $_REQUEST[ kAPI_DATABASE ],
									 $_REQUEST[ kAPI_DATA_OBJECT ] );
		
		//
		// Set total count.
		//
		$count = ( $response )
			   ? 1
			   : 0;
		$this->_OffsetManage( kAPI_DATA_STATUS, kAPI_AFFECTED_COUNT, $count );
		
		//
		// Serialise response.
		//
		CDataType::SerialiseObject( $response );
	
		//
		// Return response.
		//
		$this[ kAPI_DATA_RESPONSE ] = $response;
	
	} // _Handle_GetObjectByReference.

	 
	/*===================================================================================
	 *	_Handle_Count																	*
	 *==================================================================================*/

	/**
	 * Handle {@link kAPI_OP_COUNT COUNT} request.
	 *
	 * This method will handle the {@link kAPI_OP_COUNT kAPI_OP_COUNT} request, which
	 * returns the total count of a Mongo query.
	 *
	 * @access private
	 */
	protected function _Handle_Count()
	{
		//
		// Query database.
		//
		$cursor = $_REQUEST[ kAPI_CONTAINER ]->find( $_REQUEST[ kAPI_DATA_QUERY ] );
		
		//
		// Set count.
		//
		$this->_OffsetManage( kAPI_DATA_STATUS, kAPI_AFFECTED_COUNT,
							  $cursor->count( FALSE ) );
	
	} // _Handle_Count.

	 
	/*===================================================================================
	 *	_Handle_GetOne																	*
	 *==================================================================================*/

	/**
	 * Handle {@link kAPI_OP_GET_ONE GetOne} request.
	 *
	 * This method will handle the {@link kAPI_OP_GET_ONE kAPI_OP_GET_ONE} request, which
	 * corresponds to the findOne Mongo query.
	 *
	 * @access private
	 */
	protected function _Handle_GetOne()
	{
		//
		// Handle fields.
		//
		$fields = ( array_key_exists( kAPI_DATA_FIELD, $_REQUEST ) )
				? $_REQUEST[ kAPI_DATA_FIELD ]
				: Array();
		
		//
		// Locate object.
		//
		$object = $_REQUEST[ kAPI_CONTAINER ]->findOne( $_REQUEST[ kAPI_DATA_QUERY ],
														$fields );
		
		//
		// Set count.
		//
		$count = ( $object !== NULL ) ? 1 : 0;
		$this->_OffsetManage( kAPI_DATA_STATUS, kAPI_AFFECTED_COUNT, $count );
		if( $count )
		{
			//
			// Serialise response.
			//
			CDataType::SerialiseObject( $object );

			//
			// Copy response.
			//
			$this[ kAPI_DATA_RESPONSE ] = $object;
		}
		
		//
		// Handle not found.
		//
		else
		{
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
		}
	
	} // _Handle_GetOne.

	 
	/*===================================================================================
	 *	_Handle_Get																		*
	 *==================================================================================*/

	/**
	 * Handle {@link kAPI_OP_GET Get} request.
	 *
	 * This method will handle the {@link kAPI_OP_GET kAPI_OP_GET} request, which
	 * corresponds to the find Mongo query.
	 *
	 * @access private
	 */
	protected function _Handle_Get()
	{
		//
		// Handle query.
		//
		$query = ( array_key_exists( kAPI_DATA_QUERY, $_REQUEST ) )
				? $_REQUEST[ kAPI_DATA_QUERY ]
				: Array();
		
		//
		// Handle fields.
		//
		$fields = ( array_key_exists( kAPI_DATA_FIELD, $_REQUEST ) )
				? $_REQUEST[ kAPI_DATA_FIELD ]
				: Array();
		
		//
		// Handle sort.
		//
		$sort = ( array_key_exists( kAPI_DATA_SORT, $_REQUEST ) )
			  ? $_REQUEST[ kAPI_DATA_SORT ]
			  : Array();
		
		//
		// Get cursor.
		//
		$cursor = $_REQUEST[ kAPI_CONTAINER ]->find( $query, $fields );
		
		//
		// Set total count.
		//
		$count = $cursor->count( FALSE );
		$this->_OffsetManage( kAPI_DATA_STATUS, kAPI_AFFECTED_COUNT, $count );
		
		//
		// Continue if count option is not there.
		//
		if( (! array_key_exists( kAPI_DATA_OPTIONS, $_REQUEST ))
		 || (! array_key_exists( kAPI_OPT_COUNT, $_REQUEST[ kAPI_DATA_OPTIONS ] ))
		 || (! $_REQUEST[ kAPI_DATA_OPTIONS ][ kAPI_OPT_COUNT ]) )
		{
			//
			// Handle results.
			//
			if( $count )
			{
				//
				// Set sort.
				//
				if( $sort )
					$cursor->sort( $sort );
				
				//
				// Set paging.
				//
				if( $this->offsetExists( kAPI_DATA_PAGING ) )
				{
					//
					// Set paging.
					//
					$paging = $this->offsetGet( kAPI_DATA_PAGING );
					$start = ( array_key_exists( kAPI_PAGE_START, $paging ) )
						   ? (int) $paging[ kAPI_PAGE_START ]
						   : 0;
					$limit = ( array_key_exists( kAPI_PAGE_LIMIT, $paging ) )
						   ? (int) $paging[ kAPI_PAGE_LIMIT ]
						   : 0;
					
					//
					// Position at start.
					//
					if( $start )
						$cursor->skip( $start );
					
					//
					// Set limit.
					//
					if( $limit )
						$cursor->limit( $limit );
					
					//
					// Set page count.
					//
					$pcount = $cursor->count( TRUE );
					
					//
					// Update parameters.
					//
					$this->_OffsetManage( kAPI_DATA_PAGING, kAPI_PAGE_START, $start );
					$this->_OffsetManage( kAPI_DATA_PAGING, kAPI_PAGE_LIMIT, $limit );
					$this->_OffsetManage( kAPI_DATA_PAGING, kAPI_PAGE_COUNT, $pcount );
				
				} // Provided paging options.
				
				//
				// Handle excluded identifier.
				// By default the returned array is indexed by ID...
				//
				if( array_key_exists( kTAG_ID_NATIVE, $fields )
				 && (! $fields[ kTAG_ID_NATIVE ]) )
				{
					//
					// Collect results.
					//
					$result = Array();
					foreach( $cursor as $data )
						$result[] = $data;
				
				} // Excluded identifier.
				
				//
				// Result has identifier.
				//
				else
//					$result = iterator_to_array( $cursor );
				{
					$result = Array();
					foreach( $cursor as $element )
						$result[] = $element;
				}
				
				//
				// Handle options.
				//
				if( array_key_exists( kAPI_DATA_OPTIONS, $_REQUEST ) )
					$this->_HandleOptions( $result );
	
				//
				// Serialise response.
				//
				CDataType::SerialiseObject( $result );
				
				//
				// Copy to response.
				//
				$this[ kAPI_DATA_RESPONSE ] = $result;
				
			} // Has results.
			
			//
			// Handle not found.
			//
			else
			{
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
											 kTAG_DATA => 'No objects found.' ) );
			}
		
		} // Not COUNT option.
	
	} // _Handle_Get.

	 
	/*===================================================================================
	 *	_Handle_Set																		*
	 *==================================================================================*/

	/**
	 * Handle {@link kAPI_OP_SET Set} request.
	 *
	 * This method will handle the {@link kAPI_OP_SET kAPI_OP_SET} request, which
	 * will insert/update the provided object.
	 *
	 * @access private
	 */
	protected function _Handle_Set()
	{
		//
		// Create options.
		//
		$options = Array();
		if( array_key_exists( kAPI_DATA_OPTIONS, $_REQUEST ) )
		{
			//
			// Iterate options.
			//
			foreach( $_REQUEST[ kAPI_DATA_OPTIONS ] as $key => $value )
			{
				//
				// Parse options.
				//
				switch( $key )
				{
					case kAPI_OPT_SAFE:
					case kAPI_OPT_FSYNC:
						$options[ $key ] = (boolean) $value;
						break;
					
					case kAPI_OPT_TIMEOUT:
						$options[ $key ] = (integer) $value;
						break;
				
				} // Parsed option.
			
			} // Iterating options.
		
		} // Iterated options.
		
		//
		// Save object.
		//
		$ok = $_REQUEST[ kAPI_CONTAINER ]->save( $_REQUEST[ kAPI_DATA_OBJECT ], $options );
		
		//
		// Copy response.
		//
		$this[ kAPI_DATA_RESPONSE ] = $_REQUEST[ kAPI_DATA_OBJECT ];
		
		//
		// Serialise response.
		//
		CDataType::SerialiseObject( $_REQUEST[ kAPI_DATA_RESPONSE ] );
	
	} // _Handle_Set.

	 
	/*===================================================================================
	 *	_Handle_Insert																	*
	 *==================================================================================*/

	/**
	 * Handle {@link kAPI_OP_INSERT Insert} request.
	 *
	 * This method will handle the {@link kAPI_OP_INSERT kAPI_OP_INSERT} request, which
	 * will insert the provided object.
	 *
	 * @access private
	 */
	protected function _Handle_Insert()
	{
		//
		// Create options.
		//
		$options = Array();
		if( array_key_exists( kAPI_DATA_OPTIONS, $_REQUEST ) )
		{
			//
			// Iterate options.
			//
			foreach( $_REQUEST[ kAPI_DATA_OPTIONS ] as $key => $value )
			{
				//
				// Parse options.
				//
				switch( $key )
				{
					case kAPI_OPT_SAFE:
					case kAPI_OPT_FSYNC:
						$options[ $key ] = (boolean) $value;
						break;
					
					case kAPI_OPT_TIMEOUT:
						$options[ $key ] = (integer) $value;
						break;
				
				} // Parsed option.
			
			} // Iterating options.
		
		} // Iterated options.
		
		//
		// Insert object.
		//
		$ok
			= $_REQUEST[ kAPI_CONTAINER ]->insert
				( $_REQUEST[ kAPI_DATA_OBJECT ], $options );
		
		//
		// Copy response.
		//
		$this[ kAPI_DATA_RESPONSE ] = $_REQUEST[ kAPI_DATA_OBJECT ];
		
		//
		// Serialise response.
		//
		CDataType::SerialiseObject( $_REQUEST[ kAPI_DATA_RESPONSE ] );
	
	} // _Handle_Insert.

	 
	/*===================================================================================
	 *	_Handle_Delete																	*
	 *==================================================================================*/

	/**
	 * Handle {@link kAPI_OP_DEL Delete} request.
	 *
	 * This method will handle the {@link kAPI_OP_DEL kAPI_OP_DEL} request, whichwill delete
	 * all objects matching the provided filter.
	 *
	 * The method expects the <i>justOne</i> parameter in the provided
	 * {@link kAPI_DATA_OPTIONS options}, if not provided, it will default to <i>FALSE</i>.
	 *
	 * @access private
	 */
	protected function _Handle_Delete()
	{
		//
		// Create options.
		//
		$options = Array();
		if( array_key_exists( kAPI_DATA_OPTIONS, $_REQUEST ) )
		{
			//
			// Iterate options.
			//
			foreach( $_REQUEST[ kAPI_DATA_OPTIONS ] as $key => $value )
			{
				//
				// Parse options.
				//
				switch( $key )
				{
					case kAPI_OPT_SAFE:
					case kAPI_OPT_FSYNC:
					case kAPI_OPT_SINGLE:
						$options[ $key ] = (boolean) $value;
						break;
					
					case kAPI_OPT_TIMEOUT:
						$options[ $key ] = (integer) $value;
						break;
				
				} // Parsed option.
			
			} // Iterating options.
			
			//
			// Set justOne option.
			//
			if( ! array_key_exists( kAPI_OPT_SINGLE, $options ) )
				$options[ kAPI_OPT_SINGLE ] = FALSE;
		
		} // Iterated options.
		
		//
		// Delete object.
		//
		$ok
			= $_REQUEST[ kAPI_CONTAINER ]
				->remove( $_REQUEST[ kAPI_DATA_QUERY ], $options );
		
		//
		// Set deleted count.
		//
		if( array_key_exists( kAPI_OPT_SAFE, $options )
		 || array_key_exists( kAPI_OPT_FSYNC, $options ) )
			$this->_OffsetManage( kAPI_DATA_STATUS, kAPI_AFFECTED_COUNT, $ok[ 'n' ] );
	
	} // _Handle_Delete.

		

/*=======================================================================================
 *																						*
 *							PROTECTED OPTIONS HANDLER INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_HandleOptions																	*
	 *==================================================================================*/

	/**
	 * Handle options.
	 *
	 * This method will be called before serialising the result if
	 * {@link kAPI_DATA_OPTIONS options} are provided in the request.
	 *
	 * In this class we don't do anything, derived classes should handle specific elements.
	 *
	 * @param reference			   &$theResult			Results list.
	 * @param array					$theOptions			Key/value options list.
	 *
	 * @access private
	 */
	protected function _HandleOptions( &$theResult, $theOptions )							{}

	 

} // class CMongoDataWrapper.


?>
