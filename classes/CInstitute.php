<?php

/**
 * <i>CInstitute</i> class definition.
 *
 * This file contains the class definition of <b>CInstitute</b> which represents an
 * {@link CEntity entity} mapping a general purpose institute.
 *
 *	@package	MyWrapper
 *	@subpackage	Entities
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 04/04/2012
 */

/*=======================================================================================
 *																						*
 *									CInstitute.php										*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CContact.php" );

/**
 * Local defines.
 *
 * This include file contains the local class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CInstitute.inc.php" );

/**
 * User.
 *
 * This class overloads its {@link CContact ancestor} to implement an institute contact
 * entity.
 *
 * Institutes feature specific properties that are added to the inherited ones:
 *
 * <ul>
 *	<li><i>{@link kOFFSET_ACRONYM kOFFSET_ACRONYM}</i>: This offset represents the institute
 *		list of {@link Acronym() acronyms}.
 *	<li><i>{@link kOFFSET_URL kOFFSET_URL}</i>: This offset represents the institute list of
 *		URLs or web pages.
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
 *	@package	MyWrapper
 *	@subpackage	Entities
 */
class CInstitute extends CEntity
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
	 * the standard accessor {@link _ManageOffset() method} to manage the
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
 *							PROTECTED IDENTIFICATION INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_id																				*
	 *==================================================================================*/

	/**
	 * Return the object's unique identifier.
	 *
	 * In this class we hash the result of the {@link _index() _index} method, this means
	 * that we need to 
	 *
	 * @access protected
	 * @return mixed
	 */
	protected function _id()
	{
		//
		// In this class we hash the index value.
		//
		return new CDataTypeBinary( md5( $this->_index(), TRUE ) );
	
	} // _id.

	 
	/*===================================================================================
	 *	_index																			*
	 *==================================================================================*/

	/**
	 * Return the object's unique index.
	 *
	 * In this class we return a string composed of the following elements:
	 *
	 * <ul>
	 *	<li><i>{@link kENTITY_USER kENTITY_USER}</i>: This token defines the object domain
	 *		which is the users domain.
	 *	<li><i>{@link kTOKEN_CLASS_SEPARATOR kTOKEN_CLASS_SEPARATOR}</i>: This token is used
	 *		to separate a class from the rest of the code.
	 *	<li><i>{@link Code() Code}</i>: The user code.
	 * </ul>
	 *
	 * The concatenation of these three elements represents the unique identifier of the
	 * user.
	 *
	 * @access protected
	 * @return string
	 */
	protected function _index()
	{
		return kENTITY_USER.kTOKEN_CLASS_SEPARATOR.$this->Code();					// ==>
	
	} // _index.

		

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
	 * {@link Type() type} to the object prior {@link Commit() saving} it.
	 *
	 * We also initialise the user {@link Code() code}, if empty, with the
	 * {@link Email() e-mail}.
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
		// Initialise code.
		//
		if( $this->Code() === NULL )
			$this->Code( $this->Email() );
		
		//
		// Call parent method.
		//
		parent::_PrepareCommit( $theContainer, $theIdentifier, $theModifiers );
		
		//
		// Add user type.
		//
		$this->Type( kENTITY_USER, TRUE );
		
	} // _PrepareCommit.

	 

} // class CInstitute.


?>