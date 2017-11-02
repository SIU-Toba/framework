<?php
class toba_config
{
	protected $basic_files = array('instalacion.ini', 'bases.ini', 'cas.ini', 'openid.ini',  'rdi.ini', 'saml.ini', 'saml_onelogin.ini', 'smtp.ini' );
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
	 * @param string $nombre
	 * @return mixed
	 */
	function get_parametro($seccion=null, $nombre)
	{
		if (is_null($seccion)) {
			$seccion = 'general';
		}
		
		if (isset($this->config_values[$seccion]) && isset($this->config_values[$seccion][$parametro])) {
			return $this->config_values[$seccion][$parametro];
		}
		return null;
	}
	
	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
	/**
	 * Carga los archivos basicos de configuración de Toba
	 */
	protected function load_basics()
	{
		$dir_start = toba::instalacion()->get_path_carpeta_instalacion();
		foreach($this->basic_files as $ini) {			
			$filename = $dir_start .'/'.$ini;
			if (file_exists($filename)) {
				$index = basename($ini, '.ini');
				$this->add_config($index, parse_ini_file($filename, true));
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
				$this->config_values[$key] = $this->parse_array_values($valores);
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
		$matches=array();
		if (substr($valor, 0, 4) == '$env' && substr($valor,-1) == '$') {
			if (preg_match('/^\$env\((.*)\)\$$/', $valor, $matches) !== false) {
				$var_name = (is_array($matches) && ! empty($matches)) ? $matches[1]: '';
				return self::load_from_env($var_name);			
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
	protected function load_from_env($name)
	{
		if (! is_null($name)) {
			return getenv($name);
		}
		return false;
	}	
}
?>