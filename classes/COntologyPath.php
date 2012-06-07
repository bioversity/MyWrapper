<?php

/**
 * <i>COntologyPath</i> class definition.
 *
 * This file contains the class definition of <b>COntologyPath</b> which represents an
 * ontology term used to tag data.
 *
 *	@package	MyWrapper
 *	@subpackage	Ontology
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 06/06/2012
 */

/*=======================================================================================
 *																						*
 *									COntologyPath.php									*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CPersistentUnitObject.php" );

/**
 * Terms.
 *
 * This include file contains the terms class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."COntologyTerm.php" );

/**
 * Ontology path.
 *
 * Datasets are stored by this library in documents managed by a document database, there
 * is no predefined structure, except that each document attribute, or data element, is
 * identified by a tag which is the {@link kTAG_LID identifier} of instances from this
 * class.
 *
 * Instances of this class contain a list of ontology {@link COntologyTerm terms} whose
 * elements are related between each other by predicates, which are also
 * {@link COntologyTerm terms}.
 *
 * This path or chain of {@link COntologyTerm terms} represents the unique identifier of
 * this class instances and the tags with which data elements can be described.
 *
 * The path root is a {@link COntologyTerm term} whose {@link COntologyNode::Kind() kind}
 * must be {@link kTYPE_TRAIT kTYPE_TRAIT} which represents <i>what</b> the data element is
 * and the path leaf is a {@link COntologyTerm term} whose
 * {@link COntologyNode::Kind() kind} must be {@link kTYPE_MEASURE kTYPE_MEASURE} which
 * represents the <b>type</b> or <b>scale</b> of the data; all the
 * {@link COntologyTerm terms} found between these two describe <b>how</b> the data was
 * measured or obtained. This path or chain represents the <i>descriptors</i> of data in
 * this library and the container in which these objects are stored represents the data
 * dictionary.
 *
 * The class features the following properties:
 *
 * <ul>
 *	<li><i>{@link kTAG_CODE kTAG_CODE}</i>: This {@link Code() attribute} represents the tag
 *		that will be used to mark the data elements.
 *	<li><i>{@link kTAG_PATH kTAG_PATH}</i>: This {@link Path() attribute} holds the list of
 *		{@link COntologyTerm term} {@link COntologyTerm::GID() identifiers} representing the
 *		tag path, the attribute is a string formed by the concatenation of all the
 *		{@link COntologyTerm term} {@link COntologyTerm::GID() identifiers} structured as
 *		follows: <i>TERM/PREDICATE/TERM/PREDICATE/.../TERM</i> where all items are
 *		{@link COntologyTerm term} {@link COntologyTerm::GID() identifiers}.
 *	<li><i>{@link kTAG_TERM kTAG_TERM}</i>: This {@link Term() attribute} holds the list of
 *		{@link COntologyTerm terms} featured in the {@link Path() path} as an array of
 *		{@link COntologyTerm term} {@link kTAG_LID identifiers} in which odd elements
 *		represent the subjects and objects of the relationships and the even elements the
 *		relationship predicates.
 *	<li><i>{@link kTAG_REF_COUNT kTAG_REF_COUNT}</i>: This {@link RefCount() attribute}
 *		holds the count of data instances that refer to the current tag, or the number of
 *		data instances tagged by the current path. This attribute is required and is
 *		initialised to 0.
 * </ul>
 *
 *	@package	MyWrapper
 *	@subpackage	Ontology
 */
class COntologyPath extends CPersistentUnitObject
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
	 * We {@link CPersistentUnitObject::__construct() overload} the constructor to
	 * initialise the {@link _IsInited() inited} {@link kFLAG_STATE_INITED flag} if the
	 * {@link Path() code} attribute is set.
	 *
	 * @param mixed					$theContainer		Persistent container.
	 * @param mixed					$theIdentifier		Object identifier.
	 * @param bitfield				$theModifiers		Create modifiers.
	 *
	 * @access public
	 *
	 * @uses _IsInited
	 *
	 * @see kTAG_CODE
	 */
	public function __construct( $theContainer = NULL,
								 $theIdentifier = NULL,
								 $theModifiers = kFLAG_DEFAULT )
	{
		//
		// Enforce encoded flag.
		//
		$theModifiers |= kFLAG_STATE_ENCODED;
		
		//
		// Call parent method.
		//
		parent::__construct( $theContainer, $theIdentifier, $theModifiers );
		
		//
		// Set inited status.
		//
		$this->_IsInited( $this->offsetExists( kTAG_PATH ) );
		
	} // Constructor.

		

