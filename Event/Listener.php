<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Socloz\EventQueueBundle\Event;

use Socloz\EventQueueBundle\Queue\Job;
/**
 * Description of Listener
 *
 * @author jfb
 */
class Listener {

    protected $event;
    protected $queue;
    protected $serialize;
    protected $logger;
    
    /**
     * 
     * @param string $event 
     * @param Socloz\EventQueue\Queue\QueueInterface $queue 
     * @param Socloz\EventQueue\Serialize\SerializeInterface $serialize
     */
    public function __construct($event, $queue, $serialize, $logger) {
        $this->event = $event;
        $this->queue = $queue;
        $this->serialize = $serialize;
        $this->logger = $logger;
    }
    
    /**
     *
     * @param Symfony\Component\EventDispatcher\Event $event 
     */
    public function forwardEvent($event) {
        $data = $this->serialize->serialize($event);
        $this->queue->put(new Job(null, $this->event, $data));
        $event->stopPropagation();
    }

}
