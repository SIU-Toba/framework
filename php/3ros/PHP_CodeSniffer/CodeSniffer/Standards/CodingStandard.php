<?php
/**
 * Bass Coding Standard class.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: CodingStandard.php,v 1.4 2008/02/01 03:19:53 squiz Exp $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Base Coding Standard class.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.1.0
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PHP_CodeSniffer_Standards_CodingStandard
{


    /**
     * Return a list of external sniffs to include with this standard.
     *
     * External locations can be single sniffs, a whole directory of sniffs, or
     * an entire coding standard. Locations start with the standard name. For
     * example:
     *  PEAR                              => include all sniffs in this standard
     *  PEAR/Sniffs/Files                 => include all sniffs in this dir
     *  PEAR/Sniffs/Files/LineLengthSniff => include this single sniff
     *
     * @return array
     */
    public function getIncludedSniffs()
    {
        return array();

    }//end getIncludedSniffs()


    /**
     * Return a list of external sniffs to exclude from this standard.
     *
     * External locations can be single sniffs, a whole directory of sniffs, or
     * an entire coding standard. Locations start with the standard name. For
     * example:
     *  PEAR                              => exclude all sniffs in this standard
     *  PEAR/Sniffs/Files                 => exclude all sniffs in this dir
     *  PEAR/Sniffs/Files/LineLengthSniff => exclude this single sniff
     *
     * @return array
     */
    public function getExcludedSniffs()
    {
        return array();

    }//end getExcludedSniffs()


}//end class

?>
