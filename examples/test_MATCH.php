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
$url = 'http://localhost/wrapper/MongoDataWrapper.php';

//
// TRY BLOCK.
//
try
{
	/*===================================================================================
	 *	MATCH																			*
	 *==================================================================================*/
	echo( '<h4>Match</h4>' );
	//
	// Build query.
	//
	$query = Array();
	$q = new CMongoQuery();
	$q->AppendStatement(
			CQueryStatement::Equals(
				kTAG_LID,
				new CDataTypeBinary( md5( 'ITPGRFA:ANNEX1-CROP', TRUE ) ),
				kTYPE_BINARY ),
			kOPERATOR_AND );
	$query[] = $q->getArrayCopy();
	$q->AppendStatement( CQueryStatement::Exists( kTAG_NODE ) );
	$query[] = $q->getArrayCopy();

	$q = new CMongoQuery();
	$q->AppendStatement( CQueryStatement::Equals( kTAG_CODE, 'SAMPSTAT' ), kOPERATOR_AND );
	$query[] = $q->getArrayCopy();
	$q = new CMongoQuery();
	$q->AppendStatement(
			CQueryStatement::ContainsNoCase(
				kTAG_NAME.':'.kTAG_DATA, 'sampstat' ), kOPERATOR_AND );
	$query[] = $q->getArrayCopy();
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
		$params->Container( kDEFAULT_CNT_TERMS );
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
		$params[] = kAPI_CONTAINER.'='.kDEFAULT_CNT_TERMS;				// Container.
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
	$query = Array();
	$q = new CMongoQuery();
	$q->AppendStatement(
			CQueryStatement::Equals(
				kTAG_GID,
				'MCPD:SAMPSTAT:100',
				kTYPE_STRING ),
			kOPERATOR_AND );
	$query[] = $q->getArrayCopy();

	$q = new CMongoQuery();
	$q->AppendStatement(
			CQueryStatement::Equals(
				kTAG_CODE,
				'MCPD:SAMPSTAT:100',
				kTYPE_STRING ),
			kOPERATOR_AND );
	$query[] = $q->getArrayCopy();

	$q = new CMongoQuery();
	$q->AppendStatement(
			CQueryStatement::Equals(
				kTAG_NAME.':'.kTAG_DATA,
				'MCPD:SAMPSTAT:100',
				kTYPE_STRING ),
			kOPERATOR_AND );
	$query[] = $q->getArrayCopy();
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
		$params->Container( kDEFAULT_CNT_TERMS );
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
		$params[] = kAPI_CONTAINER.'='.kDEFAULT_CNT_TERMS;				// Container.
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
	$query = Array();
	$q = new CMongoQuery();
	$q->AppendStatement(
			CQueryStatement::Contains(
				kTAG_GID,
				'Italian' ),
			kOPERATOR_AND );
	$query[] = $q->getArrayCopy();

	$q = new CMongoQuery();
	$q->AppendStatement(
			CQueryStatement::Contains(
				kTAG_CODE,
				'Italian' ),
			kOPERATOR_AND );
	$query[] = $q->getArrayCopy();

	$q = new CMongoQuery();
	$q->AppendStatement(
			CQueryStatement::ContainsNoCase(
				kTAG_NAME.'.'.kTAG_DATA,
				'italian' ),
			kOPERATOR_AND );
	$query[] = $q->getArrayCopy();
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
		$params->Container( kDEFAULT_CNT_TERMS );
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
		$params[] = kAPI_CONTAINER.'='.kDEFAULT_CNT_TERMS;				// Container.
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
