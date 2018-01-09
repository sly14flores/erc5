<?php

$_POST = json_decode(file_get_contents('php://input'), true);

$dir = "../cache";

require_once '../classes/erc5.php';
require_once 'usage.php';

$erc5 = new eRC5($_POST['km'],$_POST['rounds']);
$time_start = microtime(true);
$enc = $erc5->encrypt($_POST['text']);
$time_end = microtime(true);

// $usage = get_server_cpu_usage();
exec('wmic cpu get LoadPercentage', $p);

$execution_time = $time_end - $time_start;
$output = '<span class="info-response">Execution Time: '.$execution_time.' seconds</span>'."\r\n";
$output .= '<span class="info-response">Processor Usage: '.$p[1].'%</span>'."\r\n";

file_put_contents("$dir/erc5.txt",$output,FILE_APPEND);

echo json_encode($enc);

?>