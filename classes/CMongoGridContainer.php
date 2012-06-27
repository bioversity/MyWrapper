<?php

/**
 * <i>CMongoGridContainer</i> class definition.
 *
 * This file contains the class definition of <b>CMongoGridContainer</b> which implements a
 * MongoDB file store.
 *
 *	@package	MyWrapper
 *	@subpackage	Persistence
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 27/06/2012
 */

/*=======================================================================================
 *																						*
 *									CMongoGridContainer.php								*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CMongoContainer.php" );

/**
 * Mongo persistent file store.
 *
 * This class extends its {@link CMongoContainer ancestor} to implement a file store based
 * on MongoGridFS containers.
 *
 *	@package	MyWrapper
 *	@subpackage	Persistence
 */
class CMongoGridContainer extends CMongoContainer
{
		

/*=======================================================================================
 *																						*
 *								PUBLIC MEMBER INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Container																		*
	 *==================================================================================*/

	/**
	 * Manage persistent container.
	 *
	 * We {@link CMongoContainer::Container() overload} this method to ensure that the
	 * provided container is a MongoGridFS object.
	 *
	 * @param mixed					$theValue			Persistent container or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return mixed
	 */
	public function Container( $theValue = NULL, $getOld = FALSE )
	{
		//
		// Handle retrieve or delete.
		//
		if( ($theValue === NULL)
		 || ($theValue === FALSE) )
			return parent::Container( $theValue, $getOld );							// ==>
		
		//
		// Check value.
		//
		if( $theValue instanceof MongoGridFS )
			return parent::Container( $theValue, $getOld );							// ==>
		
		throw new CException( "Invalid container type",
							  kERROR_INVALID_PARAMETER,
							  kMESSAGE_TYPE_ERROR,
							  array( 'Container' => $theValue ) );				// !@! ==>

	} // Container.

		

/*=======================================================================================
 *																						*
 *								PUBLIC PERSISTENCE INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Commit																			*
	 *==================================================================================*/

	/**
	 * Commit provided object.
	 *
	 * We {@link CMongoContainer::Commit() overload} this method to handle committing files
	 * as opposed to documents. We handle the provided parameters as follows:
	 *
	 * <ul>
	 *	<li><b>$theObject</b>: This parameter represents the file to be stored:
	 *	 <ul>
	 *		<li><i>SplFileInfo</i>: In this case the parameter is interpreted as a file path,
	 *			it will be resolved with the <i>getRealPath()</i> method.
	 *		<li><i>other</i>: In all other cases the parameter is interpreted as the file
	 *			contents provided as a binary string.
	 *	 </ul>
	 *	<li><b>$theIdentifier</b>: If provided, this parameter is interpreted as the file's
	 *		metadata and will be enclosed in an array labeled <i>metadata</i>.
	 *	<li><b>$theModifiers</b>: This parameter is initialised to
	 *		{@link kFLAG_PERSIST_INSERT kFLAG_PERSIST_INSERT}, since this is the default and
	 *		only operation possible.
	 * </ul>
	 *
	 * The method will return the object's key within the container or raise an exception if
	 * the operation was not successful.
	 *
	 * The only reason to overload this method is to write this documentation and set the
	 * default modifier, there is no functional difference between this method and its
	 * {@link CMongoContainer::Commit() inherited} version.
	 *
	 * @param reference			   &$theObject			Object to commit.
	 * @param mixed					$theIdentifier		Object identifier.
	 * @param bitfield				$theModifiers		Commit modifiers.
	 *
	 * @access public
	 * @return mixed
	 *
	 * @uses _PrepareCommit()
	 * @uses _Commit()
	 * @uses _FinishCommit()
	 *
	 * @see kFLAG_PERSIST_INSERT kFLAG_STATE_ENCODED
	 */
	public function Commit( &$theObject,
							 $theIdentifier = NULL,
							 $theModifiers = kFLAG_PERSIST_INSERT )
	{
		return parent::Commit( $theObject, $theIdentifier, $theModifiers );			// ==>

	} // Commit.

		

/*=======================================================================================
 *																						*
 *								PROTECTED MANAGEMENT INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_Commit																			*
	 *==================================================================================*/

