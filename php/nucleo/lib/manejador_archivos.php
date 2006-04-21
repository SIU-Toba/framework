<?php
/**
*	Manipulacion de archivos
*/
class manejador_archivos
{
	static private $caracteres_invalidos = array('*', '?', '/', '>', '<', '"', "'", ':', '|');
	static private $caracteres_reemplazo = array('%', '$', '_', ')', '(', '-',  '.', ';', ',');
	
	static function crear_arbol_directorios($path, $modo=0777)
	{
		if (!file_exists($path)) {
			if (!mkdir($path, $modo, true)) {
				throw new excepcion_toba("No es posible crear el directorio $path, verifique que el usuario de Apache posea privilegios de escritura sobre este directorio");
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
			throw new excepcion_toba("BUSCAR ARCHIVOS: El directorio '$directorio' es INVALIDO");
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
			$archivos_ok = self::buscar_archivos_directorio_recursivo( $directorio );
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
	function buscar_archivos_directorio_recursivo( $directorio )
	{
		if( ! is_dir( $directorio ) ) {
			throw new excepcion_toba("BUSCAR ARCHIVOS: El directorio '$directorio' es INVALIDO");
		} 
		$archivos = array();
		$d = dir( $directorio );
		while($archivo = $d->read()) {
			if (  $archivo != ".svn" && $archivo != "." && $archivo != "..") {
				$path = $directorio.'/'.$archivo;
				if ( is_dir( $path ) ) {
					$archivos = array_merge( self::buscar_archivos_directorio_recursivo( $path ), $archivos ) ;
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
			throw new excepcion_toba("BUSCAR SUBDIRECTORIOS: El directorio '$directorio' es INVALIDO");
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
			throw new excepcion_toba("COPIAR DIRECTORIO: El directorio de origen '$origen' es INVALIDO");
		} 
		if( ! is_dir( $destino ) ) {
			mkdir( $destino );
		} 
		//Busco los archivos del directorio
		$lista_archivos = array();
		if ( $dir = opendir( $origen ) ) {
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
			if ( is_dir( $x_origen ) ) {
				self::copiar_directorio( $x_origen, $x_destino );
			} else {
				copy( $x_origen, $x_destino );	
			}
		}
	}
	
	/**
	*	Elimina un directorio con contenido
	*/
	static function eliminar_directorio( $directorio )
	{
		if( ! is_dir( $directorio ) ) {
			throw new excepcion_toba("ELIMINAR DIRECTORIO: El directorio '$directorio' es INVALIDO");
		} 
		$dir = opendir( $directorio );
		while ( $archivo = readdir( $dir ) ) {
			$path = $directorio.'/'.$archivo;
			if ( $archivo != "." && $archivo!=".." ) {
				if ( is_dir( $path ) ) {
				   self::eliminar_directorio( $path );
				} else {
				   unlink( $path );
				}
			}
		}
		closedir( $dir );
		rmdir( $directorio );
	}	
	
	function comprimir_archivo($src, $level = 5, $dst = false){
	    if( $dst == false){
	        $dst = $src.".gz";
	    }
	    if (file_exists($src)) {
	        $filesize = filesize($src);
	        $src_handle = fopen($src, "r");
	        if ($src_handle === false) {
	            toba::get_logger()->error("Comprimir archivo: No se puede abrir $src");
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
	            toba::get_logger()->error("Comprimir archivo: $dst ya existe");
	        }
	    } else {
            toba::get_logger()->error("Comprimir archivo: $src no existe");	    	
	    }
	    return false;
	 }	
}
?>