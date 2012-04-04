<?php

/**
 * <i>CMailAddress</i> class definition.
 *
 * This file contains the class definition of <b>CMailAddress</b> which wraps this class
 * {@link CArrayObject ancestor} around a general purpose mailing address structure.
 *
 *	@package	MyWrapper
 *	@subpackage	Traits
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 03/04/2012
*/

/*=======================================================================================
 *																						*
 *									CMailAddress.php									*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CArrayObject.php" );

/**
 *	Mailing address.
 *
 * This class implements a mailing address, it wraps the {@link CArrayObject parent} class
 * around a structure that defines a mailing address.
 *
 * In general instances of this class will be embedded in
 * {@link CPersistentObject persistent} objects to add lists of addresses, the current class
 * does not feature any persistence functions, it only concentrates in managing an address
 * as a set of properties:
 *
 * <ul>
 *	<li><i>{@link Place() Place}</i>: This {@link kOFFSET_MAIL_PLACE property} defines a
 *		named place or location, it should be used only if required.
 *	<li><i>{@link Care() Care} of</i>: This {@link kOFFSET_MAIL_CARE property} indicates
 *		who is the owner or reference at the address that is not the same as the sender. It
 *		should be used only if required.
 *	<li><i>{@link Street() Street}</i>: This {@link kOFFSET_MAIL_STREET property} indicates
 *		the street name or P.O. box number.
 *	<li><i>{@link Zip() Zip}</i>: This {@link kOFFSET_MAIL_ZIP property} indicates the ZIP
 *		code.
 *	<li><i>{@link City() City}</i>: This {@link kOFFSET_MAIL_CITY property} indicates the
 *		address city name.
 *	<li><i>{@link Province() Province}</i>: This {@link kOFFSET_MAIL_PROVINCE property}
 *		indicates the address province name or code.
 *	<li><i>{@link Country() Country}</i>: This {@link kOFFSET_MAIL_COUNTRY property}
 *		indicates the address country name or code.
 *	<li><i>{@link Full() Full} address</i>: This {@link kOFFSET_MAIL_FULL property} can be
 *		used when an address does not have its properties separated, or it can be used as an
 *		export feature.
 * </ul>
 *
 *	@package	MyWrapper
 *	@subpackage	Traits
 */
class CMailAddress extends CArrayObject
{
		

/*=======================================================================================
 *																						*
 *										MAGIC											*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	__construct																		*
	 *==================================================================================*/

	/**
	 * Instantiate class.
	 *
	 * The constructor will instantiate an object either from an array, by loading all
	 * corresponding properties, from a string, in which case it will be interpreted as the
	 * {@link Full() full} address, or as an empty address.
	 *
	 * @param mixed					$theAddress			Address structure or string.
	 *
	 * @access public
	 */
	public function __construct( $theAddress = NULL )
	{
		//
		// Empty statement.
		//
		if( $theAddress === NULL )
			parent::__construct();
		
		//
		// Handle provided statement.
		//
		elseif( is_array( $theAddress )
			 || ($theAddress instanceof ArrayObject) )
		{
			//
			// Select properties.
			//
			foreach( $theAddress as $key => $value )
			{
				//
				// Parse property.
				//
				switch( $key )
				{
					case kOFFSET_MAIL_PLACE:
						$this->Place( $value );
						break;
				
					case kOFFSET_MAIL_CARE:
						$this->Care( $value );
						break;
				
					case kOFFSET_MAIL_STREET:
						$this->Street( $value );
						break;
				
					case kOFFSET_MAIL_ZIP:
						$this->Zip( $value );
						break;
				
					case kOFFSET_MAIL_CITY:
						$this->City( $value );
						break;
				
					case kOFFSET_MAIL_PROVINCE:
						$this->Province( $value );
						break;
				
					case kOFFSET_MAIL_COUNTRY:
						$this->Country( $value );
						break;
				
					case kOFFSET_MAIL_FULL:
						$this->Full( $value );
						break;
				
				} // Parsed property.
			
			} // Iterating properties.
		
		} // Provided structure.
		
		//
		// Build with elements.
		//
		else
			$this->Full( (string) $theAddress );

	} // Constructor.

		

/*=======================================================================================
 *																						*
 *								PUBLIC MEMBER INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Place																			*
	 *==================================================================================*/

