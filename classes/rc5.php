<?php
 
/**
 * @author www.Tutorialspots.com
 * @copyright 2017
 */
 
class RC5 {
	
    public static $S = array();
    public static $r = 12;
    public static $w = 32;
     
    private static function cyclicShiftLeft($array, $positions)
    {
        $temp = array_slice($array, 0, $positions);
        $temp = array_merge(array_slice($array, $positions), $temp);
        return $temp;
    }
     
    private static function ROTL($v,$n,$m=32) {
        $y = sprintf("%0".$m."b",$v);
        $r = str_split($y);
        $r = self::cyclicShiftLeft($r,$n);
        return bindec(implode("",$r)) ;
    }
     
    private static function ROTR($v,$n,$m=32){
        return self::ROTL($v,$m-$n,$m);
    } 
     
    /* Key scheduling */
    public static function rc5_init($K)
    {
       $b = 16;
       $u = self::$w/8;
       $c = max(1, ceil($b/$u));
       $t = 2 * (self::$r+1);
       $L = array();
       $P = 0xb7e15163;
       $Q = 0x9e3779b9;
        
       for($i = 0; $i < $b; $i++)
          $L[$i] = 0;
        
       for($i = $b-1, $L[$c-1] = 0; $i != -1; $i--)
          $L[$i/$u] = ($L[$i/$u] << 8) + $K[$i];
        
       for(self::$S[0] = $P, $i = 1; $i < $t; $i++)
          self::$S[$i] = self::$S[$i-1] + $Q;
          
       $n = max($t,$c);
        
       for($A = $B = $i = $j = $k = 0; $k < 3 * $n; $k++, $i = ($i+1) % $t, $j = ($j+1) % $c)
       {   
          $A = self::$S[$i] = self::ROTL(self::$S[$i] + ($A + $B), 3, self::$w);
          $B = $L[$j] = self::ROTL($L[$j] + ($A + $B), ($A + $B)&(self::$w-1), self::$w);
       }
        
    }
    public static function rc5_encrypt($pt)
    {
       $A = $pt[0] + self::$S[0]; $B = $pt[1] + self::$S[1];
        
       for($i = 1; $i <= self::$r; $i++)
       {
          $A = self::ROTL($A ^ $B, $B&(self::$w-1), self::$w) + self::$S[2*$i];
          $B = self::ROTL($B ^ $A, $A&(self::$w-1), self::$w) + self::$S[2*$i + 1];           
       }
       return array($A & (pow(2,self::$w)-1), $B & (pow(2,self::$w)-1));
    }
     
    public static function rc5_decrypt($ct)
    {
       $B=$ct[1]; $A=$ct[0];
        
       for($i = self::$r; $i > 0; $i--)
       {
          $B = self::ROTR($B - self::$S[2*$i + 1], $A&(self::$w-1), self::$w) ^ $A;
          $A = self::ROTR($A - self::$S[2*$i], $B&(self::$w-1), self::$w) ^ $B;           
       }
        
       return array(($A - self::$S[0]) & (pow(2,self::$w)-1), ($B - self::$S[1]) & (pow(2,self::$w)-1));
    }
	
	##
	private static function _nullpadding($str,$length=16) {
		if(strlen($str)%$length != 0){
			$str .= str_repeat(chr(0),$length-strlen($str)%$length);
		}
		return $str;
	}
	 
	private static function key($key) {
		for ($s = array(), $i = 0; $i < 256; $i++)
			$s[$i] = $i;
		for ($j = 0, $i = 0; $i < 256; $i++)
		{
			$j = ($j + $s[$i] + ord($key[$i % strlen($key)])) % 256;
			$x = $s[$i];
			$s[$i] = $s[$j];
			$s[$j] = $x;
		}
		  
		return $s;
	}
	 
	// convert 32bits interger to 4characters string
	private static function _32bits2str($block) {
		return pack('N',$block & 0xffffffff);
	}
	// convert 4characters string to 32bits interger
	private static function _str232bits($block) {
		return unpack('N*',$block)[1];
	}
	 
	//Stream cipher encrypt use RC5
	public static function RC5enc($str,$key,$mode='ECB') {
		$str = self::_nullpadding($str, 8);//var_dump($str);die();
		$enc = '';
		$k = self::key($key); //var_dump($k);die();
		self::rc5_init($k);
		for($i=0;$i<strlen($str)/8;$i++){
			$block = substr($str,$i*8,8);
			$chr = array(self::_str232bits(substr($block,0,4)),self::_str232bits(substr($block,4,4)));  
			$e = self::rc5_encrypt($chr);
			$enc .= self::_32bits2str($e[0]).self::_32bits2str($e[1]);
		}
		return $enc;
	}
	  
	//Stream cipher decrypt use RC5
	public static function RC5dec($str,$key,$mode='ECB') {
		$enc = '';
		$k = self::key($key); 
		self::rc5_init($k); 
		for($i=0;$i<strlen($str)/8;$i++){
			$block = substr($str,$i*8,8);
			$chr = array(self::_str232bits(substr($block,0,4)),self::_str232bits(substr($block,4,4))); 
			$e = self::rc5_decrypt($chr);
			$enc .= self::_32bits2str($e[0]).self::_32bits2str($e[1]);
		}
		return rtrim($enc,chr(0));
	}

}
 
?>