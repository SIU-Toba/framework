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
 * @package    PHPExcel_Reader
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/lgpl.txt	LGPL
 * @version    1.5.0, 2007-10-23
 */


/** PHPExcel */
require_once 'PHPExcel.php';

/** PHPExcel_Reader_IReader */
require_once 'PHPExcel/Reader/IReader.php';

/** PHPExcel_Shared_File */
require_once 'PHPExcel/Shared/File.php';


/**
 * PHPExcel_Reader_Serialized
 *
 * @category   PHPExcel
 * @package    PHPExcel_Reader
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Reader_Serialized implements PHPExcel_Reader_IReader
{
	/**
	 * Loads PHPExcel Serialized file
	 *
	 * @param 	string 		$pFilename
	 * @return 	PHPExcel
	 * @throws 	Exception
	 */	
	public function load($pFilename)
	{
		// Check if file exists
		if (!file_exists($pFilename)) {
			throw new Exception("Could not open " . $pFilename . " for reading! File does not exist.");
		}

		// Unserialize... First make sure the file supports it!
		if (!$this->fileSupportsUnserializePHPExcel($pFilename)) {
			throw new Exception("Invalid file format for PHPExcel_Reader_Serialized: " . $pFilename . ".");
		}
			
		return $this->_loadSerialized($pFilename);
	}
	
	/**
	 * Load PHPExcel Serialized file
	 *
	 * @param 	string 		$pFilename
	 * @return 	PHPExcel
	 */
	private function _loadSerialized($pFilename) {
		$xmlData = simplexml_load_string(file_get_contents("zip://$pFilename#phpexcel.xml"));
		$excel = unserialize(base64_decode((string)$xmlData->data));
		
		// Update media links
		for ($i = 0; $i < $excel->getSheetCount(); $i++) {
			for ($j = 0; $j < $excel->getSheet($i)->getDrawingCollection()->count(); $j++) {
				if ($excel->getSheet($i)->getDrawingCollection()->offsetGet($j) instanceof PHPExcl_Worksheet_BaseDrawing) {
					$imgTemp =& $excel->getSheet($i)->getDrawingCollection()->offsetGet($j);
					$imgTemp->setPath('zip://' . $pFilename . '#media/' . $imgTemp->getFilename(), false);
				}
			}
		}

		return $excel;
	}
    
    /**
     * Does a file support UnserializePHPExcel ?
     *
	 * @param 	string 		$pFilename
	 * @throws 	Exception
	 * @return 	boolean
     */
    public function fileSupportsUnserializePHPExcel($pFilename = '') {
		// Check if file exists
		if (!file_exists($pFilename)) {
			throw new Exception("Could not open " . $pFilename . " for reading! File does not exist.");
		}
		
		// File exists, does it contain phpexcel.xml?
		return PHPExcel_Shared_File::file_exists("zip://$pFilename#phpexcel.xml");
    }
}
