<?php

class eRC5 {
	
	private $charBin, $binChar, $km, $r, $xor_value;
	
	function __construct($km,$r) {
		
		$this->charBin = array("0"=>"0000","1"=>"0001","2"=>"0010","3"=>"0011","4"=>"0100","5"=>"0101","6"=>"0110","7"=>"0111","8"=>"1000","9"=>"1001","A"=>"1010","B"=>"1011","C"=>"1100","D"=>"1101","E"=>"1110","F"=>"1111");
		$this->binChar = array("0000"=>"0","0001"=>"1","0010"=>"2","0011"=>"3","0100"=>"4","0101"=>"5","0110"=>"6","0111"=>"7","1000"=>"8","1001"=>"9","1010"=>"A","1011"=>"B","1100"=>"C","1101"=>"D","1110"=>"E","1111"=>"F");
		
		$this->km = strtoupper(base_convert($km,10,16));
		$this->r = $r;
		
	}
	
	private function kmXor($str) {

		$charBin = $this->charBin;
		$binChar = $this->binChar;		

		$str_hex = $str;
		$km_hex = $this->km;
		
		if (strlen($km_hex) < strlen($str_hex)) {
			$km_hex = str_pad($km_hex,strlen($str_hex),"0",STR_PAD_LEFT);	
		}	

		$arr_pw_hex = str_split($str_hex);
		$arr_km_hex = str_split($km_hex);

		$arr_xor = [];
		foreach ($arr_pw_hex as $i => $h) {
			
			$pBin = $charBin[$arr_pw_hex[$i]];
			$kBin = $charBin[$arr_km_hex[$i]];
			
			$arr_pBin = str_split($pBin);
			$arr_kBin = str_split($kBin);
			
			$xorString = "";
			foreach ($arr_pBin as $ii => $hh) {
				$xor = intval($arr_pBin[$ii])^intval($arr_kBin[$ii]);
				$xorString .= $xor;
			}

			$arr_xor[] = $binChar[$xorString];
			
		}
		
		return join("",$arr_xor);
		
	}
	
	private function divisions($xor) {
		
		$xor_arr = str_split($xor);

		$divisions = [];
		$division = "";		
		
		$c = 0;
		$d = ceil(count($xor_arr)/4);
		foreach ($xor_arr as $i => $value) {
			if ($c == $d) {
				$divisions[] = $division;
				$division = "";
				$c = 0;				
			}
			$division .= $value;
			$c++;
			if (count($xor_arr)==($i+1)) $divisions[] = $division;
		}
		
		return $divisions;
		
	}
	
