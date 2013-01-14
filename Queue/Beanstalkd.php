<?php

/*
 * Copyright CloseToMe 2011/2012
 */

namespace Socloz\EventQueueBundle\Queue;

/**
 * Beanstalkd wrapper
 *
 * @author jfb
 */
class Beanstalkd implements QueueInterface
{

    protected $beanstalk;
    protected $tube;
    
    /**
     * @param \Socket_Beanstalk $beanstalk
     * @param type $tube 
     */
    public function __construct($beanstalk, $tube)
    {
        $this->beanstalk = $beanstalk;
        $this->tube = $tube;
    }

    public function put($event, $data)
    {
        $job = json_encode(array($event, $data));
        $this->beanstalk->useTube($this->tube);
        // FIXME : fix priority & ttl
        $this->beanstalk->put($job, 20, 0, 3600);
    }
    
    public function get()
    {
        $this->beanstalk->watch($this->tube);
        $job = $this->beanstalk->reserve();
        list($event, $data) = @json_decode($job->getData(), true);
        return new Job($job->getId(), $data, $event);
    }
    
    public function delete($job)
    {
        $this->beanstalk->delete($job);
    }
}
