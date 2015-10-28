<?php

namespace test\WArslett\TweetSync\Remote;

use Mockery as m;
use WArslett\TweetSync\Remote\TwitterApiExchangeRemoteService;

class TwitterApiExchangeRemoteServiceTest extends \PHPUnit_Framework_TestCase
{

    public function testFindByTwitterID_CallsAPIWithUsername_WhenCalledWithUsername()
    {
        $api = $this->twitterAPIRemoteFileExchange();
        $api->shouldReceive('performRequest')->andReturn($this->responseWithXNoOfObjects(0));
        $repo = new TwitterApiExchangeRemoteService($api, m::mock('\WArslett\TweetSync\Model\TweetFactory'));

        $repo->findByTwitterUser('foo');

        $api->shouldHaveReceived('buildOauth')->once()->with('https://api.twitter.com/1.1/statuses/user_timeline.json', 'GET');
        $api->shouldHaveReceived('setGetfield')->once()->with('?screen_name=foo&&exclude_replies=1&include_rts=0');
        $api->shouldHaveReceived('performRequest')->once();
    }

    public function testFindByTwitterID_DoesNotCallTweetFactory_FromUserWith0Tweets()
    {
        $api = $this->twitterAPIRemoteFileExchange();
        $factory = m::mock('\WArslett\TweetSync\Model\TweetFactory');
        $repo = new TwitterApiExchangeRemoteService($api, $factory);
        $api->shouldReceive('performRequest')->once()->andReturn($this->responseWithXNoOfObjects(0));

        $repo->findByTwitterUser('foo');

        $factory->shouldNotHaveReceived('buildFromStdObj');
    }

    public function testFindByTwitterID_ReturnsEmptyArray_FromUserWith0Tweets()
    {
        $api = $this->twitterAPIRemoteFileExchange();
        $repo = new TwitterApiExchangeRemoteService($api, m::mock('\WArslett\TweetSync\Model\TweetFactory'));
        $api->shouldReceive('performRequest')->once()->andReturn($this->responseWithXNoOfObjects(0));

        $tweets = $repo->findByTwitterUser('foo');

        $this->assertEquals([], $tweets);
    }

    public function testFindByTwitterID_CallsTweetFactoryOnce_FromUserWith1Tweets()
    {
        $api = $this->twitterAPIRemoteFileExchange();
        $tweet = m::mock('\WArslett\TweetSync\Model\Tweet');
        $factory = m::mock('\WArslett\TweetSync\Model\TweetFactory');
        $factory->shouldReceive('buildFromStdObj')->andReturn($tweet);
        $repo = new TwitterApiExchangeRemoteService($api, $factory);
        $api->shouldReceive('performRequest')->once()->andReturn($this->responseWithXNoOfObjects(1));

        $tweets = $repo->findByTwitterUser('foo');

        $factory->shouldHaveReceived('buildFromStdObj')->once()->with(m::type('\stdClass'));
    }

    public function testFindByTwitterID_ReturnsArrayOf1Tweet_FromUserWith1Tweets()
    {
        $api = $this->twitterAPIRemoteFileExchange();
        $tweet = m::mock('\WArslett\TweetSync\Model\Tweet');
        $factory = m::mock('\WArslett\TweetSync\Model\TweetFactory');
        $factory->shouldReceive('buildFromStdObj')->andReturn($tweet);
        $repo = new TwitterApiExchangeRemoteService($api, $factory);
        $api->shouldReceive('performRequest')->once()->andReturn($this->responseWithXNoOfObjects(1));

        $tweets = $repo->findByTwitterUser('foo');

        $this->assertEquals([$tweet], $tweets);
    }

    public function testFindByTwitterID_CallsTweetFactoryTwice_FromUserWith2Tweets()
    {
        $api = $this->twitterAPIRemoteFileExchange();
        $factory = m::mock('\WArslett\TweetSync\Model\TweetFactory');
        $factory->shouldReceive('buildFromStdObj');
        $repo = new TwitterApiExchangeRemoteService($api, $factory);
        $api->shouldReceive('performRequest')->once()->andReturn($this->responseWithXNoOfObjects(2));

        $repo->findByTwitterUser('foo');

        $factory->shouldHaveReceived('buildFromStdObj')->twice();
    }

    public function testFindByTwitterID_ReturnsArraySize2_FromUserWith2Tweets()
    {
        $api = $this->twitterAPIRemoteFileExchange();
        $factory = m::mock('\WArslett\TweetSync\Model\TweetFactory');
        $factory->shouldReceive('buildFromStdObj');
        $repo = new TwitterApiExchangeRemoteService($api, $factory);
        $api->shouldReceive('performRequest')->once()->andReturn($this->responseWithXNoOfObjects(2));

        $tweets = $repo->findByTwitterUser('foo');

        $this->assertEquals(2, count($tweets));
    }

    private function twitterAPIRemoteFileExchange()
    {
        $mock = m::mock('\TwitterAPIExchange');
        $mock->shouldReceive('setGetfield')->andReturnSelf();
        $mock->shouldReceive('buildOauth')->andReturnSelf();
        return $mock;
    }

    private function responseWithXNoOfObjects($x)
    {
        $collection = array();
        for($i = 0; $i<$x; $i++) {
            $collection[] = new \stdClass();
        }
        return json_encode($collection);
    }
}
