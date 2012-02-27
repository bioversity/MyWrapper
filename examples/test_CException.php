<?php

/**
 * {@link CException.php Exceptions} test suite.
 *
 * This file contains routines to test and demonstrate the behaviour of exceptions.
 *
 *	@package	Test
 *	@subpackage	Exceptions
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 04/03/2011
 */

/*=======================================================================================
 *																						*
 *									test_CException.php									*
 *																						*
 *======================================================================================*/

//
// Global includes.
//
require_once( '/Library/WebServer/Library/wrapper/includes.inc.php' );

//
// Class includes.
//
require_once( kPATH_LIBRARY_SOURCE."CException.php" );


/*=======================================================================================
 *	DECLARE TEST FUNCTIONS																*
 *======================================================================================*/
	
//
// Declare concrete instance.
//
class CTest
{
	public $mCustom = NULL;

	//
	// This method will will be called.
	//
	public function DoIt( $theParam1, $theParam2, $theOptions )
	{
		if( $theParam1 !== NULL )
			$this->mName = $theParam1;
		
		$this->Library( $theParam1, $theParam2, $theOptions );
	}
	
	//
	// This library will intercept the exception and throw a new CException.
	//
	protected function Library( $theParam1 = 'test',
								$theParam2 = NULL,
								$theOptions = NULL )
	{
		try
		{
			$this->SubLibrary( $theParam1, $theParam2, $theOptions );
		}
		catch( Exception $error )
		{
			throw new CException
				(
					"Level Library: Caught exception from SubFunction().",	// Message.
					-22,													// Code.
					kMESSAGE_TYPE_ERROR,									// Severity.
					array( 'Function' => 'SubLibrary()',					// References.
						   'Level' => 'Library',
						   'Object' => clone( $this ) ),
					$error													// Previous.
				);																// !@! ==>
		}
	}

	//
	// This sub-library will call a function.
	//
	protected function SubLibrary( $theParam1, $theParam2, $theOptions )
	{
		$this->Bunction( $theParam1, $theParam2, $theOptions );
	}

	//
	// This function will call a sub-function.
	//
	protected function Bunction( $theParam1, $theParam2, $theOptions )
	{
		$this->SubFunction( $theParam1, $theParam2, $theOptions );
	}

	//
	// This function will call a sub-function.
	//
	protected function SubFunction( $theParam1, $theParam2, $theOptions )
	{
		$this->Boutine( $theParam1, $theParam2, $theOptions );
	}

	//
	// This sub-function will call a routine.
	//
	private function Boutine( $theParam1, $theParam2, $theOptions )
	{
		try
		{
			$this->SubRoutine( $theParam1, $theParam2, $theOptions );
		}
		catch( Exception $error )
		{
			throw new CException
				(
					"Level Routine: Caught exception from SubRoutine().",	// Message.
					501,													// Code.
					kMESSAGE_TYPE_WARNING,									// Severity.
					array( 'Function' => 'SubRoutine()',					// References.
						   'Level' => 'Routine',
						   'Object' => clone( $this ) ),
					$error													// Previous.
				);																// !@! ==>
		}
	}

	//
	// This sub-routine will call a driver.
	//
	private function SubRoutine( $theParam1, $theParam2, $theOptions )
	{
		$this->Driver( $theParam1, $theParam2, $theOptions );
	}

	//
	// This driver will call a sub-driver.
	//
	private function Driver( $theParam1, $theParam2, $theOptions )
	{
		$this->SubDriver( $theParam1, $theParam2, $theOptions );
	}

	//
	// This sub-driver will generate the exception.
	//
	private function SubDriver( $theParam1, $theParam2, $theOptions )
	{
		throw new Exception( 'Unable to initialise the driver', -1 );			// !@! ==>
	}
	
} // class CTest.


/*=======================================================================================
 *	TEST DEFAULT EXCEPTIONS																*
 *======================================================================================*/
 
//
// Instantiate test class.
//
$test = new CTest();
$test1 = new CTest();

//
// Test exception.
//
try
{
	$test->DoIt( 'First run', 2.879, $test1 );
}
catch( Exception $error )
{
	echo( CException::AsHTML( $error ) );
	echo( '<pre>'.(string) $error.'</pre>' );
	echo( '<hr>' );
}

echo( "Done!<br />" );

?>
