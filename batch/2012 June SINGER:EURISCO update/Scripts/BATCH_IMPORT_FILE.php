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
	if( count( $argv ) <= 1 )
		throw new Exception
			( "USAGE: php -f BATCH_IMPORT_FILE.php "
			 ."<file-path> ...<file-path>\n" );									// !@! ==>
	
	//
	// Instantiate batch object.
	//
	$batch = new CGenesys( kDSN_GENESYS );
	
	//
	// Iterate files.
	//
	for( $i = 1; $i < count( $argv ); $i++ )
	{
		//
		// Display file.
		//
		echo( date( "c" )."\n" );
		echo( ($file = $argv[ $i ])."\n" );
		
		//
		// Import file.
		//
		$result = $batch->ImportPassport( $file );
		
		//
		// Write results.
		//
		echo( "Inserted: ".$result[ 'INSERTED' ]."\n" );
		echo( "Updated: ".$result[ 'UPDATED' ]."\n" );
		echo( "Skipped: ".$result[ 'SKIPPED' ]."\n" );
		echo( "Taxa: ".$result[ 'TAXA' ]."\n" );
	}
}

//
// Catch exceptions.
//
catch( Exception $error )
{
	echo( (string) $error );
}

echo( "==> Done!\n" );

?>
