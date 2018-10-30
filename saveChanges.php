<?php
/**
 * Created by PhpStorm.
 * User: kavindu
 * Date: 30/10/18
 * Time: 3:01 PM
 */
function update_mongodb($filename, $text){
    $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");

    try {
        $bulk = new MongoDB\Driver\BulkWrite();
        $bulk->update(
            ["filename" => $filename],
            ['$set' =>
                ["transcript" => $text]
            ]
        );

        $manager->executeBulkWrite("eduscope.transcripts", $bulk);
    } catch(MongoDB\Driver\Exception $e) {
        echo $e;
    }
}

$filename = $_POST['filename'];
$text = $_POST['transcript'];

update_mongodb($filename, $text);

header('Location: index.php');
?>