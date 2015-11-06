<?php

namespace WArslett\TweetSync\Console;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WArslett\TweetSync\Remote\TweetSync;

class InitCommand extends Command
{

    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setName('tweetsync:init')
            ->setDescription('Initialise the Database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $schemaTool = new SchemaTool($this->em);
        $schemaTool->createSchema($this->em->getMetadataFactory()->getAllMetadata());
    }
}