<?php

/**
 * <i>COntologyDataTag</i> class definition.
 *
 * This file contains the class definition of <b>COntologyDataTag</b> which represents an
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
 *									COntologyDataTag.php								*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."COntologyTerm.php" );

/**
 * Ontology path.
 *
 * Datasets are stored by this library in documents managed by a document database, there
 * is no predefined structure, except that each document attribute, or data element, is
 * identified by a tag which is the {@link kTAG_LID identifier} of instances from this
 * class or the {@link COntologyTerm COntologyTerm} class.
 *
 * A data element tagged by an instance of the {@link COntologyTerm COntologyTerm} class
 * will have all its metadata stored in that term, but there are cases in which one term is
 * not enough to describe the full metadata of a data element: for instance, a trait may be
 * measured using different methods and workflows, and this data may also be measured in
 * different units. For this reason this class extends the term
 * {@link COntologyTermObject base} class to handle this case.
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
 * No two objects may exist with the same {@link Path() path}.
 *
 * All instances of this class are uniquely {@link GID() identified} by a combination of two
 * elements:
 *
 * <ul>
 *	<li><i>{@link NS() Namespace}</i>: There is one instance of this class which represents
 *		the default namespace of all objects derived from this class, its
 *		{@link GID() identifier} is a '@' character (which is forbidden in other related
 *		term classes).
 *	<li><i>{@link Code() Code}</i>: The aforementioned namespace contains an integer field
 *		which represents a sequence counter which will be used as the object's
 *		{@link Code() code}.
 * </ul>
 *
 * The class features the following properties:
 *
 * <ul>
 *	<li><i>{@link kTAG_PATH kTAG_PATH}</i>: This {@link Path() attribute} holds the list of
 *		{@link COntologyTerm term} {@link COntologyTerm::GID() identifiers} representing the
 *		tag path, the attribute is a string formed by the concatenation of all the
 *		{@link COntologyTerm term} {@link COntologyTerm::GID() identifiers} structured as
 *		follows: <i>TERM/PREDICATE/TERM/PREDICATE/.../TERM</i> where all items are
 *		{@link COntologyTerm term} {@link COntologyTerm::GID() identifiers}. This property
 *		represents the object's unique identifier, no two records can have this same value.
 *	<li><i>{@link kTAG_TERM kTAG_TERM}</i>: This {@link Term() attribute} holds the list of
 *		{@link COntologyTerm terms} featured in the {@link Path() path} as an array of
 *		{@link COntologyTerm term} {@link kTAG_LID identifiers} in which odd elements
 *		represent the subjects and objects of the relationships and the even elements the
 *		relationship predicates.
 *	<li><i>{@link kTAG_UID kTAG_UID}</i>: This attribute holds the hashed value of the
 *		{@link Path() path}: it will be used to detect duplicate records.
 *	<li><i>{@link kTAG_REF_COUNT kTAG_REF_COUNT}</i>: This {@link RefCount() attribute}
 *		holds the count of data instances that refer to the current tag, or the number of
 *		data instances tagged by the current path. This attribute is required and is
 *		initialised to 0.
 * </ul>
 *
 *	@package	MyWrapper
 *	@subpackage	Ontology
 */
class COntologyDataTag extends COntologyTermObject
{
		

/*=======================================================================================
 *																						*
 *								PUBLIC MEMBER INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	NS																				*
	 *==================================================================================*/

	/**
	 * Manage term namespace.
	 *
	 * We {@link COntologyTerm::NS() overload} this method to force a default namespace
	 * with a '@' code, this namespace element is created programmatically.
	 *
	 * @param mixed					$theValue			Value.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @see kTAG_NAMESPACE
	 */
	public function NS( $theValue = NULL, $getOld = FALSE )
	{
		//
		// Set default namespace.
		//
		if( ! $this->offsetExists( kTAG_NAMESPACE ) )
			$this->offsetSet( kTAG_NAMESPACE, '@' );
		
		return '@';																	// ==>

	} // NS.

	 
	/*===================================================================================
	 *	Code																			*
	 *==================================================================================*/

	/**
	 * Get code.
	 *
	 * We {@link COntologyTerm::Code() overload} this method to make this method read-only,
	 * the code is generated programmatically.
	 *
	 * @param mixed					$theValue			Value.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @see kTAG_CODE
	 */
	public function Code( $theValue = NULL, $getOld = FALSE )
	{
		return $this->offsetGet( kTAG_CODE );										// ==>
	
	} // Code.

	 
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
	 *		<li><i>{@link COntologyEdge COntologyEdge}</i>: The edge elements will be
	 *			handled as follows:
	 *		 <ul>
	 *			<li><i>The current list is empty</i>: The three terms will make the list.
	 *			<li><i>The current list exists</i>: The method will check if the edge's
	 *				subject matches the last element in the list: if this is the case, the
	 *				method will add the predicate and object terms; if not, it will raise an
	 *				exception.
	 *		 </ul>
	 *		<li><i>{@link COntologyEdgeIndex COntologyEdgeIndex}</i>: The same treatment as
	 *			for the {@link COntologyEdge COntologyEdge}.
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
	 * The method will prevent modifications if the object is
	 * {@link _IsCommitted() committed}, because this would change the object's unique
	 * {@link kTAG_UID identifier}.
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
		// Modifications are only allowed if the object is not committed.
		//
		if( ! $this->offsetExists( kTAG_LID ) )
		{
			//
			// Delete list.
			//
			if( $theValue === FALSE )
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
					elseif( ! ($subject[ kTAG_LID ]
								== ($last = $save[ count( $save ) - 1 ])) )
						throw new CException
							( "Non-matching relationship",
							  kERROR_INVALID_PARAMETER,
							  kMESSAGE_TYPE_WARNING,
							  array( 'Object' => $last,
									 'Subject' => $tmp[ kTAG_LID ] ) );			// !@! ==>
				
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
					if( strlen( $path ) )
						$path .= kTOKEN_INDEX_SEPARATOR;
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
					if( strlen( $path ) )
						$path .= kTOKEN_INDEX_SEPARATOR;
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
		
		} // New object.
		
