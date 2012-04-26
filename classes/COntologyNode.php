<?php

/**
 * <i>COntologyNode</i> class definition.
 *
 * This file contains the class definition of <b>COntologyNode</b> which wraps an ontology
 * around a {@link COntologyTerm term} and a graph {@link CGraphNode node}.
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
require_once( kPATH_LIBRARY_SOURCE."COntology.php" );

use Everyman\Neo4j\Transport,
	Everyman\Neo4j\Client,
	Everyman\Neo4j\Index\NodeIndex,
	Everyman\Neo4j\Index\RelationshipIndex,
	Everyman\Neo4j\Index\NodeFulltextIndex,
	Everyman\Neo4j\Node,
	Everyman\Neo4j\Batch;

/**
 * Ontology node.
 *
 * This class overloads its {@link COntology ancestor} to add a {@link RelateTo() method}
 * that can be used to relate the current node to another one using a predicate
 * {@link COntologyTerm term} and a graph {@link CGraphEdge edge}.
 *
 * The {@link COntology parent} class does not feature this method because nodes of that
 * class are supposed to be root nodes.
 *
 *	@package	MyWrapper
 *	@subpackage	Ontology
 */
class COntologyNode extends COntology
{
		

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

	 

} // class COntologyNode.


?>
