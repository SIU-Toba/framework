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
 * @package    PHPExcel_Cell
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/lgpl.txt	LGPL
 * @version    1.5.0, 2007-10-23
 */


/**
 * PHPExcel_Cell_Hyperlink
 *
 * @category   PHPExcel
 * @package    PHPExcel_Cell
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Cell_Hyperlink
{
	/**
	 * Cell representing the hyperlink
	 *
	 * @var PHPExcel_Cell
	 */
	private $_cell;
	
	/**
	 * URL to link the cell to
	 *
	 * @var string
	 */
	private $_url;
	
	/**
	 * Tooltip to display on the hyperlink
	 *
	 * @var string
	 */
	private $_tooltip;	
	
    /**
     * Create a new PHPExcel_Cell_Hyperlink
     *
     * @param 	PHPExcel_Cell		$pCell		Parent cell
     * @param 	string				$pUrl		Url to link the cell to
     * @param	string				$pTooltip	Tooltip to display on the hyperlink
     * @throws	Exception
     */
    public function __construct(PHPExcel_Cell $pCell = null, $pUrl = '', $pTooltip = '')
    {
    	// Initialise member variables
		$this->_url 		= $pUrl;
		$this->_tooltip 	= $pTooltip;
 	
    	// Set cell
    	$this->_parent 		= $pCell;
    }
	
	/**
	 * Get URL
	 *
	 * @return string
	 */
	public function getUrl() {
		return $this->_url;
	}

	/**
	 * Set URL
	 *
	 * @param	string	$value
	 */
	public function setUrl($value = '') {
		$this->_url = $value;
	}
	
	/**
	 * Get tooltip
	 *
	 * @return string
	 */
	public function getTooltip() {
		return $this->_tooltip;
	}

	/**
	 * Set tooltip
	 *
	 * @param	string	$value
	 */
	public function setTooltip($value = '') {
		$this->_tooltip = $value;
	}
	
    /**
     * Get parent
     *
     * @return PHPExcel_Cell
     */
    public function getParent() {
    	return $this->_parent;
    }
    
	/**
	 * Set Parent
	 *
	 * @param	PHPExcel_Cell	$value
	 */
	public function setParent($value = null) {
		$this->_parent = $value;
	}
	
	/**
	 * Get hash code
	 *
	 * @return string	Hash code
	 */	
	public function getHashCode() {
    	return md5(
    		  $this->_url
    		. $this->_tooltip
    		. $this->_parent->getCoordinate()
    		. __CLASS__
    	);
    }
}
