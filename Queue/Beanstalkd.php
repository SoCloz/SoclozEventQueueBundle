<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Socloz\EventQueueBundle\Queue;

/**
 * Description of Beanstalkd
 *
 * @author jfb
 */
class Beanstalkd implements QueueInterface
{

    protected $beanstalk;
    protected $tube;
    
    /**
     *
     * @param \Socket_Beanstalk $beanstalk
     * @param type $tube 
     */
    public function __construct($beanstalk, $tube)
    {
        $this->beanstalk = $beanstalk;
        $this->tube = $tube;
    }

    public function put($job)
    {
        $job = json_encode(array($job->getEvent(), $job->getData()));
        $this->beanstalk->useTube($this->tube);
        // FIXME : fix priority & ttl
        $this->beanstalk->put(20, 0, 3600, $job);
    }
    
    public function get()
    {
        $this->beanstalk->watch($this->tube);
        $job = $this->beanstalk->reserve();
        list($event, $data) = @json_decode($job['body'], true);
        return new Job($job['id'], $event, $data);
    }
    
    public function delete($job)
    {
        $this->beanstalk->delete($job->getId());
    }
}
