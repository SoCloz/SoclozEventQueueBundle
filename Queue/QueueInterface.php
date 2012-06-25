<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Socloz\EventQueueBundle\Queue;

/**
 * Description of Interface
 *
 * @author jfb
 */
interface QueueInterface {

    public function put($job);
    
    public function get();

    public function delete($job);

}
