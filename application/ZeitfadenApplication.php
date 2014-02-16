<?php 




abstract class ServerContext
{
	abstract function getRequest();
	abstract function startSession();
	abstract function updateSession($hash);
	abstract function sendResponse($response);
	
}


class ApacheServerContext extends ServerContext
{
	public function getRequest()
	{
		$request = new ZeitfadenRequest();
		$request->setRequest($_REQUEST);
		$request->setSession($_SESSION);
		$request->setServer($_SERVER);
		$request->setFiles($_FILES);
		return $request;
	}
	
	public function startSession()
	{
		session_start();
	}
	
	public function updateSession($hash)
	{
		foreach ($hash as $name => $value)
		{
			$_SESSION[$name] = $value;
		}
	}
	
  function sendZipped($contents)
  {
    $startTime = microtime(true);
    
      $HTTP_ACCEPT_ENCODING = isset($_SERVER["HTTP_ACCEPT_ENCODING"]) ? $_SERVER["HTTP_ACCEPT_ENCODING"] : '';
      if( headers_sent() )
          $encoding = false;
      else if( strpos($HTTP_ACCEPT_ENCODING, 'x-gzip') !== false )
          $encoding = 'x-gzip';
      else if( strpos($HTTP_ACCEPT_ENCODING,'gzip') !== false )
          $encoding = 'gzip';
      else
          $encoding = false;
     
      if( $encoding )
      {
          header('Content-Encoding: '.$encoding);
          print("\x1f\x8b\x08\x00\x00\x00\x00\x00");
          $contents = gzcompress($contents, 9);
          header('X-Zeitfaden-Zipping-Time: '.(microtime(true) - $startTime));
          print($contents);
      }
      else
      {
          print($contents);        
      }
  } 
	
	public function sendResponse($response)
	{
	  if ($response->isFile())
	  {
	    foreach($response->getHeaders() as $header)
      {
        header($header['header'],$header['replace'],$header['code']);
      }
      
      readfile($response->getFileName());
	    
	  }
    else if ($response->isBytes())
    {
      foreach($response->getHeaders() as $header)
      {
        header($header['header'],$header['replace'],$header['code']);
      }
      
      echo($response->getBytes());
      
    }
    else if ($response->isStream())
    {
      foreach($response->getHeaders() as $header)
      {
        header($header['header'],$header['replace'],$header['code']);
      }
      
      http_send_stream($response->getStream());
      
    }
    else
    {
      header('Cache-Control: no-cache, must-revalidate');
      header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
      header('Content-type: application/json');
      header('Access-Control-Allow-Origin: *');
      header('Access-Control-Allow-Credentials: true');
      header('Access-Control-Expose-Headers: FooBar');
      header('Access-Control-Allow-Headers: X-Requested-With');            
      foreach($response->getHeaders() as $header)
      {
        header($header['header'],$header['replace'],$header['code']);
      }
      $this->sendZipped(json_encode($response->getHash()));
      
    }
	}
	
}

use SugarLoaf as SL;

class ZeitfadenApplication
{
	
	const STATUS_OK = true;
	const STATUS_ERROR_NOT_LOGGED_IN = -10; 
	const STATUS_GENERAL_ERROR = -100; 
	const STATUS_EMAIL_ALREADY_TAKEN = -15;
	const STATUS_ERROR_INVALID_ACTION = -1001;
	const STATUS_ERROR_WRONG_INPUT = -5;
	const STATUS_ERROR_SOLE_NOT_FOUND = -5001; 
	
	public function __construct($config)
	{
		
		$this->config = $config;
		
		$this->dependencyManager = SL\DependencyManager::getInstance();
		$this->dependencyManager->setProfilerName('PhpProfiler');
		$this->configureDependencies();
		
		$this->mySqlProfiler = $this->dependencyManager->get('SqlProfiler');
		$this->phpProfiler = $this->dependencyManager->get('PhpProfiler');

	}
	
	
	
