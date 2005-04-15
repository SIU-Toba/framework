<?php
require_once("nucleo/browser/clases/objeto_ci_me_tab.php");

class ci extends objeto_ci_me_tab 
{ 
    function __construct($id) 
    { 
        parent::__construct($id); 
    } 

    function obtener_interface_2() 
    { 
        $this->cn->debug(); 
    } 
} 

?>