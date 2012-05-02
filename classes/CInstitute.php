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
require_once( kPATH_LIBRARY_SOURCE."CInstitute.inc.php" );

/**
 * Institute.
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
 * The unique identifier of this class is composed by the default {@link _index() index} of
 * thew object, prefixed by the {@link kENTITY_INST kENTITY_INST} token and the
 * {@link kTOKEN_CLASS_SEPARATOR kTOKEN_CLASS_SEPARATOR} token, this allows institutes and
 * other types of {@link CEntity entities} to share the same {@link Code() code}; this is
 * enforced both in the {@link HashIndex() HashIndex} method, to which you only need to pass
 * the user {@link Code() code}, and in the protected
 * {@link _PrepareCommit() _PrepareCommit} method which will place the resulting string in
 * the global {@link kTAG_GID identifier}.
 *
 *	@package	MyWrapper
 *	@subpackage	Entities
 */
class CInstitute extends CContact
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
	 * {@link Name() name} element is set.
	 *
	 * We also enforce the {@link _IsEncoded() encoded} {@link kFLAG_STATE_ENCODED flag}.
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
						  $this->offsetExists( kTAG_NAME ) );
		
	} // Constructor.

		

/*=======================================================================================
 *																						*
 *								PUBLIC MEMBER INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Acronym																			*
	 *==================================================================================*/

	/**
	 * Manage entity acronyms.
	 *
	 * This method can be used to handle the institute {@link kOFFSET_ACRONYM acronyms}
	 * list, it uses the standard accessor {@link _ManageArrayOffset() method} to manage the
	 * list of acronyms.
	 *
	 * Each element of this list should indicate an acronym by which one refers to the
	 * current institute, the nature and specifics of these elements is the responsibility
	 * of concrete classes.
	 *
	 * For a more thorough reference of how this method works, please consult the
	 * {@link _ManageArrayOffset() _ManageArrayOffset} method, in which the first parameter
	 * will be the constant {@link kOFFSET_ACRONYM kOFFSET_ACRONYM}.
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
	 * @see kOFFSET_ACRONYM
	 */
	public function Acronym( $theValue = NULL, $theOperation = NULL, $getOld = FALSE )
	{
		return $this->_ManageArrayOffset
					( kOFFSET_ACRONYM, $theValue, $theOperation, $getOld );			// ==>

	} // Acronym.

	 
	/*===================================================================================
	 *	URL																				*
	 *==================================================================================*/

	/**
	 * Manage institute urls.
	 *
	 * This method can be used to manage the institute {@link kOFFSET_URL URL} or web pages
	 * list, the method expects the following parameters:
	 *
	 * <ul>
	 *	<li><b>$theValue</b>: The value or operation:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the current value selected by the second parameter.
	 *		<li><i>FALSE</i>: Delete the value selected by the second parameter.
	 *		<li><i>other</i>: Set value selected by the second parameter.
	 *	 </ul>
	 *	<li><b>$theType</b>: The element type, kind or index:
	 *	 <ul>
	 *		<li><i>NULL</i>: This value indicates that the URL has no type or kind, in
	 *			general, when adding elements, this case applies to default elements.
	 *		<li><i>other</i>: All other types will be interpreted as the kind or type of
	 *			the URL.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value <i>before</i> it was eventually modified.
	 *		<li><i>FALSE</i>: Return the value <i>after</i> it was eventually modified.
	 *	 </ul>
	 * </ul>
	 *
	 * @param string				$theValue			URL or operation.
	 * @param mixed					$theType			Mailing address kind or index.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 */
	public function URL( $theValue = NULL, $theType = NULL, $getOld = FALSE )
	{
		return $this->_ManageKindArrayOffset
			( kOFFSET_URL, kTAG_KIND, $theType, $theValue, $getOld );				// ==>

	} // URL.

		

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
	 * {@link kENTITY_INST kENTITY_INST} token, the result will be
	 * {@link CDataTypeBinary hashed}
	 *
	 * @param string				$theValue			Value to hash.
	 *
	 * @static
	 * @return string
	 */
	static function HashIndex( $theValue )
	{
		return new CDataTypeBinary(
					md5( kENTITY_INST.kTOKEN_CLASS_SEPARATOR.$theValue,
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
							  $this->offsetExists( kTAG_NAME ) );
	
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
						  $this->offsetExists( kTAG_NAME ) );
	
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
	 * We overload this method to initialise the {@link kENTITY_INST kENTITY_INST}
	 * {@link Type() type} to the object prior {@link Commit() saving} it.
	 *
	 * We also force the {@link _IsEncoded() encoded} {@link kFLAG_STATE_ENCODED flag}.
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
		$this->Kind( kENTITY_INST, TRUE );
		
		//
		// Set global identifier.
		//
		$this[ kTAG_GID ] = kENTITY_INST.kTOKEN_CLASS_SEPARATOR.$this->_index();
		
	} // _PrepareCommit.

	 

} // class CInstitute.


?>
