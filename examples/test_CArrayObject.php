<?php

/**
 * {@link CArrayObject.php Base} object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * base object {@link CArrayObject class}.
 *
 *	@package	Test
 *	@subpackage	Framework
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 13/12/2012
 */

/*=======================================================================================
 *																						*
 *									test_CArrayObject.php								*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."CArrayObject.php" );


/*=======================================================================================
 *	TEST CLASS																			*
 *======================================================================================*/
 
//
// Declare test class.
//
class MyTest extends CArrayObject
{
	public function test_ManageArrayOffset( $theValue = NULL,
											$theOperation = NULL,
											$getOld = FALSE )
	{
		return $this->_ManageArrayOffset( 'TEST1', $theValue, $theOperation, $getOld );
	}

	public function test_ManageTypedArrayOffset( $theType, $theValue = NULL,
														   $theOperation = NULL,
														   $getOld = FALSE )
	{
		return $this->_ManageTypedArrayOffset
					( 'TEST2', $theType, $theValue, $theOperation, $getOld );
	}
}


/*=======================================================================================
 *	TEST DEFAULT EXCEPTIONS																*
 *======================================================================================*/
 
//
// Instantiate test class.
//
$test = new CArrayObject();

//
// Get a timer.
//
$start = microtime( TRUE );

