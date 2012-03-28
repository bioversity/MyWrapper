<?php

/**
 * <i>CQuery</i> class definition.
 *
 * This file contains the class definition of <b>CQuery</b> which represents a query object.
 *
 *	@package	Framework
 *	@subpackage	Persistence
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 12/06/2011
  *				2.00 22/02/2012
*/

/*=======================================================================================
 *																						*
 *										CQuery.php										*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CStatusObject.php" );

/**
 * Types.
 *
 * This include file contains all data type definitions.
 */
require_once( kPATH_LIBRARY_DEFINES."Types.inc.php" );

/**
 * Operators.
 *
 * This include file contains all operator definitions.
 */
require_once( kPATH_LIBRARY_DEFINES."Operators.inc.php" );

/**
 * Local definitions.
 *
 * This include file contains all local definitions to this class.
 */
require_once( kPATH_LIBRARY_SOURCE."CQuery.inc.php" );

/**
 *	Query.
 *
 * This class implements a query.
 *
 * The main goal of this class is to provide a common framework and format to exchange data
 * store queries or filters.
 *
 * The query is an array structured as follows:
 *
 * <ul>
 *	<li><i>index</i>: The index of the root element expresses a boolean condition which
 *		qualifies its content:
 *	 <ul>
 *		<li><i>{@link kOPERATOR_AND kOPERATOR_AND}</i>: AND.
 *		<li><i>{@link kOPERATOR_NAND kOPERATOR_NAND}</i>: Not AND.
 *		<li><i>{@link kOPERATOR_OR kOPERATOR_OR}</i>: OR.
 *		<li><i>{@link kOPERATOR_NOR kOPERATOR_NOR}</i>: Not OR.
 *	 </ul>
 *		The value of this root element is an array that can be of two types:
 *	 <ul>
 *		<li><i>Query statement</i>: A query statement defines a filter structured as
 *			follows:
 *		 <ul>
 *			<li><i>{@link kAPI_QUERY_SUBJECT kAPI_QUERY_SUBJECT}</i>: The subject field. It
 *				refers to the object element that we are filtering.
 *			<li><i>{@link kAPI_QUERY_OPERATOR kAPI_QUERY_OPERATOR}</i>: The filter operator.
 *				This element is required and can take the following values:
 *			 <ul>
 *				<li><i>{@link kOPERATOR_DISABLED kOPERATOR_DISABLED}</i>: Disabled, it means
 *					that the filter is disabled.
 *				<li><i>{@link kOPERATOR_EQUAL kOPERATOR_EQUAL}</i>: Equality (=).
 *				<li><i>{@link kOPERATOR_EQUAL_NOT kOPERATOR_EQUAL_NOT}</i>: Inequality
 *					(!=), negates the {@link kOPERATOR_EQUAL kOPERATOR_EQUAL}
 *					operator.
 *				<li><i>{@link kOPERATOR_LIKE kOPERATOR_LIKE}</i>: Like, it is an accent and
 *					case insensitive equality filter.
 *				<li><i>{@link kOPERATOR_LIKE_NOT kOPERATOR_LIKE_NOT}</i>: The negation of
 *					the {@link kOPERATOR_LIKE LIKE} operator.
 *				<li><i>{@link kOPERATOR_PREFIX kOPERATOR_PREFIX}</i>: Starts with, or prefix
 *					match.
 *				<li><i>{@link kOPERATOR_CONTAINS kOPERATOR_CONTAINS}</i>: Contains, selects
 *					all elements that contain the match string.
 *				<li><i>{@link kOPERATOR_SUFFIX kOPERATOR_SUFFIX}</i>: Ends with, or suffix
 *					match.
 *				<li><i>{@link kOPERATOR_REGEX kOPERATOR_REGEX}</i>: Regular expression.
 *				<li><i>{@link kOPERATOR_LESS kOPERATOR_LESS}</i>: Smaller than (\<).
 *				<li><i>{@link kOPERATOR_LESS_EQUAL kOPERATOR_LESS_EQUAL}</i>: Smaller than
 *					or equal (\<=).
 *				<li><i>{@link kOPERATOR_GREAT kOPERATOR_GREAT}</i>: Greater than (\>).
 *				<li><i>{@link kOPERATOR_GREAT_EQUAL kOPERATOR_GREAT_EQUAL}</i>: Greater than
 *					or equal (\>=).
 *				<li><i>{@link kOPERATOR_IRANGE kOPERATOR_IRANGE}</i>: Range inclusive,
 *					matches \>= value \<=.
 *				<li><i>{@link kOPERATOR_ERANGE kOPERATOR_ERANGE}</i>: Range exclusive,
 *					matches \> value \<.
 *				<li><i>{@link kOPERATOR_NULL kOPERATOR_NULL}</i>: Is <i>NULL</i> or element
 *					is missing.
 *				<li><i>{@link kOPERATOR_NOT_NULL kOPERATOR_NOT_NULL}</i>:Not <i>NULL</i> or
 *					element exists.
 *				<li><i>{@link kOPERATOR_IN kOPERATOR_IN}</i>: In, or belongs to set.
 *				<li><i>{@link kOPERATOR_NI kOPERATOR_NI}</i>: Not in, the negation of
 *					{@link kOPERATOR_IN kOPERATOR_IN}.
 *				<li><i>{@link kOPERATOR_ALL kOPERATOR_ALL}</i>: All, or match the full set.
 *				<li><i>{@link kOPERATOR_NALL kOPERATOR_NALL}</i>: Not all, the negation of
 *					the {@link kOPERATOR_ALL kOPERATOR_ALL} operator.
 *				<li><i>{@link kOPERATOR_EX kOPERATOR_EX}</i>: Expression, indicates a
 *					complex expression.
 *			 </ul>
 *			<li><i>{@link kAPI_QUERY_TYPE kAPI_QUERY_TYPE}</i>: The data type of
 *				the {@link kAPI_QUERY_DATA kAPI_QUERY_DATA} element:
 *			 <ul>
 *				<li><i>{@link kDATA_TYPE_STRING kDATA_TYPE_STRING}</i>: String, we assume
 *					in UTF8 character set.
 *				<li><i>{@link kDATA_TYPE_INT32 kDATA_TYPE_INT32}</i>: 32 bit signed integer.
 *				<li><i>{@link kDATA_TYPE_INT64 kDATA_TYPE_INT64}</i>: 64 bit signed integer.
 *				<li><i>{@link kDATA_TYPE_FLOAT kDATA_TYPE_FLOAT}</i>: Floating point number.
 *				<li><i>{@link kDATA_TYPE_DATE kDATA_TYPE_DATE}</i>: A date.
 *				<li><i>{@link kDATA_TYPE_TIME kDATA_TYPE_TIME}</i>: A date and time.
 *				<li><i>{@link kDATA_TYPE_STAMP kDATA_TYPE_STAMP}</i>: A native timestamp.
 *				<li><i>{@link kDATA_TYPE_BOOLEAN kDATA_TYPE_BOOLEAN}</i>: An on/off switch.
 *				<li><i>{@link kDATA_TYPE_BINARY kDATA_TYPE_BINARY}</i>: A binary string.
 *				<li><i>{@link kDATA_TYPE_ENUM kDATA_TYPE_ENUM}</i>: An enumerated value.
 *				<li><i>{@link kDATA_TYPE_SET kDATA_TYPE_SET}</i>: An enumerated set of
 *					values.
 *			 </ul>
 *			<li><i>{@link kAPI_QUERY_DATA kAPI_QUERY_DATA}</i>: The statement test data.
 *		 </ul>
 *		<li><i>Nested query condition</i>: A nested structure as the current one.
 *	 </ul>
 * </ul>
 *
 *	@package	Framework
 *	@subpackage	Persistence
 */
