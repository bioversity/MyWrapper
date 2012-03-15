<?php

/**
 * <i>CMongoContainer</i> class definition.
 *
 * This file contains the class definition of <b>CMongoContainer</b> which implements a
 * MongoDB object store.
 *
 *	@package	Framework
 *	@subpackage	Persistence
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 08/03/2012
 */

/*=======================================================================================
 *																						*
 *									CMongoContainer.php									*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CContainer.php" );

/**
 * Offsets.
 *
 * This include file contains all default offset definitions.
 */
require_once( kPATH_LIBRARY_DEFINES."Offsets.inc.php" );

/**
 * Mongo persistent data store.
 *
 * This class extends its {@link CContainer ancestor} to implement an object store based on
 * MongoCollection containers.
 *
 * @package		Framework
 * @subpackage	Persistence
 */
class CMongoContainer extends CContainer
{
		

/*=======================================================================================
 *																						*
 *								PUBLIC MEMBER INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Container																		*
	 *==================================================================================*/

	/**
	 * Manage persistent container.
	 *
	 * We {@link CContainer::Container() overload} this method to ensure that the
	 * provided container is a MongoCollection object.
	 *
	 * @param mixed					$theValue			Persistent container or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 */
	public function Container( $theValue = NULL, $getOld = FALSE )
	{
		//
		// Check type.
		//
		if( ($theValue !== NULL)						// Not retrieve
		 && ($theValue !== FALSE)						// and not delete
		 && (! $theValue instanceof MongoCollection) )	// and not a MongoCollection:
			throw new CException
				( "Invalid container type",
				  kERROR_INVALID_PARAMETER,
				  kMESSAGE_TYPE_ERROR,
				  array( 'Container' => $theValue ) );							// !@! ==>
		
		return parent::Container( $theValue, $getOld );								// ==>

	} // Container.

		

/*=======================================================================================
 *																						*
 *								PUBLIC MANAGEMENT INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Commit																			*
	 *==================================================================================*/

	/**
	 * Commit provided object.
	 *
	 * We {@link CContainer::Commit() overload} this method to check whether the provided
	 * object is either an array or an ArrayObject.
	 *
	 * @param reference			   &$theObject			Object to commit.
	 * @param mixed					$theIdentifier		Object identifier.
	 * @param bitfield				$theModifiers		Commit modifiers.
	 *
	 * @access public
	 * @return mixed
	 */
	public function Commit( &$theObject,
							 $theIdentifier = NULL,
							 $theModifiers = kFLAG_PERSIST_REPLACE )
	{
		//
		// Check object.
		//
		if( is_array( $theObject )
		 || ($theObject instanceof ArrayObject) )
			return parent::Commit( $theObject, $theIdentifier, $theModifiers );		// ==>

		throw new CException
			( "Invalid object",
			  kERROR_INVALID_PARAMETER,
			  kMESSAGE_TYPE_ERROR,
			  array( 'Object' => $theObject ) );								// !@! ==>

	} // Commit.

		
	/*===================================================================================
	 *	Load																			*
	 *==================================================================================*/

	/**
	 * Load object.
	 *
	 * We {@link CContainer::Load() overload} this method to ensure that the identifier
	 * can be resolved:
	 *
	 * <ul>
	 *	<li><i>{@link CMongoDBRef CMongoDBRef}</i>: The method will let the provided object
	 *		{@link CMongoDBRef::Resolve() resolve} the issue.
	 *	<li><i>array</i>: If it is an array, the method will assume it is a MongoDBRef, it
	 *		will search for the identifier {@link kTAG_ID_REFERENCE reference} or
	 *		{@link kTAG_ID_NATIVE value} and use it.
	 *	<li><i>ArrayObject</i>: The method will work as if it was an array.
	 * </ul>
	 *
	 * @param mixed					$theIdentifier		Object identifier.
	 * @param bitfield				$theModifiers		Load modifiers.
	 *
	 * @access public
	 * @return mixed
	 */
	public function Load( $theIdentifier, $theModifiers = kFLAG_DEFAULT )
	{
		//
		// Resolve reference.
		//
		if( $theIdentifier instanceof CMongoDBRef )
			return $theIdentifier->Resolve( $this->Container() );					// ==>
		
		//
		// Look for identifier.
		//
		elseif( is_array( $theIdentifier )
			 || ($theIdentifier instanceof ArrayObject) )
		{
			//
			// Use reference ID.
			//
			if( array_key_exists( kTAG_ID_REFERENCE, (array) $theIdentifier ) )
				$theIdentifier = $theIdentifier[ kTAG_ID_REFERENCE ];
			
			//
			// Use ID.
			//
			elseif( array_key_exists( kTAG_ID_NATIVE, (array) $theIdentifier ) )
				$theIdentifier = $theIdentifier[ kTAG_ID_NATIVE ];
		}
		
		return parent::Load( $theIdentifier, $theModifiers );						// ==>

	} // Load.

		
	/*===================================================================================
	 *	Delete																			*
	 *==================================================================================*/

