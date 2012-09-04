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

//
// Neo4j tests.
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
// Create node.
//
echo( '<i>$start = $db->makeNode()->setProperty( \'NAME\', \'Start\' )->save();</i><br>' );
$start = $db->makeNode()->setProperty( 'NAME', 'Start' )->save();
$id = $start->getId();
echo( '<pre>' );
print_r( $start );
echo( '</pre><hr>' );

//
// Get node.
//
echo( '<i>$start = $db->getNode( $id );</i><br>' );
$start = $db->getNode( $id );
echo( '<pre>' );
print_r( $start );
echo( '</pre><hr>' );

//
// Test load.
//
echo( '<i>$start->load();</i><br>' );
$start->load();
echo( '<pre>' );
print_r( $start );
echo( '</pre><hr>' );

//
// Create node.
//
$end = $db->makeNode()->setProperty( 'NAME', 'End' )->save();

//
// Create edge.
//
echo( '<i>$edge = $start->relateTo( $end, \'RELATES\' )->setProperty( \'NAME\', \'Relationshit\' )->save();</i><br>' );
$edge = $start->relateTo( $end, 'RELATES' )->setProperty( 'NAME', 'Relationshit' )->save();
$id = $edge->getId();
echo( '<pre>' );
print_r( $edge );
echo( '</pre><hr>' );

//
// Get relationship.
//
echo( '<i>$edge = $db->getRelationship( $id );</i><br>' );
$edge = $db->getRelationship( $id );
echo( '<pre>' );
print_r( $edge );
echo( '</pre><hr>' );

//
// Check start node properties.
//
echo( '<i>$edge->getStartNode()->getProperties();</i><br>' );
echo( '<pre>' );
print_r( $edge->getStartNode()->getProperties() );
echo( '</pre><hr>' );

//
// Test load.
//
echo( '<i>$edge->load();</i><br>' );
$edge->load();
echo( '<pre>' );
print_r( $edge );
echo( '</pre><hr>' );

//
// Test load nodes.
//
echo( '<i>$edge->getStartNode()->load();</i><br>' );
$edge->getStartNode()->load();
echo( '<i>$edge->getEndNode()->load();</i><br>' );
$edge->getEndNode()->load();
echo( '<pre>' );
print_r( $edge );
echo( '</pre><hr>' );

//
// Collection selection.
//
$mongo = New Mongo();
$db = $mongo->selectDB( 'WAREHOUSE' );
$collection = $db->selectCollection( 'EDGES' );

//
// Build query.
//
$criteria = array( '_id' => 42 );
$modification = array( '$inc' => array( ':IN.:PREDICATE' => 1 ) );
$options = array( 'multiple' => false, 'safe' => true );

//
// Execute.
//
$ok = $collection->update( $criteria, $modification, $options );

echo( '<pre>' );
print_r( $ok );
echo( '</pre>' );

//
// Collection selection.
//
$mongo = New Mongo();
$db = $mongo->selectDB( 'WAREHOUSE' );
$collection = $db->selectCollection( 'EDGES' );

//
// Build query.
//
$query = array( ':OBJECT.:TERM' => 'ISO:3166:1:ALPHA-3',
				':SUBJECT.:TERM' => new MongoRegex( '/^ISO:3166:1:/' ),
				':PREDICATE.:TERM' => ':ENUM-OF' );
//
// Execute.
//
$found = $collection->find( $query );
echo( $found->count() );

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
// Create node with ID.
//
echo( '<i>$node = $db->getNode( 10000, TRUE );</i><br>' );
$node = $db->getNode( 10000, TRUE );
echo( '<pre>' );
print_r( $node );
echo( '</pre><hr>' );

//
// Check ID.
//
echo( '<i>$id = $node->getId();</i><br>' );
$id = $node->getId();
echo( "ID: $id<br>" );

//
// Load node.
//
echo( '<i>$node->setProperties( array( \'Uno\' => 1, \'Due\' => 2 ) );</i><br>' );
$node->setProperties( array( 'Uno' => 1, 'Due' => 2 ) );
echo( '<pre>' );
print_r( $node );
echo( '</pre><hr>' );

//
// Save node.
//
echo( '<i>$node->save();</i><br>' );
$node->save();
echo( '<pre>' );
print_r( $node );
echo( '</pre><hr>' );

//
// Test open exceptions.
//
$x = new SplFileObject( 'pippo' );

//
// GridFS test.
//
$mongo = New Mongo();
$db = $mongo->selectDB( 'TEST' );
$grid = $db->getGridFS( 'TEST' );

