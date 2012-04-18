<?php
	
/**
 * {@link CQueryStatement.php Query} object test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of the
 * {@link CQueryStatement class}.
 *
 *	@package	Test
 *	@subpackage	Persistence
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 03/04/2012
 */

/*=======================================================================================
 *																						*
 *									test_CQueryStatement.php							*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."CQueryStatement.php" );


/*=======================================================================================
 *	TEST QUERY STATEMENT OBJECT															*
 *======================================================================================*/
 
//
// TRY BLOCK.
//
try
{
	//
	// Instantiate empty query.
	//
	echo( '<i>$query = new CQuery();</i><br>' );
	$query = new CQuery();
	echo( '<pre>' ); print_r( $query ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Instantiate empty object.
	//
	echo( '<i>$test = new CQueryStatement();</i><br>' );
	$test = new CQueryStatement();
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Instantiate typeless object.
	//
	echo( '<i>$test = new CQueryStatement( \'SUBJECT\', kOPERATOR_EQUAL, NULL, new MongoDate() );</i><br>' );
	echo( '<i>Note the data type was inferred.</i><br>' );
	$test = new CQueryStatement( 'SUBJECT', kOPERATOR_EQUAL, NULL, new MongoDate() );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$query->AppendStatement( $test );</i><br>' );
	$query->AppendStatement( $test );
	echo( '<pre>' ); print_r( $query ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Instantiate disabled range statement.
	//
	echo( '<i>$test = CQueryStatement::Disabled( \'SUBJECT\', kTYPE_INT32, 10, 20 );</i><br>' );
	$test = CQueryStatement::Disabled( 'SUBJECT', 10, NULL, 20 );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$query->AppendStatement( $test );</i><br>' );
	$query->AppendStatement( $test );
	echo( '<pre>' ); print_r( $query ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Instantiate disabled typed range statement.
	//
	echo( '<i>$test = CQueryStatement::Disabled( \'SUBJECT\', new MongoDate( strtotime("2010-01-15 00:00:00") ), NULL, new MongoDate( strtotime("2012-04-03 12:30:15") ) );</i><br>' );
	$test = CQueryStatement::Disabled( 'SUBJECT',
									   new MongoDate( strtotime("2010-01-15 00:00:00") ),
									   NULL,
									   new MongoDate( strtotime("2012-04-03 12:30:15") ) );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$query->AppendStatement( $test, kOPERATOR_OR );</i><br>' );
	$query->AppendStatement( $test, kOPERATOR_OR );
	echo( '<pre>' ); print_r( $query ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Instantiate equality statement.
	//
	echo( '<i>$test = CQueryStatement::Equals( \'SUB\', 10.2 );</i><br>' );
	$test = CQueryStatement::Equals( 'SUB', 10.2 );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$query->AppendStatement( $test );</i><br>' );
	$query->AppendStatement( $test );
	echo( '<hr>' );
	
	//
	// Instantiate inequality statement.
	//
	echo( '<i>$test = CQueryStatement::NotEquals( \'SUB\', \'123\', kTYPE_INT32 );</i><br>' );
	$test = CQueryStatement::NotEquals( 'SUB', '123', kTYPE_INT32 );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$query->AppendStatement( $test );</i><br>' );
	$query->AppendStatement( $test );
	echo( '<hr>' );
	
	//
	// Instantiate LIKE statement.
	//
	echo( '<i>$test = CQueryStatement::Like( \'SUB\', 123 );</i><br>' );
	$test = CQueryStatement::Like( 'SUB', 123 );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$query->AppendStatement( $test );</i><br>' );
	$query->AppendStatement( $test );
	echo( '<hr>' );
	
	//
	// Instantiate not LIKE statement.
	//
	echo( '<i>$test = CQueryStatement::NotLike( \'SUB\', 123 );</i><br>' );
	$test = CQueryStatement::NotLike( 'SUB', 123 );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$query->AppendStatement( $test );</i><br>' );
	$query->AppendStatement( $test );
	echo( '<hr>' );
	
	//
	// Instantiate prefix statement.
	//
	echo( '<i>$test = CQueryStatement::Prefix( \'SUB\', 123 );</i><br>' );
	$test = CQueryStatement::Prefix( 'SUB', 123 );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$query->AppendStatement( $test );</i><br>' );
	$query->AppendStatement( $test );
	echo( '<hr>' );
	
	//
	// Instantiate contains statement.
	//
	echo( '<i>$test = CQueryStatement::Contains( \'SUB\', 123 );</i><br>' );
	$test = CQueryStatement::Contains( 'SUB', 123 );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$query->AppendStatement( $test );</i><br>' );
	$query->AppendStatement( $test );
	echo( '<hr>' );
	
	//
	// Instantiate suffix statement.
	//
	echo( '<i>$test = CQueryStatement::Suffix( \'SUB\', 123 );</i><br>' );
	$test = CQueryStatement::Suffix( 'SUB', 123 );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<hr>' );
	
	//
	// Instantiate regular expression statement.
	//
	echo( '<i>$test = CQueryStatement::Regex( \'SUB\', \'/^pippo$/i\' );</i><br>' );
	$test = CQueryStatement::Regex( 'SUB', '/^pippo$/i' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$query->AppendStatement( $test );</i><br>' );
	$query->AppendStatement( $test );
	echo( '<hr>' );
	
	//
	// Instantiate less than.
	//
	echo( '<i>$test = CQueryStatement::Less( \'SUB\', 12.3 );</i><br>' );
	$test = CQueryStatement::Less( 'SUB', 12.3 );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$query->AppendStatement( $test );</i><br>' );
	$query->AppendStatement( $test );
	echo( '<hr>' );
	
	//
	// Instantiate less than or equal.
	//
	echo( '<i>$test = CQueryStatement::LessEqual( \'SUB\', \'baba\' );</i><br>' );
	$test = CQueryStatement::LessEqual( 'SUB', 'baba' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$query->AppendStatement( $test );</i><br>' );
	$query->AppendStatement( $test );
	echo( '<hr>' );
	
	//
	// Instantiate greater than.
	//
	echo( '<i>$test = CQueryStatement::Great( \'SUB\', 12.3 );</i><br>' );
	$test = CQueryStatement::Great( 'SUB', 12.3 );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$query->AppendStatement( $test );</i><br>' );
	$query->AppendStatement( $test );
	echo( '<hr>' );
	
	//
	// Instantiate greater than or equal.
	//
	echo( '<i>$test = CQueryStatement::GreatEqual( \'SUB\', \'baba\' );</i><br>' );
	$test = CQueryStatement::GreatEqual( 'SUB', 'baba' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$query->AppendStatement( $test );</i><br>' );
	$query->AppendStatement( $test );
	echo( '<hr>' );
	
	//
	// Instantiate range inclusive.
	//
	echo( '<i>$test = CQueryStatement::RangeInclusive( \'SUB\', 10, 20 );</i><br>' );
	$test = CQueryStatement::RangeInclusive( 'SUB', 10, 20 );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$query->AppendStatement( $test );</i><br>' );
	$query->AppendStatement( $test );
	echo( '<hr>' );
	
	//
	// Instantiate range exclusive.
	//
	echo( '<i>$test = CQueryStatement::RangeExclusive( \'SUB\', 10, 20 );</i><br>' );
	$test = CQueryStatement::RangeExclusive( 'SUB', 10, 20 );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$query->AppendStatement( $test );</i><br>' );
	$query->AppendStatement( $test );
	echo( '<hr>' );
	
	//
	// Instantiate NULL.
	//
	echo( '<i>$test = CQueryStatement::Missing( \'SUB\' );</i><br>' );
	$test = CQueryStatement::Missing( 'SUB' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$query->AppendStatement( $test );</i><br>' );
	$query->AppendStatement( $test );
	echo( '<hr>' );
	
	//
	// Instantiate not NULL.
	//
	echo( '<i>$test = CQueryStatement::Exists( \'SUB\' );</i><br>' );
	$test = CQueryStatement::Exists( 'SUB' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$query->AppendStatement( $test );</i><br>' );
	$query->AppendStatement( $test );
	echo( '<hr>' );
	
	//
	// Instantiate member.
	//
	$list = array( new MongoDate( strtotime("2010-01-15 00:00:00") ),
				   new MongoDate( strtotime("2010-02-15 00:00:00") ),
				   new MongoDate( strtotime("2011-02-15 00:00:00") ),
				   new MongoDate() );
	echo( '<i>$test = CQueryStatement::Member( \'SUB\', $list );</i><br>' );
	$test = CQueryStatement::Member( 'SUB', $list );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$query->AppendStatement( $test );</i><br>' );
	$query->AppendStatement( $test );
	echo( '<hr>' );
	
	//
	// Instantiate not member.
	//
	$list = array( new MongoDate( strtotime("2010-01-15 00:00:00") ),
				   new MongoDate( strtotime("2010-02-15 00:00:00") ),
				   new MongoDate( strtotime("2011-02-15 00:00:00") ),
				   new MongoDate() );
	echo( '<i>$test = CQueryStatement::NotMember( \'SUB\', $list );</i><br>' );
	$test = CQueryStatement::NotMember( 'SUB', $list );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$query->AppendStatement( $test );</i><br>' );
	$query->AppendStatement( $test );
	echo( '<hr>' );
	
	//
	// Instantiate all.
	//
	$list = array( new MongoDate( strtotime("2010-01-15 00:00:00") ),
				   new MongoDate( strtotime("2010-02-15 00:00:00") ),
				   new MongoDate( strtotime("2011-02-15 00:00:00") ),
				   new MongoDate() );
	echo( '<i>$test = CQueryStatement::All( \'SUB\', $list );</i><br>' );
	$test = CQueryStatement::All( 'SUB', $list );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$query->AppendStatement( $test );</i><br>' );
	$query->AppendStatement( $test );
	echo( '<hr>' );
	
	//
	// Instantiate not all.
	//
	$list = array( new MongoDate( strtotime("2010-01-15 00:00:00") ),
				   new MongoDate( strtotime("2010-02-15 00:00:00") ),
				   new MongoDate( strtotime("2011-02-15 00:00:00") ),
				   new MongoDate() );
	echo( '<i>$test = CQueryStatement::NotAll( \'SUB\', $list );</i><br>' );
	$test = CQueryStatement::NotAll( 'SUB', $list );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$query->AppendStatement( $test );</i><br>' );
	$query->AppendStatement( $test );
	echo( '<hr>' );
	
	//
	// Instantiate expression.
	//
	echo( '<i>$test = CQueryStatement::Expression( \'SUB\', \'E=MC2\' );</i><br>' );
	$test = CQueryStatement::Expression( 'SUB', 'E=MC2' );
	echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
	echo( '<i>$query->AppendStatement( $test );</i><br>' );
	$query->AppendStatement( $test );
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
