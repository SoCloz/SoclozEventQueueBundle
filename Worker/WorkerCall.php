<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Socloz\EventQueueBundle\Worker;

/**
 * Description of WorkerCall
 *
 * @author jfb
 */
class WorkerCall {

    protected $listener;
    protected $method;
    
    public function __construct($listener, $method) {
        $this->listener = $listener;
        $this->method = $method;
    }
    
    public function call($event) {
        $method = $this->method;
        $this->listener->$method($event);
    }

}
