<?php

/**
 * {@link CGraphNode.php Base} object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * base object {@link CGraphNode class}.
 *
 *	@package	Test
 *	@subpackage	Persistence
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 19/04/2012
 */

/*=======================================================================================
 *																						*
 *									test_CGraphNode.php									*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."CGraphNode.php" );


/*=======================================================================================
 *	TEST GRAPH NODES																	*
 *======================================================================================*/
 
//
// Test class.
//
try
{
	//
	// Instantiate Neo4j client.
	//
	$container = new Everyman\Neo4j\Client( 'localhost', 7474 );
	 
	//
	// Test content.
	//
	echo( '<h3>Content</h3>' );

	try
	{
		echo( '<i>Empty object</i><br>' );
		echo( '<i>$test = new CGraphNode();</i><br>' );
		$test = new CGraphNode();
		echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	}
	catch( Exception $error )
	{
		echo( CException::AsHTML( $error ) );
		echo( '<br>' );
	}
	echo( '<hr>' );
	
	echo( '<i>From array</i><br>' );
	echo( '<i>$content = array( \'Name\' => \'Milko\' );</i><br>' );
	$content = array( 'Name' => 'Milko' );
	echo( '<i>$test = new CGraphNode( $content ) );</i><br>' );
	$test = new CGraphNode( $content );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>From ArrayObject</i><br>' );
	echo( '<i>$content = new ArrayObject( array( \'Name\' => \'Milko\' ) );</i><br>' );
	$content = new ArrayObject( array( 'Name' => 'Milko' ) );
	echo( '<i>$test = new CGraphNode( $content ) );</i><br>' );
	$test = new CGraphNode( $content );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );

	try
	{
		echo( '<i>From any other type</i><br>' );
		echo( '<i>$content = 10;</i><br>' );
		$content = 10;
		echo( '<i>$test = new CGraphNode( $content ) );</i><br>' );
		$test = new CGraphNode( $content );
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
	// Test properties.
	//
	echo( '<h3>Properties</h3>' );
	
	echo( '<i>Empty node</i><br>' );
	echo( '<i>$test = new CGraphNode( $container, 0 );</i><br>' );
	$test = new CGraphNode( $container, NULL );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Load property</i><br>' );
	echo( '<i>$test->NodeProperty( kTAG_NAME, \'The name\' );</i><br>' );
	$test->NodeProperty( kTAG_NAME, 'The name' );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Load properties</i><br>' );
	echo( '<i>$prop = array( kTAG_DESCRIPTION => \'Description\', kTAG_DEFINITION => \'Definition\' );</i><br>' );
	$prop = array( kTAG_DESCRIPTION => 'Description', kTAG_DEFINITION => 'Definition' );
	echo( '<i>$test->NodeProperty( NULL, $prop );</i><br>' );
	$test->NodeProperty( NULL, $prop );
	echo( '<i>$prop = $test->NodeProperty();</i><br>' );
	$prop = $test->NodeProperty();
	echo( 'Properties:<pre>' ); print_r( $prop ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Delete property</i><br>' );
	echo( '<i>$test->NodeProperty( kTAG_DEFINITION, FALSE );</i><br>' );
	$test->NodeProperty( kTAG_DEFINITION, FALSE );
	$prop = $test->NodeProperty();
	echo( 'Properties:<pre>' ); print_r( $prop ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Retrieve property</i><br>' );
	echo( '<i>$prop = $test->NodeProperty( kTAG_DESCRIPTION );</i><br>' );
	$prop = $test->NodeProperty( kTAG_DESCRIPTION );
	echo( 'Property:<pre>' ); print_r( $prop ); echo( '</pre>' );
	echo( '<hr>' );
	echo( '<hr>' );
	
	//
	// Persistence.
	//
	echo( '<h3>Persistence</h3>' );
	
	echo( '<i>Save node</i><br>' );
	echo( '<i>$id = $test->Commit( $container );</i><br>' );
	$id = $test->Commit( $container );
	echo( "$id:<pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Retrieve node</i><br>' );
	echo( '<i>$test = new CGraphNode( $container, $id );</i><br>' );
	$test = new CGraphNode( $container, $id );
	echo( "$id:<pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Delete node</i><br>' );
	echo( '<i>$ok = $container->deleteNode( $test );</i><br>' );
	$ok = $container->deleteNode( $test->Node() );
	echo( "$ok:<pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Delete node</i><br>' );
	echo( '<i>$ok = $container->deleteNode( $test );</i><br>' );
	$ok = $container->deleteNode( $test->Node() );
	echo( "$ok:<pre>" ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
exit;
	
	//
	// Test container.
	//
	echo( '<h3>Container</h3>' );
	
	echo( '<i>Not found</i><br>' );
	echo( '<i>$test = new CGraphNode( $container, 1 );</i><br>' );
	$test = new CGraphNode( $container, 1 );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	try
	{
		echo( '<i>Invalid container</i><br>' );
		echo( '<i>$acontainer = $container->Container();</i><br>' );
		$acontainer = $container->Container();
		echo( 'Container:<pre>' ); print_r( $acontainer ); echo( '</pre>' );
		echo( '<i>$test = new CGraphNode( $acontainer, 0 );</i><br>' );
		$test = new CGraphNode( $acontainer, 0 );
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
	echo( '<i>$test = new CGraphNode( array( \'NAME\' => \'Pippo\', \'SURNAME\' => \'Franco\' ) );</i><br>' );
	$test = new CGraphNode( array( 'NAME' => 'Pippo', 'SURNAME' => 'Franco' ) );
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
	echo( '<i>$test = new CGraphNode( $mcontainer, 1 );</i><br>' );
	$test = new CGraphNode( $mcontainer, 1 );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	echo( '<i>Update object</i><br>' );
	$test[ 'Version' ] = 1;
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$found = $test->Commit( $mcontainer );</i><br>' );
	$found = $test->Commit( $mcontainer );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( 'Found:<pre>' ); print_r( $found ); echo( '</pre>' );
	echo( '<i>$test = new CGraphNode( $mcontainer, 1 );</i><br>' );
	$test = new CGraphNode( $mcontainer, 1 );
	echo( 'Object:<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	echo( '<hr>' );
	
	//
	// Test array container.
	//
	echo( '<h3>Test array container</h3>' );
	
	echo( '<i>Append to CArrayContainer</i><br>' );
	echo( '<i>$test = new CGraphNode( array( \'NAME\' => \'Baba\', \'SURNAME\' => \'Bubu\' ) );</i><br>' );
	$test = new CGraphNode( array( 'NAME' => 'Baba', 'SURNAME' => 'Bubu' ) );
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
