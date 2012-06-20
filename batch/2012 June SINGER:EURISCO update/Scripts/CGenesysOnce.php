<?php

/**
 * <i>CGenesysOnce</i> class definition.
 *
 * This file contains the class definition of <b>CGenesysObce</b> which overloads its
 * {@link CGenesys ancestor} to implement custom actions.
 *
 *	@package	GENESYS
 *	@subpackage	Framework
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 11/06/2012
 */

/*=======================================================================================
 *																						*
 *									CGenesysOnce.php									*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( "CGenesys.php" );

/**
 *	Genesys special actions.
 *
 * This class implements a series of custom procedures which would be run once in general.
 *
 *	@package	GENESYS
 *	@subpackage	Framework
 */
class CGenesysOnce extends CGenesys
{
		

/*=======================================================================================
 *																						*
 *									CUSTOM INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	EraseUnrelated																	*
	 *==================================================================================*/

	/**
	 * Erase unrelated accessions.
	 *
	 * This method will delete all accessions that satisfy the following conditions:
	 *
	 * <ul>
	 *	<li><i>Not related</i>: Only those that are not referenced in C&E data (see
	 *		{@link MarkCharacterized() MarkCharacterized}.
	 *	<li><i>GRIN</i>: Do not belong to GRIN (USA institutes).
	 *	<li><i>ICARDA</i>: Do not belong to ICARDA (SYR002).
	 * </ul>
	 *
	 * @access public
	 */
	public function EraseUnrelated()
	{
		//
		// Get related list.
		//
		$this->MarkCharacterized();
		
		//
		// Get connection.
		//
		$db = $this->Connection();
		
		//
		// Create temporary table.
		//
		$query = 'CREATE TEMPORARY TABLE `'.kTABLE_CHARSED.'_NOT` ('
				.'`ALIS_Id` INT NOT NULL, PRIMARY KEY (`ALIS_Id`) ) '
				.'ENGINE=MyISAM  DEFAULT CHARSET=utf8';
		$ok = $db->Execute( $query );
		$ok->Close();
		
		//
		// Build conditions.
		//
		$conds = Array();
		$conds[] = "( `ALIS_Id` NOT IN( SELECT `ALIS_Id` FROM `".kTABLE_CHARSED."` ) )";
		$conds[] = "( `Institute` != 'SYR002' )";
		$conds[] = "( LEFT( `Institute`, 3 ) != 'USA' )";
		$conds = "WHERE( ".implode( ' AND ', $conds )." )";
		
		//
		// Load temporary table.
		//
		$query = "INSERT INTO `".kTABLE_CHARSED."_NOT` SELECT `ALIS_Id` "
				."FROM `all_accessions` $conds";
		$ok = $db->Execute( $query );
		$ok->Close();
		
		//
		// Load tables list.
		//
		$suffixs = array( 'accessions', 'accnames', 'acq_breeding',
						  'acq_collect', 'acq_exchange', 'environment' );
		$crops = $this->Crops();
		$crops[] = 'all';
		foreach( $crops as $crop )
		{
			//
			// Iterate suffixes.
			//
			foreach( $suffixs as $suffix )
			{
				//
				// Get table name.
				//
				$table = '`'.$crop.'_'.$suffix.'`';
				
				//
				// Build query.
				//
				$query = "DELETE FROM $table "
						."WHERE( `ALIS_Id` IN( SELECT `ALIS_Id` FROM `"
						.kTABLE_CHARSED."_NOT` ) )";
				$ok = $db->Execute( $query );
				$ok->Close();
			
			} // Iterating suffixes.
		
		} // Iterating crops.
		
		//
		// Drop temporary table.
		//
		$query = 'DROP TEMPORARY TABLE `'.kTABLE_CHARSED.'_NOT`';
		$ok = $db->Execute( $query );
		$ok->Close();

	} // EraseUnrelated.

	 

} // class CGenesysOnce.


?>
