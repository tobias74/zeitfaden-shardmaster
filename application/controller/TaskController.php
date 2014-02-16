<?php 


// http://flyservice.butterfurz.de/image/getFlyImages/imageSize/small?imageUrl=http://idlelive.com/wp-content/uploads/2013/06/1dd45_celebrity_incredible-images-from-national-geographics-traveler-photo-contest.jpg


class TaskController extends AbstractZeitfadenController
{
            
  protected function declareDependencies()
  {
    return array_merge(array(
      'TaskService' => 'taskService',
    ), parent::declareDependencies());  
  }


  public function scheduleAction()
  {
    error_log('scheduleAction calkled.');
    $url = $this->_request->getParam('url','');
    $queueName = $this->_request->getParam('queueName','standard');
    $this->getTaskService()->scheduleSingleShot($queueName, $url);
  }


  public function resetAction()
  {
    $queueName = $this->_request->getParam('queueName','standard');
    $redis = new Predis\Client();
    $redis->set('Scheduler_Queue_'.$queueName, 0);
  }
  
  public function executeNextSingleShotAction()
  {
    
    $queueName = $this->_request->getParam('queueName','standard');
    $maxParallel = $this->_request->getParam('maxParallel',1);
   
    error_log('cronjob execute me: '.$queueName);
     
    $redis = new Predis\Client();
    
    $lastExecution = $redis->get('Scheduler_Queue_'.$queueName.'_Last_Execution');
    if (($lastExecution + 3600*4) < time())
    {
      $redis->set('Scheduler_Queue_'.$queueName, 0);
    }
    
    //if (exec('ps -A | grep avconv') == '')
    //{
    //  $redis->set('Zeitfaden_CronJob_Running', 'reset. did not find avconv process.');
    //}

    $currentParallel = $redis->get('Scheduler_Queue_'.$queueName);
    
    $force = $this->_request->getParam('force','false');
        
    if (( $currentParallel < $maxParallel) || ( $force === 'true'))
    {
      $counter = $redis->get('Scheduler_Queue_'.$queueName);
      $counter++;
      $redis->set('Scheduler_Queue_'.$queueName, $counter);

      $redis->set('Scheduler_Queue_'.$queueName.'_Last_Execution',time());

      
      if ($this->getTaskService()->hasNextSingleShot($queueName))
      {
        $url = $this->getTaskService()->nextSingleShot($queueName);
        error_log('now lynxing '.$url);
        exec("lynx -dump ".$url);
        error_log('done lynxing.');
        $this->getTaskService()->completeSingleShot($queueName,$url);
      }
      else 
      {
        error_log('nothing to do');  
      }


      $counter = $redis->get('Scheduler_Queue_'.$queueName);
      $counter--;
      $redis->set('Scheduler_Queue_'.$queueName, $counter);

    }
    else
    {
      error_log('scheduler busy.');
    }
    
  }
  
      
  
  public function helloAction()
  {
    die('hello');
  }  
  
  
}



        
        
        
        
        
        
        
        
        
        