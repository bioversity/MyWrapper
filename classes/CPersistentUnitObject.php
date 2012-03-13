<?php

/**
 * <i>CPersistentUnitObject</i> class definition.
 *
 * This file contains the class definition of <b>CPersistentUnitObject</b> which extends its
 * {@link CPersistentObject ancestor} to implement an object that has a unique key, a field
 * storing its {@link kTAG_CLASS class} and a {@link kTAG_VERSION version}.
 *
 *	@package	Framework
 *	@subpackage	Persistence
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 12/03/2012
 */

/*=======================================================================================
 *																						*
 *								CPersistentUnitObject.php								*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CPersistentObject.php" );

/**
 * Unit objects ancestor.
 *
 * A unit object is one that has a unique identifier, that is, it can be uniquely identified
 * among a collection of other objects.
 *
 * Instances derived from this class have a series of additional properties and methods that
 * govern how these can be stored and retrieved from collections.
 *
 * The unique identifier or key value is returned by a protected {@link _id() method}, it
 * may return <i>NULL</i> before the object has been {@link Commit() committed} to a
 * container, in which case it means that it is the {@link CContainer container}'s duty to
 * determine that value; once the object has been {@link Commit() stored}, this value will
 * be found in the {@link kTAG_ID_NATIVE kTAG_ID_NATIVE} offset.
 *
 * Objects derived from this class also hold, by default, their class name in an
 * {@link kTAG_CLASS offset}, this is used to {@link NewObject() instantiate} objects of the
 * correct class when retrieving data from a container.
 *
 * This class also features a {@link kTAG_VERSION version} which is an integer, incremented
 * each time the object is {@link Commit() committed}: this is  useful to implement a
 * concurrency control mechanism.
 *
 * Starting from this class we only handle {@link CContainer CContainer} derived instances
 * as containers, other container types will not be supported. This is because
 * {@link CContainer CContainer} derived instances have an
 * {@link CContainer::Encode() encode} and {@link CContainer::Decode() decode} interface for
 * handling special data types, objects derived from this class should store special data
 * types as an array where the {@link kTAG_TYPE kTAG_TYPE} offset indicates the data type
 * and the {@link kTAG_DATA kTAG_DATA} offset contains the normalised data; by normalised
 * we mean, for instance, that binary data is converted to hexadecimal.
 *
 * The specifics of this are managed by the {@link CContainer CContainer} derived classes,
 * so when planning your objects think in advance in what containers you plan to store them.
 *
 * @package		Framework
 * @subpackage	Persistence
 */
class CPersistentUnitObject extends CPersistentObject
{
		

/*=======================================================================================
 *																						*
 *								PUBLIC MANAGEMENT INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	NewObject																		*
	 *==================================================================================*/

