<?php
	
/**
 * {@link CWarehouseWrapper.php Warehouse} wrapper object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * base object {@link CWarehouseWrapper class}.
 *
 *	@package	Test
 *	@subpackage	Wrappers
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 10/04/2012
 */

/*=======================================================================================
 *																						*
 *										MyTest.php										*
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

/**
 * Server environment.
 *
 * This include file contains the server run-time definitions.
 */
require_once( "/Library/WebServer/Library/wrapper/local/server.inc.php" );

//
// Categories default includes.
//
require_once( '/Library/WebServer/Library/wrapper/local/categories.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."CUser.php" );
require_once( kPATH_LIBRARY_SOURCE."CWarehouseWrapperClient.php" );


/*=======================================================================================
 *	GLOBAL DEFINITIONS																	*
 *======================================================================================*/
 
//
// Use raw parameters or use wrapper client?.
//
define( 'kUSE_CLIENT', FALSE );


/*=======================================================================================
 *	LOCAL DEFINITIONS																	*
 *======================================================================================*/
 
//
// Use raw parameters or use wrapper client?.
//
define( 'kDEFAULT_DATABASE', 'TEST' );


/*=======================================================================================
 *	TEST WRAPPER OBJECT																	*
 *======================================================================================*/
 
//
// Init local storage.
//
$url = 'http://localhost/wrapper/WarehouseWrapper.php';

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
	$collection = $db->selectCollection( 'CWarehouseWrapper' );
	
	//
	// Create utility container.
	//
	$container = new CMongoContainer( $collection );

	/*===================================================================================
	 *	CREATE USERS																	*
	 *==================================================================================*/
	 
	//
	// Create user 1.
	//
	$milko = new CUser();
	$milko->Code( 'MILKO' );
	$milko->Password( 'MilkoPass' );
	$milko->Name( 'Milko Škofič' );
	$milko->Email( 'm.skofic@cgiar.org' );
	$milko_id = $milko->Commit( $container );

	//
	// Create user 2.
	//
	$luca = new CUser();
	$luca->Code( 'LUCA' );
	$luca->Password( 'LucaPass' );
	$luca->Name( 'Luca Matteis' );
	$luca->Email( 'l.matteis@cgiar.org' );
	$luca->Manager( $milko );
	$luca_id = $luca->Commit( $container );
	
	//
	// Create user 3.
	//
	$marco = new CUser();
	$marco->Code( 'MARCO' );
	$marco->Password( 'MarcoPass' );
	$marco->Name( 'Marco Frangella' );
	$marco->Email( 'm.frangella@cgiar.org' );
	$marco->Manager( $luca );
	$marco_id = $marco->Commit( $container );
	
	echo( '<h4>Users</h4>' );
	echo( kSTYLE_TABLE_PRE );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Milko ID:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $milko_id ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Milko:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $milko ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Luca ID:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $luca_id ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Luca:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $luca ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Marco ID:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $marco_id ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Marco:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $marco ); echo( '</pre>'.kSTYLE_DATA_POS );
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
		if( kUSE_CLIENT )
		{
			echo( kSTYLE_ROW_PRE );
			echo( kSTYLE_HEAD_PRE.'Parameters:'.kSTYLE_HEAD_POS );
			echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $params ); echo( '</pre>'.kSTYLE_DATA_POS );
			echo( kSTYLE_ROW_POS );
		}
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
			$params->Format( kTYPE_PHP );
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
			$params[] = kAPI_FORMAT.'='.kTYPE_PHP;					// Format.
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
	 *	PING																			*
	 *==================================================================================*/
	echo( '<h4>Ping</h4>' );
	//
	// Ping wrapper.
	//
	$params = array( (kAPI_FORMAT.'='.kTYPE_JSON),
					 (kAPI_OPERATION.'='.kAPI_OP_PING) );
	$request = implode( '&', $params );
	$request = "$url?$request";
	$response = file_get_contents( $request );
	$decoded = json_decode( $response, TRUE );
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'URL:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.htmlspecialchars( $request ).kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Parameters:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $params ); echo( '</pre>'.kSTYLE_DATA_POS );
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

	/*===================================================================================
	 *	PING WITH LOG																	*
	 *==================================================================================*/
	echo( '<h4>Ping with log</h4>' );
	//
	// Ping wrapper.
	//
	$params = array( (kAPI_FORMAT.'='.kTYPE_JSON),
					 (kAPI_OPERATION.'='.kAPI_OP_PING),
					 (kAPI_OPT_LOG_REQUEST.'='.'1') );
	$request = implode( '&', $params );
	$request = "$url?$request";
	$response = file_get_contents( $request );
	$decoded = json_decode( $response, TRUE );
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'URL:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.htmlspecialchars( $request ).kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Parameters:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $params ); echo( '</pre>'.kSTYLE_DATA_POS );
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

	/*===================================================================================
	 *	PING WITH TIMER																	*
	 *==================================================================================*/
	echo( '<h4>Test ping with timers</h4>' );
	//
	// Ping wrapper.
	//
	$params = array( (kAPI_FORMAT.'='.kTYPE_JSON),
					 (kAPI_OPERATION.'='.kAPI_OP_PING),
					 (kAPI_REQ_STAMP.'='.gettimeofday( true )),
					 (kAPI_OPT_LOG_REQUEST.'='.'1') );
	$request = implode( '&', $params );
	$request = "$url?$request";
	$response = file_get_contents( $request );
	$decoded = json_decode( $response, TRUE );
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'URL:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.htmlspecialchars( $request ).kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Parameters:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $params ); echo( '</pre>'.kSTYLE_DATA_POS );
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

	/*===================================================================================
	 *	INVALID OPERATION																*
	 *==================================================================================*/
	echo( '<h4>Test invalid operation</h4>' );
	//
	// Invalid operator.
	//
	$params = array( (kAPI_FORMAT.'='.kTYPE_JSON),
					 (kAPI_OPERATION.'='.'XXX'),
					 (kAPI_REQ_STAMP.'='.gettimeofday( true )),
					 (kAPI_OPT_LOG_REQUEST.'='.'1') );
	$request = implode( '&', $params );
	$request = "$url?$request";
	$response = file_get_contents( $request );
	$decoded = json_decode( $response, TRUE );
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'URL:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.htmlspecialchars( $request ).kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Parameters:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $params ); echo( '</pre>'.kSTYLE_DATA_POS );
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

	/*===================================================================================
	 *	MISSING OPERATION																*
	 *==================================================================================*/
	echo( '<h4>Test missing operation</h4>' );
	//
	// Missing operator.
	//
	$params = array( (kAPI_FORMAT.'='.kTYPE_JSON),
					 (kAPI_REQ_STAMP.'='.gettimeofday( true )),
					 (kAPI_OPT_LOG_REQUEST.'='.'1') );
	$request = implode( '&', $params );
	$request = "$url?$request";
	$response = file_get_contents( $request );
	$decoded = json_decode( $response, TRUE );
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'URL:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.htmlspecialchars( $request ).kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Parameters:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $params ); echo( '</pre>'.kSTYLE_DATA_POS );
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
		$params->Format( kTYPE_JSON );
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
		$params[] = kAPI_OPERATION.'='.kAPI_OP_HELP;		// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;				// Format.
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
		$params->Format( kTYPE_JSON );
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
		$params[ kAPI_FORMAT ] = kTYPE_JSON;				// Format.
		$params[ kAPI_OPERATION ] = kAPI_OP_HELP;				// Command.
		//
		// Use static method.
		//
		$decoded = CWrapperClient::Request( $url, $params, 'POST', kTYPE_JSON );
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
	 *	LOGIN (CORRECT) MILKO															*
	 *==================================================================================*/
	echo( '<h4>Login ('.kAPI_OP_LOGIN.') MILKO</h4>' );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CWarehouseWrapperClient( $url );
		$params->Operation( kAPI_OP_LOGIN );
		$params->Format( kTYPE_JSON );
		$params->Database( 'TEST' );
		$params->Container( 'CWarehouseWrapper' );
		$params->Options( kAPI_OPT_SAFE, TRUE );
		$params->UserCode( 'MILKO' );
		$params->UserPass( 'MilkoPass' );
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
		$params[] = kAPI_OPERATION.'='.kAPI_OP_LOGIN;				// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_OPT_USER_CODE.'='.'MILKO';					// User code.
		$params[] = kAPI_OPT_USER_PASS.'='.'MilkoPass';				// User password.
		$params[] = kAPI_DATABASE.'='.'TEST';						// Database.
		$params[] = kAPI_CONTAINER.'='.'CWarehouseWrapper';			// Container.
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
	 *	LOGIN (CORRECT) LUCA															*
	 *==================================================================================*/
	echo( '<h4>Login ('.kAPI_OP_LOGIN.') LUCA</h4>' );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CWarehouseWrapperClient( $url );
		$params->Operation( kAPI_OP_LOGIN );
		$params->Format( kTYPE_JSON );
		$params->Database( 'TEST' );
		$params->Container( 'CWarehouseWrapper' );
		$params->Options( kAPI_OPT_SAFE, TRUE );
		$params->UserCode( 'LUCA' );
		$params->UserPass( 'LucaPass' );
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
		$params[] = kAPI_OPERATION.'='.kAPI_OP_LOGIN;				// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;				// Format.
		$params[] = kAPI_OPT_USER_CODE.'='.'LUCA';					// User code.
		$params[] = kAPI_OPT_USER_PASS.'='.'LucaPass';				// User password.
		$params[] = kAPI_DATABASE.'='.'TEST';						// Database.
		$params[] = kAPI_CONTAINER.'='.'CWarehouseWrapper';			// Container.
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
	 *	LOGIN (INCORRECT) LUCA															*
	 *==================================================================================*/
	echo( '<h4>Login ('.kAPI_OP_LOGIN.') LUCA (INCORRECT)</h4>' );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CWarehouseWrapperClient( $url );
		$params->Operation( kAPI_OP_LOGIN );
		$params->Format( kTYPE_JSON );
		$params->Database( 'TEST' );
		$params->Container( 'CWarehouseWrapper' );
		$params->UserCode( 'LUCA' );
		$params->UserPass( 'NOT VALID' );
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
		// Note the urlencode() on the password:
		// you MUST encode strings with spaces!
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_LOGIN;					// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;							// Format.
		$params[] = kAPI_OPT_USER_CODE.'='.'LUCA';						// User code.
		$params[] = kAPI_OPT_USER_PASS.'='.urlencode( 'NOT VALID') ;	// User password.
		$params[] = kAPI_DATABASE.'='.'TEST';							// Database.
		$params[] = kAPI_CONTAINER.'='.'CWarehouseWrapper';				// Container.
		$params[] = kAPI_OPT_LOG_TRACE.'='.'1';							// Trace exceptions.
		$params[] = kAPI_OPT_LOG_REQUEST.'='.'1';						// Log request.
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
	 *	LOGIN (NONE)																	*
	 *==================================================================================*/
	echo( '<h4>Login ('.kAPI_OP_LOGIN.') (NONE)</h4>' );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CWarehouseWrapperClient( $url );
		$params->Operation( kAPI_OP_LOGIN );
		$params->Format( kTYPE_JSON );
		$params->Database( 'TEST' );
		$params->Container( 'CWarehouseWrapper' );
		$params->Options( kAPI_OPT_SAFE, TRUE );
		$params->UserPass( 'NOT VALID' );
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
		$params[] = kAPI_OPERATION.'='.kAPI_OP_LOGIN;				// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;				// Format.
		$params[] = kAPI_OPT_USER_PASS.'='.'NOT VALID';				// User password.
		$params[] = kAPI_DATABASE.'='.'TEST';						// Database.
		$params[] = kAPI_CONTAINER.'='.'CWarehouseWrapper';			// Container.
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
		echo( kSTYLE_DATA_PRE.$response.kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
	}
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Decoded:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $decoded ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_TABLE_POS );
	echo( '<hr>' );
	echo( '<hr>' );
	
	/*===================================================================================
	 *	LIST USERS (EMPTY)																*
	 *==================================================================================*/
	echo( '<h4>List users ('.kAPI_OP_GET_USERS.') empty</h4>' );
	echo( '<i>Return first 5 records.</i>' );
	//
	// Build fields list.
	//
	$fields = array( kTAG_NAME => TRUE );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CWarehouseWrapperClient( $url );
		$params->Operation( kAPI_OP_GET_USERS );
		$params->Format( kTYPE_JSON );
		$params->Database( 'TEST' );
		$params->Container( 'CWarehouseWrapper' );
		$params->Fields( $fields );
		$params->Start( 0 );
		$params->Limit( 5 );
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
		// Build fields list.
		//
		$fields = json_encode( $fields );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_USERS;			// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_DATABASE.'='.'TEST';					// Database.
		$params[] = kAPI_CONTAINER.'='.'CWarehouseWrapper';			// Container.
		$params[] = kAPI_DATA_FIELD.'='.urlencode( $fields );		// Fields.
		$params[] = kAPI_PAGE_START.'='.'0';						// Page start.
		$params[] = kAPI_PAGE_LIMIT.'='.'5';						// Page limits.
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
	 *	LIST USERS (LIST)																*
	 *==================================================================================*/
	echo( '<h4>List users ('.kAPI_OP_GET_USERS.') list</h4>' );
	//
	// Init identifiers.
	//
	$list = array( 'MILKO', 'LUCA', 'PIPPO' );
	//
	// Init fields list.
	//
	$fields = array( kTAG_NAME => TRUE );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CWarehouseWrapperClient( $url );
		$params->Operation( kAPI_OP_GET_USERS );
		$params->Format( kTYPE_JSON );
		$params->Database( 'TEST' );
		$params->Container( 'CWarehouseWrapper' );
		$params->Fields( $fields );
		foreach( $list as $element )
			$params->Identifiers( $element, TRUE );
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
		// Build identifiers list.
		//
		$list_enc = json_encode( $list );
		//
		// Build fields list.
		//
		$fields = json_encode( $fields );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_USERS;			// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_DATABASE.'='.'TEST';						// Database.
		$params[] = kAPI_CONTAINER.'='.'CWarehouseWrapper';			// Container.
		$params[] = kAPI_DATA_FIELD.'='.urlencode( $fields );		// Fields.
		$params[] = kAPI_OPT_IDENTIFIERS.'='.$list_enc;				// Identifiers.
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
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Identifiers:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $list ); echo( '</pre>'.kSTYLE_DATA_POS );
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
	 *	GET USERS LIST (PAGING)															*
	 *==================================================================================*/
	echo( '<h4>Get users list ('.kAPI_OP_GET_USERS.') paging</h4>' );
	//
	// Init identifiers.
	//
	$list = array( 'MILKO', CUser::HashIndex( 'LUCA' ), CUser::HashIndex( 'MARCO' ), 'PIPPO' );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CWarehouseWrapperClient( $url );
		$params->Operation( kAPI_OP_GET_USERS );
		$params->Format( kTYPE_JSON );
		$params->Database( 'TEST' );
		$params->Container( 'CWarehouseWrapper' );
		foreach( $list as $element )
			$params->Identifiers( $element, TRUE );
		$params->Start( 0 );
		$params->Limit( 2 );
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
		// Build identifiers list.
		//
		$list_enc = json_encode( $list );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_USERS;			// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_DATABASE.'='.'TEST';						// Database.
		$params[] = kAPI_CONTAINER.'='.'CWarehouseWrapper';			// Container.
		$params[] = kAPI_OPT_IDENTIFIERS.'='.$list_enc;				// Identifiers.
		$params[] = kAPI_PAGE_START.'='.'0';						// Page start.
		$params[] = kAPI_PAGE_LIMIT.'='.'2';						// Page limits.
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
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Identifiers:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $list ); echo( '</pre>'.kSTYLE_DATA_POS );
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
	 *	GET USERS (QUERY NAME)															*
	 *==================================================================================*/
	echo( '<h4>Query users list ('.kAPI_OP_GET_USERS.') search by name</h4>' );
	//
	// Set query.
	//
	$query = array
	(
		kOPERATOR_OR => array
		(
			array
			(
				kAPI_QUERY_SUBJECT => kTAG_NAME,
				kAPI_QUERY_OPERATOR => kOPERATOR_CONTAINS_NOCASE,
				kAPI_QUERY_TYPE => kTYPE_STRING,
				kAPI_QUERY_DATA => 'milko'
			),
			array
			(
				kAPI_QUERY_SUBJECT => kTAG_NAME,
				kAPI_QUERY_OPERATOR => kOPERATOR_CONTAINS_NOCASE,
				kAPI_QUERY_TYPE => kTYPE_STRING,
				kAPI_QUERY_DATA => 'luca'
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
		$params = new CWarehouseWrapperClient( $url );
		$params->Operation( kAPI_OP_GET_USERS );
		$params->Format( kTYPE_JSON );
		$params->Database( 'TEST' );
		$params->Container( 'CWarehouseWrapper' );
		$params->Query( $query );
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
		// Encode query.
		//
		$query_enc = json_encode( $query );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_USERS;			// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_DATABASE.'='.'TEST';					// Database.
		$params[] = kAPI_CONTAINER.'='.'CWarehouseWrapper';			// Container.
		$params[] = kAPI_DATA_QUERY.'='.urlencode( $query_enc );	// Query.
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
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Query:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $query ); echo( '</pre>'.kSTYLE_DATA_POS );
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
	 *	LIST MANAGED USERS (EMPTY)														*
	 *==================================================================================*/
	echo( '<h4>List managed users ('.kAPI_OP_GET_MANAGED_USERS.') empty</h4>' );
	//
	// Init fields list.
	//
	$fields = array( kTAG_NAME => TRUE, kTAG_MANAGER => TRUE );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CWarehouseWrapperClient( $url );
		$params->Operation( kAPI_OP_GET_MANAGED_USERS );
		$params->Format( kTYPE_JSON );
		$params->Database( 'TEST' );
		$params->Container( 'CWarehouseWrapper' );
		$params->Fields( $fields );
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
		// Build fields list.
		//
		$fields = json_encode( $fields );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_MANAGED_USERS;	// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_DATABASE.'='.'TEST';						// Database.
		$params[] = kAPI_CONTAINER.'='.'CWarehouseWrapper';			// Container.
		$params[] = kAPI_DATA_FIELD.'='.urlencode( $fields );		// Fields.
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
	 *	LIST MANAGED USERS (LIST)														*
	 *==================================================================================*/
	echo( '<h4>List managed users ('.kAPI_OP_GET_MANAGED_USERS.') list</h4>' );
	//
	// Init identifiers.
	//
	$list = array( CUser::HashIndex( 'LUCA' ), 'MARCO' );
	//
	// Init fields list.
	//
	$fields = array( kTAG_NAME => TRUE, kTAG_MANAGER => TRUE );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CWarehouseWrapperClient( $url );
		$params->Operation( kAPI_OP_GET_MANAGED_USERS );
		$params->Format( kTYPE_JSON );
		$params->Database( 'TEST' );
		$params->Container( 'CWarehouseWrapper' );
		$params->Fields( $fields );
		foreach( $list as $element )
			$params->Identifiers( $element, TRUE );
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
		// Build identifiers list.
		//
		$list_enc = json_encode( $list );
		//
		// Build fields list.
		//
		$fields = json_encode( $fields );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_MANAGED_USERS;	// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_DATABASE.'='.'TEST';						// Database.
		$params[] = kAPI_CONTAINER.'='.'CWarehouseWrapper';			// Container.
		$params[] = kAPI_DATA_FIELD.'='.urlencode( $fields );		// Fields.
		$params[] = kAPI_OPT_IDENTIFIERS.'='.$list_enc;				// Identifiers.
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
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Identifiers:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $list ); echo( '</pre>'.kSTYLE_DATA_POS );
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
	 *	LIST TERMS (EMPTY)																*
	 *==================================================================================*/
	echo( '<h4>List terms ('.kAPI_OP_GET_TERMS.') empty</h4>' );
	echo( '<i>Return first 5 records.</i>' );
	//
	// Build fields list.
	//
	$fields = array( kTAG_NAME => TRUE );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CWarehouseWrapperClient( $url );
		$params->Operation( kAPI_OP_GET_TERMS );
		$params->Format( kTYPE_JSON );
		$params->Database( 'WAREHOUSE' );
		$params->Fields( $fields );
		$params->Start( 0 );
		$params->Limit( 5 );
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
		// Build fields list.
		//
		$fields = json_encode( $fields );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_TERMS;			// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';					// Database.
		$params[] = kAPI_DATA_FIELD.'='.urlencode( $fields );		// Fields.
		$params[] = kAPI_PAGE_START.'='.'0';						// Page start.
		$params[] = kAPI_PAGE_LIMIT.'='.'5';						// Page limits.
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
	 *	LIST TERMS (LIST)																*
	 *==================================================================================*/
	echo( '<h4>List terms ('.kAPI_OP_GET_TERMS.') list</h4>' );
	//
	// Init identifiers.
	//
	$list = array( 'ISO:3166:1:IT', COntologyTerm::HashIndex( 'MCPD:SAMPSTAT:100' ), ':TERM', 'XXX' );
	//
	// Init fields list.
	//
	$fields = array( kTAG_NAME => TRUE );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CWarehouseWrapperClient( $url );
		$params->Operation( kAPI_OP_GET_TERMS );
		$params->Format( kTYPE_JSON );
		$params->Database( 'WAREHOUSE' );
		$params->Fields( $fields );
		foreach( $list as $element )
			$params->Identifiers( $element, TRUE );
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
		// Build identifiers list.
		//
		$list_enc = json_encode( $list );
		//
		// Build fields list.
		//
		$fields = json_encode( $fields );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_TERMS;			// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';					// Database.
		$params[] = kAPI_DATA_FIELD.'='.urlencode( $fields );		// Fields.
		$params[] = kAPI_OPT_IDENTIFIERS.'='.$list_enc;				// Identifiers.
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
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Identifiers:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $list ); echo( '</pre>'.kSTYLE_DATA_POS );
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
	 *	GET TERMS LIST (QUERY)															*
	 *==================================================================================*/
	echo( '<h4>Get terms list ('.kAPI_OP_GET_TERMS.') query</h4>' );
	echo( '<i>Get all terms with code 200 that have nodes, return only name and node fields.</i>' );
	//
	// Build query.
	//
	$query = array
	(
		kOPERATOR_AND => array
		(
			array
			(
				kAPI_QUERY_SUBJECT => kTAG_CODE,
				kAPI_QUERY_OPERATOR => kOPERATOR_EQUAL,
				kAPI_QUERY_TYPE => kTYPE_STRING,
				kAPI_QUERY_DATA => '200'
			),
			array
			(
				kAPI_QUERY_SUBJECT => kTAG_NODE,
				kAPI_QUERY_OPERATOR => kOPERATOR_NOT_NULL
			)
		)
	);
	//
	// Build fields list.
	//
	$fields = array( kTAG_NAME => TRUE, kTAG_NODE => TRUE );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CWarehouseWrapperClient( $url );
		$params->Operation( kAPI_OP_GET_TERMS );
		$params->Format( kTYPE_JSON );
		$params->Database( 'WAREHOUSE' );
		$params->Query( $query );
		$params->Fields( $fields );
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
		// Build identifiers list.
		//
		$query_enc = json_encode( $query );
		//
		// Build fields list.
		//
		$fields = json_encode( $fields );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_TERMS;			// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';					// Database.
		$params[] = kAPI_DATA_FIELD.'='.urlencode( $fields );		// Fields.
		$params[] = kAPI_DATA_QUERY.'='.urlencode( $query_enc );	// Query.
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
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Query:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $query ); echo( '</pre>'.kSTYLE_DATA_POS );
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
	echo( '<hr>' );
	
	/*===================================================================================
	 *	MATCH TERMS																		*
	 *==================================================================================*/
	echo( '<h4>Match terms ('.kAPI_OP_MATCH_TERMS.') query list</h4>' );
	echo( '<i>Match all terms and their nodes who contain "Italian" in their GID, code '
		 .'and names and that have related nodes; return the first three terms</i>' );
	//
	// Build query.
	//
	$query = array
	(
		//
		// Query 1.
		//
		array
		(
			kOPERATOR_AND => array
			(
				array
				(
					kAPI_QUERY_SUBJECT => kTAG_GID,
					kAPI_QUERY_OPERATOR => kOPERATOR_EQUAL,
					kAPI_QUERY_TYPE => kTYPE_STRING,
					kAPI_QUERY_DATA => 'Italian'
				),
				array
				(
					kAPI_QUERY_SUBJECT => kTAG_NODE,
					kAPI_QUERY_OPERATOR => kOPERATOR_NOT_NULL
				)
			)
		),
		//
		// Query 2.
		//
		array
		(
			kOPERATOR_AND => array
			(
				array
				(
					kAPI_QUERY_SUBJECT => kTAG_CODE,
					kAPI_QUERY_OPERATOR => kOPERATOR_EQUAL,
					kAPI_QUERY_TYPE => kTYPE_STRING,
					kAPI_QUERY_DATA => 'Italian'
				),
				array
				(
					kAPI_QUERY_SUBJECT => kTAG_NODE,
					kAPI_QUERY_OPERATOR => kOPERATOR_NOT_NULL
				)
			)
		),
		//
		// Query 3.
		//
		array
		(
			kOPERATOR_AND => array
			(
				array
				(
					kAPI_QUERY_SUBJECT => kTAG_NAME.'.'.kTAG_DATA,
					kAPI_QUERY_OPERATOR => kOPERATOR_CONTAINS_NOCASE,
					kAPI_QUERY_TYPE => kTYPE_STRING,
					kAPI_QUERY_DATA => 'Italian'
				),
				array
				(
					kAPI_QUERY_SUBJECT => kTAG_NODE,
					kAPI_QUERY_OPERATOR => kOPERATOR_NOT_NULL
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
		$params = new CWarehouseWrapperClient( $url );
		$params->Operation( kAPI_OP_MATCH_TERMS );
		$params->Format( kTYPE_JSON );
		$params->Database( 'WAREHOUSE' );
		$params->Query( $query );
		$params->Start( 0 );
		$params->Limit( 3 );
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
		// Build identifiers list.
		//
		$query_enc = json_encode( $query );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_MATCH_TERMS;			// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';					// Database.
		$params[] = kAPI_DATA_QUERY.'='.urlencode( $query_enc );	// Query.
		$params[] = kAPI_PAGE_START.'='.'0';						// Page start.
		$params[] = kAPI_PAGE_LIMIT.'='.'3';						// Page limits.
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
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Query:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $query ); echo( '</pre>'.kSTYLE_DATA_POS );
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
	echo( '<hr>' );
	
	/*===================================================================================
	 *	SET-TAGS																		*
	 *==================================================================================*/
	echo( '<h4>Set tags ('.kAPI_OP_SET_TAGS.')</h4>' );
	echo( '<i>Set or get the <code>ISO:3166:1:ITA/:ENUM-OF/ISO:3166:1:ALPHA-3/:IS-A/ISO:3166:1</code> and <code>MCPD:ORIGCTY</code> sequences</i>' );
	//
	// Build items list.
	//
	$list = array
	(
		array( 15604, 14030 ),	// Edge identifiers.
		'MCPD:ORIGCTY'			// Term global identifier.
	);
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CWarehouseWrapperClient( $url );
		$params->Operation( kAPI_OP_SET_TAGS );
		$params->Format( kTYPE_JSON );
		$params->Database( 'WAREHOUSE' );
		foreach( $list as $element )
			$params->Identifiers( $element, TRUE );
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
		// Build identifiers list.
		//
		$list_enc = json_encode( $list );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_SET_TAGS;				// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;							// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';						// Database.
		$params[] = kAPI_OPT_IDENTIFIERS.'='.urlencode( $list_enc );	// Identifiers.
		$params[] = kAPI_OPT_LOG_TRACE.'='.'1';							// Trace exceptions.
		$params[] = kAPI_OPT_LOG_REQUEST.'='.'1';						// Log request.
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
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Identifiers:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $list ); echo( '</pre>'.kSTYLE_DATA_POS );
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
	echo( '<hr>' );
	
	/*===================================================================================
	 *	SET-TAGS																		*
	 *==================================================================================*/
	echo( '<h4>Set tags ('.kAPI_OP_SET_TAGS.') with terms</h4>' );
	echo( '<i>Set or get the <code>ISO:3166:1:ITA/:ENUM-OF/ISO:3166:1:ALPHA-3/:IS-A/ISO:3166:1</code> and <code>MCPD:ORIGCTY</code> sequences</i>' );
	//
	// Build items list.
	//
	$list = array
	(
		array( 'ISO:3166:1:ITA', ':ENUM-OF', 'ISO:3166:1:ALPHA-3', ':IS-A', 'ISO:3166:1' ),
		'',
		'MCPD:ORIGCTY'
	);
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CWarehouseWrapperClient( $url );
		$params->Operation( kAPI_OP_SET_TAGS );
		$params->Format( kTYPE_JSON );
		$params->Database( 'WAREHOUSE' );
		foreach( $list as $element )
			$params->Identifiers( $element, TRUE );
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
		// Build identifiers list.
		//
		$list_enc = json_encode( $list );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_SET_TAGS;				// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;							// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';						// Database.
		$params[] = kAPI_OPT_IDENTIFIERS.'='.urlencode( $list_enc );	// Identifiers.
		$params[] = kAPI_OPT_LOG_TRACE.'='.'1';							// Trace exceptions.
		$params[] = kAPI_OPT_LOG_REQUEST.'='.'1';						// Log request.
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
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Identifiers:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $list ); echo( '</pre>'.kSTYLE_DATA_POS );
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
	echo( '<hr>' );
	
	/*===================================================================================
	 *	GET-TAGS																		*
	 *==================================================================================*/
	echo( '<h4>Get tags ('.kAPI_OP_GET_TAGS.') empty list</h4>' );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CWarehouseWrapperClient( $url );
		$params->Operation( kAPI_OP_GET_TAGS );
		$params->Format( kTYPE_JSON );
		$params->Database( 'WAREHOUSE' );
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
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_TAGS;				// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;							// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';						// Database.
		$params[] = kAPI_OPT_LOG_TRACE.'='.'1';							// Trace exceptions.
		$params[] = kAPI_OPT_LOG_REQUEST.'='.'1';						// Log request.
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
	echo( '<hr>' );
	
	/*===================================================================================
	 *	GET-TAGS																		*
	 *==================================================================================*/
	echo( '<h4>Get tags ('.kAPI_OP_GET_TAGS.') list</h4>' );
	//
	// Build items list.
	//
	$list = array( '@:1', 2 );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CWarehouseWrapperClient( $url );
		$params->Operation( kAPI_OP_GET_TAGS );
		$params->Format( kTYPE_JSON );
		$params->Database( 'WAREHOUSE' );
		foreach( $list as $element )
			$params->Identifiers( $element, TRUE );
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
		// Build identifiers list.
		//
		$list_enc = json_encode( $list );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_TAGS;				// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;							// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';						// Database.
		$params[] = kAPI_OPT_IDENTIFIERS.'='.urlencode( $list_enc );	// Identifiers.
		$params[] = kAPI_OPT_LOG_TRACE.'='.'1';							// Trace exceptions.
		$params[] = kAPI_OPT_LOG_REQUEST.'='.'1';						// Log request.
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
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Identifiers:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $list ); echo( '</pre>'.kSTYLE_DATA_POS );
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
	echo( '<hr>' );
	
	/*===================================================================================
	 *	GET NODES (EMPTY)																*
	 *==================================================================================*/
	echo( '<h4>Get nodes list ('.kAPI_OP_GET_NODES.') empty</h4>' );
	echo( '<i>Return first 5 records.</i><br>' );
	echo( '<i>Note that fields selection works only for terms.</i><br>' );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CWarehouseWrapperClient( $url );
		$params->Operation( kAPI_OP_GET_NODES );
		$params->Format( kTYPE_JSON );
		$params->Database( 'WAREHOUSE' );
		$params->Fields( array( kTAG_NAME => TRUE ) );
		$params->Start( 0 );
		$params->Limit( 5 );
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
		// Build fields list.
		//
		$fields = json_encode( array( kTAG_NAME => TRUE ) );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_NODES;			// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';					// Database.
		$params[] = kAPI_DATA_FIELD.'='.urlencode( $fields );		// Fields.
		$params[] = kAPI_PAGE_START.'='.'0';						// Page start.
		$params[] = kAPI_PAGE_LIMIT.'='.'5';						// Page limits.
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
	 *	GET NODES LIST (LIST)															*
	 *==================================================================================*/
	echo( '<h4>Get nodes list ('.kAPI_OP_GET_NODES.') list</h4>' );
	//
	// Init identifiers.
	//
	$list = array( 1, 2, 3, 99999 );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CWarehouseWrapperClient( $url );
		$params->Operation( kAPI_OP_GET_NODES );
		$params->Format( kTYPE_JSON );
		$params->Database( 'WAREHOUSE' );
		$params->Fields( array( kTAG_NAME => TRUE ) );
		$params->Container( kDEFAULT_CNT_NODES );
		foreach( $list as $element )
			$params->Identifiers( $element, TRUE );
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
		// Build identifiers list.
		//
		$list_enc = json_encode( $list );
		//
		// Build fields list.
		//
		$fields = json_encode( array( kTAG_NAME => TRUE ) );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_NODES;			// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';					// Database.
		$params[] = kAPI_DATA_FIELD.'='.urlencode( $fields );		// Fields.
		$params[] = kAPI_CONTAINER.'='.kDEFAULT_CNT_NODES;			// Container.
		$params[] = kAPI_OPT_IDENTIFIERS.'='.$list_enc;				// Identifiers.
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
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Identifiers:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $list ); echo( '</pre>'.kSTYLE_DATA_POS );
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
	 *	GET NODES (QUERY)																*
	 *==================================================================================*/
	echo( '<h4>Get nodes list ('.kAPI_OP_GET_NODES.') query</h4>' );
	echo( '<i>Get all nodes of kind root.</i><br>' );
	//
	// Build query.
	//
	$query = array
	(
		kOPERATOR_AND => array
		(
			array
			(
				kAPI_QUERY_SUBJECT => kTAG_DATA.'.'.kTAG_KIND,
				kAPI_QUERY_OPERATOR => kOPERATOR_EQUAL,
				kAPI_QUERY_TYPE => kTYPE_STRING,
				kAPI_QUERY_DATA => kTYPE_ROOT
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
		$params = new CWarehouseWrapperClient( $url );
		$params->Operation( kAPI_OP_GET_NODES );
		$params->Format( kTYPE_JSON );
		$params->Database( 'WAREHOUSE' );
		$params->Fields( array( kTAG_NAME => TRUE ) );
		$params->Query( $query );
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
		// Build identifiers list.
		//
		$query_enc = json_encode( $query );
		//
		// Build fields list.
		//
		$fields = json_encode( array( kTAG_NAME => TRUE ) );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_NODES;			// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';					// Database.
		$params[] = kAPI_DATA_FIELD.'='.urlencode( $fields );		// Fields.
		$params[] = kAPI_DATA_QUERY.'='.urlencode( $query_enc );	// Query.
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
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Query:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $query ); echo( '</pre>'.kSTYLE_DATA_POS );
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
	 *	GET EDGES (EMPTY)																*
	 *==================================================================================*/
	echo( '<h4>Get edges list ('.kAPI_OP_GET_EDGES.') empty</h4>' );
	echo( '<i>Return first 5 records and only the name field for terms.</i>' );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CWarehouseWrapperClient( $url );
		$params->Operation( kAPI_OP_GET_EDGES );
		$params->Format( kTYPE_JSON );
		$params->Database( 'WAREHOUSE' );
		$params->Fields( array( kTAG_NAME => TRUE ) );
		$params->Start( 0 );
		$params->Limit( 5 );
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
		// Build fields list.
		//
		$fields = json_encode( array( kTAG_NAME => TRUE ) );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_EDGES;			// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';					// Database.
		$params[] = kAPI_DATA_FIELD.'='.urlencode( $fields );		// Fields.
		$params[] = kAPI_PAGE_START.'='.'0';						// Page start.
		$params[] = kAPI_PAGE_LIMIT.'='.'5';						// Page limits.
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
	 *	LIST EDGES (LIST)																*
	 *==================================================================================*/
	echo( '<h4>List edges ('.kAPI_OP_GET_EDGES.') list</h4>' );
	//
	// Init identifiers.
	//
	$list = array( 0, 1, 2, 9999999 );
	//
	// Init fields.
	//
	$fields = array( kTAG_NAME => TRUE, kTAG_TERM => TRUE );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CWarehouseWrapperClient( $url );
		$params->Operation( kAPI_OP_GET_EDGES );
		$params->Format( kTYPE_JSON );
		$params->Database( 'WAREHOUSE' );
		foreach( $list as $element )
			$params->Identifiers( $element, TRUE );
		$params->Fields( $fields );
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
		// Build identifiers list.
		//
		$list_enc = json_encode( $list );
		//
		// Build fields list.
		//
		$fields_enc = json_encode( $fields );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_EDGES;			// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';					// Database.
		$params[] = kAPI_OPT_IDENTIFIERS.'='.$list_enc;				// Identifiers.
		$params[] = kAPI_DATA_FIELD.'='.$fields_enc;				// Fields.
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
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Identifiers:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $list ); echo( '</pre>'.kSTYLE_DATA_POS );
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
	 *	LIST EDGES (QUERY)																*
	 *==================================================================================*/
	echo( '<h4>Get edges list ('.kAPI_OP_GET_EDGES.') query</h4>' );
	echo( '<i>Return all exact cross references pointing to <code>ISO:3166:1:ITA</code> term nodes.</i>' );
	//
	// Build query.
	//
	$query = array
	(
		kOPERATOR_AND => array
		(
			array
			(
				kAPI_QUERY_SUBJECT => kTAG_OBJECT.'.'.kTAG_TERM,
				kAPI_QUERY_OPERATOR => kOPERATOR_EQUAL,
				kAPI_QUERY_TYPE => kTYPE_STRING,
				kAPI_QUERY_DATA => 'ISO:3166:1:ITA'
			),
			array
			(
				kAPI_QUERY_SUBJECT => kTAG_PREDICATE.'.'.kTAG_TERM,
				kAPI_QUERY_OPERATOR => kOPERATOR_EQUAL,
				kAPI_QUERY_TYPE => kTYPE_STRING,
				kAPI_QUERY_DATA => kTAG_REFERENCE_XREF
			),
			array
			(
				kAPI_QUERY_SUBJECT => kTAG_DATA.'.'.kTAG_KIND,
				kAPI_QUERY_OPERATOR => kOPERATOR_EQUAL,
				kAPI_QUERY_TYPE => kTYPE_STRING,
				kAPI_QUERY_DATA => kTYPE_EXACT
			)
		)
	);
	//
	// Init fields.
	//
	$fields = array( kTAG_NAME => TRUE, kTAG_TERM => TRUE );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CWarehouseWrapperClient( $url );
		$params->Operation( kAPI_OP_GET_EDGES );
		$params->Format( kTYPE_JSON );
		$params->Database( 'WAREHOUSE' );
		$params->Query( $query );
		$params->Fields( $fields );
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
		// Encode query.
		//
		$query_enc = json_encode( $query );
		//
		// Build fields list.
		//
		$fields_enc = json_encode( $fields );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_EDGES;			// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';					// Database.
		$params[] = kAPI_DATA_QUERY.'='.urlencode( $query_enc );	// Query.
		$params[] = kAPI_DATA_FIELD.'='.$fields_enc;				// Fields.
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
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Query:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $query ); echo( '</pre>'.kSTYLE_DATA_POS );
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
	 *	LIST EDGES (PREDICATES)															*
	 *==================================================================================*/
	echo( '<h4>Get edges list ('.kAPI_OP_GET_EDGES.') include predicates</h4>' );
	echo( '<i>Return all cross references pointing to <code>ISO:3166:1:ITA</code> term nodes.</i>' );
	//
	// Build query.
	//
	$query = array
	(
		kOPERATOR_AND => array
		(
			array
			(
				kAPI_QUERY_SUBJECT => kTAG_OBJECT.'.'.kTAG_TERM,
				kAPI_QUERY_OPERATOR => kOPERATOR_EQUAL,
				kAPI_QUERY_TYPE => kTYPE_STRING,
				kAPI_QUERY_DATA => 'ISO:3166:1:ITA'
			)
		)
	);
	//
	// Predicates list.
	//
	$predicates = array( ':XREF' );
	//
	// Init fields.
	//
	$fields = array( kTAG_NAME => TRUE, kTAG_TERM => TRUE );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CWarehouseWrapperClient( $url );
		$params->Operation( kAPI_OP_GET_EDGES );
		$params->Format( kTYPE_JSON );
		$params->Database( 'WAREHOUSE' );
		$params->Query( $query );
		$params->Fields( $fields );
		foreach( $predicates as $predicate )
			$params->Predicates( $predicate, TRUE );
		$params->PredicatesSelector( 1 );
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
		// Encode query.
		//
		$query_enc = urlencode( json_encode( $query ) );
		//
		// Encode predicates.
		//
		$predicates_enc = urlencode( json_encode( $predicates ) );
		//
		// Build fields list.
		//
		$fields_enc = urlencode( json_encode( $fields ) );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_EDGES;			// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';					// Database.
		$params[] = kAPI_DATA_QUERY.'='.$query_enc;					// Query.
		$params[] = kAPI_DATA_FIELD.'='.$fields_enc;				// Fields.
		$params[] = kAPI_OPT_PREDICATES.'='.$predicates_enc;		// Predicates.
		$params[] = kAPI_OPT_PREDICATES_INC.'='.'1';				// Predicates selector.
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
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Query:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $query ); echo( '</pre>'.kSTYLE_DATA_POS );
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
	 *	LIST EDGES (PREDICATES)															*
	 *==================================================================================*/
	echo( '<h4>Get edges list ('.kAPI_OP_GET_EDGES.') exclude predicates</h4>' );
	echo( '<i>Return all predicates pointing to <code>ISO:3166:1:ITA</code> term nodes, except for cross-references.</i>' );
	//
	// Build query.
	//
	$query = array
	(
		kOPERATOR_AND => array
		(
			array
			(
				kAPI_QUERY_SUBJECT => kTAG_OBJECT.'.'.kTAG_TERM,
				kAPI_QUERY_OPERATOR => kOPERATOR_EQUAL,
				kAPI_QUERY_TYPE => kTYPE_STRING,
				kAPI_QUERY_DATA => 'ISO:3166:1:ITA'
			)
		)
	);
	//
	// Predicates list.
	//
	$predicates = array( ':XREF' );
	//
	// Init fields.
	//
	$fields = array( kTAG_NAME => TRUE, kTAG_TERM => TRUE );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CWarehouseWrapperClient( $url );
		$params->Operation( kAPI_OP_GET_EDGES );
		$params->Format( kTYPE_JSON );
		$params->Database( 'WAREHOUSE' );
		$params->Query( $query );
		$params->Fields( $fields );
		foreach( $predicates as $predicate )
			$params->Predicates( $predicate, TRUE );
		$params->PredicatesSelector( 0 );
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
		// Encode query.
		//
		$query_enc = urlencode( json_encode( $query ) );
		//
		// Encode predicates.
		//
		$predicates_enc = urlencode( json_encode( $predicates ) );
		//
		// Build fields list.
		//
		$fields_enc = urlencode( json_encode( $fields ) );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_EDGES;			// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';					// Database.
		$params[] = kAPI_DATA_QUERY.'='.$query_enc;					// Query.
		$params[] = kAPI_DATA_FIELD.'='.$fields_enc;				// Fields.
		$params[] = kAPI_OPT_PREDICATES.'='.$predicates_enc;		// Predicates.
		$params[] = kAPI_OPT_PREDICATES_INC.'='.'0';				// Predicates selector.
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
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Query:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $query ); echo( '</pre>'.kSTYLE_DATA_POS );
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
	 *	GET RELATED (EMPTY)																*
	 *==================================================================================*/
	echo( '<h4>Get related ('.kAPI_OP_GET_RELS.') empty</h4>' );
	echo( '<i>Should return an empty response: no node identifiers provided.</i>' );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CWarehouseWrapperClient( $url );
		$params->Operation( kAPI_OP_GET_RELS );
		$params->Format( kTYPE_JSON );
		$params->Database( 'WAREHOUSE' );
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
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_RELS;			// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';					// Database.
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
	 *	GET EDGES LIST (NO DIRECTION)													*
	 *==================================================================================*/
	echo( '<h4>Get edges list ('.kAPI_OP_GET_RELS.') list - no direction</h4>' );
	echo( '<i>Without direction the identifiers represent edge node IDs; show just name.<br></i>' );
	//
	// Init list.
	//
	$list = array( 0, 10, 51, 1000, 99999 );
	//
	// Init fields.
	//
	$fields = array( kTAG_NAME => TRUE );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CWarehouseWrapperClient( $url );
		$params->Operation( kAPI_OP_GET_RELS );
		$params->Format( kTYPE_JSON );
		$params->Database( 'WAREHOUSE' );
		foreach( $list as $element )
			$params->Identifiers( $element, TRUE );
		$params->Fields( $fields );
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
		// Build identifiers list.
		//
		$list_enc = json_encode( $list );
		//
		// Build fields list.
		//
		$fields_enc = json_encode( $fields );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_RELS;			// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';					// Database.
		$params[] = kAPI_CONTAINER.'='.kDEFAULT_CNT_TERMS;			// Container.
		$params[] = kAPI_OPT_IDENTIFIERS.'='.$list_enc;				// Identifiers.
		$params[] = kAPI_DATA_FIELD.'='.urlencode( $fields_enc );	// Fields.
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
	 *	GET EDGES LIST (LIST WITH PREDICATES - NO DIRECTION)							*
	 *==================================================================================*/
	echo( '<h4>Get edges list ('.kAPI_OP_GET_RELS.') list with predicate and no direction</h4>' );
	echo( '<i>Limit selected edges to the provided list of predicates (part-of and cross-reference).<br></i>' );
	//
	// Init list.
	//
	$list = array( 0, 10, 51, 1000, 99999 );
	//
	// Build predicates list.
	//
	$predicates = array( kPRED_PART_OF, kTAG_REFERENCE_XREF );
	//
	// Init fields.
	//
	$fields = array( kTAG_NAME => TRUE );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CWarehouseWrapperClient( $url );
		$params->Operation( kAPI_OP_GET_RELS );
		$params->Format( kTYPE_JSON );
		$params->Database( 'WAREHOUSE' );
		foreach( $list as $element )
			$params->Identifiers( $element, TRUE );
		foreach( $predicates as $element )
			$params->Predicates( $element, TRUE );
		$params->Fields( $fields );
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
		// Build identifiers list.
		//
		$list_enc = json_encode( $list );
		//
		// Build predicates list.
		//
		$predicates_enc = json_encode( $predicates );
		//
		// Build fields list.
		//
		$fields_enc = json_encode( $fields );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_RELS;					// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;								// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';							// Database.
		$params[] = kAPI_CONTAINER.'='.kDEFAULT_CNT_TERMS;					// Container.
		$params[] = kAPI_OPT_IDENTIFIERS.'='.$list_enc;						// Identifiers.
		$params[] = kAPI_DATA_FIELD.'='.urlencode( $fields_enc );			// Fields.
		$params[] = kAPI_OPT_PREDICATES.'='.urlencode( $predicates_enc );	// Predicates.
		$params[] = kAPI_OPT_LOG_TRACE.'='.'1';								// Trace exceptions.
		$params[] = kAPI_OPT_LOG_REQUEST.'='.'1';							// Log request.
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
	 *	GET DIRECTED EDGES (IN)															*
	 *==================================================================================*/
	echo( '<h4>Get directed edges ('.kAPI_OP_GET_RELS.') IN</h4>' );
	echo( '<i>Get all adges pointing to node 2, show only name.</i>' );
	//
	// Build identifiers list.
	//
	$list = array( 2 );
	//
	// Build fields list.
	//
	$fields = array( kTAG_NAME => TRUE );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CWarehouseWrapperClient( $url );
		$params->Operation( kAPI_OP_GET_RELS );
		$params->Format( kTYPE_JSON );
		$params->Database( 'WAREHOUSE' );
		$params->Container( kDEFAULT_CNT_TERMS );
		foreach( $list as $element )
			$params->Identifiers( $element, TRUE );
		$params->Fields( $fields );
		$params->Direction( kAPI_DIRECTION_IN );
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
		// Build identifiers list.
		//
		$list_enc = json_encode( $list );
		//
		// Build fields list.
		//
		$fields_enc = json_encode( $fields );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_RELS;				// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;							// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';						// Database.
		$params[] = kAPI_CONTAINER.'='.kDEFAULT_CNT_TERMS;				// Container.
		$params[] = kAPI_OPT_IDENTIFIERS.'='.urlencode( $list_enc );	// Identifiers.
		$params[] = kAPI_DATA_FIELD.'='.urlencode( $fields_enc );		// Fields.
		$params[] = kAPI_OPT_DIRECTION.'='.kAPI_DIRECTION_IN;			// Direction.
		$params[] = kAPI_OPT_LOG_TRACE.'='.'1';							// Trace exceptions.
		$params[] = kAPI_OPT_LOG_REQUEST.'='.'1';						// Log request.
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
	 *	GET DIRECTED EDGES (IN) 2 LEVELS												*
	 *==================================================================================*/
	echo( '<h4>Get directed edges ('.kAPI_OP_GET_RELS.') IN 2 LEVELS</h4>' );
	echo( '<i>Get all nodes that point to node 1 traversing 2 levels.</i>' );
	//
	// Build identifiers list.
	//
	$list = array( 1 );
	//
	// Build fields list.
	//
	$fields = array( kTAG_NAME => TRUE );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CWarehouseWrapperClient( $url );
		$params->Operation( kAPI_OP_GET_RELS );
		$params->Format( kTYPE_JSON );
		$params->Database( 'WAREHOUSE' );
		$params->Container( kDEFAULT_CNT_TERMS );
		foreach( $list as $element )
			$params->Identifiers( $element, TRUE );
		$params->Fields( $fields );
		$params->Direction( kAPI_DIRECTION_IN );
		$params->Levels( 2 );
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
		// Build identifiers list.
		//
		$list_enc = json_encode( $list );
		//
		// Build fields list.
		//
		$fields_enc = json_encode( $fields );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_RELS;				// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;							// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';						// Database.
		$params[] = kAPI_CONTAINER.'='.kDEFAULT_CNT_TERMS;				// Container.
		$params[] = kAPI_OPT_IDENTIFIERS.'='.urlencode( $list_enc );	// Identifiers.
		$params[] = kAPI_DATA_FIELD.'='.urlencode( $fields_enc );		// Fields.
		$params[] = kAPI_OPT_DIRECTION.'='.kAPI_DIRECTION_IN;			// Direction.
		$params[] = kAPI_OPT_LEVELS.'='.'2';							// Levels.
		$params[] = kAPI_OPT_LOG_TRACE.'='.'1';							// Trace exceptions.
		$params[] = kAPI_OPT_LOG_REQUEST.'='.'1';						// Log request.
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
	 *	GET DIRECTED EDGES (OUT)														*
	 *==================================================================================*/
	echo( '<h4>Get directed edges ('.kAPI_OP_GET_RELS.') OUT</h4>' );
	echo( '<i>Get all adges stemming from node 2, show only name.</i>' );
	//
	// Build identifiers list.
	//
	$list = array( 2 );
	//
	// Build fields list.
	//
	$fields = array( kTAG_NAME => TRUE );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CWarehouseWrapperClient( $url );
		$params->Operation( kAPI_OP_GET_RELS );
		$params->Format( kTYPE_JSON );
		$params->Database( 'WAREHOUSE' );
		$params->Container( kDEFAULT_CNT_TERMS );
		foreach( $list as $element )
			$params->Identifiers( $element, TRUE );
		$params->Fields( $fields );
		$params->Direction( kAPI_DIRECTION_OUT );
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
		// Build identifiers list.
		//
		$list_enc = json_encode( $list );
		//
		// Build fields list.
		//
		$fields_enc = json_encode( $fields );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_RELS;				// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;							// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';						// Database.
		$params[] = kAPI_CONTAINER.'='.kDEFAULT_CNT_TERMS;				// Container.
		$params[] = kAPI_OPT_IDENTIFIERS.'='.urlencode( $list_enc );	// Identifiers.
		$params[] = kAPI_DATA_FIELD.'='.urlencode( $fields_enc );		// Fields.
		$params[] = kAPI_OPT_DIRECTION.'='.kAPI_DIRECTION_OUT;			// Direction.
		$params[] = kAPI_OPT_LOG_TRACE.'='.'1';							// Trace exceptions.
		$params[] = kAPI_OPT_LOG_REQUEST.'='.'1';						// Log request.
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
	 *	GET DIRECTED EDGES (ALL)														*
	 *==================================================================================*/
	echo( '<h4>Get directed edges ('.kAPI_OP_GET_RELS.') list</h4>' );
	echo( '<i>Get all edges attached to node 2.</i>' );
	//
	// Build identifiers list.
	//
	$list = array( 2 );
	//
	// Build fields list.
	//
	$fields = array( kTAG_NAME => TRUE );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CWarehouseWrapperClient( $url );
		$params->Operation( kAPI_OP_GET_RELS );
		$params->Format( kTYPE_JSON );
		$params->Database( 'WAREHOUSE' );
		$params->Container( kDEFAULT_CNT_TERMS );
		foreach( $list as $element )
			$params->Identifiers( $element, TRUE );
		$params->Fields( $fields );
		$params->Direction( kAPI_DIRECTION_ALL );
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
		// Build identifiers list.
		//
		$list_enc = json_encode( $list );
		//
		// Build fields list.
		//
		$fields_enc = json_encode( $fields );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_RELS;				// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;							// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';						// Database.
		$params[] = kAPI_CONTAINER.'='.kDEFAULT_CNT_TERMS;				// Container.
		$params[] = kAPI_OPT_IDENTIFIERS.'='.urlencode( $list_enc );	// Identifiers.
		$params[] = kAPI_DATA_FIELD.'='.urlencode( $fields_enc );		// Fields.
		$params[] = kAPI_OPT_DIRECTION.'='.kAPI_DIRECTION_ALL;			// Direction.
		$params[] = kAPI_OPT_LOG_TRACE.'='.'1';							// Trace exceptions.
		$params[] = kAPI_OPT_LOG_REQUEST.'='.'1';						// Log request.
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
	 *	GET ROOTS (EMPTY)																*
	 *==================================================================================*/
	echo( '<h4>Get roots list ('.kAPI_OP_GET_ROOTS.') empty</h4>' );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CWarehouseWrapperClient( $url );
		$params->Operation( kAPI_OP_GET_ROOTS );
		$params->Format( kTYPE_JSON );
		$params->Database( 'WAREHOUSE' );
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
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_ROOTS;	// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';					// Database.
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
	 *	GET ROOTS (PASSPORT)															*
	 *==================================================================================*/
	echo( '<h4>Get roots list ('.kAPI_OP_GET_ROOTS.') Accessions domain</h4>' );
	//
	// Set query.
	//
	$query = array
	(
		kOPERATOR_OR => array
		(
			array
			(
				kAPI_QUERY_SUBJECT => kTAG_DATA.'.'.kTAG_DOMAIN,
				kAPI_QUERY_OPERATOR => kOPERATOR_EQUAL,
				kAPI_QUERY_TYPE => kTYPE_STRING,
				kAPI_QUERY_DATA => kDOMAIN_ACCESSION
			),
			array
			(
				kAPI_QUERY_SUBJECT => kTAG_DATA.'.'.kTAG_CATEGORY,
				kAPI_QUERY_OPERATOR => kOPERATOR_EQUAL,
				kAPI_QUERY_TYPE => kTYPE_STRING,
				kAPI_QUERY_DATA => kCATEGORY_PASSPORT
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
		$params = new CWarehouseWrapperClient( $url );
		$params->Operation( kAPI_OP_GET_ROOTS );
		$params->Format( kTYPE_JSON );
		$params->Database( 'WAREHOUSE' );
		$params->Query( $query );
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
		// Encode query.
		//
		$query_enc = json_encode( $query );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_ROOTS;			// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';					// Database.
		$params[] = kAPI_DATA_QUERY.'='.urlencode( $query_enc );	// Query.
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
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Query:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $query ); echo( '</pre>'.kSTYLE_DATA_POS );
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
	 *	LIST DATASETS (EMPTY)															*
	 *==================================================================================*/
	echo( '<h4>List datasets ('.kAPI_OP_GET_DATASETS.') empty</h4>' );
	echo( '<i>Return first 5 records.</i>' );
	//
	// Build fields list.
	//
	$fields = array( kTAG_TITLE => TRUE, kTAG_NAME => TRUE );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CWarehouseWrapperClient( $url );
		$params->Operation( kAPI_OP_GET_DATASETS );
		$params->Format( kTYPE_JSON );
		$params->Database( 'WAREHOUSE' );
		$params->Fields( $fields );
		$params->Start( 0 );
		$params->Limit( 5 );
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
		// Build fields list.
		//
		$fields = json_encode( $fields );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_DATASETS;		// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';					// Database.
		$params[] = kAPI_DATA_FIELD.'='.urlencode( $fields );		// Fields.
		$params[] = kAPI_PAGE_START.'='.'0';						// Page start.
		$params[] = kAPI_PAGE_LIMIT.'='.'5';						// Page limits.
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
		echo( kSTYLE_DATA_PRE.$response.kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
	}
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
