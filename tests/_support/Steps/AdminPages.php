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
}
