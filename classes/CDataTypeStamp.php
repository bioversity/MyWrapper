<?php

/**
 * <i>CDataTypeStamp</i> class definition.
 *
 * This file contains the class definition of <b>CDataTypeStamp</b> which wraps this class
 * around a time-stamp.
 *
 *	@package	MyWrapper
 *	@subpackage	Framework
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 21/03/2012
 */

/*=======================================================================================
 *																						*
 *									CDataTypeStamp.php									*
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
 * This class represents a time-stamp which is equivalent to a unix timestamp, the object
 * records the following information in its offsets:
 *
 * <ul>
 *	<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>: The constant
 *		{@link kTYPE_STAMP kTYPE_STAMP}.
 *	<li><i>{@link kTAG_DATA kTAG_DATA}</i>: The following structure:
 *	 <ul>
 *		<li><i>{@link kTYPE_STAMP_SEC kTYPE_STAMP_SEC}</i>: The number of seconds
 *			since midnight January 1st 1970 GMT, an integer.
 *		<li><i>{@link kTYPE_STAMP_USEC kTYPE_STAMP_USEC}</i>: The milliseconds part
 *			of the value, or zero.
 *	 </ul>
 * </ul>
 *
 *	@package	MyWrapper
 *	@subpackage	Framework
 */
class CDataTypeStamp extends CDataType
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
	 * We overload the parent constructor to handle different types of data:
	 *
	 * <ul>
	 *	<li><i>float</i>: If the data is a float, we assume it is the result of
	 *		<i>microtime( TRUE )</i> in which the integral part of the number represents the
	 *		number of seconds since January 1st 1970 GMT, and the fractional part represents
	 *		the microseconds.
	 *	<li><i>integer</i>: If the data is an integer, we assume it represents the number of
	 *		seconds since January 1st 1970 GMT.
	 *	<li><i>MongoDate</i>: We use the object's elements.
	 *	<li><i>DateTime</i>: We get the date seconds count.
	 *	<li><i>other</i>: We assume the data is a date time string.
	 * </ul>
	 *
	 * No <i>NULL</i> concrete instance is allowed, all instances derived from this class
	 * must have a value.
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
		// Get current time.
		//
		if( $theData === NULL )
			$theData = new MongoDate();
		
		//
		// Call parent constructor.
		//
		parent::__construct( $theData );
		
		//
		// Load data type.
		//
		$this->offsetSet( kTAG_TYPE, kTYPE_STAMP );
		
		//
		// Handle float.
		//
		if( is_float( $theData ) )
		{
			$tmp = explode( '.', (string) $theData );
			if( count( $tmp ) > 1 )
			{
				$usec = sprintf( '%-06s', $tmp[ 1 ] );
				$this->offsetSet( kTAG_DATA,
								  array( kTYPE_STAMP_SEC => (integer) $tmp[ 0 ],
										 kTYPE_STAMP_USEC => (integer) $usec ) );
			}
			else
				$this->offsetSet( kTAG_DATA,
								  array( kTYPE_STAMP_SEC => (integer) $theData,
										 kTYPE_STAMP_USEC => 0 ) );
		}
		
		//
		// Handle integer.
		//
		elseif( is_integer( $theData ) )
			$this->offsetSet( kTAG_DATA, array( kTYPE_STAMP_SEC => (integer) $theData,
												kTYPE_STAMP_USEC => 0 ) );
		
		//
		// Handle MongoDate.
		//
		elseif( $theData instanceof MongoDate )
			$this->offsetSet( kTAG_DATA, array( kTYPE_STAMP_SEC => $theData->sec,
												kTYPE_STAMP_USEC => $theData->usec ) );
		
		//
		// Handle DateTime.
		//
		elseif( $theData instanceof DateTime )
			$this->offsetSet( kTAG_DATA,
							  array( kTYPE_STAMP_SEC => $theData->format( "U" ),
									 kTYPE_STAMP_USEC => 0 ) );
		
		//
		// Handle other types.
		//
		else
		{
			$sec = strtotime( (string) $theData );
			if( $sec !== FALSE )
				$this->offsetSet( kTAG_DATA, array( kTYPE_STAMP_SEC => $sec,
													kTYPE_STAMP_USEC => 0 ) );
			else
				throw new CException( "Invalid data",
									  kERROR_INVALID_PARAMETER,
									  kMESSAGE_TYPE_ERROR,
									  array( 'Data' => $theData ) );			// !@! ==>
		}
	
	} // Constructor.

	 
	/*===================================================================================
	 *	__toString																		*
	 *==================================================================================*/

	/**
	 * Return string representation.
	 *
	 * This method should return a string representation of the custom data type contents,
	 * this method must be implemented for all concrete classes.
	 *
	 * By default this method expects the custom data part to be convertable to string, if
	 * this is not the case, overload this method.
	 *
	 * @access public
	 * @return string
	 */
	public function __toString()
	{
		//
		// Get value.
		//
		$str = $this->offsetGet( kTAG_DATA );
		
		return date( 'Y-m-d H:i:s', $this->value() );								// ==>
	
	} // __toString.

		

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
	 * This method will return a floating point number equivalent to the result of the
	 * <i>microtime( TRUE )</i> function.
	 *
	 * @access public
	 * @return float
	 *
	 * @throws Exception
	 */
	public function value()
	{
		//
		// Get structure.
		//
		$str = $this->offsetGet( kTAG_DATA );

		//
		// Get seconds.
		//
		$sec = (string) $str[ kTYPE_STAMP_SEC ];
		
		//
		// Get milliseconds.
		//
		$sec .= ('.'.$str[ kTYPE_STAMP_USEC ]);
		
		return (float) $sec;														// ==>
	
	} // value.

	 

} // class CDataTypeStamp.


?>
