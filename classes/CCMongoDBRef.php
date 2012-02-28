<?php

/**
 * <i>CCMongoDBRef</i> class definition.
 *
 * This file contains the class definition of <b>CCMongoDBRef</b> which implements the
 * MongoDBRef class as an instance.
 *
 *	@package	Framework
 *	@subpackage	Persistence
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 28/02/2012
 */

/*=======================================================================================
 *																						*
 *									CCMongoDBRef.php									*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CArrayObject.php" );

/**
 * Mongo object reference.
 *
 * This class implements the MongoDBRef class as an instance: that is, a
 * {@link CArrayObject CArrayObject} derived object which contains as properties the
 * elements
 *
 *
 * The class implements two main operations:
 *
 * <ul>
 *	<li><i>{@link __construct() Instantiation}</i>: The object is instantiated either from
 *		the referenced object or from the information needed to locate the object.
 *	<li><i>{@link Resolve() Resolve}</i>: This method should return the object the reference
 *		is pointing to.
 * </ul>
 *
 * The class uses a series of predefined offsets to indicate its properties:
 *
 * <ul>
 *	<li><i>{@link kTAG_DATABASE_REFERENCE kTAG_DATABASE_REFERENCE}</i>: This offset refers
 *		to the <i>database</i> in which the object is stored.
 *	<li><i>{@link kTAG_COLLECTION_REFERENCE kTAG_COLLECTION_REFERENCE}</i>: This offset
 *		refers to the <i>collection</i> in which the object is stored.
 *	<li><i>{@link kTAG_ID_REFERENCE kTAG_ID_REFERENCE}</i>: This offset refers to the
 *		object's identifier within the collection.
 * </ul>
 *
 *	@package	Framework
 *	@subpackage	Persistence
 */
