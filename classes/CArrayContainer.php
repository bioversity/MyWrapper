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
		 && (! is_array( $theContainer ))				// not an array,
		 && (! $theContainer instanceof ArrayObject) )	// not an ArrayObject:
			throw new CException
				( "Invalid container type",
				  kERROR_INVALID_PARAMETER,
				  kMESSAGE_TYPE_ERROR,
				  array( 'Container' => $theValue ) );							// !@! ==>
		
		return parent::Container( $theValue, $getOld );								// ==>
			

	} // Container.

		

/*=======================================================================================
 *																						*
 *								PROTECTED MANAGEMENT INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_Load																			*
	 *==================================================================================*/

	/**
	 * Load object.
	 *
	 * We implement this method to handle array or ArrayObject stores.
	 *
	 * The method will convert the identifier to a string.
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
		$container = &$this->_Container();
		
		//
		// Return match.
		//
		if( array_key_exists( (string) $theIdentifier, $container ) )
			return $container[ (string) $theIdentifier ];							// ==>
		
		return NULL;																// ==>
	
	} // _Load.

	 
	/*===================================================================================
	 *	_Delete																			*
	 *==================================================================================*/

	/**
	 * Load object.
	 *
	 * We implement this method to handle array or ArrayObject stores.
	 *
	 * The method will convert the identifier to a string.
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
		$container = &$this->_Container();
		
		//
		// Delete match.
		//
		if( array_key_exists( (string) $theIdentifier, $container ) )
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
