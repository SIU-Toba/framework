<?php 
require_once('asistentes/ci_asistente_base.php');

class ci_abms_principal extends ci_asistente_base
{
	protected $cambio_tabla = false;

	//---------------------------------------------------
	//---- Basico ----------------------------------------

	function get_nombre_tabla_actual()
	{
		return $this->dep('datos')->tabla('base')->get_columna('tabla');		
	}
	
	function get_fuente_actual()
	{
		return $this->dep('datos')->tabla('base')->get_columna('fuente');
	}
	
	function conf()
	{
		if ($this->get_nombre_tabla_actual() == '') {
			$this->pantalla()->tab('pant_form')->ocultar();	
		}
	}
	
	function conf__pant_basica()
	{
		if ($this->get_nombre_tabla_actual() == '') {
			$this->pantalla()->eliminar_dep('form_filas');
		}
	}

	function evt__form_basico__modificacion($datos)
	{
		if (isset($datos['fuente'])) {
			$this->dep('datos')->tabla('molde')->set(array('fuente' => $datos['fuente']));
		}
		$tabla_vieja = $this->get_nombre_tabla_actual();
		$this->dep('datos')->tabla('base')->set($datos);
		//--- Si cambio de tabla se regenera la información
		if ($tabla_vieja != $this->get_nombre_tabla_actual()) {
			$this->asistente()->autocompletar_informacion();
			$this->cambio_tabla = true;
		}		
	}

	function conf__form_basico(toba_ei_formulario $form)
	{
		$datos = $this->dep('datos')->tabla('base')->get();		
		$datos_molde = $this->dep('datos')->tabla('molde')->get();
		if (! isset($datos_molde['fuente'])) {
			$datos['fuente'] = toba_info_editores::get_fuente_datos_defecto(toba_editor::get_proyecto_cargado());
		} else {
			$datos['fuente'] = $datos_molde['fuente'];
		}
		return $datos;
	}
	
	function conf__form_filas(toba_ei_formulario_ml $ml)
	{
		$tabla = $this->dep('datos')->tabla('filas');
		//$ml->set_ordenar_en_linea(true);
		$ml->set_proximo_id($tabla->get_proximo_id());
		$ml->set_datos($tabla->get_filas(null, true));		
	}
	
	function evt__form_filas__modificacion($datos)
	{
		//--- Este ml cuando se cambio el combo de la tabla a usar ya no tiene datos necesarios
		if (! $this->cambio_tabla) {
			$this->dep('datos')->tabla('filas')->procesar_filas($datos);
		}
	}

	function evt__form_filas__refrescar($datos)
	{
		$this->evt__form_filas__modificacion($datos);
		$this->asistente()->autocompletar_informacion();
	}
	
	//---------------------------------------------------	
	//---- FORM ----------------------------------------
	
	function conf__pant_form()
	{
		if (! $this->dep('datos')->tabla('filas')->hay_cursor()) {
			$this->pantalla()->eliminar_dep('form_form_fila');
		}
	}
	
	function evt__cuadro_form_filas__seleccion($seleccion)
	{
		$this->dep('datos')->tabla('filas')->set_cursor($seleccion);
	}

	function conf__cuadro_form_filas(toba_ei_cuadro $cuadro)
	{
		//--Retorna sola las columnas con referencias
		$cuadro->set_datos($this->dep('datos')->tabla('filas')->get_filas(array('asistente_tipo_dato' => '1000008')));
	}	
	
	function evt__form_form_fila__modificacion($datos, $resetear=true)
	{
		$datos = array_cambiar_prefijo_claves($datos, 'ef_', false);		
		if (!isset($datos['ef_carga_php_metodo']) && isset($datos['ef_carga_php_metodo_nuevo'])) {
			$datos['ef_carga_php_metodo'] = $datos['ef_carga_php_metodo_nuevo'];
		}		
		$this->dep('datos')->tabla('filas')->set($datos);
		if ($resetear) {
			$this->dep('datos')->tabla('filas')->resetear_cursor();
		}
	}
	
	function evt__form_form_fila__regenerar($datos)
	{
		$fila = $this->dep('datos')->tabla('filas')->get();
		$this->evt__form_form_fila__modificacion($datos, false);		
		$this->asistente()->autocompletar_carga_combo($fila['columna']);
	}
	
	function conf__form_form_fila(toba_ei_formulario $form)
	{
		$datos = $this->dep('datos')->tabla('filas')->get();
		$datos = array_cambiar_prefijo_claves($datos, 'ef_', true);
		if (isset($datos['carga_php_metodo'])) {
			$datos['carga_php_metodo_nuevo'] = $datos['carga_php_metodo'];
		}		
		$form->set_datos($datos);
	}
}

?>