<?php

/**
 * <i>CEntity</i> class definition.
 *
 * This file contains the class definition of <b>CEntity</b> which represents the ancestor
 * of entity objects.
 *
 *	@package	Objects
 *	@subpackage	Entities
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 12/03/2012
 */

/*=======================================================================================
 *																						*
 *										CEntity.php										*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CPersistentUnitObject.php" );

/**
 * Local definitions.
 *
 * This include file contains all local definitions to this class.
 */
require_once( kPATH_LIBRARY_SOURCE."CEntity.inc.php" );

/**
 * Entity.
 *
 * An entity is a person, institution or legal entity that needs to have a specific identity
 * in the global data schema.
 *
 * Examples of entities could be a gene bank, a germplasm curator, a farmer that donated an
 * accession, etc. Since entities may take different forms, they can be individuals or
 * organisations.
 *
 * This class  implements the features common to all entity derived classes:
 *
 * <ul>
 *	<li><i>{@link kTAG_PARENT kTAG_PARENT}</i>: This offset represents the entity parents or
 *		affiliations. This is an array of object {@link kTAG_ID_NATIVE identifiers} or
 *		{@link CEntity CEntity} derived instances.
 *		The class features a member accessor {@link Parent() method} to manage this
 *		property.
 *	<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>: This offset represents the entity type. The
 *		{@link kTAG_CLASS class} offset indicates the class to which the object belongs,
 *		this offset should indicate what functions the object has. The data is an array.
 *		The class features a member accessor {@link Type() method} to manage this property.
 *	<li><i>{@link kTAG_CODE kTAG_CODE}</i>: This offset represents the entity code, it
 *		should also represent the entity unique identifier.
 *		The class features a member accessor {@link Code() method} to manage this property.
 *	<li><i>{@link kTAG_NAME kTAG_NAME}</i>: This offset represents the entity name.
 *		The class features a member accessor {@link Name() method} to manage this property.
 *	<li><i>{@link kOFFSET_MAIL kOFFSET_MAIL}</i>: This offset represents the entity mailing
 *		address. The data is structured as an array in which the elements are structured as
 *		follows:
 *	 <ul>
 *		<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>: This offset represents the address type,
 *			this could be "home", "office" or this element could be missing for a default
 *			address.
 *		<li><i>{@link kTAG_DATA kTAG_DATA}</i>: This offset represents the actual address,
 *			it may be a string containing the whole address, or an {@link CAddress object}
 *			in which each element of the address has its own property.
 *	 </ul>
 *		The class features a member accessor {@link Mail() method} to manage this property.
 *	<li><i>{@link kOFFSET_EMAIL kOFFSET_EMAIL}</i>: This offset represents the entity
 *		e-mail.
 *		The class features a member accessor {@link Email() method} to manage this property.
 *	<li><i>{@link kOFFSET_PHONE kOFFSET_PHONE}</i>: This offset represents the entity
 *		telephone number. The data is structured as an array in which the elements contain
 *		the telephone number.
 *		The class features a member accessor {@link Phone() method} to manage this property.
 * </ul>
 *
 * Among the above attributes the {@link kTAG_CODE code} and the {@link kTAG_NAME name} are
 * required, this means that the object will not have its {@link _IsInited() inited}
 * {@link kFLAG_STATE_INITED status} on, if any of these are not set; which also means that
 * the object will not be allowed to be {@link Commit() committed}.
 *
 * By default, the object's unique {@link kTAG_ID_NATIVE identifier} will be set with the
 * current {@link Code() code} contents.
 *
 * This class also features a static {@link DefaultContainer() method} that should return
 * the default container name in which to store such objects.
 *
 *	@package	Objects
 *	@subpackage	Entities
 */
