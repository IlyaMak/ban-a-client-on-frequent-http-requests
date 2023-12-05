<?php

session_start();

define('MAX_REQUESTS', 3);
define('MAX_BAN_TIME', 10);

if (!isset($_SESSION['counter']) && !isset($_SESSION['retryTime'])) {
    $_SESSION['counter'] = 1;
    $_SESSION['retryTime'] = time();
}

if (
    $_SESSION['counter'] > MAX_REQUESTS &&
    (isset($_SESSION['retryTime']) &&
    time() - $_SESSION['retryTime'] >= MAX_BAN_TIME)
) {
    $_SESSION['counter'] = 0;
    unset($_SESSION['retryTime']);
}

$_SESSION['counter']++;

if ($_SESSION['counter'] > MAX_REQUESTS) {
    if (!isset($_SESSION['retryTime'])) {
        $_SESSION['retryTime'] = time();
    }
    $remainingBanTime =  $_SESSION['retryTime'] + MAX_BAN_TIME - time();
    echo "You've done too many requests, please wait $remainingBanTime seconds to make new requests";
    http_response_code(429);
    header("Retry-After:" . MAX_BAN_TIME);
} else {
    echo 'Greetings';
}

