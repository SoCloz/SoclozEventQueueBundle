<?php

namespace Socloz\EventQueueBundle\Worker;

class Worker {
    
    protected $calls;
    protected $queue;
    protected $serialize;
    protected $logger;
    
    /**
     *
     * @param Symfony\Component\EventDispatcher\EventDispatcher $dispatcher
     * @param Socloz\EventQueue\Queue\QueueInterface $queue
     * @param Socloz\EventQueue\Serialize\SerializeInterface $serialize
     * @param array $calls 
     */
    public function __construct($events, $dispatcher, $queue, $serialize, $logger) {
        $this->calls = array();
        foreach ($events as $event) {
            $listeners = $dispatcher->getListeners($event);
            foreach ($listeners as $listenerArr) {
                list($listener, $method) = $listenerArr;
                if (!$listener instanceof \Socloz\EventQueueBundle\Event\Listener) {
                    $this->calls[$event][] = new WorkerCall($listener, $method);
                }
            }
        }
        $this->queue = $queue;
        $this->serialize = $serialize;
        $this->logger = $logger;
    }
    
    public function work() {
        $job = $this->queue->get();
        $this->logger->debug(sprintf('SoclozEventQueue:Worker - Received event %s : %s', $job->getEvent(), json_encode($job->getData())));
        $calls = isset($this->calls[$job->getEvent()]) ? $this->calls[$job->getEvent()] : null;
        if ($calls) {
            $eventObj = $this->serialize->deserialize($job->getData());
            if ($eventObj) {
                foreach ($calls as $call) {
                    $call->call($eventObj);
                }
            }
        }
        $this->queue->delete($job);

    }
    
}