<?php
// --------------------------------------------------------------
// -------- Pruebas SOAP ----------------------------------------
// --------------------------------------------------------------

require_once 'SOAP/Server.php';
require_once("nucleo/lib/comunicador_soap.php");

// Your class
class HelloServer {
    var $__dispatch_map = array();

    function HelloServer() {
        // Define the signature of the dispatch map
        $this->__dispatch_map['sayHello'] =
            array('in' => array('inputString' => 'string'),
                  'out' => array('outputString' => 'string'),
                  );
    }

    // Required function by SOAP_Server
    function __dispatch($methodname) {
        if (isset($this->__dispatch_map[$methodname]))
            return $this->__dispatch_map[$methodname];
        return NULL;
    }

    // Your function
    function sayHello($inputString)
    {
        return '<b>Webservice: '.$inputString.'</b>';
    }
}

// Fire up PEAR::SOAP_Server
$server = new SOAP_Server;

// Fire up your class
$helloServer = new HelloServer();

// Add your object to SOAP server (note namespace)
$server->addObjectMap($helloServer,'urn:HelloServer');

// Handle SOAP requests coming is as POST data
if (isset($_SERVER['REQUEST_METHOD']) &&
    $_SERVER['REQUEST_METHOD']=='POST') {
    $server->service($HTTP_RAW_POST_DATA);
} else {
    // Deal with WSDL / Disco here
    require_once 'SOAP/Disco.php';

    // Create the Disco server
    $disco = new SOAP_DISCO_Server($server,'HelloServer');
    header("Content-type: text/xml");
    if (isset($_SERVER['QUERY_STRING']) &&
        strcasecmp($_SERVER['QUERY_STRING'],'wsdl')==0) {
        echo $disco->getWSDL(); // if we're talking http://www.example.com/index.php?wsdl
    } else {
        echo $disco->getDISCO();
    }
    exit;
}

//---------------------------------------------------
?>