<?php

namespace WArslett\TweetSync\Model;


/**
 * Interface TwitterUserRepository
 * @package WArslett\TweetSync
 */
interface TwitterUserRepository
{

    /**
     * @param string $id
     * @return TwitterUser
     */
    public function find($id);
}