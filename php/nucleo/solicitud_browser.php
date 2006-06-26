<?php
require_once("nucleo/browser/recurso.php");					//Encapsulamiento de la llamada a recursos
require_once("nucleo/browser/js.php");						//Encapsulamiento de la utilidades javascript
require_once("nucleo/browser/debug.php");					//DUMP de arrays, arboles y estructuras centrales
require_once("nucleo/browser/vinculador.php");				//Vinculos a otros ITEMS
require_once("nucleo/lib/formateo.php"); 		//Funciones de formateo de columnas
require_once("nucleo/lib/ei.php");							//Elementos de interface
require_once("nucleo/browser/logica.php");					//Elementos de logica
require_once("nucleo/lib/parseo.php");			       		//Funciones de parseo
require_once("nucleo/lib/configuracion.php");	      		//Acceso a la configuracion del sistema
require_once("nucleo/browser/tipo_pagina/tipo_pagina.php");	//Clase base de Tipo de pagina generico
require_once("nucleo/browser/menu/menu.php");				//Clase base de Menu 
require_once("nucleo/componentes/definicion_db_transversales.php");

class solicitud_browser extends solicitud
{
	var $vinculador;	//Objeto encargado de generar nuevas solicitudes
	var $zona;			//Objeto que representa una zona que vincula varios items
	var $zona_cargada;
	var $cola_mensajes;
	
	function __construct($info)
	{
		$this->tipo_solicitud = 'browser';
		$this->info = $info;
		toba::get_cronometro()->marcar('basura',apex_nivel_nucleo);
		//toba::get_cronometro()->marcar('SOLICITUD BROWSER: Listo para cargar el ITEM',"nucleo");
		$this->hilo = toba::get_hilo();
		$item = $this->hilo->obtener_item_solicitado();
		//ATENCION: esto esjecuta un LOOP recursivo cuando un la pagina inicial es un FRAMSET
		//que tiene una direccion mal!

                
		parent::__construct($item,$this->hilo->obtener_usuario());
		
		//El elemento de item tiene que ser de tipo browser!
		if($this->tipo_solicitud!=$this->info['basica']['item_solic_tipo']) {
			monitor::evento("falta","SOLICITUD BROWSER: El ITEM de item no es de tipo: BROWSER.");
		}
		//Creo la ZONA
		if(trim($this->info['basica']['item_zona'])!=""){
			//Hay una zona, tengo que crearla...
			require_once($this->info['basica']['item_zona_archivo']);
			//Creo la clase
			$this->zona = new $this->info['basica']['item_zona']($this->info['basica']['item_zona'], 
														$this->info['basica']['item_zona_proyecto'],
														$this);
		}
		$this->vinculador = toba::get_vinculador();
		$this->cola_mensajes = toba::get_cola_mensajes();
		//Le pregunto al HILO si se solicito cronometrar la PAGINA
		if($this->hilo->usuario_solicita_cronometrar()){
			$this->registrar_db = true;
			$this->cronometrar = true;		
		}
		toba::get_cronometro()->marcar('SOLICITUD BROWSER: Inicializacion (ZONA, VINCULADOR)',"nucleo");
	}
//--------------------------------------------------------------------------------------------

	function procesar()
	{		
    	toba::get_cronometro()->marcar('basura',apex_nivel_nucleo);
		//--- Tipo de PAGINA
		toba::get_cronometro()->marcar('SOLICITUD BROWSER: Pagina TIPO (cabecera) ',apex_nivel_nucleo);
		if (isset($this->info['basica']['tipo_pagina_archivo'])) {
			require_once($this->info['basica']['tipo_pagina_archivo']);
		}
		if (isset($this->info['basica']['tipo_pagina_clase'])) {
			$tipo_pagina = new $this->info['basica']['tipo_pagina_clase']($this);
			$tipo_pagina->encabezado();
		} else {
			//--------------- MIGRACION 0.8.3 ----------------
			if(trim($this->info['basica']["item_include_arriba"]!= "")){
				toba::get_logger()->obsoleto(null, null, "0.8.3", "El tipo de página personalizado se hace con subclases");
				include_once($this->info['basica']["item_include_arriba"]);
			}			
			//-----------------------------------------------			
		}
		
		parent::procesar();
		
		//--- Dumpeo informacion del LOGGER
		flush();
		//--- Parte inferior del tipo de página
		toba::get_cronometro()->marcar('SOLICITUD BROWSER: Pagina TIPO (pie) ',apex_nivel_nucleo);
        if (isset($tipo_pagina)) {
        	$tipo_pagina->pie();
        } else {
			//--------------- MIGRACION 0.8.3 ----------------
			if(trim($this->info['basica']["item_include_abajo"]!= "")){
				toba::get_logger()->obsoleto(null, null, "0.8.3", "El tipo de página personalizado se hace con subclases");	
				include_once($this->info['basica']["item_include_abajo"]);
			}
			//-----------------------------------------------					
        }
	}

//--------------------------------------------------------------------------------------------
	function zona()
	{
		return $this->zona;
	}
	
	function hay_zona()
	{
		return isset($this->zona);	
	}
	
//--------------------------------------------------------------------------------------------

	function registrar( )
	{
		global $db;
		parent::registrar( $this->hilo->obtener_proyecto() );
		if($this->registrar_db){
			$sql = "INSERT INTO apex_solicitud_browser (solicitud_browser, sesion_browser, ip)
					VALUES ('$this->id','".$_SESSION["toba"]["id"]."','".$_SERVER["REMOTE_ADDR"]."');";
			if ($db["instancia"][apex_db_con]->Execute($sql) === false){
				monitor::evento("bug","SOLICITUD BROWSER: No se pudo registrar la solicitud: " .$db["instancia"][apex_db_con]->ErrorMsg());
			}
		}
 	}
}
?>