<?php
/**
 * PEAR_Sniffs_NamingConventions_ValidClassNameSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: ValidClassNameSniff.php,v 1.10 2008/04/28 01:02:28 squiz Exp $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * PEAR_Sniffs_NamingConventions_ValidClassNameSniff.
 *
 * Ensures class and interface names start with a capital letter
 * and use _ separators.
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
class PEAR_Sniffs_NamingConventions_ValidClassNameSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_CLASS,
                T_INTERFACE,
               );

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The current file being processed.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $className = $phpcsFile->findNext(T_STRING, $stackPtr);
        $name      = trim($tokens[$className]['content']);

        // Make sure the first letter is a capital.
        if (preg_match('|^[A-Z]|', $name) === 0) {
            $error = ucfirst($tokens[$stackPtr]['content']).' name must begin with a capital letter';
            $phpcsFile->addError($error, $stackPtr);
        }

        // Check that each new word starts with a capital as well, but don't
        // check the first word, as it is checked above.
        $validName = true;
        $nameBits  = explode('_', $name);
        $firstBit  = array_shift($nameBits);
        foreach ($nameBits as $bit) {
            if ($bit === '' || $bit{0} !== strtoupper($bit{0})) {
                $validName = false;
                break;
            }
        }

        if ($validName !== true) {
            // Strip underscores because they cause the suggested name
            // to be incorrect.
            $nameBits = explode('_', trim($name, '_'));
            $firstBit = array_shift($nameBits);
            if ($firstBit === '') {
                $error = ucfirst($tokens[$stackPtr]['content']).' name is not valid';
            } else {
                $newName = strtoupper($firstBit{0}).substr($firstBit, 1).'_';
                foreach ($nameBits as $bit) {
                    if ($bit !== '') {
                        $newName .= strtoupper($bit{0}).substr($bit, 1).'_';
                    }
                }

                $newName = rtrim($newName, '_');
                $error   = ucfirst($tokens[$stackPtr]['content'])." name is not valid; consider $newName instead";
            }

            $phpcsFile->addError($error, $stackPtr);
        }//end if

    }//end process()


}//end class


?>
