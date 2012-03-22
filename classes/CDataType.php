<?php

/**
 * <i>CDataType</i> class definition.
 *
 * This file contains the class definition of <b>CDataType</b> which is the ancestor of
 * data type mapping classes.
 *
 *	@package	Framework
 *	@subpackage	Core
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 21/03/2012
 */

/*=======================================================================================
 *																						*
 *										CDataType.php									*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CArrayObject.php" );

/**
 * Data types.
 *
 * This include file contains all default data type definitions.
 */
require_once( kPATH_LIBRARY_DEFINES."Types.inc.php" );

/**
 * Data types ancestor.
 *
 * This abstract class is the ancestor of data type classes in this library, classes derived
 * from this one can be used to wrap special data types in a standard class so that it can
 * be converted to a custom data type by {@link CDataWrapper wrappers} and
 * {@link CContainer containers}.
 *
 * This may become necessary when {@link CPersistentObject::Commit() storing} objects in
 * persistent data {@link CDataWrapper stores}.
 *
 * Current derived classes are:
 *
 * <ul>
 *	<li><i>{@link kDATA_TYPE_INT32 kDATA_TYPE_INT32}</i>: 32 bit integer, this data type is
 *		generally available, but it might be useful to distinguish it from the
 *		{@link kDATA_TYPE_INT64 64} bit version.
 *	<li><i>{@link kDATA_TYPE_INT64 kDATA_TYPE_INT64}</i>: 64 bit integer, this data type is
 *		supported only on 64 bit systems.
 *	<li><i>{@link kDATA_TYPE_STAMP kDATA_TYPE_STAMP}</i>: Time stamp, we create this type to
 *		have a standard way of representing a time-stamp.
 *	<li><i>{@link kDATA_TYPE_BINARY kDATA_TYPE_BINARY}</i>: Binary string, binary strings
 *		are supported by PHPO, but they must be encoded for transport over the network or
 *		for storing in databases.
 * </ul>
 *
 * Other specialised data types are:
 *
 * <ul>
 *	<li><i>{@link kDATA_TYPE_MongoId kDATA_TYPE_MongoId}</i>: MongoDB _id.
 *	<li><i>{@link kDATA_TYPE_MongoCode kDATA_TYPE_MongoCode}</i>: MongoDB map/reduce
 *		javascript code.
 *	<li><i>{@link kDATA_TYPE_MongoRegex kDATA_TYPE_MongoRegex}</i>: MongoDB regular
 *		expression query.
 * </ul>
 *
 * The object derives from {@link CArrayObject CArrayObject} and holds the following default
 * offsets:
 *
 * <ul>
 *	<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>: Data type code, this element indicates the data
 *		type.
 *	<li><i>{@link kTAG_DATA kTAG_DATA}</i>: This element holds the serialised data.
 * </ul>
 *
 *	@package	Framework
 *	@subpackage	Core
 */
abstract class CDataType extends CArrayObject
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
	 * This class enforces a standard constructor that accepts one parameter which
	 * represents the custom data contents, these will be stored in the
	 * {@link kTAG_DATA kTAG_DATA} offset, this element must be filled.
	 *
	 * The method will ensure that this parameter is not <i>NULL</i> and not an array; it
	 * may be an ArrayObject, but it must then have the _toString() method.
	 *
	 * No <i>NULL</i> concrete instance is allowed, all instances derived from this class
	 * must have a value.
	 *
	 * The @link kTAG_TYPE kTAG_TYPE} offset will be set by derived classes.
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
		// Check data.
		//
		if( $theData === NULL )
			throw new CException( "Data is required",
								  kERROR_INVALID_PARAMETER,
								  kMESSAGE_TYPE_ERROR );						// !@! ==>
		
		//
		// Check data array.
		//
		if( is_array( $theData ) )
			throw new CException( "Invalid data",
								  kERROR_INVALID_PARAMETER,
								  kMESSAGE_TYPE_ERROR,
								  array( 'Data' => $theData ) );				// !@! ==>
	
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
	public function __toString()		{	return (string) $this->offsetGet( kTAG_DATA );	}

		

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
	 * This method should return the custom data value in the closest type possible in PHP.
	 *
	 * By default we return the string representation of the data, this is the last resort
	 * if the actual data type cannot be represented in PHP; derived classes that can be
	 * represented in PHP should overload this method.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @throws Exception
	 */
	public function value()									{	return $this->__toString();	}

		

