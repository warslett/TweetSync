<?php

namespace test\WArslett\TweetSync\Remote;


use Mockery as m;
use WArslett\TweetSync\Remote\TweetSync;

class TweetSyncTest extends \PHPUnit_Framework_TestCase
{

    public function testSyncAllForUser_CallsFindByTwitterUserOnce()
    {
        $userObj = new \stdClass();
        $userObj->screen_name = 'foo';
        $userObj->id_str = '5402612';
        $twitterUserRepository = $this->twitterUserRepositoryReturns(null);
        $twitterUser = m::mock('\WArslett\TweetSync\Model\TwitterUser');
        $remote = $this->remoteWithResponseForUser($userObj);
        $sync = new TweetSync(
            $remote,
            $this->persistenceService(
                $twitterUser,
                $twitterUserRepository,
                $this->tweetRepositoryReturns()
            ),
            m::mock('\WArslett\TweetSync\Model\TweetFactory'),
            $this->userFactory($twitterUser)
        );

        $sync->syncAllForUser($userObj->screen_name);

        $remote->shouldHaveReceived('findByTwitterUser')->once()->with($userObj->screen_name);
    }

    public function testSyncAllForUser_CallsRemoteFindTwitterUserOnce()
    {
        $userObj = new \stdClass();
        $userObj->screen_name = 'foo';
        $userObj->id_str = '5402612';
        $twitterUserRepository = $this->twitterUserRepositoryReturns(null);
        $twitterUser = m::mock('\WArslett\TweetSync\Model\TwitterUser');
        $remote = $this->remoteWithResponseForUser($userObj);
        $sync = new TweetSync(
            $remote,
            $this->persistenceService(
                $twitterUser,
                $twitterUserRepository,
                $this->tweetRepositoryReturns()
            ),
            m::mock('\WArslett\TweetSync\Model\TweetFactory'),
            $this->userFactory($twitterUser)
        );

        $sync->syncAllForUser($userObj->screen_name);

        $remote->shouldHaveReceived('findTwitterUser')->once()->with($userObj->screen_name);
    }

    public function testSyncAllForUser_CallsTwitterUserRepositoryFindOnce()
    {
        $userObj = new \stdClass();
        $userObj->screen_name = 'foo';
        $userObj->id_str = '5402612';
        $remote = $this->remoteWithResponseForUser($userObj);
        $twitterUser = m::mock('\WArslett\TweetSync\Model\TwitterUser');
        $twitterUserRepository = $this->twitterUserRepositoryReturns(null);
        $sync = new TweetSync(
            $remote,
            $this->persistenceService(
                $twitterUser,
                $twitterUserRepository,
                $this->tweetRepositoryReturns()
            ),
            m::mock('\WArslett\TweetSync\Model\TweetFactory'),
            $this->userFactory($twitterUser)
        );

        $sync->syncAllForUser($userObj->screen_name);

        $twitterUserRepository->shouldHaveReceived('find')->once()->with($userObj->id_str);
    }

    public function testSyncAllForUser_DoesNotCreateNewTwitterUser_TwitterUserExists()
    {
        $userObj = new \stdClass();
        $userObj->screen_name = 'foo';
        $userObj->id_str = '5402612';
        $remote = $this->remoteWithResponseForUser($userObj);
        $twitterUser = m::mock('\WArslett\TweetSync\Model\TwitterUser');
        $twitterUserRepository = $this->twitterUserRepositoryReturns($twitterUser);
        $userFactory = $this->userFactory();
        $persistence = $this->persistenceService(
            $twitterUser,
            $twitterUserRepository,
            $this->tweetRepositoryReturns()
        );
        $sync = new TweetSync(
            $remote,
            $persistence,
            m::mock('\WArslett\TweetSync\Model\TweetFactory'),
            $userFactory
        );

        $sync->syncAllForUser($userObj->screen_name);

        $userFactory->shouldNotHaveReceived('buildFromStdObj');
        $persistence->shouldNotHaveReceived('persistTwitterUser');
    }

