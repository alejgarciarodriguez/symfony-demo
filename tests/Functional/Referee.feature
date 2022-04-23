Feature:
  In order to prove that the Referees API is working correctly
  As an API client
  I want to test all the scenarios in Referees API

  Scenario: It creates a new referee with no club
    When I send a PUT request to "/api/referee" with body:
    """
    {
        "name": "Areferee",
        "salary": 5
    }
    """
    Then the response status code should be 201
    When I send a GET request to "/api/referee/Areferee"
    Then the response status code should be 200
    Then the JSON node "club" should be null
    Then the JSON nodes should contain:
      | name   | Areferee |
      | salary | 5        |

  Scenario: It creates a new referee in a club
    When I send a PUT request to "/api/club" with body:
    """
    {
        "name": "Aclub",
        "budget": 10
    }
    """
    When I send a PUT request to "/api/referee" with body:
    """
    {
        "name": "Areferee",
        "club": "Aclub",
        "salary": 5
    }
    """
    Then the response status code should be 201
    When I send a GET request to "/api/referee/Areferee"
    Then the response status code should be 200
    Then the JSON nodes should contain:
      | name   | Areferee |
      | salary | 5        |
      | club   | Aclub    |
    When I send a GET request to "/api/club/Aclub"
    Then the JSON nodes should contain:
      | name   | Aclub |
      | budget | 5     |

  Scenario: It removes a referee with no club
    When I send a PUT request to "/api/referee" with body:
    """
    {
        "name": "Areferee",
        "salary": 10
    }
    """
    Then I send a DELETE request to "/api/referee/Areferee"
    And the response status code should be 204
    When I send a GET request to "/api/referee/Areferee"
    Then the response status code should be 404

  Scenario: It removes a referee in a club:
    When I send a PUT request to "/api/club" with body:
    """
    {
        "name": "Aclub",
        "budget": 10
    }
    """
    And I send a PUT request to "/api/referee" with body:
    """
    {
        "name": "Areferee",
        "club": "Aclub",
        "salary": 2
    }
    """
    When I send a GET request to "/api/club/Aclub"
    Then the JSON nodes should contain:
      | name   | Aclub |
      | budget | 8     |
    When I send a DELETE request to "/api/referee/Areferee"
    Then the response status code should be 204
    When I send a GET request to "/api/club/Aclub"
    Then the JSON nodes should contain:
      | name   | Aclub |
      | budget | 10    |

  Scenario: It returns error when try to create a new referee in a non-existing club
    When I send a PUT request to "/api/referee" with body:
    """
    {
        "name": "Areferee",
        "club": "Aclub",
        "salary": 1
    }
    """
    Then the response status code should be 404
    When I send a GET request to "/api/referee/Areferee"
    Then the response status code should be 404

  Scenario: It returns error when try to create a new referee with an empty name
    When I send a PUT request to "/api/referee" with body:
    """
    {
        "name": ""
    }
    """
    Then the response status code should be 400
    When I send a GET request to "/api/referee/"
    Then the response status code should be 404

  Scenario: It returns error when try to create a new referee with a salary less than zero
    When I send a PUT request to "/api/referee" with body:
    """
    {
        "name": "Areferee",
        "salary": -1
    }
    """
    Then the response status code should be 400
    When I send a GET request to "/api/referee/Areferee"
    Then the response status code should be 404

  Scenario: It returns error when try to create a new referee with in a club with insufficient budget
    When I send a PUT request to "/api/club" with body:
    """
    {
        "name": "Aclub",
        "budget": 2
    }
    """
    When I send a PUT request to "/api/referee" with body:
    """
    {
        "name": "Areferee",
        "club": "Aclub",
        "salary": 3
    }
    """
    Then the response status code should be 400
    When I send a GET request to "/api/referee/Areferee"
    Then the response status code should be 404

  Scenario: It removes assigned Club
    When I send a PUT request to "/api/club" with body:
    """
    {
        "name": "Aclub",
        "budget": 10
    }
    """
    And I send a PUT request to "/api/referee" with body:
    """
    {
        "name": "Areferee",
        "club": "Aclub",
        "salary": 1
    }
    """
    When I send a DELETE request to "/api/referee/Areferee/club"
    Then the response status code should be 204
    And I send a GET request to "/api/referee/Areferee"
    Then the JSON node club should be null
    When I send a GET request to "/api/club/Aclub"
    Then the JSON node budget should be equal to 10

  Scenario: It assigns a club to a referee with no club
    Given I send a PUT request to "/api/referee" with body:
    """
    {
        "name": "Areferee",
        "club": null,
        "salary": 1
    }
    """
    And I send a PUT request to "/api/club" with body:
    """
    {
        "name": "Aclub",
        "budget": 10
    }
    """
    When I send a PUT request to "/api/referee/Areferee/club/Aclub"
    Then the response status code should be 204
    Given I send a GET request to "/api/referee/Areferee"
    And the JSON nodes should contain:
      | name   | Areferee |
      | club   | Aclub   |
      | salary | 1       |

  Scenario: It assigns a new club to a referee with club
    And I send a PUT request to "/api/club" with body:
    """
    {
        "name": "Aclub",
        "budget": 10
    }
    """
    And I send a PUT request to "/api/club" with body:
    """
    {
        "name": "AnotherClub",
        "budget": 10
    }
    """
    Given I send a PUT request to "/api/referee" with body:
    """
    {
        "name": "Areferee",
        "club": "Aclub",
        "salary": 1
    }
    """
    When I send a PUT request to "/api/referee/Areferee/club/AnotherClub"
    Then the response status code should be 204
    Given I send a GET request to "/api/referee/Areferee"
    And the JSON nodes should contain:
      | name   | Areferee     |
      | club   | AnotherClub |
      | salary | 1           |
