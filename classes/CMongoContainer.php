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
require_once( kPATH_LIBRARY_SOURCE."CArrayContainer.php" );

/**
 * Mongo persistent data store.
 *
 * This class extends its {@link CArrayContainer ancestor} to implement an object store
 * based on MongoCollection containers.
 *
 * We inherit from {@link CArrayContainer CArrayContainer} to fall back onto a concrete
 * instance.
 *
 * @package		Framework
 * @subpackage	Persistence
 */
class CMongoContainer extends CArrayContainer
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
	 * We {@link CArrayContainer::Container() overload} this method to ensure that the
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
		
		return $this->_ManageMember( $this->mContainer, $theValue, $getOld );		// ==>

	} // Container.

		

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
	 * We {@link CArrayContainer::_Commit() overload} this method to handle MongoCollection
	 * stores and we perform the actual operation.
	 *
	 * @param mixed					$theObject			Object to commit.
	 * @param mixed					$theIdentifier		Object identifier.
	 * @param bitfield				$theModifiers		Commit modifiers.
	 *
	 * @access protected
	 * @return mixed
	 */
	protected function _Commit( $theObject, $theIdentifier, $theModifiers )
	{
		//
		// Check container.
		//
		$container = & $this->_Container();
		if( ! $container instanceof MongoCollection )
			throw new CException
				( "Missing native container",
				  kERROR_INVALID_STATE,
				  kMESSAGE_TYPE_ERROR,
				  array( 'Container' => $container ) );							// !@! ==>
		
		//
		// Check options.
		//
		if( ! $theModifiers & kFLAG_PERSIST_WRITE_MASK )
			throw new CException
				( "Invalid operation options",
				  kERROR_INVALID_PARAMETER,
				  kMESSAGE_TYPE_ERROR,
				  array( 'Modifiers' => $theModifiers,
						 'Mask' => kFLAG_PERSIST_WRITE_MASK ) );				// !@! ==>
		
		//
		// Replace.
		//
		if( ($theModifiers & kFLAG_PERSIST_REPLACE) == kFLAG_PERSIST_REPLACE )
		{
			//
			// Set default commit options.
			//
			$options = array( 'safe' => TRUE );
			
			//
			// Save object.
			//
			$status = $theContainer->save( $theObject, $options );
			
			return $theObject[ kTAG_ID_NATIVE ];									// ==>
		
		} // Replace.
		
		//
		// Handle modify.
		//
		if( ($theModifiers & kFLAG_PERSIST_MODIFY) == kFLAG_PERSIST_MODIFY )
		{
			//
			// Set default commit options.
			//
			$options = array( 'safe' => TRUE, 'multiple' => FALSE, 'upsert' => FALSE );
			
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
				$status
					= $container->update
						( array( kTAG_ID_NATIVE => $theIdentifier ), $tmp, $options );
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
				$status
					= $container->update
						( array( kTAG_ID_NATIVE => $theIdentifier ), $tmp, $options );
			}
			
			return $theIdentifier;													// ==>
		
		} // Modify.
		
		
		
		

		//
		// Check for duplicates.
		//
		if( $theModifiers & kFLAG_PERSIST_INSERT )
		
		//
		// Check if there.
		//
		elseif( ($theModifiers & kFLAG_PERSIST_UPDATE)
			 && (! array_key_exists( (string) $theIdentifier, (array) $container )) )
		

		//
		// Save object.
		//
		$status = $theContainer->save( $this, array( 'safe' => TRUE ) );
		
		//
		// Get identifier.
		//
		$theIdentifier = $this->offsetGet( kTAG_ID_NATIVE );

		//
		// Get container reference.
		//
		$container = & $this->_Container();
		
		//
		// Check container.
		//
		if( ! (($theModifiers & kFLAG_PERSIST_REPLACE) == kFLAG_PERSIST_REPLACE) )
		{
			//
			// Check for duplicates.
			//
			if( ($theModifiers & kFLAG_PERSIST_INSERT)
			 && array_key_exists( (string) $theIdentifier, (array) $container ) )
				throw new CException
					( "Duplicate entry",
					  kERROR_DUPLICATE,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Identifier' => $theIdentifier ) );				// !@! ==>
			
			//
			// Check if there.
			//
			elseif( ($theModifiers & kFLAG_PERSIST_UPDATE)
				 && (! array_key_exists( (string) $theIdentifier, (array) $container )) )
				throw new CException
					( "Missing entry",
					  kERROR_NOT_FOUND,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Identifier' => $theIdentifier ) );				// !@! ==>
		
		} // Replace.

		//
		// Init identifier.
		//
		if( $theIdentifier === NULL )
		{
			//
			// Append.
			//
			$container[] = $theObject;
			
			//
			// Copy to array.
			//
			$copy = (array) $container;
			
			//
			// Point to last (just added).
			//
			end( $copy );
			
			//
			// Set identifier.
			//
			$theIdentifier = key( $copy );
		
		} // Missing identifier.
		
		//
		// Replace object.
		//
		if( ! (($theModifiers & kFLAG_PERSIST_MODIFY) == kFLAG_PERSIST_MODIFY) )
			$container[ (string) $theIdentifier ] = $theObject;
		
		//
		// Modify object.
		//
		else
		{
			//
			// Get existing object.
			//
			$object = $container[ (string) $theIdentifier ];
			
			//
			// Modify.
			//
			foreach( $theObject as $key => $value )
			{
				if( $value !== NULL )
					$object[ $key ] = $theObject[ $key ];
				else
				{
					if( array_key_exists( $key, (array) $object ) )
						unset( $object[ $key ] );
				}
			}
			
			//
			// Update.
			//
			$container[ (string) $theIdentifier ] = $object;
		
		} // Modify.
		
		return $theIdentifier;														// ==>
	
	} // _Commit.

	 
	/*===================================================================================
	 *	_Load																			*
	 *==================================================================================*/

	/**
	 * Load object.
	 *
	 * We implement this method to handle array or ArrayObject stores.
	 *
	 * The method will cast the identifier to a string.
	 *
	 * @param mixed					$theIdentifier		Object identifier.
	 * @param mixed					$theOptions			Delete options.
	 *
	 * @access protected
	 * @return mixed
	 */
	protected function _Load( $theIdentifier, $theOptions = NULL )
	{
		//
		// Get container reference.
		//
		$container = & $this->_Container();
		
		//
		// Return match.
		//
		if( array_key_exists( (string) $theIdentifier, (array) $container ) )
			return $container[ (string) $theIdentifier ];							// ==>
		
		return NULL;																// ==>
	
	} // _Load.

	 
	/*===================================================================================
	 *	_Delete																			*
	 *==================================================================================*/

	/**
	 * Delete object.
	 *
	 * We implement this method to handle array or ArrayObject stores.
	 *
	 * The method will cast the identifier to a string.
	 *
	 * @param mixed					$theIdentifier		Object identifier.
	 * @param mixed					$theOptions			Delete options.
	 *
	 * @access protected
	 * @return mixed
	 */
	protected function _Delete( $theIdentifier, $theOptions = NULL )
	{
		//
		// Get container reference.
		//
		$container = & $this->_Container();
		
		//
		// Delete match.
		//
		if( array_key_exists( (string) $theIdentifier, (array) $container ) )
		{
			//
			// Save object.
			//
			$save = $container[ (string) $theIdentifier ];
			
			//
			// Delete object.
			//
			if( is_array( $container ) )
				unset( $container[ (string) $theIdentifier ] );
			else
				$container->offsetUnset( (string) $theIdentifier );
			
			return $save;															// ==>
		}
		
		return NULL;																// ==>
	
	} // _Delete.

	 

} // class CMongoContainer.


?>
