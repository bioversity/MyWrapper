<?php

/**
 * <i>CDataWrapper</i> class definition.
 *
 * This file contains the class definition of <b>CDataWrapper</b> which overloads its
 * {@link CWrapper ancestor} to implement a data store wrapper.
 *
 *	@package	Framework
 *	@subpackage	Wrappers
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 05/06/2011
 *				2.00 22/02/2012
 */

/*=======================================================================================
 *																						*
 *									CDataWrapper.php									*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CWrapper.php" );

/**
 * Query definitions.
 *
 * This include file contains the definitions of the {@link CQuery query} class.
 */
require_once( kPATH_LIBRARY_SOURCE."CQuery.php" );

/**
 * Local definitions.
 *
 * This include file contains all local definitions to this class.
 */
require_once( kPATH_LIBRARY_SOURCE."CDataWrapper.inc.php" );

/**
 *	Data wrapper.
 *
 * This class overloads its {@link CWrapper ancestor} to implement a web service that wraps
 * a data store, it represents a framework for building concrete data store web-service
 * wrappers.
 *
 * The class introduces a series of new operations and filter options that must be
 * implemented in derived classes which implement a specific data store.
 *
 * These new functionalities require a new set of parameters:
 *
 * <ul>
 *	<li><i>Data store parameters</i>: In order to refer to a specific data store we need
 *		two parameters:
 *	 <ul>
 *		<li><i>{@link kAPI_DATABASE kAPI_DATABASE}</i>: <i>Database</i>, this parameter
 *			should indicate the database or equivalent concept where the data should be
 *			stored or retrieved. This parameter can be compared to the database part of an
 *			SQL table reference (<i>DATABASE</i>.TABLE).
 *		<li><i>{@link kAPI_CONTAINER kAPI_CONTAINER}</i>: <i>Container</i>, this parameter
 *			should indicate which container within the {@link kAPI_DATABASE database} should
 *			be used to store or retrieve the data. This parameter can be compared to the
 *			table part of an SQL table reference (DATABASE.<i>TABLE</i>).
 *	 </ul>
 *	<li><i>Paging parameters</i>: Query results may possibly return large amounts of data,
 *		this means that a paging mechanism should be set in place:
 *	 <ul>
 *		<li><i>{@link kAPI_PAGE_START kAPI_PAGE_START}</i>: <i>Page start</i>, this
 *			parameter indicates the starting page or record.
 *		<li><i>{@link kAPI_PAGE_LIMIT kAPI_PAGE_LIMIT}</i>: <i>Page count</i>, this
 *			parameter indicates the maximum number of pages or records that the operation
 *			should return.
 *	 </ul>
 *	<li><i>Data parameters</i>: A query is formed by a series of sections, each of which is
 *		provided with the following parameters:
 *	 <ul>
 *		<li><i>{@link kAPI_DATA_QUERY kAPI_DATA_QUERY}</i>: <i>Query</i>, this parameter is
 *			used when retrieving data, it represents the filter or selection query; it must
 *			be expressed as one of the query {@link CQuery class} siblings. This parameter
 *			can be compared to the <i>WHERE</i> part of an SQL query.
 *		<li><i>{@link kAPI_DATA_FIELD kAPI_DATA_FIELD}</i>: <i>Fields</i>, this parameter
 *			indicates which elements of the selected objects we want returned. This
 *			parameter can be compared to the <i>SELECT</i> part of an SQL query.
 *		<li><i>{@link kAPI_DATA_SORT kAPI_DATA_SORT}</i>: <i>Sort fields</i>, this parameter
 *			indicates the sort order, the list of data elements by which the result is to
 *			be sorted. This parameter can be compared to the {@link kAPI_DATA_FIELD field}
 *			parameter or to the <i>ORDER BY</i> part of an SQL query.
 *		<li><i>{@link kAPI_DATA_OBJECT kAPI_DATA_OBJECT}</i>: <i>Object data</i>, this
 *			parameter represents the data to be stored in the database, the type should
 *			ideally be abstracted from the data store engine. This parameter can be compared
 *			to the <i>VALUES</i> or <i>SET</i> part of an SQL query.
 *		<li><i>{@link kAPI_DATA_OPTIONS kAPI_DATA_OPTIONS}</i>: <i>Options</i>, this
 *			parameter represents the options governing data store and retrieve operations.
 *			In general it will cover the options when storing data and the actual
 *			implementation is the responsibility of derived classes:
 *		 <ul>
 *			<li><i>{@link kAPI_OPT_SAFE kAPI_OPT_SAFE}</i>: Safe commit option, this is
 *				relevant only when committing data. If this option is <i>OFF</i>, it means
 *				we want to perform an asynchronous operation: the store operation will occur
 *				in the background and the program execution will not wait for it to finish;
 *				this also means that the client is responsible for checking whether the
 *				operation completed. If the option is <i>ON</i>, the operation is
 *				synchronous, which means that the program will wait for the store operation
 *				to complete.
 *			<li><i>{@link kAPI_OPT_FSYNC kAPI_OPT_FSYNC}</i>: File sync option, this tag is
 *				relevant only when committing data. If the option is <i>ON</i>, it means
 *				that the store operation will wait until the data is actually written to
 *				disk; which may not necessarily be the case even if the
 *				{@link kAPI_OPT_SAFE kAPI_OPT_SAFE} option was on. When this option is set,
 *				it is implied that the {@link kAPI_OPT_SAFE kAPI_OPT_SAFE} option is also
 *				on. If the option is <i>OFF</i>, it means that the data will be synched to
 *				disk only when the buffer is flushed.
 *			<li><i>{@link kAPI_OPT_TIMEOUT kAPI_OPT_TIMEOUT}</i>: Operation timeout, it
 *				represents the time in milliseconds beyond which the client will stop
 *				waiting for a response and expect a time out status.
 *			<li><i>{@link kAPI_OPT_SINGLE kAPI_OPT_SINGLE}</i>: First element, this option
 *				is used by the {@link kAPI_OP_DEL delete} operation: if <i>ON</i>, only the
 *				first object satisfying the {@link kAPI_DATA_QUERY query} will be deleted;
 *				if <i>OFF</i> all selected elements will be deleted.
 *			<li><i>{@link kAPI_OPT_SINGLE kAPI_OPT_SINGLE}</i>: First element, this option
 *		 </ul>
 *	 </ul>
 * </ul>
 *
 * The new operations declared in this class are:
 *
 * <ul>
 *	<li><i>{@link kAPI_OP_COUNT kAPI_OP_COUNT}</i>: This operation requests a count, which
 *		is an integer indicating the total number of elements satisfying the provided
 *		{@link kAPI_DATA_QUERY query}. This number is not to be confused with the page
 *		element {@link kAPI_PAGE_COUNT count} described further.
 *	<li><i>{@link kAPI_OP_GET kAPI_OP_GET}</i>: This operation is equivalent to a read
 *		query, it requests a list of objects satisfying the provided
 *		{@link kAPI_DATA_QUERY query}.
 *	<li><i>{@link kAPI_OP_SET kAPI_OP_SET}</i>: This operation is equivalent to an insert
 *		for new objects or an update for existing objects, the operation will replace the
 *		object in the data store with the one provided in the
 *		{@link kAPI_DATA_OBJECT kAPI_DATA_OBJECT} parameter.
 *	<li><i>{@link kAPI_OP_UPDATE kAPI_OP_UPDATE}</i>: This operation is equivalent to an
 *		update operation, this implies that the object must already exist in the data store
 *		and that the operation will replace the object in the data store with the one
 *		provided in the {@link kAPI_DATA_OBJECT kAPI_DATA_OBJECT} parameter.
 *	<li><i>{@link kAPI_OP_INSERT kAPI_OP_INSERT}</i>: This operation is equivalent to an
 *		insert operation, this implies that the object must not already exist in the data
 *		store.
 *	<li><i>{@link kAPI_OP_MODIFY kAPI_OP_MODIFY}</i>: This operation indicates that we want
 *		to modify the contents of an existing object and that the
 *		{@link kAPI_DATA_OBJECT provided} data represents only the changed elements.
 *	<li><i>{@link kAPI_OP_DEL kAPI_OP_DEL}</i>: This operation indicates that we want to
 *		delete the elements matching the provided {@link kAPI_DATA_QUERY query}: the
 *		first one only, if the provided {@link kAPI_OPT_SINGLE kAPI_OPT_SINGLE} option is
 *		on, or all if off or omitted.
 * </ul>
 *
 * The added functionality implies that a series of additional sections will be returned in
 * the response:
 *
 * <ul>
 *	<li><i>{@link kAPI_DATA_PAGING kAPI_DATA_PAGING}</i>: Paging section, this section will
 *		return the paging information of the current operation, besides the provided
 *		{@link kAPI_PAGE_START start} and {@link kAPI_PAGE_LIMIT limit} parameters, it will
 *		also feature:
 *	 <ul>
 *		<li><i>{@link kAPI_PAGE_COUNT kAPI_PAGE_COUNT}</i>: This element will hold the
 *			actual number of returned objects, this number will be either equal or smaller
 *			than the provided {@link kAPI_PAGE_LIMIT limit} parameter. 
 *	 </ul>
 *	<li><i>{@link kAPI_DATA_RESPONSE kAPI_DATA_RESPONSE}</i>: Response, this section will
 *		hold the results of the operation.
 * </ul>
 *
 *	@package	Framework
 *	@subpackage	Wrappers
 */
