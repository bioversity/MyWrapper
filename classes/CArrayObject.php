<?php

/**
 * <i>CArrayObject</i> class definition.
 *
 * This file contains the class definition of <b>CArrayObject</b> which represents the
 * ancestor of all classes in this library.
 *
 *	@package	MyWrapper
 *	@subpackage	Framework
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 07/04/2009
 *				2.00 23/11/2010
 *				3.00 13/02/2012
 */

/*=======================================================================================
 *																						*
 *									CArrayObject.php									*
 *																						*
 *======================================================================================*/

/**
 * Library.
 *
 * This include file contains the definitions of the {@link CObject CObject} class which
 * contains a static library of methods.
 */
require_once( kPATH_LIBRARY_SOURCE."CObject.php" );

/**
 * Attributes.
 *
 * This include file contains the definitions of the {@link CAttribute CAttribute} class
 * which can be used to manage the object's attributes.
 */
require_once( kPATH_LIBRARY_SOURCE."CAttribute.php" );

/**
 * Offsets.
 *
 * This include file contains all default offset definitions.
 */
require_once( kPATH_LIBRARY_DEFINES."Offsets.inc.php" );

/**
 *	Common ancestor.
 *
 * This class represents the ancestor of most entity mapped classes, it maps objects to an
 * array by deriving from {@link ArrayObject ArrayObject} and it defines a couple of general
 * purpose utility static methods.
 *
 * This class implements the following interfaces:
 *
 * <ul>
 *	<li><i>Offsets</i>: In this class there cannot be an offset with a <i>NULL</i> value,
 *		the offset itself should be {@link offsetUnset() deleted} in that case. Because of
 *		this we also override the inherited behaviour by suppressing notices and warnings
 *		when {@link offsetGet() getting} non-existant offsets.
 *	<li><i>String formatting</i>: We provide a generalised static method to
 *		{@link StringNormalise() format} strings which accepts a bitfield parameter that
 *		indicates which operation to perform, such as {@link kFLAG_MODIFIER_UTF8 UTF8}
 *		encode, {@link kFLAG_MODIFIER_LTRIM left} and {@link kFLAG_MODIFIER_RTRIM right}
 *		trim, {@link kFLAG_MODIFIER_NULL NULL} handling, {@link kFLAG_MODIFIER_NOCASE case}
 *		insensitive conversion, {@link kFLAG_MODIFIER_URL URL},
 *		{@link kFLAG_MODIFIER_HTML HTML} and {@link kFLAG_MODIFIER_HEX HEX} encoding and
 *		{@link kFLAG_MODIFIER_HASH hashing}.
 *	<li><i>Time formatting</i>: We provide a generalised static
 *		{@link DurationString() method} to display duration strings.
 * </ul>
 *
 *	@package	MyWrapper
 *	@subpackage	Framework
 */
class CArrayObject extends ArrayObject
{
		

/*=======================================================================================
 *																						*
 *								PUBLIC ARRAY ACCESS INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	offsetGet																		*
	 *==================================================================================*/

	/**
	 * Return a value at a given offset.
	 *
	 * This method should return the value corresponding to the provided offset.
	 *
	 * This method is overloaded to prevent notices from being triggered when seeking
	 * non-existing offsets.
	 *
	 * In this class no offset may have a <i>NULL</i> value, if this method returns a
	 * <i>NULL</i> value, it means that the offset doesn't exist.
	 *
	 * @param string				$theOffset			Offset.
	 *
	 * @access public
	 * @return mixed
	 */
	public function offsetGet( $theOffset )	{	return @parent::offsetGet( $theOffset );	}

	 
	/*===================================================================================
	 *	offsetSet																		*
	 *==================================================================================*/

	/**
	 * Set a value for a given offset.
	 *
	 * This method should set the provided value corresponding to the provided offset.
	 *
	 * This method is overloaded to prevent setting <i>NULL</i> values: if this is the case,
	 * the method will {@link offsetUnset() delete} the offset.
	 *
	 * @param string				$theOffset			Offset.
	 * @param string|NULL			$theValue			Value to set at offset.
	 *
	 * @access public
	 *
	 * @uses offsetUnset()
	 */
	public function offsetSet( $theOffset, $theValue )
	{
		//
		// Set value.
		//
		if( $theValue !== NULL )
			parent::offsetSet( $theOffset, $theValue );
		
		//
		// Delete offset.
		//
		else
			$this->offsetUnset( $theOffset );
	
	} // offsetSet.

	 
	/*===================================================================================
	 *	offsetUnset																		*
	 *==================================================================================*/

	/**
	 * Reset a value for a given offset.
	 *
	 * This method should reset the value corresponding to the provided offset.
	 *
	 * We overload this method to prevent notices on non-existing offsets.
	 *
	 * @param string				$theOffset			Offset.
	 *
	 * @access public
	 */
	public function offsetUnset( $theOffset )	{	@parent::offsetUnset( $theOffset );		}

		

/*=======================================================================================
 *																						*
 *								PUBLIC ARRAY UTILITY INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	keys																			*
	 *==================================================================================*/

	/**
	 * Return object's keys.
	 *
	 * This method has the same function as array's <i>array_keys()</i>, it will return an
	 * array comprised of all object's offsets.
	 *
	 * @access public
	 * @return array
	 */
	public function keys()							{	return array_keys( (array) $this );	}

	 
	/*===================================================================================
	 *	values																			*
	 *==================================================================================*/

	/**
	 * Return object's values.
	 *
	 * This method has the same function as array's <i>array_values()</i>, it will return an
	 * array comprised of all object's values.
	 *
	 * @access public
	 * @return array
	 */
	public function values()					{	return array_values( (array) $this );	}

	 

} // class CArrayObject.


?>
