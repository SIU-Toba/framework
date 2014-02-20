<?php
/**
 * Toba_Sniffs_PHP_DisallowShortOpenTagSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: DisallowShortOpenTagSniff.php,v 1.7 2007/07/23 01:47:52 squiz Exp $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Toba_Sniffs_PHP_DisallowShortOpenTagSniff.
 *
 * Makes sure that shorthand PHP open tags are not used.
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
class Toba_Sniffs_PHP_DisallowShortOpenTagSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_OPEN_TAG,
                T_OPEN_TAG_WITH_ECHO,
               );

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        // If short open tags are off, then any short open tags will be converted
        // to inline_html tags so we can just ignore them.
        // If its on, then we want to ban the use of them.
        $option = ini_get('short_open_tag');

        // Ini_get returns a string "0" if short open tags is off.
        if ($option === '0') {
            return;
        }

        $tokens  = $phpcsFile->getTokens();
        $openTag = $tokens[$stackPtr];

        if ($openTag['content'] === '<?') {
            $error = '[Clases#archivo] Short PHP opening tag used. Found "'.$openTag['content'].'" Expected "<?php".';
            $phpcsFile->addError($error, $stackPtr);
        }

        if ($openTag['code'] === T_OPEN_TAG_WITH_ECHO) {
            $nextVar = $tokens[$phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr + 1), null, true)];
            $error   = '[Clases#archivo] Short PHP opening tag used with echo. Found "';
            $error  .= $openTag['content'].' '.$nextVar['content'].' ..." but expected "<?php echo '.$nextVar['content'].' ...".';
            $phpcsFile->addError($error, $stackPtr);
        }

    }//end process()


}//end class

?>
