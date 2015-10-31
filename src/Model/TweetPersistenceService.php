<?php

namespace WArslett\TweetSync\Model;


/**
 * Interface TweetPersistenceService
 * @package WArslett\TweetSync\Model
 */
interface TweetPersistenceService
{
    /**
     * @param Tweet $tweet
     */
    public function persistTweet(Tweet $tweet);

    /**
     * @param $tweet
     */
    public function ensureTweetUpdated($tweet);

    /**
     * @param TwitterUser $twitterUser
     */
    public function persistTwitterUser(TwitterUser $twitterUser);

    /**
     * @param TwitterUser $twitterUser
     */
    public function ensureTwitterUserUpdated(TwitterUser $twitterUser);

    /**
     * @return TwitterUserRepository
     */
    public function getTwitterUserRepository();

    /**
     * @return TweetRepository
     */
    public function getTweetRepository();
}