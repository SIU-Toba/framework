<?php 

class referencia_config {
	
	private static $_config = [
			'logo_nombre' => "",
			'logo_iso' => "",
			'logo_espera' =>  "",
			'logo_login' => "",
			'main_color' => "#890c71",
			'corte-0' => 'rgb(153, 51, 153)',
			'corte-1' => 'rgb(204, 102, 204)',
			'corte-2' => ''
	];
	
	
	static function getLogoNombre(){
		return self::$_config['logo_nombre'];
	}
	
	static function setLogoNombre($value){
		self::$_config['logo_nombre'] = $value;
	}
	
	static function getIsoLogo(){
		return self::$_config['logo_iso'];
	}
	
	static function setIsoLogo($value){
		self::$_config['logo_iso'] = $value;
	}
	
	static function getLogoEspera(){
		return self::$_config['logo_espera'];
	}
	
	static function setLogoEspera($value){
		self::$_config['logo_espera'] = $value;
	}
	
	static function getLogoLogin(){
		return self::$_config['logo_login'];
	}
	
	static function setLogoLogin($value){
		self::$_config['logo_login'] = $value;
	}
	
	static function getMainColor(){
		return self::$_config['main_color'];
	}
	
	static function setMainColor($value){
		self::$_config['main_color'] = $value;
	}
	
	static function getCorteControl0(){
		return self::$_config['corte-0'];
	}
	
	static function setCorteControl0($value){
		self::$_config['corte-0'] = $value;
	}
	
	static function getCorteControl1(){
		return self::$_config['corte-1'];
	}
	
	static function setCorteControl1($value){
		self::$_config['corte-1'] = $value;
	}
	static function getCorteControl2(){
		return self::$_config['corte-2'];
	}
	
	static function setCorteControl2($value){
		self::$_config['corte-2'] = $value;
	}	
}
?>
