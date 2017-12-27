<?php

$_POST = json_decode(file_get_contents('php://input'), true);

$dir = "../cache";

require_once '../classes/erc5.php';

$erc5 = new eRC5($_POST['km'],$_POST['rounds']);
$enc = $erc5->encrypt($_POST['text']);

?>