<?php
class ci_fuentes extends toba_ci
{
	protected $s__carga_ok;
	protected $s__nombre_fuente;
	protected $lista_esquemas;
	protected $s__datos_bases_ini = array();

	function ini()
	{
		if ($editable = toba::zona()->get_editable()) {
			$clave['proyecto'] = toba_editor::get_proyecto_cargado();
			$clave['fuente_datos'] = $editable[1];						
			if (! $this->dependencia('datos')->esta_cargada()) {
				$this->s__carga_ok = $this->dependencia('datos')->cargar($clave);								
			}
		}			
	}

	function conf()
	{
		if (!$this->s__carga_ok) {
			$this->pantalla()->eliminar_evento('eliminar');
		}
	}

	function get_lista_bases()
	{
		$bases = toba_dba::get_bases_definidas();
		$datos = array();
		$orden = 0;
		foreach ($bases as $base => $descripcion) {
			$datos[$orden]['id'] = $base;
			$datos[$orden]['nombre'] = $base .' --- '. $descripcion['base'] .'@'. $descripcion['profile'];
			$orden++;
		}
		return $datos;
	}
	
	function get_lista_schemas()
	{
		if (! isset($this->lista_esquemas)) {
			$db = toba_editor::db_proyecto_cargado($this->s__nombre_fuente);
			$this->lista_esquemas = $db->get_lista_schemas_disponibles();
		}
		return $this->lista_esquemas;
	}
	
	function es_motor_postgres()
	{
		if (isset($this->s__datos_bases_ini['motor'])) {
			return (trim($this->s__datos_bases_ini['motor']) == 'postgres7');
		}
		return false;
	}

	//---- Eventos CI -------------------------------------------------------

	function evt__guardar()
	{
		//Primero grabo el archivo bases.ini	
		if (isset($this->s__datos_bases_ini['motor']) && trim($this->s__datos_bases_ini['motor']) != '') {
			$instancia = toba_editor::get_id_instancia_activa();
			$id_base = "$instancia {$this->s__datos_bases_ini['proyecto']} {$this->s__datos_bases_ini['fuente_datos']}";
			$this->persistir_archivo_conf($id_base, $this->s__datos_bases_ini);		
		}
		
		$schemas_config = $this->dependencia('datos')->tabla('esquemas')->get_cantidad_filas();
		if ($this->es_motor_postgres() && $schemas_config == 0 && trim($this->s__datos_bases_ini['usuario']) != '') {
			toba::notificacion()->agregar('No olvide agregar los schemas utilizados por la fuente', 'info');
		}		
		
		//Ahora grabo los datos en la instancia
		$this->dependencia('datos')->sincronizar();
		
		//Aca tendria que agregar los schemas a la bd.
		$clave = $this->dependencia('datos')->tabla('fuente')->get_clave_valor(0);
		$zona = toba::solicitud()->zona();
		if (! $zona->cargada()) {
			$zona->cargar(array_values($clave));
		}
		$this->s__carga_ok = true;
		admin_util::refrescar_barra_lateral();
	}

	function evt__eliminar()
	{
		$this->dependencia('datos')->eliminar_todo();
		toba::solicitud()->zona()->resetear();
		$this->s__carga_ok = false;
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
		$tmp_datos = array_merge($this->s__datos_bases_ini, $datos);
		$this->s__datos_bases_ini = $tmp_datos;
		
		// Se eliminan columnas del 
		unset($datos_orig['usuario']);
		unset($datos_orig['clave']);
		unset($datos_orig['base']);
		unset($datos_orig['puerto']);
		unset($datos_orig['conexiones_perfiles']);
		unset($datos_orig['motor']);
		unset($datos_orig['profile']);
		$this->dependencia('datos')->tabla('fuente')->set($datos_orig);		
		$this->s__nombre_fuente = $datos_orig['fuente_datos'];
	}

	function conf__form($form)
	{
		$datos = array();
		if ($this->s__carga_ok) {
			$datos = $this->dependencia('datos')->tabla('fuente')->get();
			$instancia = toba_editor::get_id_instancia_activa();
			$id_base = "$instancia {$datos['proyecto']} {$datos['fuente_datos']}";	
			if (! isset($this->s__datos_bases_ini) || empty($this->s__datos_bases_ini)) {
				$this->s__datos_bases_ini = $this->cargar_archivo_ini($id_base);			
			}	
		}
		
		$form->ef('subclase_archivo')->set_iconos_utilerias(admin_util::get_ef_popup_utileria_php());
		if (isset($datos['fuente_datos'])) {
			$datos = array_merge($datos, $this->s__datos_bases_ini);
			$datos['entrada'] = "<strong>[$id_base]</strong>";
		} else {
			$form->desactivar_efs(array('separador', 'entrada', 'motor', 'profile',	'usuario', 'clave', 'base', 'puerto'));
		}
		
		$form->set_datos($datos);		
		
		//Controlo que tabs mostrar en base a los datos del form		
		if (! isset($datos['base']) || trim($datos['base']) == '' ) {			
			$this->pantalla()->eliminar_tab(2);
			$this->pantalla()->eliminar_tab(3);			
		}

	}

	//-----------------------------------------------------------------------------------
	//---- form_auditoria ---------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_auditoria(toba_ei_formulario $form)
	{
		$datos = $this->dependencia('datos')->tabla('fuente')->get();
		$form->set_datos($datos);
	}

