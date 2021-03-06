<?php

/**
 * <i>CWrapper</i> class definition.
 *
 * This file contains the class definition of <b>CWrapper</b> which represents a web-service
 * wrapper server.
 *
 *	@package	MyWrapper
 *	@subpackage	Wrappers
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 03/06/2011
 *				2.00 22/02/2012
 */

/*=======================================================================================
 *																						*
 *										CWrapper.php									*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CStatusObject.php" );

/**
 * Types.
 *
 * This include file contains all data type definitions.
 */
require_once( kPATH_LIBRARY_DEFINES."Types.inc.php" );

/**
 * Local definitions.
 *
 * This include file contains all local definitions to this class.
 */
require_once( kPATH_LIBRARY_SOURCE."CWrapper.inc.php" );

/**
 *	Wrapper.
 *
 * This class represents a web-services wrapper server, it is the ancestor of web-service
 * classes in this library.
 *
 * Wrappers are objects that respond to standard web calls and receive parameters in both
 * <i>GET</i> and <i>POST</i> parameters, the main two parameters handled by this class are:
 *
 * <ul>
 *	<li><i>{@link kAPI_FORMAT kAPI_FORMAT}</i> (required): The data format of the response,
 *		 it will be returned as:
 *	 <ul>
 *		<li><i>{@link kTYPE_PHP kTYPE_PHP}</i>: A PHP-serialised string.
 *		<li><i>{@link kTYPE_JSON kTYPE_JSON}</i>: A JSON-serialised string.
 *	 </ul>
 *	<li><i>{@link kAPI_OPERATION kAPI_OPERATION}</i> (required): The requested operation,
 *		each class will implement specialised handlers, this class only implements the
 *		following two operations:
 *	 <ul>
 *		<li><i>{@link kAPI_OP_HELP kAPI_OP_HELP}</i>: A <i>LIST-OP</i> command, this
 *			command will return in the {@link kAPI_DATA_RESPONSE response} section the list
 *			of supported operations as an array structured as follows:
 *		 <ul>
 *			<li><i>index</i>: The index will be the operation code.
 *			<li><i>value</i>: The value will be the operation description.
 *		 </ul>
 *		<li><i>{@link kAPI_OP_PING kAPI_OP_PING}</i>: A <i>PING</i> command, this can be
 *			used to check if a service is alive.
 *	 </ul>
 * </ul>
 *
 * If both the above parameters are present, the service will return an array constituted by
 * the following three sections:
 *
 * <ul>
 *	<li><i>{@link kAPI_DATA_STATUS kAPI_DATA_STATUS}</i>: <i>Operation status</i>.
 *		This section is returned by default and will inform on the status of the requested
 *		operation. It consists of an array containing the following elements:
 *	 <ul>
 *		<li><i>{@link kTAG_STATUS kTAG_STATUS}</i>: <i>Response status</i>.
 *			This element will be returned by default regardless of the operation outcome.
 *			This corresponds to the severity of the response and it can take the following
 *			values:
 *		 <ul>
 *			<li><i>{@link kMESSAGE_TYPE_IDLE kMESSAGE_TYPE_IDLE}</i>: This is the status of
 *				the web-service before any operation has been executed, or when the
 *				operation was successful; this is the response of a successful
 *				{@link kAPI_OP_PING ping} request.
 *			<li><i>{@link kMESSAGE_TYPE_NOTICE kMESSAGE_TYPE_NOTICE}</i>: The operation was
 *				successful and a notice message was returned.
 *			<li><i>{@link kMESSAGE_TYPE_MESSAGE kMESSAGE_TYPE_MESSAGE}</i>: The operation
 *				was successful and a message was returned.
 *			<li><i>{@link kMESSAGE_TYPE_WARNING kMESSAGE_TYPE_WARNING}</i>: The operation
 *				was successful but a warning was raised.
 *			<li><i>{@link kMESSAGE_TYPE_ERROR kMESSAGE_TYPE_ERROR}</i>: The operation failed
 *				because of an error.
 *			<li><i>{@link kMESSAGE_TYPE_FATAL kMESSAGE_TYPE_FATAL}</i>: The operation failed
 *				because of a fatal error, this will generally mean that the web-service is
 *				not operational.
 *			<li><i>{@link kMESSAGE_TYPE_BUG kMESSAGE_TYPE_BUG}</i>: The operation failed
 *				because of a bug, the developers should be informed of this kind of errors.
 *		 </ul>
 *		<li><i>{@link kTAG_CODE kTAG_CODE}</i>: <i>Status code</i>.
 *			This element will be returned by default regardless of the operation outcome.
 *			It corresponds to the error code; {@link kERROR_OK zero} means no error.
 *		<li><i>{@link kTAG_DESCRIPTION kTAG_DESCRIPTION}</i>: <i>Status message</i>.
 *			The response message from the operation, this element is used to return
 *			informative messages or to return error messages when the service fails. It will
 *			generally be formatted as an array structured as follows:
 *		 <ul>
 *			<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>: The data type of the message, it will be
 *				a {@link kTYPE_STRING string} in general.
 *			<li><i>{@link kTAG_LANGUAGE kTAG_LANGUAGE}</i>: The language ISO 639 2
 *				character code in which the message is expressed in.
 *			<li><i>{@link kTAG_DATA kTAG_DATA}</i>: The actual message data contents.
 *		 </ul>
 *		<li><i>{@link kAPI_AFFECTED_COUNT kAPI_AFFECTED_COUNT}</i>: <i>Record count</i>.
 *			The total number of elements affected by the operation. This tag will only be
 *			used by derived classes returning data elements.
 *		<li><i>{@link kTAG_ANNOTATION kTAG_ANNOTATION}</i>: <i>Attachments</i>.
 *				A list of key/value pairs containing information relevant to the operation
 *				response. For instance, if a series of parameters are required and were not
 *				provided, this could list them.
 *	 </ul>
 *	<li><i>{@link kAPI_DATA_REQUEST kAPI_DATA_REQUEST}</i>: <i>Service request</i>.
 *		This section will return the actual request provided to the service, this can be
 *		used for debugging purposes and will only occur if the optional
 *		{@link kAPI_OPT_LOG_REQUEST kAPI_OPT_LOG_REQUEST} parameter has been set to 1.
 *	<li><i>{@link kAPI_DATA_TIMING kAPI_DATA_TIMING}</i>: <i>Timers</i>.
 *		This section holds timing information, it will be returned only if you provide the
 *		time of day [<i>gettimeofday( TRUE )</i>] in the
 *		{@link kAPI_REQ_STAMP kAPI_REQ_STAMP} parameter. This section is structured as
 *		follows:
 *	 <ul>
 *		<li><i>{@link kAPI_REQ_STAMP kAPI_REQ_STAMP}</i>: Request time stamp, the time in
 *			which the request was sent; this is the same value sent by the caller in the
 *			{@link kAPI_REQ_STAMP kAPI_REQ_STAMP} parameter.
 *		<li><i>{@link kAPI_PARSE_STAMP kAPI_PARSE_STAMP}</i>: Parse time stamp, the time in
 *			which the service finished parsing the request.
 *		<li><i>{@link kAPI_RES_STAMP kAPI_RES_STAMP}</i>: Response time stamp, the time in
 *			which the response was sent.
 *	 </ul>
 *	<li><i>{@link kAPI_DATA_RESPONSE kAPI_DATA_RESPONSE}</i>: Response, this section will
 *		hold the operation response, in this class we only respond to
 *		{@link kAPI_OP_HELP operations} list requests.
 * </ul>
 *
 * Besides the {@link kAPI_FORMAT format} and {@link kAPI_OPERATION operation} parameters
 * described in the first section, we have three other optional parameters that can be used
 * to receive specific information sections in the response:
 *
 * <ul>
 *	<li><i>{@link kAPI_OPT_LOG_REQUEST kAPI_OPT_LOG_REQUEST}</i>: Log the request, if the
 *		value of this parameter is 1, the response will contain the received request in the
 *		{@link kAPI_DATA_REQUEST request} section.
 *	<li><i>{@link kAPI_OPT_LOG_TRACE kAPI_OPT_LOG_TRACE}</i>: Trace exceptions, if the
 *		value of this parameter is 1, in the case of an error that triggered an exception,
 *		the error response will also include the call trace.
 *	<li><i>{@link kAPI_REQ_STAMP kAPI_REQ_STAMP}</i>: This parameter should hold the
 *		timestamp [<i>gettimeofday( TRUE )</i>] in which the client has sent the request,
 *		if provided, the service will return the timing information in the
 *		{@link kAPI_DATA_TIMING timing} section.
 * </ul>
 *
 * The parameters are expected either in <i>GET</i> or <i>POST</i>.
 *
 * If either the {@link kAPI_FORMAT format} or the {@link kAPI_OPERATION operation}
 * parameters are omitted from the request, the service will return an empty response; this
 * is to prevent unnecessary traffic.
 *
 * Instances of this class can be considered server objects, and can be implemented with
 * this simple code snippet:
 *
 * <code>
 * $server = new CWrapper();
 * $server->HandleRequest();
 * </code>
 *
 * An example of this class implementation can be found in the
 * {@link Wrapper.php Wrapper.php} source file.
 *
 *	@package	MyWrapper
 *	@subpackage	Wrappers
 */
