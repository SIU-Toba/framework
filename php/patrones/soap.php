<?php
// Patron

require_once 'SOAP/Server.php';
// Fire up PEAR::SOAP_Server
$server = new SOAP_Server;

// Fire up your class
require_once($this->info["item_parametro_b"]);
$sentencia = "\$server_especifico = new " . $this->info["item_parametro_a"] . "();";

eval($sentencia);

// Add your object to SOAP server (note namespace)
$server->addObjectMap($server_especifico,'urn:'.$this->info["item_parametro_a"]);

// Handle SOAP requests coming is as POST data
if (isset($_SERVER['REQUEST_METHOD']) &&
    $_SERVER['REQUEST_METHOD']=='POST') {
    $server->service($HTTP_RAW_POST_DATA);
} 


?>
