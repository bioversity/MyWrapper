<?php

/**
 * {@link CDataType.php Base} object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * base object {@link CDataType class}.
 *
 *	@package	Test
 *	@subpackage	Framework
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 13/12/2012
 */

/*=======================================================================================
 *																						*
 *									test_CDataType.php									*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."CDataTypeInt32.php" );
require_once( kPATH_LIBRARY_SOURCE."CDataTypeInt64.php" );
require_once( kPATH_LIBRARY_SOURCE."CDataTypeStamp.php" );
require_once( kPATH_LIBRARY_SOURCE."CDataTypeBinary.php" );
require_once( kPATH_LIBRARY_SOURCE."CDataTypeMongoId.php" );
require_once( kPATH_LIBRARY_SOURCE."CDataTypeMongoCode.php" );
require_once( kPATH_LIBRARY_SOURCE."CDataTypeMongoRegex.php" );


/*=======================================================================================
 *	TEST DEFAULT EXCEPTIONS																*
 *======================================================================================*/
 
//
// Test class.
//
try
{
	//
	// Test CDataTypeInt32.
	//
	echo( '<h3>CDataTypeInt32</h3>' );
	
	echo( '<i>$test = new CDataTypeInt32( 12 );</i>' );
	$test = new CDataTypeInt32( 12 );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'String: ['.(string) $test.']<br>' );
	echo( gettype( $test->value() ).' ['.$test->value().']' );
	echo( '<hr>' );
	
	echo( '<i>$test = new CDataTypeInt32( \'12\' );</i>' );
	$test = new CDataTypeInt32( '12' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'String: ['.(string) $test.']<br>' );
	echo( gettype( $test->value() ).' ['.$test->value().']' );
	echo( '<hr>' );
	
	try
	{
		echo( '<i>$test = new CDataTypeInt32( \'pippo\' );</i>' );
		$test = new CDataTypeInt32( 'pippo' );
		echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
		echo( 'String: ['.(string) $test.']<br>' );
		echo( gettype( $test->value() ).' ['.$test->value().']' );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );
		echo( '<br>' );
	}
	echo( '<hr>' );
	
	//
	// Test CDataTypeInt64.
	//
	echo( '<h3>CDataTypeInt64</h3>' );
	
	echo( '<i>$test = new CDataTypeInt64( 123456789123465789 );</i>' );
	$test = new CDataTypeInt64( 123456789123465789 );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'String: ['.(string) $test.']<br>' );
	echo( gettype( $test->value() ).' ['.$test->value().']' );
	echo( '<hr>' );
	
	echo( '<i>$test = new CDataTypeInt64( \'123456789123465789\' );</i>' );
	$test = new CDataTypeInt64( '123456789123465789' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'String: ['.(string) $test.']<br>' );
	echo( gettype( $test->value() ).' ['.$test->value().']' );
	echo( '<hr>' );
	
	try
	{
		echo( '<i>$test = new CDataTypeInt64( \'pippo\' );</i>' );
		$test = new CDataTypeInt64( 'pippo' );
		echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
		echo( 'String: ['.(string) $test.']<br>' );
		echo( gettype( $test->value() ).' ['.$test->value().']' );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );
		echo( '<br>' );
	}
	echo( '<hr>' );
	
	//
	// Test CDataTypeStamp.
	//
	echo( '<h3>CDataTypeStamp</h3>' );
	
	echo( '<i>$test = new CDataTypeStamp();</i>' );
	$test = new CDataTypeStamp();
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'String: ['.(string) $test.']<br>' );
	echo( 'Value: '.gettype( $test->value() ).' ['.$test->value().']' );
	echo( '<hr>' );
	
	echo( '<i>$test = new CDataTypeStamp( \'2001-12-12 14:30\' );</i>' );
	$test = new CDataTypeStamp( '2001-12-12 14:30'  );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'String: ['.(string) $test.']<br>' );
	echo( 'Value: '.gettype( $test->value() ).' ['.$test->value().']' );
	echo( '<hr>' );
	
	echo( '<i>$test = new CDataTypeStamp( \'2001-12-12 14:30:12\' );</i>' );
	$test = new CDataTypeStamp( '2001-12-12 14:30:12'  );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'String: ['.(string) $test.']<br>' );
	echo( 'Value: '.gettype( $test->value() ).' ['.$test->value().']' );
	echo( '<hr>' );
	
	echo( '<i>$test = new CDataTypeStamp( 1332345983.801 );</i>' );
	$test = new CDataTypeStamp( 1332345983.801 );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'String: ['.(string) $test.']<br>' );
	echo( 'Value: '.gettype( $test->value() ).' ['.$test->value().']' );
	echo( '<hr>' );
	
	echo( '<i>$test = new CDataTypeStamp( 1332345983 );</i>' );
	$test = new CDataTypeStamp( 1332345983 );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'String: ['.(string) $test.']<br>' );
	echo( 'Value: '.gettype( $test->value() ).' ['.$test->value().']' );
	echo( '<hr>' );
	
	try
	{
		echo( '<i>$test = new CDataTypeStamp( \'pippo\' );</i>' );
		$test = new CDataTypeStamp( 'pippo'  );
		echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
		echo( 'String: ['.(string) $test.']<br>' );
		echo( 'Value: '.gettype( $test->value() ).' ['.$test->value().']' );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );
		echo( '<br>' );
	}
	echo( '<hr>' );
	
	//
	// Test CDataTypeBinary.
	//
	echo( '<h3>CDataTypeBinary</h3>' );
	
	echo( '<i>$test = new CDataTypeBinary( \'pippo\' );</i>' );
	$test = new CDataTypeBinary( 'pippo' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'String: ['.(string) $test.']<br>' );
	echo( 'Value: '.gettype( $test->value() ).' ['.$test->value().']' );
	echo( '<hr>' );
	
	echo( '<i>$test = new CDataTypeBinary( md5( \'pippo\', TRUE ) );</i>' );
	$test = new CDataTypeBinary( md5( 'pippo', TRUE ) );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'String: ['.(string) $test.']<br>' );
	echo( 'Value: '.gettype( $test->value() ).' ['.$test->value().']' );
	echo( '<hr>' );
	
	try
	{
		echo( '<i>$test = new CDataTypeBinary( array( 1 ) );</i>' );
		$test = new CDataTypeBinary( array( 1 ) );
		echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
		echo( 'String: ['.(string) $test.']<br>' );
		echo( 'Value: '.gettype( $test->value() ).' ['.$test->value().']' );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );
		echo( '<br>' );
	}
	echo( '<hr>' );
	
	//
	// Test CDataTypeMongoId.
	//
	echo( '<h3>CDataTypeMongoId</h3>' );
	
	echo( '<i>$test = new CDataTypeMongoId();</i>' );
	$test = new CDataTypeMongoId();
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'String: ['.(string) $test.']<br>' );
	echo( 'Value:<pre>' ); print_r( $test->value() ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$test = new CDataTypeMongoId( \'4f6a036b961be58704000004\' );</i>' );
	$test = new CDataTypeMongoId( '4f6a036b961be58704000004' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'String: ['.(string) $test.']<br>' );
	echo( 'Value:<pre>' ); print_r( $test->value() ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Test CDataTypeMongoRegex.
	//
	echo( '<h3>CDataTypeMongoRegex</h3>' );
	
	echo( '<i>$test = new CDataTypeMongoRegex( \'/^pippo/i\' );</i>' );
	$test = new CDataTypeMongoRegex( '/^pippo/i' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'String: ['.(string) $test.']<br>' );
	echo( 'Value:<pre>' ); print_r( $test->value() ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$test = new CDataTypeMongoRegex( \'pippo\' );</i>' );
	$test = new CDataTypeMongoRegex( 'pippo' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'String: ['.(string) $test.']<br>' );
	echo( 'Value:<pre>' ); print_r( $test->value() ); echo( '</pre>' );
	echo( '<hr>' );
}

//
// Catch exceptions.
//
catch( Exception $error )
{
	echo( CException::AsHTML( $error ) );
	echo( '<pre>'.(string) $error.'</pre>' );
	echo( '<hr>' );
}

echo( "Done!<br />" );

?>
