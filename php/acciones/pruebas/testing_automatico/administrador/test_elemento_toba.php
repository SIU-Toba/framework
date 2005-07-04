<?php
require_once('api/elemento_objeto_ci.php');

class test_elemento_toba extends test_toba
{
	
	function asertar_eventos($elemento, $predefinidos, $invalidos, $desconocidos, $sospechosos)
	{
		foreach ($invalidos as $evento) {
			$this->assertTrue($elemento->es_evento($evento));
			$this->assertFalse($elemento->es_evento_valido($evento));		
			$this->assertFalse($elemento->es_evento_predefinido($evento));		
		}
		foreach ($predefinidos as $evento) {
			$this->assertTrue($elemento->es_evento($evento));
			$this->assertTrue($elemento->es_evento_predefinido($evento));		
			$this->assertTrue($elemento->es_evento_valido($evento));
			$this->assertFalse($elemento->es_evento_sospechoso($evento));
		}
		foreach ($desconocidos as $evento) {
			$this->assertTrue($elemento->es_evento($evento));
			$this->assertFalse($elemento->es_evento_predefinido($evento));		
			$this->assertTrue($elemento->es_evento_valido($evento));	
			$this->assertFalse($elemento->es_evento_sospechoso($evento));
		}
		foreach ($sospechosos as $evento) {
			$this->assertTrue($elemento->es_evento($evento));
			$this->assertFalse($elemento->es_evento_predefinido($evento));		
			$this->assertTrue($elemento->es_evento_valido($evento));
			$this->assertTrue($elemento->es_evento_sospechoso($evento), "$evento no es sospechoso");	
		}		
	}
	
	
	function test_eventos_ci_simple()
	{
		$predefinidos= array('evt__procesar', 'evt__cancelar');
		$invalidos = array('evt_bla', 'evtotro');
		$desconocidos = array('evt__mirar');
		$sospechosos = array('evt___otro');
		
		$et_ci = new elemento_objeto_ci();
		$this->asertar_eventos($et_ci, $predefinidos, $invalidos, $desconocidos, $sospechosos);
	}
	
	
	function test_eventos_ci_con_dependencias()
	{
		//Un formulario como dependencia que no tiene el 'baja' entre los predefinidos
		$predefinidos= array(	'evt__formulario__alta', 'evt__formulario__modificacion', 'evt__formulario__cancelar');
		$desconocidos = array('evt__formulario__observar', 'evt__formulario__baja');
		$sospechosos = array('evt__formulario___otro', 'evt__formulario_alta');
		
		$et_ci = new elemento_objeto_ci();
		$et_ci->cargar_db('toba_testing', 1323);
		$this->asertar_eventos($et_ci, $predefinidos, array(), $desconocidos, $sospechosos);		
	}	
}

?>