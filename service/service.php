<?php
require 'database.php';

class ChatServiceCore
{
    private $selfName;
    private $recipientName;
    private $db;

    public function __construct($selfName, $recipientName, $errorCallbackFunction)
    {
        $this->selfName = $selfName;
        $this->recipientName = $recipientName;
        $this->db = new DatabaseProvider($errorCallbackFunction);
    }

    public function SendChat($message)
    {
        $message = $this->db->EscapeString($message);

        if (empty($message)) {
            return false;
        }

        $queryString = "INSERT INTO `chats`
        (`sender`, `recipient`, `context`)
        VALUES
        ('$this->selfName', '$this->recipientName', '$message')";

        $queryResult = $this->db->IssueQuery($queryString);

        if ($queryResult['affected_rows'] < 1) {
            return false;
        }

        return true;
    }

    public function GetChats($numberOfMessages = 100)
    {
        if (!is_int($numberOfMessages) || $numberOfMessages < 1) {
            return false;
        }

        $queryString = "SELECT * FROM `chats`
        WHERE
        (`sender`='$this->recipientName' AND `recipient`='$this->selfName')
        OR
        (`sender`='$this->selfName' AND `recipient`='$this->recipientName')
        ORDER BY `datetime`
        LIMIT $numberOfMessages";

        $queryResult = $this->db->IssueQuery($queryString);

        if (!$queryResult) {
            return false;
        }

        $queryString = "UPDATE `chats` SET `readed`=IF(`readed`=1,1,1)
        WHERE
        (`sender`='$this->recipientName' AND `recipient`='$this->selfName')
        ORDER BY `datetime`
        LIMIT $numberOfMessages";

        $this->db->IssueQuery($queryString);

        return mysqli_fetch_all($queryResult);
    }

    public function GetRecipientName()
    {
        return $this->recipientName;
    }
}
