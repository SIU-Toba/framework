<?php

/**
 * Genera un pdf a través de una api básica
 * @package SalidaGrafica
 */
class toba_vista_jasperreports
{
	protected $path_reporte;
	protected $conexion;
	
	protected $nombre_archivo = 'archivo.pdf';
	protected $tipo_descarga = 'attachment';
	protected $jasper;
	protected $temp_salida;
	
	
	function __construct()
	{
		$this->temp_salida = toba::proyecto()->get_path_temp().'/'.uniqid('jasper_').'.pdf';		
		$this->cargar_jasper();
	}
	

	protected function cargar_jasper()
	{
		define ("JAVA_HOSTS", "127.0.0.1:8081");
        //Incluimos la libreria JavaBridge
        require_once("3ros/JavaBridge/java/Java.inc");

        //Creamos una variable que va a contener todas las librerías java presentes
		$path_libs = toba_dir().'/php/3ros/JasperReports';
		$handle = opendir($path_libs);
		$classpath = "";
		while (($lib = readdir($handle)) != false) {
			$classpath .= 'file:'.$path_libs.'/'.$lib .';';
		}
		try {
			//Añadimos las librerías
			java_require($classpath);
            
            //Creamos el objeto JasperReport que permite obtener el reporte
			$this->jasper = new JavaClass("net.sf.jasperreports.engine.JasperFillManager");
 
        } catch (JavaException $ex){
			$trace = new Java("java.io.ByteArrayOutputStream");
			$ex->printStackTrace(new Java("java.io.PrintStream", $trace));
            print "java stack trace: $trace\n";
        }		
	}
	
	//------------------------------------------------------------------------
	//-- Configuracion
	//------------------------------------------------------------------------

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
	 * Cambia la ubicación del archivo .jasper
	 * @param $path String
	 */
	function set_path_reporte($path) 
	{
		$this->path_reporte = $path;
	}
	
	function set_conexion($db)
	{
		$this->conexion = db;
	}


	//------------------------------------------------------------------------
	//-- Generacion del pdf
	//------------------------------------------------------------------------

	/**
	 * @ignore 
	 */
	function generar_salida()
	{
		if (! isset($this->path_reporte)) {
			throw new toba_error_def("Falta definir el .jasper con set_path_reporte");
		}	
		if (! isset($this->conexion)) {
			$this->conexion = toba::db();
		}
		$this->crear_pdf();
	}

	function enviar_archivo()
	{
		$this->cabecera_http(filesize($this->temp_salida));
		readfile($this->temp_salida);
		unlink($this->temp_salida);		
	}
		
	/**
	 * @ignore 
	 */
	protected function crear_pdf()
	{
		$params = $this->conexion->get_parametros();
		
		//Creamos la conexión JDBC
		$con = new Java("org.altic.jasperReports.JdbcConnection");
		//Seteamos el driver jdbc
		$con->setDriver("org.postgresql.Driver");
		$port = (isset($params['puerto'])) ? ":".$params['puerto'] : '';
		$con->setConnectString("jdbc:postgresql://".$params['profile'].$port.'/'.$params['base']);
		//Especificamos los datos de la conexión, cabe aclarar que esta conexion es la del servidor de producción
		$con->setUser($params['usuario']);
		$con->setPassword($params['clave']);

		/*Creamos una variable tipo arreglo que contendrá los parámetros En este caso el parametro se llama PARAMETRO_EJEMPLO, y el valor es un String */
		$param = new Java("java.util.HashMap");
		$param->put("paramA", new Java('java.lang.String', "toba" ));
			
		//Guardamos el reporte en una variable $print para luego exportarla
		$print = $this->jasper->fillReportToFile($this->path_reporte, $param, $con->getConnection());
            
		//Exportamos el informe y lo guardamos como pdf en el directorio donde están los reportes
		$export_manager = new JavaClass("net.sf.jasperreports.engine.JasperExportManager");
		$export_manager->exportReportToPdfFile($print, $this->temp_salida);
	}

	/**
	 * @ignore 
	 */
	protected function cabecera_http( $longuitud )
	{
		header("Cache-Control: private");
  		header("Content-type: application/pdf");
  		header("Content-Length: $longuitud");	
   		header("Content-Disposition: {$this->tipo_descarga}; filename={$this->nombre_archivo}");
  		//header("Accept-Ranges: $longuitud"); 
  		header("Pragma: no-cache:");
		header("Expires: 0");
	}
	

}
?>
