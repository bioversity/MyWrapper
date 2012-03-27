<?php

/**
 * {@link CPersistentObject.php Base} object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * base object {@link CPersistentObject class}.
 *
 *	@package	Test
 *	@subpackage	Persistence
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 13/12/2012
 */

/*=======================================================================================
 *																						*
 *								test_CPersistentObject.php								*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."CPersistentObject.php" );


/*=======================================================================================
 *	TEST CLASS																			*
 *======================================================================================*/
 
//
// Test class.
//
class MyClass extends CPersistentObject
{
	protected function _PrepareCommit( &$theContainer, &$theIdentifier, &$theModifiers )
	{
		$this->_isInited( TRUE );
		parent::_PrepareCommit( $theContainer, $theIdentifier, $theModifiers );
	}
}


/*=======================================================================================
 *	TEST DEFAULT EXCEPTIONS																*
 *======================================================================================*/
 
//
// Instantiate test class.
//
$test = new MyClass();

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
	$db = $mongo->selectDB( "TEST" );
	
	//
	// Drop database.
	//
	$db->drop();
	
	//
	// Instantiate CMongoContainer.
	//
	$mcontainer = new CMongoContainer( $db->selectCollection( 'CPersistentObject' ) );
	 
	//
	// Test object content.
	//
	echo( '<h3>Object content</h3>' );
	
	echo( '<i>Empty object</i><br>' );
	echo( '<i>$test = new MyClass();</i><br>' );
	$test = new MyClass();
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>From array</i><br>' );
	echo( '<i>$content = array( \'Name\' => \'Milko\' );</i><br>' );
	$content = array( 'Name' => 'Milko' );
	echo( '<i>$test = new MyClass( $content ) );</i><br>' );
	$test = new MyClass( $content );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>From ArrayObject</i><br>' );
	echo( '<i>$content = new ArrayObject( array( \'Name\' => \'Milko\' ) );</i><br>' );
	$content = new ArrayObject( array( 'Name' => 'Milko' ) );
	echo( '<i>$test = new MyClass( $content ) );</i><br>' );
	$test = new MyClass( $content );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	try
	{
		echo( '<i>From any other type</i><br>' );
		echo( '<i>$content = 10;</i><br>' );
		$content = 10;
		echo( '<i>$test = new MyClass( $content ) );</i><br>' );
		$test = new MyClass( $content );
		echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );
		echo( '<br>' );
	}
	echo( '<hr>' );
	echo( '<hr>' );
	
	//
	// Test container content.
	//
	echo( '<h3>Container content</h3>' );
	
	echo( '<i>Load from ArrayObject container</i><br>' );
	echo( '<i>$container = new CArrayContainer( array( array( \'NAME\' => \'Milko\', \'SURNAME\' => \'Skofic\' ) ) );</i><br>' );
	$container = new CArrayContainer( array( array( 'NAME' => 'Milko', 'SURNAME' => 'Skofic' ) ) );
	echo( 'Container:<pre>' ); print_r( $container ); echo( '</pre>' );
	echo( '<i>$test = new MyClass( $container, 0 );</i><br>' );
	$test = new MyClass( $container, 0 );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Not found</i><br>' );
	echo( '<i>$test = new MyClass( $acontainer, 1 );</i><br>' );
	$test = new MyClass( $container, 1 );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	try
	{
		echo( '<i>Invalid container</i><br>' );
		echo( '<i>$acontainer = $container->Container();</i><br>' );
		$acontainer = $container->Container();
		echo( 'Container:<pre>' ); print_r( $acontainer ); echo( '</pre>' );
		echo( '<i>$test = new MyClass( $acontainer, 0 );</i><br>' );
		$test = new MyClass( $acontainer, 0 );
		echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );
		echo( '<br>' );
	}
	echo( '<hr>' );
	echo( '<hr>' );
	
	//
	// Commit to container.
	//
	echo( '<h3>Commit to container</h3>' );
	
	echo( '<i>Store in array object</i><br>' );
	echo( '<i>$test = new MyClass( array( \'NAME\' => \'Pippo\', \'SURNAME\' => \'Franco\' ) );</i><br>' );
	$test = new MyClass( array( 'NAME' => 'Pippo', 'SURNAME' => 'Franco' ) );
	echo( '<i>$container = new CArrayContainer();</i><br>' );
	$container = new CArrayContainer();
	echo( '<i>$found = $test->Commit( $container );</i><br>' );
	$found = $test->Commit( $container );
	echo( 'Container:<pre>' ); print_r( $container ); echo( '</pre>' );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Should not store</i><br>' );
	echo( '<i>$found = $test->Commit( $container, 1 );</i><br>' );
	$found = $test->Commit( $container, 1 );
	echo( 'Container:<pre>' ); print_r( $container ); echo( '</pre>' );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Store with different index</i><br>' );
	echo( '<i>$test->Uncommit();</i><br>' );
	$test->Uncommit();
	echo( '<i>$found = $test->Commit( $container, 1 );</i><br>' );
	$found = $test->Commit( $container, 1 );
	echo( 'Container:<pre>' ); print_r( $container ); echo( '</pre>' );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Store in CMongoContainer</i><br>' );
	echo( '<i>$test->Uncommit();</i><br>' );
	$test->Uncommit();
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$found = $test->Commit( $mcontainer );</i><br>' );
	$found = $test->Commit( $mcontainer );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Store with different index</i><br>' );
	echo( '<i>$test->Uncommit();</i><br>' );
	$test->Uncommit();
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$found = $test->Commit( $mcontainer, 1 );</i><br>' );
	$found = $test->Commit( $mcontainer, 1 );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	echo( '<hr>' );
	
	//
	// Update object.
	//
	echo( '<h3>Update object</h3>' );
		
	echo( '<i>Update object</i><br>' );
	$test[ 'Version' ] = 0;
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$found = $test->Commit( $mcontainer, 1 );</i><br>' );
	$found = $test->Commit( $mcontainer, 1 );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<i>$test = new MyClass( $mcontainer, 1 );</i><br>' );
	$test = new MyClass( $mcontainer, 1 );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Update object</i><br>' );
	$test[ 'Version' ] = 1;
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$found = $test->Commit( $mcontainer );</i><br>' );
	$found = $test->Commit( $mcontainer );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<i>$test = new MyClass( $mcontainer, 1 );</i><br>' );
	$test = new MyClass( $mcontainer, 1 );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	echo( '<hr>' );
	
	//
	// Test array container.
	//
	echo( '<h3>Test array container</h3>' );
	
	echo( '<i>Append to CArrayContainer</i><br>' );
	echo( '<i>$test = new MyClass( array( \'NAME\' => \'Baba\', \'SURNAME\' => \'Bubu\' ) );</i><br>' );
	$test = new MyClass( array( 'NAME' => 'Baba', 'SURNAME' => 'Bubu' ) );
	echo( '<i>$found = $test->Commit( $container );</i><br>' );
	$found = $test->Commit( $container );
	echo( 'Container:<pre>' ); print_r( $container ); echo( '</pre>' );
	echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>$test->Uncommit();</i><br>' );
	$test->Uncommit();
	echo( '<i>$found = $test->Commit( $container );</i><br>' );
	$found = $test->Commit( $container );
	echo( 'Container:<pre>' ); print_r( $container ); echo( '</pre>' );
	echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
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
