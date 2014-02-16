<?php 
require_once dirname(__FILE__).'/phpunit_includer.php'; 

class AllTests
{
	
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('Project');
		$suite->addTest();
	}
	
	
}

