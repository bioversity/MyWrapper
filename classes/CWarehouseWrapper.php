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
 * Ontology edge definitions.
 *
 * This include file contains the ontology edge class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."COntologyEdge.php" );

/**
 * Local definitions.
 *
 * This include file contains all local definitions to this class.
 */
require_once( kPATH_LIBRARY_SOURCE."CWarehouseWrapper.inc.php" );

use Everyman\Neo4j\Transport,
	Everyman\Neo4j\Client,
	Everyman\Neo4j\Index\NodeIndex,
	Everyman\Neo4j\Index\RelationshipIndex,
	Everyman\Neo4j\Index\NodeFulltextIndex,
	Everyman\Neo4j\Node,
	Everyman\Neo4j\Relationship,
	Everyman\Neo4j\Batch;

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
 *	<li><i>{@link kAPI_OP_GET_TERMS kAPI_OP_GET_TERMS}</i>: Get terms, this operation will
 *		return the list of ontology {@link COntologyTerm terms} matching the identifiers
 *		provided in the {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} option. This
 *		list is expected to hold {@link COntologyTerm term} {@link kTAG_GID identifiers}.
 *	<li><i>{@link kAPI_OP_GET_NODES kAPI_OP_GET_NODES}</i>: Get terms, this operation will
 *		return the list of ontology {@link COntologyTerm terms} matching the identifiers
 *		provided in the {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} option. This
 *		list is expected to hold {@link COntologyTerm term} {@link kTAG_GID identifiers}.
 * </ul>
 *
 * The class adds the following options:
 *
 * <ul>
 *	<li><i>{@link kAPI_OPT_USER_CODE kAPI_OPT_USER_CODE}</i>: User code, this option expects
 *		a string corresponding to the user {@link CEntity::Code() code}.
 *	<li><i>{@link kAPI_OPT_USER_PASS kAPI_OPT_USER_PASS}</i>: User code, this option expects
 *		a string corresponding to the user {@link CUser::Password() password}.
 *	<li><i>{@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS}</i>: Identifiers list, this
 *		option expects an array of object identifiers. The actual type of these elements is
 *		determined by the operation.
 * </ul>
 *
 *	@package	MyWrapper
 *	@subpackage	Wrappers
 */
class CWarehouseWrapper extends CMongoDataWrapper
{
		

/*=======================================================================================
 *																						*
 *							PROTECTED INITIALISATION INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_InitOptions																	*
	 *==================================================================================*/

	/**
	 * Initialise options.
	 *
	 * This method is responsible for parsing and setting all default and provided options,
	 * derived classes should overload this method to handle custom options.
	 *
	 * In this class we enforce {@link kAPI_DATA_PAGING paging} options if the
	 * @link kAPI_OPT_IDENTIFIERS identifiers} list was not provided and the operation is
	 * get {@link kAPI_OP_GET_TERMS terms}.
	 *
	 * @access private
	 *
	 * @see kAPI_DATA_REQUEST kAPI_DATA_TIMING
	 */
	protected function _InitOptions()
	{
		//
		// Check operation.
		//
		if( array_key_exists( kAPI_OPERATION, $_REQUEST ) )
		{
			//
			// Parse by operation.
			//
			switch( $_REQUEST[ kAPI_OPERATION ] )
			{
				//
				// Enforce paging.
				//
				case kAPI_OP_GET_TERMS:
				case kAPI_OP_GET_NODES:
				case kAPI_OP_GET_EDGES:
					//
					// Check if identifiers are missig.
					//
					if( ! array_key_exists( kAPI_OPT_IDENTIFIERS, $_REQUEST ) )
					{
						//
						// Enforce start if missing.
						//
						if( ! array_key_exists( kAPI_PAGE_START, $_REQUEST ) )
							$_REQUEST[ kAPI_PAGE_START ] = 0;
						//
						// Enforce limit if missing.
						//
						if( ! array_key_exists( kAPI_PAGE_LIMIT, $_REQUEST ) )
							$_REQUEST[ kAPI_PAGE_LIMIT ] = kDEFAULT_LIMITS;
					
					} // Missing identifiers.

					//
					// Init container name.
					//
					if( ! array_key_exists( kAPI_CONTAINER, $_REQUEST ) )
					{
						//
						// Reparse operation.
						//
						switch( $_REQUEST[ kAPI_OPERATION ] )
						{
							case kAPI_OP_GET_TERMS:
								$_REQUEST[ kAPI_CONTAINER ] = kDEFAULT_CNT_TERMS;
								break;
						
							case kAPI_OP_GET_NODES:
								$_REQUEST[ kAPI_CONTAINER ] = kDEFAULT_CNT_NODES;
								break;
						
							case kAPI_OP_GET_EDGES:
								$_REQUEST[ kAPI_CONTAINER ] = kDEFAULT_CNT_EDGES;
								break;
						
						} // Reparsing operation.
					
					} // Missing container reference.
					
					break;
				
				case kAPI_OP_GET_ROOTS:
					//
					// Check for container.
					//
					if( (! array_key_exists( kAPI_CONTAINER, $_REQUEST ))
					 || (! strlen( $_REQUEST[ kAPI_CONTAINER ] )) )
						$_REQUEST[ kAPI_CONTAINER ] = kDEFAULT_CNT_NODES;
					
					break;
			
			} // Parsed operation.
		
		} // Has operation.
		
		//
		// Call parent method.
		//
		parent::_InitOptions();
	
	} // _InitOptions.

	 
	/*===================================================================================
	 *	_InitResources																	*
	 *==================================================================================*/

