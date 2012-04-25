<?php

/**
 * <i>COntologyNode</i> class definition.
 *
 * This file contains the class definition of <b>COntologyNode</b> which wraps an ontology
 * node around a {@link COntologyTerm term} and a graph {@link CGraphNode node}.
 *
 *	@package	MyWrapper
 *	@subpackage	Ontology
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 18/04/2012
 */

/*=======================================================================================
 *																						*
 *									COntologyNode.php									*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CGraphNode.php" );

/**
 * Local defines.
 *
 * This include file contains the local class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."COntologyNode.inc.php" );

/**
 * Edges.
 *
 * This include file contains the ontology edge class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CGraphEdge.php" );

use Everyman\Neo4j\Transport,
	Everyman\Neo4j\Client,
	Everyman\Neo4j\Index\NodeIndex,
	Everyman\Neo4j\Index\RelationshipIndex,
	Everyman\Neo4j\Index\NodeFulltextIndex,
	Everyman\Neo4j\Node,
	Everyman\Neo4j\Batch;

/**
 * Ontology graph node.
 *
 * This class implements an ontology graph node.
 *
 * The class is derived from {@link CGraphNode CGraphNode}, it adds another required
 * property, the {@link Term() term}. This class will wrap the array access framework to
 * the combination of the {@link Node() node} properties and the {@link Term() term}
 * elements, except that the {@link Term() term} elements will be read-only.
 *
 * This class introduces a new kind of container: it must be an array of two elements
 * structured as follows:
 *
 * <ul>
 *	<li><i>{@link kTAG_NODE kTAG_NODE}</i>: This element should hold the nodes container,
 *		it must be a Everyman\Neo4j\Client instance.
 *	<li><i>{@link kTAG_TERM kTAG_TERM}</i>: This element should hold the terms container,
 *		it must be a {@link CContainer CContainer} instance.
 * </ul>
 *
 * <i>Note that the class will not cast to an array correctly, you must use the
 * {@link getArrayCopy() getArrayCopy} method to get an array, if you know how to solve
 * this, please do it!</i>
 *
 *	@package	MyWrapper
 *	@subpackage	Ontology
 */
class COntologyNode extends CGraphNode
{
	/**
	 * Term.
	 *
	 * This data member holds the node {@link COntologyTerm term}.
	 *
	 * @var COntologyTerm
	 */
	 protected $mTerm = NULL;

		

/*=======================================================================================
 *																						*
 *								PUBLIC MEMBER INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Term																			*
	 *==================================================================================*/

	/**
	 * Manage node term.
	 *
	 * This method can be used to manage the node term reference, it uses the standard
	 * accessor {@link CObject::ManageMember() method} to manage the property:
	 *
	 * <ul>
	 *	<li><b>$theValue</b>: The value or operation:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the current value.
	 *		<li><i>FALSE</i>: Delete the value.
	 *		<li><i>{@link COntologyTermObject COntologyTermObject}</i>: Set value.
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
	 * @param mixed					$theValue			Term or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return COntologyTermObject
	 *
	 * @uses CObject::ManageMember()
	 * @uses _IsDirty()
	 * @uses _IsInited()
	 */
	public function Term( $theValue = NULL, $getOld = FALSE )
	{
		//
		// Check provided value.
		//
		if( ($theValue !== NULL)
		 && ($theValue !== FALSE)
		 && (! $theValue instanceof COntologyTermObject) )
			throw new CException
					( "Unsupported term type",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Term' => $theValue ) );							// !@! ==>
		
		//
		// Handle data.
		//
		$save = CObject::ManageMember( $this->mTerm, $theValue, $getOld );
				
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
							  ($this->mTerm !== NULL) );
		}
		
		return $save;																// ==>

	} // Term.

		

