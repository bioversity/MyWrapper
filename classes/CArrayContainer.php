<?php

/**
 * <i>CArrayContainer</i> class definition.
 *
 * This file contains the class definition of <b>CArrayContainer</b> which implements an
 * array or ArrayObject store.
 *
 *	@package	Framework
 *	@subpackage	Persistence
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 07/03/2012
 */

/*=======================================================================================
 *																						*
 *									CArrayContainer.php									*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CContainer.php" );

/**
 * Array persistent data store.
 *
 * This class extends its {@link CContainer ancestor} to implement a concrete object store
 * instance consisting of an array or ArrayObject. All other concrete instances of
 * persistent object stores derive from this class, so that they might fall back onto a
 * default working implementation.
 *
 * @package		Framework
 * @subpackage	Persistence
 */
class CArrayContainer extends CContainer
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
	 * We {@link CContainer::__construct() overload} this method to provide a default
	 * native container, which, in this case, will be an empty array.
	 *
	 * @param mixed					$theContainer		Native persistent container.
	 *
	 * @access public
	 */
	public function __construct( $theContainer = NULL )
	{
		//
		// Set default container.
		//
		if( $theContainer === NULL )
			$theContainer = Array();
		
		//
		// Call parent constructor.
		//
		parent::__construct( $theContainer );
		
	} // Constructor.

		

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
	 * We {@link CContainer::Container() overload} this method to ensure that the provided
	 * container is either an array or an ArrayObject.
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
		if( ($theValue !== NULL)						// No retrieve,
		 && ($theValue !== FALSE)						// no delete,
		 && (! is_array( $theValue ))					// not an array,
		 && (! $theValue instanceof ArrayObject) )		// not an ArrayObject:
			throw new CException
				( "Invalid container type",
				  kERROR_INVALID_PARAMETER,
				  kMESSAGE_TYPE_ERROR,
				  array( 'Container' => $theValue ) );							// !@! ==>
		
		return $this->_ManageMember( $this->mContainer, $theValue, $getOld );		// ==>

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
	 * We implement this method to handle array or ArrayObject stores and we ensure provided
	 * options are followed.
	 *
	 * By default the object must be an array or ArrayObject, any other type will raise an
	 * {@link kERROR_INVALID_PARAMETER exception}.
	 *
	 * The provided identifier will be cast to a string. If it is <i>NULL</i>, it means that
	 * the object is to be appended in the container and the method assumes the
	 * {@link Commit() caller} has determined that it is an
	 * {@link kFLAG_PERSIST_INSERT insert} operation.
	 *
	 * Although the {@link Commit() caller} accepts the {@link kFLAG_PERSIST_DELETE delete}
	 * option, in this class we do not, so we shall raise an
	 * {@link kERROR_INVALID_PARAMETER exception}.
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
		// Get container reference.
		//
		$container = & $this->_Container();
		if( $container === NULL )
			throw new CException
				( "Missing native container",
				  kERROR_INVALID_STATE,
				  kMESSAGE_TYPE_ERROR );										// !@! ==>
		
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
	 * @param bitfield				$theModifiers		Load modifiers.
	 *
	 * @access protected
	 * @return mixed
	 */
	protected function _Load( $theIdentifier, $theModifiers )
	{
		//
		// Get container reference.
		//
		$container = & $this->_Container();
		if( $container === NULL )
			throw new CException
				( "Missing native container",
				  kERROR_INVALID_STATE,
				  kMESSAGE_TYPE_ERROR );										// !@! ==>
		
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
	 * @param bitfield				$theModifiers		Load modifiers.
	 *
	 * @access protected
	 * @return mixed
	 */
	protected function _Delete( $theIdentifier, $theModifiers )
	{
		//
		// Get container reference.
		//
		$container = & $this->_Container();
		if( $container === NULL )
			throw new CException
				( "Missing native container",
				  kERROR_INVALID_STATE,
				  kMESSAGE_TYPE_ERROR );										// !@! ==>
		
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

	 

} // class CArrayContainer.


?>
