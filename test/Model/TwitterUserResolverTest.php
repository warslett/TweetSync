<?php

namespace test\WArslett\TweetSync\Model;

use Mockery as m;
use WArslett\TweetSync\Model\TwitterUserResolver;

class TwitterUserResolverTest extends \PHPUnit_Framework_TestCase
{

    public function testResolve_CallsFindOnRepository()
    {
        $repository = $this->twitterUserRepository();
        $userObj = $this->userResponseStdObject();
        $userObj->id_str = '481505606';
        $resolver = new TwitterUserResolver($repository, $this->twitterUserFactory());

        $resolver->resolveFromStdObj($userObj);

        $repository->shouldHaveReceived('find')->with($userObj->id_str)->once();
    }

    public function testResolve_ReturnsFoundTwitterUser_WhenUserFoundByRepository()
    {
        $repository = m::mock('\WArslett\TweetSync\Model\TwitterUserRepository');
        $userObj = $this->userResponseStdObject();
        $userObj->id_str = '481505606';
        $foundUser = m::mock('\WArslett\TweetSync\Model\TwitterUser');
        $repository->shouldReceive('find')->with($userObj->id_str)->andReturn($foundUser);
        $factory = $this->twitterUserFactory();
        $resolver = new TwitterUserResolver($repository, $factory);

        $resolvedUser = $resolver->resolveFromStdObj($userObj);

        $factory->shouldNotHaveReceived('buildFromStdObj');
        $this->assertEquals($foundUser, $resolvedUser);
    }

    public function testResolve_ReturnsBuiltTwitterUser_WhenUserNotFoundByRepository()
    {
        $repository = m::mock('\WArslett\TweetSync\Model\TwitterUserRepository');
        $userObj = $this->userResponseStdObject();
        $userObj->id_str = '481505606';
        $builtUser = m::mock('\WArslett\TweetSync\Model\TwitterUser');
        $repository->shouldReceive('find')->with($userObj->id_str)->andReturn(false);
        $factory = m::mock('\WArslett\TweetSync\Model\TwitterUserFactory');
        $factory->shouldReceive('buildFromStdObj')->with($userObj)->andReturn($builtUser);
        $resolver = new TwitterUserResolver($repository, $factory);

        $resolvedUser = $resolver->resolveFromStdObj($userObj);

        $factory->shouldHaveReceived('buildFromStdObj')->with($userObj)->once();
        $this->assertEquals($builtUser, $resolvedUser);
    }

    public function userResponseStdObject()
    {
        $obj = new \stdClass();
        $obj->id_str = '';
        return $obj;
    }

    public function twitterUserRepository()
    {
        $mock = m::mock('\WArslett\TweetSync\Model\TwitterUserRepository');
        $mock->shouldReceive('find');
        return $mock;
    }

    public function twitterUserFactory()
    {
        $mock = m::mock('\WArslett\TweetSync\Model\TwitterUserFactory');
        $mock->shouldReceive('buildFromStdObj');
        return $mock;
    }
}
