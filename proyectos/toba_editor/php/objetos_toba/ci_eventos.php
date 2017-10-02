<?php
/*
	El controlador tiene que implementar 2 metodos:
	
		- get_dbr_eventos()
		- get_eventos_estandar()		
*/
class ci_eventos extends toba_ci
{
	//Eventos
	private $tabla;
	protected $seleccion_evento;
	protected $seleccion_evento_anterior;
	private $id_intermedio_evento;
	protected $temp_importar_eventos;

	function ini()
	{
		$props = array('seleccion_evento', 'seleccion_evento_anterior');
		$this->set_propiedades_sesion($props);
	}
	
	function get_tabla()
	{		//Acceso al db_tablas
		if (! isset($this->tabla)) {
			$this->tabla = $this->controlador->get_dbr_eventos();
		}
		return $this->tabla;
	}

	function set_evento_editado($id)
	{
		$filas = $this->get_tabla()->get_id_fila_condicion(array('identificador' => $id));
		if (count($filas) == 1) {
			$this->evt__eventos_lista__seleccion(current($filas));
		}
	}
	
	function conf__1($pant)
	{
		if ($this->mostrar_evento_detalle()) {
			$pant->eliminar_dep('generador');
		} else {
			$pant->eliminar_dep('eventos');
		}
	}

	//*******************************************************************
	//*******************  EVENTOS  ************************************
	//*******************************************************************

	function mostrar_evento_detalle()
	{
		if (isset($this->seleccion_evento)) {
			return true;	
		}
		return false;
	}


	function limpiar_seleccion()
	{
		unset($this->seleccion_evento);
		unset($this->seleccion_evento_anterior);
	}
	
	function get_eventos_internos()
	{
		return $this->controlador->get_eventos_internos($this->get_tabla()->get_filas(null, true));	
	}
	
	//-------------------------------
	//---- EI: Generador ------------
	//-------------------------------

	function evt__generador__cargar($datos)
	{
		$this->temp_importar_eventos = $datos;
	}

	function post_eventos()
	{
		if (isset($this->temp_importar_eventos['modelo'])) {
			$eventos = $this->controlador->get_eventos_estandar($this->temp_importar_eventos['modelo']);
			foreach ($eventos as $evento) {
				try {
					$this->get_tabla()->nueva_fila($evento);
				} catch(toba_error $e) {
					toba::notificacion()->agregar("Error agregando el evento '{$evento['identificador']}'. " . $e->getMessage());
				}
			}
		}
	}

	function get_modelos_evento()
	{
		return $this->controlador->get_modelos_evento();
	}

	//-------------------------------
	//---- EI: Lista de eventos ----
	//-------------------------------
	
	function evt__eventos_lista__modificacion($registros)
	{
		/*
			Como en el mismo request es posible dar una evento de alta y seleccionarla,
			tengo que guardar el ID intermedio que el ML asigna en las eventos NUEVAS,
			porque ese es el que se pasa como parametro en la seleccion
		*/
		//FALT CONTROL : (Etiqueta o imagen) completa
		$dbr = $this->get_tabla();
		foreach (array_keys($registros) as $id)
		{
			$accion = $registros[$id][apex_ei_analisis_fila];
			unset($registros[$id][apex_ei_analisis_fila]);
			switch ($accion) {
				case 'A':
					$this->id_intermedio_evento[$id] = $dbr->nueva_fila($registros[$id], $id);
					break;	
				case 'B':
					//Tengo que reportarle al contenedor la eliminacion del evento					
					$fila_chk = $dbr->get_fila($id);
					$this->controlador()->notificar_eliminacion_evento($fila_chk);
					$id_evento = $fila_chk['identificador'];
					$dbr->eliminar_fila($id);
					break;	
				case 'M':
					$id_anterior = $dbr->get_fila_columna($id, 'identificador');
					$id_nuevo = $registros[$id]['identificador'];
					//Aca deberia quitar todas las pantallas que fueron relacionadas al id anterior, solo sirve para CI
					//por eso la pregunta por el metodo
					if ($id_anterior != $id_nuevo && method_exists($this->controlador(), 'set_pantallas_evento')) {
						$this->controlador()->set_pantallas_evento(array(), $id_anterior);
					}
					$dbr->modificar_fila($id, $registros[$id]);
					break;	
			}
		}
	}
	
