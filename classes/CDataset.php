<?php

/**
 * <i>CDataset</i> class definition.
 *
 * This file contains the class definition of <b>CDataset</b> which represents an object
 * that maps a dataset.
 *
 *	@package	MyWrapper
 *	@subpackage	Persistence
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 26/06/2012
 */

/*=======================================================================================
 *																						*
 *										CDataset.php									*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CRelatedUnitObject.php" );

/**
 * Grid container.
 *
 * This include file contains the file grid class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CMongoGridContainer.php" );

/**
 * Dataset object.
 *
 * Besides the inherited properties. datasets have the following attributes:
 *
 * <ul>
 *	<li><i>{@link Title() Title}</i>: The dataset {@link kTAG_TITLE title} represents the
 *		dataset name or identifier provided by the dataset creator, this attribute is used
 *		in the object's {@link LID() identifier}.
 *	<li><i>{@link User() User}</i>: The dataset {@link kENTITY_USER user} represents the
 *		{@link kTAG_LID identifier} of the {@link CUser user} that created the dataset, this
 *		attribute is used in the object's {@link LID() identifier}.
 *	<li><i>{@link Name() Name}</i>: The dataset {@link kTAG_NAME name} represents the name
 *		or label by which the dataset is referred to. Unlike the {@link Title() title} which
 *		has an identification purpose, this attribute has a documentation purpose and can be
 *		expressed in several languages.
 *	<li><i>{@link Description() Description}</i>: The dataset
 *		{@link kTAG_DESCRIPTION description} represents a description or definition of the
 *		dataset, it can be expressed in several languages.
 * </ul>
 *
 * By default, the unique {@link _index() identifier} of the object is its
 * {@link Code() code}, which is also its {@link _id() id}.
 *
 * Objects of this class require at least the {@link Code() code} {@link kTAG_CODE offset}
 * to have an {@link _IsInited() initialised} {@link kFLAG_STATE_INITED status}.
 *
 *	@package	MyWrapper
 *	@subpackage	Persistence
 */
class CDataset extends CRelatedUnitObject
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
	 * We {@link CPersistentObject::__construct() overload} the constructor to initialise
	 * the {@link _IsInited() inited} {@link kFLAG_STATE_INITED flag} if the
	 * {@link Code() code} attribute is set.
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
		// Call parent method.
		//
		parent::__construct( $theContainer, $theIdentifier, $theModifiers );
		
		//
		// Set inited status.
		//
		$this->_IsInited( $this->offsetExists( kTAG_CODE ) );
		
	} // Constructor.

		

