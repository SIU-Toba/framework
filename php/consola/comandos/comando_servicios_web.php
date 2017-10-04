<?php
require_once('comando_toba.php');
/**
 * Publica los servicios de la clase servicios_web a la consola toba
 * @package consola
 */

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
	
	//--------------------------------------------------------------------------------------------------------------------------------------//
	//								SERVER										   //
	//--------------------------------------------------------------------------------------------------------------------------------------//	
	/**
	 * Genera la documentacion de los servicios web provistos por el proyecto (requiere tener publicado el proyecto en localhost)
	 *  -r 0|1 reemplaza la documentacion existente
	 *  --tipo_ws soap|rest indica el tipo de web service (default BC:  soap)
	 * @consola_parametros Opcional: [--nombre-archivo] Ruta y nombre del archivo donde se guardara la documentacion
	 */
	function opcion__serv_generar_doc()
	{
		$param = $this->get_parametros();
		$tipo = (isset($param['--tipo_ws']))? $param['--tipo_ws'] : 'soap';
		switch ($tipo) {
			case 'soap':
				$this->generar_doc_soap($param);
				break;
			case 'rest':
				$this->generar_doc_rest($param);
				break;
			default: 
				$this->consola->error('Tipo de Web Service no reconocido');
				die;
		}
		$this->consola->progreso_fin();
	}
	
	/**
	 *  Genera la configuracion de un servicio, importando el certificado del cliente
	 *  -p Proyecto
	 *  -s Servicio a configurar (SOAP)
	 *  -h clave=valor Identificador del cliente, varias opciones separadas por coma (,) (SOAP)* 
	 *  -c Certificado del cliente a importar
	 *  --tipo_ws soap|rest indica el tipo de web service (default BC:  soap) 
	 *  --auth_tipo ssl|digest|basic indica el tipo de autenticacion a utilizar en el servidor (default digest)
	 *  --usuario Nombre usuario para autenticar (REST)
	 *  --usuario_pwd Password para autenticacion basica (REST)
	 *  --encoding Encoding de la respuesta
	 */
	function opcion__serv_configurar()
	{
		$parametros = $this->get_parametros();
		$tipo = (isset($parametros['--tipo_ws'])) ? $parametros['--tipo_ws'] : 'soap';		
		switch ($tipo) {
			case 'soap':
					$this->server_soap_configurar($parametros);
					break;
			case 'rest':					
					$this->server_rest_configurar($parametros);
					break;
			default:
				$this->consola->error('Tipo de Web Service no reconocido');
				die;
		}
		$this->consola->progreso_fin();
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
	
	//--------------------------------------------------------------------------------------------------------------------------------------//
	//								CLIENTE										   //
	//--------------------------------------------------------------------------------------------------------------------------------------//		
	/**
	 *  Genera la configuracion de un cliente de servicio web
	 *  -p Proyecto
	 *  -s cliente a configurar (id del servicio web)
	 *  -c Certificado del servidor  o CA a importar
 	 *  -u URL del sistema (opcional)
	 *  --tipo_ws soap|rest indica el tipo de web service (default BC:  soap)
	 *  --cert_cli Archivo con el certificado del cliente (REST)
	 *  --key_cli Archivo con la clave privada (REST)
	 *  --cert_pwd Password para el certificado si asi lo requiere (REST)
	 *  --usuario Nombre usuario para autenticar (REST)
	 *  --usuario_pwd Password para autenticacion basica (REST)
	 *  --all Configura todos los WS consumibles con los mismos datos
	 */	
	function opcion__cli_configurar()
	{
		$parametros = $this->get_parametros();
		$tipo = (isset($parametros['--tipo_ws'])) ? $parametros['--tipo_ws'] : 'soap';		
		$todos = (isset($parametros['--all']));
		if ($tipo == 'soap') {
			$consumibles = ($todos) ? toba_modelo_soap::get_lista_servicios_consumibles($this->get_proyecto()): array();
		} elseif ($tipo == 'rest') {
			$consumibles = ($todos) ? toba_modelo_rest::get_lista_servicios_consumibles($this->get_proyecto()): array();
		} else {
			$this->consola->error('Tipo de Web Service no reconocido');
			die;
		}		
		if ($todos && empty($consumibles)) {
			$this->consola->error('No existen Servicios Web consumibles, omitiendo configuración');			
		} else {
			do {	
				$actual = current($consumibles);
				if ($actual !== false) {
					$parametros['-s'] = $actual['servicio_web'];
				}				
				switch ($tipo) {
				case 'soap':	$this->cliente_soap_configurar($parametros);
							break;
				case 'rest':	$this->cliente_rest_configurar($parametros);
							break;
				}
			} while (next($consumibles) !== false);
		}		
		$this->consola->progreso_fin();
	}
	
	//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
	//											METODOS AUXILIARES	
	//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
	private function generar_doc_rest($params)
	{	
		$proyecto = $this->get_proyecto();		
		//Cargo los autoloaders del proyecto, esto no ingresa por el nucleo ergo se debe hacer manualmente
		$punto_php = $proyecto->get_pms()->get(toba_modelo_pms::pm_php);
		$punto_php->set_instancia_toba($this->get_instancia());
		$punto_php->registrar_autoload();
		if ($proyecto->es_personalizable()) {
			$punto_pers = $proyecto->get_pms()->get(toba_modelo_pms::pm_pers);
			$punto_pers->set_instancia_toba($this->get_instancia());
			$punto_pers->registrar_autoload();
		}
		
		//Genero la doc y la envio al archivo indicado
		$archivo = 'doc-apiRest.json';		
		if (isset($params['--nombre-archivo'])) {
			$archivo = $params['--nombre-archivo'];
		}		
		$datos = $proyecto->get_documentacion_rest();		
		if (trim($datos) != '') {
			file_put_contents($archivo, $datos);
			$this->consola->mensaje('Contenido generado, verifique el archivo json');
		}
	}
	
	private function generar_doc_soap($param)
	{
		if (! extension_loaded('wsf')) {
			throw new toba_error('La extensión wsf no se encuentra cargada, verifique la instalación.');
		}
		$forzar_reemplazo = isset($param["-r"]) ? ($param["-r"] == 1) : false;
		
		$proyecto =$this->get_proyecto();
		$servicios = toba_info_editores::get_items_servicios_web();
		$carpeta_doc = $proyecto->get_dir()."/doc/servicios_web";
		if (! file_exists($carpeta_doc)) {
			mkdir($carpeta_doc, 0777, true);
		}
		$archivo = $carpeta_doc. '/index.html';		
		if (isset($param['--nombre-archivo'])) {															//Veo si se especifico un archivo de salida
			$archivo = $carpeta_doc . '/'.$param['--nombre-archivo'];
		}
		
		$this->consola->mensaje("Generando documentacion...");		
		$index_page = toba_modelo_soap::get_encabezado_doc($proyecto);									//Genero el encabezado de la pagina
		foreach ($servicios as $serv_datos) {
			$servicio = toba_modelo_catalogo::get_servicio_web($proyecto, $serv_datos['item'], $this->consola);			//Ciclo generando la doc de cada servicio
			$file = $servicio->generar_documentacion($carpeta_doc, $forzar_reemplazo);
			$index_page .= "<li><a href='$file'>{$serv_datos['item']}</a></li>";
			unset($servicio);
		}
		$index_page .= toba_modelo_soap::get_pie_doc();
		file_put_contents($archivo, $index_page);
		$this->consola->mensaje("Listo. Navegar hacia file://$archivo");
	}
	
	//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
	private function cliente_soap_configurar($parametros)
	{
		$proyecto = $this->get_proyecto();
		$id_servicio = (isset($parametros['-s'])) ? $parametros['-s'] : $this->get_servicio_cli();
		$servicio = toba_modelo_catalogo::get_servicio_web($proyecto, $id_servicio, $this->consola);
		
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
	
	private function cliente_rest_configurar($parametros)
	{
		$url_sistema = $cert_cli = $key_cli = $cert_pwd = $usr = $usr_pwd = null;
		$proyecto = $this->get_proyecto();
		$id_servicio = (isset($parametros['-s'])) ? $parametros['-s'] : $this->get_servicio_cli();		
		$servicio = toba_modelo_catalogo::get_servicio_web($proyecto, $id_servicio, $this->consola, 'rest');		
		
		//Me fijo si se puso un usuario y pwd
		if (isset($parametros['--usuario']) && trim($parametros['--usuario']) != '') {
			if (! isset($parametros['--usuario_pwd']) || trim($parametros['--usuario_pwd']) == '') {
				$this->consola->error('Si especifica un usuario, debe especificar el password para el mismo');
				die;
			}
			$usr = $parametros['--usuario'];
			$usr_pwd = $parametros['--usuario_pwd'];
		}
		//Recupero los posibles archivos de certificados que se hayan incluido
		$leyenda = 'El archivo para el parametro $param no existe o no es accesible';
		$cert_cli = $this->recuperar_archivo_parametro($parametros, '--cert_cli', $leyenda);
		$key_cli = $this->recuperar_archivo_parametro($parametros, '--key_cli', $leyenda);
		$cert_CA = $this->recuperar_archivo_parametro($parametros, '-c', $leyenda);
		if (isset($cert_cli) && ! isset($key_cli)) {
			$this->consola->error('Falta especificar la clave privada del certificado cliente');
			die;
		}
		//Me fijo si existe un password para el certificado del cliente
		if (isset($parametros['--cert_pwd']) && trim($parametros['--cert_pwd']) != '') {
			$cert_pwd = $parametros['--cert_pwd'];
		}
		if (isset($parametros['-u']) && trim($parametros['-u']) != '') {
			$url_sistema = $parametros['-u'];
		}
		//Genero la config para el servicio especificado (analizar si se pueden configurar varios distintos)			
		$servicio->generar_configuracion_cliente($cert_CA, $url_sistema, $cert_cli, $key_cli, $cert_pwd,$usr, $usr_pwd);
	}
	
	//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
	private function server_soap_configurar($parametros)
	{
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
	
	private function server_rest_configurar($parametros)
	{
		$usr = $usr_pwd = null;		
		$proyecto= $this->get_proyecto();
		$auth_tipo = (isset($parametros['--auth_tipo'])) ? $parametros['--auth_tipo'] : 'digest';							//Verifico el tipo de autenticacion si es que se selecciono alguno

		//Busco usuario y pwd que se hayan pasado y comparo contra el tipo de autenticacion
		if (isset($parametros['--usuario']) && trim($parametros['--usuario']) != '') {
			if (! isset($parametros['--usuario_pwd']) || trim($parametros['--usuario_pwd']) == '') {
				$this->consola->error('Si especifica un usuario, debe especificar el password para el mismo');
				die;
			}
			$usr = $parametros['--usuario'];
			$usr_pwd = $parametros['--usuario_pwd'];
			if ($auth_tipo == 'ssl') {
				$this->consola->error('El tipo de autenticacion solicitado requiere un certificado digital');
				die;				
			}
		}
		//Intento recuperar el archivo del certificado del cliente
		$leyenda = 'El archivo para el parametro $param no existe o no es accesible';
		$cert_cli = $this->recuperar_archivo_parametro($parametros, '-c', $leyenda);
		if (isset($cert_cli) && $auth_tipo != 'ssl') {
			$this->consola->error('El tipo de autenticación elegido require usuario y password, no un certificado');
			die;
		}
		
		$encoding = (isset($parametros['--encoding'])) ? $parametros['--encoding'] :  'utf-8';
		$rest = new toba_modelo_rest($proyecto);
		$rest->generar_configuracion_servidor($usr, $usr_pwd, $cert_cli, $auth_tipo, $encoding);
	}	
	
	//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//	
	protected function recuperar_archivo_parametro($parametros, $param, $leyenda)
	{
		if (isset($parametros[$param]) && !file_exists($parametros[$param])) {
			$this->consola->error($leyenda);
			die;	
		}
		return isset($parametros[$param]) ? $parametros[$param]: null;
	}	
	
	protected function get_servicio_serv() 
	{
		$proyecto = $this->get_proyecto();
		$parametros = $this->get_parametros();
		$tipo = (isset($parametros['--tipo_ws'])) ? $parametros['--tipo_ws'] : null;
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
		$tipo = (isset($parametros['--tipo_ws'])) ? $parametros['--tipo_ws'] : null;
		$servicios_disponibles = toba_info_editores::get_servicios_web_acc($proyecto->get_id(), $tipo);		
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
	
}
?>