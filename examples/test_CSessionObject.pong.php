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
		
		$this->_SerialiseGraphStore();
		$this->_SerialiseUserContainer();
		$this->_SerialiseDatabase();
	}
	protected function _SerialiseGraphStore()
	{
		$data = $this->GraphStore();
		if( $data !== NULL )
			$this->GraphStore( $data->getTransport() );
	}
	protected function _SerialiseUserContainer()
	{
		$data = $this->UsersContainer();
		if( $data !== NULL )
			$this->UsersContainer( $data->Container()->getName() );
	}
	protected function _SerialiseDatabase()
	{
		$data = $this->Database();
		if( $data !== NULL )
			$this->Database( (string) $data );
	}
	
	protected function _Unserialise()
	{
		$this->_InitDataStore();
		$this->_UnserialiseGraphStore();
		$this->_UnserialiseDatabase();
		$this->_UnserialiseUserContainer();
		
		parent::_Unserialise();
	}
	protected function _UnserialiseGraphStore()
	{
		$data = $this->GraphStore();
		if( $data !== NULL )
			$this->GraphStore(
				new Everyman\Neo4j\Client(
					$data ) );
	}
	protected function _UnserialiseDatabase()
	{
		$data = $this->Database();
		if( $data !== NULL )
			$this->Database(
				$this->DataStore()->
					selectDB(
						$data ) );
	}
	protected function _UnserialiseUserContainer()
	{
		$data = $this->UsersContainer();
		if( $data !== NULL )
			$this->UsersContainer(
				new CMongoContainer(
					$this->Database()-> 
						selectCollection(
							$data ) ) );
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
$inited_counter = $inited_session = $reset = FALSE;

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
		$inited_counter = TRUE;
		
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
		// Mark inited.
		//
		$inited_session = TRUE;
		
		//
		// Initialise session counter.
		//
		if( array_key_exists( 'reset', $_REQUEST ) )
		{
			//
			// Mark inited.
			//
			$reset = TRUE;
			
			//
			// Reset session.
			//
			$_SESSION = Array();
			
			//
			// Init counter.
			//
			$_SESSION[ 'COUNTER' ] = 1;
		}
	
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
		  -- PING PONG																   --
		  -- ------------------------------------------------------------------------- -->
		<form action="test_CSessionObject.ping.php" method="post">
			<button type="submit">
				PING
			</button>
		</form>
		
		<!-- ------------------------------------------------------------------------- --
		  -- INITED?																   --
		  -- ------------------------------------------------------------------------- -->
		<?php
			if( $inited_counter )
				echo( '<h3>Inited counter</h3>' );
			else
				echo( '<h3>Counter exists</h3>' );

			if( $reset )
				echo( '<h3>Reset</h3>' );
			else
				echo( '<h3>Not reset</h3>' );

			if( $inited_session )
				echo( '<h3>Inited session</h3>' );
			else
				echo( '<h3>Session exists</h3>' );
		?>
		
		<!-- ------------------------------------------------------------------------- --
		  -- LOGGED?																   --
		  -- ------------------------------------------------------------------------- -->
		<?php
			if( array_key_exists( kDEFAULT_SESSION, $_SESSION )
			 && ($_SESSION[ kDEFAULT_SESSION ] instanceof TestClass)
			 && $_SESSION[ kDEFAULT_SESSION ][ kSESSION_USER_LOGGED ] )
				echo( '<h3>User logged</h3>' );
			else
				echo( '<h3>User NOT logged</h3>' );
		?>
		
		<!-- ------------------------------------------------------------------------- --
		  -- SESSION DATA															   --
		  -- ------------------------------------------------------------------------- -->
		<h4>Session data:</h4>
		<pre><?php print_r( $_SESSION ); ?></pre>
		<hr />

  </body>
</html>