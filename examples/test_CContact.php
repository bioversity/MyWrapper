<?php
	
/**
 * {@link CContact.php Contact} object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * {@link CContact class}.
 *
 *	@package	Test
 *	@subpackage	Persistence
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 03/04/2012
 */

/*=======================================================================================
 *																						*
 *									test_CContact.php									*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."CContact.php" );


/*=======================================================================================
 *	TEST CLASS DEFINITION																*
 *======================================================================================*/
 
//
// Test class.
//
class MyClass extends CContact{}

/*=======================================================================================
 *	TEST MAIL ADDRESS OBJECT															*
 *======================================================================================*/
 
//
// TRY BLOCK.
//
try
{
	//
	// Instantiate mailing address 1.
	//
	echo( '<i>$address1 = new CMailAddress();</i><br>' );
	$address1 = new CMailAddress();
	$address1->Place( 'Mulino di Maccarese' );
	$address1->Care( 'Bioversity International' );
	$address1->Street( 'Via dei Tre Denari, 472/a' );
	$address1->Zip( '00057' );
	$address1->City( 'Maccarese' );
	$address1->Province( 'Fiumicino (RM)' );
	$address1->Country( 'ITALY' );
	echo( '<pre>' ); print_r( $address1 ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Instantiate mailing address 2.
	//
	echo( '<i>$address2 = new CMailAddress();</i><br>' );
	$address2 = new CMailAddress();
	$address2->Street( 'Via di Torrimpietra, 227' );
	$address2->Zip( '00050' );
	$address2->City( 'Torre in Pietra' );
	$address2->Province( 'Fiumicino (RM)' );
	$address2->Country( 'ITALY' );
	echo( '<pre>' ); print_r( $address2 ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Instantiate mailing address 3.
	//
	echo( '<i>$address3 = new CMailAddress();</i><br>' );
	$address3 = new CMailAddress();
	$address3->Street( 'Via Pomponio Attico, 7b' );
	$address3->Zip( '00178' );
	$address3->City( 'Roma' );
	$address3->Province( '(RM)' );
	$address3->Country( 'ITALY' );
	echo( '<pre>' ); print_r( $address3 ); echo( '</pre>' );
	echo( '<hr>' );
	echo( '<hr>' );
	
	//
	// Instantiate empty contact.
	//
	echo( '<i>$test = new MyClass();</i><br>' );
	$test = new MyClass();
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Add default address.
	//
	echo( '<i>$test->Mail( $address1 );</i><br>' );
	$test->Mail( $address1 );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Add home address.
	//
	echo( '<i>$test->Mail( $address2, \'Home\' );</i><br>' );
	$test->Mail( $address2, 'Home' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Add other address.
	//
	echo( '<i>$test->Mail( $address3, \'Other\' );</i><br>' );
	$test->Mail( $address3, 'Other' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	echo( '<hr>' );
	
	//
	// Retrieve default address.
	//
	echo( '<i>$address = $test->Mail();</i><br>' );
	$address = $test->Mail();
	echo( '<pre>' ); print_r( $address ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Retrieve home address.
	//
	echo( '<i>$address = $test->Mail( NULL, \'Home\' );</i><br>' );
	$address = $test->Mail( NULL, 'Home' );
	echo( '<pre>' ); print_r( $address ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Delete other address.
	//
	echo( '<i>$address = $test->Mail( FALSE, \'Other\' );</i><br>' );
	$address = $test->Mail( FALSE, 'Other' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Add default telephone number.
	//
	echo( '<i>$test->Phone( \'+39 06 6118286\' );</i><br>' );
	$test->Phone( '+39 06 6118286' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Add home telephone number.
	//
	echo( '<i>$test->Phone( \'+39 06 61697702\', \'Home\' );</i><br>' );
	$test->Phone( '+39 06 6118286', 'Home' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
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