		else
			throw new CException
				( "The object has an identifier, its elements cannot be changed",
				  kERROR_PROTECTED,
				  kMESSAGE_TYPE_WARNING );										// !@! ==>
			
		return $this->offsetGet( kTAG_TERM );										// ==>

	} // Term.

		

/*=======================================================================================
 *																						*
 *								STATIC REFERENCE INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	HashIndex																		*
	 *==================================================================================*/

	/**
	 * Hash index.
	 *
	 * We {@link CPersistentUnitObject::HashIndex() overload} this method to disable index
	 * hashing: this is for two main reasons:
	 *
	 * <ul>
	 *	<li><i>Size</i>: A hashed index will have 12 characters, {@link GID() identifiers}
	 *		from this class would have to reach a sequence of 10 digits before becoming as
	 *		large as the hash.
	 *	<li><i>Uniqueness</i>: The type of the parent classe's {@link kTAG_LID identifier}
	 *		will be different than the type of instances from this class, this makes it
	 *		unlikely to generate a duplicate identifier.
	 * </ul>
	 *
	 * @param string				$theValue			Value to hash.
	 *
	 * @static
	 * @return string
	 */
	static function HashIndex( $theValue )							{	return $theValue;	}

		

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
	 * We {@link COntologyTerm::_PrepareCommit() overload} this method to check whether
	 * there is another record with the same {@link kTAG_UID unique} identifier, in that
	 * case we load the current object with the contents of the found one.
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
		// Check if object exists already.
		//
		if( (! $this->offsetExists( kTAG_LID ))				// Assuming new
		 && (! ($theModifiers & kFLAG_PERSIST_DELETE)) )	// and not deleting.
		{
			//
			// Save unique key.
			//
			$uid = new CDataTypeBinary( md5( $this->offsetGet( kTAG_PATH ), TRUE ) );
			
			//
			// Build query.
			//
			$query = new CMongoQuery();
			$query->AppendStatement(
						CQueryStatement::Equals( kTAG_UID, $uid, kTYPE_BINARY ),
						kOPERATOR_AND );
			
			//
			// Match object.
			//
			$clone = new self( $theContainer, $query );
			if( $clone->_IsCommitted() )
			{
				//
				// Replace contents.
				//
				$this->exchangeArray( $clone );
				
				//
				// Prevent committing.
				//
				$theModifiers &= (~kFLAG_PERSIST_MASK);
				
				return;																// ==>
			
			} // Matched.
			
			//
			// Handle new object.
			//
			else
			{
				//
				// Init unique key.
				//
				$this->offsetSet( kTAG_UID, $uid );
				
				//
				// Get sequence.
				//
				$sequence = $this->_NewSequence( $theContainer );
				
				//
				// Set code.
				//
				$this->offsetSet( kTAG_CODE, $sequence );
				
				//
				// Set kind.
				//
				$this->Kind( kTYPE_ANNOTATION, TRUE );
				
			} // New object.
		
		} // Not deleting
		
		//
		// Call parent method.
		//
		parent::_PrepareCommit( $theContainer, $theIdentifier, $theModifiers );
	
	} // _PrepareCommit.

		

/*=======================================================================================
 *																						*
 *								PROTECTED STATUS UTILITIES								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_Inited																			*
	 *==================================================================================*/

	/**
	 * Return {@link _IsInited() initialised} status.
	 *
	 * We {@link COntologyTerm::_Inited() overload} this method to link the presence of the
	 * {@link Term() term} {@link kTAG_TERM attribute} to the
	 * {@link _IsInited() initialised} {@link kFLAG_STATE_INITED status} and disable all
	 * other inherited requirements.
	 *
	 * @see kTAG_TERM
	 *
	 * @access protected
	 * @return boolean
	 */
	protected function _Inited()			{	return $this->offsetExists( kTAG_TERM );	}

		

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
	 * @param CMongoContainer		$theContainer		Object container.
	 *
	 * @access protected
	 * @return integer
	 *
	 * @throws {@link CException CException}
	 */
	protected function _NewSequence( CMongoContainer $theContainer )
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
		$record = $theContainer->Container()->findOne( $criteria );
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
		$status = $theContainer->Container()->update( $criteria , $modified, $options );
		if( ! $status[ 'ok' ] )
			throw new CException( "Unable to save sequence",
								  kERROR_INVALID_STATE,
								  kMESSAGE_TYPE_ERROR,
								  array( 'Status' => $status ) );				// !@! ==>
		
		return $sequence;															// ==>
	
	} // _NewSequence.

	 

} // class COntologyDataTag.


?>
