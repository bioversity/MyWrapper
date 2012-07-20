<?php

/**
 * <i>TSessionMongoNeo4jOntology</i> trait definition.
 *
 * This file contains the trait definition of <b>TSessionMongoNeo4jOntology</b> which
 * defines a trait that {@link CSessionMongoNeo4j CSessionMongoNeo4j} derived classes can
 * use to implement ontology management.
 *
 *	@package	MyWrapper
 *	@subpackage	Session
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 20/07/2012
*/

/*=======================================================================================
 *																						*
 *							TSessionMongoNeo4jOntology.php								*
 *																						*
 *======================================================================================*/

/**
 *	Ontology session Mongo and Neo4j trait.
 *
 * This class implements the necessary members and methods to access and manage ontologies,
 * storing {@link COntologyTerm terms} and node indexes in a
 * {@link CMongoContainer CMongoContainer}.
 *
 *	@package	MyWrapper
 *	@subpackage	Session
 */
trait TSessionMongoNeo4jOntology
{
	/**
	 * Tags container.
	 *
	 * This data member holds the tags container.
	 *
	 * @var mixed
	 */
	 protected $mTagsContainer = NULL;

	/**
	 * Terms container.
	 *
	 * This data member holds the terms container.
	 *
	 * @var mixed
	 */
	 protected $mTermsContainer = NULL;

	/**
	 * Nodes index container.
	 *
	 * This data member holds the nodes index container.
	 *
	 * @var mixed
	 */
	 protected $mNodesIdxContainer = NULL;

	/**
	 * Edges index container.
	 *
	 * This data member holds the edges index container.
	 *
	 * @var mixed
	 */
	 protected $mEdgesIdxContainer = NULL;

		

/*=======================================================================================
 *																						*
 *								PUBLIC RESOURCES INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	TagsContainer																	*
	 *==================================================================================*/

