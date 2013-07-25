<?php
header('Content-Type: text/html; charset=utf-8');

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/config.php';

$tweeLink = new Tweelink\Tweelink($config);
$tweeLink ->cacheNewTweets();