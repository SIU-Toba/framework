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
 * @package    PHPExcel_Style
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/lgpl.txt	LGPL
 * @version    1.5.0, 2007-10-23
 */


/** PHPExcel_Style_Color */
require_once 'PHPExcel/Style/Color.php';

/** PHPExcel_IComparable */
require_once 'PHPExcel/IComparable.php';


/**
 * PHPExcel_Style_Fill
 *
 * @category   PHPExcel
 * @package    PHPExcel_Style
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Style_Fill implements PHPExcel_IComparable
{
	/* Fill types */
	const FILL_NONE							= 'none';
	const FILL_SOLID						= 'solid';
	const FILL_GRADIENT_LINEAR				= 'linear';
	const FILL_GRADIENT_PATH				= 'path';
	const FILL_PATTERN_DARKDOWN				= 'darkDown';
	const FILL_PATTERN_DARKGRAY				= 'darkGray';
	const FILL_PATTERN_DARKGRID				= 'darkGrid';
	const FILL_PATTERN_DARKHORIZONTAL		= 'darkHorizontal';
	const FILL_PATTERN_DARKTRELLIS			= 'darkTrellis';
	const FILL_PATTERN_DARKUP				= 'darkUp';
	const FILL_PATTERN_DARKVERTICAL			= 'darkVertical';
	const FILL_PATTERN_GRAY0625				= 'gray0625';
	const FILL_PATTERN_GRAY125				= 'gray125';
	const FILL_PATTERN_LIGHTDOWN			= 'lightDown';
	const FILL_PATTERN_LIGHTGRAY			= 'lightGray';
	const FILL_PATTERN_LIGHTGRID			= 'lightGrid';
	const FILL_PATTERN_LIGHTHORIZONTAL		= 'lightHorizontal';
	const FILL_PATTERN_LIGHTTRELLIS			= 'lightTrellis';
	const FILL_PATTERN_LIGHTUP				= 'lightUp';
	const FILL_PATTERN_LIGHTVERTICAL		= 'lightVertical';
	const FILL_PATTERN_MEDIUMGRAY			= 'mediumGray';

	/**
	 * Fill type
	 *
	 * @var string
	 */
	private $_fillType;
	
	/**
	 * Rotation
	 *
	 * @var double
	 */
	private $_rotation;
	
	/**
	 * Start color
	 * 
	 * @var PHPExcel_Style_Color
	 */
	private $_startColor;
	
	/**
	 * End color
	 * 
	 * @var PHPExcel_Style_Color
	 */
	private $_endColor;
		
    /**
     * Create a new PHPExcel_Style_Fill
     */
    public function __construct()
    {
    	// Initialise values
    	$this->_fillType			= PHPExcel_Style_Fill::FILL_NONE;
    	$this->_rotation			= 0;
		$this->_startColor			= new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_WHITE);
		$this->_endColor			= new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_BLACK);
    }
    
    /**
     * Apply styles from array
     * 
     * <code>
     * $objPHPExcel->getActiveSheet()->getStyle('B2')->getFill()->applyFromArray(
     * 		array(
     * 			'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
     * 			'rotation'   => 0,
     * 			'startcolor' => array(
     * 				'rgb' => '000000'
     * 			),
     * 			'endcolor'   => array(
     * 				'argb' => 'FFFFFFFF'
     * 			)
     * 		)
     * );
     * </code>
     * 
     * @param	array	$pStyles	Array containing style information
     * @throws	Exception
     */
    public function applyFromArray($pStyles = null) {
    	if (is_array($pStyles)) {
    	    if (array_key_exists('type', $pStyles)) {
    			$this->setFillType($pStyles['type']);
    		}
    	    if (array_key_exists('rotation', $pStyles)) {
    			$this->setRotation($pStyles['rotation']);
    		}
    	    if (array_key_exists('startcolor', $pStyles)) {
    			$this->getStartColor()->applyFromArray($pStyles['startcolor']);
    		}
    	    if (array_key_exists('endcolor', $pStyles)) {
    			$this->getEndColor()->applyFromArray($pStyles['endcolor']);
    		}
    	    if (array_key_exists('color', $pStyles)) {
    			$this->getStartColor()->applyFromArray($pStyles['color']);
    		}
    	} else {
    		throw new Exception("Invalid style array passed.");
    	}
    }
    
    /**
     * Get Fill Type
     *
     * @return string
     */
    public function getFillType() {
    	if ($this->_fillType == '') {
    		$this->_fillType = self::FILL_NONE;
    	}
    	return $this->_fillType;
    }
    
    /**
     * Set Fill Type
     *
     * @param string $pValue	PHPExcel_Style_Fill fill type
     */
    public function setFillType($pValue = PHPExcel_Style_Fill::FILL_NONE) {
    	$this->_fillType = $pValue;
    }
    
    /**
     * Get Rotation
     *
     * @return double
     */
    public function getRotation() {
    	return $this->_rotation;
    }
    
    /**
     * Set Rotation
     *
     * @param double $pValue
     */
    public function setRotation($pValue = 0) {
    	$this->_rotation = $pValue;
    }
    
    /**
     * Get Start Color
     *
     * @return PHPExcel_Style_Color
     */
    public function getStartColor() {
    	return $this->_startColor;
    }
    
    /**
     * Set Start Color
     *
     * @param 	PHPExcel_Style_Color $pValue
     * @throws 	Exception
     */
    public function setStartColor(PHPExcel_Style_Color $pValue = null) {
   		$this->_startColor = $pValue;
    }
    
    /**
     * Get End Color
     *
     * @return PHPExcel_Style_Color
     */
    public function getEndColor() {
    	return $this->_endColor;
    }
    
    /**
     * Set End Color
     *
     * @param 	PHPExcel_Style_Color $pValue
     * @throws 	Exception
     */
    public function setEndColor(PHPExcel_Style_Color $pValue = null) {
   		$this->_endColor = $pValue;
    }

	/**
	 * Get hash code
	 *
	 * @return string	Hash code
	 */	
	public function getHashCode() {
    	return md5(
    		  $this->_fillType
    		. $this->_rotation
    		. $this->_startColor->getHashCode()
    		. $this->_endColor->getHashCode()
    		. __CLASS__
    	);
    }
        
	/**
	 * Implement PHP __clone to create a deep clone, not just a shallow copy.
	 */
	public function __clone() {
		$vars = get_object_vars($this);
		foreach ($vars as $key => $value) {
			if (is_object($value)) {
				$this->$key = clone $value;
			} else {
				$this->$key = $value;
			}
		}
	}
}
