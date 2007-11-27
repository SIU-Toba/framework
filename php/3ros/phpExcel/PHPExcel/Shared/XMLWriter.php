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
 * @package    PHPExcel_Shared
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/lgpl.txt	LGPL
 * @version    1.5.0, 2007-10-23
 */


/**
 * PHPExcel_Shared_XMLWriter
 *
 * @category   PHPExcel
 * @package    PHPExcel_Shared
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Shared_XMLWriter {
	/** Temporary storage method */
	const STORAGE_MEMORY	= 1;
	const STORAGE_DISK		= 2;
	
	/**
	 * Internal XMLWriter
	 *
	 * @var XMLWriter
	 */
	private $_xmlWriter;
	
	/**
	 * Temporary filename
	 *
	 * @var string
	 */
	private $_tempFileName = '';
	
	/**
	 * Create a new PHPExcel_Shared_XMLWriter instance
	 *
	 * @param int	$pTemporaryStorage	Temporary storage location
	 */
    public function __construct($pTemporaryStorage = self::STORAGE_MEMORY) {
    	// Create internal XMLWriter
    	$this->_xmlWriter = new XMLWriter();
    	
    	// Open temporary storage
    	if ($pTemporaryStorage == self::STORAGE_MEMORY) {
    		$this->_xmlWriter->openMemory();
    	} else {
    		// Create temporary filename
    		$this->_tempFileName = @tempnam('./', 'xml');
    		
    		// Open storage
    		if ($this->_xmlWriter->openUri($this->_tempFileName) === false) {
    			// Fallback to memory...
    			$this->_xmlWriter->openMemory();
    		}
    	}
    	
    	// Set default values
    	$this->_xmlWriter->setIndent(true);
    }
    
    /**
     * Destructor
     */
    public function __destruct() {
    	// Desctruct XMLWriter
    	unset($this->_xmlWriter);
    	
    	// Unlink temporary files
    	if ($this->_tempFileName != '') {
    		@unlink($this->_tempFileName);
    	}
    }
    
    /**
     * Get written data
     *
     * @return $data
     */
    public function getData() {
    	if ($this->_tempFileName == '') {
    		return $this->_xmlWriter->outputMemory(true);
    	} else {
    		$this->_xmlWriter->flush();
    		return file_get_contents($this->_tempFileName);
    	}
    }
    
    /**
     * Catch function calls (and pass them to internal XMLWriter)
     *
     * @param unknown_type $function
     * @param unknown_type $args
     */
    public function __call($function, $args) {
    	try {
    		@call_user_method_array($function, $this->_xmlWriter, $args);
    	} catch (Exception $ex) {
    		// Do nothing!
    	}
    }
}
