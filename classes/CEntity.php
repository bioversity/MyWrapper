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
require_once( kPATH_LIBRARY_SOURCE."CCodedUnitObject.php" );

/**
 * Local defines.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CEntity.inc.php" );

/**
 * Entity.
 *
 * An entity can be an individual, and organisation or a legal entity that exists as a unit,
 * rather than being embedded in another object.
 *
 * In this class we add to the {@link CCodedUnitObject parent} the following properties:
 *
 * <ul>
 *	<li><i>{@link kTAG_NAME kTAG_NAME}</i>: This offset represents the entity name, the
 *		class features a member accessor {@link Name() method} to manage this property.
 *	<li><i>{@link kOFFSET_EMAIL kOFFSET_EMAIL}</i>: This offset represents the entity e-mail
 *		address, it is a scalar property and the class features a member accessor
 *		{@link Email() method} to manage it.
 * </ul>
 *
 * The class also features a static {@link DefaultContainer() method} that returns the
 * default container name for objects of this type.
 *
 * Objects derived from this class will have their {@link _IsEncoded() encoded}
 * {@link kFLAG_STATE_ENCODED flag} set by default and must implement a protected
 * {@link _TokeniseIdentifier() method} that must add tokens to the object
 * {@link _index() index} in order to ensure a unique identifier.
 *
 *	@package	MyWrapper
 *	@subpackage	Entities
 */
class CEntity extends CCodedUnitObject
{
		

/*=======================================================================================
 *																						*
 *								PUBLIC MEMBER INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	GID																				*
	 *==================================================================================*/

	/**
	 * Manage entity global identifier.
	 *
	 * The term global {@link kTAG_GID identifier} represents the un-hashed version of the
	 * term local {@link kTAG_LID identifier}.
	 *
	 * This value is set automatically by a protected {@link _PrepareCommit() method}, so
	 * this method is read-only.
	 *
	 * @access public
	 * @return string
	 *
	 * @see kTAG_GID
	 */
	public function GID()									{	return $this[ kTAG_GID ];	}

	 
	/*===================================================================================
	 *	Name																			*
	 *==================================================================================*/

	/**
	 * Manage entity name.
	 *
	 * This method can be used to handle the entity {@link kTAG_NAME name}, it uses the
	 * standard accessor {@link CAttribute::ManageOffset() method} to manage the
	 * {@link kTAG_NAME offset}.
	 *
	 * This value should be a string that can be used as a label or as a short definition
	 * of the entity. The name may be language dependent, so the type of data stored in this
	 * offset is the responsibility of concrete classes.
	 *
	 * For a more in-depth reference of this method, please consult the
	 * {@link CAttribute::ManageOffset() CAttribute::ManageOffset} method, in which the
	 * second parameter will be the constant {@link kTAG_NAME kTAG_NAME}.
	 *
	 * @param mixed					$theValue			Value.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses CAttribute::ManageOffset()
	 *
	 * @see kTAG_NAME
	 */
	public function Name( $theValue = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageOffset( $this, kTAG_NAME, $theValue, $getOld );	// ==>

	} // Name.

	 
	/*===================================================================================
	 *	Email																			*
	 *==================================================================================*/

	/**
	 * Manage entity e-mail.
	 *
	 * This method can be used to manage the entity {@link kOFFSET_EMAIL e-mail}, it uses
	 * the standard accessor {@link CAttribute::ManageOffset() method} to manage the
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
		return CAttribute::ManageOffset
				( $this, kOFFSET_EMAIL, $theValue, $getOld );						// ==>

	} // Email.

	 
	/*===================================================================================
	 *	Kind																			*
	 *==================================================================================*/

