<?php

/**
 * <i>CUser</i> class definition.
 *
 * This file contains the class definition of <b>CUser</b> which represents an
 * {@link CEntity entity} mapping a general purpose user.
 *
 *	@package	MyWrapper
 *	@subpackage	Entities
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 20/03/2012
 */

/*=======================================================================================
 *																						*
 *										CUser.php										*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CEntity.php" );

/**
 * Tokens.
 *
 * This include file contains all default token definitions.
 */
require_once( kPATH_LIBRARY_DEFINES."Tokens.inc.php" );

/**
 * Local defines.
 *
 * This include file contains the local class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CUser.inc.php" );

/**
 * User.
 *
 * This class overloads its {@link CEntity ancestor} to implement a user entity.
 *
 * Users are entities that are used for authentication purposes, they share the same
 * attributes as their parent {@link CEntity class} and add one required element:
 *
 * <ul>
 *	<li><i>{@link kOFFSET_PASSWORD kOFFSET_PASSWORD}</i>: This offset represents the user
 *		access password, it will be used in the authentication process.
 *		The class features a member accessor {@link Password() method} to manage this
 *		property.
 * </ul>
 *
 * The object is considered {@link _IsInited() initialised} only if it has its
 * {@link Code() code}, as its {@link CEntity ancestor}, its {@link Name() name},
 * {@link Password() password} and {@link Email() e-mail} address.
 *
 * If the {@link Code() code} has not been explicitly set, {@link _PrepareCommit() before}
 * {@link Commit() committing} the object it will be set to the value of the
 * {@link Email e-mail}. Also in that phase, the {@link kENTITY_USER kENTITY_USER} constant
 * will be set in the user {@link Type() type}.
 *
 * The {@link Email() e-mail} in this class is a scalar property, in other classes it will
 * probably be a list of different e-mail types. In this class we want to link a single
 * user with a single e-mail, possibly not shared by any other user, that is why we link by
 * default the user {@link Code() code} and {@link Email() e-mail}.
 *
 * <i><b>Note: this class enforces the {@link _IsEncoded() encoded} {@link Status() status}
 * {@link kFLAG_STATE_ENCODED flag}, because the object identifier is a binary string, so
 * always use complex data type instances derived from the {@link CDataType standard}
 * types</b></i>.
 *
 * The unique identifier of this class is composed by the default {@link _index() index} of
 * the object, prefixed by the {@link kENTITY_USER kENTITY_USER} token and the
 * {@link kTOKEN_CLASS_SEPARATOR kTOKEN_CLASS_SEPARATOR} token, this allows users and other
 * types of {@link CEntity entities} to share the same {@link Code() code}; this is enforced
 * both in the {@link HashIndex() HashIndex} method, to which you only need to pass the user
 * {@link Code() code}, and in the protected {@link _PrepareCommit() _PrepareCommit} method
 * which will place the resulting string in the global {@link kTAG_GID identifier}.
 *
 *	@package	MyWrapper
 *	@subpackage	Entities
 */
class CUser extends CEntity
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
	 * We {@link CEntity::__construct() overload} the constructor to initialise the
	 * {@link _IsInited() inited} {@link kFLAG_STATE_INITED flag} if the
	 * {@link Password() password} element is set.
	 *
	 * We also pass the {@link _IsEncoded() encoded} {@link kFLAG_STATE_ENCODED flag} to the
	 * parent constructor.
	 *
	 * @param mixed					$theContainer		Persistent container.
	 * @param mixed					$theIdentifier		Object identifier.
	 * @param bitfield				$theModifiers		Create modifiers.
	 *
	 * @access public
	 */
	public function __construct( $theContainer = NULL,
								 $theIdentifier = NULL,
								 $theModifiers = kFLAG_DEFAULT )
	{
		//
		// Enforce encoded flag.
		//
		$theModifiers |= kFLAG_STATE_ENCODED;
		
		//
		// Call parent method.
		//
		parent::__construct( $theContainer, $theIdentifier, $theModifiers );
		
		//
		// Set inited status.
		//
		$this->_IsInited( $this->_IsInited() &&
						  $this->offsetExists( kTAG_NAME ) &&
						  $this->offsetExists( kOFFSET_EMAIL ) &&
						  $this->offsetExists( kOFFSET_PASSWORD ) );
		
	} // Constructor.

		

