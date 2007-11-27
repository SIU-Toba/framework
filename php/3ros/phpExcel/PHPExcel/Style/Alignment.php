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


/** PHPExcel_IComparable */
require_once 'PHPExcel/IComparable.php';


/**
 * PHPExcel_Style_Alignment
 *
 * @category   PHPExcel
 * @package    PHPExcel_Style
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Style_Alignment implements PHPExcel_IComparable
{	
	/* Horizontal alignment styles */
	const HORIZONTAL_GENERAL				= 'general';
	const HORIZONTAL_LEFT					= 'left';
	const HORIZONTAL_RIGHT					= 'right';
	const HORIZONTAL_CENTER					= 'center';
	const HORIZONTAL_JUSTIFY				= 'justify';
	
	/* Vertical alignment styles */
	const VERTICAL_BOTTOM					= 'bottom';
	const VERTICAL_TOP						= 'top';
	const VERTICAL_CENTER					= 'center';
	const VERTICAL_JUSTIFY					= 'justify';
	
	/**
	 * Horizontal
	 *
	 * @var string
	 */
	private $_horizontal;
	
	/**
	 * Vertical
	 *
	 * @var string
	 */
	private $_vertical;
	
	/**
	 * Text rotation
	 *
	 * @var int
	 */
	private $_textRotation;
	
	/**
	 * Wrap text
	 *
	 * @var boolean
	 */
	private $_wrapText;
		
    /**
     * Create a new PHPExcel_Style_Alignment
     */
    public function __construct()
    {
    	// Initialise values
    	$this->_horizontal			= PHPExcel_Style_Alignment::HORIZONTAL_GENERAL;
    	$this->_vertical			= PHPExcel_Style_Alignment::VERTICAL_BOTTOM;
    	$this->_textRotation		= 0;
    	$this->_wrapText			= false;
    }
    
    /**
     * Apply styles from array
     * 
     * <code>
     * $objPHPExcel->getActiveSheet()->getStyle('B2')->getAlignment()->applyFromArray(
     * 		array(
     * 			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
     * 			'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
     * 			'rotation'   => 0,
     * 			'wrap'       => true
     * 		)
     * );
     * </code>
     * 
     * @param	array	$pStyles	Array containing style information
     * @throws	Exception
     */
    public function applyFromArray($pStyles = null) {
        if (is_array($pStyles)) {
        	if (array_key_exists('horizontal', $pStyles)) {
    			$this->setHorizontal($pStyles['horizontal']);
    		}
        	if (array_key_exists('vertical', $pStyles)) {
    			$this->setVertical($pStyles['vertical']);
    		}
        	if (array_key_exists('rotation', $pStyles)) {
    			$this->setTextRotation($pStyles['rotation']);
    		}
        	if (array_key_exists('wrap', $pStyles)) {
    			$this->setWrapText($pStyles['wrap']);
    		}
    	} else {
    		throw new Exception("Invalid style array passed.");
    	}
    }
    
    /**
     * Get Horizontal
     *
     * @return string
     */
    public function getHorizontal() {
    	return $this->_horizontal;
    }
    
    /**
     * Set Horizontal
     *
     * @param string $pValue
     */
    public function setHorizontal($pValue = PHPExcel_Style_Alignment::HORIZONTAL_GENERAL) {
        if ($pValue == '') {
    		$pValue = PHPExcel_Style_Alignment::HORIZONTAL_GENERAL;
    	}
    	$this->_horizontal = $pValue;
    }
    
    /**
     * Get Vertical
     *
     * @return string
     */
    public function getVertical() {
    	return $this->_vertical;
    }
    
    /**
     * Set Vertical
     *
     * @param string $pValue
     */
    public function setVertical($pValue = PHPExcel_Style_Alignment::VERTICAL_BOTTOM) {
    	if ($pValue == '') {
    		$pValue = PHPExcel_Style_Alignment::VERTICAL_BOTTOM;
    	}
    	$this->_vertical = $pValue;
    }
    
    /**
     * Get TextRotation
     *
     * @return int
     */
    public function getTextRotation() {
    	return $this->_textRotation;
    }
    
    /**
     * Set TextRotation
     *
     * @param int $pValue
     * @throws Exception
     */
    public function setTextRotation($pValue = 0) {
    	if ($pValue >= -90 && $pValue <= 90) {
    		$this->_textRotation = $pValue;
    	} else {
    		throw new Exception("Text rotation should be a value between -90 and 90.");
    	}
    }
    
    /**
     * Get Wrap Text
     *
     * @return boolean
     */
    public function getWrapText() {
    	return $this->_wrapText;
    }
    
    /**
     * Set Wrap Text
     *
     * @param boolean $pValue
     */
    public function setWrapText($pValue = false) {
    	if ($pValue == '') {
    		$pValue = false;
    	}
    	$this->_wrapText = $pValue;
    }

	/**
	 * Get hash code
	 *
	 * @return string	Hash code
	 */	
	public function getHashCode() {
    	return md5(
    		  $this->_horizontal
    		. $this->_vertical
    		. $this->_textRotation
    		. $this->_wrapText
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