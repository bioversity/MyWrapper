<?php

/**
 * <i>CFAOInstitute</i> class definition.
 *
 * This file contains the class definition of <b>CFAOInstitute</b> which represents an
 * {@link CEntity entity} mapping a FAO/WIEWS institute.
 *
 *	@package	MyWrapper
 *	@subpackage	Entities
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 06/04/2012
 */

/*=======================================================================================
 *																						*
 *									CFAOInstitute.php									*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CInstitute.php" );

/**
 * Local defines.
 *
 * This include file contains the local class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CFAOInstitute.inc.php" );

/**
 * FAO/WIEWS institute.
 *
 * This class overloads its {@link CInstitute ancestor} to implement an institute entity
 * taken from the FAO WIEWS institutes database.
 *
 * This kind of institutes adds the following properties:
 *
 * <ul>
 *	<li><i>{@link kENTITY_INST_FAO_EPACRONYM kENTITY_INST_FAO_EPACRONYM}</i>: This offset
 *		represents the institute ECPGR {@link EAcronym() acronym}.
 *	<li><i>{@link kENTITY_INST_FAO_TYPE kENTITY_INST_FAO_TYPE}</i>: This offset represents
 *		the institute set of {@link FAOType() FAO} institute types.
 *	<li><i>{@link kOFFSET_URL kOFFSET_URL}</i>: This offset represents the institute
 *		{@link URL() URL}.
 *	<li><i>{@link kOFFSET_LATITUDE kOFFSET_LATITUDE}</i>: This offset represents the
 *		institute {@link Latitude() latitude}, note this is an integer value.
 *	<li><i>{@link kOFFSET_LONGITUDE kOFFSET_LONGITUDE}</i>: This offset represents the
 *		institute {@link Longitude() longitude}, note this is an integer value.
 *	<li><i>{@link kOFFSET_ALTITUDE kOFFSET_ALTITUDE}</i>: This offset represents the
 *		institute {@link Altitude() altitude}, note this is an integer value.
 * </ul>
 *
 * The object unique {@link kTAG_ID_NATIVE identifier} is {@link _id() formed} by
 * {@link _index() using} the {@link Code() code} without any formatting.
 *
 *	@package	MyWrapper
 *	@subpackage	Entities
 */
class CFAOInstitute extends CInstitute
{
		

/*=======================================================================================
 *																						*
 *								PUBLIC MEMBER INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	EAcronym																		*
	 *==================================================================================*/

	/**
	 * Manage institute ECPGR acronym.
	 *
	 * This method can be used to handle the institute ECPGR
	 * {@link kENTITY_INST_FAO_EPACRONYM acronym}, it uses the standard accessor
	 * {@link _ManageOffset() method} to manage the
	 * {@link kENTITY_INST_FAO_EPACRONYM offset}.
	 *
	 * This value should be a string.
	 *
	 * For a more in-depth reference of this method, please consult the
	 * {@link _ManageOffset() _ManageOffset} method, in which the first parameter
	 * will be the constant {@link kENTITY_INST_FAO_EPACRONYM kENTITY_INST_FAO_EPACRONYM}.
	 *
	 * @param mixed					$theValue			Value.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses _ManageOffset
	 *
	 * @see kENTITY_INST_FAO_EPACRONYM
	 */
	public function EAcronym( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset
			( kENTITY_INST_FAO_EPACRONYM, $theValue, $getOld );						// ==>

	} // EAcronym.


	/*===================================================================================
	 *	FAOType																			*
	 *==================================================================================*/

	/**
	 * Manage FAO/WIEWS types.
	 *
	 * This method can be used to handle the institute FAO/WIEWS
	 * {@link kENTITY_INST_FAO_TYPE types} list, it uses the standard accessor
	 * {@link _ManageArrayOffset() method} to manage the
	 * list of acronyms.
	 *
	 * Each element of this list should indicate an acronym by which one refers to the
	 * current institute, the nature and specifics of these elements is the responsibility
	 * of concrete classes.
	 *
	 * For a more thorough reference of how this method works, please consult the
	 * {@link _ManageArrayOffset() _ManageArrayOffset} method, in which the first parameter
	 * will be the constant {@link kENTITY_INST_FAO_TYPE kENTITY_INST_FAO_TYPE}.
	 *
	 * @param mixed					$theValue			Value or index.
	 * @param mixed					$theOperation		Operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses _ManageArrayOffset
	 *
	 * @see kENTITY_INST_FAO_TYPE
	 */
	public function FAOType( $theValue = NULL, $theOperation = NULL, $getOld = FALSE )
	{
		return $this->_ManageArrayOffset
					( kENTITY_INST_FAO_TYPE, $theValue, $theOperation, $getOld );	// ==>

	} // FAOType.

	 
	/*===================================================================================
	 *	Latitude																		*
	 *==================================================================================*/

