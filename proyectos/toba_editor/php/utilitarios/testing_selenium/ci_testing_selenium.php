<?php

class ci_testing_selenium extends toba_ci
{
	protected $s__opciones;
	
	//-----------------------------------------------------------------------------------
	//---- form_opciones ----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_opciones(toba_ei_formulario $form)
	{
		$proyecto = toba_editor::get_proyecto_cargado();
		$datos_defecto = array(
			'host' => 'http://localhost',
			'url' => toba::instancia()->get_url_proyecto($proyecto),
			'path' => toba::instancia()->get_path_proyecto($proyecto).'/testing/selenium',
			'archivo' => 'cobertura.html'
		);
		$form->set_datos_defecto($datos_defecto);
		if (isset($this->s__opciones)) {
			$form->set_datos($this->s__opciones);
		}
	}

	function evt__form_opciones__modificacion($datos)
	{
		$this->s__opciones = $datos;
	}
	
	
	//-----------------------------------------------------------------------------------
	//---- Generacion ----------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	
	function get_generador()
	{
		$url = $this->s__opciones['host'].$this->s__opciones['url'];
		$proyecto = toba_editor::get_proyecto_cargado();
		$generador = new toba_testing_selenium($proyecto, $url);
		$version = toba_editor::get_modelo_proyecto()->get_version_proyecto();
		$version = $version->__toString();
		if ($this->s__opciones['test'] == 'operaciones') {
			$generador->set_titulo("$proyecto $version - Cobertura de Operaciones");
			$generador->test_operaciones($this->s__opciones['ir_pagina_inicial']);
		}
		return $generador;		
	}
	
	function conf__pant_generacion(toba_ei_pantalla $pantalla)
	{
		$salida = $this->get_generador()->get_salida();
		$salida = "<div style='height:200px; overflow:scroll;'>$salida</div>";
		$pantalla->set_template($salida);
	}
	
	function evt__generar()
	{
		$path = $this->s__opciones['path'].'/'.$this->s__opciones['archivo'];		
		$this->get_generador()->guardar($path);
		$this->pantalla()->agregar_notificacion('Generado OK');
	}
	

}

?>