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
 * PHPExcel_Style_Border
 *
 * @category   PHPExcel
 * @package    PHPExcel_Style
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Style_Border implements PHPExcel_IComparable
{
	/* Border style */
	const BORDER_NONE				= 'none';
	const BORDER_DASHDOT			= 'dashDot';
	const BORDER_DASHDOTDOT			= 'dashDotDot';
	const BORDER_DASHED				= 'dashed';
	const BORDER_DOTTED				= 'dotted';
	const BORDER_DOUBLE				= 'double';
	const BORDER_HAIR				= 'hair';
	const BORDER_MEDIUM				= 'medium';
	const BORDER_MEDIUMDASHDOT		= 'mediumDashDot';
	const BORDER_MEDIUMDASHDOTDOT	= 'mediumDashDotDot';
	const BORDER_MEDIUMDASHED		= 'mediumDashed';
	const BORDER_SLANTDASHDOT		= 'slantDashDot';
	const BORDER_THICK				= 'thick';
	const BORDER_THIN				= 'thin';
	
	/**
	 * Border style
	 *
	 * @var string
	 */
	private $_borderStyle;
	
	/**
	 * Border color
	 * 
	 * @var PHPExcel_Style_Color
	 */
	private $_borderColor;
	
    /**
     * Create a new PHPExcel_Style_Border
     */
    public function __construct()
    {
    	// Initialise values
		$this->_borderStyle			= PHPExcel_Style_Border::BORDER_NONE;
		$this->_borderColor			= new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_BLACK);
    }
    
    /**
     * Apply styles from array
     * 
     * <code>
     * $objPHPExcel->getActiveSheet()->getStyle('B2')->getBorders()->getTop()->applyFromArray(
     * 		array(
     * 			'style' => PHPExcel_Style_Border::BORDER_DASHDOT,
     * 			'color' => array(
     * 				'rgb' => '808080'
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
    		if (array_key_exists('style', $pStyles)) {
    			$this->setBorderStyle($pStyles['style']);
    		}
    	    if (array_key_exists('color', $pStyles)) {
    			$this->getColor()->applyFromArray($pStyles['color']);
    		}
    	} else {
    		throw new Exception("Invalid style array passed.");
    	}
    }
    
    /**
     * Get Border style
     *
     * @return string
     */
    public function getBorderStyle() {
    	return $this->_borderStyle;
    }
    
    /**
     * Set Border style
     *
     * @param string $pValue
     */
    public function setBorderStyle($pValue = PHPExcel_Style_Border::BORDER_NONE) {
        if ($pValue == '') {
    		$pValue = PHPExcel_Style_Border::BORDER_NONE;
    	}
    	$this->_borderStyle = $pValue;
    }
    
    /**
     * Get Border Color
     *
     * @return PHPExcel_Style_Color
     */
    public function getColor() {
    	return $this->_borderColor;
    }
    
    /**
     * Set Border Color
     *
     * @param 	PHPExcel_Style_Color $pValue
     * @throws 	Exception
     */
    public function setColor(PHPExcel_Style_Color $pValue = null) {
   		$this->_borderColor = $pValue;
    }
    
	/**
	 * Get hash code
	 *
	 * @return string	Hash code
	 */	
	public function getHashCode() {
    	return md5(
    		  $this->_borderStyle
    		. $this->_borderColor->getHashCode()
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
