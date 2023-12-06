<?php
session_start();

define('MAX_REQUESTS', 10);
define('BAN_TIME_DURATION', 120);
define('ALLOWED_IN_TIME_REQUESTS', 60);

if (!isset($_SESSION['requestTimestamps'])) {
    $_SESSION['requestTimestamps'] = [];
}

$_SESSION['requestTimestamps'] = array_filter(
    $_SESSION['requestTimestamps'],
    'getAllowedInTimeRequests'
);

// "- 1" in this condition to MAX_REQUESTS is needed because the current filtered request is not added yet
if (
    !isset($_SESSION['banTime'])
    && count($_SESSION['requestTimestamps']) >= MAX_REQUESTS - 1
) {
    $_SESSION['banTime'] = time();
}

if (
    isset($_SESSION['banTime'])
    && time() - $_SESSION['banTime'] < BAN_TIME_DURATION
) {
    $remainingBanTime =  $_SESSION['banTime'] + BAN_TIME_DURATION - time();
    echo "You've done too many requests, please wait $remainingBanTime seconds to make new requests";
    http_response_code(429);
    header("Retry-After:" . BAN_TIME_DURATION);
} else {
    $_SESSION['requestTimestamps'][] = time();
    unset($_SESSION['banTime']);
    echo 'Greetings';
}

function getAllowedInTimeRequests(int $requestTime)
{
    return time() - $requestTime < ALLOWED_IN_TIME_REQUESTS;
}
