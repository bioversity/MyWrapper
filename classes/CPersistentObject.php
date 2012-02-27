<?php

/**
 * <i>CPersistentObject</i> class definition.
 *
 * This file contains the class definition of <b>CPersistentObject</b> which represents the
 * ancestor of all persistent classes in this library.
 *
 *	@package	Framework
 *	@subpackage	Persistence
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 07/04/2009
 *				2.00 11/03/2011
 *				3.00 14/02/2012
 */

/*=======================================================================================
 *																						*
 *									CPersistentObject.php								*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CStatusObject.php" );

/**
 * Persistent objects ancestor.
 *
 * This class is the ancestor of all persistent classes in this library, it implements the
 * common interfaces that concrete persistent instances will implement to manage persistent
 * objects.
 *
 * Classes derived from this one must share the same constructor which accepts two
 * parameters: the collection in which the object is stored and the key to it.
 *
 * The class implements a public interface which sets the operation standards, and a
 * protected interface that derived classes may overload to implement specialised data 
 * tores.
 *
 * <ul>
 *	<li>The {@link __construct() constructor} is used to instantiate either empty or
 *		initialised objects, or to load an object from the container in which it resides.
 *	<li>{Commit() Commit} is used to store objects in containers.
 *	<li>This class does not feature a <i>Delete</i> method, because this is the
 *		responsibility of the collections; derived classes that implement a consistent
 *		method to get an object's unique identifier will implement this functionality.
 * </ul>
 *
 * @package		Framework
 * @subpackage	Persistence
 */
class CPersistentObject extends CStatusObject
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
	 * The constructor can be used to instantiate an empty object, instantiate an object
	 * from some content, or load an object from a container.
	 *
	 * The parameters to this method are:
	 *
	 * <ul>
	 *	<li><b>$theContainer</b>: This parameter represents either the contents of the
	 *		object, if the next parameter is missing, or the persistent store in which the
	 *		object resides. If missing, we assume we want to create an empty object.
	 *	 <ul>
	 *		<li><i>NULL</i>: In this case it is assumed you want to instantiate an empty
	 *			object, the next parameter will be ignored.
	 *		<li><i>Array</i> or <i>ArrayObject</i>: In this case we assume the parameter
	 *			represents either:
	 *		 <ul>
	 *			<li>the object <i>contents</i>: if the next parameter is missing, or
	 *			<li>the object <i>container</i>: if the next parameter is provided.
	 *		 </ul>
	 *		<li><i>Other</i>: Any other type should be handled by derived classes, or passed
	 *			here where we raise an {@link kERROR_UNSUPPORTED exception}.
	 *	 </ul>
	 *	<li><b>$theIdentifier</b>: This parameter represents the key or query that will be
	 *		used to locate the object in the container provided in the first parameter.
	 *	 <ul>
	 *		<li><i>NULL</i>: This indicates that the first parameter represents the object
	 *			<i>contents</i>.
	 *		<li><i>other</i>: Any other type is considered as a key or query used to locate
	 *			the object in the container provided in the first parameter.
	 *	 </ul>
	 * </ul>
	 *
	 * Depending on the results of this method we set the {@link _IsCommitted() committed}
	 * {@link kFLAG_STATE_COMMITTED flag} as follows:
	 *
	 * <ul>
	 *	<li><i>Empty object</i>: By omitting the first parameter we indicate that we want to
	 *		start from scratch, the object will not have its
	 *		{@link _IsCommitted() committed} {@link kFLAG_STATE_COMMITTED flag} set.
	 *	<li><i>Filled object</i>: In this case we provide in the <i>$theContainer</i>
	 *		parameter the object's <i>contents</i>. In this case we do not consider the
	 *		object as persistent, so the object will not have its
	 *		{@link _IsCommitted() committed} {@link kFLAG_STATE_COMMITTED flag} set.
	 *	<li><i>Selected object</i>: In this case we search the <i>$theContainer</i> with
	 *		the provided <i>$theIdentifier</i> as a key or query, and if:
	 *	 <ul>
	 *		<li><i>Found</i>: We set the {@link _IsCommitted() committed}
	 *			{@link kFLAG_STATE_COMMITTED flag} on.
	 *		<li><i>Not found</i>: We instantiate an empty object and ignore the
	 *			{@link _IsCommitted() committed} {@link kFLAG_STATE_COMMITTED flag}.
	 *	 </ul>
	 * </ul>
	 *
	 * The {@link _IsInited() inited} {@link kFLAG_STATE_INITED flag} is the responsibility
	 * of derived classes, here we do not manage it.
	 *
	 * In derived classes you should not overload this method, instead, you should overload
	 * the protected interface:
	 *
	 * <ul>
	 *	<li><i>{@link _CheckContainer() _CheckContainer}()</i>: This method can be used to
	 *		verify the container and normalise the identifier, it will only be called when
	 *		loading the object from a container.
	 *	<li><i>{@link _CheckIdentifier() _CheckIdentifier}()</i>: This method can be used to
	 *		verify the identifier, it will only be called when loading the object from a
	 *		container.
	 *	<li><i>{@link _FindObject() _FindObject}()</i>: This method will retrieve the object
	 *		from a container.
	 *	<li><i>{@link _CreateObject() _CreateObject}()</i>: This method is responsible for
	 *		instantiating the object from its contents.
	 * </ul>
	 *
	 * @param mixed					$theContainer		Persistent container.
	 * @param mixed					$theIdentifier		Object identifier.
	 *
	 * @access public
	 *
	 * @throws CException
	 *
	 * @uses _CheckContainer()
	 * @uses _CheckIdentifier()
	 * @uses _CreateObject()
	 * @uses _FindObject()
	 * @uses _IsCommitted()
	 */
	public function __construct( $theContainer = NULL, $theIdentifier = NULL )
	{
		//
		// Provided container.
		//
		if( $theIdentifier !== NULL )
		{
			//
			// Check parameters.
			//
			$this->_PrepareFind( $theContainer, $theIdentifier );
			
			//
			// Find object in container, create it and set status.
			//
			$this->_IsCommitted(
				$this->_CreateObject(
					$this->_FindObject( $theContainer, $theIdentifier ) ) );
		}
		
		//
		// Provided content.
		//
		else
			$this->_CreateObject( $theContainer );
		
	} // Constructor.

		

