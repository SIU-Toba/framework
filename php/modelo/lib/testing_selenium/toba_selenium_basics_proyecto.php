<?php

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

class toba_selenium_basics_proyecto 
{
	static function abrir_browser($browser,$url = null)
	{
		if(is_null($url) || trim($url) == '')		{
			$url = toba_selenium_utilidades::get_url_proyecto();
		}
		$wd_host = 'http://localhost:4444/wd/hub';
		$path_proyecto = toba_selenium_utilidades::get_path_proyecto();		
		switch($browser) {
			case 'chrome':
				$capabilities = DesiredCapabilities::chrome();
				break;
			default:
				$capabilities = DesiredCapabilities::firefox();
				$capabilities->setCapability('acceptSslCerts', false);
				$capabilities->setCapability('profile', "$path_proyecto/php/testing/selenium/lib/perfil_firefox.b64");
				break;
		}
		$web_driver = RemoteWebDriver::create($wd_host, $capabilities);
		$web_driver->get($url);
		return $web_driver;
	}


	static function login($session1,$usuario = 'toba', $password='toba')
	{
		toba_selenium_monje_negro::setear_campo($session1, 'input', 'id', toba_selenium_utilidades::get_id_campo_usuario(), $usuario);
		toba_selenium_monje_negro::setear_campo($session1, 'input', 'id', toba_selenium_utilidades::get_id_campo_password(), $password);
		toba_selenium_monje_negro::click_element($session1, 'id',  toba_selenium_utilidades::get_id_submit_login());
	}

	static function inicio($session1)
	{
		$url = toba_selenium_utilidades::get_url_proyecto_inicio();
		$session1->get($url); 
	}

	/*static function menu_operacion($session1, $path)
	{
		$boton_menu = $session1->findElement(Facebook\WebDriver\WebDriverBy::xpath($path));
		$boton_menu->click();
	}*/

	static function salida_cabecera($texto)
	{
		echo "<ul style=\"list-style-type:circle\">";
		echo "<li> $texto " ;
	}

	static function salida_cuerpo($texto)
	{
		echo "<font color =\"green\">OK!  </font></li>";  
		echo "<li> $texto" ;
	}

	static function salida_pie()
	{
		echo "<font color =\"green\">OK!  </font></li>";
		echo "</ul>";		
	}
}
?>
