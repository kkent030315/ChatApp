<?php
namespace SERVICE_SESSION {
    /*
     * Start the session if not initiated yet
     */
    function StartSessionIfNeeded()
    {
        if (!isset($_SESSION) || !session_id()) {
            session_start();
        }
    }

    /*
     * Close current session
     * @return bool
     */
    function DisposeSessionForcibly()
    {
        /* Empty the super-global session */
        $_SESSION = array();

        /* Cookies */
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 3600, $params['path']);
        }

        /* Only destroy if the session already initialized */
        if (session_id()) {
            return session_destroy();
        }

        header("Refresh:1");

        return true;
    }
}
