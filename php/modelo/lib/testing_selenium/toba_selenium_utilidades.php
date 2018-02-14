<?php
class toba_selenium_utilidades
{
	static $proyecto_id = 'toba_usuarios';
	static $id_campo_usuario = 'usuario';
	static $id_campo_pwd = 'password';
	static $id_button_login = 'submit';
	static $item_inicio = 1000228;
	
	static function get_url_proyecto()
	{
		$id_proy = self::get_proyecto_id();
		$instalacion = new toba_modelo_instalacion();
		$proyecto = $instalacion->get_instancia('desarrollo')->get_proyecto($id_proy);
		$url = $proyecto->get_url();
		return toba_http::get_url_actual() .$url;
	}
	
	static function get_path_proyecto()
	{
		$id_proy = self::get_proyecto_id();
		$instalacion = new toba_modelo_instalacion();
		$proyecto = $instalacion->get_instancia('desarrollo')->get_proyecto($id_proy);
		return $proyecto->get_dir();
	}
	
	static function get_url_proyecto_inicio()
	{	
		$url =  toba_http::get_url_actual() . 'ai='.  self::get_proyecto_id() . '||'. self::get_id_item_inicio(); 
		return $url;
	}
	
	static function get_url_item($item)
	{
		$url =  self::get_url_proyecto() . '/aplicacion.php?ai='.  self::get_proyecto_id() . '||'. $item; 
		return $url;		
	}
		
	static function get_id_item_inicio()
	{
		return (defined('TEST_ID_PROYECTO')) ? TEST_ID_ITEM_INICIO: self::$item_inicio;
	}
	
	static function get_proyecto_id()
	{
		return (defined('TEST_ID_PROYECTO')) ? TEST_ID_PROYECTO: self::$proyecto_id;
	}
	
	static function get_id_campo_usuario()
	{		
		return (defined('TEST_ID_CAMPO_USUARIO')) ? TEST_ID_CAMPO_USUARIO: self::$id_campo_usuario;
	}
	
	static function get_id_campo_password()
	{
		return (defined('TEST_ID_CAMPO_PWD')) ? TEST_ID_CAMPO_PWD: self::$id_campo_pwd;
	}
	
	static function get_id_submit_login()
	{
		return (defined('TEST_SUBMIT_LOGIN')) ? TEST_SUBMIT_LOGIN: self::$id_button_login;
	}
}
?>
