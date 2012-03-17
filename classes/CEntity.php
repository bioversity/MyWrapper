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
 *	@version	1.00 16/03/2012
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
 * Entity.
 *
 * An entity can be an individual, and organisation or a legal entity that must be
 * referenced.
 *
 * The main difference between objects derived from this class and other instances of
 * people or organisations is that in the latter case these entities may be embedded in
 * in other objects, whereas in the case of this class, these entities must exist on their
 * own since they are to be referenced by different sources.
 *
 * In this class we declare only the bare minimum attributes that any entity needs, derived
 * classes will use this one as a base to implement concrete instances:
 *
 * The shared attributes are:
 *
 * <ul>
 *	<li><i>{@link kTAG_CODE kTAG_CODE}</i>: This attribute is a short string that can be
 *		used to discriminate the entity, the string should be readable and printable. In
 *		general, this string should be unique among all entities of the same
 *		{@link Type() type}, although this may not be required as long as duplicates are not
 *		mixed. The class features a member accessor {@link Code() method} to manage this
 *		property.
 *	<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>: This offset represents the entity type. This
 *		attribute should not be confused with the object's {@link kTAG_CLASS class}: the
 *		latter provides an indication on the functionality and structure of the entity,
 *		whereas this attribute provides an indication on the nature of the entity. This
 *		attribute is implemented as an array. The class features a member accessor
 *		{@link Type() method} to manage this property.
 *	<li><i>{@link kTAG_NAME kTAG_NAME}</i>: This offset represents the entity name. It may
 *		be considered an expanded version of the code or a label that can be applied to the
 *		entity. By default it should be a string, concrete derived instances may expand on
 *		this. The class features a member accessor {@link Name() method} to manage this
 *		property.
 *	<li><i>{@link kTAG_REF kTAG_REF}</i>: This offset represents the list of references of
 *		this entity to other entities. It is implemented as an array whose elements may
 *		either be:
 *	 <ul>
 *		<li><i>A typed reference</i>: This element is an array structured as follows:
 *		 <ul>
 *			<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>: This offset represents the reference
 *				type or context.
 *			<li><i>{@link kTAG_DATA kTAG_DATA}</i>: This offset represents the reference
 *				itself, it may simply be an object identifier, an object reference or the
 *				referenced object itself.
 *		 </ul>
 *		<li><i>A simple reference</i>: This element is a scalar representing an object
 *			identifier, an object reference or the referenced object itself. In this case
 *			the nature of the reference must be implicit.
 *	 </ul>
 *		The class features a member accessor {@link Reference() method} to manage this
 *		property.
 * </ul>
 *
 * Objects of this class require at least the {@link Code() code} {@link kTAG_CODE offset}
 * to be set if they expect to have an {@link _IsInited() initialised}
 * {@link kFLAG_STATE_INITED status}.
 *
 * When {@link Commit() committing}, eventual {@link Reference() reference} elements set as
 * the actual instances will be first {@link Commit() saved} to the same
 * {@link CContainer container}, then replaced by references.
 *
 * The class also features a static {@link DefaultContainer() method} that returns the
 * default container name for objects of this type.
 *
 *	@package	Objects
 *	@subpackage	Entities
 */
abstract class CEntity extends CPersistentUnitObject
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
	 * {@link Code() code} attribute is set.
	 *
	 * @param mixed					$theContainer		Persistent container.
	 * @param mixed					$theIdentifier		Object identifier.
	 *
	 * @access public
	 *
	 * @uses _IsInited
	 *
	 * @see kTAG_CODE
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
		$this->_IsInited( $this->offsetExists( kTAG_CODE ) );
		
	} // Constructor.

		

