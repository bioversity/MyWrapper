<?php
	
/**
 * {@link CMongoQuery.php Query} object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * base object {@link CMongoQuery class}.
 *
 *	@package	Test
 *	@subpackage	Persistence
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 22/12/2012
 */

/*=======================================================================================
 *																						*
 *									test_CMongoQuery.php								*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."CMongoQuery.php" );


/*=======================================================================================
 *	LOAD TEST OBJECTS																	*
 *======================================================================================*/
 
//
// Test queries.
//
$queries = array
(

	array
	(
		kOPERATOR_AND => array
		(
			array
			(
				kAPI_QUERY_SUBJECT => ':TYPE',
				kAPI_QUERY_OPERATOR => kOPERATOR_EQUAL,
				kAPI_QUERY_TYPE => kDATA_TYPE_STRING,
				kAPI_QUERY_DATA => ':TERM'
			),
			
			array
			(
				kAPI_QUERY_SUBJECT => ':XREF.:SCOPE',
				kAPI_QUERY_OPERATOR => kOPERATOR_EQUAL,
				kAPI_QUERY_TYPE => kDATA_TYPE_STRING,
				kAPI_QUERY_DATA => '2'
			)
		)
	),
	array
	(
		kOPERATOR_OR => array
		(
			array
			(
				kAPI_QUERY_SUBJECT => ':XREF.:DATA._code',
				kAPI_QUERY_OPERATOR => kOPERATOR_PREFIX,
				kAPI_QUERY_TYPE => kDATA_TYPE_STRING,
				kAPI_QUERY_DATA => 'NCBI_taxid:'
			),
			
			array
			(
				kAPI_QUERY_SUBJECT => ':XREF.:DATA._code',
				kAPI_QUERY_OPERATOR => kOPERATOR_PREFIX,
				kAPI_QUERY_TYPE => kDATA_TYPE_STRING,
				kAPI_QUERY_DATA =>  'GR:'
			)
		)
	),
	array
	(
		kOPERATOR_AND => array
		(
			array
			(
				kAPI_QUERY_SUBJECT => ':TYPE',
				kAPI_QUERY_OPERATOR => kOPERATOR_EQUAL,
				kAPI_QUERY_TYPE => kDATA_TYPE_STRING,
				kAPI_QUERY_DATA => ':TERM'
			),
			
			array
			(
				kAPI_QUERY_SUBJECT => ':XREF.:SCOPE',
				kAPI_QUERY_OPERATOR => kOPERATOR_EQUAL,
				kAPI_QUERY_TYPE => kDATA_TYPE_STRING,
				kAPI_QUERY_DATA => '2'
			),
			
			2 => array
			(
				kOPERATOR_OR => array
				(
					array
					(
						kAPI_QUERY_SUBJECT => ':XREF.:DATA._code',
						kAPI_QUERY_OPERATOR => kOPERATOR_PREFIX,
						kAPI_QUERY_TYPE => kDATA_TYPE_STRING,
						kAPI_QUERY_DATA => 'NCBI_taxid:'
					),
					
					array
					(
						kAPI_QUERY_SUBJECT => ':XREF.:DATA._code',
						kAPI_QUERY_OPERATOR => kOPERATOR_PREFIX,
						kAPI_QUERY_TYPE => kDATA_TYPE_STRING,
						kAPI_QUERY_DATA =>  'GR:'
					)
				)
			)
		)
	),
	array
	(
		kOPERATOR_AND => array
		(
			array
			(
				kAPI_QUERY_SUBJECT => '_id',
				kAPI_QUERY_OPERATOR => kOPERATOR_EQUAL,
				kAPI_QUERY_TYPE => kDATA_TYPE_MongoId,
				kAPI_QUERY_DATA => array
				(
					kTAG_TYPE => kDATA_TYPE_MongoId,
					kTAG_DATA => '4de8e3e7961be57b0900329a'
				)
			)
		)
	),
	array
	(
		kOPERATOR_AND => array
		(
			array
			(
				kAPI_QUERY_SUBJECT => ':REF-COUNT',
				kAPI_QUERY_OPERATOR => kOPERATOR_IRANGE,
				kAPI_QUERY_TYPE => kDATA_TYPE_INT32,
				kAPI_QUERY_DATA => array
				(
					array
					(
						kTAG_TYPE => kDATA_TYPE_INT32,
						kTAG_DATA => 186
					),
					array
					(
						kTAG_TYPE => kDATA_TYPE_INT32,
						kTAG_DATA => 103
					)
				)
			)
		)
	),
	array
	(
		kOPERATOR_AND => array
		(
			array
			(
				kAPI_QUERY_SUBJECT => ':XREF',
				kAPI_QUERY_OPERATOR => kOPERATOR_NOT_NULL
			)
		)
	),
	array
	(
		kOPERATOR_AND => array
		(
			array
			(
				kAPI_QUERY_SUBJECT => ':REF-COUNT',
				kAPI_QUERY_OPERATOR => kOPERATOR_NI,
				kAPI_QUERY_TYPE => kDATA_TYPE_INT32,
				kAPI_QUERY_DATA => array
				(
					array
					(
						kTAG_TYPE => kDATA_TYPE_INT32,
						kTAG_DATA => 186
					),
					array
					(
						kTAG_TYPE => kDATA_TYPE_INT32,
						kTAG_DATA => 103
					)
				)
			)
		)
	)
);

