<?php

/**
 * <i>CDataWrapperClient</i> class definition.
 *
 * This file contains the class definition of <b>CDataWrapperClient</b> which overloads its
 * {@link CWrapperClient ancestor} to implement a data store wrapper client.
 *
 *	@package	MyWrapper
 *	@subpackage	Wrappers
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 31/03/2012
 */

/*=======================================================================================
 *																						*
 *								CDataWrapperClient.php									*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CWrapperClient.php" );

/**
 * Server definitions.
 *
 * This include file contains all definitions of the server object.
 */
require_once( kPATH_LIBRARY_SOURCE."CDataWrapper.php" );

/**
 *	Data wrapper client.
 *
 * This class represents a web-services data wrapper client, it facilitates the job of
 * requesting information from servers derived from the {@link CDataWrapper CDataWrapper}
 * class.
 *
 * This class adds the following properties to its {@link CWrapperClient ancestor}:
 *
 * <ul>
 *	<li><i>{@link Database() Database}</i>: This element represents the web-service
 *		database, it is stored in the {@link kAPI_DATABASE kAPI_DATABASE} offset.
 *	<li><i>{@link Container() Container}</i>: This element represents the web-service
 *		database container, it is stored in the {@link kAPI_CONTAINER kAPI_CONTAINER}
 *		offset.
 *	<li><i>Page {@link Start() start}</i>: This element represents the start page requested
 *		from the web-service, it is stored in the {@link kAPI_PAGE_START kAPI_PAGE_START}
 *		offset.
 *	<li><i>Page {@link Limit() limit}</i>: This element represents the max page count
 *		requested from the web-service, it is stored in the
 *		{@link kAPI_PAGE_LIMIT kAPI_PAGE_LIMIT} offset.
 *	<li><i>{@link Query() Query}</i>: This element represents the {@link CQuery query} that
 *		will be sent to the web-service, it is stored in the
 *		{@link kAPI_DATA_QUERY kAPI_DATA_QUERY} offset.
 *	<li><i>{@link Fields() Fields}</i>: This element represents the list of fields that
 *		should be returned by the web-service, it is stored in the
 *		{@link kAPI_DATA_FIELD kAPI_DATA_FIELD} offset.
 *	<li><i>{@link Sort() Sort}</i>: This element represents the list of fields that will
 *		be used to sort the reqults returned by the web-service, it is stored in the
 *		{@link kAPI_DATA_SORT kAPI_DATA_SORT} offset.
 *	<li><i>{@link Object() Object}</i>: This element represents the object to be sent to the
 *		web-service, it is stored in the {@link kAPI_DATA_OBJECT kAPI_DATA_OBJECT} offset.
 *	<li><i>{@link Options() Options}</i>: This element represents a list of options sent to
 *		the web-service, it is stored in the {@link kAPI_DATA_OPTIONS kAPI_DATA_OPTIONS}
 *		offset.
 * </ul>
 *
 *	@package	MyWrapper
 *	@subpackage	Wrappers
 */
class CDataWrapperClient extends CWrapperClient
{
		

/*=======================================================================================
 *																						*
 *								PUBLIC MEMBER INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Operation																		*
	 *==================================================================================*/

