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


/** PHPExcel */
require_once 'PHPExcel.php';

/** PHPExcel_HashTable */
require_once 'PHPExcel/HashTable.php';

/** PHPExcel_IComparable */
require_once 'PHPExcel/IComparable.php';

/** PHPExcel_Worksheet */
require_once 'PHPExcel/Worksheet.php';

/** PHPExcel_Cell */
require_once 'PHPExcel/Cell.php';

/** PHPExcel_IWriter */
require_once 'PHPExcel/Writer/IWriter.php';

/** PHPExcel_Shared_XMLWriter */
require_once 'PHPExcel/Shared/XMLWriter.php';

/** PHPExcel_Writer_Excel2007_WriterPart */
require_once 'PHPExcel/Writer/Excel2007/WriterPart.php';

/** PHPExcel_Writer_Excel2007_StringTable */
require_once 'PHPExcel/Writer/Excel2007/StringTable.php';

/** PHPExcel_Writer_Excel2007_ContentTypes */
require_once 'PHPExcel/Writer/Excel2007/ContentTypes.php';

/** PHPExcel_Writer_Excel2007_DocProps */
require_once 'PHPExcel/Writer/Excel2007/DocProps.php';

/** PHPExcel_Writer_Excel2007_Rels */
require_once 'PHPExcel/Writer/Excel2007/Rels.php';

/** PHPExcel_Writer_Excel2007_Theme */
require_once 'PHPExcel/Writer/Excel2007/Theme.php';

/** PHPExcel_Writer_Excel2007_Style */
require_once 'PHPExcel/Writer/Excel2007/Style.php';

/** PHPExcel_Writer_Excel2007_Workbook */
require_once 'PHPExcel/Writer/Excel2007/Workbook.php';

/** PHPExcel_Writer_Excel2007_Worksheet */
require_once 'PHPExcel/Writer/Excel2007/Worksheet.php';

/** PHPExcel_Writer_Excel2007_Drawing */
require_once 'PHPExcel/Writer/Excel2007/Drawing.php';


