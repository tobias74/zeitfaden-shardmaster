<?php

class GroupRepositoryTest extends PHPUnit_Framework_TestCase
{    
	public function testCreateGroup()    
	{
		$user = new ZeitfadenUser();
		$userRepository = UserRepository::getInstance();
		$user->setNick("NewUserYeah");
		$userRepository->merge($user);
		$userId = $user->getId();
		
		
		$group = new ZeitfadenGroup();
		$group->setUserId($userId);
		$group->setDescription("some new group ".microtime(true));
		
		$groupRepository = GroupRepository::getInstance();
		$groupRepository->merge($group);
		
		$this->assertTrue($group->getId() !== false, 'What happend to the group?');
		
		$test = $groupRepository->getById($group->getId(), $userId);
		
		$this->assertEquals($test->getDescription(), $group->getDescription(), 'Ahh these two should have ame name.');
				
		
		
	}
	
	
	public function testAddToGroups()
	{
		$user = new ZeitfadenUser();
		$userRepository = UserRepository::getInstance();
		$user->setNick("NewUserYeah");
		$userRepository->merge($user);

		$group = new ZeitfadenGroup();
		$group->setUserId($user->getId());
		$group->setDescription("some new group ".microtime(true));
		$groupRepository = GroupRepository::getInstance();
		$groupRepository->merge($group);
		
		
		$friend = new ZeitfadenUser();
		$friend->setNick("FriendYeah".mt_rand(0,100000));
		$userRepository->merge($friend);

		
		$group->addFriend($friend);
		$groupRepository->merge($group);
		
		$test = $groupRepository->getById($group->getId(), $user->getId());
		$this->assertEquals($test->getDescription(), $group->getDescription(), 'Ahh these two should have ame name.');
		$this->assertTrue(count($test->getAssignedFriendsIds()) == count($group->getAssignedFriendsIds()), 'Ahh these groups should have same count of friends.: '.count($test->getAssignedFriendsIds())." and ".count($group->getAssignedFriendsIds()));
		$friends = $test->getAssignedFriendsIds();
		$compareFriends = $group->getAssignedFriendsIds();
		$this->assertTrue(array_pop($friends) === array_pop($compareFriends), 'Ahh these groups should have same goups as first group.: ');
		
		
		
	}
		
}
