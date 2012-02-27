<?php

/**
 * <i>CStatusObject</i> class definition.
 *
 * This file contains the class definition of <b>CStatusObject</b> which extends its
 * {@link CArrayObject ancestor} to handle states.
 *
 *	@package	Framework
 *	@subpackage	Core
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 14/02/2012
 */

/*=======================================================================================
 *																						*
 *									CStatusObject.php									*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This includes the ancestor class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CArrayObject.php" );

/**
 *	Status object.
 *
 * This class represents the ancestor of classes that must keep track of states or status.
 *
 * The status is recorded in a {@link $mStatus property} that does not belong to the
 * object's array data store. The data member consists of a 4 byte bit field in which the
 * first 31 elements are used to record on/off states.
 *
 * <i>Note: we only use the first 31 bits because at this time PHP does not support 64 bit
 * integers and changing the last bit usually results in inversing the other bits.</i>
 *
 * The class manages these states through a protected interface:
 *
 * <ul>
 *	<li><b>{@link _IsInited() _IsInited}</b>: This state indicates that an object has been
 *		initialised to a {@link kFLAG_STATE_INITED state} that allows it to be operated at
 *		least to a minimum extent. 
 *		Objects that are not in this state may raise {@link kERROR_NOT_INITED exceptions}
 *		when required resources cannot be found.
 *		By default objects are instantiated with this status <i>off</i>.
 *	<li><b>{@link _IsDirty() _IsDirty}</b>: This {@link kFLAG_STATE_DIRTY status} is
 *		generally set each time a change is made to the object's persistent data.
 *		By default objects are instantiated with this status <i>off</i>.
 *		This class will set this state by default when {@link offsetSet() setting} or
 *		{@link offsetUnset() deleting} array store elements.
 * </ul>
 *
 * @package		Framework
 * @subpackage	Core
 */
class CStatusObject extends CArrayObject
{
	/**
	 * Object status.
	 *
	 * This data member is a bitfield that holds the object status.
	 *
	 * @var bitfield
	 */
	 protected $mStatus = kFLAG_DEFAULT;

		

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
	 * We override this method to handle the {@link _IsDirty() dirty}
	 * {@link kFLAG_STATE_DIRTY flag}: on offset value changes we set the state on.
	 *
	 * @param string				$theOffset			Offset.
	 * @param string|NULL			$theValue			Value to set at offset.
	 *
	 * @access public
	 *
	 * @uses _IsDirty()
	 *
	 * @see kFLAG_STATE_DIRTY
	 */
	public function offsetSet( $theOffset, $theValue )
	{
		//
		// Check for changes.
		//
		if( $this->offsetGet( $theOffset ) !== $theValue )
			$this->_IsDirty( TRUE );
		
		//
		// Call parent method.
		//
		parent::offsetSet( $theOffset, $theValue );
	
	} // offsetSet.

	 
	/*===================================================================================
	 *	offsetUnset																		*
	 *==================================================================================*/

	/**
	 * Reset a value for a given offset.
	 *
	 * We override this method to handle the {@link _IsDirty() dirty}
	 * {@link kFLAG_STATE_DIRTY flag}: on offset value changes we set the state on.
	 *
	 * @param string				$theOffset			Offset.
	 *
	 * @access public
	 *
	 * @uses _IsDirty()
	 *
	 * @see kFLAG_STATE_DIRTY
	 */
	public function offsetUnset( $theOffset )
	{
		//
		// Check for changes.
		//
		if( $this->offsetGet( $theOffset ) !== NULL )
			$this->_IsDirty( TRUE );
		
		//
		// Call parent method.
		//
		parent::offsetUnset( $theOffset );
	
	} // offsetUnset.

		

/*=======================================================================================
 *																						*
 *							PROTECTED STATE MANAGEMENT INTERFACE						*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_Status																			*
	 *==================================================================================*/

	/**
	 * Set or retrieve the object status.
	 *
	 * This method can be used to manage the status bitfield as a whole, allowing to set and
	 * retrieve the whole set of states.
	 *
	 * The parameter can take the following values:
	 *
	 * <ul>
	 *	<li><i>NULL</i>: The method will return the current object's states bitfield.
	 *	<li><i>FALSE</i>: If this value is passed, the method will reset the object's status
	 *		by settig it to the {@link kFLAG_DEFAULT default} value.
	 *	<li><i>other</i>: In this case the parameter will be interpreted as a 31 bits
	 *		bit-field value and the data member will be replaced with it.
	 * </ul>
	 *
	 * In all cases the method will return the status <i>after</i> it was eventually
	 * modified.
	 *
	 * @param NULL|FALSE|bitfield	$theState			NULL, FALSE or new status.
	 *
	 * @access private
	 * @return bitfield
	 *
	 * @uses _ManageBitField()
	 *
	 * @see kFLAG_DEFAULT_MASK
	 */
	protected function _Status( $theState = NULL )
	{
		return $this->_ManageBitField( $this->mStatus,
									   kFLAG_DEFAULT_MASK,
									   $theState );									// ==>
	
	} // _Status

		
	/*===================================================================================
	 *	_IsInited																		*
	 *==================================================================================*/

