<?php

use \Mockery as M;

class MockRegularRepository  extends AbstractRegularRepository
{
	
}


class RepositoryTest extends PHPUnit_Framework_TestCase
{
	const SHARD_ID = 6;
	
	protected function setUp()
	{
		$stubMapper = M::mock('mapper');
		$stubMapper->shouldReceive('getBySpecification')->andReturn(array('yeah'));
		
		$stubMapperProvider = M::mock('mapper');
		$stubMapperProvider->shouldReceive('provide')->andReturn($stubMapper);

		$stubCriteriaFactory = M::mock('criteriaFactory');
		$stubCriteriaFactory->shouldReceive('hasId');
		
		$this->userRepository = new MockRegularRepository();
		$this->userRepository->setMapperProvider($stubMapperProvider);
		$this->userRepository->setCriteriaFactory($stubCriteriaFactory);
		$this->userRepository->setDatabaseShard('something');
		
		
	}
	
	protected function teardown()
	{
		M::close();
	}
	
	
	public function testGetById()    
	{
		$user = $this->userRepository->getById(4);
		$this->assertEquals($user, 'yeah', 'User was not the right one??');
		
	}
		
}
