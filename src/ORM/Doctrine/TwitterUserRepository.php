<?php

namespace WArslett\TweetSync\ORM\Doctrine;

use Doctrine\ORM\EntityRepository;
use \WArslett\TweetSync\Model\TwitterUserRepository as TwitterUserRepositoryInterface;

class TwitterUserRepository extends EntityRepository implements TwitterUserRepositoryInterface
{

}