<?
echo "TETETETE";
exit;
// Servicio web
class servicioHola {	

    // Your function
    function sayHello($inputString)
    {
        return 'Hello <b>'.$inputString.'</b>';
    }
}

//$server = new SoapServer(NULL, array('uri' => 'urn:servicioHola')); 
$server = new SoapServer(NULL, array('uri' => 'http://localhost:8080/toba/soap.php?ai=toba||/red/soap_test_server')); 
$server->setClass('servicioHola');
$server->handle();



?>