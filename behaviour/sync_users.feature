Feature: Sync A Users Tweets

  Background:
    Given that I have an empty tweet repository
    And that I have a dummy Twitter API

  Scenario: syncs a new user
    Given that there is a twitter user called "foo" with id "5402612" who has no tweets
    When I run the sync command for user "foo"
    Then I have the following users in the repository:
      | id      | screenName |
      | 5402612 | foo        |

  Scenario: syncs an existing user
    Given that there is a twitter user called "foo" with id "5402612" who has no tweets
    And that the local repository contains the twitter users:
      | id      | screenName |
      | 5402612 | foo        |
    When I run the sync command for user "foo"
    Then I have the following users in the repository:
      | id      | screenName |
      | 5402612 | foo        |

  Scenario: syncing the tweets of a new user who has one new tweet
    Given that there is a twitter user called "foo" with id "5402612" who has the following tweets:
      | created_at                     | id                 | text                    |
      | Sat Oct 24 14:06:39 +0000 2015 | 657921145627394048 | What a lovely day it is |
    When I run the sync command for user "foo"
    Then I have the following tweets in the repository:
      | created_at                     | id                 | text                    | user    |
      | Sat Oct 24 14:06:39 +0000 2015 | 657921145627394048 | What a lovely day it is | 5402612 |

  Scenario: syncing the tweets of a new user who has multiple new tweets
    Given that there is a twitter user called "foo" with id "5402613" who has the following tweets:
      | created_at                     | id                 | text                     |
      | Thu Sep 24 14:06:39 +0000 2015 | 657921145627394048 | What a lovely day it is  |
      | Fri Oct 16 14:06:39 +0000 2015 | 657921145627394049 | What a rubbish day it is |
    When I run the sync command for user "foo"
    Then I have the following tweets in the repository:
      | created_at                     | id                 | text                     | user    |
      | Thu Sep 24 14:06:39 +0000 2015 | 657921145627394048 | What a lovely day it is  | 5402613 |
      | Fri Oct 16 14:06:39 +0000 2015 | 657921145627394049 | What a rubbish day it is | 5402613 |

  Scenario: syncing the tweets of an existing user who has one new tweet and one existing tweet
    Given that there is a twitter user called "foo" with id "5402613" who has the following tweets:
      | created_at                     | id                 | text                     |
      | Thu Sep 24 14:06:39 +0000 2015 | 657921145627394048 | What a lovely day it is  |
      | Fri Oct 16 14:06:39 +0000 2015 | 657921145627394049 | What a rubbish day it is |
    And that the local repository contains the twitter users:
      | id      | screenName |
      | 5402613 | foo        |
    And that the local repository contains the following tweets:
      | created_at                     | id                 | text                     | user    |
      | Thu Sep 24 14:06:39 +0000 2015 | 657921145627394048 | What a lovely day it is  | 5402613 |
    When I run the sync command for user "foo"
    Then I have the following tweets in the repository:
      | created_at                     | id                 | text                     | user    |
      | Thu Sep 24 14:06:39 +0000 2015 | 657921145627394048 | What a lovely day it is  | 5402613 |
      | Fri Oct 16 14:06:39 +0000 2015 | 657921145627394049 | What a rubbish day it is | 5402613 |

  Scenario: syncing the tweets of an existing user who has one tweet that has changed
    Given that there is a twitter user called "foo" with id "5402613" who has the following tweets:
      | created_at                     | id                 | text                        |
      | Thu Sep 24 14:06:39 +0000 2015 | 657921145627394048 | What a fantastic day it is  |
    And that the local repository contains the twitter users:
      | id      | screenName |
      | 5402613 | foo        |
    And that the local repository contains the following tweets:
      | created_at                     | id                 | text                     | user    |
      | Thu Sep 24 14:06:39 +0000 2015 | 657921145627394048 | What a lovely day it is  | 5402613 |
    When I run the sync command for user "foo"
    Then I have the following tweets in the repository:
      | created_at                     | id                 | text                        | user    |
      | Thu Sep 24 14:06:39 +0000 2015 | 657921145627394048 | What a fantastic day it is  | 5402613 |