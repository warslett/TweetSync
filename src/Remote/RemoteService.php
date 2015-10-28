<?php

namespace WArslett\TweetSync\Remote;

use WArslett\TweetSync\Model\Tweet;

/**
 * Interface RemoteService
 * @package WArslett\TweetSync
 */
interface RemoteService
{
    /**
     * @param string $username
     * @return Tweet[]
     */
    public function findByTwitterUser($username);
}