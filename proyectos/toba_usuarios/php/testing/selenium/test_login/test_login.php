<?php
class test_login extends caso_base 
{	
	function test_autenticar()
	{
		$this->session = toba_selenium_basics_proyecto::abrir_browser('chrome');		
		toba_selenium_basics_proyecto::login($this->session);
		$titulo = $this->session->getTitle();
		$this->assertTrue($titulo == 'Toba - Usuarios - Mantenimiento de usuarios',"Login was unsuccessful");		
	}

	function test_salir()
	{
		toba_selenium_monje_negro::click_element($this->session, 'id', 'activador_datos_usuario');
		toba_selenium_monje_negro::click_element($this->session, 'id',  'boton_salir');				
		$this->session->switchTo()->alert()->accept();	
		
		// -- hay que procesar el alert
		$url_buscada = toba_selenium_utilidades::get_url_proyecto() . '/aplicacion.php?fs=1';
		$url_actual = $this->session->getCurrentURL();
		$this->assertTrue($url_actual == $url_buscada,"Logout was unsuccessful");
		$this->session->close();		
		unset($this->session);
	}
}
?> 