	public function encrypt($str) {
		
		$results = [];
		
		$str_hex = strtoupper(bin2hex($str));
	
		$this->xor_value = eRC5::kmXor($str_hex);
		$xor = $this->xor_value;
		
		$divisions = eRC5::divisions($xor);

		// Left Divisions	
		$A = $divisions[0];
		$B = $divisions[1];
		
		// Right Divisions
		$C = $divisions[2];
		$D = $divisions[3];		

		$dir = "../cache";		
		
		$r = $this->r;
		for ($i=1; $i<=$r; $i++) {
			
			$output = '<span class="success-response">Round '.$i.':</span>'."\r\n";
			// $time_start = microtime(true);			
			$abcd = eRC5::processEnc($A,$B,$C,$D);
			// $time_end = microtime(true);
			// $execution_time = $time_end - $time_start;			
			$Ar = $abcd[0];
			$Br = $abcd[1];
			$Cr = $abcd[2];
			$Dr = $abcd[3];
			$A = $Ar;
			$B = $Br;
			$C = $Cr;
			$D = $Dr;			
			$output .= '<span class="success-response">Encrypted Text: '.$Ar.$Br.$Cr.$Dr.'</span>'."\r\n";
			// $output .= '<span class="info-response">Execution Time: '.$execution_time.' seconds</span>'."\r\n";
			$output .= '<span class="success-response">...</span>'."\r\n";
			if ($i == 1) file_put_contents("$dir/erc5.txt",$output);
			else file_put_contents("$dir/erc5.txt",$output,FILE_APPEND);			
			
			// $data = array("round"=>$i,"encrypted"=>$A.$B.$C.$D,"time"=>$execution_time);
			// $results[] = $data;
			
			if ($i == 12) break;
			
		};
		
		$Ar14 = $Ar;
		$Br14 = $Br;
		$Cr14 = $Cr;
		$Dr14 = $Dr;		
		
		++$i;		
		if ($i == 13) {
		
			$output = '<span class="success-response">Round '.$i.':</span>'."\r\n";		
			// $time_start = microtime(true);		
			$Ar13 = eRC5::strXor($Ar,$Cr);
			$Br13 = eRC5::strXor($Br,$Cr);
			$Cr13 = eRC5::strXor($Br,$Dr);
			$Dr13 = eRC5::strXor($Dr,$Br);
			// $time_end = microtime(true);
			// $execution_time = $time_end - $time_start;
			$output .= '<span class="success-response">Encrypted Text: '.$Ar13.$Br13.$Cr13.$Dr13.'</span>'."\r\n";
			// $output .= '<span class="info-response">Execution Time: '.$execution_time.' seconds</span>'."\r\n";
			$output .= '<span class="success-response">...</span>'."\r\n";
			
			// $data = array("round"=>$i,"encrypted"=>$Ar13.$Br13.$Cr13.$Dr13,"time"=>$execution_time);
			// $results[] = $data;
			
			file_put_contents("$dir/erc5.txt",$output,FILE_APPEND);				
			
		};

		++$i;
		if ($i == 14) {
			
			$output = '<span class="success-response">Round '.$i.':</span>'."\r\n";
			// $time_start = microtime(true);			
			$Ar14 = $Cr;
			$Br14 = $Ar;
			$Cr14 = $Dr;
			$Dr14 = $Br;
			// $time_end = microtime(true);
			// $execution_time = $time_end - $time_start;			
			$output .= '<span class="success-response">Encrypted Text: '.$Ar14.$Br14.$Cr14.$Dr14.'</span>'."\r\n";
			// $output .= '<span class="info-response">Execution Time: '.$execution_time.' seconds</span>'."\r\n";
			$output .= '<span class="success-response">...</span>'."\r\n";
			
			// $data = array("round"=>$i,"encrypted"=>$Ar14.$Br14.$Cr14.$Dr14,"time"=>$execution_time);
			// $results[] = $data;				
			
			file_put_contents("$dir/erc5.txt",$output,FILE_APPEND);
			
		};
		
		return $Ar14.$Br14.$Cr14.$Dr14;
		// return $results;

	}
	
