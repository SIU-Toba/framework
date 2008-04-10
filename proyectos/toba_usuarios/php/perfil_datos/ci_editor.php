<?php 
class ci_editor extends toba_ci
{
	protected $s__proyecto;
	protected $s__dimension;
	
	function datos()
	{
		return $this->controlador()->dep('datos');	
	}
	
	function set_proyecto($proyecto)
	{
		$this->s__proyecto = $proyecto;
	}
	
	//-----------------------------------------------------------------------------------
	//---- Pantalla 1 -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	//---- perfil -----------------------------------------------------------------------

	function evt__perfil__modificacion($datos)
	{
		$datos['proyecto'] = $this->s__proyecto;
		$this->datos()->tabla('perfil')->set($datos);
	}

	function conf__perfil(toba_ei_formulario $form)
	{
		$datos = $this->datos()->tabla('perfil')->get();
		$form->set_datos($datos);
	}
	
	//---- dimensiones ------------------------------------------------------------------

	function evt__dimensiones__seleccion($seleccion)
	{
		$this->s__dimension = $seleccion['dimension'];
		$this->set_pantalla('pant_dimensiones');
	}

	function conf__dimensiones(toba_ei_cuadro $cuadro)
	{
		$datos = toba_info_editores::get_dimensiones($this->s__proyecto);
		$cuadro->set_datos($datos);
	}

	//-----------------------------------------------------------------------------------
	//---- Pantalla 2 -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__aceptar()
	{
		$this->set_pantalla('pant_perfil');
	}

	function evt__volver()
	{
		$this->set_pantalla('pant_perfil');
	}

	function conf__pant_dimensiones($pantalla)
	{
		//Saco los botones del CI de afuera
		$pantalla_externa = $this->controlador->pantalla();
		$pantalla_externa->eliminar_evento('guardar');
		$pantalla_externa->eliminar_evento('cancelar');
		$pantalla_externa->eliminar_evento('eliminar');
	}

	//---- FORM elementos --------------------------------------------------------------------

	function conf__elementos(toba_ei_formulario_ml $form_ml)
	{
		$datos = $this->get_elementos_dimension();
		$form_ml->set_datos($datos);
	}

	function evt__elementos__modificacion($datos)
	{
		ei_arbol($datos);
	}

	//---- Buscar data de la dimension --------------------------------------------------------------------
	
	function get_db($id)
	{
		$fuente_datos = toba_admin_fuentes::instancia()->get_fuente( $id,
																	 $this->s__proyecto );
		return $fuente_datos->get_db();		
	}

	function get_elementos_dimension()
	{
		$datos = toba_info_editores::get_datos_dimension($this->s__proyecto, $this->s__dimension);
		$id = explode(',',$datos['col_id']);
		$desc = explode(',',$datos['col_desc']);
		$sql = "SELECT " . implode(' || ',$id) . " as clave, " 
						. implode(' || ',$desc) . " as descripcion
				FROM {$datos['tabla']}
				ORDER BY descripcion";
		$datos = $this->get_db($datos['fuente_datos'])->consultar($sql);
		return $datos;
	}
}

?>