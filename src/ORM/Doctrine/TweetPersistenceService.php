<?php

namespace WArslett\TweetSync\ORM\Doctrine;


use Doctrine\ORM\EntityManager;
use WArslett\TweetSync\Model\Tweet;
use WArslett\TweetSync\Model\TweetPersistenceService as TweetPersistenceServiceInterface;
use WArslett\TweetSync\Model\TwitterUser;

/**
 * Class DoctrineTweetPersistenceService
 * @package WArslett\TweetSync\ORM\Doctrine
 */
class TweetPersistenceService implements TweetPersistenceServiceInterface
{

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param Tweet $tweet
     * @return mixed
     */
    public function persistTweet(Tweet $tweet)
    {
        $this->em->persist($tweet);
        $this->em->flush($tweet);
    }

    /**
     * @param $tweet
     */
    public function ensureTweetUpdated($tweet)
    {
        $this->em->flush($tweet);
    }

    /**
     * @param TwitterUser $twitterUser
     */
    public function persistTwitterUser(TwitterUser $twitterUser)
    {
        $this->em->persist($twitterUser);
        $this->em->flush($twitterUser);
    }

    /**
     * @param TwitterUser $twitterUser
     */
    public function ensureTwitterUserUpdated(TwitterUser $twitterUser)
    {
        $this->em->flush($twitterUser);
    }

    /**
     * @return TwitterUserRepository
     */
    public function getTwitterUserRepository()
    {
        return $this->em->getRepository('WArslett\TweetSync\Model\TwitterUser');
    }

    /**
     * @return TweetRepository
     */
    public function getTweetRepository()
    {
        return $this->em->getRepository('WArslett\TweetSync\Model\Tweet');
    }
}