<?php

class test_clonador_items extends test_toba
{
	function get_descripcion()
	{
		return "Clonador de Items";	
	}
	
	function SetUp()
	{
		abrir_transaccion('instancia');	
	}
	
	function TearDown()
	{
		abortar_transaccion('instancia');	
	}
	
	function probar_item($id, $anexo, $clonar_archivos=false)
	{
		$info = toba_constructor::get_info(array('componente' => $id, 'proyecto' => 'toba_testing'), 'toba_item');
		$clon = $info->clonar($anexo, $clonar_archivos, false);
		$this->assertTrue(is_numeric($clon['item']));
		$this->assertNotEqual($clon['item'], $id);		
		
		//--- Verificación
		return toba_constructor::get_info( array('proyecto' => $clon['proyecto'],
													'componente'=> $clon['item']), 'toba_item' );
	}

	function test_item_vacio()
	{
		$anexo = "Clon - ";
		$nuevos_datos = array('anexo_nombre' => $anexo);
		$meta_item = $this->probar_item('1000022', $nuevos_datos);
		$this->assertEqual($anexo."Clonador - Item Vacio", $meta_item->get_nombre_largo());
	}
	
	function test_item_con_dependencias_sin_subclases()
	{
		$anexo= "Clon - ";
		$nuevos_datos = array('anexo_nombre' => $anexo);
		$meta_item = $this->probar_item('1000034', $nuevos_datos);
		$this->assertEqual($anexo."Clonador - Item con dependencias", $meta_item->get_nombre());
		
		//--- CI
		$ci = $meta_item->get_hijos();
		$this->assertEqual(count($ci), 1);
		$ci = current($ci);
		$this->assertEqual($ci->get_nombre(), $anexo."Clonador - Item con dependencias");
		$this->assertEqual($ci->get_subclase_archivo(), 'p_acciones/clonador/subclase_ci.php');
		
		//--- Pantallas
		$pantallas = $ci->get_hijos();
		$this->assertEqual(count($pantallas), 2);
		
		//--- Form
		$form = $pantallas[0]->get_hijos();
		$this->assertEqual(count($form), 1);
		$form = current($form);
		$this->assertEqual($form->get_nombre(), $anexo."Clonador - Item con dependencias - pant1 - form1");
		$this->assertEqual($form->get_subclase_archivo(), 'p_acciones/clonador/subclase_form.php');		
		
		//--- Cuadro
		$cuadro = $pantallas[1]->get_hijos();
		$this->assertEqual(count($cuadro), 1);
		$cuadro = current($cuadro);
		$this->assertEqual($cuadro->get_nombre(), $anexo."Clonador - Item con dependencias - pant2 - cuadro");
		$this->assertEqual($cuadro->get_subclase_archivo(), 'p_acciones/clonador/sub_carpeta/subclase_cuadro.php');		
	}	
	
	function test_item_con_dependencias_con_subclases()
	{
		//$path_relativo = toba_dir()."/proyectos/".toba_editor::get_proyecto_cargado()."/php/";
		$path_relativo = toba::instancia()->get_path_proyecto(toba_editor::get_proyecto_cargado()).'/php';
		$anexo= "Clon - ";
		$nuevos_datos = array('anexo_nombre' => $anexo);
		$meta_item = $this->probar_item('1000034', $nuevos_datos, 'nuevo_dir');
		$this->assertEqual($anexo."Clonador - Item con dependencias", $meta_item->get_nombre());
		
		//--- CI
		$subclase = 'nuevo_dir/subclase_ci.php';			
		$ci = $meta_item->get_hijos();
		$ci = current($ci);
		$this->assertEqual($ci->get_subclase_archivo(), $subclase);
		$this->assertTrue(file_exists($path_relativo.'/'.$subclase));		
		unlink($path_relativo.'/'.$subclase);
				
		//--- Pantallas
		$subclase = 'nuevo_dir/subclase_pantalla.php';		
		$pantallas = $ci->get_hijos();
		$this->assertEqual($pantallas[0]->get_subclase_archivo(), $subclase);
		$this->assertTrue(file_exists($path_relativo.'/'.$subclase));
		unlink($path_relativo.'/'.$subclase);
				
		//--- Form
		$subclase = 'nuevo_dir/subclase_form.php';		
		$form = $pantallas[0]->get_hijos();
		$form = current($form);
		$this->assertEqual($form->get_subclase_archivo(), $subclase);
		$this->assertTrue(file_exists($path_relativo.'/'.$subclase));
		unlink($path_relativo.'/'.$subclase);		
		
		//--- Cuadro
		$subclase = 'nuevo_dir/subclase_cuadro.php';
		$cuadro = $pantallas[1]->get_hijos();
		$cuadro = current($cuadro);
		$this->assertEqual($cuadro->get_subclase_archivo(), $subclase);
		$this->assertTrue(file_exists($path_relativo.'/'.$subclase));
		unlink($path_relativo.'/'.$subclase);
		rmdir($path_relativo."/nuevo_dir");
	}
}

?>