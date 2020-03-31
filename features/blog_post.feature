Feature: Manage blog post
  @createSchema
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