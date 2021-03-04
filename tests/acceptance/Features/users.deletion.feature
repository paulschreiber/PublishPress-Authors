Feature: Users deletion
  In order to delete users on different scenarios
  As an admin
  I need to be able to choose what to do with the author terms and content owned by the user before the user is deleted

  Background:
    Given the user "admin_user_deletion" exists with role "administrator"
    And I am logged in as "admin_user_deletion"
    And following users exist
      | user_login | user_role |
      | john       | author    |
      | peter      | author    |
      | mary       | author    |
      | jack       | author    |
      | lucy       | author    |
      | oprah      | author    |
      | patrick    | author    |
      | jonatan    | author    |
      | erick      | author    |
      | simon      | author    |
      | joshua     | author    |
      | mathew     | author    |
      | luck       | author    |
      | soraya     | author    |
      | paul       | author    |
    And I open the users admin page

  Scenario: See correct form when selected one user which has no content and is not an author
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
    Given the user "peter" is selected
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
    When I click on the Delete row action for the user "jack"
    And I click on the submit button
    Then I see the text "User deleted"
    And I don't see the text "jack"

  Scenario: Users are deleted when selected multiple users which has no content and are not authors
    Given the user "lucy" is selected
    And the user "oprah" is selected
    When I select and apply the bulk action "delete"
    And I click on the submit button
    Then I see the text "Users deleted"
    And I don't see the text "lucy"
    And I don't see the text "oprah"

  Scenario: See option to delete author or convert to guest author when selected user has no content but is an author
    Given author exists for user "patrick"
    And the user "patrick" is selected
    When I click on the Delete row action for the user "patrick"
    Then I see the user ID for "patrick" in the deletion list
    And I see the text "patrick"
    And I see the text "What should be done with the author term?"
    And I see the text "Delete the author term"
    And I see the text "Convert to guest author"

  Scenario: See option to delete authors or convert to guest authors when selected users have no content but at least one is an author
    Given author exists for user "erick"
    And the user "jonatan" is selected
    And the user "erick" is selected
    When I select and apply the bulk action "delete"
    Then I see the user ID for "jonatan" in the deletion list
    Then I see the user ID for "erick" in the deletion list
    And I see the text "jonatan"
    And I see the text "erick"
    And I see the text "At least one of those users is an author. What should be done with the author terms"
    And I see the text "Delete the author term"
    And I see the text "Convert to guest author"

  Scenario: User is deleted when selected the option to delete author for user which has no content but is an author
    Given author exists for user "simon"
    When I click on the Delete row action for the user "simon"
    And I select the option "delete_author_terms" on "#delete_author_terms"
    And I click on the submit button
    Then I see the text "User deleted"
    And I don't see the text "simon"

  Scenario: Author is deleted when selected the option to delete author for user which has no content but is an author
    Given author exists for user "joshua"
    When I click on the Delete row action for the user "joshua"
    And I select the option "delete_author_terms" on "#delete_author_terms"
    And I click on the submit button
    And I open the authors admin page
    Then I don't see the text "joshua"

  Scenario: User is deleted when selected the option to convert to guest author for user which has no content but is an author
    Given author exists for user "luck"
    When I click on the Delete row action for the user "luck"
    And I select the option "convert_to_guest_author" on "#convert_to_guest_author"
    And I click on the submit button
    Then I see the text "User deleted"
    And I don't see the text "luck"

  Scenario: Author is not deleted when selected the option to convert to guest author for user which has no content but is an author
    Given author exists for user "soraya"
    When I click on the Delete row action for the user "soraya"
    And I select the option "convert_to_guest_author" on "#convert_to_guest_author"
    And I click on the submit button
    And I open the authors admin page
    Then I see the text "soraya"

  Scenario: Author is converted to guest author when selected the option to convert to guest author for user which has no content but is an author
    Given author exists for user "paul"
    When I click on the Delete row action for the user "paul"
    And I select the option "convert_to_guest_author" on "#convert_to_guest_author"
    And I click on the submit button
    And I open the authors admin page
    Then I see the text "paul — Guest Author"
