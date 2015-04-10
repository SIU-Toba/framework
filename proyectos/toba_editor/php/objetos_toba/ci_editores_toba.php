<?php
require_once('seleccion_imagenes.php');

abstract class ci_editores_toba extends toba_ci
{
	protected $id_objeto;
	protected $cambio_objeto;
	protected $etapa_particular;
	private $falla_carga = false;
	private $elemento_eliminado = false;

	function ini()
	{
		//Cargo el editable de la zona		
		$zona = toba::solicitud()->zona();
		if ($editable = $zona->get_editable()) {
			list($proyecto, $objeto) = $editable;
		}	
		//Se notifica un objeto y un proyecto	
		if (isset($objeto) && isset($proyecto)) {
			//Se determina si es un nuevo objeto
			$selecciono_otro = (!isset($this->id_objeto) || 
						($this->id_objeto['proyecto'] != $proyecto || $this->id_objeto['objeto'] != $objeto));
			if ($selecciono_otro) {
				$this->set_objeto(array('proyecto'=>$proyecto, 'objeto'=>$objeto));
				$this->cambio_objeto = true;
			} else {
				$this->cambio_objeto = false;	
			}
		}
		//Llegada a un TAB especifico desde el arbol
		$etapa = toba::memoria()->get_parametro('etapa');
		if (isset($etapa)) {
			$this->set_pantalla($etapa);
		}
		//Llegada desde un evento
		$evento = toba::memoria()->get_parametro('evento');
		if (isset($evento)) {
			$this->set_pantalla(3);
			$this->dependencia('eventos')->set_evento_editado($evento);
		}		
	}
	
	function get_entidad()
	{		//Acceso al DATOS_RELACION
		if ($this->cambio_objeto && !$this->falla_carga) {
			toba::logger()->debug($this->get_txt() . '*** se cargo la relacion: ' . $this->id_objeto['objeto']); 	
			if ($this->dependencia('datos')->cargar($this->id_objeto)) {
				$this->cambio_objeto = false;//Sino sigue entrando aca por cada vez que se solicita la entidad
			} else {
				toba::notificacion()->agregar('El elemento seleccionado no existe.', 'error');
				$this->falla_carga = true;	
			}
		}
		return $this->dependencia('datos');
	}

