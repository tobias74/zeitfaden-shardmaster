<?php 
use \Mockery as M;

class IntegrationTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$config = new ZeitfadenConfig($_SERVER['HTTP_HOST']);
		$this->application = new ZeitfadenApplication($config);
	}
	
	protected function prepareServerContext($request)
	{
		$this->serverContext = M::mock('ServerContext');
		$this->serverContext->shouldReceive('getRequest')->andReturn($request);
		$this->serverContext->shouldReceive('startSession')->once();
		$this->serverContext->shouldReceive('updateSession')->once();
		
	}
	
	protected function prepareRequest($data)
	{
		$request = new ZeitfadenRequest();
		$request->setRequest($data['request']);
		
		if (isset($data['session']))
		{
			$request->setSession($data['session']);
		}
		
		$this->prepareServerContext($request);
	}

	
	protected function registerUser()
	{
		$firstUserEmail = 'testuser'.mt_rand(1111111,9999999).'@zeitfaden.com';
		$firstUserPassword = 'testpass';
		
		$this->prepareRequest(array(
			'request' => array(
				'controller' => 'user',
				'action' => 'register',
				'email' => $firstUserEmail,
				'password' => $firstUserPassword
			),
			'session' => array()
		));
		
		$response = $this->application->run($this->serverContext);
		$this->assertTrue( ($response->getValue('status') == ZeitfadenApplication::STATUS_OK) || ($response->getValue('status') == ZeitfadenApplication::STATUS_EMAIL_ALREADY_TAKEN), 'user registering did not go so well? ehy not ok?');


		
		$this->prepareRequest(array(
			'request' => array(
				'controller' => 'user',
				'action' => 'login',
				'email' => $firstUserEmail,
				'password' => $firstUserPassword
			),
			'session' => array()
		));
		
		$response = $this->application->run($this->serverContext);
		$this->assertTrue( $response->getValue('loginUserEmail') == $firstUserEmail , 'user could not log in?');
		
		$firstUserId = $response->getValue('loginUserId');
		
		return array(
			'email' => $firstUserEmail,
			'password' => $firstUserPassword,
			'userId' => $firstUserId
		);
	}
	
	protected function createGroupForUser($userData)
	{
		$groupName = "someskdjhf".mt_rand(1111,999999);
		
		$this->prepareRequest(array(
			'request' => array(
				'controller' => 'group',
				'action' => 'create',
				'description' => $groupName
			),
			'session' => array(
				'email' => $userData['email'],
				'password' => md5($userData['password'])
			)
		));
		
		$response = $this->application->run($this->serverContext);
		
		
		$groupData = $response->getValue('group');
		
		$this->assertTrue( $response->getValue('loginUserEmail') == $userData['email'] , 'what 98634996538745');
		$this->assertTrue( $groupData['description'] == $groupName , 'what wrong grou Desriptn');
		$this->assertTrue( $groupData['groupId'] != false , 'what 98634996538745');
		
		return $groupData;
		
	}
	
	protected function getInbox($secondUser)
	{
		$this->prepareRequest(array(
			'request' => array(
				'controller' => 'correspondence',
				'action' => 'get',
				'box' => 'in',
				'offset' => 0
			),
			'session' => array(
				'email' => $secondUser['email'],
				'password' => md5($secondUser['password'])
			)
		));
		$response = $this->application->run($this->serverContext);
		
		if($response->getValue('status') != ZeitfadenApplication::STATUS_OK)
		{
			$this->fail($response->getValue('errorMessage'));
		}
		
		$correspondences = $response->getValue('correspondences');
		
		return $correspondences;
	}

	protected function createStationForUser($userData)
	{
		$stationName = "station_someskdjhf".mt_rand(1111,999999);
		
		$this->prepareRequest(array(
			'request' => array(
				'controller' => 'station',
				'action' => 'create',
				'description' => $stationName
			),
			'session' => array(
				'email' => $userData['email'],
				'password' => md5($userData['password'])
			)
		));
		
		$response = $this->application->run($this->serverContext);
		
		
		$stationData = $response->getValue('station');
		
		$this->assertTrue( $response->getValue('loginUserEmail') == $userData['email'] , 'what 98634996538745');
		$this->assertTrue( $stationData['description'] == $stationName , 'what wrong grou Desriptn');
		$this->assertTrue( $stationData['stationId'] != false , 'what 98634996538745');
		
		return $stationData;
		
	}
	
	public function testCompleteIntegration()
	{
		$firstUser  = $this->registerUser();
		$secondUser = $this->registerUser();
		
		$groupData = $this->createGroupForUser($firstUser);
		
		
		$this->prepareRequest(array(
			'request' => array(
				'controller' => 'user',
				'action' => 'assignToGroups',
				'userId' => $secondUser['userId'],
				'groupIds' => array($groupData['groupId'])
			),
			'session' => array(
				'email' => $firstUser['email'],
				'password' => md5($firstUser['password'])
			)
		));
		$response = $this->application->run($this->serverContext);
		
		
		$stationData = $this->createStationForUser($firstUser);

		
		
		$this->prepareRequest(array(
			'request' => array(
				'controller' => 'station',
				'action' => 'setGroups',
				'stationId' => $stationData['stationId'],
				'groupIds' => array($groupData['groupId'])
			),
			'session' => array(
				'email' => $firstUser['email'],
				'password' => md5($firstUser['password'])
			)
		));
		$response = $this->application->run($this->serverContext);
		

		
		$this->prepareRequest(array(
			'request' => array(
				'controller' => 'station',
				'action' => 'getById',
				'stationId' => $stationData['stationId'],
				'userId' => $firstUser['userId']
			),
			'session' => array(
				'email' => $secondUser['email'],
				'password' => md5($secondUser['password'])
			)
		));
		$response = $this->application->run($this->serverContext);
		$receivedStationData = $response->getValue('station');
		$this->assertTrue( $stationData['description'] == $receivedStationData['description'] , 'what wrong grou Desriptn23423423543455');
		
		
		$this->prepareRequest(array(
			'request' => array(
				'controller' => 'station',
				'action' => 'setGroups',
				'stationId' => $stationData['stationId'],
				'groupIds' => array()
			),
			'session' => array(
				'email' => $firstUser['email'],
				'password' => md5($firstUser['password'])
			)
		));
		$response = $this->application->run($this->serverContext);
		
		

		$this->prepareRequest(array(
			'request' => array(
				'controller' => 'station',
				'action' => 'getById',
				'stationId' => $stationData['stationId'],
				'userId' => $firstUser['userId']
			),
			'session' => array(
				'email' => $secondUser['email'],
				'password' => md5($secondUser['password'])
			)
		));
		$response = $this->application->run($this->serverContext);
		
		if (!($response->hasValue('error') && ($response->getValue('error') == ZeitfadenApplication::STATUS_ERROR_SOLE_NOT_FOUND)))
		{
			$this->fail('I expected no finding!!');
		}
		
		
		$this->assertTrue( $stationData['description'] == $receivedStationData['description'] , 'what wrong grou Desriptn23423423543455');
		
		
	}
	
	
	public function testRegisterUser()
	{
		$email = 'testuser'.mt_rand(1111111,9999999).'@zeitfaden.com';
		$password = 'testpass';
		
		$request = new ZeitfadenRequest();
		$request->setRequest(array(
			'controller' => 'user',
			'action' => 'register',
			'email' => $email,
			'password' => $password
		));
		
		$this->prepareServerContext($request);
		
		$response = $this->application->run($this->serverContext);
		
		
		$this->assertTrue( ($response->getValue('status') == ZeitfadenApplication::STATUS_OK) || ($response->getValue('status') == ZeitfadenApplication::STATUS_EMAIL_ALREADY_TAKEN), 'user registering did not go so well? ehy not ok?');
		
	}
	
	
	
	public function testSetNickname()    
	{
		$newNickname = 'neuer Name yeah';
		
		$request = new ZeitfadenRequest();
		$request->setRequest(array(
			'controller' => 'user',
			'action' => 'setNickname',
			'nickname' => $newNickname
		));
		$request->setSessionVar('email','testuser@zeitfaden.com');
		$request->setSessionVar('password',md5('testpass'));
		
		$this->prepareServerContext($request);
		$response = $this->application->run($this->serverContext);
		
		$this->assertNotEquals($response->getValue('status'), ZeitfadenApplication::STATUS_ERROR_NOT_LOGGED_IN, 'the user should be logged in.');
		$this->assertEquals($response->getValue('status'), ZeitfadenApplication::STATUS_OK, 'ehy not ok?');
		$this->assertEquals($response->getValue('newNickname'), $newNickname, 'ehy not ok?');
		
		

		
	}
	
	
	public function testMessageSystemIntegration()
	{
		$firstUser  = $this->registerUser();
		$secondUser = $this->registerUser();
		
		$messageSubject = "Hello in Subject";
		$messageBody = "Hello in Body eyeah...";
		
		$this->prepareRequest(array(
			'request' => array(
				'controller' => 'correspondence',
				'action' => 'start',
				'recipientId' => $secondUser['userId'],
				'messageSubject' => $messageSubject,
				'messageBody' => $messageBody
			),
			'session' => array(
				'email' => $firstUser['email'],
				'password' => md5($firstUser['password'])
			)
		));
		$response = $this->application->run($this->serverContext);
		

		$correspondences = $this->getInbox($secondUser);
		
		$this->assertTrue( count($correspondences) === 1, 'how many corrsespodences? 23423543455');
		
		$corres = array_pop($correspondences);
		$messages = $corres['messages'];
		$this->assertTrue( count($messages) === 1, 'how many messages? 2324633456425637548536455');
		
		$message = array_pop($messages);
		$this->assertTrue($message['subject'] === $messageSubject , 'wrong subject? messasdfsdfsdfages? 2324633456425637548536455');
		$this->assertTrue($message['body'] === $messageBody , 'wrong messagebody? adfsdfsdfhow maasdfsdfny messages? 2324633456425637548536455');
		
		
		
		$this->prepareRequest(array(
			'request' => array(
				'controller' => 'correspondence',
				'action' => 'delete',
				'correspondenceId' => $corres['correspondenceId']
			),
			'session' => array(
				'email' => $secondUser['email'],
				'password' => md5($secondUser['password'])
			)
		));
		$response = $this->application->run($this->serverContext);
		
		

		$correspondences = $this->getInbox($secondUser);
		$this->assertTrue( count($correspondences) === 0, 'how many corrsespodences? 23423543455');
		
		
	}
	
	
	public function testFlyImagesIntegration()
	{
		$firstUser  = $this->registerUser();
		$stationDTO = $this->createStationForUser($firstUser);
		
		
	}
	

	public function testGroupsIntegration()
	{
		$firstUser  = $this->registerUser();
		$secondUser = $this->registerUser();
		$groupDTO = $this->createGroupForUser($firstUser);
		
		$this->prepareRequest(array(
			'request' => array(
				'controller' => 'user',
				'action' => 'assignToGroups',
				'userId' => $secondUser['userId'],
				'groupIds' => array($groupDTO['groupId'])
			),
			'session' => array(
				'email' => $firstUser['email'],
				'password' => md5($firstUser['password'])
			)
		));
		$response = $this->application->run($this->serverContext);
		
		
		$this->prepareRequest(array(
			'request' => array(
				'controller' => 'group',
				'action' => 'delete',
				'groupId' => $groupDTO['groupId']
			),
			'session' => array(
				'email' => $firstUser['email'],
				'password' => md5($firstUser['password'])
			)
		));
		$response = $this->application->run($this->serverContext);
		
		
		$this->prepareRequest(array(
			'request' => array(
				'controller' => 'group',
				'action' => 'getById',
				'groupId' => $groupDTO['groupId']
			),
			'session' => array(
				'email' => $firstUser['email'],
				'password' => md5($firstUser['password'])
			)
		));
		$response = $this->application->run($this->serverContext);
		
		
		if (!($response->hasValue('error') && ($response->getValue('error') == ZeitfadenApplication::STATUS_ERROR_SOLE_NOT_FOUND)))
		{
			$this->fail('I expected no finding!!');
		}
		
		
		
		
	}
	
	
}