	/**
	 * Manage institute latitude.
	 *
	 * This method can be used to handle the institute
	 * {@link kENTITY_INST_FAO_LAT latitude}, it uses the standard accessor
	 * {@link _ManageOffset() method} to manage the {@link kENTITY_INST_FAO_LAT offset}.
	 *
	 * This value is provided as an integer, specialised classes may convert it.
	 *
	 * For a more in-depth reference of this method, please consult the
	 * {@link _ManageOffset() _ManageOffset} method, in which the first parameter
	 * will be the constant {@link kENTITY_INST_FAO_LAT kENTITY_INST_FAO_LAT}.
	 *
	 * @param mixed					$theValue			Value.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses _ManageOffset
	 *
	 * @see kENTITY_INST_FAO_LAT
	 */
	public function Latitude( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kENTITY_INST_FAO_LAT, $theValue, $getOld );	// ==>

	} // Latitude.

	 
	/*===================================================================================
	 *	Longitude																		*
	 *==================================================================================*/

	/**
	 * Manage institute longitude.
	 *
	 * This method can be used to handle the institute
	 * {@link kENTITY_INST_FAO_LON longitude}, it uses the standard accessor
	 * {@link _ManageOffset() method} to manage the {@link kENTITY_INST_FAO_LON offset}.
	 *
	 * This value is provided as an integer, specialised classes may convert it.
	 *
	 * For a more in-depth reference of this method, please consult the
	 * {@link _ManageOffset() _ManageOffset} method, in which the first parameter
	 * will be the constant {@link kENTITY_INST_FAO_LON kENTITY_INST_FAO_LON}.
	 *
	 * @param mixed					$theValue			Value.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses _ManageOffset
	 *
	 * @see kENTITY_INST_FAO_LON
	 */
	public function Longitude( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kENTITY_INST_FAO_LON, $theValue, $getOld );	// ==>

	} // Longitude.

	 
	/*===================================================================================
	 *	Altitude																		*
	 *==================================================================================*/

	/**
	 * Manage institute altitude.
	 *
	 * This method can be used to handle the institute
	 * {@link kENTITY_INST_FAO_ALT altitude}, it uses the standard accessor
	 * {@link _ManageOffset() method} to manage the {@link kENTITY_INST_FAO_ALT offset}.
	 *
	 * This value is provided as an integer, specialised classes may convert it.
	 *
	 * For a more in-depth reference of this method, please consult the
	 * {@link _ManageOffset() _ManageOffset} method, in which the first parameter
	 * will be the constant {@link kENTITY_INST_FAO_ALT kENTITY_INST_FAO_ALT}.
	 *
	 * @param mixed					$theValue			Value.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses _ManageOffset
	 *
	 * @see kENTITY_INST_FAO_ALT
	 */
	public function Altitude( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kENTITY_INST_FAO_ALT, $theValue, $getOld );	// ==>

	} // Altitude.

		