echo( 'Name: '.$grid->getName().'<br>' );

//
// Store file.
//
$file = __FILE__;
$id = $grid->storeFile( $file,
						array( 'metadata' => array( 'DATE' => new MongoDate() ) ),
						array( 'safe' => TRUE ) );
echo( '<pre>' );
print_r( $id );
echo( '</pre>' );
echo( '<pre>' );
print_r( (string) $id );
echo( '</pre>' );

//
// PHP 5.4 ArrayObject tests.
// Note that this implies having PHP 5.4 actove.
//

//
// Test class.
//
class MyClass extends ArrayObject
{
	public function test1()
	{
		return $this[ 'Two' ];
	}
	public function test2()
	{
		return $this[ 'Three' ][ 1 ];
	}
	public function test3( $theValue )
	{
		return $this[ 'Two' ] = $theValue;
	}
	public function test4( $theValue )
	{
		return $this[ 'Three' ][ 1 ] = $theValue;
	}
	public function test5( $theValue )
	{
		return $this[ 'Three' ][ 2 ][ 1 ] = $theValue;
	}
	public function & test6( $theOffset )
	{
		$ref = & $this[ $theOffset ];
		return $ref;
	}
	public function & test7( $theOffset1, $theOffset2 )
	{
		$ref = & $this[ $theOffset1 ][ $theOffset2 ];
		return $ref;
	}
	public function & test8( $theOffset1, $theOffset2, $theOffset3 )
	{
		$ref = & $this[ $theOffset1 ][ $theOffset2 ][ $theOffset3 ];
		return $ref;
	}
}

//
// Create content.
//
$array = array( 'One' => 1, 'Two' => 'Due', 'Three' => array( 1, 2, array( 'Uno', 'Due', 'Tre' ) ) );

//
// Create container.
//
$container = new MyClass( $array );

//
// Show.
//
echo( '<pre>' ); print_r( $container ); echo( '</pre>' );

//
// Test single level offset.
//
echo( '<i><b>(test1)</b> return $this[ \'Two\' ];</i><br>' );
echo( '<pre>' ); print_r( $container->test1() ); echo( '</pre>' );

//
// Test double level offset.
//
echo( '<i><b>(test2)</b> return $this[ \'Three\' ][ 1 ];</i><br>' );
echo( '<pre>' ); print_r( $container[ 'Three' ][ 1 ] ); echo( '</pre>' );

//
// Test single level reference.
//
echo( '<i><b>(test3)</b> $container[ \'Two\' ] = \'DUE\';</i><br>' );
$container->test3( 'DUE' );
echo( '<pre>' ); print_r( $container ); echo( '</pre>' );

//
// Test double level offset.
//
echo( '<i><b>(test4)</b> $container[ \'Three\' ][ 1 ] = \'DUE\';</i><br>' );
$container->test4( 'DUE' );
echo( '<pre>' ); print_r( $container ); echo( '</pre>' );

//
// Test triple level offset.
//
echo( '<i><b>(test5)</b> $container[ \'Three\' ][ 2 ][ 1 ] = 2222;</i><br>' );
$container->test5( 2222 );
echo( '<pre>' ); print_r( $container ); echo( '</pre>' );

//
// Test single level reference.
//
echo( '<i><b>(test6)</b> $ref = & $container[ \'Two\' ];</i><br>' );
$ref = & $container->test6( 'Two' );
echo( '<i>$ref = 2;</i><br>' );
$ref = 2;
echo( '<pre>' ); print_r( $container ); echo( '</pre>' );

//
// Test double level offset.
//
echo( '<i><b>(test7)</b> $ref = & $container[ \'Three\' ][ 1 ];</i><br>' );
$ref = & $container->test7( 'Three', 1 );
echo( '<i>$ref = 2;</i><br>' );
$ref = 2;
echo( '<pre>' ); print_r( $container ); echo( '</pre>' );

//
// Test triple level offset.
//
echo( '<i><b>(test8)</b> $ref = & $container[ \'Three\' ][ 2 ][ 1 ];</i><br>' );
$ref = & $container->test8( 'Three', 2, 1 );
echo( '<i>$ref = 2;</i><br>' );
$ref = 2;
echo( '<pre>' ); print_r( $container ); echo( '</pre>' );
exit;

//
// Test closures.
//
class MyTest
{
	public function test1( $a, $b, $func = NULL )
	{
		if( $func === NULL )
			$func = function( $a, $b )
					{
						return ((string) $a) == ((string) $b);
					};
		
		return ( $func($a, $b) ) ? 'YES' : 'NO';
	}
}

