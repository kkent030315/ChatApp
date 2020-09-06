<?php
namespace HTTP_HANDLER{
    define('HTTP_POST', 'POST');
    define('HTTP_GET', 'GET');

    /*
     * Registers new http request handler
     * @method HTTP_POST or HTTP_GET
     * @triggerQuery Query parameter to be triggered
     * @function Callback Function for the request
     * @return none
     */
    function RegisterHttpRequestHandler($method, $triggerQuery, $function)
    {
        if ($method === HTTP_POST) {
            if (isset($_POST[$triggerQuery])) {
                call_user_func($function);
            }
        } elseif ($method === HTTP_GET) {
            if (isset($_GET[$triggerQuery])) {
                call_user_func($function);
            }
        }
    }
}
