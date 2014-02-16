<?php 

use \Mockery as M;


class ZeitfadenFacadeTest extends PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
		$stubUserCiteriaFactory = M::mock('UserCriteriaFactory');
		$stubUserCiteriaFactory->shouldReceive('getBySpecification')->andReturn(array('yeah'));
		
		$this->zeitfadenFacade = new ZeitfadenFacade();
		$this->zeitfadenFacade->setUserCriteriaFactory($stubUserCiteriaFactory);
		
	}
	
	protected function teardown()
	{
		M::close();
	}
	
	
	public function testEmailRegistration()    
	{
		$stubCriteria = M::mock('Criteria');
		
		$stubUserCiteriaFactory = M::mock('UserCriteriaFactory');
		$stubUserCiteriaFactory->shouldReceive('hasEmail')->andReturn($stubCriteria);
		$this->zeitfadenFacade->setUserCriteriaFactory($stubUserCiteriaFactory);

		$stubUserRepository = M::mock('ZeitfadenUserRepository');
		$stubUserRepository->shouldReceive('countByCriteria')->andReturn(0,1,2);
		$this->zeitfadenFacade->setUserRepository($stubUserRepository);
		
		
		$this->assertEquals($this->zeitfadenFacade->isEmailRegistered('dummy'), false, 'email registered problem??');
		$this->assertEquals($this->zeitfadenFacade->isEmailRegistered('dummy'), true, 'email registered problem???');
		

		
		try
		{
			$this->zeitfadenFacade->isEmailRegistered('dummy');
		}
		catch (ErrorException $e)
		{
			return;	
		}

		$this->fail('expected exception about two many emails has not ben raised.');
		
		
	}
	
}
