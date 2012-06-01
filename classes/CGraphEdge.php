<?php

/**
 * <i>CGraphEdge</i> class definition.
 *
 * This file contains the class definition of <b>CGraphEdge</b> which represents the
 * ancestor of all graph edges in this library.
 *
 *	@package	MyWrapper
 *	@subpackage	Persistence
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 23/04/2012
 */

/*=======================================================================================
 *																						*
 *									CGraphEdge.php										*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CGraphNode.php" );

/**
 * Graph edge.
 *
 * This class implements a graph edge.
 *
 * This class extends its {@link CGraphNode parent} class in that its {@link Node() node}
 * property is a relationship node rather than a plain node.
 *
 *	@package	MyWrapper
 *	@subpackage	Persistence
 */
class CGraphEdge extends CGraphNode
{
		

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
	 * In this class we return the graph {@link Type() type}.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses Type()
	 */
	public function __toString()
	{
		return ( ($type = $this->Type()) !== NULL )
			 ? $type																// ==>
			 : '';																	// ==>
	
	} // __toString.

		

/*=======================================================================================
 *																						*
 *								PUBLIC MEMBER INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Type																			*
	 *==================================================================================*/

	/**
	 * Manage node type.
	 *
	 * This property corresponds to the node type, or predicate reference, this property is
	 * a string, and it is managed directly by the relationship node: we simply wrap this
	 * method around the native management:
	 *
	 * <ul>
	 *	<li><b>$theValue</b>: The value or operation:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the current value.
	 *		<li><i>other</i>: Set the value converted to string.
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
	 * @return string
	 *
	 * @uses Node()
	 * @uses _IsDirty()
	 * @uses _IsInited()
	 */
	public function Type( $theValue = NULL, $getOld = FALSE )
	{
		//
		// Save node.
		//
		$node = $this->Node();
		
		//
		// Save value.
		//
		$save = ( $node !== NULL )
			  ? $node->getType()
			  : NULL;
		
		//
		// Retrieve value.
		//
		if( $theValue === NULL )
			return $save;															// ==>
		
		//
		// Set new value.
		//
		if( $node !== NULL )
		{
			//
			// Set type.
			//
			$node->setType( (string) $theValue );
			
			//
			// Set dirty flag.
			//
			$this->_IsDirty( TRUE );
			
			//
			// Set inited flag.
			//
			$this->_IsInited( ($this->Node() !== NULL) &&
							  ($this->Node()->getType() !== NULL) &&
							  ($this->Node()->getEndNode() !== NULL) &&
							  ($this->Node()->getStartNode() !== NULL) );
			
		} // Has node.
		
		if( $getOld )
			return $save;															// ==>
		
		return (string) $theValue;													// ==>

	} // Type.

	 
	/*===================================================================================
	 *	Node																			*
	 *==================================================================================*/

	/**
	 * Manage native node.
	 *
	 * We override the {@link CGraphNode parent} {@link CGRaphNode::Node() method} to
	 * enforce Everyman\Neo4j\Relationship objects rather than Everyman\Neo4j\Node objects.
	 *
	 * @param mixed					$theValue			Node or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return Everyman\Neo4j\Relationship
	 *
	 * @uses CObject::ManageMember()
	 * @uses _IsDirty()
	 * @uses _IsInited()
	 */
	public function Node( $theValue = NULL, $getOld = FALSE )
	{
		//
		// Call parent method.
		//
		$save = parent::Node( $theValue, $getOld );
		
		//
		// Set status.
		//
		if( $theValue !== NULL )
		{
			//
			// Set inited flag.
			//
			$this->_IsInited( (($tmp = $this->Node()) !== NULL) &&
							  ($tmp->getType() !== NULL) &&
							  ($tmp->getEndNode() !== NULL) &&
							  ($tmp->getStartNode() !== NULL) );
		}
		
		return $save;																// ==>

	} // Node.

	 
	/*===================================================================================
	 *	Subject																			*
	 *==================================================================================*/

