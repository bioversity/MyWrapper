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

//
// Test array indicies.
//
$arr = Array();
$arr[ 12.27 ] = 1;
$arr[ 123456.27 ] = 2;
echo( '<pre>' ); print_r( $arr ); echo( '</pre>' );
asort( $arr );
echo( '<pre>' ); print_r( $arr ); echo( '</pre>' );

//
// Test HTTP.
//
$x = new HttpRequest( 'http://www.apple.com/' );

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
$b = array( 'x' => 1 );
$c = array( 'x' => 1 );
$collection->insert( $a );
$collection->insert( $b );
$collection->insert( $c );
$curs = $collection->find();
$list = Array();
foreach( $curs as $elm )
	$list[] = $elm;
echo( '<pre>' ); print_r( $list ); echo( '</pre>' );

$crit = array( '_id' => $list[ 0 ][ '_id' ] );
$opt = array( 'safe' => TRUE );
$stat = $collection->remove( $crit, $opt );
echo( '<pre>' ); print_r( $stat ); echo( '</pre>' );
$stat = $collection->remove( $crit, $opt );
echo( '<pre>' ); print_r( $stat ); echo( '</pre>' );

//
// Test TAB-delimited.
//
$save = ini_set( 'auto_detect_line_endings', 1 );
$fp = fopen( 'export.txt', 'r' );
if( $fp !== FALSE )
{
	$test = fgetcsv( $fp, 4096, ',', '"' );
	echo( '<pre>' );
	print_r( $test );
	echo( '</pre>' );
	
	fclose( $fp );
}
ini_set( 'auto_detect_line_endings', $save );

//
// Test min/max.
//

//
// Collection selection.
//
$mongo = New Mongo();
$db = $mongo->selectDB( 'GERMPLASM' );
$collection = $db->selectCollection( 'VOCABULARY' );

//
// Build query.
//
$query = array( 'Dataset.Name' => 'OLS' );
//
// Build fields list.
//
$fields = array( 'Dataset.ID' => TRUE, '_id' => FALSE );
//
// Build sort list.
//
$sort = array( 'Dataset.ID' => -1 );

//
// Execute.
//
$max = $collection->find( $query, $fields )->sort( $sort )->limit( 1 )->getNext();
$max = $max[ 'Dataset' ][ 'ID' ];
echo( '<pre>' );
print_r( $max );
echo( '</pre>' );

//
// Test cast to int.
//
echo( "NULL: ".(integer) NULL.'<br>' );

//
// Neo4j queries.
//

//
// Includes.
//
require_once( "/Library/WebServer/Library/wrapper/includes.inc.php" );
require_once( "/Library/WebServer/Library/wrapper/classes/CGraphNode.inc.php" );

use Everyman\Neo4j\Transport,
	Everyman\Neo4j\Client,
	Everyman\Neo4j\Index\NodeIndex,
	Everyman\Neo4j\Index\RelationshipIndex,
	Everyman\Neo4j\Index\NodeFulltextIndex,
	Everyman\Neo4j\Node,
	Everyman\Neo4j\Relationship,
	Everyman\Neo4j\Batch;

//
// Get connected.
//
$db = new Everyman\Neo4j\Client( 'localhost', 7474 );

//
// Get indexes.
//
$term_index = new NodeIndex( $db, 'TERMS' );

//
// Get all outgoing relationships until root is found (language).
//
$level = 0;
$cache = Array();
$found = $term_index->findOne( ':TERM', 'ISO:639:3:Part3:pmr' );
$found = $found->getRelationships( NULL, Everyman\Neo4j\Relationship::DirectionOut );
foreach( $found as $edge )
	$cache[] = array( 'Subject' => $edge->getStartNode(),
					  'Predicate' => $edge->getType(),
					  'Object' => $edge->getEndNode() );
while( count( $cache ) )
{
	$current = array_shift( $cache );
	echo( $current[ 'Subject' ]->getProperty( ':TERM' ) );
	if( array_key_exists( 'Predicate', $current ) )
		echo( ' ==> '.$current[ 'Predicate' ] );
	if( array_key_exists( 'Object', $current ) )
		echo( ' ==> '.$current[ 'Object' ]->getProperty( ':TERM' ) );
	echo( '<br>' );
	$current = ( array_key_exists( 'Object', $current ) )
			 ? $current[ 'Object' ]
			 : $current[ 'Subject' ];
	$found = $current->getRelationships( NULL, Everyman\Neo4j\Relationship::DirectionOut );
	foreach( $found as $edge )
		$cache[] = array( 'Subject' => $edge->getStartNode(),
						  'Predicate' => $edge->getType(),
						  'Object' => $edge->getEndNode() );

} echo( '<hr>' );

//
// Get all outgoing relationships until roots are found (Italy).
//
$level = 0;
$cache = Array();
$found = $term_index->findOne( ':TERM', 'ISO:3166-1:ITA' );
$found = $found->getRelationships( NULL, Everyman\Neo4j\Relationship::DirectionOut );
foreach( $found as $edge )
	$cache[] = array( 'Subject' => $edge->getStartNode(),
					  'Predicate' => $edge->getType(),
					  'Object' => $edge->getEndNode() );
while( count( $cache ) )
{
	$current = array_shift( $cache );
	echo( $current[ 'Subject' ]->getProperty( ':TERM' ) );
	if( array_key_exists( 'Predicate', $current ) )
		echo( ' ==> '.$current[ 'Predicate' ] );
	if( array_key_exists( 'Object', $current ) )
		echo( ' ==> '.$current[ 'Object' ]->getProperty( ':TERM' ) );
	echo( '<br>' );
	$current = ( array_key_exists( 'Object', $current ) )
			 ? $current[ 'Object' ]
			 : $current[ 'Subject' ];
	$found = $current->getRelationships( NULL, Everyman\Neo4j\Relationship::DirectionOut );
	foreach( $found as $edge )
		$cache[] = array( 'Subject' => $edge->getStartNode(),
						  'Predicate' => $edge->getType(),
						  'Object' => $edge->getEndNode() );
}

//
// Test empty string index.
//
$test = Array();
$test[ 1 ] = 'Uno';
$test[ 'Due' ] = 2;
$test[ '' ] = 'NULLA';

//
// Show.
//
echo( '<pre>' );
print_r( $test );
echo( '</pre>' );

//
// Test index.
//
echo( '$test[ 1 ] ('.$test[ 1 ].')<br>' );
echo( '$test[ \'Due\' ] ('.$test[ 'Due' ].')<br>' );
echo( '$test[ \'\' ] ('.$test[ '' ].')<br>' );

//
// Collection selection.
//
$mongo = New Mongo();
$db = $mongo->selectDB( 'WAREHOUSE' );
$collection = $db->selectCollection( kDEFAULT_CNT_TERMS );

//
// Build query.
//
$query = array( ':NAME.:DATA' => new MongoRegex( '/italian/i' ) );

//
// Execute.
//
$cursor = $collection->find( $query );
foreach( $cursor as $element )
{
	echo( '<pre>' );
	print_r( $element );
	echo( '</pre>' );
}
*/


?>