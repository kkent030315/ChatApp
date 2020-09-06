<?php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'chat_service_db');

/*
 * A Class that provides database features
 */
class DatabaseProvider
{
    private $errorCallbackFunction;

    public function __construct($errorCallbackFunction)
    {
        /* Register callback function */
        $this->errorCallbackFunction = $errorCallbackFunction;
    }

    /*
     * Execute Query
     */
    public function IssueQuery($queryString)
    {
        return mysqli_query($this->DBConnect(), $queryString);
    }

    /*
     * Escape the string to prevent from such as SQL-Injection
     * [IMPORTANT] Please note that this is not a complete method
     * [TODO] Clean XSSes
     */
    public function EscapeString($sourceString, $stripTags = true)
    {
        if ($stripTags) {
            $sourceString = strip_tags($sourceString);
        }

        return mysqli_real_escape_string($this->DBConnect(), $sourceString);
    }

    /*
     * Connect to the database
     * If any errors occurred, the callback function will be called immediately
     */
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
