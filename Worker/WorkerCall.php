<?php

/*
 * Copyright CloseToMe 2011/2012
 */

namespace Socloz\EventQueueBundle\Worker;

/**
 * Wrapper for application listener calls
 *
 * @author jfb
 */
class WorkerCall
{

    protected $listener;
    protected $method;
    
    public function __construct($listener, $method)
    {
        $this->listener = $listener;
        $this->method = $method;
    }
    
    public function call($event)
    {
        $method = $this->method;
        $this->listener->$method($event);
    }

}
