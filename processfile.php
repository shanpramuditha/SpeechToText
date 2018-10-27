<?php
    session_start();
    ini_set('max_execution_time', 0);
    require 'vendor/autoload.php';
	use Google\Cloud\Speech\SpeechClient;
	use Google\Cloud\Storage\StorageClient;
	use Google\Cloud\Core\ExponentialBackoff;
    $url = $_POST['url'];
    
    // Download File
    file_put_contents("audio.wav", fopen($url, 'r'));
    // Upload To GCloud
    $projectId = 'protean-topic-209902';
    $bucketName = 'audio-to-text-storage';
    $storage = new StorageClient([
        'projectId' => $projectId,
        'keyFilePath' => 'key.json',
    ]);
    
    $file = fopen("audio.wav", 'r');
    $bucket = $storage->bucket($bucketName);
    $object = $bucket->upload($file, [
        'name' => 'audio.wav'
    ]);
    
    // Make File Public
    try{
    $object = $bucket->object('audio.wav');
    $object->update(['acl' => []], ['predefinedAcl' => 'PUBLICREAD']);
    }catch(Exception $e){
        var_dump($object);
        exit;
    }
    $_SESSION['url'] = $url;
    echo json_encode(array("status" => "OK"));
?>