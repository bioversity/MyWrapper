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
 * Derived classes share a common identification workflow applied when
 * {@link Commit() saving} a new object:
 *
 * <ul>
 *	<li><i>Object {@link kTAG_ID_NATIVE ID}</i>: If set among the object's offsets, this
 *		will be the value used to uniquely identify the object.
 *	<li><i>Object {@link _id() identifier}</i>: If the object {@link kTAG_ID_NATIVE ID} was
 *		not explicitly set, a protected method, {@link _id() id()}, will be used to get a
 *		string that represents the object's unique identifier. This string will be hashed
 *		and the resulting 16 character binary string will become the object's unique
 *		{@link kTAG_ID_NATIVE identifier}.
 *	<li><i>Mongo default</i>: If the previous method returns <i>NULL</i>, this is the
 *		indication that we want Mongo to assign a default identifier.
 * </ul>
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
	 * When storing derived objects this method makes use of the {@link kTAG_CLASS class}
	 * offset to instantiate the object of the correct type.
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
		$data = $theContainer->findOne( array( kTAG_ID_NATIVE => $theIdentifier ) );
		if( $data === NULL )
			return NULL;															// ==>
		
		//
		// Instantiate specific class.
		//
		if( array_key_exists( kTAG_CLASS, $data ) )
		{
			$class = $data[ kTAG_CLASS ];
			$object = new $class( $theContainer, $theIdentifier );
		}
		
		//
		// Instantiate default class.
		//
		else
			$object = new CMongoObject( $theContainer, $theIdentifier );
		
		//
		// Set committed.
		//
		$object->_IsCommitted( TRUE );
		
		return $object;																// ==>
		
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
	 * This method can be used to return a string value that represents the object's unique
	 * identifier, when {@link Commit() saving} the object for the first time, if this
	 * method returns a value, this will be hashed into a 16 character binary string and set
	 * as the object's {@link kTAG_ID_NATIVE ID}.
	 *
	 * If this method returns <i>NULL</i>, it is assumed that we want MongoDB to assign a
	 * default ID.
	 *
	 * If the object already has an id {@link kTAG_ID_NATIVE offset}, this method will not
	 * be used.
	 *
	 * By default we let the system choose an identifier.
	 *
	 * @access protected
	 * @return string
	 */
	protected function _id()											{	return NULL;	}

		

/*=======================================================================================
 *																						*
 *								PROTECTED PERSISTENCE UTILITIES							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_PrepareStore																	*
	 *==================================================================================*/

	/**
	 * Normalise parameters of a store.
	 *
	 * We {@link CMongoObject::_PrepareStore() overload} this method to perform the
	 * following actions:
	 *
	 * <ul>
	 *	<li><i>Call {@link CMongoObject parent} {@link CMongoObject::_PrepareStore() method}
	 *		</i>: The parent method will check if the container is of the correct type and
	 *		set the object {@link kTAG_ID_NATIVE ID} with the value provided in the
	 *		identifier parameter if not <i>NULL</i>.
	 *	<li><i>Manage {@link kTAG_ID_NATIVE ID}</i>: This class introduces the unique
	 *		identifier {@link _id() concept}. If the identifier parameter is empty, the
	 *		method will check if the {@link _id() _id()} method returns a value: in this
	 *		case it will both set the identifier parameter and the object
	 *		{@link kTAG_ID_NATIVE ID} to the binary hash of that value.
	 *	<li><i>Manage {@link kTAG_CLASS class}</i>: If not already set, this method will
	 *		record the object's class among the offsets.
	 *	<li><i>Manage {@link kTAG_VERSION version}</i>: If set, the method will increment
	 *		its value, if not set, it will initialise it to zero.
	 *		record the object's class among the offsets.
	 * </ul>
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
		// Note that if not NULL parent method will have set the offset.
		//
		if( $theIdentifier === NULL )
		{
			//
			// Get unique identifier.
			//
			if( ($id = $this->_id()) !== NULL )
			{
				//
				// Copy identifier.
				//
				$theIdentifier = new MongoBinData( md5( $id, TRUE ) );
				
				//
				// Set offset.
				//
				$this->offsetSet( kTAG_ID_NATIVE, $theIdentifier );
			}
		}
		
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
