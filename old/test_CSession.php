<?php

/**
 * {@link CSession.php Base} object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * base object {@link CSession class}.
 *
 *	@package	Test
 *	@subpackage	Entities
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 21/12/2012
 */

/*=======================================================================================
 *																						*
 *									test_CSession.php									*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Environment includes.
//
require_once( '/Library/WebServer/Library/wrapper/local/environment.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."CSession.php" );


/*=======================================================================================
 *	TEST USER PERSISTENT OBJECTS														*
 *======================================================================================*/

//
// Test class.
//
try
{
	//
	// Instantiate Mongo database.
	//
	$mongo = New Mongo();
	
	//
	// Select MCPD database.
	//
	$db = $mongo->selectDB( "WAREHOUSE" );
	
	//
	// Select test collection.
	//
	$collection = $db->selectCollection( kDEFAULT_CNT_USERS );
	
	//
	// Create utility container.
	//
	$container = new CMongoContainer( $collection );

	/*===================================================================================
	 *	CREATE USERS																	*
	 *==================================================================================*/
	 
	//
	// Create user 1.
	//
	$user = new CUser();
	$user->Code( 'Milko' );
	$user->Password( 'Secret' );
	$user->Name( 'Milko Škofič' );
	$user->Email( 'm.skofic@cgiar.org' );
	$user->Role( array( kROLE_FILE_IMPORT, kROLE_USER_MANAGE ), TRUE );
	$user_id = $user->Commit( $container );

	/*===================================================================================
	 *	RUN TESTS																		*
	 *==================================================================================*/
	 
	//
	// Test instantiation.
	//
	echo( '<h3>Instantiation</h3>' );
	
	echo( '<i>new CSession();</i><br>' );
	$test = new CSession();
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );

	//
	// Test login.
	// Note that you must have called this script by providing the user credentials, as:
	// ?:@operation=@LOGIN&:@user-code=Milko&:@user-pass=Secret
	//
	echo( '<h3>Login</h3>' );
	
	echo( '<i>$result = $test->Login();</i><br>' );
	$result = $test->Login();
	echo( 'Result<pre>' ); print_r( $result ); echo( '</pre>' );
	echo( 'Session<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Delete user.
	//
	$container->Delete( $user[ kTAG_LID ], kFLAG_STATE_ENCODED );
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
