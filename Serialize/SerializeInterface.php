<?php

/*
 * Copyright CloseToMe 2011/2012
 */

namespace Socloz\EventQueueBundle\Serialize;

/**
 * Interface for serializers
 *
 * @author jfb
 */
interface SerializeInterface
{

    public function serialize($event);
    
    public function deserialize($data);

}
