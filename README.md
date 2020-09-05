# ChatApp
A PHP-Based Simple Chat Web App.

![IMAGE](chatsapp.png)

# Implementation of HttpRequest

What I thought is that, could I make this more simpler?

```php
if (isset($_POST['trigger_query'])) {
  // Request processes...
}
```

This implementation is quite simple, but it could make a source complicated, and less-legibility.  
So I've implemented a simple class that provides HttpRequest-Handling more easily, and reliable.  
It'll look like:

```php
/* Parameters: Method, TriggerQuery, Callback-Function */

// Execute the function in a specific namespace,
// if the query 'login' is executed. with the HTTP_POST.
HTTP_HANDLER\RegisterHttpRequestHandler(HTTP_POST, 'login', 'APP\ExecAuthenticate');

// We can execute anonymous function as well.
HTTP_HANDLER\RegisterHttpRequestHandler(HTTP_POST, 'login', function() {
  echo ('Are you tryna login?');
});
```

Very easy, good-looks.

# Implementation of Error and Exception Handling

# Implementation of Chatting

# Database Schema

# About The Security
