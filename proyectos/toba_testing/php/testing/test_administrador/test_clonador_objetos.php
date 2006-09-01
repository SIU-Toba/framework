<?php

class test_clonador_objetos extends test_toba
{
	function get_descripcion()
	{
		return "Clonador de Objetos";	
	}
	
	function SetUp()
	{
		abrir_transaccion('instancia');	
	}
	
	function TearDown()
	{
		abortar_transaccion('instancia');	
	}
	
	function probar_objeto($id)
	{
		$nuevo_nombre = "Objeto Clonado";
		$nuevos_datos = array('nombre' => $nuevo_nombre);
		$info = toba_constructor::get_info(array('componente' => $id, 'proyecto' => 'toba_testing'));
		$clon = $info->clonar($nuevos_datos, false);
		//--- Verificación
		$meta_objeto = toba_constructor::get_info( array('proyecto' => $clon['proyecto'],
													'componente'=> $clon['objeto']) );
		$this->assertEqual($nuevo_nombre, $meta_objeto->get_nombre_largo());
		$this->assertTrue(is_numeric($clon['objeto']));
		$this->assertNotEqual($clon['objeto'], $id);		
	}
	
	function test_clonar_datos_relacion()
	{
		$this->probar_objeto("1516");
	}
	
	function test_clonar_datos_tabla()
	{
		$this->probar_objeto("1428");		
	}
	
	function test_clonar_ei_archivos()
	{
		$this->probar_objeto("1668");
	}
	
	function test_clonar_ei_calendario()
	{
		$this->probar_objeto("1672");		
	}
		
	function test_clonar_ei_cuadro()
	{
		$this->probar_objeto("1149");
	}
	
	function test_clonar_ei_filtro()
	{
		$this->probar_objeto("1191");		
	}
	
	function test_clonar_ei_formulario()
	{
		$this->probar_objeto("1635");
	}
	
	function test_clonar_ei_formulario_ml()
	{
		$this->probar_objeto("1322");		
	}
	
	function test_clonar_ei_arbol()
	{
		$this->probar_objeto("1703");		
	}
	
	function test_clonar_ci()
	{
		$this->probar_objeto("1646");
	}
	
	function test_clonar_cn()
	{
		$this->probar_objeto("1000005");		
	}
	
}

?>