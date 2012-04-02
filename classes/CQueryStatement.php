<?php

/**
 * <i>CQueryStatement</i> class definition.
 *
 * This file contains the class definition of <b>CQueryStatement</b> which represents a
 * {@link CQuery query} statement object.
 *
 *	@package	MyWrapper
 *	@subpackage	Persistence
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 02/04/2012
*/

/*=======================================================================================
 *																						*
 *									CQueryStatement.php									*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CArrayObject.php" );

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
 * Query definitions.
 *
 * This include file contains the query class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CQuery.inc.php" );

/**
 *	Query statement.
 *
 * This class implements a query statement, such as those that populate the
 * {@link CQuery query} class. This class concentrates on building and managing these
 * statements, which are structured as follows:
 *
 * <ul>
 *	<li><i>{@link kAPI_QUERY_SUBJECT kAPI_QUERY_SUBJECT}</i>: The query subject. It refers
 *		to the object element that we are considering in the query.
 *	<li><i>{@link kAPI_QUERY_OPERATOR kAPI_QUERY_OPERATOR}</i>: The query predicate. This
 *		element represents the predicate or comparaison operator, it can take the following
 *		values:
 *	 <ul>
 *		<li><i>{@link kOPERATOR_DISABLED kOPERATOR_DISABLED}</i>: Disabled, it means that
 *			the current statement is disabled.
 *		<li><i>{@link kOPERATOR_EQUAL kOPERATOR_EQUAL}</i>: Equality (=), this operator
 *			implies that the statement has also an {@link kAPI_QUERY_DATA object}.
 *		<li><i>{@link kOPERATOR_EQUAL_NOT kOPERATOR_EQUAL_NOT}</i>: Inequality
 *			(!=), negates the {@link kOPERATOR_EQUAL kOPERATOR_EQUAL}
 *			operator; this operator implies that the statement has also an
 *			{@link kAPI_QUERY_DATA object}.
 *		<li><i>{@link kOPERATOR_LIKE kOPERATOR_LIKE}</i>: Like, it is an accent and
 *			case insensitive equality filter, this operator implies that the statement has
 *			also an {@link kAPI_QUERY_DATA object}.
 *		<li><i>{@link kOPERATOR_LIKE_NOT kOPERATOR_LIKE_NOT}</i>: The negation of
 *			the {@link kOPERATOR_LIKE LIKE} operator, this operator implies that the
 *			statement has also an {@link kAPI_QUERY_DATA object}.
 *		<li><i>{@link kOPERATOR_PREFIX kOPERATOR_PREFIX}</i>: Starts with, or prefix
 *			match, this operator implies that the statement has also an
 *			{@link kAPI_QUERY_DATA object}.
 *		<li><i>{@link kOPERATOR_CONTAINS kOPERATOR_CONTAINS}</i>: Contains, selects
 *			all elements that contain the match string, this operator implies that the
 *			statement has also an {@link kAPI_QUERY_DATA object}.
 *		<li><i>{@link kOPERATOR_SUFFIX kOPERATOR_SUFFIX}</i>: Ends with, or suffix
 *			match, this operator implies that the statement has also an
 *			{@link kAPI_QUERY_DATA object}.
 *		<li><i>{@link kOPERATOR_REGEX kOPERATOR_REGEX}</i>: Regular expression, this
 *			operator implies that the statement has also an {@link kAPI_QUERY_DATA object}.
 *		<li><i>{@link kOPERATOR_LESS kOPERATOR_LESS}</i>: Smaller than (\<), this operator
 *			implies that the statement has also an {@link kAPI_QUERY_DATA object}.
 *		<li><i>{@link kOPERATOR_LESS_EQUAL kOPERATOR_LESS_EQUAL}</i>: Smaller than
 *			or equal (\<=), this operator implies that the statement has also an
 *			{@link kAPI_QUERY_DATA object}.
 *		<li><i>{@link kOPERATOR_GREAT kOPERATOR_GREAT}</i>: Greater than (\>), this operator
 *			implies that the statement has also an {@link kAPI_QUERY_DATA object}.
 *		<li><i>{@link kOPERATOR_GREAT_EQUAL kOPERATOR_GREAT_EQUAL}</i>: Greater than
 *			or equal (\>=), this operator implies that the statement has also an
 *			{@link kAPI_QUERY_DATA object}.
 *		<li><i>{@link kOPERATOR_IRANGE kOPERATOR_IRANGE}</i>: Range inclusive,
 *			matches \>= value \<=; this operator implies that the statement has also an
 *			{@link kAPI_QUERY_DATA object} which should contain the two range values.
 *		<li><i>{@link kOPERATOR_ERANGE kOPERATOR_ERANGE}</i>: Range exclusive,
 *			matches \> value \<, this operator implies that the statement has also an
 *			{@link kAPI_QUERY_DATA object} which should contain the two range values.
 *		<li><i>{@link kOPERATOR_NULL kOPERATOR_NULL}</i>: Is <i>NULL</i> or element
 *			is missing.
 *		<li><i>{@link kOPERATOR_NOT_NULL kOPERATOR_NOT_NULL}</i>:Not <i>NULL</i> or
 *			element exists.
 *		<li><i>{@link kOPERATOR_IN kOPERATOR_IN}</i>: In, or belongs to set, this operator
 *			implies that the statement has also an {@link kAPI_QUERY_DATA object} that will
 *			contain the list of choices.
 *		<li><i>{@link kOPERATOR_NI kOPERATOR_NI}</i>: Not in, the negation of
 *			{@link kOPERATOR_IN kOPERATOR_IN}, this operator implies that the statement has
 *			also an {@link kAPI_QUERY_DATA object} which contains the list of choices.
 *		<li><i>{@link kOPERATOR_ALL kOPERATOR_ALL}</i>: All, or match the full set, this
 *			operator implies that the statement has also an {@link kAPI_QUERY_DATA object}
 *			which will contain the set.
 *		<li><i>{@link kOPERATOR_NALL kOPERATOR_NALL}</i>: Not all, the negation of
 *			the {@link kOPERATOR_ALL kOPERATOR_ALL} operator, this operator implies that the
 *			statement has also an {@link kAPI_QUERY_DATA object} which contains the set.
 *		<li><i>{@link kOPERATOR_EX kOPERATOR_EX}</i>: Expression, indicates a
 *			complex expression, this operator implies that the statement has also an
 *			{@link kAPI_QUERY_DATA object} which will contain the expression.
 *	 </ul>
 *	<li><i>{@link kAPI_QUERY_TYPE kAPI_QUERY_TYPE}</i>: The statement object type, or data
 *		type of the {@link kAPI_QUERY_DATA kAPI_QUERY_DATA} element, if the latter is
 *		required:
 *	 <ul>
 *		<li><i>{@link kDATA_TYPE_STRING kDATA_TYPE_STRING}</i>: String, we assume
 *			in UTF8 character set.
 *		<li><i>{@link kDATA_TYPE_INT32 kDATA_TYPE_INT32}</i>: 32 bit signed integer.
 *		<li><i>{@link kDATA_TYPE_INT64 kDATA_TYPE_INT64}</i>: 64 bit signed integer.
 *		<li><i>{@link kDATA_TYPE_FLOAT kDATA_TYPE_FLOAT}</i>: Floating point number.
 *		<li><i>{@link kDATA_TYPE_DATE kDATA_TYPE_DATE}</i>: A date.
 *		<li><i>{@link kDATA_TYPE_TIME kDATA_TYPE_TIME}</i>: A date and time.
 *		<li><i>{@link kDATA_TYPE_STAMP kDATA_TYPE_STAMP}</i>: A native timestamp.
 *		<li><i>{@link kDATA_TYPE_BOOLEAN kDATA_TYPE_BOOLEAN}</i>: An on/off switch.
 *		<li><i>{@link kDATA_TYPE_BINARY kDATA_TYPE_BINARY}</i>: A binary string.
 *	 </ul>
 *	<li><i>{@link kAPI_QUERY_DATA kAPI_QUERY_DATA}</i>: The statement object or test data.
 * </ul>
 *
 * The main goal of this class is to provide an interface that may ease the construction of
 * complex {@link CQuery queries} by providing specialised methods for building statements
 * that can then safely be {@link CQuery::AppendStatement() appended} to query
 * {@link CQuery objects}.
 *
 *	@package	MyWrapper
 *	@subpackage	Persistence
 */
