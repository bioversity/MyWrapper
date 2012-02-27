<?php
	
/**
 * {@link CDataWrapper.php Data} wrapper object server.
 *
 * This file represents an example wrapper used to test the
 * {@link CDataWrapper CDataWrapper} class.
 *
 *	@package	Server
 *	@subpackage	Wrappers
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 22/12/2011
 *				2.00 22/02/2012
 */

/*=======================================================================================
 *																						*
 *									DataWrapper.php										*
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
 * Class includes.
 *
 * This include file contains the working class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CDataWrapper.php" );


/*=======================================================================================
 *	TEST WRAPPER OBJECT																	*
 *======================================================================================*/
 
session_start();

//
// Instantiate wrapper.
//
$test = new CDataWrapper();

//
// Handle request.
//
$test->HandleRequest();

exit;

?>