class CWrapper extends CStatusObject
{
	/**
	 * Reception time-stamp.
	 *
	 * This data member holds the request reception time stamp.
	 *
	 * @var integer
	 */
	 protected $mReceivedStamp = NULL;

		

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
	 * The constructor will set-up the environment and parse the request.
	 * The workflow is as follows:
	 *
	 * <ul>
	 *	<li><i>Check required elements</i>: The method will check if all required elements
	 *		of the request are there, only if this is the case will the constructor init the
	 *		service.
	 *	<li><i>Init {@link _InitStatus() status}</i>: The response status will be
	 *		initialised to the {@link kMESSAGE_TYPE_IDLE idle} state.
	 *	<li><i>Init {@link _InitOptions() options}</i>: Service options will be initialised.
	 *	<li><i>Init {@link _InitResources() resources}</i>: Eventual resources are
	 *		initialised.
	 *	<li><i>{@link _ParseRequest() Parse} request</i>: The request is parsed.
	 *	<li><i>{@link _FormatRequest() Format} request</i>: The request is normalised if
	 *		necessary.
	 *	<li><i>{@link _ValidateRequest() Validate} request</i>: The request is validated.
	 * </ul>
	 *
	 * This protected interface should be overloaded by derived classes to implement custom
	 * services.
	 *
	 * @access public
	 *
	 * @uses _InitStatus()
	 * @uses _InitOptions()
	 * @uses _InitResources()
	 * @uses _ParseRequest()
	 * @uses _FormatRequest()
	 * @uses _ValidateRequest()
	 * @uses _Exception2Status()
	 * @uses _EncodeResponse()
	 */
	public function __construct()
	{
		//
		// Check dependencies.
		//
		if( isset( $_REQUEST )									// Has request
		 && count( $_REQUEST )									// and request not empty
		 && array_key_exists( kAPI_FORMAT, $_REQUEST )			// and has formst
		 && array_key_exists( kAPI_OPERATION, $_REQUEST ) )		// and has operation.
		{
			//
			// Set reception time.
			//
			$this->mReceivedStamp = gettimeofday( TRUE );
			
			//
			// TRY BLOCK
			//
			try
			{
				//
				// Instantiate object.
				//
				parent::__construct();
			
				//
				// Initialise service.
				//
				$this->_InitStatus();
				$this->_InitOptions();
				$this->_InitResources();
				
				//
				// Parse and validate request.
				//
				$this->_ParseRequest();
				$this->_FormatRequest();
				$this->_ValidateRequest();
					
				//
				// Set parsed timer.
				//
				if( $this->offsetExists( kAPI_DATA_TIMING ) )
					$this->_OffsetManage
						( kAPI_DATA_TIMING, kAPI_PARSE_STAMP, gettimeofday( TRUE ) );
				
				//
				// Set inited status.
				//
				$this->_IsInited( TRUE );
			}
			
			//
			// CATCH BLOCK
			//
			catch( Exception $error )
			{
				//
				// Load exception in status.
				//
				$this->_Exception2Status( $error );
				
				//
				// Return result.
				//
				echo( $this->_EncodeResponse() );
			}
		
		} // Has required elements.

	} // Constructor.

		

/*=======================================================================================
 *																						*
 *							PUBLIC REQUEST HANDLER INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	HandleRequest																	*
	 *==================================================================================*/

