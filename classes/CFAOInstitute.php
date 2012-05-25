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
 *	<li><i>{@link kENTITY_INST_FAO_LAT kOFFSET_kENTITY_INST_FAO_LATLATITUDE}</i>: This
 *		offset represents the institute {@link Latitude() latitude}, note this is an integer
 *		value.
 *	<li><i>{@link kENTITY_INST_FAO_LON kENTITY_INST_FAO_LON}</i>: This offset represents the
 *		institute {@link Longitude() longitude}, note this is an integer value.
 *	<li><i>{@link kENTITY_INST_FAO_ALT kENTITY_INST_FAO_ALT}</i>: This offset represents the
 *		institute {@link Altitude() altitude}, note this is an integer value.
 * </ul>
 *
 * Unlike its {@link CInstitute parent}, this class will use the institute
 * {@link Code() code} as-is for the object unique {@link _id() identifier}, the index
 * {@link HashIndex() hashing} method will not transform the {@link _index() index} since
 * it is at most 7 characters thus cannot conflict with other {@link CEntity entity}
 * identifiers.
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
	 * {@link CAttribute::ManageOffset() method} to manage the
	 * {@link kENTITY_INST_FAO_EPACRONYM offset}.
	 *
	 * This value should be a string.
	 *
	 * For a more in-depth reference of this method, please consult the
	 * {@link CAttribute::ManageOffset() CAttribute::ManageOffset} method, in which the
	 * second parameter will be the constant
	 * {@link kENTITY_INST_FAO_EPACRONYM kENTITY_INST_FAO_EPACRONYM}.
	 *
	 * @param mixed					$theValue			Value.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses CAttribute::ManageOffset()
	 *
	 * @see kENTITY_INST_FAO_EPACRONYM
	 */
	public function EAcronym( $theValue = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageOffset
			( $this, kENTITY_INST_FAO_EPACRONYM, $theValue, $getOld );				// ==>

	} // EAcronym.


	/*===================================================================================
	 *	FAOType																			*
	 *==================================================================================*/

	/**
	 * Manage FAO/WIEWS types.
	 *
	 * This method can be used to handle the institute FAO/WIEWS
	 * {@link kENTITY_INST_FAO_TYPE types} list, it uses the standard accessor
	 * {@link CAttribute::ManageArrayOffset() method} to manage the
	 * list of acronyms.
	 *
	 * Each element of this list should indicate an acronym by which one refers to the
	 * current institute, the nature and specifics of these elements is the responsibility
	 * of concrete classes.
	 *
	 * For a more thorough reference of how this method works, please consult the
	 * {@link CAttribute::ManageArrayOffset() CAttribute::ManageArrayOffset} method, in
	 * which the second parameter will be the constant
	 * {@link kENTITY_INST_FAO_TYPE kENTITY_INST_FAO_TYPE}.
	 *
	 * @param mixed					$theValue			Value or index.
	 * @param mixed					$theOperation		Operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses CAttribute::ManageArrayOffset()
	 *
	 * @see kENTITY_INST_FAO_TYPE
	 */
	public function FAOType( $theValue = NULL, $theOperation = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageArrayOffset
					( $this, kENTITY_INST_FAO_TYPE,
					  $theValue, $theOperation, $getOld );							// ==>

	} // FAOType.

	 
	/*===================================================================================
	 *	Latitude																		*
	 *==================================================================================*/

	/**
	 * Manage institute latitude.
	 *
	 * This method can be used to handle the institute
	 * {@link kENTITY_INST_FAO_LAT latitude}, it uses the standard accessor
	 * {@link CAttribute::ManageOffset() method} to manage the
	 * {@link kENTITY_INST_FAO_LAT offset}.
	 *
	 * This value is provided as an integer, specialised classes may convert it.
	 *
	 * For a more in-depth reference of this method, please consult the
	 * {@link CAttribute::ManageOffset() CAttribute::ManageOffset} method, in which the
	 * second parameter will be the constant
	 * {@link kENTITY_INST_FAO_LAT kENTITY_INST_FAO_LAT}.
	 *
	 * @param mixed					$theValue			Value.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses CAttribute::ManageOffset()
	 *
	 * @see kENTITY_INST_FAO_LAT
	 */
	public function Latitude( $theValue = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageOffset
				( $this, kENTITY_INST_FAO_LAT, $theValue, $getOld );	// ==>

	} // Latitude.

	 
	/*===================================================================================
	 *	Longitude																		*
	 *==================================================================================*/

	/**
	 * Manage institute longitude.
	 *
	 * This method can be used to handle the institute
	 * {@link kENTITY_INST_FAO_LON longitude}, it uses the standard accessor
	 * {@link CAttribute::ManageOffset() method} to manage the
	 * {@link kENTITY_INST_FAO_LON offset}.
	 *
	 * This value is provided as an integer, specialised classes may convert it.
	 *
	 * For a more in-depth reference of this method, please consult the
	 * {@link CAttribute::ManageOffset() CAttribute::ManageOffset} method, in which the
	 * second parameter will be the constant
	 * {@link kENTITY_INST_FAO_LON kENTITY_INST_FAO_LON}.
	 *
	 * @param mixed					$theValue			Value.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses CAttribute::ManageOffset()
	 *
	 * @see kENTITY_INST_FAO_LON
	 */
	public function Longitude( $theValue = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageOffset
				( $this, kENTITY_INST_FAO_LON, $theValue, $getOld );				// ==>

	} // Longitude.

	 
	/*===================================================================================
	 *	Altitude																		*
	 *==================================================================================*/

	/**
	 * Manage institute altitude.
	 *
	 * This method can be used to handle the institute
	 * {@link kENTITY_INST_FAO_ALT altitude}, it uses the standard accessor
	 * {@link CAttribute::ManageOffset() method} to manage the
	 * {@link kENTITY_INST_FAO_ALT offset}.
	 *
	 * This value is provided as an integer, specialised classes may convert it.
	 *
	 * For a more in-depth reference of this method, please consult the
	 * {@link CAttribute::ManageOffset() CAttribute::ManageOffset} method, in which the
	 * second parameter will be the constant
	 * {@link kENTITY_INST_FAO_ALT kENTITY_INST_FAO_ALT}.
	 *
	 * @param mixed					$theValue			Value.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses CAttribute::ManageOffset()
	 *
	 * @see kENTITY_INST_FAO_ALT
	 */
	public function Altitude( $theValue = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageOffset
				( $this, kENTITY_INST_FAO_ALT, $theValue, $getOld );				// ==>

	} // Altitude.

		

