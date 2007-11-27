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
 * @package    PHPExcel_Worksheet
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/lgpl.txt	LGPL
 * @version    1.5.0, 2007-10-23
 */


/**
 * PHPExcel_Worksheet_ColumnDimension
 *
 * @category   PHPExcel
 * @package    PHPExcel_Worksheet
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Worksheet_ColumnDimension
{			
	/**
	 * Column index
	 *
	 * @var int
	 */
	private $_columnIndex;
	
	/**
	 * Column width
	 *
	 * When this is set to a negative value, the column width should be ignored by IWriter
	 *
	 * @var double
	 */
	private $_width;
	
	/**
	 * Auto size?
	 *
	 * @var bool
	 */
	private $_autoSize;
	
	/**
	 * Visible?
	 *
	 * @var bool
	 */
	private $_visible;
		
    /**
     * Create a new PHPExcel_Worksheet_RowDimension
     *
     * @param string $pIndex Character column index
     */
    public function __construct($pIndex = 'A')
    {
    	// Initialise values
    	$this->_columnIndex		= $pIndex;
    	$this->_width			= -1;
    	$this->_autoSize		= false;
    	$this->_visible			= true;
    }
    
    /**
     * Get ColumnIndex
     *
     * @return string
     */
    public function getColumnIndex() {
    	return $this->_columnIndex;
    }
    
    /**
     * Set ColumnIndex
     *
     * @param string $pValue
     */
    public function setColumnIndex($pValue) {
    	$this->_columnIndex = $pValue;
    }
    
    /**
     * Get Width
     *
     * @return double
     */
    public function getWidth() {
    	return $this->_width;
    }
    
    /**
     * Set Width
     *
     * @param double $pValue
     */
    public function setWidth($pValue = -1) {
    	$this->_width = $pValue;
    }
    
    /**
     * Get Auto Size
     *
     * @return bool
     */
    public function getAutoSize() {
    	return $this->_autoSize;
    }
    
    /**
     * Set Auto Size
     *
     * @param bool $pValue
     */
    public function setAutoSize($pValue = false) {
    	$this->_autoSize = $pValue;
    }
    
    /**
     * Get Visible
     *
     * @return bool
     */
    public function getVisible() {
    	return $this->_visible;
    }
    
    /**
     * Set Visible
     *
     * @param bool $pValue
     */
    public function setVisible($pValue = true) {
    	$this->_visible = $pValue;
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
