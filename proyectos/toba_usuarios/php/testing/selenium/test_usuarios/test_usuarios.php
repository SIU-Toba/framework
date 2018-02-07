<?php
	

class test_usuarios extends caso_base 
{		
	function test_alta()
	{
		$session = $this->cargar_operacion_usuarios();
		toba_selenium_monje_negro::click_element($session, 'id', 'ci_2190_agregar');
		$session->wait(1);
		
		toba_selenium_monje_negro::setear_campo($session, 'input', 'id', 'ef_form_2185_basicausuario', 'catman');
		toba_selenium_monje_negro::setear_campo($session, 'input', 'id', 'ef_form_2185_basicanombre', 'Miauricio The Cat');
		toba_selenium_monje_negro::setear_campo($session, 'input', 'id', 'ef_form_2185_basicaclave', 'Kretinanovolvema5');
		toba_selenium_monje_negro::setear_campo($session, 'input', 'id', 'ef_form_2185_basicaclave_test', 'Kretinanovolvema5');
		
		toba_selenium_monje_negro::click_element($session, 'id', 'ci_2190_guardar');
		$session->close();
	}

	function test_filtrar()
	{
		$session = $this->cargar_operacion_usuarios();		
		toba_selenium_monje_negro::setear_campo($session, 'input', 'id', 'ef_form_2189_filtrousuario', 'toba');
		toba_selenium_monje_negro::click_element($session, 'id', 'form_2189_filtro_filtrar');
		
		$session->wait(2);
		$filas = toba_selenium_monje_negro::get_elementos($session, 'css', 'button.ei-boton-fila');
		$this->assertTrue(count($filas) === 1);		
		$session->close();
	}

	function test_pregunta_secreta()
	{
		$session = $this->cargar_operacion_usuarios();
		toba_selenium_monje_negro::setear_campo($session, 'input', 'id', 'ef_form_2189_filtrousuario', 'catman');
		toba_selenium_monje_negro::click_element($session, 'id', 'form_2189_filtro_filtrar');
		$session->wait(2);
		
		toba_selenium_monje_negro::click_element($session, 'id', 'cuadro_2181_cuadro0_seleccion');		
		$session->wait(2);
		$usr_id = current(toba_selenium_monje_negro::get_elementos($session, 'id', 'ef_form_2185_basicausuario'))->getAttribute('value');
		
		$this->assertTrue($usr_id == 'catman');	
		toba_selenium_monje_negro::click_element($session, 'id', 'js_form_33000065_form_pregunta_secreta_agregar');
		$session->wait(1);
		
		toba_selenium_monje_negro::setear_campo($session, 'input', 'id', '156_ef_form_33000065_form_pregunta_secretapregunta', 'preguntonta');
		toba_selenium_monje_negro::setear_campo($session, 'input', 'id', '156_ef_form_33000065_form_pregunta_secretarespuesta', 'respuestonta');		
		toba_selenium_monje_negro::click_element($session, 'id', 'ci_2190_guardar');
		$session->close();
	}

	function test_permisos_y_perfiles()
	{
		$session = $this->cargar_operacion_usuarios();
		toba_selenium_monje_negro::setear_campo($session, 'input', 'id', 'ef_form_2189_filtrousuario', 'catman');
		toba_selenium_monje_negro::click_element($session, 'id', 'form_2189_filtro_filtrar');
		$session->wait(2);
		
		toba_selenium_monje_negro::click_element($session, 'id', 'cuadro_2181_cuadro0_seleccion');
		$session->wait(2);
		
		$usr_id = current(toba_selenium_monje_negro::get_elementos($session, 'id', 'ef_form_2185_basicausuario'))->getAttribute('value');
		$this->assertTrue($usr_id == 'catman');
		toba_selenium_monje_negro::click_element($session, 'id', 'ci_2188_editor_cambiar_tab_proyecto');
		$session->wait(2);
		
		$botones = toba_selenium_monje_negro::get_elementos($session, 'xpath', "//button[contains(@name, '_seleccion')]"); //*[@id="cuadro_2186_cuadro_proyectos1_seleccion"] 
		$botones[1]->click();
		$session->wait(2);
		
		toba_selenium_monje_negro::setear_opcion_combo($session, 'xpath', '//select[contains(@name, "usuario_grupo_acc_izq")]', 1);
		toba_selenium_monje_negro::click_element($session, 'xpath', '//select[contains(@name, "usuario_grupo_acc_izq")]//following::img[1]');		
		$session->wait(1);
		toba_selenium_monje_negro::click_element($session, 'id', 'form_2187_form_proyectos_modificacion');
		$session->wait(2);
		toba_selenium_monje_negro::click_element($session, 'id', 'ci_2190_guardar');
		$session->close();
	}
	
