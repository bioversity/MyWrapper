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
 * Local defines.
 *
 * This include file contains the local class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."COntologyNode.inc.php" );

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
			// Reset predicate term.
			//
			$id = $this->Node()->getId();
			$this->mPredicateTerm->Predicate( $id, FALSE );
			$mod = array( kTAG_PRED => $id );
			$theContainer[ kTAG_TERM ]->Commit( $mod,
												$this->mPredicateTerm[ kTAG_LID ],
												kFLAG_PERSIST_MODIFY +
												kFLAG_MODIFY_PULL +
												kFLAG_STATE_ENCODED );
			$this->Term( new COntologyTerm() );
			
			//
			// Reset subject term.
			//
			$term = $this->SubjectTerm();
			$id = $this->Subject()->getId();
			$term->Node( $id, FALSE );
			$mod = array( kTAG_NODE => $id );
			$theContainer[ kTAG_TERM ]->Commit( $mod,
												$term[ kTAG_LID ],
												kFLAG_PERSIST_MODIFY +
												kFLAG_MODIFY_PULL +
												kFLAG_STATE_ENCODED );
			$this->SubjectTerm( new COntologyTerm() );
			
			//
			// Reset object term.
			//
			$term = $this->ObjectTerm();
			$id = $this->Object()->getId();
			$term->Node( $id, FALSE );
			$mod = array( kTAG_NODE => $id );
			$theContainer[ kTAG_TERM ]->Commit( $mod,
												$term[ kTAG_LID ],
												kFLAG_PERSIST_MODIFY +
												kFLAG_MODIFY_PULL +
												kFLAG_STATE_ENCODED );
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
			$mod = array( kTAG_PRED => $id );
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
			// Add indexes.
			//
			$this->_CreateNodeIndex( $theContainer[ kTAG_NODE ] );
			
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
		// Create empty subject term.
		//
		$this->SubjectTerm( new COntologyTerm() );
	
		//
		// Create empty object term.
		//
		$this->ObjectTerm( new COntologyTerm() );
	
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
	 * In this class we get the term reference from the node {@link Type() type} property
	 * and load it, along with the {@link Subject() subject} and {@link Object() object}
	 * terms.
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
		// Load term.
		//
		if( ($ref = $this->Type()) !== NULL )
		{
			//
			// Find and load predicate term.
			//
			$term = CPersistentUnitObject::NewObject
						( $theContainer[ kTAG_TERM ],
						  COntologyTermObject::HashIndex( $ref ),
						  kFLAG_STATE_ENCODED );
			if( $term )
				$this->Term( $term );
			else
				throw new CException
						( "Invalid predicate term reference",
						  kERROR_NOT_FOUND,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Term' => $ref ) );							// !@! ==>
		
			//
			// Find subject node and load subject term.
			//
			$ref = $this->Subject()->getProperty( kTAG_TERM );
			if( $ref !== NULL )
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
			$ref = $this->Object()->getProperty( kTAG_TERM );
			if( $ref !== NULL )
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
		
		} // Current node has term reference: it means it was loaded.
		
		//
		// Initialise terms.
		//
		else
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
	 *	_CreateNodeIndex																*
	 *==================================================================================*/

	/**
	 * Create node indexes.
	 *
	 * This method will save node indexes after the node was {@link _Commit() committed},
	 * it will perform the following selections:
	 *
	 * <ul>
	 *	<li><i>{@link kINDEX_TERM kINDEX_TERM}</i>: The {@link Term() term}
	 *		{@link kTAG_GID global} identifier (NodeIndex).
	 *		container, it must be a Everyman\Neo4j\Client instance.
	 *	<li><i>{@link kINDEX_TERM_NAME kINDEX_TERM_NAME}</i>: The {@link Term() term}
	 *		{@link CTerm::Name() names} in all languages (NodeIndex).
	 *	<li><i>{@link kINDEX_TERM_DEFINITION kINDEX_TERM_DEFINITION}</i>: The
	 *		{@link Term() term} {@link CTerm::Definition() definitions} (NodeFulltextIndex).
	 * </ul>
	 *
	 * @param Everyman\Neo4j\Client	$theContainer		Node container.
	 *
	 * @access protected
	 */
	protected function _CreateNodeIndex( Everyman\Neo4j\Client $theContainer )
	{
		//
		// Load term and node.
		//
		$node = $this->Node();
		$term = $this->Term();
		
		//
		// Instantiate and remove indexes.
		//
		$idx_term = new RelationshipIndex( $theContainer, kINDEX_TERM );
		$idx_term->save();
		$idx_term->remove( $node, kINDEX_TERM );
	
		$idx_name = new RelationshipIndex( $theContainer, kINDEX_TERM_NAME );
		$idx_name->save();
		$idx_name->remove( $node, kINDEX_TERM_NAME );
	
		//
		// Add term index.
		//
		$idx_term->add( $node, kTAG_GID, $term[ kTAG_GID ] );
	
		//
		// Add names index.
		//
		foreach( $term[ kTAG_NAME ] as $element )
			$idx_name->add( $node, kTAG_NAME, $element[ kTAG_DATA ] );
	
	} // _PrepareCreate.

	 

} // class COntologyEdge.


?>
