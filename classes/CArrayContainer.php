<?php

/**
 * <i>CArrayContainer</i> class definition.
 *
 * This file contains the class definition of <b>CArrayContainer</b> which implements an
 * array or ArrayObject store.
 *
 *	@package	MyWrapper
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
 * instance that uses arrays or ArrayObject objects to store data.
 *
 *	@package	MyWrapper
 *	@subpackage	Persistence
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

	 
	/*===================================================================================
	 *	__toString																		*
	 *==================================================================================*/

	/**
	 * Return container name.
	 *
	 * In this class we first check if the {@link Container() container} features this
	 * method and use it, if this is not the case, we return the data type of the
	 * {@link Container() container}.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses Container()
	 */
	public function __toString()
	{
		//
		// Get native container.
		//
		$container = $this->Container();
		
		//
		// Check array objects.
		//
		if( is_object( $container ) )
		{
			if( method_exists( $container, '__toString' ) )
				return (string) $container;											// ==>
			
			return get_class( $container );											// ==>
		}
		
		return gettype( $container );												// ==>
	
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
	 * We {@link CContainer::Container() overload} this method to ensure that the provided
	 * container is either an array or an ArrayObject.
	 *
	 * @param mixed					$theValue			Persistent container or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @throws {@link CException CException}
	 *
	 * @see kERROR_INVALID_PARAMETER
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
		if( is_array( $theValue )
		 || ($theValue instanceof ArrayObject) )
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
	 * In this class we return <i>NULL</i>.
	 *
	 * @access public
	 * @return mixed
	 */
	public function Database()											{	return NULL;	}

		

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
	 * This class does not handle custom data types, so this method will do nothing.
	 *
	 * @param reference			   &$theElement			Element to encode.
	 *
	 * @access public
	 */
	public function UnserialiseData( &$theElement )										   {}

		

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
	 * We implement this method to handle array or ArrayObject data stores and we ensure
	 * provided options are followed.
	 *
	 * When {@link kFLAG_PERSIST_MODIFY modifying} the contents of the object, we perform
	 * the following checks:
	 *
	 * <ul>
	 *	<li><i>Both matched object and provided object are arrays</i>: this is the default
	 *		scenario. If the provided object element is <i>NULL</i>, the corresponding
	 *		element in the matched object will be deleted; if not <i>NULL</i>, the provided
	 *		object element will replace the eventual existing one or be set in the matched
	 *		object.
	 *	<li><i>Matched object is array and provided object is scalar</i>: the provided
	 *		object will be appended to the matched object.
	 *	<li><i>Matched object is scalar and provided object is array</i>: we transform the
	 *		matched object by appending the existing element to an empty array, and we set
	 *		all the elements of the provided element into the newly created array.
	 *	<li><i>Matched object and provided object are scalar</i>: we transform the matched
	 *		object by appending the existing element to an empty array, and we append the
	 *		provided object to it.
	 * </ul>
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
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Commit modifiers.
	 *
	 * @access protected
	 * @return mixed
	 *
	 * @uses _Container()
	 */
	protected function _Commit( &$theObject, &$theIdentifier, &$theModifiers )
	{
		//
		// Init local storage.
		//
		$id = (string) $theIdentifier;
		$container = & $this->_Container();
		
		//
		// Replace object.
		//
		if( ! (($theModifiers & kFLAG_PERSIST_MODIFY) == kFLAG_PERSIST_MODIFY) )
			$container[ $id ] = $theObject;
		
		//
		// Modify object.
		//
		else
		{
			//
			// Get existing object.
			//
			$object = $container[ $id ];
			
			//
			// Handle matched object is an array.
			//
			if( is_array( $object )
			 || ($object instanceof ArrayObject) )
			{
				//
				// Provided object is array.
				//
				if( is_array( $theObject )
				 || ($theObject instanceof ArrayObject) )
				{
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
				
				} // Provided object is array.
				
				//
				// Provided scalar.
				//
				else
					$object[] = $theObject;
				
				//
				// Update.
				//
				$container[ $id ] = $object;
			
			} // Matched object is array.
			
			//
			// Matched object is scalar.
			//
			else
			{
				//
				// Transform into array.
				//
				$object = array( $object );
				
				//
				// Provided object is array.
				//
				if( is_array( $theObject )
				 || ($theObject instanceof ArrayObject) )
				{
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
				
				} // Provided object is array.
				
				//
				// Provided scalar.
				//
				else
					$object[] = $theObject;
				
				//
				// Update.
				//
				$container[ $id ] = $object;
			
			} // Matched object is scalar.
			
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
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Load modifiers.
	 *
	 * @access protected
	 * @return mixed
	 *
	 * @uses _Container()
	 */
	protected function _Load( &$theIdentifier, &$theModifiers )
	{
		//
		// Init local storage.
		//
		$id = (string) $theIdentifier;
		$container = & $this->_Container();

		//
		// Return match.
		//
		if( array_key_exists( $id, (array) $container ) )
			return $container[ $id ];												// ==>
		
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
	 * @param bitfield				$theModifiers		Delete modifiers.
	 *
	 * @access protected
	 * @return mixed
	 *
	 * @uses _Container()
	 */
	protected function _Delete( &$theIdentifier, &$theModifiers )
	{
		//
		// Init local storage.
		//
		$id = (string) $theIdentifier;
		$container = & $this->_Container();

		//
		// Delete match.
		//
		if( array_key_exists( $id, (array) $container ) )
		{
			//
			// Save object.
			//
			$save = $container[ $id ];
			
			//
			// Delete object.
			//
			if( is_array( $container ) )
				unset( $container[ $id ] );
			else
				$container->offsetUnset( $id );
			
			return $save;															// ==>
		}
		
		return NULL;																// ==>
	
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
	 * Normalise before a store.
	 *
	 * We {@link CContainer::_PrepareCommit() overload} this method to perform the following
	 * operations:
	 *
	 * <ul>
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
	 *	<li>Check for object in container if required.
	 *	<li>Initialise identifier if required.
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
		// Call parent method.
		//
		parent::_PrepareCommit( $theObject, $theIdentifier, $theModifiers );

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
	
	} // _PrepareCommit.

	 

} // class CArrayContainer.


?>