/*=======================================================================================
 *																						*
 *								PUBLIC MEMBER INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Code																			*
	 *==================================================================================*/

	/**
	 * Get code.
	 *
	 * This {@link kTAG_CODE attribute} holds the tag that will be used to annotate data,
	 * this string is generated at {@link Commit() commit} time, for this reason this method
	 * is read-only.
	 *
	 * @access public
	 * @return string
	 *
	 * @see kTAG_CODE
	 */
	public function Code()						{	return $this->offsetGet( kTAG_CODE );	}

	 
	/*===================================================================================
	 *	Path																			*
	 *==================================================================================*/

	/**
	 * Get path.
	 *
	 * This {@link kTAG_PATH attribute} holds the list of {@link COntologyTerm term}
	 * {@link COntologyTerm::GID() identifiers} representing the tag path, each element is
	 * separated by a {@link kTOKEN_INDEX_SEPARATOR kTOKEN_INDEX_SEPARATOR} token.
	 *
	 * This value represents the unique identifier of the object and it is set automatically
	 * by the {@link Term() term} method, for this reason this method is read-only.
	 *
	 * @access public
	 * @return string
	 *
	 * @see kTAG_PATH
	 */
	public function Path()						{	return $this->offsetGet( kTAG_PATH );	}

	 
	/*===================================================================================
	 *	RefCount																		*
	 *==================================================================================*/

	/**
	 * Manage references count.
	 *
	 * This method can be used to retrieve the object's {@link kTAG_REF_COUNT references}
	 * count, or number of data instances tagged by this object.
	 *
	 * This method is read-only, because this value is set programmatically.
	 *
	 * @access public
	 * @return integer
	 *
	 * @see kTAG_REF_COUNT
	 */
	public function RefCount()				{	return $this->offsetGet( kTAG_REF_COUNT );	}

	 
	/*===================================================================================
	 *	Term																			*
	 *==================================================================================*/

	/**
	 * Manage terms.
	 *
	 * This method can be used to add {@link COntologyTerm term} elements of the path, it
	 * accepts a single parameter which represents either the elements to be added or the
	 * operation to be performed:
	 *
	 * <ul>
	 *	<li><i>NULL</i>: This value indicates that we want to retrieve the whole list.
	 *	<li><i>FALSE</i>: This value indicates that we want to delete the whole list.
	 *	<li><i>array</i>: The elements of the array will be added to the list by recursing
	 *		this method.
	 *	<li><i>other</i>: Any other type will be analysed as follows:
	 *	 <ul>
	 *		<li><i>{@link COntologyTerm COntologyTerm}</i>: The term's
	 *			{@link kTAG_LID identifier} will be used.
	 *		<li><i>{@link COntologyEdge COntologyEdge}</i>: the edge elements will be
	 *			handled as follows:
	 *		 <ul>
	 *			<li><i>The current list is empty</i>: The three terms will make the list.
	 *			<li><i>The current list exists</i>: The method will check if the edge's
	 *				subject matches the last element in the list: if this is the case, the
	 *				method will add the predicate and object terms; if not, it will raise an
	 *				exception.
	 *		 </ul>
	 *		<li><i>other</i>: Any other type will be cast to a string and interpreted as the
	 *			{@link COntologyTerm term}'s {@link kTAG_LID identifier}.
	 *	 </ul>
	 * </ul>
	 *
	 * All elements will be appended to the list in the order they were provided, it must be
	 * noted that the class expects this list to be a sequence of term/predicate/term
	 * elements, this method is not responsible for checking this.
	 *
	 * This method will also update this object's {@link kTAG_PATH path} in the process.
	 *
	 * The method will return the current full list.
	 *
	 * @param mixed					$theValue			Term or operation.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses _CheckReference()
	 * @uses CAttribute::ManageArrayOffset()
	 *
	 * @see kTAG_TERM
	 */
	public function Term( $theValue = NULL )
	{
		//
		// Retrieve list.
		//
		if( $theValue === NULL )
			return $this->offsetGet( kTAG_TERM );									// ==>
		
		//
		// Delete list.
		//
		elseif( $theValue === FALSE )
		{
			//
			// Remove terms.
			//
			$this->offsetUnset( kTAG_TERM );
		
			//
			// Remove path.
			//
			$this->offsetUnset( kTAG_PATH );
		}
		
		//
		// Handle array.
		//
		elseif( is_array( $theValue ) )
		{
			//
			// Iterate list.
			//
			foreach( $theValue as $value )
				$this->Term( $value );
		
		} // Provided an array.
		
		//
		// Add elements.
		//
		else
		{
			//
			// Save current terms list.
			//
			$save = $this->offsetGet( kTAG_TERM );
			if( $save === NULL )
				$save = Array();

			//
			// Save current path.
			//
			$path = $this->offsetGet( kTAG_PATH );
			if( $path === NULL )
				$path = '';

			//
			// Resolve term.
			//
			if( $theValue instanceof COntologyTerm )
			{
				//
				// Add term.
				//
				$save[] = $theValue->offsetGet( kTAG_LID );
			
				//
				// Add to path.
				//
				if( strlen( $path ) )
					$path .= kTOKEN_INDEX_SEPARATOR;
				$path .= $theValue->GID();
			}
			
			//
			// Resolve ontology edge.
			//
			elseif( $theValue instanceof COntologyEdge )
			{
				//
				// Get subject.
				//
				$subject = $theValue->SubjectTerm();

				//
				// Handle empty list.
				//
				if( ! count( $save ) )
				{
					//
					// Add subject.
					//
					$save[] = $subject[ kTAG_LID ];
					
					//
					// Add subject path.
					//
					if( strlen( $path ) )
						$path .= kTOKEN_INDEX_SEPARATOR;
					$path .= $subject[ kTAG_GID ];
				
				} // Empty list.
				
				//
				// Match subject.
				//
				elseif( ! ($subject[ kTAG_LID ] == ($last = $save[ count( $save ) - 1 ])) )
					throw new CException
						( "Non-matching relationship",
						  kERROR_INVALID_PARAMETER,
						  kMESSAGE_TYPE_WARNING,
						  array( 'Object' => $last,
								 'Subject' => $tmp[ kTAG_LID ] ) );				// !@! ==>
			
				//
				// Get predicate.
				//
				$tmp = $theValue->Term();

				//
				// Add predicate.
				//
				$save[] = $tmp[ kTAG_LID ];
				
				//
				// Add predicate path.
				//
				$path .= $tmp[ kTAG_GID ];
			
				//
				// Get object.
				//
				$tmp = $theValue->ObjectTerm();

				//
				// Add object.
				//
				$save[] = $tmp[ kTAG_LID ];
				
				//
				// Add object path.
				//
				$path .= $tmp[ kTAG_GID ];
			
			} // Provided edge.
			
			//
			// Assume global identifier.
			//
			else
			{
				//
				// Add term.
				//
				$save[] = COntologyTerm::HashIndex( (string) $theValue );
				
				//
				// Add path.
				//
				if( strlen( $path ) )
					$path .= kTOKEN_INDEX_SEPARATOR;
				$path .= (string) $theValue;
			
			} // Provided global identifier.
			
			//
			// Update terms.
			//
			$this->offsetSet( kTAG_TERM, $save );
			
			//
			// Update path.
			//
			$this->offsetSet( kTAG_PATH, $path );
		
		} // Add elements.
			
		return $this->offsetGet( kTAG_TERM );										// ==>

	} // Term.

		

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
	 * {@link kFLAG_STATE_INITED status}: this is set if {@link kTAG_PATH code} property is
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
			$this->_IsInited( $this->offsetExists( kTAG_PATH ) );
	
	} // offsetSet.

	 
	/*===================================================================================
	 *	offsetUnset																		*
	 *==================================================================================*/

	/**
	 * Reset a value for a given offset.
	 *
	 * We overload this method to manage the {@link _IsInited() inited}
	 * {@link kFLAG_STATE_INITED status}: this is set if {@link kTAG_PATH code} property is
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
		$this->_IsInited( $this->offsetExists( kTAG_PATH ) );
	
	} // offsetUnset.

		

/*=======================================================================================
 *																						*
 *							PROTECTED IDENTIFICATION INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_index																			*
	 *==================================================================================*/

	/**
	 * Return the object's unique index.
	 *
	 * This method can be used to return a string value that represents the object's unique
	 * identifier. This value should generally be extracted from the object's properties.
	 *
	 * In this class we use the object's {@link Path() path}.
	 *
	 * @access protected
	 * @return string
	 */
	protected function _index()									{	return $this->Path();	}

		

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
	 * We overload this method to handle the check done {@link _PrepareCommit() before}:
	 * if the object exists already in the database, it will not be committed again. This is
	 * determined by the presence or not of the {@link Code() code}
	 * {@link kTAG_CODE attribute}: if the latter is not there, it means the object must be
	 * committed, if not, the inherited version of this method will not be executed.
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
		// Check code.
		//
		if( ! $this->offsetExists( kTAG_CODE ) )
		{
			//
			// Get sequence.
			//
			$sequence = $this->_NewSequence( $theContainer->Container() );
			
			//
			// Set code.
			//
			$this->offsetSet( kTAG_CODE, '@'.$sequence );
			
			//
			// Commit object.
			//
			$theContainer->Commit( $this, $theIdentifier, $theModifiers );
		
		} // New object.
		
		return $this->offsetGet( kTAG_CODE );										// ==>
	
	} // _Commit.

		

