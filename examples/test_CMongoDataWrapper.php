<?php
	
/**
 * {@link CMongoDataWrapper.php Data} wrapper object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * base object {@link CMongoDataWrapper class}.
 *
 *	@package	Test
 *	@subpackage	Wrappers
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 22/12/2011
 *				2.00 23/02/2012
 */

/*=======================================================================================
 *																						*
 *								test_CMongoDataWrapper.php								*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Style includes.
//
require_once( '/Library/WebServer/Library/wrapper/styles.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."CMongoDataWrapperClient.php" );


/*=======================================================================================
 *	GLOBAL DEFINITIONS																	*
 *======================================================================================*/
 
//
// Use raw parameters or use wrapper client?.
//
define( 'kUSE_CLIENT', TRUE );


/*=======================================================================================
 *	DECLARE TEST OBJECTS																*
 *======================================================================================*/
 
//
// Create object 1.
//
$object1 = Array();
$object1[ 'Name' ] = 'Milko';
$object1[ 'Surname' ] = 'Škofič';
$object1[ 'Date' ] = new CDataTypeStamp( new MongoDate( strtotime( "2011-12-31 23:59:59" ), 999999 ) );
$object1[ 'Number' ] = new CDataTypeInt64( '123456789123456' );
$object1[ 'Inst' ] = 'International Plant Genetic Resources Institute';
$object1[ 'Cat' ] = array( 'Colours' => array( 'Orange', 'Yellow' ),
						   'Teams' => array( 'Roma' ) );
$object1[ 'Other' ] = 'Other data';
$object1[ kTAG_ID_NATIVE ] = 'Škofič';
 
//
// Create object 2.
//
$object2 = Array();
$object2[ 'Name' ] = 'Luca';
$object2[ 'Surname' ] = 'Matteis';
$object2[ 'Date' ] = new CDataTypeStamp( new MongoDate( strtotime( "2012-01-03" ) ) );
$object2[ 'Number' ] = new CDataTypeInt64( '123456789123457' );
$object2[ 'Cat' ] = array( 'Colours' => array( 'Red', 'Green', 'Yellow', 'Orange' ),
						   'Teams' => array( 'Roma' ) );
$object2[ 'Inst' ] = 'Bioversity International';
 
//
// Create object 3.
//
$object3 = Array();
$object3[ 'Name' ] = 'Elisabeth';
$object3[ 'Surname' ] = 'Arnaud';
$object3[ 'Date' ] = new CDataTypeStamp( new MongoDate( strtotime( "2010-07-28 12:04:27" ) ) );
$object3[ 'Number' ] = new CDataTypeInt64( '123456789123458' );
$object3[ 'Cat' ] = array( 'Colours' => array( 'Blue', 'Green' ),
						   'Teams' => array( 'Paris St. Germain' ) );
$object3[ 'Inst' ] = 'Banana a gogo';
 
//
// Create object 4.
//
$object4 = Array();
$object4[ 'Name' ] = 'Luca';
$object4[ 'Surname' ] = 'Sampieri';
$object4[ 'Date' ] = new CDataTypeStamp( new MongoDate( strtotime( "1931-03-05 10:00:15" ) ) );
$object4[ 'Number' ] = new CDataTypeInt64( '123456789123459' );
$object4[ 'Inst' ] = 'Stretto di Messina';
$object4[ 'Cat' ] = array( 'Colours' => array( 'Blue' ),
						   'Teams' => array( 'Milan' ) );
$object4[ kTAG_ID_NATIVE ] = new CDataTypeBinary( 'Sampieri' );
 
//
// Create object 5.
//
$object5 = Array();
$object5[ 'Name' ] = 'Hannes';
$object5[ 'Surname' ] = 'Gaisberger';
$object5[ 'Date' ] = new CDataTypeStamp( new MongoDate( strtotime( "1971-105-18" ) ) );
$object5[ 'Number' ] = new CDataTypeInt64( '123' );
$object5[ 'Cat' ] = array( 'Colours' => array( 'Brown' ),
						   'Teams' => array( 'Wien' ) );
$object5[ 'Inst' ] = 'Wien';


/*=======================================================================================
 *	TEST WRAPPER OBJECT																	*
 *======================================================================================*/
 
//
// Init local storage.
//
$url = 'http://localhost/newwrapper/MongoDataWrapper.php';

