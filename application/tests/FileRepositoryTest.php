<?php

class FileRepositoryTest extends PHPUnit_Framework_TestCase
{    
	
	public function testCreateStationAndAddFile()    
	{
		$registry = ZeitfadenRegistry::getInstance();	
		$config = $registry->getConfig();
		
		
		$user = new ZeitfadenUser();
		$userRepository = UserRepository::getInstance();
		$user->setNick("NewUserYeah");
		$userRepository->merge($user);
		$userId = $user->getId();
		
		
		$station = new ZeitStation();
		$station->setUserId($userId);
		$station->setDescription("some value".mt_rand(10000,900000));
		
		
		$zeitStationRepository = ZeitStationRepository::getInstance();
		$zeitStationRepository->merge($station);
		
		$this->assertFalse($userId === false, 'Why has the User Id not been set?');

		
		$fileService = new ZeitfadenFileService($userId);
		$fileObject = $fileService->storeFileByPath($config->testDataLocation.'/somefile0987234.php');
		$fileObject->setFileType('video/mpeg');
		$fileService->merge($fileObject);
		
		$station->setFileId($fileObject->getId());
		$zeitStationRepository->merge($station);
		
		
		$test = $zeitStationRepository->getById($station->getId(), $userId);
		$this->assertEquals($test->getFileId(), $station->getFileId(), 'Ahh these stations should have same fileIds.');
		
		$testFile = $fileService->getById($fileObject->getId());
		$this->assertEquals($testFile->getPathToFile(), $fileObject->getPathToFile(), 'Ahh these stations should have same filepath.');
		
		
		
		
	}
	
}
