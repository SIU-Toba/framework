<?php
	
//use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;

/**
 * Genera un pdf a través de una api básica
 * @package SalidaGrafica
 */
class toba_vista_jasperreports
{
	//protected $path_reporte;
	protected $conexion;
//	protected $path_plantilla = '';
	
	protected $nombre_archivo = 'archivo.pdf';
	protected $tipo_descarga = 'attachment';
	//protected $jasper;
	protected $temp_salida;

	// Parametros para el reporte
	protected $parametros;
	protected $xml_path;
	protected $xpath_data;
	protected $modo_archivo = false;
	protected $limpiar_modo_archivo = false;
	
	private $lista_jrprint = array();
	protected $url; 
	protected $url_base;
	protected $usr;
	protected $pwd;
	
	protected $uri;
	protected $extension = 'pdf';
	protected $id_reporte;
	
	function __construct()
	{
		$this->temp_salida = toba::proyecto()->get_path_temp().'/'.uniqid('jasper_');
		
		$this->read_ini_config();
		$this->crear_cliente();		
		
		/*Creamos una variable tipo arreglo que contendrá los parámetros */
		$this->parametros = [];
	}
	
	/**
	 * @ignore 
	 */
	function asignar_objetos( $objetos )
	{
		$this->objetos = $objetos;
	}
	
	protected function read_ini_config()
	{
		//$this->url_base = 'http://localhost:8180/jasperserver';
		$this->usr = 'jasperadmin';
		$this->pwd = 'jasperadmin';

		$this->url_base = 'http://localhost';
		$this->url =  '/report/rest/reportes/'. toba::proyecto()->get_id();;		
	}
	
	protected function crear_cliente()
	{
		$opciones = array('to' => $this->url_base, 'auth_usuario' => $this->usr, 'auth_password' => $this->pwd, 'auth_tipo' => 'basic');
		$this->cliente = new \toba_servicio_web_cliente_rest($opciones, null, null);
	}
		
	/**
	 * Permite agregar parametros a pasar al reporte
	 * @param string $nombre nombre del parametro
	 * @param string $tipo 'D' = fecha, 'E' = entero, 'S' = string/char/varchar, 'F' = decimal/punto flotante, 'B': booleano, 'L': Locale 
	 * @param string $valor valor del parametro
	 */
	function set_parametro($nombre='', $tipo='E', $valor=0)
	{
		$tipos_parametros = array('D', 'E', 'S', 'F', 'B', 'L');		
		if (! in_array($tipo, $tipos_parametros)) {
			throw new toba_error("Tipo incorrecto de parametro");
		}
		
		//Seteo el parametro
		$this->parametros[$nombre] = utf8_e_seguro($valor);
	}

	/**
	 * Permite eliminar todos los parametros que se le pasan al reporte 
	 */
	function reset_parametros()
	{
		unset($this->parametros);
		$this->parametros = [];
	}
	
	//------------------------------------------------------------------------
	//-- Configuracion
	//------------------------------------------------------------------------

	/**
	 * Devuelve el nombre del archivo pdf destino con la ruta absoluta
	 * @return string 
	 */
	function get_nombre_archivo_generado()
	{
		return $this->temp_salida;
	}


	/**
	 * @param string $nombre Nombre del archivo pdf + la extension del mismo (pdf)
	 */
	
	function set_nombre_archivo( $nombre )
	{
		$this->nombre_archivo = $nombre;
	}
	
	/**
	 * Permite setear el tipo de descarga pdf desde el browser, inline o attachment
	 * @param string $tipo inline o attachment
	 */
	function set_tipo_descarga( $tipo )
	{
		$this->tipo_salida = $tipo;
	}
	
	/**
	 * @param $reporte JRPrint 
	 */	
	function agregar_metareporte($reporte)
	{
		$this->lista_jrprint[] = $reporte;
	}
	
	/**
	 * @ignore
	 * @return boolean 
	 */
	function hay_metareportes()
	{
		return (!empty($this->lista_jrprint));
	}
	
	//------------------------------------------------------------------------
	//-- Parametros para compilar el reporte
	//------------------------------------------------------------------------
	
	/**
	 * Cambia la ubicación del archivo .jasper
	 * @param $path String
	 */
	function set_path_reporte($path) 
	{
		$this->uri = $path;
	}
	
	function compilar_reporte($path_plantilla, $path_reporte=null)
	{
		throw new toba_error('Compilacion no soportada por la  API'); //But maybe.. just maybe podamos crear el reporte dinamicamente en el server ;)
	}
	
	//------------------------------------------------------------------------	
	//-- Parametros de conexion (BD/Datasource o XML)
	//------------------------------------------------------------------------	
	/**
	 * Setea una conexion a BD/JDataSource
	 * @param mixed $db 
	 */
	function set_conexion($db)
	{
		$this->conexion = $db;			//hay que recuperar los parametros
	}
	
	/**
	 * Setea un string xml con los datos para el reporte
	 * @param string $xml  String con los datos en formato xml
	 * @param string $xpath_data_search  XPath al nodo que contiene los datos.Opcional
	 */
	function set_xml($xml, $xpath_data_search = null) 
	{
		//Creo un archivo XML  para guardar el contenido
		$nombre = toba::proyecto()->get_path_temp().'/'. hash('sha256', uniqid(time()));
		toba_manejador_archivos::crear_archivo_con_datos($nombre, $xml);
		$this->xml_path = $nombre;
		
		$this->xpath_data = $xpath_data_search;
		if (is_null($xpath_data_search)) {		//Si esto no viene no puedo crear un JRXMLDataSource, paso a modo archivo
			$this->modo_archivo = true;
			$this->limpiar_modo_archivo = true;
		}
	}
	
