<?php
/**
 * Driver de conexión con informix
 * @package Fuentes
 * @subpackage Drivers
 */
class toba_db_informix extends toba_db
{
	protected $id_instancia_server;
	
	
	function __construct($profile, $usuario, $clave, $base, $puerto, $server, $sslmode='', $cert_path='', $key_path='', $crl_path='', $cacert_path='')
	{
		$this->motor = "informix";
		$this->id_instancia_server = $server;
		parent::__construct($profile, $usuario, $clave, $base, $puerto, $server, $sslmode, $cert_path, $key_path, $crl_path, $cacert_path);
	}
	
	/**
	 * Retorna el string de conexion para el motor
	 * @return string
	 */
	function get_dsn()
	{
		$puerto = ($this->puerto != '') ? $this->puerto : '1526';
		$certs = '';
		if ($this->sslmode != '') {
			$protocol ="protocol={$this->sslmode}";
			$certs = $this->get_config_certificados();
			$certs = (! empty($certs)) ? $this->dsn_parameters_implode(';', $certs) : '';
		} else {
			$protocol ='protocol=olsoctcp';
		}
		
		$str_conexion ="informix:host=$this->profile;service=$puerto;database=$this->base;server={$this->id_instancia_server};$protocol;EnableScrollableCursors=1;$certs";
		return $str_conexion;
	}
	
	/**
	 * Ejecuta un BEGIN WORK en la conexión
	 */	
	function abrir_transaccion()
	{
		$sql = 'BEGIN WORK';
		$this->ejecutar($sql);
		//toba::logger()->debug("************ ABRIR transaccion ($this->base@$this->profile) ****************", 'toba');
		$this->log("************ ABRIR transaccion ($this->base@$this->profile) ****************", 'debug', 'toba');
	}
	
	/**
	 * Ejecuta un ROLLBACK WORK en la conexión
	 */		
	function abortar_transaccion()
	{
		$sql = 'ROLLBACK WORK';
		$this->ejecutar($sql);		
		//toba::logger()->debug("************ ABORTAR transaccion ($this->base@$this->profile) ****************", 'toba'); 
		$this->log("************ ABORTAR transaccion ($this->base@$this->profile) ****************", 'debug', 'toba');
	}

	/**
	 * Ejecuta un COMMIT WORK en la conexión
	 */		
	function cerrar_transaccion()
	{
		$sql = "COMMIT WORK";
		$this->ejecutar($sql);		
		///toba::logger()->debug("************ CERRAR transaccion ($this->base@$this->profile) ****************", 'toba'); 
		$this->log("************ CERRAR transaccion ($this->base@$this->profile) ****************", 'debug', 'toba');
	}
	
	/**
	 * Retorna la configuracion de los certificados para una conexion SSL
	 * @return array
	 */
	function get_config_certificados()
	{
		$certs = array();
		if (! is_null($this->key_path)) {
			$certs['SSL_KEYSTORE_STH']    = $this->key_path;
		}
		if (! is_null($this->cert_path)) {
			$certs['SSL_KEYSTORE_FILE'] = $this->cert_path;
		}
		return $certs;
	}
	
	/**
	 * Retorna una ER para quitar comentarios de la SQL
	 */
	function get_separador_comentarios()
	{
		return "/\/\*([^'])*?\*\/|\{([^'])*?\}|(?:-{2,}[^']*?\R)/im";
	}
}
?>
