<?php

/**
 * <i>CGraphNode</i> class definition.
 *
 * This file contains the class definition of <b>CGraphNode</b> which represents the
 * ancestor of all graph nodes in this library.
 *
 * The class is derived from {@link CPersistentObject CPersistentObject}, of which it
 * inherits the persistence framework, but it differs profoundly in that, although the class
 * is an ArrayObject, the actual array that it manages is the properties array of the node.
 * In other words, when you add, retrieve and delete properties, you are not doing so to
 * the internal array, but to the node's properties array.
 *
 * The class features a single property, the {@link Node() node} which contains a Neo4j node
 * reference.
 *
 * <i>Note that the class will not cast to an array correctly, you must use the
 * {@link getArrayCopy() getArrayCopy} method for that.</i>
 *
 *	@package	MyWrapper
 *	@subpackage	Persistence
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 18/04/2012
 */

/*=======================================================================================
 *																						*
 *									CGraphNode.php										*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CPersistentObject.php" );

/**
 * Local defines.
 *
 * This include file contains all local definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CGraphNode.inc.php" );

/**
 * Graph node.
 *
 * This class implements a graph node.
 *
 *	@package	MyWrapper
 *	@subpackage	Persistence
 */
class CGraphNode extends CPersistentObject
{
	/**
	 * Node.
	 *
	 * This data member holds the Neo4j node.
	 *
	 * @var Everyman\Neo4j\Node
	 */
	 protected $mNode = NULL;

		

/*=======================================================================================
 *																						*
 *								PUBLIC MEMBER INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Node																			*
	 *==================================================================================*/

