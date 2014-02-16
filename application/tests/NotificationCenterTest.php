<?php 

use \Mockery as M;


class NotificationCenterTest extends PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
	
		$this->notificationCenter = new ZeitfadenNotificationCenter();
		
	}
	
	protected function teardown()
	{
		M::close();
	}
	
	protected function getCallbackUsingParameter($param)
	{
		$callback = function($value) use ($param)
		{
			if ($value->getPayload() === $param)
			{
				return true;
			}
			else
			{
				return false;
			}	
		};
		
		return $callback; 
	}

	protected function prepareNotificationCenter($noteName,$param)
	{
		$interestedStub = M::mock('NotificationListener');
		$interestedStub->shouldReceive('notify')->times(1)->with(\Mockery::on($this->getCallbackUsingParameter($param)));
		
		$this->notificationCenter->registerListener($noteName, $interestedStub);	
		
	}
	public function testEmailRegistration()    
	{
		
		$this->prepareNotificationCenter('testnote', 1);
		$this->notificationCenter->sendNotification('testnote',1);

		$this->prepareNotificationCenter('somemore',6);
		$this->notificationCenter->sendNotification('somemore',6);
		
		
		
		$this->notificationCenter->sendNotification('dummy','some');
		
		
		
	}
	
}
