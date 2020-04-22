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
					$this->add_config($index, parse_ini_file($filename, true));
				}
			} else {
				$aux = array();
				foreach($ini as $subindex => $ini_name) {
					$filename = $dir_start .'/'.$ini_name;
					if (file_exists($filename)) {
						$aux[$subindex] = $this->parse_array_values(parse_ini_file($filename, true));
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
				$this->add_config($index, parse_ini_file($ini, true));
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
		if (! is_array($value_pairs)) {
			$this->config_values[$index]['general'] = $this->parse_val($value_pairs);
		} else {
			foreach($value_pairs as $key => $valores) {
				$this->config_values[$index][$key] = $this->parse_array_values($valores);
			}
		}
	}

	/**
	 * Recorre un arreglo parseando sus valores
	 * @param array $arreglo
	 * @return array
	 */
	protected function parse_array_values($arreglo)
	{
		$datos = array();
		if (! is_array($arreglo)) {
			$datos = $this->parse_val($arreglo);
		} elseif (! empty($arreglo)) {
			foreach ($arreglo as $klave => $valor) {
				$datos[$klave] = (is_array($valor)) ? $this->parse_array_values($valor) : $this->parse_val($valor);
			}
		}
		return $datos;
	}

	/**
	 * Parsea un valor para verificar si hay un pedido de carga desde el entorno.
	 * @param mixed $valor
	 * @return mixed
	 */
	protected function parse_val($valor)
	{
		$matches = array();
		if (substr($valor, 0, 4) == '$env' && substr($valor,-1) == '$') {
			if (preg_match_all('/(?<=\$env\().*?(?=\)\$)/', $valor, $matches) !== false) {
				//Recupero los valores para las env-vars encontradas (si existe mas de una)
				$recuperados = array_map(array('toba_config', 'load_from_env'), current($matches));
				
				//Reemplazo los valores encontrados
				$valor = preg_replace_callback('~\$env\(.*?\)\$~', 
						function() use (&$recuperados) {
							return array_shift($recuperados);
						} , $valor);
						
				//Si no estallo todo lo devuelvo
				return (null !== $valor) ? $valor: false;
			}
		} else {
			return $valor;
		}
	}

	/**
	 * Levanta un valor desde el entorno devuelve false si no existe o el nombre viene null
	 * @param string $name
	 * @return mixed
	 */
	static protected function load_from_env($name)
	{
		if (! is_null($name)) {
			return getenv($name);
		}
		return false;
	}
}
?>