<?php

class MessageServiceTest extends PHPUnit_Framework_TestCase
{    
	public function testSendMessage()    
	{
		$userA = new ZeitfadenUser();
		$userRepository = UserRepository::getInstance();
		$userA->setNick("NewUserYeah".mt_rand(0,3453453));
		$userRepository->merge($userA);
		
		$userB = new ZeitfadenUser();
		$userRepository = UserRepository::getInstance();
		$userB->setNick("NewUserYeah".mt_rand(0,3453453));
		$userRepository->merge($userB);

		$messageServiceA = new ZeitfadenMessageService($userA->getId());
		$messageServiceB = new ZeitfadenMessageService($userB->getId());
		
		$messageServiceA->openNewCorrespondence($userB->getId(), "Hallo", "Hier die Nachricht");
		
		
		//$this->assertFalse($userId === false, 'Why has the User Id not been set?');
		
		//$this->assertEquals($user->getNick(), $compareUser->getNick(), 'Userid is not the good??');
		
		$corresA = $messageServiceA->getInbox(0,999);
		$corresB = $messageServiceB->getInbox(0,999);
		$this->assertTrue( count($corresA) === 0, 'Was the message whattt??not received?');
		$this->assertTrue( count($corresB) === 1, 'Was the message not received?');

		$corresA = $messageServiceA->getOutbox(0,999);
		$corresB = $messageServiceB->getOutbox(0,999);
		$this->assertTrue( count($corresA) === 1, 'Was the message whattt??not received?');
		$this->assertTrue( count($corresB) === 0, 'Was the message not received?');


		
		$messageServiceA->openNewCorrespondence($userB->getId(), "Hallo", "Hier die Nachricht");

		$corresA = $messageServiceA->getInbox(0,999);
		$corresB = $messageServiceB->getInbox(0,999);
		$this->assertTrue( count($corresA) === 0, 'Was the message whattt??not received?');
		$this->assertTrue( count($corresB) === 2, 'Was the message not received?');

		$corres = array_shift($corresB);
		
		$messageServiceB->replyToCorrespondence($corres, "Re:Some", "hier beime Natnwort");
		$corresA = $messageServiceA->getInbox(0,999);
		$this->assertTrue( count($corresA) === 1, 'Was the message whattt??not received?');
		
		

		
	}

	
	
}
