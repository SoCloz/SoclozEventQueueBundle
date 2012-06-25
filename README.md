SoclozEventQueueBundle
======================

A Symfony2 bundle that enable asynchonous execution of event listeners.

As long as some conditions are met, no code changes are required for an application to use the bundle.

The bundle is made of two parts : a listener and a worker.

The *listener* :
* is registered for the configured event, with maximum priority,
* stops propagation of those events,
* sends the serialized events to the queue.

The *worker* :
* builds the list of application listeners for the configured event,
* listens to the queue and deserializes events received,
* executes the listeners.

Dependencies
------------

* `beanstalk` - other queues can be added

Setup
-----

* enable the bundle,

* install Socket_Beanstalk (http://github.com/davidpersson/beanstalk) in your vendors directory and configure autoload

```
$loader->registerPrefixes(array(
    [...]
    'Socket_'          => __DIR__.'/../vendor/beanstalk/src',
));
```

* configure the service

```
socloz_event_queue:
    queue_type: beanstalkd
    forward: [ my.event.name, my.event.name ]
    serialize: mongoodm
    beanstalkd:
        host: 127.0.0.1
        tube: event_queue
```

Other possible configuration options (with default values) :

```
socloz_event_queue:
    beanstalkd:
        port: 11300
        persistent: true
        timeout: 1
```

* start the worker (a process management daemon, like supervisord is recommended) :

```
app/console socloz:event_queue:worker [stop_after]
```

If used, `stop_after` enables to worker to stop after a certain number of seconds (warning : the test is done *after* a job has been received).

Serializers
-----------

Events need to be serialized before being sent. Objects coming from the ORM/ODM usually cannot be serialized/deserialized using PHP builtin classes, therefore the bundle uses dedicated serializers.

For now, 2 basic serializers exist :

* Mandango,
* MongoDB ODM.

They assume :

* that a getId` method exists for ORM/ODM objects,
* that all objects having a getId method come from the ORM/ODM,
* that objects have been saved (the remote listener instantiates objects from the DB),
* that the events can be created using the constructor, and only the constructor,
* that constructor parameters have the same name as the attributes.

I did not have time/wasn't able to find a solution for those. Feel free to contribute.

Using ReflectionClass::newInstanceWithoutConstructor would help, but has only be added in 5.4. Class specific serializers, built during warm-up, would also improve performance if you need to forward a lot of events.

Example event class :

```
<?php

namespace Socloz\APIBundle\Event\Event;

use Symfony\Component\EventDispatcher\Event;

class CategoryDeleteEvent extends Event {

    protected $category;

    public function __construct($category) {
        $this->category = $category;
    }

    public function getCategory() {
        return $this->category;
    }
}
```

If you need a serializer, it needs to implement the `Socloz\EventQueueBundle\Serialize\SerializeInterface` interface.

```
interface SerializeInterface {

    public function serialize($event);

    public function deserialize($data);

}
```

It then needs to be registered as a service named `socloz_event_queue.serialize.your_serializer_name` and configured :

```
socloz_event_queue:
    serialize: your_serializer_name
```


Queue
-----

If you want to use another transport (RabbitMQ, Zer0MQ, Gearman, ...), you need to implement the `Socloz\EventQueueBundle\Queue\QueueInterface` interface.

```
interface QueueInterface {

    public function put($job);

    public function get();

    public function delete($job);

}
```

`get` is a blocking call. `delete` is only required if `get` does not remove jobs from the queue.

The transport class should be registered as a service named `socloz_event_queue.queue`.

Roadmap
-------

* Serializers I wouldn't be ashamed of

License
-------

This bundle is released under the MIT license (see LICENSE).