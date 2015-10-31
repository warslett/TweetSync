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
        $twitterUser = new TwitterUser($obj->id_str);
        $this->patchFromStdObj($twitterUser, $obj);
        return $twitterUser;
    }

    public function patchFromStdObj(TwitterUser $twitterUser, \stdClass $obj)
    {
        $twitterUser->setScreenName($obj->screen_name);
    }
}