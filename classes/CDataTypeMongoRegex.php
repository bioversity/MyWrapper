<?php

/**
 * <i>CDataTypeMongoRegex</i> class definition.
 *
 * This file contains the class definition of <b>CDataTypeMongoRegex</b> which wraps this
 * class around a MongoRegex object.
 *
 *	@package	MyWrapper
 *	@subpackage	Core
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 21/03/2012
 */

/*=======================================================================================
 *																						*
 *								CDataTypeMongoRegex.php									*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CDataType.php" );

/**
 * Time-stamp.
 *
 * This class represents a MongoRegex object, it is structured as follows:
 *
 * <ul>
 *	<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>: The constant
 *		{@link kDATA_TYPE_MongoRegex kDATA_TYPE_MongoRegex}.
 *	<li><i>{@link kTAG_DATA kTAG_DATA}</i>: The regular expression.
 * </ul>
 *
 *	@package	MyWrapper
 *	@subpackage	Core
 */
class CDataTypeMongoRegex extends CDataType
{
		

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
	 * In this class we instantiate such an object from a MongoRegex object or from a
	 * string.
	 *
	 * @param mixed					$theData			Custom data.
	 *
	 * @access public
	 *
	 * @throws Exception
	 */
	public function __construct( $theData = NULL )
	{
		//
		// Call parent constructor.
		//
		parent::__construct( $theData );
		
		//
		// Load object.
		//
		$this->offsetSet( kTAG_TYPE, kDATA_TYPE_MongoRegex );
		$this->offsetSet( kTAG_DATA, (string) new MongoRegex( (string) $theData ) );
	
	} // Constructor.

		

/*=======================================================================================
 *																						*
 *									PUBLIC DATA INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	value																			*
	 *==================================================================================*/

	/**
	 * Return data value.
	 *
	 * In this class we return the MongoRegex object.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @throws Exception
	 */
	public function value()		{	return new MongoRegEx( $this->offsetGet( kTAG_DATA ) );	}

	 

} // class CDataTypeMongoRegex.


?>
