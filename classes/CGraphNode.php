<?php

/**
 * <i>CGraphNode</i> class definition.
 *
 * This file contains the class definition of <b>CGraphNode</b> which represents the
 * ancestor of all graph nodes in this library.
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
 * The class is derived from {@link CPersistentObject CPersistentObject}, of which it
 * inherits the persistence framework, but it differs profoundly in that, although the class
 * is an ArrayObject, the actual array that it manages is the properties array of the node.
 * In other words, when you add, retrieve and delete properties, you are not doing so with
 * the internal array, but with the node's properties array.
 *
 * The class features a single data member, the {@link Node() node} which contains a Neo4j
 * node reference.
 *
 * Since the object requires a valid {@link Node() node} to exist at all times, this may
 * either be an empty one, if we wish to create a new node, or a {@link _Load() loaded}
 * one, this means that it is not possible to use the inherited interface of instantiating
 * the object with array contents, the container provided to the
 * {@link __construct() constructor} must be a Neo4j client.
 *
 * <i>Note that the class will not cast to an array correctly, you must use the
 * {@link getArrayCopy() getArrayCopy} method to get an array, if you know how to solve
 * this, please do it!</i>
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
	 * @var Everyman\Neo4j\PropertyContainer
	 */
	 protected $mNode = NULL;


		

/*=======================================================================================
 *																						*
 *											MAGIC										*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	__toString																		*
	 *==================================================================================*/

	/**
	 * Return object identifier.
	 *
	 * In this class we return the graph {@link Node() node} ID.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses Node()
	 */
	public function __toString()
	{
		$node = $this->Node();
		if( $node !== NULL )
		{
			if( $node->hasId() )
				return (string) $node->getId();										// ==>
		}
		
		return '';																	// ==>
	
	} // __toString.

		

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
	 *		<li><i>Everyman\Neo4j\PropertyContainer</i>: Set value.
	 *		<li><i>other</i>: Raise exception.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value <i>before</i> it was eventually modified.
	 *		<li><i>FALSE</i>: Return the value <i>after</i> it was eventually modified.
	 *	 </ul>
	 * </ul>
	 *
	 * The method will also set the {@link _IsDirty() dirty}
	 * {@link kFLAG_STATE_DIRTY status} and the {@link _IsInited() inited}
	 * {@link kFLAG_STATE_INITED status} if the node is provided.
	 *
	 * @param mixed					$theValue			Node or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return Everyman\Neo4j\PropertyContainer
	 *
	 * @uses CObject::ManageMember()
	 * @uses _IsDirty()
	 * @uses _IsInited()
	 */
	public function Node( $theValue = NULL, $getOld = FALSE )
	{
		//
		// Check provided value.
		//
		if( ($theValue !== NULL)
		 && ($theValue !== FALSE)
		 && (! $theValue instanceof Everyman\Neo4j\PropertyContainer) )
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
			$this->_IsInited( $this->_IsInited() &&
							  ($this->mNode !== NULL) );
		}
		
		return $save;																// ==>

	} // Node.

		

