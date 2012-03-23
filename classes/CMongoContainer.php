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
			
			return $theObject[ kTAG_ID_NATIVE ];									// ==>
		
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
				$criteria = array( kTAG_ID_NATIVE => $theIdentifier );
			
			//
			// Get identifier from object.
			//
			elseif( array_key_exists( kTAG_ID_NATIVE, (array) $theObject ) )
			{
				$theIdentifier = $theObject[ kTAG_ID_NATIVE ];
				$criteria = array( kTAG_ID_NATIVE => $theIdentifier );
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

		

/*=======================================================================================
 *																						*
 *								PROTECTED PERSISTENCE UTILITIES							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_PrepareLoad																	*
	 *==================================================================================*/

	/**
	 * Prepare before a {@link _Load() load}.
	 *
	 * The duty of this method is to ensure that the parameters provided to the
	 * {@link _Load() find} operation are valid.
	 *
	 * In this class we ensure that the identifier has been provided, that the current
	 * object has a native {@link Container() container} and we
	 * {@link UnserialiseData() unserialise} the provided identifier if
	 * {@link kFLAG_STATE_ENCODED needed}.
	 *
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Load modifiers.
	 *
	 * @access protected
	 *
	 * @throws {@link CException CException}
	 *
	 * @uses Container()
	 * @uses UnserialiseData()
	 *
	 * @see kFLAG_STATE_ENCODED
	 * @see kERROR_OPTION_MISSING kERROR_INVALID_STATE
	 */
	protected function _PrepareLoad( &$theIdentifier, &$theModifiers )
	{
		//
		// Check if identifier is there.
		//
		if( $theIdentifier === NULL )
			throw new CException
					( "Missing object identifier",
					  kERROR_OPTION_MISSING,
					  kMESSAGE_TYPE_ERROR );									// !@! ==>
		
		//
		// Check container.
		//
		if( $this->Container() === NULL )
			throw new CException
				( "Missing native container",
				  kERROR_INVALID_STATE,
				  kMESSAGE_TYPE_ERROR );										// !@! ==>
		
		//
		// Unserialise identifier.
		//
		if( $theModifiers & kFLAG_STATE_ENCODED )
			$this->UnserialiseData( $theIdentifier );
	
	} // _PrepareLoad.

	 
	/*===================================================================================
	 *	_PrepareCommit																	*
	 *==================================================================================*/

	/**
	 * Normalise before a store.
	 *
	 * We {@link CContainer::_PrepareCommit() overload} this method to perform the following
	 * operations:
	 *
	 * <ul>
	 *	<li>Check object type (array or ArrayObject).
	 *	<li>Initialise identifier if required.
	 *	<li>Call the parent {@link CContainer::_PrepareCommit() method} which will:
	 *	 <ul>
	 *		<li>Ensure the identifier is provided if the operation is not an
	 *			{@link kFLAG_PERSIST_INSERT insert}.
	 *		<li>Ensure the method has the correct options.
	 *		<li>Ensure the current object has a container.
	 *		<li>Get the {@link CPersistentObject::_IsEncoded() encoded} status
	 *			{@link kFLAG_STATE_ENCODED flag} from the object.
	 *		<li>{@link UnserialiseObject() Unserialise} object and
	 *			{@link UnserialiseData() identifier} if necessary.
	 *	 </ul>
	 * </ul>
	 *
	 * @param reference			   &$theObject			Object or data.
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Commit modifiers.
	 *
	 * @access protected
	 *
	 * @throws {@link CException CException}
	 *
	 * @uses _Container()
	 *
	 * @see kERROR_DUPLICATE kERROR_NOT_FOUND
	 */
	protected function _PrepareCommit( &$theObject, &$theIdentifier, &$theModifiers )
	{
		//
		// Check object type.
		//
		if( (! is_array( $theObject ))
		 && (! $theObject instanceof ArrayObject) )
			throw new CException
				( "Invalid data or object",
				  kERROR_INVALID_PARAMETER,
				  kMESSAGE_TYPE_ERROR,
				  array( 'Object' => $theObject ) );							// !@! ==>
		
		//
		// Get identifier from object.
		//
		if( (! ($theModifiers & kFLAG_PERSIST_INSERT))	// Not an insert
		 && ($theIdentifier === NULL) )					// and missing identifier.
		{
			//
			// Get it from object.
			//
			if( array_key_exists( kTAG_ID_NATIVE, (array) $theObject ) )
				$theIdentifier = $theObject[ kTAG_ID_NATIVE
		
		} // Missing identifier.
		
		//
		// Call parent method.
		//
		parent::_PrepareCommit( $theObject, $theIdentifier, $theModifiers );
	
	} // _PrepareCommit.

	 
	/*===================================================================================
	 *	_PrepareDelete																	*
	 *==================================================================================*/

	/**
	 * Prepare before a {@link _Delete() delete}.
	 *
	 * The duty of this method is to ensure that the parameters provided to the
	 * {@link _Delete() delete} operation are valid.
	 *
	 * In this class we ensure that the identifier has been provided, that the current
	 * object has a native {@link Container() container} and we
	 * {@link UnserialiseData() unserialise} the provided identifier if
	 * {@link kFLAG_STATE_ENCODED needed}.
	 *
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Commit modifiers.
	 *
	 * @access protected
	 *
	 * @throws {@link CException CException}
	 *
	 * @uses Container()
	 *
	 * @see kERROR_INVALID_STATE
	 */
	protected function _PrepareDelete( &$theIdentifier, &$theModifiers )
	{
		//
		// Check if identifier is there.
		//
		if( $theIdentifier === NULL )
			throw new CException
					( "Missing object identifier",
					  kERROR_OPTION_MISSING,
					  kMESSAGE_TYPE_ERROR );									// !@! ==>
		
		//
		// Check container.
		//
		if( $this->Container() === NULL )
			throw new CException
				( "Missing native container",
				  kERROR_INVALID_STATE,
				  kMESSAGE_TYPE_ERROR );										// !@! ==>
		
		//
		// Unserialise identifier.
		//
		if( $theModifiers & kFLAG_STATE_ENCODED )
			$this->UnserialiseData( $theIdentifier );
	
	} // _PrepareDelete.

	 
	/*===================================================================================
	 *	_FinishLoad																		*
	 *==================================================================================*/

	/**
	 * Normalise after a {@link _Load() load}.
	 *
	 * This method will be called after the {@link _Load() load} operation, its duty is to
	 * clean up or normalise after the operation. The parameters are passed by reference.
	 *
	 * The method expects the found object or data in the first parameter and the original
	 * identifier in the second.
	 *
	 * In this class we {@link kFLAG_STATE_ENCODED eventually}
	 * {@link CDataType::SerialiseObject() serialise} the object and the
	 * {@link CDataType::SerialiseData() identifier}.
	 *
	 * @param reference			   &$theObject			Object reference.
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Commit modifiers.
	 *
	 * @access protected
	 *
	 * @uses CDataType::SerialiseObject()
	 * @uses CDataType::SerialiseData()
	 *
	 * @see kFLAG_STATE_ENCODED
	 */
	protected function _FinishLoad( &$theObject, &$theIdentifier, &$theModifiers )
	{
		//
		// Serialise object.
		//
		if( $theModifiers & kFLAG_STATE_ENCODED )
		{
			//
			// Serialise object.
			//
			CDataType::SerialiseObject( $theObject );
			
			//
			// Serialise identifier.
			//
			CDataType::SerialiseData( $theIdentifier );
			
			//
			// Copy status.
			//
			if( $theObject instanceof CPersistentObject )
				$theObject->Status( $theObject->Status() | kFLAG_STATE_ENCODED );
		
		} // Serialise option.
	
	} // _FinishCommit.

	 
	/*===================================================================================
	 *	_FinishCommit																	*
	 *==================================================================================*/

	/**
	 * Normalise after a {@link _Commit() store}.
	 *
	 * This method will be called after the {@link _Commit() store} operation, its duty is
	 * to clean up or restore the object after the operation please refer to
	 * {@link Commit() this} documentation for a reference of these parameters. Note that in
	 * this method all three parameters are passed by reference.
	 *
	 * In this class we {@link kFLAG_STATE_ENCODED eventually}
	 * {@link CDataType::SerialiseObject() serialise} the object.
	 *
	 * @param reference			   &$theObject			Object or data.
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Commit modifiers.
	 *
	 * @access protected
	 *
	 * @uses CDataType::SerialiseObject()
	 * @uses CDataType::SerialiseData()
	 *
	 * @see kFLAG_STATE_ENCODED
	 */
	protected function _FinishCommit( &$theObject, &$theIdentifier, &$theModifiers )
	{
		//
		// Serialise object.
		//
		if( $theModifiers & kFLAG_STATE_ENCODED )
		{
			//
			// Serialise object.
			//
			CDataType::SerialiseObject( $theObject );
	
			//
			// Serialise identifier.
			//
			CDataType::SerialiseData( $theIdentifier );
		}
	
	} // _FinishCommit.

	 
	/*===================================================================================
	 *	_FinishDelete																	*
	 *==================================================================================*/

	/**
	 * Normalise after a store.
	 *
	 * This method will be called after the {@link _Delete() delete} operation, its duty is
	 * to clean up or restore the object after the operation please refer to
	 * {@link Delete() this} documentation for a reference of these parameters. Note that in
	 * this method all two parameters are passed by reference.
	 *
	 * In this class we {@link kFLAG_STATE_ENCODED eventually}
	 * {@link CDataType::SerialiseObject() serialise} the object and the
	 * {@link CDataType::SerialiseData() identifier}.
	 *
	 * @param reference			   &$theObject			Object or data.
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Commit modifiers.
	 *
	 * @access protected
	 */
	protected function _FinishDelete( &$theObject, &$theIdentifier, &$theModifiers )
	{
		//
		// Serialise identifier.
		//
		if( $theModifiers & kFLAG_STATE_ENCODED )
		{
			//
			// Serialise object.
			//
			CDataType::SerialiseObject( $theObject );
	
			//
			// Serialise identifier.
			//
			CDataType::SerialiseData( $theIdentifier );
		}
	
	} // _FinishDelete.

	 

} // class CMongoContainer.


?>
