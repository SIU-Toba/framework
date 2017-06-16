<?php
class ci_conf_auditoria extends toba_ci
{
	protected $s__seleccionado;
	protected $s__tablas;

	
	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__procesar()
	{
		//Agrego todas las tablas para desactivar todos los triggers		
		$manejador = $this->get_manejador($this->s__seleccionado['proyecto']);
		$manejador->agregar_tablas();
		$manejador->desactivar_triggers_auditoria();
			
		//Si se selecciono alguna tabla en particular, activo solo los triggers de esas tablas
		if (isset($this->s__tablas['tablas'])) {			
			//Reseteo estado interno y agrego las tablas actuales
			$manejador->reset_tablas();			
			foreach ($this->s__tablas['tablas'] as $tabla) {
				$manejador->agregar_tabla($tabla);				
			}
			$manejador->activar_triggers_auditoria();
		}
		$this->agregar_notificacion('Schema actualizado');

		//Finish him!		
		unset($this->s__tablas);		
		unset($this->s__seleccionado);
		$this->set_pantalla('pant_inicial');		
	}

	function evt__cancelar()
	{
		unset($this->s__tablas);
		unset($this->s__seleccionado);
		$this->set_pantalla('pant_inicial');
	}

	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$cuadro->desactivar_modo_clave_segura();
		$cuadro->set_datos(consultas_instancia::get_lista_proyectos());
	}
	
	function conf_evt__cuadro__configurar(toba_evento_usuario $evt, $fila)
	{
		$existe = false;
		try {
			$existe = $this->existe_auditoria($evt->get_parametros());
		} catch (toba_error $e) {
			toba::logger()->debug($e->getMessage());
		}
		if (! $existe) {
			$evt->anular(); 
		}
	}
	
	function conf_evt__cuadro__crear_auditoria(toba_evento_usuario $evt, $fila)
	{
		$existe = false;
		try {
			$existe = $this->existe_auditoria($evt->get_parametros());
		} catch (toba_error $e) {
			toba::logger()->debug($e->getMessage());
		}

		if ($existe) {
			$evt->set_etiqueta('Actualizar Schema');
			$evt->set_msg_ayuda('Migra el schema de auditoria tomando campos nuevos o modificados');
		} else {
			$evt->set_etiqueta('Activar Auditoría');
			$evt->set_msg_ayuda('Crea un schema paralelo con la misma estructura que el schema de datos original, conteniendo todas las modificaciones a los datos del mismo');
		}
	}

	function evt__cuadro__configurar($seleccion)
	{		
		if (! $this->existe_auditoria($seleccion['proyecto'])) {
			throw new toba_error_usuario('La fuente no posee auditoria, configurela adecuadamente para su correcto funcionamiento');
		} 
		$this->s__seleccionado = $seleccion; 
		$this->set_pantalla('pant_edicion');		
	}

	function evt__cuadro__crear_auditoria($seleccion)
	{		
		$this->s__seleccionado = $seleccion; 
		$proyecto = $this->s__seleccionado['proyecto'];
		$manejador = $this->get_manejador($proyecto);
		
		$manejador->agregar_tablas();				
		if (! $manejador->existe()) {
			$manejador->crear();
			$this->agregar_notificacion('Schema creado');
		} else {
			$manejador->set_triggers_eliminacion_forzada(true);
			$manejador->migrar();
			$this->agregar_notificacion('Schema actualizado');
		}
		
		//Actualizo el flag en apex_fuente_tatos
		$id = toba_info_editores::get_fuente_datos_defecto($proyecto);
		$info = toba::proyecto($proyecto)->get_info_fuente_datos($id, $proyecto);
		$tiene_metadato = ($info['tiene_auditoria'] == 1);		
		if ( ! $tiene_metadato) {
			toba::fuente($id)->set_fuente_posee_auditoria(true);
		}
		unset($this->s__seleccionado);
	}
	
	//-----------------------------------------------------------------------------------
	//---- form_tablas ------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_tablas(toba_ei_formulario $form)
	{
		if (! isset($this->s__tablas)) {
			$this->s__tablas = array('tablas' => $this->get_tablas_triggers_activos());
		}
		$form->set_datos($this->s__tablas);
	}

	function evt__form_tablas__modificacion($datos)
	{
		$this->s__tablas = $datos;
	}

	//------------------------------------------------------------------------
	//		METODOS CARGA DATOS
	//------------------------------------------------------------------------	
	
	function get_tablas_triggers_activos()
	{
		$datos = array();
		//Busco los triggers activos con sus respectivas tablas.
		$manejador = $this->get_manejador($this->s__seleccionado['proyecto']);
		$activos = $manejador->get_triggers_activos();
		foreach ($activos as $trg) {
			$datos[] = $trg['tabla'];		
		}
		return $datos;
	}
	
	function get_db($proyecto)
	{
		//Instancio la bd para el proyecto en cuestion
		$id = toba_info_editores::get_fuente_datos_defecto($proyecto);
		$fuente_datos = toba_admin_fuentes::instancia()->get_fuente($id, $proyecto);
		return $fuente_datos->get_db();
	}	
	
	function get_manejador($proyecto)
	{
		$db = $this->get_db($proyecto);
		if (! isset($db)) {
			return null;
		}
		$schema_auditoria = $db->get_schema(). '_auditoria';	
		return $db->get_manejador_auditoria($db->get_schema(), $schema_auditoria);			
	}
	
	function existe_auditoria($proyecto)
	{
		$id = toba_info_editores::get_fuente_datos_defecto($proyecto);
		$info = toba::proyecto($proyecto)->get_info_fuente_datos($id, $proyecto);
		$tiene_metadato = ($info['tiene_auditoria'] == 1);		
		
		$manejador = $this->get_manejador($proyecto);
		//Para que la auditoria funcione, tiene que tener el schema y la configuracion por metadato
		return ($tiene_metadato && isset($manejador) && $manejador->existe());
	}
	
	function get_tablas_disponibles()
	{
		if (isset($this->s__seleccionado)) {
			$db = $this->get_db($this->s__seleccionado['proyecto']);
			return $db->get_lista_tablas(false, $db->get_schema());
		}
	}
}
?>