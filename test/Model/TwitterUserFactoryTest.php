<?php

namespace test\WArslett\TweetSync\Model;


use WArslett\TweetSync\Model\TwitterUserFactory;

class TwitterUserFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testBuildFromStdObj_ReturnsTypeTwitterUser()
    {
        $factory = new TwitterUserFactory();
        $userObj = $this->userObj();

        $twitterUser = $factory->buildFromStdObj($userObj);

        $this->assertInstanceOf('\WArslett\TweetSync\Model\TwitterUser', $twitterUser);
    }

    public function testBuildFromStdObj_ID()
    {
        $factory = new TwitterUserFactory();
        $userObj = $this->userObj();
        $userObj->id_str = '5402612';

        $twitterUser = $factory->buildFromStdObj($userObj);

        $this->assertEquals($userObj->id_str, $twitterUser->getId());
    }

    public function testBuildFromStdObj_ScreenName()
    {
        $factory = new TwitterUserFactory();
        $userObj = $this->userObj();
        $userObj->screen_name = 'foo';

        $twitterUser = $factory->buildFromStdObj($userObj);

        $this->assertEquals($userObj->screen_name, $twitterUser->getScreenName());
    }

    private function userObj()
    {
        $obj = new \stdClass();
        $obj->id_str = null;
        $obj->screen_name = null;
        return $obj;
    }
}