//
// TRY BLOCK.
//
try
{
	//
	// Instantiate Mongo database.
	//
	$mongo = New Mongo();
	
	//
	// Select MCPD database.
	//
	$db = $mongo->selectDB( "TEST" );
	
	//
	// Drop database.
	//
	$db->drop();
	
	//
	// Select test collection.
	//
	$collection = $db->selectCollection( 'CMongoDataWrapper' );
	
	//
	// Create utility container.
	//
	$container = new CMongoContainer( $collection );

	/*===================================================================================
	 *	LIST OPERATIONS - GET															*
	 *==================================================================================*/
	echo( '<h4>List operations (GET)</h4>' );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CMongoDataWrapperClient( $url );
		$params->Operation( kAPI_OP_HELP );
		$params->Format( kDATA_TYPE_JSON );
		//
		// Get response.
		//
		$decoded = $params->Execute( 'GET' );
	}
	//
	// Use raw parameters.
	//
	else
	{
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_HELP;				// Command.
		$params[] = kAPI_FORMAT.'='.kDATA_TYPE_JSON;				// Format.
		//
		// Build request.
		//
		$request = $url.'?'.implode( '&', $params );
		//
		// Get response.
		//
		$response = file_get_contents( $request );
		//
		// Decode response.
		//
		$decoded = json_decode( $response, TRUE );
	}
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Parameters:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $params ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	if( ! kUSE_CLIENT )
	{
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'URL:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.htmlspecialchars( $request ).kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'Response:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.$response.kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
	}
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Decoded:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $decoded ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_TABLE_POS );
	echo( '<hr>' );

	/*===================================================================================
	 *	LIST OPERATIONS - POST															*
	 *==================================================================================*/
	echo( '<h4>List operations (POST)</h4>' );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CMongoDataWrapperClient( $url );
		$params->Operation( kAPI_OP_HELP );
		$params->Format( kDATA_TYPE_JSON );
		//
		// Get response.
		//
		$decoded = $params->Execute( 'POST' );
	}
	//
	// Use raw parameters.
	//
	else
	{
		//
		// Build parameters.
		//
		$params = Array();
		$params[ kAPI_FORMAT ] = kDATA_TYPE_JSON;				// Format.
		$params[ kAPI_OPERATION ] = kAPI_OP_HELP;				// Command.
		//
		// Use static method.
		//
		$decoded = CWrapperClient::Request( $url, $params, 'POST', kDATA_TYPE_JSON );
	}
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Parameters:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $params ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Decoded:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $decoded ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_TABLE_POS );
	echo( '<hr>' );
	
	/*===================================================================================
	 *	EMPTY REQUEST																	*
	 *==================================================================================*/
	echo( '<h4>Empty request</h4>' );
	echo( '<i>Note that an empty request will return nothing.</i><br>' );
	//
	// Try.
	//	
	try
	{
		//
		// Use wrapper client.
		//
		if( kUSE_CLIENT )
		{
			//
			// Build parameters.
			//
			$params = new CMongoDataWrapperClient( $url );
			//
			// Get response.
			//
			$decoded = $params->Execute( 'POST' );
		}
		//
		// Use raw parameters.
		//
		else
		{
			//
			// Get response.
			//
			$response = file_get_contents( $url );
			//
			// Decode response.
			//
			$decoded = json_decode( $response, TRUE );
		}
		//
		// Display.
		//
		echo( kSTYLE_TABLE_PRE );
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'Parameters:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $params ); echo( '</pre>'.kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
		if( ! kUSE_CLIENT )
		{
			echo( kSTYLE_ROW_PRE );
			echo( kSTYLE_HEAD_PRE.'Response:'.kSTYLE_HEAD_POS );
			echo( kSTYLE_DATA_PRE.$response.kSTYLE_DATA_POS );
			echo( kSTYLE_ROW_POS );
		}
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'Decoded:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $decoded ); echo( '</pre>'.kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
		echo( kSTYLE_TABLE_POS );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );

	} echo( '<hr>' );
	
	/*===================================================================================
	 *	MISSING OPERATION																*
	 *==================================================================================*/
	echo( '<h4>Missing operation</h4>' );
	echo( '<i>Note that we return nothing if no operation is provided.</i><br>' );
	//
	// Try.
	//	
	try
	{
		//
		// Use wrapper client.
		//
		if( kUSE_CLIENT )
		{
			//
			// Build parameters.
			//
			$params = new CMongoDataWrapperClient( $url );
			$params->Format( kDATA_TYPE_PHP );
			$params->Stamp( TRUE );
			$params->LogTrace( TRUE );
			$params->LogRequest( TRUE );
			//
			// Get response.
			//
			$decoded = $params->Execute( 'POST' );
		}
		//
		// Use raw parameters.
		//
		else
		{
			//
			// Build parameters.
			//
			$params = Array();
			$params[] = kAPI_FORMAT.'='.kDATA_TYPE_PHP;					// Format.
			$params[] = kAPI_REQ_STAMP.'='.gettimeofday( true );		// Time-stamp.
			$params[] = kAPI_OPT_LOG_TRACE.'='.'1';						// Trace exceptions.
			$params[] = kAPI_OPT_LOG_REQUEST.'='.'1';					// Log request.
			//
			// Build request.
			//
			$request = $url.'?'.implode( '&', $params );
			//
			// Get response.
			//
			$response = file_get_contents( $request );
			//
			// Decode response.
			//
			$decoded = unserialize( $response );
		}
		//
		// Display.
		//
		echo( kSTYLE_TABLE_PRE );
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'Parameters:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $params ); echo( '</pre>'.kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
		if( ! kUSE_CLIENT )
		{
			echo( kSTYLE_ROW_PRE );
			echo( kSTYLE_HEAD_PRE.'URL:'.kSTYLE_HEAD_POS );
			echo( kSTYLE_DATA_PRE.htmlspecialchars( $request ).kSTYLE_DATA_POS );
			echo( kSTYLE_ROW_POS );
			echo( kSTYLE_ROW_PRE );
			echo( kSTYLE_HEAD_PRE.'Response:'.kSTYLE_HEAD_POS );
			echo( kSTYLE_DATA_PRE.htmlspecialchars( $response ).kSTYLE_DATA_POS );
			echo( kSTYLE_ROW_POS );
		}
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'Decoded:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $decoded ); echo( '</pre>'.kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
		echo( kSTYLE_TABLE_POS );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );

	} echo( '<hr>' );
	
	/*===================================================================================
	 * INVALID OPERATION																*
	 *==================================================================================*/
	echo( '<h4>Invalid operation</h4>' );
	echo( '<i>Note the status.</i><br>' );
	//
	// Try.
	//	
	try
	{
		//
		// Use wrapper client.
		//
		if( kUSE_CLIENT )
		{
			//
			// Build parameters.
			//
			$params = new CMongoDataWrapperClient( $url );
			$params->Operation( 'XXX' );
			$params->Format( kDATA_TYPE_PHP );
			$params->Stamp( TRUE );
			$params->LogTrace( TRUE );
			$params->LogRequest( TRUE );
			//
			// Get response.
			//
			$decoded = $params->Execute();
		}
		//
		// Use raw parameters.
		//
		else
		{
			//
			// Build parameters.
			//
			$params = Array();
			$params[] = kAPI_FORMAT.'='.kDATA_TYPE_PHP;					// Format.
			$params[] = kAPI_OPERATION.'='.'XXX';						// Operation.
			$params[] = kAPI_REQ_STAMP.'='.gettimeofday( true );		// Time-stamp.
			$params[] = kAPI_OPT_LOG_TRACE.'='.'1';						// Trace exceptions.
			$params[] = kAPI_OPT_LOG_REQUEST.'='.'1';					// Log request.
			//
			// Build request.
			//
			$request = $url.'?'.implode( '&', $params );
			//
			// Get response.
			//
			$response = file_get_contents( $request );
			//
			// Decode response.
			//
			$decoded = unserialize( $response );
		}
		//
		// Display.
		//
		echo( kSTYLE_TABLE_PRE );
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'Parameters:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $params ); echo( '</pre>'.kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
		if( ! kUSE_CLIENT )
		{
			echo( kSTYLE_ROW_PRE );
			echo( kSTYLE_HEAD_PRE.'URL:'.kSTYLE_HEAD_POS );
			echo( kSTYLE_DATA_PRE.htmlspecialchars( $request ).kSTYLE_DATA_POS );
			echo( kSTYLE_ROW_POS );
			echo( kSTYLE_ROW_PRE );
			echo( kSTYLE_HEAD_PRE.'Response:'.kSTYLE_HEAD_POS );
			echo( kSTYLE_DATA_PRE.htmlspecialchars( $response ).kSTYLE_DATA_POS );
			echo( kSTYLE_ROW_POS );
		}
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'Decoded:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $decoded ); echo( '</pre>'.kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
		echo( kSTYLE_TABLE_POS );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );

	} echo( '<hr>' );
	
	/*===================================================================================
	 *	PING IN PHP																		*
	 *==================================================================================*/
	echo( '<h4>Ping wrapper in PHP</h4>' );
	echo( '<i>Check out the response encoding.</i><br>' );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CMongoDataWrapperClient( $url );
		$params->Operation( kAPI_OP_PING );
		$params->Format( kDATA_TYPE_PHP );
		$params->Stamp( TRUE );
		$params->LogTrace( TRUE );
		$params->LogRequest( TRUE );
		//
		// Get response.
		//
		$decoded = $params->Execute();
	}
	//
	// Use raw parameters.
	//
	else
	{
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_FORMAT.'='.kDATA_TYPE_PHP;					// Format.
		$params[] = kAPI_OPERATION.'='.kAPI_OP_PING;				// Command.
		$params[] = kAPI_REQ_STAMP.'='.gettimeofday( true );		// Time-stamp.
		$params[] = kAPI_OPT_LOG_TRACE.'='.'1';						// Trace exceptions.
		$params[] = kAPI_OPT_LOG_REQUEST.'='.'1';					// Log request.
		//
		// Build request.
		//
		$request = $url.'?'.implode( '&', $params );
		//
		// Get response.
		//
		$response = file_get_contents( $request );
		//
		// Decode response.
		//
		$decoded = unserialize( $response );
	}
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Parameters:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $params ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	if( ! kUSE_CLIENT )
	{
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'URL:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.htmlspecialchars( $request ).kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'Response:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.htmlspecialchars( $response ).kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
	}
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Decoded:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $decoded ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_TABLE_POS );
	echo( '<hr>' );
	
	/*===================================================================================
	 *	PING IN JSON																	*
	 *==================================================================================*/
	echo( '<h4>Ping wrapper in JSON</h4>' );
	echo( '<i>Check out the response encoding.</i><br>' );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CMongoDataWrapperClient( $url );
		$params->Operation( kAPI_OP_PING );
		$params->Format( kDATA_TYPE_JSON );
		$params->Stamp( TRUE );
		$params->LogTrace( TRUE );
		$params->LogRequest( TRUE );
		//
		// Get response.
		//
		$decoded = $params->Execute();
	}
	//
	// Use raw parameters.
	//
	else
	{
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_FORMAT.'='.kDATA_TYPE_JSON;				// Format.
		$params[] = kAPI_OPERATION.'='.kAPI_OP_PING;				// Command.
		$params[] = kAPI_REQ_STAMP.'='.gettimeofday( true );		// Time-stamp.
		$params[] = kAPI_OPT_LOG_TRACE.'='.'1';						// Trace exceptions.
		$params[] = kAPI_OPT_LOG_REQUEST.'='.'1';					// Log request.
		//
		// Build request.
		//
		$request = $url.'?'.implode( '&', $params );
		//
		// Get response.
		//
		$response = file_get_contents( $request );
		//
		// Decode response.
		//
		$decoded = json_decode( $response, TRUE );
	}
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Parameters:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $params ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	if( ! kUSE_CLIENT )
	{
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'URL:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.htmlspecialchars( $request ).kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'Response:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.htmlspecialchars( $response ).kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
	}
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Decoded:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $decoded ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_TABLE_POS );
	echo( '<hr>' );
	
	/*===================================================================================
	 *	INSERT																			*
	 *==================================================================================*/
	echo( '<h4>Insert object</h4>' );
	echo( '<i>The last <b>Found</b> section shows what we inserted '
		 .'in the database.</i><br>' );
	//
	// Select object.
	//
	$object = $object1;
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CMongoDataWrapperClient( $url );
		$params->Format( kDATA_TYPE_JSON );
		$params->Operation( kAPI_OP_INSERT );
		$params->Stamp( TRUE );
		$params->Database( 'TEST' );
		$params->Container( 'CMongoDataWrapper' );
		$params->Options( kAPI_OPT_SAFE, TRUE );
		$params->Object( $object );