/*=======================================================================================
 *																						*
 *								PUBLIC PERSISTENCE INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Commit																			*
	 *==================================================================================*/

	/**
	 * Commit the object into a container.
	 *
	 * This method should be used to commit the object to a container, the method accepts
	 * two parameters:
	 *
	 * <ul>
	 *	<li><b>$theContainer</b>: This parameter represents the <i>container</i> in which
	 *		the object is to be stored.
	 *	<li><b>$theIdentifier</b>: This parameter represents the unique identifier of the
	 *		object within the container provided in the first parameter.
	 * </ul>
	 *
	 * The method will only operate if either the {@link _IsCommitted() committed}
	 * {@link kFLAG_STATE_COMMITTED flag} is <i>not</i> set, or the
	 * {@link _IsDirty() dirty} {@link kFLAG_STATE_DIRTY flag} is <i>set</i>, if none of
	 * these two conditions are satisfied, the method will just exit.
	 *
	 * The method will by default <i>set</i> the {@link _IsCommitted() committed}
	 * {@link kFLAG_STATE_COMMITTED flag} and <i>reset</i> the {@link _IsDirty() dirty}
	 * {@link kFLAG_STATE_DIRTY flag}.
	 *
	 * This method should raise an exception if any error occurs.
	 *
	 * The method should return the unique identifier of the object or <i>NULL</i> if the
	 * operation was not performed.
	 *
	 * In derived classes you should not overload this method, instead, you should overload
	 * the protected interface:
	 *
	 * <ul>
	 *	<li><i>{@link _CheckContainer() _CheckContainer}()</i>: This method can be used to
	 *		verify the container and normalise the identifier.
	 *	<li><i>{@link _CheckIdentifier() _CheckIdentifier}()</i>: This method can be used to
	 *		verify or initialise the identifier.
	 *	<li><i>{@link _StoreObject() _StoreObject}()</i>: This method will save the object
	 *		in the container.
	 * </ul>
	 *
	 * In this class we support <i>array</i> and <i>ArrayObject</i> containers.
	 *
	 * @param mixed					$theContainer		Persistent container.
	 * @param mixed					$theIdentifier		Object identifier.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @throws CException
	 *
	 * @uses _IsDirty()
	 * @uses _IsCommitted()
	 * @uses _CheckContainer()
	 * @uses _CheckIdentifier()
	 * @uses _StoreObject()
	 */
	public function Commit( $theContainer = NULL, $theIdentifier = NULL )
	{
		//
		// Check if we need to do it.
		//
		if( $this->_IsDirty()
		 || (! $this->_IsCommitted()) )
		{
			//
			// Check parameters.
			//
			$this->_PrepareStore( $theContainer, $theIdentifier );
			
			//
			// Store object.
			//
			$this->_StoreObject( $theContainer, $theIdentifier );
			
			//
			// Set status.
			//
			$this->_IsCommitted( TRUE );
			$this->_IsDirty( FALSE );
			
			return $theIdentifier;													// ==>
		}
		
		return NULL;																// ==>
		
	} // Commit.

		