	/**
	 * Commit provided object.
	 *
	 * We implement this method to handle MongoGridFS object stores, this method will store
	 * the object in the current container.
	 *
	 * This particular container type only supports creating new records, that is, files are
	 * added to the grid and cannot be modified. Also, files can be added either by path or
	 * by content, depending on the provided object parameter.
	 *
	 * The parameters accepted by this method are:
	 *
	 * <ul>
	 *	<li><b>&$theObject</b>: This parameter holds the file to be added:
	 *	 <ul>
	 *		<li><i>SplFileInfo</i>: In this case the parameter is interpreted as a file path,
	 *			it will be resolved with the <i>getRealPath()</i> method.
	 *		<li><i>other</i>: In all other cases the parameter is interpreted as the file
	 *			contents provided as a binary string.
	 *	 </ul>
	 *	<li><b>$theIdentifier</b>: This parameter is interpreted as the file's metadata and
	 *		has been normalised before this method.
	 *	<li><b>$theModifiers</b>: We ignore this parameter.
	 * </ul>
	 *
	 * The
	 *
	 * @param reference			   &$theObject			Object to commit.
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Commit modifiers.
	 *
	 * @access protected
	 * @return mixed
	 *
	 * @uses Container()
	 */
	protected function _Commit( &$theObject, &$theIdentifier, &$theModifiers )
	{
		//
		// Init local storage.
		//
		$container = $this->Container();
		$options = array( 'safe' => TRUE );
		
		//
		// Add by path.
		//
		if( $theObject instanceof SplFileInfo )
			return $container->storeFile( $theObject->getRealPath(),
										  $theIdentifier,
										  $options );								// ==>
		
		return $container->storeBytes( $theObject, $theIdentifier, $options );		// ==>
	
	} // _Commit.

		

/*=======================================================================================
 *																						*
 *								PROTECTED PERSISTENCE UTILITIES							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	_PrepareCommit																	*
	 *==================================================================================*/

	/**
	 * Prepare before a {@link _Commit() commit}.
	 *
	 * We {@link CMongoContainer::_PrepareCommit() override} this method to reset the
	 * parameters that are not relevant to the operation, that is, we set the identifier to
	 * an empty array if <i>NULL</i> or enclose it in a <i>metadata</i> array if not and
	 * force the modifiers to {@link kFLAG_PERSIST_INSERT kFLAG_PERSIST_INSERT}.
	 *
	 * We also check if the native container is of the correct type.
	 *
	 * Note that we do not call the parent method, but the grandpa method explicitly.
	 *
	 * @param reference			   &$theObject			Object or data.
	 * @param reference			   &$theIdentifier		Object identifier.
	 * @param reference			   &$theModifiers		Commit modifiers.
	 *
	 * @access protected
	 *
	 * @throws {@link CException CException}
	 *
	 * @uses Container()
	 *
	 * @see kFLAG_PERSIST_INSERT kFLAG_STATE_ENCODED
	 * @see kERROR_OPTION_MISSING kERROR_INVALID_PARAMETER kERROR_INVALID_STATE
	 */
	protected function _PrepareCommit( &$theObject, &$theIdentifier, &$theModifiers )
	{
		//
		// Set default operation.
		//
		$theModifiers = ($theModifiers & (~kFLAG_PERSIST_MASK)) | kFLAG_PERSIST_INSERT;
		
		//
		// Save identifier.
		//
		$save = $theIdentifier;
		$theIdentifier = NULL;
		
		//
		// Call ancestor method.
		//
		CContainer::_PrepareCommit( $theObject, $theIdentifier, $theModifiers );
		
		//
		// Restore identifier.
		//
		if( ($theModifiers & kFLAG_STATE_ENCODED)
		 && ( is_array( $save  )
		   || ($save instanceof ArrayObject) ) )
			$this->UnserialiseObject( $save );
		if( $save === NULL )
			$theIdentifier = Array();
		elseif( $save instanceof ArrayObject )
			$theIdentifier = array( 'metadata' => $save->getArrayCopy() );
		else
			$theIdentifier = array( 'metadata' => $save );
	
	} // _PrepareCommit.

	 

} // class CMongoGridContainer.


?>
