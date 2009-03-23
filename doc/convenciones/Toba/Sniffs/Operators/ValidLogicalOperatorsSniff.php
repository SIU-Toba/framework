<?php
/**
 * Squiz_Sniffs_Operators_ValidLogicalOperatorsSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: ValidLogicalOperatorsSniff.php,v 1.6 2007/10/18 01:14:14 squiz Exp $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Squiz_Sniffs_Operators_ValidLogicalOperatorsSniff.
 *
 * Checks to ensure that the logical operators 'and' and 'or' are not used.
 * Use the && and || operators instead.
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
class Toba_Sniffs_Operators_ValidLogicalOperatorsSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_LOGICAL_AND,
                T_LOGICAL_OR,
                T_LOGICAL_XOR,
               );

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The current file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $replacements = array(
                         'and' => '&&',
                         'or'  => '||',
                         'xor' => '^',
                        );

        $operator = $tokens[$stackPtr]['content'];
        if (isset($replacements[$operator]) === false) {
            return;
        }

        $replacement = $replacements[$operator];
        $error       = "[Expresiones#booleanos] Logical operator \"$operator\" is prohibited; use \"$replacement\" instead";
        $phpcsFile->addError($error, $stackPtr);

    }//end process()


}//end class

?>
