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
        $repo = new TwitterApiExchangeRemoteService($api);

        $repo->findByTwitterUser('foo');

        $api->shouldHaveReceived('buildOauth')->once()->with('https://api.twitter.com/1.1/statuses/user_timeline.json', 'GET');
        $api->shouldHaveReceived('setGetfield')->once()->with('?screen_name=foo&exclude_replies=1&include_rts=0');
        $api->shouldHaveReceived('performRequest')->once();
    }

    public function testFindByTwitterID_ReturnsEmptyArray_FromUserWith0Tweets()
    {
        $api = $this->twitterAPIRemoteFileExchange();
        $repo = new TwitterApiExchangeRemoteService($api);
        $api->shouldReceive('performRequest')->once()->andReturn($this->responseWithXNoOfObjects(0));

        $objects = $repo->findByTwitterUser('foo');

        $this->assertEquals([], $objects);
    }

    public function testFindByTwitterID_ReturnsArrayOfOneObject_FromUserWith1Tweets()
    {
        $api = $this->twitterAPIRemoteFileExchange();
        $repo = new TwitterApiExchangeRemoteService($api);
        $response = $this->responseWithXNoOfObjects(1);
        $api->shouldReceive('performRequest')->once()->andReturn($response);

        $objects = $repo->findByTwitterUser('foo');

        $this->assertEquals(json_decode($response), $objects);
    }

    public function testFindByTwitterID_ReturnsArrayOfTwoObjects_FromUserWith2Tweets()
    {
        $api = $this->twitterAPIRemoteFileExchange();
        $repo = new TwitterApiExchangeRemoteService($api);
        $response = $this->responseWithXNoOfObjects(2);
        $api->shouldReceive('performRequest')->once()->andReturn($response);

        $objects = $repo->findByTwitterUser('foo');

        $this->assertEquals(json_decode($response), $objects);
    }

    public function testFindTwitterUser_CallsAPIWithUsername_WhenCalledWithUsername()
    {
        $api = $this->twitterAPIRemoteFileExchange();
        $response = '{}';
        $api->shouldReceive('performRequest')->andReturn($response);
        $repo = new TwitterApiExchangeRemoteService($api);
        $userObj = 'foo';

        $userObj = $repo->findTwitterUser($userObj);

        $this->assertEquals($response, json_encode($userObj));
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
        while (count($collection)<$x) {
            $collection[] = new \stdClass();
        }
        return json_encode($collection);
    }
}
