<?php 
// the config-class needs a method "performConfiguration" or #loadCOnfiguration orr something, which can be called by the application.
// the application has to provide all, but how does the application now whether to provde the CompositeNode-Balancer or the Shard_Databse-Shardiong-Service?


class MasterConfig
{
  
  public function __construct()
  {
    $this->dbConfig = new \PhpSimpleOrm\DbConfig();
  }

  
  public function setShardingService($val)
  {
    $this->shardingService = $val;
  }

  public function getApplicationId()
  {
    return $this->applicationId;
  } 
  
  

  public function getDatabaseTablePrefix()
  {
    //throw new \ErrorException('where this needed?');
    return "";
  }

  public function getShardId()
  {
    //throw new \ErrorException('where this needed?');
    return "where is this needed?";
  }
  
  public function getDbConfig()
  {
    $config = new \PhpSimpleOrm\CompleteConfig();
    $config->setMySqlConfig($this->dbConfig);
    
    return $config; 
  }
  
}