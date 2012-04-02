<?php

/**
 * <i>CWrapperClient</i> class definition.
 *
 * This file contains the class definition of <b>CWrapperClient</b> which represents a
 * web-service wrapper client.
 *
 *	@package	MyWrapper
 *	@subpackage	Wrappers
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 31/03/2012
 */

/*=======================================================================================
 *																						*
 *									CWrapperClient.php									*
 *																						*
 *======================================================================================*/

/**
 * Server definitions.
 *
 * This include file contains all definitions of the server object.
 */
require_once( kPATH_LIBRARY_SOURCE."CWrapper.php" );

/**
 *	Wrapper client.
 *
 * This class represents a web-services wrapper client, it facilitates the job of requesting
 * information from servers derived from the {@link CWrapper CWrapper} class.
 *
 * This class supports the following properties:
 *
 * <ul>
 *	<li><i>{@link Url() URL}</i>: This element represents the web-service URL, and it is
 *		stored in the {@link kOFFSET_URL kOFFSET_URL} offset.
 *	<li><i>{@link Operation() Operation}</i>: This element represents the web-service
 *		requested operation, it is stored in the {@link kAPI_OPERATION kAPI_OPERATION}
 *		offset.
 *	<li><i>{@link Format() Format}</i>: This element represents the web-service parameters
 *		and response format, it is stored in the {@link kAPI_FORMAT kAPI_FORMAT} offset.
 *	<li><i>{@link Stamp() Stamp}</i>: This element represents a switch to turn on and off
 *		timing information from the web service, it is stored in the
 *		{@link kAPI_REQ_STAMP kAPI_REQ_STAMP} offset.
 *	<li><i>Log {@link LogRequest() request}</i>: This element represents a switch to turn on
 *		and off logging the request: if on, the request will be returned by the web-service,
 *		it is stored in the {@link kAPI_OPT_LOG_REQUEST kAPI_OPT_LOG_REQUEST} offset.
 *	<li><i>Log {@link LogTrace() trace}</i>: This element represents a switch to turn on
 *		and off tracing exceptions: if on, any exception will also hold a trace, it is
 *		stored in the {@link kAPI_OPT_LOG_TRACE kAPI_OPT_LOG_TRACE} offset.
 * </ul>
 *
 * Objects of this class require at least the {@link Url() URL} {@link kOFFSET_URL offset},
 * {@link Operation() operation} {@link kAPI_OPERATION offset} and the
 * {@link Format() format} {@link kAPI_FORMAT offset} to be set if they expect to have an
 * {@link _IsInited() initialised} {@link kFLAG_STATE_INITED status}, which is necessary
 * to send {@link Request() requests} to web-services.
 *
 *	@package	MyWrapper
 *	@subpackage	Wrappers
 */
class CWrapperClient extends CStatusObject
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
	 * The constructor will initialise the object depending on the provided parameter:
	 *
	 * <ul>
	 *	<li><i>NULL</i>: An empty object.
	 *	<li><i>array</i>: The array elements will become the object properties.
	 *	<li><i>string</i>: Any other value will be converted to string and will represent
	 *		the web-service URL.
	 * </ul>
	 *
	 * @param string				$theData			Web-service URL or object data.
	 *
	 * @access public
	 */
	public function __construct( $theData = NULL )
	{
		//
		// Handle data.
		//
		if( is_array( $theData )
		 || ($theData instanceof ArrayObject) )
			parent::__construct( $theData );
		
		//
		// Handle other.
		//
		else
		{
			//
			// Initialise object.
			//
			parent::__construct();
			
			//
			// Handle URL.
			//
			if( $theData !== NULL )
				$this->Url( $theData );
		}

	} // Constructor.

		

