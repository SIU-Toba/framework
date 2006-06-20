<?

	//Por que WDDX -> solicitud_instancia ??

	if($editable = $this->zona->obtener_editable_propagado())
	{
		$this->zona->cargar_editable();
		$this->zona->obtener_html_barra_superior();
		
	    require_once("nucleo/lib/comunicador.php"); 
		require_once("nucleo/lib/paquete_toba.php");
		require_once("nucleo/lib/elemento_toba.php");
	
		$elemento = new elemento_toba_item();
		$elemento->cargar_db($editable[0],$editable[1]);
		
		$paquete = new paquete_toba();
		$paquete->set_descripcion("Prueba de envio de paquetes");
		$paquete->agregar_elemento($elemento);

		$mensaje = array(serialize($paquete));
	
	    //$ip = "168.83.60.146"; 
	    //$puerto = 3333; 
	    //$ip = "168.83.60.212"; 
	    //$puerto = 8080; 

	    //-------------------------------------
		//------ Datos del TOBA receptor ------
	    //-------------------------------------
	    //Direccion
	    $ip = "192.168.0.10"; 
	    $puerto = 3333; 
	    //Punto de acceso
	    $punto_acceso = "/toba/wddx.php"; 
	    //Item
	    $item = array('admin',"/input");
	    //-------------------------------------
	    //-------------------------------------
	
	    $instancia_receptora =& new comunicador($ip, $puerto, $punto_acceso); 
	    if( $instancia_receptora->transmitir($mensaje, $item, true, true) ){ 
	        //ei_arbol($instancia_receptora->obtener_headers(),"RESPONSE - HEADERS"); 
	        //ei_arbol($instancia_receptora->obtener_body(),"RESPONSE - BODY"); 
	        ei_arbol( $instancia_receptora->obtener_datos(),"RESPONSE - DATOS"); 
	    }else{ 
	        echo "No se pudo enviar el MENSAJE"; 
	        ei_arbol($instancia_receptora->obtener_headers(),"RESPONSE - HEADERS"); 
	    } 
		

		$this->zona->obtener_html_barra_inferior();
	}else{
		echo ei_mensaje("No se explicito el ELEMENTO a editar","error");
	}
?>