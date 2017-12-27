<?php

$_POST = json_decode(file_get_contents('php://input'), true);

$dir = "../cache";

require_once '../classes/rc5.php';

$rc5 = new RC5();
$enc = $rc5->encrypt($_POST['text'],$_POST['km']);
var_dump($enc);
file_put_contents("$dir/rc5.txt",'<span class="success-response">'.$enc.'</span>');

?>