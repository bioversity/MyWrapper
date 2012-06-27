<?php

/**
 * {@link CMongoGridContainer.php Base} object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * base object {@link CMongoGridContainer class}.
 *
 *	@package	Test
 *	@subpackage	Framework
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 27/06/2012
 */

/*=======================================================================================
 *																						*
 *								test_CMongoGridContainer.php							*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."CMongoGridContainer.php" );


/*=======================================================================================
 *	TEST																				*
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
	// Select test collection.
	//
	$collection = $db->getGridFS();
	 
	//
	// Create.
	//
	echo( '<h3>Create</h3>' );
	
	echo( '<i>$test = new CMongoGridContainer();</i><br>' );
	$test = new CMongoGridContainer();
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );

	//
	// Data members.
	//
	echo( '<h3>Data members</h3>' );
	
	echo( '<i>$test->Container( $collection );</i><br>' );
	$test->Container( $collection );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$x = (string) $test;</i><br>' );
	$x = (string) $test;
	echo( '<pre>' ); print_r( $x ); echo( '</pre>' );
	echo( '<i>$x = $test->Database();</i><br>' );
	$x = $test->Database();
	echo( '<pre>' ); print_r( $x ); echo( '</pre>' );
	echo( '<pre>' ); print_r( (string) $x ); echo( '</pre>' );
	echo( '<i>$x = $test->Container();</i><br>' );
	$x = $test->Container();
	echo( '<pre>' ); print_r( $x ); echo( '</pre>' );
	echo( '<hr>' );

	//
	// Add file.
	//
	echo( '<h3>Add file</h3>' );
	
	echo( '<i>$file = new SplFileInfo( __FILE__ );</i><br>' );
	$file = new SplFileInfo( __FILE__ );
	echo( '<pre>' ); print_r( $file ); echo( '</pre>' );
	echo( '<i>$metadata = array( \'TYPE\' => \'FILE\', \'NUMBER\' => 1 );</i><br>' );
	$metadata = array( 'TYPE' => 'FILE', 'NUMBER' => 1 );
	echo( '<pre>' ); print_r( $metadata ); echo( '</pre>' );
	echo( '<i>$id_file = $test->Commit( $file, $metadata );</i><br>' );
	$id_file = $test->Commit( $file, $metadata );
	echo( '<pre>' ); print_r( $id_file ); echo( '</pre>' );
	echo( '<hr>' );

	//
	// Add data.
	//
	echo( '<h3>Add data</h3>' );
	
	echo( '<i>$data = \'This is some data\';</i><br>' );
	$data = 'This is some data';
	echo( '<pre>' ); print_r( $data ); echo( '</pre>' );
	echo( '<i>$metadata = array( \'TYPE\' => \'DATA\', \'NUMBER\' => 2 );</i><br>' );
	$metadata = array( 'TYPE' => 'DATA', 'NUMBER' => 2 );
	echo( '<pre>' ); print_r( $metadata ); echo( '</pre>' );
	echo( '<i>$id_data = $test->Commit( $data, $metadata );</i><br>' );
	$id_data = $test->Commit( $data, $metadata );
	echo( '<pre>' ); print_r( $id_data ); echo( '</pre>' );
	echo( '<hr>' );

	//
	// Retrieve file.
	//
	echo( '<h3>Retrieve file</h3>' );
	
	echo( '<i>$found = $test->Load( $id_file );</i><br>' );
	$found = $test->Load( $id_file );
	echo( '<pre>' ); print_r( $found ); echo( '</pre>' );

	//
	// Retrieve data.
	//
	echo( '<h3>Retrieve data</h3>' );
	
	echo( '<i>$found = $test->Load( $id_data );</i><br>' );
	$found = $test->Load( $id_data );
	echo( '<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<i>$data = $found->getBytes();</i><br>' );
	$data = $found->getBytes();
	echo( '<pre>' ); print_r( $data ); echo( '</pre>' );

	//
	// Delete file.
	//
	echo( '<h3>Delete file</h3>' );
	
	echo( '<i>$found = $test->Delete( $id_file );</i><br>' );
	$found = $test->Delete( $id_file );
	echo( '<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<i>$found = $test->Load( $id_file );</i><br>' );
	$found = $test->Load( $id_file );
	echo( 'Found: <pre>' ); print_r( $found ); echo( '</pre>' );

	//
	// Delete data.
	//
	echo( '<h3>Delete data</h3>' );
	
	echo( '<i>$found = $test->Delete( $id_data );</i><br>' );
	$found = $test->Delete( $id_data );
	echo( '<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<i>$found = $test->Load( $id_data );</i><br>' );
	$found = $test->Load( $id_data );
	echo( 'Found: <pre>' ); print_r( $found ); echo( '</pre>' );
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