/*=======================================================================================
 *																						*
 *								PUBLIC MEMBER INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Password																		*
	 *==================================================================================*/

	/**
	 * Manage user password.
	 *
	 * This method can be used to manage the user {@link kOFFSET_PASSWORD password}, it uses
	 * the standard accessor {@link CAttribute::ManageOffset() method} to manage the
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
		return CAttribute::ManageOffset
				( $this, kOFFSET_PASSWORD, $theValue, $getOld );					// ==>

	} // Password.

	 
	/*===================================================================================
	 *	Role																			*
	 *==================================================================================*/

	/**
	 * Manage user roles.
	 *
	 * This method can be used to manage the user {@link kTAG_ROLE roles}, it uses
	 * the standard accessor {@link CAttribute::ManageArrayOffset() method} to manage the
	 * {@link kTAG_ROLE roles}:
	 *
	 * For a more thorough reference of how this method works, please consult the
	 * {@link CAttribute::ManageArrayOffset() CAttribute::ManageArrayOffset} method, in
	 * which the second parameter will be the constant {@link kTAG_ROLE kTAG_ROLE}.
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
	 * @see kTAG_ROLE
	 */
	public function Role( $theValue = NULL, $theOperation = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageArrayOffset
					( $this, kTAG_ROLE, $theValue, $theOperation, $getOld );		// ==>

	} // Role.

	 
	/*===================================================================================
	 *	Manager																			*
	 *==================================================================================*/

	/**
	 * Manage user manager.
	 *
	 * This method can be used to manage the user {@link kTAG_MANAGER manager}, it uses
	 * the standard accessor {@link CAttribute::ManageOffset() method} to manage the
	 * {@link kTAG_MANAGER offset}:
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
	 * The manager is either the user that created the current user or the user that is in
	 * charge of the current user.
	 *
	 * @param NULL|FALSE|string		$theValue			User password or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 */
	public function Manager( $theValue = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageOffset
				( $this, kTAG_MANAGER, $theValue, $getOld );						// ==>

	} // Manager.

		

/*=======================================================================================
 *																						*
 *								STATIC REFERENCE INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	HashIndex																		*
	 *==================================================================================*/

	/**
	 * Hash index.
	 *
	 * This method can be used to format an identifier provided as a string, it will be
	 * used by the {@link _id() _id} method to format the result of the
	 * {@link _index() _index} method. One can consider this as the index hashing method for
	 * all derived classes.
	 *
	 * In this class we take the provided {@link Code() code} and prefix it with the
	 * {@link kENTITY_USER kENTITY_USER} token, the result will be
	 * {@link CDataTypeBinary hashed}.
	 *
	 * @param string				$theValue			Value to hash.
	 *
	 * @static
	 * @return string
	 */
	static function HashIndex( $theValue )
	{
		return new CDataTypeBinary(
					md5( kENTITY_USER.kTOKEN_CLASS_SEPARATOR.$theValue,
						 TRUE ) );													// ==>
	
	} // HashIndex.

		

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
	 * {@link kFLAG_STATE_INITED status}: this is set if the
	 * {@link kTAG_NAME name}, {@link kOFFSET_EMAIL e-mail},
	 * {@link kOFFSET_PASSWORD password} and the parent {@link kTAG_CODE code} are set.
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
			$this->_IsInited( $this->_IsInited() &&
							  $this->offsetExists( kTAG_NAME ) &&
							  $this->offsetExists( kOFFSET_EMAIL ) &&
							  $this->offsetExists( kOFFSET_PASSWORD ) );
	
	} // offsetSet.

	 
	/*===================================================================================
	 *	offsetUnset																		*
	 *==================================================================================*/

	/**
	 * Reset a value for a given offset.
	 *
	 * We overload this method to manage the {@link _IsInited() inited}
	 * {@link kFLAG_STATE_INITED status}: this is set if the
	 * {@link kTAG_NAME name}, {@link kOFFSET_EMAIL e-mail},
	 * {@link kOFFSET_PASSWORD password} and the parent {@link kTAG_CODE code} are set.
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
			$this->_IsInited( $this->_IsInited() &&
							  $this->offsetExists( kTAG_NAME ) &&
							  $this->offsetExists( kOFFSET_EMAIL ) &&
							  $this->offsetExists( kOFFSET_PASSWORD ) );
	
	} // offsetUnset.

		

/*=======================================================================================
 *																						*
 *								PROTECTED PERSISTENCE UTILITIES							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_PrepareCommit																	*
	 *==================================================================================*/

	/**
	 * Normalise parameters of a store.
	 *
	 * We overload this method to add the {@link kENTITY_USER kENTITY_USER}
	 * {@link Type() type} to the object prior {@link Commit() saving} it and we initialise
	 * the user {@link Code() code}, if empty, with the {@link Email() e-mail}.
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
		// Add user type.
		//
		$this->Kind( kENTITY_USER, TRUE );
		
		//
		// Set global identifier.
		//
		$this[ kTAG_GID ] = kENTITY_USER.kTOKEN_CLASS_SEPARATOR.$this->_index();
		
		//
		// Handle manager.
		//
		$this->_ParseReferences( kTAG_MANAGER, $theContainer, $theModifiers );
		
	} // _PrepareCommit.

	 

} // class CUser.


?>
