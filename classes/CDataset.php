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
 * Name and description.
 *
 * This include file contains the name and description trait definitions.
 */
require_once( kPATH_LIBRARY_TRAITS."TNameDescription.php" );

/**
 * Domain and category.
 *
 * This include file contains the domain and category trait definitions.
 */
require_once( kPATH_LIBRARY_TRAITS."TDomainCategory.php" );

/**
 * Creation and modification.
 *
 * This include file contains the creation and last modification trait definitions.
 */
require_once( kPATH_LIBRARY_TRAITS."TDateStamp.php" );

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
 * By default, the unique {@link _index() identifier} of the object is the combination of
 * the {@link User() user} {@link kTAG_LID identifier} and the dataset
 * {@link Title() title}.
 *
 * Objects of this class require at least the {@link kENTITY_USER user} and
 * {@link kTAG_TITLE title} offsets set to have an {@link _IsInited() initialised}
 * {@link kFLAG_STATE_INITED status}.
 *
 *	@package	MyWrapper
 *	@subpackage	Persistence
 */
class CDataset extends CRelatedUnitObject
{
		

/*=======================================================================================
 *																						*
 *										TRAITS											*
 *																						*
 *======================================================================================*/

	use
	 
	/*===================================================================================
	 *	Name and description															*
	 *==================================================================================*/

	/**
	 * Manage dataset name and description.
	 *
	 * These methods record the dataset name or label which can be expressed in several
	 * languages and also the dataset description and notes.
	 *
	 * The {@link Name() name} attribute is not to be confused with the @link TTitle title}, the
	 * latter represents the dataset identifier provided by the user, this attribute
	 * represents a name or label that can be used by humans to refer to this dataset.
	 */
	TNameDescription,
	 
	/*===================================================================================
	 *	Domain & Category																*
	 *==================================================================================*/

	/**
	 * Manage domains and categories.
	 *
	 * These two attributes represent respectively the {@link Domain() domains} covered by
	 * the dataset and the {@link Category() categories} to which the dataset belongs.
	 */
	TDomainCategory,
	 
	/*===================================================================================
	 *	Creation and modification dates													*
	 *==================================================================================*/

	/**
	 * Manage creation and modification dates.
	 *
	 * These two attributes represent respectively the dataset's {@link Created() creation}
	 * and the the dataset's last {@link Modified() modification} time-stamps.
	 */
	TDateStamp;

		

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
	 * The title represents a non language-based string which can be used to identify or
	 * provide a name to an object.
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
	 * Manage user.
	 *
	 * This method can be used to handle the object's {@link kENTITY_USER user}, it uses
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
	 * @return MongoId
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
		
		//
		// Enforce encoded flag.
		//
		$theModifiers |= kFLAG_STATE_ENCODED;
		
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
