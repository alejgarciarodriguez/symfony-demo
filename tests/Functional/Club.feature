Feature:
  In order to prove that the Clubs API is working correctly
  As an API client
  I want to test all the scenarios in Clubs API

  Scenario: It creates a club
    When I send a PUT request to "/api/club" with body:
    """
    {
        "name": "Aclub",
        "budget": 100
    }
    """
    Then the response status code should be 201
    When I send a GET request to "/api/club/Aclub"
    Then the response status code should be 200
    And the JSON should be equal to:
    """
    {
        "name": "Aclub",
        "budget": 100,
        "players": [],
        "referees": []
    }
    """

  Scenario: It returns an error when try to create a new club with budget lower than zero
    When I send a PUT request to "/api/club" with body:
    """
    {
        "name": "Aclub",
        "budget": -1
    }
    """
    Then the response status code should be 400
    When I send a GET request to "/api/club/Aclub"
    Then the response status code should be 404

  Scenario: Club get their budget reduced when a player is added
    When I send a PUT request to "/api/club" with body:
    """
    {
        "name": "Aclub",
        "budget": 100
    }
    """
    And I send a PUT request to "/api/player" with body:
    """
    {
        "name": "Aplayer",
        "salary": 10,
        "club": "Aclub"
    }
    """
    When I send a GET request to "/api/club/Aclub"
    Then the JSON nodes should contain:
      | name              | Aclub   |
      | budget            | 90      |
      | players[0].name   | Aplayer |
      | players[0].salary | 10      |
      | players[0].club   | Aclub   |

  Scenario: Club get their budget reduced when a referee is added
    When I send a PUT request to "/api/club" with body:
    """
    {
        "name": "Aclub",
        "budget": 100
    }
    """
    And I send a PUT request to "/api/referee" with body:
    """
    {
        "name": "Areferee",
        "salary": 10,
        "club": "Aclub"
    }
    """
    When I send a GET request to "/api/club/Aclub"
    Then the JSON nodes should contain:
      | name               | Aclub    |
      | budget             | 90       |
      | referees[0].name   | Areferee |
      | referees[0].salary | 10       |
      | referees[0].club   | Aclub    |

  Scenario: Club get their budget increased when a player is removed
    When I send a PUT request to "/api/club" with body:
    """
    {
        "name": "Aclub",
        "budget": 100
    }
    """
    And I send a PUT request to "/api/player" with body:
    """
    {
        "name": "Aplayer",
        "salary": 10,
        "club": "Aclub"
    }
    """
    When I send a GET request to "/api/club/Aclub"
    Then the JSON nodes should contain:
      | name              | Aclub   |
      | budget            | 90      |
      | players[0].name   | Aplayer |
      | players[0].salary | 10      |
      | players[0].club   | Aclub   |
    When I send a DELETE request to "/api/player/Aplayer"
    And I send a GET request to "/api/club/Aclub"
    Then the JSON should be equal to:
    """
    {
        "name": "Aclub",
        "budget": 100,
        "players": [],
        "referees": []
    }
    """

  Scenario: Club get their budget increased when a referee is removed
    When I send a PUT request to "/api/club" with body:
    """
    {
        "name": "Aclub",
        "budget": 100
    }
    """
    And I send a PUT request to "/api/referee" with body:
    """
    {
        "name": "Areferee",
        "salary": 10,
        "club": "Aclub"
    }
    """
    When I send a GET request to "/api/club/Aclub"
    Then the JSON nodes should contain:
      | name               | Aclub    |
      | budget             | 90       |
      | referees[0].name   | Areferee |
      | referees[0].salary | 10       |
      | referees[0].club   | Aclub    |
    When I send a DELETE request to "/api/referee/Areferee"
    And I send a GET request to "/api/club/Aclub"
    Then the JSON should be equal to:
    """
    {
        "name": "Aclub",
        "budget": 100,
        "players": [],
        "referees": []
    }
    """

  Scenario: It returns a Club with players and referees:
    When I send a PUT request to "/api/club" with body:
    """
    {
        "name": "Aclub",
        "budget": 100
    }
    """
    Then the response status code should be 201
    When I send a PUT request to "/api/referee" with body:
    """
    {
        "name": "Areferee",
        "club": "Aclub",
        "salary": 1
    }
    """
    And I send a PUT request to "/api/player" with body:
    """
    {
        "name": "APlayer",
        "club": "Aclub",
        "salary": 1
    }
    """
    And I send a GET request to "/api/club/Aclub"
    Then the response status code should be 200
    Then the JSON nodes should contain:
      | name               | Aclub    |
      | budget             | 98       |
      | players[0].name    | Aplayer  |
      | players[0].salary  | 1        |
      | players[0].club    | Aclub    |
      | referees[0].name   | Areferee |
      | referees[0].salary | 1        |
      | referees[0].club   | Aclub    |

  Scenario: It updates club budget
    When I send a PUT request to "/api/club" with body:
    """
    {
        "name": "Aclub",
        "budget": 100
    }
    """
    And I send a PATCH request to "/api/club/Aclub" with body:
    """
    {
        "budget": 150
    }
    """
    Then the response status code should be 204
    And I send a GET request to "/api/club/Aclub"
    Then the JSON nodes should contain:
      | name   | Aclub |
      | budget | 150   |

  Scenario: It returns error if budget cannot be updated
    When I send a PUT request to "/api/club" with body:
    """
    {
        "name": "Aclub",
        "budget": 100
    }
    """
    And I send a PUT request to "/api/player" with body:
        """
    {
        "name": "APlayer",
        "club": "Aclub",
        "salary": 90
    }
    """
    When I send a PATCH request to "/api/club/Aclub" with body:
    """
    {
        "budget": 80
    }
    """
    Then the response status code should be 400
