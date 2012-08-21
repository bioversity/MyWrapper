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
	
	protected function _SerialiseDataStore( &$theData )
	{
		$this->DataStore( FALSE );
	}
	protected function _SerialiseGraphStore( &$theData )
	{
		$this->GraphStore( FALSE );
	}
	protected function _SerialiseDatabase( &$theData )
	{
		$theData[ 'mDatabase' ] = 'TEST';
	}
	protected function _SerialiseUserContainer( &$theData )
	{
		$theData[ 'mUsersContainer' ] = 'CSessionObject';
	}
	
	protected function _UnserialiseDataStore( &$theData )
	{
		$this->_InitDataStore();
	}
	protected function _UnserialiseGraphStore( &$theData )
	{
		$this->_InitGraphStore();
	}
	protected function _UnserialiseDatabase( &$theData )
	{
		if( array_key_exists( 'mDatabase', $theData ) )
			$this->Database(
				$this->DataStore()->
					selectDB(
						$theData[ 'mDatabase' ] ) );
	}
	protected function _UnserialiseUserContainer( &$theData )
	{
		if( array_key_exists( 'mUsersContainer', $theData ) )
			$this->UsersContainer(
				new CMongoContainer(
					$this->Database()->
						selectCollection(
							$theData[ 'mUsersContainer' ] ) ) );
	}
}


/*=======================================================================================
 *	TEST USER PERSISTENT OBJECTS														*
 *======================================================================================*/

session_start();

$inited = FALSE;

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
		// Mark inited.
		//
		$inited = TRUE;
		
		//
		// Initialise session.
		//
		$_SESSION[ kDEFAULT_SESSION ] = new TestClass();
	
		//
		// Create user 1.
		//
		$user = new CUser();
		$user->Code( 'guest' );
		$user->Password( 'guest' );
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
		<title>Test CSessionObject</title>
	</head>
	
	<body>
		
		<!-- ------------------------------------------------------------------------- --
		  -- INITED?																   --
		  -- ------------------------------------------------------------------------- -->
		<?php if( $inited ) echo( '<h3>Inited</h3>' ); ?>
		
		<!-- ------------------------------------------------------------------------- --
		  -- PING PONG																   --
		  -- ------------------------------------------------------------------------- -->
		<form action="test_CSessionObject.php" method="post">
			<button type="submit">
				GO
			</button>
		</form>
		
		<!-- ------------------------------------------------------------------------- --
		  -- SESSION DATA															   --
		  -- ------------------------------------------------------------------------- -->
		<pre><?php print_r( $_SESSION ); ?></pre>
		
		<hr />
		
		<!-- ------------------------------------------------------------------------- --
		  -- SESSION JSON															   --
		  -- ------------------------------------------------------------------------- -->
		<pre><?php echo( (string) $_SESSION[ kDEFAULT_SESSION ] ); ?></pre>

  </body>
</html>