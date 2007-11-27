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
 * PHPExcel_Cell_DataValidation
 *
 * @category   PHPExcel
 * @package    PHPExcel_Cell
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Cell_DataValidation
{
	/* Data validation types */
	const TYPE_NONE			= 'none';
	const TYPE_CUSTOM		= 'custom';
	const TYPE_DATE			= 'date';
	const TYPE_DECIMAL		= 'decimal';
	const TYPE_LIST			= 'list';
	const TYPE_TEXTLENGTH	= 'textLength';
	const TYPE_TIME			= 'time';
	const TYPE_WHOLE		= 'whole';
	
	/* Data validation error styles */
	const STYLE_STOP		= 'stop';
	const STYLE_WARNING		= 'warning';
	const STYLE_INFORMATION	= 'information';
	
	/* Data validation operators */
	const OPERATOR_BETWEEN				= 'between';
	const OPERATOR_EQUAL				= 'equal';
	const OPERATOR_GREATERTHAN			= 'greaterThan';
	const OPERATOR_GREATERTHANOREQUAL	= 'greaterThanOrEqual';
	const OPERATOR_LESSTHAN				= 'lessThan';
	const OPERATOR_LESSTHANOREQUAL		= 'lessThanOrEqual';
	const OPERATOR_NOTBETWEEN			= 'notBetween';
	const OPERATOR_NOTEQUAL				= 'notEqual';
    
    /**
     * Formula 1
     *
     * @var string
     */
    private $_formula1;
    
    /**
     * Formula 2
     *
     * @var string
     */
    private $_formula2;
    
    /**
     * Type
     *
     * @var string
     */
    private $_type = PHPExcel_Cell_DataValidation::TYPE_NONE;
    
    /**
     * Error style
     *
     * @var string
     */
    private $_errorStyle = PHPExcel_Cell_DataValidation::STYLE_STOP;
    
    /**
     * Operator
     *
     * @var string
     */
    private $_operator;
    
    /**
     * Allow Blank
     *
     * @var boolean
     */
    private $_allowBlank;
    
    /**
     * Show DropDown
     *
     * @var boolean
     */
    private $_showDropDown;
    
    /**
     * Show InputMessage
     *
     * @var boolean
     */
    private $_showInputMessage;
    
    /**
     * Show ErrorMessage
     *
     * @var boolean
     */
    private $_showErrorMessage;
    
    /**
     * Error title
     *
     * @var string
     */
    private $_errorTitle;
    
    /**
     * Error
     *
     * @var string
     */
    private $_error;
    
    /**
     * Prompt title
     *
     * @var string
     */
    private $_promptTitle;
    
    /**
     * Prompt
     *
     * @var string
     */
    private $_prompt;
    
	/**
	 * Parent cell
	 *
	 * @var PHPExcel_Cell
	 */
	private $_parent;
	
    /**
     * Create a new PHPExcel_Cell_DataValidation
     *
     * @param 	PHPExcel_Cell		$pCell	Parent cell
     * @throws	Exception
     */
    public function __construct(PHPExcel_Cell $pCell = null)
    {
    	// Initialise member variables
		$this->_formula1 			= '';
		$this->_formula2 			= '';
		$this->_type 				= PHPExcel_Cell_DataValidation::TYPE_NONE;
		$this->_errorStyle 			= PHPExcel_Cell_DataValidation::STYLE_STOP;
		$this->_operator 			= '';
		$this->_allowBlank 			= false;
		$this->_showDropDown 		= false;
		$this->_showInputMessage 	= false;
		$this->_showErrorMessage 	= false;
		$this->_errorTitle 			= '';
		$this->_error 				= '';
		$this->_promptTitle 		= '';
		$this->_prompt 				= '';
 	
    	// Set cell
    	$this->_parent = $pCell;
    }
	
	/**
	 * Get Formula 1
	 *
	 * @return string
	 */
	public function getFormula1() {
		return $this->_formula1;
	}

	/**
	 * Set Formula 1
	 *
	 * @param	string	$value
	 */
	public function setFormula1($value = '') {
		$this->_formula1 = $value;
	}

	/**
	 * Get Formula 2
	 *
	 * @return string
	 */
	public function getFormula2() {
		return $this->_formula2;
	}

	/**
	 * Set Formula 2
	 *
	 * @param	string	$value
	 */
	public function setFormula2($value = '') {
		$this->_formula2 = $value;
	}
	
	/**
	 * Get Type
	 *
	 * @return string
	 */
	public function getType() {
		return $this->_type;
	}

	/**
	 * Set Type
	 *
	 * @param	string	$value
	 */
	public function setType($value = PHPExcel_Cell_DataValidation::TYPE_NONE) {
		$this->_type = $value;
	}

	/**
	 * Get Error style
	 *
	 * @return string
	 */
	public function getErrorStyle() {
		return $this->_errorStyle;
	}

	/**
	 * Set Error style
	 *
	 * @param	string	$value
	 */
	public function setErrorStyle($value = PHPExcel_Cell_DataValidation::STYLE_STOP) {
		$this->_errorStyle = $value;
	}

	/**
	 * Get Operator
	 *
	 * @return string
	 */
	public function getOperator() {
		return $this->_operator;
	}

	/**
	 * Set Operator
	 *
	 * @param	string	$value
	 */
	public function setOperator($value = '') {
		$this->_operator = $value;
	}

	/**
	 * Get Allow Blank
	 *
	 * @return boolean
	 */
	public function getAllowBlank() {
		return $this->_allowBlank;
	}

	/**
	 * Set Allow Blank
	 *
	 * @param	boolean	$value
	 */
	public function setAllowBlank($value = false) {
		$this->_allowBlank = $value;
	}

	/**
	 * Get Show DropDown
	 *
	 * @return boolean
	 */
	public function getShowDropDown() {
		return $this->_showDropDown;
	}

	/**
	 * Set Show DropDown
	 *
	 * @param	boolean	$value
	 */
	public function setShowDropDown($value = false) {
		$this->_showDropDown = $value;
	}

	/**
	 * Get Show InputMessage
	 *
	 * @return boolean
	 */
	public function getShowInputMessage() {
		return $this->_showInputMessage;
	}

	/**
	 * Set Show InputMessage
	 *
	 * @param	boolean	$value
	 */
	public function setShowInputMessage($value = false) {
		$this->_showInputMessage = $value;
	}

	/**
	 * Get Show ErrorMessage
	 *
	 * @return boolean
	 */
	public function getShowErrorMessage() {
		return $this->_showErrorMessage;
	}

	/**
	 * Set Show ErrorMessage
	 *
	 * @param	boolean	$value
	 */
	public function setShowErrorMessage($value = false) {
		$this->_showErrorMessage = $value;
	}

	/**
	 * Get Error title
	 *
	 * @return string
	 */
	public function getErrorTitle() {
		return $this->_errorTitle;
	}

	/**
	 * Set Error title
	 *
	 * @param	string	$value
	 */
	public function setErrorTitle($value = '') {
		$this->_errorTitle = $value;
	}

	/**
	 * Get Error
	 *
	 * @return string
	 */
	public function getError() {
		return $this->_error;
	}

	/**
	 * Set Error
	 *
	 * @param	string	$value
	 */
	public function setError($value = '') {
		$this->_error = $value;
	}

	/**
	 * Get Prompt title
	 *
	 * @return string
	 */
	public function getPromptTitle() {
		return $this->_promptTitle;
	}

	/**
	 * Set Prompt title
	 *
	 * @param	string	$value
	 */
	public function setPromptTitle($value = '') {
		$this->_promptTitle = $value;
	}

	/**
	 * Get Prompt
	 *
	 * @return string
	 */
	public function getPrompt() {
		return $this->_prompt;
	}

	/**
	 * Set Prompt
	 *
	 * @param	string	$value
	 */
	public function setPrompt($value = '') {
		$this->_prompt = $value;
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
    		  $this->_formula1
    		. $this->_formula2
    		. $this->_type = PHPExcel_Cell_DataValidation::TYPE_NONE
    		. $this->_errorStyle = PHPExcel_Cell_DataValidation::STYLE_STOP
    		. $this->_operator
    		. $this->_allowBlank
    		. $this->_showDropDown
    		. $this->_showInputMessage
    		. $this->_showErrorMessage
    		. $this->_errorTitle
    		. $this->_error
    		. $this->_promptTitle
    		. $this->_prompt
    		. $this->_parent->getCoordinate()
    		. __CLASS__
    	);
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
        
/*
<complexType name="CT_DataValidation">
<sequence>
<element name="formula1" type="ST_Formula" minOccurs="0" maxOccurs="1"/>
<element name="formula2" type="ST_Formula" minOccurs="0" maxOccurs="1"/>
</sequence>
<attribute name="type" type="ST_DataValidationType" use="optional" default="none"/>
<attribute name="errorStyle" type="ST_DataValidationErrorStyle" use="optional" default="stop"/>
<attribute name="imeMode" type="ST_DataValidationImeMode" use="optional" default="noControl"/>
<attribute name="operator" type="ST_DataValidationOperator" use="optional" default="between"/>
<attribute name="allowBlank" type="xsd:boolean" use="optional" default="false"/>
<attribute name="showDropDown" type="xsd:boolean" use="optional" default="false"/>
<attribute name="showInputMessage" type="xsd:boolean" use="optional" default="false"/>
<attribute name="showErrorMessage" type="xsd:boolean" use="optional" default="false"/>
<attribute name="errorTitle" type="ST_Xstring" use="optional"/>
<attribute name="error" type="ST_Xstring" use="optional"/>
<attribute name="promptTitle" type="ST_Xstring" use="optional"/>
<attribute name="prompt" type="ST_Xstring" use="optional"/>
<attribute name="sqref" type="ST_Sqref" use="required"/>
</complexType>
*/
}
