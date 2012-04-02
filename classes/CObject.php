<?php

/**
 * <i>CObject</i> class definition.
 *
 * This file contains the class definition of <b>CObject</b> which contains common static
 * methods and definitions.
 *
 *	@package	MyWrapper
 *	@subpackage	Core
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 07/04/2012
 */

/*=======================================================================================
 *																						*
 *										CObject.php										*
 *																						*
 *======================================================================================*/

/**
 * Exceptions.
 *
 * This include file contains all exception class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CException.php" );

/**
 * Flags.
 *
 * This include file contains all flags definitions.
 */
require_once( kPATH_LIBRARY_DEFINES."Flags.inc.php" );

/**
 *	Common static ancestor.
 *
 * This <i>abstract</i> class implements a common interface shared by all classes in this
 * library.
 *
 * The library uses the following conventions:
 *
 * <b>Naming standards</b>
 *
 * <ul>
 *	<li><b>Classes</b>: All class names in this library start with a capital (<i>C</i>), for
 *		instance <i>CAnotherClass</i>, followed by a name starting with another capital
 *		letter.
 *	<li><b>Public methods</b>: All public method names should begin with a capital letter,
 *		for instance <i>AnotherMethod()</i>.
 *	<li><b>Protected and private methods</b>: All protected and private method names should
 *		begin with an underscore, for instance <i>_AnotherMethod()</i>. Another case in
 *		which names would start with an underscore is with core static methods.
 *	<li><b>Members</b>: All members should start with lowercase (<i>m</i>), followed
 *		by a capital letter. For instance <i>mMember</i>.
 *	<li><b>Static members</b>: All static members should begin with lowercase (<i>s</i>), so
 *		a static member could be <i>CSomeClass::$sStaticMember</i>.
 *	<li><b>Definitions</b>: All definitions should start with a (<i>k</i>) and be followed
 *		by a code and ending with an uppercase name, for instance <i>kTAG_DOMAIN</i> would
 *		be the definition of a domain tag.
 *	<li><b>Method arguments</b>: All method arguments should start with either <i>the</i>,
 *		for parameters holding miscellaneous values, such as <i>$theVariable</i>,, or with a
 *		lowercase verb, such as <i>is</i>, <i>has</i> or <i>do</i>, for parameters holding a
 *		flag value, such as <i>isProtected</i>.
 *	<li><b>Local variables</b>: All local variables should be in <i>lowercase</i>, for
 *		instance <i>$local_counter</i>.
 * </ul>
 *
 * In general, abstract classes implement public interfaces which call a protected
 * implementation. Usually the public methods should not be overridden, while derived
 * classes may implement custom behaviours in the protected interface.
 *
 * This class implements the following interfaces:
 *
 * <ul>
 *	<li><i>Offsets</i>: In this class there cannot be an offset with a <i>NULL</i> value,
 *		the offset itself should be {@link offsetUnset() deleted} in that case. Because of
 *		this we also override the inherited behaviour by suppressing notices and warnings
 *		when {@link offsetGet() getting} non-existant offsets.
 *	<li><i>JSON encoding</i>: Derived classes will use JSON for web-services, so we provide
 *		two static methods to {@link JsonEncode() encode} and {@link JsonDecode() decode}
 *		JSON strings allowing for exceptions on errors.
 *	<li><i>String formatting</i>: We provide a generalised static method to
 *		{@link StringNormalise() format} strings which accepts a bitfield parameter that
 *		indicates which operation to perform, such as {@link kFLAG_MODIFIER_UTF8 UTF8}
 *		encode, {@link kFLAG_MODIFIER_LTRIM left} and {@link kFLAG_MODIFIER_RTRIM right}
 *		trim, {@link kFLAG_MODIFIER_NULL NULL} handling, {@link kFLAG_MODIFIER_NOCASE case}
 *		insensitive conversion, {@link kFLAG_MODIFIER_URL URL},
 *		{@link kFLAG_MODIFIER_HTML HTML} and {@link kFLAG_MODIFIER_HEX HEX} encoding and
 *		{@link kFLAG_MODIFIER_HASH hashing}.
 *	<li><i>Time formatting</i>: We provide a generalised static
 *		{@link DurationString() method} to display duration strings.
 * </ul>
 *
 *	@package	MyWrapper
 *	@subpackage	Core
 */
abstract class CObject
{
		

/*=======================================================================================
 *																						*
 *								STATIC ENCODING INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	JsonEncode																		*
	 *==================================================================================*/