class CQuery extends CStatusObject
{
		

/*=======================================================================================
 *																						*
 *										MAGIC											*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	__construct																		*
	 *==================================================================================*/

	/**
	 * Instantiate class.
	 *
	 * The object can be constructed from an existing query structure.
	 *
	 * @param mixed					$theQuery			Query data.
	 *
	 * @access public
	 */
	public function __construct( $theQuery = NULL )
	{
		//
		// Handle empty query.
		//
		if( $theQuery === NULL )
			parent::__construct();
		
		//
		// Handle well formed query.
		//
		elseif( is_array( $theQuery )
			 || ($theQuery instanceof ArrayObject) )
		{
			//
			// Instantiate object.
			//
			parent::__construct( (array) $theQuery );
			
			//
			// Set inited status.
			//
			$this->_IsInited( TRUE );
		
		} // Provided query.
		
		//
		// Empty query.
		//
		elseif( ! strlen( $theQuery ) )
			parent::__construct();
		
		//
		// Invalid query.
		//
		else
			throw new CException( "Invalid query: expecting an array",
								  kERROR_INVALID_PARAMETER,
								  kMESSAGE_TYPE_ERROR,
								  array( 'Query' => $theQuery ) );				// !@! ==>

	} // Constructor.

		

/*=======================================================================================
 *																						*
 *								PUBLIC OPERATION INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	AppendStatement																	*
	 *==================================================================================*/

