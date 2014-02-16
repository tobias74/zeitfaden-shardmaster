<?php 
error_reporting(E_ALL);


require_once(dirname(__FILE__).'/../application-files.php');
$config = new ZeitfadenConfig($_SERVER['HTTP_HOST']);


set_include_path($config->phpUnitLocation.PATH_SEPARATOR.get_include_path());

set_include_path($config->mockeryLocation.PATH_SEPARATOR.get_include_path());
require_once 'Mockery/Loader.php';
$loader = new \Mockery\Loader;
$loader->register();


require_once('PHPUnit/Framework.php');
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once(dirname(__FILE__).'/DataMapTest.php');
require_once(dirname(__FILE__).'/ResponseTest.php');
require_once(dirname(__FILE__).'/ShardMapperTest.php');
require_once(dirname(__FILE__).'/ShardRepositoryTest.php');
require_once(dirname(__FILE__).'/UserRepositoryTest.php');
require_once(dirname(__FILE__).'/RepositoryTest.php');
require_once(dirname(__FILE__).'/GroupRepositoryTest.php');
require_once(dirname(__FILE__).'/ZeitStationRepositoryTest.php');
require_once(dirname(__FILE__).'/ProfilerTest.php');
require_once(dirname(__FILE__).'/FlyImageServicesTest.php');
require_once(dirname(__FILE__).'/MessageServiceTest.php');
require_once(dirname(__FILE__).'/StationControllerTest.php');
require_once(dirname(__FILE__).'/GroupControllerTest.php');
require_once(dirname(__FILE__).'/ApplicationTest.php');
require_once(dirname(__FILE__).'/ZeitfadenFacadeTest.php');
require_once(dirname(__FILE__).'/DependencyManagerTest.php');
require_once(dirname(__FILE__).'/NotificationCenterTest.php');
require_once(dirname(__FILE__).'/FileServiceTest.php');





$suite  = new PHPUnit_Framework_TestSuite();
/*
$suite->addTestSuite("ResponseTest");
//$suite->addTestSuite("GroupControllerTest");

$suite->addTestSuite("DataMapTest");
$suite->addTestSuite("ShardMapperTest");
$suite->addTestSuite("ShardRepositoryTest");
$suite->addTestSuite("GroupRepositoryTest");
$suite->addTestSuite("ZeitStationRepositoryTest");
$suite->addTestSuite("ProfilerTest");
$suite->addTestSuite("FileRepositoryTest");
$suite->addTestSuite("MessageServiceTest");
//$suite->addTestSuite("StationControllerTest");

$suite->addTestSuite("ApplicationTest");
*/
$suite->addTestSuite("FlyImageServicesTest");
$suite->addTestSuite("NotificationCenterTest");
$suite->addTestSuite("RepositoryTest");
$suite->addTestSuite("UserRepositoryTest");
$suite->addTestSuite("DependencyManagerTest");
$suite->addTestSuite("ZeitfadenFacadeTest");
$suite->addTestSuite("GroupControllerTest");
$suite->addTestSuite("FileServiceTest");

//$result = $suite->run();

echo "<pre>";
PHPUnit_TextUI_TestRunner::run($suite);
echo "</pre";
