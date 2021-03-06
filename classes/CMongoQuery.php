<?php

/**
 * <i>CMongoQuery</i> class definition.
 *
 * This file contains the class definition of <b>CMongoQuery</b> which overloads its
 * {@link CQuery ancestor} to implement a Mongo query object.
 *
 *	@package	MyWrapper
 *	@subpackage	Persistence
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 13/06/2011
 */

/*=======================================================================================
 *																						*
 *									CMongoQuery.php										*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CQuery.php" );

/**
 * Mongo container.
 *
 * This include file contains the class definitions of the Mongo container.
 */
require_once( kPATH_LIBRARY_SOURCE."CMongoContainer.php" );

/**
 *	Mongo query.
 *
 * This class extends its {@link CQuery ancestor} to implement a public method that will
 * convert the current object's query into a query suitable to be submitted to a Mongo
 * database.
 *
 * This class implements a query that {@link Export() exports} as a Mongo query.
 *
 *	@package	MyWrapper
 *	@subpackage	Persistence
 */
class CMongoQuery extends CQuery
{
		

/*=======================================================================================
 *																						*
 *									PUBLIC EXPORT INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Export																			*
	 *==================================================================================*/

	/**
	 * Export query.
	 *
	 * The method will return an array suitable to be provided as a MongoDB query, the method
	 * requires a container that will take care of converting query arguments to native data
	 * types, this container must be an instance of {@link CMongoContainer CMongoContainer},
	 * or the method will raise an exception.
	 *
	 * @param CMongoContainer		$theContainer			Query container.
	 *
	 * @access public
	 * @return array
	 *
	 * @throws Exception
	 */
	public function Export( $theContainer )
	{
		//
		// Check container.
		//
		if( ! $theContainer instanceof CMongoContainer )
			throw new CException
				( "Unsupported container type",
				  kERROR_INVALID_PARAMETER,
				  kMESSAGE_TYPE_ERROR,
				  array( 'Container' => $theContainer ) );						// !@! ==>

		//
		// Init local storage.
		//
		$query = Array();
		
		//
		// Traverse object.
		//
		foreach( $this as $condition => $statements )
			$this->_ConvertCondition( $query, $theContainer, $condition, $statements );
		
		return $query;																// ==>
	
	} // Export.

	 

/*=======================================================================================
 *																						*
 *								PROTECTED VALIDATION INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_ValidateCondition																*
	 *==================================================================================*/

	/**
	 * Validate condition.
	 *
	 * This method expects a condition as its argument, it will check if it is a valid
	 * condition, then it will {@link _ValidateStatement() validate} all condition
	 * statements.
	 *
	 * In this class we handle queries to Mongo databases, so the depth of the query
	 * conditions cannot go beyond 2 levels.
	 *
	 * We overload this method to prevent nesting OR conditions.
	 *
	 * @param string				$theCondition			Boolean condition.
	 * @param array					$theStatements			Statements list.
	 * @param integer				$theLevel				[PRIVATE] condition level.
	 *
	 * @access protected
	 */
	protected function _ValidateCondition( $theCondition, $theStatements, $theLevel )
	{
		//
		// Check level.
		//
		if( $theLevel > 2 )
			throw new CException
				( "Invalid query: Mongo queries cannot have nested conditions",
				  kERROR_INVALID_STATE,
				  kMESSAGE_TYPE_WARNING,
				  array( 'Statements' => $theStatements ) );					// !@! ==>
		
		//
		// Call parent method.
		//
		parent::_ValidateCondition( $theCondition, $theStatements, $theLevel );
	
	} // _ValidateCondition.

	 

/*=======================================================================================
 *																						*
 *							PROTECTED QUERY CONVERSION INTERFACE						*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_ConvertCondition																*
	 *==================================================================================*/

