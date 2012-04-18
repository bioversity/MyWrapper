<?php

/**
 * <i>CDataTypeInt64</i> class definition.
 *
 * This file contains the class definition of <b>CDataTypeInt64</b> which wraps this class
 * around a signed 64 bit integer.
 *
 *	@package	MyWrapper
 *	@subpackage	Framework
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 21/03/2012
 */

/*=======================================================================================
 *																						*
 *									CDataTypeInt64.php									*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CDataType.php" );

/**
 * 64 bit signed integer.
 *
 * This class represents a 64 bit signed integer, the object is structured as follows:
 *
 * <ul>
 *	<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>: The constant
 *		{@link kTYPE_INT64 kTYPE_INT64}.
 *	<li><i>{@link kTAG_DATA kTAG_DATA}</i>: A string representing the integer.
 * </ul>
 *
 * This class is necessary, because to date PHP doesn't handle 64 bit integers.
 *
 *	@package	MyWrapper
 *	@subpackage	Framework
 */
class CDataTypeInt64 extends CDataType
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
	 * In this class we store {@link kTYPE_INT64 kTYPE_INT64} in the
	 * {@link kTAG_TYPE kTAG_TYPE} offset and the integer in the
	 * {@link kTAG_DATA kTAG_DATA} offset.
	 *
	 * The method will check if the provided data (converted to string) is numeric, if this
	 * is not the case, it will raise an exception. This is to handle miscellaneous objects.
	 *
	 * The number will be stored in the {@link kTAG_DATA kTAG_DATA} offset as an integer, if
	 * the current system is 64 bits.
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
		if( is_numeric( $data = (string) $theData ) )
		{
			$this->offsetSet( kTAG_TYPE, kTYPE_INT64 );
			$this->offsetSet( kTAG_DATA, ( PHP_INT_SIZE < 8 ) ? $data
															  : (integer) $data );
		
		} // Valid data.
		
		else
			throw new CException( "Invalid data",
								  kERROR_INVALID_PARAMETER,
								  kMESSAGE_TYPE_ERROR,
								  array( 'Data' => $theData ) );				// !@! ==>
	
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
	 * In this class we return the integer value, if PHP handles 8 byte integers, or we call
	 * the {@link CDataType::value() parent} method.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @throws Exception
	 */
	public function value()
	{
		//
		// Handle 64 bit systems.
		//
		if( PHP_INT_SIZE >= 8 )
			return (integer) $this->offsetGet( kTAG_DATA );							// ==>
		
		return parent::value();														// ==>
	
	} // value.

	 

} // class CDataTypeInt64.


?>
