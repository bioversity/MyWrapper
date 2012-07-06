<?php

/**
 * <i>CDatasetFile</i> class definition.
 *
 * This file contains the class definition of <b>CDatasetFile</b> which wraps this class
 * {@link CArrayObject ancestor} around a {@link CDataset dataset} file reference.
 *
 *	@package	MyWrapper
 *	@subpackage	Traits
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 26/06/2012
*/

/*=======================================================================================
 *																						*
 *									CDatasetFile.php									*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CArrayObject.php" );

/**
 * Traits.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_TRAITS."TDataset.php" );

/**
 *	Dataset file.
 *
 * This class implements a dataset file, it wraps the {@link CArrayObject parent} class
 * around a structure that records a dataset file's attributes.
 *
 * In general instances of this class will be embedded in a {@link CDataset dataset} objects
 * to add lists of file references, the current class does not feature any persistence
 * functions, it only concentrates in managing a file reference as a set of properties:
 *
 * <ul>
 *	<li><i>{@link File() File}</i>: This {@link kOFFSET_FILE property} defines a file, it
 *		represents the GridFS record identifier for the file.
 *	<li><i>{@link Referenced() Referenced}</i>: This {@link kTAG_REFS property} holds the
 *		list of file {@link File() references}, this attribute all files that were generated
 *		taking the current file as a model. For instance, this list would contain all CSV
 *		files generated from all the workbooks of an Excel file.
 *	<li><i>{@link Status() Status}</i>: This {@link kTAG_STATUS property} holds the list of
 *		states in which the current file is.
 *	<li><i>{@link Kind() Kind}</i>: This {@link kTAG_KIND property} holds the list of kinds
 *		or types assigned to the file.
 *	<li><i>{@link Column() Columns}</i>: This {@link kOFFSET_COLS property} holds the list
 *		of column headers and associated metadata structured as follows:
 *	 <ul>
 *		<li><i>{@link kTAG_TAG kTAG_TAG}</i>: The {@link COntologyTag tag} associated with
 *			the data in the current column.
 *		<li><i>{@link Title() Title}</i>: The original header {@link kTAG_TITLE title}
 *			provided in the file and used for the annotation.
 *	 </ul>
 * </ul>
 *
 * You should always instantiate this class from the {@link CDataset dataset} by using its
 * {@link CDataset::NewFile() static} method rather than instantiating the class on its own,
 * this is because this way it is guaranteed that the record points to an existing file.
 *
 *	@package	MyWrapper
 *	@subpackage	Traits
 */
class CDatasetFile extends CArrayObject
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
	 * The constructor will instantiate an object either from an array, by loading all
	 * corresponding properties, or as an empty object.
	 *
	 * @param mixed					$theData			File structure.
	 *
	 * @access public
	 */
	public function __construct( $theData = NULL )
	{
		//
		// Empty statement.
		//
		if( $theData === NULL )
			parent::__construct();
		
		//
		// Handle provided statement.
		//
		elseif( is_array( $theData )
			 || ($theData instanceof ArrayObject) )
			parent::__construct( (array) $theData );

	} // Constructor.

		

