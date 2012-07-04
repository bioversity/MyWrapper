<?php

/**
 * Exception class definitions.
 *
 * This file contains the definitions of local exception classes.
 *
 * Low level exceptions are all derived from the SPL exception classes and do not add any
 * further functionality.
 *
 * High level exceptions add specific functionality and would be triggered by queing low
 * level exceptions.
 *
 *	@package	MyWrapper
 *	@subpackage	Framework
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 22/12/2010
 *				2.00 02/02/2012
 */

/*=======================================================================================
 *																						*
 *									CException.php										*
 *																						*
 *======================================================================================*/

/**
 * Errors.
 *
 * This include file contains all error code definitions.
 */
require_once( kPATH_LIBRARY_DEFINES."Errors.inc.php" );

/**
 * Message types.
 *
 * This include file contains all message severity definitions.
 */
require_once( kPATH_LIBRARY_DEFINES."MessageTypes.inc.php" );

/**
 *	Exception class.
 *
 * This class extends the built-in <i>Exception</i> class by adding a series of additional
 * members and functionality.
 *
 * {@link Severity() Severity} represents the severity of the exception such as
 * <i>error</i>, <i>warning</i>, etc. You are free to set any value, but in this library we
 * support the following standard:
 * <ul>
 *	<li><b>{@link kMESSAGE_TYPE_IDLE Idle}</b>: Idle state.
 *	<li><b>{@link kMESSAGE_TYPE_NOTICE Notice}</b>: Statistical information or message.
 *	<li><b>{@link kMESSAGE_TYPE_MESSAGE Message}</b>: A message.
 *	<li><b>{@link kMESSAGE_TYPE_WARNING Warning}</b>: A warning.
 *	<li><b>{@link kMESSAGE_TYPE_ERROR Error}</b>: An error.
 *	<li><b>{@link kMESSAGE_TYPE_FATAL Fatal}</b>: A fatal error.
 *	<li><b>{@link kMESSAGE_TYPE_BUG Bug}</b>: A bug.
 * </ul>
 *
 * {@link References() References} represent a list of reference values structured as an
 * array of key/value pairs where the key represents the reference label or name and the
 * value the reference value. Labels must be strings, values can be of any type.
 *
 * Note that this class does not throw exceptions (hahaha), operations that fail are simply
 * aborted.
 *
 *	@package	MyWrapper
 *	@subpackage	Framework
 */
class CException extends Exception
{
	/**
	 * Exception severity.
	 *
	 * This data member holds the exception severity.
	 *
	 * @var string
	 */
	 protected $mSeverity = NULL;

	/**
	 * Exception references.
	 *
	 * This data member holds the list of exception references.
	 *
	 * @var array
	 */
	 protected $mReferences = Array();

		

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
	 * The first two parameters follow the inherited interface, the constructor adds support
	 * for the extended class members:
	 *
	 * <ul>
	 *	<li><b>$theMessage</b>: This parameter represents the inherited <i>message</i>.
	 *	<li><b>$theCode</b>: This parameter represents the inherited <i>code</i>.
	 *	<li><b>$theSeverity</b>: This parameter holds {@link Severity() the} exception
	 *		type, level or severity:
	 *	 <ul>
	 *		<li><i>{@link kMESSAGE_TYPE_IDLE kMESSAGE_TYPE_IDLE}</i>: This indicates an idle
	 *			state.
	 *		<li><i>{@link kMESSAGE_TYPE_NOTICE kMESSAGE_TYPE_NOTICE}</i>: This indicates an
	 *			informative note or message.
	 *		<li><i>{@link kMESSAGE_TYPE_WARNING kMESSAGE_TYPE_WARNING}</i>: This indicates a
	 *			warning.
	 *		<li><i>{@link kMESSAGE_TYPE_ERROR kMESSAGE_TYPE_ERROR}</i>: This indicates an error.
	 *		<li><i>{@link kMESSAGE_TYPE_FATAL kMESSAGE_TYPE_FATAL}</i>: This indicates a fatal
	 *			error, in general this should halt program execution.
	 *		<li><i>{@link kMESSAGE_TYPE_BUG kMESSAGE_TYPE_BUG}</i>: This indicates a bug, such
	 *			exceptions should be logged and forwarded to developers.
	 *	 </ul>
	 *	<li><b>$theReferences</b>: This parameter holds the exception's references
	 *		{@link References() list}, it is an array indexed by reference label or term
	 *		holding the reference value as value.
	 *	<li><b>$thePrevious</b>: This parameter represents the previous exception when
	 *		forwarding.
	 * </ul>
	 *
	 * @param string				$theMessage			Exception message.
	 * @param integer				$theCode			Exception code.
	 * @param string				$theSeverity		Exception severity.
	 * @param array					$theReferences		Exception references.
	 * @param Exception				$thePrevious		Previous exception.
	 *
	 * @access public
	 *
	 * @uses CodeUser()
	 * @uses UserMessages()
	 * @uses UserNamespace()
	 * @uses Severity()
	 * @uses Reference()
	 */
	public function __construct( $theMessage = NULL,
								 $theCode = NULL,
								 $theSeverity = NULL,
								 $theReferences = NULL,
								 $thePrevious = NULL )
	{
		//
		// Construct parent.
		//
		parent::__construct( $theMessage, $theCode, $thePrevious );
		
		//
		// Handle severity.
		//
		if( $theSeverity !== NULL )
			$this->Severity( $theSeverity );
		
		//
		// Set exception references.
		//
		if( is_array( $theReferences ) )
			$this->Reference( NULL, $theReferences );

	} // Constructor.

	 
	/*===================================================================================
	 *	__toString																		*
	 *==================================================================================*/