//		$params->NoResponse( TRUE );
		$params->LogTrace( TRUE );
		$params->LogRequest( TRUE );
		//
		// Get response.
		//
		$decoded = $params->Execute();
	}
	//
	// Use raw parameters.
	//
	else
	{
		//
		// Prepare object.
		//
		$object_enc = json_encode( $object );
		//
		// Set options.
		//
		$options = array( kAPI_OPT_SAFE => 1 );
		$options = json_encode( $options );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_FORMAT.'='.kDATA_TYPE_JSON;				// Format.
		$params[] = kAPI_OPERATION.'='.kAPI_OP_INSERT;				// Command.
		$params[] = kAPI_REQ_STAMP.'='.gettimeofday( true );		// Time-stamp.
		$params[] = kAPI_DATABASE.'='.'TEST';						// Database.
		$params[] = kAPI_CONTAINER.'='.'CMongoDataWrapper';			// Container.
		$params[] = kAPI_DATA_OPTIONS.'='.urlencode( $options );	// Options.
		$params[] = kAPI_DATA_OBJECT.'='.urlencode( $object_enc );	// Object.
//		$params[] = kAPI_OPT_NO_RESP.'='.'1';						// Hide response.
		$params[] = kAPI_OPT_LOG_TRACE.'='.'1';						// Trace exceptions.
		$params[] = kAPI_OPT_LOG_REQUEST.'='.'1';					// Log request.
		//
		// Build request.
		//
		$request = $url.'?'.implode( '&', $params );
		//
		// Get response.
		//
		$response = file_get_contents( $request );
		//
		// Decode response.
		//
		$decoded = json_decode( $response, TRUE );
	}
	//
	// Parse response.
	//
	if( array_key_exists( kAPI_DATA_RESPONSE, $decoded ) )
	{
		//
		// Get object.
		//
		$object = $decoded[ kAPI_DATA_RESPONSE ];
		//
		// Save reference for testing.
		//
		$reference = Array();
		$container->UnserialiseObject( $object );			// To feed to MongoDBRef.
		$reference[ kTAG_ID_REFERENCE ] = $object[ kTAG_ID_NATIVE ];
		$reference[ kTAG_CONTAINER_REFERENCE ] = 'CMongoDataWrapper';
		$reference[ kTAG_DATABASE_REFERENCE ] = 'TEST';
		$found = MongoDBRef::get( $db, $reference );
	}
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Parameters:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $params ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	if( ! kUSE_CLIENT )
	{
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'URL:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.htmlspecialchars( $request ).kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'Response:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.htmlspecialchars( $response ).kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
	}
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Decoded:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $decoded ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	if( array_key_exists( kAPI_DATA_RESPONSE, $decoded ) )
	{
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'Found:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $found ); echo( '</pre>'.kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
	}
	echo( kSTYLE_TABLE_POS );
	echo( '<hr>' );
	
	/*===================================================================================
	 *	REPLACE																			*
	 *==================================================================================*/
	echo( '<h4>Set object</h4>' );
	echo( '<i>The last <b>Found</b> section shows what we set '
		 .'in the database.</i><br>' );
	//
	// Select object.
	//
	$object = $object2;
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CMongoDataWrapperClient( $url );
		$params->Format( kDATA_TYPE_JSON );
		$params->Operation( kAPI_OP_SET );
		$params->Stamp( TRUE );
		$params->Database( 'TEST' );
		$params->Container( 'CMongoDataWrapper' );
		$params->Options( kAPI_OPT_SAFE, TRUE );
		$params->Object( $object );
//		$params->NoResponse( TRUE );
		$params->LogTrace( TRUE );
		$params->LogRequest( TRUE );
		//
		// Get response.
		//
		$decoded = $params->Execute();
	}
	//
	// Use raw parameters.
	//
	else
	{
		//
		// Prepare object.
		//
		$object = json_encode( $object );
		//
		// Set options.
		//
		$options = array( kAPI_OPT_SAFE => 1 );
		$options = json_encode( $options );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_FORMAT.'='.kDATA_TYPE_JSON;				// Format.
		$params[] = kAPI_OPERATION.'='.kAPI_OP_SET;					// Command.
		$params[] = kAPI_REQ_STAMP.'='.gettimeofday( true );		// Time-stamp.
		$params[] = kAPI_DATABASE.'='.'TEST';						// Database.
		$params[] = kAPI_CONTAINER.'='.'CMongoDataWrapper';			// Container.
		$params[] = kAPI_DATA_OPTIONS.'='.urlencode( $options );	// Options.
		$params[] = kAPI_DATA_OBJECT.'='.urlencode( $object );		// Object.
//		$params[] = kAPI_OPT_NO_RESP.'='.'1';						// Hide response.
		$params[] = kAPI_OPT_LOG_TRACE.'='.'1';						// Trace exceptions.
		$params[] = kAPI_OPT_LOG_REQUEST.'='.'1';					// Log request.
		//
		// Build request.
		//
		$request = $url.'?'.implode( '&', $params );
		//
		// Get response.
		//
		$response = file_get_contents( $request );
		//
		// Decode response.
		//
		$decoded = json_decode( $response, TRUE );
	}
	//
	// Parse response.
	//
	if( array_key_exists( kAPI_DATA_RESPONSE, $decoded ) )
	{
		//
		// Save object ID.
		//
		$object2 = $decoded[ kAPI_DATA_RESPONSE ];
		//
		// Get object.
		//
		$object = $decoded[ kAPI_DATA_RESPONSE ];
		//
		// Saving reference for testing.
		//
		$reference = Array();
		$container->UnserialiseObject( $object );			// To feed to MongoDBRef.
		$reference[ kTAG_ID_REFERENCE ] = $object[ kTAG_ID_NATIVE ];
		$reference[ kTAG_CONTAINER_REFERENCE ] = 'CMongoDataWrapper';
		$reference[ kTAG_DATABASE_REFERENCE ] = 'TEST';
		$found = MongoDBRef::get( $db, $reference );
	}
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Parameters:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $params ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	if( ! kUSE_CLIENT )
	{
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'URL:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.htmlspecialchars( $request ).kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'Response:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.htmlspecialchars( $response ).kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
	}
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Decoded:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $decoded ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	if( array_key_exists( kAPI_DATA_RESPONSE, $decoded ) )
	{
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'Found:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $found ); echo( '</pre>'.kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
	}
	echo( kSTYLE_TABLE_POS );
	echo( '<hr>' );
	
	/*===================================================================================
	 *	BATCH INSERT																	*
	 *==================================================================================*/
	echo( '<h4>Batch insert</h4>' );
	echo( '<i>The last <b>Found</b> section shows what we set '
		 .'in the database.</i><br>' );
	//
	// Select objects.
	//
	$object = array( $object3, $object4, $object5 );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CMongoDataWrapperClient( $url );
		$params->Format( kDATA_TYPE_JSON );
		$params->Operation( kAPI_OP_BATCH_INSERT );
		$params->Database( 'TEST' );
		$params->Container( 'CMongoDataWrapper' );
		$params->Options( kAPI_OPT_SAFE, TRUE );
		$params->Object( $object );
		$params->Stamp( TRUE );
