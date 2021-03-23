<?php

namespace core\functions;

use WpunitTester;

class wp_notify_postauthorCest
{
    public function _before(WpunitTester $I)
    {
        $I->haveACleanEnvironment();
    }

    public function commentOfPostWithOneAuthorMappedToUseShouldSendEmailToTheAuthor(WpunitTester $I)
    {
        $I->am('A Developer');
        $I->wantTo('Call wp_notify_postauthor for a comment of a post with one author mapped to user');
        $I->expectTo('See a email is sent to the post author');

        $authors   = $I->haveAuthorsMappedToUsers(1);
        $postId    = $I->haveAPostWithAuthors($authors);
        $commentId = $I->haveACommentOnPost($postId);

        wp_notify_postauthor($commentId);

        $I->assertEmailIsSentToANumberOfRecipients(1);
        $I->assertEmailIsSentForTheAuthor($authors[0], 0);
    }

    public function commentOfPostWithTwoAuthorMappedToUseShouldSendEmailToAllPostAuthors(WpunitTester $I)
    {
        $I->am('A Developer');
        $I->wantTo('Call wp_notify_postauthor for a comment of a post with two authors mapped to user');
        $I->expectTo('See two emails sent, one for each author');

        $authors   = $I->haveAuthorsMappedToUsers(2);
        $postId    = $I->haveAPostWithAuthors($authors);
        $commentId = $I->haveACommentOnPost($postId);

        wp_notify_postauthor($commentId);

        $I->assertEmailIsSentToANumberOfRecipients(2);
        $I->assertEmailIsSentForTheAuthor($authors[0], 0);
        $I->assertEmailIsSentForTheAuthor($authors[1], 1);
    }

    public function commentOfPostWithOneAuthorWasLeftByTheAuthorItselfShouldNotSendAnyEmail(WpunitTester $I)
    {
        $I->am('A Developer');
        $I->wantTo(
            'Call wp_notify_postauthor for a comment of a post with one author that was left by the author itself'
        );
        $I->expectTo('See no email sent');

        $authors   = $I->haveAuthorsMappedToUsers(1);
        $postId    = $I->haveAPostWithAuthors($authors);
        $commentId = $I->haveACommentByUserOnPost($postId, $authors[0]->user_id);

        wp_notify_postauthor($commentId);

        $I->assertNoEmailIsSent();
    }

    public function commentOfPostWithTwoAuthorsWasLeftByOneOfTheAuthorsShouldSendEmailToTheOtherAuthorOnly(
        WpunitTester $I
    ) {
        $I->am('A Developer');
        $I->wantTo(
            'Call wp_notify_postauthor for a comment of a post with two authors that was left by one of the authors'
        );
        $I->expectTo('See email is sent to the other author only');

        $authors   = $I->haveAuthorsMappedToUsers(2);
        $postId    = $I->haveAPostWithAuthors($authors);
        $commentId = $I->haveACommentByUserOnPost($postId, $authors[0]->user_id);

        wp_notify_postauthor($commentId);

        $I->assertEmailIsSentToANumberOfRecipients(1);
        $I->assertEmailIsSentForTheAuthor($authors[1]);
    }

    public function commentOfPostWithTwoAuthorsOneIsTheCurrentUserShouldSendEmailToTheOtherAuthorOnly(WpunitTester $I)
    {
        $I->am('A Developer');
        $I->wantTo(
            'Call wp_notify_postauthor for a comment of a post with two authors and one of them is the current user'
        );
        $I->expectTo('See email is sent to the other author only');

        $authors           = $I->haveAuthorsMappedToUsers(2);
        $authorCurrentUser = $authors[0];
        $theOtherAuthor    = $authors[1];

        $postId    = $I->haveAPostWithAuthors($authors);
        $commentId = $I->haveACommentOnPost($postId);

        $I->iAmLoggedInAsUser($authorCurrentUser->user_login);
        $I->comment('Call wp_notify_postauthor');
        wp_notify_postauthor($commentId);

        $I->assertEmailIsSentToANumberOfRecipients(1);
        $I->assertEmailIsSentForTheAuthor($theOtherAuthor);
    }

    public function commentOfPostWithTwoGuestAuthorsWithNoEmailShouldNotSendEmails(WpunitTester $I)
    {
        $I->am('A Developer');
        $I->wantTo('Call wp_notify_postauthor for a comment of a post with two guest authors with no email addresses');
        $I->expectTo('See no email is sent');

        $authorsWithNoEmail = $I->haveGuestAuthorsWithNoEmail(2);
        $postId             = $I->haveAPostWithAuthors($authorsWithNoEmail);
        $commentId          = $I->haveACommentOnPost($postId);

        $I->comment('Call wp_notify_postauthor');
        wp_notify_postauthor($commentId);

        $I->assertNoEmailIsSent();
    }

    public function commentOfPostWithTwoGuestAuthorsWitEmailShouldSendEmailToThem(WpunitTester $I)
    {
        $I->am('A Developer');
        $I->wantTo('Call wp_notify_postauthor for a comment of a post with two guest authors with email addresses');
        $I->expectTo('See email is sent to each author');

        $authorsWithEmail = $I->haveGuestAuthorsWithEmail(2);
        $postId           = $I->haveAPostWithAuthors($authorsWithEmail);
        $commentId        = $I->haveACommentOnPost($postId);

        $I->comment('Call wp_notify_postauthor');
        wp_notify_postauthor($commentId);

        $I->assertEmailIsSentToANumberOfRecipients(2);
        $I->assertEmailIsSentForTheAuthor($authorsWithEmail[0], 0);
        $I->assertEmailIsSentForTheAuthor($authorsWithEmail[1], 1);
    }

    public function commentOfPostWithTwoMixedAuthorsWitEmailShouldSendEmailToThem(WpunitTester $I)
    {
        $I->am('A Developer');
        $I->wantTo('Call wp_notify_postauthor for a comment of a post with two authors one guest with email address and another mapped to user');
        $I->expectTo('See email is sent to each author');

        $authorsWithEmail = array_merge(
            $I->haveGuestAuthorsWithEmail(1),
            $I->haveAuthorsMappedToUsers(1)
        );
        $postId           = $I->haveAPostWithAuthors($authorsWithEmail);
        $commentId        = $I->haveACommentOnPost($postId);

        $I->comment('Call wp_notify_postauthor');
        wp_notify_postauthor($commentId);

        $I->assertEmailIsSentToANumberOfRecipients(2);
        $I->assertEmailIsSentForTheAuthor($authorsWithEmail[0], 0);
        $I->assertEmailIsSentForTheAuthor($authorsWithEmail[1], 1);
    }
}
