<?php 

class ci_visor_modelo extends toba_ci
{
	protected $fuente;
	protected $s__tabla;
	
	function ini()
	{
		if ($editable = toba::zona()->get_editable()) {
			$this->fuente = $editable[1];
		} else {
			throw new toba_error('ERROR: Esta operacion debe ser llamada desde la zona de fuentes');
		}
	}

	function evt__volver()
	{
		$this->set_pantalla('pant_tablas');
		unset($this->s__tabla);
	}

	//-- TABLAS -------------------------------------------------

	function conf__pant_tablas()
	{
		$this->pantalla()->set_descripcion('Listado de tablas de la FUENTE de DATOS <strong>'.$this->fuente.'</strong>');	
	}

	function evt__tablas__seleccion($seleccion)
	{
		$this->s__tabla = $seleccion['nombre'];
		$this->set_pantalla('pant_columnas');
	}

	function conf__tablas(toba_ei_cuadro $cuadro)
	{
		return toba::db($this->fuente, toba_editor::get_proyecto_cargado())->get_lista_tablas_y_vistas();
	}

	//-- COLUMNAS -------------------------------------------------
	
	function conf__pant_columnas()
	{
		$this->pantalla()->set_descripcion('Columnas de la TABLA <strong>'.$this->s__tabla.'</strong>');
	}

	function conf__columnas(toba_ei_cuadro $cuadro)
	{
		$columnas = toba::db($this->fuente, toba_editor::get_proyecto_cargado())->get_definicion_columnas($this->s__tabla);
		foreach (array_keys($columnas) as $id) {
			if ($columnas[$id]['pk']) {
				$columnas[$id]['pk'] = toba_recurso::imagen_toba('aplicar.png', true, null, null);
			} else {
				$columnas[$id]['pk'] = '&nbsp;';
			}
			if ($columnas[$id]['not_null']) {
				$columnas[$id]['not_null'] = toba_recurso::imagen_toba('aplicar.png', true, null, null);
			} else {
				$columnas[$id]['not_null'] = '&nbsp;';				
			}
			if (!$columnas[$id]['secuencia']) {
				$columnas[$id]['secuencia'] = '&nbsp;';				
			}
		}
		$cuadro->set_datos($columnas);
	}

	function evt__columnas__seleccion($seleccion)
	{
		$columnas = toba::db($this->fuente, toba_editor::get_proyecto_cargado())->get_definicion_columnas($this->s__tabla);
		foreach ($columnas as $columna) {
			if ($seleccion['nombre'] === $columna['nombre']) {
				$this->s__tabla = $columna['fk_tabla'];
			}
		}
	}
}

?>