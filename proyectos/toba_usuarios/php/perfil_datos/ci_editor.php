<?php 
class ci_editor extends toba_ci
{
	protected $s__proyecto;
	protected $s__dimension;
	protected $datos_dimension;
	
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
		$this->datos_dimension = toba_info_editores::get_datos_dimension($this->s__proyecto, $this->s__dimension);
		//Saco los botones del CI de afuera
		$pantalla_externa = $this->controlador->pantalla();
		$pantalla_externa->eliminar_evento('guardar');
		$pantalla_externa->eliminar_evento('cancelar');
		$pantalla_externa->eliminar_evento('eliminar');
		$perfil = $this->datos()->tabla('perfil')->get();
		$txt = "Perfil de datos: <strong>{$perfil['nombre']}</strong>.<br>";
		$txt .= "Dimensión: <strong>{$this->datos_dimension['nombre']}</strong>.<br>";
		$txt .= "Seleccione los elementos que desea habilitar para el perfil.";
		$this->pantalla()->set_descripcion($txt);
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
	
	function get_elementos_dimension()
	{
		$id = explode(',',$this->datos_dimension['col_id']);
		$desc = explode(',',$this->datos_dimension['col_desc']);
		$sql = "SELECT " . implode(' || ',$id) . " as clave, " 
						. implode(' || ',$desc) . " as descripcion
				FROM {$this->datos_dimension['tabla']}
				ORDER BY descripcion";
		$datos = $this->get_db($this->datos_dimension['fuente_datos'])->consultar($sql);
		return $datos;
	}

	function get_db($id)
	{
		$fuente_datos = toba_admin_fuentes::instancia()->get_fuente( $id,
																	 $this->s__proyecto );
		return $fuente_datos->get_db();		
	}
}

?>