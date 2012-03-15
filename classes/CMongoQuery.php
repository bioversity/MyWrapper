<?php

/**
 * <i>CMongoQuery</i> class definition.
 *
 * This file contains the class definition of <b>CMongoQuery</b> which overloads its
 * {@link CQuery ancestor} to implement a Mongo query object.
 *
 *	@package	Framework
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
 *	@package	Framework
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
	 * This method will return the current query converted to the Mongo format, it requires
	 * a Mongo {@link CMongoContainer container} as parameter in order to perform query data
	 * {@link CMongoContainer::UnserialiseData() conversion}.
	 *
	 * The method will return an array suitable to be provided as a MongoDB query.
	 *
	 * @param CMongoContainer		$theContainer			Query container.
	 *
	 * @access public
	 * @return array
	 *
	 * @throws Exception
	 */
	public function Export( CMongoContainer $theContainer )
	{
		//
		// Init local storage.
		//
		$query = Array();
		
		//
		// Traverse object.
		//
		foreach( $this as $condition => $statements )
			$this->_ConvertCondition( $query, $condition, $statements, $theContainer );
		
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
	 * @access private
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
	 *	<li><b>$theCondition</b>: Boolean condition code.
	 *	<li><b>$theStatements</b>: List of condition statements.
	 *	<li><b>$theContainer</b>: The container to which the query is directed, this
	 *		parameter will take care of converting the container specific data types.
	 * </ul>
	 *
	 * @param reference			   &$theQuery				Receives converted query.
	 * @param string				$theCondition			Boolean condition.
	 * @param array					$theStatements			Statements list.
	 * @param CMongoContainer		$theContainer			Query container.
	 *
	 * @access private
	 */
	protected function _ConvertCondition( &$theQuery, $theCondition,
													  $theStatements,
													  $theContainer )
	{
		//
		// Create condition container.
		//
		switch( $theCondition )
		{
			case kOPERATOR_OR:
				$theQuery[ '$or' ] = Array();
				$query = & $theQuery[ '$or' ];
				break;
				
			case kOPERATOR_NOR:
				$theQuery[ '$nor' ] = Array();
				$query = & $theQuery[ '$nor' ];
				break;
			
			default:
				$query = & $theQuery;
				break;
		
		} // Created condition container.
		
		//
		// Iterate statements.
		//
		foreach( $theStatements as $statement )
			$this->_ConvertStatement( $query, $theCondition, $statement, $theContainer );
	
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
	 *	<li><b>$theCondition</b>: Boolean condition code.
	 *	<li><b>$theStatement</b>: Statement.
	 *	<li><b>$theContainer</b>: The container to which the query is directed, this
	 *		parameter will take care of converting the container specific data types.
	 * </ul>
	 *
	 * @param reference			   &$theQuery				Receives converted statement.
	 * @param string				$theCondition			Boolean condition.
	 * @param array					$theStatement			Statement.
	 * @param CMongoContainer		$theContainer			Query container.
	 *
	 * @access private
	 */
	protected function _ConvertStatement( &$theQuery, $theCondition,
													  $theStatement,
													  $theContainer )
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
					( $theQuery, $condition, $theStatement[ $condition ], $theContainer );
				
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
					case kOPERATOR_AND:
					case kOPERATOR_NAND:
						$statement = & $theQuery;
						break;
				
					case kOPERATOR_OR:
					case kOPERATOR_NOR:
						$theQuery[] = Array();
						$statement = & $theQuery[ count( $theQuery ) - 1 ];
						break;
				}
				
				//
				// Parse by operator.
				//
				switch( $theStatement[ kAPI_QUERY_OPERATOR ] )
				{
					case kOPERATOR_EQUAL:
						$theContainer->UnserialiseData( $theStatement[ kAPI_QUERY_DATA ] );
						switch( $theCondition )
						{
							case kOPERATOR_AND:
							case kOPERATOR_OR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= $theStatement[ kAPI_QUERY_DATA ];
								break;
							
							case kOPERATOR_NAND:
							case kOPERATOR_NOR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array( '$ne' => $theStatement[ kAPI_QUERY_DATA ] );
								break;
						}
						break;
						
					case kOPERATOR_EQUAL_NOT:
						$theContainer->UnserialiseData( $theStatement[ kAPI_QUERY_DATA ] );
						switch( $theCondition )
						{
							case kOPERATOR_AND:
							case kOPERATOR_OR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array( '$ne' => $theStatement[ kAPI_QUERY_DATA ] );
								break;
							
							case kOPERATOR_NAND:
							case kOPERATOR_NOR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= $theStatement[ kAPI_QUERY_DATA ];
								break;
						}
						break;
						
					case kOPERATOR_LIKE:
						$tmp = '/^'.$theStatement[ kAPI_QUERY_DATA ].'$/i';
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
						$tmp = '/^'.$theStatement[ kAPI_QUERY_DATA ].'/';
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
						$tmp = new MongoRegex( $theStatement[ kAPI_QUERY_DATA ] );
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
						
					case kOPERATOR_CONTAINS:
						$tmp = new MongoRegex( '/.'.$theStatement[ kAPI_QUERY_DATA ].'./' );
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
						
					case kOPERATOR_SUFFIX:
						$tmp = '/'.$theStatement[ kAPI_QUERY_DATA ].'$/';
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
						
					case kOPERATOR_LESS:
						$theContainer->UnserialiseData( $theStatement[ kAPI_QUERY_DATA ] );
						switch( $theCondition )
						{
							case kOPERATOR_AND:
							case kOPERATOR_OR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array( '$lt' => $theStatement[ kAPI_QUERY_DATA ] );
								break;
							
							case kOPERATOR_NAND:
							case kOPERATOR_NOR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array( '$gte' => $theStatement[ kAPI_QUERY_DATA ] );
								break;
						}
						break;
						
					case kOPERATOR_LESS_EQUAL:
						$theContainer->UnserialiseData( $theStatement[ kAPI_QUERY_DATA ] );
						switch( $theCondition )
						{
							case kOPERATOR_AND:
							case kOPERATOR_OR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array( '$lte' => $theStatement[ kAPI_QUERY_DATA ] );
								break;
							
							case kOPERATOR_NAND:
							case kOPERATOR_NOR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array( '$gt' => $theStatement[ kAPI_QUERY_DATA ] );
								break;
						}
						break;
						
					case kOPERATOR_GREAT:
						$theContainer->UnserialiseData( $theStatement[ kAPI_QUERY_DATA ] );
						switch( $theCondition )
						{
							case kOPERATOR_AND:
							case kOPERATOR_OR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array( '$gt' => $theStatement[ kAPI_QUERY_DATA ] );
								break;
							
							case kOPERATOR_NAND:
							case kOPERATOR_NOR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array( '$lte' => $theStatement[ kAPI_QUERY_DATA ] );
								break;
						}
						break;
						
					case kOPERATOR_GREAT_EQUAL:
						$theContainer->UnserialiseData( $theStatement[ kAPI_QUERY_DATA ] );
						switch( $theCondition )
						{
							case kOPERATOR_AND:
							case kOPERATOR_OR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array( '$gte' => $theStatement[ kAPI_QUERY_DATA ] );
								break;
							
							case kOPERATOR_NAND:
							case kOPERATOR_NOR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array( '$lt' => $theStatement[ kAPI_QUERY_DATA ] );
								break;
						}
						break;
						
					case kOPERATOR_IRANGE:
						$list = Array();
						foreach( $theStatement[ kAPI_QUERY_DATA ] as $value )
						{
							$theContainer->UnserialiseData( $value );
							$list[ (double) (string) $value ] = $value;
						}
						ksort( $list );
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
						$list = Array();
						foreach( $theStatement[ kAPI_QUERY_DATA ] as $value )
						{
							$theContainer->UnserialiseData( $value );
							$list[ (double) (string) $value ] = $value;
						}
						ksort( $list );
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
						$keys = array_keys( $theStatement[ kAPI_QUERY_DATA ] );
						foreach( $keys as $key )
							$theContainer->UnserialiseData
								( $theStatement[ kAPI_QUERY_DATA ][ $key ] );
						switch( $theCondition )
						{
							case kOPERATOR_AND:
							case kOPERATOR_OR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array( '$in' => $theStatement[ kAPI_QUERY_DATA ] );
								break;
							
							case kOPERATOR_NAND:
							case kOPERATOR_NOR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array( '$nin' => $theStatement[ kAPI_QUERY_DATA ] );
								break;
						}
						break;
						
					case kOPERATOR_NI:
						$keys = array_keys( $theStatement[ kAPI_QUERY_DATA ] );
						foreach( $keys as $key )
							$theContainer->UnserialiseData
								( $theStatement[ kAPI_QUERY_DATA ][ $key ] );
						switch( $theCondition )
						{
							case kOPERATOR_AND:
							case kOPERATOR_OR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array( '$nin' => $theStatement[ kAPI_QUERY_DATA ] );
								break;
							
							case kOPERATOR_NAND:
							case kOPERATOR_NOR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array( '$in' => $theStatement[ kAPI_QUERY_DATA ] );
								break;
						}
						break;
						
					case kOPERATOR_ALL:
						$keys = array_keys( $theStatement[ kAPI_QUERY_DATA ] );
						foreach( $keys as $key )
							$theContainer->UnserialiseData
								( $theStatement[ kAPI_QUERY_DATA ][ $key ] );
						switch( $theCondition )
						{
							case kOPERATOR_AND:
							case kOPERATOR_OR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array( '$all' => $theStatement[ kAPI_QUERY_DATA ] );
								break;
							
							case kOPERATOR_NAND:
							case kOPERATOR_NOR:
								$statement[ $theStatement[ kAPI_QUERY_SUBJECT ] ]
									= array( '$not'
										=> array( '$all'
													=> $theStatement[ kAPI_QUERY_DATA ] ) );
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

	 

} // class CMongoQuery.


?>
