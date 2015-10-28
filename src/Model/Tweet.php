<?php

namespace WArslett\TweetSync\Model;

/**
 * Class Tweet
 * @package WArslett\TweetSync
 */
class Tweet
{

    /**
     * @var \DateTimeImmutable
     */
    private $createdAt;

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $text;

    /**
     * @var TwitterUser
     */
    private $user;

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
     * @param \DateTimeImmutable $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return TwitterUser
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param TwitterUser $user
     */
    public function setUser(TwitterUser $user)
    {
        $this->user = $user;
    }
}