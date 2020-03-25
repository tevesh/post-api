Feature: Manage blog post
  @createSchema
  Scenario: Create a blog post
    When I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/blog_post" with body:
    """
    {
      "title": "Feature post title",
      "content": "Feature post content"
    }
    """
    Then the response status code should be 201
    And the response should be in JSON