//		$params->NoResponse( TRUE );
		$params->LogTrace( TRUE );
		$params->LogRequest( TRUE );
		//
		// Get response.
		//
		$decoded = $params->Execute();
	}
	//
	// Use raw parameters.
	//
	else
	{
		//
		// Prepare object.
		//
		$object = json_encode( $object );
		//
		// Set options.
		//
		$options = array( kAPI_OPT_SAFE => 1 );
		$options = json_encode( $options );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_FORMAT.'='.kDATA_TYPE_JSON;				// Format.
		$params[] = kAPI_OPERATION.'='.kAPI_OP_BATCH_INSERT;		// Command.
		$params[] = kAPI_DATABASE.'='.'TEST';						// Database.
		$params[] = kAPI_CONTAINER.'='.'CMongoDataWrapper';			// Container.
		$params[] = kAPI_DATA_OPTIONS.'='.urlencode( $options );	// Options.
		$params[] = kAPI_DATA_OBJECT.'='.urlencode( $object );		// Object.
		$params[] = kAPI_REQ_STAMP.'='.gettimeofday( true );		// Time-stamp.
//		$params[] = kAPI_OPT_NO_RESP.'='.'1';						// Hide response.
		$params[] = kAPI_OPT_LOG_TRACE.'='.'1';						// Trace exceptions.
		$params[] = kAPI_OPT_LOG_REQUEST.'='.'1';					// Log request.
		//
		// Build request.
		//
		$request = $url.'?'.implode( '&', $params );
		//
		// Get response.
		//
		$response = file_get_contents( $request );
		//
		// Decode response.
		//
		$decoded = json_decode( $response, TRUE );
	}
	//
	// Parse response.
	//
	if( array_key_exists( kAPI_DATA_RESPONSE, $decoded ) )
	{
		//
		// Save results.
		//
		$object3 = $decoded[ kAPI_DATA_RESPONSE ][ 0 ];
		$object4 = $decoded[ kAPI_DATA_RESPONSE ][ 1 ];
		$object5 = $decoded[ kAPI_DATA_RESPONSE ][ 2 ];
	}
 	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Parameters:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $params ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	if( ! kUSE_CLIENT )
	{
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'URL:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.htmlspecialchars( $request ).kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'Response:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.htmlspecialchars( $response ).kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
	}
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Decoded:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $decoded ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_TABLE_POS );
	echo( '<hr>' );
	
	/*===================================================================================
	 *	UPDATE																			*
	 *==================================================================================*/
	echo( '<h4>Update record</h4>' );
	echo( '<i>Look for the "added" offset.</i><br>' );
	//
	// Modify object.
	//
	$object5[ 'Added' ] = 'Added property';
	//
	// Select object.
	//
	$object = $object5;
	//
	// Build query.
	//
	$query = array
	(
		kOPERATOR_AND => array
		(
			array
			(
				kAPI_QUERY_SUBJECT => kTAG_ID_NATIVE,
				kAPI_QUERY_OPERATOR => kOPERATOR_EQUAL,
				kAPI_QUERY_TYPE => $object[ kTAG_ID_NATIVE ][ kTAG_TYPE ],
				kAPI_QUERY_DATA => $object[ kTAG_ID_NATIVE ]
			)
		)
	);
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CMongoDataWrapperClient( $url );
		$params->Format( kDATA_TYPE_JSON );
		$params->Operation( kAPI_OP_UPDATE );
		$params->Database( 'TEST' );
		$params->Container( 'CMongoDataWrapper' );
		$params->Options( kAPI_OPT_SAFE, TRUE );
		$params->Options( kAPI_OPT_SINGLE, TRUE );
		$params->Query( $query );
		$params->Object( $object );
		$params->Stamp( TRUE );
//		$params->NoResponse( TRUE );
		$params->LogTrace( TRUE );
		$params->LogRequest( TRUE );
		//
		// Get response.
		//
		$decoded = $params->Execute();
	}
	//
	// Use raw parameters.
	//
	else
	{
		//
		// Prepare object.
		//
		$object = json_encode( $object );
		//
		// Prepare query.
		//
		$query_enc = json_encode( $query );
		//
		// Set options.
		//
		$options = array( kAPI_OPT_SAFE => 1, kAPI_OPT_SINGLE => 1 );
		$options = json_encode( $options );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_FORMAT.'='.kDATA_TYPE_JSON;				// Format.
		$params[] = kAPI_OPERATION.'='.kAPI_OP_UPDATE;				// Command.
		$params[] = kAPI_REQ_STAMP.'='.gettimeofday( true );		// Time-stamp.
		$params[] = kAPI_DATABASE.'='.'TEST';						// Database.
		$params[] = kAPI_CONTAINER.'='.'CMongoDataWrapper';			// Container.
		$params[] = kAPI_DATA_OPTIONS.'='.urlencode( $options );	// Options.
		$params[] = kAPI_DATA_QUERY.'='.urlencode( $query_enc );	// Query.
		$params[] = kAPI_DATA_OBJECT.'='.urlencode( $object );		// Object.
	//	$params[] = kAPI_OPT_NO_RESP.'='.'1';						// Hide response.
		$params[] = kAPI_OPT_LOG_TRACE.'='.'1';						// Trace exceptions.
		$params[] = kAPI_OPT_LOG_REQUEST.'='.'1';					// Log request.
		//
		// Build request.
		//
		$request = $url.'?'.implode( '&', $params );
		//
		// Get response.
		//
		$response = file_get_contents( $request );
		//
		// Decode response.
		//
		$decoded = json_decode( $response, TRUE );
	}
	//
	// Parse response.
	//
	if( array_key_exists( kAPI_DATA_RESPONSE, $decoded ) )
	{
		//
		// Get object.
		//
		$object = $decoded[ kAPI_DATA_RESPONSE ];
		$object = $decoded[ kAPI_DATA_RESPONSE ];
		//
		// Saving reference for testing.
		//
		$reference = Array();
		$container->UnserialiseObject( $object );			// To feed to MongoDBRef.
		$reference[ kTAG_ID_REFERENCE ] = $object[ kTAG_ID_NATIVE ];
		$reference[ kTAG_CONTAINER_REFERENCE ] = 'CMongoDataWrapper';
		$reference[ kTAG_DATABASE_REFERENCE ] = 'TEST';
		$found = MongoDBRef::get( $db, $reference );
	}
 	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
	if( ! kUSE_CLIENT )
	{
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'Query:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $query ); echo( '</pre>'.kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
	}
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Parameters:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $params ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	if( ! kUSE_CLIENT )
	{
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'URL:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.htmlspecialchars( $request ).kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'Response:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.htmlspecialchars( $response ).kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
	}
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Decoded:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $decoded ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	if( array_key_exists( kAPI_DATA_RESPONSE, $decoded ) )
	{
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'Found:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $found ); echo( '</pre>'.kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
	}
	echo( kSTYLE_TABLE_POS );
	echo( '<hr>' );
	
	/*===================================================================================
	 *	MODIFY																			*
	 *==================================================================================*/
	echo( '<h4>Modify record</h4>' );
	echo( '<i>Remove the "Added" offset and add a "New" offset.</i><br>' );
	//
	// Set modifications.
	//
	$mod = Array();
	$mod[ 'Added' ] = NULL;		// Delete this offset.
	$mod[ 'New' ] = 'Modified';	// Add this offset.
	//
	// Select object.
	//
	$object = $object5;
	//
	// Build query.
	//
	$query = array
	(
		kOPERATOR_AND => array
		(
			array
			(
				kAPI_QUERY_SUBJECT => kTAG_ID_NATIVE,
				kAPI_QUERY_OPERATOR => kOPERATOR_EQUAL,
				kAPI_QUERY_TYPE => $object[ kTAG_ID_NATIVE ][ kTAG_TYPE ],
				kAPI_QUERY_DATA => $object[ kTAG_ID_NATIVE ]
			)
		)
	);
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CMongoDataWrapperClient( $url );
		$params->Format( kDATA_TYPE_JSON );
		$params->Operation( kAPI_OP_MODIFY );
		$params->Database( 'TEST' );
		$params->Container( 'CMongoDataWrapper' );
		$params->Options( kAPI_OPT_SAFE, TRUE );
		$params->Options( kAPI_OPT_SINGLE, TRUE );
		$params->Query( $query );
		$params->Object( $mod );
		$params->Stamp( TRUE );