	/**
	 * Manage the default tags container.
	 *
	 * This method can be used to manage the session's default tags container. This object
	 * represents the container in which all ontology tags are stored.
	 *
	 * The method accepts two parameters:
	 *
	 * <ul>
	 *	<li><b>$theValue</b>: The value to set or the operation to perform:
	 *	 <ul>
	 *		<li><i>NULL</i>: Retrieve the current value.
	 *		<li><i>FALSE</i>: Delete the current value, it will be set to <i>NULL</i>.
	 *		<li><i>other</i>: Any other value will be interpreted as the new value to be
	 *			set.
	 *	 </ul>
	 *	<li><b>$getOld</b>: A boolean flag determining which value the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value <i>before</i> it has eventually been modified,
	 *			this option is only relevant when deleting or relacing a value.
	 *		<li><i>FALSE</i>: Return the value <i>after</i> it has eventually been modified.
	 *	 </ul>
	 * </ul>
	 *
	 * We ensure the provided parameter is either a MongoCollection or a
	 * {@link CMongoContainer CMongoContainer} object, the latter being the expected type.
	 *
	 * @param mixed					$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @throws {@link CException CException}
	 *
	 * @uses CObject::ManageMember()
	 */
	public function TagsContainer( $theValue = NULL, $getOld = FALSE )
	{
		//
		// New value.
		//
		if( ($theValue !== NULL)
		 && ($theValue !== FALSE) )
		{
			//
			// Convert.
			//
			if( $theValue instanceof MongoCollection )
			{
				//
				// Instantiate container.
				//
				$tmp = new CMongoContainer();
				
				//
				// Set native container.
				//
				$tmp->Container( $theValue );
				
				//
				// Update parameter.
				//
				$theValue = $tmp;
			
			} // MongoCollection.
			
			//
			// Check value.
			//
			if( ! ($theValue instanceof CMongoContainer) )
				throw new CException
					( "Unsupported tags container reference",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Reference' => $theValue ) );						// !@! ==>
		
		} // New value.
		
		return CObject::ManageMember( $this->mTagsContainer, $theValue, $getOld );	// ==>

	} // TagsContainer.

	 
	/*===================================================================================
	 *	TermsContainer																	*
	 *==================================================================================*/

	/**
	 * Manage the default terms container.
	 *
	 * This method can be used to manage the session's default terms container. This object
	 * represents the container in which all ontology terms are stored.
	 *
	 * The method accepts two parameters:
	 *
	 * <ul>
	 *	<li><b>$theValue</b>: The value to set or the operation to perform:
	 *	 <ul>
	 *		<li><i>NULL</i>: Retrieve the current value.
	 *		<li><i>FALSE</i>: Delete the current value, it will be set to <i>NULL</i>.
	 *		<li><i>other</i>: Any other value will be interpreted as the new value to be
	 *			set.
	 *	 </ul>
	 *	<li><b>$getOld</b>: A boolean flag determining which value the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value <i>before</i> it has eventually been modified,
	 *			this option is only relevant when deleting or relacing a value.
	 *		<li><i>FALSE</i>: Return the value <i>after</i> it has eventually been modified.
	 *	 </ul>
	 * </ul>
	 *
	 * We ensure the provided parameter is either a MongoCollection or a
	 * {@link CMongoContainer CMongoContainer} object, the latter being the expected type.
	 *
	 * @param mixed					$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @throws {@link CException CException}
	 *
	 * @uses CObject::ManageMember()
	 */
	public function TermsContainer( $theValue = NULL, $getOld = FALSE )
	{
		//
		// New value.
		//
		if( ($theValue !== NULL)
		 && ($theValue !== FALSE) )
		{
			//
			// Convert.
			//
			if( $theValue instanceof MongoCollection )
			{
				//
				// Instantiate container.
				//
				$tmp = new CMongoContainer();
				
				//
				// Set native container.
				//
				$tmp->Container( $theValue );
				
				//
				// Update parameter.
				//
				$theValue = $tmp;
			
			} // MongoCollection.
			
			//
			// Check value.
			//
			if( ! ($theValue instanceof CMongoContainer) )
				throw new CException
					( "Unsupported terms container reference",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Reference' => $theValue ) );						// !@! ==>
		
		} // New value.
		
		return CObject::ManageMember( $this->mTermsContainer, $theValue, $getOld );	// ==>

	} // TermsContainer.

	 
	/*===================================================================================
	 *	NodesIndexContainer																*
	 *==================================================================================*/

	/**
	 * Manage the default nodes index container.
	 *
	 * This method can be used to manage the session's default nodes index container. This
	 * object represents the container that will be used as the graph nodes index.
	 *
	 * The method accepts two parameters:
	 *
	 * <ul>
	 *	<li><b>$theValue</b>: The value to set or the operation to perform:
	 *	 <ul>
	 *		<li><i>NULL</i>: Retrieve the current value.
	 *		<li><i>FALSE</i>: Delete the current value, it will be set to <i>NULL</i>.
	 *		<li><i>other</i>: Any other value will be interpreted as the new value to be
	 *			set.
	 *	 </ul>
	 *	<li><b>$getOld</b>: A boolean flag determining which value the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value <i>before</i> it has eventually been modified,
	 *			this option is only relevant when deleting or relacing a value.
	 *		<li><i>FALSE</i>: Return the value <i>after</i> it has eventually been modified.
	 *	 </ul>
	 * </ul>
	 *
	 * We ensure the provided parameter is either a MongoCollection or a
	 * {@link CMongoContainer CMongoContainer} object, the latter being the expected type.
	 *
	 * @param mixed					$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @throws {@link CException CException}
	 *
	 * @uses CObject::ManageMember()
	 */
	public function NodesIndexContainer( $theValue = NULL, $getOld = FALSE )
	{
		//
		// New value.
		//
		if( ($theValue !== NULL)
		 && ($theValue !== FALSE) )
		{
			//
			// Convert.
			//
			if( $theValue instanceof MongoCollection )
			{
				//
				// Instantiate container.
				//
				$tmp = new CMongoContainer();
				
				//
				// Set native container.
				//
				$tmp->Container( $theValue );
				
				//
				// Update parameter.
				//
				$theValue = $tmp;
			
			} // MongoCollection.
			
			//
			// Check value.
			//
			if( ! ($theValue instanceof CMongoContainer) )
				throw new CException
					( "Unsupported nodes index container reference",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Reference' => $theValue ) );						// !@! ==>
		
		} // New value.
		
		return CObject::ManageMember
				( $this->mNodesIdxContainer, $theValue, $getOld );					// ==>

	} // NodesIndexContainer.

	 
	/*===================================================================================
	 *	EdgesIndexContainer																*
	 *==================================================================================*/

	/**
	 * Manage the default edges index container.
	 *
	 * This method can be used to manage the session's default edges index container. This
	 * object represents the container that will be used as the graph edges index.
	 *
	 * The method accepts two parameters:
	 *
	 * <ul>
	 *	<li><b>$theValue</b>: The value to set or the operation to perform:
	 *	 <ul>
	 *		<li><i>NULL</i>: Retrieve the current value.
	 *		<li><i>FALSE</i>: Delete the current value, it will be set to <i>NULL</i>.
	 *		<li><i>other</i>: Any other value will be interpreted as the new value to be
	 *			set.
	 *	 </ul>
	 *	<li><b>$getOld</b>: A boolean flag determining which value the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value <i>before</i> it has eventually been modified,
	 *			this option is only relevant when deleting or relacing a value.
	 *		<li><i>FALSE</i>: Return the value <i>after</i> it has eventually been modified.
	 *	 </ul>
	 * </ul>
	 *
	 * We ensure the provided parameter is either a MongoCollection or a
	 * {@link CMongoContainer CMongoContainer} object, the latter being the expected type.
	 *
	 * @param mixed					$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @throws {@link CException CException}
	 *
	 * @uses CObject::ManageMember()
	 */
	public function EdgesIndexContainer( $theValue = NULL, $getOld = FALSE )
	{
		//
		// New value.
		//
		if( ($theValue !== NULL)
		 && ($theValue !== FALSE) )
		{
			//
			// Convert.
			//
			if( $theValue instanceof MongoCollection )
			{
				//
				// Instantiate container.
				//
				$tmp = new CMongoContainer();
				
				//
				// Set native container.
				//
				$tmp->Container( $theValue );
				
				//
				// Update parameter.
				//
				$theValue = $tmp;
			
			} // MongoCollection.
			
			//
			// Check value.
			//
			if( ! ($theValue instanceof CMongoContainer) )
				throw new CException
					( "Unsupported edges index container reference",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Reference' => $theValue ) );						// !@! ==>
		
		} // New value.
		
		return CObject::ManageMember
				( $this->mEdgesIdxContainer, $theValue, $getOld );					// ==>

	} // EdgesIndexContainer.

		

/*=======================================================================================
 *																						*
 *							PROTECTED INITIALISATION INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_InitResources																	*
	 *==================================================================================*/

	/**
	 * Initialise resources.
	 *
	 * This method will initialise the default resources, the method expects one parameter
	 * that determines whether these resources are to be initialised, <i>TRUE</i>, or reset,
	 * <i>FALSE</i>.
	 *
	 * In this class we handle the {@link TagsContainer() tags},
	 * {@link TermsContainer() terms}, {@link NodesIndexContainer() nodes} index and
	 * {@link EdgesIndexContainer() edges} index containers.
	 *
	 * @param boolean				$theOperation		TRUE set, FALSE reset.
	 *
	 * @access protected
	 *
	 * @uses _InitTagsContainer()
	 * @uses _InitTermsContainer()
	 * @uses _InitNodesIdxContainer()
	 * @uses _InitEdgesIdxContainer()
	 */
	protected function _InitResources( $theOperation )
	{
		//
		// Call parent method.
		//
		parent::_InitResources( $theOperation );
		
		//
		// Init tags container.
		//
		$this->_InitTagsContainer( $theOperation );
		
		//
		// Init terms container.
		//
		$this->_InitTermsContainer( $theOperation );
		
		//
		// Init nodes index container.
		//
		$this->_InitNodesIdxContainer( $theOperation );
		
		//
		// Init edges index container.
		//
		$this->_InitEdgesIdxContainer( $theOperation );
		
	} // _InitResources.

	 
	/*===================================================================================
	 *	_InitTagsContainer																*
	 *==================================================================================*/

	/**
	 * Initialise tags container.
	 *
	 * This method will initialise the default tags {@link TagsContainer() container}, the
	 * method expects one boolean parameter: <i>TRUE</i> will open the tags container
	 * connection, <i>FALSE</i> will reset the tags container connection.
	 *
	 * In this class we initialise a MongoCollection container by the default tag container
	 * {@link kDEFAULT_CNT_TAGS name}.
	 *
	 * @param boolean				$theOperation		TRUE set, FALSE reset.
	 *
	 * @access protected
	 *
	 * @uses TagsContainer()
	 *
	 * @see kDEFAULT_CNT_TAGS
	 */
	protected function _InitTagsContainer( $theOperation )
	{
		//
		// Set.
		//
		if( $theOperation )
			$this->TagsContainer(
				$this->Database()->
					selectCollection(
						kDEFAULT_CNT_TAGS ) );
		
		//
		// Reset.
		//
		else
			$this->TagsContainer( FALSE );
	
	} // _InitTagsContainer.

	 
	/*===================================================================================
	 *	_InitTermsContainer																*
	 *==================================================================================*/

	/**
	 * Initialise terms container.
	 *
	 * This method will initialise the default terms {@link TermsContainer() container}, the
	 * method expects one boolean parameter: <i>TRUE</i> will open the terms container
	 * connection, <i>FALSE</i> will reset the terms container connection.
	 *
	 * In this class we initialise a MongoCollection container by the default term container
	 * {@link kDEFAULT_CNT_TERMS name}.
	 *
	 * @param boolean				$theOperation		TRUE set, FALSE reset.
	 *
	 * @access protected
	 *
	 * @uses TermsContainer()
	 *
	 * @see kDEFAULT_CNT_TERMS
	 */
	protected function _InitTermsContainer( $theOperation )
	{
		//
		// Set.
		//
		if( $theOperation )
			$this->TermsContainer(
				$this->Database()->
					selectCollection(
						kDEFAULT_CNT_TERMS ) );
		
		//
		// Reset.
		//
		else
			$this->TermsContainer( FALSE );
	
	} // _InitTermsContainer.

	 
	/*===================================================================================
	 *	_InitNodesIdxContainer															*
	 *==================================================================================*/

	/**
	 * Initialise nodes index container.
	 *
	 * This method will initialise the default nodes index
	 * {@link NodesIndexContainer() container}, the method expects one boolean parameter:
	 * <i>TRUE</i> will open the nodes index container connection, <i>FALSE</i> will reset
	 * the nodes index container connection.
	 *
	 * In this class we initialise a MongoCollection container by the default nodes index
	 * container {@link kDEFAULT_CNT_NODES name}.
	 *
	 * @param boolean				$theOperation		TRUE set, FALSE reset.
	 *
	 * @access protected
	 *
	 * @uses NodesIndexContainer()
	 *
	 * @see kDEFAULT_CNT_NODES
	 */
	protected function _InitNodesIdxContainer( $theOperation )
	{
		//
		// Set.
		//
		if( $theOperation )
			$this->NodesIndexContainer(
				$this->Database()->
					selectCollection(
						kDEFAULT_CNT_NODES ) );
		
		//
		// Reset.
		//
		else
			$this->NodesIndexContainer( FALSE );
	
	} // _InitNodesIdxContainer.

	 
	/*===================================================================================
	 *	_InitEdgesIdxContainer															*
	 *==================================================================================*/

	/**
	 * Initialise edges index container.
	 *
	 * This method will initialise the default edges index
	 * {@link EdgesIndexContainer() container}, the method expects one boolean parameter:
	 * <i>TRUE</i> will open the edges index container connection, <i>FALSE</i> will reset
	 * the edges index container connection.
	 *
	 * In this class we initialise a MongoCollection container by the default edges index
	 * container {@link kDEFAULT_CNT_EDGES name}.
	 *
	 * @param boolean				$theOperation		TRUE set, FALSE reset.
	 *
	 * @access protected
	 *
	 * @uses EdgesIndexContainer()
	 *
	 * @see kDEFAULT_CNT_EDGES
	 */
	protected function _InitEdgesIdxContainer( $theOperation )
	{
		//
		// Set.
		//
		if( $theOperation )
			$this->EdgesIndexContainer(
				$this->Database()->
					selectCollection(
						kDEFAULT_CNT_EDGES ) );
		
		//
		// Reset.
		//
		else
			$this->EdgesIndexContainer( FALSE );
	
	} // _InitEdgesIdxContainer.

		

/*=======================================================================================
 *																						*
 *							PROTECTED SERIALISATION INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_PreSerialize																	*
	 *==================================================================================*/

	/**
	 * Prepare before serialization.
	 *
	 * This method will be called before the object gets serialized, it is an opportunity
	 * to cleanup elements that cannot be serialized and do other optimisations.
	 *
	 * In this trait we close the ontology container connections.
	 *
	 * @access protected
	 */
	protected function _PreSerialize()
	{
		//
		// Call parent method.
		// Note that you must have a parent
		// since the base class is abstract.
		//
		parent::_PreSerialize();
		
		//
		// Close edges index container.
		//
		$this->_InitEdgesIdxContainer( FALSE );
		
		//
		// Close nodes index container.
		//
		$this->_InitNodesIdxContainer( FALSE );

		//
		// Close terms container.
		//
		$this->_InitTermsContainer( FALSE );
		
		//
		// Close tags container.
		//
		$this->_InitTagsContainer( FALSE );
		
	} // _PreSerialize.

	 
	/*===================================================================================
	 *	_PostUnserialize																*
	 *==================================================================================*/

	/**
	 * Prepare after unserialization.
	 *
	 * This method will be called after the object gets unserialized, it is an opportunity
	 * to restore elements that were not serialised.
	 *
	 * @access protected
	 */
	protected function _PostUnserialize()
	{
		//
		// Open tags container.
		//
		$this->_InitTagsContainer( TRUE );
		
		//
		// Open terms container.
		//
		$this->_InitTermsContainer( TRUE );
		
		//
		// Open nodes index container.
		//
		$this->_InitNodesIdxContainer( TRUE );

		//
		// Open edges index container.
		//
		$this->_InitEdgesIdxContainer( TRUE );
		
		//
		// Call parent method.
		// Note that you must have a parent
		// since the base class is abstract.
		//
		parent::_PostUnserialize();
		
	} // _PostUnserialize.

	 

} // class TSessionMongoNeo4jOntology.


?>
