<?php

class DataMapTest extends PHPUnit_Framework_TestCase
{    
	public function testAddColumn()    
	{
		$dataMap = new DataMap();
		$dataMap->addColumn('some_column','someColumn');
		$this->assertEquals($dataMap->getColumnForField('someColumn'), 'some_column', 'damn why nnot?');
		
	}
		
}