//		$params->NoResponse( TRUE );
		$params->LogTrace( TRUE );
		$params->LogRequest( TRUE );
		//
		// Get response.
		//
		$decoded = $params->Execute();
	}
	//
	// Use raw parameters.
	//
	else
	{
		//
		// Prepare modification.
		//
		$mod_enc = json_encode( $mod );
		//
		// Prepare query.
		//
		$query_enc = json_encode( $query );
		//
		// Set options.
		//
		$options = array( kAPI_OPT_SAFE => 1, kAPI_OPT_SINGLE => 1 );
		$options = json_encode( $options );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_FORMAT.'='.kDATA_TYPE_JSON;				// Format.
		$params[] = kAPI_OPERATION.'='.kAPI_OP_MODIFY;				// Command.
		$params[] = kAPI_REQ_STAMP.'='.gettimeofday( true );		// Time-stamp.
		$params[] = kAPI_DATABASE.'='.'TEST';						// Database.
		$params[] = kAPI_CONTAINER.'='.'CMongoDataWrapper';			// Container.
		$params[] = kAPI_DATA_OPTIONS.'='.urlencode( $options );	// Options.
		$params[] = kAPI_DATA_QUERY.'='.urlencode( $query_enc );	// Query.
		$params[] = kAPI_DATA_OBJECT.'='.urlencode( $mod_enc );		// Object.
//		$params[] = kAPI_OPT_NO_RESP.'='.'1';						// Hide response.
		$params[] = kAPI_OPT_LOG_TRACE.'='.'1';						// Trace exceptions.
		$params[] = kAPI_OPT_LOG_REQUEST.'='.'1';					// Log request.
		//
		// Build request.
		//
		$request = $url.'?'.implode( '&', $params );
		//
		// Get response.
		//
		$response = file_get_contents( $request );
		//
		// Decode response.
		//
		$decoded = json_decode( $response, TRUE );
	}
	//
	// Saving reference for testing.
	//
	$reference = Array();
	$container->UnserialiseObject( $object );			// To feed to MongoDBRef.
	$reference[ kTAG_ID_REFERENCE ] = $object[ kTAG_ID_NATIVE ];
	$reference[ kTAG_CONTAINER_REFERENCE ] = 'CMongoDataWrapper';
	$reference[ kTAG_DATABASE_REFERENCE ] = 'TEST';
	$found = MongoDBRef::get( $db, $reference );
 	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
	if( ! kUSE_CLIENT )
	{
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'Modifications:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $mod ); echo( '</pre>'.kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'Query:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $query ); echo( '</pre>'.kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
	}
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Parameters:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $params ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	if( ! kUSE_CLIENT )
	{
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'URL:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.htmlspecialchars( $request ).kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
	}
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Decoded:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $decoded ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Found:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $found ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_TABLE_POS );
	echo( '<hr>' );
	
	/*===================================================================================
	 *	INSERT DUPLICATE																*
	 *==================================================================================*/
	echo( '<h4>Insert duplicate object</h4>' );
	echo( '<i>Should fail: look at the status.</i><br>' );
	//
	// Select object.
	//
	$object = $object1;
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CMongoDataWrapperClient( $url );
		$params->Format( kDATA_TYPE_JSON );
		$params->Operation( kAPI_OP_INSERT );
		$params->Database( 'TEST' );
		$params->Container( 'CMongoDataWrapper' );
		$params->Options( kAPI_OPT_SAFE, TRUE );
		$params->Object( $object );
		$params->Stamp( TRUE );
//		$params->NoResponse( TRUE );
//		$params->LogTrace( TRUE );
		$params->LogRequest( TRUE );
		//
		// Get response.
		//
		$decoded = $params->Execute();
	}
	//
	// Use raw parameters.
	//
	else
	{
		//
		// Prepare object.
		//
		$object = json_encode( $object );
		//
		// Set options.
		//
		$options = array( kAPI_OPT_SAFE => 1 );
		$options = json_encode( $options );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_FORMAT.'='.kDATA_TYPE_JSON;				// Format.
		$params[] = kAPI_OPERATION.'='.kAPI_OP_INSERT;				// Command.
		$params[] = kAPI_REQ_STAMP.'='.gettimeofday( true );		// Time-stamp.
		$params[] = kAPI_DATABASE.'='.'TEST';						// Database.
		$params[] = kAPI_CONTAINER.'='.'CMongoDataWrapper';			// Container.
		$params[] = kAPI_DATA_OPTIONS.'='.urlencode( $options );	// Options.
		$params[] = kAPI_DATA_OBJECT.'='.urlencode( $object );		// Object.
//		$params[] = kAPI_OPT_NO_RESP.'='.'1';						// Hide response.
//		$params[] = kAPI_OPT_LOG_TRACE.'='.'1';						// Trace exceptions.
		$params[] = kAPI_OPT_LOG_REQUEST.'='.'1';					// Log request.
		//
		// Build request.
		//
		$request = $url.'?'.implode( '&', $params );
		//
		// Get response.
		//
		$response = file_get_contents( $request );
		//
		// Decode response.
		//
		$decoded = json_decode( $response, TRUE );
	}
	//
	// Parse response.
	//
	if( array_key_exists( kAPI_DATA_RESPONSE, $decoded ) )
	{
		//
		// Get object.
		//
		$object = $decoded[ kAPI_DATA_RESPONSE ];
		//
		// Saving reference for testing.
		//
		$reference = Array();
		$container->UnserialiseObject( $object );			// To feed to MongoDBRef.
		$reference[ kTAG_ID_REFERENCE ] = $object[ kTAG_ID_NATIVE ];
		$reference[ kTAG_CONTAINER_REFERENCE ] = 'CMongoDataWrapper';
		$reference[ kTAG_DATABASE_REFERENCE ] = 'TEST';
		$found = MongoDBRef::get( $db, $reference );
	}
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Parameters:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $params ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	if( ! kUSE_CLIENT )
	{
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'URL:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.htmlspecialchars( $request ).kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'Response:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.htmlspecialchars( $response ).kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
	}
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Decoded:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $decoded ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	if( array_key_exists( kAPI_DATA_RESPONSE, $decoded ) )
	{
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'Found:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $found ); echo( '</pre>'.kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
	}
	echo( kSTYLE_TABLE_POS );
	echo( '<hr>' );
	
	/*===================================================================================
	 *	UPDATE MISSING																	*
	 *==================================================================================*/
	echo( '<h4>Update missing object</h4>' );
	echo( '<i>Will not fail, but you can check the affected count in ['
		 .kAPI_AFFECTED_COUNT
		 .'] of the status section ['
		 .kAPI_DATA_STATUS
		 .'].</i><br>' );
	//
	// Select object.
	//
	$object = $object1;
	//
	// Build query.
	//
	$query = array
	(
		kOPERATOR_AND => array
		(
			array
			(
				kAPI_QUERY_SUBJECT => 'not there',
				kAPI_QUERY_OPERATOR => kOPERATOR_EQUAL,
				kAPI_QUERY_TYPE => kDATA_TYPE_STRING,
				kAPI_QUERY_DATA => 'either'
			)
		)
	);
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CMongoDataWrapperClient( $url );
		$params->Format( kDATA_TYPE_JSON );
		$params->Operation( kAPI_OP_UPDATE );
		$params->Database( 'TEST' );
		$params->Container( 'CMongoDataWrapper' );
		$params->Options( kAPI_OPT_SAFE, TRUE );
		$params->Options( kAPI_OPT_SINGLE, TRUE );
		$params->Query( $query );
		$params->Object( $object );
		$params->Stamp( TRUE );
