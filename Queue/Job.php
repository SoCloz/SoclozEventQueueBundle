<?php

/*
 * Copyright CloseToMe 2011/2012
 */

namespace Socloz\EventQueueBundle\Queue;

/**
 * A job sent to the queue
 *
 * @author jfb
 */
class Job extends \Pheanstalk_Job
{
    protected $event;

    public function __construct($id, $data, $event)
    {
        parent::__construct($id, $data);
        $this->event = $event;
    }

    public function getEvent()
    {
        return $this->event;
    }
}
