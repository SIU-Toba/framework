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


/** PHPExcel_Style_Color */
require_once 'PHPExcel/Style/Color.php';

/** PHPExcel_Style_Font */
require_once 'PHPExcel/Style/Font.php';

/** PHPExcel_Style_Fill */
require_once 'PHPExcel/Style/Fill.php';

/** PHPExcel_Style_Borders */
require_once 'PHPExcel/Style/Borders.php';

/** PHPExcel_Style_Alignment */
require_once 'PHPExcel/Style/Alignment.php';

/** PHPExcel_Style_NumberFormat */
require_once 'PHPExcel/Style/NumberFormat.php';

/** PHPExcel_Style_Conditional */
require_once 'PHPExcel/Style/Conditional.php';

/** PHPExcel_Style_Protection */
require_once 'PHPExcel/Style/Protection.php';

/** PHPExcel_IComparable */
require_once 'PHPExcel/IComparable.php';

/**
 * PHPExcel_Style
 *
 * @category   PHPExcel
 * @package    PHPExcel_Cell
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Style implements PHPExcel_IComparable
{
	/**
	 * Font
	 *
	 * @var PHPExcel_Style_Font
	 */
	private $_font;
	
	/**
	 * Fill
	 *
	 * @var PHPExcel_Style_Fill
	 */
	private $_fill;
	
	/**
	 * Borders
	 *
	 * @var PHPExcel_Style_Borders
	 */
	private $_borders;
	
	/**
	 * Alignment
	 *
	 * @var PHPExcel_Style_Alignment
	 */
	private $_alignment;
	
	/**
	 * Number Format
	 *
	 * @var PHPExcel_Style_NumberFormat
	 */
	private $_numberFormat;
	
	/**
	 * Conditional styles
	 *
	 * @var PHPExcel_Style_Conditional[]
	 */
	private $_conditionalStyles;
	
	/**Protection
	 *
	 * @var PHPExcel_Style_Protection
	 */
	private $_protection;
		
    /**
     * Create a new PHPExcel_Style
     */
    public function __construct()
    {
    	// Initialise values
    	$this->_fill				= new PHPExcel_Style_Fill();
    	$this->_font				= new PHPExcel_Style_Font();
    	$this->_borders				= new PHPExcel_Style_Borders();
    	$this->_alignment			= new PHPExcel_Style_Alignment();
    	$this->_numberFormat		= new PHPExcel_Style_NumberFormat();
    	$this->_conditionalStyles 	= array();
    	$this->_protection			= new PHPExcel_Style_Protection();
    }
    
    /**
     * Apply styles from array
     * 
     * <code>
     * $objPHPExcel->getActiveSheet()->getStyle('B2')->applyFromArray(
     * 		array(
     * 			'font'    => array(
     * 				'name'      => 'Arial',
     * 				'bold'      => true,
     * 				'italic'    => false,
     * 				'underline' => PHPExcel_Style_Font::UNDERLINE_DOUBLE,
     * 				'strike'    => false,
     * 				'color'     => array(
     * 					'rgb' => '808080'
     * 				)
     * 			),
     * 			'borders' => array(
     * 				'bottom'     => array(
     * 					'style' => PHPExcel_Style_Border::BORDER_DASHDOT,
     * 					'color' => array(
     * 						'rgb' => '808080'
     * 					)
     * 				),
     * 				'top'     => array(
     * 					'style' => PHPExcel_Style_Border::BORDER_DASHDOT,
     * 					'color' => array(
     * 						'rgb' => '808080'
     * 					)
     * 				)
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
        	if (array_key_exists('fill', $pStyles)) {
        		$this->getFill()->applyFromArray($pStyles['fill']);
        	}
          	if (array_key_exists('font', $pStyles)) {
        		$this->getFont()->applyFromArray($pStyles['font']);
        	}
        	if (array_key_exists('borders', $pStyles)) {
        		$this->getBorders()->applyFromArray($pStyles['borders']);
        	}
        	if (array_key_exists('alignment', $pStyles)) {
        		$this->getAlignment()->applyFromArray($pStyles['alignment']);
        	}
           	if (array_key_exists('numberformat', $pStyles)) {
        		$this->getNumberFormat()->applyFromArray($pStyles['numberformat']);
        	}
        	if (array_key_exists('protection', $pStyles)) {
        		$this->getProtection()->applyFromArray($pStyles['protection']);
        	}
    	} else {
    		throw new Exception("Invalid style array passed.");
    	}
    }
    
    /**
     * Get Fill
     *
     * @return PHPExcel_Style_Fill
     */
    public function getFill() {
    	return $this->_fill;
    }
    
    /**
     * Get Font
     *
     * @return PHPExcel_Style_Font
     */
    public function getFont() {
    	return $this->_font;
    }
    
    /**
     * Get Borders
     *
     * @return PHPExcel_Style_Borders
     */
    public function getBorders() {
    	return $this->_borders;
    }
    
    /**
     * Get Alignment
     *
     * @return PHPExcel_Style_Alignment
     */
    public function getAlignment() {
    	return $this->_alignment;
    }
    
    /**
     * Get Number Format
     *
     * @return PHPExcel_Style_NumberFormat
     */
    public function getNumberFormat() {
    	return $this->_numberFormat;
    }
    
    /**
     * Get Conditional Styles
     *
     * @return PHPExcel_Style_Conditional[]
     */
    public function getConditionalStyles() {
    	return $this->_conditionalStyles;
    }
       
    /**
     * Set Conditional Styles
     *
     * @param PHPExcel_Style_Conditional[]	$pValue	Array of condtional styles
     */
    public function setConditionalStyles($pValue = null) {
    	if (is_array($pValue)) {
    		$this->_conditionalStyles = $pValue;
    	}
    }
    
    /**
     * Get Protection
     *
     * @return PHPExcel_Style_Protection
     */
    public function getProtection() {
    	return $this->_protection;
    }
    
	/**
	 * Get hash code
	 *
	 * @return string	Hash code
	 */	
	public function getHashCode() {
		$hashConditionals = '';
		foreach ($this->_conditionalStyles as $conditional) {
			$hashConditionals .= $conditional->getHashCode();
		}
		
    	return md5(
    		  $this->_fill->getHashCode()
    		. $this->_font->getHashCode()
    		. $this->_borders->getHashCode()
    		. $this->_alignment->getHashCode()
    		. $this->_numberFormat->getHashCode()
    		. $hashConditionals
    		. $this->_protection->getHashCode()
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
