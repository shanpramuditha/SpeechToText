<?php
session_start();
$url = $_GET['url'];
$_SESSION['localFile'] = $url;
echo "done";