<?php

/**
 * {@link CSessionMongoNeo4j.php Base} object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * base object {@link CSessionMongoNeo4j class}.
 *
 *	@package	Test
 *	@subpackage	Entities
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 19/07/2012
 */

/*=======================================================================================
 *																						*
 *							test_CSessionMongoNeo4j.php									*
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
require_once( kPATH_LIBRARY_SOURCE."CSessionMongoNeo4j.php" );

session_start();


/*=======================================================================================
 *	TEST CLASS																			*
 *======================================================================================*/

//
// Test class.
//
class TestClass extends CSessionMongoNeo4j
{
	//
	// We override this method to prevent screwing up the default database.
	//
	protected function _InitDatabase( $theOperation )
	{
		if( $theOperation )
			$this->Database(
				$this->DataStore()->
					selectDB(
						'TEST' ) );
		else
			$this->Database( FALSE );
	}
}


/*=======================================================================================
 *	TEST USER PERSISTENT OBJECTS														*
 *======================================================================================*/

//
// Test class.
//
try
{
	//
	// First run.
	//
	if( ! array_key_exists( kDEFAULT_SESSION, $_SESSION ) )
	{
		//
		// Initialise session.
		//
		$_SESSION[ kDEFAULT_SESSION ] = new TestClass();
	
		//
		// Create user 1.
		//
		$user = new CUser();
		$user->Code( 'Milko' );
		$user->Password( 'Secret' );
		$user->Name( 'Milko Škofič' );
		$user->Email( 'm.skofic@cgiar.org' );
		$user->Role( array( kROLE_FILE_IMPORT, kROLE_USER_MANAGE ), TRUE );
		$user->Commit( $_SESSION[ kDEFAULT_SESSION ]->UsersContainer() );
	
	} // First time.
	
	//
	// Load user.
	//
	if( array_key_exists( 'code', $_REQUEST )
	 && array_key_exists( 'pass', $_REQUEST ) )
	{
		//
		// Look for user.
		//
		$found
			= $_SESSION[ kDEFAULT_SESSION ]
				->Login( $_REQUEST[ 'code' ], $_REQUEST[ 'pass' ] );
		if( $found )
			$_SESSION[ kDEFAULT_SESSION ]
				->User( $found );
		else
			echo( '<i>User not found</i><br />' );
	
	} // Provided credentials.
	
	echo( '<pre>' );
	print_r( $_SESSION[ kDEFAULT_SESSION ] );
	echo( '</pre>' );
	echo( '<hr />' );
	echo( (string) $_SESSION[ kDEFAULT_SESSION ] );
	echo( '<hr />' );
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