	function componente_existe_en_db()	
	{
		return $this->get_entidad()->esta_cargada();
	}

	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = 'id_objeto';
		$propiedades[] = 'cargado';
		return $propiedades;
	}	
	
	function set_objeto($id)
	{
		$this->id_objeto = $id;
	}

	function conf()
	{
		if (! $this->get_entidad()->esta_cargada()) {
			$this->pantalla()->eliminar_evento('eliminar');
		}
	}

	function evt__eliminar()
	{
		$this->get_entidad()->eliminar();
		$this->elemento_eliminado = true;
		$zona = toba::solicitud()->zona();
		$zona->resetear();
		toba::notificacion()->agregar('El elemento ha sido eliminado.', 'info');		
		admin_util::refrescar_editor_item();
	}
	

	//*******************************************************************
	//*****************  PROPIEDADES BASICAS  ***************************
	//*******************************************************************

		
	function conf__base($form)
	{
		if (! in_array($this->get_clase_actual(), toba_info_editores::get_clases_con_fuente_datos())) {
			//Oculta la fuente
			$form->desactivar_efs(array('fuente_datos'));
		}
		
		$reg = $this->get_entidad()->tabla('base')->get();
		$es_alta = !isset($this->id_objeto);
		$hay_archivo_subclase = (isset($reg['subclase_archivo']) || isset($reg['subclase']));
		$hay_personalizacion = toba_personalizacion::get_personalizacion_iniciada(toba_editor::get_proyecto_cargado());
		$pm_personalizacion = $this->get_pm_personalizacion();		
		if ($es_alta) {
			//--- Si es un nuevo objeto, se sugiere un nombre para el mismo
			$nombre = '';
			if (isset($this->controlador)
					 && method_exists($this->controlador, 'get_nombre_destino')
					 && $this->controlador->hay_destino()) {
				$nombre_dest = $this->controlador->get_nombre_destino();				 	
				if ($this->controlador->destino_es_item()) {
					$nombre = $nombre_dest;
				} else {
					$nombre = "$nombre_dest - ".$this->controlador->get_nombre_rol();	
				}
			} else {
				$nombre = $this->get_abreviacion_clase_actual();				
			}
			$reg = array();
			$reg['nombre'] = $nombre;
		}

		if ($hay_personalizacion) {
			$form->eliminar_evento('extender');				
			if (! $hay_archivo_subclase) {
				$reg['punto_montaje'] = $pm_personalizacion;
			} elseif ($form->existe_evento('personalizar')) {											//Aun sin personalizar
				if ($reg['punto_montaje'] == $pm_personalizacion) {								//Ya esta personalizado
					$form->eliminar_evento('personalizar');			
				} else {
					$form->evento('personalizar')->vinculo()->agregar_parametro('pm_pers', $pm_personalizacion);
					$form->evento('personalizar')->vinculo()->agregar_parametro('subclase_pers', $reg['subclase']);
				}
			}
		} else {
			$form->eliminar_evento('personalizar');
			if ($hay_archivo_subclase || $es_alta) {
				$form->eliminar_evento('extender');	
			}			
		}		
		
		return $reg;
	}

	function evt__base__modificacion($datos)
	{
		if (!isset($datos['fuente_datos'])) {
			$datos['fuente_datos'] = null;	
		}
		if (!isset($datos['fuente_datos_proyecto'])) {
			$datos['fuente_datos_proyecto'] = null;	
		}
		$this->get_entidad()->tabla('base')->set($datos);
	}
	
	// *******************************************************************
	// *******************  PROCESAMIENTO  *******************************
	// *******************************************************************
	
	function evt__procesar()
	{
		if (! $this->componente_existe_en_db() ) {
			//Seteo los datos asociados al uso de este editor
			$fijos = array('proyecto' => toba_editor::get_proyecto_cargado(),
							'clase_proyecto' => 'toba',
							'clase' => $this->get_clase_actual());
			$this->get_entidad()->tabla('base')->set($fijos);
		}
		//Sincronizo el DBT
		$this->get_entidad()->sincronizar();

		if ($this->componente_existe_en_db()) {
			//Algun cambio de valor del componente puede cambiar el display de la zona
			toba::zona()->recargar();
		}
		// Seteo el objeto INTERNO
		$datos = $this->get_entidad()->tabla('base')->get();
		$this->set_objeto(array('proyecto'=>$datos['proyecto'], 'objeto'=>$datos['objeto']));
	}
	
	//---------------------------------------------------------------
	//-------------------------- Consultas --------------------------
	//---------------------------------------------------------------

	function get_clase_actual()
	{
		if (isset($this->clase_actual)) {
			return $this->clase_actual;
		} else {
			throw new toba_error('El editor actual no tiene definida sobre que clase de objeto trabaja');
		}
	}
	
	function get_clase_info_actual()
	{
		return $this->get_clase_actual() . '_info';
	}	
	
	function get_abreviacion_clase_actual()
	{
		return call_user_func(array($this->get_clase_info_actual(), 'get_tipo_abreviado'));
	}
		
	/*
		Todos los EI que tienen un tab de eventos necesitan implementar estos metodos.
		Actualmente solo se utilizan en el CI
	*/
	function eliminar_evento($id) {}
	function modificar_evento($id_anterior, $id_nuevo) {}
	
	function get_modelos_evento()
	{
		return call_user_func(array($this->get_clase_info_actual(),'get_modelos_evento'));
	}	
	
	function get_eventos_internos()
	{
		return call_user_func(array($this->get_clase_info_actual(),'get_eventos_internos'), $this->get_entidad());
	}
	
	function notificar_eliminacion_evento($evento) {}
	
	function get_pm_personalizacion()
	{
		$resultado = null;
		$pms = toba_info_editores::get_pms( toba_editor::get_proyecto_cargado());
		foreach($pms as $pm) {
			if (trim($pm['etiqueta']) == 'personalizacion') {
				$resultado = $pm['id'];
			}
		}
		return $resultado;
	}
	//------------------------------------------------------------------------
	//-------------------------- SERVICIOS --------------------------
	//------------------------------------------------------------------------

	/**
	 * Servicio de mostrar listado de imagenes para elegir
	 */
	function servicio__ejecutar()
	{
		seleccion_imagenes::generar_html_listado();
	}	
	
}
?>