<?php

/**
 * <i>CMongoObject</i> class definition.
 *
 * This file contains the class definition of <b>CMongoObject</b> which overloads its
 * {@link CPersistentObject ancestor} to implement an object that resides in a MongoDB
 * collection.
 *
 *	@package	Framework
 *	@subpackage	Persistence
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 19/02/2012
 */

/*=======================================================================================
 *																						*
 *									CMongoObject.php									*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CPersistentObject.php" );

/**
 * Types.
 *
 * This include file contains all data type definitions.
 */
require_once( kPATH_LIBRARY_DEFINES."Types.inc.php" );

/**
 * Offsets.
 *
 * This include file contains the default offset definitions.
 */
require_once( kPATH_LIBRARY_DEFINES."Offsets.inc.php" );

/**
 *	MongoDB objects ancestor.
 *
 * This class is the ancestor of MongoDB database resident objects in this library, it
 * implements support for {@link __construct() instantiation} and {@link Commit() storage}
 * of objects into MongoDB collections.
 *
 * @package		Framework
 * @subpackage	Persistence
 */
class CMongoObject extends CPersistentObject
{
		

/*=======================================================================================
 *																						*
 *								STATIC REFERENCE INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	SerialiseObject																	*
	 *==================================================================================*/

	/**
	 * Serialise object.
	 *
	 * This method can be used to serialise an object in a format suitable for exchange and
	 * perform the reverse action.
	 *
	 * When serialising, the first thing the method will do is convert all embedded array
	 * objects into plain arrays, then it will traverse the object's structure and perform
	 * the following actions:
	 *
	 * <ul>
	 *	<li><i>If it encounters an object</i>: It will check if the type corresponds to a
	 *		Mongo type object and convert as follows:
	 *	 <ul>
	 *		<li><i>MongoId</i>: An array structured as follows:
	 *		 <ul>
	 *			<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>: The
	 *				{@link kDATA_TYPE_MongoId kDATA_TYPE_MongoId} constant.
	 *			<li><i>{@link kTAG_DATA kTAG_DATA}</i>: The hex string representation of
	 *				the MongoId.
	 *		 </ul>
	 *		<li><i>MongoCode</i>: An array structured as follows:
	 *		 <ul>
	 *			<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>: The
	 *				{@link kDATA_TYPE_MongoCode kDATA_TYPE_MongoCode} constant.
	 *			<li><i>{@link kTAG_DATA kTAG_DATA}</i>: An array structured as follows:
	 *			 <ul>
	 *				<li><i>{@link kOBJ_TYPE_CODE_SRC kOBJ_TYPE_CODE_SRC}</i>: The javascript
	 *					code.
	 *				<li><i>{@link kOBJ_TYPE_CODE_SCOPE kOBJ_TYPE_CODE_SCOPE}</i>: The
	 *					key/value pairs.
	 *			 </ul>
	 *		 </ul>
	 *		<li><i>MongoDate</i>: An array structured as follows:
	 *		 <ul>
	 *			<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>: The
	 *				{@link kDATA_TYPE_STAMP kDATA_TYPE_STAMP} constant.
	 *			<li><i>{@link kTAG_DATA kTAG_DATA}</i>: An array structured as follows:
	 *			 <ul>
	 *				<li><i>{@link kOBJ_TYPE_STAMP_SEC kOBJ_TYPE_STAMP_SEC}</i>: Number of
	 *					seconds since January 1st, 1970.
	 *				<li><i>{@link kOBJ_TYPE_STAMP_USEC kOBJ_TYPE_STAMP_USEC}</i>:
	 *					Microseconds.
	 *			 </ul>
	 *		 </ul>
	 *		<li><i>MongoRegex</i>: An array structured as follows:
	 *		 <ul>
	 *			<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>: The
	 *				{@link kDATA_TYPE_MongoRegex kDATA_TYPE_MongoRegex} constant.
	 *			<li><i>{@link kTAG_DATA kTAG_DATA}</i>: The regular expression string.
	 *		 </ul>
	 *		<li><i>MongoBinData</i>: An array structured as follows:
	 *		 <ul>
	 *			<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>: The
	 *				{@link kDATA_TYPE_BINARY kDATA_TYPE_BINARY} constant.
	 *			<li><i>{@link kTAG_DATA kTAG_DATA}</i>: The HEX representation of the
	 *				binary string.
	 *		 </ul>
	 *	 </ul>
	 * </ul>
	 *
	 * When unserialising, the method will traverse the object's structure and perform
	 * the following actions:
	 *
	 * <ul>
	 *	<li><i>If it encounters an array</i>: It will check if the array contains the
	 *		{@link kTAG_TYPE kTAG_TYPE} tag and convert the enclosing array to a scalar as
	 *		follows:
	 *	 <ul>
	 *		<li><i>{@link kDATA_TYPE_MongoId kDATA_TYPE_MongoId}</i>: A MongoId object.
	 *		<li><i>{@link kDATA_TYPE_MongoCode kDATA_TYPE_MongoCode}</i>: A MongoCode
	 *			object.
	 *		<li><i>{@link kDATA_TYPE_MongoRegex kDATA_TYPE_MongoRegex}</i>: A MongoRegex
	 *			object.
	 *		<li><i>{@link kDATA_TYPE_MongoDate kDATA_TYPE_MongoDate} or
	 *			{@link kDATA_TYPE_STAMP kDATA_TYPE_STAMP}</i>: A MongoDate object
	 *		<li><i>{@link kDATA_TYPE_DATE kDATA_TYPE_DATE} or
	 *			{@link kDATA_TYPE_TIME kDATA_TYPE_TIME}</i>: It will perform the
	 *			strtotime( date string ) and set it to a MongoDate object.
	 *		<li><i>{@link kDATA_TYPE_BINARY kDATA_TYPE_BINARY} or
	 *			{@link MONGO:MongoBinData MONGO:MongoBinData}</i>: A MongoBinData object.
	 *		<li><i>{@link kDATA_TYPE_INT32 kDATA_TYPE_INT32} or
	 *			{@link MONGO:MongoInt32 MONGO:MongoInt32}</i>: A MongoInt32 object.
	 *		<li><i>{@link kDATA_TYPE_INT64 kDATA_TYPE_INT64} or
	 *			{@link MONGO:MongoInt64 MONGO:MongoInt64}</i>: A MongoInt64 object.
	 *	 </ul>
	 * </ul>
	 *
	 * The method parameters are:
	 *
	 * <ul>
	 *	<li><b>&$theObject</b>: Reference to object or array.
	 *	<li><b>$doConvert</b>: This parameter determines what kind of conversion to perform:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Serialise, native types will be serialised.
	 *		<li><i>FALSE</i>: Unserialise, serialised elements will be converted to native
	 *			types.
	 *		<li><i>NULL</i>: Don't serialise, only convert array objects to arrays.
	 *	 </ul>
	 *	<li><b>$theClass</b>: This parameter represents the class of the provided object.
	 *		The parameter is <i>private</i>, so you should not need to set it. It is used
	 *		to determine which {@link SerialiseNativeObject() SerialiseNativeObject}
	 *		method should be called.
	 * </ul>
	 *
	 * This method expects an object in entry and will return an array at exit.
	 *
	 * @param mixed				   &$theObject			Object or array.
	 * @param boolean				$doConvert			TRUE means serialise values.
	 * @param string				$theClass			Object class (PRIVATE).
	 *
	 * @static
	 * @return array
	 */
	static function SerialiseObject( &$theObject, $doConvert = NULL, $theClass = NULL )
	{
		//
		// Check provided object.
		//
		if( is_array( $theObject ) )
			$object = $theObject;
		elseif( $theObject instanceof ArrayObject )
			$object = $theObject->getArrayCopy();
		else
			throw new CException
					( "Invalid parameter: object should be an array or ArrayObject",
					  kERROR_INVALID_PARAMETER,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Object' => $theObject ) );						// !@! ==>
		
		//
		// Determine object class.
		//
		if( $theObject instanceof CMongoObject )
			$theClass = get_class( $theObject );
		elseif( is_array( $theObject )
			 && array_key_exists( kTAG_CLASS, $theObject ) )
			$theClass = $theObject[ kTAG_CLASS ];
		
		//
		// Traverse object.
		//
		$keys = array_keys( $object );
		foreach( $keys as $key )
		{
			//
			// Convert.
			//
			$converted = ( $theClass !== NULL )
					   ? $theClass::SerialiseNativeObject( $object[ $key ], $doConvert )
					   : self::SerialiseNativeObject( $object[ $key ], $doConvert );
			
			//
			// Recurse.
			//
			if( ( is_array( $converted )
			  || ($converted instanceof ArrayObject) ) )
			{
				if( $theClass !== NULL )
					$converted
						= $theClass::SerialiseObject( $converted, $doConvert, $theClass );
				else
					$converted
						= self::SerialiseObject( $converted, $doConvert, $theClass );
			
			} // Recursed.
			
			//
			// Copy element.
			//
			$object[ $key ] = ( $converted instanceof ArrayObject )
							? $converted->getArrayCopy()
							: $converted;
		
		} // Traversing object.
		
		return $object;																// ==>
	
	} // SerialiseObject.

	 
	/*===================================================================================
	 *	SerialiseNativeObject															*
	 *==================================================================================*/

	/**
	 * Convert native objects.
	 *
	 * This method can be used to serialise native objects, or to unserialise a structure
	 * into a native object.
	 *
	 * When serialising:
	 *
	 * <ul>
	 *	<li><i>If it encounters an object</i>: It will check if the type corresponds to a
	 *		native object type and convert as follows:
	 *	 <ul>
	 *		<li><i>MongoId</i>: An array structured as follows:
	 *		 <ul>
	 *			<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>: The
	 *				{@link kDATA_TYPE_MongoId kDATA_TYPE_MongoId} constant.
	 *			<li><i>{@link kTAG_DATA kTAG_DATA}</i>: The hex string representation of
	 *				the MongoId.
	 *		 </ul>
	 *		<li><i>MongoCode</i>: An array structured as follows:
	 *		 <ul>
	 *			<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>: The
	 *				{@link kDATA_TYPE_MongoCode kDATA_TYPE_MongoCode} constant.
	 *			<li><i>{@link kTAG_DATA kTAG_DATA}</i>: An array structured as follows:
	 *			 <ul>
	 *				<li><i>{@link kOBJ_TYPE_CODE_SRC kOBJ_TYPE_CODE_SRC}</i>: The javascript
	 *					code.
	 *				<li><i>{@link kOBJ_TYPE_CODE_SCOPE kOBJ_TYPE_CODE_SCOPE}</i>: The
	 *					key/value pairs.
	 *			 </ul>
	 *		 </ul>
	 *		<li><i>MongoDate</i>: An array structured as follows:
	 *		 <ul>
	 *			<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>: The
	 *				{@link kDATA_TYPE_STAMP kDATA_TYPE_STAMP} constant.
	 *			<li><i>{@link kTAG_DATA kTAG_DATA}</i>: An array structured as follows:
	 *			 <ul>
	 *				<li><i>{@link kOBJ_TYPE_STAMP_SEC kOBJ_TYPE_STAMP_SEC}</i>: Number of
	 *					seconds since January 1st, 1970.
	 *				<li><i>{@link kOBJ_TYPE_STAMP_USEC kOBJ_TYPE_STAMP_USEC}</i>:
	 *					Microseconds.
	 *			 </ul>
	 *		 </ul>
	 *		<li><i>MongoRegex</i>: An array structured as follows:
	 *		 <ul>
	 *			<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>: The
	 *				{@link kDATA_TYPE_MongoRegex kDATA_TYPE_MongoRegex} constant.
	 *			<li><i>{@link kTAG_DATA kTAG_DATA}</i>: The regular expression string.
	 *		 </ul>
	 *		<li><i>MongoBinData</i>: An array structured as follows:
	 *		 <ul>
	 *			<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>: The
	 *				{@link kDATA_TYPE_BINARY kDATA_TYPE_BINARY} constant.
	 *			<li><i>{@link kTAG_DATA kTAG_DATA}</i>: The HEX representation of the
	 *				binary string.
	 *		 </ul>
	 *	 </ul>
	 * </ul>
	 *
	 * When unserialising:
	 *
	 * <ul>
	 *	<li><i>If it encounters an array</i>: It will check if the array contains the
	 *		{@link kTAG_TYPE kTAG_TYPE} tag and convert the enclosing array to a scalar as
	 *		follows:
	 *	 <ul>
	 *		<li><i>{@link kDATA_TYPE_INT32 kDATA_TYPE_INT32} or
	 *			{@link MONGO:MongoInt32 MONGO:MongoInt32}</i>: A MongoInt32 object.
	 *		<li><i>{@link kDATA_TYPE_INT64 kDATA_TYPE_INT64} or
	 *			{@link MONGO:MongoInt64 MONGO:MongoInt64}</i>: A MongoInt64 object.
	 *		<li><i>{@link kDATA_TYPE_MongoCode kDATA_TYPE_MongoCode}</i>: A MongoCode
	 *			object.
	 *		<li><i>{@link kDATA_TYPE_MongoRegex kDATA_TYPE_MongoRegex}</i>: A MongoRegex
	 *			object.
	 *		<li><i>{@link kDATA_TYPE_MongoDate kDATA_TYPE_MongoDate} or
	 *			{@link kDATA_TYPE_STAMP kDATA_TYPE_STAMP}</i>: A MongoDate object
	 *		<li><i>{@link kDATA_TYPE_DATE kDATA_TYPE_DATE} or
	 *			{@link kDATA_TYPE_TIME kDATA_TYPE_TIME}</i>: It will perform the
	 *			strtotime( date string ) and set it to a MongoDate object.
	 *		<li><i>{@link kDATA_TYPE_BINARY kDATA_TYPE_BINARY} or
	 *			{@link MONGO:MongoBinData MONGO:MongoBinData}</i>: A MongoBinData object.
	 *	 </ul>
	 * </ul>
	 *
	 * The method parameters are:
	 *
	 * <ul>
	 *	<li><b>&$theElement</b>: Reference to the element to encode or decode.
	 *	<li><b>$doConvert</b>: This parameter determines the type of conversion, see the
	 *		{@link SerialiseObject() SerialiseObject} documentation:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Serialise, native types will be serialised.
	 *		<li><i>FALSE</i>: Unserialise, serialised elements will be converted to native
	 *			types.
	 *		<li><i>NULL</i>: Don't serialise, only convert array objects to arrays.
	 *		<li><i>NULL</i>: Don't make any conversions.
	 *	 </ul>
	 * </ul>
	 *
	 * The method will return the converted object.
	 *
	 * @param mixed					$theElement			Object to convert.
	 * @param boolean				$doConvert			TRUE means serialise values.
	 *
	 * @static
	 * @return mixed
	 */
	static function SerialiseNativeObject( $theElement, $doConvert = NULL )
	{
		//
		// Serialise.
		//
		if( $doConvert )
		{
			//
			// Only handle objects.
			//
			if( is_object( $theElement ) )
			{
				//
				// Handle native types.
				//
				switch( get_class( $theElement ) )
				{
					case 'MongoId':
						return array( kTAG_TYPE => kDATA_TYPE_MongoId,
									  kTAG_DATA => (string) $theElement );			// ==>
				
					case 'MongoCode':
						return array( kTAG_TYPE => kDATA_TYPE_MongoCode,
									  kTAG_DATA
										=> array( kOBJ_TYPE_CODE_SRC
													=> $theElement->code,
												  kOBJ_TYPE_CODE_SCOPE
													=> $theElement->scope ) );		// ==>
				
					case 'MongoDate':
						return array( kTAG_TYPE => kDATA_TYPE_STAMP,
									  kTAG_DATA
										=> array( kOBJ_TYPE_STAMP_SEC
													=> $theElement->sec,
												  kOBJ_TYPE_STAMP_USEC
													=> $theElement->usec ) );		// ==>
				
					case 'MongoRegex':
						return array( kTAG_TYPE => kDATA_TYPE_MongoRegex,
									  kTAG_DATA => (string) $theElement );			// ==>
				
					case 'MongoBinData':
						return array( kTAG_TYPE => kDATA_TYPE_BINARY,
									  kTAG_DATA => bin2hex( $theElement->bin ) );	// ==>
				
					case 'MongoInt32':
						return array( kTAG_TYPE => kDATA_TYPE_INT32,
									  kTAG_DATA => (string) $theElement );			// ==>
				
					case 'MongoInt64':
						return array( kTAG_TYPE => kDATA_TYPE_INT64,
									  kTAG_DATA => (string) $theElement );			// ==>
				
				} // Parsing object values.
				
			} // Element is object.
		
		} // Serialise.
		
		//
		// Unserialise.
		//
		elseif( $doConvert === FALSE )
		{
			//
			// Handle only arrays with type tag.
			//
			if( ( is_array( $theElement )
			   && array_key_exists( kTAG_TYPE, $theElement ) )
			 || ( ($theElement instanceof ArrayObject)
			   && $theElement->offsetExists( kTAG_TYPE ) ) )
			{
				//
				// Parse by type.
				//
				switch( $theElement[ kTAG_TYPE ] )
				{
					case kDATA_TYPE_MongoId:
						return new MongoId( $theElement[ kTAG_DATA ] );			// ==>
				
					case kDATA_TYPE_MongoCode:
						$tmp1 = $theElement[ kTAG_DATA ][ kOBJ_TYPE_CODE_SRC ];
						$tmp2 = ( array_key_exists( kOBJ_TYPE_CODE_SCOPE,
													$theElement[ kTAG_DATA ] ) )
							  ? $theElement[ kTAG_DATA ][ kOBJ_TYPE_CODE_SCOPE ]
							  : Array();
						return new MongoCode( $tmp1, $tmp2 );						// ==>

					case kDATA_TYPE_MongoRegex:
						return new MongoRegex( $theElement[ kTAG_DATA ] );			// ==>
				
					case kDATA_TYPE_STAMP:
					case kDATA_TYPE_MongoDate:
						$tmp1 = $theElement[ kTAG_DATA ]
										   [ kTAG_DATA ]
										   [ kOBJ_TYPE_STAMP_SEC ];
						$tmp2 = ( array_key_exists( kOBJ_TYPE_STAMP_USEC,
													$theElement[ kTAG_DATA ]
															   [ kTAG_DATA ] ) )
							  ? $theElement[ kTAG_DATA ]
							  			   [ kTAG_DATA ]
							  			   [ kOBJ_TYPE_STAMP_USEC ]
							  : 0;
						return new MongoDate( $tmp1, $tmp2 );						// ==>
				
					case kDATA_TYPE_DATE:
					case kDATA_TYPE_TIME:
						return new MongoDate
								( strtotime( $theElement[ kTAG_DATA ] ) );			// ==>
				
					case kDATA_TYPE_BINARY:
					case kDATA_TYPE_MongoBinData:
						return new MongoBinData
								( pack( 'H*', $theElement[ kTAG_DATA ] ) );		// ==>
				
					case kDATA_TYPE_INT32:
					case kDATA_TYPE_MongoInt32:
						return new MongoInt32( $theElement[ kTAG_DATA ] );			// ==>

					case kDATA_TYPE_INT64:
					case kDATA_TYPE_MongoInt64:
						return new MongoInt64( $theElement[ kTAG_DATA ] );			// ==>
				
				} // Parsed Mongo type.
			
			} // Typed element.
		
		} // Unserialise.
		
		return $theElement;															// ==>
	
	} // SerialiseNativeObject.
	
	 

/*=======================================================================================
 *																						*
 *								PROTECTED PERSISTENCE INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_StoreObject																	*
	 *==================================================================================*/

	/**
	 * Store object in container.
	 *
	 * We overload this method to store objects into MongoDB databases, the method expects
	 * the collection to be a MongoCollection and will ignore the identifier parameter while
	 * saving, but will return the object identifier in it; this means that the object's
	 * identifier {@link kTAG_ID_NATIVE offset} must have been managed beforehand.
	 *
	 * This method does not make a distinction for inserting or updating: new records will
	 * be inserted and existing will be updated.
	 *
	 * The collection parameter is expected to be a MongoCollection.
	 *
	 * @throws Exception
	 *
	 * @param reference			   &$theContainer		Object container.
	 * @param reference			   &$theIdentifier		Object identifier.
	 *
	 * @access protected
	 */
	protected function _StoreObject( &$theContainer, &$theIdentifier )
	{
		//
		// Save object.
		//
		$status = $theContainer->save( $this, array( 'safe' => TRUE ) );
		
		//
		// Get identifier.
		//
		$theIdentifier = $this->offsetGet( kTAG_ID_NATIVE );
	
	} // _StoreObject.

	 
	/*===================================================================================
	 *	_FindObject																		*
	 *==================================================================================*/

	/**
	 * Find object.
	 *
	 * We overload this method to load the object from a MongoCollection.
	 *
	 * In this class we assume that the identifier is the object <i>_id</i> and that the
	 * collection is a <i>MongoCollection</i>.
	 *
	 * <i>The method expects the identifier to be of the compatible type, that is, if you
	 * provide a <i>MongoId</i>, the identifier parameter must be already cast to that type;
	 * this applies to all other relevant data types</i>.
	 *
	 * @param reference			   &$theContainer		Object container.
	 * @param reference			   &$theIdentifier		Object identifier.
	 *
	 * @access protected
	 * @return mixed
	 */
	protected function _FindObject( &$theContainer, &$theIdentifier )
	{
		return $theContainer->findOne( array( kTAG_ID_NATIVE => $theIdentifier ) );		// ==>
	
	} // _FindObject.

		

/*=======================================================================================
 *																						*
 *								PROTECTED PERSISTENCE UTILITIES							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_PrepareFind																	*
	 *==================================================================================*/

	/**
	 * Normalise parameters of a find.
	 *
	 * The duty of this method is to ensure that the parameters provided to a
	 * {@link _FindObject() find} operation are ready.
	 *
	 * The method should first check if the provided container is of the correct type, then
	 * it should ensure that the identifier is valid.
	 *
	 * Any errors should raise an exception.
	 *
	 * In this class we only support <i>arrays</i> and <i>ArrayObject</i> containers and the
	 * identifier must not be empty.
	 *
	 * @param reference			   &$theContainer		Object container.
	 * @param reference			   &$theIdentifier		Object identifier.
	 *
	 * @access protected
	 *
	 * @throws CException
	 *
	 * @see kERROR_OPTION_MISSING kERROR_UNSUPPORTED
	 */
	protected function _PrepareFind( &$theContainer, &$theIdentifier )
	{
		//
		// Check container.
		//
		if( $theContainer === NULL )
			throw new CException
					( "Missing object container",
					  kERROR_OPTION_MISSING,
					  kMESSAGE_TYPE_ERROR );									// !@! ==>
		elseif( ! $theContainer instanceof MongoCollection )
			throw new CException
					( "Unsupported container type",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Container' => $theContainer ) );					// !@! ==>
		
		//
		// Check identifier.
		//
		if( $theIdentifier === NULL )
			throw new CException
					( "Missing object identifier",
					  kERROR_OPTION_MISSING,
					  kMESSAGE_TYPE_ERROR );									// !@! ==>
	
	} // _PrepareFind.

	 
	/*===================================================================================
	 *	_PrepareStore																	*
	 *==================================================================================*/

	/**
	 * Normalise parameters of a store.
	 *
	 * The duty of this method is to ensure that the parameters provided to a
	 * {@link _StoreObject() store} operation are ready.
	 *
	 * The method should first check if the provided container is of the correct type, then
	 * it should ensure that the identifier is valid or determine the identifier from the
	 * object's contents.
	 *
	 * Any errors should raise an exception.
	 *
	 * In this class we only support <i>arrays</i> and <i>ArrayObject</i> containers, if
	 * the identifier is missing we assume we want to append the object in the container.
	 *
	 * @param reference			   &$theContainer		Object container.
	 * @param reference			   &$theIdentifier		Object identifier.
	 *
	 * @access protected
	 *
	 * @throws CException
	 *
	 * @see kERROR_OPTION_MISSING kERROR_UNSUPPORTED
	 */
	protected function _PrepareStore( &$theContainer, &$theIdentifier )
	{
		//
		// Check container.
		//
		if( $theContainer === NULL )
			throw new CException
					( "Missing object container",
					  kERROR_OPTION_MISSING,
					  kMESSAGE_TYPE_ERROR );									// !@! ==>
		elseif( ! $theContainer instanceof MongoCollection )
			throw new CException
					( "Unsupported container type",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Container' => $theContainer ) );					// !@! ==>
		
		//
		// Set identifier.
		//
		if( $theIdentifier !== NULL )
			$this->offsetSet( kTAG_ID_NATIVE, $theIdentifier );
	
	} // _PrepareStore.

	 

} // class CMongoObject.


?>
