<?php

/*
 * Copyright CloseToMe 2011/2012
 */

namespace Socloz\EventQueueBundle\Event;

/**
 * The event listener
 *
 * @author jfb
 */
class Listener
{

    protected $event;
    protected $queue;
    protected $serialize;
    protected $logger;
    
    /**
     * @param string $event 
     * @param Socloz\EventQueue\Queue\QueueInterface $queue 
     * @param Socloz\EventQueue\Serialize\SerializeInterface $serialize
     */
    public function __construct($event, $queue, $serialize, $logger)
    {
        $this->event = $event;
        $this->queue = $queue;
        $this->serialize = $serialize;
        $this->logger = $logger;
    }
    
    /**
     * Forwards events to the queue
     * 
     * @param Symfony\Component\EventDispatcher\Event $event 
     */
    public function forwardEvent($event)
    {
        $data = $this->serialize->serialize($event);
        $this->queue->put($this->event, $data);
        $event->stopPropagation();
    }

}
