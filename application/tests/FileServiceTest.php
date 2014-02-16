<?php

use \Mockery as M;

class FileServiceTest extends PHPUnit_Framework_TestCase
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

	public function testStoringAndReadingFile()
	{
		/*
		$stubShard = M::mock('ZeitfadenSard');
		$stubShard->shouldReceive('getPathForFiles')->andReturn('/targetTest');
		
		$stubShardingService = M::mock('ZeitfadenShardingService');
		$stubShardingService->shouldReceive('getShardByUserId')->with('/testuserid/')->andReturn($stubShard);

		$stubSystemService = M::mock('ZeitfadenSystemWrapper');
		$stubSystemService->shouldReceive('copy')->andReturn(true);
		$stubSystemService->shouldReceive('file_exists')->andReturn(false);
		$stubSystemService->shouldReceive('is_dir')->andReturn(true);
		
		$this->fileService = new ZeitfadenFileService('/testuserid/');
		$this->fileService->setShardingService($stubShardingService);
		$this->fileService->setSystemService($stubSystemService);
		$unique = $this->fileService->storeFileByPath(dirname(__FILE__).self::SOURCE_FILE);
		
		$testdata = $this->fileService->getFileBinaryString($unique);
		
		$this->assertEquals($testdata, self::SOURCE_CONTENT);
		
		$this->fileService->delete($unique);
		
		try
		{
			$testdata = $this->fileService->getFileBinaryString($unique);
		}
		catch(ZeitfadenException $e)
		{
			return;
		}

		$this->fail('I expected and exfception here.');
		
		*/
	}
	
	
	
}
