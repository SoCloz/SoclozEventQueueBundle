<?php

namespace Socloz\EventQueueBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand as BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Worker command
 */
class WorkerCommand extends BaseCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setDefinition(array())
            ->addArgument('stop_after', InputArgument::OPTIONAL, 'Number of seconds after which to stop', 0)
            ->setName('socloz:event_queue:worker')
        ;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $stopAfter = $input->getArgument('stop_after');
        
        /* @var $worker Socloz\EventQueue\Worker\Worker */
        $worker = $this->getContainer()->get('socloz_event_queue.worker');
        $end = time() + $stopAfter;
        do {
            $worker->work();
        } while (!$stopAfter || time() < $end);
    }
}
