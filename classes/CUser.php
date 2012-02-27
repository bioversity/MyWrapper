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
require_once( kPATH_LIBRARY_SOURCE."CMongoUnitObject.php" );

/**
 * User ancestor.
 *
 * This class is the ancestor of user classes in this library, it implements an object that
 * represents a user. This object features a minimum set of properties that can be set via
 * {@link Offsets.inc.php offsets}.
 *
 * This class implements only the required attributes of a user, derived classes may
 * implement more specialised functions.
 *
 * <ul>
 *	<li><i>{@link kTAG_CODE kTAG_CODE}</i>: This offset represents the user
 *		code, it is a string that represents the user access code.
 *	<li><i>{@link kOFFSET_PASSWORD kOFFSET_PASSWORD}</i>: This offset represents the user
 *		access password, it is a string.
 *	<li><i>{@link kTAG_NAME kTAG_NAME}</i>: This offset represents the user
 *		full name.
 *	<li><i>{@link kOFFSET_MAIL kOFFSET_MAIL}</i>: This offset represents the user
 *		e-mail address.
 * </ul>
 *
 * All the above attributes are required prior to {@link Commit() committing} an object: the
 * object's {@link _IsInited() inited} {@link kFLAG_STATE_INITED status} depends on having
 * all of the above offsets set.
 *
 * By default, the object's unique {@link kTAG_ID_NATIVE identifier} is linked to the user's
 * {@link kTAG_CODE code}, so you should not set the {@link kTAG_ID_NATIVE identifier}
 * manually. Also for that reason, it will not be permitted to change that value once an
 * object has been {@link Commit() committed}.
 *
 * This class also features a static {@link DefaultCollection() method} that should return
 * the default collection object.
 *
 *	@package	Objects
 *	@subpackage	Entities
 */
class CUser extends CMongoUnitObject
{
		

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
		return $this->_ManageOffset( kTAG_CODE, $theValue, $getOld );			// ==>

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
		return $this->_ManageOffset( kOFFSET_PASSWORD, $theValue, $getOld );			// ==>

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
		return $this->_ManageOffset( kTAG_NAME, $theValue, $getOld )	;		// ==>

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
	 * We overload this method to:
	 *
	 * <ul>
	 *	<li><i>Lock {@link kTAG_CODE code}</i>: The user's {@link kTAG_CODE code}
	 *		may not be modified once the object has been {@link Commit() committed}.
	 *	<li><i>Set {@link _IsInited() inited} {@link kFLAG_STATE_INITED status}</i>: The
	 *		object is considered initialised only if it has {@link kTAG_CODE code},
	 *		{@link kOFFSET_PASSWORD password}, {@link kTAG_NAME name} and
	 *		{@link kOFFSET_MAIL e-mail}.
	 * </ul>
	 *
	 * @param string				$theOffset			Offset.
	 * @param string|NULL			$theValue			Value to set at offset.
	 *
	 * @access public
	 *
	 * @throws CException
	 *
	 * @see kERROR_PROTECTED
	 *
	 * @uses _IsInited()
	 * @uses _IsCommitted()
	 */
	public function offsetSet( $theOffset, $theValue )
	{
		//
		// Lock code.
		//
		if( $this->_IsCommitted() )
		{
			//
			// Handle code.
			//
			if( ($theOffset == kTAG_CODE)
			 && ($theValue !== $this->offsetGet( kTAG_CODE )) )
				throw new CException
						( "Cannot modify this offset",
						  kERROR_PROTECTED,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Offset' => kTAG_CODE ) );				// !@! ==>
		}
		
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
							  $this->offsetExists( kOFFSET_MAIL ) );
	
	} // offsetSet.

	 
	/*===================================================================================
	 *	offsetUnset																		*
	 *==================================================================================*/

	/**
	 * Reset a value for a given offset.
	 *
	 * We overload this method to {@link _IsInited() check} if the object has all required
	 * attributes.
	 *
	 * @param string				$theOffset			Offset.
	 *
	 * @access public
	 *
	 * @throws CException
	 *
	 * @see kERROR_PROTECTED
	 *
	 * @uses _IsInited()
	 * @uses _IsCommitted()
	 */
	public function offsetUnset( $theOffset )
	{
		//
		// Lock code.
		//
		if( $this->_IsCommitted() )
		{
			//
			// Handle code.
			//
			if( ($theOffset == kTAG_CODE)
			 && ($theValue !== $this->offsetGet( kTAG_CODE )) )
				throw new CException
						( "Cannot modify this offset",
						  kERROR_PROTECTED,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Offset' => kTAG_CODE ) );				// !@! ==>
		}
		
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
						  $this->offsetExists( kOFFSET_MAIL ) );
	
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
	 * In this class the user's {@link kTAG_CODE code} is the unique identifier.
	 *
	 * @access protected
	 * @return string
	 */
	protected function _id()				{	return $this->offsetGet( kTAG_CODE );	}

		

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
		$this->_IsInited( $this->offsetExists( kTAG_CODE ) &&
						  $this->offsetExists( kOFFSET_PASSWORD ) &&
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

	 

} // class CUser.


?>