	/**
	 * Return the object name.
	 *
	 * In this class we return the stack trace.
	 *
	 * @access public
	 * @return string	The stack trace.
	 */
	public function __toString()
	{
		//
		// Init local storage.
		//
		$string = '';
		$trace = $this->_Trace( $this );
		
		//
		// Traverse trace.
		//
		while( count( $trace ) )
		{
			//
			// Get element.
			//
			$element = array_shift( $trace );
			
			//
			// Mark step.
			//
			$string .= '==> ';
			
			//
			// Handle exception.
			//
			if( $element instanceof Exception )
			{
				//
				// Add file info.
				//
				$line = $element->getLine();
				$file = $element->getFile();
				if( strlen( $line )
				 || strlen( $file ) )
					$string .= "($line) $file";
				$string .= "\n";
				
				//
				// Add exception class.
				//
				$string .= ('    ['.get_class( $element ).']');
				
				//
				// Handle our exceptions.
				//
				if( $element instanceof self )
				{
					//
					// Add severity.
					//
					if( strlen( $tmp = $element->Severity() ) )
					{
						switch( $tmp )
						{
							case kMESSAGE_TYPE_NOTICE:
								$tmp = 'NOTICE';
								break;
							case kMESSAGE_TYPE_MESSAGE:
								$tmp = 'MESSAGE';
								break;
							case kMESSAGE_TYPE_WARNING:
								$tmp = 'WARNING';
								break;
							case kMESSAGE_TYPE_ERROR:
								$tmp = 'ERROR';
								break;
							case kMESSAGE_TYPE_FATAL:
								$tmp = 'FATAL';
								break;
							case kMESSAGE_TYPE_BUG:
								$tmp = 'BUG';
								break;
						}
			
						$string .= " $tmp";
					}
					
					//
					// Next line.
					//
					$string .= "\n";
					
					//
					// Add default message.
					//
					$code = $element->getCode();
					$message = $element->getMessage();
					if( strlen( $code )
					 || strlen( $message ) )
					{
						$txt = Array();
						if( strlen( $code ) )
							$txt[] = "($code)";
						if( strlen( $message ) )
							$txt[] = $message;
						$string .= ( "  D ".implode( ' ', $txt )."\n" );
					}
				
				} // Custom exception.
				
				//
				// Handle standard exceptions.
				//
				else
				{
					//
					// Add title.
					//
					if( strlen( $tmp = $element->getMessage() ) )
						$string .= "\n    $tmp\n";
					
					//
					// Add default message.
					//
					if( strlen( $tmp = $element->getCode() ) )
						$string .= ( "  D ($tmp)\n" );
					
				
				} // Standard exception.
			
			} // Is an exception.
			
			//
			// Handle trace element.
			//
			else
			{
				//
				// Get trace elements.
				//
				$file = ( array_key_exists( 'file', $element ) )
					  ? $element[ 'file' ]
					  : NULL;
				$line = ( array_key_exists( 'line', $element ) )
					  ? $element[ 'line' ]
					  : NULL;
				$class = ( array_key_exists( 'class', $element ) )
					  ? $element[ 'class' ]
					  : NULL;
				$func = ( array_key_exists( 'function', $element ) )
					  ? $element[ 'function' ]
					  : NULL;
				$type = ( array_key_exists( 'type', $element ) )
					  ? $element[ 'type' ]
					  : NULL;
				
				//
				// Build arguments list.
				//
				$args = Array();
				foreach( $element[ 'args' ] as $arg )
					$args[] = $this->_TraceArgumentString( $arg );
				
				//
				// Add file info.
				//
				if( strlen( $line )
				 || strlen( $file ) )
					$string .= "($line) $file\n    ";
			
				//
				// Build string.
				//
				$string .= ("$class$type$func("
						   .implode( ', ', $args ).");\n");
			
			} // Is a trace step.
		
		} // Traversing trace.
		
		return $string;																// ==>
	
	} // __toString.

		

/*=======================================================================================
 *																						*
 *								PUBLIC DATA MEMBER INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Severity																		*
	 *==================================================================================*/

