<?php

namespace WArslett\TweetSync\Remote;

use TwitterAPIExchange;
use WArslett\TweetSync\Model\Tweet;
use WArslett\TweetSync\Model\TweetFactory;

/**
 * Class TwitterApiExchangeRemoteService
 * @package WArslett\TweetSync
 */
class TwitterApiExchangeRemoteService implements RemoteService
{
    /**
     * @var TwitterAPIExchange
     */
    private $tw;

    /**
     * @param TwitterAPIExchange $tw
     */
    public function __construct(TwitterAPIExchange $tw) {
        $this->tw = $tw;
    }

    /**
     * @param string $username
     * @return \stdClass[]
     */
    public function findByTwitterUser($username)
    {
        $response = $this->tw
            ->setGetfield('?screen_name=' . $username . '&exclude_replies=1&include_rts=0')
            ->buildOauth('https://api.twitter.com/1.1/statuses/user_timeline.json', 'GET')
            ->performRequest();
        return json_decode($response);
    }

    /**
     * @param $username
     * @return \stdClass
     */
    public function findTwitterUser($username)
    {
        $response = $this->tw
            ->setGetfield('?screen_name=' . $username)
            ->buildOauth('https://api.twitter.com/1.1/users/show.json', 'GET')
            ->performRequest();
        return json_decode($response);
    }
}