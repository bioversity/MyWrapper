<?php

/**
 * <i>CGraphNode</i> class definition.
 *
 * This file contains the class definition of <b>CGraphNode</b> which represents the
 * ancestor of all graph nodes in this library.
 *
 * Objects derived from this class have one main property which is the node
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
	 * We {@link CPersistentObject::__construct() overload} the constructor to initialise
	 * the {@link _IsInited() inited} {@link kFLAG_STATE_INITED flag} if the
	 * {@link Node() node} property is set.
	 *
	 * We also enforce the container, empty nodes must be instantiated by a Neo4j container.
	 *
	 * @param mixed					$theContainer		Persistent container.
	 * @param mixed					$theIdentifier		Object identifier.
	 * @param bitfield				$theModifiers		Create modifiers.
	 *
	 * @access public
	 *
	 * @uses _IsInited
	 *
	 * @see kTAG_NODE
	 */
	public function __construct( $theContainer = NULL,
								 $theIdentifier = NULL,
								 $theModifiers = kFLAG_DEFAULT )
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
		parent::__construct( $theContainer, $theIdentifier, $theModifiers );
		
		//
		// Set inited status.
		//
		$this->_IsInited( $this->offsetExists( kTAG_NODE ) );
		
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
	 * accessor {@link _ManageOffset() method} to manage the {@link kTAG_NODE offset}:
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
		
		return $this->_ManageOffset( kTAG_NODE, $theValue, $getOld );				// ==>

	} // Node.

	 
	/*===================================================================================
	 *	Property																		*
	 *==================================================================================*/

	/**
	 * Manage native node properties.
	 *
	 * This method can be used to manage the native node properties, it accepts the
	 * following parameters:
	 *
	 * <ul>
	 *	<li><b>$theKey</b>: The property key:
	 *	 <ul>
	 *		<li><i>NULL</i>: Operate on all properties.
	 *		<li><i>other</i>: Property key.
	 *	 </ul>
	 *	<li><b>$theValue</b>: The property value or operation:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the property pointed by the key.
	 *		<li><i>FALSE</i>: Delete the property pointed by the key.
	 *		<li><i>other</i>: Set the property pointed by the key.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value <i>before</i> it was eventually modified.
	 *		<li><i>FALSE</i>: Return the value <i>after</i> it was eventually modified.
	 *	 </ul>
	 * </ul>
	 *
	 * @param string				$theKey				Property key.
	 * @param mixed					$theValue			Property value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 */
	public function Property( $theKey = NULL, $theValue = NULL, $getOld = FALSE )
	{
		//
		// Save node.
		//
		$node = $this->Node();
		
		//
		// Save old values.
		//
		if( $node === NULL )
			$save = NULL;
		elseif( $theKey === NULL )
			$save = $node->getProperties();
		else
			$save = $node->getProperty( $theKey );
		
		//
		// Return property.
		//
		if( $theValue === NULL )
			return $save;															// ==>
		
		//
		// Delete property.
		//
		if( $theValue === FALSE )
		{
			//
			// Handle properties.
			//
			if( $save !== NULL )
			{
				//
				// Something to delete.
				//
				$this->_IsDirty( TRUE );
				
				//
				// Handle all properties.
				//
				if( $theKey === NULL )
				{
					//
					// Iterate properties.
					//
					foreach( $save as $key => $value )
						$node->removeProperty( $key );
				
				} // All properties.
				
				//
				// Handle single property.
				//
				else
					$node->removeProperty( $theKey );
			
			} // Has properties.
			
			if( $getOld )
				return $save;														// ==>
			
			return NULL;															// ==>
		
		} // Delete property.
		
		//
		// Something to add.
		//
		$this->_IsDirty( TRUE );
		
		//
		// Set properties.
		//
		if( $theKey === NULL )
		{
			//
			// Check values.
			//
			if( (! is_array( $theValue ))
			 && (! $theValue instanceof ArrayObject) )
				throw new CException
						( "Inconsistent parameters",
						  kERROR_INVALID_PARAMETER,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Key' => $theKey,
						  		 'Value' => $theValue ) );						// !@! ==>
		
			//
			// Set properties.
			//
			$node->setProperties( $theValue );
		
		} // Add many properties.
		
		//
		// Set property.
		//
		else
			$node->setProperty( $theKey, $theValue );
		
		if( $getOld )
			return $save;															// ==>
		
		return $theValue;															// ==>

	} // Property.

		

/*=======================================================================================
 *																						*
 *								PUBLIC ARRAY ACCESS INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	offsetSet																		*
	 *==================================================================================*/

	/**
	 * Set a value for a given offset.
	 *
	 * We overload this method to manage the {@link _IsInited() inited}
	 * {@link kFLAG_STATE_INITED status}: this is set if {@link kTAG_NODE code} property is
	 * set.
	 *
	 * @param string				$theOffset			Offset.
	 * @param string|NULL			$theValue			Value to set at offset.
	 *
	 * @access public
	 */
	public function offsetSet( $theOffset, $theValue )
	{
		//
		// Call parent method.
		//
		parent::offsetSet( $theOffset, $theValue );
		
		//
		// Set inited flag.
		//
		if( $theValue !== NULL )
			$this->_IsInited( $this->offsetExists( kTAG_NODE ) );
	
	} // offsetSet.

	 
	/*===================================================================================
	 *	offsetUnset																		*
	 *==================================================================================*/

	/**
	 * Reset a value for a given offset.
	 *
	 * We overload this method to manage the {@link _IsInited() inited}
	 * {@link kFLAG_STATE_INITED status}: this is set if {@link kTAG_NODE code} property is
	 * set.
	 *
	 * @param string				$theOffset			Offset.
	 *
	 * @access public
	 */
	public function offsetUnset( $theOffset )
	{
		//
		// Call parent method.
		//
		parent::offsetUnset( $theOffset );
		
		//
		// Set inited flag.
		//
		$this->_IsInited( $this->offsetExists( kTAG_NODE ) );
	
	} // offsetUnset.

		

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
			$this->Node( $theContainer->makeNode() );
	
	} // _FinishCreate.

	 

} // class CGraphNode.


?>
