<?php
require_once("nucleo/browser/interface/form.php");
/*
 	SETEOS necesarios en el PHP.INI para que esta clase funcione.
		- file_uploads
		- upload_tmp_dir
		- upload_max_filesize < post_max_size < memory_limit
	(Excepto la de la memoria, las demas no pueden setearse desde el SCRIPT)
*/

class manejador_archivos
//Maneja el UPLOAD de archivos (hasta ahora el UPLOAD simple)
{
	var $limite_bytes_cliente;
	var $nombre_input;
	var $nombre_archivo;

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
				global $solicitud;
				$dir_upload = $solicitud->hilo->obtener_proyecto_path() . "/temp/";
				$this->nombre_archivo = $dir_upload . $_FILES[$this->nombre_input]['name'];
				if (move_uploaded_file($_FILES[$this->nombre_input]['tmp_name'], $this->nombre_archivo)) 
				{
					//Seteo el nombre del archivo cargado para que reaparezca en la interface
					if($temp_sesion){
						global $solicitud;
						//Notifico al hilo el archivo cargado para ELIMINARLO con el FIN de SESION
						$solicitud->hilo->registrar_archivo($this->nombre_archivo);
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
		global $solicitud;
		$this->obtener_html_mensaje();
		enter();
		echo form::abrir("upload",$solicitud->vinculador->generar_solicitud());
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