<?php

/**
 * <i>COntologyNode</i> class definition.
 *
 * This file contains the class definition of <b>COntologyNode</b> which represents the
 * ancestor of all graph nodes in this library.
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
 * Graph node.
 *
 * This class implements an ontology node.
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
 *		it must be a {@link COntologyTermObject COntologyTermObject} instance.
 * </ul>
 *
 * <i>Note that the class will not cast to an array correctly, you must use the
 * {@link getArrayCopy() getArrayCopy} method for that.</i>
 *
 *	@package	MyWrapper
 *	@subpackage	Ontology
 */
class COntologyNode extends CGraphNode
{
	/**
	 * Node.
	 *
	 * This data member holds the Neo4j node.
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
	 * Manage native term.
	 *
	 * This method can be used to manage the native term reference, it uses the standard
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
	 * @param mixed					$theValue			Term or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return Everyman\Neo4j\Node
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
							  ($this->Term() !== NULL) );
		}
		
		return $save;																// ==>

	} // Term.

		

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
		if( ($term = $this->Term()) !== NULL )
			return $term->offsetExists( $theOffset );								// ==>
		
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
		if( ($term = $this->Term()) !== NULL )
			return $term->offsetGet( $theOffset );									// ==>
		
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
		//
		// Get node property count.
		//
		$count = (integer) parent::count();
		
		//
		// Require term.
		//
		if( ($term = $this->Term()) !== NULL )
			$count += $term->count();
		
		return $count;																// ==>
	
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
		// Init local storage.
		//
		$array = Array();
		
		//
		// Require term.
		//
		if( ($term = $this->Term()) !== NULL )
			$array = array_merge( $array, $term->getArrayCopy() );
		
		//
		// Add node properties.
		//
		return array_merge( $array, parent::getArrayCopy() );						// ==>
	
	} // getArrayCopy.

	 
	/*===================================================================================
	 *	getIterator																		*
	 *==================================================================================*/

	/**
	 * Get array iterator.
	 *
	 * We overload this method to wrap the internal array over the node properties.
	 *
	 * Note that if the node exists the method will return an array, if not, it will
	 * return an empty array.
	 *
	 * @access public
	 * @return ArrayIterator
	 */
	public function getIterator()
	{
		//
		// Get array.
		//
		$array = $this->getArrayCopy();
		
		return new ArrayIterator( $array );											// ==>
	
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
	 * We overload this method to wrap the internal array over the node properties.
	 *
	 * @access public
	 * @return array
	 */
	public function keys()
	{
		//
		// Get array.
		//
		$array = $this->getArrayCopy();
		
		return array_keys( $array );												// ==>
	
	} // keys.

	 
	/*===================================================================================
	 *	values																			*
	 *==================================================================================*/

	/**
	 * Return object's values.
	 *
	 * We overload this method to wrap the internal array over the node properties.
	 *
	 * @access public
	 * @return array
	 */
	public function values()
	{
		//
		// Get array.
		//
		$array = $this->getArrayCopy();
		
		return array_values( $array );												// ==>
	
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
	 * be passed to the parent method and the {@link kTAG_TERM term} container
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
		// Check container.
		//
		if( $theContainer === NULL )
			throw new CException
					( "Missing object container",
					  kERROR_OPTION_MISSING,
					  kMESSAGE_TYPE_ERROR );									// !@! ==>
			
		//
		// Check container type.
		//
		if( ! $theContainer instanceof Everyman\Neo4j\Client )
			throw new CException
					( "Unsupported container type",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Container' => $theContainer ) );					// !@! ==>
			
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
		{
			//
			// Create node.
			//
			$this->Node( $theContainer->makeNode() );
			
			//
			// Set inited flag.
			//
			$this->_IsInited( $this->_IsInited() && TRUE );
		}
	
	} // _FinishCreate.

	 

} // class COntologyNode.


?>
