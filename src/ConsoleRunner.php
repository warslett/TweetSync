<?php

namespace WArslett\TweetSync;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class ConsoleRunner
 * @package WArslett\TweetSync
 */
class ConsoleRunner
{

    /**
     * @var \Symfony\Component\Console\Application
     */
    private $application;

    /**
     * ConsoleApplication constructor.
     * @param ContainerBuilder $container
     */
    protected function __construct(ContainerBuilder $container)
    {
        $this->application = new Application();

        $container->setParameter('app.root', __DIR__);
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/Resources/config/'));
        $loader->load('services.yml');

        $this->application->add($container->get('twitter_sync_command'));
        $this->application->add($container->get('twitter_init_command'));
    }

    /**
     * @param $array
     * @return static
     */
    public static function configureFromArray($array)
    {
        $container = new ContainerBuilder();

        foreach($array as $parameter => $value) {
            $container->setParameter($parameter, $value);
        }

        return new static($container);
    }

    /**
     * @param InputInterface|null $input
     * @param OutputInterface|null $output
     * @return mixed
     */
    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        return $this->application->run($input, $output);
    }
}