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
		if (!file_exists($dir_instalacion.'/privada.key')) {
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
	
		//Creo el directorio para el servicio web
		$punto_partida =  $proyecto->get_dir_instalacion_proyecto();
		$dir_servicio = $punto_partida . '/servicios_cli/'. $servicio->get_id();
		toba_manejador_archivos::crear_arbol_directorios($dir_servicio, 0755);
		
		//Se especifico una URL?
		$url_sistema = null;
		if (isset($parametros['-u'])) {
			$url_sistema = $parametros['-u'];
		}
		
		//Genera configuracion
		$servicio->generar_configuracion_cliente($dir_servicio, $parametros['-c'], $url_sistema);
	
		$this->consola->mensaje("Ok. Certificado del servidor importado correctamente en el cliente");
	}
	
	/**
	 *  Genera la configuracion de un servicio, importando el certificado del cliente
	 *   -p Proyecto
	 *   -s Servicio a configurar
	 *   -c Certificado del cliente a importar
	 *   -h clave=valor Identificador del cliente
	 */
	function opcion__serv_configurar()
	{
		$parametros = $this->get_parametros();
		$proyecto = $this->get_proyecto();
		//$instalacion = new toba_modelo_instalacion();
		$servicio = toba_modelo_catalogo::get_servicio_web($proyecto,  $this->get_servicio_serv(), $this->consola);

		$dir_instalacion = $proyecto->get_dir_instalacion_proyecto();
		if (!file_exists($dir_instalacion.'/privada.key')) {
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
		
		//Creo el directorio para el servicio web
		$punto_partida =  $proyecto->get_dir_instalacion_proyecto();
		$dir_servicio_servidor = $punto_partida . '/servicios_serv/'. $servicio->get_id();
		toba_manejador_archivos::crear_arbol_directorios($dir_servicio_servidor, 0755);

		//Parseo el ID del cliente
		$headers = array();
		$param_headers = explode(",", trim($parametros['-h']));
		foreach ($param_headers as $param_header) {
			list($clave, $valor) = explode("=", trim($param_header));
			$headers[$clave] = $valor;
		}		
		
		//Genera configuracion
		$servicio->generar_configuracion_servidor($dir_servicio_servidor, $headers, $parametros['-c']);

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
		if (file_exists($dir_instalacion.'/privada.key') && ! isset($parametros['-r'])) {
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
		//$this->generar_certificado($dir_instalacion);
	}
	
	//----------------------------------------------------------------------------------------------------------------------------------------------------
	//							METODOS AUXILIARES
	//----------------------------------------------------------------------------------------------------------------------------------------------------
	
	
	/*protected function generar_certificado($directorio)
	{
		$dir_inst = $this->get_instalacion()->get_dir();
		if (! file_exists($dir_inst.'/openssl.ini')) {
			$this->consola->error("No existe el archivo '$dir_inst/openssl.ini'. Necesita copiarlo de la carpeta toba/php/modelo/var");
			$this->consola->enter();
			die;			
		}
		
		$cmd = "openssl req -x509 -nodes -days 20000 -newkey rsa:1024 -keyout $directorio/privada.key.sign -config $dir_inst/openssl.ini -out $directorio/publica.crt";
		$exito = toba_manejador_archivos::ejecutar($cmd, $stdout, $stderr);
		if ($exito != '0') {
			$this->consola->error($stderr);
			$this->consola->error("Asegurese tener instalados los binarios de OpenSSL y disponibles en el path. Para comprobar ejecute 'openssl version'");
			$this->consola->enter();
			die;
		}
		
		$cmd = "openssl rsa -in $directorio/privada.key.sign -out $directorio/privada.key";
		$exito = toba_manejador_archivos::ejecutar($cmd, $stdout, $stderr);
		if ($exito != '0') {
			$this->consola->error($stderr);
			$this->consola->enter();
			die;
		}		
		unlink("$directorio/privada.key.sign");
	}*/
	
	/**
	 * Asocia el proyecto y servicio con el nombre del archivo que contiene la clave publica
	 * @param toba_modelo_proyecto $proyecto
	 * @param string $servicio
	 * @param array $headers
	 * @param string $destino 
	 */
	/*protected function generar_configuracion_servidor($directorio, $headers=array(), $cert_cliente)
	{
		$config = new toba_ini($directorio . '/servicio.ini');
		if (! $config->existe_entrada("certificado")) {
			$cert = array();
			$cert['clave_servidor'] = "../../privada.key";	//Se utiliza la clave de todo el proyecto
			$cert['cert_servidor'] = "../../publica.crt";	//Se utiliza el cert de todo el proyecto
			$config->agregar_entrada('certificado', $cert);
		}
		
		//Armo ID de cliente
		$nombre = array();
		ksort($headers);
		foreach ($headers as $id => $valor) {
			$nombre[] = $id.'='.$valor;
		}
		$nombre = implode(',', $nombre);

		//Guarda el certificado del cliente
		$nombre_archivo = str_replace("=", "_", $nombre);
		copy($cert_cliente, $directorio."/$nombre_archivo.crt");
		
		$datos = array();
		$datos['archivo'] = "./$nombre_archivo.crt";
		$datos['fingerprint'] = sha1(toba_servicio_web::decodificar_certificado($directorio."/$nombre_archivo.crt"));
		$config->agregar_entrada($nombre, $datos);
		
		$config->guardar();
	}*/
	
	/**
	 * Graba el archivo de configuracion del servicio dentro del directorio que luego sera enviado al cliente.
	 * @param array $datos_cert
	 * @param array $datos_rsa
	 * @param string $directorio 
	 */
	/*protected function generar_configuracion_cliente($directorio, $cert_servidor, $url_sistema)
	{
		$config = new toba_ini($directorio . '/cliente.ini');
		if ($url_sistema != null) {
			$config->agregar_entrada("conexion", array('to' => $url_sistema));
		}
		if (! $config->existe_entrada('certificado', 'clave_cliente')) {
			$cert = array();
			$cert['clave_cliente'] = "../../privada.key";	//Se utiliza la clave de todo el proyecto
			$cert['cert_cliente'] = "../../publica.crt";	//Se utiliza el cert de todo el proyecto
		} else {
			$cert = $config->get_datos_entrada("certificado");  //Mantiene clave y cert actuales del cliente
		}

		//Guarda el certificado del servidor
		copy($cert_servidor, $directorio."/cert_servidor.crt");
		$cert['cert_servidor'] = "./cert_servidor.crt";
		$config->agregar_entrada('certificado', $cert);
				
		if (! empty($datos_cert)) {
			$config->agregar_entrada('certificado', $datos_cert);
		}
		$config->guardar();		
	}*/
	
}
?>