<?php

use \Mockery as M;

class UserRepositoryTest extends PHPUnit_Framework_TestCase
{
	const SHARD_ID = 6;
	
	protected function setUp()
	{
		$stubShard = M::mock();
		$stubShard->shouldReceive('getId')->andReturn(self::SHARD_ID);
		
		$stubShardingService = M::mock();
		$stubShardingService->shouldReceive('getShardForNewUser')->andReturn($stubShard);
		
		$stubMapper = M::mock();
		$stubMapper->shouldReceive('merge')->times(1)->andReturn(true);
		
		$stubMapperProvider = M::mock();
		$stubMapperProvider->shouldReceive('provide')->andReturn($stubMapper);
							   
		$this->userRepository = new ZeitfadenUserRepository();
		$this->userRepository->setShardingService($stubShardingService);
		$this->userRepository->setMapperProvider($stubMapperProvider);
		$this->userRepository->setNotificationCenter(new ZeitfadenNotificationCenter());
		$this->userRepository->setDatabaseShard('something');
		
	}
	
	protected function teardown()
	{
		M::close();
	}
	
	
	public function testMergeUser()    
	{
		
		$user = new ZeitfadenUser();
		$this->userRepository->merge($user);
		$this->assertEquals($user->getShardId(), self::SHARD_ID, 'Userid is not the good??');
		
	}
	
}