/*=======================================================================================
 *																						*
 *								STATIC CONVERSION INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	SerialiseObject																	*
	 *==================================================================================*/

	/**
	 * Serialise provided object.
	 *
	 * This method will take an object and convert all data elements that are compatible
	 * with one of the concrete classes derived from this one into a derived concrete
	 * instance.
	 *
	 * This method is useful when encoding objects in a format suitable for being
	 * transmitted, or just after {@link CPersistentObject::Commit() committing} them to
	 * a persistent {@link CContainer container}.
	 *
	 * This method will ensure that the object contains data types compatible with PHP and
	 * this library; it will be the duty of persistent {@link CContainer containers} to
	 * convert these structures into custom data types compatible with their storage
	 * engines.
	 *
	 * The method will scan the provided data elements: array or ArrayObject elements will
	 * be recursed, scalar elements will be sent to another public
	 * {@link SerialiseElement() method} that will take care of performing the actual data
	 * conversion.
	 *
	 * If an element of the provided structure is converted, it will <i>always</i> be an
	 * object derived from this class, and all conversions will be performed on the object
	 * itself, which means that you should expect the provided object to be modified.
	 *
	 * @param reference			   &$theObject			Object to decode.
	 *
	 * @static
	 */
	static function SerialiseObject( &$theObject )
	{
		//
		// Intercept structures.
		//
		if( is_array( $theObject )
		 || ($theObject instanceof ArrayObject) )
		{
			//
			// Recurse.
			//
			foreach( $theObject as $key => $value )
				self::SerialiseObject( $theObject[ $key ] );
		
		} // Is a struct.
		
		//
		// Convert scalars.
		//
		else
			self::SerialiseElement( $theObject );
	
	} // SerialiseObject.

	 
	/*===================================================================================
	 *	SerialiseElement																*
	 *==================================================================================*/

	/**
	 * Serialise provided data element.
	 *
	 * This method can be used to convert custom data types to a standard format that can
	 * be serialised and transmitted through the network. This method is generally called
	 * by {@link SerialiseObject() SerialiseObject} which passes each scalar element as a
	 * reference to this method which should decide whether the provided element is to be
	 * converted or not.
	 *
	 * This method will check if the provided parameter corresponds to a custom data type
	 * that needs to be converted, if so, it will convert it to an instance derived from
	 * this class.
	 *
	 * The following data types will be converted:
	 *
	 * <ul>
	 *	<li><i>MongoId</i>: We convert into a {@link CDataTypeMongoId CDataTypeMongoId}
	 *		object.
	 *	<li><i>MongoCode</i>: We convert into a
	 *		{@link CDataTypeMongoCode CDataTypeMongoCode} object.
	 *	<li><i>MongoDate</i>: We convert into a {@link CDataTypeStamp CDataTypeStamp}
	 *		object.
	 *	<li><i>MongoRegex</i>: We convert into a
	 *		{@link CDataTypeMongoRegex CDataTypeMongoRegex} object.
	 *	<li><i>MongoBinData</i>: We convert into a {@link CDataTypeBinary CDataTypeBinary}
	 *		object.
	 *	<li><i>MongoInt32</i>: We convert into a {@link CDataTypeInt32 CDataTypeInt32}
	 *		object.
	 *	<li><i>MongoInt64</i>: We convert into a {@link CDataTypeInt64 CDataTypeInt64}
	 *		object.
	 * </ul>
	 *
	 * @param reference			   &$theElement			Element to encode.
	 *
	 * @static
	 */
	static function SerialiseElement( &$theElement )
	{
		//
		// Parse structures.
		//
		if( is_object( $theElement ) )
		{
			//
			// Parse by type.
			//
			switch( get_class( $theElement ) )
			{
				case 'MongoId':
					$theElement = new CDataTypeMongoId( $theElement );
					break;
			
				case 'MongoCode':
					$theElement = new CDataTypeMongoCode( $theElement );
					break;
			
				case 'MongoDate':
					$theElement = new CDataTypeStamp( $theElement );
					break;
			
				case 'MongoRegex':
					$theElement = new CDataTypeMongoRegex( $theElement );
					break;
			
				case 'MongoBinData':
					$theElement = new CDataTypeBinary( $theElement );
					break;
			
				case 'MongoInt32':
					$theElement = new CDataTypeInt32( $theElement );
					break;
			
				case 'MongoInt64':
					$theElement = new CDataTypeInt64( $theElement );
					break;
			
			} // Parsing by class.
		
		} // Provided object.
	
	} // SerialiseElement.

	 

} // class CDataType.


?>