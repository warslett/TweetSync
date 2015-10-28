<?php

namespace WArslett\TweetSync\Model;


/**
 * Class TwitterUser
 * @package WArslett\TweetSync
 */
class TwitterUser
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $screenName;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getScreenName()
    {
        return $this->screenName;
    }

    /**
     * @param string $screenName
     */
    public function setScreenName($screenName)
    {
        $this->screenName = $screenName;
    }
}