/*=======================================================================================
 *																						*
 *								PUBLIC ARRAY ACCESS INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	offsetSet																		*
	 *==================================================================================*/

	/**
	 * Set a value for a given offset.
	 *
	 * We overload this method to override the {@link _IsInited() inited}
	 * {@link kFLAG_STATE_INITED status} of the {@link CInstitute parent} class: FAO
	 * institutes may not have a {@link Name() name} set, so we call the
	 * {@link CEntity entity} version of this method.
	 *
	 * @param string				$theOffset			Offset.
	 * @param string|NULL			$theValue			Value to set at offset.
	 *
	 * @access public
	 */
	public function offsetSet( $theOffset, $theValue )
	{
		//
		// Call entity method.
		//
		CEntity::offsetSet( $theOffset, $theValue );
	
	} // offsetSet.

	 
	/*===================================================================================
	 *	offsetUnset																		*
	 *==================================================================================*/

	/**
	 * Reset a value for a given offset.
	 *
	 * We overload this method to override the {@link _IsInited() inited}
	 * {@link kFLAG_STATE_INITED status} of the {@link CInstitute parent} class: FAO
	 * institutes may not have a {@link Name() name} set, so we call the
	 * {@link CEntity entity} version of this method.
	 *
	 * @param string				$theOffset			Offset.
	 *
	 * @access public
	 */
	public function offsetUnset( $theOffset )
	{
		//
		// Call parent method.
		//
		CEntity::offsetUnset( $theOffset );
	
	} // offsetUnset.

		