	/**
	 * Handle the request.
	 *
	 * This method will handle the request.
	 *
	 * Note that we only run the method if the object is {@link _IsInited() inited}, if this
	 * is not the case, the method will do nothing.
	 *
	 * @access public
	 *
	 * @uses _HandleRequest()
	 * @uses _Exception2Status()
	 * @uses _EncodeResponse()
	 */
	public function HandleRequest()
	{
		//
		// Check object status.
		//
		if( $this->_isInited() )
		{
			//
			// TRY BLOCK
			//
			try
			{
				//
				// Perform request.
				//
				$this->_HandleRequest();
			}
			
			//
			// CATCH BLOCK
			//
			catch( Exception $error )
			{
				//
				// Load exception in status.
				//
				$this->_Exception2Status( $error );
			}
		
			//
			// Set sent timer.
			//
			if( $this->offsetExists( kAPI_DATA_TIMING ) )
				$this->_OffsetManage( kAPI_DATA_TIMING, kAPI_RES_STAMP, gettimeofday( TRUE ) );
			
			//
			// Return result.
			//
			echo( $this->_EncodeResponse() );
		
		} // Object is inited.
	
	} // HandleRequest.

		

/*=======================================================================================
 *																						*
 *							PROTECTED INITIALISATION INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_InitStatus																		*
	 *==================================================================================*/