//		$params->NoResponse( TRUE );
		$params->LogTrace( TRUE );
		$params->LogRequest( TRUE );
		//
		// Get response.
		//
		$decoded = $params->Execute();
	}
	//
	// Use raw parameters.
	//
	else
	{
		//
		// Prepare object.
		//
		$object = json_encode( $object );
		//
		// Prepare query.
		//
		$query_enc = json_encode( $query );
		//
		// Set options.
		//
		$options = array( kAPI_OPT_SAFE => 1, kAPI_OPT_SINGLE => 1 );
		$options = json_encode( $options );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_FORMAT.'='.kDATA_TYPE_JSON;				// Format.
		$params[] = kAPI_OPERATION.'='.kAPI_OP_UPDATE;				// Command.
		$params[] = kAPI_REQ_STAMP.'='.gettimeofday( true );		// Time-stamp.
		$params[] = kAPI_DATABASE.'='.'TEST';						// Database.
		$params[] = kAPI_CONTAINER.'='.'CMongoDataWrapper';			// Container.
		$params[] = kAPI_DATA_OPTIONS.'='.urlencode( $options );	// Options.
		$params[] = kAPI_DATA_QUERY.'='.urlencode( $query_enc );	// Query.
		$params[] = kAPI_DATA_OBJECT.'='.urlencode( $object );		// Object.
//		$params[] = kAPI_OPT_NO_RESP.'='.'1';						// Hide response.
		$params[] = kAPI_OPT_LOG_TRACE.'='.'1';						// Trace exceptions.
		$params[] = kAPI_OPT_LOG_REQUEST.'='.'1';					// Log request.
		//
		// Build request.
		//
		$request = $url.'?'.implode( '&', $params );
		//
		// Get response.
		//
		$response = file_get_contents( $request );
		//
		// Decode response.
		//
		$decoded = json_decode( $response, TRUE );
	}
 	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
	if( ! kUSE_CLIENT )
	{
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'Query:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $query ); echo( '</pre>'.kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
	}
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Parameters:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $params ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	if( ! kUSE_CLIENT )
	{
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'URL:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.htmlspecialchars( $request ).kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'Response:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.htmlspecialchars( $response ).kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
	}
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Decoded:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $decoded ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_TABLE_POS );
	echo( '<hr>' );
	
	/*===================================================================================
	 *	COUNT																			*
	 *==================================================================================*/
	echo( '<h4>Get query count</h4>' );
	echo( '<i>Should return a count of 3 in ['
		 .kAPI_AFFECTED_COUNT
		 .'] of the status section ['
		 .kAPI_DATA_STATUS
		 .'].</i><br>' );
	//
	// Build query.
	//
	$query = array
	(
		kOPERATOR_OR => array
		(
			array
			(
				kAPI_QUERY_SUBJECT => 'Name',
				kAPI_QUERY_OPERATOR => kOPERATOR_EQUAL,
				kAPI_QUERY_TYPE => kDATA_TYPE_STRING,
				kAPI_QUERY_DATA => 'Luca'
			),
			array
			(
				kAPI_QUERY_SUBJECT => 'Date',
				kAPI_QUERY_OPERATOR => kOPERATOR_IRANGE,
				kAPI_QUERY_TYPE => kDATA_TYPE_STAMP,
				kAPI_QUERY_DATA => array
				(
					new CDataTypeStamp(),
					new CDataTypeStamp( '2011-12-31' )
				)
			)
		)
	);
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CMongoDataWrapperClient( $url );
		$params->Format( kDATA_TYPE_JSON );
		$params->Operation( kAPI_OP_COUNT );
		$params->Database( 'TEST' );
		$params->Container( 'CMongoDataWrapper' );
		$params->Query( $query );
		$params->Stamp( TRUE );
		//
		// Get response.
		//
		$decoded = $params->Execute();
	}
	//
	// Use raw parameters.
	//
	else
	{
		//
		// Prepare query.
		//
		$query_enc = json_encode( $query );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_FORMAT.'='.kDATA_TYPE_JSON;				// Format.
		$params[] = kAPI_OPERATION.'='.kAPI_OP_COUNT;				// Command.
		$params[] = kAPI_REQ_STAMP.'='.gettimeofday( true );		// Time-stamp.
		$params[] = kAPI_DATABASE.'='.'TEST';						// Database.
		$params[] = kAPI_CONTAINER.'='.'CMongoDataWrapper';			// Container.
		$params[] = kAPI_DATA_QUERY.'='.urlencode( $query_enc );	// Query.
		//
		// Build request.
		//
		$request = $url.'?'.implode( '&', $params );
		//
		// Get response.
		//
		$response = file_get_contents( $request );
		//
		// Decode response.
		//
		$decoded = json_decode( $response, TRUE );
	}
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
	if( ! kUSE_CLIENT )
	{
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'Query:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $query ); echo( '</pre>'.kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
	}
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Parameters:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $params ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	if( ! kUSE_CLIENT )
	{
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'URL:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.htmlspecialchars( $request ).kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'Response:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.htmlspecialchars( $response ).kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
	}
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Decoded:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $decoded ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_TABLE_POS );
	echo( '<hr>' );
	
	/*===================================================================================
	 *	GET-ONE																			*
	 *==================================================================================*/
	echo( '<h4>Get one</h4>' );
	echo( '<i>Should find one record.</i><br>' );
	//
	// Build query.
	//
	$query = array
	(
		kOPERATOR_AND => array
		(
			array
			(
				kAPI_QUERY_SUBJECT => 'Number',
				kAPI_QUERY_OPERATOR => kOPERATOR_GREAT_EQUAL,
				kAPI_QUERY_TYPE => kDATA_TYPE_INT64,
				kAPI_QUERY_DATA => new CDataTypeInt64( '123456789123456' )
			)
		)
	);
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CMongoDataWrapperClient( $url );
		$params->Format( kDATA_TYPE_JSON );
		$params->Operation( kAPI_OP_GET_ONE );
		$params->Database( 'TEST' );
		$params->Container( 'CMongoDataWrapper' );
		$params->Query( $query );
		$params->Stamp( TRUE );
		//
		// Get response.
		//
		$decoded = $params->Execute();
	}
	//
	// Use raw parameters.
	//
	else
	{
		//
		// Prepare query.
		//
		$query_enc = json_encode( $query );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_FORMAT.'='.kDATA_TYPE_JSON;				// Format.
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_ONE;				// Command.
		$params[] = kAPI_REQ_STAMP.'='.gettimeofday( true );		// Time-stamp.
		$params[] = kAPI_DATABASE.'='.'TEST';						// Database.
		$params[] = kAPI_CONTAINER.'='.'CMongoDataWrapper';			// Container.
		$params[] = kAPI_DATA_QUERY.'='.urlencode( $query_enc );	// Query.
		//
		// Build request.
		//
		$request = $url.'?'.implode( '&', $params );
		//
		// Get response.
		//
		$response = file_get_contents( $request );
		//
		// Decode response.
		//
		$decoded = json_decode( $response, TRUE );
	}
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
	if( ! kUSE_CLIENT )
	{
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'Query:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $query ); echo( '</pre>'.kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
	}
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Parameters:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $params ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	if( ! kUSE_CLIENT )
	{
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'URL:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.htmlspecialchars( $request ).kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'Response:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.htmlspecialchars( $response ).kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
	}
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Decoded:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $decoded ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_TABLE_POS );
	echo( '<hr>' );
	
	/*===================================================================================
	 *	GET																				*
	 *==================================================================================*/
	echo( '<h4>Get</h4>' );
	echo( '<i>Should return 3 records including only name '
		 .'and surname sorted by surname.</i><br>' );
	//
	// Build query.
	//
	$query = array
	(
		kOPERATOR_OR => array
		(
			array
			(
				kAPI_QUERY_SUBJECT => 'Name',
				kAPI_QUERY_OPERATOR => kOPERATOR_EQUAL,
				kAPI_QUERY_TYPE => kDATA_TYPE_STRING,
				kAPI_QUERY_DATA => 'Luca'
			),
			array
			(
				kAPI_QUERY_SUBJECT => 'Date',
				kAPI_QUERY_OPERATOR => kOPERATOR_IRANGE,
				kAPI_QUERY_TYPE => kDATA_TYPE_STAMP,
				kAPI_QUERY_DATA => array
				(
					new CDataTypeStamp(),
					new CDataTypeStamp( '2011-12-31' )
				)
			)
		)
	);
	//
	// Build fields.
	//
	$fields = array( 'Name' => TRUE, 'Surname' => TRUE );
	//
	// Build sort.
	//
	$sort = array( 'Surname' => 1 );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CMongoDataWrapperClient( $url );
		$params->Format( kDATA_TYPE_JSON );
		$params->Operation( kAPI_OP_GET );
		$params->Database( 'TEST' );
		$params->Container( 'CMongoDataWrapper' );
		$params->Query( $query );
		$params->Fields( $fields );
		$params->Sort( $sort );
		//
		// Get response.
		//
		$decoded = $params->Execute();
	}
	//
	// Use raw parameters.
	//
	else
	{
		//
		// Prepare query.
		//
		$query_enc = json_encode( $query );
		//
		// Prepare fields.
		//
		$fields_enc = json_encode( $fields );
		//
		// Prepare sort.
		//
		$sort_enc = json_encode( $sort );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_FORMAT.'='.kDATA_TYPE_JSON;				// Format.
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET;					// Command.
		$params[] = kAPI_DATABASE.'='.'TEST';						// Database.
		$params[] = kAPI_CONTAINER.'='.'CMongoDataWrapper';			// Container.
		$params[] = kAPI_DATA_QUERY.'='.urlencode( $query_enc );	// Query.
		$params[] = kAPI_DATA_FIELD.'='.urlencode( $fields_enc );	// Fields.
		$params[] = kAPI_DATA_SORT.'='.urlencode( $sort_enc );		// Sort.
		//
		// Build request.
		//
		$request = $url.'?'.implode( '&', $params );
		//
		// Get response.
		//
		$response = file_get_contents( $request );
		//
		// Decode response.
		//
		$decoded = json_decode( $response, TRUE );
	}
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
	if( ! kUSE_CLIENT )
	{
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'Query:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $query ); echo( '</pre>'.kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
	}
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Parameters:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $params ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	if( ! kUSE_CLIENT )
	{
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'URL:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.htmlspecialchars( $request ).kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'Response:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.htmlspecialchars( $response ).kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
	}
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Decoded:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $decoded ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_TABLE_POS );
	echo( '<hr>' );
	
	/*===================================================================================
	 *	GET WITH LIMITS																	*
	 *==================================================================================*/
	echo( '<h4>Get WITH LIMITS</h4>' );
	echo( '<i>Should return 3 records out of 4 records including only name '
		 .'and surname sorted descending by surname.</i><br>' );
	//
	// Build query.
	//
	$query = array
	(
		kOPERATOR_OR => array
		(
			array
			(
				kAPI_QUERY_SUBJECT => 'Name',
				kAPI_QUERY_OPERATOR => kOPERATOR_EQUAL,
				kAPI_QUERY_TYPE => kDATA_TYPE_STRING,
				kAPI_QUERY_DATA => 'Luca'
			),
			array
			(
				kAPI_QUERY_SUBJECT => 'Date',
				kAPI_QUERY_OPERATOR => kOPERATOR_IRANGE,
				kAPI_QUERY_TYPE => kDATA_TYPE_STAMP,
				kAPI_QUERY_DATA => array
				(
					new CDataTypeStamp(),
					new CDataTypeStamp( '2011-12-31' )
				)
			),
			array
			(
				kAPI_QUERY_SUBJECT => 'Cat.Colours',
				kAPI_QUERY_OPERATOR => kOPERATOR_PREFIX,
				kAPI_QUERY_TYPE => kDATA_TYPE_STRING,
				kAPI_QUERY_DATA => 'Blu'
			)
		)
	);
	//
	// Build fields.
	//
	$fields = array( 'Name' => TRUE, 'Surname' => TRUE );
	//
	// Build sort.
	//
	$sort = array( 'Surname' => -1 );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CMongoDataWrapperClient( $url );
		$params->Format( kDATA_TYPE_JSON );
		$params->Operation( kAPI_OP_GET );
		$params->Database( 'TEST' );
		$params->Container( 'CMongoDataWrapper' );
		$params->Query( $query );
		$params->Fields( $fields );
		$params->Sort( $sort );
		$params->Start( 0 );
		$params->Limit( 3 );
		//
		// Get response.
		//
		$decoded = $params->Execute();
	}
	//
	// Use raw parameters.
	//
	else
	{
		//
		// Prepare query.
		//
		$query_enc = json_encode( $query );
		//
		// Prepare fields.
		//
		$fields_enc = json_encode( $fields );
		//
		// Prepare sort.
		//
		$sort_enc = json_encode( $sort );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_FORMAT.'='.kDATA_TYPE_JSON;				// Format.
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET;					// Command.
		$params[] = kAPI_DATABASE.'='.'TEST';						// Database.
		$params[] = kAPI_CONTAINER.'='.'CMongoDataWrapper';			// Container.
		$params[] = kAPI_DATA_QUERY.'='.urlencode( $query_enc );	// Query.
		$params[] = kAPI_DATA_FIELD.'='.urlencode( $fields_enc );	// Fields.
		$params[] = kAPI_DATA_SORT.'='.urlencode( $sort_enc );		// Sort.
		$params[] = kAPI_PAGE_START.'='.'0';						// Page start.
		$params[] = kAPI_PAGE_LIMIT.'='.'3';						// Page limits.
		//
		// Build request.
		//
		$request = $url.'?'.implode( '&', $params );
		//
		// Get response.
		//
		$response = file_get_contents( $request );
		//
		// Decode response.
		//
		$decoded = json_decode( $response, TRUE );
	}
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
	if( ! kUSE_CLIENT )
	{
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'Query:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $query ); echo( '</pre>'.kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
	}
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Parameters:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $params ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	if( ! kUSE_CLIENT )
	{
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'URL:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.htmlspecialchars( $request ).kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'Response:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.htmlspecialchars( $response ).kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
	}
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Decoded:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $decoded ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_TABLE_POS );
	echo( '<hr>' );
	
	/*===================================================================================
	 *	REFERENCE																		*
	 *==================================================================================*/
	echo( '<h4>Reference object with storage</h4>' );
	echo( '<i>Note that we include the database and container references '
		 .'in the object reference.</i><br>' );
	//
	// Build reference.
	//
	$reference = Array();
	$reference[ kTAG_ID_REFERENCE ] = $object3[ kTAG_ID_NATIVE ];
	$reference[ kTAG_CONTAINER_REFERENCE ] = 'CMongoDataWrapper';
	$reference[ kTAG_DATABASE_REFERENCE ] = 'TEST';
	//
	// Convert reference.
	//
	CDataType::SerialiseObject( $reference );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CMongoDataWrapperClient( $url );
		$params->Format( kDATA_TYPE_JSON );
		$params->Operation( kAPI_OP_GET_OBJECT_REF );
		$params->Object( $reference );
		//
		// Get response.
		//
		$decoded = $params->Execute();
	}
	//
	// Use raw parameters.
	//
	else
	{
		//
		// Prepare reference.
		//
		$object = json_encode( $reference );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_FORMAT.'='.kDATA_TYPE_JSON;				// Format.
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_OBJECT_REF;		// Command.
		$params[] = kAPI_DATA_OBJECT.'='.urlencode( $object );		// Object.
		//
		// Build request.
		//
		$request = $url.'?'.implode( '&', $params );
		//
		// Get response.
		//
		$response = file_get_contents( $request );
		//
		// Decode response.
		//
		$decoded = json_decode( $response, TRUE );
	}
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
	if( ! kUSE_CLIENT )
	{
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'Reference:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $reference ); echo( '</pre>'.kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
	}
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Parameters:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $params ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	if( ! kUSE_CLIENT )
	{
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'URL:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.htmlspecialchars( $request ).kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'Response:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.htmlspecialchars( $response ).kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
	}
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Decoded:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $decoded ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_TABLE_POS );
	echo( '<hr>' );
	
	/*===================================================================================
	 *	REFERENCE																		*
	 *==================================================================================*/
	echo( '<h4>Reference object without storage</h4>' );
	echo( '<i>Note that we use the object as reference and provide both the database '
		 .'and container references among the parameters.</i><br>' );
	//
	// Build reference.
	//
	$reference = $object3;
	//
	// Convert reference.
	//
	CDataType::SerialiseObject( $reference );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
	}
	//
	// Use raw parameters.
	//
	else
	{
	}
	//
	// Prepare object.
	//
	$object = json_encode( $reference );
	//
	// Build parameters.
	//
	$params = Array();
	$params[] = kAPI_FORMAT.'='.kDATA_TYPE_JSON;				// Format.
	$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_OBJECT_REF;		// Command.
	$params[] = kAPI_REQ_STAMP.'='.gettimeofday( true );		// Time-stamp.
	$params[] = kAPI_DATABASE.'='.'TEST';						// Database.
	$params[] = kAPI_CONTAINER.'='.'CMongoDataWrapper';			// Container.
	$params[] = kAPI_DATA_OBJECT.'='.urlencode( $object );		// Object.
