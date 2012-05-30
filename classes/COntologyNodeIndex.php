<?php

/**
 * <i>COntologyNodeIndex</i> class definition.
 *
 * This file contains the class definition of <b>COntologyNodeIndex</b> which represents a
 * Neo4jNode index.
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
	 *	<li><b>$theNode</b>: The referenced node, this data can come in two flavours:
	 *	 <ul>
	 *		<li><i>Neo4j node</i>: This is the expected format, the node must be persistent,
	 *			that is, it must have an ID. In this case the second parameter is ignored.
	 *		<li><i>integer</i>: The number refers to the node ID, in this case the node will
	 *			have to be loaded from a Neo4j client, which must be provided in the next
	 *			parameter.
	 *	 </ul>
	 *	<li><b>$theContainer</b>: The nodes container, it must be an instance of a Neo4j
	 *		client; this parameter is only considered if the first parameter is an integer.
	 *		If the node is not found, the method will raise an exception.
	 * </ul>
	 *
	 * If any of the above conditions is not met, an exception will be raised.
	 *
	 * @param mixed					$theNode			Graph node.
	 * @param mixed					$theContainer		Graph container.
	 *
	 * @access public
	 *
	 * @throws {@link CException CException}
	 */
	public function __construct( $theNode, $theContainer = NULL )
	{
		//
		// Get node.
		//
		if( $theContainer === NULL )
			$this->Node( $theNode );
		
		//
		// Get from graph.
		//
		elseif( $theContainer instanceof Everyman\Neo4j\Client )
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
				// Set node.
				//
				if( $node->getId() )
					$this->Node( $node );
				
				else
					throw new CException
							( "Invalid node identifier",
							  kERROR_INVALID_PARAMETER,
							  kMESSAGE_TYPE_ERROR,
							  array( 'Node' => $theNode ) );					// !@! ==>
			
			} // Found node.
			
			else
				throw new CException
						( "Node not found",
						  kERROR_NOT_FOUND,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Node' => $theNode ) );						// !@! ==>
		
		} // Supported graph.
		
		else
			throw new CException
					( "Unsupported graph type",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Graph' => $theContainer ) );						// !@! ==>
	
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
	 *	<li><b>$theContainer</b>: This parameter represents the <i>container</i> in which
	 *		the object is to be stored, by default we expect:
	 *	 <ul>
	 *		<li><i>MongoDB</i>: A collection named after the
	 *			{@link kDEFAULT_CNT_NODES kDEFAULT_CNT_NODES} tag will be used.
	 *		<li><i>MongoCollection</i>: The provided collection will be used.
	 *		<li><i>{@link CMongoContainer CMongoContainer}</i>: The referenced Mongo
	 *			{@link CContainer::Container() collection} will be used.
	 *	 </ul>
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
	 */
	public function Commit( $theContainer, $theModifiers = kFLAG_PERSIST_REPLACE )
	{
		//
		// Resolve MongoDB.
		//
		if( $theContainer instanceof MongoDB )
			$theContainer
				= $theContainer->selectCollection( kDEFAULT_CNT_NODES );
		
		//
		// Resolve container.
		//
		elseif( $theContainer instanceof CMongoContainer )
			$theContainer = $theContainer->Container();
		
		elseif( ! $theContainer instanceof MongoCollection )
			throw new CException
					( "Unsupported container type",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Container' => $theContainer ) );					// !@! ==>
		
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
			$status = $theContainer->save( $this->getArrayCopy(), $options );
		
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
			$status = $theContainer->remove( $criteria, $options );
		
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
		
		return $status;																// ==>
		
	} // Commit.

		

/*=======================================================================================
 *																						*
 *								PROTECTED PERSISTENCE UTILITIES							*
 *																						*
 *======================================================================================*/


	 
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
	 */
	protected function _LoadNodeProperties()
	{
		//
		// Set object identifier.
		//
		$this->offsetSet( kTAG_LID, $this->Node()->getId() );
		
		//
		// Load properties.
		//
		$this->Node()->load();
		
		//
		// Set object properties.
		//
		$this->offsetSet( kTAG_DATA, $this->Node()->getProperties() );
	
	} // _PrepareCreate.

	 

} // class COntologyNodeIndex.


?>
