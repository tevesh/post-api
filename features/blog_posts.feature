Feature: Manage blog posts

  @createSchema @blog_post @comment
  Scenario: Create a blog post
    Given I am authenticated as admin
    When I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/blog_posts" with body:
    """
    {
      "title": "Feature post title",
      "content": "Feature post content"
    }
    """
    Then the response status code should be 201
    And the response should be in JSON
    And the JSON matched expected template:
    """
    {
       "@context":"/api/contexts/BlogPost",
       "@id":"@string@",
       "@type":"BlogPost",
       "id":@integer@,
       "title":"Feature post title",
       "published":"@string@.isDateTime()",
       "author":{
          "@id":"/api/users/@integer@",
          "@type":"User",
          "username":"admin",
          "name":"admin",
          "email":"admin@post-api.dev.it",
          "roles":[
             "ROLE_SUPERADMIN"
          ]
       },
       "content":"Feature post content",
       "slug":"feature-post-title",
       "comments":[],
       "images":[]
    }
    """

  @createSchema @blog_post
  Scenario: Throw an error when blog post is not valid
    Given I am authenticated as admin
    When I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/blog_posts" with body:
    """
    {
      "title": "",
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
      "hydra:description": "title: This value should not be blank.\ntitle: This value is too short. It should have 10 characters or more.\ncontent: This value should not be blank.\ncontent: This value is too short. It should have 20 characters or more.",
      "violations": [
          {
              "propertyPath": "title",
              "message": "This value should not be blank."
          },
          {
              "propertyPath": "title",
              "message": "This value is too short. It should have 10 characters or more."
          },
          {
              "propertyPath": "content",
              "message": "This value should not be blank."
          },
          {
              "propertyPath": "content",
              "message": "This value is too short. It should have 20 characters or more."
          }
      ]
    }
    """

  @createSchema @blog_post
  Scenario: Throw an error when user in not authenticated
    When I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/blog_posts" with body:
    """
    {
      "title": "",
      "content": ""
    }
    """
    Then the response status code should be 401