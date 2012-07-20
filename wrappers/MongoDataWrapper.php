<?php
	
/**
 * Mongo data wrapper server.
 *
 * This file contains a wrapper server using the {@link CMongoDataWrapper CMongoDataWrapper}
 * class.
 *
 * This can effectively be used as a wrapper to a MongoDB database.
 *
 *	@package	MyWrapper
 *	@subpackage	Server
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 22/12/2011
 *				2.00 23/02/2012
 */

/*=======================================================================================
 *																						*
 *								MongoDataWrapper.php									*
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
require_once( kPATH_LIBRARY_SOURCE."CMongoDataWrapper.php" );

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
$wrapper = new CMongoDataWrapper();

//
// Handle request.
//
$wrapper->HandleRequest();

exit;

?>