/*=======================================================================================
 *																						*
 *							PROTECTED STATE MANAGEMENT INTERFACE						*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_IsCommitted																	*
	 *==================================================================================*/

	/**
	 * Manage committed status.
	 *
	 * This method can be used to get or set the object's committed state.
	 *
	 * A committed object is one that has either been loaded from a container or committed
	 * to a container. This state indicates that the object is persistent. This state,
	 * combined with the {@link _IsDirty() dirty} status, can determine if an object needs
	 * to be committed in a container or not.
	 *
	 * The method features a single parameter:
	 *
	 * <ul>
	 *	<li><i>NULL</i>: The method will return <i>TRUE</i> if the object is committed, or
	 *		<i>FALSE</i> if the object is not committed.
	 *	<li><i>TRUE</i>: The method will set the object to committed.
	 *	<li><i>FALSE</i>: The method will reset the object's committed state.
	 * </ul>
	 *
	 * In all cases the method will return the state <i>after</i> it was eventually
	 * modified.
	 *
	 * @param mixed					$theState			TRUE, FALSE or NULL.
	 *
	 * @access protected
	 * @return boolean
	 *
	 * @uses _ManageBitField()
	 *
	 * @see kFLAG_STATE_COMMITTED
	 */
	protected function _IsCommitted( $theState = NULL )
	{
		return $this->_ManageBitField( $this->mStatus,
									   kFLAG_STATE_COMMITTED,
									   $theState );									// ==>
	
	} // _IsCommitted.

		

