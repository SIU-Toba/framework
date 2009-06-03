<?php
php_referencia::instancia()->agregar(__FILE__);
require_once('operaciones_simples/consultas.php'); 

class ci_abm_juegos extends toba_ci
{
	function ini()
	{
		//Cambio el nombre del item en Runtime, para esto chequee 'Retrasar Headers' en el editor del item.
		$datos = array('item_nombre' => 'Este item antes se llamaba ABM de Juegos, pero yo lo cambie en runtime!');
		toba::solicitud()->set_datos_item($datos);
	}

	//-- CUADRO --
	
	function conf__cuadro($cuadro)
	{
		$cuadro->set_datos(consultas::get_juegos());
	}

	function evt__cuadro__seleccion($seleccion)
	{
		$this->dep('datos')->cargar($seleccion);
	}

	//-- FORMULARIO

	function conf__formulario()
	{
		if ($this->dep('datos')->esta_cargada()) {
			return $this->dep('datos')->get();	
		}
	}

	function evt__formulario__alta($datos)
	{
		$this->dep('datos')->nueva_fila($datos);
		$this->dep('datos')->sincronizar();
		$this->dep('datos')->resetear();
	}

	function evt__formulario__modificacion($datos)
	{
		$this->dep('datos')->set($datos);
		$this->dep('datos')->sincronizar();
		$this->dep('datos')->resetear();
	}

	function evt__formulario__baja()
	{
		$this->dep('datos')->eliminar_filas();
		$this->dep('datos')->sincronizar();
		$this->dep('datos')->resetear();
	}

	function evt__formulario__cancelar()
	{
		$this->dep('datos')->resetear();
	}
}

?>