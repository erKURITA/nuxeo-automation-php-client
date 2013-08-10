<?php

require_once 'config.php';

$page = basename($_GET['site']);

$test = 'tests/' . $page . '.php';;
if (file_exists($test)) {
    include($test);
} else {
    header("HTTP/1.0 404 Not Found");
}
