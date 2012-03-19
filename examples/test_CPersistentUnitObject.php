<?php

/**
 * {@link CPersistentUnitObject.php Base} object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * base object {@link CPersistentUnitObject class}.
 *
 *	@package	Test
 *	@subpackage	Persistence
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 13/12/2012
 */

/*=======================================================================================
 *																						*
 *							test_CPersistentUnitObject.php								*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."CPersistentUnitObject.php" );


/*=======================================================================================
 *	TEST CLASS																			*
 *======================================================================================*/
 
//
// Test class.
//
class MyClass extends CPersistentUnitObject
{
	public function Reference( $theValue, $theOperation = NULL, $getOld = FALSE )
	{
		return $this->_ManageObjectList
				( 'REFERENCE', $theValue, $theOperation, $getOld );
	}
	
	protected function _PrepareStore( &$theContainer, &$theIdentifier )
	{
		$this->_isInited( TRUE );
		parent::_PrepareStore( $theContainer, $theIdentifier );
		$this->_PrepareReferenceList($theContainer, 'REFERENCE', kFLAG_REFERENCE_MASK );
	}
}


/*=======================================================================================
 *	TEST DEFAULT EXCEPTIONS																*
 *======================================================================================*/
 
//
// Instantiate test class.
//
$test = new CPersistentUnitObject();

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
	$mcontainer = new CMongoContainer( $db->selectCollection( 'CPersistentUnitObject' ) );
	 
	//
	// Test object content.
	//
	echo( '<h3>Object content</h3>' );
	
	echo( '<i>Empty object</i><br>' );
	echo( '<i>$test = new CPersistentUnitObject();</i><br>' );
	$test = new CPersistentUnitObject();
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>From array</i><br>' );
	echo( '<i>$content = array( \'Name\' => \'Milko\' );</i><br>' );
	$content = array( 'Name' => 'Milko' );
	echo( '<i>$test = new CPersistentUnitObject( $content ) );</i><br>' );
	$test = new CPersistentUnitObject( $content );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>From ArrayObject</i><br>' );
	echo( '<i>$content = new ArrayObject( array( \'Name\' => \'Milko\' ) );</i><br>' );
	$content = new ArrayObject( array( 'Name' => 'Milko' ) );
	echo( '<i>$test = new CPersistentUnitObject( $content ) );</i><br>' );
	$test = new CPersistentUnitObject( $content );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	try
	{
		echo( '<i>From any other type</i><br>' );
		echo( '<i>$content = 10;</i><br>' );
		$content = 10;
		echo( '<i>$test = new CPersistentUnitObject( $content ) );</i><br>' );
		$test = new CPersistentUnitObject( $content );
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
	
	try
	{
		echo( '<i>Load from ArrayObject container</i><br>' );
		$container = new ArrayObject( array( array( 'NAME' => 'Milko', 'SURNAME' => 'Skofic' ) ) );
		echo( 'Container:<pre>' ); print_r( $container ); echo( '</pre>' );
		echo( '<i>$test = new CPersistentUnitObject( $container, 0 );</i><br>' );
		$test = new CPersistentUnitObject( $container, 0 );
		echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
		echo( '<hr>' );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );
		echo( '<br>' );
	}
	echo( '<hr>' );
	
	echo( '<i>Load from CArrayContainer</i><br>' );
	echo( '<i>$acontainer = new CArrayContainer( $container );</i><br>' );
	$acontainer = new CArrayContainer( $container );
	echo( '<i>$test = new CPersistentUnitObject( $acontainer, 0 );</i><br>' );
	$test = new CPersistentUnitObject( $acontainer, 0 );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Not found</i><br>' );
	echo( '<i>$test = new CPersistentUnitObject( $acontainer, 1 );</i><br>' );
	$test = new CPersistentUnitObject( $acontainer, 1 );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	echo( '<hr>' );
	
	//
	// Commit to container.
	//
	echo( '<h3>Commit to container</h3>' );
	
	echo( '<i>Store in CArrayContainer object</i><br>' );
	echo( '<i>$test = new MyClass( array( \'NAME\' => \'Milko\', \'SURNAME\' => \'Skofic\' ) );</i><br>' );
	$test = new MyClass( array( 'NAME' => 'Milko', 'SURNAME' => 'Skofic' ) );
	echo( '<i>$found = $test->Commit( $acontainer );</i><br>' );
	$found = $test->Commit( $acontainer );
	echo( 'Container:<pre>' ); print_r( $acontainer ); echo( '</pre>' );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Should not store</i><br>' );
	echo( '<i>$found = $test->Commit( $container, 1 );</i><br>' );
	$found = $test->Commit( $container, 1 );
	echo( 'Container:<pre>' ); print_r( $acontainer ); echo( '</pre>' );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Store with different index</i><br>' );
	echo( '<i><b>Note the version changes on both objects since these are stored as references</b></i><br>' );
	echo( '<i>$test->Uncommit();</i><br>' );
	$test->Uncommit();
	echo( '<i>$found = $test->Commit( $acontainer, 2 );</i><br>' );
	$found = $test->Commit( $acontainer, 2 );
	echo( 'Container:<pre>' ); print_r( $acontainer ); echo( '</pre>' );
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
	// Load with object.
	//
	echo( '<h3>Load with object</h3>' );
	
	echo( '<i>Load with object</i><br>' );
	echo( '<i>$test = new MyClass( $mcontainer, $test );</i><br>' );
	$test = new MyClass( $mcontainer, $test );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Load with reference</i><br>' );
	echo( '<i>$ref = array( kTAG_ID_REFERENCE => 1 );</i><br>' );
	$ref = array( kTAG_ID_REFERENCE => 1 );
	echo( '<i>$test = new MyClass( $mcontainer, $ref );</i><br>' );
	$test = new MyClass( $mcontainer, $ref );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// New object.
	//
	echo( '<h3>New object</h3>' );
	
	echo( '<i>New object</i><br>' );
	echo( '<i>$test = MyClass::NewObject( $mcontainer, 1 );</i><br>' );
	$test = MyClass::NewObject( $mcontainer, 1 );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Reference list.
	//
	echo( '<h3>Reference list</h3>' );
	
	//
	// Create references.
	//
	$id1 = 1;
	$ref1 = new MyClass( array( 'Name' => 'Reference 1', kTAG_ID_NATIVE => $id1 ) );
	$ref2 = new MyClass( array( 'Name' => 'Reference 2' ) );
	$ref3 = new MyClass( array( 'Name' => 'Reference 3' ) );
	$id3 = $ref3->Commit( $mcontainer );
	
	echo( '<i>References</i><br>' );
	echo( '1:<pre>' ); print_r( $ref1 ); echo( '</pre>' );
	echo( '2:<pre>' ); print_r( $ref2 ); echo( '</pre>' );
	echo( '3:<pre>' ); print_r( $ref3 ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$res = $test->Reference( $ref1, TRUE );</i><br>' );
	$res = $test->Reference( $ref1, TRUE );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Result:<pre>' ); print_r( $res ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$res = $test->Reference( $ref2, TRUE );</i><br>' );
	$res = $test->Reference( $ref2, TRUE );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Result:<pre>' ); print_r( $res ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$res = $test->Reference( $id1 );</i><br>' );
	$res = $test->Reference( $id1 );
	echo( 'Result:<pre>' ); print_r( $res ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$res = $test->Reference( $ref2 );</i><br>' );
	$res = $test->Reference( $ref2 );
	echo( 'Result:<pre>' ); print_r( $res ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( 'Before:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$id = $test->Commit( $mcontainer );</i><br>' );
	$id = $test->Commit( $mcontainer );
	echo( 'After:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$res = $test->Reference( $id1, FALSE );</i><br>' );
	$res = $test->Reference( $id1, FALSE );
	echo( 'Result:<pre>' ); print_r( $res ); echo( '</pre>' );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$res = $test->Reference( $ref2, FALSE, TRUE );</i><br>' );
	$res = $test->Reference( $ref2, FALSE, TRUE );
	echo( 'Result:<pre>' ); print_r( $res ); echo( '</pre>' );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Typed reference list.
	//
	echo( '<h3>Typed reference list</h3>' );
	
	//
	// Create typed references.
	//
	$ref1 = array( kTAG_TYPE => 'PARENT', kTAG_DATA => $id1 );
	$ref2 = array( kTAG_TYPE => 'CHILD', kTAG_DATA => $ref2 );
	$ref3 = array( kTAG_TYPE => 'CHILD', kTAG_DATA => $id3 );
	$ref4 = array( kTAG_TYPE => 'OTHER', kTAG_DATA => new MyClass( array( 'Name' => 'Reference 4' ) ) );
	
	echo( '<i>References</i><br>' );
	echo( '1:<pre>' ); print_r( $ref1 ); echo( '</pre>' );
	echo( '2:<pre>' ); print_r( $ref2 ); echo( '</pre>' );
	echo( '3:<pre>' ); print_r( $ref3 ); echo( '</pre>' );
	echo( '4:<pre>' ); print_r( $ref4 ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$res = $test->Reference( $ref1, TRUE );</i><br>' );
	$res = $test->Reference( $ref1, TRUE );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Result:<pre>' ); print_r( $res ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$res = $test->Reference( $ref2, TRUE );</i><br>' );
	$res = $test->Reference( $ref2, TRUE );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Result:<pre>' ); print_r( $res ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$res = $test->Reference( $ref3, TRUE );</i><br>' );
	echo( '<i>$res = $test->Reference( $ref4, TRUE );</i><br>' );
	$res = $test->Reference( $ref3, TRUE );
	$res = $test->Reference( $ref4, TRUE );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Result:<pre>' ); print_r( $res ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$res = $test->Reference( $ref1 );</i><br>' );
	$res = $test->Reference( $ref1 );
	echo( 'Result:<pre>' ); print_r( $res ); echo( '</pre>' );
	echo( '<i>$res = $test->Reference( $ref2 );</i><br>' );
	$res = $test->Reference( $ref2 );
	echo( 'Result:<pre>' ); print_r( $res ); echo( '</pre>' );
	echo( '<i>$res = $test->Reference( $ref3 );</i><br>' );
	$res = $test->Reference( $ref3 );
	echo( 'Result:<pre>' ); print_r( $res ); echo( '</pre>' );
	echo( '<i>$res = $test->Reference( $ref4 );</i><br>' );
	$res = $test->Reference( $ref4 );
	echo( 'Result:<pre>' ); print_r( $res ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$res = $test->Reference( $ref1, FALSE );</i><br>' );
	$res = $test->Reference( $ref1, FALSE );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$res = $test->Reference( $ref2, FALSE );</i><br>' );
	$res = $test->Reference( $ref2, FALSE );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$res = $test->Reference( $ref3, FALSE );</i><br>' );
	$res = $test->Reference( $ref3, FALSE );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( 'Before:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$id = $test->Commit( $mcontainer );</i><br>' );
	$id = $test->Commit( $mcontainer );
	echo( 'After:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$ref = $test[ \'REFERENCE\' ][ 0 ][ kTAG_DATA ];</i><br>' );
	$ref = $test[ 'REFERENCE' ][ 0 ][ kTAG_DATA ];
	echo( '<i>$object = $mcontainer->Load( $ref );</i><br>' );
	$object = $mcontainer->Load( $ref );
	echo( '<pre>' ); print_r( $object ); echo( '</pre>' );
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
