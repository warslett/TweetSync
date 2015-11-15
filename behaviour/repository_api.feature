Feature: Provide users with access to tweet API.

  Background:
    Given that I have an empty tweet repository

  Scenario: Can find a tweet by username
  Given that the local repository contains the twitter users:
    | id      | screenName |
    | 5402613 | foo        |
  And that the local repository contains the following tweets:
    | created_at                     | id                 | text                     | user    |
    | Thu Sep 24 14:06:39 +0000 2015 | 657921145627394048 | What a lovely day it is  | 5402613 |
  When I find by the username "foo" I get back the following tweets:
    | created_at                     | id                 | text                     | user    |
    | Thu Sep 24 14:06:39 +0000 2015 | 657921145627394048 | What a lovely day it is  | 5402613 |