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

	/**
	 * Genera la documentación de los servicios web disponibles en el proyecto (requiere tener publicado el proyecto en localhost)
	 */
	function opcion__generar_doc()
	{
		$prefijo = "http://localhost";
		$sufijo = "/servicios.php/";
		$proyecto =$this->get_proyecto();
		$servicios = toba_info_editores::get_items_servicios_web();
		$carpeta_doc = $proyecto->get_dir()."/doc/servicios_web";
		if (! file_exists($carpeta_doc)) {
			mkdir($carpeta_doc, 0777, true);
		}
		$this->consola->mensaje("Generando documentacion...");		
		copy(toba_dir(). '/php/modelo/var/wsdl-viewer.xsl', $carpeta_doc.'/wsdl-viewer.xsl');
		copy(toba_dir(). '/php/modelo/var/wsdl-viewer.css', $carpeta_doc.'/wsdl-viewer.css');
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
	function opcion__certificados_ssl()
	{
		//Pido ruta del archivo de certificados y de la clave
		$servidor = $this->obtener_ruta_archivos_certificados_ssl('Servidor');
		$cliente = $this->obtener_ruta_archivos_certificados_ssl('Cliente');
		
		//Acomodo los datos
		$datos = array();
		$datos['cert_server'] = $servidor['cert'];
		$datos['clave_server'] = $servidor['key'];
		$datos['cert_cliente'] = $cliente['cert'];
		$datos['clave_cliente'] = $cliente['key'];		
		
		//Agrego esa info al archivo instalacion.ini
		$modelo = new toba_modelo_instalacion();
		$modelo->agregar_info_certificado_ssl($datos);
		
		//Todo: Hace falta copiar los certificados en instalacion/servicios/certificados al menos los del cliente? 
		//Por ahora esta quedando en instalacion.ini la ruta
		
		//Creo el directorio donde se colocaran los archivos
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
			throw new toba_error('No existe informacion disponible de certificados SSL, utilice el comando toba servicios_web certificado_servidor');
		}
			
		//Elijo el servicio web sobre el que trabajare
		$servicios_lista = array();		
		$servicios_disponibles = toba_info_editores::get_items_servicios_web($proyecto->get_id());		
		foreach($servicios_disponibles as $serv) {
			$servicios_lista[$serv['item']] = $serv['nombre'];
		}		
		$servicio = $this->consola->dialogo_lista_opciones($servicios_lista, 'Seleccione el servicio web');
				
		//Creo el directorio para el servicio web
		$punto_partida = $instalacion->get_dir(). '/servicios';						
		$dir_servicio = $punto_partida . '/'. $servicio;
		toba_manejador_archivos::crear_arbol_directorios($dir_servicio, 0755);
		
		//Aca hago el ciclo para pedir los datos
		$datos = array();
		$seguir = true;
		$this->consola->mensaje('Parametros a enviar por defecto', true);
		while ($seguir)  {
			$id = $this->consola->dialogo_ingresar_texto('Identificador del dato');
			$valor = $this->consola->dialogo_ingresar_texto('Valor para el dato');
			$datos[$id] = $valor;
			
			$rta = $this->consola->dialogo_ingresar_texto('Desea seguir? [s/n]');
			$seguir = ($rta == 's');
		}
		
		//Aca genero el archivo headers.ini en el directorio del servicio
		$archivo_headers = $dir_servicio . '/headers.ini';		
		$in_headers = new toba_ini($archivo_headers);
		$in_headers->agregar_titulo('Este archivo contiene los headers por defecto para el servicio '. $servicio);
		$in_headers->agregar_entrada('headers', $datos);
		$in_headers->guardar();
					
		//Aca copio el certificado del servidor
		$nombre = basename($archivos['cert_server']);				//Certificado X509
		copy($archivos['cert_server'], $dir_servicio . '/'. $nombre);
		
		//Aca copio los archivos de certificado para el cliente
		$nombre_cert = basename($archivos['cert_cliente']);				
		copy($archivos['cert_cliente'], $dir_servicio . '/'. $nombre_cert);			
		$nombre_clave = basename($archivos['clave_cliente']);				
		copy($archivos['clave_cliente'], $dir_servicio . '/'. $nombre_clave);		
		
		//Aca genero el par de claves
		$key_pair = $this->generar_par_claves($dir_servicio, $servicio);
		
		//Copio la clave publica al directorio de claves del servidor		
		$destino = $instalacion->get_dir(). '/servicios/rsa/'.$key_pair['nombre'].'.public';
		copy($key_pair['publica'], $destino);
		
		$datos_cert = array('cert_cliente' => $nombre_cert, 'clave_cliente' => $nombre_clave, 'cert_server' => $nombre);
		$datos_rsa = array('privada' => $key_pair['nombre']. '.pkey',  'publica' => $key_pair['nombre']. '.public');
		$this->generar_archivo_configuracion($datos_cert,$datos_rsa);
		
		
		//Aca guardo las claves en la base de datos
		$this->asociar_claves_servicio($proyecto, $servicio, $datos, $destino);			
		$dir_actual = getcwd();	
		
		//Aca zipeo todo y armo el paquete
		$nombre_archivo  = "$servicio.zip";
		$comando = "cd $punto_partida ; zip -1 -m -r --out $nombre_archivo  $servicio";
		if ( toba_manejador_archivos::ejecutar($comando, $stdout, $stderr) != 0) {			
			toba_logger::instancia()->debug("Error al armar el zip: $stderr");
			throw new toba_error ('No se pudo armar el paquete especificado');
		}
		
		//Dejo el archivo zip en el directorio donde se ejecuta el comando
		rename("$punto_partida/$nombre_archivo", "$dir_actual/$nombre_archivo");
		
		//Aca informo donde esta el archivo
		$this->consola->mensaje("El archivo del paquete es $nombre_archivo ");
	}
		
	function opcion__agregar_consumo()
	{
		//Pa arrancar pido el proyecto
		$proyecto = $this->get_proyecto();		
		$servicio = $this->consola->dialogo_ingresar_texto('ID Servicio web');
		
		//Obtengo en que url se supone que esta el web_service
		$url = $this->consola->dialogo_ingresar_texto('Ingrese URL de acceso al WS');
		
		$aux = $this->consola->dialogo_ingresar_texto('Usa WSA? (s/n)');
		$usa_addressing = (strtolower($aux) == 's') ? 1: 0;
				
		$datos = array('to' => $url, 'wsa' => $usa_addressing);			
		
		//El nombre del registro deberia incluir al proyecto para evitar clashing?
		$ini_conf = new toba_ini($instalacion->get_dir() . '/servicios_web.ini');
		if ($ini_conf->existe_entrada($servicio)) {
			$ini_conf->set_datos_entrada($servicio, $datos);
		} else {
			$ini_conf->agregar_entrada($servicio, $datos);
		}	
		$ini_conf->guardar();		
		
		
		//TODO:Hay que guardar esto en la BD
	}
	
	
	function opcion__configurar()
	{
		//Pa arrancar pido el proyecto
		$proyecto = $this->get_proyecto();		
		$servicio = $this->consola->dialogo_ingresar_texto('Servicio web a configurar');
		
		//Busco los archivos de certificados del servidor (son necesarios para la comunicacion, fallo rapido)
		$instalacion = new toba_modelo_instalacion();
		$dir_arranque = $instalacion->get_dir() . '/servicios';
				
		$this->consola->mensaje('Ingrese la ruta al paquete que contiene la informacion', true);
		do {
			$archivo = $this->consola->dialogo_ingresar_texto('Ruta');
			$error = (!file_exists($archivo));			
			if ($error) {
				$this->consola->mensaje('El archivo no se encuentra en la ruta especificada', true);
			}
		} while ($error);
		
		$comando = "unzip -o  $archivo  -d $dir_arranque";
		if ( toba_manejador_archivos::ejecutar($comando, $stdout, $stderr) != 0) {			
			toba_logger::instancia()->debug("Error al descomprimir el zip: $stderr");
			throw new toba_error ('No se pudo recuperar el paquete especificado');
		}
		
		//Tengo que renombrar el directorio a lo que sea que eligio el usuario como nombre del servicio
		rename(basename($archivo, '.zip'), $servicio);				
				
		$this->consola->mensaje('Se importo correctamente el paquete de configuracion', true);
	}
	
	//----------------------------------------------------------------------------------------------------------------------------------------------------
	//							METODOS AUXILIARES
	//----------------------------------------------------------------------------------------------------------------------------------------------------
	protected function obtener_ruta_archivos_certificados_ssl($titulo)
	{
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
						
			//Aca verifico que se trate de certificados SSL y no de cualquier verdura
			toba_logger::instancia()->debug("Ruta al certificado: {$datos['cert']}");
			toba_logger::instancia()->debug("Ruta a la clave: {$datos['key']}");
			$sirve = openssl_x509_checkpurpose($datos['cert'], X509_PURPOSE_ANY);
			$se_corresponde = openssl_x509_check_private_key($datos['cert'], $datos['key']);	
			if ($sirve !== true) {
				$this->consola->error("El archivo no contiene un certificado válido ($sirve)");
				$this->consola->enter();
			} elseif ($se_corresponde !== true) {
				$this->consola->error("La clave especificada no concuerda con el certificado ($se_corresponde)");
				$this->consola->enter();			
			}
			$error = (!$sirve || !$se_corresponde);
		} while ($error);		
	
		return $datos;
	}
		
	protected function generar_par_claves($directorio, $servicio)
	{
		$configargs = array('private_key_bits' => 4096, 'encrypt_key' => false, 'digest_alg' => 'sha1');
		$kp = openssl_pkey_new($configargs);
		
		$nombre =  uniqid('id_rsa');
		$archivo_pkey = $directorio . "/$nombre.pkey"; 
		if (!openssl_pkey_export_to_file($kp, $archivo_pkey)) {
			throw new toba_error('No se pudo exportar la clave privada para el firmado');
		}
		
		$archivo_public = $directorio . "/$nombre.public";		
		$keyData = openssl_pkey_get_details($kp);		
		$result = file_put_contents($archivo_public, $keyData['key']);
		if (!$keyData || !$result) {
			throw new toba_error('No se pudo exportar la clave publica para el firmado');
		}
		
		//Creo el archivo de configuracion que contendra las claves

		
		return array('nombre' => $nombre, 'privada' => $archivo_pkey,  'publica' => $archivo_public);
	}
	
	protected function asociar_claves_servicio($proyecto, $servicio, $headers, $destino)
	{
		//Primero hago un hash sobre todos los headers
		$str_headers = implode('', $headers);
		$id = hash('sha512', $str_headers);

		$db = $proyecto->get_instancia()->get_db();				
		
		//Invalido cualquier clave anterior que pueda tener dicho hash para el servicio
		$proyecto_id = $db->quote($proyecto->get_id());
		$servicio =  $db->quote($servicio);
		$id =  $db->quote($id);
		$ruta_pubk =  $db->quote($destino);
			
		try {
			$db->abrir_transaccion();
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
	
	protected function generar_archivo_configuracion($datos_cert, $datos_rsa)
	{
		$firmado = (! empty($datos_rsa)) ? 1: 0;
		$config = new toba_ini($directorio . '/servicio.ini');
		$config->agregar_titulo('Este archivo contiene la ruta de los archivos que se usan para firmar con RSA los mensajes');
		$config->agregar_entrada('RSA', $datos_rsa);
		$config->agregar_entrada('cliente_certificado', $datos_cert);
		$config->agregar_entrada('firmado' , $firmado);
		$config->guardar();		
	}
}
?>