class CDataWrapper extends CWrapper
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
	 * We overload this method to normalise the {@link kAPI_DATA_PAGING paging} options.
	 *
	 * @access private
	 *
	 * @see kAPI_PAGE_START kAPI_PAGE_LIMIT kAPI_DATA_PAGING
	 */
	protected function _InitOptions()
	{
		//
		// Call parent method.
		//
		parent::_InitOptions();
		
		//
		// Check paging option.
		//
		if( array_key_exists( kAPI_PAGE_START, $_REQUEST )
		 || array_key_exists( kAPI_PAGE_LIMIT, $_REQUEST ) )
		{
			//
			// Check limit.
			//
			if( array_key_exists( kAPI_PAGE_LIMIT, $_REQUEST ) )
			{
				//
				// Set start.
				//
				if( ! array_key_exists( kAPI_PAGE_START, $_REQUEST ) )
					$_REQUEST[ kAPI_PAGE_START ] = 0;
			
			} // Provided limit.
			
			//
			// Handle start only.
			//
			elseif( $_REQUEST[ kAPI_PAGE_START ] == 0 )
				return;																// ==>
			
			//
			// Create paging container.
			//
			$this->offsetSet( kAPI_DATA_PAGING, Array() );
		
		} // Provided paging options.
	
	} // _InitOptions.

		

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
	 * This method should be used to parse the request, check the request elements and make
	 * any necessary adjustments before the request is {@link _ValidateRequest() validated}.
	 *
	 * This is also where the relevant request elements will be logged to the relative
	 * response sections.
	 *
	 * The method is called by the {@link __construct() constructor} and should be
	 * overloaded to handle derived classes custom elements.
	 *
	 * In this class we handle the paging request.
	 *
	 * @access private
	 *
	 * @uses _ParseRequest()
	 * @uses _ParsePaging()
	 * @uses _ParseDatabase()
	 * @uses _ParseContainer()
	 * @uses _ParseQuery()
	 * @uses _ParseFields()
	 * @uses _ParseSort()
	 * @uses _ParseObject()
	 * @uses _ParseOptions()
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
		$this->_ParsePaging();
		$this->_ParseDatabase();
		$this->_ParseContainer();
		$this->_ParseQuery();
		$this->_ParseFields();
		$this->_ParseSort();
		$this->_ParseObject();
		$this->_ParseOptions();
	
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
	 * @access private
	 *
	 * @uses _FormatRequest()
	 * @uses _FormatQuery()
	 * @uses _FormatFields()
	 * @uses _FormatSort()
	 * @uses _FormatObject()
	 * @uses _FormatOptions()
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
		$this->_FormatQuery();
		$this->_FormatFields();
		$this->_FormatSort();
		$this->_FormatObject();
		$this->_FormatOptions();
	
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
	 * In this class we check the {@link kAPI_FORMAT format} and
	 * {@link kAPI_OPERATION operation} codes (their presence is checked by the
	 * {@link __construct() constructor}.
	 *
	 * @access private
	 *
	 * @uses _ValidateRequest()
	 * @uses _ValidateFields()
	 * @uses _ValidateSort()
	 * @uses _ValidateOptions()
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
		$this->_ValidateFields();
		$this->_ValidateSort();
		$this->_ValidateOptions();
	
	} // _ValidateRequest.

		

