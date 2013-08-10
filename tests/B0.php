<!DOCTYPE html>
<html>
    <head>
        <title>B1 test php Client</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <link rel="stylesheet" media="screen" type="text/css" title="design" href="/tests/design.css"/>
    </head>
    <body>
        <?php include 'nav.php' ?>
        <pre>
          Execute a <code>SELECT * FROM Document ORDER BY ecm:path</code> query to Nuxeo and print all the
          document properties. This is to ease testing in the other tests.
        </pre>
        <hr/>
        <?php

        $configuration = NAPC\Configuration::getInstance();
        $client = new Nuxeo\PhpAutomationClient($configuration->getUrl(false));
        $session = $client->getSession($configuration->getUsername(), $configuration->getPassword());
        $answer = $session->newRequest("Document.Query")
                  ->set('params', 'query', "SELECT * FROM Document ORDER BY ecm:path")
                  ->setSchema('*')
                  ->sendRequest();

        $documents = $answer->getDocumentList();
        ?>
        <table>
            <thead>
            <tr>
                <th>uid</th>
                <th>Path</th>
                <th>Type</th>
                <th>State</th>
                <th>Title</th>
                <th>Property 1</th>
                <th>Property 2</th>
                <th>Download as PDF</th>
            </tr>
            </thead>
            <tbody>
            <?php
            /** @var \Nuxeo\Document\Document $document */
            foreach ($documents as $document) {
                ?>
                <tr>
                    <td>
                        <pre><?= $document->getUid() ?></pre>
                    </td>
                    <td>
                        <pre><?= $document->getPath() ?></pre>
                    </td>
                    <td>
                        <pre><?= $document->getType() ?></pre>
                    </td>
                    <td>
                        <pre><?= $document->getState() ?></pre>
                    </td>
                    <td>
                        <pre><?= $document->getTitle() ?></pre>
                    </td>
                    <td>
                        <pre><?= $document->getProperty('dc:description') ?></pre>
                    </td>
                    <td>
                        <pre><?= $document->getProperty('dc:creator') ?></pre>
                    </td>
                    <td>
                        <form id="test" action="../tests/B5bis.php" method="post">
                            <input type="hidden" name="data" value="<?= $document->getPath() ?>"/>
                            <input type="submit" value="download"/>
                        </form>
                    </td>
                </tr>
            <?php
            }
            ?>
            </tbody>
        </table>
    </body>
</html>
