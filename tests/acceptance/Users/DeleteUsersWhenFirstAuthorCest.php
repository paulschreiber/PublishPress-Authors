<?php namespace Users;


use AcceptanceTester;
use Facebook\WebDriver\WebDriverKeys;
use MultipleAuthors\Classes\Objects\Author as PPMAAuthor;
use MultipleAuthors\Classes\Utils;

class DeleteUsersWhenFirstAuthorCest
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

    public function whenUserIsTheFirstAuthorThenISeeDeleteAllOption(AcceptanceTester $I)
    {
        $I->am('Administrator');
        $I->wantToTest('when user is the first author then I see the "Delete all content" field');

        $userIdToDelete = $I->factory()->user->create(
            [
                'role' => 'author',
            ]
        );

        $postId = $I->factory()->post->create(
            [
                'post_author' => $userIdToDelete,
            ]
        );

        $author = PPMAAuthor::create_from_user($userIdToDelete);
        Utils::set_post_authors($postId, [$author]);

        $I->amOnAdminPage('/users.php');
        $I->moveMouseOver("tr#user-{$userIdToDelete}");
        $I->click("tr#user-{$userIdToDelete} span.delete a.submitdelete");
        $I->seeInCurrentUrl('action=delete');

        $I->see("ID #{$userIdToDelete}");
        $I->see('You have specified this user for deletion');
        $I->see('What should be done with content owned by this user');
        $I->see('Delete all content');
        $I->seeElement('#delete_option_author_wrapper');
    }

    public function whenUserIsTheFirstAuthorThenISeeAuthorField(AcceptanceTester $I)
    {
        $I->am('Administrator');
        $I->wantToTest('when user is the first author then I see the author field');

        $userIdToDelete = $I->factory()->user->create(
            [
                'role' => 'author',
            ]
        );

        $postId = $I->factory()->post->create(
            [
                'post_author' => $userIdToDelete,
            ]
        );

        $author = PPMAAuthor::create_from_user($userIdToDelete);
        Utils::set_post_authors($postId, [$author]);

        $I->amOnAdminPage('/users.php');
        $I->moveMouseOver("tr#user-{$userIdToDelete}");
        $I->click("tr#user-{$userIdToDelete} span.delete a.submitdelete");
        $I->seeInCurrentUrl('action=delete');

        $I->see("ID #{$userIdToDelete}");
        $I->see('You have specified this user for deletion');
        $I->see('What should be done with content owned by this user');
        $I->see('Attribute all content to');
        $I->dontSeeElement('#reassign_user');
        $I->seeElementInDOM('#reassign_user');
    }

    public function whenUserIsTheFirstAuthorAndIDeleteAllThenUserIsDeleted(AcceptanceTester $I)
    {
        $I->am('Administrator');
        $I->wantToTest('when user is the first author then I see the author field');

        $userIdToDelete = $I->factory()->user->create(
            [
                'role' => 'author',
            ]
        );

        $postId = $I->factory()->post->create(
            [
                'post_author' => $userIdToDelete,
            ]
        );

        $author = PPMAAuthor::create_from_user($userIdToDelete);
        Utils::set_post_authors($postId, [$author]);

        $I->amOnAdminPage('/users.php');
        $I->moveMouseOver("tr#user-{$userIdToDelete}");
        $I->click("tr#user-{$userIdToDelete} span.delete a.submitdelete");
        $I->seeInCurrentUrl('action=delete');

        $I->see("ID #{$userIdToDelete}");
        $I->checkOption('#updateusers input[value="delete"]');
        $I->click('#submit');
        $I->see('User deleted');
        $I->dontSee($author->display_name);
    }

    public function whenUserIsTheFirstAuthorAndIDeleteAllThenPostsAreDeleted(AcceptanceTester $I)
    {
        $I->am('Administrator');
        $I->wantToTest('when user is the first author and I select "Delete all content" all his posts are deleted');

        $userIdToDelete = $I->factory()->user->create(
            [
                'role' => 'author',
            ]
        );

        $anotherUserId = $I->factory()->user->create(
            [
                'role' => 'author',
            ]
        );

        $userPostId1 = $I->factory()->post->create(
            [
                'post_author' => $userIdToDelete,
            ]
        );

        $userPostId2 = $I->factory()->post->create(
            [
                'post_author' => $userIdToDelete,
            ]
        );

        $userPostId3 = $I->factory()->post->create(
            [
                'post_author' => $userIdToDelete,
            ]
        );

        $anotherUserPostId1 = $I->factory()->post->create(
            [
                'post_author' => $anotherUserId,
            ]
        );

        $anotherUserPostId2 = $I->factory()->post->create(
            [
                'post_author' => $anotherUserId,
            ]
        );

        $author = PPMAAuthor::create_from_user($userIdToDelete);
        Utils::set_post_authors($userPostId1, [$author]);

        $I->amOnAdminPage('/users.php');
        $I->moveMouseOver("tr#user-{$userIdToDelete}");
        $I->click("tr#user-{$userIdToDelete} span.delete a.submitdelete");
        $I->seeInCurrentUrl('action=delete');

        $I->see("ID #{$userIdToDelete}");
        $I->checkOption('#updateusers input[value="delete"]');
        $I->click('#submit');
        $I->amOnAdminPage('/edit.php');
        $I->see('Posts');
        $I->dontSeeElement("tr#post-{$userPostId1}");
        $I->dontSeeElement("tr#post-{$userPostId2}");
        $I->dontSeeElement("tr#post-{$userPostId3}");
        $I->seeElement("tr#post-{$anotherUserPostId1}");
        $I->seeElement("tr#post-{$anotherUserPostId2}");
    }

    public function whenFirstAuthorAndAllContentToAnotherAuthorThenPostAuthorsChangedOnTheCorrectOrder(AcceptanceTester $I)
    {
        $I->am('Administrator');
        $I->wantToTest('when is primary author and we selected "Attribute All Content To" then post author changed on the correct order');

        $userIdToDelete = $I->factory()->user->create(
            [
                'role' => 'author',
            ]
        );

        $newAuthorUserId = $I->factory()->user->create(
            [
                'role' => 'author',
            ]
        );

        $secondAuthorUserId = $I->factory()->user->create(
            [
                'role' => 'author',
            ]
        );

        $postId = $I->factory()->post->create(
            [
                'post_author' => $userIdToDelete,
            ]
        );

        $authorToDelete = PPMAAuthor::create_from_user($userIdToDelete);
        $newAuthor      = PPMAAuthor::create_from_user($newAuthorUserId);
        $secondAuthor   = PPMAAuthor::create_from_user($secondAuthorUserId);

        Utils::set_post_authors($postId, [$authorToDelete, $secondAuthor]);

        $I->amOnAdminPage('/users.php');
        $I->moveMouseOver("tr#user-{$userIdToDelete}");
        $I->click("tr#user-{$userIdToDelete} span.delete a.submitdelete");
        $I->seeInCurrentUrl('action=delete');
        $I->moveMouseOver('#reassign_author + span.ppma_select2');
        $I->click('#reassign_author + span.ppma_select2');
        $I->waitForElementVisible('.ppma_select2-search__field');
        $I->fillField('.ppma_select2-search__field', $newAuthor->display_name);
        $I->clickWithLeftButton(
            '#ppma_select2-reassign_author-results .ppma_select2-results__option--highlighted',
            20,
            70
        );
        $I->click('#submit');

        $I->amOnAdminPage('/edit.php');
        $I->seeElement('tr#post-' . $postId);
        $I->see($newAuthor->display_name . ', ' . $secondAuthor->display_name, 'tr#post-' . $postId . ' td.authors.column-authors');
    }

    public function whenFirstAuthorAndAllContentToAnotherAuthorThenUserIsDeleted(AcceptanceTester $I)
    {
        $I->am('Administrator');
        $I->wantToTest('when is primary author and we selected "Attribute All Content To" then user is deleted');

        $userIdToDelete = $I->factory()->user->create(
            [
                'role' => 'author',
            ]
        );

        $newAuthorUserId = $I->factory()->user->create(
            [
                'role' => 'author',
            ]
        );

        $postId = $I->factory()->post->create(
            [
                'post_author' => $userIdToDelete,
            ]
        );

        $authorToDelete = PPMAAuthor::create_from_user($userIdToDelete);
        $newAuthor      = PPMAAuthor::create_from_user($newAuthorUserId);

        Utils::set_post_authors($postId, [$authorToDelete]);

        $I->amOnAdminPage('/users.php');
        $I->moveMouseOver("tr#user-{$userIdToDelete}");
        $I->click("tr#user-{$userIdToDelete} span.delete a.submitdelete");
        $I->seeInCurrentUrl('action=delete');
        $I->moveMouseOver('#reassign_author + span.ppma_select2');
        $I->click('#reassign_author + span.ppma_select2');
        $I->waitForElementVisible('.ppma_select2-search__field');
        $I->fillField('.ppma_select2-search__field', $newAuthor->display_name);
        $I->clickWithLeftButton(
            '#ppma_select2-reassign_author-results .ppma_select2-results__option--highlighted',
            20,
            70
        );
        $I->click('#submit');
        $I->dontSee('Warning');
        $I->see('user deleted');
        $I->dontSee($authorToDelete->display_name);
        $I->dontSee($authorToDelete->user_email);
    }

    public function whenFirstAuthorAndAllContentToAnotherAuthorThenPostAuthorColumnChanged(AcceptanceTester $I)
    {
        $I->am('Administrator');
        $I->wantToTest('when is primary author and we selected "Attribute All Content To" then post author column changed');

        $userIdToDelete = $I->factory()->user->create(
            [
                'role' => 'author',
            ]
        );

        $newAuthorUserId = $I->factory()->user->create(
            [
                'role' => 'author',
            ]
        );

        $secondAuthorUserId = $I->factory()->user->create(
            [
                'role' => 'author',
            ]
        );

        $postId = $I->factory()->post->create(
            [
                'post_author' => $userIdToDelete,
            ]
        );

        $post = get_post($postId);
        $I->assertEquals($userIdToDelete, $post->post_author);

        $authorToDelete = PPMAAuthor::create_from_user($userIdToDelete);
        $newAuthor      = PPMAAuthor::create_from_user($newAuthorUserId);
        $secondAuthor   = PPMAAuthor::create_from_user($secondAuthorUserId);

        Utils::set_post_authors($postId, [$authorToDelete, $secondAuthor]);

        $I->amOnAdminPage('/users.php');
        $I->moveMouseOver("tr#user-{$userIdToDelete}");
        $I->click("tr#user-{$userIdToDelete} span.delete a.submitdelete");
        $I->seeInCurrentUrl('action=delete');
        $I->moveMouseOver('#reassign_author + span.ppma_select2');
        $I->click('#reassign_author + span.ppma_select2');
        $I->waitForElementVisible('.ppma_select2-search__field');
        $I->fillField('.ppma_select2-search__field', $newAuthor->display_name);
        $I->clickWithLeftButton(
            '#ppma_select2-reassign_author-results .ppma_select2-results__option--highlighted',
            20,
            70
        );
        $I->click('#submit');

        $post = get_post($postId);
        $I->assertEquals($newAuthorUserId, $post->post_author);
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