	function evt__form_auditoria__modificacion($datos)
	{
		if (isset($datos['tiene_auditoria'])) {
			$this->dependencia('datos')->tabla('fuente')->set_columna_valor('tiene_auditoria', $datos['tiene_auditoria']);
		}
	}	
	
	function evt__form_auditoria__crear_auditoria()
	{
		$instalacion = toba_modelo_catalogo::instanciacion()->get_instalacion();
		$instancia = toba_editor::get_id_instancia_activa();
		$proyecto_cargado = toba_editor::get_proyecto_cargado();

		$id_base = "$instancia $proyecto_cargado {$this->s__nombre_fuente}";
		if (!$instalacion->existe_base_datos_definida($id_base)) {
			throw new toba_error('Debe definir los parámetros de conexión');
		}
		$parametros = $instalacion->get_parametros_base($id_base);
		if (! isset($parametros['schema'])) {
			$schema = 'public';
		} else {
			$schema = $parametros['schema'];
		}
		$schema_auditoria = $schema. '_auditoria';		
		
		//Creo el objeto para asignar los roles correctos a las tablas de auditoria
		$modelo_proyecto = toba_modelo_catalogo::instanciacion()->get_proyecto($instancia, $proyecto_cargado);
		$db = toba_editor::db_proyecto_cargado($this->s__nombre_fuente);//,  $proyecto_cargado);
		try {
			$auditoria = $db->get_manejador_auditoria($schema, $schema_auditoria);
			if (is_null($auditoria)) {
				throw toba_error_db('No existe manejador de auditoria para este motor de bd');
			}
			$auditoria->set_triggers_eliminacion_forzada(true);
			$auditoria->agregar_tablas();	///Agrego todas las tablas

			if (! $auditoria->existe()) {
				$auditoria->crear();
			} else {
				$auditoria->migrar();
			}
			$modelo_proyecto->generar_roles_db();
		} catch(toba_error $e) {
			throw $e;
		}
		toba::notificacion()->agregar('Esquema creado satisfactoriamente', 'info');
		$this->dependencia('datos')->tabla('fuente')->set_columna_valor('tiene_auditoria', '1');
		return true;
	}


	//-----------------------------------------------------------------------------------
	//---- form_schemas -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_schemas(eiform_fuente_datos_esquemas $form)
	{
		$manejados = array();
		
		//El encoding lo sacamos del bases ini
		$datos_base = $this->s__datos_bases_ini;
		$encoding = (isset($datos_base['encoding'])) ? $datos_base['encoding'] : '';
		
		$datos_basicos = $this->dependencia('datos')->tabla('fuente')->get();
		$datos_schemas = $this->dependencia('datos')->tabla('esquemas')->get_filas(null, false, false);
		foreach ($datos_schemas as $schema) {
			$manejados[] = $schema['nombre'];
		}		
		$datos_form = array('schema' => $datos_basicos['schema'], 'encoding' => $encoding, 'esquemas_manejados' => $manejados);
		$form->set_datos($datos_form);
		if (! $this->es_motor_postgres()) {
			$form->desactivar_efs(array('esquemas_manejados', 'schema'));
		}
	}

	function evt__form_schemas__modificacion($datos)
	{
		//Grabo la informacion en la base
		if (isset($datos['schema'])) {
			$this->dependencia('datos')->tabla('fuente')->set_columna_valor('schema', $datos['schema']);
		}
	
		if (isset($datos['esquemas_manejados'])) {
			$esquemas_seleccionados = $datos['esquemas_manejados'];
			if (count($esquemas_seleccionados) > 0) {
				$this->dependencia('datos')->tabla('esquemas')->eliminar_filas();
				foreach ($esquemas_seleccionados as $esquema) {
					$this->dependencia('datos')->tabla('esquemas')->nueva_fila(array('nombre' => $esquema, 'principal' => 0));
				}			
			}
		}
		
		//Agrego o modifico la informacion en bases ini
		if (isset($datos['encoding']) || isset($datos['schema'])) {
			$this->s__datos_bases_ini = array_merge($this->s__datos_bases_ini, $datos);
			$instancia = toba_editor::get_id_instancia_activa();
			$proyecto_cargado = toba_editor::get_proyecto_cargado();
			$id_base = "$instancia $proyecto_cargado {$this->s__nombre_fuente}";
		}
	}
	
	//----------------------------------------------------------------------------------------------------------
	//				METODOS AUXILIARES
	//----------------------------------------------------------------------------------------------------------
	function persistir_archivo_conf($id_base, $datos)
	{
		$instalacion = toba_modelo_catalogo::instanciacion()->get_instalacion(null);
		$bases = $instalacion->get_lista_bases();
		$datos = array_dejar_llaves($datos, array('motor', 'profile', 'usuario', 'clave', 'base', 'puerto', 'schema', 'encoding', 'conexiones_perfiles')); 
		if (isset($datos['conexiones_perfiles']) || is_null($datos['conexiones_perfiles'])) {
			unset($datos['conexiones_perfiles']);
		}
		if (in_array($id_base, $bases)) {
			//---Actualiza la entrada actual
			$instalacion->actualizar_db($id_base, $datos);
		} else {
			//---Crea una nueva entrada	
			$instalacion->agregar_db($id_base, $datos);
		}		
	}
		
	function cargar_archivo_ini($id_base)
	{
		$instalacion = toba_modelo_catalogo::instanciacion()->get_instalacion(null);
		if ($instalacion->existe_base_datos_definida($id_base)) {
			return $instalacion->get_parametros_base($id_base);
		}
		return array();
	}	
}
?>