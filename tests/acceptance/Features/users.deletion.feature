Feature: Users deletion
  In order to delete users on different scenarios
  As an admin
  I need to be able to choose what to do with the author terms and content owned by the user before the user is deleted

  Background:
    Given the user "admin_user_deletion" exists with role "administrator"
    And I am logged in as "admin_user_deletion"

  ############################################################################
  # User has no content and is not author
  ############################################################################

  Scenario: See correct form when selected one user which has no content and is not an author
    Given the user "john" exists with role "author"
    And I am on the users admin page
    When I click on the Delete row action for the user "john"
    Then I see the text "action=delete" in the current URL
    And I see the user ID for "john" in the deletion list
    And I see the text "john"
    And I see the text "You have specified this user for deletion"
    And I see input button with value "Confirm Deletion"
    But I don't see the text "What should be done with content owned by this user"
    And I don't see the text "Attribute all content to"
    And I don't see element "#delete_option_author_wrapper"

  Scenario: See correct form when selected multiple users which have no content and are not authors
    Given the user "peter" exists with role "author"
    And the user "mary" exists with role "author"
    And I am on the users admin page
    And the user "peter" is selected
    And the user "mary" is selected
    When I select and apply the bulk action "delete"
    Then I see the text "action=delete" in the current URL
    And I see the user ID for "peter" in the deletion list
    And I see the user ID for "mary" in the deletion list
    And I see the text "peter"
    And I see the text "mary"
    And I see the text "You have specified these users for deletion"
    And I see input button with value "Confirm Deletion"
    But I don't see the text "What should be done with content owned by these users"
    And I don't see the text "Attribute all content to"
    And I don't see element "#delete_option_author_wrapper"

  Scenario: User is deleted when selected one user which has no content and is not an author
    Given the user "jack" exists with role "author"
    And I am on the users admin page
    When I click on the Delete row action for the user "jack"
    And I click on the submit button
    Then I see the text "User deleted"
    And I don't see the text "jack"

  Scenario: Users are deleted when selected multiple users which has no content and are not authors
    Given the user "lucy" exists with role "author"
    And the user "oprah" exists with role "author"
    And I am on the users admin page
    When I select the user "lucy"
    And I select the user "oprah"
    And I select and apply the bulk action "delete"
    And I click on the submit button
    Then I see the text "Users deleted"
    And I don't see the text "lucy"
    And I don't see the text "oprah"

  ############################################################################
  # User has no content but is author - Delete the author
  ############################################################################

  Scenario: See option to delete author or convert to guest author when selected user has no content but is an author
    Given the user "patrick" exists with role "author"
    And author exists for user "patrick"
    And I am on the users admin page
    When I select the user "patrick"
    And I click on the Delete row action for the user "patrick"
    Then I see the user ID for "patrick" in the deletion list
    And I see the text "patrick"
    And I see the text "What should be done with the author term?"
    And I see the text "Delete the author term"
    And I see the text "Convert to guest author"

  Scenario: See option to delete authors or convert to guest authors when selected users have no content but at least one is an author
    Given the user "erick" exists with role "author"
    And the user "jonatan" exists with role "author"
    And author exists for user "erick"
    And I am on the users admin page
    When I select the user "jonatan"
    And I select the user "erick"
    And I select and apply the bulk action "delete"
    Then I see the user ID for "jonatan" in the deletion list
    And I see the user ID for "erick" in the deletion list
    And I see the text "jonatan"
    And I see the text "erick"
    And I see the text "At least one of those users is an author. What should be done with the author terms"
    And I see the text "Delete the author term"
    And I see the text "Convert to guest author"

  Scenario: User is deleted when selected the option to delete author for user which has no content but is an author
    Given the user "simon" exists with role "author"
    And author exists for user "simon"
    And I am on the users admin page
    When I click on the Delete row action for the user "simon"
    And I select the option "delete_author_terms" on "#delete_author_terms"
    And I click on the submit button
    Then I see the text "User deleted"
    And I don't see the text "simon"

  Scenario: Author is deleted when selected the option to delete author for user which has no content but is an author
    Given the user "joshua" exists with role "author"
    And author exists for user "joshua"
    And I am on the users admin page
    When I click on the Delete row action for the user "joshua"
    And I select the option "delete_author_terms" on "#delete_author_terms"
    And I click on the submit button
    And I open the authors admin page
    Then I don't see the text "joshua"

  ############################################################################
  # User has no content but is author - Convert to guest author
  ############################################################################

  Scenario: User is deleted when selected the option to convert to guest author for user which has no content but is an author
    Given the user "luck" exists with role "author"
    And author exists for user "luck"
    And I am on the users admin page
    When I click on the Delete row action for the user "luck"
    And I select the option "convert_to_guest_author" on "#convert_to_guest_author"
    And I click on the submit button
    Then I see the text "User deleted"
    And I don't see the text "luck"

  Scenario: Author is not deleted when selected the option to convert to guest author for user which has no content but is an author
    Given the user "soraya" exists with role "author"
    And author exists for user "soraya"
    And I am on the users admin page
    When I click on the Delete row action for the user "soraya"
    And I select the option "convert_to_guest_author" on "#convert_to_guest_author"
    And I click on the submit button
    And I open the authors admin page
    Then I see the text "soraya"

  Scenario: Author is converted to guest author when selected the option to convert to guest author for user which has no content but is an author
    Given the user "paul" exists with role "author"
    And author exists for user "paul"
    And I am on the users admin page
    When I click on the Delete row action for the user "paul"
    And I select the option "convert_to_guest_author" on "#convert_to_guest_author"
    And I click on the submit button
    And I open the authors admin page
    Then I see the text "paul — Guest Author"

  ############################################################################
  # User has content
  ############################################################################
  Scenario: See correct form when selected one user which has content
    Given the user "sheila" exists with role "author"
    And author exists for user "sheila"
    And a post named "sheilas-post-1" exists for "sheila"
    And a post named "sheilas-post-2" exists for "sheila"
    And I am on the users admin page
    When I click on the Delete row action for the user "sheila"
    Then I see the text "action=delete" in the current URL
    And I see the user ID for "sheila" in the deletion list
    And I see the text "sheila"
    And I see the text "You have specified this user for deletion"
    And I see the text "What should be done with content owned by this user"
    And I see element "#delete_option_author_wrapper"
    And I see the text "Delete all content which the user is first author"
    And I see the text "Delete all content"
    And I see the text "Attribute all content to"
    And I see the text "Keep the content but convert the user into guest author"
    And I see input button with value "Confirm Deletion"
    But I don't see the text "Delete all content."

  ############################################################################
  # User has content - Delete all content which the user is first author
  ############################################################################
  Scenario: User is deleted when selected the option to delete all content for first authors for user which has content
    Given the user "penelope" exists with role "author"
    And author exists for user "penelope"
    And a post named "penelope-post-1" exists for "penelope"
    And a post named "penelope-post-2" exists for "penelope"
    And I am on the users admin page
    When I click on the Delete row action for the user "penelope"
    And I select the option "delete_content_first_author" on "#delete_content_first_author"
    And I click on the submit button
    Then I see the text "User deleted"
    And I don't see the text "penelope"

  Scenario: Author is deleted when selected the option to delete all content for first authors for user which has content
    Given the user "bart" exists with role "author"
    And author exists for user "bart"
    And a post named "bart-post-1" exists for "bart"
    And a post named "bart-post-2" exists for "bart"
    And I am on the users admin page
    When I click on the Delete row action for the user "bart"
    And I select the option "delete_content_first_author" on "#delete_content_first_author"
    And I click on the submit button
    And I open the authors admin page
    Then I don't see the text "bart"

  Scenario: Posts which user is first author are deleted when selected the option to delete all content for first authors for user which has content
    Given the user "jones" exists with role "author"
    And the user "smiley" exists with role "author"
    And author exists for user "jones"
    And author exists for user "smiley"
    And a post named "jones-post-1" exists for "jones"
    And a post named "jones-post-2" exists for "jones"
    And a post named "smiley-post-1" exists for "smiley" and "jones"
    And I am on the users admin page
    When I click on the Delete row action for the user "jones"
    And I select the option "delete_content_first_author" on "#delete_content_first_author"
    And I click on the submit button
    And I open the posts admin page
    Then I don't see a post with title "jones-post-1"
    And I don't see a post with title "jones-post-2"
    But I see a post with title "smiley-post-1"

  Scenario: Posts which user is secondary author are kept when selected the option to delete all content for first authors for user which has content
    Given the user "daphne" exists with role "author"
    And the user "dorothea" exists with role "author"
    And author exists for user "daphne"
    And author exists for user "dorothea"
    And a post named "dorothea-post-1" exists for "dorothea"
    And a post named "dorothea-post-2" exists for "dorothea" and "daphne"
    And a post named "dorothea-post-3" exists for "dorothea" and "daphne"
    And I am on the users admin page
    When I click on the Delete row action for the user "daphne"
    And I select the option "delete_content_first_author" on "#delete_content_first_author"
    And I click on the submit button
    And I open the posts admin page
    Then I see a post with title "dorothea-post-1"
    And I see a post with title "dorothea-post-2"
    And I see a post with title "dorothea-post-3"

  Scenario: Posts which user is secondary author keep the other authors when selected the option to delete all content for first authors for user which has content
    Given the user "ashlee" exists with role "author"
    And the user "alfreda" exists with role "author"
    And author exists for user "ashlee"
    And author exists for user "alfreda"
    And a post named "alfreda-post-1" exists for "alfreda"
    And a post named "alfreda-post-2" exists for "alfreda" and "ashlee"
    And a post named "alfreda-post-3" exists for "alfreda" and "ashlee"
    And I am on the users admin page
    When I click on the Delete row action for the user "ashlee"
    And I select the option "delete_content_first_author" on "#delete_content_first_author"
    And I click on the submit button
    And I open the posts admin page
    Then I see "alfreda" as the author of the post "alfreda-post-1"
    And I see "alfreda" as the author of the post "alfreda-post-2"
    And I see "alfreda" as the author of the post "alfreda-post-3"
    But I don't see the text "ashlee"

  Scenario: Posts which user is not author are kept when selected the option to delete all content for first authors for user which has content
    Given the user "lyssa" exists with role "author"
    And the user "benedict" exists with role "author"
    And author exists for user "lyssa"
    And author exists for user "benedict"
    And a post named "benedict-post-1" exists for "benedict"
    And a post named "benedict-post-2" exists for "benedict"
    And a post named "lyssa-post-1" exists for "lyssa"
    And I am on the users admin page
    When I click on the Delete row action for the user "lyssa"
    And I select the option "delete_content_first_author" on "#delete_content_first_author"
    And I click on the submit button
    And I open the posts admin page
    Then I see a post with title "benedict-post-1"
    And I see a post with title "benedict-post-2"

  ############################################################################
  # User has content - Delete all content
  ############################################################################

  Scenario: User is deleted when selected the option do delete all content for user which has content
    Given the user "sandra" exists with role "author"
    And author exists for user "sandra"
    And a post named "sandra-post-1" exists for "sandra"
    And I am on the users admin page
    When I click on the Delete row action for the user "sandra"
    And I select the option "delete_all_content" on "#delete_all_content"
    And I click on the submit button
    Then I see the text "User deleted"
    And I don't see the text "sandra"

  Scenario: Author is deleted when selected the option to delete all content for user which has content
    Given the user "paola" exists with role "author"
    And author exists for user "paola"
    And a post named "paola-post-1" exists for "paola"
    And I am on the users admin page
    When I click on the Delete row action for the user "paola"
    And I select the option "delete_all_content" on "#delete_all_content"
    And I click on the submit button
    And I open the authors admin page
    Then I don't see the text "paola"

  Scenario: Posts which user is first author are deleted when selected the option to delete all content for user which has content
    Given the user "jonny" exists with role "author"
    And author exists for user "jonny"
    And a post named "paola-post-1" exists for "jonny"
    And I am on the users admin page
    When I click on the Delete row action for the user "jonny"
    And I select the option "delete_all_content" on "#delete_all_content"
    And I click on the submit button
    And I open the posts admin page
    Then I don't see a post with title "paola-post-1"

  Scenario: Posts which user is secondary author are deleted when selected the option to delete all content for user which has content
    Given the user "james" exists with role "author"
    And the user "suellen" exists with role "author"
    And author exists for user "james"
    And author exists for user "suellen"
    And a post named "suellen-post-1" exists for "suellen" and "james"
    And a post named "suellen-post-2" exists for "suellen" and "james"
    And I am on the users admin page
    When I click on the Delete row action for the user "james"
    And I select the option "delete_all_content" on "#delete_all_content"
    And I click on the submit button
    And I open the posts admin page
    And I don't see a post with title "suellen-post-1"
    And I don't see a post with title "suellen-post-2"

  Scenario: Posts which user is not author are kept when selected the option to delete all content for user which has content

  ############################################################################
  # User has content - Attribute content to
  ############################################################################

  Scenario: User is deleted when selected the option to attribute content to another user for user which has content

  Scenario: Author is deleted when selected the option to attribute content to another user for user which has content

  Scenario: Posts which user is first author are attributed to the selected author when selected the option to attribute content to another user for user which has content

  Scenario: Posts which user is secondary author are attributed to the selected author when selected the option to attribute content to another user for user which has content

  Scenario: Posts which user is not author are kept when selected the option to attribute content to another user for user which has content

  ############################################################################
  # User has content - Keep posts but convert to guest author
  ############################################################################

  Scenario: User is deleted when selected the option to convert to guest author for user which has content

  Scenario: Author is converted to guest author when selected the option to convert to guest author for user which has content

  Scenario: Posts which user is first author keep the same author when selected the option to convert to guest author for user which has content

  Scenario: Posts which user is secondary author keep the same author when selected the option to convert to guest author for user which has content

  Scenario: Posts which user is not author are kept when selected the option to convert to guest author for user which has content