	/**
	 * Delete object.
	 *
	 * We {@link CContainer::Delete() overload} this method to ensure that the identifier
	 * can be resolved:
	 *
	 * <ul>
	 *	<li><i>{@link CMongoDBRef CMongoDBRef}</i>: The method will let the provided object
	 *		{@link CMongoDBRef::Resolve() resolve} the issue.
	 *	<li><i>array</i>: If it is an array, the method will assume it is a MongoDBRef, it
	 *		will search for the identifier {@link kTAG_ID_REFERENCE reference} or
	 *		{@link kTAG_ID_NATIVE value} and use it.
	 *	<li><i>ArrayObject</i>: The method will work as if it was an array.
	 * </ul>
	 *
	 * @param mixed					$theIdentifier		Object identifier.
	 * @param bitfield				$theModifiers		Delete modifiers.
	 *
	 * @access public
	 * @return mixed
	 */
	public function Delete( $theIdentifier, $theModifiers = kFLAG_DEFAULT )
	{
		//
		// Resolve reference.
		//
		if( $theIdentifier instanceof CMongoDBRef )
			return $theIdentifier->Resolve( $this->Container() );					// ==>
		
		//
		// Look for identifier.
		//
		elseif( is_array( $theIdentifier )
			 || ($theIdentifier instanceof ArrayObject) )
		{
			//
			// Use reference ID.
			//
			if( array_key_exists( kTAG_ID_REFERENCE, (array) $theIdentifier ) )
				$theIdentifier = $theIdentifier[ kTAG_ID_REFERENCE ];
			
			//
			// Use ID.
			//
			elseif( array_key_exists( kTAG_ID_NATIVE, (array) $theIdentifier ) )
				$theIdentifier = $theIdentifier[ kTAG_ID_NATIVE ];
		}
		
		return parent::Delete( $theIdentifier, $theModifiers );						// ==>

	} // Delete.

		

/*=======================================================================================
 *																						*
 *								STATIC CONVERSION INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	SerialiseData																	*
	 *==================================================================================*/

