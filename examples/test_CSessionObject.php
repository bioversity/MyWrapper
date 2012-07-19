<?php

/**
 * {@link CSessionObject.php Base} object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * base object {@link CSessionObject class}.
 *
 *	@package	Test
 *	@subpackage	Entities
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 19/07/2012
 */

/*=======================================================================================
 *																						*
 *								test_CSessionObject.php									*
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
// Graph includes.
//
require_once( kPATH_LIBRARY_SOURCE."CGraphEdge.php" );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."CSessionObject.php" );

session_start();


/*=======================================================================================
 *	TEST CLASS																			*
 *======================================================================================*/

//
// Test class.
//
class TestClass extends CSessionObject
{
	public function Query()
	{
		return new CMongoQuery();
	}
	
	protected function _InitDataStore( $theOperation )
	{
		if( $theOperation )
			$this->DataStore( new Mongo() );
		else
			$this->DataStore( FALSE );
	}
	
	protected function _InitGraphStore( $theOperation )
	{
		if( $theOperation )
			$this->GraphStore(
				new Everyman\Neo4j\Client(
					DEFAULT_kNEO4J_HOST, DEFAULT_kNEO4J_PORT ) );
		else
			$this->GraphStore( FALSE );
	}
	
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
	
	protected function _InitUserContainer( $theOperation )
	{
		if( $theOperation )
			$this->UsersContainer(
				new CMongoContainer(
					$this->Database()->
						selectCollection(
							'CSessionObject' ) ) );
		else
			$this->UsersContainer( FALSE );
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
