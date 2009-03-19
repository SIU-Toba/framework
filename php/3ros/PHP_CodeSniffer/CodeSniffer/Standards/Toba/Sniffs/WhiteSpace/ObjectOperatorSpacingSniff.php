<?php
/**
 * Squiz_Sniffs_WhiteSpace_ObjectOperatorSpacingSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: ObjectOperatorSpacingSniff.php,v 1.1 2007/10/09 06:33:07 squiz Exp $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Squiz_Sniffs_WhiteSpace_ObjectOperatorSpacingSniff.
 *
 * Ensure there is no whitespace before a semicolon.
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
class Toba_Sniffs_WhiteSpace_ObjectOperatorSpacingSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_OBJECT_OPERATOR);

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $prevType = $tokens[($stackPtr - 1)]['code'];
        if (in_array($prevType, PHP_CodeSniffer_Tokens::$emptyTokens) === true) {
            $nonSpace = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr - 2), null, true);
            $expected = $tokens[$nonSpace]['content'].'->';
            $found    = $phpcsFile->getTokensAsString($nonSpace, ($stackPtr - $nonSpace)).'->';
            $error    = "Space found before object operator; expected \"$expected\" but found \"$found\"";
            $phpcsFile->addError($error, $stackPtr);
        }

        $nextType = $tokens[($stackPtr + 1)]['code'];
        if (in_array($nextType, PHP_CodeSniffer_Tokens::$emptyTokens) === true) {
            $nonSpace = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr + 2), null, true);
            $expected = '->'.$tokens[$nonSpace]['content'];
            $found    = $phpcsFile->getTokensAsString($stackPtr, ($nonSpace - $stackPtr + 1));
            $error    = "Space found after object operator; expected \"$expected\" but found \"$found\"";
            $phpcsFile->addError($error, $stackPtr);
        }

    }//end process()


}//end class

?>
