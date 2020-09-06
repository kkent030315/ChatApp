<?php
require __DIR__ . '/service/service.php';
require __DIR__ . '/service/session.php';
require __DIR__ . '/service/http_handler.php';
require __DIR__ . '/service/utils.php';

/*
 * Callback-receiver function that handles low-level fatal errors
 */
function errorCallbackReceiver()
{
    echo ('
    <script>
    document.body.innerHTML="";
    var new_element = document.createElement("h2");
    new_element.innerHTML = "Fatal error occurred on our service core system.";
    document.body.appendChild(new_element);
    new_element = document.createElement("h2");
    new_element.innerHTML = "Please try again later.";
    document.body.appendChild(new_element);
    </script>');
    die();
}

/*
 * Start session if not started yet
 */
SERVICE_SESSION\StartSessionIfNeeded();

/*
 * Flag for check if the session initialized with the service
 * Type: bool
 */
$isServiceAvailable = isset($_SESSION['service']);

if ($isServiceAvailable) {
    /* if the session already initialized, restore the objects */
    $service = unserialize($_SESSION['service']);
}

/*
 * Http Request Handler For Close Session
 * Method: POST
 */
HTTP_HANDLER\RegisterHttpRequestHandler(HTTP_POST, 'reset', 'SERVICE_SESSION\DisposeSessionForcibly');

/* Echo error label */
function EchoDOMError($innerTEXT)
{
    echo ("<div class=\"alert alert-danger\">$innerTEXT</div>");
}

/* Echo success label */
function EchoDOMSuccess($innerTEXT)
{
    echo ("<div class=\"alert alert-success\">$innerTEXT</div>");
}
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0">

    <link href="assets/css/style.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Google+Sans:400,400i,500,500i,700,700i|Google+Sans+Display:400,400i,500,500i,700,700i&subset=all" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
</head>

 <body>
     <div class="wrapper">
        <div class="container">
            <div class="sector">
                <p class="sector-header">基本設定</p>
<?php
/*
 * Http Request Handler For Configure Sender And Recipient
 * Method: POST
 */
HTTP_HANDLER\RegisterHttpRequestHandler(HTTP_POST, 'configure', function () {
    $selfName = filter_input(INPUT_POST, 'self_name');
    $recipientName = filter_input(INPUT_POST, 'recipient_name');

    if (empty($selfName) || empty($recipientName)) {
        EchoDOMError("送信先及び自分の名前を正しく入力してください");
        header("Refresh:2");
        die();
    }

    if ($selfName === $recipientName) {
        EchoDOMError("送信先と自身の名前は同一にできません");
        header("Refresh:2");
        die();
    }

    $service = new ChatServiceCore($selfName, $recipientName, 'errorCallbackReceiver');
    $_SESSION['service'] = serialize($service);

    EchoDOMSuccess("送信先 $recipientName が設定されました");
    header("Refresh:2");
});
?>
                <form method="POST">
                    <div class="general column-style">
                        <label for="my_name" class="centering">自分の名前:</label>
                        <input type="text" name="self_name" autocomplete="off" required>
                    </div>
                    <div class="general column-style">
                        <label for="my_name" class="centering">相手の名前:</label>
                        <input type="text" name="recipient_name" autocomplete="off" required>
                    </div>
                    <button class="button-normal" name="configure">設定</button>
                    <button class="button-normal" name="reset" formnovalidate>リセット</button>
                </form>
            </div>

<?php
$recipient = '未設定';
if ($isServiceAvailable) {
    $recipient = $service->GetRecipientName();
}
?>

            <div class="sector">
                <p class="sector-header">チャット<?php echo (" - $recipient"); ?></p>
<?php
/*
 * Http Request Handler For Submitting Message
 * Method: POST
 */
HTTP_HANDLER\RegisterHttpRequestHandler(HTTP_POST, 'send_chat', function () {
    $message = filter_input(INPUT_POST, 'message');

    if (!is_string($message) || empty($message)) {
        EchoDOMError("無効なメッセージ");
        header("Refresh:2");
        die();
    }

    $service = unserialize($_SESSION['service']);

    if (!$service) {
        EchoDOMError("予期しないエラー");
        header("Refresh:2");
        die();
    }

    $service->SendChat($message);

    header("Refresh:1");
});
?>
                <div class="chat-container" id="scrollable">

                <?php if ($isServiceAvailable): ?>
                    <?php $cacheSender = ""; /* Cache last-sender */?>
                    <?php foreach ($service->GetChats() as $row_chat): ?>
<?php
/* Formatted time string 00:00 */
$messageTime = datetime_format($row_chat[3], 'H:i');
/* is the message got readed or not, Type: bool */
$readed = $row_chat[4] == 1;
/* Message context */
$messageContext = $row_chat[2];
/* Sender of the message */
$messageSender = $row_chat[0];
/* if the message sender/recipient is same as before, hidden the profile image */
$shouldHiddenProfileIcon = $messageSender === $cacheSender;
/* if the message recipient is myself, show the message on right */
$isMessageSentByMyself = $recipient !== $messageSender;
?>
                    <?php if ($isMessageSentByMyself): ?>

                    <div class="row-chat right column-style">
                        <div class="row-style mr0">
                            <div class="item-chat chat-on-right">
                                <p><?php echo $messageContext/* Message context */ ?></p>
                            </div>
                            <p class="detail"><?php if ($readed) {
    echo ('既読');
}
?> <?php echo $messageTime; ?></p>
                        </div>
                        <img src="assets/img/user.png" <?php if ($shouldHiddenProfileIcon) {
    echo ('style="visibility: hidden;"');
}
?>>
                    </div>

                    <?php else: /* if ($isMessageSentByMyself) */?>

	                    <div class="row-chat column-style">
	                        <img src="assets/img/user.png" <?php if ($shouldHiddenProfileIcon) {
        echo ('style="visibility: hidden;"');
    }
    ?>>
	                        <div class="row-style mr0">
	                            <div class="item-chat chat-on-left">
	                                <p><?php echo $messageContext/* Message context */ ?></p>
	                            </div>
	                            <p class="detail"><?php echo $messageTime; ?></p>
	                        </div>
	                    </div>

	                    <?php endif; /* if ($isMessageSentByMyself) */?>
                    <?php $cacheSender = $messageSender; /* Update cache */?>
                    <?php endforeach; /* foreach ($service->GetChats() as $row_chat): */?>
                    <?php endif; /* if ($isServiceAvailable): */?>
                </div>
                <form method="POST" class="column-style">
                    <input type="text" name="message" class="mr12" placeholder="メッセージを入力" autocomplete="off" required <?php if (!$isServiceAvailable) {
    echo ('disabled');
}
?>>
                    <button name="send_chat" <?php if (!$isServiceAvailable) {echo ('disabled');}
?>><i class="fas fa-paper-plane fa-fw"></i></button>
                </form>
            </div>
        </div>
     </div>

     <script>
        // chat area should be starts scrolling from bottom
        var chat_container = document.getElementById('scrollable');
        chat_container.scrollIntoView(false);
     </script>
 </body>

</html>
