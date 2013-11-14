<?php

namespace Socloz\EventQueueBundle\Tests\Queue;
use Socloz\EventQueueBundle\Queue\Beanstalkd;
use Socloz\EventQueueBundle\Queue\Job;

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
        static $queueStub;
        if (!$queueStub) {
            $queueStub = new BeanstalkStub();
        }
        return new Beanstalkd($queueStub, self::$tube);
    }
}


class BeanstalkStub
{
    static $job;

    public function useTube($domain)
    {
    }

    public function watch($domain)
    {
    }

    public function put($job, $priority, $delay, $timeout)
    {
        self::$job = new Job(uniqid(), $job, null);
    }

    public function reserve()
    {
        return self::$job;
    }

    public function delete($job)
    {
        if ($job->getId() != self::$job->getId()) {
            throw new \Exception("cannot delete");
        }
    }
}