/*=======================================================================================
 *																						*
 *								PROTECTED PERSISTENCE INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_CreateObject																	*
	 *==================================================================================*/

	/**
	 * Create object.
	 *
	 * The duty of this object is to instantiate an object with the data it is provided.
	 *
	 * This class expects the content to be either an <i>array</i> or an <i>ArrayObject</i>,
	 * other types will raise an {@link kERROR_INVALID_PARAMETER exception}. Derived classes
	 * should only overload this method if necessary.
	 *
	 * The method should return a boolean where <i>TRUE</i> indicates that the object was
	 * instantiated with data, and <i>FALSE</i> if the object is empty.
	 *
	 * The parameter is provided as a reference.
	 *
	 * @param reference			   &$theContent			Object data content.
	 *
	 * @access protected
	 * @return boolean
	 *
	 * @throws CException
	 *
	 * @see kERROR_UNSUPPORTED
	 */
	protected function _CreateObject( &$theContent )
	{
		//
		// Create empty object.
		//
		if( $theContent === NULL )
		{
			//
			// Instantiate object.
			//
			parent::__construct();
			
			return FALSE;															// ==>
		}
		
		//
		// Check data contents.
		//
		if( is_array( $theContent )
		 || ($theContent instanceof ArrayObject) )
		{
			//
			// Instantiate object.
			//
			parent::__construct( (array) $theContent );
			
			return count( $theContent );											// ==>
		}
		
		throw new CException
				( "Unsupported content type",
				  kERROR_UNSUPPORTED,
				  kMESSAGE_TYPE_ERROR,
				  array( 'Content' => $theContent ) );							// !@! ==>
	
	} // _CreateObject.

	 
	/*===================================================================================
	 *	_StoreObject																	*
	 *==================================================================================*/

	/**
	 * Store object in container.
	 *
	 * The duty of this method is to store the current object in the provided container
	 * identified by the provided identifier.
	 *
	 * Both the {@link _CheckContainer() container} and the
	 * {@link _CheckIdentifier() identifier} must have been checked beforehand, this means
	 * that this method expects correct parameters.
	 *
	 * <i>Note: the duty of this method is to store only the array part of the object,
	 * properties should be ignored.</i>
	 *
	 * @param reference			   &$theContainer		Object container.
	 * @param reference			   &$theIdentifier		Object identifier.
	 *
	 * @access protected
	 */
	protected function _StoreObject( &$theContainer, &$theIdentifier )
	{
		$theContainer[ (string) $theIdentifier ] = (array) $this;
	
	} // _StoreObject.

	 
	/*===================================================================================
	 *	_FindObject																		*
	 *==================================================================================*/

	/**
	 * Find object.
	 *
	 * The duty of this method is to locate the object identified by <i>$theIdentifier</i>
	 * in the container <i>$theContainer</i> and return its contents or <i>NULL</i> if not
	 * found.
	 *
	 * In derived classes you should overload this method to handle the specific data store
	 * you will be supporting.
	 *
	 * Both the {@link _CheckContainer() container} and the
	 * {@link _CheckIdentifier() identifier} must have been checked beforehand, this means
	 * that this method expects correct parameters.
	 *
	 * @param reference			   &$theContainer		Object container.
	 * @param reference			   &$theIdentifier		Object identifier.
	 *
	 * @access protected
	 * @return mixed
	 */
	protected function _FindObject( &$theContainer, &$theIdentifier )
	{
		return @$theContainer[ (string) $theIdentifier ];							// ==>
	
	} // _FindObject.

		

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
	 * {@link _FindObject() find} operation are ready.
	 *
	 * The method should first check if the provided container is of the correct type, then
	 * it should ensure that the identifier is valid.
	 *
	 * Any errors should raise an exception.
	 *
	 * In this class we only support <i>arrays</i> and <i>ArrayObject</i> containers and the
	 * identifier must not be empty.
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
		// Check container.
		//
		if( $theContainer === NULL )
			throw new CException
					( "Missing object container",
					  kERROR_OPTION_MISSING,
					  kMESSAGE_TYPE_ERROR );									// !@! ==>
		elseif( (! is_array( $theContainer ))
			 && (! $theContainer instanceof ArrayObject) )
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
	
	} // _PrepareFind.

	 
	/*===================================================================================
	 *	_PrepareStore																	*
	 *==================================================================================*/

	/**
	 * Normalise parameters of a store.
	 *
	 * The duty of this method is to ensure that the parameters provided to a
	 * {@link _StoreObject() store} operation are ready.
	 *
	 * The method should first check if the provided container is of the correct type, then
	 * it should ensure that the identifier is valid or determine the identifier from the
	 * object's contents.
	 *
	 * Any errors should raise an exception.
	 *
	 * In this class we only support <i>arrays</i> and <i>ArrayObject</i> containers, if
	 * the identifier is missing we assume we want to append the object in the container.
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
		// Check container.
		//
		if( $theContainer === NULL )
			throw new CException
					( "Missing object container",
					  kERROR_OPTION_MISSING,
					  kMESSAGE_TYPE_ERROR );									// !@! ==>
		elseif( (! is_array( $theContainer ))
			 && (! $theContainer instanceof ArrayObject) )
			throw new CException
					( "Unsupported container type",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Container' => $theContainer ) );					// !@! ==>
		
		//
		// Set identifier.
		//
		if( $theIdentifier === NULL )
			$theIdentifier = count( $theContainer );
	
	} // _PrepareStore.

	 

} // class CPersistentObject.


?>
