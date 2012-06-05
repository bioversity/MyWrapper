<?php

/**
 * <i>COntologyNodeIndex</i> class definition.
 *
 * This file contains the class definition of <b>COntologyNodeIndex</b> which represents a
 * Neo4j node index.
 *
 *	@package	MyWrapper
 *	@subpackage	Persistence
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 30/05/2012
 */

/*=======================================================================================
 *																						*
 *								COntologyNodeIndex.php									*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This includes the ancestor class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CArrayObject.php" );

/**
 * Graph defines.
 *
 * This include file contains graph node definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CGraphNode.inc.php" );

/**
 * Node index.
 *
 * This class implements a graph node index, it can be used to replicate a Neo4j node to
 * a MongoDB collection.
 *
 * Neo4j uses Lucene as its standard indexing tool, in this library we use MongoDB as the
 * standard database, so it is a logical choice to use Mongo as the indexing mechanism for
 * Neo4j. Also, by storing nodes in Mongo, by dumping the database contents we also have the
 * graph structure with it.
 *
 * The class features a single data member, the {@link Node() node} which contains a Neo4j
 * node reference. When {@link __construct() instantiating} this class you are required to
 * provide a persistent instance of a node.
 *
 * The contents of the object will be the node properties as will be stored in the Mongo
 * {@link kDEFAULT_CNT_NODES default} collection for nodes:
 *
 * <ul>
 *	<li><i>{@link kTAG_LID kTAG_LID}</i>: This offset will hold the node ID.
 *	<li><i>{@link kTAG_DATA kTAG_DATA}</i>: This offset will hold the node properties.
 * </ul>
 *
 * The class provides a single member accessor method: {@link Node() Node}, which can be
 * used to set the referenced node.
 *
 * <i>Note that the class will not cast to an array correctly, you must use the
 * {@link getArrayCopy() getArrayCopy} method to get an array, if you know how to solve
 * this, please do it!</i>
 *
 *	@package	MyWrapper
 *	@subpackage	Persistence
 */
