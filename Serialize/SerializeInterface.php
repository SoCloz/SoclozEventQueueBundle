<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Socloz\EventQueueBundle\Serialize;

/**
 * Description of SerializeInterface
 *
 * @author jfb
 */
interface SerializeInterface {

    public function serialize($event);
    
    public function deserialize($data);

}
