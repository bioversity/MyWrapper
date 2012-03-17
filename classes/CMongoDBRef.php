<?php

/**
 * <i>CMongoDBRef</i> class definition.
 *
 * This file contains the class definition of <b>CMongoDBRef</b> which implements the
 * MongoDBRef class as an instance.
 *
 *	@package	Framework
 *	@subpackage	Persistence
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 28/02/2012
 */

/*=======================================================================================
 *																						*
 *										CMongoDBRef.php									*
 *																						*
 *======================================================================================*/

/**
 * Ancestor.
 *
 * This include file contains the parent class definitions.
 */
require_once( kPATH_LIBRARY_SOURCE."CPersistentObject.php" );

/**
 * Mongo object reference.
 *
 * This class implements the MongoDBRef class as an instance: that is, a
 * {@link CPersistentObject CPersistentObject} derived object which contains as properties
 * the elements comprising a Mongo object reference.
 *
 * The object properties are the same as the MongoDBRef properties:
 *
 * <ul>
 *	<li><i>{@link kTAG_DATABASE_REFERENCE kTAG_DATABASE_REFERENCE}</i>: Database reference.
 *	<li><i>{@link kTAG_CONTAINER_REFERENCE kTAG_CONTAINER_REFERENCE}</i>: Collection
 *		reference.
 *	<li><i>{@link kTAG_ID_REFERENCE kTAG_ID_REFERENCE}</i>: Object identifier.
 * </ul>
 *
 * This class adds a new reference offset, {@link kTAG_CLASS kTAG_CLASS}, which is used to
 * instantiate the correct class when dereferencing; this property is either set
 * {@link ClassName() explicitly} or it is taken from the provided reference
 * {@link kTAG_CLASS offset} when {@link __construct() constructing} the object.
 *
 *	@package	Framework
 *	@subpackage	Persistence
 */