	/**
	 * Manage native node.
	 *
	 * This method can be used to manage the native node reference, it uses the standard
	 * accessor {@link CObject::ManageMember() method} to manage the property:
	 *
	 * <ul>
	 *	<li><b>$theValue</b>: The value or operation:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the current value.
	 *		<li><i>FALSE</i>: Delete the value.
	 *		<li><i>Everyman\Neo4j\Node</i>: Set value.
	 *		<li><i>other</i>: Raise exception.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value <i>before</i> it was eventually modified.
	 *		<li><i>FALSE</i>: Return the value <i>after</i> it was eventually modified.
	 *	 </ul>
	 * </ul>
	 *
	 * @param mixed					$theValue			Node or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return Everyman\Neo4j\Node
	 */
	public function Node( $theValue = NULL, $getOld = FALSE )
	{
		//
		// Check provided value.
		//
		if( ($theValue !== NULL)
		 && ($theValue !== FALSE)
		 && (! $theValue instanceof Everyman\Neo4j\Node) )
			throw new CException
					( "Unsupported node type",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Node' => $theValue ) );							// !@! ==>
		
		//
		// Handle data.
		//
		$save = CObject::ManageMember( $this->mNode, $theValue, $getOld );
				
		//
		// Set status.
		//
		if( $theValue !== NULL )
		{
			//
			// Set dirty flag.
			//
			$this->_IsDirty( TRUE );
			
			//
			// Set inited flag.
			//
			$this->_IsInited( $this->Node() !== NULL );
		}
		
		return $save;																// ==>

	} // Node.

		

/*=======================================================================================
 *																						*
 *								PUBLIC ARRAY ACCESS INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	offsetExists																	*
	 *==================================================================================*/

	/**
	 * Check whether a given offset exists.
	 *
	 * We overload this method to wrap the internal array over the node properties.
	 *
	 * @param string				$theOffset			Offset.
	 *
	 * @access public
	 * @return boolean
	 */
	public function offsetExists( $theOffset )
	{
		//
		// Require node.
		//
		if( ($node = $this->Node()) !== NULL )
			return array_key_exists( $theOffset, $node->getProperties() );			// ==>
		
		return FALSE;																// ==>
	
	} // offsetExists.

	 
	/*===================================================================================
	 *	offsetGet																		*
	 *==================================================================================*/

	/**
	 * Return a value at a given offset.
	 *
	 * We overload this method to wrap the internal array over the node properties.
	 *
	 * In this class no offset may have a <i>NULL</i> value, if this method returns a
	 * <i>NULL</i> value, it means that the offset doesn't exist.
	 *
	 * @param string				$theOffset			Offset.
	 *
	 * @access public
	 * @return mixed
	 */
	public function offsetGet( $theOffset )
	{
		//
		// Require node.
		//
		if( ($node = $this->Node()) !== NULL )
			return $node->getProperty( $theOffset );								// ==>
		
		return NULL;																// ==>
	
	} // offsetGet.

	 
	/*===================================================================================
	 *	offsetSet																		*
	 *==================================================================================*/

	/**
	 * Set a value for a given offset.
	 *
	 * We overload this method to wrap the internal array over the node properties.
	 *
	 * In this class we delete the entry if the value is <i>NULL</i>.
	 *
	 * @param string				$theOffset			Offset.
	 * @param string|NULL			$theValue			Value to set at offset.
	 *
	 * @access public
	 */
	public function offsetSet( $theOffset, $theValue )
	{
		//
		// Require node.
		//
		if( ($node = $this->Node()) !== NULL )
		{
			//
			// Set value.
			//
			if( $theValue !== NULL )
			{
				//
				// Provided offset.
				//
				if( strlen( $theOffset ) )
					$node->setProperty( $theOffset, $theValue );
				
				//
				// Omitted offset.
				//
				else
				{
					$props = $node->getProperties();
					$props[] = $theValue;
					$node->setProperties( $props );
				}
			
			} // Provided value.
			
			//
			// Delete offset.
			//
			else
				$node->removeProperty( $theOffset );
		
		} // Has node.
	
	} // offsetSet.

	 
	/*===================================================================================
	 *	offsetUnset																		*
	 *==================================================================================*/

	/**
	 * Reset a value for a given offset.
	 *
	 * We overload this method to wrap the internal array over the node properties.
	 *
	 * @param string				$theOffset			Offset.
	 *
	 * @access public
	 */
	public function offsetUnset( $theOffset )
	{
		//
		// Require node.
		//
		if( ($node = $this->Node()) !== NULL )
			$node->removeProperty( $theOffset );
	
	} // offsetUnset.

	 
	/*===================================================================================
	 *	append																			*
	 *==================================================================================*/

	/**
	 * Append a value.
	 *
	 * We overload this method to wrap the internal array over the node properties.
	 *
	 * @param mixed					$theValue			Value.
	 *
	 * @access public
	 */
	public function append( $theValue )
	{
		//
		// Require node.
		//
		if( ($node = $this->Node()) !== NULL )
		{
			$props = $node->getProperties();
			$props[] = $theValue;
			$node->setProperties( $props );
		}
	
	} // append.

	 
	/*===================================================================================
	 *	asort																			*
	 *==================================================================================*/

	/**
	 * Sort array by values.
	 *
	 * We overload this method to wrap the internal array over the node properties.
	 *
	 * @access public
	 */
	public function asort()
	{
		//
		// Require node.
		//
		if( ($node = $this->Node()) !== NULL )
		{
			$props = $node->getProperties();
			asort( $props );
			$node->setProperties( $props );
		}
	
	} // asort.

	 
	/*===================================================================================
	 *	ksort																			*
	 *==================================================================================*/

	/**
	 * Sort array by keys.
	 *
	 * We overload this method to wrap the internal array over the node properties.
	 *
	 * @access public
	 */
	public function ksort()
	{
		//
		// Require node.
		//
		if( ($node = $this->Node()) !== NULL )
		{
			$props = $node->getProperties();
			ksort( $props );
			$node->setProperties( $props );
		}
	
	} // ksort.

	 
	/*===================================================================================
	 *	natcasesort																		*
	 *==================================================================================*/

	/**
	 * Sort array by case insensitive natural order algorythm.
	 *
	 * We overload this method to wrap the internal array over the node properties.
	 *
	 * @access public
	 */
	public function natcasesort()
	{
		//
		// Require node.
		//
		if( ($node = $this->Node()) !== NULL )
		{
			$props = $node->getProperties();
			natcasesort( $props );
			$node->setProperties( $props );
		}
	
	} // natcasesort.

	 
	/*===================================================================================
	 *	natsort																			*
	 *==================================================================================*/

	/**
	 * Sort array by natural order algorythm.
	 *
	 * We overload this method to wrap the internal array over the node properties.
	 *
	 * @access public
	 */
	public function natsort()
	{
		//
		// Require node.
		//
		if( ($node = $this->Node()) !== NULL )
		{
			$props = $node->getProperties();
			natsort( $props );
			$node->setProperties( $props );
		}
	
	} // natsort.

	 
	/*===================================================================================
	 *	count																			*
	 *==================================================================================*/

	/**
	 * Count number of elements.
	 *
	 * We overload this method to wrap the internal array over the node properties.
	 *
	 * Note that if the node exists the method will return an integer, if not, it will
	 * return <i>NULL</i>.
	 *
	 * @access public
	 * @return mixed
	 */
	public function count()
	{
		//
		// Require node.
		//
		if( ($node = $this->Node()) !== NULL )
			return count( $node->getProperties() );									// ==>
		
		return NULL;																// ==>
	
	} // count.

	 
	/*===================================================================================
	 *	exchangeArray																	*
	 *==================================================================================*/

	/**
	 * Exchange arrays.
	 *
	 * We overload this method to wrap the internal array over the node properties.
	 *
	 * Note that if the node exists the method will return an array, if not, it will
	 * return an empty array.
	 *
	 * @param mixed					$theValue			Value.
	 *
	 * @access public
	 * @return mixed
	 */
	public function exchangeArray( $theValue )
	{
		//
		// Require node.
		//
		if( ($node = $this->Node()) !== NULL )
		{
			$old = $node->getProperties();
			$node->setProperties( $theValue );
			
			return $old;															// ==>
		}
		
		return Array();																// ==>
	
	} // exchangeArray.

	 
	/*===================================================================================
	 *	getArrayCopy																	*
	 *==================================================================================*/

	/**
	 * Create a copy of the array.
	 *
	 * We overload this method to wrap the internal array over the node properties.
	 *
	 * Note that if the node exists the method will return an array, if not, it will
	 * return an empty array.
	 *
	 * @access public
	 * @return mixed
	 */
	public function getArrayCopy()
	{
		//
		// Require node.
		//
		if( ($node = $this->Node()) !== NULL )
			return $node->getProperties();											// ==>
		
		return Array();																// ==>
	
	} // getArrayCopy.

	 
	/*===================================================================================
	 *	getIterator																		*
	 *==================================================================================*/

	/**
	 * Get array iterator.
	 *
	 * We overload this method to wrap the internal array over the node properties.
	 *
	 * Note that if the node exists the method will return an array, if not, it will
	 * return an empty array.
	 *
	 * @access public
	 * @return ArrayIterator
	 */
	public function getIterator()
	{
		//
		// Require node.
		//
		if( ($node = $this->Node()) !== NULL )
			return new ArrayIterator( $node->getProperties() );						// ==>
		
		return new ArrayIterator();													// ==>
	
	} // getIterator.

		

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
	 * We overload this method to wrap the internal array over the node properties.
	 *
	 * @access public
	 * @return array
	 */
	public function keys()
	{
		//
		// Require node.
		//
		if( ($node = $this->Node()) !== NULL )
			return array_keys( $node->getProperties() );							// ==>
		
		return Array();																// ==>
	
	} // keys.

	 
	/*===================================================================================
	 *	values																			*
	 *==================================================================================*/

	/**
	 * Return object's values.
	 *
	 * We overload this method to wrap the internal array over the node properties.
	 *
	 * @access public
	 * @return array
	 */
	public function values()
	{
		//
		// Require node.
		//
		if( ($node = $this->Node()) !== NULL )
			return array_values( $node->getProperties() );							// ==>
		
		return Array();																// ==>
	
	} // keys.

		

/*=======================================================================================
 *																						*
 *								PROTECTED PERSISTENCE INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_Create																			*
	 *==================================================================================*/

	/**
	 * Create object.
	 *
	 * We {@link CPersistentObject::_Create() overload} this method to handle Neo4j nodes.
	 *
	 * @param reference			   &$theContent			Object data content.
	 *
	 * @access protected
	 * @return boolean
	 *
	 * @throws {@link CException CException}
	 *
	 * @see kERROR_UNSUPPORTED
	 */
	protected function _Create( &$theContent )
	{
		//
		// Handle node.
		//
		if( $theContent instanceof Everyman\Neo4j\Node )
		{
			//
			// Save node.
			//
			$this->Node( $theContent );
			
			return TRUE;															// ==>
		
		} // Received node.
		
		//
		// Handle empty node.
		//
		if( $theContent instanceof Everyman\Neo4j\Client )
			return FALSE;															// ==>

		return parent::_Create( $theContent );										// ==>
	
	} // _Create.

	 
	/*===================================================================================
	 *	_Commit																			*
	 *==================================================================================*/

	/**
	 * Store object in container.
	 *
	 * In this class we save the node and return its ID.
	 *
	 * We ignore the identifier here.
	 *
	 * @param reference			   &$theContainer		Object container.
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Commit modifiers.
	 *
	 * @access protected
	 * @return mixed
	 */
	protected function _Commit( &$theContainer, &$theIdentifier, &$theModifiers )
	{
		//
		// Get native node.
		//
		$node = $this->Node();
		
		//
		// Handle delete.
		//
		if( $theModifiers & kFLAG_PERSIST_DELETE )
		{
			//
			// Delete node.
			//
			if( $node->hasId()								// Prevent exceptions.
			 && $theContainer->deleteNode( $node ) )
				$this->Node( $theContainer->makeNode() );
			
			return $node->getId();													// ==>
		
		} // Delete.
		
		//
		// Save node.
		//
		$node->save();
		
		//
		// Copy node.
		//
		$this->Node( $node );
		
		return $node->getID();														// ==>
	
	} // _Commit.

	 
	/*===================================================================================
	 *	_Load																			*
	 *==================================================================================*/

	/**
	 * Find object.
	 *
	 * In this class we try to load the node.
	 *
	 * @param reference			   &$theContainer		Object container.
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Create options.
	 *
	 * @access protected
	 * @return mixed
	 */
	protected function _Load( &$theContainer, &$theIdentifier, &$theModifiers )
	{
		return $theContainer->getNode( $theIdentifier );							// ==>
	
	} // _Load.

		

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
	 * In this class we check if the provided content is supported: if the identifier was
	 * not provided it must be an array or ArrayObject.
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
		// Check container.
		//
		if( $theContainer === NULL )
			throw new CException
					( "Missing object container",
					  kERROR_OPTION_MISSING,
					  kMESSAGE_TYPE_ERROR );									// !@! ==>
			
		//
		// Call parent method.
		//
		parent::_PrepareCreate( $theContainer, $theIdentifier, $theModifiers );

		//
		// Check content.
		//
		if( ($theIdentifier === NULL)
		 && (! is_array( $theContainer ))
		 && (! $theContainer instanceof ArrayObject)
		 && (! $theContainer instanceof Everyman\Neo4j\Client) )
			throw new CException
					( "Unsupported container type",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Container' => $theContainer ) );					// !@! ==>
	
	} // _PrepareCreate.

	 
	/*===================================================================================
	 *	_PrepareLoad																	*
	 *==================================================================================*/

	/**
	 * Normalise parameters of a find.
	 *
	 * In this class we check if the provided container is supported.
	 *
	 * @param reference			   &$theContainer		Object container.
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Create modifiers.
	 *
	 * @access protected
	 *
	 * @throws {@link CException CException}
	 */
	protected function _PrepareLoad( &$theContainer, &$theIdentifier, &$theModifiers )
	{
		//
		// Call parent method.
		//
		parent::_PrepareLoad( $theContainer, $theIdentifier, $theModifiers );
	
		//
		// Check if container is supported.
		//
		if( ! $theContainer instanceof Everyman\Neo4j\Client )
			throw new CException
					( "Unsupported container type",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Container' => $theContainer ) );					// !@! ==>

	} // _PrepareLoad.

	 
	/*===================================================================================
	 *	_PrepareCommit																	*
	 *==================================================================================*/

	/**
	 * Normalise before a store.
	 *
	 * In this class we check if the provided container is supported.
	 *
	 * @param reference			   &$theContainer		Object container.
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Commit modifiers.
	 *
	 * @access protected
	 *
	 * @throws {@link CException CException}
	 */
	protected function _PrepareCommit( &$theContainer, &$theIdentifier, &$theModifiers )
	{
		//
		// Call parent method.
		//
		parent::_PrepareCommit( $theContainer, $theIdentifier, $theModifiers );
	
		//
		// Check if container is supported.
		//
		if( ! $theContainer instanceof Everyman\Neo4j\Client )
			throw new CException
					( "Unsupported container type",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Container' => $theContainer ) );					// !@! ==>
	
	} // _PrepareCommit.

	 
	/*===================================================================================
	 *	_FinishCreate																	*
	 *==================================================================================*/

	/**
	 * Normalise after a {@link _Create() create}.
	 *
	 * In this class we create an empty node by default.
	 *
	 * @param reference			   &$theContainer		Object container.
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Create modifiers.
	 *
	 * @access protected
	 */
	protected function _FinishCreate( &$theContainer, &$theIdentifier, &$theModifiers )
	{
		//
		// Create empty node.
		//
		if( $theContainer instanceof Everyman\Neo4j\Client )
		{
			//
			// Create node.
			//
			$this->Node( $theContainer->makeNode() );
			
			//
			// Set inited flag.
			//
			$this->_IsInited( TRUE );
		}
	
	} // _FinishCreate.

	 

} // class CGraphNode.


?>
