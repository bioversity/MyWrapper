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
 *									test_MATCH.php										*
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
define( 'kUSE_CLIENT', FALSE );


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
	/*===================================================================================
	 *	MATCH																			*
	 *==================================================================================*/
	echo( '<h4>Match</h4>' );
	echo( '<i>Should return the SAMPSTAT term by matching code.</i><br>' );
	//
	// Build query.
	//
	$query = array
	(
		array
		(
			kAPI_QUERY_SUBJECT => kTAG_GID,
			kAPI_QUERY_OPERATOR => kOPERATOR_EQUAL,
			kAPI_QUERY_TYPE => kTYPE_STRING,
			kAPI_QUERY_DATA => 'SAMPSTAT'
		),
		array
		(
			kAPI_QUERY_SUBJECT => kTAG_CODE,
			kAPI_QUERY_OPERATOR => kOPERATOR_EQUAL,
			kAPI_QUERY_TYPE => kTYPE_STRING,
			kAPI_QUERY_DATA => 'SAMPSTAT'
		),
		array
		(
			kAPI_QUERY_SUBJECT => kTAG_NAME.':'.kTAG_DATA,
			kAPI_QUERY_OPERATOR => kOPERATOR_REGEX,
			kAPI_QUERY_TYPE => kTYPE_STRING,
			kAPI_QUERY_DATA => new CDataTypeRegex( '/sampstat/i' )
		)
	);
	//
	// Build fields.
	//
	$fields = array( kTAG_GID => TRUE, kTAG_NAME => TRUE, kTAG_NODE => TRUE );
	//
	// Build sort.
	//
	$sort = array( kTAG_GID => 1 );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CMongoDataWrapperClient( $url );
		$params->Format( kTYPE_JSON );
		$params->Operation( kAPI_OP_MATCH );
		$params->Database( 'WAREHOUSE' );
		$params->Container( 'DICTIONARY' );
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
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_OPERATION.'='.kAPI_OP_MATCH;				// Command.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';					// Database.
		$params[] = kAPI_CONTAINER.'='.'DICTIONARY';				// Container.
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
	 *	MATCH																			*
	 *==================================================================================*/
	echo( '<h4>Match</h4>' );
	echo( '<i>Should return the SAMPSTAT 100 enumeration term by matching GID.</i><br>' );
	//
	// Build query.
	//
	$query = array
	(
		array
		(
			kAPI_QUERY_SUBJECT => kTAG_GID,
			kAPI_QUERY_OPERATOR => kOPERATOR_EQUAL,
			kAPI_QUERY_TYPE => kTYPE_STRING,
			kAPI_QUERY_DATA => 'MCPD:SAMPSTAT:100'
		),
		array
		(
			kAPI_QUERY_SUBJECT => kTAG_CODE,
			kAPI_QUERY_OPERATOR => kOPERATOR_EQUAL,
			kAPI_QUERY_TYPE => kTYPE_STRING,
			kAPI_QUERY_DATA => 'MCPD:SAMPSTAT:100'
		),
		array
		(
			kAPI_QUERY_SUBJECT => kTAG_NAME.':'.kTAG_DATA,
			kAPI_QUERY_OPERATOR => kOPERATOR_REGEX,
			kAPI_QUERY_TYPE => kTYPE_STRING,
			kAPI_QUERY_DATA => new CDataTypeRegex( '/MCPD:SAMPSTAT:100/i' )
		)
	);
	//
	// Build fields.
	//
	$fields = array( kTAG_GID => TRUE, kTAG_NAME => TRUE, kTAG_NODE => TRUE );
	//
	// Build sort.
	//
	$sort = array( kTAG_GID => 1 );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CMongoDataWrapperClient( $url );
		$params->Format( kTYPE_JSON );
		$params->Operation( kAPI_OP_MATCH );
		$params->Database( 'WAREHOUSE' );
		$params->Container( 'DICTIONARY' );
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
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_OPERATION.'='.kAPI_OP_MATCH;				// Command.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';					// Database.
		$params[] = kAPI_CONTAINER.'='.'DICTIONARY';				// Container.
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
	 *	MATCH																			*
	 *==================================================================================*/
	echo( '<h4>Match</h4>' );
	echo( '<i>Should return the Italian language enumeration terms by matching the name.</i><br>' );
	//
	// Build query.
	//
	$query = array
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
			kAPI_QUERY_SUBJECT => kTAG_CODE,
			kAPI_QUERY_OPERATOR => kOPERATOR_EQUAL,
			kAPI_QUERY_TYPE => kTYPE_STRING,
			kAPI_QUERY_DATA => 'Italian'
		),
		array
		(
			kAPI_QUERY_SUBJECT => kTAG_NAME.'.'.kTAG_DATA,
			kAPI_QUERY_OPERATOR => kOPERATOR_CONTAINS,
			kAPI_QUERY_TYPE => kTYPE_STRING,
			kAPI_QUERY_DATA => 'Italian'
		)
	);
	//
	// Build fields.
	//
	$fields = array( kTAG_GID => TRUE, kTAG_NAME => TRUE, kTAG_NODE => TRUE );
	//
	// Build sort.
	//
	$sort = array( kTAG_GID => 1 );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CMongoDataWrapperClient( $url );
		$params->Format( kTYPE_JSON );
		$params->Operation( kAPI_OP_MATCH );
		$params->Database( 'WAREHOUSE' );
		$params->Container( 'DICTIONARY' );
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
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_OPERATION.'='.kAPI_OP_MATCH;				// Command.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';					// Database.
		$params[] = kAPI_CONTAINER.'='.'DICTIONARY';				// Container.
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
