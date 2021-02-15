<?php namespace Users;


use AcceptanceTester;
use MultipleAuthors\Classes\Objects\Author;

class DeleteUsersWithNoContentCest
{
    const ADMIN_USER_LOGIN = 'test_admin';

    const ADMIN_USER_PASSWORD = 'test_admin';

    public function _before(AcceptanceTester $I)
    {
        $I->factory()->user->create(
            [
                'user_login' => self::ADMIN_USER_LOGIN,
                'role'       => 'administrator',
                'user_pass'  => self::ADMIN_USER_PASSWORD,
            ]
        );

        $I->loginAs(self::ADMIN_USER_LOGIN, self::ADMIN_USER_PASSWORD);
    }

    public function whenUserIsNotAuthorAndHasNoContentThenDoNotChangeTheDeleteForm(AcceptanceTester $I)
    {
        $I->am('Administrator');
        $I->wantToTest('when user is not author and has no content then do not change the delete form');

        $userIdToDelete = $I->factory()->user->create(
            [
                'role' => 'author'
            ]
        );

        $I->amOnAdminPage('/users.php');
        $I->moveMouseOver("tr#user-{$userIdToDelete}");
        $I->click("tr#user-{$userIdToDelete} span.delete a.submitdelete");
        $I->seeInCurrentUrl('action=delete');

        $I->see("ID #{$userIdToDelete}");
        $I->see('You have specified this user for deletion');
        $I->dontSee('What should be done with content owned by this user');
        $I->dontSee('Delete all content');
        $I->dontSee('Attribute all content to');
        $I->dontSeeElement('#delete_option_author_wrapper');
        $I->seeElement('input', ['value' => 'Confirm Deletion']);
    }

    public function whenUserIsNotAuthorAndHasNoContentTheUserIsDeleted(AcceptanceTester $I)
    {
        $I->am('Administrator');
        $I->wantToTest('when user is not author and has no content the user is deleted');

        $userIdToDelete = $I->factory()->user->create(
            [
                'role' => 'author'
            ]
        );

        $I->amOnAdminPage('/users.php');
        $I->moveMouseOver("tr#user-{$userIdToDelete}");
        $I->click("tr#user-{$userIdToDelete} span.delete a.submitdelete");
        $I->seeInCurrentUrl('action=delete');
        $I->click('#submit');
        $I->see('User deleted');
        $I->dontSee("tr#user-{$userIdToDelete}");
    }

    public function whenUserIsAuthorAndHasNoContentTheUserIsDeleted(AcceptanceTester $I)
    {
        $I->am('Administrator');
        $I->wantToTest('when user is author but has no content the user is just deleted');

        $userIdToDelete = $I->factory()->user->create(
            [
                'role' => 'author'
            ]
        );

        Author::create_from_user($userIdToDelete);

        $I->amOnAdminPage('/users.php');
        $I->moveMouseOver("tr#user-{$userIdToDelete}");
        $I->click("tr#user-{$userIdToDelete} span.delete a.submitdelete");
        $I->seeInCurrentUrl('action=delete');
        $I->click('#submit');
        $I->see('User deleted');
        $I->dontSee("tr#user-{$userIdToDelete}");
    }

    public function whenUserIsAuthorAndHasNoContentTheAuthorIsDeleted(AcceptanceTester $I)
    {
        $I->am('Administrator');
        $I->wantToTest('when user is author but has no content the author is deleted');

        $userIdToDelete = $I->factory()->user->create(
            [
                'role' => 'author'
            ]
        );

        $author = Author::create_from_user($userIdToDelete);

        $I->amOnAdminPage('/users.php');
        $I->moveMouseOver("tr#user-{$userIdToDelete}");
        $I->click("tr#user-{$userIdToDelete} span.delete a.submitdelete");
        $I->seeInCurrentUrl('action=delete');
        $I->click('#submit');
        $I->see('User deleted');
        $I->dontSee("tr#user-{$userIdToDelete}");

        $term = get_term($author->term_id);

        $I->assertFalse($term);
    }
}