	function conf__eventos_lista($ml)
	{
		$ml->set_datos($this->get_tabla()->get_filas(null, true));
		if ($this->mostrar_evento_detalle()) {
			//Protejo la evento seleccionada de la eliminacion
			$ml->set_fila_protegida($this->seleccion_evento_anterior);
		}
	}

	function evt__eventos_lista__seleccion($id)
	{
		if (isset($this->id_intermedio_evento[$id])) {
			$id = $this->id_intermedio_evento[$id];
		}
		$this->seleccion_evento = $id;
		$this->dependencia('eventos_lista')->seleccionar($id);		
	}
	
	//-----------------------------------------
	//---- EI: Info detalla de un EVENTO ------
	//-----------------------------------------
	function evt__eventos__modificacion($datos)
	{
		if (is_null($datos['accion_vinculo_servicio'])) {
			if (isset($datos['accion_vin_servicio_extra'])) {
				$datos['accion_vinculo_servicio'] = $datos['accion_vin_servicio_extra'];
			}	
		}
		$this->get_tabla()->modificar_fila($this->seleccion_evento_anterior, $datos);

		// -- Aplico los cambios a la tabla de puntos de control
		$this->get_tabla()->set_cursor($this->seleccion_evento_anterior);
		$this->controlador->dep('datos')->tabla('puntos_control')->eliminar_filas(true);
		foreach ($datos['ptos_de_control'] as $key => $value) {
			$this->controlador->dep('datos')->tabla('puntos_control')->nueva_fila(array('pto_control' => $value));
		}
	}
	
	function conf__eventos($componente)
	{
		$this->seleccion_evento_anterior = $this->seleccion_evento;
		$datos = $this->get_tabla()->get_fila($this->seleccion_evento_anterior);
	  	
	  	//Construye el id de la carpeta a partir del id del item
		if (isset($datos['accion_vinculo_item']) && $datos['accion_vinculo_item'] != '') {
			$datos['accion_vinculo_carpeta'] = toba_info_editores::get_carpeta_de_item($datos['accion_vinculo_item'], $datos['proyecto']); 
		}
	  	
		if (isset($datos['accion_vinculo_servicio']) && ! is_null($datos['accion_vinculo_servicio'])) {
			$servicios_basicos = array('vista_toba_impr_html','vista_pdf','vista_excel','ejecutar', apex_ef_no_seteado);													
			if (! in_array($datos['accion_vinculo_servicio'], $servicios_basicos)) {	  	
				$datos['accion_vin_servicio_extra'] = 'O';
			} else {
				$datos['accion_vin_servicio_extra'] = $datos['accion_vinculo_servicio'];
				$datos['accion_vinculo_servicio'] = null;
			}
		}		
	  	
		$this->get_tabla()->set_cursor($this->seleccion_evento_anterior); 
		$componente->ef('ptos_de_control')->set_estado($this->controlador->dep('datos')->tabla('puntos_control')->get_valores_columna('pto_control'));
		return $datos;
	}
  
	function evt__eventos__cancelar()
	{
		$this->limpiar_seleccion();
	}

	function evt__eventos__aceptar($datos)
	{
		$this->evt__eventos__modificacion($datos);
		$this->limpiar_seleccion();
	}

	//--------- PUNTOS DE CONTROL ------------

	function get_puntos_de_control($filtro)
	{
		$tabla_base = $this->controlador->get_entidad()->tabla('base');
		$id_objeto = $tabla_base->get_fila_columna($tabla_base->get_cursor(), 'objeto');

		// Si no puedo recuperar el contenedor es porque el objeto aun
		// no existe en la base, entonces el pido el contenedor al creador.
		$creador_obj = $this->controlador()->controlador();
		$id_contenedor = null;
		if (get_class($creador_obj) == 'ci_creador_objeto') {
			$id_contenedor = $creador_obj->get_destino_objeto();
		}

		$columnas = array(); 
		if ($filtro == 'C') {
			if ($this->controlador->get_entidad()->existe_tabla('columnas')) {
				$tabla = $this->controlador->get_entidad()->tabla('columnas');
				$columnas = $tabla->get_valores_columna('clave');
			}
			if ($this->controlador->get_entidad()->existe_tabla('efs')) {
				$tabla = $this->controlador->get_entidad()->tabla('efs');
				$columnas = $tabla->get_valores_columna('identificador');
			}
		}

		$puntos_control = toba_info_editores::get_puntos_de_control($filtro, $id_contenedor, $id_objeto, $columnas);
		return $puntos_control;
	}
	
}
?>