	/**
	 * Return JSON encoded data.
	 *
	 * This method will return the provided array or object into a JSON encoded string.
	 *
	 * @param mixed					$theData			Input data.
	 *
	 * @static
	 * @return string
	 */
	public static function JsonEncode( $theData )
	{
		//
		// Encode json.
		//
		$json = @json_encode( $theData );
		
	/*	//
		// Special JSON encode.
		//
		$json = preg_replace( '/"(-?\d+\.?\d*)"/',
							  '$1',
							  @json_encode( $theData ) );
	*/	
		//
		// Handle errors.
		//
		switch( json_last_error() )
		{
			case JSON_ERROR_DEPTH:
				throw new CException
					( "JSON encode error: maximum stack depth exceeded",
					  kERROR_INVALID_STATE,
					  kMESSAGE_TYPE_WARNING,
					  array( 'Data' => $theData ) );							// !@! ==>

			case JSON_ERROR_CTRL_CHAR:
				throw new CException
					( "JSON encode error: unexpected control character found",
					  kERROR_INVALID_PARAMETER,
					  kMESSAGE_TYPE_WARNING,
					  array( 'Data' => $theData ) );							// !@! ==>

			case JSON_ERROR_SYNTAX:
				throw new CException
					( "JSON encode error: syntax error, malformed JSON",
					  kERROR_INVALID_PARAMETER,
					  kMESSAGE_TYPE_WARNING,
					  array( 'Data' => $theData ) );							// !@! ==>

			case JSON_ERROR_NONE:
				return $json;														// ==>
		}
	
	} // JsonEncode.

	 
	/*===================================================================================
	 *	JsonDecode																		*
	 *==================================================================================*/

	/**
	 * Return JSON decoded data.
	 *
	 * This method will return an array representation of the provided JSON string.
	 *
	 * @param string				$theData			Input data.
	 *
	 * @static
	 * @return array
	 */
	static function JsonDecode( $theData )
	{
		//
		// Decode JSON.
		//
		$decoded = @json_decode( $theData, TRUE );
		
		//
		// Handle errors.
		//
		switch( json_last_error() )
		{
			case JSON_ERROR_DEPTH:
				throw new CException
					( "JSON decode error: maximum stack depth exceeded",
					  kERROR_INVALID_STATE,
					  kMESSAGE_TYPE_WARNING,
					  array( 'Data' => $theData ) );							// !@! ==>

			case JSON_ERROR_CTRL_CHAR:
				throw new CException
					( "JSON decode error: unexpected control character found",
					  kERROR_INVALID_PARAMETER,
					  kMESSAGE_TYPE_WARNING,
					  array( 'Data' => $theData ) );							// !@! ==>

			case JSON_ERROR_SYNTAX:
				throw new CException
					( "JSON decode error: syntax error, malformed JSON",
					  kERROR_INVALID_PARAMETER,
					  kMESSAGE_TYPE_WARNING,
					  array( 'Data' => $theData ) );							// !@! ==>

			case JSON_ERROR_NONE:
				return $decoded;													// ==>
		}
	
	} // JsonDecode.

		

/*=======================================================================================
 *																						*
 *							STATIC STRING FORMATTING METHODS							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	StringNormalise																	*
	 *==================================================================================*/