/*=======================================================================================
 *																						*
 *								PUBLIC RELATION INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	RelateTo																		*
	 *==================================================================================*/

	/**
	 * Relate node.
	 *
	 * This method can be used to relate the current node to anther node, it will return a
	 * Everyman\Neo4j\Relationship instance.
	 *
	 * The parameters to this method are:
	 *
	 * <ul>
	 *	<li><b>$theContainer</b>: The graph container.
	 *	<li><b>$thePredicate</b>: The predicate string or edge type.
	 *	<li><b>$theObject</b>: The destination node or relationship object node.
	 * </ul>
	 *
	 * The method will return a Everyman\Neo4j\Relationship object, or raise an exception if
	 * the operation was not successful.
	 *
	 * This method follows the logic of the relateTo() method of Everyman\Neo4j\Node.
	 *
	 * @param mixed					$theContainer		Graph container.
	 * @param string				$thePredicate		Predicate.
	 * @param mixed					$theObject			Object.
	 *
	 * @access public
	 * @return Everyman\Neo4j\Relationship
	 */
	public function RelateTo( $theContainer, $thePredicate, $theObject )
	{
		//
		// Check node.
		//
		if( ($node = $this->Node()) !== NULL )
		{
			//
			// Check object.
			//
			if( $theObject instanceof self )
				$theObject = $theObject->Node();
			
			//
			// Handle object.
			//
			if( $theObject instanceof Everyman\Neo4j\Node )
			{
				//
				// Handle container.
				//
				if( ! $theContainer instanceof Everyman\Neo4j\Client )
				{
					//
					// Provided as node.
					//
					if( $theContainer instanceof Everyman\Neo4j\PropertyContainer )
						$theContainer = $theContainer->getClient();
					
					//
					// Get current container.
					//
					else
						$theContainer = $node->getClient();
				
				} // Normalised container.
				
				//
				// Create edge.
				//
				$edge = $theContainer->makeRelationship();
				
				//
				// Set subject node.
				//
				$edge->setStartNode( $node );
				
				//
				// Set object node.
				//
				$edge->setEndNode( $theObject );
				
				//
				// Set predicate.
				//
				$edge->setType( (string) $thePredicate );
			
			} // Object is node.
			
			else
				throw new CException
						( "Unsupported object node type",
						  kERROR_UNSUPPORTED,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Object' => $theObject ) );				// !@! ==>
		
		} // Has node.
		
		else
			throw new CException
					( "Subject node is not set",
					  kERROR_OPTION_MISSING,
					  kMESSAGE_TYPE_ERROR );									// !@! ==>
		
		return $edge;																// ==>

	} // RelateTo.

		

/*=======================================================================================
 *																						*
 *								PUBLIC STATUS INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Persistent																		*
	 *==================================================================================*/

	/**
	 * Check whether object is persistent.
	 *
	 * This method will return <i>TRUE</i> if the node has an ID, which is assuming it was
	 * committed; or <i>FALSE</i> if not.
	 *
	 * @access public
	 * @return boolean
	 */
	public function Persistent()
	{
		$node = $this->Node();
		if( $node !== NULL )
			return $node->hasId();													// ==>
		
		return FALSE;																// ==>
	
	} // Persistent.

		

/*=======================================================================================
 *																						*
 *								PUBLIC ARRAY ACCESS INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	offsetExists																	*
	 *==================================================================================*/

	/**
	 * Check whether the provided offset exists.
	 *
	 * We overload this method to wrap the array access interface around the node
	 * properties array.
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
		if( $this->mNode !== NULL )
			return array_key_exists( $theOffset, $this->mNode->getProperties() );	// ==>
		
		return FALSE;																// ==>
	
	} // offsetExists.

	 
	/*===================================================================================
	 *	offsetGet																		*
	 *==================================================================================*/

	/**
	 * Return the value of the provided offset.
	 *
	 * We overload this method to wrap the array access interface around the node
	 * properties array.
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
		if( $this->mNode !== NULL )
			return $this->mNode->getProperty( $theOffset );							// ==>
		
		return NULL;																// ==>
	
	} // offsetGet.

	 
	/*===================================================================================
	 *	offsetSet																		*
	 *==================================================================================*/

	/**
	 * Set a value for a given offset.
	 *
	 * We overload this method to wrap the array access interface around the node
	 * properties array.
	 *
	 * In this class we delete the entry if the value is <i>NULL</i>.
	 *
	 * Note that we do not call array access methods: this is because this method will be
	 * overloaded by classes who will aggregate array elements.
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
		if( $this->mNode !== NULL )
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
				{
					//
					// Set dirty flag.
					//
					$this->_IsDirty
						( $this->mNode->getProperty( $theOffset ) !== $theValue );
					
					//
					// Set node property.
					//
					$this->mNode->setProperty( $theOffset, $theValue );
				}
				
				//
				// Omitted offset.
				//
				else
				{
					//
					// Set dirty flag.
					//
					$this->_IsDirty( TRUE );
					
					//
					// Add node property.
					//
					$props = $this->mNode->getProperties();
					$props[] = $theValue;
					$this->mNode->setProperties( $props );
				}
			
			} // Provided value.
			
			//
			// Delete offset.
			//
			else
			{
				//
				// Set dirty flag.
				//
				$this->_IsDirty( $this->offsetExists( $theOffset ) );
				
				//
				// Remove node property.
				//
				$this->mNode->removeProperty( $theOffset );
			}
		
		} // Has node.
	
	} // offsetSet.

	 
	/*===================================================================================
	 *	offsetUnset																		*
	 *==================================================================================*/

	/**
	 * Delete a given offset.
	 *
	 * We overload this method to wrap the array access interface around the node
	 * properties array.
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
		if( $this->mNode !== NULL )
		{
			//
			// Set dirty flag.
			//
			$this->_IsDirty( $this->offsetExists( $theOffset ) );
			
			//
			// Remove node property.
			//
			$this->mNode->removeProperty( $theOffset );
		}
	
	} // offsetUnset.

	 
	/*===================================================================================
	 *	append																			*
	 *==================================================================================*/

	/**
	 * Append a value.
	 *
	 * We overload this method to wrap the array access interface around the node
	 * properties array.
	 *
	 * @param mixed					$theValue			Value.
	 *
	 * @access public
	 */
	public function append( $theValue )
	{
		$this->offsetSet( NULL, $theValue );
	
	} // append.

	 
	/*===================================================================================
	 *	asort																			*
	 *==================================================================================*/

	/**
	 * Sort array by values.
	 *
	 * We overload this method to wrap the array access interface around the node
	 * properties array.
	 *
	 *
	 * @access public
	 */
	public function asort()
	{
		//
		// Require node.
		//
		if( $this->mNode !== NULL )
		{
			$props = $this->mNode->getProperties();
			asort( $props );
			$this->mNode->setProperties( $props );
		}
	
	} // asort.

	 
	/*===================================================================================
	 *	ksort																			*
	 *==================================================================================*/

	/**
	 * Sort array by keys.
	 *
	 * We overload this method to wrap the array access interface around the node
	 * properties array.
	 *
	 * @access public
	 */
	public function ksort()
	{
		//
		// Require node.
		//
		if( $this->mNode !== NULL )
		{
			$props = $this->mNode->getProperties();
			ksort( $props );
			$this->mNode->setProperties( $props );
		}
	
	} // ksort.

	 
	/*===================================================================================
	 *	natcasesort																		*
	 *==================================================================================*/

	/**
	 * Sort array by case insensitive natural order algorythm.
	 *
	 * We overload this method to wrap the array access interface around the node
	 * properties array.
	 *
	 * @access public
	 */
	public function natcasesort()
	{
		//
		// Require node.
		//
		if( $this->mNode !== NULL )
		{
			$props = $this->mNode->getProperties();
			natcasesort( $props );
			$this->mNode->setProperties( $props );
		}
	
	} // natcasesort.

	 
	/*===================================================================================
	 *	natsort																			*
	 *==================================================================================*/

	/**
	 * Sort array by natural order algorythm.
	 *
	 * We overload this method to wrap the array access interface around the node
	 * properties array.
	 *
	 * @access public
	 */
	public function natsort()
	{
		//
		// Require node.
		//
		if( $this->mNode !== NULL )
		{
			$props = $this->mNode->getProperties();
			natsort( $props );
			$this->mNode->setProperties( $props );
		}
	
	} // natsort.

	 
	/*===================================================================================
	 *	count																			*
	 *==================================================================================*/

	/**
	 * Count number of elements.
	 *
	 * We overload this method to wrap the array access interface around the node
	 * properties array.
	 *
	 * @access public
	 * @return mixed
	 */
	public function count()
	{
		//
		// Require node.
		//
		if( $this->mNode !== NULL )
			return count( $this->mNode->getProperties() );							// ==>
		
		return 0;																	// ==>
	
	} // count.

	 
	/*===================================================================================
	 *	exchangeArray																	*
	 *==================================================================================*/

	/**
	 * Exchange arrays.
	 *
	 * We overload this method to wrap the array access interface around the node
	 * properties array.
	 *
	 * Note that if the node exists the method will return an array, if not, it will
	 * return an empty array.
	 *
	 * This method will raise an exception if a value other than an array or ArrayObject is
	 * provided.
	 *
	 * @param mixed					$theValue			Value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @throws {@link CException CException}
	 */
	public function exchangeArray( $theValue )
	{
		//
		// Check value.
		//
		if( is_array( $theValue )
		 || ($theValue instanceof ArrayObject) )
		{
			//
			// Require node.
			//
			if( $this->mNode !== NULL )
			{
				$old = $this->mNode->getProperties();
				$this->mNode->setProperties( $theValue );
				
				return $old;														// ==>
			}
			
			return Array();															// ==>
		
		} // Provided an array.

		throw new CException
				( "Unsupported data type",
				  kERROR_UNSUPPORTED,
				  kMESSAGE_TYPE_ERROR,
				  array( 'Value' => $theValue ) );								// !@! ==>
	
	} // exchangeArray.

	 
	/*===================================================================================
	 *	getArrayCopy																	*
	 *==================================================================================*/

	/**
	 * Create a copy of the array.
	 *
	 * We overload this method to wrap the array access interface around the node
	 * properties array.
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
		if( $this->mNode !== NULL )
			return $this->mNode->getProperties();									// ==>
		
		return Array();																// ==>
	
	} // getArrayCopy.

	 
	/*===================================================================================
	 *	getIterator																		*
	 *==================================================================================*/

	/**
	 * Get array iterator.
	 *
	 * We overload this method to wrap the array access interface around the node
	 * properties array.
	 *
	 * Note that if the node exists the method will return an array, if not, it will
	 * return an empty array.
	 *
	 * @access public
	 * @return ArrayIterator
	 */
	public function getIterator()
	{
		return new ArrayIterator( $this->getArrayCopy() );							// ==>
	
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
	 * We overload this method to wrap the array access interface around the node
	 * properties array.
	 *
	 * @access public
	 * @return array
	 */
	public function keys()
	{
		return array_keys( $this->getArrayCopy() );									// ==>
	
	} // keys.

	 
	/*===================================================================================
	 *	values																			*
	 *==================================================================================*/

	/**
	 * Return object's values.
	 *
	 * We overload this method to wrap the array access interface around the node
	 * properties array.
	 *
	 * @access public
	 * @return array
	 */
	public function values()
	{
		return array_values( $this->getArrayCopy() );								// ==>
	
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
	 * We {@link CPersistentObject::_Create() overload} this method to set the
	 * {@link Node() node} member: if the provided content is a node, we set it, if it is
	 * the graph container, it means that we instantiated an empty object.
	 *
	 * @param reference			   &$theContent			Object data content.
	 *
	 * @access protected
	 * @return boolean
	 *
	 * @uses Node()
	 */
	protected function _Create( &$theContent )
	{
		//
		// Handle container.
		// This is the case when no identifier was provided:
		// in this case we pass the container in the parameter.
		//
		if( $theContent instanceof Everyman\Neo4j\Client )
			return FALSE;															// ==>
		
		//
		// Handle content.
		// This is the case when either the content was loaded,
		// or the content was provided without identifier.
		// in this case we pass the content in the parameter.
		//
		if( $theContent instanceof Everyman\Neo4j\PropertyContainer )
		{
			//
			// Save content in node.
			//
			$this->Node( $theContent );
			
			return TRUE;															// ==>
		
		} // Loading node.
		
		return parent::_Create( $theContent );										// ==>
	
	} // _Create.

	 
	/*===================================================================================
	 *	_Commit																			*
	 *==================================================================================*/

	/**
	 * Store object in container.
	 *
	 * In this class we save the node, or we delete it if the
	 * {@link kFLAG_PERSIST_DELETE kFLAG_PERSIST_DELETE} flag is provided in the modifiers.
	 *
	 * We ignore the identifier here.
	 *
	 * @param reference			   &$theContainer		Object container.
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Commit modifiers.
	 *
	 * @access protected
	 * @return mixed
	 *
	 * @uses Node()
	 */
	protected function _Commit( &$theContainer, &$theIdentifier, &$theModifiers )
	{
		//
		// Handle delete.
		//
		if( $theModifiers & kFLAG_PERSIST_DELETE )
		{
			//
			// Save node and id.
			//
			$id = $this->mNode->getId();
			if( $id !== NULL )
			{
				//
				// Delete relationship.
				//
				if( ! $theContainer->deleteNode( $this->mNode ) )
					throw new CException
							( "Unable to delete node",
							  kERROR_INVALID_STATE,
							  kMESSAGE_TYPE_ERROR,
							  array( 'Id' => $id ) );							// !@! ==>
				
				//
				// Reset relationship.
				//
				$this->Node( $theContainer->makeNode() );
		
			} // Has ID.
			
			return $id;																// ==>
		
		} // Delete.
		
		//
		// Save node.
		//
		$this->mNode->save();
		
		return $this->mNode->getID();												// ==>
	
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
	 * In this class we first enforce that the container was provided, then we check whether
	 * the container is an instance of Everyman\Neo4j\Client.
	 *
	 * @param reference			   &$theContainer		Object container.
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Create modifiers.
	 *
	 * @access protected
	 */
	protected function _PrepareCreate( &$theContainer, &$theIdentifier, &$theModifiers )
	{
		//
		// Check container.
		//
		if( $theContainer === NULL )
			throw new CException
					( "Missing node container",
					  kERROR_OPTION_MISSING,
					  kMESSAGE_TYPE_ERROR );									// !@! ==>
			
		//
		// Check container type.
		//
		if( ! $theContainer instanceof Everyman\Neo4j\Client )
			throw new CException
					( "Unsupported node container type",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Container' => $theContainer ) );					// !@! ==>
		
		//
		// Handle provided node.
		//
		if( $theIdentifier instanceof Everyman\Neo4j\PropertyContainer )
		{
			//
			// Set node as container.
			//
			$theContainer = $theIdentifier;
			
			//
			// Prevent loading from container.
			//
			$theIdentifier = NULL;
		}
			
		//
		// Call parent method.
		//
		parent::_PrepareCreate( $theContainer, $theIdentifier, $theModifiers );
	
	} // _PrepareCreate.

	 
	/*===================================================================================
	 *	_PrepareLoad																	*
	 *==================================================================================*/

	/**
	 * Normalise parameters of a find.
	 *
	 * In this class we check if the provided container is an Everyman\Neo4j\Client.
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
	 * In this class we {@link CPersistentObject::_PrepareCommit() overload} the method to
	 * set the identifier if {@link kFLAG_PERSIST_UPDATE updating} and we check whether the
	 * container is supported.
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
		// Set identifier.
		//
		if( ($theIdentifier === NULL)
		 && (($theModifiers & kFLAG_PERSIST_MASK) == kFLAG_PERSIST_UPDATE) )
			$theIdentifier = $this->mNode->getId();
		
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
	 * In this class we create an empty node if not yet set.
	 *
	 * @param reference			   &$theContainer		Object container.
	 *
	 * @access protected
	 */
	protected function _FinishCreate( &$theContainer )
	{
		//
		// Handle container.
		// This method is only called with an empty identifier.
		//
		if( ($theContainer instanceof Everyman\Neo4j\Client)
		 && ($this->Node() === NULL) )
		{
			//
			// Init node.
			//
			$this->Node( $theContainer->makeNode() );
			
			//
			// Set clean.
			// Because we don't want to commit an empty node.
			//
			$this->_IsDirty( FALSE );
		}
		
		//
		// Handle content.
		//
		else
		{
			//
			// Set committed status.
			//
			$this->_IsCommitted( $this->Node()->hasId() );
			
			//
			// Set clean if committed.
			//
			$this->_IsDirty( ! $this->Node()->hasId() );
		}
		
		//
		// Set inited flag.
		//
		$this->_IsInited( TRUE );
	
	} // _FinishCreate.

	 
	/*===================================================================================
	 *	_FinishLoad																		*
	 *==================================================================================*/

	/**
	 * Normalise after a {@link _Load() load}.
	 *
	 * In this class we create an empty node if the node was not found and we set the object
	 * as inited.
	 *
	 * @param reference			   &$theContainer		Object container.
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Create modifiers.
	 *
	 * @access protected
	 */
	protected function _FinishLoad( &$theContainer, &$theIdentifier, &$theModifiers )
	{
		//
		// Create empty node.
		//
		if( $this->Node() === NULL )
		{
			//
			// Init node.
			//
			$this->Node( $theContainer->makeNode() );
			
			//
			// Set clean.
			// Because we don't want to commit an empty node.
			//
			$this->_IsDirty( FALSE );
		}
		
		//
		// Handle loaded node.
		//
		else
		{
			//
			// Set committed status.
			//
			$this->_IsCommitted( $this->Node()->hasId() );
			
			//
			// Set clean if committed.
			//
			$this->_IsDirty( ! $this->Node()->hasId() );
		}
		
		//
		// Set inited flag.
		//
		$this->_IsInited( TRUE );
	
	} // _FinishLoad.

	 

} // class CGraphNode.


?>
