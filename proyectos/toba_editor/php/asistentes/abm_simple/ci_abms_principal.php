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
		$con_filtro = $this->dep('datos')->tabla('base')->get_columna('gen_usa_filtro');	
		if (! $con_filtro) {
			$this->pantalla()->tab('pant_filtro')->ocultar();	
		}
		if ($this->get_nombre_tabla_actual() == '') {
			$this->pantalla()->tab('pant_cuadro')->ocultar();	
			$this->pantalla()->tab('pant_form')->ocultar();	
			$this->pantalla()->tab('pant_filtro')->ocultar();	
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
			//--- Se actualizan las opciones que dependen del tipo de dato seleccionado
			foreach (array_keys($datos) as $id) {
				$nuevos_seteos = toba_catalogo_asistentes::get_fila_opciones_de_tipo($datos[$id]['asistente_tipo_dato']);
				$datos[$id]['dt_tipo_dato'] = $nuevos_seteos['dt_tipo_dato'];
				$datos[$id]['cuadro_estilo'] = $nuevos_seteos['cuadro_estilo'];
				$datos[$id]['cuadro_formato'] = $nuevos_seteos['cuadro_formato'];
				$datos[$id]['elemento_formulario'] = $nuevos_seteos['elemento_formulario'];
			}
			$this->dep('datos')->tabla('filas')->procesar_filas($datos);
		}
	}

	function evt__form_filas__refrescar($datos)
	{
		$this->evt__form_filas__modificacion($datos);
		$this->asistente()->autocompletar_informacion();
	}
	
	//---------------------------------------------------
	//---- CUADRO  ----------------------------------------
	
	function evt__form_cuadro__modificacion($datos)
	{
		$this->dep('datos')->tabla('base')->set($datos);
	}

	function conf__form_cuadro(toba_ei_formulario $form)
	{
		$datos = $this->dep('datos')->tabla('base')->get();
		$form->set_datos($datos);
	}
	
	function evt__form_cuadro_carga__modificacion($datos)
	{
		$datos = array_cambiar_prefijo_claves($datos, 'cuadro_', false);		
		if (!isset($datos['cuadro_carga_origen'])) {
			$datos['cuadro_carga_origen'] = 'datos_tabla';
		}
		if (!isset($datos['cuadro_carga_php_metodo']) && isset($datos['cuadro_carga_php_metodo_nuevo'])) {
			$datos['cuadro_carga_php_metodo'] = $datos['cuadro_carga_php_metodo_nuevo'];
		}
		$this->dep('datos')->tabla('base')->set($datos);
	}

	function conf__form_cuadro_carga(toba_ei_formulario $form)
	{
		$form->desactivar_efs(array('carga_col_clave', 'carga_col_desc'));
		$datos = $this->dep('datos')->tabla('base')->get();
		$datos = array_cambiar_prefijo_claves($datos, 'cuadro_', true);
		if (isset($datos['carga_php_metodo'])) {
			$datos['carga_php_metodo_nuevo'] = $datos['carga_php_metodo'];
		}		
		$form->set_datos($datos);
	}	
	
	/**
	 * Modifica los datos y regenera la SQL de carga del cuadro
	 */
	function evt__form_cuadro_carga__regenerar($datos)
	{
		$this->evt__form_cuadro_carga__modificacion($datos);
		$this->asistente()->autocompletar_carga_cuadro();
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
	
	//---------------------------------------------------	
	//---- FILTRO ----------------------------------------

	function evt__pant_filtro__entrada()
	{
		$datos = $this->dep('datos')->tabla('filas')->get_filas(array('en_filtro' => 1));
		if (empty($datos)) {
			throw new toba_error('Para poder utilizar el filtro es necesario definir que columnas se muestran en el mismo');
		}
	}
	
	function evt__form_filtro__modificacion($datos)
	{
		$this->dep('datos')->tabla('base')->set($datos);
	}	
	
	function conf__form_filtro(toba_ei_formulario $form)
	{
		$datos = $this->dep('datos')->tabla('base')->get();
		$form->set_datos($datos);	
	}
	
	function evt__ml_filtro_filas__modificacion($datos)
	{
		$this->dep('datos')->tabla('filas')->procesar_filas($datos);
	}
	
	function conf__ml_filtro_filas(toba_ei_formulario_ml $ml)
	{
		$datos = $this->dep('datos')->tabla('filas')->get_filas(array('en_filtro' => 1));
		$ml->set_datos($datos);
	}
	
	//---------------------------------------------------	
	//---- FILTRO ----------------------------------------

	function conf__form_presentacion(toba_ei_formulario $form)
	{
		$datos = $this->dep('datos')->tabla('base')->get();				
		$form->set_datos($datos);
	}
	
	function evt__form_presentacion__modificacion($datos)
	{
		$this->dep('datos')->tabla('base')->set($datos);
	}

	function validar_datos_ingresados()
	{
		$datos_b = $this->dep('datos')->tabla('base')->get();
		$datos_f = $this->dep('datos')->tabla('filas')->get_filas(array('en_filtro' => 1));
		if (!empty($datos_b) && ($datos_b['gen_usa_filtro'] == 1) &&empty($datos_f)) {
			throw new toba_error_def(' Se especifico un filtro pero no se definio que filas participan del mismo');
		}

		if (!empty($datos_b) && $datos_b['cuadro_carga_origen'] == 'consulta_php' ) {
			if (!isset($datos_b['cuadro_carga_php_metodo'])) {
					throw new toba_error_def(' El metodo de carga del cuadro no esta definido (MODO consulta_php).');
			}
			if (!isset($datos_b['cuadro_carga_php_clase'])) {
					throw new toba_error_def(' La clase de carga del cuadro no esta definido (MODO consulta_php).');
			}
		}

		$datos_c = $this->dep('datos')->tabla('filas')->get_filas(array('en_cuadro' => 1));
		if (empty($datos_c)) {
			throw new toba_error_def(' Se deben especificar las columnas que participan del cuadro.');
		}

		$datos_form = $this->dep('datos')->tabla('filas')->get_filas(array('en_form' => 1));
		if (empty($datos_form)) {
			throw new toba_error_def(' Se deben especificar las columnas que participan del formulario.');
		}

	}
}

?>