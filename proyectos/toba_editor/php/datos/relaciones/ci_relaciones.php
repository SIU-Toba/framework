<?php
class ci_relaciones extends toba_ci
{
	protected $s__editar = false;
	protected $fuente;
	
	function ini()
	{
		if ($editable = toba::zona()->get_editable()) {
			$this->fuente = $editable[1];
		} else {
			throw new toba_error('ERROR: Esta operacion debe ser llamada desde la zona de fuentes');
		}		
	}
	
	function conf__editar()
	{
		if ($this->s__editar) {
			$this->pantalla()->eliminar_dep('cuadro');
			$this->pantalla()->eliminar_evento('agregar');
			if (! $this->hay_datos()) {
				$this->pantalla()->eliminar_dep('form_columnas');
			} 
			if (!$this->dep('datos')->esta_cargada()) {
				$this->pantalla()->eliminar_evento('eliminar');
			}
		} else {
			$this->pantalla()->eliminar_dep('form_tablas');		
			$this->pantalla()->eliminar_dep('form_columnas');		
			$this->pantalla()->eliminar_evento('eliminar');
			$this->pantalla()->eliminar_evento('guardar');
			$this->pantalla()->eliminar_evento('cancelar');
		}
	}

	function evt__agregar()
	{
		$this->s__editar = true;	
	}
	
	function evt__cancelar()
	{
		$this->s__editar = false;	
	}

	function evt__guardar()
	{
		$this->dep('datos')->sincronizar();
		$this->dep('datos')->resetear();
		$this->s__editar = false;	
	}	

	function evt__eliminar()
	{
		$this->dep('datos')->eliminar_todo();
		$this->dep('datos')->resetear();
		$this->s__editar = false;	
	}	
	
	//---- Cuadro -----------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$datos = toba_info_editores::get_relaciones_tablas($this->fuente);
		$cuadro->set_datos($datos);
	}

	function evt__cuadro__seleccion($datos)
	{
		$this->dep('datos')->cargar($datos);
		$this->s__editar = true;	
	}

	//---- Formulario -------------------------------------------------------------------

	function hay_datos()
	{
		return ($this->dep('datos')->get_cantidad_filas() > 0);
	}

	function evt__form_tablas__modificacion($datos)
	{
		$datos['proyecto'] = toba_editor::get_proyecto_cargado();
		$datos['fuente_datos_proyecto'] = toba_editor::get_proyecto_cargado();
		$datos['fuente_datos'] = $this->fuente;
		$this->dep('datos')->set($datos);
	}

	function conf__form_tablas(toba_ei_formulario $form)
	{
		if ($this->hay_datos()) {
			$form->set_datos($this->dep('datos')->get());
		}
	}

	function evt__form_columnas__modificacion($datos)
	{
		$t1 = array(); 
		$t2 = array();
		foreach ($datos as $dato) {
			$t1[] = $dato['columna_1'];
			$t2[] = $dato['columna_2'];
		}
		$temp['tabla_1_cols'] = implode(',', $t1);
		$temp['tabla_2_cols'] = implode(',', $t2);
		$this->dep('datos')->set($temp);
	}

	function conf__form_columnas(toba_ei_formulario_ml $form)
	{
		$datos = array();
		$dt = $this->dep('datos')->get();
		if (isset($dt['tabla_1_cols'])) {
			$t1 = explode(',', $dt['tabla_1_cols']);
			$t2 = explode(',', $dt['tabla_2_cols']);
			for ($i = 0; $i < count($t1); $i++) {
				$datos[$i]['columna_1'] = $t1[$i];
				$datos[$i]['columna_2'] = $t2[$i];
			}
			$form->set_datos($datos);
		}
	}

	//---- Recuperacion de valores de la base ---------------------------------------------------------

	function get_tablas()
	{
		return toba::db($this->fuente, toba_editor::get_proyecto_cargado())->get_lista_tablas_y_vistas();
	}
	
	function get_columnas_tabla_1()
	{
		$datos = $this->dep('datos')->get();
		$columnas = toba::db($this->fuente, toba_editor::get_proyecto_cargado())->get_definicion_columnas($datos['tabla_1']);
		return $columnas;
	}
	
	function get_columnas_tabla_2()
	{
		$datos = $this->dep('datos')->get();
		$columnas = toba::db($this->fuente, toba_editor::get_proyecto_cargado())->get_definicion_columnas($datos['tabla_2']);
		return $columnas;
	}

	//------- Graficacion de la relacion ---------------------
	
	function conf__esquema($esquema)
	{
		$dot = "digraph G {
			edge [	labelfontcolor=red,	
					labelfloat=false,
					labelfontsize=9,
					arrowhead=none,
					arrowtail=none];
			node [	shape=polygon,	
					sides=4
					color=blue];\n";
		$datos = toba_info_editores::get_relaciones_tablas($this->fuente);
		foreach ($datos as $dato) {
			$dot .=	'"' . $dato['tabla_1'] . '" -> "' . $dato['tabla_2'] . '" ' .
					'[headlabel="'. $dato['tabla_2_cols'] . 
					'", taillabel="'.$dato['tabla_1_cols']."\"];\n";
		}
		$dot .= '
		}
		';	
		//echo $dot;
		return $dot;
	}

}

?>