<?php 
require_once('../application/application-files.php');
$applicationIni = parse_ini_file('../application/configuration/application.ini',true);

$serverContext = new \PivoleUndPavoli\ApacheServerContext();

$dependencyConfigurator = new DependencyConfigurator();

$application = new ShardMasterApplication(array(
  'httpHost' => $_SERVER['HTTP_HOST'],
  'dependencyConfigurator' => $dependencyConfigurator,
  'applicationIni' => $applicationIni
));

$response = $application->runRestful($serverContext);

if ($response->isEnabled())
{
    $serverContext->sendResponse($response);
}





