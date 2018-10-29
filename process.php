<?php
    session_start();

    $convertData = array(
        "input" => array(
            array(
                "type" => "remote",
                "source" => $_SESSION['localFile']
            )
        ),
        "conversion" => array(
            array(
                "category" => "audio",
                "target" => "wav",
                "options" => array(
                    "channels" => "mono",
                    "frequency" => 8000
                )
            )
        )
    );
    $payload = json_encode($convertData);

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api2.online-convert.com/jobs",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => array(
            "Cache-Control: no-cache",
            "Content-Type: application/json",
            "Host: api2.online-convert.com",
            "Postman-Token: d7f880a5-5d71-48fa-b943-1fb659802c3d",
            "x-oc-api-key: 2d33443f66afe12798fd754531b39689"
        ),
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
// var_dump($err);
// var_dump($response);
// exit;
    if ($err) {
        echo "ERROR: Convert Failed! " . $err;
    } else {
        $res = json_decode($response, true);
        $_SESSION['id'] = $res['id'];
    }

    if (isset($_SESSION['id'])){
            $id = $_SESSION['id'];
    }else{
    //var_dump($_SESSION['id']);
    //exit;
        header("Location: index.html");
        die();
    }
?>

<!doctype html>
<html lang="en">
    <head>
    	<meta charset="utf-8">
    	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <title>Converting Meeting Record To Text</title>
    </head>
    <body>
        <div class="container">
        	<div class="py-5 text-center">
		         <img class="d-block mx-auto mb-4" src="images/wertu.png" alt="" height="200"> 
		        <h2>Meeting Minutes Recorder</h2>
		        <p id="msg" class="lead">Converting Meeting Recording To Text</p>
		    </div>
		</div>
		<div id="step1" class="container">
			<div class="py-5 text-center">
				<div class="row">
					<div class="col-md-12">
						<img id="loader_image" class="d-block mx-auto" src="images/loader.gif" height="300px">
						File ID : <?php echo $id; ?>
					</div>
				</div>
			</div>
		</div>
        <script src="js/jquery-3.3.1.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
		<script type="text/javascript">
			// Check for download URL
			myVar = setInterval(checkStatus, 5000);

			function checkStatus(){
				$.ajax({
					type:'post',
					url: 'checkconvert.php',
					data: {fileId: '<?php echo $id; ?>'},
					dataType: 'json',
					success: function(data){
						if (data.status === "OK"){
							clearInterval(myVar);
							$('#msg').text("Uploading file to convert.");
							processFile(data.url);
						}
						if (data.status === "ERROR"){
							alert ("Converting Error! Please Retry.");
							window.location.href = "index.html";
						}
					}
				});
			}

			function processFile(url) {
				$.ajax({
					type:'post',
					url: 'processfile.php',
					data: {url: url},
					dataType: 'json',
					success: function(data){
						if (data.status === "OK"){
							$('#msg').text("Converting To Text. This may take longer than usual. Please do not refresh the page.");
							//$("#loader_image").attr("src","images/process.png");
							window.location.href = "textapi.php";
						}
						if (data.status === "ERROR"){
							alert ("Converting Error! Please Retry.");
							window.location.href = "index.html";
						}
					}
				});
			}
		</script>
    </body>
</html>