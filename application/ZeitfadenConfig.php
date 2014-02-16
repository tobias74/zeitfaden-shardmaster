<?php 


class ZeitfadenConfig
{
  protected $dbConfig;
  
  public function __construct($domain)
  {
    $this->dbConfig = new \BrokenPottery\DbConfig();
    $this->postgreSqlConfig = new \BrokenPottery\DbConfig();

    
    switch ($domain)
    {
      case "shardmaster.butterfurz.de":

        $this->applicationId = 'shardmaster_live';
        
        $this->dbConfig->setDbHost("localhost");
        $this->dbConfig->setDbSocket("");
        $this->dbConfig->setDbUser("shardmaster_live");
        $this->dbConfig->setDbPassword("tobkean");
        $this->dbConfig->setDbName("shardmaster_live");
        $this->dbConfig->setDbPort('3306');
        
        break;

                
      default:
        throw new Exception("could not determine db-configuration");
    }
    
    
  }


  public function getApplicationId()
  {
    return $this->applicationId;
  } 
  
  public function getDbConfig()
  {
    $config = new \BrokenPottery\PotteryConfig();
    $config->setMySqlConfig($this->dbConfig);
    $config->setPostgreSqlConfig($this->postgreSqlConfig);
    
    return $config; 
  }
  
}