	/**
	 * Manage the kind.
	 *
	 * This method can be used to handle the object's list of {@link kTAG_KIND kinds}, it
	 * uses the standard accessor {@link CAttribute::ManageArrayOffset() method} to manage
	 * the list of kind, type or function tags associated with the file.
	 *
	 * Each element of this list should indicate a specific kind or type of the object or a
	 * specific function that the object has.
	 *
	 * For a more thorough reference of how this method works, please consult the
	 * {@link CAttribute::ManageArrayOffset() CAttribute::ManageArrayOffset} method, in
	 * which the second parameter will be the constant {@link kTAG_KIND kTAG_KIND}.
	 *
	 * @param mixed					$theValue			Value or index.
	 * @param mixed					$theOperation		Operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses CAttribute::ManageArrayOffset()
	 *
	 * @see kTAG_KIND
	 */
	public function Kind( $theValue = NULL, $theOperation = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageArrayOffset
					( $this, kTAG_KIND, $theValue, $theOperation, $getOld );		// ==>

	} // Kind.

	 
	/*===================================================================================
	 *	Created																			*
	 *==================================================================================*/

	/**
	 * Manage object creation time stamp.
	 *
	 * This method can be used to manage the object {@link kTAG_CREATED creation}
	 * time-stamp, it uses the standard accessor {@link CAttribute::ManageOffset() method}
	 * to manage the {@link kTAG_MODIFIED offset}:
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
	 * @param NULL|FALSE|string		$theValue			Object creation date.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses CAttribute::ManageOffset()
	 *
	 * @see kTAG_CREATED
	 */
	public function Created( $theValue = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageOffset( $this, kTAG_CREATED, $theValue, $getOld );	// ==>

	} // Created.

	 
	/*===================================================================================
	 *	Modified																		*
	 *==================================================================================*/

	/**
	 * Manage object last modification time stamp.
	 *
	 * This method can be used to manage the object last {@link kTAG_MODIFIED modification}
	 * time-stamp, or the date in which the last modification was made on the object, it
	 * uses the standard accessor {@link CAttribute::ManageOffset() method} to manage the
	 * {@link kTAG_MODIFIED offset}:
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
	 * @param NULL|FALSE|string		$theValue			Object last modification date.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses CAttribute::ManageOffset()
	 *
	 * @see kTAG_MODIFIED
	 */
	public function Modified( $theValue = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageOffset
					( $this, kTAG_MODIFIED, $theValue, $getOld );					// ==>

	} // Modified.

		

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
	 *	_PrepareCreate																	*
	 *==================================================================================*/

	/**
	 * Normalise parameters of a create.
	 *
	 * We overload this method to enforce the {@link kFLAG_STATE_ENCODED encoded} modifier.
	 *
	 * @param reference			   &$theContainer		Object container.
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Create modifiers.
	 *
	 * @access protected
	 *
	 * @uses _IsEncoded()
	 *
	 * @see kFLAG_STATE_ENCODED
	 */
	protected function _PrepareCreate( &$theContainer, &$theIdentifier, &$theModifiers )
	{
		//
		// Set encoded flag.
		//
		$theModifiers |= kFLAG_STATE_ENCODED;
		
		//
		// Call parent method.
		//
		parent::_PrepareCreate( $theContainer, $theIdentifier, $theModifiers );
	
	} // _PrepareCreate.

	 
	/*===================================================================================
	 *	_PrepareCommit																	*
	 *==================================================================================*/

	/**
	 * Normalise before a store.
	 *
	 * We overload this method to enforce the {@link kFLAG_STATE_ENCODED encoded} modifier,
	 * and we set automatically the global {@link kTAG_GID identifier}.
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
	 * @see kFLAG_STATE_ENCODED
	 */
	protected function _PrepareCommit( &$theContainer, &$theIdentifier, &$theModifiers )
	{
		//
		// Initialise code.
		//
		if( $this->Code() === NULL )
			$this->Code( $this->Email() );
		
		//
		// Set encoded flag.
		//
		$theModifiers |= kFLAG_STATE_ENCODED;
		
		//
		// Call parent method.
		//
		parent::_PrepareCommit( $theContainer, $theIdentifier, $theModifiers );
	
	} // _PrepareCommit.

	 

} // class CEntity.


?>