	/**
	 * Set or return the exception severity.
	 *
	 * When providing <i>$theValue</i>:
	 *
	 * <ul>
	 *	<li><i>NULL</i>: Retrieve the current value.
	 *	<li><i>FALSE</i>: Reset the value to <i>NULL</i>.
	 *	<li><i>Integer</i>: Set the member to the provided value:
	 *	 <ul>
	 *		<li><i>{@link kMESSAGE_TYPE_IDLE kMESSAGE_TYPE_IDLE}</i>: Idle state.
	 *		<li><i>{@link kMESSAGE_TYPE_NOTICE kMESSAGE_TYPE_NOTICE}</i>: A notice is an
	 *			informative message that does not imply an error, nor a situation that
	 *			should be handled; it can be considered as statistical data.
	 *		<li><i>{@link kMESSAGE_TYPE_MESSAGE kMESSAGE_TYPE_MESSAGE}</i>: A message is an
	 *			informative message that is addressed to somebody, although it does not
	 *			imply an error or warning, it was issued to a receiving party.
	 *		<li><i>{@link kMESSAGE_TYPE_WARNING kMESSAGE_TYPE_WARNING}</i>: Warnings are
	 *			informative data that indicate a potential problem, although they do not
	 *			imply an error, they indicate a potential problem or an issue that should be
	 *			addressed at least at a later stage.
	 *		<li><i>{@link kMESSAGE_TYPE_ERROR kMESSAGE_TYPE_ERROR}</i>: Errors indicate that
	 *			something prevented an operation from being performed, this does not
	 *			necessarily mean that the whole process is halted, but that the results of
	 *			an operation will not be as expected.
	 *		<li><i>{@link kMESSAGE_TYPE_FATAL kMESSAGE_TYPE_FATAL}</i>: Fatal errors are
	 *			{@link kMESSAGE_TYPE_ERROR errors} that result in stopping the whole
	 *			process: in this case the error will prevent other operations from being
	 *			performed and the whole process should be halted.
	 *		<li><i>{@link kMESSAGE_TYPE_BUG kMESSAGE_TYPE_BUG}</i>: Bugs, as opposed to
	 *			{@link kMESSAGE_TYPE_ERROR errors}, result from internal causes independant
	 *			from external factors. A bug indicates that an operation will never execute
	 *			as stated, it does not necessarily mean that it is
	 *			{@link kMESSAGE_TYPE_FATAL fatal}, but rather that the behaviour of an
	 *			operation does not correspond to its declaration.
	 *	 </ul>
	 * </ul>
	 *
	 * The above mentioned codes are integer based, each state is represented by an
	 * interval, we encourage using the above mentioned codes, but this is not enforced.
	 *
	 * When setting or resetting the value, the method will return it <i>after</i> any
	 * modification was made.
	 *
	 * @param NULL|FALSE|integer	$theValue		NULL, FALSE or exception severity.
	 *
	 * @access public
	 * @return mixed	Exception severity or type.
	 */
	public function Severity( $theValue = NULL )
	{
		//
		// Return value.
		//
		if( $theValue === NULL )
			return $this->mSeverity;												// ==>
		
		//
		// Set or reset value.
		//
		$this->mSeverity = ( $theValue === FALSE )
						 ? NULL
						 : $theValue;
		
		return $this->mSeverity;													// ==>
	
	} // Severity.

	 
	/*===================================================================================
	 *	Reference																		*
	 *==================================================================================*/

