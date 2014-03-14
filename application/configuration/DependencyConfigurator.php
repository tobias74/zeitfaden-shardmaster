<?php
use SugarLoaf as SL;

class DependencyConfigurator
{
  public function configureDependencies($dm,$application)
  {


    $depList = $dm->registerDependencyManagedService(new SL\ManagedSingleton('SqlProfiler','\Tiro\Profiler'));
    
    $depList = $dm->registerDependencyManagedService(new SL\ManagedSingleton('PhpProfiler','\Tiro\Profiler'));
    
    $depList = $dm->registerDependencyManagedService(new SL\ManagedSingleton('TaskService', 'TaskService'));
    //$depList->addDependency('Profiler', new SL\ManagedComponent('PhpProfiler'));
            
  
  
    $depList = $dm->registerDependencyManagedService(new SL\ManagedService('\PhpSimpleOrm\DbService'));
    $depList->addDependency('Profiler', new SL\ManagedComponent('SqlProfiler'));
    
  
    
    $depList = $dm->registerDependencyManagedService(new SL\ManagedService('\PhpSimpleOrm\MySqlConnector'));
    
                    
    $depList = $dm->registerDependencyManagedService(new SL\ManagedService('\PhpSimpleOrm\MySqlEngine2013'));
    $depList->addDependency('Profiler', new SL\ManagedComponent('SqlProfiler'));
                                
                
    $depList = $dm->registerDependencyManagedService(new SL\ManagedService('CriteriaMaker','\VisitableSpecification\CriteriaMaker'));
                   
   
    $depList = $dm->registerDependencyManagedService(new SL\ManagedService('Shard','\PhpSimpleOrm\Shard'));
    $depList->addDependency('DbServiceProvider', new SL\ManagedComponentProvider('\BrokenPottery\DbService'));
    $depList->addDependency('MySqlConnectorProvider', new SL\ManagedComponentProvider('\BrokenPottery\MySqlConnector'));
    $depList->addDependency('Profiler', new SL\ManagedComponent('SqlProfiler'));
            
    
    $depList = $dm->registerDependencyManagedService(new SL\ManagedSingleton('ZeitfadenRootDbShard','\PhpSimpleOrm\MasterDatabaseShard', new SL\ConstantParameterArray(array($application->getConfig()->getDbConfig()))));
    $depList->addDependency('MySqlConnectorProvider', new SL\ManagedComponentProvider('\PhpSimpleOrm\MySqlConnector'));
    $depList->addDependency('DbServiceProvider', new SL\ManagedComponentProvider('\PhpSimpleOrm\DbService'));
    $depList->addDependency('Profiler', new SL\ManagedComponent('SqlProfiler'));
    $depList->addDependency('Config', new SL\UnmanagedInstance($application->getConfig()));
   
   
   
   
   
   
                
                
                
    $depList = $dm->registerDependencyManagedService(new SL\ManagedService('ShardController'));
    $depList->addDependency('ShardingServiceProvider', new SL\ManagedComponentProvider('ShardingService'));
    $depList->addDependency('Profiler', new SL\ManagedComponent('PhpProfiler'));
                
    
    $depList = $dm->registerDependencyManagedService(new SL\ManagedService('ShardingService','ShardingService'));
    $depList->addDependency('ShardRepository', new SL\ManagedComponent('ZeitfadenShardRepository'));
    $depList->addDependency('DatabaseShard', new SL\ManagedComponent('ZeitfadenRootDbShard'));
    $depList->addDependency('UsersToShardsReadStrategy', new SL\ManagedComponent('UsersToShardsAssociationReadStrategy'));
    $depList->addDependency('UsersToShardsWriteStrategy', new SL\ManagedComponent('UsersToShardsAssociationWriteStrategy'));
            

    $parameterArray = new SL\ParameterArray();
    $parameter = new SL\ManagedParameter('ZeitfadenRootDbShard');
    $parameterArray->appendParameter($parameter);
    
    
    
    
    $depList = $dm->registerDependencyManagedService(new SL\ManagedService('UsersToShardsAssociationWriteStrategy_MySql','\PhpSimpleOrm\RegularWriteStrategy'));
    $depList->addDependency('Mapper', new SL\ManagedComponent('UsersToShardsAssociation_ForMySql'));
    //$depList->addDependency('DatabaseShard', new SL\ManagedComponent('ZeitfadenRootDbShard'));
    // we dont want to asssign a shard, because the mapper already knows his shard.
    
            
    $strategies = new SL\OneArrayAsParameter();
    $strategies->appendParameter(new SL\ManagedParameter('UsersToShardsAssociationWriteStrategy_MySql'));
    $depList = $dm->registerDependencyManagedService(new SL\ManagedParameterizedService('UsersToShardsAssociationWriteStrategy','\PhpSimpleOrm\CompositeWriteStrategy', $strategies));
            
    $depList = $dm->registerDependencyManagedService(new SL\ManagedService('UsersToShardsAssociationReadStrategy','\PhpSimpleOrm\RegularReadStrategy'));
    $depList->addDependency('Mapper', new SL\ManagedComponent('UsersToShardsAssociation_ForMySql'));
    $depList->addDependency('Profiler', new SL\ManagedComponent('PhpProfiler'));
    //$depList->addDependency('DatabaseShard', new SL\ManagedComponent('ZeitfadenRootDbShard'));
    // we dont want to asssign a shard, because the mapper already knows his shard.
    
    
    
    $depList = $dm->registerDependencyManagedService(new SL\ManagedSingleton('ZeitfadenShardRepository','\PhpSimpleOrm\StrategizedRepository'));
    $depList->addDependency('CriteriaMaker', new SL\ManagedComponent('CriteriaMaker'));
    $depList->addDependency('ReadStrategy', new SL\ManagedComponent('ShardRepositoryReadStrategy'));
    $depList->addDependency('WriteStrategy', new SL\ManagedComponent('ShardRepositoryWriteStrategy'));
    
    $depList = $dm->registerDependencyManagedService(new SL\ManagedService('ShardRepositoryReadStrategy','\PhpSimpleOrm\RegularReadStrategy'));
    $depList->addDependency('Mapper', new SL\ManagedComponent('ShardMapper_ForMySql'));
    $depList->addDependency('Profiler', new SL\ManagedComponent('PhpProfiler'));
    $depList->addDependency('DatabaseShard', new SL\ManagedComponent('ZeitfadenRootDbShard'));
    

    $strategies = new SL\OneArrayAsParameter();
    $strategies->appendParameter(new SL\ManagedParameter('ShardRepositoryWriteStrategy_MySql'));
    $depList = $dm->registerDependencyManagedService(new SL\ManagedParameterizedService('ShardRepositoryWriteStrategy','\PhpSimpleOrm\CompositeWriteStrategy', $strategies));

    
    $depList = $dm->registerDependencyManagedService(new SL\ManagedService('ShardRepositoryWriteStrategy_MySql','\PhpSimpleOrm\RegularWriteStrategy'));
    $depList->addDependency('Mapper', new SL\ManagedComponent('ShardMapper_ForMySql'));
            
            
            
    //Shards 2013
        
    $depList = $dm->registerDependencyManagedService(new SL\ManagedService('SchemaUpdater_Shard_MySql'));
    $depList->addDependency('ShardMapper', new SL\ManagedComponent('ShardMapper_ForMySql'));
    $depList->addDependency('UsersToShardsMapper', new SL\ManagedComponent('UsersToShardsAssociation_ForMySql'));

            
    $dataMap = new \PhpSimpleOrm\DataMap();
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
        
    $depList = $dm->registerDependencyManagedService(new SL\ManagedParameterizedService('ShardMapper_ForMySql', '\PhpSimpleOrm\SqlEntityMapper',$parameterArray));
    $depList->addDependency('SqlEngineProvider',new SL\ManagedComponentProvider('\PhpSimpleOrm\MySqlEngine2013'));
    $depList->addDependency('Profiler', new SL\ManagedComponent('PhpProfiler'));
    $depList->addDependency('EntityProvider', new SL\ManagedComponentProvider('Shard'));
    $depList->addDependency('dataMap', new SL\UnmanagedInstance($dataMap));
    $depList->addDependency('tableName', new SL\UnmanagedValue('zeitfaden_shards_2010'));
    $depList->addDependency('SchemaUpdaterProvider', new SL\ManagedComponentProvider('SchemaUpdater_Shard_MySql'));
        
    
    $depList = $dm->registerDependencyManagedService(new SL\ManagedService('\PhpSimpleOrm\UsersToShardsMysqlEngine'));
    $depList->addDependency('Profiler', new SL\ManagedComponent('SqlProfiler'));

    
    
  // Users-to-Shards
  
        $dataMap = new \PhpSimpleOrm\DataMap();
        $dataMap->addColumn('user_id', 'user');
        $dataMap->addColumn('shard_id', 'shard');
        $dataMap->addColumn('application_id', 'application');
    
        $parameterArray = new SL\ParameterArray();
        $parameter = new SL\ManagedParameter('ZeitfadenRootDbShard');
        $parameterArray->appendParameter($parameter);
    
        $depList = $dm->registerDependencyManagedService(new SL\ManagedParameterizedService('UsersToShardsAssociation_ForMySql', '\PhpSimpleOrm\SqlAssociationMapper',$parameterArray));
        $depList->addDependency('SqlEngineProvider',new SL\ManagedComponentProvider('\PhpSimpleOrm\MySqlEngine2013'));
        $depList->addDependency('Profiler', new SL\ManagedComponent('SqlProfiler'));
        $depList->addDependency('DataMap', new SL\UnmanagedInstance($dataMap));
        $depList->addDependency('TableName', new SL\UnmanagedValue('users_to_shards'));
        $depList->addDependency('SchemaUpdaterProvider', new SL\ManagedComponentProvider('SchemaUpdater_Shard_MySql'));
                
            
    



  }


}


