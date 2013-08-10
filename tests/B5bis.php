<?php

if (!isset($_POST['data']) or empty($_POST['data'])) {
    echo 'error';
} else {
    $utils = new Nuxeo\Utilities\Utilities();
    $utils->getFileContent($_POST['data']);
}
