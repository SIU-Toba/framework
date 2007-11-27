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

/** PHPExcel_Writer_Excel2007 */
require_once 'PHPExcel/Writer/Excel2007.php';

/** PHPExcel_Writer_Excel2007_WriterPart */
require_once 'PHPExcel/Writer/Excel2007/WriterPart.php';

/** PHPExcel_Style */
require_once 'PHPExcel/Style.php';

/** PHPExcel_Style_Borders */
require_once 'PHPExcel/Style/Borders.php';

/** PHPExcel_Style_Border */
require_once 'PHPExcel/Style/Border.php';

/** PHPExcel_Style_Color */
require_once 'PHPExcel/Style/Color.php';

/** PHPExcel_Style_Fill */
require_once 'PHPExcel/Style/Fill.php';

/** PHPExcel_Style_Font */
require_once 'PHPExcel/Style/Font.php';

/** PHPExcel_Style_NumberFormat */
require_once 'PHPExcel/Style/NumberFormat.php';

/** PHPExcel_Style_Conditional */
require_once 'PHPExcel/Style/Conditional.php';

/** PHPExcel_Style_Protection */
require_once 'PHPExcel/Style/Protection.php';

/** PHPExcel_Shared_XMLWriter */
require_once 'PHPExcel/Shared/XMLWriter.php';


