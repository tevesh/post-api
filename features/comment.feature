Feature: Manage blog post
  @createSchema @comment
  Scenario: Create a blog post
    Given I am authenticated as admin
    When I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/comments" with body:
    """
    {
      "blogPost": "/api/blog_posts/1",
      "content": "Feature comment content"
    }
    """
    Then the response status code should be 201
    And the response should be in JSON
    And the JSON matched expected template:
    """
    {
      "@context": "/api/contexts/Comment",
      "@id": "/api/comments/@integer@",
      "@type": "Comment",
      "content": "Feature comment content",
      "blogPost": {
          "@id": "/api/blog_posts/@integer@",
          "@type": "BlogPost",
          "title": "@string@",
          "content": "@string@",
          "slug": "@string@",
          "images": @array@
      }
    }
    """

  @createSchema @comment
  Scenario: Throw an error when comments is not valid
    Given I am authenticated as admin
    When I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/comments" with body:
    """
    {
      "blogPost": "/api/blog_posts/1",
      "content": ""
    }
    """
    Then the response status code should be 400
    And the response should be in JSON
    And the JSON matched expected template:
    """
    {
      "@context": "/api/contexts/ConstraintViolationList",
      "@type": "ConstraintViolationList",
      "hydra:title": "An error occurred",
      "hydra:description": "content: This value should not be blank.\ncontent: This value is too short. It should have 5 characters or more.",
      "violations": [
          {
              "propertyPath": "content",
              "message": "This value should not be blank."
          },
          {
              "propertyPath": "content",
              "message": "This value is too short. It should have 5 characters or more."
          }
      ]
    }
    """

  @createSchema @comment
  Scenario: Throw an error when user in not authenticated
    When I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/comments" with body:
    """
    {
      "blogPost": "/api/blog_posts/1",
      "content": "Feature comment content"
    }
    """
    Then the response status code should be 401