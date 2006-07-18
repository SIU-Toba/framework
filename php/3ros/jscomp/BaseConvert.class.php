<?php
/**
 * BaseConvert class,
 *	converts an unsigned base 10 integer to a different base and vice versa.
 * ______________________________________________________________
 * BaseConvert
 *    |
 *    |________ constructor(newBase:string)
 *    |         	uses newBase string var for convertion
 *    |                 [i.e. "0123456789abcdef" for an hex convertion]
 *    |
 *    |________ toBase(unsignedInteger:uint):string
 *    |         	return base value of input
 *    |
 *    |________ fromBase(baseString:string):uint
 *              	return base 10 integer value of base input
 * --------------------------------------------------------------
 * REMEMBER: PHP < 6 doesn't work correctly with integer greater than 2147483647 (2^31 - 1)
 * --------------------------------------------------------------
 * @Compatibility	>= PHP 4
 * @Author		Andrea Giammarchi
 * @Site		http://www.devpro.it/
 * @Date		2006/06/05
 * @Version		1.0
 */
class BaseConvert {
	
	var	$base, $baseLength;
	
	function BaseConvert($base) {
		$this->base = &$base;
		$this->baseLength = strlen($base);
	}
	
	function toBase($num) {
		$module = 0; $result = '';
		while($num) {
			$result = $this->base{($module = $num % $this->baseLength)}.$result;
			$num = (int)(($num - $module) / $this->baseLength);
		}
		return $result !== '' ? $result : $this->base{0};
	}
	
	function fromBase($str) {
		$pos = 0; $len = strlen($str) - 1; $result = 0;
		while($pos < $len)
			$result += pow($this->baseLength, ($len - $pos)) * strpos($this->base, $str{($pos++)});
		return $len >= 0 ? $result + strpos($this->base, $str{($pos)}) : null;
	}
}
?>