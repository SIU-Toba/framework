<?php
class test_usuarios extends caso_base 
{		
	function test_alta()
	{
		$session = $this->cargar_operacion_usuarios();
		Titiritero::click_element($session, 'id', 'ci_2190_agregar');
		$session->wait(1);
		
		Titiritero::setear_campo($session, 'input', 'id', 'ef_form_2185_basicausuario', 'catman');
		Titiritero::setear_campo($session, 'input', 'id', 'ef_form_2185_basicanombre', 'Miauricio The Cat');
		Titiritero::setear_campo($session, 'input', 'id', 'ef_form_2185_basicaclave', 'Kretinanovolvema5');
		Titiritero::setear_campo($session, 'input', 'id', 'ef_form_2185_basicaclave_test', 'Kretinanovolvema5');
		
		Titiritero::click_element($session, 'id', 'ci_2190_guardar');
		$session->close();
	}

	function test_filtrar()
	{
		$session = $this->cargar_operacion_usuarios();		
		Titiritero::setear_campo($session, 'input', 'id', 'ef_form_2189_filtrousuario', 'toba');
		Titiritero::click_element($session, 'id', 'form_2189_filtro_filtrar');
		
		$session->wait(2);
		$filas = Titiritero::get_elementos($session, 'css', 'button.ei-boton-fila');
		$this->assertTrue(count($filas) === 1);		
		$session->close();
	}

	function test_pregunta_secreta()
	{
		$session = $this->cargar_operacion_usuarios();
		Titiritero::setear_campo($session, 'input', 'id', 'ef_form_2189_filtrousuario', 'catman');
		Titiritero::click_element($session, 'id', 'form_2189_filtro_filtrar');
		$session->wait(2);
		
		Titiritero::click_element($session, 'id', 'cuadro_2181_cuadro0_seleccion');		
		$session->wait(2);
		$usr_id = current(Titiritero::get_elementos($session, 'id', 'ef_form_2185_basicausuario'))->getAttribute('value');
		
		$this->assertTrue($usr_id == 'catman');	
		Titiritero::click_element($session, 'id', 'js_form_33000065_form_pregunta_secreta_agregar');
		$session->wait(1);
		
		Titiritero::setear_campo($session, 'input', 'id', '156_ef_form_33000065_form_pregunta_secretapregunta', 'preguntonta');
		Titiritero::setear_campo($session, 'input', 'id', '156_ef_form_33000065_form_pregunta_secretarespuesta', 'respuestonta');		
		Titiritero::click_element($session, 'id', 'ci_2190_guardar');
		$session->close();
	}

	function test_permisos_y_perfiles()
	{
		$session = $this->cargar_operacion_usuarios();
		Titiritero::setear_campo($session, 'input', 'id', 'ef_form_2189_filtrousuario', 'catman');
		Titiritero::click_element($session, 'id', 'form_2189_filtro_filtrar');
		$session->wait(2);
		
		Titiritero::click_element($session, 'id', 'cuadro_2181_cuadro0_seleccion');
		$session->wait(2);
		
		$usr_id = current(Titiritero::get_elementos($session, 'id', 'ef_form_2185_basicausuario'))->getAttribute('value');
		$this->assertTrue($usr_id == 'catman');
		Titiritero::click_element($session, 'id', 'ci_2188_editor_cambiar_tab_proyecto');
		$session->wait(2);
		
		$botones = Titiritero::get_elementos($session, 'xpath', '//button'); //*[@id="cuadro_2186_cuadro_proyectos1_seleccion"] //*[@id="cuadro_2186_cuadro_proyectos0_seleccion"]
		var_dump($botones[0]->getText());
		die;
		$boton[0]->click();
		$session->wait(2);
		
		$selects = Titiritero::get_elementos($session, 'id', 'ef_form_2187_form_proyectosusuario_grupo_acc');
		foreach($selects as $combo) {
			$combo->selectByIndex(1);
		}
		Titiritero::click_element($session, 'id', 'form_2187_form_proyectos_modificacion');
		Titiritero::click_element($session, 'id', 'ci_2190_guardar');
		$session->close();
	}
	
	function test_eliminar_usuario()
	{
		$session = $this->cargar_operacion_usuarios();
		Titiritero::setear_campo($session, 'input', 'id', 'ef_form_2189_filtrousuario', 'catman');
		Titiritero::click_element($session, 'id', 'form_2189_filtro_filtrar');
		$session->wait(2);
		
		Titiritero::click_element($session, 'id', 'cuadro_2181_cuadro0_seleccion');
		$session->wait(3);
		
		$usr_id = Titiritero::get_elementos($session, 'id', 'ef_form_2185_basicausuario')->getText();
		$this->assertTrue($usr_id == 'catman');
		
		Titiritero::click_element($session, 'id', 'ci_2190_eliminar');
		$session->close();
	}
}
 ?>