//
// Test class.
//
try
{
	//
	// Test offsets.
	//
	echo( '<h3>Offsets</h3>' );
	
	$test[ 'Name' ] = 'Milko';
	$test[ 'Surname' ] = 'Skofic';
	echo( '<i>$test[ \'Name\' ] = \'Milko\';<br>'
		 .'$test[ \'Surname\' ] = \'Skofic\';</i><br>' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	
	$x1 = $test[ 'Name' ];
	$x2 = $test[ 'non-existant' ];
	if( $x2 === NULL )
		$x2 = '<i>NULL</i>';
	echo( '<i>$test[ \'Name\' ] = </i>'.$x1.'<br>' );
	echo( '<i>$test[ \'non-existant\' ] = </i>'."$x2<br><br>" );
	
	$test[ 'Surname' ] = NULL;
	echo( '$test[ \'Surname\' ] = NULL;</i><br>' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	
	//
	// Test JSON.
	//
	echo( '<h3>JSONing</h3>' );
	
	$data = array( 'uno' => 1, 'due' => array( 1, 2 ) );
	echo( 'array( \'uno\' => 1, '
		  .'\'due\' => array( 1, 2 ) );<br>' );
	$json = CObject::JsonEncode( $data );
	echo( "$json<br><br>" );
	
	$data = CObject::JsonDecode( $json );
	echo( 'CObject::JsonDecode( $json );<br>' );
	echo( '<pre>' ); print_r( $data ); echo( '</pre>' );
	
	try
	{
		echo( 'CObject::JsonDecode( \'{"due":[1,&]}\' );<br>' );
		$data = CObject::JsonDecode( '{"due":[1,&]}' );
		echo( '<pre>' ); print_r( $data ); echo( '</pre>' );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );

	} echo( '<br>' );
	
	//
	// Test string normalisation.
	//
	echo( '<h3>String normalisation</h3>' );
	
	$string = ' STRING  ';
	$norm = CObject::StringNormalise( $string, kFLAG_MODIFIER_LTRIM );
	echo( 'CObject::StringNormalise( <code>['.$string.']</code>, kFLAG_MODIFIER_LTRIM );<br>' );
	echo( "<code>[$norm]</code><br><br>" );
	
	$norm = CObject::StringNormalise( $string, kFLAG_MODIFIER_RTRIM );
	echo( 'CObject::StringNormalise( <code>['.$string.']</code>, kFLAG_MODIFIER_RTRIM );<br>' );
	echo( "<code>[$norm]</code><br><br>" );
	
	$norm = CObject::StringNormalise( $string, kFLAG_MODIFIER_TRIM );
	echo( 'CObject::StringNormalise( <code>['.$string.']</code>, kFLAG_MODIFIER_TRIM );<br>' );
	echo( "<code>[$norm]</code><br><br>" );
	
	$string = '';
	$norm = CObject::StringNormalise( $string, kFLAG_MODIFIER_NULL );
	echo( 'CObject::StringNormalise( \'\', kFLAG_MODIFIER_NULL );<br>' );
	if( $norm === NULL )
		echo( "<i>NULL</i><br><br>" );
	else
		echo( "<code>[$norm]</code><br><br>" );
	
	$norm = CObject::StringNormalise( $string, kFLAG_MODIFIER_NULLSTR );
	echo( 'CObject::StringNormalise( \'\', kFLAG_MODIFIER_NULLSTR );<br>' );
	if( $norm === NULL )
		echo( "<i>NULL</i><br><br>" );
	else
		echo( "<code>[$norm]</code><br><br>" );
	
	$string = 'This is a String';
	$norm = CObject::StringNormalise( $string, kFLAG_MODIFIER_NOCASE );
	echo( 'CObject::StringNormalise( <code>['.$string.']</code>, kFLAG_MODIFIER_NOCASE );<br>' );
	echo( "<code>[$norm]</code><br><br>" );
	
	$string = '<Encode> this & THùs';
	$dstring = htmlspecialchars( $string );
	$norm = CObject::StringNormalise( $string, kFLAG_MODIFIER_URL );
	$dnorm = htmlspecialchars( $norm );
	echo( 'CObject::StringNormalise( <code>['.$dstring.']</code>, kFLAG_MODIFIER_URL );<br>' );
	echo( "<code>[$dnorm]</code><br><br>" );
	
	$dstring = htmlspecialchars( $string );
	$norm = CObject::StringNormalise( $string, kFLAG_MODIFIER_HTML );
	$dnorm = htmlspecialchars( $norm );
	echo( 'CObject::StringNormalise( <code>['.$dstring.']</code>, kFLAG_MODIFIER_HTML );<br>' );
	echo( "<code>[$dnorm]</code><br><br>" );
	
	$string = 'HEX';
	$norm = CObject::StringNormalise( $string, kFLAG_MODIFIER_HEX );
	echo( 'CObject::StringNormalise( <code>['.$string.']</code>, kFLAG_MODIFIER_HEX );<br>' );
	echo( "<code>[$norm]</code><br><br>" );
	
	$norm = CObject::StringNormalise( $string, kFLAG_MODIFIER_HEXEXP );
	echo( 'CObject::StringNormalise( <code>['.$string.']</code>, kFLAG_MODIFIER_HEXEXP );<br>' );
	echo( "<code>[$norm]</code><br><br>" );
	
	$string = 'HASH';
	$norm = CObject::StringNormalise( $string, kFLAG_MODIFIER_HASH );
	$len = strlen( $norm );
	echo( 'CObject::StringNormalise( <code>['.$string.']</code>, kFLAG_MODIFIER_HASH );<br>' );
	echo( "<code>[$norm](length: $len)</code><br><br>" );
	
	$norm = CObject::StringNormalise( $string, kFLAG_MODIFIER_HASH_BIN );
	$len = strlen( $norm );
	$dnorm = bin2hex( $norm );
	echo( 'CObject::StringNormalise( <code>['.$string.']</code>, kFLAG_MODIFIER_HASH_BIN );<br>' );
	echo( "<code>[$dnorm](length: $len)</code><br><br>" );
	
	$end = microtime( TRUE );
	echo( "Start: $start End: $end<br>" );
	echo( 'CObject::DurationString( $end - $start )<br>' );
	echo( CObject::DurationString( $end - $start )."<br><br>" );
	
	//
	// Test array functions.
	//
	echo( '<h3>Array functions</h3>' );
	
	$norm = $test->keys();
	echo( '$test-><i>keys()</i>;<br>' );
	echo( '<pre>' ); print_r( $norm ); echo( '</pre>' );
	
	$norm = $test->values();
	echo( '$test-><i>values()</i>;<br>' );
	echo( '<pre>' ); print_r( $norm ); echo( '</pre>' );
	
	//
	// Test array management function.
	//
	echo( '<h3>Array management function</h3>' );
	
	$test = new MyTest();
	echo( '<i>$test = new MyTest();</i><br>' );
	$found = $test->test_ManageArrayOffset( 1, TRUE );
	echo( '<i>$found = $test->test_ManageArrayOffset( 1, TRUE );</i><br>' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Found<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	
	$found = $test->test_ManageArrayOffset( 2, TRUE, TRUE );
	echo( '<i>$found = $test->test_ManageArrayOffset( 2, TRUE, TRUE );</i><br>' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Found<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	
	$found = $test->test_ManageArrayOffset( '1', TRUE, TRUE );
	echo( '<i>$found = $test->test_ManageArrayOffset( \'1\', TRUE, TRUE );</i><br>' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Found<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	
	$found = $test->test_ManageArrayOffset( '2', FALSE, TRUE );
	echo( '<i>$found = $test->test_ManageArrayOffset( \'2\', FALSE, TRUE );</i><br>' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Found<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	
	$found = $test->test_ManageArrayOffset();
	echo( '<i>$found = $test->test_ManageArrayOffset();</i><br>' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Found<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	
	$found = $test->test_ManageArrayOffset( 1 );
	echo( '<i>$found = $test->test_ManageArrayOffset( 1 );</i><br>' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Found<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Test typed array management function.
	//
	echo( '<h3>Typed array management function</h3>' );
	
	echo( '<i>$test = new MyTest();</i><br>' );
	$test = new MyTest();
	echo( '<i>$found = $test->test_ManageTypedArrayOffset( \'First\', 1 );</i><br>' );
	$found = $test->test_ManageTypedArrayOffset( 'First', 1 );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Found<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$found = $test->test_ManageTypedArrayOffset( \'Second\', 2, TRUE );</i><br>' );
	$found = $test->test_ManageTypedArrayOffset( 'Second', 2, TRUE );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Found<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$found = $test->test_ManageTypedArrayOffset( \'First\', 10, TRUE );</i><br>' );
	$found = $test->test_ManageTypedArrayOffset( 'First', 10, TRUE );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Found<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$found = $test->test_ManageTypedArrayOffset( \'Second\', FALSE );</i><br>' );
	$found = $test->test_ManageTypedArrayOffset( 'Second', FALSE );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Found<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$found = $test->test_ManageTypedArrayOffset( \'First\', FALSE, TRUE );</i><br>' );
	$found = $test->test_ManageTypedArrayOffset( 'First', FALSE, TRUE );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Found<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$test = new MyTest();</i><br>' );
	$test = new MyTest();
	echo( '<i>$types = array( \'first\', \'second\', \'third\' );</i><br>' );
	$types = array( 'first', 'second', 'third' );
	echo( '<i>$data = array( 10, 20, 30 );</i><br>' );
	$data = array( 10, 20, 30 );
	echo( '<i>$found = $test->test_ManageTypedArrayOffset( $types, $data );</i><br>' );
	$found = $test->test_ManageTypedArrayOffset( $types, $data );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Found<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$types = array( \'first\', \'second\', \'third\' );</i><br>' );
	$types = array( 'first', 'second', 'third' );
	echo( '<i>$found = $test->test_ManageTypedArrayOffset( $types );</i><br>' );
	$found = $test->test_ManageTypedArrayOffset( $types );
	echo( 'Found<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$types = array( \'first\', \'second\', \'third\' );</i><br>' );
	$types = array( 'first', 'second', 'third' );
	echo( '<i>$found = $test->test_ManageTypedArrayOffset( $types, FALSE, TRUE );</i><br>' );
	$found = $test->test_ManageTypedArrayOffset( $types, FALSE, TRUE );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Found<pre>' ); print_r( $found ); echo( '</pre>' );
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
