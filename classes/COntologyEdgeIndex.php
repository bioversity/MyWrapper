<?php

/**
 * <i>COntologyEdgeIndex</i> class definition.
 *
 * This file contains the class definition of <b>COntologyEdgeIndex</b> which represents a
 * Neo4j edge node index.
 *
 *	@package	MyWrapper
 *	@subpackage	Persistence
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 31/05/2012
 */

/*=======================================================================================
 *																						*
 *								COntologyEdgeIndex.php									*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This includes the ancestor class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."COntologyNodeIndex.php" );

/**
 * Tokens.
 *
 * This include file contains all token definitions.
 */
require_once( kPATH_LIBRARY_DEFINES."Tokens.inc.php" );

/**
 * Edge index.
 *
 * This class implements a graph edge node index, it can be used to replicate a Neo4j edge
 * node to a MongoDB collection.
 *
 * Neo4j uses Lucene as its standard indexing tool, in this library we use MongoDB as the
 * standard database, so it is a logical choice to use Mongo as the indexing mechanism for
 * Neo4j. Also, by storing nodes in Mongo, by dumping the database contents we also have the
 * graph structure with it.
 *
 * The class features a single data member, the {@link Node() node} which contains a Neo4j
 * edge node reference. When {@link __construct() instantiating} this class you are required to
 * provide a persistent instance of a node.
 *
 * The contents of the object will be the node properties as will be stored in the Mongo
 * {@link kDEFAULT_CNT_EDGES default} collection for edges:
 *
 * <ul>
 *	<li><i>{@link kTAG_LID kTAG_LID}</i>: This offset will hold the edge node ID.
 *	<li><i>{@link kTAG_PATH kTAG_PATH}</i>: This offset will hold the edge node path, that
 *		is, the source node ID, the predicate term {@link kTAG_GID identifier} and the
 *		object node ID, all three separated by the
 *		{@link kTOKEN_INDEX_SEPARATOR kTOKEN_INDEX_SEPARATOR} token.
 *	<li><i>{@link kTAG_SUBJECT kTAG_SUBJECT}</i>: This offset will hold the subject node
 *		information:
 *	 <ul>
 *		<li><i>{@link kTAG_TERM kTAG_TERM}</i>: The subject node term
 *			{@link kTAG_GID identifier}.
 *		<li><i>{@link kTAG_NODE kTAG_NODE}</i>: The subject node ID.
 *	 </ul>
 *	<li><i>{@link kTAG_PREDICATE kTAG_PREDICATE}</i>: This offset will hold the predicate
 *		information:
 *	 <ul>
 *		<li><i>{@link kTAG_TERM kTAG_TERM}</i>: The predicate term
 *			{@link kTAG_GID identifier}, which corresponds to the edge node type.
 *		<li><i>{@link kTAG_NODE kTAG_NODE}</i>: The edge node ID.
 *	 </ul>
 *	<li><i>{@link kTAG_OBJECT kTAG_OBJECT}</i>: This offset will hold the object node
 *		information:
 *	 <ul>
 *		<li><i>{@link kTAG_TERM kTAG_TERM}</i>: The object node term
 *			{@link kTAG_GID identifier}.
 *		<li><i>{@link kTAG_NODE kTAG_NODE}</i>: The object node ID.
 *	 </ul>
 *	<li><i>{@link kTAG_DATA kTAG_DATA}</i>: This offset will hold the edge node properties.
 * </ul>
 *
 * The class provides a single member accessor method: {@link Node() Node}, which can be
 * used to set the referenced edge node.
 *
 * <i>Note that the class will not cast to an array correctly, you must use the
 * {@link getArrayCopy() getArrayCopy} method to get an array, if you know how to solve
 * this, please do it!</i>
 *
 *	@package	MyWrapper
 *	@subpackage	Persistence
 */
