<?php

class MockPHPMailerExtended extends MockPHPMailer {
    public function get_all_sent()
    {
        return $this->mock_sent;
    }

    public function count_sent()
    {
        return count($this->mock_sent);
    }
}
