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


/** PHPExcel */
require_once 'PHPExcel.php';

/** PHPExcel_Worksheet */
require_once 'PHPExcel/Worksheet.php';

/** PHPExcel_Writer_Excel2007 */
require_once 'PHPExcel/Writer/Excel2007.php';

/** PHPExcel_Writer_Excel2007_WriterPart */
require_once 'PHPExcel/Writer/Excel2007/WriterPart.php';

/** PHPExcel_Shared_XMLWriter */
require_once 'PHPExcel/Shared/XMLWriter.php';


/**
 * PHPExcel_Writer_Excel2007_Rels
 *
 * @category   PHPExcel
 * @package    PHPExcel_Writer_Excel2007
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Writer_Excel2007_Rels extends PHPExcel_Writer_Excel2007_WriterPart
{
	/**
	 * Write relationships to XML format
	 *
	 * @param 	PHPExcel	$pPHPExcel
	 * @return 	string 		XML Output
	 * @throws 	Exception
	 */
	public function writeRelationships(PHPExcel $pPHPExcel = null)
	{		
		// Create XML writer
		$objWriter = null;
		if ($this->getParentWriter()->getUseDiskCaching()) {
			$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_DISK);
		} else {
			$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_MEMORY);
		}
			
		// XML header
		$objWriter->startDocument('1.0','UTF-8','yes');
			
		// Relationships
		$objWriter->startElement('Relationships');
		$objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/package/2006/relationships');
				
			// Relationship docProps/app.xml
			$this->_writeRelationship(
				$objWriter,
				3,
				'http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties',
				'docProps/app.xml'
			);

			// Relationship docProps/core.xml
			$this->_writeRelationship(
				$objWriter,
				2,
				'http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties',
				'docProps/core.xml'
			);

			// Relationship xl/workbook.xml
			$this->_writeRelationship(
				$objWriter,
				1, 
				'http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument',
				'xl/workbook.xml'
			);
				
		$objWriter->endElement();

		// Return
		return $objWriter->getData();
	}
	
	/**
	 * Write workbook relationships to XML format
	 *
	 * @param 	PHPExcel	$pPHPExcel
	 * @return 	string 		XML Output
	 * @throws 	Exception
	 */
	public function writeWorkbookRelationships(PHPExcel $pPHPExcel = null)
	{
		// Create XML writer
		$objWriter = null;
		if ($this->getParentWriter()->getUseDiskCaching()) {
			$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_DISK);
		} else {
			$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_MEMORY);
		}
			
		// XML header
		$objWriter->startDocument('1.0','UTF-8','yes');
		
		// Relationships
		$objWriter->startElement('Relationships');
		$objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/package/2006/relationships');

			// Relationship styles.xml
			$this->_writeRelationship(
				$objWriter,
				1,
				'http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles',
				'styles.xml'
			);

			// Relationship theme/theme1.xml
			$this->_writeRelationship(
				$objWriter,
				2,
				'http://schemas.openxmlformats.org/officeDocument/2006/relationships/theme',
				'theme/theme1.xml'
			);

			// Relationship sharedStrings.xml
			$this->_writeRelationship(
				$objWriter,
				3,
				'http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings',
				'sharedStrings.xml'
			);

			// Relationships with sheets			
			for ($i = 0; $i < $pPHPExcel->getSheetCount(); $i++) {
				$this->_writeRelationship(
					$objWriter,
					($i + 1 + 3),
					'http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet',
					'worksheets/sheet' . ($i + 1) . '.xml'
				);
			}
				
		$objWriter->endElement();

		// Return
		return $objWriter->getData();
	}
	
	/**
	 * Write worksheet relationships to XML format
	 * 
	 * Numbering is as follows:
	 * 	rId1 				- Drawings
	 *  rId_hyperlink_x 	- Hyperlinks
	 *
	 * @param 	PHPExcel_Worksheet		$pWorksheet
	 * @param 	int						$pWorksheetId
	 * @return 	string 					XML Output
	 * @throws 	Exception
	 */
	public function writeWorksheetRelationships(PHPExcel_Worksheet $pWorksheet = null, $pWorksheetId = 1)
	{		
		// Create XML writer
		$objWriter = null;
		if ($this->getParentWriter()->getUseDiskCaching()) {
			$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_DISK);
		} else {
			$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_MEMORY);
		}
			
		// XML header
		$objWriter->startDocument('1.0','UTF-8','yes');
		
		// Relationships
		$objWriter->startElement('Relationships');
		$objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/package/2006/relationships');

			// Write drawing relationships?
			if ($pWorksheet->getDrawingCollection()->count() > 0) {
				$this->_writeRelationship(
					$objWriter,
					1,
					'http://schemas.openxmlformats.org/officeDocument/2006/relationships/drawing',
					'../drawings/drawing' . $pWorksheetId . '.xml'
				);
			}
			
			// Write hyperlink relationships?
			$i = 1;
			foreach ($pWorksheet->getCellCollection() as $cell) {
				if ($cell->hasHyperlink()) {
					$this->_writeRelationship(
						$objWriter,
						'_hyperlink_' . $i,
						'http://schemas.openxmlformats.org/officeDocument/2006/relationships/hyperlink',
						$cell->getHyperlink()->getUrl() . '/',
						'External'
					);
						
					$i++;
				}
			}
				
		$objWriter->endElement();

		// Return
		return $objWriter->getData();
	}
	
	/**
	 * Write drawing relationships to XML format
	 *
	 * @param 	PHPExcel_Worksheet			$pWorksheet
	 * @return 	string 						XML Output
	 * @throws 	Exception
	 */
	public function writeDrawingRelationships(PHPExcel_Worksheet $pWorksheet = null)
	{				
		// Create XML writer
		$objWriter = null;
		if ($this->getParentWriter()->getUseDiskCaching()) {
			$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_DISK);
		} else {
			$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_MEMORY);
		}
			
		// XML header
		$objWriter->startDocument('1.0','UTF-8','yes');
		
		// Relationships
		$objWriter->startElement('Relationships');
		$objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/package/2006/relationships');

			// Loop trough images and write relationships
			$i = 1;
			$iterator = $pWorksheet->getDrawingCollection()->getIterator();		
			while ($iterator->valid()) {
				if ($iterator->current() instanceof PHPExcel_Worksheet_Drawing) {
					// Write relationship for image drawing
					$this->_writeRelationship(
						$objWriter,
						$i,
						'http://schemas.openxmlformats.org/officeDocument/2006/relationships/image',
						'../media/' . $iterator->current()->getFilename()
					);
				}
					
				$iterator->next();
				$i++;
			}
				
		$objWriter->endElement();

		// Return
		return $objWriter->getData();
	}
	
	/**
	 * Write Override content type
	 *
	 * @param 	PHPExcel_Shared_XMLWriter 	$objWriter 		XML Writer
	 * @param 	int							$pId			Relationship ID. rId will be prepended!
	 * @param 	string						$pType			Relationship type
	 * @param 	string 						$pTarget		Relationship target
	 * @param 	string 						$pTargetMode	Relationship target mode
	 * @throws 	Exception
	 */
	private function _writeRelationship(PHPExcel_Shared_XMLWriter $objWriter = null, $pId = 1, $pType = '', $pTarget = '', $pTargetMode = '')
	{
		if ($pType != '' && $pTarget != '') {
			// Write relationship
			$objWriter->startElement('Relationship');
			$objWriter->writeAttribute('Id', 		'rId' . $pId);
			$objWriter->writeAttribute('Type', 		$pType);
			$objWriter->writeAttribute('Target',	$pTarget);
			
			if ($pTargetMode != '') {
				$objWriter->writeAttribute('TargetMode',	$pTargetMode);
			}
			
			$objWriter->endElement();
		} else {
			throw new Exception("Invalid parameters passed.");
		}
	}
}
