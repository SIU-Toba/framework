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
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/lgpl.txt	LGPL
 * @version    1.5.0, 2007-10-23
 */


/** PHPExcel_Worksheet */
require_once 'PHPExcel/Worksheet.php';

/** PHPExcel_Cell */
require_once 'PHPExcel/Cell.php';

/** PHPExcel_Cell_DataType */
require_once 'PHPExcel/Cell/DataType.php';

/** PHPExcel_Calculation_FormulaParser */
require_once 'PHPExcel/Calculation/FormulaParser.php';

/** PHPExcel_Calculation_FormulaToken */
require_once 'PHPExcel/Calculation/FormulaToken.php';

/** PHPExcel_Calculation_Functions */
require_once 'PHPExcel/Calculation/Functions.php';

/** PHPExcel_Calculation_Function */
require_once 'PHPExcel/Calculation/Function.php';


/**
 * PHPExcel_Calculation (Singleton)
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Calculation
{
	/** Cache constants */
	const MAX_CACHE_SECONDS = 2;

	/**
	 * Function mappings (from Excel to PHPExcel)
	 *
	 * @var array
	 */
	private $_functionMappings = null;

	/**
	 * Calculation cache
	 *
	 * @var array
	 */
	private $_calculationCache = array();

	/**
	 * Instance of this class
	 *
	 * @var PHPExcel_Calculation
	 */
	private static $_instance;

	/**
	 * Get an instance of this class
	 *
	 * @return PHPExcel_Calculation
	 */
	public static function getInstance() {
		if (!isset(self::$_instance) || is_null(self::$_instance)) {
			self::$_instance = new PHPExcel_Calculation();
		}

		return self::$_instance;
	}

	/**
	 * Create a new PHPExcel_Calculation
	 */
	protected function __construct() {
		// Assign function mappings
		if (is_null($this->_functionMappings)) {
			$this->_functionMappings = array(
				'ABS' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,		 	'ABS', 					'abs'),
				'ACCRINT' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'ACCRINT', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'ACCRINTM' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'ACCRINTM', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'ACOS' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG, 		'ACOS', 				'acos'),
				'ACOSH' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG, 		'ACOSH', 				'acosh'),
				'ADDRESS' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_LOOKUP_AND_REFERENCE, 	'ADDRESS', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'AMORDEGRC' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'AMORDEGRC', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'AMORLINC' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'AMORLINC', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'AND' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_LOGICAL, 				'AND', 					'PHPExcel_Calculation_Functions::LOGICAL_AND'),
				'AREAS' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_LOOKUP_AND_REFERENCE, 	'AREAS', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'ASC' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA, 		'ASC', 					'PHPExcel_Calculation_Functions::DUMMY'),
				'ASIN' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG, 		'ASIN', 				'asin'),
				'ASINH' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG, 		'ASINH', 				'asinh'),
				'ATAN' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG, 		'ATAN', 				'atan'),
				'ATAN2' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG, 		'ATAN2', 				'atanh'),
				'ATANH' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG, 		'ATANH', 				'atanh'),
				'AVEDEV' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'AVEDEV', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'AVERAGE' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'AVERAGE', 				'PHPExcel_Calculation_Functions::AVERAGE'),
				'AVERAGEA' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'AVERAGEA', 			'PHPExcel_Calculation_Functions::AVERAGEA'),
				'AVERAGEIF' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'AVERAGEIF', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'AVERAGEIFS' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'AVERAGEIFS', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'BAHTTEXT' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA, 		'BAHTTEXT', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'BESSELI' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_ENGINEERING, 			'BESSELI', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'BESSELJ' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_ENGINEERING, 			'BESSELJ', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'BESSELK' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_ENGINEERING, 			'BESSELK', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'BESSELY' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_ENGINEERING, 			'BESSELY', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'BETADIST' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'BETADIST', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'BETAINV' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'BETAINV', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'BIN2DEC' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_ENGINEERING, 			'BIN2DEC', 				'bindec'),
				'BIN2HEX' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_ENGINEERING, 			'BIN2HEX', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'BIN2OCT' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_ENGINEERING, 			'BIN2OCT', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'BINOMDIST' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'BINOMDIST', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'CEILING' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG, 		'CEILING', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'CELL' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_INFORMATION, 			'CELL', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'CHAR' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA, 		'CHAR', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'CHIDIST' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'CHIDIST', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'CHIINV' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'CHIINV', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'CHITEST' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'CHITEST', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'CHOOSE' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_LOOKUP_AND_REFERENCE, 	'CHOOSE', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'CLEAN' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA, 		'CLEAN', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'CODE' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA, 		'CODE', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'COLUMN' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_LOOKUP_AND_REFERENCE, 	'COLUMN', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'COLUMNS' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_LOOKUP_AND_REFERENCE, 	'COLUMNS', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'COMBIN' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG, 		'COMBIN', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'COMPLEX' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_ENGINEERING, 			'COMPLEX', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'CONCATENATE' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA, 		'CONCATENATE', 			'PHPExcel_Calculation_Functions::CONCATENATE'),
				'CONFIDENCE' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'CONFIDENCE', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'CONVERT' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_ENGINEERING, 			'CONVERT', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'CORREL' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'CORREL', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'COS' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG, 		'COS', 					'cos'),
				'COSH' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG, 		'COSH', 				'cosh'),
				'COUNT' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'COUNT', 				'PHPExcel_Calculation_Functions::COUNT'),
				'COUNTA' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'COUNTA', 				'PHPExcel_Calculation_Functions::COUNTA'),
				'COUNTBLANK' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'COUNTBLANK', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'COUNTIF' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'COUNTIF', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'COUNTIFS' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL,			'COUNTIFS', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'COUPDAYBS' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'COUPDAYBS', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'COUPDAYSNC' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'COUPDAYSNC', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'COUPNCD' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'COUPNCD', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'COUPNUM' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'COUPNUM', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'COUPPCD' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'COUPPCD', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'COVAR' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'COVAR', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'CRITBINOM' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'CRITBINOM', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'CUBEKPIMEMBER' 		=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_CUBE, 					'CUBEKPIMEMBER', 		'PHPExcel_Calculation_Functions::DUMMY'),
				'CUBEMEMBER' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_CUBE, 					'CUBEMEMBER', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'CUBEMEMBERPROPERTY' 	=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_CUBE, 					'CUBEMEMBERPROPERTY', 	'PHPExcel_Calculation_Functions::DUMMY'),
				'CUBERANKEDMEMBER' 		=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_CUBE, 					'CUBERANKEDMEMBER', 	'PHPExcel_Calculation_Functions::DUMMY'),
				'CUBESET' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_CUBE, 					'CUBESET', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'CUBESETCOUNT' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_CUBE, 					'CUBESETCOUNT', 		'PHPExcel_Calculation_Functions::DUMMY'),
				'CUBEVALUE' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_CUBE, 					'CUBEVALUE', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'CUMIPMT' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'CUMIPMT', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'CUMPRINC' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'CUMPRINC', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'DATE' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_DATE_AND_TIME, 		'DATE', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'DATEDIF' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_DATE_AND_TIME, 		'DATEDIF', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'DATEVALUE' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_DATE_AND_TIME, 		'DATEVALUE', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'DAVERAGE' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_DATABASE, 				'DAVERAGE', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'DAY' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_DATE_AND_TIME, 		'DAY', 					'PHPExcel_Calculation_Functions::DUMMY'),
				'DAYS360' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_DATE_AND_TIME, 		'DAYS360', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'DB' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'DB', 					'PHPExcel_Calculation_Functions::DUMMY'),
				'DCOUNT' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_DATABASE, 				'DCOUNT', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'DCOUNTA' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_DATABASE, 				'DCOUNTA', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'DDB' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'DDB', 					'PHPExcel_Calculation_Functions::DUMMY'),
				'DEC2BIN' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_ENGINEERING, 			'DEC2BIN', 				'decbin'),
				'DEC2HEX' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_ENGINEERING, 			'DEC2HEX', 				'dechex'),
				'DEC2OCT' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_ENGINEERING, 			'DEC2OCT', 				'decoct'),
				'DEGREES' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG, 		'DEGREES', 				'rad2deg'),
				'DELTA' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_ENGINEERING, 			'DELTA', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'DEVSQ' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'DEVSQ', 				'PHPExcel_Calculation_Functions::DEVSQ'),
				'DGET' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_DATABASE, 				'DGET', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'DISC' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'DISC', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'DMAX' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_DATABASE, 				'DMAX', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'DMIN' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_DATABASE, 				'DMIN', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'DOLLAR' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA, 		'DOLLAR', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'DOLLARDE' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'DOLLARDE', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'DOLLARFR' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'DOLLARFR', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'DPRODUCT' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_DATABASE, 				'DPRODUCT', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'DSTDEV' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_DATABASE, 				'DSTDEV', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'DSTDEVP' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_DATABASE, 				'DSTDEVP', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'DSUM' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_DATABASE, 				'DSUM', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'DURATION' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'DURATION', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'DVAR' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_DATABASE, 				'DVAR', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'DVARP' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_DATABASE, 				'DVARP', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'EDATE' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_DATE_AND_TIME, 		'EDATE', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'EFFECT' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'EFFECT', 				'PHPExcel_Calculation_Functions::EFFECT'),
				'EOMONTH' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_DATE_AND_TIME, 		'EOMONTH', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'ERF' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_ENGINEERING, 			'ERF', 					'PHPExcel_Calculation_Functions::DUMMY'),
				'ERFC' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_ENGINEERING, 			'ERFC', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'ERROR.TYPE' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_INFORMATION, 			'ERROR.TYPE', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'EVEN' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG, 		'EVEN', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'EXACT' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA, 		'EXACT', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'EXP' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG, 		'EXP', 					'exp'),
				'EXPONDIST' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'EXPONDIST', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'FACT' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG, 		'FACT', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'FACTDOUBLE' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG, 		'FACTDOUBLE', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'FALSE' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_LOGICAL, 				'FALSE', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'FDIST' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'FDIST', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'FIND' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA, 		'FIND', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'FINDB' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA, 		'FINDB', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'FINV' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'FINV', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'FISHER' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'FISHER', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'FISHERINV' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'FISHERINV', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'FIXED' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA, 		'FIXED', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'FLOOR' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG, 		'FLOOR', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'FORECAST' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'FORECAST', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'FREQUENCY' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'FREQUENCY', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'FTEST' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'FTEST', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'FV' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'FV', 					'PHPExcel_Calculation_Functions::FV'),
				'FVSCHEDULE' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'FVSCHEDULE', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'GAMAMDIST' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'GAMAMDIST', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'GAMMAINV' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'GAMMAINV', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'GAMMALN' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'GAMMALN', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'GCD' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG, 		'GCD', 					'PHPExcel_Calculation_Functions::DUMMY'),
				'GEOMEAN' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'GEOMEAN', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'GESTEP' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_ENGINEERING, 			'GESTEP', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'GETPIVOTDATA' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_LOOKUP_AND_REFERENCE, 	'GETPIVOTDATA', 		'PHPExcel_Calculation_Functions::DUMMY'),
				'GROWTH' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'GROWTH', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'HARMEAN' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'HARMEAN', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'HEX2BIN' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_ENGINEERING, 			'HEX2BIN', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'HEX2OCT' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_ENGINEERING, 			'HEX2OCT', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'HLOOKUP' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_LOOKUP_AND_REFERENCE, 	'HLOOKUP', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'HOUR' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_DATE_AND_TIME, 		'HOUR', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'HYPERLINK' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_LOOKUP_AND_REFERENCE, 	'HYPERLINK', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'HYPGEOMDIST' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'HYPGEOMDIST', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'IF' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_LOGICAL, 				'IF', 					'PHPExcel_Calculation_Functions::STATEMENT_IF'),
				'IFERROR' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_LOGICAL, 				'IFERROR', 				'PHPExcel_Calculation_Functions::STATEMENT_IFERROR'),
				'IMABS' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_ENGINEERING, 			'IMABS', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'IMAGINARY' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_ENGINEERING, 			'IMAGINARY', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'IMARGUMENT' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_ENGINEERING, 			'IMARGUMENT', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'IMCONJUGATE' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_ENGINEERING, 			'IMCONJUGATE', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'IMCOS' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_ENGINEERING, 			'IMCOS', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'IMDIV' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_ENGINEERING, 			'IMDIV', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'IMEXP' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_ENGINEERING, 			'IMEXP', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'IMLN' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_ENGINEERING, 			'IMLN', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'IMLOG10' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_ENGINEERING, 			'IMLOG10', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'IMLOG2' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_ENGINEERING, 			'IMLOG2', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'IMPOWER' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_ENGINEERING, 			'IMPOWER', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'IMPRODUCT' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_ENGINEERING, 			'IMPRODUCT', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'IMREAL' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_ENGINEERING, 			'IMREAL', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'IMSIN' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_ENGINEERING, 			'IMSIN', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'IMSQRT' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_ENGINEERING, 			'IMSQRT', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'IMSUB' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_ENGINEERING, 			'IMSUB', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'IMSUM' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_ENGINEERING, 			'IMSUM', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'INDEX' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_LOOKUP_AND_REFERENCE,	'INDEX', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'INDIRECT' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_LOOKUP_AND_REFERENCE,	'INDIRECT', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'INFO' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_INFORMATION, 			'INFO', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'INT' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG, 		'INT', 					'intval'),
				'INTERCEPT' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'INTERCEPT', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'INTRATE' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'INTRATE', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'IPMT' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'IPMT', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'IRR' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'IRR', 					'PHPExcel_Calculation_Functions::DUMMY'),
				'ISBLANK' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_INFORMATION, 			'ISBLANK', 				'PHPExcel_Calculation_Functions::IS_BLANK'),
				'ISERR' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_INFORMATION, 			'ISERR', 				'PHPExcel_Calculation_Functions::IS_ERR'),
				'ISERROR' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_INFORMATION, 			'ISERROR', 				'PHPExcel_Calculation_Functions::IS_ERROR'),
				'ISEVEN' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_INFORMATION, 			'ISEVEN', 				'PHPExcel_Calculation_Functions::IS_EVEN'),
				'ISLOGICAL' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_INFORMATION, 			'ISLOGICAL', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'ISNA' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_INFORMATION, 			'ISNA', 				'PHPExcel_Calculation_Functions::IS_NA'),
				'ISNONTEXT' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_INFORMATION, 			'ISNONTEXT', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'ISNUMBER' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_INFORMATION, 			'ISNUMBER', 			'PHPExcel_Calculation_Functions::IS_NUMBER'),
				'ISODD' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_INFORMATION, 			'ISODD', 				'!PHPExcel_Calculation_Functions::IS_EVEN'),
				'ISPMT' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_INFORMATION, 			'ISPMT', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'ISREF' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_INFORMATION, 			'ISREF', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'ISTEXT' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_INFORMATION, 			'ISTEXT', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'JIS' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA, 		'JIS', 					'PHPExcel_Calculation_Functions::DUMMY'),
				'KURT' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'KURT', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'LARGE' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'LARGE', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'LCM' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,			'LCM', 					'PHPExcel_Calculation_Functions::DUMMY'),
				'LEFT' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA, 		'LEFT', 				'PHPExcel_Calculation_Functions::LEFT'),
				'LEFTB' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA, 		'LEFTB', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'LEN' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA, 		'LEN', 					'PHPExcel_Calculation_Functions::DUMMY'),
				'LENB' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA, 		'LENB', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'LINEST' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'LINEST', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'LN' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,			'LN', 					'PHPExcel_Calculation_Functions::DUMMY'),
				'LOG' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,			'LOG', 					'log'),
				'LOG10' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG, 		'LOG10', 				'log10'),
				'LOGEST' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'LOGEST', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'LOGINV' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'LOGINV', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'LOGNORMDIST' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'LOGNORMDIST', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'LOOKUP' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_LOOKUP_AND_REFERENCE,	'LOOKUP', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'LOWER' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA, 		'LOWER', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'MATCH' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_LOOKUP_AND_REFERENCE,	'MATCH', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'MAX' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'MAX', 					'PHPExcel_Calculation_Functions::MAX'),
				'MAXA' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'MAXA', 				'PHPExcel_Calculation_Functions::MAXA'),
				'MDETERM' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,		 	'MDETERM', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'MDURATION' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'MDURATION', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'MEDIAN' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'MEDIAN', 				'PHPExcel_Calculation_Functions::MEDIAN'),
				'MID' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA, 		'MID', 					'PHPExcel_Calculation_Functions::MID'),
				'MIDB' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA, 		'MIDB', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'MIN' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'MIN', 					'PHPExcel_Calculation_Functions::MIN'),
				'MINA' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'MINA', 				'PHPExcel_Calculation_Functions::MINA'),
				'MINUTE' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_DATE_AND_TIME, 		'MINUTE', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'MINVERSE' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,		 	'MINVERSE', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'MIRR' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'MIRR', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'MMULT' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,		 	'MMULT', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'MOD' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,		 	'MOD', 					'PHPExcel_Calculation_Functions::MOD'),
				'MODE' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'MODE', 				'PHPExcel_Calculation_Functions::MODE'),
				'MONTH' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_DATE_AND_TIME, 		'MONTH', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'MROUND' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,		 	'MROUND', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'MULTINOMIAL' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,		 	'MULTINOMIAL', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'N' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_INFORMATION, 			'N', 					'PHPExcel_Calculation_Functions::DUMMY'),
				'NA' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_INFORMATION, 			'NA', 					'PHPExcel_Calculation_Functions::NA'),
				'NEGBINOMDIST' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'NEGBINOMDIST', 		'PHPExcel_Calculation_Functions::DUMMY'),
				'NETWORKDAYS' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_DATE_AND_TIME, 		'NETWORKDAYS', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'NOMINAL' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'NOMINAL', 				'PHPExcel_Calculation_Functions::NOMINAL'),
				'NORMDIST' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'NORMDIST', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'NORMINV' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'NORMINV', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'NORMSDIST' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'NORMSDIST', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'NORMSINV' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'NORMSINV', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'NOT' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_LOGICAL, 				'NOT', 					'!'),
				'NOW' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_DATE_AND_TIME, 		'NOW', 					'PHPExcel_Calculation_Functions::DUMMY'),
				'NPER' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'NPER', 				'PHPExcel_Calculation_Functions::NPER'),
				'NPV' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'NPV', 					'PHPExcel_Calculation_Functions::NPV'),
				'OCT2BIN' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_ENGINEERING, 			'OCT2BIN', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'OCT2DEC' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_ENGINEERING, 			'OCT2DEC', 				'octdec'),
				'OCT2HEX' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_ENGINEERING, 			'OCT2HEX', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'ODD' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,		 	'ODD', 					'PHPExcel_Calculation_Functions::DUMMY'),
				'ODDFPRICE' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'ODDFPRICE', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'ODDFYIELD' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'ODDFYIELD', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'ODDLPRICE' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'ODDLPRICE', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'ODDLYIELD' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'ODDLYIELD', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'OFFSET' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_LOOKUP_AND_REFERENCE,	'OFFSET', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'OR' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_LOGICAL, 				'OR', 					'PHPExcel_Calculation_Functions::LOGICAL_OR'),
				'PEARSON' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'PEARSON', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'PERCENTILE' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'PERCENTILE', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'PERCENTRANK' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'PERCENTRANK', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'PERMUT' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'PERMUT', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'PHONETIC' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA, 		'PHONETIC', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'PI' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,		 	'PI', 					'pi'),
				'PMT' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'PMT', 					'PHPExcel_Calculation_Functions::PMT'),
				'POISSON' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'POISSON', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'POWER' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,		 	'POWER', 				'PHPExcel_Calculation_Functions::POWER'),
				'PPMT' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'PPMT', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'PRICE' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'PRICE', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'ORICEDISC' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'PRICEDISC', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'PRICEMAT' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'PRICEMAT', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'PROB' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'PROB', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'PRODUCT' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,		 	'PRODUCT', 				'PHPExcel_Calculation_Functions::PRODUCT'),
				'PROPER' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA, 		'PROPER', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'PV' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'PV', 					'PHPExcel_Calculation_Functions::PV'),
				'QUARTILE' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'QUARTILE', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'QUOTIENT' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,			'QUOTIENT', 			'PHPExcel_Calculation_Functions::QUOTIENT'),
				'RADIANS' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,		 	'RADIANS', 				'deg2rad'),
				'RAND' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,		 	'RAND', 				'PHPExcel_Calculation_Functions::RAND'),
				'RANDBETWEEN' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,		 	'RANDBETWEEN', 			'PHPExcel_Calculation_Functions::RAND'),
				'RANK' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'RANK', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'RATE' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'RATE', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'RECEIVED' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'RECEIVED', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'REPLACE' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA, 		'REPLACE', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'REPLACEB' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA, 		'REPLACEB', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'REPT' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'REPT', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'RIGHT' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA, 		'RIGHT', 				'PHPExcel_Calculation_Functions::RIGHT'),
				'RIGHTB' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA, 		'RIGHTB', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'ROMAN' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,		 	'ROMAN', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'ROUND' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,		 	'ROUND', 				'round'),
				'ROUNDDOWN' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,			'ROUNDDOWN', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'ROUNDUP' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,			'ROUNDUP', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'ROW' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_LOOKUP_AND_REFERENCE,	'ROW', 					'PHPExcel_Calculation_Functions::DUMMY'),
				'ROWS' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_LOOKUP_AND_REFERENCE,	'ROWS', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'RSQ' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'RSQ', 					'PHPExcel_Calculation_Functions::DUMMY'),
				'RTD' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_LOOKUP_AND_REFERENCE,	'RTD', 					'PHPExcel_Calculation_Functions::DUMMY'),
				'SEARCH' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA, 		'SEARCH', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'SEARCHB' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA, 		'SEARCHB', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'SECOND' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_DATE_AND_TIME, 		'SECOND', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'SERIESSUM' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,			'SERIESSUM', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'SIGN' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,			'SIGN', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'SIN' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,			'SIN', 					'sin'),
				'SINH' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,			'SINH', 				'sinh'),
				'SKEW' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'SKEW', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'SLN' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'SLN', 					'PHPExcel_Calculation_Functions::DUMMY'),
				'SLOPE' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'SLOPE', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'SMALL' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'SMALL', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'SQRT' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,			'SQRT', 				'sqrt'),
				'SQRTPI' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,			'SQRTPI', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'STANDARDIZE' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'STANDARDIZE', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'STDEV' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'STDEV', 				'PHPExcel_Calculation_Functions::STDEV'),
				'STDEVA' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'STDEVA', 				'PHPExcel_Calculation_Functions::STDEVA'),
				'STDEVP' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'STDEVP', 				'PHPExcel_Calculation_Functions::STDEVP'),
				'STDEVPA' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'STDEVPA', 				'PHPExcel_Calculation_Functions::STDEVPA'),
				'STEYX' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'STEYX', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'SUBSTITUTE' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA, 		'SUBSTITUTE', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'SUBTOTAL' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,			'SUBTOTAL', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'SUM' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,			'SUM', 					'PHPExcel_Calculation_Functions::SUM'),
				'SUMIF' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,			'SUMIF', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'SUMIFS' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,			'SUMIFS', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'SUMPRODUCT' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,			'SUMPRODUCT', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'SUMSQ' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,			'SUMSQ', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'SUMX2MY2' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,			'SUMX2MY2', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'SUMX2PY2' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,			'SUMX2PY2', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'SUMXMY2' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,			'SUMXMY2', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'SYD' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'SYD', 					'PHPExcel_Calculation_Functions::DUMMY'),
				'T' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA, 		'T', 					'PHPExcel_Calculation_Functions::DUMMY'),
				'TAN' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,			'TAN', 					'tan'),
				'TANH' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,			'TANH', 				'tanh'),
				'TBILLEQ' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'TBILLEQ', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'TBILLPRICE' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'TBILLPRICE', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'TBILLYIELD' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'TBILLYIELD', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'TDIST' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'TDIST', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'TEXT' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA, 		'TEXT', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'TIME' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_DATE_AND_TIME, 		'TIME', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'TIMEVALUE' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_DATE_AND_TIME, 		'TIMEVALUE', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'TINV' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'TINV', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'TODAY' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_DATE_AND_TIME, 		'TODAY', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'TRANSPOSE' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_LOOKUP_AND_REFERENCE,	'TRANSPOSE', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'TREND' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'TREND', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'TRIM' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA, 		'TRIM', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'TRIMMEAN' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'TRIMMEAN', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'TRUE' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_LOGICAL, 				'TRUE', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'TRUNC' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_MATH_AND_TRIG,			'TRUNC', 				'PHPExcel_Calculation_Functions::TRUNC'),
				'TTEST' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'TTEST', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'TYPE' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_INFORMATION, 			'TYPE', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'UPPER' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA, 		'UPPER', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'USDOLLAR' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'USDOLLAR', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'VALUE' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_TEXT_AND_DATA, 		'VALUE', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'VAR' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'VAR', 					'PHPExcel_Calculation_Functions::VARFunc'),
				'VARA' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'VARA', 				'PHPExcel_Calculation_Functions::VARA'),
				'VARP' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'VARP', 				'PHPExcel_Calculation_Functions::VARP'),
				'VARPA' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'VARPA', 				'PHPExcel_Calculation_Functions::VARPA'),
				'VDB' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'VDB', 					'PHPExcel_Calculation_Functions::DUMMY'),
				'VERSION' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_INFORMATION, 			'VERSION', 				'PHPExcel_Calculation_Functions::VERSION'),
				'VLOOKUP' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_LOOKUP_AND_REFERENCE,	'VLOOKUP', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'WEEKDAY' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_DATE_AND_TIME, 		'WEEKDAY', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'WEEKNUM' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_DATE_AND_TIME, 		'WEEKNUM', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'WEIBULL' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'WEIBULL', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'WORKDAY' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_DATE_AND_TIME, 		'WORKDAY', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'XIRR' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'XIRR', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'XNPV' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'XNPV', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'YEAR' 					=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_DATE_AND_TIME, 		'YEAR', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'YEARFRAC' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_DATE_AND_TIME, 		'YEARFRAC', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'YIELD' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'YIELD', 				'PHPExcel_Calculation_Functions::DUMMY'),
				'YIELDDISC' 			=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'YIELDDISC', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'YIELDMAT' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_FINANCIAL, 			'YIELDMAT', 			'PHPExcel_Calculation_Functions::DUMMY'),
				'ZTEST' 				=> new PHPExcel_Calculation_Function(PHPExcel_Calculation_Function::CATEGORY_STATISTICAL, 			'ZTEST', 				'PHPExcel_Calculation_Functions::DUMMY')
			);
		}
	}

	/**
	 * Calculate cell value (using formula)
	 *
	 * @param 	PHPExcel_Cell	$pCell	Cell to calculate
	 * @return 	mixed
	 * @throws 	Exception
	 */
	public function calculate(PHPExcel_Cell $pCell = null) {
		// Return value
		$returnValue = '';

		// Is the value present in calculation cache?
		if (isset( $this->_calculationCache[ $pCell->getParent()->getTitle() ][$pCell->getCoordinate()] )) {
			if (time() - $this->_calculationCache[ $pCell->getParent()->getTitle() ][$pCell->getCoordinate()]['time'] < self::MAX_CACHE_SECONDS) {
				return $this->_calculationCache[ $pCell->getParent()->getTitle() ][$pCell->getCoordinate()]['data'];
			} else {
				unset($this->_calculationCache[ $pCell->getParent()->getTitle() ][$pCell->getCoordinate()]);
			}
		}

		// Formula
		$formula = $pCell->getValue();

		// Executable formula array
		$executableFormulaArray = array();

		// Parse formula into a tree of tokens
		$objParser = new PHPExcel_Calculation_FormulaParser($formula);

		// Loop trough parsed tokens and create an executable formula
		$inFunction = false;
		$token 		= null;
		for ($i = 0; $i < $objParser->getTokenCount(); $i++) {
			$token = $objParser->getToken($i);

			// Is it a cell reference? Not in a function?
			if ( ($token->getTokenType() == PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_OPERAND) &&
				 ($token->getTokenSubType() == PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_RANGE) &&
				 (strpos($token->getValue(), ':') === false) &&
				 (!$inFunction) ) {
				 	// Adjust reference
				 	$reference = str_replace('$', '', $token->getValue());

				 	// Add to executable formula array
				 	array_push(
				 		$executableFormulaArray,
				 		$pCell->getParent()->getCell( $reference )->getCalculatedValue()
				 	);

				 	continue;
			}

			// Is it a cell reference? In a function?
			if ( ($token->getTokenType() == PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_OPERAND) &&
				 ($token->getTokenSubType() == PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_RANGE) &&
				 ($inFunction) ) {
				 	// Adjust reference
				 	$reference = str_replace('$', '', $token->getValue());

				 	// Add to executable formula array
				 	array_push(
				 		$executableFormulaArray,
				 		'$this->extractRange("' . $reference . '", $pCell->getParent())'
				 	);

				 	continue;
			}

			// Is it a concatenation operator?
			if ( ($token->getTokenType() == PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_OPERATORINFIX) &&
				 ($token->getTokenSubType() == PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_CONCATENATION) ) {
				 	// Add to executable formula array
				 	array_push($executableFormulaArray, '.');

				 	continue;
			}

			// Is it a logical operator?
			if ( ($token->getTokenType() == PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_OPERATORINFIX) &&
				 ($token->getTokenSubType() == PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_LOGICAL) ) {
					// Temporary variable
					$tmp = '';
					switch($token->getValue()) {
						case '=':
							$tmp = '==';
							break;
						case '<>':
							$tmp = '!=';
							break;
						default:
							$tmp = $token->getValue();
					}

				 	// Add to executable formula array
				 	array_push($executableFormulaArray, $tmp);

				 	continue;
			}

			// Is it a subexpression?
			if ($token->getTokenType() == PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_SUBEXPRESSION) {
				// Temporary variable
				$tmp = '';
				switch($token->getTokenSubType()) {
					case PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_START:
						$tmp = '(';
						break;
					case PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_STOP:
						$tmp = ')';
						break;
				}

				// Add to executable formula array
				array_push($executableFormulaArray, $tmp);

				continue;
			}

			// Is it a function?
			if ($token->getTokenType() == PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_FUNCTION) {
				// Temporary variable
				$tmp = '';

				// Check the function type
				if ($token->getValue() == 'ARRAY' || $token->getValue() == 'ARRAYROW') {
					// An array or an array row...
					$tmp = 'array(';
				} else {
					// A regular function call...
					switch($token->getTokenSubType()) {
						case PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_START:
							// Check if the function call is allowed...
					    	if (!isset($this->_functionMappings[strtoupper($token->getValue())])) {
					    		return '#NAME?';
					    	}

					    	// Map the function call
					    	$tmp = $this->_functionMappings[strtoupper($token->getValue())]->getPHPExcelName() . '(';
							$inFunction = true;
							break;
						case PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_STOP:
							$tmp = ')';
							break;
					}
				}

				// Add to executable formula array
				array_push($executableFormulaArray, $tmp);

				continue;
			}

			// Is it text?
			if ( ($token->getTokenType() == PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_OPERAND) &&
				 ($token->getTokenSubType() == PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_TEXT) ) {
				 	// Add to executable formula array
				 	array_push($executableFormulaArray, '"' . $token->getValue() . '"');

				 	continue;
			}
			
			// Is it a number?
			if ( ($token->getTokenType() == PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_OPERAND) &&
				 ($token->getTokenSubType() == PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_NUMBER) ) {
				 	// Add to executable formula array
				 	array_push($executableFormulaArray, $token->getValue());

				 	continue;
			}

			// Is it an error? Add it as text...
			if ( ($token->getTokenType() == PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_OPERAND) &&
				 ($token->getTokenSubType() == PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_ERROR) ) {
				 	// Add to executable formula array
				 	array_push($executableFormulaArray, '"' . $token->getValue() . '"');

				 	continue;
			}

			// Is it something else?
			array_push($executableFormulaArray, $token->getValue());
		}

		// Evaluate formula
		try {
			$formula = implode('', $executableFormulaArray);
			$formula = str_replace('(,', '(null,', $formula);
			$formula = str_replace(',,', ',null,', $formula);
			$formula = str_replace(',)', ',null)', $formula);

			$formula = str_replace('$this', '$pThat', $formula);

			/*
			 * The following code block can cause an error like:
			 *      Fatal error: Unsupported operand types in ...: runtime-created function on line 1
			 *
			 * This is due to the fact that a FATAL error is an E_ERROR,
			 * and it can not be caught using try/catch or any other
			 * Exception/error handling feature in PHP.
			 *
			 * A feature request seems to be made once, but it has been
			 * closed without any deliverables:
			 *      http://bugs.php.net/bug.php?id=40014
			 */
			$temporaryCalculationFunction = @create_function('$pThat, $pCell', "return $formula;");
			if ($temporaryCalculationFunction === FALSE) {
				$returnValue = '#N/A';
			} else {
	    		$returnValue = call_user_func_array($temporaryCalculationFunction, array(&$this, &$pCell));
			}
		} catch (Exception $ex) {
			$returnValue = '#N/A';
		}

		// Save to calculation cache
		$this->_calculationCache[ $pCell->getParent()->getTitle() ][$pCell->getCoordinate()]['time'] = time();
		$this->_calculationCache[ $pCell->getParent()->getTitle() ][$pCell->getCoordinate()]['data'] = $returnValue;

	    // Return result
	    return $returnValue;
	}

	/**
	 * __clone implementation. Cloning should not be allowed in a Singleton!
	 *
	 * @throws	Exception
	 */
	public final function __clone() {
		throw new Exception("Cloning a Singleton is not allowed!");
	}

	/**
	 * Extract range values
	 *
	 * @param 	string				$pRange		String based range representation
	 * @param 	PHPExcel_Worksheet	$pSheet		Worksheet
	 * @return  array				Array of values in range
	 * @throws 	Exception
	 */
	public function extractRange($pRange = 'A1', PHPExcel_Worksheet $pSheet = null) {
		// Return value
		$returnValue = array();

		// Worksheet given?
		if (!is_null($pSheet)) {
			// Extract range
			$aReferences = PHPExcel_Cell::extractAllCellReferencesInRange($pRange);

			// Extract cell data
			foreach ($aReferences as $reference) {
				// Extract range
				$currentCol 	= 0;
				$currentRow 	= 0;
				list($currentCol, $currentRow)	= PHPExcel_Cell::coordinateFromString($reference);

				$returnValue[$currentCol][$currentRow] = $pSheet->getCell($reference)->getCalculatedValue();
			}
		}

		// Return
		return $returnValue;
	}

	/**
	 * Is a specific function implemented?
	 *
	 * @param 	string	$pFunction	Function
	 * @return 	boolean
	 */
	public function isImplemented($pFunction = '') {
		$pFunction = strtoupper($pFunction);
		if (isset($this->_functionMappings[$pFunction])) {
			return $this->_functionMappings[$pFunction]->getPHPExcelName() == 'PHPExcel_Calculation_Functions::DUMMY';
		} else {
			return false;
		}
	}

	/**
	 * Get a list of implemented functions
	 *
	 * @return 	array
	 */
	public function listFunctions() {
		// Return value
		$returnValue = array();

		// Loop functions
		foreach ($this->_functionMappings as $key => $value) {
			if ($value->getPHPExcelName() != 'PHPExcel_Calculation_Functions::DUMMY') {
				$returnValue[] = $value;
			}
		}

		// Return
		return $returnValue;
	}

	/**
	 * Get a list of implemented Excel function names
	 *
	 * @return 	array
	 */
	public function listFunctionNames() {
		// Return value
		$returnValue = array();

		// Function names
		$aFunctions = $this->listFunctions();

		// Loop functions
		foreach ($aFunctions as $key => $value) {
			$returnValue[] = $value->getExcelName();
		}

		// Return
		return $returnValue;
	}
}