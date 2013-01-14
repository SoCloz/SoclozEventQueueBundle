<?php

/*
 * Copyright CloseToMe 2011/2012
 */

namespace Socloz\EventQueueBundle\Queue;

/**
 * Interface for queue wrappers
 *
 * @author jfb
 */
interface QueueInterface
{

    public function put($event, $data);
    
    public function get();

    public function delete($job);

}