	/**
	 * Set or return an exception reference.
	 *
	 * This method can be used to set, retrieve and remove exception references.
	 * References are a key/value pair that provide additional information on the exception.
	 *
	 * <ul>
	 *	<li><b>$theIndex</b>: This represents the reference label or name, if you provide
	 *		the parameter, it will be used as an array index to either retrieve, set or
	 *		remove elements, depending on the next parameter's value. If you omit the
	 *		parameter or pass <i>NULL</i> the method will consider the whole list of
	 *		references.
	 *	<li><b>$theValue</b>: This represents the reference value:
	 *	 <ul>
	 *		<li><i>NULL</i>: This value indicates that we want to retrieve a reference. If
	 *			<i>$theIndex</i> is provided, the method will return the entry matching it,
	 *			if there is no match, the method will return <i>NULL</i>. If
	 *			<i>$theIndex</i> is omitted or <i>NULL</i>, the method will return the full
	 *			references array.
	 *		<li><i>FALSE</i>: This value indicates that we want to delete a reference. If
	 *			<i>$theIndex</i> is provided, the method will delete the entry matching it
	 *			and return <i>NULL</i>. If <i>$theIndex</i> is omitted or <i>NULL</i>, the
	 *			method will delete all references and return an empty array.
	 *		<li><i>Any other type</i>: Other types of value are considered as a value to
	 *			replace or be added to the references list: if <i>$theIndex</i> is provided,
	 *			the method will add or replace the matching element in the list and return
	 *			the eventual value <i>after</i> it was modified. If <i>$theIndex</i> is
	 *			omitted or <i>NULL</i>, the operation depends on the type of value:
	 *		 <ul>
	 *			<li><i>array</i>: The provided array will replace the current list and the
	 *				method will return the list <i>after</i> it was replaced.
	 *			<li><i>Any other type</i>: The provided value will be appended to the list
	 *				by using a numeric index and the method will always return the provided
	 *				value.
	 *		 </ul>
	 *	 </ul>
	 * </ul>
	 *
	 * @param mixed				$theIndex		Reference element index.
	 * @param mixed				$theValue		NULL, FALSE, reference value or list.
	 *
	 * @access public
	 * @return mixed	Exception reference.
	 */
	public function Reference( $theIndex = NULL, $theValue = NULL )
	{
		//
		// Retrieve value.
		//
		if( $theValue === NULL )
		{
			//
			// Return the whole list.
			//
			if( $theIndex === NULL )
				return $this->mReferences;											// ==>
			
			//
			// Return list element.
			//
			return ( array_key_exists( $theIndex, $this->mReferences ) )
				   ? $this->mReferences[ $theIndex ]								// ==>
				   : NULL;															// ==>
		}
		
		//
		// Reset value.
		//
		if( $theValue === FALSE )
		{
			//
			// Reset list.
			//
			if( $theIndex === NULL )
			{
				//
				// List.
				//
				$this->mReferences = Array();
				
				return Array();														// ==>
			}
			
			//
			// Reset element.
			//
			if( array_key_exists( $theIndex, $this->mReferences ) )
				unset( $this->mReferences[ $theIndex ] );

			return NULL;															// ==>
		
		} // Reset value.
		
		//
		// Add or replace element.
		//
		if( $theIndex !== NULL )
			$this->mReferences[ $theIndex ] = $theValue;
		
		//
		// Replace list.
		//
		elseif( is_array( $theValue ) )
			$this->mReferences = $theValue;
		
		//
		// Append element.
		//
		else
			$this->mReferences[] = $theValue;
		
		return $theValue;															// ==>
	
	} // Reference.

		

/*=======================================================================================
 *																						*
 *									STATIC INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	AsHTML																			*
	 *==================================================================================*/

