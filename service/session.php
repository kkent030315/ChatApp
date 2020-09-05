<?php
namespace SERVICE_SESSION {
    function StartSessionIfNeeded()
    {
        if (!isset($_SESSION) || !session_id()) {
            session_start();
        }
    }

    function DisposeSessionForcibly()
    {
        $_SESSION = array();

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 3600, $params['path']);
        }

        if (session_id()) {
            return session_destroy();
        }

        header("Refresh:1");

        return true;
    }
}
