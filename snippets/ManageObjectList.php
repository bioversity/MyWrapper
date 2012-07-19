	/*===================================================================================
	 *	ManageObjectList																*
	 *==================================================================================*/

	/**
	 * Manage a list of object references.
	 *
	 * This method can be used to manage a list of object references, in which each element
	 * is either:
	 *
	 * <ul>
	 *	<li><i>Scalar</i>: A scalar or object representing:
	 *	 <ul>
	 *		<li><i>The object</i>: The actual referenced object.
	 *		<li><i>The object reference</i>: An object reference structure or a scalar
	 *			representing the object's {@link kTAG_LID identifier}.
	 *	 </ul>
	 *		or:
	 *	<li><i>Array</i>: A structure composed of two items:
	 *	 <ul>
	 *		<li><i>Kind</i>: This offset represents the type or predicate of the reference.
	 *		<li><i>Data</i>: This offset represents the actual object or object reference.
	 *	 </ul>
	 * </ul>
	 *
	 * The reference list is numerically indexed array and this method will ensure it
	 * remains so.
	 *
	 * The method accepts the following parameters:
	 *
	 * <ul>
	 *	<li><b>&$theReference</b>: Reference to an array or ArrayObject derived instance.
	 *	<li><b>$theMainOffset</b>: The offset to manage.
	 *	<li><b>$theTypeOffset</b>: The element's offset of the type or predicate.
	 *	<li><b>$theDataOffset</b>: The element's offset of the data.
	 *	<li><b>$theValue</b>: This parameter represents either the search key in the list
	 *		when retrieving or deleting, or the reference when replacing or adding. If you
	 *		provide an array, it means that the elements may have a kind offset and that the
	 *		reference or object must be found in the data offset. When matching, if the kind
	 *		offset is not provided, it means that only those elements that do not have a
	 *		kind offset will be selected for matching. If the types match, the method will
	 *		use the {@link CPersistentUnitObject::ObjectIdentifier() ObjectIdentifier}
	 *		method to match the references, please refer to its documentation for more
	 *		information. If the provided value is not an array, it means that the reference
	 *		list does not feature types, so matches will only be performed on the reference.
	 *	<li><b>$theOperation</b>: The operation to perform:
	 *	 <ul>
	 *		<li><i>NULL</i>: Return the element matched by the previous parameter.
	 *		<li><i>FALSE</i>: Delete the element matched by the previous parameter and
	 *			return it.
	 *		<li><i>other</i>: Any other value means that we want to add to the list the
	 *			element provided in the previous parameter, either appending it if there
	 *			was no matching element, or by replacing a matching element. The method will
	 *			return either the replaced element or the new one.
	 *	 </ul>
	 *	<li><b>$getOld</b>: Determines what the method will return when deleting or
	 *		replacing:
	 *	 <ul>
	 *		<li><i>TRUE</i>: Return the deleted or replaced element.
	 *		<li><i>FALSE</i>: Return the replacing element or <i>NULL</i> when deleting.
	 *	 </ul>
	 * </ul>
	 *
	 * The {@link CPersistentUnitObject::ObjectIdentifier() method} used to match the list
	 * elements expects {@link kTAG_LID identifiers} in the references or objects, if these
	 * are not there, there is no way to discern duplicates.
	 *
	 * @param reference			   &$theReference		Object reference.
	 * @param string				$theMainOffset		Main offset.
	 * @param string				$theTypeOffset		Type offset.
	 * @param string				$theDataOffset		Data offset.
	 * @param mixed					$theValue			Reference or instance.
	 * @param mixed					$theOperation		Operation.
	 * @param boolean				$getOld				TRUE get old value.
	 *
	 * @access protected
	 * @return mixed
	 *
	 * @throws {@link CException CException}
	 */
	static function ManageObjectList( &$theReference,
									   $theMainOffset, $theTypeOffset, $theDataOffset,
									   $theValue, $theOperation = NULL,
									   $getOld = FALSE )
	{
		//
		// Check offset.
		//
		if( $theMainOffset === NULL )
			throw new CException
					( "Invalid offset",
					  kERROR_INVALID_PARAMETER,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Offset' => $theMainOffset ) );					// !@! ==>
		
		//
		// Check reference or instance.
		//
		if( $theValue === NULL )
			throw new CException
					( "Invalid reference or instance",
					  kERROR_INVALID_PARAMETER,
					  kMESSAGE_TYPE_ERROR,
					  array( 'Reference' => $theValue ) );						// !@! ==>
		
		//
		// Generate recursive calls.
		//
		if( is_array( $theValue )
		 && (! array_key_exists( $theDataOffset, $theValue )) )
		{
			//
			// Iterate arguments.
			//
			$result = Array();
			foreach( $theValue as $value )
				$result[]
					= self::ManageObjectList
						( $theReference,
						  $theMainOffset, $theTypeOffset, $theDataOffset,
						  $value, $theOperation, $getOld );
			
			return $result;															// ==>
		
		} // Execute list.
		
		//
		// Get typed reference matchers.
		//
		if( ( is_array( $theValue )
		   || ($theValue instanceof ArrayObject) )
		 && isset( $theValue[ $theDataOffset ] ) )
		{
			//
			// Set match type.
			//
			$type = ( array_key_exists( $theTypeOffset, (array) $theValue ) )
				  ? CPersistentUnitObject::ObjectIdentifier( $theValue[ $theTypeOffset ] )
				  : NULL;
			
			//
			// Set identifier.
			//
			$ident = CPersistentUnitObject::ObjectIdentifier( $theValue[ $theDataOffset ] );
		
		} // Typed reference.
		
		//
		// Get untyped reference matchers.
		//
		else
		{
			//
			// Reset type.
			//
			$type = FALSE;
			
			//
			// Set reference identifier.
			//
			$ident = CPersistentUnitObject::ObjectIdentifier( $theValue );
		
		} // Reference matcher.
		
		//
		// Save current offset.
		//
		$save = ( array_key_exists( $theMainOffset, (array ) $theReference ) )
			  ? $theReference[ $theMainOffset ]
			  : NULL;
		
		//
		// RETRIEVE.
		//
		if( $theOperation === NULL )
		{
			//
			// Check list.
			//
			if( $save !== NULL )
			{
				//
				// Iterate list.
				//
				foreach( $save as $value )
				{
					//
					// Untyped match.
					//
					if( $type === FALSE )
					{
						//
						// Match identifier.
						//
						if( $ident
							== (string)
								CPersistentUnitObject::ObjectIdentifier( $value ) )
							return $value;											// ==>
					
					} // Untyped match.
					
					//
					// Typed match.
					//
					else
					{
						//
						// Select matching structures.
						//
						if( ( is_array( $value )
						   || ($value instanceof ArrayObject) )
						 && array_key_exists( $theDataOffset, (array) $value ) )
						{
							//
							// Match type.
							//
							if( ($type !== NULL)
							 && array_key_exists( $theTypeOffset, (array) $value )
							 && ($type
							 	== (string)
							 		CPersistentUnitObject::ObjectIdentifier
							 			( $value[ $theTypeOffset ] )) )
							{
								//
								// Match identifier.
								//
								if( $ident
									== (string)
										CPersistentUnitObject::ObjectIdentifier
											( $value[ $theDataOffset ] ) )
									return $value;									// ==>
							
							} // Matched type.
							
							//
							// Match missing type.
							//
							elseif( ($type === NULL)
								 && (! array_key_exists( $theTypeOffset, (array) $value )) )
							{
								//
								// Match identifier.
								//
								if( $ident
									== (string)
										CPersistentUnitObject::ObjectIdentifier
											( $value[ $theDataOffset ] ) )
									return $value;									// ==>
							
							} // Matched missing type.
						
						} // Matched structure.
					
					} // Typed match.
				
				} // Iterating list.
			
			} // Have list.
			
			return NULL;															// ==>
		
		} // Retrieve.
		
		//
		// Handle delete.
		//
		if( $theOperation === FALSE )
		{
			//
			// Check list.
			//
			if( $save !== NULL )
			{
				//
				// Iterate list.
				//
				$found = NULL;
				$new = Array();
				foreach( $save as $value )
				{
					//
					// Untyped match.
					//
					if( $type === FALSE )
					{
						//
						// Match identifier.
						//
						if( $ident
							== (string)
								CPersistentUnitObject::ObjectIdentifier
									( $value ) )
						{
							//
							// Save match.
							//
							$found = $value;
							
							//
							// Iterate.
							//
							continue;										// =>
						
						} // matched identifier.
					
					} // Untyped match.
					
					//
					// Typed match.
					//
					else
					{
						//
						// Select matching structures.
						//
						if( ( is_array( $value )
						   || ($value instanceof ArrayObject) )
						 && array_key_exists( $theDataOffset, (array) $value ) )
						{
							//
							// Match type.
							//
							if( ($type !== NULL)
							 && array_key_exists( $theTypeOffset, (array) $value )
							 && ($type
							 	== (string)
							 		CPersistentUnitObject::ObjectIdentifier
							 			( $value[ $theTypeOffset ] )) )
							{
								//
								// Match identifier.
								//
								if( $ident
								 	== (string)
								 		CPersistentUnitObject::ObjectIdentifier
											( $value[ $theDataOffset ] ) )
								{
									//
									// Save match.
									//
									$found = $value;
									
									//
									// Iterate.
									//
									continue;								// =>
								
								} // matched identifier.
							
							} // Matched type.
							
							//
							// Match missing type.
							//
							elseif( ($type === NULL)
								 && (! array_key_exists( $theTypeOffset, (array) $value )) )
							{
								//
								// Match identifier.
								//
								if( $ident
								 	== (string)
								 		CPersistentUnitObject::ObjectIdentifier
											( $value[ $theDataOffset ] ) )
								{
									//
									// Save match.
									//
									$found = $value;
									
									//
									// Iterate.
									//
									continue;								// =>
								
								} // matched identifier.
							
							} // Matched missing type.
						
						} // Matched structure.
					
					} // Typed match.
					
					//
					// Save noon-matching elements.
					//
					$new[] = $value;
				
				} // Iterating list.
				
				//
				// Replace list.
				//
				if( $found !== NULL )
				{
					//
					// Remove offset.
					//
					if( ! count( $new ) )
						$theReference->offsetUnset( $theMainOffset );
					
					//
					// Replace offset.
					//
					else
						$theReference->offsetSet( $theMainOffset, $new );
				
				} // Matched.
				
				if( $getOld )
					return $found;													// ==>
			
			} // Have list.
			
			return NULL;															// ==>
		
		} // Delete.
		
		//
		// Replace value.
		//
		$found = NULL;
		if( $save !== NULL )
		{
			//
			// Iterate list.
			//
			foreach( $save as $key => $value )
			{
				//
				// Untyped match.
				//
				if( $type === FALSE )
				{
					//
					// Match identifier.
					//
					if( $ident
						== (string)
							CPersistentUnitObject::ObjectIdentifier
								( $value ) )
					{
						//
						// Save replaced.
						//
						$found = $value;
						
						//
						// Replace.
						//
						$save[ $key ] = $theValue;
						
						break;												// =>
						
					} // Matched.
				
				} // Untyped match.
				
				//
				// Typed match.
				//
				else
				{
					//
					// Select matching structures.
					//
					if( ( is_array( $value )
					   || ($value instanceof ArrayObject) )
					 && array_key_exists( $theDataOffset, (array) $value ) )
					{
						//
						// Match type.
						//
						if( ($type !== NULL)
						 && array_key_exists( $theTypeOffset, (array) $value )
						 && ($type
						 	== (string)
						 		CPersistentUnitObject::ObjectIdentifier
						 			( $value[ $theTypeOffset ] )) )
						{
							//
							// Match identifier.
							//
							if( $ident
								== (string)
									CPersistentUnitObject::ObjectIdentifier
										( $value[ $theDataOffset ] ) )
							{
								//
								// Save replaced.
								//
								$found = $value;
								
								//
								// Replace.
								//
								$save[ $key ] = $theValue;
								
								break;										// =>
								
							} // Matched.
						
						} // Matched type.
						
						//
						// Match missing type.
						//
						elseif( ($type === NULL)
							 && (! array_key_exists( $theTypeOffset, (array) $value )) )
						{
							//
							// Match identifier.
							//
							if( $ident
								== (string)
									CPersistentUnitObject::ObjectIdentifier
										( $value[ $theDataOffset ] ) )
							{
								//
								// Save replaced.
								//
								$found = $value;
								
								//
								// Replace.
								//
								$save[ $key ] = $theValue;
								
								break;										// =>
								
							} // Matched.
						
						} // Matched missing type.
					
					} // Matched structure.
				
				} // Typed match.
			
			} // Iterating list.
			
			//
			// Append new element.
			//
			if( $found === NULL )
				$save[] = $theValue;
		
		} // List exists.
		
		//
		// Build list.
		//
		else
			$save = array( $theValue );
		
		//
		// Create list.
		//
		$theReference->offsetSet( $theMainOffset, $save );
		
		if( $getOld )
			return $found;															// ==>
		
		return $theValue;															// ==>
	
	} // ManageObjectList.
