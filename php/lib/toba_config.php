<?php
class toba_config
{
	protected $basic_files = array( 'instalacion' => 'instalacion.ini' ,
                                'fuentes' => array('bases' => 'bases.ini', 'usuarios' => 'conexiones.ini') ,
                                'rdi' => 'rdi.ini',
                                'idp' => array('cas' => 'cas.ini' , 'openid' => 'openid.ini' ,  'saml' =>'saml.ini', 'onelogin' => 'saml_onelogin.ini', 'ldap' => 'ldap.ini'),
                                'smtp' => 'smtp.ini',
                                'web_server' => 'web_server.ini'
                                );
	protected $config_files = array();
	protected $config_values = array();
	
	static protected $pattern_gral = '~(\$env\()(.*?)(\)\$)~';

	function __construct()
	{
	}

	/**
	 * Dispara la carga de las configuraciones desde archivos
	 */
	function load()
	{
		$this->reset();
		$this->load_basics();
		$this->load_manual_config();
		/*echo '<pre>';
		var_dump($this->config_values);*/
	}

	/**
	 * Agrega un path a un archivo de configuración a ser cargado bajo un indice especifico
	 * @param string $index
	 * @param path $file
	 */
	function add_config_file($index, $file)
	{
		if (file_exists($file)) {
			$this->config_files[$index] = realpath($file);
		}
	}

	/**
	 * Resetea los valores de configuración cargados
	 */
	function reset()
	{
		if (isset($this->config_values) && ! empty($this->config_values)) {
			unset($this->config_values);
		}
		$this->config_values = array();
	}

	/**
	 * Retorna un valor de configuracion especifico
	 * @param string $seccion
	 * @param string $subseccion
	 * @param string $parametro
	 * @return mixed
	 */
	function get_parametro($seccion=null, $subseccion=null, $parametro)
	{
		if (is_null($seccion)) {
			$seccion = 'general';
		}

		if (isset($this->config_values[$seccion])) {
			if (! is_null($subseccion) && isset($this->config_values[$seccion][$subseccion][$parametro])) {
				return $this->config_values[$seccion][$subseccion][$parametro];
			} elseif (isset($this->config_values[$seccion][$parametro]) ) {
				return $this->config_values[$seccion][$parametro];
			}
		}
		return null;
	}

	/**
	 * Devuelve una subseccion completa de configuración
	 * @param string $seccion
	 * @param string $subseccion
	 * @return mixed
	 */
	function get_subseccion($seccion, $subseccion)
	{
		return $this->get_parametro($seccion, null, $subseccion);
	}

	/**
	 * Devuelve una seccion completa de configuración
	 * @param string $seccion
	 * @return mixed
	 */
	function get_seccion($seccion)
	{
		return (isset($this->config_values[$seccion])) ? $this->config_values[$seccion] : null;
	}

	/**
	 * Define si existe un valor para la configuracion especifica
	 * @param string $seccion
	 * @param string $subseccion
	 * @param string $parametro
	 * @return boolean
	 */
	function existe_valor($seccion=null, $subseccion=null, $parametro)
	{
		return $this->get_parametro($seccion, $subseccion, $parametro) != null;
	}

	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
	/**
	 * Carga los archivos basicos de configuración de Toba
	 */
	protected function load_basics()
	{
		$dir_start = toba_nucleo::toba_instalacion_dir();			//Reemplaza a toba::instalacion()->get_path_carpeta_instalacion() para no tener un ciclo que deja mal parada la config de instalacion
		foreach($this->basic_files as $index => $ini) {
			if (! is_array($ini))  {
				$filename = $dir_start .'/'.$ini;
				if (file_exists($filename)) {
					$contenido = $this->parse_file_content($filename);
					$this->add_config($index, parse_ini_string($contenido, true));
				}
			} else {
				$aux = array();
				foreach($ini as $subindex => $ini_name) {
					$filename = $dir_start .'/'.$ini_name;
					if (file_exists($filename)) {
						$contenido = $this->parse_file_content($filename);
						$aux[$subindex] = $this->parse_array_values(parse_ini_string($contenido, true));
					}
				}
				$this->add_config($index, $aux);
			}
		}
	}

	/**
	 * Carga los archivos de configuración que se agregaron explicitamente
	 */
	protected function load_manual_config()
	{
		foreach($this->config_files as $index => $ini) {
			if (file_exists($ini)) {
				$contenido = $this->parse_file_content($ini);
				$this->add_config($index, parse_ini_string($contenido, true));
			}
		}
	}

	/**
	 * Agrega un conjunto de valores bajo un indice dado
	 * @param sting $index
	 * @param mixed $value_pairs
	 */
	protected function add_config($index, $value_pairs)
	{
		if (false !== $value_pairs) {
			foreach($value_pairs as $key => $valores) {
				$this->config_values[$index][$key] = $this->parse_array_values($valores);
			}
		}
	}

	/**
	 * Recorre un arreglo de valores parseados
	 * @param array $arreglo
	 * @return array
	 */
	protected function parse_array_values($arreglo)
	{
		$datos = array();
		if (! is_array($arreglo)) {
			$datos = $arreglo;
		} elseif (! empty($arreglo)) {
			foreach ($arreglo as $klave => $valor) {
				$datos[$klave] = (is_array($valor)) ? $this->parse_array_values($valor) : $valor;
			}
		}
		return $datos;
	}

	/**
	 * Lee el archivo especificado y convierte todas las referencias a variables de entorno por sus valores
	 * @param string $filename
	 * @return string
	 */
	protected function parse_file_content($filename)
	{
		$content = file_get_contents($filename);
		$final = preg_replace_callback(self::$pattern_gral, 
										function($match) { //$match = array(fullmatch, '$env(', VAR_NAME, ')$');
											if (count($match) == 4) { 
												 return (! is_null($match[2])) ? getenv($match[2]): false;
											}
											return false;
										}, 
										$content);
		return $final;
	}
}
?>