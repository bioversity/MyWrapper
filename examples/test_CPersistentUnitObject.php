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
	public function Relation( $theValue, $theOperation = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageObjectList( $this,
											 'REFERENCE', kTAG_KIND, kTAG_DATA,
											 $theValue, $theOperation,
											 $getOld );
	}
	
	protected function _PrepareCommit( &$theContainer, &$theIdentifier, &$theModifiers )
	{
		$this->_isInited( TRUE );
		parent::_PrepareCommit( $theContainer, $theIdentifier, $theModifiers );
		$this->_ParseReferences('REFERENCE', $theContainer, kFLAG_REFERENCE_MASK );
	}
	protected function _Commit( &$theContainer, &$theIdentifier, &$theModifiers )
	{
		$this->_SetTags();
		return parent::_Commit( $theContainer, $theIdentifier, $theModifiers );
	}
	protected function _index()
	{
		return $this->offsetGet( 'NAME' )."\t".$this->offsetGet( 'SURNAME' );
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
	$mcontainer = new CMongoContainer( $db->selectCollection( 'CPersistentUnitObject' ) );
	 
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
	
	try
	{
		echo( '<i>Load from ArrayObject container</i><br>' );
		$container = new ArrayObject( array( array( 'NAME' => 'Milko', 'SURNAME' => 'Skofic' ) ) );
		echo( 'Container:<pre>' ); print_r( $container ); echo( '</pre>' );
		echo( '<i>$test = new MyClass( $container, 0 );</i><br>' );
		$test = new MyClass( $container, 0 );
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
	echo( '<i>$test = new MyClass( $acontainer, 0 );</i><br>' );
	$test = new MyClass( $acontainer, 0 );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Not found</i><br>' );
	echo( '<i>$test = new MyClass( $acontainer, 1 );</i><br>' );
	$test = new MyClass( $acontainer, 1 );
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
	echo( '<i>$found = $test->Commit( $acontainer, 1 );</i><br>' );
	$found = $test->Commit( $acontainer, 1 );
	echo( 'Container:<pre>' ); print_r( $acontainer ); echo( '</pre>' );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Should increment version</i><br>' );
	echo( '<i>$test->Uncommit();</i><br>' );
	$test->Uncommit();
	echo( '<i>$found = $test->Commit( $acontainer, 1 );</i><br>' );
	$found = $test->Commit( $acontainer, 1 );
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
	$test[ 'Other' ] = 0;
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
	$test[ 'Other' ] = 1;
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
	echo( '<i>$ref = array( kTAG_REFERENCE_ID => 1 );</i><br>' );
	$ref = array( kTAG_REFERENCE_ID => 1 );
	echo( '<i>$test = new MyClass( $mcontainer, $ref );</i><br>' );
	$test = new MyClass( $mcontainer, $ref );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Load with query</i><br>' );
	echo( '<i>$query = new CMongoQuery();</i><br>' );
	$query = new CMongoQuery();
	echo( '<i>$query->AppendStatement( CQueryStatement::Equals( \'SURNAME\', \'Skofic\' ), kOPERATOR_AND );</i><br>' );
	$query->AppendStatement( CQueryStatement::Equals( 'SURNAME', 'Skofic' ), kOPERATOR_AND );
	echo( '<i>$test = new MyClass( $mcontainer, $query );</i><br>' );
	$test = new MyClass( $mcontainer, $query );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	echo( '<hr>' );
	
	//
	// New object.
	//
	echo( '<h3>New object</h3>' );
	
	echo( '<i>New object</i><br>' );
	echo( '<i>$test = CPersistentUnitObject::NewObject( $mcontainer, 1 );</i><br>' );
	$test = CPersistentUnitObject::NewObject( $mcontainer, 1 );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	echo( '<hr>' );
	
	//
	// Relation list.
	//
	echo( '<h3>Relation list</h3>' );
	
	//
	// Create references.
	//
	$id1 = 1;
	$ref1 = new MyClass( array( 'Name' => 'Relation 1', kTAG_LID => $id1 ) );
	$ref2 = new MyClass( array( 'Name' => 'Relation 2' ) );
	$ref3 = new MyClass( array( 'Name' => 'Relation 3' ) );
	$id3 = $ref3->Commit( $mcontainer );
	
	echo( '<i>References</i><br>' );
	echo( '1:<pre>' ); print_r( $ref1 ); echo( '</pre>' );
	echo( '2:<pre>' ); print_r( $ref2 ); echo( '</pre>' );
	echo( '3:<pre>' ); print_r( $ref3 ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$res = $test->Relation( $ref1, TRUE );</i><br>' );
	$res = $test->Relation( $ref1, TRUE );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Result:<pre>' ); print_r( $res ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$res = $test->Relation( $ref2, TRUE );</i><br>' );
	$res = $test->Relation( $ref2, TRUE );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Result:<pre>' ); print_r( $res ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$res = $test->Relation( $id1 );</i><br>' );
	$res = $test->Relation( $id1 );
	echo( 'Result:<pre>' ); print_r( $res ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$res = $test->Relation( $ref2 );</i><br>' );
	$res = $test->Relation( $ref2 );
	echo( 'Result:<pre>' ); print_r( $res ); echo( '</pre>' );
	echo( '<hr>' );
	
	try
	{
		echo( 'Before:<pre>' ); print_r( $test ); echo( '</pre>' );
		echo( '<i>$id = $test->Commit( $mcontainer );</i><br>' );
		$id = $test->Commit( $mcontainer );
		echo( 'After:<pre>' ); print_r( $test ); echo( '</pre>' );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );
		echo( '<br>' );
	}
	echo( '<hr>' );
	
	echo( '<i>$res = $test->Relation( $id1, FALSE );</i><br>' );
	$res = $test->Relation( $id1, FALSE );
	echo( 'Result:<pre>' ); print_r( $res ); echo( '</pre>' );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$res = $test->Relation( $ref2, FALSE, TRUE );</i><br>' );
	$res = $test->Relation( $ref2, FALSE, TRUE );
	echo( 'Result:<pre>' ); print_r( $res ); echo( '</pre>' );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
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
	$ref4 = array( kTAG_TYPE => 'OTHER', kTAG_DATA => new MyClass( array( 'Name' => 'Relation 4' ) ) );
	
	echo( '<i>References</i><br>' );
	echo( '1:<pre>' ); print_r( $ref1 ); echo( '</pre>' );
	echo( '2:<pre>' ); print_r( $ref2 ); echo( '</pre>' );
	echo( '3:<pre>' ); print_r( $ref3 ); echo( '</pre>' );
	echo( '4:<pre>' ); print_r( $ref4 ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$res = $test->Relation( $ref1, TRUE );</i><br>' );
	$res = $test->Relation( $ref1, TRUE );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Result:<pre>' ); print_r( $res ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$res = $test->Relation( $ref2, TRUE );</i><br>' );
	$res = $test->Relation( $ref2, TRUE );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Result:<pre>' ); print_r( $res ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$res = $test->Relation( $ref3, TRUE );</i><br>' );
	echo( '<i>$res = $test->Relation( $ref4, TRUE );</i><br>' );
	$res = $test->Relation( $ref3, TRUE );
	$res = $test->Relation( $ref4, TRUE );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$res = $test->Relation( $ref1 );</i><br>' );
	$res = $test->Relation( $ref1 );
	echo( 'Result:<pre>' ); print_r( $res ); echo( '</pre>' );
	echo( '<i>$res = $test->Relation( $ref2 );</i><br>' );
	$res = $test->Relation( $ref2 );
	echo( 'Result:<pre>' ); print_r( $res ); echo( '</pre>' );
	echo( '<i>$res = $test->Relation( $ref3 );</i><br>' );
	$res = $test->Relation( $ref3 );
	echo( 'Result:<pre>' ); print_r( $res ); echo( '</pre>' );
	echo( '<i>$res = $test->Relation( $ref4 );</i><br>' );
	$res = $test->Relation( $ref4 );
	echo( 'Result:<pre>' ); print_r( $res ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>$res = $test->Relation( $ref1, FALSE );</i><br>' );
	$res = $test->Relation( $ref1, FALSE );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$res = $test->Relation( $ref2, FALSE );</i><br>' );
	$res = $test->Relation( $ref2, FALSE );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$res = $test->Relation( $ref3, FALSE );</i><br>' );
	$res = $test->Relation( $ref3, FALSE );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( 'Before:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$id = $test->Commit( $mcontainer );</i><br>' );
	$id = $test->Commit( $mcontainer );
	echo( 'After:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	echo( '<hr>' );
	
	//
	// Serialisation.
	//
	echo( '<h3>Serialisation</h3>' );
	
	$array = array
	(
		kTAG_LID => array
		(
			kTAG_TYPE => kTYPE_MongoId,
			kTAG_DATA => '4f5e28d2961be56010000003'
		),
		'Stamp' => array
		(
			kTAG_TYPE => kTYPE_STAMP,
			kTAG_DATA => array
			(
				kTYPE_STAMP_SEC => 22,
				kTYPE_STAMP_USEC => 1246
			)
		),
		'RegExpr' => array
		(
			kTAG_TYPE => kTYPE_REGEX,
			kTAG_DATA => '/^pippo/i'
		),
		'Int32' => array
		(
			kTAG_TYPE => kTYPE_INT32,
			kTAG_DATA => 32
		),
		'Int64' => array
		(
			kTAG_TYPE => kTYPE_INT64,
			kTAG_DATA => '12345678901234'
		),
		'Binary' => array
		(
			kTAG_TYPE => kTYPE_BINARY,
			kTAG_DATA => bin2hex( 'PIPPO' )
		)
	);
	echo( '<i>Serialise object</i><br>' );
	echo( '<i>$test = new MyClass( $array, NULL, kFLAG_STATE_ENCODED );</i><br>' );
	$test = new MyClass( $array, NULL, kFLAG_STATE_ENCODED );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$found = $test->Commit( $mcontainer );</i><br>' );
	$found = $test->Commit( $mcontainer );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<i>$found = $mcontainer->UnserialiseData( $found );</i><br>' );
	$mcontainer->UnserialiseData( $found );
	echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<i>$test = $mcontainer->Load( $found );</i><br>' );
	$test = $mcontainer->Load( $found );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	echo( '<hr>' );
	
	//
	// Test Reference.
	//
	echo( '<h3>Test Reference</h3>' );
	
	echo( '<i>Reference object</i><br>' );
	echo( '<i>$ref = CPersistentUnitObject::Reference( $test, kFLAG_REFERENCE_MASK );</i><br>' );
	$ref = CPersistentUnitObject::Reference( $test, kFLAG_REFERENCE_MASK );
	echo( 'Reference:<pre>' ); print_r( $ref ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Full reference object</i><br>' );
	echo( '<i>$ref = MongoDBRef::create( \'CPersistentUnitObject\', $ref[ kTAG_REFERENCE_ID ], \'TEST\' );</i><br>' );
	$ref = MongoDBRef::create( 'CPersistentUnitObject', $ref[ kTAG_REFERENCE_ID ], 'TEST' );
	echo( '<i>$ref = CPersistentUnitObject::Reference( $ref, kFLAG_REFERENCE_MASK );</i><br>' );
	$ref = CPersistentUnitObject::Reference( $ref, kFLAG_REFERENCE_MASK );
	echo( 'Reference:<pre>' ); print_r( $ref ); echo( '</pre>' );
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