	/**
	 * Instantiate object.
	 *
	 * This method can be used to instantiate an object from a mixed class data store, it
	 * expects the container to be a {@link CContainer CContainer} derived instance and the
	 * identifier to be the {@link _id() unique} {@link kTAG_ID_NATIVE identifier} of the
	 * object.
	 *
	 * This method takes advantage of the {@link Commit() stored} {@link kTAG_CLASS class}
	 * name.
	 *
	 * The method will return an object, if the identifier matches and the object has its
	 * {@link kTAG_CLASS class} name; an array if the identifier matches, but the data
	 * doesn't contain a @link kTAG_CLASS class} name reference; <i>NULL</i> if the
	 * identifier didn't match.
	 *
	 * @param mixed					$theContainer		Persistent container.
	 * @param mixed					$theIdentifier		Object identifier.
	 *
	 * @static
	 * @return mixed
	 */
	static function NewObject( $theContainer, $theIdentifier )
	{
		//
		// Check container.
		//
		if( ! $theContainer instanceof CContainer )
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
		// Load object.
		//
		$data = $theContainer->Load( $theIdentifier );
		if( $data !== NULL )
		{
			//
			// Check class.
			//
			if( array_key_exists( kTAG_CLASS, $data ) )
			{
				//
				// Get class name.
				//
				$class = $data[ kTAG_CLASS ];
				
				//
				// Instantiate object.
				//
				$object = new $class( $data );
				
				//
				// Mark as committed.
				//
				$object->_IsCommitted( TRUE );
				
				return $object;														// ==>
			
			} // Has class name.
		
		} // Found object.
		
		return $data;																// =>
		
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
	 * identifier. When {@link Commit() saving} the object for the first time, if this
	 * method returns a value, this will be used as the object's {@link kTAG_ID_NATIVE ID}.
	 *
	 * If this method returns <i>NULL</i>, it is assumed that the
	 * {@link CContainer container} will provide a default unique value.
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
	 *	_PrepareFind																	*
	 *==================================================================================*/

	/**
	 * Normalise parameters of a find.
	 *
	 * The duty of this method is to ensure that the parameters provided to a
	 * {@link _FindObject() find} operation are valid.
	 *
	 * In this class we ensure that the container is derived from
	 * {@link CContainer CContainer}.
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
		// Check container.
		//
		if( ! $theContainer instanceof CContainer )
			throw new CException
					( "Unsupported container type",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Container' => $theContainer ) );					// !@! ==>
	
	} // _PrepareFind.

	 
	/*===================================================================================
	 *	_PrepareStore																	*
	 *==================================================================================*/

	/**
	 * Normalise parameters of a store.
	 *
	 * The duty of this method is to ensure that the parameters provided to the
	 * {@link _StoreObject() store} operation are correct.
	 *
	 * In this class we ensure that the container is a ArrayObject or a
	 * {@link CContainer CContainer} derived instance and we ensure the identifier is filled
	 * in the case it was not provided:
	 *
	 * <ul>
	 *	<li><i>Get {@link kTAG_ID_NATIVE kTAG_ID_NATIVE}</i>: If the object features the
	 *		{@link kTAG_ID_NATIVE kTAG_ID_NATIVE} offset, it will be preferred. This is
	 *		necessary, because we don't want the object identifier to change in time.
	 *	<li><i>Use the {@link _id() :id} method</i>: We use the value returned by the
	 *		{@link _id() _id} protected method, this will be the case when
	 *		{@link Commit() saving} new objects.
	 * </ul>
	 *
	 * We also handle here the other two default offsets:
	 *
	 * <ul>
	 *	<li><i>{@link kTAG_CLASS kTAG_CLASS}</i>: We set this offset with the current
	 *		object's name. Note that we overwrite old values.
	 *	<li><i>{@link kTAG_VERSION kTAG_VERSION}</i>: If not set, we initialise this value
	 *		to zero, if already set, we increment it.
	 * </ul>
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
	protected function _PrepareStore( &$theContainer, &$theIdentifier )
	{
		//
		// Call parent method.
		//
		parent::_PrepareStore( $theContainer, $theIdentifier );
		
		//
		// Check container.
		//
		if( ! $theContainer instanceof CContainer )
			throw new CException
					( "Unsupported container type",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Container' => $theContainer ) );					// !@! ==>
		
		//
		// Handle identifier.
		//
		if( $theIdentifier === NULL )
		{
			//
			// Use existing.
			//
			if( $this->offsetExists( kTAG_ID_NATIVE ) )
				$theIdentifier = $this->offsetGet( kTAG_ID_NATIVE );
			
			//
			// Set with default value.
			//
			else
				$theIdentifier = $this->_id();
		
		} // Omitted identifier.
		
		//
		// Set class.
		//
		$this->offsetSet( kTAG_CLASS, get_class( $this ) );
		
		//
		// Set version.
		//
		$this->offsetSet( kTAG_VERSION, ( $this->offsetExists( kTAG_VERSION ) )
									  ? ($this->offsetGet( kTAG_VERSION ) + 1)
									  : 0 );
	
	} // _PrepareStore.

	 

} // class CPersistentUnitObject.


?>
