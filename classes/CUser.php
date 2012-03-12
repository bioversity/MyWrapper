<?php

/**
 * <i>CUser</i> class definition.
 *
 * This file contains the class definition of <b>CUser</b> which represents a class mapping
 * a general purpose user.
 *
 *	@package	Objects
 *	@subpackage	Entities
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 21/02/2012
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
require_once( kPATH_LIBRARY_SOURCE."CPersistentUnitObject.php" );

/**
 * User ancestor.
 *
 * This class is the ancestor of user classes in this library, it implements an object that
 * represents a basic user. This object features a minimum set of properties that can be set
 * via {@link Offsets.inc.php offsets}.
 *
 * This class implements only the required attributes of a user:
 *
 * <ul>
 *	<li><i>{@link kTAG_CODE kTAG_CODE}</i>: The user code. This required property represents
 *		the user code. If empty, by default it will be initialised as the user's
 *		{@link kOFFSET_EMAIL e-mail}. Although no two users should share the same value, in
 *		this class we do not link the object {@link kTAG_ID_NATIVE ID} to this property.
 *		The class features a member accessor {@link Code() method} to manage this property.
 *	<li><i>{@link kOFFSET_PASSWORD kOFFSET_PASSWORD}</i>: This offset represents the user
 *		access password, it will be used in the authentication process. This property is
 *		not associated with any specific offset, but it is stored 
 *		The class features a member accessor {@link Password() method} to manage this
 *		property.
 *	<li><i>{@link kTAG_NAME kTAG_NAME}</i>: This offset represents the user full name.
 *		The class features a member accessor {@link Name() method} to manage this property.
 *	<li><i>{@link kOFFSET_EMAIL kOFFSET_EMAIL}</i>: This offset represents the user e-mail.
 *		The class features a member accessor {@link Email() method} to manage this property.
 * </ul>
 *
 * All the above attributes are required prior to {@link Commit() committing} an object: the
 * object's {@link _IsInited() inited} {@link kFLAG_STATE_INITED status} depends on having
 * all of the above offsets set.
 *
 * By default, the object's unique {@link kTAG_ID_NATIVE identifier} will be provided by the
 * system, whenever a new user is created, we shall use the
 * {@link kTAG_ID_NATIVE identifier} provided by MongoDB; this value should never be
 * changed.
 *
 * This class also features a static {@link DefaultCollection() method} that should return
 * the default collection in which to store such objects.
 *
 *	@package	Objects
 *	@subpackage	Entities
 */
class CUser extends CPersistentUnitObject
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
	 * {@link Code() code}, {@link Password() password}, {@link Name() name} and
	 * {@link Email() e-mail} elements are set.
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
						  $this->offsetExists( kOFFSET_PASSWORD ) &&
						  $this->offsetExists( kTAG_NAME ) &&
						  $this->offsetExists( kOFFSET_EMAIL ) );
		
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
	 *	Email																			*
	 *==================================================================================*/

	/**
	 * Manage user e-mail.
	 *
	 * This method can be used to manage the user {@link kOFFSET_EMAIL e-mail}, it uses the
	 * standard accessor {@link _ManageOffset() method} to manage the
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
	 * @param NULL|FALSE|string		$theValue			User e-mail or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 */
	public function Email( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kOFFSET_EMAIL, $theValue, $getOld );			// ==>

	} // Email.

		

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
			$this->_IsInited( $this->offsetExists( kTAG_CODE ) &&
							  $this->offsetExists( kOFFSET_PASSWORD ) &&
							  $this->offsetExists( kTAG_NAME ) &&
							  $this->offsetExists( kOFFSET_EMAIL ) );
	
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
		$this->_IsInited( $this->offsetExists( kTAG_CODE ) &&
						  $this->offsetExists( kOFFSET_PASSWORD ) &&
						  $this->offsetExists( kTAG_NAME ) &&
						  $this->offsetExists( kOFFSET_EMAIL ) );
	
	} // offsetUnset.

		

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
			$this->offsetSet( kTAG_CODE, $this->offsetGet( kOFFSET_EMAIL ) );
		
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
					  		 kOFFSET_EMAIL
					  	=> ( $this->offsetExists( kOFFSET_EMAIL ) )
					  	   ? 'OK': 'Missing' ) );								// !@! ==>
	
		//
		// Call parent method.
		//
		parent::_PrepareStore( $theContainer, $theIdentifier );
		
	} // _PrepareStore.

	 

} // class CUser.


?>
