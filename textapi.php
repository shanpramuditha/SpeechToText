<?php
	session_start();
	$_SESSION['url'] = "sdas";
	ini_set('max_execution_time', 0);
	require 'vendor/autoload.php';
	use Exception;
	use Google\Cloud\Speech\SpeechClient;
	use Google\Cloud\Storage\StorageClient;
	use Google\Cloud\Core\ExponentialBackoff;
	if (isset($_SESSION['url'])){
		$text = '';
		$projectId = 'protean-topic-209902';
		$options = [
			'encoding' => 'LINEAR16',
			'sampleRateHertz' => 8000,
		];
		$speech = new SpeechClient([
			'projectId' => $projectId,
			'languageCode' => 'en-US',
			'keyFilePath' => 'key.json',
		]);
		// Fetch the storage object
		$storage = new StorageClient([
			'projectId' => $projectId,
			'keyFilePath' => 'key.json',
		]);
		$object = $storage->bucket("audio-to-text-storage")->object("audio.wav");
		// Create the asyncronous recognize operation
		$operation = $speech->beginRecognizeOperation(
			$object,
			$options
		);
		// Wait for the operation to complete
		$backoff = new ExponentialBackoff(10);
		$backoff->execute(function () use ($operation) {
			$operation->reload();
			if (!$operation->isComplete()) {
				throw new Exception('Job has not yet completed', 500);
			}
		});
		// Print the results
		if ($operation->isComplete()) {
			$results = $operation->results();
			foreach ($results as $result) {
				$alternative = $result->alternatives()[0];
				$text .= $alternative['transcript'];
			}
		}
	}else{
		header("Location: index.php");
		die();
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
		        <p class="lead">Here's your today meeting record</p>
		    </div>
		</div>
		<div id="step1" class="container">
			<div class="py-5 text-center">
				<div class="row">
					<div class="col-md-12">
						<form>
							<div class="form-group">
							    <textarea class="form-control" id="exampleFormControlTextarea1" rows="10"><?php echo $text; ?></textarea>
							 </div>
						</form>
						<br>
						<a href="index.html" class="btn btn-success">Start New Meeting</a>
					</div>
				</div>
			</div>
		</div>
        <script src="js/jquery-3.3.1.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    </body>
</html>