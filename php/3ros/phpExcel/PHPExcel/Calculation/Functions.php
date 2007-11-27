<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2007 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel_Calculation
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/lgpl.txt	LGPL
 * @version    1.5.0, 2007-10-23
 */


/** PHPExcel_Cell */
require_once 'PHPExcel/Cell.php';

/** PHPExcel_Cell_DataType */
require_once 'PHPExcel/Cell/DataType.php';


/**
 * PHPExcel_Calculation_Functions
 *
 * @category   PHPExcel
 * @package    PHPExcel_Calculation
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Calculation_Functions {
	/**
	 * List of error codes
	 *
	 * @var array
	 */
	private static $_errorCodes = array(
										'null' 				=> "#NULL!",
										'divisionbyzero' 	=> "#DIV/0!",
										'value' 			=> "#VALUE!",
										'reference' 		=> "#REF!",
										'name' 				=> "#NAME?",
										'num' 				=> "#NUM!",
										'na' 				=> "#N/A"
									);

	/**
	 * DUMMY
	 *
	 * @return  string	#N/A!
	 */
	public static function DUMMY() {
		return self::$_errorCodes['na'];
	}

	/**
	 * NA
	 *
	 * @return  string	#N/A!
	 */
	public static function NA() {
		return self::$_errorCodes['na'];
	}

	/**
	 * LOGICAL_AND
	 *
	 * @return  boolean
	 */
	public static function LOGICAL_AND() {
		// Return value
		$returnValue = null;

		// Loop trough arguments
		$aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
		foreach ($aArgs as $arg) {
			if (is_null($returnValue)) {
				$returnValue = $arg;
			} else {
				// Is it a boolean value?
				if (is_bool($arg)) {
					$returnValue = $returnValue && $arg;
				}
			}
		}

		// Return
		return $returnValue;
	}

	/**
	 * LOGICAL_OR
	 *
	 * @return  boolean
	 */
	public static function LOGICAL_OR() {
		// Return value
		$returnValue = null;

		// Loop trough arguments
		$aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
		foreach ($aArgs as $arg) {
			if (is_null($returnValue)) {
				$returnValue = $arg;
			} else {
				// Is it a boolean value?
				if (is_bool($arg)) {
					$returnValue = $returnValue || $arg;
				}
			}
		}

		// Return
		return $returnValue;
	}

	/**
	 * SUM
	 *
	 * @return  int
	 */
	public static function SUM() {
		// Return value
		$returnValue = 0;

		// Loop trough arguments
		$aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
		foreach ($aArgs as $arg) {
			// Is it a numeric value?
			if ((is_numeric($arg)) && (!is_string($arg))) {
				$returnValue += $arg;
			}
		}

		// Return
		return $returnValue;
	}

	/**
	 * PRODUCT
	 *
	 * @return  int
	 */
	public static function PRODUCT() {
		// Return value
		$returnValue = 0;

		// Loop trough arguments
		$aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
		foreach ($aArgs as $arg) {
			// Is it a numeric value?
			if ((is_numeric($arg)) && (!is_string($arg))) {
				$returnValue *= $arg;
			}
		}

		// Return
		return $returnValue;
	}

	/**
	 * QUOTIENT
	 *
	 * @return  int
	 */
	public static function QUOTIENT() {
		// Return value
		$returnValue = null;

		// Loop trough arguments
		$aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
		foreach ($aArgs as $arg) {
			// Is it a numeric value?
			if ((is_numeric($arg)) && (!is_string($arg))) {
				if (is_null($returnValue)) {
					$returnValue = $arg;
				} else {
					$returnValue /= $arg;
				}
			}
		}

		// Return
		return intval($returnValue);
	}

	/**
	 * MIN
	 *
	 * @return  int
	 */
	public static function MIN() {
		// Return value
		$returnValue = 0;

		// Loop trough arguments
		$aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
		$returnValue = $aArgs[0];
		foreach ($aArgs as $arg) {
			// Is it a numeric value?
			if ((is_numeric($arg)) && (!is_string($arg))) {
				if ($arg < $returnValue) {
					$returnValue = $arg;
				}
			}
		}

		// Return
		return $returnValue;
	}

	/**
	 * MINA
	 *
	 * @return  int
	 */
	public static function MINA() {
		// Return value
		$returnValue = 0;

		// Loop through arguments
		$aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
		$returnValue = $aArgs[0];
		foreach ($aArgs as $arg) {
			// Is it a numeric value?
			if ((is_numeric($arg)) || (is_bool($arg)) || (is_string($arg))) {
				if (is_bool($arg)) {
					$arg = (integer) $arg;
				} elseif (is_string($arg)) {
					$arg = 0;
				}
				if ($arg < $returnValue) {
					$returnValue = $arg;
				}
			}
		}

		// Return
		return $returnValue;
	}

	/**
	 * MAX
	 *
	 * @return  int
	 */
	public static function MAX() {
		// Return value
		$returnValue = 0;

		// Loop trough arguments
		$aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
		$returnValue = $aArgs[0];
		foreach ($aArgs as $arg) {
			// Is it a numeric value?
			if ((is_numeric($arg)) && (!is_string($arg))) {
				if ($arg > $returnValue) {
					$returnValue = $arg;
				}
			}
		}

		// Return
		return $returnValue;
	}

	/**
	 * MAXA
	 *
	 * @return  float
	 */
	public static function MAXA() {
		// Return value
		$returnValue = 0;

		// Loop through arguments
		$aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
		$returnValue = $aArgs[0];
		foreach ($aArgs as $arg) {
			// Is it a numeric value?
			if ((is_numeric($arg)) || (is_bool($arg)) || (is_string($arg))) {
				if (is_bool($arg)) {
					$arg = (integer) $arg;
				} elseif (is_string($arg)) {
					$arg = 0;
				}
				if ($arg > $returnValue) {
					$returnValue = $arg;
				}
			}
		}

		// Return
		return $returnValue;
	}

	/**
	 * COUNT
	 *
	 * @return  int
	 */
	public static function COUNT() {
		// Return value
		$returnValue = 0;

		// Loop trough arguments
		$aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
		foreach ($aArgs as $arg) {
			// Is it a numeric value?
			if ((is_numeric($arg)) && (!is_string($arg))) {
				$returnValue++;
			}
		}

		// Return
		return $returnValue;
	}

	/**
	 * COUNTA
	 *
	 * @return  int
	 */
	public static function COUNTA() {
		// Return value
		$returnValue = 0;

		// Loop through arguments
		$aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
		foreach ($aArgs as $arg) {
			// Is it a numeric, boolean or string value?
			if ((is_numeric($arg)) || (is_bool($arg)) || (is_string($arg))) {
				$returnValue++;
			}
		}

		// Return
		return $returnValue;
	}

	/**
	 * AVERAGE
	 *
	 * @return  float
	 */
	public static function AVERAGE() {
		// Return value
		$returnValue = null;

		// Loop through arguments
		$aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
		$aCount = 0;
		foreach ($aArgs as $arg) {
			// Is it a numeric value?
			if ((is_numeric($arg)) && (!is_string($arg))) {
				if (is_null($returnValue)) {
					$returnValue = $arg;
				} else {
					$returnValue += $arg;
				}
				$aCount++;
			}
		}

		// Return
		if ($aCount > 0) {
			return $returnValue / $aCount;
		} else {
			return $returnValue;
		}
	}

	/**
	 * AVERAGEA
	 *
	 * @return  float
	 */
	public static function AVERAGEA() {
		// Return value
		$returnValue = null;

		// Loop through arguments
		$aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
		$aCount = 0;
		foreach ($aArgs as $arg) {
			if ((is_numeric($arg)) || (is_bool($arg)) || (is_string($arg))) {
				if (is_bool($arg)) {
					$arg = (integer) $arg;
				} elseif (is_string($arg)) {
					$arg = 0;
				}
				if (is_null($returnValue)) {
					$returnValue = $arg;
				} else {
					$returnValue += $arg;
				}
				$aCount++;
			}
		}

		// Return
		if ($aCount > 0) {
			return $returnValue / $aCount;
		} else {
			return $returnValue;
		}
	}

	/**
	 * MEDIAN
	 *
	 * @return  float
	 */
	public static function MEDIAN() {
		// Return value
		$returnValue = null;

		// Loop through arguments
		$aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
		foreach ($aArgs as $arg) {
			// Is it a numeric value?
			if ((is_numeric($arg)) && (!is_string($arg))) {
				$mArgs[] = $arg;
			}
		}

		$mValueCount = count($mArgs);
		if ($mValueCount > 0) {
			sort($mArgs,SORT_NUMERIC);
			$mValueCount = $mValueCount / 2;
			if ($mValueCount == floor($mValueCount)) {
				$returnValue = ($mArgs[$mValueCount--] + $mArgs[$mValueCount]) / 2;
			} else {
				$mValueCount == floor($mValueCount);
				$returnValue = $mArgs[$mValueCount];
			}
		}

		// Return
		return $returnValue;
	}

	/**
	 * MODE
	 *
	 * @return  float
	 */
	public static function MODE() {
		// Return value
		$returnValue = PHPExcel_Calculation_Functions::NA();

		// Loop through arguments
		$aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());

		foreach ($aArgs as $arg) {
			// Is it a numeric value?
			if ((is_numeric($arg)) && (!is_string($arg))) {
				$mArgs[] = $arg;
			}
		}

		if (count($mArgs) > 0) {
			$mArgs = array_count_values($mArgs);
			arsort($mArgs,SORT_NUMERIC);
			reset($mArgs);
			if (current($mArgs) > 1) {
				$returnValue = key($mArgs);
			}
		}

		// Return
		return $returnValue;
	}

	/**
	 * DEVSQ
	 *
	 * @return  float
	 */
	public static function DEVSQ() {
		// Return value
		$returnValue = null;

		$aMean = PHPExcel_Calculation_Functions::AVERAGE(func_get_args());
		if (!is_null($aMean)) {
			$aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());

			$aCount = -1;
			foreach ($aArgs as $arg) {
				// Is it a numeric value?
				if ((is_numeric($arg)) && (!is_string($arg))) {
					if (is_null($returnValue)) {
						$returnValue = pow(($arg - $aMean),2);
					} else {
						$returnValue += pow(($arg - $aMean),2);
					}
					$aCount++;
				}
			}

			// Return
			return $returnValue;
		}
		return NA();
	}

	/**
	 * STDEV
	 *
	 * @return  float
	 */
	public static function STDEV() {
		// Return value
		$returnValue = null;

		$aMean = PHPExcel_Calculation_Functions::AVERAGE(func_get_args());
		if (!is_null($aMean)) {
			$aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());

			$aCount = -1;
			foreach ($aArgs as $arg) {
				// Is it a numeric value?
				if ((is_numeric($arg)) && (!is_string($arg))) {
					if (is_null($returnValue)) {
						$returnValue = pow(($arg - $aMean),2);
					} else {
						$returnValue += pow(($arg - $aMean),2);
					}
					$aCount++;
				}
			}

			// Return
			if ($aCount > 0) {
				return sqrt($returnValue / $aCount);
			}
		}
		return NA();
	}

	/**
	 * STDEVA
	 *
	 * @return  float
	 */
	public static function STDEVA() {
		// Return value
		$returnValue = null;

		$aMean = PHPExcel_Calculation_Functions::AVERAGEA(func_get_args());
		if (!is_null($aMean)) {
			$aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());

			$aCount = -1;
			foreach ($aArgs as $arg) {
				// Is it a numeric value?
				if ((is_numeric($arg)) || (is_bool($arg)) || (is_string($arg))) {
					if (is_bool($arg)) {
						$arg = (integer) $arg;
					} elseif (is_string($arg)) {
						$arg = 0;
					}
					if (is_null($returnValue)) {
						$returnValue = pow(($arg - $aMean),2);
					} else {
						$returnValue += pow(($arg - $aMean),2);
					}
					$aCount++;
				}
			}

			// Return
			if ($aCount > 0) {
				return sqrt($returnValue / $aCount);
			}
		}
		return NA();
	}

	/**
	 * STDEVP
	 *
	 * @return  float
	 */
	public static function STDEVP() {
		// Return value
		$returnValue = null;

		$aMean = PHPExcel_Calculation_Functions::AVERAGE(func_get_args());
		if (!is_null($aMean)) {
			$aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());

			$aCount = 0;
			foreach ($aArgs as $arg) {
				// Is it a numeric value?
				if ((is_numeric($arg)) && (!is_string($arg))) {
					if (is_null($returnValue)) {
						$returnValue = pow(($arg - $aMean),2);
					} else {
						$returnValue += pow(($arg - $aMean),2);
					}
					$aCount++;
				}
			}

			// Return
			if ($aCount > 0) {
				return sqrt($returnValue / $aCount);
			}
		}
		return NA();
	}

	/**
	 * STDEVPA
	 *
	 * @return  float
	 */
	public static function STDEVPA() {
		// Return value
		$returnValue = null;

		$aMean = PHPExcel_Calculation_Functions::AVERAGEA(func_get_args());
		if (!is_null($aMean)) {
			$aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());

			$aCount = 0;
			foreach ($aArgs as $arg) {
				// Is it a numeric value?
				if ((is_numeric($arg)) || (is_bool($arg)) || (is_string($arg))) {
					if (is_bool($arg)) {
						$arg = (integer) $arg;
					} elseif (is_string($arg)) {
						$arg = 0;
					}
					if (is_null($returnValue)) {
						$returnValue = pow(($arg - $aMean),2);
					} else {
						$returnValue += pow(($arg - $aMean),2);
					}
					$aCount++;
				}
			}

			// Return
			if ($aCount > 0) {
				return sqrt($returnValue / $aCount);
			}
		}
		return NA();
	}

	/**
	 * VARFunc
	 *
	 * @return  float
	 */
	public static function VARFunc() {
		// Return value
		$returnValue = null;

		$summerA = $summerB = 0;

		// Loop through arguments
		$aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
		$aCount = 0;
		foreach ($aArgs as $arg) {
			// Is it a numeric value?
			if ((is_numeric($arg)) && (!is_string($arg))) {
				$summerA += ($arg * $arg);
				$summerB += $arg;
				$aCount++;
			}
		}

		// Return
		if ($aCount > 2) {
			$summerA = $summerA * $aCount;
			$summerB = ($summerB * $summerB);
			$returnValue = ($summerA - $summerB) / ($aCount * ($aCount - 1));
		}
		return $returnValue;
	}

	/**
	 * VARA
	 *
	 * @return  float
	 */
	public static function VARA() {
		// Return value
		$returnValue = null;

		$summerA = $summerB = 0;

		// Loop through arguments
		$aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
		$aCount = 0;
		foreach ($aArgs as $arg) {
			// Is it a numeric value?
			if ((is_numeric($arg)) || (is_bool($arg)) || (is_string($arg))) {
				if (is_bool($arg)) {
					$arg = (integer) $arg;
				} elseif (is_string($arg)) {
					$arg = 0;
				}
				$summerA += ($arg * $arg);
				$summerB += $arg;
				$aCount++;
			}
		}

		// Return
		if ($aCount > 2) {
			$summerA = $summerA * $aCount;
			$summerB = ($summerB * $summerB);
			$returnValue = ($summerA - $summerB) / ($aCount * ($aCount - 1));
		}
		return $returnValue;
	}

	/**
	 * VARP
	 *
	 * @return  float
	 */
	public static function VARP() {
		// Return value
		$returnValue = null;

		$summerA = $summerB = 0;

		// Loop through arguments
		$aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
		$aCount = 0;
		foreach ($aArgs as $arg) {
			// Is it a numeric value?
			if ((is_numeric($arg)) && (!is_string($arg))) {
				$summerA += ($arg * $arg);
				$summerB += $arg;
				$aCount++;
			}
		}

		// Return
		if ($aCount > 0) {
			$summerA = $summerA * $aCount;
			$summerB = ($summerB * $summerB);
			$returnValue = ($summerA - $summerB) / ($aCount * $aCount);
		}
		return $returnValue;
	}

	/**
	 * VARPA
	 *
	 * @return  float
	 */
	public static function VARPA() {
		// Return value
		$returnValue = null;

		$summerA = $summerB = 0;

		// Loop through arguments
		$aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
		$aCount = 0;
		foreach ($aArgs as $arg) {
			// Is it a numeric value?
			if ((is_numeric($arg)) || (is_bool($arg)) || (is_string($arg))) {
				if (is_bool($arg)) {
					$arg = (integer) $arg;
				} elseif (is_string($arg)) {
					$arg = 0;
				}
				$summerA += ($arg * $arg);
				$summerB += $arg;
				$aCount++;
			}
		}

		// Return
		if ($aCount > 0) {
			$summerA = $summerA * $aCount;
			$summerB = ($summerB * $summerB);
			$returnValue = ($summerA - $summerB) / ($aCount * $aCount);
		}
		return $returnValue;
	}

	/**
	 * RAND
	 *
	 * @param 	int		$min	Minimal value
	 * @param 	int		$max	Maximal value
	 * @return  int		Random number
	 */
	public static function RAND($min = 0, $max = 0) {
		$min 		= self::flattenSingleValue($min);
		$max 		= self::flattenSingleValue($max);

		if ($min == 0 && $max == 0) {
			return (rand(0,10000000)) / 10000000;
		} else {
			return rand($min, $max);
		}
	}

	/**
	 * MOD
	 *
	 * @param 	int		$a		Dividend
	 * @param 	int		$b		Divisor
	 * @return  int		Remainder
	 */
	public static function MOD($a = 1, $b = 1) {
		$a 		= self::flattenSingleValue($a);
		$b 		= self::flattenSingleValue($b);

		return $a % $b;
	}

	/**
	 * CONCATENATE
	 *
	 * @return  string
	 */
	public static function CONCATENATE() {
		// Return value
		$returnValue = '';

		// Loop trough arguments
		$aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
		foreach ($aArgs as $arg) {
			$returnValue .= $arg;
		}

		// Return
		return $returnValue;
	}

	/**
	 * LEFT
	 *
	 * @param 	string	$value	Value
	 * @param 	int		$chars	Number of characters
	 * @return  string
	 */
	public static function LEFT($value = '', $chars = null) {
		$value 		= self::flattenSingleValue($value);
		$chars 		= self::flattenSingleValue($chars);

		return substr($value, 0, $chars);
	}

	/**
	 * RIGHT
	 *
	 * @param 	string	$value	Value
	 * @param 	int		$chars	Number of characters
	 * @return  string
	 */
	public static function RIGHT($value = '', $chars = null) {
		$value 		= self::flattenSingleValue($value);
		$chars 		= self::flattenSingleValue($chars);

		return substr($value, strlen($value) - $chars);
	}

	/**
	 * MID
	 *
	 * @param 	string	$value	Value
	 * @param 	int		$start	Start character
	 * @param 	int		$chars	Number of characters
	 * @return  string
	 */
	public static function MID($value = '', $start = 1, $chars = null) {
		$value 		= self::flattenSingleValue($value);
		$start 		= self::flattenSingleValue($start);
		$chars 		= self::flattenSingleValue($chars);

		return substr($value, $start - 1, $chars);
	}

	/**
	 * IS_BLANK
	 *
	 * @param 	mixed	$value	Value to check
	 * @return  boolean
	 */
	public static function IS_BLANK($value = '') {return true;
		$value 		= self::flattenSingleValue($value);

		return ($value == '');
	}

	/**
	 * IS_ERR
	 *
	 * @param 	mixed	$value	Value to check
	 * @return  boolean
	 */
	public static function IS_ERR($value = '') {
		$value 		= self::flattenSingleValue($value);

		return self::IS_ERROR($value) && (!self::IS_NA($value));
	}

	/**
	 * IS_ERROR
	 *
	 * @param 	mixed	$value	Value to check
	 * @return  boolean
	 */
	public static function IS_ERROR($value = '') {
		$value 		= self::flattenSingleValue($value);

		return in_array($value, array_values(self::$_errorCodes));
	}

	/**
	 * IS_NA
	 *
	 * @param 	mixed	$value	Value to check
	 * @return  boolean
	 */
	public static function IS_NA($value = '') {
		$value 		= self::flattenSingleValue($value);

		return ($value == self::$_errorCodes['na']);
	}

	/**
	 * IS_EVEN
	 *
	 * @param 	mixed	$value	Value to check
	 * @return  boolean
	 */
	public static function IS_EVEN($value = 0) {
		$value 		= self::flattenSingleValue($value);

		while (intval($value) != $value) {
			$value *= 10;
		}
		return ($value % 2 == 0);
	}

	/**
	 * IS_NUMBER
	 *
	 * @param 	mixed	$value		Value to check
	 * @return  boolean
	 */
	public static function IS_NUMBER($value = 0) {
		$value 		= self::flattenSingleValue($value);

		return is_numeric($value);
	}

	/**
	 * STATEMENT_IF
	 *
	 * @param 	mixed	$value		Value to check
	 * @param 	mixed	$truepart	Value when true
	 * @param 	mixed	$falsepart	Value when false
	 * @return  mixed
	 */
	public static function STATEMENT_IF($value = true, $truepart = '', $falsepart = '') {
		$value 		= self::flattenSingleValue($value);
		$truepart 	= self::flattenSingleValue($truepart);
		$falsepart 	= self::flattenSingleValue($falsepart);

		return ($value ? $truepart : $falsepart);
	}

	/**
	 * STATEMENT_IFERROR
	 *
	 * @param 	mixed	$value		Value to check , is also value when no error
	 * @param 	mixed	$errorpart	Value when error
	 * @return  mixed
	 */
	public static function STATEMENT_IFERROR($value = '', $errorpart = '') {
		return self::STATEMENT_IF(self::IS_ERROR($value), $errorpart, $value);
	}

	/**
	 * VERSION
	 *
	 * @return  string	Version information
	 */
	public static function VERSION() {
		return 'PHPExcel 1.5.0, 2007-10-23';
	}

	/**
	 * TRUNC
	 *
	 * Truncates value to the number of fractional digits by number_digits.
	 *
	 * @param 	float		$value
	 * @param 	int			$number_digits
	 * @return  float		Truncated value
	 */
	public static function TRUNC($value = 0, $number_digits = 0) {
		$value			= self::flattenSingleValue($value);
		$number_digits 	= self::flattenSingleValue($number_digits);

		// Validate parameters
		if ($number_digits < 0) {
			return self::$_errorCodes['value'];
		}

		// Truncate
		if ($number_digits > 0) {
			$value = $value * pow(10, $number_digits);
		}
		$value = intval($value);
		if ($number_digits > 0) {
			$value = $value / pow(10, $number_digits);
		}

		// Return
		return $value;
	}

	/**
	 * POWER
	 *
	 * Computes x raised to the power y.
	 *
	 * @param 	float		$x
	 * @param 	float		$y
	 * @return  float
	 */
	public static function POWER($x = 0, $y = 2) {
		$x	= self::flattenSingleValue($x);
		$y 	= self::flattenSingleValue($y);

		// Validate parameters
		if ($x < 0) {
			return self::$_errorCodes['num'];
		}
		if ($x == 0 && $y <= 0) {
			return self::$_errorCodes['divisionbyzero'];
		}

		// Return
		return pow($x, $y);
	}

    /**
     * EFFECT
     *
     * Returns the effective interest rate given the nominal rate and the number of compounding payments per year.
     *
     * @param 	float	$nominal_rate      Nominal interest rate
     * @param 	int		$npery		       Number of compounding payments per year
     * @return 	float
     */
    public static function EFFECT($nominal_rate = 0, $npery = 0) {
		$nominal_rate	= self::flattenSingleValue($$nominal_rate);
		$npery 			= (int)self::flattenSingleValue($npery);

    	// Validate parameters
		if ($$nominal_rate <= 0 || $npery < 1) {
			return self::$_errorCodes['num'];
		}

        return pow((1 + $nominal_rate / $npery), $npery) - 1;
    }

    /**
     * NOMINAL
     *
     * Returns the nominal interest rate given the effective rate and the number of compounding payments per year.
     *
     * @param 	float	$effect_rate	Effective interest rate
     * @param 	int		$npery	        Number of compounding payments per year
     * @return 	float
     */
    public static function NOMINAL($effect_rate = 0, $npery = 0) {
		$effect_rate	= self::flattenSingleValue($effect_rate);
		$npery 			= (int)self::flattenSingleValue($npery);

    	// Validate parameters
		if ($effect_rate <= 0 || $npery < 1) {
			return self::$_errorCodes['num'];
		}

		// Calculate
        return $npery * (pow($effect_rate + 1, 1 / $npery) - 1);
    }

    /**
     * PV
     *
     * Returns the Present Value of a cash flow with constant payments and interest rate (annuities).
     *
     * @param 	float	$rate	Interest rate per period
     * @param 	int		$nper	Number of periods
     * @param 	float	$pmt	Periodic payment (annuity)
     * @param 	float	$fv		Future Value
     * @param 	int		$type	Payment type: 0 = at the end of each period, 1 = at the beginning of each period
     * @return 	float
     */
    public static function PV($rate = 0, $nper = 0, $pmt = 0, $fv = 0, $type = 0) {
		$rate	= self::flattenSingleValue($rate);
		$nper	= self::flattenSingleValue($nper);
		$pmt	= self::flattenSingleValue($pmt);
		$fv		= self::flattenSingleValue($fv);
		$type	= self::flattenSingleValue($type);

    	// Validate parameters
		if ($type != 0 && $type != 1) {
			return self::$_errorCodes['num'];
		}

		// Calculate
        if (!is_null($rate) && $rate != 0) {
            return (-$pmt * (1 + $rate * $type) * ((pow(1 + $rate, $nper) - 1) / $rate) - $fv) / pow(1 + $rate, $nper);
        } else {
            return -$fv - $pmt * $nper;
        }
    }

    /**
     * FV
     *
     * Returns the Future Value of a cash flow with constant payments and interest rate (annuities).
     *
     * @param 	float	$rate	Interest rate per period
     * @param 	int		$nper	Number of periods
     * @param 	float	$pmt	Periodic payment (annuity)
     * @param 	float	$pv		Present Value
     * @param 	int		$type	Payment type: 0 = at the end of each period, 1 = at the beginning of each period
     * @return 	float
     */
    public static function FV($rate = 0, $nper = 0, $pmt = 0, $pv = 0, $type = 0) {
		$rate	= self::flattenSingleValue($rate);
		$nper	= self::flattenSingleValue($nper);
		$pmt	= self::flattenSingleValue($pmt);
		$pv		= self::flattenSingleValue($pv);
		$type	= self::flattenSingleValue($type);

    	// Validate parameters
		if ($type != 0 && $type != 1) {
			return self::$_errorCodes['num'];
		}

		// Calculate
        if (!is_null($rate) && $rate != 0) {
            return -$pv * pow(1 + $rate, $nper) - $pmt * (1 + $rate * $type) * (pow(1 + $rate, $nper) - 1) / $rate;
        } else {
            return -$pv - $pmt * $nper;
        }
    }

    /**
     * PMT
     *
     * Returns the constant payment (annuity) for a cash flow with a constant interest rate.
     *
     * @param 	float	$rate	Interest rate per period
     * @param 	int		$nper	Number of periods
     * @param 	float	$pv		Present Value
     * @param 	float	$fv		Future Value
     * @param 	int		$type	Payment type: 0 = at the end of each period, 1 = at the beginning of each period
     * @return 	float
     */
    public static function PMT($rate = 0, $nper = 0, $pv = 0, $fv = 0, $type = 0) {
		$rate	= self::flattenSingleValue($rate);
		$nper	= self::flattenSingleValue($nper);
		$pv		= self::flattenSingleValue($pv);
		$fv		= self::flattenSingleValue($fv);
		$type	= self::flattenSingleValue($type);

    	// Validate parameters
		if ($type != 0 && $type != 1) {
			return self::$_errorCodes['num'];
		}

		// Calculate
        if (!is_null($rate) && $rate != 0) {
            return (-$fv - $pv * pow(1 + $rate, $nper)) / (1 + $rate * $type) / ((pow(1 + $rate, $nper) - 1) / $rate);
        } else {
            return (-$pv - $fv) / $nper;
        }
    }

    /**
     * NPER
     *
     * Returns the number of periods for a cash flow with constant periodic payments (annuities), and interest rate.
     *
     * @param 	float	$rate	Interest rate per period
     * @param 	int		$pmt	Periodic payment (annuity)
     * @param 	float	$pv		Present Value
     * @param 	float	$fv		Future Value
     * @param 	int		$type	Payment type: 0 = at the end of each period, 1 = at the beginning of each period
     * @return 	float
     */
    public static function NPER($rate = 0, $pmt = 0, $pv = 0, $fv = 0, $type = 0) {
		$rate	= self::flattenSingleValue($rate);
		$pmt	= self::flattenSingleValue($pmt);
		$pv		= self::flattenSingleValue($pv);
		$fv		= self::flattenSingleValue($fv);
		$type	= self::flattenSingleValue($type);

    	// Validate parameters
		if ($type != 0 && $type != 1) {
			return self::$_errorCodes['num'];
		}

		// Calculate
        if (!is_null($rate) && $rate != 0) {
        	if ($pmt == 0 && $pv == 0) {
        		return self::$_errorCodes['num'];
        	}
            return log(($pmt * (1 + $rate * $type) / $rate - $fv) / ($pv + $pmt * (1 + $rate * $type) / $rate)) / log(1 + $rate);
        } else {
            if ($pmt == 0) {
        		return self::$_errorCodes['num'];
        	}
            return (-$pv -$fv) / $pmt;
        }
    }

    /**
     * NPV
     *
     * Returns the Net Present Value of a cash flow series given a discount rate.
     *
     * @param 	float	Discount interest rate
     * @param 	array	Cash flow series
     * @return 	float
     */
    public static function NPV() {
		// Return value
		$returnValue = 0;

		// Loop trough arguments
		$aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());

		// Calculate
		$rate = array_shift($aArgs);
		for ($i = 1; $i <= count($aArgs); $i++) {
			// Is it a numeric value?
			if (is_numeric($aArgs[$i - 1])) {
				$returnValue += $aArgs[$i - 1] / pow(1 + $rate, $i);
			}
		}

		// Return
		return $returnValue;
    }

	/**
	 * ACCRINT
	 *
	 * Computes the accrued interest for a security that pays periodic interest.
	 *
	 * @param 	int		$issue
	 * @param 	int		$firstInterest
	 * @param 	int		$settlement
	 * @param 	int		$rate
	 * @param 	int		$par
	 * @param 	int		$frequency
	 * @param 	int		$basis
	 * @return  int		The accrued interest for a security that pays periodic interest.
	 */
	/*
	public static function ACCRINT($issue = 0, $firstInterest = 0, $settlement = 0, $rate = 0, $par = 1000, $frequency = 1, $basis = 0) {
		$issue 			= self::flattenSingleValue($issue);
		$firstInterest 	= self::flattenSingleValue($firstInterest);
		$settlement 	= self::flattenSingleValue($settlement);
		$rate 			= self::flattenSingleValue($rate);
		$par 			= self::flattenSingleValue($par);
		$frequency 		= self::flattenSingleValue($frequency);
		$basis 			= self::flattenSingleValue($basis);

		// Perform checks
		if ($issue >= $settlement || $rate <= 0 || $par <= 0 || !($frequency == 1 || $frequency == 2 || $frequency == 4) || $basis < 0 || $basis > 4) return self::$_errorCodes['num'];

		// Calculate value
		return $par * ($rate / $frequency) *
	}
	*/

	/**
	 * Flatten multidemensional array
	 *
	 * @param 	array	$array	Array to be flattened
	 * @return  array	Flattened array
	 */
	public static function flattenArray($array) {
		$arrayValues = array();

		foreach ($array as $value) {
			if (is_scalar($value)) {
				$arrayValues[] = self::flattenSingleValue($value);
			} else if (is_array($value)) {
				$arrayValues = array_merge($arrayValues, self::flattenArray($value));
			} else {
				$arrayValues[] = $value;
			}
		}

		return $arrayValues;
	}

	/**
	 * Convert an array with one element to a flat value
	 *
	 * @param 	mixed		$value		Array or flat value
	 * @return 	mixed
	 */
	public static function flattenSingleValue($value = '') {
		if (is_array($value)) {
			$value = self::flattenSingleValue(array_pop($value));
		}
		return $value;
	}
}
