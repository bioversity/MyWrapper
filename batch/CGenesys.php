<?php

/**
 * <i>CGenesys</i> class definition.
 *
 * This file contains the class definition of <b>CGenesys</b> which represents the GENESYS
 * database.
 *
 *	@package	GENESYS
 *	@subpackage	Framework
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 11/06/2012
 */

/*=======================================================================================
 *																						*
 *										CGenesys.php									*
 *																						*
 *======================================================================================*/

/**
 * ADODB library.
 */
require_once( kPATH_LIB_ADODB."adodb.inc.php" );

/**
 * ADODB iterators.
 */
require_once( kPATH_LIB_ADODB."adodb-iterator.inc.php" );

/**
 * ADODB exceptions.
 */
require_once( kPATH_LIB_ADODB."adodb-exceptions.inc.php" );

/**
 * Exceptions.
 *
 * This include file contains all exception class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CException.php" );

/**
 * Flags.
 *
 * This include file contains all flags definitions.
 */
require_once( kPATH_LIBRARY_DEFINES."Flags.inc.php" );

/**
 * Local.
 *
 * This include file contains all local definitions.
 */
require_once( "CGenesys.inc.php" );

/**
 *	Genesys database.
 *
 * This class implements an object that represents the Genesys database and that knows how
 * read and write to it.
 *
 *	@package	GENESYS
 *	@subpackage	Framework
 */
class CGenesys
{
	/**
	 * Data source name.
	 *
	 * This data member holds the data source name.
	 *
	 * @var string
	 */
	 protected $mDSN = NULL;

	/**
	 * Database connection.
	 *
	 * This data member holds the database connection.
	 *
	 * @var ADOConnection
	 */
	 protected $mConnection = NULL;

	/**
	 * Characterisation and evaluation tables.
	 *
	 * This data member holds the list of C&E table names.
	 *
	 * @var array
	 */
	 protected $mCETables = Array();

	/**
	 * Characterisation and evaluation table references.
	 *
	 * This data member holds the name of the temporary table holding the referenced
	 * accessions, this member will only be set if the table exists.
	 *
	 * @var string
	 */
	 protected $mCharsed = NULL;

	/**
	 * Crops.
	 *
	 * This data member holds the crops list.
	 *
	 * @var array
	 */
	 protected $mCrops = Array();

		

/*=======================================================================================
 *																						*
 *											MAGIC										*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	__construct																		*
	 *==================================================================================*/

	/**
	 * Instantiate class.
	 *
	 * The method expects a data source name, it will not open the connection, this is
	 * done by the {@link Connection() Connection} method when needed.
	 *
	 * @param string				$theDataSource		Database data source name.
	 *
	 * @access public
	 */
	public function __construct( $theDatasource = NULL )
	{
		//
		// Handle data source name.
		//
		if( $theDatasource !== NULL )
			$this->Datasource( (string) $theDatasource );
		
	} // Constructor.

	 
	/*===================================================================================
	 *	__sleep																			*
	 *==================================================================================*/

	/**
	 * Called before serialisation.
	 *
	 * This method will close the connection and reset the connection object.
	 *
	 * @access public
	 * @return array
	 */
	public function __sleep()
	{
		//
		// Handle connection object.
		//
		if( $this->mConnection instanceof ADOConnection )
		{
			//
			// Handle active connection.
			//
			if( $this->mConnection->IsConnected() )
			{
				//
				// Delete characterised accessions table.
				//
				if( $this->mCharsed )
				{
					//
					// Drop table.
					//
					$query = 'DROP TEMPORARY TABLE IF EXISTS `'.kTABLE_CHARSED.'`';
					$ok = $this->mConnection->Execute( $query );
					$ok->Close();
					
					//
					// Update member.
					//
					$this->mCharsed = TRUE;
				
				} // Had characterised temporary table.
				
				//
				// Close database connection.
				//
				$this->mConnection->Close();
			
			} // Was connected.
			
			//
			// Reset connection object.
			//
			$this->mConnection = TRUE;
			
		} // Has connection object.
		
		return array_keys( (array) $this );											// ==>
		
	} // __sleep().

	 
	/*===================================================================================
	 *	__wakeup																		*
	 *==================================================================================*/

