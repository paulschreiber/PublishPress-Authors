<?php

use MultipleAuthors\Classes\Objects\Author;
use MultipleAuthors\Classes\Utils;

class EditPrivatePostCest
{
    /**
     * @var int
     */
    private $postId;

    /**
     * @var WP_Post
     */
    private $post;

    /**
     * @var string
     */
    private $userLogin1;

    /**
     * @var string
     */
    private $userLogin2;

    /**
     * @var string
     */
    private $userLogin3;

    /**
     * @var string
     */
    private $postContent = 'The Content!';

    public function _before(AcceptanceTester $I)
    {
        $authorUserId1 = $I->factory()->user->create(
            [
                'role'       => 'author',
                'user_pass'  => 'secret',
            ]
        );

        $authorUserId2 = $I->factory()->user->create(
            [
                'role'       => 'author',
                'user_pass'  => 'secret',
            ]
        );

        $authorUserId3 = $I->factory()->user->create(
            [
                'role'       => 'author',
                'user_pass'  => 'secret',
            ]
        );

        $user1 = get_user_by('ID', $authorUserId1);
        $user2 = get_user_by('ID', $authorUserId2);
        $user3 = get_user_by('ID', $authorUserId3);

        $this->userLogin1 = $user1->user_login;
        $this->userLogin2 = $user2->user_login;
        $this->userLogin3 = $user3->user_login;

        $this->postId = $I->factory()->post->create(
            [
                'post_name'    => 'private_post1',
                'post_title'   => 'The private post title',
                'post_author'  => $authorUserId1,
                'post_content' => $this->postContent,
                'post_status'  => 'private',
            ]
        );

        $this->post = get_post($this->postId);

        $author1 = Author::create_from_user($authorUserId1);
        $author2 = Author::create_from_user($authorUserId2);
        $author3 = Author::create_from_user($authorUserId3);

        Utils::set_post_authors($this->postId, [$author1, $author2]);
    }

    public function tryToSeePrivatePostInPostLIstAsPrimaryAuthor(AcceptanceTester $I)
    {
        $I->wantTo('I see the private post in the post list if I am the primary author');

        $I->loginAs($this->userLogin1, 'secret');
        $I->amOnAdminPage('/edit.php');

        $I->seeElement('tr#post-' . $this->postId);
        $I->see($this->post->post_title);
    }

    public function tryToEditPrivatePostAsPrimaryAuthor(AcceptanceTester $I)
    {
        $I->wantTo('I edit the private post if I am the primary author');

        $I->loginAs($this->userLogin1, 'secret');
        $I->amOnAdminPage('/post.php?action=edit&post=' . $this->postId);

        $I->seeInTitle('Edit Post');
        $I->seeElementInDOM('body.block-editor-page');
    }

    public function tryToSeePrivatePostInPostListAsSecondaryAuthor(AcceptanceTester $I)
    {
        $I->wantTo('I see the private post in the post list if I am a secondary author');

        $I->loginAs($this->userLogin2 , 'secret');
        $I->amOnAdminPage('/edit.php');

        $I->seeElement('tr#post-' . $this->postId);
        $I->see($this->post->post_title);
    }

    public function tryToEditPrivatePostAsSecondaryAuthor(AcceptanceTester $I)
    {
        $I->wantTo('I edit the private post if I am a secondary author');

        $I->loginAs($this->userLogin2, 'secret');
        $I->amOnAdminPage('/post.php?action=edit&post=' . $this->postId);

        $I->seeInTitle('Edit Post');
        $I->seeElementInDOM('body.block-editor-page');
    }

    public function tryToEditPrivatePostAsGuestAndSeeLogInPage(AcceptanceTester $I)
    {
        $I->wantTo('I see login page if I am not logged in and try to edit the private post');

        $I->amOnAdminPage('/post.php?action=edit&post=' . $this->postId);
        $I->seeInTitle('Log In');
    }

    public function tryToEditPrivatePosNotBeingAuthorAndSeeNotAllowedWarning(AcceptanceTester $I)
    {
        $I->wantTo('I see a warning and can not edit a private post if I am not the author');

        $I->loginAs($this->userLogin3, 'secret');
        $I->amOnAdminPage('/post.php?action=edit&post=' . $this->postId);
        $I->see('not allowed to edit this item');
    }
}
