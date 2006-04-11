<?php

class test_item extends test_toba
{

	function get_descripcion()
	{
		return "Comportamiento básico del ítem";
	}	

	function sentencias_restauracion()
	{
		$sentencias[] = "DELETE FROM apex_usuario_grupo_acc_item WHERE proyecto='toba_testing' AND item='/pruebas_item/item_sin_permisos'";
		return $sentencias;
	}
	
	function test_distincion_entre_patron_accion_buffer()
	{
		$patron = new item_toba();
		$patron->cargar_por_id('toba_testing', '/pruebas_item/ejemplo_patron');
		$accion = new item_toba();
		$accion->cargar_por_id('toba_testing', '/pruebas_item/ejemplo_accion');
		$buffer = new item_toba();
		$buffer->cargar_por_id('toba_testing', '/pruebas_item/ejemplo_buffer');
		
		$this->assertTrue($patron->es_patron());
		$this->assertFalse($patron->es_accion());
		$this->assertFalse($patron->es_buffer());

		$this->assertFalse($accion->es_patron());
		$this->assertTrue($accion->es_accion());
		$this->assertFalse($accion->es_buffer());
		
		$this->assertFalse($buffer->es_patron());
		$this->assertFalse($buffer->es_accion());
		$this->assertTrue($buffer->es_buffer());
 	}
	
	function test_consulta_grupos_acceso()
	{
		//Un item sin permisos no debe tener grupo de acceso
		$item = new item_toba();
		$item->cargar_por_id('toba_testing', '/pruebas_item/item_sin_permisos');	
		$this->AssertEqual(count($item->grupos_acceso()), 0);
		
		//Item con dos grupos permitidos
		$item = new item_toba();
		$item->cargar_por_id('toba_testing', '/pruebas_item/item_con_dos_grupos');	
		$this->AssertEqual(count($item->grupos_acceso()), 2, 'La cantidad de grupos debe ser 2 (%s)');
		$this->AssertTrue($item->grupo_tiene_permiso('admin'), 'Admin tiene derechos sobre el item');
		$this->AssertTrue($item->grupo_tiene_permiso('documentacion'), 'Documentación tiene derechos sobre el item');		
	}
	
	function test_otorgar_permiso()
	{
		//Se carga un item sin permisos
		$item = new item_toba();
		$item->cargar_por_id('toba_testing', '/pruebas_item/item_sin_permisos');	
		
		//Se le asigna permisos al documentador en el proyecto de testing
		$item->otorgar_permiso('documentacion');
		
		//Se vuelve a cargar debe tener permisos de documentador
		$item = new item_toba();
		$item->cargar_por_id('toba_testing', '/pruebas_item/item_sin_permisos');	
		$this->AssertEqual(count($item->grupos_acceso()), 1, 'Debe haber sólo 1 grupo (%s)');
		$this->AssertTrue($item->grupo_tiene_permiso('documentacion'), 'Documentacion tiene derechos sobre el item');
	}


}


?>