	/**
	 * Append statement.
	 *
	 * This method will append the provided statement to the query, the second parameter
	 * represents the condition.
	 *
	 * Appended statements are merged at the condition level: if the condition exists at any
	 * level, the statement is appended to that condition; if the condition does not exist,
	 * it is created. Obviously, {@link kOPERATOR_AND AND} and {@link kOPERATOR_NAND NAND}
	 * are treated as equivalent, as well as {@link kOPERATOR_OR OR} and
	 * {@link kOPERATOR_NOR NOR}.
	 *
	 * If you provide an object derived from {@link CQuery CQuery} as the statement, the
	 * method will ignore the condition parameter and append the provided query to the
	 * current object.
	 *
	 * @param array					$theStatement		Statement.
	 * @param string				$theCondition		Statement condition.
	 *
	 * @access public
	 *
	 * @throws Exception
	 */
	public function AppendStatement( $theStatement, $theCondition = kOPERATOR_AND )
	{
		//
		// Get statement key.
		//
		$key = key( $theStatement );
		
		//
		// Parse by key.
		//
		switch( $key )
		{
			//
			// Handle nested conditions.
			//
			case kOPERATOR_OR:
			case kOPERATOR_NOR:
			case kOPERATOR_AND:
			case kOPERATOR_NAND:
				//
				// Iterate query conditions.
				//
				foreach( $theStatement as $condition => $statements )
				{
					//
					// Iterate query statements.
					//
					foreach( $statements as $statement )
						$this->AppendStatement( $statement, $condition );
				
				} // Iterating query conditions.
				
				break;
			
			//
			// Handle statements.
			//
			default:
		
				//
				// Point to provided statement.
				// Here we assume it is a statement, not a nested condition.
				//
				if( is_int( key( $theStatement ) ) )
				{
					$element = current( $theStatement );
					$statement = $theStatement;
				}
				else
				{
					$element = $theStatement;
					$statement = array( $theStatement );
				}
				
				//
				// Validate statement.
				//
				$this->_ValidateCondition( $theCondition, $statement, 0 );
				
				//
				// Handle empty query.
				//
				if( ! $this->count() )
					$this->offsetSet( $theCondition, $statement );
				
				//
				// Append to existing query.
				//
				else
				{
					//
					// Get array copy.
					//
					$query = $this->getArrayCopy();
					
					//
					// Append statement.
					//
					$this->_AppendStatement( $query, $theCondition, $element );
					
					//
					// Update object.
					//
					$this->exchangeArray( $query );
				
				} // Append to current query.
				
				break;
			
		} // Parsed by statement key.
	
	} // AppendStatement.

		

/*=======================================================================================
 *																						*
 *								PUBLIC VALIDATION INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Validate																		*
	 *==================================================================================*/

	/**
	 * Validate query.
	 *
	 * This method will check whether the query structure is valid.
	 *
	 * @access public
	 *
	 * @throws Exception
	 */
	public function Validate()
	{
		//
		// Traverse object.
		//
		foreach( $this as $condition => $statements )
			$this->_ValidateCondition( $condition, $statements, 0 );
	
	} // Validate.

	 

/*=======================================================================================
 *																						*
 *									PROTECTED QUERY UTILITIES							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_AppendStatement																*
	 *==================================================================================*/