	/**
	 * Normalise string.
	 *
	 * This method can be used to format a string, the provided modifiers bitfield
	 * determines what manipulations are applied:
	 *
	 * <ul>
	 *	<li><b>{@link kFLAG_MODIFIER_UTF8 kFLAG_MODIFIER_UTF8}</b>: Convert the string to
	 *		the <i>UTF8</i> character set.
	 *	<li><b>{@link kFLAG_MODIFIER_LTRIM kFLAG_MODIFIER_LTRIM}</b>: Apply left trimming to
	 *		the string.
	 *	<li><b>{@link kFLAG_MODIFIER_RTRIM kFLAG_MODIFIER_RTRIM}</b>: Apply right trimming
	 *		to the string.
	 *	<li><b>{@link kFLAG_MODIFIER_TRIM kFLAG_MODIFIER_TRIM}</b>: Apply both left and
	 *		right trimming to the string.
	 *	<li><b>{@link kFLAG_MODIFIER_NULL kFLAG_MODIFIER_NULL}</b>: If this flag is set and
	 *		the resulting string is empty, the method will return <i>NULL</i>.
	 *	 <ul>
	 *		<li><b>{@link kFLAG_MODIFIER_NULLSTR kFLAG_MODIFIER_NULLSTR}</b>: If this flag
	 *			is set and the resulting string is empty, the method will return the
	 *			'<i>NULL</i>' string; this option implies that the
	 *			{@link kFLAG_MODIFIER_NULL kFLAG_MODIFIER_NULL} is also set.
	 *	 </ul>
	 *	<li><b>{@link kFLAG_MODIFIER_NOCASE kFLAG_MODIFIER_NOCASE}</b>: Set the string to
	 *		lowercase, this is the default way to generate a case insensitive string.
	 *	<li><b>{@link kFLAG_MODIFIER_URL kFLAG_MODIFIER_URL}</b>: URL-encode the string;
	 *		note that this option and {@link kFLAG_MODIFIER_HTML kFLAG_MODIFIER_HTML} are
	 *		mutually exclusive.
	 *	<li><b>{@link kFLAG_MODIFIER_HTML kFLAG_MODIFIER_HTML}</b>: HTML-encode the string;
	 *		note that this option and {@link kFLAG_MODIFIER_URL kFLAG_MODIFIER_URL} are
	 *		mutually exclusive.
	 *	<li><b>{@link kFLAG_MODIFIER_HEX kFLAG_MODIFIER_HEX}</b>: Convert the string to
	 *		hexadecimal; note that this option and {@link kFLAG_MODIFIER_MASK_HASH hashing}
	 *		are mutually exclusive.
	 *	 <ul>
	 *		<li><b>{@link kFLAG_MODIFIER_HEXEXP kFLAG_MODIFIER_HEXEXP}</b>: Convert the
	 *			string to a hexadecimal expression; note that this option implies
	 *			{@link kFLAG_MODIFIER_HEX kFLAG_MODIFIER_HEX}, and this option and
	 *			{@link kFLAG_MODIFIER_MASK_HASH hashing} are mutually exclusive.
	 *	 </ul>
	 *	<li><b>{@link kFLAG_MODIFIER_HASH kFLAG_MODIFIER_HASH}</b>: If this bit is set
	 *		the resulting string will be hashed using the <i>md5</i> algorithm resulting in
	 *		a 32 character hexadecimal string; this option is mutually exclusive with the
	 *		{@link kFLAG_MODIFIER_MASK_HEX kFLAG_MODIFIER_MASK_HEX} option.
	 *	 <ul>
	 *		<li><b>{@link kFLAG_MODIFIER_HASH_BIN kFLAG_MODIFIER_HASH_BIN}</b>: If this bit
	 *			is set, the resulting value should be a 16 character binary string; if the
	 *			bit is <i>OFF</i>, the resulting value should be a 32 character hexadecimal
	 *			string.
	 *	 </ul>
	 * </ul>
	 *
	 * The order in which these modifications are applied are as stated.
	 *
	 * @param string			$theString		String to normalise.
	 * @param bitfield			$theModifiers	Modifiers bitfield.
	 *
	 * @static
	 * @return mixed
	 *
	 * @see kFLAG_DEFAULT, kFLAG_MODIFIER_MASK
	 * @see kFLAG_MODIFIER_UTF8
	 * @see kFLAG_MODIFIER_LTRIM, kFLAG_MODIFIER_RTRIM, kFLAG_MODIFIER_TRIM
	 * @see kFLAG_MODIFIER_NULL, kFLAG_MODIFIER_NULLSTR
	 * @see kFLAG_MODIFIER_NOCASE, kFLAG_MODIFIER_URL, kFLAG_MODIFIER_HTML
	 * @see kFLAG_MODIFIER_HEX, kFLAG_MODIFIER_HEXEXP
	 * @see kFLAG_MODIFIER_HASH, kFLAG_MODIFIER_HASH_BIN
	 */
	static function StringNormalise( $theString, $theModifiers = kFLAG_DEFAULT )
	{
		//
		// Check if any modification was requested.
		//
		if( ($theString === NULL)							// NULL string,
		 || ($theModifiers === kFLAG_DEFAULT)				// or no modifiers,
		 || (! $theModifiers & kFLAG_MODIFIER_MASK) )		// or none relevant.
			return $theString;														// ==>
		
		//
		// We know now that something is to be done with the string.
		//
		
		//
		// Convert to string.
		//
		$string = (string) $theString;
		
		//
		// Encode string to UTF8.
		//
		if( $theModifiers & kFLAG_MODIFIER_UTF8 )
		{
			if( ! mb_check_encoding( $string, 'UTF-8' ) )
				$string = mb_convert_encoding( $string, 'UTF-8' );
		}
		
		//
		// Trim.
		//
		if( $theModifiers & kFLAG_MODIFIER_MASK_TRIM )
		{
			if( ($theModifiers & kFLAG_MODIFIER_TRIM) == kFLAG_MODIFIER_TRIM ) 
				$string = trim( $string );
			elseif( $theModifiers & kFLAG_MODIFIER_LTRIM )
				$string = ltrim( $string );
			else
				$string = rtrim( $string );
		}
		
		//
		// Handle empty string.
		//
		if( (! strlen( $string ))
		 && ($theModifiers & kFLAG_MODIFIER_MASK_NULL) )
		{
			//
			// Set to NULL string.
			//
			if( ($theModifiers & kFLAG_MODIFIER_NULLSTR) == kFLAG_MODIFIER_NULLSTR )
				return 'NULL';														// ==>
			
			return NULL;															// ==>
		
		} // Empty string and NULL mask.
		
		//
		// Set case insensitive.
		//
		if( $theModifiers & kFLAG_MODIFIER_NOCASE )
			$string = ( $theModifiers & kFLAG_MODIFIER_UTF8 )
					? mb_convert_case( $string, MB_CASE_LOWER, 'UTF-8' )
					: strtolower( $string );
		
		//
		// URL-encode.
		//
		if( $theModifiers & kFLAG_MODIFIER_URL )
			$string = urlencode( $string );
		
		//
		// HTML-encode.
		//
		elseif( $theModifiers & kFLAG_MODIFIER_HTML )
			$string = htmlspecialchars( $string );
		
		//
		// handle HEX conversion.
		//
		if( $theModifiers & kFLAG_MODIFIER_MASK_HEX )
		{
			//
			// Convert to HEX string.
			//
			$string = bin2hex( $string );
			
			//
			// Convert to HEX expression.
			//
			if( ($theModifiers & kFLAG_MODIFIER_MASK_HEX) == kFLAG_MODIFIER_HEXEXP )
				$string = "0x$string";
		
		} // HEX mask.
		
		//
		// Hash string.
		//
		elseif( $theModifiers & kFLAG_MODIFIER_MASK_HASH )
		{
			if( ($theModifiers & kFLAG_MODIFIER_HASH_BIN) == kFLAG_MODIFIER_HASH_BIN )
				return md5( $string, TRUE );										// ==>
			
			return md5( $string );													// ==>
		}
		
		return $string;																// ==>
		
	} // StringNormalise.

	 
	/*===================================================================================
	 *	DurationString																	*
	 *==================================================================================*/
	
