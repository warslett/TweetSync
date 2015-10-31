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
     * @param string $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
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