<?php

/**
 * {@link CStatusObject.php Base} object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * base object {@link CStatusObject class}.
 *
 *	@package	Test
 *	@subpackage	Framework
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 13/12/2012
 */

/*=======================================================================================
 *																						*
 *								test_CStatusObject.php									*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."CStatusObject.php" );


/*=======================================================================================
 *	TEST CLASS DECLARATIONS																*
 *======================================================================================*/
 
//
// Test class.
//
class MyTest extends CStatusObject
{
	//
	// Link inited status with presence of NAME and SURNAME offsets.
	//
	
	public function offsetSet( $theOffset, $theValue )
	{
		parent::offsetSet( $theOffset, $theValue );
		$this->_IsInited
			( $this->offsetExists( 'NAME' ) && $this->offsetExists( 'SURNAME' ) );
	}
	
	public function offsetUnset( $theOffset )
	{
		parent::offsetUnset( $theOffset );
		$this->_IsInited
			( $this->offsetExists( 'NAME' ) && $this->offsetExists( 'SURNAME' ) );
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
// Test class.
//
try
{
	//
	// Test offsets.
	//
	echo( '<h3>Offsets</h3>' );
	
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$test[ \'NAME\' ] = \'Milko\';</i><br>' );
	$test[ 'NAME' ] = 'Milko';
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	
	echo( '<i>$test[ \'SURNAME\' ] = \'Skofic\';</i><br>' );
	$test[ 'SURNAME' ] = 'Skofic';
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	
	echo( '<i>$test[ \'SURNAME\' ] = NULL;</i><br>' );
	$test[ 'SURNAME' ] = NULL;
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
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
