<?php
// public/index.php

if (!function_exists('frankenphp_handle_request')) {
    // This function is defined in the frankenphp extension
    // It will call the given handler when a request is received
    // and return true if the worker should continue to run
    // or false if it should stop
    function frankenphp_handle_request(callable $handler): bool
    {
        throw new Error('frankenphp extension not loaded');
    }
}

// Prevent worker script termination when a client connection is interrupted
ignore_user_abort(true);

// Boot your app
// require __DIR__ . '/vendor/autoload.php';

$nbRequests = 0;

define('FRANKENPHP_WORKER_ID', uniqid());
define('FRANKENPHP_MAX_REQUESTS', intval(getenv('MAX_REQUESTS')));

// load CI entrypoint
include(__DIR__ . '/../index.php');

do {

    $handler = static function () use ($nbRequests) {
        $_SERVER['WORKER_ID'] = FRANKENPHP_WORKER_ID;
        $_SERVER['NB_REQUESTS'] = $nbRequests;

        // Make sure status code is 200 OK by default
        set_status_header(200, 'OK');

        ci_handle_request();
    };

    $running = \frankenphp_handle_request($handler);

    // Call the garbage collector to reduce the chances of it being triggered in the middle of a page generation
    gc_collect_cycles();
} while ($running && ++$nbRequests < FRANKENPHP_MAX_REQUESTS);

// TODO: Implement Cleanup
