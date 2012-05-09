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
	 * In this class we set the {@link kAPI_DATA_PAGING paging} options if the operation
	 * involves using the {@link kAPI_OPT_IDENTIFIERS identifiers} list and no list was
	 * provided.
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
				// Enforce page limits on all full lists.
				//
				case kAPI_OP_GET_TERMS:
				case kAPI_OP_GET_NODES:
					//
					// Check if there are identifiers.
					//
					if( ! array_key_exists( kAPI_OPT_IDENTIFIERS, $_REQUEST ) )
					{
						//
						// Enforce start.
						//
						if( ! array_key_exists( kAPI_PAGE_START, $_REQUEST ) )
							$_REQUEST[ kAPI_PAGE_START ] = 0;
						//
						// Enforce limit.
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
		// Parse identifiers.
		//
		$this->_ParseIdentifiers();
		$this->_ParseSelectors();
	
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
		$this->_FormatSelectors();
	
	} // _FormatRequest.

		

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
	 *	_ParseSelectors																	*
	 *==================================================================================*/

	/**
	 * Parse identifiers.
	 *
	 * This method will parse the user {@link kAPI_OPT_NODE_SELECTORS selectors} parameter.
	 *
	 * @access protected
	 *
	 * @see kAPI_DATA_REQUEST kAPI_OPT_NODE_SELECTORS
	 */
	protected function _ParseSelectors()
	{
		//
		// Handle selectors.
		//
		if( array_key_exists( kAPI_OPT_NODE_SELECTORS, $_REQUEST ) )
		{
			//
			// Add to request.
			//
			if( $this->offsetExists( kAPI_DATA_REQUEST ) )
				$this->_OffsetManage
					( kAPI_DATA_REQUEST, kAPI_OPT_NODE_SELECTORS,
					  $_REQUEST[ kAPI_OPT_NODE_SELECTORS ] );
		
		} // Has selectors list.
	
	} // _ParseSelectors.

		

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
	 * This method will actually create a query using the identifiers list, it will
	 * {@link _Handle_GetTerms() then} call the {@link kAPI_OP_GET GET} handler.
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
				// Parse by request.
				//
				switch( $_REQUEST[ kAPI_OPERATION ] )
				{
					case kAPI_OP_GET_TERMS:
						//
						// Iterate identifiers.
						//
						$identifiers = Array();
						foreach( $_REQUEST[ kAPI_OPT_IDENTIFIERS ] as $identifier )
							$identifiers[] = COntologyTerm::HashIndex( $identifier );
			
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
	 *	_FormatSelectors																*
	 *==================================================================================*/

	/**
	 * This method will format the request selectors list.
	 *
	 * This method will convert the attribute/value pairs provided in the
	 * {@link kAPI_OPT_NODE_SELECTORS kAPI_OPT_NODE_SELECTORS} parameter into a Lucene
	 * compatible query, connecting all clauses in <i>AND</i>.
	 *
	 * Note that the method will enforce the
	 * {@link kAPI_OPT_NODE_SELECTORS kAPI_OPT_NODE_SELECTORS} parameter by initialising it
	 * with the {@link kTYPE_ONTOLOGY ontology} {@link kTAG_KIND kind} selection.
	 *
	 * @access protected
	 *
	 * @uses _DecodeParameter()
	 *
	 * @see kAPI_OPT_NODE_SELECTORS
	 */
	protected function _FormatSelectors()
	{
		//
		// Init local storage.
		//
		$query = array( $this->_EscapeLucene( kTAG_KIND )
					   .':'
					   .$this->_EscapeLucene( kTYPE_ONTOLOGY ) );
		
		//
		// Add clauses.
		//
		if( array_key_exists( kAPI_OPT_NODE_SELECTORS, $_REQUEST ) )
		{
			//
			// Decode parameter.
			//
			$this->_DecodeParameter( kAPI_OPT_NODE_SELECTORS );
			
			//
			// Iterate attributes.
			//
			foreach( $_REQUEST[ kAPI_OPT_NODE_SELECTORS ] as $key => $value )
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
		$_REQUEST[ kAPI_OPT_NODE_SELECTORS ] = implode( ' AND ', $query );
	
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
			
			case kAPI_OP_GET_TERMS:
			case kAPI_OP_GET_NODES:
			case kAPI_OP_QUERY_ONTOLOGIES:
				
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

			case kAPI_OP_QUERY_ONTOLOGIES:
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
	 *	_Handle_GetNodes																	*
	 *==================================================================================*/

	/**
	 * Handle {@link kAPI_OP_GET_TERMS get-nodes} request.
	 *
	 * This method will return an array indexed by the {@link Node() node} ID and having
	 * as attributes the {@link getArrayCopy() merged} attributes of the
	 * {@link COntology::Term() term} and the {@link COntology::Node() node}.
	 *
	 * @access protected
	 */
	protected function _Handle_GetNodes()
	{
		//
		// Init local storage.
		//
		$count = 0;
		$nodes = Array();
		$container = array( kTAG_TERM => new CMongoContainer( $_REQUEST[ kAPI_CONTAINER ] ),
							kTAG_NODE => $_SESSION[ kSESSION_NEO4J ] );
		
		//
		// Handle identifiers.
		//
		if( array_key_exists( kAPI_OPT_IDENTIFIERS, $_REQUEST ) )
		{
			//
			// Iterate identifiers.
			//
			foreach( $_REQUEST[ kAPI_OPT_IDENTIFIERS ] as $identifier )
			{
				$node = new COntologyNode( $container, $identifier );
				if( $node->Persistent() )
				{
					$nodes[ $node->Node()->getId() ] = $node->getArrayCopy();
					$count++;
				}
			}
		}
		
		//
		// Set count.
		//
		$this->_OffsetManage( kAPI_DATA_STATUS, kAPI_AFFECTED_COUNT, $count );

		//
		// Copy response.
		//
		$this->offsetSet( kAPI_DATA_RESPONSE, $nodes );
	
	} // _Handle_GetNodes.

	 
	/*===================================================================================
	 *	_Handle_QueryOntologies															*
	 *==================================================================================*/

	/**
	 * Handle {@link kAPI_OP_QUERY_ONTOLOGIES query-nodes} request.
	 *
	 * This method will return an array indexed by the {@link Node() node} ID and having
	 * as attributes the {@link getArrayCopy() merged} attributes of the
	 * {@link COntology::Term() term} and the {@link COntology::Node() node}.
	 *
	 * @access protected
	 */
	protected function _Handle_QueryOntologies()
	{
		//
		// Init local storage.
		//
		$count = 0;
		$nodes = Array();
		$container = array( kTAG_TERM => new CMongoContainer( $_REQUEST[ kAPI_CONTAINER ] ),
							kTAG_NODE => $_SESSION[ kSESSION_NEO4J ] );
		
		//
		// Handle identifiers.
		//
		if( array_key_exists( kAPI_OPT_NODE_SELECTORS, $_REQUEST ) )
		{
			//
			// Instantiate node index.
			//
			$idx = new NodeIndex( $_SESSION[ kSESSION_NEO4J ], kINDEX_NODE_NODE );
			$idx->save();
			
			//
			// Execute query.
			//
			$results = $idx->query( $_REQUEST[ kAPI_OPT_NODE_SELECTORS ] );
			foreach( $results as $object )
			{
				//
				// Create node.
				//
				$node = new COntologyNode( $container, $object );
				if( $node->Persistent() )
				{
					$nodes[ $node->Node()->getId() ] = $node->getArrayCopy();
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
		$this->offsetSet( kAPI_DATA_RESPONSE, $nodes );
	
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
		// Add kAPI_OP_QUERY_ONTOLOGIES.
		//
		$theList[ kAPI_OP_QUERY_ONTOLOGIES ]
			= 'This operation will return the list of ontology nodes matching the provided '
			.'attribute/value pairs in ['
			.kAPI_OPT_NODE_SELECTORS
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

	 

} // class CWarehouseWrapper.


?>