	/**
	 * Initialise status.
	 *
	 * This method is responsible for initialising the {@link kAPI_DATA_STATUS status}
	 * section, derived classes may overload this method if they need to handle other
	 * states.
	 *
	 * In this class we set the status to {@link kMESSAGE_TYPE_IDLE idle} and reset the
	 * status {@link kTAG_CODE code}.
	 *
	 * @access protected
	 *
	 * @see kAPI_DATA_STATUS
	 */
	protected function _InitStatus()
	{
		//
		// Init local storage.
		//
		$status = Array();
		
		//
		// Set state.
		//
		$status[ kTAG_STATUS ] = kMESSAGE_TYPE_IDLE;
		
		//
		// Set idle status code.
		//
		$status[ kTAG_CODE ] = 0;
		
		//
		// Copy status to object.
		//
		$this->offsetSet( kAPI_DATA_STATUS, $status );
	
	//
	// In derived classes.
	//
	//	//
	//	// Call parent method.
	//	//
	//	parent::_InitStatus();
	//
	//	//
	//	// Set custom status elements.
	//	//
	//	...
	//
	
	} // _InitStatus.

	 
	/*===================================================================================
	 *	_InitOptions																	*
	 *==================================================================================*/

	/**
	 * Initialise options.
	 *
	 * This method is responsible for parsing and setting all default and provided options,
	 * derived classes should overload this method to handle custom options.
	 *
	 * In this class we initialise the {@link kAPI_DATA_REQUEST request} and
	 * {@link kAPI_DATA_TIMING timer} sections if required.
	 *
	 * @access protected
	 *
	 * @see kAPI_DATA_REQUEST kAPI_DATA_TIMING
	 */
	protected function _InitOptions()
	{
		//
		// Check request log option.
		//
		if( array_key_exists( kAPI_OPT_LOG_REQUEST, $_REQUEST )
		 && $_REQUEST[ kAPI_OPT_LOG_REQUEST ] )
			$this->offsetSet( kAPI_DATA_REQUEST, Array() );
	
		//
		// Check timing option.
		//
		if( array_key_exists( kAPI_REQ_STAMP, $_REQUEST ) )
			$this->offsetSet( kAPI_DATA_TIMING, Array() );
	
	//
	// In derived classes.
	//
	//	//
	//	// Call parent method.
	//	//
	//	parent::_InitOptions();
	//
	//	//
	//	// Set custom options.
	//	//
	//	...
	//
	
	} // _InitOptions.

	 
	/*===================================================================================
	 *	_InitResources																	*
	 *==================================================================================*/

