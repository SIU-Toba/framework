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

/** PHPExcel_Cell */
require_once 'PHPExcel/Cell.php';

/** PHPExcel_Worksheet */
require_once 'PHPExcel/Worksheet.php';

/** PHPExcel_Style_Conditional */
require_once 'PHPExcel/Style/Conditional.php';

/** PHPExcel_Shared_Font */
require_once 'PHPExcel/Shared/Font.php';

/** PHPExcel_RichText */
require_once 'PHPExcel/RichText.php';

/** PHPExcel_Shared_XMLWriter */
require_once 'PHPExcel/Shared/XMLWriter.php';


/**
 * PHPExcel_Writer_Excel2007_Worksheet
 *
 * @category   PHPExcel
 * @package    PHPExcel_Writer_Excel2007
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Writer_Excel2007_Worksheet extends PHPExcel_Writer_Excel2007_WriterPart
{
	/**
	 * Write worksheet to XML format
	 *
	 * @param 	PHPExcel_Worksheet 		$pSheet
	 * @param 	string[] 				$pStringTable
	 * @return 	string 					XML Output
	 * @throws 	Exception
	 */
	public function writeWorksheet($pSheet = null, $pStringTable = null)
	{
		if (!is_null($pSheet)) {
			// Create XML writer
			$objWriter = null;
			if ($this->getParentWriter()->getUseDiskCaching()) {
				$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_DISK);
			} else {
				$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_MEMORY);
			}
			
			// XML header
			$objWriter->startDocument('1.0','UTF-8','yes');
			
			// Worksheet
			$objWriter->startElement('worksheet');
			$objWriter->writeAttribute('xml:space', 'preserve');
			$objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
			$objWriter->writeAttribute('xmlns:r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
			
				// sheetPr
				$this->_writeSheetPr($objWriter, $pSheet);
			
				// Dimension
				$this->_writeDimension($objWriter, $pSheet);
				
				// sheetViews
				$this->_writeSheetViews($objWriter, $pSheet);
			
				// sheetFormatPr
				$this->_writeSheetFormatPr($objWriter);
				
				// cols
				$this->_writeCols($objWriter, $pSheet);
						
				// sheetData
				$this->_writeSheetData($objWriter, $pSheet, $pStringTable);
			
				// sheetProtection
				$this->_writeSheetProtection($objWriter, $pSheet);
      
				// protectedRanges
				$this->_writeProtectedRanges($objWriter, $pSheet);
				
				// autoFilter
				$this->_writeAutoFilter($objWriter, $pSheet);
				
				// mergeCells
				$this->_writeMergeCells($objWriter, $pSheet);
				
				// conditionalFormatting
				$this->_writeConditionalFormatting($objWriter, $pSheet);
				
				// dataValidations
				$this->_writeDataValidations($objWriter, $pSheet);
				
				// hyperlinks
				$this->_writeHyperlinks($objWriter, $pSheet);
				
				// Print options
				$this->_writePrintOptions($objWriter, $pSheet);
								
				// Page margins
				$this->_writePageMargins($objWriter, $pSheet);

				// Page setup
				$this->_writePageSetup($objWriter, $pSheet);
				
				// Header / footer
				$this->_writeHeaderFooter($objWriter, $pSheet);
				
				// Breaks
				$this->_writeBreaks($objWriter, $pSheet);
				
				// Drawings
				$this->_writeDrawings($objWriter, $pSheet);
				
			$objWriter->endElement();

			// Return
			return $objWriter->getData();
		} else {
			throw new Exception("Invalid PHPExcel_Worksheet object passed.");
		}
	}
	
	/**
	 * Write SheetPr
	 *
	 * @param 	PHPExcel_Shared_XMLWriter		$objWriter 		XML Writer
	 * @param 	PHPExcel_Worksheet				$pSheet			Worksheet
	 * @throws 	Exception
	 */
	private function _writeSheetPr(PHPExcel_Shared_XMLWriter $objWriter = null, PHPExcel_Worksheet $pSheet = null)
	{
		// sheetPr
		$objWriter->startElement('sheetPr');
		$objWriter->writeAttribute('codeName', $pSheet->getTitle());
		$objWriter->endElement();
	}
	
	/**
	 * Write Dimension
	 *
	 * @param 	PHPExcel_Shared_XMLWriter 	$objWriter 		XML Writer
	 * @param 	PHPExcel_Worksheet			$pSheet			Worksheet
	 * @throws 	Exception
	 */
	private function _writeDimension(PHPExcel_Shared_XMLWriter $objWriter = null, PHPExcel_Worksheet $pSheet = null)
	{
		// dimension
		$objWriter->startElement('dimension');
		$objWriter->writeAttribute('ref', $pSheet->calculateWorksheetDimension());
		$objWriter->endElement();
	}
	
	/**
	 * Write SheetViews
	 *
	 * @param 	PHPExcel_Shared_XMLWriter 			$objWriter 		XML Writer
	 * @param 	PHPExcel_Worksheet					$pSheet			Worksheet
	 * @throws 	Exception
	 */
	private function _writeSheetViews(PHPExcel_Shared_XMLWriter $objWriter = null, PHPExcel_Worksheet $pSheet = null)
	{
		// sheetViews
		$objWriter->startElement('sheetViews');
				
			// sheetView
			$objWriter->startElement('sheetView');
			$objWriter->writeAttribute('tabSelected', 		'1');
			$objWriter->writeAttribute('workbookViewId',	'0');
					
				// Pane
				if ($pSheet->getFreezePane() != '') {
					// Calculate freeze coordinates
					$xSplit = 0;
					$ySplit = 0;
					$topLeftCell = $pSheet->getFreezePane();
						
					list($xSplit, $ySplit) = PHPExcel_Cell::coordinateFromString($pSheet->getFreezePane());
					$xSplit = PHPExcel_Cell::columnIndexFromString($xSplit);
						
					// pane
					$objWriter->startElement('pane');
					$objWriter->writeAttribute('xSplit', 		$xSplit - 1);
					$objWriter->writeAttribute('ySplit', 		$ySplit - 1);
					$objWriter->writeAttribute('topLeftCell', 	$topLeftCell);
					$objWriter->writeAttribute('activePane', 	'bottomRight');
					$objWriter->writeAttribute('state', 		'frozen');
					$objWriter->endElement();
				}
					
				// Selection
				$objWriter->writeElement('selection', null);
					
			$objWriter->endElement();
				
		$objWriter->endElement();
	}
	
	/**
	 * Write SheetFormatPr
	 *
	 * @param 	PHPExcel_Shared_XMLWriter $objWriter 		XML Writer
	 * @throws 	Exception
	 */
	private function _writeSheetFormatPr(PHPExcel_Shared_XMLWriter $objWriter = null)
	{
		// sheetFormatPr
		$objWriter->startElement('sheetFormatPr');
		$objWriter->writeAttribute('defaultRowHeight', '12.75');
		$objWriter->endElement();
	}
	
	/**
	 * Write Cols
	 *
	 * @param 	PHPExcel_Shared_XMLWriter 			$objWriter 		XML Writer
	 * @param 	PHPExcel_Worksheet					$pSheet			Worksheet
	 * @throws 	Exception
	 */
	private function _writeCols(PHPExcel_Shared_XMLWriter $objWriter = null, PHPExcel_Worksheet $pSheet = null)
	{
		// cols
		$objWriter->startElement('cols');
					
			// Check if there is at least one column dimension specified. If not, create one.
			if (count($pSheet->getColumnDimensions()) == 0) {
				$pSheet->getColumnDimension('A')->setWidth(10);
			}
				
			$pSheet->calculateColumnWidths();
			
			// Loop trough column dimensions
			foreach ($pSheet->getColumnDimensions() as $colDimension) {
				// col
				$objWriter->startElement('col');
				$objWriter->writeAttribute('min', 	PHPExcel_Cell::columnIndexFromString($colDimension->getColumnIndex()));
				$objWriter->writeAttribute('max', 	PHPExcel_Cell::columnIndexFromString($colDimension->getColumnIndex()));
					
				if ($colDimension->getWidth() < 0) {
					// No width set, apply default of 10
					$objWriter->writeAttribute('width', 		'10');
				} else {
					// Width set
					$objWriter->writeAttribute('width', 		$colDimension->getWidth());
				}
					
				// Column visibility
				if ($colDimension->getVisible() == false) {
					$objWriter->writeAttribute('hidden',		'true');
				}
					
				// Auto size?
				if ($colDimension->getAutoSize()) {
					$objWriter->writeAttribute('bestFit',		'true');
				} else {
					$objWriter->writeAttribute('customWidth',	'true');
				}
					
				$objWriter->endElement();
			}
			
		$objWriter->endElement();
	}

	/**
	 * Write SheetProtection
	 *
	 * @param 	PHPExcel_Shared_XMLWriter 			$objWriter 		XML Writer
	 * @param 	PHPExcel_Worksheet					$pSheet			Worksheet
	 * @throws 	Exception
	 */
	private function _writeSheetProtection(PHPExcel_Shared_XMLWriter $objWriter = null, PHPExcel_Worksheet $pSheet = null)
	{
		// sheetProtection
		$objWriter->startElement('sheetProtection');
			
		if ($pSheet->getProtection()->getPassword() != '') {
			$objWriter->writeAttribute('password', 				$pSheet->getProtection()->getPassword());
		}
			
		$objWriter->writeAttribute('sheet', 				($pSheet->getProtection()->getSheet() 				? 'true' : 'false'));
		$objWriter->writeAttribute('objects', 				($pSheet->getProtection()->getObjects() 			? 'true' : 'false'));
		$objWriter->writeAttribute('scenarios', 			($pSheet->getProtection()->getScenarios() 			? 'true' : 'false'));
		$objWriter->writeAttribute('formatCells', 			($pSheet->getProtection()->getFormatCells() 		? 'true' : 'false'));
		$objWriter->writeAttribute('formatColumns', 		($pSheet->getProtection()->getFormatColumns() 		? 'true' : 'false'));
		$objWriter->writeAttribute('formatRows', 			($pSheet->getProtection()->getFormatRows() 			? 'true' : 'false'));
		$objWriter->writeAttribute('insertColumns', 		($pSheet->getProtection()->getInsertColumns() 		? 'true' : 'false'));
		$objWriter->writeAttribute('insertRows', 			($pSheet->getProtection()->getInsertRows() 			? 'true' : 'false'));
		$objWriter->writeAttribute('insertHyperlinks', 		($pSheet->getProtection()->getInsertHyperlinks() 	? 'true' : 'false'));
		$objWriter->writeAttribute('deleteColumns', 		($pSheet->getProtection()->getDeleteColumns() 		? 'true' : 'false'));
		$objWriter->writeAttribute('deleteRows', 			($pSheet->getProtection()->getDeleteRows() 			? 'true' : 'false'));
		$objWriter->writeAttribute('selectLockedCells', 	($pSheet->getProtection()->getSelectLockedCells() 	? 'true' : 'false'));
		$objWriter->writeAttribute('sort', 					($pSheet->getProtection()->getSort() 				? 'true' : 'false'));
		$objWriter->writeAttribute('autoFilter', 			($pSheet->getProtection()->getAutoFilter() 			? 'true' : 'false'));
		$objWriter->writeAttribute('pivotTables', 			($pSheet->getProtection()->getPivotTables() 		? 'true' : 'false'));
		$objWriter->writeAttribute('selectUnlockedCells', 	($pSheet->getProtection()->getSelectUnlockedCells()	? 'true' : 'false'));
		$objWriter->endElement();
	}

	/**
	 * Write ConditionalFormatting
	 *
	 * @param 	PHPExcel_Shared_XMLWriter 			$objWriter 		XML Writer
	 * @param 	PHPExcel_Worksheet					$pSheet			Worksheet
	 * @throws 	Exception
	 */
	private function _writeConditionalFormatting(PHPExcel_Shared_XMLWriter $objWriter = null, PHPExcel_Worksheet $pSheet = null)
	{
		// Conditional id
		$id = 1;
			
		// Loop trough styles in the current worksheet
		foreach ($pSheet->getStyles() as $cellCoodrinate => $style) {
			if (count($style->getConditionalStyles()) > 0) {
				foreach ($style->getConditionalStyles() as $conditional) {
					if ($this->getParentWriter()->getStylesConditionalHashTable()->getIndexForHashCode( $conditional->getHashCode() ) == '') {
						continue;
					}
					
					if ($conditional->getConditionType() != PHPExcel_Style_Conditional::CONDITION_NONE) {
						// conditionalFormatting
						$objWriter->startElement('conditionalFormatting');
						$objWriter->writeAttribute('sqref', 	$cellCoodrinate);
							
							// cfRule
							$objWriter->startElement('cfRule');
							$objWriter->writeAttribute('type', 		$conditional->getConditionType());
							$objWriter->writeAttribute('dxfId', 	$this->getParentWriter()->getStylesConditionalHashTable()->getIndexForHashCode( $conditional->getHashCode() ));
							$objWriter->writeAttribute('priority', 	$id++);
							
							if ($conditional->getConditionType() == PHPExcel_Style_Conditional::CONDITION_CELLIS
								&& $conditional->getOperatorType() != PHPExcel_Style_Conditional::OPERATOR_NONE) {
								$objWriter->writeAttribute('operator', 	$conditional->getOperatorType());
								
								// Formula
								$objWriter->writeElement('formula',	$conditional->getCondition());		
							}		
							
							$objWriter->endElement();
						
						$objWriter->endElement();
					}
				}
			}
		}
	}
	
	/**
	 * Write DataValidations
	 *
	 * @param 	PHPExcel_Shared_XMLWriter 			$objWriter 		XML Writer
	 * @param 	PHPExcel_Worksheet					$pSheet			Worksheet
	 * @throws 	Exception
	 */
	private function _writeDataValidations(PHPExcel_Shared_XMLWriter $objWriter = null, PHPExcel_Worksheet $pSheet = null)
	{	
		// Build a temporary array of datavalidation objects
		$aDataValidations = array();
		foreach ($pSheet->getCellCollection() as $cell) {
			if ($cell->hasDataValidation()) {
				$aDataValidations[] = $cell->getDataValidation();
			}
		}
			
		// Write data validations?
		if (count($aDataValidations) > 0) {
			$objWriter->startElement('dataValidations');
			$objWriter->writeAttribute('count', 	count($aDataValidations));
				
			foreach ($aDataValidations as $dv) {
				$objWriter->startElement('dataValidation');
							
				if ($dv->getType() != '') {
					$objWriter->writeAttribute('type', $dv->getType());
				}
					
				if ($dv->getErrorStyle() != '') {
					$objWriter->writeAttribute('errorStyle', $dv->getErrorStyle());
				}
					
				if ($dv->getOperator() != '') {
					$objWriter->writeAttribute('operator', $dv->getOperator());
				}
					
				$objWriter->writeAttribute('allowBlank', 		($dv->getAllowBlank() 		? '1'  : '0'));
				$objWriter->writeAttribute('showDropDown', 		(!$dv->getShowDropDown() 	? '1'  : '0'));
				$objWriter->writeAttribute('showInputMessage', 	($dv->getShowInputMessage()	? '1'  : '0'));
				$objWriter->writeAttribute('showErrorMessage', 	($dv->getShowErrorMessage()	? '1'  : '0'));
					
				if ($dv->getErrorTitle() != '') {
					$objWriter->writeAttribute('errorTitle', $dv->getErrorTitle());
				}
				if ($dv->getError() != '') {
					$objWriter->writeAttribute('error', $dv->getError());
				}
				if ($dv->getPromptTitle() != '') {
					$objWriter->writeAttribute('promptTitle', $dv->getPromptTitle());
				}
				if ($dv->getPrompt() != '') {
					$objWriter->writeAttribute('prompt', $dv->getPrompt());
				}
					
				$objWriter->writeAttribute('sqref', $dv->getParent()->getCoordinate());
					
				if ($dv->getFormula1() != '') {
					$objWriter->writeElement('formula1', $dv->getFormula1());
				}
				if ($dv->getFormula2() != '') {
					$objWriter->writeElement('formula2', $dv->getFormula2());
				}
					
				$objWriter->endElement();
			}

			$objWriter->endElement();		
		}	
	}
	
	/**
	 * Write Hyperlinks
	 *
	 * @param 	PHPExcel_Shared_XMLWriter 			$objWriter 		XML Writer
	 * @param 	PHPExcel_Worksheet					$pSheet			Worksheet
	 * @throws 	Exception
	 */
	private function _writeHyperlinks(PHPExcel_Shared_XMLWriter $objWriter = null, PHPExcel_Worksheet $pSheet = null)
	{	
		// Build a temporary array of hyperlink objects
		$aHyperlinks = array();
		foreach ($pSheet->getCellCollection() as $cell) {
			if ($cell->hasHyperlink()) {
				$aHyperlinks[] = $cell->getHyperlink();
			}
		}
			
		// Relation ID
		$relationId = 1;
		
		// Write hyperlinks?
		if (count($aHyperlinks) > 0) {
			$objWriter->startElement('hyperlinks');
				
			foreach ($aHyperlinks as $hyperlink) {
				$objWriter->startElement('hyperlink');
				
				$objWriter->writeAttribute('ref', 	$hyperlink->getParent()->getCoordinate());
				$objWriter->writeAttribute('r:id', 	'rId_hyperlink_' . $relationId);
							
				if ($hyperlink->getTooltip() != '') {
					$objWriter->writeAttribute('tooltip', $hyperlink->getTooltip());
				}
				
				$objWriter->endElement();
				
				$relationId++;
			}

			$objWriter->endElement();		
		}	
	}

	/**
	 * Write ProtectedRanges
	 *
	 * @param 	PHPExcel_Shared_XMLWriter 			$objWriter 		XML Writer
	 * @param 	PHPExcel_Worksheet					$pSheet			Worksheet
	 * @throws 	Exception
	 */
	private function _writeProtectedRanges(PHPExcel_Shared_XMLWriter $objWriter = null, PHPExcel_Worksheet $pSheet = null)
	{
		if (count($pSheet->getProtectedCells()) > 0) {
			// protectedRanges
			$objWriter->startElement('protectedRanges');
				
				// Loop protectedRanges
				foreach ($pSheet->getProtectedCells() as $protectedCell => $passwordHash) {
					// protectedRange
					$objWriter->startElement('protectedRange');
					$objWriter->writeAttribute('name', 		'p' . md5($protectedCell));
					$objWriter->writeAttribute('sqref', 	$protectedCell);
					$objWriter->writeAttribute('password', 	$passwordHash);
					$objWriter->endElement();
				}
				
			$objWriter->endElement();
		}
	}
	
	/**
	 * Write MergeCells
	 *
	 * @param 	PHPExcel_Shared_XMLWriter 			$objWriter 		XML Writer
	 * @param 	PHPExcel_Worksheet					$pSheet			Worksheet
	 * @throws 	Exception
	 */
	private function _writeMergeCells(PHPExcel_Shared_XMLWriter $objWriter = null, PHPExcel_Worksheet $pSheet = null)
	{
		if (count($pSheet->getMergeCells()) > 0) {
			// mergeCells
			$objWriter->startElement('mergeCells');
			
				// Loop mergeCells
				foreach ($pSheet->getMergeCells() as $mergeCell) {
					// mergeCell
					$objWriter->startElement('mergeCell');
					$objWriter->writeAttribute('ref', $mergeCell);
					$objWriter->endElement();
				}
				
			$objWriter->endElement();
		}
	}
	
	/**
	 * Write PrintOptions
	 *
	 * @param 	PHPExcel_Shared_XMLWriter 			$objWriter 		XML Writer
	 * @param 	PHPExcel_Worksheet					$pSheet			Worksheet
	 * @throws 	Exception
	 */
	private function _writePrintOptions(PHPExcel_Shared_XMLWriter $objWriter = null, PHPExcel_Worksheet $pSheet = null)
	{
		// printOptions
		$objWriter->startElement('printOptions');

		$objWriter->writeAttribute('gridLines', 	($pSheet->getShowGridlines() ? 'true': 'false'));
		$objWriter->writeAttribute('gridLinesSet', 	'true');
		
		$objWriter->endElement();
	}
	
	/**
	 * Write PageMargins
	 *
	 * @param 	PHPExcel_Shared_XMLWriter 				$objWriter 		XML Writer
	 * @param 	PHPExcel_Worksheet						$pSheet			Worksheet
	 * @throws 	Exception
	 */
	private function _writePageMargins(PHPExcel_Shared_XMLWriter $objWriter = null, PHPExcel_Worksheet $pSheet = null)
	{
		// pageMargins
		$objWriter->startElement('pageMargins');
		$objWriter->writeAttribute('left', 		$pSheet->getPageMargins()->getLeft());
		$objWriter->writeAttribute('right', 	$pSheet->getPageMargins()->getRight());
		$objWriter->writeAttribute('top', 		$pSheet->getPageMargins()->getTop());
		$objWriter->writeAttribute('bottom', 	$pSheet->getPageMargins()->getBottom());
		$objWriter->writeAttribute('header', 	$pSheet->getPageMargins()->getHeader());
		$objWriter->writeAttribute('footer', 	$pSheet->getPageMargins()->getFooter());
		$objWriter->endElement();	
	}
	
	/**
	 * Write AutoFilter
	 *
	 * @param 	PHPExcel_Shared_XMLWriter 				$objWriter 		XML Writer
	 * @param 	PHPExcel_Worksheet						$pSheet			Worksheet
	 * @throws 	Exception
	 */
	private function _writeAutoFilter(PHPExcel_Shared_XMLWriter $objWriter = null, PHPExcel_Worksheet $pSheet = null)
	{	
		if ($pSheet->getAutoFilter() != '') {
			// autoFilter
			$objWriter->startElement('autoFilter');
			$objWriter->writeAttribute('ref', 		$pSheet->getAutoFilter());					
			$objWriter->endElement();	
		}
	}				
				
	/**
	 * Write PageSetup
	 *
	 * @param 	PHPExcel_Shared_XMLWriter 			$objWriter 		XML Writer
	 * @param 	PHPExcel_Worksheet					$pSheet			Worksheet
	 * @throws 	Exception
	 */
	private function _writePageSetup(PHPExcel_Shared_XMLWriter $objWriter = null, PHPExcel_Worksheet $pSheet = null)
	{	
		// pageSetup
		$objWriter->startElement('pageSetup');
		$objWriter->writeAttribute('paperSize',		$pSheet->getPageSetup()->getPaperSize());
		$objWriter->writeAttribute('orientation', 	$pSheet->getPageSetup()->getOrientation());
		
		if (!is_null($pSheet->getPageSetup()->getScale())) {
			$objWriter->writeAttribute('scale', 	$pSheet->getPageSetup()->getScale());
		}
		if (!is_null($pSheet->getPageSetup()->getFitToHeight())) {
			$objWriter->writeAttribute('fitToHeight', 	$pSheet->getPageSetup()->getFitToHeight());
		}
		if (!is_null($pSheet->getPageSetup()->getFitToWidth())) {
			$objWriter->writeAttribute('fitToWidth', 	$pSheet->getPageSetup()->getFitToWidth());
		}
    
		$objWriter->endElement();
	}
	
	/**
	 * Write Header / Footer
	 *
	 * @param 	PHPExcel_Shared_XMLWriter 		$objWriter 		XML Writer
	 * @param 	PHPExcel_Worksheet				$pSheet			Worksheet
	 * @throws 	Exception
	 */
	private function _writeHeaderFooter(PHPExcel_Shared_XMLWriter $objWriter = null, PHPExcel_Worksheet $pSheet = null)
	{
		// headerFooter
		$objWriter->startElement('headerFooter');
		$objWriter->writeAttribute('differentOddEven', 	($pSheet->getHeaderFooter()->getDifferentOddEven() ? 'true' : 'false'));
		$objWriter->writeAttribute('differentFirst', 	($pSheet->getHeaderFooter()->getDifferentFirst() ? 'true' : 'false'));
		$objWriter->writeAttribute('scaleWithDoc', 		($pSheet->getHeaderFooter()->getScaleWithDocument() ? 'true' : 'false'));
		$objWriter->writeAttribute('alignWithMargins', 	($pSheet->getHeaderFooter()->getAlignWithMargins() ? 'true' : 'false'));
				
			$objWriter->writeElement('oddHeader', 		$pSheet->getHeaderFooter()->getOddHeader());
			$objWriter->writeElement('oddFooter', 		$pSheet->getHeaderFooter()->getOddFooter());
			$objWriter->writeElement('evenHeader', 		$pSheet->getHeaderFooter()->getEvenHeader());
			$objWriter->writeElement('evenFooter', 		$pSheet->getHeaderFooter()->getEvenFooter());
			$objWriter->writeElement('firstHeader', 	$pSheet->getHeaderFooter()->getFirstHeader());
			$objWriter->writeElement('firstFooter', 	$pSheet->getHeaderFooter()->getFirstFooter());
		$objWriter->endElement();
	}
	
	/**
	 * Write Breaks
	 *
	 * @param 	PHPExcel_Shared_XMLWriter		$objWriter 		XML Writer
	 * @param 	PHPExcel_Worksheet				$pSheet			Worksheet
	 * @throws 	Exception
	 */
	private function _writeBreaks(PHPExcel_Shared_XMLWriter $objWriter = null, PHPExcel_Worksheet $pSheet = null)
	{
		// Get row and column breaks
		$aRowBreaks = array();
		$aColumnBreaks = array();
		foreach ($pSheet->getBreaks() as $cell => $breakType) {
			if ($breakType == PHPExcel_Worksheet::BREAK_ROW) {
				array_push($aRowBreaks, $cell);
			} else if ($breakType == PHPExcel_Worksheet::BREAK_COLUMN) {
				array_push($aColumnBreaks, $cell);
			}
		}
			
		// rowBreaks
		if (count($aRowBreaks) > 0) {
			$objWriter->startElement('rowBreaks');
			$objWriter->writeAttribute('count', 			count($aRowBreaks));
			$objWriter->writeAttribute('manualBreakCount', 	count($aRowBreaks));
				
				foreach ($aRowBreaks as $cell) {
					$coords = PHPExcel_Cell::coordinateFromString($cell);
					
					$objWriter->startElement('brk');
					$objWriter->writeAttribute('id', 	$coords[1]);
					$objWriter->writeAttribute('man', 	'1');
					$objWriter->endElement();
				}
					
			$objWriter->endElement();
		}
			
		// Second, write column breaks
		if (count($aColumnBreaks) > 0) {
			$objWriter->startElement('colBreaks');
			$objWriter->writeAttribute('count', 			count($aColumnBreaks));
			$objWriter->writeAttribute('manualBreakCount', 	count($aColumnBreaks));
				
				foreach ($aColumnBreaks as $cell) {
					$coords = PHPExcel_Cell::coordinateFromString($cell);
					
					$objWriter->startElement('brk');
					$objWriter->writeAttribute('id', 	PHPExcel_Cell::columnIndexFromString($coords[0]) - 1);
					$objWriter->writeAttribute('man', 	'1');
					$objWriter->endElement();
				}
					
			$objWriter->endElement();
		}
	}
	
	/**
	 * Write SheetData
	 *
	 * @param 	PHPExcel_Shared_XMLWriter		$objWriter 		XML Writer
	 * @param 	PHPExcel_Worksheet				$pSheet			Worksheet
	 * @param 	string[]						$pStringTable	String table
	 * @throws 	Exception
	 */
	private function _writeSheetData(PHPExcel_Shared_XMLWriter $objWriter = null, PHPExcel_Worksheet $pSheet = null, $pStringTable = null)
	{
		if (is_array($pStringTable)) {
			// Flipped stringtable, for faster index searching
			$aFlippedStringTable = array_flip($pStringTable);
			
			// sheetData
			$objWriter->startElement('sheetData');

				// Get column count
				$colCount = PHPExcel_Cell::columnIndexFromString($pSheet->getHighestColumn());
			
				// Loop trough cells
				$currentRow = -1;
				$cellCollection = $pSheet->getCellCollection();
				foreach ($cellCollection as $cell) {					
					if ($currentRow != $cell->getRow()) {
						// End previous row?
						if ($currentRow != -1) {
							$objWriter->endElement();
						}

						// Set current row
						$currentRow = $cell->getRow();

						// Get row dimension
						$rowDimension = $pSheet->getRowDimension($currentRow);
			
						// Start a new row
						$objWriter->startElement('row');
						$objWriter->writeAttribute('r', 	$currentRow);
						$objWriter->writeAttribute('spans',	'1:' . $colCount);
									
						// Row dimensions
						if ($rowDimension->getRowHeight() >= 0) {
							$objWriter->writeAttribute('customHeight', 	'1');
							$objWriter->writeAttribute('ht', 			$rowDimension->getRowHeight());
						}
						
						// Row visibility
						if ($rowDimension->getVisible() == false) {
							$objWriter->writeAttribute('hidden',		'true');
						}
					}
					
					// Write cell
					$this->_writeCell($objWriter, $pSheet, $cell, $pStringTable, $aFlippedStringTable);
				}

				// End last row?
				if ($currentRow != -1) {
					$objWriter->endElement();
				}
					
			$objWriter->endElement();
		} else {
			throw new Exception("Invalid parameters passed.");
		}
	}
	
	/**
	 * Write Cell
	 *
	 * @param 	PHPExcel_Shared_XMLWriter	$objWriter 				XML Writer
	 * @param 	PHPExcel_Worksheet			$pSheet					Worksheet
	 * @param 	PHPExcel_Cell				$pCell					Cell
	 * @param 	string[]					$pStringTable			String table
	 * @param 	string[]					$pFlippedStringTable	String table (flipped), for faster index searching
	 * @throws 	Exception
	 */
	private function _writeCell(PHPExcel_Shared_XMLWriter $objWriter = null, PHPExcel_Worksheet $pSheet = null, PHPExcel_Cell $pCell = null, $pStringTable = null, $pFlippedStringTable = null)
	{
		if (is_array($pStringTable) && is_array($pFlippedStringTable)) {
			// Cell
			$objWriter->startElement('c');
			$objWriter->writeAttribute('r', $pCell->getCoordinate());
			
			// Sheet styles
			$aStyles = $pSheet->getStyles();
			if (isset($aStyles[$pCell->getCoordinate()])) {
				$styleIndex = $this->getParentWriter()->getStylesHashTable()->getIndexForHashCode( $aStyles[$pCell->getCoordinate()]->getHashCode() );
				if ($styleIndex != '') {
					$objWriter->writeAttribute('s', $styleIndex);
				}
			}

			// If cell value is supplied, write cell value
			if ($pCell->getValue() != '') {
				// Map type
				$mappedType = $pCell->getDataType();
								
				// Write data type depending on its type
				switch (strtolower($mappedType)) {
					case 'inlinestr': 	// Inline string
						$objWriter->writeAttribute('t', $mappedType);
						break;
					case 's': 			// String
						$objWriter->writeAttribute('t', $mappedType);
						break;
					case 'b': 			// Boolean
						$objWriter->writeAttribute('t', $mappedType);
						break;
					case 'f': 			// Formula
						break;
				}

				// Write data depending on its type
				switch (strtolower($mappedType)) {
					case 'inlinestr': 	// Inline string
						// Write rich-text
						$objWriter->startElement('is');
						$this->_writeRichText($objWriter, $pCell->getValue());
						$objWriter->endElement();
						break;
					case 's': 			// String
						$objWriter->writeElement('v', $pFlippedStringTable[$pCell->getValue()]);
						break;
					case 'f': 			// Formula
						$objWriter->writeElement('f', substr($pCell->getValue(), 1));
						if ($this->getParentWriter()->getOffice2003Compatibility() === false) {
							if ($this->getParentWriter()->getPreCalculateFormulas()) {
								$calculatedValue = $pCell->getCalculatedValue();
								if (substr($calculatedValue, 0, 1) != '#') {
									$objWriter->writeElement('v', $calculatedValue);
								} else {
									$objWriter->writeElement('v', '0');
								}
							} else {
								$objWriter->writeElement('v', '0');
							}
						}
						break;
					case 'n': 			// Numeric
						$objWriter->writeElement('v', $pCell->getValue());
						break;
					case 'b': 			// Boolean
						$objWriter->writeElement('v', ($pCell->getValue() ? '1' : '0'));
						break;
				}
			}

			$objWriter->endElement();
		} else {
			throw new Exception("Invalid parameters passed.");
		}
	}
	
	/**
	 * Write Drawings
	 *
	 * @param 	PHPExcel_Shared_XMLWriter		$objWriter 		XML Writer
	 * @param 	PHPExcel_Worksheet				$pSheet			Worksheet
	 * @throws 	Exception
	 */
	private function _writeDrawings(PHPExcel_Shared_XMLWriter $objWriter = null, PHPExcel_Worksheet $pSheet = null)
	{
		// If sheet contains drawings, add the relationships
		if ($pSheet->getDrawingCollection()->count() > 0) {
			$objWriter->startElement('drawing');
			$objWriter->writeAttribute('r:id', 'rId1');
			$objWriter->endElement();
		}			
	}
	
	/**
	 * Write Rich Text
	 *
	 * @param 	PHPExcel_Shared_XMLWriter		$objWriter 		XML Writer
	 * @param 	PHPExcel_RichText				$pRichText		Rich text
	 * @throws 	Exception
	 */
	private function _writeRichText(PHPExcel_Shared_XMLWriter $objWriter = null, PHPExcel_RichText $pRichText = null)
	{
		// Loop trough rich text elements
		$elements = $pRichText->getRichTextElements();
		foreach ($elements as $element) {
			// r
			$objWriter->startElement('r');
				
				// rPr
				if ($element instanceof PHPExcel_RichText_Run) {
					// rPr
					$objWriter->startElement('rPr');

						// rFont
						$objWriter->startElement('rFont');
						$objWriter->writeAttribute('val', $element->getFont()->getName());
						$objWriter->endElement();
							
						// Bold
						$objWriter->startElement('b');
						$objWriter->writeAttribute('val', ($element->getFont()->getBold() ? 'true' : 'false'));
						$objWriter->endElement();
									
						// Italic
						$objWriter->startElement('i');
						$objWriter->writeAttribute('val', ($element->getFont()->getItalic() ? 'true' : 'false'));
						$objWriter->endElement();
							
						// Striketrough
						$objWriter->startElement('strike');
						$objWriter->writeAttribute('val', ($element->getFont()->getStriketrough() ? 'true' : 'false'));
						$objWriter->endElement();			
							
						// Color
						$objWriter->startElement('color');
						$objWriter->writeAttribute('rgb', $element->getFont()->getColor()->getARGB());
						$objWriter->endElement();	
							
						// Size
						$objWriter->startElement('sz');
						$objWriter->writeAttribute('val', $element->getFont()->getSize());
						$objWriter->endElement();
							
						// Underline
						$objWriter->startElement('u');
						$objWriter->writeAttribute('val', $element->getFont()->getUnderline());
						$objWriter->endElement();
				
					$objWriter->endElement();
				}
					
				// t
				$objWriter->writeElement('t', $element->getText());
					
			$objWriter->endElement();
		}	
	}
}
