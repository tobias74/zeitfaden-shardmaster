<?php 
class ResponseTest extends PHPUnit_Framework_TestCase
{
	public function testRegisterUser()
	{
		$response = new ZeitfadenResponse();
		$response->appendValue('some',5);
		
		$responseHash = $response->getResponse();
		$this->assertEquals($response->getValue('some'), 5, 'warum ungleich');
		$this->assertEquals($responseHash['some'], 5, 'warum ungleich234234234');
		
	}
	
	
}
