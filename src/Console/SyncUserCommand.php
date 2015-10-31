<?php

namespace WArslett\TweetSync\Console;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WArslett\TweetSync\Model\TweetPersistenceService;
use WArslett\TweetSync\Remote\RemoteService;
use WArslett\TweetSync\Remote\TweetSync;

class SyncUserCommand extends Command
{

    /**
     * @var TweetSync
     */
    private $sync;

    public function __construct(TweetSync $sync)
    {
        parent::__construct();
        $this->sync = $sync;
    }

    protected function configure()
    {
        $this
            ->setName('tweetsync:user')
            ->setDescription('Greet someone')
            ->addArgument(
                'username',
                InputArgument::REQUIRED,
                'The twitter username to sync eg. BBCBreaking'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->sync->syncAllForUser($input->getArgument('username'));
    }
}