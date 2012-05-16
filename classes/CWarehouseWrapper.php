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
				// Handle terms list.
				//
				case kAPI_OP_GET_TERMS:
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
		$this->_ParseSelectors();
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
		$this->_FormatSelectors();
	
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
	 *	_ParseSelectors																	*
	 *==================================================================================*/

	/**
	 * Parse identifiers.
	 *
	 * This method will parse the attribute {@link kAPI_OPT_ATTRIBUTES selectors} parameter.
	 *
	 * @access protected
	 *
	 * @see kAPI_DATA_REQUEST kAPI_OPT_ATTRIBUTES
	 */
	protected function _ParseSelectors()
	{
		//
		// Handle selectors.
		//
		if( array_key_exists( kAPI_OPT_ATTRIBUTES, $_REQUEST ) )
		{
			//
			// Add to request.
			//
			if( $this->offsetExists( kAPI_DATA_REQUEST ) )
				$this->_OffsetManage
					( kAPI_DATA_REQUEST, kAPI_OPT_ATTRIBUTES,
					  $_REQUEST[ kAPI_OPT_ATTRIBUTES ] );
		
		} // Has selectors list.
	
	} // _ParseSelectors.

	 
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

	 
	/*===================================================================================
	 *	_FormatSelectors																*
	 *==================================================================================*/

	/**
	 * This method will format the request selectors list.
	 *
	 * This method will convert the attribute/value pairs provided in the
	 * {@link kAPI_OPT_ATTRIBUTES kAPI_OPT_ATTRIBUTES} parameter into a Lucene
	 * compatible query, connecting all clauses in <i>AND</i>.
	 *
	 * Note that the method will enforce the
	 * {@link kAPI_OPT_ATTRIBUTES kAPI_OPT_ATTRIBUTES} parameter by initialising it
	 * with the {@link kTYPE_ROOT root} {@link kTAG_KIND kind} selection.
	 *
	 * @access protected
	 *
	 * @uses _DecodeParameter()
	 *
	 * @see kAPI_OPT_ATTRIBUTES
	 */
	protected function _FormatSelectors()
	{
		//
		// Init local storage.
		//
		$query = array( $this->_EscapeLucene( kTAG_KIND )
					   .':'
					   .$this->_EscapeLucene( kTYPE_ROOT ) );
		
		//
		// Add clauses.
		//
		if( array_key_exists( kAPI_OPT_ATTRIBUTES, $_REQUEST ) )
		{
			//
			// Decode parameter.
			//
			$this->_DecodeParameter( kAPI_OPT_ATTRIBUTES );
			
			//
			// Iterate attributes.
			//
			foreach( $_REQUEST[ kAPI_OPT_ATTRIBUTES ] as $key => $value )
			{
				//
				// Convert key.
				//
				$attribute = $this->_EscapeLucene( $key );
				
				//
				// Normalise values.
				//
				if( ! is_array( $value ) )
					$value = array( $value );
				
				//
				// Iterate values.
				//
				foreach( $value as $clause )
					$query[] = "$attribute:".$this->_EscapeLucene( $clause );
			
			} // Iterating attributes.
		}
		
		//
		// Build query.
		//
		$_REQUEST[ kAPI_OPT_ATTRIBUTES ] = implode( ' AND ', $query );
	
	} // _FormatSelectors.

		

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
				
			case kAPI_OP_GET_EDGES:
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
				
			case kAPI_OP_GET_TERMS:
			case kAPI_OP_GET_NODES:
			case kAPI_OP_QUERY_ROOTS:
				
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
				$this->_Handle_Get();
				break;

			case kAPI_OP_GET_NODES:
				$this->_Handle_GetNodes();
				break;

			case kAPI_OP_GET_EDGES:
				$this->_Handle_GetEdges();
				break;

			case kAPI_OP_QUERY_ROOTS:
				$this->_Handle_QueryOntologies();
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
	 *	_Handle_GetNodes																*
	 *==================================================================================*/

	/**
	 * Handle {@link kAPI_OP_GET_TERMS get-nodes} request.
	 *
	 * This method expects the {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter
	 * to hold a list of node IDs, the method will query these nodes and return the
	 * following structure:
	 *
	 * <ul>
	 *	<li><i>{@link kAPI_RESPONSE_TERMS kAPI_RESPONSE_TERMS}</i>: The list of terms
	 *		related to the list of nodes as follows:
	 *	 <ul>
	 *		<li><i>Index</i>: The term {@link kTAG_GID identifier}.
	 *		<li><i>Value</i>: The term properties.
	 *	 </ul>
	 *	<li><i>{@link kAPI_RESPONSE_NODES kAPI_RESPONSE_NODES}</i>: The list of nodes as
	 *		follows:
	 *	 <ul>
	 *		<li><i>Index</i>: The node ID.
	 *		<li><i>Value</i>: The node properties.
	 *	 </ul>
	 * </ul>
	 *
	 * If the {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter was not provided,
	 * the method will return the above structure with no content.
	 *
	 * @access protected
	 */
	protected function _Handle_GetNodes()
	{
		//
		// Init local storage.
		//
		$count = 0;
		$container = array( kTAG_TERM => new CMongoContainer( $_REQUEST[ kAPI_CONTAINER ] ),
							kTAG_NODE => $_SESSION[ kSESSION_NEO4J ] );
		$response = array( kAPI_RESPONSE_TERMS => Array(),
						   kAPI_RESPONSE_NODES => Array() );
		
		//
		// Get fields.
		//
		$fields = ( array_key_exists( kAPI_DATA_FIELD, $_REQUEST ) )
				? $_REQUEST[ kAPI_DATA_FIELD ]
				: Array();
		
		//
		// Handle identifiers.
		//
		if( array_key_exists( kAPI_OPT_IDENTIFIERS, $_REQUEST ) )
		{
			//
			// Init local storage.
			//
			$terms = Array();
			$ref_term = & $response[ kAPI_RESPONSE_TERMS ];
			$ref_node = & $response[ kAPI_RESPONSE_NODES ];
			
			//
			// Iterate identifiers.
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
					// Get node properties.
					//
					$ref_node[ $id ] = $node->getProperties();
					
					//
					// Get term identifier.
					//
					$term = $node->getProperty( kTAG_TERM );
					if( ($term !== NULL)
					 && (! in_array( $term, $terms )) )
						$terms[] = $term;
					
					//
					// Count.
					//
					$count++;
				
				} // Found node.
				
				//
				// Handle missing node.
				//
				else
					$ref_node[ $id ] = Array();
			
			} // Iterating identifiers.
			
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
			// Load terms.
			//
			$query = array( kTAG_LID => array( '$in' => $terms ) );
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
			}
		
		} // Has identifiers.
		
		//
		// Set count.
		//
		$this->_OffsetManage( kAPI_DATA_STATUS, kAPI_AFFECTED_COUNT, $count );

		//
		// Copy response.
		//
		$this->offsetSet( kAPI_DATA_RESPONSE, $response );
	
	} // _Handle_GetNodes.

	 
	/*===================================================================================
	 *	_Handle_GetEdges																*
	 *==================================================================================*/

	/**
	 * Handle {@link kAPI_OP_GET_EDGES get-edges} request.
	 *
	 * This method will return the following structure:
	 *
	 * <ul>
	 *	<li><i>{@link kAPI_RESPONSE_TERMS kAPI_RESPONSE_TERMS}</i>: The list of terms
	 *		related to the list of subject and object nodes and the list of predicate terms
	 *		as follows:
	 *	 <ul>
	 *		<li><i>Index</i>: The term {@link kTAG_GID identifier}.
	 *		<li><i>Value</i>: The term properties.
	 *	 </ul>
	 *	<li><i>{@link kAPI_RESPONSE_NODES kAPI_RESPONSE_NODES}</i>: The list of subject and
	 *		object nodes as follows:
	 *	 <ul>
	 *		<li><i>Index</i>: The node ID.
	 *		<li><i>Value</i>: The node properties.
	 *	 </ul>
	 *	<li><i>{@link kAPI_RESPONSE_EDGES kAPI_RESPONSE_EDGES}</i>: The list of edges as an
	 *		array structured as follows:
	 *	 <ul>
	 *		<li><i>Index</i>: The edge identifier.
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
	 * </ul>
	 *
	 * The method will interpret the contents of the
	 * {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter depending on whether the
	 * {@link kAPI_OPT_DIRECTION kAPI_OPT_DIRECTION} parameter was provided or not:
	 *
	 * <ul>
	 *	<li><i>{@link kAPI_OPT_DIRECTION kAPI_OPT_DIRECTION} not provided</i>: In this case
	 *		the method will treat the {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS}
	 *		parameter elements as a list of {@link COntologyEdge edge} identifiers to be
	 *		matched.
	 *	<li><i>{@link kAPI_OPT_DIRECTION kAPI_OPT_DIRECTION} provided</i>: In this case the
	 *		method will treat the {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS}
	 *		parameter elements as a list of {@link COntologyNode node} identifiers for which
	 *		we want to retrieve connected {@link COntologyEdge edges} in the direction
	 *		provided in the {@link kAPI_OPT_DIRECTION kAPI_OPT_DIRECTION} parameter:
	 *	 <ul>
	 *		<li><i>{@link kAPI_DIRECTION_IN kAPI_DIRECTION_IN}</i>: The service will return
	 *			all {@link COntologyEdge edges} that point to the
	 *			{@link COntologyNode nodes} provided in the
	 *			{@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter.
	 *		<li><i>{@link kAPI_DIRECTION_OUT kAPI_DIRECTION_OUT}</i>: The service will
	 *			return all {@link COntologyEdge edges} pointing from the
	 *			{@link COntologyNode nodes} provided in the
	 *			{@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter.
	 *		<li><i>{@link kAPI_DIRECTION_ALL kAPI_DIRECTION_ALL}</i>: The service will
	 *			return all {@link COntologyEdge edges} connected in any way to the
	 *			{@link COntologyNode nodes} provided in the
	 *			{@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter.
	 *	 </ul>
	 * </ul>
	 *
	 * If the {@link kAPI_OPT_PREDICATES kAPI_OPT_PREDICATES} parameter was provided, only
	 * those {@link COntologyEdge edges} whose type matches any of the predicate
	 * {@link COntologyTerm term} identifiers provided in that parameter will be selected.
	 *
	 * If the {@link kAPI_OPT_IDENTIFIERS kAPI_OPT_IDENTIFIERS} parameter was not provided,
	 * the method will return the above structure with no content.
	 *
	 * @access protected
	 */
	protected function _Handle_GetEdges()
	{
		//
		// Init local storage.
		//
		$count = 0;
		$container = array( kTAG_TERM => new CMongoContainer( $_REQUEST[ kAPI_CONTAINER ] ),
							kTAG_NODE => $_SESSION[ kSESSION_NEO4J ] );
		$response = array( kAPI_RESPONSE_TERMS => Array(),
						   kAPI_RESPONSE_NODES => Array(),
						   kAPI_RESPONSE_EDGES => Array() );
		
		//
		// Handle identifiers.
		//
		if( array_key_exists( kAPI_OPT_IDENTIFIERS, $_REQUEST ) )
		{
			//
			// Init local storage.
			//
			$terms = Array();
			$ref_term = & $response[ kAPI_RESPONSE_TERMS ];
			$ref_node = & $response[ kAPI_RESPONSE_NODES ];
			$ref_edge = & $response[ kAPI_RESPONSE_EDGES ];
			
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
								( $stack, $terms, $ref_node, $ref_edge, $level );
					
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
			// Load terms.
			//
			$query = array( kTAG_LID => array( '$in' => $terms ) );
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
			}
		
		} // Provided identifiers list.
		
		//
		// Set count.
		//
		$this->_OffsetManage( kAPI_DATA_STATUS, kAPI_AFFECTED_COUNT, $count );

		//
		// Copy response.
		//
		$this->offsetSet( kAPI_DATA_RESPONSE, $response );
	
	} // _Handle_GetEdges.

	 
	/*===================================================================================
	 *	_Handle_QueryOntologies															*
	 *==================================================================================*/

	/**
	 * Handle {@link kAPI_OP_QUERY_ROOTS query-nodes} request.
	 *
	 * This method expects the {@link kAPI_OPT_ATTRIBUTES kAPI_OPT_ATTRIBUTES}
	 * parameter to hold a list of key/value pairs filter that will be added to the
	 * default {@link kTYPE_ROOT root} {@link kTAG_KIND kind} query; if the
	 * parameter was omitted, the method will select all ontologies.
	 *
	 * This method will return the following structure:
	 *
	 * <ul>
	 *	<li><i>{@link kAPI_RESPONSE_TERMS kAPI_RESPONSE_TERMS}</i>: The list of
	 *		{@link COntologyTerm terms} related to the
	 *		{@link COntologyNode::Node() ontologies} as follows:
	 *	 <ul>
	 *		<li><i>Index</i>: The term {@link kTAG_GID identifier}.
	 *		<li><i>Value</i>: The term properties.
	 *	 </ul>
	 *	<li><i>{@link kAPI_RESPONSE_NODES kAPI_RESPONSE_NODES}</i>: The list of
	 *		{@link COntologyNode::Node() ontologies} as follows:
	 *	 <ul>
	 *		<li><i>Index</i>: The {@link COntologyNode::Node() node} ID.
	 *		<li><i>Value</i>: The {@link COntologyNode::Node() node} properties.
	 *	 </ul>
	 * </ul>
	 *
	 * @access protected
	 */
	protected function _Handle_QueryOntologies()
	{
		//
		// Init local storage.
		//
		$count = 0;
		$container = array( kTAG_TERM => new CMongoContainer( $_REQUEST[ kAPI_CONTAINER ] ),
							kTAG_NODE => $_SESSION[ kSESSION_NEO4J ] );
		$response = array( kAPI_RESPONSE_TERMS => Array(),
						   kAPI_RESPONSE_NODES => Array() );
		
		//
		// Handle selectors.
		//
		if( array_key_exists( kAPI_OPT_ATTRIBUTES, $_REQUEST ) )
		{
			//
			// Init local storage.
			//
			$ref_term = & $response[ kAPI_RESPONSE_TERMS ];
			$ref_node = & $response[ kAPI_RESPONSE_NODES ];
			
			//
			// Instantiate node index.
			//
			$idx = new NodeIndex( $_SESSION[ kSESSION_NEO4J ], kINDEX_NODE_NODE );
			$idx->save();
			
			//
			// Execute query.
			//
			$results = $idx->query( $_REQUEST[ kAPI_OPT_ATTRIBUTES ] );
			foreach( $results as $object )
			{
				//
				// Instantiate node.
				//
				$node = new COntologyNode( $container, $object );
				if( $node->Persistent() )
				{
					//
					// Set node properties.
					//
					$id = $node->Node()->getId();
					if( ! array_key_exists( $id, $ref_node ) )
						$ref_node[ $id ] = $node->Node()->getProperties();
					
					//
					// Set term properties.
					//
					$id = $node->Term()->GID();
					if( ! array_key_exists( $id, $ref_term ) )
						$ref_term[ $id ] = $node->Term()->getArrayCopy();
					
					//
					// Count.
					//
					$count++;
				}
			
			} // Iterating found ontology nodes.
		}
		
		//
		// Set count.
		//
		$this->_OffsetManage( kAPI_DATA_STATUS, kAPI_AFFECTED_COUNT, $count );

		//
		// Copy response.
		//
		$this->offsetSet( kAPI_DATA_RESPONSE, $response );
	
	} // _Handle_QueryOntologies.

	 
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
			.'list of term ['
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
		// Add kAPI_OP_QUERY_ROOTS.
		//
		$theList[ kAPI_OP_QUERY_ROOTS ]
			= 'This operation will return the list of ontology nodes matching the provided '
			.'attribute/value pairs in ['
			.kAPI_OPT_ATTRIBUTES
			.'] selectors.';
	
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
	 *	<li><b>$theEdge</i>: Edge node to handle.
	 *	<li><b>&$theTerms</i>: Reference to the list of term identifiers.
	 *	<li><b>&$theNodes</i>: Reference to the list of nodes.
	 *	<li><b>&$theEdges</i>: Reference to the list of edge elements.
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
							 kAPI_RESPONSE_OBJECT => $object->getId() );
				
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
					$predicate = $subject->getProperty( kTAG_TERM );
					if( ($predicate !== NULL)
					 && (! in_array( $predicate, $theTerms )) )
						$theTerms[] = $predicate;
				
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
					$predicate = $object->getProperty( kTAG_TERM );
					if( ($predicate !== NULL)
					 && (! in_array( $predicate, $theTerms )) )
						$theTerms[] = $predicate;
				
				} // New object.
				
				return 1;															// ==>
			
			} // New edge.
		
		} // Predicate not filtered.
		
		return 0;																	// ==>
	
	} // _EdgeParser.

	 
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
	 *	<li><b>&$theList</i>: List of current level node identifiers.
	 *	<li><b>&$theTerms</i>: Reference to the list of term identifiers.
	 *	<li><b>&$theNodes</i>: Reference to the list of nodes.
	 *	<li><b>&$theEdges</i>: Reference to the list of edge elements.
	 *	<li><b>$theLevel</i>: Current traversal depth level, 0 means the lowest.
	 * </ul>
	 *
	 * The method will return the number of processed elements count.
	 *
	 * @param reference			   &$theList			Current level node identifiers.
	 * @param reference			   &$theTerms			List of term identifiers.
	 * @param reference			   &$theNodes			List of nodes.
	 * @param reference			   &$theEdges			List of edge references.
	 * @param integer				$theLevel			Depth level.
	 *
	 * @access protected
	 */
	protected function _Traverse( &$theList, &$theTerms, &$theNodes, &$theEdges, $theLevel )
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
			// Get predicates.
			//
			$predicates = ( array_key_exists( kAPI_OPT_PREDICATES, $_REQUEST ) )
						? $_REQUEST[ kAPI_OPT_PREDICATES ]
						: Array();
	
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
				$edges = $node->getRelationships( $predicates, $direction );
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
								( $nodes, $theTerms, $theNodes, $theEdges, $theLevel - 1 );
					
					} // New edge.
				
				} // Iterating edges.
			
			} // List not empty.
		
		} // Not reached last level.
		
		return $count;																// ==>
	
	} // _Traverse.

	 

} // class CWarehouseWrapper.


?>
