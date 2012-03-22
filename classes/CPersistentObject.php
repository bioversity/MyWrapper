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
 * This <i>abstract</i> class is the ancestor of all persistent objects in this library, it
 * implements the common interfaces and workflow that persistent objects will use to be
 * {@link __construct() instantiated}, {@link __construct() retrieved} or
 * {@link Commit() stored} in object {@link CContainer containers}.
 *
 * This class interacts closely with {@link CContainer CContainer} derived classes so that
 * concrete classes derived from this one need only to be concerned with their specific
 * functionality, while persistent issues are handled foth by this class and by the
 * concrete classes derived from {@link CContainer CContainer}.
 *
 * The class declares three main persistence operations whose workflow is as follows:
 *
 * <ul>
 *	<li><i>{@link __construct() Create}</i>: The {@link __construct() constructor} can
 *		instantiate an object from data, as its {@link CArrayObject ancestor}, a protected
 *		{@link _Create() method} may be overloaded by derived classes to implement
 *		specific functionality.
 *	<li><i>{@link __construct() Retrieve}</i>: The {@link __construct() constructor} is
 *		also used to retrieve objects stored in a {@link CContainer container}, this
 *		operation follows these steps:
 *	 <ul>
 *		<li><i>{@link _PrepareFind() Prepare}</i>: This step is used to check parameters and
 *			prepare the resources needed to locate and retrieve the object.
 *		<li><i>{@link _Load() Find}</i>: In this step the object will be searched for
 *			and retrieved from the {@link CContainer container}.
 *	 </ul>
 *	<li><i>{@link Commit() Store}</i>: This operation will store the object in a
 *		{@link CContainer container}, the followed steps are:
 *	 <ul>
 *		<li><i>{@link _PrepareStore() Prepare}</i>: This step is used to check parameters
 *			and prepare the resources needed to save the object.
 *		<li><i>{@link _Commit() Store}</i>: This operation will perform the actual
 *			storage, this step should be delegated to the {@link CContainer container}.
 *	 </ul>
 * </ul>
 *
 * This class implements the default behaviour, in derived classes you should overload these
 * methods if necessary, without changing the public interface.
 *
 * This class extends its {@link CStatusObject ancestor} behaviour to record persistence
 * status and accepts only instances derived from {@link CContainer CContainer} as
 * containers.
 *
 * @package		Framework
 * @subpackage	Persistence
 */