/*=======================================================================================
 *																						*
 *								PUBLIC MEMBER INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Url																				*
	 *==================================================================================*/

	/**
	 * Manage URL.
	 *
	 * This method can be used to manage the {@link kOFFSET_URL URL}, it accepts a parameter
	 * which represents either the URL or the requested operation, depending on its value:
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
	 * @param string				$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses _ManageOffset()
	 *
	 * @see kOFFSET_URL
	 */
	public function Url( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kOFFSET_URL, $theValue, $getOld );				// ==>

	} // Url.

	 
	/*===================================================================================
	 *	Operation																		*
	 *==================================================================================*/

	/**
	 * Manage operation.
	 *
	 * This method can be used to manage the {@link kAPI_OPERATION operation}, it accepts a
	 * parameter which represents either the web-service operation code or this method
	 * operation, depending on its value:
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
	 * This method will check whether the provided operation is supported, if this is not
	 * the case, it will raise an exception. Derived classes should first check their
	 * specific operations, if not matched, they should pass the parameter to the parent
	 * method. In this class we support the following operations:
	 *
	 * <ul>
	 *	<li><i>{@link kAPI_OP_HELP kAPI_OP_HELP}</i>: HELP web-service operation, which
	 *		returns the list of supported operations and options.
	 *	<li><i>{@link kAPI_OP_PING kAPI_OP_PING}</i>: PING web-service operation, which
	 *		returns a status response.
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
	 * @see kAPI_OP_HELP kAPI_OP_PING
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
				case kAPI_OP_HELP:
				case kAPI_OP_PING:
					break;
				
				default:
					throw new CException( "Unsupported operation",
										  kERROR_UNSUPPORTED,
										  kMESSAGE_TYPE_ERROR,
										  array( 'Operation' => $theValue ) );	// !@! ==>
			}
		}
		
		return $this->_ManageOffset( kAPI_OPERATION, $theValue, $getOld );			// ==>

	} // Operation.

	 
	/*===================================================================================
	 *	Format																			*
	 *==================================================================================*/

	/**
	 * Manage format.
	 *
	 * This method can be used to manage the {@link kAPI_FORMAT format} in which both the
	 * parameters are sent and the response is returned from the web-service. It accepts a
	 * parameter which represents either the web-service format code or this method
	 * operation, depending on its value:
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
	 * This method will check whether the provided format is supported, if this is not
	 * the case, it will raise an exception. Derived classes should first check their
	 * specific operations, if not matched, they should pass the parameter to the parent
	 * method. In this class we support the following operations:
	 *
	 * <ul>
	 *	<li><i>{@link kDATA_TYPE_PHP kDATA_TYPE_PHP}</i>: Parameters and response will be
	 *		serialized.
	 *	<li><i>{@link kDATA_TYPE_XML kDATA_TYPE_XML}</i>: Parameters and response will be
	 *		encoded in XML.
	 *	<li><i>{@link kDATA_TYPE_JSON kDATA_TYPE_JSON}</i>: Parameters and response will be
	 *		encoded in JSON.
	 *	<li><i>{@link kDATA_TYPE_META kDATA_TYPE_META}</i>: This data type can be used to
	 *		return service metadata, the {@link Execute() request} will return headers
	 *		metadata for troubleshooting, rather than the response from the web-service.
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
	 * @see kAPI_FORMAT
	 * @see kAPI_OP_HELP kAPI_OP_PING
	 */
	public function Format( $theValue = NULL, $getOld = FALSE )
	{
		//
		// Check operation.
		//
		if( ($theValue !== NULL)
		 && ($theValue !== FALSE) )
		{
			switch( $theValue )
			{
				case kDATA_TYPE_PHP:
				case kDATA_TYPE_JSON:
				case kDATA_TYPE_META:
					break;
				
				case kDATA_TYPE_XML:
				default:
					throw new CException( "Unsupported format",
										  kERROR_UNSUPPORTED,
										  kMESSAGE_TYPE_ERROR,
										  array( 'Format' => $theValue ) );		// !@! ==>
			}
		}
		
		return $this->_ManageOffset( kAPI_FORMAT, $theValue, $getOld );				// ==>

	} // Format.

	 
	/*===================================================================================
	 *	Stamp																			*
	 *==================================================================================*/

	/**
	 * Time-stamp switch.
	 *
	 * This method can be used to turn on or off the time-stamp section in the web-service
	 * response, the method accepts a single parameter that if resolves to <i>TRUE</i> it
	 * will turn on this option, if not it will turn it off.
	 *
	 * The second parameter is a boolean which if <i>TRUE</i> will return the <i>old</i>
	 * value when replacing values; if <i>FALSE</i>, it will return the currently set value.
	 *
	 * @param boolean				$theValue			TRUE or FALSE.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses _ManageOffset()
	 *
	 * @see kAPI_REQ_STAMP
	 */
	public function Stamp( $theValue = NULL, $getOld = FALSE )
	{
		//
		// Normalise value.
		//
		$theValue = ( $theValue ) ? TRUE : FALSE;
		
		return $this->_ManageOffset( kAPI_REQ_STAMP, $theValue, $getOld );			// ==>

	} // Stamp.

	 
	/*===================================================================================
	 *	LogRequest																		*
	 *==================================================================================*/

	/**
	 * Log request switch.
	 *
	 * This method can be used to turn on or off the request section in the web-service
	 * response, the method accepts a single parameter that if resolves to <i>TRUE</i> it
	 * will turn on this option, if not it will turn it off.
	 *
	 * The second parameter is a boolean which if <i>TRUE</i> will return the <i>old</i>
	 * value when replacing values; if <i>FALSE</i>, it will return the currently set value.
	 *
	 * @param boolean				$theValue			TRUE or FALSE.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses _ManageOffset()
	 *
	 * @see kAPI_OPT_LOG_REQUEST
	 */
	public function LogRequest( $theValue = NULL, $getOld = FALSE )
	{
		//
		// Normalise value.
		//
		$theValue = ( $theValue ) ? TRUE : FALSE;
		
		return $this->_ManageOffset( kAPI_OPT_LOG_REQUEST, $theValue, $getOld );	// ==>

	} // LogRequest.

	 
	/*===================================================================================
	 *	LogTrace																		*
	 *==================================================================================*/

	/**
	 * Log trace switch.
	 *
	 * This method can be used to turn on or off the trace in the event an exception is
	 * raised, the method accepts a single parameter that if resolves to <i>TRUE</i> it will
	 * turn on this option, if not it will turn it off.
	 *
	 * The second parameter is a boolean which if <i>TRUE</i> will return the <i>old</i>
	 * value when replacing values; if <i>FALSE</i>, it will return the currently set value.
	 *
	 * @param boolean				$theValue			TRUE or FALSE.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses _ManageOffset()
	 *
	 * @see kAPI_OPT_LOG_TRACE
	 */
	public function LogTrace( $theValue = NULL, $getOld = FALSE )
	{
		//
		// Normalise value.
		//
		$theValue = ( $theValue ) ? TRUE : FALSE;
		
		return $this->_ManageOffset( kAPI_OPT_LOG_TRACE, $theValue, $getOld );		// ==>

	} // LogTrace.

		