/*=======================================================================================
 *																						*
 *								PROTECTED PARSING UTILITIES								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_ParsePaging																	*
	 *==================================================================================*/

	/**
	 * Parse paging.
	 *
	 * This method will parse the request pager.
	 *
	 * @access private
	 *
	 * @uses _OffsetManage()
	 *
	 * @see kAPI_PAGE_START kAPI_PAGE_LIMIT kAPI_DATA_PAGING kAPI_DATA_REQUEST
	 */
	protected function _ParsePaging()
	{
		//
		// Note: the kAPI_DATA_PAGING offset was created by _InitOptions().
		//
		
		//
		// Handle paging.
		//
		if( $this->offsetExists( kAPI_DATA_PAGING ) )
		{
			//
			// Init pagers block.
			//
			$options = Array();
			$tags = array( kAPI_PAGE_START, kAPI_PAGE_LIMIT );
			
			//
			// Handle options.
			//
			foreach( $tags as $tag )
			{
				//
				// Check option.
				//
				if( array_key_exists( $tag, $_REQUEST ) )
				{
					//
					// Set option.
					//
					$options[ $tag ] = $_REQUEST[ $tag ];
					
					//
					// Log to request.
					//
					if( $this->offsetExists( kAPI_DATA_REQUEST ) )
						$this->_OffsetManage( kAPI_DATA_REQUEST, $tag, $_REQUEST[ $tag ] );
				
				} // Has page start.
			
			} // Iterating options.
			
			//
			// Update block.
			//
			$this[ kAPI_DATA_PAGING ] = $options;
		
		} // Provided paging options.
	
	} // _ParsePaging.

	 
	/*===================================================================================
	 *	_ParseDatabase																	*
	 *==================================================================================*/

	/**
	 * Parse database.
	 *
	 * This method will parse the request database.
	 *
	 * @access private
	 *
	 * @uses _OffsetManage()
	 *
	 * @see kAPI_DATABASE kAPI_DATA_REQUEST
	 */
	protected function _ParseDatabase()
	{
		//
		// Handle database.
		//
		if( array_key_exists( kAPI_DATABASE, $_REQUEST ) )
		{
			if( $this->offsetExists( kAPI_DATA_REQUEST ) )
				$this->_OffsetManage
					( kAPI_DATA_REQUEST, kAPI_DATABASE, $_REQUEST[ kAPI_DATABASE ] );
		}
	
	} // _ParseDatabase.

	 
	/*===================================================================================
	 *	_ParseContainer																	*
	 *==================================================================================*/

	/**
	 * Parse container.
	 *
	 * This method will parse the request container.
	 *
	 * @access private
	 *
	 * @uses _OffsetManage()
	 *
	 * @see kAPI_CONTAINER kAPI_DATA_REQUEST
	 */
	protected function _ParseContainer()
	{
		//
		// Handle database.
		//
		if( array_key_exists( kAPI_CONTAINER, $_REQUEST ) )
		{
			if( $this->offsetExists( kAPI_DATA_REQUEST ) )
				$this->_OffsetManage
					( kAPI_DATA_REQUEST, kAPI_CONTAINER, $_REQUEST[ kAPI_CONTAINER ] );
		}
	
	} // _ParseContainer.

	 
	/*===================================================================================
	 *	_ParseQuery																		*
	 *==================================================================================*/

	/**
	 * Parse query.
	 *
	 * This method will parse the request query.
	 *
	 * @access private
	 *
	 * @uses _OffsetManage()
	 *
	 * @see kAPI_DATA_QUERY kAPI_DATA_REQUEST
	 */
	protected function _ParseQuery()
	{
		//
		// Handle query.
		//
		if( array_key_exists( kAPI_DATA_QUERY, $_REQUEST ) )
		{
			if( $this->offsetExists( kAPI_DATA_REQUEST ) )
				$this->_OffsetManage
					( kAPI_DATA_REQUEST, kAPI_DATA_QUERY, $_REQUEST[ kAPI_DATA_QUERY ] );
		}
	
	} // _ParseQuery.

	 
	/*===================================================================================
	 *	_ParseFields																	*
	 *==================================================================================*/

	/**
	 * Parse fields.
	 *
	 * This method will parse the request fields.
	 *
	 * @access private
	 *
	 * @uses _OffsetManage()
	 *
	 * @see kAPI_DATA_FIELD kAPI_DATA_REQUEST
	 */
	protected function _ParseFields()
	{
		//
		// Handle fields.
		//
		if( array_key_exists( kAPI_DATA_FIELD, $_REQUEST ) )
		{
			if( $this->offsetExists( kAPI_DATA_REQUEST ) )
				$this->_OffsetManage
					( kAPI_DATA_REQUEST, kAPI_DATA_FIELD, $_REQUEST[ kAPI_DATA_FIELD ] );
		}
	
	} // _ParseFields.

	 
	/*===================================================================================
	 *	_ParseSort																		*
	 *==================================================================================*/

	/**
	 * Parse sort.
	 *
	 * This method will parse the request sort.
	 *
	 * @access private
	 *
	 * @uses _OffsetManage()
	 *
	 * @see kAPI_DATA_SORT kAPI_DATA_REQUEST
	 */
	protected function _ParseSort()
	{
		//
		// Handle sort.
		//
		if( array_key_exists( kAPI_DATA_SORT, $_REQUEST ) )
		{
			if( $this->offsetExists( kAPI_DATA_REQUEST ) )
				$this->_OffsetManage
					( kAPI_DATA_REQUEST, kAPI_DATA_SORT, $_REQUEST[ kAPI_DATA_SORT ] );
		}
	
	} // _ParseSort.

	 
	/*===================================================================================
	 *	_ParseObject																	*
	 *==================================================================================*/

	/**
	 * Parse object.
	 *
	 * This method will parse the request object.
	 *
	 * @access private
	 *
	 * @uses _OffsetManage()
	 *
	 * @see kAPI_DATA_OBJECT kAPI_DATA_REQUEST
	 */
	protected function _ParseObject()
	{
		//
		// Handle object.
		//
		if( array_key_exists( kAPI_DATA_OBJECT, $_REQUEST ) )
		{
			if( $this->offsetExists( kAPI_DATA_REQUEST ) )
				$this->_OffsetManage
					( kAPI_DATA_REQUEST, kAPI_DATA_OBJECT, $_REQUEST[ kAPI_DATA_OBJECT ] );
		}
	
	} // _ParseObject.

	 
	/*===================================================================================
	 *	_ParseOptions																	*
	 *==================================================================================*/

	/**
	 * Parse options.
	 *
	 * This method will parse the request options.
	 *
	 * @access private
	 *
	 * @uses _OffsetManage()
	 *
	 * @see kAPI_DATA_OPTIONS kAPI_DATA_REQUEST
	 */
	protected function _ParseOptions()
	{
		//
		// Handle object.
		//
		if( array_key_exists( kAPI_DATA_OPTIONS, $_REQUEST ) )
		{
			if( $this->offsetExists( kAPI_DATA_REQUEST ) )
				$this->_OffsetManage
					( kAPI_DATA_REQUEST, kAPI_DATA_OPTIONS, $_REQUEST[ kAPI_DATA_OPTIONS ] );
		}
	
	} // _ParseOptions.

		

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
	 * @access private
	 *
	 * @uses _DecodeParameter()
	 *
	 * @see kAPI_DATA_QUERY
	 */
	protected function _FormatQuery()		{	$this->_DecodeParameter( kAPI_DATA_QUERY );	}

	 
	/*===================================================================================
	 *	_FormatFields																	*
	 *==================================================================================*/

	/**
	 * Format fields.
	 *
	 * This method will format the request fields.
	 *
	 * @access private
	 *
	 * @uses _DecodeParameter()
	 *
	 * @see kAPI_DATA_FIELD
	 */
	protected function _FormatFields()		{	$this->_DecodeParameter( kAPI_DATA_FIELD );	}

	 
	/*===================================================================================
	 *	_FormatSort																		*
	 *==================================================================================*/

	/**
	 * Format sort.
	 *
	 * This method will format the request sort.
	 *
	 * @access private
	 *
	 * @uses _DecodeParameter()
	 *
	 * @see kAPI_DATA_SORT
	 */
	protected function _FormatSort()		{	$this->_DecodeParameter( kAPI_DATA_SORT );	}

	 
	/*===================================================================================
	 *	_FormatObject																	*
	 *==================================================================================*/

	/**
	 * Format object.
	 *
	 * This method will format the request object.
	 *
	 * @access private
	 *
	 * @uses _DecodeParameter()
	 *
	 * @see kAPI_DATA_OBJECT
	 */
	protected function _FormatObject()	{	$this->_DecodeParameter( kAPI_DATA_OBJECT );	}

	 
	/*===================================================================================
	 *	_FormatOptions																	*
	 *==================================================================================*/

	/**
	 * Format options.
	 *
	 * This method will format the request options.
	 *
	 * @access private
	 *
	 * @uses _DecodeParameter()
	 *
	 * @see kAPI_DATA_OPTIONS
	 */
	protected function _FormatOptions()	{	$this->_DecodeParameter( kAPI_DATA_OPTIONS );	}

		

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
	 * @access private
	 *
	 * @see kAPI_OPERATION kAPI_OP_SET kAPI_OP_INSERT
	 * @see kAPI_DATABASE kAPI_CONTAINER kAPI_DATA_OBJECT kAPI_DATA_QUERY
	 */
	protected function _ValidateOperation()
	{
		//
		// Parse operation.
		//
		switch( $parameter = $_REQUEST[ kAPI_OPERATION ] )
		{
			case kAPI_OP_SET:
			case kAPI_OP_INSERT:
				
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
				// Check for object.
				//
				if( ! array_key_exists( kAPI_DATA_OBJECT, $_REQUEST ) )
					throw new CException
						( "Missing object reference",
						  kERROR_OPTION_MISSING,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Operation' => $parameter ) );					// !@! ==>
				break;

			case kAPI_OP_GET:
			case kAPI_OP_COUNT:
				
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
				break;

			case kAPI_OP_UPDATE:
			case kAPI_OP_MODIFY:
				
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
				// Check for query.
				//
				if( ! array_key_exists( kAPI_DATA_QUERY, $_REQUEST ) )
					throw new CException
						( "Missing query reference",
						  kERROR_OPTION_MISSING,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Operation' => $parameter ) );					// !@! ==>
				
				//
				// Check for object.
				//
				if( ! array_key_exists( kAPI_DATA_OBJECT, $_REQUEST ) )
					throw new CException
						( "Missing object reference",
						  kERROR_OPTION_MISSING,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Operation' => $parameter ) );					// !@! ==>
				break;

			case kAPI_OP_DEL:
				
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
				// Check for query.
				//
				if( ! array_key_exists( kAPI_DATA_QUERY, $_REQUEST ) )
					throw new CException
						( "Missing query reference",
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

	 
	/*===================================================================================
	 *	_ValidateFields																	*
	 *==================================================================================*/

	/**
	 * Validate field selection reference.
	 *
	 * This method can be used to check whether the provided
	 * {@link kAPI_DATA_FIELD field} parameter is valid.
	 *
	 * In this class we ensure that the fields list is an array.
	 *
	 * @access private
	 *
	 * @see kAPI_DATA_FIELD
	 */
	protected function _ValidateFields()
	{
		//
		// Check fields.
		//
		if( array_key_exists( kAPI_DATA_FIELD, $_REQUEST ) )
		{
			//
			// Convert to array.
			//
			if( $_REQUEST[ kAPI_DATA_FIELD ] instanceof ArrayObject )
				$_REQUEST[ kAPI_DATA_FIELD ]
					= $_REQUEST[ kAPI_DATA_FIELD ]->getArrayCopy();
			
			//
			// Check type.
			//
			if( ! is_array( $_REQUEST[ kAPI_DATA_FIELD ] ) )
				throw new CException
					( "Invalid fields list data type: must be an array",
					  kERROR_INVALID_PARAMETER,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Fields' => $_REQUEST[ kAPI_DATA_FIELD ] ) );		// !@! ==>
		
		} // Provided fields.
	
	} // _ValidateFields.

	 
	/*===================================================================================
	 *	_ValidateSort																	*
	 *==================================================================================*/

	/**
	 * Validate sort selection reference.
	 *
	 * This method can be used to check whether the provided
	 * {@link kAPI_DATA_SORT sort} parameter is valid.
	 *
	 * In this class we ensure that the sort list is an array.
	 *
	 * @access private
	 *
	 * @see kAPI_DATA_SORT
	 */
	protected function _ValidateSort()
	{
		//
		// Check sort.
		//
		if( array_key_exists( kAPI_DATA_SORT, $_REQUEST ) )
		{
			//
			// Convert to array.
			//
			if( $_REQUEST[ kAPI_DATA_SORT ] instanceof ArrayObject )
				$_REQUEST[ kAPI_DATA_SORT ]
					= $_REQUEST[ kAPI_DATA_SORT ]->getArrayCopy();
			
			//
			// Check type.
			//
			if( ! is_array( $_REQUEST[ kAPI_DATA_SORT ] ) )
				throw new CException
					( "Invalid sort list data type: must be an array",
					  kERROR_INVALID_PARAMETER,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Fields' => $_REQUEST[ kAPI_DATA_SORT ] ) );		// !@! ==>
		
		} // Provided sort.
	
	} // _ValidateSort.

	 
	/*===================================================================================
	 *	_ValidateOptions																*
	 *==================================================================================*/

	/**
	 * Validate sort selection reference.
	 *
	 * This method can be used to check whether the provided
	 * {@link kAPI_DATA_SORT sort} parameter is valid.
	 *
	 * In this class we ensure that the sort list is an array.
	 *
	 * @access private
	 *
	 * @see kAPI_DATA_OPTIONS
	 */
	protected function _ValidateOptions()
	{
		//
		// Check options.
		//
		if( array_key_exists( kAPI_DATA_OPTIONS, $_REQUEST ) )
		{
			//
			// Convert to array.
			//
			if( $_REQUEST[ kAPI_DATA_OPTIONS ] instanceof ArrayObject )
				$_REQUEST[ kAPI_DATA_OPTIONS ]
					= $_REQUEST[ kAPI_DATA_OPTIONS ]->getArrayCopy();
			
			//
			// Check type.
			//
			if( ! is_array( $_REQUEST[ kAPI_DATA_OPTIONS ] ) )
				throw new CException
					( "Invalid options list data type: must be an array",
					  kERROR_INVALID_PARAMETER,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Options' => $_REQUEST[ kAPI_DATA_OPTIONS ] ) );	// !@! ==>
		
		} // Provided options.
	
	} // _ValidateOptions.

		

/*=======================================================================================
 *																						*
 *									PROTECTED UTILITIES									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_DecodeParameter																*
	 *==================================================================================*/

	/**
	 * Decode parameter.
	 *
	 * This method can be used to decode a parameter according to the provided format,
	 * {@link kDATA_TYPE_JSON JSON} or {@link kDATA_TYPE_PHP PHP}.
	 *
	 * The method will return the decoded parameter.
	 *
	 * @param string				$theParameter		Parameter offset.
	 *
	 * @access private
	 * @return array
	 *
	 * @uses CObject::JsonDecode()
	 *
	 * @see kDATA_TYPE_JSON kDATA_TYPE_PHP
	 */
	protected function _DecodeParameter( $theParameter )
	{
		//
		// Check parameter.
		//
		if( array_key_exists( $theParameter, $_REQUEST ) )
		{
			//
			// Init local storage.
			//
			$encoded = $_REQUEST[ $theParameter ];
			$format = $_REQUEST[ kAPI_FORMAT ];
			
			//
			// Parse by format.
			//
			switch( $format )
			{
				case kDATA_TYPE_JSON:
					try
					{
						$_REQUEST[ $theParameter ] = CObject::JsonDecode( $encoded );
					}
					catch( Exception $error )
					{
						if( $error instanceof CException )
						{
							$error->Reference( 'Parameter', $theParameter );
							$error->Reference( 'Format', $format );
							$error->Reference( 'Data', $encoded );
						}
						
						throw $error;											// !@! ==>
					}
					
					break;

				case kDATA_TYPE_PHP:
					$decoded = @unserialize( $encoded );
					if( $decoded === FALSE )
						throw new CException
							( "Unable to handle request: invalid PHP serialised string",
							  kERROR_INVALID_STATE,
							  kMESSAGE_TYPE_ERROR,
							  array( 'Parameter' => $theParameter,
									 'Format' => $format,
									 'Data' => $encoded ) );					// !@! ==>
					
					//
					// Update request.
					//
					$_REQUEST[ $theParameter ] = $decoded;
					
					break;
				
				//
				// Catch bugs.
				//
				default:
					throw new CException
						( "Unsupported format (should have been caught before)",
						  kERROR_UNSUPPORTED,
						  kMESSAGE_TYPE_BUG,
						  array( 'Parameter' => kAPI_FORMAT,
								 'Format' => $format ) );						// !@! ==>
			
			} // Parsed format.
		
		} // Provided parameter.
	
	} // _DecodeParameter.

	 

} // class CDataWrapper.


?>
