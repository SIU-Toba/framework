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
 * PHPExcel_Worksheet_RowDimension
 *
 * @category   PHPExcel
 * @package    PHPExcel_Worksheet
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Worksheet_RowDimension
{			
	/**
	 * Row index
	 *
	 * @var int
	 */
	private $_rowIndex;
	
	/**
	 * Row height (in pt)
	 *
	 * When this is set to a negative value, the row height should be ignored by IWriter
	 *
	 * @var double
	 */
	private $_rowHeight;
	
	/**
	 * Visible?
	 *
	 * @var bool
	 */
	private $_visible;
		
    /**
     * Create a new PHPExcel_Worksheet_RowDimension
     *
     * @param int $pIndex Numeric row index
     */
    public function __construct($pIndex = 0)
    {
    	// Initialise values
    	$this->_rowIndex		= $pIndex;
    	$this->_rowHeight		= -1;
    	$this->_visible			= true;
    }
    
    /**
     * Get Row Index
     *
     * @return int
     */
    public function getRowIndex() {
    	return $this->_rowIndex;
    }
    
    /**
     * Set Row Index
     *
     * @param int $pValue
     */
    public function setRowIndex($pValue) {
    	$this->_rowIndex = $pValue;
    }
    
    /**
     * Get Row Height
     *
     * @return double
     */
    public function getRowHeight() {
    	return $this->_rowHeight;
    }
    
    /**
     * Set Row Height
     *
     * @param double $pValue
     */
    public function setRowHeight($pValue = -1) {
    	$this->_rowHeight = $pValue;
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
