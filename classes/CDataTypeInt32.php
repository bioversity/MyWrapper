<?php

/**
 * <i>CDataTypeInt32</i> class definition.
 *
 * This file contains the class definition of <b>CDataTypeInt32</b> which wraps this class
 * around a signed 32 bit integer.
 *
 *	@package	MyWrapper
 *	@subpackage	Framework
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 21/03/2012
 */

/*=======================================================================================
 *																						*
 *									CDataTypeInt32.php									*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CDataType.php" );

/**
 * 32 bit signed integer.
 *
 * This class represents a 32 bit signed integer, the object is structured as follows:
 *
 * <ul>
 *	<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>: The constant
 *		{@link kTYPE_INT32 kTYPE_INT32}.
 *	<li><i>{@link kTAG_DATA kTAG_DATA}</i>: An integer representing the integer :-)
 * </ul>
 *
 *	@package	MyWrapper
 *	@subpackage	Framework
 */
class CDataTypeInt32 extends CDataType
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
	 * In this class we store {@link kTYPE_INT32 kTYPE_INT32} in the
	 * {@link kTAG_TYPE kTAG_TYPE} offset and the integer in the
	 * {@link kTAG_DATA kTAG_DATA} offset.
	 *
	 * The method will check if the provided data (converted to string) is numeric, if this
	 * is not the case, it will raise an exception. This is to handle miscellaneous objects.
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
			$this->offsetSet( kTAG_TYPE, kTYPE_INT32 );
			$this->offsetSet( kTAG_DATA, (integer) $data );
		
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
	 * In this class we return the integer value, since PHP handles it.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @throws Exception
	 */
	public function value()				{	return (integer) $this->offsetGet( kTAG_DATA );	}

	 

} // class CDataTypeInt32.


?>
