<?php

use MultipleAuthors\Classes\Objects\Author;
use MultipleAuthors\Classes\Utils;

class ReadPrivatePostCest
{
    private $postId;

    private $userLogin1;

    private $userLogin2;

    private $userLogin3;

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

        $author1 = Author::create_from_user($authorUserId1);
        $author2 = Author::create_from_user($authorUserId2);
        $author3 = Author::create_from_user($authorUserId3);

        Utils::set_post_authors($this->postId, [$author1, $author2]);
    }

    public function tryToReadPrivatePostAsPrimaryAuthor(AcceptanceTester $I)
    {
        $I->loginAs($this->userLogin1, 'secret');
        $I->amOnPage('/?p=' . $this->postId);

        $I->see($this->postContent);
    }

    public function tryToReadPrivatePostAsSecondaryAuthor(AcceptanceTester $I)
    {
        $authorRole = get_role('author');
        $authorRole->add_cap('read_private_posts');

        $I->loginAs($this->userLogin2, 'secret');
        $I->amOnPage('/?p=' . $this->postId);

        $I->see($this->postContent);
    }

    public function tryToReadPrivatePostAsGuestAndSee404(AcceptanceTester $I)
    {
        $I->amOnPage('/?p=' . $this->postId);
        $I->dontSee($this->postContent);
    }

    public function tryToReadPrivatePosNotBeingAuthorAndSee404(AcceptanceTester $I)
    {
        $authorRole = get_role('author');
        $authorRole->remove_cap('read_private_posts');

        $I->loginAs($this->userLogin3, 'secret');
        $I->amOnPage('/?p=' . $this->postId);
        $I->dontSee($this->postContent);
    }
}
