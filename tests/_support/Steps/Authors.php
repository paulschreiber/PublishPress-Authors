<?php

namespace Steps;


use Behat\Gherkin\Node\TableNode;
use MultipleAuthors\Classes\Objects\Author;

trait Authors
{
    /**
     * @Given author exists for user :userLogin
     */
    public function authorExistsForUser($userLogin)
    {
        $user = get_user_by('login', $userLogin);

        Author::create_from_user($user);
    }
}
