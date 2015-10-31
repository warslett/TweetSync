<?php

namespace WArslett\TweetSync\Model;

class TweetFactory
{

    /**
     * @var TwitterUserRepository
     */
    private $userRepository;

    public function __construct(TwitterUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function buildFromStdObj(\stdClass $object)
    {
        $tweet = new Tweet($object->id_str);
        $this->patchFromStdObj($tweet, $object);
        return $tweet;
    }

    public function patchFromStdObj(Tweet $tweet, \stdClass $object)
    {
        $tweet->setCreatedAt(new \DateTimeImmutable($object->created_at));
        $tweet->setText($object->text);
        $tweet->setUser($this->userRepository->find($object->user->id_str));
    }
}