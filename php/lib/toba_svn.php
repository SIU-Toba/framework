<?php	

/**
 * Clase con servicios de cliente svn
 * @package Varios
 */
class toba_svn
{
	protected $url_base;
	protected $proceso_actual;
	protected $es_windows;
	protected $pipe;
	protected $cancelado = false;
	protected $error = '';
	protected $progreso;
	
	function __construct()
	{
		$this->es_windows = toba_manejador_archivos::es_windows();
	}
	
	protected function detectUTF8($string)
	{
	        return preg_match('%(?:
	        [\xC2-\xDF][\x80-\xBF]        # non-overlong 2-byte
	        |\xE0[\xA0-\xBF][\x80-\xBF]               # excluding overlongs
	        |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}      # straight 3-byte
	        |\xED[\x80-\x9F][\x80-\xBF]               # excluding surrogates
	        |\xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
	        |[\xF1-\xF3][\x80-\xBF]{3}                  # planes 4-15
	        |\xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
	        )+%xs', $string);
	}
	
	protected function desde_consola($mensaje)
	{
		if ($this->es_windows) {
			$desde="IBM850";
		} else {
			$desde = $this->detectUTF8($mensaje) ? 'UTF-8':'ISO-8859-1';
		}
		$hasta="ISO-8859-1";	
		return iconv($desde, $hasta, $mensaje);		
	}
	
	protected function ejecutar($cmd, $no_interactivo=true, $critico=true, $loguear=true, $archivo_salida=null)
	{
		$this->error = '';
		$this->progreso = '';
		if ($no_interactivo) {
			$cmd .= '  --non-interactive';
		}
		if ($loguear) {
			toba::logger()->info("Ejecutando: ".$cmd."\n");
		}
		/*if ($no_interactivo) {
			$cmd .= ' --username '.inst_fact::config()->get('svn', 'usuario').
					' --password '.inst_fact::config()->get_clave_svn();
		}*/
		if (isset($archivo_salida)) {
			$cmd .= ' >'.$archivo_salida;
		}
		
		//toba_manejador_archivos::ejecutar($cmd, $this->progreso, $this->error);
		toba_manejador_procesos::ejecutar($cmd, $this->progreso, $this->error);
		if ($this->error != '' && $loguear) {
			toba::logger()->error($this->error);
		}
		if ($critico && $this->error != '') {
			throw new toba_error($this->error);
		}
		return $this->progreso;
	}
	
	function ejecucion_mensaje($datos)
	{
		if ($datos != '') {
			$datos = $this->desde_consola($datos);
			//--- Se busca cuantos enters tiene el mensaje para determinar el progreso
			$partes = explode("\n", $datos);
			foreach ($partes as $parte) {
				if (trim($parte) != '') {
					$this->progreso->avanzar_interno($parte."\n");
				}
			}
		}
	}
	
	function ejecucion_error($datos)
	{
		$datos = $this->desde_consola($datos);		
		$this->progreso->error($datos);
		$this->error = $datos;
	}

	function ejecucion_cancelar()
	{
		$this->cancelado = true;		
		if (isset($this->pipe) && ! $this->pipe->terminado()) {
			$id = $this->pipe->terminate();
		}
		if (isset($this->proceso_actual) && is_resource($this->proceso_actual)) {
			pclose($this->proceso_actual);
			unset($this->proceso_actual);
		}
	
	}

	//-------- Alto Nivel
	
	function probar_conexion($url, $usuario, $clave)
	{
		$cmd = "svn ls $url --username $usuario --password $clave --no-auth-cache --non-interactive";
		try {
			$ok = $this->ejecutar($cmd, false, true, false);
		} catch (toba_error $e) {
			return $e->getMessage();
		}
		return true;
	}
	
	function hay_cliente_svn()
	{
		$cmd = "svn --version";
		try {
			$ok = $this->ejecutar($cmd, false, true, false);
		} catch (toba_error $e) {
			return false;
		}
		return true;		
	}
	
	function verificar_cliente_svn()
	{
		if (! self::hay_cliente_svn()) {
			$msg = "\n\nAtención !\nHubo un error al acceder al cliente de SVN local.\nAsegúrese de tener instalada una versión reciente y que la misma esté accesible.\nEjecute 'svn --version' para más información.";
			throw new toba_error_def($msg);
		} 
	}
	
	function get_estado($path)
	{
		try {
			$cmd = "svn st \"$path\" --xml";
			$xml = $this->ejecutar($cmd);
			$xml = simplexml_load_string($xml);
			if (!isset($xml->target)) {
				return 'unversioned';
			}
			if (!isset($xml->target->entry)) {
				if (file_exists($path)) {
					return 'normal';
				} else {
					return 'unversioned';
				}
			}
			if (!isset($xml->target->entry->{'wc-status'})) {
				return 'unversioned';	
			}
			return (string) $xml->target->entry->{'wc-status'}['item'];
		} catch(toba_error $e) {
			toba::logger()->error($e);
			return 'unversioned';
		}
	}
	
	function get_url($path)
	{
		$cmd = "svn info \"$path\" --xml";
		try{
			$xml_rs = $this->ejecutar($cmd, true, true);
			$xml = simplexml_load_string($xml_rs);
			if (isset($xml->info->entry)) {
				return $xml->info->entry->url;
			}			
			if (isset($xml->entry)) {
				return $xml->entry->url;
			}
		} catch(toba_error $e) {
			toba::logger()->debug("SVN: ". $e->getMessage());
		}			
	}
	
	function get_revision($path)
	{
		$cmd = "svn info \"$path\" --xml";
		try{
			$xml_rs = $this->ejecutar($cmd, true, true);
			$xml = simplexml_load_string($xml_rs);
			if (isset($xml->info->entry->revision)) {
				return $xml->info->entry->revision->__toString();
			}
			if (isset($xml->entry->revision)) {
				return $xml->entry->revision->__toString();
			}
			if (isset($xml->entry[0]['revision'])) {
				return $xml->entry[0]['revision']->__toString();
			}
		} catch(toba_error $e) {
			toba::logger()->debug("SVN: ". $e->getMessage());
		}
		return '';
	}

	function get_revisiones_dir_recursivos($path)
	{
		$revs = array();
		$cmd = "svn info \"$path\"  -R --xml";
		try{
			$xml = $this->ejecutar($cmd, true, true, false);
			$xml = simplexml_load_string($xml);
			if ($xml !== false){				
				foreach($xml->entry as $entrada) {
					$aux_entrada = $entrada->attributes();
					if (@(!isset($aux_entrada['kind']) || !isset($aux_entrada['path']))) {
						//Problema de formato XML, posible drama de version.
						toba::logger()->debug("SVN FAIL: ". var_export($aux_entrada, true));
						$revs[] = array('error' => 'La versión de svn es demasiado antigua, por favor actualicela.');
					} else {
						if (! $entrada->xpath('commit')) {	//Es un ADD asi que aun no tiene revision de commit
							$revs[] = array('kind' => $aux_entrada['kind'], 'archivo' => $aux_entrada['path'], 'revision' => 0);
							toba::logger()->debug("SVN ADD: ". var_export($aux_entrada, true));
						} else {	//Tiene todos los valores necesarios
							$aux_revision = $entrada->commit->attributes();
							$revs[] = array('kind' => $aux_entrada['kind'], 'archivo' => $aux_entrada['path'], 'revision' => $aux_revision['revision']);
						}
					}
					unset($aux_entrada);
					unset($aux_revision);					
				}
			}
		} catch(toba_error $e) {
			toba::logger()->debug("SVN: ". $e->getMessage());
		}
		return $revs;
	}

	function reducir_url($url)
	{
		$base = $this->url_base;
		$pos = strpos($url, $base);
		if ($pos === false) {
			return $url;
		} else {
			return substr($url, strlen($base)+1);
		}		
		
	}
	
	static function es_copia_trabajo($path)
	{
		$salida = null;
		$hubo_error = null;		
		$cmd = "svn info \"$path\" --xml";
		toba_manejador_procesos::ejecutar($cmd, $salida, $hubo_error);
				
		if ((trim($hubo_error) != '' ) || trim($salida) == '') {			//Hubo algun tipo de error, puede ser que no se encontro el cliente, no WC, etc.
			return false;
		} elseif (! is_null($salida)) {					
			$xml = simplexml_load_string($salida);	
			return (isset($xml->entry));				//Me fijo si el xml corresponde a una WC
		}
	}

	function cleanup($path)
	{
		$cmd = "svn cleanup \"$path\" ";
		return $this->ejecutar($cmd, false, false);		
	}
	
	function update($path, $revision = 'HEAD')
	{
		$cmd = "svn up \"$path\" -r $revision";
		return $this->ejecutar($cmd);				
	}
	
	function do_switch($url, $path)
	{
		//$url = $this->url_base.'/'.$url;		
		$cmd = "svn switch \"$url\" \"$path\" ";
		return $this->ejecutar($cmd);
	}
	
	function do_merge($url, $path, $desde, $hacia)
	{
		$cmd = "svn merge -r $desde:$hacia \"$url\" \"$path\"";
		return $this->ejecutar($cmd);
	}
	
	function checkout($url, $path)
	{
		//$url = $this->url_base.'/'.$url;
		$cmd = "svn co \"$url\" \"$path\" ";
		return $this->ejecutar($cmd);
	}
	

	function blame($path)
	{
		$cmd = "svn blame \"$path\" ";
		return $this->ejecutar($cmd, false, false);		
	}

	function diff($path)
	{
		$cmd = "svn diff \"$path\" ";
		return $this->ejecutar($cmd, false, false);		
	}	
	
	function revert($path)
	{
		$cmd = "svn revert \"$path\" ";
		return $this->ejecutar($cmd, false, false);		
	}		
	
	function add($path)
	{
		$cmd = "svn add \"$path\" --parents";
		return $this->ejecutar($cmd, false, false);		
	}	
	
	/**
	 * Hace un checkout o lo continua con un update segun se necesite
	 */
	function descargar($url, $path)
	{
		/*$checkout = true;
		if (file_exists($path)) {
			$borrar = false;
			/*if (! $this->es_copia_trabajo($path)) {
				$pregunta = "La carpeta '$path' ya existe, desea eliminarla?";
				$borrar = inst_fact::gtk()->preguntar($pregunta, 'Carpeta existente');
			} else {
				$url_vieja = $this->reducir_url($this->get_url($path));
				$pregunta = "La carpeta '$path' ya contiene una aplicación parcialmente descargada "
							."desde '$url_vieja'.\n\n"
							."¿Desea continuar esta descarga?";
				$continuar = inst_fact::gtk()->preguntar($pregunta, 'Carpeta existente');
				$borrar = !$continuar;
				if ($continuar) {
					$checkout = false;
				}
			}
			if ($borrar) {
				//--- Para no borrarlo directamente, se renombra a .old (si ya existia este se borra)
				if (file_exists($path.'.old')) {
					inst_fact::archivos()->eliminar_directorio($path.'.old');
				}
				if (! rename($path, $path.'.old')) {
					inst_fact::archivos()->eliminar_directorio($path);
				}
			}
		}
		if ($checkout) {*/
			return $this->checkout($url, $path);
		/*} else {
			$this->cleanup($path);
			return $this->update($path);
		}*/
	}
	
	function get_revision_origen($url)
	{
		$cmd = "svn log $url --stop-on-copy --xml -q";
		$archivo = tempnam(TOBA_DIR.'/temp', 'temp_');
		$res = $this->ejecutar($cmd, true, true, true, $archivo);
		$xml = simplexml_load_string(file_get_contents($archivo));
		unlink($archivo);
		if (isset($xml->logentry)) {
			$ultimo = count($xml->logentry) - 1;
			return (string) $xml->logentry[$ultimo]['revision'];
		}		
	}
	
	function existe_url($url)
	{
		$salida = array();
		$valor_retorno = null;		
		$cmd = "svn ls \"$url\"";
		exec($cmd, $salida, $valor_retorno);
		return ($valor_retorno === 0);		
	}
	
	function crear_directorio($url, $mensaje)
	{
		$cmd = "svn mkdir \"$url\"  -m \"$mensaje\"";
		$this->ejecutar($cmd);		
	}
	
	function copy($origen, $destino, $mensaje)
	{
		$mensaje = utf8_encode($mensaje);
		
		//-- Utiliza dos niveles de soporte de creación de directorios intermedios
		$base_destino = dirname($destino);
		if (! $this->existe_url($base_destino)) {
			if (! $this->existe_url(dirname($base_destino))) {
				$this->crear_directorio(dirname($base_destino), $mensaje);
			}
			$this->crear_directorio($base_destino, $mensaje);
		}
				
		$cmd = "svn copy \"$origen\" \"$destino\" -m \"$mensaje\"";
		return $this->ejecutar($cmd);		
	}
		
}

?>
