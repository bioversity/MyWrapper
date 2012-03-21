<?php
	
/**
 * {@link CDataWrapper.php Data} wrapper object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * base object {@link CDataWrapper class}.
 *
 *	@package	Test
 *	@subpackage	Wrappers
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 22/12/2011
 *				2.00 22/02/2012
 */

/*=======================================================================================
 *																						*
 *									test_CDataWrapper.php								*
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
require_once( kPATH_LIBRARY_SOURCE."CDataWrapper.php" );


/*=======================================================================================
 *	TEST WRAPPER OBJECT																	*
 *======================================================================================*/
 
//
// Init local storage.
//
$url = 'http://localhost/newwrapper/DataWrapper.php';

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
	
	//
	// Instantiate container.
	//
	$container = new CMongoContainer( $collection );
	
	//
	// Get test object.
	//
	$object_serialised = $object = $collection->findOne( array( ':GID' => 'PATO:0001766' ) );
	CDataType::SerialiseObject( $object_serialised );
	
	//
	// Convert object.
	//
	$object_json = json_encode( $object_serialised, TRUE );
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
	$decoded = json_decode( $response, TRUE );
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'URL:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.htmlspecialchars( $response ).kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Response:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.$response.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_TABLE_POS );
	echo( '<hr>' );
	
	echo( '<h4>Test decode object in PHP</h4>' );
	//
	// Test decode object in PHP.
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
	
	echo( '<h4>Test decode object in JSON</h4>' );
	//
	// Test decode object in JSON.
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
	
	echo( '<h4>Debug query in PHP</h4>' );
	//
	// Debug query in PHP.
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
	$object_php = serialize( $object );
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
	echo( kSTYLE_DATA_PRE.$response.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_TABLE_POS );
	echo( '<hr>' );
	
	echo( '<h4>Debug query in JSON</h4>' );
	//
	// Debug query in JSON.
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
	$object_json = json_encode( $object );
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
	echo( kSTYLE_DATA_PRE.$response.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_TABLE_POS );
	echo( '<hr>' );
	
	echo( '<h4>Invalid operation</h4>' );
	//
	// Invalid operation.
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
	
	echo( '<h4>Missing operation</h4>' );
	//
	// Missing operation.
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
