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
require_once( kPATH_LIBRARY_SOURCE."CMongoDataWrapper.php" );


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
	// Open mongo connection.
	//
	$mongo = New Mongo();
	
	//
	// Select MCPD database.
	//
	$db = $mongo->selectDB( 'ONTOLOGY' );
	
	//
	// Select test collection.
	//
	$collection = $db->selectCollection( 'VOCABULARY' );
	$container = new CMongoContainer( $collection );
	
	//
	// Get test object.
	//
	$object_serialised = $object = $collection->findOne( array( ':GID' => 'PATO:0001766' ) );
	CDataType::SerialiseObject( $object_serialised );
	
	//
	// Convert object.
	//
	$object_json = json_encode( $object_serialised );
	$object_php = serialize( $object_serialised );
	
	echo( '<h4>Empty request</h4>' );
	//
	// Empty request.
	//
	$response = file_get_contents( $url );
	$decoded = json_decode( $response, TRUE );
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'URL:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.htmlspecialchars( $url ).kSTYLE_DATA_POS );
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
	
	echo( '<h4>Missing operation</h4>' );
	//
	// Missing operation.
	//
	$params = array( (kAPI_FORMAT.'='.kDATA_TYPE_PHP),
					 (kAPI_REQ_STAMP.'='.gettimeofday( true )),
					 (kAPI_OPT_LOG_REQUEST.'='.'1') );
	$request = $url.'?'.implode( '&', $params );
	$response = file_get_contents( $request );
	$decoded = unserialize( $response );
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
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
	
	echo( '<h4>Ping wrapper in PHP</h4>' );
	//
	// Ping wrapper in PHP.
	//
	$params = array( (kAPI_FORMAT.'='.kDATA_TYPE_PHP),
					 (kAPI_OPERATION.'='.kAPI_OP_PING),
					 (kAPI_REQ_STAMP.'='.gettimeofday( true )),
					 (kAPI_OPT_LOG_REQUEST.'='.'1') );
	$request = $url.'?'.implode( '&', $params );
	$response = file_get_contents( $request );
	$decoded = unserialize( $response );
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
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
	
	echo( '<h4>Ping wrapper in JSON</h4>' );
	//
	// Ping wrapper in JSON.
	//
	$params = array( (kAPI_FORMAT.'='.kDATA_TYPE_JSON),
					 (kAPI_OPERATION.'='.kAPI_OP_PING),
					 (kAPI_REQ_STAMP.'='.gettimeofday( true )),
					 (kAPI_OPT_LOG_REQUEST.'='.'1') );
	$request = $url.'?'.implode( '&', $params );
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
	echo( kSTYLE_HEAD_PRE.'Response:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.htmlspecialchars( $response ).kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Decoded:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $decoded ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_TABLE_POS );
	echo( '<hr>' );
	
	echo( '<h4>Debug wrapper</h4>' );
	//
	// Debug wrapper.
	//
	$params = array( (kAPI_FORMAT.'='.kDATA_TYPE_JSON),
					 (kAPI_OPERATION.'='.kAPI_OP_DEBUG),
					 (kAPI_REQ_STAMP.'='.gettimeofday( true )),
					 (kAPI_OPT_LOG_REQUEST.'='.'1') );
	$request = $url.'?'.implode( '&', $params );
	$response = file_get_contents( $request );
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'URL:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.htmlspecialchars( $request ).kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Response:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.$response.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_TABLE_POS );
	echo( '<hr>' );
	
	echo( '<h4>Decode object in PHP</h4>' );
	//
	// Decode object in PHP.
	//
	$params = array( (kAPI_FORMAT.'='.kDATA_TYPE_PHP),
					 (kAPI_OPERATION.'='.kAPI_OP_PING),
					 (kAPI_DATA_OBJECT.'='.urlencode( $object_php )),
					 (kAPI_REQ_STAMP.'='.gettimeofday( true )),
					 (kAPI_OPT_LOG_REQUEST.'='.'1') );
	$request = $url.'?'.implode( '&', $params );
	$response = file_get_contents( $request );
	$decoded = unserialize( $response );
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
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
	
	echo( '<h4>Decode object in JSON</h4>' );
	//
	// Decode object in JSON.
	//
	$params = array( (kAPI_FORMAT.'='.kDATA_TYPE_JSON),
					 (kAPI_OPERATION.'='.kAPI_OP_PING),
					 (kAPI_DATA_OBJECT.'='.urlencode( $object_json )),
					 (kAPI_REQ_STAMP.'='.gettimeofday( true )),
					 (kAPI_OPT_LOG_REQUEST.'='.'1') );
	$request = $url.'?'.implode( '&', $params );
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
	echo( kSTYLE_HEAD_PRE.'Response:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.htmlspecialchars( $response ).kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Decoded:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $decoded ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_TABLE_POS );
	echo( '<hr>' );
	
	echo( '<h4>Decode query in PHP</h4>' );
	//
	// Decode query in PHP.
	//
	$query_php = serialize( array
	(
		kOPERATOR_AND => array
		(
			0 => array
			(
				kAPI_QUERY_SUBJECT => ':XREF.:SCOPE',
				kAPI_QUERY_OPERATOR => kOPERATOR_EQUAL,
				kAPI_QUERY_TYPE => kDATA_TYPE_STRING,
				kAPI_QUERY_DATA => '2'
			),
			
			1 => array
			(
				kOPERATOR_OR => array
				(
					0 => array
					(
						kAPI_QUERY_SUBJECT => ':XREF.:DATA._code',
						kAPI_QUERY_OPERATOR => kOPERATOR_PREFIX,
						kAPI_QUERY_TYPE => kDATA_TYPE_STRING,
						kAPI_QUERY_DATA => 'NCBI_taxid:'
					),
					
					1 => array
					(
						kAPI_QUERY_SUBJECT => ':XREF.:DATA._code',
						kAPI_QUERY_OPERATOR => kOPERATOR_PREFIX,
						kAPI_QUERY_TYPE => kDATA_TYPE_STRING,
						kAPI_QUERY_DATA =>  'GR:'
					)
				)
			)
		)
	) );
	$fields_php = serialize( array( ':GID', ':XREF' ) );
	$sort_php = serialize( array( ':LID', ':TYPE' ) );
	$object_php = serialize( $object_serialised );
	$params = array( (kAPI_FORMAT.'='.kDATA_TYPE_PHP),
					 (kAPI_OPERATION.'='.kAPI_OP_DEBUG),
					 (kAPI_PAGE_START.'='.'0'),
					 (kAPI_PAGE_LIMIT.'='.'10'),
					 (kAPI_DATA_QUERY.'='.urlencode( $query_php )),
					 (kAPI_DATA_FIELD.'='.urlencode( $fields_php )),
					 (kAPI_DATA_SORT.'='.urlencode( $sort_php )),
					 (kAPI_DATA_OBJECT.'='.urlencode( $object_php )),
					 (kAPI_REQ_STAMP.'='.gettimeofday( true )),
					 (kAPI_OPT_LOG_REQUEST.'='.'1') );
	$request = $url.'?'.implode( '&', $params );
	$response = file_get_contents( $request );
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'URL:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.htmlspecialchars( $request ).kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Response:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.$response.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_TABLE_POS );
	echo( '<hr>' );
	
	echo( '<h4>Decode query in JSON</h4>' );
	//
	// Decode query in JSON.
	//
	$query_json = json_encode( array
	(
		kOPERATOR_AND => array
		(
			0 => array
			(
				kAPI_QUERY_SUBJECT => ':XREF.:SCOPE',
				kAPI_QUERY_OPERATOR => kOPERATOR_EQUAL,
				kAPI_QUERY_TYPE => kDATA_TYPE_STRING,
				kAPI_QUERY_DATA => '2'
			),
			
			1 => array
			(
				kOPERATOR_OR => array
				(
					0 => array
					(
						kAPI_QUERY_SUBJECT => ':XREF.:DATA._code',
						kAPI_QUERY_OPERATOR => kOPERATOR_PREFIX,
						kAPI_QUERY_TYPE => kDATA_TYPE_STRING,
						kAPI_QUERY_DATA => 'NCBI_taxid:'
					),
					
					1 => array
					(
						kAPI_QUERY_SUBJECT => ':XREF.:DATA._code',
						kAPI_QUERY_OPERATOR => kOPERATOR_PREFIX,
						kAPI_QUERY_TYPE => kDATA_TYPE_STRING,
						kAPI_QUERY_DATA =>  'GR:'
					)
				)
			)
		)
	) );
	$fields_json = json_encode( array( ':GID', ':XREF' ) );
	$sort_json = json_encode( array( ':LID', ':TYPE' ) );
	$object_json = json_encode( $object_serialised );
	$params = array( (kAPI_FORMAT.'='.kDATA_TYPE_JSON),
					 (kAPI_OPERATION.'='.kAPI_OP_DEBUG),
					 (kAPI_PAGE_START.'='.'0'),
					 (kAPI_PAGE_LIMIT.'='.'10'),
					 (kAPI_DATA_QUERY.'='.urlencode( $query_json )),
					 (kAPI_DATA_FIELD.'='.urlencode( $fields_json )),
					 (kAPI_DATA_SORT.'='.urlencode( $sort_json )),
					 (kAPI_DATA_OBJECT.'='.urlencode( $object_json )),
					 (kAPI_REQ_STAMP.'='.gettimeofday( true )),
					 (kAPI_OPT_LOG_REQUEST.'='.'1') );
	$request = $url.'?'.implode( '&', $params );
	$response = file_get_contents( $request );
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'URL:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.htmlspecialchars( $request ).kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Response:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.$response.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_TABLE_POS );
	echo( '<hr>' );
	
	echo( '<h4>Invalid operator</h4>' );
	//
	// Invalid operator.
	//
	$params = array( (kAPI_FORMAT.'='.kDATA_TYPE_JSON),
					 (kAPI_OPERATION.'='.'XXX'),
					 (kAPI_REQ_STAMP.'='.gettimeofday( true )),
					 (kAPI_OPT_LOG_REQUEST.'='.'1') );
	$request = $url.'?'.implode( '&', $params );
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
	echo( kSTYLE_HEAD_PRE.'Response:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.htmlspecialchars( $response ).kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Decoded:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $decoded ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_TABLE_POS );
	echo( '<hr>' );
	
	echo( '<h4>Missing operator</h4>' );
	//
	// Missing operator.
	//
	$params = array( (kAPI_FORMAT.'='.kDATA_TYPE_JSON),
					 (kAPI_REQ_STAMP.'='.gettimeofday( true )),
					 (kAPI_OPT_LOG_REQUEST.'='.'1') );
	$request = $url.'?'.implode( '&', $params );
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
	echo( kSTYLE_HEAD_PRE.'Response:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.htmlspecialchars( $response ).kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Decoded:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $decoded ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_TABLE_POS );
	echo( '<hr>' );
	
	echo( '<h4>Missing data store references</h4>' );
	//
	// Missing data store references.
	//
	$params = array( (kAPI_FORMAT.'='.kDATA_TYPE_JSON),
					 (kAPI_OPERATION.'='.kAPI_OP_GET_ONE),
					 (kAPI_REQ_STAMP.'='.gettimeofday( true )),
					 (kAPI_OPT_LOG_REQUEST.'='.'1') );
	$request = $url.'?'.implode( '&', $params );
	$response = file_get_contents( $request );
	$decoded = CObject::JsonDecode( $response );
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
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
	
	echo( '<h4>Missing database references</h4>' );
	//
	// Missing database references.
	//
	$params = array( (kAPI_FORMAT.'='.kDATA_TYPE_JSON),
					 (kAPI_OPERATION.'='.kAPI_OP_GET_ONE),
					 (kAPI_CONTAINER.'='.'VOCABULARY'),
					 (kAPI_REQ_STAMP.'='.gettimeofday( true )),
					 (kAPI_OPT_LOG_REQUEST.'='.'1') );
	$request = $url.'?'.implode( '&', $params );
	$response = file_get_contents( $request );
	$decoded = CObject::JsonDecode( $response );
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
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
	
	echo( '<h4>Missing container references</h4>' );
	//
	// Missing container references.
	//
	$params = array( (kAPI_FORMAT.'='.kDATA_TYPE_JSON),
					 (kAPI_OPERATION.'='.kAPI_OP_GET_ONE),
					 (kAPI_DATABASE.'='.'ONTOLOGY'),
					 (kAPI_REQ_STAMP.'='.gettimeofday( true )),
					 (kAPI_OPT_LOG_REQUEST.'='.'1') );
	$request = $url.'?'.implode( '&', $params );
	$response = file_get_contents( $request );
	$decoded = CObject::JsonDecode( $response );
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
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
	echo( '<hr>' );
	
	//
	// Test queries.
	//
	$queries = array
	(

		array
		(
			kOPERATOR_AND => array
			(
				0 => array
				(
					kAPI_QUERY_SUBJECT => ':TYPE',
					kAPI_QUERY_OPERATOR => kOPERATOR_EQUAL,
					kAPI_QUERY_TYPE => kDATA_TYPE_STRING,
					kAPI_QUERY_DATA => ':TERM'
				),
				
				1 => array
				(
					kAPI_QUERY_SUBJECT => ':XREF.:SCOPE',
					kAPI_QUERY_OPERATOR => kOPERATOR_EQUAL,
					kAPI_QUERY_TYPE => kDATA_TYPE_STRING,
					kAPI_QUERY_DATA => '2'
				)
			)
		),
		array
		(
			kOPERATOR_OR => array
			(
				0 => array
				(
					kAPI_QUERY_SUBJECT => ':XREF.:DATA._code',
					kAPI_QUERY_OPERATOR => kOPERATOR_PREFIX,
					kAPI_QUERY_TYPE => kDATA_TYPE_STRING,
					kAPI_QUERY_DATA => 'NCBI_taxid:'
				),
				
				1 => array
				(
					kAPI_QUERY_SUBJECT => ':XREF.:DATA._code',
					kAPI_QUERY_OPERATOR => kOPERATOR_PREFIX,
					kAPI_QUERY_TYPE => kDATA_TYPE_STRING,
					kAPI_QUERY_DATA =>  'GR:'
				)
			)
		),
		array
		(
			kOPERATOR_AND => array
			(
				0 => array
				(
					kAPI_QUERY_SUBJECT => ':TYPE',
					kAPI_QUERY_OPERATOR => kOPERATOR_EQUAL,
					kAPI_QUERY_TYPE => kDATA_TYPE_STRING,
					kAPI_QUERY_DATA => ':TERM'
				),
				
				1 => array
				(
					kAPI_QUERY_SUBJECT => ':XREF.:SCOPE',
					kAPI_QUERY_OPERATOR => kOPERATOR_EQUAL,
					kAPI_QUERY_TYPE => kDATA_TYPE_STRING,
					kAPI_QUERY_DATA => '2'
				),
				
				2 => array
				(
					kOPERATOR_OR => array
					(
						0 => array
						(
							kAPI_QUERY_SUBJECT => ':XREF.:DATA._code',
							kAPI_QUERY_OPERATOR => kOPERATOR_PREFIX,
							kAPI_QUERY_TYPE => kDATA_TYPE_STRING,
							kAPI_QUERY_DATA => 'NCBI_taxid:'
						),
						
						1 => array
						(
							kAPI_QUERY_SUBJECT => ':XREF.:DATA._code',
							kAPI_QUERY_OPERATOR => kOPERATOR_PREFIX,
							kAPI_QUERY_TYPE => kDATA_TYPE_STRING,
							kAPI_QUERY_DATA =>  'GR:'
						)
					)
				)
			)
		),
		array
		(
			kOPERATOR_AND => array
			(
				array
				(
					kAPI_QUERY_SUBJECT => '_id',
					kAPI_QUERY_OPERATOR => kOPERATOR_EQUAL,
					kAPI_QUERY_TYPE => kDATA_TYPE_MongoId,
					kAPI_QUERY_DATA => array
					(
						kTAG_TYPE => kDATA_TYPE_MongoId,
						kTAG_DATA => '4de8e3e7961be57b0900329a'
					)
				)
			)
		),
		array
		(
			kOPERATOR_AND => array
			(
				0 => array
				(
					kAPI_QUERY_SUBJECT => ':REF-COUNT',
					kAPI_QUERY_OPERATOR => kOPERATOR_IRANGE,
					kAPI_QUERY_TYPE => kDATA_TYPE_INT32,
					kAPI_QUERY_DATA => array
					(
						array
						(
							kTAG_TYPE => kDATA_TYPE_INT32,
							kTAG_DATA => 186
						),
						array
						(
							kTAG_TYPE => kDATA_TYPE_INT32,
							kTAG_DATA => 103
						)
					)
				)
			)
		),
		array
		(
			kOPERATOR_AND => array
			(
				0 => array
				(
					kAPI_QUERY_SUBJECT => ':XREF',
					kAPI_QUERY_OPERATOR => kOPERATOR_NOT_NULL
				)
			)
		),
		array
		(
			kOPERATOR_AND => array
			(
				0 => array
				(
					kAPI_QUERY_SUBJECT => ':REF-COUNT',
					kAPI_QUERY_OPERATOR => kOPERATOR_NI,
					kAPI_QUERY_TYPE => kDATA_TYPE_INT32,
					kAPI_QUERY_DATA => array
					(
						array
						(
							kTAG_TYPE => kDATA_TYPE_INT32,
							kTAG_DATA => 186
						),
						array
						(
							kTAG_TYPE => kDATA_TYPE_INT32,
							kTAG_DATA => 103
						)
					)
				)
			)
		)
	);
	
	echo( '<h4>Test queries</h4>' );
	//
	// Test queries.
	//
	$fields = array( ':GID', ':REF-COUNT' );
	$fields_php = serialize( $fields );
	$fields_json = json_encode( $fields );
	$sort = array( ':GID' );
	$sort_php = serialize( $sort );
	$sort_json = json_encode( $sort );
	foreach( $queries as $index => $query )
	{
		echo( "<b>Query [$index]</b><br>" );
		$query_php = serialize( $query );
		$query_json = json_encode( $query );
		
		echo( '<h4>kAPI_OP_COUNT in PHP</h4>' );
		//
		// Do kAPI_OP_COUNT.
		//
		$params = array( (kAPI_FORMAT.'='.kDATA_TYPE_PHP),
						 (kAPI_OPERATION.'='.kAPI_OP_COUNT),
						 (kAPI_DATABASE.'='.'ONTOLOGY'),
						 (kAPI_CONTAINER.'='.'VOCABULARY'),
						 (kAPI_DATA_QUERY.'='.urlencode( $query_php )),
						 (kAPI_DATA_FIELD.'='.urlencode( $fields_php )),
						 (kAPI_DATA_SORT.'='.urlencode( $sort_php )),
						 (kAPI_PAGE_START.'='.'0'),
						 (kAPI_PAGE_LIMIT.'='.'3'),
						 (kAPI_REQ_STAMP.'='.gettimeofday( true )),
						 (kAPI_OPT_LOG_REQUEST.'='.'1') );
		$request = $url.'?'.implode( '&', $params );
		$response = file_get_contents( $request );
		$decoded = unserialize( $response );
		//
		// Display.
		//
		echo( kSTYLE_TABLE_PRE );
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
		
		echo( '<h4>kAPI_OP_GET_ONE in PHP</h4>' );
		//
		// Do kAPI_OP_GET_ONE.
		//
		$params = array( (kAPI_FORMAT.'='.kDATA_TYPE_PHP),
						 (kAPI_OPERATION.'='.kAPI_OP_GET_ONE),
						 (kAPI_DATABASE.'='.'ONTOLOGY'),
						 (kAPI_CONTAINER.'='.'VOCABULARY'),
						 (kAPI_DATA_QUERY.'='.urlencode( $query_php )),
						 (kAPI_DATA_FIELD.'='.urlencode( $fields_php )),
						 (kAPI_DATA_SORT.'='.urlencode( $sort_php )),
						 (kAPI_PAGE_START.'='.'0'),
						 (kAPI_PAGE_LIMIT.'='.'3'),
						 (kAPI_REQ_STAMP.'='.gettimeofday( true )),
						 (kAPI_OPT_LOG_REQUEST.'='.'1') );
		$request = $url.'?'.implode( '&', $params );
		$response = file_get_contents( $request );
		$decoded = unserialize( $response );
		//
		// Display.
		//
		echo( kSTYLE_TABLE_PRE );
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
		
		echo( '<h4>kAPI_OP_GET in PHP</h4>' );
		//
		// Do kAPI_OP_GET.
		//
		$params = array( (kAPI_FORMAT.'='.kDATA_TYPE_PHP),
						 (kAPI_OPERATION.'='.kAPI_OP_GET),
						 (kAPI_DATABASE.'='.'ONTOLOGY'),
						 (kAPI_CONTAINER.'='.'VOCABULARY'),
						 (kAPI_DATA_QUERY.'='.urlencode( $query_php )),
						 (kAPI_DATA_FIELD.'='.urlencode( $fields_php )),
						 (kAPI_DATA_SORT.'='.urlencode( $sort_php )),
						 (kAPI_PAGE_START.'='.'0'),
						 (kAPI_PAGE_LIMIT.'='.'3'),
						 (kAPI_REQ_STAMP.'='.gettimeofday( true )),
						 (kAPI_OPT_LOG_REQUEST.'='.'1') );
		$request = $url.'?'.implode( '&', $params );
		$response = file_get_contents( $request );
		$decoded = unserialize( $response );
		//
		// Display.
		//
		echo( kSTYLE_TABLE_PRE );
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
	
	} // Iterating queries.
	
	echo( '<h4>kAPI_OP_SET in PHP</h4>' );
	//
	// Test SET in PHP.
	//
	$options = array( kAPI_OPT_SAFE => TRUE );
	$object = array( 'Pippo' => array( 'Uno', 'Due', 'Tre' ), 'Pappa' => 123 );
	$object_php = serialize( $object );
	$options_php = serialize( $options );
	$params = array( (kAPI_FORMAT.'='.kDATA_TYPE_PHP),
					 (kAPI_OPERATION.'='.kAPI_OP_SET),
					 (kAPI_DATABASE.'='."TEST"),
					 (kAPI_CONTAINER.'='."CMongoDataWrapper"),
					 (kAPI_DATA_OBJECT.'='.urlencode( $object_php )),
					 (kAPI_DATA_OPTIONS.'='.urlencode( $options_php )),
					 (kAPI_REQ_STAMP.'='.gettimeofday( true )),
					 (kAPI_OPT_LOG_REQUEST.'='.'1') );
	$request = $url.'?'.implode( '&', $params );
	$response = file_get_contents( $request );
	$decoded = unserialize( $response );
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
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
	
	echo( '<h4>kAPI_OP_SET in JSON</h4>' );
	//
	// Test SET in JSON.
	//
	$options = array( kAPI_OPT_SAFE => TRUE );
	$object = array( 'Pippo' => array( 'Uno', 'Due', 'Tre' ), 'Pappa' => 123 );
	$object_json = json_encode( $object );
	$options_json = json_encode( $options );
	$params = array( (kAPI_FORMAT.'='.kDATA_TYPE_JSON),
					 (kAPI_OPERATION.'='.kAPI_OP_SET),
					 (kAPI_DATABASE.'='."TEST"),
					 (kAPI_CONTAINER.'='."CMongoDataWrapper"),
					 (kAPI_DATA_OBJECT.'='.urlencode( $object_json )),
					 (kAPI_DATA_OPTIONS.'='.urlencode( $options_json )),
					 (kAPI_REQ_STAMP.'='.gettimeofday( true )),
					 (kAPI_OPT_LOG_TRACE.'='.'1'),
					 (kAPI_OPT_LOG_REQUEST.'='.'1') );
	$request = $url.'?'.implode( '&', $params );
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
	echo( kSTYLE_HEAD_PRE.'Response:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.htmlspecialchars( $response ).kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Decoded:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $decoded ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_TABLE_POS );
	echo( '<hr>' );
	
	echo( '<h4>kAPI_OP_INSERT in PHP</h4>' );
	//
	// Test INSERT in PHP.
	//
	$object = array( 'Pippo' => array( 'Uno', 'Due', 'Tre' ), 'Pappa' => 123 );
	$object_php = serialize( $object );
	$params = array( (kAPI_FORMAT.'='.kDATA_TYPE_PHP),
					 (kAPI_OPERATION.'='.kAPI_OP_INSERT),
					 (kAPI_DATABASE.'='."TEST"),
					 (kAPI_CONTAINER.'='."CMongoDataWrapper"),
					 (kAPI_DATA_OBJECT.'='.urlencode( $object_php )),
					 (kAPI_DATA_OPTIONS.'='.urlencode( $options_php )),
					 (kAPI_REQ_STAMP.'='.gettimeofday( true )),
					 (kAPI_OPT_LOG_REQUEST.'='.'1') );
	$request = $url.'?'.implode( '&', $params );
	$response = file_get_contents( $request );
	$decoded = unserialize( $response );
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
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
	
	echo( '<h4>kAPI_OP_INSERT in JSON</h4>' );
	//
	// Test INSERT in JSON.
	//
	$object = array( 'Pippo' => array( 'Uno', 'Due', 'Tre' ), 'Pappa' => 123 );
	$object_json = json_encode( $object );
	$params = array( (kAPI_FORMAT.'='.kDATA_TYPE_JSON),
					 (kAPI_OPERATION.'='.kAPI_OP_INSERT),
					 (kAPI_DATABASE.'='."TEST"),
					 (kAPI_CONTAINER.'='."CMongoDataWrapper"),
					 (kAPI_DATA_OBJECT.'='.urlencode( $object_json )),
					 (kAPI_DATA_OPTIONS.'='.urlencode( $options_json )),
					 (kAPI_REQ_STAMP.'='.gettimeofday( true )),
					 (kAPI_OPT_LOG_TRACE.'='.'1'),
					 (kAPI_OPT_LOG_REQUEST.'='.'1') );
	$request = $url.'?'.implode( '&', $params );
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
	echo( kSTYLE_HEAD_PRE.'Response:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.htmlspecialchars( $response ).kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Decoded:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $decoded ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_TABLE_POS );
	echo( '<hr>' );
	
	echo( '<h4>kAPI_OP_DEL in PHP</h4>' );
	//
	// Test DELETE in PHP.
	//
	$query = array
	(
		kOPERATOR_AND => array
		(
			0 => array
			(
				kAPI_QUERY_SUBJECT => 'Pappa',
				kAPI_QUERY_OPERATOR => kOPERATOR_EQUAL,
				kAPI_QUERY_TYPE => kDATA_TYPE_INT32,
				kAPI_QUERY_DATA => 123
			)
		)
	);
	$query_php = serialize( $query );
	$params = array( (kAPI_FORMAT.'='.kDATA_TYPE_PHP),
					 (kAPI_OPERATION.'='.kAPI_OP_DEL),
					 (kAPI_DATABASE.'='."TEST"),
					 (kAPI_CONTAINER.'='."CMongoDataWrapper"),
					 (kAPI_DATA_QUERY.'='.urlencode( $query_php )),
					 (kAPI_DATA_OPTIONS.'='.urlencode( $options_php )),
					 (kAPI_REQ_STAMP.'='.gettimeofday( true )),
					 (kAPI_OPT_LOG_REQUEST.'='.'1') );
	$request = $url.'?'.implode( '&', $params );
	$response = file_get_contents( $request );
	$decoded = unserialize( $response );
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
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
	
	echo( '<h4>kAPI_OP_DEL in JSON</h4>' );
	//
	// Test DELETE in JSON.
	//
	$query_json = json_encode( $query );
	$params = array( (kAPI_FORMAT.'='.kDATA_TYPE_JSON),
					 (kAPI_OPERATION.'='.kAPI_OP_DEL),
					 (kAPI_DATABASE.'='."TEST"),
					 (kAPI_CONTAINER.'='."CMongoDataWrapper"),
					 (kAPI_DATA_QUERY.'='.urlencode( $query_json )),
					 (kAPI_DATA_OPTIONS.'='.urlencode( $options_json )),
					 (kAPI_REQ_STAMP.'='.gettimeofday( true )),
					 (kAPI_OPT_LOG_REQUEST.'='.'1') );
	$request = $url.'?'.implode( '&', $params );
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
	echo( kSTYLE_HEAD_PRE.'Response:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.htmlspecialchars( $response ).kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Decoded:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $decoded ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_TABLE_POS );
	echo( '<hr>' );
	
	echo( '<h4>kAPI_OP_GET_OBJECT_REF in JSON</h4>' );
	//
	// Test GET REFERENCE in JSON.
	//
	$user_id = new MongoBinData( md5( 'Milko', TRUE ) );
	$object = MongoDBRef::create( 'USERS', $user_id );
	CDataType::SerialiseObject( $object );
	$object_json = json_encode( $object );
	$params = array( (kAPI_FORMAT.'='.kDATA_TYPE_JSON),
					 (kAPI_OPERATION.'='.kAPI_OP_GET_OBJECT_REF),
					 (kAPI_DATABASE.'='."TEST"),
					 (kAPI_DATA_OBJECT.'='.urlencode( $object_json )),
					 (kAPI_OPT_LOG_REQUEST.'='.'1') );
	$request = $url.'?'.implode( '&', $params );
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
