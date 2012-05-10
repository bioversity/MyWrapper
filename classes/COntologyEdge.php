<?php

/**
 * <i>COntologyEdge</i> class definition.
 *
 * This file contains the class definition of <b>COntologyEdge</b> which couples ontology
 * edge nodes with  {@link COntologyTerm terms}.
 *
 *	@package	MyWrapper
 *	@subpackage	Ontology
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 24/04/2012
 */

/*=======================================================================================
 *																						*
 *									COntologyEdge.php									*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CGraphEdge.php" );

/**
 * Nodes.
 *
 * This include file contains the ontology node class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."COntologyNode.php" );

use Everyman\Neo4j\Transport,
	Everyman\Neo4j\Client,
	Everyman\Neo4j\Index\NodeIndex,
	Everyman\Neo4j\Index\RelationshipIndex,
	Everyman\Neo4j\Index\NodeFulltextIndex,
	Everyman\Neo4j\Node,
	Everyman\Neo4j\Batch;

/**
 * Ontology graph edge node.
 *
 * This class implements an ontology graph edge node.
 *
 * The class is derived from {@link CGraphEdge CGraphEdge} and implements the exact same
 * functionality as {@link COntologyNode COntologyNode}, it adds two elements: the
 * {@link SubjectTerm() subject} and {@link ObjectTerm() object} {@link COntologyTerm terms}
 * which are linked with the {@link Subject() subject} and {@link Object() object} nodes.
 *
 * Instances of this class represent predicate nodes and they hold the referenced nodes.
 *
 *	@package	MyWrapper
 *	@subpackage	Ontology
 */
class COntologyEdge extends CGraphEdge
{
	/**
	 * Predicate term.
	 *
	 * This data member holds the node predicate {@link COntologyTerm term}.
	 *
	 * @var COntologyTerm
	 */
	 protected $mPredicateTerm = NULL;

	/**
	 * Subject term.
	 *
	 * This data member holds the node subject {@link COntologyTerm term}.
	 *
	 * @var COntologyTerm
	 */
	 protected $mSubjectTerm = NULL;

	/**
	 * Object term.
	 *
	 * This data member holds the node object {@link COntologyTerm term}.
	 *
	 * @var COntologyTerm
	 */
	 protected $mObjectTerm = NULL;

		

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
		$save = CObject::ManageMember( $this->mPredicateTerm, $theValue, $getOld );
				
		//
		// Set status.
		//
		if( $theValue !== NULL )
		{
			//
			// Set node type.
			//
			if( $theValue !== FALSE )
				$this->Type( $theValue[ kTAG_GID ] );
			
			//
			// Set dirty flag.
			//
			$this->_IsDirty( TRUE );
			
			//
			// Set inited flag.
			//
			$this->_IsInited( $this->_IsInited() &&
							  ($this->mPredicateTerm !== NULL) &&
							  ($this->mSubjectTerm !== NULL) &&
							  ($this->mObjectTerm !== NULL) );
		}
		
