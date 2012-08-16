<?php
class ci_conf_auditoria extends toba_ci
{
	protected $s__seleccionado;
	protected $s__tablas;

	private $db; 
	private $manejador; 
	
	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__procesar()
	{
		//Agrego todas las tablas para desactivar todos los triggers		
		$manejador = $this->get_manejador();
		$manejador->agregar_tablas();
		$manejador->desactivar_triggers_auditoria();
			
		//Si se selecciono alguna tabla en particular, activo solo los triggers de esas tablas
		if (isset($this->s__tablas['tablas'])) {			
			//Reseteo estado interno y agrego las tablas actuales
			$manejador->reset_tablas();			
			foreach($this->s__tablas['tablas'] as $tabla) {
				$manejador->agregar_tabla($tabla);				
			}
			$manejador->activar_triggers_auditoria();
		}
		
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
		$cuadro->set_datos(consultas_instancia::get_lista_proyectos());
	}

	function evt__cuadro__seleccion($seleccion)
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
		$manejador = $this->get_manejador();
		$manejador->agregar_tablas();				
		if (! $manejador->existe()) {
			$manejador->crear();
		} else {
			$manejador->migrar();
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
		$manejador = $this->get_manejador();
		$activos = $manejador->get_triggers_activos();
		foreach($activos as $trg) {
			$datos[] = $trg['tabla'];		
		}
		return $datos;
	}
	
	function get_db($proyecto=null)
	{
		if (! isset($this->db)) {
			if (is_null($proyecto)) {
				$proyecto = $this->s__seleccionado['proyecto'];
			}
			//Instancio la bd para el proyecto en cuestion
			$id = toba_info_editores::get_fuente_datos_defecto($proyecto);
			$fuente_datos = toba_admin_fuentes::instancia()->get_fuente($id, $proyecto);
			$this->db = $fuente_datos->get_db();
		}
		return $this->db;
	}	
	
	function get_manejador()
	{
		//Instancio el manejador de auditoria para la fuente		
		if (! isset($this->manejador)) {
			$db = $this->get_db();
			$schema_auditoria = $db->get_schema(). '_auditoria';		
			$this->manejador = $db->get_manejador_auditoria($db->get_schema(), $schema_auditoria);			
		}
		return $this->manejador;
	}
	
	function existe_auditoria($proyecto)
	{
		if (is_null($proyecto)) {
			$proyecto = $this->s__seleccionado['proyecto'];
		}
		$id = toba_info_editores::get_fuente_datos_defecto($proyecto);
		$info = toba::proyecto($proyecto)->get_info_fuente_datos($id, $proyecto);
		$tiene_metadato = ($info['tiene_auditoria'] == 1);		
		
		$manejador = $this->get_manejador();
		//Para que la auditoria funcione, tiene que tener el schema y la configuracion por metadato
		return ($tiene_metadato && $manejador->existe());
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