<?php

/**
 * <i>CDataTypeMongoId</i> class definition.
 *
 * This file contains the class definition of <b>CDataTypeMongoId</b> which wraps this class
 * around a MongoId object.
 *
 *	@package	MyWrapper
 *	@subpackage	Framework
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 21/03/2012
 */

/*=======================================================================================
 *																						*
 *									CDataTypeMongoId.php								*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CDataType.php" );

/**
 * Binary string.
 *
 * This class represents a MongoId object:
 *
 * <ul>
 *	<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>: The constant
 *		{@link kDATA_TYPE_MongoId kDATA_TYPE_MongoId}.
 *	<li><i>{@link kTAG_DATA kTAG_DATA}</i>: The HEX string.
 * </ul>
 *
 *	@package	MyWrapper
 *	@subpackage	Framework
 */
class CDataTypeMongoId extends CDataType
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
	 * We overload the parent constructor to set the default
	 * {@link kDATA_TYPE_MongoId offset} and to set the HEX string into the
	 * {@link kTAG_DATA kTAG_DATA} offset.
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
		// Handle default ID.
		//
		if( $theData === NULL )
			$theData = new MongoId();
		
		//
		// Call parent constructor.
		//
		parent::__construct( $theData );
		
		//
		// Load object.
		//
		$this->offsetSet( kTAG_TYPE, kDATA_TYPE_MongoId );
		$this->offsetSet( kTAG_DATA, (string) $theData );
	
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
	 * In this class we return the MongoId object.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @throws Exception
	 */
	public function value()		{	return new MongoId( $this->offsetGet( kTAG_DATA ) );	}

	 

} // class CDataTypeMongoId.


?>
