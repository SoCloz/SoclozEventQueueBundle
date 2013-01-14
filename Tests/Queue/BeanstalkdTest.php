<?php

namespace Socloz\EventQueueBundle\Tests\Queue;
use Socloz\EventQueueBundle\Queue\Beanstalkd;

class BeanstalkdTest extends \PHPUnit_Framework_TestCase
{
    private static $tube = 'socloz_event_queue_test';
    private static $queue;

    public function testPut()
    {
        self::$queue = $queue = new Beanstalkd(new \Pheanstalk('localhost', 11300, 1), self::$tube);
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
        $job = self::$queue->get();
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
        self::$queue->delete($job);
    }
}
