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
	 *	LIST TERMS (EMPTY)																*
	 *==================================================================================*/
	echo( '<h4>List terms ('.kAPI_OP_GET_TERMS.') empty</h4>' );
	echo( '<i>Return first 10 records.</i>' );
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
		$params->Limit( 10 );
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
		$params[] = kAPI_DATA_FIELD.'='.$fields;					// Fields.
		$params[] = kAPI_PAGE_START.'='.'0';						// Page start.
		$params[] = kAPI_PAGE_LIMIT.'='.'10';						// Page limits.
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
	$list = array( 'ISO:3166:1:IT', 'MCPD:SAMPSTAT:100', ':TERM', 'XXX' );
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
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_TERMS;			// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';					// Database.
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
	 *	GET TERMS LIST (PAGING)															*
	 *==================================================================================*/
	echo( '<h4>Get terms list ('.kAPI_OP_GET_TERMS.') paging</h4>' );
	//
	// Init identifiers.
	//
	$list = array( 'ISO:3166:1:IT', 'MCPD:SAMPSTAT:100', ':TERM', 'XXX' );
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
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_TERMS;			// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';					// Database.
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
	 *	GET TERMS LIST (QUERY)															*
	 *==================================================================================*/
	echo( '<h4>Get terms list ('.kAPI_OP_GET_TERMS.') query</h4>' );
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
	
	/*===================================================================================
	 *	LIST NODES (EMPTY)																*
	 *==================================================================================*/
	echo( '<h4>Get nodes list ('.kAPI_OP_GET_NODES.') empty</h4>' );
	echo( '<i>Return first 10 records.</i>' );
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
		$params->Limit( 10 );
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
		$params[] = kAPI_DATA_FIELD.'='.$fields;					// Fields.
		$params[] = kAPI_PAGE_START.'='.'0';						// Page start.
		$params[] = kAPI_PAGE_LIMIT.'='.'10';						// Page limits.
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
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_NODES;			// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';					// Database.
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
	 *	LIST NODES (QUERY)																*
	 *==================================================================================*/
	echo( '<h4>Get nodes list ('.kAPI_OP_GET_NODES.') query</h4>' );
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
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_NODES;			// Command.
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
exit;
	
	/*===================================================================================
	 *	LIST EDGES (EMPTY)																*
	 *==================================================================================*/
	echo( '<h4>Get edges list ('.kAPI_OP_GET_EDGES.') empty</h4>' );
	echo( '<i>Return first 10 records.</i>' );
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
		$params->Limit( 10 );
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
		$params[] = kAPI_DATA_FIELD.'='.$fields;					// Fields.
		$params[] = kAPI_PAGE_START.'='.'0';						// Page start.
		$params[] = kAPI_PAGE_LIMIT.'='.'10';						// Page limits.
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
	$list = array( 0, 1, 2, 3, 9999999 );
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
exit;
	
	/*===================================================================================
	 *	LIST EDGES (QUERY)																*
	 *==================================================================================*/
	echo( '<h4>Get edges list ('.kAPI_OP_GET_EDGES.') query</h4>' );
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
				kAPI_QUERY_SUBJECT => kTAG_DATA.'.'.kTAG_KIND,
				kAPI_QUERY_OPERATOR => kOPERATOR_EQUAL,
				kAPI_QUERY_TYPE => kTYPE_STRING,
				kAPI_QUERY_DATA => kTYPE_EXACT
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
		$params->Operation( kAPI_OP_GET_EDGES );
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
		// Build identifiers list.
		//
		$query_enc = json_encode( $query );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_EDGES;			// Command.
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
	 *	GET RELATED (EMPTY)																*
	 *==================================================================================*/
	echo( '<h4>Get related ('.kAPI_OP_GET_RELS.') empty</h4>' );
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
	 *	GET EDGES LIST (LIST WITH PREDICATES - NO DIRECTION)							*
	 *==================================================================================*/
	echo( '<h4>Get edges list ('.kAPI_OP_GET_RELS.') list with predicate and no direction</h4>' );
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
		$params->Identifiers( 0, TRUE );
		$params->Identifiers( 9990, TRUE );
		$params->Identifiers( 9991, TRUE );
		$params->Identifiers( 51, TRUE );
		$params->Identifiers( 99999, TRUE );
		$params->Fields( array( kTAG_NAME => TRUE ) );
		$params->Predicates( kPRED_PART_OF, TRUE );
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
		$list = json_encode( array( 0, 9990, 9991, 51, 99999 ) );
		//
		// Build fields list.
		//
		$fields = json_encode( array( kTAG_NAME => TRUE ) );
		//
		// Build identifiers list.
		//
		$predicates = json_encode( array( kPRED_PART_OF ) );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_RELS;			// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';					// Database.
		$params[] = kAPI_CONTAINER.'='.kDEFAULT_CNT_TERMS;				// Container.
		$params[] = kAPI_OPT_IDENTIFIERS.'='.$list;					// Identifiers.
		$params[] = kAPI_DATA_FIELD.'='.$fields;					// Fields.
		$params[] = kAPI_OPT_PREDICATES.'='.$predicates;			// Predicates.
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
exit;
	
	/*===================================================================================
	 *	GET DIRECTED EDGES (IN)															*
	 *==================================================================================*/
	echo( '<h4>Get directed edges ('.kAPI_OP_GET_RELS.') IN</h4>' );
	echo( '<i>Get all nodes that point to node 2.</i>' );
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
		$params->Identifiers( 2, TRUE );
		$params->Fields( array( kTAG_NAME => TRUE ) );
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
		$list = json_encode( array( 2 ) );
		//
		// Build fields list.
		//
		$fields = json_encode( array( kTAG_NAME => TRUE ) );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_RELS;			// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';					// Database.
		$params[] = kAPI_CONTAINER.'='.kDEFAULT_CNT_TERMS;				// Container.
		$params[] = kAPI_OPT_IDENTIFIERS.'='.$list;					// Identifiers.
		$params[] = kAPI_OPT_DIRECTION.'='.kAPI_DIRECTION_IN;		// Direction.
		$params[] = kAPI_DATA_FIELD.'='.$fields;					// Fields.
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
	 *	GET DIRECTED EDGES (IN) 3 LEVELS												*
	 *==================================================================================*/
	echo( '<h4>Get directed edges ('.kAPI_OP_GET_RELS.') IN 3 LEVELS</h4>' );
	echo( '<i>Get all nodes that point to node 1.</i>' );
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
		$params->Identifiers( 1, TRUE );
		$params->Fields( array( kTAG_NAME => TRUE ) );
		$params->Direction( kAPI_DIRECTION_IN );
		$params->Levels( 3 );
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
		$list = json_encode( array( 1 ) );
		//
		// Build fields list.
		//
		$fields = json_encode( array( kTAG_NAME => TRUE ) );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_RELS;			// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';					// Database.
		$params[] = kAPI_CONTAINER.'='.kDEFAULT_CNT_TERMS;				// Container.
		$params[] = kAPI_OPT_IDENTIFIERS.'='.$list;					// Identifiers.
		$params[] = kAPI_OPT_DIRECTION.'='.kAPI_DIRECTION_IN;		// Direction.
		$params[] = kAPI_OPT_LEVELS.'='.'3';						// Levels.
		$params[] = kAPI_DATA_FIELD.'='.$fields;					// Fields.
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
	 *	GET DIRECTED EDGES (OUT)														*
	 *==================================================================================*/
	echo( '<h4>Get directed edges ('.kAPI_OP_GET_RELS.') list</h4>' );
	echo( '<i>Get all nodes that node 2 points to.</i>' );
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
		$params->Identifiers( 2, TRUE );
		$params->Direction( kAPI_DIRECTION_OUT );
		$params->Fields( array( kTAG_NAME => TRUE ) );
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
		$list = json_encode( array( 2 ) );
		//
		// Build fields list.
		//
		$fields = json_encode( array( kTAG_NAME => TRUE ) );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_RELS;			// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';					// Database.
		$params[] = kAPI_CONTAINER.'='.kDEFAULT_CNT_TERMS;				// Container.
		$params[] = kAPI_OPT_IDENTIFIERS.'='.$list;					// Identifiers.
		$params[] = kAPI_OPT_DIRECTION.'='.kAPI_DIRECTION_OUT;		// Direction.
		$params[] = kAPI_DATA_FIELD.'='.$fields;					// Fields.
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
	 *	GET DIRECTED EDGES (ALL)														*
	 *==================================================================================*/
	echo( '<h4>Get directed edges ('.kAPI_OP_GET_RELS.') list</h4>' );
	echo( '<i>Get all nodes related to node 2.</i>' );
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
		$params->Identifiers( 2, TRUE );
		$params->Direction( kAPI_DIRECTION_ALL );
		$params->Fields( array( kTAG_NAME => TRUE ) );
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
		$list = json_encode( array( 2 ) );
		//
		// Build fields list.
		//
		$fields = json_encode( array( kTAG_NAME => TRUE ) );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_RELS;			// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';					// Database.
		$params[] = kAPI_CONTAINER.'='.kDEFAULT_CNT_TERMS;				// Container.
		$params[] = kAPI_OPT_IDENTIFIERS.'='.$list;					// Identifiers.
		$params[] = kAPI_OPT_DIRECTION.'='.kAPI_DIRECTION_ALL;		// Direction.
		$params[] = kAPI_DATA_FIELD.'='.$fields;					// Fields.
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
	 *	GET ONTOLOGIES (EMPTY)															*
	 *==================================================================================*/
	echo( '<h4>Get ontologies list ('.kAPI_OP_QUERY_ROOTS.') empty</h4>' );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CWarehouseWrapperClient( $url );
		$params->Operation( kAPI_OP_QUERY_ROOTS );
		$params->Format( kTYPE_JSON );
		$params->Database( 'WAREHOUSE' );
		$params->Container( kDEFAULT_CNT_TERMS );
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
		$params[] = kAPI_OPERATION.'='.kAPI_OP_QUERY_ROOTS;	// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';					// Database.
		$params[] = kAPI_CONTAINER.'='.kDEFAULT_CNT_TERMS;				// Container.
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
	 *	GET ONTOLOGIES (GEOGRAPHY)															*
	 *==================================================================================*/
	echo( '<h4>Get ontologies list ('.kAPI_OP_QUERY_ROOTS.') Geography domain</h4>' );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CWarehouseWrapperClient( $url );
		$params->Operation( kAPI_OP_QUERY_ROOTS );
		$params->Format( kTYPE_JSON );
		$params->Database( 'WAREHOUSE' );
		$params->Container( kDEFAULT_CNT_TERMS );
		$params->Attributes( kTAG_DOMAIN, kDOMAIN_GEOGRAPHY, TRUE );
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
		$list = json_encode( array( kTAG_DOMAIN => array( kDOMAIN_GEOGRAPHY ) ) );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_QUERY_ROOTS;	// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';					// Database.
		$params[] = kAPI_CONTAINER.'='.kDEFAULT_CNT_TERMS;				// Container.
		$params[] = kAPI_OPT_ATTRIBUTES.'='.$list;				// Attributes.
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
	 *	GET ONTOLOGIES (PASSPORT)														*
	 *==================================================================================*/
	echo( '<h4>Get ontologies list ('.kAPI_OP_QUERY_ROOTS.') Accessions Passport</h4>' );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CWarehouseWrapperClient( $url );
		$params->Operation( kAPI_OP_QUERY_ROOTS );
		$params->Format( kTYPE_JSON );
		$params->Database( 'WAREHOUSE' );
		$params->Container( kDEFAULT_CNT_TERMS );
		$params->Attributes( kTAG_DOMAIN, kDOMAIN_ACCESSION, TRUE );
		$params->Attributes( kTAG_CATEGORY, kCATEGORY_PASSPORT, TRUE );
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
		$list = array( kTAG_DOMAIN => array( kDOMAIN_ACCESSION ),
					   kTAG_CATEGORY => array( kCATEGORY_PASSPORT ) );
		$list = json_encode( $list );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_QUERY_ROOTS;	// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';					// Database.
		$params[] = kAPI_CONTAINER.'='.kDEFAULT_CNT_TERMS;				// Container.
		$params[] = kAPI_OPT_ATTRIBUTES.'='.$list;				// Attributes.
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
