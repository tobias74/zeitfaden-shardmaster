<?php 


// http://flyservice.butterfurz.de/image/getFlyImages/imageSize/small?imageUrl=http://idlelive.com/wp-content/uploads/2013/06/1dd45_celebrity_incredible-images-from-national-geographics-traveler-photo-contest.jpg


class ShardController extends AbstractZeitfadenController
{
            
  protected function declareDependencies()
  {
    return array_merge(array(
      'ShardingServiceProvider' => 'shardingServiceProvider',
    ), parent::declareDependencies());  
  }

  
  
  protected function getShardingService($val)
  {
    return $this->shardingServiceProvider->provide($val);
  }


  public function getShardByIdAction()
  {
    $shardId = $this->_request->getParam('shardId','');
    $applicationId = $this->_request->getParam('applicationId','');
    
    try
    {
      $shard = $this->getShardingService($applicationId)->getShardById($shardId);
      $this->_response->appendValue('shard', $this->getDtoForShard($shard));
      $this->_response->appendValue('status', 'ok');
    }
    catch (\BrokenPottery\NoMatchException $e)
    {
      $this->_response->appendValue('status', 'not_found');
    }
    
    
  }
  
  public function getShardForUserAction()
  {
    $userId = $this->_request->getParam('userId','');
    $applicationId = $this->_request->getParam('applicationId','');
    
    try
    {
      $shard = $this->getShardingService($applicationId)->getShardByUserId($userId);
      $this->_response->appendValue('shard', $this->getDtoForShard($shard));
      $this->_response->appendValue('status', 'ok');
    }
    catch (\BrokenPottery\NoMatchException $e)
    {
      $this->_response->appendValue('status', 'not_found');
    }
    
    
  }


  public function getShardByUrlAction()
  {
    $url = $this->_request->getParam('url','');
    $applicationId = $this->_request->getParam('applicationId','');
    
    $shard = $this->getShardingService($applicationId)->getShardByUrl($url);
    
    $this->_response->appendValue('shard', $this->getDtoForShard($shard));
    
    
  }


  public function getLeastUsedShardAction()
  {
    $applicationId = $this->_request->getParam('applicationId','');
    
    $allShards = $this->getShardingService($applicationId)->getAllShards();

    $rand = mt_rand(0, count($allShards)-1);
    $shard = $allShards[$rand];

    $this->_response->appendValue('shard', $this->getDtoForShard($shard));
    
    
  }


  public function getAllShardsAction()
  {
    $applicationId = $this->_request->getParam('applicationId','');
    $allShards = $this->getShardingService($applicationId)->getAllShards();
    
    $shards = array();    
    foreach ($allShards as $shard)
    {
      $shards[] = $this->getDtoForShard($shard);
    }
    
    $this->_response->appendValue('shards',$shards);
  }  
  
  public function introduceUserAction()
  {
    $userId = $this->_request->getParam('userId','');
    $applicationId = $this->_request->getParam('applicationId','');
    $this->getShardingService($applicationId)->introduceUser($userId);
    $shard = $this->getShardingService($applicationId)->getShardByUserId($userId);
    
    $this->_response->appendValue('shard', $this->getDtoForShard($shard));
  }

  public function assignUserToShardAction()
  {
    $userId = $this->_request->getParam('userId','');
    $applicationId = $this->_request->getParam('applicationId','');
    $shardId = $this->_request->getParam('shardId','');

    try
    {
      $this->getShardingService($applicationId)->assignUserToShard($userId,$shardId);
      $this->_response->appendValue('status', 'ok');
      $this->_response->appendValue('message', 'User added to shard.');
    }
    catch (ErrorException $e)
    {
      $this->_response->appendValue('status', 'error');
      $this->_response->appendValue('message', $e->getMessage());
    }


  }

  
  protected function getDtoForShard($shard)
  {
    return array(
      'shardId' => $shard->getId(),
      'applicationId' => $shard->getApplicationId(),
      
      'dbTablePrefix' => $shard->getDbTablePrefix(),
      
      'url' => $shard->getUrl(),

      'mySqlHost' => $shard->getDbHost(),
      'mySqlUser' => $shard->getDbUser(),
      'mySqlDbName' => $shard->getDbName(),
      'mySqlPassword' => $shard->getDbPassword(),
      'mySqlSocket' => $shard->getDbSocket(),
      'mySqlPort' => $shard->getDbPort(),

      'postgreSqlHost' => $shard->getPostgreSqlHost(),
      'postgreSqlUser' => $shard->getPostgreSqlUser(),
      'postgreSqlDbName' => $shard->getPostgreSqlDbName(),
      'postgreSqlPassword' => $shard->getPostgreSqlPassword(),
      'postgreSqlSocket' => $shard->getPostgreSqlSocket(),
      'postgreSqlPort' => $shard->getPostgreSqlPort(),
      
      'pathForFiles' => $shard->getPathForFiles()
    
    );
    
    
  }
  
  public function helloAction()
  {
    die('hello');
  }  
  
  
}



        
        
        
        
        
        
        
        
        
        