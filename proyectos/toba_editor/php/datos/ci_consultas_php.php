<?php 
class ci_consultas_php extends toba_ci
{
	protected $carga_ok;
	
	function ini()
	{
		if ($editable = toba::zona()->get_editable()) {
			$clave['proyecto'] = toba_editor::get_proyecto_cargado();
			$clave['consulta_php'] = $editable[1];
			$this->carga_ok = $this->dependencia('datos')->cargar($clave);
		}	
	}

	function conf()
	{
		if (!$this->carga_ok) {
			$this->pantalla()->eliminar_evento('eliminar');
		}	
	}

	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__guardar()
	{
		$this->dependencia('datos')->sincronizar();
		$clave = $this->dependencia('datos')->get_clave_valor(0);
		$zona = toba::solicitud()->zona();
		if (! $zona->cargada()) {
			$zona->cargar(array($clave['proyecto'],$clave['consulta_php']));
		}
		$this->carga_ok = true;
		admin_util::refrescar_barra_lateral();
		//Si no existe el archivo, lo creo
		$datos = $this->dependencia('datos')->get();
		if (! admin_util::existe_archivo_subclase($datos['archivo'],$datos['punto_montaje'])) {
			$this->crear_archivo(admin_util::get_path_archivo($datos['archivo'], $datos['punto_montaje']), $datos['clase']);
		}
	}

	protected function crear_archivo($archivo, $clase)
	{
		$php = '<?php' . salto_linea();
		$php .= salto_linea();
		$php .= "class $clase" . salto_linea();
		$php .= '{' .salto_linea();
		$php .= salto_linea();
		$php .= '}' .salto_linea();
		$php .= salto_linea();
		$php .= '?>';
		toba_manejador_archivos::crear_archivo_con_datos($archivo, $php);
	}

	protected function get_path_archivo($archivo)
	{
		$dir = toba::instancia()->get_path_proyecto(toba_editor::get_proyecto_cargado());
		return "$dir/php/$archivo";
	}

	function evt__eliminar()
	{
		$this->dependencia('datos')->eliminar_todo();
		toba::solicitud()->zona()->resetear();
		$this->carga_ok = false;
		admin_util::refrescar_barra_lateral();
	}

	//-----------------------------------------------------------------------------------
	//---- DEPENDENCIAS -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------


	function evt__form__modificacion($datos)
	{
		$datos['proyecto'] = toba_editor::get_proyecto_cargado();
		$this->dependencia('datos')->set($datos);
	}

	function conf__form(toba_ei_formulario $form)
	{		
		$datos = $this->dependencia('datos')->get();
		$utilerias_popup = admin_util::get_ef_popup_utileria_php();
		if (isset($datos['punto_montaje'])) {
			$claves = array_keys($utilerias_popup);
			foreach($claves as $klave) {
				$utilerias_popup[$klave]->agregar_parametros(array('punto_montaje' => $datos['punto_montaje']));
			}
		}
		$form->ef('archivo')->set_iconos_utilerias($utilerias_popup);
		$form->set_datos($datos);
	}
}

?>