/*=======================================================================================
 *																						*
 *								PUBLIC MEMBER INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	GID																				*
	 *==================================================================================*/

	/**
	 * Manage dataset global identifier.
	 *
	 * The term global {@link kTAG_GID identifier} represents the un-hashed version of the
	 * term local {@link kTAG_LID identifier}.
	 *
	 * This value is set automatically by a protected {@link _PrepareCommit() method}, so
	 * this method is read-only.
	 *
	 * @access public
	 * @return string
	 *
	 * @see kTAG_GID
	 */
	public function GID()									{	return $this[ kTAG_GID ];	}

	 
	/*===================================================================================
	 *	Title																			*
	 *==================================================================================*/

	/**
	 * Manage title.
	 *
	 * This method can be used to handle the object's {@link kTAG_TITLE title}, it uses the
	 * standard accessor {@link CAttribute::ManageOffset() method} to manage the
	 * {@link kTAG_TITLE offset}.
	 *
	 * The title represents the object's identifier provided by its creator, it should be
	 * unique within all datasets created by the same user.
	 *
	 * For a more in-depth reference of this method, please consult the
	 * {@link CAttribute::ManageOffset() ManageOffset} method, in which the first parameter
	 * will be the constant {@link kTAG_TITLE kTAG_TITLE}.
	 *
	 * @param mixed					$theValue			Value.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses CAttribute::ManageOffset()
	 *
	 * @see kTAG_TITLE
	 */
	public function Title( $theValue = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageOffset( $this, kTAG_TITLE, $theValue, $getOld );	// ==>

	} // Title.

	 
	/*===================================================================================
	 *	User																			*
	 *==================================================================================*/

	/**
	 * Manage title.
	 *
	 * This method can be used to handle the object's {@link kENTITY_USER title}, it uses
	 * the standard accessor {@link CAttribute::ManageOffset() method} to manage the
	 * {@link kENTITY_USER offset}.
	 *
	 * The user represents the object's creator, it should be provided either as an object
	 * {@link kTAG_LID identifier} or as an object itself.
	 *
	 * For a more in-depth reference of this method, please consult the
	 * {@link CAttribute::ManageOffset() CAttribute::ManageOffset} method, in which the
	 * second parameter will be the constant {@link kENTITY_USER kENTITY_USER}.
	 *
	 * In this class we feed the value to the
	 * {@link CPersistentUnitObject::NormaliseRelatedObject() NormaliseRelatedObject} method
	 * that will take care of handling object references.
	 *
	 * @param mixed					$theValue			Value.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses CAttribute::ManageOffset()
	 *
	 * @see kENTITY_USER
	 */
	public function User( $theValue = NULL, $getOld = FALSE )
	{
		//
		// Check identifier.
		//
		if( ($theValue !== NULL)
		 && ($theValue !== FALSE) )
			$theValue = CPersistentUnitObject::NormaliseRelatedObject( $theValue );
		
		return CAttribute::ManageOffset( $this, kENTITY_USER, $theValue, $getOld );	// ==>

	} // User.

	 
	/*===================================================================================
	 *	Name																			*
	 *==================================================================================*/

	/**
	 * Manage name.
	 *
	 * This method can be used to manage the dataset {@link kTAG_NAME name}, it manages an
	 * array of structures with the following offsets:
	 *
	 * <ul>
	 *	<li><i>{@link kTAG_LANGUAGE kTAG_LANGUAGE}</i>: The name's language, this element
	 *		represents the code of the language in which the next element is expressed in.
	 *	<li><i>{@link kTAG_DATA kTAG_DATA}</i>: The dataset name or label.
	 * </ul>
	 *
	 * The parameters to this method are:
	 *
	 * <ul>
	 *	<li><b>$theValue</b>: The name or operation:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the current value selected by the second parameter.
	 *		<li><i>FALSE</i>: Delete the value selected by the second parameter.
	 *		<li><i>other</i>: Set value selected by the second parameter.
	 *	 </ul>
	 *	<li><b>$theLanguage</b>: The name's language code:
	 *	 <ul>
	 *		<li><i>NULL</i>: This value indicates that the name has no language, in general,
	 *			when adding elements, this case applies to default elements.
	 *		<li><i>other</i>: All other types will be interpreted as the language code.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value <i>before</i> it was eventually modified.
	 *		<li><i>FALSE</i>: Return the value <i>after</i> it was eventually modified.
	 *	 </ul>
	 * </ul>
	 *
	 * @param mixed					$theValue			Term name or operation.
	 * @param mixed					$theLanguage		Term name language code.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses CAttribute::ManageTypedOffset()
	 *
	 * @see kTAG_NAME kTAG_LANGUAGE
	 */
	public function Name( $theValue = NULL, $theLanguage = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageTypedOffset( $this,
											  kTAG_NAME, kTAG_DATA,
											  kTAG_LANGUAGE, $theLanguage,
											  $theValue, $getOld );					// ==>

	} // Name.


	/*===================================================================================
	 *	Description																		*
	 *==================================================================================*/

	/**
	 * Manage dataset description.
	 *
	 * This method can be used to manage the {@link kTAG_DESCRIPTION description}, it
	 * manages an array of structures with the following offsets:
	 *
	 * <ul>
	 *	<li><i>{@link kTAG_LANGUAGE kTAG_LANGUAGE}</i>: The description's language, this
	 *		element represents the code of the language in which the next element is
	 *		expressed in.
	 *	<li><i>{@link kTAG_DATA kTAG_DATA}</i>: The dataset description or comment.
	 * </ul>
	 *
	 * The parameters to this method are:
	 *
	 * <ul>
	 *	<li><b>$theValue</b>: The description or operation:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the current value selected by the second parameter.
	 *		<li><i>FALSE</i>: Delete the value selected by the second parameter.
	 *		<li><i>other</i>: Set value selected by the second parameter.
	 *	 </ul>
	 *	<li><b>$theLanguage</b>: The description's language code:
	 *	 <ul>
	 *		<li><i>NULL</i>: This value indicates that the description has no language, in
	 *			general, when adding elements, this case applies to default elements.
	 *		<li><i>other</i>: All other types will be interpreted as the language code.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value <i>before</i> it was eventually modified.
	 *		<li><i>FALSE</i>: Return the value <i>after</i> it was eventually modified.
	 *	 </ul>
	 * </ul>
	 *
	 * @param mixed					$theValue			Term description or operation.
	 * @param mixed					$theLanguage		Term description language code.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses CAttribute::ManageTypedOffset()
	 *
	 * @see kTAG_DESCRIPTION kTAG_LANGUAGE
	 */
	public function Description( $theValue = NULL, $theLanguage = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageTypedOffset( $this,
											  kTAG_DESCRIPTION, kTAG_DATA,
											  kTAG_LANGUAGE, $theLanguage,
											  $theValue, $getOld );					// ==>

	} // Description.

	 
	/*===================================================================================
	 *	Domain																			*
	 *==================================================================================*/

	/**
	 * Manage domains.
	 *
	 * This method can be used to handle the object's {@link kTAG_DOMAIN domains}, it uses
	 * the standard accessor {@link CAttribute::ManageArrayOffset() method} to manage the
	 * list of domains.
	 *
	 * Each element of this list should indicate a domain to which the current object
	 * belongs to.
	 *
	 * For a more thorough reference of how this method works, please consult the
	 * {@link CAttribute::ManageArrayOffset() CAttribute::ManageArrayOffset} method, in
	 * which the second parameter will be the constant {@link kTAG_CATEGORY kTAG_CATEGORY}.
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
	 * @see kTAG_DOMAIN
	 */
	public function Domain( $theValue = NULL, $theOperation = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageArrayOffset
					( $this, kTAG_DOMAIN, $theValue, $theOperation, $getOld );		// ==>

	} // Domain.

	 
	/*===================================================================================
	 *	Category																		*
	 *==================================================================================*/

	/**
	 * Manage categories.
	 *
	 * This method can be used to handle the object's {@link kTAG_CATEGORY categories}, it
	 * uses the standard accessor {@link CAttribute::ManageArrayOffset() method} to manage
	 * the list of categories.
	 *
	 * Each element of this list should indicate a category to which the current object
	 * belongs to.
	 *
	 * For a more thorough reference of how this method works, please consult the
	 * {@link CAttribute::ManageArrayOffset() CAttribute::ManageArrayOffset} method, in
	 * which the second parameter will be the constant {@link kTAG_CATEGORY kTAG_CATEGORY}.
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
	 * @see kTAG_CATEGORY
	 */
	public function Category( $theValue = NULL, $theOperation = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageArrayOffset
					( $this, kTAG_CATEGORY, $theValue, $theOperation, $getOld );	// ==>

	} // Category.

	 
	/*===================================================================================
	 *	Created																			*
	 *==================================================================================*/

	/**
	 * Manage object creation time stamp.
	 *
	 * This method can be used to manage the object {@link kTAG_CREATED creation}
	 * time-stamp, it uses the standard accessor {@link CAttribute::ManageOffset() method}
	 * to manage the {@link kTAG_MODIFIED offset}:
	 *
	 * <ul>
	 *	<li><b>$theValue</b>: The value or operation:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the current value.
	 *		<li><i>FALSE</i>: Delete the value.
	 *		<li><i>other</i>: Set value.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value <i>before</i> it was eventually modified.
	 *		<li><i>FALSE</i>: Return the value <i>after</i> it was eventually modified.
	 *	 </ul>
	 * </ul>
	 *
	 * @param NULL|FALSE|string		$theValue			Object creation date.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses CAttribute::ManageOffset()
	 *
	 * @see kTAG_CREATED
	 */
	public function Created( $theValue = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageOffset( $this, kTAG_CREATED, $theValue, $getOld );	// ==>

	} // Created.

	 
	/*===================================================================================
	 *	Modified																		*
	 *==================================================================================*/

	/**
	 * Manage object last modification time stamp.
	 *
	 * This method can be used to manage the object last {@link kTAG_MODIFIED modification}
	 * time-stamp, or the date in which the last modification was made on the object, it
	 * uses the standard accessor {@link CAttribute::ManageOffset() method} to manage the
	 * {@link kTAG_MODIFIED offset}:
	 *
	 * <ul>
	 *	<li><b>$theValue</b>: The value or operation:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the current value.
	 *		<li><i>FALSE</i>: Delete the value.
	 *		<li><i>other</i>: Set value.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the value <i>before</i> it was eventually modified.
	 *		<li><i>FALSE</i>: Return the value <i>after</i> it was eventually modified.
	 *	 </ul>
	 * </ul>
	 *
	 * @param NULL|FALSE|string		$theValue			Object last modification date.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 *
	 * @uses CAttribute::ManageOffset()
	 *
	 * @see kTAG_MODIFIED
	 */
	public function Modified( $theValue = NULL, $getOld = FALSE )
	{
		return CAttribute::ManageOffset
					( $this, kTAG_MODIFIED, $theValue, $getOld );					// ==>

	} // Modified.

		

