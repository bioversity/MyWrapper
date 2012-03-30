<?php
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );
require_once( '/Library/WebServer/Library/wrapper/classes/CMongoDataWrapper.php' );

function rest_helper( $theUrl, $theParams = NULL, $theMode = 'GET', $theFormat = 'json')
{
	//
	// Build context parameters.
	//
	$cxp = array( 'http' => array( 'method' => $theMode,
								   'ignore_errors' => TRUE ) );
	
	//
	// Set parameters.
	//
	if( $theParams !== NULL )
	{
		//
		// Format parameters.
		//
		$theParams = http_build_query( $theParams );
		
		//
		// handle mode.
		//
		if( $theMode == 'POST' )
			$cxp[ 'http' ][ 'content' ] = $theParams;
		else
			$theUrl .= ('?'.$theParams);
	}
	
	//
	// Create context.
	//
	$context = stream_context_create( $cxp );
	
	//
	// Open stream.
	//
	$fp = fopen( $theUrl, 'rb', FALSE, $context );
	if( ! $fp )
	    throw new Exception( "[$theMode] [$theUrl] failed: [php_errormsg]" );	// !@! ==>
	
	//
	// Debug stream.
	//
	else
	{
		// If you're trying to troubleshoot problems, try uncommenting the
		// next two lines; it will show you the HTTP response headers across
		// all the redirects:
		// $meta = stream_get_meta_data( $fp );
		// var_dump( $meta[ 'wrapper_data' ]);
		
		//
		// Read stream.
		//
		$result = stream_get_contents( $fp );
	}
	
	//
	// Close stream.
	//
	fclose( $fp );
	
	//
	// Format response.
	//
	switch( $theFormat )
	{
		case 'json':
			$response = json_decode( $result );
			if( $response !== NULL )
			    return $response;													// ==>
	        throw new Exception( "Failed to decode [$result] as json" );		// !@! ==>

		case 'xml':
			$response = simplexml_load_string( $result );
			if( $response !== NULL )
			    return $response;													// ==>
	        throw new Exception( "Failed to decode [$result] as xml" );			// !@! ==>
	}
	
	return $result;																	// ==>

}

//
// Build parameters.
//
$params = array( kAPI_FORMAT => kDATA_TYPE_JSON, kAPI_OPERATION => kAPI_OP_HELP );

//
// My test.
//
$result = rest_helper( 'http://localhost/newwrapper/MongoDataWrapper.php',
					   $params,
					   'POST',
					   'json' );
					   
echo( '<pre>' );
print_r( $result );
exit( '</pre>' );


// This lists projects by Ed Finkler on GitHub:
foreach (
    rest_helper('http://github.com/api/v2/json/repos/show/funkatron')
    ->repositories as $repo) {
  echo $repo->name, "<br>\n";
  echo htmlentities($repo->description), "<br>\n";
  echo "<hr>\n";
}
echo "<hr>\n";
echo "<hr>\n";

// This incomplete snippet demonstrates using POST with the Disqus API
var_dump(
  rest_helper(
    "http://disqus.com/api/thread_by_identifier/",
    array(
      'api_version' => '1.1',
      'user_api_key' => $my_disqus_api_key,
      'identifier' => $thread_unique_id,
      'forum_api_key' => $forum_api_key,
      'title' => 'HTTP POST from PHP, without cURL',
    ), 'POST'
  )
);

?>