	/**
	 * Initialise resources.
	 *
	 * In derived classes this should be the method that initialises the data store
	 * resources, in this class we have no resources.
	 *
	 * @access protected
	 */
	protected function _InitResources()														{}

		

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
	 * In this class we handle the {@link kAPI_FORMAT format},
	 * {@link kAPI_OPERATION operation} and {@link kAPI_DATA_TIMING timing} elements.
	 *
	 * @access protected
	 *
	 * @uses _ParseFormat()
	 * @uses _ParseOperation()
	 * @uses _ParseTiming()
	 */
	protected function _ParseRequest()
	{
		//
		// Parse request parameters.
		//
		$this->_ParseFormat();
		$this->_ParseOperation();
		$this->_ParseTiming();
	
	//
	// In derived classes.
	//
	//	//
	//	// Call parent method.
	//	//
	//	parent::_ParseRequest();
	//
	//	//
	//	// Parse custom parameters.
	//	//
	//	...
	//
	
	} // _ParseRequest.

	 
	/*===================================================================================
	 *	_FormatRequest																	*
	 *==================================================================================*/

	/**
	 * Format request.
	 *
	 * This method should perform any needed formatting before the request will be handled.
	 *
	 * In this class we do nothing.
	 *
	 * @access protected
	 */
	protected function _FormatRequest()	
	{
	//
	// In derived classes.
	//
	//	//
	//	// Call parent method.
	//	//
	//	parent::_FormatRequest();
	//
	//	//
	//	// Format custom parameters.
	//	//
	//	...
	//
	
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
	 * @access protected
	 *
	 * @uses _ValidateFormat()
	 * @uses _ValidateOperation()
	 */
	protected function _ValidateRequest()
	{
		//
		// Validate parameters.
		//
		$this->_ValidateFormat();
		$this->_ValidateOperation();
	
	//
	// In derived classes.
	//
	//	//
	//	// Call parent method.
	//	//
	//	parent::_ValidateRequest();
	//
	//	//
	//	// Validate custom parameters.
	//	//
	//	...
	//
	
	} // _ValidateRequest.

		

/*=======================================================================================
 *																						*
 *								PROTECTED PARSING UTILITIES								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_ParseFormat																	*
	 *==================================================================================*/

	/**
	 * Parse format.
	 *
	 * This method will parse the request format.
	 *
	 * @access protected
	 *
	 * @see kAPI_DATA_REQUEST kAPI_FORMAT
	 */
	protected function _ParseFormat()
	{
		//
		// Note: the web service exits if either the format or the operation are missing.
		//
		
		//
		// Log format to request.
		//
		if( $this->offsetExists( kAPI_DATA_REQUEST ) )
			$this->_OffsetManage
				( kAPI_DATA_REQUEST, kAPI_FORMAT, $_REQUEST[ kAPI_FORMAT ] );
	
	} // _ParseFormat.

	 
	/*===================================================================================
	 *	_ParseOperation																	*
	 *==================================================================================*/

	/**
	 * Parse operation.
	 *
	 * This method will parse the request operation.
	 *
	 * @access protected
	 *
	 * @see kAPI_DATA_REQUEST kAPI_OPERATION
	 */
	protected function _ParseOperation()
	{
		//
		// Note: the web service exits if either the format or the operation are missing.
		//
		
		//
		// Log operation to request.
		//
		if( $this->offsetExists( kAPI_DATA_REQUEST ) )
			$this->_OffsetManage
				( kAPI_DATA_REQUEST, kAPI_OPERATION, $_REQUEST[ kAPI_OPERATION ] );
	
	} // _ParseOperation.

	 
	/*===================================================================================
	 *	_ParseTiming																	*
	 *==================================================================================*/

	/**
	 * Parse timing.
	 *
	 * This method will parse the request timers.
	 *
	 * @access protected
	 *
	 * @see kAPI_DATA_REQUEST kAPI_REQ_STAMP
	 */
	protected function _ParseTiming()
	{
		//
		// Note: the kAPI_DATA_TIMING offset was created by _InitOptions().
		//
		
		//
		// Handle timing.
		//
		if( $this->offsetExists( kAPI_DATA_TIMING ) )
		{
			//
			// Log request time to request.
			//
			if( $this->offsetExists( kAPI_DATA_REQUEST ) )
				$this->_OffsetManage
					( kAPI_DATA_REQUEST, kAPI_REQ_STAMP, $_REQUEST[ kAPI_REQ_STAMP ] );
			
			//
			// Init timers block.
			//
			$options = Array();
			$options[ kAPI_REQ_STAMP ] = $_REQUEST[ kAPI_REQ_STAMP ];
			$options[ kAPI_REC_STAMP ] = $this->mReceivedStamp;
			$this->offsetSet( kAPI_DATA_TIMING, $options );
		
		} // Log timers.
	
	} // _ParseTiming.

		

/*=======================================================================================
 *																						*
 *							PROTECTED VALIDATION UTILITIES								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_ValidateFormat																	*
	 *==================================================================================*/

	/**
	 * Validate request format.
	 *
	 * This method can be used to check whether the provided {@link kAPI_FORMAT format}
	 * parameter is valid.
	 *
	 * @access protected
	 *
	 * @see kTYPE_PHP kTYPE_JSON
	 */
	protected function _ValidateFormat()
	{
		//
		// Check parameter.
		//
		switch( $_REQUEST[ kAPI_FORMAT ] )
		{
			//
			// Valid formats.
			//
			case kTYPE_PHP:
			case kTYPE_JSON:
				break;
			
			//
			// Handle unknown formats.
			//
			default:
				throw new CException
					( "Unsupported format",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_WARNING,
					  array( 'Parameter' => kAPI_FORMAT,
							 'Value' => $_REQUEST[ kAPI_FORMAT ] ) );			// !@! ==>
			
		} // Parsing parameter.
	
	//
	// In derived classes.
	//
	//	//
	//	// Check parameter.
	//	//
	//	switch( $theParameter )
	//	{
	//		case [custom]:
	//			Handle it here.
	//			break;
	//
	//		default:
	//			parent::_ValidateFormat();
	//			break;
	//	}
	//
	
	} // _ValidateFormat.

	 
	/*===================================================================================
	 *	_ValidateOperation																*
	 *==================================================================================*/

	/**
	 * Validate request operation.
	 *
	 * This method can be used to check whether the provided
	 * {@link kAPI_OPERATION operation} parameter is valid.
	 *
	 * @access protected
	 *
	 * @see kAPI_OP_HELP kAPI_OP_PING
	 */
	protected function _ValidateOperation()
	{
		//
		// Check parameter.
		//
		switch( $_REQUEST[ kAPI_OPERATION ] )
		{
			//
			// Valid formats.
			//
			case kAPI_OP_HELP:
			case kAPI_OP_PING:
				break;
			
			//
			// Handle unknown operation.
			//
			default:
				throw new CException
					( "Unsupported operation",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_WARNING,
					  array( 'Parameter' => kAPI_OPERATION,
							 'Value' => $_REQUEST[ kAPI_OPERATION ] ) );		// !@! ==>
			
		} // Parsing parameter.
	
	//
	// In derived classes.
	//
	//	//
	//	// Check parameter.
	//	//
	//	switch( $theParameter )
	//	{
	//		case [custom]:
	//			Handle it here.
	//			break;
	//
	//		default:
	//			parent::_ValidateFormat( $theParameter );
	//			break;
	//	}
	//
	
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
	 *
	 * @uses _Handle_ListOp()
	 *
	 * @see kAPI_OP_HELP kAPI_OP_PING
	 */
	protected function _HandleRequest()
	{
		//
		// Parse by operation.
		//
		switch( $op = $_REQUEST[ kAPI_OPERATION ] )
		{
			case kAPI_OP_HELP:
				$list = Array();
				$this->_Handle_ListOp( $list );
				if( count( $list ) )
					$this->offsetSet( kAPI_DATA_RESPONSE, $list );
				break;

			case kAPI_OP_PING:
				$this->_Handle_Ping();
				break;

			default:
				throw new CException
					( "Unable to handle request: operation not implemented",
					  kERROR_NOT_IMPLEMENTED,
					  kMESSAGE_TYPE_WARNING,
					  array( 'Operation' => $op ) );							// !@! ==>
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
		// Add kAPI_OP_HELP.
		//
		$theList[ kAPI_OP_HELP ]
			= 'List operations: returns the list of supported operations.';
	
		//
		// Add kAPI_OP_PING.
		//
		$theList[ kAPI_OP_PING ]
			= 'A PING command, this can be used to check if a service is alive.';
	
	} // _Handle_ListOp.

		
	/*===================================================================================
	 *	_Handle_Ping																	*
	 *==================================================================================*/

	/**
	 * Handle {@link kAPI_OP_PING ping} request.
	 *
	 * This method will handle the {@link kAPI_OP_PING kAPI_OP_PING} request, which can be
	 * used to check if a service is alive.
	 *
	 * The ping request will return by default the {@link kAPI_DATA_STATUS status} block.
	 *
	 * @access protected
	 */
	protected function _Handle_Ping()	{}

		

/*=======================================================================================
 *																						*
 *							PROTECTED OFFSET ACCESSOR INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_OffsetManage																	*
	 *==================================================================================*/

	/**
	 * Manage offset.
	 *
	 * This method can be used to manage elements within offsets, in other words, it can be
	 * used to manage elements within an offset:
	 *
	 * <ul>
	 *	<li><b>$theBlock</b>: The main offset.
	 *	<li><b>$theElement</b>: The offset within the main offset.
	 *	<li><b>$theValue</b>: The new value or the operation:
	 *	 <ul>
	 *		<li><i>NULL</i>: Retrieve the element in the block.
	 *		<li><i>FALSE</i>: Delete the element from the block.
	 *		<li><i>other</i>: All other data types are interpreted as a new element.
	 *	 </ul>
	 * </ul>
	 *
	 * @param string				$theBlock			Object block.
	 * @param string				$theElement			Object block element.
	 * @param mixed					$theValue			Element value.
	 *
	 * @access protected
	 */
	protected function _OffsetManage( $theBlock, $theElement, $theValue = NULL )
	{
		//
		// Get paging options.
		//
		$block = $this->offsetGet( $theBlock );
		
		//
		// Return current value.
		//
		if( $theValue === NULL )
		{
			//
			// Check paging block.
			//
			if( $block !== NULL )
				return ( array_key_exists( $theElement, $block ) )
					 ? $block[ $theElement ]										// ==>
					 : NULL;														// ==>
			
			return NULL;															// ==>
		
		} // Return current value.
		
		//
		// Delete current value.
		//
		if( $theValue === FALSE )
		{
			//
			// Check paging block.
			//
			if( $block !== NULL )
			{
				//
				// Check start tag.
				//
				if( array_key_exists( $theElement, $block ) )
				{
					//
					// Save value.
					//
					$save = $block[ $theElement ];
					
					//
					// Unset value.
					//
					unset( $block[ $theElement ] );
					
					//
					// Clear paging block.
					//
					if( ! count( $block ) )
						$this->offsetUnset( $theBlock );
					
					//
					// Replace paging options.
					//
					else
						$this->offsetSet( $theBlock, $block );
					
					return $save;													// ==>
				
				} // Has tag.
				
				return NULL;														// ==>
			
			} // Has paging block.
			
			return NULL;															// ==>
		
		} // Delete current value.
		
		//
		// Create value.
		//
		if( $block === NULL )
		{
			//
			// Create block.
			//
			$this->offsetSet( $theBlock, array( $theElement => $theValue ) );
			
			return NULL;															// ==>
		
		} // New paging block.
		
		//
		// Save value.
		//
		$save = ( array_key_exists( $theElement, $block ) )
			  ? $block[ $theElement ]
			  : NULL;

		//
		// Set value.
		//
		$block[ $theElement ] = $theValue;
		
		//
		// Replace paging options.
		//
		$this->offsetSet( $theBlock, $block );
		
		return $save;																// ==>
	
	} // _OffsetManage.

		

/*=======================================================================================
 *																						*
 *							PROTECTED FORMATTING INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_Exception2Status																*
	 *==================================================================================*/

	/**
	 * Set status from exception.
	 *
	 * This method can be used to set the service status according to an exception:
	 *
	 * <ul>
	 *	<li><i>{@link CException::Severity() Severity}</i>: This value will be set as the
	 *		status {@link kTAG_STATUS status}.
	 *	<li><i>{@link Exception::getCode() Code}</i>: This value will be set as the
	 *		status {@link kTAG_CODE code}.
	 *	<li><i>{@link Exception::getMessage() Message}</i>: This value will be set in the
	 *		status {@link kTAG_DESCRIPTION description} field as a language block.
	 *	<li><i>{@link Exception::getFile() File}</i>: This value will be set in the status
	 *		{@link kTAG_ANNOTATION annotations}.
	 *	<li><i>{@link Exception::getLine() Line}</i>: This value will be set in the status
	 *		{@link kTAG_ANNOTATION annotations}.
	 *	<li><i>{@link Exception::getTrace() Trace}</i>: This value will be set in the status
	 *		{@link kTAG_ANNOTATION annotations}.
	 *	<li><i>{@link CException::Reference() References}</i>: These valuew will be set in
	 *		the status {@link kTAG_ANNOTATION annotations}.
	 * </ul>
	 *
	 * @param Exception				$theException		Exception.
	 *
	 * @access protected
	 */
	protected function _Exception2Status( Exception $theException )
	{
		//
		// Init status.
		//
		$status = Array();
		
		//
		// Set exception code.
		//
		if( ($tmp = $theException->getCode()) !== NULL )
			$status[ kTAG_CODE ] = $tmp;
		
		//
		// Set exception message.
		//
		if( ($tmp = $theException->getMessage()) !== NULL )
			$status[ kTAG_DESCRIPTION ]
				= array( array( kTAG_TYPE => kTYPE_STRING,
								kTAG_LANGUAGE => kDEFAULT_LANGUAGE,
								kTAG_DATA => $tmp ) );
		
		//
		// Set exception trace.
		//
		if( array_key_exists( kAPI_OPT_LOG_TRACE, $_REQUEST )
		 && $_REQUEST[ kAPI_OPT_LOG_TRACE ] )
		{
			//
			// Get trace parameters.
			//
			$file = $theException->getFile();
			$line = $theException->getLine();
			$trace = $theException->getTrace();
			
			//
			// Check trace parameters.
			//
			if( ($file !== NULL)
			 || ($line !== NULL)
			 || ($trace !== NULL) )
			{
				$status[ kTAG_ANNOTATION ] = Array();
				if( $file !== NULL )
					$status[ kTAG_ANNOTATION ][ 'File' ] = $file;
				if( $line !== NULL )
					$status[ kTAG_ANNOTATION ][ 'Line' ] = $line;
				if( $trace !== NULL )
					$status[ kTAG_ANNOTATION ][ 'Trace' ] = $trace;
			
			} // Has trace elements.
		
		} // Log exception trace.
		
		//
		// Handle custom exception fields.
		//
		if( $theException instanceof CException )
		{
			if( ($tmp = $theException->Severity()) !== NULL )
				$status[ kTAG_STATUS ] = $tmp;
			if( $tmp = $theException->Reference() )
			{
				if( ! array_key_exists( kTAG_ANNOTATION, $status ) )
					$status[ kTAG_ANNOTATION ] = Array();
				if( $references = $theException->Reference() )
				{
					foreach( $references as $key => $value )
						$status[ kTAG_ANNOTATION ][ $key ] = $value;
				}
			}
		
		} // Custom exception.
		
		//
		// Set status.
		//
		$this->offsetSet( kAPI_DATA_STATUS, $status );
	
	} // _Exception2Status.

	 
	/*===================================================================================
	 *	_EncodeResponse																	*
	 *==================================================================================*/

	/**
	 * Encode response.
	 *
	 * This method will return the encoded response string.
	 *
	 * @access protected
	 * @return string|NULL
	 */
	protected function _EncodeResponse()
	{
		//
		// Determine format.
		//
		$format = ( array_key_exists( kAPI_FORMAT, $_REQUEST ) )
				? $_REQUEST[ kAPI_FORMAT ]
				: kTYPE_JSON;
		
		//
		// Parse by format.
		//
		switch( $format )
		{
			case kTYPE_PHP:
				return serialize( $this->getArrayCopy() );							// ==>

			case kTYPE_JSON:
				try
				{
					return CObject::JsonEncode( $this->getArrayCopy() );				// ==>
				}
				catch( Exception $error )
				{
					if( $error instanceof CException )
						$error->Reference( 'Response', $this->getArrayCopy() );
					
					throw $error;												// !@! ==>
				}
		}
		
		return NULL;																// ==>
	
	} // _EncodeResponse.

	 

} // class CWrapper.


?>