	/**
	 * Serialise provided data element.
	 *
	 * In this class we {@link CContainer::SerialiseData() overload} this method to convert
	 * the following data types:
	 *
	 * <ul>
	 *	<li><i>MongoId</i>: We convert into:
	 *	 <ul>
	 *		<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>:
	 *			{@link kDATA_TYPE_MongoId kDATA_TYPE_MongoId}.
	 *		<li><i>{@link kTAG_DATA kTAG_DATA}</i>: The contents of the object cast to
	 *			string.
	 *	 </ul>
	 *	<li><i>MongoCode</i>: We convert into:
	 *	 <ul>
	 *		<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>:
	 *			{@link kDATA_TYPE_MongoCode kDATA_TYPE_MongoCode}.
	 *		<li><i>{@link kTAG_DATA kTAG_DATA}</i>: A structure formatted as follows:
	 *		 <ul>
	 *			<li><i>{@link kOBJ_TYPE_CODE_SRC kOBJ_TYPE_CODE_SRC}</i>: The <i>code</i>
	 *				element of the object.
	 *			<li><i>{@link OBJ_TYPE_CODE_SCOPE OBJ_TYPE_CODE_SCOPE}</i>: The <i>scope</i>
	 *				element of the object.
	 *		 </ul>
	 *	 </ul>
	 *	<li><i>MongoDate</i>: We convert into:
	 *	 <ul>
	 *		<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>:
	 *			{@link kDATA_TYPE_STAMP kDATA_TYPE_STAMP}.
	 *		<li><i>{@link kTAG_DATA kTAG_DATA}</i>: A structure formatted as follows:
	 *		 <ul>
	 *			<li><i>{@link kOBJ_TYPE_STAMP_SEC kOBJ_TYPE_STAMP_SEC}</i>: The <i>sec</i>
	 *				element of the object.
	 *			<li><i>{@link kOBJ_TYPE_STAMP_USEC kOBJ_TYPE_STAMP_USEC}</i>: The
	 *				<i>usec</i> element of the object.
	 *		 </ul>
	 *	 </ul>
	 *	<li><i>MongoRegex</i>: We convert into:
	 *	 <ul>
	 *		<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>:
	 *			{@link kDATA_TYPE_MongoRegex kDATA_TYPE_MongoRegex}.
	 *		<li><i>{@link kTAG_DATA kTAG_DATA}</i>: The contents of the object cast to
	 *			string.
	 *	 </ul>
	 *	<li><i>MongoBinData</i>: We convert into:
	 *	 <ul>
	 *		<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>:
	 *			{@link kDATA_TYPE_BINARY kDATA_TYPE_BINARY}.
	 *		<li><i>{@link kTAG_DATA kTAG_DATA}</i>: The <i>bin</i> element of the object.
	 *	 </ul>
	 *	<li><i>MongoInt32</i>: We convert into:
	 *	 <ul>
	 *		<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>:
	 *			{@link kDATA_TYPE_INT32 kDATA_TYPE_INT32}.
	 *		<li><i>{@link kTAG_DATA kTAG_DATA}</i>: The contents of the object cast to
	 *			string.
	 *	 </ul>
	 *	<li><i>MongoInt64</i>: We convert into:
	 *	 <ul>
	 *		<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>:
	 *			{@link kDATA_TYPE_INT64 kDATA_TYPE_INT64}.
	 *		<li><i>{@link kTAG_DATA kTAG_DATA}</i>: The contents of the object cast to
	 *			string.
	 *	 </ul>
	 * </ul>
	 *
	 * The conversion is performed on the provided element itself.
	 *
	 * @param reference			   &$theElement			Element to encode.
	 *
	 * @access public
	 */
	public function SerialiseData( &$theElement )
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
					$theElement = array
					(
						kTAG_TYPE => kDATA_TYPE_MongoId,
						kTAG_DATA => (string) $theElement
					);
					return;															// ==>
			
				case 'MongoCode':
					$theElement = array
					(
						kTAG_TYPE => kDATA_TYPE_MongoCode,
						kTAG_DATA => array
									(
										kOBJ_TYPE_CODE_SRC => $theElement->code,
										OBJ_TYPE_CODE_SCOPE => $theElement->scope
									)
					);
					return;															// ==>
			
				case 'MongoDate':
					$theElement = array
					(
						kTAG_TYPE => kDATA_TYPE_STAMP,
						kTAG_DATA => array
									(
										kOBJ_TYPE_STAMP_SEC => $theElement->sec,
										kOBJ_TYPE_STAMP_USEC => $theElement->usec
									)
					);
					return;															// ==>
			
				case 'MongoRegex':
					$theElement = array
					(
						kTAG_TYPE => kDATA_TYPE_MongoRegex,
						kTAG_DATA => (string) $theElement
					);
					return;															// ==>
			
				case 'MongoBinData':
					$theElement = array
					(
						kTAG_TYPE => kDATA_TYPE_BINARY,
						kTAG_DATA => bin2hex( $theElement->bin )
					);
					return;															// ==>
			
				case 'MongoInt32':
					$theElement = array
					(
						kTAG_TYPE => kDATA_TYPE_INT32,
						kTAG_DATA => (string) $theElement
					);
					return;															// ==>
			
