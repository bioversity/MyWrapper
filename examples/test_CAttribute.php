<?php

/**
 * {@link CAttribute.php Base} object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * base object {@link CAttribute class}.
 *
 *	@package	Test
 *	@subpackage	Framework
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 22/05/2012
 */

/*=======================================================================================
 *																						*
 *									test_CAttribute.php									*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."CAttribute.php" );


/*=======================================================================================
 *	TEST																				*
 *======================================================================================*/

//
// Test class.
//
try
{
	//
	// ManageOffset.
	//
	echo( '<h3>ManageOffset</h3>' );
	
	echo( '<i>$test = new ArrayObject();</i><br>' );
	$test = new ArrayObject();
	echo( '<i>$result = CAttribute::ManageOffset( $test, \'ManageOffset\', 3, FALSE );</i><br>' );
	$result = CAttribute::ManageOffset( $test, 'ManageOffset', 3, FALSE );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageOffset( $test, \'ManageOffset\', 4, TRUE );</i><br>' );
	$result = CAttribute::ManageOffset( $test, 'ManageOffset', 4, TRUE );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>$test = Array();</i><br>' );
	$test = Array();
	echo( '<i>$result = CAttribute::ManageOffset( $test, \'ManageOffset\', 3, FALSE );</i><br>' );
	$result = CAttribute::ManageOffset( $test, 'ManageOffset', 3, FALSE );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageOffset( $test, \'ManageOffset\', 4, TRUE );</i><br>' );
	$result = CAttribute::ManageOffset( $test, 'ManageOffset', 4, TRUE );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	echo( '<hr>' );

	//
	// ManageArrayOffset.
	//
	echo( '<h3>ManageArrayOffset</h3>' );
	
	echo( '<i>$test = new ArrayObject();</i><br>' );
	$test = new ArrayObject();
	echo( '<i>$result = CAttribute::ManageArrayOffset( $test, \'ManageArrayOffset\', 3, TRUE, FALSE );</i><br>' );
	$result = CAttribute::ManageArrayOffset( $test, 'ManageArrayOffset', 3, TRUE, FALSE );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageArrayOffset( $test, \'ManageArrayOffset\', 4, TRUE, TRUE );</i><br>' );
	$result = CAttribute::ManageArrayOffset( $test, 'ManageArrayOffset', 4, TRUE, TRUE );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageArrayOffset( $test, \'ManageArrayOffset\', array( 1, 2 ), TRUE );</i><br>' );
	$result = CAttribute::ManageArrayOffset( $test, 'ManageArrayOffset', array( 1, 2 ), TRUE );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageArrayOffset( $test, \'ManageArrayOffset\', 1, FALSE );</i><br>' );
	$result = CAttribute::ManageArrayOffset( $test, 'ManageArrayOffset', 1, FALSE );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageArrayOffset( $test, \'ManageArrayOffset\', 2, FALSE );</i><br>' );
	$result = CAttribute::ManageArrayOffset( $test, 'ManageArrayOffset', 2, FALSE );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageArrayOffset( $test, \'ManageArrayOffset\', array( 3, 4 ), FALSE );</i><br>' );
	$result = CAttribute::ManageArrayOffset( $test, 'ManageArrayOffset', array( 3, 4 ), FALSE );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>$test = Array();</i><br>' );
	$test = Array();
	echo( '<i>$result = CAttribute::ManageArrayOffset( $test, \'ManageArrayOffset\', 3, TRUE, FALSE );</i><br>' );
	$result = CAttribute::ManageArrayOffset( $test, 'ManageArrayOffset', 3, TRUE, FALSE );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageArrayOffset( $test, \'ManageArrayOffset\', 4, TRUE, TRUE );</i><br>' );
	$result = CAttribute::ManageArrayOffset( $test, 'ManageArrayOffset', 4, TRUE, TRUE );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageArrayOffset( $test, \'ManageArrayOffset\', array( 1, 2 ), TRUE );</i><br>' );
	$result = CAttribute::ManageArrayOffset( $test, 'ManageArrayOffset', array( 1, 2 ), TRUE );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageArrayOffset( $test, \'ManageArrayOffset\', 1, FALSE );</i><br>' );
	$result = CAttribute::ManageArrayOffset( $test, 'ManageArrayOffset', 1, FALSE );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageArrayOffset( $test, \'ManageArrayOffset\', 2, FALSE );</i><br>' );
	$result = CAttribute::ManageArrayOffset( $test, 'ManageArrayOffset', 2, FALSE );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageArrayOffset( $test, \'ManageArrayOffset\', array( 3, 4 ), FALSE );</i><br>' );
	$result = CAttribute::ManageArrayOffset( $test, 'ManageArrayOffset', array( 3, 4 ), FALSE );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	echo( '<hr>' );

	//
	// ManageTypedOffset.
	//
	echo( '<h3>ManageTypedOffset</h3>' );
	
	echo( '<i>$test = new ArrayObject();</i><br>' );
	$test = new ArrayObject();
	echo( '<i>$result = CAttribute::ManageTypedOffset( $test, \'ManageTypedOffset\', \'TYPE\', \'DATA\', \'type1\', \'data1\' );</i><br>' );
	$result = CAttribute::ManageTypedOffset( $test, 'ManageTypedOffset', 'TYPE', 'DATA', 'type1', 'data1' );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedOffset( $test, \'ManageTypedOffset\', \'TYPE\', \'DATA\', \'type2\', \'data2\' );</i><br>' );
	$result = CAttribute::ManageTypedOffset( $test, 'ManageTypedOffset', 'TYPE', 'DATA', 'type2', 'data2' );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedOffset( $test, \'ManageTypedOffset\', \'TYPE\', \'DATA\', NULL, \'data3\' );</i><br>' );
	$result = CAttribute::ManageTypedOffset( $test, 'ManageTypedOffset', 'TYPE', 'DATA', NULL, 'data3' );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedOffset( $test, \'ManageTypedOffset\', \'TYPE\', \'DATA\', NULL, \'NEW3\' );</i><br>' );
	$result = CAttribute::ManageTypedOffset( $test, 'ManageTypedOffset', 'TYPE', 'DATA', NULL, 'NEW3' );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedOffset( $test, \'ManageTypedOffset\', \'TYPE\', \'DATA\', NULL, FALSE );</i><br>' );
	$result = CAttribute::ManageTypedOffset( $test, 'ManageTypedOffset', 'TYPE', 'DATA', NULL, FALSE );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedOffset( $test, \'ManageTypedOffset\', \'TYPE\', \'DATA\', \'type1\', FALSE );</i><br>' );
	$result = CAttribute::ManageTypedOffset( $test, 'ManageTypedOffset', 'TYPE', 'DATA', 'type1', FALSE );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedOffset( $test, \'ManageTypedOffset\', \'TYPE\', \'DATA\', \'type2\', FALSE );</i><br>' );
	$result = CAttribute::ManageTypedOffset( $test, 'ManageTypedOffset', 'TYPE', 'DATA', 'type2', FALSE );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedOffset( $test, \'ManageTypedOffset\', \'TYPE\', \'DATA\', array( \'type1\', \'type2\' ), array( \'data1\', \'$data2\' ) );</i><br>' );
	$result = CAttribute::ManageTypedOffset( $test, 'ManageTypedOffset', 'TYPE', 'DATA', array( 'type1', 'type2' ), array( 'data1', '$data2' ) );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedOffset( $test, \'ManageTypedOffset\', \'TYPE\', \'DATA\', array( \'type1\', \'type2\' ), array( FALSE, FALSE ) );</i><br>' );
	$result = CAttribute::ManageTypedOffset( $test, 'ManageTypedOffset', 'TYPE', 'DATA', array( 'type1', 'type2' ), array( FALSE, FALSE ) );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>$test = Array();</i><br>' );
	$test = Array();
	echo( '<i>$result = CAttribute::ManageTypedOffset( $test, \'ManageTypedOffset\', \'TYPE\', \'DATA\', \'type1\', \'data1\' );</i><br>' );
	$result = CAttribute::ManageTypedOffset( $test, 'ManageTypedOffset', 'TYPE', 'DATA', 'type1', 'data1' );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedOffset( $test, \'ManageTypedOffset\', \'TYPE\', \'DATA\', \'type2\', \'data2\' );</i><br>' );
	$result = CAttribute::ManageTypedOffset( $test, 'ManageTypedOffset', 'TYPE', 'DATA', 'type2', 'data2' );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedOffset( $test, \'ManageTypedOffset\', \'TYPE\', \'DATA\', NULL, \'data3\' );</i><br>' );
	$result = CAttribute::ManageTypedOffset( $test, 'ManageTypedOffset', 'TYPE', 'DATA', NULL, 'data3' );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedOffset( $test, \'ManageTypedOffset\', \'TYPE\', \'DATA\', NULL, \'NEW3\' );</i><br>' );
	$result = CAttribute::ManageTypedOffset( $test, 'ManageTypedOffset', 'TYPE', 'DATA', NULL, 'NEW3' );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedOffset( $test, \'ManageTypedOffset\', \'TYPE\', \'DATA\', NULL, FALSE );</i><br>' );
	$result = CAttribute::ManageTypedOffset( $test, 'ManageTypedOffset', 'TYPE', 'DATA', NULL, FALSE );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedOffset( $test, \'ManageTypedOffset\', \'TYPE\', \'DATA\', \'type1\', FALSE );</i><br>' );
	$result = CAttribute::ManageTypedOffset( $test, 'ManageTypedOffset', 'TYPE', 'DATA', 'type1', FALSE );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedOffset( $test, \'ManageTypedOffset\', \'TYPE\', \'DATA\', \'type2\', FALSE );</i><br>' );
	$result = CAttribute::ManageTypedOffset( $test, 'ManageTypedOffset', 'TYPE', 'DATA', 'type2', FALSE );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedOffset( $test, \'ManageTypedOffset\', \'TYPE\', \'DATA\', array( \'type1\', \'type2\' ), array( \'data1\', \'$data2\' ) );</i><br>' );
	$result = CAttribute::ManageTypedOffset( $test, 'ManageTypedOffset', 'TYPE', 'DATA', array( 'type1', 'type2' ), array( 'data1', '$data2' ) );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedOffset( $test, \'ManageTypedOffset\', \'TYPE\', \'DATA\', array( \'type1\', \'type2\' ), array( FALSE, FALSE ) );</i><br>' );
	$result = CAttribute::ManageTypedOffset( $test, 'ManageTypedOffset', 'TYPE', 'DATA', array( 'type1', 'type2' ), array( FALSE, FALSE ) );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	echo( '<hr>' );

	//
	// ManageTypedArrayOffset.
	//
	echo( '<h3>ManageTypedArrayOffset</h3>' );
	
	echo( '<i>$test = new ArrayObject();</i><br>' );
	$test = new ArrayObject();
	echo( '<i>$result = CAttribute::ManageTypedArrayOffset( $test, \'ManageTypedArrayOffset\', \'TYPE\', \'DATA\', \'type1\', \'data1\', TRUE );</i><br>' );
	$result = CAttribute::ManageTypedArrayOffset( $test, 'ManageTypedArrayOffset', 'TYPE', 'DATA', 'type1', 'data1', TRUE );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedArrayOffset( $test, \'ManageTypedArrayOffset\', \'TYPE\', \'DATA\', \'type2\', \'data1\', TRUE );</i><br>' );
	$result = CAttribute::ManageTypedArrayOffset( $test, 'ManageTypedArrayOffset', 'TYPE', 'DATA', 'type2', 'data1', TRUE );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedArrayOffset( $test, \'ManageTypedArrayOffset\', \'TYPE\', \'DATA\', \'type2\', \'data2\', TRUE );</i><br>' );
	$result = CAttribute::ManageTypedArrayOffset( $test, 'ManageTypedArrayOffset', 'TYPE', 'DATA', 'type2', 'data2', TRUE );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedArrayOffset( $test, \'ManageTypedArrayOffset\', \'TYPE\', \'DATA\', \'type3\', array( 1, 2, 3 ), TRUE );</i><br>' );
	$result = CAttribute::ManageTypedArrayOffset( $test, 'ManageTypedArrayOffset', 'TYPE', 'DATA', 'type3', array( 1, 2, 3 ), TRUE );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedArrayOffset( $test, \'ManageTypedArrayOffset\', \'TYPE\', \'DATA\', \'type3\', 1 );</i><br>' );
	$result = CAttribute::ManageTypedArrayOffset( $test, 'ManageTypedArrayOffset', 'TYPE', 'DATA', 'type3', 1 );
	echo( "<pre>" ); print_r( $result ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedArrayOffset( $test, \'ManageTypedArrayOffset\', \'TYPE\', \'DATA\', \'type3\' );</i><br>' );
	$result = CAttribute::ManageTypedArrayOffset( $test, 'ManageTypedArrayOffset', 'TYPE', 'DATA', 'type3' );
	echo( "<pre>" ); print_r( $result ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedArrayOffset( $test, \'ManageTypedArrayOffset\', \'TYPE\', \'DATA\', \'type3\', NULL, FALSE );</i><br>' );
	$result = CAttribute::ManageTypedArrayOffset( $test, 'ManageTypedArrayOffset', 'TYPE', 'DATA', 'type3', NULL, FALSE );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedArrayOffset( $test, \'ManageTypedArrayOffset\', \'TYPE\', \'DATA\', \'type1\', \'data1\', FALSE );</i><br>' );
	$result = CAttribute::ManageTypedArrayOffset( $test, 'ManageTypedArrayOffset', 'TYPE', 'DATA', 'type1', 'data1', FALSE );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedArrayOffset( $test, \'ManageTypedArrayOffset\', \'TYPE\', \'DATA\', \'type2\', \'data1\', FALSE );</i><br>' );
	$result = CAttribute::ManageTypedArrayOffset( $test, 'ManageTypedArrayOffset', 'TYPE', 'DATA', 'type2', 'data1', FALSE );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedArrayOffset( $test, \'ManageTypedArrayOffset\', \'TYPE\', \'DATA\', \'type2\', \'data2\', FALSE );</i><br>' );
	$result = CAttribute::ManageTypedArrayOffset( $test, 'ManageTypedArrayOffset', 'TYPE', 'DATA', 'type2', 'data2', FALSE );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>$test = Array();</i><br>' );
	$test = Array();
	echo( '<hr>' );
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
