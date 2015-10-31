<?php

namespace test\WArslett\TweetSync\Model;

use Mockery as m;
use WArslett\TweetSync\Model\TweetFactory;

class TweetFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildFromStdObj_ReturnsTypeTweet()
    {
        $factory = new TweetFactory($this->userRepository());
        $stdObj = $this->tweetResponseObj();

        $tweet = $factory->buildFromStdObj($stdObj);

        $this->assertInstanceOf('\WArslett\TweetSync\Model\Tweet', $tweet);
    }

    public function testBuildFromStdObj_CreatedAt()
    {
        $factory = new TweetFactory($this->userRepository());
        $stdObj = $this->tweetResponseObj();
        $stdObj->created_at = 'Sat Oct 24 14:06:39 +0000 2015';

        $tweet = $factory->buildFromStdObj($stdObj);

        $this->assertEquals($stdObj->created_at, $tweet->getCreatedAt()->format('D M d G:i:s O Y'));
    }

    public function testBuildFromStdObj_ID()
    {
        $factory = new TweetFactory($this->userRepository());
        $stdObj = $this->tweetResponseObj();
        $stdObj->id_str = "657921145627394048";

        $tweet = $factory->buildFromStdObj($stdObj);

        $this->assertEquals($stdObj->id_str, $tweet->getID());
    }

    public function testBuildFromStdObj_text()
    {
        $factory = new TweetFactory($this->userRepository());
        $stdObj = $this->tweetResponseObj();
        $stdObj->text = "The rain in spain falls mainly on the plain";

        $tweet = $factory->buildFromStdObj($stdObj);

        $this->assertEquals($stdObj->text, $tweet->getText());
    }

    public function testBuildFromStdObj_User()
    {
        $repository = m::mock('\WArslett\TweetSync\Model\TwitterUserRepository');
        $twitterUser = m::mock('\WArslett\TweetSync\Model\TwitterUser');
        $stdObj = $this->tweetResponseObj();
        $stdObj->user->id_str = '5402612';
        $repository
            ->shouldReceive('find')
            ->with($stdObj->user->id_str)
            ->andReturn($twitterUser);
        $factory = new TweetFactory($repository);

        $tweet = $factory->buildFromStdObj($stdObj);

        $repository->shouldHaveReceived('find')->with($stdObj->user->id_str)->once();
        $this->assertEquals($twitterUser, $tweet->getUser());
    }

    public function testPatchFromStdObj_CallsSetCreatedAt()
    {
        $tweet = $this->tweet();
        $factory = new TweetFactory($this->userRepository());
        $stdObj = $this->tweetResponseObj();
        $stdObj->created_at = 'Sat Oct 24 14:06:39 +0000 2015';

        $factory->patchFromStdObj($tweet, $stdObj);

        $tweet->shouldHaveReceived('setCreatedAt')->with(
            m::on(function (\DateTimeImmutable $arg) use ($stdObj) {
                if ($stdObj->created_at == $arg->format('D M d G:i:s O Y')) {
                    return true;
                }
                return false;
            })
        )->once();
    }

    public function testPatchFromStdObj_CallsSetText()
    {
        $tweet = $this->tweet();
        $factory = new TweetFactory($this->userRepository());
        $stdObj = $this->tweetResponseObj();
        $stdObj->text = "The rain in spain falls mainly on the plain";

        $factory->patchFromStdObj($tweet, $stdObj);

        $tweet->shouldHaveReceived('setText')->with($stdObj->text)->once();
    }

    public function testPatchFromStdObj_User()
    {
        $tweet = $this->tweet();
        $repository = m::mock('\WArslett\TweetSync\Model\TwitterUserRepository');
        $twitterUser = m::mock('\WArslett\TweetSync\Model\TwitterUser');
        $stdObj = $this->tweetResponseObj();
        $stdObj->user->id_str = '5402612';
        $repository
            ->shouldReceive('find')
            ->with($stdObj->user->id_str)
            ->andReturn($twitterUser);
        $factory = new TweetFactory($repository);

        $factory->patchFromStdObj($tweet, $stdObj);

        $repository->shouldHaveReceived('find')->with($stdObj->user->id_str)->once();
        $tweet->shouldHaveReceived('setUser')->with($twitterUser)->once();
    }

    private function tweetResponseObj()
    {
        $obj = new \stdClass();
        $obj->created_at = null;
        $obj->id_str = null;
        $obj->text = null;
        $userObj = new \stdClass();
        $userObj->id_str = null;
        $obj->user = $userObj;
        return $obj;
    }

    private function userRepository()
    {
        $mock = m::mock('\WArslett\TweetSync\Model\TwitterUserRepository');
        $mock->shouldReceive('find')->andReturn(
            m::mock('\WArslett\TweetSync\Model\TwitterUser')
        );
        return $mock;
    }

    /**
     * @return m\MockInterface
     */
    private function tweet()
    {
        $mock = m::mock('\WArslett\TweetSync\Model\Tweet');
        $mock->shouldReceive('setCreatedAt');
        $mock->shouldReceive('setId');
        $mock->shouldReceive('setText');
        $mock->shouldReceive('setUser');
        return $mock;
    }
}
