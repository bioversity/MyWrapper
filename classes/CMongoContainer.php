<?php

/**
 * <i>CMongoContainer</i> class definition.
 *
 * This file contains the class definition of <b>CMongoContainer</b> which implements a
 * MongoDB object store.
 *
 *	@package	MyWrapper
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
 *	@package	MyWrapper
 *	@subpackage	Persistence
 */
class CMongoContainer extends CContainer
{
		

/*=======================================================================================
 *																						*
 *											MAGIC										*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	__toString																		*
	 *==================================================================================*/

	/**
	 * Return container name.
	 *
	 * This method should return the current container's name.
	 *
	 * In this class we return the collection name.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses Container()
	 */
	public function __toString()
	{
		//
		// Get container.
		//
		if( ($container = $this->Container()) !== NULL )
			return $container->getName();											// ==>
		
		return parent::__toString();												// ==>
	
	} // __toString.

		

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
		// Handle retrieve or delete.
		//
		if( ($theValue === NULL)
		 || ($theValue === FALSE) )
			return parent::Container( $theValue, $getOld );							// ==>
		
		//
		// Check value.
		//
		if( $theValue instanceof MongoCollection )
			return parent::Container( $theValue, $getOld );							// ==>
		
		throw new CException( "Invalid container type",
							  kERROR_INVALID_PARAMETER,
							  kMESSAGE_TYPE_ERROR,
							  array( 'Container' => $theValue ) );				// !@! ==>

	} // Container.

		

/*=======================================================================================
 *																						*
 *								PUBLIC ELEMENT INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Database																		*
	 *==================================================================================*/

	/**
	 * Return database.
	 *
	 * In this class we return the collection's database.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses Container()
	 */
	public function Database()
	{
		//
		// Get container.
		//
		if( ($container = $this->Container()) !== NULL )
			return $container->db;													// ==>
		
		return parent::Database();													// ==>
	
	} // Database.

		

