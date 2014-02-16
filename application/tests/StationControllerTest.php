<?php

class StationControllerTest extends PHPUnit_Framework_TestCase
{    
	public function testgetById()    
	{
		$user = new ZeitfadenUser();
		$userRepository = UserRepository::getInstance();
		$user->setNick("NewUserYeah".mt_rand(0,234234));
		$userRepository->merge($user);
		$userId = $user->getId();
		
		
		$station = new ZeitStation();
		$station->setUserId($userId);
		$description = "some value".mt_rand(10000,900000);
		$station->setDescription($description);
		
		
		$zeitStationRepository = ZeitStationRepository::getInstance();
		$zeitStationRepository->merge($station);
		
		
		$request = new ZeitfadenRequest();
		$request->setRequest(array(
			'userId' => $userId,
			'stationId' => $station->getId()
		));
		
		$response = new ZeitfadenResponse();
		
		$stationController = new StationController(false, $request, $response);
		$stationController->getByIdAction();
		
		$dto = $response->getValue('station');
		$this->assertTrue($response->getValue('status') === ZeitfadenApplication::STATUS_OK, 'Ahh these stations should have same count of groups.: ');
		$this->assertTrue($dto['description'] === $description, 'the description should be cool in the dto ');
		//$this->assertTrue(array_pop($test->getAssignedGroupsIds()) === array_pop($station->getAssignedGroupsIds()), 'Ahh these stations should have same goups as first group.: ');
		
		
		
	}

	
	public function testCreateAction()    
	{
		$user = new ZeitfadenUser();
		$userRepository = UserRepository::getInstance();
		$user->setNick("NewUserYeah".mt_rand(0,234234));
		$user->setEmail("somewhere".mt_rand(0,234234)."@zeitfaden.com");
		$user->setPassword('some');
		$userRepository->merge($user);
		$userId = $user->getId();
		
		
		$station = new ZeitStation();
		$station->setUserId($userId);
		$description = "some value".mt_rand(10000,900000);
		$station->setDescription($description);
		
		
		$zeitStationRepository = ZeitStationRepository::getInstance();
		$zeitStationRepository->merge($station);
		
		
		$request = new ZeitfadenRequest();
		$request->setSessionVar('email', $user->getEmail());
		$request->setSessionVar('password', $user->getPassword());
		
		$request->setRequest(array(
			'description' => 'Hallo',
			'startLatitude' => 5,
			'startLongitude' => 6,
			'endLatitude' => 7,
			'endLongitude' => 8,
			'startDate' => '2010-03-01',
			'endDate' => '2010-03-02',
			'publishStatus' => ZeitStation::PUBLISH_STATUS_PUBLIC
		));
		
		$response = new ZeitfadenResponse();
		
		$stationController = new StationController(false, $request, $response);
		$stationController->createAction();
		
		
		
		
		
		
		
		$this->assertTrue($response->getValue('status') === ZeitfadenApplication::STATUS_OK, 'why not opk?.: ');
		
		$dto = $response->getValue('station');
		
		$this->assertTrue($dto['description'] === 'Hallo', 'the des123cription should be cool in the dto '.$dto['description'].' with that Hallo');
		$this->assertTrue($dto['startLatitude'] === 5, 'the descrip234tion should be cool in the dto ');
		$this->assertTrue($dto['startLongitude'] === 6, 'the descrip345tion should be cool in the dto ');
		$this->assertTrue($dto['endLatitude'] === 7, 'the descriptio456n should be cool in the dto ');
		$this->assertTrue($dto['endLongitude'] === 8, 'the description shoul567d be cool in the dto ');
		
		
		
	}
	
	
}