	/**
	 * Convert condition.
	 *
	 * This method will convert the statements of the provided condition to Mongo format.
	 *
	 * The parameters to this method are:
	 *
	 * <ul>
	 *	<li><b>&$theQuery</b>: Reference to an array that will receive the converted
	 *		condition.
	 *	<li><b>$theContainer</b>: Data container, must be derived from
	 *		{@link CMongoContainer CMongoContainer}.
	 *	<li><b>$theCondition</b>: Boolean condition code.
	 *	<li><b>$theStatements</b>: List of condition statements.
	 * </ul>
	 *
	 * @param reference			   &$theQuery				Receives converted query.
	 * @param CMongoContainer		$theContainer			Query container.
	 * @param string				$theCondition			Boolean condition.
	 * @param array					$theStatements			Statements list.
	 *
	 * @access protected
	 */
	protected function _ConvertCondition( &$theQuery, $theContainer,
													  $theCondition,
													  $theStatements )
	{
		//
		// Check container.
		//
		if( ! $theContainer instanceof CMongoContainer )
			throw new CException
					( "Unsupported container type",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Container' => $theContainer ) );					// !@! ==>

		//
		// Create condition container.
		//
		switch( $theCondition )
		{
			//
			// OR: We add this clause.
			//
			case kOPERATOR_OR:
				$theQuery[ '$or' ] = Array();
				$query = & $theQuery[ '$or' ];
				break;
				
			//
			// NOR: We add this clause.
			//
			case kOPERATOR_NOR:
				$theQuery[ '$nor' ] = Array();
				$query = & $theQuery[ '$nor' ];
				break;
			
			//
			// We use the current list.
			//
			default:
				$query = & $theQuery;
				break;
		
		} // Created condition container.
		
		//
		// Iterate statements.
		//
		foreach( $theStatements as $statement )
			$this->_ConvertStatement( $query, $theContainer, $theCondition, $statement );
	
	} // _ConvertCondition.

	 
	/*===================================================================================
	 *	_ConvertStatement																*
	 *==================================================================================*/

	/**
	 * Convert statement.
	 *
	 * This method will convert the statement to Mongo format.
	 *
	 * The parameters to this method are:
	 *
	 * <ul>
	 *	<li><b>&$theQuery</b>: Reference to an array that will receive the converted
	 *		statement.
	 *	<li><b>$theContainer</b>: Data container, must be derived from
	 *		{@link CMongoContainer CMongoContainer} and we assume this check has been done by
	 *		the {@link _ConvertCondition() caller}.
	 *	<li><b>$theCondition</b>: Boolean condition code.
	 *	<li><b>$theStatement</b>: Statement.
	 * </ul>
	 *
	 * @param reference			   &$theQuery				Receives converted statement.
	 * @param CMongoContainer		$theContainer			Query container.
	 * @param string				$theCondition			Boolean condition.
	 * @param array					$theStatement			Statement.
	 *
	 * @access protected
	 */
	protected function _ConvertStatement( &$theQuery, $theContainer,
													  $theCondition,
													  $theStatement )
	{
		//
		// Parse statement.
		//
		switch( $condition = key( $theStatement ) )
		{
			//
			// Handle nested conditions.
			//
			case kOPERATOR_AND:
			case kOPERATOR_NAND:
				//
				// Create container.
				//
				$theQuery[] = Array();
				
				//
				// Point to container.
				//
				$theQuery = & $theQuery[ count( $theQuery ) - 1 ];
		
			case kOPERATOR_OR:
			case kOPERATOR_NOR:
				//
				// Recurse.
				//
				$this->_ConvertCondition
					( $theQuery, $theContainer, $condition, $theStatement[ $condition ] );
				
				break;
			
			//
			// Handle statement.
			//
			default:
				//
				// Init local storage.
				//
				$statement = Array();
				
				//
				// Get statement container.
				//
				switch( $theCondition )
				{
					//
					// Use the list.
					//
					case kOPERATOR_AND:
					case kOPERATOR_NAND:
						$statement = & $theQuery;
						break;
				
					//
					// Use last element.
					//
					case kOPERATOR_OR:
					case kOPERATOR_NOR:
						$theQuery[] = Array();
						$statement = & $theQuery[ count( $theQuery ) - 1 ];
						break;
				}
				
				//
				// Save query data.
				//
				if( array_key_exists( kAPI_QUERY_DATA, $theStatement ) )
					$data = $theStatement[ kAPI_QUERY_DATA ];
				
				//
				// Parse by operator.
				//
				switch( $theStatement[ kAPI_QUERY_OPERATOR ] )
				{
					case kOPERATOR_EQUAL:
						$theContainer->UnserialiseData( $data );
						switch( $theCondition )
						{
							case kOPERATOR_AND:
							case kOPERATOR_OR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ] = $data;
								break;
							
							case kOPERATOR_NAND:
							case kOPERATOR_NOR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array( '$ne' => $data );
								break;
						}
						break;
						
					case kOPERATOR_EQUAL_NOT:
						$theContainer->UnserialiseData( $data );
						switch( $theCondition )
						{
							case kOPERATOR_AND:
							case kOPERATOR_OR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array( '$ne' => $data );
								break;
							
							case kOPERATOR_NAND:
							case kOPERATOR_NOR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ] = $data;
								break;
						}
						break;
						
					case kOPERATOR_LIKE:
						$tmp = '/^'.$data.'$/i';
						switch( $theCondition )
						{
							case kOPERATOR_AND:
							case kOPERATOR_OR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= new MongoRegex( $tmp );
								break;
							
							case kOPERATOR_NAND:
							case kOPERATOR_NOR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array( '$not' => new MongoRegex( $tmp ) );
								break;
						}
						break;
						
					case kOPERATOR_PREFIX:
					case kOPERATOR_PREFIX_NOCASE:
						$tmp = '/^'.$data.'/';
						if( $theStatement[ kAPI_QUERY_OPERATOR ]
							== kOPERATOR_PREFIX_NOCASE )
							$tmp .= 'i';
						switch( $theCondition )
						{
							case kOPERATOR_AND:
							case kOPERATOR_OR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= new MongoRegex( $tmp );
								break;
							
							case kOPERATOR_NAND:
							case kOPERATOR_NOR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array( '$not' => new MongoRegex( $tmp ) );
								break;
						}
						break;
						
					case kOPERATOR_CONTAINS:
					case kOPERATOR_CONTAINS_NOCASE:
						$tmp = '/'.$data.'/';
						if( $theStatement[ kAPI_QUERY_OPERATOR ]
							== kOPERATOR_CONTAINS_NOCASE )
							$tmp .= 'i';
						switch( $theCondition )
						{
							case kOPERATOR_AND:
							case kOPERATOR_OR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= new MongoRegex( $tmp );
								break;
							
							case kOPERATOR_NAND:
							case kOPERATOR_NOR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array( '$not' => new MongoRegex( $tmp ) );
								break;
						}
						break;
						
					case kOPERATOR_SUFFIX:
					case kOPERATOR_SUFFIX_NOCASE:
						$tmp = '/'.$data.'$/';
						if( $theStatement[ kAPI_QUERY_OPERATOR ]
							== kOPERATOR_SUFFIX_NOCASE )
							$tmp .= 'i';
						switch( $theCondition )
						{
							case kOPERATOR_AND:
							case kOPERATOR_OR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= new MongoRegex( $tmp );
								break;
							
							case kOPERATOR_NAND:
							case kOPERATOR_NOR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array( '$not' => new MongoRegex( $tmp ) );
								break;
						}
						break;
						
					case kOPERATOR_REGEX:
						$tmp = new MongoRegex( $data );
						switch( $theCondition )
						{
							case kOPERATOR_AND:
							case kOPERATOR_OR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ] = $tmp;
								break;
							
							case kOPERATOR_NAND:
							case kOPERATOR_NOR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array( '$not' => $tmp );
								break;
						}
						break;
						
					case kOPERATOR_LESS:
						$theContainer->UnserialiseData( $data );
						switch( $theCondition )
						{
							case kOPERATOR_AND:
							case kOPERATOR_OR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array( '$lt' => $data );
								break;
							
							case kOPERATOR_NAND:
							case kOPERATOR_NOR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array( '$gte' => $data );
								break;
						}
						break;
						
					case kOPERATOR_LESS_EQUAL:
						$theContainer->UnserialiseData( $data );
						switch( $theCondition )
						{
							case kOPERATOR_AND:
							case kOPERATOR_OR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array( '$lte' => $data );
								break;
							
							case kOPERATOR_NAND:
							case kOPERATOR_NOR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array( '$gt' => $data );
								break;
						}
						break;
						
					case kOPERATOR_GREAT:
						$theContainer->UnserialiseData( $data );
						switch( $theCondition )
						{
							case kOPERATOR_AND:
							case kOPERATOR_OR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array( '$gt' => $data );
								break;
							
							case kOPERATOR_NAND:
							case kOPERATOR_NOR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array( '$lte' => $data );
								break;
						}
						break;
						
					case kOPERATOR_GREAT_EQUAL:
						$theContainer->UnserialiseData( $data );
						switch( $theCondition )
						{
							case kOPERATOR_AND:
							case kOPERATOR_OR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array( '$gte' => $data );
								break;
							
							case kOPERATOR_NAND:
							case kOPERATOR_NOR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array( '$lt' => $data );
								break;
						}
						break;
						
					case kOPERATOR_IRANGE:
						$list = $this->_OrderRange( $data,
													$theContainer,
													$theStatement[ kAPI_QUERY_TYPE ] );
						switch( $theCondition )
						{
							case kOPERATOR_AND:
							case kOPERATOR_OR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array( '$gte' => array_shift( $list ),
											 '$lte' => array_shift( $list ) );
								break;
							
							case kOPERATOR_NAND:
							case kOPERATOR_NOR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array( '$or'
										=> array( array( '$lt'
															=> array_shift( $list ) ),
												  array( '$gt'
												  			=> array_shift( $list ) ) ) );
								break;
						}
						break;
						
					case kOPERATOR_ERANGE:
						$list = $this->_OrderRange( $data,
													$theContainer,
													$theStatement[ kAPI_QUERY_TYPE ] );
						switch( $theCondition )
						{
							case kOPERATOR_AND:
							case kOPERATOR_OR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array( '$gt' => array_shift( $list ),
											 '$lt' => array_shift( $list ) );
								break;
							
							case kOPERATOR_NAND:
							case kOPERATOR_NOR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array( '$or'
										=> array( array( '$lte'
															=> array_shift( $list ) ),
												  array( '$gte'
												  			=> array_shift( $list ) ) ) );
								break;
						}
						break;
						
					case kOPERATOR_NULL:
						switch( $theCondition )
						{
							case kOPERATOR_AND:
							case kOPERATOR_OR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array( '$exists' => FALSE );
								break;
							
							case kOPERATOR_NAND:
							case kOPERATOR_NOR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array( '$exists' => TRUE );
								break;
						}
						break;
						
					case kOPERATOR_NOT_NULL:
						switch( $theCondition )
						{
							case kOPERATOR_AND:
							case kOPERATOR_OR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array( '$exists' => TRUE );
								break;
							
							case kOPERATOR_NAND:
							case kOPERATOR_NOR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array( '$exists' => FALSE );
								break;
						}
						break;
						
					case kOPERATOR_IN:
						$list = Array();
						foreach( $data as $element )
						{
							$theContainer->UnserialiseData( $element );
							$list[] = $element;
						}
						switch( $theCondition )
						{
							case kOPERATOR_AND:
							case kOPERATOR_OR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array( '$in' => $list );
								break;
							
							case kOPERATOR_NAND:
							case kOPERATOR_NOR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array( '$nin' => $list );
								break;
						}
						break;
						
					case kOPERATOR_NI:
						$list = Array();
						foreach( $data as $element )
						{
							$theContainer->UnserialiseData( $element );
							$list[] = $element;
						}
						switch( $theCondition )
						{
							case kOPERATOR_AND:
							case kOPERATOR_OR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array( '$nin' => $list );
								break;
							
							case kOPERATOR_NAND:
							case kOPERATOR_NOR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array( '$in' => $list );
								break;
						}
						break;
						
					case kOPERATOR_ALL:
						$list = Array();
						foreach( $data as $element )
						{
							$theContainer->UnserialiseData( $element );
							$list[] = $element;
						}
						switch( $theCondition )
						{
							case kOPERATOR_AND:
							case kOPERATOR_OR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array( '$all' => $list );
								break;
							
							case kOPERATOR_NAND:
							case kOPERATOR_NOR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array
										(
											'$not' => array
											(
												'$all' => $list
											)
										);
								break;
						}
						break;
					
					//
					// Catch unhandled operators.
					//
					default:
						throw new CException
								( "Unsupported query operator (should have been catched)",
								  kERROR_UNSUPPORTED,
								  kMESSAGE_TYPE_BUG,
								  array( 'Operator'
								  			=> $theStatement
								  				[ kAPI_QUERY_OPERATOR ] ) );	// !@! ==>
				
				} // Parsed operator.
				
