<?php
/**
 * An exception thrown by PHP_CodeSniffer when it encounters an unrecoverable error.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: Exception.php,v 1.3 2006/12/11 23:43:39 squiz Exp $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

require_once 'PEAR_Exception.php';

/**
 * An exception thrown by PHP_CodeSniffer when it encounters an unrecoverable error.
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
class PHP_CodeSniffer_Exception extends PEAR_Exception
{

}//end class

?>