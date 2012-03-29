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
$object1[ kTAG_ID_NATIVE ] = 'Škofič';
 
//
// Create object 2.
//
$object2 = Array();
$object2[ 'Name' ] = 'Luca';
$object2[ 'Surname' ] = 'Matteis';
$object2[ 'Date' ] = new CDataTypeStamp( new MongoDate( strtotime( "2012-01-01 00:00:00" ) ) );
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
	 
	echo( '<h4>List operations</h4>' );
	//
	// List operations.
	//
	$params = array
	(
		(kAPI_FORMAT.'='.kDATA_TYPE_JSON),			// Format.
		(kAPI_OPERATION.'='.kAPI_OP_LIST_OP)		// Command.
	);
	$request = $url.'?'.implode( '&', $params );
	$response = file_get_contents( $request );
	$decoded = json_decode( $response, TRUE );
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
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
	echo( kSTYLE_DATA_PRE.$response.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Decoded:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $decoded ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_TABLE_POS );
	echo( '<hr>' );
	
	echo( '<h4>Empty request</h4>' );
	echo( '<i>Note that an empty request will return nothing.</i><br>' );
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
	echo( '<i>Note that we return nothing if no operation is provided.</i><br>' );
	//
	// Missing operation.
	//
	$params = array
	(
		(kAPI_FORMAT.'='.kDATA_TYPE_PHP),			// Format.
		(kAPI_REQ_STAMP.'='.gettimeofday( true )),	// Time-stamp.
		(kAPI_OPT_LOG_REQUEST.'='.'1')				// Log request.
	);
	$request = $url.'?'.implode( '&', $params );
	$response = file_get_contents( $request );
	$decoded = unserialize( $response );
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
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
	
	echo( '<h4>Invalid operation</h4>' );
	echo( '<i>Note the status.</i><br>' );
	//
	// Invalid operation.
	//
	$params = array
	(
		(kAPI_FORMAT.'='.kDATA_TYPE_PHP),			// Format.
		(kAPI_OPERATION.'='.'XXX'),					// Command.
		(kAPI_REQ_STAMP.'='.gettimeofday( true )),	// Time-stamp.
		(kAPI_OPT_LOG_REQUEST.'='.'1')				// Log request.
	);
	$request = $url.'?'.implode( '&', $params );
	$response = file_get_contents( $request );
	$decoded = unserialize( $response );
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
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
	
	echo( '<h4>Ping wrapper in PHP</h4>' );
	echo( '<i>Check out the response encoding.</i><br>' );
	//
	// Ping wrapper in PHP.
	//
	$params = array
	(
		(kAPI_FORMAT.'='.kDATA_TYPE_PHP),			// Format.
		(kAPI_OPERATION.'='.kAPI_OP_PING),			// Command.
		(kAPI_REQ_STAMP.'='.gettimeofday( true )),	// Time-stamp.
		(kAPI_OPT_LOG_REQUEST.'='.'1')				// Log request.
	);
	$request = $url.'?'.implode( '&', $params );
	$response = file_get_contents( $request );
	$decoded = unserialize( $response );
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
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
	
	echo( '<h4>Ping wrapper in JSON</h4>' );
	echo( '<i>Check out the response encoding.</i><br>' );
	//
	// Ping wrapper in JSON.
	//
	$params = array
	(
		(kAPI_FORMAT.'='.kDATA_TYPE_JSON),			// Format.
		(kAPI_OPERATION.'='.kAPI_OP_PING),			// Command.
		(kAPI_REQ_STAMP.'='.gettimeofday( true )),	// Time-stamp.
		(kAPI_OPT_LOG_REQUEST.'='.'1')				// Log request.
	);
	$request = $url.'?'.implode( '&', $params );
	$response = file_get_contents( $request );
	$decoded = json_decode( $response, TRUE );
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
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
	
	echo( '<h4>Debug wrapper</h4>' );
	echo( '<i>Not really sure for what purpose we can use this...</i><br>' );
	//
	// Debug wrapper.
	//
	$params = array
	(
		(kAPI_FORMAT.'='.kDATA_TYPE_JSON),			// Format.
		(kAPI_OPERATION.'='.kAPI_OP_DEBUG),			// Command.
		(kAPI_REQ_STAMP.'='.gettimeofday( true )),	// Time-stamp.
		(kAPI_OPT_LOG_REQUEST.'='.'1')				// Log request.
	);
	$request = $url.'?'.implode( '&', $params );
	$response = file_get_contents( $request );
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
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
	echo( kSTYLE_DATA_PRE.$response.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_TABLE_POS );
	echo( '<hr>' );
	echo( '<hr>' );
	
	echo( '<h4>Insert object</h4>' );
	echo( '<i>The last <b>Found</b> section shows what we inserted '
		 .'in the database.</i><br>' );
	//
	// Insert.
	//
	$object = json_encode( $object1 );
	$params = array
	(
		(kAPI_FORMAT.'='.kDATA_TYPE_JSON),					// Format.
		(kAPI_OPERATION.'='.kAPI_OP_INSERT),				// Command.
		(kAPI_DATABASE.'='.'TEST'),							// Database.
		(kAPI_CONTAINER.'='.'CMongoDataWrapper'),			// Container.
		(kAPI_DATA_OBJECT.'='.urlencode( $object )),		// Object.
		(kAPI_OPT_LOG_REQUEST.'='.'1')						// Log request.
	);
	$request = $url.'?'.implode( '&', $params );
	$response = file_get_contents( $request );
	$decoded = json_decode( $response, TRUE );
	$object = $decoded[ kAPI_DATA_RESPONSE ];
	//
	// Saving reference for testing.
	//
	$reference = Array();
	$container->UnserialiseObject( $object );				// To feed to MongoDBRef.
	$reference[ kTAG_ID_REFERENCE ] = $object[ kTAG_ID_NATIVE ];
	$reference[ kTAG_CONTAINER_REFERENCE ] = 'CMongoDataWrapper';
	$reference[ kTAG_DATABASE_REFERENCE ] = 'TEST';
	$found = MongoDBRef::get( $db, $reference );
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
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
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Found:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $found ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_TABLE_POS );
	echo( '<hr>' );
	
	echo( '<h4>Set object</h4>' );
	echo( '<i>The last <b>Found</b> section shows what we set '
		 .'in the database.</i><br>' );
	//
	// Set.
	//
	$object = json_encode( $object2 );
	$params = array
	(
		(kAPI_FORMAT.'='.kDATA_TYPE_JSON),					// Format.
		(kAPI_OPERATION.'='.kAPI_OP_SET),					// Command.
		(kAPI_DATABASE.'='.'TEST'),							// Database.
		(kAPI_CONTAINER.'='.'CMongoDataWrapper'),			// Container.
		(kAPI_DATA_OBJECT.'='.urlencode( $object )),		// Object.
		(kAPI_OPT_LOG_REQUEST.'='.'1')						// Log request.
	);
	$request = $url.'?'.implode( '&', $params );
	$response = file_get_contents( $request );
	$decoded = json_decode( $response, TRUE );
	$object = $decoded[ kAPI_DATA_RESPONSE ];
	//
	// Saving reference for testing.
	//
	$reference = Array();
	$container->UnserialiseObject( $object );				// To feed to MongoDBRef.
	$reference[ kTAG_ID_REFERENCE ] = $object[ kTAG_ID_NATIVE ];
	$reference[ kTAG_CONTAINER_REFERENCE ] = 'CMongoDataWrapper';
	$reference[ kTAG_DATABASE_REFERENCE ] = 'TEST';
	$found = MongoDBRef::get( $db, $reference );
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
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
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Found:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $found ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_TABLE_POS );
	echo( '<hr>' );
	
	echo( '<h4>Batch insert</h4>' );
	echo( '<i>The last <b>Found</b> section shows what we set '
		 .'in the database.</i><br>' );
	//
	// Batch insert.
	//
	$object = array( $object3, $object4, $object5 );
	$object = json_encode( $object );
	$params = array
	(
		(kAPI_FORMAT.'='.kDATA_TYPE_JSON),					// Format.
		(kAPI_OPERATION.'='.kAPI_OP_BATCH_INSERT),			// Command.
		(kAPI_DATABASE.'='.'TEST'),							// Database.
		(kAPI_CONTAINER.'='.'CMongoDataWrapper'),			// Container.
		(kAPI_DATA_OBJECT.'='.urlencode( $object )),		// Object.
		(kAPI_OPT_LOG_REQUEST.'='.'1')						// Log request.
	);
	$request = $url.'?'.implode( '&', $params );
	$response = file_get_contents( $request );
	$decoded = json_decode( $response, TRUE );
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
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
	
	echo( '<h4>Reference object with storage</h4>' );
	echo( '<i>Note that we include the database and container references '
		 .'in the object reference.</i><br>' );
	//
	// Reference.
	//
	$object = json_encode( $reference );
	$params = array
	(
		(kAPI_FORMAT.'='.kDATA_TYPE_JSON),					// Format.
		(kAPI_OPERATION.'='.kAPI_OP_GET_OBJECT_REF),		// Command.
		(kAPI_DATA_OBJECT.'='.urlencode( $object )),		// Object.
		(kAPI_OPT_LOG_REQUEST.'='.'1')						// Log request.
	);
	$request = $url.'?'.implode( '&', $params );
	$response = file_get_contents( $request );
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
	
	echo( '<h4>Reference object without storage</h4>' );
	echo( '<i>Note that we use the object as reference and provide both the database '
		 .'and container references among the parameters.</i><br>' );
	//
	// Reference.
	//
	$reference = $decoded[ kAPI_DATA_RESPONSE ];
	$object = json_encode( $reference );
	$params = array
	(
		(kAPI_FORMAT.'='.kDATA_TYPE_JSON),					// Format.
		(kAPI_OPERATION.'='.kAPI_OP_GET_OBJECT_REF),		// Command.
		(kAPI_DATABASE.'='.'TEST'),							// Database.
		(kAPI_CONTAINER.'='.'CMongoDataWrapper'),			// Container.
		(kAPI_DATA_OBJECT.'='.urlencode( $object )),		// Object.
		(kAPI_OPT_LOG_REQUEST.'='.'1')						// Log request.
	);
	$request = $url.'?'.implode( '&', $params );
	$response = file_get_contents( $request );
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