				break;
		
		} // Parsed statement key.
	
	} // _ConvertStatement.

	 

/*=======================================================================================
 *																						*
 *								PROTECTED QUERY UTILITIES								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_OrderRange																		*
	 *==================================================================================*/

	/**
	 * Order range elements.
	 *
	 * This method will order the provided range elements, the method accepts an array of
	 * two elements which represent the range bounds and will return an array with the two
	 * provided elements sorted.
	 *
	 * The method accepts three parameters:
	 *
	 * <ul>
	 *	<li><b>$theRange</b>: An array containing two elements representing the range
	 *		bounds.
	 *	<li><b>$theContainer</b>: The {@link CMongoContainer container} on which the query
	 *		will be executed.
	 *	<li><b>$theType</b>: The data type of the range elements.
	 * </ul>
	 *
	 * The method expects the range elements to be in
	 * {@link CDataType::SerialiseData() serialised} format, these elements will be
	 * {@link CContainer::UnserialiseData() converted} by this method which will return
	 * them in sorted order.
	 *
	 * @param mixed					$theRange				Range elements.
	 * @param CMongoContainer		$theContainer			Query container.
	 * @param string				$theType				Elements data type.
	 *
	 * @access protected
	 * @return array
	 */
	protected function _OrderRange( $theRange, CMongoContainer $theContainer, $theType )
	{
		//
		// Normalise range.
		//
		if( is_array( $theRange )
		 || ($theRange instanceof ArrayObject) )
		{
			$list = array_values( (array) $theRange );
			if( count( $list ) == 1 )
				$list[] = $list[ 0 ];
		}
		else
			$list = array( $theRange, $theRange );
		
		//
		// Convert range elements.
		//
		foreach( $list as $key => $value )
			$theContainer->UnserialiseData( $list[ $key ] );
	
		//
		// Parse by data type.
		//
		$switch = FALSE;
		switch( $theType )
		{
			case kTYPE_INT32:
			case kTYPE_INT64:
				if( (double) (string) $list[ 0 ]
					> (double) (string) $list[ 1 ] )
					$switch = TRUE;
				break;
			
			case kTYPE_STAMP:
				$d1 = new CDataTypeStamp( $list[ 0 ] );
				$d2 = new CDataTypeStamp( $list[ 1 ] );
				if( $d1->value() > $d2->value() )
					$switch = TRUE;
				break;
			
			case kTYPE_MongoId:
				if( (string) $list[ 0 ] > (string) $list[ 1 ] )
					$switch = TRUE;
				break;
			
			default:
				if( $list[ 0 ] > $list[ 1 ] )
					$switch = TRUE;
				break;
		}
		
		//
		// Switch elements.
		//
		if( $switch )
		{
			$tmp = $list[ 0 ];
			$list[ 0 ] = $list[ 1 ];
			$list[ 1 ] = $tmp;
		}
		
		return $list;																// ==>
	
	} // _OrderRange.

	 

} // class CMongoQuery.


?>
