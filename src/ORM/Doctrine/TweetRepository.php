<?php

namespace WArslett\TweetSync\ORM\Doctrine;


use Doctrine\ORM\EntityRepository;
use WArslett\TweetSync\Model\TweetRepository as TweetRepositoryInterface;

class TweetRepository extends EntityRepository implements TweetRepositoryInterface
{

    public function findByUsername($username)
    {
        return $this->_em->createQuery(
            'SELECT t FROM \WArslett\TweetSync\Model\Tweet AS t JOIN t.user AS u WHERE u.screenName = :username'
        )->setParameter(':username', $username)->execute();
    }
}