	/**
	 * Manage operation.
	 *
	 * We {@link CWrapperClient::Operation() overload} this method to add the following
	 * allowed operations:
	 *
	 * <ul>
	 *	<li><i>{@link kAPI_OP_COUNT kAPI_OP_COUNT}</i>: COUNT web-service operation, used to
	 *		return the total number of elements satisfying a query.
	 *	<li><i>{@link kAPI_OP_GET kAPI_OP_GET}</i>: GET web-service operation, used to
	 *		retrieve objects from the data store.
	 *	<li><i>{@link kAPI_OP_SET kAPI_OP_SET}</i>: SET web-service operation, used to
	 *		insert or update objects in the data store.
	 *	<li><i>{@link kAPI_OP_UPDATE kAPI_OP_UPDATE}</i>: UPDATE web-service operation, used
	 *		to update existing objects in the data store.
	 *	<li><i>{@link kAPI_OP_INSERT kAPI_OP_INSERT}</i>: INSERT web-service operation, used
	 *		to insert new objects in the data store.
	 *	<li><i>{@link kAPI_OP_BATCH_INSERT kAPI_OP_BATCH_INSERT}</i>: This service is
	 *		equivalent to the {@link kAPI_OP_INSERT kAPI_OP_INSERT} command, except that in
	 *		this case you provide a list ov objects to insert.
	 *	<li><i>{@link kAPI_OP_MODIFY kAPI_OP_MODIFY}</i>: MODIFY web-service operation, used
	 *		to modify partial contents of objects in the data store.
	 *	<li><i>{@link kAPI_OP_DEL kAPI_OP_DEL}</i>: DELETE web-service operation, used to
	 *		delete objects from the data store.
	 * </ul>
	 *
	 * @param string				$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @throws {@link CException CException}
	 *
	 * @uses _ManageOffset()
	 *
	 * @see kAPI_OPERATION
	 * @see kAPI_OP_HELP kAPI_OP_PING
	 */
	public function Operation( $theValue = NULL, $getOld = FALSE )
	{
		//
		// Check operation.
		//
		if( ($theValue !== NULL)
		 && ($theValue !== FALSE) )
		{
			switch( $theValue )
			{
				case kAPI_OP_COUNT:
				case kAPI_OP_GET:
				case kAPI_OP_SET:
				case kAPI_OP_UPDATE:
				case kAPI_OP_INSERT:
				case kAPI_OP_BATCH_INSERT:
				case kAPI_OP_MODIFY:
				case kAPI_OP_DEL:
					break;
				
				default:
					return parent::Operation( $theValue, $getOld );					// ==>
			}
		}
		
		return $this->_ManageOffset( kAPI_OPERATION, $theValue, $getOld );			// ==>

	} // Operation.

	 
	/*===================================================================================
	 *	Database																		*
	 *==================================================================================*/