class CCMongoDBRef extends CArrayObject
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
	 * The constructor can be used to instantiate an empty reference, instantiate a
	 * reference from the referenced object or load the reference properties.
	 *
	 * The parameters to this method are:
	 *
	 * <ul>
	 *	<li><b>$theReference</b>: This parameter represents the referenced object's
	 *		identifier, or the actual referenced object. The parameter is required.
	 *	<li><b>$theContainer</b>: This parameter represents the container in which the
	 *		referenced object resides.
	 *	<li><b>$theDatabase</b>: This parameter represents the database in which the
	 *		container resides.
	 *	<li><b>$theClass</b>: This parameter represents the referenced object's class.
	 * </ul>
	 *
	 * @param mixed					$theReference		Object, or object reference.
	 * @param mixed					$theContainer		Object container.
	 * @param mixed					$theDatabase		Container database.
	 * @param string				$theClass			Object class.
	 *
	 * @access public
	 */
	abstract public function __construct( $theReference, $theContainer = NULL,
														 $theDatabase = NULL,
														 $theClass = NULL )
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
 *								PUBLIC MEMBER INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Code																			*
	 *==================================================================================*/

	/**
	 * Manage user code.
	 *
	 * This method can be used to manage the user {@link kTAG_CODE code}, it uses the
	 * standard accessor {@link _ManageOffset() method} to manage the
	 * {@link kTAG_CODE offset}:
	 *
	 * <ul>
	 *	<li><b>$theValue</b>: The value or operation:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the current value.
	 *		<li><i>FALSE</i>: Delete the value.
	 *		<li><i>other</i>: Set value.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value <i>before</i> it was eventually modified.
	 *		<li><i>FALSE</i>: Return the value <i>after</i> it was eventually modified.
	 *	 </ul>
	 * </ul>
	 *
	 * @param NULL|FALSE|string		$theValue			User code or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 */
	public function Code( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kTAG_CODE, $theValue, $getOld );				// ==>

	} // Code.

	 
	/*===================================================================================
	 *	Password																		*
	 *==================================================================================*/

	/**
	 * Manage user password.
	 *
	 * This method can be used to manage the user {@link kOFFSET_PASSWORD password}, it uses the
	 * standard accessor {@link _ManageOffset() method} to manage the
	 * {@link kOFFSET_PASSWORD offset}:
	 *
	 * <ul>
	 *	<li><b>$theValue</b>: The value or operation:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the current value.
	 *		<li><i>FALSE</i>: Delete the value.
	 *		<li><i>other</i>: Set value.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value <i>before</i> it was eventually modified.
	 *		<li><i>FALSE</i>: Return the value <i>after</i> it was eventually modified.
	 *	 </ul>
	 * </ul>
	 *
	 * @param NULL|FALSE|string		$theValue			User password or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 */
	public function Password( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kOFFSET_PASSWORD, $theValue, $getOld );		// ==>

	} // Password.

	 
	/*===================================================================================
	 *	Name																			*
	 *==================================================================================*/

	/**
	 * Manage user name.
	 *
	 * This method can be used to manage the user {@link kTAG_NAME name}, it uses the
	 * standard accessor {@link _ManageOffset() method} to manage the
	 * {@link kTAG_NAME offset}:
	 *
	 * <ul>
	 *	<li><b>$theValue</b>: The value or operation:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the current value.
	 *		<li><i>FALSE</i>: Delete the value.
	 *		<li><i>other</i>: Set value.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value <i>before</i> it was eventually modified.
	 *		<li><i>FALSE</i>: Return the value <i>after</i> it was eventually modified.
	 *	 </ul>
	 * </ul>
	 *
	 * @param NULL|FALSE|string		$theValue			User name or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 */
	public function Name( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kTAG_NAME, $theValue, $getOld )	;			// ==>

	} // Name.

	 
	/*===================================================================================
	 *	Mail																			*
	 *==================================================================================*/

	/**
	 * Manage user e-mail.
	 *
	 * This method can be used to manage the user {@link kOFFSET_MAIL e-mail}, it uses the
	 * standard accessor {@link _ManageOffset() method} to manage the
	 * {@link kOFFSET_MAIL offset}:
	 *
	 * <ul>
	 *	<li><b>$theValue</b>: The value or operation:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the current value.
	 *		<li><i>FALSE</i>: Delete the value.
	 *		<li><i>other</i>: Set value.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value <i>before</i> it was eventually modified.
	 *		<li><i>FALSE</i>: Return the value <i>after</i> it was eventually modified.
	 *	 </ul>
	 * </ul>
	 *
	 * @param NULL|FALSE|string		$theValue			User e-mail or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 */
	public function Mail( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kOFFSET_MAIL, $theValue, $getOld );			// ==>

	} // Mail.

		

/*=======================================================================================
 *																						*
 *									STATIC INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	DefaultCollection																*
	 *==================================================================================*/

	/**
	 * Return default collection.
	 *
	 * This method can be used to retrieve default collection given a database name.
	 *
	 * @param string				$theDatabase		Database name.
	 *
	 * @static
	 * @return string
	 */
	static function DefaultCollection( $theDatabase )
	{
		//
		// Instantiate Mongo database.
		//
		$mongo = New Mongo();
		
		//
		// Select database.
		//
		$db = $mongo->selectDB( $theDatabase );

		return $db->selectCollection( 'USERS' );									// ==>
		
	} // DefaultCollection.

		