//	$params[] = kAPI_OPT_NO_RESP.'='.'1';						// Hide response.
	$params[] = kAPI_OPT_LOG_REQUEST.'='.'1';					// Log request.
	//
	// Build request.
	//
	$request = $url.'?'.implode( '&', $params );
	//
	// Get response.
	//
	$response = file_get_contents( $request );
	//
	// Decode response.
	//
	$decoded = json_decode( $response, TRUE );
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Reference:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $reference ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Parameters:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $params ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'URL:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.htmlspecialchars( $request ).kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Response:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.htmlspecialchars( $response ).kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Decoded:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $decoded ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_TABLE_POS );
	echo( '<hr>' );
exit;
	
	/*===================================================================================
	 *	DELETE ONE																		*
	 *==================================================================================*/
	echo( '<h4>Delete one record</h4>' );
	echo( '<i>Here we select 2 records, but delete only first: see options ['
		 .kAPI_DATA_OPTIONS
		 .'] and affected count ['
		 .kAPI_AFFECTED_COUNT
		 .'].</i><br>' );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
	}
	//
	// Use raw parameters.
	//
	else
	{
	}
	//
	// Prepare query.
	//
	$query = array
	(
		kOPERATOR_AND => array
		(
			array
			(
				kAPI_QUERY_SUBJECT => 'Inst',
				kAPI_QUERY_OPERATOR => kOPERATOR_CONTAINS,
				kAPI_QUERY_TYPE => kDATA_TYPE_STRING,
				kAPI_QUERY_DATA => 'International'
			)
		)
	);
	$query_enc = json_encode( $query );
	//
	// Set options.
	//
	$options = array( kAPI_OPT_SAFE => 1, kAPI_OPT_SINGLE => 1 );
	$options = json_encode( $options );
	//
	// Build parameters.
	//
	$params = Array();
	$params[] = kAPI_FORMAT.'='.kDATA_TYPE_JSON;				// Format.
	$params[] = kAPI_OPERATION.'='.kAPI_OP_DEL;					// Command.
	$params[] = kAPI_REQ_STAMP.'='.gettimeofday( true );		// Time-stamp.
	$params[] = kAPI_DATABASE.'='.'TEST';						// Database.
	$params[] = kAPI_CONTAINER.'='.'CMongoDataWrapper';			// Container.
	$params[] = kAPI_DATA_OPTIONS.'='.urlencode( $options );	// Options.
	$params[] = kAPI_DATA_QUERY.'='.urlencode( $query_enc );	// Query.
	$params[] = kAPI_DATA_OBJECT.'='.urlencode( $object );		// Object.
