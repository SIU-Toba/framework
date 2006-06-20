<?
class servicioHola {	

    // Your function
    function sayHello($inputString)
    {
        return 'returned: <b>'.$inputString.'</b>';
    }
}

$server = new SoapServer(NULL, array('uri' => 'urn:servicioHola')); 
$server->setClass('servicioHola');
$server->handle();



?>