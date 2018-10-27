<?php
session_start();
$fileId = $_POST['fileId'];
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api2.online-convert.com/jobs/" . $fileId,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
        "Cache-Control: no-cache",
        "Postman-Token: d1352cc2-9b86-4b17-947f-b15cb2fa43fd",
        "x-oc-api-key: 2d33443f66afe12798fd754531b39689"
    ),
));
$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo json_encode(array("status" => "ERROR"));
} else {
    // Extract Download URL
    $res = json_decode($response, true);
    if ($res["status"]["code"] == "completed"){
        echo json_encode(array("status" => "OK", "url" => $res["output"][0]["uri"]));
    }
    if ($res["status"]["code"] == "failed"){
        echo json_encode(array("status" => "ERROR", "url" => ''));
    }
}
?>