<?php

require_once 'classes/erc5.php';

$erc5 = new eRC5("4567812131415165",14);

# 97BA FCA6 B156 C1D8

$x = $erc5->strXor("97BA","B156");

var_dump($x);

$x1 = $erc5->strXor($x,"B156");

var_dump($x1);

/* $str1 = "6A4C";
$str2 = "75CE";

var_dump(hexdec($str1));
var_dump(hexdec($str2));

$str = dechex(hexdec($str1) ^ hexdec($str2));

var_dump($str); */
	
?>