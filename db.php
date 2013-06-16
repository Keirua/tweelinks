<?php

require __DIR__.'/config.php';

mysql_connect($config['db']['host'], $config['db']['login'], $config['db']['password']);
mysql_select_db($config['db']['db_name']);