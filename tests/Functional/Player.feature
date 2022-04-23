Feature:
  In order to prove that the Players API is working correctly
  As an API client
  I want to test all the scenarios in players API

  Scenario: It creates a new player with no club
    When I send a PUT request to "/api/player" with body:
    """
    {
        "name": "Aplayer",
        "salary": 5
    }
    """
    Then the response status code should be 201
    When I send a GET request to "/api/player/Aplayer"
    Then the response status code should be 200
    Then the JSON node "club" should be null
    Then the JSON nodes should contain:
      | name   | Aplayer |
      | salary | 5       |

  Scenario: It creates a new player in a club
    When I send a PUT request to "/api/club" with body:
    """
    {
        "name": "Aclub",
        "budget": 10
    }
    """
    When I send a PUT request to "/api/player" with body:
    """
    {
        "name": "Aplayer",
        "club": "Aclub",
        "salary": 5
    }
    """
    Then the response status code should be 201
    When I send a GET request to "/api/player/Aplayer"
    Then the response status code should be 200
    Then the JSON nodes should contain:
      | name   | Aplayer |
      | salary | 5       |
      | club   | Aclub   |
    When I send a GET request to "/api/club/Aclub"
    Then the JSON nodes should contain:
      | name   | Aclub |
      | budget | 5     |

  Scenario: It removes a player with no club
    When I send a PUT request to "/api/player" with body:
    """
    {
        "name": "Aplayer",
        "salary": 10
    }
    """
    Then I send a DELETE request to "/api/player/Aplayer"
    And the response status code should be 204
    When I send a GET request to "/api/player/Aplayer"
    Then the response status code should be 404

  Scenario: It removes a player in a club:
    When I send a PUT request to "/api/club" with body:
    """
    {
        "name": "Aclub",
        "budget": 10
    }
    """
    And I send a PUT request to "/api/player" with body:
    """
    {
        "name": "Aplayer",
        "club": "Aclub",
        "salary": 2
    }
    """
    When I send a GET request to "/api/club/Aclub"
    Then the JSON nodes should contain:
      | name   | Aclub |
      | budget | 8     |
    When I send a DELETE request to "/api/player/Aplayer"
    Then the response status code should be 204
    When I send a GET request to "/api/club/Aclub"
    Then the JSON nodes should contain:
      | name   | Aclub |
      | budget | 10    |

  Scenario: It returns error when try to create a new player in a non-existing club
    When I send a PUT request to "/api/player" with body:
    """
    {
        "name": "Aplayer",
        "club": "Aclub",
        "salary": 1
    }
    """
    Then the response status code should be 404
    When I send a GET request to "/api/player/Aplayer"
    Then the response status code should be 404

  Scenario: It returns error when try to create a new player with an empty name
    When I send a PUT request to "/api/player" with body:
    """
    {
        "name": ""
    }
    """
    Then the response status code should be 400

  Scenario: It returns error when try to create a new player with a salary less than zero
    When I send a PUT request to "/api/player" with body:
    """
    {
        "name": "Aplayer",
        "salary": -1
    }
    """
    Then the response status code should be 400
    When I send a GET request to "/api/player/Aplayer"
    Then the response status code should be 404

  Scenario: It returns error when try to create a new player with in a club with insufficient budget
    When I send a PUT request to "/api/club" with body:
    """
    {
        "name": "Aclub",
        "budget": 2
    }
    """
    When I send a PUT request to "/api/player" with body:
    """
    {
        "name": "Aplayer",
        "club": "Aclub",
        "salary": 3
    }
    """
    Then the response status code should be 400
    When I send a GET request to "/api/player/Aplayer"
    Then the response status code should be 404

  Scenario: It removes assigned Club
    When I send a PUT request to "/api/club" with body:
    """
    {
        "name": "Aclub",
        "budget": 10
    }
    """
    And I send a PUT request to "/api/player" with body:
    """
    {
        "name": "Aplayer",
        "club": "Aclub",
        "salary": 1
    }
    """
    When I send a DELETE request to "/api/player/Aplayer/club"
    Then the response status code should be 204
    And I send a GET request to "/api/player/Aplayer"
    Then the JSON node club should be null
    When I send a GET request to "/api/club/Aclub"
    Then the JSON node budget should be equal to 10

  Scenario: It paginates players by club field
    When I send a PUT request to "/api/club" with body:
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
    And I send a PUT request to "/api/player" with body:
    """
    {
        "name": "Aplayer",
        "club": "Aclub",
        "salary": 1
    }
    """
    And I send a PUT request to "/api/player" with body:
    """
    {
        "name": "AnotherPlayer",
        "club": "AnotherClub",
        "salary": 1
    }
    """
    When I send a GET request to "/api/player?club=Aclub"
    Then the JSON node players should have 1 element
    Then the JSON nodes should contain:
      | players[0].name   | Aplayer |
      | players[0].club   | Aclub   |
      | players[0].salary | 1       |

  Scenario: It paginates players by limit and page
    And I send a PUT request to "/api/player" with body:
    """
    {
        "name": "Aplayer",
        "club": null,
        "salary": 1
    }
    """
    And I send a PUT request to "/api/player" with body:
    """
    {
        "name": "AnotherPlayer",
        "club": null,
        "salary": 1
    }
    """
    When I send a GET request to "/api/player?limit=1&page=0"
    Then the JSON node players should have 1 element
    And the JSON nodes should contain:
      | players[0].name   | Aplayer |
      | players[0].salary | 1       |
    When I send a GET request to "/api/player?limit=1&page=1"
    Then the JSON node players should have 1 element
    And the JSON nodes should contain:
      | players[0].name   | AnotherPlayer |
      | players[0].salary | 1             |

  Scenario: It paginates players by order
    When I send a PUT request to "/api/player" with body:
    """
    {
        "name": "Aplayer",
        "club": null,
        "salary": 2
    }
    """
    And I send a PUT request to "/api/player" with body:
    """
    {
        "name": "AnotherPlayer",
        "club": null,
        "salary": 1
    }
    """
    When I send a GET request to "/api/player?sortBy=salary&orderBy=desc"
    Then the JSON node players should have 2 element
    And the JSON nodes should contain:
      | players[0].name   | Aplayer |
      | players[0].salary | 2       |
    When I send a GET request to "/api/player?sortBy=salary&orderBy=asc"
    Then the JSON node players should have 2 element
    And the JSON nodes should contain:
      | players[0].name   | AnotherPlayer |
      | players[0].salary | 1             |

  Scenario: It assigns a new club to a player with club
    Given I send a PUT request to "/api/player" with body:
    """
    {
        "name": "Aplayer",
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
    When I send a PUT request to "/api/player/Aplayer/club/Aclub"
    Then the response status code should be 204
    Given I send a GET request to "/api/player/Aplayer"
    And the JSON nodes should contain:
      | name   | Aplayer |
      | club   | Aclub   |
      | salary | 1       |

  Scenario: It assigns a club to a player with no club
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
    Given I send a PUT request to "/api/player" with body:
    """
    {
        "name": "Aplayer",
        "club": "Aclub",
        "salary": 1
    }
    """
    When I send a PUT request to "/api/player/Aplayer/club/AnotherClub"
    Then the response status code should be 204
    Given I send a GET request to "/api/player/Aplayer"
    And the JSON nodes should contain:
      | name   | Aplayer     |
      | club   | AnotherClub |
      | salary | 1           |