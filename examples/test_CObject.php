<?php

/**
 * {@link CObject.php Base} object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * base object {@link CObject class}.
 *
 *	@package	Test
 *	@subpackage	Framework
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 07/03/2012
 */

/*=======================================================================================
 *																						*
 *									test_CObject.php									*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."CObject.php" );


/*=======================================================================================
 *	DEFINE TEST CLASS																	*
 *======================================================================================*/
 
//
// Declare test class.
//
class MyTest extends CObject
{
	private $mMember = NULL;
	 
	function Member( $theValue, $getOld = FALSE )
	{
		return $this->_ManageMember( $this->mMember, $theValue, $getOld );			// ==>
	}
}


/*=======================================================================================
 *	TEST DEFAULT EXCEPTIONS																*
 *======================================================================================*/
 
//
// Instantiate test class.
//
$test = new MyTest();

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
	
	$string = '<Encode> this & This';
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
	// Test member maagement functions.
	//
	echo( '<h3>member management functions</h3>' );
	
	$res = $test->Member( 1 );
	echo( '$res = $test->Member( 1 );<br>' );
	echo( 'Test<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Result<pre>' ); print_r( $res ); echo( '</pre>' );
	echo( '<hr>' );
	
	$res = $test->Member( 2 );
	echo( '$res = $test->Member( 2 );<br>' );
	echo( 'Test<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Result<pre>' ); print_r( $res ); echo( '</pre>' );
	echo( '<hr>' );
	
	$res = $test->Member( 3, TRUE );
	echo( '$res = $test->Member( 3, TRUE );<br>' );
	echo( 'Test<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Result<pre>' ); print_r( $res ); echo( '</pre>' );
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
