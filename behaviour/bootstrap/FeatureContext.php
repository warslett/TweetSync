<?php
use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use WArslett\TweetSync\Console\SyncUserCommand;
use WArslett\TweetSync\Model\Tweet;
use WArslett\TweetSync\Model\TweetFactory;
use WArslett\TweetSync\Model\TwitterUser;
use WArslett\TweetSync\Model\TwitterUserFactory;
use WArslett\TweetSync\Model\TwitterUserResolver;
use WArslett\TweetSync\ORM\Doctrine\TweetPersistenceService;
use Mockery as m;
use PHPUnit_Framework_Assert as a;
use WArslett\TweetSync\Remote\TweetSync;
use WArslett\TweetSync\Remote\TwitterApiExchangeRemoteService;
use WArslett\TweetSync\Remote\TwitterOAuthRemoteService;

/**
 * Class FeatureContext
 */
class FeatureContext implements Context
{

    /**
     * @var \WArslett\TweetSync\Model\TweetPersistenceService
     */
    private $tweetPersistenceService;

    /**
     * @var \WArslett\TweetSync\Remote\RemoteService
     */
    private $remote;

    /**
     * @var m\MockInterface
     */
    private $api;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @Given that I have an empty tweet repository
     */
    public function thatIHaveAnEmptyTweetRespository()
    {
        $db_file_loc = 'behaviour/tmp/test.sqlite';
        if(file_exists($db_file_loc)) {
            unlink($db_file_loc);
        }
        touch($db_file_loc);

        $paths = array(__DIR__ . "/../../src/Resources/config/doctrine");

        $dbParams = array(
            'driver'   => 'pdo_sqlite',
            'path'     => $db_file_loc
        );

        $config = Setup::createYAMLMetadataConfiguration($paths);
        $this->entityManager = EntityManager::create($dbParams, $config);
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->createSchema($this->entityManager->getMetadataFactory()->getAllMetadata());

        $this->tweetPersistenceService = new TweetPersistenceService($this->entityManager);
    }



    /**
     * @Given that I have a dummy Twitter API
     */
    public function thatIHaveADummyTwitterAPI()
    {
        $this->api = m::mock('\Abraham\TwitterOAuth\TwitterOAuth');
        $this->remote = new TwitterOAuthRemoteService($this->api);
    }

    /**
     * @Given that there is a twitter user called :screenName with id :userId who has no tweets
     */
    public function thatThereIsATwitterUserCalledWithIdWhoHasNoTweets($screenName, $userId)
    {
        $table = new \Behat\Gherkin\Node\TableNode(array(
            array('created_at', 'id', 'text')
        ));
        $this->thatThereIsATwitterUserCalledWithIdWhoHasTheFollowingTweets($screenName, $userId, $table);
    }

    /**
     * @Given that the local repository contains the twitter users:
     */
    public function thatTheLocalRepositoryContainsTheTwitterUsers(\Behat\Gherkin\Node\TableNode $table)
    {
        foreach ($table->getHash() as $row) {
            $twitterUser = new TwitterUser($row['id']);
            $twitterUser->setScreenName($row['screenName']);
            $this->entityManager->persist($twitterUser);
        }
        $this->entityManager->flush();
    }

    /**
     * @Given that the local repository contains the following tweets:
     */
    public function thatTheLocalRepositoryContainsTheFollowingTweets(\Behat\Gherkin\Node\TableNode $table)
    {
        foreach ($table->getHash() as $row) {
            $tweet = new Tweet($row['id']);
            $tweet->setCreatedAt(new \DateTimeImmutable($row['created_at']));
            $tweet->setText($row['text']);
            $tweet->setUser($this->tweetPersistenceService
                ->getTwitterUserRepository()
                ->find($row['user']));
            $this->entityManager->persist($tweet);
        }
        $this->entityManager->flush();
    }

    /**
     * @Given that there is a twitter user called :screenName with id :userId who has the following tweets:
     * @param $screenName
     * @param $userId
     * @param \Behat\Gherkin\Node\TableNode $table
     */
    public function thatThereIsATwitterUserCalledWithIdWhoHasTheFollowingTweets(
        $screenName,
        $userId,
        \Behat\Gherkin\Node\TableNode $table
    ) {
        $response = $this->buildResponse($userId, $table->getHash());
        $this->apiShouldRespondToScreenNameWithResponse($userId, $screenName, $response);
    }