/*=======================================================================================
 *																						*
 *								PUBLIC OPERATIONS INTERFACE								*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	StoreFile																		*
	 *==================================================================================*/

	/**
	 * Store a file.
	 *
	 * This method can be used to store a file in a grid
	 * {@link CMongoGridContainer container}, it will take care of adding a
	 * {@link CDatasetFile::Dataset() reference} to the current dataset in the provided file
	 * metadata {@link CDatasetFile record}.
	 *
	 * This method accepts the following parameters:
	 *
	 * <ul>
	 *	<li><b>$theFile</b>: The file path or object (SplFileInfo).
	 *	<li><b>$theContainer</b>: The grid container in the form of a
	 *		{@link CMongoGridContainer CMongoGridContainer} or a MongoGridFS; any other
	 *		type will raise an exception.
	 *	<li><b>$theMetadata</b>: An array or preferably a {@link CDatasetFile CDatasetFile}
	 *		record containing the file's metadata, which will be added to the MongoGridFS
	 *		record under the "metadata" label. If the parameter is omitted, a
	 *		{@link CDatasetFile CDatasetFile} record will be initialised with a reference to
	 *		the current dataset.
	 *	<li><b>$theModifiers</b>: A bitfield containing the operation options, the only
	 *		relevant flag is {@link kFLAG_STATE_ENCODED kFLAG_STATE_ENCODED} which
	 *		determines whether custom data types are to be encoded
	 *		(set by default in this class).
	 * </ul>
	 *
	 * <b><i>The current dataset must have been {@link Commit() committed} before calling
	 * this method, or an exception will be raised: no files can be saved from this class
	 * without a valid dataset reference</i></b>.
	 *
	 * The mnethod will return the newly created file reference ID.
	 *
	 * @param string				$theFile			File path.
	 * @param CMongoGridContainer	$theContainer		File grid container.
	 * @param mixed					$theMetadata		File metadata.
	 * @param bitfield				$theModifiers		Operation modifiers.
	 *
	 * @access public
	 * @return CDatasetFile
	 *
	 * @uses CAttribute::ManageOffset()
	 *
	 * @see kTAG_TITLE
	 */
	public function StoreFile( $theFile, $theContainer, $theMetadata = NULL,
														$theModifiers = kFLAG_DEFAULT )
	{
		//
		// Check if committed.
		//
		if( ! $this->_IsCommitted() )
			throw new CException
					( "The dataset must be committed prior to storing files",
					  kERROR_INVALID_STATE,
					  kMESSAGE_TYPE_ERROR );									// !@! ==>
		
		//
		// Check container.
		//
		if( $theContainer instanceof MongoGridFS )
			$theContainer = new CMongoGridContainer( $theContainer );
		elseif( ! ($theContainer instanceof CMongoGridContainer) )
			throw new CException
					( "Unsupported container type",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Container' => $theContainer ) );					// !@! ==>
		
		//
		// Create metadata.
		//
		if( $theMetadata === NULL )
			$theMetadata = new CDatasetFile();
		elseif( is_array( $theMetadata ) )
			$theMetadata = new CDatasetFile( $theMetadata );
		elseif( ! ($theMetadata instanceof CDatasetFile) )
			throw new CException
					( "Unsupported metadata type",
					  kERROR_UNSUPPORTED,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Metadata' => $theMetadata ) );					// !@! ==>
		
		//
		// Convert file.
		//
		if( ! ($theFile instanceof SplFileInfo) )
			$theFile = new SplFileInfo( $theFile );
		
		//
		// Update metadata.
		//
		$theMetadata->Dataset( $this->offsetGet( kTAG_LID ) );
		
		return $theContainer->Commit( $theFile, $theMetadata, $theModifiers );		// ==>

	} // StoreFile.

		

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
	 * {@link kFLAG_STATE_INITED status}: this is set if {@link kTAG_CODE code} property is
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
			$this->_IsInited( $this->offsetExists( kTAG_TITLE )
						   && $this->offsetExists( kENTITY_USER ) );
	
	} // offsetSet.

	 
	/*===================================================================================
	 *	offsetUnset																		*
	 *==================================================================================*/

	/**
	 * Reset a value for a given offset.
	 *
	 * We overload this method to manage the {@link _IsInited() inited}
	 * {@link kFLAG_STATE_INITED status}: this is set if {@link kTAG_CODE code} property is
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
		$this->_IsInited( $this->offsetExists( kTAG_TITLE )
					   && $this->offsetExists( kENTITY_USER ) );
	
	} // offsetUnset.

		

/*=======================================================================================
 *																						*
 *								PROTECTED PERSISTENCE UTILITIES							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_PrepareCreate																	*
	 *==================================================================================*/

	/**
	 * Normalise parameters of a create.
	 *
	 * We overload this method to enforce the {@link kFLAG_STATE_ENCODED encoded} modifier.
	 *
	 * @param reference			   &$theContainer		Object container.
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Create modifiers.
	 *
	 * @access protected
	 *
	 * @uses _IsEncoded()
	 *
	 * @see kFLAG_STATE_ENCODED
	 */
	protected function _PrepareCreate( &$theContainer, &$theIdentifier, &$theModifiers )
	{
		//
		// Set encoded flag.
		//
		$theModifiers |= kFLAG_STATE_ENCODED;
		
		//
		// Call parent method.
		//
		parent::_PrepareCreate( $theContainer, $theIdentifier, $theModifiers );
	
	} // _PrepareCreate.

	 
	/*===================================================================================
	 *	_PrepareCommit																	*
	 *==================================================================================*/

	/**
	 * Normalise before a store.
	 *
	 * We {@link CPersistentUnitObject::_PrepareCommit() overload} this method to
	 * {@link Commit() commit} eventual object references stored as instances. We scan the
	 * {@link User() user} references and process all elements stored as instances.
	 *
	 * @param reference			   &$theContainer		Object container.
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Commit modifiers.
	 *
	 * @access protected
	 *
	 * @throws {@link CException CException}
	 *
	 * @see kERROR_OPTION_MISSING
	 */
	protected function _PrepareCommit( &$theContainer, &$theIdentifier, &$theModifiers )
	{
		//
		// Set encoded flag.
		//
		$theModifiers |= kFLAG_STATE_ENCODED;
		
		//
		// Call parent method.
		//
		parent::_PrepareCommit( $theContainer, $theIdentifier, $theModifiers );
		
		//
		// Handle users.
		//
		$this->_ParseReferences( kENTITY_USER, $theContainer, $theModifiers );
		
		//
		// Set global identifier.
		//
		$this[ kTAG_GID ] = $this->_index();
		
	} // _PrepareCommit.

		

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
	 * In this class we build the object {@link kTAG_LID ID} by concatenating the dataset
	 * dataset {@link User() creator} with the {@link Title() title} separated by the
	 * {@link kTOKEN_INDEX_SEPARATOR kTOKEN_INDEX_SEPARATOR} token.
	 *
	 * @access protected
	 * @return string
	 */
	protected function _index()
	{
		//
		// Get user identifier.
		//
		if( ($user = $this->User()) instanceof CUser )
			$user = (string) $user->_id();
			
		return $user.kTOKEN_INDEX_SEPARATOR.$this->Title();							// ==>
	
	} // _index.

	 

} // class CDataset.


?>