/*=======================================================================================
 *																						*
 *									STATIC INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Update																			*
	 *==================================================================================*/

	/**
	 * Update institutes.
	 *
	 * This method will download the current FAO/WIEWS export file and load its contents
	 * into the provided container.
	 *
	 * If you provide the second parameter, it will be used to filter only institutes
	 * modified after the provided date; the format of that parameter is a date as
	 * <i>YYYY-MM-DD</i>.
	 *
	 * The method will return the number of updated institutes.
	 *
	 * <i>Note that the method will commit the records using the
	 * {@link kFLAG_PERSIST_REPLACE kFLAG_PERSIST_REPLACE} and
	 * {@link kFLAG_STATE_ENCODED kFLAG_STATE_ENCODED} flags.
	 *
	 * @param CContainer			$theContainer		Data container.
	 * @param string				$theDate			Update date.
	 *
	 * @static
	 * @return integer
	 */
	static function Update( $theContainer, $theDate = NULL )
	{
		//
		// Init field names.
		//
		$fields = array
		(
			'INSTCODE',
			'ACRONYM',
			'ECPACRONYM',
			'FULL_NAME',
			'TYPE',
			'PGR_ACTIVITY',
			'MAINTCOLL',
			'STREET_POB',
			'CITY_STATE',
			'ZIP_CODE',
			'PHONE',
			'FAX',
			'EMAIL',
			'URL',
			'LATITUDE',
			'LONGITUDE',
			'ALTITUDE',
			'UPDATED_ON',
			'V_INSTCODE'
		);
		
		//
		// Create temp files.
		//
		$zip = tempnam( '', '' );
		$txt = tempnam( '', '' );

		//
		// Copy file locally.
		//
		if( file_put_contents( $zip, file_get_contents( kENTITY_INST_FAO_DOWNLOAD ) )
			!== FALSE )
		{
			//
			// Open zip file.
			//
			$zp = zip_open( $zip );
			
			//
			// Read file.
			//
			if( $zp )
			{
				//
				// Cycle entries.
				//
				if( $entry = zip_read( $zp ) )
					$ok =
						file_put_contents
							( $txt, zip_entry_read
										( $entry, zip_entry_filesize( $entry ) ) );
				
				//
				// Close and delete zip file.
				//
				zip_entry_close( $entry );
				zip_close( $zp );
				unlink( $zip );
				
				//
				// Handle Mac EOL.
				//
				$save = ini_set( 'auto_detect_line_endings', 1 );
				
				//
				// Open file.
				//
				$fp = fopen( $txt, 'r' );
				if( $fp !== FALSE )
				(
					//
					// Cycle file.
					//
					while( ($data = fgetcsv( $fp, 4096, ',', '"' )) !== FALSE )
					{
					
					} // Iterating file.
				
				) // Opened text file.
				
				//
				// Unable to read zip file.
				//
				else
					throw new CException
						( "Unable to read text file",
						  kERROR_INVALID_PARAMETER,
						  kMESSAGE_TYPE_ERROR,
						  array( 'URL' => kENTITY_INST_FAO_DOWNLOAD ) );		// !@! ==>
				
				//
				// Reset EOL.
				//
				ini_set( 'auto_detect_line_endings', $save );
			
			} // Read zip file.
			
			//
			// Unable to read zip file.
			//
			else
				throw new CException
					( "Unable to read zip file",
					  kERROR_INVALID_PARAMETER,
					  kMESSAGE_TYPE_ERROR,
					  array( 'URL' => kENTITY_INST_FAO_DOWNLOAD ) );			// !@! ==>
			
		} // Loaded file.
		
		//
		// Unable to download file.
		//
		else
			throw new CException
				( "Unable to download file",
				  kERROR_INVALID_PARAMETER,
				  kMESSAGE_TYPE_ERROR,
				  array( 'URL' => kENTITY_INST_FAO_DOWNLOAD ) );				// !@! ==>
		
		
		
		
		//
		// Check data.
		//
		if( is_array( $theData )
		 || ($theData instanceof ArrayObject) )
		{
			//
			// Valid institute record.
			//
			if( array_key_exists( 'INSTCODE', (array) $theData ) )
			{
				//
				// Init local storage.
				//
				$inst = new CFAOInstitute();
				$addr = new CMailAddress();
			
				//
				// Get institute code.
				//
				$inst->Code( $theData[ 'INSTCODE' ] );
				
				//
				// Load other elements.
				//
				if( array_key_exists( 'ACRONYM', (array) $theData )
				 && strlen( $tmp = trim( $theData[ 'ACRONYM' ] ) ) )
					$inst->Acronym( $tmp );

				if( array_key_exists( 'ECPACRONYM', (array) $theData )
				 && strlen( $tmp = trim( $theData[ 'ECPACRONYM' ] ) ) )
					$inst->EAcronym( $tmp );

				if( array_key_exists( 'FULL_NAME', (array) $theData )
				 && strlen( $tmp = trim( $theData[ 'FULL_NAME' ] ) ) )
					$inst->Name( $tmp );

				if( array_key_exists( 'TYPE', (array) $theData )
				 && strlen( $theData[ 'TYPE' ] ) )
				{
					$list = explode( '/', $theData[ 'TYPE' ] );
					foreach( $list as $element )
					{
						if( strlen( $tmp = trim( $element ) ) )
							$inst->FAOType( $tmp, TRUE );
					}
				}

				if( array_key_exists( 'PGR_ACTIVITY', (array) $theData )
				 && ($theData[ 'PGR_ACTIVITY' ] == 'Y') )
					$inst->Kind( kENTITY_INST_FAO_ACT_PGR, TRUE );

				if( array_key_exists( 'MAINTCOLL', (array) $theData )
				 && ($theData[ 'MAINTCOLL' ] == 'Y') )
					$inst->Kind( kENTITY_INST_FAO_ACT_COLL, TRUE );

				if( array_key_exists( 'STREET_POB', (array) $theData )
				 && strlen( $tmp = trim( $theData[ 'STREET_POB' ] ) ) )
					$addr->Street( $tmp );

				if( array_key_exists( 'CITY_STATE', (array) $theData )
				 && strlen( $tmp = trim( $theData[ 'CITY_STATE' ] ) ) )
					$addr->City( $tmp );

				if( array_key_exists( 'ZIP_CODE', (array) $theData )
				 && strlen( $tmp = trim( $theData[ 'ZIP_CODE' ] ) ) )
					$addr->Zip( $tmp );
				
				if( count( $addr ) )
				{
					$addr->Country( substr( $theData[ 'INSTCODE' ], 0, 3 ) );
					$inst->Mail( $addr );
				}

				if( array_key_exists( 'PHONE', (array) $theData )
				 && strlen( $tmp = trim( $theData[ 'PHONE' ] ) ) )
					$inst->Phone( $tmp );

				if( array_key_exists( 'FAX', (array) $theData )
				 && strlen( $tmp = trim( $theData[ 'FAX' ] ) ) )
					$inst->Fax( $tmp );

				if( array_key_exists( 'EMAIL', (array) $theData )
				 && strlen( $tmp = trim( $theData[ 'EMAIL' ] ) ) )
					$inst->Email( $tmp );

				if( array_key_exists( 'URL', (array) $theData )
				 && strlen( $tmp = trim( $theData[ 'URL' ] ) ) )
					$inst->URL( $tmp );

				if( array_key_exists( 'LATITUDE', (array) $theData )
				 && strlen( $tmp = trim( $theData[ 'LATITUDE' ] ) ) )
					$inst->Latitude( $tmp );

				if( array_key_exists( 'LONGITUDE', (array) $theData )
				 && strlen( $tmp = trim( $theData[ 'LONGITUDE' ] ) ) )
					$inst->Longitude( $tmp );

				if( array_key_exists( 'ALTITUDE', (array) $theData )
				 && strlen( $tmp = trim( $theData[ 'ALTITUDE' ] ) ) )
					$inst->Altitude( $tmp );

				if( array_key_exists( 'UPDATED_ON', (array) $theData )
				 && strlen( $tmp = trim( $theData[ 'UPDATED_ON' ] ) ) )
					$inst->Stamp( new CDataTypeStamp( $tmp ) );

				if( array_key_exists( 'V_INSTCODE', (array) $theData )
				 && strlen( $tmp = trim( $theData[ 'V_INSTCODE' ] ) ) )
					$inst->Valid( $tmp );
				
				//
				// Commit institute.
				//
				$inst->Commit( $theContainer, NULL, kFLAG_PERSIST_REPLACE +
													kFLAG_STATE_ENCODED );
				
				return 1;															// ==>
			
			} // Valid institute.
		
		} // Valid data.
		
		else
			throw new CException
				( "Invalid import data format",
				  kERROR_INVALID_PARAMETER,
				  kMESSAGE_TYPE_ERROR,
				  array( 'Data' => $theData ) );								// !@! ==>
			
		//
		// Init local storage.
		//
		$count = 0;
			
		//
		// Assume it is a list.
		//
		foreach( $theData as $element )
			$count += self::Import( $theContainer, $element );
		
		return $count;																// ==>

	} // Update.

		

