<?php

require_once 'rc5.php';

// RC5::rc5_init(array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15));
// $enc = (RC5::rc5_encrypt( array(65) ));
// $dec = (RC5::rc5_decrypt( $enc )); 
// var_dump($enc,$dec);

$enc = RC5::RC5enc("Lorem","1234567891234567");
$dec = RC5::RC5enc($enc,"1234567891234567");

var_dump($enc);
var_dump($dec);

?>