<?php

/*
 * Copyright CloseToMe 2011/2012
 */

namespace Socloz\EventQueueBundle\Queue;

/**
 * Description of Job
 *
 * @author jfb
 */
class Job
{

    protected $id;
    protected $event;
    protected $data;
    
    public function __construct($id, $event, $data)
    {
        $this->id = $id;
        $this->event = $event;
        $this->data = $data;
    }
    
    public function getId()
    {
        return $this->id;
        
    }
    
    public function getEvent()
    {
        return $this->event;
        
    }
    
    public function getData()
    {
        return $this->data;
        
    }

}
