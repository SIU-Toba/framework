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
		$ml->set_datos( $tabla->get_filas());		
	}
	
	function evt__form_filas__modificacion($datos)
	{
		$this->dep('datos')->tabla('filas')->procesar_filas($datos);
	}

	//---- Cuadro ----------------------------------------
	
	function evt__form_cuadro__modificacion($datos)
	{
		$this->dep('datos')->tabla('base')->set($datos);
	}

	function conf__form_cuadro(toba_ei_formulario $form)
	{
		return $this->dep('datos')->tabla('base')->get();
	}
	
	//---- Cuadro ----------------------------------------
	
	function evt__form_form__modificacion($datos)
	{
		$this->dep('datos')->tabla('base')->set($datos);
	}

	function conf__form_form(toba_ei_formulario $form)
	{
		return $this->dep('datos')->tabla('base')->get();
	}	
	
	
}

?>