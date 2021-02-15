<?php namespace Users;


use AcceptanceTester;
use Facebook\WebDriver\WebDriverKeys;
use MultipleAuthors\Classes\Objects\Author as PPMAAuthor;
use MultipleAuthors\Classes\Utils;

class DeleteUsersWhenSecondaryAuthorCest
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

    public function whenUserIsSecondaryAuthorThenISeeAuthorField(AcceptanceTester $I)
    {
        $I->am('Administrator');
        $I->wantToTest('when user is secondary author then I see the author field');

        $userIdToDelete = $I->factory()->user->create(
            [
                'role' => 'author',
            ]
        );

        $primaryAuthor = $I->factory()->user->create(
            [
                'role' => 'author',
            ]
        );

        $postId = $I->factory()->post->create(
            [
                'post_author' => $primaryAuthor,
            ]
        );

        $primaryAuthor = PPMAAuthor::create_from_user($primaryAuthor);
        $secondaryAuthor = PPMAAuthor::create_from_user($userIdToDelete);

        Utils::set_post_authors($postId, [$primaryAuthor, $secondaryAuthor]);

        $I->amOnAdminPage('/users.php');
        $I->moveMouseOver("tr#user-{$userIdToDelete}");
        $I->click("tr#user-{$userIdToDelete} span.delete a.submitdelete");
        $I->seeInCurrentUrl('action=delete');

        $I->see("ID #{$userIdToDelete}");
        $I->see('You have specified this user for deletion');
        $I->see('What should be done with content owned by this user');
        $I->see('Delete all content');
        $I->see('Attribute all content to');
        $I->seeElement('#delete_option_author_wrapper');
        $I->dontSeeElement('#reassign_user');
        $I->seeElementInDOM('#reassign_user');
    }
}