	public function run($serverContext)
	{
		$serverContext->startSession();
		
		$request = $serverContext->getRequest();
		
		$response = new ZeitfadenResponse();
		
		$this->getRouteManager()->analyzeRequest($request);
		
		$frontController = new ZeitfadenFrontController();
		$frontController->setDependencyManager($this->dependencyManager);
		
		try
		{
			$frontController->dispatch($request,$response);
			$response->appendValue('status',ZeitfadenApplication::STATUS_OK);
			$response->appendValue('requestCompletedSuccessfully',true);
		}
		catch (ZeitfadenException $e)
		{
			$response->enable();
			$response->appendValue('status',$e->getCode());
			$response->appendValue('errorMessage',$e->getMessage());
			$response->appendValue('stackTrace',$e->getTraceAsString());
		}
		catch (ZeitfadenNoMatchException $e)
		{
			$response->appendValue('error', ZeitfadenApplication::STATUS_ERROR_SOLE_NOT_FOUND);
			$response->appendValue('errorMessage',$e->getMessage());
			$response->appendValue('stackTrace',$e->getTraceAsString());
		}
		
		$response->appendValue('profilerData',array(
			'phpProfiler'   => $this->phpProfiler->getHash(),
			'mysqlProfiler' => $this->mySqlProfiler->getHash()	
		));	
		
		
		$serverContext->updateSession($request->getSession());
		
		$service = $this->dependencyManager->get('ZeitfadenSessionFacade');
        $loggedInUser = $service->getLoggedInUser();
        
        if ($loggedInUser->getFacebookUserId() != false) 
        {
            $response->appendValue('isFacebookUser', true);
        } 
        else 
        {
            $response->appendValue('isFacebookUser', false);
        }        
        
        $response->appendValue('loginId', $loggedInUser->getId());
        $response->appendValue('loginEmail', $loggedInUser->getEmail());
        $response->appendValue('loginUserId', $loggedInUser->getId());
        $response->appendValue('loginUserEmail', $loggedInUser->getEmail());
        $response->appendValue('loginFacebookUserId', $loggedInUser->getFacebookUserId());
				
		
		
		return $response;
		
	}

	
	
	
	
	
    public function runRestful($serverContext)
    {
        //require_once('FirePHPCore/FirePHP.class.php');      
        $appTimer = $this->phpProfiler->startTimer('#####XXXXXXX A1A1-COMPLETE_RUN XXXXXXXXXXXX################');
        
        $serverContext->startSession();
        
        $request = $serverContext->getRequest();
        
        $response = new ZeitfadenResponse();
        


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
        
        $frontController = new ZeitfadenFrontController();
        $frontController->setDependencyManager($this->dependencyManager);
        
        try
        {
            $frontController->dispatch($request,$response);
        }
        catch (ZeitfadenException $e)
        {
            die($e->getMessage());
        }
        catch (ZeitfadenNoMatchException $e)
        {
            die($e->getMessage());
        }
        
        $appTimer->stop();
        
        $profilerJson = json_encode(array(
            'phpLog' => $this->phpProfiler->getHash(),
            'dbLog' => $this->mySqlProfiler->getHash()
        ));
        
        //header("ZeitfadenProfiler: ".$profilerJson);
        $response->addHeader("ZeitfadenProfiler: ".$profilerJson);
        
        $serverContext->updateSession($request->getSession());
        
        return $response;
        
    }
		
	
	
	public function getRouteManager()
	{
		$routeManager = new ZeitfadenRouteManager();
		

		$routeManager->addRoute(new ZeitfadenRoute(
			'/:controller/:action/*',
			array()
		));
		
		
		$routeManager->addRoute(new ZeitfadenRoute(
			'getUserById/:userId',
			array(
				'controller' => 'user',
				'action' => 'getById'
			)
		));

		$routeManager->addRoute(new ZeitfadenRoute(
			'getStationById/:stationId',
			array(
				'controller' => 'station',
				'action' => 'getById'
			)
		));

    $routeManager->addRoute(new ZeitfadenRoute(
        'getStationsByQuery/:query',
        array(
            'controller' => 'station',
            'action' => 'getByQuery'
        )
    ));

    $routeManager->addRoute(new ZeitfadenRoute(
        'getUsersByQuery/:query',
        array(
            'controller' => 'user',
            'action' => 'getByQuery'
        )
    ));
    		
    $routeManager->addRoute(new ZeitfadenRoute(
        'oauth/:action/*',
        array(
            'controller' => 'OAuth2'
        )
    ));
    								
		return $routeManager;
	}
	
	
	
