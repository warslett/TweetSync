<?php

namespace test\WArslett\TweetSync\ORM\Doctrine;


use Mockery as m;
use PHPUnit_Framework_TestCase;
use WArslett\TweetSync\ORM\Doctrine\TweetPersistenceService;

class DoctrineTweetPersistenceServiceTest extends PHPUnit_Framework_TestCase
{

    public function testPersist_PersistsOneTweet()
    {
        $em = $this->entityManager();
        $tweet = m::mock('\WArslett\TweetSync\Model\Tweet');
        $service = new TweetPersistenceService($em);

        $service->persistTweet($tweet);

        $em->shouldHaveReceived('persist')->once()->with($tweet);
        $em->shouldHaveReceived('flush')->with($tweet)->once();
    }

    public function testPersistTwitterUser_PersistsOneTwitterUser()
    {
        $em = $this->entityManager();
        $twitterUser = m::mock('\WArslett\TweetSync\Model\TwitterUser');
        $service = new TweetPersistenceService($em);

        $service->persistTwitterUser($twitterUser);

        $em->shouldHaveReceived('persist')->once()->with($twitterUser);
        $em->shouldHaveReceived('flush')->with($twitterUser)->once();
    }

    public function testEnsureTwitterUserUpdated_CallsFlush()
    {
        $em = $this->entityManager();
        $twitterUser = m::mock('\WArslett\TweetSync\Model\TwitterUser');
        $service = new TweetPersistenceService($em);

        $service->ensureTwitterUserUpdated($twitterUser);

        $em->shouldHaveReceived('flush')->with($twitterUser)->once();
    }

    public function testEnsureTweetUpdated_CallsFlush()
    {
        $em = $this->entityManager();
        $tweet = m::mock('\WArslett\TweetSync\Model\Tweet');
        $service = new TweetPersistenceService($em);

        $service->ensureTweetUpdated($tweet);

        $em->shouldHaveReceived('flush')->with($tweet)->once();
    }

    public function entityManager()
    {
        $mock = m::mock('\Doctrine\ORM\EntityManager');
        $mock->shouldReceive('persist');
        $mock->shouldReceive('flush');
        return $mock;
    }

    public function arrayOfTweets($size)
    {
        $array = array();
        while (count($array) < $size) {
            $array[] = m::mock('\WArslett\TweetSync\Model\Tweet');
        }
        return $array;
    }
}