	/**
	 * Manage place.
	 *
	 * This method can be used to manage the address {@link kOFFSET_MAIL_PLACE place}, it
	 * accepts a parameter which represents either the place name or the requested
	 * operation, depending on its value:
	 *
	 * <ul>
	 *	<li><i>NULL</i>: Return the current value.
	 *	<li><i>FALSE</i>: Delete the current value.
	 *	<li><i>other</i>: Set the value with the provided parameter.
	 * </ul>
	 *
	 * The second parameter is a boolean which if <i>TRUE</i> will return the <i>old</i>
	 * value when replacing values; if <i>FALSE</i>, it will return the currently set value.
	 *
	 * @param string				$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses _ManageOffset()
	 *
	 * @see kOFFSET_MAIL_PLACE
	 */
	public function Place( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kOFFSET_MAIL_PLACE, $theValue, $getOld );		// ==>

	} // Place.

	 
	/*===================================================================================
	 *	Care																			*
	 *==================================================================================*/

	/**
	 * Manage care-of.
	 *
	 * This method can be used to manage the address {@link kOFFSET_MAIL_CARE care-of}, it
	 * accepts a parameter which represents a care-of reference or the requested operation,
	 * depending on its value:
	 *
	 * <ul>
	 *	<li><i>NULL</i>: Return the current value.
	 *	<li><i>FALSE</i>: Delete the current value.
	 *	<li><i>other</i>: Set the value with the provided parameter.
	 * </ul>
	 *
	 * The second parameter is a boolean which if <i>TRUE</i> will return the <i>old</i>
	 * value when replacing values; if <i>FALSE</i>, it will return the currently set value.
	 *
	 * @param string				$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses _ManageOffset()
	 *
	 * @see kOFFSET_MAIL_CARE
	 */
	public function Care( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kOFFSET_MAIL_CARE, $theValue, $getOld );		// ==>

	} // Care.

	 
	/*===================================================================================
	 *	Street																			*
	 *==================================================================================*/

	/**
	 * Manage street.
	 *
	 * This method can be used to manage the address {@link kOFFSET_MAIL_STREET street}, it
	 * accepts a parameter which represents a street name, P.O. box or the requested
	 * operation, depending on its value:
	 *
	 * <ul>
	 *	<li><i>NULL</i>: Return the current value.
	 *	<li><i>FALSE</i>: Delete the current value.
	 *	<li><i>other</i>: Set the value with the provided parameter.
	 * </ul>
	 *
	 * The second parameter is a boolean which if <i>TRUE</i> will return the <i>old</i>
	 * value when replacing values; if <i>FALSE</i>, it will return the currently set value.
	 *
	 * @param string				$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses _ManageOffset()
	 *
	 * @see kOFFSET_MAIL_STREET
	 */
	public function Street( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kOFFSET_MAIL_STREET, $theValue, $getOld );		// ==>

	} // Street.

	 
	/*===================================================================================
	 *	Zip																				*
	 *==================================================================================*/

	/**
	 * Manage zip code.
	 *
	 * This method can be used to manage the address {@link kOFFSET_MAIL_ZIP zip} code, it
	 * accepts a parameter which represents the address zip code or the requested operation,
	 * depending on its value:
	 *
	 * <ul>
	 *	<li><i>NULL</i>: Return the current value.
	 *	<li><i>FALSE</i>: Delete the current value.
	 *	<li><i>other</i>: Set the value with the provided parameter.
	 * </ul>
	 *
	 * The second parameter is a boolean which if <i>TRUE</i> will return the <i>old</i>
	 * value when replacing values; if <i>FALSE</i>, it will return the currently set value.
	 *
	 * @param string				$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses _ManageOffset()
	 *
	 * @see kOFFSET_MAIL_ZIP
	 */
	public function Zip( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kOFFSET_MAIL_ZIP, $theValue, $getOld );		// ==>

	} // Zip.

	 
	/*===================================================================================
	 *	City																			*
	 *==================================================================================*/

	/**
	 * Manage city.
	 *
	 * This method can be used to manage the address {@link kOFFSET_MAIL_CITY city}, it
	 * accepts a parameter which represents the address city name or the requested
	 * operation, depending on its value:
	 *
	 * <ul>
	 *	<li><i>NULL</i>: Return the current value.
	 *	<li><i>FALSE</i>: Delete the current value.
	 *	<li><i>other</i>: Set the value with the provided parameter.
	 * </ul>
	 *
	 * The second parameter is a boolean which if <i>TRUE</i> will return the <i>old</i>
	 * value when replacing values; if <i>FALSE</i>, it will return the currently set value.
	 *
	 * @param string				$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses _ManageOffset()
	 *
	 * @see kOFFSET_MAIL_CITY
	 */
	public function City( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kOFFSET_MAIL_CITY, $theValue, $getOld );		// ==>

	} // City.

	 
	/*===================================================================================
	 *	Province																		*
	 *==================================================================================*/

	/**
	 * Manage province.
	 *
	 * This method can be used to manage the address {@link kOFFSET_MAIL_PROVINCE province},
	 * it accepts a parameter which represents the address province name, code or the
	 * requested operation, depending on its value:
	 *
	 * <ul>
	 *	<li><i>NULL</i>: Return the current value.
	 *	<li><i>FALSE</i>: Delete the current value.
	 *	<li><i>other</i>: Set the value with the provided parameter.
	 * </ul>
	 *
	 * The second parameter is a boolean which if <i>TRUE</i> will return the <i>old</i>
	 * value when replacing values; if <i>FALSE</i>, it will return the currently set value.
	 *
	 * @param string				$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses _ManageOffset()
	 *
	 * @see kOFFSET_MAIL_PROVINCE
	 */
	public function Province( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kOFFSET_MAIL_PROVINCE, $theValue, $getOld );	// ==>

	} // Province.

	 
	/*===================================================================================
	 *	Country																			*
	 *==================================================================================*/

	/**
	 * Manage province.
	 *
	 * This method can be used to manage the address {@link kOFFSET_MAIL_COUNTRY country},
	 * it accepts a parameter which represents the address country name, code or the
	 * requested operation, depending on its value:
	 *
	 * <ul>
	 *	<li><i>NULL</i>: Return the current value.
	 *	<li><i>FALSE</i>: Delete the current value.
	 *	<li><i>other</i>: Set the value with the provided parameter.
	 * </ul>
	 *
	 * The second parameter is a boolean which if <i>TRUE</i> will return the <i>old</i>
	 * value when replacing values; if <i>FALSE</i>, it will return the currently set value.
	 *
	 * @param string				$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses _ManageOffset()
	 *
	 * @see kOFFSET_MAIL_COUNTRY
	 */
	public function Country( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kOFFSET_MAIL_COUNTRY, $theValue, $getOld );	// ==>

	} // Country.

	 
	/*===================================================================================
	 *	Full																			*
	 *==================================================================================*/

	/**
	 * Manage full address.
	 *
	 * This method can be used to manage the full address as a
	 * {@link kOFFSET_MAIL_FULL string}, it accepts a parameter which represents the full
	 * address or the requested operation, depending on its value:
	 *
	 * <ul>
	 *	<li><i>NULL</i>: Return the current value.
	 *	<li><i>FALSE</i>: Delete the current value.
	 *	<li><i>other</i>: Set the value with the provided parameter.
	 * </ul>
	 *
	 * The second parameter is a boolean which if <i>TRUE</i> will return the <i>old</i>
	 * value when replacing values; if <i>FALSE</i>, it will return the currently set value.
	 *
	 * When retrieving values, if the full address was not set, this method will return in
	 * any case the string representation of the full address.
	 *
	 * @param string				$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses _ManageOffset()
	 *
	 * @see kOFFSET_MAIL_FULL
	 */
	public function Full( $theValue = NULL, $getOld = FALSE )
	{
		//
		// Return full address.
		//
		if( ($theValue === NULL)
		 && (! $this->offsetExists( kOFFSET_MAIL_FULL )) )
		{
			//
			// Build address.
			//
			$address = Array();
			if( ($tmp = $this->Place()) !== NULL )
				$address[] = $tmp;
			if( ($tmp = $this->Care()) !== NULL )
				$address[] = $tmp;
			if( ($tmp = $this->Street()) !== NULL )
				$address[] = $tmp;
			$city = Array();
			if( ($tmp = $this->Zip()) !== NULL )
				$city[] = $tmp;
			if( ($tmp = $this->City()) !== NULL )
				$city[] = $tmp;
			if( ($tmp = $this->Province()) !== NULL )
				$city[] = $tmp;
			if( count( $city ) )
				$address[] = implode( ' ', $city );
			if( ($tmp = $this->Country()) !== NULL )
				$address[] = $tmp;
			
			return implode( "\n", $address );										// ==>
		
		} // Retrieve and value missing.
		
		return $this->_ManageOffset( kOFFSET_MAIL_FULL, $theValue, $getOld );		// ==>

	} // Full.

	 

} // class CMailAddress.


?>