	/**
	 * Setea la ruta a un archivo conteniendo el xml con los datos para el reporte
	 * @param string $ruta_xml Ruta al archivo xml
	 * @param string $xpath_data_search XPath al nodo que contiene los datos. Opcional
	 */
	function set_archivo_xml($ruta_xml, $xpath_data_search = null)
	{
		$this->xml_path = toba_manejador_archivos::nombre_valido($ruta_xml);
		$this->xpath_data = $xpath_data_search;
		if (is_null($xpath_data_search)) {		//Si esto no viene no puedo crear un JRXMLDataSource, paso a modo archivo
			$this->modo_archivo = true;
		}		
	}
	//------------------------------------------------------------------------
	//-- Generacion del pdf
	//------------------------------------------------------------------------

	/**
	 * @ignore 
	 */
	function generar_salida()
	{
		foreach( $this->objetos as $objeto ) {
			if(method_exists($objeto, 'vista_jasperreports')) {
				$objeto->vista_jasperreports($this);	
			}
		}	
		
		// Pego los datos al jasper y creo el jprint	
		$this->crear_recursos_temporales();
	
		$this->crear_pdf();		//Aca uno todos los jprint en uno solito
		
		// Borrar XML si fue pasado por modo_archivo
		if (($this->modo_archivo) &&  ($this->limpiar_modo_archivo)) {
			if (file_exists($this->xml_path)) {
				unlink($this->xml_path);
			}			
		}
		
	}
			
	/**
	 * @ignore 
	 */
	protected function crear_pdf()
	{						
		$proyecto = toba::proyecto()->get_id();		
		$data = array('uri' => $this->uri, 'proyecto' => $proyecto, 'id' => $this->id_reporte, 'tipo_salida' => $this->extension);	
		//Agrego los parametros.
		if (! empty($this->parametros)) {
			$data['parametros'] = $this->parametros;
		}
				
		try {
			$resp = $this->cliente->guzzle()->request('GET', $this->url, array('query' =>$data));
		} catch(RequestException $e) {
                        var_dump($e->getMessage());
			die;
		}
		
		if ($resp->getStatusCode() == 200) {			
			file_put_contents($this->temp_salida, $resp->getBody());
		}		
	}
	
	/**
	 * Genera archivos locales en JRS para usarlos como recursos en el reporte
	 */
	function crear_recursos_temporales()
	{		
		//Si el conjunto de datos viene de un archivo comun
		$url = '/report/rest/archivos';
		$data = array('proyecto' => toba::proyecto()->get_id(), 'uri' => $this->uri);
		if ($this->modo_archivo) {
			$data['archivo']['tipo'] = 'xml';
                        $data['archivo']['temporal'] = true;
			$data['archivo']['data'] = base64_encode(file_get_contents($this->xml_path));
		}
		
		try {			
			$resp = $this->cliente->guzzle()->request('POST', $url, array('json' => $data));
		} catch (RequestException $e) {
                        var_dump($e->getMessage());
			die;
		}
		
		if ($resp->getStatusCode() == 201) {
			$result = json_decode($resp->getBody()->getContents());
			if ($result !== false) {
				$this->parametros['net.sf.jasperreports.xml.source'] = 'repo:' . $result->uri;
			}
		}		
	}

	/**
	 * Elimina los archivos creados antes.. una especie de Garbage Collector...
	 */
	function eliminar_recursos_temporales()
	{
		
	}
	
	/**
	 * Permite unir todos los jrprint en un solo archivo, a futuro quizas se devuelva directamente el arreglo
	 * @return jrprint $master_print
	 */
	protected function unir_metareportes()
	{
		
	}
	
	//------------------------------------------------------------------------
	//-- Definicion de fuente de datos
	//------------------------------------------------------------------------
	
	/**
	 * Crea una conexion por defecto, ya sea JDataSource o toba_db
	 * @return mixed
	 */
	protected function instanciar_conexion_default()
	{
	}
	
	/**
	 * Configura el schema para la conexion toba_db que se le provee
	 * @param toba_db $conexion
	 * @return JDBC 
	 */
	protected function configurar_bd(&$conexion)
	{
		/*$params = $conexion->get_parametros();
		//Creamos la conexión JDBC
		$con = new Java("org.altic.jasperReports.JdbcConnection");
		//Seteamos el driver jdbc
		$con->setDriver("org.postgresql.Driver");
		$port = (isset($params['puerto'])) ? ":".$params['puerto'] : '';
		$con->setConnectString("jdbc:postgresql://".$params['profile'].$port.'/'.$params['base']);
		//Especificamos los datos de la conexión, cabe aclarar que esta conexion es la del servidor de producción
		$con->setUser($params['usuario']);
		$con->setPassword($params['clave']);
		$con1 = $con->getConnection();
		if (isset($params['schema'])) {
			$sql = "SET search_path = \"{$params['schema']}\", \"public\";";			
			$stmt = $con1->createStatement();
			$stmt->executeUpdate($sql);
			toba::logger()->debug("Seteo el esquema por defecto para el reporte: $sql");			
		}
		return $con1;*/
	}
	
	//------------------------------------------------------------------------
	//-- Envio del archivo al cliente
	//------------------------------------------------------------------------
	
	function enviar_archivo()
	{
		$this->cabecera_http(filesize($this->temp_salida));
		readfile($this->temp_salida);
		unlink($this->temp_salida);		
	}
	
	/**
	 * @ignore 
	 */
	protected function cabecera_http( $longitud )
	{
		toba_http::headers_download($this->tipo_descarga, $this->nombre_archivo, $longitud);
	}
}
?>
