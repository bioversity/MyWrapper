<?php
	
/**
 * {@link CWarehouseWrapper.php Warehouse} wrapper object test suite, MCPD example.
 *
 * This file contains a basic MCPD navigation example uning {@link CWarehouseWrapper class}.
 *
 *	@package	Test
 *	@subpackage	Wrappers
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 11/05/2012
 */

/*=======================================================================================
 *																						*
 *										test_MCPD.php									*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Style includes.
//
require_once( '/Library/WebServer/Library/wrapper/styles.inc.php' );

//
// Environment includes.
//
require_once( '/Library/WebServer/Library/wrapper/local/environment.inc.php' );

//
// Categories default includes.
//
require_once( '/Library/WebServer/Library/wrapper/local/categories.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."CWarehouseWrapperClient.php" );


/*=======================================================================================
 *	GLOBAL DEFINITIONS																	*
 *======================================================================================*/
 
//
// Use raw parameters or use wrapper client?.
//
define( 'kUSE_CLIENT', FALSE );


/*=======================================================================================
 *	TEST WRAPPER OBJECT																	*
 *======================================================================================*/
 
//
// Init local storage.
//
$url = 'http://localhost/newwrapper/WarehouseWrapper.php';

//
// TRY BLOCK.
//
try
{
	/*===================================================================================
	 *	GET MCPD ROOT NODE																*
	 *==================================================================================*/
	echo( '<h4>Get the MCPD root node</h4>' );
	echo( '<i>We search for all root nodes that belong to the accessions domain '
		 .' and that belong to the passport category: among the results we should '
		 .'get the MCPD root node.</i>' );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Load parameters.
		//
		$params = new CWarehouseWrapperClient( $url );
		$params->Operation( kAPI_OP_QUERY_ROOTS );
		$params->Format( kTYPE_JSON );
		$params->Database( 'WAREHOUSE' );
		$params->Container( kDEFAULT_CNT_TERMS );
		$params->Attributes( kTAG_DOMAIN, kDOMAIN_ACCESSION, TRUE );
		$params->Attributes( kTAG_CATEGORY, kCATEGORY_PASSPORT, TRUE );
		//
		// Get response.
		//
		$decoded = $params->Execute();
	}
	//
	// Use raw parameters.
	//
	else
	{
		//
		// Build attributes list.
		//
		$list = json_encode( array( kTAG_DOMAIN => array( kDOMAIN_ACCESSION ),
									kTAG_CATEGORY => array( kCATEGORY_PASSPORT ) ) );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_QUERY_ROOTS;		// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;					// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';				// Database.
		$params[] = kAPI_CONTAINER.'='.kDEFAULT_CNT_TERMS;		// Container.
		$params[] = kAPI_OPT_ATTRIBUTES.'='.$list;				// Attributes.
		//
		// Build request.
		//
		$request = $url.'?'.implode( '&', $params );
		//
		// Get response.
		//
		$response = file_get_contents( $request );
		//
		// Decode response.
		//
		$decoded = json_decode( $response, TRUE );
	}
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Result:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $decoded ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_TABLE_POS );
	//
	// Save result node.
	//
	$node = key( $decoded[ kAPI_DATA_RESPONSE ]
						 [ kAPI_RESPONSE_NODES ] );
	echo( "<i>We shall use the first found node [$node] as the root node (let's hope it's MCPD ;-).</i>" );
	echo( '<hr>' );
	
	/*===================================================================================
	 *	GET DIRECTED EDGES (IN)															*
	 *==================================================================================*/
	echo( '<h4>Get the MCPD child nodes</h4>' );
	echo( "<i>We select all nodes pointing to our [$node] root node, in the process, we "
		 ."only show name, definition and node references.</i>" );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CWarehouseWrapperClient( $url );
		$params->Operation( kAPI_OP_GET_RELS );
		$params->Format( kTYPE_JSON );
		$params->Database( 'WAREHOUSE' );
		$params->Container( kDEFAULT_CNT_TERMS );
		$params->Identifiers( $node, TRUE );
		$params->Direction( kAPI_DIRECTION_IN );
		//
		// Get response.
		//
		$decoded = $params->Execute();
	}
	//
	// Use raw parameters.
	//
	else
	{
		//
		// Build identifiers list.
		//
		$list = json_encode( array( $node ) );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_RELS;			// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';					// Database.
		$params[] = kAPI_CONTAINER.'='.kDEFAULT_CNT_TERMS;				// Container.
		$params[] = kAPI_OPT_IDENTIFIERS.'='.$list;					// Identifiers.
		$params[] = kAPI_OPT_DIRECTION.'='.kAPI_DIRECTION_IN;		// Direction.
		//
		// Build request.
		//
		$request = $url.'?'.implode( '&', $params );
		//
		// Get response.
		//
		$response = file_get_contents( $request );
		//
		// Decode response.
		//
		$decoded = json_decode( $response, TRUE );
	}
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Result:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $decoded ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_TABLE_POS );
	//
	// Save result node.
	//
	$term = 'MCPD:SAMPSTAT';
	if( ! array_key_exists( $term,
							$decoded[ kAPI_DATA_RESPONSE ][ kAPI_RESPONSE_TERMS ] ) )
		exit( 'Descriptor not found: reload all terms and nodes by erasing Meo4j database and the WAREHOUSE Mongo Collection.' );
	$node = $decoded[ kAPI_DATA_RESPONSE ]
					[ kAPI_RESPONSE_TERMS ]
					[ $term ]
					[ kTAG_NODE ]
					[ 0 ];
	echo( "<i>We shall use the ($term) node [$node] as the next child node.</i>" );
	echo( '<hr>' );
	
	/*===================================================================================
	 *	GET DIRECTED EDGES (IN)															*
	 *==================================================================================*/
	echo( "<h4>Get the ($term) [$node] child nodes</h4>" );
	echo( "<i>We select all nodes pointing to our ($term) [$node] node.</i>" );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CWarehouseWrapperClient( $url );
		$params->Operation( kAPI_OP_GET_RELS );
		$params->Format( kTYPE_JSON );
		$params->Database( 'WAREHOUSE' );
		$params->Container( kDEFAULT_CNT_TERMS );
		$params->Identifiers( $node, TRUE );
		$params->Direction( kAPI_DIRECTION_IN );
		//
		// Get response.
		//
		$decoded = $params->Execute();
	}
	//
	// Use raw parameters.
	//
	else
	{
		//
		// Build identifiers list.
		//
		$list = json_encode( array( $node ) );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_RELS;			// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';					// Database.
		$params[] = kAPI_CONTAINER.'='.kDEFAULT_CNT_TERMS;				// Container.
		$params[] = kAPI_OPT_IDENTIFIERS.'='.$list;					// Identifiers.
		$params[] = kAPI_OPT_DIRECTION.'='.kAPI_DIRECTION_IN;		// Direction.
		//
		// Build request.
		//
		$request = $url.'?'.implode( '&', $params );
		//
		// Get response.
		//
		$response = file_get_contents( $request );
		//
		// Decode response.
		//
		$decoded = json_decode( $response, TRUE );
	}
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Result:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $decoded ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_TABLE_POS );
	//
	// Save result node.
	//
	$term = 'MCPD:SAMPSTAT:100';
	if( ! array_key_exists( $term,
							$decoded[ kAPI_DATA_RESPONSE ][ kAPI_RESPONSE_TERMS ] ) )
		exit( 'Descriptor not found: reload all terms and nodes by erasing Meo4j database and the WAREHOUSE Mongo Collection.' );
	$node = $decoded[ kAPI_DATA_RESPONSE ]
					[ kAPI_RESPONSE_TERMS ]
					[ $term ]
					[ kTAG_NODE ]
					[ 0 ];
	echo( "<i>We shall use the ($term) node [$node] as the next child node.</i>" );
	echo( '<hr>' );
	
	/*===================================================================================
	 *	GET DIRECTED EDGES (IN)															*
	 *==================================================================================*/
	echo( "<h4>Get the ($term) [$node] child nodes</h4>" );
	echo( "<i>We select all nodes pointing to our ($term) [$node] node.</i>" );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CWarehouseWrapperClient( $url );
		$params->Operation( kAPI_OP_GET_RELS );
		$params->Format( kTYPE_JSON );
		$params->Database( 'WAREHOUSE' );
		$params->Container( kDEFAULT_CNT_TERMS );
		$params->Identifiers( $node, TRUE );
		$params->Direction( kAPI_DIRECTION_IN );
		//
		// Get response.
		//
		$decoded = $params->Execute();
	}
	//
	// Use raw parameters.
	//
	else
	{
		//
		// Build identifiers list.
		//
		$list = json_encode( array( $node ) );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_RELS;			// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';					// Database.
		$params[] = kAPI_CONTAINER.'='.kDEFAULT_CNT_TERMS;				// Container.
		$params[] = kAPI_OPT_IDENTIFIERS.'='.$list;					// Identifiers.
		$params[] = kAPI_OPT_DIRECTION.'='.kAPI_DIRECTION_IN;		// Direction.
		//
		// Build request.
		//
		$request = $url.'?'.implode( '&', $params );
		//
		// Get response.
		//
		$response = file_get_contents( $request );
		//
		// Decode response.
		//
		$decoded = json_decode( $response, TRUE );
	}
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Result:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $decoded ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_TABLE_POS );
	//
	// Save result node.
	//
	$term = 'MCPD:SAMPSTAT:110';
	if( ! array_key_exists( $term,
							$decoded[ kAPI_DATA_RESPONSE ][ kAPI_RESPONSE_TERMS ] ) )
		exit( 'Descriptor not found: reload all terms and nodes by erasing Meo4j database and the WAREHOUSE Mongo Collection.' );
	$node = $decoded[ kAPI_DATA_RESPONSE ]
					[ kAPI_RESPONSE_TERMS ]
					[ $term ]
					[ kTAG_NODE ]
					[ 0 ];
	echo( "<i>We shall use the ($term) node [$node] as the next child node.</i>" );
	echo( '<hr>' );
	
	/*===================================================================================
	 *	GET DIRECTED EDGES (IN)															*
	 *==================================================================================*/
	echo( "<h4>Get the ($term) [$node] child nodes</h4>" );
	echo( "<i>We select all nodes pointing to our ($term) [$node] node, at some point we must stop...</i>" );
	//
	// Use wrapper client.
	//
	if( kUSE_CLIENT )
	{
		//
		// Build parameters.
		//
		$params = new CWarehouseWrapperClient( $url );
		$params->Operation( kAPI_OP_GET_RELS );
		$params->Format( kTYPE_JSON );
		$params->Database( 'WAREHOUSE' );
		$params->Container( kDEFAULT_CNT_TERMS );
		$params->Identifiers( $node, TRUE );
		$params->Direction( kAPI_DIRECTION_IN );
		//
		// Get response.
		//
		$decoded = $params->Execute();
	}
	//
	// Use raw parameters.
	//
	else
	{
		//
		// Build identifiers list.
		//
		$list = json_encode( array( $node ) );
		//
		// Build parameters.
		//
		$params = Array();
		$params[] = kAPI_OPERATION.'='.kAPI_OP_GET_RELS;			// Command.
		$params[] = kAPI_FORMAT.'='.kTYPE_JSON;						// Format.
		$params[] = kAPI_DATABASE.'='.'WAREHOUSE';					// Database.
		$params[] = kAPI_CONTAINER.'='.kDEFAULT_CNT_TERMS;				// Container.
		$params[] = kAPI_OPT_IDENTIFIERS.'='.$list;					// Identifiers.
		$params[] = kAPI_OPT_DIRECTION.'='.kAPI_DIRECTION_IN;		// Direction.
		//
		// Build request.
		//
		$request = $url.'?'.implode( '&', $params );
		//
		// Get response.
		//
		$response = file_get_contents( $request );
		//
		// Decode response.
		//
		$decoded = json_decode( $response, TRUE );
	}
	//
	// Display.
	//
	echo( kSTYLE_TABLE_PRE );
	echo( kSTYLE_ROW_PRE );
	echo( kSTYLE_HEAD_PRE.'Result:'.kSTYLE_HEAD_POS );
	echo( kSTYLE_DATA_PRE.'<pre>' ); print_r( $decoded ); echo( '</pre>'.kSTYLE_DATA_POS );
	echo( kSTYLE_ROW_POS );
	echo( kSTYLE_TABLE_POS );
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