/*=======================================================================================
 *																						*
 *								PUBLIC ARRAY ACCESS INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	offsetSet																		*
	 *==================================================================================*/

	/**
	 * Set a value for a given offset.
	 *
	 * We overload this method to manage the {@link _Is Inited() inited}
	 * {@link kFLAG_STATE_INITED status}: this is set if {@link kTAG_CODE code},
	 * {@link kOFFSET_PASSWORD password} and {@link kTAG_NAME name} are set.
	 *
	 * @param string				$theOffset			Offset.
	 * @param string|NULL			$theValue			Value to set at offset.
	 *
	 * @access public
	 *
	 * @uses _IsInited()
	 * @uses _IsCommitted()
	 */
	public function offsetSet( $theOffset, $theValue )
	{
		//
		// Call parent method.
		//
		parent::offsetSet( $theOffset, $theValue );
		
		//
		// Set inited flag.
		//
		if( $theValue !== NULL )
			$this->_IsInited( $this->offsetExists( kOFFSET_MAIL ) &&
							  $this->offsetExists( kOFFSET_PASSWORD ) &&
							  $this->offsetExists( kTAG_NAME ) );
	
	} // offsetSet.

	 
	/*===================================================================================
	 *	offsetUnset																		*
	 *==================================================================================*/

	/**
	 * Reset a value for a given offset.
	 *
	 * We overload this method to manage the {@link _Is Inited() inited}
	 * {@link kFLAG_STATE_INITED status}: this is set if {@link kTAG_CODE code},
	 * {@link kOFFSET_PASSWORD password} and {@link kTAG_NAME name} are set.
	 *
	 * @param string				$theOffset			Offset.
	 *
	 * @access public
	 *
	 * @uses _IsInited()
	 * @uses _IsCommitted()
	 */
	public function offsetUnset( $theOffset )
	{
		//
		// Call parent method.
		//
		parent::offsetUnset( $theOffset );
		
		//
		// Set inited flag.
		//
		$this->_IsInited( $this->offsetExists( kOFFSET_MAIL ) &&
						  $this->offsetExists( kOFFSET_PASSWORD ) &&
						  $this->offsetExists( kTAG_NAME ) );
	
	} // offsetUnset.

		

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
	 * We overload this method to set the object's {@link _IsInited() inited}
	 * {@link kFLAG_STATE_INITED status}.
	 *
	 * @param reference			   &$theContent			Object data content.
	 *
	 * @access protected
	 * @return boolean
	 */
	protected function _CreateObject( &$theContent )
	{
		//
		// Call parent method.
		//
		$ok = parent::_CreateObject( $theContent );
		
		//
		// Check required offsets.
		//
		$this->_IsInited( $this->offsetExists( kOFFSET_PASSWORD ) &&
						  $this->offsetExists( kTAG_NAME ) &&
						  $this->offsetExists( kOFFSET_MAIL ) );
		
		return $ok;																	// ==>
	
	} // _CreateObject.

		

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
	 * We overload this method to check if the object in {@link _IsInited() initialised} and
	 * to set the unique {@link kTAG_ID_NATIVE identifier} if it was not already set.
	 *
	 * @param reference			   &$theContainer		Object container.
	 * @param reference			   &$theIdentifier		Object identifier.
	 *
	 * @access protected
	 *
	 * @throws CException
	 *
	 * @see kERROR_OPTION_MISSING
	 */
	protected function _PrepareStore( &$theContainer, &$theIdentifier )
	{
		//
		// Init code.
		//
		if( ! $this->offsetExists( kTAG_CODE ) )
			$this->offsetSet( kTAG_CODE, $this->offsetGet( kOFFSET_MAIL ) );
		
		//
		// Check if inited.
		//
		if( ! $this->_IsInited() )
			throw new CException
					( "Object is not complete: missing required offsets",
					  kERROR_OPTION_MISSING,
					  kMESSAGE_TYPE_ERROR,
					  array( kTAG_CODE
					  	=> ( $this->offsetExists( kTAG_CODE ) )
					  	   ? 'OK': 'Missing',
					  		 kOFFSET_PASSWORD
					  	=> ( $this->offsetExists( kOFFSET_PASSWORD ) )
					  	   ? 'OK': 'Missing',
					  		 kTAG_NAME
					  	=> ( $this->offsetExists( kTAG_NAME ) )
					  	   ? 'OK': 'Missing',
					  		 kOFFSET_MAIL
					  	=> ( $this->offsetExists( kOFFSET_MAIL ) )
					  	   ? 'OK': 'Missing' ) );								// !@! ==>
	
		//
		// Call parent method.
		//
		parent::_PrepareStore( $theContainer, $theIdentifier );
		
	} // _PrepareStore.

	 

} // class CCMongoDBRef.


?>