/*=======================================================================================
 *																						*
 *								PUBLIC RELATION INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	RelateTo																		*
	 *==================================================================================*/

	/**
	 * Create a graph edge.
	 *
	 * This method can be used to create a graph edge or relation between the current node
	 * and an object node using a predicate node. The method accepts the following
	 * parameters:
	 *
	 * <ul>
	 *	<li><b>$theContainer</b>: The graph and term containers as an array:
	 *	 <ul>
	 *		<li><i>{@link kTAG_NODE kTAG_NODE}</i>: This element should hold the nodes
	 *			container, it must be a Everyman\Neo4j\Client instance.
	 *		<li><i>{@link kTAG_TERM kTAG_TERM}</i>: This element should hold the terms
	 *			container, it must be a {@link CContainer CContainer} instance.
	 *	 </ul>
	 *	<li><b>$thePredicate</b>: The predicate term:
	 *	 <ul>
	 *		<li><i>{@link COntologyTerm COntologyTerm}</i>: The term {@link kTAG_GID global}
	 *			identifier will be used as the {@link COntologyEdge node}
	 *			{@link COntologyEdge::Type() type}.
	 *		<li><i>string</i>: Any other type will be converted to string and will be used
	 *			as the {@link COntologyEdge node} {@link COntologyEdge::Type() type}.
	 *	 </ul>
	 *	<li><b>$theObject</b>: The destination node or relationship object node:
	 *	 <ul>
	 *		<li><i>Everyman\Neo4j\Node</i>: The method will use it to determine the object
	 *			node identifier.
	 *		<li><i>integer</i>: The method will search for the node corresponding to
	 *			the provided number, if the node was not found, the method will raise an
	 *			exception.
	 *	 </ul>
	 * </ul>
	 *
	 * The method will return a {@link COntologyEdge COntologyEdge} object, or raise an
	 * exception if the opreation was not successful.
	 *
	 * Note that this method will not duplicate relationships between the same
	 * nodes and predicate term: it uses the {@link kINDEX_NODE_TERM kINDEX_NODE_TERM}
	 * relationship index and its {@link kTAG_EDGE_NODE kTAG_EDGE_NODE} key to locate
	 * existing relationships.
	 *
	 * @param reference				$theContainer		Object container.
	 * @param mixed					$thePredicate		Predicate.
	 * @param mixed					$theObject			Object.
	 *
	 * @access public
	 * @return COntologyEdge
	 */
	public function RelateTo( $theContainer, $thePredicate, $theObject = NULL )
	{
		//
		// Handle edge node.
		//
		if( $thePredicate instanceof Everyman\Neo4j\Relationship )
		{
			if( ($id = $thePredicate->getId()) !== NULL )
				return new COntologyEdge( $theContainer, $id );						// ==>
		}
		
		//
		// Handle edge identifier.
		//
		if( is_integer( $thePredicate ) )
			return new COntologyEdge( $theContainer, $thePredicate );				// ==>
		
		//
		// Verify container.
		//
		if( is_array( $theContainer )
		 || ($theContainer instanceof ArrayObject) )
		{
			//
			// Get node container.
			//
			if( array_key_exists( kTAG_NODE, (array) $theContainer ) )
			{
				if( ! $theContainer[ kTAG_NODE ] instanceof Everyman\Neo4j\Client )
					throw new CException
							( "Unsupported node container type",
							  kERROR_UNSUPPORTED,
							  kMESSAGE_TYPE_ERROR,
							  array( 'Container'
							  	=> $theContainer[ kTAG_NODE ] ) );				// !@! ==>
			}
		
			//
			// Get term container.
			//
			if( array_key_exists( kTAG_TERM, (array) $theContainer ) )
			{
				if( ! $theContainer[ kTAG_TERM ] instanceof CContainer )
					throw new CException
							( "Unsupported term container type",
							  kERROR_UNSUPPORTED,
							  kMESSAGE_TYPE_ERROR,
							  array( 'Container'
							  	=> $theContainer[ kTAG_TERM ] ) );				// !@! ==>
			}
		
		} // Structured container.
		
		else
			throw new CException
					( "Invalid container type",
					  kERROR_INVALID_PARAMETER,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Container' => $theContainer ) );					// !@! ==>
		
		//
		// Resolve predicate term.
		//
		if( ! $thePredicate instanceof COntologyTerm )
		{
			//
			// Load predicate.
			//
			$id = COntologyTerm::HashIndex( (string) $thePredicate );
			$tmp = new COntologyTerm( $theContainer[ kTAG_TERM ], $id );
			if( ! $tmp->Persistent() )
				throw new CException
						( "Predicate term not found",
						  kERROR_NOT_FOUND,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Predicate' => (string) $thePredicate ) );		// !@! ==>
			$thePredicate = $tmp;
		
		} // Provided predicate GID.
		
		//
		// Resolve object node.
		//
		if( ! $theObject instanceof self )
		{
			//
			// Check if graph node.
			//
			if( $theObject instanceof Everyman\Neo4j\Node )
			{
				//
				// Init object ontology node.
				//
				$node = new self( $theContainer );
				
				//
				// Set graph node.
				//
				$node->Node( $theObject );
				
				//
				// Handle term.
				//
				$term = $theObject->getProperty( kTAG_TERM );
				if( $term !== NULL )
				{
					$id = COntologyTerm::HashIndex( $term );
					$tmp = new COntologyTerm( $theContainer[ kTAG_TERM ], $id );
					if( ! $tmp->Persistent() )
						throw new CException
								( "Object term not found",
								  kERROR_NOT_FOUND,
								  kMESSAGE_TYPE_ERROR,
								  array( 'Term' => $id ) );						// !@! ==>
					$node->Term( $tmp );
					$theObject = $node;
				}
				
				else
					throw new CException
							( "Missing object term",
							  kERROR_OPTION_MISSING,
							  kMESSAGE_TYPE_ERROR,
							  array( 'Object' => (string) $theObject ) );		// !@! ==>
			}
			
			//
			// Check if graph node ID.
			//
			elseif( is_integer( $theObject ) )
			{
				//
				// Load node.
				//
				$tmp = new self( $theContainer, $theObject );
				if( $tmp->Node()->getId() === NULL )
					throw new CException
							( "Object node not found",
							  kERROR_NOT_FOUND,
							  kMESSAGE_TYPE_ERROR,
							  array( 'Object' => (string) $theObject ) );		// !@! ==>
				$theObject = $tmp;
			}
			
			else
				throw new CException
						( "Invalid object type",
						  kERROR_INVALID_PARAMETER,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Object' => $theObject ) );					// !@! ==>
		}
		
		//
		// Check relation.
		//
		$index = new RelationshipIndex( $theContainer[ kTAG_NODE ], kINDEX_NODE_TERM );
		$index->save();
		$found = $index->findOne( kTAG_EDGE_NODE,
								  implode( kTOKEN_INDEX_SEPARATOR,
								  		   array( $this->Node()->getId(),
												  $thePredicate[ kTAG_GID ],
												  $theObject->Node()->getId() ) ) );
		if( $found )
			return new COntologyEdge( $theContainer, $found->getId() );				// ==>
		
		//
		// Create relation.
		//
		$edge = new COntologyEdge( $theContainer );
		
		//
		// Set predicate.
		//
		$edge->Term( $thePredicate );
		
		//
		// Set subject.
		//
		$edge->Subject( $this->Node() );
		$edge->SubjectTerm( $this->Term() );
		
		//
		// Set object.
		//
		$edge->Object( $theObject->Node() );
		$edge->ObjectTerm( $theObject->Term() );
		
		return $edge;																// ==>

	} // RelateTo.

		

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
		// Check node.
		//
		if( parent::offsetExists( $theOffset ) )
			return TRUE;															// ==>
		
		//
		// Require term.
		//
		if( $this->mTerm !== NULL )
			return $this->mTerm->offsetExists( $theOffset );						// ==>
		
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
		// Check node.
		//
		if( ($found = parent::offsetGet( $theOffset )) !== NULL )
			return $found;															// ==>
		
		//
		// Require term.
		//
		if( $this->mTerm !== NULL )
			return $this->mTerm->offsetGet( $theOffset );							// ==>
		
		return NULL;																// ==>
	
	} // offsetGet.


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
		return count( $this->getArrayCopy() );										// ==>
	
	} // count.

	 
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
		// Require term.
		//
		if( $this->mTerm !== NULL )
			return array_merge( $this->mTerm->getArrayCopy(),
								parent::getArrayCopy() );							// ==>
		
		return parent::getArrayCopy();												// ==>
	
	} // getArrayCopy.

		

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
	 * We {@link CGraphNode::_Commit() overload} this method to provide the correct
	 * container to the {@link CGraphNode parent} {@link CGraphNode::_Commit() method}.
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
		// Call parent method.
		//
		$id = parent::_Commit( $theContainer[ kTAG_NODE ], $theIdentifier, $theModifiers );
		
		//
		// Handle save.
		//
		if( ! ($theModifiers & kFLAG_PERSIST_DELETE) )
		{
			//
			// Set node in term.
			//
/*
			$this->mTerm->Node( $id, TRUE );
			$this->mTerm->Commit( $theContainer[ kTAG_TERM ] );
*/
			$id = $this->mNode->getId();
			$this->mTerm->Node( $id, TRUE );
			$mod = array( kTAG_NODE => $id );
			$theContainer[ kTAG_TERM ]->Commit( $mod,
												$this->mTerm[ kTAG_LID ],
												kFLAG_PERSIST_MODIFY +
												kFLAG_MODIFY_ADDSET +
												kFLAG_STATE_ENCODED );
			
			//
			// Add indexes.
			//
			$this->_IndexTerms( $theContainer[ kTAG_NODE ] );
		
		} // Saving.
		
		//
		// Handle delete.
		//
		else
		{
			//
			// Remove node from term.
			//
/*
			$this->mTerm->Node( $id, FALSE );
			$this->mTerm->Commit( $theContainer[ kTAG_TERM ] );
*/
			$this->mTerm->Node( $id, FALSE );
			$mod = array( kTAG_NODE => $id );
			$theContainer[ kTAG_TERM ]->Commit( $mod,
												$this->mTerm[ kTAG_LID ],
												kFLAG_PERSIST_MODIFY +
												kFLAG_MODIFY_PULL +
												kFLAG_STATE_ENCODED );
			
			//
			// Reset term
			//
			$this->Term( new COntologyTerm() );
			
			return $id;																// ==>
		
		} // Deleting.
		
		return $this->mNode->getId();												// ==>
	
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
		return parent::_Load( $theContainer[ kTAG_NODE ],
							  $theIdentifier,
							  $theModifiers );										// ==>
	
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
	 * In this class we first check whether the container has the following structure:
	 *
	 * <ul>
	 *	<li><i>{@link kTAG_NODE kTAG_NODE}</i>: This element should hold the nodes
	 *		container, it must be a Everyman\Neo4j\Client instance.
	 *	<li><i>{@link kTAG_TERM kTAG_TERM}</i>: This element should hold the terms
	 *		container, it must be a {@link COntologyTermObject COntologyTermObject} instance.
	 * </ul>
	 *
	 * If the container has the correct structure the {@link kTAG_NODE node} container will
	 * be passed to the parent method and the method will check if the
	 * {@link kTAG_TERM term} container is a
	 * {@link COntologyTermObject COntologyTermObject}.
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
		// Init local storage.
		//
		$node_cont = $term_cont = NULL;
		
		//
		// Verify container.
		//
		if( is_array( $theContainer )
		 || ($theContainer instanceof ArrayObject) )
		{
			//
			// Get node container.
			//
			if( array_key_exists( kTAG_NODE, (array) $theContainer ) )
				$node_cont = $theContainer[ kTAG_NODE ];
		
			//
			// Get term container.
			//
			if( array_key_exists( kTAG_TERM, (array) $theContainer ) )
				$term_cont = $theContainer[ kTAG_TERM ];
		
		} // Structured container.
		
		//
		// Check container.
		//
		if( $term_cont === NULL )
			throw new CException
					( "Missing term container",
					  kERROR_OPTION_MISSING,
					  kMESSAGE_TYPE_ERROR );									// !@! ==>
			
		//
		// Check container type.
		//
		if( ! $term_cont instanceof CContainer )
			throw new CException
					( "Unsupported term container type",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Container' => $term_cont ) );						// !@! ==>
		
		//
		// Call node method.
		//
		parent::_PrepareCreate( $node_cont, $theIdentifier, $theModifiers );
	
	} // _PrepareCreate.

	 
	/*===================================================================================
	 *	_PrepareLoad																	*
	 *==================================================================================*/

	/**
	 * Normalise parameters of a find.
	 *
	 * In this class we check if the provided container is supported., terms require a
	 * container instance derived from {@link CContainer CContainer}.
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
		// Init local storage.
		//
		$node_cont = $term_cont = NULL;
		
		//
		// Verify container.
		//
		if( is_array( $theContainer )
		 || ($theContainer instanceof ArrayObject) )
		{
			//
			// Get node container.
			//
			if( array_key_exists( kTAG_NODE, (array) $theContainer ) )
				$node_cont = $theContainer[ kTAG_NODE ];
		
			//
			// Get term container.
			//
			if( array_key_exists( kTAG_TERM, (array) $theContainer ) )
				$term_cont = $theContainer[ kTAG_TERM ];
		
		} // Structured container.
		
		//
		// Call node method.
		//
		parent::_PrepareLoad( $node_cont, $theIdentifier, $theModifiers );
		
		//
		// Check container.
		//
		if( $term_cont === NULL )
			throw new CException
					( "Missing term container",
					  kERROR_OPTION_MISSING,
					  kMESSAGE_TYPE_ERROR );									// !@! ==>
			
		//
		// Check container type.
		//
		if( ! $term_cont instanceof CContainer )
			throw new CException
					( "Unsupported term container type",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Container' => $term_cont ) );						// !@! ==>

	} // _PrepareLoad.

	 
	/*===================================================================================
	 *	_PrepareCommit																	*
	 *==================================================================================*/

	/**
	 * Normalise before a store.
	 *
	 * In this class we check if the provided container is supported and we set the
	 * {@link kTAG_TERM term} property in the node and {@link Commit() commit} the
	 * {@link Term() term}.
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
		// Init local storage.
		//
		$node_cont = $term_cont = NULL;
		
		//
		// Verify container.
		//
		if( is_array( $theContainer )
		 || ($theContainer instanceof ArrayObject) )
		{
			//
			// Get node container.
			//
			if( array_key_exists( kTAG_NODE, (array) $theContainer ) )
				$node_cont = $theContainer[ kTAG_NODE ];
		
			//
			// Get term container.
			//
			if( array_key_exists( kTAG_TERM, (array) $theContainer ) )
				$term_cont = $theContainer[ kTAG_TERM ];
		
		} // Structured container.
		
		//
		// Call parent method.
		//
		parent::_PrepareCommit( $node_cont, $theIdentifier, $theModifiers );
	
		//
		// Check if container is supported.
		//
		if( ! $term_cont instanceof CContainer )
			throw new CException
					( "Unsupported container type",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Container' => $theContainer ) );					// !@! ==>
		
		//
		// Commit term.
		//
		$id = $this->mTerm->Commit( $term_cont );
	
		//
		// Set term reference.
		//
		$this->offsetSet( kTAG_TERM, $this->mTerm[ kTAG_GID ] );
		
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
		// Create empty term.
		//
		$this->Term( new COntologyTerm() );
	
		//
		// Create empty node.
		//
		parent::_FinishCreate( $theContainer[ kTAG_NODE ], $theIdentifier, $theModifiers );
		
	} // _FinishCreate.

	 
	/*===================================================================================
	 *	_FinishLoad																		*
	 *==================================================================================*/

	/**
	 * Normalise after a {@link _Load() load}.
	 *
	 * In this class we get the {@link kTAG_TERM term} reference from the node properties
	 * and load it.
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
		// Get term reference.
		//
		$term = $this->offsetGet( kTAG_TERM );
		
		//
		// Load term.
		//
		if( $term !== NULL )
			$this->Term(
				CPersistentUnitObject::NewObject(
					$theContainer[ kTAG_TERM ],
					COntologyTermObject::HashIndex( $term ),
					kFLAG_STATE_ENCODED ) );
		
		//
		// Initialise term.
		//
		else
			$this->Term( new COntologyTerm() );
	
		//
		// Initialise empty nodes.
		//
		parent::_FinishLoad( $theContainer[ kTAG_NODE ], $theIdentifier, $theModifiers );
		
	} // _FinishLoad.

		

/*=======================================================================================
 *																						*
 *								PROTECTED INDEXING UTILITIES							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_IndexTerms																		*
	 *==================================================================================*/

	/**
	 * Create node/term indexes.
	 *
	 * This method will save node indexes after the node was {@link _Commit() committed},
	 * there are two main indexes for nodes:
	 *
	 * <ul>
	 *	<li><i>{@link kINDEX_NODE_TERM kINDEX_NODE_TERM}</i>: This index (NodeIndex) links
	 *		the node to its {@link Term() term} through the following keys:
	 *	 <ul>
	 *		<li><i>{@link kTAG_TERM kTAG_TERM}</i>: This key represents the
	 *			{@link Term() term} {@link kTAG_GID identifier}.
	 *		<li><i>{@link kTAG_NAME kTAG_NAME}</i>: This key represents the
	 *			{@link Term() term} {@link kTAG_NAME names} in all languages.
	 *	 </ul>
	 *	<li><i>{@link kINDEX_NODE_TERM_GEN kINDEX_NODE_TERM_GEN}</i>: This index
	 *		(NodeFulltextIndex) links the node to its {@link Term() term} through the
	 *		following keys:
	 *	 <ul>
	 *		<li><i>{@link kPROP_REF_TERM_WORD kPROP_REF_TERM_WORD}</i>: This key represents
	 *			the {@link Term() term} words found in the following properties:
	 *		 <ul>
	 *			<li><i>{@link kTAG_DEFINITION kTAG_DEFINITION}</i>: Term definitions in all
	 *				languages.
	 *		 </ul>
	 *	 </ul>
	 * </ul>
	 *
	 * @param Everyman\Neo4j\Client	$theContainer		Node container.
	 *
	 * @access protected
	 */
	protected function _IndexTerms( Everyman\Neo4j\Client $theContainer )
	{
		//
		// Load term and node.
		//
		$node = $this->Node();
		$term = $this->Term();
		
		//
		// Instantiate node index.
		//
		$idx = $this->_GetNodeIndex( $theContainer, kINDEX_NODE_TERM, TRUE );
	
		//
		// Add term global identifier key.
		//
		$idx->add( $node, kTAG_TERM, $term[ kTAG_GID ] );
	
		//
		// Add term names key.
		//
		foreach( $term[ kTAG_NAME ] as $element )
			$idx->add( $node, kTAG_NAME, $element[ kTAG_DATA ] );
	
	} // _IndexTerms.

	 
	/*===================================================================================
	 *	_GetNodeIndex																	*
	 *==================================================================================*/

	/**
	 * Retrieve the node index.
	 *
	 * This method can be used to return a node index identified by the provided index tag.
	 *
	 * @param Everyman\Neo4j\Client	$theContainer		Node container.
	 * @param string				$theIndex			Index tag.
	 * @param boolean				$doClear			TRUE means clear index.
	 *
	 * @access protected
	 * @return Everyman\Neo4j\NodeIndex 
	 */
	protected function _GetNodeIndex( Everyman\Neo4j\Client $theContainer,
															$theIndex,
															$doClear = FALSE )
	{
		//
		// Instantiate node index.
		//
		$idx = new NodeIndex( $theContainer, $theIndex );
		$idx->save();
		
		//
		// Clear node index.
		//
		if( $doClear )
			$idx->remove( $this->Node(), $theIndex );
		
		return $idx;																// ==>
	
	} // _GetNodeIndex.

	 

} // class COntologyNode.


?>