	/**
	 * Called after serialisation.
	 *
	 * This method will restore the connection, if the connection was open when the object
	 * was put to {@link __sleep() sleep}, the connection will be opened.
	 *
	 * @access public
	 */
	public function __wakeup()
	{
		//
		// Open connection.
		// The member is TRUE if it was connected.
		//
		if( $this->mConnection )
		{
			//
			// Connect.
			//
			$this->Connection();
			
			//
			// Load temporary table.
			//
			if( $this->mCharsed )
				$this->MarkCharacterized();
		
		} // Was connected.
		
	} // __wakeup().

	 
	/*===================================================================================
	 *	__toString																		*
	 *==================================================================================*/

	/**
	 * Return object name.
	 *
	 * This method should return the current object's name, in this class we return the
	 * data source name.
	 *
	 * @access public
	 * @return string
	 */
	public function __toString()							{	return $this->Datasource();	}

		

/*=======================================================================================
 *																						*
 *								PUBLIC MEMBER INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Datasource																		*
	 *==================================================================================*/

	/**
	 * Manage data source name.
	 *
	 * This method can be used to manage the data source name, it accepts a single
	 * parameter which represents either the value or the requested operation,
	 * depending on its type:
	 *
	 * <ul>
	 *	<li><i>NULL</i>: Return the current value.
	 *	<li><i>FALSE</i>: Delete the current value.
	 *	<li><i>other</i>: Set the value with the provided parameter.
	 * </ul>
	 *
	 * The second parameter is a boolean which if <i>TRUE</i> will return the <i>old</i>
	 * value when replacing or deleting; if <i>FALSE</i>, it will return the currently set
	 * value.
	 *
	 * @param mixed					$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 */
	public function Datasource( $theValue = NULL, $getOld = FALSE )
	{
		return $this->ManageMember( $this->mDSN, $theValue, $getOld );				// ==>

	} // Datasource.

	 
	/*===================================================================================
	 *	Crops																			*
	 *==================================================================================*/

	/**
	 * Manage crops list.
	 *
	 * This method can be used to retrieve the crop names, the method is read-only,
	 * tables are set at connection time.
	 *
	 * @access public
	 * @return array
	 */
	public function Crops()										{	return $this->mCrops;	}

	 
	/*===================================================================================
	 *	CEtables																		*
	 *==================================================================================*/

	/**
	 * Manage characterisation and evaluation tables.
	 *
	 * This method can be used to retrieve the C&E table names, the method is read-only,
	 * tables are set at connection time.
	 *
	 * @access public
	 * @return array
	 */
	public function CEtables()								{	return $this->mCETables;	}

		

/*=======================================================================================
 *																						*
 *								PUBLIC OPERATIONS INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Connection																		*
	 *==================================================================================*/