    public function testSyncAllForUser_PatchesExistingTwitterUser_TwitterUserExists()
    {
        $userObj = new \stdClass();
        $userObj->screen_name = 'foo';
        $userObj->id_str = '5402612';
        $remote = $this->remoteWithResponseForUser($userObj);
        $twitterUser = m::mock('\WArslett\TweetSync\Model\TwitterUser');
        $twitterUserRepository = $this->twitterUserRepositoryReturns($twitterUser);
        $userFactory = $this->userFactory();
        $persistence = $this->persistenceService(
            $twitterUser,
            $twitterUserRepository,
            $this->tweetRepositoryReturns()
        );
        $sync = new TweetSync(
            $remote,
            $persistence,
            m::mock('\WArslett\TweetSync\Model\TweetFactory'),
            $userFactory
        );

        $sync->syncAllForUser($userObj->screen_name);

        $userFactory->shouldHaveReceived('patchFromStdObj')->once()->with($twitterUser, $userObj);
    }

    public function testSyncAllForUser_ensuresTwitterUserUpdated_TwitterUserExists()
    {
        $userObj = new \stdClass();
        $userObj->screen_name = 'foo';
        $userObj->id_str = '5402612';
        $remote = $this->remoteWithResponseForUser($userObj);
        $twitterUser = m::mock('\WArslett\TweetSync\Model\TwitterUser');
        $twitterUserRepository = $this->twitterUserRepositoryReturns($twitterUser);
        $userFactory = $this->userFactory();
        $persistence = $this->persistenceService(
            $twitterUser,
            $twitterUserRepository,
            $this->tweetRepositoryReturns()
        );
        $sync = new TweetSync(
            $remote,
            $persistence,
            m::mock('\WArslett\TweetSync\Model\TweetFactory'),
            $userFactory
        );

        $sync->syncAllForUser($userObj->screen_name);

        $persistence->shouldHaveReceived('ensureTwitterUserUpdated')->once()->with($twitterUser);
    }

    public function testSyncAllForUser_DoesNotPatch_NewTwitterUser()
    {
        $userObj = new \stdClass();
        $userObj->screen_name = 'foo';
        $userObj->id_str = '5402612';
        $remote = $this->remoteWithResponseForUser($userObj);
        $twitterUser = m::mock('\WArslett\TweetSync\Model\TwitterUser');
        $twitterUserRepository = $this->twitterUserRepositoryReturns(null);
        $userFactory = $this->userFactory($twitterUser);
        $sync = new TweetSync(
            $remote,
            $this->persistenceService(
                $twitterUser,
                $twitterUserRepository,
                $this->tweetRepositoryReturns()
            ),
            m::mock('\WArslett\TweetSync\Model\TweetFactory'),
            $userFactory
        );

        $sync->syncAllForUser($userObj->screen_name);

        $userFactory->shouldNotHaveReceived('patchFromStdObj');
    }

    public function testSyncAllForUser_CallsUserFactoryBuildFromStdObj_NewTwitterUser()
    {
        $userObj = new \stdClass();
        $userObj->screen_name = 'foo';
        $userObj->id_str = '5402612';
        $remote = $this->remoteWithResponseForUser($userObj);
        $twitterUser = m::mock('\WArslett\TweetSync\Model\TwitterUser');
        $twitterUserRepository = $this->twitterUserRepositoryReturns(null);
        $userFactory = $this->userFactory($twitterUser);
        $sync = new TweetSync(
            $remote,
            $this->persistenceService(
                $twitterUser,
                $twitterUserRepository,
                $this->tweetRepositoryReturns()
            ),
            m::mock('\WArslett\TweetSync\Model\TweetFactory'),
            $userFactory
        );

        $sync->syncAllForUser($userObj->screen_name);

        $userFactory->shouldHaveReceived('buildFromStdObj')->once()->with($userObj);
    }

    public function testSyncAllForUser_PersistsNewTwitterUser_NewTwitterUser()
    {
        $userObj = new \stdClass();
        $userObj->screen_name = 'foo';
        $userObj->id_str = '5402612';
        $remote = $this->remoteWithResponseForUser($userObj);
        $twitterUser = m::mock('\WArslett\TweetSync\Model\TwitterUser');
        $twitterUserRepository = $this->twitterUserRepositoryReturns(null);
        $userFactory = $this->userFactory($twitterUser);
        $persistence = $this->persistenceService(
            $twitterUser,
            $twitterUserRepository,
            $this->tweetRepositoryReturns()
        );
        $sync = new TweetSync(
            $remote,
            $persistence,
            m::mock('\WArslett\TweetSync\Model\TweetFactory'),
            $userFactory
        );

        $sync->syncAllForUser($userObj->screen_name);

        $persistence->shouldHaveReceived('persistTwitterUser')->once()->with($twitterUser);
    }

