<?php

/**
 * Genera un pdf a través de una api básica
 * @package SalidaGrafica
 */
class toba_vista_jasperreports
{
	protected $path_reporte;
	protected $conexion;
//	protected $path_plantilla = '';
	
	protected $nombre_archivo = 'archivo.pdf';
	protected $tipo_descarga = 'attachment';
	protected $jasper;
	protected $temp_salida;

	// Parametros para el reporte
	protected $parametros;
	protected $xml_path;
	protected $xpath_data;
	protected $modo_archivo = false;
	protected $limpiar_modo_archivo = false;
	
	private $lista_jrprint = array();
	
	function __construct()
	{
		$this->temp_salida = toba::proyecto()->get_path_temp().'/'.uniqid('jasper_').'.pdf';		
		$this->cargar_jasper();
		/*Creamos una variable tipo arreglo que contendrá los parámetros */
		$this->parametros = new Java("java.util.HashMap");		
	}
	
	/**
	 * @ignore 
	 */
	function asignar_objetos( $objetos )
	{
		$this->objetos = $objetos;
	}

	protected function cargar_jasper()
	{
		if (!defined("JAVA_HOSTS")) define ("JAVA_HOSTS", "127.0.0.1:8081");
		//Incluimos la libreria JavaBridge
		require_once("3ros/JavaBridge/java/Java.inc");
		
		//Creamos una variable que va a contener todas las librerías java presentes
		$path_libs = toba_dir().'/php/3ros/JasperReports';
		$classpath = '';
		try {
			$archivos = toba_manejador_archivos::get_archivos_directorio($path_libs, '|.*\.jar$|' , true);
			foreach ($archivos as $archivo) {
				$classpath .= "file:$archivo;" ;
			}
		} catch (toba_error $et) {
			toba::logger()->error($et->getMessage());		//No se encontro el directorio, asi que no agrega nada al path y sigue el comportamiento que tenia con opendir			
		}
		try {
			//Añadimos las librerías
			java_require($classpath);

			//Creamos el objeto JasperReport que permite obtener el reporte
			$this->jasper = new JavaClass("net.sf.jasperreports.engine.JasperFillManager");		

		} catch (JavaException $ex) {
			$trace = new Java("java.io.ByteArrayOutputStream");
			$ex->printStackTrace(new Java("java.io.PrintStream", $trace));
			print "java stack trace: $trace\n";
		} catch (java_ConnectException $e) {
			toba::logger()->error($e->getMessage());
			throw new toba_error_usuario( 'No es posible generar el reporte, el servlet Jasper no se encuentra corriendo');
		}
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

		switch ($tipo) {
			case 'D':
				$tipo = "java.util.Date";				
				break;	
			case 'S':
				$tipo = "java.lang.String";
				$valor = utf8_e_seguro($valor);	
				break;
			case 'E':
				$tipo = "java.lang.Integer";				
				break;
			case 'F':
				$tipo = "java.math.BigDecimal";				
				break;
			case 'B':
				$tipo = "java.lang.Boolean";				
				break;
			 case 'L':
				$tipo = "java.util.Locale";
				$valor = utf8_e_seguro($valor);    
				break;			
			default:
				$tipo = "java.lang.String";
				$valor = utf8_e_seguro($valor);	
				break;
		}

		//Seteo el parametro
		$this->parametros->put($nombre, new Java($tipo, $valor));
	}

	/**
	 * Permite eliminar todos los parametros que se le pasan al reporte 
	 */
	function reset_parametros()
	{
		$this->parametros->clear();		//Borra la lista de parametros
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
		$this->path_reporte = $path;
	}

	function compilar_reporte($path_plantilla, $path_reporte=null)
	{
		if (is_null($path_reporte)) {
			$this->path_reporte = $path_plantilla . '.jasper';
		}	
		//Compilamos la plantilla
		$phpJasperCompileManager = new JavaClass("net.sf.jasperreports.engine.JasperCompileManager");
		$phpJasperCompileManager->compileReportToFile($path_plantilla, $this->path_reporte);				
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
		$this->conexion = $db;
	}
	
