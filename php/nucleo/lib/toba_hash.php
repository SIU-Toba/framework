<?php

	
class toba_hash
{
	protected $rounds = 10;
	protected $metodo = 'bcrypt';	
	private  $randomState = null;
	private $indicadores_hash = array('$5$', '$6$', '$1$');
	private $indicadores_bcrypt = array('$2y$','$2a$', '$2x$');
	
	
	public final function __construct($metodo=null)
	{
		if (version_compare(PHP_VERSION, '5.3.2') < 0) {
			throw new toba_error('Se requiere PHP 5.3.2 al menos para usar esta clase');
		}
		
		if (! is_null($metodo)) {
			$this->metodo = $metodo;
		}
	}
	
	public function set_ciclos($nro)
	{
		$this->rounds = ($nro > 10) ? $nro : 10;
	}
	
	public function hash($input) 
	{
		$hash = crypt($input, $this->getSalt());
		if(strlen($hash) > 13) {
			return $hash;
		}
		throw new toba_error('Se produjo un error al crear el hash');
	}

	public function get_hash_verificador($input, $existingHash)
	{		
		return  crypt($input, $existingHash);
	}
		
	public function verify($input, $existingHash) 
	{
		$hash = crypt($input, $existingHash);
		return ($hash === $existingHash);
	}
		
	private function getSalt() 
	{		
		switch(strtoupper($this->metodo)) {		
		case 'BCRYPT' : $str_inicial = (version_compare(PHP_VERSION, '5.3.7') < 0) ? "$2a$": "$2y$";
					  $salt = sprintf($str_inicial.'%02d$', $this->rounds);   
					   break;
		
		case 'SHA512': 
					$vueltas = ($this->rounds < 1000) ? $this->rounds * 1000: $this->rounds + 5000;
					$salt = sprintf('$6$rounds=%d$', $this->rounds);
					 break;
				 
		case 'SHA256': 
					$vueltas = ($this->rounds < 1000) ? $this->rounds * 1000: $this->rounds + 5000;
					$salt = sprintf('$5$rounds=%d$', $this->rounds);
					 break;

		case 'MD5':  	$salt = '$1$';
		
		default: 
					toba::logger()->debug("Se suministro un algoritmo no esperado para el hash: {$this->metodo}");
					$salt = '';
		}
		
		$bytes = $this->getRandomBytes(16);
		$salt .= $this->encodeBytes($bytes);
		return $salt;
	}	
	
	//-----------------------------------------------------------------------------------------------------------------------------------------//
	//			FUNCIONES PARA GENERAR SALT (tomadas de bcrypt.php @StackOverflow)
	//-----------------------------------------------------------------------------------------------------------------------------------------//
	
	private function getRandomBytes($count) 
	{
		$bytes = '';
		if(function_exists('openssl_random_pseudo_bytes') &&	(strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN')) { // OpenSSL slow on Win
			$bytes = openssl_random_pseudo_bytes($count);
		}
		if($bytes === '' && is_readable('/dev/urandom') && ($hRand = @fopen('/dev/urandom', 'rb')) !== FALSE) {
			$bytes = fread($hRand, $count);
			fclose($hRand);
		}
		
		if(strlen($bytes) < $count) {
			$bytes = '';
			if($this->randomState === null) {
				$this->randomState = microtime();
				if(function_exists('getmypid')) {
					$this->randomState .= getmypid();
				}
			}

			for($i = 0; $i < $count; $i += 16) {
				$this->randomState = md5(microtime() . $this->randomState);
				if (PHP_VERSION >= '5') {
					$bytes .= md5($this->randomState, true);
				} else {
					$bytes .= pack('H*', md5($this->randomState));
				}
			}

			$bytes = substr($bytes, 0, $count);
		}
		return $bytes;
	}

	private function encodeBytes($input) {
		// The following is code from the PHP Password Hashing Framework
		$itoa64 = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

		$output = '';
		$i = 0;
		do {
			$c1 = ord($input[$i++]);
			$output .= $itoa64[$c1 >> 2];
			$c1 = ($c1 & 0x03) << 4;
			if ($i >= 16) {
				$output .= $itoa64[$c1];
				break;
			}

			$c2 = ord($input[$i++]);
			$c1 |= $c2 >> 4;
			$output .= $itoa64[$c1];
			$c1 = ($c2 & 0x0f) << 2;

			$c2 = ord($input[$i++]);
			$c1 |= $c2 >> 6;
			$output .= $itoa64[$c1];
			$output .= $itoa64[$c2 & 0x3f];
		} while (1);
		return $output;
	}
}
?>
