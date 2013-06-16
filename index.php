<?php
header('Content-Type: text/html; charset=utf-8');

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/config.php';

echo '<h1>Tweelinks</h1>';

$tweeLink = new Tweelink\Tweelink($config);

$time_start = microtime(true);
$tweeLink ->cacheNewTweets();
$time_end = microtime(true);
echo '<h2>Fetching Twitter API in '.($time_end - $time_start).'s</h2>';

$time_start = microtime(true);
$tweeLink ->displayCacheContent();
$time_end = microtime(true);

echo '<h2>Fetching/Displaying the content of the database in '.($time_end - $time_start).'s </h2>';