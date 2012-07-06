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
	echo( '<i>$result = CAttribute::ManageOffset( $test, \'ManageOffset\' );</i><br>' );
	$result = CAttribute::ManageOffset( $test, 'ManageOffset' );
	echo( "<pre>" ); print_r( $result ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageOffset( $test, \'ManageOffset\', FALSE );</i><br>' );
	$result = CAttribute::ManageOffset( $test, 'ManageOffset', FALSE );
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
	echo( '<i>$result = CAttribute::ManageOffset( $test, \'ManageOffset\' );</i><br>' );
	$result = CAttribute::ManageOffset( $test, 'ManageOffset' );
	echo( "<pre>" ); print_r( $result ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageOffset( $test, \'ManageOffset\', FALSE );</i><br>' );
	$result = CAttribute::ManageOffset( $test, 'ManageOffset', FALSE );
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
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageArrayOffset( $test, \'ManageArrayOffset\', 1, FALSE );</i><br>' );
	$result = CAttribute::ManageArrayOffset( $test, 'ManageArrayOffset', 1, FALSE );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageArrayOffset( $test, \'ManageArrayOffset\', 2, FALSE );</i><br>' );
	$result = CAttribute::ManageArrayOffset( $test, 'ManageArrayOffset', 2, FALSE );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageArrayOffset( $test, \'ManageArrayOffset\', array( 3, 4 ), FALSE, TRUE );</i><br>' );
	$result = CAttribute::ManageArrayOffset( $test, 'ManageArrayOffset', array( 3, 4 ), FALSE, TRUE );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
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
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageArrayOffset( $test, \'ManageArrayOffset\', 1, FALSE );</i><br>' );
	$result = CAttribute::ManageArrayOffset( $test, 'ManageArrayOffset', 1, FALSE );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageArrayOffset( $test, \'ManageArrayOffset\', 2, FALSE );</i><br>' );
	$result = CAttribute::ManageArrayOffset( $test, 'ManageArrayOffset', 2, FALSE );
	echo( "[$result] <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageArrayOffset( $test, \'ManageArrayOffset\', array( 3, 4 ), FALSE, TRUE );</i><br>' );
	$result = CAttribute::ManageArrayOffset( $test, 'ManageArrayOffset', array( 3, 4 ), FALSE, TRUE );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>$test = Array();</i><br>' );
	$test = Array();
	echo( '<i>$result = CAttribute::ManageArrayOffset( $test, \'ManageArrayOffset\', array( 1, 2, 3, 4 ), TRUE );</i><br>' );
	$result = CAttribute::ManageArrayOffset( $test, 'ManageArrayOffset', array( 1, 2, 3, 4 ), TRUE );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageArrayOffset( $test, \'ManageArrayOffset\', NULL );</i><br>' );
	$result = CAttribute::ManageArrayOffset( $test, 'ManageArrayOffset', NULL );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageArrayOffset( $test, \'ManageArrayOffset\', NULL, TRUE );</i><br>' );
	$result = CAttribute::ManageArrayOffset( $test, 'ManageArrayOffset', NULL, TRUE );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageArrayOffset( $test, \'ManageArrayOffset\', NULL, FALSE, TRUE );</i><br>' );
	$result = CAttribute::ManageArrayOffset( $test, 'ManageArrayOffset', NULL, FALSE, TRUE );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>$test = Array();</i><br>' );
	$test = Array();
	echo( '<i>$item1 = new ArrayObject( array( \'Key\' => 1, \'Value\' => \'One\' ) );</i><br>' );
	$item1 = new ArrayObject( array( 'Key' => 1, 'Value' => 'One' ) );
	echo( '<i>$item2 = new ArrayObject( array( \'Key\' => 2, \'Value\' => \'Two\' ) );</i><br>' );
	$item2 = new ArrayObject( array( 'Key' => 2, 'Value' => 'Two' ) );
	echo( '<i>$item3 = new ArrayObject( array( \'Value\' => \'Three\' ) );</i><br>' );
	$item3 = new ArrayObject( array( 'Value' => 'Three' ) );
	echo( '<i>$hasher = function( $item ){ return ( isset( $item[ \'Key\' ] ) ) ? $item[ \'Key\' ]: NULL; };</i><br>' );
	$hasher = function( $item ){ return ( isset( $item[ 'Key' ] ) ) ? $item[ 'Key' ] : NULL; };
	echo( '<i>$result = CAttribute::ManageArrayOffset( $test, \'ManageArrayOffset\', array( $item1, $item2, $item3 ), TRUE, FALSE, $hasher );</i><br>' );
	$result = CAttribute::ManageArrayOffset( $test, 'ManageArrayOffset', array( $item1, $item2, $item3 ), TRUE, FALSE, $hasher );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageArrayOffset( $test, \'ManageArrayOffset\', new ArrayObject( array( \'Key\' => 2 ) ), NULL, FALSE, $hasher );</i><br>' );
	$result = CAttribute::ManageArrayOffset( $test, 'ManageArrayOffset', new ArrayObject( array( 'Key' => 2 ) ), NULL, FALSE, $hasher );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageArrayOffset( $test, \'ManageArrayOffset\', new ArrayObject(), NULL, FALSE, $hasher );</i><br>' );
	$result = CAttribute::ManageArrayOffset( $test, 'ManageArrayOffset', new ArrayObject(), NULL, FALSE, $hasher );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageArrayOffset( $test, \'ManageArrayOffset\', new ArrayObject( array( \'PIPPO\' ) ), TRUE, FALSE, $hasher );</i><br>' );
	$result = CAttribute::ManageArrayOffset( $test, 'ManageArrayOffset', new ArrayObject( array( 'PIPPO' ) ), TRUE, FALSE, $hasher );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageArrayOffset( $test, \'ManageArrayOffset\', $item2, FALSE, TRUE, $hasher );</i><br>' );
	$result = CAttribute::ManageArrayOffset( $test, 'ManageArrayOffset', $item2, FALSE, TRUE, $hasher );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageArrayOffset( $test, \'ManageArrayOffset\', new ArrayObject( array( \'Key\' => 1 ) ), FALSE, TRUE, $hasher );</i><br>' );
	$result = CAttribute::ManageArrayOffset( $test, 'ManageArrayOffset', new ArrayObject( array( 'Key' => 1 ) ), FALSE, TRUE, $hasher );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageArrayOffset( $test, \'ManageArrayOffset\', NULL, FALSE, TRUE, $hasher );</i><br>' );
	$result = CAttribute::ManageArrayOffset( $test, 'ManageArrayOffset', NULL, FALSE, TRUE, $hasher );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	echo( '<hr>' );

	//
	// ManageTypedOffset.
	//
	echo( '<h3>ManageTypedOffset</h3>' );
	
	echo( '<i>$test = new ArrayObject();</i><br>' );
	$test = new ArrayObject();
	echo( '<i>$result = CAttribute::ManageTypedOffset( $test, \'ManageTypedOffset\', \'DATA\', \'TYPE\', array( \'type1\' ), \'data1\' );</i><br>' );
	$result = CAttribute::ManageTypedOffset( $test, 'ManageTypedOffset', 'DATA', 'TYPE', 'type1', 'data1' );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedOffset( $test, \'ManageTypedOffset\', \'DATA\', array( \'TYPE\' ), array( \'type2\' ), \'data2\' );</i><br>' );
	$result = CAttribute::ManageTypedOffset( $test, 'ManageTypedOffset', 'DATA', array( 'TYPE' ), array( 'type2' ), 'data2' );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedOffset( $test, \'ManageTypedOffset\', \'DATA\', \'TYPE\', array( NULL ), \'data3\' );</i><br>' );
	$result = CAttribute::ManageTypedOffset( $test, 'ManageTypedOffset', 'DATA', 'TYPE', array( NULL ), 'data3' );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedOffset( $test, \'ManageTypedOffset\', \'DATA\', \'TYPE\', NULL, \'NEW3\', TRUE );</i><br>' );
	$result = CAttribute::ManageTypedOffset( $test, 'ManageTypedOffset', 'DATA', 'TYPE', NULL, 'NEW3', TRUE );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedOffset( $test, \'ManageTypedOffset\',  \'DATA\', \'TYPE\',NULL, FALSE, TRUE );</i><br>' );
	$result = CAttribute::ManageTypedOffset( $test, 'ManageTypedOffset', 'DATA', 'TYPE', NULL, FALSE, TRUE );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedOffset( $test, \'ManageTypedOffset\', \'DATA\', \'TYPE\', \'type1\', FALSE, TRUE );</i><br>' );
	$result = CAttribute::ManageTypedOffset( $test, 'ManageTypedOffset', 'DATA', 'TYPE', 'type1', FALSE, TRUE );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedOffset( $test, \'ManageTypedOffset\', \'DATA\', \'TYPE\', \'type2\', FALSE, TRUE );</i><br>' );
	$result = CAttribute::ManageTypedOffset( $test, 'ManageTypedOffset', 'DATA', 'TYPE', 'type2', FALSE, TRUE );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedOffset( $test, \'ManageTypedOffset\', array( \'DATA\', \'DATA\', \'DATA\' ), \'TYPE\', array( \'type1\', \'type2\', NULL ), array( \'data1\', \'data2\', \'data3\' ) );</i><br>' );
	$result = CAttribute::ManageTypedOffset( $test, 'ManageTypedOffset', array( 'DATA', 'DATA', 'DATA' ), 'TYPE', array( 'type1', 'type2', NULL ), array( 'data1', 'data2', 'data3' ) );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedOffset( $test, \'ManageTypedOffset\', array( \'DATA\', \'DATA\', \'DATA\' ), \'TYPE\', array( \'type1\', \'type2\', NULL ), array( FALSE, FALSE, FALSE ), TRUE );</i><br>' );
	$result = CAttribute::ManageTypedOffset( $test, 'ManageTypedOffset', array( 'DATA', 'DATA', 'DATA' ), 'TYPE', array( 'type1', 'type2', NULL ), array( FALSE, FALSE, FALSE ), TRUE );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$test = new ArrayObject();</i><br>' );
	$test = new ArrayObject();
	echo( '<i>$result = CAttribute::ManageTypedOffset( $test, \'ManageTypedOffset\', \'DATA\', array( \'KIND\', \'TYPE\' ), array( \'kind1\', \'type1\' ), \'data1\' );</i><br>' );
	$result = CAttribute::ManageTypedOffset( $test, 'ManageTypedOffset',  'DATA', array( 'KIND', 'TYPE' ), array( 'kind1', 'type1' ), 'data1' );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedOffset( $test, \'ManageTypedOffset\',  \'DATA\', array( \'KIND\', \'TYPE\' ), array( \'kind1\', \'type1\' ), \'data2\' );</i><br>' );
	$result = CAttribute::ManageTypedOffset( $test, 'ManageTypedOffset', 'DATA', array( 'KIND', 'TYPE' ), array( 'kind1', 'type1' ), 'data2' );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedOffset( $test, \'ManageTypedOffset\', \'DATA\', array( \'KIND\', \'TYPE\' ), array( \'kind1\', \'type2\' ), \'data2\' );</i><br>' );
	$result = CAttribute::ManageTypedOffset( $test, 'ManageTypedOffset', 'DATA', array( 'KIND', 'TYPE' ), array( 'kind1', 'type2' ), 'data2' );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedOffset( $test, \'ManageTypedOffset\', \'DATA\', array( \'KIND\', \'TYPE\' ), array( \'kind2\', \'type2\' ), \'data2\' );</i><br>' );
	$result = CAttribute::ManageTypedOffset( $test, 'ManageTypedOffset', 'DATA', array( 'KIND', 'TYPE' ), array( 'kind2', 'type2' ), 'data2' );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedOffset( $test, \'ManageTypedOffset\', \'DATA\', array( \'KIND\', \'TYPE\' ), array( \'kind2\', \'type2\' ), \'data2\' );</i><br>' );
	$result = CAttribute::ManageTypedOffset( $test, 'ManageTypedOffset', 'DATA', array( 'KIND', 'TYPE' ), array( 'kind2', 'type2' ), 'data2' );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedOffset( $test, \'ManageTypedOffset\', \'DATA\', array( \'KIND\', \'TYPE\' ), array( \'kind2\', \'type2\' ) );</i><br>' );
	$result = CAttribute::ManageTypedOffset( $test, 'ManageTypedOffset', 'DATA', array( 'KIND', 'TYPE' ), array( 'kind2', 'type2' ) );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedOffset( $test, \'ManageTypedOffset\', \'DATA\', array( \'KIND\', \'TYPE\' ), array( NULL, \'type3\' ), \'data3\' );</i><br>' );
	$result = CAttribute::ManageTypedOffset( $test, 'ManageTypedOffset', 'DATA', array( 'KIND', 'TYPE' ), array( NULL, 'type3' ), 'data3' );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedOffset( $test, \'ManageTypedOffset\', \'DATA\', array( \'KIND\', \'TYPE\' ), array( NULL, NULL ), \'data4\' );</i><br>' );
	$result = CAttribute::ManageTypedOffset( $test, 'ManageTypedOffset', 'DATA', array( 'KIND', 'TYPE' ), array( NULL, NULL ), 'data4' );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedOffset( $test, \'ManageTypedOffset\', \'DATA\', array( \'KIND\', \'TYPE\' ), array( NULL, \'type3\' ), FALSE, TRUE );</i><br>' );
	$result = CAttribute::ManageTypedOffset( $test, 'ManageTypedOffset', 'DATA', array( 'KIND', 'TYPE' ), array( NULL, 'type3' ), FALSE, TRUE );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedOffset( $test, \'ManageTypedOffset\', \'DATA\', array( \'KIND\', \'TYPE\' ), array( \'kind1\', \'type2\' ), FALSE, TRUE );</i><br>' );
	$result = CAttribute::ManageTypedOffset( $test, 'ManageTypedOffset', 'DATA', array( 'KIND', 'TYPE' ), array( 'kind1', 'type2' ), FALSE, TRUE );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedOffset( $test, \'ManageTypedOffset\', array( \'DATA\', \'DATA\', \'DATA\' ), array( array( \'KIND\', \'TYPE\' ), array( \'KIND\', \'TYPE\' ), array( \'KIND\', \'TYPE\' ) ), array( array( \'kind1\', \'type1\' ), array( \'kind2\', \'type2\' ), array( NULL, NULL ) ), FALSE, TRUE );</i><br>' );
	$result = CAttribute::ManageTypedOffset( $test, 'ManageTypedOffset', array( 'DATA', 'DATA', 'DATA' ), array( array( 'KIND', 'TYPE' ), array( 'KIND', 'TYPE' ), array( 'KIND', 'TYPE' ) ), array( array( 'kind1', 'type1' ), array( 'kind2', 'type2' ), array( NULL, NULL ) ), FALSE, TRUE );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedOffset( $test, \'ManageTypedOffset\', array( \'DATA\', \'DATA\', \'DATA\' ), array( array( \'KIND\', \'TYPE\' ), array( \'KIND\', \'TYPE\' ), array( \'KIND\', \'TYPE\' ) ), array( array( \'kind1\', \'type1\' ), array( \'kind2\', \'type2\' ), array( NULL, NULL ) ), array( \'data\', \'data2\', array( 1, 2, 3 ) ) );</i><br>' );
	$result = CAttribute::ManageTypedOffset( $test, 'ManageTypedOffset', array( 'DATA', 'DATA', 'DATA' ), array( array( 'KIND', 'TYPE' ), array( 'KIND', 'TYPE' ), array( 'KIND', 'TYPE' ) ), array( array( 'kind1', 'type1' ), array( 'kind2', 'type2' ), array( NULL, NULL ) ), array( 'data', 'data2', array( 1, 2, 3 ) ) );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedOffset( $test, \'ManageTypedOffset\', array( \'DATA\', \'DATA\', \'DATA\' ), array( array( \'KIND\', \'TYPE\' ), array( \'KIND\', \'TYPE\' ), array( \'KIND\', \'TYPE\' ) ), array( array( \'kind1\', \'type1\' ), array( \'kind2\', \'type2\' ), array( NULL, NULL ) ), FALSE, TRUE );</i><br>' );
	$result = CAttribute::ManageTypedOffset( $test, 'ManageTypedOffset', array( 'DATA', 'DATA', 'DATA' ), array( array( 'KIND', 'TYPE' ), array( 'KIND', 'TYPE' ), array( 'KIND', 'TYPE' ) ), array( array( 'kind1', 'type1' ), array( 'kind2', 'type2' ), array( NULL, NULL ) ), FALSE, TRUE );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	echo( '<hr>' );

	//
	// ManageTypedArrayOffset.
	//
	echo( '<h3>ManageTypedArrayOffset</h3>' );
	
	echo( '<i>$test = new ArrayObject();</i><br>' );
	$test = new ArrayObject();
	echo( '<i>$result = CAttribute::ManageTypedArrayOffset( $test, \'ManageTypedArrayOffset\', \'DATA\', \'TYPE\', \'type1\', \'data1\', TRUE );</i><br>' );
	$result = CAttribute::ManageTypedArrayOffset( $test, 'ManageTypedArrayOffset', 'DATA', 'TYPE', 'type1', 'data1', TRUE );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedArrayOffset( $test, \'ManageTypedArrayOffset\', \'DATA\', \'TYPE\', \'type2\', \'data1\', TRUE );</i><br>' );
	$result = CAttribute::ManageTypedArrayOffset( $test, 'ManageTypedArrayOffset', 'DATA', 'TYPE', 'type2', 'data1', TRUE );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedArrayOffset( $test, \'ManageTypedArrayOffset\', \'DATA\', \'TYPE\', \'type2\', \'data2\', TRUE );</i><br>' );
	$result = CAttribute::ManageTypedArrayOffset( $test, 'ManageTypedArrayOffset', 'DATA', 'TYPE', 'type2', 'data2', TRUE );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedArrayOffset( $test, \'ManageTypedArrayOffset\', \'DATA\', \'TYPE\', \'type3\', array( 1, 2, 3 ), TRUE );</i><br>' );
	$result = CAttribute::ManageTypedArrayOffset( $test, 'ManageTypedArrayOffset', 'DATA', 'TYPE', 'type3', array( 1, 2, 3 ), TRUE );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedArrayOffset( $test, \'ManageTypedArrayOffset\', \'DATA\', \'TYPE\', \'type2\', \'data2\' );</i><br>' );
	$result = CAttribute::ManageTypedArrayOffset( $test, 'ManageTypedArrayOffset', 'DATA', 'TYPE', 'type2', 'data2' );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedArrayOffset( $test, \'ManageTypedArrayOffset\', \'DATA\', \'TYPE\', \'type2\' );</i><br>' );
	$result = CAttribute::ManageTypedArrayOffset( $test, 'ManageTypedArrayOffset', 'DATA', 'TYPE', 'type2' );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedArrayOffset( $test, \'ManageTypedArrayOffset\', \'DATA\', \'TYPE\', \'type3\', NULL, FALSE, TRUE );</i><br>' );
	$result = CAttribute::ManageTypedArrayOffset( $test, 'ManageTypedArrayOffset', 'DATA', 'TYPE', 'type3', NULL, FALSE, TRUE );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedArrayOffset( $test, \'ManageTypedArrayOffset\', \'DATA\', \'TYPE\', \'type1\', \'data1\', FALSE, TRUE );</i><br>' );
	$result = CAttribute::ManageTypedArrayOffset( $test, 'ManageTypedArrayOffset', 'DATA', 'TYPE', 'type1', 'data1', FALSE, TRUE );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedArrayOffset( $test, \'ManageTypedArrayOffset\', \'DATA\', \'TYPE\', \'type2\', \'data1\', FALSE, TRUE );</i><br>' );
	$result = CAttribute::ManageTypedArrayOffset( $test, 'ManageTypedArrayOffset', 'DATA', 'TYPE', 'type2', 'data1', FALSE, TRUE );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedArrayOffset( $test, \'ManageTypedArrayOffset\', \'DATA\', \'TYPE\', \'type2\', \'data2\', FALSE, TRUE );</i><br>' );
	$result = CAttribute::ManageTypedArrayOffset( $test, 'ManageTypedArrayOffset', 'DATA', 'TYPE', 'type2', 'data2', FALSE, TRUE );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>$test = Array();</i><br>' );
	$test = Array();
	echo( '<i>$result = CAttribute::ManageTypedArrayOffset( $test, \'ManageTypedArrayOffset\', array( \'DATA\', \'DATA\', \'DATA\' ), \'TYPE\', array( \'type1\', \'type2\', \'type3\' ), array( \'data1\', array( \'data1\', \'data2\' ), array( 1, 2, 3 ) ), TRUE );</i><br>' );
	$result = CAttribute::ManageTypedArrayOffset( $test, 'ManageTypedArrayOffset', array( 'DATA', 'DATA', 'DATA' ), 'TYPE', array( 'type1', 'type2', 'type3' ), array( 'data1', array( 'data1', 'data2' ), array( 1, 2, 3 ) ), TRUE );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedArrayOffset( $test, \'ManageTypedArrayOffset\', array( \'DATA\', \'DATA\', \'DATA\' ), \'TYPE\', array( \'type1\', \'type2\', \'type3\' ), array( NULL, \'data2\', array( 2, 3, 4 ) ) );</i><br>' );
	$result = CAttribute::ManageTypedArrayOffset( $test, 'ManageTypedArrayOffset', array( 'DATA', 'DATA', 'DATA' ), 'TYPE', array( 'type1', 'type2', 'type3' ), array( NULL, 'data2', array( 2, 3, 4 ) ) );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedArrayOffset( $test, \'ManageTypedArrayOffset\', array( \'DATA\', \'DATA\', \'DATA\' ), \'TYPE\', array( \'type1\', \'type2\', \'type3\' ), array( NULL, \'data2\', array( 2, 3, 4 ) ), FALSE, TRUE );</i><br>' );
	$result = CAttribute::ManageTypedArrayOffset( $test, 'ManageTypedArrayOffset', array( 'DATA', 'DATA', 'DATA' ), 'TYPE', array( 'type1', 'type2', 'type3' ), array( NULL, 'data2', array( 2, 3, 4 ) ), FALSE, TRUE );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedArrayOffset( $test, \'ManageTypedArrayOffset\', array( \'DATA\', \'DATA\', \'DATA\', \'DATA\' ), \'TYPE\', array( \'type2\', \'type2\', \'type3\', \'type3\' ), array( \'data1\', \'data1\', 1, 1 ), array( NULL, FALSE, NULL, FALSE ) );</i><br>' );
	$result = CAttribute::ManageTypedArrayOffset( $test, 'ManageTypedArrayOffset', array( 'DATA', 'DATA', 'DATA', 'DATA' ), 'TYPE', array( 'type2', 'type2', 'type3', 'type3' ), array( 'data1', 'data1', 1, 1 ), array( NULL, FALSE, NULL, FALSE ) );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>$test = Array();</i><br>' );
	$test = Array();
	echo( '<i>$result = CAttribute::ManageTypedArrayOffset( $test, \'ManageTypedArrayOffset\', \'DATA\', array( \'KIND\', \'TYPE\' ), array( \'kind1\', \'type1\' ), array( \'data1\', \'data2\' ), TRUE );</i><br>' );
	$result = CAttribute::ManageTypedArrayOffset( $test, 'ManageTypedArrayOffset', 'DATA', array( 'KIND', 'TYPE' ), array( 'kind1', 'type1' ), array( 'data1', 'data2' ), TRUE );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedArrayOffset( $test, \'ManageTypedArrayOffset\', \'DATA\', array( \'TYPE\', \'KIND\' ), array( \'type1\', \'kind1\' ), array( \'data3\', \'data4\' ), TRUE );</i><br>' );
	$result = CAttribute::ManageTypedArrayOffset( $test, 'ManageTypedArrayOffset', 'DATA', array( 'TYPE', 'KIND' ), array( 'type1', 'kind1' ), array( 'data3', 'data4' ), TRUE );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedArrayOffset( $test, \'ManageTypedArrayOffset\', \'DATA\', array( \'KIND\', \'TYPE\' ), array( \'kind1\', \'type1\' ), array( \'data1\', \'data2\' ), TRUE );</i><br>' );
	$result = CAttribute::ManageTypedArrayOffset( $test, 'ManageTypedArrayOffset', 'DATA', array( 'KIND', 'TYPE' ), array( 'kind1', 'type1' ), array( 'data1', 'data2' ), TRUE );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedArrayOffset( $test, \'ManageTypedArrayOffset\', \'DATA\', array( \'KIND\', \'TYPE\' ), array( \'kind1\', \'type1\' ), array( \'data1\', \'data2\' ), FALSE, TRUE );</i><br>' );
	$result = CAttribute::ManageTypedArrayOffset( $test, 'ManageTypedArrayOffset', 'DATA', array( 'KIND', 'TYPE' ), array( 'kind1', 'type1' ), array( 'data1', 'data2' ), FALSE, TRUE );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$result = CAttribute::ManageTypedArrayOffset( $test, \'ManageTypedArrayOffset\', \'DATA\', array( \'TYPE\', \'KIND\' ), array( \'type1\', \'kind1\' ), NULL, FALSE, TRUE );</i><br>' );
	$result = CAttribute::ManageTypedArrayOffset( $test, 'ManageTypedArrayOffset', 'DATA', array( 'TYPE', 'KIND' ), array( 'type1', 'kind1' ), NULL, FALSE, TRUE );
	echo( "Result <pre>" ); print_r( $result ); echo( '</pre>' );
	echo( "Object <pre>" ); print_r( $test ); echo( '</pre>' );
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
