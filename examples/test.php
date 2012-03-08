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
*/

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
	
?>