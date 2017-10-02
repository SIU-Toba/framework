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
	
	/**
	 * Funcion que cifra un texto en base a una clave de instalacion
	 * @param mixed $no_encriptado
	 * @param mixed $clave
	 * @return mixed
	 * @deprecated desde version 3.0.11
	 */
	function cifrar($no_encriptado, $clave="get")
	{
		$cifrado = $this->Encrypt($no_encriptado,$this->clave[$clave]);
		if ($clave == 'get') {
			return urlencode($cifrado);
		} else {
			return $cifrado;	
		}
	}
	
	/**
	 *  Funcion que descifra un texto en base a una clave de instalacion
	 * @param mixed $encriptado
	 * @param mixed $clave
	 * @return mixed
	 * @deprecated desde version 3.0.11
	 */
	function descifrar($encriptado, $clave="get")
	{
		$descifrado = $this->Decrypt($encriptado,$this->clave[$clave]);
		if ($clave == 'get') {
			return urldecode($descifrado);
		} else {
			return $descifrado;	
		}		
	}

	//-------------------------------------------------------------
	// Segun ADOdb: Session Encryption by Ari Kuorikoski <ari.kuorikoski@finebyte.com>
	//-------------------------------------------------------------
	/**
	 * 
	 * @param type $txt
	 * @param type $encrypt_key
	 * @return type
	 * @deprecated desde version 3.0.11
	 */
	function keyED($txt,$encrypt_key)
	{
		$encrypt_key = md5($encrypt_key);
		$ctr=0;
		$tmp = "";
		for ($i=0;$i<strlen($txt);$i++){
				if ($ctr==strlen($encrypt_key)) $ctr=0;
				$tmp.= substr($txt,$i,1) ^ substr($encrypt_key,$ctr,1);
				$ctr++;
		}
		return $tmp;
	}

	/**
	 * Funcion que encripta un texto en base a una clave
	 * @param string $txt
	 * @param mixed $key
	 * @return mixed
	 * @deprecated desde version 3.0.11
	 */
	function Encrypt($txt,$key)
	{
		srand((double)microtime()*1000000);
		$encrypt_key = md5(rand(0,32000));
		$ctr=0;
		$tmp = "";
		for ($i=0;$i<strlen($txt);$i++)
		{
		if ($ctr==strlen($encrypt_key)) $ctr=0;
		$tmp.= substr($encrypt_key,$ctr,1) .
		(substr($txt,$i,1) ^ substr($encrypt_key,$ctr,1));
		$ctr++;
		}
		return base64_encode($this->keyED($tmp,$key));
	}

	/**
	 * Funcion que desencripta un texto en bae a una clave
	 * @param mixed $txt
	 * @param mixed $key
	 * @return mixed
	 * @deprecated desde version 3.0.11
	 */
	function Decrypt($txt,$key)
	{
		$txt = $this->keyED(base64_decode($txt),$key);
		$tmp = "";
		for ($i=0;$i<strlen($txt);$i++){
				$md5 = substr($txt,$i,1);
				$i++;
				$tmp.= (substr($txt,$i,1) ^ $md5);
		}
		return $tmp;
	}

	/**
	 * Funcion que genera una clave aleatoria
	 * @return mixed
	 * @deprecated desde version 3.0.11
	 */
	function RandPass()
	{
		$randomPassword = "";
		srand((double)microtime()*1000000);
		for($i=0;$i<8;$i++)
		{
				$randnumber = rand(48,120);

				while (($randnumber >= 58 && $randnumber <= 64) || ($randnumber >= 91 && $randnumber <= 96))
				{
						$randnumber = rand(48,120);
				}

				$randomPassword .= chr($randnumber);
		}
		return $randomPassword;
	}

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