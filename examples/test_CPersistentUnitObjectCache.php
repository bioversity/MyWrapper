<?php

/**
 * {@link CPersistentUnitObjectCache.php Base} object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * base object {@link CPersistentUnitObjectCache class}.
 *
 *	@package	Test
 *	@subpackage	Entities
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 14/03/2012
 */

/*=======================================================================================
 *																						*
 *							test_CPersistentUnitObjectCache.php							*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."CPersistentUnitObjectCache.php" );

//
// Annex includes.
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
	// Create test objects.
	//
	$object1 = new CEntity();
	$object1->Code( 'JOHN' );
	$object1->Name( 'John Smith' );
	$object1->Email( 'j.smith@cgiar.org' );

	$object2 = new CEntity();
	$object2->Code( 'MILKO' );
	$object2->Name( 'Milko Skofic' );
	$object2->Email( 'm.skofic@cgiar.org' );

	$object3 = new CPersistentUnitObject();
	$object3[ 'Uno' ] = 1;
	$object3[ 'Due' ] = 2;
	$object3[ 'Tre' ] = 3;

	//
	// Test instantiation.
	//
	echo( '<h3>Instantiation</h3>' );
	
	echo( '<i>new CPersistentUnitObjectCache();</i><br>' );
	$test = new CPersistentUnitObjectCache();
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$test->Item( $object1, TRUE );</i><br>' );
	$test->Item( $object1, TRUE );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
exit;
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
