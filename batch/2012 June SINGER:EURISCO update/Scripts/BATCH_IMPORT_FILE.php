<?php

/**
 * {@link CGenesys.php Base} object test suite.
 *
 * This file contains routines to import the provided CSV passport file in Genesys.
 *
 *	@package	GENESYS
 *	@subpackage	Batch
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 19/06/2012
 */

/*=======================================================================================
 *																						*
 *								BATCH_IMPORT_FILE.php									*
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
	// Get file path.
	//
	if( count( $argv ) > 1 )
		$file = $argv[ 1 ];
	else
		throw new Exception
			( "USAGE: php -f BATCH_IMPORT_FILE.php <file-path>\n" );			// !@! ==>
	
	//
	// Open log file.
	//
	if( count( $argv ) > 2 )
	{
		//
		// Open log file.
		//
		$log = new SplFileObject( $argv[ 2 ], "a" );
		
		//
		// Write header.
		//
		$log->fwrite( "\n".date( "c" )."\n[$file]\n" );
	}
	
	//
	// Instantiate batch object.
	//
	$batch = new CGenesys( kDSN_GENESYS );
	$result = $batch->ImportPassport( $file );
	
	//
	// Write results.
	//
	echo( $tmp = "Inserted: ".$result[ 'INSERTED' ]."\n" );
	if( isset( $log ) )
		$log->fwrite( $tmp );
	echo( $tmp = "Updated: ".$result[ 'UPDATED' ]."\n" );
	if( isset( $log ) )
		$log->fwrite( $tmp );
	echo( $tmp = "Skipped: ".$result[ 'SKIPPED' ]."\n" );
	if( isset( $log ) )
		$log->fwrite( $tmp );
	echo( $tmp = "Taxa: ".$result[ 'TAXA' ]."\n" );
	if( isset( $log ) )
		$log->fwrite( $tmp );
}

//
// Catch exceptions.
//
catch( Exception $error )
{
	if( isset( $log ) )
		$log->fwrite( (string) $error );
	echo( (string) $error );
}

echo( $tmp = "==> Done!\n" );
if( isset( $log ) )
	$log->fwrite( $tmp );

?>
