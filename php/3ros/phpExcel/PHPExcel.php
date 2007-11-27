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


/** PHPExcel_Cell */
require_once 'PHPExcel/Cell.php';

/** PHPExcel_DocumentProperties */
require_once 'PHPExcel/DocumentProperties.php';

/** PHPExcel_DocumentSecurity */
require_once 'PHPExcel/DocumentSecurity.php';

/** PHPExcel_Worksheet */
require_once 'PHPExcel/Worksheet.php';

/** PHPExcel_Shared_ZipStreamWrapper */
require_once 'PHPExcel/Shared/ZipStreamWrapper.php';


/**
 * PHPExcel
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel
{
	/**
	 * Document properties
	 *
	 * @var PHPExcel_DocumentProperties
	 */
	private $_properties;
	
	/**
	 * Document security
	 *
	 * @var PHPExcel_DocumentSecurity
	 */
	private $_security;
	
	/**
	 * Collection of Worksheet objects
	 *
	 * @var PHPExcel_Worksheet[]
	 */
	private $_workSheetCollection = array();
	
	/**
	 * Active sheet index
	 *
	 * @var int
	 */
	private $_activeSheetIndex = 0;
	
	/**
	 * Create a new PHPExcel with one Worksheet
	 */
	public function __construct()
	{
		// Initialise worksheet collection and add one worksheet
		$this->_workSheetCollection = array();
		$this->_workSheetCollection[] = new PHPExcel_Worksheet($this);
		$this->_activeSheetIndex = 0;
		
		// Create document properties
		$this->_properties = new PHPExcel_DocumentProperties();
		
		// Create document security
		$this->_security = new PHPExcel_DocumentSecurity();
	}
	
	/**
	 * Get properties
	 *
	 * @return PHPExcel_DocumentProperties
	 */
	public function getProperties()
	{
		return $this->_properties;
	}
	
	/**
	 * Set properties
	 *
	 * @param PHPExcel_DocumentProperties	$pValue
	 */
	public function setProperties(PHPExcel_DocumentProperties $pValue)
	{
		$this->_properties = $pValue;
	}
	
	/**
	 * Get security
	 *
	 * @return PHPExcel_DocumentSecurity
	 */
	public function getSecurity()
	{
		return $this->_security;
	}
	
	/**
	 * Set security
	 *
	 * @param PHPExcel_DocumentSecurity	$pValue
	 */
	public function setSecurity(PHPExcel_DocumentSecurity $pValue)
	{
		$this->_security = $pValue;
	}
	
	/**
	 * Get active sheet
	 *
	 * @return PHPExcel_Worksheet
	 */
	public function getActiveSheet()
	{
		return $this->_workSheetCollection[$this->_activeSheetIndex];
	}
	
	/**
	 * Create sheet and add it to this workbook
	 *
	 * @return PHPExcel_Worksheet
	 */
	public function createSheet()
	{
		$newSheet = new PHPExcel_Worksheet($this);
		
		$this->addSheet($newSheet);
		
		return $newSheet;
	}
	
	/**
	 * Add sheet
	 *
	 * @param PHPExcel_Worksheet $pSheet
	 * @throws Exception
	 */
	public function addSheet(PHPExcel_Worksheet $pSheet = null)
	{
		$this->_workSheetCollection[] = $pSheet;
	}
	
	/**
	 * Remove sheet by index
	 *
	 * @param int $pIndex Active sheet index
	 * @throws Exception
	 */
	public function removeSheetByIndex($pIndex = 0)
	{
		if ($pIndex > count($this->_workSheetCollection) - 1) {
			throw new Exception("Sheet index is out of bounds.");
		} else {
			array_splice($this->_workSheetCollection, $pIndex, 1);
		}
	}
	
	/**
	 * Get sheet by index
	 *
	 * @param int $pIndex Active sheet index
	 * @return PHPExcel_Worksheet
	 * @throws Exception
	 */
	public function getSheet($pIndex = 0)
	{
		if ($pIndex > count($this->_workSheetCollection) - 1) {
			throw new Exception("Sheet index is out of bounds.");
		} else {
			return $this->_workSheetCollection[$pIndex];
		}
	}
	
	/**
	 * Get sheet count
	 *
	 * @return int
	 */
	public function getSheetCount()
	{
		return count($this->_workSheetCollection);
	}
	
	/**
	 * Get active sheet index
	 *
	 * @return int Active sheet index
	 */
	public function getActiveSheetIndex()
	{
		return $this->_activeSheetIndex;
	}
	
	/**
	 * Set active sheet index
	 *
	 * @param int $pIndex Active sheet index
	 * @throws Exception
	 */
	public function setActiveSheetIndex($pIndex = 0)
	{
		if ($pIndex > count($this->_workSheetCollection) - 1) {
			throw new Exception("Active sheet index is out of bounds.");
		} else {
			$this->_activeSheetIndex = $pIndex;
		}
	}
	
	/**
	 * Get sheet names
	 *
	 * @return string[]
	 */
	public function getSheetNames()
	{
		$returnValue = array();
		for ($i = 0; $i < $this->getSheetCount(); $i++) {
			array_push($returnValue, $this->getSheet($i)->getTitle());
		}
		
		return $returnValue;
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