class CEntity extends CPersistentUnitObject
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
	 * We {@link CPersistentObject::__construct() overload} the constructor to initialise
	 * the {@link _IsInited() inited} {@link kFLAG_STATE_INITED flag} if the
	 * {@link Code() code} and {@link Name() name} elements are set.
	 *
	 * @param mixed					$theContainer		Persistent container.
	 * @param mixed					$theIdentifier		Object identifier.
	 *
	 * @access public
	 */
	public function __construct( $theContainer = NULL, $theIdentifier = NULL )
	{
		//
		// Call parent method.
		//
		parent::__construct( $theContainer, $theIdentifier );
		
		//
		// Set inited status.
		//
		$this->_IsInited( $this->offsetExists( kTAG_CODE ) &&
						  $this->offsetExists( kTAG_NAME ) );
		
	} // Constructor.

		

/*=======================================================================================
 *																						*
 *								PUBLIC MEMBER INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Type																			*
	 *==================================================================================*/

	/**
	 * Manage entity types.
	 *
	 * This method can be used to manage the entity {@link kTAG_TYPE types}, it uses the
	 * standard accessor {@link _ManageArrayOffset() method} to manage the list of parent
	 * entities.
	 *
	 * In general, elements of this list should be a token that indicates a specific
	 * function of the entity, this could be, for instance', <i>user</i> to indicate an
	 * entity that is also a user of the system.
	 *
	 * For a more in-depth reference of this method, please consult the
	 * {@link _ManageArrayOffset() _ManageArrayOffset} method, in which the first parameter
	 * will be the constant {@link kTAG_TYPE kTAG_TYPE}.
	 *
	 * Note that this method will <i<NOT</i> work on an object that was
	 * {@link CMongoDataWrapper::SerialiseObject() serialised}.
	 *
	 * @param mixed					$theValue			Value or index.
	 * @param mixed					$theOperation		Operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 */
	public function Type( $theValue = NULL, $theOperation = NULL, $getOld = FALSE )
	{
		return $this->_ManageArrayOffset
					( kTAG_TYPE, $theValue, $theOperation, $getOld );				// ==>

	} // Type.

	 
	/*===================================================================================
	 *	Code																			*
	 *==================================================================================*/

	/**
	 * Manage entity code.
	 *
	 * This method can be used to manage the entity {@link kTAG_CODE code}, it uses the
	 * standard accessor {@link _ManageOffset() method} to manage the
	 * {@link kTAG_CODE offset}:
	 *
	 * For a more in-depth reference of this method, please consult the
	 * {@link _ManageOffset() _ManageOffset} method, in which the first parameter
	 * will be the constant {@link kTAG_CODE kTAG_CODE}.
	 *
	 * @param mixed					$theValue			Value.
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
	 *	Name																			*
	 *==================================================================================*/

	/**
	 * Manage entity name.
	 *
	 * This method can be used to manage the entity {@link kTAG_NAME name}, it uses the
	 * standard accessor {@link _ManageOffset() method} to manage the
	 * {@link kTAG_NAME offset}:
	 *
	 * For a more in-depth reference of this method, please consult the
	 * {@link _ManageOffset() _ManageOffset} method, in which the first parameter
	 * will be the constant {@link kTAG_NAME kTAG_NAME}.
	 *
	 * @param mixed					$theValue			Value.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 */
	public function Name( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kTAG_NAME, $theValue, $getOld );				// ==>

	} // Name.

	 
	/*===================================================================================
	 *	Mail																			*
	 *==================================================================================*/

	/**
	 * Manage entity mailing addresses.
	 *
	 * This method can be used to manage the entity mailing {@link kOFFSET_MAIL addresses},
	 * it uses the standard accessor {@link _ManageTypedArrayOffset() method} to manage the
	 * list of addresses.
	 *
	 * This list is an array of structures where the {@link kTAG_TYPE kTAG_TYPE} element
	 * indicates the type of the address, for instance <i>home</i> or </i>office</i>, and
	 * the {@link kTAG_DATA kTAG_DATA} element represents the actual address, be it a string
	 * or itself a structure holding the address elements. If the
	 * {@link kTAG_TYPE kTAG_TYPE} element is missing, we assume it to be the default
	 * address.
	 *
	 * For a more in-depth reference of this method, please consult the
	 * {@link _ManageArrayOffset() _ManageTypedArrayOffset} method, in which the first
	 * parameter will be the constant {@link kOFFSET_MAIL kOFFSET_MAIL}.
	 *
	 * Note that this method will <i<NOT</i> work on an object that was
	 * {@link CMongoDataWrapper::SerialiseObject() serialised}.
	 *
	 * @param mixed					$theType			Item type.
	 * @param mixed					$theValue			Item value.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 */
	public function Mail( $theType = NULL, $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageTypedArrayOffset
				( kOFFSET_MAIL, $theType, $theValue, $getOld );						// ==>

	} // Mail.

	 
	/*===================================================================================
	 *	Email																			*
	 *==================================================================================*/

	/**
	 * Manage entity e-mail.
	 *
	 * This method can be used to manage the entity {@link kOFFSET_EMAIL e-mail}, it uses
	 * the standard accessor {@link _ManageOffset() method} to manage the
	 * {@link kOFFSET_EMAIL offset}:
	 *
	 * For a more in-depth reference of this method, please consult the
	 * {@link _ManageOffset() _ManageOffset} method, in which the first parameter
	 * will be the constant {@link kOFFSET_EMAIL kOFFSET_EMAIL}.
	 *
	 * @param mixed					$theValue			Value.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 */
	public function Email( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kOFFSET_EMAIL, $theValue, $getOld );			// ==>

	} // Email.

	 
	/*===================================================================================
	 *	Phone																			*
	 *==================================================================================*/

	/**
	 * Manage entity phone.
	 *
	 * This method can be used to manage the entity {@link kOFFSET_PHONE telephone} number,
	 * it uses the standard accessor {@link _ManageOffset() method} to manage the
	 * {@link kOFFSET_PHONE offset}:
	 *
	 * For a more in-depth reference of this method, please consult the
	 * {@link _ManageOffset() _ManageOffset} method, in which the first parameter
	 * will be the constant {@link kOFFSET_PHONE kOFFSET_PHONE}.
	 *
	 * @param mixed					$theValue			Value.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 */
	public function Phone( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kOFFSET_PHONE, $theValue, $getOld );			// ==>

	} // Phone.

		