	/**
	 * Append statement.
	 *
	 * This method will append the provided statement to the current query.
	 *
	 * Statements of the same type, {@link kOPERATOR_AND AND} and
	 * {@link kOPERATOR_NAND NAND},  or {@link kOPERATOR_OR OR} and
	 * {@link kOPERATOR_NOR NOR}, will be added at the same level. If the top level is an
	 * {@link kOPERATOR_OR OR} or {@link kOPERATOR_NOR NOR} and the provided statement is an
	 * {@link kOPERATOR_AND AND} or {@link kOPERATOR_NAND NAND}, the latter will be promoted
	 * to the top level.
	 *
	 * The parameters to this method are:
	 *
	 * <ul>
	 *	<li><b>&$theQuery</b>: Query receiving statement.
	 *	<li><b>$theStatement</b>: Statement to be added.
	 *	<li><b>$theCondition</b>: Boolean statement condition code.
	 * </ul>
	 *
	 * @param reference		   &$theQuery				Query.
	 * @param array				$theStatements			Statement.
	 * @param string			$theCondition			Statement condition.
	 *
	 * @access private
	 */
	protected function _AppendStatement( &$theQuery, $theCondition, $theStatement )
	{
		//
		// Handle same condition.
		//
		if( array_key_exists( $theCondition, $theQuery ) )
			$theQuery[ $theCondition ][] = $theStatement;
		
		//
		// Handle different condition.
		//
		else
		{
			//
			// Get current query top condition.
			//
			$top_condition = key( $theQuery );
			
			//
			// Promote condition.
			//
			if( ( ($top_condition == kOPERATOR_OR)
			   || ($top_condition == kOPERATOR_NOR) )
			 && ( ($theCondition == kOPERATOR_AND)
			   || ($theCondition == kOPERATOR_NAND) ) )
			{
				//
				// Provided condition is AND.
				//
				if( $theCondition == kOPERATOR_AND )
				{
					//
					// Create condition statement.
					//
					$condition = array( $theCondition => array( $theStatement ) );
		
					//
					// Append to provided condition the existing condition.
					//
					$condition[ $theCondition ][] = $theQuery;
				
				} // Provided condition is AND.
				
				//
				// Provided condition is NAND.
				//
				else
				{
					//
					// Append existing OR statement to new AND condition.
					//
					$condition = array( kOPERATOR_AND => array( $theQuery ) );
					
					//
					// Append NAND condition to query.
					//
					$condition[ $theCondition ] = array( $theStatement );
				
				} // Provided condition is NAND.
				
				//
				// Update query.
				//
				$theQuery = $condition;
			
			} // Promoted statement.
			
			//
			// Traverse conditions.
			//
			else
			{
				//
				// Handle same top level condition type.
				//
				if( ( ( ($top_condition == kOPERATOR_AND)
					 || ($top_condition == kOPERATOR_NAND) )
				   && ( ($theCondition == kOPERATOR_AND)
					 || ($theCondition == kOPERATOR_NAND) ) )
				 || ( ( ($top_condition == kOPERATOR_OR)
					 || ($top_condition == kOPERATOR_NOR) )
				   && ( ($theCondition == kOPERATOR_OR)
					 || ($theCondition == kOPERATOR_NOR) ) ) )
					$theQuery[ $theCondition ] = array( $theStatement );
				
				//
				// Append statement in new level.
				// Note: At this point we know that the top condition is
				// an AND or NAND and that the condition to be added is
				// an OR or NOR.
				//
				else
				{
					//
					// Check if we have a top AND condition.
					//
					if( array_key_exists( kOPERATOR_AND, $theQuery ) )
					{
						//
						// Iterate AND statements.
						//
						$keys = array_keys( $theQuery[ kOPERATOR_AND ] );
						foreach( $keys as $key )
						{
							//
							// Match nested condition.
							//
							if( array_key_exists( $theCondition,
												  $theQuery[ kOPERATOR_AND ][ $key ] ) )
							{
								//
								// Append to nested condition.
								//
								$theQuery[ kOPERATOR_AND ][ $key ][ $theCondition ][]
									= $theStatement;
								
								return;												// ==>
							
							} // Matched nested condition.
						
						} // Iterating AND statements.
					
					} // Query has an AND condition.
					
					//
					// Create top AND condition and append statement to it.
					//
					$theQuery[ kOPERATOR_AND ][]
						= array( $theCondition => array( $theStatement ) );
				
				} // Create new level.
			
			} // Condition not promoted.
		
		} // Different condition.
	
	} // _AppendStatement.

	 

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
	 * @param string				$theCondition			Boolean condition.
	 * @param array					$theStatements			Statements list.
	 * @param integer				$theLevel				[PRIVATE] condition level.
	 *
	 * @access private
	 */
	protected function _ValidateCondition( $theCondition, $theStatements, $theLevel )
	{
		//
		// Check condition.
		//
		switch( $theCondition )
		{
			case kOPERATOR_AND:
			case kOPERATOR_NAND:
			case kOPERATOR_OR:
			case kOPERATOR_NOR:
				
				//
				// Check statements list type.
				//
				if( ! is_array( $theStatements ) )
					throw new CException
						( "Invalid query: condition has no statements",
						  kERROR_OPTION_MISSING,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Statements' => $theStatements ) );			// !@! ==>
				
				//
				// Validate statements.
				//
				foreach( $theStatements as $key => $statement )
					$this->_ValidateStatement( $statement, $theLevel );
				
				
				break;
			
			default:
				
				//
				// Unsupported condition.
				//
				throw new CException
					( "Invalid query: unsupported condition",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_WARNING,
					  array( 'Condition' => $theCondition ) );					// !@! ==>
		
		} // Checking condition.
	
	} // _ValidateCondition.

	 
	/*===================================================================================
	 *	_ValidateStatement																*
	 *==================================================================================*/

	/**
	 * Validate statement.
	 *
	 * This method expects a statement as its argument, it will check if it is a valid
	 * statement and check if all required elements are there.
	 *
	 * @param array					$theStatement			Statement.
	 * @param integer				$theLevel				[PRIVATE] condition level.
	 *
	 * @access private
	 */
	protected function _ValidateStatement( $theStatement, $theLevel )
	{
		//
		// Check statement data type.
		//
		if( ! is_array( $theStatement ) )
			throw new CException
				( "Invalid query statement",
				  kERROR_INVALID_STATE,
				  kMESSAGE_TYPE_ERROR,
				  array( 'Statement' => $theStatement ) );						// !@! ==>

		//
		// Parse by statement index.
		//
		switch( $condition = key( $theStatement ) )
		{
			case kOPERATOR_AND:
			case kOPERATOR_NAND:
			case kOPERATOR_OR:
			case kOPERATOR_NOR:
				$this->_ValidateCondition( $condition, $theStatement, $theLevel + 1 );
				break;
			
			default:
			
				//
				// Check statement operator.
				//
				if( array_key_exists( kAPI_QUERY_OPERATOR, $theStatement ) )
				{
					//
					// Parse by operator.
					//
					switch( $theStatement[ kAPI_QUERY_OPERATOR ] )
					{
						case kOPERATOR_DISABLED:
							break;
					
						case kOPERATOR_EQUAL:
						case kOPERATOR_EQUAL_NOT:
						case kOPERATOR_LIKE:
						case kOPERATOR_LIKE_NOT:
						case kOPERATOR_PREFIX:
						case kOPERATOR_CONTAINS:
						case kOPERATOR_SUFFIX:
						case kOPERATOR_REGEX:
						case kOPERATOR_LESS:
						case kOPERATOR_LESS_EQUAL:
						case kOPERATOR_GREAT:
						case kOPERATOR_GREAT_EQUAL:
						case kOPERATOR_IRANGE:
						case kOPERATOR_ERANGE:
						case kOPERATOR_IN:
						case kOPERATOR_NI:
						case kOPERATOR_ALL:
						case kOPERATOR_NALL:
						case kOPERATOR_EX:
							if( ! array_key_exists( kAPI_QUERY_TYPE, $theStatement ) )
								throw new CException
									( "Invalid query: missing filter match data type",
									  kERROR_OPTION_MISSING,
									  kMESSAGE_TYPE_ERROR,
									  array( 'Element' => kAPI_QUERY_TYPE,
											 'Statement' => $theStatement ) );	// !@! ==>
							if( ! array_key_exists( kAPI_QUERY_DATA, $theStatement ) )
								throw new CException
									( "Invalid query: missing filter data",
									  kERROR_OPTION_MISSING,
									  kMESSAGE_TYPE_ERROR,
									  array( 'Element' => kAPI_QUERY_DATA,
											 'Statement' => $theStatement ) );	// !@! ==>
						
						case kOPERATOR_NULL:
						case kOPERATOR_NOT_NULL:
							if( ! array_key_exists( kAPI_QUERY_SUBJECT, $theStatement ) )
								throw new CException
									( "Invalid query: missing subject in statement",
									  kERROR_OPTION_MISSING,
									  kMESSAGE_TYPE_ERROR,
									  array( 'Element' => kAPI_QUERY_SUBJECT,
											 'Statement' => $theStatement ) );	// !@! ==>
							break;
						
						default:
							throw new CException
								( "Invalid query: unsupported operator",
								  kERROR_UNSUPPORTED,
								  kMESSAGE_TYPE_ERROR,
								  array( 'Operator'
										=> $theStatement
											[ kAPI_QUERY_OPERATOR ]  ) );		// !@! ==>
					
					} // Parsing by operator.
				
				} // Has operator.
				
				//
				// Handle missing statement.
				//
				else
					throw new CException
						( "Invalid query: missing operator in statement",
						  kERROR_OPTION_MISSING,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Element' => kAPI_QUERY_OPERATOR,
								 'Statement' => $theStatement ) );				// !@! ==>
				
				break;
		
		} // Parsing statement index.
		
	} // _ValidateStatement.

	 

} // class CQuery.


?>
