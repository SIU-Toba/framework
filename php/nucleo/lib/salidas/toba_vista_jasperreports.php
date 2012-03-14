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

	// PArametros para el reporte
	protected $parametros;
	
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
		//throw new toba_error('Aun estamos desarrollando esta funcionalidad.... puede que nos falte algo');
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
		$this->conexion = $db;
	}

	/**
	 * Permite agregar parametros a pasar al reporte
	 * @param string $nombre nombre del parametro
	 * @param string $tipo 'D' = fecha, 'E' = entero, 'S' = string/char/varchar, 'F' = decimal/punto flotante
	 * @param string $valor valor del parametro
	 */
	function set_parametro($nombre='', $tipo='E', $valor=0)
	{
		if (!(($tipo=='D') || ($tipo=='E') || ($tipo=='S') || ($tipo=='F'))) {
			throw new toba_error("Tipo incorrecto de parametro");
		}

		switch ($tipo) {
			case 'D':
				$tipo = "java.sql.Date";				
				break;	
			case 'S':
				$tipo = "java.lang.String";				
				break;
			case 'E':
				$tipo = "java.lang.Integer";				
				break;
			case 'F':
				$tipo = "java.math.BigDecimal";				
				break;
			default:
				$tipo = "java.lang.String";
				break;
		}

		//Seteo el parametro
		$this->parametros->put($nombre, new Java($tipo, $valor));
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
		
		if (isset($params['schema'])) {
			$sql = "SET search_path = \"{$params['schema']}\", \"public\";";
			$con1 = $con->getConnection();		
			$stmt = $con1->createStatement();
			$stmt->executeUpdate($sql);
			toba::logger()->debug("Seteo el esquema por defecto para el reporte: $sql");			
		}
		
		//Guardamos el reporte en una variable $print para luego exportarla
		$print = $this->jasper->fillReportToFile($this->path_reporte, $this->parametros, $con1);
            
		////Exportamos el informe y lo guardamos como pdf en el directorio donde están los reportes
		$export_manager = new JavaClass("net.sf.jasperreports.engine.JasperExportManager");
		$export_manager->exportReportToPdfFile($print, $this->temp_salida);
		$con1->close();
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
