<?php

/**
 * {@link CGenesys.php Base} object test suite.
 *
 * This file contains routines to remove all Genesys accessions that:
 *
 * <ul>
 *	<li>Are not from GRIN.
 *	<li>Are not from ICARDA.
 *	<li>Are not from characterised or evaluated.
 * </ul>
 *
 *	@package	GENESYS
 *	@subpackage	Batch
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 18/06/2012
 */

/*=======================================================================================
 *																						*
 *								BATCH_ERASE_UNRELATED.php								*
 *																						*
 *======================================================================================*/

//
// Class includes.
//
require_once( "CGenesysOnce.php" );


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
	$batch = new CGenesysOnce( kDSN_GENESYS );
	$batch->EraseUnrelated();
}

//
// Catch exceptions.
//
catch( Exception $error )
{
//	echo( CException::AsHTML( $error ) );
	echo( (string) $error );
}

echo( "\nDone!\n" );

?>
