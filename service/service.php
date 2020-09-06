<?php
require 'database.php';

/*
 * A Class that provides core system of the service
 */
class ChatServiceCore
{
    private $selfName;
    private $recipientName;
    private $db;

    /*
     * Constructor
     * @selfName Sender of the message
     * @recipientName Recipient of the message
     * @errorCallbackFunction The function will be called if a fatal error occured on the system
     */
    public function __construct($selfName, $recipientName, $errorCallbackFunction)
    {
        $this->selfName = $selfName;
        $this->recipientName = $recipientName;

        /* Share it to the another providers */
        $this->db = new DatabaseProvider($errorCallbackFunction);
    }

    /*
     * Send the message to current recipient
     * @return bool
     */
    public function SendChat($message)
    {
        $message = $this->db->EscapeString($message);

        if (empty($message)) {
            return false;
        }

        /* Insert the query */
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

    /*
     * Returns number of chats that this user and recipient have
     * @return array|bool
     */
    public function GetChats($numberOfMessages = 100)
    {
        if (!is_int($numberOfMessages) || $numberOfMessages < 1) {
            return false;
        }

        /* Get the number of messages */
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
        /* */

        /* Mark as readed */
        $queryString = "UPDATE `chats` SET `readed`=IF(`readed`=1,1,1)
        WHERE
        (`sender`='$this->recipientName' AND `recipient`='$this->selfName')
        ORDER BY `datetime`
        LIMIT $numberOfMessages";

        $this->db->IssueQuery($queryString);
        /* */

        return mysqli_fetch_all($queryResult);
    }

    /*
     * Returns current recipient
     * @return string
     */
    public function GetRecipientName()
    {
        return $this->recipientName;
    }
}