/*=======================================================================================
 *																						*
 *									STATIC INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Import																			*
	 *==================================================================================*/

	/**
	 * Import institutes.
	 *
	 * This method can be used to import the institutes list from the current FAO/WIEWS
	 * export file, the method expects two parameters:
	 *
	 * <ul>
	 *	<li><b>$theContainer</b>: The {@link CContainer container} in which the institutes
	 *		are stored. 
	 *	<li><b>$theURL</b>: The download URL of the FAO/WIEWS export file.
	 * </ul>
	 *
	 * The method will return the number of records added and replaced.
	 *
	 * <i>Note that the method will commit the records using the
	 * {@link kFLAG_PERSIST_REPLACE kFLAG_PERSIST_REPLACE} and
	 * {@link kFLAG_STATE_ENCODED kFLAG_STATE_ENCODED} flags</i>.
	 *
	 * @param CContainer			$theContainer		Data container.
	 * @param string				$theURL				Import file path.
	 *
	 * @static
	 * @return integer
	 */
	static function Import( $theContainer, $theURL = kENTITY_INST_FAO_DOWNLOAD )
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
				{
					//
					// Cycle file.
					//
					$count = 0;
					while( ($row = fgetcsv( $fp, 4096, ',', '"' )) !== FALSE )
					{
						//
						// Init local storage.
						//
						$inst = new CFAOInstitute();
						$addr = new CMailAddress();
						$country = NULL;
						
						//
						// Iterate header.
						//
						foreach( $fields as $index => $field )
						{
							//
							// Check if data field exists.
							//
							if( $index < count( $row ) )
							{
								//
								// Skip 'null' fields.
								//
								if( strtolower( $row[ $index ] ) == 'null' )
									$field = NULL;
								
								//
								// Parse by field.
								//
								switch( $field )
								{
									case 'INSTCODE':
										$inst->Code( $row[ $index ] );
										$country = substr( $row[ $index ], 0, 3 );
										break;
								
									case 'ACRONYM':
										if( strlen( $tmp = trim( $row[ $index ] ) ) )
											$inst->Acronym( $tmp );
										break;
								
									case 'ECPACRONYM':
										if( strlen( $tmp = trim( $row[ $index ] ) ) )
											$inst->EAcronym( $tmp );
										break;
								
									case 'FULL_NAME':
										if( strlen( $tmp = trim( $row[ $index ] ) ) )
											$inst->Name( $tmp );
										break;
								
									case 'TYPE':
										if( strlen( $row[ $index ] ) )
										{
											$list = explode( '/', $row[ $index ] );
											foreach( $list as $element )
											{
												if( strlen( $tmp = trim( $element ) ) )
													$inst->FAOType( $tmp, TRUE );
											}
										}
										break;
								
									case 'PGR_ACTIVITY':
										 if( $row[ $index ] == 'Y' )
											$inst->Kind( kENTITY_INST_FAO_ACT_PGR, TRUE );
										break;
								
									case 'MAINTCOLL':
										 if( $row[ $index ] == 'Y' )
											$inst->Kind( kENTITY_INST_FAO_ACT_COLL, TRUE );
										break;
								
									case 'STREET_POB':
										if( strlen( $tmp = trim( $row[ $index ] ) ) )
											$addr->Street( $tmp );
										break;
								
									case 'CITY_STATE':
										if( strlen( $tmp = trim( $row[ $index ] ) ) )
											$addr->City( $tmp );
										break;
								
									case 'ZIP_CODE':
										if( strlen( $tmp = trim( $row[ $index ] ) ) )
											$addr->Zip( $tmp );
										break;
								
									case 'PHONE':
										if( strlen( $tmp = trim( $row[ $index ] ) ) )
											$inst->Phone( $tmp );
										break;
								
									case 'FAX':
										if( strlen( $tmp = trim( $row[ $index ] ) ) )
											$inst->Fax( $tmp );
										break;
								
									case 'EMAIL':
										if( strlen( $tmp = trim( $row[ $index ] ) ) )
											$inst->Email( $tmp );
										break;
								
									case 'URL':
										if( strlen( $tmp = trim( $row[ $index ] ) ) )
											$inst->URL( $tmp );
										break;
								
									case 'LATITUDE':
										if( strlen( $tmp = trim( $row[ $index ] ) ) )
											$inst->Latitude( $tmp );
										break;
								
									case 'LONGITUDE':
										if( strlen( $tmp = trim( $row[ $index ] ) ) )
											$inst->Longitude( $tmp );
										break;
								
									case 'ALTITUDE':
										if( strlen( $tmp = trim( $row[ $index ] ) ) )
											$inst->Altitude( $tmp );
										break;
								
									case 'UPDATED_ON':
										if( strlen( $tmp = trim( $row[ $index ] ) ) )
										{
											$tmp = explode( '/', $tmp );
											$date = $tmp[ 2 ].'-'.$tmp[ 1 ].'-'.$tmp[ 0 ];
											$inst->Modified( new CDataTypeStamp( $date ) );
										}
										break;
								
									case 'V_INSTCODE':
										if( strlen( $tmp = trim( $row[ $index ] ) ) )
										{
											if( $tmp != $inst->Code() )
												$inst->Valid( $tmp );
										}
										break;
								
								} // Parsed field.
							
							} // Data field there.
							
							//
							// Reached end of data fields.
							//
							else
								break;										// =>
						
						} // Iterating fields.
						
						//
						// Handle address.
						//
						if( count( $addr ) )
						{
							//
							// Add country.
							//
							$addr->Country( $country );
							
							//
							// Add address.
							//
							$inst->Mail( $addr );
						
						} // Has address
						
						//
						// Check institute.
						//
						if( count( $inst ) )
						{
							//
							// Commit institute.
							//
							$inst->Commit( $theContainer, NULL, kFLAG_PERSIST_REPLACE +
																kFLAG_STATE_ENCODED );
							
							//
							// Count.
							//
							$count++;
						
						} // Has data.
					
					} // Iterating file.
					
					//
					// Close and delete text file.
					//
					fclose( $fp );
					unlink( $txt );
				
				} // Opened text file.
				
				//
				// Unable to read zip file.
				//
				else
				{
					//
					// Reset EOL.
					//
					ini_set( 'auto_detect_line_endings', $save );
				
					throw new CException
						( "Unable to read text file",
						  kERROR_INVALID_PARAMETER,
						  kMESSAGE_TYPE_ERROR,
						  array( 'URL' => kENTITY_INST_FAO_DOWNLOAD ) );		// !@! ==>
				}
			
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
		
		return $count;																// ==>

	} // Import.

	 
	/*===================================================================================
	 *	HashIndex																		*
	 *==================================================================================*/

	/**
	 * Hash index.
	 *
	 * In this class we do not nodify the provided value, it is supposed to be the FAO
	 * institute <i>INSTCODE</i> field.
	 *
	 * @param string				$theValue			Value to hash.
	 *
	 * @static
	 * @return string
	 */
	static function HashIndex( $theValue )
	{
		return $theValue;															// ==>
	
	} // HashIndex.

		

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
		
		//
		// Set global identifier.
		//
		$this[ kTAG_GID ] = $this->_index();
		
	} // _PrepareCommit.

	 

} // class CFAOInstitute.


?>
