<?php
	
use GuzzleHttp\Exception\RequestException;

/**
 * Genera un pdf a través de una api básica
 * @package SalidaGrafica
 */
class toba_vista_araireportes
{
	protected $nombre_archivo = 'archivo.pdf';
	protected $tipo_descarga = 'attachment';
	protected $temp_salida;

	// Parametros para el reporte
	protected $parametros;
	protected $xml_path;
	protected $xpath_data;
	protected $modo_archivo = false;
	protected $limpiar_modo_archivo = false;
	
	private $lista_jrprint = array();
	protected $url; 
	
	protected $uri;
	protected $extension = 'pdf';
	protected $id_reporte='';
	
	 static protected $servicio_reporte='reportes';
	
	function __construct()
	{
		$this->temp_salida = toba::proyecto()->get_path_temp().'/'.uniqid('jasper_');
		$this->url =  self::$servicio_reporte .'?proyecto='. toba::proyecto()->get_id();			//Hago que parta de la carpeta del proyecto (o al menos deberia)
		
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
	
	protected function crear_cliente()
	{
		$this->cliente = toba_servicio_web_cliente_rest::conectar('rest_arai_reportes');
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
		$this->url = self::$servicio_reporte . "/". toba::proyecto()->get_id();
	}
	
	function compilar_reporte($path_plantilla, $path_reporte=null)
	{
		throw new toba_error('Compilacion no soportada por la  API'); 
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
			if(method_exists($objeto, 'vista_araireportes')) {
				$objeto->vista_araireportes($this);	
			}
		}		
		$this->crear_recursos_temporales();	
		$this->crear_pdf();		
		
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
			toba::logger()->debug($e->getMessage());
			throw new toba_error_usuario('Se produjo un error al generar el reporte');
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
		if (isset($this->xml_path)) {
			$url = 'archivos';
			$data = array('proyecto' => toba::proyecto()->get_id(), 'uri' => $this->uri);
			if ($this->modo_archivo) {
				$data['archivo']['tipo'] = 'xml';
				$data['archivo']['temporal'] = 1;
				$data['archivo']['data'] = base64_encode(file_get_contents($this->xml_path));
			}

			try {			
				$resp = $this->cliente->guzzle()->request('POST', $url, array('json' => $data));
			} catch (RequestException $e) {
				toba::logger()->debug($e->getMessage());
				throw new toba_error_usuario('Se produjo un error al generar el reporte');
			}

			if ($resp->getStatusCode() == 201) {
				$result = json_decode($resp->getBody()->getContents());
				if ($result !== false) {
					$this->parametros['net.sf.jasperreports.xml.source'] = 'repo:' . $result->uri;
				}
			}		
		}
	}

	/**
	 * Elimina los archivos creados antes.. una especie de Garbage Collector...
	 */
	function eliminar_recursos_temporales()
	{}
	
	/**
	 * Permite unir todos los jrprint en un solo archivo, a futuro quizas se devuelva directamente el arreglo
	 * @return jrprint $master_print
	 */
	protected function unir_metareportes()
	{}
	
	//------------------------------------------------------------------------
	//-- Definicion de fuente de datos
	//------------------------------------------------------------------------
	
	/**
	 * Crea una conexion por defecto, ya sea JDataSource o toba_db
	 * @return mixed
	 */
	protected function instanciar_conexion_default()
	{}
	
	/**
	 * Configura el schema para la conexion toba_db que se le provee
	 * @param toba_db $conexion
	 * @return JDBC 
	 */
	protected function configurar_bd(&$conexion)
	{}
	
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
