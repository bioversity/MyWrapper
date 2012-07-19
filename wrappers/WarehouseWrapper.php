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
 * Local includes.
 *
 * This include file contains local definitions, it should be stored locally, in this case
 * we are using the default one
 */
require_once( '/Library/WebServer/Library/wrapper/local/environment.inc.php' );

/**
 * Class includes.
 *
 * This include file contains the working class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CWarehouseWrapper.php" );


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
