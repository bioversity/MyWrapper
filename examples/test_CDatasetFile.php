<?php
	
/**
 * {@link CDatasetFile.php Query} object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * {@link CDatasetFile class}.
 *
 *	@package	Test
 *	@subpackage	Persistence
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 03/04/2012
 */

/*=======================================================================================
 *																						*
 *									test_CDatasetFile.php								*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."CDatasetFile.php" );


/*=======================================================================================
 *	TEST MAIL ADDRESS OBJECT															*
 *======================================================================================*/
 
//
// TRY BLOCK.
//
try
{
	//
	// Instantiate empty object.
	//
	echo( '<i>$test = new CDatasetFile();</i><br>' );
	$test = new CDatasetFile();
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Add file.
	//
	echo( '<i>$test->File( \'File 1\' );</i><br>' );
	$test->File( 'File 1' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Add references.
	//
	echo( '<i>$test->Referenced( \'Ref 1\', TRUE );</i><br>' );
	$test->Referenced( 'Ref 1', TRUE );
	echo( '<i>$test->Referenced( \'Ref 2\', TRUE );</i><br>' );
	$test->Referenced( 'Ref 2', TRUE );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Add status.
	//
	echo( '<i>$test->Status( \'Original\', TRUE );</i><br>' );
	$test->Status( 'Original', TRUE );
	echo( '<i>$test->Status( \'Processed\', TRUE );</i><br>' );
	$test->Status( 'Processed', TRUE );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Add kind.
	//
	echo( '<i>$test->Kind( \'Kind 1\', TRUE );</i><br>' );
	$test->Kind( 'Kind 1', TRUE );
	echo( '<i>$test->Kind( \'Kind 2\', TRUE );</i><br>' );
	$test->Kind( 'Kind 2', TRUE );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Add columns.
	//
	echo( '<i>$test->Column( 0, \'Tag 1\', \'Title 1\' );</i><br>' );
	$test->Column( 0, 'Tag1', 'Title 1' );
	echo( '<i>$test->Column( 1, \'Tag 2\', \'Title 2\' );</i><br>' );
	$test->Column( 1, 'Tag2', 'Title 2' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Modify columns.
	//
	echo( '<i>$test->Column( 0, \'Tag 0\' );</i><br>' );
	$test->Column( 0, 'Tag 0' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Retrieve column.
	//
	echo( '<i>$found = $test->Column( 1 );</i><br>' );
	$found = $test->Column( 1 );
	echo( '<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Retrieve columns.
	//
	echo( '<i>$found = $test->Column();</i><br>' );
	$found = $test->Column();
	echo( '<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Delete column.
	//
	echo( '<i>$test->Column( 1, FALSE );</i><br>' );
	$test->Column( 1, FALSE );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Instantiate from array.
	//
	echo( '<i>$test = new CDatasetFile( $test );</i><br>' );
	$test = new CDatasetFile( $test );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<h3>DONE</h3>' );
}
catch( Exception $error )
{
	echo( '<h3>Unexpected exception</h3>' );
	echo( CException::AsHTML( $error ) );
	echo( '<pre>'.(string) $error.'</pre>' );
	echo( '<hr>' );
}

?>
