<?php
	
/*	
	//
	// Test stuff.
	//
	$x = new ArrayObject( array( 'id' => 1, 'Name' => 'Pippo' ) );
	
	@$x->offsetUnset( 'papa' );
	$r = @$x->offsetGet( 'papa' );
	
	if( $r === NULL )
		exit( "NULL\n" );
	
	exit( "??\n" );

	//
	// What's in MongoDBRef?
	//
	$x = new MongoDBRef();
	echo( '<pre>' );
	print_r( $x );
	echo( '</pre>' );
	
	$x[ 'pippo' ] = 'BABA';
	echo( '<pre>' );
	print_r( $x );
	echo( '</pre>' );

//
// Passing parameters by reference.
//
class MyTest
{
	 protected $member = NULL;
	 
	 public function &getMember()	{	return $this->member;	}
}

$test = new MyTest();
$v1 = $test->getMember();
$v2 = & $test->getMember();
$v3 = & $test->getMember();
echo( "v1: [$v1] v2: [$v2] v3: [$v3]<hr>" );

$v1 = 1;
echo( '<i>$v1 = 1;</i><br>' );
echo( "v1: [$v1] v2: [$v2] v3: [$v3]<hr>" );

$v2 = 2;
echo( '<i>$v2 = 2;</i><br>' );
echo( "v1: [$v1] v2: [$v2] v3: [$v3]<hr>" );

$v3 = 3;
echo( '<i>$v3 = 3;</i><br>' );
echo( "v1: [$v1] v2: [$v2] v3: [$v3]<hr>" );

//
// Last array key.
//
$test = Array();
$test[] = 'Uno';
$test[] = 'Due';
$test[ '1/2' ] = 'Due e mezzo';
$test[] = 'Tre';
unset( $test[ 1 ] );
echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
echo( '<i>Append element and get key.</i><br>' );
echo( '<i>$test[] = 231; end( $test ); $key = key( $test );</i><br>' );
$test[] = 231;
echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
end( $test );
$key = key( $test );
$value = $test[ $key ];
echo( $value.' = $test[ '.$key.' ];<br>' );

//
// Test iterator_to_array.
//

//
// Instantiate ArrayObject.
//
$ao = new ArrayObject();
$ao[ 'uno' ] = 1;
$ao[ 'due' ] = 2;
$ao[ 'tre' ] = 3;

//
// Get iterator.
//
//$it = iterator_to_array( $ao );
$it = (array) $ao;

echo( 'Object<pre>' ); print_r( $ao ); echo( '</pre>' );
echo( 'Iterator<pre>' ); print_r( $it ); echo( '</pre>' );
echo( '<hr>' );

//
// Make changes to iterator.
//
$it[ 'due' ] = 20;
echo( '<i>$it[ \'due\' ] = 20;</i>' );

echo( 'Object<pre>' ); print_r( $ao ); echo( '</pre>' );
echo( 'Iterator<pre>' ); print_r( $it ); echo( '</pre>' );

//
// Mongo dates?
//
require_once( "/Library/WebServer/Library/wrapper/includes.inc.php" );
require_once( "/Library/WebServer/Library/wrapper/classes/CDataTypeInt64.php" );

$d = new MongoDate();
echo( '<pre>' ); print_r( $d ); echo( '</pre>' );
echo( (string) $d );
echo( '<br>'.date('Y-M-d h:i:s', $d->sec ) );
echo( '<hr>' );

$x = microtime( true );
echo( "$x<br>" );
$d = new MongoDate( microtime( true ) );
echo( '<pre>' ); print_r( $d ); echo( '</pre>' );
echo( (string) $d );
echo( '<br>'.date('Y-M-d h:i:s', $d->sec ) );
echo( '<hr>' );

$d = new MongoDate( 1332329671, 966000 );
echo( '<pre>' ); print_r( $d ); echo( '</pre>' );
echo( (string) $d );
echo( '<br>'.date('Y-M-d h:i:s', $d->sec ) );
echo( '<hr>' );

$x = new DateTime( '2000-12-23 11:02:47' );
echo( '<pre>' ); print_r( $x ); echo( '</pre>' );
echo( '['.gettype( $x ).']' );
echo( '<hr>' );

$x = strtotime( '2000-12-23 11:02:47' );
echo( '<pre>' ); print_r( $x ); echo( '</pre>' );
echo( '['.gettype( $x ).']' );
echo( '<hr>' );

$sec = strtotime( "2011-12-31 23:59:59" );
$usec = 999999;
$d = new MongoDate( $sec, $usec );
echo( '<pre>' ); print_r( $d ); echo( '</pre>' );
echo( (string) $d );
echo( '<br>'.date('Y-M-d h:i:s', $d->sec ) );
echo( '<hr>' );
*/

//
// Instantiate Mongo database.
//
$mongo = New Mongo();
$db = $mongo->selectDB( "TEST" );
$db->drop();
$collection = $db->selectCollection( 'test' );

//
// Insert.
//
$a = array( 'x' => 1 );
$collection->insert( $a );
echo( '<pre>' ); print_r( $a ); echo( '</pre>' );

$a = array( 'x' => 2 );
$collection->save( $a );
echo( '<pre>' ); print_r( $a ); echo( '</pre>' );
echo( '<hr>' );

//
// Batch insert.
//
$l = array( array( 'x' => 1 ), array( 'x' => 2 ) );
$collection->batchInsert( $l );
echo( '<pre>' ); print_r( $l ); echo( '</pre>' );
echo( '<hr>' );

?>