	/**
	 * Return a formatted duration.
	 *
	 * This function will return a formatted duration string in H:MM:SS:mmmmm format, where
	 * <i>H</i> stands for hours, <i>M</i> stands for minutes, <i>S</i> stands for seconds
	 * and <i>m</i> stands for milliseconds, from the value of <i>microtime( TRUE )</i>.
	 *
	 * <i>Note: The provided value should be a difference between two timestamps taken with
	 * microtime( true ).</i>
	 *
	 * @param float				$theTime		Microtime difference.
	 *
	 * @static
	 * @return string
	 */
	static function DurationString( $theTime )
	{
		$h = floor( $theTime / 3600.0 );
		$m = floor( ( $theTime - ( $h * 3600 ) ) / 60.0 );
		$s = floor( $theTime - ( ( $h * 3600 ) + ( $m * 60 ) ) );
		$l = $theTime - ( ( $h * 3600 ) + ( $m * 60 ) + $s );
		
		return sprintf( "%d:%02d:%02d:%04d", $h, $m, $s, ( $l * 10000 ) );			// ==>
	
	} // DurationString.

		

/*=======================================================================================
 *																						*
 *							STATIC MEMBER ACCESSOR INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	ManageMember																	*
	 *==================================================================================*/

	/**
	 * Manage a member.
	 *
	 * This library implements a standard interface for managing object properties using
	 * methods, this method implements this interface:
	 *
	 * <ul>
	 *	<li><b>$theMember</b>: The member to manage, it is a reference to the element being
	 *		managed.
	 *	<li><b>$theValue</b>: The value or operation:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the member's current value.
	 *		<li><i>FALSE</i>: Reset the member, <i>NULL</i> by default.
	 *		<li><i>other</i>: Any other type represents the member's new value.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value of the member <i>before</i> it was eventually
	 *			modified.
	 *		<li><i>FALSE</i>: Return the value of the member <i>after</i> it was eventually
	 *			modified.
	 *	 </ul>
	 * </ul>
	 *
	 * @param string			   &$theMember			Offset.
	 * @param mixed					$theValue			Value or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @static
	 * @return mixed
	 */
	static function ManageMember( &$theMember, $theValue = NULL, $getOld = FALSE )
	{
		//
		// Return current value.
		//
		if( $theValue === NULL )
			return $theMember;														// ==>

		//
		// Save current value.
		//
		$save = $theMember;
		
		//
		// Delete offset.
		//
		if( $theValue === FALSE )
			$theMember = NULL;
		
		//
		// Set offset.
		//
		else
			$theMember = $theValue;
		
		return ( $getOld ) ? $save													// ==>
						   : $theMember;											// ==>
	
	} // ManageMember.

	 

} // class CObject.


?>
