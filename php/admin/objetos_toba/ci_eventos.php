<?php
require_once('nucleo/browser/clases/objeto_ci.php'); 
require_once("admin/db/autoload.php");
/*
	El controlador tiene que implementar 2 metodos:
	
		- get_dbr_eventos()
		- get_eventos_estandar()		
*/
class ci_eventos extends objeto_ci
{
	//Eventos
	private $db_registros;
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

	function get_dbr()
	//Acceso al db_tablas
	{
		if (! isset($this->db_registros)) {
			$this->db_registros = $this->controlador->get_dbr_eventos();
		}
		return $this->db_registros;
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

	function get_lista_eventos()
	{
		$eventos = parent::get_lista_eventos();
		$eventos = array_merge($eventos, eventos::evento_estandar("init","Cargar EVENTOS estandar",true,null,null));
		return $eventos;
	}

	function get_lista_ei()
	{
		$ei[] = "eventos_lista";
		if( $this->mostrar_evento_detalle() ){
			$ei[] = "eventos";
		}
		return $ei;	
	}

	function evt__init()
	{
		$eventos = $this->controlador->get_eventos_estandar();
		foreach($eventos as $evento)
		{
			try{
				$this->get_dbr()->agregar_registro($evento);
			}catch(excepcion_toba $e){
				toba::get_cola_mensajes()->agregar("Error agregando el evento '{$evento['identificador']}'. " . $e->getMessage());
			}
		}
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

		$dbr = $this->get_dbr();
		foreach(array_keys($registros) as $id)
		{
			//Creo el campo orden basado en el orden real de las filas
			$accion = $registros[$id][apex_ei_analisis_fila];
			unset($registros[$id][apex_ei_analisis_fila]);
			switch($accion){
				case "A":
					$this->id_intermedio_evento[$id] = $dbr->agregar_registro($registros[$id]);
					break;	
				case "B":
					$dbr->eliminar_registro($id);
					break;	
				case "M":
					$dbr->modificar_registro($registros[$id], $id);
					break;	
			}
		}
	}
	
	function evt__eventos_lista__carga()
	{
		return $this->get_dbr()->get_registros(null, true);
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
		$this->get_dbr()->modificar_registro($datos, $this->seleccion_evento_anterior);
	}
	
	function evt__eventos__carga()
	{
		$this->seleccion_evento_anterior = $this->seleccion_evento;
		return $this->get_dbr()->get_registro($this->seleccion_evento_anterior);
	}

	function evt__eventos__cancelar()
	{
		$this->limpiar_seleccion();
	}
	//-----------------------------------------
}
?>