/**
 * PHPExcel_Writer_Excel2007_Style
 *
 * @category   PHPExcel
 * @package    PHPExcel_Writer_Excel2007
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Writer_Excel2007_Style extends PHPExcel_Writer_Excel2007_WriterPart
{
	/**
	 * Write styles to XML format
	 *
	 * @param 	PHPExcel	$pPHPExcel
	 * @return 	string 		XML Output
	 * @throws 	Exception
	 */
	public function writeStyles(PHPExcel $pPHPExcel = null)
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

		// styleSheet
		$objWriter->startElement('styleSheet');
		$objWriter->writeAttribute('xml:space', 'preserve');
		$objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

			// numFmts
			$objWriter->startElement('numFmts');
			$objWriter->writeAttribute('count', $this->getParentWriter()->getNumFmtHashTable()->count());

				// numFmt
				for ($i = 0; $i < $this->getParentWriter()->getNumFmtHashTable()->count(); $i++) {
					$this->_writeNumFmt($objWriter, $this->getParentWriter()->getNumFmtHashTable()->getByIndex($i), $i);
				}
					
			$objWriter->endElement();
				
			// fonts
			$objWriter->startElement('fonts');
			$objWriter->writeAttribute('count', $this->getParentWriter()->getFontHashTable()->count());

				// font
				for ($i = 0; $i < $this->getParentWriter()->getFontHashTable()->count(); $i++) {
					$this->_writeFont($objWriter, $this->getParentWriter()->getFontHashTable()->getByIndex($i));
				}
					
			$objWriter->endElement();
				
			// fills
			$objWriter->startElement('fills');
			$objWriter->writeAttribute('count', $this->getParentWriter()->getFillHashTable()->count());

				// fill
				for ($i = 0; $i < $this->getParentWriter()->getFillHashTable()->count(); $i++) {
					$this->_writeFill($objWriter, $this->getParentWriter()->getFillHashTable()->getByIndex($i));
				}
					
			$objWriter->endElement();
				
			// borders
			$objWriter->startElement('borders');
			$objWriter->writeAttribute('count', $this->getParentWriter()->getBordersHashTable()->count());

				// border
				for ($i = 0; $i < $this->getParentWriter()->getBordersHashTable()->count(); $i++) {
					$this->_writeBorder($objWriter, $this->getParentWriter()->getBordersHashTable()->getByIndex($i));
				}
				
			$objWriter->endElement();
				
			// cellStyleXfs
			$objWriter->startElement('cellStyleXfs');
			$objWriter->writeAttribute('count', $this->getParentWriter()->getStylesHashTable()->count());
				
				// xf
				$objWriter->startElement('xf');
					$objWriter->writeAttribute('numFmtId', 	0);
					$objWriter->writeAttribute('fontId', 	0);
					$objWriter->writeAttribute('fillId', 	0);
					$objWriter->writeAttribute('borderId',	0);
				$objWriter->endElement();

			$objWriter->endElement();
				
			// cellXfs
			$objWriter->startElement('cellXfs');
			$objWriter->writeAttribute('count', $this->getParentWriter()->getStylesHashTable()->count());
			
				// xf
				for ($i = 0; $i < $this->getParentWriter()->getStylesHashTable()->count(); $i++) {
					$this->_writeCellStyleXf($objWriter, $this->getParentWriter()->getStylesHashTable()->getByIndex($i));
				}
					
			$objWriter->endElement();
  
			// cellStyles
			$objWriter->startElement('cellStyles');
			$objWriter->writeAttribute('count', 1);
					
				// cellStyle
				$objWriter->startElement('cellStyle');
					$objWriter->writeAttribute('name', 		'Normal');
					$objWriter->writeAttribute('xfId', 		0);
					$objWriter->writeAttribute('builtinId',	0);
				$objWriter->endElement();
					
			$objWriter->endElement();

			// dxfs
			$objWriter->startElement('dxfs');
			$objWriter->writeAttribute('count', $this->getParentWriter()->getStylesConditionalHashTable()->count());
			
				// dxf
				for ($i = 0; $i < $this->getParentWriter()->getStylesConditionalHashTable()->count(); $i++) {
					$this->_writeCellStyleDxf($objWriter, $this->getParentWriter()->getStylesConditionalHashTable()->getByIndex($i)->getStyle());
				}
					
			$objWriter->endElement();
				
			// tableStyles
			$objWriter->startElement('tableStyles');
			$objWriter->writeAttribute('defaultTableStyle', 'TableStyleMedium9');
			$objWriter->writeAttribute('defaultPivotStyle', 'PivotTableStyle1');
			$objWriter->endElement();
				
		$objWriter->endElement();

		// Return
		return $objWriter->getData();		
	}
					
	/**
	 * Write Fill
	 *
	 * @param 	PHPExcel_Shared_XMLWriter 	$objWriter 		XML Writer
	 * @param 	PHPExcel_Style_Fill			$pFill			Fill style
	 * @throws 	Exception
	 */
	private function _writeFill(PHPExcel_Shared_XMLWriter $objWriter = null, PHPExcel_Style_Fill $pFill = null)
	{
		// Check if this is a pattern type or gradient type
		if ($pFill->getFillType() == PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR 
			|| $pFill->getFillType() == PHPExcel_Style_Fill::FILL_GRADIENT_PATH) {
			// Gradient fill
			$this->_writeGradientFill($objWriter, $pFill);
		} else {
			// Pattern fill
			$this->_writePatternFill($objWriter, $pFill);
		}
	}
	
	/**
	 * Write Gradient Fill
	 *
	 * @param 	PHPExcel_Shared_XMLWriter 	$objWriter 		XML Writer
	 * @param 	PHPExcel_Style_Fill			$pFill			Fill style
	 * @throws 	Exception
	 */
	private function _writeGradientFill(PHPExcel_Shared_XMLWriter $objWriter = null, PHPExcel_Style_Fill $pFill = null)
	{
		// fill
		$objWriter->startElement('fill');
			
			// gradientFill
			$objWriter->startElement('gradientFill');
				$objWriter->writeAttribute('type', 		$pFill->getFillType());
				$objWriter->writeAttribute('degree', 	$pFill->getRotation());
					
				// stop
				$objWriter->startElement('stop');
				$objWriter->writeAttribute('position', '0');
						
					// color
					$objWriter->startElement('color');
					$objWriter->writeAttribute('rgb', $pFill->getStartColor()->getARGB());
					$objWriter->endElement();
						
				$objWriter->endElement();
						
				// stop
				$objWriter->startElement('stop');
				$objWriter->writeAttribute('position', '1');
						
					// color
					$objWriter->startElement('color');
					$objWriter->writeAttribute('rgb', $pFill->getEndColor()->getARGB());
					$objWriter->endElement();
						
				$objWriter->endElement();
				
			$objWriter->endElement();
						
		$objWriter->endElement();
	}
	
	/**
	 * Write Pattern Fill
	 *
	 * @param 	PHPExcel_Shared_XMLWriter			$objWriter 		XML Writer
	 * @param 	PHPExcel_Style_Fill					$pFill			Fill style
	 * @throws 	Exception
	 */
	private function _writePatternFill(PHPExcel_Shared_XMLWriter $objWriter = null, PHPExcel_Style_Fill $pFill = null)
	{
		// fill
		$objWriter->startElement('fill');
			
			// patternFill
			$objWriter->startElement('patternFill');
				$objWriter->writeAttribute('patternType', $pFill->getFillType());
					
				// If no fill is selected, there is no need for color information
				if ($pFill->getFillType() != 'none') {
					// fgColor
					$objWriter->startElement('fgColor');
					$objWriter->writeAttribute('rgb', $pFill->getStartColor()->getARGB());
					$objWriter->endElement();
						
					// bgColor
					$objWriter->startElement('bgColor');
					$objWriter->writeAttribute('rgb', $pFill->getEndColor()->getARGB());
					$objWriter->endElement();
				}
				
			$objWriter->endElement();
						
		$objWriter->endElement();
	}

	/**
	 * Write Font
	 *
	 * @param 	PHPExcel_Shared_XMLWriter		$objWriter 		XML Writer
	 * @param 	PHPExcel_Style_Font				$pFont			Font style
	 * @throws 	Exception
	 */
	private function _writeFont(PHPExcel_Shared_XMLWriter $objWriter = null, PHPExcel_Style_Font $pFont = null)
	{
		// font
		$objWriter->startElement('font');
				
			// Name
			$objWriter->startElement('name');
			$objWriter->writeAttribute('val', $pFont->getName());
			$objWriter->endElement();
				
			// Size
			$objWriter->startElement('sz');
			$objWriter->writeAttribute('val', $pFont->getSize());
			$objWriter->endElement();
				
			// Bold
			$objWriter->startElement('b');
			$objWriter->writeAttribute('val', ($pFont->getBold() ? 'true' : 'false'));
			$objWriter->endElement();
						
			// Italic
			$objWriter->startElement('i');
			$objWriter->writeAttribute('val', ($pFont->getItalic() ? 'true' : 'false'));
			$objWriter->endElement();
				
			// Underline
			$objWriter->startElement('u');
			$objWriter->writeAttribute('val', $pFont->getUnderline());
			$objWriter->endElement();
				
			// Striketrough
			$objWriter->startElement('strike');
			$objWriter->writeAttribute('val', ($pFont->getStriketrough() ? 'true' : 'false'));
			$objWriter->endElement();			
				
			// Foreground color
			$objWriter->startElement('color');
			$objWriter->writeAttribute('rgb', $pFont->getColor()->getARGB());
			$objWriter->endElement();	
						
		$objWriter->endElement();
	}
					
	/**
	 * Write Border
	 *
	 * @param 	PHPExcel_Shared_XMLWriter			$objWriter 		XML Writer
	 * @param 	PHPExcel_Style_Borders				$pBorders		Borders style
	 * @throws 	Exception
	 */
	private function _writeBorder(PHPExcel_Shared_XMLWriter $objWriter = null, PHPExcel_Style_Borders $pBorders = null)
	{
		// Write border
		$objWriter->startElement('border');
			// Diagonal?
			switch ($pBorders->getDiagonalDirection()) {
				case PHPExcel_Style_Borders::DIAGONAL_UP:
					$objWriter->writeAttribute('diagonalUp', 	'true');
					$objWriter->writeAttribute('diagonalDown', 	'false');
					break;
				case PHPExcel_Style_Borders::DIAGONAL_DOWN:
					$objWriter->writeAttribute('diagonalUp', 	'false');
					$objWriter->writeAttribute('diagonalDown', 	'true');
					break;
			}
				
			// Outline?
			$objWriter->writeAttribute('outline', ($pBorders->getOutline() ? 'true' : 'false'));
			
			// BorderPr
			$this->_writeBorderPr($objWriter, 'left', 			$pBorders->getLeft());
			$this->_writeBorderPr($objWriter, 'right', 			$pBorders->getRight());
			$this->_writeBorderPr($objWriter, 'top', 			$pBorders->getTop());
			$this->_writeBorderPr($objWriter, 'bottom', 		$pBorders->getBottom());
			$this->_writeBorderPr($objWriter, 'diagonal', 		$pBorders->getDiagonal());
			$this->_writeBorderPr($objWriter, 'vertical', 		$pBorders->getVertical());
			$this->_writeBorderPr($objWriter, 'horizontal', 	$pBorders->getHorizontal());
		$objWriter->endElement();
	}
	
	/**
	 * Write Cell Style Xf
	 *
	 * @param 	PHPExcel_Shared_XMLWriter			$objWriter 		XML Writer
	 * @param 	PHPExcel_Style						$pStyle			Style
	 * @throws 	Exception
	 */
	private function _writeCellStyleXf(PHPExcel_Shared_XMLWriter $objWriter = null, PHPExcel_Style $pStyle = null)
	{
		// xf
		$objWriter->startElement('xf');		
			$objWriter->writeAttribute('xfId', 0);
			$objWriter->writeAttribute('fontId', $this->getParentWriter()->getFontHashTable()->getIndexForHashCode($pStyle->getFont()->getHashCode()));
			$objWriter->writeAttribute('numFmtId', ($this->getParentWriter()->getNumFmtHashTable()->getIndexForHashCode($pStyle->getNumberFormat()->getHashCode()) + 164)   );
			$objWriter->writeAttribute('fillId', $this->getParentWriter()->getFillHashTable()->getIndexForHashCode($pStyle->getFill()->getHashCode()));
			$objWriter->writeAttribute('borderId', $this->getParentWriter()->getBordersHashTable()->getIndexForHashCode($pStyle->getBorders()->getHashCode()));
			
			// Apply protection styles?
			if ($pStyle->getProtection()->getLocked() != PHPExcel_Style_Protection::PROTECTION_INHERIT || $pStyle->getProtection()->getHidden() != PHPExcel_Style_Protection::PROTECTION_INHERIT) {
				$objWriter->writeAttribute('applyProtection', 'true');
			}
     
			// alignment
			$objWriter->startElement('alignment');
				$objWriter->writeAttribute('horizontal', 	$pStyle->getAlignment()->getHorizontal());
				$objWriter->writeAttribute('vertical', 		$pStyle->getAlignment()->getVertical());
				
				$textRotation = 0;
				if ($pStyle->getAlignment()->getTextRotation() >= 0) {
					$textRotation = $pStyle->getAlignment()->getTextRotation();
				} else if ($pStyle->getAlignment()->getTextRotation() < 0) {
					$textRotation = 90 - $pStyle->getAlignment()->getTextRotation();
				}
				
				$objWriter->writeAttribute('textRotation', 	$textRotation);
				$objWriter->writeAttribute('wrapText', 		($pStyle->getAlignment()->getWrapText() ? 'true' : 'false'));
			$objWriter->endElement();
			
			// protection
			if ($pStyle->getProtection()->getLocked() != PHPExcel_Style_Protection::PROTECTION_INHERIT || $pStyle->getProtection()->getHidden() != PHPExcel_Style_Protection::PROTECTION_INHERIT) {
				$objWriter->startElement('protection');
					if ($pStyle->getProtection()->getLocked() != PHPExcel_Style_Protection::PROTECTION_INHERIT) {
						$objWriter->writeAttribute('locked', 		($pStyle->getProtection()->getLocked() == PHPExcel_Style_Protection::PROTECTION_PROTECTED ? 'true' : 'false'));
					}
					if ($pStyle->getProtection()->getHidden() != PHPExcel_Style_Protection::PROTECTION_INHERIT) {
						$objWriter->writeAttribute('hidden', 		($pStyle->getProtection()->getHidden() == PHPExcel_Style_Protection::PROTECTION_PROTECTED ? 'true' : 'false'));
					}
				$objWriter->endElement();
			}
			
		$objWriter->endElement();
	}
	
	/**
	 * Write Cell Style Dxf
	 *
	 * @param 	PHPExcel_Shared_XMLWriter 		$objWriter 		XML Writer
	 * @param 	PHPExcel_Style					$pStyle			Style
	 * @throws 	Exception
	 */
	private function _writeCellStyleDxf(PHPExcel_Shared_XMLWriter $objWriter = null, PHPExcel_Style $pStyle = null)
	{
		// dxf
		$objWriter->startElement('dxf');	
			
			// font
			$this->_writeFont($objWriter, $pStyle->getFont());
				
			// numFmt
			$this->_writeNumFmt($objWriter, $pStyle->getNumberFormat());
				
			// fill
			$this->_writeFill($objWriter, $pStyle->getFill());
				
			// alignment
			$objWriter->startElement('alignment');
				$objWriter->writeAttribute('horizontal', 	$pStyle->getAlignment()->getHorizontal());
				$objWriter->writeAttribute('vertical', 		$pStyle->getAlignment()->getVertical());
				
				$textRotation = 0;
				if ($pStyle->getAlignment()->getTextRotation() >= 0) {
					$textRotation = $pStyle->getAlignment()->getTextRotation();
				} else if ($pStyle->getAlignment()->getTextRotation() < 0) {
					$textRotation = 90 - $pStyle->getAlignment()->getTextRotation();
				}
				
				$objWriter->writeAttribute('textRotation', 	$textRotation);
			$objWriter->endElement();
				
			// border
			$this->_writeBorder($objWriter, $pStyle->getBorders());
			
			// protection
			if ($pStyle->getProtection()->getLocked() != PHPExcel_Style_Protection::PROTECTION_INHERIT || $pStyle->getProtection()->getHidden() != PHPExcel_Style_Protection::PROTECTION_INHERIT) {
				$objWriter->startElement('protection');
					if ($pStyle->getProtection()->getLocked() != PHPExcel_Style_Protection::PROTECTION_INHERIT) {
						$objWriter->writeAttribute('locked', 		($pStyle->getProtection()->getLocked() == PHPExcel_Style_Protection::PROTECTION_PROTECTED ? 'true' : 'false'));
					}
					if ($pStyle->getProtection()->getHidden() != PHPExcel_Style_Protection::PROTECTION_INHERIT) {
						$objWriter->writeAttribute('hidden', 		($pStyle->getProtection()->getHidden() == PHPExcel_Style_Protection::PROTECTION_PROTECTED ? 'true' : 'false'));
					}
				$objWriter->endElement();
			}
				
		$objWriter->endElement();
	}
			
	/**
	 * Write BorderPr
	 *
	 * @param 	PHPExcel_Shared_XMLWriter		$objWriter 		XML Writer
	 * @param 	string							$pName			Element name
	 * @param 	PHPExcel_Style_Border			$pBorder		Border style
	 * @throws 	Exception
	 */
	private function _writeBorderPr(PHPExcel_Shared_XMLWriter $objWriter = null, $pName = 'left', PHPExcel_Style_Border $pBorder = null)
	{
		// Write BorderPr
		if ($pBorder->getBorderStyle() != PHPExcel_Style_Border::BORDER_NONE) {
			$objWriter->startElement($pName);
			$objWriter->writeAttribute('style', 	$pBorder->getBorderStyle());
				
				// color
				$objWriter->startElement('color');
				$objWriter->writeAttribute('rgb', 	$pBorder->getColor()->getARGB());
				$objWriter->endElement();
					
			$objWriter->endElement();
		}
	}
	
	/**
	 * Write NumberFormat
	 *
	 * @param 	PHPExcel_Shared_XMLWriter			$objWriter 		XML Writer
	 * @param 	PHPExcel_Style_NumberFormat			$pNumberFormat	Number Format
	 * @param 	int									$pId			Number Format identifier
	 * @throws 	Exception
	 */
	private function _writeNumFmt(PHPExcel_Shared_XMLWriter $objWriter = null, PHPExcel_Style_NumberFormat $pNumberFormat = null, $pId = 0)
	{
		// numFmt
		$objWriter->startElement('numFmt');
			$objWriter->writeAttribute('numFmtId', 		($pId + 164));
			$objWriter->writeAttribute('formatCode', 	$pNumberFormat->getFormatCode());
		$objWriter->endElement();
	}
	
	/**
	 * Get an array of all styles
	 *
	 * @param 	PHPExcel				$pPHPExcel
	 * @return 	PHPExcel_Style[]		All styles in PHPExcel
	 * @throws 	Exception
	 */
	public function allStyles(PHPExcel $pPHPExcel = null)
	{	
		// Get an array of all styles
		$aStyles		= array();
				
		for ($i = 0; $i < $pPHPExcel->getSheetCount(); $i++) {
			foreach ($pPHPExcel->getSheet($i)->getStyles() as $style) {
				$aStyles[] = $style;
			}
		}
				
		return $aStyles;
	}
	
	/**
	 * Get an array of all conditional styles
	 *
	 * @param 	PHPExcel				$pPHPExcel
	 * @return 	PHPExcel_Style[]		All styles in PHPExcel
	 * @throws 	Exception
	 */
	public function allConditionalStyles(PHPExcel $pPHPExcel = null)
	{
		// Get an array of all styles
		$aStyles		= array();
				
		for ($i = 0; $i < $pPHPExcel->getSheetCount(); $i++) {
			foreach ($pPHPExcel->getSheet($i)->getStyles() as $style) {
				if (count($style->getConditionalStyles()) > 0) {
					foreach ($style->getConditionalStyles() as $conditional) {
						array_push($aStyles, $conditional);
					}
				}
			}
		}
				
		return $aStyles;
	}
	
	/**
	 * Get an array of all fills
	 *
	 * @param 	PHPExcel						$pPHPExcel
	 * @return 	PHPExcel_Style_Fill[]		All fills in PHPExcel
	 * @throws 	Exception
	 */
	public function allFills(PHPExcel $pPHPExcel = null)
	{
		// Get an array of unique fills
		$aFills 	= array();
		$aStyles 	= $this->allStyles($pPHPExcel);

		foreach ($aStyles as $style) {
			if (!array_key_exists($style->getFill()->getHashCode(), $aFills)) {
				$aFills[ $style->getFill()->getHashCode() ] = $style->getFill();
			}
		}
			
		return $aFills;
	}
	
	/**
	 * Get an array of all fonts
	 *
	 * @param 	PHPExcel						$pPHPExcel
	 * @return 	PHPExcel_Style_Font[]		All fonts in PHPExcel
	 * @throws 	Exception
	 */
	public function allFonts(PHPExcel $pPHPExcel = null)
	{
		// Get an array of unique fonts
		$aFonts 	= array();
		$aStyles 	= $this->allStyles($pPHPExcel);

		foreach ($aStyles as $style) {
			if (!array_key_exists($style->getFont()->getHashCode(), $aFonts)) {
				$aFonts[ $style->getFont()->getHashCode() ] = $style->getFont();
			}
		}
				
		return $aFonts;
	}

	/**
	 * Get an array of all borders
	 *
	 * @param 	PHPExcel						$pPHPExcel
	 * @return 	PHPExcel_Style_Borders[]		All borders in PHPExcel
	 * @throws 	Exception
	 */
	public function allBorders(PHPExcel $pPHPExcel = null)
	{
		// Get an array of unique borders
		$aBorders 	= array();
		$aStyles 	= $this->allStyles($pPHPExcel);

		foreach ($aStyles as $style) {
			if (!array_key_exists($style->getBorders()->getHashCode(), $aBorders)) {
				$aBorders[ $style->getBorders()->getHashCode() ] = $style->getBorders();
			}
		}
				
		return $aBorders;
	}
	
	/**
	 * Get an array of all number formats
	 *
	 * @param 	PHPExcel								$pPHPExcel
	 * @return 	PHPExcel_Style_NumberFormat[]		All number formats in PHPExcel
	 * @throws 	Exception
	 */
	public function allNumberFormats(PHPExcel $pPHPExcel = null)
	{
		// Get an array of unique number formats
		$aNumFmts 	= array();
		$aStyles 	= $this->allStyles($pPHPExcel);

		foreach ($aStyles as $style) {
			if (!array_key_exists($style->getNumberFormat()->getHashCode(), $aNumFmts)) {
				$aNumFmts[ $style->getNumberFormat()->getHashCode() ] = $style->getNumberFormat();
			}
		}
				
		return $aNumFmts;
	}
}