	/**
	 * Setea un string xml con los datos para el reporte
	 * @param string $xml  String con los datos en formato xml
	 * @param string $xpath_data_search  XPath al nodo que contiene los datos.Opcional
	 */
	function set_xml($xml, $xpath_data_search = null) 
	{
		//Creo un archivo XML  para guardar el contenido
		$nombre = toba::proyecto()->get_path_temp().'/'.md5(uniqid(time()));
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
		
		//Uno los distintos metareportes (jrprint) en un solo archivo para enviar el pdf.
		if (! $this->hay_metareportes()) {
			// Pego los datos al jasper y creo el jprint		
			$this->completar_con_datos();
		}
		
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
		//Uno todos los metareportes para generar un solo archivo
		$master_print = $this->unir_metareportes();

		////Exportamos el informe y lo guardamos como pdf en el directorio donde están los reportes
		$export_manager = new JavaClass("net.sf.jasperreports.engine.JasperExportManager");
		$export_manager->exportReportToPdfFile($master_print, $this->temp_salida);		
	}
	
	/**
	 * Genera un archivo jrprint y lo agrega al spool de union
	 */
	function completar_con_datos()
	{
		if (! isset($this->path_reporte)) {				//Lo chequeo aca adentro por si la funcion se llama mas de una vez
			throw new toba_error_def("Falta definir el .jasper con set_path_reporte");
		}	

		if ($this->modo_archivo) {									//Si el conjunto de datos viene de un archivo comun
			$jrl = new JavaClass("net.sf.jasperreports.engine.util.JRLoader");		
			$jrxmlutil = new JavaClass("net.sf.jasperreports.engine.util.JRXmlUtils");		
			$jrxpath = new JavaClass("net.sf.jasperreports.engine.query.JRXPathQueryExecuterFactory");		
			$document = $jrxmlutil->parse($jrl->getLocationInputStream($this->xml_path));
			//Pongo el archivo con los datos como parametro y creo el reporte
			$this->parametros->put($jrxpath->PARAMETER_XML_DATA_DOCUMENT, $document);		
			$print = $this->jasper->fillReport($this->path_reporte, $this->parametros);			
		}  else {													//El conjunto de datos viene de una db o datasource
			if (! isset($this->conexion)) {
				$this->conexion = $this->instanciar_conexion_default();
			}			 
			if ($this->conexion instanceof toba_db) {						//Si es una base toba, le configuro el schema
				$con1 = $this->configurar_bd($this->conexion);
			} else {
				$con1 = $this->conexion;
			}
			//Creo el reporte finalmente con la conexion JDBC
			$print = $this->jasper->fillReport($this->path_reporte, $this->parametros, $con1);
			$con1->close();
		}		
		$this->lista_jrprint[] = $print;
	}

	/**
	 * Permite unir todos los jrprint en un solo archivo, a futuro quizas se devuelva directamente el arreglo
	 * @return jrprint $master_print
	 */
	protected function unir_metareportes()
	{
		$master_print = array_shift($this->lista_jrprint);									//Busco el primero y lo saco

		//Para cada uno de los pdfs restantes
		$max = count($this->lista_jrprint);
		for($pdfx = 0; $pdfx < $max; $pdfx++) {
			$cant_hojas = java_values($this->lista_jrprint[$pdfx]->getPages()->size());			//Recupero la cantidad de hojas del metareporte X
			for ($count = 0; $count < $cant_hojas; $count++) {
				$master_print->addPage($this->lista_jrprint[$pdfx]->getPages()->get($count));	//Agrego la hoja en cuestion para cada metareporte X
			}
		}
		return $master_print;
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
		if (isset($this->xml_path) && isset($this->xpath_data)) {
			$con = new java("net.sf.jasperreports.engine.data.JRXmlDataSource", $this->xml_path, $this->xpath_data);						
		} elseif (! isset($this->conexion)) {
			$con = toba::db();
		}
		
		return $con;
	}
	
	/**
	 * Configura el schema para la conexion toba_db que se le provee
	 * @param toba_db $conexion
	 * @return JDBC 
	 */
	protected function configurar_bd(&$conexion)
	{
		$params = $conexion->get_parametros();
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
		return $con1;
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
