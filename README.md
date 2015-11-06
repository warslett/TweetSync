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
    'api.config' => [
        // You need your own access tokens for any application or website that interacts with twitter.
        // See: https://apps.twitter.com/
        'oauth_access_token' => 'YOUR OAUTH ACCESS TOKEN',
        'oauth_access_token_secret' => 'YOUR OAUTH ACCESS TOKEN SECRET',
        'consumer_key' => 'YOUR CONSUMER KEY',
        'consumer_secret' => 'YOUR CONSUMER SECRET'
    ],
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

Development Todo:
-----------
* Add more properties to Tweet and TwitterUser so will be more useful out the box
* Provide access to Repositories
* Reimplement remote service with a nicer oauth library