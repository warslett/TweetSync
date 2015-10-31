<?php

namespace test\WArslett\TweetSync\Model;


use Mockery as m;
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

    public function testPatchFromStdObj_ScreenName()
    {
        $twitterUser = $this->twitterUser();
        $factory = new TwitterUserFactory();
        $userObj = $this->userObj();
        $userObj->screen_name = 'foo';

        $factory->patchFromStdObj($twitterUser, $userObj);

        $twitterUser->shouldHaveReceived('setScreenName')->with($userObj->screen_name)->once();
    }

    private function userObj()
    {
        $obj = new \stdClass();
        $obj->id_str = null;
        $obj->screen_name = null;
        return $obj;
    }

    private function twitterUser()
    {
        $mock = m::mock('\WArslett\TweetSync\Model\TwitterUser');
        $mock->shouldReceive('setId');
        $mock->shouldReceive('setScreenName');
        return $mock;
    }
}
