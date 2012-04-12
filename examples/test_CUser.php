<?php

/**
 * {@link CUser.php Base} object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * base object {@link CUser class}.
 *
 *	@package	Test
 *	@subpackage	Entities
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 21/12/2012
 */

/*=======================================================================================
 *																						*
 *										test_CUser.php									*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."CUser.php" );


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
	$collection = new CMongoContainer( $db->selectCollection( CUser::DefaultContainer() ) );
	 
	//
	// Test instantiation.
	//
	echo( '<h3>Instantiation</h3>' );
	
	echo( '<i>new CUser();</i><br>' );
	$test = new CUser();
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	
	$container = array( kTAG_CODE => 'Milko',
						kOFFSET_PASSWORD => 'Secret',
						kTAG_NAME => 'Milko A. Škofič' );
	echo( 'Container:<pre>' ); print_r( $container ); echo( '</pre>' );
	echo( '<i>$test = new CUser( $container );</i><br>' );
	$test = new CUser( $container );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	
	echo( '<i>$test = new CUser();</i><br>' );
	echo( '<i>$test->Code( \'JOHN\' );</i><br>' );
	echo( '<i>$test->Password( \'unknown\' );</i><br>' );
	echo( '<i>$test->Name( \'John Smith\' );</i><br>' );
	echo( '<i>$test->Email( \'m.skofic@cgiar.org\' );</i><br>' );
	$test = new CUser();
	$test->Code( 'JOHN' );
	$test->Password( 'unknown' );
	$test->Name( 'John Smith' );
	$test->Email( 'm.skofic@cgiar.org' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	 
	//
	// Test persistence.
	//
	echo( '<h3>Persistence</h3>' );

	$container = array( kTAG_CODE => 'Milko',
						kOFFSET_PASSWORD => 'Secret',
						kTAG_NAME => 'Milko A. Škofič',
						kOFFSET_EMAIL => 'm.skofic@cgiar.org',
						kOFFSET_ID => new CDataTypeBinary( md5( 'Milko', TRUE ) ) );
	echo( "Container<pre>" ); print_r( $container ); echo( '</pre>' );
	echo( '<i>$test = new CUser( $container );</i><br>' );
	echo( '<i>$identifier = $test->Commit( $collection );</i><br>' );
	$test = new CUser( $container );
	$milko = $test->Commit( $collection );
	echo( "$milko<pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );

	$container = array( kOFFSET_PASSWORD => 'Secret',
						kTAG_NAME => 'Luca Matteis',
						kOFFSET_EMAIL => 'l.matteis@cgiar.org' );
	echo( "Container<pre>" ); print_r( $container ); echo( '</pre>' );
	echo( '<i>$test = new CUser( $container );</i><br>' );
	echo( '<i>$identifier = $test->Commit( $collection );</i><br>' );
	$test = new CUser( $container );
	$luca = $test->Commit( $collection );
	echo( "$luca<pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	 
	try
	{
		echo( '<i>$test[ kTAG_NAME ] = NULL;</i><br>' );
		echo( '<i>$test->Commit( $collection );</i><br>' );
		$test[ kTAG_NAME ] = NULL;
		$test->Commit( $collection );
		echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );
		echo( '<br>' );
	}
	echo( '<hr>' );

	echo( '<i>$test = new CUser( $collection, $luca );</i><br>' );
	$test = new CUser( $collection, $luca, kFLAG_STATE_ENCODED );
	echo( "$luca<pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
exit;

	echo( '<i>$test = new CUser( $collection, new CDataTypeBinary( md5( \'Milko\', TRUE ) ) );</i><br>' );
	$test = new CUser( $collection, new CDataTypeBinary( md5( 'Milko', TRUE ) ) );
	echo( "$identifier<pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>$test = CUser::NewObject( $collection, new MongoBinData( md5( \'Milko\', TRUE ) ) );</i><br>' );
	$test = CUser::NewObject( $collection, new MongoBinData( md5( 'Milko', TRUE ) ) );
	echo( "$identifier<pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );

	echo( '<i>$test = CUser::NewObject( $collection, \'Milko\' );</i><br>' );
	$test = CUser::NewObject( $collection, 'Milko' );
	echo( "$identifier<pre>" ); print_r( $test ); echo( '</pre>' );
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
