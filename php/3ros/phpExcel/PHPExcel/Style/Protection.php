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
 * @package    PHPExcel_Style
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/lgpl.txt	LGPL
 * @version    1.4.5, 2007-08-23
 */


/** PHPExcel_IComparable */
require_once 'PHPExcel/IComparable.php';


/**
 * PHPExcel_Style_Protection
 *
 * @category   PHPExcel
 * @package    PHPExcel_Style
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Style_Protection implements PHPExcel_IComparable
{
	/** Protection styles */
	const PROTECTION_INHERIT		= 'inherit';
	const PROTECTION_PROTECTED		= 'protected';
	const PROTECTION_UNPROTECTED	= 'unprotected';

	/**
	 * Locked
	 *
	 * @var string
	 */
	private $_locked;
	
	/**
	 * Hidden
	 *
	 * @var string
	 */
	private $_hidden;

    /**
     * Create a new PHPExcel_Style_Protection
     */
    public function __construct()
    {
    	// Initialise values
    	$this->_locked			= self::PROTECTION_INHERIT;
    	$this->_hidden			= self::PROTECTION_INHERIT;
    }

    /**
     * Apply styles from array
     *
     * <code>
     * $objPHPExcel->getActiveSheet()->getStyle('B2')->getLocked()->applyFromArray( array('locked' => true, 'hidden' => false) );
     * </code>
     *
     * @param	array	$pStyles	Array containing style information
     * @throws	Exception
     */
    public function applyFromArray($pStyles = null) {
    	if (is_array($pStyles)) {
    	    if (array_key_exists('locked', $pStyles)) {
    			$this->setLocked($pStyles['locked']);
    		}
    	    if (array_key_exists('hidden', $pStyles)) {
    			$this->setHidden($pStyles['locked']);
    		}
    	} else {
    		throw new Exception("Invalid style array passed.");
    	}
    }

    /**
     * Get locked
     *
     * @return string
     */
    public function getLocked() {
    	return $this->_locked;
    }

    /**
     * Set locked
     *
     * @param string $pValue
     */
    public function setLocked($pValue = self::PROTECTION_INHERIT) {
    	$this->_locked = $pValue;
    }
    
    /**
     * Get hidden
     *
     * @return string
     */
    public function getHidden() {
    	return $this->_hidden;
    }

    /**
     * Set hidden
     *
     * @param string $pValue
     */
    public function setHidden($pValue = self::PROTECTION_INHERIT) {
    	$this->_hidden = $pValue;
    }

	/**
	 * Get hash code
	 *
	 * @return string	Hash code
	 */
	public function getHashCode() {
    	return md5(
    		  $this->_locked
    		. $this->_hidden
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
}
