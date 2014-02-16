<?php

class ProfilerTest extends PHPUnit_Framework_TestCase
{    
	public function testCreateUser()    
	{
		$profiler = new GeneralProfiler();
		$timer = $profiler->startTimer('here');
		$timer->stop();
		
		$profilingIds = $profiler->getProfilingIds();
		$firstId = array_pop($profilingIds);
		
		$this->assertEquals($firstId, 'here', 'Userid is not the good??');
		
		$count = $profiler->getCount($firstId);
		$this->assertEquals($count, 1, 'Count of Timers ist wirrred?');
		
		
	}
		
}
