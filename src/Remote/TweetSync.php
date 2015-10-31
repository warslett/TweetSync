<?php

namespace WArslett\TweetSync\Remote;


use WArslett\TweetSync\Model\TweetFactory;
use WArslett\TweetSync\Model\TweetPersistenceService;
use WArslett\TweetSync\Model\TwitterUserFactory;

/**
 * Class TweetSync
 * @package WArslett\TweetSync\Remote
 */
class TweetSync
{

    /**
     * @var RemoteService
     */
    private $remote;

    /**
     * @var TweetFactory
     */
    private $factory;
    /**
     * @var TweetPersistenceService
     */
    private $persistenceService;
    /**
     * @var TwitterUserFactory
     */
    private $userFactory;

    public function __construct(
        RemoteService $remote,
        TweetPersistenceService $persistenceService,
        TweetFactory $factory,
        TwitterUserFactory $userFactory
    ) {
        $this->remote = $remote;
        $this->persistenceService = $persistenceService;
        $this->factory = $factory;
        $this->userFactory = $userFactory;
    }

    /**
     * @param $userName
     */
    public function syncAllForUser($userName)
    {
        $this->syncTwitterUser($userName);
        $this->syncTweetsForUser($userName);
    }

    /**
     * @param $userName
     */
    protected function syncTwitterUser($userName)
    {
        $remoteUserObj = $this->remote->findTwitterUser($userName);
        $localTwitterUser = $this->persistenceService
            ->getTwitterUserRepository()
            ->find($remoteUserObj->id_str);
        if ($localTwitterUser) {
            $this->userFactory->patchFromStdObj($localTwitterUser, $remoteUserObj);
            $this->persistenceService->ensureTwitterUserUpdated($localTwitterUser);
        } else {
            $localTwitterUser = $this->userFactory->buildFromStdObj($remoteUserObj);
            $this->persistenceService->persistTwitterUser($localTwitterUser);
        }
    }

    /**
     * @param $userName
     */
    protected function syncTweetsForUser($userName)
    {
        $objects = $this->remote->findByTwitterUser($userName);
        foreach ($objects as $object) {
            $tweet = $this->persistenceService->getTweetRepository()->find($object->id_str);
            if ($tweet) {
                $this->factory->patchFromStdObj($tweet, $object);
                $this->persistenceService->ensureTweetUpdated($tweet);
            } else {
                $tweet = $this->factory->buildFromStdObj($object);
                $this->persistenceService->persistTweet($tweet);
            }
        }
    }
}