class CQueryStatement extends CArrayObject
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
	 * The constructor can be used to instantiate a statement either by providing an
	 * existing statement structure, or by providing the statement elements:
	 *
	 * <ul>
	 *	<li><b>$theSubject</b>: The statement subject:
	 *	 <ul>
	 *		<li><i>array</i> or <i>ArrayObject</i>: Structures are interpreted as built
	 *			statements, so the method will scan the structure and load the corresponding
	 *			elements.
	 *		<li><i>string</i>: Any other type will be converted to a string and interpreted
	 *			as the statement subject, or data element key.
	 *	 </ul>
	 *	<li><b>$thePredicate</b>: The statement operator or predicate:
	 *	 <ul>
	 *		<li><i>{@link kOPERATOR_DISABLED kOPERATOR_DISABLED}</i>: Disabled, it means
	 *			that the current statement is disabled; the remaining parameters are
	 *			ignored.
	 *		<li><i>{@link kOPERATOR_EQUAL kOPERATOR_EQUAL}</i>: Equality (=), this operator
	 *			implies that the method expects the next two parameters.
	 *		<li><i>{@link kOPERATOR_EQUAL_NOT kOPERATOR_EQUAL_NOT}</i>: Inequality
	 *			(!=), negates the {@link kOPERATOR_EQUAL kOPERATOR_EQUAL}
	 *			operator; this operator implies that the method expects the next two
	 *			parameters.
	 *		<li><i>{@link kOPERATOR_LIKE kOPERATOR_LIKE}</i>: Like, it is an accent and
	 *			case insensitive equality filter, this operator implies that the method
	 *			expects the next two parameters.
	 *		<li><i>{@link kOPERATOR_LIKE_NOT kOPERATOR_LIKE_NOT}</i>: The negation of
	 *			the {@link kOPERATOR_LIKE LIKE} operator, this operator implies that the
	 *			method expects the next two parameters.
	 *		<li><i>{@link kOPERATOR_PREFIX kOPERATOR_PREFIX}</i>: Starts with, or prefix
	 *			match, this operator implies that the method expects the next two
	 *			parameters.
	 *		<li><i>{@link kOPERATOR_CONTAINS kOPERATOR_CONTAINS}</i>: Contains, selects
	 *			all elements that contain the match string, this operator implies that the
	 *			method expects the next two parameters.
	 *		<li><i>{@link kOPERATOR_SUFFIX kOPERATOR_SUFFIX}</i>: Ends with, or suffix
	 *			match, this operator implies that the method expects the next two
	 *			parameters.
	 *		<li><i>{@link kOPERATOR_REGEX kOPERATOR_REGEX}</i>: Regular expression, this
	 *			operator implies that the method expects the next two parameters.
	 *		<li><i>{@link kOPERATOR_LESS kOPERATOR_LESS}</i>: Smaller than (\<), this operator
	 *			implies that the method expects the next two parameters.
	 *		<li><i>{@link kOPERATOR_LESS_EQUAL kOPERATOR_LESS_EQUAL}</i>: Smaller than
	 *			or equal (\<=), this operator implies that the method expects the next two
	 *			parameters.
	 *		<li><i>{@link kOPERATOR_GREAT kOPERATOR_GREAT}</i>: Greater than (\>), this operator
	 *			implies that the method expects the next two parameters.
	 *		<li><i>{@link kOPERATOR_GREAT_EQUAL kOPERATOR_GREAT_EQUAL}</i>: Greater than
	 *			or equal (\>=), this operator implies that the method expects the next two
	 *			parameters.
	 *		<li><i>{@link kOPERATOR_IRANGE kOPERATOR_IRANGE}</i>: Range inclusive,
	 *			matches \>= value \<=; this operator implies that the method expects the
	 *			next three parameters.
	 *		<li><i>{@link kOPERATOR_ERANGE kOPERATOR_ERANGE}</i>: Range exclusive,
	 *			matches \> value \<, this operator implies that the method expects the next
	 *			three parameters.
	 *		<li><i>{@link kOPERATOR_NULL kOPERATOR_NULL}</i>: Is <i>NULL</i> or element
	 *			is missing.
	 *		<li><i>{@link kOPERATOR_NOT_NULL kOPERATOR_NOT_NULL}</i>:Not <i>NULL</i> or
	 *			element exists.
	 *		<li><i>{@link kOPERATOR_IN kOPERATOR_IN}</i>: In, or belongs to set, this operator
	 *			implies that the method expects the next two parameters.
	 *		<li><i>{@link kOPERATOR_NI kOPERATOR_NI}</i>: Not in, the negation of
	 *			{@link kOPERATOR_IN kOPERATOR_IN}, this operator implies that the method
	 *			expects the next two parameters.
	 *		<li><i>{@link kOPERATOR_ALL kOPERATOR_ALL}</i>: All, or match the full set, this
	 *			operator implies that the method expects the next two parameters.
	 *		<li><i>{@link kOPERATOR_NALL kOPERATOR_NALL}</i>: Not all, the negation of
	 *			the {@link kOPERATOR_ALL kOPERATOR_ALL} operator, this operator implies that
	 *			the method expects the next two parameters.
	 *		<li><i>{@link kOPERATOR_EX kOPERATOR_EX}</i>: Expression, indicates a
	 *			complex expression, this operator implies that the method expects the next
	 *			two parameters.
	 *	 </ul>
	 *	<li><b>$theType</b>: The statement object data type, this qualifies all remaining
	 *		parameters. The allowed values are:
	 *	 <ul>
	 *		<li><i>{@link kDATA_TYPE_STRING kDATA_TYPE_STRING}</i>: String, we assume in
	 *			UTF8 character set, the string is expected in the next parameter.
	 *		<li><i>{@link kDATA_TYPE_INT32 kDATA_TYPE_INT32}</i>: 32 bit signed integer, the
	 *			number is expected in the next parameter, either as an integer, float or
	 *			string; once received, it will be converted to a
	 *			{@link CDataTypeInt32 CDataTypeInt32} object.
	 *		<li><i>{@link kDATA_TYPE_INT64 kDATA_TYPE_INT64}</i>: 64 bit signed integer, the
	 *			number is expected in the next parameter, either as an integer, float or
	 *			string; once received, it will be converted to a
	 *			{@link CDataTypeInt64 CDataTypeInt64} object.
	 *		<li><i>{@link kDATA_TYPE_FLOAT kDATA_TYPE_FLOAT}</i>: Floating point number, the
	 *			number is expected in the next parameter, either as an integer, float or
	 *			string.
	 *		<li><i>{@link kDATA_TYPE_DATE kDATA_TYPE_DATE}</i>: A string date, it is treated
	 *			as a string date with a YYYYMMDD format in which month and day may be
	 *			omitted.
	 *		<li><i>{@link kDATA_TYPE_TIME kDATA_TYPE_TIME}</i>: A string time, it is treated
	 *			as a string time with a YYYY-MM-DD HH:MM:SS format in which all elements are
	 *			required; this element will be converted to a
	 *			{@link kDATA_TYPE_STAMP kDATA_TYPE_STAMP} data type.
	 *		<li><i>{@link kDATA_TYPE_STAMP kDATA_TYPE_STAMP}</i>: A timestamp, optionally
	 *			including microseconds, elements of this type will be converted to
	 *			{@link CDataTypeStamp CDataTypeStamp} objects.
	 *		<li><i>{@link kDATA_TYPE_BOOLEAN kDATA_TYPE_BOOLEAN}</i>: An on/off switch, it
	 *			will be converted to a 1/0 pair.
	 *		<li><i>{@link kDATA_TYPE_BINARY kDATA_TYPE_BINARY}</i>: A binary string, the
	 *			string will be converted to a {@link CDataTypeBinary CDataTypeBinary}
	 *			object.
	 *	 </ul>
	 *	<li><b>$theObject</b>: The statement object data, it should reflect the data type
	 *		provided in the previous parameter.
	 *	<li><b>$theRange</b>: The statement range end or second element. This value must
	 *		also reflect the data type provided in the previous parameter and will be
	 *		automatically set if you provided a range and forgot to set it.
	 * </ul>
	 *
	 * @param mixed					$theSubject			Statement subject.
	 * @param string				$thePredicate		Statement predicate.
	 * @param string				$theType			Statement object data type.
	 * @param mixed					$theObject			Statement object or first range.
	 * @param mixed					$theRange			Statement second range.
	 *
	 * @access public
	 */
	public function __construct( $theSubject = NULL,
								 $thePredicate = NULL,
								 $theType = NULL,
								 $theObject = NULL,
								 $theRange = NULL )
	{
		//
		// Empty statement.
		//
		if( $theSubject === NULL )
			parent::__construct();
		
		//
		// Handle provided statement.
		//
		elseif( is_array( $theSubject )
			 || ($theSubject instanceof ArrayObject) )
				parent::__construct( (array) $theSubject );
		
		//
		// Build with elements.
		//
		else
		{
			//
			// Set subject.
			//
			if( $theSubject !== NULL )
				$this->Subject( $theSubject );
			else
				throw new CException( "Missing statement subject",
									  kERROR_OPTION_MISSING,
									  kMESSAGE_TYPE_ERROR ) );					// !@! ==>
			
			//
			// Set predicate.
			//
			if( $thePredicate !== NULL )
				$this->Predicate( $thePredicate );
			else
				throw new CException( "Missing statement predicate",
									  kERROR_OPTION_MISSING,
									  kMESSAGE_TYPE_ERROR ) );					// !@! ==>
			
			//
			// Parse by predicate.
			//
			switch( $this->Predicate() )
			{
				case kOPERATOR_DISABLED:
					if( $theType !== NULL )
						$this->Type( $theType );
					if( $theRange !== NULL )
						$this->Range( $theObject, $theRange );
					elseif( $theObject !== NULL )
						$this->Object( $theObject );
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
				case kOPERATOR_EX:
					if( $theType !== NULL )
						$this->Type( $theType );
					else
						throw new CException( "Missing statement data type",
											  kERROR_OPTION_MISSING,
											  kMESSAGE_TYPE_ERROR ) );			// !@! ==>
					if( $theObject !== NULL )
						$this->Object( $theObject );
					else
						throw new CException( "Missing statement object",
											  kERROR_OPTION_MISSING,
											  kMESSAGE_TYPE_ERROR ) );			// !@! ==>
					break;

				case kOPERATOR_IN:
				case kOPERATOR_NI:
				case kOPERATOR_ALL:
				case kOPERATOR_NALL:
					if( $theType !== NULL )
						$this->Type( $theType );
					else
						throw new CException( "Missing statement data type",
											  kERROR_OPTION_MISSING,
											  kMESSAGE_TYPE_ERROR ) );			// !@! ==>
					if( $theObject !== NULL )
					{
						if( (! is_array( $theObject ))
						 && (! $theObject instanceof ArrayObject) )
							$theObject = array( $theObject );
						$this->Object( $theObject );
					}
					else
						throw new CException( "Missing statement object",
											  kERROR_OPTION_MISSING,
											  kMESSAGE_TYPE_ERROR ) );			// !@! ==>
					break;

				case kOPERATOR_IRANGE:
				case kOPERATOR_ERANGE:
					if( $theType !== NULL )
						$this->Type( $theType );
					else
						throw new CException( "Missing statement data type",
											  kERROR_OPTION_MISSING,
											  kMESSAGE_TYPE_ERROR ) );			// !@! ==>
					if( $theObject !== NULL )
					{
						if( $theRange === NULL )
							$theRange = $theObject;
						$this->Range( $theObject, $theRange );
					}
					else
						throw new CException( "Missing statement object",
											  kERROR_OPTION_MISSING,
											  kMESSAGE_TYPE_ERROR ) );			// !@! ==>
					break;
				
				default:
					throw new CException
						( "Unsupported operator",
						  kERROR_INVALID_PARAMETER,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Operator' => $thePredicate ) );				// !@! ==>
			}
		
		} // Provided statement elements.

	} // Constructor.

		

