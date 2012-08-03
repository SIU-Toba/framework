<?php
require_once('comando_toba.php');

class comando_servicios_web extends comando_toba
{
	static function get_info()
	{
		return 'Administracion de servicios web';
	}
	
	function mostrar_observaciones()
	{
		$this->consola->mensaje("INVOCACION: toba servicios_web OPCION");
		$this->consola->enter();
	}
	
	protected function get_servicio_serv() 
	{
		$proyecto = $this->get_proyecto();
		$parametros = $this->get_parametros();
		$servicios_disponibles = toba_info_editores::get_items_servicios_web($proyecto->get_id());		
		if (isset($parametros['-s'])) {
			$servicio = $parametros['-s'];
			foreach($servicios_disponibles as $serv) {
				if ($servicio == $serv['item']) {
					return $servicio;
				}
			}			
			throw new toba_error("El servicio $servicio no esta definido en el proyecto");
		} else {
			//Elijo el servicio web sobre el que trabajare
			$servicios_lista = array();
			foreach($servicios_disponibles as $serv) {
				$servicios_lista[$serv['item']] = $serv['nombre'];
			}
			$servicio = $this->consola->dialogo_lista_opciones($servicios_lista, 'Seleccione el servicio web');
		}		
		return $servicio;
	} 
	
	protected function get_servicio_cli()
	{
		$proyecto = $this->get_proyecto();
		$parametros = $this->get_parametros();
		$servicios_disponibles = toba_info_editores::get_servicios_web_acc($proyecto->get_id());		
		if (isset($parametros['-s'])) {
			$servicio = $parametros['-s'];
			foreach($servicios_disponibles as $serv) {
				if ($servicio == $serv['servicio_web']) {
					return $servicio;
				}
			}
			throw new toba_error("El consumo del servicio $servicio no esta definido en el proyecto");
		} else {
			//Elijo el servicio web sobre el que trabajare
			$servicios_lista = array();
			foreach($servicios_disponibles as $serv) {
				$servicios_lista[$serv['servicio_web']] = $serv['servicio_web'];
			}
			$servicio = $this->consola->dialogo_lista_opciones($servicios_lista, 'Seleccione el servicio web');
		}
		return $servicio;
	}	
	
	//--------------------------------------------------------------------------------------------------------------------------------------//
	//								SERVER										   //
	//--------------------------------------------------------------------------------------------------------------------------------------//	
	/**
	 * Genera la documentacion de los servicios web provistos por el proyecto (requiere tener publicado el proyecto en localhost)
	 *        -r 0|1 reemplaza la documentacion existente
	 */
	function opcion__serv_generar_doc()
	{
		if (! extension_loaded('wsf')) {
			throw new toba_error('La extensión wsf no se encuentra cargada, verifique la instalación.');
		}
		$param = $this->get_parametros();
		$forzar_reemplazo = isset($param["-r"]) ? ($param["-r"] == 1) : false;
				
		$proyecto =$this->get_proyecto();
		$servicios = toba_info_editores::get_items_servicios_web();
		$carpeta_doc = $proyecto->get_dir()."/doc/servicios_web";
		if (! file_exists($carpeta_doc)) {
			mkdir($carpeta_doc, 0777, true);
		}				
		$this->consola->mensaje("Generando documentacion...");		
		$index_page = "<html><head>
		<link href='wsdl-viewer.css' rel='stylesheet' type='text/css' media='screen'/>
		</head>
		<body>
		<div id='header'>
		<h1>{$proyecto->get_id()}</h1><h2>Documentación Servicios Web</h2>
		</div>
		<div id='inner_box'><div class='page'>
		<ul>";
		foreach ($servicios as $serv_datos) {
			$servicio = toba_modelo_catalogo::get_servicio_web($proyecto, $serv_datos['item'], $this->consola);			
			$file = $servicio->generar_documentacion($carpeta_doc, $forzar_reemplazo);
			$index_page .= "<li><a href='$file'>{$serv_datos['item']}</a></li>";
			unset($servicio);
		}
		$index_page .= "</ul></div></div></body></html>";
		file_put_contents($carpeta_doc."/index.html", $index_page);
		$this->consola->mensaje("Listo. Navegar hacia file://".$carpeta_doc."/index.html");
	}
	
