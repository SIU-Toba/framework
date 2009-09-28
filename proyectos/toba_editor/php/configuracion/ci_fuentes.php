<?php 

class ci_fuentes extends toba_ci
{
	protected $carga_ok;

	function ini()
	{
		if ($editable = toba::zona()->get_editable()) {
			$clave['proyecto'] = toba_editor::get_proyecto_cargado();
			$clave['fuente_datos'] = $editable[1];
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
		$this->carga_ok = true;
		admin_util::refrescar_barra_lateral();
	}

	function evt__eliminar()
	{
		$this->dependencia('datos')->eliminar_todo();
		toba::solicitud()->zona()->resetear();
		$this->carga_ok = false;
		admin_util::refrescar_barra_lateral();
	}
	
	//-------------------------------------------------------------------
	//--- DEPENDENCIAS
	//-------------------------------------------------------------------

	function evt__form__modificacion($datos)
	{
		$datos['proyecto'] = toba_editor::get_proyecto_cargado();
		$datos_orig = $datos;

		//--- Actualiza bases.ini
		if (isset($datos['motor'])) {
			$instancia = toba_editor::get_id_instancia_activa();
			$id_base = "$instancia {$datos['proyecto']} {$datos['fuente_datos']}";
			$instalacion = toba_modelo_catalogo::get_instalacion(null);
			$bases = $instalacion->get_lista_bases();
			$datos = array_dejar_llaves($datos, array('motor', 'profile', 'usuario', 'clave', 'base', 'puerto', 'schema', 'encoding', 'conexiones_perfiles'));
			if (in_array($id_base, $bases)) {
				//---Actualiza la entrada actual
				$instalacion->actualizar_db($id_base, $datos);
			} else {
				//---Crea una nueva entrada	
				$instalacion->agregar_db($id_base, $datos);
			}
		}
		// Se eliminan columnas del 
		unset($datos_orig['usuario']);
		unset($datos_orig['clave']);
		unset($datos_orig['base']);
		unset($datos_orig['puerto']);
		$this->dependencia('datos')->set($datos_orig);
	}

	function conf__form()
	{
		$datos = $this->dependencia('datos')->get();
		
		if (isset($datos['fuente_datos'])) {
			$instancia = toba_editor::get_id_instancia_activa();
			$id_base = "$instancia {$datos['proyecto']} {$datos['fuente_datos']}";
			$datos['entrada'] = "<strong>[$id_base]</strong>";
			
			//--- Rellena con la info de bases.ini si existe
			$instalacion = toba_modelo_catalogo::get_instalacion();
			$bases = $instalacion->get_lista_bases();
			if (in_array($id_base, $bases)) {
				$datos = array_merge($datos, $instalacion->get_parametros_base($id_base));
			}
		} else {
			$this->dep('form')->desactivar_efs(array('separador', 'entrada', 'motor', 'profile',
													'usuario', 'clave', 'base', 'puerto', 'schema', 'encoding'));
		}
		$this->dep('form')->ef('subclase_archivo')->set_iconos_utilerias(admin_util::get_ef_popup_utileria_php());
		return $datos;
	}

	function evt__form__crear_auditoria()
	{
		$instalacion = toba_modelo_catalogo::get_instalacion();
		$datos = $this->dependencia('datos')->get();
		$instancia = toba_editor::get_id_instancia_activa();
		$id_base = "$instancia {$datos['proyecto']} {$datos['fuente_datos']}";
		if (!$instalacion->existe_base_datos_definida($id_base)) {
			throw new toba_error("Debe definir los parámetros de conexión");
		}
		$parametros = $instalacion->get_parametros_base($id_base);
		if (! isset($parametros['schema'])) {
			$schema = 'public';
		} else {
			$schema = $parametros['schema'];
		}
		$schema_auditoria = $schema. '_auditoria';		
		$id_fuente = $this->dependencia('datos')->get_columna('fuente_datos');
		$db = toba::db($id_fuente,  toba_editor::get_proyecto_cargado());
		try{
			$auditoria = new toba_auditoria_tablas_postgres($db, $schema, $schema_auditoria);
			$auditoria->set_esquema_logs($schema_auditoria);
			$auditoria->agregar_tablas();	///Agrego todas las tablas

			if (! $auditoria->existe()){
				$auditoria->crear();
			}else{
				$auditoria->migrar();
			}
		} catch(toba_error $e){
			throw $e;
/*			toba::logger()->debug($e->getMessage());
			toba::notificacion()->agregar('Error al crear el esquema de auditoria', 'error');
			return false;*/
		}
		toba::notificacion()->agregar('Esquema creado satisfactoriamente', 'info');
		$this->dependencia('datos')->set_columna_valor('tiene_auditoria', '1');
		return true;
	}
}
?>