/*=======================================================================================
 *																						*
 *							PROTECTED IDENTIFICATION INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_id																				*
	 *==================================================================================*/

	/**
	 * Return the object's unique identifier.
	 *
	 * In this class we use directly the value of the {@link _index() index} method: it is
	 * a 7 character string, so it is not necessary to hash the result.
	 *
	 * @access protected
	 * @return mixed
	 */
	protected function _id()
	{
		//
		// In this class we hash the index value.
		//
		return $this->_index();														// ==>
	
	} // _id.

	 
	/*===================================================================================
	 *	_index																			*
	 *==================================================================================*/

	/**
	 * Return the object's unique index.
	 *
	 * In this class we return directly the institute {@link Code() code}, we do not prefix
	 * the code with the domain, since the {@link Code() code} it has a specific and
	 * univoque format.
	 *
	 * @access protected
	 * @return string
	 */
	protected function _index()									{	return $this->Code();	}

		

/*=======================================================================================
 *																						*
 *								PROTECTED PERSISTENCE UTILITIES							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_PrepareCommit																	*
	 *==================================================================================*/

	/**
	 * Normalise parameters of a store.
	 *
	 * We overload this method to add the {@link kENTITY_INST_FAO kENTITY_INST_FAO}
	 * {@link Type() type} to the object prior {@link Commit() saving} it.
	 *
	 * @param reference			   &$theContainer		Object container.
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Commit modifiers.
	 *
	 * @access protected
	 *
	 * @throws {@link CException CException}
	 *
	 * @see kERROR_OPTION_MISSING
	 */
	protected function _PrepareCommit( &$theContainer, &$theIdentifier, &$theModifiers )
	{
		//
		// Call parent method.
		//
		parent::_PrepareCommit( $theContainer, $theIdentifier, $theModifiers );
		
		//
		// Add institute kind.
		//
		$this->Kind( kENTITY_INST_FAO, TRUE );
		
	} // _PrepareCommit.

	 

} // class CFAOInstitute.


?>