class CMongoDBRef extends CPersistentObject
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
	 * The constructor will instantiate an object reference in three ways:
	 *
	 * <ul>
	 *	<li><i>Empty reference</i>: Both parameters are omitted or both are <i>NULL</i>.
	 *		In this case you will have to build the reference by yourself.
	 *	<li><i>Object reference</i>: The first parameter is provided, the second is not.
	 *		In this case we assume the first parameter is already an object reference.
	 *	<li><i>Object</i>: Both parameters are provided, in this case we assume the first
	 *		parameter is the object to be referenced and the second parameter is the
	 *		collection from which it was taken; in this case we also record the object
	 *		{@link kTAG_CLASS class} in the reference.
	 * </ul>
	 *
	 * In the presence of both parameters, the provided collection takes the precedence
	 * over the referenced collection (and database).
	 *
	 * When providing either a reference or an object, the class
	 * {@link kTAG_CLASS reference} will be set only if the provided reference or object has
	 * it.
	 *
	 * The parameters to this method are:
	 *
	 * <ul>
	 *	<li><b>$theReference</b>: Either a reference or an object.
	 *	<li><b>$theContainer</b>: The collection in which the referenced object resides.
	 * </ul>
	 *
	 * @param mixed					$theReference		Object, or object reference.
	 * @param mixed					$theContainer		Object container.
	 *
	 * @access public
	 */
	public function __construct( $theReference = NULL, $theContainer = NULL )
	{
		//
		// Handle reference.
		//
		if( $theReference !== NULL )
		{
			//
			// Provided reference.
			//
			if( is_array( $theReference )
			 || ($theReference instanceof ArrayObject) )
			{
				//
				// Handle reference.
				//
				if( MongoDBRef::isRef( (array) $theReference ) )
				{
					//
					// Save reference.
					//
					$reference = (array) $theReference;
					
					//
					// Handle provided container.
					//
					if( $theContainer !== NULL )
					{
						//
						// Check container type.
						//
						if( $theContainer instanceof MongoCollection )
						{
							//
							// Set collection reference.
							//
							$reference[ kTAG_CONTAINER_REFERENCE ]
								= $theContainer->getName();
							
							//
							// Set database reference.
							//
							if( array_key_exists( kTAG_DATABASE_REFERENCE, $reference ) )
								$reference[ kTAG_DATABASE_REFERENCE ]
									= (string) $theContainer->db;
						
						} // Valid container type.
						
						else
							throw new CException
									( "The provided container parameter is invalid",
									  kERROR_INVALID_PARAMETER,
									  kMESSAGE_TYPE_ERROR,
									  array( 'Container' => $theContainer ) );	// !@! ==>
					
					} // Provided container.
				
				} // Provided reference.
				
				//
				// Handle object.
				//
				else
				{
					//
					// Init reference.
					//
					$reference = Array();
					
					//
					// Load object ID.
					//
					if( array_key_exists( kTAG_ID_NATIVE, (array) $theReference ) )
						$reference[ kTAG_ID_REFERENCE ] = $theReference[ kTAG_ID_NATIVE ];
					else
						throw new CException
								( "Unable to find object identifier",
								  kERROR_INVALID_STATE,
								  kMESSAGE_TYPE_ERROR,
								  array( 'Identifier' => kTAG_ID_NATIVE,
								  		 'Object' => $theReference ) );			// !@! ==>
					
					//
					// Handle provided container.
					//
					if( $theContainer !== NULL )
					{
						//
						// Check container type.
						//
						if( $theContainer instanceof MongoCollection )
							$reference[ kTAG_CONTAINER_REFERENCE ]
								= $theContainer->getName();
						
						else
							throw new CException
									( "The provided container parameter is invalid",
									  kERROR_INVALID_PARAMETER,
									  kMESSAGE_TYPE_ERROR,
									  array( 'Container' => $theContainer ) );	// !@! ==>
					
					} // Provided container.
					
					//
					// Set class.
					//
					if( array_key_exists( kTAG_CLASS, (array) $theReference ) )
						$reference[ kTAG_CLASS ] = $theReference[ kTAG_CLASS ];
				
				} // Provided object.
				
				//
				// Instantiate object.
				//
				parent::__construct( $reference );
			
			} // Valid reference or object.
			
			else
				throw new CException
						( "The provided reference or object parameter is invalid",
						  kERROR_INVALID_PARAMETER,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Reference' => $theReference ) );				// !@! ==>
		
		} // Provided reference.
		
		//
		// Instantiate empty object.
		//
		else
			parent::__construct();
		
		//
		// Set inited status.
		//
		if( MongoDBRef::isRef( (array) $this ) )
			$this->_IsInited( TRUE );
		
	} // Constructor.

		

/*=======================================================================================
 *																						*
 *								PUBLIC PERSISTENCE INTERFACE							*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Resolve																			*
	 *==================================================================================*/

	/**
	 * Resolve the object.
	 *
	 * This method should return the referenced object, it accepts a single parameter which
	 * should refer to the data store in which the referenced object is saved, in this case
	 * either a MongoCollection or a MongoDB.
	 *
	 * @param mixed					$theContainer		Data store.
	 *
	 * @access public
	 * @return mixed
	 */
	public function Resolve( $theContainer )
	{
		//
		// Check reference.
		//
		if( ! MongoDBRef::isRef( $this->getArrayCopy() ) )
			throw new CException
					( "The current reference cannot be used",
					  kERROR_INVALID_STATE,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Reference' => $this ) );							// !@! ==>

		//
		// Handle Mongo
		//
		if( $theContainer instanceof Mongo )
		{
			//
			// Check for database.
			//
			if( ($database = $this->Database()) === NULL )
				throw new CException
						( "Missing database reference",
						  kERROR_OPTION_MISSING,
						  kMESSAGE_TYPE_ERROR,
						  array( 'Reference' => $this ) );						// !@! ==>
			
			//
			// Get database.
			//
			$theContainer = $theContainer->selectDB( (string) $database );
		
		} // Provided Mongo.
		
		//
		// Handle MongoDB
		//
		if( $theContainer instanceof MongoDB )
			$theContainer = $theContainer->selectCollection( $this->Collection() );
		
		//
		// Enforce MongoCollection.
		//
		if( ! $theContainer instanceof MongoCollection )
			throw new CException
					( "Invalid container",
					  kERROR_INVALID_PARAMETER,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Container' => $theContainer ) );					// !@! ==>
		
		//
		// Resolve object.
		//
		$data = $theContainer->getDBRef( (array) $this );
		if( $data === NULL )
			return NULL;															// ==>
		
		//
		// Get class from reference.
		//
		$class = $this->ClassName();

		//
		// Get class from object.
		//
		if( $class === NULL )
		{
			//
			// Get class from object.
			//
			if( array_key_exists( kTAG_CLASS, $data ) )
				$class = $data[ kTAG_CLASS ];
			
			//
			// Return data.
			//
			else
				return $data;														// ==>
		
		} // Class not in reference.
		
		//
		// Instantiate.
		//
		$object = new $class( $data );
		
		//
		// Set committed flag.
		//
		if( $object instanceof CPersistentObject )
			$object->_IsCommitted( TRUE );
		
		return $object;																// ==>
		
	} // Resolve.

		

/*=======================================================================================
 *																						*
 *								PUBLIC MEMBER INTERFACE									*
 *																						*
 *======================================================================================*/


	 
	/*===================================================================================
	 *	Database																			*
	 *==================================================================================*/

	/**
	 * Manage database.
	 *
	 * This method can be used to manage the reference
	 * {@link kTAG_DATABASE_REFERENCE database}, it uses the standard accessor
	 * {@link _ManageOffset() method} to manage the {@link kTAG_DATABASE_REFERENCE offset}:
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
	 * @param NULL|FALSE|string		$theValue			User code or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 */
	public function Database( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kTAG_DATABASE_REFERENCE, $theValue, $getOld );	// ==>

	} // Database.

	 
	/*===================================================================================
	 *	Collection																		*
	 *==================================================================================*/

	/**
	 * Manage collection.
	 *
	 * This method can be used to manage the database
	 * {@link kTAG_CONTAINER_REFERENCE collection}, it uses the standard accessor
	 * {@link _ManageOffset() method} to manage the
	 * {@link kTAG_CONTAINER_REFERENCE offset}:
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
	 * @param NULL|FALSE|string		$theValue			User code or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 */
	public function Collection( $theValue = NULL, $getOld = FALSE )
	{
		//
		// Handle offset.
		//
		$result = $this->_ManageOffset( kTAG_CONTAINER_REFERENCE, $theValue, $getOld );
		
		//
		// Set inited status.
		//
		if( MongoDBRef::isRef( (array) $this ) )
			$this->_IsInited( TRUE );
		
		return $result;																// ==>

	} // Collection.

	 
	/*===================================================================================
	 *	Identifier																		*
	 *==================================================================================*/

	/**
	 * Manage identifier.
	 *
	 * This method can be used to manage the collection
	 * {@link kTAG_ID_REFERENCE identifier}, it uses the standard accessor
	 * {@link _ManageOffset() method} to manage the {@link kTAG_ID_REFERENCE offset}:
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
	 * @param NULL|FALSE|string		$theValue			User code or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 */
	public function Identifier( $theValue = NULL, $getOld = FALSE )
	{
		//
		// Handle offset.
		//
		$result = $this->_ManageOffset( kTAG_ID_REFERENCE, $theValue, $getOld );
		
		//
		// Set inited status.
		//
		if( MongoDBRef::isRef( (array) $this ) )
			$this->_IsInited( TRUE );
		
		return $result;																// ==>

	} // Identifier.

	 
	/*===================================================================================
	 *	ClassName																			*
	 *==================================================================================*/

	/**
	 * Manage referenced object class.
	 *
	 * This method can be used to manage the referenced object's {@link kTAG_CLASS class},
	 * it uses the standard accessor {@link _ManageOffset() method} to manage the
	 * {@link kTAG_CLASS offset}:
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
	 * @param NULL|FALSE|string		$theValue			User e-mail or operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access public
	 * @return string
	 */
	public function ClassName( $theValue = NULL, $getOld = FALSE )
	{
		return $this->_ManageOffset( kTAG_CLASS, $theValue, $getOld );				// ==>

	} // ClassName.

	 

} // class CMongoDBRef.


?>
