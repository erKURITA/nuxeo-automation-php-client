<!DOCTYPE html>
<html>
    <head>
        <title>B5 test php Client</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <link rel="stylesheet" media="screen" type="text/css" title="design" href="/tests/design.css"/>
    </head>
    <body>
        <?php include 'nav.php' ?>
        <br/>

        <form action="/B5" method="post">
            <table style="width: auto">
                <tr>
                    <td><label for="blob_path">Path</label></td>
                    <td>
                        <input type="text" name="path" id="blob_path"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="blob_type">Blob Type</label></td>
                    <td>
                        <input type="text" name="type" id="blob_type"/>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="submit" value="Submit"/>
                    </td>
                </tr>
            </table>
        </form>
<?php

if (isset($_POST) && $_POST != array()) {
    if (!isset($_POST['path'])) {
        echo 'path is empty';
        exit;
    }
    $utils = new \Nuxeo\Utilities\Utilities();
    if (!isset($_POST['type'])) {
        $utils->getFileContent($_POST['path']);
    } else {
        $utils->getFileContent($_POST['path'], $_POST['type']);
    }
}
?>
    </body>
</html>
