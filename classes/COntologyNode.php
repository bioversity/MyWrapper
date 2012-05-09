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
	 * and an object node using a predicate node. This method will not duplicate
	 * relationships between the same nodes and the predicate term: it uses the
	 * {@link kINDEX_NODE_TERM kINDEX_NODE_TERM} relationship index and its
	 * {@link kTAG_EDGE_NODE kTAG_EDGE_NODE} key to locate existing relationships. For that
	 * reason the subject and object terms of the relationship must relate to
	 * {@link _IsCommitted() committed} nodes.
	 *
	 * The method accepts the following parameters:
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
	 *		<li><i>{@link COntology COntology}</i>: The node {@link Term() term} {@link kTAG_GID global}
	 *			{@link COntologyTerm::GID() identifier}.
	 *		<li><i>{@link COntologyTerm COntologyTerm}</i>: The term {@link kTAG_GID global}
	 *			identifier will be used as the {@link COntologyEdge node}
	 *			{@link COntologyEdge::Type() type}.
	 *		<li><i>Everyman\Neo4j\Relationship</i>: The relationship's type will be used as
	 *			the predicate, all other elements of the provided edge node will be ignored.
	 *		<li><i>string</i>: Any other type will be converted to string and will be used
	 *			as the {@link COntologyEdge node} {@link COntologyEdge::Type() type}.
	 *	 </ul>
	 *	<li><b>$theObject</b>: The destination node or relationship object node:
	 *	 <ul>
	 *		<li><i>COntologyNode</i>: The method will use it's {@link Node() node}.
	 *		<li><i>Everyman\Neo4j\Node</i>: The method will use it as the relationship
	 *			object.
	 *		<li><i>integer</i>: The method will search for the node corresponding to
	 *			the provided number, if the node was not found, the method will raise an
	 *			exception.
	 *		<li><i>other</i>: Any other type will raise an exception.
	 *	 </ul>
	 * </ul>
	 *
	 * The method will return a {@link COntologyEdge COntologyEdge} object, or raise an
	 * exception if the operation was not successful.
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
		// Handle subject.
		//
		$subject = $this->Node();
		if( ! $subject instanceof Everyman\Neo4j\Node )
			throw new CException
					( "Missing subject node reference",
					  kERROR_OPTION_MISSING,
					  kMESSAGE_TYPE_ERROR );									// !@! ==>
		elseif( ! $subject->hasId() )
			throw new CException
					( "Subject node has no identifier",
					  kERROR_OPTION_MISSING,
					  kMESSAGE_TYPE_ERROR );									// !@! ==>
		
		//
		// Handle predicate.
		//
		if( $thePredicate instanceof COntology )
		{
			if( ($tmp = $thePredicate->Term()) !== NULL )
			{
				$predicate = $tmp->GID();
				if( ! strlen( $predicate ) )
					throw new CException
							( "Empty term global identifier",
							  kERROR_INVALID_PARAMETER,
							  kMESSAGE_TYPE_ERROR,
							  array( 'Predicate' => $thePredicate ) );			// !@! ==>
			}
			else
				throw new CException
						( "Predicate is missing term reference",
						  kERROR_OPTION_MISSING,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Predicate' => $thePredicate ) );				// !@! ==>
		}
		elseif( $thePredicate instanceof COntologyTerm )
		{
			$predicate = $thePredicate->GID();
			if( ! strlen( $predicate ) )
				throw new CException
						( "Empty term global identifier",
						  kERROR_INVALID_PARAMETER,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Predicate' => $thePredicate ) );				// !@! ==>
		}
		elseif( $thePredicate instanceof Everyman\Neo4j\Relationship )
		{
			$predicate = $thePredicate->getType();
			if( ! strlen( $predicate ) )
				throw new CException
						( "Empty edge type",
						  kERROR_INVALID_PARAMETER,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Predicate' => $thePredicate ) );				// !@! ==>
			if( $theObject === NULL )
				$theObject = $thePredicate->getEndNode();
		}
		else
		{
			$predicate = (string) $thePredicate;
			if( ! strlen( $predicate ) )
				throw new CException
						( "Predicate is empty",
						  kERROR_INVALID_PARAMETER,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Predicate' => $thePredicate ) );				// !@! ==>
		}
		
		//
		// Handle object.
		//
		if( $theObject instanceof Everyman\Neo4j\Node )
		{
			$object = $theObject;
			if( ! $object->hasId() )
				throw new CException
						( "Object node has no identifier",
						  kERROR_OPTION_MISSING,
						  kMESSAGE_TYPE_ERROR );								// !@! ==>
		}
		elseif( $theObject instanceof COntology )
		{
			$object = $theObject->Node();
			if( ! $object instanceof Everyman\Neo4j\Node )
				throw new CException
						( "Object has no node",
						  kERROR_INVALID_PARAMETER,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Object' => $theObject ) );					// !@! ==>
		}
		elseif( is_integer( $theObject ) )
		{
			$object = $theContainer[ kTAG_NODE ]->getNode( $theObject );
			if( $object === NULL )
				throw new CException
						( "Object node not found",
						  kERROR_NOT_FOUND,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Object' => $theObject ) );					// !@! ==>
		}
		else
			throw new CException
					( "Invalid object type",
					  kERROR_INVALID_PARAMETER,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Object' => $theObject ) );						// !@! ==>
		
		//
		// Check relation.
		//
		$index = new RelationshipIndex( $theContainer[ kTAG_NODE ], kINDEX_NODE_TERM );
		$index->save();
		$found = $index->findOne( kTAG_EDGE_NODE,
								  implode( kTOKEN_INDEX_SEPARATOR,
								  		   array( $subject->getId(),
												  $predicate,
												  $object->getId() ) ) );
		if( $found )
			return new COntologyEdge( $theContainer, $found->getId() );				// ==>
		
		//
		// Create edge node.
		//
		$edge = parent::RelateTo( $theContainer[ kTAG_NODE ], $predicate, $object );
		
		return new COntologyEdge( $theContainer, $edge );							// ==>

	} // RelateTo.

	 
	/*===================================================================================
	 *	RelatedTo																		*
	 *==================================================================================*/

	/**
	 * Return all {@link COntologyEdge edges} pointing to this node.
	 *
	 * This method can be used to retrieve the list of nodes that point to the current one.
	 * The method will return an array structured as follows:
	 *
	 * <ul>
	 *	<li><i>Key</i>: The {@link kTAG_GID identifier} of the predicate term.
	 *	<li><i>Value</i>: An array of object nodes structured as follows:
	 *	 <ul>
	 *		<li><i>Index</i>: The node ID.
	 *		<li><i>Value</i>: The {@link Term() term} attributes merged with the
	 *			{@link Node() node} properties.
	 *	 </ul>
	 * </ul>
	 *
	 * The method accepts the following parameters:
	 *
	 * <ul>
	 *	<li><b>$theContainer</b>: The graph and term containers as an array:
	 *	 <ul>
	 *		<li><i>{@link kTAG_NODE kTAG_NODE}</i>: This element should hold the nodes
	 *			container, it must be a Everyman\Neo4j\Client instance.
	 *		<li><i>{@link kTAG_TERM kTAG_TERM}</i>: This element should hold the terms
	 *			container, it must be a {@link CContainer CContainer} instance.
	 *	 </ul>
	 *	<li><b>$thePredicate</b>: The predicate terms as an array of the following types:
	 *	 <ul>
	 *		<li><i>{@link COntology COntology}</i>: The node {@link Term() term} {@link kTAG_GID global}
	 *			{@link COntologyTerm::GID() identifier}.
	 *		<li><i>{@link COntologyTerm COntologyTerm}</i>: The term {@link kTAG_GID global}
	 *			identifier will be used as the {@link COntologyEdge node}
	 *			{@link COntologyEdge::Type() type}.
	 *		<li><i>Everyman\Neo4j\Relationship</i>: The relationship's type will be used as
	 *			the predicate, all other elements of the provided edge node will be ignored.
	 *		<li><i>string</i>: Any other type will be converted to string and will be used
	 *			as the {@link COntologyEdge node} {@link COntologyEdge::Type() type}.
	 *	 </ul>
	 * </ul>
	 *
	 * @param reference				$theContainer		Object container.
	 * @param mixed					$thePredicate		Predicate.
	 *
	 * @access public
	 * @return array
	 */
	public function RelatedTo( $theContainer, $thePredicate = NULL )
	{
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
		// Handle subject.
		//
		$subject = $this->Node();
		if( ! $subject instanceof Everyman\Neo4j\Node )
			throw new CException
					( "Missing subject node reference",
					  kERROR_OPTION_MISSING,
					  kMESSAGE_TYPE_ERROR );									// !@! ==>
		elseif( ! $subject->hasId() )
			throw new CException
					( "Subject node has no identifier",
					  kERROR_OPTION_MISSING,
					  kMESSAGE_TYPE_ERROR );									// !@! ==>
		
		//
		// Handle predicates.
		//
		$predicates = Array();
		if( $thePredicate !== NULL )
		{
			//
			// Normalise predicates.
			//
			if( ! is_array( $thePredicate ) )
				$thePredicate = array( $thePredicate );
			
			//
			// Handle predicates.
			//
			foreach( $thePredicate as $predicate )
			{
				if( $predicate instanceof COntology )
				{
					if( ($tmp = $predicate->Term()) !== NULL )
					{
						$pred = $tmp->GID();
						if( ! strlen( $pred ) )
							throw new CException
									( "Empty term global identifier",
									  kERROR_INVALID_PARAMETER,
									  kMESSAGE_TYPE_ERROR,
									  array( 'Predicate' => $predicate ) );		// !@! ==>
					}
					else
						throw new CException
								( "Predicate is missing term reference",
								  kERROR_OPTION_MISSING,
								  kMESSAGE_TYPE_ERROR,
								  array( 'Predicate' => $predicate ) );			// !@! ==>
				}
				elseif( $predicate instanceof COntologyTerm )
				{
					$pred = $predicate->GID();
					if( ! strlen( $pred ) )
						throw new CException
								( "Empty term global identifier",
								  kERROR_INVALID_PARAMETER,
								  kMESSAGE_TYPE_ERROR,
								  array( 'Predicate' => $predicate ) );			// !@! ==>
				}
				elseif( $predicate instanceof Everyman\Neo4j\Relationship )
				{
					$pred = $predicate->getType();
					if( ! strlen( $pred ) )
						throw new CException
								( "Empty edge type",
								  kERROR_INVALID_PARAMETER,
								  kMESSAGE_TYPE_ERROR,
								  array( 'Predicate' => $predicate ) );			// !@! ==>
					if( $theObject === NULL )
						$theObject = $predicate->getEndNode();
				}
				else
				{
					$pred = (string) $predicate;
					if( ! strlen( $pred ) )
						throw new CException
								( "Predicate is empty",
								  kERROR_INVALID_PARAMETER,
								  kMESSAGE_TYPE_ERROR,
								  array( 'Predicate' => $predicate ) );			// !@! ==>
				}
				
				$predicates[] = $predicate;
			
			} // Iterating predicates.
		
		} // Provided predicates.
		
		//
		// Get edges.
		//
		$relations = Array();
		$edges = $subject->getRelationships( $predicates, Relationship::DirectionIn );
		foreach( $edges as $edge )
		{
			//
			// Get predicate list.
			//
			$list = ( array_key_exists( ($predicate = $edge->getType()), $relations ) )
				  ? $relations[ $predicate ]
				  : Array();
			
			//
			// Create predicate entry.
			//
			if( ! array_key_exists( ($predicate = $edge->getType()), $relations ) )
			{
				$relations[ $predicate ] = Array();
				$rel = & $relations[ $predicate ];
			}
			else
				$rel = & $relations[ $predicate ];
			
			//
			// Add object node.
			//
			$rel[] = new COntologyNode( $theContainer, $edge->getEndNode() );
		}
		
		return $relations;															// ==>

	} // RelatedTo.

	 

} // class COntologyNode.


?>
