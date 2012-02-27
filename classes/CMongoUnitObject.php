<?php

/**
 * <i>CMongoUnitObject</i> class definition.
 *
 * This file contains the class definition of <b>CMongoUnitObject</b> which represents the
 * ancestor of all unit object classes in this library.
 *
 *	@package	Framework
 *	@subpackage	Persistence
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 21/02/2012
 */

/*=======================================================================================
 *																						*
 *									CMongoUnitObject.php								*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CMongoObject.php" );

/**
 * Unit objects ancestor.
 *
 * A unit object represents a main class, an object that should not be mixed with objects of
 * other classes. In general, if an object is to refer to a unit object, it should use an
 * object reference rather than embedding the object.
 *
 * This class overloads its {@link CMongoObject ancestor} by adding two default offsets:
 *
 * <ul>
 *	<li><i>{@link kTAG_CLASS kTAG_CLASS}</i>: This offset will be automatically filled
 *		with the object's class name when {@link Commit() committing}, this is used to
 *		{@link NewObject() instantiate} the correct class in mixed classes collections.
 *	<li><i>{@link kTAG_VERSION kTAG_VERSION}</i>: This offset will be automatically
 *		incremented each time the object is updated.
 * </ul>
 *
 * These offsets should be managed by this class and should be considered read-only
 * attributes.
 *
 * This class declares a static {@link NewObject() method} that will make use of the
 * {@link kTAG_CLASS class} information to instantiate an object of the correct class.
 *
 * Classes derived from this one should return a string that represents the object's unique
 * identifier in a protected {@link _id() method}: if no {@link kTAG_ID_NATIVE identifier} is
 * provided when {@link Commit() committing} the object, this string will be hashed and the
 * binary string will be used as the object's unique identifier. When instantiating a
 * derived object, if the identifier is a string, it is assumed it represents the value of
 * the {@link _id() _id()} method.
 *
 * @package		Framework
 * @subpackage	Persistence
 */
class CMongoUnitObject extends CMongoObject
{
		

/*=======================================================================================
 *																						*
 *									STATIC INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	NewObject																		*
	 *==================================================================================*/

	/**
	 * Instantiate object.
	 *
	 * This method can be used to instantiate an object from a mixed class data store, it
	 * expects the container to be a MongoCollection and the identifier to be of the correct
	 * type.
	 *
	 * @param mixed					$theContainer		Persistent container.
	 * @param mixed					$theIdentifier		Object identifier.
	 *
	 * @static
	 */
	static function NewObject( $theContainer = NULL, $theIdentifier = NULL )
	{
		//
		// Check container.
		//
		if( ! $theContainer instanceof MongoCollection )
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
		
		//
		// Find object.
		//
		$data = $theContainer->findOne( array( kTAG_ID_NATIVE => $theIdentifier ),
										array( kTAG_CLASS => TRUE ) );
		if( $data === NULL )
			return NULL;															// ==>
		
		//
		// Instantiate object.
		//
		if( array_key_exists( kTAG_CLASS, $data ) )
		{
			$class = $data[ kTAG_CLASS ];
			return new $class( $theContainer, $theIdentifier );						// ==>
		}
		
		return new CMongoObject( $theContainer, $theIdentifier );					// ==>
		
	} // NewObject.

		

/*=======================================================================================
 *																						*
 *							PROTECTED IDENTIFICATION INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_id																				*
	 *==================================================================================*/

	/**
	 * Return the object's unique identifier.
	 *
	 * All derived classes must implement this method, it should return a string which
	 * uniquely identifies the object; this value will be hashed and set as a binary string
	 * in the object's identifier {@link kTAG_ID_NATIVE offset}.
	 *
	 * @access protected
	 * @return string
	 */
	protected function _id()															   {}

		

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
	 * We overload this method to check whether the provided container is a MongoCollection.
	 *
	 * If the identifier was provided as a string, it will be hashed and converted to a
	 * MongoBinData object.
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
		// Call parent method.
		//
		parent::_PrepareFind( $theContainer, $theIdentifier );
		
		//
		// Convert identifier.
		//
		if( ($theIdentifier !== NULL)
		 && (! $theIdentifier instanceof MongoBinData) )
			$theIdentifier = new MongoBinData( md5( (string) $theIdentifier, TRUE ) );
	
	} // _PrepareFind.

	 
	/*===================================================================================
	 *	_PrepareStore																	*
	 *==================================================================================*/

	/**
	 * Normalise parameters of a store.
	 *
	 * We overload this method to handle the {@link kTAG_CLASS kTAG_CLASS}, the
	 * {@link kTAG_VERSION kTAG_VERSION} offsets and set the default identifier
	 * value.
	 *
	 * @param reference			   &$theContainer		Object container.
	 * @param reference			   &$theIdentifier		Object identifier.
	 *
	 * @access protected
	 */
	protected function _PrepareStore( &$theContainer, &$theIdentifier )
	{
		//
		// Call parent method.
		//
		parent::_PrepareStore( $theContainer, $theIdentifier );
		
		//
		// Set identifier.
		//
		if( $theIdentifier === NULL )
			$theIdentifier = new MongoBinData( md5( $this->_id(), TRUE ) );
		
		//
		// Set id.
		//
		if( ! $this->offsetExists( kTAG_ID_NATIVE ) )
			$this->offsetSet( kTAG_ID_NATIVE, $theIdentifier );
		
		//
		// Set class.
		//
		if( ! $this->offsetExists( kTAG_CLASS ) )
			$this->offsetSet( kTAG_CLASS, get_class( $this ) );
		
		//
		// Set version.
		//
		if( $this->offsetExists( kTAG_VERSION ) )
			$this->offsetSet( kTAG_VERSION, $this->offsetGet( kTAG_VERSION ) + 1 );
		else
			$this->offsetSet( kTAG_VERSION, 0 );
	
	} // _PrepareStore.

	 

} // class CMongoUnitObject.


?>
