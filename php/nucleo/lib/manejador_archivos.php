<?php
require_once("nucleo/browser/interface/form.php");
/*
	Atencion, esta clase empezo siendo algo relacionado con los UPDLODAS, 
	y con el tiempo se acerco a una funcionalidad cercana a su nombre.

 	SETEOS necesarios en el PHP.INI para que esta clase funcione.
		- file_uploads
		- upload_tmp_dir
		- upload_max_filesize < post_max_size < memory_limit
	(Excepto la de la memoria, las demas no pueden setearse desde el SCRIPT)
*/

class manejador_archivos
//Maneja el UPLOAD de archivos (hasta ahora el UPLOAD simple)
{
	protected $limite_bytes_cliente;
	protected $nombre_input;
	protected $nombre_archivo;
	static private $caracteres_invalidos = array('*', '?', '/', '>', '<', '"', "'", ':', '|');
	static private $caracteres_reemplazo = array('%', '$', '_', ')', '(', '-',  '.', ';', ',');
	
	static function crear_arbol_directorios($path)
	{
		//Verifica que todos los subdirectorios existan
		$directorios = explode("/", $path);
		$path_acumulado = '';
		foreach ($directorios as $directorio) {
			$path_acumulado .= $directorio."/";
			if (! file_exists($path_acumulado)) {	//El path no existe, intenta crearlo
				if (! mkdir($path_acumulado))
					throw new excepcion_toba("No es posible crear el directorio $path_acumulado");
			}
		}
	}
	
	static function crear_archivo_con_datos($nombre, $datos)
	{
		if (! file_exists($nombre)) {
			self::crear_arbol_directorios(dirname($nombre));
		}
		file_put_contents($nombre, $datos);		
	}
	
	static function es_windows()
	{
		return (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
	}	
	
	static function path_a_windows($nombre)
	{
		$nombre = str_replace('/', "\\", $nombre);	
		//Si algun segmento del PATH tiene espacios, hay que ponerlo entre comillas.
		if(strpos($nombre,' ')){
			$segmentos = explode("\\",$nombre);
			for($a=0;$a<(count($segmentos));$a++){
				if(strpos($segmentos[$a],' ')){
					$segmentos[$a] = '"'.$segmentos[$a].'"';
				}
			}
			$nombre = implode("\\",$segmentos);
		}
		return $nombre;
	}

	static function path_a_unix($nombre)
	{
		return str_replace('\\', "/", $nombre);	
	}	
	
	/**
	 * Retorna un nombre de archivo valido
	 */
	static function path_a_plataforma($path)
	{
		if (self::es_windows()) {
			return self::path_a_windows($path);
		} else {
			return self::path_a_unix($path);		
		}
	}
	
	/**
	 * Retorna un path convertido a la plataforma actual de ejecución (unix o windows)
	 */
	static function nombre_valido( $candidato )
	{
		return str_replace( self::$caracteres_invalidos, self::$caracteres_reemplazo, $candidato );
	}
	
	/**
	*	Buscador de archivos
	*/
	static function get_archivos_directorio( $directorio, $patron = null, $recursivo_subdir = false )
	{
		$archivos_ok = array();
		if( ! is_dir( $directorio ) ) {
			throw new excepcion_toba("Buscando archivos en directorio '$directorio'. El directorio es invalido");
		} 
		if ( ! $recursivo_subdir ) {
			if ( $dir = opendir( $directorio ) ) {	
			   while (false	!==	($archivo = readdir($dir)))	{
			   		if(  $archivo != ".svn" &&  $archivo != "." && $archivo != ".." ) {
						$archivos_ok[] = $directorio . '/' . $archivo;
			   		}
				}
			   closedir($dir); 
			}
		} else {
			$archivos_ok = self::buscar_archivos_subdir( $directorio );
		}
		//Si existe un patron activado, filtro los archivos
		if( isset( $patron ) ){
			$temp = array();
			foreach( $archivos_ok as $archivo ) {
				if( preg_match( $patron, $archivo )){
					$temp[] = $archivo;
				}
			}
			$archivos_ok = $temp;
		}
		return $archivos_ok;
	}

	/**
	*	Busca en profundidad los archivos existentes dentro de un directorio
	*/
	function buscar_archivos_subdir( $directorio )
	{
		$archivos = array();
		$d = dir( $directorio );
		while($archivo = $d->read()) {
			if (  $archivo != ".svn" && $archivo != "." && $archivo != "..") {
				$path = $directorio.'/'.$archivo;
				if ( is_dir( $path ) ) {
					$archivos = array_merge( self::buscar_archivos_subdir( $path ), $archivos ) ;
				} else {
					$archivos[] = $path;
				}
			}
		}
		$d->close();
		return $archivos;
	}
	
	/**
	*	Devuelve la lista de subdirectorios de un directorio
	*/
	static function get_subdirectorios( $directorio )
	{
		$dirs = array();
		if( ! is_dir( $directorio ) ) {
			throw new excepcion_toba("Buscando archivos en directorio '$directorio'. El directorio es invalido");
		} 
		if ($dir = opendir( $directorio )) {	
		   while (false	!==	( $archivo = readdir( $dir ) ) )	{ 
				if( ( $archivo != '.' ) && ( $archivo != '..' ) && ( $archivo != '.svn' ) ) {
					$path = $directorio . '/' . $archivo;
					if ( is_dir( $path ) ) {
						$dirs[] = $path;
					}
				}
		   } 
		   closedir( $dir );
		}
		return $dirs;
	}
	
	/**
	*	Copia el contenido de un directorio a otro.
	*	No copia las carpetas SVN
	*/
	static function copiar_directorio( $origen, $destino )
	{
		if( ! is_dir( $origen ) ) {
			throw new excepcion_toba("EL directorio de origen '$origen' es INVALIDO");
		} 
		if( ! is_dir( $destino ) ) {
			throw new excepcion_toba("EL directorio de destino '$destino' es INVALIDO");
		} 
		//Falta terminar
	}

	//---------------------------------------------------------------------------------
	
	function manejador_archivos($input="archivo",$temp_sesion=true,$limite=3000)
	{
		$this->limite_bytes_cliente = $limite * 1024;
		$this->nombre_input = $input;
		$this->nombre_archivo = null;
		//Cargo el archivo
		if( acceso_post() )
		{
			$estado = $this->controlar_estado();
			if($estado[0] == 1)					//---> UPLOAD OK!
			{
				$dir_upload = toba::get_hilo()->obtener_proyecto_path() . "/temp/";
				$this->nombre_archivo = $dir_upload . $_FILES[$this->nombre_input]['name'];
				if (move_uploaded_file($_FILES[$this->nombre_input]['tmp_name'], $this->nombre_archivo)) 
				{
					//Seteo el nombre del archivo cargado para que reaparezca en la interface
					if($temp_sesion){
						//Notifico al hilo el archivo cargado para ELIMINARLO con el FIN de SESION
						toba::get_hilo()->registrar_archivo($this->nombre_archivo);
					}
				}
			}elseif( $estado[0] < 0){			//---> ERROR!
				//LOG de ERRORES
			}
		}
	}
	//-------------------------------------------------------------------------------
	
	function controlar_estado()
	//Devuelve el estado del proceso de UPLOAD
	{
		if( acceso_post() ){
			switch($_FILES[$this->nombre_input]['error']){
				case UPLOAD_ERR_OK:
					return array(1,"El archivo fue cargado correctamente");
					break;
				case UPLOAD_ERR_NO_FILE:
					return array(0,"No se envio un archivo");
					break;
				case UPLOAD_ERR_INI_SIZE:
					return array(-1,"Se supero el limite seteado en PHP.INI");
					break;
				case UPLOAD_ERR_FORM_SIZE:
					return array(-2,"Se supero el limite expresado en el FORM");
					break;
				case UPLOAD_ERR_PARTIAL:
					return array(-3,"Ha ocurrido un error cargando el archivo");
					break;
			}
		}
	}
	//-------------------------------------------------------------------------------

	function obtener_nombre_archivo()
	//Devuelve el nombre del archivo cargado
	{
		return $this->nombre_archivo;
	}
	//-------------------------------------------------------------------------------

	function obtener_nombre_input()
	//Devuelve el nombre del input
	{
		return $this->nombre_input;
	}
	//-------------------------------------------------------------------------------

	function obtener_html()
	//Llamada completa
	{
		$this->obtener_html_mensaje();
		enter();
		echo form::abrir("upload", toba::get_vinculador()->generar_solicitud());
		$this->obtener_interface();
		enter();
		echo form::submit("submit","SUBIR");
		echo form::cerrar();
	}
	//-------------------------------------------------------------------------------

	function obtener_interface()
	//Llamada como subcomponente
	{
		if(isset($this->limite_bytes_cliente)&&($this->limite_bytes_cliente>0 )){
			echo form::hidden("MAX_FILE_SIZE",$this->limite_bytes_cliente);
		}
		echo form::archivo($this->nombre_input);
	}
	//-------------------------------------------------------------------------------

	function obtener_html_mensaje()
	{
		if( acceso_post() ){
			$estado = $this->controlar_estado();
			if($estado[0] >= 0){
				echo ei_mensaje($estado[1]);
			}else{
				echo ei_mensaje($estado[1],"error");
			}
		}
	}
	//-------------------------------------------------------------------------------
}
?>