	/**
	 * Initialise resources.
	 *
	 * In this class we instantiate the Neo4j client.
	 *
	 * @access protected
	 */
	protected function _InitResources()
	{
		//
		// Set Neo4j connection.
		//
		$_SESSION[ kSESSION_NEO4J ] = new Everyman\Neo4j\Client( 'localhost', 7474 );
//		$_SESSION[ kSESSION_NEO4J ]->getTransport()
//								   ->useHttps()->setAuth( 'username', 'password' );
		
		//
		// Call parent method.
		//
		parent::_InitResources();
	
	} // _InitResources.

		

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
	 * @access protected
	 *
	 * @uses _ParseUserCode()
	 * @uses _ParseUserPass()
	 */
	protected function _ParseRequest()
	{
		//
		// Handle parameters.
		//
		$this->_ParseIdentifiers();
		$this->_ParsePredicates();
		$this->_ParseDirection();
		$this->_ParseUserCode();
		$this->_ParseUserPass();
		$this->_ParseLevels();
	
		//
		// Call parent method.
		//
		parent::_ParseRequest();
	
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
	 *
	 * @uses _FormatIdentifiers()
	 */
	protected function _FormatRequest()	
	{
		//
		// Call parent method.
		//
		parent::_FormatRequest();
		
		//
		// Generate query.
		//
		$this->_FormatIdentifiers();
		$this->_FormatPredicates();
	
	} // _FormatRequest.

		

/*=======================================================================================
 *																						*
 *								PROTECTED PARSING UTILITIES								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_ParseIdentifiers																*
	 *==================================================================================*/

	/**
	 * Parse identifiers.
	 *
	 * This method will parse the user {@link kAPI_OPT_IDENTIFIERS identifiers} parameter.
	 *
	 * @access protected
	 *
	 * @see kAPI_DATA_REQUEST kAPI_OPT_IDENTIFIERS
	 */
	protected function _ParseIdentifiers()
	{
		//
		// Handle identifiers.
		//
		if( array_key_exists( kAPI_OPT_IDENTIFIERS, $_REQUEST ) )
		{
			//
			// Add to request.
			//
			if( $this->offsetExists( kAPI_DATA_REQUEST ) )
				$this->_OffsetManage
					( kAPI_DATA_REQUEST, kAPI_OPT_IDENTIFIERS,
					  $_REQUEST[ kAPI_OPT_IDENTIFIERS ] );
		
		} // Has identifiers list.
	
	} // _ParseIdentifiers.

	 
	/*===================================================================================
	 *	_ParsePredicates																*
	 *==================================================================================*/

	/**
	 * Parse predicates.
	 *
	 * This method will parse the user {@link kAPI_OPT_PREDICATES predicates} parameter.
	 *
	 * @access protected
	 *
	 * @see kAPI_DATA_REQUEST kAPI_OPT_PREDICATES
	 */
	protected function _ParsePredicates()
	{
		//
		// Handle predicates.
		//
		if( array_key_exists( kAPI_OPT_PREDICATES, $_REQUEST ) )
		{
			//
			// Add to request.
			//
			if( $this->offsetExists( kAPI_DATA_REQUEST ) )
				$this->_OffsetManage
					( kAPI_DATA_REQUEST, kAPI_OPT_PREDICATES,
					  $_REQUEST[ kAPI_OPT_PREDICATES ] );
		
		} // Has predicates list.
	
	} // _ParsePredicates.

	 
	/*===================================================================================
	 *	_ParseDirection																	*
	 *==================================================================================*/

	/**
	 * Parse direction.
	 *
	 * This method will parse the relations {@link kAPI_OPT_DIRECTION direction} parameter.
	 *
	 * @access protected
	 *
	 * @see kAPI_DATA_REQUEST kAPI_OPT_DIRECTION
	 */
	protected function _ParseDirection()
	{
		//
		// Handle direction.
		//
		if( array_key_exists( kAPI_OPT_DIRECTION, $_REQUEST ) )
		{
			//
			// Add to request.
			//
			if( $this->offsetExists( kAPI_DATA_REQUEST ) )
				$this->_OffsetManage
					( kAPI_DATA_REQUEST, kAPI_OPT_DIRECTION,
					  $_REQUEST[ kAPI_OPT_DIRECTION ] );
		
		} // Has direction parameter.
	
	} // _ParseDirection.

		
	/*===================================================================================
	 *	_ParseUserCode																	*
	 *==================================================================================*/

	/**
	 * Parse user code.
	 *
	 * This method will parse the user {@link kAPI_OPT_USER_CODE code} parameter.
	 *
	 * @access protected
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
	 * @access protected
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

	 
	/*===================================================================================
	 *	_ParseLevels																	*
	 *==================================================================================*/

	/**
	 * Parse level.
	 *
	 * This method will parse the relations {@link kAPI_OPT_LEVELS levels} parameter.
	 *
	 * @access protected
	 *
	 * @see kAPI_DATA_REQUEST kAPI_OPT_LEVELS
	 */
	protected function _ParseLevels()
	{
		//
		// Handle levels.
		//
		if( array_key_exists( kAPI_OPT_LEVELS, $_REQUEST ) )
		{
			//
			// Add to request.
			//
			if( $this->offsetExists( kAPI_DATA_REQUEST ) )
				$this->_OffsetManage
					( kAPI_DATA_REQUEST, kAPI_OPT_LEVELS,
					  $_REQUEST[ kAPI_OPT_LEVELS ] );
		
		} // Has levels parameter.
	
	} // _ParseLevels.

	 

/*=======================================================================================
 *																						*
 *								PROTECTED FORMAT INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_FormatQuery																	*
	 *==================================================================================*/

	/**
	 * Format query.
	 *
	 * This method will format the request query.
	 *
	 * In this class we enforce the root selector for the
	 * {@link kAPI_OP_GET_ROOTS kAPI_OP_GET_ROOTS} service.
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
		// Handle get roots workflow.
		//
		if( $_REQUEST[ kAPI_OPERATION ] == kAPI_OP_GET_ROOTS )
		{
			//
			// Init query.
			//
			if( ! array_key_exists( kAPI_DATA_QUERY, $_REQUEST ) )
				$_REQUEST[ kAPI_DATA_QUERY ] = new CMongoQuery();
			
			//
			// Add root selector.
			//
			$_REQUEST[ kAPI_DATA_QUERY ]
				->AppendStatement(
					CQueryStatement::Equals(
						kTAG_DATA.'.'.kTAG_KIND, kTYPE_ROOT ),
					kOPERATOR_AND );
		
		} // Get roots service.
	
	} // _FormatQuery.

	 
	/*===================================================================================
	 *	_FormatIdentifiers																*
	 *==================================================================================*/

	/**
	 * This method will format the request identifiers list.
	 *
	 * In this class we handle the terms list {@link kAPI_OP_GET_TERMS operation} by
	 * creating a query and using the {@link kAPI_OP_GET GET} handler, in this method we
	 * create the {@link CMongoQuery query}.
	 *
	 * @access protected
	 *
	 * @uses _DecodeParameter()
	 *
	 * @see kAPI_OPT_IDENTIFIERS
	 */
	protected function _FormatIdentifiers()
	{
		//
		// Handle identifiers.
		//
		if( array_key_exists( kAPI_OPT_IDENTIFIERS, $_REQUEST ) )
		{
			//
			// Decode parameter.
			//
			$this->_DecodeParameter( kAPI_OPT_IDENTIFIERS );
			
			//
			// Handle request.
			//
			if( array_key_exists( kAPI_OPERATION, $_REQUEST ) )
			{
				//
				// Parse by operation.
				//
				switch( $_REQUEST[ kAPI_OPERATION ] )
				{
					//
					// Handle term references.
					//
					case kAPI_OP_GET_TERMS:
						//
						// Hash identifiers.
						//
						$identifiers = Array();
						foreach( $_REQUEST[ kAPI_OPT_IDENTIFIERS ] as $identifier )
						{
							//
							// Hash only if not an array.
							//
							if( ! is_array( $identifier ) )
								$identifiers[] = COntologyTerm::HashIndex( $identifier );
						}
			
						//
						// Convert to query.
						//
						$_REQUEST[ kAPI_DATA_QUERY ] = new CMongoQuery();
						$_REQUEST[ kAPI_DATA_QUERY ]->AppendStatement(
														CQueryStatement::Member(
															kTAG_LID,
															$identifiers,
															kTYPE_BINARY ),
														kOPERATOR_AND );
						break;
				
					//
					// Handle node references.
					//
					case kAPI_OP_GET_NODES:
					case kAPI_OP_GET_EDGES:
						//
						// Hash identifiers.
						//
						$identifiers = Array();
						foreach( $_REQUEST[ kAPI_OPT_IDENTIFIERS ] as $identifier )
						{
							//
							// Add only if not an array.
							//
							if( ! is_array( $identifier ) )
								$identifiers[] = $identifier;
						}
			
						//
						// Convert to query.
						//
						$_REQUEST[ kAPI_DATA_QUERY ] = new CMongoQuery();
						$_REQUEST[ kAPI_DATA_QUERY ]->AppendStatement(
														CQueryStatement::Member(
															kTAG_LID,
															$identifiers,
															kTYPE_INT64 ),
														kOPERATOR_AND );
						break;
				
				} // Parsed by request.
			
			} // Provided operation.
		}
	
	} // _FormatIdentifiers.

	 
	/*===================================================================================
	 *	_FormatPredicates																*
	 *==================================================================================*/

	/**
	 * This method will format the request predicates list.
	 *
	 * In this class we {@link _DecodeParameter() decode} the parameter.
	 *
	 * @access protected
	 *
	 * @uses _DecodeParameter()
	 *
	 * @see kAPI_OPT_PREDICATES
	 */
	protected function _FormatPredicates()
	{
		//
		// Handle identifiers.
		//
		if( array_key_exists( kAPI_OPT_PREDICATES, $_REQUEST ) )
			$this->_DecodeParameter( kAPI_OPT_PREDICATES );
	
	} // _FormatPredicates.

		

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
				
			case kAPI_OP_GET_RELS:
				//
				// Check relations level.
				//
				if( ! array_key_exists( kAPI_OPT_LEVELS, $_REQUEST ) )
					$_REQUEST[ kAPI_OPT_LEVELS ] = 1;
				
				//
				// Check relation direction.
				//
				if( array_key_exists( kAPI_OPT_DIRECTION, $_REQUEST ) )
				{
					switch( $_REQUEST[ kAPI_OPT_DIRECTION ] )
					{
						case kAPI_DIRECTION_IN:
						case kAPI_DIRECTION_OUT:
						case kAPI_DIRECTION_ALL:
							break;
						
						default:
							throw new CException
								( "Invalid relationship direction tag",
								  kERROR_INVALID_PARAMETER,
								  kMESSAGE_TYPE_ERROR,
								  array( 'Direction'
								  		=> $_REQUEST[ kAPI_OPT_DIRECTION ] ) );	// !@! ==>
					}
				}
				
				//
				// Check for container.
				//
				if( (! array_key_exists( kAPI_CONTAINER, $_REQUEST ))
				 || (! strlen( $_REQUEST[ kAPI_CONTAINER ] )) )
					$_REQUEST[ kAPI_CONTAINER ] = kDEFAULT_CNT_TERMS;
				
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
				
				break;
				
			case kAPI_OP_GET_ROOTS:
				
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

			case kAPI_OP_GET_TERMS:
			case kAPI_OP_GET_NODES:
			case kAPI_OP_GET_EDGES:
				
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

			case kAPI_OP_GET_TERMS:
				$this->_Handle_GetTerms();
				break;

			case kAPI_OP_GET_NODES:
				$this->_Handle_GetNodes();
				break;

			case kAPI_OP_GET_EDGES:
				$this->_Handle_GetEdges();
				break;

			case kAPI_OP_GET_RELS:
				$this->_Handle_GetRelations();
				break;

			case kAPI_OP_GET_ROOTS:
				$this->_Handle_GetNodes();
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
		$id = CUser::HashIndex( $_REQUEST[ kAPI_OPT_USER_CODE ] );
		
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
							  array( kTAG_TYPE => kTYPE_STRING,
									 kTAG_LANGUAGE => 'en',
									 kTAG_DATA => 'User not found.' ) );
	
	} // _Handle_Login.

	 
	/*===================================================================================
	 *	_Handle_GetTerms																*
	 *==================================================================================*/

	/**
	 * Handle {@link kAPI_OP_GET_TERMS Get-terms} request.
	 *
	 * This method will handle the {@link kAPI_OP_GET_TERMS kAPI_OP_GET_TERMS} request,
	 * which is equivalent to the {@link _Handle_Get() _Handle_Get} method, wit the only
	 * difference being that each found element is here indexed by the term global
	 * {@link kTAG_GID identifier}.
	 *
	 * The only reason to re-develop this method is to provide a consistent view of terms in
	 * all calls.
	 *
	 * @access protected
	 */
	protected function _Handle_GetTerms()
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
		// Fix fields.
		// Need to add GID, or we will not be able to index results array.
		//
		$added = FALSE;
		if( count( $fields )
		 && (! array_key_exists( kTAG_GID, $fields )) )
		{
			$fields[ kTAG_GID ] = TRUE;
			$added = TRUE;
		}
		
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
					// Collect result.
					//
					$result = Array();
					foreach( $cursor as $element )
					{
						//
						// Save index.
						//
						$idx = $element[ kTAG_GID ];
						
						//
						// Remove GID if necessary.
						//
						if( $added )
							unset( $element[ kTAG_GID ] );
						
						//
						// Add element.
						//
						$result[ $idx ] = $element;
					
					} // Loading results.
					
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
					// Set response.
					//
					$this->offsetSet( kAPI_DATA_RESPONSE, $result );
				
				} // No response option not set.
				
			} // Has results.
		
		} // Not COUNT option.
	
	} // _Handle_GetTerms.

	 
	/*===================================================================================
	 *	_Handle_GetNodes																*
	 *==================================================================================*/

	/**
	 * Handle get nodes request.
	 *
	 * This method will process the query provided in the
	 * {@link kAPI_DATA_QUERY kAPI_DATA_QUERY} parameter and return a result structured as
	 * follows:
	 *
	 * <ul>
	 *	<li><i>{@link kAPI_RESPONSE_NODES kAPI_RESPONSE_NODES}</i>: The list of found nodes
	 *		as follows:
	 *	 <ul>
	 *		<li><i>Key</i>: The node ID.
	 *		<li><i>Value</i>: The node properties.
	 *	 </ul>
	 *	<li><i>{@link kAPI_RESPONSE_TERMS kAPI_RESPONSE_TERMS}</i>: The list of terms
	 *		related to the list of found nodes as follows:
	 *	 <ul>
	 *		<li><i>Key</i>: The {@link COntologyTerm term} global
	 *			{@link kTAG_GID identifier}.
	 *		<li><i>Value</i>: The contents of the {@link COntologyTerm term}.
	 *	 </ul>
	 * </ul>
	 *
	 * @access protected
	 */
	protected function _Handle_GetNodes()
	{
		//
		// Init local storage.
		//
		$container[ kTAG_NODE ]
			= new CMongoContainer
					( $_REQUEST[ kAPI_DATABASE ]->
						selectCollection( kDEFAULT_CNT_NODES ) );
		$container[ kTAG_TERM ]
			= new CMongoContainer
					( $_REQUEST[ kAPI_DATABASE ]->
						selectCollection( kDEFAULT_CNT_TERMS ) );
		
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
		// Get nodes cursor.
		// Note that we do not use fields on nodes, but on terms yes.
		//
		$cursor = $container[ kTAG_NODE ]->Container()->find( $query );
		
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
					// Init local storage.
					//
					$terms = Array();
					$response = array( kAPI_RESPONSE_TERMS => Array(),
									   kAPI_RESPONSE_NODES => Array() );
					$ref_term = & $response[ kAPI_RESPONSE_TERMS ];
					$ref_node = & $response[ kAPI_RESPONSE_NODES ];
					
					//
					// Load nodes.
					//
					foreach( $cursor as $record )
					{
						//
						// Load node.
						//
						$ref_node[ $record[ kTAG_LID ] ] = $record[ kTAG_DATA ];
						
						//
						// Load term.
						//
						if( array_key_exists( kTAG_TERM, $record[ kTAG_DATA ] ) )
						{
							//
							// Normalise identifier.
							//
							$key = $record[ kTAG_DATA ][ kTAG_TERM ];
							if( ! array_key_exists( $key, $terms ) )
							{
								//
								// Hash index.
								//
								$value = COntologyTerm::HashIndex( $key );
								
								//
								// Normalise for Mongo.
								//
								$container[ kTAG_TERM ]->UnserialiseData( $value );
								
								//
								// Add to set.
								//
								$terms[ $key ] = $value;
							
							} // New term.
						
						} // Has term reference.
					
					} // Loading nodes.
					
					//
					// Fix fields.
					// Need to add GID, or we will not be able to index results array.
					//
					$added = FALSE;
					if( count( $fields )
					 && (! array_key_exists( kTAG_GID, $fields )) )
					{
						$fields[ kTAG_GID ] = TRUE;
						$added = TRUE;
					}
					
					//
					// Load terms.
					//
					$query = array( kTAG_LID => array( '$in' => array_values( $terms ) ) );
					$cursor = $container[ kTAG_TERM ]->Container()->find( $query, $fields );
					foreach( $cursor as $record )
					{
						if( array_key_exists( kTAG_GID, $record ) )
						{
							$id = $record[ kTAG_GID ];
							if( $added )
								unset( $record[ kTAG_GID ] );
							$ref_term[ $id ] = $record;
						}
					}
					
					//
					// Handle options.
					//
					if( array_key_exists( kAPI_DATA_OPTIONS, $_REQUEST ) )
						$this->_HandleOptions( $result );
		
					//
					// Serialise response.
					//
					CDataType::SerialiseObject( $response );
					
					//
					// Set response.
					//
					$this->offsetSet( kAPI_DATA_RESPONSE, $response );
				
				} // No response option not set.
				
			} // Has results.
		
		} // Not COUNT option.
	
	} // _Handle_GetNodes.

	 
	/*===================================================================================
	 *	_Handle_GetEdges																*
	 *==================================================================================*/

	/**
	 * Handle get edges request.
	 *
	 * This method will process the query provided in the
	 * {@link kAPI_DATA_QUERY kAPI_DATA_QUERY} parameter and return a result structured as
	 * follows:
	 *
	 * <ul>
	 *	<li><i>{@link kAPI_RESPONSE_EDGES kAPI_RESPONSE_EDGES}</i>: The list of edges as an
	 *		array structured as follows:
	 *	 <ul>
	 *		<li><i>key</i>: The edge identifier.
	 *		<li><i>Value</i>: An array structured as follows:
	 *		 <ul>
	 *			<li><i>{@link kAPI_RESPONSE_SUBJECT kAPI_RESPONSE_SUBJECT}</i>: The subject
	 *				{@link COntologyNode node} ID.
	 *			<li><i>{@link kAPI_RESPONSE_PREDICATE kAPI_RESPONSE_PREDICATE}</i>: The
	 *				predicate {@link COntologyTerm term} {@link kTAG_GID identifier}.
	 *			<li><i>{@link kAPI_RESPONSE_OBJECT kAPI_RESPONSE_OBJECT}</i>: The object
	 *				{@link COntologyNode node} ID.
	 *		 </ul>
	 *	 </ul>
	 *	<li><i>{@link kAPI_RESPONSE_NODES kAPI_RESPONSE_NODES}</i>: The list of
	 *		{@link kAPI_RESPONSE_SUBJECT subject} and {@link kAPI_RESPONSE_OBJECT object}
	 *		found nodes as follows:
	 *	 <ul>
	 *		<li><i>Key</i>: The node ID.
	 *		<li><i>Value</i>: The node properties.
	 *	 </ul>
	 *	<li><i>{@link kAPI_RESPONSE_TERMS kAPI_RESPONSE_TERMS}</i>: The list of terms
	 *		related to the list of found nodes and to the edge predicate as follows:
	 *	 <ul>
	 *		<li><i>Key</i>: The {@link COntologyTerm term} global
	 *			{@link kTAG_GID identifier}.
	 *		<li><i>Value</i>: The contents of the {@link COntologyTerm term}.
	 *	 </ul>
	 * </ul>
	 *
	 * @access protected
	 */
	protected function _Handle_GetEdges()
	{
		//
		// Init local storage.
		//
		$container = Array();
		$container[ kTAG_EDGE ]
			= new CMongoContainer
					( $_REQUEST[ kAPI_DATABASE ]->
						selectCollection( kDEFAULT_CNT_EDGES ) );
		$container[ kTAG_NODE ]
			= new CMongoContainer
					( $_REQUEST[ kAPI_DATABASE ]->
						selectCollection( kDEFAULT_CNT_NODES ) );
		$container[ kTAG_TERM ]
			= new CMongoContainer
					( $_REQUEST[ kAPI_DATABASE ]->
						selectCollection( kDEFAULT_CNT_TERMS ) );
		
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
		// Get predicates.
		//
		$predicates = ( array_key_exists( kAPI_OPT_PREDICATES, $_REQUEST ) )
					? $_REQUEST[ kAPI_OPT_PREDICATES ]
					: Array();
		
		//
		// Handle sort.
		//
		$sort = ( array_key_exists( kAPI_DATA_SORT, $_REQUEST ) )
			  ? $_REQUEST[ kAPI_DATA_SORT ]
			  : Array();
		
		//
		// Get edges cursor.
		// Note that we do not use fields on edges, but on terms yes.
		//
		$cursor = $container[ kTAG_EDGE ]->Container()->find( $query );
		
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
					// Init local storage.
					//
					$terms = $nodes = Array();
					$response = array( kAPI_RESPONSE_TERMS => Array(),
									   kAPI_RESPONSE_NODES => Array(),
									   kAPI_RESPONSE_EDGES => Array() );
					
					//
					// Init local references.
					//
					$ref_term = & $response[ kAPI_RESPONSE_TERMS ];
					$ref_node = & $response[ kAPI_RESPONSE_NODES ];
					$ref_edge = & $response[ kAPI_RESPONSE_EDGES ];
					
					//
					// Load edges.
					//
					foreach( $cursor as $record )
					{
						//
						// Filter predicates.
						//
						if( (! count( $predicates ))
						 || in_array( $theEdge->getType(), $predicates ) )
						{
							//
							// Get edge elements.
							//
							$subject = $record[ kTAG_SUBJECT ][ kTAG_NODE ];
							$predicate = $record[ kTAG_PREDICATE ][ kTAG_TERM ];
							$object = $record[ kTAG_OBJECT ][ kTAG_NODE ];
							if( array_key_exists( kTAG_DATA, $record ) )
								$data = $record[ kTAG_DATA ];
							
							//
							// Load edge.
							//
							$ref_edge[ $record[ kTAG_PREDICATE ][ kTAG_NODE ] ]
								= array( kAPI_RESPONSE_SUBJECT => $subject,
										 kAPI_RESPONSE_PREDICATE => $predicate,
										 kAPI_RESPONSE_OBJECT => $object,
										 kTAG_DATA => $data );
							
							//
							// Load subject.
							//
							$term = $record[ kTAG_SUBJECT ][ kTAG_TERM ];
							$nodes[ $subject ] = $subject;
							if( ! array_key_exists( $term, $terms ) )
							{
								//
								// Hash index.
								//
								$value = COntologyTerm::HashIndex( $term );
								
								//
								// Normalise for Mongo.
								//
								$container[ kTAG_TERM ]->UnserialiseData( $value );
								
								//
								// Add to set.
								//
								$terms[ $term ] = $value;
							
							} // New subject term.
							
							//
							// Load predicate.
							//
							if( ! array_key_exists( $predicate, $terms ) )
							{
								//
								// Hash index.
								//
								$value = COntologyTerm::HashIndex( $predicate );
								
								//
								// Normalise for Mongo.
								//
								$container[ kTAG_TERM ]->UnserialiseData( $value );
								
								//
								// Add to set.
								//
								$terms[ $predicate ] = $value;
							
							} // New predicate term.
							
							//
							// Load object.
							//
							$term = $record[ kTAG_OBJECT ][ kTAG_TERM ];
							$nodes[ $object ] = $object;
							if( ! array_key_exists( $term, $terms ) )
							{
								//
								// Hash index.
								//
								$value = COntologyTerm::HashIndex( $term );
								
								//
								// Normalise for Mongo.
								//
								$container[ kTAG_TERM ]->UnserialiseData( $value );
								
								//
								// Add to set.
								//
								$terms[ $term ] = $value;
							
							} // New object term.
							
							//
							// Load term.
							//
							if( array_key_exists( kTAG_TERM, $record[ kTAG_DATA ] ) )
							{
								//
								// Normalise identifier.
								//
								$key = $record[ kTAG_DATA ][ kTAG_TERM ];
								if( ! array_key_exists( $key, $terms ) )
								{
									//
									// Hash index.
									//
									$value = COntologyTerm::HashIndex( $key );
									
									//
									// Normalise for Mongo.
									//
									$container[ kTAG_TERM ]->UnserialiseData( $value );
									
									//
									// Add to set.
									//
									$terms[ $key ] = $value;
								
								} // New term.
							
							} // Has term reference.
						
						} // Missing or matched predicates.
					
					} // Loading edges.
					
					//
					// Load nodes.
					//
					if( count( $nodes ) )
					{
						//
						// Set query.
						//
						$query = Array();
						$query[ kTAG_LID ] = array( '$in' => array_values( $nodes ) );
						
						//
						// Make query.
						//
						$cursor = $container[ kTAG_NODE ]->Container()->find( $query );
						
						//
						// Load found nodes.
						//
						foreach( $cursor as $record )
							$ref_node[ $record[ kTAG_LID ] ] = $record[ kTAG_DATA ];					
					
					} // Found nodes.
					
					//
					// Load terms.
					//
					if( count( $terms ) )
					{
						//
						// Fix fields.
						// Need to add GID, or we will not be able to index results array.
						//
						$added = FALSE;
						if( count( $fields )
						 && (! array_key_exists( kTAG_GID, $fields )) )
						{
							$fields[ kTAG_GID ] = TRUE;
							$added = TRUE;
						}
						
						//
						// Set query.
						//
						$query = Array();
						$query[ kTAG_LID ] = array( '$in' => array_values( $terms ) );
						
						//
						// Make query.
						//
						$cursor = $container[ kTAG_TERM ]->Container()
															->find( $query, $fields );
						
						//
						// Load terms.
						//
						foreach( $cursor as $record )
						{
							if( array_key_exists( kTAG_GID, $record ) )
							{
								$id = $record[ kTAG_GID ];
								if( $added )
									unset( $record[ kTAG_GID ] );
								$ref_term[ $id ] = $record;
							}
						}
					
					} // Found terms.
					
					//
					// Handle options.
					//
					if( array_key_exists( kAPI_DATA_OPTIONS, $_REQUEST ) )
						$this->_HandleOptions( $result );
		
					//
					// Serialise response.
					//
					CDataType::SerialiseObject( $response );
					
					//
					// Set response.
					//
					$this->offsetSet( kAPI_DATA_RESPONSE, $response );
				
				} // No response option not set.
				
			} // Has results.
		
		} // Not COUNT option.
	
	} // _Handle_GetEdges.

	 
	/*===================================================================================
	 *	_Handle_GetRelations															*
	 *==================================================================================*/

	/**
	 * Handle {@link kAPI_OP_GET_RELS get-relations} request.
	 *
	 * This method will return the same structure as the
	 * {@link kAPI_OP_GET_EDGES kAPI_OP_GET_EDGES} service:
	 *
	 * <ul>
	 *	<li><i>{@link kAPI_RESPONSE_EDGES kAPI_RESPONSE_EDGES}</i>: The list of edges as an
	 *		array structured as follows:
	 *	 <ul>
	 *		<li><i>key</i>: The edge identifier.
	 *		<li><i>Value</i>: An array structured as follows:
	 *		 <ul>
	 *			<li><i>{@link kAPI_RESPONSE_SUBJECT kAPI_RESPONSE_SUBJECT}</i>: The subject
	 *				{@link COntologyNode node} ID.
	 *			<li><i>{@link kAPI_RESPONSE_PREDICATE kAPI_RESPONSE_PREDICATE}</i>: The
	 *				predicate {@link COntologyTerm term} {@link kTAG_GID identifier}.
	 *			<li><i>{@link kAPI_RESPONSE_OBJECT kAPI_RESPONSE_OBJECT}</i>: The object
	 *				{@link COntologyNode node} ID.
	 *		 </ul>
	 *	 </ul>
	 *	<li><i>{@link kAPI_RESPONSE_NODES kAPI_RESPONSE_NODES}</i>: The list of
	 *		{@link kAPI_RESPONSE_SUBJECT subject} and {@link kAPI_RESPONSE_OBJECT object}
	 *		found nodes as follows:
	 *	 <ul>
	 *		<li><i>Key</i>: The node ID.
	 *		<li><i>Value</i>: The node properties.
	 *	 </ul>
	 *	<li><i>{@link kAPI_RESPONSE_TERMS kAPI_RESPONSE_TERMS}</i>: The list of terms
	 *		related to the list of found nodes and to the edge predicate as follows:
	 *	 <ul>
	 *		<li><i>Key</i>: The {@link COntologyTerm term} global
	 *			{@link kTAG_GID identifier}.
	 *		<li><i>Value</i>: The contents of the {@link COntologyTerm term}.
	 *	 </ul>
	 * </ul>
	 *
	 * Depending on the value of the {@link kAPI_OPT_DIRECTION kAPI_OPT_DIRECTION}
	 * parameter:
	 *
	 * <ul>
	 *	<li><i>{@link kAPI_DIRECTION_IN kAPI_DIRECTION_IN}</i>: The service will return all
	 *		{@link COntologyEdge edges} that point to the {@link COntologyNode nodes}
	 *		provided in the {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter.
	 *	<li><i>{@link kAPI_DIRECTION_OUT kAPI_DIRECTION_OUT}</i>: The service will return
	 *		all {@link COntologyEdge edges} pointing from the {@link COntologyNode nodes}
	 *		provided in the {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter.
	 *	<li><i>{@link kAPI_DIRECTION_ALL kAPI_DIRECTION_ALL}</i>: The service will return
	 *		all {@link COntologyEdge edges} connected in any way to the
	 *		{@link COntologyNode nodes} provided in the
	 *		{@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter.
	 * </ul>
	 *
	 * The service also expects a {@link kAPI_OPT_LEVELS kAPI_OPT_LEVELS}parameter, a signed
	 * integer, that indicates how many levels to recurse the graph traversal, if this parameter
	 * is not provided, it will default to 1 level; to traverse all levels this parameter should
	 * be set to a negative number; a level of 0 will only return the list of involved nodes and
	 * terms.
	 *
	 * If the {@link kAPI_OPT_PREDICATES kAPI_OPT_PREDICATES} parameter was provided, only
	 * those {@link COntologyEdge edges} whose type matches any of the predicate
	 * {@link COntologyTerm term} identifiers provided in that parameter will be selected.
	 *
	 * If the {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter was not provided,
	 * the method will return the above structure with no content.
	 *
	 * <b><i>Note: this method handles the case in which the
	 * {@link kAPI_OPT_DIRECTION direction} is not provided: in that case it will treat the
	 * list of {@link kAPI_OPT_IDENTIFIERS identifiers} as a list of edge node IDs and will
	 * work as the {@link kAPI_OP_GET_EDGES kAPI_OP_GET_EDGES} service</i></b>.
	 *
	 * @access protected
	 */
	protected function _Handle_GetRelations()
	{
		//
		// Init local storage.
		//
		$count = 0;
		
		//
		// Handle identifiers.
		//
		if( array_key_exists( kAPI_OPT_IDENTIFIERS, $_REQUEST ) )
		{
			//
			// Init container.
			//
			$container = array
			(
				kTAG_TERM => new CMongoContainer( $_REQUEST[ kAPI_CONTAINER ] ),
				kTAG_NODE => $_SESSION[ kSESSION_NEO4J ]
			);
			
			//
			// Init response.
			//
			$response = array( kAPI_RESPONSE_TERMS => Array(),
							   kAPI_RESPONSE_NODES => Array(),
							   kAPI_RESPONSE_EDGES => Array() );
			
			//
			// Init response references.
			//
			$ref_term = & $response[ kAPI_RESPONSE_TERMS ];
			$ref_node = & $response[ kAPI_RESPONSE_NODES ];
			$ref_edge = & $response[ kAPI_RESPONSE_EDGES ];
			
			//
			// Init local storage.
			//
			$terms = Array();
			
			//
			// Get predicates.
			//
			$predicates = ( array_key_exists( kAPI_OPT_PREDICATES, $_REQUEST ) )
						? $_REQUEST[ kAPI_OPT_PREDICATES ]
						: Array();
			
			//
			// Get fields.
			//
			$fields = ( array_key_exists( kAPI_DATA_FIELD, $_REQUEST ) )
					? $_REQUEST[ kAPI_DATA_FIELD ]
					: Array();

			//
			// Handle node relationships.
			//
			if( array_key_exists( kAPI_OPT_DIRECTION, $_REQUEST ) )
			{
				//
				// Check direction.
				//
				switch( $_REQUEST[ kAPI_OPT_DIRECTION ] )
				{
					case kAPI_DIRECTION_IN:
					case kAPI_DIRECTION_OUT:
					case kAPI_DIRECTION_ALL:
						break;
					
					default:
						throw new CException
								( "Untrapped invalid relationship direction",
								  kERROR_UNSUPPORTED,
								  kMESSAGE_TYPE_BUG,
								  array( 'Tag'
								  		=> $_REQUEST[ kAPI_OPT_DIRECTION ] ) );	// !@! ==>
				}
				
				//
				// Iterate node identifiers.
				//
				foreach( $_REQUEST[ kAPI_OPT_IDENTIFIERS ] as $id )
				{
					//
					// Instantiate node.
					//
					$node = $_SESSION[ kSESSION_NEO4J ]->getNode( $id );
					if( $node !== NULL )
					{
						//
						// Traverse graph.
						//
						$level = $_REQUEST[ kAPI_OPT_LEVELS ];
						$stack = array( $id => $node );
						$count
							+= $this->_Traverse
								( $stack,
								  $terms, $ref_node, $ref_edge,
								  $predicates,
								  $level );
					
					} // Found node.
				
				} // Iterating node identifiers.
			
			} // Provided direction.
			
			//
			// Handle edge identifiers.
			//
			else
			{
				//
				// Iterate identifiers.
				//
				foreach( $_REQUEST[ kAPI_OPT_IDENTIFIERS ] as $id )
				{
					//
					// Instantiate relationship.
					//
					$edge =  $container[ kTAG_NODE ]->getRelationship( $id );
					if( $edge !== NULL )
						$count
							+= $this->_EdgeParser
								( $edge, $terms, $ref_node, $ref_edge );
				
				} // Iterating edge identifiers.
			
			} // Direction not provided.
			
			//
			// Handle terms.
			//
			if( count( $terms ) )
			{
				//
				// Normalise identifiers.
				//
				foreach( $terms as $key => $value )
				{
					$value = COntologyTerm::HashIndex( $value );
					$container[ kTAG_TERM ]->UnserialiseData( $value );
					$terms[ $key ] = $value;
				}
				
				//
				// Fix fields.
				//
				$added = FALSE;
				if( count( $fields )
				 && (! array_key_exists( kTAG_GID, $fields )) )
				{
					$fields[ kTAG_GID ] = TRUE;
					$added = TRUE;
				}
			
				//
				// Set query.
				//
				$query = Array();
				$query[ kTAG_LID ] = array( '$in' => array_values( $terms ) );
				
				//
				// Make query.
				//
				$cursor = $container[ kTAG_TERM ]->Container()->find( $query, $fields );
				
				//
				// Save terms.
				//
				foreach( $cursor as $record )
				{
					CDataType::SerialiseObject( $record );
					if( array_key_exists( kTAG_GID, $record ) )
					{
						$id = $record[ kTAG_GID ];
						if( $added )
							unset( $record[ kTAG_GID ] );
						$ref_term[ $id ] = $record;
					}
				
				} // Loading found terms.
				
			} // Found terms.
	
			//
			// Copy response.
			//
			$this->offsetSet( kAPI_DATA_RESPONSE, $response );
		
		} // Provided identifiers list.
		
		//
		// Set count.
		//
		$this->_OffsetManage( kAPI_DATA_STATUS, kAPI_AFFECTED_COUNT, $count );
	
	} // _Handle_GetRelations.

	 
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
		// Add kAPI_OP_LOGIN.
		//
		$theList[ kAPI_OP_LOGIN ]
			= 'This operation requests a user information according to the provided user ['
			 .kAPI_OPT_USER_CODE
			 .'] code and user ['
			 .kAPI_OPT_USER_PASS
			 .'] password: if a batch occurs, the service will return the user record.';
		
		//
		// Add kAPI_OP_GET_TERMS.
		//
		$theList[ kAPI_OP_GET_TERMS ]
			= 'This operation will return the list of ontology terms matching the provided '
			.'list of ['
			.kAPI_OPT_IDENTIFIERS
			.'] identifiers.';
		
		//
		// Add kAPI_OP_GET_NODES.
		//
		$theList[ kAPI_OP_GET_NODES ]
			= 'This operation will return the list of ontology nodes matching the provided '
			.'list of node ['
			.kAPI_OPT_IDENTIFIERS
			.'] identifiers.';
		
		//
		// Add kAPI_OP_GET_EDGES.
		//
		$theList[ kAPI_OP_GET_EDGES ]
			= 'This operation will return the list of ontology edge nodes matching the '
			 .'provided list of edge node ['
			.kAPI_OPT_IDENTIFIERS
			.'] identifiers.';
		
		//
		// Add kAPI_OP_GET_RELS.
		//
		$theList[ kAPI_OP_GET_RELS ]
			= 'This operation will return the list of ontology edges related to the '
			.'provided list of nodes ['
			.kAPI_OPT_IDENTIFIERS
			.'] identifiers.';
		
		//
		// Add kAPI_OP_GET_ROOTS.
		//
		$theList[ kAPI_OP_GET_ROOTS ]
			= 'This operation will return the list of ontology root nodes matching the provided '
			.'query in the ['
			.kAPI_DATA_QUERY
			.'] parameter.';
	
	} // _Handle_ListOp.

		

