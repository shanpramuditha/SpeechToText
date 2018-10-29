<?php
/**
 * Created by PhpStorm.
 * User: kavindu chamiran
 * Date: 29/10/18
 * Time: 4:26 PM
 */

function get_from_mongodb($query_text){
    $filter = ['transcript' => ['$regex' => ".*" . $query_text . ".*"]];
    $options = [
        'projection' => [
            '_id' => 0,
            'name' => 1,
            'filename' => 1,
            'transcript' => 1
        ]
    ];

    $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");

    $query = new MongoDB\Driver\Query( $filter, $options );

    return $manager->executeQuery("eduscope.transcripts", $query);
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <div>
        <h1 style="text-align: center">Search results</h1>
    </div>
    <h1>
        <?php
        if (isset($_POST['query'])) {
            $query = $_POST['query'];

            $no_results = true;

            $results = get_from_mongodb($query);

            foreach($results as $document) {
                $no_results = false;
                print_r($document->name. " ");
                print_r($document->transcript);
            }

            if ($no_results) {
                echo ('<h3 style="text-align: center">NO RESULTS FOUND!</h3>');
            }
        }
        ?>
    </h1>
</body>
</html>