    public function testSyncAllForUser_DoesNotCallBuildFromStdObj_ForUserWithNoTweets()
    {
        $userObj = new \stdClass();
        $userObj->screen_name = 'foo';
        $userObj->id_str = '5402612';
        $remote = $this->remoteWithResponseForUser($userObj);
        $twitterUser = m::mock('\WArslett\TweetSync\Model\TwitterUser');
        $twitterUserRepository = $this->twitterUserRepositoryReturns(null);
        $factory = m::mock('\WArslett\TweetSync\Model\TweetFactory');
        $sync = new TweetSync(
            $remote,
            $this->persistenceService(
                $twitterUser,
                $twitterUserRepository,
                $this->tweetRepositoryReturns()
            ),
            $factory,
            $this->userFactory($twitterUser)
        );

        $sync->syncAllForUser($userObj->screen_name);

        $factory->shouldNotHaveReceived('buildFromStdObj');
    }

    public function testSyncAllForUser_CallsTweetRepositoryFindOnce_ForUserWithOneNewTweet()
    {
        $userObj = new \stdClass();
        $userObj->screen_name = 'foo';
        $userObj->id_str = '5402612';
        $tweetId = '657921145627394048';
        $remote = $this->remoteWithResponseForUser($userObj, [$this->tweetObj($tweetId)]);
        $twitterUser = m::mock('\WArslett\TweetSync\Model\TwitterUser');
        $twitterUserRepository = $this->twitterUserRepositoryReturns(null);
        $factory = m::mock('\WArslett\TweetSync\Model\TweetFactory');
        $factory->shouldReceive('buildFromStdObj')->andReturn(m::mock('\WArslett\TweetSync\Model\Tweet'));
        $tweetRepository = $this->tweetRepositoryReturns([$tweetId => null]);
        $persistenceService = $this->persistenceService(
            $twitterUser,
            $twitterUserRepository,
            $tweetRepository
        );
        $persistenceService->shouldReceive('persistTweet');
        $sync = new TweetSync(
            $remote,
            $persistenceService,
            $factory,
            $this->userFactory($twitterUser)
        );

        $sync->syncAllForUser($userObj->screen_name);

        $tweetRepository->shouldHaveReceived('find')->with($tweetId)->once();
    }

    public function testSyncAllForUser_CallsTweetRepositoryFindTwice_ForUserWithTwoNewTweets()
    {
        $userObj = new \stdClass();
        $userObj->screen_name = 'foo';
        $userObj->id_str = '5402612';
        $remote = $this->remoteWithResponseForUser($userObj, $this->arrayOfXNoOfObjects(2));
        $twitterUser = m::mock('\WArslett\TweetSync\Model\TwitterUser');
        $twitterUserRepository = $this->twitterUserRepositoryReturns(null);
        $factory = m::mock('\WArslett\TweetSync\Model\TweetFactory');
        $factory->shouldReceive('buildFromStdObj')->andReturn(m::mock('\WArslett\TweetSync\Model\Tweet'));
        $tweetRepository = $this->tweetRepositoryReturns();
        $persistenceService = $this->persistenceService(
            $twitterUser,
            $twitterUserRepository,
            $tweetRepository
        );
        $persistenceService->shouldReceive('persistTweet');
        $sync = new TweetSync(
            $remote,
            $persistenceService,
            $factory,
            $this->userFactory($twitterUser)
        );

        $sync->syncAllForUser($userObj->screen_name);

        $tweetRepository->shouldHaveReceived('find')->twice();
    }

