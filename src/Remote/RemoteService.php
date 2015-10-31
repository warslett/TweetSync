<?php

namespace WArslett\TweetSync\Remote;

/**
 * Interface RemoteService
 * @package WArslett\TweetSync
 */
interface RemoteService
{
    /**
     * @param string $username
     * @return \stdClass[]
     */
    public function findByTwitterUser($username);

    /**
     * @param $username
     * @return \stdClass
     */
    public function findTwitterUser($username);
}