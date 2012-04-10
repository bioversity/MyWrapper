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
	 *	EAcronym																			*
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
	 * will be the constant {@link kTAG_NAME kTAG_NAME}.
	 *
	 * @param mixed					$theValue			Value.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses _ManageOffset
	 *
	 * @see kTAG_NAME
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
	 * will be the constant {@link kOFFSET_ACRONYM kOFFSET_ACRONYM}.
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
	 * @see kOFFSET_ACRONYM
	 */
	public function Acronym( $theValue = NULL, $theOperation = NULL, $getOld = FALSE )
	{
		return $this->_ManageArrayOffset
					( kOFFSET_ACRONYM, $theValue, $theOperation, $getOld );			// ==>

	} // Acronym.

	 
	/*===================================================================================
	 *	URL																				*
	 *==================================================================================*/

	/**
	 * Manage institute urls.
	 *
	 * This method can be used to manage the institute {@link kOFFSET_URL URL} or web pages
	 * list, the method expects the following parameters:
	 *
	 * <ul>
	 *	<li><i>{@link kTAG_KIND kTAG_KIND}</i>: The institute URL kind, this could be
	 *		<i>main</i>, <i>sales</i> or <i>international</i>. This element represents the
	 *		array key, although technically it is implemented as an element to allow
	 *		searching on all values.
	 *	<li><i>{@link kTAG_DATA kTAG_DATA}</i>: The URL or web page address.
	 * </ul>
	 *
	 * The parameters to this method are:
	 *
	 * <ul>
	 *	<li><b>$theValue</b>: The value or operation:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the current value selected by the second parameter.
	 *		<li><i>FALSE</i>: Delete the value selected by the second parameter.
	 *		<li><i>other</i>: Set value selected by the second parameter.
	 *	 </ul>
	 *	<li><b>$theType</b>: The element type, kind or index:
	 *	 <ul>
	 *		<li><i>NULL</i>: This value indicates that the URL has no type or kind, in
	 *			general, when adding elements, this case applies to default elements.
	 *		<li><i>other</i>: All other types will be interpreted as the kind or type of
	 *			the URL.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value <i>before</i> it was eventually modified.
	 *		<li><i>FALSE</i>: Return the value <i>after</i> it was eventually modified.
	 *	 </ul>
	 * </ul>
	 *
	 * @param string				$theValue			URL or operation.
	 * @param mixed					$theType			Mailing address kind or index.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 */
	public function URL( $theValue = NULL, $theType = NULL, $getOld = FALSE )
	{
		return $this->_ManageTypedArrayOffset
			( kOFFSET_URL, kTAG_KIND, $theType, $theValue, $getOld );				// ==>

	} // URL.

	 
	/*===================================================================================
	 *	Latitude																		*
	 *==================================================================================*/

	/**
	 * Manage institute latitude.
	 *
	 * This method can be used to handle the institute {@link kOFFSET_LATITUDE latitude}, it
	 * uses the standard accessor {@link _ManageOffset() method} to manage the
	 * {@link kOFFSET_LATITUDE offset}.
	 *
	 * This value is provided as an integer, specialised classes may convert it.
	 *
	 * For a more in-depth reference of this method, please consult the
	 * {@link _ManageOffset() _ManageOffset} method, in which the first parameter
	 * will be the constant {@link kTAG_NAME kTAG_NAME}.
	 *
	 * @param mixed					$theValue			Value.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses _ManageOffset
	 *
	 * @see kOFFSET_LATITUDE
	 */
	public function Latitude( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kOFFSET_LATITUDE, $theValue, $getOld );		// ==>

	} // Latitude.

	 
	/*===================================================================================
	 *	Longitude																		*
	 *==================================================================================*/

	/**
	 * Manage institute longitude.
	 *
	 * This method can be used to handle the institute {@link kOFFSET_LONGITUDE longitude},
	 * it uses the standard accessor {@link _ManageOffset() method} to manage the
	 * {@link kOFFSET_LONGITUDE offset}.
	 *
	 * This value is provided as an integer, specialised classes may convert it.
	 *
	 * For a more in-depth reference of this method, please consult the
	 * {@link _ManageOffset() _ManageOffset} method, in which the first parameter
	 * will be the constant {@link kTAG_NAME kTAG_NAME}.
	 *
	 * @param mixed					$theValue			Value.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses _ManageOffset
	 *
	 * @see kOFFSET_LONGITUDE
	 */
	public function Longitude( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kOFFSET_LONGITUDE, $theValue, $getOld );		// ==>

	} // Longitude.

	 
	/*===================================================================================
	 *	Altitude																		*
	 *==================================================================================*/

	/**
	 * Manage institute altitude.
	 *
	 * This method can be used to handle the institute {@link kOFFSET_ALTITUDE altitude},
	 * it uses the standard accessor {@link _ManageOffset() method} to manage the
	 * {@link kOFFSET_ALTITUDE offset}.
	 *
	 * This value is provided as an integer, specialised classes may convert it.
	 *
	 * For a more in-depth reference of this method, please consult the
	 * {@link _ManageOffset() _ManageOffset} method, in which the first parameter
	 * will be the constant {@link kTAG_NAME kTAG_NAME}.
	 *
	 * @param mixed					$theValue			Value.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses _ManageOffset
	 *
	 * @see kOFFSET_ALTITUDE
	 */
	public function Altitude( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kOFFSET_ALTITUDE, $theValue, $getOld );		// ==>

	} // Altitude.

		

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
	 * This method will take the provided data and load it into the provided
	 * {@link CContainer container}. The data is expected to have the same format as the
	 * FAO/WIEWS export file.
	 *
	 * The method will first check if the 
	 *
	 * @param CContainer			$theContainer		Data container.
	 * @param array					$theData			Import data.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses _ManageOffset
	 *
	 * @see kTAG_NAME
	 */
	public function EAcronym( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset
			( kENTITY_INST_FAO_EPACRONYM, $theValue, $getOld );						// ==>

	} // EAcronym.

		

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