//
// Test statements.
//
$statements = array
(
	array
	(
		kAPI_QUERY_SUBJECT => ':REF-COUNT',
		kAPI_QUERY_OPERATOR => kOPERATOR_NI,
		kAPI_QUERY_TYPE => kDATA_TYPE_INT32,
		kAPI_QUERY_DATA => array
		(
			array
			(
				kTAG_TYPE => kDATA_TYPE_INT32,
				kTAG_DATA => 186
			),
			array
			(
				kTAG_TYPE => kDATA_TYPE_INT32,
				kTAG_DATA => 103
			)
		)
	),
	
	array
	(
		kAPI_QUERY_SUBJECT => ':XREF',
		kAPI_QUERY_OPERATOR => kOPERATOR_NOT_NULL
	),

	array
	(
		kAPI_QUERY_SUBJECT => ':XREF.:DATA._code',
		kAPI_QUERY_OPERATOR => kOPERATOR_PREFIX,
		kAPI_QUERY_TYPE => kDATA_TYPE_STRING,
		kAPI_QUERY_DATA => 'NCBI_taxid:'
	),
	
	array
	(
		kAPI_QUERY_SUBJECT => ':XREF.:DATA._code',
		kAPI_QUERY_OPERATOR => kOPERATOR_PREFIX,
		kAPI_QUERY_TYPE => kDATA_TYPE_STRING,
		kAPI_QUERY_DATA =>  'GR:'
	)
);


/*=======================================================================================
 *	TEST QUERY OBJECT																	*
 *======================================================================================*/
 
//
// TRY BLOCK.
//
try
{
	//
	// Instantiate empty object.
	//
	echo( '<b>$test = new CMongoQuery();</b>' );
	$test = new CMongoQuery();
	echo( '<pre>' );
	print_r( $test );
	echo( '</pre>' );
	
	//
	// Instantiate queries.
	//
	foreach( $queries as $key => $query )
	{
		try
		{
			echo( "<hr><b>$key</b><br>" );
			//
			// Instantiate.
			//
			$test = new CMongoQuery( $query );
			echo( '<pre>' );
			print_r( $test );
			echo( '</pre>' );
			$test->Validate();
			echo( 'OK!<br>' );
			
			//
			// Convert.
			//
			$converted = $test->Export();
			echo( '<pre>' );
			print_r( $converted );
			echo( '</pre>' );
		}
		catch( Exception $error )
		{
			echo( '<h3>Exception</h3>' );
			echo( CException::AsHTML( $error ) );
			echo( '<pre>'.(string) $error.'</pre>' );
			echo( '<hr>' );
		}
	}
	echo( '<hr>' );
	
	//
	// Try adding OR-based queries.
	//
	echo( 'Adding OR-based statements<br>' );
	$test = new CMongoQuery();
	$test->AppendStatement( $statements[ 0 ], kOPERATOR_OR );
	echo( $test->Debug().'<br>' );
	$test->AppendStatement( $statements[ 1 ], kOPERATOR_OR );
	echo( $test->Debug().'<br>' );
	$test->AppendStatement( $statements[ 2 ], kOPERATOR_AND );
	echo( $test->Debug().'<br>' );
	echo( '<hr>' );
	
	//
	// Try adding AND-based queries.
	//
	echo( 'Adding AND-based statements<br>' );
	$test = new CMongoQuery();
	$test->AppendStatement( $statements[ 0 ], kOPERATOR_AND );
	echo( $test->Debug().'<br>' );
	$test->AppendStatement( $statements[ 1 ], kOPERATOR_OR );
	echo( $test->Debug().'<br>' );
	$test->AppendStatement( $statements[ 2 ], kOPERATOR_OR );
	echo( $test->Debug().'<br>' );
	echo( '<hr>' );
	
	echo( '<h3>DONE</h3>' );
}
catch( Exception $error )
{
	echo( '<h3>Unexpected exception</h3>' );
	echo( CException::AsHTML( $error ) );
	echo( '<pre>'.(string) $error.'</pre>' );
	echo( '<hr>' );
}


?>
