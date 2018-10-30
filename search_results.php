<?php
$record = $_GET['record'].".wav";

function get_from_mongodb($record){
    $filter = ['filename' => $record];
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

$results = get_from_mongodb($record);

$name = '';
$transcript = '';

foreach($results as $document) {
    $name = $document->name;
    $transcript = $document->transcript;
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <title>Step 3 | Audio To Text Converter</title>
</head>
<body>
<div class="container">
    <div class="py-5 text-center">
        <!--<img class="d-block mx-auto mb-4" src="images/logo.png" alt="" height="200"> -->
        <h2>Meeting Minute Recorder</h2>
        <p class="lead">Here's your <?php echo $name; ?> meeting transcript</p>
    </div>
</div>
<div id="step1" class="container">
    <div class="py-5 text-center">
        <div class="row">
            <div class="col-md-12">
                <br>
                <div>
                    <form action="saveChanges.php" id="submitChanges" method="post">
                        <input type="hidden" name="filename" value="<?php echo $record; ?>" />


                        <div class="form-group">
                            <textarea name="transcript" class="form-control" id="exampleFormControlTextarea1" rows="10"><?php echo $transcript; ?></textarea>
                        </div>
                    </form>

                    <audio controls>
                        <source src="uploads/<?php echo $record; ?>" type="audio/wav">
                    </audio>
                </div>

                <div>
                    <a href="#" onclick="document.getElementById('submitChanges').submit();"
                       class="btn btn-success">Save changes</a>

                    <a href="index.php" class="btn btn-success">Go home</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="js/jquery-3.3.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>