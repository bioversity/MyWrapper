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
require_once( kPATH_LIBRARY_SOURCE."CGraphUnitObject.php" );

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
 * We add to the {@link CGraphUnitObject parent} the following properties:
 *
 * <ul>
 *	<li><i>{@link kTAG_NAME kTAG_NAME}</i>: This offset represents the entity name. It may
 *		be considered an expanded version of the code or a label that can be applied to the
 *		entity. By default it should be a string, concrete derived instances may expand on
 *		this. The class features a member accessor {@link Name() method} to manage this
 *		property.
 *	<li><i>{@link kOFFSET_EMAIL kOFFSET_EMAIL}</i>: This offset represents the entity e-mail
 *		address. It is a single element and should be always up to date.
 * </ul>
 *
 * The {@link kTAG_LINK_IN incoming} and {@link kTAG_LINK_OUT outgoing} references have the
 * following structure:
 *
 * <ul>
 *	<li><i>{@link kTAG_KIND kTAG_KIND}</i>: This offset represents the reference type or
 *		context.
 *	<li><i>{@link kTAG_DATA kTAG_DATA}</i>: This offset represents the reference itself, it
 *		has the following structure:
 *	 <ul>
 *		<li><i>{@link kTAG_ID_REFERENCE kTAG_ID_REFERENCE}</i>: The unique identifier of
 *			the referenced object.
 *		<li><i>{@link kTAG_CONTAINER_REFERENCE kTAG_CONTAINER_REFERENCE}</i>: The
 *			{@link CContainer container} name.
 *		<li><i>{@link kTAG_DATABASE_REFERENCE kTAG_DATABASE_REFERENCE}</i>: The database
 *			name.
 *		<li><i>{@link kTAG_CLASS kTAG_CLASS}</i>: The object class name.
 *	 </ul>
 * </ul>
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
class CEntity extends CGraphUnitObject
{
		

/*=======================================================================================
 *																						*
 *								PUBLIC MEMBER INTERFACE									*
 *																						*
 *======================================================================================*/


	 
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
	 *	RelatedFrom																		*
	 *==================================================================================*/

	/**
	 * Manage incoming references.
	 *
	 * We {@link CGraphUnitObject::RelatedFrom() override} this method to handle references
	 * structured as follows:
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
	 * @param mixed					$theType			Reference type.
	 * @param mixed					$theValue			Reference value.
	 * @param mixed					$theOperation		Operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @see kTAG_LINK_IN kTAG_KIND kTAG_DATA
	 */
	public function RelatedFrom( $theType, $theValue, $theOperation = NULL,
													  $getOld = FALSE )
	{
		//
		// Init reference element.
		//
		$reference = Array();
		
		//
		// Handle kind.
		//
		if( $theType !== NULL )
			$reference[ kTAG_KIND ] = $theType;
		
		//
		// Set value.
		//
		$reference[ kTAG_DATA ] = $theValue;
		
		return parent::RelatedFrom( $reference, $theOperation, $getOld );			// ==>

	} // RelatedFrom.

	 
	/*===================================================================================
	 *	RelateTo																		*
	 *==================================================================================*/

	/**
	 * Manage outgoing references.
	 *
	 * We {@link CGraphUnitObject::RelateTo() override} this method to handle references
	 * structured as follows:
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
	 * @param mixed					$theType			Reference type.
	 * @param mixed					$theValue			Reference value.
	 * @param mixed					$theOperation		Operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @see kTAG_LINK_OUT kTAG_KIND kTAG_DATA
	 */
	public function RelateTo( $theType, $theValue, $theOperation = NULL, $getOld = FALSE )
	{
		//
		// Init reference element.
		//
		$reference = Array();
		
		//
		// Handle kind.
		//
		if( $theType !== NULL )
			$reference[ kTAG_KIND ] = $theType;
		
		//
		// Set value.
		//
		$reference[ kTAG_DATA ] = $theValue;
		
		return parent::RelateTo( $reference, $theOperation, $getOld );				// ==>

	} // RelateTo.

		

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

	 

} // class CEntity.


?>