class COntologyEdgeIndex extends COntologyNodeIndex
{
		

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
	 * We {@link COntologyNodeIndex::Node() overload} this method to ensure that the
	 * provided node is an edge node.
	 *
	 * @param mixed					$theValue			Node or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 */
	public function Node( $theValue = NULL, $getOld = FALSE )
	{
		//
		// Check node type.
		//
		if( $theValue !== NULL )
		{
			//
			// Check node type.
			//
			if( ! $theValue instanceof Everyman\Neo4j\Relationship )
				throw new CException
						( "Unsupported node type",
						  kERROR_UNSUPPORTED,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Node' => $theValue ) );						// !@! ==>
		
		} // Replacing node.
		
		return parent::Node( $theValue, $getOld );									// ==>

	} // Node.

		

/*=======================================================================================
 *																						*
 *									STATIC INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	EdgeNodePath																	*
	 *==================================================================================*/

	/**
	 * Build an edge node path.
	 *
	 * The edge path represents the unique identifier of an edge node using the relationship
	 * node identifiers, it is composed by the subject and object node IDs and the predicate
	 * term identifier.
	 *
	 * This method will return the {@link kTAG_PATH path} according to the provided
	 * parameters:
	 *
	 * <ul>
	 *	<li><b>$theSubject</b>: The relation subject node reference:
	 *	 <ul>
	 *		<li><i>{@link COntologyNode COntologyNode}</i>: The node ID will be used.
	 *		<li><i>Neo4j node</i>: The node ID will be used.
	 *		<li><i>other</i>: Other types will be cast to a string.
	 *	 </ul>
	 *	<li><b>$thePredicate</b>: The relation predicate term reference, the value will be
	 *		cast to a string and is expected to represent the {@link COntologyTerm term}
	 *		{@link kTAG_GID global} identifier.
	 *	<li><b>$theObject</b>: The relation object node reference:
	 *	 <ul>
	 *		<li><i>{@link COntologyNode COntologyNode}</i>: The node ID will be used.
	 *		<li><i>Neo4j node</i>: The node ID will be used.
	 *		<li><i>other</i>: Other types will be cast to a string.
	 *	 </ul>
	 * </ul>
	 *
	 * The method will check if all three parameters are not empty, if this is not the case,
	 * the method will raise an exception.
	 *
	 * @param mixed					$theSubject			Subject node reference.
	 * @param mixed					$thePredicate		Predicate term reference.
	 * @param mixed					$theObject			Object node reference.
	 *
	 * @static
	 * @return string
	 */
	static function EdgeNodePath( $theSubject, $thePredicate, $theObject )
	{
		//
		// Check parameters.
		//
		$terms = array( 'Subject' => $theSubject,
						'Predicate' => $thePredicate,
						'Object' => $theObject );
		foreach( $terms as $key => $value )
		{
			//
			// Init node reference.
			//
			$node = NULL;
			
			//
			// Get node reference.
			//
			if( $value instanceof COntologyNode )
				$node = $value->Node();
			elseif( $value instanceof Everyman\Neo4j\PropertyContainer )
				$node = $value;
			
			//
			// Handle node.
			//
			if( $node !== NULL )
			{
				if( $node->hasId() )
					$terms[ $key ] = $node->getId();
				else
					throw new CException
							( "The $key node is not persistent",
							  kERROR_INVALID_STATE,
							  kMESSAGE_TYPE_ERROR,
							  array( $key => $value ) );						// !@! ==>
			}
			
			//
			// Handle string.
			//
			else
			{
				$string = (string) $value;
				if( strlen( $string ) )
					$terms[ $key ] = $string;
				else
					throw new CException
							( "The $key reference is empty",
							  kERROR_INVALID_PARAMETER,
							  kMESSAGE_TYPE_ERROR,
							  array( $key => $value ) );						// !@! ==>
			}
		}
		
		return implode( kTOKEN_INDEX_SEPARATOR, $terms );							// ==>

	} // EdgeNodePath.

	 
	/*===================================================================================
	 *	EdgeTermPath																	*
	 *==================================================================================*/

	/**
	 * Build an edge term path.
	 *
	 * This edge path represents an identifier of an edge node using the relationship term
	 * identifiers, it is composed by the subject, predicate and object term
	 * {@link kTAG_GID identifiers}.
	 *
	 * This method will return the {@link kTAG_EDGE_TERM path} according to the provided
	 * parameters:
	 *
	 * <ul>
	 *	<li><b>$theSubject</b>: The relation subject term reference, the value will be
	 *		cast to a string and is expected to represent the {@link COntologyTerm term}
	 *		{@link kTAG_GID global} identifier.
	 *	<li><b>$thePredicate</b>: The relation predicate term reference, the value will be
	 *		cast to a string and is expected to represent the {@link COntologyTerm term}
	 *		{@link kTAG_GID global} identifier.
	 *	<li><b>$theObject</b>: The relation object node reference, the value will be
	 *		cast to a string and is expected to represent the {@link COntologyTerm term}
	 *		{@link kTAG_GID global} identifier.
	 * </ul>
	 *
	 * The method will check if all three parameters are not empty, if this is not the case,
	 * the method will raise an exception.
	 *
	 * @param mixed					$theSubject			Subject term reference.
	 * @param mixed					$thePredicate		Predicate term reference.
	 * @param mixed					$theObject			Object term reference.
	 *
	 * @static
	 * @return string
	 */
	static function EdgeTermPath( $theSubject, $thePredicate, $theObject )
	{
		//
		// Check parameters.
		//
		$terms = array( 'Subject' => $theSubject,
						'Predicate' => $thePredicate,
						'Object' => $theObject );
		foreach( $terms as $key => $value )
		{
			//
			// Cast to string.
			//
			$string = ( $value instanceof COntologyTerm )
					? $value->GID()
					: (string) $value;
			
			//
			// Check if not empty.
			//
			if( strlen( $string ) )
				$terms[ $key ] = $string;
			else
				throw new CException
						( "The $key reference is empty",
						  kERROR_INVALID_PARAMETER,
						  kMESSAGE_TYPE_ERROR,
						  array( $key => $value ) );							// !@! ==>
		}
		
		return implode( kTOKEN_INDEX_SEPARATOR, $terms );							// ==>

	} // EdgeTermPath.

		

/*=======================================================================================
 *																						*
 *									PROTECTED UTILITIES									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_ResolveIndexContainer															*
	 *==================================================================================*/

	/**
	 * Resolve index container.
	 *
	 * We {@link COntologyNodeIndex::_ResolveIndexContainer() overload} this class to use
	 * the correct {@link kDEFAULT_CNT_EDGES default} collection when providing a MongoDB.
	 *
	 * @param mixed					$theContainer		Index container.
	 *
	 * @access protected
	 * @return MongoCollection
	 *
	 * @throws {@link CException CException}
	 */
	protected function _ResolveIndexContainer( $theContainer )
	{
		//
		// Resolve MongoDB.
		//
		if( $theContainer instanceof MongoDB )
			return new CMongoContainer
				( $theContainer->selectCollection( kDEFAULT_CNT_EDGES ) );			// ==>
		
		return parent::_ResolveIndexContainer( $theContainer );						// ==>
	
	} // _ResolveIndexContainer.

	 
	/*===================================================================================
	 *	_LocateNode																		*
	 *==================================================================================*/

	/**
	 * Locate node in container.
	 *
	 * We {@link COntologyNodeIndex::_LocateNode() overload} this method to locate
	 * relationships in place of nodes.
	 *
	 * We also override the rule of raising an exception on zero node IDs, because the first
	 * relationship will have an ID of zero, here we check if the identifier is numeric.
	 *
	 * Finally, we handle arrays in the node parameter: in this case we assume the array
	 * holds the subject node ID, the predicate term {@link kTAG_GID identifier} and the
	 * object node ID, these elements will be used to select the edge node whose
	 * {@link kTAG_PATH kTAG_PATH} offset corresponds to this combination. In this case the
	 * provided container must be an array structured as follows:
	 *
	 * <ul>
	 *	<li><i>{@link kTAG_NODE kTAG_NODE}</i>: The graph container.
	 *	<li><i>{@link kTAG_TERM kTAG_TERM}</i>: The index container.
	 * </ul>
	 *
	 * @param mixed					$theNode			Graph node identifier.
	 * @param mixed					$theContainer		Graph container.
	 *
	 * @access protected
	 * @return Everyman\Neo4j\Node|NULL
	 *
	 * @throws {@link CException CException}
	 *
	 * @uses _ResolveIndexContainer()
	 */
	protected function _LocateNode( $theNode, $theContainer )
	{
		//
		// Check graph container type.
		//
		if( $theContainer instanceof Everyman\Neo4j\Client )
		{
			//
			// Check node.
			//
			if( is_numeric( $theNode ) )
				return $theContainer->getRelationship( $theNode );					// ==>
			
			throw new CException
					( "Invalid node identifier",
					  kERROR_INVALID_PARAMETER,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Node' => $theNode ) );							// !@! ==>
		
		} // Graph container.
		
		throw new CException
				( "Unsupported container type",
				  kERROR_UNSUPPORTED,
				  kMESSAGE_TYPE_ERROR,
				  array( 'Container' => $theContainer ) );						// !@! ==>
	
	} // _LocateNode.

	 
	/*===================================================================================
	 *	_LoadNodeProperties																*
	 *==================================================================================*/

	/**
	 * Copy node properties to object.
	 *
	 * We {@link COntologyNodeIndex::_LocateNode() overload} this method to load the edge
	 * node properties along with the subject, predicate and object information as follows:
	 *
	 * <ul>
	 *	<li><i>{@link kTAG_LID kTAG_LID}</i>: This offset will hold the edge node ID.
	 *	<li><i>{@link kTAG_PATH kTAG_PATH}</i>: This offset will hold the edge node path,
	 *		that is, the source node ID, the predicate term {@link kTAG_GID identifier} and
	 *		the object node ID, all three separated by the
	 *		{@link kTOKEN_INDEX_SEPARATOR kTOKEN_INDEX_SEPARATOR} token.
	 *	<li><i>{@link kTAG_SUBJECT kTAG_SUBJECT}</i>: This offset will hold the subject node
	 *		information:
	 *	 <ul>
	 *		<li><i>{@link kTAG_TERM kTAG_TERM}</i>: The subject node term
	 *			{@link kTAG_GID identifier}.
	 *		<li><i>{@link kTAG_NODE kTAG_NODE}</i>: The subject node ID.
	 *	 </ul>
	 *	<li><i>{@link kTAG_PREDICATE kTAG_PREDICATE}</i>: This offset will hold the
	 *		predicate information:
	 *	 <ul>
	 *		<li><i>{@link kTAG_TERM kTAG_TERM}</i>: The predicate term
	 *			{@link kTAG_GID identifier}, which corresponds to the edge node type.
	 *		<li><i>{@link kTAG_NODE kTAG_NODE}</i>: The edge node ID.
	 *	 </ul>
	 *	<li><i>{@link kTAG_OBJECT kTAG_OBJECT}</i>: This offset will hold the object node
	 *		information:
	 *	 <ul>
	 *		<li><i>{@link kTAG_TERM kTAG_TERM}</i>: The object node term
	 *			{@link kTAG_GID identifier}.
	 *		<li><i>{@link kTAG_NODE kTAG_NODE}</i>: The object node ID.
	 *	 </ul>
	 *	<li><i>{@link kTAG_DATA kTAG_DATA}</i>: This offset will hold the edge node
	 *		properties.
	 * </ul>
	 *
	 * @access protected
	 *
	 * @uses Node()
	 */
	protected function _LoadNodeProperties()
	{
		//
		// Load edge node properties.
		//
		parent::_LoadNodeProperties();
		
		//
		// Load subject info.
		//
		$data = Array();
		$node = $this->Node()->getStartNode();
		$subject_node = $data[ kTAG_NODE ] = $node->getId();
		if( ($tmp = $node->getProperty( kTAG_TERM )) !== NULL )
			$data[ kTAG_TERM ] = $tmp;
		$this->offsetSet( kTAG_SUBJECT, $data );
		
		//
		// Load predicate info.
		//
		$data = Array();
		$data[ kTAG_NODE ] = $this->Node()->getId();
		$predicate_term = $data[ kTAG_TERM ] = $this->Node()->getType();
		$this->offsetSet( kTAG_PREDICATE, $data );
		
		//
		// Load object info.
		//
		$data = Array();
		$node = $this->Node()->getEndNode();
		$object_node = $data[ kTAG_NODE ] = $node->getId();
		if( ($tmp = $node->getProperty( kTAG_TERM )) !== NULL )
			$data[ kTAG_TERM ] = $tmp;
		$this->offsetSet( kTAG_OBJECT, $data );
		
		//
		// Load path.
		//
		$this->offsetSet( kTAG_PATH,
						  self::EdgeNodePath
						  	( $subject_node, $predicate_term, $object_node ) );
	
	} // _LoadNodeProperties.

	 
	/*===================================================================================
	 *	_UpdateRelationshipCounts														*
	 *==================================================================================*/

	/**
	 * Update relationship counts.
	 *
	 * This method will increment or decrement the relationships count in the subject and
	 * object {node indexes.
	 *
	 * The method accepts the following parameters:
	 *
	 * <ul>
	 *	<li><b>$theContainer</b>: The index Mongo container, we use the
	 *		{@link CMongoContainer::Database() database} element and force the
	 *		{@link kDEFAULT_CNT_NODES default} node collection name, since it apparently
	 *		gives this class personality disorder problems to use the parent version of the
	 *		{@link _ResolveIndexContainer() _ResolveIndexContainer} method.
	 *	<li><b>$theModifiers</b>: This parameter represents the commit operation options,
	 *		these will have been passed by the {@link Commit() commit} method.
	 * </ul>
	 *
	 * These counts are stored in the {@link COntologyNodeIndex indexes} in the following
	 * structure:
	 *
	 * <ul>
	 *	<li><i>{@link kTAG_IN kTAG_IN}</i>: This element represents incoming relationships,
	 *		it is an array structured as follows:
	 *	 <ul>
	 *		<li><i>Key</i>: The term {@link kTAG_GID identifier} of the relationship
	 *			predicate.
	 *		<li><i>Value</i>: The number of incoming relationships for the given predicate.
	 *	 </ul>
	 *	<li><i>{@link kTAG_OUT kTAG_OUT}</i>: This element represents outgoing
	 *		relationships, it is an array structured as follows:
	 *	 <ul>
	 *		<li><i>Key</i>: The term {@link kTAG_GID identifier} of the relationship
	 *			predicate.
	 *		<li><i>Value</i>: The number of outgoing relationships for the given predicate.
	 *	 </ul>
	 * </ul>
	 *
	 * @param MongoContainer		$theContainer		Mongo container.
	 * @param bitfield				$theModifiers		Commit modifiers.
	 *
	 * @access protected
	 */
	protected function _UpdateRelationshipCounts( $theContainer, $theModifiers )
	{
		//
		// Resolve container.
		//
		$container = $theContainer->Database()->selectCollection( kDEFAULT_CNT_NODES );

		//
		// Init local storage.
		//
		$options = array( 'safe' => TRUE );
		$subject_id = $this->Node()->getStartNode()->getId();
		$predicate = $this->Node()->getType();
		$object_id = $this->Node()->getEndNode()->getId();
		
		//
		// Load subject.
		//
		$subject = $container->findOne( array( kTAG_LID => $subject_id ) );
		if( ! $subject )
			throw new CException
					( "Relationship subject index is missing",
					  kERROR_INVALID_STATE,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Subject' => $subject_id ) );						// !@! ==>
		
		//
		// Load object.
		//
		$object = $container->findOne( array( kTAG_LID => $object_id ) );
		if( ! $object )
			throw new CException
					( "Relationship object index is missing",
					  kERROR_INVALID_STATE,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Object' => $object_id ) );						// !@! ==>
		
		//
		// Increment counters.
		//
		if( ($theModifiers & kFLAG_PERSIST_REPLACE) == kFLAG_PERSIST_REPLACE )
		{
			//
			// Create first outgoing.
			//
			if( ! array_key_exists( kTAG_OUT, $subject ) )
				$subject[ kTAG_OUT ] = array( $predicate => 1 );
			
			//
			// Update existing outgoing.
			//
			else
			{
				//
				// Reference counters.
				//
				$counters = & $subject[ kTAG_OUT ];
				
				//
				// Increment.
				//
				if( array_key_exists( $predicate, $counters ) )
					$counters[ $predicate ]++;
				
				//
				// Create.
				//
				else
					$counters[ $predicate ] = 1;
			
			} // Subject has outgoing relationships.
			
			//
			// Commit subject.
			//
			$ok = $container->save( $subject, $options );
			if( ! $ok[ 'ok' ] )
				throw new CException
						( "Unable to save subject node index",
						  kERROR_INVALID_STATE,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Status' => $ok ) );							// !@! ==>
				
			//
			// Create first incoming.
			//
			if( ! array_key_exists( kTAG_IN, $object ) )
				$object[ kTAG_IN ] = array( $predicate => 1 );
			
			//
			// Update existing incoming.
			//
			else
			{
				//
				// Reference counters.
				//
				$counters = & $object[ kTAG_IN ];
				
				//
				// Increment.
				//
				if( array_key_exists( $predicate, $counters ) )
					$counters[ $predicate ]++;
				
				//
				// Create.
				//
				else
					$counters[ $predicate ] = 1;
			
			} // Object has incoming relationships.
			
			//
			// Commit object.
			//
			$ok = $container->save( $object, $options );
			if( ! $ok[ 'ok' ] )
				throw new CException
						( "Unable to save object node index",
						  kERROR_INVALID_STATE,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Status' => $ok ) );							// !@! ==>
		
		} // Created relationship.
		
		//
		// Decrement counters.
		//
		elseif( $theModifiers & kFLAG_PERSIST_DELETE )
		{
			//
			// Handle existing outgoing.
			//
			if( array_key_exists( kTAG_OUT, $subject ) )
			{
				//
				// Reference counters.
				//
				$counters = & $subject[ kTAG_OUT ];
				
				//
				// Decrement existing predicate.
				//
				if( array_key_exists( $predicate, $counters ) )
				{
					//
					// Decrement.
					//
					$counters[ $predicate ]--;
					if( ! $counters[ $predicate ] )
					{
						//
						// Delete predicate.
						//
						unset( $counters[ $predicate ] );
						
						//
						// Delete all counters.
						//
						if( ! count( $counters ) )
							unset( $subject[ kTAG_OUT ] );
					
					} // Deleted last predicate count.
					
					//
					// Commit subject.
					//
					$ok = $container->save( $subject, $options );
					if( ! $ok[ 'ok' ] )
						throw new CException
								( "Unable to save subject node index",
								  kERROR_INVALID_STATE,
								  kMESSAGE_TYPE_ERROR,
								  array( 'Status' => $ok ) );					// !@! ==>
				
				} // Has predicate.
			
			} // Has outgoing.
		
			//
			// Handle existing incoming.
			//
			if( array_key_exists( kTAG_IN, $object ) )
			{
				//
				// Reference counters.
				//
				$counters = & $object[ kTAG_IN ];
				
				//
				// Decrement existing predicate.
				//
				if( array_key_exists( $predicate, $counters ) )
				{
					//
					// Decrement.
					//
					$counters[ $predicate ]--;
					if( ! $counters[ $predicate ] )
					{
						//
						// Delete predicate.
						//
						unset( $counters[ $predicate ] );
						
						//
						// Delete all counters.
						//
						if( ! count( $counters ) )
							unset( $object[ kTAG_IN ] );
					
					} // Deleted last predicate count.
					
					//
					// Commit object.
					//
					$ok = $container->save( $object, $options );
					if( ! $ok[ 'ok' ] )
						throw new CException
								( "Unable to save object node index",
								  kERROR_INVALID_STATE,
								  kMESSAGE_TYPE_ERROR,
								  array( 'Status' => $ok ) );					// !@! ==>
				
				} // Has predicate.
			
			} // Has incoming.
		
		} // Deleted relationship.
	
	} // _UpdateRelationshipCounts.

	 

} // class COntologyEdgeIndex.


?>
