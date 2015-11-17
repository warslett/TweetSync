<?php

namespace WArslett\TweetSync\Remote;


use Abraham\TwitterOAuth\TwitterOAuth;

class TwitterOAuthRemoteService implements RemoteService
{

    /**
     * @var TwitterOAuth
     */
    private $api;

    /**
     * TwitterOAuthRemoteService constructor.
     * @param TwitterOAuth $api
     */
    public function __construct(TwitterOAuth $api)
    {
        $this->api = $api;
    }

    /**
     * @param string $username
     * @return \stdClass[]
     */
    public function findByTwitterUser($username)
    {
        return $this->api->get('statuses/user_timeline', array(
            'screen_name' => $username,
            'exclude_replies' => 1,
            'include_rts' => 0
        ));
    }

    /**
     * @param $username
     * @return \stdClass
     */
    public function findTwitterUser($username)
    {
        return $this->api->get('users/show', array(
            'screen_name' => $username,
        ));
    }
}