	/**
	 * Display an exception.
	 *
	 * This method can be used to display exceptions in a web browser page.
	 *
	 * @param Exception|CException	$theException	Exception object.
	 *
	 * @static
	 * @return string
	 *
	 * @uses Reference()
	 * @uses Argument()
	 * @uses Value()
	 */
	static function AsHTML( Exception $theException )
	{
		//
		// Open table.
		//
		$document = new DOMDocument( '1.0', 'UTF-8' );
		$table = $document->createElement( 'table' );
		$table = $document->appendChild( $table );
		$table->setAttribute( 'border', '1' );
		$table->setAttribute( 'cellspacing', '2' );
		$table->setAttribute( 'cellpadding', '0' );
		$table->setAttribute( 'bordercolor', '#000000' );
		
		//
		// Traverse trace.
		//
		$first = TRUE;
		$trace = self::_Trace( $theException );
		while( count( $trace ) )
		{
			//
			// Add divider.
			//
			if( ! $first )
			{
				$row = $document->createElement( 'tr' );
				$row = $table->appendChild( $row );
				$col = $document->createElement( 'td' );
				$col = $row->appendChild( $col );
				$col->setAttribute( 'colspan', '3' );
				$col->setAttribute( 'height', '8' );
			}
			else
				$first = FALSE;
			
			//
			// Get element.
			//
			$element = array_shift( $trace );
			
			//
			// Populate table.
			//
			if( $element instanceof Exception )
				self::_Exception2HTML( $table, $element );
			else
				self::_Trace2HTML( $table, $element );
		
		} // Traversing trace.
		
		return $document->saveHTML();												// ==>
	
	} // AsHTML.

		

/*=======================================================================================
 *																						*
 *									STATIC UTILITIES									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_Trace																			*
	 *==================================================================================*/

	/**
	 * Return the exception trace.
	 *
	 * This method can be used to return the exception trace in the order in which it was
	 * called. The main utility of this method is to manage forwarded exceptions.
	 *
	 * The method will return an array indexed by file path and line number hash in which
	 * the value is either an exception or a trace array. If you run in PHP version < 3.0.x
	 * the first element will be an exception and the others will be traces, if you run a
	 * higher version of PHP you can forward exceptions, so the elements will be mixed.
	 *
	 * @param Exception			$theException	Exception to trace.
	 *
	 * @static
	 * @return array
	 */
	static function _Trace( Exception $theException )
	{
		//
		// Init local storage.
		//
		$trace = Array();
		
		//
		// Traverse exceptions.
		//
		do
		{
			//
			// Traverse trace.
			//
			$list = $theException->getTrace();
			while( count( $list ) )
			{
				//
				// Pop trace element.
				//
				$tmp = array_pop( $list );
				$file = ( array_key_exists( 'file', $tmp ) ) ? $tmp[ 'file' ] : NULL;
				$line = ( array_key_exists( 'line', $tmp ) ) ? $tmp[ 'line' ] : NULL;
				$index = md5( "$file:$line" );
				if( ! array_key_exists( $index, $trace ) )
					$trace[ $index ] = $tmp;
			
			} // Traversing trace.
			
			//
			// Add exception.
			//
			$index = md5( $theException->getFile().':'.$theException->getLine() );
			$trace[ $index ] = $theException;
			
			//
			// Get previous exception.
			//
			if( phpversion() >= '5.3.0' )
				$theException = $theException->getPrevious();
			else
				break;														// =>
		
		} while( $theException !== NULL );
		
		return $trace;																// ==>
	
	} // _Trace.

	 
	/*===================================================================================
	 *	_TraceArgumentString															*
	 *==================================================================================*/

	/**
	 * Return the trace argument string.
	 *
	 * This method can be used to return the string representation of a trace argument.
	 *
	 * @param mixed					$theValue		Trace argument.
	 *
	 * @static
	 * @return string
	 */
	static protected function _TraceArgumentString( $theValue )
	{
		//
		// Parse argument.
		//
		if( $theValue === NULL )
			$string = 'NULL';
		elseif( $theValue === TRUE )
			$string = 'TRUE';
		elseif( $theValue === FALSE )
			$string = 'FALSE';
		elseif( is_array( $theValue ) )
			$string = 'array';
		elseif( is_object( $theValue ) )
		{
			$string = get_class( $theValue );
			if( method_exists( $theValue, '__toString' ) )
			{
				$value = (string) $theValue;
				if( strlen( $value ) )
					$string = "$string( $value )";
			}
		}
		else
		{
			if( ctype_print( (string) $theValue ) )
				$string = gettype( $theValue ).'('.(string) $theValue.')';
			else
				$string = gettype( $theValue ).'[BINARY STRING]';
		}
		
		return $string;																// ==>
	
	} // _TraceArgumentString.

	 
	/*===================================================================================
	 *	_Trace2HTML																		*
	 *==================================================================================*/