/*=======================================================================================
 *																						*
 *								PUBLIC ARRAY ACCESS INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	offsetSet																		*
	 *==================================================================================*/

	/**
	 * Set a value for a given offset.
	 *
	 * We overload this method to manage the {@link _IsInited() inited}
	 * {@link kFLAG_STATE_INITED status}: this is set if {@link kOFFSET_URL URL},
	 * {@link kAPI_OPERATION operation} and {@link kAPI_FORMAT format} are all set.
	 *
	 * @param string				$theOffset			Offset.
	 * @param string|NULL			$theValue			Value to set at offset.
	 *
	 * @access public
	 */
	public function offsetSet( $theOffset, $theValue )
	{
		//
		// Call parent method.
		//
		parent::offsetSet( $theOffset, $theValue );
		
		//
		// Set inited flag.
		//
		if( $theValue !== NULL )
			$this->_IsInited( $this->offsetExists( kOFFSET_URL ) &&
							  $this->offsetExists( kAPI_FORMAT ) &&
							  $this->offsetExists( kAPI_OPERATION ) );
	
	} // offsetSet.

	 
	/*===================================================================================
	 *	offsetUnset																		*
	 *==================================================================================*/

	/**
	 * Reset a value for a given offset.
	 *
	 * We overload this method to manage the {@link _IsInited() inited}
	 * {@link kFLAG_STATE_INITED status}: this is set if {@link kOFFSET_URL URL},
	 * {@link kAPI_OPERATION operation} and {@link kAPI_FORMAT format} are all set.
	 *
	 * @param string				$theOffset			Offset.
	 *
	 * @access public
	 */
	public function offsetUnset( $theOffset )
	{
		//
		// Call parent method.
		//
		parent::offsetUnset( $theOffset );
		
		//
		// Set inited flag.
		//
		$this->_IsInited( $this->offsetExists( kOFFSET_URL ) &&
						  $this->offsetExists( kAPI_FORMAT ) &&
						  $this->offsetExists( kAPI_OPERATION ) );
	
	} // offsetUnset.

		

