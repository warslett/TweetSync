<?php

namespace test\WArslett\TweetSync\Remote;

use Mockery as m;
use WArslett\TweetSync\Remote\TwitterOAuthRemoteService;

class TwitterOAuthRemoteServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testFindByTwitterUser_CallsAPIWithUsername_WhenCalledWithUsername()
    {
        $user = 'foo';
        $api = $this->twitterAPIRemoteFileExchange();
        $api->shouldReceive('get');
        $service = new TwitterOAuthRemoteService($api);

        $service->findByTwitterUser($user);

        $api->shouldHaveReceived('get')->once()->with('statuses/user_timeline', array(
            'screen_name' => $user,
            'exclude_replies' => 1,
            'include_rts' => 0
        ));
    }

    public function testFindByTwitterID_ReturnsEmptyArray_FromUserWith0Tweets()
    {
        $api = $this->twitterAPIRemoteFileExchange();
        $resource = 'statuses/user_timeline';
        $args = array(
            'screen_name' => 'foo',
            'exclude_replies' => 1,
            'include_rts' => 0
        );
        $response = $this->responseWithXNoOfObjects(0);
        $service = new TwitterOAuthRemoteService($api);
        $api->shouldReceive('get')->with($resource, $args)->andReturn($response);

        $objects = $service->findByTwitterUser('foo');

        $this->assertEquals($response, $objects);
    }

    public function testFindByTwitterID_ReturnsArrayOfOneObject_FromUserWith1Tweets()
    {
        $api = $this->twitterAPIRemoteFileExchange();
        $resource = 'statuses/user_timeline';
        $args = array(
            'screen_name' => 'foo',
            'exclude_replies' => 1,
            'include_rts' => 0
        );
        $response = $this->responseWithXNoOfObjects(1);
        $service = new TwitterOAuthRemoteService($api);
        $api->shouldReceive('get')->with($resource, $args)->andReturn($response);

        $objects = $service->findByTwitterUser('foo');

        $this->assertEquals($response, $objects);
    }

    public function testFindByTwitterID_ReturnsArrayOfTwoObjects_FromUserWith2Tweets()
    {
        $api = $this->twitterAPIRemoteFileExchange();
        $resource = 'statuses/user_timeline';
        $args = array(
            'screen_name' => 'foo',
            'exclude_replies' => 1,
            'include_rts' => 0
        );
        $response = $this->responseWithXNoOfObjects(2);
        $service = new TwitterOAuthRemoteService($api);
        $api->shouldReceive('get')->with($resource, $args)->andReturn($response);

        $objects = $service->findByTwitterUser('foo');

        $this->assertEquals($response, $objects);
    }

    public function testFindTwitterUser_CallsAPIWithUsername_WhenCalledWithUsername()
    {
        $api = $this->twitterAPIRemoteFileExchange();
        $resource = 'users/show';
        $args = array(
            'screen_name' => 'foo'
        );
        $service = new TwitterOAuthRemoteService($api);
        $api->shouldReceive('get');

        $service->findTwitterUser('foo');

        $api->shouldHaveReceived('get')->with($resource, $args);
    }

    public function testFindTwitterUser_ReturnsFoundUser_WhenCalledWithUsername()
    {
        $api = $this->twitterAPIRemoteFileExchange();
        $resource = 'users/show';
        $args = array(
            'screen_name' => 'foo'
        );
        $response = new \stdClass();
        $service = new TwitterOAuthRemoteService($api);
        $api->shouldReceive('get')->with($resource, $args)->andReturn($response);

        $object = $service->findTwitterUser('foo');

        $this->assertEquals($response, $object);
    }

    private function twitterAPIRemoteFileExchange()
    {
        $mock = m::mock('\Abraham\TwitterOAuth\TwitterOAuth');
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
