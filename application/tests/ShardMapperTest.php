<?php

class ShardMapperTest extends PHPUnit_Framework_TestCase
{    
	public function testListShards()    
	{
		$shardRepository = ShardRepository::getInstance();
		$shards = $shardRepository->getAll();
		$this->assertTrue(count($shards) > 0, 'There should always be shards...');

		
	}
		
}
