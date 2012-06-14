<?php

/**
 * {@link CGenesys.php Base} object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * base object {@link CGenesys class}.
 *
 *	@package	Test
 *	@subpackage	Entities
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 21/12/2012
 */

/*=======================================================================================
 *																						*
 *									test_CGenesys.php									*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Class includes.
//
require_once( "CGenesysOnce.php" );


/*=======================================================================================
 *	TEST USER PERSISTENT OBJECTS														*
 *======================================================================================*/

//
// Test class.
//
try
{
/*
	//
	// Instantiate.
	//
	echo( '<h3>Instantiate object</h3>' );
	
	echo( '<i>$test = new CGenesys();</i><br>' );
	$test = new CGenesys();
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$test = new CGenesys( \'MySQLi://GENESYS-WRITER:genesyswriter@localhost/GENESYS\');</i><br>' );
	$test = new CGenesys( 'MySQLi://GENESYS-WRITER:genesyswriter@localhost/GENESYS' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	echo( '<hr>' );

	//
	// Connect.
	//
	echo( '<h3>Connect</h3>' );
	
	echo( '<i>$db = $test->Connection();</i><br>' );
	$db = $test->Connection();
	echo( '<i>$tables = $test->CEtables();</i><br>' );
	$tables = $test->CEtables();
	echo( '<pre>' ); print_r( count( $tables ) ); echo( '</pre>' );
	echo( '<hr>' );

	//
	// Load characterised.
	//
	echo( '<h3>Load characterised</h3>' );
	
	echo( '<i>$test = new CGenesys( \'MySQLi://GENESYS-WRITER:genesyswriter@localhost/GENESYS?persist\');</i><br>' );
	$test = new CGenesys( 'MySQLi://GENESYS-WRITER:genesyswriter@localhost/GENESYS?persist' );
	echo( '<i>$count = $test->MarkCharacterized();</i><br>' );
	$count = $test->MarkCharacterized();
	echo( '<pre>' ); print_r( $count ); echo( '</pre>' );
	echo( '<hr>' );
*/
	//
	// Test import.
	//
	echo( '<h3>Test import</h3>' );
	
	echo( '<i>$test = new CGenesys( \'MySQLi://GENESYS-WRITER:genesyswriter@localhost/GENESYS?persist\');</i><br>' );
	$test = new CGenesys( 'MySQLi://GENESYS-WRITER:genesyswriter@localhost/GENESYS?persist' );
	echo( '<i>$count = $test->ImportPassport( \'/Library/WebServer/Library/wrapper/batch/test_mcpd.csv\');</i><br>' );
	$count = $test->ImportPassport( '/Library/WebServer/Library/wrapper/batch/test_mcpd.csv' );
	echo( '<pre>' ); print_r( $count ); echo( '</pre>' );
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
