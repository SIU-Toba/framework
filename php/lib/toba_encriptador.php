<?php
/**
*	Basado en la encriptacion de ADOdb
* @package Varios
*/
	
use ioncube\phpOpensslCryptor\Cryptor;

class toba_encriptador
{
	static private $instancia;	
	var $clave;
	protected $default_algo = 'aes-256-ctr';
	protected $default_hash = 'sha512';
	protected $default_formato = Cryptor::FORMAT_B64;
	
	private function __construct() 
	{
		$this->clave = toba::instalacion()->get_claves_encriptacion();
	}
	
	static function instancia() 
	{
		if (!isset(self::$instancia)) {
			self::$instancia = new toba_encriptador();	
		}
		return self::$instancia;	
	}	

	//-------------------------------------------------------------
	// Segun ADOdb: Session Encryption by Ari Kuorikoski <ari.kuorikoski@finebyte.com>
	//-------------------------------------------------------------

//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
	/**
	 * Funcion que encripta un dato y lo codifica para concatenarlo a una URL
	 * @param mixed $no_encriptado
	 * @param mixed $clave
	 * @return mixed
	 */
	function cifrar_para_web($no_encriptado, $clave)
	{		
		$cifrado = $this->encriptar($no_encriptado, $clave);
		return urlencode($cifrado);		
	}
	
	/**
	 * Funcion que desencripta un dato codificado para concatenacion en URL.
	 * @param mixed $encriptado
	 * @param mixed $clave
	 * @return mixed
	 */
	function descifrar_para_web($encriptado, $clave)
	{
		return $this->desencriptar(urldecode($encriptado), $clave);		
	}
	
	/**
	 * Encripta un dato  con AES en base a una clave provista y lo devuelve en formato B64
	 * @param mixed $dato
	 * @param mixed $clave
	 * @return mixed
	 */
	function encriptar($dato, $clave)
	{
		if (! extension_loaded('php_openssl') && ! extension_loaded('Openssl')) {
			throw new toba_error('La extensión para openssl no se encuentra cargada, verifique la instalación.');
		}
		$cipher = new Cryptor($this->default_algo, $this->default_hash, $this->default_formato);
		return $cipher->encryptString($dato, $clave);
	}
	
	/**
	 * Desencripta un dato en formato B64 con AES en base a una clave provista
	 * @param mixed $dato
	 * @param mixed $clave
	 * @return mixed
	 */
	function desencriptar($dato, $clave)
	{
		if (! extension_loaded('php_openssl') && ! extension_loaded('Openssl')) {
			throw new toba_error('La extensión para openssl no se encuentra cargada, verifique la instalación.');
		}
		$cipher = new Cryptor($this->default_algo, $this->default_hash, $this->default_formato);
		return $cipher->decryptString($dato, $clave);
	}
	
	//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
	//							FUNCIONES CON UTILIDAD A FUTURO
	//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
	/**
	 * Permite fijar el algoritmo de des/encriptación (default aes-256-ctr)
	 * @param type $algo
	 */
	function set_algoritmo($algo)
	{
		$this->default_algo = $algo;
	}
	
	/**
	 * Permite fijar el formato de retorno/actual del texto encriptado (default base64)
	 * @param type $formato
	 */
	function set_formato_retorno($formato)
	{
		$this->default_formato = $formato;
	}
}
?>