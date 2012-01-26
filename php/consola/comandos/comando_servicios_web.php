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
		$this->consola->mensaje("INVOCACION: toba servicio_web OPCION");
		$this->consola->enter();
	}

	//--------------------------------------------------------------------------------------------------------------------------------------//
	//								SERVER										   //
	//--------------------------------------------------------------------------------------------------------------------------------------//	
	/**
	 * Genera la documentación de los servicios web provistos por el proyecto (requiere tener publicado el proyecto en localhost)
	 */
	function opcion__generar_doc()
	{
		$param = $this->get_parametros();
		$forzar_reemplazo = isset($param["-r"]) ? ($param["-r"] == 1) : false;
				
		$prefijo = "http://localhost";
		$sufijo = "/servicios.php/";
		$proyecto =$this->get_proyecto();
		$servicios = toba_info_editores::get_items_servicios_web();
		$carpeta_doc = $proyecto->get_dir()."/doc/servicios_web";
		if (! file_exists($carpeta_doc)) {
			mkdir($carpeta_doc, 0777, true);
		}
		$this->consola->mensaje("Generando documentacion...");		
		if ($forzar_reemplazo || !file_exists($carpeta_doc.'/wsdl-viewer.xsl')) {
			copy(toba_dir(). '/php/modelo/var/wsdl-viewer.xsl', $carpeta_doc.'/wsdl-viewer.xsl');
		}
		if ($forzar_reemplazo || !file_exists($carpeta_doc.'/wsdl-viewer.css')) {		
			copy(toba_dir(). '/php/modelo/var/wsdl-viewer.css', $carpeta_doc.'/wsdl-viewer.css');
		}
		$include = '<?xml-stylesheet type="text/xsl" href="wsdl-viewer.xsl"?>';
		$search = '"utf-8"?>';
		$index_page = "<html><head>
		<link href='wsdl-viewer.css' rel='stylesheet' type='text/css' media='screen'/>
		</head>
		<body>
		<div id='header'>
		<h1>{$proyecto->get_id()}</h1><h2>Documentación Servicios Web</h2>
		</div>
		<div id='inner_box'><div class='page'>
		<ul>";
		foreach ($servicios as $servicio) {
			$this->consola->mensaje("Servicio: ".$servicio['item']);			
			$url = $prefijo.$proyecto->get_url().$sufijo.$servicio['item']."?wsdl2";
			$wsdl = file_get_contents($url);
			$wsdl = str_replace($search, $search.$include, $wsdl);
			$file = $servicio['item'].".wsdl.xml";
			file_put_contents($carpeta_doc."/".$file, $wsdl);
			$index_page .= "<li><a href='$file'>{$servicio['item']}</a></li>";			
		}
		$index_page .= "</ul></div></div></body></html>";
		file_put_contents($carpeta_doc."/index.html", $index_page);
		$this->consola->mensaje("Listo. Navegar hacia file://".$carpeta_doc."/index.html");		

	}
	
	/**
	 *  Agrega la ruta de los archivos .cert / .pem que seran usados para brindar servicios web seguros
	 */
	function opcion__conf_ssl_server()
	{		
		//Pido el directorio donde se encuentra la Certificate Authority
		$ca_dir = $this->consola->dialogo_ingresar_texto('Directorio donde se encuentra la CA');
		
		//Pido ruta del archivo de certificados y de la clave		
		$servidor = $this->obtener_ruta_archivos_certificados_ssl('Servidor', $ca_dir);
		$cliente = $this->obtener_ruta_archivos_certificados_ssl('Cliente', $ca_dir);
		
		//Acomodo los datos
		$datos = array();
		$datos['ca_dir'] = $ca_dir;
		$datos['cert_server'] = $servidor['cert'];
		$datos['clave_server'] = $servidor['key'];
		$datos['cert_cliente'] = $cliente['cert'];
		$datos['clave_cliente'] = $cliente['key'];		
		
		//Agrego esa info al archivo instalacion.ini
		$modelo = new toba_modelo_instalacion();
		$modelo->agregar_info_certificado_ssl($datos);
		
		//Todo: Hace falta copiar los certificados en instalacion/servicios/certificados al menos los del cliente? 
		//Por ahora esta quedando en instalacion.ini la ruta
		
		//Creo el directorio donde se colocaran los archivos para las claves RSA
		$dir = $modelo->get_dir() . '/servicios/rsa';
		toba_manejador_archivos::crear_arbol_directorios($dir, '755');
	}
	
	/**
	 * Genera un paquete firmado de headers que se intercambiaran durante el pedido de servicio web
	 */
	function opcion__paquete_cliente()
	{
		//Pa arrancar pido el proyecto
		$proyecto = $this->get_proyecto();
		
		//Busco los archivos de certificados del servidor (son necesarios para la comunicacion, fallo rapido)
		$instalacion = new toba_modelo_instalacion();
		$archivos = $instalacion->get_archivos_certificado();
		if (is_null($archivos)) {
			throw new toba_error('No existe informacion disponible de certificados SSL, utilice el comando toba servicios_web conf_ssl_server');
		}
			
		//Elijo el servicio web sobre el que trabajare
		$servicios_lista = array();		
		$servicios_disponibles = toba_info_editores::get_items_servicios_web($proyecto->get_id());		
		foreach($servicios_disponibles as $serv) {
			$servicios_lista[$serv['item']] = $serv['nombre'];
		}		
		$servicio = $this->consola->dialogo_lista_opciones($servicios_lista, 'Seleccione el servicio web');
				
		//Creo el directorio para el servicio web
		$punto_partida =  $proyecto->get_dir_instalacion_proyecto(). '/servicios';						
		$dir_servicio = $punto_partida . '/'. $servicio;
		toba_manejador_archivos::crear_arbol_directorios($dir_servicio, 0755);
		
		//Aca hago el ciclo para pedir los datos
		$datos = array();
		$pregunta = $this->consola->dialogo_ingresar_texto('Existen parámetros a enviar?[s/n]');
		$seguir = $hay_parametros =  (strtolower($pregunta) == 's');
		//$this->consola->mensaje('Parametros a enviar por defecto', true);
		while ($seguir)  {
			$id = $this->consola->dialogo_ingresar_texto('Identificador del dato');
			$valor = $this->consola->dialogo_ingresar_texto('Valor para el dato');
			$datos[$id] = $valor;
			
			$rta = $this->consola->dialogo_ingresar_texto('Desea seguir? [s/n]');
			$seguir = ($rta == 's');
		}
		
		//Aca genero el archivo headers.ini en el directorio del servicio
		if ($hay_parametros) {
			$archivo_headers = $dir_servicio . '/headers.ini';		
			$in_headers = new toba_ini($archivo_headers);
			$in_headers->agregar_titulo('Este archivo contiene los headers por defecto para el servicio '. $servicio);
			$in_headers->agregar_entrada('headers', $datos);
			$in_headers->guardar();
		}
		//Aca copio el certificado del servidor
		$nombre = basename($archivos['cert_server']);				//Certificado X509 servidor
		copy($archivos['cert_server'], $dir_servicio . '/server.cert');
		
		//Aca copio los archivos de certificado para el cliente
		copy($archivos['cert_cliente'], $dir_servicio . '/cliente.cert');
		copy($archivos['clave_cliente'], $dir_servicio . '/cliente.key');
		
		//Aca genero el par de claves RSA		
		$datos_rsa = array();
		if ($hay_parametros) {
			$key_pair = $this->generar_par_claves($dir_servicio, $servicio);
		
			//Copio la clave publica al directorio de claves del servidor		
			$destino = $instalacion->get_dir(). '/servicios/rsa/'.$key_pair['nombre'].'.public';
			copy($key_pair['publica'], $destino);
			$datos_rsa = array('privada' => $key_pair['nombre']. '.pkey',  'publica' => $key_pair['nombre']. '.public');			
		}
		
		$datos_cert = array('cert_cliente' => 'cliente.cert', 'clave_cliente' => 'cliente.key', 'cert_server' => 'server.cert');
		$this->generar_archivo_configuracion($datos_cert,$datos_rsa, $dir_servicio);		
		
		//Aca guardo las claves en la base de datos
		if ($hay_parametros) {
			$this->asociar_claves_servicio($proyecto, $servicio, $datos, $destino);			
		}
		$dir_actual = getcwd();	
		
		//Aca zipeo todo y armo el paquete
		$nombre_archivo  = "$servicio.zip";
		$comando = "cd $punto_partida ; zip -1 -m -r $nombre_archivo  $servicio";
		if ( toba_manejador_archivos::ejecutar($comando, $stdout, $stderr) != 0) {			
			toba_logger::instancia()->debug("Error al armar el zip: $comando \n $stderr");
			throw new toba_error ('No se pudo armar el paquete especificado');
		}
		
		//Dejo el archivo zip en el directorio donde se ejecuta el comando
		rename("$punto_partida/$nombre_archivo", "$dir_actual/$nombre_archivo");
		
		//Aca informo donde esta el archivo
		$this->consola->mensaje("El archivo del paquete es $nombre_archivo ");
	}
	
	//--------------------------------------------------------------------------------------------------------------------------------------//
	//								CLIENTE										   //
	//--------------------------------------------------------------------------------------------------------------------------------------//		
	/**
	 *  Agrega información sobre un servicio web consumido
	 */
	function opcion__agregar_consumo()
	{
		//Pa arrancar pido el proyecto
		$proyecto = $this->get_proyecto();		
		$servicio = $this->consola->dialogo_ingresar_texto('ID Servicio web');		
		$descripcion = $this->consola->dialogo_ingresar_texto('Ingrese una descripción significativa');		
		
		//Obtengo en que url se supone que esta el web_service
		$url = $this->consola->dialogo_ingresar_texto('Ingrese URL de acceso al WS');		
		$aux = $this->consola->dialogo_ingresar_texto('Usa WSA? [s/n]');
		$usa_addressing = (strtolower($aux) == 's') ? 1: 0;

		//Pido los parametros basicos  del servicio web, esto no son los headers
		$parametros = array();		
		$preg1 = $this->consola->dialogo_ingresar_texto('El servicio tiene parámetros?');
		if (strtolower($preg1) == 's') {
			do {
				$form = $this->consola->get_formulario("Valores para los parámetros:");
				$form->agregar_campo(array('id'=>'parametro', 'nombre' => 'Nombre Parámetro'));
				$form->agregar_campo(array('id'=>'valor', 'nombre' => 'Valor'));
				$datos = $form->procesar();

				$pregunta = $this->consola->dialogo_ingresar_texto('Desea Ingresar otro par?[s/n]');
				$quiero_seguir = (strtolower($pregunta) == 's');
				$parametros[] = $datos;
			} while ($quiero_seguir);
		}

		$this->guardar_info_servicio_web($proyecto, $servicio, $parametros, $descripcion, $url, $usa_addressing);		
	}	
	
	/**
	 *  Configura un consumo existente con el paquete recibido del proveedor del servicio
	 */
	function opcion__configurar_consumo()
	{
		//Pa arrancar pido el proyecto y servicio a configurar
		$proyecto = $this->get_proyecto();		
		
		$servicios_lista = array();		
		$servicios_disponibles = toba_info_editores::get_servicios_web_acc($proyecto->get_id());		
		foreach($servicios_disponibles as $serv) {
			$servicios_lista[$serv['servicio_web']] = $serv['servicio_web'];
		}		
		$servicio = $this->consola->dialogo_lista_opciones($servicios_lista, 'Seleccione el servicio web');		
		$dir_arranque = $proyecto->get_dir_instalacion_proyecto() . '/servicios';
				
		//Selecciono el paquete enviado desde el proveedor del servicio
		$this->consola->mensaje('Ingrese la ruta al paquete que contiene la informacion', true);
		do {
			$archivo = $this->consola->dialogo_ingresar_texto('Ruta');
			$error = (!file_exists($archivo));			
			if ($error) {
				$this->consola->mensaje('El archivo no se encuentra en la ruta especificada', true);
			}
		} while ($error);

		//Descomprimo el archivo en el directorio
		$comando = "unzip -o  $archivo  -d $dir_arranque";
		if ( toba_manejador_archivos::ejecutar($comando, $stdout, $stderr) != 0) {			
			toba_logger::instancia()->debug("Error al descomprimir el zip: $stderr");
			throw new toba_error ('No se pudo recuperar el paquete especificado');
		}
		
		//Tengo que renombrar el directorio a lo que sea que eligio el usuario como nombre del servicio
		chdir($dir_arranque);
		rename(basename($archivo, '.zip'), $servicio);					
		$this->consola->mensaje('Se importo correctamente el paquete de configuracion', true);
	}
	
	//----------------------------------------------------------------------------------------------------------------------------------------------------
	//							METODOS AUXILIARES
	//----------------------------------------------------------------------------------------------------------------------------------------------------
	/**
	 * Pido la ruta de los archivos
	 * @param string $titulo
	 * @param string $ca_dir
	 * @return array
	 * @ignore 
	 */
	protected function obtener_ruta_archivos_certificados_ssl($titulo, $ca_dir = '.')
	{
		//Pido la ubicacion de los archivos
		do {
			$form = $this->consola->get_formulario("Ubicación de archivos SSL para $titulo:");
			$form->agregar_campo(array('id'=>'cert', 'nombre' => 'Archivo Certificado'));
			$form->agregar_campo(array('id'=>'key', 'nombre' => 'Archivo Clave Privada'));
			$datos = $form->procesar();

			$error = (!file_exists($datos['cert']) || !file_exists($datos['key']));
			if ($error) {
				$this->consola->mensaje("Una de las rutas especificadas es incorrecta o el archivo es inaccesible.");
				$this->consola->enter();
			}
						
			//Aca verifico que se trate de certificados SSL y no de cualquier verdura (no uso las primitivas
			toba_logger::instancia()->debug("Ruta al certificado: {$datos['cert']}");
			toba_logger::instancia()->debug("Ruta a la clave: {$datos['key']}");
			
			//Verifico que el certificado sea apto para cualquier proposito
			//$sirve = openssl_x509_checkpurpose($datos['cert'], X509_PURPOSE_ANY);
			$cmd = ' openssl verify -CApath '. $ca_dir . ' -purpose any  '. $datos['cert'];
			toba_logger::instancia()->debug("Comando validacion: $cmd \n");
			$exito = toba_manejador_archivos::ejecutar($cmd, $stdout, $stderr);	
			if ($exito != '0' ||  $stderr != '') {
				toba_logger::instancia()->debug("Error: $stderr");
				$this->consola->error("El archivo no contiene un certificado válido ($sirve)");
				$this->consola->enter();
				$error = true;
			}
			
			//Verifico que certificado y clave se corresponden
			//$se_corresponde = openssl_x509_check_private_key($datos['cert'], $datos['key']);	
			$cmd = "(openssl x509 -noout -modulus -in {$datos['cert']} | openssl md5 ; openssl rsa -noout -modulus -in {$datos['key']} | openssl md5) | uniq;";
			toba_logger::instancia()->debug("Comando validacion: $cmd \n");
			$exito = toba_manejador_archivos::ejecutar($cmd, $stdout, $stderr);
			$resultado = explode('(stdin)',$stdout);		//Si devuelve mas de una entrada entonces no coinciden.
			if ($exito != '0' || $stderr != '' || count($resultado) > 2) {
				$this->consola->error("La clave especificada no concuerda con el certificado");
				$this->consola->enter();			
				$error = true;
			}
		} while ($error);		
	
		return $datos;
	}
		
	/**
	 * Genera un par de claves RSA de 1024 bits
	 * @param string $directorio
	 * @param string $servicio
	 * @return array
	 * @ignore
	 */
	protected function generar_par_claves($directorio, $servicio)
	{
		//Genero claves con 1024 para que la soporten los browsers mas viejos
		$configargs = array('private_key_bits' => 1024, 'encrypt_key' => false, 'digest_alg' => 'sha1');
		$kp = openssl_pkey_new($configargs);
		
		//Exporto la clave privada
		$nombre =  uniqid('id_rsa');
		$archivo_pkey = $directorio . "/$nombre.pkey"; 
		if (!openssl_pkey_export_to_file($kp, $archivo_pkey)) {
			throw new toba_error('No se pudo exportar la clave privada para el firmado');
		}
		
		//Ahora obtengo la clave publica y la saco a un archivo aparte
		$archivo_public = $directorio . "/$nombre.public";		
		$keyData = openssl_pkey_get_details($kp);		
		$result = file_put_contents($archivo_public, $keyData['key']);
		if (!$keyData || !$result) {
			throw new toba_error('No se pudo exportar la clave publica para el firmado');
		}
		
		return array('nombre' => $nombre, 'privada' => $archivo_pkey,  'publica' => $archivo_public);
	}
	
	/**
	 * Asocia el proyecto y servicio con el nombre del archivo que contiene la clave publica
	 * @param toba_modelo_proyecto $proyecto
	 * @param string $servicio
	 * @param array $headers
	 * @param string $destino 
	 */
	protected function asociar_claves_servicio($proyecto, $servicio, $headers, $destino)
	{
		//Primero hago un hash sobre todos los headers
		$str_headers = implode('', $headers);
		$id = hash('sha512', $str_headers);

		$db = $proyecto->get_instancia()->get_db();						
		$proyecto_id = $db->quote($proyecto->get_id());
		$servicio =  $db->quote($servicio);
		$id =  $db->quote($id);
		$ruta_pubk =  $db->quote($destino);
			
		try {
			$db->abrir_transaccion();
			//Invalido cualquier clave anterior que pueda tener dicho hash para el servicio
			$sql = "UPDATE apex_mapeo_rsa_kp SET anulada = 1 WHERE proyecto = $proyecto_id AND  servicio_web = $servicio AND id = $id;";
			$db->ejecutar($sql);

			//Agrego el mapeo nuevo
			$sql = "INSERT INTO apex_mapeo_rsa_kp (proyecto, servicio_web, id, pub_key) VALUES ($proyecto_id, $servicio, $id, $ruta_pubk);";
			$db->ejecutar($sql);
			
			$db->cerrar_transaccion();
		} catch(toba_error_db $e) {
			$db->abortar_transaccion();			
			throw new toba_error('No se pudo guardar la clave');
		}		
	}
	
	/**
	 * Graba el archivo de configuracion del servicio dentro del directorio que luego sera enviado al cliente.
	 * @param array $datos_cert
	 * @param array $datos_rsa
	 * @param string $directorio 
	 */
	protected function generar_archivo_configuracion($datos_cert, $datos_rsa, $directorio)
	{
		$firmado = (! empty($datos_rsa)) ? 1: 0;
		$config = new toba_ini($directorio . '/servicio.ini');
		$config->agregar_titulo('Este archivo contiene la ruta de los archivos que se usan para firmar con RSA los mensajes');
		$config->agregar_entrada('cliente_certificado', $datos_cert);
		$config->agregar_entrada('firmado' , $firmado);
		if ($firmado == 1) {
			$config->agregar_entrada('RSA', $datos_rsa);
		}		
		$config->guardar();		
	}
	
	/**
	 * Agrega un servicio web y sus parametros en el cliente.
	 * @param toba_modelo_proyecto $proyecto
	 * @param string $servicio_web
	 * @param array $parametros
	 * @param string $descripcion
	 * @param string $url
	 * @param integer $wsa 
	 */
	protected function guardar_info_servicio_web($proyecto, $servicio_web, $parametros, $descripcion = null, $url = null, $wsa = 0)
	{
		$db = $proyecto->get_instancia()->get_db();
		$proyecto_id = $db->quote($proyecto->get_id());
		$servicio_web = $db->quote($servicio_web);
		$desc = $db->quote($descripcion);
		$to = $db->quote($url);
		$wsa = $db->quote($wsa);				
		
		$sql[] = "INSERT INTO apex_servicio_web (proyecto, servicio_web, descripcion, param_to, param_wsa) VALUES ($proyecto_id, $servicio_web, $desc, $to, $wsa);";		
		foreach($parametros as $param) {
			$valores = $db->quote($param);
			$sql[] = "INSERT INTO apex_servicio_web_param (proyecto, servicio_web, parametro, valor) VALUES ($proyecto_id, $servicio_web, {$valores['parametro']}, {$valores['valor']});";
		}
		
		try {
			$db->abrir_transaccion();
			$db->ejecutar($sql);
			$db->cerrar_transaccion();
		} catch (toba_error_db $e) {
			$db->abortar_transaccion();
			toba_logger::instancia()->debug("WS: ". $e->getMessage());
			throw new toba_error ('Se produjo un error al intentar guardar la configuración, consulte el log');
		}		
	}
}
?>