	/**
	 * Return the exception trace element as HTML.
	 *
	 * This method can be used to return the trace element as HTML code.
	 *
	 * The method accepts the following parameters:
	 *
	 * <ul>
	 *	<li><b>$theTable</b>: The HTML table in which we want to place the trace.
	 *	<li><b>$theElement</b>: The trace element, it must be an array, if any other type is
	 *		passed, the method will return <i>NULL</i>.
	 * </ul>
	 *
	 * @param DOMNode				$theTable		HTML table.
	 * @param array					$theElement		Trace element.
	 *
	 * @static
	 */
	static function _Trace2HTML( DOMNode $theTable, $theElement )
	{
		//
		// Check arguments.
		//
		if( ! is_array( $theElement ) )
			return NULL;															// ==>
		if( ! $theTable instanceof DOMNode )
			return NULL;															// ==>
		
		//
		// Init local storage.
		//
		$document = $theTable->ownerDocument;
		$font = $document->createElement( 'font' );
		$font->setAttribute( 'size', '-1' );
		$font->setAttribute( 'face', 'verdana, Lucida Sans, Arial, Lucida Grande' );
		
		//
		// Handle file row.
		//
		$row = $document->createElement( 'tr' );
		$row = $theTable->appendChild( $row );

		$col = $document->createElement( 'td' );
		$col = $row->appendChild( $col );
		$col->setAttribute( 'colspan', '3' );
		$col->setAttribute( 'bgcolor', '#004080' );

		$val = $font->cloneNode( TRUE );
		$val = $col->appendChild( $val );
		$val->setAttribute( 'color', 'yellow' );
		
		$file = ( array_key_exists( 'file', $theElement ) )
			  ? $theElement[ 'file' ]
			  : NULL;
		$line = ( array_key_exists( 'line', $theElement ) )
			  ? $theElement[ 'line' ]
			  : NULL;
		if( strlen( $file )
		 || strlen( $line ) )
		{
			$tmp = $document->createTextNode( "($line) $file" );
			$val->appendChild( $tmp );
		}
		
		//
		// Handle function.
		//
		$row = $document->createElement( 'tr' );
		$row = $theTable->appendChild( $row );

		$col = $document->createElement( 'td' );
		$col = $row->appendChild( $col );
		$col->setAttribute( 'colspan', '3' );
		$col->setAttribute( 'bgcolor', '#FFFF99' );

		$val = $font->cloneNode( TRUE );
		$val = $col->appendChild( $val );
		
		$class = ( array_key_exists( 'class', $theElement ) )
			   ? $theElement[ 'class' ]
			   : NULL;
		$type = ( array_key_exists( 'type', $theElement ) )
			  ? $theElement[ 'type' ]
			   : NULL;
		$func = ( array_key_exists( 'function', $theElement ) )
			  ? ($theElement[ 'function' ].'()')
			  : NULL;
		
		$tmp = $document->createElement( 'em', $class );
		$val->appendChild( $tmp );

		$tmp = $document->createTextNode( "$type$func" );
		$val->appendChild( $tmp );
		
		//
		// Handle arguments.
		//
		if( count( $args = $theElement[ 'args' ] ) )
		{
			//
			// Add header.
			//
			$row = $document->createElement( 'tr' );
			$row = $theTable->appendChild( $row );
	
			$col = $document->createElement( 'td' );
			$col = $row->appendChild( $col );
			$col->setAttribute( 'colspan', '2' );
			$col->setAttribute( 'rowspan', count( $args ) );
			$col->setAttribute( 'bgcolor', '#ECECEC' );
	
			$val = $font->cloneNode( TRUE );
			$val = $col->appendChild( $val );
	
			$tmp = $document->createElement( 'em', 'args:' );
			$val->appendChild( $tmp );
			
			//
			// Add first argument.
			//
			$arg = array_shift( $args );
			$col = $document->createElement( 'td' );
			$col = $row->appendChild( $col );
			$col->setAttribute( 'bgcolor', '#ECECEC' );
	
			$val = $font->cloneNode( TRUE );
			$val = $col->appendChild( $val );

			$string = self::_TraceArgumentString( $arg );
			$tmp = $document->createTextNode( $string );
			$val->appendChild( $tmp );
			
			//
			// Add remaining arguments.
			//
			foreach( $args as $arg )
			{
				$row = $document->createElement( 'tr' );
				$row = $theTable->appendChild( $row );
	
				$col = $document->createElement( 'td' );
				$col = $row->appendChild( $col );
				$col->setAttribute( 'bgcolor', '#ECECEC' );
		
				$val = $font->cloneNode( TRUE );
				$val = $col->appendChild( $val );
		
				$string = self::_TraceArgumentString( $arg );
				$tmp = $document->createTextNode( $string );
				$val->appendChild( $tmp );
			
			} // Iterating arguments.
		
		} // Has arguments.
	
	} // _Trace2HTML.

	 
	/*===================================================================================
	 *	_Exception2HTML																	*
	 *==================================================================================*/