/*=======================================================================================
 *																						*
 *								PROTECTED PERSISTENCE UTILITIES							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_PrepareCommit																	*
	 *==================================================================================*/

	/**
	 * Normalise before a store.
	 *
	 * We {@link CPersistentUnitObject::_PrepareCommit() overload} this method to generate
	 * the object's {@link Code() code}.
	 *
	 * @param reference			   &$theContainer		Object container.
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Commit modifiers.
	 *
	 * @access protected
	 *
	 * @throws {@link CException CException}
	 *
	 * @uses kERROR_OPTION_MISSING kERROR_UNSUPPORTED
	 */
	protected function _PrepareCommit( &$theContainer, &$theIdentifier, &$theModifiers )
	{
		//
		// Call parent method.
		//
		parent::_PrepareCommit( $theContainer, $theIdentifier, $theModifiers );
		
		//
		// Check if object exists already.
		//
		if( ! ($theModifiers & kFLAG_PERSIST_DELETE) )
		{
			//
			// Try to find object.
			//
			$clone = new self( $theContainer, $theIdentifier, $theModifiers );
			if( $clone->_IsCommitted() )
			{
				//
				// Copy code.
				//
				$this->offsetSet( kTAG_CODE, $clone[ kTAG_CODE ] );
				
				//
				// Copy reference count.
				//
				$this->offsetSet( kTAG_REF_COUNT, $clone[ kTAG_REF_COUNT ] );
			
			} // Object exists already.
			
			//
			// Init reference count.
			//
			else
				$this->offsetSet( kTAG_REF_COUNT, 0 );
		
		} // Not deleting.
	
	} // _PrepareCommit.

		

