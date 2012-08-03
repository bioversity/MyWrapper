<?php
	
/**
 * Warehouse data wrapper server.
 *
 * This file contains a wrapper server using the {@link CWarehouseWrapper CWarehouseWrapper}
 * class.
 *
 * This can effectively be used as a wrapper to a germplasm warehouse database.
 *
 *	@package	MyWrapper
 *	@subpackage	Server
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 10/04/2012
 */

/*=======================================================================================
 *																						*
 *								WarehouseWrapper.php									*
 *																						*
 *======================================================================================*/

/**
 * Global includes.
 *
 * This include file contains default path definitions and an
 * {@link __autoload() autoloader} used to automatically include referenced classes in this
 * library.
 */
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

/**
 * Server environment.
 *
 * This include file contains the server run-time definitions.
 */
require_once( "/Library/WebServer/Library/wrapper/local/server.inc.php" );

/**
 * Class includes.
 *
 * This include file contains the working class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CWarehouseWrapper.php" );

/**
 * Session includes.
 *
 * This include file contains the definition of the session object.
 */
require_once( kPATH_LIBRARY_SOURCE."CSessionMongoNeo4j.php" );

/**
 * Start session.
 */
session_start();


/*=======================================================================================
 *	INIT SESSION																		*
 *======================================================================================*/
 
$_SESSION[ kDEFAULT_SESSION ] = new CSessionMongoNeo4j();


/*=======================================================================================
 *	TEST WRAPPER OBJECT																	*
 *======================================================================================*/
 
//
// Instantiate wrapper.
//
$wrapper = new CWarehouseWrapper();

//
// Handle request.
//
$wrapper->HandleRequest();

exit;

?>
