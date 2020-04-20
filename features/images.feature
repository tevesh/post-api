Feature: Manage images

  @image
  Scenario: Create and upload new image
    Given I am authenticated as "admin"
    When I add "Content-Type" header equal to "multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/images" with parameters:
    | key  | value |
    | file | @spongebob.jpg |
    Then the response status code should be 201
    And the response should be in JSON
    And the JSON matched expected template:
    """
    {
      "@context": "/api/contexts/Image",
      "@id": "/api/images/@integer@",
      "@type": "Image",
      "url": "/uploads/images/@string@",
      "id": @integer@,
      "createdAt": "@string@.isDateTime()",
      "updatedAt": "@string@.isDateTime()",
      "timezone": "UTC"
    }
    """

  @image
  Scenario: Update a post with an image
    Given I am authenticated as "admin"
    When I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "PUT" request to "/api/blog_posts/1" with body:
    """
    {
      "images": ["api/images/1"]
    }
    """
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON matched expected template:
    """
    {
      "@context": "/api/contexts/BlogPost",
      "@id": "/api/blog_posts/@integer@",
      "@type": "BlogPost",
      "id": @integer@,
      "title": "@string@",
      "published": "@string@.isDateTime()",
      "author": {
          "@id": "/api/users/@integer@",
          "@type": "@string@",
          "username": "@string@",
          "name": "@string@"
      },
      "content": "@string@",
      "slug": "@string@",
      "comments": [],
      "images": [
          {
              "@id": "/api/images/1",
              "@type": "Image",
              "url": "/uploads/images/@string@"
          }
      ]
    }
    """

  @image
  Scenario: Throw an error when no image are provided
    Given I am authenticated as "admin"
    When I add "Content-Type" header equal to "multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/images" with parameters:
      | key  | value     |
    Then the response status code should be 400
    And the response should be in JSON

  @image
  Scenario: Throw an error when user in not authenticated
    When I add "Content-Type" header equal to "multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/images" with parameters:
    | key  | value     |
    | file | @spongebob.jpg|
    Then the response status code should be 401
    And the response should be in JSON