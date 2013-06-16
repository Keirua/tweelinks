<?php

require __DIR__.'/config.php';

$db = new PDO('mysql:host='.$config['db']['host'].';dbname='.$config['db']['db_name'], $config['db']['login'], $config['db']['password']);

