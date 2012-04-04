<?php

/**
 * <i>CEntity</i> class definition.
 *
 * This file contains the class definition of <b>CEntity</b> which represents the ancestor
 * of entity objects.
 *
 *	@package	MyWrapper
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
 * Tokens.
 *
 * This include file contains all default token definitions.
 */
require_once( kPATH_LIBRARY_DEFINES."Tokens.inc.php" );

/**
 * Local defines.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CEntity.inc.php" );

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
 *		{@link Kind() kind}, although this may not be required as long as duplicates are not
 *		mixed. The class features a member accessor {@link Code() method} to manage this
 *		property.
 *	<li><i>{@link kTAG_KIND kTAG_KIND}</i>: This offset represents the entity kind. This
 *		attribute should not be confused with the object's {@link kTAG_CLASS class}: the
 *		latter provides an indication on the functionality and structure of the entity,
 *		whereas this attribute provides an indication on the nature of the entity. This
 *		attribute is implemented as an array. The class features a member accessor
 *		{@link Kind() method} to manage this property.
 *	<li><i>{@link kTAG_NAME kTAG_NAME}</i>: This offset represents the entity name. It may
 *		be considered an expanded version of the code or a label that can be applied to the
 *		entity. By default it should be a string, concrete derived instances may expand on
 *		this. The class features a member accessor {@link Name() method} to manage this
 *		property.
 *	<li><i>{@link kOFFSET_EMAIL kOFFSET_EMAIL}</i>: This offset represents the entity e-mail
 *		address. It is a single element and should be always up to date.
 *	<li><i>{@link kTAG_REFS kTAG_REFS}</i>: This offset represents the list of references
 *		of the current object, it is implemented as an array whose elements are structured
 *		as follows:
 *	 <ul>
 *		<li><i>{@link kTAG_KIND kTAG_KIND}</i>: This offset represents the reference type or
 *			context.
 *		<li><i>{@link kTAG_DATA kTAG_DATA}</i>: This offset represents the reference itself,
 *			it is the following structure:
 *		 <ul>
 *			<li><i>{@link kTAG_ID_REFERENCE kTAG_ID_REFERENCE}</i>: The unique identifier of
 *				the referenced object.
 *			<li><i>{@link kTAG_CONTAINER_REFERENCE kTAG_CONTAINER_REFERENCE}</i>: The
 *				{@link CContainer container} name.
 *			<li><i>{@link kTAG_DATABASE_REFERENCE kTAG_DATABASE_REFERENCE}</i>: The database
 *				name.
 *			<li><i>{@link kTAG_CLASS kTAG_CLASS}</i>: The object class name.
 *		 </ul>
 *	 </ul>
 *		The class features a member accessor {@link Affiliate() method} to manage this
 *		property.
 *	<li><i>{@link kTAG_VALID kTAG_VALID}</i>: This offset holds the
 *		{@link kTAG_ID_NATIVE native} identifier of the valid entity. This should be used
 *		when the current entity becomes obsolete or changes identity: instead of deleting it
 *		we create a new one and store in this {@link kTAG_VALID offset} the identifier of
 *		the new entity that will replace the current one.
 * </ul>
 *
 * Objects of this class require at least the {@link Code() code} {@link kTAG_CODE offset}
 * to be set if they expect to have an {@link _IsInited() initialised}
 * {@link kFLAG_STATE_INITED status}.
 *
 * When {@link Commit() committing}, eventual {@link Affiliate() reference} elements set as
 * the actual instances will be first {@link Commit() saved} to the same
 * {@link CContainer container}, then replaced by references.
 *
 * The class also features a static {@link DefaultContainer() method} that returns the
 * default container name for objects of this type.
 *
 *	@package	MyWrapper
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
	 *	Kind																			*
	 *==================================================================================*/

	/**
	 * Manage entity types.
	 *
	 * This method can be used to handle the entity {@link kTAG_KIND kinds}, it uses the
	 * standard accessor {@link _ManageArrayOffset() method} to manage the list of types.
	 *
	 * Each element of this list should indicate a function or quality of the current
	 * entity, the nature and specifics of these elements is the responsibility of concrete
	 * classes.
	 *
	 * For a more thorough reference of how this method works, please consult the
	 * {@link _ManageArrayOffset() _ManageArrayOffset} method, in which the first parameter
	 * will be the constant {@link kTAG_KIND kTAG_KIND}.
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
	public function Kind( $theValue = NULL, $theOperation = NULL, $getOld = FALSE )
	{
		return $this->_ManageArrayOffset
					( kTAG_KIND, $theValue, $theOperation, $getOld );				// ==>

	} // Kind.

	 
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
	 *	Email																			*
	 *==================================================================================*/

	/**
	 * Manage entity e-mail.
	 *
	 * This method can be used to manage the entity {@link kOFFSET_EMAIL e-mail}, it uses
	 * the standard accessor {@link _ManageOffset() method} to manage the
	 * {@link kOFFSET_EMAIL offset}:
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
	 * @param NULL|FALSE|string		$theValue			Entity e-mail or operation.
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
	 *	Affiliate																		*
	 *==================================================================================*/

	/**
	 * Manage entity references.
	 *
	 * An entity may reference a series of other entities and each reference may have a
	 * different type. This method can be used to manage the entity {@link kTAG_REFS offset}
	 * used to store these relations.
	 *
	 * The method accepts the following parameters:
	 *
	 * <ul>
	 *	<li><b>$theType</b>: Type of reference, this is a scalar, possibly a string, that
	 *		indicates the type of reference we are adding, retrieving or deleting. This
	 *		value may be <i>NULL</i> for references that do not imply a type.
	 *	<li><b>$theValue</b>: Reference or object. This parameter represents the reference,
	 *		it may be a scalar, representing either the referenced object
	 *		{@link kTAG_ID_NATIVE identifier}, a structure representing an object reference,
	 *		or the referenced object itself.
	 *	<li><b>$theOperation</b>: The operation to perform:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the element matched by the previous parameters.
	 *		<li><i>FALSE</i>: Delete the element matched by the previous parameters and
	 *			return it.
	 *		<li><i>other</i>: Any other value means that we want to add to the list the
	 *			element provided in the previous parameters, either appending it if there
	 *			was no matching element, or by replacing a matching element. The method will
	 *			return either the replaced element or the new one.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return when deleting or
	 *		replacing:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the deleted or replaced element.
	 *		<li><i>FALSE</i>: Return the replacing element or <i>NULL</i> when deleting.
	 *	 </ul>
	 * </ul>
	 *
	 * Each element of this list is structured as follows:
	 *
	 * <ul>
	 *	<li><i>{@link kTAG_KIND kTAG_KIND}</i>: This offset represents the reference type or
	 *		class, it may be omitted if the reference has no type or when we want to define
	 *		a default reference. The first parameter will be stored here.
	 *	<li><i>{@link kTAG_DATA kTAG_DATA}</i>: This offset represents the reference itself,
	 *		it may be in two forms:
	 *	 <ul>
	 *		<li><i>Scalar</i>: A scalar is interpreted as the object's
	 *			{@link kTAG_ID_NATIVE identifier}.
	 *		<li><i>Reference</i>: An object reference structured as follows:
	 *		 <ul>
	 *			<li><i>{@link kTAG_ID_REFERENCE kTAG_ID_REFERENCE}</i>: The unique
	 *				{@link kTAG_ID_NATIVE identifier} of the referenced object. This element
	 *				is required by default.
	 *			<li><i>{@link kTAG_CONTAINER_REFERENCE kTAG_CONTAINER_REFERENCE}</i>: The
	 *				{@link CContainer container} name. This element is optional.
	 *			<li><i>{@link kTAG_DATABASE_REFERENCE kTAG_DATABASE_REFERENCE}</i>: The
	 *				database name. This element is optional.
	 *		 </ul>
	 *		<li><i>Object</i>: An object derived from this class will be interpreted as the
	 *			referenced object itself. When {@link Commit() committing} the current
	 *			object, these objects will also be {@link Commit() committed} and
	 *			{@link CContainer::Reference() converted} to references.
	 *	 </ul>
	 * </ul>
	 *
	 * @param mixed					$theType			Reference type.
	 * @param mixed					$theValue			Reference value.
	 * @param mixed					$theOperation		Operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 */
	public function Affiliate( $theType, $theValue, $theOperation = NULL, $getOld = FALSE )
	{
		//
		// Build reference element.
		//
		$ref = Array();
		if( $theType !== NULL )
			$ref[ kTAG_KIND ] = $theType;
		$ref[ kTAG_DATA ] = $theValue;
		
		return $this->_ManageObjectList( kTAG_REFS, $ref, $theOperation, $getOld );	// ==>

	} // Affiliate.

	 
	/*===================================================================================
	 *	Valid																			*
	 *==================================================================================*/

	/**
	 * Manage valid entity.
	 *
	 * This method can be used to handle the valid entity {@link kTAG_ID_NATIVE identifier},
	 * it uses the standard accessor {@link _ManageOffset() method} to manage the
	 * {@link kTAG_VALID offset}.
	 *
	 * Entity references should be persistent, so it is not an option to delete an entity:
	 * by creating a new entity and referencing it from the old one, we maintain the
	 * original reference and point to the current object.
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
	 * @see kTAG_VALID
	 */
	public function Valid( $theValue = NULL, $getOld = FALSE )
	{
		//
		// Check identifier.
		//
		if( ($theValue !== NULL)
		 && ($theValue !== FALSE) )
		{
			//
			// Handle arrays or array objects, excluding data types
			//
			if( (! $theValue instanceof CDataType)
			 && ( is_array( $theValue )
			   || ($theValue instanceof ArrayObject) ) )
			{
				//
				// Check native identifier.
				//
				if( array_key_exists( kTAG_ID_NATIVE, (array) $theValue ) )
					$theValue = $theValue[ kTAG_ID_NATIVE ];
			
				//
				// Check reference identifier.
				//
				if( array_key_exists( kTAG_ID_REFERENCE, (array) $theValue ) )
					$theValue = $theValue[ kTAG_ID_REFERENCE ];
			
			} // Not an identifier.
		
		} // Provided new value.
		
		return $this->_ManageOffset( kTAG_VALID, $theValue, $getOld );				// ==>

	} // Valid.

		

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
	 * We overload this method to manage the {@link _IsInited() inited}
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
	 * We overload this method to manage the {@link _IsInited() inited}
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

	 
	/*===================================================================================
	 *	ValidEntity																		*
	 *==================================================================================*/

	/**
	 * Return a valid entity.
	 *
	 * This method can be used to instantiate a valid entity, this means that if we provide
	 * the identifier of an expired or obsolete entity that has a reference to a
	 * {@link Valid() valid} object, this method will return the valid one.
	 *
	 * The method expects the same parameters as the {@link NewObject() NewObject} static
	 * method. If the {@link Valid() valid} chain is recursive, the method will raise an
	 * exception.
	 *
	 * The method will return the entity that does not have a {@link Valid() valid}
	 * {@link kTAG_VALID reference}, or <i>NULL</i> if the entity was not found.
	 *
	 * @param mixed					$theContainer		Persistent container.
	 * @param mixed					$theIdentifier		Object identifier.
	 *
	 * @static
	 * @return CEntity
	 */
	static function ValidEntity( $theContainer, $theIdentifier )
	{
		//
		// Init local storage.
		//
		$chain = Array();
		
		//
		// Iterate.
		//
		do
		{
			//
			// Instantiate entity.
			//
			$entity = self::NewObject( $theContainer, $theIdentifier );
echo( '<pre>' ); print_r( $entity ); echo( '</pre>' );
			
			//
			// Handle entity.
			//
			if( $entity !== NULL )
			{
				//
				// Get identifier.
				//
				$id = (string) $theIdentifier;
				
				//
				// Check recursion.
				//
				if( in_array( $id, $chain ) )
					throw new CException
						( "Recursive valid entity chain",
						  kERROR_INVALID_STATE,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Chain' => $chain ) );							// !@! ==>
				
				//
				// Add to chain.
				//
				$chain[] = $id;
				
				//
				// Copy valid.
				//
				$theIdentifier = $entity[ kTAG_VALID ];
				
			} // Found entity.
			
			//
			// Catch missing chain link.
			//
			elseif( count( $chain ) )
				throw new CException
					( "Entity not found in valid chain",
					  kERROR_INVALID_STATE,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Identifier' => $theIdentifier ) );				// !@! ==>
		
		} while( $theIdentifier !== NULL );
		
		return $entity;																// ==>
		
	} // ValidEntity.

		

/*=======================================================================================
 *																						*
 *								PROTECTED PERSISTENCE UTILITIES							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_PrepareCommit																	*
	 *==================================================================================*/

	/**
	 * Normalise before a store.
	 *
	 * We overload this method to check if the object in {@link _IsInited() initialised}, if
	 * this is not the case we raise an exception.
	 *
	 * We also scan the {@link Affiliate() references} list to commit any elements that are
	 * actual instances and convert them to references: we discriminate such elements by
	 * selecting only objects derived from this class.
	 *
	 * @param reference			   &$theContainer		Object container.
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Commit modifiers.
	 *
	 * @access protected
	 *
	 * @throws {@link CException CException}
	 *
	 * @see kERROR_OPTION_MISSING
	 */
	protected function _PrepareCommit( &$theContainer, &$theIdentifier, &$theModifiers )
	{
		//
		// Call parent method.
		//
		parent::_PrepareCommit( $theContainer, $theIdentifier, $theModifiers );
		
		//
		// Handle references.
		//
		$this->_PrepareReferenceList( $theContainer, kTAG_REFS );
		
	} // _PrepareCommit.

		

/*=======================================================================================
 *																						*
 *							PROTECTED IDENTIFICATION INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_index																			*
	 *==================================================================================*/

	/**
	 * Return the object's unique index.
	 *
	 * In this class we consider the {@link kTAG_CODE code} to be the object's unique
	 * {@link kTAG_ID_NATIVE identifier}.
	 *
	 * @access protected
	 * @return string
	 */
	protected function _index()									{	return $this->Code();	}

	 

} // class CEntity.


?>
