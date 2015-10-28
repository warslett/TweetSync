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
     * @var TweetFactory
     */
    private $factory;

    /**
     * @param TwitterAPIExchange $tw
     * @param TweetFactory $f
     */
    public function __construct(TwitterAPIExchange $tw, TweetFactory $f) {
        $this->tw = $tw;
        $this->factory = $f;
    }

    /**
     * @param string $username
     * @return Tweet[]
     */
    public function findByTwitterUser($username)
    {
        $tweets = array();
        $response = json_decode($this->tw
            ->buildOauth('https://api.twitter.com/1.1/statuses/user_timeline.json', 'GET')
            ->setGetfield('?screen_name=' . $username . '&&exclude_replies=1&include_rts=0')
            ->performRequest()
        );
        foreach($response as $object) {
            $tweets[] = $this->factory->buildFromStdObj($object);
        }
        return $tweets;
    }
}