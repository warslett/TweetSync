<?php

namespace WArslett\TweetSync\ORM\Doctrine;


use Doctrine\ORM\EntityRepository;
use WArslett\TweetSync\Model\TweetRepository as TweetRepositoryInterface;

class TweetRepository extends EntityRepository implements TweetRepositoryInterface
{

}