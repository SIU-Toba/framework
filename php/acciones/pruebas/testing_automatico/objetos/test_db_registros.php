<?php
require_once('nucleo/persistencia/objeto_db_registros.php');
require_once('nucleo/browser/clases/objeto_ci.php');

Mock::generate('objeto_ci');

class test_db_registros extends test_toba
{

	function SetUp()
	{
		$this->mentir_hilo();
	}
	
	function TearDown()
	{
		$this->restaurar_hilo();
	}

	function crear_dbr($observador)
	{
		$dbr = new objeto_db_registros(array('toba_testing','1424'));	//test ei_formulario_ml
		$dbr->inicializar();
		$dbr->agregar_controlador($observador);
		return $dbr;
	}
	
	function ej()
	{
		$observador = new Mockobjeto_ci($this);
		$observador->expectOnce('registrar_evento', array(null, 'modificacion', $esperados));
		$observador->expectArgumentsAt(1, 'registrar_evento', array(null, 'seleccion', 1));
		$observador->tally();
	}

	function test_carga()
	{
		$dbr = $this->crear_dbr();
		$dbr->info();
	}
}
?>