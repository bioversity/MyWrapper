<?php

/**
 * {@link CEntity.php Base} object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * base object {@link CEntity class}.
 *
 *	@package	Test
 *	@subpackage	Entities
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 21/12/2012
 */

/*=======================================================================================
 *																						*
 *									test_CEntity.php									*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."CEntity.php" );


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
	$db = $mongo->selectDB( "TEST" );
	
	//
	// Drop database.
	//
	$db->drop();
	
	//
	// Instantiate CMongoContainer.
	//
	$collection = new CMongoContainer( $db->selectCollection( 'CEntity' ) );
	 
	//
	// Test instantiation.
	//
	echo( '<h3>Instantiation</h3>' );
	
	echo( '<i>new CEntity();</i><br>' );
	$test = new CEntity();
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	$container = array( kTAG_CODE => 'Milko',
						kTAG_NAME => 'Milko A. Škofič' );
	echo( 'Container:<pre>' ); print_r( $container ); echo( '</pre>' );
	echo( '<i>$test = new CEntity( $container );</i><br>' );
	$test = new CEntity( $container );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$test = new CEntity();</i><br>' );
	echo( '<i>$test->Code( \'JOHN\' );</i><br>' );
	echo( '<i>$test->Password( \'unknown\' );</i><br>' );
	echo( '<i>$test->Name( \'John Smith\' );</i><br>' );
	echo( '<i>$test->Email( \'m.skofic@cgiar.org\' );</i><br>' );
	$test = new CEntity();
	$test->Code( 'JOHN' );
	$test->Name( 'John Smith' );
	$test->Email( 'm.skofic@cgiar.org' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	 
	//
	// Test persistence.
	//
	echo( '<h3>Persistence</h3>' );

	echo( '<i>$identifier = $test->Commit( $collection );</i><br>' );
	$identifier = $test->Commit( $collection );
	echo( "$identifier<pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	 
	try
	{
		echo( '<i>$test->Name( FALSE );</i><br>' );
		$test->Name( FALSE );
		echo( '<i>$test->Commit( $collection );</i><br>' );
		$test->Commit( $collection );
		echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );
		echo( '<br>' );
	}
	echo( '<hr>' );
	
	echo( '<i>$test = new CEntity( $collection, $identifier );</i><br>' );
	$test = new CEntity( $collection, $identifier );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$test = CPersistentUnitObject::NewObject( $collection, $identifier );</i><br>' );
	$test = CPersistentUnitObject::NewObject( $collection, $identifier );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	 
	//
	// Test elements.
	//
	echo( '<h3>Test elements</h3>' );
	
	echo( '<i>$test->Mail( NULL, \'Default address\' );</i><br>' );
	$test->Mail( NULL, 'Default address' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$test->Mail( \'Home\', \'Home address\' );</i><br>' );
	$test->Mail( 'Home', 'Home address' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$found = $test->Mail( \'Home\' );</i><br>' );
	$found = $test->Mail( 'Home' );
	echo( '<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$found = $test->Mail();</i><br>' );
	$found = $test->Mail();
	echo( '<pre>' ); print_r( $found ); echo( '</pre>' );
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