	/**
	 * Return the exception as HTML.
	 *
	 * This method can be used to return the provided exception as HTML code, it is assumed
	 * that the element will be placed in an HTML table, so the method will return a series
	 * of table rows.
	 *
	 * The method accepts the following parameters:
	 *
	 * <ul>
	 *	<li><b>$theTable</b>: The HTML table in which we want to place the trace.
	 *	<li><b>$theElement</b>: The exception, it must be an Exception, if any other type is
	 *		passed, the method will return <i>NULL</i>.
	 *	<li><b>$theSource</b>: This parameter is used to provide a link to the source file:
	 *	 <ul>
	 *		<li><i>NULL</i>: No source file management.
	 *		<li><i>TRUE</i>: The default source file viewer will be used.
	 *		<li><i>string</i>: The method will execute the provided string as a shell
	 *			command line.
	 *	 </ul>
	 * </ul>
	 *
	 * @param DOMElement			$theTable		HTML table.
	 * @param array					$theElement		Trace element.
	 * @param NULL|TRUE|string		$theSource		Source file control.
	 *
	 * @static
	 */
	static function _Exception2HTML( $theTable, $theElement, $theSource = NULL )
	{
		//
		// Check argument.
		//
		if( ! $theElement instanceof Exception )
			return NULL;															// ==>
		if( ! $theTable instanceof DOMNode )
			return NULL;															// ==>
		
		//
		// Init local storage.
		//
		$document = $theTable->ownerDocument;
		
		//
		// Handle file row.
		//
		$row = $document->createElement( 'tr' );
		$row = $theTable->appendChild( $row );

		$col = $document->createElement( 'td' );
		$col = $row->appendChild( $col );
		$col->setAttribute( 'colspan', '3' );
		$col->setAttribute( 'bgcolor', '#004080' );

		$val = $document->createElement( 'font' );
		$val->setAttribute( 'size', '-1' );
		$val->setAttribute( 'face', 'verdana, Lucida Sans, Arial, Lucida Grande' );
		$val = $col->appendChild( $val );
		$val->setAttribute( 'color', 'yellow' );
		
		$file = $theElement->getFile();
		$line = $theElement->getLine();
		if( strlen( $file )
		 || strlen( $line ) )
		{
			$tmp = $document->createTextNode( "($line) $file" );
			$val->appendChild( $tmp );
		}
		
		//
		// Handle custom exceptions.
		//
		if( $theElement instanceof self )
		{
			//
			// Get severity and title.
			//
			$class = get_class( $theElement );
			$severity = $theElement->Severity();
			
			//
			// Set class.
			//
			$row = $document->createElement( 'tr' );
			$row = $theTable->appendChild( $row );
	
			$col = $document->createElement( 'td' );
			$col = $row->appendChild( $col );
			$col->setAttribute( 'bgcolor', '#FFCC00' );
	
			$val = $document->createElement( 'font' );
			$val->setAttribute( 'size', '-1' );
			$val->setAttribute( 'face', 'Verdana, Arial, Helvetica, sans-serif' );
			$val = $col->appendChild( $val );
	
			$tmp = $document->createElement( 'em', $class );
			$val->appendChild( $tmp );
			
			//
			// Set severity.
			//
			if( strlen( $severity ) )
			{
				$col = $document->createElement( 'td' );
				$col = $row->appendChild( $col );
				
				$col->setAttribute( 'colspan', '2' );
					
				$col->setAttribute( 'bgcolor', '#FFCC00' );
		
				$val = $document->createElement( 'font' );
				$val->setAttribute( 'size', '-1' );
				$val->setAttribute( 'face', 'verdana, Lucida Sans, Arial, Lucida Grande' );
				$val = $col->appendChild( $val );
				
				$code = $theElement->getCode();
				$message = $theElement->getMessage();
				if( strlen( $code ) )
					$message = "($code) $message";
				
				switch( $severity )
				{
					case kMESSAGE_TYPE_NOTICE:
						$val->setAttribute( 'color', 'grey' );
						$tmp = $document->createElement( 'strong', 'NOTICE' );
						$val->appendChild( $tmp );
						break;
					case kMESSAGE_TYPE_MESSAGE:
						$val->setAttribute( 'color', 'blue' );
						$tmp = $document->createElement( 'strong', 'MESSAGE' );
						$val->appendChild( $tmp );
						break;
					case kMESSAGE_TYPE_WARNING:
						$val->setAttribute( 'color', '#FF6600' );
						$tmp = $document->createElement( 'strong', 'WARNING' );
						$val->appendChild( $tmp );
						break;
					case kMESSAGE_TYPE_ERROR:
						$val->setAttribute( 'color', 'red' );
						$tmp = $document->createElement( 'strong', 'ERROR' );
						$val->appendChild( $tmp );
						break;
					case kMESSAGE_TYPE_FATAL:
						$val->setAttribute( 'color', 'red' );
						$tmp = $document->createElement( 'strong' );
						$tmp1 = $val->appendChild( $tmp );
						$tmp = $document->createElement( 'blink', 'FATAL' );
						$tmp1->appendChild( $tmp );
						break;
					case kMESSAGE_TYPE_BUG:
						$val->setAttribute( 'color', 'red' );
						$tmp = $document->createElement( 'strong' );
						$tmp1 = $val->appendChild( $tmp );
						$tmp = $document->createElement( 'blink', 'BUG' );
						$tmp1->appendChild( $tmp );
						break;
				}
			}
			
			//
			// Set default code line.
			//
			$code = $theElement->getCode();
			$message = $theElement->getMessage();
			
			$row = $document->createElement( 'tr' );
			$row = $theTable->appendChild( $row );
	
			$col = $document->createElement( 'td' );
			$col = $row->appendChild( $col );
			$col->setAttribute( 'colspan', '3' );
			$col->setAttribute( 'bgcolor', '#FFCC00' );
	
			$val = $document->createElement( 'font' );
			$val->setAttribute( 'size', '-1' );
			$val->setAttribute( 'face', 'Verdana, Arial, Helvetica, sans-serif' );
			$val = $col->appendChild( $val );

			$tmp = $document->createTextNode( "($code) $message" );
			$val->appendChild( $tmp );
			
			//
			// Set references.
			//
			if( $references = $theElement->Reference() )
			{
				foreach( $references as $label => $value )
				{
					$row = $document->createElement( 'tr' );
					$row = $theTable->appendChild( $row );
					
					//
					// Set label.
					//
					$col = $document->createElement( 'td' );
					$col = $row->appendChild( $col );
					$col->setAttribute( 'bgcolor', '#DDDDDD' );
			
					$val = $document->createElement( 'font' );
					$val->setAttribute( 'size', '-1' );
					$val->setAttribute( 'face', 'verdana, Lucida Sans, Arial, Lucida Grande' );
					$val = $col->appendChild( $val );
			
					$tmp = $document->createElement( 'em', $label );
					$val->appendChild( $tmp );
					
					//
					// Set value.
					//
					$col = $document->createElement( 'td' );
					$col = $row->appendChild( $col );
					$col->setAttribute( 'colspan', '2' );
					$col->setAttribute( 'bgcolor', '#FFFFCC' );
			
					$val = $document->createElement( 'font' );
					$val->setAttribute( 'size', '-1' );
					$val->setAttribute( 'face', 'verdana, Lucida Sans, Arial, Lucida Grande' );
					$val = $col->appendChild( $val );
					$tmp = $document->createTextNode( self::_TraceArgumentString( $value ) );
					$val->appendChild( $tmp );
				}
			}
		
		} // Custom exception.
		
		//
		// Handle default exceptions.
		//
		else
		{
			$code = $theElement->getCode();
			$message = $theElement->getMessage();
			
			if( strlen( $code )
			 || strlen( $message ) )
			{
				$row = $document->createElement( 'tr' );
				$row = $theTable->appendChild( $row );
		
				$col = $document->createElement( 'td' );
				$col = $row->appendChild( $col );
				$col->setAttribute( 'colspan', '3' );
				$col->setAttribute( 'bgcolor', '#FFCC00' );
		
				$val = $document->createElement( 'font' );
				$val->setAttribute( 'size', '-1' );
				$val->setAttribute( 'face', 'Verdana, Arial, Helvetica, sans-serif' );
				$val = $col->appendChild( $val );
		
				$tmp = $document->createTextNode( "($code) $message" );
				$val->appendChild( $tmp );
			}
		
		} // Default exceptions.
	
	} // _Exception2HTML.

	 

} // class CException.


?>