	/**
	 * Manage database.
	 *
	 * This method can be used to manage the {@link kAPI_DATABASE database}, it accepts a
	 * string which represents either the database name or the requested operation,
	 * depending on its value:
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
	 * @see kAPI_DATABASE
	 */
	public function Database( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kAPI_DATABASE, $theValue, $getOld );			// ==>

	} // Database.

	 
	/*===================================================================================
	 *	Container																		*
	 *==================================================================================*/

	/**
	 * Manage container.
	 *
	 * This method can be used to manage the database {@link kAPI_CONTAINER container}, it
	 * accepts a string which represents either the container name or the requested
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
	 * @see kAPI_CONTAINER
	 */
	public function Container( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kAPI_CONTAINER, $theValue, $getOld );			// ==>

	} // Container.

	 
	/*===================================================================================
	 *	Start																			*
	 *==================================================================================*/

	/**
	 * Manage page start.
	 *
	 * This method can be used to manage the page {@link kAPI_PAGE_START start}, it accepts
	 * a number which represents either the page start or the requested operation, depending
	 * on its value:
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
	 * @param integer				$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses _ManageOffset()
	 *
	 * @see kAPI_PAGE_START
	 */
	public function Start( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kAPI_PAGE_START, $theValue, $getOld );			// ==>

	} // Start.

	 
	/*===================================================================================
	 *	Limit																			*
	 *==================================================================================*/

	/**
	 * Manage page limit.
	 *
	 * This method can be used to manage the page {@link kAPI_PAGE_LIMIT limit}, it accepts
	 * a number which represents either the page limit or the requested operation, depending
	 * on its value:
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
	 * @param integer				$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses _ManageOffset()
	 *
	 * @see kAPI_PAGE_LIMIT
	 */
	public function Limit( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kAPI_PAGE_LIMIT, $theValue, $getOld );			// ==>

	} // Limit.

	 
	/*===================================================================================
	 *	Query																			*
	 *==================================================================================*/

	/**
	 * Manage data query.
	 *
	 * This method can be used to manage the data {@link kAPI_DATA_QUERY query}, it accepts
	 * an array or ArrayObject which represents either the data query or the requested
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
	 * If the provided value is not an array or an ArrayObject, the method will raise an
	 * exception.
	 *
	 * @param mixed					$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @throws {@link CException CException}
	 *
	 * @uses _ManageOffset()
	 *
	 * @see kAPI_DATA_QUERY
	 */
	public function Query( $theValue = NULL, $getOld = FALSE )
	{
		//
		// Check value.
		//
		if( ($theValue !== NULL)
		 && ($theValue !== FALSE)
		 && (! is_array( $theValue ))
		 && (! $theValue instanceof ArrayObject) )
			throw new CException( "Invalid query type",
								  kERROR_INVALID_PARAMETER,
								  kMESSAGE_TYPE_ERROR,
								  array( 'Query' => $theValue ) );				// !@! ==>
		
		return $this->_ManageOffset( kAPI_DATA_QUERY, $theValue, $getOld );			// ==>

	} // Query.

	 
	/*===================================================================================
	 *	Fields																			*
	 *==================================================================================*/

	/**
	 * Manage query fields.
	 *
	 * This method can be used to manage the {@link kAPI_DATA_FIELD fields} list, it accepts
	 * an array or ArrayObject which represents either the list of fields to be returned, or
	 * the requested operation, depending on its value:
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
	 * If the provided value is not an array or an ArrayObject, the method will raise an
	 * exception.
	 *
	 * @param mixed					$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @throws {@link CException CException}
	 *
	 * @uses _ManageOffset()
	 *
	 * @see kAPI_DATA_FIELD
	 */
	public function Fields( $theValue = NULL, $theOperation = NULL, $getOld = FALSE )
	{
		//
		// Check value.
		//
		if( ($theValue !== NULL)
		 && ($theValue !== FALSE)
		 && (! is_array( $theValue ))
		 && (! $theValue instanceof ArrayObject) )
			throw new CException( "Invalid fields list type",
								  kERROR_INVALID_PARAMETER,
								  kMESSAGE_TYPE_ERROR,
								  array( 'Fields' => $theValue ) );				// !@! ==>
		
		return $this->_ManageOffset( kAPI_DATA_FIELD, $theValue, $getOld );			// ==>

	} // Fields.

	 
	/*===================================================================================
	 *	Sort																			*
	 *==================================================================================*/

	/**
	 * Manage sort fields.
	 *
	 * This method can be used to manage the {@link Query() query}
	 * {@link kAPI_DATA_SORT sort} fields, it accepts an array or ArrayObject which
	 * represents either the {@link Query() query} list of sort fields, or the requested
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
	 * If the provided value is not an array or an ArrayObject, the method will raise an
	 * exception.
	 *
	 * @param mixed					$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @throws {@link CException CException}
	 *
	 * @uses _ManageOffset()
	 *
	 * @see kAPI_DATA_SORT
	 */
	public function Sort( $theValue = NULL, $getOld = FALSE )
	{
		//
		// Check value.
		//
		if( ($theValue !== NULL)
		 && ($theValue !== FALSE)
		 && (! is_array( $theValue ))
		 && (! $theValue instanceof ArrayObject) )
			throw new CException( "Invalid sort fields list type",
								  kERROR_INVALID_PARAMETER,
								  kMESSAGE_TYPE_ERROR,
								  array( 'Sort' => $theValue ) );				// !@! ==>
		
		return $this->_ManageOffset( kAPI_DATA_SORT, $theValue, $getOld );			// ==>

	} // Sort.

	 
	/*===================================================================================
	 *	Object																			*
	 *==================================================================================*/

	/**
	 * Manage query object.
	 *
	 * This method can be used to manage the {@link Query() query}
	 * {@link kAPI_DATA_OBJECT object}, it accepts an array or ArrayObject which represents
	 * the object to be sent to the web-service, or the requested operation, depending on
	 * its value:
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
	 * If the provided value is not an array or an ArrayObject, the method will raise an
	 * exception.
	 *
	 * @param mixed					$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @throws {@link CException CException}
	 *
	 * @uses _ManageOffset()
	 *
	 * @see kAPI_DATA_OBJECT
	 */
	public function Object( $theValue = NULL, $getOld = FALSE )
	{
		//
		// Check value.
		//
		if( ($theValue !== NULL)
		 && ($theValue !== FALSE)
		 && (! is_array( $theValue ))
		 && (! $theValue instanceof ArrayObject) )
			throw new CException( "Invalid object type",
								  kERROR_INVALID_PARAMETER,
								  kMESSAGE_TYPE_ERROR,
								  array( 'Object' => $theValue ) );				// !@! ==>
		
		return $this->_ManageOffset( kAPI_DATA_OBJECT, $theValue, $getOld );		// ==>

	} // Object.

	 
	/*===================================================================================
	 *	Options																			*
	 *==================================================================================*/

	/**
	 * Manage options.
	 *
	 * This method can be used to manage the {@link Query() query} data
	 * {@link kAPI_DATA_OPTIONS options}, with it you can add, retrieve and delete elements
	 * of the options list, to operate on the whole list you should use its
	 * {@link kAPI_DATA_OPTIONS offset}.
	 *
	 * The method accepts the following parameters:
	 *
	 * <ul>
	 *	<li><b>$theOption</b>: This parameter represents the option, it can take the
	 *		following values:
	 *	 <ul>
	 *		<li><i>{@link kAPI_OPT_SAFE kAPI_OPT_SAFE}</i>: Can be a boolean or integer,
	 *			defaults to FALSE. If FALSE, a database query will not wait for a response.
	 *			If TRUE, the program will wait for the database response and throw an
	 *			exception if the operation did not succeed.
	 *		<li><i>{@link kAPI_OPT_FSYNC kAPI_OPT_FSYNC}</i>: Boolean, defaults to FALSE.
	 *			Forces the update to be synced to disk before returning success. If TRUE, a
	 *			{@link kAPI_OPT_SAFE safe} update is implied and will override setting safe
	 *			to FALSE.
	 *		<li><i>{@link kAPI_OPT_TIMEOUT kAPI_OPT_TIMEOUT}</i>: Integer, if "safe" is set,
	 *			this sets how long (in milliseconds) for the client to wait for a database
	 *			response. If the database does not respond within the timeout period, an
	 *			exception will be thrown.
	 *		<li><i>{@link kAPI_OPT_SINGLE kAPI_OPT_SINGLE}</i>: Boolean, used in the
	 *			{@link kAPI_OP_DEL delete} or {@link kAPI_OP_UPDATE update} operatiosn: if
	 *			TRUE, only the first object will be affected; if not, all matching objects
	 *			will be considered.
	 *	 </ul>
	 *	<li><b>$theValue</b>: This parameter represents either the option value, or the
	 *		operation to perform:
	 *	 <ul>
	 *		<li><i>NULL</i>: This value indicates that we want to retrieve the value of the
	 *			option provided in the previous parameter.
	 *		<li><i>FALSE</i>: This value indicates that we want to delete the option
	 *			provided in the previous parameter.
	 *		<li><i>other</i>: Any other value is interpreted as the value of the option
	 *			selected by the first parameter. Note that, in general, missing options are
	 *			equivalent to setting the option to <i>FALSE</i>, when the value is a
	 *			boolean, so turning off an option is the same as removing it.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the element or list <i>before</i> it was eventually
	 *			modified.
	 *		<li><i>FALSE</i>: Return the element or list <i>after</i> it was eventually
	 *			modified.
	 *	 </ul>
	 * </ul>
	 *
	 * @param mixed					$theOption			Option selector.
	 * @param mixed					$theValue			Option value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @throws {@link CException CException}
	 *
	 * @see kAPI_DATA_OPTIONS
	 */
	public function Options( $theOption = NULL, $theValue = NULL, $getOld = FALSE )
	{
		//
		// Save current list and match.
		//
		$save = $this->offsetGet( kAPI_DATA_OPTIONS );
		$found = ( ($save !== NULL) && array_key_exists( $theOption, (array) $save ) )
			   ? $save[ $theOption ]
			   : NULL;
		
		//
		// Retrieve option.
		//
		if( $theValue === NULL )
			return $found;															// ==>
		
		//
		// Delete option.
		//
		if( $theValue === FALSE )
		{
			//
			// Delete.
			//
			if( $found !== NULL )
			{
				unset( $save[ $theOption ] );
				$this->offsetSet( kAPI_DATA_OPTIONS, $save );
			}
			
			if( $getOld )
				return $found;														// ==>
			
			return NULL;															// ==>
		
		} // Delete.
		
		//
		// Parse option.
		//
		switch( $theOption )
		{
			case kAPI_OPT_SAFE:
			case kAPI_OPT_FSYNC:
			case kAPI_OPT_SINGLE:
			case kAPI_OPT_TIMEOUT:
				if( $save === NULL )
					$save = array( $theOption => $theValue );
				else
					$save[ $theOption ] = $theValue;
				$this->offsetSet( kAPI_DATA_OPTIONS, $save );
				break;
			
			default:
				throw new CException( "Invalid option",
									  kERROR_INVALID_PARAMETER,
									  kMESSAGE_TYPE_ERROR,
									  array( 'Option' => $theOption ) );		// !@! ==>
		}
		
		if( $getOld )
			return $found;															// ==>
		
		return $theValue;															// ==>

	} // Options.

	 

} // class CDataWrapperClient.


?>
