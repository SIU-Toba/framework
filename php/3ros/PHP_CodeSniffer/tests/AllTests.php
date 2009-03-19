<?php
/**
 * A test class for running all PHP_CodeSniffer unit tests.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: AllTests.php,v 1.6 2007/08/15 01:27:30 squiz Exp $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'PHP_CodeSniffer_AllTests::main');
}

require_once 'TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

if (is_file(dirname(__FILE__).'/../CodeSniffer.php') === true) {
    // We are not installed.
    include_once 'Core/AllTests.php';
    include_once 'Standards/AllSniffs.php';
    include_once dirname(__FILE__).'/../CodeSniffer.php';
} else {
    include_once 'CodeSniffer/Core/AllTests.php';
    include_once 'CodeSniffer/Standards/AllSniffs.php';
    include_once 'PHP/CodeSniffer.php';
}

/**
 * A test class for running all PHP_CodeSniffer unit tests.
 *
 * Usage: phpunit AllTests.php
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
class PHP_CodeSniffer_AllTests
{


    /**
     * Prepare the test runner.
     *
     * @return void
     */
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());

    }//end main()


    /**
     * Add all PHP_CodeSniffer test suites into a single test suite.
     *
     * @return PHPUnit_Framework_TestSuite
     */
    public static function suite()
    {
        // Use a special PHP_CodeSniffer test suite so that we can
        // unset our autoload function after the run.
        $suite = new PHP_CodeSniffer_TestSuite('PHP CodeSniffer');

        $suite->addTest(PHP_CodeSniffer_Core_AllTests::suite());
        $suite->addTest(PHP_CodeSniffer_Standards_AllSniffs::suite());

        // Unregister this here because the PEAR tester loads
        // all package suites before running then, so our autoloader
        // will cause problems for the packages included after us.
        spl_autoload_unregister(array('PHP_CodeSniffer', 'autoload'));

        return $suite;

    }//end suite()


}//end class

?>