				case 'MongoInt64':
					$theElement = array
					(
						kTAG_TYPE => kDATA_TYPE_INT64,
						kTAG_DATA => (string) $theElement
					);
					return;															// ==>
			
			} // Parsing by class.
		
		} // Provided object.
		
		//
		// Call parent method.
		//
		parent::SerialiseData( $theElement );
	
	} // SerialiseData.

	 
	/*===================================================================================
	 *	UnserialiseData																	*
	 *==================================================================================*/

	/**
	 * Unserialise provided data element.
	 *
	 * In this class we {@link CContainer::UnserialiseData() overload} this method to parse
	 * the following {@link kTAG_TYPE kTAG_TYPE} offsets:
	 *
	 * <ul>
	 *	<li><i>{@link kDATA_TYPE_MongoId kDATA_TYPE_MongoId}</i>: We return a MongoId object
	 *		using the value provided in the {@link kTAG_DATA kTAG_DATA} offset.
	 *	<li><i>{@link kDATA_TYPE_MongoCode kDATA_TYPE_MongoCode}</i>: We return a MongoCode
	 *		object by using the values provided in the {@link kTAG_DATA kTAG_DATA} offset
	 *		which is expected to be an array structured as follows:
	 *	 <ul>
	 *		<li><i>{@link kOBJ_TYPE_CODE_SRC kOBJ_TYPE_CODE_SRC}</i>: The javascript code.
	 *		<li><i>{@link kOBJ_TYPE_CODE_SCOPE kOBJ_TYPE_CODE_SCOPE}</i>: The key/value
	 *			pairs.
	 *	 </ul>
	 *	<li><i>{@link kDATA_TYPE_MongoDate kDATA_TYPE_MongoDate} or
	 *		{@link kDATA_TYPE_STAMP kDATA_TYPE_STAMP}</i>: We return a MongoDate object
	 *		using the contents of the data in the {@link kTAG_DATA kTAG_DATA} offset which
	 *		is expected to be an array structured as follows:
	 *	 <ul>
	 *		<li><i>{@link kOBJ_TYPE_STAMP_SEC kOBJ_TYPE_STAMP_SEC}</i>: Number of seconds
	 *			since January 1st, 1970.
	 *		<li><i>{@link kOBJ_TYPE_STAMP_USEC kOBJ_TYPE_STAMP_USEC}</i>: Microseconds.
	 *	 </ul>
	 *	<li><i>{@link kDATA_TYPE_MongoInt32 kDATA_TYPE_MongoInt32} or
	 *		{@link kDATA_TYPE_INT32 kDATA_TYPE_INT32}</i>: We return a MongoInt32 using as
	 *		value the contents of the {@link kTAG_DATA kTAG_DATA} offset, which may also be
	 *		a string.
	 *	<li><i>{@link kDATA_TYPE_MongoInt64 kDATA_TYPE_MongoInt64} or
	 *		{@link kDATA_TYPE_INT64 kDATA_TYPE_INT64}</i>: We return a MongoInt64 using as
	 *		value the contents of the {@link kTAG_DATA kTAG_DATA} offset, which may also be
	 *		a string.
	 *	<li><i>{@link kDATA_TYPE_MongoRegex kDATA_TYPE_MongoRegex}</i>: We return a
	 *		MongoRegex object using the {@link kTAG_DATA kTAG_DATA} offset as the regular
	 *		expression.
	 *	<li><i>{@link kDATA_TYPE_MongoBinData kDATA_TYPE_MongoBinData} or
	 *		{@link kDATA_TYPE_BINARY kDATA_TYPE_BINARY}</i>: We return a MongoBinData object
	 *		using the {@link kTAG_DATA kTAG_DATA} offset as the hexadecimal representation
	 *		of the binary string.
	 * </ul>
	 *
	 * @param reference			   &$theElement			Element to encode.
	 *
	 * @access public
	 */
	public function UnserialiseData( &$theElement )
	{
		//
		// Handle type.
		//
		switch( $theElement[ kTAG_TYPE ] )
		{
			//
			// MongoId.
			//
			case kDATA_TYPE_MongoId:
				$theElement = new MongoId( (string) $theElement[ kTAG_DATA ] );
				break;
			
			//
			// MongoCode.
			//
			case kDATA_TYPE_MongoCode:
				if( is_array( $theElement[ kTAG_DATA ] )
				 || ($theElement[ kTAG_DATA ] instanceof ArrayObject) )
				{
					$tmp1 = $theElement[ kTAG_DATA ][ kOBJ_TYPE_CODE_SRC ];
					$tmp2 = ( array_key_exists( kOBJ_TYPE_CODE_SCOPE,
												(array) $theElement[ kTAG_DATA ] ) )
						  ? $theElement[ kTAG_DATA ][ kOBJ_TYPE_CODE_SCOPE ]
						  : Array();
					$theElement = new MongoCode( $tmp1, $tmp2 );
				}
				break;
			
			//
			// MongoDate.
			//
			case kDATA_TYPE_STAMP:
			case kDATA_TYPE_MongoDate:
				if( is_array( $theElement[ kTAG_DATA ] )
				 || ($theElement[ kTAG_DATA ] instanceof ArrayObject) )
				{
					$tmp1 = $theElement[ kTAG_DATA ][ kOBJ_TYPE_STAMP_SEC ];
					$tmp2 = ( array_key_exists( kOBJ_TYPE_STAMP_USEC,
												(array) $theElement[ kTAG_DATA ] ) )
						  ? $theElement[ kTAG_DATA ][ kOBJ_TYPE_STAMP_USEC ]
						  : 0;
					$theElement = new MongoDate( $tmp1, $tmp2 );
				}
				break;
			
			//
			// MongoInt32.
			//
			case kDATA_TYPE_INT32:
			case kDATA_TYPE_MongoInt32:
				$theElement = new MongoInt32( $theElement[ kTAG_DATA ] );
				break;
			
			//
			// MongoInt64.
			//
			case kDATA_TYPE_INT64:
			case kDATA_TYPE_MongoInt64:
				$theElement = new MongoInt64( $theElement[ kTAG_DATA ] );
				break;

			//
			// MongoRegex.
			//
			case kDATA_TYPE_MongoRegex:
				$theElement = new MongoRegex( $theElement[ kTAG_DATA ] );
				break;

			//
			// MongoBinData.
			//
			case kDATA_TYPE_BINARY:
			case kDATA_TYPE_MongoBinData:
				$theElement
					= new MongoBinData
						( ( function_exists( 'hex2bin' ) )
						? hex2bin( $theElement[ kTAG_DATA ] )
						: pack( 'H*', $theElement[ kTAG_DATA ] ) );
				break;
			
			//
			// Let parent handle it.
			//
			default:
				parent::UnserialiseData( $theElement );
				break;
		
		} // Parsing by type.
	
	} // UnserialiseData.

		