/*=======================================================================================
 *																						*
 *								PUBLIC MEMBER INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Code																			*
	 *==================================================================================*/

	/**
	 * Manage entity code.
	 *
	 * This method can be used to handle the entity {@link kTAG_CODE code}, it uses the
	 * standard accessor {@link _ManageOffset() method} to manage the
	 * {@link kTAG_CODE offset}.
	 *
	 * This code is the entity identifier or acronym, in general it will be the key to the
	 * entity in a collection of entities of the same kind. The value should be a short
	 * string, possibly printable, that could be used as the entity unique identifier.
	 *
	 * For a more in-depth reference of this method, please consult the
	 * {@link _ManageOffset() _ManageOffset} method, in which the first parameter will be
	 * the constant {@link kTAG_CODE kTAG_CODE}.
	 *
	 * @param mixed					$theValue			Value.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses _ManageOffset
	 *
	 * @see kTAG_CODE
	 */
	public function Code( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kTAG_CODE, $theValue, $getOld );				// ==>

	} // Code.

	 
	/*===================================================================================
	 *	Type																			*
	 *==================================================================================*/

	/**
	 * Manage entity types.
	 *
	 * This method can be used to handle the entity {@link kTAG_TYPE types}, it uses the
	 * standard accessor {@link _ManageArrayOffset() method} to manage the list of types.
	 *
	 * Each element of this list should indicate a function or quality of the current
	 * entity, the nature and specifics of these elements is the responsibility of concrete
	 * classes.
	 *
	 * For a more thorough reference of how this method works, please consult the
	 * {@link _ManageArrayOffset() _ManageArrayOffset} method, in which the first parameter
	 * will be the constant {@link kTAG_TYPE kTAG_TYPE}.
	 *
	 * @param mixed					$theValue			Value or index.
	 * @param mixed					$theOperation		Operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses _ManageArrayOffset
	 *
	 * @see kTAG_TYPE
	 */
	public function Type( $theValue = NULL, $theOperation = NULL, $getOld = FALSE )
	{
		return $this->_ManageArrayOffset
					( kTAG_TYPE, $theValue, $theOperation, $getOld );				// ==>

	} // Type.

	 
	/*===================================================================================
	 *	Name																			*
	 *==================================================================================*/

	/**
	 * Manage entity name.
	 *
	 * This method can be used to handle the entity {@link kTAG_NAME name}, it uses the
	 * standard accessor {@link _ManageOffset() method} to manage the
	 * {@link kTAG_NAME offset}.
	 *
	 * This value should be a string that can be used as a label or as a short definition
	 * of the entity. The name may be language dependent, so the type of data stored in this
	 * offset is the responsibility of concrete classes.
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
	 *
	 * @uses _ManageOffset
	 *
	 * @see kTAG_NAME
	 */
	public function Name( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kTAG_NAME, $theValue, $getOld );				// ==>

	} // Name.

	 
	/*===================================================================================
	 *	Reference																		*
	 *==================================================================================*/

	/**
	 * Manage entity references.
	 *
	 * This method can be used to manage the entity {@link kTAG_REF references}, it uses the
	 * standard accessor {@link _ManageObjectList() method} to manage the
	 * {@link kTAG_REF offset}.
	 *
	 * This property represents a list of elements that reference other entity objects, the
	 * elements of this list can take two forms:
	 *
	 * <ul>
	 *	<li><i>A typed reference</i>: A typed reference is a reference that has a specific
	 *		type or class, for instance an exact synonym, which is different from a generic
	 *		synonym. Such elements are expressed as an array:
	 *	 <ul>
	 *		<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>: This offset represents the reference
	 *			type or class, it may be omitted if the reference has no type or when we
	 *			want to define a default reference.
	 *		<li><i>{@link kTAG_DATA kTAG_DATA}</i>: This offset represents the reference
	 *			itself, it may simply be an object identifier, an object reference or the
	 *			referenced object itself. This offset is required, to indicate that this is
	 *			a typed reference collection.
	 *	 </ul>
	 *	<li><i>A simple reference</i>: Each element is a scalar representing an object
	 *		identifier, an object reference or the referenced object itself. In this case
	 *		the nature of the reference should be implicit.
	 * </ul>
	 *
	 * You should not generally mix these two types of elements in the same offset because
	 * this method expects both the {@link kTAG_TYPE type} and {@link kTAG_DATA reference}
	 * to match.
	 *
	 * For a more in-depth reference of this method, please consult the
	 * {@link _ManageObjectList() _ManageObjectList} method, in which the first parameter
	 * will be the constant {@link kTAG_REF kTAG_REF}.
	 *
	 * @param mixed					$theValue			Reference element.
	 * @param mixed					$theOperation		Operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses _ManageOffset
	 *
	 * @see kTAG_REF
	 */
	public function Reference( $theValue, $theOperation = NULL, $getOld = FALSE )
	{
		return $this->_ManageObjectList
			( kTAG_REF, $theValue, $theOperation, $getOld );						// ==>

	} // Reference.

		

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
	 * {@link kFLAG_STATE_INITED status}: this is set if {@link kTAG_CODE code} property is
	 * set.
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
			$this->_IsInited( $this->offsetExists( kTAG_CODE ) );
	
	} // offsetSet.

	 
	/*===================================================================================
	 *	offsetUnset																		*
	 *==================================================================================*/

	/**
	 * Reset a value for a given offset.
	 *
	 * We overload this method to manage the {@link _Is Inited() inited}
	 * {@link kFLAG_STATE_INITED status}: this is set if {@link kTAG_CODE code} property is
	 * set.
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
		$this->_IsInited( $this->offsetExists( kTAG_CODE ) );
	
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
	 * We also scan the {@link Reference() references} list to commit any elements that are
	 * actual instances and convert them to references: we discriminate such elements by
	 * selecting only objects derived from this class.
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
		if( $this->offsetExists( kTAG_REF ) )
		{
			//
			// Iterate parents.
			//
			$done = FALSE;
			$references = $this->offsetGet( kTAG_REF );
			foreach( $references as $key => $value )
			{
				//
				// Handle typed reference.
				//
				if( ( is_array( $value )
				   || ($value instanceof ArrayObject) )
				 && array_key_exists( kTAG_DATA, (array) $value ) )
				{
					//
					// Check if is an instance.
					//
					if( ($object = $value[ kTAG_DATA ]) instanceof self )
					{
						//
						// Commit.
						//
						$value[ kTAG_DATA ]->Commit( $theContainer );
						
						//
						// Save identifier.
						//
						$done = $references[ $key ] = $value[ kTAG_DATA ][ kTAG_ID_NATIVE ];
					
					} // Is an instance.
				
				} // Has data element.
				
				//
				// Handle simple reference.
				//
				elseif( $value instanceof self )
				{
					//
					// Commit.
					//
					$value->Commit( $theContainer );
					
					//
					// Save identifier.
					//
					$done = $references[ $key ] = $value[ kTAG_ID_NATIVE ];
				
				} // Is an instance.
			
			} // Iterating parents.
			
			//
			// Update list.
			//
			if( $done )
				$this->offsetSet( kTAG_REF, $references );
		
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
