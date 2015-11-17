TweetSync
=======
A tool for synchronising a twitter users recent tweets with a local database

Installation:
-----------
`composer require warslett/tweetsync`

Setup:
-----------

Create your own Console Runner wherever you want:

```php
<?php
# myConsole.php

// replace with the path to your own autoloader
require __DIR__.'/vendor/autoload.php';

use WArslett\TweetSync\ConsoleRunner;

ConsoleRunner::configureFromArray([
    'consumer_key' => 'YOUR CONSUMER KEY',
    'consumer_secret' => 'YOUR CONSUMER SECRET',
    'oauth_access_token' => 'YOUR OAUTH ACCESS TOKEN',
    'oauth_access_token_secret' => 'YOUR OAUTH ACCESS TOKEN SECRET',
    'database.config' => [
        // Replace with your own db params
        // See: http://doctrine-orm.readthedocs.org/projects/doctrine-dbal/en/latest/reference/configuration.html
        'driver'   => 'pdo_sqlite',
        'path'     => __DIR__ . "/db.sqlite"
    ]
])->run();
```

Then run init to initialise the tables in the db: `php myConsole.php tweetsync:init` (replace myConsole.php with the location of your console runner)

Usage:
-----------
To synchronise a user "BBCBreaking": `php myConsole.php tweetsync:user BBCBreaking` (replace myConsole.php with the location of your console runner)
Add to the crontab for a regular sync: http://crontab.org/

To get tweets that have been synchronised for use in your application:
```php
<?php
# myConsole.php

// replace with the path to your own autoloader
require __DIR__.'/vendor/autoload.php';

use WArslett\TweetSync\ORM\Doctrine\TweetPersistenceService;

$persistenceService = TweetPersistenceService::create([
    // Replace with your own db params
    // See: http://doctrine-orm.readthedocs.org/projects/doctrine-dbal/en/latest/reference/configuration.html
    'driver'   => 'pdo_sqlite',
    'path'     => __DIR__ . "/db.sqlite"
]);

$tweetRepository = $persistenceService->getTweetRepository();

$tweets = $tweetRepository->findByUsername('BBCBreaking');

foreach($tweets as $tweet) {
    print($tweet->getText());
}

```

Development Todo:
-----------
* Add more properties to Tweet and TwitterUser so will be more useful out the box
* Reimplement remote service with a nicer oauth library
* Put it on packagist