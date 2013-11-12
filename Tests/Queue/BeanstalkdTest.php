<?php

namespace Socloz\EventQueueBundle\Tests\Queue;
use Socloz\EventQueueBundle\Queue\Beanstalkd;

class BeanstalkdTest extends \PHPUnit_Framework_TestCase
{
    private static $tube = 'socloz_event_queue_test';

    public function testPut()
    {
        $queue = $this->getQueue();
        $data = array(
            'event' => 'event-' . rand(),
            'data' => 'data-' . rand(),
        );
        $queue->put($data['event'], $data['data']);
        return $data;
    }

    /**
     *
     * @depends testPut
     */
    public function testGet(array $data)
    {
        $job = $this->getQueue()->get();
        $this->assertEquals($data['event'], $job->getEvent());
        $this->assertEquals($data['data'], $job->getData());
        return $job;
    }

    /**
     *
     * @depends testGet
     */
    public function testDelete($job)
    {
        $this->getQueue()->delete($job);
    }

    private function getQueue()
    {
        return new Beanstalkd(new \Pheanstalk_Pheanstalk('localhost', 11300, 1), self::$tube);
    }
}
