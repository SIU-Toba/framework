<?php
require_once('nucleo/lib/item.php');

class test_item extends test_toba
{
	function test_distincion_entre_patron_accion_buffer()
	{
		$patron = new item();
		$patron->cargar_por_id('/pruebas/testing_automatico/ejemplo_patron');
		$accion = new item();
		$accion->cargar_por_id('/pruebas/testing_automatico/casos');
		$buffer = new item();
		$buffer->cargar_por_id('/pruebas/testing_automatico/ejemplo_buffer');
		
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

}


?>