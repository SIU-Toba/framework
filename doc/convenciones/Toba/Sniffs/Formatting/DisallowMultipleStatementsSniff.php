<?php
/**
 * Toba_Sniffs_Formatting_DisallowMultipleStatementsSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: DisallowMultipleStatementsSniff.php,v 1.1 2008/06/24 06:37:54 squiz Exp $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Toba_Sniffs_Formatting_DisallowMultipleStatementsSniff.
 *
 * Ensures each statement is on a line by itself.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.1.0
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Toba_Sniffs_Formatting_DisallowMultipleStatementsSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_SEMICOLON);

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in
     *                                        the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $prev = $phpcsFile->findPrevious(T_SEMICOLON, ($stackPtr - 1));
        if ($prev === false) {
            return;
        }

        // Ignore multiple statements in a FOR condition.
        if (isset($tokens[$stackPtr]['nested_parenthesis']) === true) {
            foreach ($tokens[$stackPtr]['nested_parenthesis'] as $bracket) {
                $owner = $tokens[$bracket]['parenthesis_owner'];
                if ($tokens[$owner]['code'] === T_FOR) {
                    return;
                }
            }
        }

        if ($tokens[$prev]['line'] === $tokens[$stackPtr]['line']) {
            $error = 'Each PHP statement must be on a line by itself';
            $phpcsFile->addError($error, $stackPtr);
            return;
        }

    }//end process()


}//end class

?>