//
// Test.
//
$test = new MyTest;

echo( '<i>$test->test1( 1, 2 )</i><br />' );
echo( $test->test1( 1, 2 ).'<br>' );

echo( '<i>$test->test1( 1, 2, function( $a, $b ){ return ($a < $b); } )</i><br />' );
echo( $test->test1( 1, 2, function( $a, $b ){ return ($a < $b); } ).'<br>' );

//
// Test array navigation.
//
$test = array( 'Key1' => 1, 'Key2' => 2, 'Key3' => array( 1, 2, 3 ) );
echo( '<pre>' ); print_r( $test ); echo( '</pre>' );

echo( '<i>$x = reset( $test );</i><br>' );
$x = reset( $test );
echo( '<pre>' ); print_r( $x ); echo( '</pre>' );

echo( '<i>$x = next( $test );</i><br>' );
$x = next( $test );
echo( '<pre>' ); print_r( $x ); echo( '</pre>' );

echo( '<i>$x = next( $test );</i><br>' );
$x = next( $test );
echo( '<pre>' ); print_r( $x ); echo( '</pre>' );

echo( '<hr>' );

$test = array( 1, 2, array( 1, 2, 3 ), 4, array( 5, 50 ) );
echo( '<pre>' ); print_r( $test ); echo( '</pre>' );

echo( '<i>$x = reset( $test );</i><br>' );
$x = reset( $test );
echo( '<pre>' ); print_r( $x ); echo( '</pre>' );

echo( '<i>$x = next( $test );</i><br>' );
$x = next( $test );
echo( '<pre>' ); print_r( $x ); echo( '</pre>' );

echo( '<i>$x = next( $test );</i><br>' );
$x = next( $test );
echo( '<pre>' ); print_r( $x ); echo( '</pre>' );

echo( '<i>$x = next( $test );</i><br>' );
$x = next( $test );
echo( '<pre>' ); print_r( $x ); echo( '</pre>' );

echo( '<i>$x = next( $test );</i><br>' );
$x = next( $test );
echo( '<pre>' ); print_r( $x ); echo( '</pre>' );

//
// Test references in loops.
//
$test = array( 1, 2, 3 );
echo( '<pre>' ); print_r( $test ); echo( '</pre>' );

foreach( $test as &$data )
	$data *= 10;
echo( '<pre>' ); print_r( $test ); echo( '</pre>' );

$test = array( 'A' => array( array( 'A', 'B', 'C' ), array( 'D', 'E', 'F' ) ),
			   'B' => array( array( 'A', 'B', 'C' ), array( 'D', 'E', 'F' ) ) );
echo( '<pre>' ); print_r( $test ); echo( '</pre>' );

foreach( $test as &$data )
	$data[ 0 ][ 0 ] = 'PIPO';
echo( '<pre>' ); print_r( $test ); echo( '</pre>' );

//
// Test references in loops.
//
$test = array( 1, 2, 3 );
echo( '<pre>' ); print_r( $test ); echo( '</pre>' );

foreach( $test as &$data )
	$data *= 10;
echo( '<pre>' ); print_r( $test ); echo( '</pre>' );

$test = array( 'A' => array( array( 'A', 'B', 'C' ), array( 'D', 'E', 'F' ) ),
			   'B' => array( array( 'A', 'B', 'C' ), array( 'D', 'E', 'F' ) ) );
echo( '<pre>' ); print_r( $test ); echo( '</pre>' );

foreach( $test as &$data )
	$data[ 0 ][ 0 ] = 'PIPO';
echo( '<pre>' ); print_r( $test ); echo( '</pre>' );

//
// Trait inheritance.
//

trait trait_test
{
	public function test()
	{
		$this->pippo = "In trait test.";
	}
}

class MyTest
{
	use trait_test;
	
	public $pippo = NULL;
	
	public function test()
	{
		parent::test();
	}
}

$test = new MyTest();
$test->test();
echo( '<pre>' ); print_r( $test ); echo( '</pre>' );
*/

//
// Test APC.
//
$fruit  = 'apple';
$veggie = 'carrot';

apc_store('foo', $fruit);
apc_store('bar', $veggie);

if (apc_exists('foo')) {
    echo "Foo exists: ";
    echo apc_fetch('foo');
} else {
    echo "Foo does not exist";
}

echo PHP_EOL;
if (apc_exists('baz')) {
    echo "Baz exists.";
} else {
    echo "Baz does not exist";
}

echo PHP_EOL;

$ret = apc_exists(array('foo', 'donotexist', 'bar'));
var_dump($ret);


?>