/*=======================================================================================
 *																						*
 *								PUBLIC MEMBER INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Dataset																			*
	 *==================================================================================*/

	/**
	 * Manage dataset.
	 *
	 * This method can be used to manage the file's {@link CDataset dataset}
	 * {@link kTAG_LID reference} or the operation to be performed:
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
	 * @uses CAttribute::ManageOffset()
	 *
	 * @see kTAG_DATASET
	 */
	public function Dataset( $theValue = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageOffset
				( $this, kTAG_DATASET, $theValue, $getOld );						// ==>

	} // Dataset.

	 
	/*===================================================================================
	 *	Referenced																		*
	 *==================================================================================*/

	/**
	 * Manage referenced file references.
	 *
	 * This method can be used to handle the object's {@link kTAG_REFS references}, it uses
	 * the standard accessor {@link CAttribute::ManageArrayOffset() method} to manage the
	 * list of referenced files.
	 *
	 * Each element of this list should indicate a the eventual file that the system
	 * generated according to the current file: for instance, an Excel file will generate
	 * as many CSV files as it has non-empty worksheets.
	 *
	 * For a more thorough reference of how this method works, please consult the
	 * {@link CAttribute::ManageArrayOffset() CAttribute::ManageArrayOffset} method, in
	 * which the second parameter will be the constant {@link kTAG_REFS kTAG_REFS}.
	 *
	 * @param mixed					$theValue			Value or index.
	 * @param mixed					$theOperation		Operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses CAttribute::ManageArrayOffset()
	 *
	 * @see kTAG_REFS
	 */
	public function Referenced( $theValue = NULL, $theOperation = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageArrayOffset
					( $this, kTAG_REFS, $theValue, $theOperation, $getOld );		// ==>

	} // Referenced.

	 
	/*===================================================================================
	 *	Status																			*
	 *==================================================================================*/

	/**
	 * Manage the file status.
	 *
	 * This method can be used to handle the object's list of {@link kTAG_STATUS states}, it
	 * uses the standard accessor {@link CAttribute::ManageArrayOffset() method} to manage
	 * the list of status tags associated with the file.
	 *
	 * Each element of this list should indicate an object state, status or quality. This
	 * information indicates in which state the file is in.
	 *
	 * For a more thorough reference of how this method works, please consult the
	 * {@link CAttribute::ManageArrayOffset() CAttribute::ManageArrayOffset} method, in
	 * which the second parameter will be the constant {@link kTAG_STATUS kTAG_STATUS}.
	 *
	 * @param mixed					$theValue			Value or index.
	 * @param mixed					$theOperation		Operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses CAttribute::ManageArrayOffset()
	 *
	 * @see kTAG_STATUS
	 */
	public function Status( $theValue = NULL, $theOperation = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageArrayOffset
					( $this, kTAG_STATUS, $theValue, $theOperation, $getOld );		// ==>

	} // Status.

	 
	/*===================================================================================
	 *	Kind																			*
	 *==================================================================================*/

	/**
	 * Manage the file kind.
	 *
	 * This method can be used to handle the object's list of {@link kTAG_KIND kinds}, it
	 * uses the standard accessor {@link CAttribute::ManageArrayOffset() method} to manage
	 * the list of kind or type tags associated with the file.
	 *
	 * Each element of this list should indicate a kind or type associated with the object.
	 * This information indicates what kind or type of file the current object is.
	 *
	 * For a more thorough reference of how this method works, please consult the
	 * {@link CAttribute::ManageArrayOffset() CAttribute::ManageArrayOffset} method, in
	 * which the second parameter will be the constant {@link kTAG_KIND kTAG_KIND}.
	 *
	 * @param mixed					$theValue			Value or index.
	 * @param mixed					$theOperation		Operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses CAttribute::ManageArrayOffset()
	 *
	 * @see kTAG_KIND
	 */
	public function Kind( $theValue = NULL, $theOperation = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageArrayOffset
					( $this, kTAG_KIND, $theValue, $theOperation, $getOld );		// ==>

	} // Kind.

	 
	/*===================================================================================
	 *	Column																			*
	 *==================================================================================*/

	/**
	 * Manage the file columns.
	 *
	 * This method can be used to handle the object's list of {@link kOFFSET_COLS columns},
	 * it uses the standard accessor {@link CAttribute::ManageArrayOffset() method} to
	 * manage each of the file's column metadata.
	 *
	 * Each element of this list represents a column of the file and holds the following
	 * information:
	 *
	 * <ul>
	 *	<li><i>{@link kTAG_TAG kTAG_TAG}</i>: This element holds the
	 *		{@link kTAG_LID identifier} of the ontology {@link COntologyTag tag} associated
	 *		with the data contained in the current file's column.
	 *	<li><i>{@link kTAG_TITLE kTAG_TITLE}</i>: This element holds the original header
	 *		contents which were used to determine the column {@link COntologyTag tag}.
	 * </ul>
	 *
	 * The method accepts three parameters:
	 *
	 * <ul>
	 *	<li><b>$theColumn</b>: The column index as an integer (zero based), or the operation
	 *		to be performed on the whole list:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return all columns metadata.
	 *		<li><i>FALSE</i>: Delete all columns metadata, the method will return the old
	 *			value.
	 *		<li><i>other</i>: Any other type of value will be interpreted as an integer
	 *			indicating the column index (zero based) to be considered.
	 *	 </ul>
	 *	<li><b>$theTag</b>: The {@link kTAG_GID identifier} of the {@link COntologyTag tag}
	 *		associated with the current column, or the operation to be performed:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the full contents of the current column, the next
	 *			parameter is ignored in this case.
	 *		<li><i>FALSE</i>: Delete the current column, the next parameter is ignored in
	 *			this case.
	 *		<li><i>other</i>: Any other type of value will be interpreted as the
	 *			{@link COntologyTag tag} {@link kTAG_GID identifier} associated with the
	 *			column.
	 *	 </ul>
	 *	<li><b>$theTitle</b>: The original header contents used to annotate the column.
	 * </ul>
	 *
	 * @param integer				$theColumn			Column index.
	 * @param mixed					$theTag				Column tag or operation.
	 * @param string				$theTitle			Column original header.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses CAttribute::ManageArrayOffset()
	 *
	 * @see kOFFSET_COLS kTAG_TAG kTAG_TITLE
	 */
	public function Column( $theColumn = NULL, $theTag = NULL, $theTitle = NULL )
	{
		//
		// Save current columns.
		//
		$save = ( $this->offsetExists( kOFFSET_COLS ) )
			  ? (array) $this->offsetGet( kOFFSET_COLS )
			  : NULL;
		
		//
		// Return all values.
		//
		if( $theColumn === NULL )
			return $save;															// ==>
		
		//
		// Delete all values.
		//
		if( $theColumn === FALSE )
		{
			//
			// Delete offset.
			//
			if( $save !== NULL )
				$this->offsetUnset( kOFFSET_COLS );
			
			return $save;															// ==>
		
		} // Delete all columns.
		
		//
		// Cast column index.
		//
		$theColumn = (integer) $theColumn;
		
		//
		// Reference column.
		//
		$column = ( is_array( $save ) )
				? ( ( array_key_exists( $theColumn, $save ) )
				  ? $save[ $theColumn ]
				  : NULL )
				: NULL;
		
		//
		// Return column metadata.
		//
		if( $theTag === NULL )
			return $column;															// ==>
		
		//
		// Delete column.
		//
		if( $theTag === FALSE )
		{
			//
			// Check column.
			//
			if( $column !== NULL )
			{
				//
				// Delete column.
				//
				unset( $save[ $theColumn ] );
				
				//
				// Update offset.
				//
				$this->offsetSet( kOFFSET_COLS, array_values( $save ) );
			
			} // Column exists.
			
			return $column;															// ==>
		
		} // Delete column.
		
		//
		// Replace column tag.
		//
		if( $column !== NULL )
			$column[ kTAG_TAG ] = $theTag;
		
		//
		// Create column tag.
		//
		else
			$column = array( kTAG_TAG => $theTag );
		
		//
		// Set original header.
		//
		if( $theTitle !== NULL )
			$column[ kTAG_TITLE ] = $theTitle;
		
		//
		// Create offset.
		//
		if( $save === NULL )
			$this->offsetSet( kOFFSET_COLS, array( $column ) );
		
		//
		// Update offset.
		//
		else
		{
			//
			// Update columns.
			//
			$save[ $theColumn ] = $column;
			
			//
			// Update offset.
			//
			$this->offsetSet( kOFFSET_COLS, $save );
		
		} // Has offset.
		
		return $column;																// ==>

	} // Column.

	 

} // class CDatasetFile.


?>
