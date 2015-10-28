<?php

namespace WArslett\TweetSync\Model;


/**
 * Class TwitterUserFactory
 * @package WArslett\TweetSync
 */
class TwitterUserFactory
{

    /**
     * @param \stdClass $obj
     * @return TwitterUser
     */
    public function buildFromStdObj(\stdClass $obj)
    {
        $twitterUser = new TwitterUser();
        $twitterUser->setId($obj->id_str);
        $twitterUser->setScreenName($obj->screen_name);
        return $twitterUser;
    }
}