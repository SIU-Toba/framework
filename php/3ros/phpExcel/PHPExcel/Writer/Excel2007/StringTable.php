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
 * @package    PHPExcel_Writer_Excel2007
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/lgpl.txt	LGPL
 * @version    1.5.0, 2007-10-23
 */


/** PHPExcel_Writer_Excel2007 */
require_once 'PHPExcel/Writer/Excel2007.php';

/** PHPExcel_Writer_Excel2007_WriterPart */
require_once 'PHPExcel/Writer/Excel2007/WriterPart.php';

/** PHPExcel_Cell_DataType */
require_once 'PHPExcel/Cell/DataType.php';

/** PHPExcel_Shared_XMLWriter */
require_once 'PHPExcel/Shared/XMLWriter.php';


/**
 * PHPExcel_Writer_Excel2007_StringTable
 *
 * @category   PHPExcel
 * @package    PHPExcel_Writer_Excel2007
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Writer_Excel2007_StringTable extends PHPExcel_Writer_Excel2007_WriterPart
{
	/**
	 * Create worksheet stringtable
	 *
	 * @param 	PHPExcel_Worksheet 	$pSheet				Worksheet
	 * @param 	string[] 				$pExistingTable 	Existing table to eventually merge with
	 * @return 	string[] 				String table for worksheet
	 * @throws 	Exception
	 */
	public function createStringTable($pSheet = null, $pExistingTable = null)
	{
		if (!is_null($pSheet)) {
			// Create string lookup table
			$aStringTable = array();
			$cellCollection = null;
			$aFlippedStringTable = null;	// For faster lookup
		
			// Is an existing table given?
			if (!is_null($pExistingTable) && is_array($pExistingTable)) {
				$aStringTable = $pExistingTable;
			}
			
			// Fill index array
			$aFlippedStringTable = array_flip($aStringTable);
			
	        // Loop trough cells
	        $cellCollection = $pSheet->getCellCollection();
	        foreach ($cellCollection as $cell) {
	        	if (!is_object($cell->getValue()) &&
	        		!isset($aFlippedStringTable[$cell->getValue()]) &&
	        		!is_null($cell->getValue()) &&
	        		$cell->getValue() != '' &&
	        		($cell->getDataType() == PHPExcel_Cell_DataType::TYPE_STRING || $cell->getDataType() == PHPExcel_Cell_DataType::TYPE_NULL)
	        		) {
	        			$aStringTable[] = $cell->getValue();
						$aFlippedStringTable[$cell->getValue()] = 1;
	        	}
	        }

	        // Return
	        return $aStringTable;
		} else {
			throw new Exception("Invalid PHPExcel_Worksheet object passed.");
		}
	}
	
	/**
	 * Write string table to XML format
	 *
	 * @param 	string[] 	$pStringTable
	 * @return 	string 		XML Output
	 * @throws 	Exception
	 */
	public function writeStringTable($pStringTable = null)
	{
		if (!is_null($pStringTable)) {					
			// Create XML writer
			$objWriter = null;
			if ($this->getParentWriter()->getUseDiskCaching()) {
				$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_DISK);
			} else {
				$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_MEMORY);
			}
			
			// XML header
			$objWriter->startDocument('1.0','UTF-8','yes');
			
			// String table
			$objWriter->startElement('sst');
			$objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
			$objWriter->writeAttribute('uniqueCount', count($pStringTable));
			
				// Loop trough string table
				foreach ($pStringTable as $string) {
					$objWriter->startElement('si');
						$objWriter->writeElement('t', $string);
                    $objWriter->endElement();
				}
				
			$objWriter->endElement();

			// Return
			return $objWriter->getData();
		} else {
			throw new Exception("Invalid string table array passed.");
		}
	}
}
