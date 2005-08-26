<?php
require_once("base_test_datos_relacion.php");
require_once('nucleo/persistencia/objeto_datos_relacion.php');

class test_datos_relacion_2 extends base_test_datos_relacion
{
	function get_descripcion()
	{
		return "OBJETO datos_relacion - Editor CI";
	}	

	function get_dr()
	{
		$dt = new objeto_datos_relacion(array('toba','1507'));
		return $dt;
	}

	//#############################################################
	//#    PRUEBAS    
	//#############################################################

	function test_carga()
	{
		$this->dr->cargar( array('proyecto'=>'toba','objeto'=>'1500') );
		$this->control_cambios(	array(	"base" => array("db"),
										"prop_basicas" => array("db"),
										"dependencias" => array("db","db","db","db","db","db","db","db","db"),
										"pantallas" => array("db","db","db","db"),
										"eventos" => array("db") ));
		//$this->dump_contenido();
		/*
			$ap = $this->dr->get_persistidor();
			$ap->desactivar_transaccion();
		*/
	}

}
?>