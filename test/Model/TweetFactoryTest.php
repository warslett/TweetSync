<?php

namespace test\WArslett\TweetSync\Model;

use Mockery as m;
use WArslett\TweetSync\Model\TweetFactory;

class TweetFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildFromStdObj_ReturnsTypeTweet()
    {
        $factory = new TweetFactory($this->userResolver());
        $stdObj = $this->tweetResponseObj();

        $tweet = $factory->buildFromStdObj($stdObj);

        $this->assertInstanceOf('\WArslett\TweetSync\Model\Tweet', $tweet);
    }

    public function testBuildFromStdObj_CreatedAt()
    {
        $factory = new TweetFactory($this->userResolver());
        $stdObj = $this->tweetResponseObj();
        $stdObj->created_at = 'Sat Oct 24 14:06:39 +0000 2015';

        $tweet = $factory->buildFromStdObj($stdObj);

        $this->assertEquals($stdObj->created_at, $tweet->getCreatedAt()->format('D M j G:i:s O Y'));
    }

    public function testBuildFromStdObj_ID()
    {
        $factory = new TweetFactory($this->userResolver());
        $stdObj = $this->tweetResponseObj();
        $stdObj->id_str = "657921145627394048";

        $tweet = $factory->buildFromStdObj($stdObj);

        $this->assertEquals($stdObj->id_str, $tweet->getID());
    }

    public function testBuildFromStdObj_text()
    {
        $factory = new TweetFactory($this->userResolver());
        $stdObj = $this->tweetResponseObj();
        $stdObj->text = "The rain in spain falls mainly on the plain";

        $tweet = $factory->buildFromStdObj($stdObj);

        $this->assertEquals($stdObj->text, $tweet->getText());
    }

    public function testBuildFromStdObj_User()
    {
        $resolver = $this->userResolver();
        $twitterUser = m::mock('\WArslett\TweetSync\Model\TwitterUser');
        $userResponseObj = new \stdClass();
        $resolver
            ->shouldReceive('resolveFromStdObj')
            ->with($userResponseObj)
            ->andReturn($twitterUser);
        $factory = new TweetFactory($resolver);
        $stdObj = $this->tweetResponseObj();
        $stdObj->user = $userResponseObj;

        $tweet = $factory->buildFromStdObj($stdObj);

        $resolver->shouldHaveReceived('resolveFromStdObj')->with($userResponseObj)->once();
        $this->assertEquals($twitterUser, $tweet->getUser());
    }

    private function tweetResponseObj()
    {
        $obj = new \stdClass();
        $obj->created_at = null;
        $obj->id_str = null;
        $obj->text = null;
        $obj->user = null;
        return $obj;
    }

    private function userResolver()
    {
        $mock = m::mock('\WArslett\TweetSync\Model\TwitterUserResolver');
        $mock->shouldReceive('resolveFromStdObj')->andReturn(
            m::mock('\WArslett\TweetSync\Model\TwitterUser')
        );
        return $mock;
    }
}