/*=======================================================================================
 *																						*
 *								PUBLIC CONVERSION INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	UnserialiseData																	*
	 *==================================================================================*/

	/**
	 * Unserialise provided data element.
	 *
	 * We {@link CContainer::UnserialiseData() implement} this method to convert all
	 * standard {@link CDataType types} into custom Mongo data types.
	 *
	 * In this class we parse the following types and {@link kTAG_TYPE offsets}:
	 *
	 * <ul>
	 *	<li><i>{@link CDataTypeMongoId CDataTypeMongoId} object or
	 *		{@link kTYPE_MongoId kTYPE_MongoId} offset</i>: We return a MongoId
	 *		object.
	 *	<li><i>{@link CDataTypeMongoCode CDataTypeMongoCode} object or
	 *		{@link kTYPE_MongoCode kTYPE_MongoCode} offset</i>: We return a
	 *		MongoCode object.
	 *	<li><i>{@link CDataTypeStamp CDataTypeStamp} object or
	 *		{@link kTYPE_STAMP kTYPE_STAMP} offset</i>: We return a MongoDate
	 *		object.
	 *	<li><i>{@link CDataTypeRegex CDataTypeRegex} object or
	 *		{@link kTYPE_REGEX kTYPE_REGEX} offset</i>: We return a
	 *		MongoRegex object.
	 *	<li><i>{@link CDataTypeInt32 CDataTypeInt32} object or
	 *		{@link kTYPE_INT32 kTYPE_INT32} offset</i>: We return a MongoInt32
	 *		object.
	 *	<li><i>{@link CDataTypeInt64 CDataTypeInt64} object or
	 *		{@link kTYPE_INT64 kTYPE_INT64} offset</i>: We return a MongoInt64
	 *		object.
	 *	<li><i>{@link CDataTypeBinary CDataTypeBinary} object or
	 *		{@link kTYPE_BINARY kTYPE_BINARY} offset</i>: We return a MongoBinData
	 *		object.
	 * </ul>
	 *
	 * @param reference			   &$theElement			Element to encode.
	 *
	 * @access public
	 */
	public function UnserialiseData( &$theElement )
	{
		//
		// Check type.
		//
		if( is_array( $theElement )
		 || ($theElement instanceof ArrayObject) )
		{
			//
			// Parse by type.
			//
			$data = $theElement[ kTAG_DATA ];
			switch( $theElement[ kTAG_TYPE ] )
			{
				//
				// MongoId.
				//
				case kTYPE_MongoId:
					$theElement = new MongoId( (string) $data );
					break;
				
				//
				// MongoCode.
				//
				case kTYPE_MongoCode:
					if( is_array( $data )
					 || ($data instanceof ArrayObject) )
					{
						$tmp1 = $data[ kOBJ_TYPE_CODE_SRC ];
						$tmp2 = ( array_key_exists( kOBJ_TYPE_CODE_SCOPE, (array) $data ) )
							  ? $data[ kOBJ_TYPE_CODE_SCOPE ]
							  : Array();
						$theElement = new MongoCode( $tmp1, $tmp2 );
					}
					break;
				
				//
				// MongoDate.
				//
				case kTYPE_STAMP:
					if( is_array( $data )
					 || ($data instanceof ArrayObject) )
					{
						$tmp1 = $data[ kTYPE_STAMP_SEC ];
						$tmp2 = ( array_key_exists( kTYPE_STAMP_USEC, (array) $data ) )
							  ? $data[ kTYPE_STAMP_USEC ]
							  : 0;
						$theElement = new MongoDate( $tmp1, $tmp2 );
					}
					break;
				
				//
				// MongoInt32.
				//
				case kTYPE_INT32:
					$theElement = new MongoInt32( $data );
					break;
				
				//
				// MongoInt64.
				//
				case kTYPE_INT64:
					$theElement = new MongoInt64( $data );
					break;
	
				//
				// MongoRegex.
				//
				case kTYPE_REGEX:
					$theElement = new MongoRegex( $data );
					break;
	
				//
				// MongoBinData.
				//
				case kTYPE_BINARY:
					$data = ( function_exists( 'hex2bin' ) )
						  ? hex2bin( $data )
						  : pack( 'H*', $data );
					$theElement = new MongoBinData( $data );
					break;
			
			} // Parsing by type.
		
		} // Element is a structure.
	
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
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Commit modifiers.
	 *
	 * @access protected
	 * @return mixed
	 *
	 * @uses Container()
	 *
	 * @see kFLAG_PERSIST_INSERT kFLAG_PERSIST_MODIFY
	 * @see kFLAG_PERSIST_REPLACE kFLAG_PERSIST_UPDATE
	 */
	protected function _Commit( &$theObject, &$theIdentifier, &$theModifiers )
	{
		//
		// Init local storage.
		//
		$container = $this->Container();
		$options = array( 'safe' => TRUE );
		
		//
		// Handle replace.
		//
		if( ($theModifiers & kFLAG_PERSIST_REPLACE) == kFLAG_PERSIST_REPLACE )
		{
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
				throw new CException( "Unable to save object",
									  kERROR_INVALID_STATE,
									  kMESSAGE_TYPE_ERROR,
									  array( 'Status' => $status ) );			// !@! ==>
			
			return $theObject[ kTAG_LID ];									// ==>
		
		} // Replace.
		
		//
		// Handle modify.
		//
		if( ($theModifiers & kFLAG_PERSIST_MODIFY) == kFLAG_PERSIST_MODIFY )
		{
			//
			// Use provided identifier.
			//
			if( $theIdentifier !== NULL )
				$criteria = array( kTAG_LID => $theIdentifier );
			
			//
			// Get identifier from object.
			//
			elseif( array_key_exists( kTAG_LID, (array) $theObject ) )
			{
				$theIdentifier = $theObject[ kTAG_LID ];
				$criteria = array( kTAG_LID => $theIdentifier );
			}
			
			//
			// Raise your hands.
			//
			else
				throw new CException( "Missing object identifier",
									  kERROR_OPTION_MISSING,
									  kMESSAGE_TYPE_ERROR,
									  array( 'Object' => $theObject ) );		// !@! ==>
			
			//
			// Set default commit options.
			//
			$options[ 'upsert' ] = FALSE;
			$options[ 'multiple' ] = FALSE;
			
			//
			// Handle additions and deletions.
			//
			if( ! ($theModifiers & kFLAG_MODIFY_MASK) )
			{
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
					 && ($key != kTAG_LID) )
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
			
			} // No specific modification options provided.
			
			//
			// Increment/decrement field.
			//
			elseif( ($theModifiers & kFLAG_MODIFY_MASK) == kFLAG_MODIFY_INCREMENT )
			{
				//
				// Set command.
				//
				$tmp = array( '$inc' => Array() );
				foreach( $theObject as $key => $value )
				{
					if( $key != kTAG_LID )
						$tmp[ '$inc' ][ $key ] = $value;
				}
				
				//
				// Update.
				//
				$status = $container->update( $criteria, $tmp, $options );
			
			} // Increment/decrement.
			
			//
			// Append to field.
			//
			elseif( ($theModifiers & kFLAG_MODIFY_MASK) == kFLAG_MODIFY_APPEND )
			{
				//
				// Set command.
				//
				$tmp = array( '$push' => Array() );
				foreach( $theObject as $key => $value )
				{
					if( $key != kTAG_LID )
						$tmp[ '$push' ][ $key ] = $value;
				}
				
				//
				// Update.
				//
				$status = $container->update( $criteria, $tmp, $options );
			
			} // Append.
			
			//
			// Add to set.
			//
			elseif( ($theModifiers & kFLAG_MODIFY_MASK) == kFLAG_MODIFY_ADDSET )
			{
				//
				// Set command.
				//
				$tmp = array( '$addToSet' => Array() );
				foreach( $theObject as $key => $value )
				{
					if( $key != kTAG_LID )
						$tmp[ '$addToSet' ][ $key ] = $value;
				}
				
				//
				// Update.
				//
				$status = $container->update( $criteria, $tmp, $options );
			
			} // Add to set.
			
			//
			// Pop.
			//
			elseif( ($theModifiers & kFLAG_MODIFY_MASK) == kFLAG_MODIFY_POP )
			{
				//
				// Set command.
				//
				$tmp = array( '$pop' => Array() );
				foreach( $theObject as $key => $value )
				{
					if( $key != kTAG_LID )
						$tmp[ '$pop' ][ $key ] = $value;
				}
				
				//
				// Update.
				//
				$status = $container->update( $criteria, $tmp, $options );
			
			} // Pop.
			
			//
			// Pull.
			//
			elseif( ($theModifiers & kFLAG_MODIFY_MASK) == kFLAG_MODIFY_PULL )
			{
				//
				// Set command.
				//
				$tmp = array( '$pull' => Array() );
				foreach( $theObject as $key => $value )
				{
					if( $key != kTAG_LID )
						$tmp[ '$pull' ][ $key ] = $value;
				}
				
				//
				// Update.
				//
				$status = $container->update( $criteria, $tmp, $options );
			
			} // Pull.
			
			else
				throw new CException
					( "Invalid modification options",
					  kERROR_INVALID_PARAMETER,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Modifiers' => $theModifiers ) );					// !@! ==>
			
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
				$theObject[ kTAG_LID ] = $theIdentifier;
			
			//
			// Save array.
			// Note: we need to do this ugly stuff because the
			// method expects an object, an array will not be updated,
			// at least as of 5/4/2012.
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
			
			return $theObject[ kTAG_LID ];									// ==>
		
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
				$criteria = array( kTAG_LID => $theIdentifier );
			elseif( array_key_exists( kTAG_LID, (array) $theObject ) )
			{
				$theIdentifier = $theObject[ kTAG_LID ];
				$criteria = array( kTAG_LID => $theIdentifier );
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
			  array( 'Modifiers' => $theModifiers ) );							// !@! ==>
	
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
	 * The {@link Load() caller} will have resolved references andeventually extracted the
	 * identifier from the provided parameter.
	 *
	 * If the provided parameter is an array, the method will assume it is a query; if not,
	 * the method will assume it is the value to match against the {@link kTAG_LID local}
	 * identifier.
	 *
	 * The method will use the <i>findOne</i> method to retrieve the object.
	 *
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Load modifiers.
	 *
	 * @access protected
	 * @return mixed
	 */
	protected function _Load( &$theIdentifier, &$theModifiers )
	{
		//
		// Set criteria.
		//
		$criteria = ( is_array( $theIdentifier ) )
				  ? $theIdentifier
				  : array( kTAG_LID => $theIdentifier );
		
		return $this->Container()->findOne( $criteria );							// ==>
	
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
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Load modifiers.
	 *
	 * @access protected
	 * @return mixed
	 */
	protected function _Delete( &$theIdentifier, &$theModifiers )
	{
		//
		// Load object.
		// Note that we intentionally do not provide the modifiers,
		// because the identifier has just been unserialised.
		//
		$save = $this->Load( $theIdentifier );
		
		//
		// Delete object.
		//
		if( $save !== NULL )
		{
			//
			// Set criteria.
			//
			$criteria = array( kTAG_LID => $theIdentifier );
			
			//
			// Set options.
			//
			$options = array( 'safe' => TRUE, 'justOne' => TRUE );
			
			//
			// Remove object.
			//
			$status = $this->Container()->remove( $criteria, $options );
		
		} // Object exists.
		
		return $save;																// ==>
		
	} // _Delete.

		

/*=======================================================================================
 *																						*
 *								PROTECTED PERSISTENCE UTILITIES							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_PrepareCommit																	*
	 *==================================================================================*/

	/**
	 * Prepare before a {@link _Commit() commit}.
	 *
	 * We {@link CContainer::_PrepareCommit() overload} this method to handle the
	 * identifier: if provided, it means that that is to become the object's unique
	 * {@link kTAG_LID identifier}; if not provided and the object has an
	 * {@link kTAG_LID identifier}, we use that one.
	 *
	 * We also raise an exception if the provided object is not either an array or an
	 * ArrayObject.
	 *
	 * @param reference			   &$theObject			Object or data.
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Commit modifiers.
	 *
	 * @access protected
	 *
	 * @throws {@link CException CException}
	 *
	 * @uses Container()
	 *
	 * @see kFLAG_STATE_ENCODED
	 * @see kERROR_OPTION_MISSING kERROR_INVALID_PARAMETER kERROR_INVALID_STATE
	 */
	protected function _PrepareCommit( &$theObject, &$theIdentifier, &$theModifiers )
	{
		//
		// Check object.
		//
		if( (! is_array( $theObject ))
		 && (!$theObject instanceof ArrayObject) )
			throw new CException
				( "Unsupported or invalid data or object",
				  kERROR_UNSUPPORTED,
				  kMESSAGE_TYPE_ERROR,
				  array( 'Object' => $theObject ) );							// !@! ==>
		
		//
		// Call parent method.
		//
		parent::_PrepareCommit( $theObject, $theIdentifier, $theModifiers );

		//
		// Set identifier.
		//
		if( $theIdentifier !== NULL )
			$theObject[ kTAG_LID ] = $theIdentifier;
			
		//
		// Get identifier.
		//
		elseif( array_key_exists( kTAG_LID, (array) $theObject ) )
			$theIdentifier = $theObject[ kTAG_LID ];
	
	} // _PrepareCommit.

	 
	/*===================================================================================
	 *	_PrepareLoad																	*
	 *==================================================================================*/

	/**
	 * Prepare before a {@link _Load() load}.
	 *
	 * The duty of this method is to ensure that the parameters provided to the
	 * {@link _Load() find} operation are valid.
	 *
	 * In this class we {@link CContainer::_PrepareLoad() overload} this method to handle
	 * identifiers provided as queries.
	 *
	 * @param reference			   &$theContainer		Object container.
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Create modifiers.
	 *
	 * @access protected
	 *
	 * @throws {@link CException CException}
	 *
	 * @uses Container()
	 * @uses UnserialiseData()
	 *
	 * @see kFLAG_STATE_ENCODED
	 */
	protected function _PrepareLoad( &$theIdentifier, &$theModifiers )
	{
		//
		// Handle queries.
		//
		if( $theIdentifier instanceof CMongoQuery )
		{
			//
			// Check container.
			//
			if( $this->Container() === NULL )
				throw new CException
					( "Missing native container",
					  kERROR_INVALID_STATE,
					  kMESSAGE_TYPE_ERROR );									// !@! ==>
		
			//
			// Check if identifier is there.
			//
			if( $theIdentifier === NULL )
				throw new CException
						( "Missing object identifier",
						  kERROR_OPTION_MISSING,
						  kMESSAGE_TYPE_ERROR );								// !@! ==>
			
			//
			// Convert query.
			//
			$theIdentifier = $theIdentifier->Export( $this );
		
		} // Provided a query.
		
		//
		// Handle identifier.
		//
		else
			parent::_PrepareLoad( $theIdentifier, $theModifiers );
		
	} // _PrepareLoad.

	 

} // class CMongoContainer.


?>
