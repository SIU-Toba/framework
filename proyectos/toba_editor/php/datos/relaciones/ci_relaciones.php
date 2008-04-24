<?php
class ci_relaciones extends toba_ci
{
	protected $s__editar = false;
	
	function conf__editar()
	{
		if( $this->s__editar ) {
			$this->pantalla()->eliminar_dep('cuadro');
			$this->pantalla()->eliminar_evento('agregar');
			if(! $this->hay_datos() ){
				$this->pantalla()->eliminar_dep('form_columnas');
			} 
			if(!$this->dep('datos')->esta_cargada()) {
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
		ei_arbol($this->dep('datos')->get());
		$this->dep('datos')->sincronizar();
		$this->dep('datos')->resetear();
		$this->s__editar = false;	
	}	

	//---- Cuadro -----------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$datos = toba_info_editores::get_relaciones_tablas();
		$cuadro->set_datos($datos);
	}

	function evt__cuadro__seleccion($datos)
	{
		$this->dep('datos')->cargar($datos);
	}

	//---- Formulario -------------------------------------------------------------------

	function hay_datos()
	{
		return 	($this->dep('datos')->get_cantidad_filas() > 0);
	}

	function evt__form_tablas__modificacion($datos)
	{
		$datos['proyecto'] = toba_editor::get_proyecto_cargado();
		$this->dep('datos')->set($datos);
	}

	function conf__form_tablas(toba_ei_formulario $form)
	{
		if( $this->hay_datos() ){
			$form->set_datos($this->dep('datos')->get());
		}
	}

	function evt__form_columnas__modificacion($datos)
	{
		$t1 = array(); $t2 = array();
		foreach($datos as $dato) {
			$t1[] = $dato['columna_1'];
			$t2[] = $dato['columna_2'];
		}
		$temp['tabla_1_cols'] = implode(',',$t1);
		$temp['tabla_2_cols'] = implode(',',$t2);
		$this->dep('datos')->set($temp);
	}

	function conf__form_columnas(toba_ei_formulario_ml $form)
	{
		$datos = array();
		$dt = $this->dep('datos')->get();
		if(isset($dt['tabla_1_cols'])){
			$t1 = explode(',',$dt['tabla_1_cols']);
			$t2 = explode(',',$dt['tabla_2_cols']);
			for($i=0;$i<count($t1);$i++) {
				$datos[$i]['columna_1'] = $t1[$i];
				$datos[$i]['columna_2'] = $t2[$i];
			}
			$form->set_datos($datos);
		}
	}

	//---- Recuperacion de valores de la base ---------------------------------------------------------

	function get_tablas($fuente)
	{
		return toba::db($fuente['fuente_datos'], toba_editor::get_proyecto_cargado())->get_lista_tablas();
	}
	
	function get_columnas_tabla_1()
	{
		$datos = $this->dep('datos')->get();
		$columnas = toba::db($datos['fuente_datos'], toba_editor::get_proyecto_cargado())->get_definicion_columnas( $datos['tabla_1'] );
		return $columnas;
	}
	
	function get_columnas_tabla_2()
	{
		$datos = $this->dep('datos')->get();
		$columnas = toba::db($datos['fuente_datos'], toba_editor::get_proyecto_cargado())->get_definicion_columnas( $datos['tabla_2'] );
		return $columnas;
	}

}

?>