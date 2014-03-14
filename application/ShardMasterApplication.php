<?php 

use SugarLoaf as SL;

class ShardMasterApplication
{
	
	const STATUS_OK = true;
	const STATUS_ERROR_NOT_LOGGED_IN = -10; 
	const STATUS_GENERAL_ERROR = -100; 
	const STATUS_EMAIL_ALREADY_TAKEN = -15;
	const STATUS_ERROR_INVALID_ACTION = -1001;
	const STATUS_ERROR_WRONG_INPUT = -5;
	const STATUS_ERROR_SOLE_NOT_FOUND = -5001; 
	
	public function __construct($configData)
	{
		
    $this->httpHost = $configData['httpHost'];
    $this->dependencyConfigurator = $configData['dependencyConfigurator'];
    

    
    switch ($this->httpHost)
    {
      case "test.somethingzeitfaden.de":
        $this->applicationIni = $configData['applicationIni']['test'];
        break;
  
      case "shardmaster.zeitfaden.com":
        $this->applicationIni = $configData['applicationIni']['live'];
        break;
        
        
      default:
        throw new \ErrorException("no configuration for this domain: ".$this->httpHost);
        break;
        
    }

    $this->applicationId = $this->applicationIni['application_id'];
    
    $this->config = new MasterConfig();
    
    $this->config->dbConfig->setDbHost($this->applicationIni['db_host']);
    $this->config->dbConfig->setDbSocket($this->applicationIni['db_socket']);
    $this->config->dbConfig->setDbUser($this->applicationIni['db_user']);
    $this->config->dbConfig->setDbPassword($this->applicationIni['db_password']);
    $this->config->dbConfig->setDbName($this->applicationIni['db_name']);
    $this->config->dbConfig->setDbPort($this->applicationIni['db_port']);


    
    $this->dependencyManager = SL\DependencyManager::getInstance();
    $this->dependencyManager->setProfilerName('PhpProfiler');


    $this->dependencyConfigurator->configureDependencies($this->dependencyManager,$this);

    
    $this->mySqlProfiler = $this->dependencyManager->get('SqlProfiler');
    $this->phpProfiler = $this->dependencyManager->get('PhpProfiler');
	}
	
	
  public function getConfig()
  {
    return $this->config;
  }
  
	
	
    public function runRestful($serverContext)
    {
        $appTimer = $this->phpProfiler->startTimer('#####XXXXXXX A1A1-COMPLETE_RUN XXXXXXXXXXXX################');
        
        $serverContext->startSession();
        
        $request = $serverContext->getRequest();
        
        $response = new \PivoleUndPavoli\Response();
        


        // check for options-reuqest
        if ($request->getRequestMethod() === 'OPTIONS')
        {
          $appTimer->stop();
          
          $profilerJson = json_encode(array(
              'phpLog' => $this->phpProfiler->getHash(),
              'dbLog' => $this->mySqlProfiler->getHash()
          ));
          
          return $response;
        }        

        
        
        $this->getRouteManager()->analyzeRequest($request);
        
        
        $frontController = new \PivoleUndPavoli\FrontController($this);
        $frontController->setDependencyManager($this->dependencyManager);

        try
        {
          $frontController->dispatch($request,$response);
        }
        catch (ZeitfadenNoMatchException $e)
        {
          $response->appendValue('error', ZeitfadenApplication::STATUS_ERROR_SOLE_NOT_FOUND);
          $response->appendValue('errorMessage',$e->getMessage());
          $response->appendValue('stackTrace',$e->getTraceAsString());
          $response->addHeader('X-Tobias: some');
          $response->addHeader('HTTP/1.0 404 Not Found',true,404);
          
        }
              

        
        $appTimer->stop();
        
        $profilerJson = json_encode(array(
            'phpLog' => $this->phpProfiler->getHash(),
            'dbLog' => $this->mySqlProfiler->getHash()
        ));
        
        $response->addHeader("ZeitfadenProfiler: ".$profilerJson);
        
        return $response;
    }
		
	
	
	public function getRouteManager()
	{
		$routeManager = new ZeitfadenRouteManager();
		

		$routeManager->addRoute(new ZeitfadenRoute(
			'/:controller/:action/*',
			array()
		));
		
		return $routeManager;
	}
	
	
	
	
}




