<?php

namespace WArslett\TweetSync\Model;


/**
 * Interface TweetRepository
 * @package WArslett\TweetSync\Model
 */
interface TweetRepository
{
    /**
     * @param string $id
     * @return Tweet
     */
    public function find($id);
}