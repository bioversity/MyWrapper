<?php

/**
 * <i>COntologyNode</i> class definition.
 *
 * This file contains the class definition of <b>COntologyNode</b> which wraps an ontology
 * around a {@link COntologyTerm term} and a graph root {@link CGraphNode node}.
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
 * Wrapper defines.
 *
 * This include file contains the data wrapper definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CDataWrapper.inc.php" );

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
	Everyman\Neo4j\Batch,
	Everyman\Neo4j\Client,
	Everyman\Neo4j\Node,
	Everyman\Neo4j\Relationship,
	Everyman\Neo4j\Index\NodeIndex,
	Everyman\Neo4j\Index\RelationshipIndex,
	Everyman\Neo4j\Index\NodeFulltextIndex;

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
 *											MAGIC										*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	__toString																		*
	 *==================================================================================*/

	/**
	 * Return object identifier.
	 *
	 * In this class we return the {@link Term() term} {@link Node() node}
	 * {@link kTAG_TERM property}.
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
			$term = $node->getProperty( kTAG_TERM );
			if( $term !== NULL )
				return $term;														// ==>
		}
		
		return '';																	// ==>
	
	} // __toString.

		

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

	 
	/*===================================================================================
	 *	Kind																			*
	 *==================================================================================*/

	/**
	 * Manage node kind.
	 *
	 * This method can be used to manage the node {@link kTAG_KIND kinds}, in general it
	 * reflects the {@link Term() term} {@link CCodedUnitObject::Kind() kinds}.
	 *
	 * For a more thorough reference of how this method works, please consult the
	 * {@link _ManagePropertyArray() _ManagePropertyArray} method, in which the first
	 * parameter will be the constant {@link kTAG_KIND kTAG_KIND}.
	 *
	 * The method will also set the {@link _IsDirty() dirty}
	 * {@link kFLAG_STATE_DIRTY status}.
	 *
	 * @param mixed					$theValue			Value or index.
	 * @param mixed					$theOperation		Operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses _ManagePropertyArray()
	 *
	 * @see kTAG_KIND
	 */
	public function Kind( $theValue = NULL, $theOperation = NULL, $getOld = FALSE )
	{
		//
		// Normalise value.
		//
		if( ($theValue !== NULL)
		 && ($theValue !== FALSE)
		 && (! is_array( $theValue )) )
			$theValue = (string) $theValue;
		
		return $this->_ManagePropertyArray( kTAG_KIND, $theValue,
													   $theOperation,
													   $getOld );					// ==>

	} // Kind.

	 
	/*===================================================================================
	 *	Type																			*
	 *==================================================================================*/

	/**
	 * Manage node type.
	 *
	 * This method can be used to manage the node {@link kTAG_TYPE type}, in general it
	 * reflects the {@link Term() term} {@link COntologyTerm::Type() type}.
	 *
	 * The method accepts the following parameters:
	 *
	 * <ul>
	 *	<li><b>$theValue</b>: The value or operation:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the current value.
	 *		<li><i>FALSE</i>: Delete Set value exception.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value <i>before</i> it was eventually modified.
	 *		<li><i>FALSE</i>: Return the value <i>after</i> it was eventually modified.
	 *	 </ul>
	 * </ul>
	 *
	 * The method will also set the {@link _IsDirty() dirty}
	 * {@link kFLAG_STATE_DIRTY status}.
	 *
	 * @param mixed					$theValue			Term or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses _ManageProperty()
	 *
	 * @see kTAG_TYPE
	 */
	public function Type( $theValue = NULL, $getOld = FALSE )
	{
		//
		// Normalise value.
		//
		if( ($theValue !== NULL)
		 && ($theValue !== FALSE) )
			$theValue = (string) $theValue;
		
		return $this->_ManageProperty( kTAG_TYPE, $theValue, $getOld );				// ==>

	} // Type.

	 
	/*===================================================================================
	 *	Cardinality																		*
	 *==================================================================================*/

	/**
	 * Manage data cardinality.
	 *
	 * This method can be used to manage the measure data {@link kTAG_CARDINALITY type}, it
	 * uses the standard accessor {@link CAttribute::ManageOffset() method} to manage the
	 * {@link kTAG_TYPE offset}:
	 *
	 * <ul>
	 *	<li><b>$theValue</b>: The value or operation:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the current value.
	 *		<li><i>FALSE</i>: Delete the value.
	 *		<li><i>string</i>: String values represent the cardinality enumeration:
	 *		 <ul>
	 *			<li><i>{@link kCARD_0_1 kCARD_0_1}</i>: Zero or one, a scalar or none.
	 *			<li><i>{@link kCARD_1 kCARD_1}</i>: Exactly one, a required scalar.
	 *			<li><i>{@link kCARD_ANY kCARD_ANY}</i>: Any, this implies that we either
	 *				have an array or no data.
	 *		 </ul>
	 *		<li><i>integer</i>: An integer represents the exact cardinality, in that case
	 *			we assume the data is an array of at most that number of elements.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value <i>before</i> it was eventually modified.
	 *		<li><i>FALSE</i>: Return the value <i>after</i> it was eventually modified.
	 *	 </ul>
	 * </ul>
	 *
	 * @param NULL|FALSE|string		$theValue			Data type tag.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 */
	public function Cardinality( $theValue = NULL, $getOld = FALSE )
	{
		//
		// Normalise value.
		//
		if( ($theValue !== NULL)
		 && ($theValue !== FALSE) )
		{
			switch( $theValue )
			{
				case kCARD_1:
				case kCARD_0_1:
				case kCARD_ANY:
					break;
				
				default:
					throw new CException
							( "Unsupported cardinality type",
							  kERROR_UNSUPPORTED,
							  kMESSAGE_TYPE_ERROR,
							  array( 'Cardinality' => $theValue ) );			// !@! ==>
			}
		}
		
		return $this->_ManageProperty( kTAG_CARDINALITY, $theValue, $getOld );		// ==>

	} // Cardinality.

	 
	/*===================================================================================
	 *	Domain																			*
	 *==================================================================================*/

	/**
	 * Manage node kind.
	 *
	 * This method can be used to manage the node {@link kTAG_DOMAIN domains}, in general it
	 * reflects the {@link Term() term} {@link COntologyTermObject::Domain() domains}.
	 *
	 * For a more thorough reference of how this method works, please consult the
	 * {@link _ManagePropertyArray() _ManagePropertyArray} method, in which the first
	 * parameter will be the constant {@link kTAG_DOMAIN kTAG_DOMAIN}.
	 *
	 * The method will also set the {@link _IsDirty() dirty}
	 * {@link kFLAG_STATE_DIRTY status}.
	 *
	 * @param mixed					$theValue			Term or index.
	 * @param mixed					$theOperation		Operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses _ManagePropertyArray()
	 *
	 * @see kTAG_DOMAIN
	 */
	public function Domain( $theValue = NULL, $theOperation = NULL, $getOld = FALSE )
	{
		//
		// Normalise value.
		//
		if( ($theValue !== NULL)
		 && ($theValue !== FALSE)
		 && (! is_array( $theValue )) )
			$theValue = (string) $theValue;
		
		return $this->_ManagePropertyArray( kTAG_DOMAIN, $theValue,
														 $theOperation,
														 $getOld );					// ==>

	} // Domain.

	 
	/*===================================================================================
	 *	Category																		*
	 *==================================================================================*/

	/**
	 * Manage node kind.
	 *
	 * This method can be used to manage the node {@link kTAG_CATEGORY categories}, in
	 * general it reflects the {@link Term() term}
	 * {@link COntologyTermObject::Category() categories}.
	 *
	 * For a more thorough reference of how this method works, please consult the
	 * {@link _ManagePropertyArray() _ManagePropertyArray} method, in which the first
	 * parameter will be the constant {@link kTAG_CATEGORY kTAG_CATEGORY}.
	 *
	 * The method will also set the {@link _IsDirty() dirty}
	 * {@link kFLAG_STATE_DIRTY status}.
	 *
	 * @param mixed					$theValue			Term or index.
	 * @param mixed					$theOperation		Operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses _ManagePropertyArray()
	 *
	 * @see kTAG_CATEGORY
	 */
	public function Category( $theValue = NULL, $theOperation = NULL, $getOld = FALSE )
	{
		//
		// Normalise value.
		//
		if( ($theValue !== NULL)
		 && ($theValue !== FALSE)
		 && (! is_array( $theValue )) )
			$theValue = (string) $theValue;
		
		return $this->_ManagePropertyArray( kTAG_CATEGORY, $theValue,
														   $theOperation,
														   $getOld );				// ==>

	} // Category.

		

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
	 * and an object node using a predicate node. This method consideres the triplet subject
	 * node/predicate term/object node as the relationship identifier when considering
	 * duplicate relationships. For that reason the subject and object terms of the
	 * relationship must relate to {@link _IsCommitted() committed} nodes.
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
	 *		<li><i>COntologyNode</i>: The node {@link Term() term} {@link kTAG_GID global}
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
	 *	<li><b>$doDuplicate</b>: This flag determines whether cuplicate relationships are
	 *		allowed: if <i>FALSE</i>, if a relationship with the same subject and object
	 *		nodes, along with the same predicate term are found, it will be considered a
	 *		duplicate, and this method will actually return that relationship.
	 * </ul>
	 *
	 * The method will return a {@link COntologyEdge COntologyEdge} object, or raise an
	 * exception if the operation was not successful.
	 *
	 * @param reference				$theContainer		Object container.
	 * @param mixed					$thePredicate		Predicate.
	 * @param mixed					$theObject			Object.
	 * @param boolean				$doDuplicate		TRUE means duplicate relationship.
	 *
	 * @access public
	 * @return COntologyEdge
	 */
	public function RelateTo( $theContainer, $thePredicate, $theObject = NULL,
															$doDuplicate = FALSE )
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
		if( $thePredicate instanceof self )
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
		elseif( $theObject instanceof self )
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
		// Check duplicate relation.
		//
		if( ! $doDuplicate )
		{
			//
			// Build composite container.
			//
			$composite = array( kTAG_NODE => $theContainer[ kTAG_NODE ],
								kTAG_TERM => $theContainer[ kTAG_TERM ]->Database() );
			
			//
			// Check for duplicate.
			//
			try
			{
				//
				// Build query.
				//
				$query = new CMongoQuery();
				$query->AppendStatement(
					CQueryStatement::Equals(
						kTAG_PATH,
						COntologyEdgeIndex::EdgeNodePath(
							$subject,
							$predicate,
							$object ) ),
					kOPERATOR_AND );
				
				//
				// Instantiate index.
				// This should raise a kERROR_NOT_FOUND exception
				// if the edge index does not exist: in that case
				// it means that we are creating a new relationship.
				//
				$index = new COntologyEdgeIndex( $query, $composite );
				
				return new COntologyEdge( $theContainer, $index->Node() );			// ==>
			}
			catch( CException $error )
			{
				//
				// Handle errors.
				//
				if( $error->getCode() != kERROR_NOT_FOUND )
					throw $error;												// !@! ==>
			}
		
		} // Don't allow duplicate relationships.
		
		//
		// Create edge node.
		//
		$edge = parent::RelateTo( $theContainer[ kTAG_NODE ], $predicate, $object );
		
		return new COntologyEdge( $theContainer, $edge );							// ==>

	} // RelateTo.

	 
	/*===================================================================================
	 *	GetRelated																		*
	 *==================================================================================*/

	/**
	 * Return related {@link COntologyEdge edges}.
	 *
	 * This method will return all nodes that are related to the current one in an array of
	 * {@link COntologyEdge edge} nodes whose elements are structured as follows:
	 *
	 * <ul>
	 *	<li><i>Key</i>: The edge ID.
	 *	<li><i>Value</i>: The edge {@link COntologyEdge object}.
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
	 *		<li><i>COntologyNode</i>: The node {@link Term() term} {@link kTAG_GID global}
	 *			{@link COntologyTerm::GID() identifier}.
	 *		<li><i>{@link COntologyTerm COntologyTerm}</i>: The term {@link kTAG_GID global}
	 *			identifier will be used as the {@link COntologyEdge node}
	 *			{@link COntologyEdge::Type() type}.
	 *		<li><i>Everyman\Neo4j\Relationship</i>: The relationship's type will be used as
	 *			the predicate, all other elements of the provided edge node will be ignored.
	 *		<li><i>string</i>: Any other type will be converted to string and will be used
	 *			as the {@link COntologyEdge node} {@link COntologyEdge::Type() type}.
	 *	 </ul>
	 *	<li><b>$theDirection</b>: The direction of the relation:
	 *	 <ul>
	 *		<li><i>out</i>: Outgoing relations, the current node is the subject.
	 *		<li><i>in</i>: Incoming relations, the current node is the object.
	 *		<li><i>all</i>: Incoming and outgoing relations (default), the current node is
	 *			the object or the subject.
	 *	 </ul>
	 * </ul>
	 *
	 * @param reference				$theContainer		Object container.
	 * @param mixed					$thePredicate		Predicate.
	 * @param string				$theDirection		Relation direction.
	 *
	 * @access protected
	 * @return array
	 *
	 * @throws {@link CException CException}
	 */
	public function GetRelated( $theContainer, $thePredicate = NULL, $theDirection = 'all' )
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
				if( $predicate instanceof self )
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
		$edges = $subject->getRelationships( $predicates, $theDirection );
		foreach( $edges as $edge )
			$relations[ $edge->getId() ]
				= new COntologyEdge( $theContainer, $edge );
		
		return $relations;															// ==>

	} // GetRelated.

		

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
		// Init array.
		//
		$array = ( $this->mTerm !== NULL )
			   ? array_merge( $this->mTerm->getArrayCopy(),
			   				  parent::getArrayCopy() )
			   : parent::getArrayCopy();
		
		//
		// Set ID to edge node ID.
		//
		$array[ kTAG_LID ] = $this->Node()->getId();
		
		return $array;																// ==>
	
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
	 * We also overload this method to store the node properties into a Mongo collection
	 * named {@link kDEFAULT_CNT_NODES kDEFAULT_CNT_NODES}, the record will be indexed by
	 * node ID as a 64 bit integer.
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
		// Instantiate node index before deleting node.
		//
		if( $theModifiers & kFLAG_PERSIST_DELETE )
		{
			//
			// Only if persistent.
			//
			if( $this->mNode->hasId() )
				$index = new COntologyNodeIndex( $this->mNode );
		}
		
		//
		// Call parent method.
		// The parent will take care of saving or deleting the node.
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
			// This is only done to the local copy,
			// because we use the modification protocol in order
			// not to have to re-load the term.
			//
			$this->mTerm->Node( $id, TRUE );
			
			//
			// Create term.
			//
			// Note that you should always attempt to locate the term
			// before passing it to this class, because if the term is
			// not persistent, we commit it here.
			//
			// The better workflow is to explicitly commit the term,
			// before committing the node.
			//
			if( ! $this->mTerm->_IsCommitted() )
				$this->mTerm->Commit( $theContainer[ kTAG_TERM ] );
			
			//
			// Ask container to modify persistent term.
			//
			// We do this in order to ensure that new versions of the term
			// will not be overwritten by old versions.
			//
			// Note that the term itself is not able to perform this operation,
			// since the commit method has no parameters for providing
			// the modification matrix: that's why we ask the container to do it.
			//
			else
			{
				$mod = array( kTAG_NODE => $id );
				$theContainer[ kTAG_TERM ]->Commit
					( $mod, $this->mTerm[ kTAG_LID ], kFLAG_PERSIST_MODIFY +
													  kFLAG_MODIFY_ADDSET +
													  kFLAG_STATE_ENCODED );
			}
			
			//
			// Add term indexes.
			//
			// We currently store nodes and edges in Mongo,
			// so we do not use Lucene for searching on node
			// and edge properties.
			//
		//	$this->_IndexTerms( $theContainer[ kTAG_NODE ] );
			
			//
			// Add node indexes.
			//
			// We currently store nodes and edges in Mongo,
			// so we do not use Lucene for searching on node
			// and edge properties.
			//
		//	$this->_IndexNodes( $theContainer[ kTAG_NODE ] );
			
			//
			// Add Mongo node index.
			//
			$index = new COntologyNodeIndex( $this->mNode );
			$index->Commit( $theContainer[ kTAG_TERM ]->Database() );
		
		} // Saving.
		
		//
		// Handle delete.
		//
		else
		{
			//
			// Check if node is valid.
			//
			if( isset( $index ) )
			{
				//
				// Ask container to modify persistent term.
				//
				// We do this in order to ensure that new versions of the term
				// will not be overwritten by old versions.
				//
				// Note that the term itself is not able to perform this operation,
				// since the commit method has no parameters for providing
				// the modification matrix: that's why we ask the container to do it.
				//
				$mod = array( kTAG_NODE => $id );
				$theContainer[ kTAG_TERM ]->Commit
					( $mod, $this->mTerm[ kTAG_LID ], kFLAG_PERSIST_MODIFY +
													  kFLAG_MODIFY_PULL +
													  kFLAG_STATE_ENCODED );
				
				//
				// Reset term
				//
				$this->Term( new COntologyTerm() );
				
				//
				// Remove node reference.
				//
				$index->Commit( $theContainer[ kTAG_TERM ]->Database(),
								kFLAG_PERSIST_DELETE );
			
			} // Set index from persistent node.
		
		} // Deleting.
		
		return $id;																	// ==>
	
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
	 * We then call the {@link CGraphNode parent}
	 * {@link CGraphNode::_PrepareCreate() method} and check if the container was replaced
	 * with the content: in that case we try to load the related {@link Term() term}.
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
		// Check terms container.
		//
		if( $term_cont === NULL )
			throw new CException
					( "Missing term container",
					  kERROR_OPTION_MISSING,
					  kMESSAGE_TYPE_ERROR );									// !@! ==>
			
		//
		// Check terms container type.
		//
		if( ! $term_cont instanceof CContainer )
			throw new CException
					( "Unsupported term container type",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Container' => $term_cont ) );						// !@! ==>
		
		//
		// Handle provided ontology.
		//
		if( $theIdentifier instanceof self )
			$theIdentifier = $theIdentifier->Node();
		
		//
		// Call node method.
		//
		parent::_PrepareCreate( $node_cont, $theIdentifier, $theModifiers );
		
		//
		// Handle provided node.
		// Note that $node_cont will have become a node
		// if a node was provided in the $theIdentifier
		// and $theIdentifier will have been set to NULL.
		//
		if( $node_cont instanceof Everyman\Neo4j\PropertyContainer )
		{
			//
			// Load term from node.
			//
			$term = $node_cont->getProperty( kTAG_TERM );
			if( $term !== NULL )
				$this->Term(
					CPersistentUnitObject::NewObject(
						$theContainer[ kTAG_TERM ],
						COntologyTermObject::HashIndex( $term ),
						kFLAG_STATE_ENCODED ) );
			
			//
			// Set content.
			//
			$theContainer = $node_cont;
		
		} // Provided node.
		
		//
		// Handle empty identifier:
		// set container to graph client.
		//
		elseif( $theIdentifier === NULL )
			$theContainer = $node_cont;
	
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
	 * We also copy the {@link CCodedUnitObject::Kind() kind},
	 * {@link COntologyTermObject::Domain() domain},
	 * {@link COntologyTermObject::Category() category} and
	 * {@link COntologyTerm::Type() type} elements, if not yet present, from the
	 * {@link Term() term} to the current node.
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
		// Handle save.
		//
		if( ! ($theModifiers & kFLAG_PERSIST_DELETE) )
		{
			//
			// Commit term.
			//
			$this->mTerm->Commit( $term_cont );
		
			//
			// Set term reference.
			//
			$this->offsetSet( kTAG_TERM, $this->mTerm[ kTAG_GID ] );
		
		} // Not deleting.
		
	} // _PrepareCommit.

	 
	/*===================================================================================
	 *	_FinishCreate																	*
	 *==================================================================================*/

	/**
	 * Normalise after a {@link _Create() create}.
	 *
	 * In this class we initialise the {@link Term() term} if necessary.
	 *
	 * @param reference			   &$theContainer		Object container.
	 *
	 * @access protected
	 */
	protected function _FinishCreate( &$theContainer )
	{
		//
		// Initialise term.
		//
		if( $this->Term() === NULL )
			$this->Term( new COntologyTerm() );
		
		//
		// Call parent method.
		//
		if( is_array( $theContainer )
		 || ($theContainer instanceof ArrayObject) )
			parent::_FinishCreate( $theContainer[ kTAG_NODE ] );
		else
			parent::_FinishCreate( $theContainer );
		
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
		$ref = $this->offsetGet( kTAG_TERM );
		if( $ref !== NULL )
		{
			//
			// Load term.
			//
			$term = CPersistentUnitObject::NewObject
						( $theContainer[ kTAG_TERM ],
						  COntologyTermObject::HashIndex( $ref ),
						  kFLAG_STATE_ENCODED );
			if( $term )
				$this->Term( $term );
			else
				throw new CException
						( "Invalid term reference",
						  kERROR_NOT_FOUND,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Term' => $ref ) );							// !@! ==>
		
		} // Has term reference
		
		//
		// Init term.
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
	 *	_IndexNodes																		*
	 *==================================================================================*/

	/**
	 * Create node property indexes.
	 *
	 * This method will save node indexes after the node was {@link _Commit() committed},
	 * there are two main indexes for node properties:
	 *
	 * <ul>
	 *	<li><i>{@link kINDEX_NODE_NODE kINDEX_NODE_NODE}</i>: This index (NodeIndex) links
	 *		the node to its properties through the following keys:
	 *	 <ul>
	 *		<li><i>{@link kTAG_TYPE kTAG_TYPE}</i>: This key links the current node to its
	 *			{@link Type() type}, which may either be inherited from its
	 *			{@link Term() term} or have been {@link Type() explicitly} set.
	 *		<li><i>{@link kTAG_KIND kTAG_KIND}</i>: This key links the current node to its
	 *			{@link Kind() kinds}, which may either be inherited from its
	 *			{@link Term() term} or have been {@link Kind() explicitly} set.
	 *		<li><i>{@link kTAG_DOMAIN kTAG_DOMAIN}</i>: This key links the current node to
	 *			its {@link Domain() domains}, which may either be inherited from its
	 *			{@link Term() term} or have been {@link Domain() explicitly} set.
	 *		<li><i>{@link kTAG_CATEGORY kTAG_CATEGORY}</i>: This key links the current node
	 *			to its {@link Category() categories}, which may either be inherited from its
	 *			{@link Category() term} or have been {@link Category() explicitly} set.
	 *	 </ul>
	 * </ul>
	 *
	 * @param Everyman\Neo4j\Client	$theContainer		Node container.
	 *
	 * @access protected
	 */
	protected function _IndexNodes( Everyman\Neo4j\Client $theContainer )
	{
		//
		// Load term and node.
		//
		$node = $this->Node();
		
		//
		// Instantiate node index.
		//
		$idx = $this->_GetNodeIndex( $theContainer, kINDEX_NODE_NODE, TRUE );
	
		//
		// Add type.
		//
		if( ($tmp = $this->Type()) !== NULL )
			$idx->add( $node, kTAG_TYPE, $tmp );
	
		//
		// Add kinds.
		//
		if( ($tmp = $this->Kind()) !== NULL )
		{
			foreach( $tmp as $element )
				$idx->add( $node, kTAG_KIND, $element );
		}
	
		//
		// Add domains.
		//
		if( ($tmp = $this->Domain()) !== NULL )
		{
			foreach( $tmp as $element )
				$idx->add( $node, kTAG_DOMAIN, $element );
		}
	
		//
		// Add categories.
		//
		if( ($tmp = $this->Category()) !== NULL )
		{
			foreach( $tmp as $element )
				$idx->add( $node, kTAG_CATEGORY, $element );
		}
	
	} // _IndexNodes.

	 
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

		

/*=======================================================================================
 *																						*
 *								PROTECTED PROPERTY UTILITIES							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_ManageProperty																	*
	 *==================================================================================*/

	/**
	 * Manage scalar property.
	 *
	 * This method will set, retrieve and delete scalar properties, it accepts the following
	 * parameters:
	 *
	 * <ul>
	 *	<li><b>$theOffset</b>: The property offset to manage.
	 *	<li><b>$theValue</b>: The property value or operation:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the property current value.
	 *		<li><i>FALSE</i>: Delete the property.
	 *		<li><i>other</i>: Any other type represents the new property value.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value of the property <i>before</i> it was
	 *			eventually modified.
	 *		<li><i>FALSE</i>: Return the value of the property <i>after</i> it was
	 *			eventually modified.
	 *	 </ul>
	 * </ul>
	 *
	 * Note that if the current object does no yet have a {@link Node() node} reference, the
	 * method will raise an exception.
	 *
	 * @param string				$theOffset			Property key.
	 * @param mixed					$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access protected
	 * @return mixed
	 *
	 * @throws {@link CException CException}
	 *
	 * @uses Node()
	 * @uses _IsDirty()
	 */
	protected function _ManageProperty( $theOffset, $theValue = NULL, $getOld = FALSE )
	{
		//
		// Save current value.
		//
		$node = $this->Node();
		$save = ( $node !== NULL )
			  ? $node->getProperty( $theOffset )
			  : NULL;
		
		//
		// Retrieve value.
		//
		if( $theValue === NULL )
			return $save;															// ==>
		
		//
		// Delete value.
		//
		if( $theValue === FALSE )
		{
			if( $node !== NULL )
			{
				$node->removeProperty( $theOffset );
				$this->_IsDirty( TRUE );
			}
			
			if( $getOld )
				return $save;														// ==>
			
			return NULL;															// ==>
		}
		
		//
		// Set type.
		//
		if( $node !== NULL )
		{
			$node->setProperty( $theOffset, $theValue );
			$this->_IsDirty( TRUE );
			
			if( $getOld )
				return $save;														// ==>
			
			return $theValue;														// ==>
		}
		
		throw new CException
				( "Unable to set node property: missing node reference",
				  kERROR_OPTION_MISSING,
				  kMESSAGE_TYPE_ERROR );										// !@! ==>
	
	} // _ManageProperty.

	 
	/*===================================================================================
	 *	_ManagePropertyArray															*
	 *==================================================================================*/

	/**
	 * Manage scalar property.
	 *
	 * This method will set, retrieve and delete array element properties, it accepts the
	 * following parameters:
	 *
	 * <ul>
	 *	<li><b>$theOffset</b>: The property offset to manage.
	 *	<li><b>$theValue</b>: This parameter represents either the value to add, or the
	 *		index of the element to operate on:
	 *	 <ul>
	 *		<li><i>NULL</i>: This value indicates that we want to operate on all elements,
	 *			which means that we are retrieving the full list or deleting it.
	 *		<li><i>array</i>: This value indicates that we want to replace the whole list,
	 *			this will only be tested if the next parameter evaluates to <i>TRUE</i>.
	 *		<li><i>other</i>: Any other type represents either the new value to be added or
	 *			the index to the value to be returned or deleted. <i>It must be possible to
	 *			cast this value to a string, this is what will be used to compare
	 *			elements</i>.
	 *	 </ul>
	 *	<li><b>$theOperation</b>: This parameter represents the operation to be performed,
	 *		it will be evaluated as a boolean and its scope depends on the value of the
	 *		previous parameter:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the element or list.
	 *		<li><i>FALSE</i>: Delete the element or list.
	 *		<li><i>TRUE</i>: Add the element or list. Note that with this value, if you
	 *			provide <i>NULL</i> in the previous parameter, it will be equivalent to
	 *			deleting the whole list.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the element or list <i>before</i> it was eventually
	 *			modified.
	 *		<li><i>FALSE</i>: Return the element or list <i>after</i> it was eventually
	 *			modified.
	 *	 </ul>
	 * </ul>
	 *
	 * @param string				$theOffset			Property key.
	 * @param mixed					$theValue			Value or index.
	 * @param mixed					$theOperation		Operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @return mixed
	 *
	 * @throws {@link CException CException}
	 *
	 * @uses Node()
	 * @uses _IsDirty()
	 */
	protected function _ManagePropertyArray( $theOffset, $theValue = NULL,
														 $theOperation = NULL,
														 $getOld = FALSE )
	{
		//
		// Save current node.
		//
		$node = $this->Node();
		
		//
		// Save current list.
		//
		$list = Array();
		$save = ( $node !== NULL )
			  ? $node->getProperty( $theOffset )
			  : NULL;
		if( $save !== NULL )
		{
			foreach( $save as $element )
				$list[ md5( $element, TRUE ) ] = $element;
		}
		
		//
		// Return element or list.
		//
		if( $theOperation === NULL )
		{
			//
			// Return full list or no list.
			//
			if( ($save === NULL)		// Empty list,
			 || ($theValue === NULL) )	// return full list.
				return $save;														// ==>
			
			//
			// Scan list.
			//
			if( array_key_exists( ($key = md5( (string) $theValue, TRUE )), $list ) )
				return $list[ $key ];												// ==>
			
			return NULL;															// ==>
		
		} // Return element or list.

		//
		// Delete element or list.
		//
		if( $theOperation === FALSE )
		{
			//
			// Missing list.
			//
			if( $save === NULL )
				return NULL;														// ==>
			
			//
			// Delete full list.
			//
			if( $theValue === NULL )
			{
				//
				// Delete list.
				//
				if( $node !== NULL )
				{
					$node->removeProperty( $theOffset );
					$this->_IsDirty( TRUE );
				}
				
				if( $getOld )
					return $save;													// ==>
				
				return NULL;														// ==>
			}
			
			//
			// Scan list.
			//
			if( $save !== NULL )
			{
				//
				// Find element.
				//
				if( array_key_exists( ($key = md5( (string) $theValue, TRUE )), $list ) )
				{
					//
					// Save old.
					//
					$old = $list[ $key ];
					
					//
					// Remove element.
					//
					unset( $list[ $key ] );
					
					//
					// Update object.
					//
					if( count( $list ) )
						$node->setProperty( $theOffset, array_values( $list ) );
					else
						$node->removeProperty( $theOffset );
					
					//
					// Set dirty flag.
					//
					$this->_IsDirty( TRUE );
					
					if( $getOld )
						return $old;												// ==>
				
				} // Found element.
			
			} // Has list.
			
			return NULL;															// ==>
		
		} // Delete element or list.
		
		//
		// Delete full list.
		// At this pont the operation involves
		// adding and the value is NULL.
		//
		if( $theValue === NULL )
		{
			//
			// Delete list.
			//
			if( $node !== NULL )
			{
				//
				// Delete property.
				//
				$node->removeProperty( $theOffset );
				
				//
				// Set dirty flag.
				//
				$this->_IsDirty( TRUE );
			}
			
			if( $getOld )
				return $save;														// ==>
			
			return NULL;															// ==>
		}
		
		//
		// Handle node.
		//
		if( $node !== NULL )
		{
			//
			// Replace full list.
			//
			if( is_array( $theValue ) )
			{
				//
				// Replace offset.
				//
				$node->setProperty( $theOffset, $theValue );
				
				//
				// Set dirty flag.
				//
				$this->_IsDirty( TRUE );
				
				if( $getOld )
					return $save;													// ==>
				
				return $theValue;													// ==>
			
			} // Replace full list.
			
			//
			// Add first element.
			//
			if( $save === NULL )
				$node->setProperty( $theOffset, array( $theValue ) );
			
			//
			// Set element.
			//
			else
			{
				//
				// Set in list.
				//
				$list[ md5( (string) $theValue, TRUE ) ] = (string) $theValue;
				
				//
				// Replace offset.
				//
				$node->setProperty( $theOffset, array_values( $list ) );
			}
		
			if( $getOld )
				return NULL;														// ==>
			
			return $theValue;														// ==>
		}
		
		throw new CException
				( "Unable to set node property: missing node reference",
				  kERROR_OPTION_MISSING,
				  kMESSAGE_TYPE_ERROR );										// !@! ==>
	
	} // _ManagePropertyArray.

	 

} // class COntologyNode.


?>
