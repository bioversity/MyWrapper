<?php

/**
 * <i>CMongoDataWrapper</i> class definition.
 *
 * This file contains the class definition of <b>CMongoDataWrapper</b> which overloads its
 * {@link CDataWrapper ancestor} to implement a Mongo data store wrapper.
 *
 *	@package	MyWrapper
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
 *		{@link kAPI_OPERATION operation} will return an object referenced by an identifier
 *		provided in the {@link kAPI_DATA_OBJECT kAPI_DATA_OBJECT} parameter. It is
 *		equivalent to the {@link kAPI_OP_GET_ONE kAPI_OP_GET_ONE} operation, except that
 *		instead of using the query provided in the {@link kAPI_DATA_QUERY kAPI_DATA_QUERY}
 *		parameter, it will try to extract an identifier from the object provided in the
 *		{@link kAPI_DATA_OBJECT kAPI_DATA_OBJECT} parameter. Remember to
 *		{@link CDataType::SerialiseObject() serialise} the reference before providing it to
 *		the wrapper.
 * </ul>
 *
 * This class also implements a static interface that can be used to
 * {@link UnserialiseObject() unserialise} data
 * flowing to and from the service parameters and the Mongo container.
 *
 *	@package	MyWrapper
 *	@subpackage	Wrappers
 */
