<?php 
class ApplicationTest extends PHPUnit_Framework_TestCase
{
	protected function getApplication()
	{
		$config = new ZeitfadenConfig($_SERVER['HTTP_HOST']);
		$application = new ZeitfadenApplication($config);
		return $application;
	}
	
	
	
	public function testRegisterUser()
	{
		$email = 'testuser@zeitfaden.com';
		$password = 'testpass';
		
		$application = $this->getApplication();
		
		$request = new ZeitfadenRequest();
		$request->setRequest(array(
			'controller' => 'user',
			'action' => 'register',
			'email' => $email,
			'password' => $password
		));
		
		$response = $application->run($request);
		
		
		//$this->assertEquals($response->getValue('status'), ZeitfadenApplication::STATUS_OK, 'user registering did not go so well? ehy not ok?');
		
	}
	
	
	
	public function testSetNickname()    
	{
		$newNickname = 'neuer Name yeah';
		
		$application = $this->getApplication();
		
		$request = new ZeitfadenRequest();
		$request->setRequest(array(
			'controller' => 'user',
			'action' => 'setNickname',
			'nickname' => $newNickname
		));
		$request->setSessionVar('email','testuser@zeitfaden.com');
		$request->setSessionVar('password',md5('testpass'));
		
		$response = $application->run($request);
		
		$this->assertNotEquals($response->getValue('status'), ZeitfadenApplication::STATUS_ERROR_NOT_LOGGED_IN, 'the user should be logged in.');
		$this->assertEquals($response->getValue('status'), ZeitfadenApplication::STATUS_OK, 'ehy not ok?');
		$this->assertEquals($response->getValue('newNickname'), $newNickname, 'ehy not ok?');
		
		

		
	}
	
	
}
