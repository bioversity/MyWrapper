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
 *								test_CWarehouseWrapper.php								*
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
// Environment includes.
//
require_once( '/Library/WebServer/Library/wrapper/local/environment.inc.php' );

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
define( 'kUSE_CLIENT', TRUE );


/*=======================================================================================
 *	TEST WRAPPER OBJECT																	*
 *======================================================================================*/
 
//
// Init local storage.
//
$url = 'http://localhost/newwrapper/WarehouseWrapper.php';

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
	$luca_id = $luca->Commit( $container );

	//
	// Create user 3.
	//
	$marco = new CUser();
	$marco->Code( 'MARCO' );
	$marco->Password( 'MarcoPass' );
	$marco->Name( 'Marco Frangella' );
	$marco->Email( 'm.frangella@cgiar.org' );
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
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;				// Format.
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
		$params->Options( kAPI_OPT_SAFE, TRUE );
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
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_LOGIN;				// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;				// Format.
		$params[] = kAPI_OPT_USER_CODE.'='.'LUCA';					// User code.
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
	
	/*===================================================================================
	 *	GET TERMS LIST (EMPTY)															*
	 *==================================================================================*/
	echo( '<h4>Get terms list ('.kAPI_OP_GET_TERMS.') empty</h4>' );
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
		$params->Container( 'DICTIONARY' );
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
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_TERMS;			// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';					// Database.
		$params[] = kAPI_CONTAINER.'='.'DICTIONARY';				// Container.
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
	 *	GET TERMS LIST (LIST)															*
	 *==================================================================================*/
	echo( '<h4>Get terms list ('.kAPI_OP_GET_TERMS.') list</h4>' );
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
		$params->Container( 'DICTIONARY' );
		$params->Identifiers( 'ISO-3166-1:IT', TRUE );
		$params->Identifiers( 'MCPD:SAMPSTAT:100', TRUE );
		$params->Identifiers( ':TERM', TRUE );
		$params->Identifiers( 'XXX', TRUE );
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
		$list = json_encode( array( 'ISO-3166-1:IT',
									'MCPD:SAMPSTAT:100',
									':TERM',
									'XXX' ) );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_TERMS;			// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';					// Database.
		$params[] = kAPI_CONTAINER.'='.'DICTIONARY';				// Container.
		$params[] = kAPI_OPT_IDENTIFIERS.'='.$list;					// Identifiers.
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
	 *	GET TERMS LIST (PAGING)															*
	 *==================================================================================*/
	echo( '<h4>Get terms list ('.kAPI_OP_GET_TERMS.') paging</h4>' );
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
		$params->Container( 'DICTIONARY' );
		$params->Identifiers( 'ISO-3166-1:IT', TRUE );
		$params->Identifiers( 'MCPD:SAMPSTAT:100', TRUE );
		$params->Identifiers( ':TERM', TRUE );
		$params->Identifiers( 'XXX', TRUE );
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
		$list = json_encode( array( 'ISO-3166-1:IT',
									'MCPD:SAMPSTAT:100',
									':TERM',
									'XXX' ) );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_TERMS;			// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';					// Database.
		$params[] = kAPI_CONTAINER.'='.'DICTIONARY';				// Container.
		$params[] = kAPI_OPT_IDENTIFIERS.'='.$list;					// Identifiers.
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
	 *	GET NODES LIST (EMPTY)															*
	 *==================================================================================*/
	echo( '<h4>Get nodes list ('.kAPI_OP_GET_NODES.') empty</h4>' );
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
		$params->Container( 'DICTIONARY' );
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
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_NODES;			// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';					// Database.
		$params[] = kAPI_CONTAINER.'='.'DICTIONARY';				// Container.
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
		$params->Container( 'DICTIONARY' );
		$params->Identifiers( 1, TRUE );
		$params->Identifiers( 2, TRUE );
		$params->Identifiers( 3, TRUE );
		$params->Identifiers( 99999, TRUE );
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
		$list = json_encode( array( 1, 2, 3, 99999 ) );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_NODES;			// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';					// Database.
		$params[] = kAPI_CONTAINER.'='.'DICTIONARY';				// Container.
		$params[] = kAPI_OPT_IDENTIFIERS.'='.$list;					// Identifiers.
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
	 *	GET NODES LIST (PAGING)															*
	 *==================================================================================*/
	echo( '<h4>Get terms list ('.kAPI_OP_GET_NODES.') paging</h4>' );
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
		$params->Container( 'DICTIONARY' );
		$params->Identifiers( 1, TRUE );
		$params->Identifiers( 2, TRUE );
		$params->Identifiers( 3, TRUE );
		$params->Identifiers( 4, TRUE );
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
		$list = json_encode( array( 1, 2, 3, 4 ) );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_NODES;			// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';					// Database.
		$params[] = kAPI_CONTAINER.'='.'DICTIONARY';				// Container.
		$params[] = kAPI_OPT_IDENTIFIERS.'='.$list;					// Identifiers.
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
