<?php

/**
 * <i>CDataTypeBinary</i> class definition.
 *
 * This file contains the class definition of <b>CDataTypeBinary</b> which wraps this class
 * around a binary string.
 *
 *	@package	MyWrapper
 *	@subpackage	Framework
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 21/03/2012
 */

/*=======================================================================================
 *																						*
 *									CDataTypeBinary.php									*
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
 * This class represents a binary string, the object records the following information in
 * its offsets:
 *
 * <ul>
 *	<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>: The constant
 *		{@link kDATA_TYPE_BINARY kDATA_TYPE_BINARY}.
 *	<li><i>{@link kTAG_DATA kTAG_DATA}</i>: The following structure:
 *	 <ul>
 *		<li><i>{@link kOBJ_TYPE_BINARY_BIN kOBJ_TYPE_BINARY_BIN}</i>: The binary string in
 *			hexadecimal.
 *		<li><i>{@link kOBJ_TYPE_BINARY_TYPE kOBJ_TYPE_BINARY_TYPE}</i>: The binary string
 *			type (integer):
 *		 <ul>
 *			<li><i>1</i>: Function.
 *			<li><i>2</i>: Byte array (use as default).
 *			<li><i>3</i>: UUID.
 *			<li><i>5</i>: MD5.
 *			<li><i>128</i>: Custom.
 *		 </ul>
 *	 </ul>
 * </ul>
 *
 *	@package	MyWrapper
 *	@subpackage	Framework
 */
class CDataTypeBinary extends CDataType
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
	 * {@link kDATA_TYPE_BINARY offset} and to set the binary string into the
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
		// Call parent constructor.
		//
		parent::__construct( $theData );
		
		//
		// Load object.
		//
		$this->offsetSet( kTAG_TYPE, kDATA_TYPE_BINARY );
		$this->offsetSet( kTAG_DATA, bin2hex( (string) $theData ) );
	
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
	 * This method will return the actual binary string.
	 *
	 * @access public
	 * @return float
	 *
	 * @throws Exception
	 */
	public function value()
	{
		return ( function_exists( 'hex2bin' ) )
			 ? hex2bin( $this->offsetGet( kTAG_DATA ) )								// ==>
			 : pack( 'H*', $this->offsetGet( kTAG_DATA ) );							// ==>
	
	} // value.

	 

} // class CDataTypeBinary.


?>