	public function decrypt($enc) {
		
		$abcd = eRC5::processDec("252A","6EF7","95B3","32E7");
		// var_dump($abcd);
		
		exit();
		
		// echo "Decryption\n";
		
		$divisions = eRC5::divisions($enc);
		
		$dir = "../cache";		
		
		$r = $this->r;
		$i = $r;
		
		if ($i == 14) {
			
			$output = '<span class="success-response">Round '.$i.':</span>'."\r\n";
			// $time_start = microtime(true);				
			$Ar14 = $divisions[0];
			$Br14 = $divisions[1];
			$Cr14 = $divisions[2];
			$Dr14 = $divisions[3];
			$Ar = $divisions[0];
			$Br = $divisions[1];
			$Cr = $divisions[2];
			$Dr = $divisions[3];			
			// $time_end = microtime(true);
			// $execution_time = $time_end - $time_start;			
			$output .= '<span class="success-response">Decrypted Text: '.$Ar14.$Br14.$Cr14.$Dr14.'</span>'."\r\n";
			// $output .= '<span class="info-response">Execution Time: '.$execution_time.' seconds</span>'."\r\n";
			$output .= '<span class="success-response">...</span>'."\r\n";
			
			// $data = array("round"=>$i,"encrypted"=>$Ar14.$Br14.$Cr14.$Dr14,"time"=>$execution_time);
			// $results[] = $data;				
			
			file_put_contents("$dir/erc5.txt",$output,FILE_APPEND);				
			
			--$i;
			
		};
		
		if ($i == 13) {
			
			$output = '<span class="success-response">Round '.$i.':</span>'."\r\n";
			// $time_start = microtime(true);				
			$Ar13 = eRC5::strXor($Ar,$Cr);
			$Br13 = eRC5::strXor($Br,$Cr);
			$Cr13 = eRC5::strXor($Br,$Dr);
			$Dr13 = eRC5::strXor($Dr,$Br);
			// $time_end = microtime(true);
			// $execution_time = $time_end - $time_start;			
			$output .= '<span class="success-response">Decrypted Text: '.$Ar13.$Br13.$Cr13.$Dr13.'</span>'."\r\n";
			// $output .= '<span class="info-response">Execution Time: '.$execution_time.' seconds</span>'."\r\n";
			$output .= '<span class="success-response">...</span>'."\r\n";
			
			// $data = array("round"=>$i,"encrypted"=>$Ar13.$Br13.$Cr13.$Dr13,"time"=>$execution_time);
			// $results[] = $data;				
			
			file_put_contents("$dir/erc5.txt",$output,FILE_APPEND);				
			
			--$i;
			
		};		
		
		if ($i == 12) {
			
			$output = '<span class="success-response">Round '.$i.':</span>'."\r\n";
			// $time_start = microtime(true);			
			$Ar12 = $Br14;
			$Br12 = $Dr14;
			$Cr12 = $Ar14;
			$Dr12 = $Cr14;
			// $time_end = microtime(true);
			// $execution_time = $time_end - $time_start;			
			$output .= '<span class="success-response">Decrypted Text: '.$Ar12.$Br12.$Cr12.$Dr12.'</span>'."\r\n";
			// $output .= '<span class="info-response">Execution Time: '.$execution_time.' seconds</span>'."\r\n";
			$output .= '<span class="success-response">...</span>'."\r\n";
			
			// $data = array("round"=>$i,"encrypted"=>$Ar12.$Br12.$Cr12.$Dr12,"time"=>$execution_time);
			// $results[] = $data;				
			
			file_put_contents("$dir/erc5.txt",$output,FILE_APPEND);				
			
			--$i;
			
		};
		
		$A = $Ar12;
		$B = $Br12;
		$C = $Cr12;
		$D = $Dr12;
		
		for ($i=11; $i>=1; --$i) {
			
			/* if ($i==9) {
				var_dump($A);
				var_dump($B);
				var_dump($C);
				var_dump($D);
			}; */
			
			$output = '<span class="success-response">Round '.$i.':</span>'."\r\n";
			// $time_start = microtime(true);			
			$abcd = eRC5::processDec($A,$B,$C,$D);
			// $time_end = microtime(true);
			// $execution_time = $time_end - $time_start;			
			$Ar = $abcd[0];
			$Br = $abcd[1];
			$Cr = $abcd[2];
			$Dr = $abcd[3];
			$A = $abcd[0];
			$B = $abcd[1];
			$C = $abcd[2];
			$D = $abcd[3];
			// $time_end = microtime(true);
			// $execution_time = $time_end - $time_start;			
			$output .= '<span class="success-response">Decrypted Text: '.$A.$B.$C.$D.'</span>'."\r\n";
			// $output .= '<span class="info-response">Execution Time: '.$execution_time.' seconds</span>'."\r\n";
			$output .= '<span class="success-response">...</span>'."\r\n";
			
			// $data = array("round"=>$i,"encrypted"=>$A.$B.$C.$D,"time"=>$execution_time);
			// $results[] = $data;				
			
			file_put_contents("$dir/erc5.txt",$output,FILE_APPEND);			
			
		};
		
		/* $Br2_lr = eRC5::leftRotate($Br3);
		$Br2 = eRC5::strXor($Ar3,$Br2_lr);
		$Ar2_rr = eRC5::rightRotate($Ar3);
		$Ar2 = eRC5::strXor($Ar2_rr,$Br2);

		$Cr2_n = eRC5::negateStr($Cr3);
		$Dr2_lr = eRC5::leftRotate($Dr3);
		$Dr2_x = eRC5::strXor($Cr2_n,$Dr2_lr);		
		$Dr2 = eRC5::negateStr($Dr2_x);
		
		$Cr2_rr = eRC5::rightRotate($Cr3);
		$Cr2 = eRC5::strXor($Cr2_rr,$Dr2_x);		
		
		// echo "\n";
		// echo "Round 2:\n";
		// echo "A. $Ar2\n";
		// echo "B. $Br2\n";
		// echo "C. $Cr2\n";
		// echo "D. $Dr2\n";

		$Br1_lr = eRC5::leftRotate($Br2);
		$Br1 = eRC5::strXor($Ar2,$Br1_lr);
		$Ar1_rr = eRC5::rightRotate($Ar2);
		$Ar1 = eRC5::strXor($Ar1_rr,$Br1);
		
		$Dr1_lr = eRC5::leftRotate($Dr2);
		$Cr1_n = eRC5::negateStr($Cr2);
		$Dr1_x = eRC5::strXor($Cr1_n,$Dr1_lr);
		$Dr1 = eRC5::negateStr($Dr1_x);

		$Cr1_rr = eRC5::rightRotate($Cr2);
		$Cr1 = eRC5::strXor($Cr1_rr,$Dr1_x);		

		// echo "\n";
		// echo "Round 1:\n";
		// echo "A. $Ar1\n";
		// echo "B. $Br1\n";
		// echo "C. $Cr1\n";
		// echo "D. $Dr1\n";

		$B_lr = eRC5::leftRotate($Br1);
		$B = eRC5::strXor($Ar1,$B_lr);
		$A_rr = eRC5::rightRotate($Ar1);
		$A = eRC5::strXor($A_rr,$B);
		
		$C_n = eRC5::negateStr($Cr1);
		$D_lr = eRC5::leftRotate($Dr1);
		$D_x = eRC5::strXor($C_n,$D_lr);
		$D = eRC5::negateStr($D_x);
		
		$C_rr = eRC5::rightRotate($Cr1);
		$C = eRC5::strXor($C_rr,$D_x);
		
		// echo "\n";
		// echo "A. $A\n";
		// echo "B. $B\n";
		// echo "C. $C\n";
		// echo "D. $D\n";	 */	

		$pt = $A.$B.$C.$D;
		
		$dec = eRC5::kmXor($pt);
		
		return hex2bin($dec);
		
	}
	
