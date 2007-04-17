<?php 
require_once('modelo/instalacion.php');

class ci_fuentes extends toba_ci
{
	protected $carga_ok;

	function ini()
	{
		if ($editable = toba::zona()->get_editable()) {
			$clave['proyecto'] = toba_editor::get_proyecto_cargado();
			$clave['fuente_datos'] = $editable[1];
			$this->dependencia('datos')->cargar($clave);
			$this->carga_ok = $this->dependencia('datos')->cargar($clave);
		}			
	}

	function conf()
	{
		if(!$this->carga_ok) {
			$this->pantalla()->eliminar_evento('eliminar');
		}	
	}

	function get_lista_bases()
	{
		$bases = toba_dba::get_bases_definidas();
		$datos = array();
		$orden = 0;
		foreach($bases as $base => $descripcion) {
			$datos[$orden]['id'] = $base;
			$datos[$orden]['nombre'] = $base .' --- '. $descripcion['base'] .'@'. $descripcion['profile'];
			$orden++;
		}
		return $datos;
	}

	//---- Eventos CI -------------------------------------------------------

	function evt__guardar()
	{
		$this->dependencia('datos')->sincronizar();
		$clave = $this->dependencia('datos')->get_clave_valor(0);
		$zona = toba::solicitud()->zona();
		if (! $zona->cargada()) {
			$zona->cargar(array_values($clave));
		}
	}

	function evt__eliminar()
	{
		$this->dependencia('datos')->eliminar_todo();
		toba::solicitud()->zona()->resetear();
	}
	
	//-------------------------------------------------------------------
	//--- DEPENDENCIAS
	//-------------------------------------------------------------------

	function evt__form__modificacion($datos)
	{
		$datos['proyecto'] = toba_editor::get_proyecto_cargado();
		$this->dependencia('datos')->set($datos);
		
		//--- Actualiza bases.ini
		if (isset($datos['motor'])) {
			$instancia = toba_editor::get_id_instancia_activa();
			$id_base = "$instancia {$datos['proyecto']} {$datos['fuente_datos']}";
			$instalacion = new instalacion();
			$bases = $instalacion->get_lista_bases();
			$datos = array_dejar_llaves($datos, array('motor', 'profile', 'usuario', 'clave', 'base'));
			if (in_array($id_base, $bases)) {
				//---Actualiza la entrada actual
				instalacion::actualizar_db($id_base, $datos);
			} else {
				//---Crea una nueva entrada	
				instalacion::agregar_db($id_base, $datos);
			}
		}
	}

	function conf__form()
	{
		$datos = $this->dependencia('datos')->get();
		if (isset($datos['fuente_datos'])) {
			$instancia = toba_editor::get_id_instancia_activa();
			$id_base = "$instancia {$datos['proyecto']} {$datos['fuente_datos']}";
			$datos['entrada'] = "<strong>[$id_base]</strong>";
			
			//--- Rellena con la info de bases.ini si existe
			$instalacion = new instalacion();
			$bases = $instalacion->get_lista_bases();
			if (in_array($id_base, $bases)) {
				$datos = array_merge($datos, $instalacion->get_parametros_base($id_base));
			}
		} else {
			$this->dep('form')->desactivar_efs(array('separador', 'entrada', 'motor', 'profile',
													'usuario', 'clave', 'base'));
		}
		return $datos;
	}
}
?>