/**
 * PHPExcel_Writer_Excel2007
 *
 * @category   PHPExcel
 * @package    PHPExcel_Writer
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Writer_Excel2007 implements PHPExcel_Writer_IWriter
{	
	/**
	 * Pre-calculate formulas
	 *
	 * @var boolean
	 */
	private $_preCalculateFormulas = true;
	
	/**
	 * Office2003 compatibility
	 *
	 * @var boolean
	 */
	private $_office2003compatibility = false;	
	
	/**
	 * Private writer parts
	 *
	 * @var PHPExcel_Writer_Excel2007_WriterPart[]
	 */
	private $_writerParts;
	
	/**
	 * Private PHPExcel
	 *
	 * @var PHPExcel
	 */
	private $_spreadSheet;
	
	/**
	 * Private string table
	 *
	 * @var string[]
	 */
	private $_stringTable;
	
	/**
	 * Private unique PHPExcel_Style HashTable
	 *
	 * @var PHPExcel_HashTable
	 */
	private $_stylesHashTable;
	
	/**
	 * Private unique PHPExcel_Style_Conditional HashTable
	 *
	 * @var PHPExcel_HashTable
	 */
	private $_stylesConditionalHashTable;
	
	/**
	 * Private unique PHPExcel_Style_Fill HashTable
	 *
	 * @var PHPExcel_HashTable
	 */
	private $_fillHashTable;
	
	/**
	 * Private unique PHPExcel_Style_Font HashTable
	 *
	 * @var PHPExcel_HashTable
	 */
	private $_fontHashTable;
	
	/**
	 * Private unique PHPExcel_Style_Borders HashTable
	 *
	 * @var PHPExcel_HashTable
	 */
	private $_bordersHashTable ;
	
	/**
	 * Private unique PHPExcel_Style_NumberFormat HashTable
	 *
	 * @var PHPExcel_HashTable
	 */
	private $_numFmtHashTable;
	
	/**
	 * Private unique PHPExcel_Worksheet_Drawing HashTable
	 *
	 * @var PHPExcel_HashTable
	 */
	private $_drawingHashTable;
	
	/**
	 * Use disk caching where possible?
	 *
	 * @var boolean
	 */
	private $_useDiskCaching = false;
	
    /**
     * Create a new PHPExcel_Writer_Excel2007
     *
	 * @param 	PHPExcel	$pPHPExcel
     */
    public function __construct(PHPExcel $pPHPExcel = null)
    {
    	// Assign PHPExcel
		$this->setPHPExcel($pPHPExcel);
		
    	// Initialise writer parts
    	$this->_writerParts['stringtable']		= new PHPExcel_Writer_Excel2007_StringTable();
		$this->_writerParts['contenttypes'] 	= new PHPExcel_Writer_Excel2007_ContentTypes();
		$this->_writerParts['docprops'] 		= new PHPExcel_Writer_Excel2007_DocProps();
		$this->_writerParts['rels'] 			= new PHPExcel_Writer_Excel2007_Rels();
		$this->_writerParts['theme'] 			= new PHPExcel_Writer_Excel2007_Theme();
		$this->_writerParts['style'] 			= new PHPExcel_Writer_Excel2007_Style();
		$this->_writerParts['workbook'] 		= new PHPExcel_Writer_Excel2007_Workbook();
		$this->_writerParts['worksheet'] 		= new PHPExcel_Writer_Excel2007_Worksheet();
		$this->_writerParts['drawing'] 			= new PHPExcel_Writer_Excel2007_Drawing();
		
		// Assign parent IWriter
		foreach ($this->_writerParts as $writer) {
			$writer->setParentWriter($this);
		}
		
		// Set HashTable variables
		$this->_stringTable					= array();
		$this->_stylesHashTable 			= new PHPExcel_HashTable();
		$this->_stylesConditionalHashTable 	= new PHPExcel_HashTable();
		$this->_fillHashTable 				= new PHPExcel_HashTable();	
		$this->_fontHashTable 				= new PHPExcel_HashTable();	
		$this->_bordersHashTable 			= new PHPExcel_HashTable();
		$this->_numFmtHashTable 			= new PHPExcel_HashTable();
		$this->_drawingHashTable 			= new PHPExcel_HashTable();
		
		// Other initializations
		$this->_serializePHPExcel			= false;
    }
    
	/**
	 * Get writer part
	 *
	 * @param 	string 	$pPartName		Writer part name
	 * @return 	PHPExcel_Writer_Excel2007_WriterPart
	 */
	function getWriterPart($pPartName = '') {
		if ($pPartName != '' && isset($this->_writerParts[strtolower($pPartName)])) {
			return $this->_writerParts[strtolower($pPartName)];
		} else {
			return null;
		}
	}
	
	/**
	 * Save PHPExcel to file
	 *
	 * @param 	string 		$pFileName
	 * @throws 	Exception
	 */	
	public function save($pFilename = null)
	{
		if (!is_null($this->_spreadSheet)) {
			// Create string lookup table
			$this->_stringTable = array();
			for ($i = 0; $i < $this->_spreadSheet->getSheetCount(); $i++) {
				$this->_stringTable = $this->getWriterPart('StringTable')->createStringTable($this->_spreadSheet->getSheet($i), $this->_stringTable);
			}

			// Create styles dictionaries
			$this->_stylesHashTable->addFromSource( 			$this->getWriterPart('Style')->allStyles($this->_spreadSheet) 			);
			$this->_stylesConditionalHashTable->addFromSource( 	$this->getWriterPart('Style')->allConditionalStyles($this->_spreadSheet) 			);
			$this->_fillHashTable->addFromSource( 				$this->getWriterPart('Style')->allFills($this->_spreadSheet) 			);
			$this->_fontHashTable->addFromSource( 				$this->getWriterPart('Style')->allFonts($this->_spreadSheet) 			);
			$this->_bordersHashTable->addFromSource( 			$this->getWriterPart('Style')->allBorders($this->_spreadSheet) 			);
			$this->_numFmtHashTable->addFromSource( 			$this->getWriterPart('Style')->allNumberFormats($this->_spreadSheet) 	);

			// Create drawing dictionary
			$this->_drawingHashTable->addFromSource( 			$this->getWriterPart('Drawing')->allDrawings($this->_spreadSheet) 		);
			
			// Create new ZIP file and open it for writing
			$objZip = new ZipArchive();
			
			// Try opening the ZIP file
			if ($objZip->open($pFilename, ZIPARCHIVE::OVERWRITE) !== true) {
				throw new Exception("Could not open " . $pFilename . " for writing.");
			}
			
			// Add [Content_Types].xml to ZIP file
			$objZip->addFromString('[Content_Types].xml', 			$this->getWriterPart('ContentTypes')->writeContentTypes($this->_spreadSheet));
			
			// Add relationships to ZIP file
			$objZip->addFromString('_rels/.rels', 					$this->getWriterPart('Rels')->writeRelationships($this->_spreadSheet));
			$objZip->addFromString('xl/_rels/workbook.xml.rels', 	$this->getWriterPart('Rels')->writeWorkbookRelationships($this->_spreadSheet));
			
			// Add document properties to ZIP file
			$objZip->addFromString('docProps/app.xml', 				$this->getWriterPart('DocProps')->writeDocPropsApp($this->_spreadSheet));
			$objZip->addFromString('docProps/core.xml', 			$this->getWriterPart('DocProps')->writeDocPropsCore($this->_spreadSheet));
					
			// Add theme to ZIP file
			$objZip->addFromString('xl/theme/theme1.xml', 			$this->getWriterPart('Theme')->writeTheme($this->_spreadSheet));

			// Add string table to ZIP file
			$objZip->addFromString('xl/sharedStrings.xml', 			$this->getWriterPart('StringTable')->writeStringTable($this->_stringTable));
			
			// Add styles to ZIP file
			$objZip->addFromString('xl/styles.xml', 				$this->getWriterPart('Style')->writeStyles($this->_spreadSheet));
			
			// Add workbook to ZIP file
			$objZip->addFromString('xl/workbook.xml', 				$this->getWriterPart('Workbook')->writeWorkbook($this->_spreadSheet));

			// Add worksheets
			for ($i = 0; $i < $this->_spreadSheet->getSheetCount(); $i++) {
				$objZip->addFromString('xl/worksheets/sheet' . ($i + 1) . '.xml', $this->getWriterPart('Worksheet')->writeWorksheet($this->_spreadSheet->getSheet($i), $this->_stringTable));
			}

			// Add worksheet relationships (drawings, ...)
			for ($i = 0; $i < $this->_spreadSheet->getSheetCount(); $i++) {
				
				// Currently, only drawing collection should be checked for relations.
				// In the future, update this construction!
				if ($this->_spreadSheet->getSheet($i)->getDrawingCollection()->count() > 0) {
					// Worksheet relationships
					$objZip->addFromString('xl/worksheets/_rels/sheet' . ($i + 1) . '.xml.rels', 	$this->getWriterPart('Rels')->writeWorksheetRelationships($this->_spreadSheet->getSheet($i), ($i + 1)));
				}
				
				
				// If sheet contains drawings, add the relationships
				if ($this->_spreadSheet->getSheet($i)->getDrawingCollection()->count() > 0) {
					// Drawing relationships
					$objZip->addFromString('xl/drawings/_rels/drawing' . ($i + 1) . '.xml.rels', $this->getWriterPart('Rels')->writeDrawingRelationships($this->_spreadSheet->getSheet($i)));
					
					// Drawings
					$objZip->addFromString('xl/drawings/drawing' . ($i + 1) . '.xml', $this->getWriterPart('Drawing')->writeDrawings($this->_spreadSheet->getSheet($i)));
				}
				
			}
			
			// Add media
			for ($i = 0; $i < $this->getDrawingHashTable()->count(); $i++) {
				if ($this->getDrawingHashTable()->getByIndex($i) instanceof PHPExcel_Worksheet_Drawing) {
					$objZip->addFromString('xl/media/' . $this->getDrawingHashTable()->getByIndex($i)->getFilename(), file_get_contents($this->getDrawingHashTable()->getByIndex($i)->getPath()));
				}
				//The line underneath does not support adding a file from a ZIP archive, the line above does!
				//$objZip->addFile($this->getDrawingHashTable()->getByIndex($i)->getPath(), 'xl/media/' . $this->getDrawingHashTable()->getByIndex($i)->getFilename());
			}

			// Close file
			if ($objZip->close() === false) {
				throw new Exception("Could not close zip file $pFilename.");
			}
		} else {
			throw new Exception("PHPExcel object unassigned.");
		}
	}
	
	/**
	 * Get PHPExcel object
	 *
	 * @return PHPExcel
	 * @throws Exception
	 */
	public function getPHPExcel() {
		if (!is_null($this->_spreadSheet)) {
			return $this->_spreadSheet;
		} else {
			throw new Exception("No PHPExcel assigned.");
		}
	}
	
	/**
	 * Get PHPExcel object
	 *
	 * @param 	PHPExcel 	$pPHPExcel	PHPExcel object
	 * @throws	Exception
	 */
	public function setPHPExcel(PHPExcel $pPHPExcel = null) {
		$this->_spreadSheet = $pPHPExcel;
	}
	
    /**
     * Get string table
     *
     * @return string[]
     */
    public function getStringTable() {
    	return $this->_stringTable;
    }
    
    /**
     * Get PHPExcel_Style HashTable
     *
     * @return PHPExcel_HashTable
     */
    public function getStylesHashTable() {
    	return $this->_stylesHashTable;
    }
    
    /**
     * Get PHPExcel_Style_Conditional HashTable
     *
     * @return PHPExcel_HashTable
     */
    public function getStylesConditionalHashTable() {
    	return $this->_stylesConditionalHashTable;
    }
    
    /**
     * Get PHPExcel_Style_Fill HashTable
     *
     * @return PHPExcel_HashTable
     */
    public function getFillHashTable() {
    	return $this->_fillHashTable;
    }
    
    /**
     * Get PHPExcel_Style_Font HashTable
     *
     * @return PHPExcel_HashTable
     */
    public function getFontHashTable() {
    	return $this->_fontHashTable;
    }
    
    /**
     * Get PHPExcel_Style_Borders HashTable
     *
     * @return PHPExcel_HashTable
     */
    public function getBordersHashTable() {
    	return $this->_bordersHashTable;
    }
    
    /**
     * Get PHPExcel_Style_NumberFormat HashTable
     *
     * @return PHPExcel_HashTable
     */
    public function getNumFmtHashTable() {
    	return $this->_numFmtHashTable;
    }
    
    /**
     * Get PHPExcel_Worksheet_Drawing HashTable
     *
     * @return PHPExcel_HashTable
     */
    public function getDrawingHashTable() {
    	return $this->_drawingHashTable;
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
    
    /**
     * Get Office2003 compatibility
     *
     * @return boolean
     */
    public function getOffice2003Compatibility() {
    	return $this->_office2003compatibility;
    }
    
    /**
     * Set Pre-Calculate Formulas
     *
     * @param boolean $pValue	Office2003 compatibility?
     */
    public function setOffice2003Compatibility($pValue = false) {
    	$this->_office2003compatibility = $pValue;
    }
    
	/**
	 * Get use disk caching where possible?
	 *
	 * @return boolean
	 */
	public function getUseDiskCaching() {
		return $this->_useDiskCaching;
	}
	
	/**
	 * Set use disk caching where possible?
	 *
	 * @param boolean $pValue
	 */
	public function setUseDiskCaching($pValue = false) {
		$this->_useDiskCaching = $pValue;
	}
}
