<?php

define('TESTS_ROOT_PATH', realpath(__DIR__ . '/../'));
define('PUBLISHPRESS_AUTHORS_BYPASS_INSTALLER', true);

// Override the PHPMailer
global $phpmailer;

require_once TESTS_ROOT_PATH . '/_support/Mock/MockPHPMailer.php';

$phpmailer = new MockPHPMailerExtended();

function get_phpmailer_mock()
{
    global $phpmailer;

    return $phpmailer;
}
