<?php
	
/**
 * {@link CMailAddress.php Query} object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * {@link CMailAddress class}.
 *
 *	@package	Test
 *	@subpackage	Persistence
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 03/04/2012
 */

/*=======================================================================================
 *																						*
 *									test_CMailAddress.php								*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."CMailAddress.php" );


/*=======================================================================================
 *	TEST MAIL ADDRESS OBJECT															*
 *======================================================================================*/
 
//
// TRY BLOCK.
//
try
{
	//
	// Instantiate empty object.
	//
	echo( '<i>$test = new CMailAddress();</i><br>' );
	$test = new CMailAddress();
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Add place.
	//
	echo( '<i>$test->Place( \'Mulino di Maccarese\' );</i><br>' );
	$test->Place( 'Mulino di Maccarese' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Add care of.
	//
	echo( '<i>$test->Care( \'Bioversity International\' );</i><br>' );
	$test->Care( 'Bioversity International' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Add street.
	//
	echo( '<i>$test->Street( \'Via dei Tre Denari, 472/a\' );</i><br>' );
	$test->Street( 'Via dei Tre Denari, 472/a' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Add ZIP.
	//
	echo( '<i>$test->Zip( \'00057\' );</i><br>' );
	$test->Zip( '00057' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Add city.
	//
	echo( '<i>$test->City( \'Maccarese\' );</i><br>' );
	$test->City( 'Maccarese' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Add province.
	//
	echo( '<i>$test->Province( \'Fiumicino (RM)\' );</i><br>' );
	$test->Province( 'Fiumicino (RM)' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Add country.
	//
	echo( '<i>$test->Country( \'ITALY\' );</i><br>' );
	$test->Country( 'ITALY' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Show full address.
	//
	echo( '<i>$address = $test->Full();</i><br>' );
	$address = $test->Full();
	echo( '<pre>' ); print_r( $address ); echo( '</pre>' );
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