		return $save;																// ==>

	} // Term.

	 
	/*===================================================================================
	 *	SubjectTerm																		*
	 *==================================================================================*/

	/**
	 * Manage subject term.
	 *
	 * This method can be used to manage the subject node term reference, it uses the
	 * standard accessor {@link CObject::ManageMember() method} to manage the property:
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
	public function SubjectTerm( $theValue = NULL, $getOld = FALSE )
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
		$save = CObject::ManageMember( $this->mSubjectTerm, $theValue, $getOld );
				
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
							 ($this->mPredicateTerm !== NULL) &&
							 ($this->mSubjectTerm !== NULL) &&
							 ($this->mObjectTerm !== NULL) );
		}
		
		return $save;																// ==>

	} // SubjectTerm.

	 
	/*===================================================================================
	 *	ObjectTerm																		*
	 *==================================================================================*/

	/**
	 * Manage object term.
	 *
	 * This method can be used to manage the object node term reference, it uses the
	 * standard accessor {@link CObject::ManageMember() method} to manage the property:
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
	public function ObjectTerm( $theValue = NULL, $getOld = FALSE )
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
		$save = CObject::ManageMember( $this->mObjectTerm, $theValue, $getOld );
				
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
							  ($this->mPredicateTerm !== NULL) &&
							  ($this->mSubjectTerm !== NULL) &&
							  ($this->mObjectTerm !== NULL) );
		}
		
		return $save;																// ==>

	} // ObjectTerm.

		

/*=======================================================================================
 *																						*
 *								PUBLIC MEMBER UTILITIES									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	SubjectNode																		*
	 *==================================================================================*/

	/**
	 * Return subject node.
	 *
	 * This method can be used to convert the {@link Subject() subject}
	 * {@link COntologyNode node} into an ontology {@link COntologyNode node}.
	 *
	 * The method accepts a single parameter which represents the term and node containers
	 * structured as follows:
	 *
	 * <ul>
	 *	<li><i>{@link kTAG_NODE kTAG_NODE}</i>: This element should hold the nodes
	 *		container, it must be a Everyman\Neo4j\Client instance.
	 *	<li><i>{@link kTAG_TERM kTAG_TERM}</i>: This element should hold the terms
	 *		container, it must be a {@link CContainer CContainer} instance.
	 * </ul>
	 *
	 * @param array					$theContainer		Object container.
	 *
	 * @access public
	 * @return COntologyNode
	 *
	 * @uses Subject()
	 * @uses SubjectTerm()
	 */
	public function SubjectNode( $theContainer )
	{
		//
		// Init new node.
		//
		$node = new COntologyNode( $theContainer );
		
		//
		// Set node.
		//
		$node->Node( $this->Subject() );
		
		//
		// Set term.
		//
		$node->Term( $this->SubjectTerm() );
		
		return $node;																// ==>

	} // SubjectNode.

	 
	/*===================================================================================
	 *	ObjectNode																		*
	 *==================================================================================*/

	/**
	 * Return object node.
	 *
	 * This method can be used to convert the {@link Object() object}
	 * {@link COntologyNode node} into an ontology {@link COntologyNode node}.
	 *
	 * The method accepts a single parameter which represents the term and node containers
	 * structured as follows:
	 *
	 * <ul>
	 *	<li><i>{@link kTAG_NODE kTAG_NODE}</i>: This element should hold the nodes
	 *		container, it must be a Everyman\Neo4j\Client instance.
	 *	<li><i>{@link kTAG_TERM kTAG_TERM}</i>: This element should hold the terms
	 *		container, it must be a {@link CContainer CContainer} instance.
	 * </ul>
	 *
	 * @param array					$theContainer		Object container.
	 *
	 * @access public
	 * @return COntologyNode
	 *
	 * @uses Object()
	 * @uses ObjectTerm()
	 */
	public function ObjectNode( $theContainer )
	{
		//
		// Init new node.
		//
		$node = new COntologyNode( $theContainer );
		
		//
		// Set node.
		//
		$node->Node( $this->Object() );
		
		//
		// Set term.
		//
		$node->Term( $this->ObjectTerm() );
		
		return $node;																// ==>

	} // ObjectNode.

		

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
		if( $this->mPredicateTerm !== NULL )
			return $this->mPredicateTerm->offsetExists( $theOffset );				// ==>
		
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
		if( $this->mPredicateTerm !== NULL )
			return $this->mPredicateTerm->offsetGet( $theOffset );					// ==>
		
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
		if( $this->mPredicateTerm !== NULL )
			return array_merge( $this->mPredicateTerm->getArrayCopy(),
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
	 * We {@link CGraphEdge::_Commit() overload} this method to provide the correct
	 * container to the {@link CGraphEdge parent} {@link CGraphEdge::_Commit() method}.
	 *
	 * We also {@link COntologyTerm::Commit() commit} the {@link SubjectTerm() subject} and
	 * {@link ObjectTerm() object} {@link COntologyTerm terms}.
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
			// Unrelate terms.
			//
			$this->mSubjectTerm->Relate( $this->mObjectTerm, $this->mPredicateTerm, FALSE );
			
			//
			// Reset predicate term.
			//
			$id = $this->Node()->getId();
			$this->mPredicateTerm->Predicate( $id, FALSE );
			if( count( $this->mPredicateTerm->Predicate() ) )
			{
				$mod = array( kTAG_EDGE => $id );
				$theContainer[ kTAG_TERM ]->Commit( $mod,
													$this->mPredicateTerm[ kTAG_LID ],
													kFLAG_PERSIST_MODIFY +
													kFLAG_MODIFY_PULL +
													kFLAG_STATE_ENCODED );
			}
			else
				$this->mPredicateTerm->Commit( $theContainer[ kTAG_TERM ] );
			$this->Term( new COntologyTerm() );
			
			//
			// Reset subject term.
			//
			$id = $this->Subject()->getId();
			$this->mSubjectTerm->Node( $id, FALSE );
			if( count( $this->mSubjectTerm->Node() ) )
			{
				$mod = array( kTAG_NODE => $id );
				$theContainer[ kTAG_TERM ]->Commit( $mod,
													$term[ kTAG_LID ],
													kFLAG_PERSIST_MODIFY +
													kFLAG_MODIFY_PULL +
													kFLAG_STATE_ENCODED );
			}
			else
				$this->mSubjectTerm->Commit( $theContainer[ kTAG_TERM ] );
			$this->SubjectTerm( new COntologyTerm() );
			
			//
			// Reset object term.
			//
			$id = $this->Object()->getId();
			$this->mObjectTerm->Node( $id, FALSE );
			if( count( $this->mObjectTerm->Node() ) )
			{
				$mod = array( kTAG_NODE => $id );
				$theContainer[ kTAG_TERM ]->Commit( $mod,
													$term[ kTAG_LID ],
													kFLAG_PERSIST_MODIFY +
													kFLAG_MODIFY_PULL +
													kFLAG_STATE_ENCODED );
			}
			else
				$this->mObjectTerm->Commit( $theContainer[ kTAG_TERM ] );
			$this->ObjectTerm( new COntologyTerm() );
		
		} // Delete.
		
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
			// Commit predicate term.
			//
			$id = $this->Node()->getId();
			$this->mPredicateTerm->Predicate( $id, TRUE );
			$mod = array( kTAG_EDGE => $id );
			$theContainer[ kTAG_TERM ]->Commit( $mod,
												$this->mPredicateTerm[ kTAG_LID ],
												kFLAG_PERSIST_MODIFY +
												kFLAG_MODIFY_ADDSET +
												kFLAG_STATE_ENCODED );
			
			//
			// Commit subject term.
			//
			$id = $this->Subject()->getId();
			$this->mSubjectTerm->Node( $id, TRUE );
			$mod = array( kTAG_NODE => $id );
			$theContainer[ kTAG_TERM ]->Commit( $mod,
												$this->mSubjectTerm[ kTAG_LID ],
												kFLAG_PERSIST_MODIFY +
												kFLAG_MODIFY_ADDSET +
												kFLAG_STATE_ENCODED );
			
			//
			// Commit object term.
			//
			$id = $this->Object()->getId();
			$this->mObjectTerm->Node( $id, TRUE );
			$mod = array( kTAG_NODE => $id );
			$theContainer[ kTAG_TERM ]->Commit( $mod,
												$this->mObjectTerm[ kTAG_LID ],
												kFLAG_PERSIST_MODIFY +
												kFLAG_MODIFY_ADDSET +
												kFLAG_STATE_ENCODED );
			
			//
			// Relate terms.
			//
			$this->mSubjectTerm->Relate( $this->mObjectTerm, $this->mPredicateTerm, TRUE );

			//
			// Add indexes.
			//
			$this->_IndexTerms( $theContainer[ kTAG_NODE ] );
			
			return $this->Node()->getId();											// ==>
		
		} // Saving.
		
		return $id;																	// ==>
	
	} // _Commit.

	 
	/*===================================================================================
	 *	_Load																			*
	 *==================================================================================*/

	/**
	 * Find object.
	 *
	 * In this class we pass the correct parameters to the {@link CGraphEdge parent}
	 * {@link CGraphEdge::_Load() method}.
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
	 *		container, it must be a {@link CContainer CContainer} instance.
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
		// Handle provided ontology edge.
		//
		if( $theIdentifier instanceof self )
			$theIdentifier = $theIdentifier->Node();
		
		//
		// Call node method.
		//
		parent::_PrepareCreate( $node_cont, $theIdentifier, $theModifiers );
		
		//
		// Note that at this point nothing has yet been loaded
		// into the object, so we need to work with the content.
		//
		
		//
		// Handle provided node.
		//
		if( $node_cont instanceof Everyman\Neo4j\Relationship )
		{
			//
			// Load predicate term.
			//
			if( ($ref = $node_cont->getType()) !== NULL )
			{
				$term = CPersistentUnitObject::NewObject(
							$theContainer[ kTAG_TERM ],
							COntologyTermObject::HashIndex( $ref ),
							kFLAG_STATE_ENCODED );
				if( $term )
					$this->Term( $term );
				else
					throw new CException
							( "Invalid predicate term reference",
							  kERROR_NOT_FOUND,
							  kMESSAGE_TYPE_ERROR,
							  array( 'Term' => $ref ) );						// !@! ==>

			} // Has type.
		
			//
			// Load subject term.
			//
			if( ($node = $node_cont->getStartNode()) !== NULL )
			{
				//
				// Load subject term.
				//
				if( ($ref = $node->getProperty( kTAG_TERM )) !== NULL )
				{
					$term = CPersistentUnitObject::NewObject(
								$theContainer[ kTAG_TERM ],
								COntologyTermObject::HashIndex( $ref ),
								kFLAG_STATE_ENCODED );
					if( $term )
						$this->SubjectTerm( $term );
					else
						throw new CException
								( "Invalid subject term reference",
								  kERROR_NOT_FOUND,
								  kMESSAGE_TYPE_ERROR,
								  array( 'Term' => $ref ) );					// !@! ==>
	
				} // Has term reference property.
			
			} // Has start node.
		
			//
			// Load object term.
			//
			if( ($node = $node_cont->getEndNode()) !== NULL )
			{
				//
				// Load object term.
				//
				if( ($ref = $node->getProperty( kTAG_TERM )) !== NULL )
				{
					$term = CPersistentUnitObject::NewObject(
								$theContainer[ kTAG_TERM ],
								COntologyTermObject::HashIndex( $ref ),
								kFLAG_STATE_ENCODED );
					if( $term )
						$this->ObjectTerm( $term );
					else
						throw new CException
								( "Invalid object term reference",
								  kERROR_NOT_FOUND,
								  kMESSAGE_TYPE_ERROR,
								  array( 'Term' => $ref ) );					// !@! ==>
	
				} // Has term reference property.
			
			} // Has end node.
			
			//
			// Set content.
			//
			$theContainer = $node_cont;
		
		} // Provided node.
	
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
		// Commit subject term.
		//
		$id = $this->mSubjectTerm->Commit( $term_cont );
	
		//
		// Set subject term reference.
		//
		$this->Node()->getStartNode()->setProperty
			( kTAG_TERM, $this->mSubjectTerm[ kTAG_GID ] );
		
		//
		// Commit object term.
		//
		$id = $this->mObjectTerm->Commit( $term_cont );
	
		//
		// Set subject term reference.
		//
		$this->Node()->getEndNode()->setProperty
			( kTAG_TERM, $this->mObjectTerm[ kTAG_GID ] );
		
		//
		// Commit predicate term.
		//
		$id = $this->mPredicateTerm->Commit( $term_cont );
	
		//
		// Set term reference.
		//
		$this->Type( $this->mPredicateTerm[ kTAG_GID ] );
		
		//
		// Set term relations index.
		//
		$this->Node()->setProperty
			( kTAG_EDGE_TERM, implode( kTOKEN_INDEX_SEPARATOR,
										array( $this->mSubjectTerm[ kTAG_GID ],
											   $this->mPredicateTerm[ kTAG_GID ],
											   $this->mObjectTerm[ kTAG_GID ] ) ) );
		
	} // _PrepareCommit.

	 
	/*===================================================================================
	 *	_FinishCreate																	*
	 *==================================================================================*/

	/**
	 * Normalise after a {@link _Create() create}.
	 *
	 * In this class we initialise the {@link Term() predicate},
	 * {@link SubjectTerm() subject} and {@link ObjectTerm() object}
	 * {@link COntologyTerm terms}.
	 *
	 * @param reference			   &$theContainer		Object container.
	 *
	 * @access protected
	 */
	protected function _FinishCreate( &$theContainer )
	{
		//
		// Create empty term.
		//
		if( ! $this->Term() instanceof COntologyTerm )
			$this->Term( new COntologyTerm() );
	
		//
		// Create empty subject term.
		//
		if( ! $this->SubjectTerm() instanceof COntologyTerm )
			$this->SubjectTerm( new COntologyTerm() );
	
		//
		// Create empty object term.
		//
		if( ! $this->ObjectTerm() instanceof COntologyTerm )
			$this->ObjectTerm( new COntologyTerm() );
	
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
	 * In this class we get the term reference from the node {@link Type() type} property
	 * and load it, along with the {@link Subject() subject} and {@link Object() object}
	 * terms.
	 *
	 * Note that if the object has a {@link Type() type} it means it was read from the
	 * container: in this case it <i>must</i> have term references for both the
	 * {@link SubjectTerm() subject} and {@link ObjectTerm() object}, or an exception should
	 * be raised.
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
		// Handle not found.
		//
		if( $this->Node() === NULL )
		{
			//
			// Init predicate term.
			//
			$this->Term( new COntologyTerm() );
	
			//
			// Init subject term.
			//
			$this->SubjectTerm( new COntologyTerm() );
	
			//
			// Init object term.
			//
			$this->ObjectTerm( new COntologyTerm() );
		
		} // Node not found.
		
		//
		// Handle found edge.
		//
		else
		{
			//
			// Handle predicate term.
			//
			if( ($ref = $this->Type()) !== NULL )
			{
				//
				// Load subject term.
				//
				$term = CPersistentUnitObject::NewObject
							( $theContainer[ kTAG_TERM ],
							  COntologyTermObject::HashIndex( $ref ),
							  kFLAG_STATE_ENCODED );
				if( $term )
					$this->Term( $term );
				else
					throw new CException
							( "Invalid subject term reference",
							  kERROR_NOT_FOUND,
							  kMESSAGE_TYPE_ERROR,
							  array( 'Term' => $ref ) );						// !@! ==>
			
			} // Has edge term reference.

			else
				throw new CException
						( "Predicate node is missing term reference",
						  kERROR_OPTION_MISSING,
						  kMESSAGE_TYPE_ERROR );								// !@! ==>
		
			//
			// Find subject node and load subject term.
			//
			if( ($ref = $this->Subject()->getProperty( kTAG_TERM )) !== NULL )
			{
				//
				// Load subject term.
				//
				$term = CPersistentUnitObject::NewObject
							( $theContainer[ kTAG_TERM ],
							  COntologyTermObject::HashIndex( $ref ),
							  kFLAG_STATE_ENCODED );
				if( $term )
					$this->SubjectTerm( $term );
				else
					throw new CException
							( "Invalid subject term reference",
							  kERROR_NOT_FOUND,
							  kMESSAGE_TYPE_ERROR,
							  array( 'Term' => $ref ) );						// !@! ==>
			
			} // Has subject term reference.

			else
				throw new CException
						( "Subject node is missing term reference",
						  kERROR_OPTION_MISSING,
						  kMESSAGE_TYPE_ERROR );								// !@! ==>
		
			//
			// Find object node and load object term.
			//
			if( ($ref = $this->Object()->getProperty( kTAG_TERM )) !== NULL )
			{
				//
				// Load subject term.
				//
				$term = CPersistentUnitObject::NewObject
							( $theContainer[ kTAG_TERM ],
							  COntologyTermObject::HashIndex( $ref ),
							  kFLAG_STATE_ENCODED );
				if( $term )
					$this->ObjectTerm( $term );
				else
					throw new CException
							( "Invalid object term reference",
							  kERROR_NOT_FOUND,
							  kMESSAGE_TYPE_ERROR,
							  array( 'Term' => $ref ) );						// !@! ==>
			
			} // Has object term reference.

			else
				throw new CException
						( "Object node is missing term reference",
						  kERROR_OPTION_MISSING,
						  kMESSAGE_TYPE_ERROR );								// !@! ==>
		
		} // Found edge.
	
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
	 * Create node indexes.
	 *
	 * This method will save node indexes after the node was {@link _Commit() committed},
	 * it will perform the following selections:
	 *
	 * <ul>
	 *	<li><i>{@link kINDEX_EDGE_TERM kINDEX_EDGE_TERM}</i>: The {@link Term() term}
	 *		{@link kTAG_GID global} identifier (RelationshipIndex).
	 *		container, it must be a Everyman\Neo4j\Client instance.
	 *	<li><i>{@link kINDEX_EDGE_NAME kINDEX_EDGE_NAME}</i>: The {@link Term() term}
	 *		{@link CTerm::Name() names} in all languages (RelationshipIndex).
	 *	<li><i>{@link kINDEX_EDGE_TERMS kINDEX_EDGE_TERMS}</i>: The node relations, this
	 *		index records the relation terms, that is, the combination of the subject
	 *		{@link SubjectTerm() term}, predicate {@link Term() term} and the object
	 *		{@link ObjectTerm() term}, this can be used to retrieve existing relations.
	 * </ul>
	 *
	 * The following index tags are set:
	 *
	 * <ul>
	 *	<li><i>{@link kTAG_GID kTAG_GID}</i>: The {@link Term() term}
	 *		{@link kTAG_GID global} identifier.
	 *	<li><i>{@link kTAG_NAME kTAG_NAME}</i>: The {@link Term() term}
	 *		{@link kTAG_NAME names}. 
	 *	<li><i>{@link kTAG_EDGE_TERM kTAG_EDGE_TERM}</i>: The relationships between terms
	 *		taken from the {@link Node() node}'s {@link kTAG_EDGE_TERM kTAG_EDGE_TERM}
	 *		property, formatted as a SUBJECT/PREDICATE/OBJECT string, in which each element
	 *		is the {@link kTAG_GID global} {@link COntologyTerm term} identifier.
	 *	<li><i>{@link kTAG_NODE kTAG_NODE}</i>: The relationships between
	 *		{@link Node() nodes}, expressed as a SUBJECT/PREDICATE/OBJECT string, in which
	 *		the subject and object are the {@link Node() node} identifiers and the predicate
	 *		is the {@link kTAG_GID global} {@link Term() term}  identifier.
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
		// Instantiate terms index.
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
	
		//
		// Add term relations key.
		//
		$idx->add( $node, kTAG_EDGE_TERM, $this->Node()->getProperty( kTAG_EDGE_TERM ) );
	
		//
		// Add node relations key.
		//
		$idx->add
			( $node, kTAG_EDGE_NODE, implode( kTOKEN_INDEX_SEPARATOR,
											   array( $this->Subject()->getId(),
													  $this->mPredicateTerm[ kTAG_GID ],
													  $this->Object()->getId() ) ) );
	
	} // _IndexTerms.

	 
	/*===================================================================================
	 *	_GetNodeIndex																	*
	 *==================================================================================*/

	/**
	 * Retrieve the edge index.
	 *
	 * This method can be used to return an edge index identified by the provided index tag.
	 *
	 * @param Everyman\Neo4j\Client	$theContainer		Node container.
	 * @param string				$theIndex			Index tag.
	 * @param boolean				$doClear			TRUE means clear index.
	 *
	 * @access protected
	 * @return Everyman\Neo4j\RelationshipIndex 
	 */
	protected function _GetNodeIndex( Everyman\Neo4j\Client $theContainer,
															$theIndex,
															$doClear = FALSE )
	{
		//
		// Instantiate edge index.
		//
		$idx = new RelationshipIndex( $theContainer, $theIndex );
		$idx->save();
		
		//
		// Clear node index.
		//
		if( $doClear )
			$idx->remove( $this->Node(), $theIndex );
		
		return $idx;																// ==>
	
	} // _GetNodeIndex.

	 

} // class COntologyEdge.


?>