//	$params[] = kAPI_OPT_NO_RESP.'='.'1';						// Hide response.
	$params[] = kAPI_OPT_LOG_TRACE.'='.'1';						// Trace exceptions.
	$params[] = kAPI_OPT_LOG_REQUEST.'='.'1';					// Log request.
	//
	// Build request.
	//
	$request = $url.'?'.implode( '&', $params );
	//
	// Get response.
	//
	$response = file_get_contents( $request );
	//
	// Decode response.
	//
	$decoded = json_decode( $response, TRUE );
	//
	// Parse response.
	//
	if( array_key_exists( kAPI_DATA_RESPONSE, $decoded ) )
	{
		$object = $decoded[ kAPI_DATA_RESPONSE ];
		//
		// Saving reference for testing.
		//
		$reference = Array();
		$container->UnserialiseObject( $object );			// To feed to MongoDBRef.
		$reference[ kTAG_ID_REFERENCE ] = $object[ kTAG_ID_NATIVE ];
		$reference[ kTAG_CONTAINER_REFERENCE ] = 'CMongoDataWrapper';
		$reference[ kTAG_DATABASE_REFERENCE ] = 'TEST';
		$found = MongoDBRef::get( $db, $reference );
	}
 	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Query:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $query ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Parameters:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $params ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'URL:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.htmlspecialchars( $request ).kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Response:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.htmlspecialchars( $response ).kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Decoded:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $decoded ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	if( array_key_exists( kAPI_DATA_RESPONSE, $decoded ) )
	{
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'Found:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $found ); echo( '</pre>'.kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
	}
	echo( kSTYLE_TABLE_POS );
	echo( '<hr>' );
	
	/*===================================================================================
	 *	DELETE MORE																		*
	 *==================================================================================*/
	echo( '<h4>Delete more records</h4>' );
	echo( '<i>Here we select 3 records and delete them all, see affected count ['
		 .kAPI_AFFECTED_COUNT
		 .'] in status ['
		 .kAPI_DATA_STATUS
		 .'].</i><br>' );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
	}
	//
	// Use raw parameters.
	//
	else
	{
	}
	//
	// Prepare query.
	//
	$query = array
	(
		kOPERATOR_AND => array
		(
			array
			(
				kAPI_QUERY_SUBJECT => 'Cat.Colours',
				kAPI_QUERY_OPERATOR => kOPERATOR_IN,
				kAPI_QUERY_TYPE => kDATA_TYPE_STRING,
				kAPI_QUERY_DATA => array( 'Blue', 'Green' )
			)
		)
	);
	$query_enc = json_encode( $query );
	//
	// Set options.
	//
	$options = array( kAPI_OPT_SAFE => 1 );
	$options = json_encode( $options );
	//
	// Build parameters.
	//
	$params = Array();
	$params[] = kAPI_FORMAT.'='.kDATA_TYPE_JSON;				// Format.
	$params[] = kAPI_OPERATION.'='.kAPI_OP_DEL;					// Command.
	$params[] = kAPI_REQ_STAMP.'='.gettimeofday( true );		// Time-stamp.
	$params[] = kAPI_DATABASE.'='.'TEST';						// Database.
	$params[] = kAPI_CONTAINER.'='.'CMongoDataWrapper';			// Container.
	$params[] = kAPI_DATA_OPTIONS.'='.urlencode( $options );	// Options.
	$params[] = kAPI_DATA_QUERY.'='.urlencode( $query_enc );	// Query.
	$params[] = kAPI_DATA_OBJECT.'='.urlencode( $object );		// Object.
//	$params[] = kAPI_OPT_NO_RESP.'='.'1';						// Hide response.
	$params[] = kAPI_OPT_LOG_TRACE.'='.'1';						// Trace exceptions.
	$params[] = kAPI_OPT_LOG_REQUEST.'='.'1';					// Log request.
	//
	// Build request.
	//
	$request = $url.'?'.implode( '&', $params );
	//
	// Get response.
	//
	$response = file_get_contents( $request );
	//
	// Decode response.
	//
	$decoded = json_decode( $response, TRUE );
	//
	// Parse response.
	//
	if( array_key_exists( kAPI_DATA_RESPONSE, $decoded ) )
	{
		$object = $decoded[ kAPI_DATA_RESPONSE ];
		//
		// Saving reference for testing.
		//
		$reference = Array();
		$container->UnserialiseObject( $object );			// To feed to MongoDBRef.
		$reference[ kTAG_ID_REFERENCE ] = $object[ kTAG_ID_NATIVE ];
		$reference[ kTAG_CONTAINER_REFERENCE ] = 'CMongoDataWrapper';
		$reference[ kTAG_DATABASE_REFERENCE ] = 'TEST';
		$found = MongoDBRef::get( $db, $reference );
	}
 	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Query:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $query ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Parameters:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $params ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'URL:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.htmlspecialchars( $request ).kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Response:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.htmlspecialchars( $response ).kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Decoded:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $decoded ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	if( array_key_exists( kAPI_DATA_RESPONSE, $decoded ) )
	{
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'Found:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $found ); echo( '</pre>'.kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
	}
	echo( kSTYLE_TABLE_POS );
	echo( '<hr>' );
	
	/*===================================================================================
	 *	GET																				*
	 *==================================================================================*/
	echo( '<h4>Who\'s left?</h4>' );
	echo( '<i>Here we omit the query.</i><br>' );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
	}
	//
	// Use raw parameters.
	//
	else
	{
	}
	//
	// Build parameters.
	//
	$params = Array();
	$params[] = kAPI_FORMAT.'='.kDATA_TYPE_JSON;				// Format.
	$params[] = kAPI_OPERATION.'='.kAPI_OP_GET;					// Command.
	$params[] = kAPI_REQ_STAMP.'='.gettimeofday( true );		// Time-stamp.
	$params[] = kAPI_DATABASE.'='.'TEST';						// Database.
	$params[] = kAPI_CONTAINER.'='.'CMongoDataWrapper';			// Container.
//	$params[] = kAPI_DATA_QUERY.'='.urlencode( $query_enc );	// Query.
	$params[] = kAPI_OPT_LOG_TRACE.'='.'1';						// Trace exceptions.
	$params[] = kAPI_OPT_LOG_REQUEST.'='.'1';					// Log request.
	//
	// Build request.
	//
	$request = $url.'?'.implode( '&', $params );
	//
	// Get response.
	//
	$response = file_get_contents( $request );
	//
	// Decode response.
	//
	$decoded = json_decode( $response, TRUE );
 	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Query:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $query ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Parameters:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $params ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'URL:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.htmlspecialchars( $request ).kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Response:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.htmlspecialchars( $response ).kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Decoded:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $decoded ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_TABLE_POS );
	echo( '<hr>' );
	
	echo( '<h3>DONE</h3>' );
}
catch( Exception $error )
{
	echo( '<h3>Unexpected exception</h3>' );
	echo( CException::AsHTML( $error ) );
	echo( '<pre>'.(string) $error.'</pre>' );
	echo( '<hr>' );
}

?>