/*=======================================================================================
 *																						*
 *									PROTECTED UTILITIES									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_EscapeLucene																	*
	 *==================================================================================*/

	/**
	 * Escape Lucene element.
	 *
	 * This method will escape the provided element to make it compatible with the Lucene
	 * query language, the method expects the provided parameter to be an element excluding
	 * specific lucene operators.
	 *
	 * @param string				$theElement			Element to escape.
	 *
	 * @access protected
	 * @return string
	 */
	protected function _EscapeLucene( $theElement )
	{
		//
		// Init local storage.
		//
		$escaped = '';
		$to_escape = array( '+', '-', '!', ')', '(', '{', '}', '[', ' ',
							']', '^', '"', '~', '*', '?',  ':', '\\' );
		
		//
		// Normalise parameter.
		//
		$theElement = (string) $theElement;
		
		//
		// Iterate element.
		//
		for( $i = 0; $i < strlen( $theElement ); $i++ )
		{
			if( in_array( $theElement[ $i ], $to_escape ) )
				$escaped .= '\\';
			$escaped .= $theElement[ $i ];
		}
		
		return $escaped;															// ==>
	
	} // _EscapeLucene.

	 
	/*===================================================================================
	 *	_Traverse																		*
	 *==================================================================================*/

	/**
	 * Traverse graph.
	 *
	 * This method will traverse the graph in the direction set in the
	 * {@link kAPI_OPT_DIRECTION kAPI_OPT_DIRECTION} parameter and
	 * {@link _EdgeParser() collect} all found edge elements in the provided parameters.
	 *
	 * The parameters to this method are:
	 *
	 * <ul>
	 *	<li><b>&$theList</b>: List of current level node identifiers.
	 *	<li><b>&$theTerms</b>: Reference to the list of term identifiers.
	 *	<li><b>&$theNodes</b>: Reference to the list of nodes.
	 *	<li><b>&$theEdges</b>: Reference to the list of edge elements.
	 *	<li><b>&$thePredicates</b>: Reference to the list of predicates to be considered.
	 *	<li><b>$theLevel</b>: Current traversal depth level, 0 means the lowest.
	 * </ul>
	 *
	 * The method will return the number of processed elements count.
	 *
	 * @param reference			   &$theList			Current level node identifiers.
	 * @param reference			   &$theTerms			List of term identifiers.
	 * @param reference			   &$theNodes			List of nodes.
	 * @param reference			   &$theEdges			List of edge references.
	 * @param reference			   &$thePredicates		List of predicate filter references.
	 * @param integer				$theLevel			Depth level.
	 *
	 * @access protected
	 */
	protected function _Traverse( &$theList,
								  &$theTerms, &$theNodes, &$theEdges,
								  &$thePredicates,
								  $theLevel )
	{
		//
		// Init local storage.
		//
		$count = 0;
		
		//
		// Check level.
		//
		if( $theLevel )
		{
			//
			// Get direction.
			//
			switch( $_REQUEST[ kAPI_OPT_DIRECTION ] )
			{
				case kAPI_DIRECTION_IN:
					$direction = Everyman\Neo4j\Relationship::DirectionIn;
					break;

				case kAPI_DIRECTION_OUT:
					$direction = Everyman\Neo4j\Relationship::DirectionOut;
					break;

				case kAPI_DIRECTION_ALL:
					$direction = Everyman\Neo4j\Relationship::DirectionAll;
					break;
				
				default:
					throw new CException
							( "Untrapped invalid relationship direction",
							  kERROR_UNSUPPORTED,
							  kMESSAGE_TYPE_BUG,
							  array( 'Tag'
									=> $_REQUEST[ kAPI_OPT_DIRECTION ] ) );		// !@! ==>
			}
			
			//
			// Iterate list.
			//
			while( count( $theList ) )
			{
				//
				// Get relations.
				//
				$node = array_shift( $theList );
				$edges = $node->getRelationships( $thePredicates, $direction );
				foreach( $edges as $edge )
				{
					//
					// Skip done edges.
					//
					if( ! array_key_exists( $edge->getId(), $theEdges ) )
					{
						//
						// Collect nodes.
						//
						$nodes = Array();
						switch( $direction )
						{
							//
							// Cache subject.
							//
							case Everyman\Neo4j\Relationship::DirectionIn:
								$id = $edge->getStartNode()->getId();
								if( (! array_key_exists( $id, $nodes ))
								 && ($id != $node->getId()) )
									$nodes[ $id ] = $edge->getStartNode();
								break;
						
							//
							// Cache object.
							//
							case Everyman\Neo4j\Relationship::DirectionOut:
								$id = $edge->getEndNode()->getId();
								if( (! array_key_exists( $id, $nodes ))
								 && ($id != $node->getId()) )
									$nodes[ $id ] = $edge->getEndNode();
								break;
						
							//
							// Cache subject and object.
							//
							case Everyman\Neo4j\Relationship::DirectionAll:
								$id = $edge->getStartNode()->getId();
								if( (! array_key_exists( $id, $nodes ))
								 && ($id != $node->getId()) )
									$nodes[ $id ] = $edge->getStartNode();
	
								$id = $edge->getEndNode()->getId();
								if( (! array_key_exists( $id, $nodes ))
								 && ($id != $node->getId()) )
									$nodes[ $id ] = $edge->getEndNode();
								break;
						
						} // Caching nodes.
						
						//
						// Collect edge elements.
						//
						$count
							+= $this->_EdgeParser
								( $edge, $theTerms, $theNodes, $theEdges );
						
						//
						// Recurse.
						//
						$count
							+= $this->_Traverse
								( $nodes,
								  $theTerms, $theNodes, $theEdges,
								  $thePredicates,
								  $theLevel - 1 );
					
					} // New edge.
				
				} // Iterating edges.
			
			} // List not empty.
		
		} // Not reached last level.
		
		return $count;																// ==>
	
	} // _Traverse.

	 
	/*===================================================================================
	 *	_EdgeParser																		*
	 *==================================================================================*/

	/**
	 * Collect edge elements.
	 *
	 * This method will collect the provided edge elements and set them into the provided
	 * parameters.
	 *
	 * The parameters to this method are:
	 *
	 * <ul>
	 *	<li><b>$theEdge</b>: Edge node to handle.
	 *	<li><b>&$theTerms</b>: Reference to the list of term identifiers.
	 *	<li><b>&$theNodes</b>: Reference to the list of nodes.
	 *	<li><b>&$theEdges</b>: Reference to the list of edge elements.
	 * </ul>
	 *
	 * The method will return 1 if the edge was handled, 0 if not.
	 *
	 * @param Relationship			$theEdge			Edge node to handle.
	 * @param reference			   &$theTerms			List of term identifiers.
	 * @param reference			   &$theNodes			List of nodes.
	 * @param reference			   &$theEdges			List of edge references.
	 *
	 * @access protected
	 */
	protected function _EdgeParser( $theEdge,
								   &$theTerms, &$theNodes, &$theEdges )
	{
		//
		// Filter predicates.
		//
		if( (! array_key_exists( kAPI_OPT_PREDICATES, $_REQUEST ))
		 || in_array( $theEdge->getType(), $_REQUEST[ kAPI_OPT_PREDICATES ] ) )
		{
			//
			// Check if new.
			//
			if( ! array_key_exists( $theEdge->getId(), $theEdges ) )
			{
				//
				// Save elements.
				//
				$subject = $theEdge->getStartNode();
				$predicate = $theEdge->getType();
				$object = $theEdge->getEndNode();
				
				//
				// Add edge element.
				//
				$theEdges[ $theEdge->getId() ]
					= array( kAPI_RESPONSE_SUBJECT => $subject->getId(),
							 kAPI_RESPONSE_PREDICATE => $predicate,
							 kAPI_RESPONSE_OBJECT => $object->getId(),
							 kTAG_DATA => $theEdge->getProperties() );
				
				//
				// Add predicate term.
				//
				if( ! in_array( $predicate, $theTerms ) )
					$theTerms[] = $predicate;
				
				//
				// Add subject node.
				//
				if( ! array_key_exists( $subject->getId(), $theNodes ) )
				{
					//
					// Set node.
					//
					$theNodes[ $subject->getId() ] = $subject->getProperties();
					
					//
					// Set term.
					//
					$term = $subject->getProperty( kTAG_TERM );
					if( ($term !== NULL)
					 && (! in_array( $term, $theTerms )) )
						$theTerms[] = $term;
				
				} // New subject.
				
				//
				// Add object node.
				//
				if( ! array_key_exists( $object->getId(), $theNodes ) )
				{
					//
					// Set node.
					//
					$theNodes[ $object->getId() ] = $object->getProperties();
					
					//
					// Set term.
					//
					$term = $object->getProperty( kTAG_TERM );
					if( ($term !== NULL)
					 && (! in_array( $term, $theTerms )) )
						$theTerms[] = $term;
				
				} // New object.
				
				return 1;															// ==>
			
			} // New edge.
		
		} // Predicate not filtered.
		
		return 0;																	// ==>
	
	} // _EdgeParser.

	 

} // class CWarehouseWrapper.


?>