    public function testSyncAllForUser_DoesNotBuildNewTweet_ForUserWithOneExistingTweet()
    {
        $userObj = new \stdClass();
        $userObj->screen_name = 'foo';
        $userObj->id_str = '5402612';
        $tweetId = '657921145627394048';
        $remote = $this->remoteWithResponseForUser($userObj, [$this->tweetObj($tweetId)]);
        $twitterUser = m::mock('\WArslett\TweetSync\Model\TwitterUser');
        $twitterUserRepository = $this->twitterUserRepositoryReturns($twitterUser);
        $factory = m::mock('\WArslett\TweetSync\Model\TweetFactory');
        $factory->shouldReceive('patchFromStdObj');
        $tweetRepository = $this->tweetRepositoryReturns(
            [$tweetId => m::mock('\WArslett\TweetSync\Model\Tweet')]
        );
        $persistenceService = $this->persistenceService(
            $twitterUser,
            $twitterUserRepository,
            $tweetRepository
        );
        $sync = new TweetSync(
            $remote,
            $persistenceService,
            $factory,
            $this->userFactory()
        );

        $sync->syncAllForUser($userObj->screen_name);

        $factory->shouldNotHaveReceived('buildFromStdObj');
        $persistenceService->shouldNotHaveReceived('persistTweet');
    }

    public function testSyncAllForUser_PatchesExistingTweet_ForUserWithOneExistingTweet()
    {
        $userObj = new \stdClass();
        $userObj->screen_name = 'foo';
        $userObj->id_str = '5402612';
        $tweetId = '657921145627394048';
        $tweetObj = $this->tweetObj($tweetId);
        $tweet = m::mock('\WArslett\TweetSync\Model\Tweet');
        $remote = $this->remoteWithResponseForUser($userObj, [$tweetObj]);
        $twitterUser = m::mock('\WArslett\TweetSync\Model\TwitterUser');
        $twitterUserRepository = $this->twitterUserRepositoryReturns($twitterUser);
        $factory = m::mock('\WArslett\TweetSync\Model\TweetFactory');
        $factory->shouldReceive('patchFromStdObj');
        $tweetRepository = $this->tweetRepositoryReturns(
            [$tweetId => $tweet]
        );
        $persistenceService = $this->persistenceService(
            $twitterUser,
            $twitterUserRepository,
            $tweetRepository
        );
        $sync = new TweetSync(
            $remote,
            $persistenceService,
            $factory,
            $this->userFactory()
        );

        $sync->syncAllForUser($userObj->screen_name);

        $factory->shouldHaveReceived('patchFromStdObj')->with($tweet, $tweetObj);
    }

    public function testSyncAllForUser_ensuresTweetUpdated_ForUserWithOneExistingTweet()
    {
        $userObj = new \stdClass();
        $userObj->screen_name = 'foo';
        $userObj->id_str = '5402612';
        $tweetId = '657921145627394048';
        $tweetObj = $this->tweetObj($tweetId);
        $tweet = m::mock('\WArslett\TweetSync\Model\Tweet');
        $remote = $this->remoteWithResponseForUser($userObj, [$tweetObj]);
        $twitterUser = m::mock('\WArslett\TweetSync\Model\TwitterUser');
        $twitterUserRepository = $this->twitterUserRepositoryReturns($twitterUser);
        $factory = m::mock('\WArslett\TweetSync\Model\TweetFactory');
        $factory->shouldReceive('patchFromStdObj');
        $tweetRepository = $this->tweetRepositoryReturns(
            [$tweetId => $tweet]
        );
        $persistenceService = $this->persistenceService(
            $twitterUser,
            $twitterUserRepository,
            $tweetRepository
        );
        $sync = new TweetSync(
            $remote,
            $persistenceService,
            $factory,
            $this->userFactory()
        );

        $sync->syncAllForUser($userObj->screen_name);

        $persistenceService->shouldHaveReceived('ensureTweetUpdated')->with($tweet);
    }

