<?php
require_once('nucleo/browser/clases/objeto_ci.php'); 
/*
	El controlador tiene que implementar 2 metodos:
	
		- get_dbr_eventos()
		- get_eventos_estandar()		
*/
class ci_eventos extends objeto_ci
{
	//Eventos
	private $tabla;
	protected $seleccion_evento;
	protected $seleccion_evento_anterior;
	private $id_intermedio_evento;

	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "seleccion_evento";
		$propiedades[] = "seleccion_evento_anterior";
		return $propiedades;
	}

	function get_tabla()
	//Acceso al db_tablas
	{
		if (! isset($this->tabla)) {
			$this->tabla = $this->controlador->get_dbr_eventos();
		}
		return $this->tabla;
	}

	//*******************************************************************
	//*******************  EVENTOS  ************************************
	//*******************************************************************

	function mostrar_evento_detalle()
	{
		if( isset($this->seleccion_evento) ){
			return true;	
		}
		return false;
	}

	function get_lista_ei()
	{
		$ei[] = "eventos_lista";
		if( $this->mostrar_evento_detalle() ){
			$ei[] = "eventos";
		}else{
			$ei[] = "generador";
		}
		return $ei;	
	}

	function limpiar_seleccion()
	{
		unset($this->seleccion_evento);
		unset($this->seleccion_evento_anterior);
	}
	
	function evt__post_cargar_datos_dependencias()
	{
		if( $this->mostrar_evento_detalle() ){
			//Protejo la evento seleccionada de la eliminacion
			$this->dependencias["eventos_lista"]->set_fila_protegida($this->seleccion_evento_anterior);
			//Agrego el evento "modificacion" y lo establezco como predeterminado
			$this->dependencias["eventos"]->agregar_evento( eventos::modificacion(null, false), true );
		}
	}

	//-------------------------------
	//---- EI: Generador ------------
	//-------------------------------

	function evt__generador__cargar($datos)
	{
		$eventos = $this->controlador->get_eventos_estandar($datos['modelo']);
		foreach($eventos as $evento)
		{
			try{
				$this->get_tabla()->nueva_fila($evento);
			}catch(excepcion_toba $e){
				toba::get_cola_mensajes()->agregar("Error agregando el evento '{$evento['identificador']}'. " . $e->getMessage());
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
		foreach(array_keys($registros) as $id)
		{
			$accion = $registros[$id][apex_ei_analisis_fila];
			unset($registros[$id][apex_ei_analisis_fila]);
			switch($accion){
				case "A":
					$this->id_intermedio_evento[$id] = $dbr->nueva_fila($registros[$id], $id);
					break;	
				case "B":
					//Tengo que reportarle al contenedor la eliminacion del evento
					$id_evento = $dbr->get_fila_columna($id,"identificador");
					$dbr->eliminar_fila($id);
					$this->reportar_evento( "del_evento", $id_evento );
					break;	
				case "M":
					$id_anterior = $dbr->get_fila_columna($id, 'identificador');
					$id_nuevo = $registros[$id]['identificador'];
					$dbr->modificar_fila($id, $registros[$id]);
					//Si se cambio el identificador del evento notificar al controlador de nivel superior
					if ($id_nuevo != $id_anterior) {
						$this->reportar_evento("mod_id", $id_anterior, $id_nuevo);
					}
					break;	
			}
		}
	}
	
	function evt__eventos_lista__carga()
	{
		return $this->get_tabla()->get_filas(null, true);
	}


	function evt__eventos_lista__seleccion($id)
	{
		if(isset($this->id_intermedio_evento[$id])){
			$id = $this->id_intermedio_evento[$id];
		}
		$this->seleccion_evento = $id;
	}
	
	//-----------------------------------------
	//---- EI: Info detalla de un EVENTO ------
	//-----------------------------------------

	function evt__eventos__modificacion($datos)
	{
		$this->get_tabla()->modificar_fila($this->seleccion_evento_anterior, $datos);
	}
	
	function evt__eventos__carga()
	{
		$this->seleccion_evento_anterior = $this->seleccion_evento;
		return $this->get_tabla()->get_fila($this->seleccion_evento_anterior);
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

	//-----------------------------------------
}
?>
