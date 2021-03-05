<?php

namespace Steps;


trait AdminPages
{
    /**
     * @Given I open the users admin page
     */
    public function iOpenTheUsersAdminPage()
    {
        $this->amOnAdminPage('/users.php');
    }

    /**
     * @When I open the authors admin page
     */
    public function iOpenTheAuthorsAdminPage()
    {
        $this->amOnAdminPage('edit-tags.php?taxonomy=author');
    }

    /**
     * @When I open the posts admin page
     */
    public function iOpenThePostsAdminPage()
    {
        $this->amOnAdminPage('edit.php');
    }
}
