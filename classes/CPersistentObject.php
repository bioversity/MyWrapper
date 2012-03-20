@@@

This class must accept as container only objects derived from CContainer.

The object will treat as structures (TYPE & DATA) the following data types:

kDATA_TYPE_INT32
kDATA_TYPE_INT64
kDATA_TYPE_STAMP
kDATA_TYPE_BINARY

and the following special types:

kDATA_TYPE_MongoId
kDATA_TYPE_MongoCode
kDATA_TYPE_MongoRegex

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
 * This class declares two main operations: {@link __construct() loading} an object from a
 * container and {@link Commit() storing} the object into a container. These operations
 * consist of a public interface which declares the operation steps and a protected
 * interface which implements the operation.
 *
 * This class recognises two types of persistent object stores:
 *
 * <ul>
 *	<li><i>ArrayObjects</i>: These arrays are considered as the object database.
 *	<li><i>{@link CContainer Container} derived objects</i>: These will be objects derived
 *		from the {@link CContainer CContainer} class which implement native database stores.
 * </ul>
 *
 * In general, derived classes should overload the protected interface and use the public
 * one.
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
	 *		<li><i>ArrayObject</i>: In this case we assume the parameter represents either:
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
	 *	<li><i>{@link _PrepareFind() _PrepareFind}()</i>: This method can be used to
	 *		initialise or normalise the container and identifier; this method will only be
	 *		called if the identifier was provided, in other words, when loading the object
	 *		from a container. In this class this method will ensure that the container is
	 *		an ArrayObject.
	 *	<li><i>{@link _FindObject() _FindObject}()</i>: This method will perform the actual
	 *		retrieval of the object from the container, it is only called if the identifier
	 *		was provided.
	 *	<li><i>{@link _CreateObject() _CreateObject}()</i>: This method will instantiate the
	 *		contents of the object, that is: with the container, if the identifier was not
	 *		provided; or with the result of the object {@link _FindObject() retrieval}.
	 * </ul>
	 *
	 * @param mixed					$theContainer		Persistent container.
	 * @param mixed					$theIdentifier		Object identifier.
	 *
	 * @access public
	 *
	 * @throws CException
	 *
	 * @uses _PrepareFind()
	 * @uses _FindObject()
	 * @uses _CreateObject()
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
	 *		the object is to be stored. It may either be a {@link CContainer CContainer}
	 *		derived instance or an <i>ArrayObject</i> which will be considered the
	 *		persistent object store. This parameter may also be omitted, in which case
	 *		derived classes will have to implement a default collection.
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
	 *	<li><i>{@link _PrepareStore() _PrepareStore}()</i>: This method can be used to
	 *		initialise or check both the container and the identifier.
	 *	<li><i>{@link _StoreObject() _StoreObject}()</i>: This method will perform the
	 *		actual commit.
	 * </ul>
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
			$theIdentifier = $this->_StoreObject( $theContainer, $theIdentifier );
			
			//
			// Finalise.
			//
			$this->_FinishStore( $theContainer, $theIdentifier );
			
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
 *								PUBLIC PERSISTENCE UTILITIES							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Uncommit																		*
	 *==================================================================================*/

	/**
	 * Reset {@link _IsCommitted() committed} status.
	 *
	 * This method can be used to reset the object's {@link kFLAG_STATE_COMMITTED committed}
	 * {@link _IsCommitted() status}, this may be necessary when copying an object from one
	 * container to the other, since the object will be {@link Commit() committed} only if
	 * the {@link kFLAG_STATE_COMMITTED committed} {@link _IsCommitted() status} is not set,
	 * or if the {@link kFLAG_STATE_DIRTY dirsty} {@link _IsDirty() status} is set.
	 *
	 * @access public
	 */
	public function Uncommit()							{	$this->_IsCommitted( FALSE );	}

		

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
	 * The duty of this method is to instantiate an object with the provided data.
	 *
	 * This class expects the content to be either <i>NULL</i>, meaning we are instantiating
	 * an empty object, an <i>array</i> or an <i>ArrayObject</i>, in which case we are
	 * instantiating an object from the provided data; other types should either be handled
	 * by derived classes or raise an {@link kERROR_INVALID_PARAMETER exception}.
	 *
	 * The method returns a boolean where <i>TRUE</i> indicates that the object was
	 * instantiated with data, and <i>FALSE</i> indicating that the object is empty. This
	 * will be used to set the object {@link _IsCommitted() committed}
	 * {@link kFLAG_STATE_COMMITTED flag}.
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
	 * The duty of this method is to store the current object in the provided container with
	 * a key provided by the identifier.
	 *
	 * This class can store objects in ArrayObject containers and in
	 * {@link CContainer CContainer} derived instances, in the latter case the storing will
	 * be delegated to the container.
	 *
	 * The method should expect both parameters to have been previously
	 * {@link _PrepareStore() checked}, its main duty is to perform the actual storage.
	 * In derived classes you should intercept custom containers, or call the parent method.
	 *
	 * <i>Note: the duty of this method is to store only the array part of the object,
	 * properties should be ignored.</i>
	 *
	 * @param reference			   &$theContainer		Object container.
	 * @param reference			   &$theIdentifier		Object identifier.
	 *
	 * @access protected
	 * @return mixed
	 */
	protected function _StoreObject( &$theContainer, &$theIdentifier )
	{
		//
		// Let the container handle it.
		//
		if( $theContainer instanceof CContainer )
			return $theContainer->Commit
				( $this, $theIdentifier, kFLAG_PERSIST_REPLACE );					// ==>
		
		//
		// Handle ArrayObjects.
		//
		if( $theIdentifier === NULL )
		{
			//
			// Append.
			//
			$theContainer[] = (array) $this;
			
			//
			// Copy array.
			//
			$tmp = $theContainer->getArrayCopy();
			
			//
			// Point to last element.
			//
			end( $tmp );
			
			//
			// Get key.
			//
			$theIdentifier = key( $tmp );
		}
		else
			$theContainer[ (string) $theIdentifier ] = (array) $this;
		
		return $theIdentifier;														// ==>
	
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
	 * The method should expect both parameters to have been previously checked: in this
	 * class, the container must be an <i>ArrayObject</i> representing either the actual
	 * container, or a {@link CContainer CContainer} derived instance which will take care
	 * of locating the object within its managed native container.
	 *
	 * @param reference			   &$theContainer		Object container.
	 * @param reference			   &$theIdentifier		Object identifier.
	 *
	 * @access protected
	 * @return mixed
	 */
	protected function _FindObject( &$theContainer, &$theIdentifier )
	{
		//
		// Let container retrieve it.
		//
		if( $theContainer instanceof CContainer )
			return $theContainer->Load( $theIdentifier );							// ==>
		
		//
		// Retrieve it from ArrayObject.
		//
		return $theContainer[ (string) $theIdentifier ];							// ==>
	
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
	 * {@link _FindObject() find} operation are valid.
	 *
	 * The method should first check if the provided container is of the correct type, then
	 * it should ensure that the identifier is valid.
	 *
	 * We know that the identifier cannot be missing, since this method is only called if
	 * the identifier was provided. We should check that the provided container is of the
	 * correct type. In derived classes you should first handle your custom types, then
	 * let the parent method handle other types.
	 *
	 * Any errors should raise an exception.
	 *
	 * In this class we only support <i>ArrayObject</i> containers and the identifier is not
	 * expected to be <i>NULL</i>.
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
					( "Missing container",
					  kERROR_OPTION_MISSING,
					  kMESSAGE_TYPE_ERROR );									// !@! ==>
		elseif( (! $theContainer instanceof CContainer)
			 && (! $theContainer instanceof ArrayObject) )
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
	 * Normalise before a store.
	 *
	 * This method will be called before the {@link _StoreObject() store} operation, its
	 * duty is to prepare the object and the parameters for the
	 * {@link _StoreObject() commit}.
	 *
	 * In this class we ensure that the container is either an ArrayObject or a
	 * {@link CContainer CContainer} derived instance, any other type will raise an
	 * exception. In derived classes you should handle your custom containers or delegate to
	 * the parent.
	 *
	 * In this class we do not check the identifier.
	 *
	 * Any errors should raise an exception.
	 *
	 * @param reference			   &$theContainer		Object container.
	 * @param reference			   &$theIdentifier		Object identifier.
	 *
	 * @access protected
	 *
	 * @throws CException
	 *
	 * @uses _IsInited()
	 *
	 * @see kERROR_INVALID_PARAMETER kERROR_OPTION_MISSING kERROR_UNSUPPORTED
	 */
	protected function _PrepareStore( &$theContainer, &$theIdentifier )
	{
		//
		// Check container.
		//
		if( $theContainer === NULL )
			throw new CException
					( "Missing object container",
					  kERROR_INVALID_PARAMETER,
					  kMESSAGE_TYPE_ERROR );									// !@! ==>
		elseif( (! $theContainer instanceof CContainer)
			 && (! $theContainer instanceof ArrayObject) )
			throw new CException
					( "Unsupported container type",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Container' => $theContainer ) );					// !@! ==>
	
		//
		// Check if inited.
		//
		if( ! $this->_IsInited() )
			throw new CException
					( "Unable to commit object: object not initialised",
					  kERROR_OPTION_MISSING,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Object' => $this ) );								// !@! ==>
	
	} // _PrepareStore.

	 
	/*===================================================================================
	 *	_FinishStore																	*
	 *==================================================================================*/

	/**
	 * Normalise after a store.
	 *
	 * This method will be called after the {@link _StoreObject() store} operation, its
	 * duty is to restore the object and the parameters to the state they were before the
	 * {@link _StoreObject() commit} operation.
	 *
	 * @param reference			   &$theContainer		Object container.
	 * @param reference			   &$theIdentifier		Object identifier.
	 *
	 * @access protected
	 */
	protected function _FinishStore( &$theContainer, &$theIdentifier )					   {}

	 

} // class CPersistentObject.


?>