    /**
     * @When I run the sync command for user :username
     */
    public function iRunTheSyncCommandForUser($username)
    {
        $application = new Application();
        $sync = new TweetSync(
            $this->remote,
            $this->tweetPersistenceService,
            new TweetFactory($this->tweetPersistenceService->getTwitterUserRepository()),
            new TwitterUserFactory()
        );
        $application->add(new SyncUserCommand($sync));

        $command = $application->find('tweetsync:user');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array(
                'username' => $username,
            )
        );
    }

    /**
     * @Then /^I have the following users in the repository:$/
     */
    public function iHaveTheFollowingUsersInTheRepository(\Behat\Gherkin\Node\TableNode $table)
    {
        $twitterUserRepo = $this->tweetPersistenceService->getTwitterUserRepository();
        $expected = $table->getHash();
        foreach($expected as $row) {
            $twitterUser = $twitterUserRepo->find($row['id']);
            a::assertNotNull($twitterUser);
            a::assertEquals($row['screenName'], $twitterUser->getScreenName());
        }
    }

    /**
     * @Given /^I have the following tweets in the repository:$/
     */
    public function iHaveTheFollowingTweetsInTheRepository(\Behat\Gherkin\Node\TableNode $table)
    {
        $tweetRepository = $this->tweetPersistenceService->getTweetRepository();
        $expected = $table->getHash();
        foreach ($expected as $row) {
            $tweet = $tweetRepository->find($row['id']);
            a::assertNotNull($tweet, "No tweet with id " . $row['id']);
            a::assertEquals(
                $row['created_at'],
                $tweet->getCreatedAt()->format('D M d G:i:s O Y'),
                "Tweet "
                . $row['id']
                . " has the wrong date "
                . "\nExpected:"
                . $row['created_at']
                . "\nActual:  "
                . $tweet->getCreatedAt()->format('D M d G:i:s O Y')
            );
            a::assertEquals($row['text'], $tweet->getText(), "Tweet " . $row['id'] . " has the wrong text");
            a::assertEquals($row['user'], $tweet->getUser()->getId(), "Tweet " . $row['id'] . " has the wrong user");
        }
    }

    /**
     * @param $userId
     * @param $tweetsHash
     * @return string
     */
    private function buildResponse($userId, $tweetsHash)
    {
        $objects = array();
        foreach($tweetsHash as $row) {
            $tweetObj = new stdClass();
            $tweetObj->created_at = $row['created_at'];
            $tweetObj->id_str = $row['id'];
            $tweetObj->text = $row['text'];
            $tweetObj->user = new \stdClass();
            $tweetObj->user->id_str = $userId;
            $objects[] = $tweetObj;
        }
        return $objects;
    }

    /**
     * @param $userId
     * @param $screenName
     * @return stdClass
     */
    private function buildUserObj($userId, $screenName)
    {
        $userObj = new stdClass();
        $userObj->id_str = $userId;
        $userObj->screen_name = $screenName;
        return $userObj;
    }

    /**
     * @param $screenName
     * @param $response
     */
    private function apiShouldRespondToScreenNameWithResponse($userId, $screenName, $response)
    {
        $resource = 'users/show';
        $args = array(
            'screen_name' => 'foo'
        );
        $this->api
            ->shouldReceive('get')
            ->with($resource, $args)
            ->andReturn($this->buildUserObj($userId, $screenName));

        $resource = 'statuses/user_timeline';
        $args = array(
            'screen_name' => $screenName,
            'exclude_replies' => 1,
            'include_rts' => 0
        );
        $this->api
            ->shouldReceive('get')
            ->with($resource, $args)
            ->andReturn($response);
    }

    /**
     * @When I find by the username :username I get back the following tweets:
     */
    public function iFindByTheUsernameIGetBackTheFollowingTweets($username, \Behat\Gherkin\Node\TableNode $table)
    {

        $tweets = $this->tweetPersistenceService->getTweetRepository()->findByUsername($username);
        $expected = $table->getHash();

        a::assertEquals(count($expected), count($tweets));

        $assocTweets = array();
        foreach ($tweets as $tweet) {
            $assocTweets[$tweet->getId()] = $tweet;
        }

        foreach ($expected as $row) {
            $tweet = $assocTweets[$row['id']];
            a::assertNotNull($tweet, "No tweet with id " . $row['id']);
            a::assertEquals(
                $row['created_at'],
                $tweet->getCreatedAt()->format('D M d G:i:s O Y'),
                "Tweet "
                . $row['id']
                . " has the wrong date "
                . "\nExpected:"
                . $row['created_at']
                . "\nActual:  "
                . $tweet->getCreatedAt()->format('D M d G:i:s O Y')
            );
            a::assertEquals($row['text'], $tweet->getText(), "Tweet " . $row['id'] . " has the wrong text");
            a::assertEquals($row['user'], $tweet->getUser()->getId(), "Tweet " . $row['id'] . " has the wrong user");
        }
    }
}