	function test_agrego_perfiles()
	{
		$session = $this->cargar_operacion_usuarios();
		toba_selenium_monje_negro::setear_campo($session, 'input', 'id', 'ef_form_2189_filtrousuario', 'catman');
		toba_selenium_monje_negro::click_element($session, 'id', 'form_2189_filtro_filtrar');
		$session->wait(2);
		
		toba_selenium_monje_negro::click_element($session, 'id', 'cuadro_2181_cuadro0_seleccion');
		$session->wait(2);
		
		$usr_id = current(toba_selenium_monje_negro::get_elementos($session, 'id', 'ef_form_2185_basicausuario'))->getAttribute('value');
		$this->assertTrue($usr_id == 'catman');
		toba_selenium_monje_negro::click_element($session, 'id', 'ci_2188_editor_cambiar_tab_proyecto');
		$session->wait(2);
		
		$botones = toba_selenium_monje_negro::get_elementos($session, 'xpath', "//button[contains(@name, '_seleccion')]"); 
		$botones[1]->click();
		$session->wait(2);
		
		toba_selenium_monje_negro::setear_opcion_combo($session, 'xpath', '//select[contains(@name, "usuario_perfil_datos")]', 0);
		toba_selenium_monje_negro::setear_opcion_combo($session, 'xpath', '//select[contains(@name, "usuario_grupo_acc_izq")]', 0);		
		toba_selenium_monje_negro::click_element($session, 'xpath', '//select[contains(@name, "usuario_grupo_acc_izq")]//following::img[1]');		
		$session->wait(1);
		toba_selenium_monje_negro::click_element($session, 'id', 'form_2187_form_proyectos_modificacion');
		$session->wait(2);
		toba_selenium_monje_negro::click_element($session, 'id', 'ci_2190_guardar');
		$session->close();
	}
	
	function test_quito_perfiles_y_permisos()
	{
		$session = $this->cargar_operacion_usuarios();
		toba_selenium_monje_negro::setear_campo($session, 'input', 'id', 'ef_form_2189_filtrousuario', 'catman');
		toba_selenium_monje_negro::click_element($session, 'id', 'form_2189_filtro_filtrar');
		$session->wait(2);
		
		toba_selenium_monje_negro::click_element($session, 'id', 'cuadro_2181_cuadro0_seleccion');
		$session->wait(2);
		
		$usr_id = current(toba_selenium_monje_negro::get_elementos($session, 'id', 'ef_form_2185_basicausuario'))->getAttribute('value');
		$this->assertTrue($usr_id == 'catman');
		toba_selenium_monje_negro::click_element($session, 'id', 'ci_2188_editor_cambiar_tab_proyecto');
		$session->wait(2);
		
		$botones = toba_selenium_monje_negro::get_elementos($session, 'xpath', "//button[contains(@name, '_seleccion')]"); 
		$botones[1]->click();
		$session->wait(2);
				
		toba_selenium_monje_negro::click_element($session, 'id', 'form_2187_form_proyectos_baja');
		$session->switchTo()->alert()->accept();
		$session->wait(2);
		toba_selenium_monje_negro::click_element($session, 'id', 'ci_2190_guardar');
		$session->close();
	}
	
	function test_eliminar_usuario()
	{
		$session = $this->cargar_operacion_usuarios();
		toba_selenium_monje_negro::setear_campo($session, 'input', 'id', 'ef_form_2189_filtrousuario', 'catman');
		toba_selenium_monje_negro::click_element($session, 'id', 'form_2189_filtro_filtrar');
		$session->wait(2);
		
		toba_selenium_monje_negro::click_element($session, 'id', 'cuadro_2181_cuadro0_seleccion');
		$session->wait(3);
		
		$usr_id = current(toba_selenium_monje_negro::get_elementos($session, 'id', 'ef_form_2185_basicausuario'))->getAttribute('value');
		$this->assertTrue($usr_id == 'catman');
		
		toba_selenium_monje_negro::click_element($session, 'id', 'ci_2190_eliminar');
		$session->switchTo()->alert()->accept();
		$session->wait(2);
		$session->close();
	}
}
 ?>
