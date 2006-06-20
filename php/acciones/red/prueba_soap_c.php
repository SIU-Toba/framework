<?php
// ##### -> SOAP Client

$opts = array('location'   => 'http://localhost:8080/toba/soap.php?ai=toba||/red/soap_test_server',
              'uri'        => 'urn:servicioHola',
              'exceptions' => 0);

$client = new SoapClient(NULL, $opts);
$temp = $client->sayHello('*** Probando SOAP en toba ***');

print_r($temp);

?>
