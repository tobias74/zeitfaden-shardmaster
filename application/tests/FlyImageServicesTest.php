<?php
use \Mockery as M;

class FlyImageServicesTest extends PHPUnit_Framework_TestCase
{
	const SOURCE_FILE = "/testdata/testsource.txt";
	const SOURCE_CONTENT = "testsring in csource file.";
	
	protected function setUp()
	{
		if (!file_exists(dirname(__FILE__)."/testdata"))
		{
			mkdir(dirname(__FILE__)."/testdata");
		}
		
		
		$handle = fopen(dirname(__FILE__).self::SOURCE_FILE, 'w');
		fwrite($handle, self::SOURCE_CONTENT);
		fclose($handle);
		
	}
	
	protected function teardown()
	{
		
		
		M::close();
	}

	
	
	public function testGetUrlForImage()
	{
		$stubFlyImage = M::mock('FlyImage');
		$stubFlyImage->shouldReceive('getFlyImagePath')->andReturn('yeah.jpg');
		
		
		$stubShard = M::mock('ZeitfadenSard');
		$stubShard->shouldReceive('getFlyFolderUrl')->andReturn('/flyFolderUrl');
		
		$stubSpec = M::mock('FlyImageSpecification');
		$stubSpec->shouldReceive('serialize')->andReturn('/serialized_something/');
		
		$stubRepository = M::mock('FlyImageRepository');
		$stubRepository->shouldReceive('getFlyImage')->with(55,'/userid/','/serialized_something/')->andReturn($stubFlyImage);
		
		$stubShardingService = M::mock('ZeitfadenShardingService');
		$stubShardingService->shouldReceive('getShardByUserId')->with('/userid/')->andReturn($stubShard);
		
		$this->flyImageService = new ZeitfadenFlyImageService('/userid/');
		$this->flyImageService->setFlyImageRepository($stubRepository);
		$this->flyImageService->setShardingService($stubShardingService);
		
		//$this->assertEquals('/flyFolderUrl/yeah.jpg',$this->flyImageService->getUrlForImage(55,$stubSpec), 'he path was not correct?');
		
		
	}
	

	
	public function testCreateFly()
	{
		/*
		$stubFile = M::mock('ZeitfadenFile');
		$stubFile->shouldReceive('getPathToFile')->andReturn('/somepath/');
		$stubFile->shouldReceive('getId')->andReturn(66);
		
		$stubShard = M::mock('ZeitfadenShard');
		$stubShard->shouldReceive('getFlyFolderUrl')->andReturn('/flyFolderUrl');
		
		$stubRepository = M::mock('FlyImageRepository');
		$stubRepository->shouldReceive('getFlyImage')->with(55,'/userid/','/serialized_something/')->andReturn($stubFlyImage);
		
		$stubShardingService = M::mock('ZeitfadenShardingService');
		$stubShardingService->shouldReceive('getShardByUserId')->with('/userid/')->andReturn($stubShard);
		
		$this->flyImageService = new ZeitfadenFlyImageService('/userid/');
		$this->flyImageService->setFlyImageRepository($stubRepository);
		$this->flyImageService->setShardingService($stubShardingService);
		
		//$this->assertEquals('/flyFolderUrl/yeah.jpg',$this->flyImageService->getUrlForImage(55,$stubSpec), 'he path was not correct?');
		
		*/
	}
	
	
	
}