	/**
	 * Manage subject node.
	 *
	 * This method will wrap the member accessor around the native relationship node, note
	 * that the method will not allow to delete a value, you must replace it.
	 *
	 * <ul>
	 *	<li><b>$theValue</b>: The value or operation:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the current value.
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
	 * @param mixed					$theValue			Subject node or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return Everyman\Neo4j\Node
	 *
	 * @uses Node()
	 * @uses _IsDirty()
	 * @uses _IsInited()
	 */
	public function Subject( $theValue = NULL, $getOld = FALSE )
	{
		//
		// Save node.
		//
		$node = $this->Node();
		
		//
		// Save value.
		//
		$save = ( $node !== NULL )
			  ? $node->getStartNode()
			  : NULL;
		
		//
		// Retrieve value.
		//
		if( $theValue === NULL )
			return $save;															// ==>
		
		//
		// Handle GraphNode.
		//
		if( $theValue instanceof CGraphNode )
			$theValue = $theValue->Node();
		
		//
		// Check node.
		//
		if( ! $theValue instanceof Everyman\Neo4j\Node )
			throw new CException
					( "Unsupported subject node type",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Node' => $theValue ) );							// !@! ==>
		
		//
		// Set new value.
		//
		if( $node !== NULL )
			$node->setStartNode( $theValue );
		else
			throw new CException
					( "Relationship is not initialised",
					  kERROR_NOT_INITED,
					  kMESSAGE_TYPE_ERROR );									// !@! ==>

		//
		// Set dirty flag.
		//
		$this->_IsDirty( TRUE );
		
		//
		// Set inited flag.
		//
		$this->_IsInited( ($this->Node() !== NULL) &&
						  ($this->Node()->getType() !== NULL) &&
						  ($this->Node()->getEndNode() !== NULL) &&
						  ($this->Node()->getStartNode() !== NULL) );
		
		if( $getOld )
			return $save;															// ==>
		
		return $theValue;															// ==>

	} // Subject.

	 
	/*===================================================================================
	 *	Object																			*
	 *==================================================================================*/

	/**
	 * Manage object node.
	 *
	 * This method will wrap the member accessor around the native relationship node, note
	 * that the method will not allow to delete a value, you must replace it.
	 *
	 * <ul>
	 *	<li><b>$theValue</b>: The value or operation:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the current value.
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
	 * @param mixed					$theValue			Object node or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return Everyman\Neo4j\Node
	 *
	 * @uses Node()
	 * @uses _IsDirty()
	 * @uses _IsInited()
	 */
	public function Object( $theValue = NULL, $getOld = FALSE )
	{
		//
		// Save node.
		//
		$node = $this->Node();
		
		//
		// Save value.
		//
		$save = ( $node !== NULL )
			  ? $node->getEndNode()
			  : NULL;
		
		//
		// Retrieve value.
		//
		if( $theValue === NULL )
			return $save;															// ==>
		
		//
		// Handle GraphNode.
		//
		if( $theValue instanceof CGraphNode )
			$theValue = $theValue->Node();
		
		//
		// Check Node.
		//
		if( ! $theValue instanceof Everyman\Neo4j\Node )
			throw new CException
					( "Unsupported object node type",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Node' => $theValue ) );							// !@! ==>
		
		//
		// Set new value.
		//
		if( $node !== NULL )
			$node->setEndNode( $theValue );
		else
			throw new CException
					( "Relationship is not initialised",
					  kERROR_NOT_INITED,
					  kMESSAGE_TYPE_ERROR );									// !@! ==>

		//
		// Set dirty flag.
		//
		$this->_IsDirty( TRUE );
		
		//
		// Set inited flag.
		//
		$this->_IsInited( ($this->Node() !== NULL) &&
						  ($this->Node()->getType() !== NULL) &&
						  ($this->Node()->getEndNode() !== NULL) &&
						  ($this->Node()->getStartNode() !== NULL) );
		
		if( $getOld )
			return $save;															// ==>
		
		return $theValue;															// ==>

	} // Object.

		

/*=======================================================================================
 *																						*
 *								PROTECTED PERSISTENCE INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_Commit																			*
	 *==================================================================================*/

	/**
	 * Store object in container.
	 *
	 * We {@link CGraphNode::_Commit() override} this method to initialise an empty
	 * relationship rather than a node when deleting.
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
			$save = $this->Node();
			$id = $save->getId();
			if( $id !== NULL )
			{
				//
				// Delete relationship.
				//
				if( ! $theContainer->deleteRelationship( $save ) )
					throw new CException
							( "Unable to delete relationship",
							  kERROR_INVALID_STATE,
							  kMESSAGE_TYPE_ERROR,
							  array( 'Id' => $id ) );							// !@! ==>
				
				//
				// Reset relationship.
				//
				$this->Node( $theContainer->makeRelationship() );
		
			} // Has ID.
			
			return $id;																// ==>
		
		} // Delete.
		
		//
		// Save subject node.
		//
		$this->Subject()->save();
		
		//
		// Save object node.
		//
		$this->Object()->save();
		
		return parent::_Commit( $theContainer, $theIdentifier, $theModifiers );		// ==>
	
	} // _Commit.

	 
	/*===================================================================================
	 *	_Load																			*
	 *==================================================================================*/

	/**
	 * Find object.
	 *
	 * We {@link CGraphNode::_Commit() override} this method to locate relationships rather
	 * than nodes.
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
		return $theContainer->getRelationship( $theIdentifier );					// ==>
	
	} // _Load.

		

/*=======================================================================================
 *																						*
 *								PROTECTED PERSISTENCE UTILITIES							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_FinishCreate																	*
	 *==================================================================================*/

	/**
	 * Normalise after a {@link _Create() create}.
	 *
	 * We {@link CGraphNode::_FinishCreate() override} this method to handle relationships
	 * rather than nodes, and to initialise related nodes.
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
		if( $theContainer instanceof Everyman\Neo4j\Client )
		{
			//
			// Create empty predicate node.
			//
			if( ! $this->Node() instanceof Everyman\Neo4j\Relationship )
				$this->Node( $theContainer->makeRelationship() );
			
			//
			// Create empty subject node.
			//
			if( ! $this->Subject() instanceof Everyman\Neo4j\Node )
				$this->Subject( $theContainer->makeNode() );
			
			//
			// Create empty object node.
			//
			if( ! $this->Object() instanceof Everyman\Neo4j\Node )
				$this->Object( $theContainer->makeNode() );
			
			//
			// Set clean.
			// Because we don't want to commit an empty node.
			//
			$this->_IsDirty( FALSE );
		
		} // Provided container.
		
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
	 * We {@link CGraphNode::_FinishCreate() override} this method to handle relationships
	 * rather than nodes.
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
			// Init predicate node.
			//
			$this->Node( $theContainer->makeRelationship() );
			
			//
			// Init subject node.
			//
			$this->Subject( $theContainer->makeNode() );
			
			//
			// Init object node.
			//
			$this->Object( $theContainer->makeNode() );
			
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
			$this->_IsCommitted( $ok = $this->Node()->hasId() );
			
			//
			// Set clean if committed.
			//
			$this->_IsDirty( ! $ok );
		}
		
		//
		// Set inited flag.
		//
		$this->_IsInited( TRUE );
	
	} // _FinishLoad.

	 

} // class CGraphEdge.


?>
