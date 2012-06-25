<?php

namespace Socloz\EventQueueBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

use Socloz\EventQueue\Worker\WorkerCall;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class SoclozEventQueueExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        
        if (isset($config['queue_type'])) {
            switch ($config['queue_type']) {
                case 'beanstalkd':
                    $loader->load("beanstalkd.xml");
                    break;
            }
            if (isset($config[$config['queue_type']])) {
                foreach ($config[$config['queue_type']] as $key => $value) {
                    $container->setParameter(sprintf('socloz_event_queue.%s.%s', $config['queue_type'], $key), $value);
                }
            }
        }

        $loader->load('services.xml');

        if (isset($config['serialize'])) {
            $serializer = new Reference(sprintf("socloz_event_queue.serialize.%s", $config['serialize']));
            $listener = $container->getDefinition("socloz_event_queue.listener");
            $listener->replaceArgument(2, $serializer);
            $worker = $container->getDefinition("socloz_event_queue.worker");
            $worker->replaceArgument(3, $serializer);
        }
        
        if (isset($config['forward'])) {
            $container->setParameter('socloz_event_queue.forward', $config['forward']);
            $forwardList = is_array($config['forward']) ? $config['forward'] : array($config['forward']);
            foreach ($forwardList as $event) {
                $this->createListener($container, $event);
            }
        }
                
    }
    
    public function createListener(ContainerBuilder $container, $event) {
        $id = sprintf('socloz_event_queue.listener.%s', $event);
        
        $container
            ->setDefinition($id, new DefinitionDecorator('socloz_event_queue.listener'))
            ->replaceArgument(0, $event)
            ->addTag('kernel.event_listener', array('event' => $event, 'method' => "forwardEvent", "priority" => 255));
        ;
    }
}