	/**
	 * Manage inited status.
	 *
	 * This method can be used to get or set the object's inited state.
	 *
	 * An object becomes inited when it has all the required elements necessary for it to be
	 * correctly used or persistently stored. Such a state indicates that at least the
	 * minimum required information was initialised in the object.
	 *
	 * The counterpart state indicates that the object still lacks the necessary elements to
	 * successfully operate the object.
	 *
	 * This method operates by setting or clearing the {@link kFLAG_STATE_INITED inited}
	 * {@link _Status() status} flag.
	 *
	 * The method features a single parameter:
	 *
	 * <ul>
	 *	<li><i>NULL</i>: The method will return <i>TRUE</i> if the object is this state, or
	 *		<i>FALSE</i> if the object is not in this state.
	 *	<li><i>TRUE</i>: The method will set the object to this state.
	 *	<li><i>FALSE</i>: The method will reset this state.
	 * </ul>
	 *
	 * In all cases the method will return the state <i>after</i> it was eventually
	 * modified.
	 *
	 * @param mixed					$theState			TRUE, FALSE or NULL.
	 *
	 * @access private
	 * @return boolean
	 *
	 * @uses _ManageBitField()
	 *
	 * @see kFLAG_STATE_INITED
	 */
	protected function _IsInited( $theState = NULL )
	{
		return $this->_ManageBitField( $this->mStatus,
									   kFLAG_STATE_INITED,
									   $theState );									// ==>
	
	} // _IsInited.

	 
	/*===================================================================================
	 *	_IsDirty																		*
	 *==================================================================================*/

	/**
	 * Manage dirty status.
	 *
	 * This method can be used to get or set the object's dirty state.
	 *
	 * A dirty object is one that was modified since the last time this state was probed. In
	 * general, this state should be set whenever the persistent properties of the object
	 * are modified.
	 *
	 * In this class we automatically set this state when {@link offsetSet() setting} or
 	 * {@link offsetUnset() deleting} array store elements.
	 *
	 * The method features a single parameter:
	 *
	 * <ul>
	 *	<li><i>NULL</i>: The method will return <i>TRUE</i> if the object is dirty, or
	 *		<i>FALSE</i> if the object is {@link _IsClean() clean}.
	 *	<li><i>TRUE</i>: The method will set the object to dirty.
	 *	<li><i>FALSE</i>: The method will set the object to {@link _IsClean() clean}.
	 * </ul>
	 *
	 * In all cases the method will return the state <i>after</i> it was eventually
	 * modified.
	 *
	 * @param mixed					$theState			TRUE, FALSE or NULL.
	 *
	 * @access private
	 * @return boolean
	 *
	 * @uses _ManageBitField()
	 *
	 * @see kFLAG_STATE_DIRTY
	 */
	protected function _IsDirty( $theState = NULL )
	{
		return $this->_ManageBitField( $this->mStatus,
									   kFLAG_STATE_DIRTY,
									   $theState );									// ==>
	
	} // _IsDirty.

		

/*=======================================================================================
 *																						*
 *							PROTECTED MEMBER MANAGEMENT INTERFACE						*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_ManageBitField																	*
	 *==================================================================================*/

	/**
	 * Manage bit-field property.
	 *
	 * This method can be used to manage a bitfield property, it accepts the following
	 * parameters:
	 *
	 * <ul>
	 *	<li><b>&$theField</b>: Reference to the bit-field property.
	 *	<li><b>$theMask</b>: Bit-field mask.
	 *	<li><b>$theState</b>: State or operator:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the current value masked by the next parameter.
	 *		<li><i>FALSE</i>: Reset the current value using the next parameter as the mask.
	 *		<li><i>other</i>: Set the current value to this one, masking the first 31 bits.
	 *	 </ul>
	 * </ul>
	 *
	 * In all cases the method will return the status <i>after</i> it was eventually
	 * modified.
	 *
	 * @param reference			   &$theField			Bit-field reference.
	 * @param bitfield				$theMask			Bit-field mask.
	 * @param mixed					$theState			Value or operator.
	 *
	 * @access protected
	 * @return bitfield
	 *
	 * @see kFLAG_DEFAULT_MASK
	 */
	protected function _ManageBitField( &$theField, $theMask, $theState = NULL )
	{
		//
		// Normalise mask.
		//
		$theMask &= kFLAG_DEFAULT_MASK;
		
		//
		// Modify status.
		//
		if( $theState !== NULL )
		{
			if( $theState === FALSE )
				$theField &= (~ $theMask);
			else
				$theField |= $theMask;
		}
		
		return $theField & $theMask;												// ==>
	
	} // _ManageBitField

	 

} // class CStatusObject.


?>
