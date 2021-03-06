<?php

$_POST = json_decode(file_get_contents('php://input'), true);

$dir = "../cache";

require_once '../classes/rc5.php';
require_once 'usage.php';

$rc5 = new RC5();

switch ($_GET['m']) {
	
	case "encrypt":
	
	$output = '<span class="success-response">...</span>'."\r\n";
	$output .= '<span class="success-response">ENCRYPTION</span>'."\r\n";
	$output .= '<span class="success-response">...</span>'."\r\n";
	file_put_contents("$dir/rc5.txt",$output);
	
	$time_start = microtime(true);

	for ($i=1; $i<=$_POST['rounds']; $i++) {

	$output = '<span class="success-response">Round '.$i.':</span>'."\r\n";
	$enc = $rc5->RC5enc($_POST['text'],$_POST['km']);
	$output .= '<span class="success-response">...</span>'."\r\n";

	// if ($i == 1) file_put_contents("$dir/rc5.txt",$output);
	// else file_put_contents("$dir/rc5.txt",$output,FILE_APPEND);
	file_put_contents("$dir/rc5.txt",$output,FILE_APPEND);

	};

	$time_end = microtime(true);

	exec('wmic cpu get LoadPercentage', $p);

	$execution_time = $time_end - $time_start;
	$output = '<span class="info-response">Execution Time: '.$execution_time.' seconds</span>'."\r\n";
	$output .= '<span class="info-response">Processor Usage: '.$p[1].'%</span>'."\r\n";

	file_put_contents("$dir/rc5.txt",$output,FILE_APPEND);
	
	break;
	
	case "decrypt":
	
	$output = '<span class="success-response">...</span>'."\r\n";
	$output .= '<span class="success-response">DECRYPTION</span>'."\r\n";
	$output .= '<span class="success-response">...</span>'."\r\n";
	file_put_contents("$dir/rc5.txt",$output);
	
	$time_start = microtime(true);

	for ($i=$_POST['rounds']; $i>=0; $i--) {

	$output = '<span class="success-response">Round '.$i.':</span>'."\r\n";
	$enc = $rc5->RC5enc($_POST['text'],$_POST['km']);
	$output .= '<span class="success-response">...</span>'."\r\n";

	// if ($i == 1) file_put_contents("$dir/rc5.txt",$output);
	// else file_put_contents("$dir/rc5.txt",$output,FILE_APPEND);
	if ($i > 0) file_put_contents("$dir/rc5.txt",$output,FILE_APPEND);

	};

	$time_end = microtime(true);

	exec('wmic cpu get LoadPercentage', $p);

	$execution_time = $time_end - $time_start;
	$output = '<span class="info-response">Execution Time: '.$execution_time.' seconds</span>'."\r\n";
	$output .= '<span class="info-response">Processor Usage: '.$p[1].'%</span>'."\r\n";

	file_put_contents("$dir/rc5.txt",$output,FILE_APPEND);

	break;

}

?>