<?php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'chat_service_db');

class DatabaseProvider
{
    private $errorCallbackFunction;

    public function __construct($errorCallbackFunction)
    {
        $this->errorCallbackFunction = $errorCallbackFunction;
    }

    public function IssueQuery($queryString)
    {
        return mysqli_query($this->DBConnect(), $queryString);
    }

    public function EscapeString($sourceString, $stripTags = true)
    {
        if ($stripTags) {
            $sourceString = strip_tags($sourceString);
        }

        return mysqli_real_escape_string($this->DBConnect(), $sourceString);
    }

    private function DBConnect()
    {
        $sqlConnection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if (mysqli_connect_errno()) {
            call_user_func($this->errorCallbackFunction);
            return null;
        }

        return $sqlConnection;
    }
}
