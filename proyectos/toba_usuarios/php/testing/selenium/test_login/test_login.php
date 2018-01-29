<?php
class test_login extends caso_base 
{	
	function test_autenticar()
	{
		$this->session = basics_proyecto::abrir_browser('chrome');		
		basics_proyecto::login($this->session);
		$titulo = $this->session->getTitle();
		$this->assertTrue($titulo == 'Toba - Usuarios - Mantenimiento de usuarios',"Login was unsuccessful");		
	}

	function test_salir()
	{
		Titiritero::click_element($this->session, 'id', 'activador_datos_usuario');
		Titiritero::click_element($this->session, 'id',  'boton_salir');				
		$this->session->switchTo()->alert()->accept();	
		
		// -- hay que procesar el alert
		$url_buscada = utilidades_testing::get_url_proyecto() . '/aplicacion.php?fs=1';
		$url_actual = $this->session->getCurrentURL();
		$this->assertTrue($url_actual == $url_buscada,"Logout was unsuccessful");
		$this->session->close();		
		unset($this->session);
	}
}
?> 
