<?php

class test_item extends test_toba
{

	function get_descripcion()
	{
		return "Comportamiento básico del ítem";
	}	

	function sentencias_restauracion()
	{
		$sentencias[] = "DELETE FROM apex_usuario_grupo_acc_item WHERE proyecto='toba_testing' AND item = '1000221' ";
		return $sentencias;
	}
	
	function test_consulta_grupos_acceso()
	{
		//Un item sin permisos no debe tener grupo de acceso
		$item = toba_constructor::get_info(array('proyecto' => 'toba_testing', 
												'componente' => 1000221), 
											'toba_item');
		$this->AssertEqual(count($item->grupos_acceso()), 0);
		
		//Item con dos grupos permitidos
		$item = toba_constructor::get_info(array('proyecto' => 'toba_testing', 
												'componente' => 1000219), 
											'toba_item');
		$this->AssertEqual(count($item->grupos_acceso()), 2, 'La cantidad de grupos debe ser 2 (%s)');
		$this->AssertTrue($item->grupo_tiene_permiso('admin'), 'Admin tiene derechos sobre el item');
		$this->AssertTrue($item->grupo_tiene_permiso('documentacion'), 'Documentación tiene derechos sobre el item');		
	}
	
	function test_otorgar_permiso()
	{
		//Se carga un item sin permisos
		$item = toba_constructor::get_info(array('proyecto' => 'toba_testing', 
												'componente' => 1000221), 
											'toba_item');
											
		//Se le asigna permisos al documentador en el proyecto de testing
		$item->otorgar_permiso('documentacion');
		
		//Se vuelve a cargar debe tener permisos de documentador
		$item = toba_constructor::get_info(array('proyecto' => 'toba_testing', 
												'componente' => 1000221), 
											'toba_item');
		$this->AssertEqual(count($item->grupos_acceso()), 1, 'Debe haber sólo 1 grupo (%s)');
		$this->AssertTrue($item->grupo_tiene_permiso('documentacion'), 'Documentacion tiene derechos sobre el item');
	}


}


?>