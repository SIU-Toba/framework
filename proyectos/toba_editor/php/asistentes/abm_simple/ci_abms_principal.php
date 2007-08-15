<?php 
require_once('asistentes/ci_asistente_base.php');

class ci_abms_principal extends ci_asistente_base
{
	
	//---- Basico ----------------------------------------


	function get_nombre_tabla_actual()
	{
		return $this->dep('datos')->tabla('base')->get_columna('tabla');		
	}
	
	function conf()
	{
		$con_filtro = $this->dep('datos')->tabla('base')->get_columna('gen_usa_filtro');	
		if (! $con_filtro) {
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
		$this->dep('datos')->tabla('base')->set($datos);
	}

	function conf__form_basico(toba_ei_formulario $form)
	{
		return $this->dep('datos')->tabla('base')->get();
	}
	
	function conf__form_filas(toba_ei_formulario_ml $ml)
	{
		$tabla = $this->dep('datos')->tabla('filas');
		//--- Recorre las columnas y las rellenas con los nuevos datos
		$actuales =  $tabla->get_filas(null, true);
		$nuevas = toba_catalogo_asistentes::get_lista_filas_tabla($this->get_nombre_tabla_actual());
		
		//-- Borro las filas viejas que ya no estan en la tabla
		foreach ($actuales as $id => $actual) {
			$existe = false;
			foreach ($nuevas as $nueva) {
				if ($nueva['columna'] == $actual['columna']) {
					$existe = true;
					break;	
				}
			}
			if (!$existe) {
				$tabla->eliminar_fila($id);
			}
		}
		
		//-- Agrega las filas nuevas
		foreach ($nuevas as $nueva) {
			$existe = false;
			foreach ($actuales as $id => $actual) {
				if ($nueva['columna'] == $actual['columna']) {
					$existe = true;
					break;	
				}
			}
			if (!$existe) {
				$tabla->nueva_fila($nueva);
			}
		}		
		
		//--- Rellena el ML
		$ml->set_ordenar_en_linea(true);
		$ml->set_proximo_id($tabla->get_proximo_id());
		$ml->set_datos( $tabla->get_filas(null, true));		
	}
	
	function evt__form_filas__modificacion($datos)
	{
		$this->dep('datos')->tabla('filas')->procesar_filas($datos);
	}

	function evt__form_filas__refrescar($datos)
	{
		$this->dep('datos')->tabla('filas')->eliminar_filas();
	}
	

	//---- CUADRO - Prop. basicas ----------------------------------------
	
	function evt__form_cuadro__modificacion($datos)
	{
		$this->dep('datos')->tabla('base')->set($datos);
	}

	function conf__form_cuadro(toba_ei_formulario $form)
	{
		$datos = $this->dep('datos')->tabla('base')->get();
		$form->set_datos($datos);
	}
	
	//---- CUADRO - Opciones de carga ----------------------------------------
	
	function evt__form_cuadro_carga__modificacion($datos)
	{
		$datos = array_cambiar_prefijo_claves($datos, 'cuadro_', false);		
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
		$datos['carga_php_metodo_nuevo'] = $datos['carga_php_metodo'];		
		$form->set_datos($datos);
	}	
	
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
	
	function evt__form_form_fila__modificacion($datos)
	{
		$datos = array_cambiar_prefijo_claves($datos, 'ef_', false);		
		if (!isset($datos['ef_carga_php_metodo']) && isset($datos['ef_carga_php_metodo_nuevo'])) {
			$datos['ef_carga_php_metodo'] = $datos['ef_carga_php_metodo_nuevo'];
		}		
		$this->dep('datos')->tabla('filas')->set($datos);
		$this->dep('datos')->tabla('filas')->resetear_cursor();
	}
	
	function conf__form_form_fila(toba_ei_formulario $form)
	{
		
		$datos = $this->dep('datos')->tabla('filas')->get();
		$datos = array_cambiar_prefijo_claves($datos, 'ef_', true);
		$form->set_datos($datos);
	}
	
	
	
	
	
}

?>