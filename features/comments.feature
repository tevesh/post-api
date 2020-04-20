Feature: Manage comments

  @comment
  Scenario: Create a comment for a blog post
    Given I am authenticated as admin
    When I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/comments" with body:
    """
    {
      "blogPost": "/api/blog_posts/101",
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

  @comment
  Scenario: Get all comment for a blog post and make sure that last created is present
    Given I am authenticated as admin
    When I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/blog_posts/101/comments"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON matched expected template:
    """
    {
      "@context": "/api/contexts/Comment",
      "@id": "/api/blog_posts/101/comments",
      "@type": "hydra:Collection",
      "hydra:member": [
          {
              "@id": "/api/comments/@integer@",
              "@type": "Comment",
              "id": @integer@,
              "content": "Feature comment content",
              "published":"@string@.isDateTime()",
              "author": {
                  "@id": "/api/users/1",
                  "@type": "User",
                  "username": "admin",
                  "name": "admin",
                  "email": "admin@post-api.dev.it",
                  "roles": [
                      "ROLE_SUPERADMIN"
                  ]
              }
          }
      ],
      "hydra:totalItems": 1
    }
    """

  @comment
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

  @comment
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

  @comment
  Scenario: Throw an error when create a comment for non existent blog post
    Given I am authenticated as admin
    When I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/comments" with body:
    """
    {
      "blogPost": "/api/blog_posts/105"
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
      "hydra:description": "Item not found for \"/api/blog_posts/105\".",
      "violations": [
          {
              "propertyPath": "",
              "message": "Item not found for \"/api/blog_posts/105\"."
          }
      ]
    }
    """

