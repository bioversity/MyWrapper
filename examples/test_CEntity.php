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
class MyClass extends CEntity
{
	public function Index( $object )
	{
		return $this->_ObjectIndex( $object );
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
	// Instantiate Mongo database.
	//
	$mongo = New Mongo();
	
	//
	// Select database.
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
	echo( '<i>$entity1 = new CEntity( $container );</i><br>' );
	$entity1 = new CEntity( $container );
	echo( '<pre>' ); print_r( $entity1 ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$entity2 = new CEntity();</i><br>' );
	$entity2 = new CEntity();
	echo( '<i>$entity2->Code( \'JOHN\' );</i><br>' );
	$entity2->Code( 'JOHN' );
	echo( '<i>$entity2->Name( \'John Smith\' );</i><br>' );
	$entity2->Name( 'John Smith' );
	echo( '<i>$entity2->Type( \'PERSON\', TRUE );</i><br>' );
	$entity2->Type( 'PERSON', TRUE );
	echo( '<i>$entity2->Type( \'USER\', TRUE );</i><br>' );
	$entity2->Type( 'USER', TRUE );
	echo( '<i>$entity2->Reference( \'COLL\', $entity1, TRUE );</i><br>' );
	$entity2->Reference( 'COLL', $entity1, TRUE );
	echo( '<pre>' ); print_r( $entity2 ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$entity3 = new CEntity();</i><br>' );
	$entity3 = new CEntity();
	echo( '<i>$entity3->Code( \'LUCA\' );</i><br>' );
	$entity3->Code( 'LUCA' );
	echo( '<i>$entity3->Name( \'Luca Matteis\' );</i><br>' );
	$entity3->Name( 'Luca Matteis' );
	echo( '<i>$entity3->Reference( NULL, $entity2, TRUE );</i><br>' );
	$entity3->Reference( NULL, $entity2, TRUE );
	echo( '<pre>' ); print_r( $entity3 ); echo( '</pre>' );
	echo( '<hr>' );
	 
	//
	// Test persistence.
	//
	echo( '<h3>Persistence</h3>' );

	echo( '<i>$identifier = $entity3->Commit( $collection );</i><br>' );
	$identifier = $entity3->Commit( $collection );
	echo( "$identifier<pre>" ); print_r( $entity3 ); echo( '</pre>' );
	echo( "entity1<pre>" ); print_r( $entity1 ); echo( '</pre>' );
	echo( "entity2<pre>" ); print_r( $entity2 ); echo( '</pre>' );
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
