<?php 
error_reporting(E_ALL);

function exception_error_handler($errno, $errstr, $errfile, $errline ) 
{
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
set_error_handler("exception_error_handler");

date_default_timezone_set('Europe/Berlin');



$baseDir = dirname(__FILE__);



require_once($baseDir.'/../frameworks/predis/autoload.php');
Predis\Autoloader::register();

require_once($baseDir.'/../my-frameworks/sugarloaf/sugarloaf.php');
require_once($baseDir.'/../my-frameworks/php-visitable-specification/src/php-visitable-specification.php');
require_once($baseDir.'/../my-frameworks/php-simple-orm/src/php-simple-orm.php');
require_once($baseDir.'/../my-frameworks/pivole-und-pavoli/src/pivole-und-pavoli.php');


require_once($baseDir.'/../my-frameworks/tiro-php-profiler/tiro.php');

//require_once($baseDir.'/../my-frameworks/brokenpottery/brokenpottery.php');

//require_once($baseDir.'/query-engine/ZeitfadenQueryEngine.php');
//require_once($baseDir.'/query-engine/context/Assembly.php');
//require_once($baseDir.'/query-engine/context/Handler.php');
//require_once($baseDir.'/query-engine/context/Interpreter.php');


require_once($baseDir.'/ZeitfadenExceptions.php');
//require_once($baseDir.'/TimeService.php');
require_once($baseDir.'/ZeitfadenRouter.php');
require_once($baseDir.'/ZeitfadenConfig.php');
require_once($baseDir.'/ShardingService.php');
require_once($baseDir.'/ShardMasterApplication.php');
require_once($baseDir.'/configuration/MasterConfig.php');
require_once($baseDir.'/configuration/DependencyConfigurator.php');




require_once($baseDir.'/AbstractZeitfadenController.php');
require_once($baseDir.'/ZeitfadenFrontController.php');


require_once($baseDir.'/controller/ShardController.php');



require_once($baseDir.'/model/TaskService.php');




//require_once($baseDir.'/ZeitfadenUUID.php');