class COntologyNodeIndex extends CArrayObject
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
	 *	__construct																		*
	 *==================================================================================*/

	/**
	 * Instantiate class.
	 *
	 * The constructor can be used to instantiate an object with a node, there is no
	 * provision for instantiating an empty object.
	 *
	 * The method expects the following parameters:
	 *
	 * <ul>
	 *	<li><b>$theNode</b>: The referenced node, this data can come in three flavours:
	 *	 <ul>
	 *		<li><i>Neo4j node</i>: This is the expected format, the node must be persistent,
	 *			that is, it must have an ID. In this case the second parameter is ignored.
	 *		<li><i>integer</i> or <i>numeric string</i>: The value refers to the node ID, in
	 *			this case the node will have to be loaded from a Neo4j client, which must be
	 *			provided in the next parameter.
	 *		<li><i>{@link CMongoQuery query}</i>: In this case we assume the value
	 *			represents a {@link CMongoQuery query} that will be sent to the index, only
	 *			the first matching record will be used.
	 *	 </ul>
	 *	<li><b>$theContainer</b>: The nodes container, depending on the first parameter:
	 *	 <ul>
	 *		<li><i>Neo4j node</i>: The parameter will be ignored.
	 *		<li><i>integer</i> or <i>numeric string</i>: The parameter is expected to be an
	 *			instance of a Neo4j client, it will be used to locate the node in the graph.
	 *		<li><i>array</i>: The parameter is expected to have two elements:
	 *		 <ul>
	 *			<li><i>{@link kTAG_NODE kTAG_NODE}</i>: The Neo4j client (graph container).
	 *			<li><i>{@link kTAG_TERM kTAG_TERM}</i>: The Mongo container (index container),
	 *				which can take the following types:
	 *			 <ul>
	 *				<li><i>MongoDB</i>: The database will be used to instantiate a
	 *					MongoCollection named as the
	 *					{@link kDEFAULT_CNT_NODES kDEFAULT_CNT_NODES} tag.
	 *				<li><i>MongoCollection</i>: The provided collection will be used.
	 *				<li><i>{@link CMongoContainer CMongoContainer}</i>: The referenced Mongo
	 *					{@link CContainer::Container() collection} will be used (be careful not
	 *					to send the terms collection).
	 *			 </ul>
	 *		 </ul>
	 *	 </ul>
	 * </ul>
	 *
	 * If any of the above conditions is not met, or if the node was not found, an exception
	 * will be raised.
	 *
	 * @param mixed					$theNode			Graph node.
	 * @param mixed					$theContainer		Graph container.
	 *
	 * @access public
	 *
	 * @throws {@link CException CException}
	 *
	 * @uses Node()
	 * @uses _LocateNode()
	 */
	public function __construct( $theNode, $theContainer = NULL )
	{
		//
		// Handle node.
		//
		if( $theContainer === NULL )
			$this->Node( $theNode );
		
		//
		// Locate node.
		//
		else
		{
			//
			// Locate node.
			//
			$node = $this->_QueryNode( $theNode, $theContainer );
			if( $node !== NULL )
				$this->Node( $node );
			
			else
				throw new CException
						( "Node not found",
						  kERROR_NOT_FOUND,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Node' => $theNode,
						  		 'Container' => $theContainer ) );				// !@! ==>
		
		} // Provided container.
	
	} // Constructor.

		

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
	 * @param mixed					$theValue			Node or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @throws {@link CException CException}
	 *
	 * @uses _LoadNodeProperties()
	 */
	public function Node( $theValue = NULL, $getOld = FALSE )
	{
		//
		// Save old value.
		//
		$save = $this->mNode;
		
		//
		// Return current value.
		//
		if( $theValue === NULL )
			return $save;															// ==>

		//
		// Check node type.
		//
		if( $theValue instanceof Everyman\Neo4j\PropertyContainer )
		{
			//
			// Check if persistent.
			//
			if( $theValue->hasId() )
			{
				//
				// Set data member.
				//
				$this->mNode = $theValue;
				
				//
				// Set object properties.
				//
				$this->_LoadNodeProperties();
				
				if( $getOld )
					return $save;													// ==>
				
				return $theValue;													// ==>
			
			} // Has identifier.
			
			else
				throw new CException
						( "Node is not persistent",
						  kERROR_INVALID_STATE,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Node' => $theValue ) );						// !@! ==>
		
		} // Supported node type.
		
		else
			throw new CException
					( "Unsupported node type",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Node' => $theValue ) );							// !@! ==>

	} // Node.

		

/*=======================================================================================
 *																						*
 *								PUBLIC PERSISTENCE INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Commit																			*
	 *==================================================================================*/

	/**
	 * Commit the object into a container.
	 *
	 * This method should be used to commit the object to a container, the method accepts
	 * two parameters:
	 *
	 * <ul>
	 *	<li><b>$theContainer</b>: This parameter represents the index container, the value
	 *		will be fed to the {@link _ResolveIndexContainer() _ResolveIndexContainer}
	 *		protected method which should return a {@link CMongoContainer CMongoContainer}.
	 *	<li><b>$theModifiers</b>: This parameter represents the commit operation options,
	 *		only the following options are considered:
	 *	 <ul>
	 *		<li><i>{@link kFLAG_PERSIST_REPLACE kFLAG_PERSIST_REPLACE}</i>: The provided
	 *			object will be {@link kFLAG_PERSIST_INSERT inserted}, if the identifier
	 *			doesn't match any container elements, or it will
	 *			{@link kFLAG_PERSIST_UPDATE replace} the existing object. As with
	 *			{@link kFLAG_PERSIST_UPDATE update}, it is assumed that the provided
	 *			object's attributes will replace all the existing object's ones.
	 *		<li><i>{@link kFLAG_PERSIST_DELETE kFLAG_PERSIST_DELETE}</i>: This option
	 *			assumes you want to remove the object from the container, although the
	 *			container features a specific {@link CContainer::Delete() method} for this
	 *			purpose, this option may be used to implement a <i>deleted state</i>, rather
	 *			than actually removing the object from the container.
	 *	 </ul>
	 * </ul>
	 *
	 * @param mixed					$theContainer		Persistent container.
	 * @param bitfield				$theModifiers		Commit modifiers.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @throws {@link CException CException}
	 *
	 * @uses _ResolveIndexContainer()
	 * @uses _LoadNodeProperties()
	 */
	public function Commit( $theContainer, $theModifiers = kFLAG_PERSIST_REPLACE )
	{
		//
		// Resolve container.
		//
		$theContainer = $this->_ResolveIndexContainer( $theContainer );
		$container = $theContainer->Container();
		
		//
		// Set options.
		//
		$options = array( 'safe' => TRUE );
		
		//
		// Save node.
		//
		if( ($theModifiers & kFLAG_PERSIST_REPLACE) == kFLAG_PERSIST_REPLACE )
		{
			//
			// Set object properties.
			//
			$this->_LoadNodeProperties();
			
			//
			// Replace.
			//
			$status = $container->save( $this->getArrayCopy(), $options );
		
		} // Save.

		//
		// Handle delete.
		//
		elseif( $theModifiers & kFLAG_PERSIST_DELETE )
		{
			//
			// Set criteria.
			//
			$criteria = array( kTAG_LID => $this->offsetGet( kTAG_LID ) );
			
			//
			// Update options.
			//
			$options[ 'justOne' ] = TRUE;
			
			//
			// Delete.
			//
			$status = $container->remove( $criteria, $options );
		
		} // Delete.
		
		else
			throw new CException
					( "Unsupported operation option",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Options' => $theModifiers ) );					// !@! ==>
		
		//
		// Check status.
		//
		if( ! $status[ 'ok' ] )
			throw new CException
					( "Unable to save object",
					  kERROR_INVALID_STATE,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Status' => $status ) );							// !@! ==>
		
		//
		// Handle relationship.
		// OK, this can give you identity problems...
		//
		if( $this instanceof COntologyEdgeIndex )
			$this->_UpdateRelationshipCounts( $theContainer, $theModifiers );
		
		return $status;																// ==>
		
	} // Commit.

		

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
	 * This method can be used to resolve the provided parameter to the index container, the
	 * method will return a {@link CMongoContainer CMongoContainer} which points to the
	 * index collection; the provided parameter can take the following types:
	 *
	 * <ul>
	 *	<li><i>MongoDB</i>: The method will return a {@link CMongoContainer container} using
	 *		a MongoCollection named after the value of the
	 *		{@link kDEFAULT_CNT_NODES kDEFAULT_CNT_NODES} tag.
	 *	<li><i>MongoCollection</i>: The method will return a
	 *		{@link CMongoContainer container} instantiated with the collection.
	 *	<li><i>{@link CMongoContainer CMongoContainer}</i>: The method will use that value.
	 * </ul>
	 *
	 * If the provided parameter is of any other type, the method will raise an exception.
	 *
	 * @param mixed					$theContainer		Index container.
	 *
	 * @access protected
	 * @return CMongoContainer
	 *
	 * @throws {@link CException CException}
	 */
	protected function _ResolveIndexContainer( $theContainer )
	{
		//
		// Resolve CMongoContainer.
		//
		if( $theContainer instanceof CMongoContainer )
			return $theContainer;													// ==>
		
		//
		// Handle MongoCollection.
		//
		if( $theContainer instanceof MongoCollection )
			return new CMongoContainer( $theContainer );							// ==>
		
		//
		// Resolve MongoDB.
		//
		if( $theContainer instanceof MongoDB )
			return new CMongoContainer
				( $theContainer->selectCollection( kDEFAULT_CNT_NODES ) );			// ==>
		
		throw new CException
				( "Unsupported index container type",
				  kERROR_UNSUPPORTED,
				  kMESSAGE_TYPE_ERROR,
				  array( 'Container' => $theContainer ) );						// !@! ==>
	
	} // _ResolveIndexContainer.

	 
	/*===================================================================================
	 *	_QueryNode																		*
	 *==================================================================================*/

	/**
	 * Query node.
	 *
	 * This method can be used to locate a node in the provided container, it accepts two
	 * parameters:
	 *
	 * <ul>
	 *	<li><b>$theNode</b>: The node reference:
	 *	 <ul>
	 *		<li><i>integer</i> or <i>numeric string</i>: The value refers to the node ID, in
	 *			this case the node will have to be loaded from a Neo4j client, which must be
	 *			provided in the next parameter.
	 *		<li><i>{@link CMongoQuery query}</i>: In this case we assume the value
	 *			represents a {@link CMongoQuery query} that will be sent to the index, only
	 *			the first matching record will be used.
	 *	 </ul>
	 *	<li><b>$theContainer</b>: The nodes container:
	 *	 <ul>
	 *		<li><i>Neo4j client</i>: The first parameter is interpreted as the node ID.
	 *		<li><i>array</i>: The parameter is expected to have two elements:
	 *		 <ul>
	 *			<li><i>{@link kTAG_NODE kTAG_NODE}</i>: The Neo4j client (graph container).
	 *			<li><i>{@link kTAG_TERM kTAG_TERM}</i>: The Mongo container (index container),
	 *				which can take the following types:
	 *			 <ul>
	 *				<li><i>MongoDB</i>: The database will be used to instantiate a
	 *					MongoCollection named as the
	 *					{@link kDEFAULT_CNT_NODES kDEFAULT_CNT_NODES} tag.
	 *				<li><i>MongoCollection</i>: The provided collection will be used.
	 *				<li><i>{@link CMongoContainer CMongoContainer}</i>: The referenced Mongo
	 *					{@link CContainer::Container() collection} will be used (be careful not
	 *					to send the terms collection).
	 *			 </ul>
	 *		 </ul>
	 *	 </ul>
	 * </ul>
	 *
	 * The method will return the located node or <i>NULL</i> if not found.
	 *
	 * If the located node has an ID of zero, meaning that it is the default Neo4j node, the
	 * method will raise an exception, because most likely the node parameter was a string
	 * that resolved to zero.
	 *
	 * If any of the provided parameters is incorrect, the method will raise an exception.
	 *
	 * @param mixed					$theNode			Node reference.
	 * @param mixed					$theContainer		Node container.
	 *
	 * @access protected
	 * @return Everyman\Neo4j\Node|NULL
	 *
	 * @throws {@link CException CException}
	 */
	protected function _QueryNode( $theNode, $theContainer )
	{
		//
		// Handle graph container.
		//
		if( $theContainer instanceof Everyman\Neo4j\Client )
			return $this->_locateNode( $theNode, $theContainer );					// ==>
		
		//
		// Handle index query.
		//
		if( is_array( $theContainer ) )
		{
			//
			// Check index container.
			//
			if( array_key_exists( kTAG_TERM, $theContainer ) )
			{
				//
				// Resolve containers.
				//
				$container = $this->_ResolveIndexContainer( $theContainer[ kTAG_TERM ] );
				
				//
				// Check query.
				//
				if( $theNode instanceof CMongoQuery )
				{
					//
					// Convert to Mongo query.
					//
					$theNode = $theNode->Export( $container );
					
					//
					// Set fields.
					//
					$fields = array( kTAG_LID => TRUE );
					
					//
					// Find.
					//
					$record = $container->Container()->findOne( $theNode, $fields );
					if( $record )
						return $this->_LocateNode( $record[ kTAG_LID ],
												   $theContainer[ kTAG_NODE ] );	// ==>
					
					return NULL;													// ==>
				
				} // Provided query.
				
				throw new CException
						( "Unsupported index query type",
						  kERROR_UNSUPPORTED,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Query' => $theNode ) );						// !@! ==>
			
			} // Has index container.
			
			throw new CException
					( "Missing index container",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Container' => $theContainer ) );					// !@! ==>
		
		} // Composite container.
		
		throw new CException
				( "Unsupported container type",
				  kERROR_UNSUPPORTED,
				  kMESSAGE_TYPE_ERROR,
				  array( 'Container' => $theContainer ) );						// !@! ==>
	
	} // _QueryNode.

	 
	/*===================================================================================
	 *	_LocateNode																		*
	 *==================================================================================*/

	/**
	 * Locate node in graph.
	 *
	 * This method can be used to locate a node in the provided graph container, it accepts
	 * two parameters:
	 *
	 * <ul>
	 *	<li><b>$theNode</b>: The node reference, it can be either an integer or a numeric
	 *		string referring to the seeked node ID.
	 *	<li><b>$theContainer</b>: The graph container, it must be a Neo4j client.
	 * </ul>
	 *
	 * The method will return the located node or <i>NULL</i> if not found.
	 *
	 * If the located node has an ID of zero, meaning that it is the default Neo4j node, the
	 * method will raise an exception, because most likely the node parameter was a string
	 * that resolved to zero.
	 *
	 * If any of the provided parameters is incorrect, the method will raise an exception.
	 *
	 * @param mixed					$theNode			Node reference.
	 * @param mixed					$theContainer		Node container.
	 *
	 * @access protected
	 * @return Everyman\Neo4j\Node|NULL
	 *
	 * @throws {@link CException CException}
	 */
	protected function _LocateNode( $theNode, $theContainer )
	{
		//
		// Handle graph container.
		//
		if( $theContainer instanceof Everyman\Neo4j\Client )
		{
			//
			// Get node.
			//
			$node = $theContainer->getNode( $theNode );
			
			//
			// Found node.
			//
			if( $node !== NULL )
			{
				//
				// Check node identifier.
				//
				if( ! $node->getId() )
					throw new CException
							( "Zero node ID: node identifier possibly wrong",
							  kERROR_INVALID_PARAMETER,
							  kMESSAGE_TYPE_WARNING,
							  array( 'Node' => $theNode ) );					// !@! ==>
			
			} // Found node.
			
			return $node;															// ==>
		
		} // Search in graph.
		
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
	 * This method will set the current object's {@link kTAG_LID kTAG_LID} offset to the
	 * current {@link Node() node}'s ID and the {@link kTAG_DATA kTAG_DATA} offset to the
	 * {@link Node() node}'s properties.
	 *
	 * @access protected
	 *
	 * @uses Node()
	 */
	protected function _LoadNodeProperties()
	{
		//
		// Set object identifier.
		//
		$this->offsetSet( kTAG_LID, $this->Node()->getId() );
		
		//
		// Set object properties.
		//
		$this->offsetSet( kTAG_DATA, $this->Node()->getProperties() );
	
	} // _LoadNodeProperties.

	 

} // class COntologyNodeIndex.


?>
