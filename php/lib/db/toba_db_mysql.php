<?php
/**
 * Driver de conexión con mysql.
 * @package Fuentes
 * @subpackage Drivers
 */
class toba_db_mysql extends toba_db
{
	function __construct($profile, $usuario, $clave, $base, $puerto, $server, $sslmode='', $cert_path='', $key_path='', $crl_path='', $cacert_path='')
	{
		$this->motor = "mysql";
		parent::__construct($profile, $usuario, $clave, $base, $puerto,  $server, $sslmode, $cert_path, $key_path, $crl_path, $cacert_path);
	}
	
	function get_dsn()
	{
		$puerto = ($this->puerto != '') ? "port={$this->puerto}": '';
		$certs = '';
		if ($this->sslmode != '') {
			$certs = $this->get_config_certificados();
			$certs = (! empty($certs)) ? $this->dsn_parameters_implode(';', $certs) : '';
		}
		return "mysql:host=$this->profile;dbname=$this->base;$puerto;$certs";	
	}
	
	function set_encoding($encoding, $ejecutar = true)
	{
	
	}
	
	function get_config_certificados()
	{
		$certs = array();
		if (! is_null($this->key_path)) {
			$certs[PDO::MYSQL_ATTR_SSL_KEY]    = $this->key_path;
		}
		if (! is_null($this->cert_path)) {
			$certs[PDO::MYSQL_ATTR_SSL_CERT] = $this->cert_path;
		}
		if (! is_null($this->cacert_path)) {
			$certs[PDO::MYSQL_ATTR_SSL_CA] =$this->cacert_path;
		}
		return $certs;
	}
}
?>