	private function processEnc($A,$B,$C,$D) {
		
		$Ar = eRC5::strXor($A,$B);
		$Ar = eRC5::leftRotate($Ar);

		$Br = eRC5::strXor($Ar,$B);
		$Br = eRC5::rightRotate($Br);
		
		$Dr_n = eRC5::negateStr($D);
		
		$Cr = eRC5::strXor($C,$Dr_n);
		$Cr = eRC5::leftRotate($Cr);
		
		$Cr_n = eRC5::negateStr($Cr);
		$Dr = eRC5::strXor($Cr_n,$Dr_n);
		
		$Dr = eRC5::rightRotate($Dr);
		
		return array($Ar,$Br,$Cr,$Dr);
		
	}
	
	private function processDec($Ar3,$Br3,$Cr3,$Dr3) {

		// $Br2_lr = eRC5::leftRotate($Br3);
		// $Br2 = eRC5::strXor($Ar3,$Br2_lr);
		// $Ar2_rr = eRC5::rightRotate($Ar3);
		// $Ar2 = eRC5::strXor($Ar2_rr,$Br2);

		$Cr2_n = eRC5::negateStr($Cr3); //var_dump($Cr2_n);
		$Dr2_lr = eRC5::leftRotate($Dr3); //var_dump($Dr2_lr);
		$Dr2_x = eRC5::strXor($Cr2_n,$Dr2_lr); // var_dump($Dr2_x);
		// $Dr2 = eRC5::negateStr($Dr2_x);

		// $Cr2_rr = eRC5::rightRotate($Cr3);
		// $Cr2 = eRC5::strXor($Cr2_rr,$Dr2_x);
		
		// $result = array($Ar2,$Br2,$Cr2,$Dr2);
		// var_dump($result);
		// return array($Ar2,$Br2,$Cr2,$Dr2);

	}
	
	private function strXor($str1,$str2) {
		var_dump(hexdec($str1));
		var_dump(hexdec($str2));
		$str = dechex(hexdec($str1) ^ hexdec($str2));
		var_dump($str);
		return strtoupper($str);

	}	
	
