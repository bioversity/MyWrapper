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

/**
 * Server environment.
 *
 * This include file contains the server run-time definitions.
 */
require_once( "/Library/WebServer/Library/wrapper/local/server.inc.php" );

//
// Graph includes.
//
require_once( kPATH_LIBRARY_SOURCE."CGraphEdge.php" );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."CSessionObject.php" );


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
	
	protected function _Init()
	{
		parent::_Init();
		$this->_InitDataStore();
		$this->_InitGraphStore();
		$this->_InitDatabase();
		$this->_InitUserContainer();
	}
	protected function _InitDataStore()
	{
		$this->DataStore( new Mongo() );
	}
	protected function _InitGraphStore()
	{
		$this->GraphStore(
			new Everyman\Neo4j\Client(
				kDEFAULT_kNEO4J_HOST, kDEFAULT_kNEO4J_PORT ) );
	}
	protected function _InitDatabase()
	{
		$this->Database(
			$this->DataStore()->
				selectDB(
					'TEST' ) );
	}
	protected function _InitUserContainer()
	{
		$this->UsersContainer(
			new CMongoContainer(
				$this->Database()->
					selectCollection(
						'CSessionObject' ) ) );
	}
	
	protected function _Serialise()
	{
		parent::_Serialise();
		$this->_SerialiseDataStore();
		$this->_SerialiseGraphStore();
		$this->_SerialiseDatabase();
		$this->_SerialiseUserContainer();
	}
	protected function _SerialiseDataStore()
	{
		$this->DataStore( FALSE );
	}
	protected function _SerialiseGraphStore()
	{
		$this->GraphStore( FALSE );
	}
	protected function _SerialiseDatabase()
	{
		$this->mDatabase = 'TEST';
	}
	protected function _SerialiseUserContainer()
	{
		$this->mUsersContainer = 'CSessionObject';
	}
	
	protected function _Unserialise()
	{
		$this->_UnserialiseDataStore();
		$this->_UnserialiseGraphStore();
		$this->_UnserialiseDatabase();
		$this->_UnserialiseUserContainer();
		parent::_Unserialise();
	}
	protected function _UnserialiseDataStore()
	{
		$this->_InitDataStore();
	}
	protected function _UnserialiseGraphStore()
	{
		$this->_InitGraphStore();
	}
	protected function _UnserialiseDatabase()
	{
		$this->Database(
			$this->DataStore()->
				selectDB(
					$this->mDatabase ) );
	}
	protected function _UnserialiseUserContainer()
	{
		$this->UsersContainer(
			new CMongoContainer(
				$this->Database()->
					selectCollection(
						$this->mUsersContainer ) ) );
	}
}


/*=======================================================================================
 *	TEST USER PERSISTENT OBJECTS														*
 *======================================================================================*/

//
// Start session.
//
session_start();

//
// Init local storage.
//
$inited = FALSE;
$offsets = $_SESSION;

//
// Test class.
//
try
{
	//
	// First run.
	//
	if( ! array_key_exists( 'COUNTER', $_SESSION ) )
	{
		//
		// Mark inited.
		//
		$inited = TRUE;
		
		//
		// Initialise session counter.
		//
		$_SESSION[ 'COUNTER' ] = 1;
	
	} // Missing counter.
	
	else
		$_SESSION[ 'COUNTER' ]++;
	
	//
	// Session object.
	//
	if( array_key_exists( 'reset', $_REQUEST )					// Reset request,
	 || (! array_key_exists( kDEFAULT_SESSION, $_SESSION )) )	// or missing session.
	{
		//
		// Initialise session counter.
		//
		if( array_key_exists( 'reset', $_REQUEST ) )
			$_SESSION[ 'COUNTER' ] = 1;
	
		//
		// Initialise session object.
		//
		$_SESSION[ kDEFAULT_SESSION ] = new TestClass();
	
		//
		// Create test user.
		// We make the check so that you can just comment the above line.
		//
		if( array_key_exists( kDEFAULT_SESSION, $_SESSION ) )
		{
			$user = new CUser();
			$user->Code( 'test' );
			$user->Password( 'test' );
			$user->Name( 'Milko Škofič' );
			$user->Email( 'm.skofic@cgiar.org' );
			$user->Role( array( kROLE_FILE_IMPORT, kROLE_USER_MANAGE ), TRUE );
			$user->Commit( $_SESSION[ kDEFAULT_SESSION ]->UsersContainer() );
			
			$_SESSION[ 'USER' ] = $user;
		
		} // Has session object.
		
	} // Missing session object
	
	//
	// Load user.
	//
	if( array_key_exists( 'code', $_REQUEST )
	 && array_key_exists( 'pass', $_REQUEST )
	 && array_key_exists( kDEFAULT_SESSION, $_SESSION ) )
	{
		//
		// Look for user.
		//
		$found
			= $_SESSION[ kDEFAULT_SESSION ]
				->Login( $_REQUEST[ 'code' ], $_REQUEST[ 'pass' ] );
		
		//
		// Set user.
		//
		if( $found )
			$_SESSION[ kDEFAULT_SESSION ]
				->User( $found );
	
	} // Provided credentials.
}

//
// Catch exceptions.
//
catch( Exception $error )
{
	echo( CException::AsHTML( $error ) );
	echo( '<pre>'.(string) $error.'</pre>' );
	echo( '<hr>' );
	
	exit;
}

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Test CSessionObject (pong)</title>
	</head>
	
	<body>
		
		<!-- ------------------------------------------------------------------------- --
		  -- INITED?																   --
		  -- ------------------------------------------------------------------------- -->
		<?php
			if( $inited )
				echo( '<h3>First run</h3>' );
			else
				echo( '<h3>Next run</h3>' );
		?>
		
		<!-- ------------------------------------------------------------------------- --
		  -- LOGGED?																   --
		  -- ------------------------------------------------------------------------- -->
		<?php
			if( array_key_exists( kDEFAULT_SESSION, $_SESSION )
			 && $_SESSION[ kDEFAULT_SESSION ][ kSESSION_USER_LOGGED ] )
				echo( '<h3>User logged</h3>' );
		?>
		
		<!-- ------------------------------------------------------------------------- --
		  -- PING PONG																   --
		  -- ------------------------------------------------------------------------- -->
		<form action="test_CSessionObject.ping.php" method="post">
			<button type="submit">
				PING
			</button>
		</form>
		
		<!-- ------------------------------------------------------------------------- --
		  -- SESSION OFFSETS														   --
		  -- ------------------------------------------------------------------------- -->
		<h4>Session offsets:</h4>
		<pre><?php print_r( $offsets ); ?></pre>
		<hr />
		
		<!-- ------------------------------------------------------------------------- --
		  -- SESSION DATA															   --
		  -- ------------------------------------------------------------------------- -->
		<h4>Session data:</h4>
		<pre><?php print_r( $_SESSION ); ?></pre>
		<hr />

  </body>
</html>