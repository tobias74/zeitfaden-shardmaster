<?php

class ZeitStationRepositoryTest extends PHPUnit_Framework_TestCase
{    
	public function testCreateStation()    
	{
		$user = new ZeitfadenUser();
		$userRepository = UserRepository::getInstance();
		$user->setNick("NewUserYeah");
		$userRepository->merge($user);
		$userId = $user->getId();
		
		
		$station = new ZeitStation();
		$station->setUserId($userId);
		
		
		$zeitStationRepository = ZeitStationRepository::getInstance();
		$zeitStationRepository->merge($station);
		
		
		$this->assertFalse($userId === false, 'Why has the User Id not been set?');
		
		//$this->assertEquals($user->getNick(), $compareUser->getNick(), 'Userid is not the good??');
		
		
		
	}

	
	public function testCreateStationAndAddGroups()    
	{
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
		
		$group = new ZeitfadenGroup();
		$group->setUserId($userId);
		$group->setDescription("some new group ".microtime(true));
		
		$groupRepository = GroupRepository::getInstance();
		$groupRepository->merge($group);
		
		$this->assertTrue($group->getId() !== false, 'What happend to the group?');
		
		$test = $groupRepository->getById($group->getId(), $userId);
		
		$this->assertEquals($test->getDescription(), $group->getDescription(), 'Ahh these two should have ame name.');
				
		
		$station->addToGroup($group);
		$zeitStationRepository->merge($station);

		$test = $zeitStationRepository->getById($station->getId(), $userId);
		
		$this->assertEquals($test->getDescription(), $station->getDescription(), 'Ahh these stations should have ame description.');
		

		$this->assertTrue(count($test->getAssignedGroupsIds()) == count($station->getAssignedGroupsIds()), 'Ahh these stations should have same count of groups.: '.count($station->getAssignedGroupsIds()).' and '.count($test->getAssignedGroupsIds()));
		
		$first = $test->getAssignedGroupsIds();
		$second = $station->getAssignedGroupsIds();
		
		$this->assertTrue(array_pop($first) === array_pop($second), 'Ahh these stations should have same goups as first group.: ');
		
		
		
	}
	
}