	/**
	 * Return database connection.
	 *
	 * This method can be used to retrieve the database connection, if there is no
	 * connection yet, this method will make one.
	 *
	 * @access public
	 * @return ADOConnection
	 */
	public function Connection()
	{
		//
		// Handle connection.
		//
		if( ! ($this->mConnection instanceof ADOConnection) )
		{
			//
			// Check data source name.
			//
			if( $this->Datasource() !== NULL )
			{
				//
				// Connect.
				//
				$this->mConnection = @NewADOConnection( $this->Datasource() );
				if( ! $this->mConnection )
					throw new CException
						( "Unable to connect",
						  kERROR_INVALID_PARAMETER,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Datasource' => $this->Datasource() ) );		// !@! ==>
				
				//
				// Set character set.
				//
				$this->mConnection->Execute( "SET CHARACTER SET 'utf8'" );
				
				//
				// Set fetch mode.
				//
				$this->mConnection->SetFetchMode( ADODB_FETCH_ASSOC );
				
				//
				// Load crops.
				//
				$this->_LoadCrops();
				
				//
				// Load C&E tables.
				//
				$this->_LoadCETables();
			
			} // Has data source name.
			
			//
			// Raise exception.
			//
			else
				throw new CException
					( "Unable to connect: missing data source name",
					  kERROR_OPTION_MISSING,
					  kMESSAGE_TYPE_ERROR );									// !@! ==>
		
		} // Need to connect.
		
		return $this->mConnection;													// ==>

	} // Connection.

	 
	/*===================================================================================
	 *	MarkCharacterized																*
	 *==================================================================================*/

	/**
	 * Determine characterized accessions.
	 *
	 * This method will check which accessions have been characterized or evaluated, it will
	 * create a temporary {@link kTABLE_CHARSED table} containing the Alis ID of all
	 * accessions that are referencedin the characterisation and evaluation tables.
	 *
	 * @access public
	 */
	public function MarkCharacterized()
	{
		//
		// Check if table is not already there.
		//
		if( $this->mCharsed === NULL )
		{
			//
			// Get connection.
			//
			$db = $this->Connection();
			
			//
			// Drop temporary table.
			//
			$query = 'DROP TEMPORARY TABLE IF EXISTS `'.kTABLE_CHARSED.'`';
			$ok = $db->Execute( $query );
			$ok->Close();
			
			//
			// Create temporary table.
			//
			$query = 'CREATE TEMPORARY TABLE `'.kTABLE_CHARSED.'` ('
					.'`ALIS_Id` INT NOT NULL, PRIMARY KEY (`ALIS_Id`) ) '
					.'ENGINE=MyISAM  DEFAULT CHARSET=utf8';
			$ok = $db->Execute( $query );
			$ok->Close();
			
			//
			// Iterate characterisation tables.
			//
			$count = 15;
			$tables = $this->CEtables();
			while( count( $tables ) )
			{
				//
				// Collect table names.
				//
				$i = $count;
				$list = Array();
				while( $i-- && ($tmp = array_pop( $tables )) )
					$list[] = "SELECT DISTINCT `ALIS_Id` FROM `$tmp`";
				
				//
				// Load identifiers.
				//
				$query = "REPLACE INTO `".kTABLE_CHARSED."` ";
				$query .= implode( ' UNION ', $list );
				$ok = $db->Execute( $query );
				$ok->Close();
			
			} // Tables left.
		
		} // Not already loaded.
		
		//
		// Return record count.
		//
		$query = "SELECT COUNT(*) FROM `".kTABLE_CHARSED."`";
		return $db->GetOne( $query );												// ==>

	} // MarkCharacterized.

		

/*=======================================================================================
 *																						*
 *							STATIC STRING FORMATTING METHODS							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	StringNormalise																	*
	 *==================================================================================*/

	/**
	 * Normalise string.
	 *
	 * This method can be used to format a string, the provided modifiers bitfield
	 * determines what manipulations are applied:
	 *
	 * <ul>
	 *	<li><b>{@link kFLAG_MODIFIER_UTF8 kFLAG_MODIFIER_UTF8}</b>: Convert the string to
	 *		the <i>UTF8</i> character set.
	 *	<li><b>{@link kFLAG_MODIFIER_LTRIM kFLAG_MODIFIER_LTRIM}</b>: Apply left trimming to
	 *		the string.
	 *	<li><b>{@link kFLAG_MODIFIER_RTRIM kFLAG_MODIFIER_RTRIM}</b>: Apply right trimming
	 *		to the string.
	 *	<li><b>{@link kFLAG_MODIFIER_TRIM kFLAG_MODIFIER_TRIM}</b>: Apply both left and
	 *		right trimming to the string.
	 *	<li><b>{@link kFLAG_MODIFIER_NULL kFLAG_MODIFIER_NULL}</b>: If this flag is set and
	 *		the resulting string is empty, the method will return <i>NULL</i>.
	 *	 <ul>
	 *		<li><b>{@link kFLAG_MODIFIER_NULLSTR kFLAG_MODIFIER_NULLSTR}</b>: If this flag
	 *			is set and the resulting string is empty, the method will return the
	 *			'<i>NULL</i>' string; this option implies that the
	 *			{@link kFLAG_MODIFIER_NULL kFLAG_MODIFIER_NULL} is also set.
	 *	 </ul>
	 *	<li><b>{@link kFLAG_MODIFIER_NOCASE kFLAG_MODIFIER_NOCASE}</b>: Set the string to
	 *		lowercase, this is the default way to generate a case insensitive string.
	 *	<li><b>{@link kFLAG_MODIFIER_URL kFLAG_MODIFIER_URL}</b>: URL-encode the string;
	 *		note that this option and {@link kFLAG_MODIFIER_HTML kFLAG_MODIFIER_HTML} are
	 *		mutually exclusive.
	 *	<li><b>{@link kFLAG_MODIFIER_HTML kFLAG_MODIFIER_HTML}</b>: HTML-encode the string;
	 *		note that this option and {@link kFLAG_MODIFIER_URL kFLAG_MODIFIER_URL} are
	 *		mutually exclusive.
	 *	<li><b>{@link kFLAG_MODIFIER_HEX kFLAG_MODIFIER_HEX}</b>: Convert the string to
	 *		hexadecimal; note that this option and {@link kFLAG_MODIFIER_MASK_HASH hashing}
	 *		are mutually exclusive.
	 *	 <ul>
	 *		<li><b>{@link kFLAG_MODIFIER_HEXEXP kFLAG_MODIFIER_HEXEXP}</b>: Convert the
	 *			string to a hexadecimal expression; note that this option implies
	 *			{@link kFLAG_MODIFIER_HEX kFLAG_MODIFIER_HEX}, and this option and
	 *			{@link kFLAG_MODIFIER_MASK_HASH hashing} are mutually exclusive.
	 *	 </ul>
	 *	<li><b>{@link kFLAG_MODIFIER_HASH kFLAG_MODIFIER_HASH}</b>: If this bit is set
	 *		the resulting string will be hashed using the <i>md5</i> algorithm resulting in
	 *		a 32 character hexadecimal string; this option is mutually exclusive with the
	 *		{@link kFLAG_MODIFIER_MASK_HEX kFLAG_MODIFIER_MASK_HEX} option.
	 *	 <ul>
	 *		<li><b>{@link kFLAG_MODIFIER_HASH_BIN kFLAG_MODIFIER_HASH_BIN}</b>: If this bit
	 *			is set, the resulting value should be a 16 character binary string; if the
	 *			bit is <i>OFF</i>, the resulting value should be a 32 character hexadecimal
	 *			string.
	 *	 </ul>
	 * </ul>
	 *
	 * The order in which these modifications are applied are as stated.
	 *
	 * @param string			$theString		String to normalise.
	 * @param bitfield			$theModifiers	Modifiers bitfield.
	 *
	 * @static
	 * @return mixed
	 *
	 * @see kFLAG_DEFAULT, kFLAG_MODIFIER_MASK
	 * @see kFLAG_MODIFIER_UTF8
	 * @see kFLAG_MODIFIER_LTRIM, kFLAG_MODIFIER_RTRIM, kFLAG_MODIFIER_TRIM
	 * @see kFLAG_MODIFIER_NULL, kFLAG_MODIFIER_NULLSTR
	 * @see kFLAG_MODIFIER_NOCASE, kFLAG_MODIFIER_URL, kFLAG_MODIFIER_HTML
	 * @see kFLAG_MODIFIER_HEX, kFLAG_MODIFIER_HEXEXP
	 * @see kFLAG_MODIFIER_HASH, kFLAG_MODIFIER_HASH_BIN
	 */
	static function StringNormalise( $theString, $theModifiers = kFLAG_DEFAULT )
	{
		//
		// Check if any modification was requested.
		//
		if( ($theString === NULL)							// NULL string,
		 || ($theModifiers === kFLAG_DEFAULT)				// or no modifiers,
		 || (! $theModifiers & kFLAG_MODIFIER_MASK) )		// or none relevant.
			return $theString;														// ==>
		
		//
		// We know now that something is to be done with the string.
		//
		
		//
		// Convert to string.
		//
		$string = (string) $theString;
		
		//
		// Encode string to UTF8.
		//
		if( $theModifiers & kFLAG_MODIFIER_UTF8 )
		{
			if( ! mb_check_encoding( $string, 'UTF-8' ) )
				$string = mb_convert_encoding( $string, 'UTF-8' );
		}
		
		//
		// Trim.
		//
		if( $theModifiers & kFLAG_MODIFIER_MASK_TRIM )
		{
			if( ($theModifiers & kFLAG_MODIFIER_TRIM) == kFLAG_MODIFIER_TRIM ) 
				$string = trim( $string );
			elseif( $theModifiers & kFLAG_MODIFIER_LTRIM )
				$string = ltrim( $string );
			else
				$string = rtrim( $string );
		}
		
		//
		// Handle empty string.
		//
		if( (! strlen( $string ))
		 && ($theModifiers & kFLAG_MODIFIER_MASK_NULL) )
		{
			//
			// Set to NULL string.
			//
			if( ($theModifiers & kFLAG_MODIFIER_NULLSTR) == kFLAG_MODIFIER_NULLSTR )
				return 'NULL';														// ==>
			
			return NULL;															// ==>
		
		} // Empty string and NULL mask.
		
		//
		// Set case insensitive.
		//
		if( $theModifiers & kFLAG_MODIFIER_NOCASE )
			$string = ( $theModifiers & kFLAG_MODIFIER_UTF8 )
					? mb_convert_case( $string, MB_CASE_LOWER, 'UTF-8' )
					: strtolower( $string );
		
		//
		// URL-encode.
		//
		if( $theModifiers & kFLAG_MODIFIER_URL )
			$string = urlencode( $string );
		
		//
		// HTML-encode.
		//
		elseif( $theModifiers & kFLAG_MODIFIER_HTML )
			$string = htmlspecialchars( $string );
		
		//
		// handle HEX conversion.
		//
		if( $theModifiers & kFLAG_MODIFIER_MASK_HEX )
		{
			//
			// Convert to HEX string.
			//
			$string = bin2hex( $string );
			
			//
			// Convert to HEX expression.
			//
			if( ($theModifiers & kFLAG_MODIFIER_MASK_HEX) == kFLAG_MODIFIER_HEXEXP )
				$string = "0x$string";
		
		} // HEX mask.
		
		//
		// Hash string.
		//
		elseif( $theModifiers & kFLAG_MODIFIER_MASK_HASH )
		{
			if( ($theModifiers & kFLAG_MODIFIER_HASH_BIN) == kFLAG_MODIFIER_HASH_BIN )
				return md5( $string, TRUE );										// ==>
			
			return md5( $string );													// ==>
		}
		
		return $string;																// ==>
		
	} // StringNormalise.

	 
	/*===================================================================================
	 *	DurationString																	*
	 *==================================================================================*/
	
	/**
	 * Return a formatted duration.
	 *
	 * This function will return a formatted duration string in H:MM:SS:mmmmm format, where
	 * <i>H</i> stands for hours, <i>M</i> stands for minutes, <i>S</i> stands for seconds
	 * and <i>m</i> stands for milliseconds, from the value of <i>microtime( TRUE )</i>.
	 *
	 * <i>Note: The provided value should be a difference between two timestamps taken with
	 * microtime( true ).</i>
	 *
	 * @param float				$theTime		Microtime difference.
	 *
	 * @static
	 * @return string
	 */
	static function DurationString( $theTime )
	{
		$h = floor( $theTime / 3600.0 );
		$m = floor( ( $theTime - ( $h * 3600 ) ) / 60.0 );
		$s = floor( $theTime - ( ( $h * 3600 ) + ( $m * 60 ) ) );
		$l = $theTime - ( ( $h * 3600 ) + ( $m * 60 ) + $s );
		
		return sprintf( "%d:%02d:%02d:%04d", $h, $m, $s, ( $l * 10000 ) );			// ==>
	
	} // DurationString.

		

/*=======================================================================================
 *																						*
 *							STATIC MEMBER ACCESSOR INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	ManageMember																	*
	 *==================================================================================*/

	/**
	 * Manage a member.
	 *
	 * This library implements a standard interface for managing object properties using
	 * methods, this method implements this interface:
	 *
	 * <ul>
	 *	<li><b>$theMember</b>: The member to manage, it is a reference to the element being
	 *		managed.
	 *	<li><b>$theValue</b>: The value or operation:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the member's current value.
	 *		<li><i>FALSE</i>: Reset the member, <i>NULL</i> by default.
	 *		<li><i>other</i>: Any other type represents the member's new value.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value of the member <i>before</i> it was eventually
	 *			modified.
	 *		<li><i>FALSE</i>: Return the value of the member <i>after</i> it was eventually
	 *			modified.
	 *	 </ul>
	 * </ul>
	 *
	 * @param string			   &$theMember			Offset.
	 * @param mixed					$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @static
	 * @return mixed
	 */
	static function ManageMember( &$theMember, $theValue = NULL, $getOld = FALSE )
	{
		//
		// Return current value.
		//
		if( $theValue === NULL )
			return $theMember;														// ==>

		//
		// Save current value.
		//
		$save = $theMember;
		
		//
		// Delete offset.
		//
		if( $theValue === FALSE )
			$theMember = NULL;
		
		//
		// Set offset.
		//
		else
			$theMember = $theValue;
		
		return ( $getOld ) ? $save													// ==>
						   : $theMember;											// ==>
	
	} // ManageMember.

		

/*=======================================================================================
 *																						*
 *									PROTECTED INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_LoadCrops																		*
	 *==================================================================================*/

	/**
	 * Load crops list.
	 *
	 * This method will load the list of crops, the ID in the index and the lowercase name
	 * in the value. The name corresponds to the crop prefix in table names.
	 *
	 * The method will return the table count.
	 *
	 * @access protected
	 * @return integer
	 */
	protected function _LoadCrops()
	{
		//
		// Get connection.
		//
		$db = $this->Connection();
		
		//
		// Get crop names.
		//
		$this->mCrops = Array();
		$query = "SELECT `Crop_Id`, LOWER( `Crop_Name` ) AS `NAME` FROM `crops` "
				."WHERE( `L_ID` = 1 )";
		$rs = $db->Execute( $query );
		foreach( $rs as $record )
			$this->mCrops[ $record[ 'Crop_Id' ] ] = $record[ 'NAME' ];
		$rs->Close();
		
		return count( $this->mCrops );												// ==>
	
	} // _LoadCrops.

	 
	/*===================================================================================
	 *	_LoadCETables																	*
	 *==================================================================================*/

	/**
	 * Load characterisation and evaluation tables.
	 *
	 * This method will load the list of C&E tables into a data member, this is useful,
	 * since there are a large number of these tables and matching records in those tables
	 * could be very cumbersome.
	 *
	 * The method will return the table count.
	 *
	 * @access protected
	 * @return integer
	 */
	protected function _LoadCETables()
	{
		//
		// Get connection.
		//
		$db = $this->Connection();
		
		//
		// Get table names.
		//
		$this->mCETables = Array();
		$tables = $db->MetaTables( 'TABLES' );
		foreach( $tables as $table )
		{
			//
			// Check if numeric.
			//
			if( ctype_digit( $table ) )
				$this->mCETables[] = $table;
		}
		
		return count( $this->mCETables );											// ==>
	
	} // _LoadCETables.

	 

} // class CGenesys.


?>
