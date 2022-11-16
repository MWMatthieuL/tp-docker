Feature: Access to Homepage

  @web
  Scenario: An anonymous use should see the homepage
    Given I am on "/"
    Then I should see "Hello world from Symfony Skeleton!"
