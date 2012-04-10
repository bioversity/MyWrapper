<?php

/**
 * {@link CFAOInstitute.php Base} object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * object {@link CFAOInstitute class}.
 *
 *	@package	Test
 *	@subpackage	Entities
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 10/04/2012
 */

/*=======================================================================================
 *																						*
 *									test_CFAOInstitute.php								*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."CFAOInstitute.php" );


/*=======================================================================================
 *	TEST INSTITUTE PERSISTENT OBJECTS													*
 *======================================================================================*/

//
// Test class.
//
try
{
	//
	// Instantiate Mongo database.
	//
	$mongo = New Mongo();
	
	//
	// Select database.
	//
	$db = $mongo->selectDB( "TEST" );
	
	//
	// Drop database.
	//
	$db->drop();
	
	//
	// Instantiate CMongoContainer.
	//
	$collection = new CMongoContainer( $db->selectCollection( 'CFAOInstitute' ) );
	 
	//
	// Load institutes.
	//
	echo( '<h3>Load institutes</h3>' );
	
	echo( '<i><b>INSTITUTE1</b></i><br>' );
	echo( '<i>$institute1 = new CFAOInstitute();</i><br>' );
	$institute1 = new CFAOInstitute();
	echo( '<i>$institute1->Code( \'MDG009\' );</i><br>' );
	$institute1->Code( 'MDG009' );
	echo( '<i>$institute1->Acronym( \'IRAT\', TRUE );</i><br>' );
	$institute1->Acronym( 'IRAT', TRUE );
	echo( '<i>$institute1->EAcronym( \'MDGIRAT\' );</i><br>' );
	$institute1->EAcronym( 'MDGIRAT' );
	echo( '<i>$institute1->Name( \'Mission IRAT à Madagascar\' );</i><br>' );
	$institute1->Name( 'Mission IRAT à Madagascar' );
	echo( '<i>$institute1->FAOType( \'GOV\', TRUE );</i><br>' );
	$institute1->FAOType( 'GOV', TRUE );
	echo( '<i>$institute1->FAOType( \'FRA\', TRUE );</i><br>' );
	$institute1->FAOType( 'FRA', TRUE );
	echo( '<i>$institute1->FAOType( \'MDG\', TRUE );</i><br>' );
	$institute1->FAOType( 'MDG', TRUE );
	echo( '<i>$institute1->Kind( kENTITY_INST_FAO_ACT_PGR, TRUE );</i><br>' );
	$institute1->Kind( kENTITY_INST_FAO_ACT_PGR, TRUE );
	echo( '<i>$institute1->Kind( kENTITY_INST_FAO_ACT_COLL, TRUE );</i><br>' );
	$institute1->Kind( kENTITY_INST_FAO_ACT_COLL, TRUE );
	echo( '<i>$addr = new CMailAddress();</i><br>' );
	$addr = new CMailAddress();
	echo( '<i>$addr->Street( \'BP 853\' );</i><br>' );
	$addr->Street( 'BP 853' );
	echo( '<i>$addr->City( \'Antananarivo\' );</i><br>' );
	$addr->City( 'Antananarivo' );
	echo( '<i>$addr->Zip( \'A-F07\' );</i><br>' );
	$addr->Zip( 'A-F07' );
	echo( '<i>$institute1->Mail( $addr );</i><br>' );
	$institute1->Mail( $addr );
	echo( '<i>$institute1->Phone( \'27182\' );</i><br>' );
	$institute1->Phone( '27182' );
	echo( '<i>$institute1->Fax( \'27181\' );</i><br>' );
	$institute1->Fax( '27181' );
	echo( '<i>$institute1->Email( \'mail@nowhere.bot\' );</i><br>' );
	$institute1->Email( 'mail@nowhere.bot' );
	echo( '<i>$institute1->URL( \'http://nowhere.org\' );</i><br>' );
	$institute1->URL( 'http://nowhere.org' );
	echo( '<i>$institute1->Latitude( 4159 );</i><br>' );
	$institute1->Latitude( 4159 );
	echo( '<i>$institute1->Longitude( 250 );</i><br>' );
	$institute1->Longitude( 250 );
	echo( '<i>$institute1->Altitude( 70 );</i><br>' );
	$institute1->Altitude( 70 );
	echo( '<i>$institute1->Stamp( new CDataTypeStamp( \'2004-08-06\' ) );</i><br>' );
	$institute1->Stamp( new CDataTypeStamp( '2004-08-06 00:00:00' ) );
	echo( '<i>$institute1->Valid( \'MDG010\' );</i><br>' );
	$institute1->Valid( 'MDG010' );
	$id1 = $institute1->Commit( $collection );
	echo( "$id1<pre>" ); print_r( $institute1 ); echo( '</pre>' );
	echo( '<hr>' );
	 
	//
	// Load institutes list.
	//
	echo( '<h3>Load institutes list</h3>' );
	
	echo( '<i><b>List of institutes</b></i><br>' );
	$matrix = Array();
	$fp = fopen( 'Code_FAO_Institutes.csv', 'r' );
	if( $fp !== FALSE )
	{
		if( $header = fgetcsv( $fp, 4096 ) )
		{
			$values = fgetcsv( $fp, 4096 );
			while( $values )
			{
				$tmp = Array();
				$hrd = $header;
				if( count( $values ) < count( $hrd ) )
					array_splice( $hrd, count( $values ) );
				for( $i = 0; $i < count( $hrd ); $i++ )
					$tmp[ $hrd[ $i ] ] = $values[ $i ];
				$matrix[] = $tmp;
				$values = fgetcsv( $fp, 4096 );
			}
		}
		
		fclose( $fp );
	}
	else
		echo( '<i>Unable to open file!</i><br>' );
	echo( '<i>$count = CFAOInstitute::Import( $collection, $matrix );</i><br>' );
	$count = CFAOInstitute::Import( $collection, $matrix );
	echo( $count );
	echo( '<hr>' );
	
	echo( '<h3>DONE</h3>' );
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