	protected function configureDependencies()
	{
		$dm = SL\DependencyManager::getInstance();
				
		$depList = $dm->registerDependencyManagedService(new SL\ManagedSingleton('SqlProfiler','\Tiro\Profiler'));
		
		$depList = $dm->registerDependencyManagedService(new SL\ManagedSingleton('PhpProfiler','\Tiro\Profiler'));
    
    $depList = $dm->registerDependencyManagedService(new SL\ManagedSingleton('TaskService', 'TaskService'));
    //$depList->addDependency('Profiler', new SL\ManagedComponent('PhpProfiler'));
						
  
  
    $depList = $dm->registerDependencyManagedService(new SL\ManagedService('\BrokenPottery\DbService'));
    $depList->addDependency('Profiler', new SL\ManagedComponent('SqlProfiler'));
    
  
    
    $depList = $dm->registerDependencyManagedService(new SL\ManagedService('\BrokenPottery\MySqlConnector'));
    
                		
    $depList = $dm->registerDependencyManagedService(new SL\ManagedService('\BrokenPottery\MySqlEngine2013'));
    $depList->addDependency('Profiler', new SL\ManagedComponent('SqlProfiler'));
            		            		
            		
    $depList = $dm->registerDependencyManagedService(new SL\ManagedService('CriteriaMaker','\BrokenPottery\CriteriaMaker'));
            		   
   
    $depList = $dm->registerDependencyManagedService(new SL\ManagedService('Shard','\BrokenPottery\Shard'));
    $depList->addDependency('DbServiceProvider', new SL\ManagedComponentProvider('\BrokenPottery\DbService'));
    $depList->addDependency('MySqlConnectorProvider', new SL\ManagedComponentProvider('\BrokenPottery\MySqlConnector'));
    $depList->addDependency('Profiler', new SL\ManagedComponent('SqlProfiler'));
            
    
    $depList = $dm->registerDependencyManagedService(new SL\ManagedSingleton('ZeitfadenRootDbShard','\BrokenPottery\MasterDatabaseShard', new SL\ConstantParameterArray(array($this->config->getDbConfig()))));
    $depList->addDependency('DbServiceProvider', new SL\ManagedComponentProvider('\BrokenPottery\DbService'));
    $depList->addDependency('MySqlConnectorProvider', new SL\ManagedComponentProvider('\BrokenPottery\MySqlConnector'));
    $depList->addDependency('Profiler', new SL\ManagedComponent('SqlProfiler'));
      
   
   
   
   
   
   
            		
            		
            		
    $depList = $dm->registerDependencyManagedService(new SL\ManagedService('ShardController'));
    $depList->addDependency('ShardingServiceProvider', new SL\ManagedComponentProvider('ZeitfadenShardingService'));
    $depList->addDependency('Profiler', new SL\ManagedComponent('PhpProfiler'));
            		
		
    $depList = $dm->registerDependencyManagedService(new SL\ManagedService('ZeitfadenShardingService','\BrokenPottery\ShardingService'));
    $depList->addDependency('ShardRepository', new SL\ManagedComponent('ZeitfadenShardRepository'));
    $depList->addDependency('DatabaseShard', new SL\ManagedComponent('ZeitfadenRootDbShard'));
    $depList->addDependency('UsersToShardsReadStrategy', new SL\ManagedComponent('UsersToShardsAssociationReadStrategy'));
    $depList->addDependency('UsersToShardsWriteStrategy', new SL\ManagedComponent('UsersToShardsAssociationWriteStrategy'));
            

    $parameterArray = new SL\ParameterArray();
    $parameter = new SL\ManagedParameter('ZeitfadenRootDbShard');
    $parameterArray->appendParameter($parameter);
    
    
    
    
    $depList = $dm->registerDependencyManagedService(new SL\ManagedService('UsersToShardsAssociationWriteStrategy_MySql','\BrokenPottery\RegularWriteStrategy'));
    $depList->addDependency('Mapper', new SL\ManagedComponent('UsersToShardsAssociation_ForMySql'));
    //$depList->addDependency('DatabaseShard', new SL\ManagedComponent('ZeitfadenRootDbShard'));
    // we dont want to asssign a shard, because the mapper already knows his shard.
    
            
    $strategies = new SL\OneArrayAsParameter();
    $strategies->appendParameter(new SL\ManagedParameter('UsersToShardsAssociationWriteStrategy_MySql'));
    $depList = $dm->registerDependencyManagedService(new SL\ManagedParameterizedService('UsersToShardsAssociationWriteStrategy','\BrokenPottery\CompositeWriteStrategy', $strategies));
            
    $depList = $dm->registerDependencyManagedService(new SL\ManagedService('UsersToShardsAssociationReadStrategy','\BrokenPottery\RegularReadStrategy'));
    $depList->addDependency('Mapper', new SL\ManagedComponent('UsersToShardsAssociation_ForMySql'));
    $depList->addDependency('Profiler', new SL\ManagedComponent('PhpProfiler'));
    //$depList->addDependency('DatabaseShard', new SL\ManagedComponent('ZeitfadenRootDbShard'));
    // we dont want to asssign a shard, because the mapper already knows his shard.
    
    
    
    $depList = $dm->registerDependencyManagedService(new SL\ManagedSingleton('ZeitfadenShardRepository','\BrokenPottery\StrategizedRepository'));
    $depList->addDependency('CriteriaMaker', new SL\ManagedComponent('CriteriaMaker'));
    $depList->addDependency('ReadStrategy', new SL\ManagedComponent('ShardRepositoryReadStrategy'));
    $depList->addDependency('WriteStrategy', new SL\ManagedComponent('ShardRepositoryWriteStrategy'));
    
    $depList = $dm->registerDependencyManagedService(new SL\ManagedService('ShardRepositoryReadStrategy','\BrokenPottery\RegularReadStrategy'));
    $depList->addDependency('Mapper', new SL\ManagedComponent('ShardMapper_ForMySql'));
    $depList->addDependency('Profiler', new SL\ManagedComponent('PhpProfiler'));
    $depList->addDependency('DatabaseShard', new SL\ManagedComponent('ZeitfadenRootDbShard'));
    

    $strategies = new SL\OneArrayAsParameter();
    $strategies->appendParameter(new SL\ManagedParameter('ShardRepositoryWriteStrategy_MySql'));
    $depList = $dm->registerDependencyManagedService(new SL\ManagedParameterizedService('ShardRepositoryWriteStrategy','\BrokenPottery\CompositeWriteStrategy', $strategies));

    
    $depList = $dm->registerDependencyManagedService(new SL\ManagedService('ShardRepositoryWriteStrategy_MySql','\BrokenPottery\RegularWriteStrategy'));
    $depList->addDependency('Mapper', new SL\ManagedComponent('ShardMapper_ForMySql'));
            
            
            
    //Shards 2013
        
    $depList = $dm->registerDependencyManagedService(new SL\ManagedService('SchemaUpdater_Shard_MySql'));
    $depList->addDependency('ShardMapper', new SL\ManagedComponent('ShardMapper_ForMySql'));
    $depList->addDependency('UsersToShardsMapper', new SL\ManagedComponent('UsersToShardsAssociation_ForMySql'));

            
    $dataMap = new \BrokenPottery\DataMap();
    $dataMap->addColumn('id', 'id');
    $dataMap->addColumn('application_id', 'applicationId');
    $dataMap->addColumn('url', 'url');
    $dataMap->addColumn('db_table_prefix', 'dbTablePrefix');
    $dataMap->addColumn('db_host', 'dbHost');
    $dataMap->addColumn('db_user', 'dbUser');
    $dataMap->addColumn('db_password', 'dbPassword');
    $dataMap->addColumn('db_name', 'dbName');
    $dataMap->addColumn('db_socket', 'dbSocket');
    $dataMap->addColumn('db_port', 'dbPort');
    $dataMap->addColumn('postgresql_host', 'postgreSqlHost');
    $dataMap->addColumn('postgresql_user', 'postgreSqlUser');
    $dataMap->addColumn('postgresql_password', 'postgreSqlPassword');
    $dataMap->addColumn('postgresql_dbname', 'postgreSqlDbName');
    $dataMap->addColumn('postgresql_socket', 'postgreSqlSocket');
    $dataMap->addColumn('postgresql_port', 'postgreSqlPort');
    //$dataMap->addColumn('mongo_db_name', 'mongoDbName');
    //$dataMap->addColumn('mongo_db_server_url', 'mongoDbServerUrl');
    $dataMap->addColumn('fs_path_for_files', 'pathForFiles');
    //$dataMap->addColumn('fly_folder_dir', 'flyFolderDir');
    //$dataMap->addColumn('fly_folder_url', 'flyFolderUrl');
        
    $parameterArray = new SL\ParameterArray();
    $parameter = new SL\ManagedParameter('ZeitfadenRootDbShard');
    $parameterArray->appendParameter($parameter);
        
    $depList = $dm->registerDependencyManagedService(new SL\ManagedParameterizedService('ShardMapper_ForMySql', '\BrokenPottery\SqlEntityMapper',$parameterArray));
    $depList->addDependency('SqlEngineProvider',new SL\ManagedComponentProvider('\BrokenPottery\MySqlEngine2013'));
    $depList->addDependency('Profiler', new SL\ManagedComponent('PhpProfiler'));
    $depList->addDependency('EntityProvider', new SL\ManagedComponentProvider('Shard'));
    $depList->addDependency('dataMap', new SL\UnmanagedInstance($dataMap));
    $depList->addDependency('tableName', new SL\UnmanagedValue('zeitfaden_shards_2010'));
    $depList->addDependency('SchemaUpdaterProvider', new SL\ManagedComponentProvider('SchemaUpdater_Shard_MySql'));
        
    
    $depList = $dm->registerDependencyManagedService(new SL\ManagedService('\BrokenPottery\UsersToShardsMysqlEngine'));
    $depList->addDependency('Profiler', new SL\ManagedComponent('SqlProfiler'));

    
    
  // Users-to-Shards
  
        $dataMap = new \BrokenPottery\DataMap();
        $dataMap->addColumn('user_id', 'user');
        $dataMap->addColumn('shard_id', 'shard');
        $dataMap->addColumn('application_id', 'application');
    
        $parameterArray = new SL\ParameterArray();
        $parameter = new SL\ManagedParameter('ZeitfadenRootDbShard');
        $parameterArray->appendParameter($parameter);
    
        $depList = $dm->registerDependencyManagedService(new SL\ManagedParameterizedService('UsersToShardsAssociation_ForMySql', '\BrokenPottery\SqlAssociationMapper',$parameterArray));
        $depList->addDependency('SqlEngineProvider',new SL\ManagedComponentProvider('\BrokenPottery\MySqlEngine2013'));
        $depList->addDependency('Profiler', new SL\ManagedComponent('SqlProfiler'));
        $depList->addDependency('DataMap', new SL\UnmanagedInstance($dataMap));
        $depList->addDependency('TableName', new SL\UnmanagedValue('users_to_shards'));
        $depList->addDependency('SchemaUpdaterProvider', new SL\ManagedComponentProvider('SchemaUpdater_Shard_MySql'));
                
            
    
    
    
	}
	
}




