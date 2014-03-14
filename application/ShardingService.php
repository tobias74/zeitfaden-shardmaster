<?php

class ShardingService
{
    public function __construct($applicationId)
    {
      $this->applicationId = $applicationId;
      $this->cachedShardIds = array();
      $this->cachedShards = array();
    }

    public function getDebugName()
    {
      return "ShardingService";
    }
    
    public function setUsersToShardsAssocMapper($mapper)
    {
        $this->usersToShardsAssocMapper = $mapper;
    }
    
    public function getUsersToShardsAssocMapper()
    {
        return $this->usersToShardsAssocMapper;
      //return $this->usersToShardsAssocMapperProvider->provide($this->getDatabaseShard());
    }

    public function setShardRepository($repository)
    {
      $this->shardRepository = $repository;
    }
    
    public function getShardRepository()
    {
      return $this->shardRepository;
    }
    
    public function setUsersToShardsReadStrategy($val)
    {
      $this->usersToShardsReadStrategy = $val;
    }

    public function setUsersToShardsWriteStrategy($val)
    {
      $this->usersToShardsWriteStrategy = $val;
    }
        
    public function getUsersToShardsReadStrategy()
    {
      return $this->usersToShardsReadStrategy;
    }

    public function getUsersToShardsWriteStrategy()
    {
      return $this->usersToShardsWriteStrategy;
    }
                    
    public function setProfiler($profiler)
    {
      $this->profiler = $profiler;
    }
    
    public function getProfiler()
    {
      return $this->profiler;
    }
    
    
    public function setDatabaseShard($shard)
    {
        $this->databaseShard = $shard;
    }
    
    public function getDatabaseShard()
    {
        return $this->databaseShard;
    }
    

  public function getShardByUrl($url)
  {
    
    $criteriaMaker = new \VisitableSpecification\CriteriaMaker();
    $criteria = $criteriaMaker->equals('applicationId',$this->applicationId)->logicalAnd($criteriaMaker->equals('url',$url));
    
    $spec = new \VisitableSpecification\Specification($criteria);
    
    return $this->getShardRepository()->getSoleMatch($spec);
    
  }

    
    
  public function getShardByUserId($userId)
  {
    if (isset($this->cachedShardIds[$userId]))
    {
      $shardId = $this->cachedShardIds[$userId];
    }
    else
    {
      try
      {
        $shardId = $this->getUsersToShardsReadStrategy()->getSoleAssociation('shard', array('user'=>$userId,'application'=>$this->applicationId));
        $this->cachedShardIds[$userId] = $shardId;
      }
      catch (NoMatchException $e)
      {
        throw new NoMatchException('Did not find Shard for UserId: ' . $userId); 
      }
    }
    return $this->getShardById($shardId);
  }

  public function getShardById($shardId)
  {
    if (isset($this->cachedShards[$shardId]))
    {
      $shard = $this->cachedShards[$shardId];
    }
    else
    {
      $shard = $this->getShardRepository()->getById($shardId);
      $this->cachedShards[$shardId] = $shard;
    }
    
    return $shard;
  }
  
  public function getAllShards()
  {
    $criteriaMaker = new \VisitableSpecification\CriteriaMaker();
    $criteria = $criteriaMaker->equals('applicationId',$this->applicationId);
    
    $spec = new \VisitableSpecification\Specification($criteria);
    
    return $this->getShardRepository()->getBySpecification($spec);
  }
  
  public function getAvailableShards()
  {
    return $this->getAllShards();
  }
  
    
    public function doesUserHaveShard($userId)
    {
      try
      {
        $shardId = $this->getShardByUserId($userId);
        return true;
      }
      catch (NoMatchException $e)
      {
        return false;    
      }
    }
    
    
  public function introduceUser($userId)
  {
    if ($this->doesUserHaveShard($userId))
    {
      throw new \ErrorException("This user is allready known and has shard:--".$userId."--");
    }
    
    $availableShards = $this->getAvailableShards();
    if (count($availableShards) === 0)
    {
      throw new \ErrorException("no shards defined.");
    }
    
    $rand = mt_rand(0, count($availableShards)-1);
    $assignedShard = $availableShards[$rand];
    
    $this->getUsersToShardsWriteStrategy()->makeAssociation(array('user' => $userId, 'shard' => $assignedShard->getId(), 'application' => $this->applicationId));
  }



  public function assignUserToShard($userId,$shardId)
  {
    $shard = $this->getShardById($shardId);
    $this->getUsersToShardsWriteStrategy()->makeAssociation(array('user' => $userId, 'shard' => $shard->getId(), 'application' => $this->applicationId));
  }

  
  
}





