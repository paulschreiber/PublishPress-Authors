Feature: Post edit author in the admin
    In order to edit the post author
    As an admin
    I need to be able to select one or more authors for a post

    Background:
        Given the user "pea_admin_user" exists with role "administrator"
        And I am logged in as "pea_admin_user"
        And I activated Authors for the "post" post type

    Scenario: Core post author field is not visible in the post edit page if the post type is activated
        When I open the Add New Post page
        Then I don't see the core author field

    Scenario: Core post author is visible in the post edit page if the post type is not activated
        Given I deactivated Authors for the "post" post type
        When I open the Add New Post page
        Then I see the core author field

    Scenario: Block editor works when creating a new post
        When I open the Add New Post page
        Then I don't see the post locked modal
        And I see the post visual editor
        And I can add blocks in the editor

    Scenario: Block editor works when editing a post of guest author
        Given guest author exists with name "PEA Guest Author 1" and slug "pea_guest_author_1"
        And a post named "pea_post_guest_author_1" exists for "pea_guest_author_1"
        When I edit the post name "pea_post_guest_author_1"
        Then I don't see the post locked modal
        And I see the post visual editor
        And I can add blocks in the editor

    Scenario: Block editor works when editing a post of author mapped to user
        Given the user "pea_user_1" exists with role "administrator"
        And author exists for user "pea_user_1"
        And a post named "pea_post_guest_author_2" exists for "pea_user_1"
        When I edit the post name "pea_post_guest_author_2"
        Then I don't see the post locked modal
        And I see the post visual editor
        And I can add blocks in the editor