	private function strXor_($str1,$str2) {
	
		$str1_arr = str_split($str1);
		$str2_arr = str_split($str2);

		$str = "";
		foreach ($str1_arr as $k => $f) {
			$charXor = eRC5::charXor($str1_arr[$k],$str2_arr[$k]);
			$str .= $charXor;	
		}		
		
		return $str;
		
	}	
	
	private function charXor($char1,$char2) {
		
		$charBin = $this->charBin;
		$binChar = $this->binChar;				
		
		$char1Bin = $charBin[$char1];
		$char2Bin = $charBin[$char2];
		
		$arr1 = str_split($char1Bin);
		$arr2 = str_split($char2Bin);
		
		$charBin = "";
		foreach ($arr1 as $k => $value) {
			$xor = intval($arr1[$k])^intval($arr2[$k]);
			$charBin .= $xor;	
		}
		
		return $binChar[$charBin];
		
	}
	
	private function leftRotate($str) {

		$charBin = $this->charBin;
		$binChar = $this->binChar;		
		
		$str_bin = "";		
		$str_arr = str_split($str);				
		foreach ($str_arr as $k => $value) {
			
			$str_bin .= $charBin[$value];
			
		}

		$str_bin_arr = str_split($str_bin);
		
		$new_str_bin = "";
		$str_bin_first_char = "";
		foreach ($str_bin_arr as $k => $value) {
			if ($k == 0) {
				$str_bin_first_char = $value;
				continue;
			}			
			$new_str_bin .= $value;
		}
		$new_str_bin .= $str_bin_first_char;

		$new_str_bin_arr = str_split($new_str_bin);
		$divisions = [];
		$division = "";		
		
		$c = 0;
		foreach ($new_str_bin_arr as $i => $value) {
			if ($c == 4) {
				$divisions[] = $division;
				$division = "";
				$c = 0;				
			}
			$division .= $value;
			$c++;
			if (count($new_str_bin_arr)==($i+1)) $divisions[] = $division;
		}
		
		$new_str = "";
		foreach ($divisions as $division) {
			$new_str .= $binChar[$division];
		}
		
		return $new_str;
		
	}
	
	private function rightRotate($str) {
		
		$charBin = $this->charBin;
		$binChar = $this->binChar;			
		
		$str_bin = "";		
		$str_arr = str_split($str);				
		foreach ($str_arr as $k => $value) {
			
			$str_bin .= $charBin[$value];
			
		}

		$str_bin_arr = str_split($str_bin);
		$str_bin_arr_last_index = count($str_bin_arr)-1;

		$new_str_bin = $str_bin_arr[$str_bin_arr_last_index];
		foreach ($str_bin_arr as $k => $value) {
			if ($k == $str_bin_arr_last_index) break;	
			$new_str_bin .= $value;
		}

		$new_str_bin_arr = str_split($new_str_bin);
		$divisions = [];
		$division = "";		
		
		$c = 0;
		foreach ($new_str_bin_arr as $i => $value) {
			if ($c == 4) {
				$divisions[] = $division;
				$division = "";
				$c = 0;				
			}
			$division .= $value;
			$c++;
			if (count($new_str_bin_arr)==($i+1)) $divisions[] = $division;
		}

		$new_str = "";
		foreach ($divisions as $division) {
			$new_str .= $binChar[$division];
		}
		
		return $new_str;
		
	}	

	private function negateStr($str) {

		$charBin = $this->charBin;
		
		$str_arr = str_split($str);

		$str_n = "";
		foreach ($str_arr as $key => $value) {
			$str_n .= eRC5::negateChar($charBin[$value]);
		}
		
		return $str_n;

	}
	
	private function negateChar($char) {
		
		$binChar = $this->binChar;
		
		$char_arr = str_split($char);

		$char_n = "";
		foreach ($char_arr as $k => $value) {
			$n = !intval($value);
			$n = ($n)?"1":"0";
			$char_n .= $n;
		}

		return $binChar[$char_n];
		
	}

	public function getXorValue() {
		
		return $this->xor_value;
		
	}
	
}

?>