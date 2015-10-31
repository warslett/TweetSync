<?php

namespace WArslett\TweetSync\Model;


/**
 * Class TwitterUserResolver
 *
 * Resolves TwitterUser objects. A "Resolver" differs from a "Factory" in that it does not necessarily create the
 * object. It also differs from a "Repository" in that it does not necessarily retrieve an existing object.
 * A "Resolver" encapsulates the logic for deciding whether to retrieve an existing object or to create a new one.
 *
 * @package WArslett\TweetSync
 */
class TwitterUserResolver
{

    /**
     * @var TwitterUserRepository
     */
    private $repository;

    /**
     * @var TwitterUserFactory
     */
    private $factory;

    /**
     * @param TwitterUserRepository $repository
     * @param TwitterUserFactory $factory
     */
    public function __construct(TwitterUserRepository $repository, TwitterUserFactory $factory)
    {
        $this->repository = $repository;
        $this->factory = $factory;
    }

    /**
     * @param $obj
     * @return TwitterUser
     */
    public function resolveFromStdObj($obj)
    {
        if (!($user = $this->repository->find($obj->id_str))) {
            $user = $this->factory->buildFromStdObj($obj);
        }
        return $user;
    }
}