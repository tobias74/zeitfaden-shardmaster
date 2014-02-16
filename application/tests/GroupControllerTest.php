<?php

use \Mockery as M;

class GroupControllerTest extends PHPUnit_Framework_TestCase
{
	
	protected function setUp()
	{
		
		
	}
	
	protected function teardown()
	{
		M::close();
	}

	protected function getController($request, $response, $stubService)
	{
		$stubServiceProvider = M::mock('ComponentProvider');
		$stubServiceProvider->shouldReceive('provide')->andReturn($stubService);
		
		$groupController = new GroupController($request, $response);
		$groupController->setZeitfadenServiceProvider($stubServiceProvider);
		
		return $groupController;
	}
	
	
	public function testSetDescription()
	{
		$stubRequest = M::mock('ZeitfadenRequest');
		$stubRequest->shouldReceive('getParam')->with('/userId/', 0)->andReturn(5);
		$stubRequest->shouldReceive('getParam')->with('/groupId/',0)->andReturn(10);
		$stubRequest->shouldReceive('getParam')->with('/description/',0)->andReturn('/yesyeah/');

		$stubResponse = M::mock('ZeitfadenResponse');
		
		$stubGroup = M::mock('ZeitfadenGroup');
		$stubGroup->shouldReceive('setDescription')->once()->with('/yesyeah/');

		$stubService = M::mock('ZeitfadenSessionFacade');
		$stubService->shouldReceive('getGroupById')->with(10,5)->andReturn($stubGroup);
		$stubService->shouldReceive('mergeGroup')->once()->with($stubGroup);
		

		$controller = $this->getController($stubRequest, $stubResponse, $stubService);
		$controller->setDescriptionAction();
		
	}
	
	public function testGetById()
	{
		$stubRequest = M::mock('ZeitfadenRequest');
		$stubRequest->shouldReceive('getParam')->with('/userId/', 0)->andReturn(5);
		$stubRequest->shouldReceive('getParam')->with('/groupId/',0)->andReturn(10);

		$stubResponse = M::mock('ZeitfadenResponse');
		$stubResponse->shouldReceive('appendValue')->once()->with('/group/', 888);
		
		$stubService = M::mock('ZeitfadenSessionFacade');
		$stubService->shouldReceive('getGroupById')->once()->with(10,5)->andReturn(999);
		$stubService->shouldReceive('getGroupDTO')->once()->with(999)->andReturn(888);
		
		$controller = $this->getController($stubRequest, $stubResponse, $stubService);
		$controller->getByIdAction();
		
	}
	
}
