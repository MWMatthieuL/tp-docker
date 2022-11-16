Feature: Access to Administration

  @web
  Scenario: An admin user can access to administration
    Given I am on "/admin"
    Then I should be on "/login"
    And I fill in the following:
      | _username | admin@mentalworks.fr |
      | _password | xxx                  |
    When I press "Connexion"
    Then I should be on "/admin"
    And I should see "Administrateur"
