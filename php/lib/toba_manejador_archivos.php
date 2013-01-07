<?php
/**
* Clase con servicios sobre archivos y carpetas
* @package Varios
*/
class toba_manejador_archivos
{
	static private $caracteres_invalidos = array('*', '?', '/', '>', '<', '"', "'", ':', '|');
	static private $caracteres_reemplazo = array('%', '$', '_', ')', '(', '-',  '.', ';', ',');
	
	static function crear_arbol_directorios($path, $modo=0777)
	{
		if (self::es_windows()) {
			$path = self::path_a_windows($path, false);	
		}
		if (!file_exists($path)) {
			if (!mkdir($path, $modo, true)) {
				throw new toba_error("No es posible crear el directorio $path, verifique que el usuario de Apache posea privilegios de escritura sobre este directorio");
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
	
	
	static function ejecutar($cmd, &$stdout, &$stderr)
	{
	    $outfile = tempnam(toba_dir().'/temp', "cmd");
	    $errfile = tempnam(toba_dir().'/temp', "cmd");
	    $descriptorspec = array(
	        0 => array("pipe", "r"),
	        1 => array("file", $outfile, "w"),
	        2 => array("file", $errfile, "w")
	    );
	    $proc = proc_open($cmd, $descriptorspec, $pipes);
	   
	    if (!is_resource($proc)) return 255;
	
	    fclose($pipes[0]);
	
	    $exit = proc_close($proc);
	    $stdout = file_get_contents($outfile);
	    $stderr = file_get_contents($errfile);
	    unlink($outfile);
	    unlink($errfile);
	    return $exit;
	}		
	
	/**
	 * Similar al file_exists de php pero incluye al include_path en la búsqueda
	 */
	static function existe_archivo_en_path($file)
	{
		$fp = @fopen($file, 'r', true);
		$ok = ($fp) ? true : false;
		@fclose($fp);
		return $ok;
	}
	
	static function path_a_windows($nombre, $encomillar_espacios=true)
	{
		$nombre = str_replace('/', "\\", $nombre);	
		//Si algun segmento del PATH tiene espacios, hay que ponerlo entre comillas.
		if($encomillar_espacios && strpos($nombre,' ')){
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
	static function get_archivos_directorio( $directorio, $patron = null, $recursivo_subdir = false, &$exclude_dirs = array() )
	{
		$archivos_ok = array();
		if( ! is_dir( $directorio ) ) {
			throw new toba_error("BUSCAR ARCHIVOS: El directorio '$directorio' es INVALIDO");
		}

		if (in_array($directorio, $exclude_dirs)) {
			return $archivos_ok;
		}

		if ( ! $recursivo_subdir ) {
			$dir = opendir($directorio);
			if ($dir !== false) {
				while (false	!==	($archivo = readdir($dir)))	{
			   		if(  $archivo != ".svn" &&  $archivo != "." && $archivo != ".." ) {
						$archivos_ok[] = $directorio . '/' . $archivo;
			   		}
				}
			   closedir($dir); 
			}
		} else {
			$archivos_ok = self::buscar_archivos_directorio_recursivo( $directorio, $exclude_dirs);
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
	static function buscar_archivos_directorio_recursivo( $directorio, &$exclude_dirs = array() )
	{
		if( ! is_dir( $directorio ) ) {
			throw new toba_error("BUSCAR ARCHIVOS: El directorio '$directorio' es INVALIDO");
		} 
		$archivos = array();
		$d = dir( $directorio );

		if (in_array($directorio, $exclude_dirs)) {
			return $archivos;
		}

		while(false !== ($archivo = $d->read())) {
			if (  $archivo != ".svn" && $archivo != "." && $archivo != "..") {
				$path = $directorio.'/'.$archivo;
				if ( is_dir( $path ) ) {
					$archivos = array_merge( self::buscar_archivos_directorio_recursivo( $path, $exclude_dirs ), $archivos ) ;
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
			throw new toba_error("BUSCAR SUBDIRECTORIOS: El directorio '$directorio' es INVALIDO");
		} 
		$dir = opendir($directorio);
		if ($dir !== false) {	
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
	 * @return boolean True en caso de que la copia fue exitosa
	*/
	static function copiar_directorio( $origen, $destino, $excepciones=array(), $manejador_interface = null, $copiar_ocultos=true )
	{
		if( ! is_dir( $origen ) ) {
			throw new toba_error("COPIAR DIRECTORIO: El directorio de origen '$origen' es INVALIDO");
		}
		$ok = true;
		if( ! is_dir( $destino ) ) {
			$ok = mkdir($destino) && $ok;
		} 
		//Busco los archivos del directorio
		$lista_archivos = array();
		$dir = opendir($origen);
		if ($dir !== false) {
			while (false !== ($a = readdir($dir))) {
				if ( $a != '.' && $a != '..' && $a != '.svn' ) {
					$lista_archivos[] = $a;
				}
			}
			closedir( $dir );
		}
		//Copio los archivos
		foreach ( $lista_archivos as $archivo ) {
			$x_origen = $origen . '/' . $archivo;
			$x_destino = $destino . '/' . $archivo;
			//Evito excepciones			
			if (! in_array($x_origen, $excepciones) && ($copiar_ocultos || substr($archivo, 0, 1) != '.')) {			
				if ( is_dir( $x_origen )) {
					if (isset($manejador_interface)) {
						$manejador_interface->progreso_avanzar();
					}
					$ok = self::copiar_directorio( $x_origen, $x_destino, $excepciones, $manejador_interface ) && $ok;
				} else {
					$ok = copy($x_origen, $x_destino) && $ok;
				}
			}
		}
		return $ok;
	}
	
	/**
	*	Elimina un directorio con contenido
	*/
	static function eliminar_directorio( $directorio )
	{
		if( ! is_dir( $directorio ) ) {
			throw new toba_error("ELIMINAR DIRECTORIO: El directorio '$directorio' es INVALIDO");
		}
		$ok = true;
		$dir = opendir( $directorio );
		while ( $archivo = readdir( $dir ) ) {
			$path = $directorio.'/'.$archivo;
			if ( $archivo != "." && $archivo!=".." ) {
				if ( is_dir( $path ) ) {
				   $ok = self::eliminar_directorio($path) && $ok;
				} else {
				   $ok = unlink($path) && $ok;
				}
			}
		}
		closedir( $dir );
		$ok = rmdir($directorio) && $ok;
		return $ok;
	}	
	
	static function chmod_recursivo($path, $filemode) 
	{
		if (!is_dir($path))
			return chmod($path, $filemode);

		$dh = opendir($path);
		while ($file = readdir($dh)) {
			if($file != '.' && $file != '..') {
				$fullpath = $path.'/'.$file;
				if(!is_dir($fullpath)) {
					if (!chmod($fullpath, $filemode))
						return FALSE;
				} else {
					if (!chmod_recursivo($fullpath, $filemode))
						return FALSE;
				}
			}
		}

		closedir($dh);

		if(chmod($path, $filemode))
			return TRUE;
		else
			return FALSE;
	}
	
	
	static function comprimir_archivo($src, $level = 5, $dst = false)
	{
		if( $dst == false){
			$dst = $src.".gz";
		}
		if (file_exists($src)) {
			$src_handle = fopen($src, "r");
			if ($src_handle === false) {
				toba::logger()->error("Comprimir archivo: No se puede abrir $src");
				return false;
			}
			if (!file_exists($dst)){
				$dst_handle = gzopen($dst, "w$level");
				while(!feof($src_handle)){
					$chunk = fread($src_handle, 2048);
					gzwrite($dst_handle, $chunk);
				}
				fclose($src_handle);
				gzclose($dst_handle);
				return true;
			} else {
				toba::logger()->error("Comprimir archivo: $dst ya existe");
			}
		} else {
			toba::logger()->error("Comprimir archivo: $src no existe");	    	
		}
		return false;
	 }	
	 
	static function es_directorio_vacio($dir)
	{
		$dh = @opendir($dir);
		if ($dh !== false) {
			while ($file = readdir($dh)) {
				if ($file != '.' && $file != '..') {
					closedir($dh);
					return false;
				}
			}
			closedir($dh);
			return true;
		} else {
			return false;
		}
	}

	static function es_writable($path)
	{
		if ($path{strlen($path)-1} == '/') {
			return is__writable($path.uniqid(mt_rand()).'.tmp');
		}
		if (file_exists($path)) {
			if (!($f = @fopen($path, 'r+'))) {
				return false;
			}
			fclose($f);
			return true;
		}
		if (!($f = @fopen($path, 'w'))) {
			return false;
		}
		fclose($f);
		unlink($path);
		return true;
	}

	/**
	 * Retorna el nombre de usuario que actualmente ejecuta el proceso
	 * @return null en caso 
	 */
	static function get_usuario_actual()
	{
		$usuario = null;
		if (! self::es_windows()) {
			$salida = array();
			$valor_retorno = null;
			exec('whoami', $salida, $valor_retorno);
			if ($valor_retorno == 0) {
				$usuario = strtolower($salida[0]);
			}
		} else {
			$usuario = strtolower(get_current_user());
		}
		return $usuario;
	}

	static function get_checksum_directorio($directorio)
	{
		$checksum = null;
		$archivos = self::buscar_archivos_directorio_recursivo($directorio);
		if (! empty($archivos)) {
			$hsh_handler = hash_init('sha256');
			$sin_error = true;
			foreach($archivos as $archivo) {
				$sin_error = @hash_update_file($hsh_handler, $archivo);
				if (! $sin_error) {	//Si se produce error en el calculo aborto
					hash_final($hsh_handler);
					toba::logger()->error("\nError calculando checksum con el archivo '$archivo' ");
					throw new toba_error("\nError calculando checksum con el archivo '$archivo' ");
				}
			}
			$checksum = hash_final($hsh_handler);
		}
		return $checksum;
	}
}
?>