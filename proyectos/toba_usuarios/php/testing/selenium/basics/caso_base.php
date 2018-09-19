<?php

class caso_base  extends toba_test_selenium
{
	protected $session;
	
	function cargar_operacion_usuarios()
	{
		$this->session = toba_selenium_basics_proyecto::abrir_browser('chrome');		
		toba_selenium_basics_proyecto::login($this->session);
		$url = toba_selenium_utilidades::get_url_item(3432);
		$this->session->get($url);
		$titulo = $this->session->getTitle();
		$this->assertTrue($titulo == 'Toba - Usuarios - Mantenimiento de usuarios',"Login was unsuccessful");	
		return $this->session;
	}
	
}
?>