    public function testSyncAllForUser_CallsBuildFromStdObjOnce_ForUserWithOneNewTweets()
    {
        $userObj = new \stdClass();
        $userObj->screen_name = 'foo';
        $userObj->id_str = '5402612';
        $remote = $this->remoteWithResponseForUser($userObj, $this->arrayOfXNoOfObjects(1));
        $twitterUser = m::mock('\WArslett\TweetSync\Model\TwitterUser');
        $twitterUserRepository = $this->twitterUserRepositoryReturns(null);
        $factory = m::mock('\WArslett\TweetSync\Model\TweetFactory');
        $factory->shouldReceive('buildFromStdObj')->andReturn(m::mock('\WArslett\TweetSync\Model\Tweet'));
        $persistenceService = $this->persistenceService(
            $twitterUser,
            $twitterUserRepository,
            $this->tweetRepositoryReturns()
        );
        $persistenceService->shouldReceive('persistTweet');
        $sync = new TweetSync(
            $remote,
            $persistenceService,
            $factory,
            $this->userFactory($twitterUser)
        );

        $sync->syncAllForUser($userObj->screen_name);

        $factory->shouldHaveReceived('buildFromStdObj')->once();
    }

    public function testSyncAllForUser_PersistsOneTweet_ForUserWithOneNewTweets()
    {
        $userObj = new \stdClass();
        $userObj->screen_name = 'foo';
        $userObj->id_str = '5402612';
        $remote = $this->remoteWithResponseForUser($userObj, $this->arrayOfXNoOfObjects(1));
        $tweet = m::mock('\WArslett\TweetSync\Model\Tweet');
        $factory = m::mock('\WArslett\TweetSync\Model\TweetFactory');
        $factory->shouldReceive('buildFromStdObj')->andReturn($tweet);
        $twitterUserRepository = $this->twitterUserRepositoryReturns(null);
        $twitterUser = m::mock('\WArslett\TweetSync\Model\TwitterUser');
        $persistenceService = $this->persistenceService(
            $twitterUser,
            $twitterUserRepository,
            $this->tweetRepositoryReturns()
        );
        $persistenceService->shouldReceive('persistTweet')->with($tweet);
        $sync = new TweetSync(
            $remote,
            $persistenceService,
            $factory,
            $this->userFactory($twitterUser)
        );

        $sync->syncAllForUser($userObj->screen_name);

        $persistenceService->shouldHaveReceived('persistTweet')->once()->with($tweet);
    }

    private function arrayOfXNoOfObjects($x)
    {
        $objects = array();
        while (count($objects)<$x) {
            $objects[] = $this->tweetObj();
        }
        return $objects;
    }

    private function remoteWithResponseForUser($userObj, $response = [])
    {
        $mock = m::mock('\WArslett\TweetSync\Remote\RemoteService');
        $mock->shouldReceive('findByTwitterUser')->with($userObj->screen_name)->andReturn($response);
        $mock->shouldReceive('findTwitterUser')->with($userObj->screen_name)->andReturn($userObj);
        return $mock;
    }

    private function userFactory($return = null)
    {
        $mock = m::mock('\WArslett\TweetSync\Model\TwitterUserFactory');
        if (!is_null($return)) {
            $mock->shouldReceive('buildFromStdObj')->andReturn($return);
        } else {
            $mock->shouldReceive('patchFromStdObj')->withAnyArgs();
        }
        return $mock;
    }

    private function persistenceService($expect, $twitterUserRepository, $tweetRepository)
    {
        $mock = m::mock('\WArslett\TweetSync\Model\TweetPersistenceService');
        $mock->shouldReceive('persistTwitterUser')->with($expect);
        $mock->shouldReceive('getTwitterUserRepository')->andReturn($twitterUserRepository);
        $mock->shouldReceive('getTweetRepository')->andReturn($tweetRepository);
        $mock->shouldReceive('ensureTwitterUserUpdated')->with($expect);
        $mock->shouldReceive('ensureTweetUpdated');
        return $mock;
    }

    private function twitterUserRepositoryReturns($user)
    {
        $mock = m::mock('\WArslett\TweetSync\Model\TwitterUserRepository');
        $mock->shouldReceive('find')->andReturn($user);
        return $mock;
    }

    private function tweetRepositoryReturns($tweets = [])
    {
        $mock = m::mock('\WArslett\TweetSync\Model\TweetRepository');
        $mock->shouldReceive('find')->with(null)->andReturnNull();
        foreach ($tweets as $id => $tweet) {
            $mock->shouldReceive('find')->with($id)->andReturn($tweet);
        }
        return $mock;
    }

    private function tweetObj($id = null)
    {
        $obj = new \stdClass();
        $obj->id_str = $id;
        return $obj;
    }
}
