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
 * @package    PHPExcel_Writer
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/lgpl.txt	LGPL
 * @version    1.5.0, 2007-10-23
 */


/** PHPExcel_IWriter */
require_once 'PHPExcel/Writer/IWriter.php';

/** PHPExcel_Cell */
require_once 'PHPExcel/Cell.php';

/** PHPExcel_RichText */
require_once 'PHPExcel/RichText.php';

/** PHPExcel_Shared_Drawing */
require_once 'PHPExcel/Shared/Drawing.php';

/** PHPExcel_HashTable */
require_once 'PHPExcel/HashTable.php';


/**
 * PHPExcel_Writer_HTML
 *
 * @category   PHPExcel
 * @package    PHPExcel_Writer
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Writer_HTML implements PHPExcel_Writer_IWriter {
	/**
	 * PHPExcel object
	 *
	 * @var PHPExcel
	 */
	private $_phpExcel;
	
	/**
	 * Sheet index to write
	 * 
	 * @var int
	 */
	private $_sheetIndex;
	
	/**
	 * Pre-calculate formulas
	 *
	 * @var boolean
	 */
	private $_preCalculateFormulas = true;
	
	/**
	 * Create a new PHPExcel_Writer_Excel5
	 *
	 * @param 	PHPExcel	$phpExcel	PHPExcel object
	 */
	public function __construct(PHPExcel $phpExcel) {
		$this->_phpExcel = $phpExcel;
		$this->_sheetIndex 	= 0;
	}
	
	/**
	 * Save PHPExcel to file
	 *
	 * @param 	string 		$pFileName
	 * @throws 	Exception
	 */	
	public function save($pFilename = null) {
		// Fetch sheet
		$sheet = $this->_phpExcel->getSheet($this->_sheetIndex);
		
		// Open file
		$fileHandle = fopen($pFilename, 'w');
		if ($fileHandle === false) {
			throw new Exception("Could not open file $pFilename for writing.");
		}
		
		// Get cell collection
		$cellCollection = $sheet->getCellCollection();
		
		// Get column count
		$colCount = PHPExcel_Cell::columnIndexFromString($sheet->getHighestColumn());

		// Write headers
		$this->_writeHTMLHeader($fileHandle);
		$this->_writeStyles($fileHandle, $sheet);
		$this->_writeTableHeader($fileHandle);
		
		// Loop trough cells
		$currentRow = -1;
		$rowData = array();
		foreach ($cellCollection as $cell) {
			if ($currentRow != $cell->getRow()) {
				// End previous row?
				if ($currentRow != -1) {
					$this->_writeRow($fileHandle, $sheet, $rowData, ($currentRow - 1));
				}

				// Set current row
				$currentRow = $cell->getRow();
			
				// Start a new row
				$rowData = array();
				for ($i = 0; $i < $colCount; $i++) {
					$rowData[$i] = '';
				}
			}
					
			// Copy cell
			$column = PHPExcel_Cell::columnIndexFromString($cell->getColumn()) - 1;
			$rowData[$column] = $cell;
		}
		
		// End last row?
		if ($currentRow != -1) {
			$this->_writeRow($fileHandle, $sheet, $rowData, ($currentRow - 1));
		}
				
		// Write footers
		$this->_writeTableFooter($fileHandle);
		$this->_writeHTMLFooter($fileHandle);
		
		// Close file
		fclose($fileHandle);
	}
	
	/**
	 * Map VAlign
	 */	
	private function _mapVAlign($vAlign) {
		switch ($vAlign) {
			case PHPExcel_Style_Alignment::VERTICAL_BOTTOM: return 'bottom';
			case PHPExcel_Style_Alignment::VERTICAL_TOP: return 'top';
			case PHPExcel_Style_Alignment::VERTICAL_CENTER:
			case PHPExcel_Style_Alignment::VERTICAL_JUSTIFY: return 'middle';
			default: return ' baseline';
		}
	}
	
	/**
	 * Map HAlign
	 */	
	private function _mapHAlign($hAlign) {
		switch ($hAlign) {
			case PHPExcel_Style_Alignment::HORIZONTAL_GENERAL:
			case PHPExcel_Style_Alignment::HORIZONTAL_LEFT: return 'left';
			case PHPExcel_Style_Alignment::HORIZONTAL_RIGHT: return 'right';
			case PHPExcel_Style_Alignment::HORIZONTAL_CENTER: return 'center';
			case PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY: return 'justify';
			default: return ' baseline';
		}
	}
	
	/**
	 * Map border style
	 */	
	private function _mapBorderStyle($borderStyle) {
		switch ($borderStyle) {
			case PHPExcel_Style_Border::BORDER_NONE: return '0px';
			case PHPExcel_Style_Border::BORDER_DASHED: return '1px dashed';
			case PHPExcel_Style_Border::BORDER_DOTTED: return '1px dotted';
			case PHPExcel_Style_Border::BORDER_THICK: return '2px solid';
			default: return '1px solid'; // map others to thin
		}
	}
	
	/**
	 * Get sheet index
	 * 
	 * @return int
	 */
	public function getSheetIndex() {
		return $this->_sheetIndex;
	}
	
	/**
	 * Set sheet index
	 * 
	 * @param	int		$pValue		Sheet index
	 */
	public function setSheetIndex($pValue = 0) {
		$this->_sheetIndex = $pValue;
	}
	
	/**
	 * Write HTML header to file
	 * 
	 * @param	mixed	$pFileHandle	PHP filehandle
	 * @throws	Exception
	 */
	private function _writeHTMLHeader($pFileHandle = null) {
		if (!is_null($pFileHandle)) {
			// Construct HTML
			$html = '';
			$html .= '<!-- Generated by PHPExcel - http://www.phpexcel.net -->' . "\r\n";
			$html .= '<html>' . "\r\n";
			$html .= '  <head>' . "\r\n";
			$html .= '    <title>' . $this->_phpExcel->getProperties()->getTitle() . '</title>' . "\r\n";
			$html .= '  </head>' . "\r\n";
			$html .= '' . "\r\n";
			$html .= '  <body>' . "\r\n";

			// Write to file
			fwrite($pFileHandle, $html);
		} else {
			throw new Exception("Invalid parameters passed.");
		}
	}

	/**
	 * Write images to file
	 * 
	 * @param	mixed				$pFileHandle	PHP filehandle
	 * @param	PHPExcel_Worksheet 	$pSheet			PHPExcel_Worksheet
	 * @param	string				$coordinates	Cell coordinates
	 * @throws	Exception
	 */
	private function _writeImageInCell($pFileHandle = null, PHPExcel_Worksheet $pSheet, $coordinates) {
		if (!is_null($pFileHandle)) {
			// Construct HTML
			$html = '';

			// Write images
			foreach ($pSheet->getDrawingCollection() as $drawing) {
				if ($drawing instanceof PHPExcel_Worksheet_BaseDrawing) {
					if ($drawing->getCoordinates() == $coordinates) {
						$filename = $drawing->getPath();
						
						$html .= "\r\n";
						$html .= '        <img  style="position: relative; left: ' . $drawing->getOffsetX() . 'px; top: ' . $drawing->getOffsetY() . 'px; width: "' . $drawing->getWidth() . 'px; height=' . $drawing->getHeight() . 'px;" src="' . $filename . '" border="0">' . "\r\n";
					}
				}
			}

			// Write to file
			fwrite($pFileHandle, $html);
		} else {
			throw new Exception("Invalid parameters passed.");
		}
	}
	
	/**
	 * Write styles to file
	 * 
	 * @param	mixed				$pFileHandle	PHP filehandle
	 * @param	PHPExcel_Worksheet 	$pSheet			PHPExcel_Worksheet
	 * @throws	Exception
	 */
	private function _writeStyles($pFileHandle = null, PHPExcel_Worksheet $pSheet) {
		if (!is_null($pFileHandle)) {
			// Construct HTML
			$html = '';
			
			// Start styles
			$html .= '    <style>' . "\r\n";
			$html .= '    <!--' . "\r\n";
			$html .= '      html {' . "\r\n";
			$html .= '        font-family: Calibri, Arial, Helvetica, Sans Serif;' . "\r\n";
			$html .= '        font-size: 10pt;' . "\r\n";
			$html .= '        background-color: white;' . "\r\n";
			$html .= '      }' . "\r\n";
			$html .= '      table.sheet, table.sheet td {' . "\r\n";
			if ($pSheet->getShowGridlines()) {
				$html .= '        border: 1px dotted black;' . "\r\n";
			}
			$html .= '      }' . "\r\n";				
			
			// Calculate column widths
			$pSheet->calculateColumnWidths();
			foreach ($pSheet->getColumnDimensions() as $columnDimension) {
				$column = PHPExcel_Cell::columnIndexFromString($columnDimension->getColumnIndex()) - 1;
				
				$html .= '      td.column' . $column  . ' {' . "\r\n";
				$html .= '        width: ' . PHPExcel_Shared_Drawing::cellDimensionToPixels($columnDimension->getWidth()) . 'px;' . "\r\n";
				if ($columnDimension->getVisible() === false) {
					$html .= '        display: none;' . "\r\n";
					$html .= '        visibility: hidden;' . "\r\n";
				}
				$html .= '      }' . "\r\n";
			}
			
			// Calculate row heights
			foreach ($pSheet->getRowDimensions() as $rowDimension) {
				$html .= '      tr.row' . ($rowDimension->getRowIndex() - 1)  . ' {' . "\r\n";
				$html .= '        height: ' . PHPExcel_Shared_Drawing::cellDimensionToPixels($rowDimension->getRowHeight()) . 'px;' . "\r\n";
				if ($rowDimension->getVisible() === false) {
					$html .= '        display: none;' . "\r\n";
					$html .= '        visibility: hidden;' . "\r\n";
				}
				$html .= '      }' . "\r\n";
			}
			
			// Calculate cell style hashes
			$cellStyleHashes = new PHPExcel_HashTable();
			$cellStyleHashes->addFromSource( $pSheet->getStyles() );
			for ($i = 0; $i < $cellStyleHashes->count(); $i++) {
				$html .= $this->_createCSSStyle( $cellStyleHashes->getByIndex($i) );
			}
			
			// End styles
			$html .= '    -->' . "\r\n";
			$html .= '    </style>' . "\r\n";

			// Write to file
			fwrite($pFileHandle, $html);
		} else {
			throw new Exception("Invalid parameters passed.");
		}
	}
	
	/**
	 * Create CSS style
	 * 
	 * @param	PHPExcel_Style 		$pStyle			PHPExcel_Style
	 * @return	string
	 */
	private function _createCSSStyle(PHPExcel_Style $pStyle) {
		// Construct HTML
		$html = '';
			
		// Create CSS
		$html .= '      .style' . $pStyle->getHashCode() . ' {' . "\r\n";
		$html .= $this->_createCSSStyleAlignment($pStyle->getAlignment());
		$html .= $this->_createCSSStyleFont($pStyle->getFont());
		$html .= $this->_createCSSStyleBorders($pStyle->getBorders());
		$html .= $this->_createCSSStyleFill($pStyle->getFill());
		$html .= '      }' . "\r\n";

		// Return
		return $html;
	}
	
	/**
	 * Create CSS style (PHPExcel_Style_Alignment)
	 * 
	 * @param	PHPExcel_Style_Alignment 		$pStyle			PHPExcel_Style_Alignment
	 * @return	string
	 */
	private function _createCSSStyleAlignment(PHPExcel_Style_Alignment $pStyle) {
		// Construct HTML
		$html = '';
			
		// Create CSS
		$html .= '        vertical-align: ' 	. $this->_mapVAlign($pStyle->getVertical()) . ';' . "\r\n";
		$html .= '        text-align: ' 		. $this->_mapHAlign($pStyle->getHorizontal()) . ';' . "\r\n";
		
		// Return
		return $html;
	}
	
	/**
	 * Create CSS style (PHPExcel_Style_Font)
	 * 
	 * @param	PHPExcel_Style_Font 		$pStyle			PHPExcel_Style_Font
	 * @return	string
	 */
	private function _createCSSStyleFont(PHPExcel_Style_Font $pStyle) {
		// Construct HTML
		$html = '';
			
		// Create CSS
		if ($pStyle->getBold()) {
			$html .= '        font-weight: bold;' . "\r\n";
		}
		if ($pStyle->getUnderline() != PHPExcel_Style_Font::UNDERLINE_NONE && $pStyle->getStriketrough()) {
			$html .= '        text-decoration: underline line-through;' . "\r\n";
		} else if ($pStyle->getUnderline() != PHPExcel_Style_Font::UNDERLINE_NONE) {
			$html .= '        text-decoration: underline;' . "\r\n";
		} else if ($pStyle->getStriketrough()) {
			$html .= '        text-decoration: line-through;' . "\r\n";
		}
		if ($pStyle->getItalic()) {
			$html .= '        font-style: italic;' . "\r\n";
		}
				
		$html .= '        color: ' 				. '#' . $pStyle->getColor()->getRGB() . ';' . "\r\n";
		$html .= '        font-family: ' 		. $pStyle->getName() . ';' . "\r\n";
		$html .= '        font-size: ' 			. $pStyle->getSize() . 'pt;' . "\r\n";
	
		// Return
		return $html;
	}
	
	/**
	 * Create CSS style (PHPExcel_Style_Borders)
	 * 
	 * @param	PHPExcel_Style_Borders 		$pStyle			PHPExcel_Style_Borders
	 * @return	string
	 */
	private function _createCSSStyleBorders(PHPExcel_Style_Borders $pStyle) {
		// Construct HTML
		$html = '';
			
		// Create CSS	
		$html .= '        border-bottom: ' 		. $this->_createCSSStyleBorder($pStyle->getBottom()) . ';' . "\r\n";
		$html .= '        border-top: ' 		. $this->_createCSSStyleBorder($pStyle->getTop()) . ';' . "\r\n";
		$html .= '        border-left: ' 		. $this->_createCSSStyleBorder($pStyle->getLeft()) . ';' . "\r\n";
		$html .= '        border-right: ' 		. $this->_createCSSStyleBorder($pStyle->getRight()) . ';' . "\r\n";

		// Return
		return $html;
	}
	
	/**
	 * Create CSS style (PHPExcel_Style_Border)
	 * 
	 * @param	PHPExcel_Style_Border		$pStyle			PHPExcel_Style_Border
	 * @return	string
	 */
	private function _createCSSStyleBorder(PHPExcel_Style_Border $pStyle) {
		// Construct HTML
		$html = '';
			
		// Create CSS
		$html .= $this->_mapBorderStyle($pStyle->getBorderStyle()) . ' #' . $pStyle->getColor()->getRGB();
		
		// Return
		return $html;
	}
	
	/**
	 * Create CSS style (PHPExcel_Style_Fill)
	 * 
	 * @param	PHPExcel_Style_Fill		$pStyle			PHPExcel_Style_Fill
	 * @return	string
	 */
	private function _createCSSStyleFill(PHPExcel_Style_Fill $pStyle) {
		// Construct HTML
		$html = '';
			
		// Create CSS
		$html .= '        background-color: ' 	. '#' . $pStyle->getStartColor()->getRGB() . ';' . "\r\n";

		// Return
		return $html;
	}
	
	/**
	 * Write HTML footer to file
	 * 
	 * @param	mixed	$pFileHandle	PHP filehandle
	 * @throws	Exception
	 */
	private function _writeHTMLFooter($pFileHandle = null) {
		if (!is_null($pFileHandle)) {
			// Construct HTML
			$html = '';
			$html .= '  </body>' . "\r\n";
			$html .= '</html>' . "\r\n";			

			// Write to file
			fwrite($pFileHandle, $html);
		} else {
			throw new Exception("Invalid parameters passed.");
		}
	}

	/**
	 * Write table header to file
	 * 
	 * @param	mixed	$pFileHandle	PHP filehandle
	 * @throws	Exception
	 */
	private function _writeTableHeader($pFileHandle = null) {
		if (!is_null($pFileHandle)) {
			// Construct HTML
			$html = '';
			$html .= '    <table border="0" cellpadding="0" cellspacing="0" class="sheet">' . "\r\n";

			// Write to file
			fwrite($pFileHandle, $html);
		} else {
			throw new Exception("Invalid parameters passed.");
		}
	}
	
	/**
	 * Write table footer to file
	 * 
	 * @param	mixed	$pFileHandle	PHP filehandle
	 * @throws	Exception
	 */
	private function _writeTableFooter($pFileHandle = null) {
		if (!is_null($pFileHandle)) {
			// Construct HTML
			$html = '';
			$html .= '    </table>' . "\r\n";		

			// Write to file
			fwrite($pFileHandle, $html);
		} else {
			throw new Exception("Invalid parameters passed.");
		}
	}
	
	/**
	 * Write row to HTML file
	 * 
	 * @param	mixed				$pFileHandle	PHP filehandle
	 * @param	PHPExcel_Worksheet 	$pSheet			PHPExcel_Worksheet
	 * @param	array				$pValues		Array containing cells in a row
	 * @param	int					$pRow			Row number
	 * @throws	Exception
	 */
	private function _writeRow($pFileHandle = null, PHPExcel_Worksheet $pSheet, $pValues = null, $pRow = 0) {
		if (!is_null($pFileHandle) && is_array($pValues)) {	
			// Write row start
			fwrite($pFileHandle, '        <tr class="row' . $pRow . '">' . "\r\n");
			
			// Write cells
			$colNum = 0;
			foreach ($pValues as $cell) {
				$cellData = '&nbsp;';
				$cssClass = 'column' . $colNum;
				$colSpan = 1;
				$rowSpan = 1;
				$writeCell = true;	// Write cell
				
				// PHPExcel_Cell
				if ($cell instanceof PHPExcel_Cell) {
					// Value
					if ($cell->getValue() instanceof PHPExcel_RichText) {
						// Loop trough rich text elements
						$elements = $cell->getValue()->getRichTextElements();
						foreach ($elements as $element) {
							// Rich text start?
							if ($element instanceof PHPExcel_RichText_Run) {
								$cellData .= '<span style="' . 
									str_replace("\r\n", '',
										$this->_createCSSStyleFont($element->getFont())
									) . '">';
							}
							
							$cellData .= $element->getText();
							
							if ($element instanceof PHPExcel_RichText_Run) {
								$cellData .= '</span>';
							}
						}
					} else {
						if ($this->_preCalculateFormulas) {
							$cellData = $cell->getCalculatedValue();
						} else {
							$cellData = $cell->getValue();
						}
					}
					
					// Check value
					if ($cellData == '') {
						$cellData = '&nbsp;';
					}
					
					// Extend CSS class?
					if (array_key_exists($cell->getCoordinate(), $pSheet->getStyles())) {
						$cssClass .= ' style' . $pSheet->getStyle($cell->getCoordinate())->getHashCode();
					}
				} else {
					$cell = new PHPExcel_Cell(
						PHPExcel_Cell::stringFromColumnIndex($colNum),
						($pRow + 1),
						'',
						null,
						null
					);
				}
				
				// Hyperlink?
				if ($cell->hasHyperlink()) {
					$cellData = '<a href="' . $cell->getHyperlink()->getUrl() . '" title="' . $cell->getHyperlink()->getTooltip() . '">' . $cellData . '</a>';
				}
				
				// Column/rowspan
				foreach ($pSheet->getMergeCells() as $cells) {
					if ($cell->isInRange($cells)) {
						list($first, ) = PHPExcel_Cell::splitRange($cells);
						
						if ($first == $cell->getCoordinate()) {
							list($colSpan, $rowSpan) = PHPExcel_Cell::rangeDimension($cells);
						} else {
							$writeCell = false;
						}
							
						break;
					}
				}
				
				// Write
				if ($writeCell) {
					// Column start
					fwrite($pFileHandle, '          <td');
							fwrite($pFileHandle, ' class="' . $cssClass . '"');
						if ($colSpan > 1) {
							fwrite($pFileHandle, ' colspan="' . $colSpan . '"');
						}
						if ($rowSpan > 1) {
							fwrite($pFileHandle, ' rowspan="' . $rowSpan . '"');
						}
					fwrite($pFileHandle, '>');
					
					// Image?
					$this->_writeImageInCell($pFileHandle, $pSheet, $cell->getCoordinate());
					
					// Cell data
					fwrite($pFileHandle, $cellData);
					
					// Column end
					fwrite($pFileHandle, '</td>' . "\r\n");
				}
				
				// Next column
				$colNum++;
			}
			
			// Write row end
			fwrite($pFileHandle, '        </tr>' . "\r\n");
		} else {
			throw new Exception("Invalid parameters passed.");
		}
	}
	

    /**
     * Get Pre-Calculate Formulas
     *
     * @return boolean
     */
    public function getPreCalculateFormulas() {
    	return $this->_preCalculateFormulas;
    }
    
    /**
     * Set Pre-Calculate Formulas
     *
     * @param boolean $pValue	Pre-Calculate Formulas?
     */
    public function setPreCalculateFormulas($pValue = true) {
    	$this->_preCalculateFormulas = $pValue;
    }
}