abstract class CPersistentObject extends CStatusObject
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
	 *	<li><b>$theContainer</b>: This parameter represents either the <i>contents</i> of
	 *		the object, if the next parameter is missing, or the
	 *		<i>{@link CContainer container}</i> in which the object resides. If missing, we
	 *		assume we want to create an empty object.
	 *	 <ul>
	 *		<li><i>NULL</i>: In this case it is assumed you want to instantiate an empty
	 *			object and the next parameter will be ignored.
	 *		<li><i>Array</i> or <i>ArrayObject</i>: In this case we assume the parameter
	 *			represents either the object contents, if the next parameter is <i>NULL</i>,
	 *			or the {@link CContainer container} in which the object is
	 *			{@link Commit() stored}, in which case we interpret the next parameter to be
	 *			the object's identifier.
	 *		<li><i>Other</i>: Any other type will raise an
	 *			{@link kERROR_UNSUPPORTED exception}, or it should be handled by derived
	 *			classes.
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
	 * status {@link kFLAG_STATE_COMMITTED flag} as follows:
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
	 * The {@link _IsInited() inited} status {@link kFLAG_STATE_INITED flag} is not set by
	 * default, this will prevent the object from being {@link Commit() stored} and it is
	 * the responsibility of derived concrete instances to manage this status.
	 *
	 * This method takes advantage of a protected interface which should be overloaded by
	 * derived classes instead of overloading this method:
	 *
	 * <ul>
	 *	<li><i>Retrieve object</i>: If the second parameter was provided, it implies that
	 *		the object is to be retrieved from the first parameter which represents the
	 *		{@link CContainer container} in which the object contents are stored.
	 *	 <ul>
	 *		<li><i>{@link _PrepareFind() _PrepareFind}()</i>: This method should check and
	 *			normalise the container and identifier. In this class we ensure that the
	 *			container is derived from {@link CContainer CContainer}.
	 *		<li><i>{@link _Load() _Load}()</i>: This method will delegate the
	 *			{@link CContainer container} the responsibility of locating and retrieving
	 *			the object using the provided identifier.
	 *	 </ul>
	 *	<li><i>{@link _Create() _Create}()</i>: This method should instantiate
	 *		the object from the contents returned by {@link _Load() _Load} or
	 *		by the data provided in the first parameter in the case the second parameter
	 *		was omitted.
	 * </ul>
	 *
	 * Derived classes should overload the above interface rather than overloading this
	 * method and should do so only if necessary, because this workflow should be sufficient
	 * for persisting objects.
	 *
	 * @param mixed					$theContainer		Persistent container.
	 * @param mixed					$theIdentifier		Object identifier.
	 *
	 * @access public
	 *
	 * @uses _PrepareFind()
	 * @uses _Load()
	 * @uses _Create()
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
			// Prepare.
			//
			$this->_PrepareFind( $theContainer, $theIdentifier );
			
			//
			// Find object in container, create it and set status.
			//
			$this->_IsCommitted(
				$this->_Create(
					$this->_Load( $theContainer, $theIdentifier ) ) );
			
			//
			// Finish.
			//
			$this->_FinishFind( $theContainer, $theIdentifier );
		}
		
		//
		// Provided content.
		//
		else
			$this->_Create( $theContainer );
		
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
	 *		the object is to be stored. By default we enforce concrete instances of
	 *		{@link CContainer CContainer}, derived classes may overload the protected
	 *		interface to initialise this value.
	 *	<li><b>$theIdentifier</b>: This parameter represents the value by which the current
	 *		object will be uniquely identified in the first parameter. By default this
	 *		value is required, derived classes may overload the protected interface to
	 *		initialise this value.
	 *	<li><b>$theModifiers</b>: This parameter represents the commit operation options, by
	 *		default we assume you want to {@link kFLAG_PERSIST_REPLACE replace} an object,
	 *		but you may change this value if you want to perform specific commit operations:
	 *	 <ul>
	 *		<li><i>{@link kFLAG_PERSIST_INSERT kFLAG_PERSIST_INSERT}</i>: The provided
	 *			object will be inserted in the container, it is assumed that no other
	 *			element in the container shares the same identifier, in that case the
	 *			container {@link CContainer::Commit() method} must raise an
	 *			{@link kERROR_DUPLICATE exception}.
	 *		<li><i>{@link kFLAG_PERSIST_UPDATE kFLAG_PERSIST_UPDATE}</i>: The provided
	 *			object will replace the existing object. In this case the method expects
	 *			the container to have an entry with the same key as the provided identifier,
	 *			if this is not the case the container {@link CContainer::Commit() method}
	 *			must raise an {@link kERROR_NOT_FOUND exception}. With this option it is
	 *			assumed that the provided object's attributes will replace all the existing
	 *			object's ones.
	 *		<li><i>{@link kFLAG_PERSIST_MODIFY kFLAG_PERSIST_MODIFY}</i>: The provided
	 *			object is assumed to contain a subset of an existing object's attributes,
	 *			these provided attributes will be appended or replace the existing ones.
	 *			In this case the method expects the container to have an entry with the
	 *			same key as the provided identifier, if this is not the case the method must
	 *			raise an {@link kERROR_NOT_FOUND exception}.
	 *		<li><i>{@link kFLAG_PERSIST_REPLACE kFLAG_PERSIST_REPLACE}</i>: The provided
	 *			object will be {@link kFLAG_PERSIST_INSERT inserted}, if the identifier
	 *			doesn't match any container elements, or it will
	 *			{@link kFLAG_PERSIST_UPDATE replace} the existing object. As with
	 *			{@link kFLAG_PERSIST_UPDATE update}, it is assumed that the provided
	 *			object's attributes will replace all the existing object's ones.
	 *		<li><i>{@link kFLAG_PERSIST_DELETE kFLAG_PERSIST_DELETE}</i>: This option
	 *			assumes you want to remove the object from the container, although the
	 *			container features a specific {@link CContainer::Delete() method} for this
	 *			purpose, this option may be used to implement a <i>deleted state</i>, rather
	 *			than actually removing the object from the container.
	 *	 </ul>
	 * </ul>
	 *
	 * The method will only operate if either the {@link _IsCommitted() committed}
	 * {@link kFLAG_STATE_COMMITTED flag} is <i>not set</i>, or the
	 * {@link _IsDirty() dirty} {@link kFLAG_STATE_DIRTY flag} is <i>set</i>, if none of
	 * these two conditions are satisfied, the method will do nothing.
	 *
	 * The method will by default <i>set</i> the {@link _IsCommitted() committed}
	 * {@link kFLAG_STATE_COMMITTED flag} and <i>reset</i> the {@link _IsDirty() dirty}
	 * {@link kFLAG_STATE_DIRTY flag} if the operation was successful.
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
	 *		initialise or check the parameters and resources.
	 *	<li><i>{@link _Commit() _Commit}()</i>: This method will perform the
	 *		actual commit.
	 *	<li><i>{@link _FinishStore() _FinishStore}()</i>: This method can be used to perform
	 *		eventual post-flight adjustments.
	 * </ul>
	 *
	 * @param mixed					$theContainer		Persistent container.
	 * @param mixed					$theIdentifier		Object identifier.
	 * @param bitfield				$theModifiers		Commit modifiers.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses _IsDirty()
	 * @uses _IsCommitted()
	 * @uses _PrepareStore()
	 * @uses _Commit()
	 */
	public function Commit( $theContainer = NULL,
							$theIdentifier = NULL,
							$theModifiers = kFLAG_PERSIST_REPLACE )
	{
		//
		// Check if we need to do it.
		//
		if( $this->_IsDirty()
		 || (! $this->_IsCommitted()) )
		{
			//
			// Prepare.
			//
			$this->_PrepareStore( $theContainer, $theIdentifier, $theModifiers );
			
			//
			// Store object.
			//
			$theIdentifier
				= $this->_Commit
					( $theContainer, $theIdentifier, $theModifiers );
			
			//
			// Finish.
			//
			$this->_FinishStore( $theContainer, $theIdentifier, $theModifiers );
			
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
	 * or if the {@link kFLAG_STATE_DIRTY dirty} {@link _IsDirty() status} is set.
	 *
	 * @access public
	 *
	 * @uses _IsCommitted()
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
	 *	_Create																			*
	 *==================================================================================*/

	/**
	 * Create object.
	 *
	 * The duty of this method is to instantiate an object with the provided data.
	 *
	 * The method expects one parameter which is passed by reference:
	 *
	 * <ul>
	 *	<li><b>&$theContent</b>: This parameter represents the object contents:
	 *	 <ul>
	 *		<li><i>NULL</i>: The method will instantiate an empty object.
	 *		<li><i>array</i> or an <i>ArrayObject</i>: The method will instantiate the
	 *			object with the contents of the provided parameter.
	 *		<li><i>other</i>: By default any other type will raise an exception, in derived
	 *			classes you can overload this method to handle custom types.
	 *	 </ul>
	 * </ul>
	 *
	 * The method returns a boolean where <i>TRUE</i> indicates that the object was
	 * instantiated with data, and <i>FALSE</i> indicating that the object is empty. This
	 * will be used by the {@link __construct() caller} to set the object
	 * {@link _IsCommitted() committed} status {@link kFLAG_STATE_COMMITTED flag}.
	 *
	 * The parameter is provided as a reference.
	 *
	 * @param reference			   &$theContent			Object data content.
	 *
	 * @access protected
	 * @return boolean
	 *
	 * @throws {@link CException CException}
	 *
	 * @see kERROR_UNSUPPORTED
	 */
	protected function _Create( &$theContent )
	{
		//
		// Create empty object.
		//
		if( $theContent === NULL )
		{
			//
			// Instantiate empty object.
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
			// Instantiate object with data.
			//
			parent::__construct( (array) $theContent );
			
			return count( $theContent );											// ==>
		}
		
		throw new CException( "Unsupported content type",
							  kERROR_UNSUPPORTED,
							  kMESSAGE_TYPE_ERROR,
							  array( 'Content' => $theContent ) );				// !@! ==>
	
	} // _Create.

	 
	/*===================================================================================
	 *	_Commit																			*
	 *==================================================================================*/

	/**
	 * Store object in container.
	 *
	 * The duty of this method is to store the current object in the container provided in
	 * the first parameter with as key the value provided in the second parameter with
	 * options provided in the third parameter, please refer to {@link Commit() this}
	 * documentation for a reference of these parameters. Note that in this method all three
	 * parameters are passed by reference.
	 *
	 * This class supports {@link CContainer CContainer} derived instances and will delegate
	 * the operation to the container. Note that by default we call the container's
	 * {@link CContainer::Commit() commit} method with the
	 * {@link kFLAG_PERSIST_REPLACE kFLAG_PERSIST_REPLACE} option, which will
	 * {@link kFLAG_PERSIST_INSERT insert} the object if new or
	 * {@link kFLAG_PERSIST_UPDATE replace} the eventual existing object.
	 *
	 * The method expects all parameters to have been previously
	 * {@link _PrepareStore() checked}, its main duty is to perform the actual storage.
	 * In derived classes you should intercept custom containers, or call the parent method.
	 *
	 * <i>Note: the duty of this method is to store only the array part of the object,
	 * properties should be ignored.</i>
	 *
	 * @param reference			   &$theContainer		Object container.
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Commit modifiers.
	 *
	 * @access protected
	 * @return mixed
	 */
	protected function _Commit( &$theContainer, &$theIdentifier, &$theModifiers )
	{
		return $theContainer->Commit( $this, $theIdentifier, $theModifiers );		// ==>
	
	} // _Commit.

	 
	/*===================================================================================
	 *	_Load																			*
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
	protected function _Load( &$theContainer, &$theIdentifier )
	{
		return $theContainer->Load( $theIdentifier );								// ==>
	
	} // _Load.

		

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
	 * {@link _Load() find} operation are valid.
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
	 * @throws {@link CException CException}
	 *
	 * @see kERROR_OPTION_MISSING kERROR_UNSUPPORTED
	 */
	protected function _PrepareFind( &$theContainer, &$theIdentifier )
	{
		//
		// Check if container is there.
		//
		if( $theContainer === NULL )
			throw new CException
					( "Missing object container",
					  kERROR_OPTION_MISSING,
					  kMESSAGE_TYPE_ERROR );									// !@! ==>
		
		//
		// Check if container is supported.
		//
		if( ! $theContainer instanceof CContainer )
			throw new CException
					( "Unsupported container type",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Container' => $theContainer ) );					// !@! ==>

		//
		// Check if identifier is there.
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
	 * Normalise before a store.
	 *
	 * This method will be called before the {@link _Commit() store} operation, its
	 * duty is to prepare the object and check the parameters for the
	 * {@link _Commit() commit} operation, please refer to {@link Commit() this}
	 * documentation for a reference of these parameters. Note that in this method all three
	 * parameters are passed by reference.
	 *
	 * By default we perform the following checks:
	 *
	 * <ul>
	 *	<li>Ensure the container is an instance of {@link CContainer CContainer}.
	 *	<li>Ensure the object is {@link _IsInited() initialised}.
	 * </ul>
	 *
	 * In derived classes you should handle your custom containers or delegate to the parent
	 * method.
	 *
	 * In this class we do not check the identifier.
	 *
	 * Any errors should raise an exception.
	 *
	 * @param reference			   &$theContainer		Object container.
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Commit modifiers.
	 *
	 * @access protected
	 *
	 * @throws {@link CException CException}
	 *
	 * @uses _IsInited()
	 *
	 * @see kERROR_INVALID_STATE kERROR_OPTION_MISSING kERROR_UNSUPPORTED
	 */
	protected function _PrepareStore( &$theContainer, &$theIdentifier, &$theModifiers )
	{
		//
		// Check if container is there.
		//
		if( $theContainer === NULL )
			throw new CException
					( "Missing object container",
					  kERROR_OPTION_MISSING,
					  kMESSAGE_TYPE_ERROR );									// !@! ==>
		
		//
		// Check if container is supported.
		//
		if( ! $theContainer instanceof CContainer )
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
					  kERROR_INVALID_STATE,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Object' => $this ) );								// !@! ==>
	
	} // _PrepareStore.

	 
	/*===================================================================================
	 *	_FinishFind																		*
	 *==================================================================================*/

	/**
	 * Normalise after a {@link _Load() load}.
	 *
	 * This method will be called after the {@link _Load() load} operation, its duty is to
	 * clean up or normalise after the operation. Both the container and the identifier
	 * parameters are passed by reference.
	 *
	 * In this class we do nothing.
	 *
	 * @param reference			   &$theContainer		Object container.
	 * @param reference			   &$theIdentifier		Object identifier.
	 *
	 * @access protected
	 */
	protected function _FinishFind( &$theContainer, &$theIdentifier )					   {}

	 
	/*===================================================================================
	 *	_FinishStore																	*
	 *==================================================================================*/

	/**
	 * Normalise after a {@link _Commit() commit}.
	 *
	 * This method will be called after the {@link _Commit() store} operation, its
	 * duty is to clean up or restore the object after the operation please refer to
	 * {@link Commit() this} documentation for a reference of these parameters. Note that in
	 * this method all three parameters are passed by reference.
	 *
	 * In this class we do nothing.
	 *
	 * @param reference			   &$theContainer		Object container.
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Commit modifiers.
	 *
	 * @access protected
	 */
	protected function _FinishStore( &$theContainer, &$theIdentifier, &$theModifiers )	   {}

	 

} // class CPersistentObject.


?>