/*=======================================================================================
 *																						*
 *							PUBLIC PARENT MEMBER INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Parent																			*
	 *==================================================================================*/

	/**
	 * Manage entity parents.
	 *
	 * This method can be used to manage the entity {@link kTAG_PARENT parents}, you can
	 * add/replace, retrieve and delete parent elements depending on the value of the
	 * parameters.
	 *
	 * The property is represented by an array of instances derived from this class, or
	 * strings representing entity {@link kTAG_ID_NATIVE identifiers}.
	 * In the first case, prior to {@link Commit() saving} the current object, array
	 * elements in the form of CEntity instances will also be {@link Commit() saved} and
	 * replaced by their {@link kTAG_ID_NATIVE identifiers}.
	 *
	 * This method accepts the following parameters:
	 *
	 * <ul>
	 *	<li><b>$theIndex</b>: This parameter represents the element index.
	 *	<li><b>$theOperation</b>: The operation to perform:
	 *	 <ul>
	 *		<li><i>NULL</i>: Retrieve the element, the methoid will return the matching
	 *			element or <i>NULL</i> if not found.
	 *		<li><i>FALSE</i>: Delete the element, depending on the value of the next
	 *			parameter, the method will either return the deleted element or <i>NULL</i>.
	 *		<li><i>other</i>: Any other value means that we want to add/replace the element,
	 *			in this case the method will return the added element's index.
	 *	 </ul>
	 *	<li><b>$theData</b>: This parameter represents the element data.
	 * </ul>
	 *
	 * Note that this method will <i<NOT</i> work on an object that was
	 * {@link CMongoDataWrapper::SerialiseObject() serialised}.
	 *
	 * @param mixed					$theValue			Index or value.
	 * @param mixed					$theOperation		Operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 */
	public function Parent( $theValue, $theOperation = NULL, $getOld = FALSE )
	{
		return $this->_ManageObjectList
			( kTAG_PARENT, $theValue, $theOperation,$getOld );						// ==>

	} // Parent.

		

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
	 * {@link kFLAG_STATE_INITED status}: this is set if {@link kTAG_CODE code} and
	 * {@link kTAG_NAME name} properties are set.
	 *
	 * @param string				$theOffset			Offset.
	 * @param string|NULL			$theValue			Value to set at offset.
	 *
	 * @access public
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
			$this->_IsInited( $this->offsetExists( kTAG_CODE ) &&
							  $this->offsetExists( kTAG_NAME ) );
	
	} // offsetSet.

	 
	/*===================================================================================
	 *	offsetUnset																		*
	 *==================================================================================*/

	/**
	 * Reset a value for a given offset.
	 *
	 * We overload this method to manage the {@link _Is Inited() inited}
	 * {@link kFLAG_STATE_INITED status}: this is set if {@link kTAG_CODE code} and
	 * {@link kTAG_NAME name} properties are set.
	 *
	 * @param string				$theOffset			Offset.
	 *
	 * @access public
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
		$this->_IsInited( $this->offsetExists( kTAG_CODE ) &&
						  $this->offsetExists( kTAG_NAME ) );
	
	} // offsetUnset.

		

/*=======================================================================================
 *																						*
 *									STATIC INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	DefaultContainer																*
	 *==================================================================================*/

	/**
	 * Return the default container.
	 *
	 * This method can be used to retrieve the default container name.
	 *
	 * @static
	 * @return string
	 */
	static function DefaultContainer()						{	return kENTITY_CONTAINER;	}

		