/*=======================================================================================
 *																						*
 *								PROTECTED MANAGEMENT INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_Commit																			*
	 *==================================================================================*/

	/**
	 * Commit provided object.
	 *
	 * We implement this method to handle MongoCollection object stores, this method will
	 * store the object in the current container.
	 *
	 * The method will check if the current container is a MongoCollection, if this is not
	 * the case, it will raise an {@link kERROR_INVALID_STATE exception}.
	 *
	 * If the provided modifiers indicate a {@link kFLAG_PERSIST_MODIFY modify} operation,
	 * the method will return the modified object, in all other cases the method will return
	 * the object identifier.
	 *
	 * @param reference			   &$theObject			Object to commit.
	 * @param mixed					$theIdentifier		Object identifier.
	 * @param bitfield				$theModifiers		Commit modifiers.
	 *
	 * @access protected
	 * @return mixed
	 */
	protected function _Commit( &$theObject, $theIdentifier, $theModifiers )
	{
		//
		// Check container.
		//
		$container = $this->Container();
		if( ! $container instanceof MongoCollection )
			throw new CException
				( "Missing native container",
				  kERROR_INVALID_STATE,
				  kMESSAGE_TYPE_ERROR,
				  array( 'Container' => $container ) );							// !@! ==>
		
		//
		// Init options.
		//
		$options = array( 'safe' => TRUE );
		
		//
		// Handle replace.
		//
		if( ($theModifiers & kFLAG_PERSIST_REPLACE) == kFLAG_PERSIST_REPLACE )
		{
			//
			// Set identifier.
			//
			if( $theIdentifier !== NULL )
				$theObject[ kTAG_ID_NATIVE ] = $theIdentifier;
			
			//
			// Save array.
			// Note: we need to do this ugly stuff because the
			// save method parameter is not declared as a reference.
			//
			if( is_array( $theObject ) )
			{
				$object = new ArrayObject( $theObject );
				$status = $container->save( $object, $options );
				$theObject = $object->getArrayCopy();
			}
			
			//
			// Save object.
			//
			else
				$status = $container->save( $theObject, $options );
			
			//
			// Check status.
			//
			if( ! $status[ 'ok' ] )
				throw new CException
					( "Unable to save object",
					  kERROR_INVALID_STATE,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Status' => $status ) );							// !@! ==>
			
			return $theObject[ kTAG_ID_NATIVE ];									// ==>
		
		} // Replace.
		
		//
		// Handle modify.
		//
		if( ($theModifiers & kFLAG_PERSIST_MODIFY) == kFLAG_PERSIST_MODIFY )
		{
			//
			// Determine criteria.
			//
			if( $theIdentifier !== NULL )
				$criteria = array( kTAG_ID_NATIVE => $theIdentifier );
			elseif( array_key_exists( kTAG_ID_NATIVE, (array) $theObject ) )
			{
				$theIdentifier = $theObject[ kTAG_ID_NATIVE ];
				$criteria = array( kTAG_ID_NATIVE => $theIdentifier );
			}
			else
				throw new CException
					( "Missing object identifier",
					  kERROR_OPTION_MISSING,
					  kMESSAGE_TYPE_ERROR );									// !@! ==>
			
			//
			// Set default commit options.
			//
			$options[ 'upsert' ] = FALSE;
			$options[ 'multiple' ] = FALSE;
			
			//
			// Create deletions matrix.
			//
			$tmp = Array();
			foreach( $theObject as $key => $value )
			{
				if( $value === NULL )
					$tmp[ $key ] = 1;
			}
			
			//
			// Remove attributes.
			//
			if( count( $tmp ) )
			{
				//
				// Set command.
				//
				$tmp = array( '$unset' => $tmp );
				
				//
				// Update.
				//
				$status = $container->update( $criteria, $tmp, $options );
			}
			
			//
			// Create additions matrix.
			//
			$tmp = Array();
			foreach( $theObject as $key => $value )
			{
				if( ($value !== NULL)
				 && ($key != kTAG_ID_NATIVE) )
					$tmp[ $key ] = $value;
			}
			
			//
			// Modify attributes.
			//
			if( count( $tmp ) )
			{
				//
				// Set command.
				//
				$tmp = array( '$set' => $tmp );
				
				//
				// Update.
				//
				$status = $container->update( $criteria, $tmp, $options );
			}
			
			return $this->Load( $theIdentifier );									// ==>
		
		} // Modify.
		
		//
		// Handle insert.
		//
		if( $theModifiers & kFLAG_PERSIST_INSERT )
		{
			//
			// Set identifier.
			//
			if( $theIdentifier !== NULL )
				$theObject[ kTAG_ID_NATIVE ] = $theIdentifier;
			
			//
			// Save array.
			// Note: we need to do this ugly stuff because the
			// save method parameter is not declared as a reference.
			//
			if( is_array( $theObject ) )
			{
				$object = new ArrayObject( $theObject );
				$status = $container->insert( $object, $options );
				$theObject = $object->getArrayCopy();
			}
			
			//
			// Save object.
			//
			else
				$status = $container->insert( $theObject, $options );
			
			return $theObject[ kTAG_ID_NATIVE ];									// ==>
		
		} // Insert.
		
		//
		// Handle update.
		//
		if( $theModifiers & kFLAG_PERSIST_UPDATE )
		{
			//
			// Set default commit options.
			//
			$options[ 'upsert' ] = FALSE;
			$options[ 'multiple' ] = FALSE;
			
			//
			// Determine criteria.
			//
			if( $theIdentifier !== NULL )
				$criteria = array( kTAG_ID_NATIVE => $theIdentifier );
			elseif( array_key_exists( kTAG_ID_NATIVE, (array) $theObject ) )
			{
				$theIdentifier = $theObject[ kTAG_ID_NATIVE ];
				$criteria = array( kTAG_ID_NATIVE => $theIdentifier );
			}
			else
				throw new CException
					( "Missing object identifier",
					  kERROR_OPTION_MISSING,
					  kMESSAGE_TYPE_ERROR );									// !@! ==>
			
			//
			// Update.
			//
			$status = $container->update( $criteria, $theObject, $options );
			if( ! $status[ 'updatedExisting' ] )
				throw new CException
					( "Object not found",
					  kERROR_NOT_FOUND,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Identifier' => $theIdentifier ) );				// !@! ==>
			
			return $theIdentifier;													// ==>
		
		} // Update.
		
		throw new CException
			( "Invalid operation options",
			  kERROR_INVALID_PARAMETER,
			  kMESSAGE_TYPE_ERROR,
			  array( 'Modifiers' => $theModifiers,
					 'Mask' => kFLAG_PERSIST_WRITE_MASK ) );					// !@! ==>
	
	} // _Commit.

	 
	/*===================================================================================
	 *	_Load																			*
	 *==================================================================================*/

	/**
	 * Load object.
	 *
	 * We implement this method to handle MongoCollection object stores, this method will
	 * retrieve the object from the current container.
	 *
	 * The {@link Load() caller} will have resolved {@link CMongoDBRef references} and
	 * eventually extracted the identifier from the provided parameter.
	 *
	 * This method will check if the current container is a MongoCollection, if this is not 
	 * the case, it will raise an {@link kERROR_INVALID_STATE exception}.
	 *
	 * The method will use the <i>findOne</i> method to retrieve the object.
	 *
	 * @param mixed					$theIdentifier		Object identifier.
	 * @param bitfield				$theModifiers		Load modifiers.
	 *
	 * @access protected
	 * @return mixed
	 */
	protected function _Load( $theIdentifier, $theModifiers )
	{
		//
		// Check container.
		//
		$container = $this->Container();
		if( ! $container instanceof MongoCollection )
			throw new CException
				( "Missing native container",
				  kERROR_INVALID_STATE,
				  kMESSAGE_TYPE_ERROR,
				  array( 'Container' => $container ) );							// !@! ==>
		
		//
		// Set criteria.
		//
		$criteria = array( kTAG_ID_NATIVE => $theIdentifier );
		
		return $container->findOne( $criteria );									// ==>
	
	} // _Load.

	 
	/*===================================================================================
	 *	_Delete																			*
	 *==================================================================================*/

	/**
	 * Delete object.
	 *
	 * We implement this method to handle MongoCollection object stores, this method will
	 * remove the object from the current container.
	 *
	 * The method will check if the current container is a MongoCollection, if this is not
	 * the case, it will raise an {@link kERROR_INVALID_STATE exception}.
	 *
	 * @param mixed					$theIdentifier		Object identifier.
	 * @param bitfield				$theModifiers		Delete modifiers.
	 *
	 * @access protected
	 * @return mixed
	 */
	protected function _Delete( $theIdentifier, $theModifiers )
	{
		//
		// Check container.
		//
		$container = $this->Container();
		if( ! $container instanceof MongoCollection )
			throw new CException
				( "Missing native container",
				  kERROR_INVALID_STATE,
				  kMESSAGE_TYPE_ERROR,
				  array( 'Container' => $container ) );							// !@! ==>
		
		//
		// Set criteria.
		//
		$criteria = array( kTAG_ID_NATIVE => $theIdentifier );
		
		//
		// Save object.
		//
		$save = $this->Load( $theIdentifier );
		if( $save !== NULL )
		{
			//
			// Set options.
			//
			$options = array( 'safe' => TRUE, 'justOne' => TRUE );
			
			$status = $container->remove( $criteria, $options );								// ==>
		
		} // Object exists.
		
		return $save;																// ==>
		
	} // _Delete.

	 

} // class CMongoContainer.


?>