class CMongoDataWrapper extends CDataWrapper
{
		

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
	 * @access protected
	 */
	protected function _InitResources()		{	$_SESSION[ kSESSION_MONGO ] = new Mongo();	}

		

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
	 * We overload this method to parse the no {@link kAPI_OPT_NO_RESP response} tag.
	 *
	 * @access private
	 *
	 * @uses _ParseNoResponse()
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
		$this->_ParseNoResponse();
	
	} // _ParseRequest.

	 
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
	 * @access protected
	 */
	protected function _FormatRequest()	
	{
		//
		// Handle data storage.
		//
		$this->_FormatDatabase();
		$this->_FormatContainer();
	
		//
		// Call parent method.
		//
		parent::_FormatRequest();
		
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
	 * {@link kTAG_REFERENCE_ID identifier} when executing tree functions.
	 *
	 * @access protected
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
 *								PROTECTED PARSING UTILITIES								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_ParseNoResponse																*
	 *==================================================================================*/

	/**
	 * Parse no response.
	 *
	 * This method will parse the no response operation.
	 *
	 * @access private
	 *
	 * @see kAPI_DATA_REQUEST kAPI_OPT_NO_RESP
	 */
	protected function _ParseNoResponse()
	{
		//
		// Handle no response flag.
		//
		if( array_key_exists( kAPI_OPT_NO_RESP, $_REQUEST ) )
		{
			if( $this->offsetExists( kAPI_DATA_REQUEST ) )
				$this->_OffsetManage
					( kAPI_DATA_REQUEST, kAPI_OPT_NO_RESP, $_REQUEST[ kAPI_OPT_NO_RESP ] );
		}
	
	} // _ParseNoResponse.

		

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
	 * @access protected
	 */
	protected function _FormatDatabase()
	{
		//
		// Get database connection.
		//
		if( array_key_exists( kAPI_DATABASE, $_REQUEST ) )
			$_REQUEST[ kAPI_DATABASE ]
				= $_SESSION[ kSESSION_MONGO ]->selectDB( $_REQUEST[ kAPI_DATABASE ] );
	
	} // _FormatDatabase.

	 
	/*===================================================================================
	 *	_FormatContainer																*
	 *==================================================================================*/

	/**
	 * Format container.
	 *
	 * This method will format the request container.
	 *
	 * In this class we set the request container to a MongoCollection object.
	 *
	 * @access protected
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
	 * @access protected
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
	 * @access protected
	 */
	protected function _FormatObject()
	{
		//
		// Call parent method.
		//
		parent::_FormatObject();
		
		//
		// Check if object is there.
		//
		if( array_key_exists( kAPI_DATA_OBJECT, $_REQUEST ) )
		{
			//
			// Handle references.
			//
			switch( $parameter = $_REQUEST[ kAPI_OPERATION ] )
			{
				case kAPI_OP_GET_OBJECT_REF:
					//
					// Extract reference.
					//
					$reference
						= CPersistentUnitObject::Reference
							( $_REQUEST[ kAPI_DATA_OBJECT ], kFLAG_REFERENCE_IDENTIFIER +
															 kFLAG_REFERENCE_CONTAINER +
															 kFLAG_REFERENCE_DATABASE );
					
					//
					// Handle reference.
					//
					if( $reference !== NULL )
					{
						//
						// Add database reference.
						//
						if( array_key_exists( kAPI_DATABASE, $_REQUEST ) )
							$reference[ kTAG_REFERENCE_DATABASE ]
								= (string) $_REQUEST[ kAPI_DATABASE ];
						
						//
						// Add container reference.
						//
						if( array_key_exists( kAPI_CONTAINER, $_REQUEST ) )
							$reference[ kTAG_REFERENCE_CONTAINER ]
								= $_REQUEST[ kAPI_CONTAINER ]->getName();
						
						//
						// Copy reference.
						//
						$_REQUEST[ kAPI_DATA_OBJECT ] = $reference;
						
						//
						// Handle database.
						//
						if( array_key_exists( kTAG_REFERENCE_DATABASE, $reference )
						 && (! array_key_exists( kAPI_DATABASE, $_REQUEST )) )
						{
							$_REQUEST[ kAPI_DATABASE ]
								= $reference[ kTAG_REFERENCE_DATABASE ];
							$this->_FormatDatabase();
						}
						
						//
						// Handle container.
						//
						if( array_key_exists( kTAG_REFERENCE_CONTAINER, $reference )
						 && (! array_key_exists( kAPI_CONTAINER, $_REQUEST )) )
						{
							$_REQUEST[ kAPI_CONTAINER ]
								= $reference[ kTAG_REFERENCE_CONTAINER ];
							$this->_FormatContainer();
						}
					
					} // Resolved.
					
					//
					// Invalid reference.
					//
					else
						throw new CException
							( "Invalid object reference",
							  kERROR_UNSUPPORTED,
							  kMESSAGE_TYPE_ERROR,
							  array( 'Reference'
								=> $_REQUEST[ kAPI_DATA_OBJECT ] ) );			// !@! ==>
					
					break;
			}
			
			//
			// Check if container is there.
			//
			if( array_key_exists( kAPI_CONTAINER, $_REQUEST ) )
			{
				//
				// Correct container type.
				//
				if( $_REQUEST[ kAPI_CONTAINER ] instanceof MongoCollection )
				{
					//
					// Instantiate container.
					//
					$container = new CMongoContainer( $_REQUEST[ kAPI_CONTAINER ] );
					
					//
					// Convert object.
					//
					$container->UnserialiseObject( $_REQUEST[ kAPI_DATA_OBJECT ] );
				
				} // Supported container.
				
				else
					throw new CException
						( "Unsupported container type",
						  kERROR_UNSUPPORTED,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Container'
							=> $_REQUEST[ kAPI_CONTAINER ] ) );				// !@! ==>
			
			} // Provided container.
			
			else
				throw new CException
					( "Missing container reference",
					  kERROR_OPTION_MISSING,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Parameter' => kAPI_CONTAINER ) );				// !@! ==>
		
		} // Provided object.
	
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
	 * check if the object {@link kTAG_LID native} identifier is there: in that case
	 * we compile the query with that value.
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
				// Enforce query.
				// MILKO - !!! Don't know why!!!
				//
				if( ! array_key_exists( kAPI_DATA_QUERY, $_REQUEST ) )
					$_REQUEST[ kAPI_DATA_QUERY ] = Array();

				break;
			
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
	 * {@link kAPI_DATA_OBJECT object} contains the {@link kTAG_REFERENCE_ID identifier}
	 * when executing tree functions.
	 *
	 * @access protected
	 */
	protected function _ValidateObject()
	{
		//
		// Parse operation.
		//
		switch( $parameter = $_REQUEST[ kAPI_OPERATION ] )
		{
			case kAPI_OP_GET_OBJECT_REF:
				if( ! array_key_exists( kAPI_DATA_OBJECT, $_REQUEST ) )
					throw new CException
						( "Missing object reference parameter",
						  kERROR_OPTION_MISSING,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Operation' => $parameter,
						  		 'Parameter' => kAPI_DATA_OBJECT ) );			// !@! ==>
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
	 * @access protected
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
			{
				//
				// Check container.
				//
				if( array_key_exists( kAPI_CONTAINER, $_REQUEST ) )
				{
					//
					// Correct container type.
					//
					if( $_REQUEST[ kAPI_CONTAINER ] instanceof MongoCollection )
					{
						//
						// Convert query.
						//
						$_REQUEST[ kAPI_DATA_QUERY ]
							= $_REQUEST[ kAPI_DATA_QUERY ]
								->Export( new CMongoContainer
									( $_REQUEST[ kAPI_CONTAINER ] ) );
					
					} // Supported container.
					
					else
						throw new CException
							( "Unsupported container type",
							  kERROR_UNSUPPORTED,
							  kMESSAGE_TYPE_ERROR,
							  array( 'Container'
							  	=> $_REQUEST[ kAPI_CONTAINER ] ) );				// !@! ==>
				
				} // Provided container.
				
				else
					throw new CException
						( "Missing container reference",
						  kERROR_OPTION_MISSING,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Parameter' => kAPI_CONTAINER ) );				// !@! ==>
				
			} // Still there.
		
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
	 * @access protected
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

			case kAPI_OP_BATCH_INSERT:
				$this->_Handle_BatchInsert();
				break;

			case kAPI_OP_UPDATE:
				$this->_Handle_Update();
				break;

			case kAPI_OP_MODIFY:
				$this->_Handle_Modify();
				break;

			case kAPI_OP_DEL:
				$this->_Handle_Delete();
				break;

			default:
				parent::_HandleRequest();
				break;
		}
	
	} // _HandleRequest.

	 
	/*===================================================================================
	 *	_Handle_ListOp																	*
	 *==================================================================================*/

	/**
	 * Handle {@link kAPI_OP_HELP list} operations request.
	 *
	 * This method will handle the {@link kAPI_OP_HELP kAPI_OP_HELP} request, which
	 * should return the list of supported operations.
	 *
	 * @param reference				$theList			Receives operations list.
	 *
	 * @access protected
	 */
	protected function _Handle_ListOp( &$theList )
	{
		//
		// Call parent method.
		//
		parent::_Handle_ListOp( $theList );
		
		//
		// Add kAPI_OP_GET_ONE.
		//
		$theList[ kAPI_OP_GET_ONE ]
			= 'This operation is equivalent to the ['
			 .kAPI_OP_GET
			 .'] operation, except that it will only return the first found element. '
			 .'It is equivalent to the Mongo findOne() method.';
		
		//
		// Add kAPI_OP_GET_OBJECT_REF.
		//
		$theList[ kAPI_OP_GET_OBJECT_REF ]
			= 'This operation will return an object referenced by an identifier '
			 .'provided in the ['
			 .kAPI_DATA_OBJECT
			 .'] parameter. It is equivalent to the ['
			 .kAPI_OP_GET_ONE
			 .'] operation, except that instead of using the query provided in the ['
			 .kAPI_DATA_QUERY
			 .'] parameter, it will try to extract an identifier from the object '
			 .'provided in the ['
			 .kAPI_DATA_OBJECT
			 .'] parameter.';
	
	} // _Handle_ListOp.

		
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
	 * @access protected
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
		if( (! array_key_exists( kAPI_OPT_NO_RESP, $_REQUEST ))
		 || (! $_REQUEST[ kAPI_OPT_NO_RESP ]) )
			$this->offsetSet( kAPI_DATA_RESPONSE, $response );
	
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
	 * @access protected
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
	 * @access protected
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
			$this->offsetSet( kAPI_DATA_RESPONSE, $object );
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
								  array( kTAG_TYPE => kTYPE_STRING,
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
	 * @access protected
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
				// Handle response.
				//
				if( (! array_key_exists( kAPI_OPT_NO_RESP, $_REQUEST ))
				 || (! $_REQUEST[ kAPI_OPT_NO_RESP ]) )
				{
					//
					// Handle excluded identifier.
					// By default the returned array is indexed by ID...
					//
					if( array_key_exists( kTAG_LID, $fields )
					 && (! $fields[ kTAG_LID ]) )
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
//						$result = iterator_to_array( $cursor );
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
					$this->offsetSet( kAPI_DATA_RESPONSE, $result );
				
				} // No response option not set.
				
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
									  array( kTAG_TYPE => kTYPE_STRING,
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
	 * @access protected
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
		$this->_OffsetManage( kAPI_DATA_STATUS, kAPI_STATUS_NATIVE, $ok );
		
		//
		// Handle response.
		//
		if( (! array_key_exists( kAPI_OPT_NO_RESP, $_REQUEST ))
		 || (! $_REQUEST[ kAPI_OPT_NO_RESP ]) )
		{
			//
			// Copy response.
			//
			$this->offsetSet( kAPI_DATA_RESPONSE, $_REQUEST[ kAPI_DATA_OBJECT ] );
			
			//
			// Serialise response.
			//
			// Note this ugly workflow:
			// I need to do this or else I get this
			// Notice: Indirect modification of overloaded element of MyClass
			// has no effect in /MySource.php
			// Which means that I cannot pass $this[ kAPI_DATA_RESPONSE ] to SerialiseData()
			// or I get the notice and the thing doesn't work.
			//
			$save = $this->offsetGet( kAPI_DATA_RESPONSE );
			CDataType::SerialiseObject( $save );
			$this->offsetSet( kAPI_DATA_RESPONSE, $save );
				
		} // No response option not set.
	
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
	 * @access protected
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
		$ok = $_REQUEST[ kAPI_CONTAINER ]->insert
				( $_REQUEST[ kAPI_DATA_OBJECT ], $options );
		$this->_OffsetManage( kAPI_DATA_STATUS, kAPI_STATUS_NATIVE, $ok );
		
		//
		// Handle response.
		//
		if( (! array_key_exists( kAPI_OPT_NO_RESP, $_REQUEST ))
		 || (! $_REQUEST[ kAPI_OPT_NO_RESP ]) )
		{
			//
			// Copy response.
			//
			$this->offsetSet( kAPI_DATA_RESPONSE, $_REQUEST[ kAPI_DATA_OBJECT ] );
			
			//
			// Serialise response.
			//
			// Note this ugly workflow:
			// I need to do this or else I get this
			// Notice: Indirect modification of overloaded element of MyClass
			// has no effect in /MySource.php
			// Which means that I cannot pass $this[ kAPI_DATA_RESPONSE ] to SerialiseData()
			// or I get the notice and the thing doesn't work.
			//
			$save = $this->offsetGet( kAPI_DATA_RESPONSE );
			CDataType::SerialiseObject( $save );
			$this->offsetSet( kAPI_DATA_RESPONSE, $save );
				
		} // No response option not set.
	
	} // _Handle_Insert.

	 
	/*===================================================================================
	 *	_Handle_BatchInsert																*
	 *==================================================================================*/

	/**
	 * Handle {@link kAPI_OP_BATCH_INSERT batch} Insert request.
	 *
	 * This method will handle the {@link kAPI_OP_BATCH_INSERT kAPI_OP_BATCH_INSERT}
	 * request, which will insert the provided list of objects.
	 *
	 * @access protected
	 */
	protected function _Handle_BatchInsert()
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
		// Insert objects.
		//
		$ok = $_REQUEST[ kAPI_CONTAINER ]->batchInsert
				( $_REQUEST[ kAPI_DATA_OBJECT ], $options );
		$this->_OffsetManage( kAPI_DATA_STATUS, kAPI_STATUS_NATIVE, $ok );
		
		//
		// Handle response.
		//
		if( (! array_key_exists( kAPI_OPT_NO_RESP, $_REQUEST ))
		 || (! $_REQUEST[ kAPI_OPT_NO_RESP ]) )
		{
			//
			// Copy response.
			//
			$this->offsetSet( kAPI_DATA_RESPONSE, $_REQUEST[ kAPI_DATA_OBJECT ] );
			
			//
			// Serialise response.
			//
			// Note this ugly workflow:
			// I need to do this or else I get this
			// Notice: Indirect modification of overloaded element of MyClass
			// has no effect in /MySource.php
			// Which means that I cannot pass $this[ kAPI_DATA_RESPONSE ] to SerialiseData()
			// or I get the notice and the thing doesn't work.
			//
			$save = $this->offsetGet( kAPI_DATA_RESPONSE );
			CDataType::SerialiseObject( $save );
			$this->offsetSet( kAPI_DATA_RESPONSE, $save );
				
		} // No response option not set.
	
	} // _Handle_BatchInsert.

	 
	/*===================================================================================
	 *	_Handle_Update																	*
	 *==================================================================================*/

	/**
	 * Handle {@link kAPI_OP_UPDATE Update} request.
	 *
	 * This method will handle the {@link kAPI_OP_UPDATE kAPI_OP_UPDATE} request, which
	 * will update the provided object.
	 *
	 * @access protected
	 */
	protected function _Handle_Update()
	{
		//
		// Handle query.
		//
		$query = ( array_key_exists( kAPI_DATA_QUERY, $_REQUEST ) )
				? $_REQUEST[ kAPI_DATA_QUERY ]
				: Array();
		
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
					
					case kAPI_OPT_SINGLE:
						$options[ 'multiple' ] = ( (integer) $value )
											   ? 0
											   : 1;
						break;
				
				} // Parsed option.
			
			} // Iterating options.
			
			//
			// Enforce the 'upsert' option.
			// This operation requires the object to exist, so no.
			//
			$options[ 'upsert' ] = 0;
			
			//
			// Enforce the 'multiple' option.
			// Since the provided option is opposite,
			// we set it to true if missing (dangerous).
			//
			if( ! array_key_exists( 'multiple', $options ) )
				$options[ 'multiple' ] = 1;
		
		} // Iterated options.
		
		//
		// Update object.
		//
		$ok = $_REQUEST[ kAPI_CONTAINER ]->update
				( $query, $_REQUEST[ kAPI_DATA_OBJECT ], $options );
		$this->_OffsetManage( kAPI_DATA_STATUS, kAPI_STATUS_NATIVE, $ok );
		
		//
		// Set operation status.
		//
		if( array_key_exists( kAPI_OPT_SAFE, $options )
		 || array_key_exists( kAPI_OPT_FSYNC, $options ) )
		{
			//
			// Handle errors.
			//
			if( ! $ok[ 'ok' ] )
			{
				//
				// Set severity.
				//
				$this->_OffsetManage( kAPI_DATA_STATUS, kTAG_STATUS, kMESSAGE_TYPE_ERROR );
				
				//
				// Set code.
				//
				$this->_OffsetManage( kAPI_DATA_STATUS, kTAG_CODE, $ok[ 'code' ] );
				
				//
				// Set message.
				//
				$this->_OffsetManage( kAPI_DATA_STATUS, kTAG_DESCRIPTION,
									  array( kTAG_TYPE => kTYPE_STRING,
											 kTAG_LANGUAGE => 'en',
											 kTAG_DATA => $ok[ 'errmsg' ] ) );
			}
			else
				$this->_OffsetManage( kAPI_DATA_STATUS, kAPI_AFFECTED_COUNT, $ok[ 'n' ] );
		
		} // Safe option provided.
		
		//
		// Handle response.
		//
		if( (! array_key_exists( kAPI_OPT_NO_RESP, $_REQUEST ))
		 || (! $_REQUEST[ kAPI_OPT_NO_RESP ]) )
		{
			//
			// Copy response.
			//
			$this->offsetSet( kAPI_DATA_RESPONSE, $_REQUEST[ kAPI_DATA_OBJECT ] );
			
			//
			// Serialise response.
			//
			// Note this ugly workflow:
			// I need to do this or else I get this
			// Notice: Indirect modification of overloaded element of MyClass
			// has no effect in /MySource.php
			// Which means that I cannot pass $this[ kAPI_DATA_RESPONSE ] to SerialiseData()
			// or I get the notice and the thing doesn't work.
			//
			$save = $this->offsetGet( kAPI_DATA_RESPONSE );
			CDataType::SerialiseObject( $save );
			$this->offsetSet( kAPI_DATA_RESPONSE, $save );
				
		} // No response option not set.
	
	} // _Handle_Update.

	 
	/*===================================================================================
	 *	_Handle_Modify																	*
	 *==================================================================================*/

	/**
	 * Handle {@link kAPI_OP_MODIFY Modify} request.
	 *
	 * This method will handle the {@link kAPI_OP_MODIFY kAPI_OP_MODIFY} request, which
	 * will update the provided object.
	 *
	 * The data provided in the {@link kAPI_DATA_OBJECT object} parameter will be scanned
	 * and all <i>NULL</i> values will be set in the <i>$unset</i> array and the non
	 * <i>NULL</i> values in the <i>$set</i> array.
	 *
	 * @access protected
	 */
	protected function _Handle_Modify()
	{
		//
		// Handle query.
		//
		$query = ( array_key_exists( kAPI_DATA_QUERY, $_REQUEST ) )
				? $_REQUEST[ kAPI_DATA_QUERY ]
				: Array();
		
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
					
					case kAPI_OPT_SINGLE:
						$options[ 'multiple' ] = ( (integer) $value )
											   ? 0
											   : 1;
						break;
				
				} // Parsed option.
			
			} // Iterating options.
			
			//
			// Enforce the 'upsert' option.
			// This operation requires the object to exist, so no.
			//
			$options[ 'upsert' ] = 0;
			
			//
			// Enforce the 'multiple' option.
			// Since the provided option is opposite,
			// we set it to true if missing (dangerous).
			//
			if( ! array_key_exists( 'multiple', $options ) )
				$options[ 'multiple' ] = 1;
		
		} // Iterated options.
		
		//
		// Create modifications.
		//
		$mod = Array();
		$set = Array();
		$unset = Array();
		foreach( $_REQUEST[ kAPI_DATA_OBJECT ] as $key => $value )
		{
			if( $value !== NULL )
				$set[ $key ] = $value;
			else
				$unset[ $key ] = TRUE;
		}
		
		//
		// Set modifications.
		//
		if( count( $set ) )
			$mod[ '$set' ] = $set;
		if( count( $unset ) )
			$mod[ '$unset' ] = $unset;
		
		//
		// Modify object.
		//
		$ok = $_REQUEST[ kAPI_CONTAINER ]->update( $query, $mod, $options );
		
		//
		// Set operation status.
		//
		if( array_key_exists( kAPI_OPT_SAFE, $options )
		 || array_key_exists( kAPI_OPT_FSYNC, $options ) )
		{
			//
			// Handle errors.
			//
			if( ! $ok[ 'ok' ] )
			{
				//
				// Set severity.
				//
				$this->_OffsetManage( kAPI_DATA_STATUS, kTAG_STATUS, kMESSAGE_TYPE_ERROR );
				
				//
				// Set code.
				//
				$this->_OffsetManage( kAPI_DATA_STATUS, kTAG_CODE, $ok[ 'code' ] );
				
				//
				// Set message.
				//
				$this->_OffsetManage( kAPI_DATA_STATUS, kTAG_DESCRIPTION,
									  array( kTAG_TYPE => kTYPE_STRING,
											 kTAG_LANGUAGE => 'en',
											 kTAG_DATA => $ok[ 'errmsg' ] ) );
			}
			else
				$this->_OffsetManage( kAPI_DATA_STATUS, kAPI_AFFECTED_COUNT, $ok[ 'n' ] );
		
		} // Safe option provided.
	
	} // _Handle_Modify.

	 
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
	 * @access protected
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
		$ok = $_REQUEST[ kAPI_CONTAINER ]
				->remove( $_REQUEST[ kAPI_DATA_QUERY ], $options );
		
		//
		// Set operation status.
		//
		if( array_key_exists( kAPI_OPT_SAFE, $options )
		 || array_key_exists( kAPI_OPT_FSYNC, $options ) )
		{
			//
			// Handle errors.
			//
			if( ! $ok[ 'ok' ] )
			{
				//
				// Set severity.
				//
				$this->_OffsetManage( kAPI_DATA_STATUS, kTAG_STATUS, kMESSAGE_TYPE_ERROR );
				
				//
				// Set code.
				//
				$this->_OffsetManage( kAPI_DATA_STATUS, kTAG_CODE, $ok[ 'code' ] );
				
				//
				// Set message.
				//
				$this->_OffsetManage( kAPI_DATA_STATUS, kTAG_DESCRIPTION,
									  array( kTAG_TYPE => kTYPE_STRING,
											 kTAG_LANGUAGE => 'en',
											 kTAG_DATA => $ok[ 'errmsg' ] ) );
			}
			else
				$this->_OffsetManage( kAPI_DATA_STATUS, kAPI_AFFECTED_COUNT, $ok[ 'n' ] );
		
		} // Safe option provided.
	
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
	 * @access protected
	 */
	protected function _HandleOptions( &$theResult, $theOptions )							{}

	 

} // class CMongoDataWrapper.


?>
