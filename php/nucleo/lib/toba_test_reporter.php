<?php
require_once('3ros/simpletest/reporter.php');

class toba_test_reporter extends HtmlReporter 
{

    function paintHeader($test_name) {
        print "<style type=\"text/css\">\n";
        print $this->_getCss() . "\n";
        print "</style>\n";
        print "<h1>$test_name</h1>\n";
        flush();
    }
    
    function paintFooter($test_name) {
        $colour = ($this->getFailCount() + $this->getExceptionCount() > 0 ? "red" : "green");
        print "<div style=\"";
        print "padding: 8px; margin-top: 1em; background-color: $colour; color: white;";
        print "\">";
        print $this->getTestCaseProgress() . "/" . $this->getTestCaseCount();
        print " test cases complete:\n";
        print "<strong>" . $this->getPassCount() . "</strong> passes, ";
        print "<strong>" . $this->getFailCount() . "</strong> fails and ";
        print "<strong>" . $this->getExceptionCount() . "</strong> exceptions.";
        print "</div>\n";
    }
}

?>