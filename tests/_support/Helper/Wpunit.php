<?php

namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use MultipleAuthors\Classes\Objects\Author;
use MultipleAuthors\Classes\Utils;

class Wpunit extends \Codeception\Module
{
    public function haveACleanEnvironment()
    {
        $this->resetMailer();
        $this->logout();
    }

    public function haveGuestAuthorsWithNoEmail($number)
    {
        $authors = [];

        for ($i = 0; $i < $number; $i++) {
            $authorSlug = sprintf('guest_author_%s', rand(1, PHP_INT_MAX));

            $authors[] = Author::create(
                [
                    'slug'         => $authorSlug,
                    'display_name' => strtoupper($authorSlug),
                ]
            );
        }

        return $authors;
    }

    public function haveGuestAuthorsWithEmail($number)
    {
        $authors = $this->haveGuestAuthorsWithNoEmail($number);

        foreach ($authors as $author) {
            add_term_meta($author->term_id, 'user_email', $author->slug . '@example.com');
        }

        return $authors;
    }

    public function haveAuthorsMappedToUsers($number)
    {
        $wpLoader = $this->getModule('WPLoader');

        $authors = [];
        for ($currentNumber = 0; $currentNumber < $number; $currentNumber++) {
            $userId = $wpLoader->factory('a new user' . $currentNumber)->user->create();

            $authors[$currentNumber] = Author::create_from_user($userId);
        }

        return $authors;
    }

    public function haveGuestAuthors($number, $metaData = null)
    {
        $authors = [];
        for ($currentNumber = 0; $currentNumber < $number; $currentNumber++) {
            $author = Author::create(
                [
                    'slug'         => 'author' . $currentNumber . '_' . microtime(),
                    'display_name' => 'Author ' . $currentNumber . ' ' . microtime(),
                ]
            );

            if (!empty($metaData) && is_array($metaData)) {
                foreach ($metaData as $metaDataKey => $metaDataValue) {
                    update_term_meta(
                        $author->term_id,
                        $metaDataKey,
                        sprintf($metaDataValue, $currentNumber)
                    );
                }
            }

            $authors[$currentNumber] = $author;
        }

        return $authors;
    }

    public function haveAPost()
    {
        $wpLoader = $this->getModule('WPLoader');

        return $wpLoader->factory('a new post')->post->create();
    }

    public function getPostAuthors($postId)
    {
        return wp_get_post_terms($postId, 'author');
    }

    public function haveAPostWithAuthors($authorsList)
    {
        $postId = $this->haveAPost();

        Utils::set_post_authors($postId, $authorsList);

        return $postId;
    }

    public function haveACommentOnPost($postId, $commentText = 'Hey')
    {
        return wp_insert_comment(
            [
                'comment_content' => $commentText,
                'comment_post_ID' => $postId,
            ]
        );
    }

    public function haveACommentByUserOnPost($postId, $userId, $commentText = 'Hey')
    {
        return wp_insert_comment(
            [
                'comment_content' => $commentText,
                'comment_post_ID' => $postId,
                'user_id'         => $userId,
            ]
        );
    }

    public function getMailer()
    {
        return get_phpmailer_mock();
    }

    public function assertNoEmailIsSent()
    {
        $mailer = $this->getMailer();
        $count  = $mailer->count_sent();

        if ($count > 0) {
            $this->fail(
                sprintf(
                    'Failing to check that no email was sent. %d was sent.',
                    $count
                )
            );
        }
    }

    public function assertEmailIsSentToANumberOfRecipients($number)
    {
        $mailer = $this->getMailer();
        $count  = $mailer->count_sent();

        if (empty($count)) {
            $this->fail('Failing to check the number of recipients. No email was sent');
        }

        $this->assertEquals(
            $number,
            $count,
            sprintf('There should have %d recipients on the email', $number)
        );
    }

    public function assertEmailIsSentForTheAuthor(Author $author, $emailIndex = 0)
    {
        $mailer = $this->getMailer();
        $email  = $mailer->get_sent($emailIndex);

        if (empty($email)) {
            $this->fail('No email was sent');
        }

        $author = Author::get_by_term_id($author->term_id);

        foreach ($email->to as $recipient) {
            if ($author->user_email === $recipient[0]) {
                return true;
            }
        }

        $this->fail(
            sprintf(
                'The author %d is not in the recipient list of the sent email',
                $author->term_id
            )
        );
    }

    public function iAmLoggedInAsUser($userLogin)
    {
        wp_signon(
            [
                'user_login'    => $userLogin,
                'user_password' => $userLogin,
                'remember'      => 0
            ]
        );

        $user = get_user_by('login', $userLogin);

        wp_set_current_user($user->ID);
    }

    public function logout()
    {
        wp_logout();
    }

    public function resetMailer()
    {
        reset_phpmailer_instance();

        global $phpmailer;

        $phpmailer = new \MockPHPMailerExtended();
    }
}