/*=======================================================================================
 *																						*
 *								PRIVATE SEQUENCE INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_NewSequence																	*
	 *==================================================================================*/

	/**
	 * Get new sequence.
	 *
	 * This method will return the next sequence number and increment the existing one.
	 * The sequence is stored in a singleton record identified by the '@' character, this
	 * record contains a single {@link kTAG_COUNT attribute} which represents the next
	 * available sequence number.
	 *
	 * When retrieving the sequence, if the main record does not exist, this method will
	 * create one with a first value of 1.
	 *
	 * @param MongoCollection		$theContainer		Object container.
	 *
	 * @access protected
	 * @return integer
	 *
	 * @throws {@link CException CException}
	 */
	protected function _NewSequence( MongoCollection $theContainer )
	{
		//
		// Init local storage.
		//
		$criteria = array( kTAG_LID => '@' );
		$modified = array( '$inc' => array( kTAG_COUNT => 1 ) );
		$options = array( 'upsert' => TRUE, 'multiple' => FALSE, 'safe' => TRUE );
		
		//
		// Get sequence.
		//
		$record = $theContainer->findOne( $criteria );
		if( $record )
			$sequence = $record[ kTAG_COUNT ];
		else
		{
			$sequence = 1;
			$modified[ '$inc' ][ kTAG_COUNT ] = 2;
		}
		
		//
		// Increment sequence.
		//
		$status = $theContainer->update( $criteria , $modified, $options );
		if( ! $status[ 'ok' ] )
			throw new CException( "Unable to save sequence",
								  kERROR_INVALID_STATE,
								  kMESSAGE_TYPE_ERROR,
								  array( 'Status' => $status ) );				// !@! ==>
		
		return $sequence;															// ==>
	
	} // _NewSequence.

	 

} // class COntologyPath.


?>