	/**
	 *  Genera la configuracion de un cliente de servicio, importando el certificado del servidor
	 *   -p Proyecto
	 *   -s cliente a configurar
	 *   -c Certificado del servidor a importar
 	 *   -u URL del sistema (opcional)        
	 */
	function opcion__cli_configurar()
	{
		$parametros = $this->get_parametros();
		$proyecto = $this->get_proyecto();
		$servicio = toba_modelo_catalogo::get_servicio_web($proyecto, $this->get_servicio_cli(), $this->consola);
	
		$dir_instalacion = $proyecto->get_dir_instalacion_proyecto();
		if (! toba_modelo_servicio_web::existe_archivo_certificado($proyecto)) {
			$this->consola->error("No existe la clave privada/certificado del proyecto en '$dir_instalacion/privada.key'. Puede generarlos usando el comando generar_cert");
			die;
		}
		if (!isset($parametros['-c'])) {
			$this->consola->error("Debe incluir el parametro -c con la ruta al archivo de certificado del servidor");
			die;
		}
		if (! file_exists($parametros['-c'])) {
			$this->consola->error("No fue posible encontrar el certificado en '{$parametros['-c']}'");
			die;
		}
			
		//Se especifico una URL?
		$url_sistema = null;
		if (isset($parametros['-u'])) {
			$url_sistema = $parametros['-u'];
		}
		
		//Genera configuracion
		$servicio->generar_configuracion_cliente($parametros['-c'], $url_sistema);
	
		$this->consola->mensaje("Ok. Certificado del servidor importado correctamente en el cliente");
	}
	
	/**
	 *  Genera la configuracion de un servicio, importando el certificado del cliente
	 *   -p Proyecto
	 *   -s Servicio a configurar
	 *   -c Certificado del cliente a importar
	 *   -h clave=valor Identificador del cliente, varias opciones separadas por coma (,)
	 */
	function opcion__serv_configurar()
	{
		$parametros = $this->get_parametros();
		$proyecto = $this->get_proyecto();
		$servicio = toba_modelo_catalogo::get_servicio_web($proyecto,  $this->get_servicio_serv(), $this->consola);

		$dir_instalacion = $proyecto->get_dir_instalacion_proyecto();
		if (! toba_modelo_servicio_web::existe_archivo_certificado($proyecto)) {
			$this->consola->error("No existe la clave privada/certificado del proyecto en '$dir_instalacion/privada.key'. Puede generarlos usando el comando generar_cert");
			die;
		}		
		if (!isset($parametros['-c'])) {
			$this->consola->error("Debe incluir el parametro -c con la ruta al archivo de certificado del cliente");
			die;			
		}
		if (! file_exists($parametros['-c'])) {
			$this->consola->error("No fue posible encontrar el certificado en '{$parametros['-c']}'");
			die;
		}
		
		if (!isset($parametros['-h'])) {
			$this->consola->error("Debe incluir el parametro -h con los pares clave=valor que identifican al cliente");
			die;
		}
		
		//Parseo el ID del cliente
		$headers = array();
		$param_headers = explode(",", trim($parametros['-h']));
		foreach ($param_headers as $param_header) {
			list($clave, $valor) = explode("=", trim($param_header));
			$headers[$clave] = $valor;
		}		
		
		//Genera configuracion
		$servicio->generar_configuracion_servidor($parametros['-c'], $headers);

		$this->consola->mensaje("Ok. Certificado del cliente importado correctamente en el servidor");
	}	
	
	/**
	 *  Genera o configura los archivos de certificados necesario para seguridad a nivel capa de transporte
	 *       Opcional: -r Reemplaza los certificados actuales
	 *      
	 */
	function opcion__generar_cert()
	{		
		$parametros = $this->get_parametros();
		//Creo el directorio para el servicio web
		$proyecto = $this->get_proyecto();
		$dir_instalacion = $proyecto->get_dir_instalacion_proyecto();		
		if (toba_modelo_servicio_web::existe_archivo_certificado($proyecto) && ! isset($parametros['-r'])) {
			$this->consola->error("Ya existe la clave privada del proyecto en '$dir_instalacion/privada.key'. Para sobreescribirla indique el parametro -r");
			die;
		}
		
		try {
			toba_modelo_servicio_web::generar_certificados($proyecto, $dir_instalacion);
			$this->consola->mensaje("Ok. Certificado publico generado en:");
			$this->consola->mensaje("$dir_instalacion/publica.crt");				
		} catch (toba_error_usuario $e) {
			$this->consola->mensaje('Se ha producido un error durante el proceso', true);
			$this->consola->error($e->getMessage());
		}
	}	
}
?>