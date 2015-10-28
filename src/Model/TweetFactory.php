<?php

namespace WArslett\TweetSync\Model;

use WArslett\TweetSync\Model\TwitterUserResolver;

class TweetFactory
{

    /**
     * @var TwitterUserResolver
     */
    private $userResolver;

    public function __construct(TwitterUserResolver $userResolver)
    {
        $this->userResolver = $userResolver;
    }

    public function buildFromStdObj($object)
    {
        $tweet = new Tweet();
        $tweet->setCreatedAt(new \DateTimeImmutable($object->created_at));
        $tweet->setId($object->id_str);
        $tweet->setText($object->text);
        $tweet->setUser($this->userResolver->resolveFromStdObj($object->user));
        return $tweet;
    }
}