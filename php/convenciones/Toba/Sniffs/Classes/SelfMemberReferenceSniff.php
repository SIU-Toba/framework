<?php
/**
 * Squiz_Sniffs_Classes_ClassFileNameSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: SelfMemberReferenceSniff.php,v 1.5 2007/12/05 03:31:16 squiz Exp $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (class_exists('PHP_CodeSniffer_Standards_AbstractScopeSniff', true) === false) {
    $error = 'Class PHP_CodeSniffer_Standards_AbstractScopeSniff not found';
    throw new PHP_CodeSniffer_Exception($error);
}

/**
 * Tests self member references.
 *
 * Verifies that :
 * <ul>
 *  <li>self:: is used instead of Self::</li>
 *  <li>self:: is used for local static member reference</li>
 *  <li>self:: is used instead of self ::</li>
 * </ul>
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
class Toba_Sniffs_Classes_SelfMemberReferenceSniff extends PHP_CodeSniffer_Standards_AbstractScopeSniff
{


    /**
     * Constructs a Squiz_Sniffs_Classes_SelfMemberReferenceSniff.
     */
    public function __construct()
    {
        parent::__construct(array(T_CLASS), array(T_DOUBLE_COLON));

    }//end __construct()


    /**
     * Processes the function tokens within the class.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file where this token was found.
     * @param int                  $stackPtr  The position where the token was found.
     * @param int                  $currScope The current scope opener token.
     *
     * @return void
     */
    protected function processTokenWithinScope(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $currScope)
    {
        $tokens = $phpcsFile->getTokens();

        $className = ($stackPtr - 1);
        if ($tokens[$className]['code'] === T_SELF) {
            if (strtolower($tokens[$className]['content']) !== $tokens[$className]['content']) {
                $error = '[Funciones#invocacion] Must use "self::" for local static member reference; found "'.$tokens[$className]['content'].'::"';
                $phpcsFile->addError($error, $className);
                return;
            }
        } else if ($tokens[$className]['code'] === T_STRING) {
            // Make sure this is another class reference.
            $declarationName = $phpcsFile->getDeclarationName($currScope);
            if ($declarationName === $tokens[$className]['content']) {
                // Class name is the same as the current class.
                $error = '[Funciones#invocacion] Must use "self::" for local static member reference';
                $phpcsFile->addError($error, $className);
                return;
            }
        }

        if ($tokens[($stackPtr - 1)]['code'] === T_WHITESPACE) {
            $found = strlen($tokens[($stackPtr - 1)]['content']);
            $error = "[Funciones#invocacion] Expected 0 spaces before double colon; $found found";
            $phpcsFile->addError($error, $className);
        }

        if ($tokens[($stackPtr + 1)]['code'] === T_WHITESPACE) {
            $found = strlen($tokens[($stackPtr + 1)]['content']);
            $error = "[Funciones#invocacion] Expected 0 spaces after double colon; $found found";
            $phpcsFile->addError($error, $className);
        }

    }//end processTokenWithinScope()


}//end class

?>
