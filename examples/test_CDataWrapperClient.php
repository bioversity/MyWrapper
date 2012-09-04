<?php
	
/**
 * {@link CDataWrapperClient.php Data} wrapper object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * base object {@link CDataWrapperClient class}.
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
 *								test_CDataWrapperClient.php								*
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
require_once( kPATH_LIBRARY_SOURCE."CDataWrapperClient.php" );


/*=======================================================================================
 *	TEST WRAPPER OBJECT																	*
 *======================================================================================*/
 
//
// Init local storage.
//
$url = 'http://localhost/wrapper/DataWrapper.php';

//
// TRY BLOCK.
//
try
{
	echo( '<h4>Instantiate</h4>' );
	//
	// Build object.
	//
	$test = new CDataWrapperClient( $url );
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'URL:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.htmlspecialchars( $url ).kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Client:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $test ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_TABLE_POS );
	echo( '<hr>' );
	echo( '<hr>' );
	
	echo( '<h4>Test static method</h4>' );
	//
	// Ping wrapper.
	//
	$params = array( kAPI_FORMAT => kTYPE_JSON, kAPI_OPERATION => kAPI_OP_PING );
	echo( '<i>$decoded = CDataWrapperClient::Request( $url, $params, \'POST\', kTYPE_JSON );</i><br>' );
	$decoded = CDataWrapperClient::Request( $url, $params, 'POST', kTYPE_JSON );
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
	
	echo( '<h4>Test public method</h4>' );
	//
	// Instantiate.
	//
	echo( '<i>$test = new CDataWrapperClient();</i><br>' );
	$test = new CDataWrapperClient();
	echo( '<i>$test->Url( $url );</i><br>' );
	$test->Url( $url );
	echo( '<i>$test->Operation( kAPI_OP_PING );</i><br>' );
	$test->Operation( kAPI_OP_PING );
	echo( '<i>$test->Format( kTYPE_JSON );</i><br>' );
	$test->Format( kTYPE_JSON );
	echo( '<i>$decoded = $test->Execute( \'GET\' );</i><br>' );
	$decoded = $test->Execute( 'GET' );
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Client:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $test ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Decoded:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $decoded ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_TABLE_POS );
	echo( '<hr>' );
	
	echo( '<h4>Test help in POST</h4>' );
	//
	// Instantiate.
	//
	echo( '<i>$test = new CDataWrapperClient();</i><br>' );
	$test = new CDataWrapperClient();
	echo( '<i>$test->Url( $url );</i><br>' );
	$test->Url( $url );
	echo( '<i>$test->Operation( kAPI_OP_HELP );</i><br>' );
	$test->Operation( kAPI_OP_HELP );
	echo( '<i>$test->Format( kTYPE_PHP );</i><br>' );
	$test->Format( kTYPE_PHP );
	echo( '<i>$decoded = $test->Execute( \'POST\' );</i><br>' );
	$decoded = $test->Execute( 'POST' );
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Client:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $test ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Decoded:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $decoded ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_TABLE_POS );
	echo( '<hr>' );
	
	echo( '<h4>Test ping with timers</h4>' );
	//
	// Instantiate.
	//
	echo( '<i>$test = new CDataWrapperClient();</i><br>' );
	$test = new CDataWrapperClient();
	echo( '<i>$test->Url( $url );</i><br>' );
	$test->Url( $url );
	echo( '<i>$test->Operation( kAPI_OP_PING );</i><br>' );
	$test->Operation( kAPI_OP_PING );
	echo( '<i>$test->Format( kTYPE_JSON );</i><br>' );
	$test->Format( kTYPE_JSON );
	echo( '<i>$test->Stamp( TRUE );</i><br>' );
	$test->Stamp( TRUE );
	echo( '<i>$decoded = $test->Execute( \'POST\' );</i><br>' );
	$decoded = $test->Execute( 'POST' );
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Client:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $test ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Decoded:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $decoded ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_TABLE_POS );
	echo( '<hr>' );
	
	echo( '<h4>Test invalid operation</h4>' );
	//
	// Invalid operation.
	//
	try
	{
		echo( '<i>$test->Operation( 20 );</i><br>' );
		$test->Operation( 20 );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );
	}
	echo( '<hr>' );
	
	echo( '<h4>Test invalid format</h4>' );
	//
	// Invalid format.
	//
	try
	{
		echo( '<i>$test->Format( 20 );</i><br>' );
		$test->Format( 20 );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );
	}
	echo( '<hr>' );
	
	echo( '<h4>Test missing element</h4>' );
	//
	// Missing element.
	//
	try
	{
		echo( '<i>$test->Url( FALSE );</i><br>' );
		$test->Url( FALSE );
		//
		// Display.
		//
		echo( kSTYLE_TABLE_PRE );
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'Client:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $test ); echo( '</pre>'.kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
		echo( kSTYLE_TABLE_POS );
		//
		// Execute.
		//
		echo( '<i>$decoded = $test->Execute();</i><br>' );
		$decoded = $test->Execute();
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );
	}
	echo( '<hr>' );
	
	echo( '<h4>Test client debug</h4>' );
	//
	// Client debug.
	//
	try
	{
		echo( '<i>$test->Url( $url );</i><br>' );
		$test->Url( $url );
		//
		// Execute.
		//
		echo( '<i>$test->Format( kTYPE_META );</i><br>' );
		$test->Format( kTYPE_META );
		//
		// Execute.
		//
		echo( '<i>$decoded = $test->Execute();</i><br>' );
		$decoded = $test->Execute();
		//
		// Display.
		//
		echo( kSTYLE_TABLE_PRE );
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'Client:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $test ); echo( '</pre>'.kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'Response:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $decoded ); echo( '</pre>'.kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
		echo( kSTYLE_TABLE_POS );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );
	}
	echo( '<hr>' );
	
	echo( '<h4>Test properties</h4>' );
	//
	// Properties.
	//
	try
	{
		echo( '<i>$test->Format( kTYPE_JSON );</i><br>' );
		$test->Format( kTYPE_JSON );
		echo( '<i>$test->Operation( kAPI_OP_COUNT );</i><br>' );
		$test->Operation( kAPI_OP_COUNT );
		echo( '<i>$test->Database( \'Database\' );</i><br>' );
		$test->Database( 'Database' );
		echo( '<i>$test->Container( \'Container\' );</i><br>' );
		$test->Container( 'Container' );
		echo( '<i>$test->Start( 0 );</i><br>' );
		$test->Start( 0 );
		echo( '<i>$test->Limit( 25 );</i><br>' );
		$test->Limit( 25 );
		$query = array
		(
			kOPERATOR_OR => array
			(
				array
				(
					kAPI_QUERY_SUBJECT => ':XREF.:DATA._code',
					kAPI_QUERY_OPERATOR => kOPERATOR_PREFIX,
					kAPI_QUERY_TYPE => kTYPE_STRING,
					kAPI_QUERY_DATA => 'NCBI_taxid:'
				),
				array
				(
					kAPI_QUERY_SUBJECT => ':XREF.:DATA._code',
					kAPI_QUERY_OPERATOR => kOPERATOR_PREFIX,
					kAPI_QUERY_TYPE => kTYPE_STRING,
					kAPI_QUERY_DATA =>  'GR:'
				)
			)
		);
		$statement = array
		(
			kAPI_QUERY_SUBJECT => ':XREF',
			kAPI_QUERY_OPERATOR => kOPERATOR_NOT_NULL
		);
		$query = new CQuery( $query );
		$query->AppendStatement( $statement );
		echo( '<i>$test->Query( $query );</i><br>' );
		$test->Query( $query );
		$fields = array( 'field1', 'field2', 'field3' );
		echo( '<i>$test->Fields( $fields );</i><br>' );
		$test->Fields( $fields );
		echo( '<i>$test->Sort( $fields );</i><br>' );
		$test->Sort( $fields );
		echo( '<i>$test->Object( $fields );</i><br>' );
		$test->Object( $fields );
		echo( '<i>$test->Options( kAPI_OPT_SAFE, TRUE );</i><br>' );
		$test->Options( kAPI_OPT_SAFE, TRUE );
		echo( '<i>$test->Options( kAPI_OPT_TIMEOUT, 300 );</i><br>' );
		$test->Options( kAPI_OPT_TIMEOUT, 300 );
		//
		// Execute.
		//
		echo( '<i>$decoded = $test->Execute();</i><br>' );
		$decoded = $test->Execute();
		//
		// Display.
		//
		echo( kSTYLE_TABLE_PRE );
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'Client:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $test ); echo( '</pre>'.kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
		echo( kSTYLE_ROW_PRE );
		echo( kSTYLE_HEAD_PRE.'Response:'.kSTYLE_HEAD_POS );
		echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $decoded ); echo( '</pre>'.kSTYLE_DATA_POS );
		echo( kSTYLE_ROW_POS );
		echo( kSTYLE_TABLE_POS );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );
	}
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