/*=======================================================================================
 *																						*
 *								PROTECTED PERSISTENCE UTILITIES							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_PrepareStore																	*
	 *==================================================================================*/

	/**
	 * Normalise before a store.
	 *
	 * We overload this method to check if the object in {@link _IsInited() initialised}, if
	 * this is not the case we raise an exception.
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
		// Call parent method.
		//
		parent::_PrepareStore( $theContainer, $theIdentifier );
		
		//
		// Handle parents.
		//
		if( $this->offsetExists( kTAG_PARENT ) )
		{
			//
			// Iterate parents.
			//
			$parents = $this->offsetGet( kTAG_PARENT );
			foreach( $parents as $key => $value )
			{
				//
				// Handle instances.
				//
				if( $value instanceof self )
				{
					//
					// Save parent.
					//
					$value->Commit( $theContainer );
					
					//
					// Use parent index.
					//
					$parents[ $key ] = $value[ kTAG_ID_NATIVE ];
				
				} // Parent element is an entity instance.
			
			} // Iterating parents.
			
			//
			// Save list.
			//
			$this->offsetSet( kTAG_PARENT, $parents );
		
		} // Has parents.
		
	} // _PrepareStore.

		

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
	 * We overload this method to return the object's {@link kTAG_ID_NATIVE identifier}, if
	 * it is set, or the object's {@link Code() code}.
	 *
	 * If none of the above are set, we call the parent method.
	 *
	 * @access protected
	 * @return string
	 */
	protected function _id()
	{
		//
		// Call parent method.
		//
		$id = parent::_id();
		
		//
		// Check default value.
		//
		if( $id !== NULL )
			return $id;																// ==>
		
		//
		// Try code.
		//
		if( $this->offsetExists( kTAG_CODE ) )
			return $this->offsetGet( kTAG_CODE );									// ==>
		
		return $id;																	// ==>
	
	} // _id.

	 

} // class CEntity.


?>