/*=======================================================================================
 *																						*
 *								PUBLIC MEMBER INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Subject																			*
	 *==================================================================================*/

	/**
	 * Manage subject.
	 *
	 * This method can be used to manage the query {@link kAPI_QUERY_SUBJECT subject}, it
	 * accepts a parameter which represents either the statement subject or the requested
	 * operation, depending on its value:
	 *
	 * <ul>
	 *	<li><i>NULL</i>: Return the current value.
	 *	<li><i>FALSE</i>: Delete the current value.
	 *	<li><i>other</i>: Set the value with the provided parameter.
	 * </ul>
	 *
	 * The second parameter is a boolean which if <i>TRUE</i> will return the <i>old</i>
	 * value when replacing values; if <i>FALSE</i>, it will return the currently set value.
	 *
	 * @param string				$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses _ManageOffset()
	 *
	 * @see kAPI_QUERY_SUBJECT
	 */
	public function Subject( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kAPI_QUERY_SUBJECT, $theValue, $getOld );		// ==>

	} // Subject.

	 
	/*===================================================================================
	 *	Object																			*
	 *==================================================================================*/

	/**
	 * Manage object.
	 *
	 * This method can be used to manage the query {@link kAPI_QUERY_DATA object} or match
	 * data, it accepts a parameter which represents either the statement object or the
	 * requested operation, depending on its value:
	 *
	 * <ul>
	 *	<li><i>NULL</i>: Return the current value.
	 *	<li><i>FALSE</i>: Delete the current value.
	 *	<li><i>other</i>: Set the value with the provided parameter.
	 * </ul>
	 *
	 * The second parameter is a boolean which if <i>TRUE</i> will return the <i>old</i>
	 * value when replacing values; if <i>FALSE</i>, it will return the currently set value.
	 *
	 * @param string				$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses _ManageOffset()
	 *
	 * @see kAPI_QUERY_DATA
	 */
	public function Object( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kAPI_QUERY_DATA, $theValue, $getOld );			// ==>

	} // Object.

	 
	/*===================================================================================
	 *	Predicate																		*
	 *==================================================================================*/

	/**
	 * Manage predicate.
	 *
	 * This method can be used to manage the query {@link kAPI_QUERY_OPERATOR operator} or
	 * predicate, it accepts a parameter which represents either the statement predicate or
	 * the requested operation, depending on its value:
	 *
	 * <ul>
	 *	<li><i>NULL</i>: Return the current value.
	 *	<li><i>FALSE</i>: Delete the current value.
	 *	<li><i>other</i>: Set the value with the provided parameter:
	 *	 <ul>
	 *		<li><i>{@link kOPERATOR_DISABLED kOPERATOR_DISABLED}</i>: Disabled, it means
	 *			that the current statement is disabled.
	 *		<li><i>{@link kOPERATOR_EQUAL kOPERATOR_EQUAL}</i>: Equality (=).
	 *		<li><i>{@link kOPERATOR_EQUAL_NOT kOPERATOR_EQUAL_NOT}</i>: Inequality
	 *			(!=), negates the {@link kOPERATOR_EQUAL kOPERATOR_EQUAL}
	 *			operator.
	 *		<li><i>{@link kOPERATOR_LIKE kOPERATOR_LIKE}</i>: Like, it is an accent and
	 *			case insensitive equality filter.
	 *		<li><i>{@link kOPERATOR_LIKE_NOT kOPERATOR_LIKE_NOT}</i>: The negation of
	 *			the {@link kOPERATOR_LIKE LIKE} operator.
	 *		<li><i>{@link kOPERATOR_PREFIX kOPERATOR_PREFIX}</i>: Starts with, or prefix
	 *			match.
	 *		<li><i>{@link kOPERATOR_CONTAINS kOPERATOR_CONTAINS}</i>: Contains, selects
	 *			all elements that contain the match string.
	 *		<li><i>{@link kOPERATOR_SUFFIX kOPERATOR_SUFFIX}</i>: Ends with, or suffix
	 *			match.
	 *		<li><i>{@link kOPERATOR_REGEX kOPERATOR_REGEX}</i>: Regular expression.
	 *		<li><i>{@link kOPERATOR_LESS kOPERATOR_LESS}</i>: Smaller than (\<).
	 *		<li><i>{@link kOPERATOR_LESS_EQUAL kOPERATOR_LESS_EQUAL}</i>: Smaller than
	 *			or equal (\<=).
	 *		<li><i>{@link kOPERATOR_GREAT kOPERATOR_GREAT}</i>: Greater than (\>).
	 *		<li><i>{@link kOPERATOR_GREAT_EQUAL kOPERATOR_GREAT_EQUAL}</i>: Greater than
	 *			or equal (\>=).
	 *		<li><i>{@link kOPERATOR_IRANGE kOPERATOR_IRANGE}</i>: Range inclusive,
	 *			matches \>= value \<=.
	 *		<li><i>{@link kOPERATOR_ERANGE kOPERATOR_ERANGE}</i>: Range exclusive,
	 *			matches \> value \<.
	 *		<li><i>{@link kOPERATOR_NULL kOPERATOR_NULL}</i>: Is <i>NULL</i> or element
	 *			is missing.
	 *		<li><i>{@link kOPERATOR_NOT_NULL kOPERATOR_NOT_NULL}</i>:Not <i>NULL</i> or
	 *			element exists.
	 *		<li><i>{@link kOPERATOR_IN kOPERATOR_IN}</i>: In, or belongs to set.
	 *		<li><i>{@link kOPERATOR_NI kOPERATOR_NI}</i>: Not in, the negation of
	 *			{@link kOPERATOR_IN kOPERATOR_IN}.
	 *		<li><i>{@link kOPERATOR_ALL kOPERATOR_ALL}</i>: All, or match the full set.
	 *		<li><i>{@link kOPERATOR_NALL kOPERATOR_NALL}</i>: Not all, the negation of
	 *			the {@link kOPERATOR_ALL kOPERATOR_ALL} operator.
	 *		<li><i>{@link kOPERATOR_EX kOPERATOR_EX}</i>: Expression, indicates a
	 *			complex expression.
	 *	 </ul>
	 *		If the provided value does not match any of the above, the method will raise an
	 *		exception.
	 * </ul>
	 *
	 * The second parameter is a boolean which if <i>TRUE</i> will return the <i>old</i>
	 * value when replacing values; if <i>FALSE</i>, it will return the currently set value.
	 *
	 * @param string				$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses _ManageOffset()
	 *
	 * @see kAPI_QUERY_OPERATOR
	 */
	public function Predicate( $theValue = NULL, $getOld = FALSE )
	{
		//
		// Check predicate.
		//
		if( ($theValue !== NULL)
		 && ($theValue !== FALSE) )
		{
			switch( (string) $theValue )
			{
				case kOPERATOR_DISABLED:
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
				case kOPERATOR_EX:
				case kOPERATOR_IN:
				case kOPERATOR_NI:
				case kOPERATOR_ALL:
				case kOPERATOR_NALL:
				case kOPERATOR_IRANGE:
				case kOPERATOR_ERANGE:
					break;
				
				default:
					throw new CException
						( "Unsupported operator",
						  kERROR_INVALID_PARAMETER,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Operator' => $theValue ) );					// !@! ==>
			}
		}
		
		return $this->_ManageOffset( kAPI_QUERY_OPERATOR, $theValue, $getOld );		// ==>

	} // Predicate.

	 
	/*===================================================================================
	 *	Type																			*
	 *==================================================================================*/

	/**
	 * Manage data type.
	 *
	 * This method can be used to manage the query data {@link kAPI_QUERY_TYPE type} or
	 * {@link Object() object} data type, it accepts a parameter which represents either the
	 * data type in which the {@link Object() object} is expressed, or the requested
	 * operation, depending on its value:
	 *
	 * <ul>
	 *	<li><i>NULL</i>: Return the current value.
	 *	<li><i>FALSE</i>: Delete the current value.
	 *	<li><i>other</i>: Set the value with the provided parameter:
	 *	 <ul>
	 *		<li><i>{@link kDATA_TYPE_STRING kDATA_TYPE_STRING}</i>: String, we assume in
	 *			UTF8 character set.
	 *		<li><i>{@link kDATA_TYPE_INT32 kDATA_TYPE_INT32}</i>: 32 bit signed integer.
	 *		<li><i>{@link kDATA_TYPE_INT64 kDATA_TYPE_INT64}</i>: 64 bit signed integer.
	 *		<li><i>{@link kDATA_TYPE_FLOAT kDATA_TYPE_FLOAT}</i>: Floating point number.
	 *		<li><i>{@link kDATA_TYPE_DATE kDATA_TYPE_DATE}</i>: A string date, it means a
	 *			string date with a YYYYMMDD format in which month and day may be omitted.
	 *		<li><i>{@link kDATA_TYPE_TIME kDATA_TYPE_TIME}</i>: A string time, it is treated
	 *			as a string time with a YYYY-MM-DD HH:MM:SS format in which all elements are
	 *			required.
	 *		<li><i>{@link kDATA_TYPE_STAMP kDATA_TYPE_STAMP}</i>: A timestamp, optionally
	 *			including microseconds.
	 *		<li><i>{@link kDATA_TYPE_BOOLEAN kDATA_TYPE_BOOLEAN}</i>: An on/off switch.
	 *		<li><i>{@link kDATA_TYPE_BINARY kDATA_TYPE_BINARY}</i>: A binary string.
	 *	 </ul>
	 *		If the provided value does not match any of the above, the method will raise an
	 *		exception.
	 * </ul>
	 *
	 * The second parameter is a boolean which if <i>TRUE</i> will return the <i>old</i>
	 * value when replacing values; if <i>FALSE</i>, it will return the currently set value.
	 *
	 * @param string				$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses _ManageOffset()
	 *
	 * @see kAPI_QUERY_TYPE
	 */
	public function Type( $theValue = NULL, $getOld = FALSE )
	{
		//
		// Check predicate.
		//
		if( ($theValue !== NULL)
		 && ($theValue !== FALSE) )
		{
			switch( (string) $theValue )
			{
				case kDATA_TYPE_STRING:
				case kDATA_TYPE_INT32:
				case kDATA_TYPE_INT64:
				case kDATA_TYPE_FLOAT:
				case kDATA_TYPE_DATE:
				case kDATA_TYPE_TIME:
				case kDATA_TYPE_STAMP:
				case kDATA_TYPE_BOOLEAN:
				case kDATA_TYPE_BINARY:
					break;
				
				default:
					throw new CException
						( "Unsupported data type",
						  kERROR_INVALID_PARAMETER,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Type' => $theValue ) );						// !@! ==>
			}
		}
		
		return $this->_ManageOffset( kAPI_QUERY_TYPE, $theValue, $getOld );			// ==>

	} // Type.

	 
	/*===================================================================================
	 *	Range																			*
	 *==================================================================================*/

	/**
	 * Manage range.
	 *
	 * This method can be used to manage the query {@link kAPI_QUERY_DATA object} if it is
	 * in the form of a range. This method will only allow you to set the range, to retrieve
	 * or delete the range you must use the {@link Object() Object} method.
	 *
	 * The method accepts the following parameters:
	 *
	 * <ul>
	 *	<li><b>$theBound1</b>: First range bound.
	 *	<li><b>$theBound2</b>: Second range bound.
	 *	<li><b>$getOld</b>: If <i>TRUE</i> will return the <i>old</i> value when replacing
	 *		values; if <i>FALSE</i>, it will return the currently set value.
	 * </ul>
	 *
	 * @param mixed					$theBound1			First bound.
	 * @param mixed					$theBound2			Second bound.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses _ManageOffset()
	 *
	 * @see kAPI_QUERY_DATA
	 */
	public function Range( $theBound1, $theBound2, $getOld = FALSE )
	{
		//
		// Get old bounds.
		//
		$save = $this->Object();
		
		//
		// Build range.
		//
		$range = Array();
		$range[] = CDataType::SerialiseData( $theBound1 );
		$range[] = CDataType::SerialiseData( $theBound2 );
		
		return $this->Object( $range, $getOld );									// ==>

	} // Range.

	 

} // class CQueryStatement.


?>