/*=======================================================================================
 *																						*
 *								PUBLIC REQUEST INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Execute																			*
	 *==================================================================================*/

	/**
	 * Send an HTTP request.
	 *
	 * This method can be used to sent an <i>HTTP</i> request using the current contents of
	 * the object and return a response.
	 *
	 * @param string				$theMode			Request mode, POST or GET
	 *
	 * @access public
	 * @return mixed
	 *
	 * @throws {@link CException CException}
	 */
	public function Execute( $theMode = 'POST' )
	{
		//
		// Check if inited.
		//
		if( ! $this->_IsInited() )
			throw new CException
					( "Unable to execute request: object not initialised",
					  kERROR_INVALID_STATE,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Object' => $this ) );								// !@! ==>
	
		//
		// Copy parameters.
		//
		$params = (array) $this;
		
		//
		// Extract URL.
		//
		$url = $params[ kOFFSET_URL ];
		unset( $params[ kOFFSET_URL ] );
		
		//
		// Extract format.
		//
		$format = $params[ kAPI_FORMAT ];
		
		//
		// Set time-stamp.
		//
		if( $this->offsetExists( kAPI_REQ_STAMP ) )
			$this->offsetSet( kAPI_REQ_STAMP, gettimeofday( TRUE ) );
		
		//
		// Format parameters.
		//
		foreach( $params as $key => $value )
		{
			if( is_array( $value )
			 || ($value instanceof ArrayObject) )
			{
				switch( $this->Format() )
				{
					case kDATA_TYPE_PHP:
						$params[ $key ] = serialize( $value );
						break;

					case kDATA_TYPE_JSON:
						$params[ $key ] = CObject::JsonEncode( $value );
						break;
					
					default:
						$params[ $key ] = (string) $value;
						break;
				}
			}
		}
		
		return self::Request( $url, $params, $theMode, $format );					// ==>
	
	} // Execute.

		

/*=======================================================================================
 *																						*
 *								STATIC REQUEST INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Request																			*
	 *==================================================================================*/

	/**
	 * Send a HTTP request.
	 *
	 * This method can be used to sent an <i>HTTP</i> request via <i>GET</i> or <i>POST</i>.
	 *
	 * The method accepts the following parameters:
	 *
	 * <ul>
	 *	<li><b>$theUrl</b>: The request URL.
	 *	<li><b>$theParams</b>: An array of key/value parameters for the request.
	 *	<li><b>$theMode</b>: The request mode:
	 *	 <ul>
	 *		<li><i>GET</i>: GET, default.
	 *		<li><i>POST</i>: POST.
	 *	 </ul>
	 *	<li><b>$theFormat</b>: The request format:
	 *	 <ul>
	 *		<li><i>{@link kDATA_TYPE_XML kDATA_TYPE_XML}</i>: XML.
	 *		<li><i>{@link kDATA_TYPE_PHP kDATA_TYPE_PHP}</i>: PHP.
	 *		<li><i>{@link kDATA_TYPE_JSON kDATA_TYPE_JSON}</i>: JSON.
	 *		<li><i>{@link kDATA_TYPE_META kDATA_TYPE_META}</i>: Metadata: if you provide
	 *			this format, the method will return the metadata of the operation for
	 *			troubleshooting purposes.
	 *	 </ul>
	 * </ul>
	 *
	 * @param string				$theUrl				Request URL.
	 * @param mixed					$theParams			Request parameters.
	 * @param string				$theMode			Request mode.
	 * @param string				$theFormat			Response format.
	 *
	 * @static
	 * @return mixed
	 *
	 * @throws {@link CException CException}
	 */
	static function Request( $theUrl, $theParams = NULL,
									  $theMode = 'POST',
									  $theFormat = kDATA_TYPE_JSON )
	{
		//
		// Check mode.
		//
		switch( $tmp = strtoupper( $theMode ) )
		{
			case 'GET':
			case 'POST':
				$theMode = $tmp;
				break;
			
			default:
				throw new CException( "Unsupported HTTP mode",
									  kERROR_UNSUPPORTED,
									  kMESSAGE_TYPE_ERROR,
									  array( 'Mode' => $theMode ) );			// !@! ==>
		}
		
		//
		// Build context parameters.
		//
		$cxp = array( 'http' => array( 'method' => $theMode,
									   'ignore_errors' => TRUE ) );
		
		//
		// Set parameters.
		//
		if( $theParams !== NULL )
		{
			//
			// Format parameters.
			//
			$theParams = http_build_query( $theParams );
			
			//
			// handle mode.
			//
			if( $theMode == 'POST' )
				$cxp[ 'http' ][ 'content' ] = $theParams;
			else
				$theUrl .= ('?'.$theParams);
		}
		
		//
		// Create context.
		//
		$context = stream_context_create( $cxp );
		
		//
		// Open stream.
		//
		$fp = @fopen( $theUrl, 'rb', FALSE, $context );
		if( ! $fp )
			throw new CException( "Unable to open [$theMode] [$theUrl]",
								  kERROR_INVALID_STATE,
								  kMESSAGE_TYPE_ERROR,
								  array( 'Mode' => $theMode,
								  		 'URL' => $theUrl ) );					// !@! ==>
		
		//
		// Get stream metadata.
		// This can be used to troubleshoot the operation:
		// by displating the metadata you can see the HTTP response header
		// across all redirects.
		//
		if( $theFormat == kDATA_TYPE_META )
		{
			$meta = stream_get_meta_data( $fp );
			fclose( $fp );
			return $meta;															// ==>
		}
		
		//
		// Read stream.
		//
		$result = stream_get_contents( $fp );
		
		//
		// Close stream.
		//
		fclose( $fp );
		
		//
		// Format response.
		//
		switch( $theFormat )
		{
			case kDATA_TYPE_JSON:
				return CObject::JsonDecode( $result );								// ==>
	
			case kDATA_TYPE_XML:
				$response = simplexml_load_string( $result );
				if( $response !== NULL )
					return $response;												// ==>
				throw new CException( "Unable to decode XML string",
									  kERROR_INVALID_STATE,
									  kMESSAGE_TYPE_ERROR,
									  array( 'Response' => $result ) );			// !@! ==>
			
			case kDATA_TYPE_PHP:
				return unserialize( $result );										// ==>
		}
		
		return $result;																// ==>
	
	} // Request.

	 

} // class CWrapperClient.


?>
