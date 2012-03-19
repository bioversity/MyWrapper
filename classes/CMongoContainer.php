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
		$container = $this->Container();
		if( $container !== NULL )
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
		$container = $this->Container();
		if( $container !== NULL )
			return $container->db;													// ==>
		
		return parent::Database();													// ==>
	
	} // Database.

		

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
