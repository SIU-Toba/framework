<?php
use Facebook\WebDriver\WebDriverBy;	
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverSelect;

class Titiritero
{
	static $selectores = array(
		'id' => 'Facebook\WebDriver\WebDriverBy::id',
		'css' => 'Facebook\WebDriver\WebDriverBy::cssSelector',
		'xpath' => 'Facebook\WebDriver\WebDriverBy::xpath',
		'name' => 'Facebook\WebDriver\WebDriverBy::name',
		'class' => 'Facebook\WebDriver\WebDriverBy::className'
	);		
	
	static function setear_campo($session1, $tipo, $selector, $id, $texto = null)
	{
		$par = self::get_selector($selector);
		switch ($tipo) {
			case 'input':
				self::typear_texto($session1, $par($id), $texto);
				break;			
			case 'radio':
				self::push_click($session1, $par($id));
				break;
			case 'boton':
				self::push_click($session1, $par($id));
				break;
			case 'combo':
				if ($selector == 'css') {
					$ident = "select[id=".$id."] option[value='".$texto."']";			//Doesn't seems right
					self::typear_texto($session1, $par($ident), "\uE004");
				} elseif ($selector == 'xpath') {
					self::typear_texto($session1, $par($id), "\uE004");
				} 
				break;
			case 'combo_editable':
				if (is_null($texto)) {
					self::clear_busqueda($session1, $par($id));
				} else {
					self::set_busqueda($session1, $par($id), $texto);
				}
				break;
		}
	}

	static function click_element($session1, $selector, $value)
	{
		if ($selector == 'className') {
			throw new Exception('Selector invalido' );
		}
		$par = self::get_selector($selector);
		self::push_click($session1, $par($value));
		sleep(3);
	}
	
	static function setear_opcion_combo($session1, $selector, $ident, $id_opcion=null, $texto_opcion=null, $deseleccionar=false)
	{
		//Busco el campo
		$par = self::get_selector($selector);
		$campo = $session1->findElement($par($ident));
		
		//Lo transformo a Select para poder operar especificamente
		$combo_util = new WebDriverSelect($campo);
		$es_por_texto = (is_null($id_opcion) && !is_null($texto_opcion));
		$es_por_index = (is_null($texto_opcion) && !is_null($id_opcion));
		$permite_multiple = $combo_util->isMultiple();		
		switch (true) {
			case $es_por_index:
				if ($permite_multiple) {
					$id_fijar = (! is_array($id_opcion)) ? array($id_opcion) : $id_opcion;
				} else {
					$id_fijar = (! is_array($id_opcion)) ?  array($id_opcion): array(current($id_opcion));
				}				
				foreach($id_fijar as $indice) {
					($deseleccionar) ? $combo_util->deselectByIndex($indice) : $combo_util->selectByIndex($indice);
				}
				break;				
			case $es_por_texto:				
				if ($permite_multiple) {
					$id_fijar = (! is_array($texto_opcion)) ? array($texto_opcion) : $texto_opcion;
				} else {
					$id_fijar = (! is_array($texto_opcion)) ?  array($texto_opcion): array(current($texto_opcion));
				}				
				foreach($id_fijar as $texto) {
					($deseleccionar) ? $combo_util->deselectByText($texto) : $combo_util->selectByText($texto);
				}
				break;				
			default:
				throw new Exception('No se especifico valor para el combo' );
		}		
	}
	
	//------------------------------------------------------------------------------------------------------------//
	//						AUXILIARES							     //
	//------------------------------------------------------------------------------------------------------------//
	static function get_selector($selector)
	{
		if (isset(self::$selectores[$selector])) {
			return self::$selectores[$selector];
		}
		throw new Exception('No se indico un selector valido');
	}
	
	static function typear_texto($session, $elemento, $texto)
	{
		$campo = $session->findElement($elemento)->click();			
		$campo->clear();
		$session->getKeyboard()->sendKeys($texto);
		return $campo;
	}
	
	static function push_click($session, $elemento)
	{
		$campo = $session->findElement($elemento);
		$campo->click();
		return $campo;
	}
	
	static function set_busqueda($session, $elemento, $texto)
	{
		self::typear_texto($session, $elemento, $texto);
		sleep(3);
		$session->getKeyboard()->sendKeys("\uE015");
		$session->getKeyboard()->sendKeys("\uE004");
	}
	
	static function clear_busqueda($session, $elemento)
	{
		$campo = self::push_click($session, $elemento);
		$campo->clear();
	}
	
	static function get_elementos($session, $selector, $value)
	{
		$par = self::get_selector($selector);
		return $session->findElements($par($value));
	}	
}
?>
