<?php

/**
 * {@link CGenesys.php Base} object test suite.
 *
 * This file contains routines to test whether Genesys has C&E records not linked to
 * existing passport records.
 *
 *	@package	GENESYS
 *	@subpackage	Batch
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 20/06/2012
 */

/*=======================================================================================
 *																						*
 *							BATCH_CHECK_CHARACTERIZED.php								*
 *																						*
 *======================================================================================*/

//
// Class includes.
//
require_once( "CGenesys.php" );


/*=======================================================================================
 *	TEST USER PERSISTENT OBJECTS														*
 *======================================================================================*/

//
// Test class.
//
try
{
	//
	// Instantiate batch object.
	//
	$batch = new CGenesys( kDSN_GENESYS );
	$result = $batch->CheckCharacterized();
	
	print_r( $result );
}

//
// Catch exceptions.
//
catch( Exception $error )
{
	echo( (string) $error );
}

?>
